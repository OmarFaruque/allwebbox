
<div style="overflow:hidden;" id="sendSelectedEmail" class="innerpageallwebbox">
	<div class="title objective">
		<h2 style="float:left;"><?php echo __("Objective's", 'allwebbox'); ?></h2>
		<?php 
			$queryst = http_build_query(array('send_mails' => $_GET['send_mails']));	
		?>
		<a style="float:right;" href="<?php echo admin_url( $path = '/admin.php?page=email_markeging_campaigns&' . $queryst, $scheme = 'admin' ); ?>" class="button button-primary button-large"><< Go Back</a>
	</div>
    		<?php foreach($allObjects as $sObj): 
		        	$subObjects = $wpdb->get_results('SELECT * FROM '.$tbl_subobjective.' WHERE oids='.$sObj->id.'', OBJECT);
		        ?> 
			    <div class="newTemplate newObjectiveW">
				    <div class="SlidingP">
			            <div class="slidInner">
			              	<span alt="f140" class="dashicons dashicons-arrow-right"></span> &nbsp; 
			              	<input class="hidden" type="text" name="objective_name" value="<?php echo $sObj->objective_name; ?>" class="form-control" />
			              	<span class="title"><?php echo $sObj->objective_name; ?></span>
			          	</div>
			        </div>

	     			<div class="tempNewInner tmhidden">
			          <div data-id="<?php echo $sObj->id; ?>" class="objectDelete">
			              <span alt="f158" class="dashicons dashicons-no"></span>
			          </div>
			          <div class="tempEditOjb" data-id="<?php echo $sObj->id; ?>">
			              <span alt="f464" class="dashicons dashicons-edit"></span>
			          </div>

			          <?php foreach($subObjects as $eachS): ?>
						<div class="newTemplate newObjectiveSub">
	 					<form data-exid="<?php echo $eachS->id; ?>" data-campaign="<?php echo $sObj->id; ?>" action="" method="post" accept-charset="utf-8">
	 					<div class="deleteSubObj" data-id="<?php echo $eachS->id; ?>"><span alt="f153" class="dashicons dashicons-dismiss"></span></div>
	 					<div class="subCamptitle title">
	 						<div class="titleInner">
	 							<h4><span alt="f345" class="dashicons dashicons-arrow-right-alt2"></span>&nbsp;<?php echo $eachS->sub_obj; ?></h4>
	 							
	 						</div>
	 					</div>
	 					<div class="wrap hidden email_campains bgff p20">
						<div class="innerpageallwebbox">
						
						<!-- Sub-Objective Name -->
						<input type="text" name="sub_obj" class="form-control" value="<?php echo $eachS->sub_obj; ?>" placeholder="Sub-Objective..." />

						<!-- // End Sub-Campaign Name -->

						<br>
						<input type="submit" name="scampain_submit" value="Submit" class="button button-primary" />
						</div>
						</div>
						</form>
    					</div>
    					<?php endforeach; //$scampaigns ?>
			          <div class="addnewSubObjective"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>

			      	</div>
		        </div>
		    	<?php endforeach; ?>


		<div id="addnewCampaign" class="newCamp newObjective"><span alt="f502" class="dashicons dashicons-plus-alt"></span></div>
</div>

