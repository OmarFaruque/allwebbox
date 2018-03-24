<?php 
/*
* Details Entry 
*/

$msg = '';
if(isset($_POST['update'])){

	$id = $_POST['entry_id'];
	unset($_POST['update']);
	unset($_POST['entry_id']);
	unset($_POST['date']);

	$_POST['others'] = implode('; ', array_map(
		    function ($v, $k) 
		    { 
		    		return sprintf("%s=%s", $k, $v); 	
		    },
		    $_POST['others'],
		    array_keys($_POST['others'])
	));

	$update = $wpdb->update(
		$entryTable, 
		$_POST,
		array(
			'id' => $id
		)
	);
	$mst = '';
	if($update){
		$msg .= 'Data Successfully Update.';
	}else{
		$msg .= 'Data Update Faild';
	}
}

$details = $wpdb->get_row('SELECT * FROM '.$entryTable.' WHERE form_id='.$_GET['form-id'].' AND id='.$_GET['details'].'');
$entryID = $details->id;
unset($details->id);
unset($details->form_id);

$others = explode('; ', $details->others);
/*echo '<pre>';
print_r($details);
echo '</pre>';
*/
?>
<div id="detailsInfor">
	<div class="innterdetails">
		<section>
			<form action="" method="post" accept-charset="utf-8">
			<div class="msg">
				<?php if($msg): ?>
					<div class="msgDisplay">
						<?php echo $msg; ?>
					</div>
				<?php endif; ?>
			</div>
			<header id="header" class="">
				<h3 class="detailsHeading">Details Info: <?php echo $details->lastname; ?></h3>
			</header><!-- /header -->
			<summary>
				
				<table class="table table-striped" colleps>
					<thead>
						<tr>
							<th>Item Name</th>
							<th>Fillup Value</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($details as $k => $sdetail):
									
									switch($k){
										case 'firstname':
											echo '<tr>';
											echo '<th>First Name</th>';
											echo '<td><span class="details">'.$sdetail.'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'lastname':
											echo '<tr>';
											echo '<th>Last Name</th>';
											echo '<td><span class="details">'.$sdetail.'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'nickname':
											echo '<tr>';
											echo '<th>Nick Name</th>';
											echo '<td><span class="details">'.$sdetail.'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'idnumber':
											echo '<tr>';
											echo '<th>ID Number</th>';
											echo '<td><span class="details">'.$sdetail.'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'phonenumber':
											echo '<tr>';
											echo '<th>Phone Number</th>';
											echo '<td><span class="details">'.$sdetail.'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'dateofbirth':
											echo '<tr>';
											echo '<th>Date of Birth</th>';
											echo '<td><span class="details">'.date('Y/m/d', strtotime($sdetail)).'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.date('Y-m-d', strtotime($sdetail)).'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'civilstatus':
											echo '<tr>';
											echo '<th>Civil Status</th>';
											echo '<td><span class="details">'.$sdetail.'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'academiclevel':
											echo '<tr>';
											echo '<th>Academic Level</th>';
											echo '<td><span class="details">'.$sdetail.'</span>
											<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
											<span alt="f464" class="edit dashicons dashicons-edit"></span>
											</td></tr>';
										break;
										case 'others':
												for($i=0; count($others) > $i; $i++){
													$otherSitem = explode('=', $others[$i]);
													echo '<tr>';
														echo '<th>'.ucfirst(ltrim($otherSitem[0])).'</th>';
														$ov = ($otherSitem[1] != '0')?$otherSitem[1]:'';
														echo '<td><span class="details">'.$ov.'</span>
														<input class="hidden" type="text" name="others['.ltrim($otherSitem[0]).']" value="'.$ov.'" />
														<span alt="f464" class="edit dashicons dashicons-edit"></span>
														</td>';
													echo '</tr>';
												}
											
										break;
										
										
										default:
										echo '<tr>';
										echo '<th>'.ucfirst($k).'</th>';
										echo '<td><span class="details">'.$sdetail.'</span>
										<input class="hidden" type="text" name="'.$k.'" value="'.$sdetail.'" />
										<span alt="f464" class="edit dashicons dashicons-edit"></span>
										</td></tr>';
									}
									
								
								
						endforeach; ?>
					</tbody>
				</table>

			</summary>

			<footer class="mt20" style="display:block; overflow:hidden;">
				<div class="buttongroup mt20">
					<a class="button button-primary pull-left" href="<?php echo admin_url( $path = '/admin.php?page=crm', $scheme = 'admin' ); ?>">Back</a>
					<input type="submit" name="update" value="Update" class="button button-primary pull-right" />
				</div>
				<input type="hidden" name="entry_id" value="<?php echo $entryID; ?>"/>
			</footer>
			</form>
		</section>
	</div>
</div>