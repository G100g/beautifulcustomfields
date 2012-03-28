<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Multi Select Field

*/

class bcf_multiselect extends bcf_select {

	
	
	public function html_header() {
?>
	<style type="text/css" media="screen">
	
	#wpcontent .bcf_row.bcf_multiselect select.multiple {
		
		width: 100%;
		height: auto;
		
	}
	
	</style>

<?php		
	}

	public function show_field($field, $id, $name, $class, $active, $value) {
			
		$value = ($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '');
		
		if ($value != "") {
			$value = unserialize($value);
		} else {
			$value = array();
		}
			
?>
		<select multiple="multiple" size="5" id="<?php echo $id; ?>" name="<?php echo $name; ?>[]" class="multiple <?php echo $id. " " .$class; ?>"<?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?>>
			<?php $this->_select_values($this->_get_options($field), $value); ?>
		</select>
<?php						
			
	}
	
	public function save_field($field, $value, $name) {
	
		if (is_array($value) && !empty($value)) {
					$value = serialize( $value );
				}
		
		return parent::save_field($field, $value, $name);
	
	}

}