<?php
$page_title = "契約マスタ";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm( "$_SERVER[PHP_SELF]","POST");

//HTMLイメージ作成用部品
require_once(PATH."include/html_quick.php");

/****************************/
//HTMLヘッダ
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//HTMLフッタ
/****************************/
$html_footer = Html_Footer();

/****************************/
//メニュー作成
/****************************/
$page_menu = Create_Menu_h('system','1');
/****************************/
//画面ヘッダー作成
/****************************/
$page_header = Create_Header($page_title);

/****************************/
//ページ作成
/****************************/
//仮の件数
$total_count = 100;

//表示範囲指定
$range = "20";

//ページ数を取得
$page_count = $_POST["f_page1"];

$html_page = Html_Page($total_count,$page_count,1,$range,'650');
$html_page2 = Html_Page($total_count,$page_count,2,$range,'650');



// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());

//その他の変数をassign
$smarty->assign('var',array(
	'html_header'   => "$html_header",
	'page_menu'     => "$page_menu",
	'page_header'   => "$page_header",
	'html_footer'   => "$html_footer",
	'html_page'     => "$html_page",
	'html_page2'    => "$html_page2",
));

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
