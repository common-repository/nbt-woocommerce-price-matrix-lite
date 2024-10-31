<?php
/*
  Plugin Name: NBT WooCommerce Price Matrix (Lite)
  Plugin URI: https://wordpress.org/plugins/price-matrix-for-woocommerce/
  Description: WooCommerce Price Matrix helps to show the price of variable products become easier and more intuitive under price list.
  Version: 1.0.0
  Author: Netbaseteam
  Author URI: https://cmsmart.net
  Text Domain: nbt-price-matrix
  Domain Path: /languages
 */

define('NBT_PRICEMATRIX_PATH', plugin_dir_path( __FILE__ ));
define('NBT_PRICEMATRIX_URL', plugin_dir_url( __FILE__ ));

class NBT_Solutions_Price_Matrix {
    static $plugin_id = 'price-matrix';
    
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    public static $types = array();
    
    /**
     * Initialize functions.
     *
     * @return  void
     */
    public static function initialize() {
        // Do nothing if pluggable functions already initialized.
        if ( self::$initialized ) {
            return;
        }

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'install_woocommerce_admin_notice') );
        }else {
            $plugin = plugin_basename( __FILE__ );
            add_filter( "plugin_action_links_$plugin", array(__CLASS__, 'settings_link') );
            add_filter( 'body_class', array( __CLASS__, 'pm_body_classes') );

            add_action( 'init', array( __CLASS__, 'load_textdomain' ) );

            if( ! defined('PREFIX_NBT_SOL')){
                include(NBT_PRICEMATRIX_PATH . 'inc/settings.php');
            }

            require_once( NBT_PRICEMATRIX_PATH . 'inc/functions.init.php' );
            $GLOBALS['pm_settings'] = nb_get_setting(self::$plugin_id);

            require_once( NBT_PRICEMATRIX_PATH . 'inc/ajax.php' );
            require_once( NBT_PRICEMATRIX_PATH . 'inc/admin.php' );
            require_once( NBT_PRICEMATRIX_PATH . 'inc/frontend.php' );

            if ( !class_exists('NBT_Solutions_Metabox') ) {
                require_once NBT_PRICEMATRIX_PATH . 'inc/metabox.php';
                NBT_Solutions_Metabox::initialize();
            }
        }
        // State that initialization completed.
        self::$initialized = true;
    }

    public static function pm_direction() {
        return array(
            'vertical' => __('Vertical', 'nbt-price-matrix'),
            'horizontal' => __('Horizontal', 'nbt-price-matrix')
        );
    }


    public static function pm_body_classes( $classes ) {
        global $woocommerce, $post, $product;
        if(isset($post) && get_post_meta($post->ID, '_enable_price_matrix', true) == 'on' && get_post_meta($post->ID, '_pm_num', true)){
                $classes[] = 'wc-price-matrix';
        }
        return $classes;
    }

    public static function settings_link( $links ) {
        unset($links['edit']);
        $settings_link['configure'] = '<a href="'.admin_url('admin.php?page='.NBT_Solutions_Price_Matrix::$plugin_id).'">' . __( 'Configure' ) . '</a>';
        $settings_link['docs'] = '<a href="http://demo5.cmsmart.net/wordpress/plg_pricematrix/userguide.pdf">' . __( 'Docs' ) . '</a>';
        $settings_link['support'] = '<a href="https://cmsmart.net/support_ticket/" target="_blank">' . __( 'Support' ) . '</a>';

        $links = array_merge( $settings_link, $links );
        return $links;
    }
    /**
     * Method Featured.
     *
     * @return  array
     */
    public static function install_woocommerce_admin_notice() {?>
        <div class="error">
            <p><?php _e( 'WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>NBT WooCommerce Price Matrix (Lite)</strong>.', 'nbt-price-matrix' ); ?></p>
        </div>
        <?php    
    }

    public static function get_attribute_taxonomies($product_id, $attribute_name) {
        $transient_name = 'wc_attr_tax_' . md5($product_id . $attribute_name);
        $attribute_taxonomies = get_transient( $transient_name );
        if ( false === $attribute_taxonomies ) {
            global $wpdb;

            $attribute_taxonomies = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute_name'" );

            set_transient( $transient_name, $attribute_taxonomies );
        }
 
        return $attribute_taxonomies;
    }

    public static function load_textdomain() {
        load_plugin_textdomain( 'nbt-price-matrix', false, NBT_PRICEMATRIX_PATH . 'languages' );
    }
}

NBT_Solutions_Price_Matrix::initialize();