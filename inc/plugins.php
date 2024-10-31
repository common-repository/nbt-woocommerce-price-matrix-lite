<?php
class NBT_Plugins {

	public $settings = array();
	/**
	 * Class constructor.
	 */
	public function __construct($args = array()) {
		if ( ! empty( $args ) ) {
			$this->settings = $args;

	        if( isset( $this->settings['create_menu_page'] ) && $this->settings[ 'create_menu_page'] ){
	            $this->add_menu_page();
	        }

	        add_action( 'admin_menu', array( $this, 'add_setting_page' ), 20 );
	    }

	}

	public function add_menu_page() {
		global $admin_page_hooks;

		if(!isset($admin_page_hooks['ntb_plugin_panel'])){
			$position = apply_filters( 'nbt_plugins_menu_item_position', '62.32' );
			add_menu_page( 'ntb_plugin_panel', 'NBT Plugins', 'manage_options', 'ntb_plugin_panel', NULL, 'dashicons-awards', $position );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_method') );
		}
	}
	public function admin_scripts_method(){
		wp_enqueue_style( 'font-nbt-plugins', $this->settings['font-path']  );
	}
	public function add_setting_page(){
	        $this->settings['icon_url'] = isset( $this->settings['icon_url'] ) ? $this->settings['icon_url'] : '';
		    $this->settings['position'] = isset( $this->settings['position'] ) ? $this->settings['position'] : null;
	        $parent = $this->settings['parent_slug'] . $this->settings['parent_page'];

	        if ( ! empty( $parent ) ) {
		        add_submenu_page( $parent, $this->settings['page_title'], $this->settings['menu_title'], $this->settings['capability'], $this->settings['page'], $this->settings['functions'] );
	        } else {
		        //add_menu_page( $this->settings['page_title'], $this->settings['menu_title'], $this->settings['capability'], $this->settings['page'], array( $this, 'yit_panel' ), $this->settings['icon_url'], $this->settings['position'] );
	        }
            /* === Duplicate Items Hack === */
            $this->remove_duplicate_submenu_page();
            do_action( 'nbt_after_add_settings_page' );
	}


        public function remove_duplicate_submenu_page() {
            /* === Duplicate Items Hack === */
            remove_submenu_page( 'ntb_plugin_panel', 'ntb_plugin_panel' );
        }



}