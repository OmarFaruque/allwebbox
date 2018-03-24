<?php
/*
* Email Markeing Campaign
*/
	global $wpdb;
	$entryTable   		= $wpdb->prefix . 'awe_entry';
	$template_table 	= $wpdb->prefix . 'template_table';
  	$qry="select * from ".$entryTable."";
    $formsEntryData=$wpdb->get_results($qry, OBJECT);
?>
<hr>
<?php if(!isset($_GET['send_mails'])): ?>
<form style="max-width:1150px;" method='get' action='<?php echo admin_url( $path = '/admin.php?page=crm', $scheme = 'admin' ); ?>'>
<input type="hidden" name="page" value="email_markeging_campaigns">
<button type="submit" class="btn blue pull-right">Start Campaign</button>
<table id="searchReasult" class="display" cellspacing="0" width="100%">
        <thead>
            <tr> <th><input type='checkbox' id='selbtn' onClick='selectAll()'/></th>
                <th>Name</th>
                <th>Email </th>
                <th>Id Number</th>
                <th>Form Name</th>
                <th>Entry Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><input type='checkbox'/></th>
                <th>Name</th>
                <th>Email </th>
                <th>Id Number</th>
                <th>Form Name</th>
                <th>Entry Date </th>
                <th>Action</th>
            </tr>
        </tfoot>
        <tbody>
        <?php  
		 $html="";
		 foreach($formsEntryData as $value) {
             $name = '';
             if($value->lastname !=''){
                $name .= $value->lastname;
             }elseif($value->nickname !=''){
                $name .= $value->nickname;
             }else{
                $name .= $value->firstname;
             }
             $fname = $wpdb->get_row('SELECT `form_name` FROM '.$frmTable.' WHERE row_id='.$value->form_id.'', OBJECT);
		         $html.="<tr>
		                 <td style='text-align:center;'><input type='checkbox' name='send_mails[]' value='".$value->email."'/></td>  
			               <td> <a href='".admin_url( $path = 'admin.php?page=crm&details=' . $value->id. '&form-id='.$value->form_id, $scheme = 'admin' )."'>".$name."</a></td>
			               <td>".$value->email."</td>
					           <td>".$value->idnumber."</td>
					           <td>".$fname->form_name."</td>
                     <td style='text-align:center;'>".$value->date."</td>
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
<?php else: // else if(!isset($_GET['send_mails'])); ?>
<?php 
$exTemplates = $wpdb->get_results('SELECT * FROM '.$template_table.'', OBJECT);
if(isset($_GET['ltmpid'])){
	$exTmp = $wpdb->get_row('SELECT `tmplate` FROM '.$template_table.' WHERE id='.$_GET['ltmpid'].'', OBJECT);
}
if(isset($_POST['sendto'])){
	/*echo '<pre>';
	print_r($_POST);
	echo '</pre>';*/
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
		<div id="sendSelectedEmail" class="innerpageallwebbox">
			<form action="" method="post" accept-charset="utf-8">
				<div class="suecessMessage">
					<?php echo ($msg)?$msg:''; ?>
				</div>
				<div class="form-group">
					<label for="campainfor">Email Campaign for</label>
					<select class="form-control" id="campainfor" name="campainfor">
						<option value="registered">Registered User</option>
						<option value="new">New User</option>
					</select>
				</div>
				<div class="form-group">
					<label for="assign_page">Landing Page / Form</label>
					<select class="form-control" id="assign_page" name="assign_page">
						<option value="">Select Landing Page</option>
						<?php 
						$allpages = get_all_page_ids();
							foreach($allpages as $sp){
								$slted = (isset($_POST['assign_page']) && $_POST['assign_page'] == $sp)?'selected':'';
								echo '<option '.$slted.' value="'.$sp.'">'.get_the_title( $sp ).'</option>';
							}
						?>
					</select>
				</div>
				<div class="form-group">
			      <div class="toEmail" id="sendTo">
			        <label>Send To</label>
			        <div id="innersendTo">
				        <?php foreach(array_unique($_GET['send_mails']) as $se): ?>
				          <input type="hidden" name="sendto[]" value="<?php echo $se; ?>">
				          <span class="semail"><?php echo $se; ?></span>
				        <?php endforeach; ?>
			    	</div>

			      </div>
			    </div>
			    <div class="form-group">
			    	<label for="esubject">Email Subject</label>
			    	<input type="text" name="esubject" class="form-control" id="esubject" value="<?php echo (isset($_POST['esubject']))?$_POST['esubject']:''; ?>" />
			    </div>

				<div class="form-group">
					<div class="halfDiv">
						<div class="pull-left">
							<label for="content">Content</label>
						</div>
						<div class="pull-right">
							<div class="inlinelabel">
								<label for="loadExistingTemplate"></label>
								<select id="loadExistingTemplate" name="loadTemplate">
									<option value="">Load Template...</option>
									<?php foreach($exTemplates as $sT):
										$selected = (isset($_GET['ltmpid']) && $_GET['ltmpid'] == $sT->id )?'selected':'';
									?>
										<option <?php echo $selected; ?> value="<?php echo $sT->id; ?>"><?php echo $sT->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<textarea style="width:100%; min-height:350px;" name="content" id="content">
						<?php 
							echo (isset($_GET['ltmpid']))?$exTmp->tmplate:'';
						?>
					</textarea>
				</div>
				<br>
				<input type="submit" name="campain_submit" value="Submit" class="button button-primary" />
			</form>
		</div>
	</div>

<?php endif; //End if(!isset($_GET['send_mails'])); ?>