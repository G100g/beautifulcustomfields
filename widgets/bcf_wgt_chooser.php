<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Chooser Field

*/

class bcf_chooser extends bcf_select {

	public function init() {			
	
		//wp_enqueue_script('jquery-tokeninput', WP_PLUGIN_URL.'/beautifulcustomfields/
		//wp_enqueue_style('jquery-tokeninput-css', WP_PLUGIN_URL.'/beautifulcustomfields/lib/jquery-tokeninput/token-input-wp.css','', '1.5.0');
		
		wp_enqueue_script('jquery-tokeninput', plugins_url( 'lib/jquery-tokeninput/jquery.tokeninput.js', dirname(__FILE__) ), array('jquery'), '1.5.0');
		wp_enqueue_style('jquery-tokeninput-css', plugins_url( 'lib/jquery-tokeninput/token-input-wp.css', dirname(__FILE__) ), NULL, '1.5.0');
				
		//add_action('wp_ajax_bcf_chooser_source', array(bcf_chooser, 'ajax_source'), 120);

		$widget_name = get_class($this);
		
		//echo ":::". $widget_name ."::<br>";
//					echo __CLASS__ . "<br>";
		//add_action('wp_ajax_bcf_chooser_source', array(__CLASS__, 'ajax_source'), 120);
		add_action('wp_ajax_'.$widget_name.'_source', array($this, 'ajax_source'), 120);
		
	}
	
	public function html_header() {
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
	
	//function show_field($field, $id, $class, $active, $value) {
	public function show_field($field, $id, $name, $class, $active, $value) {
	
		global $post;
		
		$value = ($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '');

		//Creo la lista dei campi già inseriti
		
<<<<<<< HEAD
=======

		
>>>>>>> origin/master
		//Ottengo l'elenco
		$selected_options = explode(",", $value);
		$json_selected_options = array();
		
		if (!empty($value)) {
<<<<<<< HEAD

=======
			
>>>>>>> origin/master
			$options = $this->_get_options($field, $value);

			foreach($options as $k => $option) {
					
				$option = explode("[::]", $option);
<<<<<<< HEAD

=======
							
>>>>>>> origin/master
				if (in_array($option[0], $selected_options)) {
	
					$json_selected_options[] = (object) array("id" => $option[0], 'name' => $option[1]);
					
				}
				
			}
		
		}
		
?>			
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" class="full <?php echo $id. " " .$class; ?>" <?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?> />
<?php
		
		$options["tokenLimit"] = null;
		
		$widget_name = get_class($this);
		
		//Applico il filtro	
		$options = apply_filters($widget_name . "_token_options", $options);
		//Applico il filtro specifico al campo		
		$options = apply_filters($widget_name.'_token_options_'.$field->cf_name, $options);
		
		
		
		$ajax_var = "?action={$widget_name}_source&cf_name={$field->cf_name}";
		
		$ajax_var = apply_filters($widget_name . "_ajax_var", $ajax_var);
		$ajax_var = apply_filters($widget_name . "_ajax_var_" . $field->cf_name, $ajax_var);

		//$post->ID
		
<<<<<<< HEAD
		
=======
>>>>>>> origin/master
?>
	<script type="text/javascript">
	jQuery(function($) {
	
		var $searchable = $("input.<?php echo $name; ?>");

		$searchable.tokenInput( ajaxurl + '<?php echo $ajax_var; ?>', 
														{ 
														prePopulate: <?php echo json_encode($json_selected_options); ?>,
														animateDropdown: false,
														preventDuplicates: true,
<<<<<<< HEAD
														minChars: 0,
=======
														minChars: 2,
>>>>>>> origin/master
														tokenLimit: <?php echo is_numeric($options["tokenLimit"]) ? $options["tokenLimit"] : 'null'; ?>,
														theme: 'wp'
														<?php if ($options["tokenLimit"] != 1) : ?>
														,
														onReady: function () {
															$( ".<?php echo $name; ?> ul.token-input-list-wp" ).sortable({
																placeholder: 'ui-state-highlight',
																update: function (event, ui) {
																	$searchable.tokenInput('updateHidden');
																}
															});
															$( ".<?php echo $name; ?> ul.token-input-list" ).disableSelection();
															
														}
														<?php endif; ?>		
														});		
	});
	</script>
		
<?php		
		
		//parent::show_field($field, $id, $class, $active, $value);
	}
	
	function __search_by_title_only( $search, &$wp_query )
	{
		
		global $wpdb;
		
	    if ( empty($search) )
	        return $search; // skip processing - no search term in query
	
	    $q =& $wp_query->query_vars;
	
	    // wp-includes/query.php line 2128 (version 3.1)
	    $n = !empty($q['exact']) ? '' : '%';
	    $searchand = '';
		$_search = '';
	    foreach( (array) $q['search_terms'] as $term ) {
	        $term = esc_sql( like_escape( $term ) );
	        $_search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
	        $searchand = ' AND ';
	    }
	
	    $term = esc_sql( like_escape( $q['s'] ) );
	    if ( empty($q['sentence']) && count($q['search_terms']) > 1 && $q['search_terms'][0] != $q['s'] )
	        $_search .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
	
	    if ( !empty($_search) ) {
	        $_search = " AND ({$_search}) ";
	        if ( !is_user_logged_in() )
	            $_search .= " AND ($wpdb->posts.post_password = '') ";
	    }
	
	    return $_search; 
	}

	/**
	
		Get Options to Suggest
		
	**/


	public function ajax_source() {
	
		$cf_name = $_REQUEST["cf_name"];
		
		
		//Ritrovo il campo definito nel backedn
		$bcf_fields = unserialize( get_option('bcf_fields') );
		$field = null;
		
		foreach ($bcf_fields as $box_id => $fields) {
		
			if (array_key_exists($cf_name, $fields)) {
			
				$field = $fields[$cf_name];
				$field->cf_name = $cf_name;
				
				break;
			
			}
	
		}
		
		/*
		//Becco i dati del campo detto 
		$class_name = "bcf_" . $field->type;//"bcf_chooser";

<<<<<<< HEAD
		if ( class_exists( $class_name ,false) ) {
=======
		if ( class_exists( $class_name ) ) {
>>>>>>> origin/master
			$f = new $class_name();
			if (method_exists($f, '_get_options') ) {
				$options = $f->_get_options($field);
			}
		}
		
		*/
<<<<<<< HEAD

=======
		
>>>>>>> origin/master
		$options = $this->_get_options($field);
		
		$source = array();
		if (is_array($options)) {
			foreach($options as $k => $option) {
				$option = explode("[::]", $option);
				$source[] = (object) array("id" => $option[0], 'name' => $option[1]);
			}
		}
	
		echo json_encode($source);
		
		die();
	}
	

}