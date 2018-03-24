<?php 
/*
* Email Templates 
*/


global $wpdb;
$template_table = $wpdb->prefix . 'template_table';
?>
<div class="wrap email_templates_single bgff p20">
  
<?php if(!isset($_GET['tm-action'])): ?>
  <div class="emalitoptabs">
    <ul>
      <li><a class="active" href="#newTemplkate" title="New Template">New Template</a></li>
      <li><a href="#allJourney" title="All Journey">All Template</a></li>
    </ul>
  </div>


<?php
 if(isset($_POST['form_submit_email_final'])){
    $to='';
    foreach($_POST['srcmail'] as $val) {
    $to.=$val.',';
    }
    
      $to=rtrim($to,","); 
     $body_email=$_POST['area2'];
  
     $body_email=stripslashes($body_email);
   
   
    // Always set content-type when sending HTML email
    $subject ="Mail From Plugin";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // More headers
    $headers .= 'From: <mohit.suthar17@gmail.com>' . "\r\n";
     

    mail($to,$subject,$body_email,$headers);
 
} //End Submit email final

/*
* Journey email insert to DB
*/
if(isset($_POST['name'])){
	$name 		=	$_POST['name'];
    $template 	= 	stripslashes($_POST['tmplate']);

    $insert = $wpdb->insert(
      $template_table, 
      array(
        'name'    	=> $name,
        'tmplate'   => $template
      ),
      array(
        '%s',
        '%s'
      )
    );

    if($insert){
        echo 'success';
    }else{
      echo 'faild';
    }
}// End Insert


 ?>

<!--<script src="https://rawgit.com/dbrekalo/attire/master/dist/js/build.min.js"></script>-->

<!-- Start New Journey -->
<div id="newTemplkate" class="active section">
  <div class="innermail">
    <h2>Add New Template</h2>
    <form action="" method="post" accept-charset="utf-8">
        <div class="form-group">
          <label for="template_name">Template Name</label>
          <input type="text" required name="name" id="template_name" value="<?php echo ($_POST['name'])?$_POST['name']:''; ?>" class="form-control p5">
        </div>
        <br>
        <div class="newTemplate">
        	<label for="tmplate">Content</label>
          	<textarea style="width:100%; min-height:250px;" id="tmplate" min-height:120px;" name="tmplate" class="form-control"><?php echo ($_POST['tmplate'])?stripslashes($_POST['tmplate']):''; ?></textarea>
        </div>

        <button type="submit" class="button button-primary">Submit</button>
    </form>
  </div>
</div>

<div id="allJourney" class="hidden section">
    <div class="innermail">
        <h2>All Email Template</h2>
        <table class="table table-striped">
          <caption>All Email Template List</caption>
          <thead>
            <tr>
              <th>Sl</th>
              <th>Name</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
        <?php 
          $alltemplate = $wpdb->get_results('SELECT * FROM '.$template_table.'', OBJECT);
          $count = 1;
          foreach($alltemplate as $sTmp){ ?>
              <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php echo $sTmp->name; ?></td>
                  <td>
                    <a href="<?php echo admin_url( $path = '/admin.php?page=email_templates&tm-action=edit&id=' . $sTmp->id, $scheme = 'admin' ); ?>" title="Edit">Edit</a>&nbsp;|&nbsp; 
                    <a class="templateDelete" data-id="<?php echo $sTmp->id; ?>" href="#" title="Delete">Delete</a>
                  </td>
              </tr>
          <?php $count++; }
        ?>
        </tbody>
        </table>
    </div>  
</div>




<div id="seneEmail" class="hidden section">
    <br>
    <h2>Send Emails</h2>
      <form method='post' action=''> 
       <?php 
         foreach($_POST['mails'] as $val) {
    echo $val.",";
    }
       ?>

       <?php
       if(isset($_POST['form_submit_email'])) {
        foreach($_POST['mails'] as $val) { 
       echo "<input type='hidden' name='srcmail[]' value='$val'/>"; 
       }
       }
       
       ?>
      <select class="multipleSelect" multiple name="language" width="400px;">
      <?php  $g=0;
          foreach($_POST['mails'] as $val) {
                   $mailDate=explode("@",$val);
               echo "<option value='$g' selected>".$mailDate[0]."&commat;".$mailDate[1]. "</option>";
$g++;
         }
       ?>
      
      </select>
      <textarea style="width:100%; min-height:120px;" name="area2">
           Dear [Name]
           <br />
           <br />
           <br />Thank you 
      </textarea><br />
      <button type="submit" name="form_submit_email_final" class="btn blue pull-right">Send Email</button>
    </form>
    </div> 
  <?php else:
    require_once(ALWEBDIR . 'inc/edit-email_template.php');
  endif; // End isset get action; ?>
  </div>
 <?php
 
 
 


 
 

?>