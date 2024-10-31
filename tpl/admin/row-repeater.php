<div id="price_matrix_table" data-count="<?php echo $total_attributes;?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $new_attribute ) );?>">
	<form action="" method="POST" id="frm-price-matrix">
		<?php
		if( $total_attributes > 4 ) {?>
		<div id="message" class="max-attribute-notice notice woocommerce-message">
			<p><?php _e( 'Currently, Price Matrix only support display maximum is 4 attributes to price matrix table', 'nbt-solution' ); ?></p>
			<p><?php _e('By default, attribute at the bottom will not be displayed to the table, it will just show in selection mode. You can choose which attribute to be shown in the table by click Show/hide next to it', 'nbt-solution');?></p>
		</div>
		<?php }?>
		<table class="pm_repeater">
			<thead>
				<tr>
					<th class="pm-row-zero"></th>
					<th class="pm-th"><?php _e('Attributes', 'nbt-wocommerce-price-matrix');?></th>
					<th class="pm-th"><?php _e('Direction', 'nbt-wocommerce-price-matrix');?></th>
					<th class="pm-row-zero"></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$i = 1;
				foreach($new_attribute as $k_row => $row) { ?>
				<tr class="pm-row">
					<td class="pm-row-zero order pm-handle">
						<span><?php echo $i;?></span>
					</td>

					<td class="pm-field">
						<div class="pm-input">
							<div class="pm-input-wrap">
								<select class="pm-attributes-field" name="pm_attr[]">
									<option value="<?php echo $row['slug'];?>" selected><?php echo $row['label'];?></option>
								</select>
							</div>
						</div>
					</td>
					<td class="pm-field">
						<div class="pm-input">
							<div class="pm-input-wrap">
								<select class="pm-direction-field" name="pm_direction[]">
									<?php foreach( NBT_Solutions_Price_Matrix::pm_direction() as $k => $v ) {?>
									<option value="<?php echo $k;?>"<?php if( isset($row['direction']) && $row['direction'] == $k ) { echo ' selected';}?>><?php echo $v;?></option>
									<?php }?>
								</select>
							</div>
						</div>
					</td>
					<td class="pm-row-zero">
						<a class="pm-icon -plus small" href="#" data-event="add-row" title="Add row"></a>
						<a class="pm-icon -minus small" href="#" data-event="remove-row" title="Remove row"></a>
					</td>
				</tr>
				<?php $i++;
				}?>
			</tbody>
		</table>

		<input type="hidden" name="security" value="<?php echo wp_create_nonce( "_price_matrix_save" );?>" />
		<button type="button" class="button save_price_matrix button-primary" style="margin-top: 15px;">Save</button>
		<button type="button" class="button btn-enter-price" disabled>Input Price</button>
		<button type="button" class="button btn-order-attributes">Order Attributes</button>
	</form>
</div>
<pre>
	<?php print_r($order_attribute);?>
			</pre>
<div id="order_attributes">
	<hr style="margin: 25px 0 10px" />
	<label>Order Attributes</label>
	<select name="order_attribute" class="select-order-attribute">
		<option value="">Select a attribute</option>
		<?php foreach( $vacant_attribute as $attribute_name => $attribute) {?>
		<option value="<?php echo $attribute_name;?>"><?php echo $attribute['label'];?></option>
		<?php }?>
	</select>

	<?php
	$data_attribute = get_post_meta($product->get_id(), '_pm_order_attributes', true);
	foreach( $get_attributes as $attribute_name => $attribute) {
		
		if ( $attribute->is_taxonomy() && ( $attribute_taxonomy = $attribute->get_taxonomy_object() ) ) {
			//$rs = NBT_Solutions_Color_Swatches::get_attribute_taxonomies( $product->get_id(), str_replace('pa_', '', $attribute_name) );
			
			$attr_new = array();
			foreach ( $attribute->get_terms() as $option ) {
				$attr_new[$option->slug] = $option->name;
			}				
		}else {
			$attr_new = array();
			foreach ( $attribute->get_options() as $option ) {
				$attr_new[$option] = esc_attr( $option );
			}
		}
		?>
		<table class="pm_repeater" data-id="<?php echo $attribute_name;?>">
			<thead>
				<tr>
					<th class="pm-row-zero"></th>
					<th class="pm-th">Value</th>
				</tr>
			</thead>

			<tbody class="ui-sortable">
				<?php
				$i = 1;

				if( isset($data_attribute[$attribute_name]) ) {
					$attr_new = $data_attribute[$attribute_name];
				}

				foreach($attr_new as $k => $v) {?>
				<tr class="pm-row">

					<td class="pm-row-zero order pm-handle ui-sortable-handle">
						<span><?php echo $i;?></span>
					</td>

					<td class="pm-field">
						<div class="pm-input">
							<div class="pm-input-wrap">
								<select class="pm-attributes-field" name="pm_attr[]">
									<option value="<?php echo $k;?>" selected=""><?php echo $v;?></option>
								</select>
							</div>
						</div>
					</td>
					
				</tr>
				<?php $i++;
				}?>
			</tbody>
		</table>
	<?php }?>
</div>