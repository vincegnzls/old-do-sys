<?php
/********************
 * 発注入力
 *
 *
 * 変更履歴
 *   2006/07/06 (kaji)
 *     ・shop_gidをなくす
 *   2006/07/19 (kaji)
 *     ・在庫受払テーブルへの書込みSQLを修正
 *       （作業区分抜け、カンマ抜け）
 *　 2006/07/21
 *     ・入数を商品マスタのに移動したことによる変更
 *   　・引当数計算の変更
 *   2006/07/26 (watanabe-k)
 *　　 ・商品の抽出条件の変更
 *   2006/07/31 (watanabe-k)
 *     ・消費税計算処理変更
 *   2006/09/16 (fukuda-sss)
 *     ・消費税計算処理変更
 *   2006/12/01 (suzuki)
 *     ・仕入金額計算処理変更
 *     ・仕入先変更時に商品初期化
********************/
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 *   2006/10/16  06-001        watanabe-k  先に出荷可能数を以上入力した後に商品コードを入力すると商品名が表示されないバグの修正 
 *   2006/10/16  06-003        watanabe-k  発注点警告→発注入力へ画面遷移した場合、行追加ボタンをクリックっしても入力行が増えないバグの修正
 *   2006/10/16  06-004        watanabe-k  在庫照会→発注入力へ画面遷移した場合、ランタイムエラーが発生するバグの修正
 *   2006/10/16  06-004        watanabe-k  在庫照会→発注入力へ画面遷移した場合、ランタイムエラーが発生するバグの修正
 *   2006/10/16  06-006        watanabe-k  現金仕入れで発注を行い、再度発注照会で表示すると掛仕入と表示されるバグの修正
 *   2006/10/16  06-007        watanabe-k  発注入力画面で商品名をブランクにして発注を行うと、「商品が一つも選択されていません。」というメッセージが表示されるバグの修正
 *   2006/10/16  06-004        watanabe-k  在庫照会→発注入力へ画面遷移した場合、ランタイムエラーが発生するバグの修正
 *   2006/10/17  06-018        watanabe-k  不正な商品コードを入力してもエラーた表示されないバグの修正 
 *   2006/10/17  06-019        watanabe-k　不正内仕入単価（aaa,000）を入力してもエラーメッセージが表示されないバグの修正 
 *   2006/10/17  06-022        watanabe-k　発注日のエラーメッセージが本部FCとで異なるバグの修正 
 *   2006/10/17  06-025        watanabe-k  同時処理を行うとエラーで白い画面になるバグの修正
 *   2006/10/17  06-025        watanabe-k  システム開始日より前の日で発注ができてしまうバグの修正
 *   2006/10/17  06-025        watanabe-k  金額の小数点以下をブランクにして発注するとエラーになるバグの修正
 * 　2006/10/18  06-024        watanabe-k  同時処理を行うとエラーになるバグの修正
 * 　2006/10/18  06-038        watanabe-k  URL操作でSQLエラーが表示されるバグの修正
 * 　2006/10/19  06-040        watanabe-k  商品コードを不正入力し、フォーカスを異動しないまま発注確認画面へボタンをクリックすると、確認画面に遷移してしまう。
 * 　2006/10/19  06-044        watanabe-k  仕入先コードを不正入力し、フォーカスを異動しないまま発注確認画面へボタンをクリックすると、確認画面に遷移してしまう。
 * 　2006/11/11  06-047        watanabe-k　仕入入力済みの発注データを変更可能なバグを修正
 * 　2006/11/11  06-049        watanabe-k　長い商品名が表示されていないバグの修正
 * 　2006/11/13  06-079        watanabe-k　仕入中に発注変更ができてしまうバグの修正
 * 　2006/11/13  06-105        watanabe-k　仕入完了後に発注変更できてしまうバグの修正
 * 　2006/11/13  06-081        watanabe-k　ロット数に文字列を入力するとNaNと表示されるバグの修正
 * 　2007/01/24  仕様変更      watanabe-k　ボタンの色変更
 * 　2007/02/01                watanabe-k　発注先と直送先にTELを登録する
 * 　2007/02/05                watanabe-k　発信日を表示しないように修正
 *   2007/02/28                  morita-d  商品名は正式名称を表示するように変更 
 *   2007/03/13                watanabe-k  同一商品を複数選択した場合のエラーを具体的に表示するように修正
 *   2007/05/18                watanabe-k  直送先のプルダウンを等幅に変更
 *   2007/05/25                  morita-d  発注先は仕入先マスタでなくＦＣマスタを使用するように変更 
 *   2007/05/25                  morita-d  変更時に商品が5行までしか表示されない不具合を修正 
 *	 2009/06/16	 改修-No.13		 aizawa-m  在庫照会のソートと同じ条件に変更
 *	 2009/06/17	 改修おまけ		 aizawa-m  画面「仕入金額」のデフォルトを0で出力するに変更
 *	 2009/09/08			　　　　 aoyama-n  値引機能追加
 *   2009/09/15               hashimoto-y  商品の値引きを赤字表示に修正
 *   2009/10/09  rev.1.3        kajioka-h  直送先テキスト入力・ダイアログ化
 *               rev.1.3        kajioka-h  ショップごとに商品の在庫管理フラグを持つ変更対応
 *   2009/10/13                 kajioka-h  発注変更時に得意先担当者（client_charger1）にt_client.telが入っていたのをt_client.charger1に修正
 *   2009/12/21                 aoyama-n   税率をTaxRateクラスから取得
 *     
 */



$page_title = "発注入力";

//環境設定ファイル
require_once("ENV_local.php");
require_once(INCLUDE_DIR."function_buy.inc");

$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/*****************************/
// 再遷移先をSESSIONにセット
/*****************************/
// GET、POSTが無い場合
if ($_GET == null && $_POST == null){
    Set_Rtn_Page("ord");
}


/*****************************/
//外部変数取得
/*****************************/
//$shop_gid = $_SESSION["shop_gid"];
$shop_id  = $_SESSION["client_id"];
$rank_cd  = $_SESSION["rank_cd"];
$staff_id = $_SESSION["staff_id"];

/*****************************/
//初期表示設定
/*****************************/

#2009-12-21 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);

//自動採番の発注番号取得
$sql  = "SELECT";
$sql .= "   MAX(ord_no)";
$sql .= " FROM";
$sql .= "   t_order_h";
$sql .= " WHERE";
$sql .= "   shop_id = $shop_id";
$sql .= ";"; 

$result = Db_Query($db_con, $sql);
$order_no = pg_fetch_result($result, 0 ,0);
$order_no = $order_no +1;
$order_no = str_pad($order_no, 8, 0, STR_PAD_LEFT);

$def_data["form_order_no"] = $order_no;

//出荷倉庫
$sql  = "SELECT";
$sql .= "   ware_id";
$sql .= " FROM";
$sql .= "   t_client";
$sql .= " WHERE";
$sql .= "   client_id = $shop_id";
$sql .= ";";

$result = Db_Query($db_con, $sql);
$def_data["form_ware"] = pg_fetch_result($result, 0, 0);

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

//希望納期
$def_data["form_hope_day"]["y"] = date("Y", mktime(0,0,0,date("m"),date("d")+1,date("Y")));
$def_data["form_hope_day"]["m"] = date("m", mktime(0,0,0,date("m"),date("d")+1,date("Y")));
$def_data["form_hope_day"]["d"] = date("d", mktime(0,0,0,date("m"),date("d")+1,date("Y")));

//入荷予定日
$def_data["form_arrival_day"]["y"] = date("Y", mktime(0,0,0,date("m"),date("d")+1,date("Y")));
$def_data["form_arrival_day"]["m"] = date("m", mktime(0,0,0,date("m"),date("d")+1,date("Y")));
$def_data["form_arrival_day"]["d"] = date("d", mktime(0,0,0,date("m"),date("d")+1,date("Y")));


$form->setDefaults($def_data);

/*****************************/
//初期設定＆共通処理
/*****************************/
//自分の消費税を抽出
#2009-12-21 aoyama-n
#$sql  = "SELECT";
#$sql .= "   tax_rate_n";
#$sql .= " FROM";
#$sql .= "   t_client";
#$sql .= " WHERE";
#$sql .= "   client_id = $shop_id";
#$sql .= ";";

#$result = Db_Query($db_con, $sql);
#$tax_rate = pg_fetch_result($result,0,0);
#$rate  = bcdiv($tax_rate,100,2);                //消費税率

//データ表示行数
if($_POST["max_row"] != NULL){
    $max_row = $_POST["max_row"];
//初期表示のみ
}else{
    $max_row = 5;
}

//仕入先IDの有無により、メッセージ設定OR仕入先情報を取得
$client_search_flg = ($_POST["hdn_client_id"] != NULL)? true : false;

if($client_search_flg == true){
    //仕入先の情報を取得
    $client_id  = $_POST["hdn_client_id"];      //仕入先ID
    $coax       = $_POST["hdn_coax"];           //丸め区分
    $tax_franct = $_POST["hdn_tax_franct"];     //端数区分
}else{
    $warning = "仕入先を選択してください。";
    $client_search_flg = false;
}

//削除行数
$del_history[] = NULL;

//Submitした場合に持ちまわるデータ
$goods_id       = $_POST["hdn_goods_id"];
$stock_num      = $_POST["hdn_stock_num"];
$stock_manage   = $_POST["hdn_stock_manage"];
$name_change    = $_POST["hdn_name_change"];

/****************************/
//フォーム作成
/****************************/
//発注残一覧
$form->addElement("button","ord_button","発注残一覧","onClick=\"javascript:location.href='1-3-106.php'\"");
//入力・変更
$form->addElement("button","new_button","入　力",$g_button_color." onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
//照会
$form->addElement("button","change_button","照会・変更","onClick=\"javascript:location.href='1-3-104.php'\"");

//発信日
$form->addElement(
    "text","form_send_date","",
    "style=\"color : #000000; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);

//出荷可能数
$form->addElement(
    "text","form_designated_date","",
    "size=\"4\" maxLength=\"4\" 
    $g_form_option 
    style=\"text-align: right; $g_form_style \"
    onChange=\"javascript:Button_Submit('hdn_designated_date_flg','#','t')\""
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

//入荷予定日
$form_arrival_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\"
    style=\"$g_form_style \" 
    onkeyup=\"changeText(this.form,'form_arrival_day[y]','form_arrival_day[m]',4)\" 
    onFocus=\"onForm_today(this,this.form,'form_arrival_day[y]','form_arrival_day[m]','form_arrival_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form_arrival_day[] = $form->createElement(
    "text","m","",
    "size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style \" 
    onkeyup=\"changeText(this.form,'form_arrival_day[m]','form_arrival_day[d]',2)\" 
    onFocus=\"onForm_today(this,this.form,'form_arrival_day[y]','form_arrival_day[m]','form_arrival_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form_arrival_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style \" 
    onFocus=\"onForm_today(this,this.form,'form_arrival_day[y]','form_arrival_day[m]','form_arriva_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form->addGroup( $form_arrival_day,"form_arrival_day","","-");


//グリーン指定業者
$select_value = Select_Get($db_con,"trans");
$form->addElement("select", "form_trans",'', $select_value, $g_form_option_select);

//発注先
/*
$form_client[] = $form->createElement(
    "text","cd","",
    "size=\"7\" maxLength=\"6\" 
    style=\"$g_form_style \" 
    onChange=\"javascript:Button_Submit('hdn_client_search_flg','#','true')\" 
    $g_form_option"
);
*/
$form_client[] = $form->createElement(
    "text","cd1","",
    "size=\"7\" maxLength=\"6\"
    style=\"$g_form_style\"
    onChange=\"javascript:Change_Submit('hdn_client_search_flg','#','true','form_client[cd2]')\"
    onkeyup=\"changeText(this.form,'form_client[cd1]','form_client[cd2]',6)\"
    $g_form_option"
);
$form_client[] = $form->createElement("static","","","-");
$form_client[] = $form->createElement(
    "text","cd2","",
    "size=\"4\" maxLength=\"4\"
    style=\"$g_form_style\"
    onChange=\"javascript:Button_Submit('hdn_client_search_flg','#','true')\"
    $g_form_option"
);
$form_client[] = $form->createElement(
    "text","name","",
    "size=\"34\" $g_text_readonly");
$form->addGroup( $form_client, "form_client", "");

//直送先
//$select_value = Select_Get($db_con,'direct');
//$form->addElement('select', 'form_direct', "", $select_value,"class=\"Tohaba\"".$g_form_option_select);
//rev.1.3 テキスト入力へ変更
$form_direct[] = $form->createElement(
    "text","cd","","size=\"4\" maxLength=\"4\"
     style=\"$g_form_style\"
     onChange=\"javascript:Button_Submit('hdn_direct_search_flg','#','true')\"
     $g_form_option"
);
$form_direct[] = $form->createElement(
    "text","name","",
    "size=\"34\" $g_text_readonly");
$form_direct[] = $form->createElement(
    "text","claim","",
    "size=\"34\" $g_text_readonly");
$form->addGroup($form_direct, "form_direct_text", "");


//仕入倉庫
$where  = " WHERE";
$where .= "  shop_id = $_SESSION[client_id]";
$where .= "  AND";
$where .= "  nondisp_flg = 'f'";
$select_value = Select_Get($db_con,'ware', $where);
$form->addElement('select', 'form_ware', '', $select_value,$g_form_option_select);

//取引区分
$select_value = Select_Get($db_con,'trade_ord');
$form->addElement('select', 'form_trade', '', $select_value,$g_form_option_select);

//担当者
$select_value = Select_Get($db_con,'staff','',true);
$form->addElement('select', 'form_staff', '', $select_value,$g_form_option_select);

//通信欄（仕入先宛）
$form->addElement("textarea","form_note_your",""," rows=\"2\" cols=\"75\" $g_form_option_area");

// 第二通信欄（仕入先宛）
$form->addElement("textarea","form_note_your2",""," rows=\"4\" cols=\"75\" $g_form_option_area");

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
//各入力フラグ
$form->addElement("hidden", "hdn_client_search_flg");       //仕入先コード入力フラグ
$form->addElement("hidden", "hdn_direct_search_flg");       //rev.1.3 直送先コード入力フラグ
$form->addElement("hidden", "form_direct");                 //rev.1.3 直送先ID
$form->addElement("hidden", "hdn_goods_search_flg");        //商品コード入力フラグ
$form->addElement("hidden", "hdn_designated_date_flg");     //出荷可能数入力フラグ
$form->addElement("hidden", "hdn_sum_button_flg");          //合計ボタン押下フラグ
$form->addElement("hidden", "add_row_flg");                 //行追加ボタン押下フラグ
//処理ステータス
$form->addElement("hidden", "hdn_first_disp_flg");          //初期表示完了フラグ
//持ちまわる仕入先の情報
$form->addElement("hidden", "hdn_coax");                    //丸め区分
$form->addElement("hidden", "hdn_tax_franct");              //端数区分
$form->addElement("hidden", "hdn_client_id");               //得意先ID
$form->addElement("hidden", "hdn_order_id");                //変更時（発注ID）
$form->addElement("hidden", "hdn_ord_enter_day");           //登録日時
//表示行数に関係する情報
$form->addElement("hidden", "del_row");                     //削除行
$form->addElement("hidden", "max_row");                     //最大行数

#2009-09-15 hashimoto-y
for($i = 0; $i < $max_row; $i++){
    if(!in_array("$i", $del_history)){
        $form->addElement("hidden","hdn_discount_flg[$i]","","");
    }
}


/****************************/
//行削除処理
/****************************/
if($_POST["del_row"] != NULL){
    $now_form = NULL;
    //削除リストを取得
    $del_row = $_POST["del_row"];

    //削除履歴を配列にする。
    $del_history = explode(",", $del_row);

    //削除したデータの商品ID等にNULLをセット
    for($i = 0; $i < count($del_history); $i++){
        $goods_id[$del_histoty[$i]]     = null;
        $stock_num[$del_history[$i]]    = null;
        $stock_manage[$del_history[$i]] = null;
        $name_change[$del_history[$i]]  = null;
    }
    //削除した行数
    $del_num     = count($del_history)-1;
}

/****************************/
//行数追加
/****************************/
if($_POST["add_row_flg"]== 'true'){
    //最大行に、＋５する
    $max_row = $_POST["max_row"]+5;

    //行数追加フラグをクリア
    $add_row_data["add_row_flg"] = "";
    $form->setConstants($add_row_data);
}

/****************************/
//各処理判定
/****************************/
//仕入先が入力された場合、仕入先検索フラグにtrueをセット
$client_input_flg = ($_POST["hdn_client_search_flg"] == 'true')? true : false;       //仕入先入力判定フラグ

//rev.1.3 直送先が入力された場合、直送先検索フラグにtrueをセット
$direct_input_flg = ($_POST["hdn_direct_search_flg"] == 'true') ? true : false;      //直送先入力判定フラグ

//商品が選択された場合、商品検索フラグにtrueをセット
$goods_search_flg = ($_POST["hdn_goods_search_flg"] != NULL)? true : false;       //商品検索フラグ

//出荷可能数が変更された場合、出荷可能数変更フラグにtureをセット
$designated_date_flg = ($_POST["hdn_designated_date_flg"] == 't' && ereg("^[0-9]+$", $_POST["form_designated_date"]))? true : false;  //出荷可能数変更フラグ

//合計ボタンが押下された場合
$sum_button_flg = ($_POST["hdn_sum_button_flg"] == 't')? true : false;            //合計ボタン押下フラグ

//変更の場合（ord_idがGetで渡ってきた場合）、変更フラグにtrueをセット
$update_flg = ($_GET["ord_id"] != NULL)? true : false;                            //発注変更フラグ
Get_Id_Check3($_GET["ord_id"]);
if($update_flg == false){
    $update_flg = ($_POST["hdn_order_id"] != null)? true : false;
    $get_order_id = $_POST["hdn_order_id"];
}

//在庫調整一覧と発注点警告から遷移してきた場合
$get_flg = (count($_GET["order_goods_id"]) > 0)? true : false;                    //在庫調整一覧と発注点警告からの遷移フラグ
Get_Id_Check3($_GET["order_goods_id"]);

//発注ボタンが押下された場合
$add_ord_flg = ($_POST["form_order_button"] == "発注確認画面へ")? true : false;   //発注完ボタン押下フラグ

//発注完了ボタンが押下された場合
$add_comp_flg = ($_POST["form_comp_button"] == "発注完了")? true : false;         //発注完了ボタン押下フラグ

//発注完了と伝票出力が押下された場合
$add_comp_slip_flg = ($_POST["form_slip_comp_button"] == "発注完了と発注書出力")? true : false; //発注完了と伝票出力ボタン押下フラグ

//在庫調整一覧と発注点警告から遷移してきた場合で、初期表示処理を完了している場合true
$first_disp_flg = ($_POST["hdn_first_disp_flg"] == true)? true : false;             //初期表示完了フラグ

/****************************/
//合計処理
/****************************/
//合計ボタン押下フラグがtrueの場合
if($sum_button_flg == true || $add_ord_flg == true){
    $buy_data   = $_POST["form_buy_amount"];

    //配列を初期化
    $price_data = NULL;
    $tax_div    = NULL;
    //課税区分
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

    $data = Total_Amount($price_data, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $db_con);

    //フォームに値セット
    $set_data["form_buy_money"]     = number_format($data[0]);
    $set_data["form_tax_money"]     = number_format($data[1]);
    $set_data["form_total_money"]   = number_format($data[2]);
    $set_data["hdn_sum_button_flg"] = "";
}

/****************************/
//仕入先入力処理
/****************************/
//仕入先検索フラグがtureの場合
if(true == $client_input_flg){
    //$client_cd = $_POST["form_client"]["cd"];       //得意先コード
    $client_cd1 = $_POST["form_client"]["cd1"];       //得意先コード
    $client_cd2 = $_POST["form_client"]["cd2"];       //得意先コード

    //指定された仕入先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id,";                        //仕入先ID
    $sql .= "   client_cname,";                      //仕入先名
    $sql .= "   coax,";                             //丸め区分
    $sql .= "   tax_franct,";                        //端数区分
    $sql .= "   buy_trade_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    //$sql .= "   client_cd1 = '$client_cd'";
    //$sql .= "   AND";
    //$sql .= "   client_div = '2'";
    $sql .= "   client_cd1 = '$client_cd1'";
    $sql .= "   AND";
    $sql .= "   client_cd2 = '$client_cd2'";
    $sql .= "   AND";
    $sql .= "   client_div = '3'";
    $sql .= "   AND";
    //$sql .= "   shop_gid = '$shop_gid'";
    if($_SESSION[group_kind] == "2"){
        $sql .= "   shop_id IN (".Rank_Sql().") ";
    }else{
        $sql .= "   shop_id = $shop_id";
    }

    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    $get_client_data_count = pg_num_rows($result);
    $get_client_data       = pg_fetch_array($result);

    //該当する仕入先があった場合のみ処理開始
    if($get_client_data_count > 0){
        //以降の処理で持ちまわる得意先ID
        $client_id = $get_client_data["client_id"];

        //抽出した仕入先の情報をセット
        $set_data = NULL;
        $set_data["hdn_client_id"]                  = $get_client_data["client_id"];    //仕入先ID
        $set_data["form_client"]["name"]            = $get_client_data["client_cname"];  //仕入先名
        $set_data["hdn_tax_franct"]                 = $get_client_data["tax_franct"];   //丸め区分
        $set_data["hdn_coax"]                       = $get_client_data["coax"];         //端数区分
        $set_data["form_trade"]                     = $get_client_data["buy_trade_id"];     //取引区分

        //該当する仕入先があるので、仕入先検索フラグを立てる
        //警告メッセージを初期化
        $client_search_flg = true;
        $warning = null;

    //該当するデータがなかった場合は、既に入力済みの商品データを全て初期化
    }else{
        $set_data = NULL;       //セットするデータの初期化
        $set_data["hdn_client_id"]                  = "";       //仕入先ID
        $set_data["form_client"]["name"]            = "";       //仕入先名

        //該当する仕入先がないので、仕入先検索フラグにはfalseをセット
        $client_search_flg = false;
        $warning = "仕入先を入力して下さい。";
    }

    if($get_flg != true){
	    for($i = 0; $i < $max_row; $i++){
            $set_data["hdn_goods_id"][$i]           = "";       //商品ID
            $set_data["form_goods_cd"][$i]          = "";       //商品CD
            $set_data["form_goods_name"][$i]        = "";       //商品名
            $set_data["hdn_stock_manage"][$i]       = "";       //在庫管理
            $set_data["hdn_name_change"][$i]        = "";       //品名変更
            $set_data["form_stock_num"][$i]         = "";       //実棚数
            $set_data["form_rstock_num"][$i]        = "";       //発注済数
            $set_data["form_rorder_num"][$i]        = "";       //出荷可能数
            $set_data["form_designated_num"][$i]    = "";       //引当数
            $set_data["form_buy_price"][$i]["i"]    = "";       //仕入単価（整数部）
            $set_data["form_buy_price"][$i]["d"]    = "";       //仕入単価（少数部）
            $set_data["hdn_tax_div"][$i]            = "";       //課税区分
            $set_data["form_in_num"][$i]            = "";       //入数
            $set_data["form_buy_amount"][$i]        = "";       //発注金額
            $set_data["form_order_num"][$i]         = "";       //発注数
            //aoyama-n 
            $set_data["hdn_discount_flg"][$i]       = "";       //値引フラグ

            $goods_id[$i]                           = "";       //商品ID
            $stock_num[$i]                          = "";       //実棚数(リンク用)
            $stock_manage[$i]                       = "";       //在庫管理（在庫数表示判定）
            $name_change[$i]                        = "";       //品名変更（品名変更付加判定）
        } 
	    $max_row = 5;
	    $set_data["max_row"]            = 5;
    }

	$set_data["del_row"]            = "";
	$set_data["form_buy_money"]     = "";
    $set_data["form_tax_money"]     = "";
    $set_data["form_total_money"]   = "";
	//削除行数
    unset($del_history);
    $del_history[] = NULL;
	$del_row = NULL;

    //仕入先検索フラグを初期化
    $set_data["hdn_client_search_flg"]          = "";                            

/****************************/
//発注点警告OR在庫照会から遷移してきた場合
/****************************/
//ゲットフラグがtrue && 仕入先検索フラグの場合
}elseif($get_flg == true && $first_disp_flg === false){

    //GETで渡ってきた商品ID
    $get_goods_id = $_GET["order_goods_id"];    

    //商品IDをカンマ区切りにする
//    $ary_get_goods_id = implode(',',$get_goods_id);

    //GETで渡ってきた出荷可能数
    $designated_date = (ereg("^[0-9]+$", $_GET["designated_date"]))? $_GET["designated_date"] : 0;

    //GETで渡ってきた配列内に、文字列ORNULLがあった場合はエラー
    for($i = 0; $i < count($get_goods_id); $i++){
        //正常な値の場合はカンマ区切りで連結
        if(ereg("^[0-9]+$", $get_goods_id[$i])){

            if($i > 0){
                $ary_get_goods_id .= ",";
            }

            $ary_get_goods_id .= $get_goods_id[$i];
        }else{
            header("Location:../top.php");
            exit;
        }
    }

    $sql  = "SELECT\n";
    $sql .= "   t_goods.goods_id,\n";
    $sql .= "   t_goods.name_change,\n";
    //$sql .= "   t_goods.stock_manage,\n";
    $sql .= "   t_goods_info.stock_manage,\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "   t_goods.goods_cd,\n";
//    $sql .= "   t_goods.goods_cname,\n";
    //$sql .= "   t_goods.goods_name,\n";
    $sql .= "     (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS goods_name, \n";    //正式名
    //$sql .= "   CASE t_goods.stock_manage\n";
    $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_stock.stock_num,0)\n";
    $sql .= "   END AS rack_num,\n";
    //$sql .= "   CASE t_goods.stock_manage\n";
    $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_stock_io.order_num,0)\n";
    $sql .= "   END AS on_order_num,\n";
    //$sql .= "   CASE t_goods.stock_manage\n";
    $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";
//    $sql .= "            - COALESCE(t_allowance_io.allowance_io_num,0)\n";
    $sql .= "   END AS allowance_total,\n";
    $sql .= "   COALESCE(t_stock.stock_num,0)\n";
    $sql .= "    + COALESCE(t_stock_io.order_num,0)\n";
//    $sql .= "    - (COALESCE(t_stock.rstock_num,0)\n";
    $sql .= "    - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,\n";
    $sql .= "   t_price.r_price,\n";
    $sql .= "   t_goods.tax_div,\n";
//    $sql .= "   t_goods_info.in_num\n";
    //aoyama-n 2009-09-08
    #$sql .= "   t_goods.in_num\n";
    $sql .= "   t_goods.in_num,\n";
    $sql .= "   t_goods.discount_flg\n";
    $sql .= " FROM\n";
    $sql .= "   t_goods\n";
    $sql .= "     INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
    $sql .= "       INNER JOIN\n";
    $sql .= "   t_price\n";
    $sql .= "   ON t_goods.goods_id = t_price.goods_id\n";

    $sql .= "       LEFT JOIN\n";

    //在庫数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock.goods_id,\n";
    $sql .= "       SUM(t_stock.stock_num)AS stock_num,\n";
    $sql .= "       SUM(t_stock.rstock_num)AS rstock_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock.shop_id =  $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "   GROUP BY t_stock.goods_id\n";
    $sql .= "   )AS t_stock\n";
    $sql .= "   ON t_goods.goods_id = t_stock.goods_id\n";

    $sql .= "       LEFT JOIN\n";

    //発注残数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num\n";
    $sql .= "            * CASE t_stock_hand.io_div\n";
    $sql .= "               WHEN 1 THEN 1\n";
    $sql .= "               WHEN 2 THEN -1\n";
    $sql .= "       END ) AS order_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = '3'\n";
    $sql .= "       AND\n";
    $sql .= "       t_stock_hand.shop_id =  $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "       AND\n";
//    $sql .= "       CURRENT_DATE <= t_stock_hand.work_day\n";
//    $sql .= "       AND\n"; 
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_stock_io\n";
    $sql .= "   ON t_goods.goods_id=t_stock_io.goods_id\n";

    $sql .= "       LEFT JOIN\n";

    //引当数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num\n";
    $sql .= "           * CASE t_stock_hand.io_div\n";
    $sql .= "               WHEN 1 THEN -1\n";
    $sql .= "               WHEN 2 THEN 1\n";
    $sql .= "           END ) AS allowance_io_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = '1'\n";
    $sql .= "       AND\n";
    $sql .= "       t_stock_hand.shop_id = $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "       AND\n";
//    $sql .= "       t_stock_hand.work_day > (CURRENT_DATE + $designated_date)\n";
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_allowance_io\n";
    $sql .= "   ON t_goods.goods_id = t_allowance_io.goods_id\n";

	//-- 20090616 追加
	// ソート条件を同じにするため、INNER JOIN追加
	$sql.=	"	INNER JOIN t_g_goods ON t_g_goods.g_goods_id = t_goods.g_goods_id \n"
		.	"	INNER JOIN t_product ON t_product.product_id = t_goods.product_id \n";
	//-------

	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       INNER JOIN\n";
    $sql .= "   t_goods_info\n";
    $sql .= "   ON t_goods.goods_id = t_goods_info.goods_id";
    $sql .= " WHERE\n";
    $sql .= "   t_goods.goods_id IN ($ary_get_goods_id)\n";
    $sql .= "   AND\n";
    $sql .= "   t_goods.public_flg = 't'\n";
    $sql .= "   AND\n";
    $sql .= "   t_price.rank_cd = '1'\n";
    $sql .= "   AND\n";
    //$sql .= "   t_goods.shop_gid = $shop_gid\n";
    if($_SESSION[group_kind] == "2"){
        $sql .= "   t_goods.shop_id IN (".Rank_Sql().") ";
    }else{
        $sql .= "   t_goods.shop_id = $_SESSION[client_id]";
    }

    $sql .= "   AND\n";
    //$sql .= "   t_price.shop_gid = $shop_gid\n";
    if($_SESSION[group_kind] == "2"){
        $sql .= "   t_price.shop_id IN (".Rank_Sql().") ";
    }else{
        $sql .= "   t_price.shop_id = $_SESSION[client_id]";
    }

    $sql .= "   AND\n";
    //$sql .= "   t_goods_info.shop_gid = $shop_gid\n";
//    if($_SESSION[group_kind] == "2"){
//        $sql .= "   t_goods_info.shop_id IN (".Rank_Sql().") ";
//    }else{
        $sql .= "   t_goods_info.shop_id = $_SESSION[client_id]";
//    }

	//-- 2009/06/16 改修No.13 追加
	// ソート追加(在庫照会と同じ順に)
	$sql.= 	" ORDER BY\n"
		. 	"	t_g_goods.g_goods_cd, \n"
		. 	"	t_product.product_cd, \n"
		. 	"	t_g_product.g_product_id, \n"
		. 	"	t_goods.goods_cd, \n"
		.	"	t_goods.attri_div \n";
	//------

    $sql .= ";\n";

    $result = Db_Query($db_con, $sql);
    Get_Id_Check($result);
    $get_data_count = pg_num_rows($result);

    $set_data = NULL;       //セットするデータの初期化

    //抽出したレコード数回ループ
    for($i = 0; $i < $get_data_count; $i++){
        //抽出したデータ
        $get_data = pg_fetch_array($result, $i);

        //抽出した単価データをセットする形式に変更
        //抽出した単価を変数にセット
        $goods_price        = $get_data["r_price"];      //仕入単価
        //単価を整数部と少数部に分ける
        $goods_price_data   = explode(".", $goods_price); 

        //抽出したデータを配列にセット
        $set_data["hdn_goods_id"][$i]          = $get_data["goods_id"];          //商品ID
        $set_data["form_goods_cd"][$i]         = $get_data["goods_cd"];          //商品コード
        $set_data["form_goods_name"][$i]       = $get_data["goods_name"];        //商品名
        $set_data["hdn_stock_manage"][$i]      = $get_data["stock_manage"];      //在庫管理
        $set_data["hdn_name_change"][$i]       = $get_data["name_change"];       //品名変更
        $set_data["form_stock_num"][$i]        = $get_data["rack_num"];          //実棚数
        $set_data["hdn_stock_num"][$i]         = $get_data["rack_num"];          //実棚数(hidden用)
        $order_num                             = $get_data["on_order_num"];      //発注済数
        $set_data["form_rorder_num"][$i]       = ($order_num != NULL)? $order_num : '-';
        $rstock_num                            = $get_data["allowance_total"];   //引当数
        $set_data["form_rstock_num"][$i]       = ($rstock_num != NULL)? $rstock_num : '0';
        $stock_total                           = $get_data["stock_total"];       //出荷可能数
        $set_data["form_designated_num"][$i]   = ($stock_total != NULL)? $stock_total : '0';
        $set_data["form_buy_price"][$i]["i"]   = $goods_price_data[0];           //仕入単価
        $set_data["form_buy_price"][$i]["d"]   = $goods_price_data[1];
        $set_data["hdn_tax_div"][$i]           = $get_data["tax_div"];           //課税区分
        $set_data["form_in_num"][$i]           = $get_data["in_num"];            //入数
		//-- 2009/06/17 改修おまけ 追加
		// デフォルトに0をセット
		$set_data["form_buy_amount"][$i] = 0;	//仕入金額
		//---

        //aoyama-n 2009-09-08
        $set_data["hdn_discount_flg"][$i]      = $get_data["discount_flg"];      //値引フラグ

        //以降の処理で持ちまわる値
        $stock_manage[$i]                      = $get_data["stock_manage"];      //在庫管理
        $stock_num[$i]                         = $get_data["rack_num"];          //在庫数
        $name_change[$i]                       = $get_data["name_change"];       //品名変更
        $goods_id[$i]                          = $get_data["goods_id"];          //商品ID
   }

    //最大行数をセット
    $max_row = $get_data_count;

    $set_data["hdn_goods_search_flg"]          = "";                             //商品コード入力フラグを初期化
    $set_data["hdn_first_disp_flg"]            = true;                           //初期表示完了フラグ

/*****************************/
//商品コード入力処理
/*****************************/
//商品検索フラグがtureの場合
}elseif(true == $goods_search_flg){
    $search_row = $_POST["hdn_goods_search_flg"];                       //商品検索行

    $goods_cd  = $_POST["form_goods_cd"][$search_row];                  //検索対象商品コード
    
    //出荷可能数を取得、空白の場合は０を代入
    $designated_date = ($_POST["form_designated_date"] != NULL && ereg("^[0-9]+$", $_POST["form_designated_date"]))? $_POST["form_designated_date"] : 0;

    $sql  = "SELECT\n";
    $sql .= "   t_goods.goods_id,\n";                                   //商品ID
    $sql .= "   t_goods.name_change,\n";                                //品名変更
    //$sql .= "   t_goods.stock_manage,\n";                               //在庫管理
    $sql .= "   t_goods_info.stock_manage,\n";                          //在庫管理 rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "   t_goods.goods_cd,\n";                                   //商品コード
    //$sql .= "   t_goods.goods_name,\n";                                 //商品名
    $sql .= "     (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS goods_name, \n";    //正式名
    //$sql .= "   CASE t_goods.stock_manage\n";                           //実棚数
    $sql .= "   CASE t_goods_info.stock_manage\n";                      //実棚数 rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_stock.stock_num,0)\n";       
    $sql .= "       END AS rack_num,\n";
    //$sql .= "   CASE t_goods.stock_manage\n";                           //発注済数
    $sql .= "   CASE t_goods_info.stock_manage\n";                         //発注済数 rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_stock_io.order_num,0)\n";
    $sql .= "   END AS on_order_num,\n";
    //$sql .= "   CASE t_goods.stock_manage\n";                           //引当数
    $sql .= "   CASE t_goods_info.stock_manage\n";                         //引当数 rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";
//    $sql .= "        - COALESCE(t_allowance_io.allowance_io_num,0)\n";
    $sql .= "   END AS allowance_total,\n";
    $sql .= "   COALESCE(t_stock.stock_num,0)\n";                       //出荷可能数
    $sql .= "       + COALESCE(t_stock_io.order_num,0)\n";
//    $sql .= "       - (COALESCE(t_stock.rstock_num,0)\n";
    $sql .= "       - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,\n";
    $sql .= "   t_price.r_price,\n";                                    //仕入単価
    $sql .= "   t_goods.tax_div,\n";                                    //課税区分
//    $sql .= "   t_goods_info.in_num\n";
    //aoyama-n 2009-09-08
    #$sql .= "   t_goods.in_num\n";
    $sql .= "   t_goods.in_num,\n";
    $sql .= "   t_goods.discount_flg\n";
    $sql .= " FROM\n";
    $sql .= "   t_goods \n";
    $sql .= "       INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
    $sql .= "       INNER JOIN \n";
    $sql .= "   t_price\n";
    $sql .= "   ON t_goods.goods_id = t_price.goods_id\n";
    $sql .= "      LEFT JOIN\n";

    //在庫数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock.goods_id,\n";
    $sql .= "       SUM(t_stock.stock_num)AS stock_num, \n";
	$sql .= "       SUM(t_stock.rstock_num)AS rstock_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock.shop_id = $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "       GROUP BY t_stock.goods_id\n";
    $sql .= "   )AS t_stock\n";
    $sql .= "   ON t_goods.goods_id = t_stock.goods_id\n";

    $sql .= "       LEFT JOIN \n";

    //発注残数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num\n";
    $sql .= "               * CASE t_stock_hand.io_div\n";
    $sql .= "                    WHEN 1 THEN 1\n";
    $sql .= "                    WHEN 2 THEN -1\n";
    $sql .= "       END ) AS order_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = '3' \n";
    $sql .= "       AND\n";
    $sql .= "       t_stock_hand.shop_id =  $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "       AND\n";
//    $sql .= "       CURRENT_DATE <= t_stock_hand.work_day \n";
//    $sql .= "       AND\n"; 
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_stock_io\n";
    $sql .= "   ON t_goods.goods_id=t_stock_io.goods_id\n";

    $sql .= "      LEFT JOIN \n";
    //引当数
    $sql .= "   (SELECT\n";
    $sql .= "        t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div\n";
    $sql .= "                        WHEN 1 THEN -1\n";
    $sql .= "                        WHEN 2 THEN 1\n";
    $sql .= "       END ) AS allowance_io_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = '1'\n";
    $sql .= "       AND\n";
    $sql .= "       t_stock_hand.shop_id = $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "       AND\n";
//    $sql .= "       t_stock_hand.work_day > (CURRENT_DATE + $designated_date)\n";
    $sql .= "       t_stock_hand.work_day  <= (CURRENT_DATE + $designated_date)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_allowance_io\n";
    $sql .= "   ON t_goods.goods_id = t_allowance_io.goods_id\n";

	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       INNER JOIN\n";
    $sql .= "   t_goods_info\n";
    $sql .= "   ON t_goods.goods_id = t_goods_info.goods_id\n";

    $sql .= " WHERE\n";
    $sql .= "   t_goods.goods_cd = '$goods_cd'\n";
    $sql .= "   AND\n";
    $sql .= "   t_goods.public_flg = 't'\n";
    $sql .= "   AND\n";
    $sql .= "   t_goods.compose_flg = 'f'\n";
    $sql .= "   AND\n";
    $sql .= "   t_goods.state <> '2'\n";
    $sql .= "   AND\n";
    $sql .= "   t_goods.accept_flg = '1'\n";
    $sql .= "   AND\n";
    $sql .= "   t_price.rank_cd = '1'\n";
    $sql .= "   AND\n";
    //$sql .= "   t_goods.shop_gid = $shop_gid\n";
    if($_SESSION[group_kind] == "2"){
        $sql .= "   t_goods.shop_id IN (".Rank_Sql().") ";
    }else{
        $sql .= "   t_goods.shop_id = $_SESSION[client_id]";
    }

    $sql .= "   AND\n";
    //$sql .= "   t_price.shop_gid = $shop_gid\n";
    if($_SESSION[group_kind] == "2"){
        $sql .= "   t_price.shop_id IN (".Rank_Sql().") ";
    }else{
        $sql .= "   t_price.shop_id = $_SESSION[client_id]";
    }

	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "   AND\n";
    //$sql .= "   t_goods_info.shop_gid = $shop_gid\n";
//    if($_SESSION[group_kind] == "2"){
//        $sql .= "   t_goods_info.shop_id IN (".Rank_Sql().") ";
//    }else{
        $sql .= "   t_goods_info.shop_id = $_SESSION[client_id]";
//    }

    $sql .= ";\n";

    $result = Db_Query($db_con, $sql);
    $get_goods_data_count   = pg_num_rows($result);
    $get_goods_data         = pg_fetch_array($result);

    //上記のSQLで該当するレコードがあった場合(正当な商品コードが入力された場合)
    if($get_goods_data_count == 1){
        //抽出した単価データをセットする形式に変更
        //抽出した単価を変数にセット
        $goods_price        = $get_goods_data["r_price"];      //仕入単価
        //単価を整数部と少数部に分ける
        $goods_price_data   = explode(".", $goods_price); 

        //すでに発注数が入力されていた場合、金額を再計算する
        //発注数入力判定

        if($_POST["form_order_num"][$search_row] != NULL){

            //仕入金額（税抜き)
            $buy_amount = bcmul($goods_price, $_POST["form_order_num"][$search_row],2);
            $buy_amount = Coax_Col($coax, $buy_amount);
        }

        //抽出した商品のデータをセット
        $set_data = NULL;       //セットするデータの初期化
        $set_data["hdn_goods_id"][$search_row]          = $get_goods_data["goods_id"];          //商品ID
        $set_data["form_goods_name"][$search_row]       = $get_goods_data["goods_name"];        //商品名
        $set_data["hdn_stock_manage"][$search_row]      = $get_goods_data["stock_manage"];      //在庫管理
        $set_data["hdn_name_change"][$search_row]       = $get_goods_data["name_change"];       //品名変更
        $set_data["form_stock_num"][$search_row]        = $get_goods_data["rack_num"];          //実棚数
        $set_data["hdn_stock_num"][$search_row]         = $get_goods_data["rack_num"];          //実棚数(hidden用)
        $order_num                                      = $get_goods_data["on_order_num"];      //発注済数
        $set_data["form_rorder_num"][$search_row]       = ($order_num != NULL)? $order_num : '-';
        $rstock_num                                     = $get_goods_data["allowance_total"];   //引当数
        $set_data["form_rstock_num"][$search_row]       = ($rstock_num != NULL)? $rstock_num : '0';
        $stock_total                                    = $get_goods_data["stock_total"];       //出荷可能数
        $set_data["form_designated_num"][$search_row]   = ($stock_total != NULL)? $stock_total : '0';
        $set_data["form_buy_price"][$search_row]["i"]   = $goods_price_data[0];                 //仕入単価
        $set_data["form_buy_price"][$search_row]["d"]   = $goods_price_data[1];
        $set_data["hdn_tax_div"][$search_row]           = $get_goods_data["tax_div"];           //課税区分
        $set_data["form_in_num"][$search_row]           = $get_goods_data["in_num"];            //入数
        $set_data["form_buy_amount"][$search_row]       = number_format($buy_amount);           //仕入金額
        //aoyama-n 2009-09-08
        $set_data["hdn_discount_flg"][$search_row]      = $get_goods_data["discount_flg"];      //値引フラグ


        //以降の処理で持ちまわるデータ
        $goods_id[$search_row]                          = $get_goods_data["goods_id"];          //商品ID
        $stock_num[$search_row]                         = $get_goods_data["rack_num"];          //実棚数(リンク用)
        $stock_manage[$search_row]                      = $get_goods_data["stock_manage"];      //在庫管理（在庫数表示判定）
        $name_change[$search_row]                       = $get_goods_data["name_change"];       //品名変更（品名変更付加判定）

    //不正な値が入力された場合
    }else{
        //抽出した商品のデータをセット
        //セットするデータの初期化
        $set_data = NULL;       //セットするデータの初期化
        $set_data["hdn_goods_id"][$search_row]          = "";       //商品ID
        $set_data["form_goods_name"][$search_row]       = "";       //商品名
        $set_data["hdn_stock_manage"][$search_row]      = "";       //在庫管理
        $set_data["hdn_name_change"][$search_row]       = "";       //品名変更
        $set_data["form_stock_num"][$search_row]        = "";       //実棚数
        $set_data["form_rstock_num"][$search_row]       = "";       //発注済数
        $set_data["form_rorder_num"][$search_row]       = "";       //出荷可能数
        $set_data["form_designated_num"][$search_row]   = "";       //引当数
        $set_data["form_buy_price"][$search_row]["i"]   = "";       //仕入単価（整数部）
        $set_data["form_buy_price"][$search_row]["d"]   = "";       //仕入単価（少数部）
        $set_data["hdn_tax_div"][$search_row]           = "";       //課税区分
        $set_data["form_in_num"][$search_row]           = "";       //入数
        $set_data["form_buy_amount"][$search_row]       = "";       //発注金額
        $set_data["form_stock_num"][$search_row]        = "";       //実棚数
        $set_data["hdn_stock_num"][$search_row]         = "";       //実棚数(hidden用)
        $set_data["form_order_num"][$search_row]        = "";       //発注数
        //aoyama-n 2009-09-08
        $set_data["hdn_discount_flg"][$search_row]      = "";       //値引フラグ	

        $goods_id[$search_row]                          = null;     //商品ID
        $stock_num[$search_row]                         = null;     //実棚数(リンク用)
        $stock_manage[$search_row]                      = null;     //在庫管理（在庫数表示判定）
        $name_change[$search_row]                       = null;     //品名変更（品名変更付加判定）
    }

    //商品入力フラグを初期化
    $set_data["hdn_goods_search_flg"] = "";

/****************************/
//出荷可能数再計算処理
/****************************/
//出荷可能数変更フラグがtureの場合
}elseif(true === $designated_date_flg){

    //入力された出荷予定日
    $designated_date = $_POST["form_designated_date"];

    //入力されている商品の商品ID
    $ary_goods_id = $_POST["hdn_goods_id"];

    //表示されている商品入力行数分ループ
    for($i = 0; $i < $max_row; $i++){
        //商品が入力されている行のみ処理開始
        if($ary_goods_id[$i] != NULL){
            $sql  = "SELECT\n";
            $sql .= "   t_goods.goods_id,\n";
            $sql .= "   t_goods.name_change,\n";
            //$sql .= "   t_goods.stock_manage,\n";
            $sql .= "   t_goods_info.stock_manage,\n";	//rev.1.3 ショップごとに在庫管理フラグ
            $sql .= "   t_goods.goods_cd,\n";
            $sql .= "   t_goods.goods_name,\n";
            //$sql .= "   CASE t_goods.stock_manage\n";
            $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
            $sql .= "       WHEN 1 THEN COALESCE(t_stock.stock_num,0)\n";
            $sql .= "   END AS rack_num,\n";
            //$sql .= "   CASE t_goods.stock_manage\n";
            $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
            $sql .= "       WHEN 1 THEN COALESCE(t_stock_io.order_num,0)\n";
            $sql .= "    END AS on_order_num,\n";
            //$sql .= "   CASE t_goods.stock_manage\n";
            $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
            $sql .= "       WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";
//            $sql .= "                    - COALESCE(t_allowance_io.allowance_io_num,0)\n";
            $sql .= "   END AS allowance_total,\n";
            $sql .= "   COALESCE(t_stock.stock_num,0)\n";
            $sql .= "    + COALESCE(t_stock_io.order_num,0)\n";
//            $sql .= "    - (COALESCE(t_stock.rstock_num,0)\n";
            $sql .= "    - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,\n";
            $sql .= "   t_price.r_price,\n";
            $sql .= "   t_goods.tax_div\n";
            $sql .= " FROM\n";
            $sql .= "   t_goods\n";
            $sql .= "       INNER JOIN\n";
            $sql .= "   t_price\n";
            $sql .= "   ON t_goods.goods_id = t_price.goods_id\n";
            $sql .= "   LEFT JOIN\n";

            //在庫数
            $sql .= "   (SELECT\n";
            $sql .= "       t_stock.goods_id,\n";
            $sql .= "       SUM(t_stock.stock_num)AS stock_num,\n";
            $sql .= "       SUM(t_stock.rstock_num)AS rstock_num\n";
            $sql .= "   FROM\n";
            $sql .= "       t_stock\n";
            $sql .= "           INNER JOIN\n";
            $sql .= "       t_ware\n";
            $sql .= "       ON t_stock.ware_id = t_ware.ware_id\n";
            $sql .= "   WHERE\n";
            $sql .= "        t_stock.shop_id = $shop_id\n";
            $sql .= "        AND\n";
            $sql .= "        t_ware.count_flg = 't'\n";
            $sql .= "   GROUP BY t_stock.goods_id\n";
            $sql .= "   )AS t_stock\n";
            $sql .= "   ON t_goods.goods_id = t_stock.goods_id\n";

            $sql .= "       LEFT JOIN\n";

            //発注残数
            $sql .= "   (SELECT\n";
            $sql .= "       t_stock_hand.goods_id,\n";
            $sql .= "       SUM(t_stock_hand.num\n";
            $sql .= "           * CASE t_stock_hand.io_div\n";
            $sql .= "               WHEN 1 THEN 1\n";
            $sql .= "               WHEN 2 THEN -1\n";
            $sql .= "       END ) AS order_num\n";
            $sql .= "   FROM\n";
            $sql .= "       t_stock_hand\n";
            $sql .= "           INNER JOIN\n";
            $sql .= "       t_ware\n";
            $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
            $sql .= "   WHERE\n";
            $sql .= "       t_stock_hand.work_div = 3\n";
            $sql .= "       AND\n";
            $sql .= "       t_stock_hand.shop_id = $shop_id\n";
            $sql .= "       AND\n";
            $sql .= "       t_ware.count_flg = 't'\n";
            $sql .= "       AND\n";
//            $sql .= "       CURRENT_DATE <= t_stock_hand.work_day\n";
//            $sql .= "       AND\n";
            $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
            $sql .= "   GROUP BY t_stock_hand.goods_id\n";
            $sql .= "   ) AS t_stock_io\n";
            $sql .= "   ON t_goods.goods_id=t_stock_io.goods_id\n";

            $sql .= "       LEFT JOIN \n";

            //引当数
            $sql .= "   (SELECT\n";
            $sql .= "       t_stock_hand.goods_id,\n";
            $sql .= "       SUM(t_stock_hand.num\n";
            $sql .= "            * CASE t_stock_hand.io_div\n";
            $sql .= "               WHEN 1 THEN -1\n";
            $sql .= "               WHEN 2 THEN 1\n";
            $sql .= "       END ) AS allowance_io_num\n";
            $sql .= "   FROM\n";
            $sql .= "       t_stock_hand\n";
            $sql .= "           INNER JOIN\n";
            $sql .= "       t_ware\n";
            $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
            $sql .= "   WHERE\n";
            $sql .= "       t_stock_hand.work_div = 1\n";
            $sql .= "       AND\n";
            $sql .= "       t_stock_hand.shop_id = $shop_id\n";
            $sql .= "       AND\n";
            $sql .= "       t_ware.count_flg = 't'\n";
            $sql .= "       AND\n";
//            $sql .= "       t_stock_hand.work_day > (CURRENT_DATE + $designated_date)\n";
            $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + $designated_date)\n";
            $sql .= "   GROUP BY t_stock_hand.goods_id\n";
            $sql .= "   ) AS t_allowance_io\n";
            $sql .= "   ON t_goods.goods_id = t_allowance_io.goods_id\n";

			$sql .= "    INNER JOIN t_goods_info ON t_goods.goods_id = t_goods_info.goods_id \n";    //rev.1.3 ショップごとに在庫管理フラグ

            $sql .= " WHERE\n";
            $sql .= "   t_goods.goods_id = $goods_id[$i]\n";
            $sql .= "   AND\n";
            $sql .= "   t_goods.public_flg = 't'\n";
            $sql .= "   AND\n";
            $sql .= "   t_price.rank_cd = '1'\n";

			//rev.1.3 ショップごとに在庫管理フラグ
            $sql .= "    AND \n";
            $sql .= "    t_goods_info.shop_id = $shop_id \n";

            $sql .= ";\n";  

            $result = Db_Query($db_con, $sql);
            $get_designated_data = pg_fetch_array($result);

            //抽出した商品のデータをセット
            $set_data = NULL;       //セットするデータの初期化
//            $set_data["hdn_goods_id"][$i]           = $get_designated_data["goods_id"];          //商品ID
//            $set_data["form_goods_name"][$i]        = $get_designated_data["goods_name"];        //商品名
//            $set_data["hdn_stock_manage"][$i]       = $get_designated_data["stock_manage"];      //在庫管理
//            $set_data["hdn_name_change"][$i]        = $get_designated_data["name_change"];       //品名変更
            $set_data["form_stock_num"][$i]         = $get_designated_data["stock_num"];         //実棚数
            $order_num      = $get_designated_data["on_order_num"];                              //発注済数
            $set_data["form_rorder_num"][$i]        = ($order_num != NULL)? $order_num : '-';
            $rstock_num     = $get_designated_data["allowance_total"];                           //引当数
            $set_data["form_rstock_num"][$i]        = ($rstock_num != NULL)? $rstock_num : '0';
            $stock_total    = $get_designated_data["stock_total"];                               //出荷可能数
            $set_data["form_designated_num"][$i]    = ($stock_total != NULL)? $stock_total : '0';
//            $set_data["form_buy_price"][$i]["i"]    = $goods_price_data[0];                      //仕入単価
//            $set_data["form_buy_price"][$i]["d"]    = $goods_price_data[1];
//            $set_data["hdn_tax_div"][$i]            = $get_designated_data["tax_div"];           //課税区分
//            $set_data["form_in_num"][$i]            = $get_designated_data["in_num"];            //入数
//            $set_data["form_buy_amount"][$i]        = number_format($buy_amount);                //仕入金額

            //以降の処理で持ちまわるデータ
//            $goods_id[$i]                           = $get_designated_data["goods_id"];          //商品ID              
            $stock_num[$i]                          = $get_designated_data["rack_num"];          //実棚数(リンク用)
//            $stock_manage[$i]                       = $get_designated_data["stock_manage"];      //在庫管理（在庫数表示判定）
//            $name_change[$i]                        = $get_designated_data["name_change"];       //品名変更（品名変更付加判定）
        }
        //出荷可能数入力フラグを初期化
        $set_data["hdn_designated_date_flg"] = "";
    }

//--------------------------//
// rev.1.3 直送先入力処理
//--------------------------//
//直送先検索フラグがtrueの場合
}elseif($direct_input_flg){
	$direct_cd = $_POST["form_direct_text"]["cd"];

	//指定された直送先の情報を抽出
	$sql  = "SELECT \n";
	$sql .= "    direct_id, \n";            //直送先ID
	$sql .= "    direct_cd, \n";            //直送先コード
	$sql .= "    direct_name, \n";          //直送先名
	$sql .= "    direct_cname, \n";         //略称
	$sql .= "    t_client.client_cname \n"; //請求先
	$sql .= "FROM \n";
	$sql .= "    t_direct \n";
	$sql .= "    LEFT JOIN t_client ON t_direct.client_id = t_client.client_id \n";
	$sql .= "WHERE \n";
	$sql .= "    t_direct.shop_id = $shop_id \n";
	$sql .= "    AND \n";
	$sql .= "    t_direct.direct_cd = '$direct_cd' \n";
	$sql .= ";";

    $result = Db_Query($db_con, $sql);
    $get_direct_data_count = pg_num_rows($result);
    $get_direct_data       = pg_fetch_array($result);

    //該当する直送先があった場合のみ処理開始
    if($get_direct_data_count > 0){
        //抽出した直送先の情報をセット
        $set_data = NULL;
        $set_data["form_direct"]				= $get_direct_data["direct_id"];	//直送先ID
        $set_data["form_direct_text"]["name"]	= $get_direct_data["direct_cname"];	//直送先名略称
        $set_data["form_direct_text"]["claim"]	= $get_direct_data["client_cname"];	//請求先

    //該当するデータがなかった場合は、既に入力データを全て初期化
    }else{
        $set_data = NULL;
        $set_data["form_direct"]                = "";	//直送先ID
        $set_data["form_direct_text"]["cd"]     = "";   //直送先コード
        $set_data["form_direct_text"]["name"]   = "";   //直送先名略称
        $set_data["form_direct_text"]["claim"]  = "";   //請求先
	}

    //直送先検索フラグを初期化
    $set_data["hdn_direct_search_flg"]          = "";

}


/****************************/
//値検証
/****************************/
//発注確認へボタン押下フラグがtrue
//  OR
//発注完了ボタン押下フラグがtrue
//  OR
//発注完了と発注書出力ボタン押下フラグがtrue
//の場合
if($add_ord_flg == true || $add_comp_flg == true || $add_comp_slip_flg == true){

    //ルール作成
    //仕入先
    //必須チェック
    $form->addGroupRule('form_client', array(
            //'cd' => array(
            //        array('正しい仕入先コードを入力してください。', 'required')
            //),
            'cd1' => array(
                    array('正しい仕入先コードを入力してください。', 'required')
            ),
            'cd2' => array(
                    array('正しい仕入先コードを入力してください。', 'required')
            ),
            'name' => array(
                    array('正しい仕入先コードを入力してください。','required')
            )
    ));

    //出荷可能数
    $form->addRule("form_designated_date","発注済数と引当数を考慮する日数は半角数値のみです。","regex", '/^[0-9]+$/');

    //発注日
    //●必須チェック
    $form->addGroupRule('form_order_day', array(
            'y' => array(
                    array('正しい発注日を入力してください。', 'required'),
                    array('正しい発注日を入力してください。', 'numeric')
            ),
            'm' => array(
                    array('正しい発注日を入力してください。','required'),
                    array('正しい発注日を入力してください。','numeric')
            ),
            'd' => array(
                    array('正しい発注日を入力してください。','required'),
                    array('正しい発注日を入力してください。','numeric')
            )
    ));
    //システム開始日


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

    //入荷予定日
    //●半角チェック
    $form->addGroupRule('form_arrival_day', array(
            'y' => array(
                    array('正しい入荷予定日を入力してください。', 'numeric')
            ),      
            'm' => array(
                    array('正しい入荷予定日を入力してください。','numeric')
            ),      
            'd' => array(
                    array('正しい入荷予定日を入力してください。','numeric')
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


    //通信欄   //●文字数チェック
    $form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
    $form->addRule("form_note_your","通信欄（仕入先宛）は95文字以内です。","mb_maxlength","95");

    // 第二通信欄
    // ●文字数チェック
    $form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
    $form->addRule("form_note_your2", "第二通信欄（仕入先宛）は200文字以内です。", "mb_maxlength", "200");

    //POST取得
    $designated_date    = $_POST["form_designated_date"];               //出荷可能数
    $order_no           = $_POST["form_order_no"];                      //発注番号
    $order_day["y"]     = $_POST["form_order_day"]["y"];                //発注日（年）
    $order_day["m"]     = $_POST["form_order_day"]["m"];                //発注日（月）
    $order_day["d"]     = $_POST["form_order_day"]["d"];                //発注日（日）
    $hope_day["y"]      = $_POST["form_hope_day"]["y"];                 //希望納期（年）
    $hope_day["m"]      = $_POST["form_hope_day"]["m"];                 //希望納期（月）
    $hope_day["d"]      = $_POST["form_hope_day"]["d"];                 //希望納期（日）
    $arrival_day["y"]   = $_POST["form_arrival_day"]["y"];              //入荷予定日（年）
    $arrival_day["m"]   = $_POST["form_arrival_day"]["m"];              //入荷予定日（月）
    $arrival_day["d"]   = $_POST["form_arrival_day"]["d"];              //入荷予定日（日）
    $trade              = $_POST["form_trade"];                         //取引区分
    $direct             = $_POST["form_direct"];
    $trans              = $_POST["form_trans"];                         //運送業者
    $staff              = $_POST["form_staff"];                         //担当者ID
    $ware               = $_POST["form_ware"];                          //倉庫ID 
    $note_your          = $_POST["form_note_your"];                     //備考
    $note_your2         = $_POST["form_note_your2"];                    // 第二通信欄
    //$client_cd          = $_POST["form_client"]["cd"];                  //仕入先コード
    $client_cd1         = $_POST["form_client"]["cd1"];                  //仕入先コード
    $client_cd2         = $_POST["form_client"]["cd2"];                  //仕入先コード
    $client_id          = $_POST["hdn_client_id"];

    //運送業者のグリーンフラグを抽出
    if($trans != null){
        $sql  = "SELECT";
        $sql .= "   green_trans";
        $sql .= " FROM";    
        $sql .= "   t_trans";   
        $sql .= " WHERE";   
        $sql .= "   trans_id = $trans";     
        $sql .= ";";

        $result = Db_Query($db_con, $sql);    
        $trans_flg = pg_fetch_result($result ,0,0);
    }else{
        $trans     = null;
        $trans_flg = 'f';   
    }


    //仕入先チェック
    $sql  = "SELECT";
    $sql .= "   COUNT(client_id) ";
    $sql .= "FROM";
    $sql .= "   t_client ";
    $sql .= "WHERE";
    //$sql .= "   client_cd1 = '$client_cd'";
    $sql .= "   client_cd1 = '$client_cd1'";
    $sql .= "   AND";
    $sql .= "   client_cd2 = '$client_cd2'";
    $sql .= "   AND";
    $sql .= "   client_id = $client_id";
    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    $client_num = pg_fetch_result($result, 0,0);

    if($client_num != 1){
        $form->setElementError("form_client", "仕入先情報取得前に 発注確認画面へボタン <br>が押されました。操作をやり直してください。");
    //}elseif($client_cd != null){
    }elseif($client_cd1 != null && $client_cd2 != null){
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
                                 'ord',                                         //区分
                                 //aoyama-n 2009-09-08
                                 #$db_con                                        //DBコネクト
                                 $db_con,                                        //DBコネクト
                                 null,
                                 null,
                                 null,
                                 null,
                                 $_POST[hdn_discount_flg]                        //値引フラグ
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

    //検証
    //エラーがない場合処理開始
    if($form->validate() && $err_flg != true){

        //発注データ登録処理
        //発注完了ボタン押下フラグがtrue
        //  OR
        //発注完了と発注書出力ボタン押下フラグがtrueの場合
        if($add_comp_flg == true || $add_comp_slip_flg == true){
            //日付を結合
            $hope_date      = $hope_day["y"]."-".$hope_day["m"]."-".$hope_day["d"];     //希望納期
            $order_date     = $order_day["y"]."-".$order_day["m"]."-".$order_day["d"];  //発注日
            $arrival_date   = $arrival_day["y"]."-".$arrival_day["m"]."-".$arrival_day["d"];  //発注日

            #2009-12-21 aoyama-n
            $tax_rate_obj->setTaxRateDay($order_date);
            $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

            //税抜合計、消費税金額を算出
            for($i = 0; $i < count($goods_id); $i++){
                $total_amount_data = Total_Amount($buy_amount, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $db_con);
            }

            Db_Query($db_con, "BEGIN;");

            //変更処理
            //変更フラグがtrueの場合
            if($update_flg == true){

                //発注が削除されていないかを確認
                $update_check_flg = Update_Check($db_con, "t_order_h", "ord_id", $get_order_id, $_POST["hdn_ord_enter_day"]);
                //既に削除されていた場合
                if($update_check_flg === false){ 
                    header("Location: ./1-3-103.php?ord_id=$get_order_id&output_flg=delete");
                    exit;   
                }       

                //仕入が完了されていないかを確認
                $finish_check_flg = Finish_Check($db_con, "t_order_h", "ord_id", $get_order_id);
                //既に完了していた場合
                if($finish_check_flg === false){
                    header("Location: ./1-3-103.php?ord_id=$get_order_id&output_flg=finish");
                    exit;
                }

                //発注ヘッダをアップデート
                $sql  = "UPDATE t_order_h SET\n";
                $sql .= "   ord_no = '$order_no',";                                             //発注番号
                $sql .= "   client_id = $client_id,";                                           //仕入先ID
                $sql .= ($direct != null) ? " direct_id = $direct,\n" : " direct_id = NULL, ";  //直送先ID
                $sql .= "   trade_id  = '$trade',\n";                                           //取引区分
                $sql .= "   green_flg = '$trans_flg',\n";                                       //グリーンフラグ
                $sql .= ($trans != null) ? " trans_id = '$trans',\n" : " trans_id = NULL, ";    //運送業者
                $sql .= "   ord_time  = '$order_date',\n";                                      //発注日
                //希望納期が入力された場合
                if($hope_date != '--'){
                    $sql .= "   hope_day = '$hope_date',\n";                                    //希望納期
                //希望納期が入力されなかった場合
                }else{
                    $sql .= "   hope_day = null,\n";
                }

                //入荷予定日が入力された場合
                if($arrival_date != '--'){
                    $sql .= "   arrival_day = '$arrival_date',\n";
                //入荷予定日が入力されなかった場合
                }else{
                    $sql .= "   arrival_day = null,\n";
                }
                $sql .= "   note_your = '$note_your',\n";               //通信欄
                $sql .= "   note_your2 = '$note_your2',\n";             // 第二通信欄
                $sql .= "   c_staff_id = $staff,\n";                    //担当者ID
                $sql .= "   ware_id = $ware,\n";                        //倉庫ID 
                $sql .= "   ord_staff_id = $staff_id,\n";               //発注者ID
                $sql .= "   net_amount = $total_amount_data[0],\n";     //税抜金額
                $sql .= "   tax_amount = $total_amount_data[1],\n";     //消費税額
                $sql .= "   client_cd1 = (SELECT client_cd1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_cd2 = (SELECT client_cd2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_name = (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_name2 = (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   c_shop_name1 = (SELECT shop_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   c_shop_name2 = (SELECT shop_name2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_post_no1 = (SELECT post_no1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_post_no2 = (SELECT post_no2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_address1 = (SELECT address1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_address2 = (SELECT address2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_address3 = (SELECT address3 FROM t_client WHERE client_id = $client_id), ";
                //$sql .= "   client_charger1 = (SELECT tel FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   client_charger1 = (SELECT charger1 FROM t_client WHERE client_id = $client_id), ";	//kajioka-h 2009/10/13
                $sql .= "   client_tel = (SELECT tel FROM t_client WHERE client_id = $client_id), ";
                $sql .= ($direct != null) ? " direct_name = (SELECT direct_name FROM t_direct WHERE direct_id = $direct), " : " direct_name = NULL, ";
                $sql .= ($direct != null) ? " direct_name2 = (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct), " : " direct_name2 = NULL, ";
                $sql .= ($direct != null) ? " direct_cname = (SELECT direct_cname FROM t_direct WHERE direct_id = $direct), " : " direct_cname = NULL, ";
                $sql .= ($direct != null) ? " direct_post_no1 = (SELECT post_no1 FROM t_direct WHERE direct_id = $direct), " : " direct_post_no1 = NULL, ";
                $sql .= ($direct != null) ? " direct_post_no2 = (SELECT post_no2 FROM t_direct WHERE direct_id = $direct), " : " direct_post_no2 = NULL, ";
                $sql .= ($direct != null) ? " direct_address1 = (SELECT address1 FROM t_direct WHERE direct_id = $direct), " : " direct_address1 = NULL, ";
                $sql .= ($direct != null) ? " direct_address2 = (SELECT address2 FROM t_direct WHERE direct_id = $direct), " : " direct_address2 = NULL, ";
                $sql .= ($direct != null) ? " direct_address3 = (SELECT address3 FROM t_direct WHERE direct_id = $direct), " : " direct_address3 = NULL, ";
                $sql .= ($direct != null) ? " direct_tel = (SELECT tel FROM t_direct WHERE direct_id = $direct), " : " direct_tel = NULL, ";
                $sql .= ($trans != null) ? " trans_name = (SELECT trans_name FROM t_trans WHERE trans_id = $trans), " : "trans_name = NULL, ";
                $sql .= "   ware_name = (SELECT ware_name FROM t_ware WHERE ware_id = $ware), ";
                $sql .= "   c_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $staff), ";
                $sql .= "   ord_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $staff_id), ";
                $sql .= "   client_cname = (SELECT client_cname FROM t_client WHERE client_id = $client_id),";
                $sql .= "   change_day = CURRENT_TIMESTAMP";
                $sql .= " WHERE";
                $sql .= "    ord_id = $get_order_id";            //発注ID
                $sql .= ";";

                $result = Db_Query($db_con, $sql);
                if($result === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }

                //発注データを削除
                $sql  = "DELETE FROM";
                $sql .= "    t_order_d";
                $sql .= " WHERE";
                $sql .= "    ord_id = $get_order_id";
                $sql .= ";";

                $result = Db_Query($db_con, $sql );
                if($result === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }

            //新規登録処理
            }else{
                //発注ヘッダテーブルに登録
                $sql  = "INSERT INTO t_order_h (\n";
                $sql .= "   ord_id,\n";             //発注ID
                $sql .= "   ord_no,\n";             //発注番号
                $sql .= "   client_id,\n";          //得意先ID
                $sql .= ($direct != null) ? " direct_id,\n" : null;          //直送先ID
                $sql .= "   trade_id,\n";           //取引区分
                $sql .= "   green_flg,\n";          //グリーンフラグ
                $sql .= ($trans != null) ? " trans_id,\n" : null;           //運送業者
                $sql .= "   ord_time,\n";           //発注日
                //希望納期に入力があった場合
                if($hope_date != '--'){
                    $sql .= "   hope_day,\n";       //希望納期
                }
                //入荷予定日に入力があった場合
                if($arrival_date !=  '--'){
                    $sql .= "   arrival_day,\n";    //入荷予定日
                } 
                $sql .= "   note_your,\n";            //通信欄
                $sql .= "   note_your2,\n";         // 第二通信欄
                $sql .= "   c_staff_id,\n";         //担当者ID
                $sql .= "   ware_id,\n";            //倉庫ID
                $sql .= "   ord_staff_id,\n";       //発注者ID
                $sql .= "   ps_stat,\n";            //処理状況
                $sql .= "   net_amount,\n";         //税抜金額
                $sql .= "   tax_amount,\n";         //消費税額
                //$sql .= "   shop_id,\n";            //ショップID
                $sql .= "   shop_id,\n";            //ショップID
                //$sql .= "   shop_gid\n";            //FCグループID
                $sql .= "   client_cd1, ";
                $sql .= "   client_cd2, ";
                $sql .= "   client_name, ";
                $sql .= "   client_name2, ";
                $sql .= "   client_post_no1, ";
                $sql .= "   client_post_no2, ";
                $sql .= "   client_address1, ";
                $sql .= "   client_address2, ";
                $sql .= "   client_address3, ";
                $sql .= "   client_charger1, ";
                $sql .= "   client_tel, ";
                if ($direct != null){
                    $sql .= "   direct_name, ";
                    $sql .= "   direct_name2, ";
                    $sql .= "   direct_cname, ";
                    $sql .= "   direct_post_no1, ";
                    $sql .= "   direct_post_no2, ";
                    $sql .= "   direct_address1, ";
                    $sql .= "   direct_address2, ";
                    $sql .= "   direct_address3, ";
                    $sql .= "   direct_tel, ";
                }
                $sql .= ($trans != null) ? " trans_name, " : null;
                $sql .= "   ware_name, ";
                $sql .= "   c_staff_name, ";
                $sql .= "   ord_staff_name, ";
                $sql .= "   my_client_name, ";
                $sql .= "   my_client_name2,";
                $sql .= "   send_date,";
                $sql .= "   client_cname, ";
                $sql .= "   c_shop_name1, ";
                $sql .= "   c_shop_name2 ";
                $sql .= " )VALUES(\n";
                $sql .= "   (SELECT COALESCE(MAX(ord_id), 0)+1 FROM t_order_h),";   //発注ID
                $sql .= "   '$order_no',\n";        //発注番号
                $sql .= "   $client_id,\n";         //仕入先ID
                $sql .= ($direct != null) ? " $direct,\n" : null;            //直送先ID
                $sql .= "   '$trade',\n";           //取引区分
                $sql .= "   '$trans_flg',\n";       //グリーンフラグ
                $sql .= ($trans != null) ? " $trans,\n" : null;             //運送業者ID
                $sql .= "   '$order_date',\n";       //発注日
                //希望納期に入力があった場合
                if($hope_date != '--'){
                    $sql .= "   '$hope_date',\n";       //希望納期
                }
                //入荷予定日に入力があった場合
                if($arrival_date != '--'){
                    $sql .= "   '$arrival_date',\n";    //入荷予定日
                }
                $sql .= "   '$note_your',\n";         //通信欄
                $sql .= "   '$note_your2',\n";      // 第二通信欄
                $sql .= "   $staff,\n";             //担当者
                $sql .= "   $ware,\n";              //倉庫ID
                $sql .= "   $staff_id,\n";          //発注者ID
                $sql .= "   '1',\n";                //処理状況
                $sql .= "   $total_amount_data[0],\n";
                $sql .= "   $total_amount_data[1],\n";
                //$sql .= "   $shop_id,\n";
                $sql .= "   $shop_id,\n";
                //$sql .= "   $shop_gid\n";
                $sql .= "   (SELECT client_cd1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT client_cd2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT post_no1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT post_no2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT address1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT address2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT address3 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT charger1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT tel      FROM t_client WHERE client_id = $client_id), ";
                $sql .= ($direct != null) ? " (SELECT direct_name FROM t_direct WHERE direct_id = $direct), "  : null;
                $sql .= ($direct != null) ? " (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct), " : null;
                $sql .= ($direct != null) ? " (SELECT direct_cname FROM t_direct WHERE direct_id = $direct), " : null;
                $sql .= ($direct != null) ? " (SELECT post_no1 FROM t_direct WHERE direct_id = $direct), "     : null;
                $sql .= ($direct != null) ? " (SELECT post_no2 FROM t_direct WHERE direct_id = $direct), "     : null;
                $sql .= ($direct != null) ? " (SELECT address1 FROM t_direct WHERE direct_id = $direct), "     : null;
                $sql .= ($direct != null) ? " (SELECT address2 FROM t_direct WHERE direct_id = $direct), "     : null;
                $sql .= ($direct != null) ? " (SELECT address3 FROM t_direct WHERE direct_id = $direct), "     : null;
                $sql .= ($direct != null) ? " (SELECT tel      FROM t_direct WHERE direct_id = $direct), "     : null;
                $sql .= ($trans != null) ? " (SELECT trans_name FROM t_trans WHERE trans_id = $trans), "       : null;
                $sql .= "   (SELECT ware_name FROM t_ware WHERE ware_id = $ware), ";
                $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $staff), ";
                $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $staff_id), ";
                $sql .= "   (SELECT shop_name FROM t_client WHERE client_id = $shop_id), ";
                $sql .= "   (SELECT shop_name2 FROM t_client WHERE client_id = $shop_id), ";
                $sql .= "   NOW(),";
                $sql .= "   (SELECT client_cname FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT shop_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "   (SELECT shop_name2 FROM t_client WHERE client_id = $client_id) ";
                $sql .= ");";

                $result = Db_Query($db_con, $sql);

                //発注番号が重複した場合は再度採番しなおす
                if($result === false){
                    $err_message = pg_last_error(); 
                    $err_format = "t_order_h_ord_no_key";

                    Db_Query($db_con, "ROLLBACK;");
                    if(strstr($err_message, $err_format) != false){
                        $error = "同時に発注を行ったため、発注NOが重複しました。もう一度発注をして下さい。";

                        //再度発注NOを取得する
                        $sql  = "SELECT ";
                        $sql .= "   MAX(ord_no)";
                        $sql .= " FROM";
                        $sql .= "   t_order_h";
                        $sql .= " WHERE";
                        $sql .= "   shop_id = $shop_id";
                        $sql .= ";";

                        $result = Db_Query($db_con, $sql);
                        $order_no = pg_fetch_result($result, 0 ,0);
                        $order_no = $order_no +1;
                        $order_no = str_pad($order_no, 8, 0, STR_PAD_LEFT);

                        $set_data["form_order_no"] = $order_no;

                        $duplicate_flg = true;
                    }else{
                        exit;
                    }
                }
            }

            //発注番号の重複が無かった場合、発注データを登録
            if($duplicate_flg != true){
                //入力された商品数分ループ
                for($i = 0; $i < count($goods_id); $i++){ 
                    //行
                    $line = $i + 1;

                    //仕入単価の整数部と小数部を結合
                    $price = $buy_price[$i]["i"].".".$buy_price[$i]["d"];    //仕入単価 

                    //仕入金額税抜き、を算出
                    $buy_amount = bcmul($price, $order_num[$i],2);
					$buy_amount = Coax_Col($coax,$buy_amount);

                    $sql  = "INSERT INTO t_order_d (\n";
                    $sql .= "   ord_d_id,\n";           //発注データID
                    $sql .= "   ord_id,\n";             //発注ID
                    $sql .= "   line,\n";               //行
                    $sql .= "   goods_id,\n";           //商品ID
                    $sql .= "   goods_name,\n";         //商品名
                    $sql .= "   num,\n";                //発注数
                    $sql .= "   tax_div,\n";            //課税区分
                    $sql .= "   buy_price,\n";          //仕入単価
                    $sql .= "   buy_amount,\n";         //仕入金額合計
                    $sql .= "   goods_cd, ";
                    $sql .= "   goods_cname, ";
                    $sql .= "   in_num ";
                    $sql .= ")VALUES(\n";
                    $sql .= "   (SELECT COALESCE(MAX(ord_d_id), 0)+1 FROM t_order_d),\n";
                    $sql .= "   (SELECT\n";
                    $sql .= "       ord_id\n";
                    $sql .= "   FROM\n";
                    $sql .= "       t_order_h\n";
                    $sql .= "   WHERE\n";
                    $sql .= "       ord_no = '$order_no'\n";
                    $sql .= "       AND\n";
                    $sql .= "       shop_id = $shop_id\n";
                    $sql .= "   ),\n";
                    $sql .= "   $line,\n";
                    $sql .= "   $goods_id[$i],\n";
                    $sql .= "   '$goods_name[$i]',\n";
                    $sql .= "   '$order_num[$i]',\n";
                    $sql .= "   '$tax_div[$i]',\n";
                    $sql .= "   $price,\n";
                    $sql .= "   $buy_amount, \n";
                    $sql .= "   (SELECT goods_cd FROM t_goods WHERE goods_id = $goods_id[$i]), ";
                    $sql .= "   (SELECT goods_cname FROM t_goods WHERE goods_id = $goods_id[$i]), ";
                    $sql .= "   (SELECT in_num FROM t_goods WHERE goods_id = $goods_id[$i]) ";
                    $sql .= ");\n";

                    $result = Db_Query($db_con, $sql);
                    if($result === false){
                        Db_Query($db_con, "ROLLBACK;");
                        exit;
                    }

                    //在庫受払テーブルに登録
                    //在庫管理する商品で直送先が選択されていない場合
//                    if($stock_manage[$i] != '2' ||  $direct == null){
//                    if($stock_manage[$i] != '2'){
//いかなる場合も受け払いテーブルには表示するように変更
                        $sql  = "INSERT INTO t_stock_hand (\n";
                        $sql .= "   goods_id,\n";
                        $sql .= "   enter_day,\n";
                        $sql .= "   work_day,\n";
                        $sql .= "   work_div,\n";
                        $sql .= "   client_id,\n";
                        $sql .= "   ware_id,\n";
                        $sql .= "   io_div,\n";
                        $sql .= "   num,\n";
                        $sql .= "   slip_no,\n";
                        $sql .= "   ord_d_id,\n";
                        $sql .= "   staff_id,\n";
                        $sql .= "   shop_id, \n";
                        $sql .= "   client_cname \n";
                        $sql .= " )VALUES(\n";
                        $sql .= "   $goods_id[$i],\n";
                        $sql .= "   NOW(),\n";
                        $sql .= "   '$order_date',\n";
                        //2006-07-19 kaji 作業区分が抜けてたのを追加
                        $sql .= "   '3',\n";
                        $sql .= "   $client_id,\n";
                        $sql .= "   $ware,\n";
                        $sql .= "   '1',";
                        $sql .= "   $order_num[$i],\n";
                        $sql .= "   '$order_no',\n";
                        $sql .= "   (SELECT\n";
                        $sql .= "       ord_d_id\n";
                        $sql .= "   FROM\n";
                        $sql .= "       t_order_d\n";
                        $sql .= "   WHERE\n";
                        $sql .= "       line = $line\n";
                        $sql .= "       AND\n";
                        $sql .= "       ord_id = (SELECT\n";
                        $sql .= "                   ord_id\n";
                        $sql .= "                FROM\n";
                        $sql .= "                   t_order_h\n";
                        $sql .= "                WHERE\n";
                        $sql .= "                   ord_no = '$order_no'";
                        $sql .= "                   AND\n";
                        $sql .= "                   shop_id = $shop_id";
                        $sql .= "               )\n";
                        $sql .= "   ),\n";
                        //2006-07-19 kaji スタッフIDの後のカンマ抜けを追加
                        $sql .= "   $staff_id,\n";
                        $sql .= "   $shop_id,\n";
                        $sql .= "   (SELECT client_cname FROM t_client WHERE client_id = $client_id)";
                        $sql .= ");";

                        $result = Db_Query($db_con, $sql);
                        if($result === false){
                            Db_Query($db_con, "ROLLBACK;");
                            exit;
                        }
//                    }
                }

                Db_Query($db_con, "COMMIT;");

                //発注確認画面へ遷移
                //Getで渡す値を取得
                $sql  = "SELECT";
                $sql .= "   ord_id";
                $sql .= " FROM";
                $sql .= "   t_order_h";
                $sql .= " WHERE";
                $sql .= "   ord_no = '$order_no'";
                $sql .= "   AND";
                $sql .= "   shop_id = $shop_id";
                $sql .= ";";

                $result = Db_Query($db_con, $sql);
                $order_id = pg_fetch_result($result,0,0);

                //発注完了ボタン押下フラグがtrueの場合
                if($add_comp_flg == true){
                    header("Location: ./1-3-103.php?ord_id=$order_id&output_flg=false");
                //発注完了と発注書出力ボタン押下フラグがtrueの場合
                }elseif($add_comp_slip_flg == true){
                    header("Location: ./1-3-103.php?ord_id=$order_id&output_flg=true");
                }
            }

        //発注確認へボタン押下フラグがtrueの場合
        }else{
            //フォームを固めるためのフリーズフラグ
            $freeze_flg = true;
        }
    }
/****************************/
//変更の処理
/****************************/
//変更フラグがtureの場合
}elseif(true === $update_flg 
        && 
        null == $get_order_id 
        && 
        true != $goods_search_flg 
        && 
        true != $sum_button_flg
        &&
        true != $client_search_flg
        &&
        true != $designated_date_flg
    ){
    $get_ord_id = $_GET["ord_id"];              //GETで取得した発注ID

    //発注ヘッダから下記を抽出
    $sql  = "SELECT\n";
    $sql .= "   t_order_h.ord_no,\n";                                       //発注番号
    $sql .= "   to_char(t_order_h.ord_time, 'YYYY-mm-dd') AS ord_time,\n";  //発注日
    $sql .= "   t_order_h.hope_day,\n";                                     //希望納期
    $sql .= "   t_order_h.arrival_day,\n";                                  //入荷予定日
    $sql .= "   t_order_h.trans_id,\n";                                     //運送業者
    $sql .= "   t_client.client_id,\n";                                     //仕入先ID
    $sql .= "   t_client.client_cd1,\n";                                    //仕入先コード
    $sql .= "   t_client.client_cd2,\n";                                    //仕入先コード
//    $sql .= "   t_client.client_name,\n";       //仕入先名
    $sql .= "   t_client.client_cname,\n";                                  //仕入先名
    $sql .= "   t_client.tax_franct,\n";                                    //端数区分
    $sql .= "   t_client.coax,\n";                                          //丸め区分
    $sql .= "   t_order_h.direct_id,\n";                                    //直送先ID
    $sql .= "   t_order_h.ware_id,\n";                                      //倉庫ID
    $sql .= "   t_order_h.trade_id,\n";                                     //取引区分コード
    $sql .= "   t_order_h.c_staff_id,\n";                                   //担当者ID
    $sql .= "   t_order_h.note_your,\n";                                    //通信欄
    $sql .= "   t_order_h.note_your2,\n";                                   // 第二通信欄
    $sql .= "   to_char(t_order_h.send_date, 'yyyy-mm-dd hh24:mi') AS send_date,\n";
    $sql .= "   t_order_h.enter_day\n";

	//rev.1.3 直送先テキスト化
	$sql .= ",\n";
    $sql .= "    t_direct.direct_cd, \n";		//直送先CD
    $sql .= "    t_order_h.direct_cname, \n";	//直送先略称
    $sql .= "    t_direct_claim.client_cname AS direct_claim \n";	//直送先請求先

    $sql .= " FROM\n";
    $sql .= "   t_order_h\n";
    $sql .= "       INNER JOIN\n";
    $sql .= "   t_client\n";
    $sql .= "   ON t_order_h.client_id = t_client.client_id\n";
	$sql .= "   LEFT JOIN t_direct ON t_order_h.direct_id = t_direct.direct_id \n";							//rev.1.3 直送先テキスト化
	$sql .= "   LEFT JOIN t_client AS t_direct_claim ON t_direct.client_id = t_direct_claim.client_id \n";	//rev.1.3 直送先テキスト化
    $sql .= " WHERE\n";
    $sql .= "   t_order_h.ord_id = $get_ord_id\n";
    $sql .= "   AND\n";
    $sql .= "   t_order_h.ps_stat = '1'\n";
    $sql .= "   AND\n";
    $sql .= "   t_order_h.shop_id = $shop_id\n";
    $sql .= ";\n";

    $result = Db_Query($db_con, $sql);
    Get_Id_Check($result);
    $update_data = pg_fetch_array($result, 0);
    
    //Getを元に抽出したデータをセット
    $set_data["form_order_no"]          = $update_data["ord_no"];           //発注番号
    $set_data["hdn_client_id"]          = $update_data["client_id"];        //仕入先ID
    $set_data["hdn_coax"]               = $update_data["coax"];             //丸め区分
    $set_data["hdn_tax_franct"]         = $update_data["tax_franct"];       //端数区分
    //$set_data["form_client"]["cd"]      = $update_data["client_cd1"];       //仕入先コード
    $set_data["form_client"]["cd1"]     = $update_data["client_cd1"];       //仕入先コード
    $set_data["form_client"]["cd2"]     = $update_data["client_cd2"];       //仕入先コード
    $set_data["form_client"]["name"]    = $update_data["client_cname"];     //仕入先名
    $order_day = explode("-", $update_data["ord_time"]);                    //発注日時
    $set_data["form_order_day"]["y"]    = $order_day[0];
    $set_data["form_order_day"]["m"]    = $order_day[1];
    $set_data["form_order_day"]["d"]    = $order_day[2];
    $hope_day  = explode("-", $update_data["hope_day"]);                    //希望納期
    $set_data["form_hope_day"]["y"]     = $hope_day[0];
    $set_data["form_hope_day"]["m"]     = $hope_day[1];
    $set_data["form_hope_day"]["d"]     = $hope_day[2];
    $arrival_day = explode("-", $update_data["arrival_day"]);               //入荷予定日
    $set_data["form_arrival_day"]["y"]  = $arrival_day[0];
    $set_data["form_arrival_day"]["m"]  = $arrival_day[1];
    $set_data["form_arrival_day"]["d"]  = $arrival_day[2];
    $set_data["form_staff"]             = $update_data["c_staff_id"];       //担当者ID
    $set_data["form_ware"]              = $update_data["ware_id"];          //倉庫ID
    $set_data["form_trade"]             = $update_data["trade_id"];         //取引区分
    $set_data["form_note_your"]         = $update_data["note_your"];        //通信欄
    $set_data["form_note_your2"]        = $update_data["note_your2"];       // 第二通信欄
    $set_data["form_direct"]            = $update_data["direct_id"];        //直送先
    $set_data["form_trans"]             = $update_data["trans_id"];         //運送業者
    $set_data["form_send_date"]         = $update_data["send_date"];        //発信日
    $set_data["hdn_ord_enter_day"]      = $update_data["enter_day"];        //登録日

	//rev.1.3 直送先テキスト化
    $set_data["form_direct_text"]["cd"]	= $update_data["direct_cd"];        //直送先CD
    $set_data["form_direct_text"]["name"]	= $update_data["direct_cname"];	//直送先略称
    $set_data["form_direct_text"]["claim"]	= $update_data["direct_claim"];	//直送先請求先

    //仕入先検索フラグにtrueをセット
    $client_search_flg = true;
 
    //以降の処理で持ちまわるデータ
    $client_id  = $update_data["client_id"];
    $coax       = $update_data["coax"];
    $tax_franct = $update_data["tax_franct"];

    //発注データ情報抽出
    $sql  = "SELECT\n";
    $sql .= "   t_goods.goods_id,\n";
    $sql .= "   t_goods.name_change,\n";
    //$sql .= "   t_goods.stock_manage,\n";
    $sql .= "   t_goods_info.stock_manage,\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "   t_order_d.goods_cd,\n";
    $sql .= "   t_order_d.goods_name,\n";
    //$sql .= "   CASE t_goods.stock_manage\n";
    $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_stock.stock_num,0)\n";
    $sql .= "   END AS rack_num,\n";
    //$sql .= "   CASE t_goods.stock_manage\n";
    $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_stock_io.order_num,0)\n";
    $sql .= "   END AS on_order_num,\n ";
    //$sql .= "   CASE t_goods.stock_manage\n";
    $sql .= "   CASE t_goods_info.stock_manage\n";	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       WHEN 1 THEN COALESCE(t_allowance_io.allowance_io_num,0)\n";
//    $sql .= "                - COALESCE(t_allowance_io.allowance_io_num,0)\n";
    $sql .= "   END AS allowance_total,\n";
    $sql .= "   COALESCE(t_stock.stock_num,0)\n";
    $sql .= "   + COALESCE(t_stock_io.order_num,0)\n";
//    $sql .= "   - (COALESCE(t_stock.rstock_num,0)\n";
    $sql .= "   - COALESCE(t_allowance_io.allowance_io_num,0) AS stock_total,\n";
    $sql .= "   t_order_d.num,\n";
    $sql .= "   t_order_d.buy_price,\n";
    $sql .= "   t_order_d.tax_div,\n";
    $sql .= "   t_order_d.buy_amount,\n";
//    $sql .= "   t_goods_info.in_num\n";
    //aoyama-n 2009-09-08
    #$sql .= "   t_goods.in_num\n";
    $sql .= "   t_goods.in_num,\n";
    $sql .= "   t_goods.discount_flg\n";
    $sql .= " FROM\n ";
    $sql .= "   t_order_d\n";
    $sql .= "       INNER JOIN\n ";
    $sql .= "   t_order_h\n";
    $sql .= "   ON t_order_d.ord_id = t_order_h.ord_id\n ";
    $sql .= "       INNER JOIN\n ";
    $sql .= "   t_goods\n ";
    $sql .= "   ON t_order_d.goods_id = t_goods.goods_id\n";
    $sql .= "       LEFT JOIN \n";

    //在庫数
    $sql .= "   (SELECT\n ";
    $sql .= "       t_stock.goods_id,\n";
    $sql .= "       SUM(t_stock.stock_num)AS stock_num,\n";
    $sql .= "       SUM(t_stock.rstock_num)AS rstock_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock.shop_id = $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "   GROUP BY t_stock.goods_id\n";
    $sql .= "   )AS t_stock \n";
    $sql .= "   ON t_order_d.goods_id = t_stock.goods_id\n";
    $sql .= "       LEFT JOIN\n";

    //発注残数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div\n";
    $sql .= "                                    WHEN 1 THEN 1\n";
    $sql .= "                                    WHEN 2 THEN -1\n";
    $sql .= "                              END\n";
    $sql .= "    ) AS order_num\n ";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = 3\n";
    $sql .= "       AND\n";
    $sql .= "       t_stock_hand.shop_id = $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "       AND\n";
//    $sql .= "       CURRENT_DATE <= t_stock_hand.work_day\n ";
//    $sql .= "       AND\n";
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + 7)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_stock_io\n";
    $sql .= "   ON t_order_d.goods_id=t_stock_io.goods_id\n";
    $sql .= "       LEFT JOIN\n";

    //引当数
    $sql .= "   (SELECT\n";
    $sql .= "       t_stock_hand.goods_id,\n";
    $sql .= "       SUM(t_stock_hand.num * CASE t_stock_hand.io_div\n";
    $sql .= "                                    WHEN 1 THEN 1\n";
    $sql .= "                                    WHEN 2 THEN -1\n";
    $sql .= "                              END\n";
    $sql .= "        ) AS allowance_io_num\n";
    $sql .= "   FROM\n";
    $sql .= "       t_stock_hand\n";
    $sql .= "           INNER JOIN\n";
    $sql .= "       t_ware\n";
    $sql .= "       ON t_stock_hand.ware_id = t_ware.ware_id\n";
    $sql .= "   WHERE\n";
    $sql .= "       t_stock_hand.work_div = 1\n";
    $sql .= "       AND\n";
    $sql .= "       t_stock_hand.shop_id =  $shop_id\n";
    $sql .= "       AND\n";
    $sql .= "       t_ware.count_flg = 't'\n";
    $sql .= "       AND\n";
//    $sql .= "       t_stock_hand.work_day > (CURRENT_DATE + 7)\n";
    $sql .= "       t_stock_hand.work_day <= (CURRENT_DATE + 7)\n";
    $sql .= "   GROUP BY t_stock_hand.goods_id\n";
    $sql .= "   ) AS t_allowance_io\n";
    $sql .= "   ON t_order_d.goods_id = t_allowance_io.goods_id\n";

	//rev.1.3 ショップごとに在庫管理フラグ
    $sql .= "       INNER JOIN\n ";
    $sql .= "   t_goods_info\n ";
    $sql .= "   ON t_goods.goods_id = t_goods_info.goods_id\n";
    $sql .= " WHERE\n";
    $sql .= "   t_order_d.ord_id = $get_ord_id\n";
    $sql .= "   AND\n";
    $sql .= "   t_order_h.shop_id = $shop_id\n";
    $sql .= "   AND\n";
    //$sql .= "   t_goods_info.shop_gid = $shop_gid\n";
//    if($_SESSION[group_kind] == "2"){
//        $sql .= "   t_goods_info.shop_id IN (".Rank_Sql().") ";
//    }else{
        $sql .= "   t_goods_info.shop_id = $_SESSION[client_id]";
//    }

    $sql .= " ORDER BY t_order_d.line\n";
    $sql .= ";\n"; 

    $result = Db_Query($db_con, $sql);
    Get_Id_Check($result);
    $update_data_num = pg_num_rows($result);

    for($i = 0; $i < $update_data_num; $i++){
        //抽出したデータ
        $update_d_data = pg_fetch_array($result, $i);

        //抽出した単価データをセットする形式に変更
        //抽出した単価を変数にセット
        $goods_price        = $update_d_data["buy_price"];      //仕入単価
        //単価を整数部と少数部に分ける
        $goods_price_data   = explode(".", $goods_price); 

        //抽出したデータをフォームにセット
        $set_data["form_goods_id"][$i]      = $update_d_data["goods_id"];        //商品ID
        $set_data["hdn_stock_manage"][$i]   = $update_d_data["stock_manage"];    //在庫管理
        $set_data["hdn_name_change"][$i]    = $update_d_data["name_change"];     //品名変更
        $set_data["hdn_goods_id"][$i]       = $update_d_data["goods_id"];        //商品ID
        $set_data["form_goods_cd"][$i]      = $update_d_data["goods_cd"];        //商品コード
        $set_data["form_goods_name"][$i]    = $update_d_data["goods_name"];      //商品名
        $set_data["form_stock_num"][$i]     = $update_d_data["rack_num"];        //実棚数
        $set_data["hdn_stock_num"][$i]      = $update_d_data["rack_num"];        //実棚数
        $set_data["hdn_stock_manage"][$i]   = $update_d_data["stock_manage"];    //実棚数
        $set_data["form_rorder_num"][$i]    = $update_d_data["on_order_num"];    //発注残数
        $set_data["form_designated_num"][$i]= $update_d_data["stock_total"];     //出荷可能数
        $set_data["form_rstock_num"][$i]    = $update_d_data["allowance_total"]; //引当数
        $set_data["form_order_num"][$i]     = $update_d_data["num"];             //発注数
        $set_data["hdn_tax_div"][$i]        = $update_d_data["tax_div"];         //課税区分
        $set_data["form_in_num"][$i]        = $update_d_data["in_num"];          //入数
        $set_data["form_buy_price"][$i]["i"]= $goods_price_data[0];              //仕入単価（整数部）
        $set_data["form_buy_price"][$i]["d"]= $goods_price_data[1];              //仕入単価（少数部） 
        $set_data["form_buy_amount"][$i]    = $update_d_data["buy_amount"];      //仕入金額
        $set_data["hdn_tax_div"][$i]        = $update_d_data["tax_div"];         //課税区分
        //aoyama-n 2009-09-08
        $set_data["hdn_discount_flg"][$i]   = $update_d_data["discount_flg"];    //値引フラグ

        //以降の処理で持ちまわるデータ
        $goods_id[$i]                       = $update_d_data["goods_id"];        //商品ID
        $stock_num[$i]                      = $update_d_data["rack_num"];        //実棚数(リンク用)
        $stock_manage[$i]                   = $update_d_data["stock_manage"];    //在庫管理（在庫数表示判定）
        $name_change[$i]                    = $update_d_data["name_change"];     //品名変更（品名変更付加判定）

        //合計を求めるための課税区分
        $price_data[$i] = $update_d_data["buy_amount"];   //金額
        $tax_div[$i] = $update_d_data["tax_div"];         //課税区分
    }

    //行数をセット
    $max_row = $update_data_num;

    #2009-12-21 aoyama-n
    $tax_rate_obj->setTaxRateDay($update_data["ord_time"]);
    $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

    $data = Total_Amount($price_data, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $db_con);

    //フォームに値セット
    $set_data["form_buy_money"]     = number_format($data[0]);
    $set_data["form_tax_money"]     = number_format($data[1]);
    $set_data["form_total_money"]   = number_format($data[2]);

    //Getで取得した発注IDをhiddenで持ちまわる
    $set_data["hdn_order_id"] = $get_ord_id;
}

//最大行数をhiddenにセット
$set_data["max_row"] = $max_row;

//上記処理により生成した値をフォームにセット
$form->setConstants($set_data);

/****************************/
//動的に増減するフォーム作成
/****************************/
//仕入先が選択されていない場合
if($client_search_flg === false || $freeze_flg == true){
    #2009-09-15 hashimoto-y
    #$style = "color : #000000;
    #        border : #ffffff 1px solid; 
    #        background-color: #ffffff";
    $style = "border : #ffffff 1px solid; 
            background-color: #ffffff";
    $type = "readonly";
}
$row_num= 1;
for($i = 0; $i < $max_row; $i++){
    //表示行判定(削除履歴にない行のみ表示)
    if(!in_array("$i", $del_history)){

        $del_data = $del_row.",".$i;

        #2009-09-15 hashimoto-y
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
            onChange=\"return goods_search_2(this.form, 'form_goods_cd', 'hdn_goods_search_flg', $i ,$row_num);\""
        );

        //商品名
        if($name_change[$i] == '2'){
            $read_only = "readonly";
        }else{
            $read_only = null;
        }
        $form->addElement(
            "text","form_goods_name[$i]","",
            "size=\"54\" maxlength=\"41\" 
            style=\"$font_color $style \"
            $read_only"
        );

        //品名変更フラグ
        $form->addElement("hidden","hdn_name_change[$i]");

        //在庫管理
        $form->addElement("hidden","hdn_stock_manage[$i]");

        //実棚数
        $form->addElement("hidden","hdn_stock_num[$i]");

        $form->addElement("link",
            "form_stock_num[$i]","","#","$stock_num[$i]",
            "onClick=\"javascript:Open_mlessDialmg_g('1-3-111.php','$goods_id[$i]',1,300,160);\""
        );

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
        //発注数
        $form->addElement("text","form_order_num[$i]","",
            "size=\"6\" maxLength=\"5\"
             onKeyup=\"Mult('hdn_goods_id[$i]','form_order_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');ord_in_num($i);\"
            style=\"text-align: right; $font_color $style $g_form_style \"
            $type"
        );

        //仕入単価
        $form_buy_price[$i][] =& $form->createElement(
            "text","i","",
            "size=\"11\" maxLength=\"9\" class=\"money\"
            onKeyup=\"Mult('hdn_goods_id[$i]','form_order_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
            style=\"text-align: right; $font_color $style $g_form_style \"
            $type"
        );
        $form_buy_price[$i][] =& $form->createElement(
            "text","d","","size=\"2\" maxLength=\"2\"
             onKeyup=\"Mult('hdn_goods_id[$i]','form_order_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
            style=\"text-align: left; $font_color $style $g_form_style \"
            $type"
        );
        $form->addGroup( $form_buy_price[$i], "form_buy_price[$i]", "",".");

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

        //aoyama-n 2009-09-08
        //値引フラグ
        #$form->addElement("hidden","hdn_discount_flg[$i]","","");

        //その他商品か登録確認画面の場合は非表示
        if($client_search_flg === true && $freeze_flg != true){
            //検索リンク
            $form->addElement(
                "link","form_search[$i]","","#","検索",
                "TABINDEX=-1 
                onClick=\"return Open_SubWin_2('../dialog/1-0-210.php', Array('form_goods_cd[$i]','hdn_goods_search_flg'), 500, 450,5,$client_id,$i,$row_num);\""
            );

            //削除リンク
            //最終行を削除する場合、削除した後の最終行に合わせる
            if($row_num == $max_row-$del_num){
                $form->addElement(
                    "link","form_del_row[$i]","",
                    "#","<font color='#FEFEFE'>削除</font>",
                    "TABINDEX=-1 
                    onClick=\"javascript:Dialogue_3('削除します。', '$del_data', 'del_row' ,$row_num-1);return false;\"");

            //最終行以外を削除する場合、削除する行と同じNOの行に合わせる
            }else{
                $form->addElement(
                    "link","form_del_row[$i]","","#",
                    "<font color='#FEFEFE'>削除</font>",
                    "TABINDEX=-1 
                    onClick=\"javascript:Dialogue_3('削除します。', '$del_data', 'del_row' ,$row_num);return false;\""                                  );
            }
        }
        /****************************/
        //表示用HTML作成
        /****************************/
        $html .= " <tr class=\"Result1\">";
        $html .= "  <A NAME=$row_num><td align=\"right\">$row_num</td></A>";
        $html .= "  <td align=\"left\">";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_cd[$i]"]]->toHtml();
        //得意先が選択された場合
        if($client_search_flg === true && $freeze_flg != true){
            $html .= "      （".$form->_elements[$form->_elementIndex["form_search[$i]"]]->toHtml()."）";
        }
        $html .= "<br>";
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
            #2009-09-15 hashimoto-y
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

        //得意先が選択された場合
        if($client_search_flg === true && $freeze_flg != true){
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
    $form->addElement("button","form_sum_button","合　計",
            "onClick=\"javascript:Button_Submit('hdn_sum_button_flg','#foot','t')\"");
/*
    $form->addElement("link","form_client_link","","#","発注先","
        onClick=\"return Open_SubWin('../dialog/1-0-208.php',Array('form_client[cd]','form_client[name]', 'hdn_client_search_flg'),500,450,5,1);\""    );    
*/
    $form->addElement("link","form_client_link","#","./1-3-207.php","発注先","
    onClick=\"return Open_SubWin('../dialog/1-0-250.php',Array('form_client[cd1]','form_client[cd2]','form_client[name]','hdn_client_search_flg'),500,450,'1-3-207',1);\"");

	//rev.1.3 直送先ダイアログリンク
    $form->addElement("link","form_direct_link","#","./1-3-207.php","直送先","
    onClick=\"return Open_SubWin('../dialog/1-0-260.php',Array('form_direct_text[cd]','form_direct_text[name]','form_direct_text[claim]','hdn_direct_search_flg'),500,450,'1-3-207',1);\"");


    //仕入先が選択されていない場合はフリーズ
    //行追加ボタン
    if($client_search_flg === true){
        $form->addElement("button","form_add_row_button", "追　加", "onClick=\"javascript:Button_Submit('add_row_flg','#foot','true')\"");
    }

}else{
    //登録確認画面では以下のボタンを表示
    //戻る  
    $form->addElement("button","form_back_button","戻　る","onClick=\"javascript:history.back()\"");
    
    //OK
    $form->addElement("submit","form_comp_button","発注完了", $disabled);

    //発注書出力
    $form->addElement("submit","form_slip_comp_button","発注完了と発注書出力", $disabled);

    //発注先
    $form->addElement("static","form_client_link","","発注先"); 

    //直送先
    $form->addElement("static","form_direct_link","","発注先"); 

    $form->freeze();
}

/****************************/
//javascript
/****************************/
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
$js .= "    if(isNaN(v_num) == true){\n";
$js .= "        var v_num = \"\"\n";
$js .= "    }\n";
$js .= "    document.dateForm.elements[ord_num].value = v_num;\n";
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
$js .= "    var result = v_ord_num % v_in_num;    if(result == 0){\n";
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
$page_menu = Create_Menu_h('buy','1');

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "　".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[ord_button]]->toHtml();
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
    'html'          => "$html",
    'client_search_flg'    => "$client_search_flg",
    'warning'       => "$warning",
    'js'            => "$js",
    'freeze_flg'    => "$freeze_flg",
    'goods_twice'   => "$goods_twice",
    'error'         => "$error",
    'update_flg'    => "$update_flg",
));

$smarty->assign('price_err',$price_err);
$smarty->assign('price_num_err',$price_num_err);
$smarty->assign('num_err',$num_err);
$smarty->assign('goods_err',$goods_err);
$smarty->assign('duplicate_goods_err', $duplicate_goods_err);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
