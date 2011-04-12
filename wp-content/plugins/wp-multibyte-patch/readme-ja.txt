
WP Multibyte Patch 1.1

動作対象: WordPress 2.6
プラグイン URI: http://eastcoder.com/code/wp-multibyte-patch/
作者: tenpura (Email: 210pura at gmail dot com)


== 説明 ==

本家版、日本語版 WordPress のマルチバイト文字の取り扱いに関する不具合の
累積的修正と強化を行うプラグインです。
詳しくは下記のページをご覧ください。
http://eastcoder.com/code/wp-multibyte-patch/
なお、本リリースには、日本語処理に特化した拡張機能（エクステンション）が
含まれています。


== インストール方法 ==

1. ZIP を解凍し、フォルダごと /wp-content/plugins/ の中に入れてください。

2. 設定ファイルを利用する場合は下記のようにそれぞれ変名してください。

	wpmp-config-sample.php -> wpmp-config.php
	config-sample.php -> config.php

3. アドミンのプラグイン管理ページで WP Multibyte Patch を
   有効にしてください。


== 前バージョンからのアップグレード ==

フォルダごと中身を上書きしてください。


== 日本語エクステンションのフォルダ名について ==

wp-config.php の言語設定が

define ('WPLANG', 'ja'); の場合は

	/wp-multibyte-patch/ext/ja
	 または 
	/wp-multibyte-patch/ext/default

define ('WPLANG', ''); の場合は

	/wp-multibyte-patch/ext/default

となるように必要に応じて変名してください。
後者は「本家英語版利用時に文字データの入出力処理は日本語で行いたい」
といった場合に有効な設定です。

* /wp-multibyte-patch/ext 以下に WPLANG 値と同名のフォルダがある場合は
  そのフォルダ（エクステンション）が優先して読み込まれます。
* WPLANG 値と同名のフォルダがなく 'default' という名前のフォルダがある場合は
  'default' フォルダが読み込まれます。


== 更新履歴 ==

下記をご覧ください。
http://eastcoder.com/?tag=releases+wpmp

