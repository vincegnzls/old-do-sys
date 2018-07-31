<?php

/******************************
 *  変更履歴
 *      ・（2006-10-26）売上率が０％の巡回担当者を表示<suzuki>
 *
 *
******************************/
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2007/03/05      要望6-3     kajioka-h   アイテム名を「正式（商品分類＋商品名）」と「略称」表示に
 *  2007/03/09      xx-xxx      kajioka-h   内訳、コース表示を削除
 *  2007/03/22      要望21      kajioka-h   伝票削除処理を追加
 *  2007/03/26      要望21      kajioka-h   確定、報告、承認機能を追加
 *  2007/03/27      要望21      kajioka-h   商品予定出荷済の伝票の削除時に商品を戻す倉庫を指定可能に
 *  2007/04/05      その他25    kajioka-h   代行料・紹介料を本部の仕入する場合のチェック処理追加
 *  2007/04/06      要望1-4     kajioka-h   紹介口座の仕様変更により、紹介口座名、紹介口座金額と、備考を表示
 *  2007/04/13      その他      kajioka-h   「配送日」→「予定巡回日」に表示変更
 *  2007/04/27      他79,152    kajioka-h   代行料の仕様変更
 *  2007/05/08      その他      kajioka-h   「順路」「得意先CD」「受注ID」順に表示
 *  2007/05/17      xx-xxx      kajioka-h   予定データ明細、予定データ訂正、売上伝票訂正、手書伝票でレイアウトを合わせた
 *  2007/05/23      xx-xxx      kajioka-h   代行伝票の現金取引可能により、エラーメッセージ追加
 *  2007/05/24      B0702-057   kajioka-h   備考の改行が余計に改行されているバグ修正
 *  2007/06/05      要件64      kajioka-h   予定手書で作った伝票は予定手書に遷移するように
 *                  要件64      kajioka-h   予定手書で作った伝票は直送先を表示するように
 *  2007/06/14      その他14    kajioka-h   前受金
 *  2007/06/25                  fukuda      確定/承認時に予定巡回日が未来の場合はエラー
 *  2007/08/20      保守66      kajioka-h   予定巡回日の一括訂正機能
 *  2009/09/18                  aoyama-n    値引商品の場合は赤字で表示
 *  2009/10/06      rev.1.3     kajioka-h   予定巡回日の訂正時に2ヶ月以上前だと警告メッセージ追加
 *  2010/01/23      rev.1.4     hashimoto-y 一括訂正を行った場合に、受注ヘッダの消費税額を巡回日に応じて更新する処理を追加
 *
 */


$page_title = "予定データ明細";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

//echo "<font color=red style=\"font-family: 'HGS行書体';\"><b>編集中につき、動きません</b></font>";

// 権限チェック
$auth       = Auth_Check($db_con);
// 権限チェック
//$auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/****************************/
//契約関数定義
/****************************/
require_once(INCLUDE_DIR."function_keiyaku.inc");

require_once(PATH ."function/trade_fc.fnc");


//--------------------------//
//エラーメッセージファイル
//--------------------------//
require_once(INCLUDE_DIR."error_msg_list.inc");


/*****************************/
//外部変数取得
/*****************************/
$id           = $_GET["aord_id"];        //受注ID
$array_id     = $_GET["aord_id_array"];  //該当日の全ての受注ID
$back_display = $_GET["back_display"];   //遷移元
$staff_id     = $_GET["staff_id"];       //巡回担当者ID
$client_id    = $_SESSION["client_id"];  //取引先ID
$shop_id      = $_SESSION["client_id"];  //取引先ID
$group_kind   = $_SESSION["group_kind"]; //グループ種別

//受注ID複数判定
if($id != NULL && $array_id == NULL){
    //一つの得意先リンクから遷移

    $aord_id = $id;
}else{
    //該当日の日付リンクから遷移

    //アンシリアライズ化
    $array_id = stripslashes($array_id);
    $array_id = urldecode($array_id);
    $array_id = unserialize($array_id);

    $count = count($array_id);
    for($i=0, $array_id_tmp=""; $i<$count; $i++){
        $array_id_tmp .= $array_id[$i].", ";
    }
    $array_id_tmp = substr($array_id_tmp, 0, strlen($array_id_tmp)-2);

    if($array_id_tmp == null){
        header("Location: ".Make_Rtn_Page("contract"));
        exit();
    }

    //順路、得意先CD、受注IDで並べ替え
    $sql  = "SELECT \n";
    $sql .= "    aord_id \n";
    $sql .= "FROM \n";
    $sql .= "    t_aorder_h \n";
    $sql .= "WHERE \n";
    $sql .= "    aord_id IN (".$array_id_tmp.") \n";
    $sql .= "ORDER BY \n";
    $sql .= "    route, \n";
    $sql .= "    client_cd1, \n";
    $sql .= "    client_cd2, \n";
    $sql .= "    aord_id \n";
    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    $count = pg_num_rows($result);

    for($i=0, $array_id=array(); $i<$count; $i++){
        $array_id[] = pg_fetch_result($result, $i, 0);
    }



    $aord_id = $array_id;
}
Get_Id_Check2($aord_id);

//不正判定
Get_ID_Check3($id);
Get_ID_Check3($staff_id);
Get_ID_Check3($array_id);

/*****************************/
//フォーム作成
/*****************************/
// 戻るボタン遷移先判定
switch($back_display){
    case "cal_month" :
        // 巡回カレンダー(月)
        $form->addElement("button","modoru","戻　る","onClick=\"location.href='".FC_DIR."sale/2-2-101-2.php?search=1'\"");
        break;
    case "cal_week" :
        // 巡回カレンダー(週)
        $form->addElement("button","modoru","戻　る","onClick=\"location.href='".FC_DIR."sale/2-2-102-2.php?search=1'\"");
        break;
    case "confirm" :
        // 予定伝票売上確定
        $form->addElement("button","modoru","戻　る","onClick=\"location.href='".FC_DIR."sale/2-2-206.php?search=1'\"");
        break;
    case "output" :
        // 伝票発行
        $form->addElement("button","modoru","戻　る","onClick=\"location.href='".FC_DIR."sale/2-2-204.php?search=1'\"");
        break;
    case "count_daily" :
        // 集計日報
        $form->addElement("button","modoru","戻　る","onClick=\"location.href='".FC_DIR."sale/2-2-113.php?search=1'\"");
        break;
    case "round" :
        // 委託巡回
        $form->addElement("button","modoru","戻　る","onClick=\"location.href='".FC_DIR."system/2-1-237.php?search=1'\"");
        break;
    case "round_act" :
        // 受託巡回
        $form->addElement("button","modoru","戻　る","onClick=\"location.href='".FC_DIR."system/2-1-238.php?search=1'\"");
        break;
    case "reserve" :
        // 削除伝票一覧
        $form->addElement("button","modoru","戻　る","onClick=\"javascript:location.href('../sale/2-2-209.php?search=1');\"");
        break;
    case "act_count" :
        // 代行集計表
        $form->addElement("button","modoru","戻　る","onClick=\"javascript:location.href('../sale/2-2-116.php?search=1');\"");
        break;
    Default:
        //無い場合は、カレンダー(月)に遷移
//        header("Location: ".FC_DIR."sale/2-2-101-2.php");
        header("Location: ".Make_Rtn_Page("contract"));
}

$form->addElement("hidden", "check_value_flg");     //予定データ訂正チェックボックス復元判定フラグ
$con_data["check_value_flg"]   = "t";
$form->setConstants($con_data);

$form->addElement("hidden", "hdn_slip_del");        //削除ボタン判定フラグ
$form->addElement("hidden", "hdn_confirm");         //確定ボタン判定フラグ
$form->addElement("hidden", "hdn_report");          //報告ボタン判定フラグ
$form->addElement("hidden", "hdn_accept");          //承認ボタン判定フラグ


//一括訂正
Addelement_Date($form, "form_lump_change_date", "", "-");   //日付入力フォーム
$form->addElement("button", "btn_lump_change", "一括訂正", " onClick=\"Dialogue_1('一括訂正します。', 'true', 'hdn_lump_change');\" $disabled");
$form->addElement("hidden", "hdn_lump_change");     //一括訂正ボタン判定フラグ

//rev.1.3 変更前予定巡回日
$form->addElement("hidden", "hdn_former_delivery_day");


//--------------------------//
//一括訂正ボタン押下処理
//--------------------------//
//if($_POST["hdn_lump_change"] == "true"){
//rev.1.3 警告ボタン押下時処理追加
if($_POST["hdn_lump_change"] == "true" || $_POST["form_lump_change_warn"] == "警告を無視して一括訂正"){

    //予定巡回日
    //●必須チェック
    $form->addGroupRule("form_lump_change_date", array(
        "y" => array(array($h_mess[35], "required")),
        "m" => array(array($h_mess[35], "required")),
        "d" => array(array($h_mess[35], "required")),
    ));
    //数値チェック
    $form->addGroupRule("form_lump_change_date", array(
        "y" => array(array($h_mess[35], "regex", "/^[0-9]+$/")),
        "m" => array(array($h_mess[35], "regex", "/^[0-9]+$/")),
        "d" => array(array($h_mess[35], "regex", "/^[0-9]+$/")),
    ));

    if($form->validate()){
        //妥当性チェック
        if(!checkdate((int)$_POST["form_lump_change_date"]["m"], (int)$_POST["form_lump_change_date"]["d"], (int)$_POST["form_lump_change_date"]["y"])){
            $form->setElementError("form_lump_change_date", $h_mess[35]);
        }

		//rev.1.3 警告無視ボタン押下されてない場合は2ヶ月以上離れているかチェック
		if($_POST["form_lump_change_warn"] != "警告を無視して一括訂正"){
			$b_lump_day = date("Y-m-d", mktime(0, 0, 0, $_POST["form_lump_change_date"]["m"] - 2, $_POST["form_lump_change_date"]["d"], $_POST["form_lump_change_date"]["y"]));	//2ヶ月前
			$a_lump_day = date("Y-m-d", mktime(0, 0, 0, $_POST["form_lump_change_date"]["m"] + 2, $_POST["form_lump_change_date"]["d"], $_POST["form_lump_change_date"]["y"]));	//2ヶ月後
			//2ヶ月以上離れている
			if(($_POST["hdn_former_delivery_day"] <= $b_lump_day) || ($_POST["hdn_former_delivery_day"] >= $a_lump_day)){
				$warn_lump_change = "入力した予定巡回日は2ヶ月以上離れています。";
				$form->addElement("submit", "form_lump_change_warn", "警告を無視して一括訂正");
			//守備範囲内
			}else{
				$warn_lump_change = null;
			}
		}else{
			$warn_lump_change = null;
		}

    }

    $error_flg = (count($form->_errors) > 0) ? true : false;


    //ここから一括訂正処理
    //if($error_flg == false){
	//rev.1.3 エラーじゃなく、2ヶ月以上離れていない
    if($error_flg == false && $warn_lump_change == null){

        $lump_change_date  = str_pad($_POST["form_lump_change_date"]["y"], 4, "0", STR_PAD_LEFT);
        $lump_change_date .= "-";
        $lump_change_date .= str_pad($_POST["form_lump_change_date"]["m"], 2, "0", STR_PAD_LEFT);
        $lump_change_date .= "-";
        $lump_change_date .= str_pad($_POST["form_lump_change_date"]["d"], 2, "0", STR_PAD_LEFT);


        //受注ID配列をIN句で使うためにカンマ区切りにする
        $count = count($aord_id);
        for($i=0, $array_id_tmp=""; $i<$count; $i++){
            $array_id_tmp .= $aord_id[$i].", ";
        }
        $array_id_tmp = substr($array_id_tmp, 0, strlen($array_id_tmp)-2);


        //WHERE句生成
        $where_sql .= "WHERE \n";
        if($group_kind == "2"){
            //直営
            $where_sql .= "    shop_id IN (".Rank_Sql().") \n";
        }else{
            //FC（自社巡回、または自分が代行先の伝票）
            $where_sql .= "    ( \n";
            $where_sql .= "        shop_id = $shop_id \n";
            $where_sql .= "        OR \n";
            $where_sql .= "        act_id = $shop_id \n";
            $where_sql .= "    ) \n";
        }
        $where_sql .= "    AND \n";
        $where_sql .= "    aord_id IN ($array_id_tmp) \n";
        $where_sql .= "    AND \n";
        $where_sql .= "    confirm_flg = false \n";         //売上確定していない
        $where_sql .= "    AND \n";
        $where_sql .= "    trust_confirm_flg = false \n";   //代行で報告していない
        $where_sql .= "    AND \n";
        $where_sql .= "    del_flg = false \n";             //削除伝票じゃない


        Db_Query($db_con, "BEGIN;");

        //更新対象の件数カウント
        $sql  = "SELECT \n";
        $sql .= "    COUNT(*) \n";
        $sql .= "FROM \n";
        $sql .= "    t_aorder_h \n";
        $sql .= $where_sql;
        $sql .= ";";

        $result = Db_Query($db_con, $sql);

        //更新対象が0件の場合
        if(pg_fetch_result($result, 0, 0) == 0){
            $lump_change_comp_mess = "一括訂正の対象の予定データがありません。";
            Db_Query($db_con, "ROLLBACK;");

        //更新対象がある場合
        }else{

            //対象の予定データを更新
            $sql  = "UPDATE \n";
            $sql .= "    t_aorder_h \n";
            $sql .= "SET \n";
            $sql .= "    ord_time = '$lump_change_date', \n";       //予定巡回日
            $sql .= "    arrival_day = '$lump_change_date', \n";    //請求日
            $sql .= "    change_flg = true, \n";                    //確定前変更フラグ
            $sql .= "    slip_flg = false, \n";                     //伝票出力フラグ
            $sql .= "    slip_out_day = NULL, \n";                  //売上伝票出力日
            $sql .= "    ord_staff_id = ".$_SESSION["staff_id"].", \n";                     //オペレータID
            $sql .= "    ord_staff_name = '".addslashes($_SESSION["staff_name"])."', \n";   //オペレータ名
            $sql .= "    ship_chk_cd = NULL \n";                    //変更チェックコード
            $sql .= $where_sql;
            $sql .= ";";
//print_array($sql);

            $result = Db_Query($db_con, $sql);





            #2010-01-23 hashimoto-y
            #受注ヘッダの消費税額変更処理を追加
            #予定巡回日を変更したデータのshop_idとclient_idを取得する
            $count = count($aord_id);
            for($i=0; $i<$count; $i++){

                #受注ヘッダのショップID
                #受注ヘッダのクライアントID
                $aorder_h_shop_id   = NULL;
                $aorder_h_client_id = NULL;


               //更新対象のshop_id,client_id抽出
                $sql  = "SELECT \n";
                $sql .= "    shop_id, \n";
                $sql .= "    client_id \n";
                $sql .= "FROM \n";
                $sql .= "    t_aorder_h \n";
                $sql .= "WHERE \n";
                $sql .= "    aord_id = ".$aord_id[$i];
                $sql .= ";";

                $result = Db_Query($db_con, $sql);
                $aorder_h_shop_id    = pg_fetch_result($result, 0, "shop_id");
                $aorder_h_client_id  = pg_fetch_result($result, 0, "client_id");


                #受託先も使用するため受注IDのshop_idを取得し、訂正するＦＣの訂正対象日付における消費税率を取得する
                #税率クラス　インスタンス生成
                $tax_rate_obj = new TaxRate($aorder_h_shop_id);

                $tax_rate_obj->setTaxRateDay($lump_change_date);

                $tax_num = $tax_rate_obj->getClientTaxRate($aorder_h_client_id);


                #消費税額クラス　インスタンス生成
                $tax_amount_obj = new TaxAmount();

                #変更する受注IDをgetAorderTaxAmountメソッドに渡し消費税額を計算
                $tax_amount = $tax_amount_obj->getAorderTaxAmount($tax_num, $aord_id[$i]);

                #受注ヘッダの消費税額更新
                $sql  = "UPDATE t_aorder_h SET ";
                $sql .= "    tax_amount = ".$tax_amount;
                $sql .= "WHERE ";
                $sql .= "    aord_id = ".$aord_id[$i].";";
                $result = Db_Query($db_con, $sql);

            }



            if($result == false){
                $lump_change_comp_mess = "一括訂正できませんでした。";
                Db_Query($db_con, "ROLLBACK;");
            }else{
                $lump_change_comp_mess = "一括訂正しました。";
                Db_Query($db_con, "COMMIT;");
            }

        }

    }//ここまで一括訂正処理


    $lump_con_data["hdn_lump_change"]   = "";   //一括訂正ボタン押下フラグを初期化

    $lump_con_data["hdn_slip_del"]  = "";       //削除ボタン押下フラグを初期化
    $lump_con_data["hdn_confirm"]   = "";       //確定ボタン押下時にセットした受注IDを初期化
    $lump_con_data["hdn_report"]    = "";       //報告ボタン押下時にセットした受注IDを初期化
    $lump_con_data["hdn_accept"]    = "";       //承認ボタン押下時にセットした受注IDを初期化
    $form->setConstants($lump_con_data);

    $_POST["hdn_confirm"]   = "";       //POSTの確定ボタンも初期化
    $_POST["hdn_report"]    = "";       //POSTの報告ボタンも初期化
    $_POST["hdn_accept"]    = "";       //POSTの承認ボタンも初期化

}


/*****************************/
//削除ボタン押下時
/*****************************/
if($_POST["hdn_slip_del"] != null){
    $array_tmp = explode(",", $_POST["hdn_slip_del"]);
    $del_line       = $array_tmp[0];        //何番目の伝票を削除する？
    $del_aord_id    = $array_tmp[1];        //削除する受注ID
    $to_ware_id     = $_POST["back_ware"][$del_line];   //移動先の倉庫ID

    //対象の伝票の情報を取得
    $sql  = "SELECT \n";
    $sql .= "    confirm_flg, \n";
    $sql .= "    trust_confirm_flg, \n";
    $sql .= "    move_flg, \n";
    $sql .= "    contract_div \n";
    $sql .= "FROM \n";
    $sql .= "    t_aorder_h \n";
    $sql .= "WHERE \n";
    $sql .= "    aord_id = $del_aord_id \n";
    $sql .= ";";
    $result = Db_Query($db_con, $sql);

    $chk_confirm_flg        = pg_fetch_result($result, 0, "confirm_flg");       //確定フラグ（代行のときは承認）
    $chk_trust_confirm_flg  = pg_fetch_result($result, 0, "trust_confirm_flg"); //巡回報告フラグ
    $chk_move_flg           = pg_fetch_result($result, 0, "move_flg");          //商品予定出荷済フラグ
    $contract_div           = pg_fetch_result($result, 0, "contract_div");      //契約区分

    //エラーチェック
    $error_flg = false;

    //商品予定出荷済で、
    //・自社巡回、
    //・オンライン代行で受託先画面
    //の場合、在庫移動先倉庫指定セレクトの必須チェック
    if($chk_move_flg == "t" &&
        ($contract_div == "1" || ($contract_div == "2" && $group_kind != "2"))
    ){
        $form->addRule("back_ware[$del_line]", "在庫を返却する倉庫を指定してください。", "required");
    }

    //確定チェック
    //自社巡回の場合
    if($contract_div == "1" && $chk_confirm_flg == "t"){
        $form->setElementError("del_err_mess", "伝票が売上確定されているため、削除できません。");
        $error_flg = true;
    //オンライン代行で直営、またはオフライン代行の場合
    }elseif(($contract_div == "2" || $contract_div == "3") && $chk_confirm_flg == "t"){
        $form->setElementError("del_err_mess", "伝票が巡回承認されているため、削除できません。");
        $error_flg = true;
    //オンライン代行でFCが代行料を売上げた場合
    }elseif($contract_div == "2" && $chk_trust_confirm_flg == "t"){
        $form->setElementError("del_err_mess", "伝票が巡回報告されているため、削除できません。");
        $error_flg = true;
    }


    //エラーじゃない場合、削除処理スタート
    if($form->validate() && $error_flg != true){
        Db_Query($db_con, "BEGIN;");

        //削除フラグをtrueに
        $sql = "UPDATE t_aorder_h SET del_flg = true WHERE aord_id = $del_aord_id;";
        $result = Db_Query($db_con, $sql);
        if($result == false){
            Db_Query($db_con, "ROLLBACK;");
            exit();
        }

        //受払を消す
        $sql = "DELETE FROM t_stock_hand WHERE aord_d_id IN (SELECT aord_d_id FROM t_aorder_d WHERE aord_id = $del_aord_id);";
        $result = Db_Query($db_con, $sql);
        if($result == false){
            Db_Query($db_con, "ROLLBACK;");
            exit();
        }

        //返却倉庫がある場合、移動済み商品を戻す
        if($_POST["back_ware"][$del_line] != null){

            //移動元（担当）倉庫IDを取得
            $sql = "SELECT staff_id FROM t_aorder_staff WHERE aord_id = $del_aord_id AND staff_div = '0';";
            $result = Db_Query($db_con, $sql);
            if($result == false){
                Db_Query($db_con, "ROLLBACK;");
                exit();
            }
            $from_ware_id = Get_Staff_Ware_Id($db_con, pg_fetch_result($result, "staff_id"));


            //出庫品テーブルから戻す商品を取得
            $sql  = "SELECT aord_d_id, goods_id, num \n";
            $sql .= "FROM t_aorder_ship \n";
            $sql .= "WHERE aord_d_id IN ( \n";
            $sql .= "    SELECT aord_d_id FROM t_aorder_d WHERE aord_id = $del_aord_id \n";
            $sql .= ");";

            $result = Db_Query($db_con, $sql);
            if($result == false){
                Db_Query($db_con, "ROLLBACK;");
                exit();
            }
            $stock_hand_data = Get_Data($result, 3);

            $stock_hand_data_count = pg_num_rows($result);
            for($i=0; $i<$stock_hand_data_count; $i++){
                //担当倉庫から出庫
                $sql  = "INSERT INTO t_stock_hand ( ";
                $sql .= "    goods_id, ";
                $sql .= "    enter_day, ";
                $sql .= "    work_day, ";
                $sql .= "    work_div, ";
                $sql .= "    ware_id, ";
                $sql .= "    io_div, ";
                $sql .= "    num, ";
                $sql .= "    aord_d_id, ";
                $sql .= "    staff_id, ";
                $sql .= "    shop_id ";
                $sql .= ") VALUES ( ";
                $sql .= "    ".$stock_hand_data[$i][1].", ";
                $sql .= "    CURRENT_TIMESTAMP, ";
                $sql .= "    CURRENT_TIMESTAMP, ";
                $sql .= "    '5', ";
                $sql .= "    ".$from_ware_id.", ";
                $sql .= "    '2', ";
                $sql .= "    ".$stock_hand_data[$i][2].", ";
                $sql .= "    ".$stock_hand_data[$i][0].", ";
                $sql .= "    ".$_SESSION["staff_id"].", ";
                $sql .= "    ".$_SESSION["client_id"]." ";
                $sql .= "); ";
                $result = Db_Query($db_con, $sql);
                if($result == false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit();
                }

                //拠点倉庫へ入庫
                $sql  = "INSERT INTO t_stock_hand ( ";
                $sql .= "    goods_id, ";
                $sql .= "    enter_day, ";
                $sql .= "    work_day, ";
                $sql .= "    work_div, ";
                $sql .= "    ware_id, ";
                $sql .= "    io_div, ";
                $sql .= "    num, ";
                $sql .= "    aord_d_id, ";
                $sql .= "    staff_id, ";
                $sql .= "    shop_id ";
                $sql .= ") VALUES ( ";
                $sql .= "    ".$stock_hand_data[$i][1].", ";
                $sql .= "    CURRENT_TIMESTAMP, ";
                $sql .= "    CURRENT_TIMESTAMP, ";
                $sql .= "    '5', ";
                $sql .= "    ".$to_ware_id.", ";
                $sql .= "    '1', ";
                $sql .= "    ".$stock_hand_data[$i][2].", ";
                $sql .= "    ".$stock_hand_data[$i][0].", ";
                $sql .= "    ".$_SESSION["staff_id"].", ";
                $sql .= "    ".$_SESSION["client_id"]." ";
                $sql .= "); ";
                $result = Db_Query($db_con, $sql);
                if($result == false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit();
                }
            }
        }

        Db_Query($db_con, "COMMIT;");


        //受注IDの配列を詰めなおす
        $aord_id_tmp = array();
        foreach($aord_id as $value){
            if($value != $del_aord_id){
                $aord_id_tmp[] = $value;
            }
        }
        $aord_id = $aord_id_tmp;

        //表示する予定データが0件になった場合は、エラーメッセージの下に戻るボタンを表示する
        $modoru_disp_flg = (count($aord_id) == 0) ? true : false;

        $del_comp_mess = "削除しました。";      //完了メッセージ

    //エラーの場合
    }else{
        $form->addElement("text", "del_err_mess");

        $modoru_disp_flg = false;               //戻るボタンは表示しない
        $del_comp_mess = null;                  //完了メッセージ
    }

    $del_con_data["hdn_slip_del"]   = "";       //削除ボタン押下フラグを初期化
    $del_con_data["hdn_confirm"]    = "";       //確定ボタン押下時にセットした受注IDを初期化
    $del_con_data["hdn_report"]     = "";       //報告ボタン押下時にセットした受注IDを初期化
    $del_con_data["hdn_accept"]     = "";       //承認ボタン押下時にセットした受注IDを初期化
    $form->setConstants($del_con_data);

    $_POST["hdn_confirm"]   = "";       //POSTの確定ボタンも初期化
    $_POST["hdn_report"]    = "";       //POSTの報告ボタンも初期化
    $_POST["hdn_accept"]    = "";       //POSTの承認ボタンも初期化
}


/*****************************/
//確定ボタン押下時
/*****************************/
if($_POST["hdn_confirm"] != null || $_POST["warn_confirm_flg"] == true){
    $aord_array[0] = $_POST["hdn_confirm"];             //確定する受注ID

    require(INCLUDE_DIR."fc_sale_confirm.inc");

    if($move_warning == null){
        $confirm_con_data["hdn_confirm"]    = "";       //確定ボタン押下時にセットした受注IDを初期化
    }
    $confirm_con_data["hdn_slip_del"]       = "";       //削除ボタン押下フラグを初期化
    $confirm_con_data["hdn_report"]         = "";       //報告ボタン押下時にセットした受注IDを初期化
    $form->setConstants($confirm_con_data);

    $_POST["hdn_slip_del"]  = "";       //POSTの削除ボタンも初期化
}


/****************************/
//報告ボタン押下処理
/****************************/
if($_POST["hdn_report"] != null || $_POST["warn_report_flg"] == true){
    $aord_array[0] = $_POST["hdn_report"];              //報告する受注ID

    require(INCLUDE_DIR."fc_sale_report.inc");

    if($move_warning == null){
        $repo_con_data["hdn_report"]        = "";       //報告ボタン押下時にセットした受注IDを初期化
    }
    $repo_con_data["hdn_slip_del"]          = "";       //削除ボタン押下フラグを初期化
    $repo_con_data["hdn_confirm"]           = "";       //確定ボタン押下時にセットした受注IDを初期化
    $form->setConstants($repo_con_data);

    $_POST["hdn_slip_del"]  = "";       //POSTの削除ボタンも初期化
}


/****************************/
//承認ボタン押下処理
/****************************/
if($_POST["hdn_accept"] != null || $_POST["warn_accept_flg"] == true){
    $aord_array[0] = $_POST["hdn_accept"];              //承認する受注ID

    require(INCLUDE_DIR."fc_sale_accept.inc");

    $accept_con_data["hdn_accept"]          = "";       //承認ボタン押下時にセットした受注IDを初期化
    $accept_con_data["hdn_slip_del"]        = "";       //削除ボタン押下フラグを初期化
    $form->setConstants($accept_con_data);

    $_POST["hdn_slip_del"]  = "";       //POSTの削除ボタンも初期化
}


/*****************************/
//明細データ配列取得
/*****************************/
//伝票分配列作成
for($s=0;$s<count($aord_id);$s++){

    /****************************/
    //受注ヘッダ抽出SQL
    /****************************/
    $sql  = "SELECT ";
    $sql .= "    t_aorder_h.ord_no,";                     //伝票番号0
    $sql .= "    t_aorder_h.ord_time,";                   //予定巡回日1
    $sql .= "    t_aorder_h.route,";                      //順路2
    $sql .= "    t_aorder_h.client_cd1,";                 //得意先cd13
    $sql .= "    t_aorder_h.client_cd2,";                 //得意先cd24
    $sql .= "    t_aorder_h.client_cname,";               //得意先名5
    $sql .= "    CASE t_aorder_h.trade_id";               //取引区分6
    $sql .= "        WHEN '11' THEN '掛売上'";
    $sql .= "        WHEN '13' THEN '掛返品'";
    $sql .= "        WHEN '14' THEN '掛値引'";
    $sql .= "        WHEN '61' THEN '現金売上'";
    $sql .= "        WHEN '63' THEN '現金返品'";
    $sql .= "        WHEN '64' THEN '現金値引'";
    $sql .= "    END,";                       
    $sql .= "    t_aorder_h.hope_day,";                   //売上計上日7
    $sql .= "    t_aorder_h.arrival_day,";                //請求日8
    $sql .= "    '1:' || t_staff1.staff_name || "; //担当者１・売上率１9
    $sql .= "    '(' || t_staff1.sale_rate || '%)',"; 
    $sql .= "    '2:' || t_staff2.staff_name || "; //担当者２・売上率２10
    $sql .= "    '(' || t_staff2.sale_rate || '%)',"; 
    $sql .= "    '3:' || t_staff3.staff_name || "; //担当者３・売上率３11
    $sql .= "    '(' || t_staff3.sale_rate || '%)',"; 
    $sql .= "    '4:' || t_staff4.staff_name || "; //担当者４・売上率４12
    $sql .= "    '(' || t_staff4.sale_rate || '%)',"; 
    $sql .= "    t_aorder_h.net_amount, ";                //商品合計13
    $sql .= "    t_aorder_h.tax_amount, ";                //消費税14
    $sql .= "    t_aorder_h.reason_cor,";                 //訂正理由15
    $sql .= "    t_aorder_h.aord_id, ";                   //受注ID 16
    $sql .= "    t_aorder_h.client_id,";                  //得意先ID 17
    $sql .= "    t_aorder_h.confirm_flg,";                //確定フラグ 18
    $sql .= "    t_aorder_h.act_id, ";                    //代行先ID19
    $sql .= "    t_aorder_h.contract_div,";               //契約区分20
    $sql .= "    t_aorder_h.trust_confirm_flg, ";         //確定フラグ(受託先) 21
    //$sql .= "    t_aorder_h.reserve_del_flg ";            //保留伝票削除フラグ 22
    $sql .= "    t_aorder_h.del_flg, ";                     //削除フラグ 22
    $sql .= "    t_aorder_h.move_flg, ";                    //商品予定出荷で在庫移動済フラグ 23
    $sql .= "    t_aorder_h.note, ";                        //備考 24
    $sql .= "    t_aorder_h.trust_note, ";                  //備考（受託先用） 25
    $sql .= "    t_aorder_h.intro_account_id, ";            //紹介者ID 26
    $sql .= "    t_aorder_h.intro_ac_cd1, ";                //紹介者CD1 27
    $sql .= "    t_aorder_h.intro_ac_cd2, ";                //紹介者CD2 28
    $sql .= "    t_aorder_h.intro_ac_name, ";               //紹介者名 29
    $sql .= "    t_aorder_h.intro_ac_div, ";                //紹介口座区分 30
    $sql .= "    t_aorder_h.intro_amount, ";                //紹介口座金額 31
    $sql .= "    t_aorder_h.act_div, ";                     //代行料区分 32
    $sql .= "    t_aorder_h.act_request_rate, ";            //代行料（％）33
    $sql .= "    t_aorder_h.act_request_price, ";           //代行料（固定額）34
    $sql .= "    t_aorder_h.trust_net_amount, ";            //売上金額（受託先）35
    $sql .= "    t_aorder_h.trust_tax_amount, ";            //消費税額（受託先）36
    $sql .= "    t_aorder_h.act_cd1, ";                     //代行者CD1 37
    $sql .= "    t_aorder_h.act_cd2, ";                     //代行者CD2 38
    $sql .= "    t_aorder_h.act_name, ";                    //代行者名 39
    $sql .= "    t_aorder_h.hand_plan_flg, ";               //予定手書フラグ 40
    $sql .= "    t_aorder_h.direct_id, ";                   //直送先ID 41
    $sql .= "    t_direct.direct_cd, ";                     //直送先CD 42
    $sql .= "    t_direct.direct_cname, ";                  //直送先名（略称） 43
    $sql .= "    t_d_claim.client_cname, ";                 //直送先の請求先名（略称） 44
    $sql .= "    t_aorder_h.claim_div, ";                   //請求先区分 45
    $sql .= "    t_aorder_h.advance_offset_totalamount ";   //前受相殺額合計 46

    $sql .= "FROM ";
    $sql .= "    t_aorder_h ";
    $sql .= "    INNER JOIN t_client ON t_client.client_id = t_aorder_h.client_id ";
    $sql .= "    LEFT JOIN t_direct ON t_aorder_h.direct_id = t_direct.direct_id ";
    $sql .= "    LEFT JOIN t_client AS t_d_claim ON t_direct.client_id = t_d_claim.client_id ";

    $sql .= "    LEFT JOIN ";
    $sql .= "        (SELECT ";
    $sql .= "             t_aorder_staff.aord_id,";
    $sql .= "             t_aorder_staff.staff_name,";
    $sql .= "             t_aorder_staff.sale_rate ";
    $sql .= "         FROM ";
    $sql .= "             t_aorder_staff ";
    $sql .= "         WHERE ";
    $sql .= "             t_aorder_staff.staff_div = '0'";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/25　02-015　　　　suzuki-t　　売上率が０％の担当者表示
 *
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate != '0'\n";
*/
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
    $sql .= "        )AS t_staff1 ON t_staff1.aord_id = t_aorder_h.aord_id ";
     
    $sql .= "    LEFT JOIN ";
    $sql .= "        (SELECT ";
    $sql .= "             t_aorder_staff.aord_id,";
    $sql .= "             t_aorder_staff.staff_name,";
    $sql .= "             t_aorder_staff.sale_rate ";
    $sql .= "         FROM ";
    $sql .= "             t_aorder_staff ";
    $sql .= "         WHERE ";
    $sql .= "             t_aorder_staff.staff_div = '1'";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/25　02-015　　　　suzuki-t　　売上率が０％の担当者表示
 *
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate != '0'\n";
*/
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
    $sql .= "        )AS t_staff2 ON t_staff2.aord_id = t_aorder_h.aord_id ";

    $sql .= "    LEFT JOIN ";
    $sql .= "        (SELECT ";
    $sql .= "             t_aorder_staff.aord_id,";
    $sql .= "             t_aorder_staff.staff_name,";
    $sql .= "             t_aorder_staff.sale_rate ";
    $sql .= "         FROM ";
    $sql .= "             t_aorder_staff ";
    $sql .= "         WHERE ";
    $sql .= "             t_aorder_staff.staff_div = '2'";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/25　02-015　　　　suzuki-t　　売上率が０％の担当者表示
 *
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate != '0'\n";
*/
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
    $sql .= "        )AS t_staff3 ON t_staff3.aord_id = t_aorder_h.aord_id ";

    $sql .= "    LEFT JOIN ";
    $sql .= "        (SELECT ";
    $sql .= "             t_aorder_staff.aord_id,";
    $sql .= "             t_aorder_staff.staff_name,";
    $sql .= "             t_aorder_staff.sale_rate ";
    $sql .= "         FROM ";
    $sql .= "             t_aorder_staff ";
    $sql .= "         WHERE ";
    $sql .= "             t_aorder_staff.staff_div = '3'";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/25　02-015　　　　suzuki-t　　売上率が０％の担当者表示
 *
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate != '0'\n";
*/
    $sql .= "         AND \n";
    $sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
    $sql .= "        )AS t_staff4 ON t_staff4.aord_id = t_aorder_h.aord_id ";

    $sql .= "WHERE ";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-017　　　　suzuki-t　　取引状態に関係なく得意先表示
 *
    $sql .= "    t_client.state = '1' ";
    $sql .= "AND ";
*/
    $sql .= "    t_aorder_h.aord_id = ".$aord_id[$s]." \n";
    $sql .= ";";
//print($sql);

    $result = Db_Query($db_con,$sql);
    $h_data_list[$s] = Get_Data($result);

    //FC側の代行伝票表示判定
    //if($h_data_list[$s][0][19] != NULL && $group_kind == 3){
    //FCはオフライン代行を見えなくする
    if($h_data_list[$s][0][19] != NULL && $h_data_list[$s][0][20] == "2" && $group_kind == 3){
        //ＦＣ側のオンライン代行伝票

        /****************************/
        //不正値判定関数
        /****************************/
        Injustice_check($db_con,"a_trust",$aord_id[$s],$client_id);
    }else{
        //通常伝票・オフライン代行伝票・直営側のオンライン代行伝票

        /****************************/
        //不正値判定関数
        /****************************/
        Injustice_check($db_con,"aorder",$aord_id[$s],$client_id);
    }

    /****************************/
    //請求先・巡回形式取得
    /****************************/
    $sql  = "SELECT ";
    $sql .= "    t_client.client_cname,";    //請求先名
    $sql .= "    t_aorder_h.round_form,";    //巡回形式
    $sql .= "    t_client.client_cd1 || '-' || t_client.client_cd2 ";    //請求先CD 
    $sql .= "FROM ";
    $sql .= "    t_aorder_h ";
    $sql .= "    INNER JOIN t_client ON t_client.client_id = t_aorder_h.claim_id ";
    $sql .= "WHERE ";
    $sql .= "    t_aorder_h.aord_id = ".$aord_id[$s].";";
    $result = Db_Query($db_con,$sql);
    $con_data_list[$s] = Get_Data($result);

    //--------------------------//
    //紹介口座取得
    //--------------------------//
    if($h_data_list[$s][0][26] != null){
        $sql = "SELECT client_div FROM t_client WHERE client_id = ".$h_data_list[$s][0][26].";";
        $result = Db_Query($db_con, $sql);
        //紹介者が仕入先の場合
        if(pg_fetch_result($result, 0, "client_div") == "2"){
            $h_data_list[$s][0][27] = $h_data_list[$s][0][27];
        //FCの場合、コード1にコード2をくっつける
        }else{
            $h_data_list[$s][0][27] = $h_data_list[$s][0][27]."-".$h_data_list[$s][0][28];
        }
    }


    /****************************/
    //受注データ抽出SQL
    /****************************/
    $data_sql  = "SELECT ";
    $data_sql .= "    CASE t_aorder_d.sale_div_cd ";     //販売区分
    $data_sql .= "         WHEN '01' THEN 'リピート'";
    $data_sql .= "         WHEN '02' THEN '商品'";
    $data_sql .= "         WHEN '03' THEN 'レンタル'";
    $data_sql .= "         WHEN '04' THEN 'リース'";
    $data_sql .= "         WHEN '05' THEN '工事'";
    $data_sql .= "         WHEN '06' THEN 'その他'";
    $data_sql .= "    END,";
    $data_sql .= "    CASE t_aorder_d.serv_print_flg ";  //サービス印字
    $data_sql .= "         WHEN 't' THEN '○'";
    $data_sql .= "         WHEN 'f' THEN '×'";
    $data_sql .= "    END,";
    $data_sql .= "    t_aorder_d.serv_cd,";              //サービスcd
    $data_sql .= "    t_aorder_d.serv_name,";            //サービス名
    $data_sql .= "    CASE t_aorder_d.goods_print_flg "; //アイテム印字
    $data_sql .= "         WHEN 't' THEN '○'";
    $data_sql .= "         WHEN 'f' THEN '×'";
    $data_sql .= "    END,";                        

    $data_sql .= "    t_aorder_d.goods_cd,";             //アイテムcd
    $data_sql .= "    t_aorder_d.goods_name,";           //アイテム名

    $data_sql .= "    t_aorder_d.set_flg,";              //一式フラグ
    $data_sql .= "    t_aorder_d.num,";                  //アイテム数

    $data_sql .= "    t_aorder_d.sale_price,";          //売上単価
    $data_sql .= "    t_aorder_d.sale_amount,";         //売上金額

    $data_sql .= "    t_aorder_d.egoods_cd,";            //消耗品cd
    $data_sql .= "    t_aorder_d.egoods_name,";          //消耗品名
    $data_sql .= "    t_aorder_d.egoods_num,";           //消耗品数

    $data_sql .= "    t_aorder_d.rgoods_cd,";            //本体cd
    $data_sql .= "    t_aorder_d.rgoods_name,";          //本体名
    $data_sql .= "    t_aorder_d.rgoods_num,";           //本体数

    $data_sql .= "    t_aorder_d.aord_d_id,";            //受注データID 17
    $data_sql .= "    t_aorder_d.aord_id, ";             //受注ID 18

    $data_sql .= "    t_aorder_d.contract_id, ";         //契約ID 19

    //FC側の代行伝票表示判定
    if($h_data_list[$s][0][19] != NULL && $group_kind == 3){
        $data_sql .= "    t_aorder_d.trust_cost_price, ";   //営業原価(受託先) 20
        $data_sql .= "    t_aorder_d.trust_cost_amount, ";  //営業金額(受託先) 21
    }else{
        $data_sql .= "    t_aorder_d.cost_price,";           //営業原価 20
        $data_sql .= "    t_aorder_d.cost_amount, ";         //営業金額 21
    }

    $data_sql .= "    t_aorder_d.official_goods_name, ";    //アイテム名（正式）22
    $data_sql .= "    t_aorder_d.account_price, ";          //紹介口座単価 23
    $data_sql .= "    t_aorder_d.account_rate, ";           //紹介口座率 24
    $data_sql .= "    t_aorder_d.advance_flg, ";            //前受相殺フラグ 25
    //aoyama-n 2009-09-18
    #$data_sql .= "    t_aorder_d.advance_offset_amount ";   //前受相殺額 26
    $data_sql .= "    t_aorder_d.advance_offset_amount, ";   //前受相殺額 26
    $data_sql .= "    t_goods.discount_flg ";                //値引フラグ 27

    $data_sql .= "FROM ";
    $data_sql .= "    t_aorder_d ";
    $data_sql .= "    INNER JOIN t_aorder_h ON t_aorder_d.aord_id = t_aorder_h.aord_id ";
    //aoyama-n 2009-09-18
    $data_sql .= "    LEFT JOIN t_goods ON t_aorder_d.goods_id = t_goods.goods_id ";

    $data_sql .= "WHERE ";
    $data_sql .= "    t_aorder_d.aord_id = ".$aord_id[$s];

    $data_sql .= " ORDER BY ";
    $data_sql .= "    t_aorder_d.line;";

    $result = Db_Query($db_con,$data_sql);
    $data_list[$s] = Get_Data($result);

    //スタッフIDが指定されている場合のみコース名表示
    //if($staff_id != NULL){
        /****************************/
        //コース名取得
        /****************************/
        //$course_data[$s] = Course_Id_Get($db_con,$data_list[$s][0][19],$staff_id,$client_id);
    //}
}

/****************************/
//ヘッダ・データ配列の表示形式変更
/****************************/
//受注IDシリアライズ化
$array_id = serialize($aord_id);
$array_id = urlencode($array_id);

for($i=0;$i<count($h_data_list);$i++){

    //順路指定判定
    if($h_data_list[$i][0][2] != NULL){
        //順路形式変更
        $h_data_list[$i][0][2] = str_pad($h_data_list[$i][0][2], 4, 0, STR_POS_LEFT); //順路
        $route1         = substr($h_data_list[$i][0][2],0,2);  
        $route2         = substr($h_data_list[$i][0][2],2,2);  
        $h_data_list[$i][0][2] = $route1."-".$route2;
    }else{
        $h_data_list[$i][0][2] = "　 　";
    }

/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/11/14　02-063　　　　kajioka-h 　伝票の変更ボタンの表示判定変更（保留削除されたら表示しないように）
 *
*/
/*
    //自社巡回・オフライン代行で確定していない、オンライン代行で受託先が報告していない受注のみ訂正に遷移可
    if(
        (
            ($h_data_list[$i][0][20] != "2" && $h_data_list[$i][0][18] == 'f') || ($h_data_list[$i][0][20] == "2" && $h_data_list[$i][0][21] == 'f')
        ) && $h_data_list[$i][0][22] != "t"
    ){
*/
    //直営は確定していない、
    //FCは自社巡回で確定していない、またはオンライン代行で報告していない、
    //かつ削除伝票じゃない受注のみ訂正に遷移可
    if(
        (
            ($group_kind == "2" && $h_data_list[$i][0][18] == "f") ||
            ($group_kind != "2" && 
                ($h_data_list[$i][0][20] != "2" && $h_data_list[$i][0][18] == "f") || ($h_data_list[$i][0][20] == "2" && $h_data_list[$i][0][21] == "f") 
            )
        ) && $h_data_list[$i][0][22] != "t" 
    ){
        //予定伝票訂正へ遷移ボタン
        if($h_data_list[$i][0][40] == "f"){
            $form->addElement("button","slip_change[$i]","伝票の変更","onClick=\"Submit_Page2('".FC_DIR."sale/2-2-107.php?aord_id=".$h_data_list[$i][0][16]."&back_display=$back_display&aord_id_array=$array_id');\"");
        }else{
            $form->addElement("button","slip_change[$i]","伝票の変更","onClick=\"Submit_Page2('".FC_DIR."sale/2-2-118.php?aord_id=".$h_data_list[$i][0][16]."&back_display=$back_display&aord_id_array=$array_id');\"");
        }
    }

    //代行判定
    if($h_data_list[$i][0][19] != NULL && $group_kind == 3){
        //代行依頼へ遷移ボタン
        $form->addElement("button","con_change[$i]","契約マスタの変更","onClick=\"location.href='".FC_DIR."system/2-1-239.php?aord_id=".$h_data_list[$i][0][16]."&aord_id_array=$array_id&get_flg=cal&back_display=$back_display'\"");
    }else{
        //契約マスタへ遷移ボタン
        $form->addElement("button","con_change[$i]","契約マスタの変更","onClick=\"location.href='".FC_DIR."system/2-1-115.php?aord_id=".$h_data_list[$i][0][16]."&aord_id_array=$array_id&client_id=".$h_data_list[$i][0][17]."&get_flg=cal&back_display=$back_display'\"");
    }

    //削除していない、かつ
    //・自社巡回・オフライン代行で確定していない、
    //・オンライン代行で受託先が報告していない
    //    商品予定出荷している場合は受託先のみ削除可能
    //場合、削除ボタン
    if($h_data_list[$i][0][22] != "t" &&
        (
            ($h_data_list[$i][0][20] != "2" && $h_data_list[$i][0][18] == "f") || 
            ($h_data_list[$i][0][20] == "2" && $h_data_list[$i][0][21] == "f" && 
                (($group_kind == "2" && $h_data_list[$i][0][23] == "f") || $group_kind != "2")
            )
        )
    ){
        //削除ボタン
        $form->addElement("button", "slip_del[$i]", "削　除", " onClick=\"Dialogue_1('削除します。', '$i,".$h_data_list[$i][0][16]."', 'hdn_slip_del');\" $disabled");

        //商品予定出荷済で、
        //・自社巡回、
        //・オンライン代行で受託先画面
        //の場合、在庫移動先倉庫指定セレクト
        if($h_data_list[$i][0][23] == "t" &&
            ($h_data_list[$i][0][20] == "1" || ($h_data_list[$i][0][20] == "2" && $group_kind != "2"))
        ){
            $ware_where  = " WHERE ";
            if($group_kind == "2"){
                $ware_where .= "    shop_id IN (".Rank_Sql().") ";
            }else{
                $ware_where .= "    shop_id = ".$_SESSION["client_id"]." ";
            }
            $ware_where .= " AND ";
            $ware_where .= "    staff_ware_flg = false ";
            $ware_where .= " AND ";
            $ware_where .= "    nondisp_flg = false ";

            $select_value = Select_Get($db_con, 'ware', $ware_where);
            $form->addElement("select", "back_ware[$i]", 'セレクトボックス', $select_value, "onkeydown=\"chgKeycode();\" $g_form_option_select");
        }
    }

    //伝票番号付番済で確定していなくて削除していなくて、自社巡回のみ、確定ボタン
    if($h_data_list[$i][0][0] != null && $h_data_list[$i][0][20] == "1" && $h_data_list[$i][0][18] == 'f' && $h_data_list[$i][0][22] != "t"){
        //確定ボタン
        $form->addElement("button", "confirm[$i]", "確　定", " onClick=\"Dialogue_1('確定します。', '".$h_data_list[$i][0][16]."', 'hdn_confirm');\" $disabled");
    }

    //伝票番号付番済で報告していなくて削除していなくて、オンライン代行の受託側のみ、報告ボタン
    if($h_data_list[$i][0][0] != null && $h_data_list[$i][0][20] != "1" && $h_data_list[$i][0][21] == 'f' && $h_data_list[$i][0][22] != "t" && $group_kind != "2"){
        //報告ボタン
        $form->addElement("button", "report[$i]", "報　告", " onClick=\"Dialogue_1('報告します。', '".$h_data_list[$i][0][16]."', 'hdn_report');\" $disabled");
    }

    //伝票番号付番済で確定していなくて削除していなくて委託側で、オンライン代行なら報告済でオフライン代行なら特にないのみ、承認ボタン
    if($h_data_list[$i][0][0] != null && $h_data_list[$i][0][18] == "f" && $h_data_list[$i][0][22] != "t" && $group_kind == "2" && (($h_data_list[$i][0][20] == "2" && $h_data_list[$i][0][21] == "t") || $h_data_list[$i][0][20] == "3")){
        //承認ボタン
        $form->addElement("button", "accept[$i]", "承　認", " onClick=\"Dialogue_1('承認します。', '".$h_data_list[$i][0][16]."', 'hdn_accept');\" $disabled");
    }


    //巡回担当・売上比率表示判定
    for($c=9;$c<=12;$c++){
        //数値判定
        if(!ereg("[0-9]",$h_data_list[$i][0][$c])){
            //値が入力されていない場合は、NULL
            $h_data_list[$i][0][$c] = NULL;
        }else{
            //値が入力されている場合は、メイン以外は改行を追加
            if($c!=9){
                $h_data_list[$i][0][$c] = "<br>".$h_data_list[$i][0][$c];
            }
        }
    }

    //伝票合計計算処理
    $h_data_list[$i][0][22] = $h_data_list[$i][0][13] + $h_data_list[$i][0][14];

    //金額形式変更
    //税抜合計
    $h_data_list[$i][0][13] = number_format($h_data_list[$i][0][13]);
    //消費税
    $h_data_list[$i][0][14] = number_format($h_data_list[$i][0][14]);
    //伝票合計
    $h_data_list[$i][0][22] = number_format($h_data_list[$i][0][22]);
    //紹介料
    $h_data_list[$i][0][31] = number_format($h_data_list[$i][0][31]);
    //前受金残高
    $h_data_list[$i][0][45] = number_format(Advance_Offset_Claim($db_con, $h_data_list[$i][0][1], $h_data_list[$i][0][17], $h_data_list[$i][0][45]));
    //前受相殺額合計
    $h_data_list[$i][0][46] = ($h_data_list[$i][0][46] != null) ? number_format($h_data_list[$i][0][46]) : null;

/*
    //備考
    $h_data_list[$i][0][24] = nl2br($h_data_list[$i][0][24]);
    //備考（受託先）
    $h_data_list[$i][0][25] = nl2br($h_data_list[$i][0][25]);
*/

    //委託料
    if($h_data_list[$i][0][32] == "1"){
        $h_data_list[$i][0][36] = "発生しない";
    }elseif($h_data_list[$i][0][32] == "2"){
        $h_data_list[$i][0][36] = number_format($h_data_list[$i][0][34])."（固定額）";
    }elseif($h_data_list[$i][0][32] == "3"){
        $h_data_list[$i][0][36] = number_format($h_data_list[$i][0][35])."（売上の".$h_data_list[$i][0][33]."％）";
    }
    //受託先
    $h_data_list[$i][0][35] = $h_data_list[$i][0][37]."-".$h_data_list[$i][0][38]."<br>".$h_data_list[$i][0][39];

    //請求先
    $h_data_list[$i][0][32] = $con_data_list[$i][0][0];
    //請求先CD
    $h_data_list[$i][0][33] = $con_data_list[$i][0][2];
    //巡回形式
    $h_data_list[$i][0][34] = $con_data_list[$i][0][1];
    //コース名
    //$h_data_list[$i][0][27] = $course_data[$i][1];

    //直送先名
    $h_data_list[$i][0][34] = $con_data_list[$i][0][1];
}
//print_array($course_data);
// 置き換え
for ($i=0; $i<count($data_list); $i++){
    for ($j=0; $j<count($data_list[$i]); $j++){
        $data_list[$i][$j][8] = my_number_format($data_list[$i][$j][8]);
        $data_list[$i][$j][9] = my_number_format($data_list[$i][$j][9],2);
        $data_list[$i][$j][10] = my_number_format($data_list[$i][$j][10]);
        $data_list[$i][$j][13] = my_number_format($data_list[$i][$j][13]);
        $data_list[$i][$j][16] = my_number_format($data_list[$i][$j][16]);

        $data_list[$i][$j][20] = my_number_format($data_list[$i][$j][20],2);
        $data_list[$i][$j][21] = my_number_format($data_list[$i][$j][21]);
        $data_list[$i][$j][23] = my_number_format($data_list[$i][$j][23]);
        $data_list[$i][$j][26] = my_number_format($data_list[$i][$j][26]);

/*
        //内訳指定判定
        $sql  = "SELECT aord_d_id FROM t_aorder_detail WHERE aord_d_id = ".$data_list[$i][$j][17].";";
        $result = Db_Query($db_con, $sql);
        $row_num = pg_num_rows($result);
        if(1 <= $row_num){
            //内訳リンク表示
            $data_list[$i][$j][22] = true;
        }else{
            //内訳リンク非表示
            $data_list[$i][$j][22] = false;
        }
*/
    }
}

// ヘッダ　行毎に色変え
for ($i=0; $i<count($h_data_list); $i++){
    $h_data_list[$i][0][23] = (bcmod($i, 2) == 0) ? "Result1" : "Result2";
    
    //代行判定
    if($h_data_list[$i][0][20] != '1'){
        //代行
        $h_data_list[$i][0][23] = "Result6";
        $color_flg = true; //代行の場合、データも色変更
    }

    // データ　行毎に色変え
    for ($j=0; $j<count($data_list[$i]); $j++){
        //色判定
        //使ってないので、t_aorder_d.contract_id（19）に詰めます
        if($color_flg == true){
            $data_list[$i][$j][19] = "Result6";
        }else{
            $data_list[$i][$j][19] = (bcmod($i, 2) == 0) ? "Result1" : "Result2";
        }
    }
    
    $color_flg = false;
}

//rev.1.3 変更前の予定巡回日をhiddenに持たせる
$form->setConstants(array("hdn_former_delivery_day" => $h_data_list[0][0][1]));


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
//$page_menu = Create_Menu_f('sale','1');

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
    //'page_menu'     => "$page_menu",
    'page_header'   => "$page_header",
    'html_footer'   => "$html_footer",
    'modoru_disp_flg'   => $modoru_disp_flg,
    'del_line'      => "$del_line",
    'move_warning'  => "$move_warning",

    //完了メッセージ
    'del_comp_mess' => "$del_comp_mess",
    'confirm_comp_mess'     => "$confirm_comp_mess",
    'repo_comp_mess'        => "$repo_comp_mess",
    'accept_comp_mess'      => "$accept_comp_mess",
    'lump_change_comp_mess' => "$lump_change_comp_mess",


    //確定エラーメッセージ
    'confirm_err'               => "$confirm_err",
    'del_err'                   => "$del_err",
    'deli_day_renew_err'        => "$deli_day_renew_err",
    'deli_day_start_err'        => "$deli_day_start_err",
    'claim_day_renew_err'       => "$claim_day_renew_err",
    'claim_day_start_err'       => "$claim_day_start_err",
    'claim_day_bill_err'        => "$claim_day_bill_err",
    "buy_err_mess1"             => "$buy_err_mess1",
    "buy_err_mess2"             => "$buy_err_mess2",
    "buy_err_mess3"             => "$buy_err_mess3",
    "error_pay_no"              => "$error_pay_no",
    "error_buy_no"              => "$error_buy_no",

    //確定エラーの伝票番号
    "ary_err_confirm"           => $ary_err_confirm,
    "ary_err_del"               => $ary_err_del,
    "ary_err_deli_day_renew"    => $ary_err_deli_day_renew,
    "ary_err_deli_day_start"    => $ary_err_deli_day_start,
    "ary_err_claim_day_renew"   => $ary_err_claim_day_renew,
    "ary_err_claim_day_start"   => $ary_err_claim_day_start,
    "ary_err_claim_day_bill"    => $ary_err_claim_day_bill,
    "ary_err_buy1"              => $ary_err_buy1,
    "ary_err_buy2"              => $ary_err_buy2,
    "ary_err_buy3"              => $ary_err_buy3,
    "ary_err_pay_no"            => $ary_err_pay_no,
    "ary_err_buy_no"            => $ary_err_buy_no,

    // 報告時エラーメッセージ
    "trust_confirm_err"         => "$trust_confirm_err",    // 既に巡回報告されていないかチェック
    "ord_time_itaku_err"        => "$ord_time_itaku_err",   // 巡回日チェック月次（委託先が得意先に対して月次やってたら売上確定できないので）
    "del_err"                   => "$del_err",              // 削除されているかチェック
    "ord_time_err"              => "$ord_time_err",         // 巡回日チェック月次（自分が委託先に対して月次やってたらダメ）
    "ord_time_start_err"        => "$ord_time_start_err",   // 巡回日チェックシステム開始日
    "claim_day_bill_err"        => "$claim_day_bill_err",   // 前回の請求締日以降かチェック
    "error_sale"                => "$error_sale",           // 番号が重複した場合
    "err_future_date_msg"       => "$err_future_date_msg",  // 予定巡回日が未来日付の場合のエラー
    // 報告時エラーの伝票番号
    "trust_confirm_no"          => $trust_confirm_no,
    "ord_time_itaku_no"         => $ord_time_itaku_no,
    "del_no"                    => $del_no,
    "ord_time_start_no"         => $ord_time_start_no,
    "ord_time_no"               => $ord_time_no,
    "claim_day_bill_no"         => $claim_day_bill_no,
    "err_sale_no"               => $err_sale_no,
    "ary_future_date_no"        => $ary_future_date_no,

    //承認エラーメッセージ
    'cancel_err'    => "$cancel_err",
    'error_buy'     => "$error_buy",
    'error_payin'   => "$error_payin",
    'deli_day_act_renew_err'    => "$deli_day_act_renew_err",
    'pay_day_act_err'           => "$pay_day_act_err",
    'deli_day_intro_renew_err'  => "$deli_day_intro_renew_err",
    'pay_day_intro_renew_err'   => "$pay_day_intro_renew_err",

    // 前受関連エラーメッセージ
    "err_trade_advance_msg"     => "$err_trade_advance_msg",
    "err_future_date_msg"       => "$err_future_date_msg",
    "err_advance_fix_msg"       => "$err_advance_fix_msg",
    "err_paucity_advance_msg"   => "$err_paucity_advance_msg",
    //"err_day_advance_msg"       => "$err_day_advance_msg",

    // 前受関連エラーのあった伝票番号配列
    "ary_err_trade_advance"     => $ary_err_trade_advance,
    "ary_err_future_date"       => $ary_err_future_date,
    "ary_err_advance_fix"       => $ary_err_advance_fix,
    "ary_err_paucity_advance"   => $ary_err_paucity_advance,
    "ary_trade_advance_no"      => $ary_trade_advance_no,
    "ary_future_date_no"        => $ary_future_date_no,
    "ary_advance_fix_no"        => $ary_advance_fix_no,
    "ary_paucity_advance_no"    => $ary_paucity_advance_no,

    // rev.1.3 巡回日一括訂正の警告メッセージ
    "warn_lump_change"          => $warn_lump_change,


//    'error_pay'     => "$error_pay",
//    'buy_err_mess'  => "$buy_err_mess",

));

//表示データ
$smarty->assign("data_list", $data_list);
$smarty->assign("h_data_list",$h_data_list);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

//print_array($_POST);


?>
