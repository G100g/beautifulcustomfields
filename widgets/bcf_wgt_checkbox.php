<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Text Fields

*/

class bcf_checkbox extends bcf_widget {

	public function show_field($field, $id, $name, $class, $active, $value) {
		
			
//		$res = '<textarea ' . ( !$active ? ' disabled="disabled"' : ''  ) .' id="'.$id.'" name="'.$id.$ml.'" class="full '.$id.$class.'">'.($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '').'</textarea>';
		
		$value = ($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '');
		
?>		
		<input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="1" class="full <?php echo $id. " " .$class; ?>" <?php echo ( !$active ? ' disabled="disabled"' : ''  );  echo ($value == 1 ? 'checked="checked" ' : ''); ?> />
<?php		
	
	}
	
}