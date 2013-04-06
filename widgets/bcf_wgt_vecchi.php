<?php
/**
 * @package bcf
 * @author G100g
 * @version 0.2
 */

/*
	
	Fields Types

*/

add_filter('bcf_types', 'bcf_standard_types' );

function bcf_standard_types($types) {

	$types = array_merge( 
					$types, 
					array(
							'text' => "Text", 
							'textarea' => "Textarea",
							'checkbox' => 'Checkbox',
							'select' => 'Select',
							'selectmultiple' => 'Multiple Choice',
							'richcontent' => "Rich Content",
							'postattachments' => "Post Attachment",
							'selectmultipleattachments' => "Multiple Post Attachments",
							'suggest' => "Suggest",
							'menus' => "Menus"
							
						)
					);
	//, 'checkbox' => "Checkbox", 'radio' => 'Radio', 'select' => 'Select'

	return $types;

}

class bcf_postattachments extends bcf_text {

	function bcf_postattachments($multilanguage = 0) {
		parent::bcf_text($multilanguage);
	}
	
	function html_header() {
	}
	
	function get_attachments($post_ID) {
	
		$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post_ID ); 
		return get_posts($args);
	
	}
	
	function get_field($value, $_name, $_class, $active) {
	
		global $post;
	
?>
			<select name="<?php echo $_name; ?>" class="<?php echo $_class; ?>">
				<option value="" <?php echo ($value == "" ? "selected=\"selected\"" : ""); ?>></option>
<?php
		
			//Creo un select con i file allegati al post
			//$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID ); 
//			$attachments = get_posts($args);			
			$attachments = $this->get_attachments($post->ID);

			if ($attachments) :
				foreach ( $attachments as $attachment ) :
					$attachment_url = wp_get_attachment_url( $attachment->ID , false );
?>				
				<option value="<?php echo $attachment_url; ?>" <?php echo ($value == $attachment_url ? "selected=\"selected\"" : ""); ?>><?php echo apply_filters( 'the_title' , $attachment->post_title ); ?></option>
<?php				
				endforeach;
			endif;
?>
			</select>
			
<?php				
	
	}
	
	
	function show_field($field, $id, $class, $active, $value) {
	
		global $post;
	
		$ml = $field->multiple == 1 ? '[]' : ''; //Valore multiplo

		if ($this->multilanguage && $field->multilanguage == 1 ) {
			$value = qtrans_split($value); //qtrans_getAvailableLanguages($value);

		}

		if (is_array($value)) {
			foreach ($value as $l => $v) {
				$id_lang = $id."_".$l;
				//$res .= qtrans_getLanguageName($l).' <input ' . ( !$active ? ' disabled="disabled"' : ''  ) .' type="checkbox" name="'.$id_lang.$ml.'" class="'.$id.' '.$id_lang.$class.'" value="1" '.($value == 1 ? 'checked="checked" ' : '').' /><br />';
?>
				<p><?php echo qtrans_getLanguageName($l); ?><br />
				<?php $this->get_field($v, $id_lang.$ml, $id.' '.$id_lang.$class,$active); ?></p>
				
<?php				
			}
		} else {
			
			$this->get_field($value, $id.$ml, $id.$class, $active);
			
		}
		
	}

}

class bcf_selectmultipleattachments extends bcf_selectmultiple {

	function bcf_selectmultipleattachments($multilanguage = 0) {
		parent::bcf_selectmultiple($multilanguage = 0);
	}
	
	function html_header() {
	
?>

	<style media="all">
		
		.bcf_selectmultipleattachments input {
			width: auto;
		}
		
		.bcf_selectmultipleattachments label {
			padding-right: 10px;
		}
		
	</style>

<?php
	
	}
	/*
	function save_field($field, $cf_value, $multilanguage) {
	
		if (is_array($_REQUEST["bcf_".$cf_value]) && !empty($_REQUEST["bfc_".$cf_value])) {
			$_REQUEST["bfc_".$cf_value] = serialize( $_REQUEST["bfc_".$cf_value] );
		}

		return parent::save_field($field, $cf_value, $multilanguage);
	}
	
	function _select_values($_name, $_class, $type_options, $selected_value = null) {
		
		if (!is_array($selected_value)) $selected_value = array($selected_value);
	
		//Controllo se è specificata una funzione
		if ( strpos($type_options, '_function_') !== FALSE) {
			
			$type_options = str_replace('_function_', "", $type_options);
			$type_options = call_user_func($type_options);
		
		}	
	
		$values = explode(";",$type_options);
		
		foreach($values as $value) :
		
			if (strpos($value, ":")) {
				$value = explode(":", $value);
			} else {
				$value = array($value, $value);
			}
?>		
			<label for="<?php echo $_name; ?>_<?php echo $value[0]; ?>"><input type="checkbox" id="<?php echo $_name; ?>_<?php echo $value[0]; ?>" name="<?php echo $_name; ?>[]" class="<?php echo $_class; ?>" value="<?php echo $value[0]; ?>" <?php echo (in_array($value[0], $selected_value)  ? 'checked="checked"' : ''  );?> /> <?php echo $value[1]; ?></label>
<?php		
		endforeach;
		
	
	}
	*/
	
	function get_attachments($post_ID) {
	
		$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post_ID ); 
		return get_posts($args);
	
	}
	
	function show_field($field, $id, $class, $active, $value) {
	
		global $post;
	
		$ml = $field->multiple == 1 ? '[]' : ''; //Valore 

		if ($this->multilanguage && $field->multilanguage == 1 ) {
			$value = qtrans_split($value); //qtrans_getAvailableLanguages($value);
		}
	
		if (is_array($value)) {
			foreach ($value as $l => $v) {
				$id_lang = $id."_".$l;
				//$res .= qtrans_getLanguageName($l).' <input ' . ( !$active ? ' disabled="disabled"' : ''  ) .' type="checkbox" name="'.$id_lang.$ml.'" class="'.$id.' '.$id_lang.$class.'" value="1" '.($value == 1 ? 'checked="checked" ' : '').' /><br />';
				
				//Value diventa un array
				
				if ($v != "") {
					$v = explode("::", $v);
				} else {
					$v = array();
				}
				
?>				
			<p><?php echo qtrans_getLanguageName($l); ?>
					<?php $this->_select_values($field->type_options, $v); ?>
				<?php $this->_select_values($id_lang.$ml, $id.$class." ".$id_lang.$class, $field->type_options, $v); ?>
			</p>
<?php				
			}
		} else {
			
			//Value diventa un array
			//$value = explode("::", $value);
			if ($value != "") {
				$value = unserialize($value);
			} else {
				$value = array();
			}
			
			$attachments = $this->get_attachments($post->ID);
			
			$e = array();
			
			if ($attachments) {
				foreach ( $attachments as $attachment ) {
					$attachment_url = wp_get_attachment_url( $attachment->ID , false );
					
					//Elimino l'url del blog
					$attachment_url = str_replace(get_bloginfo('wpurl'), '', $attachment_url);
					
					$e[] = $attachment_url.":". apply_filters( 'the_title' , $attachment->post_title );
				}
			}			

			$field->type_options = implode(";", $e);
?>
				<?php $this->_select_values($id.$ml, $id.$class, $field->type_options, $value); ?>
<?php						
			
		}
		
	}

}


/**


	MENUs 
	

**/

class bcf_menus extends bcf_select {

	function bcf_menus($multilanguage = 0) {
		parent::bcf_select($multilanguage = 0);
	}
	
	function html_header() {
	}
	
	function show_field($field, $id, $class, $active, $value) {
		
		$menus = wp_get_nav_menus();
		
		$_menus = " :  ;";
		
		$sep = "";
		foreach ($menus as $menu) {
		
			$_menus .= $sep. $menu->term_id . ":".$menu->name;
			if ($sep == "") {
				$sep = ";";
			}
			
		}
		
		$field->type_options = $_menus;
			
		parent::show_field($field, $id, $class, $active, $value);
	}

}

/*
 * 
 * Suggest
 * 
 *  
 */

class bcf_suggest extends bcf_select {

	function bcf_suggest($multilanguage = 0) {
		parent::bcf_select($multilanguage = 0);
	}
	
	function init() {			
		wp_enqueue_script('jquery-tokeninput', WP_PLUGIN_URL.'/beautifulcustomfields/lib/jquery-tokeninput/jquery.tokeninput.js', array('jquery'), '1.5.0');
		wp_enqueue_style('jquery-tokeninput-css', WP_PLUGIN_URL.'/beautifulcustomfields/lib/jquery-tokeninput/token-input-wp.css','', '1.5.0');
	}
	
	function html_header() {
?>	
		<style>
		.ui-state-highlight {
			height: 30px;
			background-color: #EEE;
			margin: 10px;
		}
		
		</style>
		
<?php	
	}
	
	function show_field($field, $id, $class, $active, $value) {

		$cf_value = "bcf_".$field->cf_value;

		$class .= " " .$cf_value;
		
		$ml = $field->multiple == 1 ? '[]' : ''; //Valore multiplo
	
		if ($this->multilanguage && $field->multilanguage == 1 ) {
			$value = qtrans_split($value); //qtrans_getAvailableLanguages($value);
		}

		if (is_array($value)) {
			foreach ($value as $l => $v) {
				$id_lang = $id."_".$l;
				$res .= qtrans_getLanguageName($l).' <input size="10" ' . ( !$active ? ' disabled="disabled"' : ''  ) .' type="text" name="'.$id_lang.$ml.'" class="full '.$id." ".$id_lang.$class.'" value="'.($v !== false ? htmlentities($v, null, get_option('blog_charset')) : '').'"/><br />';
			}
		} else {
			
			$res = '<input ' . ( !$active ? ' disabled="disabled"' : ''  ) .' type="text" name="'.$id.$ml.'" class="full '.$id.$class.'" value="'.($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '').'"/>';
		}

		//Creo la lista dei campi già inseriti
		
		
		
		//Ottengo l'elenco
		$selected_options = explode(",", $value);
		$json_selected_options = array();
		
		if (!empty($value)) {
			
			$options = $this->_get_options($field, $value);
//			var_dump($options);
			
			foreach($options as $k => $option) {
					
				$option = explode(":##:", $option);
							
				if (in_array($option[0], $selected_options)) {
	
					$json_selected_options[] = (object) array("id" => $option[0], 'name' => $option[1]);
					
				}
				
			}
		
		}
		
		//return $res;
		echo $res;
		
		$options["tokenLimit"] = null;
		
		//Applico il filtro	
		$options = apply_filters(get_class($this) . "_options", $options);
		//Applico il filtro specifico al campo		
		$options = apply_filters(get_class($this).'_options_'.$field->cf_value, $options);
		
?>
	<script type="text/javascript">
	jQuery(function($) {
	
		var $searchable = $("input.<?php echo $cf_value; ?>");
			
		$searchable.tokenInput( ajaxurl + '?action=bcf_suggest_source&cf_value=<?php echo $field->cf_value; ?>', 
														{ 
														prePopulate: <?php echo json_encode($json_selected_options); ?>,
														animateDropdown: false,
														preventDuplicates: true,
														minChars: 2,
														tokenLimit: <?php echo is_numeric($options["tokenLimit"]) ? $options["tokenLimit"] : 'null'; ?>,
														theme: 'wp'
														<?php if ($options["tokenLimit"] != 1) : ?>
														,
														onReady: function () {
															$( ".<?php echo $cf_value; ?> ul.token-input-list-wp" ).sortable({
																placeholder: 'ui-state-highlight',
																update: function (event, ui) {
																	$searchable.tokenInput('updateHidden');
																}
															});
															$( ".<?php echo $cf_value; ?> ul.token-input-list" ).disableSelection();
															
														}
														<?php endif; ?>		
														});		
	});
	</script>
		
<?php		
		
		//parent::show_field($field, $id, $class, $active, $value);
	}

}

function bcf_suggest_source() {

	$cf_value = $_REQUEST["cf_value"];
	
	//Ritrovo il campo definito nel backedn
	$bcf_fields = unserialize( get_option('bcf_fields') );
	
	$bcf_field = $bcf_fields[0][$cf_value];
	$bcf_field->cf_value = $cf_value;
	
	//Becco i dati del campo detto 
	$class_name = "bcf_suggest";
	
<<<<<<< HEAD
	if ( class_exists( $class_name ,false) ) {
=======
	if ( class_exists( $class_name ) ) {
>>>>>>> origin/master
		$f = new $class_name();
		if (method_exists($f, '_get_options') ) {
			$options = $f->_get_options($bcf_field);
		}
	}
	$source = array();
	foreach($options as $k => $option) {
		$option = explode(":##:", $option);
		$source[] = (object) array("id" => $option[0], 'name' => $option[1]);
	}

	echo json_encode($source);
	
	die();
}

add_action('wp_ajax_bcf_suggest_source', 'bcf_suggest_source', 120);







 
