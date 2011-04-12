<?php
/* Options Contact Form III */


load_plugin_textdomain('cfiii', PLUGINDIR . '/wp-contact-form-iii/languages'); 


$location = 'options-general.php?page=wp-contact-form-iii/wp-contactform-iii-options.php'; 

/*Lets add some default options if they don't exist*/
add_option('wpcf_email', get_settings('admin_email'));
add_option('wpcf_prefix', '['.get_bloginfo('name').'] ');
add_option('wpcf_success_msg', __('Thanks for your comments!', 'cfiii'));
add_option('wpcf_error_msg', __('Please fill in the required fields.', 'cfiii'));
add_option('wpcf_question', __('2 + 2 =', 'cfiii'));
add_option('wpcf_answer', __('4', 'cfiii'));







/*Get options for form fields*/
$wpcf_email = stripslashes(get_option('wpcf_email'));
$wpcf_prefix = stripslashes(get_option('wpcf_prefix'));
$wpcf_success_msg = stripslashes(get_option('wpcf_success_msg'));
$wpcf_error_msg = stripslashes(get_option('wpcf_error_msg'));
$wpcf_question = stripslashes(get_option('wpcf_question'));
$wpcf_answer = stripslashes(get_option('wpcf_answer'));

?>

<div class="wrap">
  <h2><?php _e('Contact Form Options', 'cfiii') ?></h2>
	<form method="post" action="options.php">


	<?php wp_nonce_field('update-options'); ?>


		<h3><?php _e('General', 'cfiii') ?></h3>

	    <table width="100%" cellspacing="2" cellpadding="5" class="form-table">
	      <tr valign="top">
		<th scope="row"><label for="wpcf_email"><?php _e('E-mail Address:','cfiii') ?></label></th>
		<td><input name="wpcf_email" type="text" id="wpcf_email" value="<?php echo $wpcf_email; ?>" size="40" tabindex="1" />
		<br />
		<?php _e('This address is where the email will be sent to.', 'cfiii') ?></td>
	      </tr>

	      <tr valign="top">
		<th scope="row"><label for="wpcf_prefix"><?php _e('Subject prefix:','cfiii') ?></label></th>
		<td><input name="wpcf_prefix" type="text" id="wpcf_prefix" value="<?php echo $wpcf_prefix; ?>" size="40" tabindex="2" />
		<br />
		<?php _e('This the prefix for the subject of your email. Leave empty for no prefix', 'cfiii') ?></td>
	      </tr>

	     </table>


		<h3><?php _e('Challenge Question', 'cfiii') ?></h3>
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		  <tr valign="top">
			<th scope="row"><label for="wpcf_question"><?php _e('What is your challenge question?', 'cfiii') ?></label></th>
			<td><input name="wpcf_question" id="wpcf_question" type="text" value="<?php echo $wpcf_question; ?>" size="40" tabindex="3" />
			<br />
	<?php _e('This is a question asked to the contact form user to see if they are human.', 'cfiii') ?></td>
		  </tr>
		  <tr valign="top">
			<th scope="row"><label for="wpcf_answer"><?php _e('Correct response:', 'cfiii') ?></label></th>
			<td><input name="wpcf_answer" id="wpcf_answer" type="text" value="<?php echo $wpcf_answer; ?>" size="40" tabindex="4" />
			<br />
	<?php _e('This is the exact response to the challenge question.', 'cfiii') ?> <br />
		  </tr>
		</table>



		<h3><?php _e('Messages', 'cfiii') ?></h3>
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		  <tr valign="top">
			<th scope="row"><label for="wpcf_success_msg"><?php _e('Success Message:', 'cfiii') ?></label></th>
			<td><textarea name="wpcf_success_msg" id="wpcf_success_msg" style="width: 80%;" rows="4" cols="50" tabindex="5"><?php echo $wpcf_success_msg; ?></textarea>
			<br />
	<?php _e('When the form is sucessfully submitted, this is the message the user will see.', 'cfiii') ?></td>
		  </tr>
		  <tr valign="top">
			<th scope="row"><label for="wpcf_error_msg"><?php _e('Error Message:', 'cfiii') ?></label></th>
			<td><textarea name="wpcf_error_msg" id="wpcf_error_msg" style="width: 80%;" rows="4" cols="50" tabindex="6"><?php echo $wpcf_error_msg; ?></textarea>
			<br />
	<?php _e('If the user skips a required field, this is the message he will see.', 'cfiii') ?> <br />
	<?php _e('You can apply CSS to this text by wrapping it in <code>&lt;p style="[your CSS here]"&gt; &lt;/p&gt;</code>.', 'cfiii') ?><br />
	<?php _e('ie. <code>&lt;p style="color:red;"&gt;Please fill in the required fields.&lt;/p&gt;</code>.', 'cfiii') ?></td>
		  </tr>
		</table>




		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="wpcf_email,wpcf_prefix,wpcf_success_msg,wpcf_error_msg,wpcf_question,wpcf_answer" />

		<p class="submit">
		<input type="submit" tabindex="8" name="Submit" value="<?php _e('Update Options','cfiii') ?>" />
		</p>


 </form>




</div>
