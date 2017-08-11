<?php
/*
Plugin Name: Entity Decode Button
Plugin URI: https://wordpress.org/plugins/entity-decode-button/
Description: テキストエディタにentityとdecodeボタンを追加します。
Version: 1.0
Author: oxynotes
Author URI: http://oxynotes.com
License: GPL2

// お決まりのGPL2の文言（省略や翻訳不可）
Copyright 2015 oxy (email : oxy@oxynotes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/




if ( !defined('ABSPATH') ) { exit(); }

// クラスが定義済みか調べる
if ( !class_exists('EntityDecodeButton') ) {

class EntityDecodeButton {

	public function __construct() {
		add_action( 'admin_head', array( $this, 'add_jquery' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'edb_add_quicktags' ) );
	}

	/**
	 * ページ名がpost-newの時にスクリプトを追加する
	 * 編集画面はデフォルトで既にjQuery v1.11.3が読み込まれているため不要
	 * テーマによっては無い場合もあるかもしれないので一応追加
	 */
	function add_jquery() {
	    wp_enqueue_script( 'jcrop' );
	}




	/**
	 * テキストエディタにボタンを追加するための関数
	 * 
	 * クイックタグAPIを利用している（以下codexのメモ。覚えるのめんどい）
	 * 
	 * 第1引数:id        :htmlボタンのid。buttonのidがeg_hrの例ならqt_content_eg_hrとなる。
	 * 第2引数:display   :htmlボタンの表示
	 * 第3引数:arg1      :クリックされた時に追加される文字列。もしくはコールバック
	 * 第4引数:arg2      :（以降オプション）閉じタグ
	 * 第5引数:access_key:ボタンのショートカットアクセスキー
	 * 第6引数:title     :htmlボタンのタイトルの値（ポップアップ）
	 * 第7引数:priority  :ツールバーのボタンの希望する順番を表す数値
	 * 第8引数:instance  :Quicktagsの特定のインスタンスにあるボタンを制限し、 存在しない場合はすべてのインスタンスに追加します。
	 */
	function edb_add_quicktags() {
	    if (wp_script_is('quicktags')){
		$plgin_dir = plugins_url("entity-decode-button"); // JavaScript側だとこの記述で取れないので予めセットしとく
	?>
		<script type="text/javascript">
		QTags.addButton( 'edb_entity_char', 'entity_char', edb_entity, '', '', 'Character Entity', 1 );
		QTags.addButton( 'edb_entity_num', 'entity_num', edb_entity, '', '', 'Numeric Entity', 2 );
		QTags.addButton( 'edb_decode', 'decode', edb_entity, '', '', 'Decode', 3 );

	/**
	 * デコードするためにPHPにデータを渡す関数
	 * returnで出力を返り値にしている
	 * 
	 * @param	str		変換する文字列
	 * @param	str		変換のフラグ（entity_num or entity_char or decode）
	 */
	function send_txt(selection, convert_flag){
		return jQuery.ajax({ // WordPress 版 jQuery はそのままでは $ が使えないので注意
			type: 'POST',
			url: "<?php echo $plgin_dir; ?>/lib/convert.php",
			data: {
				'select_txt': selection,
				'convert_flag': convert_flag
			}
		})
	}

	/**
	 * PHPのsubstr_replaceを模した関数
	 * strのstart文字目から、end文字目までをrepに置換する（1文字目は0から始まる）
	 * 
	 * @param	str		置換前の全文
	 * @param	str		置換部分の文字列
	 * @param	str		全文の先頭から置換する先頭文字までの文字数
	 * @param	str		全文の先頭から置換する最終文字までの文字数
	 * @return	str		置換後の文字列
	 */
	function substr_replace(str,rep,start,end){
		return str.substr(0, start) + rep + str.substr(end);
	};

	/**
	 * 選択した文字をエンコード、もしくはデコードする
	 * 
	 * QTags.addButtonのコールバック関数
	 * eはボタン自体（input）
	 * cはエディター全体（テキストエリア）
	 * cはQTogs（クイックタグのオブジェクト）がそれぞれ入ってる
	 * thisはコールバック関数自体が入ってる
	 */
	function edb_entity(e, c, ed) {

		var selection, selectionStart, selectionEnd, textLength;

		// 文字を選択しているか調べる
		if ( ed.canvas.selectionStart !== ed.canvas.selectionEnd ) { // 選択している場合
			selectionStart = ed.canvas.selectionStart
			selectionEnd = ed.canvas.selectionEnd
			textLength = ed.canvas.textLength

			selection = ed.canvas.value.substring(selectionStart, selectionEnd);

			// エンコード（エンティティ）、もしくはデコード処理
			// ボタンのvalueでentityかdecodeを分岐
			// done() fail()で成功した場合と、失敗した場合に分けている
			send_txt(selection, e.value).done(function(result) { // 成功した場合			
				ed.canvas.value = substr_replace(ed.canvas.value, result, selectionStart, selectionEnd);

				// カーソル位置を取るために変換前と後の文字列を比較し差分を出す（増えた文字数）
				textLength_rep = ed.canvas.value.length;
				diff = textLength_rep - textLength;

				// カーソルの位置を選択範囲の後ろに移動する
				// 増えた文字数 + 変換前の選択範囲の後半を足してキャレットの位置を決める
				ed.canvas.selectionStart = diff + selectionEnd; // フォーカスの前に位置を指定
				ed.canvas.selectionEnd = diff + selectionEnd;
				ed.canvas.focus();
			}).fail(function(result) { // 失敗した場合
			    // console.log(result);
			});

		} else { // 選択していない場合
			alert('文字を選択した状態で実行してください');
			return false;
		}
	}

	    </script>
	<?php
	    }
	}
} // end class
} // if class




$EntityDecodeButton = new EntityDecodeButton();
