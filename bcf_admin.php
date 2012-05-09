<?php
/**
 * @package bcf
 * @author G100g
 * @version 1.0.0
 */
class Beautiful_custom_fields_admin {

	var $bcf = null;
	
//	function Beautiful_custom_fields() {
	function __construct(&$_bcf) {
	
		$this->bcf = $_bcf;
		
		add_action( "admin_print_styles", array(&$this, 'add_style'), 10);
		add_action( "admin_print_scripts", array(&$this, 'add_javascript'), 10);
		
		add_action('admin_menu', array(&$this, 'admin_menu'), 10);
	
		
		add_action('wp_ajax_bcf_get_options', array(&$this, 'ajax_bcf_get_options'), 120);
		
		add_action('wp_ajax_bcf_update_sorting_box', array(&$this, 'ajax_bcf_update_sorting_box'), 120);
	
		//$this->fields_types = new Beautiful_custom_fields_types();
	}
	
	function add_style() {
	
		wp_enqueue_style('bcf_admin_style', plugins_url( 'css/admin.css', __FILE__ ), NULL, '1.0');
	
	}
	
	function add_javascript() {
	
		wp_enqueue_script('bcf_admin', plugins_url( 'js/admin.js', __FILE__ ), array('jquery-ui-sortable'), '1.0');
		
	
		//wp_enqueue_script('jquery');
		//wp_enqueue_script('jquery-ui-core');
		//enqueue jQuery DatePicker
		//wp_register_script('jquery_datepicker', get_bloginfo('wpurl') . '/wp-content/plugins/supple-forms/js/ui.datepicker.js', array('jquery'), '1.0');
		//wp_enqueue_script('jquery_datepicker');
				
		//enqueue SuppleForms Javascript
		//wp_register_script('supple_js', get_bloginfo('wpurl') . '/wp-content/plugins/supple-forms/js/supple-admin.js', array('jquery'), '1.0');
		//wp_enqueue_script('supple_js');
		
	
	}
	
	function admin_menu() {
	
	
		//Aggiungo la pagina ai settings
		add_options_page('Beautiful Custom Fields', 'Beautiful Custom Fields', 8, basename(__FILE__), array(&$this, 'admin_page') );
		//add_menu_page('Supple Forms', 'Supple Forms', 9, basename(__FILE__), array(&$suppleForms, 'loadAdminPage'));
	
	}	
		
	function cf_exists($cf_value, $bcf_fields = null) {
	
		if ($bcf_fields != null) $bcf_fields = unserialize( get_option('bcf_fields') );
	
		if (is_array($bcf_fields)) {
	
			foreach ($bcf_fields as $boxs) {
			
				if (is_array($boxs)) {
			
					foreach ($boxs as $field_key => $field) {
					
						if ($field_key == $cf_value) {
						
							return TRUE;
						
						}
						
					}
				
				}
			}
		
		}
		
		return FALSE;	
	
	}
	
	/*
	function _array_remove_key ()
	{
	  $args  = func_get_args();
	  return array_diff_key($args[0],array_flip(array_slice($args,1)));
	}
	*/
	
	function create_field($name, $cf_name, $type_options, $taxonomy, $posts_ids, $custom_post_types, $type, $sort, $multiple, $multilanguage, $depth, $page_template_ids) {
	
		//Creo il nuovo field e restituisco l'oggetto
		
		$res = false;
		
		$res = (object) array("name" => $name, "taxonomy" => $taxonomy, "posts_ids" => $posts_ids, "custom_post_types" => $custom_post_types, "sort" => $sort, "type" => $type, "type_options" => $type_options, "multilanguage" => $multilanguage, "multiple" => $multiple, "depth" => $depth, 'page_template_ids' => $page_template_ids );
		
		return $res;
	
	}
	
	function clean_custom_field_value($cf) {
	
		return ereg_replace("[^A-Za-z0-9_\-]", "", $cf );
	
	}
	
	function get_option_fields() {
		
		$bcf_fields = get_option('bcf_fields');
		return is_string($bcf_fields) ? unserialize( $bcf_fields ) : $bcf_fields;
	
	}
	
	function get_option_boxs() {
		
		$bcf_boxs = get_option('bcf_boxs');
		return is_string($bcf_boxs) ? unserialize( $bcf_boxs ) : $bcf_boxs;
	
	}

	function admin_page() {
	
		global $post;

		$this->bcf->check_multilanguage();
		
		
		$msg = array();

	// If form was submitted
	if (isset($_REQUEST['action'])) {			
		  	
		  	if (!isset($_REQUEST['create']) ) {
		  	
			  	switch($_REQUEST['action']) {
			  	
			  		case 'add_box':
			  		
			  			//Creo il box indicato
			  			
			  			if (trim($_REQUEST['box_title']) != '') {
			  			
			  				//Appendo i box all'array dei box
			  				$tmp_bcf_fields = unserialize( get_option('bcf_fields') );
			  				$tmp_bcf_boxs = unserialize( get_option('bcf_boxs') );			  						  			
			  				
			  				if (is_array($tmp_bcf_fields)) {
			  					
			  					$box_key = count($tmp_bcf_fields);
			  				
			  					//$tmp_bcf_fields = array_push(array(), $tmp_bcf_fields);
			  					$tmp_bcf_fields[$box_key] = array();
			  					
			  				} else {
			  					$box_key = 0;
			  					$tmp_bcf_fields = array(0 => array());
			  				}
			  				
			  				$box_data = array(
			  					'title' => trim($_REQUEST['box_title']),
			  					'context' => trim($_REQUEST['box_context']),
			  					'priority' => trim($_REQUEST['box_priority'])
			  				);
			  				
			  				if (is_array($tmp_bcf_boxs)) {
			  					$tmp_bcf_boxs[$box_key] = $box_data;
			  				} else {
			  					$tmp_bcf_boxs = array($box_key => $box_data);
			  				}
			  				
			  				update_option( 'bcf_fields', serialize($tmp_bcf_fields) );
			  				update_option( 'bcf_boxs', serialize($tmp_bcf_boxs) );
			  				
			  				$msg[] = array(0, 'Box data saved.');
			  				
			  			} else {
			  			
			  				$box_title = trim($_REQUEST['box_title']);
			  				$box_context = trim($_REQUEST['box_context']);
			  				$box_priority = trim($_REQUEST['box_priority']);
			  			
			  				$msg[] = array(0, 'Box data missing.');
			  			}
			  		
			  		break;
			  		
			  		case 'save_box':
			  		
			  			//Salvo il box editato
			  			if ($_REQUEST['box_index'] !== NULL && trim($_REQUEST['box_title']) != '') {
			  			
			  				//Ritrovo la lista dei box
			  				$tmp_bcf_boxs = unserialize( get_option('bcf_boxs') );			  						  			
			  					
			  				$box_index = $_REQUEST["box_index"];	
			  							  				
			  				//Aggirono i dati
			  				if (array_key_exists($box_index, $tmp_bcf_boxs )) {
	
			  					$box_data = array(
			  						'title' => trim($_REQUEST['box_title']),
			  						'context' => trim($_REQUEST['box_context']),
			  						'priority' => trim($_REQUEST['box_priority'])
			  					);
			  					
			  					$tmp_bcf_boxs[$box_index] = $box_data;
			  				
			  					//Salvo i dati
			  					update_option( 'bcf_boxs', serialize($tmp_bcf_boxs) );			  					
			  				
			  					$msg[] = array(0, 'Box saved.');
			  				
			  				} else {
			  					$msg[] = array(0, 'Error saving box.');
			  				}
			  				
			  			} else {
			  				$msg[] = array(0, 'Box data missing.');
			  			}
			  		
			  		break;
			  		
			  		case 'delete_box':
			  		
			  			//Salvo il box editato
			  			if ($_REQUEST['box_index'] !== NULL) {
			  			
			  				$tmp_bcf_boxs = unserialize( get_option('bcf_boxs') );			  						  			
			  						  					
			  				$box_index = $_REQUEST["box_index"];	
			  							  				
			  				//Aggirono i dati
			  				if (array_key_exists($box_index, $tmp_bcf_boxs )) {
	
								$tmp_bcf_fields = unserialize( get_option('bcf_fields') );
	
								//Controllo se il box ha dei campi assegnati
								if (array_key_exists($box_index, $tmp_bcf_fields) && !empty($tmp_bcf_fields[$box_index])) {
									
									$msg[] = array(0, 'Box contain fields. Remove or move fields first.');
								
								} else {
									//no ha campi associati e lo posso eliminare
									unset($tmp_bcf_boxs[$box_index]);
									unset($tmp_bcf_fields[$box_index]);
									
									update_option( 'bcf_boxs', serialize($tmp_bcf_boxs) );	
									update_option( 'bcf_fields', serialize($tmp_bcf_fields) );	
									
									$msg[] = array(0, 'Box deleted.');
										
								}
	
			  				
			  				} else {
			  					$msg[] = array(0, 'Box missing.');
			  				}
			  				
			  			} else {
			  				$msg[] = array(0, 'Error deleting box.');
			  			}
			  		
			  		break;
			  		
			  		case 'edit_box':
			  		
			  			$box_index = $_REQUEST["box_index"];
			  		
			  			//Ritrovo i dati del box
			  			$tmp_bcf_boxs = unserialize( get_option('bcf_boxs') );
			  			
			  			if (is_array($tmp_bcf_boxs)) {
			  			
			  				$box = array_key_exists($box_index, $tmp_bcf_boxs) ? $tmp_bcf_boxs[$box_index] : null;
			  				
			  				if ($box != null) {

			  					//Prepearo i dati per il form
			  					$box_index = $_REQUEST["box_index"];
			  					$box_title = $box['title'];
			  					$box_context = $box['context'];
			  					$box_priority = $box['priority'];
			  				
			  				} else {
			  					$msg[] = array(0, 'Error retrieving box data.');
			  				}
			  			
			  			} else {
			  			
			  				$msg[] = array(0, 'Error retrieving box data.');
			  			
			  			}
			  		
			  		break;
			  	
			  		case 'add':
			  		
			  			$form_errors = FALSE;
			  		
			  			if (trim($_REQUEST['nf_name']) != '' && trim($_REQUEST['nf_cf_name']) != '' && ( !empty($_REQUEST['nf_taxonomy']) || !empty($_REQUEST['nf_posts_ids']) || !empty($_REQUEST['nf_custom_post_types']) ) ) {
				  			
				  			if ($_REQUEST['nf_sort'] == '') {
				  				$_REQUEST['nf_sort'] = 0;
				  			}
				  			
				  			//Aggiungo il campo all'array
				  			$new_field_obj = $this->create_field( $_REQUEST['nf_name'], $_REQUEST['nf_cf_name'], $_REQUEST['nf_type_options'], $_REQUEST['nf_taxonomy'], $_REQUEST['nf_posts_ids'], $_REQUEST['nf_custom_post_types'], $_REQUEST['nf_type'], $_REQUEST['nf_sort'], $_REQUEST['nf_multiple'], $_REQUEST['nf_multilanguage'], $_REQUEST['nf_depth'], $_REQUEST['nf_page_template_ids'] );
				  			
				  			if ( $new_field_obj ) {
				  				
				  				//Ritrovo i valori
				  				$tmp_bcf_fields = unserialize( get_option('bcf_fields') );
				  				
				  				$cf_key_value = $this->clean_custom_field_value($_REQUEST['nf_cf_name']);
				  				
				  				//Controllo se esiste il campo
				  				
				  				//$tmp_bcf_fields[0][ $this->clean_custom_field_value($_REQUEST['nf_cf_name']) ] = $new_field_obj;
				  				
				  				if ($this->cf_exists($cf_key_value, $tmp_bcf_fields) && !array_key_exists('old_nf_cf_name', $_REQUEST)) {
				  				
//				  				if (array_key_exists($cf_key_value, [$_REQUEST['nf_box']])) {
				  					$form_errors = TRUE;
				  					$msg[] = array(1, 'Custom Field Value exists.');
				  				
				  				} else {
				  				
					  				$tmp_bcf_fields[$_REQUEST['nf_box']][ $cf_key_value ] = $new_field_obj;
					  				
					  				//Se è cambiato il box elimino il campo dal vecchio box
					  				
					  				if (isset($_REQUEST['old_nf_box']) && $_REQUEST['old_nf_box'] != $_REQUEST['nf_box']) {				  				
					  					unset($tmp_bcf_fields[$_REQUEST['old_nf_box']]);
					  				}				  				
					  				
					  				update_option( 'bcf_fields', serialize($tmp_bcf_fields) );
					  				
					  				$msg[] = array(0, 'Field data saved.');
				  				
				  				}
				  			}
			  			
			  			} else {
			  			
			  				$form_errors = TRUE;
			  				$msg[] = array(1, 'You have to fill name field, custom field value field and chose a category, a page or a custom post type at least.');
			  			
			  			}
			  			
			  			if ($form_errors) {
			  				
			  				$nf_name = $_REQUEST['nf_name'];
			  				$nf_cf_name = trim($_REQUEST['nf_cf_name']);
			  				$nf_type_options = trim($_REQUEST['nf_type_options']);
			  				$nf_posts_ids = $_REQUEST['nf_posts_ids'];
			  				$nf_custom_post_types = $_REQUEST['nf_custom_post_types'];
			  				$nf_taxonomy = $_REQUEST['nf_taxonomy'];
			  				$nf_sort = $_REQUEST['nf_sort'];
			  				$nf_multilanguage = $_REQUEST['nf_multilanguage'];
			  				$nf_type = $_REQUEST['nf_type'];
			  				$nf_multiple = $_REQUEST['nf_multiple'];
			  				$nf_depth = $_REQUEST['nf_depth'];
			  				$nf_page_template_ids = $_REQUEST['nf_page_template_ids'];
			  				$nf_box = $_REQUEST['nf_box'];
			  				
			  			
			  				
			  			
			  			}
			  		
			  		break;
			  		
			  		case 'delete':
	
			  			if ($_REQUEST['cf_name'] != '') {
			  		
				  			// Rimuovo
				  			$tmp_bcf_fields = unserialize( get_option('bcf_fields') );			  			
				  			
				  			//Rimuovo la chiave dal'aaray
				  			//$tmp_bcf_fields[$_REQUEST['nf_box']] = $this->_array_remove_key($tmp_bcf_fields[0], $_REQUEST['cf_name']);
				  			
				  			unset($tmp_bcf_fields[$_REQUEST['nf_box']][$_REQUEST['cf_name']]);
				  			
				  			update_option( 'bcf_fields', serialize( $tmp_bcf_fields ) );
				  			
				  			$msg[] = array(0, 'Field deleted.');
			  			
			  			}
			  		
			  		break;
			  		
			  		case 'edit':
			  		
			  			if ($_REQUEST['cf_name'] != '') {
			  		
				  			$tmp_bcf_fields = unserialize( get_option('bcf_fields') );	
				  		
				  			//$field = $tmp_bcf_fields[0][ $_REQUEST['cf_name'] ];
				  			$field = $tmp_bcf_fields[$_REQUEST['nf_box']][ $_REQUEST['cf_name'] ];
				  			
				  			if ($field) {
					  			//ritrovo i dati per l'edit
					  			$nf_name = $field->name;
					  			$nf_cf_name = $_REQUEST['cf_name'];
					  			
					  			$nf_type_options = $field->type_options;
					  			
					  			//$nf_pages = $field->pages;
					  			//$nf_pages = $field->pages;
					  			$nf_posts_ids = $field->posts_ids;
					  			$nf_custom_post_types = $field->custom_post_types;					  			
					  			$nf_taxonomy = $field->taxonomy;
					  			
					  			$nf_sort = $field->sort;
					  			$nf_multilanguage = $field->multilanguage;
					  			$nf_type = $field->type;
				  				$nf_multiple = $field->multiple;
				  				
				  				$nf_depth = $field->depth;
				  				
				  				$nf_page_template_ids = $field->page_template_ids;
				  				
				  				$nf_box = $_REQUEST['nf_box'];
				  			}
			  						  			
			  			}
			  		
			  		break;
			  	
			  	
			  	}
		  	
		  	}
		  	
		  	/*
		  	check_admin_referer('rfi');
		  			  
			$bcf_fields = !isset($_POST['bcf_fields'])? '': stripslashes($_POST['bcf_fields']);
			update_option('bcf_fields', $rfi_roles);
			$msg_status = 'Settings saved.';
							
		    // Show message
		   _e('<div id="message" class="updated fade"><p>' . $msg_status . '</p></div>');
		   */
		
	}

	$nonce = wp_create_nonce('bcf');
	$actionurl = $_SERVER['REQUEST_URI'];
	$plainurl = 'admin.php?page=bcf_admin.php';
	
	$bcf_fields = get_option('bcf_fields');
	$bcf_fields = is_string($bcf_fields) ? unserialize( $bcf_fields ) : $bcf_fields;
	
	$bcf_boxs = get_option('bcf_boxs');
	
	//Se non esiste un box ne creo uno
	if (!is_string($bcf_boxs)) {
	
		$tmp_bcf_boxs[0] = array(
  						'title' => "Options",
  						'context' => 'normal',
  						'priority' => 'high'
  					);
	
		//Salvo i dati
		$bcf_boxs = serialize($tmp_bcf_boxs);
		
		update_option( 'bcf_boxs', $bcf_boxs );
	
	}
	
	$bcf_boxs = is_string($bcf_boxs) ? unserialize( $bcf_boxs ) : $bcf_boxs;
	
	
	
		
	//Ritrovo le categorie e creo un checkbox
	//$post_categories = get_categories(array('hide_empty'=> false));
	
	$taxonomy = get_taxonomies(
					array('public'   => true, 'show_ui' => true),
					'objects'
				);
	
	//Ritrovo le pagine
	$pages = get_posts("post_type=page&numberposts=-1");
	
	$page_templates = get_page_templates();
//	$page_templates = array_merge(array('Default' => "default"), $page_templates);	
	
	//Ritrovo i custom post type
	$custom_post_types = get_post_types(array(
			'public'   => true,
			'_builtin' => false
	
	));
	
	$custom_post_types[] = 'post';
	$custom_post_types[] = 'page';

	//Tipi di input
	//$this->fields_types = apply_filters( 'bcf_types', $this->fields_types );
//	$this->fields_types->types
	
	//$ftype = $this->fields_types;
	
/*


	$bcf_fields = array( "panel" => array( "name" => array("options" => (object) array("") )  ) );


*/
?>

<div class="wrap">
	
	<div class="icon32" id="icon-options-general"><br/></div>
	<h2>Beautiful Custom Fields</h2>
	
<?php if (!empty($msg) ) : foreach ($msg as $m) :?>

	<?php _e('<div id="message" class="'.($m[0] == 1 ? 'error' : 'updated' ).' fade"><p>' . $m[1] . '</p></div>'); ?>

<?php endforeach; endif; ?>	
	
	
	<div id="forms">
	
		<?php if (is_array($bcf_boxs) && !empty($bcf_boxs)) : ?>
	
		<div id="form-field" class="metabox-holder disabled-metabox-holder-disabled">
		
			<div class="postbox">
		
				<h3><span>New Field</span></h3>
				<div class="inside">	
				
					<form action="<?php echo $action_url; ?>" method="post">
						<input type="hidden" name="action" value="add" />
						<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
		
<!--		<h3>New Fields</h3> -->

						<table class="form-table">
							<tbody>
									
							<tr valign="top">
								<th scope="row"><label for="nf_cf_name">Custom Field Value</label></th>
								<td><input type="text" class="regular-text" value="<?php echo $nf_cf_name; ?>" id="nf_cf_name" name="nf_cf_name"/></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="nf_name">Name</label></th>
								<td><input type="text" class="regular-text" value="<?php echo $nf_name; ?>" id="nf_name" name="nf_name"/></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="nf_category">With Taxonomies</label></th>
								<td>
<?php 
			foreach ($taxonomy as $tax) :
			
//			var_dump($tax);
			
				if ($tax->hierarchical) :
			
?>
				<strong><?php echo $tax->label; ?></strong><br />

<?php
				$terms  = get_terms($tax->name, array('hide_empty' => 0));

				foreach ($terms as $term) :
				
				$selected = '';
				
				if (is_array( $nf_taxonomy )) {
					$selected = ( in_array( $tax->name."-".$term->term_id, $nf_taxonomy ) ? ' checked="checked" ' : '' ); 
				}
			
?>						
			<label for="nf_taxonomy-<?php echo $term->slug; ?>"><input type="checkbox" value="<?php echo $tax->name."-".$term->term_id; ?>" id="nf_taxonomy-<?php echo $term->slug; ?>" name="nf_taxonomy[<?php echo $tax->name; ?>]" <?php echo $selected; ?> /> <?php echo $term->name; ?></label>

<?php 			endforeach;  ?>

				<br /><br />

<?php			endif;

			endforeach; ?>			
			</td>
		</tr>
		
		<tr valign="top">
					<th scope="row"><label for="nf_category">For Pages</label></th>
					<td>
		<?php 
		
					foreach ($pages as $post) : setup_postdata($post);
						$selected = '';
						if (is_array( $nf_posts_ids )) {	
							$selected = ( in_array( $post->ID, $nf_posts_ids ) ? ' checked="checked" ' : '' ); 
						}
					
		?>						
					<label for="nf_posts_ids-<?php echo $post->post_name; ?>"><input type="checkbox" value="<?php echo $post->ID; ?>" id="nf_posts_ids-<?php echo $post->post_name; ?>" name="nf_posts_ids[]" <?php echo $selected; ?>/> <?php the_title(); ?></label>
		
		<?php 		endforeach; ?>			
					</td>
				</tr>
				
		<tr valign="top">
					<th scope="row"><label for="nf_category">With Page Templates</label></th>
					<td>
		<?php 
		
					foreach ($page_templates as $key_page_template => $page_template) :
						$selected = '';
						if (is_array( $nf_page_template_ids )) {	
							$selected = ( in_array( $page_template, $nf_page_template_ids ) ? ' checked="checked" ' : '' ); 
						}
					
		?>						
					<label for="nf_page_template_ids-<?php echo $page_template; ?>"><input type="checkbox" value="<?php echo $page_template; ?>" id="nf_page_template_ids-<?php echo $page_template; ?>" name="nf_page_template_ids[]" <?php echo $selected; ?>/> <?php echo $key_page_template; ?></label>
		
		<?php 		endforeach; ?>			
					</td>
				</tr>		
				
				
		<tr valign="top">
			<th scope="row"><label for="nf_category">For Post Type</label></th>
			<td>
<?php 

			foreach ($custom_post_types as $cpt) : 
				//setup_postdata($post);
				$selected = '';
				
				if (is_array( $nf_custom_post_types )) {	
					$selected = ( in_array( $cpt, $nf_custom_post_types ) ? ' checked="checked" ' : '' ); 
				}
			
?>						
			<label for="nf_custom_post_types-<?php echo $cpt; ?>"><input type="checkbox" value="<?php echo $cpt; ?>" id="nf_custom_post_types-<?php echo $cpt; ?>" name="nf_custom_post_types[]" <?php echo $selected; ?>/> <?php echo $cpt; ?></label>

<?php 		endforeach; ?>			
			</td>
		</tr>	
		
		<tr valign="top">
					<th scope="row"><label for="nf_category">Depth Child</label></th>
					<td><input type="text" class="regular-text" value="<?php echo $nf_depth; ?>" id="nf_depth" name="nf_depth"/></td>
				</tr>			
		
			<tr valign="top">
				<th scope="row"><label for="nf_category">Type</label></th>
				<td>
			
			<select name="nf_type">
<?php 
			
			foreach ($this->bcf->fields_types as $type => $name) :
				$selected = '';
				if ($nf_type == $type ) $selected = ' selected="selected" ';
				
?>						
			<option value="<?php echo $type; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>

<?php 		endforeach; ?>	

				</select>		
				</td>
			</tr>
			
			<input type="hidden" name="nf_type_options" value="<?php echo $nf_type_options; ?>">
						
			<tr id="row_options" valign="top">
				<th scope="row"><label for="nf_sort">Options</label></th>
				<td></td>
			</tr>
			
<?php		$checked = '';
			if ($nf_multiple == 1) $checked = ' checked="checked" ';  ?>			
			<tr valign="top">
				<th scope="row"><label for="nf_multiple">Allow Multiple</label></th>
				<td><input type="checkbox" value="1" id="nf_multiple" name="nf_multiple" <?php echo $checked; ?>/></td>
			</tr>
			
<?php if ( $this->bcf->multilanguage ) : 

			$checked = '';
			if ($nf_multilanguage == 1) $checked = ' checked="checked" '; 

?>		
			<tr valign="top">
				<th scope="row"><label for="nf_multilanguage">Is Multilanguage</label></th>
				<td><input type="checkbox" value="1" id="nf_multilanguage" name="nf_multilanguage" <?php echo $checked; ?>/></td>
			</tr>
<?php else: ?>
			<input type="hidden" name="nf_multilanguage" value="0" />
<?php endif; ?>		
			<tr valign="top">
				<th scope="row"><label for="nf_sort">Sort</label></th>
				<td><input type="text" class="regular-text" value="<?php echo $nf_sort; ?>" id="nf_sort" name="nf_sort"/></td>
			</tr>
			
			<tr valign="top">
					<th scope="row"><label for="nf_category">Box</label></th>
					<td>
				
				<select name="nf_box">
	<?php 
				
				foreach ($bcf_boxs as $box_id => $box) :
					$selected = '';
					if ($nf_box == $box_id ) $selected = ' selected="selected" ';
					
	?>						
				<option value="<?php echo $box_id; ?>"<?php echo $selected; ?>><?php echo $box["title"]; ?></option>
	
	<?php 		endforeach; ?>	
	
					</select>		
					</td>
			</tr>
			
		</tbody>
		
		</table>
		<?php if ($_REQUEST['action'] == 'edit') : ?>
		<input type="hidden" name="old_nf_box" value="<?php echo $nf_box; ?>" />
		<input type="hidden" name="old_nf_cf_name" value="<?php echo $nf_cf_name; ?>" />
		
		<p class="submit"><input type="submit" value="Create New Field" class="button-highlighted button" name="create"/> <input type="submit" value="Save" class="button-primary" name="Submit"/></p>
		<?php else: ?>
		<p class="submit"><input type="submit" value="Create" class="button-primary" name="Submit"/></p>
		<?php endif; ?>
	</form>
	
		
				</div>
			</div>
		
		</div> <!-- metabox-holder -->
		
		<?php endif; ?>
		
		<div id="form-box" class="metabox-holder disabled-metabox-holder-disabled">
		
			<div class="postbox">
		
				<h3><span>New Box</span></h3>
				<div class="inside">	
					<?php if (is_array($bcf_boxs) && empty($bcf_boxs)) : ?>
					<p>To add fields, you have to create a box.</p>
					<?php endif; ?>
					
					<form action="<?php echo $action_url; ?>" method="post">
						<?php if ($_REQUEST['action'] == 'edit_box') : ?>
						<input type="hidden" name="action" value="save_box" />
						<input type="hidden" name="box_index" value="<?php echo $box_index; ?>" />
						
						<?php else: ?>
						<input type="hidden" name="action" value="add_box" />
						<?php endif; ?>
						<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
			
						<table class="form-table">
							<tbody>
									
								<tr valign="top">
									<th scope="row"><label for="box_title">Box Title</label></th>
									<td><input type="text" class="regular-text" value="<?php echo $box_title; ?>" id="box_title" name="box_title"/></td>
								</tr>
								
								<tr valign="top">
									<th scope="row"><label for="nf_name">Context</label></th>
									<td>
										<select name="box_context">
											<option value="normal"<?php echo ($box_context == "normal" ? " selected" : ""); ?>>Normal</option>
											<option value="advanced"<?php echo ($box_context == "advanced" ? " selected" : ""); ?>>Advanced</option>
											<option value="side"<?php echo ($box_context == "side" ? " selected" : ""); ?>>Side</option>
										</select>
									</td>								
								</tr>
								
								<tr valign="top">
									<th scope="row"><label for="nf_name">Priority</label></th>
									<td>
										<select name="box_priority">
											<option value="high"<?php echo ($box_priority == "high" ? " selected" : ""); ?>>High</option>
											<option value="core"<?php echo ($box_priority == "core" ? " selected" : ""); ?>>Core</option>
											<option value="default"<?php echo ($box_priority == "default" ? " selected" : ""); ?>>Default</option>
											<option value="low"<?php echo ($box_priority == "low" ? " selected" : ""); ?>>Low</option>
										</select>
									</td>								
								</tr>

							
							</tbody>
						
						</table>
						<?php if ($_REQUEST['action'] == 'edit_box') : ?>
						<p class="submit"><input type="submit" value="Create New Box" class="button-highlighted button" name="create_box"/> <input type="submit" value="Save" class="button-primary" name="Submit"/></p>
						<?php else: ?>
						<p class="submit"><input type="submit" value="Create" class="button-primary" name="Submit"/></p>
						<?php endif; ?>
					</form>
				
				</div>
	
			</div>
		</div>
		
		<div class="clear"></div>
	
	</div> <!-- forms -->
	
	

<?php

//	if (is_array($bcf_fields) && !empty($bcf_fields[0]) ) :

	if (is_array($bcf_boxs) ) :
	
		foreach ($bcf_boxs as $box_index => $box) :
		
			$panel = $bcf_fields[$box_index];
			
?>
	<div class="box-header">
		<h3><?php echo $box["title"]; ?></h3>
		<div class="box-actions submitbox">
			<a class="" href="<?php echo $plainurl; ?>&amp;box_index=<?php echo $box_index; ?>&amp;action=edit_box&amp;_wpnonce=<?php echo $nonce; ?>">Edit Box</a>
			<a class="submitdelete deletion menu-delete" href="<?php echo $plainurl; ?>&amp;box_index=<?php echo $box_index; ?>&amp;action=delete_box&amp;_wpnonce=<?php echo $nonce; ?>">Delete Box</a>
		</div>
	</div>

	<table id="box_<?php echo $box_index; ?>" class="widefat box-fields connectedSortable" cellspacing="0">
		<thead>
			<tr>
				<th scope="col">&nbsp;</th>
				<th scope="col">Name</th>
				<th scope="col">Custom Field Value</th>
				<th scope="col">Taxonomy</th>
				<th scope="col">Pages</th>
				<th scope="col">Post Types</th>
				<th scope="col">Type</th>
				<th scope="col">Multiple</th>
				<?php if ( $this->bcf->multilanguage ) : ?>
				<th scope="col">Multilanguage</th>
				<?php endif; ?>
				<th scope="col">Depth</th>			
				<th scope="col">Sorting</th>			
			</tr>	
		</thead>

<?php	
//		foreach ($bcf_fields as $panel) :
			
			//Ordino i campi tramite l'opzione sort
			
			if (!is_array($panel)) $panel = array();
			
			$panel = $this->bcf->multisort_obj($panel, 'sort', true, 2);
			
			foreach ($panel as $cf_name => $field) :
			
			//$field = $panel
			

?>	

		<tr id="field_<?php echo $cf_name; ?>" class="field">
			
			<td class="dd-holder">=<br>=</strong>
			
			<td><strong><?php echo $field->name; ?></strong>
			
			<div class="row-actions">
				<span class="edit"><a href="<?php echo $plainurl; ?>&amp;cf_name=<?php echo $cf_name; ?>&amp;nf_box=<?php echo $box_index; ?>&amp;action=edit">Edit</a> | </span>
				<span class="edit"><a href="<?php echo $plainurl; ?>&amp;cf_name=<?php echo $cf_name; ?>&amp;nf_box=<?php echo $box_index; ?>&amp;action=duplicate">Duplicate</a> | </span>
				<span class="trash submitdelete"><a href="<?php echo $plainurl; ?>&amp;cf_name=<?php echo $cf_name; ?>&amp;nf_box=<?php echo $box_index; ?>&amp;action=delete&amp;_wpnonce=<?php echo $nonce; ?>">Delete</a></span>
			</div>
			</td>
			<td><?php echo $cf_name ; ?></td>
			<td><?php
				if (is_array($field->taxonomy)) {
					$sep = "";
					
					foreach ($field->taxonomy as $term_id) {
					
						$tax = substr($term_id, 0, strrpos($term_id, "-"));
						

						
						$term_id = substr($term_id, strrpos($term_id, "-")+1);

						$term = get_term_by("id", $term_id, $tax); 
						echo $sep.$term->name;
						if ($sep == "") $sep = ", ";
					}
				}
				
			?></td>
			<td><?php
				if (is_array($field->posts_ids)) {
					$sep = "";
					foreach ($field->posts_ids as $post_ID) {
						$post = get_post($post_ID); 
						setup_postdata($post);
						echo $sep;
						the_title();//$cat->name;
						if ($sep == "") $sep = ", ";
					}
				}
				
			?></td>
			
			<td><?php
				if (is_array($field->custom_post_types)) {
					$sep = "";
					foreach ($field->custom_post_types as $post_type) {
						//$post = get_post($page_ID); 
						//setup_postdata($post);
						echo $sep;
						echo $post_type;
						if ($sep == "") $sep = ", ";
					}
				}
				
			?></td>
			
			<th scope="col"><?php echo $this->bcf->fields_types[$field->type]; ?></th>
			<th scope="col"><?php echo ($field->multiple == 1 ? 'yes' : 'no' ); ?></th>
			<?php if ( $this->bcf->multilanguage ) : ?>
			<th scope="col"><?php echo ($field->multilanguage == 1 ? 'yes' : 'no' ); ?></th>
			<?php endif; ?>
			<th scope="col"><?php echo $field->depth; ?></th>
			<th><?php echo $field->sort; ?></td>
		</tr>	

<?php

			endforeach;
		//endforeach;
?>

	</table>
<?php	
		
		endforeach;
		
	else:
	
?>

	<p>No fields</p>

<?php	
		
	endif;

?>

</div>

<?php

		
	
	}
	
	
	function ajax_bcf_get_options() {
	
		$widget_name = $_REQUEST["widget_name"];
		
		$class_name = "bcf_".$widget_name;

		if ( class_exists( $class_name ) ) {				

			if (method_exists($class_name, 'option_init') ) {
				if(is_callable(array($class_name,"option_init"))) {
					call_user_func(array($class_name, 'option_init'));
				}
			}
							
		}
	
		die();
	
	}
		
	function ajax_bcf_update_sorting_box() {
	
		$boxs_with_fields = $_REQUEST["boxs_with_fields"];
		
		//Ritrovo i Widget Presenti
		$option_fields = $this->get_option_fields();
		
		//Creo un array con i dati
		$fields = array();
		
		foreach ($option_fields as $boxs) {
		
			if (is_array($boxs)) {
		
				foreach ($boxs as $field_key => $field) {
				
					$fields[$field_key] = $field;
					
				}
			
			}
		}
		
		
//		$option_box = $this->get_option_box();
		$new_options_fields = array();
//		echo "$widget_name: $widget_sorting in $widget_box";
		
		foreach ($boxs_with_fields as $value) {
			
			$value = explode(":", $value);
			//0 : box_id
			//1 : field_name
			//2 : sorting
			
			if (!is_array($new_options_fields[$value[0]])) {
				
				$new_options_fields[$value[0]] = array();
			
			} 
			
			//Becco il field salvato in origine
			
			$field = $fields[$value[1]];
			
			$field->sort = $value[2];
			
			//Assegno il nuovo sorting
			
			$new_options_fields[$value[0]][$value[1]] = $field;
			
		}
		
		//Salvo il nuovo sorting
		update_option( 'bcf_fields', serialize($new_options_fields) );
		
		die();
	
	}

}

