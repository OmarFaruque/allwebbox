<?php

function wpdocs_set_html_mail_content_type() {
   	return 'text/html';
}


function view_area($form_id) {
	global $wpdb;
	global $post;
	$form_table 	= $wpdb->prefix . 'awe_forms'; 
	$q_question 	= $wpdb->prefix . 'awe_customq'; 
	$f_q_value 		= $wpdb->prefix . 'awe_formqansvalue';
	$entry_table 	= $wpdb->prefix . 'awe_entry';
	$journeyTbl 	= $wpdb->prefix . 'awe_journey';
	$brandsms 		= $wpdb->prefix . 'brandsms';
	$ip=$_SERVER['REMOTE_ADDR'];

	/* Is Landing page from Email */
	$editByUsr = false;
	if(isset($_POST['rootEdit'])){
		$editByUsr = true;
		$getUserD = $wpdb->get_row('SELECT * FROM '.$entry_table.' WHERE id='.$_POST['r_entry_id'].'');

		$getOthers = $getUserD->others;
		$getOthers = explode(';', $getOthers);
	}

	/* End Is Landing page from Email */




 	//$EntryCol = $wpdb->get_col("DESC {$entry_table}", 0);
 	/*$cols_sql = "DESCRIBE $entry_table";
	$EntryCol = $wpdb->get_results( $cols_sql );
 	echo '<pre>';
 	print_r($EntryCol);
 	echo '</pre>';*/
	/*
	* Unsubscribe Process
	*/
	if(isset($_GET['j_id']) && isset($_GET['email'])){
		$exisUnsb = $wpdb->get_row('SELECT `j_unsubscribe` FROM '.$journeyTbl.' WHERE id='.$_GET['j_id'].'', OBJECT);
		
		if($exisUnsb->j_unsubscribe){
			$jsonemail = json_decode($exisUnsb->j_unsubscribe);
		}else{
			$jsonemail = array();
		}
		array_push($jsonemail, $_GET['email']);
		$jsonemail = array_unique($jsonemail);
		$jsonemail = json_encode($jsonemail);
		$upUns = $wpdb->update(
			$journeyTbl,
			array(
				'j_unsubscribe' => $jsonemail
			),
			array(
				'id' => $_GET['j_id']
			)
		);
	} //End Unsubscribe Process




	/*
	* Post process
	*/
	if(isset($_POST['aw_submit'])){





			
			















		//$imploadOthers = implode(',', $_POST['others']);
		$otherArray = array();
		if(isset($_POST['others'])){
			foreach($_POST['others'] as $k => $ot) $otherArray[$k] = (empty($ot))?0:$ot;	
		}
		


		$imploadOthers = implode('; ', array_map(
		    function ($v, $k) 
		    { 
		    	if(is_array($v)){
		    		$outp = array();
		    		foreach($v as $sv) {
		    			array_push($outp, sprintf("%s", $sv));
		    		}
		    		$out = $k . '=' . implode(', ', $outp);
		    		return $out;
		    	}else{
		    		return sprintf("%s=%s", $k, $v); 	
		    	}
		    },
		    $otherArray,
		    array_keys($otherArray)
		));



		//$_POST['others'] 		= $imploadOthers;
		$idQArray = (isset($_POST['idQ']))?$_POST['idQ']:array();
		$posts 						= array_merge($idQArray, $_POST['contact_info']);
		$posts['others'] 			= $imploadOthers;
		$posts['brand'] 			= json_encode($_POST['idQ']['brand']);
		$posts['form_id'] 			= $_POST['form_id'];
		$posts['journey_lastdate'] 	= date('Y-m-d H:i:s');
		$posts['smslastdate'] 		= date('Y-m-d');
		$posts['ip'] 				= $ip;
		if(isset($posts['dateofbirth'])) $posts['dateofbirth'] 	= date('Y-m-d', strtotime($posts['dateofbirth']));
		

		$extRecord = $wpdb->get_row('SELECT `email` FROM '.$entry_table.' WHERE email="'.$posts['email'].'"', OBJECT);



		$qrTrye = false;
		$qrUdate = false;

		if(!$extRecord && !isset($_POST['edit_entry'])){
			$qrTrye = true;
		}elseif($extRecord && isset($_POST['edit_entry'])){
			$qrUdate = true;
		}elseif(!isset($_POST['edit_entry'])){
			$exmsg = 'exist';
		}

		
		if($qrTrye == true)
		{
				$insert = $wpdb->insert(
					$entry_table,
					$posts
				);		
		}elseif($qrUdate == true){
			//echo 'Inside Update <br/>';
			$update = $wpdb->update(
				$entry_table,
				$posts,
				array(
					'id' => $_POST['edit_entry']
				)
			);
		}


		if(isset($_POST['edit_entry']) && $update){
			$uMsg = 'success';
		}elseif(isset($_POST['edit_entry']) && !$update){
			$uMsg = 'fail';
		}


		if(isset($insert) && $qrTrye == true)
		{	

			$smsg = 'success';	

			/*
			* Mail to user & admin
			*/
			
			$adminEm = get_option( 'admin_email', $default = false );
			$siteTitle = get_bloginfo( $show = 'name', $filter = 'raw' );

			$allinputs = array_merge($_POST['contact_info'], $_POST['others'], $_POST['idQ']);
			$mtitle = __('Below Information are fill-up by user', 'allwebbox');

			$emailOutput = '';
			$userEmail = '';
			if(isset($allinputs) && count($allinputs) > 0){
				$emailOutput .= '<h2 style="font-size:20px;">'.$mtitle.': </h2>';
				$emailOutput .= '<table style="width:100%; border:1px solid #ddd; border-collapse:collapse;"><tbody>';
				foreach($allinputs as $k => $s_em){
					switch($k){
						case'dateofbirth':
						$k = 'Date of Birth'; 
						break;
						case 'firstname':
						$k = 'First Name';
						break; 
						case 'lastname':
						$k = 'Last Name';
						break;
						case 'email':
						$userEmail = $s_em;
						break;
						case 'brand':
						$s_em = implode(', ', $s_em);
						$k = ucfirst($k);
						break;

						default:
						$k = ucfirst($k);
					}

				$emailOutput .=	'<tr>
									<th style="text-align:left;border:1px solid #ddd; padding:5px 10px;">'.$k.'</th>
									<td style="border:1px solid #ddd; padding:5px 10px;">'.$s_em.'</td>
								</tr>';
						
				}
				$emailOutput .='</tbody></table>';	


				$headers = "From: $siteTitle <$adminEm>" . "\r\n";	
				//$headers[] = 'Content-Type: text/html; charset=UTF-8' . "\r\n";
				$adminSub = __('Nuevo registro de cliente web', 'allwebbox');

				add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
				$adminE = wp_mail( $adminEm, $adminSub, $emailOutput, $headers);
				remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

				if($adminE){
					add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
					wp_mail( $userEmail, $adminSub, $emailOutput, $headers);
					remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
				}

			} // End if(isset($allinputs) && count($allinputs) > 0)

			/*
			* End mail Sent to Admin & user
			*/

			
		} //End if($insert)
		elseif(!isset($_POST['edit_entry']))
		{
			$emsg = 'fail';		
		} //End if($insert) else
	}
	//$allentrys = $wpdb->get_results('SELECT * FROM '.$entry_table.'', OBJECT);


	$countries = array(
	'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia and Herzegowina', 'Botswana', 'Bouvet Island', 'Brazil', 	'British Indian Ocean Territory', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Congo, the Democratic Republic of the', 'Cook Islands', 'Costa Rica', 'Cote d\'Ivoire', 'Croatia (Hrvatska)', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji', 	'Finland', 'France', 'France Metropolitan', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Heard and Mc Donald Islands', 'Holy See (Vatican City State)', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran (Islamic Republic of)', 'Iraq', 'Ireland', 'Israel', 'Italy','Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Korea, Democratic People\'s Republic of', 'Korea, Republic of', 'Kuwait', 'Kyrgyzstan', 'Lao, People\'s Democratic Republic', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macau', 'Macedonia, The Former Yugoslav Republic of', 	'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia, Federated States of', 'Moldova, Republic of', 	'Monaco', 'Mongolia', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 	'Nigeria', 'Niue', 'Norfolk Island', 'Northern Mariana Islands', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Reunion', 'Romania', 'Russian Federation', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia','Senegal', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia (Slovak Republic)', 'Slovenia', 'Solomon Islands','Somalia', 'South Africa', 'South Georgia and the South Sandwich Islands', 'Spain', 'Sri Lanka', 'St. Helena', 'St. Pierre and Miquelon', 'Sudan', 'Suriname', 'Svalbard and Jan Mayen Islands', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic', 'Taiwan, Province of China','Tajikistan','Tanzania, United Republic of', 'Thailand', 'Togo', 'Tokelau', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'United States Minor Outlying Islands', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam', 'Virgin Islands (British)', 'Virgin Islands (U.S.)', 'Wallis and Futuna Islands', 'Western Sahara', 'Yemen', 'Yugoslavia', 'Zambia', 'Zimbabwe'
);
	if(isset($_POST['form_submit'])) {
		/* $form_id=$_POST['form_id'];
		 $user_id=0;
		 
		 $qry="insert into wp_awe_entries(form_id,user_id) VALUES('$form_id','$user_id')";
		 $wpdb->query($qry);
		  $lastid = $wpdb->insert_id;
		 
		 
		 foreach($_POST['wp_awe_identification'] as $val => $key) {
          $qury="insert into wp_awe_identification_ans(user_id,form_id,entry_id,question_id,question_ans) VALUES('$user_id','$form_id','$lastid','$val','$key')";
		 $wpdb->query($qury);
		 }
		
		
		 foreach($_POST['wp_awe_contactinfo_ans'] as $val => $key) {
         $qury="insert into wp_awe_contactinfo_ans(user_id,form_id,entry_id,question_id,question_ans) VALUES('$user_id','$form_id','$lastid','$val','$key')";
		 $wpdb->query($qury);
		 }
		  
		 
		 foreach($_POST['wp_awe_profileques_ans'] as $val => $key) {
         $qury="insert into wp_awe_profileques_ans(user_id,form_id,entry_id,question_id,question_ans) VALUES('$user_id','$form_id','$lastid','$val','$key')";
		 $wpdb->query($qury);
		 }   
		    
		 foreach($_POST['wp_awe_customques_ans'] as $val => $key) {
         $qury="insert into wp_awe_customques_ans(user_id,form_id,entry_id,question_id,question_ans) VALUES('$user_id','$form_id','$lastid','$val','$key')";
		 $wpdb->query($qury);
		 }
		 */
	
        echo "<h2 style='color:green;'><center>Thank you For Submit. Admin Will contact you soon</center></h2>";
	 
	    }	
	



	 //$querystr="select * from ".$form_table." where row_id='$form_id'";
     $forms = $wpdb->get_row("SELECT * FROM $form_table WHERE row_id=".$form_id."", OBJECT);
     

	 
	 
	 //$formcustomerqes=$forms[0]['total_custom_ques'];
	 
	  /*
	  * Forms other element
	  */


	  $basics 		= json_decode($forms->identi_ques);
	  $contacts 	= json_decode($forms->contact_ques);
	  $profile_ques = json_decode($forms->profile_ques); 
	  $custom_ques 	= json_decode($forms->custom_ques); 
	  $terms 		= json_decode($forms->terms);
	  $styles 		= json_decode($forms->style);
	  $brandOptions = json_decode($forms->brand_options);


	  $btnText 		= ($styles->buttontext !='')?$styles->buttontext:__('Submit', 'allwebbox');
	  $rstbtnText 	= ($styles->buttontext !='')?$styles->rstbuttontext:'Reset';


	  
	$html='<div id="entryFormAllWebBox" class="entryForm"><div class="entryFormInner">';
		if(isset($smsg) && $smsg == 'success'){
			$html .='<div id="displMessg" class="success bg-success mb20"><span>Form successfully submit.</span></div>';
		}elseif(isset($emsg) && $emsg == 'fail' && !isset($exmsg)){
			$html .='<div id="displMessg" class="error bg-danger mb20"><span>Form submit failed, try again.</span></div>';
		}elseif(isset($exmsg) && $exmsg == 'exist'){
			$html .='<div id="displMessg" class="error bg-danger mb20"><span>This email already exist. Please try with another email.</span></div>';
		}elseif(isset($uMsg) && $uMsg == 'success'){
			$html .='<div id="displMessg" class="success bg-success mb20"><span>Form successfully Update.</span></div>';
		}elseif(isset($uMsg) && $uMsg == 'fail'){
			$html .='<div id="displMessg" class="error bg-danger mb20"><span>Form update fail, try again.</span></div>';
		}

	$html .='<form class="form allwebboxDynamicForm" action="" method="post" accept-charset="utf-8">';
		if(count($basics) > 0){
			$html .='<div class="row">';
			//$html .= '<div class="col-md-12 col-sm-12"><h3>Identification Information</h3></div>';
			foreach($basics as $bcs):
				$html .='<div class="form-group col-sm-6 col-xs-12 col-sm-6">
					<label for="'. str_replace(' ', '', strtolower($bcs))  .'">'.$bcs.' <small><i>(required *)</i></small></label>';
					switch($bcs){

						case 'Salute (Mr, Ms, Miss, Dr, etc)':
						$pslt = (isset($_POST['idQ']))?$_POST['idQ']['salute']:'Mr';
						$salutesArray = array('Mr', 'Ms', 'Miss', 'Dr');

						$html .= '
							<select required name="idQ['.str_replace(' ', '', strtolower(str_replace(' (Mr, Ms, Miss, Dr, etc)', '', $bcs))).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control">';
							for($sa=0; count($salutesArray) > $sa; $sa++){
								
								$selected = (isset($emsg) && $pslt == $salutesArray[$sa])?'selected':''; 
								//$editByUsr
								if($editByUsr && $selected == '' && $getUserD->salute == $salutesArray[$sa] )
								{
									$selected = 'selected';	
								}
								$html .= '<option '.$selected.' value="'.$salutesArray[$sa].'">'.$salutesArray[$sa].'</option>';
							}	
							$html .='</select>';	
						break;
						case 'Brand':
							$html .= '<br/>';
							$dbBrands = ($editByUsr)?json_decode($getUserD->brand):array(); 
							foreach($brandOptions as $sOpt){
								$bselected = ($editByUsr && in_array($sOpt, $dbBrands))?'checked':'';
								$html .= '<label class="brand checkbox-inline"><input required '.$bselected.' type="checkbox" value="'.$sOpt.'" name="idQ['.strtolower($bcs).'][]" />'.$sOpt.'</label>';
							}
						
						break;
						default:

							$val = (isset($emsg) && isset($_POST['idQ']))?$_POST['idQ'][str_replace(' ', '', strtolower($bcs))]:'';
							$thisN = str_replace(' ', '', strtolower($bcs));

							if($editByUsr && $val == '') $val = $getUserD->$thisN;
							$html .= '<input required value="'.$val.'" type="text" name="idQ['.str_replace(' ', '', strtolower($bcs)).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control" >';
					} // End Switch
				$html .= '</div>';
			endforeach;
			$html .= '</div><hr/>';
		} // Check if array existing basic question

		/*
		* Contact Information
		*/
		if(count($contacts) > 0){
			$html .='<div class="row">';
			//$html .= '<div class="col-md-12 col-sm-12"><h3>Contact Information</h3></div>';
			foreach($contacts as $bcs):
				$val = (isset($emsg) && isset($_POST['contact_info']))?$_POST['contact_info'][str_replace(' ', '', strtolower(str_replace('+', '', $bcs)))]:'';
				$thisN = str_replace(' ', '', strtolower($bcs));
				if($editByUsr) $val = $getUserD->$thisN;

				
				$html .='<div class="form-group col-sm-6 col-xs-12 col-sm-6">
					<label for="'. str_replace(' ', '', strtolower($bcs))  .'">'.ucfirst($bcs).' <small><i>(required *)</i></small></label>';
					switch($bcs){
						case 'country':
						$html .= '
							<select required name="contact_info['.str_replace(' ', '', strtolower($bcs)).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="chosen form-control">
							<option>Select Country</option>';
								for($cn=0; count($countries) > $cn; $cn++ ){
									$sleced = (isset($emsg) && $val == $countries[$cn])?'selected':'';
									if($editByUsr && $val == $countries[$cn]) $sleced = 'selected'; 
									$html .= '<option '.$sleced.' value="'.$countries[$cn].'">'.$countries[$cn].'</option>';
								}
							$html .= '</select>
						';	
						break;
						case 'email':
							$html .= '<input required value="'.$val.'" type="email" name="contact_info['.str_replace(' ', '', strtolower($bcs)).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control" >';
						break;
						case 'Phone Number':
							$html .= '<input required type="tel" value="'.$val.'" name="contact_info['.str_replace(' ', '', strtolower($bcs)).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control" >';
						break;
						default:
							$html .= '<input required type="text" value="'.$val.'" name="contact_info['.str_replace(' ', '', strtolower(str_replace('+', '', $bcs))).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control" >';
					} // End Switch
				$html .= '</div>';
			endforeach;
			$html .= '</div><hr/>';
		} // Check if array existing basic question


		/*
		* Profiling Questions
		*/
		if(count($profile_ques) > 0){
			$html .='<div class="row">';
			//$html .= '<div class="col-md-12 col-sm-12"><h3>Profiling Information</h3></div>';
			foreach($profile_ques as $bcs):
				$val = (isset($emsg) && isset($_POST['contact_info']))?$_POST['contact_info'][str_replace(' ', '', strtolower($bcs))]:'';
				$thisN = str_replace(' ', '', strtolower($bcs));
				if($editByUsr) $val = $getUserD->$thisN;

				$html .='<div class="form-group col-sm-6 col-xs-12 col-sm-6">
					<label for="'. str_replace(' ', '', strtolower($bcs))  .'">'.ucfirst($bcs).' <small><i>(required *)</i></small></label>';
					switch($bcs){
						case 'Date of Birth':
							$html .= '<input value="'.$val.'" required type="text" placeholder="yyyy-mm-dd" name="contact_info['.str_replace(' ', '', strtolower($bcs)).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control datepicker" >';
						break;
						default:
							$html .= '<input type="text" required value="'.$val.'" name="contact_info['.str_replace(' ', '', strtolower($bcs)).']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control" >';
					} // End Switch
				$html .= '</div>';
			endforeach;
			$html .= '</div><hr/>';
		} // Check if array existing basic question


		/*
		* Other's Information
		*/


		if(count($custom_ques) > 0){

			$otherCnt = 1;
			$html .='<div class="row">';
			//$html .= '<div class="col-md-12 col-sm-12"><h3>Other Information</h3></div>';
			foreach($custom_ques as $bk => $bcs):
				$selectType = $wpdb->get_row("SELECT `row_id`, `answer_type`, `required` FROM $q_question WHERE `questions`= '$bcs'", OBJECT);

				$val = (isset($emsg) && isset($_POST['others']))?@$_POST['others'][$bcs]:'';

				//$thisN = str_replace(' ', '', strtolower($bcs));
				if($editByUsr){
					$getDV = explode('=', $getOthers[$bk]);
					$val = ($getDV[1] != '0')?$getDV[1]:'';
				}
				
				$required = (isset($selectType->required) && $selectType->required == '1')?'required':'';
				$reqTExt = (isset($selectType->required) && $selectType->required == '1')?'<small><i>(required *)</i></small>':'';



				$oddeven = ($otherCnt%2 == 0)?' even':' odd';
				$class = (isset($selectType->answer_type) && $selectType->answer_type == 2)?'col-sm-12 col-xs-12 col-sm-12':'col-sm-6 col-xs-12 col-sm-6';
				$html .='<div class="'.$class.''.$oddeven.'">
					<div class="form-group">
					<label for="'. str_replace(' ', '', strtolower($bcs))  .'">'.ucfirst($bcs).' '.$reqTExt.''.'</label>';
						if($selectType){
							switch($selectType->answer_type){
								case 3:
									$soptions = $wpdb->get_results("SELECT `row_id`, `ques_value` FROM $f_q_value WHERE `entry_id`= '$selectType->row_id'", OBJECT);
									$html .='<select '.$required.' name="others['.$bcs.']" class="form-control">';
										for($c=0; count($soptions) > $c; $c++){
											$seltd = (isset($emsg) && $val == $soptions[$c]->ques_value)?'selected':'';
											if($editByUsr && $val == $soptions[$c]->ques_value) $seltd = 'selected';
											$html .='<option '.$seltd.' value="'.$soptions[$c]->ques_value.'">'.$soptions[$c]->ques_value.'</option>';
										}
									$html .='</select>';
								break;
								case 4:
									$soptions = $wpdb->get_results("SELECT `row_id`, `ques_value` FROM $f_q_value WHERE `entry_id`= '$selectType->row_id'", OBJECT);

									//$html .='<select '.$required.' multiple name="others['.$bcs.'][]" class="form-control">';
									$html .= '<br/>';
										for($c=0; count($soptions) > $c; $c++){
											$sltd = (isset($emsg) && $val == $soptions[$c]->ques_value)?'checked':'';
											if($editByUsr && $val == $soptions[$c]->ques_value) $sltd = 'checked';

											//$html .='<option '.$sltd.' value="'.$soptions[$c]->ques_value.'">'.$soptions[$c]->ques_value.'</option>';
											$html .= '<label  class="questom '.$required.' checkbox-inline"><input '.$sltd.' type="checkbox" '.$required.' name="others['.$bcs.'][]" value="'.$soptions[$c]->ques_value.'" />'.$soptions[$c]->ques_value.'</label>';

										}
									//$html .='</select>';
								break;
								case 2: 
									$html .= '<textarea '.$required.' id="'. str_replace(' ', '', strtolower($bcs))  .'" name="others['.$bcs.']" class="form-control">'.$val.'</textarea>';
								break;
								case 5: 
									$html .= '<input type="number" '.$required.' class="form-control" name="others['.$bcs.']" value="'.$val.'"/>';
								break;
								case 6: 
									$html .= '<input type="text" class="datepicker '.$required.' form-control" name="others['.$bcs.']" value="'.$val.'"/>';
								break;

								case 7: 
									$html .= '<input type="email" class="form-control" '.$required.' name="others['.$bcs.']" value="'.$val.'"/>';
								break;								
								default:
								$html .= '<input type="text" '.$required.' name="others['.$bcs.']" id="'.str_replace(' ', '', strtolower($bcs)).'" class="form-control" value="'.$val.'" >';
							}

						}else{
							$html .= '<input type="text" '.$required.' name="others['.$bcs.']" id="'.str_replace(' ', '', strtolower($bcs)).'" value="'.$val.'" class="form-control" >';
						}
				$html .= '</div></div>';
				$otherCnt++;
			endforeach;
			$html .= '</div>';
		} // Check if array existing basic question
		if(isset($terms->active) && $terms->active == 1 && $terms->text!=''){
			$termText = ($terms->link !='')?'<a target="_blank" href="'.get_the_permalink( $terms->link ).'">'.$terms->text.'</a>':$terms->text;
			$html .= '<div class="row"><div class="terms col-sm-12 col-xs-12 col-sm-12"><div class="form-check">';
				$html .= '<label class="form-check-label"><input style="margin-left:5px;" type="checkbox" id="termsnCondition" class="form-check-input" value="1" required autofocus name="term"/>&nbsp;&nbsp;'.$termText.'</label>';
			$html .= '</div></div></div>';
		}
		$html .= '<input type="hidden" value="'.$forms->row_id.'" name="form_id" />';
		$html .= '<input type="hidden" value="'.$forms->journey.'" name="journey" />';
		if($editByUsr) $html .= '<input type="hidden" value="'.$_POST['r_entry_id'].'" name="edit_entry"/>';
		$html .= '<input type="hidden" value="'. $post->ID .'" name="assign_page"/>';
		$html .= '<div style="margin-top:30px;" class="btn-group"><input class="btn btn-submit btn-primary" type="submit" value="'.$btnText.'" name="aw_submit"/>';
		
		if(isset($_POST['aw_submit'])){
			$html .= '<a class="btn btn-primary reset" href="'.get_the_permalink( $post->ID, $leavename = false ).'">'.$rstbtnText.'</a>';
		}
		$html .= '</div>';

	$html .'</form></div>';
	$html .= '</div>'; 
	

	/*
	* CSS
	*/
     $bgimg 	= ($styles->backgroundimg !='')?wp_get_attachment_url( $styles->backgroundimg ):'';
     $bgrepet 	= ($styles->bgimagerepeat == 'yes')?'repeat':'no-repeat';	 

     $html .= '<style>
     div#entryFormAllWebBox{
     	background-color:'.$styles->backgroundc.';
     	padding:'.$styles->boxpadding.'px;
     	background-image:url('.$bgimg.');
     	background-repeat:'.$bgrepet.';
     	background-attachment:'.$styles->bgimageattachment.';
     	background-size:'.$styles->bgsize.';
     	position:relative;
     }

     div#entryFormAllWebBox label{
     	color:'.$styles->textcolor.';
     }
     .allwebboxDynamicForm .btn-group a.btn:hover,
     div#entryFormAllWebBox input[type="submit"]:hover{
     	background-color:'.hex2rgba($styles->buttoncollor, 0.8).';
     	border-color:'.hex2rgba($styles->buttoncollor, 0.8).';
     }
     .allwebboxDynamicForm .btn-group a.btn,
     div#entryFormAllWebBox input[type="submit"]{
     	background-color:'.$styles->buttoncollor.';
     	border-color:'.$styles->buttoncollor.';
     }';
    if(isset($styles->bgoverley) && $styles->bgoverley == 1){
		$html .= '
		div#entryFormAllWebBox .entryFormInner:before{
			background-color: '.hex2rgba($styles->overleycolor, $styles->ovrltrans).';
		}
		';
	}
     
     $html .='</style>';

	return $html;
   }




   /* Convert hexdec color string to rgb(a) string */
 
function hex2rgba($color, $opacity = false) {
 
	$default = 'rgb(0,0,0)';
 
	//Return default if no color provided
	if(empty($color))
          return $default; 
 
	//Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }
 
        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }
 
        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);
 
        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }
 
        //Return rgb(a) color string
        return $output;
}


?>
