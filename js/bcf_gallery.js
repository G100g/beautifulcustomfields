var bcf_gallery = {};
		
(function ($) {
	
		bcf_gallery = Backbone.View.extend({
	/*
events: {
			    "click .icon":          "open",
			    "click .button.edit":   "openEditDialog",
			    "click .button.delete": "destroy"
			},
	
*/		initialize: function () {
				
				this.$button = this.$('button.select_media');
				this.$preview = this.$('.preview');
				
				this.init_frame();
				
				this.render();
				
			},
			
			render: function () {
				
				
			},
			
			init_frame: function () {
				
				var bcf_field_name = this.options.field_name;
				
				var options = this.options;
				
				wp.media[bcf_field_name] = {
				
					$preview: $('#'+bcf_field_name+'_view .preview'),
					$field_input: $('#'+bcf_field_name),
	     
				    frame: function() {
				    
				        if ( this._frame )
				            return this._frame;
				            
				        var _self = this;
						
						var selection = this.select();
				        this._frame = wp.media({
				            id:         bcf_field_name,
				            frame:		options.multi_selection ? 'post' : 'select',
				            state:		options.multi_selection ? 'gallery-edit' : null,
				            title:  	options.multi_selection ?  wp.media.view.l10n.editGalleryTitle : wp.media.view.l10n.insertMediaTitle,
				            editing:		options.multi_selection ? true : false,

				            button: {
								text: 'Select File',
      						},
				            multiple:		options.multi_selection ? true : false,
				            selection:  selection,
				            library: {
					            type: options.mime_type == "" ? 'image' : options.mime_type
         					},
				        });
				        
				        this._frame.on( 'select', function (attachments) {
				        
					        var attachment = _self._frame.state().get('selection').first().toJSON();
 							//_self.update_preview([ attachment.sizes.thumbnail.url ]);
 							_self.update_preview([ attachment ]);
 							_self.update_field(attachment.id);
 							
				        });
				        
				        this._frame.on( 'update', function (attachments) {
				        	/*
				        	var image_urls = [];
				        	attachments.each(function(attachment) {
								image_urls.push(attachment.get('sizes').thumbnail.url);
						    });
						    _self.update_preview(image_urls);
						    */
						    
						    _self.update_preview(attachments.toJSON());
						    
						    var ids = attachments.pluck('id');						    
						    _self.update_field(ids);
						    
				        });
				        
				        return this._frame;
				    },
				    
				    update_preview: function (attachments) {
				    	var _self = this;
				    	this.$preview.html("");
			        	$.each(attachments, function(i, attachment) {
			        	
			        		var icon_image = attachment.type == 'image' ? attachment.sizes.thumbnail.url : attachment.icon;
			        	    _self.$preview.append('<span class="thumbnail '+attachment.type+'"><img src="'+icon_image+'" /><span class="title">'+attachment.filename+'</span></span>');
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
				
				this.$( wp.media[bcf_field_name].init );
				
			}
			
			
		});

})(jQuery);