<?php
/* WP Multibyte Patch global config file */

// Controls how long excerpts will be.
$wpmp_conf['excerpt_length'] = 55;            // Word count for ascii posts
$wpmp_conf['excerpt_mblength'] = 110;         // Character count for multibyte posts
$wpmp_conf['comment_excerpt_length'] = 20;    // Word count for ascii comments
$wpmp_conf['comment_excerpt_mblength'] = 40;  // Character count for multibyte comments

// Set "false" to disable patches individually.
$wpmp_conf['patch_wp_mail'] = true;
$wpmp_conf['patch_incoming_trackback'] = true;
$wpmp_conf['patch_incoming_pingback'] = true;
$wpmp_conf['patch_wp_trim_excerpt'] = true;
$wpmp_conf['patch_get_comment_excerpt'] = true;
$wpmp_conf['patch_process_search_terms'] = true;
$wpmp_conf['patch_admin_custom_css'] = true;

?>