<?php

/*
 * 履歴：
 *
 *  日付        担当者          内容
 *-----------------------------------------------
 *  2007-11-17  fukuda          新規作成
 *
 */

//-----------------------------------------------
// 初期設定
//-----------------------------------------------
// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB接続
$db_con = Db_Connect();

// ショップID
$shop_id    = $_SESSION["client_id"];

// フォームオブジェクト定義
Mk_Form($db_con, $form);


//-----------------------------------------------
// 表示ボタン押下時処理
//-----------------------------------------------
// 表示ボタンのPOSTデータがある場合
if ($_POST["form_display"] != null) {

    // POSTフラグ
    $post_flg = true;

    // POSTデータ取得
    $post = $form->exportValues();

    // エラーチェック処理
    Err_Chk_Date_YM($form);

    // エラーチェック適用
    $form->validate();

    // エラーチェック結果
    $err_flg = (count($form->_errors) > 0) ? true : false;

}


//-----------------------------------------------
// データ抽出処理
//-----------------------------------------------
// POSTフラグがtrue＋エラーチェック結果がtrueでない場合
if ($post_flg === true && $err_flg !== true) {

    // 集計期間取得処理
    $ary_ym         = Make_Analysis_YM($post);
    $count_month    = count($ary_ym);

    // 表示用データ抽出処理
    $res            = Select_Each_Part_Staff_Amount($db_con, $post, $shop_id, $ary_ym);

    // 表示用データ集計処理
    $disp_data      = Edit_Query_Data_Hogepiyo($res, $post);

    // 表示用データのヘッダ行取得処理
    $disp_head      = Get_Header_YM(
        $post["form_trade_ym_s"]["y"],
        $post["form_trade_ym_s"]["m"],
        $post["form_trade_ym_e"]
    );

    // 出力フラグ
    $out_flg        = "true";

    // 本部の場合
    if ($shop_id === "1") {
        // ショップコード、ショップ名を取得
        $shop_data  = Get_Shop_Data($db_con, $post["form_shop_part"][1]);
    }

}


//-----------------------------------------------
// CSV出力処理
//-----------------------------------------------
// CSV出力が選択されている＋出力フラグがtrueの場合
if ($post["form_output_type"] == "2" && $out_flg === "true") {

    // 粗利表示フラグ
    $margin_flg = ($post["form_margin"] == "1") ? true : false;

    $csvobj = new Analysis_Csv_Class($margin_flg, false); 
    $csvobj->Enc_FileName($page_title.date("Ymd").".csv");

    // CSV項目名作成
    $csv_head = array("部署コード", "部署名", "担当者コード", "スタッフ名");

    $csvobj->Make_Csv_Head($disp_head, $csv_head);
    $csvobj->Make_Csv_Data($disp_data);

    // CSVを出力して終了
    Header("Content-disposition: attachment; filename=".$csvobj->filename);
    Header("Content-type: application/octet-stream; name=".$csvobj->file_name);
    print $csvobj->res_csv;
    exit;

}


//-----------------------------------------------
// HTMLテンプレート用データ
//-----------------------------------------------
// HTMLヘッダ
$html_header = Html_Header($page_title);

// HTMLフッタ
$html_footer = Html_Footer();

// 画面ヘッダー作成
$page_header = Create_Header($page_title);

// ページ作成
$html_page = Html_Page($total_count, $page_count, 1, $range);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form関連の変数をassign
$smarty->assign("form", $renderer->toArray());

// その他のデータをassign
$smarty->assign("var", array(
	"html_header"   => "$html_header",
	"page_header"   => "$page_header",
	"html_footer"   => "$html_footer",
	"html_page"     => "$html_page",
    "count_month"   => "$count_month",
));

// 表示データをassign
$smarty->assign("disp_head", $disp_head);
$smarty->assign("disp_data", $disp_data);
$smarty->assign("out_flg",   "$out_flg");
$smarty->assign("shop_data", "$shop_data");

// テンプレートへ値を渡す
$smarty->display("132.php.tpl");



//-----------------------------------------------
// モジュール内関数定義
//-----------------------------------------------

/**
 * 集計期間取得
 *
 * @param       $post       
 * @return      $ary_ym     
 */
function Make_Analysis_YM($post){

    // POSTデータを変数へ格納
    $sy     = $post["form_trade_ym_s"]["y"];
    $sm     = $post["form_trade_ym_s"]["m"];
    $span   = $post["form_trade_ym_e"];

    // 集計期間開始のPOSTがnullの場合は終了
    if ($sy == null || $sm == null) {
        return -1;
    }

    // 集計終了年月を算出
    $s_unix = mktime(0, 0, 0, $sm, 1, $sy);
    $e_unix = mktime(0, 0, 0, date("m", $s_unix) + $span, 0, date("Y", $s_unix));
    $s_date = date("Ym", $s_unix);
    $e_date = date("Ym", $e_unix);

    // 集計する月の年月を配列に格納
    for ($i = 1; $i < $span + 1; $i++) {
        $unix       = mktime(0, 0, 0, date("m", $s_unix) + $i, 0, date("Y", $s_unix));
        $ary_ym[]   = date("Y-m", $unix);
    }

    return $ary_ym;

}


/**
 * 本部用   自ショップコード、自ショップ名取得関数
 *
 * @author      fukuda
 * @version     1.0.0 (2007/11/03)
 *
 * @param       resource    $db_con         DBコネクション
 * @param       string      $form_data      POST情報
 * @param       integer     $shop_id        セッションのショップID
 * @param       array       $ary_ym         抽出期間の配列（YYYYMM形式）
 *
 * @return                  $res            クエリ実行結果
 *
 */
function Get_Shop_Data($db_con, $post_shop_id) {

    // クエリ作成
    $sql  = "SELECT \n";
    $sql .= "   client_cd1 || '-' || client_cd2, \n";
    $sql .= "   client_cname \n";
    $sql .= "FROM \n";
    $sql .= "   t_client \n";
    $sql .= "WHERE \n";
    $sql .= "   client_id = ".$post_shop_id." \n";
    $sql .= ";";

    // データ取得
    $res  = Db_Query($db_con, $sql);
    $ary_shop_data = pg_fetch_array($res);

    // データ整形
    $shop_data = "【".$ary_shop_data[0]." ".$ary_shop_data[1]."】";

    // かえします。
    return $shop_data;

}

