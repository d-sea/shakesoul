<?php
function ks_convert_kana($buffer) {
	$charset = get_bloginfo('charset');
	if (preg_match('/^(utf-8|shift_jis|sjis|sjis-win|euc-jp|eucjp-win)$/i', $charset)) {
		$buffer = mb_convert_kana($buffer, 'kas', $charset);
	}
	return $buffer;
}

if (! defined('KS_ADMIN_MODE')) {
	add_filter('raw_content/ktai_style.php', 'ks_convert_kana');
}
?>