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
				this.$field_input = this.$('#'+this.options.field_name);
					
				this.init_frame();
				
				this.render();
				
			},
			
			render: function () { },
			
			frame: function () {
			
				var _frame,
					options = this.options,
					selection = this.select()
				;
				
				options.mime_type.toLowerCase();				
				if (options.mime_type == "") options.mime_type = "image";
				
				if (options.mime_type.lastIndexOf( "image" ) == -1  ) {
				
					_frame = new BCFPost({
							
						id:         options.field_name,
						multiple:		options.multi_selection ? true : false,
						button: {
							text: 'Select File',
  						},
  						selection:  selection,
						library: {
				            type: options.mime_type == "" ? 'image' : options.mime_type
     					}
					
					});
				
					/*

					_frame = wp.media({
			            id:         options.field_name,
			            frame:		'post',
//			            state: 'insert',
			            title:  	options.multi_selection ?  wp.media.view.l10n.editGalleryTitle : wp.media.view.l10n.insertMediaTitle,
			            //editing:		options.multi_selection ? true : false,
			            editing: false,

			            button: {
							text: 'Select File',
  						},
			            multiple:		options.multi_selection ? true : false,
			            selection:  selection,
			            library: {
				            type: options.mime_type == "" ? 'image' : options.mime_type
     					}
			        });
*/

					

			        
				} else {
			
					_frame = wp.media({
			            id:         options.field_name,
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
     					}
			        });
				
				}
				
				return _frame;
			},
			
			init_frame: function () {
				
				var _view = this;
				
				var bcf_field_name = this.options.field_name;
				
				var options = this.options;
				
				wp.media[bcf_field_name] = {
	     
				    frame: function() {
				    
				        if ( this._frame )
				            return this._frame;
				            
				        var _self = this;
						
				        this._frame = _view.frame();
				        
				        this._frame.on( 'select', function (attachments) {
				        
				        	console.log('select', attachments);
				        
					        var attachment = _self._frame.state().get('selection').first().toJSON();
 							//_self.update_preview([ attachment.sizes.thumbnail.url ]);
 							_view.update_preview([ attachment ]);
 							_view.update_field(attachment.id);
 							
				        });
				        
				        this._frame.on( 'update', function (attachments) {
				        
				        	console.log('update', attachments);

				        
				        	/*
				        	var image_urls = [];
				        	attachments.each(function(attachment) {
								image_urls.push(attachment.get('sizes').thumbnail.url);
						    });
						    _self.update_preview(image_urls);
						    */
						    
						    _view.update_preview(attachments.toJSON());
						    
						    var ids = attachments.pluck('id');						    
						    _view.update_field(ids);
						    
				        });
				        
				        return this._frame;
				    },
				    
				    init: function() {
				        
				       _view.$button.click( function( event ) {
				            event.preventDefault();
				            wp.media[bcf_field_name].frame().open();
				        });
				        
				    },
				    
				};
				
				this.$( wp.media[bcf_field_name].init );
				
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
			
			select: function() {
				    console.log(this.$field_input.val());
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
			
			
		});

var media = wp.media
	,l10n;
l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

var BCFPost = media.view.MediaFrame.Post.extend({

	initialize: function() {
		_.defaults( this.options, {
			multiple:  true,
			editing:   true,
			state:    'insert'
		});

		media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
		this.createIframeStates();
	},
	createStates: function() {
		var options = this.options;

		// Add the default states.
		this.states.add([
			// Main states.
			new media.controller.Library({
				id:         'insert',
				title:      l10n.insertMediaTitle,
				priority:   20,
				toolbar:    'main-insert',
				filterable: 'all',
				library:    media.query( options.library ),
				multiple:   options.multiple ? 'reset' : false,
				editable:   true,

				// If the user isn't allowed to edit fields,
				// can they still edit it locally?
				allowLocalEdits: true,

				// Show the attachment display settings.
				displaySettings: true,
				// Update user settings when users adjust the
				// attachment display settings.
				displayUserSettings: true
			})
		]);
		
	}						
						
});


})(jQuery);