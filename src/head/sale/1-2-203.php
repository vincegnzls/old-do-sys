<?php
/**************************
//変更履歴
//　（20060904）分割回数を表示<watanabe-k>
//　（20060916）CSVのSQL変更<kaji>
//    ・売上金額合計をt_sale_d.sale_amountに変更
//    ・UNION ALLのあとにスペース、そのあとのSQLを結合するあたりにドットを入れた
//
//
/**************************/

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/10/31      08-005      ふ          表示しようとしている伝票が日更新実施済であれば専用モジュールへ遷移させる処理を追加
 *  2006/11/06      08-007      suzuki      データを削除する際に、日次更新されたか判定
 *  2006/11/06      08-020      suzuki      リロードによる伝票削除防止のため、伝票削除時に伝票作成日時と照らし合わせる
 *  2006/11/06      08-113      suzuki      CSV出力時の検索条件を有効にする
 *  2006/11/06      08-053      suzuki      マイナス金額を検索できるように修正
 *  2006/11/06      08-054      suzuki      表示順を得意先にしてCSV出力できるように修正
 *  2006/11/08      08-129      suzuki      得意先を略称表示
 *  2006/11/09      08-134      suzuki      税込金額検索結果をCSV出力できるように修正
 *  2006/11/09      08-135      suzuki      伝票番号検索できるように修正
 *  2006/12/07      ban_0048    suzuki      日付のゼロ埋め
 *  2007/01/25                  watanabe-k  ボタンの色変更
 *  2007/03/01                  morita-d    商品名は正式名称を表示するように変更 
 *  2007/03/01                  morita-d    CSVに販売区分を表示しないように修正 
 *  2007/03/05      作業項目12  ふくだ      掛・現金の合計を出力
 *  2007-04-05                  fukuda      検索条件復元処理追加
 *  2007-05-24                  watanabe-k  受注がない売上にも伝票番号が表示されるバグの修正
 *  2007-06-21                  fukuda      合計行の額を＋と−に分割
 *  2007/06/23      その他275   kajioka-h   CSVに原価単価、原価金額を追加
 *  2007/07/23                  watanabe-k  原価を表示するように修正
 *  2007/07/23                  watanabe-k  CSVに取引区分を考慮した金額を表示するように修正 
 *  2007-07-13                  fukuda      モジュール名を「売上照会」から「売上（割賦）照会」に変更
 *  2007-09-11                  watanabe-k  CSVに管理区分を出力するように修正
 *	2009-06-18		改修No.36	aizawa-m	CSVに商品コードを出力するよう修正
 *  2009/09/28      なし        hashimoto-y 取引区分から値引きを廃止
 *
 */

$page_title = "売上（割賦）照会";

// 環境設定ファイル
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/****************************/
// 検索条件復元関連
/****************************/
// 検索フォーム初期値配列
$ary_form_list = array(
    "form_display_num"      => "1",
    "form_output_type"      => "1",
    "form_client_branch"    => "",
    "form_attach_branch"    => "",
    "form_client"           => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_sale_staff"       => array("cd" => "", "select" => ""),
    "form_ware"             => "",
    "form_claim"            => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_multi_staff"      => "",
    "form_sale_day"         => array(
        "sy" => date("Y"),
        "sm" => date("m"),
        "sd" => "01",
        "ey" => date("Y"),
        "em" => date("m"),
        "ed" => date("t", mktime(0, 0, 0, date("m"), date("d"), date("Y")))
    ),
    "form_claim_day"        => array("sy" => "", "sm" => "", "sd" => "", "ey" => "", "em" => "", "ed" => ""),
    "form_renew"            => "1",
    "form_sale_amount"      => array("s" => "", "e" => ""),
    "form_slip_no"          => array("s" => "", "e" => ""),
    "form_aord_no"          => array("s" => "", "e" => ""),
    "form_goods"            => array("cd" => "", "name" => ""),
    "form_g_goods"          => "",
    "form_product"          => "",
    "form_g_product"        => "",
    "form_slip_type"        => "1",
    "form_slip_out"         => "1",
    "form_trade"            => "",
    "form_installment_day"  => array("sy" => "", "sm" => "", "sd" => "", "ey" => "", "em" => "", "ed" => ""),
    "form_shop"             => "2",
);

$ary_pass_list = array(
    "form_output_type"      => "1",
);

// 検索条件復元
Restore_Filter2($form, array("sale", "aord"), "form_show_button", $ary_form_list, $ary_pass_list);


/*****************************/
// 外部変数取得
/*****************************/
$shop_id    = $_SESSION["client_id"];


/****************************/
// 初期値セット
/****************************/
$form->setDefaults($ary_form_list);

$limit          = null;     // LIMIT
$offset         = "0";      // OFFSET
$total_count    = "0";      // 全件数
$page_count     = ($_POST["f_page1"] != null) ? $_POST["f_page1"] : "1";    // 表示ページ数


/*****************************/
// フォームパーツ定義
/*****************************/
/* 共通フォーム */
Search_Form_Sale_H($db_con, $form, $ary_form_list);

/* モジュール別フォーム */
// 日次更新
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "指定なし", "1");
$obj[]  =&  $form->createElement("radio", null, null, "未実施",   "2");
$obj[]  =&  $form->createElement("radio", null, null, "実施済",   "3");
$form->addGroup($obj, "form_renew", "");

// 税込金額
Addelement_Money_Range($form, "form_sale_amount", "税込金額");

// 伝票番号
Addelement_Slip_Range($form, "form_slip_no", "伝票番号");

// 受注番号
Addelement_Slip_Range($form, "form_aord_no", "受注番号");

// 商品
$obj    =   null;
$obj[]  =&  $form->createElement("text", "cd", "", "size=\"10\" maxLength=\"8\" class=\"ime_disabled\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", " ");
$obj[]  =&  $form->createElement("text", "name", "", "size=\"34\" maxLength=\"15\" $g_form_option");
$form->addGroup($obj, "form_goods", "", "");

// Ｍ区分
$item   =   null;
$item   =   Select_Get($db_con, "g_goods");
$form->addElement("select", "form_g_goods", "", $item, $g_form_option_select);

// 管理区分
$item   =   null;
$item   =   Select_Get($db_con, "product");
$form->addElement("select", "form_product", "", $item, $g_form_option_select);

// 商品分類
$item   =   null;
$item   =   Select_Get($db_con, "g_product");
$form->addElement("select", "form_g_product", "", $item, $g_form_option_select);

// 伝票発行
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "全て", "1");
$obj[]  =&  $form->createElement("radio", null, null, "有",   "2");
$obj[]  =&  $form->createElement("radio", null, null, "指定", "3");
$obj[]  =&  $form->createElement("radio", null, null, "無",   "4");
$form->addGroup($obj, "form_slip_type", "");

// 発行状況
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "全て",   "1");
$obj[]  =&  $form->createElement("radio", null, null, "未発行", "2");
$obj[]  =&  $form->createElement("radio", null, null, "発行済", "3");
$form->addGroup($obj, "form_slip_out", "");

// 取引区分
$item   =   null;
$item   =   Select_Get($db_con, "trade_sale");
#2009-09-28 hashimoto-y
#$form->addElement("select", "form_trade", "", $item, $g_form_option_select);

$trade_form=$form->addElement('select', 'form_trade', null, null, $g_form_option_select);

#値引きを廃止
$select_value_key = array_keys($item);
for($i = 0; $i < count($item); $i++){
    if( $select_value_key[$i] != 14 && $select_value_key[$i] != 64){
        $trade_form->addOption($item[$select_value_key[$i]], $select_value_key[$i]);
    }
}
#print_r($item);


// 割賦回収日
Addelement_Date_Range($form, "form_installment_day", "", "-");

// 抽出対象
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "東陽以外", "1");
$obj[]  =&  $form->createElement("radio", null, null, "全て",     "2");
$form->addGroup($obj, "form_shop", "");

// ソートリンク
$ary_sort_item = array(
    "sl_client_cd"      => "FC・取引先コード",
    "sl_client_name"    => "FC・取引先名",
    "sl_slip"           => "伝票番号",
    "sl_sale_day"       => "売上計上日",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_sale_day");

// 表示ボタン
$form->addElement("submit", "form_show_button", "表　示",
    "onClick=\"javascript: Which_Type('form_output_type', '1-2-207.php', '".$_SERVER["PHP_SELF"]."');\""
);

// クリアボタン
$form->addElement("button", "form_clear_button", "クリア", "onClick=\"javascript:location.href('".$_SERVER["PHP_SELF"]."');\"");

// 伝票発行ボタン
$form->addElement("button", "output_slip_button", "伝票発行", "
    onClick=\"javascript: document.dateForm.elements['hdn_button'].value = '伝票発行';
    Post_book_vote('./1-2-206.php', '".$_SERVER["PHP_SELF"]."');\"
");

// 再発行ボタン
$form->addElement("button", "output_re_slip_button", "再発行", "
    onClick=\"javascript: document.dateForm.elements['hdn_button'].value = '再発行';
    Post_book_vote('./1-2-206.php', '".$_SERVER["PHP_SELF"]."');\"
");

// ヘッダ部リンクボタン
$form->addElement("button", "203_button", "照会・変更", "$g_button_color onClick=\"location.href='".$_SERVER["PHP_SELF"]."'\"");
$form->addElement("button", "201_button", "入　力", "onClick=\"javascript: Referer('1-2-201.php');\"");

// 処理フラグ
$form->addElement("hidden", "del_id");                  // 削除売上ID
$form->addElement("hidden", "data_delete_flg");         // 削除ボタン押下判定フラグ
$form->addElement("hidden", "hdn_del_enter_date");      // 伝票作成日時
$form->addElement("hidden", "slip_button_flg");         // 伝票発行ボタン押下フラグ
$form->addElement("hidden", "re_slip_button_flg");      // 再発行ボタン押下フラグ
$form->addElement("hidden", "hdn_button");              // 発行ボタンの押下区別用


/*****************************/
// 削除リンク押下時
/*****************************/
if ($_POST["data_delete_flg"] == "true"){

    /*** 削除前調査 ***/
    // 選択されたデータの売上IDを取得
    $del_id = $_POST["del_id"];                     // 削除する売上ID
    // 選択された売上伝票の作成日次を取得
    $enter_date = $_POST["hdn_del_enter_date"];

    // POSTされた削除売上IDが正当か（伝票作成日時を元に）調べる
    $valid_flg = Update_check($db_con, "t_sale_h", "sale_id", $del_id, $enter_date);

    // 正当伝票なら削除処理実行
    if ($valid_flg == true){

        // 受注IDを抽出
        $sql        = "SELECT aord_id FROM t_sale_h WHERE sale_id = $del_id AND renew_flg = 'f';";
        $result     = Db_Query($db_con,$sql);
        $aord_id    = Get_Data($result);

        //日次更新されているか判定
        if ($aord_id != NULL){

             Db_Query($db_con, "BEGIN;");

            // 受注から起こしている場合は処理状況を未処理にもどしてあげる
            if($aord_id[0][0] != NULL){
                $sql    = "UPDATE t_aorder_h SET ps_stat = '1' WHERE aord_id = ".$aord_id[0][0].";";
                $result = Db_Query($db_con, $sql);
                if($result == false){
                    Db_Query($db_con, $sql);
                    exit;
                }
            }
            // 該当する行を削除SQL
            $sql = "DELETE FROM t_sale_h WHERE sale_id = $del_id;";

            $result     = Db_Query($db_con, $sql);
            if($result == false){
                Db_Query($db_con, "ROLLBACK");
                exit;
            }

            Db_Query($db_con, "COMMIT;");

        }else{

            $del_error_msg = "既に日次更新処理を行っている為削除できません。";

        }

    }

    // 削除データを初期化
    $del_data["del_id"]             = "";                                   
    $del_data["hdn_del_enter_date"] = "";                                    
    $del_data["data_delete_flg"]    = "";                                    
    $form->setConstants($del_data);

    $post_flg = true;

}


/***************************/
// 伝票発行・再発行ボタン押下時
/***************************/
if ($_POST["hdn_button"] != null){

    // 伝票出力ID配列初期値
    $sale_id = null;

    // 伝票発行ボタン押下時
    if ($_POST["slip_button_flg"] == "true"){
        $ary_check_id = $_POST["slip_check"];
    // 再発行ボタン押下時
    }else{
        $ary_check_id = $_POST["re_slip_check"];
    }

    // チェックされている伝票のIDをカンマ区切り（SQLで使用）
    if (count($ary_check_id) > 0){
        $i = 0;
        while ($check_num = each($ary_check_id)){
            // この添字のIDを使用する
            $check = $check_num[0];
            if ($check_num[1] != null && $check_num[1] != "f"){
                if ($i == 0){
                    $sale_id = $ary_check_id[$check];
                }else{
                    $sale_id .= ", ".$ary_check_id[$check];
                }
                $i++;
            }
        }
    }

    // チェック存在判定
    if ($sale_id != null){
        // チェックあり
        $check_flg = true;
    }else{
        // チェックなし
        $output_error = "発行する伝票が選択されていません。";
        $check_flg = false;
    }

    // チェックがあった場合
    // 発行状況と発行日を更新
    if ($check_flg == true){

        Db_Query($db_con, "BEGIN;");

        $sql  = "UPDATE \n";
        $sql .= "   t_sale_h \n";
        $sql .= "SET \n";
        $sql .= "   slip_flg = 't', \n";
        $sql .= "   slip_out_day = NOW() \n";
        $sql .= "WHERE \n";
        $sql .= "   t_sale_h.sale_id IN ($sale_id) \n";
        $sql .= "AND \n";
        $sql .= "   slip_flg ='f' \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if($res === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        Db_Query($db_con, "COMMIT;");

    }

    // hiddenをクリア
    $clear_hdn["hdn_button"] = "";
    $form->setConstants($clear_hdn);

    $post_flg = true;

}


/****************************/
// 表示ボタン押下処理
/****************************/
if ($_POST["form_show_button"] != null){

    // 日付POSTデータの0埋め
    $_POST["form_sale_day"]         = Str_Pad_Date($_POST["form_sale_day"]);
    $_POST["form_claim_day"]        = Str_Pad_Date($_POST["form_claim_day"]);
    $_POST["form_installment_day"]  = Str_Pad_Date($_POST["form_installment_day"]);

    /****************************/
    // エラーチェック
    /****************************/
    // ■売上担当者
    $err_msg = "売上担当者 は数値のみ入力可能です。";
    Err_Chk_Num($form, "form_sale_staff", $err_msg);

    // ■売上担当複数選択
    $err_msg = "売上担当複数選択 は数値と「,」のみ入力可能です。";
    Err_Chk_Delimited($form, "form_multi_staff", $err_msg);

    // ■売上計上日
    $err_msg = "売上計上日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_sale_day", $err_msg);

    // ■請求日
    $err_msg = "請求日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_claim_day", $err_msg);

    // ■税込金額
    $err_msg = "税込金額 は数値のみ入力可能です。";
    Err_Chk_Int($form, "form_sale_amount", $err_msg);

    // ■割賦回収日
    $err_msg = "割賦回収日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_installment_day", $err_msg);

    /****************************/
    // エラーチェック結果集計
    /****************************/
    // チェック適用
    $form->validate();
    // 結果をフラグに
    $err_flg = (count($form->_errors) > 0) ? true : false;

    $post_flg = ($err_flg != true) ? true : false;

}


/****************************/
// 1. 表示ボタン押下＋エラーなし時
// 2. ページ切り替え時
/****************************/
if (($_POST["form_show_button"] != null && $err_flg != true) || ($_POST != null && $_POST["form_show_button"] == null)){

    // 日付POSTデータの0埋め
    $_POST["form_sale_day"]         = Str_Pad_Date($_POST["form_sale_day"]);
    $_POST["form_claim_day"]        = Str_Pad_Date($_POST["form_claim_day"]);
    $_POST["form_installment_day"]  = Str_Pad_Date($_POST["form_installment_day"]);

    // 1. フォームの値を変数にセット
    // 2. SESSION（hidden用）の値（検索条件復元関数内でセット）を変数にセット
    // 一覧取得クエリ条件に使用
    $display_num        = $_POST["form_display_num"];
    $output_type        = $_POST["form_output_type"];
    $client_cd1         = $_POST["form_client"]["cd1"];
    $client_cd2         = $_POST["form_client"]["cd2"];
    $client_name        = $_POST["form_client"]["name"];
    $sale_staff_cd      = $_POST["form_sale_staff"]["cd"];
    $sale_staff_select  = $_POST["form_sale_staff"]["select"];
    $ware               = $_POST["form_ware"];
    $claim_cd1          = $_POST["form_claim"]["cd1"];
    $claim_cd2          = $_POST["form_claim"]["cd2"];
    $claim_name         = $_POST["form_claim"]["name"];
    $multi_staff        = $_POST["form_multi_staff"];
    $sale_day_sy        = $_POST["form_sale_day"]["sy"];
    $sale_day_sm        = $_POST["form_sale_day"]["sm"];
    $sale_day_sd        = $_POST["form_sale_day"]["sd"];
    $sale_day_ey        = $_POST["form_sale_day"]["ey"];
    $sale_day_em        = $_POST["form_sale_day"]["em"];
    $sale_day_ed        = $_POST["form_sale_day"]["ed"];
    $claim_day_sy       = $_POST["form_claim_day"]["sy"];
    $claim_day_sm       = $_POST["form_claim_day"]["sm"];
    $claim_day_sd       = $_POST["form_claim_day"]["sd"];
    $claim_day_ey       = $_POST["form_claim_day"]["ey"];
    $claim_day_em       = $_POST["form_claim_day"]["em"];
    $claim_day_ed       = $_POST["form_claim_day"]["ed"];
    $renew              = $_POST["form_renew"];
    $sale_amount_s      = $_POST["form_sale_amount"]["s"];
    $sale_amount_e      = $_POST["form_sale_amount"]["e"];
    $slip_no_s          = $_POST["form_slip_no"]["s"];
    $slip_no_e          = $_POST["form_slip_no"]["e"];
    $aord_no_s          = $_POST["form_aord_no"]["s"];
    $aord_no_e          = $_POST["form_aord_no"]["e"];
    $goods_cd           = $_POST["form_goods"]["cd"];
    $goods_name         = $_POST["form_goods"]["name"];
    $g_goods            = $_POST["form_g_goods"];
    $product            = $_POST["form_product"];
    $g_product          = $_POST["form_g_product"];
    $slip_type          = $_POST["form_slip_type"];
    $slip_out           = $_POST["form_slip_out"];
    $trade              = $_POST["form_trade"];
    $installment_day_sy = $_POST["form_installment_day"]["sy"];
    $installment_day_sm = $_POST["form_installment_day"]["sm"];
    $installment_day_sd = $_POST["form_installment_day"]["sd"];
    $installment_day_ey = $_POST["form_installment_day"]["ey"];
    $installment_day_em = $_POST["form_installment_day"]["em"];
    $installment_day_ed = $_POST["form_installment_day"]["ed"];
    $shop               = $_POST["form_shop"];

    $post_flg = true;

}


/****************************/
// 一覧データ取得条件作成
/****************************/
if ($post_flg == true && $err_flg != true){

    /* WHERE */
    $sql = null;

    // FC・取引先コード1
    $sql .= ($client_cd1 != null) ? "AND t_sale_h.client_cd1 LIKE '$client_cd1%' \n" : null;
    // FC・取引先コード2
    $sql .= ($client_cd2 != null) ? "AND t_sale_h.client_cd2 LIKE '$client_cd2%' \n" : null;
    // FC・取引先名
    if ($client_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_sale_h.client_name  LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_sale_h.client_name2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_sale_h.client_cname LIKE '%$client_name%' \n";
        $sql .= "   ) \n";
    }
    // 売上担当者コード
    $sql .= ($sale_staff_cd != null) ? "AND t_staff.charge_cd = '$sale_staff_cd' \n" : null;
    // 売上担当者セレクト
    $sql .= ($sale_staff_select != null) ? "AND t_staff.staff_id = $sale_staff_select \n" : null;
    // 倉庫
    $sql .= ($ware != null) ? "AND t_sale_h.ware_id = $ware \n" : null;
    // 請求先コード１   
    $sql .= ($claim_cd1 != null) ? "AND t_client_claim.client_cd1 LIKE '$claim_cd1%' \n" : null;
    // 請求先コード２
    $sql .= ($claim_cd2 != null) ? "AND t_client_claim.client_cd2 LIKE '$claim_cd2%' \n" : null;
    // 請求先名
    if ($claim_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_client_claim.client_name  LIKE '%$claim_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client_claim.client_name2 LIKE '%$claim_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client_claim.client_cname LIKE '%$claim_name%' \n";
        $sql .= "   ) \n";
    }
    // 売上担当複数選択
    if ($multi_staff != null){
        $ary_multi_staff = explode(",", $multi_staff);
        $sql .= "AND \n";
        $sql .= "   t_staff.charge_cd IN (";
        foreach ($ary_multi_staff as $key => $value){
            $sql .= "'".trim($value)."'";
            $sql .= ($key+1 < count($ary_multi_staff)) ? ", " : ") \n";
        }
    }
    // 売上計上日（開始）
    $sale_day_s  = $sale_day_sy."-".$sale_day_sm."-".$sale_day_sd;
    $sql .= ($sale_day_s != "--") ? "AND '$sale_day_s' <= t_sale_h.sale_day \n" : null;
    // 売上計上日（終了）
    $sale_day_e  = $sale_day_ey."-".$sale_day_em."-".$sale_day_ed;
    $sql .= ($sale_day_e != "--") ? "AND t_sale_h.sale_day <= '$sale_day_e' \n" : null;
    // 請求日（開始）
    $claim_day_s  = $claim_day_sy."-".$claim_day_sm."-".$claim_day_sd;
    $sql .= ($claim_day_s != "--") ? "AND t_sale_h.claim_day >= '$claim_day_s' \n" : null; 
    // 請求日（終了）
    $claim_day_e  = $claim_day_ey."-".$claim_day_em."-".$claim_day_ed;
    $sql .= ($claim_day_e != "--") ? "AND t_sale_h.claim_day <= '$claim_day_e' \n" : null; 
    // 日次更新
    if ($renew == "2"){
        $sql .= "AND t_sale_h.renew_flg = 'f' \n";
    }else
    if ($renew == "3"){
        $sql .= "AND t_sale_h.renew_flg = 't' \n";
    }
    // 伝票番号（開始）
    $sql .= ($slip_no_s != null) ? "AND t_sale_h.sale_no >= '".str_pad($slip_no_s, 8, 0, STR_PAD_LEFT)."' \n" : null; 
    // 伝票番号（終了）
    $sql .= ($slip_no_e != null) ? "AND t_sale_h.sale_no <= '".str_pad($slip_no_e, 8, 0, STR_PAD_LEFT)."' \n" : null; 
    // 受注番号（開始）
    $sql .= ($aord_no_s != null) ? "AND t_aorder_h.ord_no >= '".str_pad($aord_no_s, 8, 0, STR_PAD_LEFT)."' \n" : null; 
    // 受注番号（終了）
    $sql .= ($aord_no_e != null) ? "AND t_aorder_h.ord_no <= '".str_pad($aord_no_e, 8, 0, STR_PAD_LEFT)."' \n" : null; 
    // 商品コード
    if ($goods_cd != null){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_sale_h.sale_id FROM t_sale_h \n";
        $sql .= "       INNER JOIN t_sale_d ON  t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "                           AND t_sale_d.goods_cd LIKE '$goods_cd%' \n";
        $sql .= "       GROUP BY t_sale_h.sale_id \n";
        $sql .= "   ) \n";
    }
    // 商品名
    if ($goods_name != null){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_sale_h.sale_id FROM t_sale_h \n";
        $sql .= "       INNER JOIN t_sale_d ON  t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "                           AND t_sale_d.official_goods_name LIKE '%$goods_name%' \n";
        $sql .= "       GROUP BY t_sale_h.sale_id \n";
        $sql .= "   ) \n";
    }
    // Ｍ区分
    if ($g_goods != null){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_sale_h.sale_id FROM t_sale_h \n";
        $sql .= "       INNER JOIN t_sale_d ON  t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "       INNER JOIN t_goods ON  t_sale_d.goods_id = t_goods.goods_id \n";
        $sql .= "                          AND t_goods.g_goods_id = $g_goods \n";
        $sql .= "       GROUP BY t_sale_h.sale_id \n";
        $sql .= "   ) \n";
    }
    // 管理区分
    if ($product != null){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_sale_h.sale_id FROM t_sale_h \n";
        $sql .= "       INNER JOIN t_sale_d ON  t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "       INNER JOIN t_goods ON  t_sale_d.goods_id = t_goods.goods_id \n";
        $sql .= "                          AND t_goods.product_id = $product \n";
        $sql .= "       GROUP BY t_sale_h.sale_id \n";
        $sql .= "   ) \n";
    }
    // 商品分類
    if ($g_product != null){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_sale_h.sale_id FROM t_sale_h \n";
        $sql .= "       INNER JOIN t_sale_d ON t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "       INNER JOIN t_goods ON  t_sale_d.goods_id = t_goods.goods_id \n";
        $sql .= "                          AND t_goods.g_product_id = $g_product \n";
        $sql .= "       GROUP BY t_sale_h.sale_id \n";
        $sql .= "   ) \n";
    }
/*
    // 商品コード
    $sql .= ($goods_cd != null) ? "AND t_sale_d.goods_cd LIKE '$goods_cd%' " : null;
    // 商品名
    $sql .= ($goods_name != null) ? "AND t_sale_d.official_goods_name LIKE '%$goods_name%' \n" : null;
    // Ｍ区分
    $sql .= ($g_goods != null) ? "AND t_g_goods.g_goods_id = $g_goods \n" : null;
    // 管理区分
    $sql .= ($product != null) ? "AND t_product.product_id = $product \n" : null;
    // 商品分類
    $sql .= ($g_product != null) ? "AND t_g_product.g_product_id = $g_product \n" : null;
*/
    // 伝票発行
    if ($slip_type == "2"){
        $sql .= "AND t_sale_h.slip_out = '1' \n";
    }else
    if ($slip_type == "3"){
        $sql .= "AND t_sale_h.slip_out = '2' \n";
    }else
    if ($slip_type == "4"){
        $sql .= "AND t_sale_h.slip_out = '3' \n";
    }
    // 発行状況
    if ($slip_out == "2"){
        $sql .= "AND t_sale_h.slip_flg = 'f' \n";
    }else
    if ($slip_out == "3"){
        $sql .= "AND t_sale_h.slip_flg = 't' \n";
    }
    // 取引区分
    $sql .= ($trade != null) ? "AND t_sale_h.trade_id = $trade \n" : null;
    // 割賦回収日（開始）
    // 割賦回収日（終了）
    $installment_day_s  = $installment_day_sy."-".$installment_day_sm."-".$installment_day_sd;
    $installment_day_e  = $installment_day_ey."-".$installment_day_em."-".$installment_day_ed;
    if ($installment_day_s != "--" || $installment_day_e != "--"){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           sale_id \n";
        $sql .= "       FROM \n";
        $sql .= "           t_installment_sales \n";
        $sql .= "       WHERE \n";
        $sql .= "           sale_id IS NOT NULL \n";
        $sql .= ($installment_day_s != "--") ? "       AND collect_day >= '$installment_day_s' \n" : null;
        $sql .= ($installment_day_e != "--") ? "       AND collect_day <= '$installment_day_e' \n" : null;
        $sql .= "       GROUP BY \n";
        $sql .= "           sale_id \n";
        $sql .= "   ) \n";
    }

    // 変数詰め替え（CSV出力用）
    $csv_where_sql = $sql;

    // 税込金額（開始）
    if ($sale_amount_s != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           CASE \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) *  1 \n";
        $sql .= "               ELSE (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "           END \n";
        $sql .= "   ) \n";
        $sql .= "   >= $sale_amount_s \n";
    }
    // 税込金額（終了）
    if ($sale_amount_e != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           CASE \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) *  1 \n";
        $sql .= "               ELSE (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "           END \n";
        $sql .= "   ) \n";
        $sql .= "   <= $sale_amount_e \n";
    }
    // 抽出対象
    if ($shop == "1"){
        $sql .= "AND t_sale_h.client_id != 93 \n";
    }

    // 変数詰め替え（画面出力用）
    $disp_where_sql = $sql;


    /* HAVING */

    $sql = null;

    // 税込金額（開始） 
    if ($sale_amount_s != null){
        $sql  = "HAVING \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           SUM( \n";
        $sql .= "               CASE \n";
        $sql .= "                   WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "                   THEN (t_sale_h.net_amount + t_sale_h.tax_amount) *  1 \n";
        $sql .= "                   ELSE (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "               END \n";
        $sql .= "           ) \n";
        $sql .= "   ) \n";
        $sql .= "   >= $sale_amount_s \n";
    }
    // 税込金額（終了）
    if ($sale_amount_e != null){
        $sql .= ($sale_amount_s == null) ? "HAVING \n" : "AND \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           SUM( \n";
        $sql .= "               CASE \n";
        $sql .= "                   WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "                   THEN (t_sale_h.net_amount + t_sale_h.tax_amount) *  1 \n";
        $sql .= "                   ELSE (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "               END \n";
        $sql .= "           ) \n";
        $sql .= "   ) \n";
        $sql .= "   <= $sale_amount_e \n";
    }

    // 変数詰め替え（CSV用）
    $csv_having_sql = $sql;


    /* ORDER BY */
    $sql = null;

    // 画面出力
    // CSV出力
    if ($output_type == "1" || $output_type == "3"){

        switch ($_POST["hdn_sort_col"]){
            // 得意先コード
            case "sl_client_cd":
                $sql .= "   t_sale_h.client_cd1, \n";
                $sql .= "   t_sale_h.client_cd2, \n";
                $sql .= "   t_sale_h.sale_day, \n";
                $sql .= "   t_sale_h.sale_no \n";
                break;
            // 得意先名
            case "sl_client_name":
                $sql .= "   t_sale_h.client_cname, \n";
                $sql .= "   t_sale_h.sale_day, \n";
                $sql .= "   t_sale_h.sale_no, \n";
                $sql .= "   t_sale_h.client_cd1, \n";
                $sql .= "   t_sale_h.client_cd2 \n";
                break;
            // 伝票番号
            case "sl_slip":
                $sql .= "   t_sale_h.sale_no \n";
                break;
            // 売上計上日
            case "sl_sale_day":
                $sql .= "   t_sale_h.sale_day, \n";
                $sql .= "   t_sale_h.sale_no, \n";
                $sql .= "   t_sale_h.client_cd1, \n";
                $sql .= "   t_sale_h.client_cd2 \n";
                break;
        }

    }

    // 変数詰め替え
    $order_sql = $sql;

}


/****************************/
// 一覧データ取得
/****************************/
if ($post_flg == true && $err_flg != true){

    // 画面出力時
    if ($output_type == "1"){

        $sql  = "SELECT \n";
        $sql .= "   t_sale_h.sale_id, \n";              // 売上ID
        $sql .= "   t_sale_h.sale_no, \n";              // 伝票番号
        $sql .= "   t_sale_h.client_cd1, \n";           // FC・取引先コード１
        $sql .= "   t_sale_h.client_cd2, \n";           // FC・取引先コード２
        $sql .= "   t_sale_h.client_cname, \n";         // FC・取引先名
        $sql .= "   t_sale_h.sale_day, \n";             // 売上計上日
        $sql .= "   t_sale_h.trade_id, \n";             // 取引区分ID
        $sql .= "   t_trade.trade_name, \n";            // 取引区分
        $sql .= "   ( \n";
        $sql .= "       CASE \n";
        $sql .= "           WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "           THEN (t_sale_h.net_amount) *  1 \n";
        $sql .= "           ELSE (t_sale_h.net_amount) * -1 \n";
        $sql .= "       END \n";
        $sql .= "   ) AS notax_amount, \n";             // 税抜金額
        $sql .= "   ( \n";
        $sql .= "       CASE \n";
        $sql .= "           WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "           THEN (t_sale_h.tax_amount) *  1 \n";
        $sql .= "           ELSE (t_sale_h.tax_amount) * -1 \n";
        $sql .= "       END \n";
        $sql .= "   ) AS tax_amount, \n";               // 消費税額
        $sql .= "   ( \n";
        $sql .= "       CASE \n";
        $sql .= "           WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "           THEN (t_sale_h.net_amount + t_sale_h.tax_amount) *  1 \n";
        $sql .= "           ELSE (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "       END \n";
        $sql .= "   ) AS sale_amount, \n";              // 税込金額
        $sql .= "   t_aorder_h.aord_id, \n";            // 受注ID
        $sql .= "   t_aorder_h.ord_no, \n";             // 受注番号
        $sql .= "   t_sale_h.total_split_num, \n";      // 分割回数
        $sql .= "   t_sale_h.enter_day, \n";            // 作成日時
        $sql .= "   t_sale_h.renew_flg, \n";            // 日次更新フラグ
        $sql .= "   to_char(t_sale_h.renew_day, 'yyyy-mm-dd') AS renew_day, \n";

        $sql .= "   ( \n";
        $sql .= "       CASE \n";
        $sql .= "           WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "           THEN (t_sale_h.cost_amount) * 1 \n";
        $sql .= "           ELSE (t_sale_h.cost_amount) * -1 \n";
        $sql .= "       END \n";
        $sql .= "   ) AS cost_amount, ";
                                                        // 日次更新日
        $sql .= "   t_sale_h.slip_out, \n";             // 伝票形式
        $sql .= "   t_sale_h.slip_flg, \n";             // 発行状況
        $sql .= "   t_sale_h.slip_out_day \n";          // 伝票発行日
        $sql .= "FROM \n";
        $sql .= "   t_sale_h \n";
        $sql .= "   LEFT  JOIN t_aorder_h                 ON t_sale_h.aord_id = t_aorder_h.aord_id \n";
        $sql .= "   LEFT  JOIN t_client AS t_client_claim ON t_sale_h.claim_id = t_client_claim.client_id \n";
        $sql .= "   LEFT  JOIN t_staff                    ON t_sale_h.c_staff_id = t_staff.staff_id \n";
        $sql .= "   LEFT  JOIN t_trade                    ON t_sale_h.trade_id = t_trade.trade_id \n";
/*
        if ($goods_cd != null || $goods_name != null || $g_goods != null || $product != null || $g_product != null){
        $sql .= "   INNER JOIN t_sale_d     ON t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "   INNER JOIN t_goods      ON t_sale_d.goods_id = t_goods.goods_id \n";
        }
        if ($g_goods != null){
        $sql .= "   INNER JOIN t_g_goods    ON t_goods.g_goods_id = t_g_goods.g_goods_id \n";
        }
        if ($product != null){
        $sql .= "   INNER JOIN t_product    ON t_goods.product_id = t_product.product_id \n";
        }
        if ($g_product != null){
        $sql .= "   INNER JOIN t_g_product  ON t_goods.g_product_id = t_g_product.g_product_id \n";
        }
*/
        $sql .= "WHERE \n";
        $sql .= "   t_sale_h.shop_id = 1 \n";
        $sql .= $disp_where_sql;
        $sql .= "ORDER BY \n";
        $sql .= $order_sql;

    // CSV出力時
    }elseif ($output_type == "3"){

        $sql  = "SELECT \n";
        $sql .= "   t_sale_h.client_cd1 || '-' || t_sale_h.client_cd2, \n";
        $sql .= "   t_sale_h.client_cname, \n";
        $sql .= "   t_sale_h.sale_no, \n";
        $sql .= "   t_sale_h.sale_day, \n";
        $sql .= "   t_sale_h.claim_day, \n";
        $sql .= "   t_sale_h.trade_name, \n";
        $sql .= "   t_sale_h.direct_cname, \n";
        $sql .= "   t_sale_h.trans_name, \n";
        $sql .= "   t_sale_h.ware_name, \n";
        $sql .= "   t_sale_h.c_staff_name, \n";
        $sql .= "   t_product.product_name, \n";
		//-- 2009/06/18 改修No.36 追加
		// 商品コード
		$sql .= "	t_sale_d.goods_cd, \n";
		//--
        $sql .= "   t_sale_d.official_goods_name, \n";

        $sql .= "   CASE \n";
        $sql .= "       WHEN t_sale_h.trade_id IN (13, 63) THEN t_sale_d.num * -1 \n";
        $sql .= "       ELSE t_sale_d.num \n";
        $sql .= "   END AS num, \n";

        $sql .= "   CASE \n";
        $sql .= "       WHEN t_sale_h.trade_id IN (14, 64) THEN t_sale_d.cost_price * -1 \n";
        $sql .= "       ELSE t_sale_d.cost_price \n";
        $sql .= "   END AS cost_price, \n";

        $sql .= "   CASE \n";
        $sql .= "       WHEN t_sale_h.trade_id IN (14,13,64,63) THEN t_sale_d.cost_amount * -1 \n";
        $sql .= "       ELSE t_sale_d.cost_amount \n";
        $sql .= "   END AS cost_amount, \n";

        $sql .= "   CASE \n";
        $sql .= "       WHEN t_sale_h.trade_id IN (14,64) THEN t_sale_d.sale_price * -1\n";
        $sql .= "       ELSE t_sale_d.sale_price \n";
        $sql .= "   END AS sale_price, \n";

        $sql .= "   CASE \n";
        $sql .= "       WHEN t_sale_h.trade_id IN (14,13,64,63) THEN t_sale_d.sale_amount * -1 \n";
        $sql .= "       ELSE t_sale_d.sale_amount \n";
        $sql .= "   END AS sale_amount, \n";


        $sql .= "   CASE t_sale_h.total_split_num \n";
        $sql .= "       WHEN 1 THEN NULL \n";
        $sql .= "       ELSE t_sale_h.total_split_num \n";
        $sql .= "   END AS total_split_num, \n";
        $sql .= "   t_sale_h.slip_out_day, \n";
        $sql .= "   t_sale_h.renew_day \n";
        $sql .= "FROM \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           t_sale_h.sale_id, \n";
        $sql .= "           t_sale_h.client_cd1, \n";
        $sql .= "           t_sale_h.client_cd2, \n";
        $sql .= "           t_sale_h.client_cname, \n";
        $sql .= "           t_sale_h.sale_no, \n";
        $sql .= "           t_sale_h.sale_day, \n";
        $sql .= "           t_sale_h.claim_day, \n";
        $sql .= "           t_trade.trade_id, \n";
        $sql .= "           t_trade.trade_name, \n";
        $sql .= "           t_sale_h.direct_cname, \n";
        $sql .= "           t_sale_h.trans_name, \n";
        $sql .= "           t_sale_h.ware_name, \n";
        $sql .= "           t_sale_h.c_staff_name, \n";
        $sql .= "           sum(t_sale_h.net_amount + t_sale_h.tax_amount) AS total_amount, \n";
        $sql .= "           t_sale_h.total_split_num, \n";
        $sql .= "           t_sale_h.slip_out_day, \n";
        $sql .= "           to_char(t_sale_h.renew_day, 'yyyy-mm-dd') AS renew_day \n";
        $sql .= "       FROM \n";
        $sql .= "           t_sale_h \n";
        $sql .= "           LEFT  JOIN t_aorder_h                 ON t_sale_h.aord_id = t_aorder_h.aord_id \n";
        $sql .= "           LEFT  JOIN t_client AS t_client_claim ON t_sale_h.claim_id = t_client_claim.client_id \n";
        $sql .= "           LEFT  JOIN t_staff                    ON t_sale_h.c_staff_id = t_staff.staff_id \n";
        $sql .= "           LEFT  JOIN t_trade                    ON t_sale_h.trade_id = t_trade.trade_id \n";
        $sql .= "           INNER JOIN t_sale_d                   ON t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "       WHERE \n";
        $sql .= "           t_sale_h.shop_id = 1 \n";
        $sql .= $csv_where_sql;
        $sql .= "       GROUP BY \n";
        $sql .= "           t_sale_h.sale_id, \n";
        $sql .= "           t_sale_h.sale_day, \n";
        $sql .= "           t_sale_h.sale_no, \n";
        $sql .= "           t_sale_h.client_cname, \n";
        $sql .= "           t_sale_h.client_cd1, \n";
        $sql .= "           t_sale_h.client_cd2, \n";
        $sql .= "           t_sale_h.total_split_num, \n";
        $sql .= "           t_aorder_h.ord_no, \n";
        $sql .= "           t_aorder_h.aord_id, \n";
        $sql .= "           t_sale_h.renew_flg, \n";
        $sql .= "           t_sale_h.renew_day, \n";
        $sql .= "           t_sale_h.claim_day, \n";
        $sql .= "           t_trade.trade_id, \n";
        $sql .= "           t_trade.trade_name, \n";
        $sql .= "           t_sale_h.direct_cname, \n";
        $sql .= "           t_sale_h.trans_name, \n";
        $sql .= "           t_sale_h.ware_name, \n";
        $sql .= "           t_sale_h.c_staff_name, \n";
        $sql .= "           t_sale_h.slip_out_day \n";
        $sql .= $csv_having_sql;
        $sql .= "   ) \n";
        $sql .= "   AS t_sale_h \n";
        $sql .= "   INNER JOIN t_sale_d ON t_sale_h.sale_id = t_sale_d.sale_id \n";
        $sql .= "   INNER JOIN t_goods                    ON t_sale_d.goods_id = t_goods.goods_id \n";
        $sql .= "   INNER JOIN t_product                  ON t_goods.product_id = t_product.product_id \n";
        $sql .= "ORDER BY \n";
        $sql .= $order_sql;

    }

    // 全件数取得
    $total_result   = Db_Query($db_con, $sql.";");
    $total_count    = pg_num_rows($total_result);

    // 表示件数
    switch ($display_num){
        case "1":
            $limit = $total_count;
            break;
        case "2":
            $limit = "100";
            break;
    }

    // 取得開始位置
    $offset = ($page_count != null) ? ($page_count - 1) * $limit : "0";

    // 行削除でページに表示するレコードが無くなる場合の対処
    if($page_count != null){
        // 行削除でtotal_countとoffsetの関係が崩れた場合
        if ($total_count <= $offset){
            // オフセットを選択件前に
            $offset     = $offset - $limit;
            // 表示するページを1ページ前に（一気に2ページ分削除されていた場合などには対応してないです）
            $page_count = $page_count - 1;
            // 選択件数以下時はページ遷移を出力させない(nullにする)
            $page_count = ($total_count <= $display_num) ? null : $page_count;
        }   
    }else{ 
        $offset = 0;
    }

    // LIMITSQL
    $sql .= ($output_type == "1") ? "LIMIT $limit OFFSET $offset " : null;

    $result     = Db_Query($db_con, $sql.";");
    $data_num   = pg_num_rows($result);
    $page_data  = Get_Data($result, 2, "ASSOC");

}


/****************************/
// CSV出力
/****************************/
if ($post_flg == true && $err_flg != true && $output_type == "3"){

    $page_data  = Get_Data($total_result, 2);

    // ファイル名
    $csv_file_name = "売上明細一覧.csv";

    //CSVヘッダ作成
    $csv_header = array(
        "得意先コード",
        "得意先名",
        "伝票番号",
        "売上計上日",
        "請求日",
        "取引区分",
        "直送先名",
        "運送業者名",
        "出荷倉庫名",
        "売上担当者",
        "管理区分",
		//-- 2009/06/18 改修No.36 追加
		"商品コード",
		//--
        "商品名",
        "数量",
        "原価単価",
        "原価金額",
        "売上単価",
        "売上金額",
        "分割回数",
        "売上伝票発効日",
        "日次更新日",
    );

    //CSVデータ取得
    for($i=0;$i<count($page_data);$i++){
        $sale_data[$i][0]  = $page_data[$i][0];
        $sale_data[$i][1]  = $page_data[$i][1];
        $sale_data[$i][2]  = $page_data[$i][2];
        $sale_data[$i][3]  = $page_data[$i][3];
        $sale_data[$i][4]  = $page_data[$i][4];
        $sale_data[$i][5]  = $page_data[$i][5];
        $sale_data[$i][6]  = $page_data[$i][6];
        $sale_data[$i][7]  = $page_data[$i][7];
        $sale_data[$i][8]  = $page_data[$i][8];
        $sale_data[$i][9]  = $page_data[$i][9];
        $sale_data[$i][10] = $page_data[$i][10];
        $sale_data[$i][11] = $page_data[$i][11];
        $sale_data[$i][12] = $page_data[$i][12];
        $sale_data[$i][13] = $page_data[$i][13];
        $sale_data[$i][14] = $page_data[$i][14];
        $sale_data[$i][15] = $page_data[$i][15];
        $sale_data[$i][16] = $page_data[$i][16];
        $sale_data[$i][17] = $page_data[$i][17];
        $sale_data[$i][18] = $page_data[$i][18];
        $sale_data[$i][19] = $page_data[$i][19];
		//-- 2009/06/18 改修No.36 追加
        $sale_data[$i][20] = $page_data[$i][20];
		//-- 
    }

    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($sale_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;

}


/****************************/
// HTML用関数
/****************************/
function Number_Format_Color($num){
    return ($num < 0) ? "<span style=\"color: #ff0000;\">".number_format($num)."</span>" : number_format($num);
}


/****************************/
// HTML作成（一覧部）
/****************************/
if ($post_flg == true && $err_flg != true){

    /* ページ分け */
    $html_page  = Html_Page2($total_count, $page_count, 1, $limit);
    $html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

    $html_l = null;

    // 取得データでループ
    if (count($page_data) > 0){
        foreach ($page_data as $key => $value){

            /* 前処理 */
            // 行色css
            $row_color      = (bcmod($key, 2) == 0) ? "Result1" : "Result2";
            // No.
            $no             = $page_count * $limit - $limit + 1 + $key;
            // FC・取引先
            $client         = $value["client_cd1"]."-".$value["client_cd2"]."<br>".htmlspecialchars($value["client_cname"])."<br>";
            // 伝票番号リンク
            if ($value["renew_flg"] == "t"){
                $slip_no_link   = "<a href=\"./1-2-205.php?sale_id=".$value["sale_id"]."&slip_flg=true\">".$value["sale_no"]."</a>";
            }else{
                $slip_no_link   = "<a href=\"./1-2-201.php?sale_id=".$value["sale_id"]."\">".$value["sale_no"]."</a>";
            }
            // 受注番号リンク
            if ($value["aord_id"] != null){
                $aord_no_link   = "<a href=\"./1-2-108.php?aord_id=".$value["aord_id"]."\">".$value["ord_no"]."</a>";
            }else{
                $aord_no_link   = null;
            }
            // 分割回数リンク
            if ($value["trade_id"] == "15"){
                $split_link     = "<a href=\"./1-2-208.php?sale_id=".$value["sale_id"]."&division_flg=true\">".$value["total_split_num"]."回</a>";
            }else{
                $split_link     = null;
            }
            // 削除リンク
            if ($value["renew_flg"] == "f" && $disabled == null){
                $del_link       = "<a href=\"#\" onClick=\"Order_Delete";
                $del_link      .= "('data_delete_flg', 'del_id', ".$value["sale_id"].", 'hdn_del_enter_date', '".$value["enter_day"]."');";
                $del_link      .= "\">削除</a>";
            }else{
                $del_link       = null;
            }
            /*
            メモ
                １．以下条件全てに該当する伝票のみ伝票発行可能
                    ・伝票発行"有"（旧通常伝票）
                ２．上記条件かつ発行状況が"発行済"の場合は「再発行」で発行
                ３．伝票発行"無"は伝票発行しない
            */
            // 売上伝票発行チェック
            // 伝票発行"有"＋未発行
            if ($value["slip_out"] == "1" && $value["slip_flg"] == "f"){
                $form->addElement("advcheckbox", "slip_check[$key]", null, null, null, array(null, $value["sale_id"]));
                $slip_print[$key]   = "print";
                $slip_data[$key]    = $value["sale_id"];
                $ckear_check["slip_check[$key]"] = "";
            }else
            // 伝票発行"有"＋発行済
            if ($value["slip_out"] == "1" && $value["slip_flg"] == "t"){
                $form->addElement("static", "output_day[$key]", "", $value["slip_out_day"]);
                $slip_print[$key]   = "output_day";
            }else{
            }
            // 再発行チェック
            // 伝票発行"有"＋未発行
            if ($value["slip_out"] == "1" && $value["slip_flg"] == "t"){
                $form->addElement("advcheckbox", "re_slip_check[$key]", null, null, null, array(null, $value["sale_id"]));
                $re_slip_print[$key]= "print";
                $re_slip_data[$key] = $value["sale_id"];
                $ckear_check["re_slip_check[$key]"] = "";
            }

            /* 一覧html作成 */
            $html_l .= "<tr class=\"$row_color\">\n";
            $html_l .= "    <td align=\"right\">$no</td>\n";
            $html_l .= "    <td>$client</td>\n";
            $html_l .= "    <td align=\"center\">$slip_no_link</td>\n";
            $html_l .= "    <td align=\"center\">".$value["sale_day"]."</td>\n";
            $html_l .= "    <td align=\"center\">".$value["trade_name"]."</td>\n";
            $html_l .= "    <td align=\"right\">".Number_Format_Color($value["cost_amount"])."</td>\n";
            $html_l .= "    <td align=\"right\">".Number_Format_Color($value["notax_amount"])."</td>\n";
            $html_l .= "    <td align=\"right\">".Number_Format_Color($value["tax_amount"])."</td>\n";
            $html_l .= "    <td align=\"right\">".Number_Format_Color($value["sale_amount"])."</td>\n";
            $html_l .= "    <td align=\"center\">$aord_no_link</td>\n";
            $html_l .= "    <td align=\"center\">$split_link</td>\n";
            $html_l .= "    <td align=\"center\">$del_link</td>\n";
            $html_l .= "    <td align=\"center\">".$value["renew_day"]."</td>\n";
            if ($slip_print[$key] == "print"){
            $html_l .= "    <td align=\"center\">".$form->_elements[$form->_elementIndex["slip_check[$key]"]]->toHtml()."</td>\n";
            }else
            if ($slip_print[$key] == "output_day"){
            $html_l .= "    <td align=\"center\">".$form->_elements[$form->_elementIndex["output_day[$key]"]]->toHtml()."</td>\n";
            }else{
            $html_l .= "    <td></td>\n";
            }
            if ($re_slip_print[$key] == "print"){
            $html_l .= "    <td align=\"center\">".$form->_elements[$form->_elementIndex["re_slip_check[$key]"]]->toHtml()."</td>\n";
            }else{
            $html_l .= "    <td></td>\n";
            }
            $html_l .= "</tr>\n";

            /* 合計テーブル用に金額加算 */
            switch ($value["trade_id"]){
                case "11":
                case "15":
                case "13":
                case "14":
                    $kake_notax_amount      += $value["notax_amount"];
                    $kake_tax_amount        += $value["tax_amount"];
                    $kake_ontax_amount      += $value["sale_amount"];
                    break;
                case "61":
                case "63":
                case "64":
                    $genkin_notax_amount    += $value["notax_amount"];
                    $genkin_tax_amount      += $value["tax_amount"];
                    $genkin_ontax_amount    += $value["sale_amount"];
                    break;
            }

            /* 合計行用に金額加算 */
            switch ($value["trade_id"]){
                case "11":
                case "15":
                    $gross_notax_amount     += $value["notax_amount"];
                    $gross_tax_amount       += $value["tax_amount"];
                    $gross_ontax_amount     += $value["sale_amount"];
                    $gross_cost_amount      += $value["cost_amount"];
                    break;
                case "13":
                case "14":
                    $minus_notax_amount     += $value["notax_amount"];
                    $minus_tax_amount       += $value["tax_amount"];
                    $minus_ontax_amount     += $value["sale_amount"];
                    $minus_cost_amount      += $value["cost_amount"];
                    break;
                case "61":
                    $gross_notax_amount     += $value["notax_amount"];
                    $gross_tax_amount       += $value["tax_amount"];
                    $gross_ontax_amount     += $value["sale_amount"];
                    $gross_cost_amount      += $value["cost_amount"];
                    break;
                case "63":
                case "64":
                    $minus_notax_amount     += $value["notax_amount"];
                    $minus_tax_amount       += $value["tax_amount"];
                    $minus_ontax_amount     += $value["sale_amount"];
                    $minus_cost_amount      += $value["cost_amount"];
                    break;
            }

        }
    }

    // チェックボックスのチェックをクリア
    $clear_check["slip_check_all"]      = "";
    $clear_check["re_slip_check_all"]   = "";
    $form->setConstants($clear_check);

    // 掛と現金の合計算出
    $notax_amount   = $kake_notax_amount + $genkin_notax_amount;
    $tax_amount     = $kake_tax_amount   + $genkin_tax_amount;
    $ontax_amount   = $kake_ontax_amount + $genkin_ontax_amount;
    $cost_amount    = $gross_cost_amount + $minus_cost_amount;

    // 一覧htmlフッタ
    $html_m  = "    <tr class=\"Result3\">\n";
    $html_m .= "        <td><b>合計</b></td>\n";
    $html_m .= "        <td></td>\n";
    $html_m .= "        <td></td>\n";
    $html_m .= "        <td></td>\n";
    $html_m .= "        <td></td>\n";
    $html_m .= "        <td align=\"right\">\n";
    $html_m .= "            ".Numformat_Ortho($gross_cost_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($minus_cost_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($cost_amount)."<br>\n";
    $html_m .= "        </td>\n";
    $html_m .= "        <td align=\"right\">\n";
    $html_m .= "            ".Numformat_Ortho($gross_notax_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($minus_notax_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($notax_amount)."<br>\n";
    $html_m .= "        </td>\n";
    $html_m .= "        <td align=\"right\">\n";
    $html_m .= "            ".Numformat_Ortho($gross_tax_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($minus_tax_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($tax_amount)."<br>\n";
    $html_m .= "        </td>\n";
    $html_m .= "        <td align=\"right\">\n";
    $html_m .= "            ".Numformat_Ortho($gross_ontax_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($minus_ontax_amount)."<br>\n";
    $html_m .= "            ".Numformat_Ortho($ontax_amount)."<br>\n";
    $html_m .= "        </td>\n";
    $html_m .= "        <td></td>\n";
    $html_m .= "        <td></td>\n";
    $html_m .= "        <td></td>\n";
    $html_m .= "        <td></td>\n";
    $html_m1  = $html_m;
    $html_m1 .= "        <td></td>\n";
    $html_m1 .= "        <td></td>\n";
    $html_m1 .= "    </tr>\n";
    $html_m2  = $html_m;
    $html_m2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["output_slip_button"]]->toHtml()."</td>\n";
    $html_m2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["output_re_slip_button"]]->toHtml()."</td>\n";
    $html_m2 .= "    </tr>\n";

    // 発行ALLチェックフォーム作成
    $form->addElement("checkbox", "slip_check_all",    "", "売上伝票発行", "onClick=\"javascript:All_Check_Slip('slip_check_all');\"");
    $form->addElement("checkbox", "re_slip_check_all", "", "再発行",       "onClick=\"javascript:All_Check_Re_Slip('re_slip_check_all');\"");

    // 上記ALLチェックフォーム用jsを作成
    $javascript .= Create_Allcheck_Js("All_Check_Slip",    "slip_check",    $slip_data);
    $javascript .= Create_Allcheck_Js("All_Check_Re_Slip", "re_slip_check", $re_slip_data);

    // 一覧htmlヘッダ
    $html_h  = "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
    $html_h .= "        <td class=\"Title_Pink\">No.</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">\n";
    $html_h .= "            ".Make_Sort_Link($form, "sl_client_cd")."<br>\n";
    $html_h .= "            <br style=\"font-size: 4px;\">\n";
    $html_h .= "            ".Make_Sort_Link($form, "sl_client_name")."<br>\n";
    $html_h .= "        </td>\n";
    $html_h .= "        <td class=\"Title_Pink\">".Make_Sort_Link($form, "sl_slip")."</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">".Make_Sort_Link($form, "sl_sale_day")."</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">取引区分</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">原価金額</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">売上金額(税抜)</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">消費税</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">売上金額(税込)</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">受注番号</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">分割回数</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">削除</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">日次更新</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">".$form->_elements[$form->_elementIndex["slip_check_all"]]->toHtml()."</td>\n";
    $html_h .= "        <td class=\"Title_Pink\">".$form->_elements[$form->_elementIndex["re_slip_check_all"]]->toHtml()."</td>\n";
    $html_h .= "    </tr>\n";

    // 一覧テーブルを合体させる
    $html_p  = "\n";
    $html_p .= "<table class=\"List_Table\" border=\"1\" width=\"100%\">\n";
    $html_p .= $html_h;
    $html_p .= $html_m1;
    $html_p .= $html_l;
    $html_p .= $html_m2;
    $html_p .= "</table>\n";

    // 合計テーブルhtml作成
    $html_g  = "<table class=\"List_Table\" border=\"1\" align=\"right\">\n";
    $html_g .= "<col width=\"80px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_g .= "<col width=\"80px\" align=\"right\">\n";
    $html_g .= "<col width=\"80px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_g .= "<col width=\"80px\" align=\"right\">\n";
    $html_g .= "<col width=\"80px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_g .= "<col width=\"80px\" align=\"right\">\n";
    $html_g .= "    <tr class=\"Result1\">\n";
    $html_g .= "        <td class=\"Title_Pink\">掛売金額</td>\n";
    $html_g .= "        <td>".Number_Format_Color($kake_notax_amount)."</td>\n";
    $html_g .= "        <td class=\"Title_Pink\">掛売消費税</td>\n";
    $html_g .= "        <td>".Number_Format_Color($kake_tax_amount)."</td>\n";
    $html_g .= "        <td class=\"Title_Pink\">掛売合計</td>\n";
    $html_g .= "        <td>".Number_Format_Color($kake_ontax_amount)."</td>\n";
    $html_g .= "    </tr>\n";
    $html_g .= "    <tr class=\"Result1\">\n";
    $html_g .= "        <td class=\"Title_Pink\">現金金額</td>\n";
    $html_g .= "        <td>".Number_Format_Color($genkin_notax_amount)."</td>\n";
    $html_g .= "        <td class=\"Title_Pink\">現金消費税</td>\n";
    $html_g .= "        <td>".Number_Format_Color($genkin_tax_amount)."</td>\n";
    $html_g .= "        <td class=\"Title_Pink\">現金合計</td>\n";
    $html_g .= "        <td>".Number_Format_Color($genkin_ontax_amount)."</td>\n";
    $html_g .= "    </tr>\n";
    $html_g .= "    <tr class=\"Result1\">\n";
    $html_g .= "        <td class=\"Title_Pink\">税込金額</td>\n";
    $html_g .= "        <td>".Number_Format_Color($notax_amount)."</td>\n";
    $html_g .= "        <td class=\"Title_Pink\">消費税合計</td>\n";
    $html_g .= "        <td>".Number_Format_Color($tax_amount)."</td>\n";
    $html_g .= "        <td class=\"Title_Pink\">税込合計</td>\n";
    $html_g .= "        <td>".Number_Format_Color($ontax_amount)."</td>\n";
    $html_g .= "    </tr>\n";
    $html_g .= "</table>\n";

}


/****************************/
//JavaScript
/****************************/
$order_delete  = " function Order_Delete(hidden1,hidden2,ord_id,hidden3,enter_date){\n";
$order_delete .= "    res = window.confirm(\"削除します。よろしいですか？\");\n";
$order_delete .= "    if (res == true){\n";
$order_delete .= "        var id = ord_id;\n";
$order_delete .= "        var edate = enter_date;\n";
$order_delete .= "        var hdn1 = hidden1;\n";
$order_delete .= "        var hdn2 = hidden2;\n";
$order_delete .= "        var hdn3 = hidden3;\n";
$order_delete .= "        document.dateForm.elements[hdn1].value = 'true';\n";
$order_delete .= "        document.dateForm.elements[hdn2].value = id;\n";
$order_delete .= "        document.dateForm.elements[hdn3].value = edate;\n";
$order_delete .= "        //同じウィンドウで遷移する\n";
$order_delete .= "        document.dateForm.target=\"_self\";\n";
$order_delete .= "        //自画面に遷移する\n";
$order_delete .= "        document.dateForm.action='".$_SERVER["PHP_SELF"]."';\n";
$order_delete .= "        //POST情報を送信する\n";
$order_delete .= "        document.dateForm.submit();\n";
$order_delete .= "        return true;\n";
$order_delete .= "    }else{\n";
$order_delete .= "        return false;\n";
$order_delete .= "    }\n";
$order_delete .= "}\n";


/****************************/
// HTML作成（検索部）
/****************************/
// モジュール個別テーブルの共通部分１
$html_s_tps  = "<br style=\"font-size: 4px;\">\n";
$html_s_tps .= "\n";
$html_s_tps .= "<table class=\"Table_Search\">\n";
$html_s_tps .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s_tps .= "    <col width=\"300px\">\n";
$html_s_tps .= "    <col width=\"130px\" style=\"font-weight: bold;\">\n";
$html_s_tps .= "    <col width=\"300px\">\n";
$html_s_tps .= "    <tr>\n";
// モジュール個別テーブルの共通部分２
$html_s_tpe .= "    </tr>\n";
$html_s_tpe .= "</table>\n";
$html_s_tpe .= "\n";

// 共通検索テーブル
$html_s  = Search_Table_Sale_H($form);
// モジュール個別検索テーブル１
$html_s .= $html_s_tps;
$html_s .= "        <td class=\"Td_Search_3\">日次更新</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_renew"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">税込金額</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_sale_amount"]]->toHtml()."</td>\n";
$html_s .= $html_s_tpe;
// モジュール個別検索テーブル２
$html_s .= $html_s_tps;
$html_s .= "        <td class=\"Td_Search_3\">伝票番号</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_slip_no"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">受注番号</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_aord_no"]]->toHtml()."</td>\n";
$html_s .= $html_s_tpe;
// モジュール個別検索テーブル３
$html_s .= $html_s_tps;
$html_s .= "        <td class=\"Td_Search_3\">商品</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_goods"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">Ｍ区分</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_g_goods"]]->toHtml()."</td>\n";
$html_s .= $html_s_tpe;
// モジュール個別検索テーブル４
$html_s .= $html_s_tps;
$html_s .= "        <td class=\"Td_Search_3\">管理区分</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_product"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">商品分類</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_g_product"]]->toHtml()."</td>\n";
$html_s .= $html_s_tpe;
// モジュール個別検索テーブル５
$html_s .= $html_s_tps;
$html_s .= "        <td class=\"Td_Search_3\">伝票発行</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_slip_type"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">発行状況</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_slip_out"]]->toHtml()."</td>\n";
$html_s .= $html_s_tpe;
// モジュール個別検索テーブル６
$html_s .= $html_s_tps;
$html_s .= "        <td class=\"Td_Search_3\">取引区分</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_trade"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">割賦回収日</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_installment_day"]]->toHtml()."</td>\n";
$html_s .= $html_s_tpe;
// モジュール個別検索テーブル７
$html_s .= $html_s_tps;
$html_s .= "        <td class=\"Td_Search_3\">抽出対象</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_shop"]]->toHtml()."</td>\n";
$html_s .= $html_s_tpe;
// ボタン
$html_s .= "<table align=\"right\"><tr><td>\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["form_show_button"]]->toHtml()."　\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["form_clear_button"]]->toHtml()."\n";
$html_s .= "</td></tr></table>";
$html_s .= "\n";


/****************************/
// HTMLヘッダ
/****************************/
$html_header = Html_Header($page_title);

/****************************/
// HTMLフッタ
/****************************/
$html_footer = Html_Footer();

/****************************/
// メニュー作成
/****************************/
$page_menu = Create_Menu_h("sale", "2");

/****************************/
// 画面ヘッダー作成
/****************************/
$page_title .= "　".$form->_elements[$form->_elementIndex["203_button"]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex["201_button"]]->toHtml();
$page_header = Create_Header($page_title);

/****************************/
// ページ作成
/****************************/
// ページ数を取得
$html_page =  Html_Page2($total_count, $page_count, 1, $limit);
$html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form関連の変数をassign
$smarty->assign("form", $renderer->toArray());

// その他の変数をassign
$smarty->assign("var", array(
    // 共通
    "html_header"           => "$html_header",
    "page_menu"             => "$page_menu",
    "page_header"           => "$page_header",
    "html_footer"           => "$html_footer",
    // エラーメッセージ（伝票削除、伝票発行）
    "del_error_msg"         => "$del_error_msg",
    "output_error_msg"      => "$output_error_msg",
    // js
    "order_delete"          => "$order_delete",
    "javascript"            => $javascript,
    // フラグ
    "post_flg"              => "$post_flg",
    "err_flg"               => "$err_flg",
));

$smarty->assign("html", array(
    "html_s"        => $html_s,
    "html_p"        => $html_p,
    "html_g"        => $html_g,
    "html_page"     => $html_page,
    "html_page2"    => $html_page2,
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF].".tpl"));
?>
