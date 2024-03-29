<?php
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *   2016/01/22                amano  Dialogue 関数でボタン名が送られない IE11 バグ対応
 */
$page_title = "納品書メモ設定";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//デフォルト値設定
/****************************/
$sql  = "SELECT ";
$sql .= "d_memo1, ";		//納品書コメント1
$sql .= "d_memo2, ";		//納品書コメント2
$sql .= "d_memo3 ";			//納品書コメント3
$sql .= "FROM ";
$sql .= "t_h_ledger_sheet;";

$result = Db_Query($db_con,$sql);
//DBの値を配列に保存
$d_memo = Get_Data($result, 2);

//行の存在判定フラグ
$id_null_flg=false;
//データのNULL判定フラグ
$value_flg=false;
//データ存在判定
if(pg_num_rows($result)==null){
	$id_null_flg = true;
}
//新規登録・更新判定
for($c=1;$c<count($d_memo[0]);$c++){
	if($d_memo[0][$c] != null){
		$value_flg = true;
	}
}

$def_fdata = array(
    "d_memo1"     	=> $d_memo[0][0],
    "d_memo2"      	=> $d_memo[0][1],
    "d_memo3"     	=> $d_memo[0][2]
);
$form->setDefaults($def_fdata);

/****************************/
//部品定義
/****************************/
//コメント&#65533;
$freeze = $form->addElement("text","d_memo1".$x,"テキストフォーム","size=\"50\" maxLength=\"46\" style=\"font-size:12px;\"".$g_form_option."\"");
$freeze->freeze();
//コメント&#65533;
$freeze = $form->addElement("text","d_memo2".$x,"テキストフォーム","size=\"50\" maxLength=\"46\" style=\"font-size:12px;\"".$g_form_option."\"");
$freeze->freeze();

$form->addElement("textarea","d_memo3",""," rows=\"3\" cols=\"45\" $g_form_option_area");
$form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
$form->addRule("d_memo3","メモは60文字以内です。","mb_maxlength","60");

//登録ボタン
$form->addElement("submit","new_button","登　録","onClick=\"javascript: return Dialogue('登録します。','#', this)\" $disabled");

//納品書出力ボタン
$form->addElement("button","deli_button","プレビュー","onClick=\"javascript:window.open('../system/1-1-317.php','_blank','')\"");

/****************************/
//登録ボタン押下処理
/****************************/
if($_POST["new_button"] == "登　録"){
	$d_memo1 = $_POST["d_memo1"];				//納品書コメント1
	$d_memo2 = $_POST["d_memo2"];				//納品書コメント2
	$d_memo3 = $_POST["d_memo3"];				//納品書コメント3
                                                
	Db_Query($db_con, "BEGIN;");

	if($form->validate()){
		//変更完了メッセージ
		$comp_msg = "変更しました。";

		$sql  = "UPDATE ";
		$sql .= "t_h_ledger_sheet ";
		$sql .= "SET ";
		$sql .= "d_memo1 = '$d_memo1', ";
		$sql .= "d_memo2 = '$d_memo2', ";
		$sql .= "d_memo3 = '$d_memo3';";
	}

	$result = Db_Query($db_con,$sql);
	if($result == false){
		Db_Query($db_con,"ROLLBACK;");
		exit;
	}
	Db_Query($db_con, "COMMIT;");
}

/****************************/
//HTMLヘッダ
/****************************/
//$html_header = Html_Header($page_title);
$html_header = Html_Header($page_title, "amenity.js", "global.css", "slip.css");

/****************************/
//HTMLフッタ
/****************************/
$html_footer = Html_Footer();

/****************************/
//メニュー作成
/****************************/
$page_menu = Create_Menu_h('system','2');
/****************************/
//画面ヘッダー作成
/****************************/
$page_header = Create_Header($page_title);


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
	'comp_msg'   	=> "$comp_msg",
    'auth_r_msg'    => "$auth_r_msg",
));

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
