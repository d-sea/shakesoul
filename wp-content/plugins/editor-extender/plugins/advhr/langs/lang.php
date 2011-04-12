<?php
// escape text only if it needs translating
if( !function_exists('ee_mce_escape') ){
	function ee_mce_escape($text) {
		global $language;
		
		if ( 'en' == $language ) return $text;
		else return js_escape($text);
	}
}
$strings = 'tinyMCE.addI18n("' . $mce_locale . '.advhr_dlg",{
width:"' . ee_mce_escape( __('Width') ) . '",
size:"' . ee_mce_escape( __('Height') ) . '",
noshade:"' . ee_mce_escape( __('No shadow') ) . '"
});
';
?>