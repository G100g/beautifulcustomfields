<?php
/**
 * @package bcf
 * @author G100g
 * @version 0.2
 */

/*
	
	Fields Types

*/

abstract class bcf_widget {

	public $multilanguage = 0; //Is multilanguage DEPRECATED
	public $single_columns = FALSE; //Show field single Column
	
	public $index = 0;
	
	//public static $widget_name = '';
	
	function __construct() {
	
		//__CLASS__::widget_name = get_class($this);
		//parent::__construct();
	}
	
	/*
	function bcf_text() {
	
	
	}
	*/
	
	/**
	
		Static Init
		
	**/
	
	public function init() {
	}
	
	/**
		
		Funzione eseguita nell'header di WP in modo globale per tutti i campi di questo tipo
		
	**/
		
	public function html_header() {
		
	}
	
	/**
		
		Option Init
		
		Eseguita dalla pagina delle opzioni
		
	**/
	
	public static function option_init() {

		//Nessun output

	}
	
	public function show_field($field, $id, $name, $class, $active, $value) {
	
		//$ml = $field->multiple == 1 ? '[]' : ''; //Valore multiplo
	
		/*
		if ($this->multilanguage && $field->multilanguage == 1 ) {
			$value = qtrans_split($value); //qtrans_getAvailableLanguages($value);
		}

		if (is_array($value)) {
			foreach ($value as $l => $v) {
				$id_lang = $id."_".$l;
				$res .= qtrans_getLanguageName($l).' <input size="10" ' . ( !$active ? ' disabled="disabled"' : ''  ) .' type="text" name="'.$id_lang.$ml.'" class="full '.$id." ".$id_lang.$class.'" value="'.($v !== false ? htmlentities($v, null, get_option('blog_charset')) : '').'"/><br />';
			}
		} else {
		*/
			//$res = '<input ' . ( !$active ? ' disabled="disabled"' : ''  ) .' type="text" name="'.$id.$ml.'" class="full '.$id.$class.'" value="'.($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '').'"/>';
		//}

		$value = ($value !== false ? htmlentities($value, ENT_QUOTES, get_option('blog_charset')) : '');
		
?>		
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" class="full <?php echo $id. " " .$class; ?>" <?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?> />
<?php
		//return $res;
		//echo $res;
		
	}
	
	/**
	
		Format data to save in custom field
		
	**/
	
	public function save_field($field, $value, $name) {
	
		return $value;
	
	}

}

require(dirname(__FILE__).'/widgets/bcf_wgt_text.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_textarea.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_richtext.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_checkbox.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_select.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_multiselect.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_multicheckbox.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_chooser.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_chooser_post_attachments.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_chooser_posts.php');
require(dirname(__FILE__).'/widgets/bcf_wgt_gallery.php');



add_filter('bcf_types', 'bcf_standard_widgets' );

function bcf_standard_widgets($types) {

	if (!is_array($types)) return $types;

	$types = array_merge( 
					$types, 
					array(
							'text' => "Text",
							'textarea' => "Textarea",
							'richtext' => "Richtext Editor",
							'checkbox' => "Single Checkbox",
							'multicheckbox' => 'Multi Checkbox',
							'select' => 'Select',
							'multiselect' => 'Multi Select',
							'chooser' => 'Chooser with autosuggest',
							'chooser_post_attachments' => 'Post Attachments',
							'chooser_posts' => 'Choose from Posts, Page And Custom Post Types',
							'gallery' => 'Galleria Immagini'
							
							/*, 
							'textarea' => "Textarea",
							'checkbox' => 'Checkbox',
							
							'selectmultiple' => 'Multiple Choice',
							'richcontent' => "Rich Content",
							'postattachments' => "Post Attachment",
							'selectmultipleattachments' => "Multiple Post Attachments",
							'suggest' => "Suggest",
							'menus' => "Menus"
							*/
						)
					);
	//, 'checkbox' => "Checkbox", 'radio' => 'Radio', 'select' => 'Select'

	return $types;

}