<?php
	function wc_get_product_direction_pm_options(){
		return array(
			'default'    => __( 'Default', 'nbt-price-matrix' ),
			'before_tab' => __( 'Before Tab', 'nbt-price-matrix' ),
		);
	}

	/**
	 * Price Matrix: Attribute Label
	 *
	 * Shows label with attribute keys
	 */
	function pm_attribute_label($tax, $product_id, $order_attributes){
		$product = wc_get_product($product_id);
		$get_attributes = $product->get_attributes( 'edit' );
		

		if(isset($get_attributes[$tax])) :
			$attribute = $get_attributes[$tax];


			if ( $attribute->is_taxonomy() ) :
				$terms = array();
				foreach($attribute->get_terms() as $k => $term) {
					$terms[$term->slug] = (array) $term;
				}
			else :
				$value_array = $attribute->get_options();
				$terms = array();

				foreach ($value_array as $key => $value) {
					$terms[trim($value)] = array(
						'taxonomy' => $tax,
						'name' => trim($value),
						'slug' => trim($value),
						'is_taxonomy' => false
					);
				}
			endif;



			/* Rewrite */
			if( isset($order_attributes[$tax]) ) {
				$array = array();
				foreach( $order_attributes[$tax] as $k_order => $val_order ) {

					if( isset($terms[$k_order]) ) {
						$array[] = array(
							'taxonomy' => $tax,
							'name' => trim($val_order),
							'slug' => trim($k_order),
							'is_taxonomy' => false
						);
					}
				}

				$terms = $array;
			}

			$terms = array_values($terms);




			$terms = json_decode(json_encode($terms), FALSE);
		endif;

		return $terms;
	}

	/**
	 * Price Matrix: Attribute Label
	 *
	 * Shows label with attribute keys
	 */
	function pm_attribute_tax($tax, $product_id){
		global $wpdb;

		$_product_attributes = get_post_meta($product_id, '_product_attributes', TRUE);
		if(isset($_product_attributes[$tax])){
			$data = $_product_attributes[$tax];
			if($data['is_taxonomy']){
				$tax = str_replace('pa_', '', $tax);
				
				$rs =  $wpdb->get_row($wpdb->prepare("SELECT attribute_label FROM ".$wpdb->prefix."woocommerce_attribute_taxonomies WHERE attribute_name = '%s'", $tax), OBJECT);
				if($rs){
					return $rs->attribute_label;
				}
			}else{
				return $data['name'];
			}
		}
	}

	/**
	 * Price Matrix: Attribute Price
	 *
	 * Shows price with attribute
	 */
	function pm_attribute_price($attrs = array(), $product, $ajax = false){
		global $wpdb;
		
		$where = $name = $price = '';
		$attributes = array();

		$sql = "SELECT posts.ID as id, posts.post_parent as parent FROM {$wpdb->posts} as posts";
		if( is_array($attrs) ) {
			foreach( $attrs as $k => $attr ) {
				$sql .= " INNER JOIN {$wpdb->postmeta} AS postmeta".$k." ON posts.ID = postmeta".$k.".post_id";
				$where .= " AND postmeta" . $k . ".meta_key = 'attribute_" . $attr['name'] . "' AND postmeta" . $k . ".meta_value = '" . $attr['value'] . "'";
				$attributes[$attr['name']] = $attr['value'];
				$name .= $attr['name'] . $attr['value'];
			}
		}

		$sql .= " WHERE posts.post_parent = '" . $product->get_id() . "' AND posts.post_type IN ( 'product', 'product_variation' ) AND posts.post_status = 'publish'";
		$sql .= $where;

		$name = '{pm_' . $product->get_id() .'}'.$name;
		$variation_id = get_transient( $name );
		if ( false === $variation_id ) {
			$rs = $wpdb->get_row($sql);

			if( $rs ) {
				$variation_id = $rs->id;
			} else {
				$insert_data = array(
					'post_title'    => $product->get_title(),
					'post_comment'  => 'closed',
					'post_status'   => 'publish',
					'ping_status'   => 'closed',
					'post_author'   => get_current_user_id(),
					'post_name'   => $product->get_slug(),
					'post_parent'   => $product->get_id(),
					'guid'   => esc_url( get_permalink($product->get_id()) ),
					'post_type' => 'product_variation'
				);

 				$variation_id = wp_insert_post( $insert_data );

				foreach( $attributes as $attribute_name => $attribute_value ) {
					update_post_meta($variation_id, 'attribute_' . $attribute_name, $attribute_value);
				}
				update_post_meta($variation_id, '_stock_status', 'instock');
			}

			set_transient( $name, $variation_id );
		}

		$_regular_price = get_post_meta($variation_id, '_regular_price', true);
		$_sale_price = get_post_meta($variation_id, '_sale_price', true);

		if( $ajax ) {
			if( $_regular_price && $_sale_price ) {
				$price = $_regular_price.'-'.$_sale_price;
			}else{
				$price = $_regular_price;
			}
		}else {
			$settings = nb_get_setting(NBT_Solutions_Price_Matrix::$plugin_id);

			if( $_regular_price && $_sale_price ) {
				if( isset($settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_sales']) && $settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_sales'] == true ) {
					$price = '<del>' . wc_price($_regular_price) .'</del><ins>' . wc_price($_sale_price) .'</ins>';
				}else {
					$price = wc_price($_sale_price);
				}

				$find_price = $_sale_price;
				
			}else{
				$price = wc_price($_regular_price);
				$find_price = $_regular_price;
			}
		}


		if( $ajax ) {
			return $price;
		}else {
			return array(
				'price' => $price,
				'final_price' => $find_price,
				'variation_id' => $variation_id
			);
		}
		
	}


	function get_by_name($products, $field){
		foreach ($array as $key => $value) {
			if($value['name'] == $field){
				return $key;
			}
		}
	}


	function getPriceVariation($variation_id) {
		$price = get_post_meta($variation_id, '_regular_price', true);
		$_sale_price = get_post_meta($variation_id, '_sale_price', true);

		if( $_sale_price ) {
			$price = $_sale_price;
		}

		return $price;
	}

if( ! function_exists('nb_get_setting')) {
	function nb_get_setting( $module, $name = null ) {
		if( defined('PREFIX_NBT_SOL') ) {
			return NB_Solution($module, $name);
		}else {
			$option_name = $module . '_settings';
			$settings = get_transient( $option_name );
	
			if ( empty($settings) ) {

				$settings = get_option($module . '_settings');
				
				if ( class_exists('NBT_Price_Matrix_Settings') ) {
                    $module_setting = call_user_func('NBT_Price_Matrix_Settings::get_settings');

                    if( is_array($module_setting) ) {
                        $default_setting = array();
                        foreach( $module_setting as $key => $set) {
                            if( isset($set['id']) && $field_key = $set['id'] ) {
                                $default_setting[$field_key] = $set['default'];

                                if( isset($settings[$field_key]) ) {
									$default_setting[$field_key] = $settings[$field_key];
                                }
                            }
                        }

                        $settings = apply_filters( 'nbt_'.$option_name, $default_setting );
                    }
                }

                set_transient( $option_name, $settings );
			}

			return $settings;
		}
	}
}