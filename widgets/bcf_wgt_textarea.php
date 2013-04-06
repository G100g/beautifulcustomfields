<?php
/**
 * @package bcf
 * @author G100g
 * @version 0.2
 */

/*
	
	Widget

*/


class bcf_textarea extends bcf_text {


	public function show_field($field, $id, $name, $class, $active, $value) {
		
			
//		$res = '<textarea ' . ( !$active ? ' disabled="disabled"' : ''  ) .' id="'.$id.'" name="'.$id.$ml.'" class="full '.$id.$class.'">'.($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '').'</textarea>';
		
		$value = ($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '');
		
?>		

		<textarea id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="full <?php echo $id. " " .$class; ?>"<?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?>><?php echo $value; ?></textarea>
		
<?php		
	
	}
	
}