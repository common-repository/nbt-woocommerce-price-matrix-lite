jQuery( function( $ ) {
	var $el = $( '.variations_form');

	var pm_load = {
		/**
		 * Init jQuery.BlockUI
		 */
		block: function() {
			$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * Remove jQuery.BlockUI
		 */
		unblock: function() {
			$el.unblock();
		}
	}
	var pm_frontend = {

		/**
		 * Initialize variations actions
		 */
		init: function() {
        	$(document).on('click', '.pure-table .pm-td-price', this.selected_price);
        	$(document).on('change', 'table.un-variations tr:visible select', this.change_attr);

			Tippy('.tippy', {
				animation: 'scale',
				duration: 200,
				arrow: true,
				position: 'bottom'
			});

			$(window).load(this.window_load);
			
			if( nbt_solutions.isCalculatorText != undefined && nbt_solutions.isCalculatorText !== '' ) {
				$(document).on('change', 'body.single-product input[name="quantity"]', this.change_price_calculator);
			}
		},
		
		change_price_calculator: function() {
			var wrapper_selected = $('#price-matrix-wrapper td.selected'),
				qty = $('body.single-product input[name="quantity"]').val(),
				format_price = $('#price-matrix-wrapper').attr('data-format_price'),
				_decimal = nbt_solutions.decimal;
			
			if( wrapper_selected.length > 0 ) {
				var price_selected = wrapper_selected.attr('data-price'),
					total_price = price_selected * qty;
					
				

				var total_price_format = accounting.formatMoney( total_price, {
					symbol:    '',
					decimal:   _decimal,
					thousand:  nbt_solutions.thousand,
					precision: nbt_solutions.precision,
					format:    ''
				} );
				
				var html_price = format_price.replace('{price}', total_price_format)
				$('.woocommerce-variation-price > .price').html(html_price);
				
				
				/* Display calculator */
				if( nbt_solutions.isCalculatorText != undefined && nbt_solutions.isCalculatorText !== '' ) {
					var price_format = accounting.formatMoney( price_selected, {
						symbol:    nbt_solutions.symbol,
						decimal:   _decimal,
						thousand:  nbt_solutions.thousand,
						precision: nbt_solutions.precision,
						format:    nbt_solutions.format
					} );
					
					var total_price_format_cal = accounting.formatMoney( total_price, {
						symbol:    nbt_solutions.symbol,
						decimal:   _decimal,
						thousand:  nbt_solutions.thousand,
						precision: nbt_solutions.precision,
						format:    nbt_solutions.format
					} );
					
					
					var _html = '';
					_html += '<label>' + nbt_solutions.pricematrix.total_label + ':</label>';
					_html += ' ' + price_format + ' x ' + qty;
					_html += ' = ' + total_price_format_cal;

					$('.nbpm-calculator').html('<p class="nbpm-calculator-price">' + _html + '</p>');
				}
			}
			
			
			if( nbt_solutions.isCalculatorText != undefined && nbt_solutions.isCalculatorText !== '' ) {
				
				$('table.price-matrix-table td.pm-td-price').each(function( index ) {
					var price = $(this).attr('data-price'),
					tooltip = $(this).attr('data-original-title'),
					total_tooltip_price = price * qty,
					remake_tooltips = tooltip.replace(/<td class="total_price">(.*)<\/td>/gm, '<td class="total_price">' + format_price.replace('{price}', total_tooltip_price) + '</td>');
					
					$(this).attr('title', remake_tooltips);
				});
				
				Tippy('.tippy', {
					animation: 'scale',
					duration: 200,
					arrow: true,
					position: 'bottom'
				});
			}
		},
		
		autoload_pm: function() {
			//pm_load.block();
			if( $('#single-product_variations').length ) {
				var $product_variations = $('#single-product_variations').attr('data-product_variations');
				var $product_attr = $('#single-product_variations').attr('data-attr');
				var $product_count = $('#single-product_variations').attr('data-count');
				$.ajax({
					url: nbt_solutions.ajax_url,
					data: {
						action:     'pm_autoload',
						security:   $('[name="security"]').val(),
						product_id: $('[name="add-to-cart"]').val(),
						vacant: $.parseJSON($product_variations),
						attr: $.parseJSON($product_attr),
						count: $product_count,
						suffix: $('.un-variations').attr('data-suffix')
					},
					type: 'POST',
					success: function( response ) {
						pm_load.unblock();
						$('body').append(response);
					},
					error:function(){
						//alert('There was an error when processing data, please try again !');
					}
				});
			}
		},

		window_load: function() {
			pm_frontend.set_last_attribtutes();
			pm_frontend.set_default_attributes();
		},

		set_default_attributes: function(vacant = false) {
			
			if( $( '.un-variations').length > 0 ) {
				if( ! vacant ) {
					var total_unvariations = $( ".un-variations td.value select" ).length;
					var total_setvariations = 0;
					$( ".un-variations td.value select" ).each(function( index ) {
						var attribute_name = $(this).attr('data-attribute_name');
						var attribute_value = $( this ).val();
	
						if( typeof nbt_solutions.default_attributes[attribute_name] != 'undefined' ) {
							total_setvariations += 1;
						}
					});
	
					if( total_unvariations == total_setvariations ) {
						$('.un-variations tr:last-child select').trigger('change');
	
						if( nbt_solutions.debug ) {
							console.log('%c Turn on debug!', 'background: #222; color: #bada55');
							console.log('trigger select variations default!');
						}
					}
				}else {
					pm_frontend.trigger_default_attributes();
				}

			}else {
				if( ! vacant ) {
					pm_frontend.trigger_default_attributes();
				}
			}
		},

		set_last_attribtutes: function() {
			if( $( "form.variations_form .variations select.pm-select-last" ).length <= 0) {
				var variations_form_length = ($( "form.variations_form .variations select" ).length - 1);
				
				$( "form.variations_form .variations select" ).each(function( index ) {

					if( $(this).is('[data-attribute_name]') && index == variations_form_length ) {
						$(this).addClass('pm-select-last');
					}
				});

				if( nbt_solutions.debug ) {
					console.log('%c Turn on debug!', 'background: #222; color: #bada55');
					console.log('set_last_attribtutes!');
				}
			}
		},
		
		trigger_default_attributes: function() {
			if( nbt_solutions.debug ) {
				console.log('%c Turn on debug!', 'background: #222; color: #bada55');
				console.log('trigger_default_attributes!');
			}

			var total_variations = $( 'table.variations td.value select' ).length;
			var total_default_variations = 0;
			$( 'table.variations td.value select' ).each(function( index ) {
				var attribute_name = $(this).attr('data-attribute_name'),
					attribute_value = $( this ).val(),
					attribute_name = attribute_name.replace("attribute_", "");

				
				if( typeof nbt_solutions.default_attributes[attribute_name] != 'undefined' ) {
					$(this).val(attribute_value);
					total_default_variations += 1;
				}
			});

			if( total_variations == total_default_variations ) {
				console.log('trigger now!');
				$('.pm-select-last').trigger('change');
			}
		},

		change_attr: function(){
			var $this = $(this).val();
			pm_load.block();
			var $total = $( 'table.un-variations tr:visible select' ).length;

			var optionVal = {};
			var pmid = '';
			var count = 0;
			$( 'table.un-variations tr:visible select' ).each(function( index ) {
				if($(this).val()){
					var $id = $(this).closest('select').attr('id');
					var $val = $(this).val();
					optionVal[$id] = $val;
					pmid += $id + $val;
					count += 1;
				}
			});

			if($this && $total == count) {
				$.ajax({
					url: nbt_solutions.ajax_url,
					data: {
						action:     'pm_load_matrix',
						security:   $('[name="security"]').val(),
						product_id: $('[name="add-to-cart"]').val(),
						attr: optionVal
					},
					type: 'POST',
					datatype: 'json',
					success: function( response ) {
						
						if(response.complete != undefined){
							$('.price_attr').remove();
							$('.table-responsive, [name="price_attr"], [name="security"]').remove();
							//$('.load-table-pm').html(rs.return);

							$('#price-matrix-wrapper .load-table-pm').show().html(response.return);

							Tippy('.tippy', {
								animation: 'scale',
								duration: 200,
								arrow: true,
								position: 'bottom'
							});

							pm_frontend.set_default_attributes(true);
						}
						pm_load.unblock();

					},
					error:function(){
						alert('There was an error when processing data, please try again !');
					}
				});


			}else{
				$('.table-responsive').remove();
				pm_load.unblock();
			}


		},
		selected_price: function() {

			var price = $(this).html();
			var total_length = $( "form.variations_form select" ).length;
	
			if( $('.pure-table td.pm-td-price').not($(this)).hasClass('selected') ){
				$('.pure-table td.pm-td-price.selected').removeAttr('style');
			}

			$('.pure-table .pm-td-price').removeClass('selected');
			$(this).addClass('selected');
			
			var $attr = $(this).attr('data-attr');
			$('.nbtcs-swatches .swatch').removeClass('selected');


			$.each(JSON.parse($attr), function (key, pm) {
				$('[name="attribute_' + pm.name + '"]').val(pm.value);

				console.log('attribute_' + pm.name + '');
				console.log(pm.value);
				console.log('----------------');

				if( $('.nbtcs-swatches').length ) {
					$('.nbtcs-swatches [data-value="' + pm.value + '"]').addClass('selected');
				}
			});
			
			$( "form.variations_form .variations select.pm-select-last" ).trigger('change');

			if( nbt_solutions.is_scroll ) {
				$('html,body').animate({
					scrollTop: $("form.variations_form").find('[type="submit"]').offset().top - 150},
		        'slow');
			}
			
			pm_frontend.change_price_calculator();
		}
	}

	if($( ".pure-table" ).length > 0){
		pm_frontend.init();
	}
	
});