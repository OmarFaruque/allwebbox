<?php 
/*
* Edit Journey
*/
//global $wpdb;
//$jurneytable = $wpdb->prefix . 'awe_journey';

/*
* Update Query
*/
$columns = $wpdb->get_col("DESC " . $entryTbl, 0);



if(isset($_POST['edit_id'])){




 $name         = $_POST['j_name'];
    $defaultUnit  = $_POST['time_unit_default'];
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
    $eid             = $_POST['edit_id']; 
    $j_type          = $_POST['j_type'];

    

    unset($_POST['j_name']);
    unset($_POST['j_time_default']);
    unset($_POST['time_unit_default']);
    unset($_POST['j_description']);
    unset($_POST['j_goal']);
    unset($_POST['j_sender']);
    unset($_POST['j_rep_email']);
    unset($_POST['edit_id']);
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
        $_POST['j_time'][$i]    =   $_POST['j_time'][$i];
        $_POST['time_unit'][$i] = '0'; 
      }

      //echo 'post time: ' . $_POST['j_time'][$i] . '<br/>';
    }


  $j_emails = json_encode($_POST);
  

	$update = $wpdb->update(
		$jurneytable,
		array(
			'j_name' 	        => $name,
      'j_description'   => $j_description,
      'j_goal'          => $j_goal,
      'j_sender'        => $j_sender,
      'j_rep_email'     => $j_rep_email,
			'j_time' 	        => $defaultTime,
      'j_time_unit'     => $defaultUnit, 
			'j_emails'	      => $j_emails,
      'j_type'          => $j_type
		),
		array(
			'id' 	=> $eid
		)
	);


}
$journey = $wpdb->get_row('SELECT * FROM '.$jurneytable.' WHERE `id`='.$_GET['id'].' ', OBJECT);

$templates = json_decode($journey->j_emails);
$timeUnit = (array)$templates->time_unit;
ksort($timeUnit);
$timeUnit = implode(',', $timeUnit);
$timeUnit = explode(',', $timeUnit);
//$columns = $wpdb->get_col("DESC " . $entryTbl, 0);

          /*echo '<pre>';
          print_r($templates);
          echo '</pre>';*/

        $de_time = '';
          if($journey->j_time_unit == 'month'){
            $de_time =    $journey->j_time / 24;         
            $de_time =    $de_time / 30;         
          }elseif($journey->j_time_unit == 'week'){
            $de_time =    $journey->j_time / 24;         
            $de_time =    $de_time / 7;         
          }elseif($journey->j_time_unit == 'day'){
            $de_time =    $journey->j_time / 24;         
          }else{
            $de_time =    $journey->j_time;         
          }

?>
<div class="back">
	<a class="button button-primary pull-right" href="<?php echo admin_url( $path = 'admin.php?page=email_marketing#allJourney', $scheme = 'admin' ) ?> " title="Back to List"><< Back to List</a>
</div>
<div id="editJourney">
	<div class="innermail">
    <h2>Edit Journey: <?php echo $journey->j_name; ?></h2>
    <form action="" method="post" accept-charset="utf-8">
        <div class="form-group">
          <label for="journey_name"><?php echo __('Name of journey', 'allwebbox'); ?><small><i>&nbsp;<?php echo __('(Journey Name use for Email Subject)', 'allwebbox'); ?></i></small></label>
          <input type="text" required name="j_name" id="journey_name" value="<?php echo $journey->j_name; ?>" class="form-control p5">
        </div>
        <div class="form-group">
          <label for="journey_description">Description of journey</label>
          <textarea style="width:100%;" name="j_description" id="journey_description"><?php echo $journey->j_description;  ?></textarea>
        </div>
        <div class="form-group">
          <label for="journey_goal">Goal of journey</label>
          <input type="text" name="j_goal" id="journey_goal" value="<?php echo $journey->j_goal; ?>" class="form-control"/>
        </div>
        <div class="form-group">
          <label for="nameofSender">Name of Sender</label>
          <input type="text" name="j_sender" id="nameofSender" value="<?php echo $journey->j_sender; ?>" class="form-control"/>
        </div>
        <div class="form-group">
          <label for="jReplayEmail">Reply email address</label>
          <input type="email" name="j_rep_email" id="jReplayEmail" value="<?php echo $journey->j_rep_email; ?>" class="form-control"/>
        </div>

        <div class="three-half left">
          <div class="form-group">
            <label for="journey_time">Email sent after <small><i>(Default)</i></small></label>
            <input type="number" step="any" min="1" step="1.0" id="journey_time" name="j_time_default" value="<?php echo ($de_time != 0)?$de_time:''; ?>" class="form-control p5">
          </div>
        </div>
        <div class="three-half middle">
          <div class="form-group">
            <label for="time_unit">Each</label><br>
            <label><input <?php echo ($journey->j_time_unit == 'month')?'checked':''; ?> type="radio" value="month" name="time_unit_default" />Month</label>&nbsp;&nbsp;
            <label><input <?php echo ($journey->j_time_unit == 'week')?'checked':''; ?> type="radio" value="week" name="time_unit_default" />Week</label>&nbsp;&nbsp;
            <label><input <?php echo ($journey->j_time_unit == 'day')?'checked':''; ?> type="radio" value="day" name="time_unit_default" />Day</label>&nbsp;&nbsp;
            <label><input <?php echo ($journey->j_time_unit == 'hour')?'checked':''; ?> type="radio" value="hour" name="time_unit_default" />Hour</label>
          </div>
        </div>
        <div class="three-half right">
          <div class="form-group">
            <label for="j_type">Message Type</label><br>
            <label><input <?php echo ($journey->j_type == 'email')?'checked':''; ?> type="radio" value="email" name="j_type" />Email</label>&nbsp;&nbsp;
            <label><input <?php echo ($journey->j_type == 'pushtobrowser')?'checked':''; ?> type="radio" value="pushtobrowser" name="j_type" />PUSH to Browser</label>&nbsp;&nbsp;
            <label><input <?php echo ($journey->j_type == 'sms')?'checked':''; ?> type="radio" value="sms" name="j_type" />SMS</label>
          </div>
        </div>

        <!--<div class="form-group">
          <label for="journey_time">Email sent after each (hours)</label>
          <input type="number" required id="journey_time" name="j_time" value="<?php echo $journey->j_time; ?>" class="form-control p5">
        </div>-->
        <br class="clear"/>
        <hr/>
        <br/>


        <?php for($si=0; count($templates->j_emails) > $si; $si++): 

         
          $es_jtime = '';
          if(isset($timeUnit[$si])){

            if($timeUnit[$si] == 'month'){
              $es_jtime =    $templates->j_time[$si] / 24;         
              $es_jtime =    $es_jtime / 30;   
            }elseif($timeUnit[$si] == 'week'){
              $es_jtime =    $templates->j_time[$si] / 24;         
              $es_jtime =    $es_jtime / 7;         
            }elseif($timeUnit[$si] == 'day'){
              $es_jtime =    $templates->j_time[$si] / 24;         
            }elseif($timeUnit[$si] == 'minutes'){
              $es_jtime =    $templates->j_time[$si] * 60;         
            }else{
              $es_jtime =    $templates->j_time[$si];         
            }
          }
        ?>


        <div class="newTemplate">
          <div class="SlidingP">
            <div class="slidInner">
              <span alt="f140" class="dashicons dashicons-arrow-right"></span> &nbsp; <?php echo sprintf(__('Message %s', 'allwebbox'), $si+1); ?>
            </div>
          </div>
          <div class="tempNewInner tmhidden">
        	<div class="tempDelete">
              <span alt="f158" class="dashicons dashicons-no"></span>
          	</div>

          <div class="three-half left">
            <div class="form-group">
              <label for="journey_date">Email sent date <small><i>(Date)</i></small></label>
              <input type="text" style="padding:3.5px;" class="datepicker form-control p5" name="j_date[]" value="<?php echo $templates->j_date[$si]; ?>">
            </div>
          </div>
          <div class="three-half left middle">
            <div class="form-group">
              <label for="journey_time">Email sent after <small><i>(time)</i></small></label>
              <input type="number" id="journey_time" step="any" name="j_time[]" value="<?php echo ($es_jtime != 0)?$es_jtime:''; ?>" class="form-control p5">
            </div>
          </div>
          
          <div class="three-half right">
            <div class="form-group">
              <label for="time_unit">Each</label><br>
              <label><input <?php echo (isset($timeUnit[$si]) && $timeUnit[$si] == 'month')?'checked':''; ?> type="radio" value="month" name="time_unit[<?php echo $si; ?>]" />Month</label>&nbsp;&nbsp;
              <label><input <?php echo (isset($timeUnit[$si]) && $timeUnit[$si] == 'week')?'checked':''; ?> type="radio" value="week" name="time_unit[<?php echo $si; ?>]" />Week</label>&nbsp;&nbsp;
              <label><input <?php echo (isset($timeUnit[$si]) && $timeUnit[$si] == 'day')?'checked':''; ?> type="radio" value="day" name="time_unit[<?php echo $si; ?>]" />Day</label>&nbsp;&nbsp;
              <label><input <?php echo (isset($timeUnit[$si]) &&  $timeUnit[$si] == 'hour')?'checked':''; ?> type="radio" value="hour" name="time_unit[<?php echo $si; ?>]" />Hour</label>&nbsp;&nbsp;
              <?php if($si == '0'): ?>
                  <label><input <?php echo (isset($timeUnit[$si]) && $timeUnit[$si] == 'minutes')?'checked':''; ?> type="radio" value="minutes" name="time_unit[<?php echo $si; ?>]" />Minutes</label>
              <?php endif; ?>
            </div>
          </div>
           <div class="subject full">
            <div class="form-group">
              <label for="j_subject">Subject</label>
              <input type="text" value="<?php echo $templates->j_subject[$si]; ?>" class="form-control" name="j_subject[]" />
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
          <textarea style="width:100%; min-height:120px;" name="j_emails[]" class="form-control tinymce"><?php echo $templates->j_emails[$si]; ?></textarea>
          </div>
        </div>
    	<?php endfor; ?>
        <div id="addnewTemplate"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>
        <input type="hidden" name="edit_id" value="<?php echo $_GET['id']; ?>">
        <button style="float:left;" type="submit" class="button button-primary">Submit</button>
    </form>
  </div>
</div>
