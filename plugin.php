<?php
/*
Plugin Name: Defer Adsense Load
Author: webfood
Plugin URI: http://webfood.info/
Description: Defer Adsense Load.
Version: 0.1
Author URI: http://webfood.info/
Text Domain: Defer Adsense Load
Domain Path: /languages

License:
 Released under the GPL license
  http://www.gnu.org/copyleft/gpl.html

  Copyright 2019 (email : webfood.info@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function defer_adsense_script(){
	echo <<< EOM
<script>
(function(window, document) {
function main() {
	// GoogleAdSense読込み
	var ad = document.createElement('script');
	ad.type = 'text/javascript';
	ad.async = true;
	// 新コードの場合、サイト運営者IDを書き換えてコメントアウトを外す
	// 旧コードの場合、コメントアウトしたままにする
	//ad.dataset.adClient = 'ca-pub-XXXXXXXXXXXXXXXX';
	ad.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
	var sc = document.getElementsByTagName('script')[0];
	sc.parentNode.insertBefore(ad, sc);
}

// 遅延読込み
var lazyLoad = false;
function onLazyLoad() {
	if (lazyLoad === false) {
		// 複数呼び出し回避 + イベント解除
		lazyLoad = true;
		window.removeEventListener('scroll', onLazyLoad);
		window.removeEventListener('mousemove', onLazyLoad);
		window.removeEventListener('mousedown', onLazyLoad);
		window.removeEventListener('touchstart', onLazyLoad);
		window.removeEventListener('keydown', onLazyLoad);

		main();
	}
}
window.addEventListener('scroll', onLazyLoad);
window.addEventListener('mousemove', onLazyLoad);
window.addEventListener('mousedown', onLazyLoad);
window.addEventListener('touchstart', onLazyLoad);
window.addEventListener('keydown', onLazyLoad);
window.addEventListener('load', function() {
	// ドキュメント途中（更新時 or ページ内リンク）
	if (window.pageYOffset) {
		onLazyLoad();
	}
});
})(window, document);
</script>
EOM;
}

add_filter('the_content','insert_defer_adsense', 99);
function insert_defer_adsense($content){
	if(get_post_meta( get_the_ID(), "custom_ad_off", true) == 'この記事で広告を表示しない' ) {
		return $content;
	}
	$check_content = mb_convert_encoding($content, 'HTML-ENTITIES', 'auto');
	$dom = new DOMDocument;
	$dom->loadHTML($check_content);
	$xpath = new DOMXPath($dom);
	$adsense = $xpath->query('//*[@class="adsbygoogle"]');
	if($adsense->length == 0){
		return $content;
	}
  add_action( 'shutdown', 'defer_adsense_script' );
	return $content;
}
