<?php
/**
 * @package bcf
 * @author G100g
 * @version 0.2
 */

/*
	
	Widget

*/


class bcf_richtext extends bcf_textarea {

	public $single_columns = TRUE;

	public function html_header() {
	?>
	
	<style type="text/css" media="screen">
		.bcf_richtext .postarea {
			border-color: #DFDFDF;
		}
		
		.bcf_richtext .postarea {
			
			background-color: #FFF;
			
			border-style: solid;
			border-width: 1px;
			border-collapse: separate;
			-moz-border-radius: 3px 3px 0 0;
			-webkit-border-top-right-radius: 3px;
			-webkit-border-top-left-radius: 3px;
			-khtml-border-top-right-radius: 3px;
			-khtml-border-top-left-radius: 3px;
			border-top-right-radius: 3px;
			border-top-left-radius: 3px;
		}
		
			#post-body .bcf_richtext .wp_themeSkin .mceStatusbar a.mceResize {
				top:  -2px;
			}
		
	</style>	
	<?php
	}
	
	public function show_field($field, $id, $name, $class, $active, $value) {
		
			
//		$res = '<textarea ' . ( !$active ? ' disabled="disabled"' : ''  ) .' id="'.$id.'" name="'.$id.$ml.'" class="full '.$id.$class.'">'.($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '').'</textarea>';
		
		//$value = ($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '');
		
?>		
	<div class="postarea">
	<?php if (!function_exists("wp_editor")) : ?>
		<div id="bcf_rc_editor_<?php echo $id; ?>" class="bcf_rc_editor_<?php echo $id; ?>"><textarea rows="3" <?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?> id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="theEditor <?php echo $id ." " . $class; ?>"><?php echo ($value !== false ? apply_filters( 'the_editor_content' , $value ) : ''); ?></textarea></div>
	
	<?php else: ?>
	<div id="bcf_rc_editor_<?php echo $id; ?>" class="bcf_rc_editor_<?php echo $id; ?>">
		<?php 
		
		$class = "theEditor ". $id ." " . $class;
		//$content = ($value !== false ? apply_filters( 'the_editor_content' , $value ) : '');
		$content = $value	;
		
		wp_editor( $content, $id, array("textarea_name" => $name, "textarea_rows" => 3, "textarea_class" => $class) );
		
	?>
		
	</div>
<?php endif; ?>
	
	</div>
	
	 
		
	</div>
		
<?php		
	
	}
	
}