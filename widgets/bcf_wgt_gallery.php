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

	public function show_field($field, $id, $name, $class, $active, $value) {
	
		$value = ($value !== false ? htmlentities($value, ENT_QUOTES, get_option('blog_charset')) : '');
		
?>		
		<span id="<?php echo $name; ?>_preview">
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
		</span>
		
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" class="full <?php echo $id. " " .$class; ?>" <?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?> />
		
		<button id="bcf_gallery_new" class="button">Select Images</button>
		
		<script lang="javascript/text">
		
			(function ($) {
				$(document).ready(function(){
				
					$preview = $('#<?php echo $name; ?>_preview');
				
				
				wp.media.bcf_gallery = {
	     
				    frame: function() {
				        if ( this._frame )
				            return this._frame;
						
						var selection = this.select();
				        this._frame = wp.media({
				            id:         'my-frame',                
				            frame:      'post',
				            state:      'gallery-edit',
				            title:      wp.media.view.l10n.editGalleryTitle,
				            editing:    true,
				            multiple:   true,
				            selection:  selection
				        });
				        
				        this._frame.on( 'update', function (attachments) {
				        
				        	//console.log(onon);
				        	//Preview
				        	$preview.html("");
				        	attachments.each(function(attachment) {
										    
								var url = attachment.get('sizes').thumbnail.url;    
						        $preview.append('<img src="'+url+'" />');
        
						    });
						    
						    var ids = attachments.pluck('id');						    
						    $('#bcf_gallery').val(ids);
				        	
				        	/*
				        	var controller = wp.media.bcf_gallery._frame.states.get('gallery-edit');
				        	
						    console.log(controller, 'controller');
				        	
						    var library = controller.get('library');
						    
						    var gallery = library.get('gallery');
						    console.log(gallery);
						     
						     
						    var selection = controller.get('selection');
						    console.log(selection);
						    
						    
						    //var gallery = selection.get('gallery');
						    
						   
						    
				        	onon.each(function(attachment) {
						        console.log(attachment);
        // this will return an object with all the attachment-details
						    });
				        	
						    
						    
						    // Need to get all the attachment ids for gallery
						    
						    console.log(library);
						    
						    var ids = library.pluck('id');
						    
						    $('#bcf_gallery').val(ids);
						    */
						    
				        });
				        
				       // this._frame.
				        
				        return this._frame;
				    },
				 
				    init: function() {
				        
				        $('#bcf_gallery_new').click( function( event ) {
				            event.preventDefault();
				            wp.media.bcf_gallery.frame().open();
				 
				        });
				        
				    },
				    
				    select: function() {
						    //[gallery ids="10,7"]
						    var shortcode = '[gallery ids="7, 10"]',
						        defaultPostId = wp.media.gallery.defaults.id,
						        attachments, selection;
						 
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
						    
						    /*
						    var shortcode = wp.shortcode.next( 'gallery', wp.media.view.settings.shibaMlib.shortcode ),
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
						 	*/
						    return selection;
						},
				};
				 
				
				    $( wp.media.bcf_gallery.init );
				});
			
			})(jQuery);

					
		</script>
		
<?php
		
	}
	
}