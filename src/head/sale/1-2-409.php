<?php

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/10/10      05-003      ふ          ブラウザの戻るまたは戻るボタン押下時、hierselectに値が復元されないバグに対応
 *  2007/01/25                  watanabe-k  ボタンの色変更 
 *  2009/06/15     	改修No.4    aizawa-m	取引区分：「相殺」の場合、「銀行」必須にしない
 *	2009/06/16		改修No.4	aizawa-m	取引区分：「相殺」の場合、「銀行」に入力があるとエラー
 *  2009/07/27                  aoyama-n    請求先を変更すると請求締日以前の日付で入金できてしまう不具合修正
 *  2009-10-16                  hashimoto-y 親子関係を選択したときの警告表示を廃止
 *  2011/02/05                  watanabe-k  請求書作成以後に、入金できてしまう不具合の修正
 *   2016/01/20                amano  Button_Submit, Button_Submit_1 関数でボタン名が送られない IE11 バグ対応
 *
 */

$page_title = "入金入力";

// 環境設定ファイル
require_once("ENV_local.php");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB接続
$db_con = Db_Connect();

// モジュール名
$mod_me = "1-2-409";


/*****************************/
// 権限関連処理
/*****************************/
// 権限チェック
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/*****************************/
// 再遷移先をSESSIONにセット
/*****************************/
// GET、POSTが無い場合
if ($_GET == null && $_POST == null){
    Set_Rtn_Page("payin");
}


/****************************/
// 初期処理
/****************************/
// OKボタン押下時はvalueをクリア
if ($_POST["form_ok_btn"] != null){
    $clear_hdn["form_ok_btn"] = "";
    $form->setConstants($clear_hdn);
}


/*****************************/
// 外部変数取得
/*****************************/
// SESSION
$shop_id            = $_SESSION["client_id"];           // ショップID
$group_kind         = $_SESSION["group_kind"];          // グループ種別
$staff_id           = $_SESSION["staff_id"];            // スタッフID
$staff_name         = $_SESSION["staff_name"];          // スタッフ名
// POST
$post               = $_POST["post"];                   // ページPOST情報
// POST(hidden)
$calc_flg           = $_POST["hdn_calc_flg"];           // 合計算出フラグ


/****************************/
// 行数関連処理
/****************************/
/*** 初期設定 ***/
// 最大行数がPOSTされている場合はその行数
// POSTされていない場合はデフォルト
$max_row = ($_POST["hdn_max_row"] != null) ? $_POST["hdn_max_row"] : 5;

/*** 行追加設定 ***/
// 行追加フラグがtrueの場合
if ($_POST["hdn_add_row_flg"] == true){

    // 最大行+5
    $max_row = $max_row+5;

    // 行追加フラグをクリア
    $clear_hdn_flg["hdn_add_row_flg"] = "";
    $form->setConstants($clear_hdn_flg);

}

/*** 行数をhiddenに ***/
// 行数をhiddenに格納
$row_num_data["hdn_max_row"] = $max_row;
$form->setConstants($row_num_data);

// hiddenの行削除履歴がPOSTされた場合
if ($_POST["hdn_ary_del_rows"] != null){

    // これまでに削除された行全てを削除行履歴配列へ代入
    foreach ($_POST["hdn_ary_del_rows"] as $key => $value){
        $ary_del_row_history[] = $value;
    }

}

/*** 行削除設定 ***/
// 削除行番号がPOSTされた場合
if ($_POST["hdn_del_row_no"] != null){

    // 削除行番号取得
    $del_row_no = $_POST["hdn_del_row_no"];

    // 削除行番号を削除行履歴配列へ追加
    $ary_del_row_history[] = $_POST["hdn_del_row_no"];

    // 削除行履歴配列をhiddenにセット
    $del_rows_data["hdn_ary_del_rows"] = $ary_del_row_history;
    $form->setConstants($del_rows_data);

    // hiddenの削除行番号をクリア
    $clear_hdn_data["hdn_del_row_no"] = "";
    $form->setConstants($clear_hdn_data);

}

// 行削除が一度も行われていない場合
if ($_POST["hdn_ary_del_rows"] == null && $_POST["hdn_del_row_no"] == null){

    // 空の削除行格納用配列を作成
    $ary_del_row_history = array();

}


/****************************/
// 一括設定ボタン処理
/****************************/
// 一括設定ボタンが押下された場合
if ($_POST["hdn_clt_set_flg"] != null){

    // 行数分（削除済行含む）ループ
    for ($i=0; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){
            // 一括設定内容をフォームにセット
            $clt_set_data["form_payin_date"][$i]["y"]   = $_POST["form_payin_date_clt_set"]["y"];   // 入金日（年）
            $clt_set_data["form_payin_date"][$i]["m"]   = $_POST["form_payin_date_clt_set"]["m"];   // 入金日（月）
            $clt_set_data["form_payin_date"][$i]["d"]   = $_POST["form_payin_date_clt_set"]["d"];   // 入金日（日）
            $clt_set_data["form_trade"][$i]             = $_POST["form_trade_clt_set"];             // 取引区分
            $clt_set_data["form_bank_".$i][0]           = $_POST["form_bank_clt_set"][0];           // 銀行
            $clt_set_data["form_bank_".$i][1]           = $_POST["form_bank_clt_set"][1];           // 支店
            $clt_set_data["form_bank_".$i][2]           = $_POST["form_bank_clt_set"][2];           // 口座番号
        }

    }

    // 一括設定内容をフォームにセット
    $form->setConstants($clt_set_data);

    // 一括設定フラグをクリア
    $clear_hdn_data["hdn_clt_set_flg"] = "";
    $form->setConstants($clear_hdn_data);

}


/****************************/
// hdnの振込名義をstaticにセット
/****************************/
for ($i=0; $i<$max_row; $i++){

    // 請求先IDのある行の場合
    if ($_POST["hdn_claim_id"][$i] != null){
        // 必要なデータ（振込名義1,2）をセット
        $pay_account_set["form_pay_name"][$i]       = stripslashes(htmlspecialchars($_POST["hdn_pay_name"][$i]));
        $pay_account_set["form_account_name"][$i]   = stripslashes(htmlspecialchars($_POST["hdn_account_name"][$i]));
        $form->setConstants($pay_account_set);
    }

}


/****************************/
// 請求先の補完（コード入力時）
/****************************/
// POSTデータがある場合
if ($_POST["hdn_claim_search_flg"] != null){

    // 請求先検索された行番号を取得
    $claim_search_row = array_search("true", $_POST["hdn_claim_search_flg"]);

    // 請求先検索された行番号がnullでない場合
    if ($claim_search_row !== null){

        // POSTされた請求先コードを変数へ代入
        $search_claim_cd1 = $_POST["form_claim_cd"][$claim_search_row]["cd1"];
        $search_claim_cd2 = $_POST["form_claim_cd"][$claim_search_row]["cd2"];

        // 検索された請求先の情報を抽出
        $sql  = "SELECT \n";
        $sql .= "   t_client.client_id, \n";
        $sql .= "   t_client.client_cname, \n";
        $sql .= "   t_client.pay_name, \n";
        $sql .= "   t_client.account_name \n";
        $sql .= "FROM \n";
        $sql .= "   t_client \n";
        $sql .= "   INNER JOIN t_claim ON t_client.client_id = t_claim.claim_id \n";
        $sql .= "WHERE \n";
        $sql .= "   t_client.client_cd1 = '$search_claim_cd1' \n";
        $sql .= "AND \n";
        $sql .= "   t_client.client_cd2 = '$search_claim_cd2' \n";
        $sql .= "AND \n";
        $sql .= "   t_client.client_div = '3' \n";
//        $sql .= "AND \n";
//        $sql .= "   t_client.state = '1' \n";
        $sql .= "AND \n";
        $sql .= "   t_client.shop_id = $shop_id \n";
        $sql .= ";";

        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);

        // 該当データがある場合
        if ($num > 0){
            $search_claim_id        = pg_fetch_result($res, 0, 0);  // 請求先ID
            $search_claim_cname     = pg_fetch_result($res, 0, 1);  // 請求先名（略称）
            $search_pay_name        = pg_fetch_result($res, 0, 2);  // 振込名義1
            $search_account_name    = pg_fetch_result($res, 0, 3);  // 振込名義2
            $claim_found_flg        = true;                         // 請求先存在フラグ
        }else{
            $search_claim_id        = "";
            $search_claim_cname     = "";
            $search_pay_name        = "";
            $search_account_name    = "";
            $claim_found_flg        = false;
        }

        // 請求先ID、請求先名（略称）、振込名義1,2をフォームにセット
        $claim_data["hdn_claim_id"][$claim_search_row]      = $search_claim_id;
        $claim_data["form_claim_cname"][$claim_search_row]  = $search_claim_cname;
        $claim_data["form_pay_name"][$claim_search_row]     = htmlspecialchars($search_pay_name);
        $claim_data["form_account_name"][$claim_search_row] = htmlspecialchars($search_account_name);
        $claim_data["hdn_pay_name"][$claim_search_row]      = $search_pay_name;
        $claim_data["hdn_account_name"][$claim_search_row]  = $search_account_name;
        $form->setConstants($claim_data);

        // 請求先存在フラグがtrueの場合
        if ($claim_found_flg == true){

/*
            // 該当請求先宛で最新の請求書IDのデータレコードの支払予定額集計期間を取得
            $sql  = "SELECT \n";
            $sql .= "   ( \n";
            $sql .= "       CASE \n";
            $sql .= "           WHEN \n";
            $sql .= "               t_bill_d.payment_this < 0 \n";
            $sql .= "           THEN \n";
            $sql .= "               0 \n";
            $sql .= "           ELSE \n";
            $sql .= "               t_bill_d.payment_this \n";
            $sql .= "       END \n";
            $sql .= "   ) \n";
            $sql .= "   AS payment_this, \n";
            $sql .= "   t_bill_d.payment_extraction_s, \n";
            $sql .= "   t_bill_d.payment_extraction_e \n";
            $sql .= "FROM \n";
            $sql .= "   ( \n";
            $sql .= "       SELECT \n";
            $sql .= "           MAX(bill_id) AS bill_id \n";
            $sql .= "       FROM \n";
            $sql .= "           t_bill \n";
            $sql .= "       WHERE \n";
            $sql .= "           t_bill.claim_id = $search_claim_id \n";
            $sql .= "       AND \n";
            $sql .= "           t_bill.shop_id = $shop_id \n";
            $sql .= "   ) \n";
            $sql .= "   AS t_bill_max \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill_max.bill_id = t_bill_d.bill_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill_d.client_id = $search_claim_id \n";
            $sql .= "AND \n";
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            $num  = pg_num_rows($res);

            // 請求データがある場合
            if ($num > 0){

                $search_bill_amount   = pg_fetch_result($res, 0, 0);    // 請求額
                $payment_extraction_s = pg_fetch_result($res, 0, 1);    // 支払予定額抽出期間（開始）
                $payment_extraction_e = pg_fetch_result($res, 0, 2);    // 支払予定額抽出期間（終了）

                // 今だけ設定
                $payment_extraction_s = ($payment_extraction_s == null) ? "2005-01-01" : $payment_extraction_s;

                // 取得してきた期間に該当する該当請求先宛の請求書ID、請求番号を取得
                $sql  = "SELECT \n";
                $sql .= "   t_bill.bill_id, \n";
                $sql .= "   t_bill.bill_no, \n";
                $sql .= "   ( \n";
                $sql .= "       CASE \n";
                $sql .= "           WHEN \n";
                $sql .= "               t_bill_d.payment_this < 0 \n";
                $sql .= "           THEN \n";
                $sql .= "               0 \n";
                $sql .= "           ELSE \n";
                $sql .= "               t_bill_d.payment_this \n";
                $sql .= "       END \n";
                $sql .= "   ) \n";
                $sql .= "   AS payment_this \n";
                $sql .= "FROM \n";
                $sql .= "   t_bill \n";
                $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
                $sql .= "WHERE \n";
                $sql .= "   t_bill.claim_id = $search_claim_id \n";
                $sql .= "AND \n";
                $sql .= "   t_bill.shop_id = $shop_id \n";
                $sql .= "AND \n";
                $sql .= "   t_bill.collect_day > '$payment_extraction_s' \n";
                $sql .= ($payment_extraction_e != null) ? "AND \n" : null;
                $sql .= ($payment_extraction_e != null) ? "   t_bill.collect_day <= '$payment_extraction_e' \n" : null;
                $sql .= "AND \n";
                $sql .= "   t_bill_d.bill_data_div = '0' \n";
                $sql .= "ORDER BY \n";
                $sql .= "   t_bill.bill_no DESC \n";    // 伝票が2枚ある場合の応急処置
                $sql .= ";";
                $res  = Db_Query($db_con, $sql);
                $num  = pg_num_rows($res);

                if ($num > 0){
                    $search_bill_id     = pg_fetch_result($res, 0, 0);      // 請求書ID
                    $search_bill_no     = pg_fetch_result($res, 0, 1);      // 請求番号
//                    $search_bill_amount = pg_fetch_result($res, 0, 2);      // 請求額
                    $bill_found_flg     = true;
                }

            }

        }
*/

            // 該当請求先宛で最新の請求書データを取得
            $sql  = "SELECT \n";
            $sql .= "   t_bill.bill_id, \n";
            $sql .= "   t_bill.bill_no, \n";
            $sql .= "   t_bill_d.payment_this \n";
            $sql .= "FROM \n";
            $sql .= "   ( \n";
            $sql .= "       SELECT \n";
            $sql .= "           MAX(bill_id) AS bill_id \n";
            $sql .= "       FROM \n";
            $sql .= "           t_bill \n";
            $sql .= "       WHERE \n";
            $sql .= "           t_bill.claim_id = $search_claim_id \n";
            $sql .= "       AND \n";
            $sql .= "           t_bill.shop_id = ".$_SESSION["client_id"]." \n";
            $sql .= "   ) \n";
            $sql .= "   AS t_bill_max \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill_max.bill_id = t_bill_d.bill_id \n";
            $sql .= "   INNER JOIN t_bill   ON t_bill_d.bill_id = t_bill.bill_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill_d.client_id = $search_claim_id \n";
            $sql .= "AND \n";
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= ";"; 
            $res  = Db_Query($db_con, $sql);
            $num  = pg_num_rows($res);

            if ($num > 0){
                $search_bill_id     = pg_fetch_result($res, 0, 0);      // 請求書ID
                $search_bill_no     = pg_fetch_result($res, 0, 1);      // 請求番号
                $search_bill_amount = pg_fetch_result($res, 0, 2);      // 請求額
                $bill_found_flg     = true; 
            }

        }

        // 請求データ取得フラグがtrueの場合
        if ($bill_found_flg == true){
            $set_bill_data["hdn_bill_id"][$claim_search_row]        = $search_bill_id;
            $set_bill_data["form_bill_no"][$claim_search_row]       = $search_bill_no;
            $set_bill_data["form_bill_amount"][$claim_search_row]   = $search_bill_amount;
        }else{
            $set_bill_data["hdn_bill_id"][$claim_search_row]        = "";
            $set_bill_data["form_bill_no"][$claim_search_row]       = "";
            $set_bill_data["form_bill_amount"][$claim_search_row]   = "";
        }
        $form->setConstants($set_bill_data);

    }

    // hiddenの請求先検索行番号をクリア
    $hdn_clear_claim["hdn_claim_search_flg[$claim_search_row]"] = "";
    $form->setConstants($hdn_clear_claim);

}


/****************************/
// 金額合計算出処理
/****************************/
// 金額合計算出フラグがtrue、または入金ボタンが押下された場合
if ($calc_flg == true || $_POST["form_verify_btn"] != null){

    // 行数分（削除済行含む）ループ
    for ($i=0; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){

            // 全ての金額を加算していく
            $total_amount += ($_POST["form_trade"][$i] != "35") ? $_POST["form_amount"][$i] : null; 

            // 全ての手数料を加算していく
            $total_rebate += ($_POST["form_trade"][$i] == "35") ? $_POST["form_amount"][$i] : null; 
            $total_rebate += $_POST["form_rebate"][$i];

            // 合計 
            $total_payin  += $_POST["form_amount"][$i];
            $total_payin  += $_POST["form_rebate"][$i];

        }

    }

    // hiddenの金額合計フラグを削除
    // 金額の合計をフォームにセット
    $amount_sum_data["calc_flg"]            = "";
    $amount_sum_data["form_amount_total"]   = ($total_amount != null) ? number_format($total_amount) : 0;
    $amount_sum_data["form_rebate_total"]   = ($total_rebate != null) ? number_format($total_rebate) : 0;
    $amount_sum_data["form_payin_total"]    = ($total_payin  != null) ? number_format($total_payin)  : 0;
    $form->setConstants($amount_sum_data);

}


/****************************/
// フォームパーツ定義
/****************************/
// ヘッダ部リンクボタン
$ary_h_btn_list = array("照会・変更" => "./1-2-403.php", "入　力" => "./1-2-402.php");
Make_H_Link_Btn($form, $ary_h_btn_list, 2);

// 取引区分
$select_value_trade = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (32, 33, 34, 36, 37, 38) ");
$form->addElement("select", "form_trade_clt_set", "", $select_value_trade, $g_form_option_select);

// 入金日
$text = null;
$text[] =& $form->createElement("text", "y", "",
    "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
     onkeyup=\"changeText(this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]',4)\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]','form_payin_date_clt_set[d]')\"
     onBlur=\"blurForm(this)\""
);
$text[] =& $form->createElement("static", "", "", "-");
$text[] =& $form->createElement("text", "m", "",
    "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
     onkeyup=\"changeText(this.form,'form_payin_date_clt_set[m]','form_payin_date_clt_set[d]',2)\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]','form_payin_date_clt_set[d]')\"
     onBlur=\"blurForm(this)\""
);
$text[] =& $form->createElement("static", "", "", "-");
$text[] =& $form->createElement("text", "d", "",
    "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]','form_payin_date_clt_set[d]')\"
     onBlur=\"blurForm(this)\""
);
$form->addGroup($text, "form_payin_date_clt_set", "");

// 銀行
$select_value_bank  = Make_Ary_Bank($db_con);
$attach_html        = "　";
$obj_bank_select =& $form->addElement("hierselect", "form_bank_clt_set", "", "", $attach_html);
$obj_bank_select->setOptions($select_value_bank);

// 入金額合計
$form->addElement(
    "text", "form_amount_total", "",
    "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\"
     readonly'"
);

// 手数料合計
$form->addElement(
    "text", "form_rebate_total", "",
    "size=\"16\" maxLength=\"18\" 
     style=\"color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" 
     readonly"
);

// 合計
$form->addElement(
    "text", "form_payin_total", "",
    "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\"
     readonly'"
);

// hidden
$form->addElement("hidden", "hdn_clt_set_flg", null, null);             // 一括設定ボタン押下フラグ
$form->addElement("hidden", "hdn_max_row", null, null);                 // 最大行数
$form->addElement("hidden", "hdn_add_row_flg", null, null);             // 行追加フラグ
$form->addElement("hidden", "hdn_del_row_no", null, null);              // 削除行番号
$form->addElement("hidden", "hdn_calc_flg", null, null);                // 金額合計算出フラグ
for ($i=0; $i<count($ary_del_row_history); $i++){
    $form->addElement("hidden", "hdn_ary_del_rows[$i]", null, null);    // 削除行番号履歴   # 削除行履歴分作成する
}

// エラーセット専用フォーム
$form->addElement("text", "err_noway_forms", null, null);               // 1行以上入力があるか
$form->addElement("text", "err_illegal_verify", null, null);            // 不正POST
// 最大行数分（削除済行含む）ループ
for ($i=0, $j=1; $i<$max_row; $i++){
    // 削除行履歴にある行はスルー
    if (!in_array($i, $ary_del_row_history)){
        $form->addElement("text", "err_claim1[$j]", null, null);        // 請求先1
        $form->addElement("text", "err_claim2[$j]", null, null);        // 請求先2
        $form->addElement("text", "err_payin_date1[$j]", null, null);   // 入金日1
        $form->addElement("text", "err_payin_date2[$j]", null, null);   // 入金日2
        $form->addElement("text", "err_payin_date3[$j]", null, null);   // 入金日3
        $form->addElement("text", "err_payin_date4[$j]", null, null);   // 入金日4
        $form->addElement("text", "err_payin_date5[$j]", null, null);   // 入金日5
        $form->addElement("text", "err_payin_date6[$j]", null, null);   // 入金日6
        $form->addElement("text", "err_trade[$j]", null, null);         // 取引区分
        $form->addElement("text", "err_bank[$j]", null, null);          // 銀行
        $form->addElement("text", "err_amount1[$j]", null, null);       // 金額1
        $form->addElement("text", "err_amount2[$j]", null, null);       // 金額2
        $form->addElement("text", "err_rebate[$j]", null, null);        // 手数料
        $form->addElement("text", "err_limit_date1[$j]", null, null);   // 手形期日1
        $form->addElement("text", "err_limit_date2[$j]", null, null);   // 手形期日2
        $form->addElement("text", "err_limit_date3[$j]", null, null);   // 手形期日3
    }
    $j++;
}


/****************************/
// 請求先コードの編集中に入金確認画面へボタンが押下された場合の対処処理
/****************************/
// 入金確認画面へボタンが押された、かつ請求先検索フラグ配列にtrueがある場合
if ($_POST["form_verify_btn"] != null && in_array(true, $_POST["hdn_claim_search_flg"])){

    // 請求先検索行数を取得
    $search_key = array_search(true, $_POST["hdn_claim_search_flg"]);

    // hiddenの請求先ID配列の該当キーに請求先IDが格納されている場合
    if ($_POST["hdn_claim_id"][$search_key] != null){
        // hiddenに格納されている得意先IDとPOSTされた得意先コードの整合性をチェック
        $sql  = "SELECT ";
        $sql .= "   client_id ";
        $sql .= "FROM ";
        $sql .= "   t_client ";
        $sql .= "WHERE ";
        $sql .= "   client_id = ".$_POST["hdn_claim_id"][$search_key]." ";
        $sql .= "AND "; 
        $sql .= "   client_cd1 = '".$_POST["form_claim"][$search_key]["cd1"]."' ";
        $sql .= "AND "; 
        $sql .= "   client_cd2 = '".$_POST["form_claim"][$search_key]["cd2"]."' ";
        $sql .= "AND "; 
        $sql .= "   client_div = '3' ";
//        $sql .= "AND "; 
//        $sql .= "   state = '1' ";
        $sql .= "AND "; 
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";"; 
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        // 結果を不正POSTフラグに
        $illegal_verify_flg = ($num > 0) ? false : true; 
    // hiddenの請求先ID配列の該当キーに請求先IDが格納されていない場合
    }else{  
        // 不正POSTフラグをtrueに
        $illegal_verify_flg = true; 
    }

    // 不正POSTフラグtrueの場合はエラーをセット
    if ($illegal_verify_flg == true){
        $form->setElementError("err_illegal_verify", "請求先情報取得前に 入金確認画面へボタン が押されました。<br>操作をやり直してください。");
    }

}


/****************************/
// エラーチェック - PHP
/****************************/
// 入金確認画面へボタンが押下された場合
if ($_POST["form_verify_btn"] != null){

    /****************************/
    // データ部チェック
    /****************************/
    // 最大行数分（削除済行含む）ループ
    for ($i=0, $j=1; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){

            // チェック・クエリ処理し易い様、POSTデータを変数へ
            $post_claim_id[$i]      = $_POST["hdn_claim_id"][$i];
            $post_claim_cd1[$i]     = $_POST["form_claim_cd"][$i]["cd1"];
            $post_claim_cd2[$i]     = $_POST["form_claim_cd"][$i]["cd2"];
            $post_claim_cname[$i]   = $_POST["form_claim_cname"][$i];
            $post_payin_date_y[$i]  = $_POST["form_payin_date"][$i]["y"];
            $post_payin_date_m[$i]  = $_POST["form_payin_date"][$i]["m"];
            $post_payin_date_d[$i]  = $_POST["form_payin_date"][$i]["d"];
            $post_trade[$i]         = $_POST["form_trade"][$i];
            $post_bank_id[$i]       = $_POST["form_bank_$i"][0];
            $post_b_bank_id[$i]     = $_POST["form_bank_$i"][1];
            $post_account_id[$i]    = $_POST["form_bank_$i"][2];
            $post_bill_no[$i]       = $_POST["form_bill_no"][$i];
            $post_bill_amount[$i]   = $_POST["form_bill_amount"][$i];
            $post_amount[$i]        = $_POST["form_amount"][$i];
            $post_rebate[$i]        = $_POST["form_rebate"][$i];
            $post_limit_date_y[$i]  = $_POST["form_limit_date"][$i]["y"];
            $post_limit_date_m[$i]  = $_POST["form_limit_date"][$i]["m"];
            $post_limit_date_d[$i]  = $_POST["form_limit_date"][$i]["d"];
            $post_bill_paper_no[$i] = $_POST["form_bill_paper_no"][$i];
            $post_note[$i]          = $_POST["form_note"][$i];

            // 該当行のフォームに1つでも入力があればエラーチェックを行う（一括設定可能なフォームは除く）
            if ($post_claim_cd1[$i]     != null ||
                $post_claim_cd2[$i]     != null ||
                $post_claim_cname[$i]   != null ||
                $post_bill_no[$i]       != null ||
                $post_bill_amount[$i]   != null ||
                $post_amount[$i]        != null ||
                $post_rebate[$i]        != null ||
                $post_limit_date_y[$i]  != null ||
                $post_limit_date_m[$i]  != null ||
                $post_limit_date_d[$i]  != null ||
                $post_bill_paper_no[$i] != null ||
                $post_note[$i]          != null)
            {

                /*** 請求先 ***/
                // ■必須チェック
                if ($post_claim_cd1[$i] == null && $post_claim_cd2[$i] == null && $post_claim_cname[$i] == null){
                    // エラーをセット
                    $form->setElementError("err_claim1[$j]", $j."行目　請求先 は必須です。");
                    $err_claim_flg[$i] = true;
                }

                /*** 入金日 ***/
                // ■必須チェック
                if ($post_payin_date_y[$i] == null || $post_payin_date_m[$i] == null || $post_payin_date_d[$i] == null){
                    // エラーをセット
                    $form->setElementError("err_payin_date1[$j]", $j."行目　入金日 は必須です。");
                    $err_payin_date_flg[$i] = true;
                }
                // ■数値チェック
                if ($err_payin_date_flg[$i] != true &&
                    (!ereg("^[0-9]+$", $post_payin_date_y[$i]) ||
                     !ereg("^[0-9]+$", $post_payin_date_m[$i]) ||
                     !ereg("^[0-9]+$", $post_payin_date_d[$i]))
                ){
                    $form->setElementError("err_payin_date2[$j]", $j."行目　入金日 の日付が妥当ではありません。");
                    $err_payin_date_flg[$i] = true;
                }
                // ■日付としての正当性チェック
                if ($err_payin_date_flg[$i] != true){
                    // 日付としてエラーの場合
                    if(!checkdate((int)$post_payin_date_m[$i], (int)$post_payin_date_d[$i], (int)$post_payin_date_y[$i])){
                        // エラーをセット
                        $form->setElementError("err_payin_date2[$j]", $j."行目　入金日 の日付が妥当ではありません。");
                        $err_payin_date_flg[$i] = true;
                    }
                }
                // ■システム開始日以前チェック
                if ($err_payin_date_flg[$i] != true){ 
                    $chk_res = Sys_Start_Date_Chk($post_payin_date_y[$i], $post_payin_date_m[$i], $post_payin_date_d[$i], "入金日");
                    if ($chk_res != null){
                        // エラーをセット
                        $form->setElementError("err_payin_date6[$j]", $j."行目　".$chk_res);
                        $err_payin_date_flg[$i] = true;
                    }
                }
                // ■未来日付が入力されていないかチェック
                if ($err_payin_date_flg[$i] != true){ 
                    // 未来日付の場合
                    $post_payin_date_y2[$i] = str_pad($post_payin_date_y[$i], 4, "0", STR_PAD_LEFT);
                    $post_payin_date_m2[$i] = str_pad($post_payin_date_m[$i], 2, "0", STR_PAD_LEFT);
                    $post_payin_date_d2[$i] = str_pad($post_payin_date_d[$i], 2, "0", STR_PAD_LEFT);
                    if (date("Y-m-d") < $post_payin_date_y2[$i]."-".$post_payin_date_m2[$i]."-".$post_payin_date_d2[$i]){
                        // エラーをセット
                        $form->setElementError("err_payin_date3[$j]", $j."行目　入金日 が未来の日付になっています。");
                        $err_payin_date_flg[$i] = true;
                    }
                }
                // ■最新の月次更新以前の日付が入力されていないかチェック
                if ($err_payin_date_flg[$i] != true){
                    // 最新の月次更新日を取得
                    $sql  = "SELECT ";
                    $sql .= "   to_date(MAX(close_day), 'YYYY-MM-DD') AS close_day ";
                    $sql .= "FROM ";
                    $sql .= "   t_sys_renew ";
                    $sql .= "WHERE ";
                    $sql .= "   renew_div = '2' ";
                    $sql .= "AND ";
                    $sql .= "   shop_id = $shop_id ";
                    $sql .= ";";
                    $res  = Db_Query($db_con, $sql);
                    $num  = pg_num_rows($res);
                    $last_monthly_renew_date = ($num == 1) ? pg_fetch_result($res, 0) : null;
                    // 月次更新日がある場合
                    if ($last_monthly_renew_date != null){
                        // 最終の月次更新日以前の日付の場合
                        $post_payin_date_y2 = str_pad($post_payin_date_y[$i], 4, "0", STR_PAD_LEFT);
                        $post_payin_date_m2 = str_pad($post_payin_date_m[$i], 2, "0", STR_PAD_LEFT);
                        $post_payin_date_d2 = str_pad($post_payin_date_d[$i], 2, "0", STR_PAD_LEFT);
                        if ($post_payin_date_y2."-".$post_payin_date_m2."-".$post_payin_date_d2 <= $last_monthly_renew_date){
                            // エラーをセット
                            $form->setElementError("err_payin_date4[$j]", $j."行目　入金日 に前回の月次更新以前の日付が入力されています。");
                            $err_payin_date_flg[$i] = true;
                        }
                    }
                }
                // ■前回の請求締日以前の日付が入力されていないかチェック
                if ($err_payin_date_flg[$i] != true && $post_claim_id[$i] != null && $illegal_verify_flg != true){
                    // 最新の請求の締日を取得
                    $sql  = "SELECT ";
                    $sql .= "   MAX(t_bill_d.bill_close_day_this) ";
                    $sql .= "FROM ";
                    $sql .= "   t_bill ";
                    $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id ";
                    $sql .= "WHERE ";
                    //aoyama-n 2009-07-27
                    #$sql .= "   t_bill.claim_id = $post_claim_id[$i] ";
                    $sql .= "   t_bill_d.claim_div = '1' ";
                    $sql .= "AND ";
                    $sql .= "   t_bill.claim_id = $post_claim_id[$i] ";
                    $sql .= "AND "; 
                    $sql .= "   t_bill_d.client_id = $post_claim_id[$i] ";
                    $sql .= "AND "; 
                    $sql .= "   t_bill.shop_id = $shop_id ";
                    $sql .= ";";
                    $res  = Db_Query($db_con, $sql);
                    $num  = pg_num_rows($res);
                    $last_close_date[$i] = ($num > 0) ? pg_fetch_result($res, 0) : null; 
                    // 締日がある場合
                    if ($last_close_date[$i] != null){
                        // 入力された入金日が、前回の請求締日以前の場合
                        if ($post_payin_date_y[$i]."-".$post_payin_date_m[$i]."-".$post_payin_date_d[$i] <= $last_close_date[$i]){
                            // エラーをセット
                            $form->setElementError("err_payin_date5[$j]", $j."行目　入金日 に請求書作成済の日付が入力されています。<br>入金日を変更するか、請求書を削除して下さい。");
                            $err_payin_date_flg[$i] = true;
                        }
                    }
                }

                /*** 取引区分 ***/
                // ■必須チェック
                if ($post_trade[$i] == null){
                    // エラーをセット
                    $form->setElementError("err_trade[$j]", $j."行目　取引区分 は必須です。");
                }

                /*** 銀行 ***/
				//-- 2009/06/15 改修No.4 変更
				// 取引区分が「34:相殺」以外の場合、銀行の必須チェック
                // ■必須入力チェック
                if ($post_trade[$i] != 34 && 
						($post_bank_id[$i] == null || $post_b_bank_id[$i] == null || $post_account_id[$i] == null)){
                //if ($post_bank_id[$i] == null || $post_b_bank_id[$i] == null || $post_account_id[$i] == null){
                    $form->setElementError("err_bank[$j]", $j."行目　銀行・支店・口座番号 は必須です。");
                    $err_bank_flg[$i] = true; 
                } 
				//-- 2009/06/16 改修No.4 追加
				// 取引区分が「34:相殺」の場合、銀行の入力チェック
				if ($post_trade[$i] == 34 &&
						($post_bank_id[$i] != null || $post_b_bank_id[$i] != null || $post_account_id[$i] != null)){
					$form->setElementError("err_bank[$j]", $j."行目　取引区分 が 34:相殺 の場合、銀行・支店・口座番号 は選択できません。");
				}

                /*** 請求番号・請求額 ***/
                // ■必須チェック
                if ($err_claim_flg != true && ($post_bill_no[$i] == null || $post_bill_amount[$i] == null)){
                    // エラーをセット
                    $form->setElementError("err_claim2[$j]", $j."行目　請求データのない 請求先 が選択されています。");
                    $err_bill_no_flg[$i] = true;
                }

                /*** 金額 ***/
                // ■必須チェック
                if ($post_amount[$i] == null){
                    // エラーをセット
                    $form->setElementError("err_amount1[$j]", $j."行目　金額 は必須です。");
                    $err_amount[$i] = true;
                }
                // ■数値チェック
                if ($err_amount[$i] != true && !ereg("^[-]?[0-9]+$", $post_amount[$i])){
                    // エラーをセット
                    $form->setElementError("err_amount2[$j]", $j."行目　金額 は数値のみ入力可能です。");
                    $err_amount[$i] = true;
                }

                /*** 手数料 ***/
                // ■数値チェック
                if ($post_rebate[$i] != null && !ereg("^[-]?[0-9]+$", $post_rebate[$i])){
                    // エラーをセット
                    $form->setElementError("err_rebate[$j]", $j."行目　手数料 は数値のみ入力可能です。");
                    $err_rebate[$i] = true;
                }

                /*** 手形期日 ***/
                // ■条件付き必須チェック（取引区分が手形入金の時）
                if ($post_trade[$i] == 33 &&
                    ($post_limit_date_y[$i] == null &&
                     $post_limit_date_m[$i] == null &&
                     $post_limit_date_d[$i] == null)
                ){
                    $form->setElementError("err_limit_date1[$j]", $j."行目　取引区分 が 33:手形入金 の場合、手形期日 は必須です。");
                    $err_limit_date[$i] = true;
                }
                // ■日付として妥当かチェック
                if ($err_limit_date[$i] != true &&
                    ($post_limit_date_y[$i] != null ||
                     $post_limit_date_m[$i] != null ||
                     $post_limit_date_d[$i] != null)
                ){
                    if(!checkdate((int)$post_limit_date_m[$i], (int)$post_limit_date_d[$i], (int)$post_limit_date_y[$i])){
                        $form->setElementError("err_limit_date2[$j]", $j."行目　手形期日 の日付が妥当ではありません。");
                        $err_limit_date[$i] = true;
                    }
                }
                // ■システム開始日以前チェック
                if ($err_limit_date[$i] != true){ 
                    $chk_res = Sys_Start_Date_Chk($post_limit_date_y[$i], $post_limit_date_m[$i], $post_limit_date_d[$i], "手形期日");
                    if ($chk_res != null){
                        // エラーをセット
                        $form->setElementError("err_limit_date3[$j]", $j."行目　".$chk_res);
                        $err_limit_date[$i] = true;
                    }
                }

            // 入力が無い行の場合
            }else{

                // 入力の無い行の行番号を配列に代入していく
                $ary_noway_forms[] = $i;

            }

            // 実際に表示されている（削除されていない）行数を取得するため行番号を配列に代入していく
            $ary_all_forms[] = $i;

            // 行番号カウンタ（エラーメッセージの行数表示用）
            $j++;

        }

    }

    /*** データ部分の入力行数 ***/
    // ■入力0件チェック
    // 実際に表示されている行数と、入力の無い行数が同じ場合
    if (count($ary_noway_forms) == count($ary_all_forms)){
        $form->setElementError("err_noway_forms", "入金データ を入力して下さい。");
    }

}


/****************************
// 全エラーチェック結果集計
/****************************/
// 入金確認画面へボタンが押下された、かつ不正POSTフラグがtrueでない場合
if ($_POST["form_verify_btn"] != null && $illegal_verify_flg != true){

    /*** 結果集計 ***/
    // エラーのあるフォームのフォーム名を格納するための配列を作成
    $ary_all_err_forms = array();
    // エラーのあるフォームのフォーム名を配列に格納
    foreach ($form as $key1 => $value1){
        if ($key1 == "_errors"){
            foreach ($value1 as $key2 => $value2){
                $ary_all_err_forms[] = $key2;
            }
        }
    }
    // エラー件数が0件の場合は確認フラグをtrueにする
    $verify_flg = (count($ary_all_err_forms) == 0) ? true : false;

}


/****************************/
// 一括設定可能フォームの、不必要なデータを消去
/****************************/
// 確認画面フラグがtrueの場合
if ($verify_flg == true){

    // 最大行数分（削除済行含む）ループ
    for ($i=0; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){

            // 必須項目に入力がない場合（INSERTしない行）
            if ($_POST["form_claim_cd"][$i]["cd1"] == null){
                // 一括設定可能フォームの、不必要なデータをクリア
                $clt_form_clear["form_payin_date"][$i]["y"] = "";
                $clt_form_clear["form_payin_date"][$i]["m"] = "";
                $clt_form_clear["form_payin_date"][$i]["d"] = "";
                $clt_form_clear["form_trade[$i]"]           = "";
                $clt_form_clear["form_bank_$i"][0]          = "";
                $clt_form_clear["form_bank_$i"][1]          = "";
                $clt_form_clear["form_bank_$i"][2]          = "";
                $form->setConstants($clt_form_clear);
            }

            // 請求先IDのある行の場合
            if ($_POST["hdn_claim_id"][$i] != null){
                // 必要なデータ（振込名義1,2）をセット
                $pay_account_name_set["form_pay_name"][$i]      = stripslashes(htmlspecialchars($_POST["hdn_pay_name"][$i]));
                $pay_account_name_set["form_account_name"][$i]  = stripslashes(htmlspecialchars($_POST["hdn_account_name"][$i]));
                $form->setConstants($pay_account_name_set);
            }

        }

    }

}


/****************************/
// 登録処理等
/****************************/
// 入金OKボタン押下時
if ($_POST["form_ok_btn"] != null){

    /*** POSTデータを変数へ ***/
    // 最大行数分（削除済行含む）ループ
    for ($i=0; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){

            // 該当行のフォームに1つでも入力がある場合（一括設定可能なフォームは除く）
            if ($_POST["form_claim_cd"][$i]["cd1"]  != null ||
                $_POST["form_claim_cd"][$i]["cd2"]  != null ||
                $_POST["form_claim_cname"][$i]      != null ||
                $_POST["form_bill_no"][$i]          != null ||
                $_POST["form_bill_amount"][$i]      != null ||
                $_POST["form_trade"][$i]            != null ||
                $_POST["form_amount"][$i]           != null ||
                $_POST["form_rebate"][$i]           != null ||
                $_POST["form_limit_date"][$i]["y"]  != null ||
                $_POST["form_limit_date"][$i]["m"]  != null ||
                $_POST["form_limit_date"][$i]["d"]  != null ||
                $_POST["form_bill_paper_no"][$i]    != null ||
                $_POST["form_note"][$i]             != null)
            {
                // POSTデータを変数に代入
                $post_claim_id[$i]          = $_POST["hdn_claim_id"][$i];
                $post_claim_cd1[$i]         = $_POST["form_claim_cd"][$i]["cd1"];
                $post_claim_cd2[$i]         = $_POST["form_claim_cd"][$i]["cd2"];
                $post_claim_cname[$i]       = $_POST["form_claim_cname"][$i];
                $post_pay_name[$i]          = $_POST["hdn_pay_name"][$i];
                $post_account_name[$i]      = $_POST["hdn_account_name"][$i];
                if ($_POST["form_payin_date"][$i]["y"] != null ||
                    $_POST["form_payin_date"][$i]["m"] != null ||
                    $_POST["form_payin_date"][$i]["d"] != null)
                {
                    $post_payin_date_y[$i]  = str_pad($_POST["form_payin_date"][$i]["y"], 4, "0", STR_PAD_LEFT);
                    $post_payin_date_m[$i]  = str_pad($_POST["form_payin_date"][$i]["m"], 2, "0", STR_PAD_LEFT);
                    $post_payin_date_d[$i]  = str_pad($_POST["form_payin_date"][$i]["d"], 2, "0", STR_PAD_LEFT);
                    $post_payin_date[$i]    = $post_payin_date_y[$i]."-".$post_payin_date_m[$i]."-".$post_payin_date_d[$i];
                }else{
                    $post_payin_date[$i]    = null;
                }
                $post_trade[$i]             = $_POST["form_trade"][$i];
                $post_bank_id[$i]           = $_POST["form_bank_$i"][0];
                $post_b_bank_id[$i]         = $_POST["form_bank_$i"][1];
                $post_account_id[$i]        = $_POST["form_bank_$i"][2];
                $hdn_bill_id[$i]            = $_POST["hdn_bill_id"][$i];
                $post_amount[$i]            = $_POST["form_amount"][$i];
                $post_rebate[$i]            = $_POST["form_rebate"][$i];
                if ($_POST["form_limit_date"][$i]["y"] != null ||
                    $_POST["form_limit_date"][$i]["m"] != null ||
                    $_POST["form_limit_date"][$i]["d"] != null)
                {
                    $post_limit_date_y[$i]  = str_pad($_POST["form_limit_date"][$i]["y"], 4, "0", STR_PAD_LEFT);
                    $post_limit_date_m[$i]  = str_pad($_POST["form_limit_date"][$i]["m"], 2, "0", STR_PAD_LEFT);
                    $post_limit_date_d[$i]  = str_pad($_POST["form_limit_date"][$i]["d"], 2, "0", STR_PAD_LEFT);
                    $post_limit_date[$i]    = $post_limit_date_y[$i]."-".$post_limit_date_m[$i]."-".$post_limit_date_d[$i];
                }else{
                    $post_limit_date[$i]    = null;
                }
                $post_bill_paper_no[$i]     = $_POST["form_bill_paper_no"][$i];
                $post_note[$i]              = $_POST["form_note"][$i];

                // 実際に入力がある行の行番号のみを配列に代入していく
                $ary_insert_forms[] = $i;

            }

        }

    }

    /*** 最新+1の入金番号を取得 ***/
    $sql  = "SELECT ";
    $sql .= "   MAX(pay_no) ";
    $sql .= "FROM ";
    $sql .= "   t_payin_h ";
    $sql .= "WHERE ";
    $sql .= "   shop_id = $shop_id ";
    $sql .= ";"; 
    $res  = Db_Query($db_con, $sql);
    $payin_no = str_pad(pg_fetch_result($res, 0 ,0)+1, 8, "0", STR_PAD_LEFT);

    /****************************/
    // DB処理
    /****************************/
    /*** トランザクション開始 ***/
    Db_Query($db_con, "BEGIN;");

    // 入力のある行数分ループ
    foreach ($ary_insert_forms as $key => $i){

        /*** 入金ヘッダINSERT ***/
        $sql  = "INSERT INTO \n";
        $sql .= "   t_payin_h \n";
        $sql .= "( \n";
        $sql .= "   pay_id, \n";
        $sql .= "   pay_no, \n";
        $sql .= "   pay_day, \n";
        $sql .= "   collect_staff_id, \n";
        $sql .= "   collect_staff_name, \n";
        $sql .= "   client_id, \n";
        $sql .= "   client_cd1, \n";
        $sql .= "   client_cd2, \n";
        $sql .= "   client_name, \n";
        $sql .= "   client_name2, \n";
        $sql .= "   client_cname, \n";
        $sql .= "   c_bank_cd, \n";
        $sql .= "   c_bank_name, \n";
        $sql .= "   c_b_bank_cd, \n";
        $sql .= "   c_b_bank_name, \n";
        $sql .= "   c_deposit_kind, \n";
        $sql .= "   c_account_no, \n";
        $sql .= "   claim_div, \n";
        $sql .= "   pay_name, \n";
        $sql .= "   account_name, \n";
        $sql .= "   bill_id, \n";
        $sql .= "   claim_cd1, \n";
        $sql .= "   claim_cd2, \n";
        $sql .= "   claim_cname, \n";
        $sql .= "   input_day, \n";
        $sql .= "   e_staff_id, \n";
        $sql .= "   e_staff_name, \n";
        $sql .= "   ac_staff_id, \n";
        $sql .= "   ac_staff_name, \n";
        $sql .= "   sale_id, \n";
        $sql .= "   renew_flg, \n";
        $sql .= "   renew_day, \n";
        $sql .= "   shop_id \n";
        $sql .= ") \n";
        $sql .= "VALUES \n";
        $sql .= "( \n";
        $sql .= "   (SELECT COALESCE(MAX(pay_id), 0)+1 FROM t_payin_h), \n";
        $sql .= "   '$payin_no', \n";
        $sql .= "   '$post_payin_date[$i]', \n";
        $sql .= "   NULL, \n";
        $sql .= "   NULL, \n";
        $sql .= "   $post_claim_id[$i], \n";
        $sql .= "   '$post_claim_cd1[$i]', \n";
        $sql .= "   '$post_claim_cd2[$i]', \n";
        $sql .= "   (SELECT client_name FROM t_client WHERE client_id = $post_claim_id[$i]), \n";
        $sql .= "   (SELECT client_name2 FROM t_client WHERE client_id = $post_claim_id[$i]), \n";
        $sql .= "   '$post_claim_cname[$i]', \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_bank.bank_cd FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_bank.bank_name FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_b_bank.b_bank_cd FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_b_bank.b_bank_name FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_account.deposit_kind FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_account.account_no FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   '1', \n";
        $sql .= "   '$post_pay_name[$i]', \n";
        $sql .= "   '$post_account_name[$i]', \n";
        $sql .= "   $hdn_bill_id[$i], \n";
        $sql .= "   '$post_claim_cd1[$i]', \n";
        $sql .= "   '$post_claim_cd2[$i]', \n";
        $sql .= "   '$post_claim_cname[$i]', \n";
        $sql .= "   NOW(), \n";
        $sql .= "   $staff_id, \n";
        $sql .= "   '".addslashes($staff_name)."', \n";
        $sql .= "   $staff_id, \n";
        $sql .= "   '".addslashes($staff_name)."', \n";
        $sql .= "   NULL, \n";
        $sql .= "   'f', \n";
        $sql .= "   NULL, \n";
        $sql .= "   $shop_id \n";
        $sql .= ") \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        // エラー時はロールバック
        if($res == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        /*** 入金データINSERT ***/
        // 入金ヘッダに登録した入金IDを取得
        $sql  = "SELECT ";
        $sql .= "   pay_id ";
        $sql .= "FROM ";
        $sql .= "   t_payin_h ";
        $sql .= "WHERE ";
        $sql .= "   pay_no = '$payin_no' ";
        $sql .= "AND "; 
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";"; 
        $res  = Db_Query($db_con, $sql);
        $payin_id[$i] = pg_fetch_result($res, 0);

        // INSERT実行
        $sql  = "INSERT INTO ";
        $sql .= "   t_payin_d ";
        $sql .= "( ";
        $sql .= "   pay_d_id, ";
        $sql .= "   pay_id, ";
        $sql .= "   trade_id, ";
        $sql .= "   amount, ";
        $sql .= "   bank_cd, ";
        $sql .= "   bank_name, ";
        $sql .= "   b_bank_cd, ";
        $sql .= "   b_bank_name, ";
        $sql .= "   account_id, ";
        $sql .= "   deposit_kind, ";
        $sql .= "   account_no, ";
        $sql .= "   payable_day, ";
        $sql .= "   payable_no, ";
        $sql .= "   note ";
        $sql .= ") ";
        $sql .= "VALUES ";
        $sql .= "( ";
        $sql .= "   (SELECT COALESCE(MAX(pay_d_id), 0)+1 FROM t_payin_d), ";
        $sql .= "   $payin_id[$i], ";
        $sql .= "   $post_trade[$i], ";
        $sql .= "   '$post_amount[$i]', ";
        $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_cd FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_name FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_cd FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_name FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_account_id[$i] != null) ? " $post_account_id[$i], " : " NULL, ";
        $sql .= ($post_account_id[$i] != null) ? " (SELECT deposit_kind FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
        $sql .= ($post_account_id[$i] != null) ? " (SELECT account_no FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
        $sql .= ($post_limit_date[$i] != null) ? " '$post_limit_date[$i]', " : " NULL, ";
        $sql .= ($post_bill_paper_no[$i] != null) ? " '$post_bill_paper_no[$i]', " : " NULL, ";
        $sql .= "   '$post_note[$i]' ";
        $sql .= ") ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        // エラー時はロールバック
        if($res == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        // 手数料が入力されている場合はさらに1レコードINSERT
        if ($post_rebate[$i] != null){
            $sql  = "INSERT INTO ";
            $sql .= "   t_payin_d ";
            $sql .= "( ";
            $sql .= "   pay_d_id, ";
            $sql .= "   pay_id, ";
            $sql .= "   trade_id, ";
            $sql .= "   amount, ";
            $sql .= "   bank_cd, ";
            $sql .= "   bank_name, ";
            $sql .= "   b_bank_cd, ";
            $sql .= "   b_bank_name, ";
            $sql .= "   account_id, ";
            $sql .= "   deposit_kind, ";
            $sql .= "   account_no, ";
            $sql .= "   payable_day, ";
            $sql .= "   payable_no, ";
            $sql .= "   note ";
            $sql .= ") ";
            $sql .= "VALUES ";
            $sql .= "( ";
            $sql .= "   (SELECT COALESCE(MAX(pay_d_id), 0)+1 FROM t_payin_d), ";
            $sql .= "   $payin_id[$i], ";
            $sql .= "   35, ";
            $sql .= "   '$post_rebate[$i]', ";
            $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_cd FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_name FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_cd FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_name FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_account_id[$i] != null) ? " $post_account_id[$i], " : " NULL, ";
            $sql .= ($post_account_id[$i] != null) ? " (SELECT deposit_kind FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
            $sql .= ($post_account_id[$i] != null) ? " (SELECT account_no FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
            $sql .= "   NULL, ";
            $sql .= "   NULL, ";
            $sql .= "   NULL ";
            $sql .= ") ";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            // エラー時はロールバック
            if($res == false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

        }

        /* 入金番号テーブル更新 **/
        // 直営の場合
        if ($group_kind == 2){
            $sql  = "INSERT INTO ";
            $sql .= "   t_payin_no_serial ";
            $sql .= "( ";
            $sql .= "   pay_no ";
            $sql .= ") ";
            $sql .= "VALUES ";
            $sql .= "( ";
            $sql .= "   '$payin_no' ";
            $sql .= ") ";
            $sql .= ";"; 
            $res  = Db_Query($db_con, $sql);
            // エラー時はロールバック
            if($res == false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;   
            }       
        }

        /*** 入金番号を加算して桁埋め ***/
        $payin_no = str_pad($payin_no+1, 8, "0", STR_PAD_LEFT);

    }

    /*** トランザクション完結 ***/
    Db_Query($db_con, "COMMIT;");
    
    /*** ページ遷移 ***/
    // 完了画面へページ遷移
    header("Location: ./1-2-408.php");

}


/****************************/
// 可変フォームパーツ定義（表）
/****************************/
// セレクトボックスアイテムの配列作成
$select_value_trade     = null;
$select_value_trade     = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (32, 33, 34, 36, 37, 38) ");
$select_value_bank  = Make_Ary_Bank($db_con);
// 銀行hierselect用連結html
$attach_html        = "<br>";

// フォームの見た目をstaticっぽくするためのCSSを変数に入れておく（長いので）
$style = "color: #585858; border: #ffffff 1px solid; background-color: #ffffff;";


// 最大行数分（削除済行含む）ループ
for ($i=0, $row_num=0; $i<$max_row; $i++){

    // 削除行履歴にある行はスルー
    if (!in_array($i, $ary_del_row_history)){

        $row_num++;

        // 確認画面フラグがtrueでない場合
        if ($verify_flg != true){

            // 請求先コード
            $text = null;
            $text[] =& $form->addElement("text", "cd1", "",
                "size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
                 onChange=\"javascript:Change_Submit('hdn_claim_search_flg[$i]','#$row_num','true','form_claim_cd[$i][cd2]')\"
                 onkeyup=\"changeText(this.form,'form_claim_cd[$i][cd1]','form_claim_cd[$i][cd2]',6)\" ".$g_form_option."\""
            );
            $text[] =& $form->addElement("static", "", "", "-");
            $text[] =& $form->addElement("text", "cd2", "",
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onChange=\"javascript:Button_Submit('hdn_claim_search_flg[$i]','#$row_num','true', this)\" ".$g_form_option."\""
            );
            $form->addGroup($text, "form_claim_cd[$i]", "");

            // 請求先名
            $text[] =& $form->addElement("text", "form_claim_cname[$i]", "",
                "size=\"34\" style=\"color : #000000; border : #ffffff 1px solid; background-color: #ffffff;\" readonly"
            );

            // 振込名義1
            $form->addElement("static", "form_pay_name[$i]", "", "");

            // 振込名義2
            $form->addElement("static", "form_account_name[$i]", "", "");

            //検索リンク
            $form->addElement("link", "form_claim_search[$i]", "", "#", "検索",
                "tabindex=\"-1\" 
                 onClick=\"javascript:return Open_SubWin_3('../dialog/1-0-250.php',
                   Array('form_claim_cd[$i][cd1]','form_claim_cd[$i][cd2]','form_claim_cname[$i]','hdn_claim_search_flg[$i]','hdn_claim_id[$i]'),
                   500, 450, '2-409', 1, $row_num);\""
            );

            // 入金日
            $text = null;
            $text[] =& $form->createElement("text", "y", "",
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]',4)\"
                 onFocus=\"onForm_today(this,this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]','form_payin_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_payin_date[$i][m]','form_payin_date[$i][d]',2)\"
                 onFocus=\"onForm_today(this,this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]','form_payin_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onFocus=\"onForm_today(this,this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]','form_payin_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $form->addGroup($text, "form_payin_date[$i]", "");

            // 取引区分
            $form->addElement("select", "form_trade[$i]", "", $select_value_trade, $g_form_option_select);

            // 銀行
            $obj_bank_select =& $form->addElement("hierselect", "form_bank_$i", "", "", $attach_html);
            $obj_bank_select->setOptions($select_value_bank);

            // 請求番号
            $form->addElement("text", "form_bill_no[$i]", "", "size=\"9\" maxlength=\"8\" style=\"$style\" readonly");

            // 請求額
            $form->addElement("text", "form_bill_amount[$i]", "",
                "class=\"money\" size=\"9\" maxlength=\"8\" style=\"text-align: right; $style\" readonly"
            );      

            // 金額 
            $form->addElement("text", "form_amount[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
            );      

            // 手数料
            $form->addElement("text", "form_rebate[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
            );      

            // 手形期日
            $text = null;
            $text[] =& $form->createElement("text", "y", "",
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]',4)\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]','form_limit_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_limit_date[$i][m]','form_limit_date[$i][d]',2)\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]','form_limit_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]','form_limit_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $form->addGroup($text, "form_limit_date[$i]", "");

            // 手形券面番号
            $form->addElement("text", "form_bill_paper_no[$i]", "", "size=\"13\" maxLength=\"10\" style=\"$g_form_style\" $g_form_option\"");

            // 備考
            $form->addElement("text", "form_note[$i]", "", "size=\"34\" maxLength=\"20\" $g_form_option\"");

            // 削除リンク
            $link_no = ($i+1 == $del_row_no) ? $row_num - 1 : $row_num;
            $form->addElement("link", "form_del_row[$i]", "", "#", "<font color=\"#fefefe\">削除</font>",
                "tabindex=-1 onClick=\"javascript:Dialogue_3('削除します。', $i, 'hdn_del_row_no', $row_num); return false;\""
            );

            // hidden
            $form->addElement("hidden", "hdn_claim_search_flg[$i]", null, null);
            $form->addElement("hidden", "hdn_claim_id[$i]", null, null);        // 各行で選択されている請求先ID
            $form->addElement("hidden", "hdn_bill_id[$i]", null, null);         // 各行で選択されている請求先IDから取得した請求書ID
            $form->addElement("hidden", "hdn_pay_name[$i]", null, null);        // 各行で選択されている請求先の振込名義1
            $form->addElement("hidden", "hdn_account_name[$i]", null, null);    // 各行で選択されている請求先の振込名義2

        // 確認画面フラグがtrueの場合
        }else{

            // 請求先コード
            $text = null;
            $text[] =& $form->addElement("text", "cd1", "", "size=\"7\" maxLength=\"6\" style=\"$style\" readonly");
            $text[] =& $form->addElement("static", "", "", "-");
            $text[] =& $form->addElement("text", "cd2", "", "size=\"4\" maxLength=\"4\" style=\"$style\" readonly");
            $form->addGroup($text, "form_claim_cd[$i]", "");

            // 請求先名
            $text[] =& $form->addElement("text", "form_claim_cname[$i]", "", "size=\"34\" style=\"$style\" readonly");

            // 振込名義1
            $form->addElement("static", "form_pay_name[$i]", "", "");

            // 振込名義2
            $form->addElement("static", "form_account_name[$i]", "", "");

            // 入金日
            $text = null;
            $text[] =& $form->createElement("text", "y", "", "size=\"4\" maxLength=\"4\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $form->addGroup($text, "form_payin_date[$i]", "");

            // 取引区分
            $verify_freeze_data_trade = $form->addElement("select", "form_trade[$i]", "", $select_value_trade, $g_form_option_select);
            $verify_freeze_data_trade->freeze();

            // 銀行
            $verify_freeze_data_bank = $obj_bank_select =& $form->addElement("hierselect", "form_bank_$i", "", "", $attach_html);
            $obj_bank_select->setOptions($select_value_bank);
            $verify_freeze_data_bank->freeze();

            // 請求番号
            $form->addElement("text", "form_bill_no[$i]", "", "size=\"9\" maxlength=\"8\" style=\"$style\" readonly");

            // 請求額
            $form->addElement("text", "form_bill_amount[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $style\" readonly"
            );

            // 金額
            $form->addElement("text", "form_amount[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $style\" readonly"
            );

            // 手数料
            $form->addElement("text", "form_rebate[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $style\" readonly"
            );

            // 手形期日
            $text = null;
            $text[] =& $form->createElement("text", "y", "", "size=\"4\" maxLength=\"4\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $form->addGroup($text, "form_limit_date[$i]", "");

            // 手形券面番号
            $form->addElement("text", "form_bill_paper_no[$i]", "", "size=\"13\" maxLength=\"10\" style=\"$style\" readonly");

            // 備考
            $form->addElement("text", "form_note[$i]", "", "size=\"34\" maxLength=\"20\" style=\"$style\" readonly");

            // hidden
            $form->addElement("hidden", "hdn_claim_id[$i]", null, null);        // 各行で選択されている請求先ID
            $form->addElement("hidden", "hdn_bill_id[$i]", null, null);         // 各行で選択されている請求先IDから取得した請求書ID
            $form->addElement("hidden", "hdn_pay_name[$i]", null, null);        // 各行で選択されている請求先の振込名義1
            $form->addElement("hidden", "hdn_account_name[$i]", null, null);    // 各行で選択されている請求先の振込名義2

        }

    }

}


/****************************/
// 可変フォームパーツ定義（ボタン）
/****************************/
/* 登録確認画面では、以下のボタンを表示しない */
if($verify_flg != true){

    // 得意先単位ボタン
    $form->addElement("button", "form_trans_client_btn", "得意先単位", "onClick=\"location.href('1-2-402.php')\"");

    // 銀行単位ボタン
    $form->addElement("button", "form_trans_bank_btn", "銀行単位", $g_button_color." onClick=\"location.href('1-2-409.php')\"");

    // 一括設定ボタン
    $form->addElement("button", "form_clt_set_btn", "一括設定", "onClick=\"javascript:Button_Submit('hdn_clt_set_flg','#','true', this)\"\"");

    // 行追加ボタン
    $form->addElement("button", "form_add_row_btn", "行追加", "onClick=\"javascript:Button_Submit_1('hdn_add_row_flg', '#foot', 'true', this)\"");

    // 合計ボタン
    $form->addElement("button", "form_calc_btn", "合　計", "onClick=\"javascript:Button_Submit('hdn_calc_flg','#foot','true', this)\"");

    // 入金確認画面へボタン
    $form->addElement("submit", "form_verify_btn", "入金確認画面へ", "$disabled");

}

/* 登録直後の確認画面のみ表示 */
if ($verify_flg == true){

    // 入金OKボタン
    $form->addElement("button", "hdn_form_ok_btn", "入金ＯＫ", "onClick=\"Double_Post_Prevent2(this);\" $disabled");

    // 入金OKhidden
    $form->addElement("hidden", "form_ok_btn");

    // 戻るボタン
    $form->addElement("button", "form_return_btn", "戻　る", "onClick=\"javascript:SubMenu2('#')\"");
}


/****************************/
// 表示用html作成
/****************************/
// html用変数定義
$html = null;

// 最大行数分（削除済行含む）ループ
for ($i=0, $j=0; $i<$max_row; $i++){

    // 削除行履歴にある行は表示させない
    if (!in_array($i, $ary_del_row_history)){

        // 請求先の状態取得
        $claim_state_print[$i] = Get_Client_State($db_con, $_POST["hdn_claim_id"][$i]);

        // 請求先検索フラグがtrue＋ループカウンタが請求先検索行と同じ場合
        // （コードを直接入力された場合用 ※請求先IDがまだhdnに入ってないので）
        if ($_POST["hdn_claim_search_flg"] != null && ($i == array_search("true", $_POST["hdn_claim_search_flg"]))){
            $claim_state_print[$i] = Get_Client_State($db_con, $search_claim_id);
        }

        // html作成
        $html .= "<tr class=\"Result1\">\n";
        $html .= "<A NAME=\"".++$j."\"></A>\n";
        $html .= "  <td align=\"right\">".$j."</td>\n";                                                 // 行番号
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_claim_cd[$i]"]]->toHtml();          // 請求先コード1
        $html .= "      ".$claim_state_print[$i];
        // 確認画面でない場合
        if ($verify_flg != true){
        $html .= " (";
        $html .=        $form->_elements[$form->_elementIndex["form_claim_search[$i]"]]->toHtml();      // 請求先検索リンク
        $html .= ")";
        }
        $html .= "      <br>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_claim_cname[$i]"]]->toHtml();       // 請求先名（略称）
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_pay_name[$i]"]]->toHtml();          // 振込名義1
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_account_name[$i]"]]->toHtml();      // 振込名義2
        $html .= "      <br>";
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_payin_date[$i]"]]->toHtml();        // 入金日
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_trade[$i]"]]->toHtml();             // 取引区分
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bank_$i"]]->toHtml();               // 銀行
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bill_no[$i]"]]->toHtml();           // 請求番号
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bill_amount[$i]"]]->toHtml();       // 請求額
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_amount[$i]"]]->toHtml();            // 金額
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_rebate[$i]"]]->toHtml();            // 手数料
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_limit_date[$i]"]]->toHtml();        // 手形期日
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_bill_paper_no[$i]"]]->toHtml();     // 手形券面番号
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_note[$i]"]]->toHtml();              // 備考
        $html .= "  </td>\n";
        // 確認画面でない場合
        if ($verify_flg != true){
        $html .= "  <td class=\"Title_Add\" align=\"center\">\n";
        $html .=        $form->_elements[$form->_elementIndex["form_del_row[$i]"]]->toHtml();           // 削除リンク
        $html .= "  </td>\n";
        }
        $html .= "</tr>\n";

    }

}


/****************************/
// 親子関係のある請求先チェック
/****************************/
#2009-10-16 hashimoto-y
/*
if ($verify_flg != true){

    // 最大行数分（削除済行含む）ループ
    for ($i=0, $j=0; $i<$max_row; $i++){

        // 削除行履歴にある行は表示させない
        if (!in_array($i, $ary_del_row_history)){

            // 入力された請求先コードのIDを取得
            $sql  = "SELECT \n";
            $sql .= "   t_client.client_id \n";
            $sql .= "FROM \n";
            $sql .= "   t_client \n";
            $sql .= "   INNER JOIN t_claim ON t_client.client_id = t_claim.claim_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_client.client_cd1 = '".$_POST["form_claim_cd"][$i]["cd1"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_client.client_cd2 = '".$_POST["form_claim_cd"][$i]["cd2"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_client.client_div = '3' \n";
            $sql .= "AND \n";
            $sql .= "   t_client.shop_id = $shop_id \n";
            $sql .= ";"; 
            $res  = Db_Query($db_con, $sql);
            $num  = pg_num_rows($res);

            // 該当データがある場合
            if ($num > 0){

                // 対象の請求先IDを取得
                $target_claim_id = pg_fetch_result($res, 0, 0);

                // 「対象の請求先ID」の親子関係をチェックする
                $filiation_flg[] = Claim_Filiation_Chk($db_con, $target_claim_id);
            }else{  

                $filiation_flg[] = null; 

            }       

        }       

    }

}
*/


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
$page_menu = Create_Menu_h("sale", "4");

/****************************/
// 画面ヘッダー作成
/****************************/
$page_title .= Print_H_Link_Btn($form, $ary_h_btn_list);
$page_header = Create_Header($page_title);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form関連の変数をassign
$smarty->assign("form", $renderer->toArray());

// その他の変数をassign
$smarty->assign("var", array(
	"html_header"   => "$html_header",
	"page_menu"     => "$page_menu",
	"page_header"   => "$page_header",
	"html_footer"   => "$html_footer",
    "html"          => "$html",
    "verify_flg"    => "$verify_flg",
    "filiation_flg" => $filiation_flg,
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
