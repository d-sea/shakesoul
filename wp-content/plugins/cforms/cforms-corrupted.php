<?php
/*
please see cforms.php for more information
*/
load_plugin_textdomain('cforms');

### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die(__('Access Denied','cforms'));
}
?>

<?php if( $_POST['fixsettings'] ) :?>

<div class="error"><p><?php _e('Please deactivate and then re-activate the cforms plugin now.','cforms'); ?></p></div>
<?php
	$c = stripslashes($_POST['currentsettings']);

    $nc='';
    for($i=0; $i<strlen($c); $i++ ){

		if ( substr($c,$i,2) == 's:' ){
			$q1=strpos($c,'"',$i);
			$q2=strpos($c,'";',$q1)-1;
        	$nc .= 's:'.($q2-$q1).':'.substr($c,$q1,($q2-$q1)+3);
            $i = $i + ($q2-$q1) +6 + (strlen(strval($q2-$q1))) -1;
        }
		else
        	$nc .= substr($c,$i,1);
    }

    update_option('cforms_settings',$nc);
    
	die();
?>

<?php elseif( $_POST['resetsettings'] ) : ?>

<div class="error"><p><?php _e('Please deactivate and then re-activate the cforms plugin now.','cforms'); ?></p></div>
<?php
	//$alloptions =  $wpdb->query("DELETE FROM `$wpdb->options` WHERE option_name='cforms_settings'");
    delete_option('cforms_settings');
	global $cformsSettings;
    $cformsSettings = array();
	require_once(dirname(__FILE__) . '/lib_activate.php');
	die();
?>

<?php else :?>

<div class="error"><p><?php _e('It appears that WP has corrupted your cforms settings, the settings array can not be read properly.','cforms'); ?></p></div>

<?php endif;

$c = str_replace('&','&amp;',$wpdb->get_var("SELECT option_value FROM `$wpdb->options` WHERE option_name='cforms_settings'"));

?>

<form name="corruptedsettings" method="POST">
<div id="wpbody">
	<div class="wrap">
		<h2><?php _e('Corrupted cforms settings detected','cforms'); ?></h2>
		<p><?php _e('You can either try and fix the settings array or reset it and start from scratch.','cforms'); ?> &nbsp;<input class="allbuttons deleteall" type="submit" name="resetsettings" id="resetsettings" value="<?php _e('RESET','cforms'); ?>"/></p>
    </div>
	<div class="wrap">
		<h2><?php _e('Corrupted cforms settings array (raw code)','cforms'); ?></h2>
		<p><?php _e('Depending on your Wordpress/PHP skills you may want to try and fix the serialized data below, then hit the fix button or try just like that, cforms may magically fix it for you.','cforms'); ?></p>
		<textarea style="font-size:11px; width:100%;" rows="16" cols="150" name="currentsettings" id="currentsettings"><?php echo $c; ?></textarea><br/>
		<input class="allbuttons" type="submit" name="fixsettings" id="fixsettings" value="<?php _e('FIX and save data','cforms'); ?>"/>
    </div>
</div>
</form>

<?php die(); ?>