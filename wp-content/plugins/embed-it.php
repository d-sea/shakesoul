<?php
/* Plugin Name: EmbedIt 
Plugin URI: http://www.matteoionescu.com/wordpress 
Description:  A simple plugin that allows you to embed any HTML code (Youtube, UStream or whatever) in a post, deciding precisely where to embed it, allowing you freedom of coding your html without being annoyed by the wysiwyg editor. USAGE:  While editing a post, create a custom field with key HTML1 and paste the code you want to embed into the Value field. Then just write [HTML1] in your post where you want to embed the html code. (Just use the standard wysiwyg post content editing window, no need to switch to Code view).   If you need, you can use up to nine times the trick: HTML2...HTML9 in order to embed up to nine html different snippets per post, placing each snippet in a different custom field.
Plugin Version: 0.1
Plugin Author: Matteo Ionescu
Author URI: http://www.matteoionescu.com/wordpress
 */ 
add_filter('the_content', 'substitute');




function substitute($content) { 
global $post;
 
$out=$content; // get the html of the whole current post/page

for ($number=1;$number<=9;$number++)
			{
			$key="HTML".$number;
			$html= get_post_meta($post->ID, $key,false);
			$html=$html[0];

			$find="[".$key."]";
			$out=str_replace($find,$html, $out); 
			}
	
return $out;
	}
?>