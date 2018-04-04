/**
* Admin Script
*/
function adds(type) {	
	jQuery(".options_answer").hide();	
	jQuery(".questypes").hide();
	var foo = document.getElementById("quest_area");
	jQuery(".place_questions").hide();
 
	for(var i=1;i<=type;i++) {
		 
		jQuery("#place_questions_"+i).show();

	}
}

function showAnsType(id, parent){
       jQuery("#place_questions_"+parent+" > .questypes").hide();
	    jQuery("#place_questions_"+parent+" >.options_answer").hide();	
	if(id==3 || id==4){
	 
		jQuery("#place_questions_"+parent+" > #questypes_"+id).show();
	 
	}
	return;	
}


function showOptionAnswers(id,parent){
	 jQuery("#place_questions_"+parent+" > .options_answer").hide();	 
	   for(var i=1;i<=id;i++) {	 
		jQuery("#place_questions_"+parent+" > #options_answer_"+i).show();
	}
}





jQuery(document).ready(function($){
	//Create from content toggle
	//$('div#basic_questions').addClass('active');
	//$('div#basic_questions').closest('.sectionAllow').addClass('active');
	//$('a#visiableBasicQuestion').addClass('active');
	$(document.body).on('click', '.visiableSection', function(){
		$(this).closest('.form-group').next('.allwebContentBdy').toggleClass('active');
		$(this).closest('.sectionAllow').toggleClass('active');
		$(this).toggleClass('active');
	});

	// Color picker length
	if($('.colorpicker').length){
		$('.colorpicker').wpColorPicker();	
	}
	/*
    * Date Picker
    */
     $('.datepicker').datepicker({
	 	dateFormat: "yy-mm-dd",
	 	yearRange: "-120:+0",
	 	changeMonth: true,
      	changeYear: true
	 });
    

    // Image Uploder
      var mediaUploader;
	 
	  $(document.body).on('click', '#fbImage, div#modiF_bg_img a', function(e) {
		e.preventDefault();
		// If the uploader object has already been created, reopen the dialog
		  if (mediaUploader) {
		  mediaUploader.open();
		  return;
		}
		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
		  title: 'Choose Image',
		  button: {
		  text: 'Choose Image'
		}, multiple: false });
	 
		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function() {
		  attachment = mediaUploader.state().get('selection').first().toJSON();
		  $('#backgroundImg, #lnd_bgImg').val(attachment.id);
		  $('.imgPreviewbImage, #modiF_bg_img').html('<div class="pimg"><img src="'+attachment.url+'"/></div>')
		  if($('#modiF_bg_img').length){
		  	$('#modiF_bg_img').append('<div class="dlt"><span alt="f153" class="dashicons dashicons-dismiss"></span></div>');	
		  }
		  
		});
		// Open the uploader dialog
		mediaUploader.open();
	  });
	

	  /*
	  * Delete image from exsting entry form from email 
	  */
	  $(document.body).on('click', 'div#modiF_bg_img .dlt', function(){
	  	$('#modiF_bg_img').html('<a class="text-center" href="#">Form Background Image</a>');
	  });


	/**
	* Shortable link
	*/
	 $( "#sortable1, #sortable2, #sortable3, #sortable4" ).sortable({
      placeholder: "ui-state-highlight"
     });
	 $( "#sortable1, #sortable2, #sortable3, #sortable4" ).disableSelection();



	 /*
	 * Toggle question Edit field 
	 */
	  $(document.body).on('click', 'div#basic_questions .ui-sortable li .actionbutton .edit, div#personalized_questions li .actionbutton .edit', function(){
	  	$(this).closest('.actionbutton').next('.editablefield').slideToggle(300);
	  });


	  /*
	  * Add new options
	  */
	  $(document.body).on('click', 'a.addnewOption', function(e){
	  	e.preventDefault();
	  	$input = '';
	  	if(!$(this).hasClass('brand')){
	  		$input += '<input type="text" style="margin-right:5px;" class="form-control" name="edit_options[]" value="">';
	  	}else{
	  		$input += '<input type="text" style="margin-right:5px;" class="form-control" name="brand_options[]" value="">';
	  	}
	  	$newOption = '<span class="inputF">'+ $input
	  				+'<span alt="f153" class="dashicons dashicons-dismiss"></span>'
	  				+'</span>';
	  	$(this).prev('.qoptionsList').append($newOption);
	  });


	  /**
	  * Delete Options
	  */
	  $(document.body).on('click', 'span.inputF span', function(){
	  	$('.awbox-spinner').show();
	  	$rowId = $(this).prev('input').data('option_row_id');
	  	$this = $(this);
	  	if (typeof $rowId != 'undefined'){
		  	$.ajax({
				type:'POST', 
	            //dataType: "json",
	            url: webbox,
	            data:
	            {
	                'action'    	: 'deleteOptions',
	                'row_id'    	: $rowId
	            },success:function(data){
	            		if(data == 'success'){
	            			$this.closest('span.inputF').remove();	
	            			$('.awbox-spinner').hide();
	            		}
	            }
			}); // End Ajsx
	   	}else{
	  		$this.closest('span.inputF').remove();	
	  		$('.awbox-spinner').hide();
	  	}
	  });



	 /*
	 * Ajax Function
	 */
	 /*
	 * Delete Custome Question
	 */

	 $(document.body).on('click', 'div#personalized_questions li .delete', function(){

	 	$question = $(this).closest('div.actionbutton').prev('div').find('input').val();
	 	$thisLI = $(this).closest('li'); 

	 	$.ajax({
			type:'POST', 
            //dataType: "json",
            url: webbox,
            data:
            {
                'action'    	: 'deleteCustomQuestion',
                'question'    	: $question
            },success:function(data){
            		if(data == 'Success'){
            			$thisLI.remove();	
            		}

            }
		});
	 }); // End Ajax Delete Function 


	 $(document.body).on('click', 'button.updateOptions', function(e){
	 	e.preventDefault();
	 	$this = $(this);
	 	$('.awbox-spinner').show();
	 	$groupdiv 	= $(this).closest('.form-group'); 
	 	$type 		= $groupdiv.find('select').val();
	 	$entry_id 	= $groupdiv.data('entry_row_id');
	 	$required 	= ($groupdiv.find('input[name="updateRequred"]').is(':checked'))?1:0;
	 	$formid 	= $('input[name="edit"]').val();
	 	$options 	= [];
	 	$groupdiv.find('input[name="edit_options[]"]').each(function(index){
	 		$newArray = new Array();
	 		$thisoVal = $(this).val();
	 		$optionID = $(this).data('option_row_id');
	 		$newArray[0] = $optionID;
	 		$newArray[1] = $thisoVal;
	 		$options.push($newArray);
	 	});
	 	console.log($options);

	 	$.ajax({
			type:'POST', 
            //dataType: "json",
            url: webbox,
            data:
            {
                'action'    	: 'updateCustomQuestion',
                'type'    		: $type,
                'entry_id' 		: $entry_id,
                'required' 		: $required,
                'options' 		: $options,
                'formid' 		: $formid
            },success:function(data){
            		console.log(data);
            		if(data == 'success'){
            			$this.next('.ajaxSuccess').html('<span class="success"><b>Update Success</b></span>');
            		}else{
            			$this.next('.ajaxSuccess').html('<span class="fail"><b>Update Fail</b></span>');
            		}
            		$('.awbox-spinner').hide();
            }
		});
	 }); // End Question Update function 




	 /*
	 * Delete Journey
	 */
	 $(document.body).on('click', 'div#allJourney a.journeyDelete', function(e){
	 	e.preventDefault();
	 	if (confirm('Are you sure you want to delete this thing into the database?')){
	 		$('.awbox-spinner').show();
	 		$thisTr = $(this).closest('tr'); 
	 		$id = $(this).data('id');
	 		$.ajax({
	 			type:'POST', 
	            //dataType: "json",
	            url: webbox,
	            data:
	            {
	                'action'    	: 'journeyDelete',
	                'id'    		: $id
	            },success:function(data){
	            		console.log(data);
	            		if(data == 'success'){
	            			$thisTr.remove();
	            		}
	            		$('.awbox-spinner').hide();
	            }
	 		}); //End Ajax
	 	}
	 }); // End Journey Delete function


	 /*
	 * Add option add button if select / multiselect type
	 */ 
	 $(document.body).on('change', 'select[name="editAnswerType"]', function(){

	 	$thisVal = $(this).val();
	 	$btnHTML = '<div class="qoptionsList">Options:<br/><span class="inputF"><input type="text" class="form-control" style="margin-right:5px;" name="edit_options[]" value=""><span alt="f153" class="dashicons dashicons-dismiss"></span></span></div>'
	 				+'<a class="addnewOption" href="#"><span alt="f502" class="dashicons dashicons-plus-alt"></span></a>';
	 	if($thisVal == 3 || $thisVal == 4){
	 		if(!$(this).closest('.form-group').find('.qoptionsList').length){
	 			$($btnHTML).insertBefore($(this).closest('.form-group').find('button.updateOptions'));		
	 		}
	 	}else{
	 		if($(this).closest('.form-group').find('.qoptionsList').length){
	 			$(this).closest('.form-group').find('.qoptionsList').remove();
	 			$(this).closest('.form-group').find('.addnewOption').remove();
	 		}
	 	}
	 });

	 /*Functionaty for email template */
	 if($('.email_templates, #formjourneyncontent').length){
	 	$('.multipleSelect').fastselect();
	 	//bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });	
	 	 tinymce.init({
	        selector: "textarea.tinymce",
	        theme: "modern",
	        height: 300,
	        plugins: [
	             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	             "save table contextmenu directionality emoticons template paste textcolor"
	       ],
	       toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
	       style_formats: [
	            {title: 'Bold text', inline: 'b'},
	            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
	            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
	            {title: 'Example 1', inline: 'span', classes: 'example1'},
	            {title: 'Example 2', inline: 'span', classes: 'example2'},
	            {title: 'Table styles'},
	            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
	        ]
	    }); 
	 }
	 if($('#sendSelectedEmail, .email_templates_single, .single_brand').length){
	 	//bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });	
	 	 tinymce.init({
	        selector: "textarea.tinymce",
	        theme: "modern",
	        height: 300,
	        plugins: [
	             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	             "save table contextmenu directionality emoticons template paste textcolor"
	       ],
	       toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
	       style_formats: [
	            {title: 'Bold text', inline: 'b'},
	            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
	            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
	            {title: 'Example 1', inline: 'span', classes: 'example1'},
	            {title: 'Example 2', inline: 'span', classes: 'example2'},
	            {title: 'Table styles'},
	            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
	        ]
	    }); 

	 }

	 /*
	 * add new email template
	 */
	 $(document.body).on('click', 'div#addnewTemplate span', function(){
		var usrPrameters 	= jQuery.parseJSON(entry_clmn);
		var alletemp 		= jQuery.parseJSON(emalTems);
		var prem 			= '';
		var templates 		= '';

		$.each(alletemp, function(k, v) {
			$vrSelected = (sltTemp == k)?'selected':'';
		    templates += '<option '+$vrSelected+' value="'+k+'">'+v+'</option>';
		});
		
		for(s of usrPrameters){
				prem +='<li data-param="'+s+'">['+s+']</li>';
		}
		var lengh = $('.newTemplate').length;
	 	$newTemplate = '<div class="newTemplate">'

	 					+'<div class="tempDelete">'
              			+'<span alt="f158" class="dashicons dashicons-no"></span>'
          				+'</div>'
          				+'<div class="form-group">'
          				+'<span class="noteS"><small><i>Note: Use any one from Email Date & Email time</i></small></span>'
          				+'<div class="three-half left">'
              			+'<label for="journey_date">Email sent date <small><i>(Date)</i></small></label>'
              			+'<input type="text" style="padding:3.5px;" class="datepicker form-control p5" name="j_date[]" value="">'
          				+'</div>'

          				+'<div class="three-half left middle">'
            			
              			+'<label for="journey_time">Email sent after <small><i>(time)</i></small></label>'
              			+'<input type="number" id="journey_time" name="j_time[]" value="" class="form-control p5">'
            			
          				+'</div>'

          				+'<div class="three-half right">'
            			
              			+'<label for="time_unit">Each</label><br>'
              			+'<label><input type="radio" value="month" name="time_unit['+lengh+']" />Month</label>&nbsp;&nbsp;'
              			+'<label><input type="radio" value="week" name="time_unit['+lengh+']" />Week</label>&nbsp;&nbsp;'
              			+'<label><input type="radio" value="day" name="time_unit['+lengh+']" />Day</label>&nbsp;&nbsp;'
              			+'<label><input type="radio" value="hour" name="time_unit['+lengh+']" />Hour</label>'
            			+'</div>'
          				+'</div>'

          				+'<div class="subject full">'
            			+'<div class="form-group">'
              			+'<label for="j_subject">Subject</label>'
              			+'<input type="text" value="" class="form-control" name="j_subject[]" />'
            			+'</div>'
          				+'</div>'

          				+' <div class="userParemeters" id="campaignPrameter">'
            			+'<label>User Parameters <span alt="f139" class="dashicons dashicons-arrow-right"></span></label>'
            			+'<ul class="usParementslist hidden">'+prem+'</ul>'
          				+'</div>'


          				+'<div class="form-group">'
          				+'<div class="pull-right">'
						+'<div class="inlinelabel">'
						+'<label for="loadExistingTemplate"></label>'
						+'<select id="loadExistingTemplate" name="loadTemplate">'
						+'<option value="">Load Template...</option>'
						+ templates
						+'</select>'
						+'</div>'
						+'</div>'




      					+'<div class="visualTextArea"><textarea style="width:100%; min-height:120px;" name="j_emails[]" class="form-control tinymce">Hi [lastname],&nbsp;<div><br></div><div>Regards,</div></textarea></div>'
      					+'</div>' // End form-group for textarea

    					+'</div>';
    	var appendT = $($newTemplate).insertBefore($(this).closest('#addnewTemplate'));
    	$('.datepicker').datepicker({
	 		dateFormat: "yy-mm-dd",
	 		changeMonth: true,
      		changeYear: true
		});
    	
    	//new nicEditor().panelInstance(appendT[0].lastChild); 

    		tinymce.init({
	        selector: "textarea.tinymce",
	        theme: "modern",
	        height: 300,
	        plugins: [
	             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	             "save table contextmenu directionality emoticons template paste textcolor"
	       ],
	       toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
	       style_formats: [
	            {title: 'Bold text', inline: 'b'},
	            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
	            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
	            {title: 'Example 1', inline: 'span', classes: 'example1'},
	            {title: 'Example 2', inline: 'span', classes: 'example2'},
	            {title: 'Table styles'},
	            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
	        ]
	    }); //End tinymce 
    	//removeInstance
    	//console.log(appendT[0]);
	 });





	/*
	 * Add new Sub-Objective Start
	*/
	 $(document.body).on('click', 'div.addnewSubObjective span', function(){
	 	$newTemplate = '<div class="newTemplate newObjectiveSub">'
	 					+ '<form action="" method="post" accept-charset="utf-8">'
	 					+ '<div class="deleteSubObj"><span alt="f153" class="dashicons dashicons-dismiss"></span></div>'
	 					+ '<div class="wrap email_campains bgff p20">'
						+ '<div class="innerpageallwebbox">'
						
						// Sub-Campaign Name
						+ '<input type="text" name="sub_obj" class="form-control" value="" placeholder="Sub-Objective..." />'

						+ '<div class="subObjDesc"><textarea class="form-control" style="width:100%;" rows="4" name="sub_desc"></textarea></div>'
						// End Sub-Campaign Name

						+ '<input type="submit" name="scampain_submit" value="Submit" class="button button-primary" />'
						+ '</div>'
						+ '</div>'
						+ '</form>'
    					+ '</div>';
    	$($newTemplate).insertBefore($(this).closest('.addnewSubObjective'));



    	//removeInstance
    	//console.log(appendT[0]);
	 }); // Add new Sub-Objective End




	  /*
	 * Add new Sub-Campaign Start
	 */
	 $(document.body).on('click', 'div.addnewSubCampaign span', function(){
	 	$('.awbox-spinner').show();
		var thisI = $(this);
		var allPagesj 	= jQuery.parseJSON(allPages);
		var alletemp 	= jQuery.parseJSON(emalTems);
		var prPages 	= '';
		var templates 	= '';
		var id 			= $(this).closest('.newTemplate.emailCampaign').find('select[name="campaignName"]').val();

		
		
		$.each(allPagesj, function(k, v) {
		    prPages += '<option value="'+k+'">'+v+'</option>';
		});
		$.each(alletemp, function(k, v) {
			$vrSelected = (sltTemp == k)?'selected':'';
		    templates += '<option '+$vrSelected+' value="'+k+'">'+v+'</option>';
		});



		var lengh = $('.newTemplate').length;


				$.ajax({
				type:'POST', 
			    url: webbox,
			    dataType: 'json',
			    data:
			        {
			        'action'	: 'selectRltSubObject',
			    	'id'		: id
			        },success:function(data){
						$newTemplate = '<div class="newTemplate newSubCampaign">'
	 					+ '<form action="" method="post" accept-charset="utf-8">'
	 					+ '<div class="deleteSubCamp"><span alt="f153" class="dashicons dashicons-dismiss"></span></div>'
	 					+ '<div class="wrap email_campains bgff p20">'
						+ '<div class="innerpageallwebbox">'
						
						// Sub-Campaign Name
						+ '<input type="text" name="scmp_name" class="form-control" value="" placeholder="Sub-Campaign Name..." />'

						+'<div class="form-group selectSubCampaign">'
			       		+'<label for="subcampaignName">Sub Objective</label>'
			          	+'<select class="form-control" multiple name="sub_obj">';
				       	$.each(data, function(k, v) {
				       		$newTemplate +='<option value="'+v.id+'">'+v.sub_obj+'</option>';	
				       	});
				       	$newTemplate +='</select></div>'
						// End Sub-Campaign Name 
						+ '<div class="form-group">'
						+ '<div class="halfDiv">'

						+ '<div class="smspushAea"><textarea  name="sub_camdesc" rows="6" class="form-control"></textarea></div>'
						+ '</div>'
						+ '<br>'
						+ '<input type="submit" name="scampain_submit" value="Submit" class="button button-primary" />'
						+ '</div>'
						+ '</div>'
						+ '</form>'
    					+ '</div>';

						$($newTemplate).insertBefore(thisI.closest('.addnewSubCampaign'));




			        	
			        	
				       	$('.awbox-spinner').hide();
			        }
    			}); // Ajax



	 



    	
    	$('.datepicker').datepicker({
	 		dateFormat: "yy-mm-dd",
	 		changeMonth: true,
      		changeYear: true
		});
    	
    	//new nicEditor().panelInstance(appendT[0].lastChild); 

    		tinymce.init({
	        selector: "textarea.tinymce",
	        theme: "modern",
	        height: 300,
	        plugins: [
	             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	             "save table contextmenu directionality emoticons template paste textcolor"
	       ],
	       toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
	       style_formats: [
	            {title: 'Bold text', inline: 'b'},
	            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
	            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
	            {title: 'Example 1', inline: 'span', classes: 'example1'},
	            {title: 'Example 2', inline: 'span', classes: 'example2'},
	            {title: 'Table styles'},
	            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
	        ]
	    }); //End tinymce 
    	//removeInstance
    	//console.log(appendT[0]);
	 }); // Add new Sub-Campaign End
	 


	 	 /*
	 * add new email template
	 */
	 $(document.body).on('click', 'div#addnewTemplateMsg span', function(){
		var usrPrameters = jQuery.parseJSON(entry_clmn);
		var prem = '';
		for(s of usrPrameters){
				prem +='<li data-param="'+s+'">['+s+']</li>';
		}
		var lengh = $('.newTemplate').length;
	 	$newTemplate = '<div class="newTemplate">'

	 					+'<div class="tempDelete">'
              			+'<span alt="f158" class="dashicons dashicons-no"></span>'
          				+'</div>'

      					+'<textarea style="width:100%; min-height:120px;" name="msg[]" class="form-control tinymce">Hi [lastname], Regards</textarea>'

    					+'</div>';
    	var appendT = $($newTemplate).insertBefore($(this).closest('#addnewTemplateMsg'));
    	$('.datepicker').datepicker({
	 		dateFormat: "yy-mm-dd",
	 		changeMonth: true,
      		changeYear: true
		});
    
    	//new nicEditor().panelInstance(appendT[0].lastChild); 
    	   	tinymce.init({
	        selector: "textarea.tinymce",
	        theme: "modern",
	        height: 300,
	        plugins: [
	             "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
	             "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
	             "save table contextmenu directionality emoticons template paste textcolor"
	       ],
	       toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
	       style_formats: [
	            {title: 'Bold text', inline: 'b'},
	            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
	            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
	            {title: 'Example 1', inline: 'span', classes: 'example1'},
	            {title: 'Example 2', inline: 'span', classes: 'example2'},
	            {title: 'Table styles'},
	            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
	        ]
	    }); //End tinymce 
    	
	 });
	 

	 /*
	 * Journey Tabs
	 */ 
	 $(document.body).on('click', '.emalitoptabs ul li a', function(e){
	 	e.preventDefault();
	 	if(!$(this).hasClass('active')){
	 		$('.emalitoptabs ul li a').removeClass('active');
	 		$href = $(this).attr('href');
	 		$(this).addClass('active');
	 		$('.section').addClass('hidden').removeClass('active');
	 		$($href).addClass('active').removeClass('hidden');
	 	}
	 });

	 /*
	 * journey default tab if has hash
	 */
	 	if(window.location.hash) {
      		var hash = window.location.hash; //Puts hash in variable, and removes the # character
      		$('.emalitoptabs ul li a').removeClass('active');
      		$('a[href="'+hash+'"]').addClass('active');
      		$('.section').addClass('hidden').removeClass('active');
      		$(hash).addClass('active').removeClass('hidden');
  		}

	 /*
	 * Delete Journey email template
	 */
	 $(document.body).on('click', '.newTemplate .tempDelete span', function(){
	 	if (confirm('Are you sure you want to delete this?')){
	 		$(this).closest('.newTemplate').remove();
	 	}
	 });

	 /*
	 * jQuery Data Shortable
	 */
	 if($('#formList, #searchReasult, #formBrand').length){
	 	$('#formList, #searchReasult, #formBrand').DataTable();	
	 }
	 

	 /*
	 * Delete Form
	 */
	 $(document.body).on('click', '#formList a.deleteMainForm', function(e){
	 	e.preventDefault();
	 	if (confirm('Are you sure you want to delete this thing into the database?')){
	 		$('.awbox-spinner').show();
	 		$thisTr = $(this).closest('tr'); 
	 		$tId = $(this).data('id');
	 		$.ajax({
	 			type:'POST', 
	            //dataType: "json",
	            url: webbox,
	            data:
	            {
	                'action'    	: 'formDelete',
	                'id'    		: $tId
	            },success:function(data){
	            		//console.log(data);
	            		if(data == 'success'){
	            			$thisTr.remove();
	            		}
	            		$('.awbox-spinner').hide();
	            }
	 		}); //End Ajax
	 	}
	 });



	 /*
	 * CRM page function 
	 */
	 function adds(type) {
 		 
	$(".options_answer").hide();	
	$(".questypes").hide();
	var foo = document.getElementById("quest_area");
	$(".place_questions").hide();
 
	for(var i=1;i<=type;i++) {
		 
		$("#place_questions_"+i).show();

		}
	}

	function showAnsType(id, parent){
	       $("#place_questions_"+parent+" > .questypes").hide();
		    $("#place_questions_"+parent+" >.options_answer").hide();	
		if(id==3 || id==4){
		 
			$("#place_questions_"+parent+" > #questypes_"+id).show();
		 
		}
		return;	
	}


	function showOptionAnswers(id,parent){
		 $("#place_questions_"+parent+" > .options_answer").hide();	 
		   for(var i=1;i<=id;i++) {	 
			$("#place_questions_"+parent+" > #options_answer_"+i).show();
		}
	}



    /*
    * Delete Form from admin
    */
    $(document.body).on('click', 'table#searchReasult a.delete_entry', function(e){
    	e.preventDefault();
    	if(confirm('Are you sure you want to delete this thing into the database?')){
    		$('.awbox-spinner').show();
    		$id 		= $(this).data('id');
    		$thisTr 	= $(this).closest('tr'); 

    		$.ajax({
				type:'POST', 
	            //dataType: "json",
	            url: webbox,
	            data:
	            {
	                'action'    	: 'formEntryDelete',
	                'id'    		: $id
	            },success:function(data){
	            		//console.log(data);
	            		if(data == 'success'){
	            			$thisTr.remove();
	            		}
	            		$('.awbox-spinner').hide();
	            }
    		});
    	}
    }); // End Delete Entry 


    /*
    * Delete value if no entry check box active
    */
    $(document.body).on('click', 'input.noansweractive', function(){
    	if($(this).is(':checked')){
    		$(this).closest('label').prev('input').val('');
    	}
    });


    /*
    * CRM page if text input key up then uncheck no-answer
    */
    $(document.body).on('keyup', 'div#crmWrap input[type="text"]', function(){
    	$val = $(this).val();
    	if($val != ''){
    		$(this).next('label').find('input').prop('checked', false);
    	}else{
    		$(this).next('label').find('input').prop('checked', true);
    	}
    });

    /*
    * Email Campaign change new / existing
    */
    $(document.body).on('change', 'select#campainfor', function(){
    	$val 		= $(this).val();
    	$url 		= window.location.href;
    	$splt		= $url.substring($url.indexOf('?') + 1).split('&');
    	
    	$splt 		= $splt.filter( function( item, index, inputArray ) {
           return inputArray.indexOf(item) == index;
    	});
    	$htmlext 	= '';
    	$newEmal = [];
    	//console.log($splt);
    	for($i=0; $splt.length > $i; $i++){
    		if($splt[$i].match("^send_mails")){
    			$smail = $splt[$i].split('=');
    			$cMail = $smail[1].replace('%40', '@');
    			$newEmal.push($cMail);	
    		}
    	}
    	$newEmal 	= $newEmal.filter( function( item, index, inputArray ) {
           return inputArray.indexOf(item) == index;
    	});
    	for($k=0; $newEmal.length > $k; $k++){
    		$htmlext	+='<input type="hidden" name="sendto[]" value="'+$newEmal[$k]+'">'
			         	+'<span class="semail">'+$newEmal[$k]+'</span>';
    	}

    	if($val == 'new'){
    		$html = '<div class="sEmdiv"><input type="text" name="sendto[]" value=""></div>'
    		+'<div id="addnewEfield"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>';
    		$('#innersendTo').html($html);
    	}else{
    		$('#innersendTo').html($htmlext);
    	}
    });

    /*
    * Add new Email to Campaign
    */
    $(document.body).on('click', 'div#addnewEfield', function(){
    	$insElement = '<div class="sEmdiv"><input type="text" name="sendto[]" value=""><span alt="f158" class="deleteThisEmail dashicons dashicons-no"></span></div>';
    	$($insElement).insertBefore($(this));
    });

    /*
    * Delete Email Camplatin New Email
    */
    $(document.body).on('click', 'span.deleteThisEmail', function(){
    	$(this).closest('.sEmdiv').remove();
    });


    /*
    * Load Email Template
    */
    $(document.body).on('change', 'select#loadExistingTemplate', function(){
    	$('.awbox-spinner').show();
    	var val 	= $(this).val();
    	var thisI = $(this);
    	if(val){
    		$.ajax({
					type:'POST', 
			        //dataType: 'json',
			        url: webbox,
			        data:
			            {
			                'action'	: 'loadTemplateFunction',
			                'val'		: val
			            },success:function(data){
			            			$('.awbox-spinner').hide();
			            			thisI.closest('.form-group').find('.visualTextArea').find('textarea.tinymce').val(data);
			            			thisI.closest('.form-group').find('.visualTextArea').find('iframe').contents().find('body').html(data);
			            }

    			}); // Ajax
    	}
    	
    });


    /*
    * Edit Entry from Admin in single entry details page
    */
    $(document.body).on('click', 'div#detailsInfor table tr td span.edit', function(){
    	if($(this).closest('td').find('input').hasClass('hidden')){
	    	$(this).closest('td').find('span.details').hide();
	    	$(this).closest('td').find('input').removeClass('hidden').addClass('active');
	    	$(this).removeClass('dashicons-edit').addClass('dashicons-no');
    	}else{
    		$(this).closest('td').find('span.details').show();
	    	$(this).closest('td').find('input').removeClass('active').addClass('hidden');
	    	$(this).removeClass('dashicons-no').addClass('dashicons-edit');
    	}
    });


    /*
    * Insert parameter to journey body
    */

     $(document.body).on('click', 'ul.usParementslist li', function(){
	 	$(this).OneClickSelect();
	 }); //End insert pa


	/*
	* Select for Copy function
	*/
	$.fn.OneClickSelect = function () {
	  return $(this).on('click', function () {

	    // In here, "this" is the element

	    var range, selection;

	    if (window.getSelection) {
	      selection = window.getSelection();
	      range = document.createRange();
	      range.selectNodeContents(this);
	      selection.removeAllRanges();
	      selection.addRange(range);
	    } else if (document.body.createTextRange) {
	      range = document.body.createTextRange();
	      range.moveToElementText(this);
	      range.select();
	    }
	  });
	};


	/*
	* Change nicEditor to Normal textarea if not Email type
	*/
	$(document.body).on('change', 'div.single_brand select[name="msgtype"]', function(){
		var msgType = $(this).val();
		if(msgType != 'email'){
			$('#newjourney .newTemplate').each(function(){
				var value = $(this).find('.nicEdit-main').text();
				$(this).find('.nicEdit-main').text(value);
				//console.log(textarea_id);
				//area.removeInstance(textarea_id); 
	    		//removeInstance
			});
		}
	});

	/*
	* on load sms if type not email
	*/


	if($('.single_brand').length){
		var msgType = $('div.single_brand select[name="msgtype"]').val();
		if(msgType != 'email'){
			$('.newTemplate').each(function(){
				$(this).find('.nicEdit-main').addClass('omar');
				var value = '<div>' + $(this).find('textarea').val() + '</div>';
				//console.log('value: ' + $(value).text());
				$(this).find('textarea').val($(value).text());
				//console.log(textarea_id);
				//area.removeInstance(textarea_id); 
	    		//removeInstance
			});
		}
	}


	/*
	* Brand Icon Upload 
	*/
	  var mediaUploader;
	 
	  $('#iconBanner > div.iconInner').click(function(e) {
		e.preventDefault();
		// If the uploader object has already been created, reopen the dialog
		  if (mediaUploader) {
		  mediaUploader.open();
		  return;
		}
		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
		  title: 'Choose Brand Icon',
		  button: {
		  text: 'Choose Brand Icon'
		}, multiple: false });
	 
		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function() {
		  attachment = mediaUploader.state().get('selection').first().toJSON();
		  $('#iconBanner > div.iconInner').html('<img src="'+attachment.url+'"/>');
		  $('input#brand_icon').val(attachment.id);
		  console.log(attachment);
		});
		// Open the uploader dialog
		mediaUploader.open();
	  }); // End upload functionality


	  /*
	  * CRM Filter show / hide
	  */
	  $(document.body).on('click', 'input[name="crmQry"]', function(){
	  	if($(this).is(':checked')){
	  		$('#filterSectionQry').removeClass('hidden');
	  	}else{
	  		$('#filterSectionQry').addClass('hidden');
	  	}
	  }); // End CRM Filter Show and hide



	  /*
	  * CRM Filter saved details description show & hide
	  */
	  $(document.body).on('click', 'div#savedFilterBody i.fa', function(){
	  	$(this).closest('p').next('.filtrDescription').toggleClass('hidden');
	  	$(this).toggleClass('fa-caret-down');
	  	$(this).toggleClass('fa-caret-up');
	  });

	  /*
	  * Delete Form Background Image from Admin Form 
	  */
	  $(document.body).on('click', 'button#deleteFormBgImage', function(e){
	  	e.preventDefault();
	  	$('.imgPreviewbImage').html('');
	  	$('input#backgroundImg').val('');
	  	$(this).hide();
	  }); // End delete Image


	  /*
	  * Add entry to Journey
	  */
	  $(document.body).on('change', '#searchReasult_wrapper table tr td input[name="send_mails[]"], #searchReasult_wrapper input#selbtn', function(){
	  	var chekedLen = $('#searchReasult_wrapper table tr td input[name="send_mails[]"]:checked').length;
	  	var checkVal = [];
	  	$('#searchReasult_wrapper table tr td input[name="send_mails[]"]:checked').each(function(){
	  		checkVal.push($(this).val());
	  	});
	  	var ckechValJson  = checkVal.join();
	  	
	  	var href = window.location.href;
	  	if(chekedLen > 0){

	  		$('.atttoJourny, .startCamp').remove();
	 		$campHtml = '<div class="startCamp mt20"><a href="'+ href + '&a_action=startcampaign&send_mails=' + ckechValJson +'" class="button button-primary">Start Campaign</a></div>';
	  		$outHtml = '<div class="atttoJourny mt20"><a href="'+href+ '&adto=' + ckechValJson +'" class="button button-primary">Add to Journey</a></div>';
	  		
	  		//$($outHtml).insertAfter($('#searchReasult_wrapper'));
	  		$('div#addToJourneyForm').append($outHtml);
	  		$('div#selectCam .adtoInner').append($campHtml);
	  		$('div#addToJourneyForm, div#selectCam').removeClass('hidden');
	  	}else{
	  		$('.atttoJourny, .startCamp').remove();
	  		$('div#addToJourneyForm, div#selectCam').addClass('hidden');
	  	}
	  });


	  /*
	  * Add to Journey button hit
	  */
	  $(document.body).on('click', '.atttoJourny > a', function(e){
	  	var journVal = $('select#alljourney').val();
	  	e.preventDefault();
	  	if(journVal != ''){
	  		var pUrl 	= $(this).attr('href');
	  		var newUrl 	= pUrl + '&jid=' + journVal;
	  		window.location.replace(newUrl);
	  	}else{
	  		$('select#alljourney').addClass('error');
	  	}
	  	
	  });


	  /*
	  * Each Journey Sliding when Edit 
	  */
	  $(document.body).on('click', '.SlidingP', function(){
	  	$(this).next('.tempNewInner').toggleClass('tmhidden');
	  	$(this).find('span.dashicons').toggleClass('dashicons-arrow-right');
	  	$(this).find('span.dashicons').toggleClass('dashicons-arrow-down');
	  });


	  /*
	  * if check send email also auto select sms-send 
	  */
	  $(document.body).on('click', 'input[name="send_mails[]"]', function(){
	  	if($(this).is(':checked')){
	  		$(this).next('input[type="checkbox"]').prop('checked', true);
	  	}else{
	  		$(this).next('input[type="checkbox"]').prop('checked', false);
	  	}
	  }); //End Send email and select sms also auto

	  /*
	  * Chagne Emal or SMS type in send campaing page
	  */
	  $(document.body).on('change', 'select[name="selectType"]', function(){
	  	if($(this).val() == 'email'){
	  		$('.toEmail, .visualTextArea, .toSms, div.mce-tinymce.mce-container, .esubject.replay, #loadTemplate, textarea.normal, div.form-group.email, .sectionAllow.crm.‍sendEmail').toggleClass('hidden');
	  	}else{
	  		$('.toEmail, .toSms, div.mce-tinymce.mce-container, .esubject.replay, .visualTextArea, #loadTemplate, textarea.normal, div.form-group.email, .sectionAllow.crm.‍sendEmail').toggleClass('hidden');
	  	}
	  });



	 /*
	  * Add not answer question  to custom email for ask new question via email 
	  */
	  $(document.body).on('click', 'input#askQButton', function(e){
	  	var newQ = [];
	  	$('#crmWrap').find('input[type="checkbox"]:checked').each(function(){
	  		var q = $(this).closest('div.col-md-3').children('label:first-child').text();
	  		newQ.push(q);
	  	});
	  	var inVal = newQ.join(';');
	  	$('input[name="asqQsn"]').val(inVal);
	  });


	  /*
	  * show if checkbox is check on style for landing page
	  */
	  $(document.body).on('change', 'input[name="bg_overley_active"]', function(){
	  	if($(this).is(':checked')){
	  		$(this).closest('label').next('.overleyFunctional').removeClass('hidden');
	  	}else{
	  		$(this).closest('label').next('.overleyFunctional').addClass('hidden');
	  	}
	  });


	  /*
	  * User parameter hide / show
	  */
	  $(document.body).on('click', '#campaignPrameter label', function(){
	  	$(this).next('ul').toggleClass('hidden');
	  	$(this).children('span').toggleClass('dashicons-arrow-right');
	  	$(this).children('span').toggleClass('dashicons-arrow-down');
	  	
	  });


	  /*
	  * SMS Campaign data storage
	  */
	  $(document.body).on('submit', 'div.newTemplate.newSubCampaign form', function(e){
	  		$('.awbox-spinner').show();
	  		e.preventDefault();
	  		var thisv 			= $(this);
	  		var exID 			= $(this).data('exid');
	  		var campID 			= $(this).data('campaign');
	  		var obj 			= $(this).closest('.newTemplate.emailCampaign').find('select[name="campaignName"]').val();
	  		var camp_desc 		= $(this).closest('.newTemplate.emailCampaign').find('textarea[name="camp_desc"]').val();
	  		var alldata 		= $(this).serializeArray();
	  		var subCampTitle 	= $(this).find('input[name="scmp_name"]').val(); 
	  		var campainName 	= $(this).closest('.newTemplate.emailCampaign').find('input[name="campain_name"]').val();
			
			var realvalues = new Array();//storing the selected values inside an array
    		thisv.find('select[name="sub_obj"]').each(function(i, selected) {
        		realvalues[i] = $(selected).val();
    		});
    		alldata[alldata.length] = { name: "sub_obj", value: realvalues };
			
	  		console.log(alldata);
			    $.ajax({
						type:'POST', 
			            //dataType: 'json',
			            url: webbox,
			            data:
			            {
			                'action'	: 'storeCampaign',
			                'formdata'	: alldata,
			                'campaign'	: campainName,
			                'exid' 		: exID,
			                'campID' 	: campID,
			                'obj' 		: obj,
			                'camp_desc' : camp_desc
			            },success:function(data){
			            			console.log(data);
			            			$('.awbox-spinner').hide();
			            			if(data != '' && !isNaN(data)){
			            				$('<div class="tempEdit" data-id="'+data+'"><span alt="f464" class="dashicons dashicons-edit"></span></div>').insertAfter($(thisv.closest('.newTemplate.emailCampaign').find('.camtempDelete')));
			            				thisv.closest('.newTemplate.emailCampaign').find('.camtempDelete').attr('data-id', data);
			            			}
			            			thisv.closest('.newTemplate.emailCampaign').find('.SlidingP span.dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
			            			thisv.closest('.tempNewInner').addClass('tmhidden');
			            			thisv.find('.subCamptitle.title').remove();
			            			$('<div class="subCamptitle title"><div class="titleInner"><h4><span alt="f345" class="dashicons dashicons-arrow-right-alt2"></span>&nbsp;'+subCampTitle+'</h4></div></div>').insertAfter($(thisv.find('.deleteSubCamp')));
			            			thisv.closest('.newTemplate.emailCampaign').find('.SlidingP span.title').text(campainName);
			            			thisv.closest('.newTemplate.emailCampaign').find('.SlidingP span.title').removeClass('hidden');
			            			thisv.closest('.newTemplate.emailCampaign').find('.SlidingP input[name="campain_name"]').addClass('hidden');
			            }

    			}); // Ajax
	  }); // End  on('submit', 'div#sendSelectedEmail form', function(e)



	  /*
	  * Campaign Objective Data Storage Start
	  */
	  $(document.body).on('submit', 'div.newTemplate.newObjectiveSub form', function(e){
	  		$('.awbox-spinner').show();
	  		e.preventDefault();
	  		var thisv = $(this);
	  		var exID = $(this).data('exid');
	  		var ObjID = $(this).data('campaign');
	  		var subObj 		= $(this).find('input[name="sub_obj"]').val();
	  		var ob_desc 	= $(this).closest('.newTemplate.newObjectiveW').find('textarea[name="ob_desc"]').val();
	  		var ObjectName 	= $(this).closest('.newTemplate.newObjectiveW').find('input[name="objective_name"]').val();
	  		var sub_desc 	= $(this).find('textarea[name="sub_desc"]').val();	

	  		//console.log('ob_desc:  ' + ob_desc );
			   
			    $.ajax({
						type:'POST', 
			            dataType: 'json',
			            url: webbox,
			            data:
			            {
			                'action'		: 'storeObjective',
			                'sub_obj'		: subObj,
			                'objective_name': ObjectName,
			                'ob_desc' 		: ob_desc,
			                'exid' 			: exID,
			                'ObjID' 		: ObjID,
			                'sub_desc' 		: sub_desc
			            },success:function(data){
			            			console.log(data);
			            			
			            			$('.awbox-spinner').hide();
			            			if(typeof data.obj_id !== 'undefined' && !isNaN(data.obj_id)){
			            				$('<div class="tempEditOjb" data-id="'+data.obj_id+'"><span alt="f464" class="dashicons dashicons-edit"></span></div>').insertAfter($(thisv.closest('.newTemplate.newObjectiveW').find('.objectDelete')));
			            				thisv.closest('.newTemplate.newObjectiveW').find('.objectDelete').attr('data-id', data.obj_id);
			            			}

			            			if(typeof data.subObid !== 'undefined' && !isNaN(data.subObid)){
			            				thisv.find('.deleteSubObj').attr('data-id', data.subObid);
			            			}

			            			thisv.closest('.newTemplate.newObjectiveW').find('.SlidingP span.dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
			            			thisv.closest('.tempNewInner').addClass('tmhidden');
			            			thisv.find('div.subCamptitle.title').remove();
			            			$('<div class="subCamptitle title"><div class="titleInner"><h4><span alt="f345" class="dashicons dashicons-arrow-down-alt2"></span>&nbsp;'+subObj+'</h4></div></div>').insertAfter($(thisv.find('.deleteSubObj')));
			            			thisv.closest('.newTemplate.newObjectiveW').find('.SlidingP span.title').text(ObjectName);
			            			thisv.closest('.newTemplate.newObjectiveW').find('.SlidingP span.title').removeClass('hidden');
			            			thisv.closest('.newTemplate.newObjectiveW').find('.SlidingP input[name="objective_name"]').addClass('hidden');
			            }

    			}); // Ajax
	  }); // End  on('submit', 'div.newTemplate.newObjectiveW form', function(e)


	  /*
	  * Delete Campagin .newTemplate.emailCampaign .camtempDelete
	  */

	  $(document.body).on('click', '.newTemplate.emailCampaign .camtempDelete', function(){
	  	$('.awbox-spinner').show();
	  	var id = $(this).data('id');
	  	console.log('id: ' + id);

	  	var temp = $(this).closest('.newTemplate.emailCampaign');
	  	if(typeof id != 'undefined'){
	  		$.ajax({
				type:'POST', 
			    url: webbox,
			    data:
			        {
			        'action'	: 'campaignDelete',
			    	'id'		: id
			        },success:function(data){
				       	if(data == 'success'){
				       		temp.remove();
				       		$('.awbox-spinner').hide();
			        	}
			        }

    			}); // Ajax
	  	}else{
	  		temp.remove();
	  		$('.awbox-spinner').hide();
	  	}

	  }); // End Delete Campagin




	  /*
	  * Delete Objective .newTemplate.newObjectiveW .objectDelete
	  */

	  $(document.body).on('click', '.newTemplate.newObjectiveW .objectDelete', function(){
	  	$('.awbox-spinner').show();
	  	var id = $(this).data('id');

	  	var temp = $(this).closest('.newTemplate.newObjectiveW');
	  	if(typeof id != 'undefined'){
	  		$.ajax({
				type:'POST', 
			    url: webbox,
			    data:
			        {
			        'action'	: 'objectiveDelete',
			    	'id'		: id
			        },success:function(data){
				       	if(data == 'success'){
				       		temp.remove();
				       		$('.awbox-spinner').hide();
			        	}
			        }

    			}); // Ajax
	  	}else{
	  		temp.remove();
	  		$('.awbox-spinner').hide();
	  	}

	  }); // End Delete Campagin


	  /*
	  * Delete Sub-Campaign deleteSubCamp
	  */
	  $(document.body).on('click', '.newTemplate.newSubCampaign .deleteSubCamp', function(){
	  	$('.awbox-spinner').show();
	  	var id 		= $(this).data('id');
	  	var thisH 	= $(this).closest('.newTemplate.newSubCampaign');
	  		  if(typeof id != 'undefined')
	  		  {
	  		  $.ajax({
				type:'POST', 
			    url: webbox,
			    data:
			        {
			        'action'	: 'subCampaignDelete',
			    	'id'		: id
			        },success:function(data){
				       	if(data == 'success'){
				       		thisH.remove();
				       		$('.awbox-spinner').hide();
			        	}
			        }
    			}); // Ajax
	  		} // if id exist
	  		else
	  		{
	  			thisH.remove();
				$('.awbox-spinner').hide();	
	  		}
	  });

	  // End delete Sub-Campaign deleteSubCamp


	  	  /*
	  * Delete Sub-Objective deleteSubObj
	  */
	  $(document.body).on('click', '.newTemplate.newObjectiveSub .deleteSubObj', function(){
	  	$('.awbox-spinner').show();
	  	var id 		= $(this).data('id');
	  	var thisH 	= $(this).closest('.newTemplate.newObjectiveSub');
	  		  if(typeof id != 'undefined')
	  		  {
	  		  $.ajax({
				type:'POST', 
			    url: webbox,
			    data:
			        {
			        'action'	: 'subObjectiveDelete',
			    	'id'		: id
			        },success:function(data){
				       	if(data == 'success'){
				       		thisH.remove();
				       		$('.awbox-spinner').hide();
			        	}
			        }
    			}); // Ajax
	  		} // if id exist
	  		else
	  		{
	  			thisH.remove();
				$('.awbox-spinner').hide();	
	  		}
	  });

	  // End delete Sub-Objective deleteSubObj


	  /*
	  * Edit Campaign Title
	  */
	  $(document.body).on('click', '.tempNewInner div.tempEdit', function(){

	  	if($(this).hasClass('updateit')){
	  		$('.awbox-spinner').show();
	  		var id 			= $(this).data('id');
	  		var title 		= $(this).closest('.tempNewInner').prev('.SlidingP').find('input[name="campain_name"]').val(); 
	  		var span 		= $(this).closest('.tempNewInner').prev('.SlidingP').find('span.title'); 
	  		var obj 		= $(this).closest('.tempNewInner').find('select[name="campaignName"]').val();
	  		var camp_desc 	= $(this).closest('.tempNewInner').find('textarea[name="camp_desc"]').val();

	  		  	$.ajax({
				type:'POST', 
			    url: webbox,
			    data:
			        {
			        'action'		: 'updateCampaignTitle',
			    	'id'			: id,
			    	'title' 		: title,
			    	'obj' 			: obj,
			    	'camp_desc' 	: camp_desc
			        },success:function(data){
				       	if(data == 'success'){
				       		$('.awbox-spinner').hide();
				       		span.text(title);
			        	}else{
			        		$('.awbox-spinner').hide();
			        	}
			        }
    			}); // Ajax
	  	}
	  	

	  	$(this).children('span.dashicons').toggleClass('dashicons-edit');
		$(this).children('span.dashicons').toggleClass('dashicons-yes');
		$(this).addClass('updateit');
		$(this).closest('.tempNewInner').prev('.SlidingP').find('span.title').toggleClass('hidden');
		$(this).closest('.tempNewInner').prev('.SlidingP').find('input[name="campain_name"]').toggleClass('hidden');

	  	if($(this).closest('.tempNewInner').hasClass('tmhidden')){
		  	$(this).closest('.tempNewInner').prev('.SlidingP').find('.slidInner').children('span.dashicons').addClass('dashicons-arrow-down').removeClass('dashicons-arrow-right');
		  	$(this).closest('.tempNewInner').addClass('active');
		  	$(this).closest('.tempNewInner').removeClass('tmhidden');
	  	}

	  });






	  /*
	  * Edit Objective Title
	  */
	  $(document.body).on('click', '.tempNewInner div.tempEditOjb', function(){

	  	if($(this).hasClass('updateit')){
	  		$('.awbox-spinner').show();
	  		var id 			= $(this).data('id');
	  		var title 		= $(this).closest('.tempNewInner').prev('.SlidingP').find('input[name="objective_name"]').val(); 
	  		var span 		= $(this).closest('.tempNewInner').prev('.SlidingP').find('span.title'); 
	  		var obj_desc 	= $(this).closest('.tempNewInner').find('textarea[name="ob_desc"]').val();

	  		console.log('Desc: ' + obj_desc);

	  		  	$.ajax({
				type:'POST', 
			    url: webbox,
			    data:
			        {
			        'action'		: 'updateObjectiveTitle',
			    	'id'			: id,
			    	'title' 		: title,
			    	'obj_desc' 		: obj_desc
			        },success:function(data){
				       	if(data == 'success'){
				       		$('.awbox-spinner').hide();
				       		span.text(title);
			        	}else{
			        		$('.awbox-spinner').hide();
			        	}
			        }
    			}); // Ajax
	  	}
	  	

	  	$(this).children('span.dashicons').toggleClass('dashicons-edit');
		$(this).children('span.dashicons').toggleClass('dashicons-yes');
		$(this).addClass('updateit');
		$(this).closest('.tempNewInner').prev('.SlidingP').find('span.title').toggleClass('hidden');
		$(this).closest('.tempNewInner').prev('.SlidingP').find('input[name="objective_name"]').toggleClass('hidden');

	  	if($(this).closest('.tempNewInner').hasClass('tmhidden')){
		  	$(this).closest('.tempNewInner').prev('.SlidingP').find('.slidInner').children('span.dashicons').addClass('dashicons-arrow-down').removeClass('dashicons-arrow-right');
		  	$(this).closest('.tempNewInner').addClass('active');
		  	$(this).closest('.tempNewInner').removeClass('tmhidden');
	  	}

	  });



	  /*
	  * Sutter open-close sub-campaign 
	  */
	  $(document.body).on('click', '.subCamptitle.title h4', function(){
	  	$(this).children('span.dashicons').toggleClass('dashicons-arrow-right-alt2');
	  	$(this).children('span.dashicons').toggleClass('dashicons-arrow-down-alt2');
	  	$(this).closest('.subCamptitle.title').next('.email_campains').toggleClass('hidden');
	  });


	  /*
	  * Add New Campaign
	  */
	  $(document.body).on('click', 'div#addnewCampaign', function(){
	  	var camp;
	  	var Jobjective = jQuery.parseJSON(objectives);
	  	$.each(Jobjective, function(k, v) {
	  		camp +='<option value="'+k+'">'+v+'</option>';
	  	});

	  	if(!$(this).hasClass('newObjective')){
	  		var newHtml = '<div class="newTemplate emailCampaign">'
					    +'<div class="SlidingP">'
				            +'<div class="slidInner">'
				              	+'<span alt="f140" class="dashicons dashicons-arrow-right"></span> &nbsp;' 
				              	+'<input placeholder="Campaign Name..." type="text" name="campain_name" value="">'
				              	+'<span class="title hidden"></span>'
				          	+'</div>'
				        +'</div>'

		     			+'<div class="tempNewInner tmhidden">'
				          +'<div class="form-group selectCampaign mt15">'
			          		+'<label for="campaignName">Objective</label>'
			          		+'<select class="form-control" name="campaignName">'
			          			+'<option value="">Select Objective</option>'
			          			+ camp
			          		+'</select>'
			          	+'</div>'
			          	+'<div class="campaignDesc">'
			          		+'<div class="form-group">'
			          			+'<label for="camp_desc">Description</label>'
			          			+'<textarea rows="4" style="width:100%;" name="camp_desc"></textarea>'
			          		+'</div>'
			          	+'</div>'
				          +'<div class="camtempDelete">'
				              +'<span alt="f158" class="dashicons dashicons-no"></span>'
				          +'</div>'

	    				  +'<div class="addnewSubCampaign"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>'
				      	+'</div>'
		        	+'</div>';
	
	  	}else{
			var newHtml = '<div class="newTemplate newObjectiveW">'
					    +'<div class="SlidingP">'
				            +'<div class="slidInner">'
				              	+'<span alt="f140" class="dashicons dashicons-arrow-right"></span> &nbsp;' 
				              	+'<input placeholder="Objective Name..." type="text" name="objective_name" value="">'
				              	+'<span class="title hidden"></span>'
				          	+'</div>'
				        +'</div>'

		     			+'<div class="tempNewInner tmhidden">'

		     			+ '<div class="ob_desc"><textarea class="form-control" style="width:100%;" rows="4" name="ob_desc"></textarea></div>'

				          +'<div class="objectDelete">'
				              +'<span alt="f158" class="dashicons dashicons-no"></span>'
				          +'</div>'
	    					+'<div class="addnewSubObjective"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>'
				      	+'</div>'
		        	+'</div>';
	  	}
	  	


		$(newHtml).insertBefore($(this));


	  }); // End add new Campaign


	  /*
	  * Campaign Email Send
	  */
	  $(document.body).on('click', '.subCamptitle.title div.actionEmail', function(){
	  	var emails 			= $('input[name="sendto[]"]').map(function(){return $(this).val();}).get();
	  	var id 				= $(this).data('subcid');
	  	var campainfor 		= $('select[name="campainfor"]').val(); 
	  	$('.awbox-spinner').show();
	  	$.ajax({
				type:'POST', 
			    url: webbox,
			    data:
			        {
			        'action'		: 'campaignEmailSent',
			    	'id'			: id,
			    	'emails' 		: emails,
			    	'campainfor'	: campainfor
			        },success:function(data){
			        	console.log(data);
				       	if(data == 'success'){
				       		console.log('success email send');
				       		$('.awbox-spinner').hide();
			        	}else{
			        		$('.awbox-spinner').hide();
			        	}
			        }
    			}); // Ajax

	  });



	/*
	* Return Sub-Object to Campaign page
	*/
	$(document.body).on('change', '.selectCampaign select[name="campaignName"]', function(){
		var id = $(this).val();
		var thisCmp = $(this).closest('.selectCampaign');
		var outHtml = '';
		
		$('.awbox-spinner').show();
		$.ajax({
				type:'POST', 
			    url: webbox,
			    dataType: 'json',
			    data:
			        {
			        'action'	: 'selectRltSubObject',
			    	'id'		: id
			        },success:function(data){
			        	//console.log(data);
			        	thisCmp.closest('.campoignObjective').nextAll('.newTemplate.newSubCampaign').find('.selectSubCampaign').remove();
			        	outHtml +='<div class="form-group selectSubCampaign">';
			        	outHtml += '<label for="subcampaignName">Sub Objective</label>';
			          	outHtml +='<select class="form-control" multiple name="sub_obj">';
				       	$.each(data, function(k, v) {
				       		outHtml +='<option value="'+v.id+'">'+v.sub_obj+'</option>';	
				       	});
				       	outHtml +='</select></div>';
				       	thisCmp.closest('.campoignObjective').nextAll('.newTemplate.newSubCampaign').find('input[name="scmp_name"]').addClass("Omar Test");
				       	$(outHtml).insertAfter(thisCmp.closest('.campoignObjective').nextAll('.newTemplate.newSubCampaign').find('input[name="scmp_name"]'));
				       	$('.awbox-spinner').hide();
			        }
    			}); // Ajax
	});


	/*
	* Change campaign type .newSubCampaign .email_campains select[name="type"]
	*/
	$(document.body).on('change', '.newSubCampaign .email_campains select[name="type"]', function(){
		var tval = $(this).val();
		if(tval != 'email'){
			$(this).closest('form').find('.smspushAea').removeClass('hidden');
			$(this).closest('form').find('.visualTextArea').addClass('hidden');
		}else{
			$(this).closest('form').find('.smspushAea').addClass('hidden');
			$(this).closest('form').find('.visualTextArea').removeClass('hidden');
		}
	});




	/*
	* Brand Configuration Submit
	*/
	$(document.body).on('click', 'div#brandSettings input[type="submit"]', function(e){
		e.preventDefault();
		var formData = $(this).closest('form').serializeArray();
		console.log(formData);
	});


	/*
	* REturn false if campaign name empty
	*/
	$(document.body).on('click', '.startCamp a', function(){
		$(this).closest('.startCamp').prev('.form-group').find('select').addClass("test class");
		var campName = $(this).closest('.startCamp').prev('.form-group').find('select').val();
		var sbcmp = getQueryString('sbcmp', $(this).attr('href'));


		var newQ = [];
	  	$('#crmWrap').find('input[type="checkbox"]:checked').each(function(){
	  		var q = $(this).closest('div.col-md-3').children('label:first-child').text();
	  		newQ.push(q);
	  	});
	  	var inVal = newQ.join(';');
	  	console.log(inVal);
	  	//$('input[name="asqQsn"]').val(inVal);


		if(sbcmp == null){
			$(this).attr('href', $(this).attr('href') + '&sbcmp='+ campName + '&asqQsn=' + inVal );
			return true;
		}else{
			return false;	
		}
	});


}); //End Document Ready



	/*
	* Select all fromCRM
	*/
	function selectAll() {
        if(document.getElementById("selbtn").checked) { 
       //document.getElementById("selbtn").checked = true;
        var items = document.getElementsByName('send_mails[]');
        var sneMSM = document.getElementsByName('send_sms[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
            	sneMSM[i].checked = true;
        }
      } else {   
      var items = document.getElementsByName('send_mails[]');
      var sneMSM = document.getElementsByName('send_sms[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
            	sneMSM[i].checked = false;
        }

       }

    }


var getQueryString = function ( field, url ) {
    var href = url ? url : window.location.href;
    var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
    var string = reg.exec(href);
    return string ? string[1] : null;
};


