<?php

// 'imgtext' plugin for PukiWiki
// author: m0370
// Twitter: @m0370
// サムネイル用に、ページタイトルを取り込んだ画像を出力してくれるPukiwikiプラグイン

// ver1.0 (2024.3.31)

// 設定項目
define('IMGTEXT_DIR', 'img/'); // 画像保存ディレクトリ
// if (!defined('IMAGE_DIR')) define('IMAGE_DIR', 'img/'); 

define('TEMPLATE_IMG', './skin/thumbnail.png'); // テンプレート画像ファイル（Twitterブログカード用の推奨画像サイズ：2048x1072）
define('FONTPATH', './skin/fonts/ipagp.ttf'); // TTFフォントのファイル
define('FONTSIZE', 80); // フォントサイズ（推奨値：80）
define('LEFT', 160); // 左からのテキスト開始位置（推奨値：160）
define('TOP', 240); // 上からのテキスト開始位置（推奨値：240）
define('LINE', 150); // 行の高さ（推奨値：150）
define('SUB_FONTSIZE', 48); // 日時表示のフォントサイズ（推奨値：48）


function plugin_imgtext_convert()
{
	global $vars;
	$page = isset($vars['page']) ? $vars['page'] : '';

	exist_plugin('s');
	$page_id = plugin_s_get_page_id($page);
	$gifcache = IMGTEXT_DIR . $page_id . '.gif';
	$jpgcache = IMGTEXT_DIR . $page_id . '.jpg';
	$pngcache = IMGTEXT_DIR . $page_id . '.png';
	if(file_exists($jpgcache)) { $imgcache = $jpgcache ; }
	else if(file_exists($gifcache)) { $imgcache = $gifcache ; }
	else { $imgcache = $pngcache ; }

	if(!file_exists($imgcache)) {
		exist_plugin('topicpath');
		$leafname = plugin_topicpath_leafname_inline($page);
	
		// 画像を生成
		$img = imagecreatefrompng(TEMPLATE_IMG);
		$text_date = substr(format_date(get_filetime($page)), 0, 10);
		$text_array = mb_str_split($leafname, 16);
		
		// パラメータ設定
		$text_color = imagecolorallocate($img, 127, 127, 127); 
		$y = TOP;
		
		foreach ($text_array as $text) {
		  // 生成した画像に文字を貼り付ける
		  // 引数：(画像, フォントサイズ, 文字の角度, x座標, y座標,  文字色, フォント, 貼り付ける文字)
		  imagettftext($img, FONTSIZE, 0, LEFT, $y, $text_color, FONTPATH, $text);
		  // 指定した高さ分y座標を下げることで、改行をする
		  $y = $y + LINE;
		  if ($y >= 600) {break;} 
		}
		
		imagettftext($img, SUB_FONTSIZE, 0, LEFT, $y, $text_color, FONTPATH, $text_date);
		
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