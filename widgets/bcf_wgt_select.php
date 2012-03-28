<?php
/**
 * @package bcf_wgt
 * @author G100g
 * @version 0.2
 */

/*
	
	Select Field

*/

class bcf_select extends bcf_text {


	
	/**
		
		Option Init
		
		Eseguita dalla pagina delle opzioni
		
	**/
	
	public static function option_init() {	
	
?>
		<div id="select_options">
			<form>	
				<p>Label: <input type="text" name="option_label"> Value: <input type="text" name="option_value"> <a id="add_select_option" href="#">Add Option</a></p>
			</form>		
			
			<ul>
<!--				<li>Pomodori (0)<a href="#">remove</a></li> -->
			</ul>
			
		</div>
		
		<style>
			#select_options {
				width: 300px;
			}
				#select_options input[name="option_label"] {
					width: 110px;
				}
				#select_options input[name="option_value"] {
					width: 40px;
				}
				
				#select_options ul li {
					padding: 5px;
					border-bottom: 1px solid #CCC;
					cursor: move;
				}
				
					#select_options ul li a{
						float: right;
						font-size: .9em;
					}
				
		</style>
		
		<script>
		(function ($) {
		
			var $input = $('input[name="nf_type_options"]');
			
			function init_options() {
			
				var options = $input.val();
				
				if (options != '') {
					var options = options.split(":##:");
					var $ul = $('#select_options ul');					
					
					$.each(options, function (i, e) {
					
						var option = e.split("[::]");						
						$ul.append('<li rel="'+option[0]+'"><span>'+option[1]+'</span> ('+option[0]+')<a href="#">remove</a></li>');

					
					});
				
				}
			
			}
			
			function update_input() {
			
				
				var new_value = '';
				var sep = '';
				$('#select_options ul li').each(function (i, e) {
								
					var $li = $(e);
					//console.log(e);
					new_value += sep + $li.attr('rel') + "[::]" +$li.find('span').text();
					sep = ':##:';					
				
				});
				
				$input.val(new_value);
			
			}
		
			function add_option() {
			
				var option_label = $('#select_options form input[name="option_label"]').val();
				var option_value = $('#select_options form input[name="option_value"]').val();
				
				if (option_label != '' && option_value != "") {
				
					var $ul = $('#select_options ul');					
					$ul.append('<li rel="'+option_value+'"><span>'+option_label+'</span> ('+option_value+')<a href="#">remove</a></li>');
					update_input();
				
				} else {
					alert("You have to insert a Label and a Value");
				}
			
			}
			
			function remove_option($option) {
			
				$option.remove();
				update_input();
			}
		
			//console.log($('input[name="nf_type_options"]'));
			
			$( "#select_options ul" ).sortable({
				
				update: function () {
					update_input();
				}
			
			});
			$( "#select_options ul" ).disableSelection();
		
		
			$('#select_options #add_select_option').click(function () {
				add_option();
				return false;
			
			});
			
			init_options();
			
			$('#select_options ul li a').click(function () {
				remove_option($(this).parent());
				return false;
			});
			
			$('#select_options form input[name="option_label"], #select_options form input[name="option_value"]').keyup(function (event) {
				if (event.which == 13) {
					$('#select_options #add_select_option').click();
				}			
			});
		
			
		
		})(jQuery);
		
		
		</script>
<?php		

	}
	
	public function _get_options($field, $value = "") {				

		$values = explode(":##:",$field->type_options);
		//Applico il filtro	
	
		//echo ".... " . $field->type . "....<br>";
//		echo get_class($this);		

		$widget_name = get_class($this);

		$values = apply_filters($widget_name, $values, $value);
		//Applico il filtro specifico al campo		
		$values = apply_filters($widget_name.'_'.$field->cf_name, $values, $value);
		
		return $values;
		
	}
		
	public function _select_values($values, $selected_value = null) {
	
		if (!is_array($selected_value)) $selected_value = array($selected_value);

		foreach($values as $value) :
		
			if (strpos($value, "[::]")) {
				$value = explode("[::]", $value);
			} else {
				$value = array($value, $value);
			}
?>		
			<option value="<?php echo htmlspecialchars($value[0]); ?>"<?php echo (in_array($value[0], $selected_value)  ? 'selected="selected"' : ''  );?>><?php echo $value[1]; ?></option>
<?php		
		endforeach;
	
	}	
		
	public function show_field($field, $id, $name, $class, $active, $value) {
			
		$value = ($value !== false ? htmlentities($value, null, get_option('blog_charset')) : '');
			
?>
		<select id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="<?php echo $id. " " .$class; ?>"<?php echo ( !$active ? ' disabled="disabled"' : ''  ); ?>>
			<?php $this->_select_values($this->_get_options($field), $value); ?>
		</select>
<?php						
			
	}

}