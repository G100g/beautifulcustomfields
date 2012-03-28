<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Chooser Field

*/

class bcf_chooser_post_attachments extends bcf_chooser {

		public function init() {			
		
			global $widget_name;
		
			parent::init();
			
			$widget_name = get_class($this);
			
			add_filter($widget_name, array($this, 'options_attachments'), 1, 2);
			
			add_filter($widget_name."_ajax_var", array($this, 'extend_ajax_var'));
			
			//echo ;
			
		
		}
		
		public function extend_ajax_var($ajax_var) {
			
			global $post;			
			return $ajax_var . "&post_id=".$post->ID;
			
		}
		
		public function options_attachments($options, $value) {

			$post_id = $_REQUEST["post_id"];
			$values = $this->get_attachment($post_id, $value, $options);
			
			return $values;
		
		}
		
		protected function get_attachment($post_id, $value, $options = array()) {

			$post_attachments = array();
			
			$options = $options[0];
			
			//if ($options == '*') $options = '';
			$value = explode(",", $value);
			$value = array_flip($value);		
			
			$args = array(
				'post_status' => null,
				'post_type' => 'attachment',
				'post_mime_type' => $options,
				'post_parent' => $post_id,
				'order_by' => 'title',
				'order' => 'asc',
			);
			$a = get_posts($args);

			if ($a) {
					foreach($a as $attachment) {
				
					//Becco il thumb del file
					$thumb = wp_get_attachment_image( $attachment->ID, array(46, 46), TRUE  );
					
					if (!is_array($value)) {
						$post_attachments[] = $attachment->ID."[::]" . $thumb  . get_the_title($attachment->ID);
					} else {
						$post_attachments[ $value[$attachment->ID] ] = $attachment->ID."[::]" . $thumb  . get_the_title($attachment->ID) . " | <a href=\"". get_permalink($attachment->ID)."\" target=\"_blank\" title=\"#". $attachment->ID."\">view</a>";
					}
				}
			}
			
			ksort($post_attachments);
			
			return $post_attachments;
		
		}
		

		/**
			
			Option Init
			
			Eseguita dalla pagina delle opzioni
			
		**/
		
		public static function option_init() {	
			
			
	?>
			<div id="chooser_post_attachments_options">
				
				<form>	
					<p><label><input type="radio" name="mime_type"  value="*"> All</label>
					
					<label><input type="radio" name="mime_type" value="image"> Images</label>
					<label><input type="radio" name="mime_type" value="video"> Videos</label>
					<label><input type="radio" name="mime_type" value="audio"> Audios</label>
					
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
					
					if (options == '') {
						options = "*";
						$input.val(options);				
					}
					
					$('#chooser_post_attachments_options input[value="'+options+'"]').prop('checked', 'checked');	
				
				}
				
				function update_options () {
				
					var new_value = '';

					new_value = $('#chooser_post_attachments_options input:checked').val();
					
					$input.val(new_value);
				
				}
				
				$('#chooser_post_attachments_options input:radio').change(function () {				
					update_options();				
				});
				
				init_options();
				
			
			})(jQuery);
			
			
			</script>
	<?php		
	
		}
	

}