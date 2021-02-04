<?php

/**
 * 2021 Go For Pet S.r.l.
 *
 * @author    Lucio Benini <dev@goforpet.com>
 * @copyright 2021 Go For Pet S.r.l.
 * @license   http://opensource.org/licenses/LGPL-3.0  The GNU Lesser General Public License, version 3.0 ( LGPL-3.0 )
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class GoForPetPrices extends Module
{
    public function __construct()
    {
        $this->name          = 'goforpetprices';
        $this->tab           = 'front_office_features';
        $this->version       = '1.0.0';
        $this->author        = 'Go For Pet';
        $this->need_instance = 1;

        parent::__construct();

        $this->displayName = $this->l('Go For Pet - Price Blocks');
        $this->description = $this->l('Standard features for Go For Pet store.');

        $this->ps_versions_compliancy = array(
            'min' => '1.7',
            'max' => _PS_VERSION_
        );
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayGoForPetProductPriceBlock') && $this->registerHook('actionFrontControllerSetMedia');
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if (get_class($this->context->controller) === 'ProductController') {
            $this->context->controller->registerStylesheet(
                $this->name . '-style',
                'modules/' . $this->name . '/views/css/front.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                ]
            );
            $this->context->controller->registerJavascript(
                $this->name . '-style',
                'modules/' . $this->name . '/views/js/front.js'
            );
        }
    }

    public function hookDisplayGoForPetProductPriceBlock($params)
    {
        $product  = $params['product'];

        $results = Db::getInstance()->executeS("
            SELECT
                pa.id_product_attribute,
                pa.minimal_quantity,
                pa.price,
                COALESCE(pa.reference, product.reference) AS reference,
                COALESCE(pa.ean13, product.ean13) AS ean13,
                COALESCE(pa.upc, product.upc) AS upc,
                COALESCE(pa.isbn, product.isbn) AS isbn,
                (product.unit_price_ratio + (pa.price / pa.unit_price_impact)) AS unit_price_ratio,
                (pa.quantity + sa.quantity) AS quantity,
                GROUP_CONCAT(agl.name, '" . PHP_EOL . "', al.name ORDER BY ag.position SEPARATOR '" . PHP_EOL . "') AS designations,
                pl.link_rewrite
            FROM `" . _DB_PREFIX_ . "product_attribute_combination` pac
            INNER JOIN `" . _DB_PREFIX_ . "product_attribute` pa ON (pa.id_product_attribute=pac.id_product_attribute)
            INNER JOIN `" . _DB_PREFIX_ . "product` product ON (product.id_product=pa.id_product)
            INNER JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (product.id_product=pl.id_product AND pl.id_lang=" . (int) $this->context->language->id . ")
            LEFT JOIN `" . _DB_PREFIX_ . "stock_available` sa ON (sa.id_product_attribute=pac.id_product_attribute AND sa.id_product=product.id_product)
            LEFT JOIN `" . _DB_PREFIX_ . "attribute` a ON (a.id_attribute=pac.id_attribute)
            LEFT JOIN `" . _DB_PREFIX_ . "attribute_group` ag ON (ag.id_attribute_group=a.id_attribute_group)
            LEFT JOIN `" . _DB_PREFIX_ . "attribute_lang` al ON (a.id_attribute=al.id_attribute AND al.id_lang=" . (int) $this->context->language->id . ")
            LEFT JOIN `" . _DB_PREFIX_ . "attribute_group_lang` agl ON (ag.id_attribute_group=agl.id_attribute_group AND agl.id_lang=" . (int) $this->context->language->id . ")
            WHERE pa.id_product=" . pSQL($product->id) . "
            GROUP BY pac.id_product_attribute
            ORDER BY a.position ASC
        ");

        $locale = Tools::getContextLocale($this->context);
        $combinations = array();

        foreach ($results as $result) {
            $price = Product::getPriceStatic(
                $product->id,
                true,
                $result['id_product_attribute'],
                6,
                null,
                false,
                true,
                $result['minimal_quantity']
            );
            $original = Product::getPriceStatic(
                $product->id,
                true,
                $result['id_product_attribute'],
                6,
                null,
                false,
                false,
                $result['minimal_quantity']
            );
            $reduction = Product::getPriceStatic(
                $product->id,
                true,
                $result['id_product_attribute'],
                6,
                null,
                true,
                true,
                $result['minimal_quantity']
            );
            $ratio = (float) $result['unit_price_ratio'];

            $combinations[] = array(
                'id_product_attribute' => $result['id_product_attribute'],
                'designations' => $this->splitDesignations($result['designations']),
                'quantity' => $result['quantity'],
                'minimal_quantity' => $result['minimal_quantity'],
                'price' => $locale->formatPrice($price, $this->context->currency->iso_code),
                'price_without_reduction' => $locale->formatPrice(
                    $original,
                    $this->context->currency->iso_code
                ),
                'unit_price' => $ratio > 0 ? $this->formatUnityPrice($locale, (float) $price / (float) $ratio, $product->unity) : null,
                'reduction' => $reduction > 0 ? $locale->formatPrice($reduction, $this->context->currency->iso_code) : 0,
                'images' => $this->getImageLinks((int) $product->id, (int) $result['id_product_attribute'], $result['link_rewrite']),
                'reference' => $result['reference'],
                'ean13' => $result['ean13'],
                'upc' => $result['upc'],
                'isbn' => $result['isbn']
            );
        }

        $this->smarty->assign('combinations', $combinations);

        return $this->display(__FILE__, 'views/templates/hook/displayGoForPetProductPriceBlock.tpl');
    }

    protected function getImageLinks(int $product, int $attribute, string $rewrite)
    {
        $images = array();

        foreach (Image::getImages($this->context->language->id, $product, (int) $attribute) as $image) {
            $images[] = array(
                'large' => $this->context->link->getImageLink($rewrite, $image['id_image'], 'large_default'),
                'medium' => $this->context->link->getImageLink($rewrite, $image['id_image'], 'medium_default'),
                'thumb' => $this->context->link->getImageLink($rewrite, $image['id_image'], 'home_default')
            );
        }

        return $images;
    }

    protected function formatUnityPrice($locale, $price, $unity)
    {
        return $locale->formatPrice($price, $this->context->currency->iso_code) . '/' . (!empty($unity) ? $unity : $this->l('unit'));
    }

    protected function splitDesignations($designations)
    {
        $results = array();
        $a = explode(PHP_EOL, $designations);

        for ($i = 0; $i < count($a); $i += 2) {
            $results[$a[$i]] = $a[$i + 1];
        }

        return $results;
    }
}
