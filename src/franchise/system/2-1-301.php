<?php
/******************************
 *変更履歴
 *  社印の登録・削除処理追加（20060831）kaji
 *  カレンダー表示期間変更時処理追加（20061103）suzuki
 *  直営同士はカレンダー表示期間を共有するように変更（20061127）suzuki
 *
******************************/

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-12-09      ban_0106    suzuki      日付をゼロ埋め
 *  2007/04/05      B0702-034   kajioka-h   社名1の必須チェックが外れていたのを修正
 *  2007/09/27                  watanabe-k  税切替日には未来の日付のみ入力可能とするチェックを追加
 *  2009/12/25                  aoyama-n    旧消費税率と現消費税率を表示し、新消費税率を登録できるように変更
 *  2010/01/10                  aoyama-n    消費税率改定の為の受注ヘッダの消費税額更新処理追加
 *  2015/05/01                  amano  Dialogue関数でボタン名が送られない IE11 バグ対応
 * 
*/

$page_title = "自社プロフィール";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//SESSION情報
/****************************/
$client_id  = $_SESSION["client_id"];
$group_kind = $_SESSION["group_kind"];

/****************************/
//初期値(radio)
/****************************/
$def_fdata = array(
    "form_claim_num"    => "1",
    "form_coax" => "1",
    "from_fraction_div" => "1",
    "form_cal_peri"     => "1",
);
$form->setDefaults($def_fdata);

//社印のパス
$path_shain = FC_DIR."system/2-1-301-3.php?shop_id=".$client_id;

//hiddenの状態値があった場合に復元
if($_POST["before_state"] != NULL){
	$before_state = $_POST["before_state"];
}

/****************************/
//初期表示処理
/****************************/
$select_sql  = "SELECT \n";
$select_sql .= "    t_client.client_cd1,\n";                  //ショップコード１ 0
$select_sql .= "    t_client.client_cd2,\n";                  //ショップコード２ 1
$select_sql .= "    t_client.shop_name,\n";                   //社名 2
$select_sql .= "    t_client.shop_name2,\n";                  //社名2 3
$select_sql .= "    t_client.shop_read,\n";                   //社名(フリガナ) 4
$select_sql .= "    t_client.shop_read2,\n";                  //社名2(フリガナ) 5
$select_sql .= "    t_client.client_name,\n";                 //ショップ名 6
$select_sql .= "    t_client.client_read,\n";                 //ショップ名(フリガナ) 7
$select_sql .= "    t_client.represe,\n";                     //代表者役職 8
$select_sql .= "    t_client.rep_name,\n";                    //代表者氏名 9
$select_sql .= "    t_client.rep_htel,\n";                    //代表者携帯 10
$select_sql .= "    t_client.post_no1,\n";                    //郵便番号 11
$select_sql .= "    t_client.post_no2,\n";                    //郵便番号 12
$select_sql .= "    t_client.address1,\n";                    //住所1 13
$select_sql .= "    t_client.address2,\n";                    //住所2 14
$select_sql .= "    t_client.address3,\n";                    //住所3 15
$select_sql .= "    t_client.address_read,\n";                //住所(フリガナ) 16
$select_sql .= "    t_client.capital,\n";                     //資本金 17
$select_sql .= "    t_client.tel,\n";                         //TEL 18
$select_sql .= "    t_client.fax,\n";                         //FAX 19
$select_sql .= "    t_client.email,\n";                       //Email 20
$select_sql .= "    t_client.url,\n";                         //URL 21
$select_sql .= "    t_client.direct_tel,\n";                  //直通TEL 22
#2009-12-25 aoyama-n
#$select_sql .= "    t_client.tax_rate_n,\n";                  //現在消費税率 23
#$select_sql .= "    t_client.tax_rate_c,\n";                  //消費税率 24
#$select_sql .= "    t_client.tax_rate_cday,\n";               //税率切替日 25
$select_sql .= "    NULL,\n";                                 //現在消費税率 23
$select_sql .= "    NULL,\n";                                 //消費税率 24
$select_sql .= "    NULL,\n";                                 //税率切替日 25
$select_sql .= "    t_client.my_close_day,\n";                //自社締日 26
$select_sql .= "    t_client.my_pay_m,\n";                    //支払日(月) 27
$select_sql .= "    t_client.my_pay_d,\n";                    //支払日(日) 28
$select_sql .= "    t_client.cutoff_month,\n";                //決算月 29
$select_sql .= "    t_client.cutoff_day,\n";                  //決算日 30
$select_sql .= "    t_client.claim_set,\n";                   //請求書番号設定 31
$select_sql .= "    t_client.regist_day,\n";                  //法人登記日 32
$select_sql .= "    '',\n";                                   //本社・支社区分 33(本社支社区分カラムは削除するが、処理変更を少なくするために空を抽出)
$select_sql .= "    t_client.establish_day,\n";               //創業日 34
$select_sql .= "    t_client.area_id,\n";                     //地区 35
$select_sql .= "    t_client.ware_id,\n";                     //基本出荷倉庫 36
$select_sql .= "    t_client.charger_name,\n";                //連絡担当者氏名 37
$select_sql .= "    t_client.charger,\n";                     //連絡担当者役職 38
$select_sql .= "    t_client.cha_htel,\n";                    //連絡担当者携帯 39
$select_sql .= "    t_client.my_coax, \n";                    //金額丸め区分 40
$select_sql .= "    t_client.my_tax_franct, \n";              //消費税端数区分 41
$select_sql .= "    t_client.cal_peri, \n";                   //カレンダー表示期間 42
$select_sql .= "    t_client.pay_m, \n";                      //集金日（月） 43
$select_sql .= "    t_client.pay_d, ";                      //集金日（日） 44
$select_sql .= "    t_client2.close_day, \n";                 // アメニティとの取引締日　45
$select_sql .= "    t_client2.pay_m, \n";                     // アメニティとの取引支払月　46
#2009-12-25 aoyama-n
#$select_sql .= "    t_client2.pay_d \n";                      // アメニティとの取引支払日　47
$select_sql .= "    t_client2.pay_d, \n";                      // アメニティとの取引支払日　47
$select_sql .= "    t_client.tax_rate_old, \n";                //旧消費税率
$select_sql .= "    t_client.tax_rate_now, \n";                //現消費税率
$select_sql .= "    t_client.tax_change_day_now, \n";          //現税率切替日
$select_sql .= "    t_client.tax_rate_new, \n";                //新消費税率
$select_sql .= "    t_client.tax_change_day_new \n";           //新税率切替日
$select_sql .= " FROM\n";
$select_sql .= "    t_client, \n";
//$select_sql .= "    INNER JOIN \n";
$select_sql .= " (SELECT close_day, pay_m, pay_d, shop_id FROM t_client WHERE head_flg = 't' AND \n";
$select_sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") \n" : " shop_id = $client_id \n";
$select_sql .= ") AS t_client2 \n";
///$select_sql .= "    ON t_client.client_id = t_client2.shop_id \n";
$select_sql .= " WHERE\n";
//$select_sql .= ($group_kind == "2") ? " t_client.shop_id IN (".Rank_Sql().") " : " t_client.shop_id = $client_id ";
$select_sql .= " t_client.client_id = $client_id \n";
$select_sql .= ";\n";

//クエリ発行
$result = Db_Query($db_con, $select_sql);

//Get_Id_Check($result);
//データ取得
$head_data = @pg_fetch_array ($result, 0, PGSQL_NUM);

//巡回基準日取得
$select_sql = "SELECT ";
$select_sql .= "    stand_day";
$select_sql .= " FROM";
$select_sql .= "    t_stand;";
//クエリ発行
$result = Db_Query($db_con, $select_sql);

//データ取得
$stand_row = @pg_num_rows($result);
$abcd_data = @pg_fetch_array ($result, 0, PGSQL_NUM);

//初期値データ
$defa_data["form_shop_cd"]["cd1"]         = $head_data[0];         //ショップコード1
$defa_data["form_shop_cd"]["cd2"]         = $head_data[1];         //ショップコード2
$defa_data["form_comp_name"]              = $head_data[2];         //社名
$defa_data["form_comp_name2"]             = $head_data[3];         //社名2
$defa_data["form_comp_read"]              = $head_data[4];         //社名(フリガナ)
$defa_data["form_comp_read2"]             = $head_data[5];         //社名2(フリガナ)
$defa_data["form_cname"]                  = $head_data[6];         //ショップ名
$defa_data["form_cread"]                  = $head_data[7];         //ショップ名(フリガナ)
$defa_data["form_represe"]                = $head_data[8];         //代表者役職
$defa_data["form_rep_name"]               = $head_data[9];         //代表者氏名
$defa_data["form_represent_cell"]         = $head_data[10];        //代表者携帯
$defa_data["form_post_no"]["no1"]         = $head_data[11];        //郵便番号
$defa_data["form_post_no"]["no2"]         = $head_data[12];        //郵便番号
$defa_data["form_address1"]               = $head_data[13];        //住所1
$defa_data["form_address2"]               = $head_data[14];        //住所2
$defa_data["form_address3"]               = $head_data[15];        //住所3
$defa_data["form_address_read"]           = $head_data[16];        //住所(フリガナ)
$defa_data["form_capital_money"]          = $head_data[17];        //資本金
$defa_data["form_tel"]                    = $head_data[18];        //TEL
$defa_data["form_fax"]                    = $head_data[19];        //FAX
$defa_data["form_email"]                  = $head_data[20];        //Email
$defa_data["form_url"]                    = $head_data[21];        //URL
$defa_data["form_direct_tel"]             = $head_data[22];        //直通TEL
$defa_data["form_tax_now"]                = $head_data[23];        //現在消費税率
#2009-12-25 aoyama-n
#$defa_data["form_tax"]                    = $head_data[24];        //消費税率
#$rate_day[y] = substr($head_data[25],0,4);
#$rate_day[m] = substr($head_data[25],5,2);
#$rate_day[d] = substr($head_data[25],8,2);
#$defa_data["form_tax_rate_day"]["y"]      = $rate_day[y];          //税率切替日(年)
#$defa_data["form_tax_rate_day"]["m"]      = $rate_day[m];          //税率切替日(月)
#$defa_data["form_tax_rate_day"]["d"]      = $rate_day[d];          //税率切替日(日)
$defa_data["form_close_day"]              = $head_data[45];        //締日
$defa_data["form_pay_month"]              = $head_data[46];        //支払日(月)
$defa_data["form_pay_day"]                = $head_data[47];        //支払日(日)
$defa_data["form_my_close_day"]           = $head_data[26];        //自社締日

//支払日存在判定
if($head_data[27] != NULL && $head_data[28] != NULL){
    //支払日（＊自社）をセット
    $defa_data["form_my_pay_month"]       = $head_data[27];        //自社支払日(月)
    $defa_data["form_my_pay_day"]         = $head_data[28];        //自社支払日(日)
}else{
    //支払日が設定されていない場合は、ＦＣマスタの集金日をセット
    $defa_data["form_my_pay_month"]       = $head_data[43];        //集金日(月)
    $defa_data["form_my_pay_day"]         = $head_data[44];        //集金日(日)
}
$defa_data["form_cutoff_month"]           = $head_data[29];        //決算月
$defa_data["form_cutoff_day"]             = $head_data[30];        //決算月
//請求書番号設定が存在するか判定
if($head_data[31] != NULL){
    $defa_data["form_claim_num"]              = $head_data[31];        //請求書番号設定
}
$corpo_day[y] = substr($head_data[32],0,4);
$corpo_day[m] = substr($head_data[32],5,2);
$corpo_day[d] = substr($head_data[32],8,2);
$defa_data["form_corpo_day"]["y"]         = $corpo_day[y];         //法人登記日(年)
$defa_data["form_corpo_day"]["m"]         = $corpo_day[m];         //法人登記日(月)
$defa_data["form_corpo_day"]["d"]         = $corpo_day[d];         //法人登記日(日)
//$attach_gid                               = $head_data[33];
$establish_day[y] = substr($head_data[34],0,4);
$establish_day[m] = substr($head_data[34],5,2);
$establish_day[d] = substr($head_data[34],8,2);
$defa_data["form_establish_day"]["y"]     = $establish_day[y];     //創業日(年)
$defa_data["form_establish_day"]["m"]     = $establish_day[m];     //創業日(月)
$defa_data["form_establish_day"]["d"]     = $establish_day[d];     //創業日(日)
$defa_data["form_area"]                  = $head_data[35];         //地区
//$defa_data["form_ware"]                  = $head_data[36];         //基本出荷倉庫
$defa_data["form_contact_name"]          = $head_data[37];         //連絡担当者氏名
$defa_data["form_contact_position"]      = $head_data[38];         //連絡担当者役職
$defa_data["form_contact_cell"]          = $head_data[39];         //連絡担当者携帯
//金額丸め区分が存在するか判定
if($head_data[40] != NULL){
    $defa_data["form_coax"]              = $head_data[40];         //金額丸め区分
}
//消費税端数区分が存在するか判定
if($head_data[41] != NULL){
    $defa_data["from_fraction_div"]          = $head_data[41];         //消費税端数区分
}

//カレンダー表示期間が存在するか判定
if($head_data[42] != NULL){
    $defa_data["form_cal_peri"]          = $head_data[42];         //カレンダー表示期間
}

$before_state                            = $head_data[42];         //初期表示の状態をhiddenにセット
$defa_data["before_state"]               = $head_data[42];         

#2009-12-25 aoyama-n
$defa_data["form_tax_rate_old"]           = $head_data[48];         //旧消費税率
$defa_data["form_tax_rate_now"]           = $head_data[49];         //現消費税率
$change_day_now[y] = substr($head_data[50],0,4);
$change_day_now[m] = substr($head_data[50],5,2);
$change_day_now[d] = substr($head_data[50],8,2);
$defa_data["form_tax_change_day_now"]["y"] = $change_day_now[y];    //現消率切替日
$defa_data["form_tax_change_day_now"]["m"] = $change_day_now[m];    //現消率切替日
$defa_data["form_tax_change_day_now"]["d"] = $change_day_now[d];    //現消率切替日
$defa_data["form_tax_rate_new"]           = $head_data[51];         //新消費税率
$change_day_new[y] = substr($head_data[52],0,4);
$change_day_new[m] = substr($head_data[52],5,2);
$change_day_new[d] = substr($head_data[52],8,2);
$defa_data["form_tax_change_day_new"]["y"] = $change_day_new[y];    //新消率切替日
$defa_data["form_tax_change_day_new"]["m"] = $change_day_new[m];    //新消率切替日
$defa_data["form_tax_change_day_new"]["d"] = $change_day_new[d];    //新消率切替日

$abcd_day[y] = substr($abcd_data[0],0,4);
$abcd_day[m] = substr($abcd_data[0],5,2);
$abcd_day[d] = substr($abcd_data[0],8,2);
$defa_data["form_abcd_day"]["y"]          = $abcd_day[y];          //ABCD巡回基準日(年)
$defa_data["form_abcd_day"]["m"]          = $abcd_day[m];          //ABCD巡回基準日(月)
$defa_data["form_abcd_day"]["d"]          = $abcd_day[d];          //ABCD巡回基準日(日)

//初期値設定                                         
$form->setDefaults($defa_data);

/*
//FCグループコード取得
if($attach_gid != null){
    $select_sql = "SELECT ";
    $select_sql .= "    shop_gcd";
    $select_sql .= " FROM";
    $select_sql .= "    t_shop_gr";
    $select_sql .= " WHERE";
    $select_sql .= "    shop_gid = $attach_gid";
    $select_sql .= ";";
    //クエリ発行
    $result = Db_Query($db_con, $select_sql);
    //データ取得
    $attach_gid = @pg_fetch_result ($result, 0);
}
*/

/****************************/
//フォーム生成
/****************************/
//ショップコード
$form_shop_cd[] =& $form->createElement(
        "text","cd1","テキストフォーム","size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
        $g_form_option"
        );
$form_shop_cd[] =& $form->createElement(
        "static","","","-"
        );
$form_shop_cd[] =& $form->createElement(
        "text","cd2","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        $g_form_option"
        );
$anytime_freeze[] = $form->addGroup( $form_shop_cd, "form_shop_cd", "form_shop_cd");

//社名
$form->addElement(
        "text","form_comp_name","テキストフォーム","size=\"46\" maxLength=\"25\" 
        $g_form_option"
        );

//社名2
$form->addElement(
        "text","form_comp_name2","テキストフォーム","size=\"46\" maxLength=\"25\" 
        $g_form_option"
        );

//社名(フリガナ)
$form->addElement(
        "text","form_comp_read","テキストフォーム","size=\"46\" maxLength=\"50\" 
        $g_form_option"
        );

//社名2(フリガナ)
$form->addElement(
        "text","form_comp_read2","テキストフォーム","size=\"46\" maxLength=\"50\" 
        $g_form_option"
        );

//ショップ名
$anytime_freeze[] = $form->addElement(
        "text","form_cname","テキストフォーム","size=\"46\" maxLength=\"25\" 
        $g_form_option"
        );

//ショップ名（フリガナ）
$anytime_freeze[] = $form->addElement(
        "text","form_cread","テキストフォーム","size=\"46\" maxLength=\"50\" 
        $g_form_option"
        );

//代表者役職
$form->addElement(
        "text","form_represe","テキストフォーム","size=\"22\" maxLength=\"10\" 
        $g_form_option"
        );

//代表者氏名
$form->addElement(
        "text","form_rep_name","テキストフォーム","size=\"34\" maxLength=\"15\" 
        $g_form_option"
        );

//直通TEL
$form->addElement(
        "text","form_direct_tel","","size=\"34\" maxLength=\"30\" style=\"$g_form_style\""." $g_form_option"
        );

//代表者携帯
$form->addElement(
        "text","form_represent_cell","テキストフォーム","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

//地区
$where  = " where ";
$where .= " shop_id = (SELECT shop_id FROM t_client WHERE client_div = '0') ";
$select_ary = Select_Get($db_con,'area',$where);
$anytime_freeze[] = $form->addElement('select', 'form_area',"", $select_ary,$g_form_option_select);

//郵便番号
$form_post[] =& $form->createElement(
        "text","no1","テキストフォーム","size=\"3\" maxLength=\"3\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_post_no[no1]','form_post_no[no2]',3)\"".$g_form_option."\""
        );
$form_post[] =& $form->createElement(
        "static","","","-"
        );
$form_post[] =& $form->createElement(
        "text","no2","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        $g_form_option"
        );
$form->addGroup( $form_post, "form_post_no", "form_post_no");

//資本金
$form->addElement(
        "text","form_capital_money","",
        "class=\"money\" size=\"11\" maxLength=\"9\" style=\"$g_form_style;text-align: right\"
        ".$g_form_option.""
        );

//住所1
$form->addElement(

        "text","form_address1","テキストフォーム","size=\"46\" maxLength=\"25\" 
        $g_form_option"
        );

//住所2
$form->addElement(
        "text","form_address2","テキストフォーム","size=\"46\" maxLength=\"25\" 
        $g_form_option"
        );

//住所3
$form->addElement(
        "text","form_address3","テキストフォーム","size=\"46\" maxLength=\"30\" 
        $g_form_option"
        );

//住所2(フリガナ)
$form->addElement(
        "text","form_address_read","テキストフォーム","size=\"46\" maxLength=\"50\" 
        $g_form_option"
        );

//TEL
$form->addElement(
        "text","form_tel","テキストフォーム","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

//FAX
$form->addElement(
        "text","form_fax","テキストフォーム","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

//Email
$form->addElement(
        "text","form_email","","size=\"34\" maxLength=\"60\" style=\"$g_form_style\""." $g_form_option"
        );

//URL
$form->addElement(
        "text","form_url","テキストフォーム","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

#2009-12-25 aoyama-n
/*
//消費税率(現在)
$form->addElement(
        "text","form_tax_now","テキストフォーム","size=\"1\" maxLength=\"2\" 
         style=\"text-align: center; border : #ffffff; background-color: #ffffff;\" readonly"
         );

//消費税率
$form->addElement(
        "text","form_tax","テキストフォーム","size=\"1\" maxLength=\"2\" 
        $g_form_option 
         style=\"$g_form_style;text-align: right\"");
*/


//創業日
/*
$form_establish_day[] =& $form->createElement(
        "text","y","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_establish_day[y]','form_establish_day[m]',4)\" 
        $g_form_option"
        );
$form_establish_day[] =& $form->createElement(
        "static","","","-"
        );
$form_establish_day[] =& $form->createElement(
        "text","m","テキストフォーム","size=\"2\" maxLength=\"2\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_establish_day[m]','form_establish_day[d]',2)\" 
        ".$g_form_option."\""
        );
$form_establish_day[] =& $form->createElement(
        "static","","","-"
        );
$form_establish_day[] =& $form->createElement(
        "text","d","テキストフォーム","size=\"2\" maxLength=\"2\" style=\"$g_form_style\" 
        ".$g_form_option."\""
        );
$form->addGroup( $form_establish_day,"form_establish_day","form_establish_day");
*/
Addelement_Date($form,"form_establish_day","創業日","-");


//法人登記日
/*
$form_corpo_day[] =& $form->createElement(
        "text","y","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_corpo_day[y]','form_corpo_day[m]',4)\" 
        ".$g_form_option."\""
        );
$form_corpo_day[] =& $form->createElement(
        "static","","","-"
        );
$form_corpo_day[] =& $form->createElement(
        "text","m","テキストフォーム","size=\"2\" maxLength=\"2\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_corpo_day[m]','form_corpo_day[d]',2)\" 
        ".$g_form_option."\""
        );
$form_corpo_day[] =& $form->createElement(
        "static","","","-"
        );
$form_corpo_day[] =& $form->createElement(
        "text","d","テキストフォーム","size=\"2\" maxLength=\"2\" style=\"$g_form_style\" 
        ".$g_form_option."\""
        );
$form->addGroup( $form_corpo_day,"form_corpo_day","form_corpo_day");
*/
Addelement_Date($form,"form_corpo_day","法人登記日","-");


//連絡担当者氏名
$form->addElement(
        "text","form_contact_name","テキストフォーム","size=\"34\" maxLength=\"15\" 
        $g_form_option"
        );

//連絡担当者役職
$form->addElement(
        "text","form_contact_position","テキストフォーム","size=\"22\" maxLength=\"10\" 
        $g_form_option"
        );

//連絡担当者携帯
$form->addElement(
        "text","form_contact_cell","テキストフォーム","size=\"34\" maxLength=\"30\" style=\"$g_form_style\" 
        $g_form_option"
        );

//税率切替日
#2009-12-25
/*
$form_tax_rate_day[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_tax_rate_day[y]','form_tax_rate_day[m]',4)\" 
        ".$g_form_option."\""
        );
$form_tax_rate_day[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_rate_day[] =& $form->createElement(
        "text","m","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" 
        onkeyup=\"changeText(this.form,'form_tax_rate_day[m]','form_tax_rate_day[d]',2)\" 
        ".$g_form_option."\""
        );
$form_tax_rate_day[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_rate_day[] =& $form->createElement(
        "text","d","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" 
        ".$g_form_option."\""
        );
$form->addGroup( $form_tax_rate_day,"form_tax_rate_day","");
*/

//旧消費税率
$form->addElement(
        "text","form_tax_rate_old","テキストフォーム","size=\"2\" maxLength=\"2\"
         style=\"text-align: center; border : #ffffff; background-color: #ffffff;$g_form_style\" readonly"
         );

//現消費税率
$form->addElement(
        "text","form_tax_rate_now","テキストフォーム","size=\"2\" maxLength=\"2\"
         style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
         );

//現税率切替日
$form_tax_change_day_now[] =& $form->createElement(
        "text","y","","size=\"5\" maxLength=\"4\"
         style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
         );
$form_tax_change_day_now[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_now[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\"
         style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
        );
$form_tax_change_day_now[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_now[] =& $form->createElement(
        "text","d","","size=\"2\" maxLength=\"2\"
         style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
        );
$form->addGroup( $form_tax_change_day_now,"form_tax_change_day_now","");


//新消費税率設定フラグ
$form->addElement("checkbox", "form_tax_setup_flg", "", "");

//新消費税率
$form->addElement(
        "text","form_tax_rate_new","テキストフォーム","size=\"1\" maxLength=\"2\"
        $g_form_option
        style=\"text-align: right;$g_form_style\"");

//新税率切替日
$form_tax_change_day_new[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_tax_change_day_new[y]','form_tax_change_day_new[m]',4)\"
        ".$g_form_option."\""
        );
$form_tax_change_day_new[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_new[] =& $form->createElement(
        "text","m","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_tax_change_day_new[m]','form_tax_change_day_new[d]',2)\"
        ".$g_form_option."\""
        );
$form_tax_change_day_new[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_new[] =& $form->createElement(
        "text","d","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        ".$g_form_option."\""
        );
$form->addGroup( $form_tax_change_day_new,"form_tax_change_day_new","");


//自社締日
$close_day = Select_Get($db_con, "close" );
$anytime_freeze[] = $form->addElement('select', 'form_close_day', 'セレクトボックス', $close_day, "onchange=\"window.focus();\"");

//支払日
//月
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
$anytime_freeze[] = $form->addElement("select", "form_pay_month", "セレクトボックス", $select_month, $g_form_option_select);

//日
for($i = 0; $i <= 29; $i++){
    if($i == 29){
        $select_day[$i] = '月末';
    }elseif($i == 0){
        $select_day[null] = null;
    }else{
        $select_day[$i] = $i."日";
    }
}
$anytime_freeze[] = $form->addElement("select", "form_pay_day", "セレクトボックス", $select_day, $g_form_option_select);

//自社締日
$my_close_day = Select_Get($db_con, "close" );
$form->addElement('select', 'form_my_close_day', 'セレクトボックス', $my_close_day, "onchange=\"window.focus();\"");

//支払日
//月
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
$form->addElement("select", "form_my_pay_month", "セレクトボックス", $select_month, $g_form_option_select);

//日
for($i = 0; $i <= 29; $i++){
    if($i == 29){
        $select_day[$i] = '月末';
    }elseif($i == 0){
        $select_day[null] = null;
    }else{
        $select_day[$i] = $i."日";
    }
}
$form->addElement("select", "form_my_pay_day", "セレクトボックス", $select_day, $g_form_option_select);

//決算月
$form->addElement(
        "text","form_cutoff_month","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style;text-align: right\"
        ".$g_form_option."\""
        );

//決算月
$form->addElement(
        "text","form_cutoff_day","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style;text-align: right\"
        ".$g_form_option."\""
        );

//ABCD巡回基準日
$form_abcd_day[] =& $form->createElement(
        "static","y","","-"
        );
/*$form_abcd_day[] =& $form->createElement(
        "static","","","-"
        );*/
$form_abcd_day[] =& $form->createElement(
        "static","m","","-"
        );
/*$form_abcd_day[] =& $form->createElement(
        "static","","","-"
        );*/
$form_abcd_day[] =& $form->createElement(
        "static","d","","-"
        );
$form->addGroup( $form_abcd_day,"form_abcd_day","","-");

//請求書番号設定
$form_claim_num[] =& $form->createElement(
        "radio",NULL,NULL, "通常","1"
        );
$form_claim_num[] =& $form->createElement(
        "radio",NULL,NULL, "年度別　","2"
        );
$form_claim_num[] =& $form->createElement(
        "radio",NULL,NULL, "月別","3"
        );
$form->addGroup($form_claim_num, "form_claim_num", "請求書番号設定");

//まるめ区分
$form_coax[] =& $form->createElement(
         "radio",NULL,NULL, "切捨","1"
         );
$form_coax[] =& $form->createElement(
         "radio",NULL,NULL, "四捨五入","2"
         );
$form_coax[] =& $form->createElement(
         "radio",NULL,NULL, "切上","3"
         );
$form->addGroup($form_coax, "form_coax", "まるめ区分");

//端数区分
$from_fraction_div[] =& $form->createElement(
         "radio",NULL,NULL, "切捨","1"
         );
$from_fraction_div[] =& $form->createElement(
         "radio",NULL,NULL, "四捨五入","2"
         );
$from_fraction_div[] =& $form->createElement(
         "radio",NULL,NULL, "切上","3"
         );
$form->addGroup($from_fraction_div, "from_fraction_div", "端数区分");

//カレンダー表示期間
$form_cal_peri[] =& $form->createElement(
         "radio",NULL,NULL, "1ヶ月","1"
         );
$form_cal_peri[] =& $form->createElement(
         "radio",NULL,NULL, "2ヶ月","2"
         );
$form_cal_peri[] =& $form->createElement(
         "radio",NULL,NULL, "3ヶ月","3"
         );
$form->addGroup( $form_cal_peri,"form_cal_peri","カレンダー表示期間");

//基本出荷倉庫
//$select_value = Select_Get($db_con,'ware');
//$form->addElement('select', 'form_ware', 'セレクトボックス', $select_value,$g_form_option_select);

//自動入力ボタン
$button[] = $form->createElement(
        "button","input_button","自動入力","onClick=\"javascript:Button_Submit_1('input_button_flg','#','true')\""
        ); 

//登録ボタン
$button[] = $form->createElement(
        "submit","entry_button","登　録",
        "onClick=\"javascript:return Dialogue('登録します。','#', this)\" $disabled"
);

//社印の変更ボタン
$button[] = $form->createElement(
        "button","change_stamp","社印の変更","onClick=\"location.href('2-1-301-2.php')\" $disabled"
        );

//社印の削除ボタン
$button[] = $form->createElement(
        "submit","delete_stamp","社印の削除","onClick=\"javascript:return dialogue5('社印を削除します。','#')\" $disabled"
        );

$form->addGroup($button, "button", "");

//hidden
$form->addElement("hidden","input_button_flg");
$form->addElement("hidden", "before_state");  //初期表示時の状態値

/***************************/
//Freeze
/***************************/
$anytime_freeze_form = $form->addGroup($anytime_freeze, "anytime_freeze", "");
$anytime_freeze_form->freeze();

/***************************/
//ルール作成（QuickForm）
/***************************/
//■社名
//●必須チェック
$form->addRule("form_comp_name", "社名は1文字以上25文字以下です。","required");
// 全角/半角スペースのみチェック
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_comp_name", "社名 にスペースのみの登録はできません。", "no_sp_name");

//■ショップ名
//●必須チェック
$form->addRule("form_cname", "ショップ名は1文字以上25文字以下です。","required");

//■代表者氏名
//●必須チェック
$form->addRule("form_rep_name", "代表者氏名は1文字以上15文字以下です。","required");

//■郵便番号
//●必須チェック
//●半角数字チェック
//●文字数チェック
$form->addGroupRule('form_post_no', array(
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

//■住所１
//●必須チェック
$form->addRule("form_address1", "住所１は1文字以上25文字以下です。","required");

//■地区
//●必須チェック
$form->addRule("form_area", "地区を選択して下さい。","required");

//■TEL
//●必須チェック
$form->addRule("form_tel", "TELは半角数字と｢-｣のみ30桁以内です。", "required");

#2009-12-25 aoyama-n
//■消費税率
//●半角数字チェック
//$form->addRule("form_tax", "消費税率は半角数字のみです。", "regex", "/^[0-9]+$/");
#$form->addRule("form_tax", "消費税率は半角数字のみです。", "regex", "/^[0-9]+$/");

//■税率切替日
//●半角数字チェック
#$form->addGroupRule('form_tax_rate_day', array(
#        'y' => array(
#                array('税率切替日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
#        ),
#        'm' => array(
#                array('税率切替日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
#        ),
#        'd' => array(
#                array('税率切替日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
#        ),
#));

//■自社締日
//●必須チェック
$form->addRule("form_my_close_day", "自社締日を選択してください。","required");

//■支払日（月）
//●必須チェック
//●半角数字チェック
$form->addRule("form_my_pay_month", "支払日（月）は半角数字のみです。", "required");
$form->addRule("form_my_pay_month", "支払日（月）は半角数字のみです。", "regex", "/^[0-9]+$/");

//■支払日（日）
//●必須チェック
//●半角数字チェック
$form->addRule("form_my_pay_day", "支払日（日）は半角数字のみです。", "required");
$form->addRule("form_my_pay_day", "支払日（日）は半角数字のみです。", "regex", "/^[0-9]+$/");

//■決算月
//●半角数字チェック
$form->addRule("form_cutoff_month", "決算月は半角数字のみです。","regex", "/^[0-9]+$/");

//■決算日
//●半角数字チェック
$form->addRule("form_cutoff_day", "決算日は半角数字のみです。","regex", "/^[0-9]+$/");

//■資本金
//●半角数字チェック
//$form->addRule("form_capital_money", "資本金は半角数字のみです。","regex", "/^[0-9]+$/");
$form->addRule("form_capital_money", "資本金は半角数字のみです。","regex", '/^[0-9]+$/');

//■決算日
//●半角数字チェック
$form->addRule("form_cutoff_day", "決算日は半角数字のみです。","regex", "/^[0-9]+$/");

//■創業日
//●半角数字チェック
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

//■法人登記日
//●半角数字チェック
$form->addGroupRule('form_corpo_day', array(
        'y' => array(
                array('法人登記日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'm' => array(
                array('法人登記日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
        'd' => array(
                array('法人登記日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
        ),
));

//■基本出荷倉庫
//●必須チェック
//$form->addRule("form_ware", "基本出荷倉庫を選択してください。","required");

/****************************/
//登録ボタン押下
/****************************/
if($_POST["button"]["entry_button"] == "登　録"){
    /****************************/
    //POST取得
    /****************************/
    $head_cd1         = $_POST["anytime_freeze"]["form_shop_cd"]["cd1"];          //ショップコード1
    $head_cd2         = $_POST["anytime_freeze"]["form_shop_cd"]["cd2"];          //ショップコード2
    $comp_name        = $_POST["form_comp_name"];               //社名
    $comp_name_read   = $_POST["form_comp_read"];               //社名(フリガナ)
    $comp_name2       = $_POST["form_comp_name2"];              //社名2
    $comp_name_read2  = $_POST["form_comp_read2"];              //社名2(フリガナ)
    $cname            = $_POST["form_cname"];                   //ショップ名
    $cread            = $_POST["form_cread"];                   //ショップ名(フリガナ)
    $represe          = $_POST["form_represe"];                 //代表者役職
    $rep_name         = $_POST["form_rep_name"];                //代表者氏名
    $rep_htel         = $_POST["form_represent_cell"];          //代表者携帯
    $post_no1         = $_POST["form_post_no"]["no1"];          //郵便番号
    $post_no2         = $_POST["form_post_no"]["no2"];          //郵便番号
    $address1         = $_POST["form_address1"];                //住所1
    $address2         = $_POST["form_address2"];                //住所2
    $address3         = $_POST["form_address3"];                //住所3
    $address_read     = $_POST["form_address_read"];            //住所(フリガナ)
    $capital_money    = $_POST["form_capital_money"];           //資本金
    $tel              = $_POST["form_tel"];                     //TEL
    $fax              = $_POST["form_fax"];                     //FAX
    $email            = $_POST["form_email"];                   //Email
    $url              = $_POST["form_url"];                     //URL
    $direct_tel       = $_POST["form_direct_tel"];              //直通TEL
    #2009-12-25 aoyama-n
    $tax_setup_flg    = $_POST["form_tax_setup_flg"];           //新消費税率設定フラグ
    #$tax              = $_POST["form_tax"];                     //消費税率
    $tax_rate_new     = $_POST["form_tax_rate_new"];            //新消費税率

    #2009-12-25 aoyama-n
    #$rate_day         = str_pad($_POST["form_tax_rate_day"]["y"],4,0,STR_PAD_LEFT);       //税率切替日(年)
    #$rate_day        .= "-";
    #$rate_day        .= str_pad($_POST["form_tax_rate_day"]["m"],2,0,STR_PAD_LEFT);       //税率切替日(月)
    #$rate_day        .= "-";
    #$rate_day        .= str_pad($_POST["form_tax_rate_day"]["d"],2,0,STR_PAD_LEFT);       //税率切替日(日)
    $tax_change_day_new  = str_pad($_POST["form_tax_change_day_new"]["y"],4,0,STR_PAD_LEFT);  //新税率切替日(年)
    $tax_change_day_new .= "-";
    $tax_change_day_new .= str_pad($_POST["form_tax_change_day_new"]["m"],2,0,STR_PAD_LEFT);  //新税率切替日(月)
    $tax_change_day_new .= "-";
    $tax_change_day_new .= str_pad($_POST["form_tax_change_day_new"]["d"],2,0,STR_PAD_LEFT);  //新税率切替日(日)

    $my_close_day     = $_POST["form_my_close_day"];            //自社締日
    $my_pay_month     = $_POST["form_my_pay_month"];            //自社支払日(月)
    $my_pay_day       = $_POST["form_my_pay_day"];              //自社支払日(日)
    $cutoff_month     = $_POST["form_cutoff_month"];            //決算月
    $cutoff_day       = $_POST["form_cutoff_day"];              //決算日
    
    $claim_num        = $_POST["form_claim_num"];               //請求書番号設定
    $coax             = $_POST["form_coax"];                    //金額丸め区分
    $franct           = $_POST["from_fraction_div"];            //消費税端数区分
    $area             = $_POST["form_area"];                    //地区
    //$ware             = $_POST["form_ware"];                    //基本出荷倉庫
    $contact_name     = $_POST["form_contact_name"];            //連絡担当者氏名
    $contact_position = $_POST["form_contact_position"];        //連絡担当者役職
    $contact_cell     = $_POST["form_contact_cell"];            //連絡担当者携帯

    $establish_day    = str_pad($_POST["form_establish_day"]["y"],4,0,STR_PAD_LEFT);      //創業日(年)
    $establish_day   .= "-";
    $establish_day   .= str_pad($_POST["form_establish_day"]["m"],2,0,STR_PAD_LEFT);      //創業日(月)
    $establish_day   .= "-";
    $establish_day   .= str_pad($_POST["form_establish_day"]["d"],2,0,STR_PAD_LEFT);      //創業日(日)

    $corpo_day        = str_pad($_POST["form_corpo_day"]["y"],4,0,STR_PAD_LEFT);          //法人登記日(年)
    $corpo_day       .= "-";
    $corpo_day       .= str_pad($_POST["form_corpo_day"]["m"],2,0,STR_PAD_LEFT);          //法人登記日(月)
    $corpo_day       .= "-";
    $corpo_day       .= str_pad($_POST["form_corpo_day"]["d"],2,0,STR_PAD_LEFT);          //法人登記日(日)

    $cal_peri         = $_POST["form_cal_peri"];                //カレンダー表示期間


    /***************************/
    //０埋め
    /***************************/
    //ショップコード１
    $head_cd1 = str_pad($head_cd1, 6, 0, STR_POS_LEFT);

    //ショップコード２
    $head_cd2 = str_pad($head_cd2, 4, 0, STR_POS_LEFT);

    /***************************/
    //ルール作成（PHP）
    /***************************/
    // 直営の場合はチェックしない
    if ($group_kind != "2"){
        //ショップコード
        if($head_cd1 != null && $head_cd2 != null){
            $head_cd_sql  = "SELECT";
            $head_cd_sql  .= " client_id FROM t_client";
            $head_cd_sql  .= " WHERE";
            $head_cd_sql  .= " client_cd1 = '$head_cd1'";
            $head_cd_sql  .= " AND";
            $head_cd_sql  .= " '$head_data[0]' != '$head_cd1'";
            $head_cd_sql  .= " AND";
            $head_cd_sql  .= " client_cd2 = '$head_cd2'";
            $head_cd_sql  .= " AND";
            $head_cd_sql  .= " '$head_data[1]' != '$head_cd2'";
            $head_cd_sql  .= ";";
            $select_shop = Db_Query($db_con, $head_cd_sql);
            $select_shop = pg_num_rows($select_shop);
            if($select_shop != 0){
                $head_cd_err = "入力されたショップコードは使用中です。";
                $err_flg = true;
            }
        }
    }
    
    //■TEL
    //●半角数字と「-」以外はエラー
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$tel)){
        $tel_err = "TELは半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }

    //■FAX
    //●半角数字と「-」以外はエラー
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$fax) && $fax != null){
        $fax_err = "FAXは半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }

    //■Email
    if (!(ereg("^[^@]+@[^.]+\..+", $email)) && $email != "") {
        $email_err = "Emailが妥当ではありません。";
        $err_flg = true;
    }

    //■URL
    //●入力チェック
    if (!preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $url) && $url != null) {
        $url_err = "正しいURLを入力して下さい。";
        $err_flg = true;
    }

    //■直通TEL
    //●半角数字と「-」以外はエラー
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$direct_tel) && $direct_tel != ""){
        $d_tel_err = "直通TELは半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }

    //■連絡担当者携帯
    //●半角数字と「-」以外はエラー
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$contact_cell) && $contact_cell != ""){
        $contact_cell_err = "連絡担当者携帯は半角数字と｢-｣のみ30桁以内です。";
        $err_flg = true;
    }

    //■創業日
    //●日付の妥当性チェック
    $eday_y = (int)$_POST["form_establish_day"]["y"];
    $eday_m = (int)$_POST["form_establish_day"]["m"];
    $eday_d = (int)$_POST["form_establish_day"]["d"];
    
    if($eday_m != null || $eday_d != null || $eday_y != null){
        $eday_flg = true;
    }
    $check_e_day = checkdate($eday_m,$eday_d,$eday_y);
    if($check_e_day == false && $eday_flg == true){
        $eday_err = "創業日の日付は妥当ではありません。";
        $err_flg = true;
    }
    
    //■法人登記日
    //●日付の妥当性チェック
    $cday_y = (int)$_POST["form_corpo_day"]["y"];
    $cday_m = (int)$_POST["form_corpo_day"]["m"];
    $cday_d = (int)$_POST["form_corpo_day"]["d"];
    
    if($cday_m != null || $cday_d != null || $cday_y != null){
        $cday_flg = true;
    }
    $check_c_day = checkdate($cday_m,$cday_d,$cday_y);
    if($check_c_day == false && $cday_flg == true){
        $cday_err = "法人登記日の日付は妥当ではありません。";
        $err_flg = true;
    }
    
    //■税率切替日
    #2009-12-25 aoyama-n
    //●日付の妥当性チェック
    #$rday_y = (int)$_POST["form_tax_rate_day"]["y"];
    #$rday_m = (int)$_POST["form_tax_rate_day"]["m"];
    #$rday_d = (int)$_POST["form_tax_rate_day"]["d"];

    #if($rday_m != null || $rday_d != null || $rday_y != null){
    #    $rday_flg = true;
    #}
    #$check_r_day = checkdate($rday_m,$rday_d,$rday_y);
    #if($check_r_day == false && $rday_flg == true){
    #    $rday_err = "税率切替日の日付は妥当ではありません。";
    #    $err_flg = true;
    //未来の日付かチェック
    #}elseif(date("Ymd",mktime(0,0,0,$rday_m, $rday_d, $rday_y)) <= date("Ymd")){
    #    $rday_err = "税率切替日は未来の日付を指定して下さい。";
    #    $err_flg = true;
    #}

    #2009-12-25 aoyama-n
    #新消費税率を設定するにチェックがある場合
    if($tax_setup_flg == 1){

        //■消費税率
        //●必須チェック
        $form->addRule("form_tax_rate_new", "新消費税率は半角数字のみです。","required");
        //●半角数字チェック
        $form->addRule("form_tax_rate_new", "新消費税率は半角数字のみです。", "regex", '/^[0-9]+$/');

        //■税率切替日
        //●半角数字チェック
        $form->addGroupRule('form_tax_change_day_new', array(
                'y' => array(
                        array('新税率切替日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
                ),
                'm' => array(
                        array('新税率切替日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
                ),
                'd' => array(
                       array('新税率切替日の日付は妥当ではありません。',"regex", "/^[0-9]+$/")
                ),
        ));

        //●日付の妥当性チェック
        $rday_y = (int)$_POST["form_tax_change_day_new"]["y"];
        $rday_m = (int)$_POST["form_tax_change_day_new"]["m"];
        $rday_d = (int)$_POST["form_tax_change_day_new"]["d"];

        if($rday_m != null || $rday_d != null || $rday_y != null){
            //妥当性チェック
            $check_r_day = checkdate($rday_m,$rday_d,$rday_y);
            if($check_r_day == false){
                $rday_err = "新税率切替日の日付は妥当ではありません。";
                $err_flg = true;
            //未来の日付チェック
            }elseif(date("Ymd", mktime(0,0,0,$rday_m, $rday_d, $rday_y)) <= date("Ymd")){
                $rday_err = "新税率切替日は未来の日付を指定して下さい。";
                $err_flg = true;
            }
        }else{
            $rday_err = "新税率切替日の日付は妥当ではありません。";
            $err_flg = true;
        }
    #新消費税率を設定するにチェックが無い場合
    }else{
        #新消費税率を新たに設定する場合
        if($defa_data["form_tax_rate_new"]         == null &&

           #2010-05-31 hashimoto-y バグ修正
           #($_POST["form_tax_change_day_new"]      != null ||
            #$_POST["form_tax_change_day_new"]["y"] != null ||
            ($_POST["form_tax_change_day_new"]["y"] != null ||
            $_POST["form_tax_change_day_new"]["m"] != null ||
            $_POST["form_tax_change_day_new"]["d"] != null)){
           $rday_err = "新消費税率を設定する場合は、チェックボックスにチェックを入れてください。";
           $err_flg = true;
        }
    }

	//■カレンダー表示期間
    //●削除範囲の受注番号がふられている伝票はないか判定
	if($before_state > $cal_peri){
		//カレンダー表示日数
		$cal_day = 31 * ($cal_peri+1);

		//対象期間（開始）取得
		$day_y = date("Y");            
		$day_m = date("m");
		$day_d = date("d");
		$end = mktime(0, 0, 0, $day_m,$day_d+$cal_day,$day_y);
		$end_day = date("Y-m-d",$end);

		//受注番号が振られていない伝票のみ削除
		$sql  = "SELECT "; 
		$sql .= "   t_aorder_h.aord_id ";
		$sql .= "FROM ";
		$sql .= "    t_aorder_h ";
		$sql .= "   INNER JOIN t_aorder_d ON t_aorder_d.aord_id = t_aorder_h.aord_id ";
		$sql .= "WHERE ";
		//直営判定
		if($group_kind == 2){
			//直営
			$sql .= "   t_aorder_h.shop_id IN (".Rank_Sql().") ";
		}else{
			//FC
			$sql .= "   t_aorder_h.shop_id = $client_id ";
		}
		$sql .= " AND ";
		$sql .= "   t_aorder_d.contract_id IS NOT NULL ";
		$sql .= "AND ";
		$sql .= "   t_aorder_h.ord_no IS NOT NULL ";
		$sql .= "AND ";
		$sql .= "   t_aorder_h.ord_time >= '$end_day';";
		$result = Db_Query($db_con,$sql);
	    $check_num = pg_num_rows($result);
        if($check_num != 0){
			$cal_err = "翌日に削除される予定データに、受注番号が振られているデータが存在します。";
        	$err_flg = true;
		}
	}
}

if($_POST["button"]["entry_button"] == "登　録" && $form->validate() && $err_flg != true ){

    //登録完了メッセージ
    $fin_msg = "登録しました。";

    Db_Query($db_con, "BEGIN;");
    
    $update_sql  = "UPDATE \n";
    $update_sql .= "    t_client\n";
    $update_sql .= " SET\n";
    $update_sql .= "    client_cd1 = '$head_cd1',\n";
    $update_sql .= "    client_cd2 = '$head_cd2',\n";
    $update_sql .= "    shop_name = '$comp_name',\n";
    $update_sql .= "    shop_read = '$comp_name_read',\n";
    $update_sql .= "    shop_name2 = '$comp_name2',\n";
    $update_sql .= "    shop_read2 = '$comp_name_read2',\n";
    $update_sql .= "    client_name = '$cname',\n";
    $update_sql .= "    client_read = '$cread',\n";
    $update_sql .= "    represe = '$represe',\n";
    #2009-12-25 aoyama-n
    #$update_sql .= "    tax_rate_c = '$tax',\n";  
    #2009-12-25 aoyama-n
    #if($rate_day == "0000-00-00"){
    #    $update_sql .= "    tax_rate_cday = null,\n"; 
    #}else{
    #    $update_sql .= "    tax_rate_cday = '$rate_day',\n";  
    #}
    #「新消費税率を設定する」にチェックがある場合のみ処理を行う
    if($tax_setup_flg == 1){
        $update_sql .= "    tax_rate_new = '$tax_rate_new',";
        $update_sql .= "    tax_change_day_new = '$tax_change_day_new',";
    }
    $update_sql .= "    cutoff_month = '$cutoff_month',\n";   
    $update_sql .= "    cutoff_day = '$cutoff_day',\n";   
    $update_sql .= "    rep_name = '$rep_name',\n";
    $update_sql .= "    rep_htel = '$rep_htel',\n";
    $update_sql .= "    post_no1 = '$post_no1',\n";
    $update_sql .= "    post_no2 = '$post_no2',\n";
    $update_sql .= "    address1 = '$address1',\n";
    $update_sql .= "    address2 = '$address2',\n";
    $update_sql .= "    address3 = '$address3',\n";
    $update_sql .= "    address_read = '$address_read',\n";
    $update_sql .= "    capital = '$capital_money',\n";
    $update_sql .= "    tel = '$tel',\n";
    $update_sql .= "    fax = '$fax',\n";
    $update_sql .= "    area_id = '$area',\n";
    $update_sql .= "    email = '$email',\n";
    $update_sql .= "    url = '$url',\n";
    $update_sql .= "    direct_tel = '$direct_tel',\n";
    $update_sql .= "    my_close_day = '$my_close_day',\n";
    if($establish_day == "0000-00-00"){
        $update_sql .= "    establish_day = null,\n"; 
    }else{
        $update_sql .= "    establish_day = '$establish_day',\n";
    }
    $update_sql .= "    my_pay_m = '$my_pay_month',\n";
    $update_sql .= "    my_pay_d = '$my_pay_day',\n";
    if($corpo_day == "0000-00-00"){
        $update_sql .= "    regist_day = null,\n";    
    }else{
        $update_sql .= "    regist_day = '$corpo_day',\n";
    }
    $update_sql .= "    charger_name = '$contact_name',\n";
    $update_sql .= "    charger = '$contact_position',\n";
    $update_sql .= "    cha_htel = '$contact_cell',\n";
    //$update_sql .= "    ware_id = '$ware',\n";
    $update_sql .= "    claim_set = '$claim_num',\n";
    $update_sql .= "    my_coax = '$coax',\n";
    $update_sql .= "    my_tax_franct = '$franct',\n";
    $update_sql .= "    cal_peri = '$cal_peri' \n";
    $update_sql .= " WHERE \n";
    $update_sql .= "    client_id = $client_id\n";
    $update_sql .= ";\n";

    $result = Db_Query($db_con, $update_sql);
    if($result === false){
        Db_Query($db_con, "ROLLBACK;");
        exit;
    }
    //更新した情報をログに残す
    $result = Log_Save( $db_con, "client", "2", $head_cd1."-".$head_cd2, $comp_name);
    if($result === false){
        Db_Query($db_con, "ROLLBACK");
        exit;
    }
  
	//直営判定
	if($group_kind == 2){
		//直営なら他の直営のカレンダー表示期間も変更した期間に合わせる
		$sql  = "UPDATE ";
		$sql .= "    t_client ";
		$sql .= "SET ";
		$sql .= "    cal_peri = '$cal_peri' ";
		$sql .= "WHERE ";
		$sql .= "    client_id IN (".Rank_Sql().");";
		$result = Db_Query($db_con, $sql);
	    if($result === false){
	        Db_Query($db_con, "ROLLBACK;");
	        exit;
	    }
	}

	//カレンダー表示期間が変更前より減ったか判定
	if($before_state > $cal_peri){
		$cal_peri_num = $before_state - $cal_peri;
		$cal_peri_num = $cal_peri_num * -1;
		//直営判定
		if($group_kind == 2){
			//直営

			//カレンダ変更期間更新SQL
			$sql  = "UPDATE t_client SET cal_peri_num = $cal_peri_num WHERE client_id IN (".Rank_Sql().");";
		}else{
			//FC

			//カレンダ変更期間更新SQL
			$sql  = "UPDATE t_client SET cal_peri_num = $cal_peri_num WHERE client_id = $client_id;";
		}
		$result = Db_Query($db_con, $sql);
	    if($result === false){
	        Db_Query($db_con, "ROLLBACK;");
	        exit;
	    }
	}
  
	//カレンダー表示期間が変更前より増えたか判定
	if($before_state < $cal_peri){
		$cal_peri_num = $cal_peri - $before_state;
		//直営判定
		if($group_kind == 2){
			//直営

			//カレンダ変更期間更新SQL
			$sql  = "UPDATE t_client SET cal_peri_num = $cal_peri_num WHERE client_id IN (".Rank_Sql().");";
		}else{
			//FC

			//カレンダ変更期間更新SQL
			$sql  = "UPDATE t_client SET cal_peri_num = $cal_peri_num WHERE client_id = $client_id;";
		}
		$result = Db_Query($db_con, $sql);
	    if($result === false){
	        Db_Query($db_con, "ROLLBACK;");
	        exit;
	    }
	}

    #2010-01-10 aoyama-n
    #------------------------------------------
    #新消費税率を設定するにチェックがある場合のみ処理を行う
    if($tax_setup_flg == 1){
        #消費税額クラス　インスタンス生成
        $tax_amount_obj = new TaxAmount();

        #消費税額更新対象の受注IDを取得(新消費税率切替日以降の受注データ)
        $sql  = "SELECT ";
        $sql .= "    t_aorder_h.aord_id ";
        $sql .= "FROM ";
        $sql .= "    t_aorder_h ";
        $sql .= "WHERE ";
        $sql .= "    t_aorder_h.ord_time >= '".$tax_change_day_new."' ";
        $sql .= "AND ";
        $sql .= "    t_aorder_h.shop_id = $client_id;";
        $result = Db_Query($db_con, $sql);
        $aord_id_list = Get_Data($result);

        for($i=0; $i<count($aord_id_list); $i++){
            #受注ヘッダの消費税額計算
            $tax_amount = $tax_amount_obj->getAorderTaxAmount($tax_rate_new, $aord_id_list[$i][0]);

            #受注ヘッダの消費税額更新
            $sql  = "UPDATE t_aorder_h SET ";
            $sql .= "    tax_amount = ".$tax_amount;
            $sql .= "WHERE ";
            $sql .= "    aord_id = ".$aord_id_list[$i][0].";";
            $result = Db_Query($db_con, $sql);
            if($result === false){
	            Db_Query($db_con, "ROLLBACK;");
	            exit;
            }
        }
    }
    #------------------------------------------

    Db_Query($db_con, "COMMIT;");
}
/****************************/
//自動入力ボタン押下
/****************************/
if($_POST["input_button_flg"]==true){
    $post1     = $_POST["form_post_no"]["no1"];             //郵便番号１
    $post2     = $_POST["form_post_no"]["no2"];             //郵便番号２
    $post_value = Post_Get($post1,$post2,$db_con);
    //郵便番号フラグをクリア
    $cons_data["input_button_flg"] = "";
    //郵便番号から自動入力
    $cons_data["form_post_no"]["no1"] = $_POST["form_post_no"]["no1"];
    $cons_data["form_post_no"]["no2"] = $_POST["form_post_no"]["no2"];
    $cons_data["form_address_read"] = $post_value[0];
    $cons_data["form_address1"] = $post_value[1];
    $cons_data["form_address2"] = $post_value[2];

    $form->setConstants($cons_data);
}
/****************************/
//社印削除処理
/****************************/
if($_POST["button"]["delete_stamp"] == "社印の削除"){

    $shain_file = COMPANY_SEAL_DIR.$client_id.".jpg";

    // ファイル存在判定
    if(file_exists($shain_file)){
        // ファイル削除
        $res = unlink($shain_file);
        header("Location: ".$_SERVER[PHP_SELF]." ");
    }
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
$page_menu = Create_Menu_f('system','3');
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
    'head_cd_err'   => "$head_cd_err",
    'tel_err'       => "$tel_err",
    'fax_err'       => "$fax_err",
    'cday_err'      => "$cday_err",
    'eday_err'      => "$eday_err",
	'cal_err'       => "$cal_err",
    'rday_err'      => "$rday_err",
    'attach_gid'    => "$attach_gid",
    'email_err'     => "$email_err",
    'url_err'       => "$url_err",
    'd_tel_err'     => "$d_tel_err",
    'contact_cell_err' => "$contact_cell_err",
    'fin_msg'       => "$fin_msg",
    'path_shain'    => "$path_shain",
));

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
