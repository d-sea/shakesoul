<?php
//
// VALIDATE all fields
//

$CFfunctionsC = dirname(dirname(__FILE__)).$cformsSettings['global']['cforms_IIS'].'cforms-custom'.$cformsSettings['global']['cforms_IIS'].'my-functions.php';
$CFfunctions = dirname(__FILE__).$cformsSettings['global']['cforms_IIS'].'my-functions.php';
if ( file_exists($CFfunctionsC) )
    include_once($CFfunctionsC);
else if ( file_exists($CFfunctions) )
    include_once($CFfunctions);


require_once (dirname(__FILE__) . '/lib_validate.php');

if( isset($_POST['sendbutton'.$no]) && $all_valid ) {

//
// all valid? get ready to send
//

		if( function_exists('my_cforms_filter') )
			$_POST = my_cforms_filter($_POST);

		if ( ($cformsSettings['form'.$no]['cforms'.$no.'_maxentries']<>'' && get_cforms_submission_left($no)==0) || !cf_check_time($no) ){
			$cflimit = 'reached';
			return;
		}

		$usermessage_text = preg_replace ( '|\r\n|', '<br />', stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_success']) );

		$formdata = '';
		$htmlformdata = '';

		$track = array();
		$trackinstance = array();

  		$to_one = "-1";
		$ccme = false;
		$field_email = '';

		$filefield=0;
		$taf_youremail = false;
		$taf_friendsemail = false;
		$send2author = false;

		$key = 0;

		$customspace = (int)($cformsSettings['form'.$no]['cforms'.$no.'_space']>0) ? $cformsSettings['form'.$no]['cforms'.$no.'_space'] : 30;

		for($i = 1; $i <= $field_count; $i++) {

			if ( !$custom )
				$field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i ]);
			else
				$field_stat = explode('$#$', $customfields[$i-1]);

			// filter non input fields
			while ( in_array($field_stat[1],array('fieldsetstart','fieldsetend','textonly','captcha','verification')) ) {

				if ( $field_stat[1] == 'captcha' && !(is_user_logged_in() || $captchaopt['fo']<>'1') )
					break;
				if ( $field_stat[1] == 'verification' && !(is_user_logged_in() && $captchaopt['foqa']<>'1') )
					break;

					if ( in_array($field_stat[1],array('fieldsetstart','fieldsetend')) ){ // include and make only fieldsets pretty!

						//just for email looks
						$space='-';
						$n = ((($customspace*2)+2) - strlen($field_stat[0])) / 2;
						$n = ($n<0)?0:$n;
						if ( strlen($field_stat[0]) < (($customspace*2)-2) )
							$space = str_repeat("-", $n );

						$formdata .= substr("\n$space".stripslashes($field_stat[0])."$space",0,($customspace*2)) . "\n\n";
						$htmlformdata .= '<tr><td style=3D"'.$cformsSettings['global']['cforms_style_fs_td'].'" colspan=3D"2">' . $field_stat[0] . '</td></tr>' . "\n";

						if ( $field_stat[1] == 'fieldsetstart' ){
							$track['$$$'.$i] = 'Fieldset'.$fieldsetnr;
							$track['Fieldset'.$fieldsetnr++] = $field_stat[0];
						}
					}

					//get next in line...
					$i++;

					if ( !$custom )
  						$field_stat = explode('$#$', $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i ]);
					else
       					$field_stat = explode('$#$', $customfields[$i-1]);

					if( $field_stat[1] == '')
							break 2; // all fields searched, break both while & for
			}

			$field_name = $field_stat[0];
  			$field_type = $field_stat[1];

			$custom_names = ($cformsSettings['form'.$no]['cforms'.$no.'_customnames']=='1')?true:false;

    		if ( $custom_names ){

				preg_match('/^([^#\|]*).*/',$field_name,$input_name);

				if ( strpos($input_name[1],'[id:')!==false ){
					$idPartA = strpos($input_name[1],'[id:');
					$idPartB = strpos($input_name[1],']',$idPartA);
					$customTrackingID = substr($input_name[1],$idPartA+4,($idPartB-$idPartA)-4);
					$current_field = cf_sanitize_ids( $customTrackingID );

					$field_name = substr_replace($input_name[1],'',$idPartA,($idPartB-$idPartA)+1);
				} else{
					$current_field = cf_sanitize_ids($input_name[1]);
					$customTrackingID='';
				}

			}
			else
				$current_field = 'cf'.$no.'_field_' . $i;

			// check if fields needs to be cleared
		    $obj = explode('|', $field_name,3);
			$defaultval = stripslashes($obj[1]);
			if ( $_POST[$current_field] == $defaultval && $field_stat[4]=='1')
				$_POST[$current_field] = '';

			// strip out default value
			$field_name = $obj[0];

			// special Tell-A-Friend fields
			if ( !$taf_friendsemail && $field_type=='friendsemail' && $field_stat[3]=='1')
					$field_email = $taf_friendsemail = $_POST[$current_field];

			if ( !$taf_youremail && $field_type=='youremail' && $field_stat[3]=='1')
					$taf_youremail = $_POST[$current_field];

			if ( $field_type=='friendsname' )
					$taf_friendsname = $_POST[$current_field];

			if ( $field_type=='yourname' )
					$taf_yourname = $_POST[$current_field];


			// special email field in WP Commente
			if ( $field_type=='email' )
					$field_email = (isset($_POST['email']))?$_POST['email']:$user->user_email;


			// special radio button WP Comments
			if( $field_type=='send2author' && $_POST['send2author']=='1') {
				$send2author=true;
				continue; // don't record it.
			}

			// find email address
			if ( $field_email == '' && $field_stat[3]=='1')
					$field_email = $_POST[$current_field];


			// special case: select box & radio box
			if ( $field_type == "checkboxgroup" || $field_type == "multiselectbox" || $field_type == "selectbox" || $field_type == "radiobuttons" ) { //only needed for field name
			  $field_name = explode('#',$field_name);
			  $field_name = $field_name[0];
			}


			// special case: check box
			if ( $field_type == "checkbox" || $field_type == "ccbox" ) {
			  $field_name = explode('#',$field_name);
			  $field_name = ($field_name[1]=='')?$field_name[0]:$field_name[1];
				// if ccbox
			  if ($field_type == "ccbox" && isset($_POST[$current_field]) )
			      $ccme = $field_name;
			}


		if ( $field_type == "emailtobox" ){  				//special case where the value needs to bet get from the DB!

            $field_name = explode('#',$field_stat[0]);  //can't use field_name, since '|' check earlier
            $to_one = $_POST[$current_field];

			$tmp = explode('|set:', $field_name[1] );	// remove possible |set:true
            $offset = (strpos($tmp[0],'|')===false) ? 1 : 2; // names come usually right after the label

            $value 	= $field_name[(int)$to_one+$offset];  // values start from 0 or after!
            $field_name = $field_name[0];
 		}
 		else if ( $field_type == "upload" ){

 			//$fsize = $file['size'][$filefield]/1000;
 			$value = str_replace(' ','_',$file['name'][$filefield++]);

 		}
 		else if ( $field_type == "multiselectbox" || $field_type == "checkboxgroup"){

            $all_options = $_POST[$current_field];
 		    if ( count($all_options) > 0)
                $value = stripslashes(implode(',', $all_options));
            else
                $value = '';

        }
		else if ( $field_stat[1] == 'captcha' ) // captcha response

			$value = $_POST['cforms_captcha'.$no];

		else if ( $field_stat[1] == 'verification' ) { // verification Q&A response

			$value = $_POST['cforms_q'.$no]; // add Q&A label!
			$field_name = __('Q&A','cforms');

		}
		else if( $field_type == 'cauthor' )  // WP Comments special fields
			$value = ($user->display_name<>'')?$user->display_name:$_POST[$field_type];

		else if( $field_type == 'url')
			$value = ($user->user_url<>'')?$user->user_url:$_POST[$field_type];

		else if( $field_type == 'email' )
			$value = ($user->user_email<>'')?$user->user_email:$_POST[$field_type];

		else if( $field_type == 'comment' )
			$value = $_POST[$field_type];

		else if( $field_type == 'hidden' )
			$value = rawurldecode($_POST[$current_field]);

		else
			$value = $_POST[$current_field];       // covers all other fields' values

		//check boxes
		if ( $field_type == "checkbox" || $field_type == "ccbox" ) {

				if ( isset($_POST[$current_field]) )
					$value = ($_POST[$current_field]<>'')?$_POST[$current_field]:'X';
				else
					$value = '-';

		} else if ( $field_type == "radiobuttons" ) {

				if ( ! isset($_POST[$current_field]) )
					$value = '-';

		}

		//for db tracking
		$inc='';
		$trackname = trim( ($field_type == "upload")?$field_name.'[*]':$field_name );
		if ( array_key_exists($trackname, $track) ){
			if ( $trackinstance[$trackname]==''  )
				$trackinstance[$trackname]=2;
			$inc = '___'.($trackinstance[$trackname]++);
		}

		$track['$$$'.$i] = $trackname.$inc;
		$track[$trackname.$inc] = $value;
		if( $customTrackingID<>'' )
			$track['$$$'.$customTrackingID] = $trackname.$inc;

		//for all equal except textareas!
		$htmlvalue = str_replace("=","=3D",$value);
		$htmlfield_name = $field_name;

		// just for looks: break for textarea
		if ( $field_type == "textarea" || $field_type=="comment" ) {
				$field_name = "\n" . $field_name;
				$htmlvalue = str_replace(array("=","\n"),array("=3D","<br />\n"),$value);
				$value = "\n" . $value . "\n";
		}


		// for looks
		$space='';
		if ( strlen(stripslashes($field_name)) < $customspace )
			  $space = str_repeat(" ", $customspace - strlen(stripslashes($field_name)));

		$field_name .= ': ' . $space;


		if ( $field_stat[1] <> 'verification' && $field_stat[1] <> 'captcha' ){
				$formdata .= stripslashes( $field_name ) . $value . "\n";
				$htmlformdata .= '<tr><td style=3D"'.$cformsSettings['global']['cforms_style_data_td'].'">' . $htmlfield_name . '</td><td>' . $htmlvalue . '</td></tr>' . "\n";
		}

	} //for all fields

	// assemble html formdata
	$htmlformdata = '<div style=3D"'.$cformsSettings['global']['cforms_style_datablock'].'"><table width=3D"100%" cellpadding=3D"2">' . stripslashes( $htmlformdata ) . '</table></div><span style=3D"'.$cformsSettings['global']['cforms_style_cforms'].'">powered by <a href=3D"http://www.deliciousdays.com/cforms-plugin">cformsII</a></span>';

	//
	// FIRST into the database is required!
	//

	$subID = ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2' && !$send2author )?'noid':write_tracking_record($no,$field_email);

	//
	// allow the user to use form data for other apps
	//
	$trackf['id'] = $no;
	$trackf['data'] = $track;
	if( function_exists('my_cforms_action') )
		my_cforms_action($trackf);


	//
	//set reply-to & watch out for T-A-F
	//
	$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_email']) );

	// multiple recipients? and to whom is the email sent?
	if ( substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='2' && $track['send2author']=='1'){
			$to = $wpdb->get_results("SELECT U.user_email FROM $wpdb->users as U, $wpdb->posts as P WHERE P.ID = {$_POST['comment_post_ID']} AND U.ID=P.post_author");
			$to = $replyto =  ($to[0]->user_email<>'')?$to[0]->user_email:$replyto;
	}
	else if ( $to_one <> "-1" ) {
			$all_to_email = explode(',', $replyto);
			$replyto = $to = $all_to_email[ $to_one ];
	} else
			$to = $replyto;

	//T-A-F? then overwrite
	if ( $taf_youremail && $taf_friendsemail && substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='1' )
		$replyto = "\"{$taf_yourname}\" <{$taf_youremail}>";



	//
	// ready to send email
	// email header
	//
	$eol = "\n";

	$frommail = check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track,$no);
	if ( $frommail=='' )
		$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';

	$headers = "From: ". $frommail . $eol;
	$headers.= "Reply-To: " . $field_email . $eol;

	if ( ($tempBcc=stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_bcc'])) != "")
	    $headers.= "Bcc: " . $tempBcc . $eol;

	$headers.= "MIME-Version: 1.0"  .$eol;
	$headers.= "Content-Type: multipart/mixed; boundary=\"----MIME_BOUNDRY_main_message\"";

	// prep message text, replace variables
	$message	= $cformsSettings['form'.$no]['cforms'.$no.'_header'];
	$message	= check_default_vars($message,$no);
	$message	= check_cust_vars($message,$track,$no);

	// text & html message
	$fmessage = "This is a multi-part message in MIME format."  . $eol;
	$fmessage .= "------MIME_BOUNDRY_main_message"  . $eol;


	// HTML message part?
	$html_show = ( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1' )?true:false;
	$htmlmessage = '';
    
	if ($html_show) {
		$fmessage .= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_sub_message\"" . $eol . $eol;
		$fmessage .= "------MIME_BOUNDRY_sub_message"  . $eol;
		$fmessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
	}
	else
		$fmessage .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"" . $eol . $eol;

	$fmessage .= $message . $eol;

	// need to add form data summary or is all in the header anyway?
	if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1')
		$fmessage .= $eol . $formdata . $eol;


	// HTML text
	if ( $html_show ) {

		// actual user message
		$htmlmessage = $cformsSettings['form'.$no]['cforms'.$no.'_header_html'];
		$htmlmessage = check_default_vars($htmlmessage,$no);
		$htmlmessage = str_replace('=','=3D', stripslashes( check_cust_vars($htmlmessage,$track,$no) ) );


		$fmessage .= "------MIME_BOUNDRY_sub_message"  . $eol;
		$fmessage .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
		$fmessage .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;

		$fmessage .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;

		$fmessage .= "<HTML>" . $eol;
		$fmessage .= "<BODY>" . $eol;

		$fmessage .= $htmlmessage;

		// need to add form data summary or is all in the header anyway?
		if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],1,1)=='1')
			$fmessage .= $eol . $htmlformdata;

		$fmessage .= "</BODY></HTML>"  . $eol . $eol;
	}

	// end of sub message

	$attached='';
	// possibly add attachment
	if ( isset($_FILES['cf_uploadfile'.$no]) && !$cformsSettings['form'.$no]['cforms'.$no.'_noattachments'] ) {

			// different header for attached files
	 		//
	 		$all_mime = array("txt"=>"text/plain", "htm"=>"text/html", "html"=>"text/html", "gif"=>"image/gif", "png"=>"image/x-png",
	 						 "jpeg"=>"image/jpeg", "jpg"=>"image/jpeg", "tif"=>"image/tiff", "bmp"=>"image/x-ms-bmp", "wav"=>"audio/x-wav",
	 						 "mpeg"=>"video/mpeg", "mpg"=>"video/mpeg", "mov"=>"video/quicktime", "avi"=>"video/x-msvideo",
	 						 "rtf"=>"application/rtf", "pdf"=>"application/pdf", "zip"=>"application/zip", "hqx"=>"application/mac-binhex40",
	 						 "sit"=>"application/x-stuffit", "exe"=>"application/octet-stream", "ppz"=>"application/mspowerpoint",
							 "ppt"=>"application/vnd.ms-powerpoint", "ppj"=>"application/vnd.ms-project", "xls"=>"application/vnd.ms-excel",
							 "doc"=>"application/msword");

			if ( $html_show )
				$fmessage .= "------MIME_BOUNDRY_sub_message--"  . $eol;

			for ( $filefield=0; $filefield < count($_FILES['cf_uploadfile'.$no][name]); $filefield++) {

				if ( $filedata[$filefield] <> '' ){
					$mime = (!$all_mime[$fileext[$filefield]])?'application/octet-stream':$all_mime[$fileext[$filefield]];

					$attached .= "------MIME_BOUNDRY_main_message" . $eol;
					$attached .= "Content-Type: $mime;\n\tname=\"" . $file['name'][$filefield] . "\"" . $eol;
					$attached .= "Content-Transfer-Encoding: base64" . $eol;
					$attached .= "Content-Disposition: inline;\n\tfilename=\"" . $file['name'][$filefield] . "\"\n" . $eol;
					$attached .= $eol . $filedata[$filefield]; 	//The base64 encoded message
				}

			}

	}


	//
	// finally send mails
	//

	//either use configured subject or user determined
	//now replace the left over {xyz} variables with the input data
	$vsubject = $cformsSettings['form'.$no]['cforms'.$no.'_subject'];
	$vsubject = check_default_vars($vsubject,$no);
	$vsubject = stripslashes( check_cust_vars($vsubject,$track,$no) );

	// SMTP server or native PHP mail() ?
	if ( $smtpsettings[0]=='1' )
		$sentadmin = cforms_phpmailer( $no, $frommail, $field_email, $to, $vsubject, $message, $formdata, $htmlmessage, $htmlformdata, $fileext );
	else
		$sentadmin = @mail($to, encode_header($vsubject), $fmessage.$attached, $headers);

	if( $sentadmin==1 ) {
			  // send copy or notification?
		    if ( ($cformsSettings['form'.$no]['cforms'.$no.'_confirm']=='1' && $field_email<>'') || ($ccme&&$trackf[$ccme]<>'-') )  // not if no email & already CC'ed
		    {

						$frommail = check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track,$no);
						if ( $frommail=='' )
							$frommail = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';

						// HTML message part?
						$html_show_ac = ( substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],3,1)=='1' )?true:false;
						$automsg = '';

						$headers2 = "From: ". $frommail . $eol;
						$headers2.= "Reply-To: " . $replyto . $eol;

						if ( $taf_youremail && $taf_friendsemail && substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='1' ) //TAF: add CC
							$headers2.= "CC: " . $replyto . $eol;

						$headers2.= "MIME-Version: 1.0"  .$eol;

						if( $html_show_ac || ($html_show && ($ccme&&$trackf[$ccme]<>'-')) ){
							$headers2.= "Content-Type: multipart/alternative; boundary=\"----MIME_BOUNDRY_main_message\"";
							$automsg .= "This is a multi-part message in MIME format."  . $eol;
							$automsg .= "------MIME_BOUNDRY_main_message"  . $eol;
							$automsg .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"; format=flowed" . $eol;
							$automsg .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;
						}
						else
							$headers2.= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"; format=flowed";


						// actual user message
						$cmsg = $cformsSettings['form'.$no]['cforms'.$no.'_cmsg'];
						$cmsg = check_default_vars($cmsg,$no);
						$cmsg = check_cust_vars($cmsg,$track,$no);


						// text text
						$automsg .= $cmsg . $eol;

						// HTML text
						if ( $html_show_ac ) {

							// actual user message
							$cmsghtml = $cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'];
							$cmsghtml = check_default_vars($cmsghtml,$no);
							$cmsghtml = str_replace(array("=","\n"),array("=3D","<br />\n"), check_cust_vars($cmsghtml,$track,$no) );

							$automsg .= "------MIME_BOUNDRY_main_message"  . $eol;
							$automsg .= "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"". $eol;
							$automsg .= "Content-Transfer-Encoding: quoted-printable"  . $eol . $eol;;

							$automsg .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">"  . $eol;
							$automsg .= "<HTML><BODY>"  . $eol;
							$automsg .= $cmsghtml;
							$automsg .= "</BODY></HTML>"  . $eol . $eol;
						}

					 	$subject2 = $cformsSettings['form'.$no]['cforms'.$no.'_csubject'];
						$subject2 = check_default_vars($subject2,$no);
						$subject2 = check_cust_vars($subject2,$track,$no);

						// different cc & ac subjects?
						$t=explode('$#$',$subject2);
						$t[1] = ($t[1]<>'') ? $t[1] : $t[0];

						// email tracking via 3rd party?
						$field_email = ($cformsSettings['form'.$no]['cforms'.$no.'_tracking']<>'')?$field_email.$cformsSettings['form'.$no]['cforms'.$no.'_tracking']:$field_email;

						// if in Tell-A-Friend Mode, then overwrite header stuff...
						if ( $taf_youremail && $taf_friendsemail && substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1)=='1' )
							$field_email = "\"{$taf_friendsname}\" <{$taf_friendsemail}>";

						if ( $ccme&&$trackf[$ccme]<>'-' ) {
							if ( $smtpsettings[0]=='1' )
								$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[1]), $message, $formdata, $htmlmessage, $htmlformdata, 'ac' );
							else
								$sent = @mail($field_email, encode_header(stripslashes($t[1])), $fmessage, $headers2); //the admin one
						}
						else {
							if ( $smtpsettings[0]=='1' )
								$sent = cforms_phpmailer( $no, $frommail, $replyto, $field_email, stripslashes($t[0]) , $cmsg , '', $cmsghtml, '', 'ac' );
							else
								$sent = @mail($field_email, encode_header(stripslashes($t[0])), stripslashes($automsg), $headers2); //takes the above
						}

		  		if( $sent<>'1' )
			  			$usermessage_text = __('Error occurred while sending the auto confirmation message: ','cforms')." ($sent)";
		    }

		// redirect to a different page on suceess?
		if ( $cformsSettings['form'.$no]['cforms'.$no.'_redirect']==1 ) {
			if ( function_exists('my_cforms_logic') )
            	$rp = my_cforms_logic($trackf, $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'],'redirection');  ### use trackf!
            else
            	$rp = $cformsSettings['form'.$no]['cforms'.$no.'_redirect_page'];

			?>
			<script type="text/javascript">
				location.href = '<?php echo $rp; ?>';
			</script>
			<?php
		}

  	} // if first email already failed
	else
		$usermessage_text = __('Error occurred while sending the message: ','cforms') . '<br />'. $smtpsettings[0]?'<br />'.$sentadmin:'';


	//
	// Files uploaded??
	//
	$filefield=0;
    $temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
    $fileuploaddir = $temp[0];

	if ( isset($_FILES['cf_uploadfile'.$no]) ) {
		foreach( $_FILES['cf_uploadfile'.$no][tmp_name] as $tmpfile ) {
            //copy attachment to local server dir
            if ( is_uploaded_file($tmpfile) ){
            	move_uploaded_file($tmpfile,$fileuploaddir.'/'.$subID.'-'.str_replace(' ','_',$file['name'][$filefield]) );
            }
        	$filefield++;
		}
	}

} //if isset & valid sendbutton

?>