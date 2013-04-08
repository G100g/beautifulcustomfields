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
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" class="full <?php echo $id. " " .$class; ?>" <?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?> />
		
		<button id="bcf_gallery_new" class="button">Select Images</button>
		
		<script lang="javascript/text">
		
			(function ($) {
				$(document).ready(function(){
				
				wp.media.bcf_gallery = {
	     
				    frame: function() {
				        if ( this._frame )
				            return this._frame;
				 
				        this._frame = wp.media({
				            id:         'my-frame',                
				            frame:      'post',
				            state:      'gallery-edit',
				            title:      wp.media.view.l10n.editGalleryTitle,
				            editing:    true,
				            multiple:   true,
				        });
				        
				        this._frame.on( 'update', function (onon) {
				        
				        console.log(onon);
				        	
				        	var controller = wp.media.bcf_gallery._frame.states.get('gallery-edit');
				        	
						    console.log(controller);
				        	
						    var library = controller.get('library');
						    
						    var gallery = library.get('gallery');
						    console.log(gallery);
						     
						     
						    var selection = controller.get('selection');
						    console.log(selection);
						    
						    
						    //var gallery = selection.get('gallery');
						    
						   
						    
				        	gallery.each(function(attachment) {
						        console.log(attachment);
        // this will return an object with all the attachment-details
						    });
				        	
						    
						    
						    // Need to get all the attachment ids for gallery
						    
						    console.log(library);
						    
						    var ids = library.pluck('id');
						    
						    $('#bcf_gallery').val(ids);
						    
				        });
				        
				       // this._frame.
				        
				        return this._frame;
				    },
				 
				    init: function() {
				        
				        $('#bcf_gallery_new').click( function( event ) {
				            event.preventDefault();
				            wp.media.bcf_gallery.frame().open();
				 
				        });
				        
				    }
				};
				 
				
				    $( wp.media.bcf_gallery.init );
				});
			
			})(jQuery);

					
		</script>
		
<?php
		
	}
	
}