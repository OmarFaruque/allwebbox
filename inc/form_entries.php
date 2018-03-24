<?php
function Form_entries(){
	global $wpdb;
	$entryTable   = $wpdb->prefix . 'awe_entry';

    $qry="select * from ".$entryTable." ORDER BY `date` DESC";
    $formsEntryData=$wpdb->get_results($qry, OBJECT);
	echo "<h2>Form Entries</h2>";
?>

<hr>
<form style="max-width:1150px;" method='get' action='<?php echo admin_url( $path = '/admin.php?page=crm', $scheme = 'admin' ); ?>'>
<input type="hidden" name="page" value="crm">
<button type="submit" class="btn blue pull-right">Send Email</button>
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
			               <td> <a href='".admin_url( $path = 'admin.php?page=crm&details=' . $value->id . '&form-id='.$value->form_id , $scheme = 'admin' )."'>".$name."</a></td>
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
<?php
}
?>