<?php
/**
 * @package bcf_media
 * @author G100g
 * @version 0.2
 */

/*
	
	Media

*/

class bcf_media extends bcf_text {

	public function init() {			
	
		wp_enqueue_script('bcf_media', plugins_url( 'js/bcf_media.js', dirname(__FILE__) ), array('backbone'), '0.0.1', true);
		//wp_enqueue_style('jquery-tokeninput-css', plugins_url( 'lib/jquery-tokeninput/token-input-wp.css', dirname(__FILE__) ), NULL, '1.5.0');
	}

	public function html_header() {
?>
		
		<style type="text/css">
		
			.bcf_media_view .preview {
				float: inherit;
			}
			
				.bcf_media_view .preview .thumbnail {
					color: #333;
					text-align: center;
					margin-right: 6px;
				}
				
					.bcf_media_view .preview .thumbnail .title {
						line-height: 32px;
					}
					
					.bcf_media_view .preview .image .title {
						display: none;
					}
				
				.bcf_media_view .preview img {
					height: 32px;
					width: auto;
					vertical-align: middle;
				}	
				
				.bcf_media_view .preview .image img {
					border: 1px solid #999;
					height: 60px;
				} 
		
		</style>
		
		<script type="text/javascript"></script>
<?php
		
	}

	public function show_field($field, $id, $name, $class, $active, $value) {
	
		$value = ($value !== false ? htmlentities($value, ENT_QUOTES, get_option('blog_charset')) : '');
		
		$options = explode(",", $field->type_options);
		
?>		
		<div id="<?php echo $name; ?>_view" class="bcf_media_view">
		
			<p class="preview">
<?php
		
			if ($value != "") {
				
				$image_ids = explode("," ,$value);
				foreach($image_ids as $image_id) :
				
				//if (wp_attachment_is_image($image_id)) {
					$filename = basename ( get_attached_file( $image_id ) );
					$image = wp_get_attachment_image($image_id, 'thumbnail', true);
				
?>
					<span class="thumbnail <?php echo (wp_attachment_is_image($image_id) ? 'image' : ''); ?>">
						<?php echo $image; ?>
						<span class="title"><?php echo $filename; ?></span>
					</span>
				
				
					
<?php				
				endforeach;
			
			}
?>
			</p>
		
			<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" class="full <?php echo $id. " " .$class; ?>" <?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?> />
		
			<p><button class="button select_media">Select Media</button> <button class="button reset_media <?php echo ($value == "" ? 'hidden' : ''); ?>">Reset</button></p>
		
		</div>
		
		<script type="text/javascript">
		
			(function ($) {
			
				$(document).ready(function(){
				
					var bcf_field_name = '<?php echo $name; ?>';
					new bcf_media({
						el: '#'+bcf_field_name + '_view',
						field_name: bcf_field_name,
						
						multiple: <?php echo ($options[0] == 1 ? "true" : "false"); ?>,
						mime_type: '<?php echo ($options[1] == "" ? 'image' : $options[1]); ?>'
						
						});
				    
				});
			
			})(jQuery);

					
		</script>
		
<?php
		
	}
	
		/**
			
			Option Init
			
			Eseguita dalla pagina delle opzioni
			
		**/
		
	public static function option_init() {	
			
	?>
			<div id="bcf_media_options">
				
				<form>
				
					<p><label><input type="radio" value="0" name="bcf_media_single" /> Single</label> <label><input type="radio" value="1" name="bcf_media_single" /> Multiple</label></p>					
					<p><label>Mime-type</label> <input type="text" value="" name="bcf_media_mime" /></p>
					
				</form>		
				
			</div>
			
			<style></style>
			
			<script type="text/javascript">
			(function ($) {
				
				var $input = $('input[name="nf_type_options"]');
				
				function init_options() {
							
					var options = $input.val();
									
					if (options != '') {
						var options = options.split(",");
						$('#bcf_media_options input[name="bcf_media_single"][value="'+options[0]+'"]').prop('checked', 'checked');
						$('#bcf_media_options input[name="bcf_media_mime"]').val(options[1]);
					}
				
				}
				
				function update_options () {
				
					var selection = $('#bcf_media_options input[name="bcf_media_single"]:checked').first().val()
					var mime_type = $('#bcf_media_options input[name="bcf_media_mime"]').val().toString();
					mime_type = mime_type.replace(/,/gi, "");
					$input.val(selection + "," + mime_type);
									
				}
				
				$('#bcf_media_options input[name="bcf_media_single"]').change(function () {				
					update_options();				
				});
				
				$('#bcf_media_options input[name="bcf_media_mime"]').keyup(function () {				
					update_options();				
				});
				
				init_options();				
			
			})(jQuery);			
			
			</script>
	<?php		
	
	}
	
}