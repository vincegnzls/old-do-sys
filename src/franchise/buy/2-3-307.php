<?php

/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/11　なし　　　　yamanaka-s　　システム開始日以前の日付は入力不正と判断する処理を追加
 * 　2006/10/13　なし　　　　yamanaka-s　　支払入力と同じようにFC(client_div = 3)も代行処理関係なく対象とする
 * 　2006/10/13　なし　　　　yamanaka-s　　未来の日付は締処理できないように
 * 　2006/10/16　なし　　　　yamanaka-s　　直営の場合は直営全体のデータ共有をするように変更
 * 　2006/10/19　なし　　　　yamanaka-s　　締処理の計算で対象の月にアクションを起こしていない仕入先は対象外とする
 * 　2006/10/19　なし　　　　yamanaka-s　　仕入ヘッダーテーブルのINSERT部分で取引先名を取引先名(略称)に変更
 *   2006/11/27              watanabe-k    割賦した場合の支払金額が2ヶ月分抽出されるバグの修正
 *   2006/11/27              watanabe-k    FCのデータが登録されていないバグの修正
 *   2006/11/29              watanabe-k    FCの支払日が登録されていないバグの修正
 *   2006-12-09  ban_0116    suzuki        日付をゼロ埋め
 *   2006-12-10              watanabe-k    今回支払予定額の抽出SQLに渡す引数が正しくないバグの修正
 *   2006-12-13  scl_0066    watanabe-k    ロイヤリティ伝票で仕入日が正しくないバグの修正 
 *   2006-12-14  scl_0080    watanabe-k    2ヶ月目以降の支払予定額が正しくないバグの修正 
 *   2007-01-22              watanabe-k    未来の日付で締めを行えるバグの修正
 *   2007-01-22              watanabe-k    今回支払予定額が正しくないバグの修正
 *   2007-01-22              watanabe-k    2月分の今回支払予定額が正しくないバグの修正 
 *   2007-02-22              watanabe-k    一括消費税の処理を追加 
 *   2007-03-20              watanabe-k    RtoRの伝票を作成 
 *   2007-05-31              watanabe-k    RtoRの伝票を複数作成できるように修正 
 *   2007-06-06              watanabe-k    締処理の履歴を残すように修正 
 *   2007-06-14              watanabe-k    支払処理時にシンタックスエラーが表示されるバグの修正 
 *   2007-06-16              watanabe-k    残高０の場合に間違った予定データ出来上がるバグの修正
 *   2007-06-27              watanabe-k    同締日で再作成を可能にするように変更
 *   2007-07-11             fukuda-s        「支払締」を「仕入締」に変更
 *   2007-08-21              watanabe-k    取引があり金額が０の場合にデータを削除しないように修正
 *   2007-08-22              watanabe-k    割賦仕入抽出の仕様を請求と統一
 *   2007-11-15              watanabe-k    削除処理を追加し、金額が全て０の場合という条件を追加
 *   2008-05-31              watanabe-k    ロイヤリティを0以外で起こすように修正
 *   2009-12-29              aoyama-n      税率をTaxRateクラスから取得
 *   2011-11-19              hashimoto-y   mktime関数使用方法の修正（前月を取得する際に正しい月が取得できないバグ）
 */
 
 
$page_title = "仕入締処理";

//本日の日付
$today = date('Y-m-d');

//環境設定ファイル
require_once("ENV_local.php");

//レンタル伝票作成関数ファイル
require_once(INCLUDE_DIR."rental.inc");
require_once(INCLUDE_DIR."function_keiyaku.inc");
//現モジュール内のみで使用する関数ファイル
require_once(INCLUDE_DIR."schedule_payment.inc");


//HTML_QuickForm
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$conn = Db_Connect();

// 権限チェック
$auth = Auth_Check($conn);

// 入力・変更権限無しメッセージ
//$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;

// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

//初期設定
//$def_data["form_slipout_type"] = '1';
$form->setDefaults($def_data);

// HTMLヘッダ
$html_header = Html_Header($page_title);

// HTMLフッタ
$html_footer = Html_Footer();

// 画面ヘッダー作成
// 支払予定一覧 −ボタンの色を変更 2007.06.14−
$form->addElement("button", "2-2-307", "仕入締処理", "style=color:#ff0000 onClick=\"location.href='$_SERVER[PHP_SELF]'\"".$g_button_color);

// 入力
$form->addElement("button", "2-2-301", "支払予定一覧", "onClick=\"javascript:Referer('2-3-301.php')\"");
$page_title .= "　".$form->_elements[$form->_elementIndex["2-2-307"]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex["2-2-301"]]->toHtml();
$page_header = Create_Header($page_title);
$page_header = Create_Header($page_title);


/************外部変数取得****************/
$shop_id = $_SESSION["client_id"];
$staff_id = $_SESSION["staff_id"];
$staff_name = $_SESSION["staff_name"];
//print_array($_SESSION);

#2009-12-29 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);

/***************** フォームパーツ定義 *****************/
// 実行ボタン
$form->addElement(
    "submit", 
    "submit", 
    "締処理", 
    "onClick=\"javascript:return Dialogue4('締処理を行います。')\" $disabled"
);




//締日のフォーム用
//存在する締日を取得する
$sql  = "SELECT";
$sql .= "       DISTINCT close_day";
$sql .= " FROM";
$sql .= "       t_client";
$sql .= " WHERE";
$sql .= "( \n";
//直営の場合はデータを共有する	2006-10-16
//$sql .= "       shop_id = $shop_id";
/*
if($_SESSION[group_kind] == "2"){
     $sql .= "   shop_id IN (".Rank_Sql().") ";
     $sql .= " AND \n";
     $sql .= "   client_div = '2'\n";
     $sql .= " ) \n";
     $sql .= " OR";
     $sql .= " ( \n";
     $sql .= "      client_div = '3' \n";
     $sql .= " AND \n";
     $sql .= "      shop_id = 1 \n";
}else{
     $sql .= "   shop_id = $shop_id";
     $sql .= " AND \n";
     $sql .= "        (t_client.client_div = '2' OR t_client.client_div = '3') \n";
}
*/

$sql .= "   t_client.client_div = '2' ";
$sql .= "   AND ";
$sql .= "   t_client.shop_id = $shop_id";
$sql .= ") \n";

/*		2006-10-13	仕様変更　支払入力と同じようにFCも全て抽出するように修正
$sql .= " UNION \n";				//2006-09-19	FCのみclient_divが3も対象になる為、変更
$sql .= " SELECT \n";
$sql .= "       t_client.close_day \n";
$sql .= " FROM \n";
$sql .= "       t_client \n";
$sql .= " WHERE \n";
$sql .= "       client_id IN ( \n";
$sql .= "              SELECT \n";
$sql .= "                     t_buy_h.client_id \n";
$sql .= "              FROM \n";
$sql .= "                     t_client \n";
$sql .= "              INNER JOIN t_buy_h ON \n";
$sql .= "                     t_buy_h.shop_id = $shop_id \n";
$sql .= "              WHERE \n";
$sql .= "                     t_client.client_id = t_buy_h.client_id \n";
$sql .= "              AND \n";
$sql .= "                     (intro_sale_id IS NOT NULL OR act_sale_id IS NOT NULL) \n";
$sql .= "       ); \n";
*/

$result = Db_Query($conn, $sql);
$num = @pg_num_rows($result);
$select_value[] = null;

//2006/09/12	ソート処理を追加
for($i = 0; $i < $num; $i++){
	$client_close_day[] = @pg_fetch_result($result, $i,0);
}
//print_array($client_close_day);
asort($client_close_day);
$client_close_day = array_values($client_close_day);
//print_array($client_close_day);
for($i = 0; $i < $num; $i++){
    //1〜28日
    if($client_close_day[$i] < 29 && $client_close_day[$i] != ''){
        $select_value[$client_close_day[$i]] = $client_close_day[$i]."日";
    }
    //29日以上
    elseif($client_close_day[$i] != '' && $client_close_day[$i] >= 29){
        $select_value[$client_close_day[$i]] = "月末";
    }
}


//フォームパーツ
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
$form->addGroup( $form_claim_day1, "form_claim_day1", "仕入締日","");


/*************PREPARE用SQL*************/
$data  = "SELECT";
$data .= "       t_client.client_id, \n";
$data .= "       t_client.client_name, \n";
$data .= "       t_client.client_name2, \n";
$data .= "       t_client.client_cname, \n";
$data .= "       t_client.client_cd1, \n";
$data .= "       t_client.client_cd2, \n";	//2006/09/12	新規追加
$data .= "       t_client.bank_name, \n";	//使用していない
$data .= "       t_client.b_bank_name, \n";
$data .= "       t_client.intro_ac_num, \n";
$data .= "       t_client.account_name, \n";
$data .= "       t_client.trade_id, \n";
$data .= "       t_client.close_day, \n";
$data .= "       t_client.payout_m, \n";
$data .= "       t_client.payout_d, \n";
$data .= "       t_client.coax, \n";
$data .= "       t_client.tax_div, \n";
$data .= "       t_client.tax_franct, \n";
$data .= "       t_client.c_tax_div, \n";
//$data .= "       t_client.tax_rate_n, \n";	2006/09/12
$data .= "       t_client.royalty_rate, \n";
$data .= "       t_client.shop_id, \n";
$data .= "       t_client.col_terms, \n";
$data .= "       last_payment_data.payment_close_day, \n";
$data .= "       last_payment_data.schedule_of_payment_this, \n";
$data .= "       last_payment_data.ca_balance_this, \n";
$data .= "       last_payment_data.payment_extraction_e, \n";
$data .= "       COALESCE(total_buy_payment.total_net_amount, 0) AS total_net_amount, \n";
$data .= "       COALESCE(total_buy_payment.total_tax_amount, 0) AS total_tax_amount, \n";
$data .= "       COALESCE(total_buy_close_day.total_buy_amount, 0) AS total_buy_amount, \n";
$data .= "       COALESCE(total_buy_close_day.total_no_tax_buy_amount, 0) AS total_no_tax_buy_amount, \n";
$data .= "       COALESCE(total_kup.total_net_kup_amount, 0) AS total_net_kup_amount, \n";
$data .= "       COALESCE(total_kup.total_tax_net_amount, 0) AS total_tax_net_amount, \n";
$data .= "       COALESCE(total_split_pay_amount.total_split_pay_amount, 0) AS total_split_pay_amount, \n";
$data .= "       COALESCE(total_pay1.total_pay_amount, 0) AS total_pay_amount, \n";					//2006/09/22	修正
$data .= "       COALESCE(total_pay2.total_adjustment_amount, 0) AS total_adjustment_amount, \n";	//2006/09/22	修正
$data .= "       COALESCE(total_split_pay_kup_amount.total_split_pay_balance_amount, 0) AS total_split_pay_balance_amount, \n";
$data .= "       COALESCE(total_royalty_amount.total_royalty_buy_amount, 0) AS total_royalty_buy_amount, \n";
$data .= "       COALESCE(total_royalty_amount.total_no_tax_royalty_buy_amount, 0) AS total_no_tax_royalty_buy_amount, \n";
$data .= "       t_client.col_terms, \n";
$data .= "       t_client.client_div, \n";		//2006-09-19	新規追加
$data .= "       t_client.head_flg \n";
/*******前回の買掛情報を抽出********/
$data .= " FROM  \n";
$data .= "      t_client LEFT JOIN ( \n";
$data .= "               SELECT \n";
$data .= "                      t_schedule_payment.client_id,  \n";
$data .= "                      t_schedule_payment.payment_close_day,  \n";
//$data .= "                      t_schedule_payment.schedule_of_payment_this,  \n";

$data .= "                      CASE t_schedule_payment.payout_schedule_id ";
$data .= "                          WHEN 0 THEN 0 \n";
$data .= "                          ELSE schedule_of_payment_this ";
$data .= "                      END AS schedule_of_payment_this, ";

$data .= "                      t_schedule_payment.ca_balance_this,  \n";
$data .= "                      t_schedule_payment.payment_extraction_e \n";
$data .= "               FROM \n";
$data .= "                      t_schedule_payment \n";
$data .= "               WHERE \n";
$data .= "                      schedule_payment_id = ( \n";
$data .= "                               SELECT \n";
$data .= "                                      MAX(schedule_payment_id) \n";
$data .= "                               FROM \n";
$data .= "                                      t_schedule_payment \n";
$data .= "                               WHERE \n";
$data .= "                                      client_id = $1 \n";
$data .= "                               AND \n";
$data .= "                                      shop_id = $2 \n";
$data .= "                               GROUP BY \n";
$data .= "                                      client_id, shop_id \n";
$data .= "                               ) \n";
$data .= "              ) AS last_payment_data \n";
$data .= "      ON t_client.client_id = last_payment_data.client_id \n";
$data .= "      LEFT JOIN ( \n";
/*******掛仕入の買い上げ額を抽出(課税単位が伝票単位)********/
$data .= "               SELECT \n";
$data .= "                      COALESCE( SUM(net_amount * CASE trade_id WHEN 21 THEN 1 ELSE -1 END))AS total_net_amount, \n";
$data .= "                      COALESCE( SUM(tax_amount * CASE trade_id WHEN 21 THEN 1 ELSE -1 END))AS total_tax_amount, \n";
$data .= "                      client_id \n";
$data .= "               FROM \n";
$data .= "                      t_buy_h \n";
$data .= "               WHERE \n";
$data .= "                      client_id = $1 \n";
$data .= "               AND \n";
$data .= "                      shop_id = $2 \n";
$data .= "               AND \n";
$data .= "                      buy_day > ( \n";
$data .= "                             SELECT \n";
$data .= "                                    COALESCE (MAX(payment_close_day), '".START_DAY."') \n";
$data .= "                             FROM \n";
$data .= "                                    t_schedule_payment \n";
$data .= "                             WHERE \n";
$data .= "                                    client_id = $1 \n";
$data .= "                             AND \n";
$data .= "                                    shop_id = $2) \n";
$data .= "               AND \n";
$data .= "                      buy_day <= $3 \n";
$data .= "               AND \n";
$data .= "                      trade_id IN (21,23,24) \n";
$data .= "               GROUP BY client_id, shop_id \n";
$data .= "               ) AS total_buy_payment \n";
$data .= "      ON t_client.client_id = total_buy_payment.client_id \n";
$data .= "      LEFT JOIN ( \n";
/*******掛仕入の買い上げ額を抽出(課税単位が締日単位)********/
$data .= "                SELECT \n";
$data .= "                       COALESCE( SUM(CASE WHEN t_buy_d.tax_div = '1' THEN t_buy_d.buy_amount * CASE t_buy_h.trade_id WHEN 21 THEN 1 ELSE -1 END END), 0) AS total_buy_amount, \n";
$data .= "                       COALESCE( SUM(CASE WHEN t_buy_d.tax_div = '3' THEN t_buy_d.buy_amount * CASE t_buy_h.trade_id WHEN 21 THEN 1 ELSE -1 END END), 0) AS total_no_tax_buy_amount, \n";
$data .= "                       t_buy_h.client_id \n";
$data .= "                FROM \n";
$data .= "                       t_buy_h \n";
$data .= "                INNER JOIN \n";
$data .= "                       t_buy_d \n";
$data .= "                ON \n";
$data .= "                       t_buy_h.buy_id = t_buy_d.buy_id \n";
$data .= "                WHERE \n";
$data .= "                       t_buy_h.client_id = $1 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.shop_id = $2 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.buy_day > ( \n";
$data .= "                                   SELECT \n";
$data .= "                                          COALESCE (MAX(payment_close_day), '".START_DAY."') \n";
$data .= "                                   FROM \n";
$data .= "                                          t_schedule_payment \n";
$data .= "                                   WHERE \n";
$data .= "                                          client_id = $1 \n";
$data .= "                                   AND \n";
$data .= "                                          shop_id = $2) \n";
$data .= "                AND \n"; 
$data .= "                       t_buy_h.buy_day <= $3 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.trade_id IN (21,23,24) \n";
$data .= "                GROUP BY t_buy_h.client_id \n";
$data .= "               ) AS total_buy_close_day \n";
$data .= "      ON t_client.client_id = total_buy_close_day.client_id \n";
$data .= "      LEFT JOIN ( \n";
/*******「割賦仕入の買い上げ額(全額)」********/
$data .= "                SELECT \n";
$data .= "                       SUM(net_amount) AS total_net_kup_amount, \n";	//total_net_amountからtotal_net_kup_amountへ名前を変更
$data .= "                       SUM(tax_amount) AS total_tax_net_amount, \n";
$data .= "                       t_buy_h.client_id \n";
$data .= "                FROM \n";
$data .= "                       t_buy_h \n";
$data .= "                WHERE \n";
$data .= "                       client_id = $1 \n";
$data .= "                AND \n";
$data .= "                       shop_id = $2 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.buy_day > ( \n";
$data .= "                                   SELECT \n";
$data .= "                                           COALESCE( MAX(payment_close_day), '".START_DAY."') \n";
$data .= "                                   FROM \n";
$data .= "                                           t_schedule_payment \n";
$data .= "                                   WHERE \n";
$data .= "                                           client_id = $1 \n";
$data .= "                                   AND \n";
$data .= "                                           shop_id = $2) \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.buy_day <= $3 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.trade_id = 25 \n";
$data .= "                GROUP BY t_buy_h.client_id \n";
$data .= "               ) AS total_kup \n";
$data .= "      ON t_client.client_id = total_kup.client_id \n";
$data .= "      LEFT JOIN ( \n";
/*******掛仕入と割賦のロイヤリティ額を抽出********/
$data .= "                SELECT \n";
$data .= "                       COALESCE( SUM( CASE WHEN t_buy_d.tax_div = '1' THEN t_buy_d.buy_amount * CASE WHEN t_buy_h.trade_id = 21 OR t_buy_h.trade_id = 25 OR t_buy_h.trade_id = 71 THEN 1 ELSE -1 END END ) , 0 ) AS total_royalty_buy_amount, \n";
$data .= "                       COALESCE(  SUM( CASE WHEN t_buy_d.tax_div = '3' THEN t_buy_d.buy_amount * CASE WHEN t_buy_h.trade_id = 21 OR t_buy_h.trade_id = 25 OR t_buy_h.trade_id = 71 THEN 1 ELSE -1 END END ) , 0  ) AS total_no_tax_royalty_buy_amount, \n";
$data .= "                       t_buy_h.client_id \n";
$data .= "                FROM \n";
$data .= "                       t_buy_h \n";
$data .= "                INNER JOIN \n";
$data .= "                       t_buy_d \n";
$data .= "                ON \n";
$data .= "                       t_buy_h.buy_id = t_buy_d.buy_id \n";
$data .= "                WHERE \n";
$data .= "                       t_buy_h.client_id = $1 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.shop_id = $2 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.buy_day > ( \n";
$data .= "                                   SELECT \n";
$data .= "                                            COALESCE (MAX(payment_close_day), '".START_DAY."') \n";
$data .= "                                   FROM \n";
$data .= "                                            t_schedule_payment \n";
$data .= "                                   WHERE \n";
$data .= "                                            client_id = $1 \n";
$data .= "                                   AND \n";
$data .= "                                            shop_id = $2) \n";
$data .= "                AND \n";
$data .= "                      t_buy_h.buy_day <= $3 \n";
$data .= "                AND \n";
$data .= "                      t_buy_h.trade_id IN (21, 23, 24, 25, 71, 73, 74) \n";
$data .= "                AND \n";
$data .= "                      t_buy_d.royalty = '1' \n";
$data .= "                GROUP BY t_buy_h.client_id \n";
$data .= "                 ) AS total_royalty_amount \n";
$data .= "      ON t_client.client_id = total_royalty_amount.client_id \n";
$data .= "      LEFT JOIN ( \n";
/*******割賦仕入の支払金額を抽出********/
$data .= "                SELECT \n";
$data .= "                       SUM(t_amortization.split_pay_amount) AS total_split_pay_amount, \n";
$data .= "                       t_buy_h.client_id \n";
$data .= "                FROM \n";
$data .= "                       t_buy_h \n";
$data .= "                INNER JOIN \n";
$data .= "                        t_amortization \n";
$data .= "                ON \n";
$data .= "                       t_buy_h.buy_id = t_amortization.buy_id \n";
$data .= "                WHERE \n";
$data .= "                       t_buy_h.client_id = $1 \n";
$data .= "                AND \n";
$data .= "                       t_buy_h.shop_id = $2 \n";
$data .= "                AND \n";
$data .= "                       t_amortization.pay_day > ( \n";
$data .= "                                   SELECT \n";
$data .= "                                            COALESCE (MAX(payment_close_day), '".START_DAY."') \n";
$data .= "                                   FROM \n";
$data .= "                                            t_schedule_payment \n";
$data .= "                                   WHERE \n";
$data .= "                                            client_id = $1 \n";
$data .= "                                   AND \n";
$data .= "                                            shop_id = $2) \n";
$data .= "                AND \n";
$data .= "                      t_amortization.pay_day <= $3 \n";
$data .= "                AND \n";
$data .= "                      t_buy_h.trade_id = 25 \n";
$data .= "                GROUP BY \n";
$data .= "                      t_buy_h.client_id \n";
$data .= "               ) AS total_split_pay_amount \n";
$data .= "      ON t_client.client_id = total_split_pay_amount.client_id \n";
$data .= "      LEFT JOIN ( \n";
/*******支払額を抽出********/		//2006-09-22	修正
$data .= "                SELECT \n";
$data .= "                       SUM (t_payout_d.pay_amount) AS total_pay_amount, \n";
$data .= "                       t_payout_h.client_id \n";
$data .= "                FROM \n";
$data .= "                       t_payout_h \n";
$data .= "                INNER JOIN t_payout_d \n";
$data .= "                ON t_payout_h.pay_id = t_payout_d.pay_id \n";
$data .= "                WHERE \n";
$data .= "                       t_payout_h.client_id = $1 \n";
$data .= "                AND \n";
$data .= "                       t_payout_h.shop_id = $2 \n";
$data .= "                AND \n";
$data .= "                       t_payout_h.pay_day > ( \n";
$data .= "                                   SELECT \n";
$data .= "                                            COALESCE ( MAX(payment_close_day), '".START_DAY."') \n";
$data .= "                                   FROM \n";
$data .= "                                            t_schedule_payment \n";
$data .= "                                   WHERE \n";
$data .= "                                            client_id = $1 \n";
$data .= "                                   AND \n";
$data .= "                                            shop_id = $2) \n";
$data .= "                AND t_payout_h.pay_day <= $3 \n";
$data .= "                AND buy_id IS NULL \n";
$data .= "                GROUP BY t_payout_h.client_id \n";
$data .= "               ) AS total_pay1 \n";
$data .= "      ON t_client.client_id = total_pay1.client_id \n";
$data .= "      LEFT JOIN ( \n";

/*******支払額を抽出********/
$data .= "                SELECT \n";
$data .= "                       SUM (CASE WHEN trade_id = 46 THEN t_payout_d.pay_amount END ) AS total_adjustment_amount, \n";
$data .= "                       t_payout_h.client_id \n";
$data .= "                FROM \n";
$data .= "                       t_payout_h \n";
$data .= "                INNER JOIN t_payout_d \n";
$data .= "                ON t_payout_h.pay_id = t_payout_d.pay_id \n";
$data .= "                WHERE \n";
$data .= "                       t_payout_h.client_id = $1 \n";
$data .= "                AND \n";
$data .= "                       t_payout_h.shop_id = $2 \n";
$data .= "                AND \n";
$data .= "                       t_payout_h.pay_day > ( \n";
$data .= "                                   SELECT \n";
$data .= "                                            COALESCE ( MAX(payment_close_day), '".START_DAY."') \n";
$data .= "                                   FROM \n";
$data .= "                                            t_schedule_payment \n";
$data .= "                                   WHERE \n";
$data .= "                                            client_id = $1 \n";
$data .= "                                   AND \n";
$data .= "                                            shop_id = $2) \n";
$data .= "                AND t_payout_h.pay_day <= $3 \n";
$data .= "                GROUP BY t_payout_h.client_id \n";
$data .= "               ) AS total_pay2 \n";
$data .= "      ON t_client.client_id = total_pay2.client_id \n";
$data .= "      LEFT JOIN ( \n";

/*******割賦仕入の残高を抽出********/
$data .= "                SELECT \n";
$data .= "                      SUM (t_amortization.split_pay_amount) AS total_split_pay_balance_amount, \n";
$data .= "                      t_buy_h.client_id \n";
$data .= "                FROM \n";
$data .= "                      t_buy_h \n";
$data .= "                INNER JOIN t_amortization \n";
$data .= "                ON t_buy_h.buy_id = t_amortization.buy_id \n";
$data .= "                WHERE \n";
$data .= "                      t_buy_h.client_id = $1 \n";
$data .= "                AND \n";
$data .= "                      t_buy_h.shop_id = $2 \n";
$data .= "                AND \n";
$data .= "                      t_amortization.pay_day > $3 \n";
$data .= "                AND \n";
$data .= "                      t_buy_h.trade_id = 25 \n";
$data .= "                GROUP BY t_buy_h.client_id \n";
$data .= "                ) AS total_split_pay_kup_amount \n";
$data .= "      ON t_client.client_id = total_split_pay_kup_amount.client_id \n";
$data .= " WHERE \n";
$data .= "       t_client.close_day = $4 \n";
$data .= " AND \n";
$data .= "       t_client.client_id = $1 \n";


$sql = " PREPARE get_schedule(int, int, date, varchar) AS $data";
Db_Query($conn, $sql);


//支払予定テーブルから今回の支払予定の対象となるデータを抽出
$data  = "           SELECT \n";
$data .= "                 SUM (account_payable) AS total_account_payable, \n";
$data .= "                 SUM (installment_purchase) AS total_installment_purchase \n";
$data .= "           FROM \n";
$data .= "                 t_schedule_payment \n";
$data .= "           WHERE \n";
$data .= "                 t_schedule_payment.client_id = $1 \n";
$data .= "           AND \n";
$data .= "                 t_schedule_payment.shop_id = $2 \n";
$data .= "           AND \n";
//$data .= "                 payment_expected_date > $3 \n";
$data .= "          payout_schedule_id IS NULL \n";
$data .= "           AND \n";

$data .= "          payment_expected_date <= $4";

//$data .= "          GROUP BY payment_close_day, t_schedule_payment.schedule_payment_id \n";
//$data .= "          GROUP BY payment_close_day \n";

//$sql = " PREPARE get_schedule_payment(int, int, date, date, int, int) AS $data";
$sql = " PREPARE get_schedule_payment(int, int, date, date) AS $data";
Db_Query($conn, $sql);


//前回支払予定日を支払予定テーブルから取得する
$data  = " SELECT \n";
$data .= "       MAX(payment_expected_date) AS last_expected_day \n";
$data .= " FROM \n";
$data .= "       t_schedule_payment \n";
$data .= " WHERE \n";
$data .= "       t_schedule_payment.client_id = $1 \n";
$data .= " AND \n";
$data .= "       t_schedule_payment.shop_id = $2 \n";

$sql = " PREPARE get_expected_date(int, int) AS $data";

Db_Query($conn, $sql);


//分割支払額を対象の期間で再度抽出(前回の支払予定締日[2-4] 〜 [11-15])
$data  = " SELECT \n";
$data .= "         SUM(t_amortization.split_pay_amount) AS re_total_split_pay_amount, \n";
$data .= "         t_buy_h.client_id \n";
$data .= " FROM \n";
$data .= "         t_buy_h \n";
$data .= " INNER JOIN \n";
$data .= "         t_amortization \n";
$data .= " ON \n";
$data .= "         t_buy_h.buy_id = t_amortization.buy_id \n";
$data .= " WHERE \n";
$data .= "         t_buy_h.client_id = $1 \n";
$data .= " AND \n";
$data .= "         t_buy_h.shop_id = $2 \n";
$data .= " AND \n";
//期間で集計するのではなく、支払予定IDがNULLのものを集計対象にするように修正
//$data .= "         t_amortization.pay_day > $3 \n";
$data .= "        t_amortization.schedule_payment_id IS NULL \n";
$data .= " AND \n";
/*
$data .= "         t_amortization.pay_day <=  ( \n";
$data .= "                 SELECT \n";
$data .= "                        CASE WHEN payout_d = '29' THEN \n";
$data .= "                             SUBSTR(TO_DATE(SUBSTR($4,1,8) || 01, 'YYYY-MM-DD') + (($5 + 1 ) * interval '1 month') - interval '1 day',1,10) \n";
$data .= "                        ELSE \n";
$data .= "                             SUBSTR(TO_DATE($4, 'YYYY-MM-DD') + ($5 * interval '1 month'), 1, 8) || LPAD($6,2,0) \n";
$data .= "                        END \n";
$data .= "                 FROM \n";
$data .= "                        t_client \n";
$data .= "                 WHERE \n";
$data .= "                        client_id = $1 \n";
$data .= "                 AND \n";
$data .= "                        shop_id = $2 \n";
$data .= "                 )\n";
*/
$data .= "      t_amortization.pay_day <= $4 \n";
$data .= " AND \n";
$data .= "         t_buy_h.trade_id = 25 \n";
$data .= " GROUP BY \n";
$data .= "         t_buy_h.client_id \n";

//$sql = " PREPARE re_total_split_pay_amount(int, int, date, date, int, int) AS $data";
$sql = " PREPARE re_total_split_pay_amount(int, int, date, date) AS $data";

Db_Query($conn, $sql);


//UPDATE用PREPARE
$data  = "UPDATE t_schedule_payment set \n";
$data .= "       schedule_of_payment_this = $1, \n";
$data .= "       installment_payment_this = $2, \n";
$data .= "       last_update_day = $4 \n";
$data .= "WHERE \n";
$data .= "       schedule_payment_id = $3";

$sql = "PREPARE update_t_schedule_payment (bigint, bigint, bigint, date) AS $data";
Db_Query($conn, $sql);


/******** 判定 *********/
$post_jikkou = ($_POST["submit"] == "締処理") ? true : false;

//締処理ボタン押下フラグがtrueの場合
if($post_jikkou == true){
	//トランザクション開始
	Db_Query($conn, "BEGIN;");


	//日付取得
	$post_close_day_y = $_POST["form_claim_day1"]["y"];
	$post_close_day_m = $_POST["form_claim_day1"]["m"];
	$post_close_day_d = $_POST["form_claim_day1"]["d"];
	
	//入力値チェック
	if($post_close_day_y == null || $post_close_day_m == null || $post_close_day_d == 0){
		$payment_day_flg = true;
	}
	else{
	
		//日付計算	入力された日付 OR 入力された月の月末を返す
	    $post_close_day = set_close_day($post_close_day_y,$post_close_day_m,$post_close_day_d);
		
		//月末指定の場合
		if($post_close_day_d >= 29){
			
			//レアケース 閏年以外の2月判定処理 月末を28日に変換する
			$day = $post_close_day_y.'-'.$post_close_day_m.'-'.'01';
			$check_last_day = date("t",strtotime($day));
			
			if($post_close_day_m == '2' || $post_close_day_m == '02'){
				$check_day = $check_last_day;
			}
			//閏年の場合は29日をセット
			else{
				$check_day = $check_last_day;
			}
		}
		//月末以外
		else{
			if($post_close_day_d < 10){
				$check_day = str_pad($post_close_day_d, 2, 0, STR_PAD_LEFT);
			}
			else{
				$check_day = $post_close_day_d;
			}
		}
		//日付妥当性チェック
		//$payment_day_flg = (checkdate($post_close_day_m, $check_day, $post_close_day_y) == false)? true : false ;		2006/09/21	修正
		$payment_day_flg = (checkdate((int)$post_close_day_m, (int)$check_day, (int)$post_close_day_y) == false)? true : false ;
	
		//年月の範囲を指定する用の年月日を作成する sample_day = 入力された日
		$sample_day = str_pad($post_close_day_y,4,0,STR_PAD_LEFT).'-'.str_pad($post_close_day_m,2,0,STR_PAD_LEFT).'-'.$check_day;
		
		//2006-10-11	入力された日付がシステム開始日より過去の場合はエラーとする
		if(strtotime($post_close_day) < strtotime(START_DAY)){
			$payment_day_flg = true;
		}
		
        $renew_day_flg = Payment_Monthly_Renew_Check($conn, $sample_day);

		//2006-10-13	未来の締処理は不正
//		$future_flg = (date('Ymt') < $post_close_day_y.$post_close_day_m.$post_close_day_d)? true : false;

        $target_day_y = str_pad($post_close_day_y,4,0,STR_PAD_LEFT);
        $target_day_m = str_pad($post_close_day_m,2,0,STR_PAD_LEFT);
        $target_day_d = str_pad($post_close_day_d,2,0,STR_PAD_LEFT);

        $target_day = $target_day_y.$target_day_m.$target_day_d;
        $future_flg = (date('Ymd') < $target_day)? true : false;
	}
	
	//請求日に妥当な日付が入力された場合
    if($payment_day_flg == false && $future_flg == false && $renew_day_flg != true){
		
		//締日(日のみ)として使用される変数作成
		//月末
		if($post_close_day_d >= 29){
				$serch_post_day = 29;
			}
		//月末以外
		else{
			//日付が一桁
			if($post_close_day_d < 10){
				$serch_post_day = str_pad($post_close_day_d, 2, 0, STR_PAD_LEFT);
			}
			//日付が二桁
			else{
				$serch_post_day = $post_close_day_d;
			}
		}
		
		/*******ロイヤリティの商品のデータを抽出(FCのみの機能)********/
/*
		$sql  = " SELECT \n";
		$sql .= "       goods_cd, \n";					//[14-1]商品コード
		$sql .= "       goods_name, \n";				//[14-2]商品名
		$sql .= "       royalty as royalty_div \n";		//[14-3]ロイヤリティ　AS　ロイヤリティ区分
		$sql .= " FROM \n";
		$sql .= "       t_goods \n";
		$sql .= " WHERE \n";
		$sql .= "       goods_cd = '0000002' \n";
		
		$result = Db_Query($conn, $sql);
		$royalty_info = @pg_fetch_all($result);

        /******一括消費税の商品のデータを抽出**************************/
/*
        $sql  = "SELECT \n";
        $sql .= "   goods_id, \n";
        $sql .= "   goods_cd, \n";
        $sql .= "   goods_name, \n";
        $sql .= "   royalty as royalty_div \n";
        $sql .= "FROM \n";
        $sql .= "   t_goods \n";
        $sql .= "WHERE \n";
        $sql .= "   goods_cd = '0000001' ";
        $sql .= ";";

        $result = Db_Query($conn, $sql);
        $lump_info = @pg_fetch_all($result);
	
		/*******自分の情報を取得する********/
		$sql  = " SELECT \n";
        #2009-12-29 aoyama-n
		#$sql .= "       tax_rate_n, \n";
		$sql .= "       royalty_rate \n";
		$sql .= " FROM \n";
		$sql .= "       t_client \n";
		$sql .= " WHERE \n";
		$sql .= "       client_id = $shop_id ";
		
		$result = Db_Query($conn, $sql);
		$my_base_info = @pg_fetch_all($result);

		//値をキャスト
        #2009-12-29 aoyama-n
		#$base_info["tax_rate_n"] = (int)$my_base_info[0]["tax_rate_n"];
		$base_info["royalty_rate"] = (int)$my_base_info[0]["royalty_rate"];
		
		/*******入力された締日で絞った仕入先を抽出********/
		$sql  = " SELECT \n";
		$sql .= "        t_client.client_id, \n";
        $sql .= "        t_client.head_flg \n";
		$sql .= " FROM \n";
		$sql .= "        t_client \n";
		$sql .= " WHERE \n";
		$sql .= "        t_client.close_day = ".(int)$serch_post_day." \n";
		$sql .= " AND \n";
        $sql .= "        t_client.client_div = '2' ";
        $sql .= " AND \n";
        $sql .= "        t_client.shop_id = $shop_id ";
        $sql .= ";";
//        $sql .= "((t_client.client_div = '2' AND shop_id = $shop_id) OR t_client.client_div = '3')";

		$result = Db_Query($conn, $sql);
		$close_day_client_id_cast = @pg_fetch_all($result);

		//2つの値をキャスト
		for($i=0; $i < count($close_day_client_id_cast); $i++){
			$close_day_client_id[$i]["client_id"] = (int)$close_day_client_id_cast[$i]["client_id"];
			$close_day_client_id[$i]["head_flg"] = $close_day_client_id_cast[$i]["head_flg"];
//			$close_day_client_id[$i]["tax_rate_n"] = (int)$close_day_client_id_cast[$i]["tax_rate_n"];
		}

		/*******過去の年月を締めようとしていないか********/
/*
		for($i=0; $i < count($close_day_client_id); $i++){
			$sql  = " SELECT \n";
			$sql .= "        schedule_payment_id \n";
			$sql .= " FROM \n";
			$sql .= "        t_schedule_payment \n";
			$sql .= " WHERE \n";
			$sql .= "        client_id = ".$close_day_client_id[$i]["client_id"]. " \n";
			$sql .= "   AND \n";
			$sql .= "        shop_id = $shop_id \n";
			$sql .= "   AND \n";
			$sql .= "        payment_close_day > '$sample_day' \n";
			$sql .= " ; \n";

			$result = Db_Query($conn, $sql);
			$last_id_count = @pg_fetch_all($result);
		}
		if(count($last_id_count) > 0 && $last_id_count != null){
			//過去エラーフラグ設定
			$last_month_flg = true;
		}
*/		
	}
	
	/****************************/
	//エラー処理
	/****************************/
	//締日エラーフラグがtrueの場合
	if($payment_day_flg == true){
	    $form->setElementError("form_claim_day1","指定した日付は妥当ではありません。");
	}

/*	
	//過去エラーフラグがtrueの場合
	if($last_month_flg == true){
	    $form->setElementError("form_claim_day1","指定した過去の日付は妥当ではありません。");
	}
*/	
	//2006-10-13	追記
	//未来エラーフラグがtrueの場合
	if($future_flg == true){
		$form->setElementError("form_claim_day1","指定した未来の日付は妥当ではありません。");
	}

    if($renew_day_flg === true){
		$form->setElementError("form_claim_day1","月次更新日以前の日付で仕入締処理は行なえません。");
    }
	
	//エラーフラグがtrueではない場合のみ処理を行う
//	if($payment_day_flg != true && $last_month_flg != true && $future_flg != true && $renew_day_flg != true){
	if($payment_day_flg != true && $future_flg != true && $renew_day_flg != true){


		for($i= 0; $i < count($close_day_client_id); $i++){

            #2009-12-29 aoyama-n
            $tax_rate_obj->setTaxRateDay($sample_day);
            $tax_rate = $tax_rate_obj->getClientTaxRate($close_day_client_id[$i][client_id]);
            $base_info[$i]["tax_rate_n"] = $tax_rate;
/*
			$sql  = " SELECT \n";
			$sql .= "       COUNT(schedule_payment_id) \n";
			$sql .= " FROM \n";
			$sql .= "       t_schedule_payment \n";
			$sql .= " WHERE \n";
			$sql .= "       t_schedule_payment.shop_id = $shop_id \n";
			$sql .= " AND \n";
			$sql .= "       t_schedule_payment.payment_close_day = '$sample_day' \n";
			
			$result = Db_Query($conn, $sql);
			$num = @pg_fetch_result($result, 0, 0);
			
			if($num > 0){
				$form->setElementError("form_claim_day1","指定した締日のデータは作成済みです。");
				$duplicate_err = true;
				break;
			}
*/
		    //指定した仕入締処理期間内に日付更新していない支払データを抽出
		    //支払テーブルの更新フラグが'f'のデータ
			$sql  = " SELECT \n";
			$sql .= "        t_payout_h.client_id \n";
			$sql .= " FROM \n";
			$sql .= "        t_payout_h \n";
			$sql .= " WHERE \n";
			$sql .= "        t_payout_h.client_id = ".$close_day_client_id[$i][client_id]." \n";
			$sql .= " AND \n";
			$sql .= "        t_payout_h.pay_day > \n";
			$sql .= "            ( \n";
			$sql .= "              SELECT \n";
			$sql .= "                     COALESCE(MAX(payment_close_day), '".START_DAY."')\n";
			$sql .= "              FROM \n";
			$sql .= "                     t_schedule_payment \n";
			$sql .= "              WHERE \n";
			$sql .= "                     t_schedule_payment.client_id = ".$close_day_client_id[$i][client_id]." \n";
			$sql .= "              AND \n";
			$sql .= "                     shop_id = $shop_id \n";
			$sql .= "            ) \n";
			$sql .= " AND \n";
			$sql .= "        t_payout_h.pay_day <= '$sample_day' \n";
			$sql .= " AND \n";
			$sql .= "        t_payout_h.shop_id = '$shop_id' \n";
			$sql .= " AND \n";
			$sql .= "        t_payout_h.renew_flg = 'f' \n";
			$sql .= " GROUP BY t_payout_h.client_id \n";
			
			$sql .= " UNION ALL \n";
			
			//仕入ヘッダテーブルの更新フラグが'f'のデータ
			$sql .= " SELECT \n";
			$sql .= "        t_buy_h.client_id \n";
			$sql .= " FROM \n";
			$sql .= "        t_buy_h \n";
			$sql .= " INNER JOIN \n";
			$sql .= "        t_amortization\n";
			$sql .= " ON t_buy_h.buy_id = t_amortization.buy_id \n";
			$sql .= " WHERE \n";
			$sql .= "        t_buy_h.client_id = ".$close_day_client_id[$i][client_id]." \n";
			$sql .= " AND \n";
			$sql .= "        t_buy_h.renew_flg = 'f' \n";
			$sql .= " AND \n";
			$sql .= "        t_buy_h.shop_id = $shop_id \n";
			$sql .= " AND \n";
			$sql .= "        t_amortization.pay_day > \n";
			$sql .= "            ( \n";
			$sql .= "              SELECT \n";
			$sql .= "                     COALESCE(MAX(payment_close_day), '".START_DAY."')\n";
			$sql .= "              FROM \n";
			$sql .= "                     t_schedule_payment \n";
			$sql .= "              WHERE \n";
			$sql .= "                     t_schedule_payment.client_id = ".$close_day_client_id[$i][client_id]." \n";
			$sql .= "              AND \n";
			$sql .= "                     shop_id = $shop_id \n";
			$sql .= "            ) \n";
			$sql .= " AND \n";
			$sql .= "        t_amortization.pay_day <= '$sample_day' \n";
			
			$sql .= " UNION ALL \n";
			
			//仕入ヘッダテーブルの更新フラグが'f'のデータ
			$sql .= " SELECT \n";
			$sql .= "        t_buy_h.client_id \n";
			$sql .= " FROM \n";
			$sql .= "        t_buy_h \n";
			$sql .= " WHERE \n";
			$sql .= "        t_buy_h.client_id = ".$close_day_client_id[$i][client_id]." \n";
			$sql .= " AND \n";
			$sql .= "        t_buy_h.renew_flg = 'f' \n";
			$sql .= " AND \n";
			$sql .= "        t_buy_h.shop_id = $shop_id \n";
			$sql .= " AND \n";
			$sql .= "        t_buy_h.buy_day > \n";
			$sql .= "            ( \n";
			$sql .= "              SELECT \n";
			$sql .= "                     COALESCE(MAX(payment_close_day), '".START_DAY."')\n";
			$sql .= "              FROM \n";
			$sql .= "                     t_schedule_payment \n";
			$sql .= "              WHERE \n";
			$sql .= "                     t_schedule_payment.client_id = ".$close_day_client_id[$i][client_id]." \n";
			$sql .= "              AND \n";
			$sql .= "                     shop_id = $shop_id \n";
			$sql .= "            ) \n";
			$sql .= " AND \n";
			$sql .= "        t_buy_h.buy_day <= '$sample_day' \n";
			$sql .= " ; \n";

			$result = Db_Query($conn, $sql);
			$unrenew_count = @pg_num_rows($result);
			
			//仕入、または支払いに関して日付更新処理がされていない場合はrenew_flgをtrueにする
			if($unrenew_count > 0){
				$renew_flg = true;
				//日付更新がされていない場合は警告メッセージを表示する
				$form->setElementError("form_claim_day1","仕入締処理期間に日次更新を行っていないデータがあります");
			}
		}	

		if($duplicate_err != true && $renew_flg != true){
            //仕入締履歴テーブルに登録
            Add_Payment_Make_History($conn, $sample_day, $post_close_day_d);

			/**********SELECT-1*************/
			//ユーザの情報を取得する
			for($i=0; $i < count($close_day_client_id); $i++){

                //入力した締日以降に支払予定データが既に存在する場合、
                //支払予定データを作成しない。
                $remake_flg = Check_Remake_Data($conn, $close_day_client_id[$i]["client_id"], $sample_day);

                if($remake_flg === true){
				    $buying_for_appoint[$i] = null;
                    continue;
                }       

                //仕入先アメニティの場合
                if($close_day_client_id[$i]["head_flg"] == 't'){
                    //------------------------------------------------------------
                    //レンタルの伝票を作成
                    //------------------------------------------------------------
                    $sql  = "SELECT";
                    $sql .= "   COALESCE(MAX(payment_close_day), '".START_DAY."')\n";        
                    $sql .= "FROM ";
                    $sql .= "   t_schedule_payment \n"; 
                    $sql .= "WHERE ";
                    $sql .= "   t_schedule_payment.client_id = (SELECT \n";
                    $sql .= "                                       client_id \n";
                    $sql .= "                                   FROM \n";
                    $sql .= "                                       t_client \n";
                    $sql .= "                                   WHERE ";
                    $sql .= "                                       head_flg = 't' \n";
                    $sql .= "                                       AND \n";
                    $sql .= "                                       shop_id = $shop_id \n";
                    $sql .= "                                       AND \n";
                    $sql .= "                                       client_div = '2' \n";
                    $sql .= "                                   )" ;
                    $sql .= "   AND ";
                    $sql .= "   shop_id = $shop_id ";

                    $day_res = Db_Query($conn, $sql);
                    $buy_day_s = pg_fetch_result($day_res,0,0);
                    $buy_day_e = $sample_day;

                    $ary_buy_id = Regist_Buy_Rental_Range($conn,$buy_day_s,$buy_day_e); 
                    //------------------------------------------------------------
                }

                //未更新の支払データがある場合は支払データを作成しない。
                $result = Check_Non_Update_Data ($conn, $close_day_client_id[$i]["client_id"]);
                if(is_array($result)){
                    $buying_for_appoint[$i] = null; 
                    $non_update_err[] = $result;
                    continue;
                } 

                //初回の場合のみ初期残高を登録
                Add_First_ap_Balance($conn, $close_day_client_id[$i]["client_id"], $sample_day);

				$sql = "EXECUTE get_schedule('".$close_day_client_id[$i][client_id]."',$shop_id,'$sample_day',".(int)$serch_post_day."); \n";

				$result = Db_Query($conn, $sql);
				$var[$i] = @pg_fetch_all($result);
				//入れなおし
				$buying_for_appoint[$i] = $var[$i][0];
			}


			//基本の情報が取得できた場合のみ処理を行う
			//入力された日付が正しくても1件も取得できない場合は初期表示にする	2006-09-23
			//取得された場合は0番目がnullということはないので以下の条件で分岐する
//			if($buying_for_appoint[0] != null){
			if(count($buying_for_appoint) > 0){
				//計算結果を取得する
				$result_amount = amount_function($buying_for_appoint, $base_info);
				
				for($count_update = 0; $count_update < count($buying_for_appoint); $count_update ++){
					//取得した支払予定抽出期間の最後
					//日付が登録されている
					if($buying_for_appoint[$count_update]["payment_extraction_e"] != null){
						$payment_extraction_e = $buying_for_appoint[$count_update]["payment_extraction_e"];
					}
					//nullまたは登録されていない
					else{
						$payment_extraction_e = START_DAY;
					}
				}
				
				/*******計算********/
				//仕入締日	締日が月末の場合は今日の日付ではマズいので締日関数で計算する
				//締日が29以下の場合
				if($serch_post_day < 29){
					$payment_close_day_d = $serch_post_day;
				//締め日が29以上の場合
				}
				else{
					$payment_close_day_d = date(29);
				}
				
				$payment_close_day = str_pad($post_close_day_y,4,0,STR_PAD_LEFT).'-'.str_pad($post_close_day_m,2,0,STR_PAD_LEFT).'-'.$check_day;

				for($i = 0; $i < count($buying_for_appoint); $i++){


                    if($buying_for_appoint[$i] == null){
                        continue;
                    }

					//UPDATE用のSQLを実行 前回の支払予定日を抽出する。取得できない場合はSTART_DAYを代入
					$sql = "EXECUTE get_expected_date(" . $buying_for_appoint[$i][client_id] . ", $shop_id); \n"; 
					$result = Db_Query($conn, $sql);
					$last_expected_date = @pg_fetch_all($result);
					
					//前回の支払予定日がない場合(初回で過去のデータが存在しない)
					if($last_expected_date[0][last_expected_day] == null){
						$last_expected_date = START_DAY;
					}
					//過去のデータが存在する場合
					else{
						$last_expected_date = $last_expected_date[0][last_expected_day];
					}
					
					/**********INSERT*************/
					//取引区分
					$set_trade_id                   = $buying_for_appoint[$i]["trade_id"];
					//仕入先名２
					$set_client_name2               = addslashes($buying_for_appoint[$i]["client_name2"]);
					//仕入先名(略称)
					$set_client_cname               = addslashes($buying_for_appoint[$i]["client_cname"]);
					//取引先コード
					$set_client_cd1                 = $buying_for_appoint[$i]["client_cd1"];
					//取引先コード	2006/09/12	新規追加
					$set_client_cd2                 = $buying_for_appoint[$i]["client_cd2"];
					//銀行名
					$set_bank_name                  = addslashes($buying_for_appoint[$i]["b_bank_name"]);
					//口座番号
					$set_intro_ac_num               = $buying_for_appoint[$i]["intro_ac_num"];
					//講座名義
					$set_account_name               = addslashes($buying_for_appoint[$i]["account_name"]);
					//支払日(月)
					$set_pay_m                      = $buying_for_appoint[$i]["payout_m"];
					//支払日(日)    
					$set_pay_d                      = $buying_for_appoint[$i]["payout_d"];
					//金額(丸め区分)
					$set_coax                       = $buying_for_appoint[$i]["coax"];
					//消費税(課税単位)
					$set_tax_div                    = $buying_for_appoint[$i]["tax_div"];
					//消費税(端数)
					$set_tax_franct                 = $buying_for_appoint[$i]["tax_franct"];
					//消費税(課税単位)
					$set_c_tax_div                  = $buying_for_appoint[$i]["c_tax_div"];
					//消費税率
                    #2009-12-29 aoyama-n
					#$set_tax_rate                   = $base_info["tax_rate_n"];
					$set_tax_rate                   = $base_info[$i]["tax_rate_n"];
					//ロイヤリティ
					$set_royalty_rate               = $base_info["royalty_rate"];
					//調整額
					$set_tune_amount                = $buying_for_appoint[$i]["total_adjustment_amount"];
					//今回仕入額
					$sale_amount                    = $result_amount[$i]["sale_amount"];
					//今回消費税額
					$set_tax_amount                 = (int)$result_amount[$i][tax_amount];
					//今回仕入額(税込)
					$set_account_payable            = $result_amount[$i]["account_payable"];
					//今回割賦支払額
					$set_installment_payment_this   = (int)$buying_for_appoint[$i]["total_split_pay_amount"];
					//割賦の支払金額合計
					$set_installment_balance        = $buying_for_appoint[$i]["total_split_pay_balance_amount"];
					//支払予定抽出期間(開始日)
					$set_payment_extraction_s       = $buying_for_appoint[$i]["payment_extraction_e"];
					//支払い予定額抽出期間(終了日) SQLで取得してきた終了日をそのまま入れる　→　取得してくるときに既に計算している為
					$set_payment_extraction_e       = end_close_day((string)$post_close_day_y, (string)$post_close_day_m, (string)$post_close_day_d, (string)1);
					//割賦仕入額(全額)
					$set_installment_purchase       = $result_amount[$i][installment_purchase];
					//今回支払予定額
					$set_schedule_of_payment_this   = 0;
					//今回買掛残高(税込)
					$set_ca_balance_this            = $result_amount[$i][ca_balance_this];
					//繰越額
					$set_rest_amount                = $result_amount[$i][rest_amount];
					//前回買掛残高
					$set_last_account_payable       = $buying_for_appoint[$i]["ca_balance_this"];
					//今回支払額
					$set_payment                    = $buying_for_appoint[$i]["total_pay_amount"];
					//支払条件
					$set_col_terms                  = $buying_for_appoint[$i]["col_terms"];
					//仕入先ID
					$set_client_id                  = $buying_for_appoint[$i]["client_id"];
					//仕入先名
					$set_client_name                = addslashes($buying_for_appoint[$i]["client_name"]);
					//締日
					$set_close_day                  = $buying_for_appoint[$i]["close_day"];
                    //前回支払予定額
                    $set_shedule_of_payment_last    = $buying_for_appoint[$i]["schedule_of_payment_this"];
                    //一括消費税
                    $lump_tax_amount                = (int)$result_amount[$i]["lump_tax_amount"];


					//(前回支払残と今回仕入額(税込)と今回支払額と繰越額)全てが0以外の場合に表示する
//					if(!($set_last_account_payable == 0 && $set_account_payable == 0 && $set_payment == 0 && $set_rest_amount == 0 && $set_ca_balance_this == 0)){
					if(!($set_shedule_of_payment_last == 0 && $set_account_payable == 0 && $set_payment == 0 && $set_rest_amount == 0 && $set_ca_balance_this == 0)){

						$sql  = "INSERT INTO t_schedule_payment (\n";
						$sql .= "          schedule_payment_id,\n";				//支払予定ID
						$sql .= "          payment_close_day,\n";				//仕入締日
						$sql .= "          execution_day,\n";					//仕入締処理実施日
						$sql .= "          last_update_day,\n";					//仕入締更新日
						$sql .= "          last_account_payable,\n";			//前回買掛残高
						$sql .= "          payment,\n";							//今回支払額
						$sql .= "          tune_amount,\n";						//調整額
						$sql .= "          rest_amount,\n";						//繰越額
						$sql .= "          sale_amount,\n";						//今回仕入額
						$sql .= "          tax_amount,\n";						//今回消費税額
						$sql .= "          account_payable,\n";					//今回仕入額(税込)
						$sql .= "          ca_balance_this,\n";					//今回買掛残高(税込)
						$sql .= "          schedule_of_payment_this,\n";		//今回支払予定額
						$sql .= "          installment_purchase,\n";			//割賦仕入額(全額)
						$sql .= "          payment_expected_date,\n";			//支払予定日
						$sql .= "          payment_extraction_s,\n";			//支払予定額抽出期間(開始日)
						$sql .= "          payment_extraction_e,\n";			//支払予定抽出期間(終了日)
						$sql .= "          installment_payment_this,\n";		//今回割賦支払額
						$sql .= "          installment_balance,\n";				//締時点の分割仕入の残高
						$sql .= "          client_id,\n";						//仕入先ID
						$sql .= "          client_name,\n";						//仕入先名
						$sql .= "          client_name2,\n";					//仕入先名２
						$sql .= "          client_cname,\n";					//仕入先名(略称)
						$sql .= "          client_cd1,\n";						//仕入先コード1
						$sql .= "          client_cd2,\n";						//仕入先コード2
						$sql .= "          bank_name,\n";						//銀行名
						$sql .= "          intro_ac_num,\n";					//口座番号
						$sql .= "          account_name,\n";					//口座名義
						$sql .= "          operation_staff_name,\n";			//実施者
						$sql .= "          trade_id,\n";						//取引区分ID
						$sql .= "          close_day,\n";						//締日
						$sql .= "          pay_m,\n";							//支払日(月)
						$sql .= "          pay_d,\n";							//支払日(日)
						$sql .= "          coax,\n";							//金額(丸め区分)
						$sql .= "          tax_div,\n";							//消費税(課税単位)
						$sql .= "          tax_franct,\n";						//消費税(端数)
						$sql .= "          c_tax_div,\n";						//課税区分
						$sql .= "          tax_rate,\n";						//消費税率
						$sql .= "          royalty_rate,\n";					//ロイヤリティ
						$sql .= "          shop_id,\n";							//取引先ID
						$sql .= "          col_terms\n";							//支払条件
						$sql .= ")VALUES(\n";
						
						/*******支払い予定IDの取得********/
						$sql .= "                           ( \n";
						$sql .= "                            SELECT \n";
						$sql .= "                                  COALESCE(MAX(schedule_payment_id), 0)+1 \n";
						$sql .= "                           FROM \n";
						$sql .= "                                  t_schedule_payment \n";
						$sql .= "                          ), \n";
						$sql .=   " '$payment_close_day',\n";
						$sql .=   " '$today',\n";
						$sql .=   ($last_update_day != null) ?          " '$last_update_day',\n "        : "null,\n";
						$sql .=   ($set_last_account_payable != null) ? " $set_last_account_payable,\n " : "0,\n";	//2006/09/16	nullを0に修正
						$sql .=   ($set_payment != null) ?              "$set_payment,\n"                : "0,\n";//
						$sql .=   ($set_tune_amount != null) ?          "$set_tune_amount,\n"            : "0,\n";
						$sql .=   ($set_rest_amount != null) ?          "$set_rest_amount,\n"            : "0,\n";
						$sql .=   ($sale_amount != null) ?              "$sale_amount,\n "               : "0,\n";
						$sql .=   ($set_tax_amount != null) ?           "$set_tax_amount,\n "            : "0,\n";
						$sql .=   ($set_account_payable != null) ?      " $set_account_payable,\n "      : "0,\n";
						$sql .=   ($set_ca_balance_this != null) ?      " $set_ca_balance_this,\n "      : "0,\n";
						$sql .=   ($set_schedule_of_payment_this != null) ? " $set_schedule_of_payment_this,\n " : "0,\n";
						$sql .=   ($set_installment_purchase != null) ? " $set_installment_purchase,\n " : "0,\n";
						$sql .= "                           ( \n";
						$sql .= "                             SELECT \n";
		//				$sql .= "                                  CASE WHEN payout_d >= '29' THEN \n";	2006/09/13	修正
						$sql .= "                                  CASE WHEN payout_d = '29' THEN \n";
						$sql .= "                                       SUBSTR(TO_DATE(SUBSTR('$sample_day',1,8) || 01, 'YYYY-MM-DD') + ( ".$buying_for_appoint[$i]["payout_m"]." + 1) * interval '1 month' - interval '1 day' , 1 ,10) \n";
						$sql .= "                                  ELSE \n";
						$sql .= "                                       SUBSTR(TO_DATE('$sample_day','YYYY-MM-DD') + (".$buying_for_appoint[$i]["payout_m"]." * interval '1 month'),1,8) || LPAD('".$buying_for_appoint[$i]["payout_d"]."', 2, 0) \n";
						$sql .= "                                  END \n";
						$sql .= "                              FROM \n";
						$sql .= "                                  t_client \n";
						$sql .= "                              WHERE \n";
						$sql .= "                                  t_client.client_id = ".$buying_for_appoint[$i]["client_id"]." \n";
						$sql .= " ) :: date, \n";
						$sql .=   ($set_payment_extraction_s != null) ?     " '$set_payment_extraction_s',\n " : "'".START_DAY."',\n";	//nullの場合はシステム開始年月日
						$sql .=   ($set_payment_extraction_e != null) ?     " '$set_payment_extraction_e',\n " : "null,\n";
						$sql .=   ($set_installment_payment_this != null) ? " $set_installment_payment_this,\n " : "null,\n";
						$sql .=   ($set_installment_balance != null) ?      " $set_installment_balance,\n " : "null,\n";
						$sql .=   ($set_client_id != null) ?                " $set_client_id ,\n "          : "null,\n";
						$sql .=   ($set_client_name != null) ?              " '$set_client_name' ,\n "      : "null,\n";
						$sql .=   ($set_client_name2 != null) ?             " '$set_client_name2',\n "      : "null,\n";
						$sql .=   ($set_client_cname != null) ?             " '$set_client_cname',\n "      : "null,\n";
						$sql .=   ($set_client_cd1 != null) ?               " '$set_client_cd1',\n "        : "null,\n";
						$sql .=   ($set_client_cd2 != null) ?               " '$set_client_cd2',\n "        : "null,\n";		//2006/09/12	新規追加
						$sql .=   ($set_bank_name != null) ?                " '$set_bank_name',\n "         : "null,\n";
						$sql .=   ($set_intro_ac_num != null) ?             " '$set_intro_ac_num',\n "      : "null,\n";
						$sql .=   ($set_account_name != null) ?             " '$set_account_name',\n "      : "null,\n";
						$sql .=   " '".addslashes($staff_name)."',\n";		//2006/09/22	セッションのstaff_nameでよい
						//$sql .=   ($fix_staff_name != null) ? " '$fix_staff_name',\n " : "null,\n";	2006/09/22	この項目は不要
						$sql .=   ($set_trade_id != null) ?                 " $set_trade_id,\n "            : "null,\n";
						$sql .=   ($set_close_day != null) ?                " '$set_close_day',\n "         : "null,\n";
						$sql .=   ($set_pay_m != null) ?                    " '$set_pay_m',\n "             : "null,\n";
						$sql .=   ($set_pay_d != null) ?                    " '$set_pay_d',\n "             : "null,\n";
						$sql .=   ($set_coax != null) ?                     " '$set_coax',\n "              : "null,\n";
						$sql .=   ($set_tax_div != null) ?                  " '$set_tax_div',\n "           : "null,\n";
						$sql .=   ($set_tax_franct != null) ?               " '$set_tax_franct',\n "        : "null,\n";
						$sql .=   ($set_c_tax_div != null) ?                " '$set_c_tax_div',\n "         : "null,\n";
                        #2009-12-29 aoyama-n
						#$sql .=   ($set_tax_rate != null) ?                 " '$set_tax_rate',\n "          : "null,\n";
						$sql .=   ($set_tax_rate !== null) ?                 " '$set_tax_rate',\n "          : "null,\n";
						$sql .=   ($set_royalty_rate !== null) ?            " '$set_royalty_rate',\n "      : "null,\n";
						$sql .=   " $shop_id,\n";
						$sql .=   ($set_col_terms != null) ?                " '$set_col_terms'\n "          : "null\n";
						$sql .=   ");";
						
						$result = Db_Query($conn, $sql);
						//失敗した場合はロールバック
						if($result == false){
							Db_Query($conn, "ROLLBACK;");
							exit;
						}
					
						/**********SELECT-2 今回の支払い予定額を抽出*************/
						//支払予定抽出期間(終了日)がnullの場合はSTART_DAYを代入
						if($set_payment_extraction_e != null){
							$last_payment_payment_close_day = $set_payment_extraction_e;
						}else{
							$last_payment_payment_close_day = START_DAY;
						}
						
						//EXECUTE実行
						$sql  = "EXECUTE get_schedule_payment(\n";
                        $sql .= "   ".$buying_for_appoint[$i][client_id].",\n";
                        $sql .= "   ". $shop_id.",\n";
                        $sql .= ($set_payment_extraction_s != Null) ? "'".$set_payment_extraction_s."',\n" : "'".START_DAY."',\n";
                        $sql .= "   '".$set_payment_extraction_e."'); \n";

						$result = Db_Query($conn, $sql);

						$get_payment_array = @pg_fetch_all($result);
						
						$sql  = " SELECT \n";
						$sql .= "       MAX(schedule_payment_id) \n";
						$sql .= " FROM \n";
						$sql .= "       t_schedule_payment \n";
						$sql .= " WHERE \n";
						$sql .= "       client_id = ".$close_day_client_id[$i][client_id]." \n";
						$sql .= " AND \n";
						$sql .= "       shop_id = $shop_id \n";

						$result = Db_Query($conn, $sql);
						$update_id = @pg_fetch_all($result);

						/*******割賦支払額を支払予定日の期間で再度抽出********/
						$sql  = "EXECUTE re_total_split_pay_amount(\n";
                        $sql .= "   ".$buying_for_appoint[$i][client_id].",\n";
                        $sql .= "   ".$shop_id.",\n";
                        $sql .= ($set_payment_extraction_s != Null) ? "'".$set_payment_extraction_s."',\n" : "'".START_DAY."',\n";
                        $sql .= "   '".$set_payment_extraction_e."'\n";
                        $sql .= "   ); \n";
						
						$result = Db_Query($conn, $sql);
						$resplit_pay_array = @pg_fetch_all($result);

                        //今回抽出対象となった割賦データに支払予定IDを残す                      
                        Update_Collect_Amortization ($conn, $set_payment_extraction_e, $update_id[0]["max"], $buying_for_appoint[$i]["client_id"]);
						
						/**********今回支払予定額を計算*************/
                        $update_schedule_of_payment_this = (int)bcsub(bcadd(bcadd(bcsub($get_payment_array[0][total_account_payable], $get_payment_array[0][total_installment_purchase],0),$resplit_pay_array[0][re_total_split_pay_amount],0),$set_shedule_of_payment_last,0), $set_payment,0);

						/**********UPDATE*************/
						//直前のSELECTで抽出範囲内にデータがある場合にはUPDATE、ない場合は処理をせずに終了
						//今回割賦支払額がnullの場合は0を代入する
						if($resplit_pay_array[0][re_total_split_pay_amount] == null){
							$update_pay_array = 0;
						}else{
							$update_pay_array = $resplit_pay_array[0][re_total_split_pay_amount];
						}

/*
(2007-11-16)
watanabe-k
                        //買掛残高が０で支払予定額が0の場合
//                        if($set_ca_balance_this == 0 && $update_schedule_of_payment_this == 0 && $update_pay_array == 0){							
*/
                        //各金額が０の場合に処理開始
                        //買掛残高が０
                        if($set_ca_balance_this == 0
                            &&
                        //支払予定額が０
                        $update_schedule_of_payment_this == 0
                            &&
                        //割賦支払額が０
                        $update_pay_array == 0
                            &&
                        //今回買上額(税抜)が０
                        $sale_amount == 0
                            &&
                        //今回消費税額が０
                        $set_tax_amount == 0
                            &&
                        //今回買上額（税込）が０
                        $set_account_payable == 0
                            &&
                        //今回支払額が０
                        $set_payment == 0
                            &&
                        //繰越残高が０
                        $set_rest_amount == 0
                            &&
                        //調整額が０
                        $set_tune_amount == 0
                            &&
                        //今回割賦仕入額が０
                        $set_installment_purchase == 0
                            &&
                        //前回買掛残高額が０
                        $set_last_account_payable == 0
                        ){
                            //支払予定IDを０にする。
                            Update_Payout_Schedule_Id($conn, $buying_for_appoint[$i]["client_id"], $set_payment_extraction_e, "0");

//                            $sql = "DELETE FROM t_schedule_payment WHERE schedule_payment_id = ".$update_id[0]["max"].";";
                            //削除処理を追加
                            $sql = "DELETE FROM t_schedule_payment WHERE schedule_payment_id = ".$update_id[0]["max"].";";
                            $result = Db_Query($conn, $sql);

                            if($result === false){
                                Db_Query($conn, "ROLLBACK;");
                                exit;
                            }

                            continue;
                        }else{
                            //支払予定IDを残す
                            Update_Payout_Schedule_Id($conn, $buying_for_appoint[$i]["client_id"], $set_payment_extraction_e, $update_id[0]["max"]);
                        }

						//EXECUTE実行
						$sql = "EXECUTE update_t_schedule_payment (
                                            ".$update_schedule_of_payment_this.", 
                                            ".$update_pay_array.", 
                                            ".$update_id[0]["max"].", 
                                            '$today'); \n";

						$result = Db_Query($conn, $sql);

						//失敗した場合はロールバック
						if($result == false){
							Db_Query($conn, "ROLLBACK;");
							exit;
						}

                        /*********************レンタルToレンタル************************/
                        if(is_array($ary_buy_id)){
                            foreach($ary_buy_id AS $key => $buy_id){
                                if($buying_for_appoint[$i]["head_flg"] == 't' && $buy_id != false){

                                    //UPDATE条件
                                    $where["buy_id"] = $buy_id;
                                    $where           = pg_convert($conn,'t_buy_h',$where);

                                    $buy_head["schedule_payment_id"] = $update_id[0][max];

                                    //仕入データ登録
                                    $return = Db_Update($conn, "t_buy_h", $buy_head, $where);
                                    if($return === false){
                                        Db_Query($conn, "ROLLBACK;");
                                        exit;
                                    }
                                }
                            }
                        }
						
						/**********FC機能のみ　ロイヤリティが1以上の場合のみ登録処理を行う*************/
#						if($result_amount[$i]["royalty"] >= 1){
						#0以外でロイヤリティがおきるように修正
						if($result_amount[$i]["royalty"] != 0){
							
                            //売上登録関数に渡す引数作成
                            $ary = array(
                                $sample_day,                            //請求締日
                                $buying_for_appoint[$i]["client_id"],   //得意先ID
                                $update_id[0][max],                     //支払予定ID
                                $shop_id,                               //ショップID
                            );

                            Insert_Sale_Head ($conn, $result_amount[$i][royalty], $result_amount[$i][tax_royalty], $ary, '1');
                        }

						/**********一括消費税*************/
                        if($lump_tax_amount != 0){
                            //売上登録関数に渡す引数作成
                            $ary = array(
                                $sample_day,                           //請求締日
                                $buying_for_appoint[$i]["client_id"],   //得意先ID
                                $update_id[0][max],                     //支払予定ID
                                $shop_id,                               //ショップID
                            );

                            Insert_Sale_Head ($conn, 0, $lump_tax_amount, $ary, '2');
                        }
                    }
				}
			}
		}
		Db_Query($conn, "COMMIT;");
	}
}

/****************************/
//一覧データ作成
/****************************/
//先月の1日
$now_month = date('m');
$last_date = date('Y/m/d',mktime(0,0,0,$now_month-1,01));

#2011-11-19 hashimoto-y
#$last_m = date('Y年m月', mktime(0,0,0,$now_month-1));
$last_m = date('Y年m月', mktime(0,0,0,$now_month-1,01));

//今月の1日
$now_date = date('Y/m/')."01";

$now_m = date('Y年m月', mktime(0,0,0,$now_month));

//来月の1日
$next_date = date('Y/m/d',mktime(0,0,0,$now_month+1,01));

$sql  = " SELECT \n";
$sql .= "        close_day_list.close_day, \n";
$sql .= "        payment_close_day_list1.payment_close_day, \n";
$sql .= "        payment_close_day_list2.payment_close_day \n";
$sql .= " FROM \n";
$sql .= "        ( \n";
$sql .= "           SELECT \n";
$sql .= "                DISTINCT t_client.close_day \n";
$sql .= "           FROM \n";
$sql .= "                t_client \n";
$sql .= "           WHERE \n";
//$sql .= "                t_client.shop_id = $shop_id \n";

$sql .= "  t_client.client_div = '2' AND shop_id = $shop_id\n";

$sql .= "           AND \n";
$sql .= "                t_client.close_day != '' \n";

$sql .= "        )close_day_list \n";
//前月
$sql .= "   LEFT JOIN \n";
$sql .= "        ( \n";
$sql .= "           SELECT \n";
$sql .= "                 close_day, \n";
$sql .= "                 payment_close_day \n";
$sql .= "           FROM \n";
$sql .= "                 t_payment_make_history \n";
$sql .= "           WHERE \n";
$sql .= "                 '$last_date' <= t_payment_make_history.payment_close_day \n";
$sql .= "           AND \n";
$sql .= "                 t_payment_make_history.payment_close_day < '$now_date' \n";
$sql .= "           AND \n";														//2006/09/14	追記
//$sql .= "                 t_schedule_payment.shop_id = $shop_id \n";				//2006/09/14	追記

     $sql .= "   shop_id = $shop_id";

$sql .= "        ) AS payment_close_day_list1 \n";
$sql .= "   ON close_day_list.close_day = payment_close_day_list1.close_day \n";
$sql .= "   LEFT JOIN \n";
//今月
$sql .= "        ( \n";
$sql .= "           SELECT \n";
$sql .= "                close_day, \n";
$sql .= "                payment_close_day \n";
$sql .= "           FROM \n";
$sql .= "                t_payment_make_history \n";
$sql .= "           WHERE \n";
$sql .= "                '$now_date' <= t_payment_make_history.payment_close_day \n";
$sql .= "           AND \n";
$sql .= "                t_payment_make_history.payment_close_day < '$next_date' \n";
$sql .= "           AND \n";														//2006/09/14	追記
//$sql .= "                t_schedule_payment.shop_id = $shop_id \n";					//2006/09/14	追記

     $sql .= "   shop_id = $shop_id";

$sql .= "        ) AS payment_close_day_list2 \n";
$sql .= "   ON close_day_list.close_day = payment_close_day_list2.close_day \n";
$sql .= " GROUP BY close_day_list.close_day, payment_close_day_list1.payment_close_day, payment_close_day_list2.payment_close_day \n";
$sql .= " ORDER BY close_day_list.close_day \n";

$sql .= "; \n";

$result = Db_Query($conn, $sql);
$num = pg_num_rows($result);		//	2006/09/12	修正
//$page_data = Get_Data($result);

//2006/09/12	表示用にソート処理を追加
for($i = 0; $i < $num; $i++){
    $page_data = Get_Data($result);
}

asort($page_data);
$page_data = array_values($page_data);

//締日を置換
for($i = 0; $i < count($page_data); $i++){
    if($page_data[$i][0] < 29){
        $page_data[$i][0] = $page_data[$i][0]."日";
    }else{
        $page_data[$i][0] = "月末";
    }
}

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form関連の変数をassign
$smarty->assign("form", $renderer->toArray());

// その他の変数をassign
$smarty->assign("var", array(
	'html_header'       => "$html_header",
	'page_menu'         => "$page_menu",
	'page_header'       => "$page_header",
	'html_footer'       => "$html_footer",
    'result'			=> "$res",
	'count'				=> "$countData",
	'duplicate_msg'		=> "$duplicate_msg",
	'last_date'			=> "$last_m",
    'now_date'			=> "$now_m",
    'error'				=> "$error" 
));
$smarty->assign("page_data",$page_data);
$smarty->assign("err_msg",$err_msg);
$smarty->assign("non_update_err",$non_update_err);
//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));


/**
 *
 * [金額計算] FC
 * 繰越額、今回仕入額、今回仕入額(税込)、消費税額を計算する
 * $arrayは締日により取得した情報の配列
 * $arrayの階層：2
 * 2階層目に以下の情報が存在する
 * 				int			$ca_balance_this					今回買掛残高(税込)[2-3]
 * 				int			$total_pay_amount					支払額合計[8-1]
 * 				int			$total_net_kup_amount				割賦仕入(全額)の税抜合計[5-1]
 * 				int			$total_tax_net_amount				割賦仕入(全額)の消費税額合計[5-2]
 * 				int			$total_buy_amount					課税の仕入額[4-1]
 * 				int			$total_no_tax_buy_amount			非課税の仕入額[4-2]
 * 				int			$tax_rate_n							消費税率(現在)[0-1]
 * 				string		$tax_div							消費税(課税単位)　1:締日単位 2:伝票単位
 * 				string		$c_tax_div							課税区分　1:外税 2:内税
 * 				string		$tax_franct							消費税(端数)　1:切捨 2:四捨五入 3:切上
 * 				string		$total_net_amount					仕入金額(税抜き)　※伝票単位の計算時に使用する[3-1]
 * 				string		$total_tax_amount					消費税　※伝票単位の計算時に使用する[3-2]
 * 				string		$total_no_tax_royalty_buy_amount	非課税のロイヤリティ額[4-4]
 * 				string		$total_royalty_buy_amount			課税のロイヤリティ額[4-3]
 * @param		array		$array					上記説明を参照
 * @return		bool		戻り値の説明
 * 
 * @author		yamanaka-s <yamanaka-s@bhsk.co.jp>
 * @version		1.0.2 (2006/08/24)
 *
 */

function amount_function(&$array, &$base){
	for($i=0; $i < count($array); $i++){
		//繰越額	$ca_balance_this - $total_pay_amount
		$rest_amount[$i] = bcsub($array[$i][ca_balance_this], $array[$i][total_pay_amount],0);	//前回買掛残高 - 今回支払額
		
		//client_div=2 かつ head_flg='t'のデータのみロイヤリティ計算を行う
		if($array[$i]["client_div"] == 2 && $array[$i]["head_flg"] == 't'){
			//ロイヤリティ
			//仕入先の課税区分が外税の場合
			if($array[$i][c_tax_div] == '1'){
//				($array[$i][total_royalty_buy_amount] + $array[$i][total_no_tax_royalty_buy_amount] ) * $array[$i][royalty_rate] / 100
				$royalty_amount[$i] = bcmul(bcadd($array[$i][total_royalty_buy_amount], $array[$i][total_no_tax_royalty_buy_amount],2),bcdiv($base[royalty_rate],100,2),2);
			//仕入先の課税区分が内税の場合
            #2009-12-29 aoyama-n
			#}elseif($array[$i][c_tax_div] == '2'){
//				($array[$i][total_no_tax_royalty_buy_amount] / (1 + $array[$i][tax_rate_n] / 100) + $array[$i][total_no_tax_royalty_buy_amount]) * $array[$i][royalty_rate] / 100
//				$royalty_amount = bcmul(bcadd(bcdiv($array[$i][total_no_tax_royalty_buy_amount],bcadd(1,bcdiv($base[tax_rate_n],100,2),2),2),$array[$i][total_no_tax_royalty_buy_amount],2),bcdiv($base[royalty_rate],100,2),2);
				//2006/09/13	修正
				#$royalty_amount[$i] = bcmul(bcadd(bcdiv($array[$i][total_royalty_buy_amount],bcadd(1,bcdiv($base[tax_rate_n],100,2),2),2),$array[$i][total_no_tax_royalty_buy_amount],2),bcdiv($base[royalty_rate],100,2),2);
            #2009-12-29 aoyama-n
            #非課税の場合
			}elseif($array[$i][c_tax_div] == '3'){
				$royalty_amount[$i] = bcmul(bcadd($array[$i][total_royalty_buy_amount], $array[$i][total_no_tax_royalty_buy_amount],2),bcdiv($base[royalty_rate],100,2),2);
            }

			//金額丸め
			//端数計算
			if($array[$i][tax_franct] == '1'){
				$royalty[$i] = floor($royalty_amount[$i]);
			}elseif($array[$i][tax_franct] == '2'){
				$royalty[$i] = round($royalty_amount[$i]);
			}elseif($array[$i][tax_franct] == '3'){
				$royalty[$i] = ceil($royalty_amount[$i]);
			}else{
//				print "端数単位エラー";
				$royalty[$i] = floor($royalty_amount[$i]);
			}
			//ロイヤリティ消費税
            #2009-12-29 aoyama-n
			#$tax_royalty_appoint[$i] = bcmul($royalty_amount[$i],bcdiv($base[tax_rate_n],100,2),2);
            //外税の場合
            if($array[$i][c_tax_div] == '1'){
                $tax_royalty_appoint[$i] = bcmul($royalty_amount[$i],bcdiv($base[$i][tax_rate_n],100,2),2);
            //非課税の場合
            }elseif($array[$i][c_tax_div] == '3'){
                $tax_royalty_appoint[$i] = 0;
            }

			//金額丸め
			if($array[$i][tax_franct] == '1'){
				$tax_royalty[$i] = floor($tax_royalty_appoint[$i]);
			}elseif($array[$i][tax_franct] == '2'){
				$tax_royalty[$i] = round($tax_royalty_appoint[$i]);
			}elseif($array[$i][tax_franct] == '3'){
				$tax_royalty[$i] = ceil($tax_royalty_appoint[$i]);
			}else{
				$tax_royalty[$i] = floor($tax_royalty_appoint[$i]);
			}
		}
		/***************[締日単位][外税]****************/
		//今回仕入額	$total_buy_amount + $total_no_tax_buy_amount + $total_net_kup_amount + $royalty
		$sale_amount1[$i] = bcadd(bcadd(bcadd($array[$i][total_buy_amount],$array[$i][total_no_tax_buy_amount],0),$array[$i][total_net_kup_amount],0),$royalty[$i],0);

		//消費税額		($total_buy_amount * $tax_rate_n / 100) + $total_tax_net_amount + $tax_royalty
        #2009-12-29 aoyama-n
		#$before_tax_amount1[$i] = bcadd(bcadd(bcmul($array[$i][total_buy_amount],bcdiv($base[tax_rate_n],100,2),2),$array[$i][total_tax_net_amount],2),$tax_royalty[$i],2);
		$before_tax_amount1[$i] = bcadd(bcadd(bcmul($array[$i][total_buy_amount],bcdiv($base[$i][tax_rate_n],100,2),2),$array[$i][total_tax_net_amount],2),$tax_royalty[$i],2);
		
		//端数計算
		if($array[$i][tax_franct] == '1'){
			$tax_amount1[$i] = floor($before_tax_amount1[$i]);
		}elseif($array[$i][tax_franct] == '2'){
			$tax_amount1[$i] = round($before_tax_amount1[$i]);
		}elseif($array[$i][tax_franct] == '3'){
			$tax_amount1[$i] = ceil($before_tax_amount1[$i]);
		//デフォルト値
		}else{
			$tax_amount1[$i] = floor($before_tax_amount1[$i]);
		}
			
		//今回仕入額(税込)	$sale_amount + $tax_amount
		$account_payable1[$i] = bcadd($sale_amount1[$i],$tax_amount1[$i],0);
		
		/****************[締日単位][内税]****************/
		//今回仕入額(税込)	$total_buy_amount + $total_no_tax_buy_amount + $total_net_kup_amount + $total_tax_net_amount + $royalty + $tax_royalty
        /**** 2009-12-29 aoyama-n ****
		$account_payable2[$i] = bcadd(bcadd(bcadd(bcadd(bcadd($array[$i][total_buy_amount],$array[$i][total_no_tax_buy_amount],0),$array[$i][total_net_kup_amount],0),$array[$i][total_tax_net_amount],0),$royalty[$i],2),$tax_royalty[$i],2);

		//消費税額			$total_buy_amount - ($total_buy_amount / (1 + $tax_rate_n)) + $total_tax_net_amount + $tax_royalty
		//$before_tax_amount2 = bcadd(bcadd(bcsub($array[$i][total_buy_amount],bcdiv($array[$i][total_buy_amount],bcadd(1,bcdiv($array[$i][tax_rate_n],100,2),2),2),2),$array[$i][total_tax_net_amount],2),$tax_royalty,2);
		$before_tax_amount2[$i] = bcadd(bcadd(bcsub($array[$i][total_buy_amount],bcdiv($array[$i][total_buy_amount],bcadd(1,bcdiv($base[tax_rate_n],100,2),2),2),2),$array[$i][total_tax_net_amount],2),$tax_royalty[$i],2);
		//端数計算
		if($array[$i][tax_franct] == '1'){
			$tax_amount2[$i] = floor($before_tax_amount2[$i]);
		}elseif($array[$i][tax_franct] == '2'){
			$tax_amount2[$i] = round($before_tax_amount2[$i]);
		}elseif($array[$i][tax_franct] == '3'){
			$tax_amount2[$i] = ceil($before_tax_amount2[$i]);
		//デフォルト値
		}else{
			$tax_amount2[$i] = floor($before_tax_amount2[$i]);
		}
		
		//今回仕入額		    $account_payable2 - $tax_amount
		$sale_amount2[$i]       = bcsub($account_payable2[$i],$tax_amount2[$i],0);
        **** 2009-12-29 aoyama-n ****/ 

        #2009-12-29 aoyama-n
        /***************[締日単位][非課税]****************/
        //今回仕入額    $total_buy_amount + $total_no_tax_buy_amount + $total_net_kup_amount + $royalty
        $sale_amount4[$i] = bcadd(bcadd(bcadd($array[$i][total_buy_amount],$array[$i][total_no_tax_buy_amount],0),$array[$i][total_net_kup_amount],0),$royalty[$i],0);

        //消費税額
        $tax_amount4[$i] = 0;

        //今回仕入額(税込)  $sale_amount + $tax_amount
        $account_payable4[$i] = bcadd($sale_amount4[$i],$tax_amount4[$i],0);

		/****************[伝票単位]*****************/
		//今回仕入額	        $total_net_amount + $total_net_kup_amount + $royalty
		$sale_amount3[$i]       = bcadd(bcadd($array[$i][total_net_amount],$array[$i][total_net_kup_amount],0),$royalty[$i],0);
		
		//消費税額		        $total_tax_amount + $total_tax_net_amount + $tax_royalty
		$tax_amount3[$i]        = bcadd(bcadd($array[$i][total_tax_amount],$array[$i][total_tax_net_amount],0),$tax_royalty[$i],0);
		
		//今回仕入額(税込)	    $sale_amount + $tax_amount
		$account_payable3[$i]   = bcadd($sale_amount3[$i],$tax_amount3[$i],0);
		
		//繰越額をreturn用配列に格納
		$result[$i][rest_amount] = $rest_amount[$i];
		
		/****************上記3つの計算のどれを取得するか判別****************/
		//締日単位
		if($array[$i][tax_div] == '1'){
			//外税
			if($array[$i][c_tax_div] == '1'){
				//締日単位の外税
//				$result[$i][rest_amount] = $rest_amount;
				$result[$i][sale_amount]     = $sale_amount1[$i];
				$result[$i][tax_amount]      = $tax_amount1[$i];
				$result[$i][account_payable] = $account_payable1[$i];
			//内税
            #2009-12-29 aoyama-n
			#}elseif($array[$i][c_tax_div] == '2'){
				//締日単位の内税
			#	$result[$i][sale_amount]     = $sale_amount2[$i];
			#	$result[$i][tax_amount]      = $tax_amount2[$i];
			#	$result[$i][account_payable] = $account_payable2[$i];
            #2009-12-29 aoyama-n
			//課税区分がない場合は外税で計算する
			#}else{
			#	$result[$i][sale_amount]     = $sale_amount1[$i];
			#	$result[$i][tax_amount]      = $tax_amount1[$i];
			#	$result[$i][account_payable] = $account_payable1[$i];
            //非課税
            }elseif($array[$i][c_tax_div] == '3'){
                //締日単位の非課税
                $result[$i][sale_amount] = $sale_amount4[$i];
                $result[$i][tax_amount] = $tax_amount4[$i];
                $result[$i][account_payable] = $account_payable4[$i];
            }

            //一括消費税
            $result[$i][lump_tax_amount]     = ($tax_amount3[$i] + $tax_royalty[$i] - $result[$i][tax_amount]) * -1;
 
		//伝票単位
		}elseif($array[$i][tax_div] == '2'){
			//伝票単位
			$result[$i][sale_amount]         = $sale_amount3[$i];
			$result[$i][tax_amount]          = $tax_amount3[$i];
			$result[$i][account_payable]     = $account_payable3[$i];
		//課税単位がない場合は伝票単位で計算する
		}else{
			$result[$i][sale_amount]         = $sale_amount3[$i];
			$result[$i][tax_amount]          = $tax_amount3[$i];
			$result[$i][account_payable]     = $account_payable3[$i];
		}
		
		//今回買掛残高
		$result[$i][ca_balance_this] = bcadd($rest_amount[$i],$result[$i][account_payable],0);
		
		//割賦仕入額(全額)
		$result[$i][installment_purchase] = bcadd($array[$i][total_net_kup_amount],$array[$i][total_tax_net_amount],0);

		if($array[$i]["client_div"] == 2 && $array[$i]["head_flg"] == 't'){
			//ロイヤリティ
			$result[$i][royalty] = $royalty[$i];
			//ロイヤリティ消費税
			$result[$i][tax_royalty] = $tax_royalty[$i];
		}else{
			$result[$i][royalty] = 0;
			$result[$i][tax_royalty] = 0;
		}

	}
	return $result;
}


/**
 *
 * 締日計算
 * 1〜28日を選択した場合はそのままセット
 * 月末を選択した場合は計算して対応した年月の月末をセット
 *
 * @param		string		$year		年(入力項目) 
 * @param		int			$month		月(入力項目)
 * @param		int			$day		日(入力項目)
 *
 *
 * @return		string		$post_close_day		締日
 *
 * @author		yamanaka-s <yamanaka-s@bhsk.co.jp>
 * @version		1.0.0 (2006/08/25)
 *
 */
function set_close_day(&$year, &$month, $day){

	//日付を整形
	$befor_day = $year.'-'.$month.'-'.$day;

	//日の判定
	//1〜28日指定
	if($day > '0' && $day < '29'){
		$day = $day;
	}
	//月末指定
    elseif($day >= '29'){
//		$day = date("t",strtotime($befor_day));
        $day = date('t',mktime(0,0,0,$month, 1, $year));
	}
	//表示日数以外を指定
	else{
		$day = null;
	}
	
	////日付を整形
	$post_close_day = $year.'-'.$month.'-'.$day;

	return $post_close_day;
	
}


/**
 *
 * 日付計算
 * 以下のデータを受け取り、日付計算を行う
 * stringのみ動作確認済
 * int型、date型は計算結果が異なることに注意
 * 入力された年、月、日、何ヶ月後
 * 
 *
 * @param		string		$year			対象となる年
 * @param		string		$month			対象となる月
 * @param		string		$day			対象となる日
 * $param		string		$n				nヶ月後
 *
 *
 * @return		date		 $return_comp	nヶ月後の年月日
 *
 * @author		nishibayashi-t <nishibayashi-t@bhsk.co.jp>
 * @version		1.0.0 (2006/09/02)
 *
 */
function end_close_day($year, $month, $day, $n){

    //締日が月末の場合
    if($day == '29'){
        $return_comp = date("Ymt", mktime(0,0,0,$month + 1, 1, $year));
    //月末以外の場合
    }else{
        $return_comp = date('Ymd', mktime(0,0,0,$month + 1, $day, $year));
    }

/*
	$comp1=	mktime(0, 0, 0, $month, $day, $year);//当月
	$comp2=	mktime(0, 0, 0, $month+$n, $day, $year);//当月のnヶ月後
	
	//�〇慊蠧�にnヵ月後を計算　月
	$tmp1=date("m",$comp1)+$n;
	$tmp2=round($tmp1/12);
	$tmp1=$tmp1-($tmp2*12);
	
	//�∋慊蠧�のnヵ月後を計算(mktimeを使用)　月
	$tmp3=date("m",$comp2);
	$tmp5=round($tmp3/12);
	$tmp3=$tmp3-($tmp5*12);
	
	//指定日のnヵ月後を計算(mktimeを使用)　年
	$tmp4=date("Y",$comp2);
	
	//指定日が月末
	if(date("d",$comp1)==date("t",$comp1)){
		$ytmp=$tmp4;
		if($tmp1!=$tmp3){
			$mtmp=date("m",$comp2)-1;
		}else{
			$mtmp=date("m",$comp2);
		}
		$comp3=	mktime(0, 0, 0, $mtmp, 01, $ytmp);
		$comp4=	mktime(0, 0, 0, $mtmp, date("t",$comp3), date("Y",$comp3));
		
		$return_comp = date("Y/m/d",$comp4);
		
	}
	//�，鉢△侶覯未�同じ場合
	elseif($tmp1==$tmp3){
		$return_comp = date("Y/m/d",$comp2);
		
	}
	//そのた
	else{
		$ytmp=$tmp4;
		$mtmp=date("m",$comp2)-1;
		$comp3=	mktime(0, 0, 0, $mtmp, 01, $ytmp);
		$comp4=	mktime(0, 0, 0, $mtmp, date("t",$comp3), date("Y",$comp3));
		
		$return_comp = date("Y/m/d",$comp4);
	}
*/
	return $return_comp;
}

?>
