<?php
function Email_marketing(){ 
global $wpdb;
$jurneytable = $wpdb->prefix . 'awe_journey';
$entryTbl   = $wpdb->prefix . 'awe_entry';
?>
<div class="wrap email_templates bgff p20">
  
<?php if(!isset($_GET['action'])): ?>
  <div class="emalitoptabs">
    <ul>
      <li><a class="active" href="#newjourney" title="New Journey">New Journey</a></li>
      <li><a href="#allJourney" title="All Journey">All Journey</a></li>
    </ul>
  </div>


<?php

/*$extable = $wpdb->get_row('SELECT `j_emails` FROM '.$jurneytable.' WHERE id=18', OBJECT);
$jDEcode = json_decode($extable->j_emails);*/

/*
* Journey email insert to DB
*/
$msg = '';
if(isset($_POST['j_time_default'])){

    $name         = $_POST['j_name'];
    $defaultUnit  = (isset($_POST['time_unit_default']))?$_POST['time_unit_default']:'day';
    if($defaultUnit == 'month'){
      $defaultTime  = $_POST['j_time_default'] * 30 * 24;  
    }elseif($defaultUnit == 'week'){
      $defaultTime  = $_POST['j_time_default'] * 7 * 24;  
    }elseif($defaultUnit == 'day'){
      $defaultTime  = $_POST['j_time_default'] * 24;  
    }else{
      $defaultTime  = $_POST['j_time_default']; 
    }
    
    $j_description   = $_POST['j_description'];
    $j_goal          = $_POST['j_goal'];
    $j_sender        = $_POST['j_sender'];
    $j_rep_email     = $_POST['j_rep_email'];
    $j_type          = $_POST['j_type'];


    unset($_POST['j_name']);
    unset($_POST['j_time_default']);
    unset($_POST['time_unit_default']);
    unset($_POST['j_description']);
    unset($_POST['j_goal']);
    unset($_POST['j_sender']);
    unset($_POST['j_rep_email']);
    unset($_POST['j_type']);



    //$s_email = array();
    for($i=0; count($_POST['j_emails']) > $i; $i++){
      if(isset($_POST['time_unit'][$i]) && $_POST['time_unit'][$i] == 'month'){
          $_POST['j_time'][$i] =   $_POST['j_time'][$i] * 30 * 24;
      }elseif(isset($_POST['time_unit'][$i]) && $_POST['time_unit'][$i] == 'week'){
         $_POST['j_time'][$i] =   $_POST['j_time'][$i] * 7 * 24;
      }elseif(isset($_POST['time_unit'][$i]) && $_POST['time_unit'][$i] == 'day'){
         $_POST['j_time'][$i] =   $_POST['j_time'][$i] * 24;
      }elseif(isset($_POST['time_unit'][$i]) && $_POST['time_unit'][$i] == 'minutes'){
         $_POST['j_time'][$i] =   $_POST['j_time'][$i] / 60;
      }else{
        $_POST['j_time'][$i] =   $_POST['j_time'][$i];
         $_POST['time_unit'][$i] = '0'; 
      }
    }

    /*echo '<hr/>';
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';*/

    $j_emails = json_encode($_POST);

    $insert = $wpdb->insert(
      $jurneytable, 
      array(
        'j_name'          => $name,
        'j_description'   => $j_description,
        'j_goal'          => $j_goal,
        'j_sender'        => $j_sender,
        'j_rep_email'     => $j_rep_email,
        'j_time'          => $defaultTime,
        'j_time_unit'     => $defaultUnit, 
        'j_emails'        => $j_emails,
        'j_type'          => $j_type

      ),
      array(
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s'
      )
    );

    if($insert){
        $msg .= 'success';
    }else{
        $msg .= 'faild';
    }
}// End Insert


 ?>

<!--<script src="https://rawgit.com/dbrekalo/attire/master/dist/js/build.min.js"></script>-->

<!-- Start New Journey -->
<div id="newjourney" class="active section">
  <div id="msg">
    <?php if($msg != '' && $msg == 'success'): ?>
        <div class="success msg"><h5>Journey Succefully Create.</h5></div>
    <?php elseif($msg !='' && $msg == 'faild'): ?>
        <div class="error msg"><h5>Journey Create Failed.</h5></div>
    <?php endif; ?>
  </div>
  <div class="innermail">
    <h2>New Journey</h2>
    <?php 
      $columns = $wpdb->get_col("DESC " . $entryTbl, 0);
    ?>
    <form action="" method="post" accept-charset="utf-8">
        <div class="form-group">
          <label for="journey_name">Journey Name <small><i>&nbsp;(Journey Name use for Email Subject)</i></small></label>
          <input type="text" required name="j_name" id="journey_name" value="" class="form-control p5">
        </div>



          <div class="form-group">
          <label for="journey_description">Description of journey</label>
          <textarea style="width:100%;" name="j_description" id="journey_description"></textarea>
        </div>
        <div class="form-group">
          <label for="journey_goal">Goal of journey</label>
          <input type="text" name="j_goal" id="journey_goal" value="" class="form-control"/>
        </div>
        <div class="form-group">
          <label for="nameofSender">Name of Sender</label>
          <input type="text" name="j_sender" id="nameofSender" value="" class="form-control"/>
        </div>
        <div class="form-group">
          <label for="jReplayEmail">Reply email address</label>
          <input type="email" name="j_rep_email" id="jReplayEmail" value="" class="form-control"/>
        </div>


        <div class="three-half left">
          <div class="form-group">
            <label for="journey_time">Email sent after <small><i>(Default)</i></small></label>
            <input type="number" id="journey_time" name="j_time_default" value="" class="form-control p5">
          </div>
        </div>
        <div class="three-half middle">
          <div class="form-group">
            <label for="time_unit">Each</label><br>
            <label><input type="radio" value="month" name="time_unit_default" />Month</label>&nbsp;&nbsp;
            <label><input type="radio" value="week" name="time_unit_default" />Week</label>&nbsp;&nbsp;
            <label><input type="radio" value="day" name="time_unit_default" />Day</label>&nbsp;&nbsp;
            <label><input type="radio" value="hour" name="time_unit_default" />Hour</label>
          </div>
        </div>
        <div class="three-half right">
          <div class="form-group">
            <label for="j_type">Message Type</label><br>
            <label><input type="radio" value="email" name="j_type" />Email</label>&nbsp;&nbsp;
            <label><input type="radio" value="pushtobrowser" name="j_type" />PUSH to Browser</label>&nbsp;&nbsp;
            <label><input type="radio" value="sms" name="j_type" />SMS</label>
          </div>
        </div>

        <br class="clear">
        <hr/>
        <br>
        <div class="newTemplate">
          <div class="tempDelete">
              <span alt="f158" class="dashicons dashicons-no"></span>
          </div>
          <div class="three-half left">
            <div class="form-group">
              <label for="journey_date">Email sent date <small><i>(Date)</i></small></label>
              <input type="text" style="padding:3.5px;" class="datepicker form-control p5" name="j_date[]" value="">
            </div>
          </div>
          <div class="three-half left middle">
            <div class="form-group">
              <label for="journey_time">Email sent after <small><i>(time)</i></small></label>
              <input type="number" id="journey_time" name="j_time[]" value="" class="form-control p5">
            </div>
          </div>
          <div class="three-half right">
            <div class="form-group">
              <label for="time_unit">Each</label><br>
              <label><input type="radio" value="month" name="time_unit[0]" />Month</label>&nbsp;&nbsp;
              <label><input type="radio" value="week" name="time_unit[0]" />Week</label>&nbsp;&nbsp;
              <label><input type="radio" value="day" name="time_unit[0]" />Day</label>&nbsp;&nbsp;
              <label><input type="radio" value="hour" name="time_unit[0]" />Hour</label>&nbsp;&nbsp;
              <label><input type="radio" value="minutes" name="time_unit[0]" />minutes</label>
              
            </div>
          </div>
          <div class="subject full">
            <div class="form-group">
              <label for="j_subject">Subject</label>
              <input type="text" value="" class="form-control" name="j_subject[]" />
            </div>
          </div>
          <div class="userParemeters">
            <label>User Parameters </label>
            <ul class="usParementslist">
              <?php foreach($columns as $sCl): ?>
                <li data-param="<?php echo $sCl; ?>">[<?php echo $sCl; ?>]</li>
              <?php endforeach; ?>
            </ul>
          </div>
          <textarea style="width:100%; min-height:120px;" name="j_emails[]" class="form-control tinymce">Hi [lastname],&nbsp;<div><br></div><div>Regards,</div></textarea>
        </div>
        <div id="addnewTemplate"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>

        <button type="submit" style="float:left;" class="button button-primary">Submit</button>
    </form>
  </div>
</div>

<div id="allJourney" class="hidden section">
    <div class="innermail">
        <h2>All Journey</h2>
        <table class="table table-striped">
          <caption>All Journey List</caption>
          <thead>
            <tr>
              <th>Sl</th>
              <th>Name</th>
              <th>Time Distance</th>
              <th>Template Count</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
        <?php 
          $alljurney = $wpdb->get_results('SELECT * FROM '.$jurneytable.'', OBJECT);
          $count = 1;
          foreach($alljurney as $sJurney){
            $emals = json_decode($sJurney->j_emails); ?>
              <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php echo $sJurney->j_name; ?></td>
                  <td><?php echo $sJurney->j_time ?> Day's</td>
                  <td><?php echo count($emals); ?></td>
                  <td>
                    <a href="<?php echo admin_url( $path = '/admin.php?page=email_marketing&action=edit&id=' . $sJurney->id, $scheme = 'admin' ); ?>" title="Edit">Edit</a>&nbsp;|&nbsp; 
                    <a class="journeyDelete" data-id="<?php echo $sJurney->id; ?>" href="#" title="Delete">Delete</a>
                  </td>
              </tr>
          <?php $count++; }
        ?>
        </tbody>
        </table>
    </div>  
</div>

  <?php else:
    require_once(ALWEBDIR . 'inc/edit-journey.php');
  endif; // End isset get action; ?>
  </div>
 <?php
 
 
 
}


 
 

?>