(function ($) {

	function load_options() {
	
		var widget_name = $('select[name="nf_type"] option:selected').val();
		
		$.get(ajaxurl, {action: 'bcf_get_options', widget_name: widget_name}, function(r) {

			var $row_options = $('#row_options');
			
			if (r != "") {
			
				$row_options.find('td').html(r);
				
				$row_options.show();
			} else {
				$row_options.hide();
			}
		
		});
		
		
	}

	function update_fields_positions() {
	
		//Leggo i Box esistenti e salvo le posizioni
		
		var fields = [];
		
		$('table.box-fields').each(function (i, box) {
		
			console.log(box);
			var box_id = $(box).attr('id').replace('box_', '');
			
			$(box).find('tr.field').each(function (i, field) {
				
				var field_name = $(field).attr('id').replace('field_', '');
				
				console.log(box_id, field_name, i);
				
				fields.push(box_id+":"+field_name+":"+i);
				
			
			});
		
		});
		
		$.get(ajaxurl, {action: 'bcf_update_sorting_box', boxs_with_fields: fields}, function (rt) {
		
			console.log(rt);
		
		});
		
	
	}

	$(function () {
		
		$('a.submitdelete').click(function (e) {	
		
			if (!confirm('Are you sure?')) {
				return false;	
			}
		
		});
	
		$('select[name="nf_type"]').change(function () {		
			load_options();
		});
		
		load_options();
		
		//Drag & Drop
		$( "table.box-fields" ).sortable({
					connectWith: ".connectedSortable",
					handle: 'td.dd-holder',
					items: 'tr',
					update: function(event, ui) { 
					
						update_fields_positions()
					
					}
				});
				
				//.disableSelection()
		
	});

})(jQuery);