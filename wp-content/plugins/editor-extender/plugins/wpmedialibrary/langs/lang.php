<?php
// escape text only if it needs translating
if( !function_exists('ee_mce_escape') ){
	function ee_mce_escape($text) {
		global $language;
		
		if ( 'en' == $language ) return $text;
		else return js_escape($text);
	}
}
$strings = 'tinyMCE.addI18n("' . $mce_locale . '.wpmedialibrary_dlg",{
	title : "This is just a example title"
});

tinyMCE.addI18n("' . $mce_locale . '.wpmedialibrary",{
	desc : "This is just a template button"
});

';
?>
