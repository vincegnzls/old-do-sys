<?php
/********************
 * 売上確定一覧
 *
 *
 * 変更履歴
 *    2006/09/11 (kaji)
 *      ・伝票番号のリンク先をfreezeした変更画面に変更
 *    2006/09/20 (kaji)
 *      ・画面名称変更（確定済売上伝票一覧→売上確定一覧）
 *      ・表示件数が10件に戻るのを修正
 *    2006/10/10 (suzuki)
 *      ・０件を表示し、再度検索するとエラーになるのを修正
 *    2006/10/25 (kaji)
 *      ・巡回担当者の検索はメインだけ引っかかるように変更
 *    2006/10/26 (suzuki)
 *      ・売上率が0%の担当者も表示するように変更
 *    2006/10/30 (suzuki)
 *      ・伝票発行押下時に受注情報を更新しないように変更
 *    2006/12/04 (suzuki)
 *      ・商品分類名・正式名称の抽出SQLを変更
 *
 ********************/
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/11      03-003      kajioka-h   割賦の場合は掛の方に金額を表示するようにした
 *                  03-049      kajioka-h   売上金額の数値チェックを追加
 *  2006/12/07      bun_0062　　suzuki　　　日付をゼロ埋め
 *  2006/12/09      03-058　　  suzuki　　　割賦売上は掛売金額に含まれるように修正
 *  2006/12/12      03-055　　  suzuki　　　割賦売上の分割回数を表示するように修正
 *  2006/12/27      xx-xxx      kajioka-h   伝票番号での検索を範囲指定に
 *  2007/01/05      xx-xxx      kajioka-h   巡回担当者(メイン1)のセレクトボックスを契約マスタのセレクトボックスと同じに
 *  2007/01/06      xx-xxx      kajioka-h   直営の場合は代行者で検索できるようにした
 *  2007/02/22      xx-xxx      kajioka-h   帳票、CSV出力機能を削除
 *  2007/03/05      作業項目12  ふくだ      掛・現金の合計を出力
 *  2007/03/06      xx-xxx      kajioka-h   担当支店で検索機能を追加
 *  2007/03/15      xx-xxx      ふくだ      全ての検索項目を共通のものに変更（WHERE句なども変更）
 *  2007/03/20      xx-xxx      watanabe-k  ヘッダのスリム化、日時更新日の表示に変更、売上伝票発行⇒再発行に変更
 *                  xx-xxx      kajioka-h   取消リンクをなくす（契約からの伝票も「削除」リンクを使う）
 *  2007/03/28      要望21他    kajioka-h   予定からの売上削除時、元の予定に引当を行うように変更
 *  2007/04/05      その他25    kajioka-h   代行料・紹介料を本部の仕入する場合のチェック処理追加
 *  2007/04/06                  watanabe-k  伝票発行時に出荷案内出力の有無を選択できるように修正
 *  2007/04/09                  fukuda      共通フォームのエラーチェックは関数ひとつで実行するよう修正
 *                  その他25    kajioka-h   代行料・紹介料を本部の仕入が日次更新されたら変更・削除リンクを表示しない
 *  2007/04/12      xx-xxx      kajioka-h   オフライン代行の削除を可能に
 *  2007/06/10      xx-xxx      watanabe-k  出荷案内書発行の有無の選択を無効にするように修正
 *
 *      （このへんでふくだがソート機能をつけた）
 *
 *  2007-06-21                  fukuda      合計行の額を＋と−に分割
 *  2007/06/22      xx-xxx      kajioka-h   CSV出力機能追加
 *                              kajioka-h   代行料、紹介料の符号を仕入の取引区分を見るように変更
 *  2007-06-29                  fukuda      CSV出力時、同一伝票内に複数商品がある場合でもヘッダ部データは省略しないよう修正
 *  2007-07-03                  watanabe-k  再発行列を発行ステータスが分かるような形に変更
 *  2007-07-10                  watanabe-k  原価を表示するように修正
 *  2007-07-11                  fukuda      一覧の1行目にも合計行を出力する
 *  2007-07-12                  watanabe-k  伝票発行の無を他表に変更
 *  2007-07-25                  watanabe-k  CSVに得意先名２を表示
 *  2007-08-17                  fukuda      CSVにサービスコードを追加
 *  2007-10-05                  watanabe-k  ショップの機能でCSVを出力した場合にヘッダと内容が一致しないバグの修正
 *  2007-11-03                  watanabe-k  CSVにグループ表示するように修正 
 *  2009-09-21                  watanabe-k  定期リピート機能を追加 
 *  2009/09/28      なし        hashimoto-y 取引区分から値引きを廃止
 *  2009/10/08      なし        hashimoto-y Rev.1.0から存在した潜在バグ修正
 *  2010/04/28      Rev.1.5     hashimoto-y アイテムと販売区分の検索を追加(Rev.1.5)
 *
 */

// ページ名
$page_title = "売上確定一覧";

// 環境設定ファイル
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");

//取引区分の関数
require_once(PATH ."function/trade_fc.fnc");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

#print_r($_POST);
/****************************/
// 検索条件復元関連
/****************************/
// 検索フォーム初期値配列
$ary_form_list = array(
    "form_display_num"  => "1", 
    "form_output_type"  => "1",
    "form_client_branch"=> "",
    "form_attach_branch"=> "",
    "form_client"       => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_round_staff"  => array("cd" => "", "select" => ""),
    "form_part"         => "",
    "form_claim"        => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_multi_staff"  => "",
    "form_ware"         => "",
    "form_charge_fc"    => array("cd1" => "", "cd2" => "", "name" => "", "select" => array("0" => "", "1" => "")),
    "form_claim_day"    => array("sy" => "", "sm" => "", "sd" => "", "ey" => "", "em" => "", "ed" => ""), 
    "form_slip_no"      => array("s" => "", "e" => ""),
    "form_sale_amount"  => array("s" => "", "e" => ""),
    "form_sale_day"     => array(
        "sy" => date("Y"),
        "sm" => date("m"),
        "sd" => "01",
        "ey" => date("Y"),
        "em" => date("m"),
        "ed" => date("t", mktime(0, 0, 0, date("m"), date("d"), date("Y")))
    ),
    "form_contract_div" => "1",
    "form_client_gr"    => array("name" => "", "select" => ""),
    "form_trade"        => "",
    "form_renew"        => "1",
	"form_repeat"		=> "1"
);

$ary_pass_list = array(
    "form_output_type"  => "1",
);

// 検索条件復元
//Restore_Filter2($form, "sale", "form_display", $ary_form_list);
Restore_Filter2($form, "sale", "form_display", $ary_form_list, $ary_pass_list);


/****************************/
// 外部変数取得
/****************************/
$shop_id   = $_SESSION["client_id"];


/****************************/
// 初期値設定
/****************************/
$limit          = null;     // LIMIT
$offset         = "0";      // OFFSET
$total_count    = "0";      // 全件数
$page_count     = ($_POST["f_page1"] != null) ? $_POST["f_page1"] : "1";    // 表示ページ数


/****************************/
// フォームパーツ定義
/****************************/
/* 共通フォーム */
Search_Form($db_con, $form, $ary_form_list, true);

// 予定巡回日を削除
$form->removeElement("form_round_day");

/* モジュール別フォーム */
// 伝票番号（開始〜終了）
$obj    =   null;
$obj[]  =&  $form->createElement("text", "s", "", "size=\"10\" maxlength=\"8\" class=\"ime_disabled\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", "〜");
$obj[]  =&  $form->createElement("text", "e", "", "size=\"10\" maxlength=\"8\" class=\"ime_disabled\" $g_form_option");
$form->addGroup($obj, "form_slip_no", "", "");

// 売上金額（開始〜終了）
Addelement_Money_Range($form, "form_sale_amount", "", "");

// 売上計上日（開始〜終了）
Addelement_Date_Range($form, "form_sale_day", "売上計上日", "-");

// 契約区分
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "全て",           "1");
$obj[]  =&  $form->createElement("radio", null, null, "通常",           "2");
$obj[]  =&  $form->createElement("radio", null, null, "オンライン代行", "3");
$obj[]  =&  $form->createElement("radio", null, null, "オフライン代行", "4");
$form->addGroup($obj, "form_contract_div", "", "");

// グループ
$item   =   null;
$item   =   Select_Get($db_con, "client_gr");
$obj    =   null;
$obj[]  =   $form->createElement("text", "name", "", "size=\"34\" maxLength=\"25\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", " ");
$obj[]  =   $form->createElement("select", "select", "",$item, $g_form_option_select);
$form->addGroup($obj, "form_client_gr", "", "");

// 日次更新
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "全て",   "1");
$obj[]  =&  $form->createElement("radio", null, null, "未実施", "2");
$obj[]  =&  $form->createElement("radio", null, null, "実施済", "3");
$form->addGroup($obj, "form_renew", "", "");

// 取引区分
$item   =   null;   
$item   =   Select_Get($db_con, "trade_sale");

#2009-09-28 hashimoto-y
#$form->addElement("select", "form_trade", "", $item, $g_form_option_select);

$trade_form=$form->addElement('select', 'form_trade', null, null, $g_form_option_select);

#値引きを廃止
$select_value_key = array_keys($item);
for($i = 0; $i < count($item); $i++){
    if( $select_value_key[$i] != 14 && $select_value_key[$i] != 64){
        $trade_form->addOption($item[$select_value_key[$i]], $select_value_key[$i]);
    }
}

// 定期リピート
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "全て",           "1");
$obj[]  =&  $form->createElement("radio", null, null, "定期",           "2");
$obj[]  =&  $form->createElement("radio", null, null, "不定期",         "3");
$form->addGroup($obj, "form_repeat", "", "");

#2010-04-26 hashimoto-y
// 商品
$obj    =   null;
$obj[]  =&  $form->createElement("text", "cd", "", "size=\"10\" maxLength=\"8\" class=\"ime_disabled\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", " ");
$obj[]  =&  $form->createElement("text", "name", "", "size=\"34\" maxLength=\"15\" $g_form_option");
$form->addGroup($obj, "form_goods", "", "");

#2010-04-26 hashimoto-y
//販売区分
$array_divide = Select_Get($db_con, "divide_con");
$form->addElement('select', "form_divide", "", $array_divide, "$g_form_option_select");


// ソートリンク
$ary_sort_item = array(
    "sl_client_cd"      => "得意先コード",
    "sl_client_name"    => "得意先名",
    "sl_slip"           => "伝票番号",
    "sl_sale_day"       => "売上計上日",
    "sl_round_staff"    => "巡回担当者<br>（メイン１）",
    "sl_act_client_cd"  => "代行先コード",
    "sl_act_client_name"=> "代行先名",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_sale_day");

// 表示ボタン
$form->addElement("submit", "form_display", "表　示");

// クリアボタン
$form->addElement("button","form_clear","クリア", "onClick=\"javascript:location.href('".$_SERVER["PHP_SELF"]."');\"");

// 処理フラグ
$form->addElement("hidden", "sale_slip_flg");       // 発行フラグ
$form->addElement("hidden", "sale_republish_flg");  // 再発行フラグ
$form->addElement("hidden", "hdn_cancel_id");       // 取消売上ID
$form->addElement("hidden", "hdn_delete_id");       // 削除売上ID


/***************************/
// 発行ボタン押下時
/***************************/
if ($_POST["sale_slip_flg"] == "true"){

    // 伝票配列がPOSTされている場合
    if ($_POST["form_slip_check"] != null){

        $ary_output_sale = null;    // 伝票発行売上ID配列

        // POSTされた伝票チェックでループ
        foreach ($_POST["form_slip_check"] as $key => $value){
            // 値が1の場合、伝票発行売上ID配列へ
            if ($value == "1"){
                $ary_output_sale[] = $_POST["output_id_array"][$key];
            }
        }

    }

    // チェックが付いていなかった場合
    if ($ary_output_sale == null){
        // エラーメッセージ
        $err_msg_print = "発行する伝票が選択されていません。";
    }

    // 再発行フラグクリア
    $clear_data["sale_slip_flg"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}

/***************************/
// 再発行ボタン押下時
/***************************/
if ($_POST["sale_republish_flg"] == "true"){

    // 伝票配列がPOSTされている場合
    if ($_POST["form_republish_check"] != null){

        $ary_output_sale = null;    // 伝票発行売上ID配列

        // POSTされた伝票チェックでループ
        foreach ($_POST["form_republish_check"] as $key => $value){
            // 値が1の場合、伝票発行売上ID配列へ
            if ($value == "1"){
                $ary_output_sale[] = $_POST["output_id_array"][$key];
            }
        }

    }

    // チェックが付いていなかった場合
    if ($ary_output_sale == null){
        // エラーメッセージ
        $err_msg_print = "再発行する伝票が選択されていません。";
    }

    // 再発行フラグクリア
    $clear_data["sale_republish_flg"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}



/***************************/
// 削除リンク押下時（予定からの伝票）
/***************************/
if ($_POST["hdn_cancel_id"] != null){

    Db_Query($db_con, "BEGIN;");

    // 受注ID、手書伝票フラグ、取得
    $sql = "SELECT aord_id, renew_flg, intro_account_id, intro_amount, contract_div FROM t_sale_h WHERE sale_id = ".$_POST["hdn_cancel_id"].";";
    $result = Db_Query($db_con, $sql);
    if($result == false){ 
        Db_Query($db_con, "ROLLBACK;");
        exit;
    }
    $cancel_aord_id = pg_fetch_result($result, 0, "aord_id");       // 削除対象の受注ID
    $renew_flg      = pg_fetch_result($result, 0, "renew_flg");     // 日次更新フラグ
    $intro_account_id   = pg_fetch_result($result, 0, "intro_account_id");      // 紹介者ID
    $intro_amount   = pg_fetch_result($result, 0, "intro_amount");  // 紹介料
    $contract_div   = pg_fetch_result($result, 0, "contract_div");  // 契約区分

    //代行伝票の場合、代行料仕入が日次更新されてないかチェック
    if($_SESSION["group_kind"] == "2" && $contract_div != "1"){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE act_sale_id = ".$_POST["hdn_cancel_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){ 
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $act_buy_renew_err = "業務代行料 の仕入伝票が本部で日次更新されているため削除できません。";
        }
    }

    //紹介料の仕入が存在する場合、紹介料仕入が日次更新されてないかチェック
    if($intro_account_id != null && $intro_amount > 0){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE intro_sale_id = ".$_POST["hdn_cancel_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){ 
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $intro_buy_renew_err = "紹介口座料 の仕入伝票が本部で日次更新されているため削除できません。";
        }
    }


    //エラーじゃない場合、処理開始
    if($renew_flg == "f" && $act_buy_renew_err == null && $intro_buy_renew_err == null){

        // 売上伝票削除
        $sql  = "DELETE FROM \n";
        $sql .= "   t_sale_h \n";
        $sql .= "WHERE \n";
        $sql .= "   sale_id = ".$_POST["hdn_cancel_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if ($res === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        // 受注ヘッダの確定フラグをfalseに
        $sql  = "UPDATE \n";
        $sql .= "   t_aorder_h \n";
        $sql .= "SET \n";
        $sql .= "   confirm_flg = 'f', \n";
        $sql .= "   ps_stat = '1' \n";
        $sql .= "WHERE \n";
        $sql .= "   aord_id = $cancel_aord_id \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if ($res === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        //以下、自社巡回、またはオンライン代行の場合
        if($contract_div == "1" || $contract_div == "2"){


            // オンライン代行の場合は、受注ヘッダの確定フラグ(受託先用)をfalse・取消フラグをtrue
            $sql  = "UPDATE \n";
            $sql .= "   t_aorder_h \n";
            $sql .= "SET \n";
            $sql .= "   trust_confirm_flg = 'f', \n";
            $sql .= "   cancel_flg = 't' \n";
            $sql .= "WHERE \n";
            $sql .= "   contract_div = '2' \n";
            $sql .= "AND \n";
            $sql .= "   aord_id = $cancel_aord_id \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if ($res === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            // 引当てる倉庫IDを取得
            $move_ware_id = FC_Move_Ware_Id($db_con, $cancel_aord_id);
            if($move_ware_id === false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            // 受注IDから自社巡回の場合はショップID、代行の場合は代行先IDを取得
            $sql  = "SELECT \n";
            $sql .= "    CASE contract_div \n";
            $sql .= "        WHEN '1' THEN shop_id \n";
            $sql .= "        ELSE act_id \n";
            $sql .= "    END \n";
            $sql .= "FROM \n";
            $sql .= "    t_aorder_h \n";
            $sql .= "WHERE \n";
            $sql .= "    aord_id = $cancel_aord_id \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if($res == false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
            $move_shop_id = pg_fetch_result($res, 0, 0);    // 実際に巡回を行うショップID

            // 出庫品テーブルから引当る受注データID、商品ID、数量等を取得
            $sql  = "SELECT \n";
            $sql .= "    t_aorder_h.client_id, \n";     // 0 得意先ID
            $sql .= "    t_aorder_h.client_cname, \n";  // 1 得意先名（略称）
            $sql .= "    t_aorder_h.ord_no, \n";        // 2 伝票番号
            $sql .= "    t_aorder_ship.aord_d_id, \n";  // 3 受注データID
            $sql .= "    t_aorder_ship.goods_id, \n";   // 4 商品ID
            $sql .= "    t_aorder_ship.num \n";         // 5 数量
            $sql .= "FROM \n";
            $sql .= "    t_aorder_h \n";
            $sql .= "    INNER JOIN t_aorder_d ON t_aorder_h.aord_id = t_aorder_d.aord_id \n";
            $sql .= "    INNER JOIN t_aorder_ship ON t_aorder_d.aord_d_id = t_aorder_ship.aord_d_id \n";
            $sql .= "WHERE \n";
            $sql .= "    t_aorder_h.aord_id = $cancel_aord_id \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if($res == false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
            $move_goods_data = Get_Data($res, 3);    // 引当る商品だとか
            $move_goods_data_count = pg_num_rows($res);

            for($i = 0; $i < $move_goods_data_count; $i++){
                // 作業区分「1：引当」、入出庫区分「2：出庫」
                $sql  = "INSERT INTO \n";
                $sql .= "   t_stock_hand \n";
                $sql .= "( \n";
                $sql .= "   goods_id, \n";
                $sql .= "   enter_day, \n";
                $sql .= "   work_day, \n";
                $sql .= "   work_div, \n";
                $sql .= "   client_id, \n";
                $sql .= "   client_cname, \n";
                $sql .= "   ware_id, \n";
                $sql .= "   io_div, \n";
                $sql .= "   num, \n";
                $sql .= "   slip_no, \n";
                $sql .= "   aord_d_id, \n";
                $sql .= "   staff_id, \n";
                $sql .= "   shop_id \n";
                $sql .= ")VALUES( \n";
                $sql .= "   ".$move_goods_data[$i][4].", \n";
                $sql .= "   CURRENT_TIMESTAMP, \n";
                $sql .= "   CURRENT_TIMESTAMP, \n";
                $sql .= "   '1', \n";
                $sql .= "   ".$move_goods_data[$i][0].", \n";
                $sql .= "   '".$move_goods_data[$i][1]."', \n";
                $sql .= "   $move_ware_id, \n";
                $sql .= "   '1', \n";
                $sql .= "   ".$move_goods_data[$i][5].", \n";
                $sql .= "   '".$move_goods_data[$i][2]."', \n";
                $sql .= "   ".$move_goods_data[$i][3].", \n";
                $sql .= "   ".$_SESSION["staff_id"].", \n";
                $sql .= "   $move_shop_id \n";
                $sql .= ") \n";
                $sql .= ";";
                $res = Db_Query($db_con, $sql);
                if($res == false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }
            }
        }

        Db_Query($db_con, "COMMIT;");

    }else{
        Db_Query($db_con, "ROLLBACK;");
    }

    // 伝票取消フラグクリア
    $clear_data["hdn_cancel_id"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}


/****************************/
// 削除リンク押下時（手書伝票）
/****************************/
if($_POST["hdn_delete_id"] != null){

    // 日次更新フラグ、紹介者ID、紹介料、契約区分、取得
    $sql = "SELECT renew_flg, intro_account_id, intro_amount, contract_div FROM t_sale_h WHERE sale_id = ".$_POST["hdn_delete_id"].";";
    $result = Db_Query($db_con, $sql);
    if($result == false){
        Db_Query($db_con, "ROLLBACK;");
        exit;
    }
    $renew_flg      = pg_fetch_result($result, 0, "renew_flg");     // 日次更新フラグ
    $intro_account_id   = pg_fetch_result($result, 0, "intro_account_id");      // 紹介者ID
    $intro_amount   = pg_fetch_result($result, 0, "intro_amount");  // 紹介料
    $contract_div   = pg_fetch_result($result, 0, "contract_div");  // 契約区分

    //代行伝票の場合、代行料仕入が日次更新されてないかチェック
    if($_SESSION["group_kind"] == "2" && $contract_div != "1"){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE act_sale_id = ".$_POST["hdn_delete_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $act_buy_renew_err = "業務代行料 の仕入伝票が本部で日次更新されているため削除できません。";
        }
    }

    //紹介料の仕入が存在する場合、紹介料仕入が日次更新されてないかチェック
    if($intro_account_id != null && $intro_amount > 0){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE intro_sale_id = ".$_POST["hdn_delete_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $intro_buy_renew_err = "紹介口座料 の仕入伝票が本部で日次更新されているため削除できません。";
        }
    }


    //エラーじゃない場合、処理開始
    if($renew_flg == "f" && $act_buy_renew_err == null && $intro_buy_renew_err == null){

        Db_Query($db_con, "BEGIN;");

        // 売上データ全て・仕入ヘッダ・入金ヘッダ削除SQL
        $sql  = "DELETE FROM \n";
        $sql .= "   t_sale_h \n";
        $sql .= "WHERE \n";
        $sql .= "   sale_id = ".$_POST["hdn_delete_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if($res === false){ 
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        Db_Query($db_con, "COMMIT;");
    }

    // 伝票削除フラグクリア
    $clear_data["hdn_delete_id"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}


/****************************/
// 表示ボタン押下時
/****************************/
if ($_POST["form_display"] != null){

    /****************************/
    // エラーチェック
    /****************************/
    // ■共通フォームチェック
    Search_Err_Chk($form);

    // ■伝票番号
    // エラーメッセージ
    $err_msg = "伝票番号 は数値のみ入力可能です。";
    Err_Chk_Num($form, "form_slip_no", $err_msg);

    // ■売上金額
    // エラーメッセージ
    $err_msg = "売上金額 は数値のみ入力可能です。";
    Err_Chk_Int($form, "form_sale_amount", $err_msg);

    // ■売上計上日
    // エラーメッセージ
    $err_msg = "売上計上日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_sale_day", $err_msg);

    /****************************/
    // エラーチェック結果集計
    /****************************/
    // チェック適用
    $test = $form->validate();

    // 結果をフラグに
    $err_flg = (count($form->_errors) > 0) ? true : false;

    $post_flg = ($err_flg != true) ? true : false;

}


/****************************/
// 1. 表示ボタン押下＋エラーなし時
// 2. ページ切り替え時、その他のPOST時
/****************************/
if (($_POST["form_display"] != null && $err_flg != true) || ($_POST != null && $_POST["form_display"] == null)){

    // 日付POSTデータの0埋め
    $_POST["form_claim_day"] = Str_Pad_Date($_POST["form_claim_day"]);
    $_POST["form_sale_day"]  = Str_Pad_Date($_POST["form_sale_day"]);

    // 1. フォームの値を変数にセット
    // 2. SESSION（hidden用）の値（検索条件復元関数内でセット）を変数にセット
    // 一覧取得クエリ条件に使用
    $display_num        = $_POST["form_display_num"];
    $output_type        = $_POST["form_output_type"];
    $client_branch      = $_POST["form_client_branch"];
    $attach_branch      = $_POST["form_attach_branch"];
    $client_cd1         = $_POST["form_client"]["cd1"];
    $client_cd2         = $_POST["form_client"]["cd2"];
    $client_name        = $_POST["form_client"]["name"];
    $round_staff_cd     = $_POST["form_round_staff"]["cd"];
    $round_staff_select = $_POST["form_round_staff"]["select"];
    $part               = $_POST["form_part"];
    $claim_cd1          = $_POST["form_claim"]["cd1"];
    $claim_cd2          = $_POST["form_claim"]["cd2"];
    $claim_name         = $_POST["form_claim"]["name"];
    $multi_staff        = $_POST["form_multi_staff"];
    $ware               = $_POST["form_ware"];
    $claim_day_sy       = $_POST["form_claim_day"]["sy"];
    $claim_day_sm       = $_POST["form_claim_day"]["sm"];
    $claim_day_sd       = $_POST["form_claim_day"]["sd"];
    $claim_day_ey       = $_POST["form_claim_day"]["ey"];
    $claim_day_em       = $_POST["form_claim_day"]["em"];
    $claim_day_ed       = $_POST["form_claim_day"]["ed"];
    $charge_fc_cd1      = $_POST["form_charge_fc"]["cd1"];
    $charge_fc_cd2      = $_POST["form_charge_fc"]["cd2"];
    $charge_fc_name     = $_POST["form_charge_fc"]["name"];
    $charge_fc_select   = $_POST["form_charge_fc"]["select"]["1"];
    $slip_no_s          = $_POST["form_slip_no"]["s"];
    $slip_no_e          = $_POST["form_slip_no"]["e"];
    $sale_amount_s      = $_POST["form_sale_amount"]["s"];
    $sale_amount_e      = $_POST["form_sale_amount"]["e"];
    $sale_day_sy        = $_POST["form_sale_day"]["sy"];
    $sale_day_sm        = $_POST["form_sale_day"]["sm"];
    $sale_day_sd        = $_POST["form_sale_day"]["sd"];
    $sale_day_ey        = $_POST["form_sale_day"]["ey"];
    $sale_day_em        = $_POST["form_sale_day"]["em"];
    $sale_day_ed        = $_POST["form_sale_day"]["ed"];
    $contract_div       = $_POST["form_contract_div"];
    $client_gr_name     = $_POST["form_client_gr"]["name"];
    $client_gr_select   = $_POST["form_client_gr"]["select"];
    $trade              = $_POST["form_trade"];
    $renew              = $_POST["form_renew"];
    $repeat             = $_POST["form_repeat"];

    #2010-04-28 hashimoto-y
    $form_goods_cd      = $_POST["form_goods"]["cd"]; 
    $form_goods_name    = $_POST["form_goods"]["name"]; 
    $form_divide        = $_POST["form_divide"]; 

    $post_flg = true;

}


/****************************/
// 一覧データ取得条件作成
/****************************/
if ($post_flg == true && $err_flg != true){

    $sql = null;

    // 顧客担当支店
    $sql .= ($client_branch != null) ? "AND t_client.charge_branch_id = $client_branch \n" : null;
    // 所属本支店
    if ($attach_branch != null){
        $sql .= "AND \n";
        $sql .= "   t_round_staff.staff_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           t_attach.staff_id \n";
        $sql .= "       FROM \n";
        $sql .= "           t_attach \n";
        $sql .= "           INNER JOIN t_part ON t_attach.part_id = t_part.part_id \n";
        $sql .= "       WHERE \n";
        $sql .= "           t_part.branch_id = $attach_branch \n";
        $sql .= "   ) \n";
    }
    // 得意先コード１
    $sql .= ($client_cd1 != null) ? "AND t_sale_h.client_cd1 LIKE '$client_cd1%' \n" : null;
    // 得意先コード２
    $sql .= ($client_cd2 != null) ? "AND t_sale_h.client_cd2 LIKE '$client_cd2%' \n" : null;
    // 得意先名
    if ($client_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_sale_h.client_name  LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_sale_h.client_name2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_sale_h.client_cname LIKE '%$client_name%' \n";
        $sql .= "   ) \n";
    }
    // 巡回担当者コード
    $sql .= ($round_staff_cd != null) ? "AND t_round_staff.charge_cd = '$round_staff_cd' \n" : null;
    // 巡回担当者セレクト
    $sql .= ($round_staff_select != null) ? "AND t_round_staff.staff_id = $round_staff_select \n" : null;
    // 部署
    $sql .= ($part != null) ? "AND t_round_staff.part_id = $part \n" : null;
    // 請求先コード１   
    $sql .= ($claim_cd1 != null) ? "AND t_client_claim.client_cd1 LIKE '$claim_cd1%' \n" : null;
    // 請求先コード２
    $sql .= ($claim_cd2 != null) ? "AND t_client_claim.client_cd2 LIKE '$claim_cd2%' \n" : null;
    // 請求先名
    if ($claim_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_client_claim.client_name  LIKE '%$claim_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client_claim.client_name2 LIKE '%$claim_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client_claim.client_cname LIKE '%$claim_name%' \n";
        $sql .= "   ) \n";
    }
    // 複数選択
    if ($multi_staff != null){
        $ary_multi_staff = explode(",", $multi_staff);
        $sql .= "AND \n";
        $sql .= "   t_round_staff.charge_cd IN (";
        foreach ($ary_multi_staff as $key => $value){
            $sql .= "'".trim($value)."'";
            $sql .= ($key+1 < count($ary_multi_staff)) ? ", " : ") \n";
        }
    }
    // 倉庫
    $sql .= ($ware != null) ? "AND t_sale_h.ware_id = $ware \n" : null;
    // 請求日（開始）
    $claim_day_s  = $claim_day_sy."-".$claim_day_sm."-".$claim_day_sd;
    $sql .= ($claim_day_s != "--") ? "AND t_sale_h.claim_day >= '$claim_day_s' \n" : null;
    // 請求日（終了）
    $claim_day_e  = $claim_day_ey."-".$claim_day_em."-".$claim_day_ed;
    $sql .= ($claim_day_e != "--") ? "AND t_sale_h.claim_day <= '$claim_day_e' \n" : null;
    // 委託先FCコード１
    $sql .= ($charge_fc_cd1 != null) ? "AND t_sale_h.act_cd1 LIKE '$charge_fc_cd1%' \n" : null;
    // 委託先FCコード２
    $sql .= ($charge_fc_cd2 != null) ? "AND t_sale_h.act_cd2 LIKE '$charge_fc_cd2%' \n" : null;
    // 委託先FC名
    if ($charge_fc_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_act_client.client_name  LIKE '%$charge_fc_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_act_client.client_name2 LIKE '%$charge_fc_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_act_client.client_cname LIKE '%$charge_fc_name%' \n";
        $sql .= "   ) \n";
    }
    // 委託先FCセレクト
    $sql .= ($charge_fc_select != null) ? "AND t_sale_h.act_id = $charge_fc_select \n" : null;
    // 伝票番号（開始）
    $sql .= ($slip_no_s != null) ? "AND t_sale_h.sale_no >= '".str_pad($slip_no_s, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // 伝票番号（終了）
    $sql .= ($slip_no_e != null) ? "AND t_sale_h.sale_no <= '".str_pad($slip_no_e, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // 売上金額（開始）
    if ($sale_amount_s != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           CASE \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * 1 \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "           END \n";
        $sql .= "   ) \n";
        $sql .= "   >= $sale_amount_s \n";
    }
    // 売上金額（終了）
    if ($sale_amount_e != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           CASE \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * 1 \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "           END \n";
        $sql .= "   ) \n";
        $sql .= "   <= $sale_amount_e \n";
    }
    // 売上計上日（開始）
    $sale_day_s = $sale_day_sy."-".$sale_day_sm."-".$sale_day_sd;
    $sql .= ($sale_day_s != "--") ? "AND t_sale_h.sale_day >= '$sale_day_s' \n" : null;
    // 売上計上日（終了）
    $sale_day_e = $sale_day_ey."-".$sale_day_em."-".$sale_day_ed;
    $sql .= ($sale_day_e != "--") ? "AND t_sale_h.sale_day <= '$sale_day_e' \n" : null;
    // 契約区分
    if ($contract_div == "2"){
        $sql .= "AND t_sale_h.contract_div = '1' \n";
    }elseif ($contract_div == "3"){
        $sql .= "AND t_sale_h.contract_div = '2' \n";
    }elseif ($contract_div == "4"){
        $sql .= "AND t_sale_h.contract_div = '3' \n";
    }
    // グループ名
    $sql .= ($client_gr_name != null) ? "AND t_client_gr.client_gr_name LIKE '%$client_gr_name%' \n" : null;
    // グループセレクト
    $sql .= ($client_gr_select != null) ? "AND t_client.client_gr_id = $client_gr_select \n" : null;
    // 取引区分
    $sql .= ($trade != null) ? "AND t_sale_h.trade_id = $trade \n" : null;
    // 日次更新
    if ($renew == "2"){
        $sql .= "AND t_sale_h.renew_flg = 'f' \n";
    }elseif ($renew == "3"){
        $sql .= "AND t_sale_h.renew_flg = 't' \n";
    }

	// 定期リピート
	switch ($repeat){
		// 定期
		case '2' :
        	$sql .= "AND t_aorder_h.contract_id IS NOT NULL \n";
			break;
		// 不定期
		case '3' :
        	$sql .= "AND t_aorder_h.contract_id IS NULL \n";
			break;
	}

    #2010-04-28 hashimoto-y
    // 商品コード、商品名、商品区分
    if ($form_goods_cd != null || $form_goods_name != null || $form_divide != null){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN ";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "            DISTINCT(sale_id) \n";
        $sql .= "       FROM  \n";
        $sql .= "            t_sale_d \n";
        $sql .= "       WHERE \n";

        $sql .= ($form_divide     != null) ? "sale_div_cd = '$form_divide' \n" : null;

        if($form_divide == null){
            $sql .= ($form_goods_cd   != null) ? "goods_cd LIKE '$form_goods_cd%' \n" : null;
        }else{
            $sql .= ($form_goods_cd   != null) ? "AND goods_cd LIKE '$form_goods_cd%' \n" : null;
        }

        if($form_divide == null && $form_goods_cd == null){
            $sql .= ($form_goods_name != null) ? "official_goods_name LIKE '%$form_goods_name%' \n" : null;
        }else{
            $sql .= ($form_goods_name != null) ? "AND official_goods_name LIKE '%$form_goods_name%' \n" : null;
        }

        $sql .= "   ) \n";
    }

    // 変数詰め替え
    $where_sql = $sql;


    $sql = null;

    // ソート順
    switch ($_POST["hdn_sort_col"]){
        // 得意先コード
        case "sl_client_cd":
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // 得意先名
        case "sl_client_name":
            $sql .= "   t_sale_h.client_cname, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // 伝票番号
        case "sl_slip":
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // 売上計上日
        case "sl_sale_day":
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // 巡回担当者（メイン１）
        case "sl_round_staff":
            $sql .= "   t_round_staff.charge_cd, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // 代行先コード
        case "sl_act_client_cd":
            $sql .= "   t_act_client.client_cd1, \n";
            $sql .= "   t_act_client.client_cd2, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // 代行先名
        case "sl_act_client_name":
            $sql .= "   t_act_client.client_cname, \n";
            $sql .= "   t_act_client.client_cd1, \n";
            $sql .= "   t_act_client.client_cd2, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
    }

    // 変数詰め替え
    $order_sql = $sql;

}

#echo $order_sql;


/****************************/
// 一覧データ取得
/****************************/
#2009-10-08 hashimoto-y
#
#手書伝票作成画面の登録中に別ウィンドウで売上確定一覧の初期表示を行い
#手書伝票作成の登録後、完了ボタンをクリックした場合にSQLエラーとなる
#
#$order_sqlがセットされてない場合は初期表示
if($order_sql == "") $post_flg = false;


if ($post_flg == true && $err_flg != true){

    $sql  = "SELECT \n";
    $sql .= "   t_sale_h.sale_id, \n";                                          //  0 売上ID
    $sql .= "   t_sale_h.sale_no, \n";                                          //  1 売上番号
    $sql .= "   t_sale_h.sale_day, \n";                                         //  2 売上計上日
    $sql .= "   t_sale_h.client_cd1 || '-' || t_sale_h.client_cd2 \n";
    $sql .= "   AS client_cd, \n";                                              //  3 得意先コード
    $sql .= "   t_sale_h.client_cname, \n";                                     //  4 得意先略称
    $sql .= "   t_round_staff.charge_cd, \n";                                   //  5 担当者コード
    $sql .= "   t_round_staff.staff_name, \n";                                  //  6 担当者名
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN t_sale_h.net_amount * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN t_sale_h.net_amount * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS net_amount,\n";                                              //  7 売上金額（税抜）
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN t_sale_h.tax_amount * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN t_sale_h.tax_amount * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS tax_amount,\n";                                              //  8 消費税
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS sale_amount,\n";                                             //  9 売上金額（税込）
    $sql .= "   CASE t_sale_h.renew_flg \n";
    $sql .= "       WHEN 't' THEN '○' \n";
    $sql .= "       ELSE NULL \n";
    $sql .= "   END \n";
    $sql .= "   AS renew, \n";                                                  // 10 日次更新
    $sql .= "   t_sale_h.hand_slip_flg, \n";                                    // 11 手書伝票フラグ
    $sql .= "   t_sale_h.aord_id, \n";                                          // 12 受注ID
    $sql .= "   t_sale_h.contract_div AS s_contract_div, \n";                                   // 13 契約区分
    $sql .= "   t_aorder_h.contract_div, \n";                                   // 13 契約区分
    $sql .= "   t_sale_h.act_request_flg, \n";                                  // 14 代行料フラグ
    $sql .= "   CASE t_sale_h.trade_id \n";
    $sql .= "       WHEN '11' THEN '掛売上' \n";
    $sql .= "       WHEN '15' THEN '割賦売上' \n";
    $sql .= "       WHEN '13' THEN '掛返品' \n";
    $sql .= "       WHEN '14' THEN '掛値引' \n";
    $sql .= "       WHEN '61' THEN '現金売上' \n";
    $sql .= "       WHEN '63' THEN '現金返品' \n";
    $sql .= "       WHEN '64' THEN '現金値引' \n";
    $sql .= "   END \n";
    $sql .= "   AS trade, \n";                                                  // 15 取引区分
    $sql .= "   CASE t_client.slip_out \n";                                     // 16 伝票発行
    $sql .= "       WHEN '1' THEN '有' \n";
    $sql .= "       ELSE '無' \n";
    $sql .= "   END \n";
    $sql .= "   AS slip_out, \n";
    $sql .= "   t_sale_h.total_split_num, \n";                                  // 17 割賦回数
    $sql .= "   t_act_client.client_cd1 || '-' || t_act_client.client_cd2 \n";
    $sql .= "   AS act_cd, \n";                                                 // 18 代行先コード
    $sql .= "   t_act_client.client_cname AS act_cname, \n";                    // 19 代行先名略称
    $sql .= "   to_date(t_sale_h.renew_day, 'YYYY-MM-DD') AS renew_day, \n";    // 20 日次更新日
    $sql .= "   CASE \n";
    $sql .= "       WHEN (intro_buy_h.renew_flg = true OR act_buy_h.renew_flg = true) THEN true \n";
    $sql .= "       ELSE false \n";
    $sql .= "   END AS buy_renew_flg, \n";                                      // 21 代行料・紹介料の仕入の日次更新フラグ
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN t_sale_h.advance_offset_totalamount * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN t_sale_h.advance_offset_totalamount * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS advance_offset_totalamount, \n";                             // 22 前受相殺額合計
    $sql .= "   CASE \n";
    $sql .= "       WHEN act_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "       THEN (act_buy_h.net_amount + act_buy_h.tax_amount) *  1 \n";
    $sql .= "       WHEN act_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "       THEN (act_buy_h.net_amount + act_buy_h.tax_amount) * -1 \n";
    $sql .= "   END AS act_amount, \n";                                         // 23 代行料（税込）
    $sql .= "   CASE \n";
    $sql .= "       WHEN intro_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "       THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) *  1 \n";
    $sql .= "       WHEN intro_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "       THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) * -1 \n";
    $sql .= "   END AS intro_amount, \n";                                        // 24 紹介口座料（税込）

    //伝票発行 // 25
    $sql .= "   CASE t_client.slip_out \n";
    $sql .= "       WHEN '1' THEN CASE t_sale_h.hand_slip_flg ";
    $sql .= "                       WHEN 't' THEN CASE\n";
    $sql .= "                                       WHEN t_sale_h.slip_out_day IS NOT NULL THEN CAST(t_sale_h.slip_out_day AS varchar)";
    $sql .= "                                       ELSE NULL ";
    $sql .= "                                   END ";
    $sql .= "                       ELSE CASE \n";
    $sql .= "                           WHEN t_aorder_h.slip_out_day IS NOT NULL THEN CAST(t_aorder_h.slip_out_day AS varchar)";
    $sql .= "                           ELSE NULL ";
    $sql .= "                       END ";
    $sql .= "                   END ";
    $sql .= "       WHEN '2' THEN '指定' ";
    $sql .= "       WHEN '3' THEN '他票' ";
    $sql .= "   END AS slip_out_day, " ;

    //原価金額26
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11,15,61) \n";
    $sql .= "       THEN t_sale_h.cost_amount *  1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13,14,63,64)\n";
    $sql .= "       THEN t_sale_h.cost_amount * -1 \n";
    $sql .= "   END AS cost_amount, \n";                                        // 24 紹介口座料（税込）

	// リピート
	$sql .= "	CASE \n";
	$sql .= "		WHEN t_aorder_h.contract_id IS NOT NULL THEN NULL \n";
	$sql .= "		WHEN t_aorder_h.contract_id IS NULL THEN '不定期' \n";
	$sql .= "	END AS repeat \n";

    $sql .= "FROM \n";
    $sql .= "   t_sale_h \n";
    $sql .= "   INNER JOIN t_client ON t_sale_h.client_id = t_client.client_id \n";
    $sql .= "   LEFT JOIN t_client AS t_client_claim ON t_sale_h.claim_id = t_client_claim.client_id \n";
    $sql .= "   LEFT JOIN t_client AS t_act_client   ON t_sale_h.act_id = t_act_client.client_id \n";
    $sql .= "   LEFT JOIN t_aorder_h ON t_sale_h.aord_id = t_aorder_h.aord_id \n";
    $sql .= "   LEFT JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_sale_staff.sale_id, \n";
    $sql .= "           t_sale_staff.staff_id, \n";
    $sql .= "           CAST(LPAD(t_staff.charge_cd, 4, '0') AS TEXT) AS charge_cd, \n";
    $sql .= "           t_sale_staff.staff_name, \n";
    $sql .= "           t_attach.part_id \n";
    $sql .= "       FROM \n";
    $sql .= "           t_sale_staff \n";
    $sql .= "           LEFT JOIN t_staff ON t_sale_staff.staff_id = t_staff.staff_id \n";
    $sql .= "           LEFT JOIN t_attach ON t_sale_staff.staff_id = t_attach.staff_id \n";
    $sql .= "           AND t_attach.shop_id = $shop_id \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_sale_staff.staff_div = '0' \n";
    $sql .= "       AND \n";
    $sql .= "           t_sale_staff.sale_rate IS NOT NULL \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_round_staff \n";
    $sql .= "   ON t_sale_h.sale_id = t_round_staff.sale_id \n";
    $sql .= "   LEFT JOIN t_buy_h AS intro_buy_h ON t_sale_h.sale_id = intro_buy_h.intro_sale_id \n";
    $sql .= "   LEFT JOIN t_buy_h AS act_buy_h ON t_sale_h.sale_id = act_buy_h.act_sale_id \n";
    $sql .= "   LEFT JOIN t_client_gr ON t_client.client_gr_id = t_client_gr.client_gr_id \n";
    // ショップ
    $sql .= "WHERE \n";
    if ($_SESSION["group_kind"] == "2"){
    $sql .= "   t_sale_h.shop_id IN (".Rank_Sql().") \n";
    }else{
    $sql .= "   t_sale_h.shop_id = $shop_id \n";
    }
    // 伝票番号付番済
    $sql .= "AND \n";
    $sql .= "   t_sale_h.sale_no IS NOT NULL \n";

    $sql .= $where_sql;
    $sql .= "ORDER BY \n";
    $sql .= $order_sql;

    // 全件数取得
    $res            = Db_Query($db_con, $sql.";");
    $total_count    = pg_num_rows($res);

    // LIMIT, OFFSET条件作成
    if ($post_flg == true && $err_flg != true){

        // 表示件数
        switch ($display_num){
            case "1":
                $limit = $total_count;
                break;
            case "2":
                $limit = "100";
                break;
        }

        // 取得開始位置
        $offset = ($page_count != null) ? ($page_count - 1) * $limit : "0";

        // 行削除でページに表示するレコードが無くなる場合の対処
        if($page_count != null){
            // 行削除でtotal_countとoffsetの関係が崩れた場合
            if ($total_count <= $offset){
                // オフセットを選択件前に
                $offset     = $offset - $limit;
                // 表示するページを1ページ前に（一気に2ページ分削除されていた場合などには対応してないです）
                $page_count = $page_count - 1;
                // 選択件数以下時はページ遷移を出力させない(nullにする)
                $page_count = ($total_count <= $display_num) ? null : $page_count;
            }
        }else{
            $offset = 0;
        }

    }

    // ページ内データ取得
    $limit_offset   = ($limit != null) ? "LIMIT $limit OFFSET $offset " : null;
    $res            = Db_Query($db_con, $sql.$limit_offset.";");
    $match_count    = pg_num_rows($res);
    $ary_sale_data  = Get_Data($res, 2, "ASSOC");

}


/****************************/
// CSV出力
/****************************/
if ($post_flg == true && $err_flg != true && $output_type == "2"){

    $sql  = "SELECT \n";
    $sql .= "    t_sale_h.sale_id, \n";             // 0
    $sql .= "    t_sale_h.contract_div, \n";        // 1
    $sql .= "    t_sale_h.client_cd1 || '-' || t_sale_h.client_cd2 AS client_cd, \n";   // 2
    $sql .= "    t_sale_h.client_cname, \n";        // 3
    $sql .= "    t_sale_h.sale_no, \n";             // 4
    $sql .= "    t_sale_h.sale_day, \n";            // 5
    $sql .= "    t_round_staff.charge_cd, \n";      // 6
    $sql .= "    t_round_staff.staff_name, \n";     // 7
    $sql .= "    t_round_staff.sale_rate, \n";      // 8
    $sql .= "    t_sale_h.act_cd1 || '-' || t_sale_h.act_cd2 AS act_cd, \n";    // 9
    $sql .= "    t_sale_h.act_cname, \n";           //10
    $sql .= "    t_sale_h.act_div, \n";             //11
    $sql .= "    t_sale_h.act_request_price, \n";   //12
    $sql .= "    t_sale_h.act_request_rate, \n";    //13
    $sql .= "    CASE \n";
    $sql .= "        WHEN act_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "        THEN (act_buy_h.net_amount + act_buy_h.tax_amount) *  1 \n";
    $sql .= "        WHEN act_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "        THEN (act_buy_h.net_amount + act_buy_h.tax_amount) * -1 \n";
    $sql .= "    END AS act_amount, \n";            //14 代行料（税込）
    $sql .= "    CASE \n";
    $sql .= "        WHEN intro_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "        THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) *  1 \n";
    $sql .= "        WHEN intro_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "        THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) * -1 \n";
    $sql .= "    END AS intro_amount, \n";          //15 紹介口座料（税込）
    $sql .= "    CASE t_sale_h.trade_id \n";
    $sql .= "        WHEN '11' THEN '掛売上' \n";
    $sql .= "        WHEN '15' THEN '割賦売上' \n";
    $sql .= "        WHEN '13' THEN '掛返品' \n";
    $sql .= "        WHEN '14' THEN '掛値引' \n";
    $sql .= "        WHEN '61' THEN '現金売上' \n";
    $sql .= "        WHEN '63' THEN '現金返品' \n";
    $sql .= "        WHEN '64' THEN '現金値引' \n";
    $sql .= "    END AS trade, \n";                 //16
    $sql .= "    t_sale_h.cost_amount, \n";         //17
    $sql .= "    t_sale_h.net_amount, \n";          //18
    $sql .= "    t_sale_h.tax_amount, \n";          //19
    $sql .= "    t_sale_h.advance_offset_totalamount, \n";      //20
    $sql .= "    t_sale_h.total_split_num, \n";     //21
    $sql .= "    to_date(t_sale_h.renew_day, 'YYYY-MM-DD') AS renew_day, \n";   //22
    $sql .= "    CASE t_sale_d.sale_div_cd \n";
    $sql .= "        WHEN '01' THEN 'リピート' \n";
    $sql .= "        WHEN '02' THEN '商品' \n";
    $sql .= "        WHEN '03' THEN 'レンタル' \n";
    $sql .= "        WHEN '04' THEN 'リース' \n";
    $sql .= "        WHEN '05' THEN '工事' \n";
    $sql .= "        WHEN '06' THEN 'その他' \n";
    $sql .= "    END AS sale_div, \n";              //23
    $sql .= "    t_sale_d.serv_name, \n";           //24
    $sql .= "    t_sale_d.goods_cd, \n";            //25
    $sql .= "    t_sale_d.official_goods_name, \n"; //26
    $sql .= "    CASE t_sale_d.set_flg \n";
    $sql .= "        WHEN true THEN '○' \n";
    $sql .= "    END AS set_flg, \n";               //27


    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 63) THEN t_sale_d.num * -1 \n";
    $sql .= "       ELSE t_sale_d.num \n";
    $sql .= "    END AS num, \n";                   //28

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,64) THEN t_sale_d.cost_price * -1 \n";
    $sql .= "       ELSE t_sale_d.cost_price \n";
    $sql .= "    END AS cost_price, \n";            //29

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,13,64,63) THEN t_sale_d.cost_amount * -1 \n";
    $sql .= "       ELSE t_sale_d.cost_amount \n";
    $sql .= "    END AS cost_amount, \n";           //30

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,64) THEN t_sale_d.sale_price * -1 \n";
    $sql .= "       ELSE t_sale_d.sale_price \n";
    $sql .= "    END AS sale_price, \n";          //31

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,13,64,63) THEN t_sale_d.sale_price * -1 \n";
    $sql .= "       ELSE t_sale_d.sale_amount \n";  //32
    $sql .= "    END AS sale_amount, ";

    $sql .= "    t_sale_d.egoods_cd, \n";           //33
    $sql .= "    t_sale_d.egoods_name, \n";         //34
    $sql .= "    t_sale_d.egoods_num, \n";          //35
    $sql .= "    t_sale_d.rgoods_cd, \n";           //36
    $sql .= "    t_sale_d.rgoods_name, \n";         //37
    $sql .= "    t_sale_d.rgoods_num, \n";          //38
    $sql .= "    t_sale_h.intro_ac_div, \n";        //39
    $sql .= "    t_sale_d.account_price, \n";       //40
    $sql .= "    t_sale_d.account_rate, \n";        //41
    $sql .= "    CASE t_sale_d.advance_flg \n";
    $sql .= "        WHEN '1' THEN 'なし' \n";
    $sql .= "        ELSE CAST(t_sale_d.advance_offset_amount AS TEXT) \n";
    $sql .= "    END AS advance_offset, \n";        //42
    $sql .= "   t_sale_h.claim_day, \n";            // 43
    $sql .= "   t_sale_h.client_name2, \n";         // 44
    $sql .= "    t_sale_d.serv_cd, \n";             // 45
    $sql .= "    t_client_gr.client_gr_cd,\n ";     //46得意先グループコード
    $sql .= "    t_client_gr.client_gr_name, \n";   //47得意先グループ名
    // 48リピート
	$sql .= "	 CASE \n";
	$sql .= "		WHEN t_aorder_h.contract_id IS NOT NULL THEN NULL \n";
    $sql .= "		WHEN t_aorder_h.contract_id IS NULL THEN '不定期' \n";
	$sql .= "	 END \n";

    $sql .= "FROM \n";
    $sql .= "    t_sale_h \n";
    $sql .= "    INNER JOIN t_sale_d ON t_sale_h.sale_id = t_sale_d.sale_id \n";
    $sql .= "	 LEFT JOIN t_aorder_h ON t_sale_h.aord_id = t_aorder_h.aord_id \n";
    $sql .= "    INNER JOIN t_client ON t_sale_h.client_id = t_client.client_id \n";
    $sql .= "    LEFT JOIN t_client AS t_client_claim ON t_sale_h.claim_id = t_client_claim.client_id \n";
    $sql .= "    LEFT JOIN t_client AS t_act_client ON t_sale_h.act_id = t_act_client.client_id \n";
    $sql .= "    LEFT JOIN ( \n";
    $sql .= "        SELECT \n";
    $sql .= "            t_sale_staff.sale_id, \n";
    $sql .= "            t_sale_staff.staff_id, \n";
    $sql .= "            CAST(LPAD(t_staff.charge_cd, 4, '0') AS TEXT) AS charge_cd, \n";
    $sql .= "            t_sale_staff.staff_name, \n";
    $sql .= "            t_sale_staff.sale_rate, \n";
    $sql .= "            t_attach.part_id \n";
    $sql .= "        FROM \n";
    $sql .= "            t_sale_staff \n";
    $sql .= "            LEFT JOIN t_staff ON t_sale_staff.staff_id = t_staff.staff_id \n";
    $sql .= "            LEFT JOIN t_attach ON t_sale_staff.staff_id = t_attach.staff_id \n";
    if($_SESSION["group_kind"] == "2"){
        $sql .= "                AND t_attach.shop_id IN (".Rank_Sql().") \n";
    }else{
        $sql .= "                AND t_attach.shop_id = $shop_id \n";
    }
    $sql .= "        WHERE \n";
    $sql .= "            t_sale_staff.staff_div = '0' \n";
    $sql .= "    ) AS t_round_staff ON t_sale_h.sale_id = t_round_staff.sale_id \n";
    $sql .= "    LEFT JOIN t_buy_h AS intro_buy_h ON t_sale_h.sale_id = intro_buy_h.intro_sale_id \n";
    $sql .= "    LEFT JOIN t_buy_h AS act_buy_h ON t_sale_h.sale_id = act_buy_h.act_sale_id \n";
    $sql .= "    LEFT JOIN t_client_gr ON t_client.client_gr_id = t_client_gr.client_gr_id \n";

    $sql .= "WHERE \n";
    if($_SESSION["group_kind"] == "2"){
        $sql .= "    t_sale_h.shop_id IN (".Rank_Sql().") \n";
    }else{
        $sql .= "    t_sale_h.shop_id = $shop_id \n";
    }
    $sql .= $where_sql;

    $sql .= "ORDER BY \n";
    $sql .= $order_sql.", ";
    $sql .= "    t_sale_d.line \n";

    $result = Db_Query($db_con, $sql.";");
    $page_data  = Get_Data($result, 2);

    // ファイル名
    $csv_file_name = "売上確定一覧.csv";

    //CSVヘッダ作成
    $csv_header   = array();    //配列初期化
    $csv_header[] = "グループコード";
    $csv_header[] = "グループ名";
    $csv_header[] = "得意先コード";
    $csv_header[] = "得意先名";
    $csv_header[] = "得意先名2";
    $csv_header[] = "伝票番号";
    $csv_header[] = "売上計上日";
    $csv_header[] = "請求日";
    $csv_header[] = "巡回担当者コード（メイン１）";
    $csv_header[] = "巡回担当者名（メイン１）";
    if($_SESSION["group_kind"] == "2"){
        $csv_header[] = "代行先コード";
        $csv_header[] = "代行先名";
        $csv_header[] = "代行委託料";
    }
    $csv_header[] = "紹介口座料";
    $csv_header[] = "取引区分";
    $csv_header[] = "原価金額";
    $csv_header[] = "売上金額";
    $csv_header[] = "消費税額";
    $csv_header[] = "前受相殺額合計";
    $csv_header[] = "分割回数";
    $csv_header[] = "リピート";
    $csv_header[] = "日次更新日";

    $csv_header[] = "販売区分";
    $csv_header[] = "サービスコード";
    $csv_header[] = "サービス名";
    $csv_header[] = "アイテムコード";
    $csv_header[] = "アイテム";
    $csv_header[] = "一式";
    $csv_header[] = "数量";
    $csv_header[] = "営業原価";
    $csv_header[] = "原価合計額";
    $csv_header[] = "売上単価";
    $csv_header[] = "売上合計額";
    $csv_header[] = "消耗品コード";
    $csv_header[] = "消耗品";
    $csv_header[] = "数量";
    $csv_header[] = "本体商品コード";
    $csv_header[] = "本体商品";
    $csv_header[] = "数量";
    $csv_header[] = "口座料(商品単位)";
    $csv_header[] = "前受相殺額";

    // CSVデータ取得
    $sale_data  = array();
    $sale_id    = null;

    for ($i = 0; $i < count($page_data); $i++){

        $sale_data[$i][] = $page_data[$i][46];          //グループコード
        $sale_data[$i][] = $page_data[$i][47];          //グループ名
        
        $sale_data[$i][] = $page_data[$i][2];           // 得意先コード
        $sale_data[$i][] = $page_data[$i][3];           // 得意先名
        $sale_data[$i][] = $page_data[$i][44];          // 請求日
        $sale_data[$i][] = $page_data[$i][4];           // 伝票番号
        $sale_data[$i][] = $page_data[$i][5];           // 売上計上日
        $sale_data[$i][] = $page_data[$i][43];          // 請求日
        $sale_data[$i][] = $page_data[$i][6];           // 巡回担当者コード（メイン１）
        $sale_data[$i][] = $page_data[$i][7];           // 巡回担当者名（メイン１）
        if($_SESSION["group_kind"] == "2"){
            $sale_data[$i][] = $page_data[$i][9];       // 代行先コード
            $sale_data[$i][] = $page_data[$i][10];      // 代行先名
            $sale_data[$i][] = $page_data[$i][14];      // 代行料
        }
        $sale_data[$i][] = $page_data[$i][15];          // 紹介料
        $sale_data[$i][] = $page_data[$i][16];          // 取引区分
        if($page_data[$i][16] == "掛売上" || $page_data[$i][16] == "現金売上" || $page_data[$i][16] == "割賦売上"){
            $sale_data[$i][] = $page_data[$i][17];          // 原価金額
            $sale_data[$i][] = $page_data[$i][18];      // 売上金額
            $sale_data[$i][] = $page_data[$i][19];      // 消費税額
        }else{
            $sale_data[$i][] = -1 * $page_data[$i][17];          // 原価金額
            $sale_data[$i][] = -1 * $page_data[$i][18]; // 売上金額
            $sale_data[$i][] = -1 * $page_data[$i][19]; // 消費税額
        }
        $sale_data[$i][] = $page_data[$i][20];          // 前受相殺額
        $sale_data[$i][] = $page_data[$i][21];          // 分割回数
        $sale_data[$i][] = $page_data[$i][48];          // リピート
        $sale_data[$i][] = $page_data[$i][22];          // 日次更新

        $sale_data[$i][] = $page_data[$i][23];          // 販売区分
        $sale_data[$i][] = $page_data[$i][45];          // サービスコード
        $sale_data[$i][] = $page_data[$i][24];          // サービス名
        $sale_data[$i][] = $page_data[$i][25];          // アイテムコード
        $sale_data[$i][] = $page_data[$i][26];          // アイテム
        $sale_data[$i][] = $page_data[$i][27];          // 一式
        $sale_data[$i][] = $page_data[$i][28];          // 数量
        $sale_data[$i][] = $page_data[$i][29];          // 営業原価
        $sale_data[$i][] = $page_data[$i][30];          // 営業金額
        $sale_data[$i][] = $page_data[$i][31];          // 売上単価
        $sale_data[$i][] = $page_data[$i][32];          // 売上金額
        $sale_data[$i][] = $page_data[$i][33];          // 消耗品コード
        $sale_data[$i][] = $page_data[$i][34];          // 消耗品
        $sale_data[$i][] = $page_data[$i][35];          // 数量
        $sale_data[$i][] = $page_data[$i][36];          // 本体商品コード
        $sale_data[$i][] = $page_data[$i][37];          // 本体商品
        $sale_data[$i][] = $page_data[$i][38];          // 数量
        // 紹介料区分が「商品別」の場合、口座料区分を出力
        if ($page_data[$i][39] == "4"){
            if ($page_data[$i][40] != null){
                $sale_data[$i][] = "固定額 ".(string)$page_data[$i][40]."円";   // 口座料(商品単位)（固定額）
            }else
            if ($page_data[$i][41] != null){
                $sale_data[$i][] = "売上の ".$page_data[$i][41]."％";           // 口座料(商品単位)（率）
            }
        // それ以外は空白を出力
        }else{
            $sale_data[$i][] = "";
        }
        $sale_data[$i][] = $page_data[$i][42];          // 前受相殺額

    }// データ行数分ループおわり


    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($sale_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;

}


/****************************/
// 表示用データ作成
/****************************/
if ($post_flg == true && $err_flg != true){

    // チェックボックス非表示行番号配列
    $hidden_check = array();

    if ($ary_sale_data != null){
    foreach ($ary_sale_data as $key => $value){

        // 売上IDをhiddenに追加
        $form->addElement("hidden", "output_id_array[$key]");           // 伝票出力売上ID配列
        $sale_id_set["output_id_array[$key]"] = $value["sale_id"];      // 売上ID

        // 伝票発行判定
        if($value["slip_out"] == "無"){
            // チェックボックス非表示行番号取得
            $hidden_check[] = $key;
        }

        // 印刷用改行設定
        $ary_sale_data[$key]["return"] = (bcmod($key, 45) == 0 && $key != "0") ? " style=\"page-break-before: always;\"" : null;

        // 伝票番号リンク作成
        $link_module = ($value["hand_slip_flg"] == "f") ? "2-2-202" : "2-2-201";
        $ary_sale_data[$key]["slip_no_link"]  = "<a href=\"./".$link_module.".php?sale_id=".$value["sale_id"]."&renew_flg=true\">";
        $ary_sale_data[$key]["slip_no_link"] .= $value["sale_no"];
        $ary_sale_data[$key]["slip_no_link"] .= "</a>";

        // 「担当者コード：担当者名」を作成
        if ($value["charge_cd"] != null){
            $ary_sale_data[$key]["staff"] = str_pad($value["charge_cd"], 4, "0", STR_PAD_LEFT)."：".htmlspecialchars($value["staff_name"]);
        }

        // 掛・現金の見分け
        if ($value["trade"] == "掛売上" || $value["trade"] == "掛返品" || $value["trade"] == "掛値引" || $value["trade"] == "割賦売上"){
            $ary_sale_data[$key]["trade_type"] = "kake";
        }else{
            $ary_sale_data[$key]["trade_type"] = "genkin";
        }

        // 分割回数リンク作成
        if ($value["trade"] == "割賦売上"){
            $ary_sale_data[$key]["split_link"]  = "<a href=\"./2-2-214.php?sale_id=".$value["sale_id"]."&division_flg=true\">";
            $ary_sale_data[$key]["split_link"] .= $value["total_split_num"]."回";
            $ary_sale_data[$key]["split_link"] .= "</a>";
        }else{
            $ary_sale_data[$key]["split_link"]  = null;
        }

        // 変更・取消・削除リンク作成
        //以下のいづれかに当てはまる場合は変更、削除リンクは表示しない
        //----------------------------//
        //日次更新している
        //受託先の代行料売上伝票
        //オンライン代行
        //手書伝票じゃなくて、受注からきた伝票じゃない（意味不明）
        //本部にあげた代行料・紹介料の仕入が日次更新されている
        //そもそも権限がない
        //----------------------------//
        if (
            $value["renew"] == "○" || $value["act_request_flg"] == "t" ||
            //$value["contract_div"] == "2" || $value["contract_div"] == "3" ||
            $value["contract_div"] == "2" || 
            ($value["hand_slip_flg"] == "f" && $value["aord_id"] == NULL) ||
            $value["buy_renew_flg"] == "t" || 
            $disabled != null
        ){
            // 変更リンク
            $ary_sale_data[$key]["chg_link"] = null;
            // 削除リンク
            $ary_sale_data[$key]["del_link"] = null;
        }else{
            // 変更リンク
            if($value["contract_div"] == "3"){
                $ary_sale_data[$key]["chg_link"] = null;
            }else{
                $ary_sale_data[$key]["chg_link"] = "<a href=\"./".$link_module.".php?sale_id=".$value["sale_id"]."\">変更</a>";
            }
            // 削除リンク
            if ($value["hand_slip_flg"] == "t"){
                $ary_sale_data[$key]["del_link"]  = "<a href=\"#\" ";
                $ary_sale_data[$key]["del_link"] .= "onClick=\"return Dialogue_2('削除します。', '".$_SERVER["PHP_SELF"]."', ".$value["sale_id"].", 'hdn_delete_id');\"";
                $ary_sale_data[$key]["del_link"] .= ">削除</a>";
            }else{
                $ary_sale_data[$key]["del_link"]  = "<a href=\"#\" ";
                $ary_sale_data[$key]["del_link"] .= "onClick=\"return Dialogue_2('削除します。', '".$_SERVER["PHP_SELF"]."',  ".$value["sale_id"].", 'hdn_cancel_id');\"";
                $ary_sale_data[$key]["del_link"] .= ">削除</a>";
            }
        }

        // 掛・現金の税抜・消費税額を算出
        // 掛売上時
        if ($value["trade"] == "掛売上" || $value["trade"] == "掛返品" || $value["trade"] == "掛値引" || $value["trade"] == "割賦売上"){
            // 現金売上（税抜）算出
            $kake_notax     += $value["net_amount"];
            // 掛消費税算出
            $kake_tax       += $value["tax_amount"];
        // 現金売上時
        }else{
            // 現金売上（税抜）算出
            $genkin_notax   += $value["net_amount"];
            // 現金消費税算出
            $genkin_tax     += $value["tax_amount"];
        }


        // 合計行用に金額算出
        switch ($value["trade"]){
            case "掛売上":
            case "割賦売上":
                $gross_act_amount           += $value["act_amount"];
                $gross_intro_amount         += $value["intro_amount"];
                $gross_kake_notax_amount    += $value["net_amount"];
                $gross_kake_ontax_amount    += $value["net_amount"] + $value["tax_amount"];
                $gross_advance_offset       += $value["advance_offset_totalamount"];
                $gross_cost_amount          += $value["cost_amount"];
                break;
            case "掛返品":
            case "掛値引":
                $minus_act_amount           += $value["act_amount"];
                $minus_intro_amount         += $value["intro_amount"];
                $minus_kake_notax_amount    += $value["net_amount"];
                $minus_kake_ontax_amount    += $value["net_amount"] + $value["tax_amount"];
                $minus_advance_offset       += $value["advance_offset_totalamount"];
                $minus_cost_amount          += $value["cost_amount"];
                break;
            case "現金売上":
                $gross_act_amount           += $value["act_amount"];
                $gross_intro_amount         += $value["intro_amount"];
                $gross_genkin_notax_amount  += $value["net_amount"];
                $gross_genkin_ontax_amount  += $value["net_amount"] + $value["tax_amount"];
                $gross_advance_offset       += $value["advance_offset_totalamount"];
                $gross_cost_amount          += $value["cost_amount"];
                break;
            case "現金返品":
            case "現金値引":
                $minus_act_amount           += $value["act_amount"];
                $minus_intro_amount         += $value["intro_amount"];
                $minus_genkin_notax_amount  += $value["net_amount"];
                $minus_genkin_ontax_amount  += $value["net_amount"] + $value["tax_amount"];
                $minus_advance_offset       += $value["advance_offset_totalamount"];
                $minus_cost_amount          += $value["cost_amount"];
                break;
        }
        // 代行料合計を算出
        $sum_act_amount += $value["act_amount"];

        // 紹介口座料合計を算出
        $sum_intro_amount += $value["intro_amount"];

        // 前受相殺額の合計を算出
        $sum_advance_offset += $value["advance_offset_totalamount"];

        //原価合計
        $sum_cost_amount += $value["cost_amount"];
    }
    }
    $form->setConstants($sale_id_set); 

    // 掛売合計
    $kake_ontax     = $kake_notax + $kake_tax;
    // 現金合計
    $genkin_ontax   = $genkin_notax + $genkin_tax;
    // 税抜合計
    $notax_amount   = $kake_notax + $genkin_notax;
    // 消費税合計
    $tax_amount     = $kake_tax + $genkin_tax;
    // 税込合計
    $ontax_amount   = $kake_ontax + $genkin_ontax;

}


/****************************/
// POST時にチェック初期化
/****************************/
//2009-09-08 hashimoto-y
if ($_POST != null){
    for($i = 0; $i < $match_count; $i++){
        $clear_check["form_slip_check"][$i] = "";
        $clear_check["form_republish_check"][$i] = "";
    }
    $clear_check["form_slip_all_check"] = "";
    $clear_check["form_republish_all_check"] = "";
    $form->setConstants($clear_check);
}


/****************************/
// フォーム動的部品作成
/****************************/
// 伝票発行ALLチェック
$form->addElement("checkbox", "form_slip_all_check", "", "発行", "
    onClick=\"javascript:All_check('form_slip_all_check', 'form_slip_check', $match_count)\"
");

// 伝票再発行ALLチェック
//2009-09-08 hashimoto-y
$form->addElement("checkbox", "form_republish_all_check", "", "再発行", "
    onClick=\"javascript:All_check('form_republish_all_check', 'form_republish_check', $match_count)\"
");


// 伝票発行チェック
for ($i = 0; $i < $match_count; $i++){

    //チェックボックス表示判定
    //伝票発行有で未発行
    if($ary_sale_data[$i]["slip_out_day"] == '' && $ary_sale_data[$i]["slip_out"] == "有"){
        $form->addElement("checkbox", "form_slip_check[$i]", "", "", "", "");
    }else{
        $freeze = $form->addElement("text", "form_slip_check[$i]", "", "");
        $set_data["form_slip_check"][$i] = $ary_sale_data["$i"]["slip_out_day"]; 
        $freeze->freeze();
    }


    //2009-09-08 hashimoto-y
    if($ary_sale_data[$i]["slip_out_day"] != '' && $ary_sale_data[$i]["slip_out"] == "有"){
        $form->addElement("checkbox", "form_republish_check[$i]", "", "", "", "");
    }else{
        $freeze = $form->addElement("text", "form_republish_check[$i]", "", "");
        $freeze->freeze();
    }


/*
    // 表示行判定
    if (!in_array("$i", $hidden_check)){
        // 未承認行はチェックボックス定義
        $form->addElement("checkbox", "form_slip_check[$i]", "", "", "", "");
    }else{
        // 承認行は非表示にする為にhiddenで定義
        $form->addElement("hidden","form_slip_check[$i]");
    }
*/
}

// 発行ボタン
$form->addElement("button", "form_sale_slip", "発　行", "
    onClick=\"javascript:Post_book_vote2('売上伝票を発行します。', 'sale_slip_flg',
    '".FC_DIR."sale/2-2-205.php', '".$_SESSION["PHP_SELF"]."', 'form_slip_check', $match_count)\"
");

// 再発行ボタン
$form->addElement("button", "form_sale_republish", "再発行", "
    onClick=\"javascript:Post_book_vote2('売上伝票を再発行します。', 'sale_republish_flg',
    '".FC_DIR."sale/2-2-205.php', '".$_SESSION["PHP_SELF"]."', 'form_republish_check', $match_count)\"
");

// 出荷案内書にデフォルト値セット
$form->setConstants($set_data);


/****************************/
// HTML用関数
/****************************/
function Number_Format_Color($num){
    return ($num < 0) ? "<span style=\"color: #ff0000;\">".number_format($num)."</span>" : number_format($num);
}


/****************************/
// HTML作成（検索部）
/****************************/
// 共通検索テーブル
$html_s .= Search_Table($form, true);
// モジュール個別検索テーブル１
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">伝票番号</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_slip_no"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">売上金額</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_sale_amount"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// モジュール個別検索テーブル２
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
if ($_SESSION["group_kind"] == "2"){
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";
}
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">売上計上日</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_sale_day"]]->toHtml()."</td>\n";
if ($_SESSION["group_kind"] == "2"){
$html_s .= "        <td class=\"Td_Search_3\">契約区分</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_contract_div"]]->toHtml()."</td>\n";
}
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// モジュール個別検索テーブル３
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"746px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">グループ</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_client_gr"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// モジュール個別検索テーブル４
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">取引区分</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_trade"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">日次更新</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_renew"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// モジュール個別検索テーブル５
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
#2010-04-26 hashimot-y
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";

$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">リピート</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_repeat"]]->toHtml()."</td>\n";
#2010-04-26 hashimot-y
$html_s .= "        <td class=\"Td_Search_3\">アイテム</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_goods"]]->toHtml()."</td>\n";

$html_s .= "    </tr>\n";
$html_s .= "</table>\n";


// モジュール個別検索テーブル５
#2010-04-26 hashimot-y
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";

$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">販売区分</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_divide"]]->toHtml()."</td>\n";

$html_s .= "    </tr>\n";
$html_s .= "</table>\n";


// ボタン
$html_s .= "<table align=\"right\"><tr><td>\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["form_display"]]->toHtml()."　\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["form_clear"]]->toHtml()."\n";
$html_s .= "</td></tr></table>";
$html_s .= "\n";


/****************************/
// HTML作成（一覧部）
/****************************/
if ($post_flg == true){

    // ページ分け
    $html_page  = Html_Page2($total_count, $page_count, 1, $limit);
    $html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

    // 一覧テーブル
    $html_1  = "\n";
    $html_1 .= "<table width=\"100%\" border=\"0\">\n";
    $html_1 .= "    <tr>\n";
    $html_1 .= "        <td align=\"right\">\n";
    $html_1 .= "        <span style=\"color: #0000ff; font-weight: bold;\">※リピート欄には契約マスタに登録のないものが「不定期」と表示されます</span>\n";
    $html_1 .= "        </td>\n";
    $html_1 .= "</table>\n";
    $html_1 .= "<table class=\"List_Table\" width=\"100%\" border=\"1\">\n";
    $html_1 .= "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">No.</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_client_name")."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_slip")."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_sale_day")."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_round_staff")."</td>\n";
    if ($_SESSION["group_kind"] == "2"){
        $html_1 .= "        <td class=\"Title_Act\" rowspan=\"2\">".Make_Sort_Link($form, "sl_act_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_act_client_name")."</td>\n";
        $html_1 .= "        <td class=\"Title_Act\" rowspan=\"2\">代行委託料</td>\n";
    }
    $html_1 .= "        <td class=\"Title_Act\" rowspan=\"2\">紹介口座料</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">取引区分</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">原価金額</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" colspan=\"2\">掛売金額</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" colspan=\"2\">現金金額</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">前受<br>相殺額</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">変更</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">削除</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">分割<br>回数</td>\n";
    //2009-09-08 hashimoto-y
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">ﾘﾋﾟｰﾄ</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">日次更新</td>\n";
//    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">出荷案内書</td>";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">";
    $html_1 .=          $form->_elements[$form->_elementIndex["form_slip_all_check"]]->toHtml()."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">";
    $html_1 .=          $form->_elements[$form->_elementIndex["form_republish_all_check"]]->toHtml()."</td>\n";
    $html_1 .= "    </tr>\n";
    $html_1 .= "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
    $html_1 .= "        <td class=\"Title_Pink\">税抜</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\">税込</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\">税抜</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\">税込</td>\n";
    $html_1 .= "    </tr>\n";
    $html_1 .= "    <tr class=\"Result3\">\n";
    $html_1 .= "        <td><b>合計</b></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    if ($_SESSION["group_kind"] == "2"){
    $html_1 .= "        <td></td>\n";
    //$html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_act_amount)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_act_amount)."<br>".Numformat_Ortho($minus_act_amount)."<br>".Numformat_Ortho($sum_act_amount)."<br></td>\n";
    }
/*
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_intro_amount)."</td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($sum_advance_offset)."</td>\n";
*/
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_intro_amount)."<br>".Numformat_Ortho($minus_intro_amount)."<br>".Numformat_Ortho($sum_intro_amount)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_cost_amount)."<br>".Numformat_Ortho($minus_cost_amount)."<br>".Numformat_Ortho($sum_cost_amount)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_notax_amount)."<br>".Numformat_Ortho($minus_kake_notax_amount)."<br>".Numformat_Ortho($kake_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_ontax_amount)."<br>".Numformat_Ortho($minus_kake_ontax_amount)."<br>".Numformat_Ortho($kake_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_notax_amount)."<br>".Numformat_Ortho($minus_genkin_notax_amount)."<br>".Numformat_Ortho($genkin_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_ontax_amount)."<br>".Numformat_Ortho($minus_genkin_ontax_amount)."<br>".Numformat_Ortho($genkin_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_advance_offset)."<br>".Numformat_Ortho($minus_advance_offset)."<br>".Numformat_Ortho($sum_advance_offset)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    //2009-09-08 hashimoto-y
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "    </tr>\n";
    if ($ary_sale_data != null){
    foreach ($ary_sale_data as $key => $value){
        if (bcmod($key, 2) == 0){
        $html_1 .= "    <tr class=\"Result1\"".$value["return"].">\n";
        }else{
        $html_1 .= "    <tr class=\"Result2\"".$value["return"].">\n";
        }
        $html_1 .= "        <td align=\"right\">".((($page_count - 1) * $limit) + $key + 1)."</td>\n";
        $html_1 .= "        <td>".$value["client_cd"]."<br>".htmlspecialchars($value["client_cname"])."<br></td>\n";
        $html_1 .= "        <td align=\"center\">".$value["slip_no_link"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["sale_day"]."</td>\n";
        $html_1 .= "        <td>".$value["staff"]."</td>\n";
        if ($_SESSION["group_kind"] == "2"){
            $html_1 .= "        <td>".$value["act_cd"]."<br>".htmlspecialchars($value["act_cname"])."<br></td>\n";
            $html_1 .= "        <td align=\"right\">".Numformat_Ortho($value["act_amount"], null, true)."</td>\n";
        }
        $html_1 .= "        <td align=\"right\">".Numformat_Ortho($value["intro_amount"], null, true)."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["trade"]."</td>";
        $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["cost_amount"])."</td>";
        if ($value["trade_type"] == "kake"){
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["net_amount"])."</td>\n";
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["sale_amount"])."</td>\n";
            $html_1 .= "        <td></td>\n";
            $html_1 .= "        <td></td>\n";
        }else{
            $html_1 .= "        <td></td>\n";
            $html_1 .= "        <td></td>\n";
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["net_amount"])."</td>\n";
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["sale_amount"])."</td>\n";
        }
        $html_1 .= "        <td align=\"right\">".Numformat_Ortho($value["advance_offset_totalamount"], null, true)."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["chg_link"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["del_link"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["split_link"]."</td>\n";
        //2009-09-08 hashimoto-y
        $html_1 .= "        <td align=\"center\">".$value["repeat"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["renew_day"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_slip_check[$key]"]]->toHtml()."</td>\n";
        $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_republish_check[$key]"]]->toHtml()."</td>\n";
        $html_1 .= "    </tr>\n";
    }
    }
    $html_1 .= "    <tr class=\"Result3\">\n";
    $html_1 .= "        <td><b>合計</b></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    if ($_SESSION["group_kind"] == "2"){
    $html_1 .= "        <td></td>\n";
    //$html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_act_amount)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_act_amount)."<br>".Numformat_Ortho($minus_act_amount)."<br>".Numformat_Ortho($sum_act_amount)."<br></td>\n";
    }
/*
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_intro_amount)."</td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($sum_advance_offset)."</td>\n";
*/
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_intro_amount)."<br>".Numformat_Ortho($minus_intro_amount)."<br>".Numformat_Ortho($sum_intro_amount)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_cost_amount)."<br>".Numformat_Ortho($minus_cost_amount)."<br>".Numformat_Ortho($sum_cost_amount)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_notax_amount)."<br>".Numformat_Ortho($minus_kake_notax_amount)."<br>".Numformat_Ortho($kake_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_ontax_amount)."<br>".Numformat_Ortho($minus_kake_ontax_amount)."<br>".Numformat_Ortho($kake_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_notax_amount)."<br>".Numformat_Ortho($minus_genkin_notax_amount)."<br>".Numformat_Ortho($genkin_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_ontax_amount)."<br>".Numformat_Ortho($minus_genkin_ontax_amount)."<br>".Numformat_Ortho($genkin_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_advance_offset)."<br>".Numformat_Ortho($minus_advance_offset)."<br>".Numformat_Ortho($sum_advance_offset)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    //2009-09-08 hashimoto-y
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_sale_slip"]]->toHtml()."</td>\n";
    $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_sale_republish"]]->toHtml()."</td>\n";
    $html_1 .= "    </tr>\n";
    $html_1 .= "</table>\n";
    $html_1 .= "\n";

    // 合計テーブル
    $html_2  = "\n";
    $html_2 .= "<table class=\"List_Table\" border=\"1\" width=\"500px\" align=\"right\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"right\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"right\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"right\">\n";
    $html_2 .= "    <tr class=\"Result1\">\n";
    $html_2 .= "        <td class=\"Title_Pink\">掛売金額</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($kake_notax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">掛売消費税</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($kake_tax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">掛売合計</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($kake_ontax)."</td>\n";
    $html_2 .= "    </tr>\n";
    $html_2 .= "    <tr class=\"Result1\">\n";
    $html_2 .= "        <td class=\"Title_Pink\">現金金額</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($genkin_notax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">現金消費税</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($genkin_tax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">現金合計</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($genkin_ontax)."</td>\n";
    $html_2 .= "    </tr>\n";
    $html_2 .= "    <tr class=\"Result1\">\n";
    $html_2 .= "        <td class=\"Title_Pink\">税抜合計</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($notax_amount)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">消費税合計</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($tax_amount)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">税込合計</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($ontax_amount)."</td>\n";
    $html_2 .= "    </tr>\n";
    $html_2 .= "</table>\n";
    $html_2 .= "\n";

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
// 画面ヘッダー作成
/****************************/
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

    "renew_flg"     => "$renew_flg",
    "act_buy_renew_err"     => "$act_buy_renew_err",
    "intro_buy_renew_err"   => "$intro_buy_renew_err",
));

// htmlをassign
$smarty->assign("html", array(
    "html_page"     =>  $html_page,
    "html_page2"    =>  $html_page2,
    "html_s"        =>  $html_s,
    "html_1"        =>  $html_1,
    "html_2"        =>  $html_2,
));

// msgをassign
$smarty->assign("msg", array(
    "err_msg_print" => $err_msg_print,
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF].".tpl"));

?>
