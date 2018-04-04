<?php
/*
* Email Markeing Campaign
*/
	global $wpdb;
	$entryTable   		= $wpdb->prefix . 'awe_entry';
	$template_table 	= $wpdb->prefix . 'template_table';
	$tbl_campaign 		= $wpdb->prefix . 'tbl_campaign';
	$tbl_subcampaign 	= $wpdb->prefix . 'tbl_subcampaign';
	$tbl_objective 		=  $wpdb->prefix . 'tbl_objective';
	$tbl_subobjective 	= $wpdb->prefix . 'tbl_subobjective';
	$allObjects = $wpdb->get_results('SELECT * FROM '.$tbl_objective.'');

  	$qry="select * from ".$entryTable."";
    $formsEntryData=$wpdb->get_results($qry, OBJECT);
    $columns = $wpdb->get_col("DESC " . $entryTable, 0);
    $allCamps = $wpdb->get_results('SELECT * FROM '.$tbl_campaign.'', OBJECT);
?>
<hr>

<?php 
$exTemplates = $wpdb->get_results('SELECT * FROM '.$template_table.'', OBJECT);
if(isset($_GET['ltmpid'])){
	$exTmp = $wpdb->get_row('SELECT `tmplate` FROM '.$template_table.' WHERE id='.$_GET['ltmpid'].'', OBJECT);
}
if(isset($_POST['sendto'])){

	$to 		= array_unique($_POST['sendto']);
	$subject 	= $_POST['esubject'];
	$message  	= stripslashes($_POST['content']);
	$permalink 	= ($_POST['campainfor'] == 'new')?get_permalink($_POST['assign_page']):get_permalink($_POST['assign_page']) . '?el=1';
	
	if($_POST['campainfor'] && $_POST['assign_page'] ){
		$message	 .= '<div style="text-align: center; overflow: hidden;line-height: 40px;"><a style="background: #4c4c4c; background: -moz-linear-gradient(top, #4c4c4c 0%, #595959 12%, #666666 25%, #474747 39%, #2c2c2c 49%, #000000 51%, #111111 60%, #2b2b2b 76%, #1c1c1c 91%, #131313 100%); background: -webkit-linear-gradient(top, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); background: linear-gradient(to bottom, #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 49%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); color: #fff; text-decoration: none; font-size: 18px;  padding: 15px; text-transform: uppercase; border-radius: 10px; line-height: 50px;" href="'.$permalink.'">Click for Start</a></div>';
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');
	
	$send = wp_mail( $to, $subject, $message, $headers);

	
	if($send){
		$msg = '<ul>';
		foreach($to as $mto){
			$msg .= '<li><span alt="f147" class="dashicons dashicons-yes"></span> Mail Send to '. $mto . '<li>';
		}
		$msg .= '</ul>';
	}
} // End if isset($_POST['sendto']);
?>

	<div class="wrap email_campains bgff p20">
		<?php if(!isset($_GET['campains']) && !isset($_GET['history'])):
			require_once(ALWEBDIR . 'inc/objectives.php'); 

		elseif(isset($_GET['history'])): 
			require_once(ALWEBDIR . 'inc/strategy_history.php'); 	

		else: //if(!isset($_GET['objectives'])): ?>
		<div style="overflow:hidden;" id="sendSelectedEmail" class="innerpageallwebbox">
				<div class="suecessMessage">
					<?php echo (isset($query))?$msg:''; ?>
				</div>
				<div class="form-group first">
					<div class="half left <?php echo (!isset($_GET['send_mails']))?'opacity0':''; ?>">
							<label for="campainfor">Email Campaign for</label>
							<select class="form-control" id="campainfor" name="campainfor">
								<option value="registered">Registered User</option>
								<option value="new">New User</option>
							</select>

					</div>
					<div class="half right">
					<?php 
						$queryst =(isset($_GET['send_mails']))?http_build_query(array('send_mails' => $_GET['send_mails'])):'';	
					?>
					<?php if(isset($_GET['send_mails'])): ?>
			
					<a href="<?php echo admin_url( $path = '/admin.php?page=my-menu&' . $queryst, $scheme = 'admin' ); ?>" class="button button-primary button-bigger"><?php echo __('Objectives', 'allwebbox'); ?></a>
				<?php else: ?>
		
					<a style="margin-right:20px;" href="<?php echo admin_url( $path = '/admin.php?page=my-menu&history=1', $scheme = 'admin' ); ?>" class="button button-primary button-bigger"><?php echo __('Communication History', 'allwebbox'); ?></a>

					<a href="<?php echo admin_url( $path = '/admin.php?page=my-menu', $scheme = 'admin' ); ?>" class="button button-primary button-bigger"><?php echo __('Objectives', 'allwebbox'); ?></a>
				<?php endif; ?>
					</div>
				</div>
				<?php if(isset($_GET['send_mails'])): ?>
				<div class="form-group" style="overflow:hidden;">
			      <div class="toEmail" id="sendTo">
			        <label><?php echo __('Send To', 'allwebbox'); ?></label>
			        <div id="innersendTo">
				        <?php foreach(array_unique($_GET['send_mails']) as $se): ?>
				          <input type="hidden" name="sendto[]" value="<?php echo $se; ?>">
				          <span class="semail"><?php echo $se; ?></span>
				        <?php endforeach; ?>
			    	</div>
			      </div>
			    </div>
				<?php endif; ?>
		        <?php foreach($allCamps as $sCmp): 
		        	$scampaigns = $wpdb->get_results('SELECT * FROM '.$tbl_subcampaign.' WHERE cid='.$sCmp->id.'', OBJECT);
		        	//$sub_obs = ($sCmp->sub_obj != '')?json_decode($sCmp->sub_obj):array();
		        ?> 
			    <div class="newTemplate emailCampaign">
				    <div class="SlidingP">
			            <div class="slidInner">
			              	<span alt="f140" class="dashicons dashicons-arrow-right"></span> &nbsp; 
			              	<input class="hidden" type="text" name="campain_name" value="<?php echo $sCmp->cmp_name; ?>" class="form-control" />
			              	<span class="title"><?php echo $sCmp->cmp_name; ?></span>
			          	</div>
			        </div>

	     			<div class="tempNewInner tmhidden">
			          <div data-id="<?php echo $sCmp->id; ?>" class="camtempDelete">
			              <span alt="f158" class="dashicons dashicons-no"></span>
			          </div>
			          <div class="tempEdit" data-id="<?php echo $sCmp->id; ?>">
			              <span alt="f464" class="dashicons dashicons-edit"></span>
			          </div>

			          <div class="campoignObjective wrapp mt20">
			          	<div class="form-group selectCampaign">
			          		<label for="campaignName"><?php echo __('Objective', 'allwebbox'); ?></label>
			          		<select class="form-control" name="campaignName">
			          			<option value=""><?php echo __('Select Campaign', 'allwebbox'); ?></option>
			          			<?php 
			          				foreach($allObjects as $sObj){
			          					$sltd = ($sObj->id == $sCmp->obj)?'selected':'';
			          					echo '<option '.$sltd.' value="'.$sObj->id.'">'.$sObj->objective_name.'</option>';	
			          				} 
			          			?>
			          		</select>
			          	</div>
			          	<div class="campaignDesc">
			          		<div class="form-group">
			          			<label for="camp_desc"><?php echo __('Description', 'allwebbox'); ?></label>
			          			<textarea rows="4" style="width:100%;" name="camp_desc"><?php echo $sCmp->camp_desc; ?></textarea>
			          		</div>
			          	</div>

			          	<?php if($sCmp->obj != ''): 
			          		$allSubObj = $wpdb->get_results('SELECT * FROM '.$tbl_subobjective.' WHERE oids='.$sCmp->obj.'', OBJECT);

			          	?>
			          <!--	<div class="form-group selectSubCampaign">
			          		<label for="subcampaignName"><?php //echo __('Sub Objective', 'allwebbox'); ?></label>
			          		<select class="form-control" multiple name="subcampaignName">
			          			<?php //for($i=0; count($allSubObj) > $i; $i++){
			          				//$sSelectd = (in_array($allSubObj[$i]->id, $sub_obs))?'selected':''; 
			          			//	echo '<option '.$sSelectd.' value="'.$allSubObj[$i]->id.'">'.$allSubObj[$i]->sub_obj.'</option>';
			          			//} ?>
			          		</select>
			          	</div>-->
			          	<?php endif; ?>

			          </div>
			          <?php foreach($scampaigns as $each): ?>
						<div class="newTemplate newSubCampaign">
	 					<form data-exid="<?php echo $each->id; ?>" data-campaign="<?php echo $sCmp->id; ?>" action="" method="post" accept-charset="utf-8">
	 					<div class="deleteSubCamp" data-id="<?php echo $each->id; ?>"><span alt="f153" class="dashicons dashicons-dismiss"></span></div>
	 					<div class="subCamptitle title">
	 						<div class="titleInner">
	 							<h4><span alt="f345" class="dashicons dashicons-arrow-right-alt2"></span>&nbsp;<?php echo $each->scmp_name; ?></h4>
	 							<?php if(isset($_GET['send_mails'])): ?>
		 							<div class="actionEmail" data-subcid="<?php echo $each->id; ?>">
		 								<span alt="f148" class="dashicons dashicons-admin-collapse"></span>
		 							</div>
	 							<?php endif; ?>
	 						</div>
	 					</div>
	 					<div class="wrap hidden email_campains bgff p20">
						<div class="innerpageallwebbox">
						
						<!-- Sub-Campaign Name -->
						<input type="text" name="scmp_name" class="form-control" value="<?php echo $each->scmp_name; ?>" placeholder="Sub-Campaign Name..." />

						<!-- // End Sub-Campaign Name -->


						<!-- Selected Sub Objective  -->
						<?php

						$relatedSubObj = $wpdb->get_results('SELECT `id`, `sub_obj` FROM '.$tbl_subobjective.' WHERE oids='.$sCmp->obj.'', OBJECT);
						$jSubObs = json_decode($each->sub_obj);
						
						?>
						<div class="form-group selectSubCampaign">
							<label for="subcampaignName"><?php echo __('Sub Objective', 'allwebbox'); ?></label>
							<select class="form-control" multiple="" name="sub_obj">
								<?php 
									foreach($relatedSubObj as $sob){
										$sLtd = (in_array($sob->id, $jSubObs[0]))?'selected':'';
										echo '<option '.$sLtd.' value="'.$sob->id.'">'.$sob->sub_obj.'</option>';		
									}
								?>
							</select>
						</div>


						<!-- End Selected Sub Objective -->


						<div class="form-group">
							<div class="smspushAea">
								<textarea  name="sub_camdesc" rows="6" class="form-control"><?php echo $each->sub_camdesc; ?></textarea>
							</div>
						</div>
						<br>
						<input type="submit" name="scampain_submit" value="Submit" class="button button-primary" />
						</div>
						</div>
						</form>
    					</div>
    					<?php endforeach; //$scampaigns ?>
			          <div class="addnewSubCampaign"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>

			      	</div>
		        </div>
		    	<?php endforeach; ?>
				<div id="addnewCampaign" class="newCamp"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>
		</div>
		<?php endif; //if(!isset($_GET['objectives'])): ?>


	</div> <!-- End Wrap -->
