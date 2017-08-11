=== Entity Decode Button ===
Contributors: oxynotes
Donate link: https://wordpress.org/plugins/entity-decode-button/
Tags: editor, entity, encode, decode
Requires at least: 4.4.1
Tested up to: 4.4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

テキストエディタにentityとdecodeボタンを追加します。

== Description ==

テキストエディタに3つのボタンを追加します。
&lt;や&gt;といったHTMLタグだけでなく、&#64;や&#169;といった特殊文字もエンティティ化することができます。

**「entity_char」ボタン**

文字を選択した状態で「entity_char」ボタンをクリックすると実体参照化します。PHPのhtmlspecialcharsに相当します。

例）「entity_char」ボタンの変換
`<div>　→　&lt;div&gt;`

**「entity_num」ボタン**

「entity_num」ボタンは10進数の数値文字参照化（文字参照）します。PHPのmb_encode_numericentityに相当します。（改行も変換するので複数行実行する場合は注意）

例）「entity_num」ボタンの変換
`<div>　→　&#60;&#100;&#105;&#118;&#62;`

**「decode」ボタン**

「decode」ボタンは実体参照・文字参照の両方をデコードします。

例）「decode」ボタンの変換
`&lt;div&gt;　→　<div>
&#60;&#100;&#105;&#118;&#62;　→　<div>`

== Installation ==

1. プラグインの新規追加ボタンをクリックして、検索窓に「Entity Decode Button」と入力して「今すぐインストール」をクリックします。
1. もしくはこのページのzipファイルをダウンロードして解凍したフォルダを`/wp-content/plugins/`ディレクトリに保存します。
1. 設定画面のプラグインで **Entity Decode Button** を有効にしてください。

== Frequently asked questions ==

-

== Screenshots ==

1. html editor.

== Changelog ==

1.0
初めのバージョン。


== Upgrade notice ==

-