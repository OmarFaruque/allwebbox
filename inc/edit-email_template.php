<?php 
/*
* Edit Email Template
*/


/*
* Update Query
*/
if(isset($_POST['edit_id'])){
	$name    = $_POST['name'];
  $template   =   stripslashes($_POST['tmplate']);
	
	$update = $wpdb->update(
		$template_table,
		array(
			'name' 	=> $name,
			'tmplate' 	=> $template
		),
		array(
			'id' 	=> $_POST['edit_id']
		)
	);
}
$sTemp = $wpdb->get_row('SELECT * FROM '.$template_table.' WHERE `id`='.$_GET['id'].' ', OBJECT);


?>
<div class="back">
	<a class="button button-primary pull-right" href="<?php echo admin_url( $path = 'admin.php?page=email_templates#allJourney', $scheme = 'admin' ) ?> " title="Back to List"><< Back to List</a>
</div>
<div id="editJourney">
	<div class="innermail">
    <h2>Edit Template: <?php echo $sTemp->name; ?></h2>
    <form action="" method="post" accept-charset="utf-8">
        <div class="form-group">
          <label for="template_name">Template Name</label>
          <input type="text" required name="name" id="template_name" value="<?php echo $sTemp->name; ?>" class="form-control p5">
        </div>
        <br>
        <div class="userParemeters" id="campaignPrameter">
            <label><?php echo __('User Parameters', 'allwebbox'); ?> <span alt="f139" class="dashicons dashicons-arrow-right"></span></label>
            <ul class="usParementslist hidden">
              <?php 
              $columns = $wpdb->get_col("DESC " . $entryTbl, 0);
              foreach($columns as $sCl): ?>
                <li data-param="<?php echo $sCl; ?>">[<?php echo $sCl; ?>]</li>
              <?php endforeach; ?>
            </ul>
        </div>
        <div class="newTemplate">
          <textarea style="width:100%; min-height:250px;" name="tmplate" class="form-control tinymce"><?php echo $sTemp->tmplate; ?></textarea>
        </div>
        <input type="hidden" name="edit_id" value="<?php echo $_GET['id']; ?>">
        <button type="submit" class="button button-primary">Submit</button>
    </form>
  </div>
</div>
