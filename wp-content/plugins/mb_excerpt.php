<?php
/*
Plugin Name: Multibyte Excerpt
Plugin URI: http://www.techdego.com/2007/02/mb_excerpt_plugin.php
Description: This plugin fooks the_excerpt(), the_excerpt_rss() and comment_excerpt() function to excerpt the multibyte words such as Japanese with the number of characters (not words).
Author: Junon
Version: 0.91b
Author URI: http://www.techdego.net/
*/ 

$mb_excerpt_word = 50; // how many mb characters to show
$mb_rss_word = 100;
$mb_comment_word = 80;


mb_internal_encoding(get_bloginfo('charset'));

function is_singlebytechar($character) {
    if (ord($character)>=32 && ord($character)<=126) 
    	return true;
    else
    	return false;
}

function mb_wordwrap($mb_word, $txt) {
   $pointer = 0;
   $result_text = "";

   while ($pointer < mb_strlen($txt)) {
       $this_char = mb_substr($txt,$pointer,1);
       if (is_singlebytechar($this_char)){
	       $mb_word++; // Do not count single byte char;
	   } elseif ($pointer > $mb_word) {
	   	   return mb_substr($txt,0,$pointer);
       }
       $pointer++;
    }
    return $txt; //Since length not exceeded return whole text
}

function mb_excerpt($content) {
	global $mb_excerpt_word;
	$text = mb_wordwrap($mb_excerpt_word, $content);
	if (preg_match("/(\.{3}|&#8230;)$/",strip_tags($text)) == 1) // ・ｽc:&#8230;
		return $text;
	else {
		return $text.'...';
	}
}

function mb_excerpt_rss($content) {
	global $mb_rss_word;
	return mb_wordwrap($mb_rss_word, $content);
}

function mb_comment_excerpt($content) {
	global $mb_comment_word;
	return mb_wordwrap($mb_comment_word, $content);
}

// Now we set that function up to execute when the tag is called
add_filter('the_excerpt', 'mb_excerpt', 9);
add_filter('the_excerpt_rss', 'mb_excerpt_rss', 9);
add_filter('comment_excerpt', 'mb_comment_excerpt', 9);
?>
