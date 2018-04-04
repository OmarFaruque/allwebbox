<?php
/*
* Send Email
*/


function wpdocs_set_html_mail_content_type() {
   	return 'text/html';
}

global $wpdb;
$entryTable   		= $wpdb->prefix . 'awe_entry';
$template_table 	= $wpdb->prefix . 'template_table';
$tbl_subcampaign 	= $wpdb->prefix . 'tbl_subcampaign'; 
$columns = $wpdb->get_col("DESC " . $entryTable, 0);





if(isset($_POST['smail']) && $_POST['selectType'] == 'email'){


	$allpmail = (isset($_GET['a_action']))?explode(',', $_GET['send_mails']):$_GET['send_mails'];
	$sendEmails	= array_unique($allpmail);


	$subject 	= ($_POST['esubject'] != '')?$_POST['esubject']:get_bloginfo( 'name' );
	
	//$headers[] = 'Content-Type: text/html; charset=UTF-8';
	$headers = '';
	//$headers .= 'Content-type: text/html;charset=utf-8' . "\r\n";
	if($_POST['replay'] !='' && $_POST['replay_email'] !=''){
		$name = $_POST['replay'];
		$email = $_POST['replay_email'];
		$headers .= "From: $name <$email>" . "\r\n";	
	}

	$message  = stripslashes($_POST['smail']);
	preg_match_all("/\[([^\]]*)\]/", $message, $matches);


	$msg = '<ul>';


	foreach($sendEmails as $to):

		$idQry = $wpdb->get_row('SELECT `id` FROM '.$entryTable.' WHERE email="'.$to.'"', OBJECT);
		
		foreach($matches[0] as $mk => $sm){
					$vFind = $matches[1][$mk];
					$getMatchFDB = $wpdb->get_row('SELECT `'.$matches[1][$mk].'` FROM '.$entryTable.' WHERE email="'.$to.'"', OBJECT);
					$message = str_replace("'", "", str_replace($sm, $getMatchFDB->$vFind, $message));
		}

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
		
		$id = (isset($_GET['sbcmp']))?$_GET['sbcmp']:'';
		$extComplete = $wpdb->get_row('SELECT `action_complete` FROM '.$tbl_subcampaign.' WHERE id='.$id.'', OBJECT);
		$exEm = ($extComplete->action_complete != '')?json_decode($extComplete->action_complete):array();
		array_push($exEm, $to);
		$exEm = array_unique($exEm);
		$newallEml = json_encode($exEm);

		$wpdb->update(
			$tbl_subcampaign,
			array('action_complete' => $newallEml), 
			array('id' => (int)$id),
			array('%s'),
			array('%d')
		);
	}
	endforeach; //End Mail Loop
	$msg .= '</ul>';

}elseif(isset($_POST['smail_sms']) && $_POST['selectType'] == 'sms'){

	$message  	= stripslashes($_POST['smail_sms']);

	$allpmail = (isset($_GET['a_action']))?explode(',', $_GET['send_mails']):$_GET['send_mails'];
	$sendEmails	= array_unique($allpmail);


	foreach($sendEmails as $smo){
		$getM = $wpdb->get_row('SELECT `mobile` FROM '.$entryTable.' WHERE email="'.$smo.'"', OBJECT);
		echo "<script>
					jQuery.ajax({
						type:'POST', 
			            dataType: 'json',
			            url: 'http://sms.calltopbx.co/api/v1/enviar.json',
			            data:
			            {
			                'envio[cliente]'   	: '18',
			                'envio[apikey]'    	: 'f9f74c3d9a728ea0e23156430a2eb58b',
			                'envio[telefono]' 	: '".$getM->mobile."',
			                'envio[mensaje]' 	: '".$message."'
			            },success:function(data){
			            		console.log('Success sms');
			            }


    			}); // Ajax
		</script>";


		$id = (isset($_GET['sbcmp']))?$_GET['sbcmp']:'';
		$extComplete = $wpdb->get_row('SELECT `action_complete` FROM '.$tbl_subcampaign.' WHERE id='.$id.'', OBJECT);
		$exEm = ($extComplete->action_complete != '')?json_decode($extComplete->action_complete):array();
		array_push($exEm, $smo);
		$exEm = array_unique($exEm);
		$newallEml = json_encode($exEm);

		$wpdb->update(
			$tbl_subcampaign,
			array('action_complete' => $newallEml), 
			array('id' => (int)$id),
			array('%s'),
			array('%d')
		);

	}

} // End post 
elseif(isset($_POST['smail_sms']) && $_POST['selectType'] == 'push'){
	$allpmail = (isset($_GET['a_action']))?explode(',', $_GET['send_mails']):$_GET['send_mails'];
	$js_emails = (count($allpmail) > 0)?json_encode($allpmail):'';
	$id = (isset($_GET['sbcmp']))?$_GET['sbcmp']:'';
			$updateAction = $wpdb->update(
				$tbl_subcampaign, 
				array(
					'type' 		=> 'push',
					'action' 	=> 1,
					'nd_action' => $js_emails
				),
				array('id' => (int)$id), 
				array('%s', '%d', '%s'),
				array('%d')
			);
}



?>

<div id="sendSelectedEmail">
	<form action="" method="post" accept-charset="utf-8">
	  <div class="innerSendEmail">
	  	<div class="msg">
	  		<?php  echo (isset($msg))?$msg:''; ?>
	  	</div>	  	

	  
	  	<hr>
	    <!--<div class="form-group">
	      <div class="toEmail">
	        <label>Send To</label>
	        <?php //foreach(array_unique($_GET['send_mails']) as $se): ?>
	          <span class="semail"><?php// echo $se; ?></span>
	        <?php// endforeach; ?>
	      </div>
	      <div class="toSms hidden">
	      	<label>Send To</label>
	      	<?php //foreach(array_unique($_GET['send_sms']) as $sms): ?>
	          <span class="semail"><?php //echo $sms; ?></span>
	        <?php //endforeach; ?>
	      </div>
	    </div>-->

	    <?php if(isset($_GET['sbcmp'])): 
	    $getsbCmpName = $wpdb->get_row('SELECT `scmp_name` FROM '.$tbl_subcampaign.' WHERE id='.$_GET['sbcmp'].'', OBJECT);
	    ?>
	    <div class="form-group subcName">
	    	<label for="subCampName"><?php echo __('Communication Name', 'allwebbox'); ?></label>
	    	<input type="text" name="subCampName" id="subCampName" value="<?php echo$getsbCmpName->scmp_name; ?>" class="form-control" /> 
	    </div>
	    <?php endif; ?>

	    <div class="form-group" style="overflow:hidden;">
	      <div class="esubject replay name sublect pull-left half">
	        <label for="esubject"><?php echo __('Email Subject', 'allwebbox'); ?></label>
	        <input type="text" class="form-control" name="esubject" id="esubject" value="<?php echo (isset($_POST['esubject']))?$_POST['esubject']:''; ?>" />
	      </div>
	      <div class="pull-right half replay email">
	      		 <label for="selectType"><?php echo __('Type of communication', 'allwebbox'); ?></label>
	  		<select name="selectType" class="form-control">
	  			<option value="email"><?php echo __('Email', 'allwebbox'); ?></option>
	  			<option value="sms"><?php echo __('SMS', 'allwebbox'); ?></option>
	  			<option value="push"><?php echo __('PUSH', 'allwebbox'); ?></option>
	  		</select>
	  	</div>
	    </div>
	   <div class="form-group email" style="overflow:hidden; width:100%; float:left; margin-top:10px;">
	      <div class="replay name">
	        <label for="replay"><?php echo __('Name of who sends', 'allwebbox'); ?></label>
	        <input style="max-width:99%;" type="text" class="form-control" name="replay" id="replay" value="<?php echo (isset($_POST['replay']))?$_POST['replay']:''; ?>" />
	      </div>

	      <div class="replay email" style="float:right;">
	        <label for="replay_email"><?php echo __('Response Email', 'allwebbox'); ?></label>
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

	    <div class="userParemeters" id="campaignPrameter">
            <label><?php echo __('User Parameters', 'allwebbox'); ?> <span alt="f139" class="dashicons dashicons-arrow-right"></span></label>
            <ul class="usParementslist hidden">
              <?php foreach($columns as $sCl): ?>
                <li data-param="<?php echo $sCl; ?>">[<?php echo $sCl; ?>]</li>
              <?php endforeach; ?>
            </ul>
        </div>


	    <div class="halfDiv">
	        <div class="smail">

		        <div class="pull-left">
		          <label for="smail">Content</label>
		      	</div>
		      	<div class="pull-right" id="loadTemplate">
							<div class="inlinelabel">
							<label for="loadExistingTemplate"></label>
							<select id="loadExistingTemplate" name="loadTemplate">
							<option value=""><?php echo __('Load Template...', 'allwebbox'); ?></option>
							<?php 
								$exTemplates 	= $wpdb->get_results('SELECT * FROM '.$template_table.'', OBJECT);
								foreach($exTemplates as $tmplt) echo '<option value="'.$tmplt->id.'">'.$tmplt->name.'</option>';
							?>

							</select>
							</div>
				</div>


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
	          	<div class="visualTextArea">
	        	  <textarea style="min-height:250px;" id="smail" class="form-control tinymce" name="smail"><?php echo $bodyVal; ?></textarea>
	      		</div>
	          <textarea style="min-height:150px;" id="smail_sms" class="form-control normal hidden" name="smail_sms"><?php echo $smsBody; ?></textarea>
	        </div>
	    </div>
		</div>
	
	    

	    <br class="clearfix" />
	    <?php if(isset($_GET['asqQsn']) && $_GET['asqQsn'] != ''): ?>
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
		<?php endif; ?>
		<br class="clearfix">
	    <button style="float:left;" type="submit" class="button button-primary"><?php echo __('Send Email', 'allwebbox'); ?></button>
	    </div>
    </form>
  </div>