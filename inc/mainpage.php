<?php
function All_web_box(){
	echo "All Web Box";
}



global $wpdb;
$prefix 		= $wpdb->prefix; 
//echo 'prefix: ' . $prefix . '<br/>';
$form_table 	= $prefix . 'awe_forms'; 
$q_question 	= $prefix . 'awe_customq'; 
$f_q_value 		= $prefix . 'awe_formqansvalue';
$jurneytable 	= $prefix . 'awe_journey';




 if(isset($_POST['form_submit']) ){

    $identiQues="";
	$contactQues="";
	$profileQues="";
	$customQues="";
	$content="";
	
	$form_name=$_POST['name_form']; 	
	$total_num_question=$_POST['num_questions'];
	  
	 	
  // print_r($_POST['identi_default_questions']);
    if(!empty($_POST['identi_default_questions'])) {
    	$identiQues .= json_encode($_POST['identi_default_questions']);
    }
	  
  	if(!empty($_POST['contact_default_questions'])) {
    	$contactQues .= json_encode($_POST['contact_default_questions']); 	
    }
	  
	  
	if(!empty($_POST['profile_default_questions'])) {
    	$profileQues .= json_encode($_POST['profile_default_questions']);
    }  
   
   if(!empty($_POST['custom_questions'])) {
    $customQues .= json_encode($_POST['custom_questions']);
    } 
    
   $journey = $_POST['journey'];
   if(!empty($_POST['content'])) {
    $content .= json_encode($_POST['content']);
    } 


    $style = '';
    if(!empty($_POST['style'])){
		$styleMap = array_map('htmlentities',$_POST['style']);
		$style .= json_encode($styleMap);
	}

	$terms = '';
	if(!empty($_POST['termsncondition'])){
		$termMaP = array_map('htmlentities',$_POST['termsncondition']);
		$terms .= json_encode($termMaP);
	}

	$brands = '';
	$_POST['brand_options'] = array_unique(array_values(array_filter($_POST['brand_options'])));

	if(!empty($_POST['brand_options']) && count($_POST['brand_options']) > 0){
		$brands .= json_encode($_POST['brand_options']);	
	}
	
 

	if(isset($_POST['name_question'])){

		$questoQsArguArray = ($customQues)?json_decode($customQues):array();

		$customQuesArray =  array_merge($questoQsArguArray, array_filter($_POST['name_question']));

		$customQuesArray = array_map('htmlentities',$customQuesArray);

		$customQues = json_encode($customQuesArray);

	}
 	
 
   if(!isset($_POST['edit'])){
    	$qry="insert into $form_table(form_name,identi_ques,contact_ques,profile_ques,custom_ques,total_custom_ques,style,journey,content,terms,brand_options) VALUES('$form_name','$identiQues','$contactQues','$profileQues','$customQues','$total_num_question','$style','$journey','$content','$terms','$brands')";
    	$insert = $wpdb->query($qry);	 
    	$lastid = $wpdb->insert_id;
    	if($insert){
    		$msg = '<span class="bg-success color-success">Form created successfully</span>';
    	}else{
    		$msg = '<span class="bg-error color-error">Form created Failed</span>';
    	}
	}else{
		$id = $_POST['edit'];
		$update = $wpdb->query($wpdb->prepare("UPDATE $form_table SET form_name='$form_name',
			identi_ques='$identiQues',
			contact_ques='$contactQues',
			profile_ques='$profileQues',
			custom_ques='$customQues',
			total_custom_ques='$total_num_question',
			style='$style',
			journey='$journey',
			content='$content',
			terms='$terms',
			brand_options='$brands' WHERE row_id=%d", $id));
		if($update){
			$lastid = $id;
			$msg = '<span class="bg-success color-success">Form Update successfully</span>';
		}else{
			$msg = '<span class="bg-error color-error">Form Update Failed</span>';
		}
	}
    
  



/*echo 'allPosts: <br/>';
echo '<pre>';
print_r($_POST);
echo '</pre>';*/



$j=1; 
     for($i=0;$i<$total_num_question;$i++) {

		     $singlechoice=0;
			 $multichoice=0;
			 $ttl = 0;
			 // Question Value 
		 $ques=$_POST['name_question'][$j];	
		 $ques = htmlentities($ques);	
		     // Question Answer Type 
		 $type_ans_format=$_POST['type_question'][$j];		
		     // if answer type is single select 
			 if($type_ans_format==3) { 
		 $ttl .= $_POST['num_options_question'][$j];
			$singlechoice=$ttl;		 
			 } 
			  // if answer type is multiple select 
			  if($type_ans_format==4) {
		 $ttl .= $_POST['num_options_question_m'][$j];
		     $multichoice=$ttl;
		      }  		
		 		
		// save custom questions in table 
		$required = (isset($_POST['req_question'][$j]))?$_POST['req_question'][$j]:0;
		$exist = $wpdb->get_row('SELECT questions FROM '.$q_question.' WHERE questions="'.$ques.'"');
		if(!$exist){
		 $qry="insert into $q_question(form_id,questions,answer_type,total_single,total_multi,question_enable,required) VALUES('$lastid','$ques','$type_ans_format','$singlechoice','$multichoice','1',$required)";
		 $wpdb->query($qry);	
		 $customlastid=$wpdb->insert_id;
		      //Answer text box's values 

		 for($k=0;$k<(int)$ttl;$k++) {
		    $quesCustom=$_POST['option_answ_'.$j][$k];
			$qry="insert into $f_q_value(entry_id,form_id,ques_value) VALUES('$customlastid','$lastid','$quesCustom')";
			$wpdb->query($qry);
		  }
		} // End if data exist check
		 
		$j++; 
	 }
 

 /*header('Location:admin.php?page=allwebbox/class/allwebClass.php/all_forms'); */


} /* End Post Submit */



/**
* Query process for Edit
*/

$identyfQry 	= array();
$contactfQry 	= array();
$termsnC 		= array();
$profilingQs 	= array();
$customQs 		= array();
$styleQrs 		= array();
$contents 		= array();
$journey 		= '';

if(isset($_GET['id']) && $_GET['id']!= ''){
	$id = $_GET['id'];
	$editfQueryQ="select * from $form_table WHERE `row_id`=$id";

 $editfQuery=$wpdb->get_row($editfQueryQ, OBJECT); 

 /*echo '<pre>';
 print_r($editfQuery);
 echo '</pre>';*/


	$identyfQry 	= json_decode($editfQuery->identi_ques); //Identification Questions From Database as array
	$contactfQry 	= ($editfQuery->contact_ques)?json_decode($editfQuery->contact_ques):array(); //Contact Information Questions From Database as array
	$termsnC 		= ($editfQuery->terms)?json_decode($editfQuery->terms):array(); //Terms & Condition Questions From Database as array
	$profilingQs 	= ($editfQuery->profile_ques)?json_decode($editfQuery->profile_ques):array(); //Profiling questions From Database as array
	$customQs 		= ($editfQuery->custom_ques && $editfQuery->custom_ques !='null')?json_decode($editfQuery->custom_ques):array(); //Custom questions From Database as array
	$styleQrs 		= json_decode($editfQuery->style); //Style from database array
	$contents 		= json_decode($editfQuery->content); //Decode Content post id's
	$brands 		= json_decode($editfQuery->brand_options); //Decode all Brands
	$journey 		= $editfQuery->journey;

	

} /*End isset get id */


?>


<div class="page-content-wrapper allwebbox">
<div class="page-content">

	<div class="clearfix"></div>
<div class="row">
<div class="col-md-12 col-sm-12">

<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-dark bold uppercase">
				<?php if(isset($_GET['id'])): ?>
					<?php echo __('Edit Form', 'allwebbox'); ?>: <?php echo $editfQuery->form_name; ?>
				<?php else:  
					echo __('Creating a New Form', 'allwebbox' );
				endif; ?>
			</span> 
		</div>
	</div> 
<br/>
<div class="portlet-body form">
<form action="" method="post" enctype="application/x-www-form-urlencoded" class="form-horizontal" role="form">
<div class="form-body">
	<div id="successMsg">
		<?php 
			if(isset($msg) && $msg != ''){
				echo $msg;
			}
		?>
	</div>
<div class="form-group" id="name_form">
	<div class="col-md-12">
		<div class="fix-padding">
			<h4><b><?php echo __('Form Name', 'allwebbox'); ?></b>	<a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('Give a Name  to your form', 'allwebbox'); ?>  </span></a></h4>
			<input type="text" class="form-control" value="<?php echo (isset($_GET['id']))?$editfQuery->form_name:''; ?>" name="name_form" autofocus="true" maxlength="255" placeholder="Form Name" required="true">
			<input type="hidden" name="id_cms" value="" id="id_cms">
		</div>
	</div>
</div>
<hr>
<div class="sectionAllow">
	<div class="form-group">
		<div class="col-md-12">
			<h3>
			1. <?php echo __('Basic Questions', 'allwebbox'); ?>.
			<a id="visiableBasicQuestion" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
			</h3>
		</div>
	</div>
	<div class="form-group allwebContentBdy" id="basic_questions">
	<div class="col-md-12">
	<div class="fix-padding">
		<h4>
			<b><?php echo __('Identification Questions', 'allwebbox'); ?></b>
			<a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span><?php echo __('Basic Information', 'allwebbox'); ?>  </span></a>
		</h4>
	</div>
	<?php 
/*
	$sql = "SHOW TABLES LIKE '%'";
	$results = $wpdb->get_results($sql);

	foreach($results as $index => $value) {
	    foreach($value as $tableName) {
	        echo $tableName . '<br />';
	    }
	}
*/

	 $identity_ques = array(
	 	'First Name' 	=> 'First Name', 
	 	'Last Name' 	=> 'Last Name', 
	 	'Nick Name' 	=> 'Nick Name',
	 	'Salute (Mr, Ms, Miss, Dr, etc)' 	=> 'Salute (Mr, Ms, Miss, Dr, etc)',
	 	'ID Number' 	=> 'ID Number',
	 	'Brand' 		=> 'Brand'
	 );
	 
	$contact_quesm = array(
		'country', 'city', 'address', 'email', 'subscribed', 'mobile', 'Phone Number', 'facebook', 'twitter', 'linkedin',  'instagram', 'google+', 'pinterest', 'youtube', 'whatsapp'
	);
	$contact_ques = array_unique(array_merge($contactfQry, $contact_quesm), SORT_REGULAR); //marge with database for rearrange as like dragble 
	
	$profile_quesm 	= array('Gender', 'Date of Birth', 'Civil Status', 'Academic Level');


	
	$profile_ques 	= array_unique(array_merge($profilingQs, $profile_quesm), SORT_REGULAR);

	$querystr="select * from $q_question";
	$allCreated_ques=$wpdb->get_results($querystr, OBJECT); 


	$custom_quesm 	= array('Date of visit', 'In Charge', 'Economic Activity',  'Presence in Social Networks', 'Has a Website', 'Your Website Has');
	
	
	$allCreatedArray 	= array();
	foreach($allCreated_ques as $scQ) array_push($allCreatedArray, $scQ->questions);
	$custom_ques_all	= array_unique(array_merge($custom_quesm, $allCreatedArray), SORT_REGULAR);


	
	$custom_ques 		= array_unique(array_merge($customQs, $custom_ques_all), SORT_REGULAR);
	


	 /*$querystr="select * from wp_awe_contactinfo";
	 $contact_ques = $wpdb->get_results($querystr, OBJECT);
	 
	 
	 $querystr="select * from wp_awe_profileques";
	 $profile_ques = $wpdb->get_results($querystr, OBJECT);
	 

	 $questionall="select * from $q_question";
	 $custom_ques = $wpdb->get_results($querystr, OBJECT);*/

	 $ansTypes = array('Text', 'Description', 'Single selection', 'Multiple selection', 'Number', 'Date', 'Email');

	?>

	<div class="row"> 
		<ul id="sortable1" class="shortablefield">
		<?php 
		 foreach($identity_ques as $val => $si) {
		 	$checked = (in_array($val, $identyfQry))?'checked':'';
		?>
		<li class="ui-state-default">
			<label class="mt-checkbox mt-checkbox-outline">
			<input type="checkbox" <?php echo $checked; ?>   name="identi_default_questions[]" value="<?php echo $val;?>"><?php echo $si; ?>
			<span></span>
			</label>
			<?php if($si == 'Brand'): ?>
				<div class="actionbutton">
					<div class="edit"><span alt="f464" class="dashicons dashicons-edit"></span></div>
				</div>

				<!-- Editable Field -->
				<div class="editablefield">
				<div class="form-group">

				<?php 				
				echo '<div class="qoptionsList">Options: <br/>';
				if($brands && count($brands) > 0){
					foreach($brands as $sBrand):
						echo '<span class="inputF"><input type="text" style="margin-right:5px;" class="form-control" name="brand_options[]" value="'.$sBrand.'"><span alt="f153" class="dashicons dashicons-dismiss"></span></span>';
					endforeach;
				}else{
					echo '<input type="text" name="brand_options[]" value=""/>';			
				}
				
				echo '</div>
					<a class="addnewOption brand" href="#"><span alt="f502" class="dashicons dashicons-plus-alt"></span></a>';
				?>
				</div>
			</div>
			<?php endif; ?>
		</li>
		 <?php } ?>
		</ul>
	</div>
	 
	<hr>
	<div class="row"> 
	<div class="half col-md-6">
	<div class="fix-padding" >
	<h4>
		<b><?php echo __('Contact Information', 'allwebbox'); ?></b>
		<a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span><?php echo __('Contact Information', 'allwebbox'); ?>  </span></a>
	</h4>
	</div>
	<div class="row">


	<ul id="sortable2" class="shortablefield">
	<?php 
	 foreach($contact_ques as $sC) {
	 	$checked = (in_array($sC, $contactfQry))?'checked':'';
	?>
	<li class="ui-state-default">
		<div class="col-md-3">
		<label class="mt-checkbox mt-checkbox-outline">
		<input type="checkbox" <?php echo $checked; ?>   name="contact_default_questions[]" value="<?php echo $sC; ?>"><?php echo ucfirst($sC); ?>
		<span></span>
		</label></div> 
	</li>
	 <?php } ?>
	</ul>

	</div>  
	</div> <!-- Half -->
	<div class="half col-md-6">
		
		<div class="fix-padding" >
			<h4>
				<b><?php echo __('Terms and conditions', 'allwebbox'); ?></b>
				<a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span><?php echo __('Terms and conditions', 'allwebbox'); ?> </span></a>
			</h4>
		</div>
		<div class="col-md-3">
			<label class="mt-checkbox mt-checkbox-outline">
				<input type="checkbox" <?php echo (isset($termsnC->active) && $termsnC->active == 1 )?'checked':''; ?>    name="termsncondition[active]" value="1"> <?php echo __('Active Terms & Condition', 'allwebbox'); ?>
				<span></span>
			</label>
		</div>
		<div class="col-md-3">
			<label for="termsnconditiontext"><?php echo __('Terms & Condition', 'allwebbox'); ?></label>
			<input style="width:100%;" type="text" name="termsncondition[text]" id="termsnconditiontext" class="form-control" value="<?php echo $termsnC->text; ?>" />
		</div>

		<div class="col-md-3 mt5"> <!-- terms and condition link -->

			<label for="termsnconditionlink">T&C Page</label><br/>
			<select id="termsnconditionlink" name="termsncondition[link]" class="form-control">
				<option value=""><?php echo __('Select Terms & Condition Page', 'allwebbox'); ?></option>
				<?php 
					$page_ids = get_all_page_ids(); 
					foreach($page_ids as $spage){
						$selectPage = ($termsnC->link == $spage)?'selected':'';
						echo '<option '.$selectPage.' value="'.$spage.'">'.get_the_title( $spage).'</option>';
					}
				?>
			</select>			
		</div>

		<br><br>

		<div class="fix-padding" >
			<h4>
				<b><?php echo __('Profiling questions', 'allwebbox'); ?></b>
				<a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span><?php echo __('Profile Data', 'allwebbox'); ?> </span></a>
			</h4>
		</div>

		<ul id="sortable3" class="shortablefield">
			<?php 
			 foreach($profile_ques as $sP) {
			 	$checkprof = (in_array($sP, $profilingQs))?'checked':'';
			?>
			<li class="ui-state-default">
				<div class="col-md-3">
					<label class="mt-checkbox mt-checkbox-outline">
					<input type="checkbox" <?php echo $checkprof; ?> name="profile_default_questions[]" value="<?php echo $sP; ?>"><?php echo ucfirst($sP);?>
					<span></span>
					</label>
				</div>
			</li>
			 <?php } ?>
		</ul>
	</div>
	</div> 
	</div>
	</div>
</div> 
<!-- End Section -->
<!-- Start New Section -->
<div class="sectionAllow">
	<div class="form-group">
		<div class="col-md-12">
			<div class="fix-padding">
					<h3>
						2. <?php echo __('Custom Questions', 'allwebbox'); ?>.
						<a id="visiableBasicQuestion2" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
					</h3>
			</div>
		</div>
	</div>
	
	<div class="form-group allwebContentBdy" id="personalized_questions">
	<div class="col-md-12">
	<div class="row">

	<ul id="sortable4" class="shortablefield">
		<?php 
		$count = 0;
		 foreach($custom_ques as $cQ) {
		 	$cuChecked = (in_array($cQ, $customQs))?'checked':'';
		 	if($cQ != ''){
		 		$selectType = $wpdb->get_row("SELECT `row_id`, `answer_type`, `required` FROM $q_question WHERE `questions`= '$cQ'", OBJECT);

		?>
		<li class="ui-state-default">
			<div class="col-md-3">
				<label class="mt-checkbox mt-checkbox-outline">
					<input type="checkbox" <?php echo $cuChecked; ?>   name="custom_questions[]" value="<?php echo $cQ; ?>"><?php echo ucfirst($cQ); ?>
					<span></span>
				</label>
			</div> 
			<?php if($selectType): ?>
			<div class="actionbutton">
				<div class="edit"><span alt="f464" class="dashicons dashicons-edit"></span></div>
				<div class="delete"><span alt="f153" class="dashicons dashicons-dismiss"></span></div>
			</div>
			<div class="editablefield">
				<div class="form-group" data-entry_row_id="<?php echo $selectType->row_id; ?>">
					<label for="editAnswerType<?php echo $count;  ?>"><?php echo __('Answer Type', 'allwebbox'); ?>:</label>
					
					<select name="editAnswerType" id="editAnswerType<?php echo $count;  ?>">
					<option><?php echo __('Select Your Answer Format', 'allwebbox'); ?> </option>
					<?php 
						for($t=0; count($ansTypes) > $t; $t++){ 
						$selectedOp = ($t+1 == $selectType->answer_type)?'selected':'';
					?>
                    <option <?php echo $selectedOp; ?> value="<?php echo $t+1; ?>"><?php echo $ansTypes[$t]; ?></option>
                    <?php } ?>
					</select>

					&nbsp;&nbsp;<label><?php echo __('Required', 'allwebbox'); ?>: <input <?php echo ($selectType->required == 1)?'checked':''; ?> type="checkbox" name="updateRequred" value="1"/></label>

				<?php 
				if($selectType->answer_type == 3 || $selectType->answer_type == 4 ):
				$selectOptions = $wpdb->get_results("SELECT `row_id`, `ques_value` FROM $f_q_value WHERE `entry_id`= '$selectType->row_id'", OBJECT);
				/*echo '<pre>';
				print_r($selectOptions);
				echo '</pre>';*/
				echo '<div class="qoptionsList">Options: <br/>';
				foreach($selectOptions as $sEOptions):
				?>
					<span class="inputF"><input type="text" class="form-control" data-option_row_id="<?php echo $sEOptions->row_id; ?>" name="edit_options[]" value="<?php echo $sEOptions->ques_value; ?>"/><span alt="f153" class="dashicons dashicons-dismiss"></span></span>
				<?php
				endforeach;
				echo '</div>
					<a class="addnewOption" href="#"><span alt="f502" class="dashicons dashicons-plus-alt"></span></a>';

				endif;
				?>
				<button type="submit" data-entry_id="" class="updateOptions button button-primary"><?php echo __('Update', 'allwebbox'); ?></button>
				<div class="ajaxSuccess"></div>
				</div>
			</div>
			<?php endif; ?>
			
		</li>
		<?php $count++; } /*Chekc if empty*/ } ?>     


	<?php 
	//foreach($allCreated_ques as $val) {
	//	$cuChecked = (in_array($val->questions, $customQs))?'checked':'';
	?>
<!--	<li class="ui-state-default">
		<div class="col-md-3">
			<label class="mt-checkbox mt-checkbox-outline">
			<input type="checkbox" <?php echo $cuChecked; ?>   name="custom_questions[]" value="<?php echo $val->questions;?>"><?php echo $val->questions;?>
			<span></span>
			</label>
		</div> 
	</li>-->
	<?php //} ?>

	</ul>



	</div>

	</div>
	</div>
</div>
<!-- End Section -->
<!-- Start New Section -->
<div class="sectionAllow">


	<div class="form-group">
		<div class="col-md-12">
		<div class="fix-padding">
			<h3>
			3. <?php echo __('Create Custom Questions', 'allwebbox'); ?>.
			<a id="visiableBasicQuestion3" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
			</h3>
		</div>

		</div>
	</div>

	<div class="form-group allwebContentBdy" id="build_questions">
		<div class="col-md-12">
			<p>
			<?php echo __('Now you must decide how many additional questions you want to create. Try not to do (in total) more than 6 or 7 questions because your clients will feel unmotivated if they are many. The more questions you ask, the fewer customers will leave your information.', 'allwebbox'); ?> <br>
			<h4><?php echo __('First Select the Number of Additional Questions You Want to Ask', 'allwebbox'); ?>.</h4>
			</p>
			<select class="form-control" id="num_questions" name="num_questions" onChange="adds(this.value)">
			<option value="-1"><?php echo __('Select the Number of Questions to Create', 'allwebbox'); ?></option>
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
			<?php 
			include("questions.php");
			?> 
			<div class="col-md-12">&nbsp;</div>
		</div>
	</div>
</div> <!-- End Section -->


<!-- Start New Section -->
<div class="sectionAllow">
	<div class="form-group">
		<div class="col-md-12">
		<div class="fix-padding">
			<h3>
			4. <?php echo __('Form Style', 'allwebbox'); ?>.
			<a id="visiableBasicQuestion4" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
			</h3>
		</div>

		</div>
	</div>

	<div class="form-group allwebContentBdy" id="formStyle">
		<div class="col-md-12">
			<p>
				<?php echo __('Set your form style and view', 'allwebbox'); ?>. <br>
			</p>
			
			<div class="form-group">
				<label for="boxpadding"><?php echo __('Form Padding', 'allwebbox'); ?></label>
				<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->boxpadding:'';  ?>" id="boxpadding" class=""  type="number" name="style[boxpadding]"> px
			</div>
			<!--<div class="form-group">
				<label for="hedingfontsize">Heading Font Size</label>
				<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->hedingfontsize:'';  ?>" id="hedingfontsize" class=""  type="number" name="style[hedingfontsize]"> px
			</div>
			<div class="form-group">
				<label for="headingcolor">Heading Color</label>
				<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->headingcolor:'';  ?>" id="headingcolor" class="colorpicker"  type="text" name="style[headingcolor]">
			</div>-->
			<div class="form-group">
				<label for="textcolor"><?php echo __('Text Color', 'allwebbox'); ?></label>
				<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->textcolor:'';  ?>" id="textcolor" class="colorpicker"  type="text" name="style[textcolor]">
			</div>

			<div class="form-group">
				<label for="backgroundc"><?php echo __('Background Color', 'allwebbox'); ?></label>
				<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->backgroundc:'';  ?>" id="backgroundc" class="colorpicker"  type="text" name="style[backgroundc]">
			</div>
			<div class="form-group">
				<label for="backgroundImg"><?php echo __('Background Image', 'allwebbox'); ?></label>
				<input id="backgroundImg" type="hidden" value="<?php echo (count($styleQrs) > 0)?$styleQrs->backgroundimg:'';  ?>" name="style[backgroundimg]">
				<div class="imgPreviewbImage">
					<?php if(count($styleQrs) > 0 && $styleQrs->backgroundimg !=''): ?>
						<div class="pimg"><img src="<?php echo wp_get_attachment_url( $styleQrs->backgroundimg ); ?>"/></div>
					<?php endif; ?>

				</div>
				<button type="button" id="fbImage" class="button button-primary"> 
				<?php echo (count($styleQrs) > 0 && isset($styleQrs->backgroundimg) && $styleQrs->backgroundimg != '')?__('Edit Image', 'allwebbox'):__('Select Image', 'allwebbox'); ?>
				</button>
				<?php  //Image Remove Button
					if(count($styleQrs) > 0 && isset($styleQrs->backgroundimg) && $styleQrs->backgroundimg != ''){
						echo sprintf(__('<button id="deleteFormBgImage" class="button button-primary">%s</button>', 'allwebbox'), 'Delete Image');
					}
				?>
			</div>
			<div class="form-group">
				<label for="bgImageRepeat"><?php echo __('Background Image Repeat', 'allwebbox'); ?></label>
				<select name="style[bgimagerepeat]" id="bgImageRepeat">
					<option value="no"><?php echo __('No', 'allwebbox'); ?></option>
					<option <?php echo (count($styleQrs) > 0 && $styleQrs->bgimagerepeat == 'yes')?'selected':''; ?> value="yes"><?php echo __('Yes', 'allwebbox'); ?></option>
				</select>
			</div>
			<div class="form-group">
				<label for="bgImageAttachment"><?php echo __('Background Attachment', 'allwebbox'); ?></label>
				<select name="style[bgimageattachment]" id="bgImageAttachment">
					<?php 
					$attArray = array(
						'inherit',
						'scroll',
						'fixed',
						'local',
						'initial'
						); 
					foreach($attArray as $sat){
						$selectedsat = (count($styleQrs) > 0 && $styleQrs->bgimageattachment == $sat)?'selected':'';
						echo '<option '.$selectedsat.' value="'.$sat.'">'.ucfirst($sat).'</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="bgsize"><?php echo __('Background Size', 'allwebbox'); ?></label>
				<select name="style[bgsize]" id="bgsize">
					<option value=""><?php echo __('Select Background Position', 'allwebbox'); ?></option>
					<?php 
					$bgsizes = array('auto', 'cover', 'contain', 'initial', 'inherit');
					foreach($bgsizes as $sps){
						$psSelected =  (count($styleQrs) > 0 && $styleQrs->bgsize == $sps)?'selected':'';
						echo '<option '.$psSelected.' value="'.$sps.'">'.ucfirst($sps).'</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="buttonCollor"><?php echo __('Button Color', 'allwebbox'); ?></label>
				<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->buttoncollor:'';  ?>" id="buttonCollor" class="colorpicker"  type="text" name="style[buttoncollor]">
			</div>

			<div class="form-group">
				<label for="buttontext"><?php echo __('Button Text', 'allwebbox'); ?></label>
				<input value="<?php echo (count($styleQrs) > 0 && isset($styleQrs->buttontext))?$styleQrs->buttontext:'';  ?>" id="buttontext"  type="text" name="style[buttontext]">
			</div>
			<div class="form-group">
				<label for="buttontext"><?php echo __('Reset Button Text', 'allwebbox'); ?></label>
				<input value="<?php echo (count($styleQrs) > 0 && isset($styleQrs->rstbuttontext))?$styleQrs->rstbuttontext:'';  ?>" id="buttontext"  type="text" name="style[rstbuttontext]">
			</div>
			<div class="form-group">
				<label for="bgoverley">
				<input value="1" <?php echo (isset($styleQrs->bgoverley) && $styleQrs->bgoverley == 1)?'checked':''; ?> id="bgoverley" type="checkbox" name="style[bgoverley]">&nbsp;<?php echo __('Background Overley', 'allwebbox'); ?></label>
			</div>

			<div id="overleySection">
				<div class="form-group" id="overleycolorID">
					<label for="overleycolor"><?php echo __('Overley Color', 'allwebbox'); ?></label>
					<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->overleycolor:'';  ?>" id="overleycolor" class="colorpicker"  type="text" name="style[overleycolor]">
				</div>
				<div class="form-group">
					<label for="ovrltrans"><?php echo __('Overley Opacity', 'allwebbox'); ?></label>
					<input value="<?php echo (count($styleQrs) > 0)?$styleQrs->ovrltrans:'';  ?>" id="overleycolor"  type="number" min=".01" max="1.0" step="0.01" name="style[ovrltrans]">&nbsp;&nbsp;<small>(0.01 <?php echo __('to', 'allwebbox'); ?> 1.00)</small>
				</div>

			</div>
			<div class="col-md-12">&nbsp;</div>
		</div>
	</div>
</div> <!-- End Section -->


<!-- Start New Section -->
<!-- Journey & Content -->
<div id="JouurneynContent" class="sectionAllow">
	<div class="form-group">
		<div class="col-md-12">
		<div class="fix-padding">
			<h3>
			5. <?php echo __('Automated Campaigns', 'allwebbox'); ?>.
			<a id="visiableBasicQuestion4" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
			</h3>
		</div>

		</div>
	</div>

	<div class="form-group allwebContentBdy" id="formjourneyncontent">
		<div class="col-md-12">
			<p>
				<?php echo __('Set Journey for registered user. For create new journey click', 'allwebbox'); ?> <a href="<?php echo admin_url( $path = 'admin.php?page=email_marketing', $scheme = 'admin' ); ?>" title="New Journey"><?php echo __('here', 'allwebbox'); ?></a>. <?php echo __('Premium Content / Page only can see who register usign this form', 'allwebbox'); ?>. <br>
			</p>
			<div class="form-group">
				<?php $journeyQs = $wpdb->get_results('SELECT `id`,`j_name` FROM '.$jurneytable.'', OBJECT); ?>
				<label for="journey"><?php echo __('Journey', 'allwebbox'); ?>: </label>
				<select  name="journey" id="journey" class="form-control">
					<option><?php echo __('Select Journey', 'allwebbox'); ?></option>
					<?php 
						foreach($journeyQs as $sj){
							$sltd = ($sj->id == $journey)?'selected':'';
						 	echo '<option '.$sltd.' value="'.$sj->id.'">'.$sj->j_name.'</option>';
						}
					?>
				</select>
			</div>
			<br>
			<div class="from-group">
				<label for="content"><?php echo __('Premium Content', 'allwebbox'); ?>: </label>
				<?php 
						/*
						 * The WordPress Query class.
						 *
						 * @link http://codex.wordpress.org/Function_Reference/WP_Query
						 */
						$args = array(
							// Type & Status Parameters
							'post_type'   => 'any',
							'post_status' => 'any',
							'posts_per_page' => -1
						);
					
				$query = new WP_Query( $args );
				if($query->have_posts()):
				?>
				<select class="multipleSelect" multiple name="content[]" id="content" width="100%;">
					<?php while($query->have_posts()): $query->the_post(); global $post; 
						$slcted = (in_array($post->ID, $contents))?'selected':'';
					?>
						<option <?php echo $slcted; ?> value="<?php echo $post->ID; ?>"><?php echo get_the_title( $post->ID ); ?></option>
					<?php endwhile; ?>
				</select>
				<?php endif; ?>
			</div>


			<div class="col-md-12">&nbsp;</div>
		</div>
	</div>
</div> <!-- End Section -->

<div class="form-group">
	<div class="col-md-12">		
		<?php if(isset($_GET['id'])){ ?>
		<input type="hidden" name="edit" value="<?php echo $_GET['id'];  ?>"/>
		<?php } ?>
		<button type="submit" name="form_submit" class="btn blue pull-right"><?php echo (isset($_GET['id']))? 'Submit Edit':'Create Form';  ?></button>
	</div>
</div>


</form>
</div>
</div>
</div>
</div>
</div>
</div>

