<?php 
/* ==================================================
 *   Ktai Admin Output Footer
   ================================================== */
?>
<!--end paging-->
<hr color="#4f96c8" />
<a name="tail" href="#head"><img localsrc="29" alt="<?php _e('^', 'ktai_style'); ?>" /></a>| <?php include_once dirname(__FILE__) . '/menu-header.php'; ?>
<hr color="#4f96c8" />
<div align="right">Ktai Style <?php echo KS_VERSION; ?></div>
</body></html>
<?php 
global $Ktai_Style, $KS_Admin, $page_charset, $iana_charset, $mime_type;
$referer = $KS_Admin->get_referer();
$url = parse_url($referer);
parse_str($url['query'], $param);
if (! isset($param[KS_SESSION_NAME])) {
	$referer = $KS_Admin->add_sid($referer, false);
}
$KS_Admin->store_referer()->save_data();
$KS_Admin->unset_prev_session($KS_Admin->get_sid());
$buffer = $Ktai_Style->ktai->get('preamble') . "\n";
$buffer .= ob_get_contents();
ob_end_clean();
$buffer = $Ktai_Style->ktai->shrink_pre_encode($buffer);
$buffer = mb_convert_encoding($buffer, $page_charset, get_bloginfo('charset'));
$buffer = $Ktai_Style->ktai->shrink_pre_split($buffer);
$buffer = $Ktai_Style->ktai->convert_pict($buffer);
list($header, $buffer) = preg_split('/\n*<!--start paging-->\n*/', $buffer, 2);
list($buffer, $footer) = preg_split('/\n*<!--end paging-->\n*/', $buffer, 2);
if (strlen("$header$buffer$footer") > $Ktai_Style->ktai->get('page_size')) {
	$buffer = sprintf(__('<p>The page is too big for your terminal. Please back to <a href="%s">the previous page</a>.</p>', 'ktai_style'), attribute_escape($referer));
	$buffer = mb_convert_encoding($buffer, $page_charset, get_bloginfo('charset'));
}
$buffer = $Ktai_Style->ktai->shrink_post_split("$header$buffer$footer");
header ("Content-Type: $mime_type; charset=$iana_charset");
echo $buffer;
?>