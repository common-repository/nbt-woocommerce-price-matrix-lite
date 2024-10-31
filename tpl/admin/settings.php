<?php
$all_settings = NBT_Price_Matrix_Settings::get_settings();
$options = get_option(NBT_Solutions_Price_Matrix::$plugin_id.'_settings');
$key_id = str_replace('-', '_', NBT_Solutions_Price_Matrix::$plugin_id);


if(isset($_POST['submit-'.$key_id])){
	$options = array();
	foreach ($all_settings as $key => $value) {
		if( isset($value['id']) && isset($_POST[$value['id']]) && $id = $value['id']){
			$options[$id] = $_POST[$id];
		}
		
	}
	?>
	<div id="setting-error-settings_updated" class="updated settings-error">
		<p><strong><?php _e('Settings Saved', 'nbt-plugins') ?></strong></p>
	</div>
	<?php
	delete_transient(NBT_Solutions_Price_Matrix::$plugin_id.'_settings');
	update_option(NBT_Solutions_Price_Matrix::$plugin_id.'_settings', $options);
}
?>

    <form action="<?php echo admin_url('admin.php?page='.NBT_Solutions_Price_Matrix::$plugin_id) ?>" method="post" >
    	<table class="form-table">
    		<tbody>
			<?php foreach ($all_settings as $key => $set) {
				echo NBT_Solutions_Metabox::show_field($set, $options);
			}?>
			</tbody>
		</table>
		<div class="submit">
			<?php wp_nonce_field( 'plugin-settings' ) ?>
			<input type="submit" name="submit-<?php echo $key_id;?>" value="<?php _e('Save Changes', 'nbt-solution-core') ?>" class="button-primary" />
		</div>
	</form>