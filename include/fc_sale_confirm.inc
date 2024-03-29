<?php
/**
 *
 * 自社巡回の売上確定処理（エラーチェックからデータ登録まで）
 *
 * 呼出元
 * ・2-2-106（予定データ明細）
 * ・2-2-206（予定伝票売上確定）
 *
 */
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2007/04/09      要件1-4     kajioka-h   紹介料の仕様変更に対応
 *  2007/05/30      xx-xxx      kajioka-h   予定手書フラグを売上ヘッダに残す
 *  2007/06/01      xx-xxx      kajioka-h   直送先を残す
 *  2007-06-07      xx-xxx      fukuda      前受金関連のカラム追加により、受注伝票から売上伝票を作成するクエリにカラムを追加
 *  2007-06-08      xx-xxx      fukuda      前受相殺額がある伝票の確定時、取引区分「前受相殺」で入金を起こすよう修正
 *  2007-06-08      xx-xxx      fukuda      ↑用のエラーチェック追加
 *  2007-06-15      xx-xxx      fukuda      このファイル内の、エラーメッセージ出力フォーマットを統一
 *  2007/06/22      xx-xxx      kajioka-h   紹介口座の月次・仕入締チェック
 *  2007-07-12                  fukuda      「支払締」を「仕入締」に変更
 *
 */


/****************************/
// 予定出荷で在庫移動しているか確認
/****************************/
// 商品予定出荷で在庫移動しているかチェックのため受注IDを取得
if ($aord_array != null && $_POST["warn_confirm_flg"] != true){

    // 伝票番号をカンマ区切りに
    $aord_array_count = count($aord_array);
    for ($s = 0, $aord_array_str = null; $s < $aord_array_count; $s++){
        $aord_array_str .= $aord_array[$s].", ";
    }
    $aord_array_str = substr($aord_array_str, 0, (strlen($aord_array_str) - 2));

    // 未確定、保留削除されてない伝票で、商品予定出荷されてない伝票番号を取得
    $sql .= "SELECT \n";
    $sql .= "   DISTINCT ord_no \n";
    $sql .= "FROM \n";
    $sql .= "   t_aorder_h \n";
    $sql .= "   INNER JOIN t_aorder_d ON t_aorder_h.aord_id = t_aorder_d.aord_id \n";
    $sql .= "   INNER JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           aord_d_id, \n";
    $sql .= "           COUNT(aord_d_id) AS count \n";
    $sql .= "       FROM \n";
    $sql .= "           t_aorder_ship \n";
    $sql .= "       GROUP BY \n";
    $sql .= "           aord_d_id \n";
    $sql .= "   ) AS \n";
    $sql .= "   t_aorder_ship_count \n";
    $sql .= "   ON t_aorder_d.aord_d_id = t_aorder_ship_count.aord_d_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_aorder_h.aord_id IN (".$aord_array_str.") \n";
    $sql .= "AND \n";
    $sql .= "   t_aorder_h.move_flg = false \n";
    $sql .= "AND \n";
    $sql .= "   t_aorder_h.confirm_flg = false \n";
    $sql .= "AND \n";
    $sql .= "   t_aorder_h.del_flg = false \n";
    $sql .= "AND \n";
    $sql .= "   t_aorder_ship_count.count != 0 \n";
    $sql .= "GROUP BY \n";
    $sql .= "   t_aorder_h.ord_no \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $aord_array_count = pg_num_rows($res);

    if ($aord_array_count != 0){
        for ($s = 0, $move_warning = "伝票番号 "; $s < $aord_array_count; $s++){
            $move_warning .= pg_fetch_result($res, $s, 0)." ";
            $alert_flg = true;
        }
        $move_warning .= " の伝票は商品予定出荷で担当倉庫へ在庫移動されていません。<br>";

        // 無視用ボタン
        $form->addElement("button", "form_confirm_warn", "警告を無視して確定", 
            "onClick=\"javascript:Button_Submit('warn_confirm_flg', '".$_SERVER["REQUEST_URI"]."', true);\" $disabled"
        );
        $form->addElement("hidden", "warn_confirm_flg");
    }

}


/****************************/
// エラーチェック＋確定処理
/****************************/
// チェック判定
if (
    $aord_array != null &&
    ($_POST["warn_confirm_flg"] == true || (($_POST["confirm_flg"] == true || $_POST["hdn_confirm"] != null) && $move_warning == null))
){

    // 本部のclient_idを取得
    $sql  = "SELECT \n";
    $sql .= "   client_id \n";
    $sql .= "FROM \n";
    $sql .= "   t_client \n";
    $sql .= "WHERE \n";
    $sql .= "   rank_cd = (SELECT rank_cd FROM t_rank WHERE group_kind = '1') \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $head_id = pg_fetch_result($res, 0, "client_id");    // 本部のclient_id

    // トランザクション開始
    Db_Query($db_con, "BEGIN;");

    $error_pay_no = null;  // 重複エラーメッセージ
    $error_buy_no = null;  // 重複エラーメッセージ

    // チェックした受注ID分伝票表示
    for ($s = 0; $s < count($aord_array); $s++){

        /****************************/
        // 受注ヘッダーテーブル更新
        /****************************/
        $sql  = "SELECT \n";
        $sql .= "   confirm_flg, \n";       // 確定フラグ
        $sql .= "   del_flg, \n";           // 削除フラグ
        $sql .= "   ord_no, \n";            // 受注番号
        $sql .= "   intro_account_id, \n";  // 紹介者ID
        $sql .= "   shop_id \n";            // ショップID
        $sql .= "FROM \n";
        $sql .= "   t_aorder_h \n";
        $sql .= "WHERE \n";
        $sql .= "   aord_id = ".$aord_array[$s]." \n";
        $sql .= ";";

        $res  = Db_Query($db_con, $sql);

        if (pg_num_rows($res) != 0){

            // 取得した受注伝票情報を変数へ
            $confirm_flg        = (pg_fetch_result($res, 0, "confirm_flg") == "t") ? true : false;
            $del_flg            = (pg_fetch_result($res, 0, "del_flg"    ) == "t") ? true : false;
            $ord_no             =  pg_fetch_result($res, 0, "ord_no");
            $intro_account_id   =  pg_fetch_result($res, 0, "intro_account_id");
            $aord_shop_id       =  pg_fetch_result($res, 0, "shop_id");

            // 既に確定されていないかチェック
            if ($confirm_flg == true){
                $confirm_err       = "以下の伝票、は売上確定されているため確定できません。";
                $ary_err_confirm[] = $ord_no;
                continue;
            }

            // 削除されていないかチェック
            if ($del_flg == true){
                $del_err       = "以下の伝票は、削除されてるため確定できません。";
                $ary_err_del[] = $ord_no;
                continue;
            }


            //紹介口座がある場合、月次・仕入締日チェック
            if($intro_account_id != null){

                $sql  = "SELECT \n";
                $sql .= "   ord_time, \n";      // 配送日
                $sql .= "   arrival_day \n";    // 請求日
                $sql .= "FROM \n";
                $sql .= "   t_aorder_h \n";
                $sql .= "WHERE \n";
                $sql .= "   aord_id = ".$aord_array[$s]." \n";
                $sql .= ";";
                $res  = Db_Query($db_con, $sql);

                // 取得した配送日、請求日を年月日に分けて変数へ
                $day_array      = explode("-", pg_fetch_result($res, 0, "ord_time"));
                $delivery_day_y = $day_array[0];        // 配送日（年）
                $delivery_day_m = $day_array[1];        // 配送日（月）
                $delivery_day_d = $day_array[2];        // 配送日（日）
                $day_array      = explode("-", pg_fetch_result($res, 0, "arrival_day"));
                $request_day_y  = $day_array[0];        // 請求日（年）
                $request_day_m  = $day_array[1];        // 請求日（月）
                $request_day_d  = $day_array[2];        // 請求日（日）

                $sql  = "SELECT \n";
                $sql .= "   client_div \n";         // 取引先区分
                $sql .= "FROM \n";
                $sql .= "   t_client \n";
                $sql .= "WHERE \n";
                $sql .= "   client_id = $intro_account_id \n";
                $sql .= ";";
                $res  = Db_Query($db_con, $sql);

                // 直営で、紹介口座がFCの場合、本部の月次・仕入締チェック
                if ($_SESSION["group_kind"] == "2" && pg_fetch_result($res, 0, "client_div") == "3"){
                    // 紹介料仕入を起こす際、本部が代行者に月次更新をやってないかチェック
                    if (Check_Monthly_Renew($db_con, $intro_account_id, "2", $delivery_day_y, $delivery_day_m, $delivery_day_d, $head_id) == false){
                        $buy_err_mess1  = "以下の伝票は、配送日が前回の本部月次更新以前の日付のため確定できません。";
                        $ary_err_buy1[] = $ord_no;
                    }

                    // 紹介料仕入を起こす際、本部が代行者に仕入締をやってないかチェック
                    if (Check_Payment_Close_Day($db_con, $intro_account_id, $request_day_y, $request_day_m, $request_day_d, $head_id) == false){
                        $buy_err_mess2  = "以下の伝票は、請求日が本部の仕入締日以前の日付のため確定できません。";
                        $ary_err_buy2[] = $ord_no;
                    }

                //それ以外は、自分のとこの仕入締日チェック
                }else{
                    if (Check_Payment_Close_Day($db_con, $intro_account_id, $request_day_y, $request_day_m, $request_day_d, $aord_shop_id) == false){
                        $buy_err_mess3  = "以下の伝票は、請求日が仕入締日以前の日付のため確定できません。";
                        $ary_err_buy3[] = $ord_no;
                    }
                }
            }
        }

        // 受注ヘッダ更新処理
        $sql  = "UPDATE \n";
        $sql .= "   t_aorder_h \n";
        $sql .= "SET \n";
        $sql .= "   confirm_flg = 't', \n";     // 確定フラグ
        $sql .= "   ps_stat = '3' \n";          // 処理状況
        $sql .= "WHERE \n";
        $sql .= "   aord_id = ".$aord_array[$s]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if ($res === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        /****************************/
        // 受注ヘッダを元に売上ヘッダ作成
        /****************************/
        $sql  = "SELECT \n";
        $sql .= "   ord_no, \n";
        $sql .= "   ord_time, \n";
        $sql .= "   arrival_day, \n";
        $sql .= "   client_id, \n";
        $sql .= "   trade_id, \n";
        $sql .= "   ware_id, \n";
        $sql .= "   note, \n";
        $sql .= "   cost_amount, \n";
        $sql .= "   net_amount, \n";
        $sql .= "   tax_amount, \n";
        $sql .= "   intro_ac_name, \n";
        $sql .= "   slip_flg, \n";
        $sql .= "   shop_id,  \n";
        $sql .= "   intro_account_id, \n";
        $sql .= "   intro_amount, \n";
        $sql .= "   contract_div, \n";

        // 履歴用
        $sql .= "   client_cd1, \n";                    // 16 得意先コード１
        $sql .= "   client_cd2, \n";                    // 17 得意先コード２
        $sql .= "   client_cname, \n";                  // 18 略称
        $sql .= "   client_name, \n";                   // 19 得意先名
        $sql .= "   client_name2, \n";                  // 20 得意先名２
        $sql .= "   ware_name, \n";                     // 21 出荷倉庫名

        $sql .= "   reason_cor, \n";                    // 22
        $sql .= "   route, \n";                         // 23
        $sql .= "   intro_ac_price, \n";                // 24
        $sql .= "   intro_ac_rate, \n";                 // 25
        $sql .= "   claim_id, \n";                      // 26
        $sql .= "   claim_div, \n";                     // 27
        $sql .= "   round_form, \n";                    // 28
        $sql .= "   slip_out, \n";                      // 29
        $sql .= "   intro_ac_cd1, \n";                  // 30
        $sql .= "   intro_ac_cd2, \n";                  // 31
        $sql .= "   intro_ac_div, \n";                  // 32
        $sql .= "   hand_plan_flg, \n";                 // 33 予定手書フラグ
        $sql .= "   direct_id, \n";                     // 34
        $sql .= "   direct_name, \n";                   // 35
        $sql .= "   direct_name2, \n";                  // 36
        $sql .= "   direct_cname, \n";                  // 37
        $sql .= "   advance_offset_totalamount \n";     // 38 前受相殺合計（2007-06-07 fukuda）
        $sql .= "FROM \n";
        $sql .= "    t_aorder_h \n";
        $sql .= "WHERE \n";
        $sql .= "   aord_id = ".$aord_array[$s]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $data_list = Get_Data($res, 5);

        // 以下、kajio追記 //

        // 配送日を取得
        $array_date = explode("-", $data_list[0][1]);

        // 配送日が前回月次更新より先かチェック
        if(Check_Monthly_Renew($db_con, $data_list[0][3], "1", $array_date[0], $array_date[1], $array_date[2]) == false){
            $deli_day_renew_err       = "以下の伝票は、配送日が前回の月次更新以前のため確定できません。";
            $ary_err_deli_day_renew[] = $data_list[0][0];
        }

/*
        // 配送日がシステム開始日より先かチェック
        $err_mess = Sys_Start_Date_Chk($array_date[0], $array_date[1], $array_date[2], "配送日");
        if ($err_mess != null){
            $deli_day_start_err       = "".$err_mess;
            $ary_err_deli_day_start[] = $data_list[0][0];
        }
*/

        // 請求日を取得
        $array_date = explode("-", $data_list[0][2]);

        //請求日が前回月次更新より先かチェック
        if (Check_Monthly_Renew($db_con, $data_list[0][3], "1", $array_date[0], $array_date[1], $array_date[2]) == false){
            $claim_day_renew_err       = "以下の伝票は、請求日が前回の月次更新以前のため確定できません。";
            $ary_err_claim_day_renew[] = $data_list[0][0];
        }

        // 請求日が前回より先かチェック
        #2013-05-23 hashimoto-y
        #請求先区分(claim_div)の条件不足
        #if (Check_Bill_Close_Day($db_con, $data_list[0][3], $array_date[0], $array_date[1], $array_date[2]) == false){
        if (Check_Bill_Close_Day_Claim($db_con, $data_list[0][3], $data_list[0][27], $array_date[0], $array_date[1], $array_date[2]) == false){
            $claim_day_bill_err       = "以下の伝票は、請求日に請求書作成済の日付が入力されています。<br>";
            $claim_day_bill_err      .= "請求日を変更するか、請求書を削除して下さい。";
            $ary_err_claim_day_bill[] = $data_list[0][0];
        }

/*
        // 請求日がシステム開始日より先かチェック
        $err_mess = Sys_Start_Date_Chk($array_date[0], $array_date[1], $array_date[2], "請求日");
        if ($err_mess != null){
            $claim_day_start_err       = "以下伝票の".$err_mess;
            $ary_err_claim_day_start[] = $data_list[0][0];
        }
*/

        // 受注ヘッダを元に売上ヘッダ作成処理（受払登録前のエラーチェックで使用するため、エラーがあってもINSERTする）
        $sql  = "INSERT INTO \n";
        $sql .= "   t_sale_h \n";
        $sql .= "( \n";
        $sql .= "   sale_id, \n";           // 売上ID
        $sql .= "   sale_no, \n";           // 売上番号
        $sql .= "   aord_id, \n";           // 受注ID
        $sql .= "   sale_day, \n";          // 売上日
        $sql .= "   claim_day, \n";         // 請求日
        $sql .= "   client_id, \n";         // 得意先ID
        $sql .= "   trade_id, \n";          // 取引区分
        $sql .= "   ware_id, \n";           // 出荷倉庫
        $sql .= "   note, \n";              // 備考
        $sql .= "   cost_amount, \n";       // 原価金額
        $sql .= "   net_amount, \n";        // 売上金額
        $sql .= "   tax_amount, \n";        // 消費税額
        $sql .= "   intro_ac_name, \n";     // 紹介口座名
        $sql .= "   slip_flg, \n";          // 伝票出力フラグ
        $sql .= "   shop_id, \n";           // 取引先ID
        $sql .= "   intro_account_id, \n";  // 紹介口座ID
        $sql .= "   intro_amount, \n";      // 紹介口座料
        $sql .= "   c_shop_name, \n";       // ショップ名
        $sql .= "   c_shop_name2, \n";      // ショップ名２
        $sql .= "   client_cd1, \n";        // 得意先CD1
        $sql .= "   client_cd2, \n";        // 得意先CD2
        $sql .= "   client_cname, \n";      // 略称
        $sql .= "   client_name, \n";       // 得意先名1
        $sql .= "   client_name2, \n";      // 得意先名2
        $sql .= "   c_post_no1, \n";        // 郵便番号1
        $sql .= "   c_post_no2, \n";        // 郵便番号2
        $sql .= "   c_address1, \n";        // 住所１
        $sql .= "   c_address2, \n";        // 住所２
        $sql .= "   c_address3, \n";        // 住所３
        $sql .= "   ware_name, \n";         // 出荷倉庫名
        $sql .= "   reason_cor, \n";        // 訂正理由
        $sql .= "   route, \n";             // 順路
        $sql .= "   intro_ac_price, \n";    // 口座単価(得意先)
        $sql .= "   intro_ac_rate, \n";     // 口座率(得意先)
        $sql .= "   claim_id, \n";          // 請求先ID
        $sql .= "   claim_div, \n";         // 請求先区分
        $sql .= "   round_form, \n";        // 巡回形式
        $sql .= "   contract_div, \n";      // 契約区分
        $sql .= "   slip_out, \n";          // 伝票形式
        $sql .= "   e_staff_id, \n";        // オペレータID
        $sql .= "   e_staff_name, \n";      // オペレータ名
        $sql .= "   intro_ac_cd1, \n";      // 紹介口座先CD1
        $sql .= "   intro_ac_cd2, \n";      // 紹介口座先CD2
        $sql .= "   intro_ac_div, \n";      // 紹介口座料区分
        // 直送先がある場合
        if ($data_list[0][34] != null){
            $sql .= "   direct_id, \n";     // 直送先ID
            $sql .= "   direct_cd, \n";     // 直送先CD
            $sql .= "   direct_name, \n";   // 直送先名
            $sql .= "   direct_name2, \n";  // 直送先名2
            $sql .= "   direct_cname, \n";  // 直送先名（略称）
            $sql .= "   d_post_no1, \n";    // 直送先郵便番号1
            $sql .= "   d_post_no2, \n";    // 直送先郵便番号2
            $sql .= "   d_address1, \n";    // 直送先住所1
            $sql .= "   d_address2, \n";    // 直送先住所2
            $sql .= "   d_address3, \n";    // 直送先住所3
            $sql .= "   d_tel, \n";         // 直送先電話番号
            $sql .= "   d_fax, \n";         // 直送先FAX
        }
        $sql .= "   hand_plan_flg, \n";     // 予定手書フラグ
        $sql .= "   advance_offset_totalamount \n"; // 前受相殺額合計（2007-06-07 fukuda）
        $sql .= ") \n";
        $sql .= "VALUES \n";
        $sql .= "( \n";
        $sql .= "   ".$aord_array[$s].", \n";
        // 受注番号がある場合
        $sql .= ($data_list[0][0] != null) ? "    '".$data_list[0][0]."', \n" : "   NULL, \n";
        $sql .= "   ".$aord_array[$s].", \n";
        $sql .= "   '".$data_list[0][1]."', \n";
        $sql .= "   '".$data_list[0][2]."', \n";
        $sql .= "   ".$data_list[0][3].", \n";
        $sql .= "   '".$data_list[0][4]."', \n";
        $sql .= "   ".$data_list[0][5].", \n";
        $sql .= "   '".$data_list[0][6]."', \n";
        $sql .= "   ".$data_list[0][7].", \n";
        $sql .= "   ".$data_list[0][8].", \n";
        $sql .= "   ".$data_list[0][9].", \n";
        $sql .= "   '".$data_list[0][10]."', \n";
        $sql .= "   '".$data_list[0][11]."', \n";
        $sql .= "   ".$data_list[0][12].", \n";
        // 紹介口座先がある場合
        $sql .= ($data_list[0][13] != null) ? "   ".$data_list[0][13].", \n" : "   NULL, \n";
        // 紹介口座料がある場合
        $sql .= ($data_list[0][14] != null) ? "   ".$data_list[0][14].", \n" : "   NULL, \n";
        $ary = array("shop_name", "shop_name2");
        foreach ($ary as $val){
            $sql .= "   ( \n";
            $sql .= "       SELECT \n";
            $sql .= "           t_client.$val \n";
            $sql .= "       FROM \n";
            $sql .= "           t_aorder_h \n";
            $sql .= "           INNER JOIN t_client ON t_client.client_id = t_aorder_h.shop_id \n";
            $sql .= "       WHERE \n";
            $sql .= "           t_aorder_h.aord_id = ".$aord_array[$s]." \n";
            $sql .= "   ), \n";
        }
        $sql .= "   '".$data_list[0][16]."', \n";
        $sql .= "   '".$data_list[0][17]."', \n";
        $sql .= "   '".$data_list[0][18]."', \n";
        $sql .= "   '".$data_list[0][19]."', \n";
        $sql .= "   '".$data_list[0][20]."', \n";
        $ary  = array("post_no1", "post_no2", "address1", "address2", "address3");
        foreach ($ary as $val){
            $sql .= "   ( \n";
            $sql .= "       SELECT \n";
            $sql .= "           t_client.$val \n";
            $sql .= "       FROM \n";
            $sql .= "           t_aorder_h \n";
            $sql .= "           INNER JOIN t_client ON t_client.client_id = t_aorder_h.client_id \n";
            $sql .= "       WHERE \n";
            $sql .= "           t_aorder_h.aord_id = ".$aord_array[$s]." \n";
            $sql .= "   ), \n";
        }
        $sql .= "   '".$data_list[0][21]."', \n";
        $sql .= "   '".$data_list[0][22]."', \n";
        // 順路がある場合
        $sql .= ($data_list[0][23] != null) ? "   ".$data_list[0][23].", \n" : "   NULL, \n";
        // 口座単価(得意先)がある場合
        $sql .= ($data_list[0][24] != null) ? "   ".$data_list[0][24].", \n" : "   NULL, \n";
        $sql .= "   '".$data_list[0][25]."', \n";
        $sql .= "   ".$data_list[0][26].", \n";
        $sql .= "   '".$data_list[0][27]."', \n";
        $sql .= "   '".$data_list[0][28]."', \n";
        $sql .= "   '".$data_list[0][15]."', \n";
        $sql .= "   '".$data_list[0][29]."', \n";
        $sql .= "   ".$_SESSION["staff_id"].", \n";
        $sql .= "   '".addslashes($_SESSION["staff_name"])."', \n";
        $sql .= "   '".$data_list[0][30]."', \n";
        $sql .= "   '".$data_list[0][31]."', \n";
        $sql .= "   '".$data_list[0][32]."', \n";
        // 直送先がある場合
        if ($data_list[0][34] != null){
            $sql .= "   ".$data_list[0][34].",  \n";
            $sql .= "   (SELECT direct_cd FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
            $sql .= "   '".$data_list[0][35]."', \n";
            $sql .= "   '".$data_list[0][36]."', \n";
            $sql .= "   '".$data_list[0][37]."', \n";
            $sql .= "   (SELECT post_no1 FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
            $sql .= "   (SELECT post_no2 FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
            $sql .= "   (SELECT address1 FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
            $sql .= "   (SELECT address2 FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
            $sql .= "   (SELECT address3 FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
            $sql .= "   (SELECT tel      FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
            $sql .= "   (SELECT fax      FROM t_direct WHERE direct_id = ".$data_list[0][34]."), \n";
        }
        $sql .= "   '".$data_list[0][33]."', \n";
        // 前受相殺額がある場合
        $sql .= ($data_list[0][38] != null) ? "   ".$data_list[0][38]." \n" : "   NULL \n";
        $sql .= ") \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if ($res === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        /****************************/
        // 巡回担当者テーブル登録
        /****************************/
        $sql  = "SELECT \n";
        $sql .= "    staff_div, \n";
        $sql .= "    staff_id, \n";
        $sql .= "    sale_rate, \n";
        $sql .= "    staff_name, \n";
        $sql .= "    course_id \n";
        $sql .= "FROM \n";
        $sql .= "    t_aorder_staff \n";
        $sql .= "WHERE \n";
        $sql .= "    aord_id = ".$aord_array[$s]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $data_list3 = Get_Data($res, 5);

        for ($a = 0; $a < count($data_list3); $a++){
            $sql  = "INSERT INTO \n";
            $sql .= "   t_sale_staff \n";
            $sql .= "( \n";
            $sql .= "   sale_id, \n";           // 売上ID
            $sql .= "   staff_div, \n";         // 担当者識別
            $sql .= "   staff_id, \n";          // 担当者ID
            $sql .= "   sale_rate, \n";         // 売上率
            $sql .= "   staff_name, \n";        // 担当者名
            $sql .= "   course_id \n";          // コースID
            $sql .= ") \n";
            $sql .= "VALUES \n";
            $sql .= "( \n";
            $sql .= "   ".$aord_array[$s].", \n";
            $sql .= "   '".$data_list3[$a][0]."', \n";
            $sql .= "   ".$data_list3[$a][1].", \n";
            // 売上率がある場合
            $sql .= ($data_list3[$a][2] != null) ? "   ".$data_list3[$a][2].", \n" : "   NULL, \n";
            $sql .= "   '".$data_list3[$a][3]."', \n";
            // コースがある場合
            $sql .= ($data_list3[$a][4] != null) ? "   ".$data_list3[$a][4]." \n" : "   NULL \n";
            $sql .= ") \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if($res === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
        }

        /****************************/
        // 受注データを元に売上データ作成
        /****************************/
        $sql  = "SELECT \n";
        $sql .= "   t_aorder_d.aord_d_id, \n";              //  0 受注データID
        $sql .= "   t_aorder_d.line, \n";                   //  1 行
        $sql .= "   t_aorder_d.sale_div_cd, \n";            //  2 販売区分
        $sql .= "   t_aorder_d.serv_print_flg, \n";         //  3 サービス印字
        $sql .= "   t_aorder_d.serv_id, \n";                //  4 サービスID
        $sql .= "   t_aorder_d.set_flg, \n";                //  5 一式フラグ
        $sql .= "   t_aorder_d.goods_print_flg, \n";        //  6 アイテム印字
        $sql .= "   t_aorder_d.goods_id, \n";               //  7 アイテムID
        $sql .= "   t_aorder_d.goods_name, \n";             //  8 アイテム名
        $sql .= "   t_aorder_d.num, \n";                    //  9 アイテム数
        $sql .= "   t_aorder_d.tax_div, \n";                // 10 課税区分
        $sql .= "   t_aorder_d.buy_price, \n";              // 11 仕入単価
        $sql .= "   t_aorder_d.cost_price, \n";             // 12 営業単価
        $sql .= "   t_aorder_d.sale_price, \n";             // 13 売上単価
        $sql .= "   t_aorder_d.buy_amount, \n";             // 14 仕入金額
        $sql .= "   t_aorder_d.cost_amount, \n";            // 15 営業金額
        $sql .= "   t_aorder_d.sale_amount, \n";            // 16 売上金額
        $sql .= "   t_aorder_d.rgoods_id, \n";              // 17 本体ID
        $sql .= "   t_aorder_d.rgoods_name, \n";            // 18 本体名
        $sql .= "   t_aorder_d.rgoods_num, \n";             // 19 本体数
        $sql .= "   t_aorder_d.egoods_id, \n";              // 20 消耗品ID
        $sql .= "   t_aorder_d.egoods_name, \n";            // 21 消耗品名
        $sql .= "   t_aorder_d.egoods_num, \n";             // 22 消耗品数
        $sql .= "   t_aorder_d.contract_id, \n";            // 23 契約情報ID
        $sql .= "   t_aorder_d.account_price, \n";          // 24 口座単位
        $sql .= "   t_aorder_d.account_rate, \n";           // 25 口座率
        $sql .= "   t_aorder_d.serv_name, \n";              // 26 サービス名
        $sql .= "   t_aorder_d.serv_cd, \n";                // 27 サービスCD
        $sql .= "   t_aorder_d.goods_cd, \n";               // 28 アイテムCD
        $sql .= "   t_aorder_d.rgoods_cd, \n";              // 29 本体CD
        $sql .= "   t_aorder_d.egoods_cd, \n";              // 30 消耗品CD
        $sql .= "   t_goods.unit, \n";                      // 31 商品単位
        $sql .= "   t_aorder_d.g_product_name, \n";         // 32 商品分類名
        $sql .= "   t_aorder_d.official_goods_name, \n";    // 33 正式名称
        $sql .= "   t_aorder_d.advance_flg, \n";            // 34 前受相殺フラグ（2007-06-07 fukuda）
        $sql .= "   t_aorder_d.advance_offset_amount \n";   // 35 前受相殺額（2007-06-07 fukuda）
        $sql .= "FROM \n";
        $sql .= "   t_aorder_d \n";
        $sql .= "   LEFT JOIN t_goods ON t_goods.goods_id = t_aorder_d.goods_id \n";
        $sql .= "WHERE \n";
        $sql .= "   t_aorder_d.aord_id = ".$aord_array[$s]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $data_list2 = Get_Data($res, 5);

        for ($a = 0; $a < count($data_list2); $a++){

            // 受注データを元に売上データ登録実行
            $sql  = "INSERT INTO \n";
            $sql .= "   t_sale_d \n";
            $sql .= "( \n";
            $sql .= "   sale_d_id, \n";             // 売上データID
            $sql .= "   sale_id, \n";               // 売上ID
            $sql .= "   line, \n";                  // 行
            $sql .= "   sale_div_cd, \n";           // 販売区分CD
            $sql .= "   serv_print_flg, \n";        // サービス印字
            $sql .= "   serv_id, \n";               // サービスID
            $sql .= "   set_flg, \n";               // 一式フラグ
            $sql .= "   goods_print_flg, \n";       // アイテム印字
            $sql .= "   goods_id, \n";              // アイテムID
            $sql .= "   goods_name, \n";            // アイテム名
            $sql .= "   num, \n";                   // アイテム数
            $sql .= "   tax_div, \n";               // 課税区分
            $sql .= "   buy_price, \n";             // 仕入単価
            $sql .= "   cost_price, \n";            // 営業単価
            $sql .= "   sale_price, \n";            // 売上単価
            $sql .= "   buy_amount, \n";            // 仕入金額
            $sql .= "   cost_amount, \n";           // 営業金額
            $sql .= "   sale_amount, \n";           // 売上金額
            $sql .= "   rgoods_id, \n";             // 本体ID
            $sql .= "   rgoods_name, \n";           // 本体名
            $sql .= "   rgoods_num, \n";            // 本体数
            $sql .= "   egoods_id, \n";             // 消耗品ID
            $sql .= "   egoods_name, \n";           // 消耗品名
            $sql .= "   egoods_num, \n";            // 消耗品数
            $sql .= "   aord_d_id, \n";             // 受注データID
            $sql .= "   contract_id, \n";           // 契約情報ID
            $sql .= "   account_price, \n";         // 口座単価
            $sql .= "   account_rate, \n";          // 口座率
            $sql .= "   serv_name, \n";             // サービス名
            $sql .= "   serv_cd, \n";               // サービスCD
            $sql .= "   goods_cd, \n";              // アイテムCD
            $sql .= "   rgoods_cd, \n";             // 本体CD
            $sql .= "   egoods_cd, \n";             // 消耗品CD
            $sql .= "   unit, \n";                  // 商品単位
            $sql .= "   g_product_name, \n";        // 商品分類
            $sql .= "   official_goods_name, \n";   // アイテム名(正式名称)
            $sql .= "   advance_flg, \n";           // 前受相殺フラグ（2007-06-07 fukuda）
            $sql .= "   advance_offset_amount \n";  // 前受相殺額（2007-06-07 fukuda）
            $sql .= ") \n";
            $sql .= "VALUES \n";
            $sql .= "( \n";
            $sql .= "   ".$data_list2[$a][0].", \n";
            $sql .= "   ".$aord_array[$s].", \n";
            $sql .= "   '".$data_list2[$a][1]."', \n";
            $sql .= "   '".$data_list2[$a][2]."', \n";
            $sql .= "   '".$data_list2[$a][3]."', \n";
            // サービスID
            $sql .= ($data_list2[$a][4] != null) ? "   ".$data_list2[$a][4].", \n" : "   NULL, \n";
            $sql .= "   '".$data_list2[$a][5]."', \n";
            $sql .= "   '".$data_list2[$a][6]."', \n";
            // アイテム商品ID
            $sql .= ($data_list2[$a][7] != null) ? "   ".$data_list2[$a][7].", \n" : "   NULL, \n";
            $sql .= "   '".$data_list2[$a][8]."', \n";
            // アイテム数
            $sql .= ($data_list2[$a][9] != null) ? "   ".$data_list2[$a][9].", \n" : "   NULL, \n";
            $sql .= "   '".$data_list2[$a][10]."', \n";
            // 在庫単価指定判定
            $sql .= ($data_list2[$a][11] != null) ? "   ".$data_list2[$a][11].", \n" : "   NULL, \n";
            $sql .= "   ".$data_list2[$a][12].", \n";
            $sql .= "   ".$data_list2[$a][13].", \n";
            // 在庫金額指定判定
            $sql .= ($data_list2[$a][14] != null) ? "   ".$data_list2[$a][14].", \n" : "   NULL, \n";
            $sql .= "   ".$data_list2[$a][15].", \n";
            $sql .= "   ".$data_list2[$a][16].", \n";
            // 本体商品ID
            $sql .= ($data_list2[$a][17] != null) ? "   ".$data_list2[$a][17].", \n" : "   NULL, \n";
            $sql .= "   '".$data_list2[$a][18]."', \n";
            // 本体数
            $sql .= ($data_list2[$a][19] != null) ? "   ".$data_list2[$a][19].", \n" : "   NULL, \n";
            // 消耗品ID
            $sql .= ($data_list2[$a][20] != null) ? "   ".$data_list2[$a][20].", \n" : "   NULL, \n";
            $sql .= "   '".$data_list2[$a][21]."', \n";
            // 消耗品数
            $sql .= ($data_list2[$a][22] != null) ? "   ".$data_list2[$a][22].", \n" : "   NULL, \n";
            $sql .= "   ".$data_list2[$a][0].", \n";
            // 契約情報ID
            $sql .= ($data_list2[$a][23] != null) ? "   ".$data_list2[$a][23].", \n" : "   NULL, \n";
            // 口座単価 
            $sql .= ($data_list2[$a][24] != null) ? "   ".$data_list2[$a][24].", \n" : "   NULL, \n";
            $sql .= "   '".$data_list2[$a][25]."', \n";
            $sql .= "   '".$data_list2[$a][26]."', \n";
            $sql .= "   '".$data_list2[$a][27]."', \n";
            $sql .= "   '".$data_list2[$a][28]."', \n";
            $sql .= "   '".$data_list2[$a][29]."', \n";
            $sql .= "   '".$data_list2[$a][30]."', \n";
            $sql .= "   '".$data_list2[$a][31]."', \n";
            $sql .= "   '".$data_list2[$a][32]."', \n";
            $sql .= "   '".$data_list2[$a][33]."', \n";
            $sql .= "   '".$data_list2[$a][34]."', \n";
            $sql .= ($data_list2[$a][35] != null) ? "   ".$data_list2[$a][35]." \n" : "   NULL \n";
            $sql .= ") \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if ($res === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            /****************************/
            // 受注の出庫品テーブルを基に、売上の出庫品テーブル作成
            /****************************/
            $sql  = "SELECT \n";
            $sql .= "   aord_d_id, \n";
            $sql .= "   goods_id, \n";
            $sql .= "   goods_name, \n";
            $sql .= "   num, \n";
            $sql .= "   goods_cd \n";
            $sql .= "FROM \n";
            $sql .= "   t_aorder_ship \n";
            $sql .= "WHERE \n";
            $sql .= "   aord_d_id = ".$data_list2[$a][0]." \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            $data_list5 = Get_Data($res, 5);

            for ($c = 0; $c < count($data_list5); $c++){
                $sql  = "INSERT INTO \n";
                $sql .= "   t_sale_ship \n";
                $sql .= "( \n";
                $sql .= "   sale_d_id, \n";
                $sql .= "   goods_id, \n";
                $sql .= "   goods_name, \n";
                $sql .= "   num, \n";
                $sql .= "   goods_cd \n";
                $sql .= ") \n";
                $sql .= "VALUES \n";
                $sql .= "( \n";
                $sql .= "   ".$data_list5[$c][0].", \n";
                $sql .= "   ".$data_list5[$c][1].", \n";
                $sql .= "   '".$data_list5[$c][2]."', \n";
                $sql .= "   ".$data_list5[$c][3].", \n";
                $sql .= "   '".$data_list5[$c][4]."' \n";
                $sql .= ") \n";
                $sql .= ";";
                $res  = Db_Query($db_con, $sql);
                if ($res === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }
            }
        }

        // 未来の日付が入力されていないかチェック
        if ($data_list[0][1] > date("Y-m-d")){
            $err_future_date_msg    = "予定巡回日が未来の日付になっているため以下の伝票を確定できません。";
            $ary_err_future_date[]  = $data_list[0][0];
        }

        /****************************/
        // 前受相殺関連処理（2007-06-08 fukuda）
        /****************************/
        // 受注ヘッダの前受相殺額合計がある場合
        if ($data_list[0][38] != null){

            // 受注ヘッダの取得データを、わかりやすいように変数にセット
            $ord_h_ord_no       = $data_list[0][0];
            $ord_h_ord_time     = $data_list[0][1];
            $ord_h_client_id    = $data_list[0][3];
            $ord_h_trade_id     = $data_list[0][4];
            $ord_h_claim_div    = $data_list[0][27];
            $ord_h_adv_amount   = $data_list[0][38];

            // 請求先に対する前受金残高取得（引数：ord_time, client_id, claim_div）
            $advance_balance = Advance_Offset_Claim($db_con, $ord_h_ord_time, $ord_h_client_id, $ord_h_claim_div);

            // 現金の場合
            if ($ord_h_trade_id == 61 || $ord_h_trade_id == 63 || $ord_h_trade_id == 64){
                $err_trade_advance_msg  = "以下の伝票の取引区分が現金売上になっています。<br>";
                $err_trade_advance_msg .= "前受相殺額のある伝票は取引区分を掛売上に変更してください。";
                $ary_err_trade_advance[]= $ord_h_ord_no;
            }

            // 確定されていない伝票がないかチェック（引数：ord_time, client_id, claim_div）
            if (Chk_Advance_Fix($db_con, $ord_h_ord_time, $ord_h_client_id, $ord_h_claim_div) === false){
                $err_advance_fix_msg  = "予定巡回日以前に未確定の前受金伝票が存在するため以下の伝票を確定できません。<br>";
                $err_advance_fix_msg .= "前受金確定を行ってください。";
                $ary_err_advance_fix[]= $ord_h_ord_no;
            }

            // 前受相殺額合計 > 前受金残高 かつこれまでに同じ得意先、同じ請求先区分にこのエラーがない場合
            if ($ord_h_adv_amount > $advance_balance && $ary_err_client_claim[$ord_h_client_id] != $ord_h_claim_div){
                $err_paucity_advance_msg  = "確定済の前受金残高が不足しているため以下の伝票を確定できません。";
                $ary_err_paucity_advance[]= $ord_h_ord_no;
                // エラーのあった得意先、請求先はこのエラーチェックをもう行わないようにする判断のため
                // 得意先と請求先のペアを変数にセットしておく（array[得意先ID] => 請求先区分）
                $ary_err_client_claim[$ord_h_client_id] = $ord_h_claim_div;
            }

        }

        /****************************/
        // 受払テーブル作成処理
        /****************************/
        $res = FC_Trade_Query($db_con,$aord_array[$s]);

        if ($res === false){

            // 同時実行制御処理
            $err_message = pg_last_error();
            $err_format  = "t_payin_h_pay_no_key";
            $err_format2 = "t_payin_no_serial_pkey";
            Db_Query($db_con, "ROLLBACK;");

            // 入金番号が重複した場合(直営処理・FC処理)
            if ((strstr($err_message, $err_format) != false) || (strstr($err_message, $err_format2) != false)){
                $error_pay_no     = "以下の伝票は、入金番号が重複しました。もう一度確定を行ってください。";
                $ary_err_pay_no[] = $data_list[0][0];
                $err_data["confirm_flg"] = false;
                $form->setConstants($err_data);
            }else{
                exit;
            }

        }

        /****************************/
        // 売上IDから紹介料を計算し、仕入テーブルへ登録
        /****************************/
        // 紹介料指定判定
        $sql  = "SELECT \n";
        $sql .= "   intro_account_id, \n";
        $sql .= "   intro_amount, \n";
        $sql .= "   intro_ac_div \n";
        $sql .= "FROM \n";
        $sql .= "   t_sale_h \n";
        $sql .= "WHERE \n";
        $sql .= "   sale_id = ".$aord_array[$s]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);

        $intro_account_id = pg_fetch_result($res, 0, "intro_account_id");   // 紹介者ID
        $intro_amount     = pg_fetch_result($res, 0, "intro_amount");       // 紹介料
        $intro_ac_div     = pg_fetch_result($res, 0, "intro_ac_div");       // 紹介料区分

        // 紹介料区分が発生しないじゃない、紹介料が0より大きい場合、紹介料仕入を登録
        if($intro_account_id != null && $intro_amount > 0 && $intro_ac_div != "1"){

            $res = FC_Intro_Buy_Query($db_con, $aord_array[$s], $data_list[0][13]);

            if ($res === false){
                // 同時実行制御処理
                $err_message = pg_last_error();
                $err_format = "t_buy_h_buy_no_key";
                Db_Query($db_con, "ROLLBACK;");
                // 仕入番号が重複した場合
                if(strstr($err_message, $err_format) != false){
                    $error_buy_no     = "仕入番号が重複しました。もう一度確定を行ってください。";
                    $ary_err_buy_no[] = $data_list[0][0];
                    $err_data["confirm_flg"] = false;
                    $form->setConstants($err_data);
                    break;
                }else{
                    exit;
                }

            }

        }

    }   // チェックの付いている受注伝票ループ終了

    /****************************/
    // COMMITかROLLBACKか
    /****************************/
    // エラーが全く無かった場合はCOMMIT
    if (
        $confirm_err            == null &&
        $del_err                == null &&
        $deli_day_renew_err     == null &&
        $deli_day_start_err     == null &&
        $claim_day_renew_err    == null &&
        $claim_day_bill_err     == null &&
        $claim_day_start_err    == null &&
        $buy_err_mess1          == null &&
        $buy_err_mess2          == null &&
        $buy_err_mess3          == null &&
        $err_trade_advance_msg  == null &&
        $err_future_date_msg    == null &&
        $err_advance_fix_msg    == null &&
        $err_paucity_advance_msg== null &&
        $error_pay_no           == null &&
        $error_buy_no           == null
    ){

        Db_Query($db_con, "COMMIT;");

        // 完了メッセージ
        $confirm_comp_mess = "確定しました。";

        // 確定処理画面時は自画面に遷移する
        if (strstr($_SERVER["PHP_SELF"], "2-2-206") != false){
            header("Location: ".$_SERVER["REQUEST_URI"]);
            exit;
        }

    }else{

        Db_Query($db_con, "ROLLBACK;");

    }

}else
//チェックが無い場合はエラー
if ($aord_array == NULL){

    $error_msg3 = "確定する伝票が一つも選択されていません。";
    $error_flg = true;

}

?>
