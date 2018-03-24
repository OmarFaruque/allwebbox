/*
* All Webbox Front JS
*/
jQuery(document).ready(function($){
		 /*
	 * Choosen Select 
	 */
	 if($('.chosen').length){
	 	$('.chosen').chosen();
	 }

	 $('.datepicker').datepicker({
	 	dateFormat: "yy-mm-dd",
	 	yearRange: "-120:+0",
	 	changeMonth: true,
      	changeYear: true
	 });

	  /*
	 * Checkbox required funtion
	 */
	 /*$(document.body).on('click', 'div#entryFormAllWebBox input[type="submit"]', function(){
	 	  var checked = $("div#entryFormAllWebBox input[type=checkbox].required:checked").length;
	 	  if(!checked) {
	        alert("You must check at least one checkbox.");
	        return false;
	      }
	 });*/



	$('label.brand input[type="checkbox"]').change(function(){
	    if($('label.brand input[type="checkbox"]').is(':checked')) {
	        $('label.brand input[type="checkbox"]').removeAttr('required');
	    }
	    else {
	        $('label.brand input[type="checkbox"]').attr('required', 'required');
	    }
	});

	$(document.body).on('change', 'label.questom.required input[type="checkbox"]', function(){
		if($(this).is(':checked')){
			$(this).closest('div.form-group').find('label.questom.required input[type="checkbox"]').removeAttr('required');
		}else{
			$(this).closest('div.form-group').find('label.questom.required input[type="checkbox"]').attr('required', 'required');
		}
	});



	






}); // End Document Ready

