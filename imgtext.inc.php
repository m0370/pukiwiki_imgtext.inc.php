<?php

// 'imgtext' plugin for PukiWiki
// author: m0370
// Twitter: @m0370
// サムネイル用に、ページタイトルを取り込んだ画像を出力してくれるPukiwikiプラグイン

// ver0.9 (2024.3.31) プロトタイプ
// 自分自身のサイト用に作成した段階なのでフォルダやパラメータなどの微調整は未実施です。

function plugin_imgtext_convert()
{
	global $vars;
	$page = isset($vars['page']) ? $vars['page'] : '';

	exist_plugin('s');
	$page_id = plugin_s_get_page_id($page);
	$gifcache = 'img/' . $page_id . '.gif';
	$jpgcache = 'img/' . $page_id . '.jpg';
	$pngcache = 'img/' . $page_id . '.png';
	if(file_exists($jpgcache)) { $imgcache = $jpgcache ; }
	else if(file_exists($gifcache)) { $imgcache = $gifcache ; }
	else { $imgcache = $pngcache ; }

	if(!file_exists($imgcache)) {
		exist_plugin('topicpath');
		$leafname = plugin_topicpath_leafname_inline($page);
	
		// 画像を生成
		$img = imagecreatefrompng('./skin/oncologynote_thumbnail.png');
		$text_full = $leafname;
		$text_date = substr(format_date(get_filetime($page)), 0, 10);
		$text_array = mb_str_split($text_full, 16);
		
		// パラメータ設定
		$fontsize = 80;
		$text_color = imagecolorallocate($img, 127, 127, 127); 
		$font = './skin/fonts/hiraginoW6.ttf';
		$y = 240;
		
		foreach ($text_array as $text) {
		  // 生成した画像に文字を貼り付ける
		  // 引数：(画像, フォントサイズ, 文字の角度, x座標, y座標,  文字色, フォント, 貼り付ける文字)
		  imagettftext($img, $fontsize, 0, 160, $y, $text_color, $font, $text);
		  // 指定した高さ分y座標を下げることで、改行をする
		  $y = $y + 150;
		  if ($y >= 600) {break;} 
		}
		
		imagettftext($img, 48, 0, 160, $y, $text_color, $font, $text_date);
		
		// 出力
		$imgfilename = $page_id . '.png';
		imagepng($img,$pngcache);
		imagedestroy($img);

		return <<<EOD
		サムネイル画像を生成しました。<br>
		leafname: $leafname<br>
		text_date: $text_date<br>
		EOD;
		
	}
}
?>