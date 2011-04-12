<?php
// escape text only if it needs translating
if( !function_exists('ee_mce_escape') ){
	function ee_mce_escape($text) {
		global $language;
		
		if ( 'en' == $language ) return $text;
		else return js_escape($text);
	}
}
$strings = 'tinyMCE.addI18n("' . $mce_locale . '.searchreplace_dlg",{
searchnext_desc:"Find again",
notfound:"The search has been completed. The search string could not be found.",
search_title:"Find",
replace_title:"Find/Replace",
allreplaced:"All occurrences of the search string were replaced.",
findwhat:"Find what",
replacewith:"Replace with",
direction:"Direction",
up:"Up",
down:"Down",
mcase:"Match case",
findnext:"Find next",
replace:"Replace",
replaceall:"Replace all"
});
';
?>