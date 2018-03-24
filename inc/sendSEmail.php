<?php
/*
* Send Email
*/
if(isset($_GET['a_action']) && $_GET['a_action'] == 'Start Campaign'){
	$query = http_build_query(array('send_mails' => $_GET['send_mails']));	
	wp_redirect( admin_url( $path = '/admin.php?page=email_markeging_campaigns&' . $query, $scheme = 'admin' ), $status = 302 );
}



function wpdocs_set_html_mail_content_type() {
   	return 'text/html';
}

/*if(isset($_POST['smail'])){
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
}*/


global $wpdb;
$entryTable   = $wpdb->prefix . 'awe_entry';
$smsAll = array_map('trim',array_filter($_GET['send_sms']));
$smsAll = array_unique($smsAll);
$smsAll = implode(',', $smsAll);



if(isset($_POST['smail']) && $_POST['selectType'] == 'email'){
	$sendEmails	= array_unique($_GET['send_mails']);


	$subject 	= ($_POST['esubject'] != '')?$_POST['esubject']:get_bloginfo( 'name' );
	
	

	//$headers[] = 'Content-Type: text/html; charset=UTF-8';
	$headers = '';
	if($_POST['replay'] !='' && $_POST['replay_email'] !=''){
		$name = $_POST['replay'];
		$email = $_POST['replay_email'];
		$headers .= "From: $name <$email>" . "\r\n";	
	}
	

	$msg = '<ul>';
	foreach($sendEmails as $to):
		$idQry = $wpdb->get_row('SELECT `id` FROM '.$entryTable.' WHERE email="'.$to.'"', OBJECT);
		$message  = stripslashes($_POST['smail']);
	if(isset($_GET['asqQsn']) && $_GET['asqQsn'] != ''){ 
		$btnText 	= ($_POST['btn_text'] != '')?$_POST['btn_text']:'Hit me';
		$btnBg 		= ($_POST['btn_bg'] != '')?$_POST['btn_bg']:'#090909';
		$fntSz 		= ($_POST['btn_txt_size'] != '')?$_POST['btn_txt_size']:'14';

		$lnStyle = array();
		if($_POST['lnd_heading'] != '') 	array_push($lnStyle, 'lnd_heading='.$_POST['lnd_heading']); 
		if($_POST['lnd_bgImg'] != '') 		array_push($lnStyle, 'lnd_bgImg='.$_POST['lnd_bgImg']); 
		if($_POST['bg_repeat'] != '') 		array_push($lnStyle, 'bg_repeat='.$_POST['bg_repeat']); 
		if($_POST['bg_attachment'] != '') 	array_push($lnStyle, 'bg_attachment='.$_POST['bg_attachment']); 
		if($_POST['bg_size'] != '') 		array_push($lnStyle, 'bg_size='.$_POST['bg_size']); 
		if($_POST['bg_overColor'] != '') 	array_push($lnStyle, 'bg_overColor='.$_POST['bg_overColor']); 
		if($_POST['bg_overOpacity'] != '') 	array_push($lnStyle, 'bg_overOpacity='.$_POST['bg_overOpacity']); 

		

		$lnStyleImp = implode(';', $lnStyle);

		
		
		$message .= '<div style="text-align:center">';
		$message .= '<form method="POST" action="'.get_home_url( $blog_id = null, $path = '', $scheme = null ).'">';
		$message .= '<input type="hidden" name="rootEdit" value="1"/>';
		$message .= '<input type="hidden" name="r_entry_id" value="'.$idQry->id.'"/>';
		$message .= '<input type="hidden" name="style" value="'.$lnStyleImp.'"/>';
		$message .= '<input type="hidden" name="r_quss" value="'.$_GET['asqQsn'].'"/>';
		$message .= '<button style="text-align: center; overflow: hidden; background: '.$btnBg.'; color: #fff; text-decoration: none; font-size: '.$fntSz.'px;  padding: 10px 20px; border-width:0px; border-radius: 5px;" type="submit"><span>'.$btnText.'</span></button>';
		$message .= '</form>';
		$message .= '</div>';
	}
	add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
		$send = wp_mail( $to, $subject, $message, $headers);
	remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );



	if($send){
		$msg .= '<li><span alt="f147" class="dashicons dashicons-yes"></span> Mail Send to '. $to . '<li>';
	}
	endforeach; //End Mail Loop
	$msg .= '</ul>';

}elseif(isset($_POST['smail_sms']) && $_POST['selectType'] == 'sms'){
	$exmobile 	= explode(',', $_POST['smsto']);
	$message  	= stripslashes($_POST['smail_sms']);
	foreach($exmobile as $smo){
		echo "<script>
					jQuery.ajax({
						type:'POST', 
			            dataType: 'json',
			            url: 'http://sms.calltopbx.co/api/v1/enviar.json',
			            data:
			            {
			                'envio[cliente]'   	: '18',
			                'envio[apikey]'    	: 'f9f74c3d9a728ea0e23156430a2eb58b',
			                'envio[telefono]' 	: '".$smo."',
			                'envio[mensaje]' 	: '".$message."'
			            },success:function(data){
			            		console.log('Success sms');
			            }


    			}); // Ajax
		</script>";
	}

} // End post 



?>

<div id="sendSelectedEmail">
	<form action="" method="post" accept-charset="utf-8">
	  <div class="innerSendEmail">
	  	<div class="msg">
	  		<?php  echo (isset($msg))?$msg:''; ?>
	  	</div>	  	
	  	<hr>
	  	<div class="form-group">
	  		<select name="selectType">
	  			<option value="email"><?php echo __('Email', 'allwebbox'); ?></option>
	  			<option value="sms"><?php echo __('SMS', 'allwebbox'); ?></option>
	  		</select>
	  	</div>
	  	<hr>
	    <div class="form-group">
	      <div class="toEmail">
	        <label>Send To</label>
	        <?php foreach(array_unique($_GET['send_mails']) as $se): ?>
	          <span class="semail"><?php echo $se; ?></span>
	        <?php endforeach; ?>
	      </div>
	      <div class="toSms hidden">
	      	<label>Send To</label>
	      	<?php foreach(array_unique($_GET['send_sms']) as $sms): ?>
	          <span class="semail"><?php echo $sms; ?></span>
	        <?php endforeach; ?>
	      </div>
	    </div>
	    <div class="form-group email">
	      <div class="sublect">
	        <label for="esubject"><?php echo __('Email Subject', 'allwebbox'); ?></label>
	        <input type="text" class="form-control" name="esubject" id="esubject" value="<?php echo (isset($_POST['esubject']))?$_POST['esubject']:''; ?>" />
	      </div>
	    </div>
	   <div class="form-group email" style="overflow:hidden;">
	      <div class="replay name">
	        <label for="replay"><?php echo __('Email Replay Name', 'allwebbox'); ?></label>
	        <input style="max-width:99%;" type="text" class="form-control" name="replay" id="replay" value="<?php echo (isset($_POST['replay']))?$_POST['replay']:''; ?>" />
	      </div>

	      <div class="replay email" style="float:right;">
	        <label for="replay_email"><?php echo __('Replay Email', 'allwebbox'); ?></label>
	        <input style="max-width:99.90%;" type="text" class="form-control" name="replay_email" id="replay_email" value="<?php echo (isset($_POST['replay_email']))?$_POST['replay_email']:''; ?>" />
	      </div>
	    </div>
	    <!--<div class="form-group">
	    	<label for="sendEmailForm"><?php // echo __('Landing Page with Form', 'allwebbox'); ?></label>
	    	<select style="width:100%;" name="sendemailform" class="form-control" id="sendEmailForm">
	    		<option value=""><?php // echo __('Select Landing Page with Form', 'allwebbox'); ?></option>
	    		<?php 
	    		//	$allPags = get_all_page_ids();
	    			//foreach($allPags as $sp){
	    				//echo '<option value="'.$sp.'">'.get_the_title( $sp ).'</option>';
	    			//}
	    		?>
	    	</select>
	    </div>-->
	    <div class="form-group">
	        <div class="smail">
	          <label for="smail">Content</label>
	          <?php 
	          	$bodyVal = '';
	          	$smsBody = '';
	          	if(isset($_POST['smail'])){
	          		$bodyVal .= stripslashes($_POST['smail']);
	          		$smsBody .= stripslashes($_POST['smail_sms']);
	          	}
	          	if(isset($_GET['asqQsn']) && $_GET['asqQsn'] != '' && !isset($_POST['smail'])){
	          		$qArr = explode(';', $_GET['asqQsn']);
	          		$bodyVal = '<p>'.__("You have to answer following question's", "allwebbox").'</p><ol>';
	          		foreach($qArr as $k => $sQ){
	          			$bodyVal .= '<li>'.$sQ.'</li>';
	          		}
	          		$bodyVal .= '</ol>';
	          	}
	          ?>
	          <textarea style="min-height:250px;" id="smail" class="form-control tinymce" name="smail"><?php echo $bodyVal; ?></textarea>
	          <textarea style="min-height:150px;" id="smail_sms" class="form-control normal hidden" name="smail_sms"><?php echo $smsBody; ?></textarea>
	        </div>
	    </div>
	    <input type="hidden" name="smsto" value="<?php echo $smsAll; ?>">



	    <div class="sectionAllow crm ‍sendEmail">
		    <div class="form-group">
		      <div class="col-md-12">
		        <h3>
		        <?php echo __('Style', 'allwebbox'); ?>
		        <a id="savedFilter" class="visiableSection" href="javascript:void(0)"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
		        </h3>
		      </div>
		    </div>
		    <div class="form-group allwebContentBdy" id="saveEmailStyle">
		      <div class="col-md-12">
		        <div class="info filter"><span><?php echo __('Set some style for email & landing page.', 'allwebbox'); ?></span></div>
		        <div class="fix-padding">
		        	<div class="col-md-6 half left">
		        	<div class="form-group">
		        		<label for="btn_text"><?php echo __('Email Button Text ', 'allwebbox'); ?><a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('button for email body.'); ?> </span></a></label>
		        		<input type="text" class="form-control" name="btn_text" id="btn_text" value="" />
		        	</div>

		        	<div class="form-group">
		        		<label for="btn_bg"><?php echo __('Email Button Color ', 'allwebbox'); ?><a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('Email button color.'); ?> </span></a></label>
		        		<input type="text" class="form-control colorpicker" name="btn_bg" id="btn_bg" value="" />
		        	</div>

		        	<div class="form-group">
		        		<label for="btn_txt_size"><?php echo __('Email Button text size ', 'allwebbox'); ?><a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('Email button text size.'); ?> </span></a></label>
		        		<input style="max-width:95%;" type="number" min="10" class="form-control" name="btn_txt_size" id="btn_txt_size" value="" /><span>&nbsp;px</span>
		        	</div>

		        
		        </div> <!-- End half -->
		        <div class="col-md-6 half right">
		        	<div class="form-group">
		        		<label for="lnd_heading"><?php echo __('Landing Heading Text ', 'allwebbox'); ?><a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('Landing page heading text.'); ?> </span></a></label>
		        		<input type="text" class="form-control" name="lnd_heading" id="lnd_heading" value="" />
		        	</div>

		        	<div class="form-group">
		        		<div id="modiF_bg_img">
		        			<a class="text-center" href="#"><?php echo __('Form Background Image', 'allwebbox'); ?></a>
		        		</div>
		        		<input type="hidden" class="form-control" name="lnd_bgImg" id="lnd_bgImg" value="" />
		        	</div>

		        	<div class="form-group">
		        		<label for="bg_repeat"><?php echo __('Landing Background Image Repeat ', 'allwebbox'); ?><a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('Landing page Background Image Repeat.'); ?> </span></a></label>
		        		<select class="form-control" id="bg_repeat" name="bg_repeat">
		        			<option value="no"><?php echo __('No', 'allwebbox'); ?></option>
		        			<option value="yes"><?php echo __('Yes', 'allwebbox'); ?></option>
		        		</select>
		        	</div>

		        	<div class="form-group">
		        		<label for="bg_attachment"><?php echo __('Landing Background Image Attachment ', 'allwebbox'); ?><a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('Landing page Background Image Attachment.'); ?> </span></a></label>
		        		<select class="form-control" id="bg_attachment" name="bg_attachment">
		        			<option value="fixed"><?php echo __('Fixed', 'allwebbox'); ?></option>
		        			<option value="inherit"><?php echo __('Inherit', 'allwebbox'); ?></option>
		        			<option value="scroll"><?php echo __('Scroll', 'allwebbox'); ?></option>
		        			<option value="local"><?php echo __('Local', 'allwebbox'); ?></option>
		        			<option value="initial"><?php echo __('Initial', 'allwebbox'); ?></option>
		        		</select>
		        	</div>

		        	<div class="form-group">
		        		<label for="bg_size"><?php echo __('Landing Background Image Size ', 'allwebbox'); ?><a class="tooltips" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo __('Landing page Background Image Size.'); ?> </span></a></label>
		        		<select class="form-control" id="bg_size" name="bg_size">
		        			<option value="cover"><?php echo __('Cover', 'allwebbox'); ?></option>
							<option value="auto"><?php echo __('Auto', 'allwebbox'); ?></option>
							<option value="contain"><?php echo __('Contain', 'allwebbox'); ?></option>
							<option value="initial"><?php echo __('Initial', 'allwebbox'); ?></option>
							<option value="inherit"><?php echo __('Inherit', 'allwebbox'); ?></option>		
		        		</select>
		        	</div>

		        	<div class="form-group">
		        		<label><input type="checkbox" value="yes" name="bg_overley_active" class="custom-checkbox" /> <?php echo __('Background Overley', 'allwebbox'); ?></label>
		        		<div class="overleyFunctional mt15 hidden" id="bgOverA">
		        			<div class="sOverlay">
		        				<label for="bg_overColor"><?php echo __('Overley Color', 'allwebbox'); ?></label>
		        				<input type="text" name="bg_overColor" class="form-control colorpicker" id="bg_overColor" value="" />
		        			</div>
		        			<div class="sOverlay">
		        				<label for="bg_overOpacity"><?php echo __('Overley Opacity', 'allwebbox'); ?></label>
		        				<input type="number" min="0.05" max="1.0" step="0.01" name="bg_overOpacity" class="form-control" id="bg_overOpacity" value="" />
		        			</div>
		        		</div>
		        	</div>

		        </div> <!-- End Right Half -->

		            
		        </div>
		      </div>
		    </div>
		</div>






	    <button type="submit" class="button button-primary"><?php echo __('Send Email', 'allwebbox'); ?></button>
	    </div>
    </form>
  </div>