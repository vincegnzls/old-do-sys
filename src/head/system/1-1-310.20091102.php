<?php
/**********************************/
//変更履歴
//  DB構成変更にともない処理を変更9.11
//
//
//
//
//
/**********************************/



$page_title = "請求書設定";

// 環境設定ファイル
require_once("ENV_local.php");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

// DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
// 外部変数取得
/****************************/
$client_id  = $_SESSION[h_client_id];

/****************************/
// デフォルト値
/****************************/
$def_data["font_size_select_0"] = "12";
$def_data["font_size_select_1"] = "9";
$def_data["font_size_select_2"] = "6";
$def_data["font_size_select_3"] = "6";

$form->setDefaults($def_data);

/****************************/
// 部品定義
/****************************/
// セレクトボックス
$select_value = Select_Get($db_con, "claim_pattern");
$form->addElement("select", "pattern_select", "", $select_value, "size=\"5\" style=\"width: 350px;\"");

// パターン名
$commit_freeze[] = $form->addElement("text", "pattern_name", "", "size=\"34\" maxLength=\"30\" $g_form_option");

// 会社名
$commit_freeze[] = $form->addElement("text", "c_memo1", "", "maxLength=\"46\" style=\"width: 300px; font-size: 15px;\" $g_form_option");
// 代表取締役
$commit_freeze[] = $form->addElement("text", "c_memo2", "", "maxLength=\"46\" style=\"width: 300px; font-size: 13px;\" $g_form_option");
// 住所
$commit_freeze[] = $form->addElement("text", "c_memo3", "", "maxLength=\"46\" style=\"width: 300px; font-size: 10px;\" $g_form_option");
// TEL/FAX
$commit_freeze[] = $form->addElement("text", "c_memo4", "", "maxLength=\"46\" style=\"width: 300px; font-size: 10px;\" $g_form_option");
// 取引銀行
for($x=5;$x<=12;$x++){
    $commit_freeze[] = $form->addElement("text", "c_memo".$x,"", "size=\"56\" maxLength=\"62\" style=\"font-size: 10px;\" $g_form_option");
}
// コメント
$commit_freeze[] = $form->addElement("text", "c_memo13", "", "size=\"119\" maxLength=\"110\" style=\"font-size: 10px;\" $g_form_option");

// 請求書発行ボタン
$form->addElement("submit", "preview_button", "プレビュー", "onClick=\"javascript:PDF_POST('1-1-310-2.php')\"");

// 新規登録ボタン
$form->addElement("submit", "form_new_button", "新規登録", "onClick=\"javascript:Button_Submit('form_new_flg','#','true')\" $disabled");

// 登録ボタン
$form->addElement("submit", "new_button", "登　録", "onClick=\"javascript:return Dialogue('登録します。','#')\" $disabled");

// 変更ボタン
$form->addElement("submit", "change_button", "変　更", "onClick=\"javascript:Button_Submit('form_update_flg','#','true')\" $disabled");

// クリアボタン
$form->addElement("button", "clear_button", "クリア", "onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

// OKボタン
$form->addElement("button", "ok_button", "Ｏ　Ｋ", "onClick=javascript:location.href='1-1-310.php' $disabled");

// フォントサイズ
for($i=0; $i<4; $i++){
    $data["5"]  = "5";
    $data["6"]  = "6";
    $data["7"]  = "7";
    $data["8"]  = "8";
    $data["9"]  = "9";
    $data["10"] = "10";
    $data["11"] = "11";
    $data["12"] = "12";
    $data["13"] = "13";
    $data["14"] = "14";
    $data["15"] = "15";
    $form->addElement("select", "font_size_select_".$i, "", $data);
}

// hidden
$form->addElement("hidden", "form_new_flg");       // 新規登録ボタンフラグ
$form->addElement("hidden", "form_update_flg");    // 変更ボタンフラグ
$form->addElement("hidden", "h_pattern_id");       // パターン

/****************************/
// 変更ボタン押下処理
/****************************/
if($_POST["change_button"] == "変　更"){
    if($_POST[pattern_select]!=null){
        $sql  = "SELECT ";
        $sql .= "    c_pattern_name,";  // パターン名
        $sql .= "    c_memo1, ";        // コメント1
        $sql .= "    c_memo2, ";        // コメント2
        $sql .= "    c_memo3, ";        // コメント3
        $sql .= "    c_memo4, ";        // コメント4
        $sql .= "    c_fsize1,";        // コメント1フォントサイズ
        $sql .= "    c_fsize2,";        // コメント2フォントサイズ
        $sql .= "    c_fsize3,";        // コメント3フォントサイズ
        $sql .= "    c_fsize4,";        // コメント4フォントサイズ
        $sql .= "    c_memo5, ";        // コメント5
        $sql .= "    c_memo6, ";        // コメント6
        $sql .= "    c_memo7, ";        // コメント7
        $sql .= "    c_memo8, ";        // コメント8
        $sql .= "    c_memo9, ";        // コメント9
        $sql .= "    c_memo10, ";       // コメント10
        $sql .= "    c_memo11, ";       // コメント11
        $sql .= "    c_memo12, ";       // コメント12
        $sql .= "    c_memo13 ";        // コメント13
        $sql .= "FROM ";
        $sql .= "    t_claim_sheet ";
        $sql .= "WHERE ";
        $sql .= "    shop_id = $client_id ";
        $sql .= "AND ";
        $sql .= "    c_pattern_id = $_POST[pattern_select];";

        $result = Db_Query($db_con,$sql);
        // DBの値を配列に保存
        $c_memo = Get_Data($result,2);

        // データをフォームにセットする
        $update_button_data["pattern_name"]        = $c_memo[0][0];
        $update_button_data["c_memo1"]             = $c_memo[0][1];
        $update_button_data["c_memo2"]             = $c_memo[0][2];
        $update_button_data["c_memo3"]             = $c_memo[0][3];
        $update_button_data["c_memo4"]             = $c_memo[0][4];
        $update_button_data["font_size_select_0"]  = $c_memo[0][5];
        $update_button_data["font_size_select_1"]  = $c_memo[0][6];
        $update_button_data["font_size_select_2"]  = $c_memo[0][7];
        $update_button_data["font_size_select_3"]  = $c_memo[0][8];
        $update_button_data["c_memo5"]             = $c_memo[0][9];
        $update_button_data["c_memo6"]             = $c_memo[0][10];
        $update_button_data["c_memo7"]             = $c_memo[0][11];
        $update_button_data["c_memo8"]             = $c_memo[0][12];
        $update_button_data["c_memo9"]             = $c_memo[0][13];
        $update_button_data["c_memo10"]            = $c_memo[0][14];
        $update_button_data["c_memo11"]            = $c_memo[0][15];
        $update_button_data["c_memo12"]            = $c_memo[0][16];
        $update_button_data["c_memo13"]            = $c_memo[0][17];
        $update_button_data["form_new_flg"]        = false;
        $update_button_data["h_pattern_id"]        = $_POST["pattern_select"];    // 選択されたパターンをhiddenにセットする
        $form->setConstants($update_button_data);
    }else{
        $pattern_err = "・変更するパターンを選択してください。";
    }
}

/****************************/
// 登録ボタン押下処理
/****************************/
if (isset($_POST["new_button"])){

    /****************************/
    // エラーチェック
    /****************************/
    // パターン名
    // ●必須チェック
    $form->addRule("pattern_name", "パターン名は30文字以内です。", "required");

    $qf_err_flg = ($form->validate() == false) ? true : false;

}

if($_POST["new_button"] == "登　録" && $qf_err_flg === false){
    $c_pattern_name = $_POST["pattern_name"];           // パターン名
    $c_memo1        = $_POST["c_memo1"];                // コメント1
    $c_memo2        = $_POST["c_memo2"];                // コメント2
    $c_memo3        = $_POST["c_memo3"];                // コメント3
    $c_memo4        = $_POST["c_memo4"];                // コメント4
    $c_fsize1       = $_POST["font_size_select_0"];     // コメント1(フォントサイズ)
    $c_fsize2       = $_POST["font_size_select_1"];     // コメント2(フォントサイズ)
    $c_fsize3       = $_POST["font_size_select_2"];     // コメント3(フォントサイズ)
    $c_fsize4       = $_POST["font_size_select_3"];     // コメント4(フォントサイズ)
    $c_memo5        = $_POST["c_memo5"];                // コメント5
    $c_memo6        = $_POST["c_memo6"];                // コメント6
    $c_memo7        = $_POST["c_memo7"];                // コメント7
    $c_memo8        = $_POST["c_memo8"];                // コメント8
    $c_memo9        = $_POST["c_memo9"];                // コメント9
    $c_memo10       = $_POST["c_memo10"];               // コメント10
    $c_memo11       = $_POST["c_memo11"];               // コメント11
    $c_memo12       = $_POST["c_memo12"];               // コメント12
    $c_memo13       = $_POST["c_memo13"];               // コメント13
    $pattern_id     = $_POST["h_pattern_id"];           // パターンID
    
    Db_Query($db_con, "BEGIN;");

    // 新規登録・更新判定
    if($_POST["form_new_flg"] ==true){
        $sql  = "INSERT INTO ";
        $sql .= "t_claim_sheet ";
        $sql .= "(shop_id,";
        $sql .= "c_pattern_id,";
        $sql .= "c_pattern_name,";
        $sql .= "c_memo1,";
        $sql .= "c_memo2,";
        $sql .= "c_memo3,";
        $sql .= "c_memo4,";
        $sql .= "c_fsize1,";
        $sql .= "c_fsize2,";
        $sql .= "c_fsize3,";
        $sql .= "c_fsize4,";
        $sql .= "c_memo5,";
        $sql .= "c_memo6,";
        $sql .= "c_memo7,";
        $sql .= "c_memo8,";
        $sql .= "c_memo9,";
        $sql .= "c_memo10,";
        $sql .= "c_memo11,";
        $sql .= "c_memo12,";
        $sql .= "c_memo13) ";
        $sql .= "VALUES(";
        $sql .= "'$client_id',";
        $sql .= "(SELECT COALESCE(MAX(c_pattern_id), 0)+1 FROM t_claim_sheet),";
        $sql .= "'$c_pattern_name',";
        $sql .= "'$c_memo1',";
        $sql .= "'$c_memo2',";
        $sql .= "'$c_memo3',";
        $sql .= "'$c_memo4',";
        $sql .= "'$c_fsize1',";
        $sql .= "'$c_fsize2',";
        $sql .= "'$c_fsize3',";
        $sql .= "'$c_fsize4',";
        $sql .= "'$c_memo5',";
        $sql .= "'$c_memo6',";
        $sql .= "'$c_memo7',";
        $sql .= "'$c_memo8',";
        $sql .= "'$c_memo9',";
        $sql .= "'$c_memo10',";
        $sql .= "'$c_memo11',";
        $sql .= "'$c_memo12',";
        $sql .= "'$c_memo13');";
    }else{
        $sql  = "UPDATE ";
        $sql .= "t_claim_sheet ";
        $sql .= "SET ";
        $sql .= "c_memo1        = '$c_memo1', ";
        $sql .= "c_pattern_name = '$c_pattern_name',";
        $sql .= "c_memo2        = '$c_memo2', ";
        $sql .= "c_memo3        = '$c_memo3', ";
        $sql .= "c_memo4        = '$c_memo4', ";
        $sql .= "c_fsize1       = '$c_fsize1', ";
        $sql .= "c_fsize2       = '$c_fsize2', ";
        $sql .= "c_fsize3       = '$c_fsize3', ";
        $sql .= "c_fsize4       = '$c_fsize4', ";
        $sql .= "c_memo5        = '$c_memo5', ";
        $sql .= "c_memo6        = '$c_memo6', ";
        $sql .= "c_memo7        = '$c_memo7', ";
        $sql .= "c_memo8        = '$c_memo8', ";
        $sql .= "c_memo9        = '$c_memo9', ";
        $sql .= "c_memo10       = '$c_memo10', ";
        $sql .= "c_memo11       = '$c_memo11', ";
        $sql .= "c_memo12       = '$c_memo12', ";
        $sql .= "c_memo13       = '$c_memo13' ";
        $sql .= "WHERE ";
        $sql .= "   shop_id = $client_id";
        $sql .= "   AND ";
        $sql .= "   c_pattern_id = $pattern_id;";
    }

    $result = Db_Query($db_con,$sql);
    if($result == false){
        Db_Query($db_con,"ROLLBACK;");
        exit;
    }
    $claim_data[0]  = $c_pattern_name;
    $claim_data[1]  = $c_memo1;
    $claim_data[2]  = $c_memo2;
    $claim_data[3]  = $c_memo3;
    $claim_data[4]  = $c_memo4;
    $claim_data[5]  = $c_memo5;
    $claim_data[6]  = $c_memo6;
    $claim_data[7]  = $c_memo7;
    $claim_data[8]  = $c_memo8;
    $claim_data[9]  = $c_memo9;
    $claim_data[10] = $c_memo10;
    $claim_data[11] = $c_memo11;
    $claim_data[12] = $c_memo12;
    $claim_data[13] = $c_memo13;

    $commit_flg = true;
    Db_Query($db_con, "COMMIT;");
}

if ($commit_flg == true){
    $commit_freeze_form = $form->addGroup($commit_freeze, "commit_freeze", "");
    $commit_freeze_form->freeze();
}

/****************************/
// HTMLヘッダ
/****************************/
//$html_header = Html_Header($page_title);
$html_header = Html_Header($page_title, "amenity.js", "global.css", "slip.css");

/****************************/
// HTMLフッタ
/****************************/
$html_footer = Html_Footer();

/****************************/
// メニュー作成
/****************************/
$page_menu = Create_Menu_h("system", "2");

/****************************/
// 画面ヘッダー作成
/****************************/
$page_header = Create_Header($page_title);


// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form関連の変数をassign
$smarty->assign("form", $renderer->toArray());

$smarty->assign("row", $claim_data);

// その他の変数をassign
$smarty->assign("var", array(
    "html_header"   => "$html_header",
    "page_menu"     => "$page_menu",
    "page_header"   => "$page_header",
    "html_footer"   => "$html_footer",
    "comp_msg"      => "$comp_msg",
    "commit_flg"    => "$commit_flg",
    "pattern_err"   => "$pattern_err",
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF].".tpl"));

?>
