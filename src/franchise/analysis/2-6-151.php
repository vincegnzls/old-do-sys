<?php
$page_title = "ルート予定表";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm( "$_SERVER[PHP_SELF]","POST");

//DB接続
$db_con = Db_Connect();

//HTMLイメージ作成用部品
//require_once(PATH."include/html_quick.php");

//select作成用部品
//require_once(PATH."include/select_part.php");

//$button[] = $form->createElement("submit","hyouji45","表　示","onClick=\"javascript:window.open('".FC_DIR."analysis/2-6-152.php','_blank','')\"");

/****************************/
// フォームパーツ定義
/****************************/
// 出力形式
$radio54[] =& $form->createElement("radio", null, null, "画面", "1");
$radio54[] =& $form->createElement("radio", null, null, "CSV", "3");
$form->addGroup($radio54, "f_r_output23", "");

// 巡回月
$text8_1[] =& $form->createElement("text", "y_input", "", "size=\"4\" maxLength=\"4\" value=\"\" style=\"$g_form_style\" onkeyup=\"changeText13(this.form,1)\" $g_form_option");
$text8_1[] =& $form->createElement("static", "", "", "-");
$text8_1[] =& $form->createElement("text", "m_input", "", "size=\"1\" maxLength=\"2\" value=\"\" style=\"$g_form_style\" $g_form_option");
$form->addGroup($text8_1, "f_date_c1", "");

// 部署
$select_value = Select_Get($db_con, "part");
$form->addElement("select", "form_part_1", "", $select_value, $g_form_option_select);

// 巡回担当者
$select_value = Select_Get($db_con, "staff");
$form->addElement("select", "form_staff_1", "", $select_value, $g_form_option_select);

// 対象拠点
$select_value = Select_Get($db_con, "cshop");
$form->addElement("select", "form_cshop_1", "", $select_value, $g_form_option_select);

// 表示ボタン
$form->addElement("submit", "hyouji45", "表　示", "onClick=\"javascript:window.open('".FC_DIR."analysis/2-6-152.php','_blank','')\"");

// クリアボタン
$form->addElement("button", "kuria", "クリア", "onClick=\"javascript:SubMenu2('$_SERVER[PHP_SELF]')\"");


$def_fdata = array(
    "f_r_output23"  => "1"
);

$form->setDefaults($def_fdata);

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
$page_menu = Create_Menu_f('analysis','1');

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
