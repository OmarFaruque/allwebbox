<?php
  /*
  global $wpdb;
  $entry_table = $wpdb->prefix . 'awe_entry';
  $brandsms = $wpdb->prefix . 'brandsms';

*/
 

/// End Test 

function All_web_box(){
	 
}


function new_mail_from($old) {
return 'no-reply@firstsiteguide.com';
}
function new_mail_from_name($old) {
return 'Your Name Here';
}


ini_set("error_reporting", E_ALL);
global $wpdb;
$frmTable     = $wpdb->prefix . 'awe_forms';
$q_question   = $wpdb->prefix . 'awe_customq'; 
$entryTable   = $wpdb->prefix . 'awe_entry';
$optionsTbl   = $wpdb->prefix . 'awe_formqansvalue';
$svdFltrTbl   = $wpdb->prefix . 'savedflr_tbl';
$tableJourney = $wpdb->prefix . 'awe_journey';

$getFilter = $wpdb->get_results('SELECT * FROM '.$svdFltrTbl.'', OBJECT);


/*
* vest_journey add email to journey from backend
* Admin can add email from CRM page.
*/
if(isset($_GET['adto']) && isset($_GET['jid'])){
  $adTos = explode(',', $_GET['adto']);

  foreach($adTos as $sAdt){
    $updaAdto = $wpdb->update(
      $entryTable,
      array(
        'vest_journey' => $_GET['jid']
      ),
      array(
        'email' => $sAdt
      )      
    );//End Insert

    if($inserAdto){
      $updaAdto = true;
    }
  }

  if(isset($updaAdto) && $updaAdto == true){
    wp_redirect( admin_url( $path = '/admin.php?page=crm', $scheme = 'admin' ), $status = 302 );
  }
  

  


}







/*
* Export Import Section
*/
$csvDownLink = '';
if(isset($_POST['exportSubmit'])){
    $exQry = $wpdb->get_results($_POST['exportQry'], OBJECT);
    $fname = 'smar-marketing-entry';
    $fp = fopen(ALWEBDIR.'csv/'.$fname.'.csv', 'w');
    
    $fileHead = $wpdb->get_col("DESC {$entryTable}", 0);
    $remoArr = array('id', 'date');
    $fileHead = array_diff($fileHead, $remoArr);
    
    fputcsv($fp, $fileHead);

    for($e=0; count($exQry) > $e; $e++){
      unset($exQry[$e]->id);
      unset($exQry[$e]->date);
      fputcsv($fp, (array)$exQry[$e]);
    }
    fclose($fp);

    $csvDownLink .=  ALWEBURL . 'csv/' . $fname . '.csv';
} // End Export Process





/*
* Import date with CSV
*/
  $csvMsg ='';
  if(isset($_POST['csv'])):
    $fuleurl = get_attached_file($_POST['csv']);
    if (($handle = fopen($fuleurl, "r")) !== FALSE) {
       $data = array();
       while( ($line = fgetcsv($handle)) !== false) {
        $data[] = $line;
       }
       for($i=1; count($data) > $i; $i++ ){

          $insertData = array();
          foreach($data[0] as $k=>$d) $insertData[$d] = $data[$i][$k];
          $insert = $wpdb->insert(
            $entryTable,
            $insertData
          );
          if($insert){
            $csvMsg = 'Data upload sucess from CSV.';
          }else{
            $csvMsg = 'Data upload fail from CSV.';
          }
       }

       fclose($handle);
    }
  endif; // End if(isset($_POST['csv'])):




/*
* Saved Qury Delete
*/

if(isset($_GET['sflr_delete']) && $_GET['sflr_delete'] == true){
  
  $delete = $wpdb->delete(
      $svdFltrTbl, 
      array(
        'id' => $_GET['sflr_id'] 
      )    
  );

  if($delete){
    wp_redirect( admin_url( $path = '/admin.php?page=crm', $scheme = 'admin' ), $status = 302 );
  }
}

 
/* if(isset($_POST['form_submit'])){
   

	
     $idetiyFlag=false;
     $contactFlag=false;
     $profileFlag=false;
      
	
	
 	

    // Identi Query 
    /*for($i=1;$i<6;$i++) {
      if($_POST['default_question_ide_'.$i])  { 
        $idetiyFlag=true;
         $val=$_POST['default_question_ide_'.$i]; 
         echo $qry.="'$val',";    
       }           
    } */
    // $data_ans=rtrim($qry,",");
     /*$qry="SELECT * FROM wp_awe_identification_ans WHERE question_ans in($data_ans) GROUP BY entry_id";
     $formSearchedData=$wpdb->get_results($qry, OBJECT);
     $ident_enty="";

     foreach($formSearchedData as $val) {
     $ident_enty.=$val->entry_id.",";
     }
     
     echo $ident_enty_o=rtrim($ident_enty,",");
     
     */
     
    // Contact Query default_question_cont_1 
    /* $qryC="";
     for($i=1;$i<9;$i++) {
      if($_POST['default_question_cont_'.$i])  { 
          $val=$_POST['default_question_cont_'.$i]; 
          $contactFlag=true;
          $qryC.="'$val',";    
       }           
    }
    
      $data_ans_con=rtrim($qryC,",");
      $qryC="SELECT * FROM wp_awe_contactinfo_ans WHERE question_ans in($data_ans_con) GROUP BY entry_id";
     $formSearchedData=$wpdb->get_results($qryC, OBJECT);
     $contact_enty="";

     foreach($formSearchedData as $val) {
     $contact_enty.=$val->entry_id.",";
     }*/
       //  $ident_enty_c=rtrim($contact_enty,",");
     
 
     // profile Query  default_question_pro_1
    /*$qryP="";
     for($i=1;$i<9;$i++) {
      if($_POST['default_question_pro_'.$i])  { 
          $val=$_POST['default_question_pro_'.$i]; 
          $profileFlag=true;
          $qryP.="'$val',";    
       }           
    }*/
    
      /*$data_ans_pro=rtrim($qryP,",");
      $qryP="SELECT * FROM wp_awe_profileques_ans WHERE question_ans in($data_ans_pro) GROUP BY entry_id";
     $formSearchedData=$wpdb->get_results($qryP, OBJECT);
     $contact_enty="";

     foreach($formSearchedData as $val) {
     $contact_enty.=$val->entry_id.",";
     }*/
      //   $ident_enty_p=rtrim($contact_enty,",");
   
 
 
 
 
 
 
 
 
    // Two join tabled records 
     /*if($idetiyFlag) {
     $ident_enty=rtrim($ident_enty_o,",");  
     }
     if($contactFlag) {
     $ident_enty=rtrim($ident_enty_c,",");
     } 
     if($profileFlag) {
     $ident_enty=rtrim($ident_enty_p,",");
     }*/
     
     
    /*  if($idetiyFlag==true && $contactFlag==true) {
     
      echo $finalQuery= "SELECT * FROM wp_awe_identification_ans WHERE question_ans in($data_ans) GROUP BY entry_id UNION ALL SELECT * FROM wp_awe_contactinfo_ans WHERE question_ans in($data_ans_con) GROUP BY entry_id";
    $finalSearchData=$wpdb->get_results($finalQuery, OBJECT);
    
      
     foreach($finalSearchData as $val) {
      $finalEntry[]=$val->entry_id;
     }  
      $finalEntry= array_unique($finalEntry); 
      $ident_enty=implode(',',$finalEntry);
         $ident_enty;
     
     }*/
     
      /*-if($idetiyFlag==true && $contactFlag==true && $profileFlag==true) {
     
      echo $finalQuery= "SELECT * FROM wp_awe_identification_ans WHERE question_ans in($data_ans) GROUP BY entry_id UNION ALL SELECT * FROM wp_awe_contactinfo_ans WHERE question_ans in($data_ans_con) GROUP BY entry_id UNION ALL SELECT * FROM wp_awe_profileques_ans WHERE question_ans in($data_ans_pro) GROUP BY entry_id";
    $finalSearchData=$wpdb->get_results($finalQuery, OBJECT);
    
      
     foreach($finalSearchData as $val) {
      $finalEntry[]=$val->entry_id;
     }  
      $finalEntry= array_unique($finalEntry); 
      $ident_enty=implode(',',$finalEntry);
         $ident_enty;
     
     }   */
     
 /*}*/
?>




<div class="page-content-wrapper allwebbox crm mt30">
<div class="page-content">
<!-- <h3 class="page-title"> Form Name
<small></small>
</h3> -->
<div class="clearfix"></div>
<div class="row">
<div class="col-md-12 col-sm-12">
  <div class="portlet light bordered" style="margin-bottom:0px;">
    <?php if(!isset($_GET['send_mails']) && !isset($_GET['details'])): ?>
    <div class="portlet-title">
      <div class="caption">
      <!-- <i class="fa fa-check-square-o"></i> -->
        <span class="caption-subject font-dark bold uppercase">Search Your Records</span>
        <button type="button" role="button" style="background-color: transparent;border-color: transparent;color: #000;font-size: 18px;" class="popovers" data-container="body" data-trigger="focus hover" data-html="true" data-placement="bottom" data-original-title="Ayuda">
        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  <div class="portlet-body form">

  <div class="form-body">
    <div class="form-group" id="name_form">
      <div class="col-md-12">
      <label>
      <!-- 
      --> 
      </div>
    </div>



  <div class="form-group" id="crmWrap">
  <div class="col-md-12">
  
    <!-- All Brand -->

  <div class="sectionAllow crm <?php echo (isset($_GET['bdt']) && $_GET['bdt'] != '')?'active':''; ?>">
    <div class="form-group">
      <div class="col-md-12">
        <h3>
        <?php echo __("All Brand's", "allwebbox"); ?> <small><i>(<?php echo __('sms & message settings', 'allwebbox'); ?>)</i></small>
        <a id="savedFilter" class="visiableSection <?php echo (isset($_GET['bdt']) && $_GET['bdt'] != '')?'active':''; ?>" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
        </h3>
      </div>
    </div>
    <div class="form-group allwebContentBdy <?php echo (isset($_GET['bdt']) && $_GET['bdt'] != '')?'active':''; ?>" id="savedFilterBody">
      <div class="col-md-12">
        <div class="info filter">
          <span><?php echo __('Click on the brand for setting sms & message.', 'allwebbox'); ?></span>

        </div>
        <div class="fix-padding">
          <?php require_once( ALWEBDIR . 'inc/smsnmessage.php'); ?>
        </div>
      </div>
    </div>
  </div>
  <!-- End All Brand -->
  <!-- Saved Filter -->
  <form action="" method="post" enctype="application/x-www-form-urlencoded" class="form-horizontal" role="form">





  <div class="sectionAllow crm">
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
        <b>Identification Questions</b>
        <a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span>Basic Information  </span></a>
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
      'First Name'  => 'First Name', 
      'Last Name'   => 'Last Name', 
      'Nick Name'   => 'Nick Name',
      'Salute (Mr, Ms, Miss, Dr, etc)'  => 'Salute (Mr, Ms, Miss, Dr)',
      'ID Number'   => 'ID Number'
     );
     
    $contact_ques = array(
      'country', 'city', 'address', 'email', 'subscribed', 'mobile', 'Phone Number', 'facebook', 'twitter', 'linkedin',  'instagram', 'google+', 'pinterest', 'youtube', 'whatsapp'
    );
    

    $profile_ques  = array('Gender', 'Date of Birth', 'Civil Status', 'Academic Level');

    $querystr="select * from $q_question";
    $allCreated_ques=$wpdb->get_results($querystr, OBJECT); 


    $custom_quesm   = array('Date of visit', 'In Charge', 'Economic Activity',  'Presence in Social Networks', 'Has a Website', 'Your Website Has');
    
    
    $allCreatedArray  = array();
    foreach($allCreated_ques as $scQ) array_push($allCreatedArray, $scQ->questions);
    $custom_ques  = array_unique(array_merge($custom_quesm, array_filter($allCreatedArray)), SORT_REGULAR);

    //$custom_ques    = array_unique(array_merge($customQs, $custom_ques_all), SORT_REGULAR);

     /*$querystr="select * from wp_awe_contactinfo";
     $contact_ques = $wpdb->get_results($querystr, OBJECT);
     
     
     $querystr="select * from wp_awe_profileques";
     $profile_ques = $wpdb->get_results($querystr, OBJECT);
     

     $questionall="select * from $q_question";
     $custom_ques = $wpdb->get_results($querystr, OBJECT);*/

     $ansTypes = array('Text', 'Description', 'Single selection', 'Multiple selection', 'Number', 'Date', 'Email');

    ?>
    <div class="row"> 
      <div class="col-md-12">
      <?php 
      $cnt = 1;
      foreach($identity_ques as $val => $si) {
        $val = str_replace(' (Mr, Ms, Miss, Dr)', '', $si);
        $value = (isset($_POST['form_submit']))?$_POST['idQ'][strtolower(str_replace(' ', '', $val))]:'';
        $noAns = (isset($_POST['not_answered'][strtolower(str_replace(' ', '', $val))]))?$_POST['not_answered'][strtolower(str_replace(' ', '', $val))]:'';
      ?>
      <div class="col-md-3 <?php echo ($cnt%5 == 0)?'last':''; ?>">

        <label for="<?php echo strtolower(str_replace(' ', '', $val)); ?>" ><?php echo $si; ?></label>
        <input type="text" name="idQ[<?php echo strtolower(str_replace(' ', '', $val)); ?>]" value="<?php echo $value; ?>">
        <label class="mt-checkbox">
          <input class="noansweractive" <?php echo ($noAns)?'checked':''; ?> name="not_answered[<?php echo strtolower(str_replace(' ', '', $val)); ?>]" value="1" type="checkbox">Not Answered 
          <span></span> 
        </label>
      </div>
       <?php $cnt++; } ?>
      </div>
    </div>
     
    <hr>
    <div class="row"> 
    <div class="full col-md-12">
    <div class="fix-padding" >
    <h4>
      <b>Contact Information</b>
      <a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span>Contact Information  </span></a>
    </h4>
    </div>
    <div class="row">


    
    <?php 
    $conIn= 1;
     foreach($contact_ques as $sC) {
       $noAns = (isset($_POST['not_answered'][strtolower($sC)]))?$_POST['not_answered'][strtolower($sC)]:'';
       $val =  (isset($_POST['form_submit']))?$_POST['contact_info'][str_replace('+', '', str_replace(' ', '', strtolower($sC)))]:'';
       
    ?>
    <div class="col-md-3 <?php echo ($conIn%5 == 0)?'last':''; ?>">
      <label for="<?php echo strtolower($sC); ?>"><?php echo ucfirst($sC); ?></label>
      <input type="text" name="contact_info[<?php echo  str_replace('+', '', str_replace(' ', '', strtolower($sC))); ?>]" class="form-control" value="<?php echo $val; ?>"/>
      <label class="mt-checkbox">
          <input <?php echo ($noAns)?'checked':''; ?> name="not_answered[<?php echo strtolower($sC); ?>]" value="1" type="checkbox">Not Answered 
          <span></span> 
        </label>
    </div>
     <?php $conIn++; } ?>

    </div>  
    </div> <!-- Half -->
    <div class="full col-md-12">
      
      <div class="fix-padding" >
        <h4>
          <b>Profiling questions</b>
          <a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i> <span>Profile Data </span></a>
        </h4>
      </div>
        <?php 
         foreach($profile_ques as $sP) {
           $noAns = (isset($_POST['not_answered'][str_replace(' ', '', strtolower($sP))]))?$_POST['not_answered'][str_replace(' ', '', strtolower($sP))]:'';
           $Pval =  (isset($_POST['profile'][str_replace(' ', '', strtolower($sP))]))?$_POST['profile'][str_replace(' ', '', strtolower($sP))]:'';

        ?>
          <div class="col-md-3">
            <label for="<?php echo str_replace(' ', '', strtolower($sP));  ?>"><?php echo ucfirst($sP);  ?></label>
            <?php switch($sP): 
                case 'Date of Birth':
                $Pval_start = '';
                $Pval_end = '';
                if(isset($_POST['form_submit'])){
                  $Pval_start .=  $_POST['profile'][str_replace(' ', '', strtolower($sP)) . '_start']; 
                  $Pval_end .=  $_POST['profile'][str_replace(' ', '', strtolower($sP)) . '_end']; 
                }
            ?><br/>
            <label style="margin-right: 0px; overflow: hidden; display: block; float: left; width: 100%; line-height: 30px;">Start: <input style="max-width:70%; float:right;" type="text" class="datepicker" placeholder="yyyy-mm-dd" id="<?php echo str_replace(' ', '', strtolower($sP)); ?>_start" name="profile[<?php echo str_replace(' ', '', strtolower($sP)) . '_start';  ?>]" value="<?php echo $Pval_start; ?>"></label><br/>
            <label style="margin-right: 0px; overflow: hidden; display: block; float: left; width: 100%; line-height: 30px;">End: <input style="max-width:70%;  float:right;" type="text" class="datepicker" placeholder="yyyy-mm-dd" id="<?php echo str_replace(' ', '', strtolower($sP)); ?>_end" name="profile[<?php echo str_replace(' ', '', strtolower($sP)). '_end';  ?>]" value="<?php echo $Pval_end; ?>"></label>
            <?php break; default: ?>
            <input type="text" id="<?php echo str_replace(' ', '', strtolower($sP)); ?>" name="profile[<?php echo str_replace(' ', '', strtolower($sP));  ?>]" value="<?php echo $Pval; ?>">
            <?php endswitch; ?>
            <label class="mt-checkbox">
                <input <?php echo ($noAns)?'checked':''; ?> name="not_answered[<?php echo str_replace(' ', '', strtolower($sP));  ?>]" value="1" type="checkbox">Not Answered 
                <span></span> 
            </label>
          </div>
         <?php } ?>

    </div>
    </div> 
    </div>
    </div>
  </div> 
  <!-- End Section -->

  <!-- Start New Section -->
  <div class="sectionAllow crm">
    <div class="form-group">
      <div class="col-md-12">
        <div class="fix-padding">
            <h3>
              2. Custom Questions.
              <a id="visiableBasicQuestion2" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
            </h3>
        </div>
      </div>
    </div>
    <div class="form-group allwebContentBdy" id="custom_questions">
    <div class="col-md-12">
    <div class="row">


      <?php 

      $count = 1;
      
      /*echo '<pre>';
      print_r($custom_ques);
      echo '</pre>';*/
      foreach($custom_ques as $cQ) {
         $noAns = (isset($_POST['other_noans'][$cQ]))?$_POST['other_noans'][$cQ]:'';
         $cQval =  (isset($_POST['customQus'][$cQ]))?$_POST['customQus'][$cQ]:'';
         $ansQry = $wpdb->get_row('SELECT `row_id`, `answer_type` FROM '.$q_question.' WHERE `questions`="'.$cQ.'"', OBJECT);

        
         
      ?>
          <div class="col-md-3 <?php echo ($count%2 == 0)?'last':'';  ?>">
            <label for="<?php echo str_replace(' ', '', strtolower($cQ));  ?>"><?php echo ucfirst($cQ);  ?></label> 
            <?php 

              if($ansQry && $ansQry->answer_type == 3 || $ansQry && $ansQry->answer_type == 4): 
                $itemsQus = $wpdb->get_results('SELECT `ques_value` FROM '.$optionsTbl.' WHERE `entry_id`='.$ansQry->row_id.'');
                echo '<div class="radio">';
                foreach($itemsQus as $itmS):
                  $checked = ($cQval == $itmS->ques_value)?'checked':'';
                ?> 
                  <label><input <?php echo $checked; ?> type="radio" value="<?php echo $itmS->ques_value;  ?>" name="customQus[<?php echo $cQ;  ?>]" /><?php echo $itmS->ques_value; ?></label>&nbsp;&nbsp;
            <?php endforeach; 
              echo '</div>';
              else: ?>
            <?php switch($cQ): 
              case 'Date of visit':
            ?>
            <input type="text" placeholder="yyyy-mm-dd" class="datepicker" id="<?php echo str_replace(' ', '', strtolower($cQ)); ?>" name="customQus[<?php echo $cQ;  ?>]" value="<?php echo $cQval; ?>">
            <?php break; default: ?>
            <input type="text" id="<?php echo str_replace(' ', '', strtolower($cQ)); ?>" name="customQus[<?php echo $cQ;  ?>]" value="<?php echo $cQval; ?>">
            <?php endswitch; ?>
            <?php endif; // End if($ansQry->answer_type == 3 || $ansQry->answer_type == 4): ?>

            <label class="mt-checkbox">
                <input <?php echo ($noAns)?'checked':''; ?> name="other_noans[<?php echo $cQ; ?>]" value="1" type="checkbox">Not Answered 
                <span></span> 
            </label>
          </div> 
      <?php $count++; } /*Chekc if empty*/  ?>     
    



    </div>

    </div>
    </div>
  </div>
  <!-- End Section -->


    <div class="sectionAllow crm">
    <div class="form-group">
      <div class="col-md-12">
        <h3>
        <?php echo __('Saved Filter', 'allwebbox'); ?>
        <a id="savedFilter" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
        </h3>
      </div>
    </div>
    <div class="form-group allwebContentBdy" id="savedFilterBody">
      <div class="col-md-12">
        <div class="info filter"><span>Click on the text body for view saved filter.</span></div>
        <div class="fix-padding">
            <?php 
              if($getFilter && count($getFilter) > 0): 
                foreach($getFilter as $sfltr):
            ?>
            <p>
              <a href="<?php echo admin_url( $path = '/admin.php?page=crm&sflr=' .$sfltr->id , $scheme = 'admin' ) ?>"><span class="leftText"><?php echo $sfltr->filter_name; ?></span></a>
              <i class="fa fa-caret-down" aria-hidden="true"></i></p>
            <div class="filtrDescription hidden">
              <div class="disInner"><?php echo $sfltr->f_description; ?></div>
              <div class="deleteF float-right">
                <a href="<?php echo admin_url( $path = '/admin.php?page=crm&sflr_id=' . $sfltr->id . '&sflr_delete=' . true, $scheme = 'admin' ) ?>">Delete</a>
              </div>
            </div>
            <?php 
                endforeach;
              endif; 
            ?>

        </div>
      </div>
    </div>
  </div>

  <!-- End Saved Filter -->


  <div class="sectionAllow crm">
    <div class="form-group">
      <div class="col-md-12">
        <div id="filInsertMsg"></div>
        <label for="crmQry">Do you like to use this Filter in future?</label>&nbsp;&nbsp;
        <label><input type="checkbox" <?php echo (isset($_POST['crmQry']))?'checked':''; ?> value="1" name="crmQry" id="crmQry" class="checkbox"/> Yes</label>
      </div>
    </div>
    <div id="filterSectionQry" class="<?php echo (!isset($_POST['crmQry']))?'hidden':'';?> mt20">

      <div class="form-group">
        <label for="filter_name">Filter Name</label>
        <input type="text" name="filter_name" id="filter_name" value="<?php echo (isset($_POST['filter_name']))?$_POST['filter_name']:''; ?>" class="form-control" />
      </div>

      <div class="form-group">
        <label for="f_description">Filter Description</label>
        <textarea name="f_description" id="f_description" class="form-control"><?php echo (isset($_POST['filter_name']))?$_POST['f_description']:''; ?></textarea>
      </div>
    </div>

  </div>


  <button type="submit" name="form_submit" class="btn blue pull-right">Search</button>
  </form>
  </div>
  </div>

  </div>
  </div>


<?php elseif(isset($_GET['details'])): // if set $_GET['details'] ?>
  <?php require_once(ALWEBDIR . 'inc/detailsEntry.php'); ?>

<?php elseif(isset($_GET['send_mails'])): ?>
  <?php require_once(ALWEBDIR . 'inc/sendSEmail.php'); ?>
<?php endif; // check if set get details // !isset($_GET['details']) ?>


</div>
</div>
</div>
</div>
</div>
</div>

		
<?php

 if(isset($_POST['form_submit'])) 
 {

    $qry='SELECT * FROM '.$entryTable.' WHERE ';
    if(isset($_POST['not_answered']) && $_POST['not_answered']['firstname']){
      $qry .= '`firstname` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['lastname']){
      $qry .= '`lastname` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['nickname']){
      $qry .= '`nickname` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['salute']){
      $qry .= '`salute` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['idnumber']){
      $qry .= '`idnumber` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['country']){
      $qry .= '`country` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['city']){
      $qry .= '`city` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['address']){
      $qry .= '`address` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['email']){
      $qry .= '`email` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['subscribed']){
      $qry .= '`subscribed` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['mobile']){
      $qry .= '`mobile` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['phone number']){
      $qry .= '`phonenumber` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['facebook']){
      $qry .= '`facebook` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['twitter']){
      $qry .= '`twitter` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['linkedin']){
      $qry .= '`linkedin` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['instagram']){
      $qry .= '`instagram` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['google+']){
      $qry .= '`google` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['pinterest']){
      $qry .= '`pinterest` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['youtube']){
      $qry .= '`youtube` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['whatsapp']){
      $qry .= '`whatsapp` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['gender']){
      $qry .= '`gender` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['dateofbirth']){
      $qry .= '`dateofbirth` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['civilstatus']){
      $qry .= '`civilstatus` ="" AND ';
    }
    if(isset($_POST['not_answered']) && $_POST['not_answered']['academiclevel']){
      $qry .= '`academiclevel` ="" AND ';
    }

    // Query Custom Question no answer
    if(isset($_POST['other_noans']) && count($_POST['other_noans']) > 0 ){
      foreach($_POST['other_noans'] as $k => $sother){
        $qry .= '`others` REGEXP "'.$k.'=0" AND ';
      } //endforeach
    }

    // Query Basic Question
    if(isset($_POST['idQ']) && count($_POST['idQ']) > 0){
      foreach($_POST['idQ'] as $iq => $siq){

        $qry .= ($siq != '')?'`'.$iq.'` LIKE "%'.$siq.'%" AND ':'';

      }
    }

    // Query contact_info
    if(isset($_POST['contact_info']) && count($_POST['contact_info']) > 0){
      foreach($_POST['contact_info'] as $kC => $ski){
        $qry .= ($ski != '')?'`'.$kC.'` LIKE "%'.$ski.'%" AND ':'';
      }
    }

    // Profile Query (Filter)
    /*echo '<pre>';
    print_r($_POST['profile']);
    echo '</pre>';*/
    if(isset($_POST['profile']) && count($_POST['profile']) > 0){
      if($_POST['profile']['dateofbirth_start'] != '' && $_POST['profile']['dateofbirth_end'] == ''){
          $qry .= '(`dateofbirth` BETWEEN "'.$_POST['profile']['dateofbirth_start'].'" AND curdate()) AND ';
      }elseif($_POST['profile']['dateofbirth_start'] == '' && $_POST['profile']['dateofbirth_end'] != ''){
        $qry .= '(`dateofbirth` BETWEEN "1900-01-01" AND "'.$_POST['profile']['dateofbirth_end'].'") AND ';
      }elseif($_POST['profile']['dateofbirth_start'] != '' && $_POST['profile']['dateofbirth_end'] != ''){
        $qry .= '(dateofbirth BETWEEN "'.$_POST['profile']['dateofbirth_start'].'" AND "'.$_POST['profile']['dateofbirth_end'].'") AND ';
      }
      unset($_POST['profile']['dateofbirth_start']);
      unset($_POST['profile']['dateofbirth_end']);
      foreach($_POST['profile'] as $pk => $sp ){        
        $qry .= ($sp != '')?'`'.$pk.'` LIKE "%'.$sp.'%" AND ':'';

      }
    }

    // Query Custom Question
    if(isset($_POST['customQus']) && count($_POST['customQus']) > 0 ){
      foreach($_POST['customQus'] as $ck => $qsQ){
        $qry .= ($qsQ !='')?'`others` REGEXP "'.$ck.'='.$qsQ.'" AND ':'';
      } //endforeach
    }

//Economic Activity='', Presence in Social Networks='', Has a Website='', Your Website Has='', sfssfsdfsdf='', Single Selection='Option1sfd', stwrwrwer9=''
    $qry .= '`form_id` != "" ORDER BY `date` DESC';
    
    $formsEntryData=$wpdb->get_results($qry, OBJECT);
 }elseif(isset($_GET['sflr']) && $_GET['sflr'] != ''){ // Qry Saved Filter
    $savedFltrqry = $wpdb->get_row('SELECT `sv_filter` FROM '.$svdFltrTbl.' WHERE id='.$_GET['sflr'].'', OBJECT);
    $qry = $savedFltrqry->sv_filter;
    $formsEntryData=$wpdb->get_results($savedFltrqry->sv_filter, OBJECT);
 }
 else
 {
    $qry="select * from ".$entryTable." ORDER BY `date` DESC";
    $formsEntryData=$wpdb->get_results($qry, OBJECT);
 }

 if(isset($_POST['crmQry']) && isset($_POST['form_submit'])){

    //$intdQry = json_encode($formsEntryData);
    //echo 'intdQry: ' . $qry . '<br/>';
    $instdFilter = $wpdb->insert(
        $svdFltrTbl,
        array(
          'filter_name'     => $_POST['filter_name'],
          'f_description'   => $_POST['f_description'],
          'sv_filter'       => $qry 
        ),
        array(
          '%s',
          '%s',
          '%s'
        )
    );

    if($instdFilter){
      echo '<script>
        jQuery("#filInsertMsg").html("<span>Filter Successfully Save.</span>");
      </script>';
    }

    //filInsertMsg

 }

 
?>







<?php if(!isset($_GET['send_mails']) && !isset($_GET['details']) && is_array($formsEntryData)): ?>

<div id="importExport" class="page-content-wrapper allwebbox crm pt50 pb10">
  <div class="page-content">
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-md-12 col-sm-12">

      <div class="portlet light bordered">
        <div id="csvSuccess">
          <?php if(isset($csvMsg)): ?>
             <div class="csvsMesg message">
                 <span><?php echo $csvMsg; ?></span>
              </div>
          <?php endif; ?>
        </div>
        <div class="threehalf">
          <h3><b>Import / Export</b></h3>
        </div>
        <div class="threehalf bttn">
          <?php if($csvDownLink == ''): ?>
          <form action="<?php echo admin_url( $path = '/admin.php?page=crm#importExport', $scheme = 'admin' ); ?>" method="POST" accept-charset="utf-8">
              <input type="hidden" value="<?php echo $qry; ?>" name="exportQry" />
              <input type="submit" name="exportSubmit" value="Export" class="button button-primary large">
          </form>
        <?php else: ?>
          <a download class="button button-primary" href="<?php echo $csvDownLink; ?>">Download</a>
        <?php endif; ?>

        </div>
        <div class="threehalf bttn" style="position:relative;">
          <form action="<?php echo admin_url( $path = '/admin.php?page=crm#importExport', $scheme = 'admin' ); ?>" method="POST" enctype="multipart/form-data" accept-charset="utf-8">
              <input type="hidden" class="regular-text" name="csv" id="csv" value="" />
              <button id="taggr_upload" class="button button-primary">Upload CSV</button>
              <script type="text/javascript">
                  jQuery(document).ready(function($) {
                      // Uploading files
                      var file_frame;
                      var extensions = ["csv"];


                      jQuery('#taggr_upload').on('click', function( event ) {

                          event.preventDefault();

                          // If the media frame already exists, reopen it.
                          if ( file_frame ) {
                              file_frame.open();
                              return;
                          }

                          // Create the media frame.
                          file_frame = wp.media.frames.file_frame = wp.media({
                              title: jQuery( this ).data( 'uploader_title' ),
                              button: {
                                  text: jQuery( this ).data( 'uploader_button_text' ),
                              },
                              multiple: false  // Set to true to allow multiple files to be selected
                          });

                          // When an image is selected, run a callback.
                          file_frame.on( 'select', function() {
                              // We set multiple to false so only get one image from the uploader
                              var attachment = file_frame.state().get('selection').first().toJSON();
                              // Do something with attachment.id and/or attachment.url here
                              
                              
                              $last3 = attachment.filename.substr(attachment.filename.length - 3);
                              if($last3 == 'csv'){
                                $('p.fileInst, #csv_upload_process').remove();
                                $('#csv').val(attachment.id);
                        $('#taggr_upload').before('<p class="fileInst">'+attachment.filename+'</p>');
                        $('#taggr_upload').after('<button style="margin-left:15px;" id="csv_upload_process" class="button button-primary"><?php echo _e('Click to process', 'allwebbox'); ?></button>');
                              }else{
                                $('p.fileInst').remove();
                                $('#taggr_upload').before('<p class="fileInst" style="color:red;"><?php echo _e('Please Select a CSV file.', 'allwebbox'); ?></p>');
                              }

                          });

                          // Finally, open the modal
                          file_frame.open();
                      });
                  });
              </script>

          </form>

        </div>
         <div class="threehalf bttn">
            <a class="button button-primary" href="<?php echo  ALWEBURL;  ?>csv/demo.csv" title="emporter.csv" download >Download Demo csv</a>
          </div>

      </div>
    </div>
  </div>
  </div>
</div>
<hr>
<form style="max-width:1150px;" method='GET' action='<?php echo admin_url( $path = '/admin.php?page=crm', $scheme = 'admin' ); ?>'>
<input type="hidden" name="asqQsn" value="">
<input type="hidden" name="page" value="crm">
<input type="submit" name="a_action" class="btn blue pull-right" value="<?php echo __('Start Campaign', 'allwebbox'); ?>" />
<input id="askQButton" type="submit" name="a_action" class="btn blue pull-right" value="<?php echo __('Send Email / SMS', 'allwebbox'); ?>" />

<table id="searchReasult" class="display" cellspacing="0" width="100%">
        <thead>
            <tr> <th><input type='checkbox' id='selbtn' onClick='selectAll()'/></th>
                <th><?php echo __('Name', 'allwebbox'); ?></th>
                <th><?php echo __('Email', 'allwebbox'); ?> </th>
                <th><?php echo __('Form Name', 'allwebbox'); ?></th>
                <th><?php echo __('Journey Name', 'allwebbox'); ?></th>
                <th><?php echo __('Entry Date', 'allwebbox'); ?></th>
                <th><?php echo __('Action', 'allwebbox'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><input type='checkbox'/></th>
                <th><?php echo __('Name', 'allwebbox'); ?></th>
                <th><?php echo __('Email', 'allwebbox'); ?> </th>
                <th><?php echo __('Form Name', 'allwebbox'); ?></th>
                <th><?php echo __('Journey Name', 'allwebbox'); ?></th>
                <th><?php echo __('Entry Date', 'allwebbox'); ?></th>
                <th><?php echo __('Action', 'allwebbox'); ?></th>
            </tr>
        </tfoot>
        <tbody>
        <?php  
		 $html="";
		 foreach($formsEntryData as $value) {
             $fname = $wpdb->get_row('SELECT t.`form_name`, j.`j_name` FROM '.$frmTable.' t LEFT JOIN '.$tableJourney.' j ON t.`journey` = j.`id` WHERE t.`row_id`='.$value->form_id.'', OBJECT);
             $nameFL = ($value->firstname != '')?$value->firstname:$value->lastname;
             $JName = $fname->j_name;
             $altFormN = '';
             if($JName == '' && $value->vest_journey != ''){
                $altFQry = $wpdb->get_row('SELECT `j_name` FROM '.$tableJourney.' WHERE id='.$value->vest_journey.'', OBJECT);
                $JName = ($altFQry)?$altFQry->j_name:'';
             }

		         $html.="<tr>
		                 <td style='text-align:center;'>
                     <input type='checkbox' name='send_mails[]' value='".$value->email."'/>
                     <input type='checkbox' class='hidden' name='send_sms[]' value='".str_replace(' ', '',$value->mobile)."' />
                     </td>  
			               
                     <td> <a href='".admin_url( $path = 'admin.php?page=crm&details=' . $value->id . '&form-id='.$value->form_id , $scheme = 'admin' )."'>".$nameFL."</a></td>
                     <td>".$value->email."</td>
					           <td>".$fname->form_name."</td>
                     <td>".$JName."</td>
                     <td style='text-align:center;'>".date('F j, Y', strtotime($value->date))."</td>
                     <td style='text-align:center;'>
                      <a href='#' data-id='".$value->id."' class='delete_entry button button-primary'>Delete</a>
                      |
                      <a href='".admin_url( $path = 'admin.php?page=crm&details=' . $value->id . '&form-id='.$value->form_id , $scheme = 'admin' )."' class='button button-primary details'>Details</a> 
                     </td>
                     </tr>";
		    }
           echo $html;			
		?>			 
        </tbody>
    </table>
</form>
<div id="addToJourneyForm" class="hidden">
  <div class="adtoInner">
    <div class="form-group">
      <label for="allJourney">Select Journey</label>
      <select name="alljourney" id="alljourney" class="form-control">
        <option value="">Select Journey</option>
        <?php 
          $qeryAllJ = $wpdb->get_results('SELECT `id`, `j_name` FROM '.$tableJourney.'', OBJECT);
          foreach($qeryAllJ as $sJ):
        ?>
          <option value="<?php echo $sJ->id; ?>"><?php echo $sJ->j_name; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
</div>

<?php endif; // check is array or not ?>

