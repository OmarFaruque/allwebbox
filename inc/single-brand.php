<?php 
/*
* Single Brand
*/
 $msg ='';
 global $wpdb;
 $brandTable = $wpdb->prefix . 'brandsms';
 
 

 if(isset($_POST['brand_name'])){

    $msgArr = array();
    foreach($_POST['msg'] as $pMsg) array_push($msgArr, stripslashes($pMsg));
    $_POST['msg'] = json_encode($msgArr);
    $exData =  $wpdb->get_row('SELECT * FROM '.$brandTable.' WHERE brand_name="'.$_POST['brand_name'].'"', OBJECT);

    if($exData){
      $qry = $wpdb->update(
          $brandTable,
          $_POST,
          array(
            'brand_name' => $_POST['brand_name']
          )
      );
    }else{


      $qry = $wpdb->insert(
        $brandTable,
        $_POST,
        array(
          '%s',
          '%s',
          '%d',
          '%s',
          '%s'
        )
      );
    }

 } // End isset($_POST['brand_name'])
  $dbbrands =  $wpdb->get_row('SELECT * FROM '.$brandTable.' WHERE brand_name="'.$_GET['bdt'].'"', OBJECT);


  $msgs = ($dbbrands)?json_decode($dbbrands->msg):array();

?>

<div id="newjourney" class="active section single_brand">
  <div id="msg">
    <?php if($msg != '' && $msg == 'success'): ?>
        <div class="success msg"><h5>Journey Succefully Create.</h5></div>
    <?php elseif($msg !='' && $msg == 'faild'): ?>
        <div class="error msg"><h5>Journey Create Failed.</h5></div>
    <?php endif; ?>
  </div>
  <div class="innermail sms">
    <h2>Brand: <?php echo $_GET['bdt']; ?></h2>
   
    <form action="" method="post" accept-charset="utf-8">

        <div class="threQrtr left">
          <div class="form-group">
            <label for="messageType"><?php echo __('Message Type', 'allwebbox'); ?></label><br>
            <select style="width:100%;" name="msgtype" class="form-control">
              <option <?php echo ($dbbrands && $dbbrands->msgtype == 'email')?'selected':''; ?> value="email"><?php echo __('Email', 'allwebbox'); ?></option>
              <option <?php echo ($dbbrands &&  $dbbrands->msgtype == 'sms')?'selected':''; ?> value="sms"><?php echo __('SMS', 'allwebbox'); ?></option>
              <option <?php echo ($dbbrands && $dbbrands->msgtype == 'pushtoapps')?'selected':''; ?> value="pushtoapps"><?php echo __('PUSH messages to APPs', 'allwebbox'); ?></option>
              <option <?php echo ($dbbrands && $dbbrands->msgtype == 'pushtobrowser')?'selected':''; ?> value="pushtobrowser"><?php echo __('PUSH messages to browsers', 'allwebbox'); ?></option>
            </select>
          </div>
        </div>
        <div class="threQrtr middle">
          <div class="form-group">
            <label for="msgduration"><?php echo __('Message / SMS Sent Each', 'allwebbox'); ?></label><br>
            <label><input type="radio" <?php echo ($dbbrands && $dbbrands->msgduration == 'month')?'checked':''; ?> value="month" name="msgduration" /><?php echo __('Month', 'allwebbox'); ?></label>&nbsp;&nbsp;
            <label><input type="radio" <?php echo ($dbbrands && $dbbrands->msgduration == 'week')?'checked':''; ?> value="week" name="msgduration" /><?php echo __('Week', 'allwebbox'); ?></label>&nbsp;&nbsp;
            <label><input type="radio" <?php echo ($dbbrands && $dbbrands->msgduration == 'day')?'checked':''; ?> value="day" name="msgduration" /><?php echo __('Day', 'allwebbox'); ?></label>&nbsp;&nbsp;
          </div>
        </div>
        <div class="threQrtr right">
          <div class="form-group">
            <label for="msgamount"><?php echo __('Total Amount', 'allwebbox'); ?> </label>
            <input type="number" min="0" step="1" name="msgamount" value="<?php echo ($dbbrands->msgamount != '')?$dbbrands->msgamount:''; ?>" class="form-control">
          </div>
        </div>
        <br>
        
        <?php if($dbbrands && count($msgs) > 0): ?>
          <?php $cnt=1; foreach($msgs as $sMsg): ?>
             <div class="newTemplate">
                <div class="tempDelete">
                    <span alt="f158" class="dashicons dashicons-no"></span>
                </div>
                <textarea style="width:100%; min-height:120px;" id="singleSMS_<?php echo $cnt; ?>" name="msg[]" class="form-control tinymce"><?php echo $sMsg; ?></textarea>
              </div>
          <?php $cnt++; endforeach; ?>
        <?php else: ?>
        <div class="newTemplate">
          <div class="tempDelete">
              <span alt="f158" class="dashicons dashicons-no"></span>
          </div>
          <textarea style="width:100%; min-height:120px;" id="singleSMS_1" name="msg[]" class="form-control tinymce">Hi [lastname],&nbsp;<div><br></div><div>Regards,</div></textarea>
        </div>
        <?php endif; ?>

        <div id="addnewTemplateMsg"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>
        <input type="hidden" name="brand_name" value="<?php echo $_GET['bdt']; ?>" />
        <input type="hidden" id="brand_icon" name="brand_icon" value="<?php echo ($dbbrands && $dbbrands->brand_icon != '')?$dbbrands->brand_icon:''; ?>"/>
        <br>
        <div id="iconBanner">
          <label style="display:block; margin-bottom:10px;">Brand Icon:</label>
          <div class="iconInner">
            <?php $imgUrl = ($dbbrands && $dbbrands->brand_icon != '')?wp_get_attachment_thumb_url( $dbbrands->brand_icon ):'';  ?>
            <?php echo ($imgUrl != '')?'<img src="'.$imgUrl.'"/>':'<span>Add Icon</span>'; ?>
          </div>
        </div>
        <button type="submit" style="float:left;" class="button button-primary">Submit</button>
    </form>
  </div>
</div>

