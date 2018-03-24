<?php 
/*
* SMS And Message Template
*/
global $wpdb;
$form_table = $wpdb->prefix . 'awe_forms';
$entryTbl 	= $wpdb->prefix . 'awe_entry'; 
$querystr="select `brand_options` from $form_table WHERE brand_options!='' ORDER BY `row_id` DESC";


$all_brands = $wpdb->get_results($querystr, OBJECT);


$brandArray = array();

foreach($all_brands as $sB){
	$sArray = json_decode($sB->brand_options);
	for($i=0; count($sArray) > $i; $i++ ) array_push($brandArray, $sArray[$i]);
}
$brandArray = array_unique($brandArray);


?>
<div class="messagensms">
	<?php if(!isset($_GET['bdt'])): ?>
	<table id="formBrand" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>SL</th>
                <th>Brand Name</th>
                <th>Brand User</th>
                <th>Action</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>SL</th>
                <th>Brand Name</th>
                <th>Brand User</th>
                <th>Action</th>
            </tr>
        </tfoot>
        <tbody>
			 <?php 
			 $c = 1;
			 foreach($brandArray as $sBrand) {
			 ?>
            <tr>
            	<td style="text-align:center;"><?php echo $c; ?></td>
                <td><a href='<?php echo admin_url( $path = 'admin.php?page=crm&bdt=' . $sBrand, $scheme = 'admin' ) ?>'><?php echo $sBrand; ?></a> </td>
                <td style="text-align:center;">0</td>
                <td style="text-align:center;"><?php echo __('SMS/Message', 'allwebbox'); ?></td>      
			 </tr> <?php $c++; } ?> 
        </tbody>
    </table>
	<?php else: ?>
		<?php require_once(ALWEBDIR . 'inc/single-brand.php'); ?>
	<?php endif; ?>
</div>