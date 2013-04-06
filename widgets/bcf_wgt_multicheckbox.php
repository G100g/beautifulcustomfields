<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Multi Checkbox Field

*/

class bcf_multicheckbox extends bcf_multiselect {

	
	
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
	
	public function _select_values($_name, $_class, $values, $selected_value = null) {
			
		if (!is_array($selected_value)) $selected_value = array($selected_value);
		
			foreach($values as $value) :

				if (strpos($value, "[::]")) {
					$value = explode("[::]", $value);
				} else {
					$value = array($value, $value);
				}

	?>		
				<label><input type="checkbox" id="<?php echo $_name; ?>_<?php echo $value[0]; ?>" name="<?php echo $_name; ?>[]" class="<?php echo $_class; ?>" value="<?php echo $value[0]; ?>" <?php echo (in_array($value[0], $selected_value)  ? 'checked="checked"' : ''  );?> /> <?php echo $value[1]; ?></label>
	<?php		
			endforeach;
			
		
		}
	

	public function show_field($field, $id, $name, $class, $active, $value) {
			
		$value = ($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '');
		
		if ($value != "") {
			$value = unserialize($value);
		} else {
			$value = array();
		}
			
?>		
		<p><?php $this->_select_values($name, $class, $this->_get_options($field), $value); ?></p>
<?php						
			
	}

}