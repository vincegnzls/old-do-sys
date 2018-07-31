<?php
/*
 * 変更履歴
 *   (2006/07/31)消費税計算処理の変更（watanabe-k）
 *   (2006/12/13)受注担当者表示データ変更（suzuki）
*/
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/11/01　08-040　　　　watanabe-k　オンライン受注変更完了時、商品コードが表示されていなバグの修正
 * 　2006/11/01　08-041　　　　watanabe-k＿確認画面が表示されずに完了画面に遷移するバグの修正
 * 　2006/11/01　08-042　　　　watanabe-k　通信欄の改行が＜br/＞になっているバグの修正
 * 　2006/11/03　      　　　　watanabe-k　発注番号が表示されていないバグの修正
 * 　2006/11/03　      　　　　watanabe-k　グリーン指定業者が選択されていないのにチェックされているバグの修正
 * 　2006/11/03　08-081　　　　watanabe-k　Getチェック追加
 * 　2006/11/03　08-082　　　　watanabe-k　Getチェック追加
 * 　2006/11/09　08-145　　　　suzuki　    受注伝票から得意先情報取得するように修正
 * 　2006/11/09　08-125　　　　watanabe-k　通信欄（得意先宛）の改行が<br />で表示されている
 * 　2006/11/09　08-126　　　　watanabe-k　出荷予定日をNULLにして登録が可能なバグの修正
 * 　2006/11/09　08-150　　　　watanabe-k　通信欄（得意先宛）のチェック追加
 * 　2007/01/25　      　　　　watanabe-k　ボタンの色変更
 *   2007/03/01                  morita-d  商品名は正式名称を表示するように変更 
 *   2007/03/08                 fukuda-s     実棚数ダイアログが出ない不具合修正
 *   2009/10/12                hashimoto-y 値引き商品を赤字表示に変更
 *   2009/10/13                hashimoto-y 在庫管理フラグをショップ別商品情報テーブルに変更
 *   2009/12/21                aoyama-n    税率をTaxRateクラスから取得
 *   2016/01/20                amano  Button_Submit 関数でボタン名が送られない IE11 バグ対応
 */

$page_title = "受注入力";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
//$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");
$form =& new HTML_QuickForm("dateForm", "POST", "#");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//外部変数取得
/****************************/
$shop_id     = $_SESSION["client_id"];
$shop_gid    = $_SESSION["shop_gid"];
$rank_cd     = $_SESSION["rank_cd"];
$o_staff_id  = $_SESSION["staff_id"];
$aord_id     = $_GET["aord_id"];
Get_Id_Check3($_GET["aord_id"]);
Get_Id_Check2($_GET["aord_id"]);

//得意先が指定されているか
if($_POST["hdn_client_id"] == null){
    $warning = "得意先を選択して下さい。";
}else{
    $warning = null;
    $client_id   = $_POST["hdn_client_id"];
    $coax        = $_POST["hdn_coax"];
    $tax_franct  = $_POST["hdn_tax_franct"];
    $attach_gid  = $_POST["attach_gid"];
    $client_name = $_POST["form_client"]["name"];
}

//変更前に対象データが削除されていないかチェック    
$sql  = "SELECT";
$sql .= "   COUNT(*) ";
$sql .= "FROM";
$sql .= "   t_aorder_h ";
$sql .= "WHERE";
$sql .= "   aord_id = $aord_id"; 
$sql .= ";";

$result = Db_Query($db_con, $sql);
if(pg_fetch_result($result,0,0) == 0){
    header("Location: ./1-2-108.php?add_del_flg='t'");
    exit;
}

/****************************/
//初期設定
/****************************/

#2009-12-21 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);

//変更処理判定
if($aord_id != NULL){

    //受注ヘッダ取得SQL
    $sql  = "SELECT ";
    $sql .= "    t_aorder_h.ord_no,";
    $sql .= "    t_aorder_h.ord_time,";
    $sql .= "    t_aorder_h.hope_day,";
    $sql .= "    t_aorder_h.arrival_day,";
    $sql .= "    t_aorder_h.green_flg,";
    $sql .= "    t_aorder_h.trans_id,";
    $sql .= "    t_aorder_h.client_id,";
/*
    $sql .= "    t_client.client_cd1,";
    $sql .= "    t_client.client_cd2,";
    $sql .= "    t_client.client_cname,";
*/
	$sql .= "    t_aorder_h.client_cd1,";
    $sql .= "    t_aorder_h.client_cd2,";
    $sql .= "    t_aorder_h.client_cname,";
    $sql .= "    t_aorder_h.direct_id,";
    $sql .= "    t_aorder_h.ware_id,";
    $sql .= "    t_aorder_h.trade_id,";
    $sql .= "    t_aorder_h.c_staff_id,";
    $sql .= "    t_aorder_h.note_your,";
    $sql .= "    t_aorder_h.note_my,";
    $sql .= "    t_direct.direct_cname, ";
    $sql .= "    t_order_h.ord_no ";
    $sql .= "FROM ";
    $sql .= "    t_aorder_h ";
    $sql .= "       INNER JOIN";
    $sql .= "    t_client";
    $sql .= "    ON t_client.client_id = t_aorder_h.client_id ";
    $sql .= "       INNER JOIN";
    $sql .= "    t_order_h";
    $sql .= "    ON t_aorder_h.fc_ord_id = t_order_h.ord_id";
    $sql .= "       LEFT JOIN";
    $sql .= "    t_direct";
    $sql .= "    ON t_direct.direct_id = t_aorder_h.direct_id ";
    $sql .= "WHERE ";
    $sql .= "    t_aorder_h.aord_id = $aord_id ";
    $sql .= "    AND ";
    $sql .= "    t_aorder_h.ps_stat = '1';";

    $result = Db_Query($db_con,$sql);

    //GETデータ判定
    Get_Id_Check($result);
    $h_data_list = Get_Data($result,2);

    //受注データ取得SQL
    $sql  = "SELECT\n";
    $sql .= "   t_goods.goods_id,\n";
    $sql .= "   t_goods.name_change,\n";
    #2009-10-13_1 hashimoto-y
    #$sql .= "   t_goods.stock_manage,\n";
    $sql .= "   t_goods_info.stock_manage,\n";

//    $sql .= "   t_goods.goods_cd,";
    $sql .= "   t_aorder_d.goods_cd,\n";
    //$sql .= "   t_aorder_d.goods_name,\n";
    $sql .= "   t_aorder_d.official_goods_name,\n";
    #2009-10-13_1 hashimoto-y
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,\n";
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,\n";
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,\n";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,\n";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";

//    $sql .= "    - COALESCE(t_allowance_io.allowance_io_num,0) \n";
    $sql .= " END AS allowance_total,\n";
    $sql .= "   COALESCE(t_stock.stock_num,0)\n"; 
    $sql .= "   + COALESCE(t_stock_io.order_num,0)\n";
//    $sql .= "   - (COALESCE(t_stock.rstock_num,0)\n";
    $sql .= "   - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,\n";
    $sql .= "   t_aorder_d.num,\n";
    $sql .= "   t_aorder_d.tax_div,\n";
    $sql .= "   t_aorder_d.cost_price,\n";
    $sql .= "   t_aorder_d.sale_price,\n";
    $sql .= "   t_aorder_d.cost_amount,\n";
    #2009-10-13 hashimoto-y
    #$sql .= "   t_aorder_d.sale_amount \n";
    $sql .= "   t_aorder_d.sale_amount, \n";
    $sql .= "   t_goods.discount_flg \n";

    $sql .= " FROM\n";
    $sql .= "   t_aorder_d \n";

    $sql .= "   INNER JOIN  t_aorder_h ON t_aorder_d.aord_id = t_aorder_h.aord_id\n";
    $sql .= "   INNER JOIN  t_goods ON t_aorder_d.goods_id = t_goods.goods_id\n";

    $sql .= "   LEFT JOIN\n";

    //在庫数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock.goods_id,\n";
    $sql .= "       SUM(t_stock.stock_num)AS stock_num,\n";
    $sql .= "       SUM(t_stock.rstock_num)AS rstock_num\n";
    $sql .= "       FROM\n";
    $sql .= "            t_stock INNER JOIN t_ware ON t_stock.ware_id = t_ware.ware_id\n";
    $sql .= "       WHERE\n";
//    $sql .= "            t_stock.shop_id =  ".$h_data_list[0][6]."\n";
    $sql .= "            t_stock.shop_id =  1\n";
    $sql .= "       AND\n";
    $sql .= "            t_ware.count_flg = 't'\n";
    $sql .= "       GROUP BY t_stock.goods_id\n";
    $sql .= "   )AS t_stock ON t_aorder_d.goods_id = t_stock.goods_id\n";

    $sql .= "   LEFT JOIN\n";

    //発注残数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS order_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = 3\n";
    $sql .= "   AND\n";
//    $sql .= "       t_stock_hand.client_id = ".$h_data_list[0][6]."\n";
    $sql .= "       t_stock_hand.shop_id = ".$shop_id."\n";
    $sql .= "   AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
//    $sql .= "   AND\n";
//    $sql .= "       CURRENT_DATE <= t_stock_hand.work_day\n";
    $sql .= "   AND\n";
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + 7)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_stock_io ON t_aorder_d.goods_id=t_stock_io.goods_id\n";

    $sql .= "   LEFT JOIN\n";

    //引当数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS allowance_io_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = 1\n";
    $sql .= "   AND\n";
//    $sql .= "       t_stock_hand.client_id = ".$h_data_list[0][6]."\n";
    $sql .= "       t_stock_hand.shop_id = ".$shop_id."\n";
    $sql .= "   AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "   AND\n";
//    $sql .= "       t_stock_hand.work_day >= (CURRENT_DATE + 7)\n";
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + 7)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_allowance_io ON t_aorder_d.goods_id = t_allowance_io.goods_id\n";

    #2009-10-13_1 hashimoto-y
    $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id\n";

    $sql .= " WHERE \n";
    $sql .= "       t_aorder_d.aord_id = $aord_id \n";
    $sql .= " AND \n";
    $sql .= "       t_aorder_h.shop_id = $shop_id \n";
    #2009-10-13_1 hashimoto-y
    $sql .= " AND\n";
    $sql .= "       t_goods_info.shop_id = $shop_id \n";

    $sql .= " ORDER BY t_aorder_d.line;\n";
    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    $data_list = Get_Data($result,2);

    //得意先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id,";
    $sql .= "   coax,";
    $sql .= "   tax_franct,";
//    $sql .= "   attach_gid ";
    $sql .= "   shop_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_id = ".$h_data_list[0][6];
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $client_list = Get_Data($result);

    /****************************/
    //フォームに値を復元
    /****************************/
    $sale_money = NULL;                        //商品の売上金額
    $tax_div    = NULL;                        //課税区分

    //ヘッダー復元
    $update_goods_data["form_order_no"]                = $h_data_list[0][0];  //受注番号

    //受注日を年月日に分ける
    $ex_ord_day = explode('-',$h_data_list[0][1]);
    $update_goods_data["form_ord_day"]["y"]            = $ex_ord_day[0];   //受注日
    $update_goods_data["form_ord_day"]["m"]            = $ex_ord_day[1];   
    $update_goods_data["form_ord_day"]["d"]            = $ex_ord_day[2];   

    //希望納期を年月日に分ける
    $ex_hope_day = explode('-',$h_data_list[0][2]);
    $update_goods_data["form_hope_day"]["y"]           = $ex_hope_day[0];  //希望納期
    $update_goods_data["form_hope_day"]["m"]           = $ex_hope_day[1];   
    $update_goods_data["form_hope_day"]["d"]           = $ex_hope_day[2];   

    //入荷予定日を年月日に分ける
    $ex_arr_day = explode('-',$h_data_list[0][3]);
    $update_goods_data["form_arr_day"]["y"]            = $ex_arr_day[0];   //入荷予定日
    $update_goods_data["form_arr_day"]["m"]            = $ex_arr_day[1];   
    $update_goods_data["form_arr_day"]["d"]            = $ex_arr_day[2];   

    $update_goods_data["form_trans_check"]             = ($h_data_list[0][4] == 't')? 1:null;  //グリーン指定
    $update_goods_data["form_trans_select"]            = $h_data_list[0][5];  //運送業者
    $update_goods_data["form_client"]["cd1"]           = $h_data_list[0][7];  //得意先コード１
    $update_goods_data["form_client"]["cd2"]           = $h_data_list[0][8];  //得意先コード２
    $update_goods_data["form_client"]["name"]          = htmlspecialchars($h_data_list[0][9]);  //得意先名
    $update_goods_data["form_direct_select"]           = $h_data_list[0][10]; //直送先
    $update_goods_data["form_ware_select"]             = $h_data_list[0][11]; //倉庫
    $update_goods_data["trade_aord_select"]            = $h_data_list[0][12]; //取引区分
    $update_goods_data["form_staff_select"]            = $h_data_list[0][13]; //担当者
    $update_goods_data["form_note_client"]             = $h_data_list[0][14]; //通信欄（得意先）
    $update_goods_data["form_note_head"]               = nl2br(htmlspecialchars($h_data_list[0][15])); //通信欄（本部）
    $update_goods_data["form_direct_name"]             = htmlspecialchars($h_data_list[0][16]); //直送先名
    $update_goods_data["form_fc_order_no"]             = $h_data_list[0][17]; //発注番号

    //データ復元
    for($i=0;$i<count($data_list);$i++){
        $update_goods_data["hdn_goods_id"][$i]         = $data_list[$i][0];   //商品ID
        $hdn_goods_id[$i]                              = $data_list[$i][0];   //POSTする前に商品IDを総在庫数で使用する為

        $update_goods_data["hdn_name_change"][$i]      = $data_list[$i][1];   //品名変更フラグ
        $hdn_name_change[$i]                           = $data_list[$i][1];   //POSTする前に商品名の変更不可判定を行なう為

        $update_goods_data["hdn_stock_manage"][$i]     = $data_list[$i][2];   //在庫管理
        $hdn_stock_manage[$i]                          = $data_list[$i][2];   //POSTする前に実棚数の在庫管理判定を行なう為

        $update_goods_data["form_goods_cd"][$i]        = $data_list[$i][3];   //商品CD
        $update_goods_data["form_goods_name"][$i]      = $data_list[$i][4];   //商品名

        $update_goods_data["form_stock_num"][$i]       = number_format($data_list[$i][5]);   //実棚数
        $update_goods_data["hdn_stock_num"][$i]        = number_format($data_list[$i][5]);   //実棚数（hidden）
        $stock_num[$i]                                 = number_format($data_list[$i][5]);   //実棚数(リンクの値)

        $update_goods_data["form_rorder_num"][$i]      = $data_list[$i][6];   //発注済数
        $update_goods_data["form_rstock_num"][$i]      = $data_list[$i][7];   //引当数
        $update_goods_data["form_designated_num"][$i]  = $data_list[$i][8];   //出荷可能数
        $update_goods_data["form_sale_num"][$i]        = $data_list[$i][9];   //受注数
        $update_goods_data["hdn_tax_div"][$i]          = $data_list[$i][10];  //課税区分

        //原価単価を整数部と少数部に分ける
        $cost_price = explode('.', $data_list[$i][11]);
        $update_goods_data["form_cost_price"][$i]["i"] = $cost_price[0];  //原価単価
        $update_goods_data["form_cost_price"][$i]["d"] = ($cost_price[1] != null)? $cost_price[1] : '00';     
        $update_goods_data["form_cost_amount"][$i]     = number_format($data_list[$i][13]);  //原価金額

        //売上単価を整数部と少数部に分ける
        $sale_price = explode('.', $data_list[$i][12]);
        $update_goods_data["form_sale_price"][$i]["i"] = $sale_price[0];  //売上単価
        $update_goods_data["form_sale_price"][$i]["d"] = ($sale_price[1] != null)? $sale_price[1] : '00';
        $update_goods_data["form_sale_amount"][$i]     = number_format($data_list[$i][14]);  //売上金額

        $sale_money[]                                  = $data_list[$i][14];  //売上金額合計
        $tax_div[]                                     = $data_list[$i][10];  //消費税合計

        #2009-10-13 hashimoto-y
        $update_goods_data["hdn_discount_flg"][$i]     = $data_list[$i][15]; //値引フラグ
    }

    //得意先情報復元
    $client_id      = $client_list[0][0];        //得意先ID
    $coax           = $client_list[0][1];        //丸め区分（金額）
    $tax_franct     = $client_list[0][2];        //端数区分（消費税）
    $attach_gid     = $client_list[0][3];        //所属グループ
    $warning = null;
    $update_goods_data["hdn_client_id"]       = $client_id;
    $update_goods_data["hdn_coax"]            = $coax;
    $update_goods_data["hdn_tax_franct"]      = $tax_franct;
    $update_goods_data["attach_gid"]          = $attach_gid;

    //現在の消費税率
    #2009-12-21 aoyama-n
    #$sql  = "SELECT ";
    #$sql .= "    tax_rate_n ";
    #$sql .= "FROM ";
    #$sql .= "    t_client ";
    #$sql .= "WHERE ";
    #$sql .= "    client_id = ".$h_data_list[0][6].";";
    #$result = Db_Query($db_con, $sql); 
    #$tax_num = pg_fetch_result($result, 0,0);

    #2009-12-21 aoyama-n
    $tax_rate_obj->setTaxRateDay($h_data_list[0][1]);
    $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

    $total_money = Total_Amount($sale_money, $tax_div,$coax,$tax_franct,$tax_num, $client_id, $db_con);

    $sale_money = number_format($total_money[0]);
    $tax_money  = number_format($total_money[1]);
    $st_money   = number_format($total_money[2]);

    //フォームに値セット
    $update_goods_data["form_sale_total"]      = $sale_money;
    $update_goods_data["form_sale_tax"]        = $tax_money;
    $update_goods_data["form_sale_money"]      = $st_money;
//    $update_goods_data["sum_button_flg"]       = "";
    $update_goods_data["form_designated_date"] = 7; //出荷可能数

//    $form->setConstants($update_goods_data);
    $form->setDefaults($update_goods_data);

    //表示行数
    if($_POST["max_row"] != NULL){
        $max_row = $_POST["max_row"];
    }else{
        //受注データの数
        $max_row = count($data_list);
    }

//受注IDが無い場合
}else{

}

//初期表示位置変更
$form_potision = "<body bgcolor=\"#D8D0C8\">";

//削除行数
$del_history[] = NULL; 

/****************************/
//行数追加処理
/****************************/
if($_POST["add_row_flg"]==true){
    //最大行に、＋１する
    $max_row = $_POST["max_row"]+1;
    //行数追加フラグをクリア
    $add_row_data["add_row_flg"] = "";
    $form->setConstants($add_row_data);
}

/****************************/
//行削除処理
/****************************/
if($_POST["del_row"] != ""){

    //削除リストを取得
    $del_row = $_POST["del_row"];

    //削除履歴を配列にする。
    $del_history = explode(",", $del_row);
}

//***************************/
//最大行数をhiddenにセット
/****************************/
$max_row_data["max_row"] = $max_row;

$form->setConstants($max_row_data);

//***************************/
//グリーン指定チェック処理
/****************************/
//チェックの場合は、運送業者のプルダウンの値を変更する
if($_POST["trans_check_flg"] == true){
    $where  = " WHERE ";
    $where .= "    shop_id = $shop_id";
//  $where .= "    shop_gid = $shop_gid";
    $where .= " AND";
    $where .= "    green_trans = 't'";

    //初期化
    $trans_data["trans_check_flg"]   = "";
    $form->setConstants($trans_data);
}else{
    $where = "";
}

/****************************/
//部品作成
/****************************/
//発注番号
$form->addElement("static","form_fc_order_no","");

//受注番号
$form->addElement("static","form_order_no","");

//得意先
$form_client[] =& $form->createElement("static","cd1","","");
$form_client[] =& $form->createElement("static","","","-");
$form_client[] =& $form->createElement("static","cd2","","");
$form_client[] =& $form->createElement("static","name","","");
$form->addGroup( $form_client, "form_client", "");

//出荷可能数
$form->addElement(
    "text","form_designated_date","",
    "size=\"4\" maxLength=\"4\" 
    $g_form_option 
     style=\"$g_form_style;text-align: right\"
    onChange=\"javascript:Button_Submit('recomp_flg','#','true', this)\"
    "
);

//受注日
$form_ord_day[] =& $form->createElement("text","y","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_ord_day[y]','form_ord_day[m]',4)\" onFocus=\"onForm_today(this,this.form,'form_ord_day[y]','form_ord_day[m]','form_ord_day[d]')\" onBlur=\"blurForm(this)\"");
$form_ord_day[] =& $form->createElement("static","","","-");
$form_ord_day[] =& $form->createElement("text","m","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_ord_day[m]','form_ord_day[d]',2)\" onFocus=\"onForm_today(this,this.form,'form_ord_day[y]','form_ord_day[m]','form_ord_day[d]')\" onBlur=\"blurForm(this)\"");
$form_ord_day[] =& $form->createElement("static","","","-");
$form_ord_day[] =& $form->createElement("text","d","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" onFocus=\"onForm_today(this,this.form,'form_ord_day[y]','form_ord_day[m]','form_ord_day[d]')\" onBlur=\"blurForm(this)\"");
$form->addGroup( $form_ord_day,"form_ord_day","form_ord_day");

//希望納期
$form_hope_day[] =& $form->createElement("static","y","テキストフォーム","");
$form_hope_day[] =& $form->createElement("static","m","テキストフォーム","");
$form_hope_day[] =& $form->createElement("static","d","テキストフォーム","");
$form->addGroup( $form_hope_day,"form_hope_day","form_hope_day","-");

//入荷予定日
$form_arr_day[] =& $form->createElement("text","y","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_arr_day[y]','form_arr_day[m]',4)\" onFocus=\"onForm_today(this,this.form,'form_arr_day[y]','form_arr_day[m]','form_arr_day[d]')\" onBlur=\"blurForm(this)\"");
$form_arr_day[] =& $form->createElement("static","","","-");
$form_arr_day[] =& $form->createElement("text","m","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_arr_day[m]','form_arr_day[d]',2)\" onFocus=\"onForm_today(this,this.form,'form_arr_day[y]','form_arr_day[m]','form_arr_day[d]')\" onBlur=\"blurForm(this)\"");
$form_arr_day[] =& $form->createElement("static","","","-");
$form_arr_day[] =& $form->createElement("text","d","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" onFocus=\"onForm_today(this,this.form,'form_arr_day[y]','form_arr_day[m]','form_arr_day[d]')\" onBlur=\"blurForm(this)\"");
$form->addGroup( $form_arr_day,"form_arr_day","form_arr_day");


//売上金額合計
$form->addElement(
    "text","form_sale_total","",
    "size=\"25\" maxLength=\"18\" 
    style=\"$g_form_style;color : #000000; 
    border : #FFFFFF 1px solid; 
    background-color: #FFFFFF; 
    text-align: right\" readonly'"
);

//消費税額(合計)
$form->addElement(
        "text","form_sale_tax","",
        "size=\"25\" maxLength=\"18\" 
        style=\"$g_form_style;color : #000000; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//売上金額（税込合計)
$form->addElement(
        "text","form_sale_money","",
        "size=\"25\" maxLength=\"18\" 
        style=\"$g_form_style;color : #000000; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//通信欄（得意先宛）
$form->addElement("textarea","form_note_client","テキストフォーム",' rows="2" cols="75" onFocus="onForm(this)" onBlur="blurForm(this)"');
//通信欄（本部宛）
$form->addElement("static","form_note_head","");

//グリーン指定
$form->addElement('checkbox', 'form_trans_check', 'グリーン指定', '<b>グリーン指定</b>　',"onClick=\"javascript:Link_Submit('form_trans_check','trans_check_flg','#','true')\"");
//運送業者
$select_value = Select_Get($db_con,'trans',$where);
$form->addElement('select', 'form_trans_select', 'セレクトボックス', $select_value,$g_form_option_select);

//直送先
//$select_value = Select_Get($db_con,'direct');
//$form->addElement('select', 'form_direct_select', 'セレクトボックス', $select_value,$g_form_option_select);
$form->addElement('static', 'form_direct_name', 'セレクトボックス' );

//倉庫
$select_value = Select_Get($db_con,'ware');
$form->addElement('select', 'form_ware_select', 'セレクトボックス', $select_value,$g_form_option_select);
//取引区分
$select_value = Select_Get($db_con,'trade_aord');
$form->addElement('select', 'trade_aord_select', 'セレクトボックス', $select_value,$g_form_option_select);
//担当者
$select_value = Select_Get($db_con,'staff',null,true);
$form->addElement('select', 'form_staff_select', 'セレクトボックス', $select_value,$g_form_option_select);
//受注確認画面へ
$form->addElement("submit","order_conf","受注確認画面へ", $disabled);
//受注
$form->addElement("submit","order","受　注", $disabled);
//合計
//$form->addElement("button","form_sum_button","合　計","onClick=\"javascript:Button_Submit('sum_button_flg','#','true')\"");

// ヘッダ部リンクボタン
$ary_h_btn_list = array("照会・変更" => "./1-2-105.php", "入　力" => "./1-2-101.php", "受注残一覧" => "./1-2-106.php");
Make_H_Link_Btn($form, $ary_h_btn_list, 2);

//hidden
$form->addElement("hidden", "hdn_client_id");       //得意先ID
$form->addElement("hidden", "attach_gid");          //所属グループID
$form->addElement("hidden", "client_search_flg");   //得意先コード入力フラグ
$form->addElement("hidden", "hdn_coax");            //丸め区分
$form->addElement("hidden", "hdn_tax_franct");      //端数区分
$form->addElement("hidden", "del_row");             //削除行
$form->addElement("hidden", "add_row_flg");         //追加行フラグ
$form->addElement("hidden", "max_row");             //最大行数
$form->addElement("hidden", "goods_search_row");    //商品コード入力行
$form->addElement("hidden", "sum_button_flg");      //合計ボタン押下フラグ
$form->addElement("hidden", "complete_flg");        //チェック完了ボタン押下フラグ
$form->addElement("hidden", "trans_check_flg");     //グリーン指定チェックフラグ
$form->addElement("hidden", "recomp_flg");          //出荷可能数フラグ

#2009-10-13 hashimoto-y
for($i = 0; $i < $max_row; $i++){
    if(!in_array("$i", $del_history)){
        $form->addElement("hidden","hdn_discount_flg[$i]");
    }
}

/****************************/
//出荷可能数入力
/****************************/
/*
if($_POST["recomp_flg"] == true){
    //出荷可能数
    $designated_date = ($_POST["form_designated_date"] != null)? $_POST["form_designated_date"] : 0;
    //数字以外が入力されている場合
    if(!ereg("^[0-9]+$", $designated_date)){
        $designated_date = 0;
    }

    $attach_gid   = $_POST["attach_gid"];     //得意先の所属グループ
    $ary_goods_id = $_POST["hdn_goods_id"];   //入力した商品ID

    //入力された商品の個数を再計算する
    for($i = 0; $i < count($ary_goods_id); $i++){
        //商品存在判定
        if($ary_goods_id[$i] != NULL){
            //再計算SQL
            $sql  = "SELECT";
            $sql .= "   t_goods.goods_id,";
            $sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,";
            $sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,";
            $sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.rstock_num,0) - COALESCE(t_allowance_io.allowance_io_num,0) ";
            $sql .= " END AS allowance_total,";
            $sql .= "   CASE t_goods.stock_manage WHEN 1 THEN";
            $sql .= "   COALESCE(t_stock.stock_num,0)"; 
            $sql .= "   + COALESCE(t_stock_io.order_num,0)";
            $sql .= "   - (COALESCE(t_stock.rstock_num,0)";
            $sql .= "   - COALESCE(t_allowance_io.allowance_io_num,0)) END AS stock_total ";
            $sql .= " FROM";
            $sql .= "   t_goods ";

            $sql .= "   INNER JOIN  t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
            $sql .= "   INNER JOIN  t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";

            $sql .= "   LEFT JOIN";
            $sql .= "   (SELECT";
            $sql .= "       t_stock.goods_id,";
            $sql .= "       SUM(t_stock.stock_num)AS stock_num,";
            $sql .= "       SUM(t_stock.rstock_num)AS rstock_num";
            $sql .= "       FROM";
            $sql .= "            t_stock INNER JOIN t_ware ON t_stock.ware_id = t_ware.ware_id";
            $sql .= "       WHERE";
            $sql .= "            t_stock.shop_id =  $shop_id";
            $sql .= "       AND";
            $sql .= "            t_ware.count_flg = 't'";
            $sql .= "       GROUP BY t_stock.goods_id";
            $sql .= "   )AS t_stock ON t_goods.goods_id = t_stock.goods_id";

            $sql .= "   LEFT JOIN";
            $sql .= "   (SELECT";
            $sql .= "       t_stock_hand.goods_id,";
            $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS order_num";
            $sql .= "   FROM";
            $sql .= "       t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id";
            $sql .= "   WHERE";
            $sql .= "       t_stock_hand.work_div = 3";
            $sql .= "   AND";
            $sql .= "       t_stock_hand.client_id = $shop_id";
            $sql .= "   AND";
            $sql .= "       t_ware.count_flg = 't'";
            $sql .= "   AND";
            $sql .= "       CURRENT_DATE <= t_stock_hand.work_day";
            $sql .= "   AND";
            $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)";
            $sql .= "   GROUP BY t_stock_hand.goods_id";
            $sql .= "   ) AS t_stock_io ON t_goods.goods_id=t_stock_io.goods_id";

            $sql .= "   LEFT JOIN";
            $sql .= "   (SELECT";
            $sql .= "       t_stock_hand.goods_id,";
            $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS allowance_io_num";
            $sql .= "   FROM";
            $sql .= "       t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id";
            $sql .= "   WHERE";
            $sql .= "       t_stock_hand.work_div = 1";
            $sql .= "   AND";
            $sql .= "       t_stock_hand.client_id = $shop_id";
            $sql .= "   AND";
            $sql .= "       t_ware.count_flg = 't'";
            $sql .= "   AND";
            $sql .= "       t_stock_hand.work_day >= (CURRENT_DATE + $designated_date)";
            $sql .= "   GROUP BY t_stock_hand.goods_id";
            $sql .= "   ) AS t_allowance_io ON t_goods.goods_id = t_allowance_io.goods_id";

            $sql .= " WHERE ";
            $sql .= "       t_goods.goods_id = $ary_goods_id[$i]";
            $sql .= " AND ";
            $sql .= "       t_goods.public_flg = 't' ";
            $sql .= " AND ";
            $sql .= "       initial_cost.rank_cd = '1' ";
            $sql .= " AND ";
            $sql .= "       sale_price.rank_cd = ";
            $sql .= "       (SELECT ";
            $sql .= "           rank_cd ";
            $sql .= "       FROM ";
//          $sql .= "           t_shop_gr ";
            $sql .= "           t_client ";
            $sql .= "       WHERE ";
//          $sql .= "           shop_gid = $attach_gid);";
            $sql .= "           shop_id  = $attach_gid);";

            $result = Db_Query($db_con, $sql);
            $goods_data = pg_fetch_array($result);

            $set_designated_data["hdn_goods_id"][$i]         = $goods_data[0];   //商品ID
            $set_designated_data["form_stock_num"][$i]       = $goods_data[1];   //実棚数
            $set_designated_data["hdn_stock_num"][$i]        = $goods_data[1];   //実棚数（hidden）
            $stock_num[$i]                                   = $goods_data[1];   //実棚数(リンクの値)
            $set_designated_data["form_rorder_num"][$i]      = $goods_data[2];   //発注済数
            $set_designated_data["form_rstock_num"][$i]      = $goods_data[3];   //引当数
            $set_designated_data["form_designated_num"][$i]  = $goods_data[4];   //出荷可能数
        }
    }

    //出荷可能数入力フラグに空白をセット
    $set_designated_data["recomp_flg"] = "";
    $form->setConstants($set_designated_data);
}

/****************************/
//エラーチェック(addRule)
/****************************/
//得意先

//出荷可能数
$form->addRule("form_designated_date","発注済数と引当数を考慮する日数は半角数値のみです。","regex", '/^[0-9]+$/');

//受注日
//●必須チェック
//●半角数字チェック
$form->addGroupRule('form_ord_day', array(
        'y' => array(
                array('受注日 の日付は妥当ではありません。', 'required'),
                array('受注日 の日付は妥当ではありません。', 'numeric')
        ),      
        'm' => array(
                array('受注日 の日付は妥当ではありません。','required'),
                array('受注日 の日付は妥当ではありません。', 'numeric')
        ),       
        'd' => array(
                array('受注日 の日付は妥当ではありません。','required'),
                array('受注日 の日付は妥当ではありません。', 'numeric')
        )       
));

//入荷予定日
//●半角数字チェック
$form->addGroupRule('form_arr_day', array(
        'y' => array(
                array('出荷予定日 の日付は妥当ではありません。', 'required'),
                array('出荷予定日 の日付は妥当ではありません。', 'numeric')
        ),
        'm' => array(
                array('出荷予定日 の日付は妥当ではありません。', 'required'),
                array('出荷予定日 の日付は妥当ではありません。','numeric')
        ),
        'd' => array(
                array('出荷予定日 の日付は妥当ではありません。', 'required'),
                array('出荷予定日 の日付は妥当ではありません。','numeric')
        ),
));

//出荷倉庫
//●必須チェック
$form->addRule("form_ware_select","出荷倉庫を選択してください。","required");

//取引区分
//●必須チェック
$form->addRule("trade_aord_select","取引区分を選択してください。","required");

//担当者
//●必須チェック
$form->addRule("form_staff_select","担当者を選択してください。","required");

//通信欄（本部宛）
//●文字数チェック
$form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
$form->addRule("form_note_client","通信欄（得意先宛）は50文字以内です。","mb_maxlength","50");

/****************************/
//受注ボタン押下処理
/****************************/
if($_POST["order"] == "受　注" || $_POST["order_conf"] == "受注確認画面へ" ){
    //ヘッダー情報
    $ord_no               = $_POST["form_order_no"];           //受注番号
    $designated_date      = $_POST["form_designated_date"];    //出荷可能数
    $ord_day_y            = $_POST["form_ord_day"]["y"];       //受注日
    $ord_day_m            = $_POST["form_ord_day"]["m"];            
    $ord_day_d            = $_POST["form_ord_day"]["d"];            
    $hope_day_y           = $_POST["form_hope_day"]["y"];      //希望納期
    $hope_day_m           = $_POST["form_hope_day"]["m"];           
    $hope_day_d           = $_POST["form_hope_day"]["d"];           
    $arr_day_y            = $_POST["form_arr_day"]["y"];       //入荷予定日
    $arr_day_m            = $_POST["form_arr_day"]["m"];            
    $arr_day_d            = $_POST["form_arr_day"]["d"];            
    $client_cd1           = $_POST["form_client"]["cd1"];      //得意先CD1
    $client_cd2           = $_POST["form_client"]["cd2"];      //得意先CD2
    $client_name          = $_POST["form_client"]["name"];     //得意先名
    $note_client          = $_POST["form_note_client"];        //通信欄（得意先）
    $note_head            = $_POST["form_note_head"];          //通信欄（本部）
    $trans_check          = $_POST["form_trans_check"];        //グリーン指定
    $trans_id             = $_POST["form_trans_select"];       //運送業者
    $direct_id            = $_POST["form_direct_select"];      //直送先
    $ware_id              = $_POST["form_ware_select"];        //倉庫
    $trade_aord           = $_POST["trade_aord_select"];       //取引区分
    $c_staff_id           = $_POST["form_staff_select"];       //担当者

    //データ情報
    $j = 0;
    for($i = 0; $i < $max_row; $i++){
        if($_POST["form_goods_name"][$i] != null){
            $goods_id[$j]         = $_POST["hdn_goods_id"][$i];                         //商品ID
            $goods_cd[$j]         = $_POST["form_goods_cd"][$i];                        //商品CD
            $goods_name[$j]       = $_POST["form_goods_name"][$i];                      //商品名
            $sale_num[$j]         = $_POST["form_sale_num"][$i];                        //受注数
            $cost_price_i[$j]     = $_POST["form_cost_price"][$i]["i"];                 //原価単価（整数部）
            $cost_price_d[$j]     = $_POST["form_cost_price"][$i]["d"];                 //原価単価（小数部）
            $sale_price_i[$j]     = $_POST["form_sale_price"][$i]["i"];                 //売上単価（整数部）
            $sale_price_d[$j]     = $_POST["form_sale_price"][$i]["d"];                 //売上単価（小数部）
            $tax_div[$j]          = $_POST["hdn_tax_div"][$i];                          //課税区分
            $stock_manage_flg[$j] = $_POST["hdn_stock_manage"][$i];                     //在庫管理
            $cost_amount[$j]      = str_replace(',','',$_POST["form_cost_amount"][$i]); //原価金額
            $sale_amount[$j]      = str_replace(',','',$_POST["form_sale_amount"][$i]); //売上金額
            $j++;

        }
    }

    /****************************/
    //エラーチェック(PHP)
    /****************************/
    $error_flg = false;                                         //エラー判定フラグ

    //商品選択チェック
    for($i = 0; $i < count($goods_id); $i++){
        if($goods_name[$i] != null){
           $input_error_flg = true;
        }
    }
    if($input_error_flg != true){
        $goods_error0 ="商品が一つも選択されていません。";
        $error_flg = true;
    }

    //◇受注日
    //・文字種チェック
    if($ord_day_y != null && $ord_day_m != null && $ord_day_d != null){
        $ord_day_y = (int)$ord_day_y;
        $ord_day_m = (int)$ord_day_m;
        $ord_day_d = (int)$ord_day_d;
        if(!checkdate($ord_day_m,$ord_day_d,$ord_day_y)){
            $form->setElementError("form_ord_day","受注日 の日付は妥当ではありません。");
        }else{
            $err_msge = Sys_Start_Date_Chk($ord_day_y, $ord_day_m, $ord_day_d, "受注日");
            if($err_msge != null){
                $form->setElementError("form_ord_day","$err_msge");
            }
        }       
    }

    //◇入荷予定日
    //・文字種チェック
    if($arr_day_y != null || $arr_day_m != null || $arr_day_d != null){
        $arr_day_y = (int)$arr_day_y;
        $arr_day_m = (int)$arr_day_m;
        $arr_day_d = (int)$arr_day_d;
        if(!checkdate($arr_day_m,$arr_day_d,$arr_day_y)){
            $form->setElementError("form_arr_day","入荷予定日 の日付は妥当ではありません。");
        }else{
            $err_msge = Sys_Start_Date_Chk($arr_day_y, $arr_day_m, $arr_day_d, "受注日");
            if($err_msge != null){
                $form->setElementError("form_arr_day","$err_msge");
            }

            $arr_day_y = str_pad($arr_day_y, 4, "0", STR_PAD_LEFT);
            $arr_day_m = str_pad($arr_day_m, 2, "0", STR_PAD_LEFT);
            $arr_day_d = str_pad($arr_day_d, 2, "0", STR_PAD_LEFT);
            $arr_day_ymd = $arr_day_y.$arr_day_m.$arr_day_d;

            $ord_day_y = str_pad($ord_day_y, 4, "0", STR_PAD_LEFT);
            $ord_day_m = str_pad($ord_day_m, 2, "0", STR_PAD_LEFT);
            $ord_day_d = str_pad($ord_day_d, 2, "0", STR_PAD_LEFT);
            $ord_day_ymd = $ord_day_y.$ord_day_m.$ord_day_d;

            if($arr_day_ymd < $ord_day_ymd){
                $form->setElementError("form_arr_day","出荷予定日は受注日以降の日付を指定してください。");
            }       
        }
    }
    
    //商品チェック
    //商品重複チェック
    for($i = 0; $i < count($goods_id); $i++){
        for($j = 0; $j < count($goods_id); $j++){
            if($i != $j && $goods_id[$i] == $goods_id[$j]){
                $goods_error1 = "同じ商品が２度選択されています。";
//                $error_flg = true;
            }
        }
    }

    //商品チェック
    //受注数、原価単価、売上単価、入力チェック
    for($i = 0; $i < count($goods_id); $i++){
        if($goods_id[$i] != null && ($sale_num[$i] == null || $cost_price_i[$i] == null || $cost_price_d[$i] == null || $sale_price_i[$i] == null || $sale_price_d[$i] == null)){
            $goods_error2 = "受注入力に受注数と原価単価と売上単価は必須です。";
            $error_flg = true;
        }

        //受注数半角数字チェック
        if(!ereg("^[0-9]+$",$sale_num[$i]) && $sale_num[$i] != null){
            $goods_error3 = "受注数は半角数字のみです。";
            $error_flg = true;
        }


        #2009-10-13 hashimoto-y 
        $hdn_discount_flg = $form->getElementValue("hdn_discount_flg[$i]");
        #echo $hdn_discount_flg ."<br>";

        if($hdn_discount_flg === 't'){

            //原価単価半角数字チェック
            if(!ereg("^[-0-9]+$",$cost_price_i[$i]) || !ereg("^[0-9]+$",$cost_price_d[$i])){
                $goods_error4 = "原価単価は「-」と半角数字のみ入力可能です。";
                $error_flg = true;
            }elseif($cost_price_i[$i] > 0){
                $goods_error4 = "商品に値引を指定した場合、原価単価は０以下の数値のみ入力可能です。";
                $error_flg = true;
            }

            //売上単価半角数字チェック
            if(!ereg("^[-0-9]+$",$sale_price_i[$i]) || !ereg("^[0-9]+$",$sale_price_d[$i])){
                $goods_error5 = "売上単価は「-」と半角数字のみ入力可能です。";
                $error_flg = true;
            }elseif($sale_price_i[$i] > 0){
                $goods_error5 = "商品に値引を指定した場合、売上単価は０以下の数値のみ入力可能です。";
                $error_flg = true;
            }

        }else{

            //原価単価半角数字チェック
            if(!ereg("^[0-9]+$",$cost_price_i[$i]) || !ereg("^[0-9]+$",$cost_price_d[$i])){
                $goods_error4 = "原価単価は半角数字のみです。";
                $error_flg = true;
            }

            //売上単価半角数字チェック
            if(!ereg("^[0-9]+$",$sale_price_i[$i]) || !ereg("^[0-9]+$",$sale_price_d[$i])){
                $goods_error5 = "売上単価は半角数字のみです。";
                $error_flg = true;
            }
        }

    }

    //エラーの場合はこれ以降の表示処理を行なわない
    if($form->validate() && $error_flg == false){

        //売上金額配列
        $sale_money = $_POST["form_sale_amount"];
        //課税区分配列
        $tax_div    = $_POST["hdn_tax_div"];

        //現在の消費税率
        #2009-12-21 aoyama-n
        #$sql  = "SELECT ";
        #$sql .= "    tax_rate_n ";
        #$sql .= "FROM ";
        #$sql .= "    t_client ";
        #$sql .= "WHERE ";
        #$sql .= "    client_id = $client_id;";
        #$result = Db_Query($db_con, $sql); 
        #$tax_num = pg_fetch_result($result, 0,0);

        #2009-12-21 aoyama-n
        $tax_rate_obj->setTaxRateDay($ord_day_y."-".$ord_day_m."-".$ord_day_d);
        $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

        $total_money = Total_Amount($sale_money, $tax_div,$coax,$tax_franct,$tax_num,$client_id, $db_con);
        $sale_money  = $total_money[0];
        $sale_tax    = $total_money[1];

        //日付の形式変更
        $ord_day  = $ord_day_y."-".$ord_day_m."-".$ord_day_d;
        if($hope_day_y != null){
            $hope_day = $hope_day_y."-".$hope_day_m."-".$hope_day_d;
        }
        if($arr_day_y != null){
            $arr_day  = $arr_day_y."-".$arr_day_m."-".$arr_day_d;
        }
        //受注ヘッダ・受注データ　登録・更新SQL
        Db_Query($db_con, "BEGIN");

        //登録ボタンが押された場合
        if($_POST["order"] == "受　注"){

            //変更処理判定
            if($aord_id != NULL){
                //受注ヘッダー変更
                $sql  = "UPDATE t_aorder_h SET ";
                $sql .= "    ord_time = '$ord_day',";
                $sql .= "    trade_id = '$trade_aord',";
                //運送業者が指定されているか
                if($trans_id != null){
                    $sql .= "    trans_id = $trans_id,";
                }
                //チェック値をbooleanに変更
                if($trans_check==1){
                    $sql .= "green_flg = true,";    
                }else{
                    $sql .= "green_flg = false,";    
                }
                //出荷予定日が指定されているか
                if($arr_day != null){
                    $sql .= "    arrival_day = '$arr_day',";
                }
                //$sql .= "    note_my = '$note_head',";
                $sql .= "    note_your = '$note_client',";
                $sql .= "    net_amount = $sale_money,";    
                $sql .= "    tax_amount = $sale_tax,";    
                $sql .= "    c_staff_id = $c_staff_id,";
                $sql .= "    ware_id = $ware_id,";
                $sql .= "    ord_staff_id = $o_staff_id, ";
                $sql .= ($trans_id != NULL) ? " trans_name = (SELECT trans_name FROM t_trans WHERE trans_id = $trans_id), " : " trans_name = NULL, ";
                $sql .= ($trans_id != NULL) ? " trans_cname = (SELECT trans_cname FROM t_trans WHERE trans_id = $trans_id) " : " trans_cname = NULL, ";
                $sql .= "    change_day = CURRENT_TIMESTAMP ";
                $sql .= "WHERE ";
                $sql .= "    aord_id = $aord_id;";

                $result = Db_Query($db_con,$sql);
                if($result == false){
                    Db_Query($db_con,"ROLLBACK;");
                    exit;
                }

                //受注データを削除
                $sql  = "DELETE FROM";
                $sql .= "    t_aorder_d";
                $sql .= " WHERE";
                $sql .= "    aord_id = $aord_id";
                $sql .= ";";

                $result = Db_Query($db_con, $sql );
                if($result == false){
                    Db_Query($db_con, "ROLLBACK");
                    exit;
                }
            }

            //受注データ登録
            for($i = 0; $i < count($goods_id); $i++){
                //行
                $line = $i + 1;

                //形式変更
                $c_price = $cost_price_i[$i].".".$cost_price_d[$i];   //原価金額
                $s_price = $sale_price_i[$i].".".$sale_price_d[$i];   //売上金額

                $total_tax = Total_Amount($sale_amount[$i], $tax_div[$i],$coax,$tax_franct,$tax_num, $client_id, $db_con);
          
                //消費税額
                $t_price = $total_tax[1];

                $sql  = "INSERT INTO t_aorder_d (";
                $sql .= "    aord_d_id,";
                $sql .= "    aord_id,";
                $sql .= "    line,";
                $sql .= "    goods_id,";
                $sql .= "    goods_cd,";
                //$sql .= "    goods_name,";
                $sql .= "    official_goods_name,";
                $sql .= "    num,";
                $sql .= "    tax_div,";
                $sql .= "    cost_price,";
                $sql .= "    cost_amount,";
                $sql .= "    sale_price,";
                $sql .= "    sale_amount";
                $sql .= ")VALUES(";
                $sql .= "    (SELECT COALESCE(MAX(aord_d_id), 0)+1 FROM t_aorder_d),";  
                $sql .= "    (SELECT";
                $sql .= "         aord_id";
                $sql .= "     FROM";
                $sql .= "        t_aorder_h";
                $sql .= "     WHERE";
                $sql .= "        ord_no = '" .$h_data_list[0][0] ."'";
                $sql .= "        AND";
                $sql .= "        shop_id = $shop_id";
                $sql .= "    ),";
                $sql .= "    '$line',";
                $sql .= "    $goods_id[$i],";
                $sql .= "    '$goods_cd[$i]',";
                $sql .= "    '$goods_name[$i]',"; 
                $sql .= "    '$sale_num[$i]',";
                $sql .= "    '$tax_div[$i]',";
                $sql .= "    $c_price,";
                $sql .= "    $cost_amount[$i],";
                $sql .= "    $s_price,";
                $sql .= "    $sale_amount[$i]";
                $sql .= ");";

                $result = Db_Query($db_con, $sql);

                if($result == false){
                    Db_Query($db_con, "ROLLBACK");
                    exit;
                }
            }

            for($i = 0; $i < count($goods_id); $i++){
                $line = $i + 1;

                if($stock_manage_flg[$i] == '1'){
                    //受け払いテーブルに登録
                    $sql  = " INSERT INTO t_stock_hand (";
                    $sql .= "    goods_id,";
                    $sql .= "    enter_day,";
                    $sql .= "    work_day,";
                    $sql .= "    work_div,";
                    $sql .= "    client_id,";
                    $sql .= "    ware_id,";
                    $sql .= "    io_div,";
                    $sql .= "    num,";
                    $sql .= "    slip_no,";
                    $sql .= "    aord_d_id,";
                    $sql .= "    staff_id,";
                    $sql .= "    shop_id,";
                    $sql .= "    client_cname";
                    $sql .= ")VALUES(";
                    $sql .= "    $goods_id[$i],";
                    $sql .= "    NOW(),";
                    $sql .= "    '$ord_day',";
                    $sql .= "    '1',";
                    $sql .= "    $client_id,";
                    $sql .= "    $ware_id,";
                    $sql .= "    '2',";
                    $sql .= "    $sale_num[$i],";
                    $sql .= "    '$ord_no',";
                    $sql .= "    (SELECT";
                    $sql .= "        aord_d_id";
                    $sql .= "    FROM";
                    $sql .= "        t_aorder_d";
                    $sql .= "    WHERE";
                    $sql .= "        line = $line";
                    $sql .= "        AND";
                    $sql .= "        aord_id = (SELECT";
                    $sql .= "                    aord_id";
                    $sql .= "                 FROM";
                    $sql .= "                    t_aorder_h";
                    $sql .= "                 WHERE";
                    $sql .= "                    ord_no = '".$h_data_list[0][0] ."'";
                    $sql .= "                    AND";
                    $sql .= "                    shop_id = $shop_id";
                    $sql .= "                )";
                    $sql .= "    ),";
                    $sql .= "    $o_staff_id,";
                    $sql .= "    $shop_id,";
                    $sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id)";
                    $sql .= ");";

                    $result = Db_Query($db_con, $sql);
                    if($result == false){
                        Db_Query($db_con, "ROLLBACK");
                        exit;
                    }
                }
            }

            Db_Query($db_con, "COMMIT");
            header("Location: ./1-2-108.php?aord_id=$aord_id&input_flg=true");
        }else{
            $freeze_flg = true;
            $form->freeze();
        }
    }
}

/****************************/
//部品作成（可変）
/****************************/
//行番号カウンタ
$row_num = 1;

for($i = 0; $i < $max_row; $i++){
    //表示行判定
    if(!in_array("$i", $del_history)){
        $del_data = $del_row.",".$i;


        #2009-10-13 hashimoto-y
        //値引商品を選択した場合には赤字に変更
        $font_color = "";

        $hdn_discount_flg = $form->getElementValue("hdn_discount_flg[$i]");

        if($hdn_discount_flg === 't'){
            $font_color = "color: red; ";
        }else{
            $font_color = "color: #000000; ";
        }


        //商品コード      
        $form->addElement(
            "text","form_goods_cd[$i]","",
            "size=\"11\" maxLength=\"9\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: left\" readonly'"
        );
      
        //商品名
        $form->addElement(
            "text","form_goods_name[$i]","",
            "size=\"52\" maxLength=\"41\" 
            style=\"$font_color
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: left\" readonly'"
        );

        //実棚数
        //在庫管理判定
        if($no_goods_flg!=true && ($_POST["hdn_stock_manage"][$i] == '1' || $hdn_stock_manage[$i] == '1')){
            //有

            //POSTする前のとき以外は、hiddenの値を使用する(実棚数)
            if($_POST["hdn_stock_num"][$i] != NULL){
                $hdn_num = $_POST["hdn_stock_num"][$i];
            }else{
                $hdn_num = $stock_num[$i];
            }
            //POSTする前のとき以外は、hiddenの値を使用する(商品ID)
            if($_POST["hdn_goods_id"][$i] != NULL){
                if($_POST["hdn_goods_id"][$i] == $hdn_goods_id[$i]){
                    $hdn_id = $_POST["hdn_goods_id"][$i];
                }else{
                    $hdn_id = $hdn_goods_id[$i];
                }
            }else{
                $hdn_id = $hdn_goods_id[$i];
            }
//            $form->addElement("link","form_stock_num[$i]","","#","$hdn_num","onClick=\"Open_mlessDialog_g('1-2-107.php',$hdn_id,$client_id,300,160);\"");
            $form->addElement("link","form_stock_num[$i]","","#","$hdn_num","onClick=\"Open_mlessDialmg_g('1-2-107.php',$hdn_id,$client_id,300,160);\"");
        }else if($no_goods_flg!=true && ($_POST["hdn_stock_manage"][$i] == '2' || $hdn_stock_manage[$i] == '2')){
            //無
            #2009-10-13 hashimoto-y
            #$form->addElement("static","form_stock_num[$i]","#","-","");

            $form->addElement(
                "text","form_stock_num[$i]","",
                "size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            $form->setConstants(array("form_stock_num[$i]" => "-"));

        $form->addElement(
            "text","form_goods_name[$i]","",
            "size=\"52\" maxLength=\"41\" 
            style=\"$font_color
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: left\" readonly'"
        );
        }else{
            $form->addElement(
                "text","form_stock_num[$i]","",
                "size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
        }

        //発注済数
        //在庫管理判定
        if($_POST["hdn_stock_manage"][$i] == '2' || $hdn_stock_manage[$i] == '2'){
            //無
            #2009-10-13 hashimoto-y
            #$form->addElement("static","form_rorder_num[$i]","#","-","");

            $form->addElement(
                "text","form_rorder_num[$i]","",
                "class=\"money\" size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            $form->setConstants(array("form_rorder_num[$i]" => "-"));

        }else{
            $form->addElement(
                "text","form_rorder_num[$i]","",
                "class=\"money\" size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
        }

        //引当数
        //在庫管理判定
        if($_POST["hdn_stock_manage"][$i] == '2' || $hdn_stock_manage[$i] == '2'){
            //無
            #2009-10-13 hashimoto-y
            #$form->addElement("static","form_rstock_num[$i]","#","-","");

            $form->addElement("text","form_rstock_num[$i]","",
                "class=\"money\" size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            $form->setConstants(array("form_rstock_num[$i]" => "-"));

        }else{
            $form->addElement("text","form_rstock_num[$i]","",
                "class=\"money\" size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
        }

        //出荷可能数
        if($_POST["hdn_stock_manage"][$i] == '2' || $hdn_stock_manage[$i] == '2'){
            //無
            #2009-10-13 hashimoto-y
            #$form->addElement("static","form_designated_num[$i]","#","-","");

            $form->addElement("text","form_designated_num[$i]","",
                "class=\"money\" size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            $form->setConstants(array("form_designated_num[$i]" => "-"));

        }else{
            $form->addElement("text","form_designated_num[$i]","",
                "class=\"money\" size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
        }

        //受注数
        $form->addElement(
            "text","form_sale_num[$i]","",
            "class=\"money\" size=\"11\" maxLength=\"9\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
        );

        //原価単価
        $form_cost_price[$i][] =& $form->createElement(
            "text","i","",
            "size=\"11\" maxLength=\"9\"
            class=\"money\"
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
        );
        $form_cost_price[$i][] =& $form->createElement("static","","",".");
        $form_cost_price[$i][] =& $form->createElement(
            "text","d","","size=\"2\" maxLength=\"2\" 
                style=\"$font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
        );
        $form->addGroup( $form_cost_price[$i], "form_cost_price[$i]", "");

        //原価金額
        $form->addElement(
            "text","form_cost_amount[$i]","",
            "size=\"25\" maxLength=\"18\" 
            style=\"$g_form_style; $font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );
        
        //売上単価
        $form_sale_price[$i][] =& $form->createElement(
            "text","i","",
            "size=\"11\" maxLength=\"9\"
            class=\"money\"
            style=\"$g_form_style; $font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );
        $form_sale_price[$i][] =& $form->createElement("static","","",".");
        $form_sale_price[$i][] =& $form->createElement(
            "text","d","","size=\"2\" maxLength=\"2\" 
            style=\"$g_form_style; $font_color
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );
        $form->addGroup( $form_sale_price[$i], "form_sale_price[$i]", "");

        //売上金額
        $form->addElement(
            "text","form_sale_amount[$i]","",
            "size=\"25\" maxLength=\"18\" 
            style=\"$g_form_style; $font_color
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );

        //検索リンク
        $form->addElement(
            "link","form_search[$i]","","#","検索",
            "onClick=\"return Open_SubWin('../dialog/1-0-210.php', Array('form_goods_cd[$i]','goods_search_row'), 500, 450,5,1,$i);\""
        );

        //削除リンク
        //1.0.4 (2006/03/29) kaji 確認ダイアログのキャンセルボタン押下時でも登録されてしまうバグ対策
        $form->addElement(
            "link","form_del_row[$i]","",
            "#","削除","onClick=\"return Dialogue_1('削除します。', '$del_data', 'del_row');\""
        ); 

        //商品ID
        $form->addElement("hidden","hdn_goods_id[$i]");
        //課税区分
        $form->addElement("hidden", "hdn_tax_div[$i]");
        //品名変更フラグ
        $form->addElement("hidden","hdn_name_change[$i]");
        //在庫管理
        $form->addElement("hidden","hdn_stock_manage[$i]");
        //実棚数
        $form->addElement("hidden","hdn_stock_num[$i]");

        /****************************/
        //表示用HTML作成
        /****************************/
        if($freeze_flg == true && $hdn_discount_flg === 't'){
            $html .= "<tr class=\"Result1\">";
            $html .=    "<td align=\"right\">$row_num</td>";
            $html .=    "<td align=\"left\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_goods_cd[$i]"]]->toHtml();
            $html .=    "<br>";
            $html .=        $form->_elements[$form->_elementIndex["form_goods_name[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .= "  <td align=\"right\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_stock_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .= "  <td align=\"right\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_rorder_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .= "  <td align=\"right\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_rstock_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .= "  <td align=\"right\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_designated_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .=    "<td align=\"right\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_sale_num[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .=    "<td align=\"right\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_cost_price[$i]"]]->toHtml();
            $html .=    "<br>";
            $html .=        $form->_elements[$form->_elementIndex["form_sale_price[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .=    "<td align=\"right\" style=\"color: red\">";
            $html .=        $form->_elements[$form->_elementIndex["form_cost_amount[$i]"]]->toHtml();
            $html .=    "<br>";
            $html .=        $form->_elements[$form->_elementIndex["form_sale_amount[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .= "</tr>";
        }else{
            $html .= "<tr class=\"Result1\">";
            $html .=    "<td align=\"right\">$row_num</td>";
            $html .=    "<td align=\"left\">";
            $html .=        $form->_elements[$form->_elementIndex["form_goods_cd[$i]"]]->toHtml();
            $html .=    "<br>";
            $html .=        $form->_elements[$form->_elementIndex["form_goods_name[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .= "  <td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_stock_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_rorder_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_rstock_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_designated_num[$i]"]]->toHtml();
            $html .= "  </td>";
            $html .=    "<td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_sale_num[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .=    "<td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_cost_price[$i]"]]->toHtml();
            $html .=    "<br>";
            $html .=        $form->_elements[$form->_elementIndex["form_sale_price[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .=    "<td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_cost_amount[$i]"]]->toHtml();
            $html .=    "<br>";
            $html .=        $form->_elements[$form->_elementIndex["form_sale_amount[$i]"]]->toHtml();
            $html .=    "</td>";
            $html .= "</tr>";
        }

        //行番号を＋１
        $row_num = $row_num+1;
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
$page_menu = Create_Menu_h('sale','1');
/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= Print_H_Link_Btn($form, $ary_h_btn_list);
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
    'warning'       => "$warning",
    'html'          => "$html",
    'goods_error0'  => "$goods_error0",
    'goods_error1'  => "$goods_error1",
    'goods_error2'  => "$goods_error2",
    'goods_error3'  => "$goods_error3",
    'goods_error4'  => "$goods_error4",
    'goods_error5'  => "$goods_error5",
    'aord_id'       => "$aord_id",
    'duplicate_err' => "$error",
    'form_potision' => "$form_potision",
    'auth_r_msg'    => "$auth_r_msg",
    'freeze_flg'    => "$freeze_flg",
));

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
