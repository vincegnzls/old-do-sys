<?php
/**
 *
 * 入金入力
 *
 * 1.0.0 (2006/xx/xx) 新規作成
 * 1.0.1 (2006/09/20) (kaji)
 *   ・月次更新、請求締処理より先の日付かチェックを関数に変更
 *
 * @author      ふくだ <ふくだ@bhsk.co.jp>
 * @version     1.0.1 (2006/09/20)
 *
 */

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/10/06      05-004      ふ          入金変更直前に日時更新処理が行われた場合の対処
 *  2006/10/06      05-005      ふ          入金変更直前に日時更新処理が行われた場合の対処
 *  2006/10/07      05-007      ふ          入金変更直前に入金データが削除された場合の対処
 *  2006/10/07      05-011      ふ          入金変更直前に入金データが削除され、さらに新規入金が行われた場合の対処
 *  2006/10/10      05-002      ふ          ブラウザの戻るまたは戻るボタン押下時、hierselectに値が復元されないバグに対応
 *  2007/01/25                  watanabe-k  ボタンの色変更
 *  2007-04-12                  fukuda      日次更新済明細時、請求先に必ず親が出力されてしまう不具合を修正
 *  2007-04-12                  fukuda      得意先・請求先選択時に、請求番号・請求額を出力
 *  2009-07-27                  aoyama-n    請求先を変更すると請求締日以前の日付で入金できてしまう不具合修正
 *  2009-10-16                  hashimoto-y 親子関係を選択したときの警告表示を廃止
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
$mod_me = "1-2-402";


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
$client_id          = $_POST["client_id"];              // 得意先ID
// POST(hidden)
$client_search_flg  = $_POST["client_search_flg"];      // 得意先検索フラグ
$calc_flg           = $_POST["calc_flg"];               // 合計算出フラグ
$state              = $_POST["hdn_state"];              // ステータス
$enter_date         = $_POST["hdn_enter_date"];         // 伝票作成日時
// GET
$payin_id           = $_GET["payin_id"];                // 入金ID


/*****************************/
// 可変初期値セット
/*****************************/
// 入金IDをhiddenに保持する
if ($_GET["payin_id"] != null){
    // 入金IDをhiddenにセット
    $payin_id_set["payin_id"] = $_GET["payin_id"];
    $form->setConstants($payin_id_set);
}else{
    // hiddenから入金ID取得
    $payin_id = $_POST["payin_id"];
}

// 得意先IDをhiddenに保持する
if ($_POST["client_id"] != null){
    // 得意先IDをhiddenにセット
    $client_id_set["client_id"] = $_POST["client_id"];
    $form->setConstants($client_id_set);
}

// POSTの判断用に自モジュール名をhiddenにセット
$post_set["post"] = $mod_me;
$form->setConstants($post_set);


/*****************************/
// 初期チェック
/*****************************/
/*** 新規登録・変更判断 ***/
// まだステータスが無い、かつ入金IDがある場合
if ($state == null && $payin_id != null){
    // 型変換
    $payin_id = (float)$payin_id;
    // 取得した入金IDの正当性をチェック
    $sql  = "SELECT ";
    $sql .= "   pay_id ";
    $sql .= "FROM ";
    $sql .= "   t_payin_h ";
    $sql .= "WHERE ";
    $sql .= "   pay_id = $payin_id ";
    $sql .= "AND ";
    $sql .= "   shop_id = $shop_id ";
    $sql .= ";"; 
    $res  = Db_Query($db_con, $sql);
    if (pg_num_rows($res) == 0){
        // TOPへ遷移
        header("Location: ../top.php");
    }else{  
        $state = "chg";
    }
}elseif ($state == null && $payin_id == null){
    $state = "new";
}
// ステータスをhiddenにセットデフォルト
$hdn_state_set["hdn_state"] = $state;
$form->setDefaults($hdn_state_set);

/*** 入金変更時に、対象となるデータの正当性を調べるための前準備 ***/
// 入金変更更、かつhiddenの伝票作成日時がnullの場合
if ($state == "chg" && $enter_date == null){
    // 入金日時を取得し、hiddenにセットデフォルト
    $sql  = "SELECT ";
    $sql .= "   enter_day ";
    $sql .= "FROM ";
    $sql .= "   t_payin_h ";
    $sql .= "WHERE ";
    $sql .= "   pay_id = $payin_id ";
    $sql .= "AND ";
    $sql .= "   shop_id = $shop_id ";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    if (pg_num_rows($res) > 0){
        $enter_date = pg_fetch_result($res, 0);
        $hdn_enter_date_set["hdn_enter_date"] = $enter_date;
        $form->setDefaults($hdn_enter_date_set);
    }
}

/*** 確認のみ（照会から遷移時）か判断 ***/
// 日次更新実施済または売上IDがある場合
if ($payin_id != null){
    $sql  = "SELECT ";
    $sql .= "   pay_id ";
    $sql .= "FROM ";
    $sql .= "   t_payin_h ";
    $sql .= "WHERE ";
    $sql .= "   pay_id = $payin_id ";
    $sql .= "AND ";
    $sql .= "   (renew_flg = 't' OR sale_id IS NOT NULL) ";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    // 該当レコードがある場合は確認画面フラグをtrueにする
    $verify_only_flg = (pg_num_rows($res) == 1) ? true : false;
}

// 入金変更中は日次更新されていてもフォームだけは操作できるように（変更を登録することはできません。最後で弾きます。）
$verify_only_flg = ($post == $mod_me && $_POST["form_ok_btn"] == null && $verify_only_flg == true) ? false : $verify_only_flg;


/****************************/
// formへdefaultデータセット
/****************************/
// 変更時かつ自ページからの遷移が無い場合、または入金OKボタン押下かつ明細画面フラグがtrue（日次更新が行われていた）の場合
if (($state == "chg" && $post != $mod_me) || ($_POST["form_ok_btn"] != null && $verify_only_flg == true)){

    /*** フォームセット用データ取得 ***/
    // 入金ヘッダ取得
    $sql  = "SELECT ";
    $sql .= "   t_payin_h.pay_no, ";
    $sql .= "   t_payin_h.client_id, ";
    $sql .= "   t_client.client_cd1, ";
    $sql .= "   t_client.client_cd2, ";
    $sql .= "   t_client.client_cname, ";
    $sql .= "   t_payin_h.client_cd1 AS verify_client_cd1, ";
    $sql .= "   t_payin_h.client_cd2 AS verify_client_cd2, ";
    $sql .= "   t_payin_h.client_cname AS verify_client_cname, ";
    $sql .= "   t_payin_h.pay_day, ";
    $sql .= "   t_payin_h.collect_staff_id, ";
    $sql .= "   t_bill.bill_id, ";
    $sql .= "   t_bill.bill_no, ";
    $sql .= "   t_payin_h.claim_div, ";
//    $sql .= "   t_claim_client.client_cd1 AS verify_claim_cd1, ";
//    $sql .= "   t_claim_client.client_cd2 AS verify_claim_cd2, ";
//    $sql .= "   t_claim_client.client_cname AS verify_claim_cname, ";
    $sql .= "   t_payin_h.claim_cd1 AS verify_claim_cd1, ";
    $sql .= "   t_payin_h.claim_cd2 AS verify_claim_cd2, ";
    $sql .= "   t_payin_h.claim_cname AS verify_claim_cname, ";
    $sql .= "   t_payin_h.renew_flg, ";
    $sql .= "   t_payin_h.sale_id ";
    $sql .= "FROM ";
    $sql .= "   t_payin_h ";
    $sql .= "   LEFT JOIN t_client ON t_payin_h.client_id = t_client.client_id ";
    $sql .= "   LEFT JOIN t_claim ON t_client.client_id = t_claim.client_id ";
    $sql .= "   LEFT JOIN t_client AS t_claim_client ";
    $sql .= "       ON t_claim.client_id = t_claim_client.client_id ";
    $sql .= "       AND t_payin_h.claim_div = t_claim.claim_div ";
    $sql .= "   LEFT JOIN t_bill ON t_payin_h.bill_id = t_bill.bill_id ";
    $sql .= "WHERE ";
    $sql .= "   pay_id = $payin_id ";
    $sql .= "AND ";
    $sql .= "   t_payin_h.shop_id = $shop_id ";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $payin_h_def_data = pg_fetch_array($res, 0);

    // 入金データ取得
    $sql  = "SELECT ";
    $sql .= "   t_payin_d.trade_id, ";
    $sql .= "   t_payin_d.amount,  ";
    $sql .= "   t_bank.bank_id, ";
    $sql .= "   t_payin_d.bank_cd AS verify_bank_cd, ";
    $sql .= "   t_payin_d.bank_name AS verify_bank_name, ";
    $sql .= "   t_b_bank.b_bank_id, ";
    $sql .= "   t_payin_d.b_bank_cd AS verify_b_bank_cd, ";
    $sql .= "   t_payin_d.b_bank_name AS verify_b_bank_name, ";
    $sql .= "   t_payin_d.account_id, ";
    $sql .= "   CASE t_payin_d.deposit_kind ";
    $sql .= "       WHEN '1' THEN '普通' ";
    $sql .= "       WHEN '2' THEN '当座' ";
    $sql .= "   END ";
    $sql .= "   AS verify_deposit, ";
    $sql .= "   t_payin_d.account_no AS verify_account_no, ";
    $sql .= "   t_payin_d.payable_day, ";
    $sql .= "   t_payin_d.payable_no, ";
    $sql .= "   t_payin_d.note ";
    $sql .= "FROM ";
    $sql .= "   t_payin_d ";
    $sql .= "   LEFT JOIN t_payin_h ON t_payin_d.pay_id = t_payin_h.pay_id ";
    $sql .= "   LEFT JOIN t_account ON t_payin_d.account_id = t_account.account_id ";
    $sql .= "   LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id ";
    $sql .= "   LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id ";
    $sql .= "WHERE ";
    $sql .= "   t_payin_h.pay_id = $payin_id ";
    $sql .= "AND ";
    $sql .= "   t_payin_h.shop_id = $shop_id ";
    $sql .= "ORDER BY ";
    $sql .= "   t_payin_d.pay_d_id ";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $payin_d_def_rows = pg_num_rows($res);
    while ($data_list = pg_fetch_array($res)){
        $payin_d_def_data[] = $data_list;
    }

    /*** フォームにデータをセット ***/
    // 入金ヘッダの取得データをフォームにセット
    $payin_h_def_fdata["form_payin_no"]         = $payin_h_def_data["pay_no"];
    $payin_h_def_fdata["form_client"]["cd1"]    = ($verify_only_flg != true) ? $payin_h_def_data["client_cd1"]
                                                                             : $payin_h_def_data["verify_client_cd1"];
    $payin_h_def_fdata["form_client"]["cd2"]    = ($verify_only_flg != true) ? $payin_h_def_data["client_cd2"]
                                                                             : $payin_h_def_data["verify_client_cd2"];
    $payin_h_def_fdata["form_client"]["name"]   = ($verify_only_flg != true) ? $payin_h_def_data["client_cname"]
                                                                             : $payin_h_def_data["verify_client_cname"];
    $payin_h_def_fdata["form_payin_date"]["y"]  = substr($payin_h_def_data["pay_day"], 0, 4);
    $payin_h_def_fdata["form_payin_date"]["m"]  = substr($payin_h_def_data["pay_day"], 5, 2);
    $payin_h_def_fdata["form_payin_date"]["d"]  = substr($payin_h_def_data["pay_day"], 8, 2);
    $payin_h_def_fdata["form_bill_no"]          = $payin_h_def_data["bill_no"];
    $payin_h_def_fdata["form_claim_select"]     = ($verify_only_flg != true) ? $payin_h_def_data["claim_div"]
                                                                             : htmlspecialchars($payin_h_def_data["verify_claim_cd1"])." - ".
                                                                               htmlspecialchars($payin_h_def_data["verify_claim_cd2"])." ".
                                                                               htmlspecialchars($payin_h_def_data["verify_claim_cname"]);
    $payin_h_def_fdata["client_id"]             = $payin_h_def_data["client_id"];
    $form->setConstants($payin_h_def_fdata);

    // 入金データの取得データをフォームにセット
    foreach ($payin_d_def_data as $key => $value){
        $payin_d_def_fdata["form_trade_$key"]               = $value["trade_id"];
        $payin_d_def_fdata["form_amount_$key"]              = $value["amount"];
        $payin_d_def_fdata["form_bank_$key"][0]             = ($verify_only_flg != true) ? $value["bank_id"]
                                                                                         : htmlspecialchars($value["verify_bank_cd"])." ： ".htmlspecialchars($value["verify_bank_name"]);
        $payin_d_def_fdata["form_bank_$key"][1]             = ($verify_only_flg != true) ? $value["b_bank_id"]
                                                                                         : htmlspecialchars($value["verify_b_bank_cd"])." ： ".htmlspecialchars($value["verify_b_bank_name"]);
        $payin_d_def_fdata["form_bank_$key"][2]             = ($verify_only_flg != true) ? $value["account_id"]
                                                                                         : htmlspecialchars($value["verify_deposit"])." ： ".htmlspecialchars($value["verify_account_no"]);
        $payin_d_def_fdata["form_limit_date_".$key."[y]"]   = substr($value["payable_day"], 0, 4);
        $payin_d_def_fdata["form_limit_date_".$key."[m]"]   = substr($value["payable_day"], 5, 2);
        $payin_d_def_fdata["form_limit_date_".$key."[d]"]   = substr($value["payable_day"], 8, 2);
        $payin_d_def_fdata["form_bill_paper_no_$key"]       = $value["payable_no"];
        $payin_d_def_fdata["form_note_$key"]                = $value["note"];
    }
    $form->setConstants($payin_d_def_fdata);

    // 得意先IDを変数へ代入
    $client_id = $payin_h_def_data["client_id"];
    // 請求IDを変数へ代入
    $form_bill_id = $payin_h_def_data["bill_id"];

}

// 新規時かつ自ページからの遷移が無い場合
if ($state == "new" && $post != $mod_me){

    // 入金番号を自動採番
    $sql  = "SELECT ";
    $sql .= "   MAX(pay_no) ";
    $sql .= "FROM ";
    $sql .= ($group_kind == "2") ? " t_payin_no_serial " : " t_payin_h ";   // 直営の時は入金番号テーブルから取得
    $sql .= ($group_kind != "2") ? " WHERE shop_id = $shop_id " : null;
    $sql .= ";"; 
    $res  = Db_Query($db_con, $sql);
    $payin_no = str_pad(pg_fetch_result($res, 0 ,0)+1, 8, "0", STR_PAD_LEFT);
    $def_data["form_payin_no"] = $payin_no;
    $form->setDefaults($def_data);

}


/****************************/
// 得意先フォームの入力・補完処理
/****************************/
// 得意先検索フラグがtrueの場合
if ($_POST["client_search_flg"] == true){

    // POSTされた得意先コードを変数へ代入
    $client_cd1 = $_POST["form_client"]["cd1"];
    $client_cd2 = $_POST["form_client"]["cd2"];

    // 得意先の情報を抽出
    $sql  = "SELECT ";
    $sql .= "   client_id,";
    $sql .= "   client_cname ";
    $sql .= "FROM ";
    $sql .= "   t_client ";
    $sql .= "WHERE ";
    $sql .= "   client_cd1 = '$client_cd1' ";
    $sql .= "AND ";
    $sql .= "   client_cd2 = '$client_cd2' ";
    $sql .= "AND ";
    $sql .= "   client_div = '3' ";
//    $sql .= "AND ";
//    $sql .= "   state = '1' ";
    $sql .= "AND ";
    $sql .= "   shop_id = $shop_id ";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);

    // 該当データがある場合
    if ($num > 0){
        $client_id      = pg_fetch_result($res, 0, 0);      // 得意先ID
        $client_name    = pg_fetch_result($res, 0, 1);      // 得意先名（略称）
        $claim_div      = 1;                                // 請求先区分１をデフォルトでセット
    }else{
        $client_id      = "";
        $client_name    = "";
        $claim_div      = "";
    }
    // 得意先コード入力フラグをクリア
    // 得意先ID、得意先名（略称）、請求先区分をフォームにセット
    $client_data["client_search_flg"]   = "";
    $client_data["client_id"]           = $client_id;
    $client_data["form_client"]["name"] = $client_name;
    $client_data["form_claim_select"]   = $claim_div;
    $form->setConstants($client_data);

}


/****************************/
// 行数関連処理
/****************************/
/*** 初期設定 ***/
// 最大行数がPOSTされている場合
if ($_POST["max_row"] != null){

    // POSTされた最大行数を変数へ代入
    $max_row = $_POST["max_row"];

// 最大行数がPOSTされていない場合
}else{

    // 変更の場合は入金データ件数
    // 新規の場合はデフォルト
    $max_row = ($state == "chg") ? $payin_d_def_rows : 5;

}

/*** 行追加設定 ***/
// 行追加フラグがtrueの場合
if ($_POST["add_row_flg"] == true){

    // 最大行+5
    $max_row = $max_row+5;

    // 行追加フラグをクリア
    $clear_hdn_flg["add_row_flg"] = "";
    $form->setConstants($clear_hdn_flg);

}

/*** 行数をhiddenに ***/
// 行数をhiddenに格納
$row_num_data["max_row"] = $max_row;
$form->setConstants($row_num_data);


// hiddenの行削除履歴がPOSTされた場合
if ($_POST["ary_del_rows"] != null){

    // これまでに削除された行全てを削除行履歴配列へ代入
    foreach ($_POST["ary_del_rows"] as $key => $value){
        $ary_del_row_history[] = $value;
    }

}

/*** 行削除設定 ***/
// 削除行番号がPOSTされた場合
if ($_POST["del_row_no"] != null){

    // 削除行番号取得
    $del_row_no = $_POST["del_row_no"];

    // 削除行番号を削除行履歴配列へ追加
    $ary_del_row_history[] = $_POST["del_row_no"];

    // 削除行履歴配列をhiddenにセット
    $del_rows_data["ary_del_rows"] = $ary_del_row_history;
    $form->setConstants($del_rows_data);

    // hiddenの削除行番号をクリア
    $clear_hdn_data["del_row_no"] = "";
    $form->setConstants($clear_hdn_data);

}

// 行削除が一度も行われていない場合
if ($_POST["ary_del_rows"] == null && $_POST["del_row_no"] == null){

    // 空の削除行格納用配列を作成
    $ary_del_row_history = array();

}


/****************************/
// 金額合計算出処理
/****************************/
// 金額合計算出フラグがtrue、または入金ボタンが押下された場合
if ($calc_flg == true || $_POST["form_verify_btn"] != null){

    $total_amount = 0;
    $rebate_amount = 0;

    // 行数分（削除済行含む）ループ
    for ($i=0; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){

            // 全ての金額を加算していく
            $total_amount += $_POST["form_amount_$i"];

            // 該当行の取引区分が手数料の場合
            $rebate_amount += ($_POST["form_trade_$i"] == 35) ? $_POST["form_amount_$i"] : null;

        }

    }

    // hiddenの金額合計フラグを削除
    // 金額の合計をフォームにセット
    $amount_sum_data["calc_flg"]            = "";
    $amount_sum_data["form_amount_total"]   = number_format($total_amount - $rebate_amount);
    $amount_sum_data["form_rebate_total"]   = number_format($rebate_amount);
    $amount_sum_data["form_payin_total"]    = number_format($total_amount);
    $form->setConstants($amount_sum_data);

}

// 変更時かつ自ページからの遷移が無い場合
if ($state == "chg" && $post != $mod_me){

    $total_amount = 0;
    $rebate_amount = 0;

    foreach ($payin_d_def_data as $key => $value){

        // 全ての金額を加算していく
        $total_amount += $value["amount"];

        // 該当レコードの取引区分が手数料の場合
        $rebate_amount += ($value["trade_id"] == 35) ? $value["amount"] : 0;

    }

    // 金額の合計をフォームにセット
    $amount_sum_data["form_amount_total"]   = number_format($total_amount - $rebate_amount);
    $amount_sum_data["form_rebate_total"]   = number_format($rebate_amount);
    $amount_sum_data["form_payin_total"]    = number_format($total_amount);
    $form->setConstants($amount_sum_data);

}


/****************************/
// フォームパーツ定義
/****************************/
// ヘッダ部リンクボタン
$ary_h_btn_list = array("照会・変更" => "./1-2-403.php", "入　力" => $_SERVER["PHP_SELF"]);
Make_H_Link_Btn($form, $ary_h_btn_list);

// 入金番号
$form->addElement(
    "text", "form_payin_no", "",
    "size=\"11\" maxLength=\"8\" style=\"color: #525552; border: #ffffff 1px solid; background-color: #ffffff; text-align: left\" readonly'"
);

// 得意先
$text = null;
$verify_freeze_header[] = $text[] =& $form->createElement("text", "cd1", "", 
    "size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
     onChange=\"javascript:Change_Submit('client_search_flg','#','true','form_client[cd2]')\"
     onkeyup=\"changeText(this.form,'form_client[cd1]','form_client[cd2]',6)\" ".$g_form_option."\""
);
$verify_freeze_header[] = $text[] =& $form->createElement("static", "", "", "-");
$verify_freeze_header[] = $text[] =& $form->createElement("text", "cd2", "",
    "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
     onChange=\"javascript:Button_Submit('client_search_flg','#','true')\" ".$g_form_option."\""
);
$verify_freeze_header[] = $text[] =& $form->createElement("text", "name", "", "size=\"34\" $g_text_readonly");
$form->addGroup($text, "form_client", "");

// 銀行
$form->addElement("static", "form_c_bank", "", "");
// 支店
$form->addElement("static", "form_c_b_bank", "", "");
// 口座
$form->addElement("static", "form_c_account", "", "");

// 入金日
$text = null;
$verify_freeze_header[] = $text[] =& $form->createElement("text", "y", "", 
    "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
     onkeyup=\"changeText(this.form,'form_payin_date[y]','form_payin_date[m]',4)\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date[y]','form_payin_date[m]','form_payin_date[d]')\"
     onBlur=\"blurForm(this)\""
);
$verify_freeze_header[] = $text[] =& $form->createElement("static", "", "", "-");
$verify_freeze_header[] = $text[] =& $form->createElement("text", "m", "",
    "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
     onkeyup=\"changeText(this.form,'form_payin_date[m]','form_payin_date[d]',2)\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date[y]','form_payin_date[m]','form_payin_date[d]')\"
     onBlur=\"blurForm(this)\""
);
$verify_freeze_header[] = $text[] =& $form->createElement("static", "", "", "-");
$verify_freeze_header[] = $text[] =& $form->createElement("text", "d", "",
    "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date[y]','form_payin_date[m]','form_payin_date[d]')\"
     onBlur=\"blurForm(this)\""
);
$form->addGroup($text, "form_payin_date", "");

// 請求番号
/*
$verify_freeze_header[] = $form->addElement("text", "form_bill_no", "",
    "size=\"9\" maxlength=\"8\" style=\"$g_form_style\" $g_form_option"
);
*/
$form->addElement("hidden", "form_bill_no");

// 請求先
// 確認画面の場合（登録直後は含まない）
if ($verify_only_flg == true){
    $form->addElement("static", "form_claim_select", "", "");
// 変更画面の場合（登録直後の確認画面を含む）
}else{
    $select_value = "";
    $select_value = ($client_id != null) ? Select_Get($db_con, "claim_payin", "t_claim.client_id = $client_id ") : null;
    unset($select_value[null]);
//    $verify_freeze_header[] = $form->addElement("select", "form_claim_select", "", $select_value, $g_form_option_select);
    $verify_freeze_header[] = $form->addElement("select", "form_claim_select", "", $select_value, "onChange=\"Pay_Account_Name(); Bill_No_Amount();\"");
}

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
$form->addElement("hidden", "hdn_state", null, null);               // ステータス
$form->addElement("hidden", "client_search_flg", null, null);       // 得意先コード入力フラグ
$form->addElement("hidden", "max_row", null, null);                 // 最大行数
$form->addElement("hidden", "add_row_flg", null, null);             // 行追加フラグ
$form->addElement("hidden", "del_row_no", null, null);              // 削除行番号
$form->addElement("hidden", "calc_flg", null, null);                // 金額合計算出フラグ
for ($i=0; $i<count($ary_del_row_history); $i++){
    $form->addElement("hidden", "ary_del_rows[$i]", null, null);    // 削除行番号履歴   # 削除行履歴分作成する
}
$form->addElement("hidden", "payin_id", null, null);                // 入金ID
$form->addElement("hidden", "client_id", null, null);               // 得意先ID
$form->addElement("hidden", "post", null, null);                    // ページPOST
$form->addElement("hidden", "hdn_enter_date", null, null);          // 伝票作成日時
$form->addElement("hidden", "hdn_enter_date", null, null);          // 伝票作成日時
$form->addElement("hidden", "hdn_c_bank_cd", null, null);           // 銀行コード
$form->addElement("hidden", "hdn_c_bank_name", null, null);         // 銀行名
$form->addElement("hidden", "hdn_c_b_bank_cd", null, null);         // 支店コード
$form->addElement("hidden", "hdn_c_b_bank_name", null, null);       // 支店名
$form->addElement("hidden", "hdn_c_deposit_kind", null, null);      // 預金種目
$form->addElement("hidden", "hdn_c_account_no", null, null);        // 口座番号

// エラーセット専用フォーム
$form->addElement("text", "err_illegal_verify", null, null);        // 不正POST
$form->addElement("text", "err_noway_forms", null, null);           // 入金データ未入力
$form->addElement("text", "err_plural_rebate", null, null);         // 手数料複数行
// 最大行数分（削除済行含む）ループ
for ($i=0, $j=1; $i<$max_row; $i++){
    // 削除行履歴にある行はスルー
    if (!in_array($i, $ary_del_row_history)){
        $form->addElement("text", "err_trade1[$j]", null, null);        // 取引区分1
        $form->addElement("text", "err_amount1[$j]", null, null);       // 金額1
        $form->addElement("text", "err_amount2[$j]", null, null);       // 金額2
        $form->addElement("text", "err_bank1[$j]", null, null);         // 銀行1
        $form->addElement("text", "err_bank2[$j]", null, null);         // 銀行2
        $form->addElement("text", "err_limit_date1[$j]", null, null);   // 手形期日1
        $form->addElement("text", "err_limit_date2[$j]", null, null);   // 手形期日2
        $form->addElement("text", "err_limit_date3[$j]", null, null);   // 手形期日3
    }
    $j++;
}


/****************************/
// 得意先コードの編集中に入金確認画面へボタンが押下された場合の対処処理
/****************************/
// 入金確認画面へボタンが押された、かつ得意先検索フラグtrueの場合
if ($_POST["form_verify_btn"] != null && $_POST["client_search_flg"] == true){

    // hiddenに得意先IDが格納されている場合
    if ($_POST["client_id"] != null){
        // hiddenに格納されている得意先IDとPOSTされた得意先コードの整合性をチェック
        $sql  = "SELECT ";
        $sql .= "   client_id ";
        $sql .= "FROM ";
        $sql .= "   t_client ";
        $sql .= "WHERE ";
        $sql .= "   client_id = ".$_POST["client_id"]." ";
        $sql .= "AND ";
        $sql .= "   client_cd1 = '".$_POST["form_client"]["cd1"]."' ";
        $sql .= "AND ";
        $sql .= "   client_cd2 = '".$_POST["form_client"]["cd2"]."' ";
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
    // hiddenに得意先IDが格納されていない場合
    }else{
        // 不正POSTフラグをtrueに
        $illegal_verify_flg = true;
    }

    // 不正POSTフラグtrueの場合はエラーをセット
    if ($illegal_verify_flg == true){
        $form->setElementError("err_illegal_verify", "得意先情報取得前に 入金確認画面へボタン が押されました。<br>操作をやり直してください。");
    }

}


/****************************/
// エラーチェック - addRule
/****************************/
// 入金確認画面へボタンが押下された場合
if ($_POST["form_verify_btn"] != null){

    /*** チェック処理し易い様、POSTデータを変数へ ***/
    // ヘッダ部フォームデータを変数へ
    $post_payin_no      = $_POST["form_payin_no"];
    $post_payin_date_y  = $_POST["form_payin_date"]["y"];
    $post_payin_date_m  = $_POST["form_payin_date"]["m"];
    $post_payin_date_d  = $_POST["form_payin_date"]["d"];
    $post_bill_no       = $_POST["form_bill_no"];
    $post_claim_div     = $_POST["form_claim_select"];

    // データ部フォームデータを変数へ
    // 最大行数分（削除済行含む）ループ
    for ($i=0; $i<$max_row; $i++){
        // 削除行履歴にある行は表示させない
        if (!in_array($i, $ary_del_row_history)){
            $post_trade[$i]         = $_POST["form_trade_$i"];
            $post_amount[$i]        = $_POST["form_amount_$i"];
            $post_bank[$i]          = $_POST["form_bank_$i"][0];
            $post_b_bank[$i]        = $_POST["form_bank_$i"][1];
            $post_account[$i]       = $_POST["form_bank_$i"][2];
            $post_limit_date_y[$i]  = $_POST["form_limit_date_$i"]["y"];
            $post_limit_date_m[$i]  = $_POST["form_limit_date_$i"]["m"];
            $post_limit_date_d[$i]  = $_POST["form_limit_date_$i"]["d"];
            $post_bill_paper_no[$i] = $_POST["form_bill_paper_no_$i"];
            $post_note[$i]          = $_POST["form_note_$i"];
        }
    }

    /****************************/
    // ヘッダ部チェック
    /****************************/
    // 不正POSTフラグがtrueでない場合
    if ($illegal_verify_flg != true){
        /*** 得意先 ***/
        // ■必須チェック
        $err_msg = "正しい 得意先コード を入力して下さい。";
        $form->addGroupRule("form_client", array(
            "cd1" => array(
                array($err_msg, "required"),
                array($err_msg, "regex", "/^[0-9]+$/")
            ),
            "cd2" => array(
                array($err_msg, "required"),
                array($err_msg, "regex", "/^[0-9]+$/")
            ),
            "name" => array(
                array($err_msg, "required")
            )
        ));
    }

    /*** 入金日 ***/
    // ■必須チェック
    // ■半角数字チェック
    $err_msg = "入金日 の日付が妥当ではありません。";
    $form->addGroupRule("form_payin_date", array(
        "y" => array(
            array($err_msg, "required"),
            array($err_msg, "regex", "/^[0-9]+$/")
        ),      
        "m" => array(
            array($err_msg, "required"),
            array($err_msg, "regex", "/^[0-9]+$/")
        ),       
        "d" => array(
            array($err_msg, "required"),
            array($err_msg, "regex", "/^[0-9]+$/")
        )
    ));

    /*** 請求先 ***/
    // ■必須チェック
    $form->addRule("form_claim_select", "請求先 が選択されていません。", "required");

    /****************************/
    // addRule適用結果収集
    /****************************/
    /*** チェック適用 ***/
    $form->validate();

    /*** 結果集計 ***/
    // エラーのあるフォームのフォーム名を格納するための配列を作成
    $ary_addrule_err_forms = array();
    // エラーのあるフォームのフォーム名を配列に格納
    foreach ($form as $key1 => $value1){
        if ($key1 == "_errors"){
            foreach ($value1 as $key2 => $value2){
                $ary_addrule_err_forms[] = $key2;
            }
        }
    }

}


/****************************/
// エラーチェック - PHP
/****************************/
// 入金確認画面へボタンが押下された場合
if ($_POST["form_verify_btn"] != null){

    // 請求先選択エラーが無い、かつ不正POSTフラグがtrueでない場合
    if (!in_array("form_claim_select", $ary_addrule_err_forms) && $illegal_verify_flg != true){
        // 選択された請求先の請求先IDを取得
        $sql  = "SELECT ";
        $sql .= "   t_client.client_id ";
        $sql .= "FROM ";
        $sql .= "   t_claim ";
        $sql .= "   INNER JOIN t_client ON t_claim.claim_id = t_client.client_id ";
        $sql .= "WHERE ";
        $sql .= "   t_claim.client_id = $client_id ";
        $sql .= "AND ";
        $sql .= "   t_claim.claim_div = $post_claim_div ";
        $sql .= "AND ";
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $claim_id = @pg_fetch_result($res, 0);
    }

    /****************************/
    // ヘッダ部チェック
    /****************************/
    /*** 入金日 ***/
    // ■日付としての妥当性チェック
    if (!in_array("form_payin_date", $ary_addrule_err_forms)){
        // 日付としてエラーの場合
        if(!checkdate((int)$post_payin_date_m, (int)$post_payin_date_d, (int)$post_payin_date_y)){
            // エラーをセット
            $form->setElementError("form_payin_date", "入金日 の日付が妥当ではありません。");
            $payin_date_err_flg = true;
        }
    }

    // ■システム開始日以前チェック
    if (!in_array("form_payin_date", $ary_addrule_err_forms) && $payin_date_err_flg != true){
        $chk_res = Sys_Start_Date_Chk($post_payin_date_y, $post_payin_date_m, $post_payin_date_d, "入金日");
        if ($chk_res != null){
            $form->setElementError("form_payin_date", $chk_res);
            $payin_date_err_flg = true;
        }
    }

    // ■未来日付が入力されていないかチェック
    if (!in_array("form_payin_date", $ary_addrule_err_forms) && $payin_date_err_flg != true){
        $post_payin_date_y2 = str_pad($post_payin_date_y, 4, "0", STR_PAD_LEFT);
        $post_payin_date_m2 = str_pad($post_payin_date_m, 2, "0", STR_PAD_LEFT);
        $post_payin_date_d2 = str_pad($post_payin_date_d, 2, "0", STR_PAD_LEFT);
        // 未来日付の場合
        if (date("Y-m-d") < $post_payin_date_y2."-".$post_payin_date_m2."-".$post_payin_date_d2){
            // エラーをセット
            $form->setElementError("form_payin_date", "入金日 が未来の日付になっています。");
            $payin_date_err_flg = true;
        }
    }

    // ■最新の月次更新以前の日付が入力されていないかチェック
    if (!in_array("form_payin_date", $ary_addrule_err_forms) && $payin_date_err_flg != true){
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
            $post_payin_date_y2 = str_pad($post_payin_date_y, 4, "0", STR_PAD_LEFT);
            $post_payin_date_m2 = str_pad($post_payin_date_m, 2, "0", STR_PAD_LEFT);
            $post_payin_date_d2 = str_pad($post_payin_date_d, 2, "0", STR_PAD_LEFT);
            if ($post_payin_date_y2."-".$post_payin_date_m2."-".$post_payin_date_d2 <= $last_monthly_renew_date){
                // エラーをセット
                $form->setElementError("form_payin_date", "入金日 に前回の月次更新以前の日付が入力されています。");
                $payin_date_err_flg = true;
            }
        }
    }

    // ■前回の請求締日以前の日付が入力されていないかチェック
    if ($payin_date_err_flg != true && !in_array("form_claim_select", $ary_addrule_err_forms) && $illegal_verify_flg != true){
        // 最新の請求の締日を取得
        $sql  = "SELECT ";
        $sql .= "   MAX(t_bill_d.bill_close_day_this) ";
        $sql .= "FROM ";
        $sql .= "   t_bill ";
        $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id ";
        $sql .= "WHERE ";
        //aoyama-n 2009-07-27
        #$sql .= "   t_bill.claim_id = $claim_id ";
        $sql .= "   t_bill_d.claim_div = '1' ";
        $sql .= "AND ";
        $sql .= "   t_bill_d.client_id = $client_id ";
        $sql .= "AND ";
        $sql .= "   t_bill.shop_id = $shop_id ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        $last_close_date = ($num > 0) ? pg_fetch_result($res, 0) : null;
        // 締日がある場合
        if ($last_close_date != null){
            // 入力された入金日が、前回の請求締日以前の場合
            $post_payin_date_y = str_pad($post_payin_date_y, 4, "0", STR_PAD_LEFT);
            $post_payin_date_m = str_pad($post_payin_date_m, 2, "0", STR_PAD_LEFT);
            $post_payin_date_d = str_pad($post_payin_date_d, 2, "0", STR_PAD_LEFT);
            if ($post_payin_date_y."-".$post_payin_date_m."-".$post_payin_date_d <= $last_close_date){
                // エラーをセット
                $form->setElementError("form_payin_date", "入金日 に前回の請求締日以前の日付が入力されています。");
//                $form->setElementError("form_payin_date","入金日 に請求書作成済の日付が入力されています。<br>入金日を変更するか、請求書を削除して下さい。");
                $payin_date_err_flg = true;
            }
        }
    }

/*
    //前回月次更新より先の日付かチェック
    if ($payin_date_err_flg != true && !in_array("form_client", $ary_addrule_err_forms) &&
        !in_array("form_payin_date", $ary_addrule_err_forms)){
        if(Check_Monthly_Renew($db_con, $client_id, "1", $post_payin_date_y, $post_payin_date_m, $post_payin_date_d) == false){
            $form->setElementError("form_payin_date","入金日 に前回の月次更新以前の日付が入力されています。");
            $payin_date_err_flg = true;
        }
    }

    //前回請求締日より先の日付かチェック
    if ($payin_date_err_flg != true && !in_array("form_claim_select", $ary_addrule_err_forms) &&
        !in_array("form_payin_date", $ary_addrule_err_forms)){
        if(Check_Bill_Close_Day($db_con, $claim_id, $post_payin_date_y, $post_payin_date_m, $post_payin_date_d) == false){
            $form->setElementError("form_payin_date","入金日 に請求書作成済の日付が入力されています。<br>入金日を変更するか、請求書を削除して下さい。");
            $payin_date_err_flg = true;
        }
    }
*/

    /*** 請求番号 ***/
    // ■請求番号の妥当性チェック
/*
    if ($post_bill_no != null && !in_array("form_client", $ary_addrule_err_forms) &&
        !in_array("form_claim_select", $ary_addrule_err_forms) &&
        $illegal_verify_flg != true
    ){
        // フォーム入力された内容の請求番号が妥当か調べる
        $sql  = "SELECT \n";
        $sql .= "   t_bill.bill_id \n";
        $sql .= "FROM \n";
        $sql .= "   t_bill \n";
        $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
        $sql .= "WHERE \n";
        $sql .= "   t_bill.bill_no = '$post_bill_no' \n";
        $sql .= "AND \n";
        $sql .= "   t_bill.claim_id IN (SELECT claim_id FROM t_claim WHERE client_id = $client_id AND claim_div = '$post_claim_div') \n";
        $sql .= "AND \n";
        $sql .= "   t_bill.shop_id = ".$_SESSION["client_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        // 無い場合
        if ($num == 0){
            // エラーをセット
            $form->setElementError("form_bill_no", "請求番号 に誤りがあります。");
            $bill_no_flg = true;
        }
    }
*/

    /****************************/
    // データ部チェック
    /****************************/
    // 最大行数分（削除済行含む）ループ
    for ($i=0, $j=1; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){

            // 該当行のフォームに1つでも入力があればエラーチェックを行う
            if ($post_trade[$i]         != null ||
                $post_amount[$i]        != null ||
                $post_bank[$i]          != null ||
                $post_limit_date_y[$i]  != null ||
                $post_limit_date_m[$i]  != null ||
                $post_limit_date_d[$i]  != null ||
                $post_bill_paper_no[$i] != null ||
                $post_note[$i]          != null)
            {

                /*** 取引区分 ***/
                // ■必須チェック
                if ($post_trade[$i] == null){
                    $form->setElementError("err_trade1[$j]", $j."行目　取引区分 は必須です。");
                    $trade_err_flg[$i] = true;
                }

                /*** 金額 ***/
                // ■必須チェック
                if ($post_amount[$i] == null){
                    $form->setElementError("err_amount1[$j]", $j."行目　金額 は必須です。");
                    $amount_err_flg[$i] = true;
                }
                // ■数値チェック
                if ($amount_err_flg[$i] != true && $post_amount[$i] != null && !ereg("^[-]?[0-9]+$", $post_amount[$i])){
                    $form->setElementError("err_amount2[$j]", $j."行目　金額 は数値のみ入力可能です。");
                    $amount_err_flg[$i] = true;
                }

                /*** 銀行 ***/
                // ■条件付必須チェック
                if (($post_trade[$i] == 32 || $post_trade[$i] == 33 || $post_trade[$i] == 35) && $post_bank[$i] == null){
                    $form->setElementError("err_bank1[$j]", $j."行目　取引区分32, 33, 35の場合は 銀行・支店・口座番号 は必須です。");
                    $bank_err_flg[$i] = true;
                }
                // ■半端入力チェック
                if ($bank_err_flg != true &&
                    !(($post_bank[$i] != null && $post_b_bank[$i] != null && $post_account[$i] != null) ||
                      ($post_bank[$i] == null && $post_b_bank[$i] == null && $post_account[$i] == null))){
                    $form->setElementError("err_bank2[$j]", $j."行目　銀行 入力時は 口座番号 まで入力してください。");
                    $bank_err_flg[$i] = true;
                }

                /*** 手形期日 ***/
                // ■条件付必須チェック
                if ($post_trade[$i] == 33 &&
                    ($post_limit_date_y[$i] == null && $post_limit_date_m[$i] == null && $post_limit_date_d[$i] == null)){ 
                    $form->setElementError("err_limit_date2[$j]", $j."行目　取引区分が 33 の場合は、手形期日は必須です。");
                    $limit_date_err_flg[$i] = true;
                }  
                // ■数値チェック
                if ($limit_date_err_flg[$i] != true &&
                    ($post_limit_date_y[$i] != null || $post_limit_date_m[$i] != null || $post_limit_date_d[$i] != null) &&
                    (!ereg("^[0-9]+$", $post_limit_date_y[$i]) ||
                     !ereg("^[0-9]+$", $post_limit_date_m[$i]) ||
                     !ereg("^[0-9]+$", $post_limit_date_d[$i]))
                ){
                    $form->setElementError("err_limit_date1[$j]", $j."行目　手形期日 の日付が妥当ではありません。");
                    $limit_date_err_flg[$i] = true; 
                }

                // ■システム開始日以前チェック
                if ($limit_date_err_flg[$i] != true &&
                    ($post_limit_date_y[$i] != null || $post_limit_date_m[$i] != null || $post_limit_date_d[$i] != null)){
                    $chk_res = Sys_Start_Date_Chk($post_limit_date_y[$i], $post_limit_date_m[$i], $post_limit_date_d[$i], "手形期日");
                    if ($chk_res != null){
                        // エラーをセット
                        $form->setElementError("err_limit_date3[$j]", $j."行目　".$chk_res);
                        $limit_date_err_flg[$i] = true; 
                    }
                }
                // ■日付として妥当かチェック
                if ($limit_date_err_flg[$i] != true && 
                    ($post_limit_date_y[$i] != null || $post_limit_date_m[$i] != null || $post_limit_date_m[$i] != null)){ 
                    $post_limit_date_y[$i] = (int)$post_limit_date_y[$i];
                    $post_limit_date_m[$i] = (int)$post_limit_date_m[$i];
                    $post_limit_date_d[$i] = (int)$post_limit_date_d[$i];
                    if (!checkdate($post_limit_date_m[$i], $post_limit_date_d[$i], $post_limit_date_y[$i])){
                        $form->setElementError("err_limit_date1[$j]", $j."行目　手形期日 の日付が妥当ではありません。");
                        $limit_date_err_flg[$i] = true; 
                    }
                }

                // 取引区分「手数料」の行をカウント
                ($post_trade[$i] == "35") ? $rebate_rows[] = $i : null; 

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

    /*** 入金データ ***/
    // ■入力0件チェック
    // 実際に表示されている行数と、入力の無い行数が同じ場合
    if (count($ary_noway_forms) == count($ary_all_forms)){
        $form->setElementError("err_noway_forms", "入金データ を入力して下さい。");
    }

    // 取引区分「手数料」複数行チェック
    if (count($rebate_rows) > 1){
        $form->setElementError("err_plural_rebate", "手数料 は1行のみ入力可能です。");
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
// ヘッダフォームフリーズ
/****************************/
// 確認フラグがtrue、または確認のみフラグがtrueの場合
if ($verify_flg == true || $verify_only_flg == true){

    // ヘッダフォームをフリーズ
    $verify_freeze_header_form = $form->addGroup($verify_freeze_header, "verify_freeze_header", "");
    $verify_freeze_header_form->freeze();

}


/****************************/
// ・入金変更時、
// ・確認画面で
// ・入金OKボタンを押したら
// ・既に日次更新されてた場合
/****************************/
// 入金OKボタン押下かつ明細フラグがtrueの場合
if ($_POST["form_ok_btn"] != null && $verify_only_flg == true){

    // ちょうど今、日次更新されてしまいましたフラグをtrueに
    $just_daily_update_flg = true;

    // フォームに元の入金データを復元させる処理は上のほうにあります。

}


/****************************/
// ・入金変更時、
// ・確認画面で
// ・入金OKボタンを押したら
// ・既に削除されてた場合
/****************************/
// 入金OKボタン押下かつステータスがchgの場合
if ($_POST["form_ok_btn"] != null && $state == "chg"){

    // 入金ID、伝票作成時間と入金データの突合せ
    $same_data_flg = Update_Check($db_con, "t_payin_h", "pay_id", $payin_id, $enter_date);

    // 突合せエラーの場合
    if ($same_data_flg == false){
        // エラーフラグをGETに持たせ、完了画面へ遷移
        header("Location: ./1-2-408.php?ref=view&err=1");
        exit;
    }

}


/****************************/
// 登録処理等
/****************************/
// 入金OKボタン押下かつ明細フラグがtrueでない場合
if ($_POST["form_ok_btn"] != null && $verify_only_flg != true){

    /*** POSTデータを変数へ ***/
    // ヘッダ部フォームデータを変数へ
    $post_payin_no          = $_POST["form_payin_no"];
    $post_client_cd1        = $_POST["form_client"]["cd1"];
    $post_client_cd2        = $_POST["form_client"]["cd2"];
    $post_client_name       = $_POST["form_client"]["name"];
    $post_c_bank_cd         = html_entity_decode($_POST["hdn_c_bank_cd"]);
    $post_c_bank_name       = html_entity_decode($_POST["hdn_c_bank_name"]);
    $post_c_b_bank_cd       = html_entity_decode($_POST["hdn_c_b_bank_cd"]);
    $post_c_b_bank_name     = html_entity_decode($_POST["hdn_c_b_bank_name"]);
    $post_c_deposit_kind    = html_entity_decode($_POST["hdn_c_deposit_kind"]);
    $post_c_account_no      = html_entity_decode($_POST["hdn_c_account_no"]);
    $post_payin_date_y      = str_pad($_POST["form_payin_date"]["y"], 4, "0", STR_PAD_LEFT);
    $post_payin_date_m      = str_pad($_POST["form_payin_date"]["m"], 2, "0", STR_PAD_LEFT);
    $post_payin_date_d      = str_pad($_POST["form_payin_date"]["d"], 2, "0", STR_PAD_LEFT);
    $post_payin_date        = $post_payin_date_y."-".$post_payin_date_m."-".$post_payin_date_d;
    $post_bill_no           = $_POST["form_bill_no"];
    $post_claim_div         = $_POST["form_claim_select"];

    // データ部フォームデータを変数へ
    // 最大行数分（削除済行含む）ループ
    for ($i=0; $i<$max_row; $i++){

        // 削除行履歴にある行はスルー
        if (!in_array($i, $ary_del_row_history)){

            // 該当行のフォームに1つでも入力がある場合
            if ($_POST["form_trade_$i"]             != null ||
                $_POST["form_amount_$i"]            != null ||
                $_POST["form_bank_$i"][0]           != null ||
                $_POST["form_bank_$i"][1]           != null ||
                $_POST["form_bank_$i"][2]           != null ||
                $_POST["form_limit_date_$i"]["y"]   != null ||
                $_POST["form_limit_date_$i"]["m"]   != null ||
                $_POST["form_limit_date_$i"]["d"]   != null ||
                $_POST["form_bill_paper_no_$i"]     != null ||
                $_POST["form_note_$i"]              != null)
            {

                $post_trade[$i]         = $_POST["form_trade_$i"];
                $post_amount[$i]        = $_POST["form_amount_$i"];
                $post_bank[$i]          = $_POST["form_bank_$i"][0];
                $post_b_bank[$i]        = $_POST["form_bank_$i"][1];
                $post_account[$i]       = $_POST["form_bank_$i"][2];
                if ($_POST["form_limit_date_$i"]["y"] != null ||
                    $_POST["form_limit_date_$i"]["m"] != null ||
                    $_POST["form_limit_date_$i"]["d"] != null)
                {
                    $post_limit_date_y[$i]  = str_pad($_POST["form_limit_date_$i"]["y"], 4, "0", STR_PAD_LEFT);
                    $post_limit_date_m[$i]  = str_pad($_POST["form_limit_date_$i"]["m"], 2, "0", STR_PAD_LEFT);
                    $post_limit_date_d[$i]  = str_pad($_POST["form_limit_date_$i"]["d"], 2, "0", STR_PAD_LEFT);
                    $post_limit_date[$i]    = $post_limit_date_y[$i]."-".$post_limit_date_m[$i]."-".$post_limit_date_d[$i];
                }else{
                    $post_limit_date[$i]    = null;
                }
                $post_bill_paper_no[$i] = $_POST["form_bill_paper_no_$i"];
                $post_note[$i]          = $_POST["form_note_$i"];

                // 実際に入力がある行の行番号のみを配列に代入していく
                $ary_insert_forms[] = $i;

            }

        }

    }

    /*** 請求先情報を取得 ***/
    // 請求番号が入力されている場合
    if ($post_bill_no != null){

        // 請求番号がある場合は請求書IDも取得
        $sql  = "SELECT ";
        $sql .= "   t_bill.bill_id, ";
        $sql .= "   t_bill.claim_cd1, ";
        $sql .= "   t_bill.claim_cd2, ";
        $sql .= "   t_bill.claim_cname ";
        $sql .= "FROM ";
        $sql .= "   t_bill ";
        $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id ";
        $sql .= "WHERE ";
        $sql .= "   t_bill.bill_no = '$post_bill_no' ";
        $sql .= "AND ";
        $sql .= "   t_bill_d.client_id = $client_id ";
        $sql .= "AND ";
        $sql .= "   t_bill_d.claim_div = $post_claim_div ";
//        $sql .= "AND ";
//        $sql .= "   t_bill.last_update_flg = 'f' ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        while ($data_list = @pg_fetch_array($res)){
            $bill_id        = $data_list["bill_id"];
            $claim_cd1      = $data_list["claim_cd1"];
            $claim_cd2      = $data_list["claim_cd2"];
            $claim_cname    = $data_list["claim_cname"];
        }

    // 請求番号が未入力の場合
    }else{

        $sql  = "SELECT ";
        $sql .= "   t_client.client_cd1, ";
        $sql .= "   t_client.client_cd2, ";
        $sql .= "   t_client.client_cname ";
        $sql .= "FROM ";
        $sql .= "   t_claim ";
        $sql .= "   INNER JOIN t_client ON t_claim.claim_id = t_client.client_id ";
        $sql .= "WHERE ";
        $sql .= "   t_claim.client_id = $client_id ";
        $sql .= "AND ";
        $sql .= "   t_claim.claim_div = $post_claim_div ";
        $sql .= "AND ";
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        while ($data_list = @pg_fetch_array($res)){
            $claim_cd1      = $data_list["client_cd1"];
            $claim_cd2      = $data_list["client_cd2"];
            $claim_cname    = $data_list["client_cname"];
        }

    }

    /****************************/
    // エラーチェック
    /****************************/
    /*** 入金番号 ***/
    // ■重複チェック
    if ($state == "new"){
        $sql  = "SELECT ";
        $sql .= "   pay_no ";
        $sql .= "FROM ";
        $sql .= "   t_payin_h ";
        $sql .= "WHERE ";
        $sql .= "   pay_no = '$post_payin_no' ";
        $sql .= "AND ";
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        $duplicate_err_flg = ($num != 0) ? true : false;
        $duplicate_err_msg = ($num != 0) ? "同時に入金を行ったため、伝票番号が重複しました。もう一度入金を行ってください。" : null;
    }

    /****************************/
    // DB処理
    /****************************/
    /*** トランザクション開始 ***/
    Db_Query($db_con, "BEGIN;");

    /*** 新規時 ***/
    if ($state == "new" && $duplicate_err_flg != true){

        /* 入金ヘッダINSERT */
        $sql  = "INSERT INTO ";
        $sql .= "   t_payin_h ";
        $sql .= "( ";
        $sql .= "   pay_id, ";
        $sql .= "   pay_no, ";
        $sql .= "   pay_day, ";
        $sql .= "   collect_staff_id, ";
        $sql .= "   collect_staff_name, ";
        $sql .= "   client_id, ";
        $sql .= "   client_cd1, ";
        $sql .= "   client_cd2, ";
        $sql .= "   client_name, ";
        $sql .= "   client_name2, ";
        $sql .= "   client_cname, ";
        $sql .= "   c_bank_cd, ";
        $sql .= "   c_bank_name, ";
        $sql .= "   c_b_bank_cd, ";
        $sql .= "   c_b_bank_name, ";
        $sql .= "   c_deposit_kind, ";
        $sql .= "   c_account_no, ";
        $sql .= "   claim_div, ";
        $sql .= "   pay_name, ";
        $sql .= "   account_name, ";
        $sql .= "   bill_id, ";
        $sql .= "   claim_cd1, ";
        $sql .= "   claim_cd2, ";
        $sql .= "   claim_cname, ";
        $sql .= "   input_day, ";
        $sql .= "   e_staff_id, ";
        $sql .= "   e_staff_name, ";
        $sql .= "   ac_staff_id, ";
        $sql .= "   ac_staff_name, ";
        $sql .= "   sale_id, ";
        $sql .= "   renew_flg, ";
        $sql .= "   renew_day, ";
        $sql .= "   shop_id ";
        $sql .= ") ";
        $sql .= "VALUES ";
        $sql .= "( ";
        $sql .= "   (SELECT COALESCE(MAX(pay_id), 0)+1 FROM t_payin_h), ";
        $sql .= "   '$post_payin_no', ";
        $sql .= "   '$post_payin_date', ";
        $sql .= "   $staff_id, ";
        $sql .= "   '".addslashes($staff_name)."', ";
        $sql .= "   $client_id, ";
        $sql .= "   '$post_client_cd1', ";
        $sql .= "   '$post_client_cd2', ";
        $sql .= "   (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
        $sql .= "   (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
        $sql .= "   '$post_client_name', ";
        $sql .= "   '$post_c_bank_cd', ";
        $sql .= "   '$post_c_bank_name', ";
        $sql .= "   '$post_c_b_bank_cd', ";
        $sql .= "   '$post_c_b_bank_name', ";
        $sql .= "   '$post_c_deposit_kind', ";
        $sql .= "   '$post_c_account_no', ";
        $sql .= "   '$post_claim_div', ";
        $sql .= "   (SELECT t_client.pay_name FROM t_claim LEFT JOIN t_client ON t_claim.claim_id = t_client.client_id 
                        WHERE t_claim.client_id = $client_id AND t_claim.claim_div = $post_claim_div), ";
        $sql .= "   (SELECT t_client.account_name FROM t_claim LEFT JOIN t_client ON t_claim.claim_id = t_client.client_id 
                        WHERE t_claim.client_id = $client_id AND t_claim.claim_div = $post_claim_div), ";
        $sql .= ($bill_id != null) ? " $bill_id, " : " NULL, ";
        $sql .= "   '".addslashes($claim_cd1)."', ";
        $sql .= "   '".addslashes($claim_cd2)."', ";
        $sql .= "   '".addslashes($claim_cname)."', ";
        $sql .= "   NOW(), ";
        $sql .= "   $staff_id, ";
        $sql .= "   '".addslashes($staff_name)."', ";
        $sql .= "   $staff_id, ";
        $sql .= "   '".addslashes($staff_name)."', ";
        $sql .= "   NULL, ";
        $sql .= "   'f', ";
        $sql .= "   NULL, ";
        $sql .= "   $shop_id ";
        $sql .= ") ";
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
        $sql .= "   pay_no = '$post_payin_no' ";
        $sql .= "AND ";
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $payin_id = pg_fetch_result($res, 0);

        // 入力のある行数分ループ
        foreach ($ary_insert_forms as $key => $i){

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
            $sql .= "   $payin_id, ";
            $sql .= "   $post_trade[$i], ";
            $sql .= "   '$post_amount[$i]', ";
            $sql .= ($post_bank[$i] != null) ? " (SELECT bank_cd FROM t_bank WHERE bank_id = $post_bank[$i]), " : " NULL, ";
            $sql .= ($post_bank[$i] != null) ? " (SELECT bank_name FROM t_bank WHERE bank_id = $post_bank[$i]), " : " NULL, ";
            $sql .= ($post_b_bank[$i] != null) ? " (SELECT b_bank_cd FROM t_b_bank WHERE b_bank_id = $post_b_bank[$i]), " : " NULL, ";
            $sql .= ($post_b_bank[$i] != null) ? " (SELECT b_bank_name FROM t_b_bank WHERE b_bank_id = $post_b_bank[$i]), " : " NULL, ";
            $sql .= ($post_account[$i] != null) ? " $post_account[$i], " : " NULL, ";
            $sql .= ($post_account[$i] != null) ? " (SELECT deposit_kind FROM t_account WHERE account_id = $post_account[$i]), " : " NULL, ";
            $sql .= ($post_account[$i] != null) ? " (SELECT account_no FROM t_account WHERE account_id = $post_account[$i]), " : " NULL, ";
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
            $sql .= "   '$post_payin_no' ";
            $sql .= ") ";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            // エラー時はロールバック
            if($res == false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
        }

    }

    /*** 変更時 ***/
    if ($state == "chg"){

        /* 入金ヘッダUPDATE */
        $sql  = "UPDATE ";
        $sql .= "   t_payin_h ";
        $sql .= "SET ";
        $sql .= "   pay_no = '$post_payin_no', ";
        $sql .= "   pay_day = '$post_payin_date', ";
        $sql .= "   collect_staff_id = $staff_id, ";
        $sql .= "   collect_staff_name = '".addslashes($staff_name)."', ";
        $sql .= "   client_id = $client_id, ";
        $sql .= "   client_cd1 = '$post_client_cd1', ";
        $sql .= "   client_cd2 = '$post_client_cd2', ";
        $sql .= "   client_name = (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
        $sql .= "   client_name2 = (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
        $sql .= "   client_cname = '$post_client_name', ";
        $sql .= "   c_bank_cd = '$post_c_bank_cd', ";
        $sql .= "   c_bank_name = '$post_c_bank_name', ";
        $sql .= "   c_b_bank_cd = '$post_c_b_bank_cd', ";
        $sql .= "   c_b_bank_name = '$post_c_b_bank_name', ";
        $sql .= "   c_deposit_kind = '$post_c_deposit_kind', ";
        $sql .= "   c_account_no = '$post_c_account_no', ";
        $sql .= "   claim_div = '$post_claim_div', ";
        $sql .= "   pay_name = (SELECT t_client.pay_name FROM t_claim LEFT JOIN t_client ON t_claim.claim_id = t_client.client_id 
                        WHERE t_claim.client_id = $client_id AND t_claim.claim_div = $post_claim_div), ";
        $sql .= "   account_name = (SELECT t_client.account_name FROM t_claim LEFT JOIN t_client ON t_claim.claim_id = t_client.client_id 
                        WHERE t_claim.client_id = $client_id AND t_claim.claim_div = $post_claim_div), ";
        $sql .= ($bill_id != null) ? " bill_id = $bill_id, " : null;
        $sql .= "   claim_cd1 = '".addslashes($claim_cd1)."', ";
        $sql .= "   claim_cd2 = '".addslashes($claim_cd2)."', ";
        $sql .= "   claim_cname = '".addslashes($claim_cname)."', ";
        $sql .= "   input_day = NOW(), ";
        $sql .= "   e_staff_id = $staff_id, ";
        $sql .= "   e_staff_name = '".addslashes($staff_name)."', ";
        $sql .= "   ac_staff_id = $staff_id, ";
        $sql .= "   ac_staff_name = '".addslashes($staff_name)."', ";
        $sql .= "   sale_id = NULL, ";
        $sql .= "   renew_flg = 'f', ";
        $sql .= "   renew_day = NULL, ";
        $sql .= "   shop_id = $shop_id ";
        $sql .= "WHERE ";
        $sql .= "   pay_id = $payin_id ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        // エラー時はロールバック
        if($res == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        /* 入金データDELETE */
        $sql  = "DELETE FROM ";
        $sql .= "   t_payin_d ";
        $sql .= "WHERE ";
        $sql .= "   pay_id = $payin_id ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        // エラー時はロールバック
        if($res == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        /* 入金データINSERT */
        // 入力のある行数分ループ
        foreach ($ary_insert_forms as $key => $i){

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
            $sql .= "   $payin_id, ";
            $sql .= "   $post_trade[$i], ";
            $sql .= "   '$post_amount[$i]', ";
            $sql .= ($post_bank[$i] != null) ? " (SELECT bank_cd FROM t_bank WHERE bank_id = $post_bank[$i]), " : " NULL, ";
            $sql .= ($post_bank[$i] != null) ? " (SELECT bank_name FROM t_bank WHERE bank_id = $post_bank[$i]), " : " NULL, ";
            $sql .= ($post_b_bank[$i] != null) ? " (SELECT b_bank_cd FROM t_b_bank WHERE b_bank_id = $post_b_bank[$i]), " : " NULL, ";
            $sql .= ($post_b_bank[$i] != null) ? " (SELECT b_bank_name FROM t_b_bank WHERE b_bank_id = $post_b_bank[$i]), " : " NULL, ";
            $sql .= ($post_account[$i] != null) ? " $post_account[$i], " : " NULL, ";
            $sql .= ($post_account[$i] != null) ? " (SELECT deposit_kind FROM t_account WHERE account_id = $post_account[$i]), " : " NULL, ";
            $sql .= ($post_account[$i] != null) ? " (SELECT account_no FROM t_account WHERE account_id = $post_account[$i]), " : " NULL, ";
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

        }

    }

    /*** トランザクション完結 ***/
    Db_Query($db_con, "COMMIT;");

    /****************************/
    // ページ遷移
    /****************************/
    // 重複エラーの場合
    if ($duplicate_err_flg == true){

        // 入金番号を自動採番
        $sql  = "SELECT ";
        $sql .= "   MAX(pay_no) ";
        $sql .= "FROM ";
        $sql .= "   t_payin_h ";
        $sql .= "WHERE ";
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";"; 
        $res  = Db_Query($db_con, $sql);
        $payin_no = str_pad(pg_fetch_result($res, 0 ,0)+1, 8, "0", STR_PAD_LEFT);
        $def_data["form_payin_no"] = $payin_no;
        $form->setConstants($def_data);

        // 得意先フォームにデータをセット
        $client_data["form_client"]["cd1"]  = $_POST["form_client"]["cd1"];
        $client_data["form_client"]["cd2"]  = $_POST["form_client"]["cd2"];
        $client_data["form_client"]["name"] = $_POST["form_client"]["name"];
        $form->setConstants($client_data);

    // 重複エラーでない場合
    }else{

        header("Location: ./1-2-408.php");

    }

}


/****************************/
// 可変フォームパーツ定義（表）
/****************************/
// セレクトボックスアイテムの配列作成
$select_value_trade = ($verify_only_flg == true) ? Select_Get($db_con, "trade_payin")
                                                 : Select_Get($db_con, "trade_payin", " WHERE trade_id NOT IN (39, 40) AND kind = '2' ");
$select_value_bank  = Make_Ary_Bank($db_con);
// 銀行hierselect用連結html
$attach_html        = "<br>";

// 最大行数分（削除済行含む）ループ
for ($i=0, $row_num=0; $i<$max_row; $i++){

    // 削除行履歴にある行は表示させない
    if (!in_array($i, $ary_del_row_history)){

        $row_num++;

        // 確認画面の場合
        if ($verify_flg == true || $verify_only_flg == true){

            // フォームの見た目をstaticっぽくするためのStylesheetを変数に入れておく（長いので変数に）
            $style = "color: #585858; border: #ffffff 1px solid; background-color: #ffffff;";

            // 取引区分
            $verify_freeze_data_trade = $form->addElement("select", "form_trade_$i", "", $select_value_trade, $g_form_option_select);
            $verify_freeze_data_trade->freeze();

            // 金額
            $form->addElement("text", "form_amount_$i", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $style\" readonly"
            );

            // 銀行・支店・口座番号
            // 日次更新後または売上IDのあるデータの確認画面の場合
            if ($verify_only_flg == true){
                $verify_only_bank_form = null;
                $verify_only_bank_form[] = $form->addElement("static", "0", "", "");
                $verify_only_bank_form[] = $form->addElement("static", "1", "", "");
                $verify_only_bank_form[] = $form->addElement("static", "2", "", "");
                $form->addGroup($verify_only_bank_form, "form_bank_".$i, "", $attach_html);
            // 登録ボタン押下後の確認画面の場合
            }else{
                $verify_freeze_data_bank = $obj_bank_select =& $form->addElement("hierselect", "form_bank_$i", "", "", $attach_html);
                $obj_bank_select->setOptions($select_value_bank);
                $verify_freeze_data_bank->freeze();
            }

            // 入金日
            $text = null;
            $text[] =& $form->createElement("text", "y", "", "size=\"4\" maxLength=\"4\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $form->addGroup($text, "form_limit_date_$i", "");

            // 手形券面番号
            $form->addElement("text", "form_bill_paper_no_$i", "", "size=\"13\" maxLength=\"10\" style=\"$style\" readonly");

            // 備考
            $form->addElement("text", "form_note_$i", "", "size=\"34\" maxLength=\"20\" style=\"$style\" readonly"); 

        // 変更可能画面の場合
        }else{

            // 取引区分
            $form->addElement("select", "form_trade_$i", "", $select_value_trade, $g_form_option_select);

            // 金額
            $form->addElement("text", "form_amount_$i", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
            );

            // 銀行
            $obj_bank_select =& $form->addElement("hierselect", "form_bank_$i", "", "", $attach_html);
            $obj_bank_select->setOptions($select_value_bank);

            // 入金日
            $text = null;
            $text[] =& $form->createElement("text", "y", "", 
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_limit_date_".$i."[y]','form_limit_date_".$i."[m]',4)\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date_".$i."[y]','form_limit_date_".$i."[m]','form_limit_date_".$i."[d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_limit_date_".$i."[m]','form_limit_date_".$i."[d]',2)\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date_".$i."[y]','form_limit_date_".$i."[m]','form_limit_date_".$i."[d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date_".$i."[y]','form_limit_date_".$i."[m]','form_limit_date_".$i."[d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $form->addGroup($text, "form_limit_date_$i", "");

            // 手形券面番号
            $form->addElement("text", "form_bill_paper_no_$i", "", "size=\"13\" maxLength=\"10\" style=\"$g_form_style\" $g_form_option\"");

            // 備考
            $form->addElement("text", "form_note_$i", "", "size=\"34\" maxLength=\"20\" $g_form_option\""); 

            // 削除リンク
            $link_no = ($i+1 == $del_row_no) ? $row_num - 1 : $row_num;
            $form->addElement("link", "form_del_row_$i", "", "#", "<font color=\"#fefefe\">削除</font>",
                "tabindex=-1 onClick=\"javascript:Dialogue_3('削除します。', $i, 'del_row_no', $row_num); return false;\""
            );

        }

    }

}


/****************************/
// 可変フォームパーツ定義（ボタン）
/****************************/
/* 登録確認画面では、以下のボタンを表示しない */
if($verify_flg != true && $verify_only_flg != true){

    // 得意先単位ボタン
    $form->addElement("button", "form_trans_client_btn", "得意先単位", $g_button_color." onClick=\"location.href('1-2-402.php')\"");

    // 銀行単位ボタン
    $form->addElement("button", "form_trans_bank_btn", "銀行単位", "onClick=\"location.href('1-2-409.php')\"");

    // 行追加ボタン
    $form->addElement("button", "form_add_row_btn", "行追加", "onClick=\"javascript:Button_Submit_1('add_row_flg', '#foot', 'true')\"");

    // 合計ボタン
    $form->addElement("button", "form_calc_btn", "合　計", "onClick=\"javascript:Button_Submit('calc_flg','#foot','true')\"");

    // 入金確認画面へボタン
    $form->addElement("submit", "form_verify_btn", "入金確認画面へ", "$disabled");

}

/* 登録直後の確認画面のみ表示 */
if ($verify_flg == true && $verify_only_flg != true && $just_daily_update_flg != true){

    // 入金OKボタン
    $form->addElement("button", "hdn_form_ok_btn", "入金ＯＫ", "onClick=\"Double_Post_Prevent2(this);\" $disabled");

    // 入金OKhidden
    $form->addElement("hidden", "form_ok_btn");

    // 戻るボタン
    $form->addElement("button", "form_return_btn", "戻　る", "onClick=\"javascript:SubMenu2('#')\"");
}

/* 日次更新済または売上IDのあるデータの確認時のみ表示 */
if ($verify_flg != true && $verify_only_flg == true && $just_daily_update_flg != true){

    // 戻るボタン
    $form->addElement("button", "form_return_btn", "戻　る", "onClick=\"javascript:history.back()\"");

}

/* 入金変更で入金OKを押したら日次更新されいて変更できなかった場合 */
if ($just_daily_update_flg == true){

    // 戻るボタン
    $form->addElement("button", "form_return_btn", "戻　る", "onClick=\"location.href('1-2-403.php')\"");

}


/****************************/
// 表示用html作成
/****************************/
// html用変数定義
$html = null;

// 最大行数分（削除済行含む）ループ
for ($i=0, $j=1; $i<$max_row; $i++){

    // 削除行履歴にある行は表示させない
    if (!in_array($i, $ary_del_row_history)){

        // html作成
        $html .= "<tr class=\"Result1\">\n";
        $html .= "<A NAME=\"$j\"></A>";
        $html .= "  <td align=\"right\">$j</td>\n";                                                 // 行番号
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_trade_$i"]]->toHtml();          // 取引区分
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_amount_$i"]]->toHtml();         // 金額
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bank_$i"]]->toHtml();           // 銀行・支店・口座
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_limit_date_$i"]]->toHtml();     // 手形期日
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bill_paper_no_$i"]]->toHtml();  // 手形券面番号
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_note_$i"]]->toHtml();           // 備考
        $html .= "  </td>\n";
        // 確認画面でない場合
        if ($verify_flg != true && $verify_only_flg != true){
        $html .= "  <td class=\"Title_Add\" align=\"center\">\n";
        $html .=        $form->_elements[$form->_elementIndex["form_del_row_$i"]]->toHtml();        // 削除リンク
        $html .= "  </td>\n";
        }
        $html .= "</tr>\n";

        // 行番号カウンタ+1
        $j++;

    }

}


/****************************/
// 振込名義取得/出力処理
/****************************/
// 得意先IDがNULLでない場合
if ($client_id != null){

    // クエリテンプレ
    $sql  = "SELECT \n";
    $sql .= "   t_claim.claim_div, \n";
    $sql .= "   t_client.pay_name, \n";
    $sql .= "   t_client.account_name \n";
    $sql .= "FROM \n";
    $sql .= "   t_claim \n";
    $sql .= "   LEFT JOIN t_client ON t_claim.claim_id = t_client.client_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_claim.client_id = $client_id \n";

    $sql_tpl = $sql;

    // 得意先検索時
    if ($client_search_flg == true){

        // 請求先区分１の振込名義をマスタから取得
        $sql .= "AND \n";
        $sql .= "   t_claim.claim_div = '1' \n";

    // 入金確認画面時
    }elseif ($verify_flg == true){

        // POSTされた請求先の振込名義取得
        $sql .= "AND \n";
        $sql .= "   t_claim.claim_div = ".$_POST["form_claim_select"]." \n";

    // 明細画面時
    }elseif ($verify_only_flg == true){

        // 伝票の振込名義取得（テンプレ使わない）
        $sql  = "SELECT \n";
        $sql .= "   claim_div, \n";
        $sql .= "   pay_name, \n";
        $sql .= "   account_name \n";
        $sql .= "FROM \n";
        $sql .= "   t_payin_h \n";
        $sql .= "WHERE \n";
        $sql .= "   pay_id = $payin_id \n";

    // 得意先検索以外、入金確認画面以外、明細画面時以外
    }else{

        // POSTがある場合（行追加、削除など）
        if ($_POST != null){

            // 請求先が選択されている場合
            if ($_POST["form_claim_select"] != null){
                // 該当得意先で該当請求先の振込名義をマスタから取得
                $sql .= "AND \n";
                $sql .= "   t_claim.claim_div = ".$_POST["form_claim_select"]." \n";
            // 請求先がNULLの場合
            }else{
                // NULLを取得させる
                $sql  = "SELECT NULL AS claim_div, NULL AS pay_name, NULL AS account_name;";
            }

        // 変更画面でPOSTが無い場合（変更初期画面）
        }else{

            // 伝票の請求先区分の振込名義をマスタから取得
            $sql .= "AND \n";
            $sql .= "   t_claim.claim_div = (SELECT claim_div FROM t_payin_h WHERE pay_id = $payin_id) \n";

        }

    }

    // 初期表示・POST時表示用
    $res = Db_Query($db_con, $sql.";");
    $num = pg_num_rows($res);
    if ($num > 0){
        $ary_pay_account    = Get_Data($res, 1);
        $pay_account_name   = $ary_pay_account[0][1]."<br>".$ary_pay_account[0][2]."<br>";
    }

    // 請求先切り替えjs用
    $res = Db_Query($db_con, $sql_tpl.";");
    $num = pg_num_rows($res);
    if ($num > 0){
        $ary_pay_account_def = Get_Data($res, 4);
        // 振込名義切り替えjs作成
        $js_sheet  = "function Pay_Account_Name(){\n";
        $js_sheet .= "  // 振込名義リスト作成\n";
        $js_sheet .= "  data = new Array(".count($ary_pay_account_def).");\n";
        foreach ($ary_pay_account_def as $key => $value){
        $js_sheet .= "  data['".$value[0]."'] = '".$value[1]."<br>".$value[2]."<br>'\n";
        }
        $js_sheet .= "  // 請求書プルダウンが空でない場合\n";
        $js_sheet .= "  if (document.dateForm.form_claim_select.value != ''){\n";
        $js_sheet .= "      // プルダウンの選択内容により、リストの値を代入\n";
        $js_sheet .= "      var num = document.dateForm.form_claim_select.value;\n";
        $js_sheet .= "      document.getElementById('pay_account_name').innerHTML = data[num];\n";
        $js_sheet .= "  }else{\n";
        $js_sheet .= "      document.getElementById('pay_account_name').innerHTML = '<br><br>';\n";
        $js_sheet .= "  }\n";
        $js_sheet .= "}\n";
    }

}else{

    // 初期値
    $pay_account_name       = "<br><br>";

}


/****************************/
// 請求番号・請求額出力処理（新規・変更時）
/****************************/
// 得意先IDがNULLでない場合
if ($client_id != null && $verify_only_flg == null){

    $sql  = "SELECT \n";
    $sql .= "   t_claim.claim_div, \n";
    $sql .= "   t_client.pay_name, \n";
    $sql .= "   t_client.account_name, \n";
    $sql .= "   t_client.client_id \n";
    $sql .= "FROM \n";
    $sql .= "   t_claim \n";
    $sql .= "   LEFT JOIN t_client ON t_claim.claim_id = t_client.client_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_claim.client_id = $client_id \n";

    $sql_tpl = $sql;

    // 得意先検索時
    if ($client_search_flg == true){

        // 請求先区分１の振込名義をマスタから取得
        $sql .= "AND \n";
        $sql .= "   t_claim.claim_div = '1' \n";

    // 入金確認画面時
    }elseif ($verify_flg == true){

        // POSTされた請求先の振込名義取得
        $sql .= "AND \n";
        $sql .= "   t_claim.claim_div = ".$_POST["form_claim_select"]." \n";

    // 明細画面時
    }elseif ($verify_only_flg == true){

        // 伝票の振込名義取得（テンプレ使わない）
        $sql  = "SELECT \n";
        $sql .= "   t_payin_h.claim_div, \n";
        $sql .= "   t_payin_h.pay_name, \n";
        $sql .= "   t_payin_h.account_name, \n";
        $sql .= "   t_bill.claim_id \n";
        $sql .= "FROM \n";
        $sql .= "   t_payin_h \n";
        $sql .= "   INNER JOIN t_bill ON t_payin_h.bill_id = t_payin_h.bill_id \n";
        $sql .= "WHERE \n";
        $sql .= "   pay_id = $payin_id \n";

    // 得意先検索以外、入金確認画面以外、明細画面時以外
    }else{

        // POSTがある場合（行追加、削除など）
        if ($_POST != null){

            // 請求先が選択されている場合
            if ($_POST["form_claim_select"] != null){
                // 該当得意先で該当請求先の振込名義をマスタから取得
                $sql .= "AND \n";
                $sql .= "   t_claim.claim_div = ".$_POST["form_claim_select"]." \n";
            // 請求先がNULLの場合
            }else{
                // NULLを取得させる
                $sql  = "SELECT NULL AS claim_div, NULL AS pay_name, NULL AS account_name;";
            }

        // 変更画面でPOSTが無い場合（変更初期画面）
        }else{

            // 伝票の請求先区分の振込名義をマスタから取得
            $sql .= "AND \n";
            $sql .= "   t_claim.claim_div = (SELECT claim_div FROM t_payin_h WHERE pay_id = $payin_id) \n";

        }

    }

    // 初期表示・POST時表示用
    $res = Db_Query($db_con, $sql.";");
    $num = pg_num_rows($res);
    // 請求先データがある場合
    if ($num > 0){
        // 請求先データ取得
        $ary_pay_account    = Get_Data($res, 1);
        // 請求データ取得
        $ary_bill_data      = Get_Bill_Data($db_con, $client_id, $ary_pay_account[0][3]);
        $bill_no_amount     = $ary_bill_data[1]."<br>".$ary_bill_data[2]."<br>";
        $bill_amount        = $ary_bill_data[2];
        $set_bill_no["form_bill_no"] = $ary_bill_data[1];
        $form->setConstants($set_bill_no);
    }

    // 請求先切り替えjs用
    $res = Db_Query($db_con, $sql_tpl.";");
    $num = pg_num_rows($res);
    // 請求データがある場合 
    if ($num > 0){
        // 請求先データ取得
        $ary_pay_account_def    = Get_Data($res, 1);
        // 取得した請求先データでループ
        foreach ($ary_pay_account_def as $key_claim => $value_claim){
            // 請求データ取得
            $ary_bill_data_def[$key_claim] = Get_Bill_Data($db_con, $client_id, $value_claim[3]);
        }
        // 請求書データがある場合
        if (count($ary_bill_data_def) > 0){
            // 振込名義切り替えjs作成
            $js_sheet .= "function Bill_No_Amount(){\n";
            $js_sheet .= "  form_bill_no = document.dateForm.elements[\"form_bill_no\"]\n";
            $js_sheet .= "  // 振込名義リスト作成\n";
            $js_sheet .= "  data1 = new Array(".count($ary_bill_data_def).");\n";
            $js_sheet .= "  data2 = new Array(".count($ary_bill_data_def).");\n";
            $js_sheet .= "  data3 = new Array(".count($ary_bill_data_def).");\n";
            foreach ($ary_bill_data_def as $key_bill => $value_bill){
            $js_sheet .= "  data1['".($key_bill+1)."'] = '".$value_bill[1]."<br>".$value_bill[2]."<br>'\n";
            $js_sheet .= "  data2['".($key_bill+1)."'] = '".$value_bill[1]."'\n";
            }
            $js_sheet .= "  // 請求書プルダウンが空でない場合\n";
            $js_sheet .= "  if (document.dateForm.form_claim_select.value != ''){\n";
            $js_sheet .= "      // プルダウンの選択内容により、リストの値を代入\n";
            $js_sheet .= "      var num = document.dateForm.form_claim_select.value;\n";
            $js_sheet .= "      document.getElementById('bill_no_amount').innerHTML = data1[num];\n";
            $js_sheet .= "      form_bill_no.value = data2[num];\n";
            $js_sheet .= "      document.getElementById('bill_amount').innerHTML = data3[num];\n";
            $js_sheet .= "  }else{\n";
            $js_sheet .= "      document.getElementById('bill_no_amount').innerHTML = '<br><br>';\n";
            $js_sheet .= "      form_bill_no.value = ''\n";
            $js_sheet .= "      document.getElementById('bill_amount').innerHTML = '';\n";
            $js_sheet .= "  }\n";
            $js_sheet .= "}\n";
        }else{
            $js_sheet .= "function Bill_No_Amount(){\n";
            $js_sheet .= "  form_bill_no = document.dateForm.elements[\"form_bill_no\"]\n";
            $js_sheet .= "  form_bill_no.value = ''\n";
            $js_sheet .= "}\n";
        }
    }else{
        $js_sheet .= "function Bill_No_Amount(){\n";
        $js_sheet .= "  form_bill_no = document.dateForm.elements[\"form_bill_no\"]\n";
        $js_sheet .= "  form_bill_no.value = ''\n";
        $js_sheet .= "}\n";
    }

}else{

    $js_sheet .= "function Bill_No_Amount(){\n";
    $js_sheet .= "  form_bill_no = document.dateForm.elements[\"form_bill_no\"]\n";
    $js_sheet .= "  form_bill_no.value = ''\n";
    $js_sheet .= "}\n";
    $bill_no_amount = "<br><br>";
    $bill_amount = "";

}


/****************************/
// 請求番号・請求額出力処理（明細時）
/****************************/
if ($verify_only_flg == true && $form_bill_id != null){

    // 該当請求書IDの請求額を取得
    $sql  = "SELECT \n";
    $sql .= "   t_bill.bill_no, \n";
    $sql .= "   t_bill_d.payment_this \n";
    $sql .= "FROM \n";
    $sql .= "   t_bill \n";
    $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_bill.bill_id = $form_bill_id \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    if (pg_num_rows($res)){
        $ary_bill_data = Get_Data($res, 1);
    }
    $bill_no_amount = $ary_bill_data[0][0]."<br>".number_format($ary_bill_data[0][1])."<br>";
    $bill_amount    = number_format($ary_bill_data[0][1])."<br>";

}


/****************************/
// 得意先の銀行・支店・口座取得
/****************************/
// 得意先検索フラグがtrueの場合
if ($_POST["client_search_flg"] == true || $_POST == null){

    // 該当得意先が存在する場合
    if ($client_id != null){

        // 該当得意先の銀行情報を抽出
        // 明細画面でない場合
        if ($verily_only_flg != true){
            $sql  = "SELECT \n";
            $sql .= "   t_bank.bank_cd, \n";
            $sql .= "   t_bank.bank_name, \n";
            $sql .= "   t_b_bank.b_bank_cd, \n";
            $sql .= "   t_b_bank.b_bank_name, \n";
            $sql .= "   t_account.deposit_kind, \n";
            $sql .= "   t_account.account_no \n";
            $sql .= "FROM \n";
            $sql .= "   t_client \n";
            $sql .= "   LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
            $sql .= "   LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
            $sql .= "   LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_client.client_id = $client_id \n";
            $sql .= ";"; 
        // 明細画面の場合
        }else{  
            $sql  = "SELECT \n";
            $sql .= "   t_payin_h.c_bank_cd, \n";
            $sql .= "   t_payin_h.c_bank_name, \n";
            $sql .= "   t_payin_h.c_b_bank_cd, \n";
            $sql .= "   t_payin_h.c_b_bank_name, \n";
            $sql .= "   t_payin_h.c_deposit_kind, \n";
            $sql .= "   t_payin_h.c_account_no \n";
            $sql .= "FROM \n";
            $sql .= "   t_payin_h \n";
            $sql .= "   LEFT JOIN t_account ON t_payin_h.account_id = t_account.account_id \n";
            $sql .= "   LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
            $sql .= "   LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_client.client_id = $client_id \n";
            $sql .= ";"; 
        }  
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);

        // 該当データがある場合
        if ($num > 0){
            $get_c_bank_data = true;
            // マスタから取得
            $c_bank_cd      = htmlspecialchars(pg_fetch_result($res, 0, 0));
            $c_bank_name    = htmlspecialchars(pg_fetch_result($res, 0, 1));
            $c_b_bank_cd    = htmlspecialchars(pg_fetch_result($res, 0, 2));
            $c_b_bank_name  = htmlspecialchars(pg_fetch_result($res, 0, 3));
            $c_deposit_kind = htmlspecialchars(pg_fetch_result($res, 0, 4));
            $c_account_no   = htmlspecialchars(pg_fetch_result($res, 0, 5));
        }else{
            $c_bank_cd      = null;
            $c_bank_name    = null;
            $c_b_bank_cd    = null;
            $c_b_bank_name  = null;
            $c_deposit_kind = null;
            $c_account_no   = null;
        }

        // 取得した銀行情報をhiddenにセット
        $hdn_c_bank_data["hdn_c_bank_cd"]       = $c_bank_cd;
        $hdn_c_bank_data["hdn_c_bank_name"]     = $c_bank_name;
        $hdn_c_bank_data["hdn_c_b_bank_cd"]     = $c_b_bank_cd;
        $hdn_c_bank_data["hdn_c_b_bank_name"]   = $c_b_bank_name;
        $hdn_c_bank_data["hdn_c_deposit_kind"]  = $c_deposit_kind;
        $hdn_c_bank_data["hdn_c_account_no"]    = $c_account_no;
        $form->setConstants($hdn_c_bank_data);

        // 銀行情報をstaticにセット
        $stc_c_bank_data["form_c_bank"]         = ($c_bank_cd != null)      ? $c_bank_cd." : ".$c_bank_name     : "";
        $stc_c_bank_data["form_c_b_bank"]       = ($c_b_bank_cd != null)    ? "&nbsp;".$c_b_bank_cd." : ".$c_b_bank_name : "";
        if ($c_deposit_kind != null){
            $stc_c_bank_data["form_c_account"]  = ($c_deposit_kind == "1") ? "普通" : "当座";
            $stc_c_bank_data["form_c_account"] .= " : ".$c_account_no;
        }else{
            $stc_c_bank_data["form_c_account"]  = "";
        }
        $form->setConstants($stc_c_bank_data);

    // 該当得意先が存在しない場合
    }else{

        // 空をセット
        $hdn_c_bank_data["hdn_c_bank_cd"]       = "";
        $hdn_c_bank_data["hdn_c_bank_name"]     = "";
        $hdn_c_bank_data["hdn_c_b_bank_cd"]     = "";
        $hdn_c_bank_data["hdn_c_b_bank_name"]   = "";
        $hdn_c_bank_data["hdn_c_deposit_kind"]  = "";
        $hdn_c_bank_data["hdn_c_account_no"]    = "";
        $form->setConstants($hdn_c_bank_data);

        // 空をセット
        $stc_c_bank_data["form_c_bank"]         = "";
        $stc_c_bank_data["form_c_b_bank"]       = "";
        $stc_c_bank_data["form_c_account"]      = "";
        $form->setConstants($stc_c_bank_data);

    }

}

/****************************/
// 銀行・支店・口座情報の出力・セット
/****************************/
// 得意先がある、銀行情報取得フラグがtrueでない、hiddenの銀行情報がある場合
if ($client_id != null && $get_c_bank_data != true && $_POST["hdn_c_bank_cd"] != null){

    // POSTされたhiddenの銀行情報をセット
    if ($_POST["hdn_c_bank_cd"] != null){
        $stc_c_bank_data["form_c_bank"]     = $_POST["hdn_c_bank_cd"]." : ".stripslashes($_POST["hdn_c_bank_name"]);
    }else{
        $stc_c_bank_data["form_c_bank"]     = "";
    }
    if ($_POST["hdn_c_b_bank_cd"] != null){
        $stc_c_bank_data["form_c_b_bank"]   = "&nbsp;".$_POST["hdn_c_b_bank_cd"]." : ".stripslashes($_POST["hdn_c_b_bank_name"]);
    }else{
        $stc_c_bank_data["form_c_b_bank"]   = "";
    }
    if ($_POST["hdn_c_deposit_kind"] != null){
        $stc_c_bank_data["form_c_account"]  = ($_POST["hdn_c_deposit_kind"] == "1") ? "普通" : "当座";
        $stc_c_bank_data["form_c_account"] .= " : ".$_POST["hdn_c_account_no"];
    }else{
        $stc_c_bank_data["form_c_account"]  = "";
    }
    $form->setConstants($stc_c_bank_data);

}


/****************************/
// 得意先の状態取得
/****************************/
$client_state_print = Get_Client_State($db_con, $client_id);


/****************************/
// 親子関係のある得意先か調べる
/****************************/
// 確認画面、フリーズ画面以外
#2009-10-16 hashimoto-y
/*
if ($verify_flg != true && $verify_only_flg != true){
    // 得意先IDがある場合
    if ($client_id != null){
        $filiation_flg = Client_Filiation_Chk($db_con, $client_id);
    }
}
*/


/****************************/
// 関数
/****************************/
function Get_Bill_Data($db_con, $client_id, $claim_id){

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
    $sql .= "           t_bill.claim_id = $claim_id \n";
    $sql .= "       AND \n";
    $sql .= "           t_bill.shop_id = ".$_SESSION["client_id"]." \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_bill_max \n";
    $sql .= "   INNER JOIN t_bill_d ON t_bill_max.bill_id = t_bill_d.bill_id \n";
    $sql .= "   INNER JOIN t_bill   ON t_bill_d.bill_id = t_bill.bill_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_bill_d.client_id = $client_id \n";
    $sql .= "AND \n";
    $sql .= "   t_bill_d.bill_data_div = '0' \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);
    if ($num > 0){
        $ary_bill_data = Get_Data($res, 1);
        $ary_bill_data = $ary_bill_data[0];
        $ary_bill_data[2] = ($ary_bill_data[2] != null) ? number_format($ary_bill_data[2]) : null;
    }

    return $ary_bill_data;

}


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
$page_menu = Create_Menu_f('sale','4');

/****************************/
// 画面ヘッダー作成
/****************************/
$page_title .= Print_H_Link_Btn($form, $ary_h_btn_list);
$page_header = Create_Header($page_title);

/****************************/
// 画面ヘッダー作成
/****************************/
$page_header = Create_Header($page_title);


// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form関連の変数をassign
$smarty->assign("form", $renderer->toArray());

// その他の変数をassign
$smarty->assign("var", array(
	"html_header"       => "$html_header",
	"page_menu"         => "$page_menu",
	"page_header"       => "$page_header",
	"html_footer"       => "$html_footer",
    "verify_flg"        => "$verify_flg",
    "verify_only_flg"   => "$verify_only_flg",
    "duplicate_err_msg" => "$duplicate_err_msg",
    "html"              => "$html",
    "just_daily_update_flg" => "$just_daily_update_flg",
    "pay_account_name"  => "$pay_account_name",
    "bill_no_amount"    => "$bill_no_amount",
    "bill_amount"       => "$bill_amount",
    "client_state_print"=> "$client_state_print",
    "filiation_flg"     => "$filiation_flg",
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
