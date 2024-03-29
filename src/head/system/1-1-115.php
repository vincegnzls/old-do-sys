<?php
/*******************************
edit history 変更履歴
    ・集金日の項目変更
    ・「締日」の右側に「集金方法」を表示し、「集金日」の右側は空けておくよう変更。
    ・「集金方法」に「自動引落」を追加し「納品時」を削除
    ・「取引区分」で「現金売上」を選択した場合は、「締日」「集金日」は空白を選択する。
    ・種別を追加
    ・請求先のチェック処理変更    
   （2006/07/21）口座区分登録(suzuki)
    (2006/07/31) 課税区分の登録変更処理追加（watanabe-k）
    (2006/08/07) 戻るボタンの遷移先変更（watanabe-k）
    (2006/08/29) 締日と支払日の妥当性チェック追加（watanbe-k）
    2006/11/13  0033    kaku-m  TELFAX番号のチェックを関数で行うように修正。
*******************************/

/*
 *history  履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2009/12/25                  aoyama-n    課税区分の内税を削除
 *   2016/01/21                amano  Button_Submit_1 関数でボタン名が送られない IE11 バグ対応  
*/

$page_title = "得意先マスタ";

//environment setting 環境設定ファイル
require_once("ENV_local.php");

//create HTML_QuickForm HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]",null,
             "onSubmit=return confirm(true)");

//Connect to Database DBに接続
$conn = Db_Connect();

// authorization check 権限チェック
$auth       = Auth_Check($conn);
// no input・edit authorization message 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// button disabled ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;
// delete button disabled 削除ボタンDisabled
$del_disabled = ($auth[1] == "f") ? "disabled" : null;

/****************************/
//set initial value 初期値設定(radio・select)
/****************************/
/*
$def_data["form_state"] = 1;
$def_data["form_slip_out"] = 1;
$def_data["form_deliver_radio"] = 1;
$def_data["form_claim_out"] = 1;
$def_data["form_coax"] = 1;
$def_data["form_tax_div"] = 2;
$def_data["form_tax_franct"] = 1;
$def_data["form_claim_send"] = 1;
$def_data["form_bank_div"] = 1;
$def_data["form_prefix"] = 1;
//$def_data["form_claim_scope"] = 't';
$def_data["form_type"] = 1;
$def_data["form_c_tax_div"] = "1";

$form->setDefaults($def_data);
*/

/****************************/
//distinguish if new 新規判別
/****************************/
$shop_id = $_SESSION[client_id];
$staff_id = $_SESSION[staff_id];
if(isset($_GET["client_id"])){
    $get_client_id = $_GET["client_id"];
    $new_flg = false;
}else{
    $new_flg = true;
}

/* Check the validyt of the ID that was GETGETしたIDの正当性チェック */
if ($_GET["client_id"] != null && Get_Id_Check_Db($conn, $_GET["client_id"], "client_id", "t_client", "num", " client_div = '1' ") != true){
    header("Location: ../top.php");
}elseif($_GET == null){
    header("Location: ../top.php");
}

/****************************/
//initial setting (if there is a GET) 初期設定（GETがある場合）
/****************************/
if($new_flg == false){
    $select_sql  = "SELECT \n";
    $select_sql .= "    t_client.client_cd1,\n";    
    $select_sql .= "    t_client.client_cd2,\n";
    $select_sql .= "    t_client.state,\n";
    $select_sql .= "    t_client.client_name,\n";
    $select_sql .= "    t_client.client_read,\n";
    $select_sql .= "    t_client.client_cname,\n";
    $select_sql .= "    t_client.post_no1,\n";
    $select_sql .= "    t_client.post_no2,\n";
    $select_sql .= "    t_client.address1,\n";
    $select_sql .= "    t_client.address2,\n";
    $select_sql .= "    t_client.address_read,\n";
    $select_sql .= "    t_client.area_id,\n";
    $select_sql .= "    t_client.tel,\n";
    $select_sql .= "    t_client.fax,\n";
    $select_sql .= "    t_client.rep_name,\n";
    $select_sql .= "    t_client.charger1,\n";
    $select_sql .= "    t_client.charger2,\n";
    $select_sql .= "    t_client.charger3,\n";
    $select_sql .= "    t_client.charger_part1,\n";
    $select_sql .= "    t_client.charger_part2,\n";
    $select_sql .= "    t_client.trade_stime1,\n";
    $select_sql .= "    t_client.trade_etime1,\n";
    $select_sql .= "    t_client.trade_stime2,\n";
    $select_sql .= "    t_client.trade_etime2,\n";
    $select_sql .= "    t_client.holiday,\n";
    $select_sql .= "    t_client.sbtype_id,\n";
    $select_sql .= "    t_client.b_struct,\n";
    $select_sql .= "    t_client_claim.client_cd1,\n";
    $select_sql .= "    t_client_claim.client_cd2,\n";
    $select_sql .= "    t_client_claim.client_name,\n";
    $select_sql .= "    t_client_intro_act.client_cd1,\n";
    $select_sql .= "    t_client_intro_act.client_name,\n";
    //$select_sql .= "    t_client.intro_ac_price,\n";
    //$select_sql .= "    t_client.intro_ac_rate,\n";
    $select_sql .= "    '',\n";
    $select_sql .= "    '',\n";
    $select_sql .= "    t_client_info.cclient_shop,\n";
    $select_sql .= "    t_client.c_staff_id1,\n";
    $select_sql .= "    t_client.c_staff_id2,\n";
//     $select_sql .= "    t_client.d_staff_id1,";
//     $select_sql .= "    t_client.d_staff_id2,";
//     $select_sql .= "    t_client.d_staff_id3,";
    $select_sql .= "    t_client.col_terms,\n";
    $select_sql .= "    t_client.credit_limit,\n";
    $select_sql .= "    t_client.capital,\n";
    $select_sql .= "    t_client.trade_id,\n";
    $select_sql .= "    t_client.close_day,\n";
    $select_sql .= "    t_client.pay_m,\n";
    $select_sql .= "    t_client.pay_d,\n";
    $select_sql .= "    t_client.pay_way,\n";
//    $select_sql .= "    t_client.b_bank_id,\n";
    $select_sql .= "    t_account.b_bank_id,\n";
    $select_sql .= "    t_client.pay_name,\n";
    $select_sql .= "    t_client.account_name,\n";
    $select_sql .= "    t_client.cont_sday,\n";
    $select_sql .= "    t_client.cont_eday,\n";
    $select_sql .= "    t_client.cont_peri,\n";
    $select_sql .= "    t_client.cont_rday,\n";
    $select_sql .= "    t_client.slip_out,\n";
    $select_sql .= "    t_client.deliver_note,\n";
    $select_sql .= "    t_client.claim_out,\n";
    $select_sql .= "    t_client.coax,\n";
    $select_sql .= "    t_client.tax_div,\n";
    $select_sql .= "    t_client.tax_franct,\n";
    $select_sql .= "    t_client.note,\n";
    $select_sql .= "    t_client.email,\n";
    $select_sql .= "    t_client.url,\n";
    $select_sql .= "    t_client.rep_htel,\n";
    $select_sql .= "    t_client.direct_tel,\n";
    $select_sql .= "    t_client.b_struct,\n";
    $select_sql .= "    t_client.inst_id,\n";
    $select_sql .= "    t_client.establish_day,\n";
    $select_sql .= "    t_client.deal_history,\n";
    $select_sql .= "    t_client.importance,\n";
    $select_sql .= "    t_client.intro_ac_name,\n";
    $select_sql .= "    t_client.intro_bank,\n";
    $select_sql .= "    t_client.intro_ac_num,\n";
    $select_sql .= "    t_client.round_day,\n";
    $select_sql .= "    t_client.deliver_effect,\n";
    $select_sql .= "    t_client.claim_send,\n";
    $select_sql .= "    t_client.charger_part3,\n";
    $select_sql .= "    t_client.client_div,\n";
    $select_sql .= "    t_client.client_cread,\n";
    $select_sql .= "    t_client.represe,\n";
    $select_sql .= "    t_b_bank.bank_id,\n";
    $select_sql .= "    t_client.address3,\n";
    $select_sql .= "    t_client.company_name,\n";
    $select_sql .= "    t_client.company_tel,\n";
    $select_sql .= "    t_client.company_address,\n";
    $select_sql .= "    t_client.client_name2,\n";
    $select_sql .= "    t_client.client_read2,\n";
    $select_sql .= "    t_client.charger_represe1,\n";
    $select_sql .= "    t_client.charger_represe2,\n";
    $select_sql .= "    t_client.charger_represe3,\n";
    $select_sql .= "    t_client.charger_note,\n";
    $select_sql .= "    t_client.bank_div,\n";
    $select_sql .= "    t_client.claim_note,\n";
    $select_sql .= "    t_client.client_slip1,\n";
    $select_sql .= "    t_client.client_slip2,\n";
    $select_sql .= "    t_client.parent_rep_name,\n";
    $select_sql .= "    t_client.parent_establish_day,\n";
//    $select_sql .= "    t_client.type,\n";
    $select_sql .= "    '',\n";
    $select_sql .= "    t_client.compellation,\n";
    $select_sql .= "    t_client.act_flg, \n";
    $select_sql .= "    t_client.s_pattern_id,\n";
    $select_sql .= "    t_client.c_pattern_id,\n";
    $select_sql .= "    t_client.charge_branch_id,\n";
    $select_sql .= "    t_client.c_tax_div,\n";
    $select_sql .= "    t_client_claim2.client_cd1 AS claim2_cd1,\n";
    $select_sql .= "    t_client_claim2.client_cd2 AS claim2_cd2,\n";
    $select_sql .= "    t_client_claim2.client_name AS claim2_name,\n";
    $select_sql .= "    t_account.account_id,\n";
    $select_sql .= "    t_client_claim2.claim2_id, \n";
    $select_sql .= "    t_client.client_gr_id, \n";
    $select_sql .= "    CASE\n";
    $select_sql .= "        WHEN t_client.parents_flg IS NULL THEN 'null'\n";
    $select_sql .= "        ELSE CASE t_client.parents_flg\n";
    $select_sql .= "                WHEN 't' THEN 'true'\n";
    $select_sql .= "                WHEN 'f' THEN 'false'\n";
    $select_sql .= "            END \n";
    $select_sql .= "    END AS parents_flg, \n";
    $select_sql .= "    t_client_intro_act.client_div AS intro_act_div,\n";     //classificaiton of customer's accounts introduced (not really sure what it means) 紹介口座先区分
    $select_sql .= "    t_client_intro_act.client_cd2 AS intro_act_cd2, \n";     // introduced customer account code2 (not sure what it means too) 紹介口座先コード２
    $select_sql .= "    t_client_intro_act.intro_account_id  AS intro_act_id, \n";      // introduced account ID 紹介口座先ID
    $select_sql .= "    t_client.shop_id, ";
    $select_sql .= "    fc.client_name AS fc_name,";
    $select_sql .= "    fc.client_cd1 AS fc_cd1,";
    $select_sql .= "    fc.client_cd2 AS fc_cd2,";
    $select_sql .= "    fc.state AS fc_state,";
    $select_sql .= "    t_rank.group_kind, ";

    //invoice creation month 請求書作成月
    $select_sql .= "    t_client_claim.month1_flg, ";
    $select_sql .= "    t_client_claim.month2_flg, ";
    $select_sql .= "    t_client_claim.month3_flg, ";
    $select_sql .= "    t_client_claim.month4_flg, ";
    $select_sql .= "    t_client_claim.month5_flg, ";
    $select_sql .= "    t_client_claim.month6_flg, ";
    $select_sql .= "    t_client_claim.month7_flg, ";
    $select_sql .= "    t_client_claim.month8_flg, ";
    $select_sql .= "    t_client_claim.month9_flg, ";
    $select_sql .= "    t_client_claim.month10_flg, ";
    $select_sql .= "    t_client_claim.month11_flg, ";
    $select_sql .= "    t_client_claim.month12_flg ";

    $select_sql .= " FROM\n";
    $select_sql .= "    t_client\n";
    $select_sql .= "        INNER JOIN\n";
    $select_sql .= "    (SELECT\n";
    $select_sql .= "        t_claim.*,\n";
    $select_sql .= "        t_client.client_cd1,\n";
    $select_sql .= "        t_client.client_cd2,\n";
    $select_sql .= "        t_client.client_cname AS client_name\n";
    $select_sql .= "    FROM\n";
    $select_sql .= "        t_claim\n";
    $select_sql .= "            INNER JOIN\n";
    $select_sql .= "        t_client\n";
    $select_sql .= "        ON t_claim.claim_id = t_client.client_id\n";
    $select_sql .= "        AND t_claim.claim_div = '1'\n";
    $select_sql .= "    )AS t_client_claim\n";
    $select_sql .= "    ON t_client.client_id = t_client_claim.client_id\n";
    $select_sql .= "        LEFT JOIN\n";
    $select_sql .= "    (SELECT\n";
    $select_sql .= "         t_client_info.intro_account_id,\n";
    $select_sql .= "         t_client_info.client_id,\n";
    $select_sql .= "         t_client.client_cd1,\n";
    $select_sql .= "         t_client.client_cd2,\n";
    $select_sql .= "         t_client.client_div,\n";
    $select_sql .= "         t_client.client_cname AS client_name\n";
    $select_sql .= "    FROM\n";
    $select_sql .= "         t_client_info,\n";
    $select_sql .= "         t_client\n";
    $select_sql .= "    WHERE\n";
    $select_sql .= "         t_client.client_id  = t_client_info.intro_account_id\n";
    $select_sql .= "    ) AS t_client_intro_act\n";
    $select_sql .= "    ON t_client.client_id = t_client_intro_act.client_id\n";
    $select_sql .= "        LEFT JOIN\n";
    $select_sql .= "    t_account\n";
    $select_sql .= "    ON t_client.account_id = t_account.account_id\n";
    $select_sql .= "        LEFT JOIN\n";
    $select_sql .= "    t_b_bank\n";
    $select_sql .= "    ON t_account.b_bank_id = t_b_bank.b_bank_id\n";
    $select_sql .= "        LEFT JOIN\n";
    $select_sql .= "    (SELECT\n";
    $select_sql .= "        t_claim.client_id,\n";
    $select_sql .= "        t_client.client_id AS claim2_id,\n";
    $select_sql .= "        t_client.client_cd1,\n";
    $select_sql .= "        t_client.client_cd2,\n";
    $select_sql .= "        t_client.client_cname AS client_name\n";
    $select_sql .= "    FROM\n";
    $select_sql .= "        t_claim\n";
    $select_sql .= "            INNER JOIN\n";
    $select_sql .= "        t_client\n";
    $select_sql .= "        ON t_claim.claim_id = t_client.client_id\n";
    $select_sql .= "        AND t_claim.claim_div = '2'\n";
    $select_sql .= "    ) AS t_client_claim2\n";
    $select_sql .= "    ON t_client.client_id = t_client_claim2.client_id\n";
    $select_sql .= "    LEFT JOIN";
    $select_sql .= "        t_client_info";
    $select_sql .= "    ON t_client.client_id = t_client_info.client_id";
    $select_sql .= "    LEFT JOIN t_rank ";
    $select_sql .= "    ON t_rank.rank_cd = t_client.rank_cd ";
    $select_sql .= "    INNER JOIN (SELECT * FROM t_client WHERE client_div = '0' OR client_div = '3' ) AS fc ON t_client.shop_id = fc.client_id";
    $select_sql .= " WHERE\n";
    $select_sql .= "    t_client.client_id = $_GET[client_id]\n";
    $select_sql .= ";";

    //クエリ発行 issue query
    $result = Db_Query($conn, $select_sql);
    Get_Id_Check($result);
    //データ取得 acquire data
    $client_data = @pg_fetch_array ($result, 0);

    //initial data value 初期値データ
    $defa_data["form_client"]["cd1"]          = $client_data[0];         // customer code 1 得意先コード１
    $defa_data["form_client"]["cd2"]          = $client_data[1];         // customer code 2 得意先コード２
    $defa_data["form_fc"]["cd1"]              = $client_data["fc_cd1"];
    $defa_data["form_fc"]["cd2"]              = $client_data["fc_cd2"];
    $defa_data["form_state"]                  = $client_data[2];         // condition 状態
    $defa_data["form_state_fc"]               = $client_data["fc_state"];  // shop condition ショップ状態
    $defa_data["form_client_name"]            = $client_data[3];         // cusomer name得意先名
    $defa_data["form_fc_name"]                = $client_data["fc_name"];
    $defa_data["form_client_read"]            = $client_data[4];         // customer name (phonetic in katakana) 得意先名(フリガナ)
    $defa_data["form_client_cname"]           = $client_data[5];         // abbreviation 略称
    $defa_data["form_post"]["no1"]            = $client_data[6];         // zip 1 郵便番号１
    $defa_data["form_post"]["no2"]            = $client_data[7];         // zip 2 郵便番号２
    $defa_data["form_address1"]               = $client_data[8];         // address 1 住所１
    $defa_data["form_address2"]               = $client_data[9];         // address 2 住所２
    $defa_data["form_address_read"]           = $client_data[10];        // address (phonetic in katakana) 住所(フリガナ)
    $defa_data["form_area_id"]                   = $client_data[11];        // map 地区
    $defa_data["form_tel"]                    = $client_data[12];        // TEL
    $defa_data["form_fax"]                    = $client_data[13];        // FAX
    $defa_data["form_rep_name"]               = $client_data[14];        // representative's name 代表者氏名
    $defa_data["form_charger1"]               = $client_data[15];        // assignee 1 ご担当者１
    $defa_data["form_charger2"]               = $client_data[16];        // assignee 2 ご担当者２
    $defa_data["form_charger3"]               = $client_data[17];        // assignee 3 ご担当者３
    $defa_data["form_charger_part1"]          = $client_data[18];        // name of the window clerk 窓口ご担当
    $defa_data["form_charger_part2"]          = $client_data[19];        // keyman キーマン
    $trade_stime1[1] = substr($client_data[20],0,2);
    $trade_stime1[2] = substr($client_data[20],3,2);
    $trade_etime1[1] = substr($client_data[21],0,2);
    $trade_etime1[2] = substr($client_data[21],3,2);
    $trade_stime2[1] = substr($client_data[22],0,2);
    $trade_stime2[2] = substr($client_data[22],3,2);
    $trade_etime2[1] = substr($client_data[23],0,2);
    $trade_etime2[2] = substr($client_data[23],3,2);
    $defa_data["form_trade_stime1"]["h"]      = $trade_stime1[1];        // business hour (morning starting time) 営業時間(午前開始時間)
    $defa_data["form_trade_stime1"]["m"]      = $trade_stime1[2];        // business hour (morning starting time) 営業時間(午前開始時間)
    $defa_data["form_trade_etime1"]["h"]      = $trade_etime1[1];        // business hour (morning finishing time) 営業時間(午前終了時間)
    $defa_data["form_trade_etime1"]["m"]      = $trade_etime1[2];        // business hour (morning finishing time) 営業時間(午前終了時間)
    $defa_data["form_trade_stime2"]["h"]      = $trade_stime2[1];        // business hour (afternoon starting time) 営業時間(午後後開始時間)
    $defa_data["form_trade_stime2"]["m"]      = $trade_stime2[2];        // business hour (afternoon starting time) 営業時間(午後開始時間)
    $defa_data["form_trade_etime2"]["h"]      = $trade_etime2[1];        // business hour (afternoon finishing time) 営業時間(午後終了時間)
    $defa_data["form_trade_etime2"]["m"]      = $trade_etime2[2];        // business hour (afternoon finishing time) 営業時間(午後終了時間)
    $defa_data["form_holiday"]                = $client_data[24];        // holiday 休日
    $defa_data["form_btype"]                  = $client_data[25];        // industry type 業種
    $defa_data["form_b_struct"]               = $client_data[26];        // business type 業態
    $defa_data["form_claim"]["cd1"]           = $client_data[27];        // invoice code 1 請求先コード１
    $defa_data["form_claim"]["cd2"]           = $client_data[28];        // invoice code 2請求先コード２
    $defa_data["form_claim"]["name"]          = $client_data[29];        // customer name for this invoice 請求先名

    $defa_data["form_intro_act"]["cd"]        = $client_data[30];        // introduced customer account code 紹介口座先コード
    $defa_data["form_intro_act"]["name"]      = $client_data[31];        // introduced customer account name 紹介口座先名
    /*
        if($client_data[32] != null){
        $defa_data["form_account"]["1"] = checked;
        $check_which = 1;
        $defa_data["form_account"]["price"]       = $client_data[32];        // membership fee 口座料
    }
    if($client_data[33] != null){
        $defa_data["form_account"]["2"] = checked;
        $check_which = 2;
        $defa_data["form_account"]["rate"]        = $client_data[33];        // membership fee (rate) 口座料(率)
    }
        */
    //$defa_data["form_cshop"]                  = $client_data[34];        // assigned branch 担当支店
    $defa_data["form_c_staff_id1"]            = $client_data[35];        // contract assignee 1 契約担当１
    $defa_data["form_c_staff_id2"]            = $client_data[36];        // contract assignee 2 契約担当２
//     $defa_data["form_d_staff_id1"]            = $client_data[37];        // assigned patrolman 1 巡回担当１
//     $defa_data["form_d_staff_id2"]            = $client_data[38];        // assigned patrolman 2 巡回担当２
//     $defa_data["form_d_staff_id3"]            = $client_data[39];        // assigned patrolman 3 巡回担当３
    $defa_data["form_col_terms"]              = $client_data[37];        // collection condition 回収条件
    $defa_data["form_cledit_limit"]           = $client_data[38];        // credit limit 与信限度
    $defa_data["form_capital"]                = $client_data[39];        // capital 資本金
    $defa_data["trade_aord_1"]                = $client_data[40];        // trade classification 取引区分
    $defa_data["form_close"]                  = $client_data[41];        // closing date 締日
    $defa_data["form_pay_m"]                  = $client_data[42];        // collection day (month) 集金日(月)
    $defa_data["form_pay_d"]                  = $client_data[43];        // collection day (day) 集金日(日)
    $defa_data["form_pay_way"]                = $client_data[44];        // collection method 集金方法
    $defa_data["form_bank"][1]                = $client_data[45];        // deposit bank 振込銀行
    $defa_data["form_pay_name"]               = $client_data[46];        // deposit name 振込名義
    $defa_data["form_account_name"]           = $client_data[47];        // account name 口座名義
    $cont_s_day[y] = substr($client_data[48],0,4);
    $cont_s_day[m] = substr($client_data[48],5,2);
    $cont_s_day[d] = substr($client_data[48],8,2);
    $cont_e_day[y] = substr($client_data[49],0,4);
    $cont_e_day[m] = substr($client_data[49],5,2);
    $cont_e_day[d] = substr($client_data[49],8,2);
    $cont_r_day[y] = substr($client_data[51],0,4);
    $cont_r_day[m] = substr($client_data[51],5,2);
    $cont_r_day[d] = substr($client_data[51],8,2);
    $defa_data["form_cont_s_day"]["y"]        = $cont_s_day[y];          // contract date (year) 契約年月日(年)
    $defa_data["form_cont_s_day"]["m"]        = $cont_s_day[m];          // contract date (month) 契約年月日(月)
    $defa_data["form_cont_s_day"]["d"]        = $cont_s_day[d];          // contract date (day) 契約年月日(日)
    $defa_data["form_cont_e_day"]["y"]        = $cont_e_day[y];          // contract end date (year) 契約終了日(年)
    $defa_data["form_cont_e_day"]["m"]        = $cont_e_day[m];          // contract end date (month) 契約終了日(月)
    $defa_data["form_cont_e_day"]["d"]        = $cont_e_day[d];          // contract ending date (day) 契約終了日(日)
    $defa_data["form_cont_peri"]              = $client_data[50];        // contract period 契約期間
    $defa_data["form_cont_r_day"]["y"]        = $cont_r_day[y];          // contract update date (year) 契約更新日(年)
    $defa_data["form_cont_r_day"]["m"]        = $cont_r_day[m];          // contract update date (month) 契約更新日(月)
    $defa_data["form_cont_r_day"]["d"]        = $cont_r_day[d];          // contract update date (day) 契約更新日(日)
    $defa_data["form_slip_out"]               = $client_data[52];        // issue slip 伝票発行
    $defa_data["form_deliver_note"]           = $client_data[53];        // delivery note comment 納品書コメント
    $defa_data["form_claim_out"]              = $client_data[54];        // invoice issueance請求書発行
    $defa_data["form_coax"]                   = $client_data[55];        // round　off 丸め
    $defa_data["form_tax_div"]                = $client_data[56];        // consumption tax (taxable unit) 消費税(課税単位)
    $defa_data["form_tax_franct"]             = $client_data[57];        // consumption tax (faction classification) fraction classification doesn't make sense to me but I think it means round off/up the last digits 消費税(端数区分)
    $defa_data["form_note"]                   = $client_data[58];        // facility/equipment information ・ others 設備情報等・その他
    $defa_data["form_email"]                  = $client_data[59];        // Email
    $defa_data["form_url"]                    = $client_data[60];        // URL
    $defa_data["form_represent_cell"]         = $client_data[61];        // representative cellphone 代表者携帯
    $defa_data["form_direct_tel"]             = $client_data[62];        // direct telephone number 直通TEL
    $defa_data["form_bstruct"]                = $client_data[63];        // business type 業態
    $defa_data["form_inst"]                   = $client_data[64];        // facility 施設
    $establish_day[y] = substr($client_data[65],0,4);
    $establish_day[m] = substr($client_data[65],5,2);
    $establish_day[d] = substr($client_data[65],8,2);
    $defa_data["form_establish_day"]["y"]     = $establish_day[y];       // established date (year) 創業日(年)
    $defa_data["form_establish_day"]["m"]     = $establish_day[m];       // established date (month) 創業日(月)
    $defa_data["form_establish_day"]["d"]     = $establish_day[d];       // established date (day) 創業日(日)
    $defa_data["form_record"]                 = $client_data[66];        // trade history 取引履歴
    $defa_data["form_important"]              = $client_data[67];        // important matter重要事項
    $defa_data["form_trans_account"]          = $client_data[68];        // account name where the money will be transferredお振込先口座名
    $defa_data["form_bank_fc"]                = $client_data[69];        // bank/branch name 銀行/支店名
    $defa_data["form_account_num"]            = $client_data[70];        // account number 口座番号
    $round_start[y] = substr($client_data[71],0,4);
    $round_start[m] = substr($client_data[71],5,2);
    $round_start[d] = substr($client_data[71],8,2);
    $defa_data["form_round_start"]["y"]       = $round_start[y];         // patrol start date (year) 巡回開始日(年)
    $defa_data["form_round_start"]["m"]       = $round_start[m];         // patrol start date (month) 巡回開始日(月)
    $defa_data["form_round_start"]["d"]       = $round_start[d];         // patrol start date (day) 巡回開始日(日)
    $defa_data["form_deliver_radio"]          = $client_data[72];        // delivery note comment (effect) 納品書コメント(効果)
    $defa_data["form_claim_send"]             = $client_data[73];        // send invoice (through mail service) 請求書送付(郵送)
    $defa_data["form_charger_part3"]          = $client_data[74];        // accounting department accounting wicket (difficult to understand even in japanese) 経理会計窓口
    $defa_data["form_cname_read"]             = $client_data[76];        // abbreviation (phonetic in katakana)略称(フリガナ)
    $defa_data["form_rep_position"]           = $client_data[77];        // representative position 代表者役職
    $defa_data["form_bank"][0]                = $client_data[78];        // bank where money will be transferred 振込銀行
    $defa_data["form_address3"]               = $client_data[79];        // address 3 住所３
    $defa_data["form_company_name"]           = $client_data[80];        // parent company name 親会社名
    $defa_data["form_company_tel"]            = $client_data[81];        // parent company TEL 親会社TEL
    $defa_data["form_company_address"]        = $client_data[82];        // parent company address 親会社住所
    $defa_data["form_client_name2"]           = $client_data[83];        // customer name 2 得意先名2
    $defa_data["form_client_read2"]           = $client_data[84];        // customer name 2 (phonetic in katakana) 得意先名2(フリガナ)
    $defa_data["form_charger_represe1"]       = $client_data[85];        // assignee 1 position ご担当者役職１
    $defa_data["form_charger_represe2"]       = $client_data[86];        // assignee 2 position ご担当者役職２
    $defa_data["form_charger_represe3"]       = $client_data[87];        // assignee 3 position ご担当者役職３
    $defa_data["form_charger_note"]           = $client_data[88];        // assignee remarks ご担当者備考
    $defa_data["form_bank_div"]               = $client_data[89];        // assignee remarks ご担当者備考
    $defa_data["form_claim_note"]             = $client_data[90];        // invoice comment 請求書コメント
    $defa_data["form_client_slip1"]           = $client_data[91];        // customer 1 print of the slip 得意先１伝票印字
    $defa_data["form_client_slip2"]           = $client_data[92];        // customer 1 print of the slip 得意先２伝票印字
    $defa_data["form_parent_rep_name"]        = $client_data[93];        // parent company representative name 親会社代表者氏名
    $parent_establish_day[y] = substr($client_data[94],0,4);
    $parent_establish_day[m] = substr($client_data[94],5,2);
    $parent_establish_day[d] = substr($client_data[94],8,2);
    $defa_data["form_parent_establish_day"]["y"]   = $parent_establish_day[y];
    $defa_data["form_parent_establish_day"]["m"]   = $parent_establish_day[m];
    $defa_data["form_parent_establish_day"]["d"]   = $parent_establish_day[d];
//    $defa_data["form_type"]                   = $client_data[95];
    $defa_data["form_prefix"]                 = $client_data[96];
    $defa_data["form_act_flg"]                = $client_data[97];            // *act on somebody's behalf* flag 代行フラグ
    $act_flg                                  = $client_data[97];
    $defa_data["sale_pattern"]       = $client_data[98];            //pattern of issuing slip 伝票発行パターン
    $defa_data["claim_pattern"]       = $client_data[99];            //pattern of issuing invoice 請求書発効パターン
    $defa_data["form_charge_branch_id"]       = $client_data["charge_branch_id"];   //assigned branch担当支店
    $defa_data["form_c_tax_div"]              = $client_data["c_tax_div"];   //taxed division 課税区分
    $defa_data["form_claim2"]["cd1"]          = $client_data["claim2_cd1"];  //invoice destination 2 code 1 請求先２コード１
    $defa_data["form_claim2"]["cd2"]          = $client_data["claim2_cd2"];  //invoice destination 2 code 2 請求先２コード２
    $defa_data["form_claim2"]["name"]         = $client_data["claim2_name"]; //invoice destination 2 names 請求先２名
    $defa_data["form_bank"][2]                = $client_data["account_id"]; //bank account ID 口座ID

    $defa_data["form_client_gr"]              = $client_data["client_gr_id"];
    $defa_data["form_parents_div"]            = $client_data["parents_flg"];
//    $defa_data["form_bank_all"]               = $client_data["bank_name"];

    if($client_data["intro_act_div"] == '3'){
        $defa_data["form_intro_act"]["cd2"]       = $client_data["intro_act_cd2"];  //introduced account CD2 紹介口座先CD2
        $defa_data["form_client_div"]         = '1';
    }elseif($client_data["intro_act_div"] == '2'){
        $defa_data["form_client_div"]         = '2';
    }else{
        $defa_data["form_client_div"]         = '1';
    }

    //invoice created month 請求書作成月
    for($i = 0; $i < 12; $i++){
        $defa_data["claim1_monthly_check"][$i] = ($client_data["month".($i+1)."_flg"] == 't')? $client_data["month".($i+1)."_flg"] : null;
    }


    //set initial value 初期値設定
    $form->setDefaults($defa_data);

    $id_data = Make_Get_Id($conn, "client",$client_data[0].",".$client_data[1]);
    $next_id = $id_data[0];
    $back_id = $id_data[1];

	if($shop_id != $client_data[79]){
		$complete_flg = true;
	}

    //Whether you yourself are being registered as invoice destination in other customers, 自分が請求先として他の得意先に登録されているか
    $sql  = "SELECT";
    $sql .= "   count(client_id)";
    $sql .= " FROM";
    $sql .= "   t_client_info";
    $sql .= " WHERE";
    $sql .= "   client_id <> $get_client_id";
    $sql .= "   AND";
    $sql .= "   claim_id = $get_client_id";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $claim_num = pg_fetch_result($result,0,0);

    if($claim_num > 0){
        $change_flg = true;
    }else{
        $change_flg = false;
    }

}
$client_div = $client_data[75];
/***************************/
//create form フォーム作成
/***************************/
//classification 種別
$form_type[] =& $form->createElement( "radio",NULL,NULL, "リピート","1");
$form_type[] =& $form->createElement( "radio",NULL,NULL, "リピート外","2");
$form->addGroup($form_type, "form_type", "");

//group グループ
$select_value = null;
//$select_value = Select_Get($conn, "client_gr");
$sql  = "SELECT client_gr_id, client_gr_cd, client_gr_name ";
$sql .= "FROM t_client_gr ";
$sql .= "WHERE ";
if($client_data["group_kind"] == "2"){
    $sql .= "    shop_id IN (".Rank_Sql().")";
}else{
    $sql .= "     shop_id = ".$client_data["shop_id"];
}
$sql .= " ORDER BY client_gr_cd ";
$sql .= ";";
$result = Db_Query($conn,$sql);
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
    $data_list[0] = htmlspecialchars($data_list[0]);
    $data_list[1] = htmlspecialchars($data_list[1]);
    $data_list[2] = htmlspecialchars($data_list[2]);
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}

$form->addElement("select", "form_client_gr", "",$select_value,$g_form_option_select);
//$form->addElement("text","form_client_gr","");

$form_parents_div[] =& $form->createElement("radio", null, null, "親", "true");
$form_parents_div[] =& $form->createElement("radio", null, null, "子", "false");
$form_parents_div[] =& $form->createElement("radio", null, null, "独立", "null");
$form->addGroup($form_parents_div, "form_parents_div", "");

//customer code 得意先コード
$form_client[] =& $form->createElement(
        "text","cd1","","size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_client[cd1]','form_client[cd2]',6)\"".$g_form_option."\""
        );
$form_client[] =& $form->createElement(
        "static","","","-"
        );
$form_client[] =& $form->createElement(
        "text","cd2","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" $g_form_option"
        );
$form->addGroup( $form_client, "form_client", "");

//shop code ショップコード
$form_fc[] =& $form->createElement(
        "text","cd1","","size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_fc[cd1]','form_fc[cd2]',6)\"".$g_form_option."\""
        );
$form_fc[] =& $form->createElement(
        "static","","","-"
        );
$form_fc[] =& $form->createElement(
        "text","cd2","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" $g_form_option"
        );
$form->addGroup( $form_fc, "form_fc", "");


//shope name ショップ名
$form->addElement(
        "text","form_fc_name","",'size="44" maxLength="25"'." $g_form_option"
        );


//currently transacting 取引中
$text = null;
$text[] =& $form->createElement( "radio",NULL,NULL, "取引中","1");
$text[] =& $form->createElement("radio", null, null, "解約・休止中", "2");
//$text[] =& $form->createElement( "radio",NULL,NULL, "解約","3");
$form->addGroup($text, "form_state", "");


//currently transacting shop 取引中ショップ
$text = null;
$text[] =& $form->createElement( "radio",NULL,NULL, "取引中","1");
$text[] =& $form->createElement("radio", null, null, "解約・休止中", "2");
//$text[] =& $form->createElement( "radio",NULL,NULL, "解約","3");
$form->addGroup($text, "form_state_fc", "");


//customer name 得意先名
$form->addElement(
        "text","form_client_name","",'size="44" maxLength="25"'." $g_form_option"
        );

//customer name (phonetic katakana) 得意先名（フリガナ）
$form->addElement(
        "text","form_client_read","",'size="46" maxLength="50"'." $g_form_option"
        );

//customer name 2 得意先名２
$form->addElement(
        "text","form_client_name2","",'size="44" maxLength="25"'." $g_form_option"
        );

//customer name 1 print of slip 得意先名１伝票印字
$form->addElement("checkbox", "form_client_slip1", "");

//customer name 2 print of slip 得意先名２伝票印字
$form->addElement("checkbox", "form_client_slip2", "");


//customer name 2 (phonetic in katakana) 得意先名２（フリガナ）
$form->addElement(
        "text","form_client_read2","",'size="46" maxLength="50"'." $g_form_option"
        );

//abbreviation 略称
$form->addElement(
        "text","form_client_cname","",'size="44" maxLength="20"'." $g_form_option"
        );

//abbreviation (phonetic in katakana) 略称（フリガナ）
$form->addElement(
        "text","form_cname_read","",'size="46" maxLength="40"'." $g_form_option"
        );

//postal code 郵便番号
$form_post[] =& $form->createElement(
        "text","no1","","size=\"3\" maxLength=\"3\" style=\"$g_form_style\"  onkeyup=\"changeText(this.form,'form_post[no1]','form_post[no2]',3)\"".$g_form_option."\""
        );
$form_post[] =& $form->createElement(
        "static","","","-"
        );
$form_post[] =& $form->createElement(
        "text","no2","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"  $g_form_option"
        );
$form->addGroup( $form_post, "form_post", "");

//address 1 住所１
$form->addElement(
        "text","form_address1","",'size="44" maxLength="25"'." $g_form_option"
        );

//address 2 住所２
$form->addElement(
        "text","form_address2","",'size="46" maxLength="25"'." $g_form_option"
        );

//address 3 住所3
$form->addElement(
        "text","form_address3","",'size="44" maxLength="30"'." $g_form_option"
        );

//address (phonetic in katakana) 住所(フリガナ)
$form->addElement(
        "text","form_address_read","",'size="46" maxLength="50"'." $g_form_option"
        );

//postal code (zip) 郵便番号
//if the autofill button is being pressed 自動入力ボタンが押下された場合
if($_POST["input_button_flg"]==true){
    $post1     = $_POST["form_post"]["no1"];             //postal code 1 郵便番号１
    $post2     = $_POST["form_post"]["no2"];             //postal code 2 郵便番号２
    $post_value = Post_Get($post1,$post2,$conn);
    //clear the postal code flag郵便番号フラグをクリア
    $cons_data["input_button_flg"] = "";
    //autofill from the postal code 郵便番号から自動入力
    $cons_data["form_post"]["no1"] = $_POST["form_post"]["no1"];
    $cons_data["form_post"]["no2"] = $_POST["form_post"]["no2"];
    $cons_data["form_address_read"] = $post_value[0];
    $cons_data["form_address1"] = $post_value[1];
    $cons_data["form_address2"] = $post_value[2];

    $form->setConstants($cons_data);
}

//map 地区
//$select_ary = Select_Get($conn,'area');
if($client_data["group_kind"] == '2'){
    $where = "WHERE shop_id IN (".Rank_Sql().")";
}else{
    $where = "WHERE shop_id = ".$client_data["shop_id"];
}
$sqla = "SELECT area_id,area_cd,area_name ";
$sqla .= "FROM t_area ";
$sqla .= $where;
$sqla .= " ORDER BY area_cd";
$sqla .= ";";
$result = Db_Query($conn,$sqla);
$select_value[""] = "";
while($data_list = pg_fetch_array($result) ){
    $data_list[0] = htmlspecialchars($data_list[0]);
    $data_list[1] = htmlspecialchars($data_list[1]);
    $data_list[2] = htmlspecialchars($data_list[2]);
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}
//$form->addElement('text', 'form_area_id',"","size=\"22\"style=\"$g_form_style\"$g_form_option");
$form->addElement("select", "form_area_id", "",$select_value,$g_form_option_select);
//TEL
$form->addElement(
        "text","form_tel","","size=\"34\" maxLength=\"30\" style=\"$g_form_style\" $g_form_option"
        );

//FAX
$form->addElement(
        "text","form_fax","","size=\"34\" maxLength=\"30\" style=\"$g_form_style\" $g_form_option"
        );

//Email
$form->addElement(
        "text","form_email","","size=\"34\" maxLength=\"60\" style=\"$g_form_style\" $g_form_option"
        );

//URL
$form->addElement(
        "text","form_url","テキストフォーム","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

//representative 代表者
$form->addElement(
        "text","form_rep_name","",'size="34" maxLength="15"'." $g_form_option"
        );

//position of the representative 代表者役職
$form->addElement(
        "text","form_rep_position","",'size="22" maxLength="10"'." $g_form_option"
        );

//representative cellphone 代表者携帯
$form->addElement(
        "text","form_represent_cell","テキストフォーム","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

//parent company name 親会社名
$form->addElement(
        "text","form_company_name","","size=\"44\" maxLength=\"30\"
        $g_form_option"        );      

//parent company TEL 親会社TEL
$form->addElement(
        "text","form_company_tel","","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );      

//parent company address 親会社住所
$form->addElement(
        "text","form_company_address","","size=\"120\" maxLength=\"100\" 
        $g_form_option"
        );

//parent company establishment date 親会社創業日
$form_parent_establish_day[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_parent_establish_day[y]','form_parent_establish_day[m]',4)\" 
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form_parent_establish_day[] =& $form->createElement(
        "static","","","-"
        );
$form_parent_establish_day[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_parent_establish_day[m]','form_parent_establish_day[d]',2)\" 
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form_parent_establish_day[] =& $form->createElement(
        "static","","","-"
        );
$form_parent_establish_day[] =& $form->createElement(
        "text","d","","size=\"2\" maxLength=\"2\" style=\"$g_form_style\" 
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form->addGroup( $form_parent_establish_day,"form_parent_establish_day","");

//parent company representative name 親会社代表者氏名
$form->addElement(
        "text","form_parent_rep_name","",'size="34" maxLength="15"'." $g_form_option"
        );

//**************************************//
//changed assignee 担当者変更（2006/04/20）
//**************************************//
//department 1 部署１
$form->addElement(
        "text","form_charger_part1","","size=\"22\" maxLength=\"10\" $g_form_option"
);
//department 2 部署２
$form->addElement(
        "text","form_charger_part2","","size=\"22\" maxLength=\"10\" $g_form_option"
);
//department 3 部署３
$form->addElement(
        "text","form_charger_part3","","size=\"22\" maxLength=\"10\" $g_form_option"
);
//position 1 役職１
$form->addElement(
        "text","form_charger_represe1","","size\"22\" maxLength=\"10\" $g_form_option"
);
//position 2 役職２
$form->addElement(
        "text","form_charger_represe2","","size\"22\" maxLength=\"10\" $g_form_option"
);
//position 3 役職３
$form->addElement(
        "text","form_charger_represe3","","size\"22\" maxLength=\"10\" $g_form_option"
);

//assignee 1 ご担当1
$form->addElement(
        "text","form_charger1","",'size="34" maxLength="15"'." $g_form_option"
        );

//assignee 2 ご担当2
$form->addElement(
        "text","form_charger2","",'size="34" maxLength="15"'." $g_form_option"
        );

//assignee 3 ご担当3
$form->addElement(
        "text","form_charger3","",'size="34" maxLength="15"'." $g_form_option"
        );

//assignee remarks 担当者備考
$form->addElement(
         "textarea","form_charger_note","",' rows="5" cols="75"'." $g_form_option_area"
);

//transaction fee 銀行手数料負担区分
$form_bank_div[] =& $form->createElement( "radio",NULL,NULL, "お客様負担","1");
$form_bank_div[] =& $form->createElement( "radio",NULL,NULL, "自社負担","2");
$form->addGroup($form_bank_div, "form_bank_div", "");

/***************************************/

//employee hour 営業時間
//morning starting hour 午前開始時間
$form_stime1[] =& $form->createElement(
        "text","h","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_trade_stime1[h]','form_trade_stime1[m]',2)\"".$g_form_option."\""
        );
$form_stime1[] =& $form->createElement(
        "static","","",":"
        );
$form_stime1[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_trade_stime1[m]','form_trade_etime1[h]',2)\"".$g_form_option."\""
        );
$form->addGroup( $form_stime1,"form_trade_stime1","");

//morning ending hour 午前終了時間
$form_etime1[] =& $form->createElement(
        "text","h","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_trade_etime1[h]','form_trade_etime1[m]',2)\"".$g_form_option."\""
        );
$form_etime1[] =& $form->createElement(
        "static","","",":"
        );
$form_etime1[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_trade_etime1[m]','form_trade_stime2[h]',2)\"".$g_form_option."\""
        );
$form->addGroup( $form_etime1,"form_trade_etime1","");

//afternoon starting hour 午後開始時間
$form_stime2[] =& $form->createElement(
        "text","h","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_trade_stime2[h]','form_trade_stime2[m]',2)\"".$g_form_option."\""
        );
$form_stime2[] =& $form->createElement(
        "static","","",":"
        );
$form_stime2[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_trade_stime2[m]','form_trade_etime2[h]',2)\"".$g_form_option."\""
        );
$form->addGroup( $form_stime2,"form_trade_stime2","");

//afternoon ending hour午後終了時間
$form_etime2[] =& $form->createElement(
        "text","h","","size=\"2\" maxLength=\"2\" style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_trade_etime2[h]','form_trade_etime2[m]',2)\"".$g_form_option."\""
        );
$form_etime2[] =& $form->createElement(
        "static","","",":"
        );
$form_etime2[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\" style=\"$g_form_style\"  $g_form_option"
        );
$form->addGroup( $form_etime2,"form_trade_etime2","");

//holiday 休日
$form->addElement(
        "text","form_holiday","",'size="22" maxLength="10"'." $g_form_option"
        );

//industry 業種
$sql  = "SELECT";
$sql .= "   t_lbtype.lbtype_cd,";
$sql .= "   t_lbtype.lbtype_name,";
$sql .= "   t_sbtype.sbtype_id,";
$sql .= "   t_sbtype.sbtype_cd,";
$sql .= "   t_sbtype.sbtype_name";
$sql .= " FROM";
$sql .= "   (SELECT";
$sql .= "       t_lbtype.lbtype_id,";
$sql .= "       t_lbtype.lbtype_cd,";
$sql .= "       t_lbtype.lbtype_name";
$sql .= "   FROM";
$sql .= "       t_lbtype";
$sql .= "   WHERE";
$sql .= "       accept_flg = '1'";
$sql .= "   ) AS t_lbtype";
$sql .= "   INNER JOIN";
$sql .= "   (SELECT";
$sql .= "       t_sbtype.sbtype_id,";
$sql .= "       t_sbtype.lbtype_id,";
$sql .= "       t_sbtype.sbtype_cd,";
$sql .= "       t_sbtype.sbtype_name";
$sql .= "   FROM";
$sql .= "       t_sbtype";
$sql .= "   WHERE";
$sql .= "       accept_flg = '1'";
$sql .= "   ) AS t_sbtype";
$sql .= "   ON t_lbtype.lbtype_id = t_sbtype.lbtype_id";
$sql .= " ORDER BY t_lbtype.lbtype_cd, t_sbtype.sbtype_cd";
$sql .= ";";

$result = Db_Query($conn, $sql);

while($data_list = pg_fetch_array($result)){
    if($max_len < mb_strwidth($data_list[1])){
        $max_len = mb_strwidth($data_list[1]);
    }
}

$result = Db_Query($conn, $sql);
$select_value = "";
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
    $space = "";
    for($i = 0; $i< $max_len; $i++){
        if(mb_strwidth($data_list[1]) <= $max_len && $i != 0){
                $data_list[1] = $data_list[1]."　";
        }
    }
    
    $select_value[$data_list[2]] = $data_list[0]." ： ".$data_list[1]."　　 ".$data_list[3]." ： ".$data_list[4];
}

$form->addElement('select', 'form_btype',"", $select_value,$g_form_option_select);

//facility 施設
$sql  = "SELECT";
$sql .= "   inst_id,";
$sql .= "   inst_cd,";
$sql .= "   inst_name";
$sql .= " FROM";
$sql .= "   t_inst";
$sql .= " WHERE";
$sql .= "   accept_flg = '1'";
$sql .= " ORDER BY inst_cd";
$sql .= ";";

$result = Db_Query($conn, $sql);

$select_value = "";
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}
$form->addElement('select', 'form_inst',"", $select_value,$g_form_option_select);

//business type 業態
$sql  = "SELECT";
$sql .= "   bstruct_id,";
$sql .= "   bstruct_cd,";
$sql .= "   bstruct_name";
$sql .= " FROM";
$sql .= "   t_bstruct";
$sql .= " WHERE";
$sql .= "   accept_flg = '1'";
$sql .= " ORDER BY bstruct_cd";
$sql .= ";";

$result = Db_Query($conn, $sql);

$select_value = "";
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}
$form->addElement('select', 'form_bstruct',"", $select_value,$g_form_option_select);

//invoice destination 請求先
$form_claim[] =& $form->createElement(
        "text","cd1","","size=\"7\" maxLength=\"6\"  style=\"$g_form_style\"
        onkeyup=\"javascript:client1('form_claim[cd1]','form_claim[cd2]','form_claim[name]'); 
        changeText(this.form,'form_claim[cd1]','form_claim[cd2]',6)\"".$g_form_option."\"" 
        );
$form_claim[] =& $form->createElement(
        "static","","","-"
        );
$form_claim[] =& $form->createElement(
        "text","cd2","","size=\"4\" maxLength=\"4\"  style=\"$g_form_style\"
        onKeyUp=\"javascript:client1('form_claim[cd1]','form_claim[cd2]','form_claim[name]')\"".$g_form_option."\"" 
        );
$form_claim[] =& $form->createElement(
        "text","name","","size=\"34\" 
        $g_text_readonly"
        );
$freeze = $form->addGroup( $form_claim, "form_claim", "");
if($change_flg == true){
    $freeze->freeze();
}

//invoice destination 2 請求先２
$form_claim2= "";
$form_claim2[] =& $form->createElement("text", "cd1", "", "size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
            onkeyup=\"changeText(this.form, 'form_claim2[cd1]', 'form_claim2[cd2]',6)\"
            onkeyup=\"javascript:claim1('form_claim2[cd1]', 'form_claim2[cd2]', 'form_claim2[name]','','','');
            changeText(this.form,'form_claim2[cd1]','form_claim2[cd2]',6)\"".$g_form_option."\"");
$form_claim2[] =& $form->createElement("static", "", "", "-");
$form_claim2[] =& $form->createElement("text", "cd2", "", "size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onkeyup=\"javascript:claim1('form_claim2[cd1]', 'form_claim2[cd2]', 'form_claim2[name]','','','')\"".$g_form_option."\"");
$form_claim2[] =& $form->createElement("text", "name", "", "size=\"44\" $g_text_readonly");
$freeze = $form->addGroup($form_claim2, "form_claim2", "");

if($change_flg == true){
    $freeze->freeze();
}

//introduced account destination 紹介口座先
/*
$form_intro_act[] =& $form->createElement(
        "text","cd","","size=\"7\" maxLength=\"6\"  style=\"$g_form_style\"
        onKeyUp=\"javascript:client(this,'form_intro_act[name]')\" 
        $g_form_option"
        );
*/
//for only direct management 直営の場合のみ
if($client_data['intro_act_div'] == '3'){
    $form_intro_act[] =& $form->createElement("text", "cd", "", "size=\"7\" maxLength=\"6\"
            style=\"$g_form_style\"
            onKeyUp=\"javascript:client2('form_intro_act[cd]','form_intro_act[cd2]','form_intro_act[name]')\"
            $g_form_option");
    $form_intro_act[] =& $form->createElement("static", "", "", "-");
    $form_intro_act[] =& $form->createElement("text", "cd2", "", "size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onKeyUp=\"javascript:client2('form_intro_act[cd]','form_intro_act[cd2]','form_intro_act[name]')\" $g_form_option");
}else{
    $form_intro_act[] =& $form->createElement("text", "cd", "", "size=\"7\" maxLength=\"6\"
            style=\"$g_form_style\"
            onKeyUp=\"javascript:client2('form_intro_act[cd]','form_intro_act[name]')\"
            $g_form_option");
}

$form_intro_act[] =& $form->createElement(
        "text","name","","size=\"34\" 
        $g_text_readonly"
        );
$form->addGroup( $form_intro_act, "form_intro_act", "");

//radio button for account inquiry 照会口座用ラジオボタン
$form_client_div[] =& $form->createElement("radio", null, null, "FC", "1", "onClick=code_disable(); onChange=client_div();");
$form_client_div[] =& $form->createElement("radio", null, null, "仕入先", "2", "onClick=code_disable(); onChange=client_div();");
$form->addGroup($form_client_div, "form_client_div", "");


//bank account name お振込先口座名
$form->addElement(
        "text","form_trans_account","",'size="34" maxLength="20"'." $g_form_option"
        );

//bank branch name 銀行支店名
$form->addElement(
        "text","form_bank_fc","",'size="34" maxLength="20"'." $g_form_option"
        );

//bank account number 口座番号
$form->addElement(
        "text","form_account_num","","size=\"20\" maxLength=\"15\" style=\"$g_form_style\"  $g_form_option"
        );

//bank account fee (with bank account name) *not sure if this is a membership fee or bank account fee 口座料(口座名義ごと)
$form_account[] =& $form->createElement( 
        "checkbox","1" ,"" ,"" ," 
        onClick='return Check_Button2(1);'"
        );
$form_account[] =& $form->createElement(
        "static","　","","固定金額"
        );
$form_account[] =& $form->createElement(
        "text","price","","size=\"11\" maxLength=\"9\"
        $g_form_option
        style=\"text-align: right; $g_form_style\""
        );
$form_account[] =& $form->createElement(
        "static","","","円　　　　"
        );
$form_account[] =& $form->createElement( 
        "checkbox","2" ,"" ,"" ," 
        onClick='return Check_Button2(2);'"
        );
$form_account[] =& $form->createElement(
        "static","","","売上の"
        );
$form_account[] =& $form->createElement(
        "text","rate","","size=\"3\" maxLength=\"3\" 
        $g_form_option
        style=\"text-align: right; $g_form_style\"");
$form_account[] =& $form->createElement(
        "static","","","％"
        );
$form->addGroup( $form_account, "form_account", "");
//assignee branch 担当支店
//$where  = " WHERE";
//$where .= "     t_client.client_div = '3'";


//$select_ary = Select_Get($conn,'cshop', $where);
//$form->addElement('select', 'form_cshop',"", $select_ary, $g_form_option_select );
//$select_value = Select_Get($conn,'branch');

$sql  = "SELECT branch_id, branch_cd, branch_name ";
$sql .= "FROM t_branch ";
$sql .= "WHERE ";
$sql .= " shop_id = ".$client_data["shop_id"]." ";
$sql .= " ORDER BY branch_cd";
$sql .= ";";

$result = Db_Query($conn,$sql);
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
    $data_list[0] = htmlspecialchars($data_list[0]);
    $data_list[1] = htmlspecialchars($data_list[1]);
    $data_list[2] = htmlspecialchars($data_list[2]);
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}
$form->addElement('select', 'form_charge_branch_id', '支店', $select_value,$g_form_option_select);
//$form->addElement("text","form_charge_branch_id","","");


//contract assignee 1 契約担当1
$sql  = "SELECT ";
$sql .= "    t_staff.staff_id,";
$sql .= "    charge_cd,";
$sql .= "    staff_name ";
$sql .= "FROM ";
$sql .= "    t_staff LEFT JOIN t_attach ON t_staff.staff_id = t_attach.staff_id ";
$sql .= "WHERE ";
if($client_data[group_kind] == '2'){
    $sql .= "   t_attach.shop_id IN (".Rank_Sql().")";
}else{
    $sql .= "   t_attach.shop_id = $client_data[shop_id] ";
}
$sql .= "ORDER BY charge_cd";
$sql .= ";";
$result = Db_Query($conn,$sql);
$select_value = NULL;
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
    $data_list[0] = htmlspecialchars($data_list[0]);
    $data_list[1] = htmlspecialchars($data_list[1]);
    $data_list[1] = str_pad($data_list[1], 4, 0, STR_POS_LEFT);
    $data_list[2] = htmlspecialchars($data_list[2]);
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}       
$form->addElement('select', 'form_c_staff_id1',"", $select_value, $g_form_option_select );
//$form->addElement("text","form_c_staff_id1","","","");

//contract assignee 2 契約担当2
//$select_ary = Select_Get($conn,'cstaff');
$form->addElement('select', 'form_c_staff_id2',"", $select_value, $g_form_option_select );
//$form->addElement("text","form_c_staff_id2","","","");

//assigned patrolman 1巡回担当者1
//$select_ary = Select_Get($conn,'cstaff');
//$form->addElement('select', 'form_d_staff_id1',"", $select_ary, $g_form_option_select );

//assigned patrolman 2巡回担当者2
//$select_ary = Select_Get($conn,'cstaff');
//$form->addElement('select', 'form_d_staff_id2',"", $select_ary, $g_form_option_select );

//assigned patrolman 3巡回担当者3
//$select_ary = Select_Get($conn,'cstaff');
//$form->addElement('select', 'form_d_staff_id3',"", $select_ary, $g_form_option_select );

//patrol start date 巡回開始日
$form_round_start[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_round_start[y]','form_round_start[m]',4)\" 
        ".$g_form_option."\""
        );
$form_round_start[] =& $form->createElement(
        "static","","","-"
        );
$form_round_start[] =& $form->createElement(
        "text","m","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_round_start[m]','form_round_start[d]',2)\" 
        ".$g_form_option."\""
        );
$form_round_start[] =& $form->createElement(
        "static","","","-"
        );
$form_round_start[] =& $form->createElement(
        "text","d","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        ".$g_form_option."\""
        );
$form->addGroup( $form_round_start,"form_round_start","");

//collection condition 回収条件
$form->addElement(
        "text","form_col_terms","",'size="34" maxLength="50"'." $g_form_option"
        );

//credit limit 与信限度
$form->addElement(
        "text","form_cledit_limit","","size=\"11\" maxLength=\"9\" class=\"money\"
        $g_form_option 
        style=\"text-align: right; $g_form_style\""
        );

//capital資本金
$form->addElement(
        "text","form_capital","","size=\"11\" maxLength=\"9\" class=\"money\"
        $g_form_option
        style=\"text-align: right; $g_form_style\""
        );

//trade classification 取引区分
//$select_value = Select_Get($conn,'trade_aord');
//$select_value[11] .= "　(締日、集金日が必須となります。closing date, collection date is a must entry)";
//$form->addElement('select', 'trade_aord_1', 'セレクトボックス', $select_value,
//        "onKeyDown=\"chgKeycode();\" onChange =\"window.focus();pay_way()\"");

$where = "WHERE trade_id = '11' OR trade_id = '61'";
$sql = "SELECT trade_id,trade_cd, trade_name ";
$sql .= "FROM t_trade ";
$sql = $sql.$where;
$sql .= " ORDER BY trade_cd";
$sql .= ";";

$result = Db_Query($conn,$sql);
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
    $data_list[0] = htmlspecialchars($data_list[0]);
    $data_list[1] = htmlspecialchars($data_list[1]);
    $data_list[2] = htmlspecialchars($data_list[2]);
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}

$form->addElement('select', 'trade_aord_1', 'セレクトボックス', $select_value,
        "onKeyDown=\"chgKeycode();\" onChange =\"window.focus();\"");


//closing date 締日
$select_value = Select_Get($conn,'close');
$form->addElement("select", "form_close", "締日", $select_value);

//collection date 集金日
//collection date 集金日
//month 月
$select_month[null] = null;
for($i = 0; $i <= 12; $i++){
    if($i == 0){
        $select_month[0] = "当月";
    }elseif($i == 1){
        $select_month[1] = "翌月";
    }else{
        $select_month[$i] = $i."ヶ月後";
    }
}
$form->addElement("select", "form_pay_m", "セレクトボックス", $select_month, $g_form_option_select);

//day 日
for($i = 0; $i <= 29; $i++){
    if($i == 29){
        $select_day[$i] = '月末';
    }elseif($i == 0){
        $select_day[null] = null;
    }else{
        $select_day[$i] = $i."日";
    }
}
$form->addElement("select", "form_pay_d", "セレクトボックス", $select_day, $g_form_option_select);

//payment method 支払い方法
$select_value = Select_Get($conn, "pay_way");
$form->addElement(
        'select', 'form_pay_way',"", $select_value,$g_form_option_select
        );     

//bank where the transfer of money will happen振込銀行
/*
$select_ary = Select_Get($conn,'bank');
$sql = " WHERE"; 
$sql .= "   t_bank.shop_id = $shop_id";
$ary_b_bank = Select_Get($conn,'b_bank', $sql);
$bank_select = $form->addElement('hierselect', 'form_bank', '',$g_form_option_select 
    ,"</td><td class=\"Title_Purple\"><b>振込銀行支店</b></td><td class=\"value\">");
$bank_select->setOptions(array($select_ary, $ary_b_bank));
*/
//$select_ary = Make_Ary_Bank($conn);
// acquire SQL bank・branch・bank account detail 銀行・支店・口座情報取得SQL
$sql  = "SELECT ";
$sql .= "   t_bank.bank_id, ";
$sql .= "   t_bank.bank_cd, ";
$sql .= "   t_bank.bank_name, ";
$sql .= "   t_b_bank.b_bank_id, ";
$sql .= "   t_b_bank.b_bank_cd, ";
$sql .= "   t_b_bank.b_bank_name, ";
$sql .= "   t_account.account_id, ";
$sql .= "   CASE t_account.deposit_kind ";
$sql .= "       WHEN '1' THEN '普通' ";
$sql .= "       WHEN '2' THEN '当座' ";
$sql .= "   END ";
$sql .= "   AS deposit, ";
$sql .= "   t_account.account_no ";
$sql .= "FROM ";
$sql .= "   t_bank ";
$sql .= "   INNER JOIN t_b_bank ON t_bank.bank_id = t_b_bank.bank_id ";
$sql .= "   INNER JOIN t_account ON t_b_bank.b_bank_id = t_account.b_bank_id ";
$sql .= "WHERE ";
$sql .= ($client_data["group_kind"] == "2") ? " t_bank.shop_id IN (".Rank_Sql().") " : " t_bank.shop_id = ".$client_data["shop_id"]." ";
$sql .= "ORDER BY ";
$sql .= "   t_bank.bank_cd, t_b_bank.b_bank_cd, t_account.account_no ";
$sql .= ";";

$res  = Db_Query($conn, $sql);
$num  = pg_num_rows($res);
// define array for hierselecthierselect 用配列定義
$ary_hier1[null] = null;
$ary_hier2       = null;
$ary_hier3       = null;
// if there is more than 1 transaction 1件以上ある場合
if ($num > 0){

    for ($i=0; $i<$num; $i++){

        // acquire data (per record) データ取得（レコード毎）
        $data_list[$i] = pg_fetch_array($res, $i, PGSQL_ASSOC);

        // substitute different level of ID to the variable 分かりやすいように各階層のIDを変数に代入
        $hier1_id = $data_list[$i]["bank_id"];
        $hier2_id = $data_list[$i]["b_bank_id"];
        $hier3_id = $data_list[$i]["account_id"];

        /* creation process of level 1 array 第1階層配列作成処理 */
        // if the bank code of the currently referred record and the bank code of the previously preferred record is different現在参照レコードの銀行コードと前に参照したレコードの銀行コードが異なる場合
        if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"]){
            // acquired item from level 1 to the array 第1階層取得アイテムを配列へ
//                $ary_hier1[$hier1_id] = $data_list[$i]["bank_cd"]." ： ".htmlspecialchars($data_list[$i]["bank_name"]);
            $ary_hier1[$hier1_id] = $data_list[$i]["bank_cd"]." ： ".htmlentities($data_list[$i]["bank_name"], ENT_COMPAT, EUC);
        }

        /* creation process of level 2 array第2階層配列作成処理 */
        // if the bank code of the currently referred record and the bank code of the previously preferred record is different 現在参照レコードの銀行コードと前に参照したレコードの銀行コードが異なる場合
        // Also, if the branch  code of the currently referred record and the branch code of the previously preferred record is different または、現在参照レコードの支店コードと前に参照したレコードの支店コードが異なる場合
        if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"] ||
            $data_list[$i]["b_bank_cd"] != $data_list[$i-1]["b_bank_cd"]){
            // set NULL in the beginning of level 2 selected item 第2階層セレクトアイテムの最初にNULLを設定
            if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"]){
                $ary_hier2[$hier1_id][null] = "";
            }
            // acquired item from level 1 to the array 第2階層取得アイテムを配列へ
//                $ary_hier2[$hier1_id][$hier2_id] = $data_list[$i]["b_bank_cd"]." ： ".htmlspecialchars($data_list[$i]["b_bank_name"]);
            $ary_hier2[$hier1_id][$hier2_id] = $data_list[$i]["b_bank_cd"]." ： ".htmlentities($data_list[$i]["b_bank_name"], ENT_COMPAT, EUC);
        }

        /* creation process of level 3 array第3階層配列作成処理 */
        // if the bank code of the currently referred record and the bank code of the previously preferred record is different現在参照レコードの銀行コードと前に参照したレコードの銀行コードが異なる場合
        // Also, if the branch  code of the currently referred record and the branch code of the previously preferred record is differentまたは、現在参照レコードの支店コードと前に参照したレコードの支店コードが異なる場合
        // Also, if the bank account number of the currently referred record and the bank account number of the previously preferred record is differentまたは、現在参照レコードの口座番号と前に参照したレコードの口座番号が異なる場合
        if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"] ||
            $data_list[$i]["b_bank_cd"] != $data_list[$i-1]["b_bank_cd"] ||
            $data_list[$i]["account_no"] != $data_list[$i-1]["account_no"]){
            // set NULL in the beginning of level 3 selected item 第3階層セレクトアイテムの最初にNULLを設定
            if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"] ||
                $data_list[$i]["b_bank_cd"] != $data_list[$i-1]["b_bank_cd"]){
                $ary_hier3[$hier1_id][$hier2_id][null] = "";
            }
            // acquired item from level 3 to the array 第3階層取得アイテムを配列へ
            $ary_hier3[$hier1_id][$hier2_id][$hier3_id] = $data_l ist[$i]["deposit"]." ： ".$data_list[$i]["account_no"];
        }

    }
$select_ary = array($ary_hier1, $ary_hier2, $ary_hier3);
}else{
    $ary[null] = "";
    $select_ary = array($ary,$ary,$ary);
}

$bank_select = $form->addElement('hierselect', 'form_bank', '',$g_form_option_select ,"　　　");
$bank_select->setOptions(array($select_ary[0], $select_ary[1], $select_ary[2]));
//$form->addElement("text","form_bank_all","");

//name of the person the money will be transferred to 振込名義1
$form->addElement(
        "text","form_pay_name","",'size="34" maxLength="50"'." $g_form_option"
        );

//bank account name 口座名義
$form->addElement(
        "text","form_account_name","",'size="34" maxLength="15"'." $g_form_option"
        );

//contract date 契約年月日
$form_cont_s_day[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_cont_s_day[y]','form_cont_s_day[m]',4)\" 
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form_cont_s_day[] =& $form->createElement(
        "static","","","-"
        );
$form_cont_s_day[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_cont_s_day[m]','form_cont_s_day[d]',2)\" 
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form_cont_s_day[] =& $form->createElement(
        "static","","","-"
        );
$form_cont_s_day[] =& $form->createElement(
        "text","d","","size=\"2\" maxLength=\"2\"  style=\"$g_form_style\"
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form->addGroup( $form_cont_s_day,"form_cont_s_day","");

//contract end date 契約終了日
$form_cont_e_day[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" 
         style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_cont_e_day[y]','form_cont_e_day[m]',4)\"".$g_form_option."\""
        );
$form_cont_e_day[] =& $form->createElement(
        "static","","","-"
        );
$form_cont_e_day[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\" 
         style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_cont_e_day[m]','form_cont_e_day[d]',2)\"".$g_form_option."\""
        );
$form_cont_e_day[] =& $form->createElement(
        "static","","","-"
        );
$form_cont_e_day[] =& $form->createElement(
        "text","d","","size=\"2\" maxLength=\"2\" 
         style=\"$g_form_style\"".$g_form_option."\""
        );
$form->addGroup( $form_cont_e_day,"form_cont_e_day","");

//contract period 契約期間
$form->addElement(
        "text","form_cont_peri","","size=\"2\" maxLength=\"2\" style=\"text-align: right; $g_form_style\"
        onkeyup=\"Contract(this.form)\" $g_form_option"
        );

//contract update date 契約更新日
$form_cont_r_day[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_cont_r_day[y]','form_cont_r_day[m]',4)\" 
        onchange=\"Contract(this.form)\" $g_form_option"
        );
$form_cont_r_day[] =& $form->createElement(
        "static","","","-"
        );
$form_cont_r_day[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_cont_r_day[m]','form_cont_r_day[d]',2)\" 
        onchange=\"Contract(this.form)\" $g_form_option"
        );
$form_cont_r_day[] =& $form->createElement(
        "static","","","-"
        );
$form_cont_r_day[] =& $form->createElement(
        "text","d","","size=\"2\" maxLength=\"2\" style=\"$g_form_style\"
        onchange=\"Contract(this.form)\" $g_form_option"
        );
$form->addGroup( $form_cont_r_day,"form_cont_r_day","");

//establishment date 創業日
$form_establish_day[] =& $form->createElement(
        "text","y","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_establish_day[y]','form_establish_day[m]',4)\" 
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form_establish_day[] =& $form->createElement(
        "static","","","-"
        );
$form_establish_day[] =& $form->createElement(
        "text","m","テキストフォーム","size=\"2\" maxLength=\"2\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_establish_day[m]','form_establish_day[d]',2)\" 
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form_establish_day[] =& $form->createElement(
        "static","","","-"
        );
$form_establish_day[] =& $form->createElement(
        "text","d","テキストフォーム","size=\"2\" maxLength=\"2\" style=\"$g_form_style\"
        onchange=\"Contract(this.form)\"".$g_form_option."\""
        );
$form->addGroup( $form_establish_day,"form_establish_day","form_establish_day");

//issue slip 伝票発行
$form_slip_out[] =& $form->createElement( "radio",NULL,NULL, "有","1");
$form_slip_out[] =& $form->createElement( "radio",NULL,NULL, "無","2");
$form->addGroup($form_slip_out, "form_slip_out", "");

//delivery note comment 納品書コメント
//radio button ラヂオボタン
$form_deliver_radio[] =& $form->createElement( "radio",NULL,NULL, "コメント無効","1");
$form_deliver_radio[] =& $form->createElement( "radio",NULL,NULL, "個別コメント有効","2");
$form_deliver_radio[] =& $form->createElement( "radio",NULL,NULL, "全体コメント有効","3");
$form->addGroup($form_deliver_radio, "form_deliver_radio", "");
//text テキスト
$form->addElement(
        "textarea","form_deliver_note","",' rows="5" cols="75"'." $g_form_option_area"
        );


//issue invoice請求書発行
$form_claim_out[] =& $form->createElement( "radio",NULL,NULL, "明細請求書","1");
$form_claim_out[] =& $form->createElement( "radio",NULL,NULL, "合計請求書","2");
$form_claim_out[] =& $form->createElement( "radio",NULL,NULL, "個別明細請求書", "5");
$form_claim_out[] =& $form->createElement( "radio",NULL,NULL, "出力しない","3");
$form_claim_out[] =& $form->createElement( "radio",NULL,NULL, "指定",      "4");

$form->addGroup($form_claim_out, "form_claim_out", "");
/*
//invoice range 請求範囲
$form_claim_scope[] =& $form->createElement( "radio",NULL,NULL, "繰越額を含める","t");
$form_claim_scope[] =& $form->createElement( "radio",NULL,NULL, "繰越額を含めない","f");
$form->addGroup($form_claim_scope, "form_claim_scope", "");
*/

//send invoice 請求書送付
$form_claim_send[] =& $form->createElement( "radio",null,NULL, "郵送","1");
$form_claim_send[] =& $form->createElement( "radio",null,NULL, "メール","2");
$form_claim_send[] =& $form->createElement( "radio",null,NULL, "WEB","4");
$form_claim_send[] =& $form->createElement( "radio",null,NULL, "郵送・メール","3");
$form->addGroup($form_claim_send, "form_claim_send", "");

//invoice comment 請求書コメント
$form->addElement(
        "textarea","form_claim_note","",' rows="5" cols="75"'." $g_form_option_area"
        );

//honorific 敬称
$form_prefix_radio[] =& $form->createElement( "radio",NULL,NULL, "御中","1");
$form_prefix_radio[] =& $form->createElement( "radio",NULL,NULL, "様","2");
$form->addGroup($form_prefix_radio,"form_prefix","");
     
//amount 金額
//round off classification 丸め区分
$form_coax[] =& $form->createElement( "radio",NULL,NULL, "切捨","1");
$form_coax[] =& $form->createElement( "radio",NULL,NULL, "四捨五入","2");
$form_coax[] =& $form->createElement( "radio",NULL,NULL, "切上","3");
$form->addGroup($form_coax, "form_coax", "");

//tax unit 課税単位
$form_tax_div[] =& $form->createElement( "radio",NULL,NULL, "伝票単位","2");
$form_tax_div[] =& $form->createElement( "radio",NULL,NULL, "締日単位","1");
$form->addGroup($form_tax_div, "form_tax_div", "");

//fraction classification 端数区分
$form_tax_franct[] =& $form->createElement( "radio",NULL,NULL, "切捨","1");
$form_tax_franct[] =& $form->createElement( "radio",NULL,NULL, "四捨五入","2");
$form_tax_franct[] =& $form->createElement( "radio",NULL,NULL, "切上","3");
$form->addGroup($form_tax_franct, "form_tax_franct", "");

//tax classification課税区分
$form_c_tax_div[] =& $form->createElement( "radio",NULL,NULL, "外税","1");
#2009-12-25 aoyama-n
#$form_c_tax_div[] =& $form->createElement( "radio",NULL,NULL, "内税","2");
$form->addGroup($form_c_tax_div, "form_c_tax_div", "");

//equipment information・others 設備情報等・その他
$form->addElement(
        "textarea","form_note","",' rows="3" cols="75"'." $g_form_option_area"
        );

//trade history 取引履歴
$form->addElement(
        "textarea","form_record","",' rows="3" cols="75"'." $g_form_option_area"
        );

//important matter 重要事項
$form->addElement(
        "textarea","form_important","",' rows="3" cols="75"'." $g_form_option_area"
        );

//hidden
$form->addElement("hidden", "input_button_flg");
$form->addElement("hidden", "ok_button_flg");

//format of the sale slip 売上伝票様式
//$select_value = Select_Get($conn,'pattern');
$select_value="";
$where  = " WHERE";
if($client_data["group_kind"] == "2"){
    $where .= "    shop_id IN (".Rank_Sql().")";
}else{
    $where .= "     shop_id = ".$client_data["shop_id"];
}
$sql = "SELECT s_pattern_id, s_pattern_no, s_pattern_name ";
$sql .= "FROM t_slip_sheet ";
$sql = $sql.$where;
$sql .= " ORDER BY s_pattern_no";
$sql .= ";";

$result = Db_Query($conn,$sql);
while($data_list = pg_fetch_array($result)){
    $data_list[0] = htmlspecialchars($data_list[0]);
    $data_list[1] = htmlspecialchars($data_list[1]);
    $data_list[2] = htmlspecialchars($data_list[2]);
    $select_value[$data_list[0]] = $data_list[1]." ： ".$data_list[2];
}
$form->addElement("select","sale_pattern","",$select_value);

//invoice format 請求書様式
//$select_value = Select_Get($conn,'claim_pattern');
$select_value = "";
$sql = "SELECT c_pattern_id,c_pattern_name ";
$sql .= "FROM t_claim_sheet ";
$sql = $sql.$where;
$sql .= " ORDER BY c_pattern_id";
$sql .= ";";
$result = Db_Query($conn,$sql);
while($data_list = pg_fetch_array($result)){
    $data_list[0] = htmlspecialchars($data_list[0]);
    $data_list[1] = htmlspecialchars($data_list[1]);
    $select_value[$data_list[0]] = $data_list[1];
}
$form->addElement("select","claim_pattern","",$select_value);

//creation month of the invoice (invoice destination 1) 請求作成月（請求先１）
for($i = 0; $i < 12; $i++){
    $form->addElement("checkbox", "claim1_monthly_check[$i]","", ($i+1)."月");
}  

/****************************/
//create rules ルール作成
/****************************/
$form->registerRule("telfax","function","Chk_Telfax");
//■map 地区
//●required field check 必須チェック
$form->addRule("form_area_id", "地区を選択して下さい。","required");

//■industry 業種
//●required field check 必須チェック
$form->addRule("form_btype", "業種を選択して下さい。","required");

$form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
/*
//■FC group FCグループ
//●required field check 必須チェック
$form->addRule("form_shop_gr_1", "FCグループを選択してください。","required");
*/

//■customer code 得意先コード
//●required field check 必須チェック
$form->addGroupRule('form_client', array(
        'cd1' => array(
                array('得意先コードは半角数字のみです。', 'required')
        ),      
        'cd2' => array(
                array('得意先コードは半角数字のみです。','required')
        ),      
));

//●check if it's a half-width number 半角数字チェック
$form->addGroupRule('form_client', array(
        'cd1' => array(
                array('得意先コードは半角数字のみです。', "regex", "/^[0-9]+$/")
        ),      
        'cd2' => array(
                array('得意先コードは半角数字のみです。',"regex", "/^[0-9]+$/")
        ),      
));

//■customer name 得意先名
//●required field check 必須チェック
$form->addRule("form_client_name", "得意先名は1文字以上25文字以下です。","required");
// only check full-width/half-width space 全角/半角スペースのみチェック
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_client_name", "得意先名に スペースのみの登録はできません。", "no_sp_name");

//■abbreviation 略称
//●required field check 必須チェック
$form->addRule("form_client_cname", "略称は1文字以上20文字以下です。","required");
// only check full-width/half-width space 全角/半角スペースのみチェック
$form->addRule("form_client_cname", "略称に スペースのみの登録はできません。", "no_sp_name");

//■zip code 郵便番号
//●required field check 必須チェック
//●half-widthcheck (i think this part will be converted to accept full width and convert to half width. same with others) 半角数字チェック
//●check length 文字数チェック
$form->addGroupRule('form_post', array(
        'no1' => array(
                array('郵便番号は半角数字のみ7桁です。', 'required'),
                array('郵便番号は半角数字のみ7桁です。', "regex", "/^[0-9]+$/"),
                array('郵便番号は半角数字のみ7桁です。', "rangelength", array(3,3))
        ),
        'no2' => array(
                array('郵便番号は半角数字のみ7桁です。','required'),
                array('郵便番号は半角数字のみ7桁です。',"regex", "/^[0-9]+$/"),
                array('郵便番号は半角数字のみ7桁です。', "rangelength", array(4,4))
        ),
));

//■address 1 住所１
//●required field check 必須チェック
$form->addRule("form_address1", "住所１は1文字以上25文字以下です。","required");

//■TEL
//●required field check必須チェック
//●half-width number check 半角数字チェック
$form->addRule(form_tel, "TELは半角数字と｢-｣のみ30桁以内です。", "required");
$form->addRule("form_tel","TELは半角数字と｢-｣のみ30桁以内です。","telfax");

//■representative name 代表者氏名
//●required field check必須チェック
$form->addRule("form_rep_name", "代表者氏名は1文字以上15文字以下です。","required");

//■parent company establishment date 親会社創業日
//●half-width check 半角数字チェック
$form->addGroupRule('form_parent_establish_day', array(
        'y' => array(
                array('親会社の創業日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('親会社の創業日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('親会社の創業日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
));

//■operating hour 営業時間
//■morning starting hour 午前開始時間
//●half-width check半角数字チェック
$form->addGroupRule('form_trade_stime1', array(
        'h' => array(
                array('営業時間は半角数字のみです。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('営業時間は半角数字のみです。',"regex", "/^[0-9]+$/")
        ),
));

//■morning ending time 午前終了時間
//●half width check 半角数字チェック
$form->addGroupRule('form_trade_etime1', array(
        'h' => array(
                array('営業時間は半角数字のみです。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('営業時間は半角数字のみです。',"regex", "/^[0-9]+$/")
        ),
));

//■afternoon starting time 午後開始時間
//●half width check 半角数字チェック
$form->addGroupRule('form_trade_stime2', array(
        'h' => array(
                array('営業時間は半角数字のみです。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('営業時間は半角数字のみです。',"regex", "/^[0-9]+$/")
        ),
));

//■afternoon ending time 午後終了時間
//●half width check 半角数字チェック
$form->addGroupRule('form_trade_etime2', array(
        'h' => array(
                array('営業時間は半角数字のみです。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('営業時間は半角数字のみです。',"regex", "/^[0-9]+$/")
        ),
));

//■bank account fee (membership fee? or fee for maintaining the bank account? not sure) 口座料
//●half width check 半角数字チェック
$form->addGroupRule('form_account', array(
        'price' => array(
                array('口座料は半角数字のみです。', "regex", "/^[0-9]+$/")
        ),
        'rate' => array(
                array('口座料は半角数字のみです。',"regex", "/^[0-9]+$/")
        ),
));

//■assigned branch担当支店
//●input check 入力チェック
$form->addRule("form_charge_branch_id", "担当支店を選択して下さい。","required");

//■credit limit 与信限度
//●half width check 半角数字チェック
$form->addRule("form_cledit_limit", "与信限度は半角数字のみです。", "regex", "/^[0-9]+$/");

//■capital 資本金
//●half width check 半角数字チェック
$form->addRule("form_capital", "資本金は半角数字のみです。", "regex", "/^[0-9]+$/");

//■collection date (month)集金日（月）
//●half width check 半角数字チェック
$form->addRule("form_pay_m", "集金日（月）は半角数字のみです。", "required");
$form->addRule("form_pay_m", "集金日（月）は半角数字のみです。", "regex", "/^[0-9]+$/");

//■collection date (day) 集金日（日）
//●half width check 半角数字チェック
$form->addRule("form_pay_d", "集金日（日）は半角数字のみです。", "required");
$form->addRule("form_pay_d", "集金日（日）は半角数字のみです。", "regex", "/^[0-9]+$/");


//■contract date 契約年月日
//●half width check 半角数字チェック
$form->addGroupRule('form_cont_s_day', array(
        'y' => array(
                array('契約年月日の日付は妥当ではありません。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('契約年月日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('契約年月日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
));
//■contract end date 契約終了日
//●half width check 半角数字チェック
$form->addGroupRule('form_cont_e_day', array(
        'y' => array(
                array('契約終了日の日付は妥当ではありません。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('契約終了日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('契約年月日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
));
//■contract update date 契約更新日
//●half width check 半角数字チェック
$form->addGroupRule('form_cont_r_day', array(
        'y' => array(
                array('契約更新日の日付は妥当ではありません。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('契約更新日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('契約更新日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
));

//■establishment date 創業日
//●half width check 半角数字チェック
$form->addGroupRule('form_establish_day', array(
        'y' => array(
                array('創業日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('創業日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('創業日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
));

//■trade classification 取引区分
//●required field check 必須チェック
$form->addRule("trade_aord_1", "取引区分を選択して下さい。","required");

//■delivery note check 納品書コメント
//●half width check 半角数字チェック
$form->addRule("form_deliver_note", "納品書コメントは50文字以内です。", "mb_maxlength",'50');

//■contract period契約期間
//●half width check 半角数字チェック
$form->addRule("form_cont_peri", "契約期間は半角数字のみです。", "regex", "/^[0-9]+$/");

//■patrol starting date 巡回開始日
//●half width check 半角数字チェック
$form->addGroupRule('form_round_start', array(
        'y' => array(
                array('巡回開始日の日付は妥当ではありません。', "regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('巡回開始日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('巡回開始日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
));

/***************************/
//create rule ルール作成（PHP）
/***************************/
if($_POST["button"]["entry_button"] == "登　録"){
    /****************************/
    //acquire POST POST取得
    /****************************/
//    $shop_gr        = $_POST["form_shop_gr_1"];                 //FC group FCグループ
    $client_cd1         = $_POST["form_client"]["cd1"];             //customer code 1 得意先コード１
    $client_cd2         = $_POST["form_client"]["cd2"];             //customer code 2 得意先コード２
    $state              = $_POST["form_state"];                     //condition 状態
    $state_fc           = $_POST["form_state_fc"];
    $client_name        = $_POST["form_client_name"];               //customer name 得意先名
    $client_read        = $_POST["form_client_read"];               //customer name (phonetic in katakana) 得意先名（フリガナ）
    $client_name2       = $_POST["form_client_name2"];              //customer name 2 得意先名2
    $client_read2       = $_POST["form_client_read2"];              //customer name 2 (phonetic in katakana) 得意先名2（フリガナ）
    $client_cname       = $_POST["form_client_cname"];              //abbreviation 略称
    $cname_read         = $_POST["form_cname_read"];                //abbreviation (phonetic in katakana) 略称（フリガナ）
    $post_no1           = $_POST["form_post"]["no1"];               //postal code 1 郵便番号１
    $post_no2           = $_POST["form_post"]["no2"];               //postal code 2郵便番号２
    $address1           = $_POST["form_address1"];                  //address 1住所１
    $address2           = $_POST["form_address2"];                  //addresss 2住所２
    $address3           = $_POST["form_address3"];                  //address 3住所3
    $address_read       = $_POST["form_address_read"];              //address 1 (phonetic in katakana)住所１（フリガナ）
    $area_id            = $_POST["form_area_id"];                   //district code 地区コード
    $tel                = $_POST["form_tel"];                       //TEL
    $fax                = $_POST["form_fax"];                       //FAX
    $rep_name           = $_POST["form_rep_name"];                  //representative name 代表者氏名

/*20060420追加(add)*/
    $charger1           = $_POST["form_charger1"];                  //assignee1 ご担当者１
    $charger2           = $_POST["form_charger2"];                  //assignee2 ご担当者２
    $charger3           = $_POST["form_charger3"];                  //assignee3 ご担当者３
    $charger_represe1   = $_POST["form_charger_represe1"];          //assignee position 1ご担当者役職１
    $charger_represe2   = $_POST["form_charger_represe2"];          //assignee position 2ご担当者役職２
    $charger_represe3   = $_POST["form_charger_represe3"];          //assignee position 3ご担当者役職３
    $charger_part1      = $_POST["form_charger_part1"];             //assignee department 1ご担当者部署１
    $charger_part2      = $_POST["form_charger_part2"];             //assignee department 2ご担当者部署２
    $charger_part3      = $_POST["form_charger_part3"];             //assignee department 3ご担当者部署３
    $charger_note       = $_POST["form_charger_note"];              //assignee remarksご担当者備考
/**************/

    $trade_stime1       = str_pad($_POST["form_trade_stime1"]["h"],2,0,STR_PAD_LEFT);         //operating hours (morning starting time) 営業時間（午前開始）
    $trade_stime1      .= ":"; 
    $trade_stime1      .= str_pad($_POST["form_trade_stime1"]["m"],2,0,STR_PAD_LEFT);
    $trade_etime1       = str_pad($_POST["form_trade_etime1"]["h"],2,0,STR_PAD_LEFT);         //operating hour (morning ending time) 営業時間（午前終了）
    $trade_etime1      .= ":"; 
    $trade_etime1      .= str_pad($_POST["form_trade_etime1"]["m"],2,0,STR_PAD_LEFT);
    $trade_stime2       = str_pad($_POST["form_trade_stime2"]["h"],2,0,STR_PAD_LEFT);         //operating hour (afternoon starting time) 営業時間（午後開始）
    $trade_stime2      .= ":"; 
    $trade_stime2      .= str_pad($_POST["form_trade_stime2"]["m"],2,0,STR_PAD_LEFT);
    $trade_etime2       = str_pad($_POST["form_trade_etime2"]["h"],2,0,STR_PAD_LEFT);         //operating hour (afternoon ending time) 営業時間（午後終了）
    $trade_etime2      .= ":"; 
    $trade_etime2      .= str_pad($_POST["form_trade_etime2"]["m"],2,0,STR_PAD_LEFT);
    $holiday            = $_POST["form_holiday"];                   //holiday 休日
    $btype              = $_POST["form_btype"];                     //industry code業種コード
    $b_struct           = $_POST["form_b_struct"];                  //business type 業態
    $claim_cd1          = $_POST["form_claim"]["cd1"];              //invoice code 1請求先コード１
    $claim_cd2          = $_POST["form_claim"]["cd2"];              //invoice code 2請求先コード２
    $claim_name         = $_POST["form_claim"]["name"];             //invoice name 請求先名
    $intro_act_cd       = $_POST["form_intro_act"]["cd"];           //introduced bank account code 紹介口座先コード
    $intro_act_name     = $_POST["form_intro_act"]["name"];         //introduced bank account name 紹介口座先名
    $price_check        = $_POST["form_account"]["1"];
    $account_price      = $_POST["form_account"]["price"];          //bank account fee (membership fee? bank account maintaining fee?)口座料
    $rate_check         = $_POST["form_account"]["2"];
    $account_rate       = $_POST["form_account"]["rate"];           //bank account rate 口座率
//    $cshop_id           = $_POST["form_cshop"];                     //assigned branch 担当支店
    $charge_branch_id       = $_POST["form_charge_branch_id"];          // assigned branch 担当支店
    $c_staff_id1        = $_POST["form_c_staff_id1"];               //assigned contract 1 契約担当１
    $c_staff_id2        = $_POST["form_c_staff_id2"];               //assigned contract 2 契約担当２
//    $d_staff_id1        = $_POST["form_d_staff_id1"];               //assigned patrolman 1 巡回担当１
//    $d_staff_id2        = $_POST["form_d_staff_id2"];               //assigned patrolman 2 巡回担当２
//    $d_staff_id3        = $_POST["form_d_staff_id3"];               //assigned patrolman 3 巡回担当３
    $col_terms          = $_POST["form_col_terms"];                 //collection method 回収条件
    $cledit_limit       = $_POST["form_cledit_limit"];              //credit limit 与信限度
    $capital            = $_POST["form_capital"];                   //capital 資本金
    $trade_id           = $_POST["trade_aord_1"];                   //trade classification 取引区分
    $close_day_cd       = $_POST["form_close"];                     //closing date 締日
    $pay_m              = $_POST["form_pay_m"];                     //collection date (month) 集金日（月）
    $pay_d              = $_POST["form_pay_d"];                     //collection date (day) 集金日（日）
    $pay_way            = $_POST["form_pay_way"];                   //collection method 集金方法
//    $bank_enter_cd      = $_POST["form_bank"][1];                   //bank calling code 銀行呼出コード
    $bank_enter_cd          = $_POST["form_bank"][2];
    $pay_name           = $_POST["form_pay_name"];                  //name of the account the money will be transferred to 振込名義
    $account_name       = $_POST["form_account_name"];              //bank account name 口座名義
    $cont_s_day         = $_POST["form_cont_s_day"]["y"];           //contract starting date 契約開始日
    $cont_s_day        .= "-"; 
    $cont_s_day        .= $_POST["form_cont_s_day"]["m"];
    $cont_s_day        .= "-"; 
    $cont_s_day        .= $_POST["form_cont_s_day"]["d"];
    $cont_e_day         = $_POST["form_cont_e_day"]["y"];            //contract ending date 契約終了日
    $cont_e_day        .= "-"; 
    $cont_e_day        .= $_POST["form_cont_e_day"]["m"];
    $cont_e_day        .= "-"; 
    $cont_e_day        .= $_POST["form_cont_e_day"]["d"];
    $cont_peri          = $_POST["form_cont_peri"];                 //contract period 契約期間
    $cont_r_day         = $_POST["form_cont_r_day"]["y"];            //contract update date 契約更新日
    $cont_r_day        .= "-"; 
    $cont_r_day        .= $_POST["form_cont_r_day"]["m"];
    $cont_r_day        .= "-"; 
    $cont_r_day        .= $_POST["form_cont_r_day"]["d"];
    $slip_out           = $_POST["form_slip_out"];                  //issue slip 伝票発行
    $deliver_note       = $_POST["form_deliver_note"];              //delivery note comment 納品書コメント
    $claim_out          = $_POST["form_claim_out"];                 //invoice issuance 請求書発行
    $coax               = $_POST["form_coax"];                      //amount: round off decimalas 金額：丸め区分
    $tax_div            = $_POST["form_tax_div"];                   //consumption tax: taxation unit 消費税：課税単位
    $tax_franct         = $_POST["form_tax_franct"];                //consumption tax: round off digits 消費税：端数区分
    $note               = $_POST["form_note"];                      //equipment information・others 設備情報等・その他
    $email              = $_POST["form_email"];                     //Email
    $url                = $_POST["form_url"];                       //URL
    $represent_cell     = $_POST["form_represent_cell"];            //representative cellphone 代表者携帯
    $represe            = $_POST["form_rep_position"];              //representative position 代表者役職
    $bstruct            = $_POST["form_bstruct"];                   //business type 業態
    $inst               = $_POST["form_inst"];                      //facility 施設
    $establish_day      = $_POST["form_establish_day"]["y"];        //establishment date 創業日
    $establish_day     .= "-"; 
    $establish_day     .= $_POST["form_establish_day"]["m"];
    $establish_day     .= "-"; 
    $establish_day     .= $_POST["form_establish_day"]["d"];
    $record             = $_POST["form_record"];                    //trade history 取引履歴
    $important          = $_POST["form_important"];                 //important matter 重要事項
    $trans_account      = $_POST["form_trans_account"];             //bank account name for money transfer お振込先口座名
    $bank_fc            = $_POST["form_bank_fc"];                   //bank/branch name 銀行/支店名
    $account_num        = $_POST["form_account_num"];               //bank account number 口座番号
    $round_start        = $_POST["form_round_start"]["y"];          //patrol starting date 巡回開始日
    $round_start       .= "-"; 
    $round_start       .= $_POST["form_round_start"]["m"];
    $round_start       .= "-"; 
    $round_start       .= $_POST["form_round_start"]["d"];
    $deliver_radio      = $_POST["form_deliver_radio"];             //delivery note commnet (effect) 納品書コメント(効果
    $claim_send         = $_POST["form_claim_send"];                //send invoice (through mail service) 請求書送付(郵送)
    $company_name       = $_POST["form_company_name"];              //parent compay name 親会社名
    $company_tel        = $_POST["form_company_tel"];               //parent company TEL 親会社TEL
    $company_address    = $_POST["form_company_address"];           //parent company address 親会社住所
    $bank_div           = $_POST["form_bank_div"];                  //bank transcaction fee classification 銀行手数料負担区分
    $claim_note         = $_POST["form_claim_note"];                //invoice slip comment 請求書コメント
    $client_slip1       = $_POST["form_client_slip1"];              //cusotmer 1 print of slip 得意先１伝票印字
    $client_slip2       = $_POST["form_client_slip2"];              //customer 2 print of slip 得意先２伝票印字
    $parent_rep_name    = $_POST["form_parent_rep_name"];           //parent company representative 親会社代表者名
    $parent_establish_day  = $_POST["form_parent_establish_day"]["y"];
    $parent_establish_day .= "-";
    $parent_establish_day .= $_POST["form_parent_establish_day"]["m"];
    $parent_establish_day .= "-";
    $parent_establish_day .= $_POST["form_parent_establish_day"]["d"];
    $type               = $_POST["form_type"];                      //type/classification 種別
    //$claim_scope        = $_POST["form_claim_scope"];               //invoice range 請求範囲
    $compellation       = $_POST["form_prefix"];                    //honorific 敬称
    $c_tax_div          = $_POST["form_c_tax_div"];                 //taxation type/classification課税区分

    //error distinction flag エラー判別フラグ
    $err_flg = false;
    /****************************/
    //bank account fee checkbox distinction 口座料チェックボックス判別
    /****************************/
    if($price_check == 1){
        $check_which = 1;
    }else if($rate_check == 1){
        $check_which = 2;
    }else{
        $check_which = 0;
    }

    /***************************/
    //０埋め fill 0s
    /***************************/
    //customer code 1 得意先コード１
    $client_cd1 = str_pad($client_cd1, 6, 0, STR_POS_LEFT);

    //customer code 2 得意先コード２
    $client_cd2 = str_pad($client_cd2, 4, 0, STR_POS_LEFT);

    if(($client_cd1 != null && $client_data[0] != $client_cd1) || ( $client_cd2 != null && $client_data[1] != $client_cd2)){
        $client_cd_sql  = "SELECT";
        $client_cd_sql  .= " client_id FROM t_client";
        $client_cd_sql  .= " WHERE";
        $client_cd_sql  .= " client_cd1 = '$client_cd1'";
        $client_cd_sql  .= " AND";
        $client_cd_sql  .= " client_cd2 = '$client_cd2'";
        $client_cd_sql  .= " AND";
        $client_cd_sql .= "  (t_client.client_div = '1'";
        $client_cd_sql .= "  OR";
        $client_cd_sql .= "  t_client.client_div = '3')";
        $client_cd_sql  .= ";";
        $select_client = Db_Query($conn, $client_cd_sql);
        $select_client = pg_num_rows($select_client);
        if($select_client != 0){
            $client_cd_err = "入力された得意先コードは使用中です。";
            $err_flg = true;
        }
    }

    //■TEL
    //●error if other than half-width numbers and hyphen was inserted 半角数字と「-」以外はエラー
/*
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+-?[0-9]+-?[0-9]+$",$tel)){
        $tel_err = "TELは半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }
*/

    //■FAX
    //●error if other than half-width numbers and hyphen was inserted 半角数字と「-」以外はエラー
    $form->addRule("form_fax","FAXは半角数字と｢-｣のみ30桁以内です。","telfax");
/*
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+-?[0-9]+-?[0-9]+$",$fax) && $fax != null){
        $fax_err = "FAXは半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }
*/

    //■Email
    if (!(ereg("^[^@]+@[^.]+\..+", $email)) && $email != "") {
        $email_err = "Emailが妥当ではありません。";
        $err_flg = true;
    }

    //■URL
    //●input check 入力チェック
    if (!preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $url) && $url != null) {
        $url_err = "正しいURLを入力して下さい。";
        $err_flg = true;
    }

    //■representative cellphone 代表者携帯
    //●error if other than half-width numbers and hyphen was inserted 半角数字と「-」以外はエラー
/*
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$represent_cell) && $represent_cell != ""){
        $rep_cell_err = "代表者携帯は半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }
*/

    //■parent company TEL 親会社TEL
    //●error if other than half-width numbers and hyphen was inserted 半角数字と「-」以外はエラー
    $form->addRule("form_company_tel","親会社TELは半角数字と｢-｣のみ30桁以内です。","telfax");
/*
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+-?[0-9]+-?[0-9]+$",$company_tel) && $company_tel != ""){
        $company_tel_err = "親会社TELは半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }
*/

    //■closing date 締日
    //●required check field 必須チェック
    if($_POST["form_close"] == 0){
        $close_err = "締日を選択して下さい。";
        $err_flg = true;
    }

    //when current month is collection month and the closing date is larger than the payment date. 集金日（月）が当月で締日が支払日（日）より大きい場合
    if($_POST["form_pay_m"] == "0" && ($_POST["form_close"] >= $_POST["form_pay_d"])){
        $close_err = "支払日（月）で当月を選択した場合は締日より小さい支払日（日）を選択して下さい。";
        $err_flg = true;
    }
    
    //■invoice destination 請求先
    //●input check 入力チェック
    //when invoice destination was set to own company 請求先に自社を設定した場合
    if($_POST["form_client"]["cd1"] == $_POST["form_claim"]["cd1"] && $_POST["form_client"]["cd2"] == $_POST["form_claim"]["cd2"]){
        $claim_flg = true;
    //when an invalid input was inputted in the invoice destination 請求先に不正な値を設定した場合
    }elseif(
        ($_POST["form_claim"]["cd1"] != null || $_POST["form_claim"]["cd2"] != null || $_POST["form_claim"]["name"] != null)
        &&      
        ($_POST["form_claim"]["cd1"] == null || $_POST["form_claim"]["cd2"] == null || $_POST["form_claim"]["name"] == null)
    ){
        $claim_err = "正しい請求先コードを入力して下さい。";
        $err_flg = true;
    //when the invoice destination was selected to other company 請求先に他社を設定した場合
    }elseif($claim_cd1 != null && $claim_cd2 != null && $claim_name){
        $sql  = "SELECT";
        $sql .= "   close_day,";
        $sql .= "   coax,";
        $sql .= "   tax_div,";
        $sql .= "   tax_franct,";
        $sql .= "   c_tax_div";
        $sql .= " FROM";
        $sql .= "   t_client";
        $sql .= " WHERE";
        $sql .= "   client_cd1 = '$claim_cd1'";
        $sql .= "   AND";
        $sql .= "   client_cd2 = '$claim_cd2'";
        $sql .= "   AND";
        $sql .= "   client_div = '1'";
        $sql .= "   AND";
        $sql .= "   shop_id = $shop_id";
        $sql .= ";"; 

        $result = Db_Query($conn ,$sql);
        $claim_data = pg_fetch_array($result,0);

        $claim_close_day  = $claim_data["close_day"];
        $claim_coax       = $claim_data["coax"];
        $claim_tax_div    = $claim_data["tax_div"];
        $claim_tax_franct = $claim_data["tax_franct"];        
        $claim_c_tax_div  = $claim_data["tax_div"];

        if($close_day_cd != $claim_close_day){
			//decide if it is end of the month 月末判定
			if($claim_close_day == "29"){
				$claim_err = "締日は請求先と同じ 月末 を選択して下さい。";
			}else{
				$claim_err = "締日は請求先と同じ ".$claim_close_day."日 を選択して下さい。";
			}
            $err_flg = true;
        }else{
            $claim_flg = true;
        }

        // start process if it is not the same with decimal round off classification of invoice destination請求先の丸め区分と同じではない場合処理開始
        if($coax != $claim_coax){
            //convert the round off classification for error message エラーメッセージに表示するため丸め区分を置換
            if($claim_coax == '1'){
                $claim_coax = "切捨";
            }elseif($claim_coax == '2'){
                $claim_coax = "四捨五入";
            }elseif($claim_coax == '3'){
                $claim_coax = "切上";
            }

            $claim_coax_err = "まるめ区分は請求先と同じ ".$claim_coax." を選択して下さい。";
            $err_flg = true;
        }

        //start process if it is not same with the taxation unit of the invoice destination's 請求先の課税単位と同じではない場合処理開始
        if($tax_div != $claim_tax_div){
            //replace the tax unit for display in error message エラーメッセージに表示するため課税単位を置換
            if($claim_tax_div == '2'){
                $claim_tax_div = "伝票単位";
            }elseif($claim_tax_div == '1'){
                $claim_tax_div = "締日単位";
            }

            $claim_tax_div_err = "課税単位は請求先と同じ ".$claim_tax_div." を選択して下さい。";
            $err_flg = true;
        }

        //start process if it is not the same wth the last digits of the invoice destination's 請求先の端数と同じでない場合処理開始
        if($tax_franct != $claim_tax_franct){
            //replace the last digit for display in error message エラーメッセージに表示するため端数を置換
            if($claim_tax_franct == '1'){
                $claim_tax_franct = "切捨";
            }elseif($claim_tax_franct == '2'){
                $claim_tax_franct = "四捨五入";
            }elseif($claim_tax_franct == '3'){
                $claim_tax_franct = "切上";
            }

            $claim_tax_franct_err = "端数は請求先と同じ ".$claim_tax_franct." を選択して下さい。";
            $err_flg = true;
        }

        //start process if the tax classification is not the same with invoice destination's 請求先の課税区分と同じではない場合処理開始
        if($c_tax_div != $claim_c_tax_div){

            //replace the taxation unit for display in error message エラーメッセージに表示するため課税区分を置換
            if($claim_c_tax_div == '1'){
                $claim_c_tax_div = "外税"; 
            }elseif($claim_c_tax_div == '2'){
                $claim_c_tax_div = "内税"; 
            }       

            $claim_c_tax_div_err = "課税区分は請求先と同じ ".$claim_c_tax_div." を選択して下さい。";
            $err_flg = true; 

        }       
    }

    //■introduced bank account 紹介口座先
    //●input checking 入力チェック
    if($_POST["form_intro_act"]["cd"] != null && $_POST["form_intro_act"]["name"] == null){
        $intro_act_err = "正しい紹介口座先コードを入力して下さい。";
        $err_flg = true;
    }
    
    //■contract date・contract update date 契約年月日・契約更新日
    //●check the validity of the date 日付の妥当性チェック
    $csday_y = (int)$_POST["form_cont_s_day"]["y"];
    $csday_m = (int)$_POST["form_cont_s_day"]["m"];
    $csday_d = (int)$_POST["form_cont_s_day"]["d"];
    $crday_y = (int)$_POST["form_cont_r_day"]["y"];
    $crday_m = (int)$_POST["form_cont_r_day"]["m"];
    $crday_d = (int)$_POST["form_cont_r_day"]["d"];
    $rsday_y = (int)$_POST["form_round_start"]["y"];
    $rsday_m = (int)$_POST["form_round_start"]["m"];
    $rsday_d = (int)$_POST["form_round_start"]["d"];
    $esday_y = (int)$_POST["form_establish_day"]["y"];
    $esday_m = (int)$_POST["form_establish_day"]["m"];
    $esday_d = (int)$_POST["form_establish_day"]["d"];
    $parent_esday_y = $_POST["form_parent_establish_day"]["y"];
    $parent_esday_m = $_POST["form_parent_establish_day"]["m"];
    $parent_esday_d = $_POST["form_parent_establish_day"]["d"];
	$ceday_y = $_POST["form_cont_e_day"]["y"];
	$ceday_m = $_POST["form_cont_e_day"]["m"];
	$ceday_d = $_POST["form_cont_e_day"]["d"];

    if($csday_m != null || $csday_d != null || $csday_y != null){
        $csday_flg = true;
    }
    $check_s_day = checkdate($csday_m,$csday_d,$csday_y);
    if($check_s_day == false && $csday_flg == true){
        $csday_err = "契約年月日の日付は妥当ではありません。";
        $err_flg = true;
    }
    if($crday_m != null || $crday_d != null || $crday_y != null){
        $crday_flg = true;
    }
    $check_r_day = checkdate($crday_m,$crday_d,$crday_y);
    if($check_r_day == false && $crday_flg == true){
        $crday_err = "契約更新日の日付は妥当ではありません。";
        $err_flg = true;
    }
    if($rsday_m != null || $rsday_d != null || $rsday_y != null){
        $rsday_flg = true;
    }
    $check_r_day = checkdate($rsday_m,$rsday_d,$rsday_y);
    if($check_r_day == false && $rsday_flg == true){
        $rsday_err = "巡回開始日の日付は妥当ではありません。";
        $err_flg = true;
    }
    if($esday_m != null || $esday_d != null || $esday_y != null){
        $esday_flg = true;
    }
    $check_r_day = checkdate($esday_m,$esday_d,$esday_y);
    if($check_r_day == false && $esday_flg == true){
        $esday_err = "創業日の日付は妥当ではありません。";
        $err_flg = true;
    }

    //check validity of the parent company establishment date 親会社創業日日付妥当性チェック
	//◇contract date 契約日
    //・check string type 文字種チェック
    if($parent_esday_y != null || $parent_esday_m != null || $parent_esday_d != null){
        $parent_esday_y = (int)$parent_esday_y;
        $parent_esday_m = (int)$parent_esday_m;
        $parent_esday_d = (int)$parent_esday_d;
        if(!checkdate($parent_esday_m,$parent_esday_d,$parent_esday_y)){
			$parent_esday_err = "親会社創業日の日付は妥当ではありません。";
        	$err_flg = true;
        }
    }

	//check validity of the contract ending date 契約終了日日付妥当性チェック
	//◇contract ending date 契約終了日
    //・string type check 文字種チェック
    if($ceday_y != null || $ceday_m != null || $ceday_d != null){
        $ceday_y = (int)$ceday_y;
        $ceday_m = (int)$ceday_m;
        $ceday_d = (int)$ceday_d;
        if(!checkdate($ceday_m,$ceday_d,$ceday_y)){
			$cont_e_day_err = "契約終了日の日付は妥当ではありません。";
        	$err_flg = true;
        }
    }

    //●check if the contract update date is not a previous date than the contract date 契約更新日が契約年月日よりも前でないかチェック
    if($cont_s_day >= $cont_r_day && $cont_s_day != '--' && $cont_r_day != '--'){
        $csday_rday_err = "契約更新日の日付は妥当ではありません。";
        $err_flg = true;
    }
}
    //if it's and error, then do not register or edit エラーの際には、登録・変更処理を行わない
if($_POST["button"]["entry_button"] == "登　録" && $form->validate() && $err_flg != true ){
    /******************************/
    //Database registration information DB登録情報
    /******************************/
    $create_day = date("Y-m-d");
    /******************************/
    //registration information 登録処理
    /******************************/
    if($new_flg == true){
        Db_Query($conn, "BEGIN;");
        $insert_sql  = " INSERT INTO t_client (";
        $insert_sql .= "    client_id,";                        //cusomterID得意先ID
        $insert_sql .= "    client_cd1,";                       //customer code 得意先コード
        $insert_sql .= "    client_cd2,";                       //branch code 支店コード
        $insert_sql .= "    shop_id,";                          //FC group ID FCグループID
        $insert_sql .= "    create_day,";                       //creation date作成日
        $insert_sql .= "    state,";                            //condition 状態
        $insert_sql .= "    client_name,";                      //customer name 得意先名
        $insert_sql .= "    client_read,";                      //customer name (phonetic in katakana) 得意先名（フリガナ）
        $insert_sql .= "    client_name2,";                     //customer name 2 得意先名2
        $insert_sql .= "    client_read2,";                     //customer name (phonetic in katakana) 得意先名2（フリガナ）
        $insert_sql .= "    client_cname,";                     //abbreviation略称
        $insert_sql .= "    post_no1,";                         //postal code 1 郵便番号１
        $insert_sql .= "    post_no2,";                         //psotal code 2 郵便番号２
        $insert_sql .= "    address1,";                         //address 1 住所１
        $insert_sql .= "    address2,";                         //address 2 住所２
        $insert_sql .= "    address3,";                         //address 3 住所3
        $insert_sql .= "    address_read,";                     //address (phonetic in katakana) 住所（フリガナ）
        $insert_sql .= "    area_id,";                          //district ID 地区ID
        $insert_sql .= "    tel,";                              //tel
        $insert_sql .= "    fax,";                              //fax
        $insert_sql .= "    rep_name,";                         //representative name 代表者氏名
        $insert_sql .= "    c_staff_id1,";                      //cotract assignee 1 契約担当１
        $insert_sql .= "    c_staff_id2,";                      //contract assignee 2 契約担当２
//        $insert_sql .= "    d_staff_id1,";                      //patrol assignee 1巡回担当１
//        $insert_sql .= "    d_staff_id2,";                      //patrol assignee 2 巡回担当２
//        $insert_sql .= "    d_staff_id3,";                      //patrol assignee 3巡回担当３

/*20060420追加*/
        $insert_sql .= "    charger1,";                         //assigned staff 1 ご担当者１
        $insert_sql .= "    charger2,";                         //assigned staff 2 ご担当者２
        $insert_sql .= "    charger3,";                         //assigned staff 3 ご担当者３
        $insert_sql .= "    charger_part1,";                    //assigned staff department 1 ご担当者部署１
        $insert_sql .= "    charger_part2,";                    //assigned staff department 2 ご担当者部署２
        $insert_sql .= "    charger_part3,";                    //assigned staff department 3 ご担当者部署３
        $insert_sql .= "    charger_represe1,";                 //assigned staff position 1 ご担当者役職１
        $insert_sql .= "    charger_represe2,";                 //assigned staff position 2 ご担当者役職2
        $insert_sql .= "    charger_represe3,";                 //assigned staff position 3 ご担当者役職3
        $insert_sql .= "    charger_note,";                     //assigned staff remarks  ご担当者備考
/***************/
        $insert_sql .= "    trade_stime1,";                     //operating hour (starting time morning) 営業時間（午前開始）
        $insert_sql .= "    trade_etime1,";                     //operating hour (ending time morning) 営業時間（午前終了）
        $insert_sql .= "    trade_stime2,";                     //operating hour (afternoon start time)営業時間（午後開始）
        $insert_sql .= "    trade_etime2,";                     //operating hour (ending time afternoon) 営業時間（午後終了）
        $insert_sql .= "    sbtype_id,";                        //industry ID 業種ID
        $insert_sql .= "    holiday,";                          //holiday 休日
        $insert_sql .= "    close_day,";                        //cllosing date締日
        $insert_sql .= "    trade_id,";                          //trade classifcation 取引区分
        $insert_sql .= "    pay_m,";                            //collection date (month) 集金日（月）
        $insert_sql .= "    pay_d,";                            //collection date (day) 集金日（日）
        $insert_sql .= "    pay_way,";                          //collection method 集金方法
        $insert_sql .= "    account_name,";                     //bank account name 口座名義
        $insert_sql .= "    pay_name,";                         //name which the money transfer will happen 振込名義
        $insert_sql .= "    b_bank_id,";                        //bankID 銀行ID
        $insert_sql .= "    slip_out,";                         //output slip 伝票出力
        $insert_sql .= "    deliver_note,";                     //delivery note comment 納品書コメント
        $insert_sql .= "    claim_out,";                        //invoice output 請求書出力
        $insert_sql .= "    coax,";                             //amount: round off decimal classification 金額：丸め区分
        $insert_sql .= "    tax_div,";                          //tax: taxation unit 消費税：課税単位
        $insert_sql .= "    tax_franct,";                       //consumption tax: last digit classification消費税：端数区分
        $insert_sql .= "    cont_sday,";                        //contract starting date契約開始日
        $insert_sql .= "    cont_eday,";                        //contract ending date 契約終了日
        $insert_sql .= "    cont_peri,";                        //contract period 契約期間
        $insert_sql .= "    cont_rday,";                        //contract updating date 契約更新日
        $insert_sql .= "    col_terms,";                        //collection condition 回収条件
        $insert_sql .= "    credit_limit,";                     //credit limit 与信限度
        $insert_sql .= "    capital,";                          //capital 資本金
        $insert_sql .= "    note,";                             //equipment info and ohter info 設備情報等/その他
        $insert_sql .= "    client_div,";                       //customer classifcation 得意先区分
        //bank account fee (membership fee?)・bank account rate distinction 口座料・口座率判別
        if($price_check == 1){
            $insert_sql .= "    intro_ac_price,";               //bank account fee 口座料
        }else if($rate_check == 1){
            $insert_sql .= "    intro_ac_rate,";                //bank account rate口座率
        }
        $insert_sql .= "    email,";                            //Email
        $insert_sql .= "    url,";                              //URL
        $insert_sql .= "    rep_htel,";                         //representative cellphone 代表者携帯
        $insert_sql .= "    b_struct,";                         //business type 業態
        $insert_sql .= "    inst_id,";                          //facility 施設
        $insert_sql .= "    establish_day,";                    //establishment date 創業日
        $insert_sql .= "    deal_history,";                     //trade history 取引履歴
        $insert_sql .= "    importance,";                       //important matter 重要事項
        $insert_sql .= "    intro_ac_name,";                    //bank account name for money transfer お振込先口座名
        $insert_sql .= "    intro_bank,";                       //bank/branch name 銀行/支店名
        $insert_sql .= "    intro_ac_num,";                     //bank account number 口座番号
        $insert_sql .= "    round_day,";                        //patrol starting date 巡回開始日
        $insert_sql .= "    deliver_effect,";                   //delivery note comment (effect) 納品書コメント(効果)
        $insert_sql .= "    claim_send,";                       //send invoice (mail service) 請求書送付(郵送)
        $insert_sql .= "    client_cread,";                     //abbreviation (phonetic in katakana) 略称(フリガナ)
        $insert_sql .= "    represe,";                          //representative position 代表者役職
        $insert_sql .= "    shop_name,";
        $insert_sql .= "    shop_div,";
        $insert_sql .= "    royalty_rate,";
        $insert_sql .= "    tax_rate_n,";
        $insert_sql .= "    company_name,";                     //parent company name 親会社名
        $insert_sql .= "    company_tel,";                      //parent company TEL 親会社TEL
        $insert_sql .= "    company_address,";                  //parent company address 親会社住所
        $insert_sql .= "    bank_div,";                         //bank transaction fee shoulder classification銀行手数料負担区分
        $insert_sql .= "    claim_note,";                       //invoice slip comment 請求書コメント
        $insert_sql .= "    client_slip1,";
        $insert_sql .= "    client_slip2,";
        $insert_sql .= "    parent_establish_day,";
        $insert_sql .= "    parent_rep_name,";
        $insert_sql .= "    type,";                             //type/classification 種別
        $insert_sql .= "    compellation,";                     //honorific 敬称
        //$insert_sql .= "    claim_scope";                       //invoice range 請求範囲
		$insert_sql .= "    intro_ac_div, ";                     // bank account classification 口座区分
        $insert_sql .= "    c_tax_div";                         //tax classifcation 課税区分
        $insert_sql .= " )VALUES(";
        $insert_sql .= "    (SELECT COALESCE(MAX(client_id), 0)+1 FROM t_client),";
        $insert_sql .= "    '$client_cd1',";                    //customer code 得意先コード
        $insert_sql .= "    '$client_cd2',";                    //branch code 支店コード
        $insert_sql .= "    $shop_id,";                         //FC group ID FCグループID
        $insert_sql .= "    NOW(),";                            //creation date 作成日
        $insert_sql .= "    '$state',";                         //condition 状態
        $insert_sql .= "    '$client_name',";                   //customer name 得意先名
        $insert_sql .= "    '$client_read',";                   //customer (phonetic in katakana) 得意先（フリガナ）
        $insert_sql .= "    '$client_name2',";                  //customer name 2 得意先名2
        $insert_sql .= "    '$client_read2',";                  //customer 2 (phonetic in katakana) 得意先2（フリガナ）
        $insert_sql .= "    '$client_cname',";                  //abbreviation 略称
        $insert_sql .= "    '$post_no1',";                      //postal code 1 郵便番号１
        $insert_sql .= "    '$post_no2',";                      //postal code 2 郵便番号２
        $insert_sql .= "    '$address1',";                      //address1住所１
        $insert_sql .= "    '$address2',";                      //address2住所２
        $insert_sql .= "    '$address3',";                      //address3住所3
        $insert_sql .= "    '$address_read',";                  //address (phonetic in katakana) 住所（フリガナ）
        $insert_sql .= "    $area_id,";                         //district ID 地区ID
        $insert_sql .= "    '$tel',";                           //TEL
        $insert_sql .= "    '$fax',";                           //FAX
        $insert_sql .= "    '$rep_name',";                      //representative name  代表者氏名
        if($c_staff_id1 == ""){
                $c_staff_id1 = "    null"; 
        }
        if($c_staff_id2 == ""){
                $c_staff_id2 = "    null"; 
        }
//        if($d_staff_id1 == ""){
//                $d_staff_id1 = "    null";
//        }
//        if($d_staff_id2 == ""){
//                $d_staff_id2 = "    null";
//        }
//        if($d_staff_id3 == ""){
//                $d_staff_id3 = "    null";
//        }
        $insert_sql .= "    $c_staff_id1,";                    //contract assignee 1契約担当１
        $insert_sql .= "    $c_staff_id2,";                    //contract assignee 2 契約担当２
//        $insert_sql .= "    $d_staff_id1,";                    //patrol assignee 1巡回担当１
//        $insert_sql .= "    $d_staff_id2,";                    //patrol assignee 2巡回担当２
//        $insert_sql .= "    $d_staff_id3,";                    //patrol assignee 3巡回担当３
 
/*20060420追加(add)*/
        $insert_sql .= "    '$charger1',";                     //assigned staff 1 ご担当者１
        $insert_sql .= "    '$charger2',";                     //assigned staff 2ご担当者２
        $insert_sql .= "    '$charger3',";                     //assigned staff 3 ご担当者３
        $insert_sql .= "    '$charger_part1',";                //assigned staff department 1ご担当者部署１
        $insert_sql .= "    '$charger_part2',";                //assigned staff department 2ご担当者部署２
        $insert_sql .= "    '$charger_part3',";                //assigned staff department 3ご担当者部署３
        $insert_sql .= "    '$charger_represe1',";             //assigned staff position 1ご担当者役職１
        $insert_sql .= "    '$charger_represe2',";             //assigned staff position 2ご担当者役職２
        $insert_sql .= "    '$charger_represe3',";             //assigned staff position 3ご担当者役職３
        $insert_sql .= "    '$charger_note',";                 //assign staff remarks ご担当者備考
/***************/

        if($trade_stime1 == ":"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$trade_stime1',";             //oprating hour (morning start time) 営業時間（午前開始）
        }
        if($trade_etime1 == ":"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$trade_etime1',";             //oprating hour (morning end time) 営業時間（午前終了）
        }
        if($trade_stime2 == ":"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$trade_stime2',";             //oprating hour (afternoon start time) 営業時間（午後開始）
        }
        if($trade_etime2 == ":"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$trade_etime2',";             //oprating hour (afternoon end time) 営業時間（午後終了）
        }
        if($btype == ""){
                $btype = "    null"; 
        }
        $insert_sql .= "    $btype,";                          //industryID 業種ID
        $insert_sql .= "    '$holiday',";                      //holiday 休日
        $insert_sql .= "    '$close_day_cd',";                 //closing day 締日
        $insert_sql .= "    '$trade_id',";                     //contract classifcation取引区分
        if($pay_m == ""){
                $pay_m = ""; 
        }
        $insert_sql .= "    '$pay_m',";                        //collection date (month) 集金日（月）
        if($pay_d == ""){
                $pay_d = ""; 
        }
        $insert_sql .= "    '$pay_d',";                        //collection date (day) 集金日（日）
        $insert_sql .= "    '$pay_way',";                      //payment method 支払い方法 
        $insert_sql .= "    '$account_name',";                 //bank account name 口座名義
        $insert_sql .= "    '$pay_name',";                     //payment name 振込名義
        if($bank_enter_cd == ""){
                $insert_sql .= "    null,"; 
        }else{
	        $insert_sql .= "    $bank_enter_cd,";               //bank 銀行
        }
        $insert_sql .= "    '$slip_out',";                     //slip output 伝票出力
        $insert_sql .= "    '$deliver_note',";                 //delivery note comment 納品書コメント
        $insert_sql .= "    '$claim_out',";                    //invoice output 請求書出力
        $insert_sql .= "    '$coax',";                         //amount: round off decimal classification 金額：丸め区分
        $insert_sql .= "    '$tax_div',";                      //consumption tax: taxation unit 消費税：課税単位
        $insert_sql .= "    '$tax_franct',";                   //consumption tax: fraction unit 消費税：端数単位
        if($cont_s_day == "--"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$cont_s_day',";               //contract starting date 契約開始日
        }
        if($cont_e_day == "--"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$cont_e_day',";               //contract ending date 契約終了日
        }
        if($cont_peri == ""){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$cont_peri',";                //contract period 契約期間
        }
        if($cont_r_day == "--"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$cont_r_day',";               //contract update date 契約更新日
        }
        $insert_sql .= "    '$col_terms',";                    //collection condition 回収条件
        $insert_sql .= "    '$cledit_limit',";                 //credit limit 与信限度
        $insert_sql .= "    '$capital',";                      //capital 資本金
        $insert_sql .= "    '$note',";                         //equipment information/other設備情報等/その他
        $insert_sql .= "    '1',";                             //customer classification 得意先区分
        //bank account fee (membership fee) ・bank account rate distinction 口座料・口座率判別
        if($price_check == 1 && $account_price != NULL){
            $insert_sql .= "    $account_price,";              //bank account fee 口座料
        }else if($rate_check == 1 && $account_rate != NULL){
            $insert_sql .= "    $account_rate,";               //bank account fee (rate) 口座料(率)
        }
        $insert_sql .= "    '$email',";                        //Email
        $insert_sql .= "    '$url',";                          //URL
        $insert_sql .= "    '$represent_cell',";               //representative cellphone 代表者携帯
        if($bstruct == ""){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$bstruct',";                  //business type 業態
        }
        if($inst == ""){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    $inst,";                     //facility 施設
        }
        if($establish_day == "--"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$establish_day',";            //establishment date 創業日
        }
        $insert_sql .= "    '$record',";                       //trade history 取引履歴
        $insert_sql .= "    '$important',";                    //important matter 重要事項
        $insert_sql .= "    '$trans_account',";                //bank account name for money transfer お振込先口座名
        $insert_sql .= "    '$bank_fc',";                      //bank/branch name 銀行/支店名
        $insert_sql .= "    '$account_num',";                  //bank account number 口座番号
        if($round_start == "--"){
            $insert_sql .= "    null,";
        }else{
            $insert_sql .= "    '$round_start',";              //patrol starting date 巡回開始日
        }
        $insert_sql .= "    '$deliver_radio',";                //delivery note comment (effect) 納品書コメント(効果
        $insert_sql .= "    '$claim_send',";                    //send invoice (mail service) 請求書送付(郵送)
        $insert_sql .= "    '$cname_read',";                   //abbreviation (phonetic in katakana) 略称(フリガナ)
        $insert_sql .= "    '$represe',";                        //representative position 代表者役職
        $insert_sql .= "    '',";
        $insert_sql .= "    '',";
        $insert_sql .= "    '',";
        $insert_sql .= "    (SELECT";                          //consumption tax rate (currently) 消費税率(現在)
        $insert_sql .= "        tax_rate_n";
        $insert_sql .= "    FROM";
        $insert_sql .= "        t_client";
        $insert_sql .= "    WHERE";
        $insert_sql .= "    client_div = '0'";
        $insert_sql .= "    ) ,";
        $insert_sql .= "    '$company_name',";              //parent company name 親会社名
        $insert_sql .= "    '$company_tel',";               //parent company TEL 親会社TEL
        $insert_sql .= "    '$company_address',";           //parent company address 親会社住所
        $insert_sql .= "    '$bank_div',";
        $insert_sql .= "    '$claim_note',";
                $insert_sql .= "    '$client_slip1',";
        $insert_sql .= "    '$client_slip2',";
        if($parent_establish_day != '--'){
            $insert_sql .= "    '$parent_establish_day',";      //parent company establishment date 親会社創業日
        }else{
            $insert_sql .= "    null,";
        }
        $insert_sql .= "    '$parent_rep_name',";            //parent company representative name 親会社代表者名
        $insert_sql .= "    '$type',";
        $insert_sql .= "    '$compellation',";            //parent company representative name 親会社代表者名
        //$insert_sql .= "    '$claim_scope'";
		//bank account classification distinction 口座区分判定
		if($price_check == 1 && $account_price != NULL){
			//fixed amount 固定金額
			$insert_sql .= "    '1',";                                   
		}else if($rate_check == 1 && $account_rate != NULL){
			//select % ％指定
			$insert_sql .= "    '2',";                                   
		}else{
			//なし none
			$insert_sql .= "    '',";                                   
		}
        $insert_sql .= "    $c_tax_div";
        $insert_sql .= ");";
        $result = Db_Query($conn, $insert_sql);
        if($result === false){
            Db_Query($conn, "ROLLBACK;");
            exit;
        }

        //■請求先 invoice destination
        //●入力時 when inputting
        if($claim_flg == true){
            $insert_sql  = " INSERT INTO t_client_info (";
            $insert_sql .= "    client_id,";
            $insert_sql .= "    intro_account_id,";
            $insert_sql .= "    claim_id,";
	        $insert_sql .= "    cclient_shop";
            $insert_sql .= " )VALUES(";
            $insert_sql .= "    (SELECT";
            $insert_sql .= "        client_id";
            $insert_sql .= "    FROM";
            $insert_sql .= "        t_client";
            $insert_sql .= "    WHERE";
            $insert_sql .= "        client_cd1 = '$client_cd1'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd2 = '$client_cd2'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_div = '1'";
            $insert_sql .= "    ),";
            $insert_sql .= "    (SELECT";
            $insert_sql .= "        client_id";
            $insert_sql .= "    FROM";
            $insert_sql .= "        t_client";
            $insert_sql .= "    WHERE";
            $insert_sql .= "        shop_id = $shop_id";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd1 = '$intro_act_cd'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_div = '2'";
            $insert_sql .= "    ),";
            $insert_sql .= "    (SELECT";
            $insert_sql .= "        client_id";
            $insert_sql .= "    FROM";
            $insert_sql .= "        t_client";
            $insert_sql .= "    WHERE";
            $insert_sql .= "        client_cd1 = '$claim_cd1'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd2 = '$claim_cd2'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_div = '1'";
            $insert_sql .= "    ),";
	        $insert_sql .= "    $cshop_id";
            $insert_sql .= ");";
            $result = Db_Query($conn, $insert_sql);
        }else{
            $insert_sql = " INSERT INTO t_client_info (";
            $insert_sql .= "    client_id,";
            $insert_sql .= "    intro_account_id,";
            $insert_sql .= "    claim_id,";
	        $insert_sql .= "    cclient_shop";
            $insert_sql .= " )VALUES(";
            $insert_sql .= "    (SELECT";
            $insert_sql .= "        client_id";
            $insert_sql .= "    FROM";
            $insert_sql .= "        t_client";
            $insert_sql .= "    WHERE";
            $insert_sql .= "        shop_id = $shop_id";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd1 = '$client_cd1'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd2 = '$client_cd2'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_div = '1'";
            $insert_sql .= "    ),";
            $insert_sql .= "    (SELECT";
            $insert_sql .= "        client_id";
            $insert_sql .= "    FROM";
            $insert_sql .= "        t_client";
            $insert_sql .= "    WHERE";
            $insert_sql .= "        shop_id = $shop_id";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd1 = '$intro_act_cd'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_div = '2'";
            $insert_sql .= "    ),";
            $insert_sql .= "    (SELECT";
            $insert_sql .= "        client_id";
            $insert_sql .= "    FROM";
            $insert_sql .= "        t_client";
            $insert_sql .= "    WHERE";
            $insert_sql .= "        shop_id = $shop_id";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd1 = '$client_cd1'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_cd2 = '$client_cd2'";
            $insert_sql .= "        AND";
            $insert_sql .= "        client_div = '1'";
            $insert_sql .= "    ),";
	        $insert_sql .= "    $cshop_id";
            $insert_sql .= ");";
            $result = Db_Query($conn, $insert_sql);
        }
        if($result === false){
            Db_Query($conn, "ROLLBACK;");
            exit;
        }

        //登録した情報をログに残す leave the registration information in log 
        $result = Log_Save( $conn, "client", "1", $client_cd1."-".$client_cd2, $client_name);
        if($result === false){
            Db_Query($conn, "ROLLBACK");
            exit;
        }
        
    /******************************/
    //更新処理 update process
    /******************************/
    }else if($new_flg == false){
        // acquire invoice ID before registering customer 得意先登録前に請求先IDを取得
        // when the invoice destination is inputted 請求先が入力された場合
        if($claim_name != null){
            $sql  = "SELECT";
            $sql .= "   client_id";
            $sql .= " FROM";
            $sql .= "   t_client";
            $sql .= " WHERE";
            $sql .= "   client_div = '1'";
            $sql .= "   AND";
            $sql .= "   client_cd1 = '$claim_cd1'";
            $sql .= "   AND";
            $sql .= "   client_cd2 = '$claim_cd2'";
            $sql .= "   AND";
            // $sql .= "   shop_gid = $shop_gid";
            $sql .= "   shop_id = $shop_id";
            $sql .= ";";

            $result = Db_Query($conn, $sql);
            $claim_id = pg_fetch_result($result, 0,0 );
        }else{
            $claim_id = $get_client_id;
        }

        //得意先マスタ customer master 
        Db_Query($conn, "BEGIN;");
        $update_sql = "UPDATE";
        $update_sql .= "    t_client";
        $update_sql .= " SET";
        $update_sql .= "    client_cd1 = '$client_cd1',";
        $update_sql .= "    client_cd2 = '$client_cd2',";
        $update_sql .= "    state = '$state',";
        $update_sql .= "    client_name = '$client_name',";
        $update_sql .= "    client_read = '$client_read',";
        $update_sql .= "    client_name2 = '$client_name2',";
        $update_sql .= "    client_read2 = '$client_read2',";
        $update_sql .= "    client_cname = '$client_cname',";
        $update_sql .= "    post_no1 = '$post_no1',";
        $update_sql .= "    post_no2 = '$post_no2',";
        $update_sql .= "    address1 = '$address1',";
        $update_sql .= "    address2 = '$address2',";
        $update_sql .= "    address3 = '$address3',";
        $update_sql .= "    address_read = '$address_read',";
        $update_sql .= "    area_id = $area_id,";
        $update_sql .= "    tel = '$tel',";
        $update_sql .= "    fax = '$fax',";
        $update_sql .= "    rep_name = '$rep_name',";
        if($c_staff_id1 == ""){
            $c_staff_id1 = null;
        }
        if($c_staff_id2 == ""){
            $c_staff_id2 = null;
        }
//        if($d_staff_id1 == ""){
//            $d_staff_id1 = null;
//        }
//        if($d_staff_id2 == ""){
//            $d_staff_id2 = null;
//        }
//        if($d_staff_id3 == ""){
//            $d_staff_id3 = null;
//        }
/*20060420追加(add)*/
        $update_sql .= "    charger1 = '$charger1',";
        $update_sql .= "    charger2 = '$charger2',";
        $update_sql .= "    charger3 = '$charger3',";
        $update_sql .= "    charger_part1 = '$charger_par1',";
        $update_sql .= "    charger_part2 = '$charger_par2',";
        $update_sql .= "    charger_part3 = '$charger_par3',";
        $update_sql .= "    charger_represe1 = '$charger_represe1',";
        $update_sql .= "    charger_represe2 = '$charger_represe2',";
        $update_sql .= "    charger_represe3 = '$charger_represe3',";
        $update_sql .= "    charger_note = '$charger_note',";
/**************/
 
        if($trade_stime1 == ":"){
            $update_sql .= "        trade_stime1 = null,";
        }else{
            $update_sql .= "        trade_stime1 = '$trade_stime1',";
        }
        if($trade_etime1 == ":"){
            $update_sql .= "        trade_etime1 = null,";
        }else{
            $update_sql .= "        trade_etime1 = '$trade_etime1',";
        }
        if($trade_stime2 == ":"){
            $update_sql .= "        trade_stime2 = null,";
        }else{
            $update_sql .= "        trade_stime2 = '$trade_stime2',";
        }
        if($trade_etime2 == ":"){
            $update_sql .= "        trade_etime2 = null,";
        }else{
            $update_sql .= "        trade_etime2 = '$trade_etime2',";
        }
        $update_sql .= "    holiday = '$holiday',";
        if($btype == ""){
            $update_sql .= "    sbtype_id = null,";
        }else{
            $update_sql .= "    sbtype_id = $btype,";
        }
        if($price_check == 1 && $account_price != NULL){
            $update_sql .= "        intro_ac_price = $account_price,";
            $update_sql .= "        intro_ac_rate = null,";
        }else if($rate_check == 1 && $account_rate != NULL){
            $update_sql .= "        intro_ac_rate = $account_rate,";
            $update_sql .= "        intro_ac_price = null,";
        }else{
            $update_sql .= "        intro_ac_price = null,";
            $update_sql .= "        intro_ac_rate = null,";
        }
        if($c_staff_id1 == ""){
            $update_sql .= "    c_staff_id1 = null,";
        }else{
            $update_sql .= "    c_staff_id1 = $c_staff_id1,";
        }
        if($c_staff_id2 == ""){
            $update_sql .= "    c_staff_id2 = null,";
        }else{
            $update_sql .= "    c_staff_id2 = $c_staff_id2,";
        }
//        if($d_staff_id1 == ""){
//            $update_sql .= "    d_staff_id1 = null,";
//        }else{
//            $update_sql .= "    d_staff_id1 = $d_staff_id1,";
//        }
//        if($d_staff_id2 == ""){
//            $update_sql .= "    d_staff_id2 = null,";
//        }else{
//            $update_sql .= "    d_staff_id2 = $d_staff_id2,";
//        }
//        if($d_staff_id3 == ""){
//            $update_sql .= "    d_staff_id3 = null,";
//        }else{
//            $update_sql .= "    d_staff_id3 = $d_staff_id3,";
//        }
        $update_sql .= "    col_terms = '$col_terms',";
        $update_sql .= "    credit_limit = '$cledit_limit',";
        $update_sql .= "    capital = '$capital',";
        $update_sql .= "    trade_id = '$trade_id',";
        $update_sql .= "    close_day = '$close_day_cd',";
        if($pay_m == ""){
            $update_sql .= "        pay_m = '',";
        }else{
            $update_sql .= "        pay_m = '$pay_m',";
        }
        if($pay_d == ""){
            $update_sql .= "        pay_d = '',";
        }else{
            $update_sql .= "        pay_d = '$pay_d',";
        }
        $update_sql .= "    pay_way = '$pay_way',";
        if($bank_enter_cd == ""){
            $update_sql .= "        b_bank_id = null,";
        }else{
            $update_sql .= "        b_bank_id = $bank_enter_cd,";
        }
        $update_sql .= "    pay_name = '$pay_name',";
        $update_sql .= "    account_name = '$account_name',";
        if($cont_s_day == "--"){
            $update_sql .= "        cont_sday = null,";
        }else{
            $update_sql .= "        cont_sday = '$cont_s_day',";
        }
        if($cont_e_day == "--"){
            $update_sql .= "        cont_eday = null,";
        }else{
            $update_sql .= "        cont_eday = '$cont_e_day',";
        }
        if($cont_peri == ""){
            $update_sql .= "        cont_peri = null,";
        }else{
            $update_sql .= "        cont_peri = '$cont_peri',";
        }
        if($cont_r_day == "--"){
            $update_sql .= "        cont_rday = null,";
        }else{
            $update_sql .= "        cont_rday = '$cont_r_day',";
        }
        $update_sql .= "    slip_out = '$slip_out',";
        $update_sql .= "    deliver_note = '$deliver_note',";
        $update_sql .= "    claim_out = '$claim_out',";
        $update_sql .= "    coax = '$coax',";
        $update_sql .= "    tax_div = '$tax_div',";
        $update_sql .= "    tax_franct = '$tax_franct',";
        $update_sql .= "    note = '$note',";
        $update_sql .= "    email = '$email',";
        $update_sql .= "    url = '$url',";
        $update_sql .= "    rep_htel = '$represent_cell',";
        if($bstruct == ""){
            $update_sql .= "        b_struct = null,";
        }else{
            $update_sql .= "        b_struct = $bstruct,";
        }
        if($inst == ""){
            $update_sql .= "   inst_id = null,";
        }else{
            $update_sql .= "   inst_id = $inst,";
        }
        if($establish_day == "--"){
            $update_sql .= "        establish_day = null,";
        }else{
            $update_sql .= "        establish_day = '$establish_day',";
        }
        $update_sql .= "    deal_history = '$record',";
        $update_sql .= "    importance = '$important',";
        $update_sql .= "    intro_ac_name = '$trans_account',";
        $update_sql .= "    intro_bank = '$bank_fc',";
        $update_sql .= "    intro_ac_num = '$account_num',";
        if($round_start == "--"){
            $update_sql .= "        round_day = null,";
        }else{
            $update_sql .= "        round_day = '$round_start',";
        }
        $update_sql .= "    deliver_effect = '$deliver_radio',";
        $update_sql .= "    claim_send = '$claim_send',";
        $update_sql .= "    client_cread = '$cname_read', ";
        $update_sql .= "    represe = '$represe',";
        $update_sql .= "    company_name = '$company_name',";
        $update_sql .= "    company_tel  = '$company_tel',";
        $update_sql .= "    company_address = '$company_address',";
        $update_sql .= "    bank_div = '$bank_div',";
        $update_sql .= "    claim_note = '$claim_note',";
        $update_sql .= "    client_slip1 = '$client_slip1',";
        $update_sql .= "    client_slip2 = '$client_slip2',";
        if($parent_establish_day != '--'){
            $update_sql .= "    parent_establish_day = '$parent_establish_day',";
        }else{
            $update_sql .= "    parent_establish_day = null,";
        }
        $update_sql .= "    parent_rep_name = '$parent_rep_name',";
        $update_sql .= "    type = '$type',";
        $update_sql .= "    compellation = '$compellation',";
        //$update_sql .= "    claim_scope = '$claim_scope'";
		//bank account classification distinction 口座区分判定
		if($price_check == 1 && $account_price != NULL){
			//fixed amount 固定金額
			$update_sql .= "    intro_ac_div = '1', ";                                   
		}else if($rate_check == 1 && $account_rate != NULL){
			//select % ％指定
			$update_sql .= "    intro_ac_div = '2', ";                                   
		}else{
			//none なし
			$update_sql .= "    intro_ac_div = '', ";                                   
		}
        $update_sql .= "    c_tax_div = $c_tax_div";
        $update_sql .= " WHERE";
        $update_sql .= "    shop_id = $shop_id";
        $update_sql .= "    AND";
        $update_sql .= "    client_id = $get_client_id";
        $update_sql .= ";";

        $result = Db_Query($conn, $update_sql);
        if($result === false){
            Db_Query($conn, "ROLLBACK;");
            exit;
        }

        //transaction client information table 取引先情報テーブル
        if($claim_cd1 != null && $claim_cd2 != null && $claim_name != null){
            $update_sql = " UPDATE t_client_info ";
            $update_sql .= "SET ";
            $update_sql .= "    claim_id = (SELECT";
            $update_sql .= "        client_id";
            $update_sql .= "    FROM";
            $update_sql .= "        t_client";
            $update_sql .= "    WHERE";
            $update_sql .= "        shop_id = $shop_id";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd1 = '$claim_cd1'";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd2 = '$claim_cd2'";
            $update_sql .= "        AND";
            $update_sql .= "        client_div = '1'";
            $update_sql .= "    ),";
	        $update_sql .= "    intro_account_id = (SELECT";
	        $update_sql .= "        client_id";
	        $update_sql .= "    FROM";
	        $update_sql .= "        t_client";
	        $update_sql .= "    WHERE";
	        $update_sql .= "        shop_id = $shop_id";
	        $update_sql .= "        AND";
	        $update_sql .= "        client_cd1 = '$intro_act_cd'";
	        $update_sql .= "        AND";
	        $update_sql .= "        client_div = '2'";
	        $update_sql .= "    ),";
	        $update_sql .= "    cclient_shop = $cshop_id ";
            $update_sql .= "WHERE ";
            $update_sql .= "client_id = $get_client_id";
            $update_sql .= ";";
            $result = Db_Query($conn, $update_sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }
        //register yourself 自分を登録
        }else if($claim_cd1 == null && $claim_cd2 == null && $claim_name == null){
            $update_sql = " UPDATE t_client_info ";
            $update_sql .= "SET ";
            $update_sql .= "    claim_id = (SELECT";
            $update_sql .= "        client_id";
            $update_sql .= "    FROM";
            $update_sql .= "        t_client";
            $update_sql .= "    WHERE";
            $update_sql .= "        shop_id = $shop_id";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd1 = '$client_cd1'";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd2 = '$client_cd2'";
            $update_sql .= "        AND";
            $update_sql .= "        client_div = '1'";
	        $update_sql .= "    ),";
	        $update_sql .= "    cclient_shop = $cshop_id ";
            $update_sql .= "WHERE ";
            $update_sql .= "client_id = $get_client_id";
            $update_sql .= ";";
            $result = Db_Query($conn, $update_sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }
        }
        if($client_div == '1'){
            //update history table 更新履歴テーブル
            $update_sql  = " INSERT INTO t_renew (";
            $update_sql .= "    client_id,";                        //customer ID 得意先ID
            $update_sql .= "    staff_id,";                         //staff ID スタッフID
            $update_sql .= "    renew_time";                        //current timestamp 現在のtimestamp
            $update_sql .= " )VALUES(";
            $update_sql .= "    (SELECT";
            $update_sql .= "        client_id";
            $update_sql .= "    FROM";
            $update_sql .= "        t_client";
            $update_sql .= "    WHERE";
            $update_sql .= "        shop_id = $shop_id";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd1 = '$client_cd1'";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd2 = '$client_cd2'";
            $update_sql .= "        AND";
            $update_sql .= "        client_div = '1'";
            $update_sql .= "    ),";
            $update_sql .= "    $staff_id,";
            $update_sql .= "    NOW()";
            $update_sql .= ");";
            
            $result = Db_Query($conn, $update_sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }
        }else if($client_div == '3'){
            //update history table 更新履歴テーブル
            $update_sql  = " INSERT INTO t_renew (";
            $update_sql .= "    client_id,";                        //customer ID 得意先ID
            $update_sql .= "    staff_id,";                         //staff ID スタッフID
            $update_sql .= "    renew_time";                        //current timestamp 現在のtimestamp
            $update_sql .= " )VALUES(";
            $update_sql .= "    (SELECT";
            $update_sql .= "        client_id";
            $update_sql .= "    FROM";
            $update_sql .= "        t_client";
            $update_sql .= "    WHERE";
            $update_sql .= "        shop_id = $shop_id";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd1 = '$client_cd1'";
            $update_sql .= "        AND";
            $update_sql .= "        client_cd2 = '$client_cd2'";
            $update_sql .= "        AND";
            $update_sql .= "        client_div = '3'";
            $update_sql .= "    ),";
            $update_sql .= "    $staff_id,";
            $update_sql .= "    NOW()";
            $update_sql .= ");";
            
            $result = Db_Query($conn, $update_sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }
        }
        //leave the edited information in log 変更した情報をログに残す
        $result = Log_Save( $conn, "client", "2", $client_cd1."-".$client_cd2, $client_name);
        if($result === false){
            Db_Query($conn, "ROLLBACK");
            exit;
        }
    }
    Db_Query($conn, "COMMIT;");
    $complete_flg = true;
}

if($_POST["ok_button_flg"]==true){
    header("Location: ./1-1-115.php");
}

//button ボタン
//edit・list 変更・一覧
$form->addElement("button","change_button","変更・一覧","onClick=\"javascript:location.href='1-1-113.php'\"");

//register (header) 登録(ヘッダ)
//$form->addElement("button","new_button","登録画面","style=\"color: #ff0000;\" onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","new_button","登録画面",$g_button_color." onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

//create the registered form to Database DBに登録後のフォームを作成
if($complete_flg == true){
    $form->addElement("static","intro_claim_link","","請求先");
    $form->addElement("static","intro_act_link","","ご紹介口座");
    $button[] = $form->createElement(
            "button","back_button","戻　る",
            "onClick='javascript:history.back()'
            ");
    //ＯＫ
//    $button[] = $form->addElement("button","ok_button","Ｏ　Ｋ","onClick=\"javascript:Button_Submit_1('ok_button_flg', '#', 'true')\"");
    $form->freeze();
}else{

    //check the inquiry fee checkbox ご照会料のチェックボックスのチェック
    $onload = "Check_Button2($check_which);";
    //create the pre-registered form in DB DBに登録前のフォームを作成
    if($change_flg == true){
        $form->addElement("static","intro_claim_link","","請求先");
    }else{
        $form->addElement("link","intro_claim_link","","#","請求先","onClick=\"javascript:return Open_SubWin('../dialog/1-0-220-2.php',Array('form_claim[cd1]','form_claim[cd2]','form_claim[name]'), 500, 450, 1,1)\"");
    }
    $form->addElement("link","intro_act_link","","#","ご紹介口座","onClick=\"javascript:return Open_SubWin('../dialog/1-0-208.php', Array('form_intro_act[cd]', 'form_intro_act[name]'), 500, 450)\"");

    //confirm list of registered items登録済一覧確認
    $button[] = $form->createElement(
            "button","list_confirm_button","登録済一覧確認",
            "style=width:110 onClick=\"javascript:return Open_mlessDialog('../dialog/1-0-250-1.php','470','500')\""
            );

    $button[] = $form->createElement(
            "button","input_button","自動入力","onClick=\"javascript:Button_Submit_1('input_button_flg', '#', 'true', this)\""
            ); 
    $button[] = $form->createElement(
            "button","back_button","戻　る",
            "onClick='javascript:history.back()'
            ");
    if($_GET["client_id"] != null){
    $button[] = $form->createElement(
            "button","res_button","実　績",
            "onClick=\"javascript:window.open('".HEAD_DIR."system/1-1-106.php?client_id=$get_client_id','_blank','width=480,height=600')\""
            );
    }

    //next button 次へボタン
    if($next_id != null){
        $form->addElement("button","next_button","次　へ","onClick=\"location.href='./1-1-115.php?client_id=$next_id'\"");
    }else{
        $form->addElement("button","next_button","次　へ","disabled");
    }
    //previous button 前へボタン
    if($back_id != null){
        $form->addElement("button","back_button","前　へ","onClick=\"location.href='./1-1-115.php?client_id=$back_id'\"");
    }else{
        $form->addElement("button","back_button","前　へ","disabled");
    }
}
$form->addGroup($button, "button", "");


/***************************/
//Code_value
/***************************/
//請求先 invoice 

$code_value = Code_Value("t_client",$conn,"",5);
$where_sql = "    WHERE";
$where_sql .= "        shop_id = $shop_id";
$where_sql .= "        AND";
$where_sql .= "        client_div = '2'";
$code_value .= Code_Value("t_client",$conn,"$where_sql",2);



/****************************acquire contract ending date契約終了日取得*************************/

$contract = "function Contract(me){\n";
$contract .= "  var TERM = \"form_cont_peri\";\n";
$contract .= "  var SY = \"form_cont_s_day[y]\";\n";
$contract .= "  var SM = \"form_cont_s_day[m]\";\n";
$contract .= "  var SD = \"form_cont_s_day[d]\";\n";
$contract .= "  var EY = \"form_cont_e_day[y]\";\n";
$contract .= "  var EM = \"form_cont_e_day[m]\";\n";
$contract .= "  var ED = \"form_cont_e_day[d]\";\n";
$contract .= "  var RY = \"form_cont_r_day[y]\";\n";
$contract .= "  var RM = \"form_cont_r_day[m]\";\n";
$contract .= "  var RD = \"form_cont_r_day[d]\";\n";
$contract .= "  var s_year = parseInt(me.elements[SY].value);\n";
$contract .= "  var r_year = parseInt(me.elements[RY].value);\n";
$contract .= "  var term = me.elements[TERM].value;\n";
$contract .= "  len_ry = me.elements[RY].value.length;\n";
$contract .= "  len_rm = me.elements[RM].value.length;\n";
$contract .= "  len_rd = me.elements[RD].value.length;\n";
$contract .= "  len_sy = me.elements[SY].value.length;\n";
$contract .= "  len_sm = me.elements[SM].value.length;\n";
$contract .= "  len_sd = me.elements[SD].value.length;\n";
$contract .= "  if(len_rm == 1){\n";
$contract .= "      me.elements[RM].value = '0'+me.elements[RM].value;\n";
$contract .= "      len_rm = me.elements[RM].value.length;\n";
$contract .= "  }\n";
$contract .= "  if(len_rd == 1){\n";
$contract .= "      me.elements[RD].value = '0'+me.elements[RD].value;\n";
$contract .= "      len_rd = me.elements[RD].value.length;\n";
$contract .= "  }\n";
$contract .= "  if(len_sm == 1){\n";
$contract .= "      me.elements[SM].value = '0'+me.elements[SM].value;\n";
$contract .= "      len_sd = me.elements[SD].value.length;\n";
$contract .= "  }\n";
$contract .= "  if(len_sd == 1){\n";
$contract .= "      me.elements[SD].value = '0'+me.elements[SD].value;\n";
$contract .= "      len_sd = me.elements[SD].value.length;\n";
$contract .= "  }\n";
$contract .= "  if(me.elements[RM].value == '02' && me.elements[RD].value == '29' && term != \"\" && len_ry == 4 && len_rm == 2 && len_rd == 2){\n";
$contract .= "      var term = parseInt(term);\n";
$contract .= "      me.elements[EY].value = r_year+term;\n";
$contract .= "      me.elements[EM].value = \"03\";\n";
$contract .= "      me.elements[ED].value = \"01\";\n";
$contract .= "  }else if(term != \"\" && len_ry == 4 && len_rm == 2 && len_rd == 2){\n";
$contract .= "      var term = parseInt(term);\n";
$contract .= "      me.elements[EY].value = r_year+term;\n";
$contract .= "      me.elements[EM].value = me.elements[RM].value;\n";
$contract .= "      me.elements[ED].value = me.elements[RD].value;\n";
$contract .= "  }else if(me.elements[SM].value == '02' && me.elements[SD].value == '29' && term != \"\" && len_sy == 4 && len_sm == 2 && len_sd == 2){\n";
$contract .= "      var term = parseInt(term);\n";
$contract .= "      me.elements[EY].value = s_year+term;\n";
$contract .= "      me.elements[EM].value = \"03\";\n";
$contract .= "      me.elements[ED].value = \"01\";\n";
$contract .= "  }else if(term != \"\" && len_sy == 4 && len_sm == 2 && len_sd == 2){\n";
$contract .= "      var term = parseInt(term);\n";
$contract .= "      me.elements[EY].value = s_year+term;\n";
$contract .= "      me.elements[EM].value = me.elements[SM].value;\n";
$contract .= "      me.elements[ED].value = me.elements[SD].value;\n";
$contract .= "  }else{\n";
$contract .= "      me.elements[EY].value = \"\";\n";
$contract .= "      me.elements[EM].value = \"\";\n";
$contract .= "      me.elements[ED].value = \"\";\n";
$contract .= "  }\n";
$contract .= "}\n";

/****************************************************/
/*
//集金日 collection date
$contract .= "function pay_way(){\n";
$contract .= "  if(document.dateForm.trade_aord_1.value=='61'){\n";
$contract .= "      document.dateForm.form_close.value='';\n";
$contract .= "      document.dateForm.form_pay_m.value='';\n";
$contract .= "      document.dateForm.form_pay_d.value='';\n";
$contract .= "  }\n";
$contract .= "}\n";

/****************************/
//HTMLヘッダ HTML header
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//HTMLフッタ HTML footer 
/****************************/
$html_footer = Html_Footer();

/****************************/
//メニュー作成 create menu
/****************************/
$page_menu = Create_Menu_h('system','1');

/****************************/
//画面ヘッダー作成 create screen header 
/****************************/

/****************************/
//全件数取得 acquire all item
/****************************/
$client_sql  = " SELECT";
$client_sql .= "     COUNT(client_id)";
$client_sql .= " FROM";
$client_sql .= "     t_client";
$client_sql .= " WHERE";
$client_sql .= "     t_client.client_div = '1'";
$client_sql .= ";";

//all items that will be displayed in header ヘッダーに表示させる全件数
$total_count_sql = $client_sql.";";
$count_res = Db_Query($conn, $total_count_sql);
$total_count = pg_fetch_result($count_res,0,0);

$page_title .= "(全".$total_count."件)";
//$page_title .= "　".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
$page_header = Create_Header($page_title);

// render related setting Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//assign form related variable form関連の変数をassign
$smarty->assign('form',$renderer->toArray());
//その他の変数をassign assign other values
$smarty->assign('var',array(
    'html_header'           => "$html_header",
    'page_menu'             => "$page_menu",
    'page_header'           => "$page_header",
    'html_footer'           => "$html_footer",
    'claim_err'             => "$claim_err",
    'intro_act_err'         => "$intro_act_err",
    'close_err'             => "$close_err",
    'sday_err'              => "$csday_err",
    'rday_err'              => "$crday_err",
    'rsday_err'             => "$rsday_err",
    'esday_err'             => "$esday_err",
    'sday_rday_err'         => "$csday_rday_err",
    'code_value'            => "$code_value",
    'client_cd_err'         => "$client_cd_err",
    'tel_err'               => "$tel_err",
    'fax_err'               => "$fax_err",
    'contract'              => "$contract",
    'next_id'               =>  $next_id,
    'back_id'               =>  $back_id,
    'onload'                => "$onload",
    'email_err'             => "$email_err",
    'url_err'               => "$url_err",
    'rep_cell_err'          => "$rep_cell_err",
    'd_tel_err'             => "$d_tel_err",
    'company_tel_err'       => "$company_tel_err",
    'auth_r_msg'            => "$auth_r_msg",
	'parent_esday_err'      => "$parent_esday_err",
	'cont_e_day_err'        => "$cont_e_day_err",
    'claim_coax_err'        => "$claim_coax_err",
    'claim_tax_div_err'     => "$claim_tax_div_err",
    'claim_tax_franct_err'  => "$claim_tax_franct_err",
    'claim_c_tax_div_err'   => "$claim_c_tax_div_err",
));

//テンプレートへ値を渡す pass the value to template
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
