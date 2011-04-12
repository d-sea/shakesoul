<?php
/*
Plugin Name: Hello Dolly
Plugin URI: http://wordpress.org/#
Description: これはただのプラグインではありません。Louis Armstrong によって歌われた最も有名な二つの単語、Hello, Dolly に要約された同一世代のすべての人々の希望と情熱を象徴するものです。このプラグインが有効にされると、すべての管理画面の右上に <cite>Hello, Dolly</cite> からの歌詞がランダムに表示されます。
Author: Matt Mullenweg
Version: 1.5
Author URI: http://ma.tt/
*/

// These are the lyrics to Hello Dolly
$lyrics = "Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
We feel the room swayin'
While the band's playin'
One of your old favourite songs from way back when
So, take her wrap, fellas
Find her an empty lap, fellas
Dolly'll never go away again
Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
We feel the room swayin'
While the band's playin'
One of your old favourite songs from way back when
Golly, gee, fellas
Find her a vacant knee, fellas
Dolly'll never go away
Dolly'll never go away
Dolly'll never go away again";

// Here we split it into lines
$lyrics = explode("\n", $lyrics);
// And then randomly choose a line
$chosen = wptexturize( $lyrics[ mt_rand(0, count($lyrics) - 1) ] );

// This just echoes the chosen line, we'll position it later
function hello_dolly() {
	global $chosen;
	echo "<p id='dolly'>$chosen</p>";
}

// Now we set that function up to execute when the admin_footer action is called
add_action('admin_footer', 'hello_dolly');

// We need some CSS to position the paragraph
function dolly_css() {
	echo "
	<style type='text/css'>
	#dolly {
		position: absolute;
		top: 2.3em;
		margin: 0;
		padding: 0;
		right: 10px;
		font-size: 16px;
		color: #d54e21;
	}
	</style>
	";
}

add_action('admin_head', 'dolly_css');

?>