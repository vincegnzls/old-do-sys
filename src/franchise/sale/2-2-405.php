<?php
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容
 * 　2007/04/02　01-002　　　　kaku-m　　　金額に小数が登録できないように修正。
 * 　2007/04/02　01-003　　　　kaku-m　　　一覧画面から遷移して変更するときの入金番号検索にshop_idを追加
 * 　2007/04/10　　　　　　　　kaku-m　　　金額の桁数チェックをはずした
 * 　2007/04/10　　　　　　　　kaku-m　　　参照画面からの遷移時にclient_cd1,2でソートするように修正
 * 　2007/04/11　　　　　　　　kaku-m　　　t_payallocation_dへINSERT時にclaim_divを追加
 * 　2007/04/11　　　　　　　　kaku-m　　　取引区分から「38:入金調整」を削除
 * 　2007/04/12　　　　　　　　fukuda　　　日次更新済明細表示時、請求額が0になる不具合を修正
 * 　2007/04/16　　　　　　　　kaku-m　　　入金確認ボタン押下時に得意先コードのチェックをするように追加
 * 　2007/04/16　　　　　　　　kaku-m　　　入金確認ボタン押下時に請求番号のチェックをするように追加
 * 　2007/04/16　　　　　　　　kaku-m　　　入金変更時には振分ボタンを非表示に変更
 * 　2007/04/16　　　　　　　　kaku-m　　　変更データ抽出時に入金区分=2の条件追加
 * 　2007/04/18　　　　　　　　fukuda　　　排他処理などを追加
 *  2007-04-18      U14-001     fukuda      請求額がない場合は確認画面の請求額をナンバーフォーマットしないよう修正
 *  2007-04-18      U14-002     fukuda      確認画面時、入金額をナンバーフォーマットするよう修正
 *  2007-04-18      U14-003     fukuda      確認画面時、手数料をナンバーフォーマットするよう修正
 *  2007-04-18      U14-005     fukuda      独立の請求先を指定することができる不具合を修正
 *  2007-04-18      U14-006     fukuda      異なる請求先の請求番号で振分を行うとクエリエラーがでる不具合を修正
 *  2007-04-18      U14-007     fukuda      GETの値に文字列を指定するとクエリエラーがでる不具合を修正
 *  2007-04-18      C15-003     fukuda      振分時、hiddenの請求書IDを更新できていなかった不具合を修正
 *  2007-04-18      C15-004     fukuda      請求先区分２で入金したデータの変更画面で子（得意先）が表示されない不具合を修正
 *  2007-04-23      その他172   fukuda      合計ボタン押下時、ページ内リンクで合計表示部へ移動する
 *  2009-06-11      改修No.3	aizawa-m    取引区分に「集金」の選択可に変更
 *  2009-07-28                  aoyama-n    請求番号を変更し、再度振り分けを実行した場合に合計の請求額得意先の金額が表示される不具合修正
 *  2012-12-19                  hashimoto-y 親子一括入金処理で、子の締日に入金出来る不具合の修正
 *
 */

$page_title = "入金入力";

// 環境設定ファイル
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");
require_once(INCLUDE_DIR."2-2-405.php.inc");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB接続
$db_con = Db_Connect();


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
if ($_POST["ok_button"] != null){
    $clear_hdn["ok_button"] = "";
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


/****************************/
// GETしたIDの正当性チェック
/****************************/
if ($_GET["payin_id"] != null){

    $sql  = "SELECT \n";
    $sql .= "   pay_id \n";
    $sql .= "FROM \n";
    $sql .= "   t_payin_h \n";
    $sql .= "WHERE \n";
    $sql .= "   pay_id = ".(float)$_GET["payin_id"]." \n";
    $sql .= "AND \n";
    $sql .= ($group_kind == "2") ? "   shop_id IN (".Rank_Sql().") \n" : "   shop_id = $shop_id \n";
    $sql .= "AND \n";
    $sql .= "   payin_div = '2' \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    if (pg_num_rows($res) == 0){
        // TOPへ遷移
        header("Location: ../top.php");
    }

}


/****************************/
// 入金ID取得・GET判定
/****************************/
// GETの入金IDがある
if ($_GET["payin_id"] != null){

    // 該当伝票の作成日時を取得
    $sql  = "SELECT \n";
    $sql .= "   enter_day \n";
    $sql .= "FROM \n";
    $sql .= "   t_payin_h \n";
    $sql .= "WHERE \n";
    $sql .= "   pay_id = ".$_GET["payin_id"]." \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $enter_day = pg_fetch_result($res, 0, 0);

    $payin_id               = $_GET["payin_id"];
    $set_hdn["hdn_payin_id"]= $payin_id;
    $get_flg                = true;
    $set_hdn["get_flg"]     = "true";
    $set_hdn["hdn_enter_day"] = $enter_day;
    $form->setConstants($set_hdn);

}else

// hiddenの入金IDがある
if ($_POST["hdn_payin_id"] != null){

    $payin_id               = $_POST["hdn_payin_id"];
    $get_flg                = ($_POST["get_flg"] == "true") ? true : false;

}else

// なにもない場合は空
if ($_GET["payin_id"] == null && $_POST["get_flg"] == ""){

    $payin_id               = "";
    $set_hdn["hdn_payin_id"]= "";
    $get_flg                = false;
    $set_hdn["get_flg"]     = "";
    $form->setConstants($set_hdn);

}


/*****************************/
// 初期値設定
/*****************************/
$max_row    = null;     // 子の行数


/*****************************/
// フォームパーツ定義
/*****************************/

/****************************/
// 請求先コード入力時
/****************************/
// POSTデータがある場合
if ($_POST["hdn_claim_search_flg"] == "true"){

// ？
    $set_data["divide_flg"] = "";
    $form->setConstants($set_data);
// ？

    // 検索された請求先の情報を抽出
    $get_claim_data = Get_Claim_Data($db_con);

    // 該当データがある場合
    if ($get_claim_data != null){
        $search_claim_id        = $get_claim_data[0]["client_id"];      // 請求先ID
        $search_claim_cname     = $get_claim_data[0]["client_cname"];   // 請求先名（略称）
        $search_pay_name        = $get_claim_data[0]["pay_name"];       // 振込名義1
        $search_account_name    = $get_claim_data[0]["account_name"];   // 振込名義2
        $claim_found_flg        = true;                                 // 請求先存在フラグ
    }else{
        $search_claim_id        = "";
        $search_claim_cname     = "";
        $search_pay_name        = "";
        $search_account_name    = "";
        $claim_found_flg        = false;
    }

    // 請求先ID、請求先名（略称）、振込名義1,2をフォームにセット
    $claim_data["hdn_claim_id"]         = $search_claim_id;
    $claim_data["form_claim"]["name"]   = $search_claim_cname;
    $claim_data["form_pay_name"]        = $search_pay_name;
    $claim_data["form_account_name"]    = $search_account_name;
    $form->setConstants($claim_data);

    // 請求先存在フラグがtrueの場合
    if ($claim_found_flg == true){

        // 請求データ取得
        $get_bill_data = Get_Bill_Data($db_con, $search_claim_id);

        if ($get_bill_data != null){
            $search_bill_id     = $get_bill_data[0]["bill_id"];         // 請求書ID
            $search_bill_no     = $get_bill_data[0]["bill_no"];         // 請求番号
            $search_bill_amount = $get_bill_data[0]["payment_this"];    // 請求額
            $bill_found_flg     = true;
        }

    }

    // 請求データ取得フラグがtrueの場合
    if ($bill_found_flg == true){
        $set_form["hdn_bill_id"]        = $search_bill_id;
        $set_form["hdn_bill_no"]        = $search_bill_no;
        $set_form["form_bill_no"]       = $search_bill_no;
        $set_form["form_bill_amount"]   = $search_bill_amount;
        $set_form["form_bill2_amount"]  = ($search_bill_amount != null) ? number_format($search_bill_amount) : "";
    }else{
        $set_form["hdn_bill_id"]        = "";
        $set_form["hdn_bill_no"]        = "";
        $set_form["form_bill_no"]       = "";
        $set_form["form_bill_amount"]   = "";
        $set_form["form_bill2_amount"]  = "";
    }

    // その他のフォームを空にする
    $set_form["form_payin_day"]["y"]    = "";
    $set_form["form_payin_day"]["m"]    = "";
    $set_form["form_payin_day"]["d"]    = "";
    $set_form["form_trade"]             = "";
	$set_form["form_collect_staff"]		= "";	//--2009/06/11 改修No.3 追加
    $set_form["form_bank"]              = "";
    $set_form["form_limit_day"]["y"]    = "";
    $set_form["form_limit_day"]["m"]    = "";
    $set_form["form_limit_day"]["d"]    = "";
    $set_form["form_bill_paper_no"]     = "";
    $set_form["hdn_max_row"]            = "";
    $set_form["form_amount_total"]      = "";
    $set_form["form_rebate_total"]      = "";
    $set_form["form_payin_total"]       = "";

    // 請求先検索フラグをクリア
    $set_form["hdn_claim_search_flg"]   = "";

    $form->setConstants($set_form);

    // 一覧行数をnullにする
    $max_row = null;

}


/****************************/
// 金額振分処理
/****************************/
// 金額振分ボタン押下時
//if ($_POST["divide_flg"]   && $_POST['hdn_claim_search_flg']==null){// || $_POST['form_verify_btn']!=null ){
if ($_POST["divide_flg"] == "true"){

    /****************************/
    // エラーチェック
    /****************************/
    // ■請求先
    // 必須チェック
    if ($_POST["form_claim"]["cd1"] == null && $_POST["form_claim"]["cd2"] == null){
        $form->setElementError("err_claim", "請求先 は必須です。");
        $divide_null_flg = $err_flg = true;
    }

    // 正しい請求先が選択されていない場合
    if ($err_flg != true){
        // 入力された請求先コードの請求先情報を取得
        $get_claim_data = Get_Claim_Data($db_con);
        // ↑の結果がnullの場合
        if ($get_claim_data == null){
            $err_msg = "正しい請求先を入力してください。";
            $form->setElementError("form_claim", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }

    // コード変更中に金額振分ボタンが押下された場合
    if ($err_flg != true){
        $err_msg = "請求先情報取得前に 金額振分ボタン が押されました。<br>操作をやり直してください。";
        $divide_null_flg = $err_flg = $illegal_post_flg = Illegal_Post_Chk($form, $db_con, "err_illegal_post", $err_msg);
    }

    // ■請求番号
    // 請求番号の正当性チェック
    if ($err_flg != true){
        $bill_chk = Bill_Check($db_con);
        if ($bill_chk == "bill_false" && $_POST["form_bill_no"] != null){
            $err_msg = "入力された請求番号は存在しません。";
            $form->setElementError("form_bill_no", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }


    /****************************/
    // エラーチェック結果集計
    /****************************/
    // チェック適用
    $form->validate();

    // 結果をフラグに
    $divide_err_flg = (count($form->_errors) > 0) ? true : false;

    /****************************/
    // エラー時処理
    /****************************/
    // 金額振分エラー時は請求額を空に
    if ($divide_err_flg == true){
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $form->setConstants($clear_form);
    }

    // コード変更中にボタンが押下された場合は請求先コード以外のフォームとhiddenを空に
    if ($illegal_post_flg == true){
        $clear_form["form_claim"]["name"]   = "";
        $clear_form["form_pay_name"]        = "";
        $clear_form["form_account_name"]    = "";
        $clear_form["form_bill_no"]         = "";
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $clear_form["hdn_claim_id"]         = "";
        $clear_form["hdn_bill_no"]          = "";
        $form->setConstants($clear_form);
    }

    /****************************/
    // 振分処理
    /****************************/
    if ($divide_err_flg != true){

        // 請求額取得
        if ($_POST["form_bill_no"] != null){
            $sql  = "SELECT \n";
            $sql .= "   t_bill_d.payment_this, \n";
            $sql .= "   t_bill_d.bill_id \n";
            $sql .= "FROM \n";
            $sql .= "   t_bill \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id  \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill.bill_no = '".$_POST["form_bill_no"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
            $sql .= "AND \n";
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            $payment_this   = pg_fetch_result($res, 0, 0);
            $bill_id        = pg_fetch_result($res, 0, 1);
            $set_bill_amount["form_bill_amount"] = $payment_this;
            $set_bill_amount["form_bill2_amount"]= ($payment_this != null) ? number_format($payment_this) : "";
            $set_bill_amount["hdn_bill_id"]      = $bill_id;
            $form->setConstants($set_bill_amount);
        }

        // 請求先選択時に自動補完された請求番号の場合
        if ($bill_chk == "bill_true"){

            // 請求書データ取得
            $get_divide_data = Get_Divide_Data($db_con, $bill_id);

            // 1行以上あれば
            if ($get_divide_data != null){
                $divide_flg                         = true;
                $max_row                            = count($get_divide_data);
                $set_form_data["hdn_max_row"]       = $max_row;
                $form->setConstants($set_form_data);
                // 振分フォームに請求書データをセット
                Set_Divide_Form($form, $max_row, $get_divide_data);
            }

        // 請求番号が存在する場合
        }elseif ($bill_chk == "bill_found"){

            // 入力された請求番号の請求書IDと請求額を取得
            $sql  = "SELECT \n";
            $sql .= "   t_bill.bill_id, \n";
            $sql .= "   t_bill_d.payment_this \n";
            $sql .= "FROM \n";
            $sql .= "   t_bill \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill.bill_no = '".$_POST["form_bill_no"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
            $sql .= "AND \n";
            //aoyama-n 2009-07-28
            //請求番号を変更し、再度振り分けを実行した場合に合計の請求額得意先の金額が表示される不具合修正
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= "AND \n";
            $sql .= "   t_bill.shop_id = ".$_SESSION["client_id"]." \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            $bill_id        = pg_fetch_result($res, 0, 0);
            $bill_amount    = pg_fetch_result($res, 0, 1);

            // 請求書データ取得
            $get_divide_data = Get_Divide_Data($db_con, $bill_id);

            // 1行以上あれば
            if ($get_divide_data != null){
                $divide_flg                         = true;
                $max_row                            = count($get_divide_data);
                $set_form_data["hdn_max_row"]       = $max_row;
                $set_form_data["hdn_bill_id"]       = $bill_id;
                $set_form_data["form_bill_no"]      = $bill_no;
                $set_form_data["form_bill_amount"]  = $bill_amount;
                $set_form_data["form_bill2_amount"] = ($bill_amount != null) ? number_format($bill_amount) : "";
                $form->setConstants($set_form_data);
                // 振分フォームに請求書データをセット
                Set_Divide_Form($form, $max_row, $get_divide_data);
            }

        // 請求番号が入力されてない場合
        }elseif ($bill_chk == "bill_null"){

            $set_form_data["form_bill_amount"] = "";
            $set_form_data["form_bill2_amount"]= "";

            $sql  = "SELECT \n";
            $sql .= "   t_client.client_cd1, \n";
            $sql .= "   t_client.client_cd2, \n";
            $sql .= "   t_client.client_id, \n";
            $sql .= "   t_client.client_cname, \n";
            $sql .= "   t_claim.claim_div \n";
            $sql .= "FROM \n";
            $sql .= "   t_client \n";
            $sql .= "   INNER JOIN t_claim ON t_client.client_id = t_claim.client_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_claim.claim_id = ".$_POST["hdn_claim_id"]." \n";
            $sql .= "ORDER BY \n";
            $sql .= "   client_cd1, \n";
            $sql .= "   client_cd2 \n";
            $sql .= ";";
            $res  = Db_Query($db_con,$sql);
            $num  = pg_num_rows($res);

            if ($num > 0){
                $divide_flg                     = true;
                $client_data                    = pg_fetch_all($res);
                $set_form_data["hdn_max_row"]   = $num;
                $max_row                        = $num;
                $set_form_data["hdn_bill_id"]   = "";
                for ($i = 0; $i < $num; $i++){
                    $set_form_data["form_claim_id"][$i]         = $client_data[$i]["client_id"];
                    $set_form_data["form_claim_cd"][$i]["cd1"]  = $client_data[$i]["client_cd1"];
                    $set_form_data["form_claim_cd"][$i]["cd2"]  = $client_data[$i]["client_cd2"];
                    $set_form_data["form_claim_name"][$i]       = $client_data[$i]["client_cname"];
                    $set_form_data["hdn_claim_div"][$i]         = $client_data[$i]["claim_div"];
                    if($_POST["divide_flg"] == "true"){
                        $set_form_data["form_base_amount"][$i]  = "";
                        $set_form_data["form_amount"][$i]       = "";
                        $set_form_data["form_rebate"][$i]       = "";
                        $set_form_data["form_note"][$i]         = "";
                        $set_form_data["hdn_claim_div"][$i]     = $client_data[$i]["claim_div"];
                    }
                    $form->setConstants($set_form_data);
                }
            }

        }

        // 金額振分時の請求番号をhiddenにセットしておく
        $hdn_post_bill_no_set["hdn_post_bill_no"] = ($_POST["form_bill_no"] != null) ? $_POST["form_bill_no"] : "";
        $form->setConstants($hdn_post_bill_no_set);

    }

    // 金額振分フラグと合計金額をクリア
    $set_data["divide_flg"]         = "";
    $set_data["form_amount_total"]  = "";
    $set_data["form_rebate_total"]  = "";
    $set_data["form_payin_total"]   = "";
    $form->setConstants($set_data);

}


/****************************/
// 合計処理
/****************************/
// 合計ボタンが押されたとき
if ($_POST["calc_flg"] == true || $_POST["form_verify_btn"] != null){

    $divide_flg                         = true;
    $max_row                            = $_POST['hdn_max_row'];
    $set_form_data["form_amount_total"] = null;
    $set_form_data["form_rebate_total"] = null;
    $set_form_data["form_payin_total"]  = null;
    for ($i = 0; $i < $max_row; $i++){
        $set_form_data["form_amount"][$i]   = $_POST["form_amount"][$i];
        $set_form_data["form_amount_total"] = $set_form_data["form_amount_total"] + str_replace(",", null, $_POST["form_amount"][$i]);
        $set_form_data["form_rebate_total"] = $set_form_data["form_rebate_total"] + str_replace(",", null, $_POST["form_rebate"][$i]);
    }
    $payin_total = $set_form_data["form_amount_total"] + $set_form_data["form_rebate_total"];
    $set_form_data["form_amount_total"] = number_format($set_form_data["form_amount_total"]);
    $set_form_data["form_rebate_total"] = number_format($set_form_data["form_rebate_total"]);
    $set_form_data["form_payin_total"]  = number_format($payin_total);
    $set_form_data["calc_flg"]          = "";
    $form->setConstants($set_form_data);

}


/****************************/
// 入力確認
/****************************/
if ($_POST["form_verify_btn"] != null){

    // 日付の0埋め
    $_POST["form_payin_day"] = Str_Pad_Date($_POST["form_payin_day"]);
    $_POST["form_limit_day"] = Str_Pad_Date($_POST["form_limit_day"]);

    // POSTデータを変数にセット
    $payin_day_y   = $_POST["form_payin_day"]["y"];
    $payin_day_m   = $_POST["form_payin_day"]["m"];
    $payin_day_d   = $_POST["form_payin_day"]["d"];
    $payin_day     = $payin_day_y."-".$payin_day_m."-".$payin_day_d;
    $limit_day_y   = $_POST["form_limit_day"]["y"];
    $limit_day_m   = $_POST["form_limit_day"]["m"];
    $limit_day_d   = $_POST["form_limit_day"]["d"];
    $limit_day     = $limit_day_y."-".$limit_day_m."-".$limit_day_d;

    /****************************/
    // エラーチェック
    /****************************/
    // ■請求先
    // 必須チェック
    if ($_POST["form_claim"]["cd1"] == null && $_POST["form_claim"]["cd2"] == null){
        $form->setElementError("err_claim", "請求先 は必須です。");
        $divide_null_flg = $err_flg = true;
    }

    // 正しい請求先が選択されていない場合
    if ($err_flg != true){
        // 入力された請求先コードの請求先情報を取得
        $get_claim_data = Get_Claim_Data($db_con);
        // ↑の結果がnullの場合
        if ($get_claim_data == null){
            $err_msg = "正しい請求先を入力してください。";
            $form->setElementError("form_claim", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }

    // コード変更中に金額振分ボタンが押下された場合
    if ($err_flg != true){
        $err_msg = "請求先情報取得前に 金額振分ボタン が押されました。<br>操作をやり直してください。";
        $divide_null_flg = $err_flg = $illegal_post_flg = Illegal_Post_Chk($form, $db_con, "err_illegal_post", $err_msg);
    }

    // ■請求番号
    // 請求番号の正当性チェック
    if ($err_flg != true){
        $bill_chk = Bill_Check($db_con);
        if ($bill_chk == "bill_false" && $_POST["form_bill_no"] != null){
            $err_msg = "入力された請求番号は存在しません。";
            $form->setElementError("form_bill_no", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }

    // 金額振分時と請求番号が異なる場合
    if ($_POST["hdn_post_bill_no"] != $_POST["form_bill_no"]){
        $form->setElementError("form_bill_no", "請求番号が金額振分時から変更されたため登録できません。<br>再度金額振分を行って下さい。");
        $divide_null_flg = $err_flg = true;
    }

    // ■入金日
    $err_msg = "入金日 が妥当ではありません。";

    // 必須チェック
    if ($payin_day == "--"){
        $form->setElementError("form_payin_day", "入金日 は必須です。");
        $err_payin_day = true;
    }

    // 数値チェック
    if ($err_payin_day != true){
        if (!ereg("^[0-9]+$", $payin_day_y) || !ereg("^[0-9]+$", $payin_day_m) || !ereg("^[0-9]+$", $payin_day_d)){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // 妥当性チェック
    if ($err_payin_day != true){
        if (!checkdate((int)$payin_day_m, (int)$payin_day_d, (int)$payin_day_y)){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // システム開始日以前チェック
    if ($err_payin_day != true){
        $chk_res = Sys_Start_Date_Chk($payin_day_y, $payin_day_m, $payin_day_d, "入金日");
        if ($chk_res != null){
            $form->setElementError("form_payin_day", $chk_res);
            $err_payin_day = true;
        }
    }

    // 未来日付が入力されていないかチェック
    if ($err_payin_day != true){
        if (date("Y-m-d") < $payin_day){
            $form->setElementError("form_payin_day", "入金日 が未来の日付になっています。");
            $err_payin_day = true;
        }
    }

    // 最新の月次更新以前の日付が入力されていないかチェック
    if ($err_payin_day != true){
        $err_msg = "入金日 に前回の月次更新以前の日付が入力されています。";
        $sql  = "SELECT \n";
        $sql .= "   to_date(MAX(close_day), 'YYYY-MM-DD') AS close_day \n";
        $sql .= "FROM \n";
        $sql .= "   t_sys_renew \n";
        $sql .= "WHERE \n";
        $sql .= "   renew_div = '2' \n";
        $sql .= "AND \n";
        $sql .= ($group_kind == "2") ? "   shop_id IN (".Rank_Sql().") \n" : "   shop_id = $shop_id \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        $last_monthly_renew_date = ($num == 1) ? pg_fetch_result($res, 0) : null;
        // 月次更新日がある＋月次更新日より前の入金日の場合
        if ($last_monthly_renew_date != null && $payin_day <= $last_monthly_renew_date && $payin_day != "--"){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // 前回の請求締日以前の日付が入力されていないかチェック
    if ($err_payin_day != true){
        $err_msg = "入金日 に請求書作成済の日付が入力されています。<br>入金日を変更するか、請求書を削除して下さい。";
        $sql  = "SELECT \n";
        $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
        $sql .= "FROM \n";
        $sql .= "   t_bill \n";
        $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
        $sql .= "WHERE \n";
        $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
        $sql .= "AND \n";
        $sql .= "   t_bill_d.client_id = ".$_POST["hdn_claim_id"]." \n";
        $sql .= "AND \n";
        $sql .= ($group_kind == "2") ? "   t_bill.shop_id IN (".Rank_Sql().") \n" : "   t_bill.shop_id = $shop_id \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        $last_close_date = ($num > 0) ? pg_fetch_result($res, 0) : null;
        // 締日がある＋締日より前の入金日の場合
        if ($last_close_date != null && $payin_day <= $last_close_date && $payin_day != "--"){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // ■取引区分
    // 必須チェック
    if ($_POST["form_trade"] == null){
        // エラーをセット
        $form->setElementError("form_trade", "取引区分 は必須です。");
    }
	//-- 2009/06/11 改修No.3 追加
	// 集金担当者が選択されている場合、取引区分の入力チェック
	else if ($_POST["form_trade"] != "31" && $_POST["form_collect_staff"]) {
		$form->setElementError("form_trade", "集金担当者 を選択した場合は、取引区分「集金」のみ選択可能です。");
	}
	//-- 2009/06/11 改修No.3 追加
	// 取引区分「集金」の場合、集金担当者の必須チェック
	else if ($_POST["form_trade"] == "31" && !$_POST["form_collect_staff"]) {
		$form->setElementError("form_trade", "取引区分が「集金」 の場合は、集金担当者 は必須です。");
	}	


    // ■銀行
    // 必須チェック
    if (
        ($_POST["form_trade"] == 32 || $_POST["form_trade"] == 33) &&
        ($_POST["form_bank"][0] == null && $_POST["form_bank"][1] == null && $_POST["form_bank"][2] == null)
    ){
        $form->setElementError("form_bank", "取引区分が振込入金・手形入金の場合、銀行は必須です。");
        $err_bank = true;
    }

    // 半端入力チェック
    if (
        ($_POST["form_bank"][0] != null || $_POST["form_bank"][1] != null || $_POST["form_bank"][2] != null) &&
        ($_POST["form_bank"][0] == null || $_POST["form_bank"][1] == null || $_POST["form_bank"][2] == null)
    ){
        $form->setElementError("form_bank", "銀行は口座番号まで選択して下さい。");
        $err_bank = true;
    }

    // ■手形期日
    $err_msg = "手形期日 が妥当ではありません。";

    // 半端入力チェック
    if (
        ($limit_day_y != null || $limit_day_m != null || $limit_day_d != null) &&
        ($limit_day_y == null || $limit_day_m == null || $limit_day_d == null)
    ){
        $form->setElementError("form_limit_day", $err_msg);
        $err_limit_day = true;
    }

    // 数値チェック
    if ($err_limit_day != true && $limit_day != "--"){
        if (!ereg("^[0-9]+$", $limit_day_y) || !ereg("^[0-9]+$", $limit_day_m) || !ereg("^[0-9]+$", $limit_day_d)){
            $form->setElementError("form_limit_day", $err_msg);
            $err_limit_day = true;
        }   
    }      

    // 妥当性チェック
    if ($err_limit_day != true && $limit_day != "--"){
        if (!checkdate((int)$limit_day_m, (int)$limit_day_d, (int)$limit_day_y)){
            $form->setElementError("form_limit_day", $err_msg);
            $err_limit_day = true;
        }   
    }

    // 必須チェック
    if ($err_limit_day != true){
        if ($_POST["form_trade"] == 33 && $limit_day == "--"){
            $form->setElementError("form_limit_day", "取引区分が手形入金の場合は、手形期日を入力してください。");
            $err_limit_day = true;
        }
    }

    // 振分テーブル非表示フラグがtrueでない場合
    if ($divide_null_flg != true){

        // ■入金額・手数料
        $max_row = $_POST["hdn_max_row"];
        for ($i = 0; $i < $max_row; $i++){
            $form->addRule("form_amount[$i]", ($i+1)."行目 正しい入金額を入力してください。", "regex", "/^[-]?[0-9]+$/");
            $form->addRule("form_rebate[$i]", ($i+1)."行目 正しい手数料を入力してください。", "regex", "/^[-]?[0-9]+$/");
        }

        // 行数取得
        $max_row = $_POST["hdn_max_row"];

        // 行数でループ
        for ($i = 0; $i < $max_row; $i++){

            // 入金額・手数料・備考に入力がある場合
            if ($_POST["form_amount"][$i] != null || $_POST["form_rebate"][$i] != null || $_POST["form_note"][$i] != null){

                // ■銀行必須チェック
                if ($err_bank != true && $_POST["form_rebate"][$i] != null && $_POST["form_bank"][0] == null){
                    $form->setElementError("form_bank", "手数料 を入力する場合は、銀行は必須です。");
                }

                // ■入金額
                // 数値チェック
                if ($_POST["form_amount"][$i] != null && ereg("^(-)?([0-9])*$", $_POST["form_amount"][$i]) == false){ 
                    $form->setElementError("form_amount[$i]", ($i+1)."行目 正しい入金額を入力してください。");
                }
                // 必須チェック（手数料・備考が入力されている場合）
                if ($_POST["form_amount"][$i] == null && ($_POST["form_rebate"][$i] != null || $_POST["form_note"][$i] != null)){
                    $form->setElementError("form_amount[$i]", ($i+1)."行目 手数料・備考を入力する場合は、入金額は必須です。");
                }
                // ■手数料
                // 数値チェック
                if ($_POST["form_rebate"][$i] != null && ereg("^(-)?([0-9])*$", $_POST["form_rebate"][$i]) == false){ 
                    $form->setElementError("form_rebate[$i]", ($i+1)."行目 正しい手数料を入力してください。");
                }
                // ■入金中に子が親に変わっていた場合を考慮し、入金日が子の請求締日
                if ($err_payin_day != true){
                    $err_msg = "入金日 に請求書作成済の日付が入力されています。<br>入金日を変更するか、請求書を削除して下さい。";
                    $sql  = "SELECT \n";
                    $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
                    $sql .= "FROM \n";
                    $sql .= "   t_bill_d \n";
                    $sql .= "   INNER JOIN t_claim ON  t_bill_d.client_id = t_claim.client_id \n";
                    $sql .= "                      AND t_bill_d.claim_div = t_claim.claim_div \n";
                    $sql .= "WHERE \n";
                    $sql .= "   t_bill_d.client_id = ".$_POST["form_claim_id"][$i]." \n";
                    $sql .= "AND \n";
                    $sql .= "   t_bill_d.claim_div = ".$_POST["hdn_claim_div"][$i]." \n";
                    $sql .= ";";
                    $res  = Db_Query($db_con, $sql);
                    $num  = pg_num_rows($res);
                    if ($num > 0){
                        $max_bill_closeday_this = pg_fetch_result($res, 0, 0);

                        #20121219 hashimoto-y 入力日と$max_bill_closeday_thisが同じ場合、入金出来る不具合
                        #if ($payin_day < $max_bill_closeday_this){
                        if ($payin_day <= $max_bill_closeday_this){
                            $form->setElementError("form_payin_day", ($i+1)."行目　$err_msg");
                        }
                    }
                }
            // 入金額・手数料、どちらも入力がない場合
            }else{

                $form_count[] = $i;

            }

        }

        // ■入金額
        // 金額が全てnullの場合
        if(count($form_count) == $max_row){
            $form->setElementError("err_count", "入金額を入力してください。");
        }

    }

    /****************************/
    // エラーチェック結果集計
    /****************************/
    // チェック適用
    $form->validate();

    // 結果をフラグに
    $err_flg = (count($form->_errors) > 0) ? true : false;

    /****************************/
    // エラー時処理
    /****************************/
    // 金額振分エラー時は請求額を空に
    if ($divide_null_flg == true){
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $form->setConstants($clear_form);
    }

    // コード変更中にボタンが押下された場合は請求先コード以外のフォームとhiddenを空に
    if ($illegal_post_flg == true){
        $clear_form["form_claim"]["name"]   = "";
        $clear_form["form_pay_name"]        = "";
        $clear_form["form_account_name"]    = "";
        $clear_form["form_bill_no"]         = "";
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $clear_form["hdn_claim_id"]         = "";
        $clear_form["hdn_bill_no"]          = "";
        $clear_form["hdn_post_bill_no"]     = "";
        $form->setConstants($clear_form);
    }

    /****************************/
    // エラーなし時処理
    /****************************/
    if ($err_flg != true){

        $freeze_flg = true;
        $divide_flg = true;

        if($_POST["get_flg"] != "true"){
            $form->addElement("static", "divide_msg", "", "以下の内容で入金しますか？");
        }else{
            $form->addElement("static", "divide_msg", "", "以下の内容で入金変更しますか？");
        }

        $set_data["freeze_flg"] = true;
        $form->setConstants($set_data);

    }

}

/****************************/
// 入金ボタン押下
/****************************/
if ($_POST["ok_button"] != null){

    // POSTされた値を処理しやすいように変数に代入
    if ($post_bill_no == null){
        $post_bill_id       = null;
        $post_bill_amount   = null;
    }else{
        $post_bill_id       = $_POST["hdn_bill_id"];                                // 請求ID
        $post_bill_amount   = str_replace(",", null, $_POST["form_bill_amount"]);   // 請求金額
    }
    $max_row = $_POST["hdn_max_row"];

    // 手数料があるか
    for ($i = 0; $i < $max_row; $i++){    
        if ($_POST["form_amount"][$i] != null){
            $post_amount    = $post_amount + str_replace(",", "", $_POST["form_amount"][$i]);
        }
        if ($_POST["form_rebate"][$i] != null){
            $post_rebate    = $post_rebate + str_replace(",", "", $_POST["form_rebate"][$i]);
            $rebate_flg     = true;
        }
    }

    $_POST["form_payin_day"] = Str_Pad_Date($_POST["form_payin_day"]);
    $payin_day = $_POST["form_payin_day"]["y"]."-".$_POST["form_payin_day"]["m"]."-".$_POST["form_payin_day"]["d"];
    
    /******************************/
    // 最終エラーチェック
    /******************************/
    // 伝票変更時、対象となる伝票が削除→新規作成され、別の伝票を上書きしようとしていないかチェック
    if ($_POST["get_flg"] == "true"){
        // 入金ID、伝票作成時間と入金データの突合せ
        $same_data_flg = Update_Check($db_con, "t_payin_h", "pay_id", $_POST["hdn_payin_id"], $_POST["hdn_enter_day"]);
        if ($same_data_flg == false){
            header("Location: ./2-2-410.php?err=3");
            exit;
        }
    }

    // 伝票変更時、日次更新されていないかチェック
    if ($_POST["get_flg"] == "true"){
        $sql  = "SELECT \n";
        $sql .= "   renew_flg \n";
        $sql .= "FROM \n";
        $sql .= "   t_payin_h \n";
        $sql .= "WHERE \n";
        $sql .= "   pay_id = ".$_POST["hdn_payin_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $renew = pg_fetch_result($res, 0, 0);
        if ($renew == "t"){
            header("Location: ./2-2-410.php?err=1");
            $last_err_flg = true;
            exit;
        }

    }

    // 請求書が削除されていないかチェック
    if ($_POST["form_bill_no"] != null){
        $sql  = "SELECT \n";
        $sql .= "   bill_id \n";
        $sql .= "FROM \n";
        $sql .= "   t_bill \n";
        $sql .= "WHERE \n";
        $sql .= "   bill_id = ".$_POST["hdn_bill_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        if ($num == 0){
            header("Location: ./2-2-410.php?err=2");
            $last_err_flg = true;
            exit;
        }
    }

    // 最新の月次更新以前の日付が入力されていないかチェック
    $sql  = "SELECT \n";
    $sql .= "   to_date(MAX(close_day), 'YYYY-MM-DD') AS close_day \n";
    $sql .= "FROM \n";
    $sql .= "   t_sys_renew \n";
    $sql .= "WHERE \n";
    $sql .= "   renew_div = '2' \n";
    $sql .= "AND \n";
    $sql .= ($group_kind == "2") ? "   shop_id IN (".Rank_Sql().") \n" : "   shop_id = $shop_id \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);
    $last_monthly_renew_date = ($num == 1) ? pg_fetch_result($res, 0) : null;
    // 月次更新日がある＋月次更新日より前の入金日の場合
    if ($last_monthly_renew_date != null && $payin_day <= $last_monthly_renew_date && $payin_day != "--"){
        $form->setElementError("form_payin_day", "入金日 に前回の月次更新以前の日付が入力されています。");
        $last_err_flg = true;
    }

    // 前回の請求締日以前の日付が入力されていないかチェック
    $sql  = "SELECT \n";
    $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
    $sql .= "FROM \n";
    $sql .= "   t_bill \n";
    $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
    $sql .= "AND \n";
    $sql .= "   t_bill_d.client_id = ".$_POST["hdn_claim_id"]." \n";
    $sql .= "AND \n";
    $sql .= ($group_kind == "2") ? "   t_bill.shop_id IN (".Rank_Sql().") \n" : "   t_bill.shop_id = $shop_id \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);
    $last_close_date = ($num > 0) ? pg_fetch_result($res, 0) : null;
    // 締日がある＋締日より前の入金日の場合
    if ($last_close_date != null && $payin_day <= $last_close_date && $payin_day != "--"){
        $form->setElementError("form_payin_day", "入金日 に請求書作成済の日付が入力されています。<br>入金日を変更するか、請求書を削除して下さい。");
        $last_err_flg = true;
    }

    // ■入金中に子が親に変わっていた場合を考慮し、入金日が子の請求締日より前になっていないかチェック
    // 行数でループ
    if ($last_err_flg != true){
        for ($i = 0; $i < $max_row; $i++){
            // 入金額・手数料・備考に入力がある場合
            if ($_POST["form_amount"][$i] != null || $_POST["form_rebate"][$i] != null || $_POST["form_note"][$i] != null){
                $sql  = "SELECT \n";
                $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
                $sql .= "FROM \n";
                $sql .= "   t_bill_d \n";
                $sql .= "   INNER JOIN t_claim ON  t_bill_d.client_id = t_claim.client_id \n";
                $sql .= "WHERE \n";
                $sql .= "   t_bill_d.client_id = ".$_POST["form_claim_id"][$i]." \n";
                $sql .= "AND \n";
                $sql .= "   t_bill_d.claim_div = ".$_POST["hdn_claim_div"][$i]." \n";
                $sql .= ";";
                $res  = Db_Query($db_con, $sql);
                $num  = pg_num_rows($res);
                if ($num > 0){
                    $max_bill_closeday_this = pg_fetch_result($res, 0, 0);
                    if ($payin_day < $max_bill_closeday_this){
                        $form->setElementError("form_payin_day", ($i+1)."行目　入金日 に請求書作成済の日付が入力されています。<br>入金日を変更するか、請求書を削除して下さい。");
                    }
                }
            }
        }
    }

    /******************************/
    // エラーがあった場合
    /******************************/
    if ($last_err_flg == true){

        // 処理なし

    }

    /******************************/
    // エラーなし時のDB処理
    /******************************/
    if ($last_err_flg != true){

        /******************************/
        // 新規登録の場合
        /******************************/
        if ($_POST["get_flg"] != "true"){

            // トランザクション開始
            Db_Query($db_con, "BEGIN;");

            // 最新入金番号取得
            $payin_no = Get_New_Payin_No($db_con);

            // INSERT 入金ヘッダ
            Insert_Payin_H($db_con, $payin_no);

            // INSERT 入金データ
            Insert_Payin_D($db_con, $payin_no, $post_amount, $post_rebate, $rebate_flg);

            // UPDATE 入金番号テーブル
            Insert_Serial($db_con, $payin_no);

            // INSERT 一括入金テーブル
            Insert_Allocate($db_con, $max_row, $payin_no);

            // トランザクション完結
            Db_Query($db_con, "COMMIT;");

            $location = "new";

        /****************************/
        // 変更の場合
        /****************************/
        }elseif ($_POST["get_flg"] == "true"){

            $payin_id = $_POST["hdn_payin_id"];

            // トランザクション開始
            Db_Query($db_con, "BEGIN;");

            // 入金ヘッダ更新
            Update_Payin_H($db_con);

            // 入金データ更新
            Update_Payin_D($db_con, $post_amount, $post_rebate, $rebate_flg);

            // 振分テーブル更新
            Update_Allocate($db_con, $max_row);

            // トランザクション完結
            Db_Query($db_con, "COMMIT;");

            $location = "get";

        }

        // 完了画面へページ遷移
        header("Location: ./2-2-410.php?flg=".$location);

    }
        
}

/****************************/
// 登録変更（参照画面からの遷移）
/****************************/
if ($_GET["payin_id"] != null && $_POST["get_flg"] != "true"){

    // 渡された入金IDから入金データを取得
    // ヘッダ部
    $sql = "SELECT \n";
    $sql .= "   t_payin_h.pay_day, \n";                 // 入金日
    $sql .= "   t_payin_h.client_id, \n";               // 請求先ID
    $sql .= "   t_payin_h.client_cd1, \n";              // 請求先コード１
    $sql .= "   t_payin_h.client_cd2, \n";              // 請求先コード２
    $sql .= "   t_payin_h.client_cname, \n";            // 請求先名（略称）
	$sql .= "	t_payin_h.collect_staff_id, \n";		// 集会担当者ID	--2006/06/11 改修No.3 追加
    $sql .= "   t_payin_h.pay_name, \n";                // 振り込み名義１
    $sql .= "   t_payin_h.account_name, \n";            // 振込名義２
    $sql .= "   t_payin_h.renew_flg, \n";               // 日時更新フラグ
    $sql .= "   t_payin_h.bill_id, \n";                 // 請求書ID
    $sql .= "   t_bill.bill_no, \n";                    // 請求書番号
    $sql .= "   t_payin_d.trade_id, \n";                // 取引区分ID
    $sql .= "   t_bank.bank_id, \n";                    // 銀行コード
    $sql .= "   t_b_bank.b_bank_id, \n";                // 支店コード
    $sql .= "   t_payin_d.account_id, \n";              // 口座番号
    $sql .= "   t_payin_d.payable_day, \n";             // 手形期日
    $sql .= "   t_payin_d.payable_no \n";               // 手形背面番号
    $sql .= "FROM \n";
    $sql .= "   t_payin_h \n";
    $sql .= "   INNER JOIN t_payin_d ON  t_payin_h.pay_id = t_payin_d.pay_id \n";
    $sql .= "   LEFT  JOIN t_bank    ON  t_bank.bank_cd = t_payin_d.bank_cd \n";
    $sql .= "                        AND t_bank.shop_id = ".$_SESSION["client_id"]." \n";
    $sql .= "   LEFT  JOIN t_b_bank  ON  t_b_bank.bank_id = t_bank.bank_id \n";
    $sql .= "                        AND t_b_bank.b_bank_cd = t_payin_d.b_bank_cd \n";
    $sql .= "   LEFT JOIN t_bill     ON  t_bill.bill_id = t_payin_h.bill_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_payin_h.pay_id = ".$_GET["payin_id"]." \n";
    $sql .= "AND \n";
    $sql .= "   trade_id != 35 \n";
    $sql .= "AND \n";
    $sql .= "   t_payin_h.shop_id = ".$_SESSION["client_id"]." \n";
    $sql .= "AND \n";
    $sql .= "   t_payin_h.payin_div = '2' \n";
    $sql .= "; \n";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);

    if ($num > 0){
        $get_payin_data = pg_fetch_array($res);
        $get_flg = true;
    }

    if($get_payin_data["bill_id"] != null){
        $sql  = "SELECT \n";
        $sql .= "   payment_this \n";
        $sql .= "FROM \n";
        $sql .= "   t_bill_d \n";
        $sql .= "WHERE \n";
        $sql .= "   bill_id = ".$get_payin_data["bill_id"]." \n";
        $sql .= "   AND bill_data_div = '0' \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        if ($num > 0){
            $bill_amount = pg_fetch_result($res, 0, 0);
        }
    }

    $set_form["hdn_claim_id"]           = $get_payin_data["client_id"];
    $set_form["form_claim"]["cd1"]      = $get_payin_data["client_cd1"];
    $set_form["form_claim"]["cd2"]      = $get_payin_data["client_cd2"];
    $set_form["form_claim"]["name"]     = $get_payin_data["client_cname"];
    $set_form["form_pay_name"]          = $get_payin_data["pay_name"];
    $set_form["form_account_name"]      = $get_payin_data["account_name"];
    if ($get_payin_data["renew_flg"] == "t"){
        $set_form["renew_flg"]          = true;
        $renew_flg                      = true;
    }
    $set_form["hdn_bill_id"]            = $get_payin_data["bill_id"];
    $set_form["form_bill_no"]           = $get_payin_data["bill_no"];
    $set_form["hdn_bill_no"]            = $get_payin_data["bill_no"];
    $set_form["hdn_post_bill_no"]       = $get_payin_data["bill_no"];
    $set_form["form_bill_amount"]       = $bill_amount;
    $set_form["form_bill2_amount"]      = ($bill_amount != null) ? number_format($bill_amount) : "";
    $set_form["form_payin_day"]["y"]    = substr($get_payin_data["pay_day"],0,4);
    $set_form["form_payin_day"]["m"]    = substr($get_payin_data["pay_day"],5,2);
    $set_form["form_payin_day"]["d"]    = substr($get_payin_data["pay_day"],8,2);
    $set_form["form_trade"]             = $get_payin_data["trade_id"];
	$set_form["form_collect_staff"]		= $get_payin_data["collect_staff_id"];	//-- 2009/06/11 改修No.3 追加
    $set_form["form_bank"][0]           = $get_payin_data["bank_id"];
    $set_form["form_bank"][1]           = $get_payin_data["b_bank_id"];
    $set_form["form_bank"][2]           = $get_payin_data["account_id"];
    $set_form["form_limit_day"]["y"]    = substr($get_payin_data["payable_day"], 0, 4);
    $set_form["form_limit_day"]["m"]    = substr($get_payin_data["payable_day"], 5, 2);
    $set_form["form_limit_day"]["d"]    = substr($get_payin_data["payable_day"], 8, 2);
    $set_form["form_bill_paper_no"]     = $get_payin_data["payable_no"];
    $form->setConstants($set_form);

    //データ部
    $sql  = "SELECT \n";
    if ($get_payin_data["bill_id"] != null){
    $sql .= "   t_bill_d.payment_this, \n";             // 請求番号があった場合、請求金額
    }
    $sql .= "   t_payallocation_d.client_id, \n";       // 得意先ID
    $sql .= "   t_payallocation_d.client_cd1, \n";      // 得意先コード１
    $sql .= "   t_payallocation_d.client_cd2, \n";      // 得意先コード２
    $sql .= "   t_payallocation_d.client_cname, \n";    // 得意先名（略称）
    $sql .= "   t_payallocation_d.trade_id, \n";        // 取引区分ID
    $sql .= "   t_payallocation_d.amount, \n";          // 金額
    $sql .= "   t_payallocation_d.note, \n";            // 備考
    $sql .= "   t_payallocation_d.claim_div \n";
    $sql .= "FROM \n";
    $sql .= "   t_payallocation_d \n";
    if ($get_payin_data["bill_id"] != null){
    $sql .= "   LEFT JOIN t_bill_d ON t_payallocation_d.client_id = t_bill_d.client_id \n";
    }
    $sql .= "WHERE \n";
    $sql .= "   t_payallocation_d.pay_id = ".$_GET["payin_id"]." \n";
    if ($get_payin_data["bill_id"] != null){
    $sql .= "AND \n";
    $sql .= "   t_bill_d.claim_div IS NOT NULL \n";
    $sql .= "AND \n";
    $sql .= "   t_bill_d.bill_id = ".$get_payin_data["bill_id"]." \n";
    }
    $sql .= "ORDER BY \n";
    $sql .= "   t_payallocation_d.client_cd1, \n";
    $sql .= "   t_payallocation_d.client_cd2 \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);

    if ($num > 0){
        $get_allocate_data = pg_fetch_all($res);
        for ($i = 0; $i < count($get_allocate_data); $i++){
            if ($get_allocate_data[$i]["trade_id"] == 35){
                $rebate_array[] = $i;
            }
        }
        $max_row = (count($get_allocate_data) - count($rebate_array));
    }

    for ($i = 0, $j = 0; $i < count($get_allocate_data); $i++){
        if (count($rebate_array) == 0 || array_search($i,$rebate_array) === false){
            $set_form["form_claim_id"][$j]          = $get_allocate_data[$i]["client_id"];
            $set_form["form_claim_cd"][$j]["cd1"]   = $get_allocate_data[$i]["client_cd1"];
            $set_form["form_claim_cd"][$j]["cd2"]   = $get_allocate_data[$i]["client_cd2"];
            $set_form["form_claim_name"][$j]        = $get_allocate_data[$i]["client_cname"];
            $set_form["hdn_claim_div"][$j]          = $get_allocate_data[$i]["claim_div"];
            if ($get_payin_data["bill_id"] != null){
                $set_form["form_base_amount"][$j]   = number_format($get_allocate_data[$i]["payment_this"]);
            }
            $set_form["form_amount"][$j] = ($renew_flg) ? 
                ($get_allocate_data[$i]["amount"] != null) ? 
                    number_format($get_allocate_data[$i]["amount"]) : $get_allocate_data[$i]["amount"]
                : $get_allocate_data[$i]["amount"];
            $set_form["form_note"][$j] = $get_allocate_data[$i]["note"];
            $sum_amount = $sum_amount + $get_allocate_data[$i]["amount"];
            $j++;
        }else{
            if ($get_allocate_data[$i]["client_id"] == $get_allocate_data[$i+1]["client_id"]){
                $set_form["form_rebate"][$j] = ($renew_flg) ?
                    ($get_allocate_data[$i]["amount"] != null) ?
                        number_format($get_allocate_data[$i]["amount"]) : $get_allocate_data[$i]["amount"]
                    : $get_allocate_data[$i]["amount"];
                $sum_rebate = $sum_rebate + $get_allocate_data[$i]["amount"];
            }elseif ($get_allocate_data[$i]["client_id"] == $get_allocate_data[$i-1]["client_id"]){
                $set_form["form_rebate"][$j-1] = ($renew_flg) ?
                    ($get_allocate_data[$i]["amount"] != null) ? 
                        number_format($get_allocate_data[$i]["amount"]) : $get_allocate_data[$i]["amount"]
                    : $get_allocate_data[$i]["amount"];
                $sum_rebate = $sum_rebate + $get_allocate_data[$i]["amount"];
            }
        }
    }

    $set_form["form_amount_total"]  = number_format($sum_amount);
    $set_form["form_rebate_total"]  = number_format($sum_rebate);
    $set_form["form_payin_total"]   = number_format($sum_amount + $sum_rebate);
    $set_form["hdn_max_row"]        = $max_row;
    $set_form["get_flg"]            = "true";
    $form->setConstants($set_form);

}


/****************************/
// フォームパーツ定義
/****************************/
if ($freeze_flg == true){

    for ($i = 0 ; $_POST != null && $i < $max_row; $i++){
        if ($_POST["form_amount"][$i] != null){
            $set_data["form_amount"][$i] = number_format($_POST["form_amount"][$i]);
        }
        if ($_POST["form_rebate"][$i] != null){
            $set_data["form_rebate"][$i] = number_format($_POST["form_rebate"][$i]);
        }
    }
    $set_data["form_bill_amount"] = ($_POST["form_bill_amount"] != null) ? number_format($_POST["form_bill_amount"]) : null;
    $set_data["form_bill2_amount"]= $set_data["form_bill_amount"];
    $form->setConstants($set_data);
    $form->freeze();

// 日次更新済の場合
}elseif ($renew_flg == true){

    $set_data["form_bill_amount"] = ($bill_amount != null) ? number_format($bill_amount) : "";
    $set_data["form_bill2_amount"]= $set_data["form_bill_amount"];
    $form->setConstants($set_data);
    $form->freeze();

}elseif ($_POST["calc_flg"] != null || ($get_flg != null && $_GET[""]!= null)){

    for ($i = 0; $_POST != null && $i < $max_row; $i++){
        $set_data["form_amount"][$i] = str_replace(",", null, $_POST["form_amount"][$i]);
        $set_data["form_rebate"][$i] = str_replace(",", null, $_POST["form_rebate"][$i]);
    }
    $set_data["form_bill_amount"] = str_replace(",", null, $_POST["form_bill_amount"]);
    $set_data["form_bill2_amount"]= ($set_data["form_bill_amount"] != null) ? number_format($set_data["form_bill_amount"]) : "";
    $form->setConstants($set_data);

}

// 請求先検索リンク
$form->addElement("link", "form_claim_search", "", "#", "請求先","tabindex=\"-1\"
    onClick=\"javascript:return Open_SubWin('../dialog/2-0-250.php',
    Array('form_claim[cd1]','form_claim[cd2]','form_claim[name]','hdn_claim_search_flg','hdn_claim_id'),
    500, 450, '2-405', 1);\"
");

// 請求先
$text = null;
$text[] =& $form->createElement("text", "cd1", "", "size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
     onChange=\"javascript:Change_Submit('hdn_claim_search_flg','#','true','form_claim[cd2]');\"
     onkeyup=\"changeText(this.form,'form_claim[cd1]','form_claim[cd2]',6);\" $g_form_option
");
$text[] =& $form->createElement("static", "", "", "-");
$text[] =& $form->createElement("text", "cd2", "", "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
    onChange=\"javascript:Button_Submit('hdn_claim_search_flg','#','true');\" $g_form_option
");
$text[] =& $form->createElement("text", "name", "", "size=\"34\" $g_text_readonly");

if ($get_flg == true || $_POST["get_flg"] == "true"){
    $form->addGroup($text, "form_claim", "")->freeze();
}else{
    $form->addGroup($text, "form_claim", "");
}

// 振込名義1
$form->addElement("text", "form_pay_name", "", "size=\"50\" style=\"border:none;text-align:left;\" readonly");

// 振込名義2
$form->addElement("text", "form_account_name", "", "size=\"30\" style=\"border:none;text-align:left;\" readonly");

// 請求番号
if ($get_flg == true || $_POST["get_flg"] == "true"){
    $form->addElement("text", "form_bill_no", "", "size=\"9\" maxlength=\"8\" style=\"$g_form_style\" $g_form_option")->freeze();
}else{
    $form->addElement("text", "form_bill_no", "", "size=\"9\" maxlength=\"8\" style=\"$g_form_style\" $g_form_option");
}

// 請求金額
$form->addElement("text", "form_bill_amount", "",
    "class=\"money\" size=\"9\" maxlength=\"8\" style=\"border:none;text-align: right; $style\" readonly"
);

// 入金日
Addelement_Date($form, "form_payin_day", "入金日", "-");

// 取引区分
//-- 2009/06/11 改修No.3 「31:集金」の追加
$select_value_trade = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (31, 32, 33, 34, 36, 37) ");
$form->addElement("select", "form_trade", "", $select_value_trade, $g_form_option_select);

// 集金担当者
//-- 2009/06/11 改修No.3 追加
$sql  = "SELECT t_attach.staff_id, t_staff.charge_cd, t_staff.staff_name ";
$sql .= "FROM   t_attach ";
$sql .= "INNER JOIN t_staff ON t_attach.staff_id = t_staff.staff_id ";
$sql .= "WHERE ";
$sql .= ($_SESSION["group_kind"] == "2") ? "   t_attach.shop_id IN (".Rank_Sql().") " 
										 : "   t_attach.shop_id = ".$_SESSION["client_id"]." ";
$sql .= "AND    t_staff.state != '退職' ";
$sql .= "ORDER BY charge_cd ";
$sql .= ";"; 
$result = Db_Query($db_con, $sql);
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
	$data_list[0] = htmlspecialchars($data_list[0]);
	$data_list[1] = htmlspecialchars($data_list[1]);
	$data_list[1] = str_pad($data_list[1], 4, 0, STR_POS_LEFT);
	$data_list[2] = htmlspecialchars($data_list[2]);
	$select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}
if ($group_kind == "2"){
	$form->addElement("select", "form_collect_staff", "", $select_value,
		"onChange=\"window.focus();\" onkeydown=\"chgKeycode();\"");
		//"onChange=\"Staff_Select(); window.focus();\" onkeydown=\"chgKeycode();\"");
}else{
	$form->addElement("select", "form_collect_staff", "", $select_value,
		"onChange=\"window.focus();\" onkeydown=\"chgKeycode();\"");
}
//---

// 銀行
if (($renew_flg && $get_payin_data['bank_id'] == null) || ($freeze_flg && $_POST['form_bank'][0] == null)){
}else{
    $select_value_bank  = Make_Ary_Bank($db_con);
    $attach_html        = "　";
    $obj_bank_select =& $form->addElement("hierselect", "form_bank", "", "", $attach_html);
    $obj_bank_select->setOptions($select_value_bank);
}

// 手形期日
Addelement_Date($form, "form_limit_day", "手形期日", "-");

// 手形券面番号
$form->addElement("text", "form_bill_paper_no", "", "size=\"13\" maxLength=\"10\" style=\"$style $g_form_style\" $g_form_option");

// 入金額合計
$form->addElement("text", "form_amount_total", "", "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// 手数料合計
$form->addElement("text", "form_rebate_total", "", "size=\"16\" maxLength=\"18\"
     style=\"color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// 合計
$form->addElement("text", "form_payin_total", "", "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// 請求金額（画面下）
$form->addElement("text", "form_bill2_amount", "", "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// 確認画面時
if ($freeze_flg == true){
    // 入金ボタン
    $form->addElement("button", "hdn_ok_button", "入　金", "onClick=\"Double_Post_Prevent2(this);\" $disabled");
    // 入金ボタンhidden
    $form->addELement("hidden", "ok_button");
    // 戻るボタン
    $form->addElement("button", "back_button", "戻　る", "onClick=\"javascript:Button_Submit('calc_flg','#','true');\"");
}

// 金額振分ボタン
$form->addElement("button", "divide_button", "金額振分", "onClick=\"javascript:Button_Submit('divide_flg','#','true');\"");

// クリアボタン
$form->addElement("button","clear_button","クリア","onclick=\"location.href='2-2-405.php'\"");

// 合計ボタン
$form->addElement("button", "sum_button", "合　計", "onclick=\"javascript:Button_Submit('calc_flg','#sum','true');\"");

// 入金確認画面へボタン
$form->addElement("submit", "form_verify_btn", "入力確認画面へ");

// 明細画面時の戻るボタン
$form->addElement("button", "get_back_btn", "戻　る", "onclick=\"location.href='2-2-403.php?search=1'\"");

// ヘッダ部リンクボタン
$ary_h_btn_list = array("照会・変更" => "./2-2-403.php", "入　力" => "./2-2-402.php");
Make_H_Link_Btn($form, $ary_h_btn_list, 2);

// 入金内切り替えボタン
$form->addElement("button", "402_button", "得意先単位",   "onClick=\"location.href='2-2-402.php'\"");
$form->addElement("button", "409_button", "銀行単位",     "onClick=\"location.href='2-2-409.php'\"");
$form->addElement("button", "405_button", "親子一括入金", "$g_button_color onClick=\"location.href='2-2-405.php'\"");

// hidden
$form->addElement("hidden", "hdn_max_row");             // 最大行数
$form->addElement("hidden", "hdn_claim_search_flg");    // 請求先検索フラグ
$form->addElement("hidden", "hdn_bill_no");             // 請求書No（自動入力された請求番号をセット）
$form->addElement("hidden", "hdn_post_bill_no");        // 請求書No（フォーム入力された請求番号をセット）
$form->addElement("hidden", "hdn_claim_id");            // 請求先ID
$form->addElement("hidden", "hdn_bill_id");             // 請求先IDから取得した請求書ID
$form->addElement("hidden", "calc_flg");                // 合計フラグ
$form->addElement("hidden", "divide_flg");              // 金額振分フラグ
$form->addElement("hidden", "freeze_flg");              // 入金確認用
$form->addelement("hidden", "hdn_payin_id");            // 変更用請求ID
$form->addelement("hidden", "get_flg");                 // 新規/変更判断用
$form->addelement("hidden", "hdn_enter_day");           // 伝票変更新、該当伝票の作成日時を保持しておく

// エラーセット用
$form->addElement("text", "err_illegal_post");
$form->addElement("text", "err_claim");
$form->addElement("text", "err_count");


/****************************/
// 可変フォームパーツ定義（表）
/****************************/
// セレクトボックスアイテムの配列作成
$select_value_trade = null;
$select_value_trade = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (32, 33, 34, 35, 36, 37, 38) ");
$select_value_bank  = Make_Ary_Bank($db_con);
// 銀行hierselect用連結html
$attach_html        = "</td><td>";

// フォームの見た目をstaticっぽくするためのCSSを変数に入れておく（長いので）
$style = "color: #585858; border: #ffffff 1px solid; background-color: #ffffff;";

for ($i=0; $i<$max_row; $i++){

    // 請求先コード
    $text = null;
    $text[] =& $form->addElement("text", "cd1", "",
        "size=\"7\" maxLength=\"6\" style=\"border:none\" readonly");
    $text[] =& $form->addElement("static", "", "", "-");
    $text[] =& $form->addElement("text", "cd2", "",
        "size=\"4\" maxLength=\"4\" style=\"border:none\" readonly");
    $form->addGroup($text, "form_claim_cd[$i]", "");

    // 請求先名
    $text[] =& $form->addElement("text", "form_claim_name[$i]", "",
        "size=\"34\" style=\"color : #000000; border : #ffffff 1px solid; background-color: #ffffff;\" readonly"
     );
    //元金額
    $form->addElement("text","form_base_amount[$i]","","size=11 style=\"text-align:right;border:none\"  readonly");
    // 金額
    $form->addElement("text", "form_amount[$i]", "",
        "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
    );

    // 手数料
    $form->addElement("text", "form_rebate[$i]", "",
        "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
    );
    // 備考
    $form->addElement("text", "form_note[$i]", "", "size=\"34\" maxLength=\"20\" $g_form_option\"");

    //hidden（請求先ID）
    $form->addElement("hidden","form_claim_id[$i]");

    //hidden（請求先区分）
    $form->addElement("hidden", "hdn_claim_div[$i]");

}


/****************************/
// 請求先の状態取得
/****************************/
// 請求先検索フラグがある場合（請求先検索時）
if ($_POST["hdn_claim_search_flg"] == "true"){
    $claim_state_print = Get_Client_State($db_con, $search_claim_id);
}else
// hiddenの請求先IDがある場合（ダイアログからの請求先検索時、振分などのPOST時）
if ($_POST["hdn_claim_id"] != null){
    $claim_state_print = Get_Client_State($db_con, $_POST["hdn_claim_id"]);
}else
// GETの入金IDがある場合（入金変更時）
if ($_GET["payin_id"] != null){
    $claim_state_print = Get_Client_State($db_con, $get_payin_data["client_id"]);
}


/****************************/
// 表示用html作成
/****************************/
// html用変数定義
$html = null;

// 最大行数分（削除済行含む）ループ
for ($i=0, $j=0; $i<$max_row; $i++){

    // html作成
    $html .= "<tr class=\"Result1\">\n";
    $html .= "  <td align=\"right\">".++$j."</td>\n";                                               // 行番号
    $html .= "  <td>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_claim_cd[$i]"]]->toHtml();          // 請求先コード
    $html .= "      <br>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_claim_name[$i]"]]->toHtml();        // 請求先名
    $html .= "  </td>\n";
    $html .= ($freeze_flg || $renew_flg) ? "    <td align=right>\n":"  <td align=center>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_base_amount[$i]"]]->toHtml();       // はじめに表示している金額
    $html .= "   </td>\n";
    $html .= ($freeze_flg || $renew_flg) ? "    <td align=right>\n":"  <td align=center>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_amount[$i]"]]->toHtml();            // 金額
    $html .= "   </td>\n";
    $html .= ($freeze_flg || $renew_flg) ? "    <td align=right>\n":"   <td align=center>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_rebate[$i]"]]->toHtml();            // 手数料
    $html .= "  </td>\n";
    $html .= "  <td>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_note[$i]"]]->toHtml();              // 備考
    $html .= "  </td>\n";
    $html .= "</tr>\n";

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
$page_menu = Create_Menu_f("sale", "4");

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
    "divide_flg"    => "$divide_flg",
    "renew_flg"     => "$renew_flg",
    "freeze_flg"    => "$freeze_flg",
    "get_flg"       => "$get_flg",

    "illegal_post_flg"  => "$illegal_post_flg",
    "divide_null_flg"   => "$divide_null_flg",
    "claim_state_print" => "$claim_state_print",
));

$smarty->assign("amount_num_err_msg",$amount_num_err_msg);
// テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>

