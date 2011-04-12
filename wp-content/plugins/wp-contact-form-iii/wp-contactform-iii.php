<?php
/*
Plugin Name: WP Contact Form III
Plugin URI: http://wangenweb.com/wordpress/plugins/contact-form-iii/
Description: WP Contact Form III is a simple drop in form for users to contact you. It can easily be added to a page using the code [contactform]. 
Go to <a href="options-general.php?page=wp-contact-form-iii/options-contactform.php">Settings -&gt; ContactForm III</a> to configure. Challenge (antispam) mod by <a href="http://www.douglaskarr.com">Doug Karr</a>. Original plugin by <a href="http://ryanduff.net">Ryan Duff</a> 
Author: Kristin K. Wangen
Author URI: http://wangenweb.com/
Version: 1.6.2d
*/

/*  Copyleft 2007  Kristin K. Wangen 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



load_plugin_textdomain('cfiii', PLUGINDIR . '/wp-contact-form-iii/languages'); 




/* Declare strings that change depending on input. This also resets them so errors clear on resubmission. */

$wpcf_strings = array('error' => '',
	'name' => '<input type="text" class="text" name="wpcf_your_name" id="wpcf_your_name" size="30" maxlength="50" value="' . $_POST['wpcf_your_name'] . '" tabindex="1" />',

	'email' => '<input class="text" type="text" name="wpcf_email" id="wpcf_email" size="30" maxlength="50" value="' . $_POST['wpcf_email'] . '" tabindex="2"/>',

	'subject' => '<input class="text" type="text" name="wpcf_subject" id="wpcf_subject" size="30" maxlength="50" value="' .$_POST['wpcf_subject'] . '" tabindex="4"/>',

	'msg' => '<textarea class="textarea" name="wpcf_msg" id="wpcf_msg" cols="35" rows="8" tabindex="5">' . $_POST['wpcf_msg'] . '</textarea>',

	'response' => '<input class="text" type="text" name="wpcf_response" id="wpcf_response" size="30" maxlength="50" value="' . $_POST['wpcf_response'] . '" tabindex="6"/>',
);

function wpcf_is_malicious($input) {
	$is_malicious = false;
	$bad_inputs = array("\r", "\n", "mime-version", "content-type", "bcc:", "cc:", "to:","*","document.cookie","onclick","onload");
	foreach($bad_inputs as $bad_input) {
		if(strpos(strtolower($input), strtolower($bad_input)) !== false) {
			$is_malicious = true; break;
		}
	}
	return $is_malicious;
}

function wpcf_is_challenge($input) {
	$is_challenge = false;
	$answer = get_option('wpcf_answer');
	$answer = htmlentities(stripslashes(attribute_escape($answer)));
	if($input == $answer) {
		$is_challenge = true;
	}
	return $is_challenge;
}




/* This function checks for errors on input and changes $wpcf_strings if there are any errors. Shortcircuits if there has not been a submission */

function wpcf_check_input()
{
	if(!(isset($_POST['wpcf_stage']))) {return false;} // Shortcircuit.
	$_POST['wpcf_your_name'] = htmlspecialchars(stripslashes(attribute_escape($_POST['wpcf_your_name'])));
	$_POST['wpcf_email'] = htmlentities(stripslashes(attribute_escape($_POST['wpcf_email'])));
	$_POST['wpcf_response'] = htmlentities(stripslashes(attribute_escape($_POST['wpcf_response'])));
	$_POST['wpcf_website'] = htmlentities(stripslashes(attribute_escape($_POST['wpcf_website'])));
    	$_POST['wpcf_subject'] = wp_specialchars(stripslashes(attribute_escape($_POST['wpcf_subject'])));
	$_POST['wpcf_msg'] = wp_specialchars(stripslashes(attribute_escape($_POST['wpcf_msg'])));

	global $wpcf_strings;
	$ok = true;

	if(empty($_POST['wpcf_your_name']))
	{
		$ok = false; $reason = 'empty';
		$wpcf_strings['name'] = '<input type="text" name="wpcf_your_name" id="wpcf_your_name" size="30" maxlength="50" value="* ' . $_POST['wpcf_your_name'] . '" class="contacterror" tabindex="1" />' ;
	}

    if(!is_email($_POST['wpcf_email']))
    {
	    $ok = false; $reason = 'empty';
	    $wpcf_strings['email'] = '<input type="text" name="wpcf_email" id="wpcf_email" size="30" maxlength="50" value="* ' . $_POST['wpcf_email'] . '" class="contacterror" tabindex="2"/>';
	}

    if(empty($_POST['wpcf_subject']))
    {
        $ok = false; $reason = 'empty';
        $wpcf_strings['subject'] = '<input type="text" name="wpcf_subject" id="wpcf_subject" size="30" maxlength="50" value="* ' . $_POST['wpcf_subject'] . '" class="contacterror" tabindex="4"/>';
    }

   if(empty($_POST['wpcf_msg']))
    {
	    $ok = false; $reason = 'empty';
	    $wpcf_strings['msg'] = '<textarea name="wpcf_msg" id="wpcf_message" cols="35" rows="8" class="contacterror" tabindex="5">* ' . $_POST['wpcf_msg'] . '</textarea>';

	}

    if(empty($_POST['wpcf_response']))
    {
	    $ok = false; $reason = 'empty';
	    $wpcf_strings['response'] = '<input type="text" name="wpcf_response" id="wpcf_response" size="30" maxlength="50" value="* ' . $_POST['wpcf_response'] . '" class="contacterror" tabindex="6" />';
	}  

	if (!wpcf_is_challenge($_POST['wpcf_response'])) {
	    $ok = false; $reason = 'wrong';
	    $wpcf_strings['response'] = '<input type="text" name="wpcf_response" id="wpcf_response" size="30" maxlength="50" value="* ' . $_POST['wpcf_response'] . '" class="contacterror" tabindex="6"/>';
	}


	if(wpcf_is_malicious($_POST['wpcf_your_name']) || wpcf_is_malicious($_POST['wpcf_email']) ||  wpcf_is_malicious($_POST['wpcf_subject'])) {
		$ok = false; $reason = 'malicious';
	}

	if($ok == true)
	{
		return true;
	}
	else {
		if($reason == 'malicious') {
			$wpcf_strings['error'] = "<p><em>".__('You can not use any of the following in the Name or Email fields: a linebreak, or the phrases \'mime-version\', \'content-type\', \'cc:\' \'bcc:\'or \'to:\'.','cfiii')."</em></p>";
		} elseif($reason == 'empty') {
			$wpcf_strings['error'] = '' . wp_specialchars(stripslashes(get_option('wpcf_error_msg'))) . '';
		} elseif($reason == 'wrong') {
			$wpcf_strings['error'] = "<p><em>".__('You answered the challenge question incorrectly.', 'cfiii')."</em></p>";
		}
		return false;
	}
}




/*Wrapper function which calls the form.*/
function wpcf_callback( $content )
{
	global $wpcf_strings;
	global $charset;
	/* Run the input check. */




    if(wpcf_check_input()) // If the input check returns true (ie. there has been a submission & input is ok)
    {

		$recipient = get_option('wpcf_email');
		$subject     = get_option('wpcf_prefix') . attribute_escape($_POST['wpcf_subject']);
		$success_msg = get_option('wpcf_success_msg');
		$success_msg = stripslashes($success_msg);

		$name = attribute_escape($_POST['wpcf_your_name']);
		$email = attribute_escape($_POST['wpcf_email']);
		$website = attribute_escape($_POST['wpcf_website']);
		$msg = attribute_escape($_POST['wpcf_msg']);

      		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $name <$email>\n";
		$headers .= "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";

		$fullmsg = "$name ".__('wrote:','cfiii')."\n";
		$fullmsg .= wordwrap($msg, 80, "\n") . "\n\n";
		$fullmsg .= "".__('Website:','cfiii')." " . $website . "\n";
		$fullmsg .= "".__('IP:','cfiii')."" . getip();

            	mail($recipient, $subject, $fullmsg, $headers);

		$results = '<p class="successmsg">' . $success_msg . '</p>';
		$homelink = '<p class="successmsg"><a href="'. get_bloginfo('url') .'/">'. __('Return home','cfiii') .'</a></p>';
		echo $results . $homelink;
    }
    else // Else show the form. If there are errors the strings will have updated during running the inputcheck.
    {
	$question = htmlentities(stripslashes(get_option('wpcf_question')));
 	$form = '

<form id="contactform" action="'. get_permalink() .'" method="post">
'.  $wpcf_strings['error'] .'

<p>* '. __('必須 Required fields','cfiii') .'</p>
	<fieldset>
	<label for="wpcf_your_name">'.  __('お名前 Name: ', 'cfiii') .'*</label>
		'.  $wpcf_strings['name'] .'

	<label for="wpcf_email">'.  __('Email:', 'cfiii').'*</label>
		'.  $wpcf_strings['email'] .'
<!--
	<label for="wpcf_website">'.  __('Website:', 'cfiii') .'</label>
	<input type="text" name="wpcf_website" id="wpcf_website" size="30" maxlength="50" value="'. $_POST['wpcf_website']  .'" tabindex="3" /> 
-->
	<label for="wpcf_subject">'.  __('題名 Subject:', 'cfiii')  .'*</label>
	'. $wpcf_strings['subject']  .' 

	<label for="wpcf_msg">'. __('Message: ', 'cfiii') .'*</label>
	'. $wpcf_strings['msg']  .'

	<label for="wpcf_response">'. __($question,'cfiii') .'*</label>
	'. $wpcf_strings['response'] .'

	<input type="submit" name="Submit" value="'.  __('Send', 'cfiii') .'" id="contactsubmit" tabindex="7" />
	<input type="hidden" class="hiddenfield" name="wpcf_stage" value="process" />
	</fieldset>
</form>

';


	return $form;

   }
}



/*Can't use WP's function here, so lets use our own*/
function getip()
{
	if (isset($_SERVER))
	{
 		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
 		{
  			$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
 		}
 		elseif (isset($_SERVER["HTTP_CLIENT_IP"]))
 		{
  			$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
 		}
 		else
 		{
 			$ip_addr = $_SERVER["REMOTE_ADDR"];
 		}
	}
	else
	{
 		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
 		{
  			$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
 		}
 		elseif ( getenv( 'HTTP_CLIENT_IP' ) )
 		{
  			$ip_addr = getenv( 'HTTP_CLIENT_IP' );
 		}
 		else
 		{
  			$ip_addr = getenv( 'REMOTE_ADDR' );
 		}
	}
return $ip_addr;
}




/*CSS Styling*/
function wpcf_css() { 
	$cssurl = WP_PLUGIN_URL;

	if(file_exists( WP_PLUGIN_DIR . '/wp-contact-form-iii/mystyle.css')) { 

?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $cssurl; ?>/wp-contact-form-iii/mystyle.css" />

<?php
	} else /*load default styling*/ { ?>
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $cssurl; ?>/wp-contact-form-iii/wp-contactform-iii.css" />

<?php }

} 

function wpcf_add_options_page()
	{
	add_options_page(__('Contact Form Settings', 'cfiii'), __('Contact Form', 'cfiii'), 10, 'wp-contact-form-iii/wp-contactform-iii-options.php');
	}



/* Action calls for all functions */
add_action('wp_head', 'wpcf_css');
add_action('admin_head', 'wpcf_add_options_page');
add_shortcode('contactform', 'wpcf_callback');
?>
