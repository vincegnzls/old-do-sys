<?php
/*******************************
//変更履歴
//  DB構成の変更に伴う修正（0906）
//
//  (2006-09-15) (kaji)
//  ・DB構成変更に伴う修正
//  ・月次更新後は初期設定できないように変更
//
//  (2006-11-09)(watanabe-k）
//    カラム名変更にともなう修正
//
//  (2007-01-05)(watanabe-k)
//      FCに対し買掛初期残高設定ができるように修正
/******************************/
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2007-01-09      xx-xxx      kajioka-h   仕入先にFCが指定された場合に、担当者名をFCマスタのSVを登録するように変更
 *  2007-01-29      xx-xxx      kajioka-h   月次買掛残高テーブルに取引先区分を残すように変更
 *                  xx-xxx      kajioka-h   月次買掛残高テーブルに支払日を残していなかったバグ修正
 *                  xx-xxx      watanabe-k  残高移行日を表示するように修正
 *  2009/12/24                  aoyama-n    税率をTaxRateクラスから取得
 *
 */

$page_title = "買掛残高初期設定";

//環境設定ファイル
require_once("ENV_local.php");

$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

$conn = Db_Connect();

// 権限チェック
$auth       = Auth_Check($conn);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//外部変数取得
/****************************/
$shop_id = $_SESSION["client_id"];

#2009-12-24 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);

/****************************/
// フォームデフォルト値
/****************************/
$def_fdata["form_state_radio"] = "1";
$def_fdata["form_zandaka_radio"] = "2";
$def_fdata["static_sum"] = "static";
$form->setDefaults($def_fdata);


/****************************/
// フォームパーツ作成
/****************************/
// 状態
$radio = null; 
$radio[] =& $form->createElement("radio", null, null, "取引中", "1");
$radio[] =& $form->createElement("radio", null, null, "解約・休止中", "2");
//$radio[] =& $form->createElement("radio", null, null, "解約", "3");
$radio[] =& $form->createElement("radio", null, null, "全て", "4");
$form->addGroup($radio, "form_state_radio", "");

// 買掛残高
$radio = null; 
$radio[] =& $form->createElement("radio", null, null, "0のみ", "1");
$radio[] =& $form->createElement("radio", null, null, "全て", "2");
$form->addGroup($radio, "form_zandaka_radio", "");

// 表示ボタン
$form->addElement("button", "form_show_button", "表　示", "onClick=\"javascript:Button_Submit('show_button_flg','#','true')\"");

// 残高移行年月日
$text3_1[] =& $form->createElement(
    "text", "y", "", "size=\"4\" maxLength=\"4\" 
    style=\"$g_form_style\" 
    onkeyup=\"changeText(this.form,'form_close_day[y]','form_close_day[m]',4)\" 
    onFocus=\"onForm_today(this,this.form,'form_close_day[y]','form_close_day[m]','form_close_day[d]')\" 
    onBlur=\"blurForm(this)\""
);
$text3_1[] =& $form->createElement("static", "", "", "-");
$text3_1[] =& $form->createElement(
    "text", "m", "", "size=\"1\" maxLength=\"2\" 
    style=\"$g_form_style\" 
    onkeyup=\"changeText(this.form,'form_close_day[m]','form_close_day[d]',4)\" 
    onFocus=\"onForm_today(this,this.form,'form_close_day[y]','form_close_day[m]','form_close_day[d]')\" 
    onBlur=\"blurForm(this)\""
);
$text3_1[] =& $form->createElement("static", "", "", "-");
$text3_1[] =& $form->createElement(
    "text", "d", "", "size=\"1\" maxLength=\"2\" 
    style=\"$g_form_style\" 
    onFocus=\"onForm_today(this,this.form,'form_close_day[y]','form_close_day[m]','form_close_day[d]')\" 
    onBlur=\"blurForm(this)\""
);

// 残高合計
$form->addElement("static", "static_sum", "");

// 登録ボタン
$form->addElement(
    "submit", "form_entry_button", "登　録", 
    "onClick=\"javascript:return Dialogue('登録します。','./1-1-305.php')\" $disabled"
);

//エラー埋め込み用フォーム
$form->addElement("text", "form_set_error","","");

// hidden
$form->addElement("hidden", "show_button_flg", "");


/****************************/
//登録ボタン押下処理
/****************************/
if($_POST["form_entry_button"] == "登　録"){

    //POST取得
    $close_day_y = str_pad($_POST["form_close_day"]["y"], 4, "0", STR_PAD_LEFT);    //締日
    $close_day_m = str_pad($_POST["form_close_day"]["m"], 2, "0", STR_PAD_LEFT);
    $close_day_d = str_pad($_POST["form_close_day"]["d"], 2, "0", STR_PAD_LEFT);

    for($i = 0; $i < count($_POST["form_init_cbln"]); $i++){
        if($_POST["form_init_cbln"][$i] != null && $_POST["hdn_input_flg"][$i] == 'f'){
            $init_cbln[] = $_POST["form_init_cbln"][$i];    //買掛残高
            $client_id[] = $_POST["hdn_client_id"][$i];
        }
    }

    //日付エラーチェック
    $form->addGroupRule('form_close_day', array(
        'y' => array(
                array('残高移行年月日の日付は妥当ではありません。', 'required'),
                array('残高移行年月日の日付は妥当ではありません。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('残高移行年月日の日付は妥当ではありません。', 'required'),
                array('残高移行年月日の日付は妥当ではありません。', "regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('残高移行年月日の日付は妥当ではありません。', 'required'),
                array('残高移行年月日の日付は妥当ではありません。', "regex", "/^[0-9]+$/")
        ),
    ));
    if(!checkdate((int)$close_day_m, (int)$close_day_d, (int)$close_day_y)){
        $form->setElementError("form_close_day","残高移行年月日の日付は妥当ではありません。");
        $err_flg = true;
    }elseif($close_day_y."-".$close_day_m."-".$close_day_d > date("Y-m-d")){
        $form->setElementError("form_close_day","残高移行年月日の日付は妥当ではありません。");
        $err_flg = true;
    } 
/*
    //月次更新より後の日付かチェック
    if($err_flg != true){
        $sql = "SELECT MAX(close_day) FROM t_sys_renew WHERE renew_div = '2' AND shop_id = $shop_id;";
        $result = Db_Query($conn, $sql);
        $close_day_last = (pg_fetch_result($result, 0, 0) != null) ? pg_fetch_result($result, 0, 0) : START_DAY;
        if($close_day_y."-".$close_day_m."-".$close_day_d <= $close_day_last){
            $form->setElementError("form_close_day","残高以降年月日 は、前回月次更新年月日 より先の日付を入力してください。");
            $err_flg = true;
        }
    }
*/
    //月次更新より後の日付かチェック
    if($err_flg != true){
        $err_mess = Sys_Start_Date_Chk($close_day_y, $close_day_m, $close_day_d, "移行年月日");
        if($err_mess != null){
            $form->setElementError("form_close_day",$err_mess);
            $err_flg = true;
        }else{
            $sql = "SELECT MAX(close_day) FROM t_sys_renew WHERE renew_div = '2' AND shop_id = $shop_id;";
            $result = Db_Query($conn, $sql);
            $close_day_last = (pg_fetch_result($result, 0, 0) != null) ? pg_fetch_result($result, 0, 0) : START_DAY;


            if($close_day_y."-".$close_day_m."-".$close_day_d <= $close_day_last){
                $form->setElementError("form_close_day","残高以降年月日 は、前回月次更新年月日 より先の日付を入力してください。");
                $err_flg = true;
            }
        }
    }

    //買掛残高入力チェック
    if(count($init_cbln) == 0){
        $form->setElementError("form_set_error","買掛初期残高は数値のみ入力可能です。");
        $err_flg = true;
    } 

    for($i = 0; $i < count($init_cbln); $i++){

        //半角チェック
        if(!ereg("^[-]?[0-9]+$", $init_cbln[$i])){
            $form->setElementError("form_set_error","買掛初期残高は数値のみ入力可能です。");
            $err_flg = true;
            break;
        }

        //仕入ヘッダ、支払いテーブルに登録がないか確認
        //仕入ヘッダに問い合わせ
        $sql  = "SELECT";
        $sql .= "   count(buy_id)";
        $sql .= " FROM";
        $sql .= "   t_buy_h";
        $sql .= " WHERE";
        $sql .= "   client_id = $client_id[$i]";
        $sql .= ";";

        $result = Db_Query($conn, $sql);
        $buy_count = pg_fetch_result($result,0,0);

        //仕入ヘッダに登録がない場合
        if($buy_count >  0){
            $form->setElementError(
                "form_set_error",
                "既に仕入が起こっているため、買掛初期残高を設定できません。"
            );
            $err_flg = true;
            break;
        }
            
        //支払いヘッダに問い合わせ
        $sql  = "SELECT";
        $sql .= "   count(pay_id)";
        $sql .= " FROM";
        $sql .= "   t_payout_h";
        $sql .= " WHERE";
        $sql .= "   client_id = $client_id[$i]";
        $sql .= ";";

        $result = Db_Query($conn, $sql);
        $pay_count = pg_fetch_result($result,0,0);

        if($pay_count > 0){
            $form->setElementError(
                "form_set_error",
                "既に支払が起こっているため、買掛初期残高を設定できません。"
            );
            $err_flg = true;
            break;
        }
    }

    /***********************/
    //検証
    /***********************/
    if($form->validate() && $err_flg != true){

        $close_day = $close_day_y."-".$close_day_m."-".$close_day_d;

        #2009-12-24 aoyama-n
        $tax_rate_obj->setTaxRateDay($close_day);

        /***************************/
        //登録処理
        /**************************/
        Db_Query($conn, "BEGIN;");
        for($i = 0; $i < count($init_cbln); $i++){

            //仕入先の2重登録をチェック
            $sql  = "SELECT";
            $sql .= "   count(client_id)";
            $sql .= " FROM";
            $sql .= "   t_first_ap_balance";
            $sql .= " WHERE";
            $sql .= "   client_id = $client_id[$i]";
            $sql .= "   AND\n";
            $sql .= "   shop_id = $shop_id\n";
            $sql .= ";";

            $result = Db_Query($conn, $sql);
            $add_count = pg_fetch_result($result,0,0);
            if($add_count > 0){
                continue;
            }

            //初期買掛残高テーブル
            $sql  = "INSERT INTO t_first_ap_balance (";
            $sql .= "   ap_balance,";
            $sql .= "   client_id,";
            $sql .= "   shop_id";
            $sql .= ") VALUES (";
            $sql .= "   $init_cbln[$i],";
            $sql .= "   $client_id[$i],";
            $sql .= "   $shop_id";
            $sql .= ");";

            $result = Db_Query($conn, $sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }

            #2009-12-24 aoyama-n
            $tax_num = $tax_rate_obj->getClientTaxRate($client_id[$i]);

            //仕入先の情報を取得
            $sql  = "
                SELECT 
                    t_client.close_day, 
                    t_client.payout_m, 
                    t_client.payout_d, 
                    t_client.client_cd1, 
                    t_client.client_cd2, 
                    t_client.client_name AS client_name1, 
                    t_client.client_name2, 
                    t_client.client_cname, 
                    t_staff1.staff_name AS staff1_name, 
                    t_client.c_tax_div, 
                    t_client.tax_franct, 
                    t_client.coax, 
                    t_client.tax_div, 
                    t_client.client_div 
                FROM 
                    t_client 
                    LEFT JOIN t_staff AS t_staff1 ON 
                        CASE t_client.client_div 
                            WHEN '2' THEN t_client.c_staff_id1 = t_staff1.staff_id 
                            ELSE          t_client.sv_staff_id = t_staff1.staff_id 
                        END 
                WHERE 
                    t_client.client_id = $client_id[$i] 
                ;
            ";

            $result = Db_Query($conn, $sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }
            $client_data = pg_fetch_array($result);

            foreach ($client_data as $key => $value){
                $client_data[$key] = addslashes($value);
            }

            //買掛残高テーブル
/*
            $sql  = "INSERT INTO t_ap_balance (";
            $sql .= "   ap_balance,";
            $sql .= "   close_day,";
            $sql .= "   client_id,";
            $sql .= "   shop_id";
            $sql .= ") VALUES (";
            $sql .= "   $init_cbln[$i],";
            $sql .= "   '$close_day',";
            $sql .= "   $client_id[$i],";
            $sql .= "   $shop_id";
            $sql .= ");";
*/
            $sql  = "
                INSERT INTO 
                    t_ap_balance 
                ( 
                    ap_balance_id, 
                    monthly_close_day_last, 
                    monthly_close_day_this, 
                    close_day, 
                    payout_m, 
                    payout_d, 
                    client_id, 
                    client_cd1, 
                    client_cd2, 
                    client_name1, 
                    client_name2, 
                    client_cname, 
                    ap_balance_last, 
                    pay_amount, 
                    tune_amount, 
                    rest_amount, 
                    net_buy_amount, 
                    tax_amount, 
                    intax_buy_amount, 
                    ap_balance_this, 
                    staff1_name, 
                    amortization_trade_balance, 
                    amortization_amount, 
                    shop_id, 
                    tax_rate_n, 
                    c_tax_div, 
                    tax_franct, 
                    coax, 
                    tax_div, 
                    client_div 
                ) VALUES ( 
                    (SELECT COALESCE(MAX(ap_balance_id), 0)+1 FROM t_ap_balance), 
                    '".START_DAY."', 
                    '$close_day', 
                    '".$client_data["close_day"]."', 
                    '".$client_data["payout_m"]."', 
                    '".$client_data["payout_d"]."', 
                    $client_id[$i], 
                    '".$client_data["client_cd1"]."', 
                    '".$client_data["client_cd2"]."', 
                    '".addslashes($client_data["client_name1"])."', 
                    '".addslashes($client_data["client_name2"])."', 
                    '".addslashes($client_data["client_cname"])."', 
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
                    0, 
                    $init_cbln[$i], 
                    '".addslashes($client_data["staff1_name"])."', 
                    0, 
                    0, 
                    $shop_id, 
                    '".$tax_num."',
                    '".$client_data["c_tax_div"]."', 
                    '".$client_data["tax_franct"]."', 
                    '".$client_data["coax"]."', 
                    '".$client_data["tax_div"]."', 
                    '".$client_data["client_div"]."' 
                );
            ";
                    #2009-12-24 aoyama-n
                    #(SELECT tax_rate_n FROM t_client WHERE client_id = $shop_id), 

            $result = Db_Query($conn, $sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }

            //$add_msg = "登録しました。";

        }

        Db_Query($conn, "COMMIT;");

        $add_msg = "登録しました。";
    }
}

/****************************/
// 一覧表示処理
/****************************/
if ($_POST["show_button_flg"] == true){

    // 状態を取得
    $state      = $_POST["form_state_radio"];
    $zandaka    = $_POST["form_zandaka_radio"];

    // WHERE句作成
    $where_sql  = null;
    $where_sql  = ($state == 1) ? " AND t_client.state = '1' " : $where_sql;
    $where_sql  = ($state == 2) ? " AND t_client.state = '2' " : $where_sql;
    $where_sql  = ($state == 3) ? " AND t_client.state = '3' " : $where_sql;

    $where_sql .= ($zandaka == 1) ? " AND t_first_ap_balance.ap_balance = 0 " : null;

    //--支払OR仕入を起こしているAND初期残高未設定
    $sql  = "SELECT\n";
    $sql .= "    t_client.client_id,\n";
    $sql .= "    t_client.client_cd1,\n";
    $sql .= "    t_client.client_cd2,\n";
    $sql .= "    t_client.client_name,\n";
    $sql .= "    t_client.client_name2,\n";
    $sql .= "    t_first_ap_balance.ap_balance,\n";
    $sql .= "    't' AS input_flg,\n";
    $sql .= "    t_client.client_div,\n";
    $sql .= "    null ";
    $sql .= " FROM\n";
    $sql .= "    t_client\n";
    $sql .= "        LEFT JOIN\n";
    $sql .= "    t_buy_h\n";
    $sql .= "    ON t_client.client_id = t_buy_h.client_id\n";
    $sql .= "        LEFT JOIN\n";
//    $sql .= "    t_payout\n";
    $sql .= "    t_payout_h\n";
//    $sql .= "    ON t_client.client_id = t_payout.client_id\n";
    $sql .= "    ON t_client.client_id = t_payout_h.client_id\n";
    $sql .= "        LEFT JOIN\n";
    $sql .= "    t_first_ap_balance\n";
    $sql .= "    ON t_client.client_id = t_first_ap_balance.client_id\n";
    $sql .= " WHERE\n";
    $sql .= "    t_client.shop_id = $shop_id\n";
    $sql .= "    AND\n";
//    $sql .= "    t_client.client_div = '2'\n";
//    $sql .= "    t_client.client_div IN ('2', '3')\n";
    $sql .= "    t_client.client_div = '3'\n";
    $sql .= "    AND\n";
    $sql .= "    ((t_buy_h.client_id IS NOT NULL\n";
    $sql .= "    AND\n";
    $sql .= "    t_buy_h.shop_id = $shop_id)\n";
    $sql .= "    OR\n";
//    $sql .= "    t_payout.client_id IS NOT NULL)\n";
    $sql .= "    (t_payout_h.shop_id = $shop_id\n";
    $sql .= "    AND\n";
    $sql .= "    t_payout_h.client_id IS NOT NULL))\n";
    $sql .= "    AND\n";
    $sql .= "    t_first_ap_balance.client_id IS NULL\n";
    $sql .= $where_sql;
    $sql .= " GROUP BY\n";
    $sql .= "   t_client.client_id,\n";
    $sql .= "   t_client.client_cd1,\n";
    $sql .= "   t_client.client_cd2,\n";
    $sql .= "   t_client.client_name,\n";
    $sql .= "   t_client.client_name2,\n";
    $sql .= "   t_first_ap_balance.ap_balance,\n";
    $sql .= "   t_client.client_div \n";
    $sql .= " UNION ALL\n";
    //--初期残高設定済
    $sql .= " SELECT\n";
    $sql .= "    t_client.client_id,\n";
    $sql .= "    t_client.client_cd1,\n";
    $sql .= "    t_client.client_cd2,\n";
    $sql .= "    t_client.client_name,\n";
    $sql .= "    t_client.client_name2,\n";
    $sql .= "    t_first_ap_balance.ap_balance,\n";
    $sql .= "   't' AS input_flg,\n";
    $sql .= "    t_client.client_div, \n";
    $sql .= "    t_ap_balance.close_day ";
    $sql .= " FROM\n";
    $sql .= "    t_client\n";
    $sql .= "        LEFT JOIN\n";
    $sql .= "    t_first_ap_balance\n";
    $sql .= "    ON t_client.client_id = t_first_ap_balance.client_id\n";
    $sql .= "        LEFT JOIN ";
    $sql .= "    (SELECT ";
    $sql .= "       client_id, ";
    $sql .= "       MIN(monthly_close_day_this) AS close_day ";
    $sql .= "    FROM ";
    $sql .= "       t_ap_balance ";
    $sql .= "    WHERE ";
    $sql .= "       shop_id = $shop_id ";
    $sql .= "    GROUP BY ";
    $sql .= "       client_id ";
    $sql .= "    ) AS t_ap_balance ";
    $sql .= "    ON t_client.client_id = t_ap_balance.client_id ";

    $sql .= " WHERE\n";
    $sql .= "    t_client.shop_id = $shop_id\n";
    $sql .= "    AND\n";
//    $sql .= "    t_client.client_div IN ('2', '3')\n";
    $sql .= "    t_client.client_div = '3'\n";
//    $sql .= "    t_client.client_div = '2'\n";
    $sql .= "    AND\n";
    $sql .= "    t_first_ap_balance.client_id IS NOT NULL\n";
    $sql .= "    AND\n";
    $sql .= "    t_first_ap_balance.shop_id = $shop_id\n";
    $sql .= $where_sql;
    $sql .= "UNION ALL\n";
    //--支払いOR仕入を起こしていないAND未設定
    $sql .= "  SELECT\n";
    $sql .= "    t_client.client_id,\n";
    $sql .= "    t_client.client_cd1,\n";
    $sql .= "    t_client.client_cd2, \n";
    $sql .= "    t_client.client_name,\n";
    $sql .= "    t_client.client_name2,\n";
    $sql .= "    t_first_ap_balance.ap_balance,\n";
    $sql .= "   'f' AS input_flg,\n";
    $sql .= "    t_client.client_div, ";
    $sql .= "    null ";
    $sql .= " FROM\n";
    $sql .= "    t_client\n";
    $sql .= "        LEFT JOIN\n";
    $sql .= "    t_buy_h\n";
    $sql .= "    ON t_client.client_id = t_buy_h.client_id\n";
    $sql .= "    AND t_buy_h.shop_id = $shop_id \n";
    $sql .= "        LEFT JOIN\n";
//    $sql .= "    t_payout\n";
    $sql .= "    t_payout_h\n";
//    $sql .= "    ON t_client.client_id = t_payout.client_id\n";
    $sql .= "    ON t_client.client_id = t_payout_h.client_id\n";
    $sql .= "    AND t_payout_h.shop_id = $shop_id \n";
    $sql .= "        LEFT JOIN\n";
    $sql .= "    t_first_ap_balance\n";
    $sql .= "    ON t_client.client_id = t_first_ap_balance.client_id\n";
    $sql .= "    AND t_first_ap_balance.shop_id = $shop_id\n";
    $sql .= " WHERE\n";
    $sql .= "    t_client.shop_id = $shop_id\n";
    $sql .= "    AND\n";
//    $sql .= "    t_client.client_div IN ('2', '3')\n";
    $sql .= "    t_client.client_div = '3'\n";
    $sql .= "    AND\n";
    $sql .= "    t_buy_h.client_id IS NULL\n";
    $sql .= "    AND \n";
    $sql .= "    t_payout_h.client_id IS NULL\n";
    $sql .= "    AND\n";
    $sql .= "    t_first_ap_balance.client_id IS NULL\n";
    $sql .= $where_sql;
    $sql .= " ORDER BY client_div, client_cd1, client_cd2\n";
    $sql .= ";\n";

    $result = Db_Query($conn, $sql);
    $match_count = pg_num_rows($result);
    $show_data = Get_Data($result);

    $form->addGroup( $text3_1, "form_close_day", "残高移行年月日");

    /****************************/
    // フォームパーツ作成
    /****************************/
    $num = 0;
    for($i = 0; $i < count($show_data); $i++){
        // 月次更新済か確認し、済の場合は初期設定は不可
        $sql  = "SELECT ";
        $sql .= "    COUNT(ap_balance_id) ";
        $sql .= "FROM ";
        $sql .= "    t_ap_balance ";
        $sql .= "WHERE ";
        $sql .= "    client_id = ".$show_data[$i][0]." ";
        $sql .= "    AND shop_id = $shop_id ";
        $sql .= ";";

        $result = Db_Query($conn, $sql);
        $result_count = pg_fetch_result($result, 0, 0);

        // 買掛残高
        //入力できるもの
        //if($show_data[$i][5] == 'f'){
        if($show_data[$i][6] == 'f' && $result_count == 0){
            $form->addElement(
                "text", "form_init_cbln[$i]", "", "class=\"money\" size=\"11\" maxLength=\"9\" 
                $g_form_option style=\"text-align: right; $g_form_style\""
            );
            $set_data["form_init_cbln[$i]"] = "";
        }else{
            $freeze =& $form->addElement(
                "text", "form_init_cbln[$i]", "","size =\"13\"
                style=\"text-align : right; 
                border : #ffffff 1px solid; 
                background-color: #ffffff;\" 
                readonly"
            );

            $set_data["form_init_cbln[$i]"] = "";

            if($show_data[$i][5] != null){
                $set_data["form_init_cbln[$i]"] = number_format($show_data[$i][5]);
            }else{
                $set_data["form_init_cbln[$i]"] = "0";
            }

            // 金額を足していく
            $num += $show_data[$i][5];

        }
        //仕入先ID
        $form->addElement("hidden","hdn_client_id[$i]");
        //登録済フラグ
        $form->addElement("hidden","hdn_input_flg[$i]");

        $set_data["hdn_client_id[$i]"] = $show_data[$i][0];
        $set_data["hdn_input_flg[$i]"] = $show_data[$i][6];
    }

    // 金額合計をセット
    $set_data["static_sum"] = number_format($num);

    $form->setConstants($set_data);

}


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
//$page_menu = Create_Menu_h('system','3');

/****************************/
//画面ヘッダー作成
/****************************/
$page_header = Create_Header($page_title);

/****************************/
//ページ作成
/****************************/
// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());

//その他の変数をassign
$smarty->assign('var',array(
    'html_header'   => "$html_header",
    //'page_menu'     => "$page_menu",
    'page_header'   => "$page_header",
    'html_footer'   => "$html_footer",
    'html_page'     => "$html_page",
    'html_page2'    => "$html_page2",
    'match_count'   => "$match_count",
    'add_msg'       => "$add_msg",
    'auth_r_msg'    => "$auth_r_msg",
));

$smarty->assign("show_data", $show_data);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
