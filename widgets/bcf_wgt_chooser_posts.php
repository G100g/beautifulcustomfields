<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Chooser Field

*/

class bcf_chooser_posts extends bcf_chooser {

		public function init() {			
		
			global $widget_name;
		
			parent::init();
			
			$widget_name = get_class($this);
			
			add_filter($widget_name, array($this, 'options_posts'), 1, 2);
			
			add_filter($widget_name."_ajax_var", array($this, 'extend_ajax_var'));
			
			//echo ;
			
		
		}
		
		public function extend_ajax_var($ajax_var) {
			
			global $post;			
			return $ajax_var . "&post_id=".$post->ID;
			
		}
		
		public function options_posts($options, $value) {

			$exclude_posts = null;
			if (array_key_exists("post_id", $_REQUEST)) {
				$exclude_posts = array($_REQUEST["post_id"]);
			}
			$values = $this->get_posts($value, $options, $exclude_posts);
			
			return $values;
		
		}
		
		protected function get_posts($value, $options = array(), $exclude_posts = null) {
			
			global $post;
			
			$post_types = explode(",", $options[0]);
			
			$_post = $post;
			$args = array(
						'post_type' => $post_types,
						'posts_per_page' => -1,
						'orderby' => 'title', 
						'order' => 'asc'		
						);
			
			if ($exclude_posts != null) {
				
				$args["post__not_in"] = $exclude_posts;
				
			}
						 	
			if (isset($_REQUEST["q"]) && !empty($_REQUEST["q"])) {
				
				add_filter( 'posts_search', array($this, '__search_by_title_only'), 10, 2 );
				
				//$related->set('s',  $_REQUEST["q"]);
				$args['s'] = $_REQUEST["q"];
				$values = array();
				
			} else {

				if ($value == "") {
				 //if (count($values) == 1 && $values[0] == "") {
				 	//Non ho ho cercato nulla e non ho elementi da preinsierire dentro al form
					
				 	$values = array();
				
					$post = $_post;
				
					return $values;
					 
				 } else {
							
		//			var_dump($value);
					//Circosscrivo la ricerca ai post precedentemente selezionati	 	
					 //$related->set('post__in',  explode(",", $value) );
					 //$args['post__in'] = explode(",", $value);
					 $value = explode(",", $value);
					 $args['post__in'] = $value;
					 
					 $value = array_flip($value);
					 
				 }
				
			}
		
			$related = new WP_Query($args);
		
			//$related->get_posts();
		
			if ($related->have_posts()) while ($related->have_posts()) {
				$related->the_post();
				
				//Becco il thumb dell'immagine
				$thumb = "";
				if ( has_post_thumbnail($related->post->ID)) {
					$thumb = get_the_post_thumbnail( $related->post->ID, array(32, 32) );
				}
		///		var_dump($thumb);
				
			//	$thumb = "";
			
				//Se sto cercando i post salavati ne mantengo l'rodine
				if (!is_array($value)) {
					$values[] = $related->post->ID."[::]" . $thumb  . get_the_title($related->post->ID);
				} else {
					$values[ $value[$related->post->ID] ] = $related->post->ID."[::]" . $thumb  . get_the_title($related->post->ID) . " | <a href=\"". get_permalink($related->post->ID)."\" target=\"_blank\" title=\"#". $related->post->ID."\">view</a>";
				}
				
			}
			
			$post = $_post;
			
			if (!empty($values)) {
				ksort($values);
			}
			
			return $values;
		}
		
		protected function __get_posts($post_id, $value, $options = array()) {

			
			add_filter( 'posts_search', '__search_by_title_only', 10, 2 );
			
			$_posts = array();
			
			$options = $options[0];
			
			//if ($options == '*') $options = '';
			$value = explode(",", $value);
			$value = array_flip($value);		
			
			$args = array(
				'post_status' => null,
				'post_type' => $options,
				'order_by' => 'title',
				'order' => 'asc',
			);
			$p = get_posts($args);

			if ($p) {
					foreach($p as $_post) {
				
					//Becco il thumb del file
					//$thumb = wp_get_attachment_image( $attachment->ID, array(46, 46), TRUE  );
					
					//if (!is_array($value)) {
					//	$post_attachments[] = $attachment->ID."[::]" . $thumb  . get_the_title($attachment->ID);
					$_posts[] = $_post->ID."[::]" . get_the_title($_post->ID);
					//} else {
						//$post_attachments[ $value[$attachment->ID] ] = $attachment->ID."[::]" . $thumb  . get_the_title($attachment->ID) . " | <a href=\"". get_permalink($attachment->ID)."\" target=\"_blank\" title=\"#". $attachment->ID."\">view</a>";
					//}
					
					
				}
			}
			
			ksort($_posts);
			
			return $_posts;
		
		}
		

		/**
			
			Option Init
			
			Eseguita dalla pagina delle opzioni
			
		**/
		
		public static function option_init() {	
			
			
			$post_types = get_post_Types(array(
				
				'public' => TRUE,
				'_builtin' => TRUE
			
			), 'object');
			
			$custom_post_types = get_post_Types(array(
				
				//'public' => TRUE,
				'_builtin' => FALSE
			
			), 'object');
			
			if (is_array($custom_post_types)) {
				$post_types = array_merge($post_types, $custom_post_types); 
			}
			
	?>
			<div id="chooser_post_attachments_options">
				
				<form>	
					<p>
					<?php foreach ($post_types as $key => $post_type) :
					
//					var_dump($post_type);
					?>
						
					<label><input type="checkbox" name="post_types"  value="<?php echo $key; ?>"> <?php echo $post_type->labels->name; ?></label>
					
					<?php endforeach; ?>
					
					</p>
				</form>		
				
			</div>
			
			<style>
				
					
			</style>
			
			<script>
			(function ($) {
				
				var $input = $('input[name="nf_type_options"]');
				
				function init_options() {
							
					var options = $input.val();
									
					if (options != '') {
						var options = options.split(",");
						
						$.each(options, function (i, e) {
						
							$('#chooser_post_attachments_options input[value="'+e+'"]').prop('checked', 'checked');	
						
						});
					
					}
				
				}
				
				function update_options () {
				
					var new_value = '';
					var sep = "";
					$('#chooser_post_attachments_options input:checked').each( function (i,e) {
						new_value += sep + $(e).val();
						sep = ",";					
					});
					
					$input.val(new_value);				
				}
				
				$('#chooser_post_attachments_options input:checkbox').change(function () {				
					update_options();				
				});
				
				init_options();
				
			
			})(jQuery);
			
			
			</script>
	<?php		
	
		}
	

}