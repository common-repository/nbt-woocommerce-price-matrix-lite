<?php
class NBT_Price_Matrix_Settings{

	protected static $initialized = false;

	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}


		// State that initialization completed.
		self::$initialized = true;
	}

    public static function get_settings() {
        $settings = array(
            'show_on' => array(
                'name' => __( 'Price matrix table position', 'nbt-solution' ),
                'type' => 'select',
                // 'desc' => __( 'Vị trí hiển thị bảng bên cạnh ảnh hoặc phía dưới ảnh', 'nbt-solution' ),
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_on',
                'options'       => array(
                    'default'   => __( 'Default', 'nbt-solution' ),
                    'before_tab'    => __( 'Before Tab', 'nbt-solution' )
                ),
                'default' => 'default'
            ),
            'hide_info' => array(
                'name' => __( 'Hide Additional information', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_hide_info',
                'default' => false,
                'desc' => __('Hide Additional information tab in Product Details', 'nbt-solution')
            ),
            'show_calculator' => array(
                'name' => __( 'Show calculator text', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_calculator',
                'default' => false,
                'desc' => __('Show calculator text after Add to cart button', 'nbt-solution')                
            ),
            'is_heading' => array(
                'name' => __( 'Enable heading', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_is_heading',
                'default' => false,
                'desc' => __('Turn on heading before Price Matrix table', 'nbt-solution')                
            ),
            'heading_label' => array(
                'name' => __( 'Heading title', 'nbt-solution' ),
                // 'desc' => __( 'Hiển thị tiêu đề heading ở trên bảng', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_heading',
                'default' => ''
            ),
            'is_scroll' => array(
                'name' => __( 'Scroll when select price', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_is_scroll',
                'default' => false,
                'desc' => __('Scroll the screen to the Price Matrix table when user choose attributes', 'nbt-solution')                                
            ),
            'is_show_sales' => array(
                'name' => __( 'Display regular & sale price', 'nbt-solution' ),
                // 'desc' => __( 'Hiển thị giá giảm trong bảng', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_sales',
                'default' => false,
                'desc' => __('Display the sale price in the Price Matrix table', 'nbt-solution')                                
            ),
            array(
                'type' => 'border'
            ),
            'table_bg' => array(
                'name' => __( 'Background color of price matrix table', 'nbt-solution' ),
                // 'desc' => __( 'Chọn màu chính cho bảng', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_table',
                'default' => '#efefef',
            ),
            'table_color' => array(
                'name' => __( 'Table Text color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn màu chữ cho bảng', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_text',
                'default' => '#333'
            ),
            'border_color' => array(
                'name' => __( 'Table Border color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn màu border cho bảng', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_border',
                'default' => '#ccc'
            ),
            array(
                'type' => 'border'
            ),
            'bg_tooltip' => array(
                'name' => __( 'Tooltips background color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn background color cho tooltip', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_bg_tooltip',
                'default' => '#efefef'
            ),
            'color_tooltip' => array(
                'name' => __( 'Tooltips text color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn màu chữ cho tooltip', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_tooltip',
                'default' => '#333'
            ),
            'border_tooltip' => array(
                'name' => __( 'Tooltips border color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn border cho tooltip', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_border_tooltip',
                'default' => '#ccc'
            ),
            'font_size' => array(
                'name' => __( 'Font Size', 'nbt-solution' ),
                // 'desc' => __( 'Chọn kích thước chữ trong bảng', 'nbt-solution' ),
                'type' => 'number',
                'desc' => 'px',
                'id'   => 'wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_font_size',
                'default' => 14,
                'min' => 14,
                'max' => 50,
                'step' => 1
            )
        );
        return $settings;
    }
}
