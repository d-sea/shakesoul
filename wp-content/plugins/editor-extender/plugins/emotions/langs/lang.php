<?php
// escape text only if it needs translating
if( !function_exists('ee_mce_escape') ){
	function ee_mce_escape($text) {
		global $language;
		
		if ( 'en' == $language ) return $text;
		else return js_escape($text);
	}
}
$strings = 'tinyMCE.addI18n("' . $mce_locale . '.emotions_dlg",{
title:"Insert emotion",
desc:"Emotions",
cool:"Cool",
cry:"Cry",
embarassed:"Embarassed",
foot_in_mouth:"Foot in mouth",
frown:"Frown",
innocent:"Innocent",
kiss:"Kiss",
laughing:"Laughing",
money_mouth:"Money mouth",
sealed:"Sealed",
smile:"Smile",
surprised:"Surprised",
tongue_out:"Tongue out",
undecided:"Undecided",
wink:"Wink",
yell:"Yell"
});
';
?>