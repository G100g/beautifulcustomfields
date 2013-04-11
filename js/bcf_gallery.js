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
				this.$reset_button = this.$('button.reset_media');
				
				this.$preview = this.$('.preview');
				this.$field_input = this.$('#'+this.options.field_name);
				
				this.is_gallery = (this.options.mime_type.lastIndexOf( "image" ) > -1 && this.options.multiple) || false;
					
				//_.bindAll(this, 'select');	
					
				this.init_frame();
				
				this.render();
				
			},
			
			render: function () { },
			
			frame: function () {
			
				var _frame,
					options = this.options
				;	
				
				options.mime_type.toLowerCase();				
				if (options.mime_type == "") options.mime_type = "image";
				
				if (this.is_gallery ) {
				
					var selection = this.select();
			
					_frame = wp.media({
			            id:         options.field_name,
			            frame:		options.multiple ? 'post' : 'select',
			            state:		options.multiple ? 'gallery-edit' : null,
			            title:  	options.multiple ?  wp.media.view.l10n.editGalleryTitle : wp.media.view.l10n.insertMediaTitle,
			            editing:		options.multiple ? true : false,

			            button: {
							text: 'Select File',
  						},
			            multiple:		options.multiple ? true : false,
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
			            title:  	options.multiple ?  wp.media.view.l10n.editGalleryTitle : wp.media.view.l10n.insertMediaTitle,
			            //editing:		options.multiple ? true : false,
			            editing: false,

			            button: {
							text: 'Select File',
  						},
			            multiple:		options.multiple ? true : false,
			            selection:  selection,
			            library: {
				            type: options.mime_type == "" ? 'image' : options.mime_type
     					}
			        });
*/

					

			        
				} else {
			
					_frame = new BCFPost({
							
						id:         options.field_name,
						multiple:		options.multiple ? true : false,
						button: {
							text: 'Select File',
  						},
  						//selection:  selection,
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
				        
				        this._frame.on( 'insert', function () {
					        
					        var attachments = _self._frame.state().get('selection');
					        _view.update(attachments);
					        
				        });
				        
				        this._frame.on( 'select', function () {
				        
					        //var attachment = _self._frame.state().get('selection').first().toJSON();
					        
					        var attachments = _self._frame.state().get('selection');
					         _view.update(attachments);
 							
				        });
				        
				        this._frame.on('open',function() {
				        	if (!options.is_gallery )
											        	_view._select();
				        });
				        
				        this._frame.on( 'update', function (attachments) {
				        
				        	 _view.update(attachments);
						    
				        });
				        
				        return this._frame;
				    },
				    
				    init: function() {
				        
				       _view.$button.click( function( event ) {
				            event.preventDefault();
				            wp.media[bcf_field_name].frame().open();
				        });
				        
				       _view.$reset_button.click( function( event ) {
				            event.preventDefault();
				            //wp.media[bcf_field_name].frame().open();
				            
				            _view.reset();
				            
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
			    
			    this.$reset_button.removeClass('hidden');
			    
		    },
		    
		    reset_preview: function () {
		    	var _self = this;
		    	this.$preview.html("");
	        	this.$reset_button.addClass('hidden');
	        	
		    },
		    
		    update_field: function (value) {
		    	this.$field_input.val(value);
		    },
		    
		    reset: function (attachments) {
		    
		    	this.reset_preview();
		    	this.update_field('');
		    
		    },
		    
		    update: function (attachments) {
			    
			    this.update_preview(attachments.toJSON());
				var ids = attachments.pluck('id');						    
			    this.update_field(ids);
			    
		    },
			
			_select: function() {
				
				//19,31,30 
				//console.log(wp.media[this.options.field_name]);
				
				//console.log( this.$field_input.val() );
				var attachment;
				var selection = wp.media[this.options.field_name].frame().state().get('selection');
				//var selection = new wp.media.model.Selection([], {multiple: true});
			
				var ids = this.$field_input.val().split(',');
                ids.forEach(function(id) {
                    attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                });
                
                return selection;
                
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
			
			
		});

var media = wp.media
	,l10n;
l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

var BCFPost = media.view.MediaFrame.Post.extend({

	initialize: function() {
		_.defaults( this.options, {
			selection: [],
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
				filterable: 'all',
				toolbar: options.multiple ? 'main-insert' : 'select',
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