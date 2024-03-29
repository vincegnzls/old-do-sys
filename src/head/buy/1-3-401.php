<?php
/**
 *
 * 売掛残高一覧
 *   または
 * 買掛残高一覧
 *
 *
 *
 *
 *
 *
 *
 *   !! 本部・FC画面ともに同じソース内容です !!
 *   !! 変更する場合は片方をいじって他方にコピってください !!
 *
 *
 *
 *
 *
 *
 *
 * 1.0.0 (2007/01/31) 新規作成
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.1.0 (2007/03/07)
 *
 */
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2007/02/22      xx-xxx      kajioka-h   帳票出力機能を削除
 *  2007/03/07      その他-22   kajioka-h   ロイヤリティ、一括消費税の処理追加による変更
 *  2007/05/08      xx-xxx      kajioka-h   日付フォームにデフォルト値を設定
 *  2007/05/11      B0702-051   kajioka-h   調整額の符号が間違っているのを修正
 *                  xx-xxx      kajioka-h   FCの売掛残高一覧の場合、グループで検索できるように変更
 *  2007/05/16      xx-xxx      kajioka-h   初期表示時は結果テーブルを表示しない
 *                  xx-xxx      kajioka-h   結果が0件の場合は、結果テーブルと下の全件数は表示しない
 *                  xx-xxx      kajioka-h   結果テーブルを緑、白の交互に、合計行をグレー表示
 *                  xx-xxx      kajioka-h   グループ検索に、文字列で検索できるように変更
 *  2007/05/25      xx-xxx      kajioka-h   本部買掛残高一覧にFC、仕入先区分の検索項目を追加
 *  2007/05/28      B0702-058   kajioka-h   本部の場合、同じclient_idで売りも買いもするので、ロイヤリティ・一括消費税の区別がつかないバグ修正
 *  2007/05/30      xx-xxx      kajioka-h   本部の場合に、FC・取引先区分で検索できるように
 *                  xx-xxx      kajioka-h   本部売掛、「得意先」→「FC・取引先」に文言変更
 *  2007-06-07                  fukuda      検索結果1行目にも合計行を表示
 *  2007-06-07                  fukuda      検索項目に「取引状態」追加、「残高（税込）」の検索条件を拡張
 *  2007-06-07                  fukuda      金額の範囲で検索を行うとクエリエラーが出る不具合の修正
 *  2007-06-07                  fukuda      ページ数切り替え時、仕入先区分を変更→POST可能な不具合を修正
 *  2007-06-13                  fukuda      残高検索「0円以外」を、"全ての項目が0円の場合のみ抽出しない"に修正
 *  2007/06/21      xx-xxx      kajioka-h   買掛残高一覧の仕入先区分をFC・取引先区分に名称変更、抽出条件を「全て、FC（ＣＴ・ＳＰ）」に変更
 *  2007/06/25      xx-xxx      kajioka-h   直営の買掛残高一覧から「FC・取引先区分」の検索条件を削除
 *                  B0702-064   kajioka-h   残高に数値以外を入力してもエラーメッセージが表示されないバグ修正
 *  2007/06/26      xx-xxx      kajioka-h   自分のショップの得意先・仕入先のデータしか抽出しないように変更
 *  2007/06/28      xx-xxx      kajioka-h   本部買掛残高一覧の場合、FCのデータしか抽出しないように変更
 *  2007-07-12                  fukuda      手数料の列を追加
 *  2007-07-27                  fukuda      割賦残高の列を追加
 *  2011-01-22                  watanabe-k  検索月以後の割賦残高をすべて集計してしまうバグの修正
 *  2011-02-11                  watanabe-k  初期残高設定が前月残高として抽出されないバグの修正
 *
 */

//売買区分
//・売掛残高一覧の場合は「sale」
//・買掛残高一覧の場合は「buy」
//$trade_div = "sale";
$trade_div = "buy";

$page_title = ($trade_div == "sale") ? "売掛残高一覧" : "買掛残高一覧";
$trade_mess = ($trade_div == "sale") ? "売掛" : "買掛";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("$_SERVER[PHP_SELF]", "POST");

//DB接続
$db_con = Db_Connect();

//SQL生成関数
require_once(INCLUDE_DIR."function_monthly_renew.inc");


//日次更新フラグ
//・「true」対象期間の日次更新済のものだけ集計
//・「false」日次更新実施有無に関係なく、対象期間のデータを全て集計
$renew_flg = false;

//売掛と買掛で違うとこに使う
$arp = ($trade_div == "sale") ? "ar" : "ap";
$t_balance_name = "t_".$arp."_balance";
$pay_div = ($trade_div == "sale") ? "payin" : "payout";


/****************************/
// 外部変数取得
/****************************/
$shop_id    = $_SESSION["client_id"];
$group_kind = $_SESSION["group_kind"];



/****************************/
// フォームパーツ作成
/****************************/
/*
// 出力形式
$radio_output_type[] =& $form->createElement("radio", null, null, "画面", "1");
$radio_output_type[] =& $form->createElement("radio", null, null, "帳票", "2");
$form->addGroup($radio_output_type, "form_output_type", "");
*/

// 対象月
$text_input_day[] =& $form->createElement("text", "y", "", 
                        "size=\"4\" maxLength=\"4\" value=\"\" style=\"$g_form_style\" $g_form_option
                         onkeyup=\"changeText(this.form, 'form_input_day[y]', 'form_input_day[m]', 4);\"
                         onFocus=\"onForm_today2(this,this.form,'form_input_day[y]','form_input_day[m]');\"
                        ");
$text_input_day[] =& $form->createElement("static", "", "", "-");
$text_input_day[] =& $form->createElement("text", "m", "", 
                        "size=\"1\" maxLength=\"2\" value=\"\" style=\"$g_form_style\" $g_form_option
                         onFocus=\"onForm_today2(this,this.form,'form_input_day[y]','form_input_day[m]');\"
                        ");
$form->addGroup( $text_input_day, "form_input_day", "");

//表示件数
$select_num = array(
    "10"    => "10",
    "50"    => "50",
    "100"   => "100",
    "ALL"   => "全て", 
);
$form->addElement("select", "form_display_num", "", $select_num, $g_form_option_select);

// 取引先コード
//売掛残高一覧、または本部の買掛残高一覧の場合は取引先CD2も
if($trade_div == "sale" || $group_kind == "1"){
    $client_cd[] =& $form->createElement("text", "cd1", "", "size=\"7\" maxLength=\"6\" value=\"\" style=\"$g_form_style\" onkeyup=\"changeText(this.form, 'form_client_cd[cd1]', 'form_client_cd[cd2]', 6)\" $g_form_option");
    $client_cd[] =& $form->createElement("static", "", "", "-");
    $client_cd[] =& $form->createElement("text", "cd2", "", "size=\"4\" maxLength=\"4\" value=\"\" style=\"$g_form_style\" $g_form_option");
//FCの買掛残高一覧は取引先CD1のみ
}else{
    $client_cd[] =& $form->createElement("text", "cd1", "", "size=\"7\" maxLength=\"6\" value=\"\" style=\"$g_form_style\" $g_form_option");
}
$form->addGroup( $client_cd, "form_client_cd", "");

// 取引先名
$form->addElement("text", "form_client_name", "", "size=\"34\" maxLength=\"25\" $g_form_option");

//FC・取引先（本部画面のみ）
if($group_kind == "1"){
    $rank_select_value = Select_Get($db_con, "rank");
    $form->addElement("select", "form_rank", "", $rank_select_value, $g_form_option_select);
}

/*
// SV
$select_staff1 = Select_Get($db_con, "staff", true);
$form->addElement("select", "form_staff1", "", $select_staff1, $g_form_option_select);

// 窓口担当１
$select_staff2 = Select_Get($db_con, "staff", true);
$form->addElement("select", "form_staff2", "", $select_staff2, $g_form_option_select);
*/

// 当月/売|買/掛残高
$balance_this[] =& $form->createElement("text", "min", "", "size=\"11\" maxLength=\"9\" value=\"\" onkeyup=\"changeText(this.form, 'form_balance_this[min]', 'form_balance_this[max]', 9)\" $g_form_option style=\"text-align: right; $g_form_style\"");
$balance_this[] =& $form->createElement("static", "", "", "　〜　");
$balance_this[] =& $form->createElement("text", "max", "", "size=\"11\" maxLength=\"9\" value=\"\" $g_form_option style=\"text-align: right; $g_form_style\"");
$form->addGroup( $balance_this, "form_balance_this", "");

// 月次更新
/*
$monthly_renew[] =& $form->createElement("radio", null, null, "更新後", "1");
$monthly_renew[] =& $form->createElement("radio", null, null, "更新前", "2");
$form->addGroup($monthly_renew, "form_monthly_renew", "月次更新");
*/

// グループ（FCの売掛残高一覧のみ）
if($group_kind != "1" && $trade_div == "sale"){
    $select_group = null;
    $select_group = Select_Get($db_con, "client_gr");
    $form->addElement("select", "form_client_gr", "", $select_group, $g_form_option_select);

    $form->addElement("text", "form_client_gr_name", "", "size=\"34\" maxLength=\"25\" $g_form_option");
}

// 残高（ラジオボタン）
$obj    =   null;   
$obj[]  =&  $form->createElement("radio", null, null, "0円以外", "1");
$obj[]  =&  $form->createElement("radio", null, null, "全て",  "2");
$form->addGroup($obj, "form_balance_radio", "");

//状態（得意先）
$obj    =   null;   
$obj[]  =&  $form->createElement("radio", null, null, "取引中",       "1");   
$obj[]  =&  $form->createElement("radio", null, null, "解約・休止中", "2");
$obj[]  =&  $form->createElement("radio", null, null, "全て",         "0");   
$form->addGroup($obj, "form_state", "");

// 取引区分
$item   =   null;
if ($trade_div == "sale"){
    $item   =   Select_Get($db_con, "trade_aord");
}else{
    $item   =   Select_Get($db_con, "trade_ord");
}
$form->addElement("select", "form_trade", "", $item, "onKeyDown=\"chgKeycode();\" onChange =\"window.focus();\"");

// 表示ボタン
$form->addElement("submit", "form_display_button", "表　示", "");

// クリアボタン
$form->addElement("button", "form_clear_button", "クリア", "onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

// 初期値指定
$def_fdata = array(
    //"form_output_type"      => "1",
    "form_display_num"      => "50",
    "form_monthly_renew"    => "1",
    "form_supplier_div"     => "0",
    "form_input_day"        => array(
        "y" => date("Y"),
        "m" => date("m"),
    ),
    "form_balance_radio"    => "1",
    "form_state"            => "1",
);

$form->setDefaults($def_fdata);


// hidden
//$form->addElement("hidden", "hdn_output_type");
$form->addElement("hidden", "hdn_display_num");
$form->addElement("hidden", "hdn_input_day[y]");
$form->addElement("hidden", "hdn_input_day[m]");
$form->addElement("hidden", "hdn_client_cd[cd1]");
$form->addElement("hidden", "hdn_client_cd[cd2]");
$form->addElement("hidden", "hdn_client_name");
$form->addElement("hidden", "hdn_client_gr");
$form->addElement("hidden", "hdn_client_gr_name");
$form->addElement("hidden", "hdn_rank");
//$form->addElement("hidden", "hdn_staff1");
//$form->addElement("hidden", "hdn_staff2");
$form->addElement("hidden", "hdn_balance_this[min]");
$form->addElement("hidden", "hdn_balance_this[max]");
//$form->addElement("hidden", "hdn_monthly_renew");
$form->addElement("hidden", "hdn_balance_radio");
$form->addElement("hidden", "hdn_state");
$form->addElement("hidden", "hdn_trade");


// 本部買掛残高一覧の場合は仕入先区分も生成
if($trade_div == "buy" && $group_kind == "1"){
    $radio_supplier_div[] =& $form->createElement("radio", null, null, "全て", "0");
    //$radio_supplier_div[] =& $form->createElement("radio", null, null, "仕入先", "2");
    $radio_supplier_div[] =& $form->createElement("radio", null, null, "FC（ＣＴ・ＳＰ）", "3");
    $form->addGroup($radio_supplier_div, "form_supplier_div", "仕入先区分");
    $form->addElement("hidden", "hdn_supplier_div");
}


/****************************/
//POST取得
/****************************/
if($_POST["form_display_button"] == "表　示"){
//print_array($select_num);

    $range                  = (array_key_exists($_POST["form_display_num"], $select_num)) ? $_POST["form_display_num"] : "50";   //表示件数
    $page_count             = 1;
    $offset                 = 0;

    //$output_type            = (in_array($_POST["form_output_type"], array("1", "2"))) ? $_POST["form_output_type"] : "1";
    $input_day_y            = $_POST["form_input_day"]["y"];
    $input_day_m            = $_POST["form_input_day"]["m"];
    $client_cd1             = $_POST["form_client_cd"]["cd1"];
    $client_cd2             = $_POST["form_client_cd"]["cd2"];
    $client_name            = $_POST["form_client_name"];
    $client_gr              = $_POST["form_client_gr"];
    $client_gr_name         = $_POST["form_client_gr_name"];
    $rank                   = $_POST["form_rank"];
    //$staff1                 = (array_key_exists($_POST["form_staff1"], $select_staff1)) ? $_POST["form_staff1"] : "";
    //$staff2                 = (array_key_exists($_POST["form_staff2"], $select_staff2)) ? $_POST["form_staff2"] : "";
    $balance_this_min       = $_POST["form_balance_this"]["min"];
    $balance_this_max       = $_POST["form_balance_this"]["max"];
    //$monthly_renew          = (in_array($_POST["form_monthly_renew"], array("1", "2"))) ? $_POST["form_monthly_renew"] : "1";
    $balance_radio          = $_POST["form_balance_radio"];
    $state                  = $_POST["form_state"];
    $trade                  = $_POST["form_trade"];

    //検索条件をformに詰める
    //$con_data["form_output_type"]           = $output_type;
    $con_data["form_display_num"]           = $range;
    $con_data["form_input_day[y]"]          = $input_day_y;
    $con_data["form_input_day[m]"]          = $input_day_m;
    $con_data["form_client_cd[cd1]"]        = $client_cd1;
    $con_data["form_client_cd[cd2]"]        = $client_cd2;
    $con_data["form_client_name"]           = $client_name;
    $con_data["form_client_gr"]             = $client_gr;
    $con_data["form_client_gr_name"]        = $client_gr_name;
    $con_data["form_rank"]                  = $rank;
    //$con_data["form_staff1"]                = $staff1;
    //$con_data["form_staff2"]                = $staff2;
    $con_data["form_balance_this[min]"]     = $balance_this_min;
    $con_data["form_balance_this[max]"]     = $balance_this_max;
    //$con_data["form_monthly_renew"]         = $monthly_renew;
    $con_data["hdn_balance_radio"]          = $balance_radio;
    $con_data["hdn_state"]                  = $state;
    $con_data["hdn_trade"]                  = $trade;

    //検索条件をhiddenに詰める
    //$con_data["hdn_output_type"]            = $output_type;
    $con_data["hdn_display_num"]            = $range;
    $con_data["hdn_input_day[y]"]           = $input_day_y;
    $con_data["hdn_input_day[m]"]           = $input_day_m;
    $con_data["hdn_client_cd[cd1]"]         = $client_cd1;
    $con_data["hdn_client_cd[cd2]"]         = $client_cd2;
    $con_data["hdn_client_name"]            = $client_name;
    $con_data["hdn_client_gr"]              = $client_gr;
    $con_data["hdn_client_gr_name"]         = $client_gr_name;
    $con_data["hdn_rank"]                   = $rank;
    //$con_data["hdn_staff1"]                 = $staff1;
    //$con_data["hdn_staff2"]                 = $staff2;
    $con_data["hdn_balance_this[min]"]      = $balance_this_min;
    $con_data["hdn_balance_this[max]"]      = $balance_this_max;
    //$con_data["hdn_monthly_renew"]          = $monthly_renew;
    $con_data["hdn_balance_radio"]          = $balance_radio;
    $con_data["hdn_state"]                  = $state;
    $con_data["hdn_trade"]                  = $trade;


    if($trade_div == "buy" && $group_kind == "1"){
        $supplier_div       = (in_array($_POST["form_supplier_div"], array("0", "2", "3"))) ? $_POST["form_supplier_div"] : "0";
        $con_data["form_supplier_div"]      = $supplier_div;
        //$supplier_div                       = $_POST["form_supplier_div"];
        $con_data["hdn_supplier_div"]       = $supplier_div;
    }


//ページ遷移時
}elseif($_POST["form_display_button"] == null && $_POST["f_page1"] != null){

    $range                  = (in_array($_POST["hdn_display_num"], $select_num)) ? $_POST["hdn_display_num"] : "50";   //表示件数
    $page_count             = $_POST["f_page1"];
    $offset                 = ($page_count - 1) * $range;

    //$output_type            = (in_array($_POST["hdn_output_type"], array("1", "2"))) ? $_POST["hdn_output_type"] : "1";
    $input_day_y            = $_POST["hdn_input_day"]["y"];
    $input_day_m            = $_POST["hdn_input_day"]["m"];
    $client_cd1             = $_POST["hdn_client_cd"]["cd1"];
    $client_cd2             = $_POST["hdn_client_cd"]["cd2"];
    $client_name            = $_POST["hdn_client_name"];
    $client_gr              = $_POST["hdn_client_gr"];
    $client_gr_name         = $_POST["hdn_client_gr_name"];
    $rank                   = $_POST["hdn_rank"];
    //$staff1                 = (array_key_exists($_POST["hdn_staff1"], $select_staff1)) ? $_POST["hdn_staff1"] : "";
    //$staff2                 = (array_key_exists($_POST["hdn_staff2"], $select_staff2)) ? $_POST["hdn_staff2"] : "";
    $balance_this_min       = $_POST["hdn_balance_this"]["min"];
    $balance_this_max       = $_POST["hdn_balance_this"]["max"];
    //$monthly_renew          = (in_array($_POST["hdn_monthly_renew"], array("1", "2"))) ? $_POST["hdn_monthly_renew"] : "1";
    $balance_radio          = $_POST["hdn_balance_radio"];
    $state                  = $_POST["hdn_state"];
    $trade                  = $_POST["hdn_trade"];


    //表示ボタン押下時以外はhiddenの値を検索フォームにセットする
    //$con_data["form_output_type"]           = $output_type;
    $con_data["form_display_num"]           = $range;
    $con_data["form_input_day[y]"]          = $input_day_y;
    $con_data["form_input_day[m]"]          = $input_day_m;
    $con_data["form_client_cd[cd1]"]        = $client_cd1;
    $con_data["form_client_cd[cd2]"]        = $client_cd2;
    $con_data["form_client_name"]           = $client_name;
    $con_data["form_client_gr"]             = $client_gr;
    $con_data["form_client_gr_name"]        = $client_gr_name;
    $con_data["form_rank"]                  = $rank;
    //$con_data["form_staff1"]                = $staff1;
    //$con_data["form_staff2"]                = $staff2;
    $con_data["form_balance_this[min]"]     = $balance_this_min;
    $con_data["form_balance_this[max]"]     = $balance_this_max;
    //$con_data["form_monthly_renew"]         = $monthly_renew;
    $con_data["form_balance_radio"]         = $balance_radio;
    $con_data["form_state"]                 = $state;
    $con_data["form_trade"]                 = $trade;


    if($trade_div == "buy" && $group_kind == "1"){
        //$supplier_div       = (in_array($_POST["form_supplier_div"], array("0", "2", "3"))) ? $_POST["form_supplier_div"] : "0";
        //$con_data["form_supplier_div"]      = $supplier_div;
        $supplier_div                       = $_POST["hdn_supplier_div"];
        $con_data["form_supplier_div"]       = $supplier_div;
    }


//初期表示
}else{

    $range                  = 50;
    $page_count             = 1;
    $offset                 = 0;

}

$form->setConstants($con_data);


/****************************/
//表示ボタン押下またはページ切替時
/****************************/
if($_POST["form_display_button"] == "表　示" || $_POST["f_page1"] != null){

    // 対象月をゼロ埋めしておく
    $_POST["form_input_day"] = Str_Pad_Date($_POST["form_input_day"]);
    // 変数にセット
    $form_day_y = $_POST["form_input_day"]["y"];
    $form_day_m = $_POST["form_input_day"]["m"];
    $input_day = $form_day_y."-".$form_day_m;

    /****************************/
    //エラーチェック(addRule)
    /****************************/
    // ■対象月
    // 必須チェック
    if ($_POST["form_input_day"]["y"] == null || $_POST["form_input_day"]["m"] == null){
        $form->setElementError("form_input_day", "対象月 は必須です。");
    }
    // 数値チェック
    elseif (!ereg("^[0-9]+$", $form_day_y) || !ereg("^[0-9]+$", $form_day_m)){
        $form->setElementError("form_input_day", "対象月 が妥当ではありません。");
    }       
    // 日付の妥当性チェック
    elseif (!checkdate((int)$form_day_m, (int)1, (int)$form_day_y)){
        $form->setElementError("form_input_day", "対象月 が妥当ではありません。");
    }
    // これまでにエラーがない場合
    if ($form->getElementError("form_input_day") == null){
        // システム開始日以降チェック関数実行
        $sysup_chk = Sys_Start_Date_Chk($form_day_y, $form_day_m, "1", "対象月 ");
        // システム開始日以降チェック
        if ($sysup_chk != null){
            $form->setElementError("form_input_day", str_replace("1日", "", $sysup_chk));
        } 
    }

    //当月/売|買/掛残高
    //開始
    if($balance_this_min != null){
        if(!ereg("^[-]?[0-9]+$", $balance_this_min)){
            $form->setElementError("form_balance_this", "当月".$trade_mess."残高 は数値のみ入力可能です。");
        }
    }
    //終了
    if($balance_this_max != null){
        if(!ereg("^[-]?[0-9]+$", $balance_this_max)){
            $form->setElementError("form_balance_this", "当月".$trade_mess."残高 は数値のみ入力可能です。");
        }
    }

    // チェック適用
    $form->validate();

    // 結果をフラグに
    $err_flg = (count($form->_errors) > 0) ? true : false;

    //エラーの場合はこれ以降の表示処理を行なわない
    //if($form->validate() && $error_flg == false){
    if($err_flg != true){


        //指定年月が月次更新済かどうか
        $sql  = "SELECT \n";
        $sql .= "    close_day \n";
        $sql .= "FROM \n";
        $sql .= "    t_sys_renew \n";
        $sql .= "WHERE \n";
        $sql .= "    shop_id = ".$shop_id." \n";
        $sql .= "    AND \n";
        $sql .= "    renew_div = '2' \n";
        $sql .= "    AND \n";
        $sql .= "    close_day LIKE '$input_day-%' \n";
        $sql .= ";\n";

        $result = Db_Query($db_con, $sql);


        //月次更新データがある場合、月次/売|買/掛残高テーブルからデータを抽出
        if(pg_num_rows($result) != 0){

            /* 次回締日を取得 */

            //自社締日(日)を取得
            $sql = "SELECT my_close_day FROM t_client WHERE client_id = $shop_id;";
            $result = Db_Query($db_con, $sql);
            $my_close_day = pg_fetch_result($result, 0, 0);     //自社締日(日)

            $close_day_this  = $input_day;
            $close_day_this .= "-";
            $close_day_this .= ($my_close_day == 29) ? date("t", mktime(0, 0, 0, $input_day_m, 1, $input_day_y)) : $my_close_day;   //入力年月の締日(年月日)

            //指定年月から一回前の月次更新日を取得
            $sql  = "SELECT \n";
            $sql .= "   close_day \n";
            $sql .= "FROM \n";
            $sql .= "   t_sys_renew \n";
            $sql .= "WHERE \n";
            $sql .= "   shop_id = ".$shop_id." \n";
            $sql .= "AND \n";
            $sql .= "   renew_div = '2' \n";
            $sql .= "AND \n";
            $sql .= "   close_day < '$close_day_this' \n";
            $sql .= "ORDER BY \n";
            $sql .= "   close_day DESC \n";
            $sql .= "LIMIT 1 \n";
            $sql .= ";\n";

            $result = Db_Query($db_con, $sql);
            $close_day_last = (pg_num_rows($result) == 0) ? START_DAY : pg_fetch_result($result, 0, 0);     //前回の月次更新日


            $start_day = $close_day_last;   //抽出期間の始め
            $end_day   = $close_day_this;   //抽出期間の終わり


            //WHERE句作成
            $where_sql = "";

            //取引先コード
            $where_sql .= ($client_cd1 != null) ? "    AND $t_balance_name.client_cd1 LIKE '".$client_cd1."%' \n" : "";
            $where_sql .= ($client_cd2 != null) ? "    AND $t_balance_name.client_cd2 LIKE '".$client_cd2."%' \n" : "";

            //取引先名
            if($client_name != null){
                $where_sql .= "    AND ( \n";
                $where_sql .= "        $t_balance_name.client_name1 LIKE '%".$client_name."%' OR \n";
                $where_sql .= "        $t_balance_name.client_name2 LIKE '%".$client_name."%' OR \n";
                $where_sql .= "        $t_balance_name.client_cname LIKE '%".$client_name."%' \n";
                $where_sql .= "    ) \n";
            }

            //グループ
            $where_sql .= ($client_gr != null) ? "    AND t_client.client_gr_id = $client_gr \n" : "";
            $where_sql .= ($client_gr_name != null) ? "    AND t_client_gr.client_gr_name LIKE '%$client_gr_name%' \n" : "";

            //FC・取引先区分
            $where_sql .= ($rank != null) ? "    AND t_client.rank_cd = '$rank' \n" : "";

/*
            //SV
            if($staff1 != null){
                //$where_sql .= "    AND staff1_name = '".$select_staff1[$_POST["form_staff1"]]."' \n";
                $where_sql .= "    AND staff1_name = (SELECT staff_name FROM t_staff WHERE staff_id = ".(int)$staff1.") \n";
            }

            //窓口担当1
            if($staff2 != null){
                //$where_sql .= "    AND staff2_name = '".$select_staff2[$_POST["form_staff2"]]."' \n";
                $where_sql .= "    AND staff2_name = (SELECT staff_name FROM t_staff WHERE staff_id = ".(int)$staff2.") \n";
            }
*/

            //当月/売|買/掛残高
            //開始
            if($balance_this_min != null){
                $where_sql .= "    AND ".$arp."_balance_this >= '".$balance_this_min."' \n";
            }
            //終了
            if($balance_this_max != null){
                $where_sql .= "    AND ".$arp."_balance_this <= '".$balance_this_max."' \n";
            }

            //FC・取引先区分
            if($trade_div == "buy" && $group_kind == "1"){
                if($supplier_div == "2" || $supplier_div == "3"){
                    //$where_sql .= "    AND $t_balance_name.client_div = '".$supplier_div."' \n";
                    $where_sql .= "    AND t_client.rank_cd IN ('0001', '0002') \n";
                }              
            }

            // 残高（ラジオボタン）
            if ($balance_radio == "1"){
                $where_sql .= "    AND \n";
                $where_sql .= "     ( \n";
                $where_sql .= "         ".$t_balance_name.".".$arp."_balance_last != 0          OR \n"; // 前月/売|買/掛残高
                $where_sql .= "         ".$t_balance_name.".net_".$trade_div."_amount != 0      OR \n"; // 当月/売上|仕入/額（税抜）
                $where_sql .= "         ".$t_balance_name.".tax_amount != 0                     OR \n"; // 消費税額
                $where_sql .= "         ".$t_balance_name.".intax_".$trade_div."_amount != 0    OR \n"; // 当月/売上|仕入/額（税込）
                $where_sql .= "         ".$t_balance_name.".pay_amount != 0                     OR \n"; // 今月/入金|支払/額
                $where_sql .= "          t_".$pay_div."_rebate.".$pay_div."_rebate != 0         OR \n"; // 今月/入金|支払/手数料
                $where_sql .= "         ".$t_balance_name.".tune_amount != 0                    OR \n"; // 調整額
                $where_sql .= "         ".$t_balance_name.".rest_amount != 0                    OR \n"; // 繰越額
                $where_sql .= "         ".$t_balance_name.".".$arp."_balance_this != 0 \n";             // 当月/売|買/掛残高
                $where_sql .= "     ) \n";
            }

            // 取引状態
            if ($state != "0"){
                $where_sql .= "    AND t_client.state = '$state' \n";
            }

            // 取引区分
            if ($trade != null){
                if ($trade_div == "buy" && $group_kind == "1"){
                    $where_sql .= "    AND t_client.buy_trade_id = $trade \n";
                }else{
                    $where_sql .= "    AND t_client.trade_id = $trade \n";
                }
            }

            //月次更新後表示データ抽出SQL
            $sql  = "SELECT \n";
            $sql .= "    $t_balance_name.client_cd1, \n";                   // 0 取引先CD1
            $sql .= "    $t_balance_name.client_cd2, \n";                   // 1 取引先CD2
            $sql .= "    $t_balance_name.client_cname, \n";                 // 2 取引先名（略称）
            #$sql .= "    $t_balance_name.".$arp."_balance_last, \n";        // 3 前月/売|買/掛残高
            $sql .= "    COALESCE (first_".$arp."_balance.first_".$arp."_balance, $t_balance_name.".$arp."_balance_last) AS ".$arp."_balance_last, \n";        // 3 前月/売|買/掛残高
            $sql .= "    $t_balance_name.net_".$trade_div."_amount, \n";    // 4 当月/売上|仕入/額（税抜）
            $sql .= "    $t_balance_name.tax_amount, \n";                   // 5 消費税額
            $sql .= "    $t_balance_name.intax_".$trade_div."_amount, \n";  // 6 当月/売上|仕入/額（税込）
            $sql .= "    $t_balance_name.pay_amount, \n";                   // 7 今月/入金|支払/額
            $sql .= "   COALESCE(t_".$pay_div."_rebate.".$pay_div."_rebate, 0) AS pay_rebate, \n";  // 8 今月/入金|支払/手数料額
            $sql .= "    $t_balance_name.tune_amount, \n";                  // 9 調整額
            $sql .= "    $t_balance_name.rest_amount, \n";                  //10 繰越額
            if ($trade_div == "sale"){
            $sql .= "    $t_balance_name.installment_receivable_balance, \n";         //11 割賦金額
            }else{
            $sql .= "    $t_balance_name.amortization_trade_balance, \n";         //11 割賦金額
            }

            $sql .= "    $t_balance_name.".$arp."_balance_this, \n";        //12 当月/売|買/掛残高
            $sql .= "    $t_balance_name.staff1_name, \n";                  //13 SV（本部）または契約担当1（FC）
            if($trade_div == "sale"){
                $sql .= "    $t_balance_name.staff2_name \n";               //14 窓口担当1（本部）または（FC）
            }else{
                $sql .= "    NULL, \n";
                $sql .= "    $t_balance_name.client_div \n";                //15 取引先区分
            }
            $sql .= "FROM \n";
            $sql .= "    $t_balance_name \n";
            //$sql .= "    INNER JOIN t_client ON $t_balance_name.client_id = t_client.client_id \n";
            $sql .= "    INNER JOIN t_sys_renew ON t_".$arp."_balance.monthly_close_day_this = t_sys_renew.close_day \n";
            $sql .= "    INNER JOIN t_client ON $t_balance_name.client_id = t_client.client_id \n";
            $sql .= "    LEFT JOIN t_client_gr ON t_client_gr.client_gr_id = t_client.client_gr_id \n";

            // 前回締日から今回締日までの手数料を抽出
            $sql .= "
                    LEFT JOIN 
                    ( 
            ";
            if($trade_div == "sale"){
                $sql .= Monthly_Payin_Rebate_Sql ($shop_id, $start_day, $end_day, $renew_flg);
            }else{  
                $sql .= Monthly_Payout_Rebate_Sql($shop_id, $start_day, $end_day, $renew_flg);
            }       
            $sql .= "
                    ) AS t_".$pay_div."_rebate ON t_client.client_id = t_".$pay_div."_rebate.client_id 
            ";

/*
            // 割賦金額取得（売掛残高時）
            if ($trade_div == "sale"){
                $sql .= "LEFT JOIN \n";
                $sql .= "( \n";
                $sql .= "   SELECT \n";
                $sql .= "       t_sale_h.client_id, \n";
                $sql .= "       SUM(t_installment_sales.collect_amount) AS split_balance_amount \n";
                $sql .= "   FROM \n";
                $sql .= "       t_installment_sales \n";
                $sql .= "       INNER JOIN t_sale_h \n";
                $sql .= "           ON  t_installment_sales.sale_id = t_sale_h.sale_id \n";
                $sql .= "           AND t_sale_h.trade_id = 15 \n";
                $sql .= "           AND t_sale_h.shop_id = ".$_SESSION["client_id"]." \n";
                $sql .= "   WHERE \n";
                $sql .= "       t_installment_sales.collect_day > '$end_day' \n";
                $sql .= "   GROUP BY \n";
                $sql .= "       t_sale_h.client_id \n";
                $sql .= ") \n";
                $sql .= "AS t_split_balance \n";
                $sql .= "ON t_client.client_id = t_split_balance.client_id \n";
            // 割賦金額取得（買掛残高時）
            }else{
                $sql .= "LEFT JOIN \n";
                $sql .= "( \n";
                $sql .= "   SELECT \n";
                $sql .= "       t_buy_h.client_id, \n";
                $sql .= "       SUM(t_amortization.split_pay_amount) AS split_balance_amount \n";
                $sql .= "   FROM \n";
                $sql .= "       t_amortization \n";
                $sql .= "       INNER JOIN t_buy_h \n";
                $sql .= "           ON  t_amortization.buy_id = t_buy_h.buy_id \n";
                $sql .= "           AND t_buy_h.trade_id = 25 \n";
                $sql .= "           AND t_buy_h.shop_id = ".$_SESSION["client_id"]." \n";
                $sql .= "   WHERE \n";
                $sql .= "       t_amortization.pay_day > '$end_day' \n";
                $sql .= "   GROUP BY \n";
                $sql .= "       t_buy_h.client_id \n";
                $sql .= ") \n";
                $sql .= "AS t_split_balance \n";
                $sql .= "ON t_client.client_id = t_split_balance.client_id \n";
            }
*/
			# 初期残高のみを抽出
            $sql .= "    LEFT JOIN \n";
            $sql .= "(SELECT \n";
            $sql .= "    t_first_".$arp."_balance.".$arp."_balance AS first_".$arp."_balance, \n";
            $sql .= "    t_first_".$arp."_balance.client_id, \n";
            $sql .= "    first_data.first_close_day \n";
            $sql .= "FROM \n";
            $sql .= "    t_first_".$arp."_balance \n";
            $sql .= "      INNER JOIN \n";
            $sql .= "    (SELECT \n";
            $sql .= "      client_id, \n";
            $sql .= "      MIN(monthly_close_day_this) AS first_close_day \n";
            $sql .= "    FROM \n";
            $sql .= "      ".$t_balance_name . "\n";
            $sql .= "    WHERE shop_id = ".$shop_id."\n";
            $sql .= "    GROUP BY \n";
            $sql .= "     client_id \n";
            $sql .= "    ) first_data \n";
            $sql .= "    ON first_data.client_id = t_first_".$arp."_balance.client_id \n";
			$sql .= "	) first_".$arp."_balance \n";
            $sql .= " ON ".$t_balance_name.".client_id = first_".$arp."_balance.client_id \n";
            $sql .= " AND ".$t_balance_name.".monthly_close_day_this = first_".$arp."_balance.first_close_day \n";

            $sql .= "WHERE \n";
            $sql .= "    t_client.shop_id = $shop_id \n";
            $sql .= "    AND \n";
            //本部買掛残高一覧はFCだけ抽出する
            if($group_kind == "1" && $trade_div == "buy"){
                $sql .= "    t_client.client_div = '3' \n";
                $sql .= "    AND \n";
            }
            $sql .= "    $t_balance_name.shop_id = $shop_id \n";
            $sql .= "    AND \n";
            //$sql .= "    $t_balance_name.monthly_close_day_this LIKE '$input_day-%' \n";
            $sql .= "    t_sys_renew.shop_id = $shop_id \n";
            $sql .= "    AND \n";
            $sql .= "    t_sys_renew.renew_div = '2' \n";
            $sql .= "    AND \n";
            $sql .= "    t_sys_renew.close_day LIKE '$input_day-%' \n";
            $sql .= $where_sql;
            $sql .= "ORDER BY \n";
            if($trade_div == "buy"){
                $sql .= "    client_div, \n";
            }
            $sql .= "    client_cd1, \n";
            $sql .= "    client_cd2 \n";

            $result = Db_Query($db_con, $sql.";");
            $total_count = pg_num_rows($result);    //全件数

            $sql .= "LIMIT $range \n";
            $sql .= "OFFSET $offset \n";
            $sql .= ";\n";
//print_array($sql, "月次更新後/売|買/掛残高一覧");


            $result = Db_Query($db_con, $sql);
            $data_list_count = pg_num_rows($result);    //表示データ件数
            $data_list = Get_Data($result);
//print_array($data_list);

        //始めと終わりが正しいとき（月次更新未実施の年月を指定された場合）
        }else{

            /* 次回締日を取得 */

            //自社締日(日)を取得
            $sql = "SELECT my_close_day FROM t_client WHERE client_id = $shop_id;";
            $result = Db_Query($db_con, $sql);
            $my_close_day = pg_fetch_result($result, 0, 0);     //自社締日(日)

            $close_day_this  = $input_day;
            $close_day_this .= "-";
            $close_day_this .= ($my_close_day == 29) ? date("t", mktime(0, 0, 0, $input_day_m, 1, $input_day_y)) : $my_close_day;   //入力年月の締日(年月日)


            //前回の月次更新日を取得
            $sql  = "SELECT \n";
            $sql .= "    close_day \n";
            $sql .= "FROM \n";
            $sql .= "    t_sys_renew \n";
            $sql .= "WHERE \n";
            $sql .= "    shop_id = ".$shop_id." \n";
            $sql .= "    AND \n";
            $sql .= "    renew_div = '2' \n";
            $sql .= "ORDER BY \n";
            $sql .= "    close_day DESC \n";
            $sql .= "LIMIT 1 \n";
            $sql .= ";\n";

            $result = Db_Query($db_con, $sql);
            $close_day_last = (pg_num_rows($result) == 0) ? START_DAY : pg_fetch_result($result, 0, 0);     //前回の月次更新日


            $start_day = $close_day_last;   //抽出期間の始め
            $end_day   = $close_day_this;   //抽出期間の終わり

            //始めと終わりの関係が正しい場合だけ抽出処理
            //if($start_day < $end_day){


            //WHERE句作成
            $where_sql = "";

            //取引先コード
            if($client_cd1 != null){
                $where_sql .= ($where_sql == "") ? "    WHERE \n" : "    AND \n";
                $where_sql .= "    client_cd1 LIKE '".$client_cd1."%' \n";
            }
            if($client_cd2 != null){
                $where_sql .= ($where_sql == "") ? "    WHERE \n" : "    AND \n";
                $where_sql .= "    client_cd2 LIKE '".$client_cd2."%' \n";
            }


            $client_where_sql  = "    WHERE \n";
            $client_where_sql .= "        t_client.shop_id = $shop_id \n";
            //本部買掛残高一覧はFCだけ抽出する
            if($group_kind == "1" && $trade_div == "buy"){
                $client_where_sql .= "        AND \n";
                $client_where_sql .= "        t_client.client_div = '3' \n";
            }

            //取引先名、グループ、FC・取引先
            //  だけは別の変数にいれる（メインではなくサブクエリの中で検索条件を使うため）
            if($client_name != null || $client_gr != null || $client_gr_name != null || $rank != null){

                //取引先名
                if($client_name != null){
                    $client_where_sql .= "    AND \n";
                    $client_where_sql .= "    ( \n";
                    $client_where_sql .= "        t_client.client_name  LIKE '%".$client_name."%' OR \n";
                    $client_where_sql .= "        t_client.client_name2 LIKE '%".$client_name."%' OR \n";
                    $client_where_sql .= "        t_client.client_cname LIKE '%".$client_name."%' \n";
                    $client_where_sql .= "    ) \n";
                }

                //グループ
                if($client_gr != null){
                    $client_where_sql .= "    AND \n";
                    $client_where_sql .= "    t_client.client_gr_id = $client_gr \n";
                }
                if($client_gr_name != null){
                    $client_where_sql .= "    AND \n";
                    $client_where_sql .= "    t_client_gr.client_gr_name LIKE '%$client_gr_name%' \n";
                }

                //FC・取引先区分
                if($rank != null){
                    $client_where_sql .= "    AND \n";
                    $client_where_sql .= "    t_client.rank_cd = '$rank' \n";
                }
            }

/*
            //SV
            if($staff1 != null){
                //$where_sql .= "    AND staff1_name = '".$select_staff1[$_POST["form_staff1"]]."' \n";
                $where_sql .= "    AND staff1_name = (SELECT staff_name FROM t_staff WHERE staff_id = ".(int)$staff1.") \n";
            }

            //窓口担当1
            if($staff2 != null){
                //$where_sql .= "    AND staff2_name = '".$select_staff2[$_POST["form_staff2"]]."' \n";
                $where_sql .= "    AND staff2_name = (SELECT staff_name FROM t_staff WHERE staff_id = ".(int)$staff2.") \n";
            }
*/

            //当月/売|買/掛残高
            //開始
            if($balance_this_min != null){
                $where_sql .= ($where_sql == "") ? "    WHERE \n" : "    AND \n";
                $where_sql .= "    ".$arp."_balance_this >= '".$balance_this_min."' \n";
            }
            //終了
            if($balance_this_max != null){
                $where_sql .= ($where_sql == "") ? "    WHERE \n" : "    AND \n";
                $where_sql .= "    ".$arp."_balance_this <= '".$balance_this_max."' \n";
            }

            //仕入先区分
            if($trade_div == "buy" && $group_kind == "1"){
                if($supplier_div == "2" || $supplier_div == "3"){
                    $client_where_sql .= ($client_where_sql == "") ? "    WHERE \n" : "    AND \n";
                    //$client_where_sql .= "    t_client.client_div = '".$supplier_div."' \n";
                    $client_where_sql .= "    t_client.rank_cd IN ('0001', '0002') \n";
                }              
            }

            // 残高（ラジオボタン）
            if ($balance_radio == "1"){
                $where_sql .= ($where_sql == "") ? "    WHERE \n" : "    AND \n";
                $where_sql .= "     ( \n";
                $where_sql .= "         ".$arp."_balance_last != 0          OR \n"; // 前月/売|買/掛残高
                $where_sql .= "         net_".$trade_div."_amount != 0      OR \n"; // 当月/売上|仕入/額（税抜）
                $where_sql .= "         tax_amount != 0                     OR \n"; // 消費税額
                $where_sql .= "         intax_".$trade_div."_amount != 0    OR \n"; // 当月/売上|仕入/額（税込）
                $where_sql .= "         pay_amount != 0                     OR \n"; // 今月/入金|支払/額
                $where_sql .= "         tune_amount != 0                    OR \n"; // 調整額
                $where_sql .= "         rest_amount != 0                    OR \n"; // 繰越額
                $where_sql .= "         ".$arp."_balance_this != 0 \n";             // 当月/売|買/掛残高
                $where_sql .= "     ) \n";
            }

            // 取引状態
            if ($state != "0"){
                $client_where_sql .= ($client_where_sql == "") ? "    WHERE \n" : "    AND \n";
                $client_where_sql .= "    t_client.state = '$state' \n";
            }

            // 取引区分
            if ($trade != null){
                if ($trade_div == "buy" && $group_kind == "1"){
                    $client_where_sql .= ($client_where_sql == "") ? "    WHERE \n" : "    AND \n";
                    $client_where_sql .= "    t_client.buy_trade_id = $trade \n";
                }else{
                    $client_where_sql .= ($client_where_sql == "") ? "    WHERE \n" : "    AND \n";
                    $client_where_sql .= "    t_client.trade_id = $trade \n";
                }
            }

            $sql  = "SELECT \n";
            $sql .= "    client_cd1, \n";                   // 0 取引先CD1
            $sql .= "    client_cd2, \n";                   // 1 取引先CD2
            $sql .= "    client_cname, \n";                 // 2 取引先名（略称）
            $sql .= "    ".$arp."_balance_last, \n";        // 3 前月/売|買/掛残高
            $sql .= "    net_".$trade_div."_amount, \n";    // 4 当月/売上|仕入/額（税抜）
            $sql .= "    tax_amount, \n";                   // 5 消費税額
            $sql .= "    intax_".$trade_div."_amount, \n";  // 6 当月/売上|仕入/額（税込）
            $sql .= "    pay_amount, \n";                   // 7 今月/入金|支払/額
            $sql .= "    pay_rebate, \n";                   // 8 今月/入金|支払/手数料額
            $sql .= "    tune_amount, \n";                  // 9 調整額
            $sql .= "    rest_amount, \n";                  //10 繰越額
            $sql .= "    split_balance_amount, \n";         //11 割賦金額
            $sql .= "    ".$arp."_balance_this, \n";        //12 当月/売|買/掛残高
            $sql .= "    staff1_name, \n";                  //13 SV（本部）または契約担当1（FC）
            $sql .= "    staff2_name, \n";                  //14 窓口担当1（本部）または（FC）
            $sql .= "    client_div \n";                    //15 取引先区分
            $sql .= "FROM \n";
            $sql .= "    ( \n";

            $sql .= "
                SELECT 
                    t_client.client_cd1, 
                    t_client.client_cd2, 
                    t_client.client_cname, 
                    COALESCE(t_".$arp."_balance_last.".$arp."_balance_last, 0) AS ".$arp."_balance_last, 
                    ( COALESCE(t_".$trade_div."_amount.net_".$trade_div."_amount, 0) 
                      + COALESCE(t_royalty.royalty_price, 0) 
                    ) AS net_".$trade_div."_amount, 
                    ( COALESCE(t_".$trade_div."_amount.tax_amount, 0) 
                      + COALESCE(t_royalty.royalty_tax, 0) 
                      + COALESCE(t_adjust_tax.adjust_tax, 0) 
                    ) AS tax_amount, 
                    ( COALESCE(t_".$trade_div."_amount.net_".$trade_div."_amount, 0) 
                      + COALESCE(t_royalty.royalty_price, 0) 
                      + COALESCE(t_".$trade_div."_amount.tax_amount, 0) 
                      + COALESCE(t_royalty.royalty_tax, 0) 
                      + COALESCE(t_adjust_tax.adjust_tax, 0) 
                    ) AS intax_".$trade_div."_amount, 
                    COALESCE(t_".$pay_div."_amount.".$pay_div."_amount, 0) AS pay_amount, 
                    COALESCE(t_".$pay_div."_rebate.".$pay_div."_rebate, 0) AS pay_rebate, 
                    COALESCE(t_".$pay_div."_amount.tune_amount, 0) AS tune_amount, 
                    ( COALESCE(t_".$arp."_balance_last.".$arp."_balance_last, 0) 
                      - COALESCE(t_".$pay_div."_amount.".$pay_div."_amount, 0) 
                      - COALESCE(t_".$pay_div."_amount.tune_amount, 0) 
                    ) AS rest_amount, 
                    ( COALESCE(t_".$arp."_balance_last.".$arp."_balance_last, 0) 
                      - COALESCE(t_".$pay_div."_amount.".$pay_div."_amount, 0) 
                      - COALESCE(t_".$pay_div."_amount.tune_amount, 0) 
                      + COALESCE(t_".$trade_div."_amount.net_".$trade_div."_amount, 0) 
                      + COALESCE(t_royalty.royalty_price, 0) 
                      + COALESCE(t_".$trade_div."_amount.tax_amount, 0) 
                      + COALESCE(t_royalty.royalty_tax, 0) 
                      + COALESCE(t_adjust_tax.adjust_tax, 0) 
                    ) AS ".$arp."_balance_this, 
            ";
            //売掛残高一覧の場合の担当者取得
            if($trade_div == "sale"){
                //本部画面ではFCマスタのSV、窓口担当1を表示
                if($group_kind == "1"){
                    $sql .= "
                    (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.sv_staff_id) AS staff1_name, 
                    (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.b_staff_id1) AS staff2_name, 
                    ";
                //FC画面では得意先マスタの契約担当1、契約担当2を表示
                }else{
                    $sql .= "
                    (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.c_staff_id1) AS staff1_name, 
                    (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.c_staff_id2) AS staff2_name, 
                    ";
                }

            //買掛残高一覧の場合の担当者
            }else{
                //本部画面、FC画面とも、仕入先の場合は契約担当1、FCの場合はSVを表示
                //（2つ目は買掛残高一覧では使わないのでNULL）
                $sql .= "
                    CASE t_client.client_div 
                        WHEN '2' THEN (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.c_staff_id1) 
                        ELSE          (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.sv_staff_id) 
                    END AS staff1_name, 
                    NULL AS staff2_name, 
                ";
            }

            $sql .= "
                    t_client.client_div, 
                    t_split_balance.split_balance_amount 

                FROM 
            ";

            //締日以前に/売上|仕入/、/入金|支払/、月次更新（/売|買/掛残高初期設定）のあった取引先IDを全て抽出
            $sql .= "
                    ( 
            ";
            $sql .= Monthly_All_Client_Sql_For_Balance($shop_id, $trade_div, $end_day, $renew_flg);
            $sql .= "
                    ) AS t_all_client 

                    INNER JOIN t_client ON t_all_client.client_id = t_client.client_id 
                    LEFT JOIN t_client_gr ON t_client_gr.client_gr_id = t_client.client_gr_id 
            ";

            //前回月次更新時の月次更新日、売掛残高を抽出
            $sql .= "
                    LEFT JOIN 
                    ( 
            ";
            $sql .= Monthly_Balance_Last_Sql($shop_id, $trade_div);
            $sql .= "
                    ) AS t_".$arp."_balance_last ON t_all_client.client_id = t_".$arp."_balance_last.client_id 
            ";

            //前回締日から今回締日までの/売上|仕入/額（税抜）、消費税額、/売上|仕入/額（税込）を抽出
            $sql .= "
                    LEFT JOIN 
                    ( 
            ";
            if($trade_div == "sale"){
                $sql .= Monthly_Sale_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
            }else{
                $sql .= Monthly_Buy_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
            }
            $sql .= "
                    ) AS t_".$trade_div."_amount ON t_all_client.client_id = t_".$trade_div."_amount.client_id 
            ";

            //前回締日から今回締日までの入金額（除調整額）、調整額を抽出
            $sql .= "
                    LEFT JOIN 
                    ( 
            ";
            if($trade_div == "sale"){
                $sql .= Monthly_Payin_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
            }else{
                $sql .= Monthly_Payout_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
            }
            $sql .= "
                    ) AS t_".$pay_div."_amount ON t_all_client.client_id = t_".$pay_div."_amount.client_id 
            ";

            // 前回締日から今回締日までの手数料を抽出
            $sql .= "
                    LEFT JOIN 
                    ( 
            ";
            if($trade_div == "sale"){
                $sql .= Monthly_Payin_Rebate_Sql($shop_id, $start_day, $end_day, $renew_flg);
            }else{
                $sql .= Monthly_Payout_Rebate_Sql($shop_id, $start_day, $end_day, $renew_flg);
            }
            $sql .= "
                    ) AS t_".$pay_div."_rebate ON t_all_client.client_id = t_".$pay_div."_rebate.client_id 
            ";

            //前回締日から今回締日までのロイヤリティ額、その消費税額を抽出
            $sql .= "
                    LEFT JOIN 
                    ( 
            ";
            $sql .= Monthly_Lump_Amount_Sql($shop_id, $start_day, $end_day, "1", $trade_div);
            $sql .= "
                    ) AS t_royalty ON t_all_client.client_id = t_royalty.client_id 
            ";

            //前回締日から今回締日までの一括消費税額を抽出
            $sql .= "
                    LEFT JOIN 
                    ( 
            ";
            $sql .= Monthly_Lump_Amount_Sql($shop_id, $start_day, $end_day, "2", $trade_div);
            $sql .= "
                    ) AS t_adjust_tax ON t_all_client.client_id = t_adjust_tax.client_id 
            ";


            // 割賦金額取得（売掛残高時）
            if ($trade_div == "sale"){
                $sql .= "LEFT JOIN \n";
                $sql .= "( \n";
                $sql .= "   SELECT \n";
                $sql .= "       t_sale_h.client_id, \n";
                $sql .= "       SUM(t_installment_sales.collect_amount) AS split_balance_amount \n";
                $sql .= "   FROM \n";
                $sql .= "       t_installment_sales \n";
                $sql .= "       INNER JOIN t_sale_h \n";
                $sql .= "           ON  t_installment_sales.sale_id = t_sale_h.sale_id \n";
                $sql .= "           AND t_sale_h.trade_id = 15 \n";
                $sql .= "           AND t_sale_h.shop_id = ".$_SESSION["client_id"]." \n";
                $sql .= "   WHERE \n";
                $sql .= "       t_installment_sales.collect_day > '$end_day' \n";
                $sql .= "        AND \n";
                $sql .= "       t_sale_h.sale_day <= '$end_day' \n";
                $sql .= "   GROUP BY \n";
                $sql .= "       t_sale_h.client_id \n";
                $sql .= ") \n";
                $sql .= "AS t_split_balance \n";
                $sql .= "ON t_all_client.client_id = t_split_balance.client_id";
            // 割賦金額取得（買掛残高時）
            }else{
                $sql .= "LEFT JOIN \n";
                $sql .= "( \n";
                $sql .= "   SELECT \n";
                $sql .= "       t_buy_h.client_id, \n";
                $sql .= "       SUM(t_amortization.split_pay_amount) AS split_balance_amount \n";
                $sql .= "   FROM \n";
                $sql .= "       t_amortization \n";
                $sql .= "       INNER JOIN t_buy_h \n";
                $sql .= "           ON  t_amortization.buy_id = t_buy_h.buy_id \n";
                $sql .= "           AND t_buy_h.trade_id = 25 \n";
                $sql .= "           AND t_buy_h.shop_id = ".$_SESSION["client_id"]." \n";
                $sql .= "   WHERE \n";
                $sql .= "       t_amortization.pay_day > '$end_day' \n";
                $sql .= "        AND \n";
                $sql .= "       t_buy_h.buy_day <= '$end_day' \n";
                $sql .= "   GROUP BY \n";
                $sql .= "       t_buy_h.client_id \n";
                $sql .= ") \n";
                $sql .= "AS t_split_balance \n";
                $sql .= "ON t_all_client.client_id = t_split_balance.client_id \n";
            }


            $sql .= $client_where_sql;  //得意先名での検索条件

            $sql .= "    ) AS $t_balance_name \n";

            $sql .= $where_sql;         //得意先名以外の検索条件

            $result = Db_Query($db_con, $sql.";");
            $total_count = pg_num_rows($result);    //全件数

            $sql .= "ORDER BY \n";
            if($trade_div == "buy"){
                $sql .= "    client_div, \n";
            }
            $sql .= "    client_cd1, \n";
            $sql .= "    client_cd2 \n";
            $sql .= "LIMIT $range \n";
            $sql .= "OFFSET $offset \n";
            $sql .= ";\n";
//print_array($sql, "月次更新前/売|買/掛集計SQL：");


            $result = Db_Query($db_con, $sql);
            $data_list_count = pg_num_rows($result);    //表示データ件数
            $data_list = Get_Data($result);
//print_array($data_list);


            //始めと終わりが正しくない時
/*
            }else{
                //$total_count = 0;   //全件数
                $data_list_count = 0;   //表示データ件数
                $data_list = null;      //表示データ件数
            }
*/

        }//月次更新前データの抽出終わり

        for($i=0;$i<$data_list_count;$i++){
            $disp_data[$i][0]  = $data_list[$i][0]; //取引先CD1
            $disp_data[$i][1]  = $data_list[$i][1]; //取引先CD2
            $disp_data[$i][2]  = $data_list[$i][2]; //取引先名（略称）
            //前月売掛残高がマイナスの場合は青字、0以上の場合は赤字に
            $money_color = ($data_list[$i][3] < 0) ? "#3366FF" : "red";

            // 入金/支払額から手数料分を引く
            $data_list[$i][7] -= $data_list[$i][8];

            for($j=3;$j<=12;$j++){
                //合計欄に表示する金額集計
                $total_money[$j] = $total_money[$j] + $data_list[$i][$j];

                //マイナス金額に色をつける＆number_format
                if($data_list[$i][$j] < 0){
                    $disp_data[$i][$j] = "<font color=\"$money_color\">".number_format($data_list[$i][$j])."</font>";
                }else{
                    $disp_data[$i][$j] = number_format($data_list[$i][$j]);
                }
            }

            $disp_data[$i][13] = ($data_list[$i][14] == null) ? "&nbsp;" : $data_list[$i][13];  //SV（本部）または契約担当1（FC）
            $disp_data[$i][14] = ($data_list[$i][14] == null) ? "&nbsp;" : $data_list[$i][14];  //窓口担当1（本部）または（FC）

            $disp_data[$i][15] = $data_list[$i][15];    //取引先区分

            $disp_data[$i][16] = $offset + $i + 1;      //行番号

        }

        //合計金額をnumber_format
        for($j=3;$j<=11;$j++){
            $total_money[$j] = Minus_Numformat($total_money[$j]);
        }

    }//エラーじゃない場合の処理終わり

}//表示ボタン押下時の処理終わり


//$total_count = ($total_count != null) ? $total_count : 0;


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
//$page_menu = Create_Menu_h('sale','5');

/****************************/
//画面ヘッダー作成
/****************************/
$page_header = Create_Header($page_title);


/****************************/
//ページ作成
/****************************/

//表示範囲指定
//$range = "20";

//ページ数を取得
//$page_count = $_POST["f_page1"];

//全件表示、または全件数が表示件数以下の場合
if($range == "ALL" || $total_count <= $range){
    $range = $total_count;
    $page_count = null;
}

$html_page  = Html_Page($total_count, $page_count, 1, $range);
//$html_page  = Html_Page($total_count, $page_count, 1, $total_count);
$html_page2 = Html_Page($total_count, $page_count, 2, $range);


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
    'html_page'     => "$html_page",
    'html_page2'    => "$html_page2",
    'input_day'     => "$input_day",
    'data_list_count'   => "$data_list_count",
    'group_kind'    => "$group_kind",
    'trade_div'     => "$trade_div",
    "err_flg"       => "$err_flg",
));

//テーブル表示データをassign
$smarty->assign('disp_data', $disp_data);
$smarty->assign('total_money', $total_money);


//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));


//print_array($form, "form");

//print_array($_POST);


?>
