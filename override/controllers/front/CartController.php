<?php

/**
 * 2021 Go For Pet S.r.l.
 *
 * @author    Lucio Benini <dev@goforpet.com>
 * @copyright 2021 Go For Pet S.r.l.
 * @license   http://opensource.org/licenses/LGPL-3.0  The GNU Lesser General Public License, version 3.0 ( LGPL-3.0 )
 */

class CartController extends CartControllerCore
{
    public function init()
    {
        parent::init();

        if (Tools::getValue('update') == 1) {
            $product = new Product($this->id_product);
            $qty = $product->minimal_quantity > 0 ? $product->minimal_quantity : 1;

            if ($this->id_product_attribute) {
                $combination = new Combination($this->id_product_attribute);

                if ($combination->minimal_quantity > 0 ? $combination->minimal_quantity : 1) {
                    $qty = (int) $combination->minimal_quantity;
                }
            }

            if ($this->qty % $qty != 0) {
                $this->qty = ceil($this->qty / $qty) * $qty;
            }
        }
    }
}
