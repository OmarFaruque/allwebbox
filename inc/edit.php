<?php
function edit_record(){
global $wpdb;
$form_id=$_GET['id'];
  
 $qry="select * from wp_awe_forms where row_id='$form_id'";
 $form_details = $wpdb->get_results($qry, ARRAY_A);

 $form_name=$form_details[0]['form_name'];

 $from_identi= explode(",",$form_details[0]['identi_ques']); 

 $from_contact=explode(",",$form_details[0]['contact_ques']);
 
 $form_profque=explode(",",$form_details[0]['profile_ques']);
 	
 $form_customque=explode(",",$form_details[0]['custom_ques']);

 
 $qry="select * from wp_awe_customq where form_id='$form_id'"; 
 $created_ques = $wpdb->get_results($qry, OBJECT);
  
 $querystr="select * from wp_awe_customq";
 $allCreated_ques=$wpdb->get_results($querystr, OBJECT);

 if(isset($_POST['form_submit'])){
 
    $identiQues="";
	$contactQues="";
	$profileQues="";
	$customQues="";
	$createdcustomQues="";
	
	  $form_name=$_POST['name_form']; 	
	  $total_num_question=$_POST['num_questions'];
	  
	 	
  // print_r($_POST['identi_default_questions']);
    if(!empty($_POST['identi_default_questions'])) {
    foreach($_POST['identi_default_questions'] as $check) {
		$identiQues.=$check.",";
           // echo $check; //echoes the value set in the HTML form for each checked checkbox.
                         //so, if I were to check 1, 3, and 5 it would echo value 1, value 3, value 5.
                         //in your case, it would echo whatever $row['Report ID'] is equivalent to.
    }
    }
	  
  if(!empty($_POST['contact_default_questions'])) {
    foreach($_POST['contact_default_questions'] as $check) {
		$contactQues.=$check.",";
            //echo $check; //echoes the value set in the HTML form for each checked checkbox.
                         //so, if I were to check 1, 3, and 5 it would echo value 1, value 3, value 5.
                         //in your case, it would echo whatever $row['Report ID'] is equivalent to.
    }
    }
	  
	  
	 if(!empty($_POST['profile_default_questions'])) {
    foreach($_POST['profile_default_questions'] as $check) {
		$profileQues.=$check.",";
            //echo $check; //echoes the value set in the HTML form for each checked checkbox.
                         //so, if I were to check 1, 3, and 5 it would echo value 1, value 3, value 5.
                         //in your case, it would echo whatever $row['Report ID'] is equivalent to.
    }
    }  
   
   if(!empty($_POST['custom_questions'])) {
    foreach($_POST['custom_questions'] as $check) {
		$customQues.=$check.",";
            //echo $check; //echoes the value set in the HTML form for each checked checkbox.
                         //so, if I were to check 1, 3, and 5 it would echo value 1, value 3, value 5.
                         //in your case, it would echo whatever $row['Report ID'] is equivalent to.
    }
    } 
 
 
 
   
    
     if(!empty($_POST['custom_created_questions'])) {    
        $allqus=explode(",",$_POST['help_custom']);
          
         for($i=0;$i<count($allqus);$i++) {
          
         if($_POST['custom_created_questions'][$allqus[$i]]) {         
          $qry="update wp_awe_customq set question_enable='1' where row_id='$allqus[$i]'";             
          $wpdb->query($qry); 
          
         }
         else  {
         $qry="update wp_awe_customq set question_enable='0' where row_id='$allqus[$i]'";             
          $wpdb->query($qry);
           
         }    
        }
     }
    
     
   
    
    
    
          
 
 
    $identiQues=rtrim($identiQues,",");
	$contactQues=rtrim($contactQues,",");
	$profileQues=rtrim($profileQues,",");
	$customQues=rtrim($customQues,",");
   
    /* $qry="insert into wp_awe_forms(form_name,identi_ques,contact_ques,profile_ques,custom_ques,total_custom_ques) VALUES('$form_name','$identiQues'
	 ,'$contactQues','$profileQues','$customQues','$total_num_question')";
    $wpdb->query($qry);	 
    $lastid = $wpdb->insert_id;*/
 
  
   //Update query 
    $qry="update wp_awe_forms set form_name='$form_name',identi_ques='$identiQues',contact_ques='$contactQues',profile_ques='$profileQues',custom_ques='$customQues' where row_id='$form_id'";
    $wpdb->query($qry);
  
  
  
  
$j=1;


  /*
     for($i=0;$i<$total_num_question;$i++) {
		     $singlechoice=0;
			 $multichoice=0;
			 // Question Value 
		 $ques=$_POST['name_question'][$i];		
		     // Question Answer Type 
		 $type_ans_format=$_POST['type_question'][$j];		
		     // if answer type is single select 
			 if($type_ans_format==3) { 
		 $ttl=$_POST['num_options_question'][$j];
			$singlechoice=$ttl;		 
			 } 
			  // if answer type is multiple select 
			  if($type_ans_format==4) {
		 $ttl=$_POST['num_options_question_m'][$j];
		     $multichoice=$ttl;
		      }  		
		 		
		 // save custom questions in table 
		 $qry="insert into wp_awe_customq(form_id,questions,answer_type,total_single,total_multi) VALUES('$lastid','$ques','$type_ans_format','$singlechoice','$multichoice')";
		 $wpdb->query($qry);	
		 $customlastid=$wpdb->insert_id;
		      //Answer text box's values 
		 for($k=0;$k<$ttl;$k++) {
		    echo $quesCustom=$_POST['option_answ_'.$j][$k];
			$qry="insert into wp_awe_formqansvalue(entry_id,form_id,ques_value) VALUES('$customlastid','$lastid','$quesCustom')";
			$wpdb->query($qry);
		  }
		 
		$j++; 
		echo "<hr/>";
	 }
   
     */

 }
 
 
 
 
 $qry="select * from wp_awe_forms where row_id='$form_id'";
 $form_details = $wpdb->get_results($qry, ARRAY_A);

 $form_name=$form_details[0]['form_name'];

 $from_identi= explode(",",$form_details[0]['identi_ques']); 

 $from_contact=explode(",",$form_details[0]['contact_ques']);
 
 $form_profque=explode(",",$form_details[0]['profile_ques']);
 	
 $form_customque=explode(",",$form_details[0]['custom_ques']);

 
 $qry="select * from wp_awe_customq where form_id='$form_id'"; 
 $created_ques = $wpdb->get_results($qry, OBJECT);

?>


<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> 

<script>
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


</script>

<div class="page-content-wrapper">
<div class="page-content">
<!-- <h3 class="page-title"> Form Name
<small></small>
</h3> -->
<div class="clearfix"></div>
<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light bordered">
<div class="portlet-title">
<div class="caption">
<!-- <i class="fa fa-check-square-o"></i> -->
<span class="caption-subject font-dark bold uppercase">Edit Your  Form </span>
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-original-title="Ayuda">
<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
</div>
</div>
<div class="portlet-body form">
<form action="" method="post" enctype="application/x-www-form-urlencoded" class="form-horizontal" role="form">
<div class="form-body">
<div class="form-group" id="name_form">
<div class="col-md-12">
<label>
Form Name
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-placement="bottom"  data-original-title="Ayuda">
<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
</label>
<input type="text" class="form-control" name="name_form" value="<?php echo $form_name; ?>" autofocus="true" maxlength="255" placeholder="Form Name" required="true">
<input type="hidden" name="id_cms" value="" id="id_cms">
</div>
</div>
<div class="form-group">
<div class="col-md-12">
<h3>
1. Basic Questions.
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-placement="bottom"   data-original-title="Ayuda">
 <i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
<hr>
</h3>
</div>
</div>
<div class="form-group" id="basic_questions">
<div class="col-md-12">
<div class="fix-padding" style="width:100%; display: inline-block;padding-left: 15px; padding-right:15px;">
<h4>
<b>Identification Questions</b>
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-placement="bottom"  data-original-title="Ayuda">
<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
</h4>
<hr>
</div>  
<?php 
 $querystr="select * from wp_awe_identification";
 $identity_ques = $wpdb->get_results($querystr, OBJECT);
 

 $querystr="select * from wp_awe_contactinfo";
 $contact_ques = $wpdb->get_results($querystr, OBJECT);
 
 
  $querystr="select * from wp_awe_profileques";
 $profile_ques = $wpdb->get_results($querystr, OBJECT);
 
   $querystr="select * from wp_awe_customques";
 $custom_ques = $wpdb->get_results($querystr, OBJECT);
 
 
?>


<div class="row"> 
<?php 
 foreach($identity_ques as $val) {
  
 $chk="";
 if(in_array($val->row_id, $from_identi)) {
 $chk="checked";
 }
      


?>
<label class="mt-checkbox mt-checkbox-outline">
<input type="checkbox" <?php echo $chk;?> name="identi_default_questions[]" value="<?php echo $val->row_id;?>"><?php echo $val->identi_label;?>
<span></span>
</label>
 <?php } ?>
</div>
 



<div class="row"> 
<div class="fix-padding" style="width:100%; display: inline-block;padding-left: 15px; padding-right:15px;">
<h4>
<b>Contact Information</b>
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-placement="bottom"  data-original-title="Ayuda">
<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
</h4>
<hr>
</div>
<div class="row">


<?php 
 foreach($contact_ques as $val) {
   

 $chk="";
 if(in_array($val->row_id, $from_contact)) {
 $chk="checked";
 }
   
?>
<div class="col-md-3">
<label class="mt-checkbox mt-checkbox-outline">
<input type="checkbox" <?php echo $chk;?> name="contact_default_questions[]" value="<?php echo $val->row_id;?>"><?php echo $val->identi_label;?>
<span></span>
</label></div> 
 <?php } ?>

</div>  

 
<div class="fix-padding" style="width:100%; display: inline-block;padding-left: 15px; padding-right:15px;">
<h4>
<b>Profiling questions</b>
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-placement="bottom"   data-original-title="Ayuda">
<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
</h4>
<hr>
</div>


<?php 
 foreach($profile_ques as $val) {
 $chk="";
 if(in_array($val->row_id, $form_profque)) {
 $chk="checked";
 }
  
?>
<div class="col-md-3">
<label class="mt-checkbox mt-checkbox-outline">
<input type="checkbox" <?php echo $chk;?>  name="profile_default_questions[]" value="<?php echo $val->row_id;?>"><?php echo $val->identi_label;?>
<span></span>
</label></div>
 <?php } ?>



</div> 
</div>
</div>
<div class="form-group">
 <div class="col-md-12">
<h3>
2. Custom Questions.
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-placement="bottom" data-content="Preguntas personalizadas que ya están creadas." data-original-title="Ayuda">
<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
<hr>
</h3>
</div>
</div>
<div class="form-group" id="personalized_questions">
<div class="col-md-12">
<div class="row">
<?php 

 $allids='';
 foreach($custom_ques as $val) {
 $chk="";

 if(in_array($val->row_id, $form_customque)) {
 $chk="checked";
 }

?>
<div class="col-md-3">
<label class="mt-checkbox mt-checkbox-outline">
<input type="checkbox" <?php echo $chk;?> name="custom_questions[]" value="<?php echo $val->row_id;?>"><?php echo $val->identi_label;?>
<span></span>
</label></div> 
 <?php } ?> 
</div>


 
<!-- created question update code -->

<div class="row">
<?php 

 foreach($created_ques as $val) {
   $allids.=$val->row_id.",";
  $chk="";
  if($val->question_enable==1) {
  $chk="checked";
  }

?>
<div class="col-md-3">
<label class="mt-checkbox mt-checkbox-outline">
<input type="checkbox" <?php echo $chk;?> name="custom_created_questions[<?php echo $val->row_id;?>]" value="<?php echo $val->row_id;?>"><?php echo $val->questions;?>
<span></span>
</label></div> 
 <?php } ?> 
</div>
 
 
 <?php 
foreach($allCreated_ques as $val) {
?>
<div class="col-md-3">
<label class="mt-checkbox mt-checkbox-outline">
<input type="checkbox"   name="custom_created_questions[]" value="<?php echo $val->row_id;?>"><?php echo $val->questions;?>
<span></span>
</label></div> 
<?php } ?>
 
 
 
 
 <input type='hidden' name='help_custom' value='<?php echo rtrim($allids,",");?>'/>



<div class="row">  </div> 
</div>
<div class="form-group">
<div class="col-md-12">
<!-- 
<h3>
3. Create Custom Questions.
<button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true"   data-original-title="Ayuda">
<i class="fa fa-question-circle-o" aria-hidden="true"></i>
</button>
<hr>
</h3>
</div>
</div>
<div class="form-group" id="build_questions">
<div class="col-md-12">
<p>
Now you must decide how many additional questions you want to create. Try not to do (in total) more than 6 or 7 questions because your clients will feel unmotivated if they are many. The more questions you ask, the fewer customers will leave your information. <br>
<h4>First Select the Number of Additional Questions You Want to Ask.</h4>
</p>
<select class="form-control" id="num_questions" name="num_questions" onChange="adds(this.value)">
<option value="-1">Select the Number of Questions to Create</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
</select>
<hr>
-->


<?php 
include("questions.php");
?> 

 

<div class="col-md-12">&nbsp;</div>


</div>
</div>
<div class="form-group">
<div class="col-md-12">
<button type="submit" name="form_submit" class="btn blue pull-right">Update Form</button>
</div>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>

<link rel='stylesheet' id='customcss-css'  href='http://sutharphp.com/london/wp-content/plugins/allwebbox/inc/style.css' type='text/css' media='' />


<?php } ?> 
