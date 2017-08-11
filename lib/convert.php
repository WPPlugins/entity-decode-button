<?php
/**
 * entity decode buttonのsend_txt()から渡されたデータを
 * エンコード、もしくはデコードし、変換したテキストを返す
 *
 * $_POST['select_txt'] 	編集画面で選択したテキスト（変換するテキスト）
 * $_POST['convart_flag']	ボタンのフラグ entity_char or entity_num or decode
 */

$str = $_POST['select_txt'];
$flag = $_POST['convert_flag'];

if ( $flag == "entity_char" ) {	// 実体参照化
	// $str =  htmlspecialchars($str, ENT_QUOTES); // クォート等のエンティはいらない？
	$str =  htmlspecialchars($str, ENT_NOQUOTES);
} elseif ( $flag == "entity_num" ) { // 10進数の数値文字参照化（文字参照）
	$convmap = array(0, 0x10FFFF, 0, 0x10FFFF);
	$str =  mb_encode_numericentity($str, $convmap, 'UTF-8');
} else { // デコード
	$str = html_entity_decode($str);
	$convmap = array(0, 0x10FFFF, 0, 0x10FFFF);
	$str = mb_decode_numericentity($str, $convmap, 'UTF-8');
}

echo urldecode($str);

?>