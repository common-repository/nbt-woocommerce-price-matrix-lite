<?php
class NBT_Price_Matrix_Ajax {

	public function load_table() {
		$json = array();
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : false;
		$vacants = isset( $_POST['vacant'] ) ? wc_clean( wp_unslash( $_POST['vacant'] )) : false;
		$product = wc_get_product($product_id);

		if( $vacants ) {
			$deprived = array();
			foreach($vacants as $k_vacant => $value_vacant) {
				$deprived[] = array('name' => $k_vacant, 'value' => $value_vacant);
			}

			$html = $this->trigger_load_table($product, $deprived);
			$json['complete'] = true;
			$json['template'] = $html;
		}

		wp_send_json($json);


	}

	public function load_variations(){
		if (  ! wp_verify_nonce( $_REQUEST['security'], 'load-variations' ) ) {
		     die( 'Security check' ); 
		} else {
			$json = $order_attribute = $vacant_attribute = array();
			$attr_parent = '';

			$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : false;
			$product = wc_get_product($product_id);
			$get_attributes = $product->get_attributes( 'edit' );
			$pm_table = get_post_meta($product->get_id(), '_pm_table_attr', true);
			$pm_table_direction = get_post_meta($product->get_id(), '_pm_table_direction', true);

			$html = '';
			$total_attributes = count($get_attributes);
			if( $product && $get_attributes && $total_attributes >= 2 ) {
				$new_attribute = array();
				foreach( $get_attributes as $attribute_name => $attribute) {
					if ( $attribute->is_taxonomy() && ( $attribute_taxonomy = $attribute->get_taxonomy_object() ) ) {
						$rs = NBT_Solutions_Price_Matrix::get_attribute_taxonomies( $product->get_id(), str_replace('pa_', '', $attribute_name) );
						
						if( $rs ) {
							$new_attribute[$attribute_name] = array(
								'label' => $rs->attribute_label,
								'slug' => $attribute_name
							);
						}
					} else {
						$new_attribute[$attribute_name] = array(
							'label' => $attribute->get_name(),
							'slug' => $attribute_name
						);
					}
				}
				$vacant_attribute = $new_attribute;

				$order_attribute = $attr_new;

				if( $new_attribute ) {
					/* Rewrite attribute */
					if( isset($pm_table) && ! empty($pm_table) && is_array($pm_table) ) {
						$rewrite_attribute = array();
						foreach( $pm_table as $k => $v) {
							if( isset($new_attribute[$v]) ) {
								if( isset($pm_table_direction[$k]) ) {
									$new_attribute[$v]['direction'] = $pm_table_direction[$k];
								}
								$rewrite_attribute[$v] = $new_attribute[$v];
							}
						}
						$new_attribute = $rewrite_attribute;
					}

					ob_start();
					include_once NBT_PRICEMATRIX_PATH .'tpl/admin/row-repeater.php';
					$json['totals'] = $total_attributes;
					$json['template'] = ob_get_clean();
					$json['complete'] = true;
				}

			}else {
				ob_start();
				include_once NBT_PRICEMATRIX_PATH .'tpl/admin/row-repeater-empty.php';
				$json['template'] = ob_get_clean();
				$json['complete'] = true;
			}
		}

		wp_send_json($json);
	}

	/**
	 * Load input price
	 */
	public function input_price() {
		$json = array();

		check_ajax_referer( 'input-price', 'security' );

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : false;
		$product = wc_get_product($product_id);

		if( $product ) {
			$html = '<div id="price-matrix-popup" class="white-popup mfp-hide"><h2>'. __('Price Matrix: Input Price', 'nbt-price-matrix').'</h2><div class="enter-price-wrap">';
			$html .= $this->trigger_load_table($product);
			$html .= '</div></div>';
			$json['complete'] = true;
			$json['html'] = $html;
		}

		wp_send_json($json);
	}

	/**
	 * Save price when input price
	 */
	public function save_price() {
		$json = array();

		check_ajax_referer( 'save-price', 'security' );

		$product_id = isset( $_POST['product_id'] ) ? wc_clean( wp_unslash( $_POST['product_id'] )) : '';
		$attrs = isset($_REQUEST['attr']) ? wc_clean( wp_unslash($_REQUEST['attr'])) : array();
		$_price = isset($_REQUEST['price']) ? wc_clean( wp_unslash($_REQUEST['price'])) : array();


		
		$product = wc_get_product($product_id);
		if( $product ) {
			foreach( $attrs as $k => $attr) {
				if( is_array($attr) ) {
					$post_id = $this->save_price_find_attributes( $product, $attr );

					update_post_meta($product->get_id(), '_stock_status', 'instock');
					

					if( isset($_price[$k]['price']) ) {
						$price = trim($_price[$k]['price']);
						$price = preg_replace("/\s+/", '', $price);
						$price = str_replace( array(' ', '&nbsp;'), '', $price);

						if( is_numeric($price) ) {
							$meta_regular_price = $price;
							$meta_sale_price = '';
							$meta_price = $price;
						}else {
							$price = preg_replace("/\s+/", '', $_price[$k]['price']);

							if( preg_match('/^([0-9.,]+)(-)([0-9\.,]+)/', $price, $output_array) ) {
								$meta_regular_price = $output_array[1];
								$meta_sale_price = $output_array[3];
								$meta_price = $output_array[3];
							}
						}

						if( empty($price) ) {
							$meta_regular_price = '';
							$meta_sale_price = '';
							$meta_price = '';
							update_post_meta($post_id, '_stock_status', 'outinstock');
						}

						update_post_meta($post_id, '_regular_price', $meta_regular_price);
						update_post_meta($post_id, '_sale_price', $meta_sale_price);
						update_post_meta($post_id, '_price', $meta_price);
					}



					$json['complete'] = true;

				}

			}
		}

		wp_send_json($json);
	}

	/**
	 * Save price when input price
	 */
	public function order_attribute() {
		$json = array();

		$attribute = isset($_REQUEST['attribute']) ? wc_clean( wp_unslash($_REQUEST['attribute'])) : array();
		$order_status = json_decode(str_replace('\\', '', wc_clean( wp_unslash($_REQUEST['order_status'])) ));
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : false;

		if( $product_id ) {
			$new = get_post_meta($product_id, '_pm_order_attributes', true);
			if( ! $new ) {
				$new = array();
			}
	
			$new[$attribute] = $order_status;
			update_post_meta($product_id, '_pm_order_attributes', $new);
		}
	}

	public function trigger_load_table( $product, $deprived = array() ) {
		$data_attribute = get_post_meta($product->get_id(), '_pm_table_attr', true);
		$data_direction = get_post_meta($product->get_id(), '_pm_direction', true);
		$data_num = get_post_meta($product->get_id(), '_pm_num', true);
		$get_attributes = $product->get_attributes( 'edit' );
		$product_id = $product->get_id();

		switch( $data_num ) {
			case 3:
				$suffix = '-' . $data_direction . '-' . $data_num;
				break;
			case 4:
				$suffix = '-' . $data_num;
				break;
			default:
				$suffix = '';
				break;
		}

		if( file_exists(NBT_PRICEMATRIX_PATH . 'tpl/admin/table-matrix' . $suffix . '.php') ) {
			$_pm_attr = get_post_meta($product->get_id(), '_pm_attr', true);
			$order_attributes = get_post_meta($product->get_id(), '_pm_order_attributes', true);

			foreach( $get_attributes as $attribute_name => $attribute ) {
				if( in_array($attribute_name, $data_attribute) ) {
					unset($get_attributes[$attribute_name]);
				}
			}

			/* Show hidden attributes */
			$vacant_attributes = [];
			if( $get_attributes ) {

				foreach( $get_attributes as $k_attr => $val_attr) {

 					if ( $val_attr->is_taxonomy() && ( $attribute_taxonomy = $val_attr->get_taxonomy_object() ) ) {
						$wc_attribute_tax = NBT_Solutions_Price_Matrix::get_attribute_taxonomies( $product->get_id(), str_replace('pa_', '', $k_attr) );
						
						$array = array();
						foreach( $val_attr->get_terms() as $key => $value) {
							$array[] = (array)$value;
						}
						
						$vacant_attributes[$k_attr] = array_merge((array) $wc_attribute_tax, array(
							'terms' => $array
						));
					}else {
						$array = array();
						foreach ($val_attr->get_options() as $key => $value) {
							$array[] = array(
								'term_id' => $key,
								'taxonomy' => $k_attr,
								'name' => trim($value),
								'slug' => trim($value),
								'is_taxonomy' => false
							);
						}
						

						$wc_attribute_tax = new stdClass();
						$wc_attribute_tax->attribute_label = $val_attr->get_name();
						$wc_attribute_tax->attribute_name = $k_attr;

						$vacant_attributes[$k_attr] = array_merge((array) $wc_attribute_tax, array(
							'terms' => $array
						));
					}



				}

			}


			ob_start();
			if( empty($deprived) ) {
				echo '<p class="attribute-p nopadding">'. __('<strong>Note:</strong> Double click to input the price!</p><p>To input the sale price, please use the "-" characters between prices. Eg: original price is $5, sale price is $2, the convention is 5-2', 'nbt-price-matrix').'</p>';
				include( NBT_PRICEMATRIX_PATH . 'tpl/admin/vacant-attributes.php' );
			}

			/* Create deprived if found */
			if( $get_attributes && empty($deprived) ) {
				$deprived = array();
				foreach($vacant_attributes as $k_vacant => $val_vacant) {
					$first_vacant = $val_vacant['terms'][0];
					$deprived[] = array(
						'name' => $first_vacant['taxonomy'],
						'value' => $first_vacant['slug']
					);
				}
			}

			include( NBT_PRICEMATRIX_PATH . 'tpl/admin/table-matrix' . $suffix . '.php' );
			$html .= ob_get_clean();
		}

		return $html;
	}

	public function save_price_find_attributes($product, $attrs) {
		global $wpdb;
		
		
		$where = $name = '';
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
		$post_id = get_transient( $name );
		if ( false === $post_id ) {
			$rs = $wpdb->get_row($sql);

			if( $rs ) {
				$post_id = $rs->id;
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

 				$post_id = wp_insert_post( $insert_data );

				foreach( $attributes as $attribute_name => $attribute_value ) {
					update_post_meta($post_id, 'attribute_' . $attribute_name, $attribute_value);
				}
				update_post_meta($post_id, '_stock_status', 'instock');
			}

			set_transient( $name, $post_id );
		}

		return $post_id;
	}
}