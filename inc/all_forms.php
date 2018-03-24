<?php
global $wpdb;
$prefix = $wpdb->prefix; 
$form_table = $prefix . 'awe_forms';
$entryTbl = $prefix . 'awe_entry'; 
$brandsms = $prefix . 'brandsms';
$ip=$_SERVER['REMOTE_ADDR'];



/*Test*/
/*$qry = $wpdb->get_row('SELECT e.`id`, e.`email`, e.`brandsms_count`, b.`msgtype`, e.`smslastdate`, b.`brand_icon`, b.`msgduration`, b.`msg`, e.`brandsms_count`, e.`brand` FROM '.$entryTbl.' e LEFT JOIN '.$brandsms.' b ON e.brand=b.brand_name WHERE e.brand!="" AND (b.msgtype="pushtobrowser" OR b.msgtype="sms") AND e.ip="'.$ip.'"  ORDER BY e.`brandsms_count` ASC', OBJECT);

        echo 'qry: '.$ip.' <br/>';
        echo '<pre>';
        print_r($qry);
        echo '</pre>';*/

        /*$browser = get_browser(null, true);
        echo '<pre>';
        print_r($browser);
        echo '</pre>';*/
      /*  function tergateSMSDate($lastD, $duration){
            $prevDate = ($lastD !='0000-00-00')?$lastD:date('Y-m-d', strtotime("-1 day"));
            $nextDate = '';
            if($duration == 'day'){
                $nextDate .= date('Y-m-d', strtotime($prevDate . ' +1 day'));
            }elseif($duration == 'week'){
                $nextDate .= date('Y-m-d', strtotime($prevDate . ' +7 days'));
            }else{
                $nextDate .= date('Y-m-d', strtotime($prevDate . ' +30 days'));
            }
            return $nextDate;
        }
*/


          //  $nextCount = $qry->brandsms_count + 1;
            /*echo 'current Count: ' . $qry->brandsms_count . '<br/>';
            echo 'next Count: ' . $nextCount . '<br/>';*/
            // components for our email
           // $nextDate = tergateSMSDate($qry->smslastdate, $qry->msgduration);
           
            //echo 'prev date: '. $prevDate . '<br/>';
           /* echo 'lastsms date: '. $qry->smslastdate . '<br/>';
            echo 'today: '. date('Y-m-d') . '<br/>';
            echo 'next Date: ' . $nextDate . '<br/>';

            $msg = json_decode($qry->msg);
            $recepients = $qry->email;
            $subject = 'Hello from your Cron Job';
            $message = stripslashes($msg[$qry->brandsms_count]);

                preg_match_all("/\[([^\]]*)\]/", $message, $matches);
                foreach($matches[0] as $mk => $sm){
                    $vFind = $matches[1][$mk];
                    $getMatchFDB = $wpdb->get_row('SELECT `'.$matches[1][$mk].'` FROM '.$entryTbl.' WHERE id='.$qry->id.'', OBJECT);
                    $message = str_replace($sm, $getMatchFDB->$vFind, $message);
                }

            echo 'Message:  <br/>';
            echo $message . '<br/>';*/
/*
            if(isset($msg[0])){
                echo 'exist <br/>';
            }else{
                echo 'Not exist <br/>';
            }
            */
            // let's send it 
            //wp_mail($recepients, $subject, $message);   


/*End Test*/


echo "<h2>All Forms</h2>";

 $querystr="select * from $form_table ORDER BY `row_id` DESC";
 $all_forms = $wpdb->get_results($querystr, OBJECT);


if(isset($_GET['fm'])) {
?>
<center><h3 style='color:green;'>Your Form has been created Successfully </h3></center>
<?php } ?>


<table id="formList" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Form Name</th>
                <th>Short Code </th>
                <th>Total Enteries</th>
                <th>Form Created Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Form Name</th>
                <th>Short Code </th>
                <th>Total Enteries</th>
                <th>Form Created Date</th>
                <th>Action</th>
            </tr>
        </tfoot>
        <tbody>
			 <?php 
			 foreach($all_forms as $val) {
				 $id=$val->row_id;

              $qry="select * from $entryTbl where form_id='$id'";
              $allentry = $wpdb->get_results($qry, OBJECT);

			 ?>
            <tr>
                <td><a href='?id=<?php echo $id; ?>&page=my-menu'><?php echo $val->form_name;?></a> </td>
                <td>[form-custom id="<?php echo $id;?>"]  </td>
                <td><?php echo count($allentry);?> </td>
                <td><?php echo $val->created_dt;?> </td>      
                <td>
                    <a href="<?php echo admin_url( $path = 'admin.php?page=my-menu&id=' . $id, $scheme = 'admin' ) ?>">Edit</a>
                    &nbsp;|&nbsp;
                    <a href="#delete" class="deleteMainForm" data-id="<?php echo $id; ?>" title="Delete">Delete</a>
                </td>
			 </tr> <?php } ?> 
              
 

        </tbody>
    </table>
