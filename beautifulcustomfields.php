<?php
/**
 * @package bcf
 * @author G100g
 * @version 1.0.0
 */
/*
Plugin Name: Beautiful Custom Fields
Plugin URI: http://g100g.net/
Description: Give dignity to custom fields
Author: G100g
Version: 1.0.0
Author URI: http://g100g.net/
*/
/*
class Beautiful_custom_fields_types {

	var $types = array();// = array('text' => "Text", 'textarea' => "Textarea", 'checkbox' => "Checkbox", 'radio' => 'Radio', 'select' => 'Select');
	var $multilanguage = false;
	
	function __construct() {
	
		//if ( function_exists('qtrans_getLanguage') ) $this->multilanguage = true;
	}
	
	function get_widget_object($field) {
	
		//Inizializzo il campo
		$class_name = "bcf_".$field->type;
		if ( !class_exists( $class_name ,false) ) {
			$class_name = "bcf_text";
		}
		
		$f = new $class_name($this->multilanguage);
		
		return $f;
	}

	
	function get_types() {
		return $this->types;
	}

}
*/

require(dirname(__FILE__).'/bcf_default_widgets.php');
require(dirname(__FILE__).'/bcf_admin.php');

class Beautiful_custom_fields {

	var $fields_types = array();
	var $multilanguage = false;
	var $lang = null;

//	function Beautiful_custom_fields() {
	function __construct() {
	
		//$this->fields_types = new Beautiful_custom_fields_types();
		//$this->init_options();
	}
	
	/**
	
		Field type
		
	**/
	
	private function get_widget_object($field) {
		
		//Inizializzo il campo
		$class_name = "bcf_".(is_object($field) ? $field->type : $field);
		
		if ( !class_exists( $class_name ,false) ) {
			$class_name = "bcf_text";
		}
		//$f = new $class_name($this->multilanguage);
		$f = new $class_name();
		
		return $f;
	}
	
	function get_option_fields() {
		
		$bcf_fields = get_option('bcf_fields');
		
		$bcf_fields = !empty($bcf_fields) ? unserialize( $bcf_fields ) : array();
		
		if (!is_array($bcf_fields)) $bcf_fields = array();
		
		return $bcf_fields;
	
	}
	
	function get_option_boxs() {
		
		$bcf_boxs = get_option('bcf_boxs');
		
		$bcf_boxs = !empty($bcf_boxs) ? unserialize( $bcf_boxs ) : array();
		
		if (!is_array($bcf_boxs)) $bcf_boxs = array();
		
		//return is_string($bcf_boxs) ? unserialize( $bcf_boxs ) : $bcf_boxs;
		return $bcf_boxs;
	
	}
	
	public function init_options() {
				
		//add_action('load-page-new.php',array(&$this,'rich_editor')); 
		//add_action('load-page.php',array(&$this,'rich_editor'));
		
		if ($this->check_multilanguage()) {
			$this->lang = qtrans_getSortedLanguages();
		}
		
		
		//wp_enqueue_script( 'jquery-ui-widget');
		//wp_enqueue_script( 'jquery-ui-widget');
//		wp_enqueue_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js', array('jquery'), '1.8.6');
		//wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/base/jquery-ui.css','', '1.8.6');
		$this->rich_editor();
		
		//Eseguo l'init dei plugin
		$this->fields_types = apply_filters ( 'bcf_types', $this->fields_types ) ;
		
		//Leggo i form
		foreach ($this->fields_types as $widget_name => $label) {
		
			$wgt_class = $this->get_widget_object($widget_name);
			
			if ($wgt_class) {

				if (method_exists($wgt_class, 'init') ) {

					if(is_callable(array($wgt_class,"init"))) {									
						$wgt_class->init();				
					}
				}
			
			}
			/**
			
				STAICO
			$class_name = "bcf_".$cf_name;
			if ( class_exists( $class_name ,false) ) {								
				if (method_exists($class_name, 'init') ) {
					if(is_callable(array($class_name,"init"))) {
						call_user_func(array($class_name, 'init'));
					}
				}
			}
			
			**/
		}
		
	}
	
	function rich_editor() {
	
		
		add_filter('mce_buttons', array(&$this,'rich_editor_button'));
		//add_action('admin_head', array(&$this,'rich_editor_styles'));
	}
	
	function rich_editor_button($buttons) {
				
		array_push($buttons, "code"); //add HTML view		
		array_push($buttons, "separator","add_image","add_video","add_media","add_audio");
		
		return $buttons;
	}	
	
	function check_multilanguage(){		
		if ( function_exists('qtrans_getLanguage') ) {
			$this->multilanguage = true; 
//			$this->fields_types->multilanguage = true;
		}
		return $this->multilanguage;
	}
	
	function add_javascript(){
				
		//wp_enqueue_script('jquery');
		//wp_enqueue_script('jquery-ui-core');
		//enqueue jQuery DatePicker
		//wp_register_script('jquery_datepicker', get_bloginfo('wpurl') . '/wp-content/plugins/supple-forms/js/ui.datepicker.js', array('jquery'), '1.0');
		//wp_enqueue_script('jquery_datepicker');
				
		//enqueue SuppleForms Javascript
		//wp_register_script('supple_js', get_bloginfo('wpurl') . '/wp-content/plugins/supple-forms/js/supple-admin.js', array('jquery'), '1.0');
		//wp_enqueue_script('supple_js');
				
	}
	
	public function set_header(){		
	
		global $post;
		
?>
		<script type="text/javascript">
		
		<!--//
		
		/**
			
			Beautiful Custom Field 			
		
		**/
		
		jQuery.fn.clearForm = function($) {
			return this.each(function() {
			
				var type = this.type, tag = this.tagName.toLowerCase();
				if (tag == 'form') return $(':input',this).clearForm();
				if (type == 'text' || type == 'password' || tag == 'textarea') this.value = '';
				else if (type == 'checkbox' || type == 'radio')
					this.checked = false;
				else if (tag == 'select')
				this.selectedIndex = -1;
			});
		};
		
		var bc_fields_class = ['bcf_post_type_<?php echo get_post_type($post->ID);?>'];
		var bc_fields_names = [];
		
		<?php if (get_post_type($post->ID) == 'page') : ?>
		bc_fields_class.push('bcf_page_template_default');
		<?php endif; ?>
		
		jQuery(function ($) {

			function bc_update_forms_fields(_field, _action) {
			
				var _bc_fields_names = [];
			
				var in_array = false;
				
				//Leggo l'array di classi e lo ricostruisco
				for (var i in bc_fields_names) {
					
					//if (_action != 'remove' && bc_fields_class[i] != _class) {
					if (bc_fields_names[i] != _field) {
						
						_bc_fields_names.push(bc_fields_names[i]);
						
					} else {
						if (_action == 'remove') {
							in_array = true;
						} else{
							in_array = false;
						}
					}
					
				}
				
				if (in_array == false && _action == 'add') {
					_bc_fields_names.push(_field);
				}
				
				bc_fields_names = _bc_fields_names;
			
			}
		
			function bc_update_forms_class(_class, _action) {
			
				var _bc_fields_class = [];
			
				var in_array = false;
				
				//Leggo l'array di classi e lo ricostruisco
				for (var i in bc_fields_class) {
					
					//if (_action != 'remove' && bc_fields_class[i] != _class) {
					if (bc_fields_class[i] != _class) {
						
						_bc_fields_class.push(bc_fields_class[i]);
						
					} else {
						if (_action == 'remove') {
							in_array = true;
						} else{
							in_array = false;
						}
					}
					
				}
				
				if (in_array == false && _action == 'add') {
					_bc_fields_class.push(_class);
				}
				
				bc_fields_class = _bc_fields_class;
			
			}
			
			function bc_update_forms() {				
				var box_class = '';
				var sep = ".";
				
				for (var i in bc_fields_class) {
					box_class += sep+bc_fields_class[i];				
				}

				//Disattivo quelli che non corrispondono
				
				$('.postbox tr.bcf_row').hide();
				//$('.postbox tr.bcf_row :input').attr('disabled', 'disabled');

				//attivo quelli con determinate classi					
				$('.postbox tr'+box_class).show();
				$('.postbox tr'+box_class+' :input').attr('disabled', false);
				
				//Attivo quelli che devono essere mostrati a priori
				
				for (var i in bc_fields_names) {
					//box_class += bc_fields_names[i];				
					$('.postbox tr.'+bc_fields_names).show();	
					$('.postbox tr.'+bc_fields_names+' :input').attr('disabled', false);
					
				}
				
				console.log(bc_fields_names);
				
				/*
				$('#bcf_metabox tr.bcf_row').hide();
				$('#bcf_metabox tr.bcf_row :input').attr('disabled', 'disabled');

				//attivo quelli con determinate classi					
				$('#bcf_metabox tr'+box_class).show();
				$('#bcf_metabox tr'+box_class+' :input').attr('disabled', false);
				
				//Attivo quelli che devono essere mostrati a priori
				for (var i in bc_fields_names) {
					//box_class += bc_fields_names[i];				
					$('#bcf_metabox tr.'+bc_fields_names).show();	
					$('#bcf_metabox tr.'+bc_fields_names+' :input').attr('disabled', false);
					
					console.log("nome", bc_fields_names);
				}
				*/
				
			}
		
<?php

	//Ritrovo i campi e applico il click per morstrarli quando si seleziona una categoria dentro un post
		//$bcf_fields = unserialize( get_option('bcf_fields') );
		
		$bcf_fields = $this->get_option_fields();
		
		//Costruisco l'array che determina categoria e campi da mostrare
		$taxonomy_fields = array();
				
		//Leggo i campi creati e creo un array con Taxnomi-ID e id del campo
		

		foreach ($bcf_fields as $box_id => $fields) {
			foreach ($fields as $cf_name => $f) {
				
				if (is_array($f->taxonomy)) {
					foreach ($f->taxonomy as $tax => $term) {
						$taxonomy_fields[$term][] = 'bcf_'.$cf_name;
					}
				
				}		
				
			}
		}
		
		foreach($taxonomy_fields as $term => $field) : ?>
	
		var bcf_taxonomy_click_action = function (e) {

<?php		foreach($field as $f) : ?>
			
			bc_update_forms_fields('<?php echo $f; ?>', ($(this).attr('checked') || e.type == 'bcf_taxonomy_click_action' ? 'add' : 'remove'));
			
<?php		endforeach; ?>		

			bc_update_forms();

		};	
		
		$('#<?php echo $term; ?> :checkbox, #popular-<?php echo $term; ?> :checkbox')
					.click(bcf_taxonomy_click_action)
					.bind('bcf_taxonomy_click_action', bcf_taxonomy_click_action);	
				
		
<?php		endforeach;
		
		
		//Becco le taxonomy associate al post
		
		/*
		
		$taxonomy = get_taxonomies(
						array('public'   => true),
						'objects'
					);
		
		foreach($taxonomy as $tax) :
		
			//Becco i termini
			$terms  = get_terms($tax->name, array('hide_empty' => 0));
		
			//$categories = get_categories(array('hide_empty'=> false));
		
		/*
		foreach ($bcf_fields[0] as $cf_value => $field) {
			foreach ($field->categories as $cat) {
				$categories[$cat->cat_ID] = $cf_value;
			}
		}
		*/
		//foreach ($terms as $term) {
		
		$post_type_taxonomies = get_object_taxonomies( $post );
		
		foreach ($post_type_taxonomies as $ptc) :
		
			
		
?>			
			$('#<?php echo $ptc; ?>checklist :checkbox:checked').trigger('bcf_taxonomy_click_action');
			//Template
<?php
			
		endforeach;
			
			if (get_post_type($post->ID) == 'page') :
			
			?>
			
			var last_depth = 'level--1';
			function check_parent(level) {

				bc_update_forms_class('bcf_depth_'+last_depth, 'remove');
				
				if (level == undefined || level == "") {
					level = 'level--1';
				}
				
				last_depth = level;
				bc_update_forms_class('bcf_depth_'+level, 'add');
				
				bc_update_forms();

			}
			
			//Depth
			$('select#parent_id').change( function () {
			//$('#in-category-<?php //echo $k; ?>').click( function () {
				var id = $(':selected', this).attr('class');
				check_parent(id);	
			
			});
			
			var id = $('select#parent_id :selected').attr('class');
			check_parent(id);
			
			var last_template = 'default';
			function check_template(template) {

				template = template.replace(".", "-");
				template = template.replace("/", "-");

				bc_update_forms_class('bcf_page_template_'+last_template, 'remove');
				
				if (template == undefined || template == "") {
					template = 'default';
				}
				
				last_template = template;
				bc_update_forms_class('bcf_page_template_'+last_template, 'add');
				
				bc_update_forms();

			}
			
			$('select#page_template').change( function () {
			//$('#in-category-<?php //echo $k; ?>').click( function () {
				var id = $(':selected', this).val();
				check_template(id);	
			
			});
			
			var id = $('select#page_template :selected').val();
			check_template(id);
			
			<?php endif; ?>
			
			
			
			//Initi Depth
			//$('#bcf_metabox tr.bcf_depth').hide();
			//$('#bcf_metabox tr.bcf_depth :input').attr('disabled', 'disabled');
			
			
			bc_update_forms();
			
			
			//Active 
			
			//Azione more
			$('a.one-more').live('click', function () {
				/*
				var f = $(this).parent().parent().parent();
				var c = f.clone();
				$(':input', c).clearForm();
				//clearForm();
				c.children().children().children('a.one-less').show();
				//c.clearForm();
				
				f.after( c );
				*/
				
				var f = $(this).parent().parent().parent();
				
				//Loading del campo in più via ajax
				$.get(ajaxurl, {action: 'bcf_get_new_field', cf_name: $(this).attr('href'), post_id: '<?php echo $post->ID; ?>'}, function (rt) {				
					f.after( rt );
				});
				
				return false;
				
			});
			
			//Remnove
			$('a.one-less').live('click', function () {
				$(this).parent().parent().parent().remove();
				return false;
			});
			
					
<?php 	//} ?>		
		});
		
		
		//-->
		</script>
		
		<style type="text/css" media="screen">
		
			.inner-sidebar tr.bcf_row th,
			.inner-sidebar tr.bcf_row td {
				clear: both;
				display: block;
				float: left;
				width: 95%;
			}
			
				.inner-sidebar tr.bcf_row th {
					padding-bottom: 0;
					
				}
				
		
			input.full,
			textarea.full {
				width: 95%;
			}
		
			.edButtonBCF { cursor:pointer; display:block; float:right; height:18px; margin:5px 5px 0px 0px; padding:4px 5px 2px; border-width:1px; border-style:solid;-moz-border-radius: 3px 3px 0 0; -webkit-border-top-right-radius: 3px; -webkit-border-top-left-radius: 3px; -khtml-border-top-right-radius: 3px;-khtml-border-top-left-radius: 3px; border-top-right-radius: 3px; border-top-left-radius: 3px; background-color:#F1F1F1; border-color:#DFDFDF; color:#999999; }
			.edButtonBCF.active {
				border-bottom-color: #E9E9E9;
				background-color: #E9E9E9;
				color: #333;
			}
		
			.bcf_richcontent .mceEditor.wp_themeSkin { 
				display: block;
				padding: 5px 0 0 0;
				border: 1px solid #CCC;
				background-color: #E9E9E9;
				-moz-border-radius: 6px 6px 0 0;
				-webkit-border-top-right-radius: 6px;
				-webkit-border-top-left-radius: 6px;
				-khtml-border-top-right-radius: 6px;
				-khtml-border-top-left-radius: 6px;
				border-top-right-radius: 6px;
				border-top-left-radius: 6px;
				
			}
				
			 
			.bcf_richcontent .mce_wp_more, #content_code, .bcf_richcontent .mce_fullscreen, #content_add_image, #content_add_video, #content_add_media, #content_add_audio { display: none !important; } 
			.bcf_richcontent .mceResize { top: 0 !important; }
		
		</style>
		
		
<?php

		//Eseguo il codice di testa dei plugin
		//$bcf_fields = unserialize( get_option('bcf_fields') );
		//$this->fields_types->types = apply_filters ( 'bcf_types', $this->fields_types->types ) ;
		
		//Leggo i form
		foreach ($this->fields_types as $cf_name => $label) {
			
			//Inizializzo l'oggetto ed eseguo il codice
			$class_name = "bcf_".$cf_name;
			if ( class_exists( $class_name ,false) ) {
				
				$f = new $class_name();				
				if (method_exists($f, 'html_header') ) {
					$f->html_header();
				}
				
				/*
				if (method_exists($class_name, 'html_header') ) {
					if(is_callable(array($class_name,"html_header"))) {
						call_user_func(array($class_name, 'html_header'));
					}
				}
				*/

			}
		}
		
			
	}
		
	function attach_meta_box() {
	  //Note:  Supple Forms requires WP 2.5 or greater
	  if( function_exists( 'add_meta_box' )) {
		
		//INserisco i box
		
		$option_boxs = $this->get_option_boxs();
		$option_fields = $this->get_option_fields();
		
		//Creo una lista per determinare 
		
		global $post;
		
		$post_type = get_post_type( $post->ID );
		$post_type = $post_type == "" ? "post" : $post_type;
		
		//Leggo i field dei boxper determinare se il box deve apparire in questo posttype
		
		$boxs_to_show = array();
		
		foreach ($option_fields as $box_id => $fields) {
		
			if (is_array($fields)) {
		
				foreach ($fields as $field_name => $field) {
				
					if (is_array($field->custom_post_types)) {
					
						if (
							in_array($post_type, $field->custom_post_types)
							) {
							$boxs_to_show[$box_id] = $option_boxs[$box_id];
							break;
						}
					
					} else if ( is_array($field->posts_ids)) {
						
						if (
							in_array($post->ID, $field->posts_ids)
							) {
							$boxs_to_show[$box_id] = $option_boxs[$box_id];
							break;
						}
					
					}
				
				}
			
			}
		
		}
		
		/*
		
			if (!array_key_exists($box_id, $boxs_to_show)) {
		
				foreach ($option_fields[$box_id] as $field_name => $field) {
					
					var_dump($field_name);
					
					if (is_array($field->custom_post_types)) {
					
						if (in_array($post_type, $field->custom_post_types)) {
							$boxs_to_show[$box_id] = $box;
							break;
						}
					
					}
					
				}
			
			}
		}
		*/
		//	$post_types = (is_array($field->custom_post_types) ? $field->custom_post_types : array() );
								
		//Ho i box da visualizzare e li visualizzo

		foreach ($boxs_to_show as $box_id => $box) {
			
			add_meta_box( 'bcf_metabox_'.$box_id
				, __( $box["title"], 'bcf_textdomain' )
				, array(&$this,'populate_box'), $post_type, $box["context"], $box["priority"], array('fields' => $option_fields[$box_id]) );
	
	
		}
		
		/*		
		add_meta_box( 'bcf_metabox'
			, __( "Content Options", 'bcf_textdomain' )
			, array(&$this,'populate_box'), 'post', 'normal', 'core' );
	   
		add_meta_box( 'bcf_metabox'
			, __( "Content Options", 'bcf_textdomain' )
			, array(&$this,'populate_box'), 'page', 'normal', 'core' );
			
		
		foreach (
				get_post_types(array(
					'public'   => true,
					'_builtin' => false
				)) as $custom_post_type) {
		
			add_meta_box( 'bcf_metabox'
				, __( "Content Options", 'bcf_textdomain' )
				, array(&$this,'populate_box'), $custom_post_type, 'normal', 'core' );
		}
		*/
	   }
	   
	   
	}
	
	function populate_box($post, $metabox) {
	
//		global $post;

		$bcf_fields = $metabox['args']['fields'];
		
//		var_dump($bcf_fields);

		$this->check_multilanguage();
		
		//Becco i widget
		$this->fields_types = apply_filters ( 'bcf_types', $this->fields_types ) ;
	
		//Becco i field creati
		//$bcf_fields = unserialize( get_option('bcf_fields') );

		//Leggo i form
		

		if (is_array($bcf_fields)) { //Path per il multi BOX da implementare
		

			$bcf_fields = $this->multisort_obj($bcf_fields, 'sort', true, 2); //Sorting
		

		if (is_array($bcf_fields) && !empty($bcf_fields)) :
		
?>
	<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
<?php			

		foreach ($bcf_fields as $cf_name => $field) {
						
			$this->add_field_to_box($cf_name, $field);
		
		}
?>
	</table>
<?php			
			endif;
		
		} else {
			echo '<p>No options for this content.</p>';
		}
	}
	
	
	function add_field_to_box($cf_name, $field, $blank_field = FALSE) {
	
		global $post;
		$post_type = get_post_type( $post->ID );
		
		//Se sono una pagina e non il campo non è per delle pagine lo sego direattamente
		
		if(!is_array($field->custom_post_types)) $field->custom_post_types = array();
		//$post_types = (is_array($field->custom_post_types) ? $field->custom_post_types : array() );
		$post_types = $field->custom_post_types;
		
//		$post_types[] = "page";
//		$post_types[] = "post";
		
		/*
		if (empty($field->pages)) {
			$post_types[] = "pages";
		}
		if (empty($field->pages)) {
			$post_types[] = "pages";
		}
		*/
		
		//if ($post_type == 'page' && empty($field->pages) && empty($field->custom_post_types)) return;
		
		//Se sono un post ed il campo non è per dei post lo sego ameno che non abbia un custom post typ eseganto
		//if ($post_type != 'page' && empty($field->categories) && empty($field->custom_post_types)) return;
		
		//Controllo che il campo debba apparire
		if ($blank_field == TRUE) {
			$active = TRUE;
		} else {
			$active = false;
		}
		//Se il campo ha specificato l'id del post lo visualizzo

/*		
		if (in_array($post->ID, $field->posts_ids)) {
			$active =  TRUE;
		}
*/		
		
		/*
		if (
				(in_category( $field->categories ) && get_post_type( $post->ID ) == 'post' ) ||
				( in_array( $post->ID, (is_array($field->pages) ? $field->pages : array() ) ) && get_post_type( $post->ID ) == 'page' ) ||
				( in_array( get_post_type( $post->ID ), (is_array($field->custom_post_types) ? $field->custom_post_types : array() ) ) )
			
			) { 
			
			$active = true;
			
		}
		*/

		$name = "bcf_".$cf_name;
		$field->cf_name = $cf_name;
		$label = $field->name;
		//$categories = $field->name;

		//Controllo se il post ha il custom field
		if ($blank_field == FALSE) {

			$post_meta = get_post_meta( $post->ID, $cf_name );
			
		} else {
			$post_meta = null;	
		}
		
		if (empty($post_meta)) $post_meta = array(""); //Se non ha il meta ne genero cmq uno vuoto
		
		//Creo le classi in base alla categoria in cui deve apparire
		$class = '';
		
		if ( is_array($field->taxonomy) ) {
		
			foreach ($field->taxonomy as $tax_term  ) {
				$class .= ' bcf_'.$tax_term;
			}
		
		}
		
		$res = "";
		$i = 0;

		foreach ($post_meta as $pm) {

			//$res = $this->fields_types->get_field( $field, $id, $class, $active, $pm );
			$class_depth = '';


			/**
			
				Genero le classi per la visualizzazione in base al depth
				
			**/
			
			if ($field->depth == "") {
				$field->depth = 0;
			}
			
			if (strpos($field->depth, ",")) {
		
				$depths = explode(",", $field->depth);
		
				foreach($depths as $depth) {
					$class_depth .= ' bcf_depth_level-'.($depth-1);
				}
			
			} else {
				$class_depth .= ' bcf_depth_level-'.($field->depth-1);
			}
			
			$class_depth .= '  bcf_depth';
//			}
			
			$class_post_type = "";
			foreach ($post_types as $_t) {
				$class_post_type .= " bcf_post_type_".$_t;	
			}
			
			if (is_array($field->posts_ids)) {
			
				if (in_array($post->ID, $field->posts_ids)) {
					//Se indicato, aggiungo la tipologia del
					$class_post_type .= " bcf_post_type_".get_post_type($post->ID);
				}
			
				foreach ($field->posts_ids as $_post_id) {
					$class_post_type .= " bcf_post_id_".$_post_id;	
				}
				//$class_post_type .= " bcf_post_id_";
			}
			
			//$class_post_type = "";
			
			if (get_post_type($post->ID) == 'page') {
			
				if (!is_array($field->page_template_ids)) {
					$field->page_template_ids = get_page_templates();
					
					$class_post_type .= " bcf_page_template_default";
					
				} 
				foreach ($field->page_template_ids as $_pt) {
					//$class_post_type .= " bcf_page_template_".str_replace(".", "-", $_pt);
					$class_post_type .= " bcf_page_template_".str_replace(array(".", "/"), "-", $_pt);	
				}
				
			}
			
			//form-field
?>		
		<tr class='bcf_row bcf_<?php echo $field->type; ?> <?php echo $class_post_type; ?> <?php echo $class_depth; ?> <?php echo $name; ?><?php echo $class; ?>'<?php echo ( !$active ? ' style="display: none;"' : ''  ); ?>>
		
			<?php 
			
			$widget = $this->get_widget_object( $field );
			
			if ($widget->single_columns == TRUE) : ?>
			
			<td colspan="2"><label for='bcf_<?php echo $field->cf_name;?>'><?php echo $label;?></label><br />
			<?php else: ?>
		
			<th scope='row'><label for='bcf_<?php echo $field->cf_name;?>'><?php echo $label;?></label></th>
			<td>
			
			<?php endif; ?>
			
				<?php 
				
				 //$this->fields_types->get_field( $field, $id, $class, $active, $pm ); //echo $res; 
				 
				$widget->index = time()+$i;				
			
				//Controllo pe ril multilingua
				if ($field->multilanguage == 1 && $this->check_multilanguage()) {
					
					//Mostro il form per le varie lingue
					$pm_lang = qtrans_split($pm);
					
					//Aggiungo la classe del nome base del campo
					$class .= $name . " ";
					
					if (is_array($pm_lang)) {
						
						foreach ($pm_lang as $l => $pm) {
							
							echo '<em>'.qtrans_getLanguageName($l).'</em><br />';
							
							$ml_name = $name."_".$l;
							if ($field->multiple == 1) $ml_name .= "[]";
							
							$widget->show_field($field, 'bcf_'.$field->cf_name."_".$l, $ml_name, $class, $active, $pm);	
						}
					
					}
					
				} else {

				 	$widget->show_field($field, 'bcf_'.$field->cf_name, $name . ($field->multiple == 1 ? "[]" : ""), $class, $active, $pm);
				 }
				 
				 ?>
				 
<?php		if ($field->multiple == 1) : ?>			
				<p><a class="one-more" href="#<?php echo $field->cf_name; ?>">Add another</a> <a class="one-less" href="#"<?php echo ($i == 0 && $blank_field == FALSE? ' style="display:none;"' : ''); ?>>remove</a></p>
<?php 		endif; ?>			
			
			</td>
			
		</tr>
<?php
	
			$i++;
	
		}
	
	}
	
	function save_custom_fields($post_id) {
	
		global $post;

		//Prevent BULK
		
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'duplicate_post_save_as_new_post')
			return $post_id;
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'duplicate_post_save_as_new_post_draft')
			return $post_id;
			
		if (isset($_REQUEST['bulk_edit']))
			return $post_id;
        
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || defined('DOING_AJAX') && DOING_AJAX)
		        return $post_id;

		if (wp_is_post_revision($post_id)) return;

		//$this->check_multilanguage();
		
//		echo $post_id . "<br />";
		
		//ELimino l'hook al save post per evitare loop
		//remove_action('save_post', array(&$this, 'save_custom_fields'));
		
		//Controllo che sia stato inviato il custom field		
		$post = get_post($post_id);
		
		//Salvo il form se c'è
		$bcf_fields = unserialize( get_option('bcf_fields') );

		if (!is_array($bcf_fields)) return $post_id;

		//$real_id =  wp_is_post_revision($post_id);
		
		//if ($post_id == $real_id) {
			//Ritrovo i campi da salvare
			foreach ( $bcf_fields as $box_id => $fields ) {
			
				if (is_array($fields)) {
			
					foreach ( $fields as $cf_name => $field ) {
	
						//Se fa parte del post lo salvo
						//$to_save = false;
						
						/*				
						if (get_post_type( $post_id ) == 'post') {
							
							//echo "SALVO ";
							//var_dump($field->categories);
						
							if ( in_category( $field->categories, $post_id )) {
		
								$to_save = true;	
							}
						} else if (get_post_type( $post_id ) == 'page') {
							if ( in_array( $post_id, (is_array($field->pages) ? $field->pages : array() ) ) ) {
								$to_save = true;
							}
						} else {
							if ( in_array( get_post_type( $post_id ), (is_array($field->custom_post_types) ? $field->custom_post_types : array() ) ) ) {
								$to_save = true;
							}
						}
						*/
						
						
						
						//var_dump($cf_value, $to_save);		
						//$bcf_fields
						
						//if ($to_save) {
						
							//$value = $_REQUEST["bfc_".$cf_value];
							
							//Inizializzo il campo
							$class_name = "bcf_".$field->type;
							
							if ( class_exists( $class_name ,false) ) {
								
								//$f = new $class_name($this->multilanguage);
								$f = new $class_name();
								//$value = $f->save_field($field, $cf_name, $this->multilanguage);
								
								//Nome del campo inviato
								$name = "bcf_" . $cf_name;
								$save_value = "";						
								
								if (isset($_REQUEST[$name])) {
									$value = $_REQUEST[$name];
								} else {
									$value = "";
								}
							
							
							
							
								if ($field->multiple == 1) {
										
										/*
										if ($field->multilanguage == 1) {
											
											//becco le lingue disponibili
											$pm_lang = $this->lang;
											
											if (is_array($pm_lang)) {
											
												//Becco i campi con la lingua indicata
												foreach ($pm_lang as $l) {
												
													$qt_name = $name."_".$l;											
													$value = $_REQUEST[$qt_name];
													
													//Otengo l'array con il valore
													if (!is_array($value)) {
														$value = array("");
													}
													foreach ($value as $k => $_value) {
														$save_value[$k] .= "<!--:$l-->" . $f->save_field($field, $_value, $qt_name) . "<!--:-->";
													}
													
												}
												
											}
											
										} else {
										*/
											$save_value = array();
											if (!is_array($value)) {
												$value = array();
											}
											foreach ($value as $_value) {
										
												$save_value[] = $f->save_field($field, $_value, $name);	
											
											}
											
										//}
										
								} else {
								
									

								
									/*
									if ($field->multilanguage == 1) {
										
										//becco le lingue disponibili
										$pm_lang = $this->lang;
										
										if (is_array($pm_lang)) {
										
											//Becco i campi con la lingua indicata
											foreach ($pm_lang as $l) {
											
												$qt_name = $name."_".$l;											
												$value = $_REQUEST[$qt_name];
												$save_value .= "<!--:$l-->" . $f->save_field($field, $value, $qt_name) . "<!--:-->";
												
											}
											
										}
										
									} else {
									*/
										$save_value = $f->save_field($field, $value, $name);	
									
									//}
								}
							
								//Se il campo è multimplo salvo i vari valori
								
								//Eseguo la funzione che processa il valore e mi restituisce il valore da salvare
								//$value = $f->save_field($field, $cf_name, $this->multilanguage);
		
								do_action('bcf_save_meta_'.$cf_name, $post_id, $save_value, $cf_name);
								do_action('bcf_save_meta', $post_id, $save_value, $cf_name);						
	
								if (is_array($save_value)) {
								
									//Cancello tutti le chiavi e le reinserisco
									delete_post_meta($post_id, $cf_name);
									
									foreach ($save_value as $v) {
											
										add_post_meta($post_id, $cf_name, $v);
									}
								
								} else {
								
									//var_dump($save_value);
									//var_dump($cf_name);
								
									update_post_meta($post_id, $cf_name, $save_value);
								
								}
								
							}
							
							/*
							
							
							//echo "SALVO ".$this->multilanguage;					
							if ($this->multilanguage && $field->multilanguage == 1) {
								//echo "SALVO ML";
								//Riunisco il valore delle lingue
								$lang = qtrans_getSortedLanguages();
								
								$text = array();
								foreach ($lang as $l) {
									$text[$l] = $_REQUEST["bfc_".$cf_value."_".$l];
								}
								$value = qtrans_join($text);
							}
							*/
							
							//update_post_meta($post_id, $cf_value, $value);
						//}
						
					}
				
				}
				
			}

			do_action('bcf_save_metas', $post_id);
		
		//}
	
	}
	
	function get_field_by_cf_name($cf_name) {
			
		$bcf_fields = unserialize( get_option('bcf_fields') );
		foreach ($bcf_fields as $box_id => $fields) {
			if (array_key_exists($cf_name, $fields)) {			
				return $fields[$cf_name];
			}
		}
	
	}
	
	function ajax_bcf_get_new_field() {
		
		global $post;
		//Get news field by cf_name
		
		$cf_name = substr($_REQUEST["cf_name"], 1);
		$post_id = $_REQUEST["post_id"];
		
		$post = get_post($post_id);
		
		$field = $this->get_field_by_cf_name($cf_name);
		
		
		$this->add_field_to_box($cf_name, $field, TRUE);
		
		//echo $cf_name;
		
		
	}
	
	/*
	############################################################################################################################################
	
		ARRAY
		
	############################################################################################################################################	
	*/
	
	
	//Ordina un array multidimensionale data una o pi chiavi chiave
	
	// Based on the other notes given before.
	// Sorts an array (you know the kind) by key
	// and by the comparison operator you prefer.
	
	// Note that instead of most important criteron first, it's
	// least important criterion first.
	
	// The default sort order is ascending, and the default sort
	// type is strnatcmp.
	
	// function multisort($array[, $key, $order, $type]...)
	
	function multisort($array)
	{
	   for($i = 1; $i < func_num_args(); $i += 3)
	   {
	       $key = func_get_arg($i);
	       if (is_string($key)) $key = '"'.$key.'"';
	    
	       $order = true;
	       if($i + 1 < func_num_args())
	           $order = func_get_arg($i + 1);
	    
	       $type = 0;
	       if($i + 2 < func_num_args())
	           $type = func_get_arg($i + 2);
	
	       switch($type)
	       {
	           case 1: // Case insensitive natural.
	               $t = 'strcasecmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	           case 2: // Numeric.
	               $t = '($a[' . $key . '] == $b[' . $key . ']) ? 0:(($a[' . $key . '] < $b[' . $key . ']) ? -1 : 1)';
	               break;
	           case 3: // Case sensitive string.
	               $t = 'strcmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	           case 4: // Case insensitive string.
	               $t = 'strcasecmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	           default: // Case sensitive natural.
	               $t = 'strnatcmp($a[' . $key . '], $b[' . $key . '])';
	               break;
	       }
	//           echo $t;
	       uasort($array, create_function('$a, $b', '; return ' . ($order ? '' : '-') . '(' . $t . ');'));
	   }
	   return $array;
	}
		
	function multisort_obj($array)
	{
	   for($i = 1; $i < func_num_args(); $i += 3)
	   {
	       $key = func_get_arg($i);
	       //if (is_string($key)) $key = '"'.$key.'"';
	    
	       $order = true;
	       if($i + 1 < func_num_args())
	           $order = func_get_arg($i + 1);
	    
	       $type = 0;
	       if($i + 2 < func_num_args())
	           $type = func_get_arg($i + 2);
	
	       switch($type)
	       {
	           case 1: // Case insensitive natural.
	               $t = 'strcasecmp($a->' . $key . ', $b->' . $key . ')';
	               break;
	           case 2: // Numeric.
	               $t = '($a->' . $key . ' == $b->' . $key . ') ? 0:(($a->' . $key . ' < $b->' . $key . ') ? -1 : 1)';
	               break;
	           case 3: // Case sensitive string.
	               $t = 'strcmp($a->' . $key . ', $b->' . $key . ')';
	               break;
	           case 4: // Case insensitive string.
	               $t = 'strcasecmp($a->' . $key . ', $b->' . $key . ')';
	               break;
	           default: // Case sensitive natural.
	               $t = 'strnatcmp($a->' . $key . ', $b->' . $key . ')';
	               break;
	       }
	//           echo $t;
	       uasort($array, create_function('$a, $b', '; return ' . ($order ? '' : '-') . '(' . $t . ');'));
	   }
	   return $array;
	}
	
}

function init_bcf() {

	$bcf = new Beautiful_custom_fields();
	
	//Init Admin Option Page
	$bcf_admin = new Beautiful_custom_fields_admin($bcf);
	
	
	//add_action('admin_init', array(&$bcf, 'attach_meta_box'), 10); //backwards compatible
	
	
	
	if ( isset($_REQUEST["action"]) && $_REQUEST["action"] != 'inline-save' && $_REQUEST["action"] != 'autosave' && is_admin()) {
		add_action('save_post', array(&$bcf, 'save_custom_fields'));
	}
	
//	echo "P-P";
	
	//if ( ($_REQUEST["action"] == 'edit' && (strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'post.php')) !== FALSE) ) {
	if ( ((strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'post.php')) !== FALSE) ) {
		//add_action('admin_head', array(&$bcf, 'set_header'));
		
		add_action('add_meta_boxes', array(&$bcf, 'attach_meta_box'), 10);
		add_action( "admin_print_scripts", array(&$bcf, 'add_javascript'), 10);

		add_action('admin_footer', array(&$bcf, 'set_header'));
	}
	
	add_action('admin_init',array(&$bcf,'init_options'));
	
	//AJAX
	add_action('wp_ajax_bcf_get_new_field', array(&$bcf, 'ajax_bcf_get_new_field'), 120);
	//add_action('wp_ajax_nopriv_social_feed', 'ajax_social_feed', 120);

}

add_action('plugins_loaded', 'init_bcf');



/*
add_shortcode('supple', array(&$suppleForms, 'shortCode'));

//Load the Javascript file to the Admin pages only

//Call the Function that will Add the Options Page

//Call the INIT function whenever the Plugin is activated
add_action('activate_supple-forms/supple-forms.php',
array(&$suppleForms, 'init'));

add_action('admin_notices', array(&$suppleForms, 'checkTablesInstalled'));
*/

/* Use the admin_menu action to define the custom attach_meta_boxes */
//add_action('admin_menu', array(&$suppleForms, 'attachMetaBox'));


/* Use the save_post action to do something with the data entered */
//add_action('save_post', array(&$suppleForms, 'saveMetaData'));

/* Add jQuery DatePicker Style sheet */
//add_action('admin_head', array(&$suppleForms, 'addDatePickerCSS'));


?>