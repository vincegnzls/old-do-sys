<?php
$page_title = "発注入力";

/***********変更履歴(2006/4/4)****************/
//・仕入単価半角数字チェック条件文変更
//・商品重複チェック条件文変更
//・t_order_dにINSERT時の条件追加
/**********************************************/
/***********変更履歴(2006/5/17)****************/
//・追加・削除・商品コード入力の際に、スクロールしないに変更
//・発注後にフリーズした画面を表示して、そこでDBに登録に変更
//・削除行は、エラー判定しないように変更
//・追加リンクをボタンに変更
//・登録後に戻るボタンから遷移した場合に、発注先を変更可能にした
/**********************************************/
/***********変更履歴(2006/5/25)****************/
//・フリーズした画面に、確認メッセージを表示
//・検索リンクにフォーカスが当たらないように変更
/**********************************************/
/***********変更履歴(2006/07/07)***************/
//kaji
//・shop_gidをなくす
/**********************************************/
/***********変更履歴(2006/07/07)***************/
//koji
//・発注日を変更可能に変更
/**********************************************/
/***********変更履歴(2006/07/31)***************/
//watanabe-k
//・消費税の計算方法変更
/**********************************************/
/***********変更履歴(2006/08/07)***************/
//watanabe-k
//・本部に発注した場合に発注状況を登録しないバグの修正
/**********************************************/
/***********変更履歴(2006/09/20)***************/
// fukuda-sss
//・権限処理修正
/**********************************************/
/***********変更履歴(2006/10/12)***************/
// watanabe-k
//・略称登録
/**********************************************/
/*変更履歴***************
 *   2006/12/01 (suzuki)
 *     ・仕入金額計算処理変更
 *     ・仕入先変更時に商品初期化
*************************/

/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/17　06-007　　　　watanabe-k　発注入力画面で商品名をブランクにして発注を行うと「商品が一つも選択されていません」とメッセージがでる。
 * 　2006/10/18　06-016　　　　watanabe-k　在庫照会→発注入力へ画面遷移して場合の初期表示で選択していた商品が表示されていないバグの修正。
 * 　2006/10/18　06-014　　　　watanabe-k　発注入力画面で本部、FCで文言が異なるバグの修正
 * 　2006/10/18　06-017　　　　watanabe-k　発注入力画面で運送業者を選択して発注を行うとSQLエラーが表示されるバグの修正
 * 　2006/10/18　06-018　　　　watanabe-k　不正な商品コードを入力してもエラーメッセージが表示されないバグの修正
 * 　2006/10/18　06-019　　　　watanabe-k　不正な仕入単価を入力してもエラーメッセージが表示されないバグの修正
 * 　2006/10/18　06-022　　　　watanabe-k　発注日のエラーメッセージが本部とFCで異なるバグの修正
 * 　2006/10/18　06-032　　　　watanabe-k　システム開始日より前の日で発注ができてしまうバグの修正
 * 　2006/10/18　06-033　　　　watanabe-k　金額の小数点以下をブランクにして発注するとエラーが表示されるバグの修正
 * 　2006/10/18  06-024        watanabe-k  同時処理を行うとエラーになるバグの修正
 * 　2006/10/18  06-039        watanabe-k  URL操作でSQLエラーが表示されるバグの修正
 * 　2006/10/19  06-041        watanabe-k  商品コードを不正入力し、フォーカスを異動しないまま発注確認画面へボタンをクリックすると、確認画面に遷移してしまう。
 * 　2006/10/19  06-045        watanabe-k  仕入先コードを不正入力し、フォーカスを異動しないまま発注確認画面へボタンをクリックすると、確認画面に遷移してしまう。
 * 　2006/11/11  06-092        watanabe-k  商品名が表示しきれていないバグの修正
 * 　2006/11/11  06-097        watanabe-k  仕入変更後に発注が変更できてしまうバグの修正
 * 　2006/11/11  wat-0134      watanabe-k  初期表示処理が何度も実行されるバグの修正
 * 　2007/01/31                watanabe-k  発注先と直送先の電話番号を残すように修正
 * 　2007/02/06                watanabe-k  発信日を表示しないように修正 
 *   2007/02/21                watanabe-k  初期表示の倉庫を拠点倉庫に変更  
 *   2007/02/27                morita-d    商品名は正式名を表示するように修正  
 *   2007/03/13                watanabe-k   商品の重複選択エラーを具体的に表示するように修正
 *   2007/04/06                watanabe-k  取消された発注を再度登録するとクエリエラーが表示されるバグの修正
 *   2007/05/18                watanabe-k  直送先のプルダウンを当幅フォントに修正
 *   2007/07/13                watanabe-k  ０除算の警告が表示されるバグの修正 
 *   2007/08/01                watanabe-k  顧客区分コードが特殊の場合にFCに対する発注でも本部商品を使用かのうにするように修正 
 * 	 2009/06/17	改修No.13	   aizawa-m	   在庫照会のソートと同じ条件に変更
 *				改修おまけ	   aizawa-m	   画面「仕入金額」のデフォルトを0で出力するように変更
 *   2009/09/07                aoyama-n    値引機能追加
 *   2009/09/07                aoyama-n    出荷可能数考慮日入力時のSQLエラー修正
 *   2009/09/07                aoyama-n    出荷可能数考慮日入力時で商品名に商品分類が表示されない不具合修正
 *   2009/09/15                hashimoto-y 商品の値引きを赤字表示に修正
 *   2009/10/12                hashimoto-y 在庫管理フラグをショップ別商品情報テーブルに変更
 *   2009/12/21                aoyama-n    税率をTaxRateクラスから取得
 */

//環境設定ファイル
require_once("ENV_local.php");
require_once(INCLUDE_DIR."function_buy.inc");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"], null, "onSubmit=return confirm(true)");

//DB接続
$conn = Db_Connect();

// 権限チェック
$auth       = Auth_Check($conn);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/*****************************/
// 再遷移先をSESSIONにセット
/*****************************/
// GET、POSTが無い場合
if ($_GET == null && $_POST == null){
    Set_Rtn_Page("ord");
}

/****************************/
//外部変数取得
/****************************/
//$shop_gid = $_SESSION["shop_gid"];
$shop_id    = $_SESSION["client_id"];
$rank_cd    = $_SESSION["rank_cd"];
$staff_id   = $_SESSION["staff_id"];
$group_kind = $_SESSION["group_kind"];

//顧客区分が特殊の場合
if($rank_cd == '0055'){
    $furein_flg = true;
}


//商品ID
if(count($_GET["order_goods_id"]) > 0){
    $get_goods_id = $_GET["order_goods_id"];
    Get_Id_Check3($_GET["order_goods_id"]);
    $get_flg = true;
}

//商品IDが選択されているときだけセット
//if($_POST["hdn_goods_id"][$i] != NULL){
if(count($_POST["hdn_goods_id"]) > 0){
    $goods_id = $_POST["hdn_goods_id"];
}
//発注IDを取得
$order_id = $_POST["hdn_order_id"];
if($_GET["ord_id"] != null){
    $order_id = $_GET["ord_id"];
    Get_Id_Check3($_GET["ord_id"]);
    $update_flg = true;
}

//対象商品
if($_GET["target_goods"] != NULL){
    Get_Id_Check3($_GET["target_goods"]);
    $set_id_data["hdn_target"] = $_GET["target_goods"];
    $get_target_goods = $_GET["target_goods"];
    $form->setConstants($set_id_data);
}else{
    $get_target_goods = $_POST["hdn_target"];
}
/*
//本部・FC判定
if($get_target_goods == 1){
    $head_flg = 't';
}else{
    $head_flg = 'f';
}
$head_fc_data["head_flg"] = $head_flg;
$form->setConstants($head_fc_data);
*/
//出荷可能日数
if($_GET["designated_date"] != NULL){
    Get_Id_Check3($_GET["designated_date"]);
    $set_id_data["hdn_design"] = $get_designated_date;
    $form->setConstants($set_id_data);
}else{
    $get_designated_date = $_POST["hdn_design"];
}
/****************************/
//初期設定
/****************************/

#2009-12-21 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);

//表示行数
if($_POST["max_row"] != null){
    $max_row = $_POST["max_row"];
}else{
    $max_row = 5;
}

//初期表示位置変更
$form_position = "<body bgcolor=\"#D8D0C8\">";

//削除行数
$del_history[] = null;

//仕入先が指定されているか
if($_POST["hdn_client_id"] == null || $_POST["hdn_client_id"] == ""){
    $warning = "仕入先を選択してください。";
}elseif($_POST["hdn_client_id"] != null){
    $client_search_flg = true;

    $head_flg = $_POST["head_flg"];

    if($head_flg == 't'){
        $message = "本部商品のみ入力可能です。";
    //顧客区分コードが特殊の場合
    }elseif($furein_flg === true){
        $message = "商品が入力可能です。";
    }else{
        $message = "本部商品以外が入力可能です。";
    }

    $client_id  = $_POST["hdn_client_id"];
    $head_flg   = $_POST["head_flg"];
    $coax       = $_POST["hdn_coax"];
    $tax_franct = $_POST["hdn_tax_franct"];
}

//自分の消費税額を抽出
#2009-12-21 aoyama-n
#$sql  = "SELECT";
#$sql .= "   tax_rate_n";
#$sql .= " FROM";
#$sql .= "   t_client";
#$sql .= " WHERE";
#$sql .= "   client_id = $shop_id";
#$sql .= ";";

#$result = Db_Query($conn, $sql);
#$tax_rate = pg_fetch_result($result,0,0);
#$rate  = bcdiv($tax_rate,100,2);                //消費税率

/****************************/
//行削除処理
/****************************/
if($_POST["del_row"] != NULL){
    $now_form = null;
    //削除リストを取得
    $del_row = $_POST["del_row"];

    //削除履歴を配列にする。
    $del_history = explode(",", $del_row);
    //削除した行数
    $del_num     = count($del_history)-1;
}

/****************************/
//行数追加
/****************************/
if($_POST["add_row_flg"]==true){
    //最大行に、＋５する
    $max_row = $_POST["max_row"]+5;

    //行数追加フラグをクリア
    $add_row_data["add_row_flg"] = "";
    $form->setConstants($add_row_data);

}

/***************************/
//初期値設定
/***************************/
//出荷可能数
$def_data["form_designated_date"] = 7;

//担当者
$def_data["form_staff"] = $staff_id;

//取引区分
$def_data["form_trade"] = 21;

//発注日
$def_data["form_order_day"]["y"] = date("Y");
$def_data["form_order_day"]["m"] = date("m");
$def_data["form_order_day"]["d"] = date("d");

//自動採番の発注番号取得
$sql  = "SELECT";
$sql .= "   MAX(ord_no)";
$sql .= " FROM";
$sql .= "   t_order_h";
$sql .= " WHERE";
$sql .= "   shop_id = $shop_id";
$sql .= ";";

$result = Db_Query($conn, $sql);
$order_no = pg_fetch_result($result, 0 ,0);
$order_no = $order_no +1;
$order_no = str_pad($order_no, 8, 0, STR_PAD_LEFT);

$def_data["form_order_no"] = $order_no;

//倉庫
/*
$sql  = "SELECT";
$sql .= "   ware_id";
$sql .= " FROM";
$sql .= "   t_client";
$sql .= " WHERE";
$sql .= "   client_id = $shop_id";
$sql .= ";";
$result = Db_Query($conn, $sql);
$def_ware_id = pg_fetch_result($result ,0,0);
*/
$def_ware_id = Get_ware_id($conn, Get_Branch_Id($conn));
$def_data["form_ware"] = $def_ware_id;

$form->setDefaults($def_data);

//実棚数リンクに値をセット&在庫管理フラグ取得
$const_data["form_stock_num"] = $_POST["hdn_stock_num"];

$form->setConstants($const_data);

$stock_num = $_POST["hdn_stock_num"];
$stock_manage = $_POST["hdn_stock_manage"];
$name_change = $_POST["hdn_name_change"];
/****************************/
//変更フラグがtrueの場合
/****************************/
//if($update_flg == true){
if($update_flg == true && $_POST["hdn_first_set"] == null){
    //発注ヘッダ情報抽出
    $sql  = "SELECT";
    $sql .= "   t_order_h.ord_no,";
    $sql .= "   to_date(t_order_h.ord_time,'YYYY-MM-DD'),";
    $sql .= "   t_order_h.hope_day,";
    $sql .= "   t_order_h.arrival_day,";
    $sql .= "   t_order_h.green_flg,";
    $sql .= "   t_order_h.trans_id,";
    $sql .= "   t_client.client_id,";
    $sql .= "   t_client.client_cd1,";
    $sql .= "   t_client.client_cname,";
    $sql .= "   t_client.coax,";
    $sql .= "   t_client.tax_franct,";
    $sql .= "   t_client.head_flg,";
    $sql .= "   t_order_h.direct_id,";
    $sql .= "   t_order_h.ware_id,";
    $sql .= "   t_order_h.trade_id,";
    $sql .= "   t_order_h.c_staff_id,";
    $sql .= "   t_order_h.note_your,";
    $sql .= "   t_order_h.ord_stat,";
    $sql .= "   to_char(t_order_h.send_date, 'yyyy-mm-dd hh24:mi') AS send_date,";
    $sql .= "   t_order_h.enter_day";
    $sql .= " FROM ";
    $sql .= "   t_order_h";
    $sql .= "   INNER JOIN t_client";
    $sql .= "   ON t_order_h.client_id = t_client.client_id";
    $sql .= " WHERE";
    $sql .= "   ord_id = $order_id";
    $sql .= "   AND";
    $sql .= "   t_order_h.shop_id = $shop_id";
    $sql .= "   AND";
    $sql .= "   (ord_stat = '3' OR (ord_stat IS NULL AND ps_stat = '1'))";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    Get_Id_Check($result);
    $update_data = pg_fetch_array($result, 0);

    //本部フラグ判定
    if($update_data[11] == 't'){
        $message = "本部商品のみ選択可能です。";
        $head_flg = 't';
    }else{
        $message = "本部商品以外が選択可能です。";
        $head_flg = 'f';
    }

    //仕入先選択フラグ
    $client_search_flg = true;
    $warning           = null;

    //日付生成
    $update_order_day       = explode('-',$update_data[1]);
    $update_hope_day        = explode('-',$update_data[2]);
    $update_arrival_day     = explode('-',$update_data[3]);

    //仕入先ID
    $client_id = $update_data[6];

    //データセット
    $set_update_data["form_order_no"]           = $update_data[0];              //発注番号
    $set_update_data["form_order_day"]["y"]     = $update_order_day[0];         //発注日（年）
    $set_update_data["form_order_day"]["m"]     = $update_order_day[1];         //発注日（月）
    $set_update_data["form_order_day"]["d"]     = $update_order_day[2];         //発注日（日）
    $set_update_data["form_hope_day"]["y"]      = $update_hope_day[0];          //希望納期（年）
    $set_update_data["form_hope_day"]["m"]      = $update_hope_day[1];          //希望納期（月）
    $set_update_data["form_hope_day"]["d"]      = $update_hope_day[2];          //希望納期（日）
    $set_update_data["form_arrival_day"]["y"]   = $update_arrival_day[0];       //入荷予定日（年）
    $set_update_data["form_arrival_day"]["m"]   = $update_arrival_day[1];       //入荷予定日（月）
    $set_update_data["form_arrival_day"]["d"]   = $update_arrival_day[2];       //入荷予定日（日）
    if($head_flg == 't'){
        $set_update_data["form_trans"]          = ($update_data[4] == 't')? 1 : null;              //グリーン指定業者
    }else{
        $set_update_data["form_trans"]          = $update_data[5];              //運送業者
    }
    $set_update_data["hdn_client_id"]           = $update_data[6];              //得意先ID
    $set_update_data["form_client"]["cd"]       = $update_data[7];              //仕入先コード
    $set_update_data["form_client"]["name"]     = $update_data[8];              //仕入先名
    $set_update_data["hdn_coax"]                = $update_data[9];              //金額（丸め区分）
    $set_update_data["hdn_tax_franct"]          = $update_data[10];             //消費税端数
    $set_update_data["head_flg"]                = $update_data[11];             //本部フラグ
    $set_update_data["form_direct"]             = $update_data[12];             //直送先
    $set_update_data["form_ware"]               = $update_data[13];             //倉庫
    $set_update_data["form_trade"]              = $update_data[14];             //取引区分
    $set_update_data["form_staff"]              = $update_data[15];             //担当者
    $set_update_data["form_note_your"]            = $update_data[16];             //通信欄（仕入先宛）
    $set_update_data["hdn_order_id"]            = $order_id;
    //$set_update_data["form_send_date"]          = $update_data["send_date"];
    $set_update_data["hdn_ord_enter_day"]       = $update_data["enter_day"];    //登録日

    //仕入先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   coax,";
    $sql .= "   tax_franct";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_id = $client_id";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $coax = pg_fetch_result($result, 0,0 );
    $tax_franct = pg_fetch_result($result, 0, 1);

    //発注データ情報抽出
    $sql  = "SELECT ";
    $sql .= "   t_goods.goods_id,";
    $sql .= "   t_goods.name_change,";
    #2009-10-12 hashimoto-y
    #$sql .= "   t_goods.stock_manage,";
    $sql .= "   t_goods_info.stock_manage,";

    $sql .= "   t_order_d.goods_cd,";
    $sql .= "   t_order_d.goods_name,";
    #2009-10-12 hashimoto-y
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,";
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num, ";
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num, ";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";

    $sql .= "   END AS allowance_total,";
    $sql .= "   COALESCE(t_stock.stock_num,0)";
    $sql .= "   + COALESCE(t_stock_io.order_num,0)";
    $sql .= "   - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,";
    $sql .= "   t_order_d.num,";
    $sql .= "   t_order_d.buy_price,";
    $sql .= "   t_order_d.tax_div,";
    $sql .= "   t_order_d.buy_amount,";
    //aoyama-n 2009-09-07
    #$sql .= "   t_goods.in_num";
    $sql .= "   t_goods.in_num,";
    $sql .= "   t_goods.discount_flg";
    $sql .= " FROM ";
    $sql .= "   t_order_d";
    $sql .= "   INNER JOIN ";
    $sql .= "   t_order_h";
    $sql .= "   ON t_order_d.ord_id = t_order_h.ord_id ";
    $sql .= "   INNER JOIN ";
    $sql .= "   t_goods ";
    $sql .= "   ON t_order_d.goods_id = t_goods.goods_id";
    $sql .= "   LEFT JOIN ";

    //在庫数
    $sql .= "   (SELECT ";
    $sql .= "   t_stock.goods_id,";
    $sql .= "   SUM(t_stock.stock_num)AS stock_num, ";
    $sql .= "   SUM(t_stock.rstock_num)AS rstock_num";
    $sql .= "   FROM t_stock INNER JOIN t_ware ON t_stock.ware_id = t_ware.ware_id";
    $sql .= "   WHERE t_stock.shop_id = $shop_id";
    $sql .= "   AND t_ware.count_flg = 't'";
    $sql .= "   GROUP BY t_stock.goods_id";
    $sql .= "   )AS t_stock ";
    $sql .= "   ON t_order_d.goods_id = t_stock.goods_id";
    $sql .= "   LEFT JOIN";

    //発注残数
    $sql .= "   (SELECT t_stock_hand.goods_id,";
    $sql .= "   SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS order_num ";
    $sql .= "   FROM t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id";
    $sql .= "   WHERE t_stock_hand.work_div = 3 ";
    $sql .= "   AND t_stock_hand.shop_id =  $shop_id";
    $sql .= "   AND t_ware.count_flg = 't'";
    $sql .= "   AND  t_stock_hand.work_day <= (CURRENT_DATE + 7)";      
    $sql .= "   GROUP BY t_stock_hand.goods_id";
    $sql .= "   ) AS t_stock_io";
    $sql .= "   ON t_order_d.goods_id=t_stock_io.goods_id";

    $sql .= "   LEFT JOIN";

    //引当数
    $sql .= "   (SELECT t_stock_hand.goods_id,";
    $sql .= "   SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN -1 WHEN 2 THEN 1 END ) AS allowance_io_num";
    $sql .= "   FROM t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id";
    $sql .= "   WHERE t_stock_hand.work_div = 1";
    $sql .= "   AND t_stock_hand.shop_id =  $shop_id";
    $sql .= "   AND t_ware.count_flg = 't'";
    $sql .= "   AND  t_stock_hand.work_day <= (CURRENT_DATE + 7)";
    $sql .= "   GROUP BY t_stock_hand.goods_id";
    $sql .= "   ) AS t_allowance_io";
    $sql .= "   ON t_order_d.goods_id = t_allowance_io.goods_id";
    #2009-10-12 hashimoto-y
    $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

    $sql .= " WHERE";
    $sql .= "   t_order_d.ord_id = $order_id";
    $sql .= "   AND";
    $sql .= "   t_order_h.shop_id = $shop_id";
    #2009-10-12 hashimoto-y
    $sql .= "   AND ";
    $sql .= "   t_goods_info.shop_id = $shop_id ";

    $sql .= " ORDER BY t_order_d.line";
    $sql .= ";";


    $result                 = Db_Query($conn, $sql);
    $num                    = pg_num_rows($result);
    for($i = 0; $i < $num; $i++){
        $update_goods_data[]    = pg_fetch_array($result,$i,PGSQL_NUM);
    }

    if($max_row < $num){
        $max_row = $num;
    }

    for($i = 0; $i < $num; $i++){
        //仕入単価を整数部と少数部に分ける
        $price_data = explode('.', $update_goods_data[$i][10]);

        $goods_id[$i] = $update_goods_data[$i][0];

        //商品のデータをフォームにセット
        $set_update_data["hdn_goods_id"][$i]            = $update_goods_data[$i][0];        //商品ID
        $goods_id[$i]                                   = $update_goods_data[$i][0];
        $set_update_data["hdn_name_change"][$i]         = $update_goods_data[$i][1];        //品名変更
        $name_change[$i]                                = $update_goods_data[$i][1];
        $set_update_data["hdn_stock_manage"][$i]        = $update_goods_data[$i][2];        //在庫管理
        $stock_manage[$i]                               = $update_goods_data[$i][2];        //在庫管理
        $set_update_data["form_goods_cd"][$i]           = $update_goods_data[$i][3];        //商品コード
        $set_update_data["form_goods_name"][$i]         = $update_goods_data[$i][4];        //商品名
        $set_update_data["form_stock_num"][$i]          = $update_goods_data[$i][5];        //実棚数
        $set_update_data["hdn_stock_num"][$i]           = $update_goods_data[$i][5];        //実棚数（hidden用）
        $stock_num[$i]                                  = $update_goods_data[$i][5];        //実棚数
        $set_update_data["form_rorder_num"][$i]         = ($update_goods_data[$i][6] != null)? $update_goods_data[$i][6] : '0';        //発注済み数
        $set_update_data["form_rstock_num"][$i]         = ($update_goods_data[$i][7] != null)? $update_goods_data[$i][7] : '-';        //引当数
        $set_update_data["form_designated_num"][$i]     = $update_goods_data[$i][8];        //出荷可能数
        $set_update_data["form_order_num"][$i]          = $update_goods_data[$i][9];        //発注数
        $set_update_data["form_buy_price"][$i]["i"]     = $price_data[0];                    //仕入単価（整数部）
        $set_update_data["form_buy_price"][$i]["d"]     = ($price_data[1] != null)? $price_data[1] : "00";    //仕入単価（小数部）
        $set_update_data["hdn_tax_div"][$i]             = $update_goods_data[$i][11];       //課税区分
        $set_update_data["form_buy_amount"][$i]         = number_format($update_goods_data[$i][12]);       //仕入金額
        $set_update_data["form_in_num"][$i]             = $update_goods_data[$i][13];
        //aoyama-n 2009-09-07
        $set_update_data["hdn_discount_flg"][$i]        = $update_goods_data[$i][14];       //値引フラグ

        //if($update_goods_data[$i][9]%$update_goods_data[$i][13] == 0){
        //入数（$update_goods_data[$i][13]）で割れないときは処理を通らないように(kaji)
        if(($update_goods_data[$i][9]%$update_goods_data[$i][13] == 0) && ($update_goods_data[$i][13] != 0 && $update_goods_data[$i][13] != null)){
	        $set_update_data["form_order_in_num"][$i]   = $update_goods_data[$i][9]/$update_goods_data[$i][13];
        }

        //仕入金額を抽出
        $buy_amount[$i] = $update_goods_data[$i][12];
        
        //課税区分
        $tax_div[$i] = $update_goods_data[$i][11];
    }       

    #2009-12-21 aoyama-n
    $tax_rate_obj->setTaxRateDay($update_data[1]);
    $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);
        
    $total_amount_data = Total_Amount($buy_amount, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $conn);

    $set_update_data["form_buy_money"]   = $total_amount_data[0];
    $set_update_data["form_tax_money"]   = $total_amount_data[1];
    $set_update_data["form_total_money"] = $total_amount_data[2];

    $form->setConstants($set_update_data);

/****************************/
//発注警告、在庫照会からの遷移
/****************************/
}elseif($get_flg == true  && $client_id == null){
    //対象商品が本部の場合
    if($get_target_goods == '1'){
        $sql  = "SELECT";   
        $sql .= "   client_id,";
        $sql .= "   client_cd1,";
        $sql .= "   client_cname,";
        $sql .= "   coax,";
        $sql .= "   tax_franct";
        $sql .= " FROM";
        $sql .= "   t_client";
        $sql .= " WHERE";
        //$sql .= "   shop_gid = $shop_gid";
        if($_SESSION[group_kind] == "2"){
            $sql .= "   shop_id IN (".Rank_Sql().") ";
        }else{
            $sql .= "   shop_id = $_SESSION[client_id]";
        }

        $sql .= "   AND";
        $sql .= "   head_flg = 't'";
        $sql .= "   AND";
        $sql .= "   client_div = '2'";
        $sql .= ";";

        $result = Db_Query($conn, $sql);
        $client_data = pg_fetch_array($result, 0, PGSQL_NUM);

        $client_id = $client_data[0];
        $set_get_data["hdn_client_id"]       = $client_data[0];       //仕入先ID
        $set_get_data["form_client"]["cd"]   = $client_data[1];       //仕入先コード
        $set_get_data["form_client"]["name"] = $client_data[2];       //仕入先名
        $set_get_data["hdn_coax"]            = $client_data[3];       //丸め区分
        $coax                                = $client_data[3];       //丸め区分
        $set_get_data["hdn_tax_franct"]      = $client_data[4];       //端数区分
        $tax_franct                          = $client_data[4];
        $set_get_data["form_order_day"]["y"] = date("Y");
        $set_get_data["form_order_day"]["m"] = date("m");
        $set_get_data["form_order_day"]["d"] = date("d");

        $head_flg = 't';

        $warning = null;

        $client_search_flg = true;
    }

    //商品のデータを抽出
    //GETで取得した発注商品ID
    $ary_get_goods_id = implode(',',$get_goods_id); 

    $sql  = "SELECT";
    $sql .= "   t_goods.goods_id,";
    $sql .= "   t_goods.name_change,";
    #2009-10-12 hashimoto-y
    #$sql .= "   t_goods.stock_manage,";
    $sql .= "   t_goods_info.stock_manage,";

    $sql .= "   t_goods.goods_cd,";
    $sql .= "   (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS goods_name, \n";    //正式名
    #2009-10-12 hashimoto-y
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,";
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,";
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";

    $sql .= "    END AS allowance_total,";
    $sql .= "   COALESCE(t_stock.stock_num,0)";
    $sql .= "    + COALESCE(t_stock_io.order_num,0)";
    $sql .= "    - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,";
    $sql .= "   t_price.r_price,";
    $sql .= "   t_goods.tax_div,";
    //aoyama-n 2009-09-07
    #$sql .= "   t_goods.in_num";
    $sql .= "   t_goods.in_num,";
    $sql .= "   t_goods.discount_flg";
    $sql .= " FROM";
    $sql .= "   t_goods ";
    $sql .= "     INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
    $sql .= "       INNER JOIN";
    $sql .= "   t_price";
    $sql .= "   ON t_goods.goods_id = t_price.goods_id";
    $sql .= "       LEFT JOIN";

    //在庫数
    $sql .= "   (SELECT";
    $sql .= "   t_stock.goods_id,";
    $sql .= "       SUM(t_stock.stock_num)AS stock_num, ";
    $sql .= "       SUM(t_stock.rstock_num)AS rstock_num";
    $sql .= "   FROM";
    $sql .= "       t_stock INNER JOIN t_ware ON t_stock.ware_id = t_ware.ware_id";
    $sql .= "   WHERE";
    $sql .= "       t_stock.shop_id = $shop_id";
    $sql .= "       AND";
    $sql .= "       t_ware.count_flg = 't'";
    $sql .= "   GROUP BY t_stock.goods_id";
    $sql .= "   )AS t_stock";
    $sql .= "   ON t_goods.goods_id = t_stock.goods_id";

    $sql .= "       LEFT JOIN ";

    //発注数
    $sql .= "   (SELECT";
    $sql .= "       t_stock_hand.goods_id,";
    $sql .= "   SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS order_num";
    $sql .= "   FROM";
    $sql .= "       t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id";
    $sql .= "   WHERE t_stock_hand.work_div = 3 ";
    $sql .= "       AND";
    $sql .= "       t_stock_hand.shop_id =  $shop_id";
    $sql .= "       AND";
    $sql .= "       t_ware.count_flg = 't'";
    $sql .= "       AND";
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + 7)";
    $sql .= "   GROUP BY t_stock_hand.goods_id";
    $sql .= "   ) AS t_stock_io";
    $sql .= "   ON t_goods.goods_id=t_stock_io.goods_id";

    $sql .= "       LEFT JOIN ";

    //引当数
    $sql .= "   (SELECT";
    $sql .= "        t_stock_hand.goods_id,";
    $sql .= "        SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN -1 WHEN 2 THEN 1 END ) AS allowance_io_num";
    $sql .= "   FROM";
    $sql .= "        t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id";
    $sql .= "   WHERE";
    $sql .= "        t_stock_hand.work_div = 1";
    $sql .= "        AND";
    $sql .= "        t_stock_hand.shop_id = $shop_id";
    $sql .= "        AND";
    $sql .= "        t_ware.count_flg = 't'";
    $sql .= "        AND";
    $sql .= "        t_stock_hand.work_day <= (CURRENT_DATE + 7)";
    $sql .= "   GROUP BY t_stock_hand.goods_id";
    $sql .= "   ) AS t_allowance_io";
    $sql .= "   ON t_goods.goods_id = t_allowance_io.goods_id";

	//-- 2009/06/17 改修No.13 追加
	// ソート条件を同じにするため、INNER JOINの追加
	$sql .= "	INNER JOIN t_g_goods ON t_g_goods.g_goods_id = t_goods.g_goods_id \n"
		 .	"	INNER JOIN t_product ON t_product.product_id = t_goods.product_id \n";	
	//-------

    #2009-10-12 hashimoto-y
    $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

    $sql .= " WHERE";
    $sql .= "   t_goods.goods_id IN($ary_get_goods_id)";

    #2009-10-12 hashimoto-y
    $sql .= "   AND ";
    $sql .= "   t_goods_info.shop_id = $shop_id ";

    if($get_target_goods == '1'){
        $sql .= "   AND";
        $sql .= "   t_goods.public_flg = 't'";
        $sql .= "   AND";
        $sql .= "   t_price.rank_cd = '$rank_cd'";
    }elseif($get_target_goods == '2'){
        $sql .= "   AND";
        if($_SESSION[group_kind] == "2"){
            $sql .= "   t_goods.shop_id IN (".Rank_Sql().") ";
        }else{
            $sql .= "   t_goods.shop_id = $_SESSION[client_id]";
        }
        $sql .= "   AND";
        $sql .= "   t_price.rank_cd = '1'";
        $sql .= "   AND";
        if($_SESSION[group_kind] == "2"){
            $sql .= "   t_price.shop_id IN (".Rank_Sql().") ";
        }else{
            $sql .= "   t_price.shop_id = $_SESSION[client_id]";
        }

    }
	//-- 2009/06/17 改修No.13 追加
	// ソート追加(在庫照会と同じ順に)
	$sql.=	" ORDER BY \n"
		.	"	t_g_goods.g_goods_cd, \n"
		.	"	t_product.product_cd, \n"
		.	"	t_g_product.g_product_id, \n"
		.	"	t_goods.goods_cd, \n"
		.	"	t_goods.attri_div \n";
	//------

    $sql .= ";";

    $result = Db_Query($conn, $sql);

    $get_data_num = pg_num_rows($result);
    for($i = 0; $i < $get_data_num; $i++){
        $get_data[] = pg_fetch_array($result, $i, PGSQL_NUM);
    }

    if($max_row < $get_data_num){
        $max_row = $get_data_num;
    }

    for($i = 0; $i < $get_data_num; $i++){
        $price = $get_data[$i][9];

        //仕入単価を整数部と少数部に分ける
        $price_data = explode('.', $price);

        $goods_id[$i] = $get_data[$i][0];

        $set_get_data["hdn_goods_id"][$i]           = $get_data[$i][0];
        $set_get_data["hdn_name_change"][$i]        = $get_data[$i][1];
        $name_change[$i]                            = $get_data[$i][1];
        $set_get_data["hdn_stock_manage"][$i]       = $get_data[$i][2];
        $stock_manage[$i]                           = $get_data[$i][2];

        if($client_id == null){
            $set_get_data["form_goods_cd"][$i]          = $get_data[$i][3];
        }else{
            $set_get_data["form_goods_cd"][$i]      = $get_data[$i][3];
        }

        $set_get_data["form_goods_name"][$i]        = $get_data[$i][4];
        $set_get_data["form_stock_num"][$i]         = $get_data[$i][5];
        $set_get_data["hdn_stock_num"][$i]          = $get_data[$i][5];
        $stock_num[$i]                              = $get_data[$i][5];
        $set_get_data["form_rorder_num"][$i]        = $get_data[$i][6];
        $set_get_data["form_rstock_num"][$i]        = $get_data[$i][7];
        $set_get_data["form_designated_num"][$i]    = $get_data[$i][8];
        $set_get_data["form_buy_price"][$i]["i"]    = $price_data[0];
        $set_get_data["form_buy_price"][$i]["d"]    = ($price_data[1] != null)? $price_data[1] : '00';
        $set_get_data["hdn_tax_div"][$i]            = $get_data[$i][10];
        $set_get_data["form_in_num"][$i]            = $get_data[$i][11];

        //aoyama-n 2009-09-07
        $set_get_data["hdn_discount_flg"][$i]       = $get_data[$i][12];

		//-- 2009/06/17 改修おまけ 追加
		// 仕入金額のデフォルトを0でセット
		$set_get_data["form_buy_amount"][$i] = 0;
    }

    //本部フラグをhiddenにセット
    if($get_target_goods == '1'){
        $set_get_data["head_flg"] = 't';
    }else{
        $set_get_data["head_flg"] = 'f';
    }
    
    $set_get_data["form_designated_date"] = $_GET["designated_date"];

    $form->setConstants($set_get_data);
}

/****************************/
//出荷可能数入力
/****************************/
if($_POST["recomp_flg"] == true){
    //商品が入力判定
    for($i = 0; $i < $max_row; $i++){
        if($_POST["hdn_goods_id"][$i] != null){
            $goods_input_flg = true;
        }
        $ary_goods_id[] = $_POST["hdn_goods_id"][$i];
    }

    //商品が入力されている場合
    if($goods_input_flg == true){

        //出荷可能数
        $designated_date = ($_POST["form_designated_date"] != null)? $_POST["form_designated_date"] : 0;

        //数字以外が入力されている場合
        if(!ereg("^[0-9]+$", $designated_date)){
            $designated_date = 0;
        }

        //表示されている行分ループ
        for($i = 0; $i < $max_row; $i++){
            //顧客区分コードが「特殊」の場合
            //ハードコーディング
            if($furein_flg === true && $ary_goods_id[$i] != null){
                $designated_data[] = Get_Rank_Goods ($conn, $designated_date, $ary_goods_id[$i]);
            //通常
            }elseif($ary_goods_id[$i] != null){
                $sql  = "SELECT\n ";
                $sql .= "   t_goods.goods_id,\n";
                $sql .= "   t_goods.name_change,\n";
                #2009-10-12 hashimoto-y
                #$sql .= "   t_goods.stock_manage,\n";
                $sql .= "   t_goods_info.stock_manage,\n";

                $sql .= "   t_goods.goods_cd,\n";
                //aoyama-n 2009-09-07
                //商品名に商品分類が表示されない不具合
                #$sql .= "   t_goods.goods_name,\n";
                $sql .= "   (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS goods_name, \n";    //正式名
                #2009-10-12 hashimoto-y
                #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,\n";
                #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,\n";
                #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0) END AS allowance_total,\n";
                $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,\n";
                $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,\n";
                $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0) END AS allowance_total,\n";

                //aoyama-n 2009-09-07
                //スペルミス
                #$sql .= "   COALESCE(t_stock.stock_num,0) + COALESCE(t_stock_io.order_num,0) - COALESCE(t_allwance_io.allowance_io_num,0) AS stock_total\n";
                $sql .= "   COALESCE(t_stock.stock_num,0) + COALESCE(t_stock_io.order_num,0) - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total\n";
                $sql .= " FROM\n";
                $sql .= "   t_goods \n";
                $sql .= "   INNER JOIN\n"; 
                $sql .= "   t_price\n";
                $sql .= "   ON t_goods.goods_id = t_price.goods_id\n";
                //aoyama-n 2009-09-07
                //商品名に商品分類が表示されない不具合
                $sql .= "   INNER JOIN\n"; 
                $sql .= "   t_g_product\n";
                $sql .= "   ON t_goods.g_product_id = t_g_product.g_product_id\n";

                $sql .= "   LEFT JOIN\n";

                $sql .= "   (SELECT\n";
                $sql .= "   t_stock.goods_id,\n";
                $sql .= "   SUM(t_stock.stock_num)AS stock_num,\n ";
                $sql .= "   SUM(t_stock.rstock_num)AS rstock_num\n";
                $sql .= "   FROM t_stock INNER JOIN t_ware ON t_stock.ware_id = t_ware.ware_id\n";
                $sql .= "   WHERE t_stock.shop_id =  $shop_id\n";
                $sql .= "   AND t_ware.count_flg = 't'\n";
                $sql .= "   GROUP BY t_stock.goods_id\n";
                $sql .= "   )AS t_stock\n";
                $sql .= "   ON t_goods.goods_id = t_stock.goods_id\n";

                $sql .= "   LEFT JOIN \n";

                $sql .= "   (SELECT t_stock_hand.goods_id,\n";
                $sql .= "   SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS order_num\n";
                $sql .= "   FROM t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id\n";
                $sql .= "   WHERE t_stock_hand.work_div = 3 \n";
                $sql .= "   AND t_stock_hand.shop_id =  $shop_id\n";
                $sql .= "   AND t_ware.count_flg = 't'\n";
                $sql .= "   AND  t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
                $sql .= "   GROUP BY t_stock_hand.goods_id\n";
                $sql .= "   ) AS t_stock_io\n";
                $sql .= "   ON t_goods.goods_id=t_stock_io.goods_id\n";

                $sql .= "   LEFT JOIN \n";

                $sql .= "   (SELECT t_stock_hand.goods_id,\n";
                $sql .= "   SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN -1 WHEN 2 THEN 1 END ) AS allowance_io_num\n";
                $sql .= "   FROM t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id\n";
                $sql .= "   WHERE t_stock_hand.work_div = 1\n";
                $sql .= "   AND t_stock_hand.shop_id = $shop_id\n";
                $sql .= "   AND t_ware.count_flg = 't'\n";
                $sql .= "   AND  t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
                $sql .= "   GROUP BY t_stock_hand.goods_id\n";
                $sql .= "   ) AS t_allowance_io\n";
                $sql .= "   ON t_goods.goods_id = t_allowance_io.goods_id\n";
                #2009-10-12 hashimoto-y
                $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

                $sql .= " WHERE\n";
                $sql .= "   t_goods.goods_id = $ary_goods_id[$i]\n";

                #2009-10-12 hashimoto-y
                $sql .= "   AND ";
                $sql .= "   t_goods_info.shop_id = $shop_id ";

                if($head_flg == 't'){
                    $sql .= "   AND\n";
                    $sql .= "   t_goods.public_flg = 't'\n";
                    $sql .= "   AND\n";
                    $sql .= "   t_price.rank_cd = '$rank_cd'\n";
                }elseif($head_flg == 'f'){
                    $sql .= "   AND\n";
                    $sql .= "   t_goods.public_flg = 'f'\n";
                    $sql .= "   AND\n";
                    if($_SESSION[group_kind] == "2"){
                        $sql .= "   t_goods.shop_id IN (".Rank_Sql().")\n ";
                    }else{
                        $sql .= "   t_goods.shop_id = $_SESSION[client_id]\n";
                    }

                    $sql .= "   AND\n";
                    $sql .= "   t_price.rank_cd = '1'\n";
                    $sql .= "   AND\n";
                    if($_SESSION[group_kind] == "2"){
                        $sql .= "   t_price.shop_id IN (".Rank_Sql().")\n ";
                    }else{
                        $sql .= "   t_price.shop_id = $_SESSION[client_id]\n";
                    }

                }

                $sql .= ";\n";
                $result = Db_Query($conn, $sql);
                $data_num = pg_num_rows($result);
                $designated_data[] = pg_fetch_array($result, 0, PGSQL_NUM);
            }
        }

        for($i = 0; $i < count($designated_data); $i++){

            $goods_id[$i] = $designated_data[$i][0];

            //取得し直したデータをフォームにセット
            $set_designated_data["hdn_goods_id"][$i]         = $designated_data[$i][0];                 //商品ID
            $set_designated_data["hdn_name_change"][$i]      = $designated_data[$i][1];                 //品名変更
            $set_designated_data["hdn_stock_manage"][$i]     = $designated_data[$i][2];                 //在庫管理
            $stock_manage[$i]                                = $designated_data[$i][2];                 //在庫管理
            $set_designated_data["form_goods_cd"][$i]        = $designated_data[$i][3];                 //商品コード
            $set_designated_data["form_goods_name"][$i]      = $designated_data[$i][4];                 //発注数
            $set_designated_data["form_stock_num"][$i]       = $designated_data[$i][5];                 //実棚数
            $set_designated_data["hdn_stock_num"][$i]        = $designated_data[$i][5];                 //実棚数(hiddn用)
            $stock_num[$i]                                   = $designated_data[$i][5];                 //実棚数(hiddn用)
            $set_designated_data["form_rorder_num"][$i]      = $designated_data[$i][6];   //引当数
            $set_designated_data["form_rstock_num"][$i]      = $designated_data[$i][7];                 //発注済数
            $set_designated_data["form_designated_num"][$i]  = $designated_data[$i][8];                 //出荷可能数
            $set_designated_data["goods_search_row"]         = "";
        }
    }

    //出荷可能数入力フラグに空白をセット
    $set_designated_data["recomp_flg"] = "";

    $form->setConstants($set_designated_data);

/****************************/
//商品コード入力
/****************************/
}elseif($_POST["goods_search_row"] != null){

    $search_row = $_POST["goods_search_row"];

    $designated_date = ($_POST["form_designated_date"] != null)? $_POST["form_designated_date"] : 0;
    if(!ereg("^[0-9]+$", $designated_date)){
        $designated_date = 0;
    }


    //顧客区分が特殊の場合
    //ハードコーディング
    if($furein_flg === true){
        $goods_data = Get_Rank_Goods ($conn, $designated_date, null, $_POST["form_goods_cd"][$search_row]);

        //該当レコードなし
        if($goods_data === false){
            $data_num = 0;
        }else{
            $data_num = count($goods_data);   
        }

    //通常
    }else{

        $sql  = "SELECT\n";
        $sql .= "   t_goods.goods_id,\n";
        $sql .= "   t_goods.name_change,\n";
        #2009-10-12 hashimoto-y
        #$sql .= "   t_goods.stock_manage,\n";
        $sql .= "   t_goods_info.stock_manage,\n";

        $sql .= "   t_goods.goods_cd,\n";
		$sql .= "   (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS goods_name, \n";  //商品名（正式）
        #2009-10-12 hashimoto-y
        #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,\n";
        #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,\n";
        #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0) END AS allowance_total,\n";
        $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS rack_num,\n";
        $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock_io.order_num,0) END AS on_order_num,\n";
        $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0) END AS allowance_total,\n";

        $sql .= "   COALESCE(t_stock.stock_num,0) + COALESCE(t_stock_io.order_num,0) - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,\n";
        $sql .= "   t_price.r_price,\n";
        $sql .= "   t_goods.tax_div,\n";
        //aoyama-n 2009-09-07
        #$sql .= "   t_goods.in_num\n";
        $sql .= "   t_goods.in_num,\n";
        $sql .= "   t_goods.discount_flg\n";
        $sql .= " FROM\n";

        $sql .= "   t_goods INNER JOIN  t_price ON t_goods.goods_id = t_price.goods_id\n";
	    $sql .= "       INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
        $sql .= "       LEFT JOIN\n";

        //在庫数
        $sql .= "   (SELECT\n";
        $sql .= "       t_stock.goods_id,\n";
        $sql .= "       SUM(t_stock.stock_num)AS stock_num,\n";
        $sql .= "       SUM(t_stock.rstock_num)AS rstock_num\n";
        $sql .= "       FROM\n";
        $sql .= "            t_stock INNER JOIN t_ware ON t_stock.ware_id = t_ware.ware_id\n";
        $sql .= "       WHERE\n";
        $sql .= "            t_stock.shop_id =  $shop_id\n";
        $sql .= "            AND\n";
        $sql .= "            t_ware.count_flg = 't'\n";
        $sql .= "       GROUP BY t_stock.goods_id\n";
        $sql .= "   )AS t_stock ON t_goods.goods_id = t_stock.goods_id\n";

        $sql .= "       LEFT JOIN\n";

        //発注済数
        $sql .= "   (SELECT\n";
        $sql .= "       t_stock_hand.goods_id,\n";
        $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN 1 WHEN 2 THEN -1 END ) AS order_num\n";
        $sql .= "   FROM\n";
        $sql .= "       t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id\n";
        $sql .= "   WHERE\n";
        $sql .= "       t_stock_hand.work_div = 3\n";
        $sql .= "       AND\n";
        $sql .= "       t_stock_hand.shop_id = $shop_id\n";
        $sql .= "       AND\n";
        $sql .= "       t_ware.count_flg = 't'\n";
        $sql .= "       AND\n";
        $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
        $sql .= "   GROUP BY t_stock_hand.goods_id\n";
        $sql .= "   ) AS t_stock_io ON t_goods.goods_id=t_stock_io.goods_id\n";

        $sql .= "       LEFT JOIN\n";

        //引当数
        $sql .= "   (SELECT\n";
        $sql .= "       t_stock_hand.goods_id,\n";
        $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div WHEN 1 THEN -1 WHEN 2 THEN 1 END ) AS allowance_io_num\n";
        $sql .= "   FROM\n";
        $sql .= "       t_stock_hand INNER JOIN t_ware ON t_stock_hand.ware_id = t_ware.ware_id\n";
        $sql .= "   WHERE\n";
        $sql .= "       t_stock_hand.work_div = 1\n";
        $sql .= "       AND\n";
        $sql .= "       t_stock_hand.shop_id = $shop_id\n";
        $sql .= "       AND\n";
        $sql .= "       t_ware.count_flg = 't'\n";
        $sql .= "       AND\n";
        $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
        $sql .= "   GROUP BY t_stock_hand.goods_id\n";
        $sql .= "   ) AS t_allowance_io ON t_goods.goods_id = t_allowance_io.goods_id\n";

        #2009-10-12 hashimoto-y
        $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

        $sql .= " WHERE \n";
        $sql .= "       t_goods.goods_cd = '".$_POST["form_goods_cd"][$search_row]."'\n";
        $sql .= "       AND\n";
        $sql .= "       t_goods.accept_flg = '1'\n";
        $sql .= "       AND\n";

        #2009-10-12 hashimoto-y
        $sql .= "       t_goods_info.shop_id = $shop_id ";
        $sql .= "       AND ";

        $sql .= ($group_kind == '2') ? " t_goods.state IN (1,3)\n" : " t_goods.state = '1'\n";

 
        //仕入先に本部が指定されている場合
        if($head_flg == 't'){
            $sql .= "       AND \n";
            $sql .= "       t_goods.public_flg = 't' \n";
            $sql .= "       AND \n";
            $sql .= "       t_price.rank_cd = '$rank_cd'\n";
        //仕入先に本部以外が指定されている場合
        }elseif($head_flg ==  'f'){
            $sql .= "       AND \n";
            $sql .= "       t_goods.public_flg = 'f' \n";
            $sql .= "       AND \n";
            if($_SESSION[group_kind] == "2"){
                $sql .= "     t_goods.shop_id IN (".Rank_Sql().") \n";
            }else{
                $sql .= "     t_goods.shop_id = $_SESSION[client_id]\n";
            }

            $sql .= "       AND\n";
            $sql .= "       t_price.rank_cd = '1'\n";
            $sql .= "       AND \n";
            if($_SESSION[group_kind] == "2"){
                $sql .= "     t_price.shop_id IN (".Rank_Sql().") \n";
            }else{
                $sql .= "     t_price.shop_id = $_SESSION[client_id]\n";
            }
        }

        $sql .= ";\n";            

        $result = Db_Query($conn, $sql);

        $data_num = pg_num_rows($result);
        $goods_data = pg_fetch_array($result);
    }

    if($data_num > 0){

        $price = $goods_data[9];

        //仕入単価を整数部と少数部に分ける
        $price_data = explode('.', $price);

        //仕入金額（税抜）を算出
        $buy_amount= null;
        if($_POST["form_order_num"][$search_row] != null){
            //仕入金額（税抜き）
            $buy_amount = bcmul($price, $_POST["form_order_num"][$search_row],2);
            $buy_amount = Coax_Col($coax, $buy_amount);
        }
        //品名変更
        $name_change[$search_row] = $goods_data[1];

        $goods_id[$search_row] = $goods_data[0];

        //商品データ
        $set_goods_data["hdn_goods_id"][$search_row]         = $goods_data[0];              //商品ID
        $set_goods_data["hdn_name_change"][$search_row]      = $goods_data[1];              //品名変更
        $set_goods_data["hdn_stock_manage"][$search_row]     = $goods_data[2];              //在庫管理
        $stock_manage[$search_row]                           = $goods_data[2];              //在庫管理
        $set_goods_data["form_goods_cd"][$search_row]        = $goods_data[3];              //商品コード
        $set_goods_data["form_goods_name"][$search_row]      = $goods_data[4];              //発注数
        $set_goods_data["form_stock_num"][$search_row]       = $goods_data[5];              //実棚数
        $set_goods_data["hdn_stock_num"][$search_row]        = $goods_data[5];              //実棚数(hiddn用)
        $stock_num[$search_row]                              = $goods_data[5];              //実棚数
        $set_goods_data["form_rorder_num"][$search_row]      = ($goods_data[6] != null)? $goods_data[6] : '0';              //引当数
        $set_goods_data["form_rstock_num"][$search_row]      = ($goods_data[7] != null)? $goods_data[7] : '-';              //発注済数
        $set_goods_data["form_designated_num"][$search_row]  = $goods_data[8];              //出荷可能数
        $set_goods_data["form_buy_price"][$search_row]["i"]  = $price_data[0];              //仕入単価（整数部）
        $set_goods_data["form_buy_price"][$search_row]["d"]  = ($price_data[1] != null)? $price_data[1] : '00';             //仕入単価（小数部）
        $set_goods_data["hdn_tax_div"][$search_row]          = $goods_data[10];             //課税区分
        $set_goods_data["form_buy_amount"][$search_row]      = number_format($buy_amount);    //仕入金額（税抜き）
        $set_goods_data["goods_search_row"]                  = "";
        $set_goods_data["hdn_order_id"]                      = $_POST["hdn_order_id"];
        $set_goods_data["form_in_num"][$search_row]          = $goods_data["in_num"];
        //aoyama-n 2009-09-07
        $set_goods_data["hdn_discount_flg"][$search_row]     = $goods_data["discount_flg"]; //値引フラグ  

    //商品コードに不正な値を入力された場合
    }else{
        //商品データ
        $set_goods_data["hdn_goods_id"][$search_row]         = "";              //商品ID
        $set_goods_data["hdn_name_change"][$search_row]      = "";              //品名変更
        $set_goods_data["hdn_stock_manage"][$search_row]     = "";              //在庫管理
        $set_goods_data["form_goods_name"][$search_row]      = "";              //発注数
        $set_goods_data["form_stock_num"][$search_row]       = "";              //実棚数
        $set_goods_data["hdn_stock_num"][$search_row]        = "";              //実棚数(hiddn用)
        $set_goods_data["form_rstock_num"][$search_row]      = "";              //発注済数
        $set_goods_data["form_rorder_num"][$search_row]      = "";              //引当数
        $set_goods_data["form_designated_num"][$search_row]  = "";              //出荷可能数
        $set_goods_data["form_buy_price"][$search_row]["i"]  = "";              //仕入単価（整数部）
        $set_goods_data["form_buy_price"][$search_row]["d"]  = "";              //仕入単価（小数部）
        $set_goods_data["hdn_tax_div"][$search_row]          = "";              //課税区分
        $set_goods_data["form_order_in_num"][$search_row]    = "";              //発注数
        $set_goods_data["form_buy_amount"][$search_row]      = "";              //仕入金額（税抜き）
        $set_goods_data["goods_search_row"]                  = "";
        $set_goods_data["form_in_num"][$search_row]          = "";
        //aoyama-n 2009-09-07
        $set_goods_data["hdn_discount_flg"][$search_row]     = "";              //値引フラグ
        $stock_num[$search_row]                              = null;
        $goods_id[$search_row]                               = null;
        $name_change[$search_row]                            = null;
        $stock_manage[$search_row]                           = null;
        
    }
    $set_goods_data["conf_button_flg"] = ""; 
    $form->setConstants($set_goods_data);

/****************************/
//仕入先コード入力処理
/****************************/
}elseif($_POST["client_search_flg"] == true){

    //POST取得
    $client_cd = $_POST["form_client"]["cd"];       //得意先コード

    //仕入先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id,";
    $sql .= "   client_cname,";
    $sql .= "   coax,";
    $sql .= "   tax_franct,";
    $sql .= "   head_flg,";
    $sql .= "   trade_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_cd1 = '$client_cd'";
    $sql .= "   AND";
    $sql .= "   client_div = '2'";
    $sql .= "   AND";
    //$sql .= "   shop_gid = '$shop_gid'";
    if($_SESSION[group_kind] == "2"){
        $sql .= "   shop_id IN (".Rank_Sql().") ";
    }else{
        $sql .= "   shop_id = $_SESSION[client_id]";
    }

    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $num = pg_num_rows($result);

    if($num != 0){

        $client_id      = pg_fetch_result($result, 0,0);
        $client_name    = pg_fetch_result($result, 0,1);
        $coax           = pg_fetch_result($result, 0,2);        //丸め区分（商品）
        $tax_franct     = pg_fetch_result($result, 0,3);        //端数区分（消費税）
        $head_flg       = pg_fetch_result($result, 0,4);        //本部フラグ
        $trade_id       = pg_fetch_result($result, 0,5);        //取引区分

        //取得したデータをhiddenセット
        $client_data["client_search_flg"]   = "";
        $client_data["hdn_client_id"]       = $client_id;
        $client_data["form_client"]["name"] = $client_name;
        $client_data["head_flg"]            = $head_flg;
        $client_data["hdn_coax"]            = $coax;
        $client_data["hdn_tax_franct"]      = $tax_franct;
        $client_data["form_trade"]          = $trade_id;

        //本部フラグがの値が「ｔ」⇒「ｆ」または「ｆ」⇒「ｔ」になった場合、入力されていた商品情報は全てクリア
        if($head_flg != $_POST["head_flg"]){
	        //在庫管理を初期化
            $stock_manage = null;

            //品名変更を初期化
            $name_change = null;

	        //商品情報は全てクリア
            for($i = 0; $i < $max_row; $i++){
                $client_data["hdn_goods_id"][$i]         = "";
                $client_data["form_goods_cd"][$i]        = "";
                $client_data["form_goods_name"][$i]      = "";
                $client_data["hdn_name_change"][$i]      = "";
                $client_data["form_rorder_num"][$i]      = "";
                $client_data["form_rstock_num"][$i]      = "";
                $client_data["hdn_stock_num"][$i]        = "";
                $client_data["hdn_stock_manage"][$i]     = "";
                $client_data["form_stock_num"][$i]       = "";
                $client_data["form_designated_num"][$i]  = "";
                $client_data["form_order_num"][$i]       = "";
                $client_data["form_buy_price"][$i]["i"]  = "";
                $client_data["form_buy_price"][$i]["d"]  = "";
                $client_data["hdn_tax_div"][$i]          = "";
                $client_data["form_buy_amount"][$i]      = "";
                $client_data["form_in_num"][$i]          = "";        
                $client_data["form_order_in_num"][$i]    = "";        
                //aoyama-n 2009-09-07
                $client_data["hdn_discount_flg"][$i]     = "";        

                $stock_num[$i]                           = null;
                $goods_id[$i]                            = null;
                $name_change[$i]                         = null;
                $stock_manage[$i]                        = null;

            	$max_row = 5;
	            $client_data["max_row"]            = 5;
            }
            
        }

        //メッセージ出力
        if($head_flg == 't'){
            $message = "本部商品のみ入力可能です。";

        }elseif($furein_flg === true){
            $message = "商品が入力可能です。";
        }else{
            $message = "本部商品以外が入力可能です。";
        }
        $client_search_flg = true;

        $warning = null;
    }else{
        $warning = "仕入先を指定してください。";
        //在庫管理を初期化
        $stock_manage = null;
        $name_change  = null;

        $client_data["client_search_flg"]   = "";
        $client_data["hdn_client_id"]       = "";
        $client_data["form_client"]["name"] = "";
        $client_data["head_flg"]            = "";
        $client_data["hdn_ord_id"]          = $_POST["hdn_ord_id"];

        $client_search_flg = "";
    }

	$client_data["del_row"]            = "";
	$client_data["max_row"]            = 5;
	$client_data["form_buy_money"]     = "";
    $client_data["form_tax_money"]     = "";
    $client_data["form_total_money"]   = "";
	//削除行数
    unset($del_history);
    $del_history[] = NULL;
	$del_row = NULL;

    $client_data["conf_button_flg"] = "";
    $form->setConstants($client_data);
}
//***************************/
//最大行数をhiddenにセット
/****************************/
$max_row_data["max_row"] = $max_row;

$form->setConstants($max_row_data);

/****************************/
//フォーム作成
/****************************/
//発信日
$form->addElement(
        "text","form_send_date","",
        "size=\"25\" maxLength=\"18\" 
        style=\"color : #000000; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//出荷可能数
$form->addElement(
    "text","form_designated_date","",
    "size=\"4\" maxLength=\"4\" 
    $g_form_option 
    style=\"text-align: right; $g_form_style \"
    onChange=\"javascript:Button_Submit('recomp_flg','#','true')\"
    "
);

//発注番号
$form->addElement(
    "text","form_order_no","",
    "style=\"color : #000000; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);

//発注日
if($head_flg == 't'){
    //リードオンリー外しています。(後で戻す)
    $form_order_day[] = $form->createElement(
        "text","y","",
        "size=\"4\" maxLength=\"4\"
        style=\"$g_form_style \"
        onkeyup=\"changeText(this.form,'form_order_day[y]','form_order_day[m]',4)\" 
        onFocus=\"onForm_today(this,this.form,'form_order_day[y]','form_order_day[m]','form_order_day[d]')\"
        onBlur=\"blurForm(this)\""
    );
    $form_order_day[] = $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\" 
        style=\"$g_form_style \"
        onkeyup=\"changeText(this.form, 'form_order_day[m]','form_order_day[d]',2)\" 
        onFocus=\"onForm_today(this,this.form,'form_order_day[y]','form_order_day[m]','form_order_day[d]')\"
        onBlur=\"blurForm(this)\""
    );
    $form_order_day[] = $form->createElement(
        "text","d","",
        "size=\"2\" maxLength=\"2\"
        style=\"$g_form_style \" 
        onFocus=\"onForm_today(this,this.form,'form_order_day[y]','form_order_day[m]','form_order_day[d]')\"
        onBlur=\"blurForm(this)\""
    );
    $form->addGroup( $form_order_day,"form_order_day","","-");
}else{
    $form_order_day[] = $form->createElement(
        "text","y","",
        "size=\"4\" maxLength=\"4\"
        style=\"$g_form_style \"
        onkeyup=\"changeText(this.form,'form_order_day[y]','form_order_day[m]',4)\" 
        onFocus=\"onForm_today(this,this.form,'form_order_day[y]','form_order_day[m]','form_order_day[d]')\"
        onBlur=\"blurForm(this)\""
    );
    $form_order_day[] = $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\" 
        style=\"$g_form_style \"
        onkeyup=\"changeText(this.form, 'form_order_day[m]','form_order_day[d]',2)\" 
        onFocus=\"onForm_today(this,this.form,'form_order_day[y]','form_order_day[m]','form_order_day[d]')\"
        onBlur=\"blurForm(this)\""
    );
    $form_order_day[] = $form->createElement(
        "text","d","",
        "size=\"2\" maxLength=\"2\"
        style=\"$g_form_style \" 
        onFocus=\"onForm_today(this,this.form,'form_order_day[y]','form_order_day[m]','form_order_day[d]')\"
        onBlur=\"blurForm(this)\""
    );
    $form->addGroup( $form_order_day,"form_order_day","","-");
}

//希望納期
$form_hope_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\"
    style=\"$g_form_style \" 
    onkeyup=\"changeText(this.form,'form_hope_day[y]','form_hope_day[m]',4)\" 
    onFocus=\"onForm_today(this,this.form,'form_hope_day[y]','form_hope_day[m]','form_hope_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form_hope_day[] = $form->createElement(
    "text","m","",
    "size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style \" 
    onkeyup=\"changeText(this.form,'form_hope_day[m]','form_hope_day[d]',2)\" 
    onFocus=\"onForm_today(this,this.form,'form_hope_day[y]','form_hope_day[m]','form_hope_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form_hope_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style \" 
    onFocus=\"onForm_today(this,this.form,'form_hope_day[y]','form_hope_day[m]','form_hope_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form->addGroup( $form_hope_day,"form_hope_day","","-");

//グリーン指定業者
if($head_flg == 't'){
    $form->addElement('checkbox', 'form_trans', 'グリーン指定', '<b>グリーン指定</b>　');
}elseif($head_flg == 'f'){
    $select_value = Select_Get($conn,"trans");
    $form->addElement("select", "form_trans",'', $select_value, $g_form_option_select);
}

//仕入先
$form_client[] = $form->createElement(
    "text","cd","",
    "size=\"7\" maxLength=\"6\" 
    style=\"$g_form_style \" 
    onChange=\"javascript:Button_Submit('client_search_flg','#','true')\" 
    $g_form_option"
);
$form_client[] = $form->createElement(
    "text","name","",
    "size=\"34\" $g_text_readonly");
$form->addGroup( $form_client, "form_client", "");

//直送先
$select_value = Select_Get($conn,'direct');
$form->addElement('select', 'form_direct', "", $select_value,"class=\"Tohaba\"".$g_form_option_select);

//仕入倉庫
/*
$where  = " WHERE";
$where .= "  shop_id = $shop_id";
$where .= "  AND";
$where .= "  nondisp_flg = 'f'";
$select_value = Select_Get($conn,'ware', $where);
*/
$select_value = Select_Get($conn,'ware',"WHERE shop_id = $_SESSION[client_id] AND staff_ware_flg = 'f' AND nondisp_flg = 'f' ");
$form->addElement('select', 'form_ware', '', $select_value,$g_form_option_select);

//取引区分
$select_value = Select_Get($conn,'trade_ord');
$form->addElement('select', 'form_trade', '', $select_value,$g_form_option_select);

//担当者
$select_value = Select_Get($conn,'staff',null,true);
$form->addElement('select', 'form_staff', '', $select_value,$g_form_option_select);

//通信欄（仕入先宛）
$form->addElement("textarea","form_note_your",""," rows=\"2\" cols=\"75\" $g_form_option_area");


//仕入金額(合計)
$form->addElement(
        "text","form_buy_money","",
        "size=\"25\" maxLength=\"18\" 
        style=\"color : #000000; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//消費税額(合計)
$form->addElement(
        "text","form_tax_money","",
        "size=\"25\" maxLength=\"18\" 
        style=\"color : #000000; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//仕入金額（税込合計)
$form->addElement(
        "text","form_total_money","",
        "size=\"25\" maxLength=\"18\" 
        style=\"color : #000000; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//hidden
$form->addElement("hidden", "del_row");             //削除行
$form->addElement("hidden", "add_row_flg");         //追加行フラグ
$form->addElement("hidden", "max_row");             //最大行数
$form->addElement("hidden", "client_search_flg");   //仕入先コード入力フラグ
$form->addElement("hidden", "head_flg");            //本部フラグ
$form->addElement("hidden", "hdn_client_id");       //得意先ID
$form->addElement("hidden", "goods_search_row");     //商品コード入力行
$form->addElement("hidden", "recomp_flg");          //出荷可能数フラグ
$form->addElement("hidden", "hdn_coax");            //丸め区分
$form->addElement("hidden", "hdn_tax_franct");      //端数区分
$form->addElement("hidden", "hdn_order_id");        //確認ボタン押下フラグ
$form->addElement("hidden", "sum_button_flg");      //合計ボタン押下フラグ
$form->addElement("hidden", "hdn_design");          //出荷可能日数
$form->addElement("hidden", "hdn_target");          //対象商品
$form->addElement("hidden", "goods_cd_form");       //商品コード
$form->addElement("hidden", "hdn_ord_enter_day");   //商品コード
$form->addElement("hidden", "hdn_first_set", "1");  //初期表示フラグ

//発注残一覧
$form->addElement("button","ord_button","発注残一覧","onClick=\"javascript:Referer('2-3-106.php')\"");
//入力・変更
$form->addElement("button","new_button","入　力",$g_button_color."onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
//照会
$form->addElement("button","change_button","照会・変更","onClick=\"javascript:Referer('2-3-104.php')\"");


#2009-09-15 hashimoto-y
for($i = 0; $i < $max_row; $i++){
    if(!in_array("$i", $del_history)){
        $form->addElement("hidden","hdn_discount_flg[$i]");
    }
}


/****************************/
//合計ボタン押下処理
/****************************/
if(($_POST["sum_button_flg"] == true || $_POST["del_row"] != "" || $_POST["form_order_button"] == "発注確認画面へ") && $_POST["client_search_flg"] == false){
    //削除リストを取得
    $del_row = $_POST["del_row"];
    //削除履歴を配列にする。
    $del_history = explode(",", $del_row);

    $buy_data   = $_POST["form_buy_amount"];  //仕入金額
    $price_data = NULL;                        //商品の仕入金額
    $tax_div    = NULL;                        //課税区分

    //仕入金額の合計値計算
    for($i=0;$i<$max_row;$i++){
        if($buy_data[$i] != "" && !in_array("$i", $del_history)){
            $price_data[] = $buy_data[$i];
            $tax_div[]    = $_POST["hdn_tax_div"][$i];
        }
    }

    #2009-12-21 aoyama-n
    $tax_rate_obj->setTaxRateDay($_POST["form_order_day"]["y"]."-".$_POST["form_order_day"]["m"]."-".$_POST["form_order_day"]["d"]);
    $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

    $data = Total_Amount($price_data, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $conn);

    if($_POST["sum_button_flg"] == true){
    $height = $max_row * 100;
    }

    //フォームに値セット
    $money_data["form_buy_money"]   = number_format($data[0]);
    $money_data["form_tax_money"]   = number_format($data[1]);
    $money_data["form_total_money"] = number_format($data[2]);
    $money_data["sum_button_flg"]   = "";
    $form->setConstants($money_data);
}

/****************************/
//ボタン押下処理
/****************************/
if($_POST["form_order_button"] == "発注確認画面へ" || $_POST["comp_button"] == "オンライン発注OK" || $_POST["comp_button"] == "オフライン発注OK" || $_POST["order_button"] == "オンライン発注OKと発注書出力" || $_POST["order_button"] == "オフライン発注OKと発注書出力"){

    /******************************/
    //ルール作成
    /******************************/
    //仕入先
    //必須チェック
    $form->addGroupRule('form_client', array(
            'cd' => array(
                    array('正しい仕入先コードを入力してください。', 'required')
            ),      
            'name' => array(
                    array('正しい仕入先コードを入力してください。','required')
            )       
    ));

    //出荷可能数
    $form->addRule("form_designated_date","発注済数と引当数を考慮する日数は半角数値のみです。","regex", '/^[0-9]+$/');

    //発注日
    //本部以外
    if($head_flg != 't'){
        //●必須チェック
        $form->addGroupRule('form_order_day', array(
            'y' => array(
                    array('正しい発注日を入力してください。', 'required'),
                    array('正しい発注日を入力してください。', 'numeric')
            ),      
            'm' => array(
                    array('正しい発注日を入力してください。','required'),
                    array('正しい発注日を入力してください。', 'numeric')
            ),       
            'd' => array(
                    array('正しい発注日を入力してください。','required'),
                    array('正しい発注日を入力してください。', 'numeric')
            )       
        ));
    }

    //希望納期
    //●半角チェック
    $form->addGroupRule('form_hope_day', array(
            'y' => array(
                    array('正しい希望納期を入力してください。', 'numeric')
            ),      
            'm' => array(
                    array('正しい希望納期を入力してください。','numeric')
            ),       
            'd' => array(
                    array('正しい希望納期を入力してください。','numeric')
            )
    ));

    //仕入倉庫
    //●必須チェック
    $form->addRule("form_ware","仕入倉庫を選択してください。","required");

    //取引区分
    //●必須チェック
    $form->addRule("form_trade","取引区分を選択してください。","required");

    //担当者
    //●必須チェック
    $form->addRule("form_staff","担当者を選択してください。","required");


    //通信欄（仕入先宛）
    //●文字数チェック
    $form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
    $form->addRule("form_note_your","通信欄（仕入先宛）は95文字以内です。","mb_maxlength","95");

    /*****************************/
    //POST取得
    /*****************************/
    $designated_date    = $_POST["form_designated_date"];                                   //出荷可能数
    $order_no           = $_POST["form_order_no"];                                          //発注番号
    $order_day["y"]     = $_POST["form_order_day"]["y"];                                    //発注日（年）
    $order_day["m"]     = $_POST["form_order_day"]["m"];                                    //発注日（月）
    $order_day["d"]     = $_POST["form_order_day"]["d"];                                    //発注日（日）

    $hope_day["y"]      = $_POST["form_hope_day"]["y"];                                     //希望納期（年）
    $hope_day["m"]      = $_POST["form_hope_day"]["m"];                                     //希望納期（月）
    $hope_day["d"]      = $_POST["form_hope_day"]["d"];                                     //希望納期（日）
    $arrival_day["y"]   = $_POST["form_arrival_day"]["y"];                                  //入荷予定日（年）
    $arrival_day["m"]   = $_POST["form_arrival_day"]["m"];                                  //入荷予定日（月）
    $arrival_day["d"]   = $_POST["form_arrival_day"]["d"];                                  //入荷予定日（日）

    if($head_flg == 't'){
        $trans_flg      = ($_POST["form_trans"] != null)? 't' : 'f';                        //グリーン指定業者
        $trans          = null;
    }elseif($head_flg == 'f'){
        $trans          = ($_POST["form_trans"] != null)? $_POST["form_trans"] : null;    //運送業者
        if($trans != null){
            $sql  = "SELECT";
            $sql .= "   green_trans";
            $sql .= " FROM";
            $sql .= "   t_trans";
            $sql .= " WHERE";
            $sql .= "   trans_id = $trans";
            $sql .= ";";

            $result = Db_Query($conn, $sql);
            $trans_flg = pg_fetch_result($result ,0,0);
        }else{
            $trans_flg = 'f';
        }
    }

    $client_id          = $_POST["hdn_client_id"];                                          //仕入先
    $direct             = $_POST["form_direct"];  //直送先
    $staff              = $_POST["form_staff"];                                             //担当者    
    $ware               = $_POST["form_ware"];                                              //倉庫
    $trade              = $_POST["form_trade"];                                             //取引区分
    $note_your          = $_POST["form_note_your"];                                           //通信欄（仕入先宛）
    $client_cd          = $_POST["form_client"]["cd"];

    //仕入先チェック
    $sql  = "SELECT";
    $sql .= "   COUNT(client_id) ";
    $sql .= "FROM";
    $sql .= "   t_client ";
    $sql .= "WHERE";
    $sql .= "   client_id = $client_id";
    $sql .= "   AND";
    $sql .= "   client_cd1 = '$client_cd'";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $client_num = pg_fetch_result($result, 0, 0);

    if($client_num != 1){
        $form->setElementError("form_client", "仕入先情報取得前に 発注確認画面へボタン <br>が押されました。操作をやり直してください。");
    }elseif($client_cd != null){ 

        //削除リストを取得
        $del_row = $_POST["del_row"];
        //削除履歴を配列にする。
        $del_history = explode(",", $del_row);

        //値をチェックする関数
        $check_data = Row_Data_Check(
                                 $_POST[hdn_goods_id],                          //商品ID
                                 $_POST[form_goods_cd],                         //商品コード
                                 $_POST[form_goods_name],                       //商品名
                                 $_POST[form_order_num],                        //発注数
                                 $_POST[form_buy_price],                        //仕入単価
                                 str_replace(',','',$_POST[form_buy_amount]),   //仕入金額
                                 $_POST[hdn_tax_div],                           //課税区分
                                 $del_history,                                  //行削除履歴
                                 $max_row,                                      //最大行数
                                 'ord',                                          //区分
                                 //aoyama-n 2009-09-07
                                 #$conn
                                 $conn,
                                 null,
                                 null,
                                 null,
                                 null,
                                 $_POST[hdn_discount_flg]                       //値引フラグ
                                  );

        //変数初期化
        $goods_id   = null;
        $goods_cd   = null;
        $goods_name = null;
        $order_num  = null;
        $buy_price  = null;
        $buy_amount = null;
        $tax_div    = null;

        //エラーがあった場合
        if($check_data[0] === true){
            //商品が一つも選択されていない場合
            $form->setElementError("form_buy_money",$check_data[1]);
            //正しい商品コードが入力されていない場合
            $goods_err = $check_data[2];
            //発注数と仕入単価に入力があるか
            $price_num_err = $check_data[3];
            //発注数半角数字チェック
            $num_err = $check_data[4];
            //単価半角チェック
            $price_err = $check_data[5];

            $err_flg = true;

        //エラーがなかった場合
        }else{

            //登録対象データを変数にセット
            $goods_id   = $check_data[1][goods_id];
            $goods_cd   = $check_data[1][goods_cd];
            $goods_name = $check_data[1][goods_name];
            $order_num  = $check_data[1][num];
            $buy_price  = $check_data[1][price];
            $buy_amount = $check_data[1][amount];
            $tax_div    = $check_data[1][tax_div];
            $def_line   = $check_data[1][def_line];
        }
    }

    /******************************/
    //PHPでチェック
    /******************************/

    if($head_flg != 't'){
        //発注日日付妥当性チェック
        if(!checkdate((int)$order_day["m"], (int)$order_day["d"], (int)$order_day["y"])){
            $form->setElementError("form_order_day", "発注日の日付は妥当ではありません。");
        }else{
            //システム開始日チェック
            //発注日
            $order_day_err   = Sys_Start_Date_Chk($order_day["y"], $order_day["m"], $order_day["d"], "発注日");
            if($order_day_err != Null){
                $form->setElementError("form_order_day", $order_day_err);
            }
        }
    }

    //希望納期チェック
    if($hope_day["m"] != null || $hope_day["d"] != null || $hope_day["y"] != null){
        $hope_day_input_flg = true;
    }
    if(!checkdate((int)$hope_day["m"], (int)$hope_day["d"], (int)$hope_day["y"]) && $hope_day_input_flg == true){
        $form->setElementError("form_hope_day", "希望納期の日付は妥当ではありません。");
    }else{
        //希望納期
        $hope_day_err    = Sys_Start_Date_Chk($hope_day["y"], $hope_day["m"], $hope_day["d"], "希望納期");
        if($hope_day_err != null){
            $form->setElementError("form_hope_day", $hope_day_err);
        }
    }

    //入荷予定日チェック
    if($arrival_day["m"] != null || $arrival_day["d"] != null || $arrival_day["y"] != null){
        $arrival_day_input_flg = true;
    }

    if(!checkdate((int)$arrival_day["m"], (int)$arrival_day["d"], (int)$arrival_day["y"]) && $arrival_day_input_flg == true){
        $form->setElementError("form_arrival_day", "入荷予定日の日付は妥当ではありません。");
    }else{
        //入荷予定日
        $arrival_day_err = Sys_Start_Date_Chk($arrival_day["y"], $arrival_day["m"], $arrival_day["d"], "入荷予定日");
        if($arrival_day_err != null){
            $form->setElementError("form_arrival_day", $arrival_day_err);
        }
    }

/*
    //商品チェック
    //商品重複チェック
    for($i = 0; $i < count($goods_id); $i++){
        for($j = 0; $j < count($goods_id); $j++){
            if($goods_id[$i] != null && $goods_id[$j] != null && $i != $j && $goods_id[$i] == $goods_id[$j]){
				$goods_twice = "同じ商品が複数選択されています。";
            }
        }
    }
*/
    //商品チェック
    //商品重複チェック
    $goods_count = count($goods_id);
    for($i = 0; $i < $goods_count; $i++){

        //既にチェック済みの商品の場合はｽｷｯﾌﾟ
        if(@in_array($goods_id[$i], $checked_goods_id)){
            continue;
        }

        //チェック対象となる商品
        $err_goods_cd = $goods_cd[$i];
        $mst_line = $def_line[$i];

        for($j = $i+1; $j < $goods_count; $j++){
            //商品が同じ場合
            if($goods_id[$i] == $goods_id[$j]) {
                $duplicate_line .= ", ".($def_line[$j]);
            }
        }
        $checked_goods_id[] = $goods_id[$i];    //チェック済み商品

        if($duplicate_line != null){
            $duplicate_goods_err[] =  "商品コード：".$err_goods_cd."の商品が複数選択されています。(".$mst_line.$duplicate_line."行目)";
        }

        $err_goods_cd   = null;
        $mst_line       = null;
        $duplicate_line = null;
    }

    /**************************/
    //値検証
    /**************************/    
    if($err_flg != true &&  $form->validate()){

        /***************************/
        //登録ボタン押下処理
        /***************************/
        if($_POST["comp_button"] == "オンライン発注OK" || $_POST["comp_button"] == "オフライン発注OK" || $_POST["order_button"] == "オンライン発注OKと発注書出力" || $_POST["order_button"] == "オフライン発注OKと発注書出力"){
            //発注ヘッダに登録

            //日付生成
            //入荷予定日
            if($arrival_day["y"] != null){
                $arrival_date = $arrival_day['y']."-".$arrival_day['m']."-".$arrival_day['d'];
            }

            //希望納期
            if($hope_day["y"] != null){
                $hope_date = $hope_day['y']."-".$hope_day['m']."-".$hope_day['d'];        
            }

            //発注日
            $order_date = $order_day['y']."-".$order_day['m']."-".$order_day['d'];

            #2009-12-21 aoyama-n
            $tax_rate_obj->setTaxRateDay($order_date);
            $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

            //税抜合計、消費税金額を算出
            for($i = 0; $i < count($goods_name); $i++){
                $total_amount_data = Total_Amount($buy_amount, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $conn);
            }

            Db_Query($conn, "BEGIN;");

            //変更
            //発注ヘッダテーブルの内容を変更
            if($order_id != null){
                //発注が削除されていないかを確認
                $update_check_flg = Update_Check($conn, "t_order_h", "ord_id", $order_id, $_POST["hdn_ord_enter_day"]);
                //既に削除されていた場合
                if($update_check_flg === false){
                    header("Location: ./2-3-103.php?ord_id=$order_id&offline_flg=true&output_flg=delete");
                    exit;
                }

                //仕入が完了されていないかを確認
                $finish_check_flg = Finish_Check($conn, "t_order_h", "ord_id", $order_id);
                //既に完了していた場合
                if($finish_check_flg === false){
                    header("Location: ./2-3-103.php?ord_id=$order_id&offline_flg=true&output_flg=finish");
                    exit;
                }       

                $insert_sql  = "UPDATE t_order_h SET";
                $insert_sql .= "    ord_no = '$order_no',";         //発注番号
                $insert_sql .= "    client_id = $client_id,";       //仕入先ID
                $insert_sql .= ($direct != null) ? " direct_id = $direct," : " direct_id = NULL, ";          //直送先ID
                $insert_sql .= "    trade_id = $trade,";            //取引区分
                $insert_sql .= "    green_flg = '$trans_flg',";     //グリーンフラグ

//                //本部に発注する場合
//                if($head_flg == 't'){
//                    $insert_sql .= "    ord_time = now(),";         //発注日時
//                //仕入先に発注する場合
//                }elseif($head_flg == 'f'){
//                    $insert_sql .= "    ord_time = '$order_date',"; //発注日時
                //本部に対して発注する場合は登録時の時間を登録
                $insert_sql .= ($head_flg == 't' ) ? "ord_time = CURRENT_TIMESTAMP, \n" : "ord_time = '$order_date',\n " ;
//                }
        
                //本部に発注する場合
                if($head_flg == 'f'){
                    $insert_sql .= ($trans != null) ? " trans_id = $trans," : "trans_id = NULL, ";        //運送業者ID
                }
                //仕入先に発注する場合
                if($hope_day["y"] != null){
                    $insert_sql .= "    hope_day = '$hope_date',";  //希望納期
                }

                $insert_sql .= "    note_your = '$note_your',";         //通信欄
                $insert_sql .= "    c_staff_id = $staff,";          //担当者ID
                $insert_sql .= "    ware_id = $ware,";              //倉庫ID    
                $insert_sql .= "    ord_staff_id = $staff_id,";     //発注者ID

                //本部に発注する場合
                if($head_flg == 't'){
                    $insert_sql .= "    ord_stat = '1',";           //発注状況
                //仕入先に発注する場合
                }elseif($head_flg == 'f'){
                    $insert_sql .= "    ord_stat = NULL,";
                }

                $insert_sql .= "    net_amount = $total_amount_data[0],";    //税抜金額
                $insert_sql .= "    tax_amount = $total_amount_data[1],";    //消費税額
                $insert_sql .= "    ps_stat = '1',";                         //処理状況
                $insert_sql .= "    client_cd1      = (SELECT client_cd1   FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_name     = (SELECT client_name  FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_name2    = (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_cname    = (SELECT client_cname FROM t_client WHERE client_id = $client_id),";
                $insert_sql .= "    client_post_no1 = (SELECT post_no1     FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_post_no2 = (SELECT post_no2     FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_address1 = (SELECT address1     FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_address2 = (SELECT address2     FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_address3 = (SELECT address3     FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_charger1 = (SELECT charger1     FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_tel      = (SELECT tel          FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= ($direct != null) ? " direct_name     = (SELECT direct_name  FROM t_direct WHERE direct_id = $direct), " : " direct_name     = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_name2    = (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct), " : " direct_name2    = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_cname    = (SELECT direct_cname FROM t_direct WHERE direct_id = $direct), " : " direct_cname    = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_post_no1 = (SELECT post_no1     FROM t_direct WHERE direct_id = $direct), " : " direct_post_no1 = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_post_no2 = (SELECT post_no2     FROM t_direct WHERE direct_id = $direct), " : " direct_post_no2 = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_address1 = (SELECT address1     FROM t_direct WHERE direct_id = $direct), " : " direct_address1 = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_address2 = (SELECT address2     FROM t_direct WHERE direct_id = $direct), " : " direct_address2 = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_address3 = (SELECT address3     FROM t_direct WHERE direct_id = $direct), " : " direct_address3 = NULL,  ";
                $insert_sql .= ($direct != null) ? " direct_tel      = (SELECT tel          FROM t_direct WHERE direct_id = $direct), " : " direct_tel      = NULL,  ";
                $insert_sql .= "    ware_name       = (SELECT ware_name FROM t_ware WHERE ware_id = $ware), ";
                $insert_sql .= ($head_flg == "f" && $trans != null) ? " trans_name = (SELECT trans_name FROM t_trans WHERE trans_id = $trans), " : " trans_name = NULL, ";
                $insert_sql .= "    c_staff_name    = (SELECT staff_name   FROM t_staff  WHERE staff_id = $staff), ";
                $insert_sql .= "    ord_staff_name  = (SELECT staff_name   FROM t_staff  WHERE staff_id = $staff_id), ";
                $insert_sql .= "    my_client_cd1   = (SELECT client_cd1   FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_client_cd2   = (SELECT client_cd2   FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_client_name  = (SELECT client_name  FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_client_name2 = (SELECT client_name2 FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_client_cname = (SELECT client_cname FROM t_client WHERE client_id = $shop_id),";
                $insert_sql .= "    my_shop_name    = (SELECT shop_name    FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_shop_name2   = (SELECT shop_name2   FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_post_no1     = (SELECT post_no1     FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_post_no2     = (SELECT post_no2     FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_address1     = (SELECT address1     FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_address2     = (SELECT address2     FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    my_address3     = (SELECT address3     FROM t_client WHERE client_id = $shop_id), ";
                $insert_sql .= "    change_day      = CURRENT_TIMESTAMP ";
                $insert_sql .= " WHERE";
                $insert_sql .= "    ord_id = $order_id";            //発注ID
                $insert_sql .= ";";

                $result = Db_Query($conn, $insert_sql);
                if($result === false){
                    Db_Query($conn, "ROLLBACK;");
                    exit;
                }

                //発注データを削除
                $insert_sql  = "DELETE FROM";
                $insert_sql .= "    t_order_d";
                $insert_sql .= " WHERE";
                $insert_sql .= "    ord_id = $order_id";
                $insert_sql .= ";";

                $result = Db_Query($conn, $insert_sql );
                if($result === false){
                    Db_Query($conn, "ROLLBACK;");
                    exit;
                }

            //新規登録
            }else{
                $insert_sql  = "INSERT INTO t_order_h (\n";
                $insert_sql .= "    ord_id,\n";                       //発注ID
                $insert_sql .= "    ord_no,\n";                       //発注番号
                $insert_sql .= "    client_id,\n";                    //得意先ID
                $insert_sql .= ($direct != null) ? " direct_id,\n " : null;                    //直送先ID
                $insert_sql .= "    trade_id,\n ";                     //取引区分
                $insert_sql .= "    green_flg,\n";                    //グリーンフラグ
                $insert_sql .= ($head_flg == 'f' && $trans != null) ? " trans_id,\n " : null;                 //運送業者ID
                $insert_sql .= "    ord_time,\n";
                $insert_sql .= ($hope_day["y"] != null) ? " hope_day,\n " : null;     //希望納期
                $insert_sql .= "    note_your,\n";                      //通信欄
                $insert_sql .= "    c_staff_id,\n";                   //担当者ID
                $insert_sql .= "    ware_id,\n";                      //倉庫ID
                $insert_sql .= "    ord_staff_id,\n";                 //発注者ID
                $insert_sql .= "    ps_stat,\n";                      //処理状況
                $insert_sql .= "    ord_stat,\n";                     //発注状況
                $insert_sql .= "    net_amount,\n";                   //税抜金額
                $insert_sql .= "    tax_amount,\n";                   //消費税額
                $insert_sql .= "    shop_id,\n ";                      //ショップID
                //$insert_sql .= "    shop_gid";                      //FCグループID
                $insert_sql .= "    client_cd1,\n ";
                $insert_sql .= "    client_name,\n ";
                $insert_sql .= "    client_name2,\n ";
                $insert_sql .= "    client_post_no1,\n ";
                $insert_sql .= "    client_post_no2,\n ";
                $insert_sql .= "    client_address1,\n ";
                $insert_sql .= "    client_address2,\n ";
                $insert_sql .= "    client_address3,\n ";
                $insert_sql .= "    client_charger1,\n ";
                $insert_sql .= "    client_tel, \n";
                if ($direct != null){
                    $insert_sql .= "    direct_name,\n ";
                    $insert_sql .= "    direct_name2,\n ";
                    $insert_sql .= "    direct_cname,\n ";
                    $insert_sql .= "    direct_post_no1,\n ";
                    $insert_sql .= "    direct_post_no2,\n ";
                    $insert_sql .= "    direct_address1,\n ";
                    $insert_sql .= "    direct_address2,\n ";
                    $insert_sql .= "    direct_address3,\n ";
                    $insert_sql .= "    direct_tel,\n";
                }
                $insert_sql .= "    ware_name,\n ";
                $insert_sql .= ($head_flg == "f" && $trans != null) ? " trans_name,\n " : null;
                $insert_sql .= "    c_staff_name,\n ";
                $insert_sql .= "    ord_staff_name,\n ";
                $insert_sql .= "    send_date,\n";
                $insert_sql .= "    client_cname,\n";
                $insert_sql .= "    my_client_cd1,\n";
                $insert_sql .= "    my_client_cd2,\n";
                $insert_sql .= "    my_client_name,\n ";
                $insert_sql .= "    my_client_name2,\n ";
                $insert_sql .= "    my_shop_name,\n";
                $insert_sql .= "    my_shop_name2,\n";
                $insert_sql .= "    my_client_cname,\n";
                $insert_sql .= "    my_post_no1,\n ";
                $insert_sql .= "    my_post_no2,\n ";
                $insert_sql .= "    my_address1,\n ";
                $insert_sql .= "    my_address2,\n ";
                $insert_sql .= "    my_address3 \n";
                $insert_sql .= " )VALUES(\n";
                $insert_sql .= "    (SELECT COALESCE(MAX(ord_id), 0)+1 FROM t_order_h),\n";
                $insert_sql .= "    '$order_no',\n";
                $insert_sql .= "    $client_id,\n";
                $insert_sql .= ($direct != null) ? " $direct,\n " : null;
                $insert_sql .= "    '$trade',\n";
                $insert_sql .= "    '$trans_flg',\n";
                $insert_sql .= ($head_flg == 'f' && $trans != null) ? " $trans, \n" : null;
                $insert_sql .= ($head_flg == 't' ) ? "CURRENT_TIMESTAMP, \n" : "'$order_date',\n " ;
//                $insert_sql .= "    '$order_date',\n";
                $insert_sql .= ($hope_day["y"] != null) ? " '$hope_date',\n " : null;
                $insert_sql .= "    '$note_your',\n";
                $insert_sql .= "    $staff,\n";
                $insert_sql .= "    $ware,\n";
                $insert_sql .= "    $staff_id,\n";
                $insert_sql .= "    '1',\n";

                if($head_flg == 't'){
                    $insert_sql .= "    '1',\n";
                }else{
                    $insert_sql .= "    NULL,\n";
                }
                $insert_sql .= "    $total_amount_data[0],\n";
                $insert_sql .= "    $total_amount_data[1],\n";
                //$insert_sql .= "    $shop_gid";
                $insert_sql .= "    $shop_id,\n ";
                $insert_sql .= "    (SELECT client_cd1      FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT client_name     FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT client_name2    FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT post_no1        FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT post_no2        FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT address1        FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT address2        FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT address3        FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT charger1        FROM t_client WHERE client_id = $client_id),\n ";
                $insert_sql .= "    (SELECT tel             FROM t_client WHERE client_id = $client_id),\n ";
                if ($direct != null){
                    $insert_sql .= "    (SELECT direct_name  FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT direct_cname FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT post_no1     FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT post_no2     FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT address1     FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT address2     FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT address3     FROM t_direct WHERE direct_id = $direct),\n ";
                    $insert_sql .= "    (SELECT tel          FROM t_direct WHERE direct_id = $direct),\n ";
                }
                $insert_sql .= "    (SELECT ware_name  FROM t_ware WHERE ware_id = $ware),\n ";
                if($head_flg == 'f'){
                    $insert_sql .= ($trans != null) ? " (SELECT trans_name FROM t_trans WHERE trans_id = $trans),\n " : null;
                }
                $insert_sql .= "    (SELECT staff_name      FROM t_staff  WHERE staff_id = $staff),\n ";
                $insert_sql .= "    (SELECT staff_name      FROM t_staff  WHERE staff_id = $staff_id),\n ";
                $insert_sql .= "    NOW(),\n";
                $insert_sql .= "    (SELECT client_cname    FROM t_client WHERE client_id = $client_id),\n";
                $insert_sql .= "    (SELECT client_cd1      FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT client_cd2      FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT client_name     FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT client_name2    FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT shop_name       FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT shop_name2      FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT client_cname    FROM t_client WHERE client_id = $shop_id),\n";
                $insert_sql .= "    (SELECT post_no1        FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT post_no2        FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT address1        FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT address2        FROM t_client WHERE client_id = $shop_id),\n ";
                $insert_sql .= "    (SELECT address3        FROM t_client WHERE client_id = $shop_id) \n ";
                $insert_sql .= ");\n";

                $result = Db_Query($conn, $insert_sql);
                
                if($result === false){
                    $err_message = pg_last_error();
                    $err_format = "t_order_h_ord_no_key";

                    Db_Query($conn, "ROLLBACK;");
                    //発注NOが重複した場合           
 
                    if((strstr($err_message, $err_format) != false)){ 
                        $error = "同時に発注を行ったため、発注NOが重複しました。もう一度発注をして下さい。";
 
                        //再度発注NOを取得する
                        $sql  = "SELECT ";
                        $sql .= "   MAX(ord_no)";
                        $sql .= " FROM";
                        $sql .= "   t_order_h";
                        $sql .= " WHERE";
                        $sql .= "   shop_id = $shop_id";
                        $sql .= ";";

                        $result = Db_Query($conn, $sql);
                        $order_no = pg_fetch_result($result, 0 ,0);
                        $order_no = $order_no +1;
                        $order_no = str_pad($order_no, 8, 0, STR_PAD_LEFT);

                        $err_data["form_order_no"] = $order_no;

                        $form->setConstants($err_data);

                        $duplicate_err = true;
                    }else{
                        exit;
                    }
                }
            }

            if($duplicate_err != true){
                //発注データ登録
                for($i = 0; $i < count($goods_id); $i++){
                    //条件追加
                    //商品が存在した場合
                    if($goods_id[$i] != null){
                        //行
                        $line = $i + 1;

                        /***************************************/
                        //仕入金額税抜、消費税、消費税合計を算出
                        /***************************************/
                        $price = $buy_price[$i]["i"].".".$buy_price[$i]["d"];         //仕入単価

                        /*********************/
                        //仕入金額
                        /*********************/
                        $buy_amount = bcmul($price, $order_num[$i],2);  
						$buy_amount = Coax_Col($coax, $buy_amount);      
//                      $data = Total_Amount($buy_amount, $tax_div[$i], $coax, $tax_franct, $tax_rate);
//                      $tax_price   = $data[1];

                        $insert_sql  = "INSERT INTO t_order_d (";
                        $insert_sql .= "    ord_d_id,";
                        $insert_sql .= "    ord_id,";
                        $insert_sql .= "    line,";
                        $insert_sql .= "    goods_id,";
                        $insert_sql .= "    goods_name,";
                        $insert_sql .= "    num,";
                        $insert_sql .= "    tax_div,";
                        $insert_sql .= "    buy_price,";
                        $insert_sql .= "    buy_amount,";
//                      $insert_sql .= "    tax_amount";
                        $insert_sql .= "    goods_cd ";
                        $insert_sql .= ")VALUES(";
                        $insert_sql .= "    (SELECT COALESCE(MAX(ord_d_id), 0)+1 FROM t_order_d),";  
                        $insert_sql .= "    (SELECT";
                        $insert_sql .= "         ord_id";
                        $insert_sql .= "     FROM";
                        $insert_sql .= "        t_order_h";
                        $insert_sql .= "     WHERE";
                        $insert_sql .= "        ord_no = '$order_no'";
                        $insert_sql .= "        AND";
                        $insert_sql .= "        shop_id = $shop_id";
                        $insert_sql .= "    ),";
                        $insert_sql .= "    '$line',";
                        $insert_sql .= "    $goods_id[$i],";
                        $insert_sql .= "    '$goods_name[$i]',"; 
                        $insert_sql .= "    '$order_num[$i]',";
                        $insert_sql .= "    '$tax_div[$i]',";
                        $insert_sql .= "    $price,";
                        $insert_sql .= "    $buy_amount,";
//                      $insert_sql .= "    $tax_price";
                        $insert_sql .= "    (SELECT goods_cd FROM t_goods WHERE goods_id = $goods_id[$i]) ";
                        $insert_sql .= ");";
                        $result = Db_Query($conn, $insert_sql);

                        //失敗した場合はロールバック
                        if($result === false){
                            Db_Query($conn, "ROLLBACK;");
                            exit;
                        }
                    }
                }

                for($i = 0; $i < count($goods_id); $i++){
        
                    $line = $i + 1;
//いかなる場合も在庫受け払いテーブルに登録
//                    if($stock_manage_flg[$i] == '1'){
                        //受け払いテーブルに登録
                        $insert_sql  = " INSERT INTO t_stock_hand (";
                        $insert_sql .= "    goods_id,";
                        $insert_sql .= "    enter_day,";
                        $insert_sql .= "    work_day,";
                        $insert_sql .= "    work_div,";
                        $insert_sql .= "    client_id,";
                        $insert_sql .= "    ware_id,";
                        $insert_sql .= "    io_div,";
                        $insert_sql .= "    num,";
                        $insert_sql .= "    slip_no,";
                        $insert_sql .= "    ord_d_id,";
                        $insert_sql .= "    staff_id,";
                        $insert_sql .= "    shop_id,";
                        $insert_sql .= "    client_cname ";
                        $insert_sql .= ")VALUES(";
                        $insert_sql .= "    $goods_id[$i],";
                        $insert_sql .= "    NOW(),";
                        $insert_sql .= ($head_flg == 't' ) ? "CURRENT_TIMESTAMP, \n" : "'$order_date',\n " ;
//                        $insert_sql .= "    '".$order_day["y"]."-".$order_day["m"]."-".$order_day["d"]."',";
                        $insert_sql .= "    '3',";
                        $insert_sql .= "    $client_id,";
                        $insert_sql .= "    $ware,";
                        $insert_sql .= "    '1',";
                        $insert_sql .= "    $order_num[$i],";
                        $insert_sql .= "    '$order_no',";
                        $insert_sql .= "    (SELECT";
                        $insert_sql .= "        ord_d_id";
                        $insert_sql .= "    FROM";
                        $insert_sql .= "        t_order_d";
                        $insert_sql .= "    WHERE";
                        $insert_sql .= "        line = $line";
                        $insert_sql .= "        AND";
                        $insert_sql .= "        ord_id = (SELECT";
                        $insert_sql .= "                    ord_id";
                        $insert_sql .= "                 FROM";
                        $insert_sql .= "                    t_order_h";
                        $insert_sql .= "                 WHERE";
                        $insert_sql .= "                    ord_no = '$order_no'";
                        $insert_sql .= "                    AND";
                        $insert_sql .= "                    shop_id = $shop_id";
                        $insert_sql .= "                )";
                        $insert_sql .= "    ),";
                        $insert_sql .= "    $staff_id,";
                        $insert_sql .= "    $shop_id,";
                        $insert_sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id)";
                        $insert_sql .= ");";

                        $result = Db_Query($conn, $insert_sql);
                        if($result === false){
                            Db_Query($conn, "ROLLBACK;");
                            exit;
                        }
//                    }
                }

                //発注確認に渡す発注ID取得
                $select_sql  = "SELECT ";
                $select_sql .= "    ord_id ";
                $select_sql .= "FROM ";
                $select_sql .= "    t_order_h ";
                $select_sql .= "WHERE ";
                $select_sql .= "    ord_no = '$order_no'";
                $select_sql .= "AND ";
                $select_sql .= "    shop_id = $shop_id;";
                $result = Db_Query($conn, $select_sql);
                $order_id = pg_fetch_result($result,0,0);

                Db_Query($conn, "COMMIT;");

                if($head_flg == 't'){
                    if($_POST["order_button"] == "オンライン発注OKと発注書出力" || $_POST["order_button"] == "オフライン発注OKと発注書出力"){
                        //本部かつ発注書出力
                        header("Location: ./2-3-103.php?ord_id=$order_id&online_flg=true&output_flg=true");
                    }else{
                        //本部かつ発注書ださない
                        header("Location: ./2-3-103.php?ord_id=$order_id&online_flg=true");
                    }
                }else{
                    if($_POST["order_button"] == "オンライン発注OKと発注書出力" || $_POST["order_button"] == "オフライン発注OKと発注書出力"){
                        //FCかつ発注書出力
                        header("Location: ./2-3-103.php?ord_id=$order_id&offline_flg=true&output_flg=true");
                    }else{
                        //FCかつ発注書ださない
                        header("Location: ./2-3-103.php?ord_id=$order_id&offline_flg=true");
                    } 
                }
            }
        }else{
            //登録確認画面を表示フラグ
            $freeze_flg = true;
        }
    }
}

/****************************/
//行数追加
/****************************/
if($_POST["add_row_flg"]==true){
    //最大行に、＋５する
    $max_row = $_POST["max_row"]+5;

    //行数追加フラグをクリア
    $add_row_data["add_row_flg"] = "";
    $form->setConstants($add_row_data);

}

/*****************************/
//フォーム作成（変動）
/*****************************/
//仕入先が選択されていないか、発注登録確認の場合はフリーズ
if($client_search_flg != true || $freeze_flg == true){
    #2009-09-15 hashimoto-y
    #$style = "color : #000000; 
    #        border : #ffffff 1px solid; 
    #        background-color: #ffffff"; 
    $style = "border : #ffffff 1px solid; 
            background-color: #ffffff"; 
    $type = "readonly";
}else{
    $type = $g_form_option;
}
//行番号カウンタ
$row_num = 1;
$i = 0;

for($i = 0; $i < $max_row; $i++){
    //表示行判定
    if(!in_array("$i", $del_history)){

        $del_data = $del_row.",".$i;

        #2009-09-10 hashimoto-y
        //値引商品を選択した場合には赤字に変更
        $font_color = "";

        $hdn_discount_flg = $form->getElementValue("hdn_discount_flg[$i]");

        #print_r($hdn_discount_flg);

        if($hdn_discount_flg === 't'){
            $font_color = "color: red; ";
        }else{
            $font_color = "color: #000000; ";
        }



        //商品ID
        $form->addElement("hidden","hdn_goods_id[$i]");

        //商品コード
        $form->addElement(
            "text","form_goods_cd[$i]","","size=\"10\" maxLength=\"8\" 
             style=\"$font_color $style $g_form_style \" $type 
            onChange=\"return goods_search_2(this.form, 'form_goods_cd', 'goods_search_row', $i ,$row_num)\""
        );

        //商品名
//        if(($_POST["hdn_name_change"][$i] == '2' || $name_change[$i] == '2') && $freeze_flg != true){
        if(($name_change[$i] == '2') && $freeze_flg != true){
            $form->addElement(
                "text","form_goods_name[$i]","",
                "size=\"54\" $g_text_readonly" 
            );
        }else{
            $form->addElement(
                "text","form_goods_name[$i]","",
                "size=\"54\" maxLength=\"41\" 
                style=\"$font_color $style\" $type"
            );
        }

        //品名変更フラグ
        $form->addElement("hidden","hdn_name_change[$i]");

        //在庫管理
        $form->addElement("hidden","hdn_stock_manage[$i]");

        //実棚数
        if($stock_manage[$i] == 1){
            if($client_id != null){
                $form->addElement("link","form_stock_num[$i]","","#","$stock_num[$i]",
                "onClick=\"javascript:Open_mlessDialmg_g('2-3-111.php','$goods_id[$i]',$shop_id,300,160);\"");
            }else{
                $form->addElement("static","form_stock_num[$i]","","$stock_num[$i]");
            }
        }

        $form->addElement("hidden","hdn_stock_num[$i]","","");

        //発注済数
        $form->addElement(
            "text","form_rorder_num[$i]","",
            "size=\"11\" maxLength=\"9\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );

        //引当数
        $form->addElement("text","form_rstock_num[$i]","",
            "size=\"11\" maxLength=\"9\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );

        //出荷可能数
        $form->addElement("text","form_designated_num[$i]","",
            "size=\"11\" maxLength=\"9\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );

        //入数
        $form->addElement("text","form_in_num[$i]","",
            "size=\"11\" maxLength=\"9\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" 
            readonly'"
        );

        //注文入数
        $form->addElement("text","form_order_in_num[$i]","",
            "size=\"6\" maxLength=\"5\" 
            onKeyup=\"in_num($i,'hdn_goods_id[$i]','form_order_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
            style=\"text-align: right; $font_color $style $g_form_style \"
            $type"
        );

        //オンライン発注の場合は個数変更不可
/*
        if($head_flg == 't'){ 
            //発注数
            $form->addElement("text","form_order_num[$i]","",
                "size=\"11\" maxLength=\"9\" 
                style=\"color : #000000; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'
            ");

        }else{
*/
            //発注数
            $form->addElement("text","form_order_num[$i]","",
                "size=\"6\" maxLength=\"5\" 
                onKeyup=\"Mult('hdn_goods_id[$i]','form_order_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');ord_in_num($i);\"
                style=\"text-align: right; $font_color $style $g_form_style \"
                $type"
            );
//        }

        //仕入単価
        $form_buy_price[$i][] =& $form->createElement(
            "text","i","",
            "size=\"11\" maxLength=\"9\" class=\"money\"
            onKeyup=\"Mult('hdn_goods_id[$i]','form_order_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
            style=\"text-align: right; $font_color $style $g_form_style \"
            $type"
        );
        $form_buy_price[$i][] =& $form->createElement("static","","",".");
        $form_buy_price[$i][] =& $form->createElement(
            "text","d","","size=\"2\" maxLength=\"2\" 
            onKeyup=\"Mult('hdn_goods_id[$i]','form_order_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
            style=\"text-align: left; $font_color $style $g_form_style \"
            $type"
        );
        $form->addGroup( $form_buy_price[$i], "form_buy_price[$i]", "");

        //課税区分
        $form->addElement("hidden","hdn_tax_div[$i]","","");

        //金額(税抜き)
        $form->addElement(
            "text","form_buy_amount[$i]","",
            "size=\"25\" maxLength=\"18\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );

        //aoyama-n 2009-09-07
        //値引フラグ
        #2009-09-15 hashimoto-y
        #$form->addElement("hidden","hdn_discount_flg[$i]");


        //その他商品か登録確認画面の場合は非表示
        if($client_search_flg === true && $freeze_flg == false){
            //検索リンク
            $form->addElement(
                "link","form_search[$i]","","#","検索",
                "TABINDEX=-1 onClick=\"return Open_SubWin_2('../dialog/2-0-210.php', Array('form_goods_cd[$i]','goods_search_row'), 500, 450,5,$client_id,$i,$row_num);\""
            );
            //削除リンク
            //最終行を削除する場合、削除した後の最終行に合わせる
            $del_str = ($auth[0] == "w") ? "削除" : null;
            if($row_num == $max_row-$del_num){
                $form->addElement(
                    "link","form_del_row[$i]","",
                    "#","<font color='#FEFEFE'>$del_str</font>","TABINDEX=-1 onClick=\"javascript:Dialogue_3('削除します。', '$del_data', 'del_row' ,$row_num-1);return false;\""
                );
            //最終行以外を削除する場合、削除する行と同じNOの行に合わせる
            }else{
                $form->addElement(
                    "link","form_del_row[$i]","",
                    "#","<font color='#FEFEFE'>$del_str</font>","TABINDEX=-1 onClick=\"javascript:Dialogue_3('削除します。', '$del_data', 'del_row' ,$row_num);return false;\""
                );
            }
        }

        /****************************/
        //表示用HTML作成
        /****************************/
        $html .= " <tr class=\"Result1\">";
        $html .= "  <A NAME=$row_num><td align=\"right\">$row_num</td></A>";
        $html .= "  <td align=\"left\">";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_cd[$i]"]]->toHtml();
        if($client_search_flg === true && $freeze_flg == false){
            $html .= "      （".$form->_elements[$form->_elementIndex["form_search[$i]"]]->toHtml()."）";
        }
        $html .= "  <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_name[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"right\" width=\"80\">";

        //在庫管理する場合
        if($stock_manage[$i] == 1){
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
        //在庫管理しない場合
        }elseif($stock_manage[$i] == 2){
            $html .= "<p style=\"$font_color\">";
            $html .= "-";
            $html .= "</p>";
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .= "<p style=\"$font_color\">";
            $html .= "-";
            $html .= "</p>";
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .= "<p style=\"$font_color\">";
            $html .= "-";
            $html .= "</p>";
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .= "<p style=\"$font_color\">";
            $html .= "-";
            $html .= "</p>";
            $html .= "  </td>";
        //その他
        }else{
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .= "  </td>";
            $html .= "  <td align=\"right\">";
            $html .= "  </td>";
        }
        $html .= "  <td align=\"center\">";
        $html .=        $form->_elements[$form->_elementIndex["form_order_in_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_in_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"center\">";
        $html .=        $form->_elements[$form->_elementIndex["form_order_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"center\">";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_price[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_amount[$i]"]]->toHtml();
        $html .= "  </td>";
        
        if($client_search_flg === true && $freeze_flg == false){
            $html .= "  <td class=\"Title_Add\" align=\"center\">";
            $html .=        $form->_elements[$form->_elementIndex["form_del_row[$i]"]]->toHtml();
            $html .= "  </td>";
        }
        $html .= " </tr>";

        //行番号を＋１
        $row_num = $row_num+1;
    }
}

//登録確認画面では、以下のボタンを表示しない
if($freeze_flg != true){

    //button
    $form->addElement("submit","form_order_button","発注確認画面へ", $disabled);
    $form->addElement("button","form_back_button","戻　る","onClick=\"javascript:history.back()\"");
    $form->addElement("button","form_sum_button","合　計","onClick=\"javascript:Button_Submit('sum_button_flg','#foot','true')\"");

    $form->addElement("link","form_client_link","","./2-3-102.php","発注先","
        onClick=\"return Open_SubWin('../dialog/2-0-208.php',Array('form_client[cd]','form_client[name]', 'client_search_flg'),500,450,5,1);\""
    );
    //仕入先が選択されていない場合はフリーズ
    //行追加ボタン
    if($client_search_flg === true){
        $form->addElement("button","form_add_row","行追加","onClick=\"javascript:Button_Submit_1('add_row_flg', '#foot', 'true')\"");
    }

}else{
    //登録確認画面では以下のボタンを表示
    //戻る
    $form->addElement("button","return_button","戻　る","onClick=\"javascript:history.back()\"");
    
    if($head_flg == 't'){
        //OK
        $form->addElement("submit","comp_button","オンライン発注OK", $disabled);
        //発注書出力
        $form->addElement("submit","order_button","オンライン発注OKと発注書出力", $disabled);
    }else{
        //OK
        $form->addElement("submit","comp_button","オフライン発注OK", $disabled);
        //発注書出力
        $form->addElement("submit","order_button","オフライン発注OKと発注書出力", $disabled);
    }
    $form->freeze();
}

/****************************/
//JavaScript
/****************************/
/****************************************************/
//ロット数を計算する
$js  = " function in_num(num,id,order_num,price_i,price_d,amount,coax){\n";
$js .= "    var in_num = \"form_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_in_num = \"form_order_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_num = \"form_order_num\"+\"[\"+num+\"]\";\n";
$js .= "    var buy_amount = \"form_buy_amount\"+\"[\"+num+\"]\";\n";

$js .= "    var v_in_num = document.dateForm.elements[in_num].value;\n";
$js .= "    var v_ord_in_num = document.dateForm.elements[ord_in_num].value;\n";
$js .= "    var v_ord_num = document.dateForm.elements[ord_num].value;\n";
$js .= "    var v_num = v_in_num * v_ord_in_num;\n";

$js .= "    if(isNaN(v_num) == false){\n";
$js .= "        document.dateForm.elements[ord_num].value = v_num;\n";
$js .= "    }else{\n";
$js .= "        document.dateForm.elements[ord_num].value = \"\";\n";
$js .= "    }\n";
$js .= "    Mult(id,order_num,price_i,price_d,amount,coax);\n";
$js .= "}\n";

//注文ロット数を計算する
$js .= "function ord_in_num(num){\n";
$js .= "    var in_num = \"form_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_in_num = \"form_order_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_num = \"form_order_num\"+\"[\"+num+\"]\";\n";
$js .= "    var v_in_num = document.dateForm.elements[in_num].value;\n";
$js .= "    var v_ord_in_num = document.dateForm.elements[ord_in_num].value;\n";
$js .= "    var v_ord_num = document.dateForm.elements[ord_num].value;\n";
$js .= "    var result = v_ord_num % v_in_num;\n";
$js .= "    if(result == 0){\n";
$js .= "        var res = v_ord_num / v_in_num;\n";
$js .= "        document.dateForm.elements[ord_in_num].value = res;\n";
$js .= "    }else{  \n";
$js .= "        document.dateForm.elements[ord_in_num].value = \"\";\n";
$js .= "    }\n";
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
$page_menu = Create_Menu_f('buy','1');

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "　".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[ord_button]]->toHtml();
$page_header = Create_Header($page_title);

// Render関連の設定
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());

//その他の変数をassign
$smarty->assign('var',array(
    'html_header'   => "$html_header",
    'page_menu'     => "$page_menu",
    'page_header'   => "$page_header",
    'html_footer'   => "$html_footer",
    'html'          => "$html",
    'message'       => "$message",
    'warning'       => "$warning",
    'error'         => "$error",
    'freeze_flg'    => "$freeze_flg",
    'goods_twice'   => "$goods_twice",
    'js'            => "$js",
    'auth_r_msg'    => "$auth_r_msg",
    'update_flg'    => "$update_flg",
    'head_flg'      => "$head_flg",
));

$smarty->assign("goods_err", $goods_err);
$smarty->assign("price_num_err", $price_num_err);
$smarty->assign("num_err", $num_err);
$smarty->assign("price_err", $price_err);
$smarty->assign("duplicate_goods_err", $duplicate_goods_err);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
