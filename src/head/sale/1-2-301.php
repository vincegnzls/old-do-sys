<?php
/****************************/
//
// ・ (2006/05/31)新規作成＜watanabe-k＞
//
/****************************/
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/27　04-002　　　　watanabe-ｋ 7月分の請求更新後、8月分の請求書を作成すると「前回請求額」が正しくないバグの修正。            
 * 　2006/10/27　04-004　　　　watanabe-ｋ 7月分の請求更新後、8月分の請求書を作成すると「今月御支払額」が正しくないバグの修正。            
 * 　2006/10/27　04-012　　　　watanabe-ｋ 2006年8月の請求更新後に随時請求で同じ2006年8月の請求書ができてしまうバグの修正 
 * 　2006/10/27　04-013　　　　watanabe-ｋ 2006年8月分の随時請求を請求更新して　2006年9月分を作成しても請求データが作成されないバグの修正            
 * 　2006/10/27　04-017　　　　watanabe-ｋ ロイヤリティの伝票が請求書に含まれていないバグの修正            
 * 　2006/10/27　04-018　　　　watanabe-ｋ 請求書に得意先名が登録されていないバグの修正
 * 　2006/10/27　04-020　　　　watanabe-ｋ ロイヤリティ伝票の得意先名がショップ名になっていたバグの修正         
 * 　2006/10/27　04-021　　　　watanabe-ｋ 倉庫IDが登録されていなかったバグの修正
 * 　2006/10/27　04-022　　　　watanabe-ｋ 請求更新すると対象のデータがありません。と表示されてしまうバグの修正         
 * 　2006/10/27　04-023　　　　watanabe-ｋ 売上金額と消費税金額にロイヤリティが含まれていないバグの修正
 * 　2006/10/27　04-025　　　　watanabe-ｋ 親子関係の無い得意先にも関わらず、親子関係のある請求書が作成される。この現象は３回目の請求から発生する           
 * 　2006/10/27　04-026　　　　watanabe-ｋ 伝票単位の場合、売上金額と消費税額にロイヤリティが含まれていないバグの修正
 * 　2006/11/27　scl-0003　　　watanabe-ｋ ロイヤリティ伝票に得意先名略称を登録するように修正
 * 　2006/12/04　scl-0037　　　watanabe-ｋ ２重サブミット禁止 
 * 　2006/12/04　　　　        watanabe-ｋ 日次更新チェック修正
 * 　2006/01/04　　　　        watanabe-ｋ 随時の場合の日付チェックを変更
 * 　2007/02/07　　　　        watanabe-ｋ 年月分を削除 
 * 　2007/02/13　　　　        watanabe-ｋ 一括消費税の処理を作成 
 * 　2007/03/07　　　　        watanabe-ｋ 伝票単位の時に一括消費税の値をクリアする処理を追加 
 * 　2007/03/20　　　　        watanabe-ｋ 請求書作成時にRtoRの伝票を作成する処理を追加 
 * 　2007/04/04　　　　        watanabe-ｋ 親子関係を残すように修正 
 * 　2007/04/10　　　　        watanabe-ｋ エラーメッセージを表示するように修正 
 * 　2007/04/16　　　　        watanabe-ｋ 請求時に確定フラグをtにするように修正 
 * 　2007/04/17　　　　        watanabe-ｋ 割賦金額を残高に含めないように修正 
 * 　2007/04/19　　　　        watanabe-ｋ 今回御買上残高の計算を　繰越残高＋税込買上額-割賦買上額+今回割賦請求額とする 
 *   2007/05/08                watanabe-k  回収予定額の抽出条件を変更
 *   2007/06/01                watanabe-k  随時の場合の請求書の作成期間を入力された日付までにする
 *   2007/06/18                watanabe-k  随時の場合の締日はマスタの締日になるように修正
 *   2007/07/03                watanabe-k  伝票枚数が０で登録されるバグの修正
 *   2007/07/03                watanabe-k  取引があっても金額が０の場合は請求書を作成しないように修正
 *   2008/02/21                watanabe-k  一括消費税データが発生しないバグの修正
 *   2008/05/31                watanabe-k  ロイヤリティ発生条件をプラスではなく０以外に修正
 *   2009/12/28                aoyama-n    税率をTaxRateクラスから取得
 *   2010/01/20                hashimoto-y 一括消費税登録処理の潜在バグを修正
 *   2011/11/19                aoyama-n    mktime関数使用方法の修正（前月を取得する際に正しい月が取得できないバグ）
 *   2011/12/11                hashimoto-y お支払額抽出バグの修正
 */
$page_title = "請求データ作成";

//環境設定ファイル
require_once("ENV_local.php");

//現モジュール内のみで使用する関数ファイル
require_once(INCLUDE_DIR.(basename($_SERVER[PHP_SELF].".inc")));

//レンタル伝票作成関数ファイル
require_once(INCLUDE_DIR."rental.inc");
require_once(INCLUDE_DIR."function_keiyaku.inc");

//請求読み込みフィル
require_once(INCLUDE_DIR."seikyu.inc");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//外部変数取得
/****************************/
$shop_id = $_SESSION["client_id"];
$staff_id = $_SESSION["staff_id"];
$group_kind= $_SESSION["group_kind"];

unset($_SESSION["get_data"]);
unset($_SESSION["no_sheet_data"]);
unset($_SESSION["renew_data"]);
unset($_SESSION["made_data"]);
/****************************/
//初期値設定
/****************************/
#2009-12-28 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);

$def_data["form_slipout_type"] = '1';
$form->setDefaults($def_data);

/****************************/
// フォームパーツ作成
/****************************/
//発行形式
//$r_value = array("一括発行","随時発行");
$r_value = array("締日一括作成","随時個別作成");
for($i = 0; $i < 2; $i++){
    $radio = "";
    $radio[] =& $form->createElement( "radio",NULL,NULL, $r_value[$i],$i+1,"onClick=\"Text_Disabled($i+1);\"");
    $form->addGroup( $radio, "form_slipout_type[]", "");
}

//■一時発行用
// 請求締日
$sql1  = "SELECT";
$sql1 .= "   DISTINCT close_day";
$sql1 .= " FROM";
$sql1 .= "   t_client";
$sql1 .= " WHERE";
$sql1 .= "   client_div = '3'";
$sql1 .= "   AND";
$sql1 .= "   shop_id = $shop_id";
$sql1 .= ";";

$result = Db_Query($db_con, $sql1);
$num = pg_num_rows($result);
$select_value[] = null;
for($i = 0; $i < $num; $i++){
    $client_close_day[] = pg_fetch_result($result, $i,0);
}

asort($client_close_day);
$client_close_day = array_values($client_close_day);

for($i = 0; $i < $num; $i++){
    if($client_close_day[$i] < 29 && $client_close_day[$i] != ''){
        $select_value[$client_close_day[$i]] = $client_close_day[$i]."日";
    }elseif($client_close_day[$i] != '' && $client_close_day[$i] >= 29){
        $select_value[$client_close_day[$i]] = "月末";
    }
}

$form_claim_day1[] =& $form->createElement(
    "text", "y", "", "size=\"4\" maxLength=\"4\" 
    style=\"$g_form_style\" 
    onkeyup=\"changeText(this.form,'form_claim_day1[y]','form_claim_day1[m]',4)\" 
    onFocus=\"onForm_today2(this,this.form,'form_claim_day1[y]','form_claim_day1[m]')\"
    onBlur=\"blurForm(this)\""
);
$form_claim_day1[] =& $form->createElement(
        "static","","","年"
        );    
$form_claim_day1[] =& $form->createElement(
    "text", "m", "", "size=\"1\" maxLength=\"2\" 
    style=\"$g_form_style\"  
    onkeyup=\"changeText(this.form,'form_claim_day1[m]','form_claim_day1[d]',4)\" 
    onFocus=\"onForm_today2(this,this.form,'form_claim_day1[y]','form_claim_day1[m]')\"
    onBlur=\"blurForm(this)\"");
$form_claim_day1[] =& $form->createElement(
        "static","","","月"
        );    
$form_claim_day1[] =& $form->createElement("select", "d", "", $select_value);
$form->addGroup( $form_claim_day1, "form_claim_day1", "請求締日","");

//随時発行用
// 請求締日
$form_claim_day2[] =& $form->createElement(
    "text", "y", "", "size=\"4\" maxLength=\"4\" 
    style=\"$g_form_style\" 
    onkeyup=\"changeText(this.form,'form_claim_day2[y]','form_claim_day2[m]',4)\" 
    onFocus=\"onForm_today2(this,this.form,'form_claim_day2[y]','form_claim_day2[m]')\"
     onBlur=\"blurForm(this)\""
);
$form_claim_day2[] =& $form->createElement(
        "static","","","年"
        );    
$form_claim_day2[] =& $form->createElement(
    "text", "m", "", "size=\"1\" maxLength=\"2\" 
    style=\"$g_form_style\"  
    onkeyup=\"changeText(this.form,'form_claim_day2[m]','form_claim_day2[d]',2)\" 
    onFocus=\"onForm_today2(this,this.form,'form_claim_day2[y]','form_claim_day2[m]')\" 
    onBlur=\"blurForm(this)\"");
$form_claim_day2[] =& $form->createElement(
        "static","","","月"
        );    
$form_claim_day2[] =& $form->createElement(
    "text", "d", "", "size=\"3\" maxLength=\"2\" class=\"nborder\" readonly
");

//$form_claim_day2[] =& $form->createElement(
//        "static","","","日"
//        );    

$form->addGroup( $form_claim_day2, "form_claim_day2", "請求締日","");


//請求先
$form->addElement(
        "link", "form_claim_link", "", "#", "請求先", 
        "onClick=\"javascript:return Open_SubWin('../dialog/1-0-250.php',Array('form_claim[cd1]', 'form_claim[cd2]', 'form_claim[name]', 'form_claim_day2[d]'), 500, 450,3,1)\"
");

$form_claim[] =& $form->createElement(
        "text","cd1","","size=\"7\" maxLength=\"6\"  style=\"$g_form_style\"
        onkeyup=\"javascript:Claim_Data_Set('form_claim[cd1]','form_claim[cd2]','form_claim[name]', 'form_claim_day2[d]');
        changeText(this.form,'form_claim[cd1]', 'form_claim[cd2]', 6)\"
        ".$g_form_option."\""
);
$form_claim[] =& $form->createElement(
        "static","","","-"
);      
$form_claim[] =& $form->createElement(
        "text","cd2","","size=\"4\" maxLength=\"4\"  style=\"$g_form_style\"
        onKeyUp=\"javascript:Claim_Data_Set('form_claim[cd1]','form_claim[cd2]','form_claim[name]','form_claim_day2[d]')\"
        ".$g_form_option."\""
);
$form_claim[] =& $form->createElement(
        "text","name","","size=\"34\" 
        $g_text_readonly"
);      
$form->addGroup( $form_claim, "form_claim", "");

$code_value = Create_Claim_Close_Day_Js($db_con);

$form->addElement(
    "button", 
    "form_create_button", 
    "作　成", 
    "onClick=\"Dialog_Double_Post_Prevent('form_create_button', 'hdn_button_flg', 'true', '作成します。')\" $disabled"
);
$form->addElement("hidden", "hdn_button_flg");

//指定された請求先と結びつく得意先のデータを抽出
$sql  = "SELECT\n";
$sql .= "   t_client.client_id,\n";
$sql .= "   t_claim.claim_div,\n";
$sql .= "   COALESCE(MAX(t_bill_d.bill_close_day_this), '".START_DAY."') AS client_close_day \n";
$sql .= "FROM\n";
$sql .= "   t_claim\n";
$sql .= "       INNER JOIN\n";
$sql .= "   t_client\n";
$sql .= "   ON t_claim.client_id = t_client.client_id\n";
$sql .= "       LEFT JOIN\n";
$sql .= "   t_bill_d\n";
$sql .= "   ON t_client.client_id = t_bill_d.client_id\n";
$sql .= "WHERE\n";
$sql .= "   t_claim.claim_id = $1 \n";
//$sql .= "   AND\n";
//$sql .= "   t_client.state = '1' \n";
$sql .= "GROUP BY t_client.client_id, t_claim.claim_div, t_client.client_cd1, t_client.client_cd2\n";
$sql .= "ORDER BY t_client.client_cd1, t_client.client_cd2";
$sql .= ";\n";

$sql = "PREPARE close_day_data(int) AS $sql ";
Db_Query($db_con, $sql);



/**********************************************************/
//PREPEAR用のSQLを作成
//指定された請求先と結びつく得意先のデータを抽出
//■得意先情報抽出
//■前回の請求データ抽出
//■今回入金額抽出
//■今回買上額抽出(得意先の課税単位が伝票単位の場合に使用)
//■今回買上額抽出(得意先の課税単位が締日単位の場合に使用)
//■分割請求額抽出
//■分割請求残高抽出
/**********************************************************/
$sql  = "SELECT \n";
$sql .= "   t_client.client_id, \n";                                                        //[5-1]取引先ID
$sql .= "   t_client.client_cd1,\n";                                                        //[5-2]取引先コード１
$sql .= "   t_client.client_cd2,\n";                                                        //[5-3]取引先コード２
$sql .= "   t_client.shop_name,\n";                                                         //[5-4]ショップ名
$sql .= "   t_client.shop_name2,\n";                                                        //[5-5]ショップ名２
$sql .= "   t_client.client_cname,\n";                                                      //[5-6]略称
$sql .= "   t_client.c_tax_div,\n";                                                         //[5-7]課税区分
$sql .= "   t_client.tax_franct,\n";                                                        //[5-8]消費税（端数）
$sql .= "   t_client.claim_out,\n";                                                         //[5-9]請求書形式
$sql .= "   t_claim.claim_div,\n";                                                          //[5-10]請求先区分
$sql .= "   t_client.royalty_rate,\n";                                                      //[5-11]ロイヤリティ（％）
$sql .= "   t_client.coax,\n";                                                              //[5-12]金額（まるめ区分）
$sql .= "   t_client.close_day,\n";                                                         //[5-13]締日
$sql .= "   last_claim_data.bill_close_day_last,\n";                                        //[6-1]請求締日　　AS　前回請求締日
$sql .= "   t_client.post_no1,\n";
$sql .= "   t_client.post_no2,\n";
$sql .= "   t_client.address1,\n";
$sql .= "   t_client.address2,\n";
$sql .= "   t_client.address3,\n";
$sql .= "   t_client.slip_out,\n";
$sql .= "   t_client.ware_id,\n";
$sql .= "   COALESCE(last_claim_data.bill_amount_last,0) AS bill_amount_last,\n";           //[6-2]今回請求額　AS　前回請求額
$sql .= "   COALESCE(last_claim_data.payment_last,0) AS payment_last,\n";                   //[6-3]今回支払額　AS　前回支払額
$sql .= "   last_claim_data.payment_extraction_s,\n";                                       //[6-4]支払予定額抽出期間（終了）AS　支払予定額抽出期間（開始）
//※取引区分は全て　かつ入金ヘッダの売上IDがNULLのデータ
$sql .= "   COALESCE(money_received_data1.pay_amount, 0) AS pay_amount,\n";                 //[7-1]SUM（金額）　AS　今回入金額
//※取引区分は調整額のみ
$sql .= "   COALESCE(money_received_data2.tune_amount, 0) AS tune_amount, \n";              //[7-2]SUM（金額）　AS　調整額
//・掛取引の+とマイナスの区分により抽出
$sql .= "   COALESCE(sale_data1.receivable_net_amount, 0) AS receivable_net_amount, \n";    //[8-1]SUM (売上金額（税抜）)
$sql .= "   COALESCE(sale_data1.receivable_tax_amount, 0) AS receivable_tax_amount,\n";     //[8-2]SUM（消費税額）
$sql .= "   COALESCE(sale_data2.taxation_amount, 0) AS taxation_amount,\n";                 //[9-1]SUM (売上金額)　※課税区分＝課税
$sql .= "   COALESCE(sale_data2.notax_amount, 0) AS notax_amount,\n";                       //[9-2]SUM (売上金額)　※課税区分＝非課税
//・割賦売上でデータを抽出
$sql .= "   COALESCE(sale_data3.installment_net_amount, 0) AS installment_net_amount, \n";  //[8-4]SUM（売上金額（税抜））AS　割賦売上の売上金額(税抜)
$sql .= "   COALESCE(sale_data3.installment_tax_amount, 0) AS installment_tax_amount,\n";   //[8-5]SUM（消費税額） AS　割賦売上の消費税額
$sql .= "   royalty_data.royalty_sale_intax_amount,\n";                                     //[]ロイヤリティ対象売上合計（課税）
$sql .= "   royalty_data.royalty_sale_notax_amount,\n";                                     //[]ロイヤリティ対象売上合計（非課税）
$sql .= "   COALESCE(sale_data1.sales_slip_num,0) + COALESCE(sale_data3.sales_slip_num,0) AS sales_slip_num \n";    //[8-3]+[8-6]伝票枚数
$sql .= "FROM\n";
$sql .= "   t_client\n";
$sql .= "        INNER JOIN\n";
$sql .= "   t_claim\n";
$sql .= "   ON t_client.client_id = t_claim.client_id \n";
$sql .= "   AND\n";
$sql .= "   t_claim.claim_id = $1 \n";
$sql .= "   AND\n";
$sql .= "   t_client.client_id = $6\n";
$sql .= "       LEFT JOIN\n";

//-----------------------------------------------------------------------------------------
//前回の請求データ
//-----------------------------------------------------------------------------------------
$sql .= "   (SELECT \n";
$sql .= "       MAX(t_bill_d.bill_d_id), \n";                               
$sql .= "       t_bill_d.client_id, \n";                                    
$sql .= "       t_bill_d.bill_close_day_this AS bill_close_day_last, \n";   //前回請求締日
$sql .= "       t_bill_d.bill_amount_this AS bill_amount_last,\n";          //前回請求額
//$sql .= "       t_bill_d.payment_this AS payment_last,\n";                  //前回御支払額

//前回のデータの残高が既に０の場合は前回お支払額は０
$sql .= "       CASE t_bill_d.collect_bill_d_id ";
$sql .= "           WHEN 0 THEN 0 ";
$sql .= "           ELSE t_bill_d.payment_this ";
$sql .= "       END AS payment_last, ";


$sql .= "       t_bill_d.payment_extraction_e AS payment_extraction_s\n";   //支払予定額抽出期間（開始）
$sql .= "   FROM \n";
$sql .= "       t_bill_d \n";
$sql .= "   WHERE \n";
$sql .= "       t_bill_d.claim_div = (SELECT\n";
$sql .= "                               claim_div\n";
$sql .= "                           FROM\n";
$sql .= "                               t_claim\n";
$sql .= "                           WHERE\n";
$sql .= "                               client_id = t_bill_d.client_id\n";
$sql .= "                               AND\n";
$sql .= "                               claim_id = $1\n";
$sql .= "                           )\n";
$sql .= "       AND\n";
$sql .= "       t_bill_d.bill_close_day_this = $4\n";
$sql .= "       AND\n";
$sql .= "       t_bill_d.client_id = $6\n";
$sql .= "   GROUP BY\n";
$sql .= "       t_bill_d.client_id,\n";
$sql .= "       t_bill_d.bill_close_day_this,\n";
$sql .= "       t_bill_d.bill_amount_this,\n";
$sql .= "       t_bill_d.payment_this,\n";
$sql .= "       t_bill_d.payment_extraction_e,\n";
$sql .= "       t_bill_d.collect_bill_d_id ";
$sql .= "   ) AS last_claim_data\n";
$sql .= "   ON t_client.client_id = last_claim_data.client_id\n";
$sql .= "       LEFT JOIN\n";
//-----------------------------------------------------------------------------------------
//今回入金額
//-----------------------------------------------------------------------------------------
$sql .= "   (SELECT \n";
$sql .= "       t_payin_h.client_id,\n";
$sql .= "       COALESCE(SUM(t_payin_d.amount),0) AS pay_amount\n";
$sql .= "   FROM\n";
$sql .= "       t_payin_h\n";
$sql .= "           INNER JOIN\n";
$sql .= "       t_payin_d \n";
$sql .= "       ON t_payin_h.pay_id = t_payin_d.pay_id\n";
$sql .= "   WHERE\n";
$sql .= "       t_payin_h.pay_day > $4\n";
$sql .= "       AND \n";
$sql .= "       t_payin_h.pay_day <= $2 \n";
$sql .= "       AND \n";
$sql .= "       t_payin_h.claim_div = (SELECT\n";
$sql .= "                               claim_div\n";
$sql .= "                            FROM\n";
$sql .= "                                t_claim\n";
$sql .= "                            WHERE\n";
$sql .= "                                client_id = t_payin_h.client_id\n";
$sql .= "                                AND\n";
$sql .= "                                claim_id = $1\n";
$sql .= "                           )\n";
$sql .= "       AND \n";
$sql .= "       t_payin_h.client_id = $6\n";
$sql .= "       AND \n";
$sql .= "       t_payin_h.sale_id IS NULL\n";
$sql .= "   GROUP BY t_payin_h.client_id\n";
$sql .= "   ) AS money_received_data1 \n";
$sql .= "   ON t_client.client_id = money_received_data1.client_id\n";
$sql .= "       LEFT JOIN\n";
//-----------------------------------------------------------------------------------------
//調整額
//-----------------------------------------------------------------------------------------
$sql .= "   (SELECT \n";
$sql .= "       t_payin_h.client_id,\n";
$sql .= "       COALESCE(SUM(t_payin_d.amount),0) AS tune_amount\n";
$sql .= "   FROM\n";
$sql .= "       t_payin_h\n";
$sql .= "           INNER JOIN\n";
$sql .= "       t_payin_d \n";
$sql .= "       ON t_payin_h.pay_id = t_payin_d.pay_id\n";
$sql .= "   WHERE\n";
$sql .= "       t_payin_h.pay_day > $4\n";
$sql .= "       AND \n";
$sql .= "       t_payin_h.pay_day <= $2 \n";
$sql .= "       AND \n";
$sql .= "       t_payin_h.claim_div = (SELECT\n";
$sql .= "                               claim_div\n";
$sql .= "                           FROM\n";
$sql .= "                               t_claim\n";
$sql .= "                           WHERE\n";
$sql .= "                               client_id = t_payin_h.client_id\n";
$sql .= "                               AND\n";
$sql .= "                               claim_id = $1\n";
$sql .= "                           )\n";
$sql .= "       AND \n";
$sql .= "       t_payin_h.client_id = $6\n";
$sql .= "       AND\n";
$sql .= "       t_payin_d.trade_id = 38 \n";
$sql .= "   GROUP BY t_payin_h.client_id\n";
$sql .= "   ) AS money_received_data2\n";
$sql .= "   ON t_client.client_id = money_received_data2.client_id\n";
$sql .= "       LEFT JOIN\n";
//-----------------------------------------------------------------------------------------
//今回買上額（掛取引）
//-----------------------------------------------------------------------------------------
            //-----------------------------------------------------------------------------
            //税抜金額合計・消費税合計
            //-----------------------------------------------------------------------------
$sql .= "   (SELECT\n";
$sql .= "       t_sale_h.client_id,\n";
$sql .= "       COALESCE(SUM(t_sale_h.net_amount * CASE WHEN t_sale_h.trade_id=11 THEN 1 ELSE -1 END),0) AS receivable_net_amount, \n";
$sql .= "       COALESCE(SUM(t_sale_h.tax_amount * CASE WHEN t_sale_h.trade_id=11 THEN 1 ELSE -1 END),0) AS receivable_tax_amount, \n";
$sql .= "       COALESCE(COUNT(t_sale_h.sale_id),0) AS sales_slip_num \n";
$sql .= "   FROM\n";
$sql .= "       t_sale_h\n";
$sql .= "   WHERE\n";
$sql .= "       t_sale_h.claim_day > $4\n"; 
$sql .= "       AND \n";
$sql .= "       t_sale_h.claim_day <= $2 \n";
$sql .= "       AND \n";
$sql .= "       t_sale_h.claim_div = (SELECT\n";
$sql .= "                                claim_div\n";
$sql .= "                           FROM\n";
$sql .= "                               t_claim";
$sql .= "                           WHERE\n";
$sql .= "                               client_id = t_sale_h.client_id\n";
$sql .= "                               AND\n";
$sql .= "                               claim_id = $1\n";
$sql .= "                           )\n";
$sql .= "       AND \n";
$sql .= "       t_sale_h.client_id = $6\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.trade_id IN (11,13,14) \n";
$sql .= "       GROUP BY t_sale_h.client_id \n";
$sql .= "   ) AS sale_data1 \n";
$sql .= "   ON t_client.client_id = sale_data1.client_id\n";
$sql .= "       LEFT JOIN\n";
            //-----------------------------------------------------------------------------
            //課税金額合計・非課税金額合計
            //-----------------------------------------------------------------------------
$sql .= "   (SELECT\n";
$sql .= "       t_sale_h.client_id,\n";
$sql .= "       COALESCE(\n";
$sql .= "           SUM(\n";
$sql .= "               CASE\n";
$sql .= "                   WHEN t_sale_d.tax_div = '1' THEN t_sale_d.sale_amount * \n";
$sql .= "                   CASE\n";
$sql .= "                       WHEN t_sale_h.trade_id = 11 THEN 1\n";
$sql .= "                       ELSE -1\n";
$sql .= "                   END\n";
$sql .= "               END\n";
$sql .= "           )\n";
$sql .= "       ,0) AS taxation_amount,\n";                 
$sql .= "       COALESCE(\n";
$sql .= "           SUM(\n";
$sql .= "               CASE\n";
$sql .= "                   WHEN t_sale_d.tax_div = '3' THEN t_sale_d.sale_amount *\n";
$sql .= "                       CASE\n";
$sql .= "                           WHEN t_sale_h.trade_id = 11 THEN 1 \n";
$sql .= "                           ELSE -1\n";
$sql .= "                       END\n";
$sql .= "                   END\n";
$sql .= "               )\n";
$sql .= "           ,0) AS notax_amount\n";
$sql .= "   FROM\n";
$sql .= "       t_sale_h\n";
$sql .= "           INNER JOIN\n";
$sql .= "       t_sale_d\n";
$sql .= "       ON t_sale_h.sale_id = t_sale_d.sale_id\n";
$sql .= "   WHERE\n";
$sql .= "       t_sale_h.claim_day > $4";
$sql .= "       AND\n";
$sql .= "       t_sale_h.claim_day <= $2\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.claim_div = (SELECT\n";
$sql .= "                               claim_div\n";
$sql .= "                           FROM\n";
$sql .= "                               t_claim\n";
$sql .= "                           WHERE\n";
$sql .= "                               client_id = t_sale_h.client_id\n";
$sql .= "                               AND\n";
$sql .= "                               claim_id = $1\n";
$sql .= "                           )\n";
$sql .= "       AND \n";
$sql .= "       t_sale_h.client_id = $6\n";
$sql .= "       AND \n";
$sql .= "       t_sale_h.trade_id IN (11,13,14) \n";
$sql .= "   GROUP BY t_sale_h.client_id \n";
$sql .= "   ) AS sale_data2\n";
$sql .= "   ON t_client.client_id = sale_data2.client_id\n";
$sql .= "       LEFT JOIN\n";
//-----------------------------------------------------------------------------------------
//今回買上額（割賦売上）
//-----------------------------------------------------------------------------------------
$sql .= "    (SELECT\n";
$sql .= "       t_sale_h.client_id,\n";
$sql .= "       COALESCE(SUM(t_sale_h.net_amount),0) AS installment_net_amount,\n";
$sql .= "       COALESCE(SUM(t_sale_h.tax_amount),0) AS installment_tax_amount,\n";
$sql .= "       COALESCE(COUNT(t_sale_h.sale_id),0) AS sales_slip_num \n";
$sql .= "   FROM\n";
$sql .= "       t_sale_h\n";
$sql .= "   WHERE\n";
$sql .= "       t_sale_h.claim_day > $4\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.claim_day <= $2\n";
$sql .= "       AND \n";
$sql .= "       t_sale_h.claim_div = (SELECT\n";
$sql .= "                               claim_div\n";
$sql .= "                           FROM\n";
$sql .= "                               t_claim\n";
$sql .= "                           WHERE\n";
$sql .= "                               client_id = t_sale_h.client_id AND claim_id = $1\n";
$sql .= "                           )\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.client_id = $6\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.trade_id = 15\n";
$sql .= "   GROUP BY t_sale_h.client_id \n";
$sql .= "   ) AS sale_data3 \n";
$sql .= "   ON t_client.client_id = sale_data3.client_id\n";
$sql .= "       LEFT JOIN\n";

//-----------------------------------------------------------------------------------------
//ロイヤリティ
//-----------------------------------------------------------------------------------------
$sql .= "   (SELECT\n"; 
$sql .= "       t_sale_h.client_id,\n";
$sql .= "       COALESCE(\n";
$sql .= "           SUM(\n";
$sql .= "               CASE\n";
$sql .= "                   WHEN t_sale_d.tax_div = '1' THEN t_sale_d.sale_amount *\n";
$sql .= "                       CASE\n";
$sql .= "                           WHEN t_sale_h.trade_id = 11 OR t_sale_h.trade_id = 15 OR t_sale_h.trade_id = 61 THEN 1\n";
$sql .= "                           ELSE -1\n";
$sql .= "                       END\n";
$sql .= "                    END\n";
$sql .= "               )\n";
$sql .= "          ,0) AS royalty_sale_intax_amount,\n";
$sql .= "       COALESCE(\n";
$sql .= "           SUM(\n";
$sql .= "               CASE\n";
$sql .= "                   WHEN t_sale_d.tax_div = '3' THEN t_sale_d.sale_amount *\n";
$sql .= "                       CASE\n";
$sql .= "                           WHEN t_sale_h.trade_id = 11 OR t_sale_h.trade_id = 15 THEN 1\n";
$sql .= "                           ELSE -1\n";
$sql .= "                       END\n";
$sql .= "                   END\n";
$sql .= "               )\n";
$sql .= "           ,0) AS royalty_sale_notax_amount\n";
$sql .= "   FROM\n";
$sql .= "       t_sale_h\n";
$sql .= "           INNER JOIN\n";
$sql .= "       t_sale_d\n";
$sql .= "       ON t_sale_h.sale_id = t_sale_d.sale_id\n";
$sql .= "   WHERE\n";
$sql .= "       t_sale_h.claim_day > $4\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.claim_day <= $2\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.claim_div = (SELECT\n";
$sql .= "                               claim_div\n";
$sql .= "                            FROM\n";
$sql .= "                               t_claim\n";
$sql .= "                            WHERE\n";
$sql .= "                               client_id = t_sale_h.client_id\n";
$sql .= "                                AND\n";
$sql .= "                                claim_id = $1\n";
$sql .= "                           )\n";
$sql .= "       AND\n";
$sql .= "       t_sale_h.trade_id IN (11,13,14,15,61,63,64)\n";
$sql .= "       AND\n";
$sql .= "       t_sale_d.royalty = '1'\n";
$sql .= "   GROUP BY t_sale_h.client_id\n";
$sql .= "   ) AS royalty_data\n";
$sql .= "   ON t_client.client_id = royalty_data.client_id\n";
$sql .= "ORDER BY";
$sql .= "   t_client.client_cd1, t_client.client_cd2 \n";
$sql .= ";\n";

$sql = "PREPARE claim_data(int, date, int, date, varchar, int) AS $sql ";
Db_Query($db_con, $sql);

//■請求先情報抽出        
$sql  = "SELECT \n";        
$sql .= "   t_client.close_day, \n";                //[1-1] 締日
$sql .= "   t_client.pay_m, \n";                    //[1-2] 支払日（月）
$sql .= "   t_client.pay_d, \n";                    //[1-3] 支払日（日）
$sql .= "   t_client.client_cd1, \n";               //[1-4] 取引先コード1
$sql .= "   t_client.client_cd2, \n";               //[1-5] 取引先コード2
$sql .= "   t_client.client_name, \n";              //[1-6] 取引先1
$sql .= "   t_client.client_name2, \n";             //[1-7] 取引先2        
$sql .= "   t_client.client_cname, \n";             //[1-8] 略称        
$sql .= "   t_client.compellation, \n";             //[1-9] 敬称
$sql .= "   t_client.post_no1, \n";                 //[1-10] 郵便番号1
$sql .= "   t_client.post_no2, \n";                 //[1-11] 郵便番号2
$sql .= "   t_client.address1, \n";                 //[1-12] 住所1
$sql .= "   t_client.address2, \n";                 //[1-13] 住所2
$sql .= "   t_client.address3, \n";                 //[1-14] 住所3
$sql .= "   t_claim_sheet.c_memo1, \n";             //[1-15] 請求書コメント1
$sql .= "   t_claim_sheet.c_memo2, \n";             //[1-16] 請求書コメント2
$sql .= "   t_claim_sheet.c_memo3, \n";             //[1-17] 請求書コメント3
$sql .= "   t_claim_sheet.c_memo4, \n";             //[1-18] 請求書コメント4
$sql .= "   t_claim_sheet.c_memo5, \n";             //[1-19] 請求書コメント5
$sql .= "   t_claim_sheet.c_memo6, \n";             //[1-20] 請求書コメント6
$sql .= "   t_claim_sheet.c_memo7, \n";             //[1-21] 請求書コメント7
$sql .= "   t_claim_sheet.c_memo8, \n";             //[1-22] 請求書コメント8
$sql .= "   t_claim_sheet.c_memo9, \n";             //[1-23] 請求書コメント9
$sql .= "   t_claim_sheet.c_memo10, \n";            //[1-24] 請求書コメント10
$sql .= "   t_claim_sheet.c_memo11, \n";            //[1-25] 請求書コメント11
$sql .= "   t_claim_sheet.c_memo12, \n";            //[1-26] 請求書コメント12
$sql .= "   t_claim_sheet.c_memo13, \n";            //[1-27] 請求書コメント13
$sql .= "   t_client.claim_send, \n";               //[1-28] 請求書送付
$sql .= "   t_client.tax_div, \n";                  //[1-29] 消費税（課税単位）
$sql .= "   t_client.c_tax_div, \n";                //[1-30] 課税区分
$sql .= "   t_client.tax_franct, \n";               //[1-31] 消費税（端数）
$sql .= "   t_client.coax, \n";                     //[1-32] 金額（まるめ区分）
$sql .= "   t_client.pay_way, \n";                  //[1-33] 集金方法
$sql .= "   t_staff.charge_cd, \n";                 //[1-34] 担当者コード（契約担当者1）
$sql .= "   t_staff.staff_name, \n";                //[1-35] スタッフ名（契約担当1）
//入力された請求締日の1ヶ月後の締日　　　　　　　　　※締日が月末の場合を考慮し、妥当な日付を抽出
$sql .= "   CASE WHEN pay_d = 29 THEN \n";        //[1-36] 回収予定日
$sql .= "       SUBSTR(TO_DATE(SUBSTR($1, 1, 8) || 01, 'YYYY-MM-DD')\n";
$sql .= "           +\n";
$sql .= "       ((CAST(pay_m AS int) + 1) * interval '1 month') - interval '1 day', 1, 10)\n";
$sql .= "   ELSE\n";
$sql .= "       SUBSTR(TO_DATE($1, 'YYYY-MM-DD') + (CAST(pay_m AS int) * interval '1 month'), 1, 8)\n";
$sql .= "           || \n";
$sql .= "   LPAD(pay_d, 2, 0) \n";
$sql .= "   END AS next_close_day, \n";
//入力された請求締日から支払日（月）後の支払日（日）　※支払日（日）が月末の場合を考慮し、妥当な日付を抽出
$sql .= "   CASE WHEN close_day = 29 THEN \n";    //[1-37] 支払予定額抽出期間（終了）
$sql .= "       SUBSTR(TO_DATE(SUBSTR($1, 1, 8) || 01, 'YYYY-MM-DD') \n";
$sql .= "           + interval '2 month' - interval '1 day', 1, 10) \n";
$sql .= "       ELSE \n";
$sql .= "       SUBSTR(TO_DATE($1, 'YYYY-MM-DD') + interval '1 month', 1, 8) \n";
$sql .= "           ||\n";
$sql .= "       LPAD(close_day, 2, 0) \n";
$sql .= "   END AS after_pay_d,\n";
$sql .= "   t_claim_sheet.c_fsize1, \n";            //[1-27] 請求書コメント13
$sql .= "   t_claim_sheet.c_fsize2, \n";            //[1-27] 請求書コメント13
$sql .= "   t_claim_sheet.c_fsize3, \n";            //[1-27] 請求書コメント13
$sql .= "   t_claim_sheet.c_fsize4, \n";            //[1-27] 請求書コメント13
$sql .= "   t_client.claim_out, \n";
$sql .= "   COALESCE(t_sys_renew.renew_close_day, '".START_DAY."') AS renew_day \n";
$sql .= "FROM \n";
$sql .= "   t_claim_sheet \n";
$sql .= "       INNER JOIN \n";
$sql .= "   t_client \n";
$sql .= "   ON t_claim_sheet.shop_id = $shop_id\n";
$sql .= "   AND\n";
$sql .= "   t_client.c_pattern_id = t_claim_sheet.c_pattern_id\n";
$sql .= "   AND\n";
$sql .= "   t_client.client_id = $2\n";
$sql .= "       LEFT JOIN \n";
$sql .= "   t_staff\n";
$sql .= "   ON t_client.c_staff_id1 = t_staff.staff_id \n";
//$sql .= "WHERE\n";
/*追加分*/
$sql .= "       LEFT JOIN \n";
$sql .= "   (SELECT\n";
$sql .= "       claim_id,\n";
$sql .= "       MAX(close_day) AS renew_close_day \n";
$sql .= "   FROM\n";
$sql .= "       t_bill_renew\n";
$sql .= "   GROUP BY claim_id\n";
$sql .= "    ) AS t_sys_renew\n";
$sql .= "   ON t_client.client_id = t_sys_renew.claim_id\n";
$sql .= "; \n";
$sql = "PREPARE get_claim_data(date, int) AS $sql ";

Db_Query($db_con, $sql);

//指定した請求期間内に日次更新していない売上または入金データを抽出
//入金テーブルの更新フラグが'ｆ'のデータを抽出
$sql  = "SELECT\n";
$sql .= "   pay_no,\n";
$sql .= "   '1' AS slip_div \n";
$sql .= " FROM\n";
$sql .= "   t_client";
$sql .= "       INNER JOIN ";
$sql .= "   t_claim";
$sql .= "   ON t_client.client_id = t_claim.client_id";
$sql .= "   AND t_claim.claim_id = $1";
$sql .= "       INNER  JOIN ";
$sql .= "   t_payin_h\n";
$sql .= "   ON t_claim.client_id = t_payin_h.client_id";
$sql .= " WHERE\n";
$sql .= "   t_payin_h.pay_day >\n";
$sql .= "       (\n";
$sql .= "           SELECT\n";                                  //指定された請求先にひもづく
$sql .= "               COALESCE(MAX(t_bill_d.bill_close_day_this), '".START_DAY."')\n";
$sql .= "           FROM\n";
$sql .= "               t_bill\n";
$sql .= "                   INNER JOIN\n";
$sql .= "               t_bill_d\n";
$sql .= "               ON t_bill.bill_id = t_bill_d.bill_id\n";
$sql .= "           WHERE\n";
$sql .= "               t_bill_d.client_id = t_payin_h.client_id\n";
$sql .= "               AND \n";
$sql .= "               t_bill_d.claim_div = t_payin_h.claim_div\n";
$sql .= "       )\n";
$sql .= "   AND\n";
$sql .= "   t_payin_h.pay_day <= $2\n";           //指定された請求締日
$sql .= "   AND\n";
$sql .= "   t_payin_h.shop_id = $shop_id\n";
$sql .= "   AND\n";
$sql .= "   t_payin_h.renew_flg = 'f'\n";

$sql .= " UNION ALL \n";
//売上ヘッダテーブルの更新フラグが'f'のデータを抽出
$sql .= "SELECT\n";
$sql .= "   t_sale_h.sale_no,\n";
$sql .= "   '2' AS slip_div \n";
$sql .= " FROM\n";
$sql .= "   t_sale_h\n";
$sql .= " WHERE\n";
$sql .= "   t_sale_h.claim_id = $1\n";              //指定した請求先ID
$sql .= "   AND\n";
$sql .= "   t_sale_h.renew_flg = 'f'\n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.shop_id = $shop_id\n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.claim_day >\n";
$sql .= "       (\n";
$sql .= "           SELECT\n";
$sql .= "               COALESCE(MAX(t_bill_d.bill_close_day_this), '".START_DAY."')\n";
$sql .= "           FROM\n";
$sql .= "               t_bill\n";
$sql .= "                   INNER JOIN\n";
$sql .= "               t_bill_d\n";
$sql .= "               ON t_bill.bill_id = t_bill_d.bill_id\n";
$sql .= "           WHERE\n";
$sql .= "               t_bill_d.client_id = t_sale_h.client_id\n";
$sql .= "       )\n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.claim_day <= $2\n";
$sql  = "PREPARE renew_flg_check(int ,date) AS $sql;";
Db_Query($db_con,$sql);

//同じ日に請求データを作成している数
$sql  = "SELECT \n";
$sql .= "   COUNT(bill_d_id) \n";
$sql .= "FROM \n";
$sql .= "   t_bill_d \n";
$sql .= "WHERE\n";
$sql .= "   t_bill_d.client_id = $1";
$sql .= "   AND\n";
$sql .= "   t_bill_d.bill_close_day_this = $2";
$sql .= ";\n";
$sql  = "PREPARE duplicate_date_check(int, date) AS $sql;";
Db_Query($db_con,$sql);

//請求書を再作成した場合の未更新のデータを抽出
$sql  = "SELECT\n";
$sql .= "   t_bill.claim_id,\n";
$sql .= "   t_bill.close_day \n";
$sql .= " FROM\n";
$sql .= "   t_bill\n";
$sql .= " WHERE\n";
$sql .= "   t_bill.claim_id = $1\n";
$sql .= "   AND\n";
$sql .= "   t_bill.shop_id = $2\n";
$sql .= "   AND\n";
$sql .= "   t_bill.last_update_flg = 'f'";
$sql .= ";\n";

$sql = "PREPARE nonupdate_count(int,int) AS $sql;";
Db_Query($db_con, $sql);

//請求番号通常連番よりMAX(連番)+１を抽出
$sql  = "SELECT\n";
$sql .= "   COALESCE(MAX(serial_num),0)+1\n";
$sql .= " FROM\n";
$sql .= "   t_bill_no_serial\n";
$sql .= " WHERE\n";
$sql .= "   shop_id = $1\n";
$sql .= ";\n";

$sql = "PREPARE get_no_serial(int) AS $sql;";
Db_Query($db_con, $sql);

//請求番号年別連番よりMAX(連番)+１を抽出
$sql  = "SELECT\n";
$sql .= "   COALESCE(MAX(serial_num),0)+1\n";
$sql .= " FROM\n";
$sql .= "   t_bill_no_y_serial\n";
$sql .= " WHERE\n";
$sql .= "   year = to_char(NOW(), 'YYYY')\n";
$sql .= "   AND\n";
$sql .= "   shop_id = $1\n";
$sql .= ";\n";

$sql  = "PREPARE get_no_y_serial(int) AS $sql;";
Db_Query($db_con, $sql);

//請求番号月別連番よりMAX(連番)+１を抽出
$sql  = "SELECT\n";
$sql .= "   COALESCE(MAX(serial_num),0)+1\n";
$sql .= " FROM\n";
$sql .= "   t_bill_no_m_serial\n";
$sql .= " WHERE";
$sql .= "   month = to_char(NOW(), 'YYYYMM')\n";
$sql .= "   AND\n";
$sql .= "   shop_id = $1";
$sql .= ";";

$sql  = "PREPARE get_no_m_serial(int) AS $sql;";
Db_Query($db_con, $sql);

//各連番テーブルに登録するSQL
$sql  = "INSERT INTO t_bill_no_serial (";
$sql .= "   serial_num,";
$sql .= "   shop_id";
$sql .= ")VALUES(";
$sql .= "   $1,";
$sql .= "   $2";
$sql .= ");";

$sql  = "PREPARE insert_no_serial(int, int) AS $sql;";
Db_Query($db_con, $sql);

$sql  = "INSERT INTO t_bill_no_y_serial (";
$sql .= "   serial_num,";
$sql .= "   year,";
$sql .= "   shop_id";
$sql .= ")VALUES(";
$sql .= "   $1,";
$sql .= "   $2,";
$sql .= "   $3";
$sql .= ");";

$sql  = "PREPARE insert_no_y_serial(int, varchar, int) AS $sql;";
Db_Query($db_con, $sql);

$sql  = "INSERT INTO t_bill_no_m_serial (";
$sql .= "   serial_num,";
$sql .= "   month,";
$sql .= "   shop_id";
$sql .= ")VALUES(";
$sql .= "   $1,";
$sql .= "   $2,";
$sql .= "   $3";
$sql .= ");";

$sql  = "PREPARE insert_no_m_serial(int, varchar, int) AS $sql;";
Db_Query($db_con, $sql);

//明細請求書を登録している得意先をカウント
$sql  = "SELECT";
$sql .= "   COUNT(client_id) ";
$sql .= "FROM";
$sql .= "    t_client ";
$sql .= "WHERE";
$sql .= "   client_id IN (SELECT";
$sql .= "                   client_id";
$sql .= "               FROM";
$sql .= "                   t_claim";
$sql .= "               WHERE";
$sql .= "                   claim_id = $1";
$sql .= "               )";
$sql .= "   AND";
$sql .= "   claim_out = '1'";
$sql .= ";";

$sql  = "PREPARE bill_format_count(int) AS $sql;";
Db_Query($db_con, $sql);

//登録した請求書IDを抽出
$sql  = "SELECT";
$sql .= "   bill_id ";
$sql .= "FROM";
$sql .= "   t_bill ";
$sql .= "WHERE";
$sql .= "   bill_no = $1";
$sql .= "   AND";
$sql .= "   shop_id = $2";
$sql .= ";";

$sql  = "PREPARE get_bill_id(varchar,int) AS $sql;";
Db_Query($db_con, $sql);

//分割請求額
//分割残高額
$sql  = "SELECT\n";
$sql .= "   COALESCE(SUM(split_bill_data1.split_bill_amount),0),\n";
$sql .= "   COALESCE(SUM(split_bill_data2.installment_receivable_balance),0)\n";
$sql .= "FROM\n";
$sql .= "   t_sale_h\n";
$sql .= "       LEFT JOIN\n";
$sql .= "   (SELECT\n";
$sql .= "       sale_id,\n";
$sql .= "       COALESCE(SUM(t_installment_sales.collect_amount), 0) AS split_bill_amount\n";
$sql .= "   FROM\n";
$sql .= "       t_installment_sales\n";
$sql .= "   WHERE\n";
$sql .= "       t_installment_sales.collect_day <= $1\n";
//$sql .= "       AND\n";
//$sql .= "       t_installment_sales.collect_day > $4\n";
$sql .= "       AND \n";
$sql .= "       t_installment_sales.bill_id IS NULL \n";

$sql .= "   GROUP BY sale_id\n";
$sql .= "   ) AS split_bill_data1\n";
$sql .= "   ON t_sale_h.sale_id = split_bill_data1.sale_id\n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.claim_day <= $5\n";
$sql .= "       LEFT JOIN\n";
$sql .= "   (SELECT\n";
$sql .= "       sale_id,\n";
$sql .= "       COALESCE(SUM(t_installment_sales.collect_amount), 0) AS installment_receivable_balance\n";
$sql .= "   FROM\n";
$sql .= "       t_installment_sales\n";
$sql .= "   WHERE\n";
$sql .= "       t_installment_sales.collect_day > $1\n";
$sql .= "   GROUP BY sale_id\n";
$sql .= "   ) AS split_bill_data2\n";
$sql .= "   ON t_sale_h.sale_id = split_bill_data2.sale_id \n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.claim_day <= $5\n";
$sql .= "WHERE\n";
$sql .= "   t_sale_h.client_id = $2\n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.trade_id = 15\n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.claim_div = (SELECT\n";
$sql .= "                           claim_div\n";
$sql .= "                       FROM\n";
$sql .= "                           t_claim\n";
$sql .= "                       WHERE\n";
$sql .= "                           client_id = t_sale_h.client_id\n";
$sql .= "                           AND\n";
$sql .= "                           claim_id = $3\n";
$sql .= "                       )\n";
$sql .= "   AND\n";
$sql .= "   t_sale_h.renew_flg = 't'\n";
$sql .= ";\n";

$sql  = "PREPARE get_split_amount(date,int, int, date, date) AS $sql";
Db_Query($db_con, $sql);


//今回支払額抽出
$sql  = "SELECT ";
//$sql .= "   SUM(t_bill_d.intax_amount) AS sum_intax_amount, ";                        //[19-1]SUM（税込買上額）
$sql .= "   SUM(t_bill_d.first_payment) AS sum_intax_amount, ";                        //[19-1]SUM（税込買上額）
$sql .= "   SUM(t_bill_d.installment_sales_amount) AS sum_installment_sales_amount "; //[19-2]SUM（割賦売上額）
$sql .= "FROM\n";
$sql .= "   t_bill_d";
$sql .= "       INNER JOIN";
$sql .= "   t_bill";
$sql .= "   ON t_bill_d.bill_id = t_bill.bill_id";
$sql .= "   AND\n";
$sql .= "   t_bill_d.client_id = $1";             //[5-1]
$sql .= "   AND\n";
//$sql .= "   t_bill.collect_day > $2 ";
//$sql .= "   AND\n";
$sql .= "   t_bill.collect_day <= $3";
//合計請求書は抽出対照としない
$sql .= "   AND\n";
$sql .= "   t_bill_d.close_day IS NOT NULL\n";
$sql .= "   AND \n";
$sql .= "   t_bill_d.collect_bill_d_id IS NULL\n";
$sql .= ";\n";

$sql  = "PREPARE get_all_amount(int, date, date) AS $sql";
Db_Query($db_con, $sql);

/****************************/
//作成ボタン押下処理
/****************************/
//作成ボタンが押された場合、作成ボタン押下フラグをtrueにする。
$add_create_button_flg = ($_POST["hdn_button_flg"] == "true")? true : false;

//作成ボタン押下フラグがtrueの場合
if($add_create_button_flg == true){

    $set_data["hdn_button_flg"] = "";

    $form->setConstants($set_data);

    Db_Query($db_con, "BEGIN;");
    //発行形式
    $slipout_type = $_POST["form_slipout_type"][0];             //一括の場合は1 随時の場合は2
}

/****************************/
//請求先抽出処理
/****************************/
//発行形式が一括の場合
if($slipout_type == '1'){

    //請求締日の半角、必須チェック
    $form->addGroupRule('form_claim_day1', array(
        'y' => array(
                array('請求締日の日付は妥当ではありません。','required'),
                array('請求締日の日付は妥当ではありません。','numeric')),
        'm' => array(
                array('請求締日の日付は妥当ではありません。','required'),
                array('請求締日の日付は妥当ではありません。','numeric')),
        'd' => array(
                array('請求締日の日付は妥当ではありません。','required')),
    ));

    //POST取得
    $close_day          = $_POST["form_claim_day1"]["d"];  //締日
    $claim_close_day_y  = $_POST["form_claim_day1"]["y"];  //請求締日（年）
    $claim_close_day_m  = $_POST["form_claim_day1"]["m"];  //請求締日（月）

    //締日が29以下の場合
    if($close_day < 29){
        $claim_close_day_d = $close_day;
    //締め日が29以上の場合
    }else{
        $claim_close_day_d = date('d', mktime(0, 0, 0, (int)$claim_close_day_m+1, 0, (int)$claim_close_day_y));
    }

    if($form->validate()){
        //月・日を0埋め
        $claim_close_day_y = str_pad($claim_close_day_y, 4, 0, STR_PAD_LEFT);
        $claim_close_day_m = str_pad($claim_close_day_m, 2, 0, STR_PAD_LEFT);
        $claim_close_day_d = str_pad($claim_close_day_d, 2, 0, STR_PAD_LEFT);

        //請求日の日付が妥当ではない場合、請求日エラーフラグを立てる
        $claim_check_flg = (!checkdate((int)$claim_close_day_m, (int)$claim_close_day_d, (int)$claim_close_day_y))? true : false ;

        $claim_day_flg = (date('Ymd') < $claim_close_day_y.$claim_close_day_m.$claim_close_day_d)? true : false;
    }else{
        $err_flg = true;
    }

    //日付でエラーがあった場合はエラーを表示する。
    if($claim_check_flg === true || $claim_day_flg === true){
        $claim_day_flg = true;
    }

    //請求日に妥当な日付が入力された場合
    if($claim_day_flg == false &&  $err_flg != true){
        //請求日を「-」区切りで連結
        $claim_close_day = $claim_close_day_y."-".$claim_close_day_m."-".$claim_close_day_d;

        //請求締日がシステム開始日以前の場合はエラー
        $system_start_flg = ($claim_close_day < date('Y-m-d',mktime(0,0,0,1,1,2005)))? true : false;

        //月次更新日チェック
        $renew_err = Bill_Monthly_Renew_Check($db_con, $claim_close_day);


        //指定された締日に設定されている請求先を抽出
        $sql  = "SELECT";
        $sql .= "   DISTINCT t_claim.claim_id ";
        $sql .= "FROM";
        $sql .= "   t_claim\n";
        $sql .= "       INNER JOIN\n";
        $sql .= "   t_client\n";
        $sql .= "   ON t_claim.claim_id = t_client.client_id \n";
        $sql .= "WHERE \n";
        $sql .= "   t_client.close_day = '$close_day'\n";
        $sql .= "   AND\n";
        $sql .= "   t_client.shop_id = $shop_id\n";
        $sql .= ";";

        $result = Db_Query($db_con, $sql);
        $claim_count = pg_num_rows($result);

        for($i = 0; $i < $claim_count; $i++){
            $claim_id[] = pg_fetch_result($result, $i, 0);
        }

    }else{
        $err_flg = true;
    }

//発行形式が随時の場合
}elseif($slipout_type == '2'){

    //請求締日の半角、必須チェック
    $form->addGroupRule('form_claim_day2', array(
        'y' => array(
                array('請求締日の日付は妥当ではありません。','required'),
                array('請求締日の日付は妥当ではありません。','numeric'),
                array("請求締日に'2005-01-01'以前の日付は入力できません。","rangelength", array(4,4))),
        'm' => array(
                array('請求締日の日付は妥当ではありません。','required'),
                array('請求締日の日付は妥当ではありません。','numeric')),
        'd' => array(
                array('請求締日の日付は妥当ではありません。','required'))
    ));

    //POST取得
    $claim_cd1  = $_POST["form_claim"]["cd1"];                  //請求先コード１
    $claim_cd2  = $_POST["form_claim"]["cd2"];                  //請求先コード２
    $claim_name = $_POST["form_claim"]["name"];                 //請求先名
    $claim_close_day_y = $_POST["form_claim_day2"]["y"];        //請求締日（年）
    $claim_close_day_m = $_POST["form_claim_day2"]["m"];        //請求締日（月）

//    $claim_close_day_d = $_POST["form_claim_day2"]["d"];        //請求締日（日）
    //日はマスタの締日を使用するので、日付のチェックのためにとりあえず1日をセット
    $claim_close_day_d = 1;

    //請求日の日付エラーチェック
    $claim_day_flg = (checkdate((int)$claim_close_day_m, (int)$claim_close_day_d, (int)$claim_close_day_y) == false)? true : false;

    //請求先コードが空の場合は請求先エラーフラグを立てる
    $claim_err_flg = ($claim_name == null)? true : false ;

    //請求日エラーフラグがfalse、請求先エラーフラグがfalse場合
    if($form->validate() && $claim_day_flg == false && $claim_err_flg == false){

        //指定した請求先の情報を抽出
        $sql  = "SELECT\n";
        $sql .= "   client_id,\n";
        $sql .= "   close_day \n";
        $sql .= " FROM\n";
        $sql .= "   t_client\n";
        $sql .= " WHERE\n";
        $sql .= "   client_div = '3'\n";
        $sql .= "   AND\n";
        $sql .= "   shop_id = $shop_id\n";
        $sql .= "   AND\n";
        $sql .= "   client_cd1 = '$claim_cd1'\n";
        $sql .= "   AND\n";
        $sql .= "   client_cd2 = '$claim_cd2'\n";
        $sql .= ";\n";

        $result = Db_Query($db_con, $sql);
        $claim_count    = pg_num_rows($result);
        $claim_id[]     = @pg_fetch_result($result, 0, 0);
        $mst_close_day  = @pg_fetch_result($result, 0, 1);

        //締日が29以下の場合
        if($mst_close_day < 29){
            $mst_close_day = $mst_close_day;
        //締め日が29以上の場合
        }else{
            $mst_close_day = date('d', mktime(0, 0, 0, (int)$claim_close_day_m+1, 0, (int)$claim_close_day_y));
        }

        $year           = $claim_close_day_y;
        $month          = $claim_close_day_m;
        $mst_close_day  = str_pad($mst_close_day, 2, 0, STR_PAD_LEFT);

        //入力された請求締日
        //今回請求日
        $target_close_day = $year."-".$month."-".$mst_close_day;

        //月・日を0埋め
        $claim_close_day_m = str_pad($claim_close_day_m, 2, 0, STR_PAD_LEFT);
        $cliam_close_day_d = str_pad($claim_close_day_d, 2, 0, STR_PAD_LEFT);

        //請求日を「-」区切りで連結
        $claim_close_day = $claim_close_day_y."-".$claim_close_day_m."-".$mst_close_day;
        //請求締日がシステム開始日以前の場合はエラー
        $system_start_flg = ($claim_close_day < date('Y-m-d',mktime(0,0,0,1,1,2005)))? true : false;

        //指定した請求先のMAXの締日を抽出
        $sql  = "SELECT";
        $sql .= "   COALESCE(MAX(close_day), '".START_DAY."') AS close_day ";
        $sql .= "FROM";
        $sql .= "   t_bill ";
        $sql .= "WHERE";
        $sql .= "   claim_id = $claim_id[0]";
        $sql .= ";";

        $result = Db_Query($db_con, $sql);
        $last_close_day = pg_fetch_result($result, 0,0);

        //前回締日が今回締日以前を指定している場合はエラー
        if($last_close_day >= $claim_close_day){
            $last_close_day_err = true;
        //前回締日が今回請求日以前の場合はエラー　OR　今回締日が今回請求日以降の場合はエラー
        }elseif(($last_close_day >= $target_close_day)){
            $last_close_day_err = true;
        }

        //月次更新日チェック
        $renew_err = Bill_Monthly_Renew_Check($db_con, $claim_close_day);
    }else{
        $err_flg = true;
    }
}
/****************************/
//エラー処理
/****************************/
//請求日エラーフラグがtrueの場合、エラーフラグを立てる
if($claim_day_flg == true){
    $form->setElementError("form_claim_day2","請求締日の日付は妥当ではありません。");
    $err_flg = true;
}

//請求先エラーフラグがtrueの場合、エラーフラグを立てる
if($claim_err_flg == true){
    $form->setElementError("form_claim","正しい請求先コードを入力して下さい。");
    $err_flg = true;
}

//システム開始日エラーがtureの場合
if($system_start_flg == true){
    $form->setElementError("form_claim_day2","請求締日に2005年1月1日より前の日付は入力できません。");
    $err_flg = true;
}

//随時選択時、最新請求更新日より前の締日を指定した場合
if($last_close_day_err == true){
    $form->setElementError("form_claim_day2","指定した請求先に対する請求データは既に作成済みです。");
    $err_flg = true; 
}

//月次更新日エラー
if($renew_err == true){
    $form->setElementError("form_claim_day2","月次更新日以前で請求書は作成できません。");
    $err_flg = true;
}

/***************************/
//値保持
/***************************/
$set_data["form_slipout_type"] = $slipout_type;
$form->setConstants($set_data);

/***************************/
//請求ヘッダ用データ抽出処理
/***************************/
//自社の消費税率（現在）と請求書書式設定を抽出
$sql  = "SELECT\n";
#2009-12-28 aoyama-n
#$sql .= "   tax_rate_n, \n";
$sql .= "   NULL, \n";
$sql .= "   claim_set   \n";
$sql .= "FROM\n";
$sql .= "   t_client \n";
$sql .= "WHERE\n";
$sql .= "   t_client.client_id = $shop_id\n";
$sql .= ";";

$result = Db_Query($db_con, $sql);
#2009-12-28 aoyama-n
#$rate      = pg_fetch_result($result ,0,0);       //[0-1]消費税率
$claim_set = pg_fetch_result($result ,0,1);       //[0-3]請求書書式設定
#2009-12-28 aoyama-n
#$tax_rate  = bcdiv($rate, 100, 2);                //[0-1]/ 100

if($claim_set == null && $add_create_button_flg == true){
    $form->setElementError("form_claim_day2","請求書番号設定を行ってください。");
    $err_flg = true;
}

//スタッフ名　AS　作成者名　※SESSIONのスタッフIDとマッチするスタッフ名を抽出
$sql  = "SELECT\n";
$sql .= "   staff_name \n";
$sql .= "FROM\n";
$sql .= "   t_staff \n";
$sql .= "WHERE\n";
$sql .= "   staff_id = $staff_id\n";
$sql .= ";";

$result = Db_Query($db_con, $sql);
$staff_name = pg_fetch_result($result, 0,0);     //[0-2]スタッフ名　AS　作成者名

//エラーフラグがtrueじゃない場合のみ処理開始
if($err_flg != true && $add_create_button_flg == true ){
    //抽出した請求先分繰り返す
    for($i = 0; $i < $claim_count; $i++){

        //重複エラーフラグがtrueの場合は処理を中止する。
        if($duplicate_err == true){
            break;
        }

        #2009-12-28 aoyama-n
        $tax_rate_obj->setTaxRateDay($claim_close_day);
        $rate = $tax_rate_obj->getClientTaxRate($claim_id[$i]);
        $tax_rate  = bcdiv($rate, 100, 2);

        //指定した請求期間内に日次更新していない売上または入金データを抽出
//        $sql = "EXECUTE renew_flg_check(".$claim_id[$i].",'".$claim_close_day."', ".$shop_id.");";
        $sql = "EXECUTE renew_flg_check(".$claim_id[$i].",'".$claim_close_day."');";

        $result = Db_QUery($db_con, $sql);
       // $unrenew_count = pg_fetch_result($result,0,0);
        $unrenew_count = pg_num_rows($result);
        $renew_flg = ($unrenew_count > 0)? true : false;

        //指定した請求期間内に日次更新していない入金データがある場合はエラー
        if($renew_flg == true){

            $unrenew_data = pg_fetch_all($result); 
            $duplicate_err = true; 
             for($j = 0; $j < $unrenew_count; $j++){
                if($unrenew_data[$j]["slip_div"] == "1"){
                    $pay_err[] = $unrenew_data[$j]["pay_no"];
                }else{
                    $sale_err[] = $unrenew_data[$j]["pay_no"];
                }
            }
            continue;
/*
            $form->setElementError("form_claim_day1","請求期間内に日時更新を行っていないデータがあります。");
            $duplicate_err = true;
            break;    
*/
        }

        //請求書を再作成した場合に、未更新の請求書がある場合にはその得意先の請求書は処理しない。
        $sql = "EXECUTE nonupdate_count($claim_id[$i], $shop_id);";
        $result = Db_Query($db_con, $sql);
        $num = @pg_num_rows($result);
        if($num > 0){
            if($slipout_type == '1'){
                $make_day = $claim_close_day;                                 //入力した締日
            }else{
                $make_day = $target_close_day;
            }
            $chk_date = pg_fetch_result($result, 0,1);
            if($chk_date != $make_day){
                $judge_count[] = pg_fetch_result($result, 0, 0);
            }else{
                $made_slip_count[] = pg_fetch_result($result, 0,0);
            }
            continue;
        }

        $result = Db_Query($db_con, $sql);
        $num = @pg_num_rows($result);
        //該当するレコードがあった場合
        if($num > 0){
            $judge_count[] = pg_fetch_result($result, 0,0);
            continue;
        }

        //■請求先情報抽出
        $sql = "EXECUTE get_claim_data('".$claim_close_day."', ".$claim_id[$i].");";

        $result = Db_Query($db_con, $sql);
        $claim_data_count = pg_num_rows($result);

        //該当レコードがある場合
        if($claim_data_count > 0){
            $claim_data = pg_fetch_array($result, 0);
        //該当レコードがない場合
        }else{
            $no_claim_sheet_count[] = $claim_id[$i];
            continue;
        }
  /*      //■請求先情報抽出
        $sql = "EXECUTE get_claim_data('".$claim_close_day."', ".$claim_id[$i].");";
        $result = Db_Query($db_con, $sql);
        $claim_data_count = pg_num_rows($result);
        //該当レコードがある場合
        if($claim_data_count > 0){
            $claim_data = pg_fetch_array($result, 0);        //該当レコードがない場合
        }else{
            $no_claim_sheet_count[] = $claim_id[$i];
            continue;
        }
*/

        //今回請求締日より今回請求締日が過去の場合は処理しない。
        if($claim_close_day <= $claim_data[renew_day]){
            $renew_claim_count[] = $claim_id[$i];
            continue;
        }

        //請求先にひもづく得意先の前回請求データ作成時の締日抽出
        $sql = "EXECUTE close_day_data(".$claim_id[$i].");";
        $close_day_result = Db_Query($db_con,$sql);
        $child_count = pg_num_rows($close_day_result);

        //該当する得意先がある場合
        if($child_count > 0){
            //請求書番号が「１（通常）の場合」
            if($claim_set == '1'){
                //請求番号通常連番よりMAX(連番)+１を抽出
                $sql    = "EXECUTE get_no_serial($shop_id);";
                $result = Db_Query($db_con, $sql);
                $max_no = pg_fetch_result($result ,0,0);

                //抽出した連番を8桁になるように左側を0埋めする
                $claim_sheet_no = str_pad($max_no, 8, 0, STR_PAD_LEFT);     //[3-1]請求書番号

            //請求書番号設定が「2（年度別）」の場合
            }elseif($claim_set == '2'){
                //請求番号年別連番よりMAX(連番)+１を抽出
                $sql    = "EXECUTE get_no_y_serial($shop_id);";
                $result = Db_Query($db_con, $sql);
                $max_no = pg_fetch_result($result, 0,0);

                //抽出した連番を6桁になるように左側を0埋めする
                $claim_sheet_no = str_pad($max_no, 6, 0, STR_PAD_LEFT);

                //年の下二桁と0埋めした連番を組み合わせる
                $year = date('y');
                $claim_sheet_no = $year.$claim_sheet_no;                    //[3-1]請求書番号

            //請求書番号設定が「3（月別）」の場合
            }elseif($claim_set == '3'){
                //請求番号月別連番よりMAX(連番)+１を抽出
                $sql    = "EXECUTE get_no_m_serial($shop_id);";
                $result = Db_Query($db_con, $sql);
                $max_no = pg_fetch_result($result,0,0);

                //抽出した連番を4桁になるように左側を0埋めする
                $claim_sheet_no = str_pad($max_no, 4, 0, STR_PAD_LEFT);

                //年の下二桁と0埋めした連番を組み合わせる
                $year_month = date('ym');
                $claim_sheet_no = $year_month.$claim_sheet_no;              //[3-1]請求書番号
            }

            //請求書を設定している得意先の数をカウント
            $sql = "EXECUTE bill_format_count($claim_id[$i]);";
            $result = Db_Query($db_con, $sql);
            $claim_out_count = pg_fetch_result($result, 0,0);

            //請求ヘッダに登録する値[4-1]
            //COUNTの結果が１以上の場合
            if($claim_out_count >= 1){
                $claim_out = '1';       //(明細)
            }else{
                $claim_out = '2';       //(合計)
            }

            //強制的に親に合わせる
            $claim_out = $claim_data["claim_out"];
            /**********************************************************/
            //■請求書ヘッダテーブルへ登録（１レコード追加）
            /**********************************************************/
            //ヘッダテーブルの登録は1回のため、ループ回数が1回目のみ処理開始
            $sql  = "INSERT INTO t_bill (\n";
            $sql .= "   bill_id,\n";                                    //[14-1]請求書ID
            $sql .= "   bill_no,\n";                                    //[14-2]請求書番号
            $sql .= "   close_day,\n";                                  //[14-3]締日
            $sql .= "   collect_day,\n";                                //[14-4]回収予定日
            $sql .= "   claim_id,\n";                                   //[14-5]請求先ID
            $sql .= "   claim_cd1,\n";                                  //[14-6]請求先コード１
            $sql .= "   claim_cd2,\n";                                  //[14-7]請求先コード２
            $sql .= "   claim_name1,\n";                                //[14-8]請求先名１
            $sql .= "   claim_name2,\n";                                //[14-9]請求先名２
            $sql .= "   claim_cname,\n";                                //[14-10]請求先名
            $sql .= "   compellation,\n";                               //[14-11]敬称
            $sql .= "   post_no1,\n";                                   //[14-12]請求先郵便番号１
            $sql .= "   post_no2,\n";                                   //[14-13]請求先郵便番号２
            $sql .= "   address1,\n";                                   //[14-14]請求先住所１
            $sql .= "   address2,\n";                                   //[14-15]請求先住所２
            $sql .= "   address3,\n";                                   //[14-16]請求先住所３
            $sql .= "   c_memo1,\n";                                    //[14-17]請求書コメント１
            $sql .= "   c_memo2,\n";                                    //[14-18]請求先コメント２
            $sql .= "   c_memo3,\n";                                    //[14-19]請求先コメント３
            $sql .= "   c_memo4,\n";                                    //[14-20]請求先コメント４
            $sql .= "   c_memo5,\n";                                    //[14-21]請求先コメント５
            $sql .= "   c_memo6,\n";                                    //[14-22]請求先コメント６
            $sql .= "   c_memo7,\n";                                    //[14-23]請求先コメント７
            $sql .= "   c_memo8,\n";                                    //[14-24]請求先コメント８
            $sql .= "   c_memo9,\n";                                    //[14-25]請求先コメント９
            $sql .= "   c_memo10,\n";                                   //[14-26]請求先コメント１０
            $sql .= "   c_memo11,\n";                                   //[14-27]請求先コメント１１
            $sql .= "   c_memo12,\n";                                   //[14-28]請求先コメント１２
            $sql .= "   c_memo13,\n";                                   //[14-29]請求先コメント１３
            $sql .= "   claim_send,\n";                                 //[14-30]請求書送付
            $sql .= "   bill_format,\n";                                //[14-31]請求書書式設定
            $sql .= "   tax_div,\n";                                    //[14-32]課税単位
            $sql .= "   c_tax_div,\n";                                  //[14-33]課税区分
            $sql .= "   tax_franct,\n";                                 //[14-34]消費税（端数）
            $sql .= "   coax,\n";                                       //[14-35]金額（まるめ区分）
            $sql .= "   pay_way,\n";                                    //[14-36]集金方法
            $sql .= "   staff_cd,\n";                                   //[14-37]担当者コード
            $sql .= "   staff_name,\n";                                 //[14-38]担当者名
            $sql .= "   create_staff_name,\n";                          //[14-39]作成者名
            $sql .= "   shop_id,\n";                                    //[14-40]取引先ID
            $sql .= "   tax_rate_n,\n";                                 //[14-41]消費税率
            $sql .= "   c_fsize1,\n";
            $sql .= "   c_fsize2,\n";
            $sql .= "   c_fsize3,\n";
            $sql .= "   c_fsize4,\n";
            $sql .= "   pay_m,\n";
            $sql .= "   pay_d,\n";
            $sql .= "   slipout_type, \n";       //伝票発行形式（１：一括　OR　２：随時）
            $sql .= "   ownership_flg, \n";       //親子関係フラグ
            $sql .= "   fix_flg \n";
            $sql .= ")VALUES(";
            $sql .= "   (SELECT COALESCE(MAX(bill_id), 0)+1 FROM t_bill),\n"; //MAX（請求ID）＋１
            $sql .= "   '$claim_sheet_no',\n";                          //[3-1]
            //一括の場合
            if($slipout_type == '1'){
                $sql .= "   '$claim_close_day',\n";                                 //入力した締日
            }else{
                $sql .= "   '$target_close_day',\n";
            }
            $sql .= "   '$claim_data[next_close_day]',\n";              //[1-36]
            $sql .= "   $claim_id[$i],\n";                              //画面で入力された請求先の取引先ID
            $sql .= "   '".addslashes($claim_data[client_cd1])."',\n";   //[1-4]
            $sql .= "   '".addslashes($claim_data[client_cd2])."',\n";   //[1-5]
            $sql .= "   '".addslashes($claim_data[client_name])."',\n";  //[1-6]
            $sql .= "   '".addslashes($claim_data[client_name2])."',\n"; //[1-7]
            $sql .= "   '".addslashes($claim_data[client_cname])."',\n"; //[1-8]
            $sql .= "   '$claim_data[compellation]',\n";                 //[1-9]
            $sql .= "   '$claim_data[post_no1]',\n";                    //[1-10]
            $sql .= "   '$claim_data[post_no2]',\n";                    //[1-11]
            $sql .= "   '".addslashes($claim_data[address1])."',\n";    //[1-12]
            $sql .= "   '".addslashes($claim_data[address2])."',\n";    //[1-13]
            $sql .= "   '".addslashes($claim_data[address3])."',\n";    //[1-14]
            $sql .= "   '".addslashes($claim_data[c_memo1])."',\n";     //[1-15]
            $sql .= "   '".addslashes($claim_data[c_memo2])."',\n";     //[1-16]
            $sql .= "   '".addslashes($claim_data[c_memo3])."',\n";     //[1-17]
            $sql .= "   '".addslashes($claim_data[c_memo4])."',\n";     //[1-18]
            $sql .= "   '".addslashes($claim_data[c_memo5])."',\n";     //[1-19]
            $sql .= "   '".addslashes($claim_data[c_memo6])."',\n";     //[1-20]
            $sql .= "   '".addslashes($claim_data[c_memo7])."',\n";     //[1-21]
            $sql .= "   '".addslashes($claim_data[c_memo8])."',\n";     //[1-22]
            $sql .= "   '".addslashes($claim_data[c_memo9])."',\n";     //[1-23]
            $sql .= "   '".addslashes($claim_data[c_memo10])."',\n";    //[1-24]
            $sql .= "   '".addslashes($claim_data[c_memo11])."',\n";    //[1-25]
            $sql .= "   '".addslashes($claim_data[c_memo12])."',\n";    //[1-26]
            $sql .= "   '".addslashes($claim_data[c_memo13])."',\n";    //[1-27]
            $sql .= "   '$claim_data[claim_send]',\n";                  //[1-28]
            $sql .= "   '$claim_out',\n";                               //[4-1]
            $sql .= "   '$claim_data[tax_div]',\n";                     //[1-29]
            $sql .= "   '$claim_data[c_tax_div]',\n";                   //[1-30]
            $sql .= "   '$claim_data[tax_franct]',\n";                  //[1-31]
            $sql .= "   '$claim_data[coax]',\n";                        //[1-32]
            $sql .= "   '$claim_data[pay_way]',\n";                     //[1-33]
            $sql .= ($claim_data[charge_cd] != null)? $claim_data[charge_cd].",\n" : "null,\n";     //[1-34]
            $sql .= "   '".addslashes($claim_data[staff_name])."',\n";  //[1-35]
            $sql .= "   '".addslashes($staff_name)."',\n";              //[0-2]
            $sql .= "   $shop_id,\n";                                   //$_SESSION[shop_id]
            $sql .= "   '$rate',\n";                                    //[0-1]
            $sql .= "   '$claim_data[c_fsize1]',\n";                    //[1-27]
            $sql .= "   '$claim_data[c_fsize2]',\n";                    //[1-27]
            $sql .= "   '$claim_data[c_fsize3]',\n";                    //[1-27]
            $sql .= "   '$claim_data[c_fsize4]',\n";                    //[1-27]
            $sql .= "   '$claim_data[pay_m]',\n";
            $sql .= "   '$claim_data[pay_d]',\n";
            $sql .= "   '$slipout_type',\n";
            $sql .= ($child_count == 1)? "'f'," : "'t',";
            $sql .= "   't' ";
            $sql .= ");\n";

            $result = Db_Query($db_con, $sql);
            if($result === false){
                $err_message = pg_last_error();
                $err_format = "t_bill_bill_no_key";
                Db_Query($db_con, "ROLLBACK");

                //発注NOが重複した場合
                if(strstr($err_message,$err_format) !== false){
                    $error = "同時に請求データの作成を行ったため、伝票番号が重複しました。もう一度仕入を行ってください。";
                    $duplicate_err = true;
                    break;
                }else{
                    exit;
                }
            }

            //登録した請求書IDを抽出
            $sql     = "EXECUTE get_bill_id('".$claim_sheet_no."', ".$shop_id.")";
            $result  = Db_Query($db_con, $sql);
            $bill_id = pg_fetch_result($result, 0,0);
//          }
        }

        /**********************************************************/
        //■抽出したデータをもとに集計（得意先単位）
        /**********************************************************/
        //合計金額初期化
        $sum_bill_amount_last               = null;
        $sum_pay_amount                     = null;
        $sum_tune_amount                    = null;
        $sum_rest_amount                    = null;
        $sum_sale_amount                    = null;
        $sum_tax_amount                     = null;
        $sum_intax_amount                   = null;
        $sum_installment_sales_amount       = null;
        $sum_split_bill_amount              = null;
        $sum_installment_receivable_balance = null;
        $sum_bill_amount_this               = null;
        $sum_pay_amount_this                = null;
        $sum_sales_slip_num                 = null;
        $sum_royalty_amount                 = null;
        $sum_royalty_tax_amount             = null;
        $sum_lump_tax_amount                = null;
        $sum_installment_out_amount         = null;

        //抽出した得意先数分ループ
        $claim_sheet_data_div = 0;
        for($j = 0; $j < $child_count; $j++){
            $client_close_day_data  = pg_fetch_array($close_day_result, $j);

            $client_close_day       = $client_close_day_data["client_close_day"];
            $client_claim_div       = $client_close_day_data["claim_div"];
            $client_id              = $client_close_day_data["client_id"];

            //同じ日に請求書を作成している場合は作成しない。
            //レンタル伝票IDを初期化
            $sale_id = null;
            //一括の場合
            if($slipout_type == '1'){
                //レンタル伝票作成
//                if($claim_close_day > '2007-02-28'){
                if($claim_close_day > '2007-01-31'){
                    $sale_id = Regist_Sale_Rental_Range($db_con, $client_id, $client_close_day, $claim_close_day);
                }
                $sql = "EXECUTE duplicate_date_check(".$client_id.",'".$claim_close_day."')";
            //随時の場合
            }else{
                //レンタル伝票作成
//                if($claim_close_day > '2007-02-28'){
                if($claim_close_day > '2007-01-31'){

                    $sale_id = Regist_Sale_Rental_Range($db_con, $client_id, $client_close_day, $claim_close_day);
                }
                $sql = "EXECUTE duplicate_date_check(".$client_id.",'".$target_close_day."')";
            }

            $result = Db_Query($db_con, $sql);
            $num = @pg_fetch_result($result,0,0);
        
            if($num >0){
                continue;
            }

            $sql  = "EXECUTE claim_data(".$claim_id[$i]." ,\n";
            $sql .= "                   '".$claim_close_day."',\n";
            $sql .= "                    ".$shop_id.",\n";
            $sql .= "                   '".$client_close_day."',\n";
            $sql .= "                   ' ".$client_claim_div."',\n";
            $sql .= "                    ".$client_id."\n";
            $sql .= ");";

            $client_result = Db_Query($db_con,$sql);
            $claim_client_data = pg_fetch_array($client_result, 0);

            $c_client_id[$j]                = $claim_client_data["client_id"];                      //[5-1]取引先ID
            $c_client_cd1[$j]               = $claim_client_data["client_cd1"];                     //[5-2]取引先コード１
            $c_client_cd2[$j]               = $claim_client_data["client_cd2"];                     //[5-3]取引先コード２
            $c_client_name[$j]              = $claim_client_data["shop_name"];                      //[5-4]取引先名
            $c_client_name2[$j]             = $claim_client_data["shop_name2"];                     //[5-5]取引先名２
            $c_client_cname[$j]             = $claim_client_data["client_cname"];                   //[5-6]略称
            $c_c_tax_div[$j]                = $claim_client_data["c_tax_div"];                      //[5-7]課税区分
            $c_tax_franct[$j]               = $claim_client_data["tax_franct"];                     //[5-8]消費税（端数）
            $c_claim_out[$j]                = $claim_client_data["claim_out"];                      //[5-9]請求書形式
            $c_claim_div[$j]                = $claim_client_data["claim_div"];                      //[5-10]請求先区分
            $royalty_rate[$j]               = $claim_client_data["royalty_rate"];                   //[5-11]ロイヤリティレイト
            $c_coax[$j]                     = $claim_client_data["coax"];                           //[5-12]金額（まるめ区分）
            $c_close_day[$j]                = $claim_client_data["close_day"];                      //[5-13]締日
            $c_address1[$j]                 = $claim_client_data["address1"];
            $c_address2[$j]                 = $claim_client_data["address2"];
            $c_address3[$j]                 = $claim_client_data["address3"];
            $c_post_no1[$j]                 = $claim_client_data["post_no1"];
            $c_post_no2[$j]                 = $claim_client_data["post_no2"];
            $c_slip_out[$j]                 = $claim_client_data["slip_out"];
            $c_ware_id[$j]                  = $claim_client_data["ware_id"];                        //基本出荷倉庫
            $bill_close_day_last[$j]        = $claim_client_data["bill_close_day_last"];            //[6-1]前回請求日
            $bill_amount_last[$j]           = $claim_client_data["bill_amount_last"];               //[6-2]前回請求額
            $payment_last[$j]               = $claim_client_data["payment_last"];                   //[6-3]前回支払額
            $payment_extraction_s[$j]       = $claim_client_data["payment_extraction_s"];           //[6-4]支払予定額抽出期間（開始）    
            $pay_amount[$j]                 = $claim_client_data["pay_amount"];                     //[7-1]今回入金額
            $tune_amount[$j]                = $claim_client_data["tune_amount"];                    //[7-2]調整額   
            $receivable_net_amount[$j]      = $claim_client_data["receivable_net_amount"];          //[8-1]掛取引の売上金額（税抜）
            $receivable_tax_amount[$j]      = $claim_client_data["receivable_tax_amount"];          //[8-2]掛取引の消費税額
            $installment_net_amount[$j]     = $claim_client_data["installment_net_amount"];         //[8-4]割賦売上の売上金額（税抜）
            $installment_tax_amount[$j]     = $claim_client_data["installment_tax_amount"];         //[8-5]割賦売上の消費税額
            $taxation_amount[$j]            = $claim_client_data["taxation_amount"];                //[9-1]掛取引の課税合計
            $notax_amount[$j]               = $claim_client_data["notax_amount"];                   //[9-2]掛取引の非課税合計
            $royalty_sale_intax_amount[$j]  = $claim_client_data["royalty_sale_intax_amount"];      //ロイヤリティ対象売上合計額（課税）
            $royalty_sale_notax_amount[$j]  = $claim_client_data["royalty_sale_notax_amount"];      //ロイヤリティ対象売上合計額（非課税）
            //[13-1]繰越残高額を求める
            //前回請求額−（今回入金額）
            $rest_amount[$j]                = $bill_amount_last[$j] - ($pay_amount[$j]);   //[13-1]繰越残高

            //[13-2]ロイヤリティを求める
            //【外税】
            #2009-12-28 aoyama-n
            #if($claim_data["c_tax_div"]){
            if($claim_data["c_tax_div"] == '1'){
                //([18-1] + [18-2]) * [5-11] / 100
                $royalty_amount[$j]     = bcmul(bcadd($royalty_sale_intax_amount[$j], $royalty_sale_notax_amount[$j]),bcdiv($royalty_rate[$j],100,2),2);
            //【内税】
            #2009-12-28 aoyama-n
            #}else{
                //(([18-1] / (1 + [0-1] / 100)) + [18-2]) * [5-11] /100
            #    $royalty_amount[$j]     = bcadd(1,$tax_rate,2);
            #    $royalty_amount[$j]     = bcdiv($royalty_sale_intax_amount[$j], $royalty_amount[$j],2);
            #    $royalty_amount[$j]     = bcadd($royalty_amount[$j], $royalty_sale_notax_amount[$j],2);
            #    $royalty_amount[$j]     = bcmul($royalty_amount[$j], bcdiv($royalty_rate[$j],100,2),2);
            #2009-12-28 aoyama-n
            //【非課税】
            }elseif($claim_data["c_tax_div"] == '3'){
                //([18-1] + [18-2]) * [5-11] / 100
                $royalty_amount[$j]     = bcmul(bcadd($royalty_sale_intax_amount[$j], $royalty_sale_notax_amount[$j]),bcdiv($royalty_rate[$j],100,2),2);
            }

            //金額の丸め処理
            $royalty_amount[$j]     = Coax_Col($claim_data["coax"], $royalty_amount[$j]);

//           if($royalty_goods_data[0][tax_div] == '1'){
            //[13-3]ロイヤリティ消費税を求める
            #2009-12-28 aoyama-n
            if($claim_data["c_tax_div"] == '1'){
                $royalty_tax_amount[$j]     = bcmul($royalty_amount[$j], $tax_rate,2);
                $royalty_tax_amount[$j]     = Coax_Col($claim_data["tax_franct"], $royalty_tax_amount[$j]);
            }elseif($claim_data["c_tax_div"] == '3'){
                $royalty_tax_amount[$j]     = 0;
            }

            //[13-5]今回消費税額を求める
            //[13-6]税込御買上額を求める

            //・請求先の課税単位が「締日単位」の場合
            if($claim_data["tax_div"] == '1'){
                //売上データテーブルから抽出した「売上金額（課税）」と「売上金額（非課税）」を使用
                //請求先の [1-30]課税区分 により消費税の計算が異なる
                //消費税の端数は請求先の [1-31]消費税（端数） により処理する

                //【外税】
                if($claim_data["c_tax_div"] == '1'){
                    //[9-1] + [9-2] + [8-4]
                    $sale_amount[$j]    = $taxation_amount[$j] + $notax_amount[$j] + $installment_net_amount[$j] + $royalty_amount[$j];   //[13-4]今回買上額


                    #echo "tax_amount:" .bcmul($taxation_amount[$j], $tax_rate,2) ."<br>";
                    #echo "royalty_tax_amount:" .$royalty_tax_amount[$j] ."<br>";


                    //([9-1] * [0-1] / 100) + [8-5]
                    #2010-05-29 hashimoto-y
                    #既存バグ　�．好院璽襪了慊蠅�ない
                    #$tax_amount[$j]     = bcadd(bcadd(bcmul($taxation_amount[$j], $tax_rate,2), $installment_tax_amount[$j], 2), $royalty_tax_amount[$j]);  //[13-5]今回消費税額（税まるめ前）
                    $tax_amount[$j]     = bcadd(bcadd(bcmul($taxation_amount[$j], $tax_rate,2), $installment_tax_amount[$j], 2), $royalty_tax_amount[$j], 2);  //[13-5]今回消費税額（税まるめ前）

                    #echo "tax_amount:" .$tax_amount[$j] ."<br>";
                    #echo "tax_franct:" .$claim_data["tax_franct"] ."<br>";

                    $tax_amount[$j]     = Coax_Col($claim_data["tax_franct"], $tax_amount[$j]);     //[13-5]


            
                    //[13-4] + [13-5]
                    $intax_amount[$j]   = $sale_amount[$j] + $tax_amount[$j];                           //[13-6]税込御買上額

                //【内税】
                #2009-12-28 aoyama-n
                #}else{
                    //[9-1] + [9-2] + [8-4] + [8-5]
                #    $intax_amount[$j]   = $taxation_amount[$j] + $notax_amount[$j] + $installment_net_amount[$j] + $installment_tax_amount[$j] + $royalty_amount[$j] + $royalty_tax_amount[$j]; //[13-6]税込御買上額

                    //[9-1] - ([9-1] / ( 1 + [0-1])) + [8-5]
                    #$t_amount_data[$j]  = bcmul($taxation_amount[$j], $tax_rate,2);                 //[9-1] / ( 1 + [0-1])
                    #$tax_amount[$j]     = bcadd(bcadd(bcsub($taxation_amount[$j], $t_amount_data[$j]), $installment_tax_amount[$j], 2), $royalty_tax_amount[$j]);//[13-5]今回消費税額（税まるめ前）
                    #$tax_amount[$j]     = Coax_Col($claim_data["tax_franct"], $tax_amount[$j]);

                    //[13-6] - [13-5]
                    #$sale_amount[$j]    = $intax_amount[$j] - $tax_amount[$j];

                #2009-12-28 aoyama-n
                //【非課税】
                }elseif($claim_data["c_tax_div"] == '3'){
                    //[13-4]今回買上額
                    $sale_amount[$j]    = $taxation_amount[$j] + $notax_amount[$j] + $installment_net_amount[$j] + $royalty_amount[$j];

                    $tax_amount[$j]     = 0;
            
                    //[13-6]税込御買上額
                    $intax_amount[$j]   = $sale_amount[$j] + $tax_amount[$j];

                }

//                $lump_tax_amount[$j] = ($receivable_tax_amount[$j] + $installment_tax_amount[$j] + $royalty_tax_amount[$j] - $tax_amount[$j]) * -1;
                $lump_tax_amount[$j]    = $tax_amount[$j] - ($receivable_tax_amount[$j] + $installment_tax_amount[$j] + $royalty_tax_amount[$j]);

#print $lump_tax_amount[$j]."<br>";
##exit;

            //・請求先の課税単位が「伝票単位」の場合
            }else{
                // 売上ヘッダテーブルから抽出した「売上金額（税抜）」と「消費税額」を使用
                //[8-1]+[8-4]
                $sale_amount[$j]        = $receivable_net_amount[$j] + $installment_net_amount[$j] + $royalty_amount[$j];         //[13-4]今回買上額

                //[8-2]+[8-5]
                $tax_amount[$j]         = $receivable_tax_amount[$j] + $installment_tax_amount[$j] + $royalty_tax_amount[$j];         //[13-5]今回消費税額

                //[13-4]+[13-5]
                $intax_amount[$j]       = $sale_amount[$j] + $tax_amount[$j];                               //[13-6]税込御買上額

                //伝票単位の場合はnull
                $lump_tax_amount[$j]    = 0;
            }

            //[13-7]割賦売上額を求める
            //[8-4] + [8-5]
            $installment_sales_amount[$j]   = $installment_net_amount[$j] + $installment_tax_amount[$j];    //[13-7]割賦売上額

            //[13-8]今回請求額を求める
            //[13-1] + [13-6]
//            $bill_amount_this[$j]           = $rest_amount[$j] + $intax_amount[$j];                         //[13-8]今回請求額

            //※今回割賦請求額が含まれいないため、再度抽出しなおす
            $bill_amount_this[$j]           = $rest_amount[$j] + $intax_amount[$j] - $installment_sales_amount[$j]; //[13-8]今回請求額

            //[13-9]伝票枚数
            $sales_slip_num[$j]             = $claim_client_data["sales_slip_num"];                 //[13-9]伝票枚数
            $sales_slip_num[$j]             = $claim_client_data["sales_slip_num"];                 //[13-9]伝票枚数
   
            //税込買上割賦除外額
            $installment_out_amount[$j]     = $intax_amount[$j] - $installment_sales_amount[$j];    //税込買上額　−　割賦売上額

 
            //今回がはじめての請求書作成かを判別
            $sql  = "SELECT\n";
            $sql .= "   COUNT(bill_d_id) \n";
            $sql .= "FROM\n";
            $sql .= "   t_bill\n";
            $sql .= "       INNER JOIN\n";
            $sql .= "   t_bill_d \n";
            $sql .= "   ON t_bill.bill_id = t_bill_d.bill_id \n";
            $sql .= "      AND\n";
/*
            $sql .= "      t_bill.claim_id = $claim_id[$i]\n";
            $sql .= "      AND\n";
*/
            //2011-12-11 hashimoto-y
            $sql .= "      t_bill_d.claim_div = $c_claim_div[$j]\n";

            $sql .= "      AND\n";
            $sql .= "      t_bill.first_set_flg = 'f'\n";
            $sql .= "      AND\n";
            $sql .= "      t_bill_d.client_id = $c_client_id[$j]\n";
            $sql .= ";";

            $result = Db_Query($db_con, $sql);
            //初登録ならば初回登録フラグをtrueにセット
            $first_add_flg = (pg_fetch_result($result, 0,0) == 0)? true : false;

            //初回登録フラグがtrueならば前回請求額（初期請求残高額）＋ 買上額を求める
            if($first_add_flg === true){
                $first_payment[$j] = $bill_amount_last[$j] + $intax_amount[$j];
            }else{
                $first_payment[$j] = $intax_amount[$j];
            }

            /**********************************************************/
            //レンタルTOレンタルの伝票をアップデート
            /**********************************************************/
//            if($sale_id != false){
//            if($sale_id != false && $claim_close_day > '2007-02-28'){

            //レンタルの伝票枚数分ループ
            $rental_slip_count = count($sale_id);
            for($l = 0; $l < $rental_slip_count; $l++){
                if($sale_id[$l] != false && $claim_close_day > '2007-01-31'){
                    //UPDATE条件
                    $where["sale_id"] = $sale_id[$l];
                    $where            = pg_convert($db_con,'t_sale_h',$where);

                    $sale_head["bill_id"] = $bill_id;

                    //売上データ登録
                    $return = Db_Update($db_con, "t_sale_h", $sale_head, $where);
                    if($return === false){
                        Db_Query($db_con, "ROLLBACK;");
                        exit;
                    }
                }
            }
	        /**********************************************************/
	        //■請求書データテーブル登録（複数レコード登録）
	        /**********************************************************/
	        //請求書番号設定が通常または親、子のデータ
            $sql  = "INSERT INTO t_bill_d(\n";
            $sql .= "   bill_d_id,\n";                  //請求書データID
            $sql .= "   bill_id,\n";                    //[15-2]請求書ID
            $sql .= "   bill_close_day_last,\n";        //[15-3]請求締日（前回）
            $sql .= "   bill_close_day_this,\n";        //[15-4]請求締日
            $sql .= "   client_id,\n";                  //[15-5]得意先ID
            $sql .= "   client_cd1,\n";                 //[15-6]得意先コード１
            $sql .= "   client_cd2,\n";                 //[15-7]得意先コード２
            $sql .= "   client_name1,\n";               //[15-8]得意先名１
            $sql .= "   client_name2,\n";               //[15-9]得意先名２
            $sql .= "   client_cname,\n";               //[15-10]得意先名（略称）
            $sql .= "   bill_type,\n";                  //[15-11]請求書形式
            $sql .= "   bill_data_div,\n";              //[15-12]請求書データ区分
            $sql .= "   claim_div,\n";                  //[15-13]請求先区分
            $sql .= "   bill_amount_last,\n";           //[15-14]前回請求額
            $sql .= "   pay_amount,\n";                 //[15-15]今回入金額
            $sql .= "   tune_amount,\n";                //[15-16]調整額
            $sql .= "   rest_amount,\n";                //[15-17]繰越残高額
            $sql .= "   sale_amount,\n";                //[15-18]今回買上額
            $sql .= "   tax_amount,\n";                 //[15-19]今回消費税額
            $sql .= "   intax_amount,\n";               //[15-20]税込買上額
            $sql .= "   installment_sales_amount,\n";   //[15-21]割賦売上額
            $sql .= "   royalty_amount,\n";             //[15-24]ロイヤリティ
            $sql .= "   royalty_tax,\n";                //[15-25]ロイヤリティ（消費税）
            $sql .= "   bill_amount_this,\n";           //[15-26]今回請求額
            $sql .= "   payment_this,\n";               //[15-27]今回支払額
            $sql .= "   sales_slip_num,\n";             //[15-28]伝票枚数
            $sql .= "   payment_extraction_s,\n";       //[15-29]支払予定額抽出期間（開始）
            $sql .= "   payment_extraction_e,\n";       //[15-30]支払予定額抽出期間（終了）
            $sql .= "   c_tax_div,\n";                  //[15-31]課税区分
            $sql .= "   tax_franct,\n";                 //[15-32]消費税（端数）
            $sql .= "   coax,\n";                       //[15-33]金額（まるめ区分）
            $sql .= "   royalty_rate,\n";               //[15-34]ロイヤリティ区分
            $sql .= "   close_day,\n";                  //[15-35]締日
            $sql .= "   first_payment,\n";              //[]初期残高額＋税込売上額
            $sql .= "   installment_out_amount \n";     //税込買上割賦除外額
            $sql .= ")VALUES(";
  	        $sql .= "   (SELECT COALESCE(MAX(bill_d_id), 0)+1 FROM t_bill_d),";                             //MAX（請求書）データID＋１
	        $sql .= "   $bill_id,";                                                                         //[14-1]
	        $sql .= ($bill_close_day_last[$j] != null)? "'".$bill_close_day_last[$j]."'," : "'".START_DAY."',"; //[6-1]
	        $sql .= "   '$claim_close_day',";                                                               //画面上から入力された請求締日
	        $sql .= "   $c_client_id[$j],";                                                                 //[5-1]
	        $sql .= "   '$c_client_cd1[$j]',";                                                              //[5-2]
	        $sql .= "   '$c_client_cd2[$j]',";                                                              //[5-3]
	        $sql .= "   '".addslashes($c_client_name[$j])."',";                                             //[5-4]
	        $sql .= "   '".addslashes($c_client_name2[$j])."',";                                            //[5-5]
	        $sql .= "   '".addslashes($c_client_cname[$j])."',";                                            //[5-6]
	        $sql .= "   '$c_claim_out[$j]',";                                                               //[5-9]
	        //自分が通常の場合は０、親または子のデータの場合は得意先コードの昇順で１から＋１する
	        //自分が親の場合
	        if($child_count == 1){
	            $sql .= "   0,";
	        //親または子のデータの場合                                           
	        }else{
	            $claim_sheet_data_div++;            //請求データ区分カウンタ
	            $sql .= "  $claim_sheet_data_div,";
	        }
	        $sql .= "   '$c_claim_div[$j]',";                                                               //[5-10]
	        $sql .= ($bill_amount_last[$j] != null)?  $bill_amount_last[$j]."," : " 0,";                    //[6-2]
	        $sql .= ($pay_amount[$j] != null)?        $pay_amount[$j].","       : " 0,";                    //[7-1]
	        $sql .= ($tune_amount[$j] != null)?       $tune_amount[$j].","      : " 0,";                    //[7-2]
	        $sql .= ($rest_amount[$j] != null)?       $rest_amount[$j].","      : " 0,";                    //[13-1]
	        $sql .= ($sale_amount[$j] != null)?       $sale_amount[$j].","      : " 0,";                    //[13-4]
	        $sql .= ($tax_amount[$j] != null)?        $tax_amount[$j].","       : " 0,";                    //[13-5]
	        $sql .= ($intax_amount[$j] != null)?      $intax_amount[$j].","     : " 0,";                    //[13-6]
	        $sql .= ($installment_sales_amount[$j] != null)?  $installment_sales_amount[$j]."," : " 0,";    //[13-7]
            $sql .= ($royalty_amount[$j] != null)?  $royalty_amount[$j].","     : " 0,";                    //[13-2]
            $sql .= ($royalty_tax_amount[$j] != null)? $royalty_tax_amount[$j]."," : " 0,";                 //[13-3]
	        $sql .= ($bill_amount_this[$j] != null)?  $bill_amount_this[$j]."," : " 0,";                    //[13-8]
	        $sql .= "   null,";                                                                             //NULL
	        $sql .= ($sales_slip_num[$j] != null)?  $sales_slip_num[$j]."," : " 0,";                        //[13-9]
	        $sql .= ($payment_extraction_s[$j] != null)? "   '$payment_extraction_s[$j]'," : "'".START_DAY."',";      //[6-4]
	        $sql .= "   '$claim_data[after_pay_d]',";                                                       //[1-37]
	        $sql .= "   '$c_c_tax_div[$j]',";                                                               //[5-7]
	        $sql .= "   '$c_tax_franct[$j]',";                                                              //[5-8]
	        $sql .= "   '$c_coax[$j]',";                                                                    //[5-12]
            $sql .= "   '$roaylty_rate[$j]',";                                                              
	        $sql .= "   '$c_close_day[$j]',";                                                               //[5-13]
            $sql .= ($first_payment[$j] != null)?     $first_payment[$j]."," : " 0,";                            //[]初期残高額＋税込売上額
            $sql .= ($installment_out_amount[$j] != null)? $installment_out_amount[$j] : "0";               //税込買上割賦除外額
	        $sql .= ");";
	    
	        $result = Db_Query($db_con, $sql);
	        if($result === false){
	            Db_Query($db_con, "ROLLBACK;");
	            exit;
	        }

            /**********************************************************/
            //■分割請求額抽出
            /**********************************************************/
            //[10-1] SUM（回収金額）　　　　　　　　
            //■抽出条件
            //[6-4]　または　今回の請求締日 < 割賦売上テーブルの回収日 <= [1-37]
            //※[6-4]の結果が無い場合には今回の請求締日を開始日とする
            $sql  = "EXECUTE get_split_amount(\n";
            $sql .= "           '".$claim_data[after_pay_d]."',\n";
            $sql .= "           $c_client_id[$j],\n";
            $sql .= "           $claim_id[$i],\n";
            $sql .= ($payment_extraction_s[$j] != null)? "   '$payment_extraction_s[$j]'," : "'".START_DAY."',";       //[6-4]
            $sql .= "           '".$claim_close_day."'\n";
            $sql .= "            );";
            $result = Db_Query($db_con, $sql);
            
            $split_bill_amount[$j]              = @pg_fetch_result($result,0,0);         //[10-1]分割請求額
            $installment_receivable_balance[$j] = @pg_fetch_result($result,0,1);         //[11-1]分割請求残高

            $sql  = "UPDATE";
            $sql .= "   t_installment_sales ";
            $sql .= "SET ";
//            $sql .= "   collect_flg = 't' ";
            $sql .= "   bill_id = $bill_id ";
            $sql .= "WHERE ";
            $sql .= "   sale_id IN (SELECT ";
            $sql .= "                   sale_id ";
            $sql .= "               FROM ";
            $sql .= "                   t_sale_h ";
            $sql .= "               WHERE ";
            $sql .= "                   t_sale_h.client_id = $c_client_id[$j]";
            $sql .= "                   AND ";
            $sql .= "                   t_sale_h.claim_div = (SELECT ";
            $sql .= "                                           claim_div\n";
            $sql .= "                                       FROM\n";
            $sql .= "                                           t_claim\n";
            $sql .= "                                       WHERE\n";
            $sql .= "                                           client_id = t_sale_h.client_id\n";
            $sql .= "                                           AND\n";
            $sql .= "                                           claim_id = $claim_id[$i]\n";
            $sql .= "                                       )\n";
            $sql .= "                   AND ";
            $sql .= "                   t_sale_h.trade_id = '15'";
            $sql .= "                   AND ";
            $sql .= "                   t_sale_h.renew_flg = 't'";
            $sql .= "                   AND ";
            $sql .= "                   t_sale_h.claim_day <= '$claim_close_day'\n";
            $sql .= "           ) \n";
            $sql .= "   AND\n";
            $sql .= "   t_installment_sales.collect_day <=  '".$claim_data[after_pay_d]."'\n";
            $sql .= "   AND \n";
            $sql .= "   t_installment_sales.bill_id IS NULL";
//            $sql .= "   AND\n";
//            $sql .= "   t_installment_sales.collect_day > ";
//            $sql .= ($payment_extraction_s[$j] != null)? "   '".$payment_extraction_s[$j]."'" : "'".START_DAY."'";
            $sql .= ";";
            $result = Db_Query($db_con, $sql);

            if($result === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

	        /**********************************************************/
	        //■今回支払額を抽出
	        /**********************************************************/
            $sql  = "EXECUTE get_all_amount($c_client_id[$j],";
            $sql .= ($payment_extraction_s[$j] != null)? "'$payment_extraction_s[$j]'," : "'$claim_close_day',";
            $sql .= "                       '".$claim_data[after_pay_d]."'";
            $sql .= ");";   

	        $result = Db_Query($db_con, $sql);
	        $all_intax_amount[$j]               = pg_fetch_result($result, 0,0);        //[19-1]
	        $all_installment_sales_amount[$j]   = pg_fetch_result($result, 0,1);        //[19-2]

/*
            //回収した請求データを更新
            $sql  = "UPDATE ";
            $sql .= "   t_bill_d ";
            $sql .= "SET ";
            $sql .= "   collect_bill_d_id = (   SELECT ";
            $sql .= "                               bill_d_id ";
            $sql .= "                           FROM ";
            $sql .= "                               t_bill_d ";
            $sql .= "                           WHERE ";
            $sql .= "                               t_bill_d.bill_id = $bill_id ";
            $sql .= "                               AND ";
            $sql .= "                               t_bill_d.client_id = $c_client_id[$j]";
            $sql .= "                               AND ";
            $sql .= "                               t_bill_d.close_day IS NOT NULL \n";
            $sql .= "                       )";
            $sql .= "WHERE ";
            $sql .= "   t_bill_d.bill_d_id IN ( SELECT ";
            $sql .= "                               t_bill_d.bill_d_id ";
            $sql .= "                           FROM ";
            $sql .= "                               t_bill ";
            $sql .= "                                   INNER JOIN ";
            $sql .= "                               t_bill_d ";
            $sql .= "                               ON t_bill.bill_id = t_bill_d.bill_id ";
            $sql .= "                           WHERE ";
            $sql .= "                               t_bill.collect_day <= '".$claim_data[after_pay_d]."'";
            $sql .= "                               AND\n";
            $sql .= "                               t_bill_d.client_id = $c_client_id[$j]";
            $sql .= "                               AND ";
            $sql .= "                               t_bill_d.close_day IS NOT NULL \n";
            $sql .= "                               AND ";
            $sql .= "                               t_bill_d.collect_bill_d_id IS NULL ";
            $sql .= "                           )";
            $sql .= ";";

            $result = Db_Query($db_con, $sql);

            if($result === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
*/
	        /**********************************************************/
	        //■今回支払額を更新
	        /**********************************************************/
	        //支払額を抽出
	        //[19-1] - [19-2] + [10-1] + [6-3] + [7-1]
	        $pay_amount_this[$j] = $all_intax_amount[$j] 
	                            - 
	                           $all_installment_sales_amount[$j]
	                            +
	                           $split_bill_amount[$j]
	                            +
	                           $payment_last[$j]
	                            -
	                           $pay_amount[$j];     //[19-3]

            $bill_amount_this[$j] = $bill_amount_this[$j] + $split_bill_amount[$j];     //今回請求残高
	                                                    
	        $sql  = "UPDATE\n";
	        $sql .= "   t_bill_d \n";
	        $sql .= "SET\n";
	        $sql .= "   payment_this = ";
            $sql .= ($pay_amount_this[$j] != null)? $pay_amount_this[$j]."," : "0,\n";
            $sql .= "   split_bill_amount = ";
            $sql .= ($split_bill_amount[$j] != null)? $split_bill_amount[$j]."," : "0,\n";
            $sql .= "   split_bill_rest_amount = ";
            $sql .= ($installment_receivable_balance[$j] != null)? $installment_receivable_balance[$j]."," : "0,\n";
            $sql .= "   bill_amount_this = ";
            $sql .= ($bill_amount_this[$j] != null) ? $bill_amount_this[$j] : "0 \n";

	        $sql .= "WHERE\n";
	        $sql .= "   bill_id = $bill_id\n";
            $sql .= "   AND\n";
            $sql .= "   client_id = $c_client_id[$j]\n";
            $sql .= ";\n";

	        $result = Db_Query($db_con, $sql);
	        if($result === false){
	            Db_Query($db_con, "ROLLBACK;");
	            exit;
	        }

            /**********************************************************/
            //■得意先ごとにロイヤリティの売上をおこす
            /**********************************************************/
            //[13-2]ロイヤリティ合計が+の場合のみ処理開始

#売上額がプラスの場合のみにロイヤリティが発生するという条件になっていたため、
#０以外はロイヤリティが発生するように修正
#            if($royalty_amount[$j] > 0){
            if($royalty_amount[$j] != 0){
                //売上登録関数に渡す引数作成
                $ary = array($claim_close_day,  //請求締日
                            $c_client_id[$j],   //得意先ID
                            $bill_id,           //請求書ID
                            $shop_id,           //ショップID
                       );

                Insert_Sale_Head ($db_con, $royalty_amount[$j], $royalty_tax_amount[$j], $ary, '1');     
            }

	        /**********************************************************/
	        //■合計金額を更新
	        /**********************************************************/
            $sum_bill_amount_last                   = $sum_bill_amount_last         + $bill_amount_last[$j];
            $sum_pay_amount                         = $sum_pay_amount               + $pay_amount[$j];
            $sum_tune_amount                        = $sum_tune_amount              + $tune_amount[$j];
            $sum_rest_amount                        = $sum_rest_amount              + $rest_amount[$j];
            $sum_sale_amount                        = $sum_sale_amount              + $sale_amount[$j];
            $sum_tax_amount                         = $sum_tax_amount               + $tax_amount[$j];
            $sum_intax_amount                       = $sum_intax_amount             + $intax_amount[$j];
            $sum_installment_sales_amount           = $sum_installment_sales_amount + $installment_sales_amount[$j];
            $sum_split_bill_amount                  = $sum_split_bill_amount        + $split_bill_amount[$j];
            $sum_installment_receivable_balance     = $sum_installment_receivable_balance + $installment_receivable_balance[$j];
            $sum_bill_amount_this                   = $sum_bill_amount_this         + $bill_amount_this[$j];
            $sum_pay_amount_this                    = $sum_pay_amount_this          + $pay_amount_this[$j];
            $sum_sales_slip_num                     = $sum_sales_slip_num           + $sales_slip_num[$j];
            $sum_royalty_amount                     = $sum_royalty_amount           + $royalty_amount[$j];
            $sum_royalty_tax_amount                 = $sum_royalty_tax_amount       + $royalty_tax_amount[$j];
            $sum_lump_tax_amount                    = $sum_lump_tax_amount          + $lump_tax_amount[$j];
            $sum_installment_out_amount             = $sum_installment_out_amount   + $installment_out_amount[$j];

            #2010-01-20 hashimoto-y
            #一括消費税の処理の箇所が得意先ループ外なため修正
            #潜在不具合
            /**********************************************************/
            //■一括消費税
            /**********************************************************/
            if($sum_lump_tax_amount != null){
//            if($lump_tax_amount[$j] != null){
                //売上登録関数に渡す引数作成
                #2010-01-20 hashimoto-y
                #潜在不具合修正
                #$ary = array($claim_close_day,  //請求締日
                #            $c_client_id[$i],      //得意先ID
                #            $bill_id,           //請求書ID
                #            $shop_id,           //ショップID
                #        );
                $ary = array($claim_close_day,  //請求締日
                            $c_client_id[$j],      //得意先ID
                            $bill_id,           //請求書ID
                            $shop_id,           //ショップID
                        );

                //Insert_Sale_Head ($db_con, 0, $lump_tax_amount[$j], $ary, '2');     
                Insert_Sale_Head ($db_con, 0, $sum_lump_tax_amount, $ary, '2');     
            }

            //金額が全てNULLの場合は請求データを作成しない
            if($bill_amount_last[$j] == 0
                &&
                $pay_amount[$j] == 0
                &&
                $tune_amount[$j] == 0
                &&
                $rest_amount[$j] == 0
                &&
                $sale_amount[$j] == 0
                &&
                $tax_amount[$j] == 0
                &&
                $intax_amount[$j] == 0
                &&
                $installment_sales_amount[$j] == 0
                &&
                $split_bill_amount[$j] == 0
                &&
                $installment_receivable_balance[$j] == 0
                &&
                $pay_amount_this[$j] == 0
//                &&
//                $sales_slip_num[$j] == 0
                &&
                $royalty_amount[$j] == 0
                &&
                $royalty_tax_amount[$j] == 0
            ){
                //回収済みのデータをアップデート（削除されるためid０でアップデート)
                Collect_Bill_D_Update($db_con, 0, $c_client_id[$j], $c_claim_div[$j], $claim_data[after_pay_d]);

                $sql = "DELETE FROM t_bill_d WHERE bill_id = $bill_id AND client_id = $c_client_id[$j];\n";

                $result = Db_Query($db_con, $sql);
                if($result === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }


            }else{  
               //回収済みのデータをアップデート
               Collect_Bill_D_Update($db_con, $bill_id, $c_client_id[$j], $c_claim_div[$j], $claim_data[after_pay_d]);
            }       
        }
#ERROR


        #2010-01-20 hashimoto-y
        #一括消費税の処理の箇所が得意先ループ外なため修正
        #潜在不具合
        #/**********************************************************/
        #//■一括消費税
        #/**********************************************************/
        #if($sum_lump_tax_amount != null){
//      #  if($lump_tax_amount[$j] != null){
        #    //売上登録関数に渡す引数作成
        #    #2010-01-20 hashimoto-y
        #    #潜在不具合修正
        #    #$ary = array($claim_close_day,  //請求締日
        #    #            $c_client_id[$i],      //得意先ID
        #    #            $bill_id,           //請求書ID
        #    #            $shop_id,           //ショップID
        #    #        );
        #    $ary = array($claim_close_day,  //請求締日
        #                $c_client_id[$j],      //得意先ID
        #                $bill_id,           //請求書ID
        #                $shop_id,           //ショップID
        #            );

        #    //Insert_Sale_Head ($db_con, 0, $lump_tax_amount[$j], $ary, '2');     
        #    Insert_Sale_Head ($db_con, 0, $sum_lump_tax_amount, $ary, '2');     
        #}

	    /*********************************************************/
	    //■請求データテーブルへ登録（親子関係がある場合のみ1レコード追加）
	    /*********************************************************/
        if($child_count > 1){
            $sql  = "INSERT INTO t_bill_d(\n";
            $sql .= "   bill_d_id,\n";                  //請求書データID
            $sql .= "   bill_id,\n";                    //[15-2]請求書ID
            $sql .= "   bill_close_day_last,\n";        //[15-3]請求締日（前回）
            $sql .= "   bill_close_day_this,\n";        //[15-4]請求締日
            $sql .= "   client_id,\n";                  //[15-5]得意先ID
            $sql .= "   client_cd1,\n";                 //[15-6]得意先コード１
            $sql .= "   client_cd2,\n";                 //[15-7]得意先コード２
            $sql .= "   client_name1,\n";               //[15-8]得意先名１
            $sql .= "   client_name2,\n";               //[15-9]得意先名２
            $sql .= "   client_cname,\n";               //[15-10]得意先名（略称）
            $sql .= "   bill_type,\n";                  //[15-11]請求書形式
            $sql .= "   bill_data_div,\n";              //[15-12]請求書データ区分
            $sql .= "   claim_div,\n";                  //[15-13]請求先区分
            $sql .= "   bill_amount_last,\n";           //[15-14]前回請求額
            $sql .= "   pay_amount,\n";                 //[15-15]今回入金額
            $sql .= "   tune_amount,\n";                //[15-16]調整額
            $sql .= "   rest_amount,\n";                //[15-17]繰越残高額
            $sql .= "   sale_amount,\n";                //[15-18]今回買上額
            $sql .= "   tax_amount,\n";                 //[15-19]今回消費税額
            $sql .= "   intax_amount,\n";               //[15-20]税込買上額
            $sql .= "   installment_sales_amount,\n";   //[15-21]割賦売上額
            $sql .= "   split_bill_amount,\n";          //[15-22]分割請求額
            $sql .= "   split_bill_rest_amount,\n";     //[15-23]分割請求残高
            $sql .= "   bill_amount_this,\n";           //[15-26]今回請求額
            $sql .= "   payment_this,\n";               //[15-27]今回支払額
            $sql .= "   sales_slip_num,\n";             //[15-28]伝票枚数
            $sql .= "   royalty_amount,\n";             //ロイヤリティ
            $sql .= "   royalty_tax,\n";                //ロイヤリティ消費税
            $sql .= "   payment_extraction_s,\n";       //[15-29]支払予定額抽出期間（開始）
            $sql .= "   payment_extraction_e,\n";       //[15-30]支払予定額抽出期間（終了）
            $sql .= "   c_tax_div,\n";                  //[15-31]課税区分
            $sql .= "   tax_franct,\n";                 //[15-32]消費税（端数）
            $sql .= "   coax,\n";                       //[15-33]金額（まるめ区分）
            $sql .= "   royalty_rate,\n";               //ロイヤリティ
            $sql .= "   close_day,\n";                   //[15-35]締日
            $sql .= "   installment_out_amount \n";     //税込買上割賦除外額
            $sql .= ")VALUES(";
	        $sql .= "   (SELECT COALESCE(MAX(bill_d_id), 0)+1 FROM t_bill_d), \n";
	        $sql .= "   $bill_id,\n";
	        $sql .= "   null,\n";
	        $sql .= "   '$claim_close_day', \n";
//	        $sql .= "   null,\n";
	        $sql .= "   $claim_id[$i],\n";              //請求先を登録するように変更
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
	        $sql .= "   '2', \n";
	        $sql .= "   0,\n";
	        $sql .= "   null,\n";
	        $sql .= "   ".(int)$sum_bill_amount_last.",\n";
	        $sql .= "   ".(int)$sum_pay_amount.",\n";
	        $sql .= "   ".(int)$sum_tune_amount.",\n";
	        $sql .= "   ".(int)$sum_rest_amount.",\n";
	        $sql .= "   ".(int)$sum_sale_amount.",\n";
	        $sql .= "   ".(int)$sum_tax_amount.",\n";
	        $sql .= "   ".(int)$sum_intax_amount.",\n";
	        $sql .= "   ".(int)$sum_installment_sales_amount.",\n";
	        $sql .= "   ".(int)$sum_split_bill_amount.",\n";
	        $sql .= "   ".(int)$sum_installment_receivable_balance.",\n";
	        $sql .= "   ".(int)$sum_bill_amount_this.",\n";
	        $sql .= "   ".(int)$sum_pay_amount_this.",\n";
	        $sql .= "   ".(int)$sum_sales_slip_num.",\n";
            $sql .= "   ".(int)$sum_royalty_amount.",\n";
            $sql .= "   ".(int)$sum_royalty_tax_amount.",\n";
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
	        $sql .= "   null,\n";
            $sql .= "   null,\n";
	        $sql .= "   null,\n";
            $sql .= "   ".(int)$sum_installment_out_amount." \n";
	        $sql .= ");\n";
	        $result = Db_Query($db_con, $sql);
	        if($result === false){
	            Db_Query($db_con, "ROLLBACK;");
	            exit;
            }
	    }

        //合計請求書の金額が全て０の場合作成した請求書を削除する
       if(($sum_bill_amount_last == null                || $sum_bill_amount_last == 0)
            &&
            ($sum_pay_amount == null                    || $sum_pay_amount == 0)
            &&
            ($sum_tune_amount == null                   || $sum_tune_amount == 0)
            &&
            ($sum_rest_amount == null                   || $sum_rest_amount == 0)
            &&
            ($sum_sale_amount == null                   || $sum_sale_amount == 0)
            &&
            ($sum_tax_amount == null                    || $sum_tax_amount == 0)
            &&
            ($sum_intax_amount == null                  || $sum_intax_amount == 0)
            &&
            ($sum_installment_sales_amount == null      || $sum_installment_sales_amount == 0)
            &&
            ($sum_split_bill_amount == null             || $sum_split_bill_amount == 0)
            &&
            ($sum_installment_receivable_balance == null || $sum_installment_receivable_balance == 0)
            &&
            ($sum_bill_amount_this == null              || $sum_bill_amount_this == 0)
            &&
            ($sum_pay_amount_this == null               || $sum_pay_amount_this == 0)
//            &&
//            ($sum_sales_slip_num == null                || $sum_sales_slip_num == 0)
            &&
            ($sum_royalty_amount == null                || $sum_royalty_amount == 0)
            &&
            ($sum_royalty_tax_amount == null            || $sum_royalty_tax_amount == 0)
        ){
            $sql = "DELETE FROM t_bill WHERE bill_id = $bill_id;\n";
            $result = Db_Query($db_con, $sql);

            if($result === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
        }else{
            /*****************************/
            //請求番号登録
            /*****************************/
            //通常設定の場合
            if($claim_set == '1'){
                $sql = "EXECUTE insert_no_serial($max_no, $shop_id);";

            //年別設定の場合
            }elseif($claim_set == '2'){
                $sql = "EXECUTE insert_no_y_serial($max_no, '".date('Y')."', $shop_id);";

            //月別設定の場合
            }elseif($claim_set == '3'){
                $sql = "EXECUTE insert_no_m_serial($max_no, '".date('Ym')."', $shop_id);";
            }

            $result = Db_Query($db_con, $sql);
            if($result === false){
                $err_message = pg_last_error();
                $err_format1 = "t_bill_no_serial_serial_num_key";
                $err_format2 = "t_bill_no_y_serial_year_key";
                $err_format3 = "t_bill_no_m_serial_month_key";

                Db_Query($db_con, "ROLLBACK");
                //発注NOが重複した場合
                if((strstr($err_message,$err_format1) !== false)
                        ||
                    (strstr($err_message,$err_format2) !== false)
                        ||
                    (strstr($err_message,$err_format3) !== false)){
                        $error = "同時に請求データの作成を行ったため、請求番号が重複しました。もう一度仕入を行ってください。";
                        $duplicate_err = true;
                        break;
                }else{
                    exit;
                }
            }
        }
	}

    //重複エラーがない場合
    if($duplicate_err != true ){

        if($slipout_type == '1'){

            //作成履歴があるか
            $sql  = "SELECT\n";
            $sql .= "   COUNT(*) \n";
            $sql .= "FROM\n";
            $sql .= "   t_bill_make_history \n";
            $sql .= "WHERE\n";
            $sql .= "   bill_close_day = '$claim_close_day'";
            $sql .= "   AND\n";
            $sql .= "   close_day = '$close_day'\n";
            $sql .= "   AND\n";
            $sql .= "  shop_id = $shop_id\n";
            $sql .= ";\n";

            $result = Db_Query($db_con, $sql);
            $make_count = pg_fetch_result($result, 0,0);
    
            //作成履歴がない場合
            if($make_count == 0){
                //請求データ作成履歴を残す
                $sql  = "INSERT INTO t_bill_make_history(\n";
                $sql .= "   bill_close_day,\n";
                $sql .= "   close_day,\n";
                $sql .= "   shop_id\n";
                $sql .= ")VALUES(\n";
                $sql .= "   '$claim_close_day', \n";
                $sql .= "   $close_day, \n";
                $sql .= "   $shop_id\n";
                $sql .= ");\n";

                $result = Db_Query($db_con, $sql);
                if($result === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }
            }
        }
    
        //請求先を作成できなかったリスト
        if(count($judge_count) > 0){
            $_SESSION[get_data] = $judge_count;
        }
/*
        //請求書を設定しなかったリスト
        if(count($no_claim_sheet_count) > 0){
            $_SESSION[no_sheet_data] = $no_claim_sheet_count;
        }

        //請求更新以前の日付を入力したリスト
        if(count($renew_claim_count) > 0){
            $_SESSION[renew_data] = $renew_claim_count;
        }       

        //今月2回目の作成
        if(count($made_slip_count) > 0){
            $_SESSION[made_data] = $made_slip_count;
        }
*/
	    Db_Query($db_con, "COMMIT;");
	    //Db_Query($db_con, "ROLLBACK;");
        header("Location:./1-2-308.php?add_flg=true");
    }else{
	    Db_Query($db_con, "ROLLBACK;");
    }
	Db_Query($db_con, "COMMIT;");
}


/****************************/
//一覧データ作成
/****************************/
//先月の1日
$now_month = date('m');
$last_date = date('Ymd',mktime(0,0,0,$now_month-1,01));
#2011-11-19 aoyama-n
#$last_m = date('Y年m月', mktime(0,0,0,$now_month-1));
$last_m = date('Y年m月', mktime(0,0,0,$now_month-1,01));

//今月の1日
$now_date = date('Ym')."01";
$now_m = date('Y年m月', mktime(0,0,0,$now_month));

//来月の1日
$next_date = date('Ymd',mktime(0,0,0,$now_month+1,01));

$sql  = "SELECT\n";
$sql .= "   close_day_list.close_day,\n";
$sql .= "   bill_close_day_list1.bill_close_day,\n";
$sql .= "   bill_close_day_list2.bill_close_day \n";
$sql .= "FROM\n";
$sql .= "   (\n";
$sql .= "       SELECT\n";
$sql .= "           DISTINCT t_client.close_day\n";
$sql .= "       FROM\n";
$sql .= "           t_client INNER JOIN t_claim\n";
$sql .= "           ON t_client.client_id = t_claim.claim_id\n";
$sql .= "       WHERE\n";
$sql .= "           t_client.shop_id = $shop_id\n";
$sql .= "           AND\n";
$sql .= "           t_client.client_div = '3'\n";
$sql .= "   )close_day_list\n";
//前月
$sql .= "   LEFT JOIN\n";
$sql .= "   (\n";
$sql .= "   SELECT\n";
$sql .= "       close_day,\n";
$sql .= "       bill_close_day\n";
$sql .= "     FROM\n";
$sql .= "       t_bill_make_history\n";
$sql .= "   WHERE\n";
$sql .= "       '$last_date' <= t_bill_make_history.bill_close_day\n";
$sql .= "   AND \n";
$sql .= "       t_bill_make_history.bill_close_day < '$now_date'\n";
$sql .= "   AND\n";
$sql .= "       t_bill_make_history.shop_id = $shop_id\n";
$sql .= "   ) AS bill_close_day_list1\n";
$sql .= "   ON close_day_list.close_day = bill_close_day_list1.close_day\n";
$sql .= "   LEFT JOIN\n";
//今月
$sql .= "   (\n";
$sql .= "   SELECT\n";
$sql .= "       close_day,\n";
$sql .= "       bill_close_day\n";
$sql .= "   FROM\n";
$sql .= "       t_bill_make_history\n";
$sql .= "   WHERE\n";
$sql .= "       '$now_date' <= t_bill_make_history.bill_close_day\n";
$sql .= "   AND \n";
$sql .= "       t_bill_make_history.bill_close_day < '$next_date'\n";
$sql .= "   AND \n";
$sql .= "       t_bill_make_history.shop_id = $shop_id\n";
$sql .= "   ) AS bill_close_day_list2\n";
$sql .= "   ON close_day_list.close_day = bill_close_day_list2.close_day\n";
$sql .= "ORDER BY cast(close_day_list.close_day as int)\n";
$sql .= ";\n";

$result = Db_Query($db_con, $sql);
$page_data = Get_Data($result);

//締日を置換
for($i = 0; $i < count($page_data); $i++){
    if($page_data[$i][0] < 29){
        $page_data[$i][0] = $page_data[$i][0]."日";
    }else{
        $page_data[$i][0] = "月末";
    }
}

/****************************/
//JavaSclipt
/****************************/
$js  = "function Dialog_Double_Post_Prevent(btn_name, hdn_name, hdn_value, str_check)\n";
$js .= "{\n";

// 確認ダイアログ表示
$js .= "    res = window.confirm(str_check+\"\\nよろしいですか？\");\n";
$js .= "    var BN = btn_name;\n";
$js .= "    var HN = hdn_name;\n";
$js .= "    var HV = hdn_value;\n";
$js .= "    if (res == true){\n";
$js .= "        dateForm.elements[BN].disabled=true;\n";
$js .= "        dateForm.elements[HN].value=HV;\n";
$js .= "        dateForm.submit();\n";
$js .= "        return true;\n";
$js .= "    }else{\n";
$js .= "        return false;\n";
$js .= "    }\n";
$js .= "}\n";

$js .= "function Text_Disabled(num){\n";

$js .= "  var dis_type    = num;\n";
$js .= "  var dis_date_y1 = \"form_claim_day1[y]\";\n";
$js .= "  var dis_date_m1 = \"form_claim_day1[m]\";\n";
$js .= "  var dis_date_d1 = \"form_claim_day1[d]\";\n";
$js .= "  var dis_date_y2 = \"form_claim_day2[y]\";\n";
$js .= "  var dis_date_m2 = \"form_claim_day2[m]\";\n";
$js .= "  var dis_date_d2 = \"form_claim_day2[d]\";\n";
$js .= "  var dis_cd1     = \"form_claim[cd1]\";\n";
$js .= "  var dis_cd2     = \"form_claim[cd2]\";\n";
$js .= "  var dis_name    = \"form_claim[name]\";\n";

$js .= "  if(dis_type == '2'){\n";
$js .= "    document.forms[0].elements[dis_date_y2].disabled = false;\n";
$js .= "    document.forms[0].elements[dis_date_m2].disabled = false;\n";
$js .= "    document.forms[0].elements[dis_date_d2].disabled = false;\n";
$js .= "    document.forms[0].elements[dis_cd1].disabled     = false;\n";
$js .= "    document.forms[0].elements[dis_cd2].disabled     = false;\n";
$js .= "    document.forms[0].elements[dis_name].disabled    = false;\n";
$js .= "    document.forms[0].elements[dis_date_y2].style.backgroundColor = \"white\";\n";
$js .= "    document.forms[0].elements[dis_date_m2].style.backgroundColor = \"white\";\n";
$js .= "    document.forms[0].elements[dis_date_d2].style.backgroundColor = \"white\";\n";
$js .= "    document.forms[0].elements[dis_cd1].style.backgroundColor = \"white\";\n";
$js .= "    document.forms[0].elements[dis_cd2].style.backgroundColor = \"white\";\n";
$js .= "    document.forms[0].elements[dis_name].style.backgroundColor = \"white\";\n";

$js .= "    document.forms[0].elements[dis_date_y1].disabled = true;\n";
$js .= "    document.forms[0].elements[dis_date_m1].disabled = true;\n";
$js .= "    document.forms[0].elements[dis_date_d1].disabled = true;\n";
$js .= "    document.forms[0].elements[dis_date_y1].style.backgroundColor = \"gainsboro\";\n";
$js .= "    document.forms[0].elements[dis_date_m1].style.backgroundColor = \"gainsboro\"\n";
$js .= "    document.forms[0].elements[dis_date_d1].style.backgroundColor = \"gainsboro\";\n";
$js .= "  }else{\n";
$js .= "    document.forms[0].elements[dis_date_y1].style.backgroundColor = \"white\";\n";
$js .= "    document.forms[0].elements[dis_date_m1].style.backgroundColor = \"white\"\n";
$js .= "    document.forms[0].elements[dis_date_d1].style.backgroundColor = \"white\";\n";

$js .= "    document.forms[0].elements[dis_date_y1].disabled = false;\n";
$js .= "    document.forms[0].elements[dis_date_m1].disabled = false;\n";
$js .= "    document.forms[0].elements[dis_date_d1].disabled = false;\n";

$js .= "    document.forms[0].elements[dis_date_y2].disabled = true;\n";
$js .= "    document.forms[0].elements[dis_date_m2].disabled = true;\n";
$js .= "    document.forms[0].elements[dis_date_d2].disabled = true;\n";
$js .= "    document.forms[0].elements[dis_cd1].disabled     = true;\n";
$js .= "    document.forms[0].elements[dis_cd2].disabled     = true;\n";
$js .= "    document.forms[0].elements[dis_name].disabled    = true;\n";
$js .= "    document.forms[0].elements[dis_date_y2].style.backgroundColor = \"gainsboro\";\n";
$js .= "    document.forms[0].elements[dis_date_m2].style.backgroundColor = \"gainsboro\";\n";
$js .= "    document.forms[0].elements[dis_date_d2].style.backgroundColor = \"gainsboro\";\n";
$js .= "    document.forms[0].elements[dis_cd1].style.backgroundColor = \"gainsboro\";\n";
$js .= "    document.forms[0].elements[dis_cd2].style.backgroundColor = \"gainsboro\";\n";
$js .= "    document.forms[0].elements[dis_name].style.backgroundColor = \"gainsboro\";\n";
$js .= "  }\n";
$js .= "}\n";




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
$page_menu = Create_Menu_h('sale','3');

/****************************/
//画面ヘッダー作成
/****************************/
//$page_header = Create_Header($page_title);
$page_header = Bill_Header($page_title);

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
    'code_value'    => "$code_value",
    'last_date'     => "$last_m",
    'now_date'      => "$now_m",
    'error'         => "$error",
    'no_claim_err'  => "$no_claim_err",
    'js'            => "$js",
));

$smarty->assign("page_data",$page_data);
$smarty->assign("err_msg",$err_msg);
$smarty->assign("pay_err",$pay_err);
$smarty->assign("sale_err",$sale_err);


//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
