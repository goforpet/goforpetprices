<div class="variants-table goforpet-prices">
	{foreach from=$combinations item=combination}
	<div class="clearfix product-variants-item">
		<form action="{$urls.pages.cart}" method="post">
			<div class="product-add-to-cart" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer" data-product-attribute="{$combination.id_product_attribute}" data-product-images='{$combination.images|@json_encode nofilter}'>
				<meta itemprop="url" content="{$combination.url}" />
				<link itemprop="availability" href="https://schema.org/InStock" />
				<meta itemprop="priceCurrency" content="{$currency.iso_code}" />
				{if $product.has_discount AND $combination.specific_price.to AND $combination.specific_price.to != "0000-00-00 00:00:00"}
				<meta itemprop="priceValidUntil" content="{$combination.specific_price.to}" />
				{/if}
				{if !empty($combination.ean13) OR !empty($combination.reference)}
				<meta itemprop="gtin" content="{if empty($combination.ean13)}{$combination.reference}{else}{$combination.ean13}{/if}" />
				{/if}
				{if !empty($combination.ean13)}
				<meta itemprop="gtin13" content="{$combination.ean13}" />
				{/if}
				{if !empty($combination.isbn)}
				<meta itemprop="isbn" content="{$combination.isbn}" />
				{/if}
				{if !empty($combination.upc)}
				<meta itemprop="upc" content="{$combination.upc}" />
				{/if}
				{if !empty($combination.reference)}
				<meta itemprop="sku" content="{$combination.reference}" />
				{/if}
				{if !empty($product_manufacturer->name)}
				<meta itemprop="brand" content="{$product_manufacturer->name}" />
				{/if}
				<p class="hidden">
                    <input type="hidden" name="token" value="{$static_token}" />
                    <input type="hidden" class="id_combination" name="id_product_attribute" value="{$combination.id_product_attribute}" />
                    <input type="hidden" class="id_product" name="id_product" value="{$product.id}" />
                </p>
				<div class="row">
					<div class="col-md-4 col-lg-3">
						<div class="row designation">
							<div class="col-xs-4 hidden-md-up">
								{if !empty($combination.images)}
								<img src="{$combination.images[0].medium}" alt="{$product.name}" />
								{/if}
							</div>
							<div class="col-xs-8">
								<ul class="designations">
									{foreach from=$combination.designations item=designation key=key}
									<li><span>{$key}</span>{$designation}</li>
									{/foreach}
								</ul>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-lg-2">
						<div class="row">
							<div class="col-xs-4 hidden-md-up text-xs-right">
								<span class="control-label">{l s='Price' d='Shop.Theme.Catalog'}</span>
							</div>
							<div class="col-xs-8 col-md-12">
								<div class="price-container">
									{if $combination.price_without_reduction != $combination.price}
									<div class="regular-price">{$combination.price_without_reduction}</div>
									{/if}
									<div class="price">
										<span itemprop="price" content="{$combination.price_amount}"{if $combination.reduction} title="{l s='You save %s' sprintf=[$combination.reduction] mod='goforpetprices'}"{/if}>{$combination.price}</span>
										{if $combination.minimal_quantity > 1}
										<span class="per-part">{l s='per part' mod='goforpetprices'}</span>
										{/if}
									</div>
									{if $combination.unit_price}
									<div class="unit-price">({$combination.unit_price})</div>
									{/if}
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-5 col-lg-7">
						<div class="row">
							<div class="col-xs-12 col-lg-6 cart-block">
								<div class="row">
									<div class="col-xs-4 col-md-12">
										<div class="hidden-md-up text-xs-right">
											<span class="control-label">{l s='Quantity' d='Shop.Theme.Actions'}</span>
										</div>
									</div>
									<div class="col-xs-8 col-md-12">
										<div class="qty product-quantity clearfix">
											<input type="number" name="qty" value="{$combination.minimal_quantity}" class="input-group form-control" min="{$combination.minimal_quantity}" data-min="{$combination.minimal_quantity}" max="{$combination.quantity}" data-max="{$combination.quantity}" step="{$combination.minimal_quantity}" data-step="{$combination.minimal_quantity}" aria-label="{l s='Quantity' d='Shop.Theme.Actions'}">
										</div>
										<div class="product-availability text-xs-left">
											<span id="product-availability">
												{if $combination.quantity > 1}
												<i class="material-icons rtl-no-flip product-available">&#xE5CA;</i>
												{l s='%s items available' sprintf=[$combination.quantity] mod='goforpetprices'}
												{elseif $combination.quantity == 1}
												<i class="material-icons product-last-items">&#xE002;</i>
												{l s='Last item available' mod='goforpetprices'}
												{else}
												<i class="material-icons product-unavailable">&#xE14B;</i>
												{l s='Out of stock' mod='goforpetprices'}
												{/if}
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xs-12 col-lg-6 cart-block">
								<div class="add text-xs-center text-md-left">
									<button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit" {if !$product.add_to_cart_url} disabled{/if}>
										<i class="material-icons shopping-cart">&#xE547;</i>
										{l s='Add to cart' d='Shop.Theme.Actions'}
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	{/foreach}
</div>