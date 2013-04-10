<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Text Fields

*/

class bcf_gallery extends bcf_text {

	public function html_header() {
?>
		
		<style type="text/css">
		
			.bcf_gallery_preview {
			
			}
				.bcf_gallery_preview img {
					border: 1px solid #999;
					margin-right: 6px;
					
					height: 80px;
					width: auto;
				} 
		
		</style>
<?php
		
	}

	public function show_field($field, $id, $name, $class, $active, $value) {
	
		$value = ($value !== false ? htmlentities($value, ENT_QUOTES, get_option('blog_charset')) : '');
		
		$options = explode(",", $field->type_options);
		
?>		
		<p id="<?php echo $name; ?>_preview" class="bcf_gallery_preview">
<?php
		
			if ($value != "") {
				
				$image_ids = explode("," ,$value);
				foreach($image_ids as $image_id) :
				
				$image = wp_get_attachment_image($image_id, 'thumbnail');
				
?>
				<?php echo $image; ?>	
<?php				
				endforeach;
			
			}
?>
		</p>
		
		<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" class="full <?php echo $id. " " .$class; ?>" <?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?> />
		
		<p><button id="<?php echo $name; ?>_new" class="button">Select Image<?php echo $options[0] == 1? "s" : ""; ?></button></p>
		
		<script type="text/javascript">
		
			(function ($) {
			
				$(document).ready(function(){
				
				var bcf_field_name = '<?php echo $name; ?>';
				
				wp.media[bcf_field_name] = {
				
					$preview: $('#'+bcf_field_name+'_preview'),
					$field_input: $('#'+bcf_field_name),
	     
				    frame: function() {
				        
				        if ( this._frame )
				            return this._frame;
				            
				        var _self = this;
						
						var selection = this.select();
				        this._frame = wp.media({
				            id:         bcf_field_name,
				            <?php echo ($options[0] == 1 ? "frame:      'post'," : ""); ?>
				            <?php echo ($options[0] == 1 ? "state: 'gallery-edit'," : ""); ?>
				            title:      <?php echo ($options[0] == 1 ? 'wp.media.view.l10n.editGalleryTitle' : 'wp.media.view.l10n.insertMediaTitle'); ?>,
				            <?php echo ($options[0] == 1 ? "editing:    true," : ""); ?>

				            button: {
								text: 'Select File',
      						},
				            multiple:   <?php echo ($options[0] == 1 ? 'true' : 'false'); ?>,
				            selection:  selection,
				            library: {
					            type: 'image'
         					},
				        });
				        
				        this._frame.on( 'select', function (attachments) {
				        
					        var attachment = _self._frame.state().get('selection').first().toJSON();
 							_self.update_preview([ attachment.sizes.thumbnail.url ]);
 							_self.update_field(attachment.id);
 							
				        });
				        
				        this._frame.on( 'update', function (attachments) {
				        
				        	var image_urls = [];
				        	attachments.each(function(attachment) {
								image_urls.push(attachment.get('sizes').thumbnail.url);
						    });
						    _self.update_preview(image_urls);
						    
						    var ids = attachments.pluck('id');						    
						    _self.update_field(ids);
						    
				        });
				        
				        return this._frame;
				    },
				    
				    update_preview: function (images) {
				    	var _self = this;
				    	this.$preview.html("");
			        	$.each(images, function(i, image) {
					        _self.$preview.append('<img src="'+image+'" />');
					    });
				    },
				    
				    update_field: function (value) {
				    	this.$field_input.val(value);
				    },
				 
				    init: function() {
				        
				        $('#'+bcf_field_name+'_new').click( function( event ) {
				        
				            event.preventDefault();
				            wp.media[bcf_field_name].frame().open();
				 
				        });
				        
				    },
				    
				    select: function() {
						    var shortcode = wp.shortcode.next( 'gallery', '[gallery ids="'+this.$field_input.val()+'"]'  ),
						        defaultPostId = wp.media.gallery.defaults.id,
						        attachments, selection;
						 
						    // Bail if we didn't match the shortcode or all of the content.
						    if ( ! shortcode )
						        return;
						 
						    // Ignore the rest of the match object.
						    shortcode = shortcode.shortcode;
						 
						    if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
						        shortcode.set( 'id', defaultPostId );
						 
						    attachments = wp.media.gallery.attachments( shortcode );
						    selection = new wp.media.model.Selection( attachments.models, {
						        props:    attachments.props.toJSON(),
						        multiple: true
						    });
						     
						    selection.gallery = attachments.gallery;
						 
						    // Fetch the query's attachments, and then break ties from the
						    // query to allow for sorting.
						    selection.more().done( function() {
						        // Break ties with the query.
						        selection.props.set({ query: false });
						        selection.unmirror();
						        selection.props.unset('orderby');
						    });
						    return selection;
						},
				};
				
				    $( wp.media[bcf_field_name].init );
				    
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
			<div id="bcf_gallery_options">
				
				<form>
				
					<p><label><input type="radio" value="0" name="bcf_gallery_single" /> Single</label> <label><input type="radio" value="1" name="bcf_gallery_single" /> Multiple</label></p>					
					<p><label>Mime-type</label> <input type="text" value="" name="bcf_gallery_mime" /></p>
					
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
						$('#bcf_gallery_options input[name="bcf_gallery_single"][value="'+options[0]+'"]').prop('checked', 'checked');
						$('#bcf_gallery_options input[name="bcf_gallery_mime"]').val(options[1]);
					}
				
				}
				
				function update_options () {
				
					var selection = $('#bcf_gallery_options input[name="bcf_gallery_single"]:checked').first().val()
					var mime_type = $('#bcf_gallery_options input[name="bcf_gallery_mime"]').val().toString();
					mime_type = mime_type.replace(/,/gi, "");
					$input.val(selection + "," + mime_type);
									
				}
				
				$('#bcf_gallery_options input[name="bcf_gallery_single"]').change(function () {				
					update_options();				
				});
				
				$('#bcf_gallery_options input[name="bcf_gallery_mime"]').keyup(function () {				
					update_options();				
				});
				
				init_options();				
			
			})(jQuery);			
			
			</script>
	<?php		
	
	}
	
}