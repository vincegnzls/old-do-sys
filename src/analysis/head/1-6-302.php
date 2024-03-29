<?php
$page_title = "実績データ";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm( "$_SERVER[PHP_SELF]","POST");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);

//本部蓄積データ
#2010-04-22 hashimoto-y
/*
$button[] = $form->createElement("button","csvh26","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-103.php')\"");
$button[] = $form->createElement("button","csvh27","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-101.php')\"");
$button[] = $form->createElement("button","csvh28","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-100.php')\"");
$button[] = $form->createElement("button","csvh29","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-102.php')\"");
$button[] = $form->createElement("button","csvh30","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-108.php')\"");
$button[] = $form->createElement("button","csvh31","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-105.php')\"");
$button[] = $form->createElement("button","csvh32","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-106.php')\"");
$button[] = $form->createElement("button","csvh33","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-107.php')\"");
$button[] = $form->createElement("button","csvh34","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-112.php')\"");
$button[] = $form->createElement("button","csvh35","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-111.php')\"");
$button[] = $form->createElement("button","csvh36","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-122.php')\"");
$button[] = $form->createElement("button","csvh37","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-121.php')\"");
$button[] = $form->createElement("button","csvh38","出力画面へ","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-131.php')\"");
*/
$button[] = $form->createElement("button","csvh20","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-132.php')\"");
$button[] = $form->createElement("button","csvh26","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-103.php')\"");
$button[] = $form->createElement("button","csvh27","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-101.php')\"");
$button[] = $form->createElement("button","csvh28","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-108.php')\"");
$button[] = $form->createElement("button","csvh29","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-106.php')\"");
$button[] = $form->createElement("button","csvh30","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-107.php')\"");
$button[] = $form->createElement("button","csvh31","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-112.php')\"");
$button[] = $form->createElement("button","csvh32","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-110.php')\"");
$button[] = $form->createElement("button","csvh33","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-111.php')\"");
$button[] = $form->createElement("button","csvh34","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-113.php')\"");
$button[] = $form->createElement("button","csvh35","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-114.php')\"");
$button[] = $form->createElement("button","csvh36","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-122.php')\"");
$button[] = $form->createElement("button","csvh37","出力画面へ","onClick=\"javascript:location.href('".ANALYSIS_DIR."head/1-6-121.php')\"");
$form->addGroup($button, "button", "");

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
$page_menu = Create_Menu_h('analysis','3');

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

$html_page = Html_Page($total_count,$page_count,1,$range);
$html_page2 = Html_Page($total_count,$page_count,2,$range);



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
