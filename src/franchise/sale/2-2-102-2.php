<?php

/******************************
 *  変更履歴
 *      ・（2006-××-××）表示ボタン押下時にクエリー実行<suzuki>
 *      ・（2006-10-26）売上率が0%の担当者も表示<suzuki>
 *                      得意先・ショップの取引状態に関係なく表示<suzuki>
 *      ・（2006-10-31）検索時にPOST情報初期化<suzuki>
 *      ・（2006-11-01）同じオンライン伝票が２件表示されるのを修正<suzuki>
 *      ・              日付リンク押下時にTOPに遷移しないように修正<suzuki>
 *      ・              コースボタン押下時にSQLエラーにならないように修正<suzuki>
 *
******************************/
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/10      02-061      suzuki      予定日にカレンダー表示期間判定を行うように修正
 *  2007/02/09      要望14-1    kajioka-h   表示を得意先でまとめて表示（リンク、件数）
 *                  要望14-2    kajioka-h   日曜日始まりの表示に変更
 *                  B0702-005   kajioka-h   日付リンクに余計な受注が入っていたのを修正
 *                  B0702-006   kajioka-h   直営に受託先の代行料売上が表示されていたのを修正
 *                  B0702-007   kajioka-h   代行伝票の日付リンクに重複して受注を表示していたのを修正
 *  2007/02/22      xx-xxx      kajioka-h   帳票出力機能を削除
 *  2007/02/26      B0702-010   kajioka-h   表示ボタン押下時にJSエラーが出ていたのを修正
 *  2007/03/14      xx-xxx      kajioka-h   保留削除された伝票は表示しないように変更
 *  2007/03/27                  watanabe-k  巡回担当者のリストをスタッフマスタをもとに作成するように修正
 *  2007-04-09                  fukuda      検索条件復元処理
 *  2007/04/17      B0702-045   kajioka-h   他の画面から遷移してきた場合に、日付の検索項目が復元されないバグ修正
 *                  B0702-046   kajioka-h   他の画面から遷移してきた場合に、先週ボタンが機能しないバグ修正
 *  2007/05/08      その他149上 kajioka-h   過去1年分見れるように変更
 *                  xx-xxx      kajioka-h   コメントアウトされてた箇所（コース、前日・翌日ボタンあたり）をざっくり削除
 *  2010/05/13      Rev.1.5     hashimoto-y 売上率０％の場合、全額予定額に集計される不具合の修正
 *
 */

$page_title = "巡回予定カレンダー(週)";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth   = Auth_Check($db_con);

//過去何ヶ月分表示するの？
$back_cal_month = 12;


/****************************/
// 検索条件復元関連
/****************************/
// 検索フォーム初期値配列
$ary_form_list = array(
    "form_part_1"   => "",
    "form_staff_1"  => "",
    "form_fc"       => "",
);

// 検索条件復元
Restore_Filter2($form, "contract", "indicate_button", $ary_form_list);

// モジュール番号取得
$module_no = Get_Mod_No();

// 再遷移時は表示月を復元
if ($_GET["search"] == "1"){
    $_POST["next_w_button_flg"] = $_SESSION[$module_no]["all"]["next_w_button_flg"];
    $_POST["back_w_button_flg"] = $_SESSION[$module_no]["all"]["back_w_button_flg"];
    $_POST["form_sale_day"]["y"]= $_SESSION[$module_no]["all"]["form_sale_day"]["y"];
    $_POST["form_sale_day"]["m"]= $_SESSION[$module_no]["all"]["form_sale_day"]["m"];
    $_POST["form_sale_day"]["d"]= $_SESSION[$module_no]["all"]["form_sale_day"]["d"];
}


/****************************/
//契約関数定義
/****************************/
require_once(INCLUDE_DIR."function_keiyaku.inc");

/****************************/
//外部変数
/****************************/
$client_id = $_SESSION["client_id"];

/****************************/
//得意先情報取得
/****************************/
$sql  = "SELECT ";
$sql .= "    cal_peri ";    //カレンダー表示期間
$sql .= "FROM ";
$sql .= "    t_client ";
$sql .= "WHERE ";
$sql .= "    client_id = $client_id;";
$result = Db_Query($db_con, $sql);
$num = pg_num_rows($result);
//該当データがある
if($num == 1){
    $cal_peri      = pg_fetch_result($result, 0,0);
}

//巡回基準日
$sql  = "SELECT stand_day FROM t_stand;";
$result = Db_Query($db_con, $sql); 
$stand_day = pg_fetch_result($result,0,0);   
$day_by = substr($stand_day,0,4);
$day_bm = substr($stand_day,5,2);
$day_bd = substr($stand_day,8,2);


/****************************/
//休日データ取得
/****************************/
$sql  = "SELECT ";
$sql .= "    holiday ";     //休日
$sql .= "FROM ";
$sql .= "    t_holiday ";
$sql .= "WHERE ";
$sql .= "    shop_id = $client_id;";
$result = Db_Query($db_con, $sql);
$h_data = Get_Data($result);
for($h=0;$h<count($h_data);$h++){
    //休日データを連想配列によって定義
    $holiday[$h_data[$h][0]] = "1";
}

/****************************/
//日付データ取得
/****************************/
//本日の日付取得
$year  = date("Y");
$month = date("m");
$day   = date("d");

//指定日の週の日曜日を求める
$date_tmp = date("Y-m-d", mktime(0, 0, 0, $month, $day - date("w", mktime(0, 0, 0, $month, $day, $year)), $year));
$date_array_tmp = explode("-", $date_tmp);
$year  = $date_array_tmp[0];
$month = $date_array_tmp[1];
$day   = $date_array_tmp[2];

//表示期間の最初の月取得
$str = mktime(0, 0, 0, date("n") - $back_cal_month, 1, date("Y"));
$b_year  = date("Y",$str);
$b_month = date("m",$str);

//カレンダー表示期間の最後の月取得
$str = mktime(0, 0, 0, date("n")+$cal_peri,1,date("Y"));
$c_year  = date("Y",$str);
$c_month = date("m",$str);

//カレンダー表示期間
$cal_range = $b_year."年 ".$b_month."月 〜 ".$c_year."年 ".$c_month."月";

//一週間後の日付取得
$next = mktime(0, 0, 0, $month,$day+6,$year);
$nyear  = date("Y",$next);
$nmonth = date("m",$next);
$nday   = date("d",$next);

/****************************/
//POST情報取得
/****************************/
if ($_POST != null){
    $post_part_id   = $_POST["form_part_1"];    //部署
    $post_staff_id  = $_POST["form_staff_1"];   //担当者
    $post_fc_id     = $_POST["form_fc"];        //委託先
}

//予定日が指定されているか
// 予定データの戻るボタンが押されていない＆（予定日のPOSTがある場合）または、
// 予定データの戻るボタンが押されている　＆（予定日のSESSIONがある場合）
if(
    ($_POST["form_sale_day"]["y"] != NULL || $_POST["form_sale_day"]["m"] != NULL || $_POST["form_sale_day"]["d"] != NULL)
){

        $year  = str_pad($_POST["form_sale_day"]["y"],4, 0, STR_PAD_LEFT);
        $month = str_pad($_POST["form_sale_day"]["m"],2, 0, STR_PAD_LEFT);
        $day   = str_pad($_POST["form_sale_day"]["d"],2, 0, STR_PAD_LEFT);

    //指定日の週の日曜日を求める
    $date_tmp = date("Y-m-d", mktime(0, 0, 0, $month, $day - date("w", mktime(0, 0, 0, $month, $day, $year)), $year));
    $date_array_tmp = explode("-", $date_tmp);
    $year  = $date_array_tmp[0];
    $month = $date_array_tmp[1];
    $day   = $date_array_tmp[2];

    //先週判定
    if($_POST["back_w_button_flg"] == true){
        $snext = mktime(0, 0, 0, $month,$day-7,$year);
    //翌週判定
    }else if($_POST["next_w_button_flg"] == true){
        $snext = mktime(0, 0, 0, $month,$day+7,$year);
    }else{
        //表示ボタン押下時
        $snext = mktime(0, 0, 0, $month,$day,$year);
    }
    $snyear  = date("Y",$snext);
    $snmonth = date("m",$snext);
    $snday   = date("d",$snext);

    //カレンダー表示期間分しか表示させない為判定
    $max_day = date("t",mktime(0, 0, 0, date("m")+$cal_peri,1,date("Y")));
    $fast_day = date("Y-m-d", mktime(0, 0, 0, date("m")+$cal_peri,$max_day,date("Y")));

    //翌月の最初の週か
    $check_day = date("Y-m-d", mktime(0, 0, 0, $snmonth,$snday+7,$snyear));
    if($check_day > $fast_day){
        //翌週ボタンを非表示にする
        $nw_disabled_flg = true;

        //月末までの日付取得
        $nyear = substr($fast_day,0,4);
        $nmonth = substr($fast_day,5,2);
        $nday = substr($fast_day,8,2);
    }else{
        //一週間後の日付取得
        $next = mktime(0, 0, 0, $snmonth,$snday+6,$snyear);
        $nyear  = date("Y",$next);
        $nmonth = date("m",$next);
        $nday   = date("d",$next);
    }


    //先月の1日の日曜日を求める（カレンダーの最初の日）
    //$last_day = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1 - date("w", mktime(0, 0, 0, date("m")-1, 1, date("Y"))), date("Y")));
    //表示期間の最初の月の1日がある週の日曜日を求める（カレンダーの最初の日）
    $last_day = date("Y-m-d", mktime(0, 0, 0, date("m") - $back_cal_month, 1 - date("w", mktime(0, 0, 0, date("m") - $back_cal_month, 1, date("Y"))), date("Y")));
    
    //前月の最初の週か
    $check_day = date("Y-m-d", mktime(0, 0, 0, $snmonth,$snday,$snyear));

    //if($check_day < $last_day){
    if($check_day <= $last_day){
        //先週ボタンを非表示にする
        $bw_disabled_flg = true;
    }


    /****************************/
    //エラーチェック(PHP)
    /****************************/
    $error_flg = false;            //エラー判定フラグ

    //数値判定
    if(!ereg("^[0-9]{4}$",$year) || !ereg("^[0-9]{2}$",$month) || !ereg("^[0-9]{2}$",$day)){
        $error_msg = "予定巡回日 の日付は妥当ではありません。";
        $error_flg = true;        //エラー表示
    }

    //・文字種チェック
    $year  = (int)$year;
    $month = (int)$month;
    $day   = (int)$day;
    if(!checkdate($month,$day,$year)){
        $error_msg = "予定巡回日 の日付は妥当ではありません。";
        $error_flg = true;        //エラー表示
    }

    $ord_date = str_pad($snyear,4,"0", STR_PAD_LEFT)."-".str_pad($snmonth,2,"0", STR_PAD_LEFT)."-".str_pad($snday,2,"0", STR_PAD_LEFT);
    /****************************/
    //カレンダ表示期間取得
    /****************************/
    $cal_array = Cal_range($db_con,$client_id,true);
    $check_edate   = $cal_array[1];     //対象終了期間

    //カレンダー表示期間判定
    if($last_day > $ord_date || $ord_date > $check_edate){
        $error_msg2 = "予定巡回日 はカレンダー表示期間内を指定して下さい。";
        $error_flg = true;        //エラー表示
    }

}

/****************************/
//前日・先週ボタン押下処理
/****************************/
if($_POST["back_w_button_flg"] == true){

    //先週判定
    if($_POST["back_w_button_flg"] == true){
        //先週の日付取得
        $str = mktime(0, 0, 0, $month,$day-7,$year);
    }else{
        //前日の日付取得
        $str = mktime(0, 0, 0, $month,$day-1,$year);
    }

    $year  = date("Y",$str);
    $month = date("m",$str);
    $day   = date("d",$str);

    //カレンダー表示期間分しか表示させない為判定
    $max_day = date("t",mktime(0, 0, 0, date("m")+$cal_peri,1,date("Y")));
    $fast_day = date("Y-m-d", mktime(0, 0, 0, date("m")+$cal_peri,$max_day,date("Y")));

    //翌月の最初の週か
    $check_day = date("Y-m-d", mktime(0, 0, 0, $month,$day+7,$year));
    if($check_day > $fast_day){
        //翌週ボタンを非表示にする
        $nw_disabled_flg = true;

        //月末までの日付取得
        $nyear = substr($fast_day,0,4);
        $nmonth = substr($fast_day,5,2);
        $nday = substr($fast_day,8,2);
    }else{
        //一週間後の日付取得
        $next = mktime(0, 0, 0, $month,$day+6,$year);
        $nyear  = date("Y",$next);
        $nmonth = date("m",$next);
        $nday   = date("d",$next);
    }

    //先月の1日の日曜日を求める（カレンダーの最初の日）
    //$last_day = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1 - date("w", mktime(0, 0, 0, date("m")-1, 1, date("Y"))), date("Y")));
    //表示期間の最初の月の1日がある週の日曜日を求める（カレンダーの最初の日）
    $last_day = date("Y-m-d", mktime(0, 0, 0, date("m") - $back_cal_month, 1 - date("w", mktime(0, 0, 0, date("m") - $back_cal_month, 1, date("Y"))), date("Y")));
    
    //前月の最初の週か
    $check_day = date("Y-m-d", mktime(0, 0, 0, $month,$day-7,$year));

    if($check_day < $last_day){
        //先週ボタンを非表示にする
        $bw_disabled_flg = true;
    }


    //前日・先週を考慮した日を予定日に設定
    $back_data["form_sale_day"]["y"] = $year;
    $back_data["form_sale_day"]["m"] = $month;
    $back_data["form_sale_day"]["d"] = $day;

    //前日・先週を考慮した日をセッションに詰め直す
    $_SESSION[$module_no]["all"]["form_sale_day"]["y"] = $year;
    $_SESSION[$module_no]["all"]["form_sale_day"]["m"] = $month;
    $_SESSION[$module_no]["all"]["form_sale_day"]["d"] = $day;

    //フラグをクリア
    $back_data["back_w_button_flg"] = "";
    $form->setConstants($back_data);
}

/****************************/
//翌日・翌週ボタン押下処理
/****************************/
if($_POST["next_w_button_flg"] == true){

    //翌週判定
    if($_POST["next_w_button_flg"] == true){
        //翌週の日付取得
        $str = mktime(0, 0, 0, $month,$day+7,$year);
    }else{
        //翌日の日付取得
        $str = mktime(0, 0, 0, $month,$day+1,$year);
    }

    $year  = date("Y",$str);
    $month = date("m",$str);
    $day   = date("d",$str);

    //カレンダー表示期間分しか表示させない為判定
    $max_day = date("t",mktime(0, 0, 0, date("m")+$cal_peri,1,date("Y")));
    $fast_day = date("Y-m-d", mktime(0, 0, 0, date("m")+$cal_peri,$max_day,date("Y")));

    //翌月の最初の週か
    $check_day = date("Y-m-d", mktime(0, 0, 0, $month,$day+7,$year));
    if($check_day > $fast_day){
        //翌週ボタンを非表示にする
        $nw_disabled_flg = true;

        //月末までの日付取得
        $nyear = substr($fast_day,0,4);
        $nmonth = substr($fast_day,5,2);
        $nday = substr($fast_day,8,2);
    }else{
        //一週間後の日付取得
        $next = mktime(0, 0, 0, $month,$day+6,$year);
        $nyear  = date("Y",$next);
        $nmonth = date("m",$next);
        $nday   = date("d",$next);
    }


    //翌日・翌週を考慮した日を予定日に設定
    $next_data["form_sale_day"]["y"] = $year;
    $next_data["form_sale_day"]["m"] = $month;
    $next_data["form_sale_day"]["d"] = $day;

    //翌日・翌週を考慮した日をセッションに詰め直す
    $_SESSION[$module_no]["all"]["form_sale_day"]["y"] = $year;
    $_SESSION[$module_no]["all"]["form_sale_day"]["m"] = $month;
    $_SESSION[$module_no]["all"]["form_sale_day"]["d"] = $day;

    //フラグをクリア
    //$next_data["next_d_button_flg"] = "";
    $next_data["next_w_button_flg"] = "";
    $form->setConstants($next_data);

}

/****************************/
//初期表示
/****************************/
$def_fdata = array(
    //"form_output"     => "1",
    "form_sale_day" => array(
        "y" => date("Y"),
        "m" => date("m"),
        "d" => date("d"),
    ),
);
$form->setDefaults($def_fdata);

/****************************/
//フォーム定義
/****************************/

//担当者
$select_value = NULL;
//$select_value = Select_Get($db_con, "round_staff_ms");
$select_value = Select_Get($db_con, "round_staff_m");
$form->addElement('select', 'form_staff_1', 'セレクトボックス', $select_value,$g_form_option_select);

//部署
$select_value = Select_Get($db_con,'part');
$form->addElement('select', 'form_part_1', 'セレクトボックス', $select_value,$g_form_option_select);

// FCのショップ
$select_value = NULL;
$select_value = Select_Get($db_con, "calshop");
$form->addElement('select', 'form_fc', 'セレクトボックス', $select_value,$g_form_option_select);

//表示ボタン
$form->addElement("submit","indicate_button","表　示", null);

//クリアボタン
$form->addElement("button","clear_button","クリア","onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

//伝票作成
$form->addElement("button","form_slip_button","伝票作成","onClick=\"javascript:Referer('2-2-201.php')\"");

//ヘッダに表示するボタン
$form->addElement("button","week_button","　週　",$g_button_color."onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","month_button","　月　","onClick=\"location.href='2-2-101-2.php'\"");


$form->addElement("hidden", "back_w_button_flg");     //先週ボタン押下判定
$form->addElement("hidden", "next_w_button_flg");     //翌週ボタン押下判定
$form->addElement("hidden", "next_d_count");          //今日から何日後
$form->addElement("hidden", "back_d_count");          //今日から何日前

//予定日
$text = NULL;
$text[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_sale_day[y]', 'form_sale_day[m]',4)\"
         onFocus=\"onForm_today(this,this.form,'form_sale_day[y]','form_sale_day[m]','form_sale_day[d]')\"
         onBlur=\"blurForm(this)\""
);
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement(
        "text","m","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
         onkeyup=\"changeText(this.form,'form_sale_day[m]', 'form_sale_day[d]',2)\"
         onFocus=\"onForm_today(this,this.form,'form_sale_day[y]','form_sale_day[m]','form_sale_day[d]')\"
         onBlur=\"blurForm(this)\""
);
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement(
        "text","d","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
         onFocus=\"onForm_today(this,this.form,'form_sale_day[y]','form_sale_day[m]','form_sale_day[d]')\"
         onBlur=\"blurForm(this)\""
);
$form->addGroup( $text,"form_sale_day","form_sale_day");


/****************************/
// フォームデータをセッションに
/****************************/
// 表示ボタン、日付系ボタン押下かつ予定データ明細の戻るが押下されていないかつエラーの無い時
if (($_POST["indicate_button"] != null ||
     $_POST["next_w_button_flg"] != null ||
     $_POST["back_w_button_flg"] != null )
    && $error_flg != true){

    // POSTデータをSESSIONにセット
    $_SESSION[$module_no]["next_w_button_flg"]  = $_POST["next_w_button_flg"];
    $_SESSION[$module_no]["back_w_button_flg"]  = $_POST["back_w_button_flg"];
    $_SESSION[$module_no]["form_sale_day"]["y"] = $_POST["form_sale_day"]["y"];
    $_SESSION[$module_no]["form_sale_day"]["m"] = $_POST["form_sale_day"]["m"];
    $_SESSION[$module_no]["form_sale_day"]["d"] = $_POST["form_sale_day"]["d"];

}


//巡回担当者識別配列
$aorder_staff = array(1=>"0",2=>"1",3=>"2",4=>"3");

//エラーの場合はこれ以降の表示処理を行なわない
if($error_flg == false && $_POST != null){

    //委託先が指定していない場合に通常伝票表示
    if($post_fc_id == NULL){
        /****************************/
        //照会データ取得（契約区分が通常）
        /****************************/
        for($i=1;$i<=4;$i++){

            //担当者（メイン）判定
            if($i!=1){
                //メイン以外はUNIONで結合
                $sql .= "UNION \n";
                $sql .= "SELECT \n";
            }else{
                //メイン
                $sql  = "SELECT \n";
            }

            //確定前
            $sql .= "    t_part.part_name, \n";             //部署名0
            $sql .= "    t_staff$i.staff_name, \n";         //スタッフ名1
            $sql .= "    t_aorder_h.net_amount, \n";        //売上金額2
            $sql .= "    t_aorder_h.ord_time, \n";          //受注日3
            $sql .= "    t_aorder_h.route, \n";             //順路4
            $sql .= "    t_aorder_h.client_cname, \n";      //得意先名5
            $sql .= "    t_aorder_h.aord_id, \n";           //受注ID6
            $sql .= "    NULL, \n";                         //手書伝票フラグ7
            $sql .= "    t_aorder_h.del_flg, \n";   //保留伝票削除フラグ8
            $sql .= "    NULL, \n";                         //更新フラグ9
            $sql .= "    t_staff$i.staff_cd1, \n";          //スタッフコード1 10
            $sql .= "    t_staff$i.staff_cd2, \n";          //スタッフコード2 11
            $sql .= "    CASE \n";                          //部署ID12
            $sql .= "    WHEN t_part.part_id IS NULL THEN 0 \n";
            $sql .= "    WHEN t_part.part_id IS NOT NULL THEN t_part.part_id \n";
            $sql .= "    END, \n";
            $sql .= "    t_aorder_h.reason, \n";            //保留理由13
            $sql .= "    t_aorder_h.confirm_flg, \n";       //確定伝票14
            $sql .= "    t_aorder_h.client_id, \n";         //得意先ID15
            $sql .= "    t_staff$i.charge_cd, \n";          //担当者コード16
            $sql .= "    t_aorder_h.client_cd1, \n";        //得意先コード1 17
            $sql .= "    t_aorder_h.client_cd2, \n";        //得意先コード2 18
            $sql .= "    t_staff$i.staff_id, \n";           //スタッフID19
            $sql .= "    t_aorder_h.tax_amount, \n";        //消費税額 20
            $sql .= "    t_staff$i.sale_rate, \n";          //売上率 21
            $sql .= "    t_part.part_cd, \n";               //部署名CD 22
            $sql .= "    t_staff_count.num, \n";            //伝票人数 23
            $sql .= "    t_aorder_h.act_id \n";             //代行先ID 24

            $sql .= "FROM \n";
            $sql .= "    t_aorder_h \n";

            $sql .= "    INNER JOIN ( \n";
            $sql .= "        SELECT \n";
            $sql .= "            aord_id,\n";
            $sql .= "            count(aord_id)AS num \n";
            $sql .= "        FROM \n";
            $sql .= "            t_aorder_staff \n";
            $sql .= "        WHERE \n";
            $sql .= "            sale_rate IS NOT NULL \n";
            $sql .= "        GROUP BY \n";
            $sql .= "            aord_id \n";
            $sql .= "    )AS t_staff_count ON t_staff_count.aord_id = t_aorder_h.aord_id \n";

            $sql .= "    INNER JOIN \n";
            $sql .= "        (SELECT \n";
            $sql .= "             t_aorder_staff.aord_id, \n";
            $sql .= "             t_staff.staff_id, \n";
            $sql .= "             t_aorder_staff.staff_name, \n";
            $sql .= "             t_staff.staff_cd1, \n";
            $sql .= "             t_staff.staff_cd2, \n";
            $sql .= "             t_staff.charge_cd, \n";
            $sql .= "             t_aorder_staff.sale_rate \n";
            $sql .= "         FROM \n";
            $sql .= "             t_aorder_staff  \n";
            $sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id \n";
            $sql .= "         WHERE \n";
            $sql .= "             t_aorder_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-007　　　　suzuki-t　　売上率が0%の担当者を表示
 *
            $sql .= "         AND  \n";
            $sql .= "             t_aorder_staff.sale_rate != '0'  \n";
*/
            $sql .= "         AND \n";
            $sql .= "             t_aorder_staff.sale_rate IS NOT NULL \n";
            $sql .= "        )AS t_staff$i ON t_staff$i.aord_id = t_aorder_h.aord_id \n";

            $sql .= "    INNER JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id \n";

            $sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id \n";

            $sql .= "WHERE \n";

            if($_SESSION["group_kind"] == '2'){
                $sql .= "    t_aorder_h.shop_id IN (".Rank_Sql().") \n";
            }else{
                $sql .= "    t_aorder_h.shop_id = $client_id \n";
            }
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-008　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *
            $sql .= "    AND ";
            $sql .= "    t_client.state = '1' ";
*/
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.ps_stat != '4' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.confirm_flg = 'f' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.contract_div = '1' \n";
            $sql .= "    AND \n";
            $sql .= "    t_attach.h_staff_flg = 'false' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.ord_time >= '$year-$month-$day' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.ord_time <= '$nyear-$nmonth-$nday' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.del_flg = 'f' \n";

            //部署指定判定
            if($post_part_id != NULL){
                $sql .= "    AND \n";
                $sql .= "    t_part.part_id = $post_part_id \n";
            }
            //担当者指定判定
            if($post_staff_id != NULL){
                $sql .= "    AND \n";
                $sql .= "    t_staff$i.staff_id = $post_staff_id \n";
            }

            $sql .= "UNION \n";

            //確定後
            $sql .= "SELECT \n";
            $sql .= "    t_part.part_name, \n";             //部署名0
            $sql .= "    t_staff$i.staff_name, \n";         //スタッフ名1
            $sql .= "    t_sale_h.net_amount, \n";          //売上金額2
            $sql .= "    t_sale_h.sale_day, \n";            //売上日3
            $sql .= "    t_aorder_h.route, \n";             //順路4
            $sql .= "    t_sale_h.client_cname, \n";        //得意先名5
            $sql .= "    t_sale_h.aord_id, \n";             //受注ID6
            $sql .= "    NULL, \n";                         //手書伝票フラグ7
            $sql .= "    t_aorder_h.del_flg, \n";   //保留伝票削除フラグ8
            $sql .= "    NULL, \n";                         //更新フラグ9
            $sql .= "    t_staff$i.staff_cd1, \n";          //スタッフコード1 10
            $sql .= "    t_staff$i.staff_cd2, \n";          //スタッフコード2 11
            $sql .= "    CASE \n";                          //部署ID12
            $sql .= "    WHEN t_part.part_id IS NULL THEN 0 \n";
            $sql .= "    WHEN t_part.part_id IS NOT NULL THEN t_part.part_id \n";
            $sql .= "    END, \n";
            $sql .= "    t_aorder_h.reason, \n";            //保留理由13
            $sql .= "    t_aorder_h.confirm_flg, \n";       //確定伝票14
            $sql .= "    t_sale_h.client_id, \n";           //得意先ID15
            $sql .= "    t_staff$i.charge_cd, \n";          //担当者コード16
            $sql .= "    t_sale_h.client_cd1, \n";          //得意先コード1 17
            $sql .= "    t_sale_h.client_cd2, \n";          //得意先コード2 18
            $sql .= "    t_staff$i.staff_id, \n";           //スタッフID19
            $sql .= "    t_sale_h.tax_amount, \n";          //消費税額 20
            $sql .= "    t_staff$i.sale_rate, \n";          //売上率 21
            $sql .= "    t_part.part_cd, \n";               //部署名CD 22
            $sql .= "    t_staff_count.num, \n";            //伝票人数 23
            $sql .= "    t_aorder_h.act_id \n";             //代行先ID 24
               
            $sql .= "FROM \n";
            $sql .= "    t_sale_h \n";

            $sql .= "    INNER JOIN ( \n";
            $sql .= "        SELECT \n";
            $sql .= "            sale_id, \n";
            $sql .= "            count(sale_id)AS num \n";
            $sql .= "        FROM \n";
            $sql .= "            t_sale_staff \n";
            $sql .= "        WHERE \n";
            $sql .= "            sale_rate IS NOT NULL \n";
            $sql .= "        GROUP BY \n";
            $sql .= "            sale_id \n";
            $sql .= "    )AS t_staff_count ON t_staff_count.sale_id = t_sale_h.sale_id  \n";

            $sql .= "    INNER JOIN \n";
            $sql .= "        (SELECT \n";
            $sql .= "             t_sale_staff.sale_id, \n";
            $sql .= "             t_staff.staff_id, \n";
            $sql .= "             t_sale_staff.staff_name, \n";
            $sql .= "             t_staff.staff_cd1, \n";
            $sql .= "             t_staff.staff_cd2, \n";
            $sql .= "             t_staff.charge_cd, \n";
            $sql .= "             t_sale_staff.sale_rate \n";
            $sql .= "         FROM \n";
            $sql .= "             t_sale_staff \n";
            $sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_sale_staff.staff_id \n";
            $sql .= "         WHERE \n";
            $sql .= "             t_sale_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-007　　　　suzuki-t　　売上率が0%の担当者を表示
 *
            $sql .= "         AND  \n";
            $sql .= "             t_sale_staff.sale_rate != '0'  \n";
*/
            $sql .= "         AND \n";
            $sql .= "             t_sale_staff.sale_rate IS NOT NULL \n";
            $sql .= "        )AS t_staff$i ON t_staff$i.sale_id = t_sale_h.sale_id \n";

            $sql .= "    INNER JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id \n";
            $sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id \n";

            $sql .= "    INNER JOIN  t_aorder_h ON t_aorder_h.aord_id = t_sale_h.aord_id \n";

            $sql .= "WHERE \n";
            if($_SESSION["group_kind"] == '2'){
                $sql .= "    t_sale_h.shop_id IN (".Rank_Sql().") \n";
            }else{
                $sql .= "    t_sale_h.shop_id = $client_id \n";
            }
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-008　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *
            $sql .= "    AND  \n";
            $sql .= "    t_client.state = '1'  \n";
*/
            $sql .= "    AND \n";
            $sql .= "    t_attach.h_staff_flg = 'false' \n";
            $sql .= "    AND \n";
            $sql .= "    t_sale_h.act_request_flg = 'f' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.confirm_flg = 't' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.contract_div = '1' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.ord_time >= '$year-$month-$day' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.ord_time <= '$nyear-$nmonth-$nday' \n";
            $sql .= "    AND \n";
            $sql .= "    t_aorder_h.del_flg = 'f' \n";

            //部署指定判定
            if($post_part_id != NULL){
                $sql .= "    AND \n";
                $sql .= "    t_part.part_id = $post_part_id \n";
            }
            //担当者指定判定
            if($post_staff_id != NULL){
                $sql .= "    AND \n";
                $sql .= "    t_staff$i.staff_id = $post_staff_id \n";
            }

            //FCの場合は、代行のカレンダーは結合して表示
            if($_SESSION["group_kind"] == '3'){
                $sql .= "UNION \n";

                //確定前
                $sql .= "SELECT \n";
                $sql .= "    t_part.part_name, \n";             //部署名0
                $sql .= "    t_staff$i.staff_name, \n";         //スタッフ名1
                $sql .= "    t_aorder_h.net_amount, \n";        //売上金額2
                $sql .= "    t_aorder_h.ord_time, \n";          //受注日3
                $sql .= "    t_aorder_h.route, \n";             //順路4
                $sql .= "    t_aorder_h.client_cname, \n";      //得意先名5
                $sql .= "    t_aorder_h.aord_id, \n";           //受注ID6
                $sql .= "    NULL, \n";                         //手書伝票フラグ7
                $sql .= "    t_aorder_h.del_flg, \n";   //保留伝票削除フラグ8
                $sql .= "    NULL, \n";                         //更新フラグ9
                $sql .= "    t_staff$i.staff_cd1, \n";          //スタッフコード1 10
                $sql .= "    t_staff$i.staff_cd2, \n";          //スタッフコード2 11
                $sql .= "    CASE \n";                          //部署ID12
                $sql .= "    WHEN t_part.part_id IS NULL THEN 0 \n";
                $sql .= "    WHEN t_part.part_id IS NOT NULL THEN t_part.part_id \n";
                $sql .= "    END, \n";
                $sql .= "    t_aorder_h.reason, \n";            //保留理由13
                $sql .= "    t_aorder_h.confirm_flg, \n";       //確定伝票14
                $sql .= "    t_aorder_h.client_id, \n";         //得意先ID15
                $sql .= "    t_staff$i.charge_cd, \n";          //担当者コード16
                $sql .= "    t_aorder_h.client_cd1, \n";        //得意先コード1 17
                $sql .= "    t_aorder_h.client_cd2, \n";        //得意先コード2 18
                $sql .= "    t_staff$i.staff_id, \n";           //スタッフID19
                $sql .= "    t_aorder_h.tax_amount, \n";        //消費税額 20
                $sql .= "    t_staff$i.sale_rate, \n";          //売上率 21
                $sql .= "    t_part.part_cd, \n";               //部署名CD 22
                $sql .= "    NULL, \n";                         //伝票人数 23
                $sql .= "    t_aorder_h.act_id \n";             //代行先ID 24

                $sql .= "FROM \n";
                $sql .= "    t_aorder_h \n";

                $sql .= "    INNER JOIN \n";
                $sql .= "        (SELECT \n";
                $sql .= "             t_aorder_staff.aord_id, \n";
                $sql .= "             t_staff.staff_id, \n";
                $sql .= "             t_aorder_staff.staff_name, \n";
                $sql .= "             t_staff.staff_cd1, \n";
                $sql .= "             t_staff.staff_cd2, \n";
                $sql .= "             t_staff.charge_cd, \n";
                $sql .= "             t_aorder_staff.sale_rate \n";
                $sql .= "         FROM \n";
                $sql .= "             t_aorder_staff \n";
                $sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id \n";
                $sql .= "         WHERE \n";
                $sql .= "             t_aorder_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-007　　　　suzuki-t　　売上率が0%の担当者を表示
 *
                $sql .= "         AND  \n";
                $sql .= "             t_aorder_staff.sale_rate != '0'  \n";
*/
                $sql .= "         AND \n";
                $sql .= "             t_aorder_staff.sale_rate IS NOT NULL \n";
                $sql .= "        )AS t_staff$i ON t_staff$i.aord_id = t_aorder_h.aord_id \n";

                $sql .= "    LEFT JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id AND t_attach.h_staff_flg = 'false' \n";

                $sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id \n";

                $sql .= "WHERE \n";

                $sql .= "    t_aorder_h.act_id = $client_id \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-010　　　　suzuki-t　　代行先の取引状態に関係なく表示
 *              
                $sql .= "    AND  \n";
                $sql .= "    t_client.state = '1'  \n";
*/
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.ps_stat != '4' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.confirm_flg = 'f' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.contract_div = '2' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.ord_time >= '$year-$month-$day' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.ord_time <= '$nyear-$nmonth-$nday' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.del_flg = 'f' \n";

                //部署指定判定
                if($post_part_id != NULL){
                    $sql .= "    AND \n";
                    $sql .= "    t_part.part_id = $post_part_id \n";
                }
                //担当者指定判定
                if($post_staff_id != NULL){
                    $sql .= "    AND \n";
                    $sql .= "    t_staff$i.staff_id = $post_staff_id \n";
                }

                $sql .= "UNION \n";

                //確定後
                $sql .= "SELECT ";
                $sql .= "    t_part.part_name, \n";             //部署名0
                $sql .= "    t_staff$i.staff_name, \n";         //スタッフ名1
                $sql .= "    t_sale_h.net_amount, \n";          //売上金額2
                $sql .= "    t_sale_h.sale_day, \n";            //売上日3
                $sql .= "    t_aorder_h.route, \n";             //順路4
                $sql .= "    t_sale_h.client_cname, \n";        //得意先名5
                $sql .= "    t_sale_h.aord_id, \n";             //受注ID6
                $sql .= "    NULL, \n";                         //手書伝票フラグ7
                $sql .= "    t_aorder_h.del_flg, \n";   //保留伝票削除フラグ8
                $sql .= "    NULL, \n";                         //更新フラグ9
                $sql .= "    t_staff$i.staff_cd1, \n";          //スタッフコード1 10
                $sql .= "    t_staff$i.staff_cd2, \n";          //スタッフコード2 11
                $sql .= "    CASE \n";                          //部署ID12
                $sql .= "    WHEN t_part.part_id IS NULL THEN 0 \n";
                $sql .= "    WHEN t_part.part_id IS NOT NULL THEN t_part.part_id \n";
                $sql .= "    END, \n";
                $sql .= "    t_aorder_h.reason, \n";            //保留理由13
                $sql .= "    t_aorder_h.confirm_flg, \n";       //確定伝票14
                $sql .= "    t_sale_h.client_id, \n";           //得意先ID15
                $sql .= "    t_staff$i.charge_cd, \n";          //担当者コード16
                $sql .= "    t_sale_h.client_cd1, \n";          //得意先コード1 17
                $sql .= "    t_sale_h.client_cd2, \n";          //得意先コード2 18
                $sql .= "    t_staff$i.staff_id, \n";           //スタッフID19
                $sql .= "    t_sale_h.tax_amount, \n";          //消費税額 20
                $sql .= "    t_staff$i.sale_rate, \n";          //売上率 21
                $sql .= "    t_part.part_cd, \n";               //部署名CD 22
                $sql .= "    NULL, \n";                         //伝票人数 23
                $sql .= "    t_aorder_h.act_id \n";             //代行先ID 24
                   
                $sql .= "FROM \n";
                $sql .= "    t_sale_h \n";

                $sql .= "    INNER JOIN \n";
                $sql .= "        (SELECT \n";
                $sql .= "             t_sale_staff.sale_id, \n";
                $sql .= "             t_staff.staff_id, \n";
                $sql .= "             t_sale_staff.staff_name, \n";
                $sql .= "             t_staff.staff_cd1, \n";
                $sql .= "             t_staff.staff_cd2, \n";
                $sql .= "             t_staff.charge_cd, \n";
                $sql .= "             t_sale_staff.sale_rate \n";
                $sql .= "         FROM \n";
                $sql .= "             t_sale_staff \n";
                $sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_sale_staff.staff_id \n";
                $sql .= "         WHERE \n";
                $sql .= "             t_sale_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-007　　　　suzuki-t　　売上率が0%の担当者を表示
 *
                $sql .= "         AND  \n";
                $sql .= "             t_sale_staff.sale_rate != '0'  \n";
*/
                $sql .= "         AND \n";
                $sql .= "             t_sale_staff.sale_rate IS NOT NULL \n";
                $sql .= "        )AS t_staff$i ON t_staff$i.sale_id = t_sale_h.sale_id \n";

                $sql .= "    LEFT JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id AND t_attach.h_staff_flg = 'false' \n";

                $sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id \n";

                $sql .= "    INNER JOIN  t_aorder_h ON t_aorder_h.aord_id = t_sale_h.aord_id \n";

                $sql .= "WHERE \n";

                $sql .= "    t_aorder_h.act_id = $client_id \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-010　　　　suzuki-t　　代行先の取引状態に関係なく表示
 *          
                $sql .= "    AND  \n";
                $sql .= "    t_client.state = '1'  \n";
*/
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.contract_div = '2' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.confirm_flg = 't' \n";
                $sql .= "    AND \n";
                $sql .= "    t_sale_h.act_request_flg = 'f' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.ord_time >= '$year-$month-$day' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.ord_time <= '$nyear-$nmonth-$nday' \n";
                $sql .= "    AND \n";
                $sql .= "    t_aorder_h.del_flg = 'f' \n";

                //部署指定判定
                if($post_part_id != NULL){
                    $sql .= "    AND \n";
                    $sql .= "    t_part.part_id = $post_part_id \n";
                }
                //担当者指定判定
                if($post_staff_id != NULL){
                    $sql .= "    AND \n";
                    $sql .= "    t_staff$i.staff_id = $post_staff_id \n";
                }
            }
        }
        $sql .= "ORDER BY \n";
        $sql .= "    23, \n";   //部署名CD
        $sql .= "    17, \n";   //担当者コード
        $sql .= "    4, \n";    //受注日
        $sql .= "    18, \n";   //得意先コード1
        $sql .= "    19, \n";   //得意先コード2
        $sql .= "    7 \n";     //受注ID
        $sql .= ";";
//print_array($sql);

        $result = Db_Query($db_con, $sql);

        //委託先の検索時には通常伝票非表示
        if($post_fc_id == NULL){
            $data_list = Get_Data($result);
        }

        /****************************/
        //受注ヘッダーにある巡回担当者のデータを担当者配列に上書きする
        /****************************/
        $n_data_list = NULL;
        $staff_data_flg = false;  //受注データ存在フラグ判定
        for($x=0;$x<count($data_list);$x++){
            $ymd = $data_list[$x][3];          //巡回日
            $part_id = $data_list[$x][12];     //部署ID
            $staff_id = $data_list[$x][19];    //スタッフID

            //連想配列に登録する。
            $n_data_list[$part_id][$staff_id][$ymd][] = $data_list[$x];

            $data_list2[$part_id][0][0] = $data_list[$x][0];    //部署名
            //$data_list2[$part_id][0][1]++;                          //予定件数

            //予定金額 (売上金額+消費税額)×売上率
            $money1 = $data_list[$x][2] + $data_list[$x][20];
            $money2 = $data_list[$x][21] / 100;

            #2010-05-13 hashimoto-y
            #echo "売上率:" .$data_list[$x][21];

            #2010-05-13 hashimoto-y
            //売上率判定
            #if($money2 != 0){
            if($staff_id != null){
                //売上率から計算
                $total1 = bcmul($money1,$money2,2);
                //得意先の丸め区分を取得し、丸め処理
                $sql  = "SELECT";
                $sql .= "   t_client.coax ";
                $sql .= " FROM";
                $sql .= "   t_client ";
                $sql .= " WHERE";
                $sql .= "   t_client.client_id = ".$data_list[$x][15];
                $sql .= ";";
                $result = Db_Query($db_con, $sql); 
                $client_list = Get_Data($result,2);

                $coax            = $client_list[0][0];        //丸め区分（商品）
                $total1 = Coax_Col($coax,$total1);
            }else{
                //オフライン代行の場合は、売上率は無い為乗算しない
                $total1 = $money1;
            }
            $data_list2[$part_id][0][2] = bcadd($total1,$data_list2[$part_id][0][2]);

            //受注データが存在した
            $staff_data_flg = true;
        }

        //受注データが存在した場合にその担当者データをカレンダー配列に上書き
        if($staff_data_flg == true){

            //数値形式変更
            while($money_num = each($data_list2)){
                $money = $money_num[0];
                //予定金額切捨て
                $data_list2[$money][0][2] = floor($data_list2[$money][0][2]);
                $data_list2[$money][0][2] = number_format($data_list2[$money][0][2]);
            }
            //指標をリセットする
            reset($data_list2);

            /****************************/
            //カレンダーテーブル作成処理
            /****************************/

            //カレンダーHTML
            $calendar   = NULL;
            $date_num_y = NULL;
            $date_num_m = NULL;
            $date_num_d = NULL;

            //ABCD週の表示データ作成
            for($ab=0;$ab<7;$ab++){
                //該当日が何週か取得処理
                $next = mktime(0, 0, 0, $month,$day+$ab,$year);
                $cnyear     = date("Y",$next); //年
                $cnmonth    = date("m",$next); //月
                $cnday      = date("d",$next); //日
                $week[$ab]  = date("w",$next); //曜日

                $date_num_y[] = $cnyear;       //一週間の年配列
                $date_num_m[] = $cnmonth;      //一週間の月配列
                $date_num_d[] = $cnday;        //一週間の日配列

                //ABCD判別関数
                //月の最初の日が何週か取得処理
                $base_date = Basic_date($day_by,$day_bm,$day_bd,$cnyear,$cnmonth,$cnday);
                $row = $base_date[0];
                //基準日より過去の日付の場合は、０を代入
                if($row == NULL){
                    $row = 0;
                }
                $abcd[$ab] = $row;
            }

            //ABCD週の表示配列
            $abcd_w[1] = "A";
            $abcd_w[2] = "B";
            $abcd_w[3] = "C";
            $abcd_w[4] = "D";

            //ABCD週の結合数取得
            $rowspan = array_count_values($abcd);

            /****************************/
            //カレンダーテーブル上書き処理
            /****************************/
            //部署ごとに巡回データ作成
            while($part_num = each($n_data_list)){
                //部署の添字取得
                $part = $part_num[0];

                //先週ボタン
                //非表示判定
                if($bw_disabled_flg == true){
                    //非表示
                    $form->addElement("button","back_w_button[$part]","<<　先週","disabled");
                }else{
                    //表示
                    $form->addElement("button","back_w_button[$part]","<<　先週","onClick=\"javascript:Button_Submit('back_w_button_flg','".$_SERVER["PHP_SELF"]."#$part','true')\"");
                }

                //翌週ボタン
                //非表示判定
                if($nw_disabled_flg == true){
                    //非表示
                    $form->addElement("button","next_w_button[$part]","翌週　>>","disabled");
                }else{
                    //表示
                    $form->addElement("button","next_w_button[$part]","翌週　>>",
                        "onClick=\"javascript:Button_Submit('next_w_button_flg','".$_SERVER["PHP_SELF"]."#$part','true')\""
                    );
                }


                /****************************/
                //巡回担当者
                /****************************/
                //部署に属するスタッフがいた場合に表示
                if($n_data_list[$part] != NULL){
                    //担当者ごとに巡回データ作成
                    while($staff_num = each($n_data_list[$part])){
                        //担当者ID
                        $staff_id = $staff_num[0];

                        /****************************/
                        //ABCD週HTML
                        /****************************/
                        $calendar[$part]  = "<tr height=\"40\">";
                        $calendar[$part] .= "  <td align=\"center\" bgcolor=\"#cccccc\" width=\"60\"><b>巡回基準</b></td>";
                        //ABCD週作成
                        while($abcd_num = each($rowspan)){
                            //ABCDの添字取得
                            $ab_num = $abcd_num[0];
                            $calendar[$part] .= "  <td align=\"center\" bgcolor=\"#e5e5e5\" colspan=\"".$rowspan[$ab_num]."\" style=\"font-size: 130%; font-weight: bold; padding: 0px;\">".$abcd_w[$ab_num]."</td>";
                        }
                        $calendar[$part] .= "</tr>";
                        //指標をリセットする
                        reset($rowspan);

                        /****************************/
                        //曜日HTML
                        /****************************/
                        //曜日配列
                        $week_w[0] = "日";
                        $week_w[1] = "月";
                        $week_w[2] = "火";
                        $week_w[3] = "水";
                        $week_w[4] = "木";
                        $week_w[5] = "金";
                        $week_w[6] = "土";

                        $calendar[$part]  .= "<tr height=\"20\">";
                        $calendar[$part]  .= "  <td align=\"center\" bgcolor=\"#cccccc\" rowspan=\"2\" width=\"80\"><b>巡回担当者</b></td>";
                        //一週間分表示
                        for($w=0;$w<7;$w++){
                            //曜日判定
                            if($week[$w] == 6 && $holiday[$date_num_y[$w]."-".$date_num_m[$w]."-".$date_num_d[$w]] != 1){
                                //土曜かつ休日ではない
                                $calendar[$part] .= "       <td width=\"135px\" align=\"center\" bgcolor=\"#66CCFF\"><b>";
                            }else if($week[$w] == 0 || $holiday[$date_num_y[$w]."-".$date_num_m[$w]."-".$date_num_d[$w]] == 1){
                                //日曜or休日
                                $calendar[$part] .= "       <td width=\"135px\" align=\"center\" bgcolor=\"#FFBBC3\"><b>";
                            }else{
                                //月〜金
                                $calendar[$part] .= "<td width=\"135px\" align=\"center\" bgcolor=\"#cccccc\"><b>";
                            }
                            $calendar[$part] .= $week_w[$week[$w]]."</b></td>";
                        }
                        $calendar[$part] .= "</tr>";

                        //担当者名
                        $num1 = each($n_data_list[$part][$staff_id]);
                        $num2 = each($n_data_list[$part][$staff_id][$num1[0]]);
                        $staff_name = $n_data_list[$part][$staff_id][$num1[0]][$num2[0]][1];
                        $calendar2[$part][$staff_id]  = "<tr>";
                        $calendar2[$part][$staff_id]  .= "<td width=\"80px\" align=\"center\" valign=\"center\" bgcolor=\"#E5E5E5\" >";
                        $calendar2[$part][$staff_id]  .= "<font size=\"2\">$staff_name</font></td>";

                        //一週間分表示
                        for($d=0;$d<7;$d++){
                            //曜日判定
                            if($week[$d] == 6 && $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] != 1){
                                //土曜かつ休日ではない
                                $calendar2[$part][$staff_id] .= "       <td width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#99FFFF\" class=\"cal3\" width=\"13%\">";
                            }else if($week[$d] == 0 || $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] == 1){
                                //日曜or休日
                                $calendar2[$part][$staff_id] .= "       <td width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#FFDDE7\" class=\"cal3\" width=\"13%\">";
                            }else{
                                //月〜金
                                $calendar2[$part][$staff_id] .= "       <td width=\"135px\" align=\"left\" valign=\"top\" class=\"cal3\" width=\"13%\">";
                            }
                            //データ存在判定
                            $date = $date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d];

                                //順路・得意先表示

                                //該当日に巡回データが複数存在した場合、その件数分表示
                                $calendar_tmp = array();
                                $client_cname_tmp = array();
                                $link_color = null;
                                for($y=0;$y<count($n_data_list[$part][$staff_id][$date]);$y++){
                                    //該当日に巡回データが存在するか判定
                                    if($n_data_list[$part][$staff_id][$date][$y][6] != NULL){

                                        //データが存在した為、その日をリンクにする
                                        $link_data[$part][$d] = true;

                                        //予定明細に渡す受注ID配列作成
                                        $aord_id_array[$part][$date][] = $n_data_list[$part][$staff_id][$date][$y][6];

                                        //得意先ごとにまとめる
                                        // [得意先ID][] = 受注ID という配列を作る
                                        $client_id_tmp = $n_data_list[$part][$staff_id][$date][$y][15];     //得意先ID
                                        $calendar_tmp[$client_id_tmp][] = $n_data_list[$part][$staff_id][$date][$y][6];     //受注IDを詰める配列
                                        $client_cname_tmp[$client_id_tmp] = $n_data_list[$part][$staff_id][$date][$y][5];   //得意先名(略称)を詰める配列
                                        //リンクの色を詰める配列
                                        $link_color[$client_id_tmp] = L_Link_Color($link_color[$client_id_tmp], $n_data_list[$part][$staff_id][$date][$y][14], $n_data_list[$part][$staff_id][$date][$y][24], $n_data_list[$part][$staff_id][$date][$y][23]);
                                    }
                                }
                                //得意先ごとにまとめたデータからリンクを生成
                                foreach($calendar_tmp as $client_id_key => $aord_id_array_value){
                                    //シリアライズ化
                                    $array_id = serialize($aord_id_array_value);
                                    $array_id = urlencode($array_id);

                                    $calendar2[$part][$staff_id] .= " <a href=\"./2-2-106.php?aord_id_array=".$array_id."&back_display=cal_week\"";
                                    $calendar2[$part][$staff_id] .= " style=\"color: ".$link_color[$client_id_key].";\"";
                                    $calendar2[$part][$staff_id] .= ">";
                                    $calendar2[$part][$staff_id] .= $client_cname_tmp[$client_id_key]."</a><br>";

                                    $data_list2[$part][0][1]++;     //予定件数
                                }
                            //}
                            $calendar2[$part][$staff_id] .= "</font></td>";
                            $course_array = NULL;
                        }
                        $calendar2[$part][$staff_id] .= "</tr>";
                    }

                    /****************************/
                    //日付HTML上書き処理
                    /****************************/
                    $calendar3[$part]  = "<tr height=\"20\" style=\"font-size: 130%; font-weight: bold; \">";
                    //一週間分表示
                    for($d=0;$d<7;$d++){
                        //曜日判定
                        if($week[$d] == 6 && $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] != 1){
                            //土曜かつ休日ではない
                            $calendar3[$part] .= "      <td width=\"135px\" align=\"center\" bgcolor=\"#99FFFF\"><b>";
                        }else if($week[$d] == 0 || $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] == 1){
                            //日曜or休日
                            $calendar3[$part] .= "      <td width=\"135px\" align=\"center\" bgcolor=\"#FFDDE7\"><b>";
                        }else{
                            //月〜金
                            $calendar3[$part] .= "<td width=\"135px\" align=\"center\" bgcolor=\"#cccccc\"><b>";
                        }

                        //日付リンク判定
                        if($link_data[$part][$d] == true){

                            //週の日付
                            $date = $date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d];

                            for($p=0;$p<count($aord_id_array[$part][$date]);$p++){
                                $aord_id_array2[] = $aord_id_array[$part][$date][$p];
                            }

                            //重複を削除
                            $aord_id_array3 = array_unique($aord_id_array2);
                            //配列を詰め直す
                            $aord_id_array2 = array_values($aord_id_array3);

                            //シリアライズ化
                            $array_id = serialize($aord_id_array2);
                            $array_id = urlencode($array_id);

                            //土日休（リンク）
                            $calendar3[$part] .= "<a href=\"2-2-106.php?aord_id_array=".$array_id."&back_display=cal_week\" style=\"color: #555555;\">";
                            $calendar3[$part] .= (int)$date_num_d[$d]."</a></b></td>";

                            $aord_id_array2 = null;
                            $aord_id_array3 = null;
                        }else{
                            //土日休（リンクなし）
                            $calendar3[$part] .= (int)$date_num_d[$d]."</b></td>";
                        }
                    }
                    $calendar3[$part] .= "</tr>";
                }
                $aord_id_array = null;
            }//部署ループ
        }
    }

    //検索時には代行カレンダー非表示
    if($post_part_id == NULL && $_SESSION["group_kind"] != 3 && $post_staff_id == NULL){

/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/11/01　02-004　　　　suzuki-t　　同じオンライン伝票が２件表示されるのを修正
 *
*/              
        /****************************/
        //照会データ取得（契約区分が代行）
        /****************************/
        //確定前
        $sql  = "SELECT  \n";
        $sql .= "    t_aorder_h.act_name, \n";           //受託先名0
        $sql .= "    t_aorder_h.act_id, \n";             //受託先ID1
        $sql .= "    t_aorder_h.act_cd1, \n";            //受託先コード1 2
        $sql .= "    t_aorder_h.act_cd2, \n";            //受託先コード2 3
        $sql .= "    t_aorder_h.net_amount, \n";         //売上金額4
        $sql .= "    t_aorder_h.ord_time, \n";           //受注日5
        $sql .= "    t_aorder_h.route, \n";              //順路6
        $sql .= "    t_aorder_h.client_cname, \n";       //得意先名7
        $sql .= "    t_aorder_h.aord_id, \n";            //受注ID8
        $sql .= "    t_aorder_h.client_id, \n";          //得意先ID9
        $sql .= "    NULL,  \n";   
        $sql .= "    t_aorder_h.client_cd1, \n";         //得意先コード1 11
        $sql .= "    t_aorder_h.client_cd2, \n";         //得意先コード2 12
        $sql .= "    t_aorder_h.tax_amount, \n";         //消費税額 13
        $sql .= "    NULL,  \n";   
        $sql .= "    t_aorder_h.confirm_flg \n";         //確定フラグ 15

        $sql .= "FROM  \n";
        $sql .= "    t_aorder_h  \n";

        $sql .= "WHERE  \n";

        if($_SESSION["group_kind"] == '2'){
            $sql .= "    t_aorder_h.shop_id IN (".Rank_Sql().") \n";
        }else{
            $sql .= "    t_aorder_h.shop_id = $client_id  \n";
        }

        //委託先指定判定
        if($post_fc_id != NULL){
            $sql .= "    AND  \n";
            $sql .= "    t_aorder_h.act_id = $post_fc_id  \n";
        }
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-008　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *          
        $sql .= "    AND  \n";
        $sql .= "    t_client.state = '1'  \n";

 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-010　　　　suzuki-t　　代行先の取引状態に関係なく表示
 *          
        $sql .= "    AND  \n";
        $sql .= "    t_act.state = '1'  \n";
*/
        $sql .= "    AND  \n";
        $sql .= "    t_aorder_h.ps_stat != '4'  \n";
        $sql .= "    AND  \n";
        $sql .= "    t_aorder_h.confirm_flg = 'f'  \n";
        $sql .= "    AND  \n";
        $sql .= "    (t_aorder_h.contract_div = '2' OR t_aorder_h.contract_div = '3') \n";
        $sql .= "    AND \n";
        $sql .= "    t_aorder_h.ord_time >= '$year-$month-$day' \n";
        $sql .= "    AND ";
        $sql .= "    t_aorder_h.ord_time <= '$nyear-$nmonth-$nday' \n";
        $sql .= "    AND \n";
        $sql .= "    t_aorder_h.del_flg = 'f' \n";

        $sql .= "UNION  \n";

        //確定後
        $sql .= "SELECT  \n";
        $sql .= "    t_aorder_h.act_name, \n";           //受託先名0
        $sql .= "    t_aorder_h.act_id, \n";             //受託先ID1
        $sql .= "    t_sale_h.act_cd1, \n";              //受託先コード1 2
        $sql .= "    t_sale_h.act_cd2, \n";              //受託先コード2 3
        $sql .= "    t_sale_h.net_amount, \n";           //売上金額4
        $sql .= "    t_sale_h.sale_day, \n";             //売上日5
        $sql .= "    t_aorder_h.route, \n";              //順路6
        $sql .= "    t_sale_h.client_cname, \n";         //得意先名7
        $sql .= "    t_sale_h.aord_id, \n";              //受注ID8
        $sql .= "    t_sale_h.client_id,  \n";           //得意先ID9
        $sql .= "    NULL,  \n";  
        $sql .= "    t_sale_h.client_cd1, \n";           //得意先コード1 11
        $sql .= "    t_sale_h.client_cd2, \n";           //得意先コード2 12
        $sql .= "    t_sale_h.tax_amount, \n";           //消費税額 13
        $sql .= "    NULL,  \n";  
        $sql .= "    t_aorder_h.confirm_flg \n";         //確定フラグ 15

        $sql .= "FROM  \n";
        $sql .= "    t_sale_h  \n";
        $sql .= "    INNER JOIN  t_aorder_h ON t_aorder_h.aord_id = t_sale_h.aord_id  \n";

        $sql .= "WHERE  \n";
        if($_SESSION["group_kind"] == '2'){
            $sql .= "    t_sale_h.shop_id IN (".Rank_Sql().") \n";
        }else{
            $sql .= "    t_sale_h.shop_id = $client_id  \n";
        }

        //委託先指定判定
        if($post_fc_id != NULL){
            $sql .= "    AND  \n";
            $sql .= "    t_aorder_h.act_id = $post_fc_id  \n";
        }
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-008　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *          
        $sql .= "    AND  \n";
        $sql .= "    t_client.state = '1'  \n";

 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-010　　　　suzuki-t　　代行先の取引状態に関係なく表示
 *          
        $sql .= "    AND  \n";
        $sql .= "    t_act.state = '1'  \n";
*/
        $sql .= "    AND  \n";
        $sql .= "    t_aorder_h.confirm_flg = 't'  \n";
        $sql .= "    AND  \n";
        $sql .= "    (t_aorder_h.contract_div = '2' OR t_aorder_h.contract_div = '3') \n";
        $sql .= "    AND \n";
        $sql .= "    t_aorder_h.ord_time >= '$year-$month-$day' \n";
        $sql .= "    AND ";
        $sql .= "    t_aorder_h.ord_time <= '$nyear-$nmonth-$nday' \n";
        $sql .= "    AND \n";
        $sql .= "    t_aorder_h.del_flg = 'f' \n";

        $sql .= "ORDER BY  \n";
        $sql .= "    3, \n";    //受託先コード1
        $sql .= "    4, \n";    //受託先コード2
        $sql .= "    6, \n";    //受注日
        $sql .= "    12, \n";   //得意先コード1
        $sql .= "    13, \n";   //得意先コード2
        $sql .= "    9; \n";    //受注ID
//print_array($sql);

        $result = Db_Query($db_con, $sql);
        $act_data_list = Get_Data($result);

        /****************************/
        //受託先HTML作成
        /****************************/
        $n_data_list = NULL;
        $act_data_flg = false;  //受注データ存在フラグ判定
        for($x=0;$x<count($act_data_list);$x++){
            $ymd = $act_data_list[$x][5];          //巡回日
            $act_id = "daiko".$act_data_list[$x][1];       //受託先ID

            //連想配列に登録する。
            $n_data_list[$act_id][$ymd][] = $act_data_list[$x];
            
            //予定金額 (売上金額+消費税額)×売上率
            $money1 = $act_data_list[$x][4] + $act_data_list[$x][13];
            $money2 = $act_data_list[$x][14] / 100;
            //売上率判定
            if($money2 != 0){
                //売上率から計算
                $total1 = bcmul($money1,$money2,2);
            }else{
                //オフライン代行の場合は、売上率は無い為乗算しない
                $total1 = $money1;
            }
            $act_data_list2[$act_id][0][1] = bcadd($total1,$act_data_list2[$act_id][0][1]);

            //受注データが存在した
            $act_data_flg = true;
        }
//print_array($n_data_list);
        //受注データが存在した場合にその担当者データをカレンダー配列に上書き
        if($act_data_flg == true){

            //数値形式変更
            $money_num = NULL;
            $link_data = NULL;

            while($money_num = each($act_data_list2)){
                $money = $money_num[0];
                //予定金額切捨て
                $act_data_list2[$money][0][1] = floor($act_data_list2[$money][0][1]);
                $act_data_list2[$money][0][1] = number_format($act_data_list2[$money][0][1]);
            }
            //指標をリセットする
            reset($act_data_list2);

            /****************************/
            //カレンダーテーブル作成処理
            /****************************/

            //カレンダーHTML
            $date_num_y = NULL;
            $date_num_m = NULL;
            $date_num_d = NULL;

            //ABCD週の表示データ作成
            for($ab=0;$ab<7;$ab++){
                //該当日が何週か取得処理
                $next = mktime(0, 0, 0, $month,$day+$ab,$year);
                $nyear     = date("Y",$next); //年
                $nmonth    = date("m",$next); //月
                $nday      = date("d",$next); //日
                $week[$ab] = date("w",$next); //曜日

                $date_num_y[] = $nyear;       //一週間の年配列
                $date_num_m[] = $nmonth;      //一週間の月配列
                $date_num_d[] = $nday;        //一週間の日配列

                //ABCD判別関数
                //月の最初の日が何週か取得処理
                $base_date = Basic_date($day_by,$day_bm,$day_bd,$nyear,$nmonth,$nday);
                $row = $base_date[0];
                //基準日より過去の日付の場合は、０を代入
                if($row == NULL){
                    $row = 0;
                }
                $abcd[$ab] = $row;
            }

            //ABCD週の表示配列
            $abcd_w[1] = "A";
            $abcd_w[2] = "B";
            $abcd_w[3] = "C";
            $abcd_w[4] = "D";

            //ABCD週の結合数取得
            $rowspan = array_count_values($abcd);

            /****************************/
            //カレンダーテーブル上書き処理
            /****************************/
            //受託先ごとに巡回データ作成
            while($act_num = each($n_data_list)){
                //受託先の添字取得
                $act = $act_num[0];

                //先週ボタン
                //非表示判定
                if($bw_disabled_flg == true){
                    //非表示
                    $form->addElement("button","back_w_button[$act]","<<　先週","disabled");
                }else{
                    //表示
                    $form->addElement("button","back_w_button[$act]","<<　先週","onClick=\"javascript:Button_Submit('back_w_button_flg','".$_SERVER["PHP_SELF"]."#$act','true')\"");
                }

                //翌週ボタン
                //非表示判定
                if($nw_disabled_flg == true){
                    //非表示
                    $form->addElement("button","next_w_button[$act]","翌週　>>","disabled");
                }else{
                    //表示
                    $form->addElement("button","next_w_button[$act]","翌週　>>",
                        "onClick=\"javascript:Button_Submit('next_w_button_flg','".$_SERVER["PHP_SELF"]."#$act','true')\""
                    );
                }

                /****************************/
                //ABCD週HTML
                /****************************/
                $act_calendar[$act]  = "<tr height=\"40\">";
                $act_calendar[$act] .=  "  <td align=\"center\" bgcolor=\"#cccccc\" width=\"60\"><b>巡回基準</b></td>";
                //ABCD週作成
                while($abcd_num = each($rowspan)){
                    //ABCDの添字取得
                    $ab_num = $abcd_num[0];
                    $act_calendar[$act] .= "  <td align=\"center\" bgcolor=\"#e5e5e5\" colspan=\"".$rowspan[$ab_num]."\" style=\"font-size: 130%; font-weight: bold; padding: 0px;\">".$abcd_w[$ab_num]."</td>";
                }
                $act_calendar[$act] .= "</tr>";
                //指標をリセットする
                reset($rowspan);

                /****************************/
                //曜日HTML
                /****************************/
                $act_calendar[$act]  .= "<tr height=\"20\">";
                $act_calendar[$act]  .= "   <td align=\"center\" bgcolor=\"#cccccc\" rowspan=\"2\" width=\"80\"><b>巡回担当者</b></td>";
                //一週間分表示
                for($w=0;$w<7;$w++){
                    //曜日判定
                    if($week[$w] == 6 && $holiday[$date_num_y[$w]."-".$date_num_m[$w]."-".$date_num_d[$w]] != 1){
                        //土曜かつ休日ではない
                        $act_calendar[$act] .= "        <td width=\"135px\" align=\"center\" bgcolor=\"#66CCFF\"><b>";
                    }else if($week[$w] == 0 || $holiday[$date_num_y[$w]."-".$date_num_m[$w]."-".$date_num_d[$w]] == 1){
                        //日曜or休日
                        $act_calendar[$act] .= "        <td width=\"135px\" align=\"center\" bgcolor=\"#FFBBC3\"><b>";
                    }else{
                        //月〜金
                        $act_calendar[$act] .= "<td width=\"135px\" align=\"center\" bgcolor=\"#cccccc\"><b>";
                    }
                    $act_calendar[$act] .= $week_w[$week[$w]]."</b></td>";
                }
                $act_calendar[$act] .= "</tr>";

                //受託先名
                $num1 = each($n_data_list[$act]);
                $num2 = each($n_data_list[$act][$num1[0]]);
                $act_name = $n_data_list[$act][$num1[0]][$num2[0]][0];
                $act_calendar2[$act]  = "<tr>";
                $act_calendar2[$act]  .= "<td width=\"80px\" align=\"center\" valign=\"center\" bgcolor=\"#E5E5E5\" >";
                $act_calendar2[$act]  .= "<font size=\"2\">$act_name</font></td>";

                //一週間分表示
                for($d=0;$d<7;$d++){
                    //曜日判定
                    if($week[$d] == 6 && $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] != 1){
                        //土曜かつ休日ではない
                        $act_calendar2[$act] .= "       <td width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#99FFFF\" class=\"cal3\" width=\"13%\">";
                    }else if($week[$d] == 0 || $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] == 1){
                        //日曜or休日
                        $act_calendar2[$act] .= "       <td width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#FFDDE7\" class=\"cal3\" width=\"13%\">";
                    }else{
                        //月〜金
                        $act_calendar2[$act] .= "       <td width=\"135px\" align=\"left\" valign=\"top\" class=\"cal3\" width=\"13%\">";
                    }
                    //データ存在判定
                    $date = $date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d];

                        //得意先表示

                        $calendar_tmp = array();
                        $client_cname_tmp = array();
                        $link_color = null;

                        //該当日に巡回データが複数存在した場合、その件数分表示
                        for($y=0;$y<count($n_data_list[$act][$date]);$y++){

                            //該当日に巡回データが存在するか判定
                            if($n_data_list[$act][$date][$y][8] != NULL){

                                //データが存在した為、その日をリンクにする
                                $link_data[$act][$d] = true;

                                /*
                                 * 履歴：
                                 * 　日付　　　　B票No.　　　　担当者　　　内容　
                                 * 　2006/11/01　02-035　　　　suzuki-t　  日付リンク押下時にTOPに遷移しないように修正
                                */
                                //予定明細に渡す受注ID配列作成
                                $aord_id_array[$act][$date][$y] = $n_data_list[$act][$date][$y][8];

                                //得意先ごとにまとめる
                                // [得意先ID][] = 受注ID という配列を作る
                                $client_id_tmp = $n_data_list[$act][$date][$y][9];      //得意先ID
                                $calendar_tmp[$client_id_tmp][] = $n_data_list[$act][$date][$y][8];     //受注IDを詰める配列
                                $client_cname_tmp[$client_id_tmp] = $n_data_list[$act][$date][$y][7];   //得意先名(略称)を詰める配列
                                //リンクの色を詰める配列
                                $link_color[$client_id_tmp] = L_Link_Color($link_color[$client_id_tmp], $n_data_list[$act][$date][$y][15], $n_data_list[$act][$date][$y][1], 1);

                            }
                        }
                        //得意先ごとにまとめたデータからリンクを生成
                        foreach($calendar_tmp as $client_id_key => $aord_id_array_value){
                            //シリアライズ化
                            $array_id = serialize($aord_id_array_value);
                            $array_id = urlencode($array_id);

                            $act_calendar2[$act] .= " <a href=\"./2-2-106.php?aord_id_array=".$array_id."&back_display=cal_week\"";
                            $act_calendar2[$act] .= " style=\"color: ".$link_color[$client_id_key].";\"";
                            $act_calendar2[$act] .= ">";
                            $act_calendar2[$act] .= $client_cname_tmp[$client_id_key]."</a><br>";

                            $act_data_list2[$act][0][0]++;   //予定件数
                        }
                    //}
                    $act_calendar2[$act] .= "</td>";
                }
                $act_calendar2[$act] .= "</tr>";

                /****************************/
                //日付HTML上書き処理
                /****************************/
                $act_calendar3[$act]  = "<tr height=\"20\" style=\"font-size: 130%; font-weight: bold; \">";
                //一週間分表示
                for($d=0;$d<7;$d++){
                    //曜日判定
                    if($week[$d] == 6 && $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] != 1){
                        //土曜かつ休日ではない
                        $act_calendar3[$act] .= "       <td width=\"135px\" align=\"center\" bgcolor=\"#99FFFF\"><b>";
                    }else if($week[$d] == 0 || $holiday[$date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d]] == 1){
                        //日曜or休日
                        $act_calendar3[$act] .= "       <td width=\"135px\" align=\"center\" bgcolor=\"#FFDDE7\"><b>";
                    }else{
                        //月〜金
                        $act_calendar3[$act] .= "<td width=\"135px\" align=\"center\" bgcolor=\"#cccccc\"><b>";
                    }
                    
                    //日付リンク判定
                    if($link_data[$act][$d] == true){

                        //該当日の受注ID全てを予定明細に渡す
                        $aord_id_array2 = NULL;
                        //週の日付
                        $date = $date_num_y[$d]."-".$date_num_m[$d]."-".$date_num_d[$d];

                        for($p=0;$p<count($aord_id_array[$act][$date]);$p++){
                            $aord_id_array2[] = $aord_id_array[$act][$date][$p];
                        }

                        //重複を削除
                        $aord_id_array3 = array_unique($aord_id_array2);
                        //配列を詰め直す
                        $aord_id_array2 = array_values($aord_id_array3);

                        //シリアライズ化
                        $array_id = serialize($aord_id_array2);
                        $array_id = urlencode($array_id);

                        //土日休（リンク）
                        $act_calendar3[$act] .= "<a href=\"2-2-106.php?aord_id_array=".$array_id."&back_display=cal_week\" style=\"color: #555555;\">";
                        $act_calendar3[$act] .= (int)$date_num_d[$d]."</a></b></td>";
                    }else{
                        //土日休（リンクなし）
                        $act_calendar3[$act] .= (int)$date_num_d[$d]."</b></td>";
                    }
                }

                $aord_id_array = null;

                $act_calendar3[$act] .= "</tr>";
            }
        }
    }

    //データが無い場合は、リンク先指定なしのボタン作成
    if($staff_data_flg == false && $act_data_flg == false){

        //先週ボタン
        //非表示判定
        if($bw_disabled_flg == true){
            //非表示
            $form->addElement("button","back_w_button","<<　先週","disabled");
        }else{
            //表示
            $form->addElement("button","back_w_button","<<　先週","onClick=\"javascript:Button_Submit('back_w_button_flg','".$_SERVER["PHP_SELF"]."','true')\"");
        }


        //翌週ボタン
        //非表示判定
        if($nw_disabled_flg == true){
            //非表示
            $form->addElement("button","next_w_button","翌週　>>","disabled");
        }else{
            //表示
            $form->addElement("button","next_w_button","翌週　>>",
                "onClick=\"javascript:Button_Submit('next_w_button_flg','".$_SERVER["PHP_SELF"]."','true')\""
            );
        }

        $data_msg = "巡回データがありません。";
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
//$page_menu = Create_Menu_f('sale','1');

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "　".$form->_elements[$form->_elementIndex[month_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[week_button]]->toHtml();
$page_header = Create_Header($page_title);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);


//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());

//その他の変数をassign
$smarty->assign('var',array(
    'html_header'     => "$html_header",
    //'page_menu'       => "$page_menu",
    'page_header'     => "$page_header",
    'html_footer'     => "$html_footer",
    'year'            => "$year",
    'month'           => "$month",
    'staff_data_flg'  => "$staff_data_flg",
    'charge_data_flg' => "$charge_data_flg",
    'act_data_flg'    => "$act_data_flg",
    'cal_range'       => "$cal_range",
    'data_msg'        => "$data_msg",
    'error_msg'       => "$error_msg",
    'error_msg2'      => "$error_msg2",
));

//表示データ
$smarty->assign("disp_data", $data_list2);
$smarty->assign("calendar", $calendar);
$smarty->assign("calendar2", $calendar2);
$smarty->assign("calendar3", $calendar3);

$smarty->assign("act_disp_data", $act_data_list2);
$smarty->assign("act_calendar", $act_calendar);
$smarty->assign("act_calendar2", $act_calendar2);
$smarty->assign("act_calendar3", $act_calendar3);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));


//print_array($_SESSION, "SESSION");
//print_array($_POST, "POST");


/**
 *
 * カレンダー内の得意先名リンクの色を返す
 *
 * @param       string      $color              色
 * @param       string      $confirm_flg        確定フラグ
 *                                                  "t" => 確定伝票
 *                                                  "f" => 未確認飛行物体
 * @param       int         $act_id             受託先ID
 * @param       int         $staff_num          伝票の巡回担当者数
 *
 * @return      string      色（色名または16進数）
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/02/07)
 *
 */
function L_Link_Color($color, $confirm_flg, $act_id, $staff_num)
{
    //確定伝票
    if(($color == "gray" || $color == null) && $confirm_flg == "t"){
        return "gray";
    //代行伝票
    }elseif(($color == "green" || $color == null) && $act_id != null){
        return "green";
    //予定伝票(一人)
    }elseif(($color == "blue" || $color == null) && $staff_num == 1){
        return "blue";
    //予定伝票(二人)
    }elseif(($color == "Fuchsia" || $color == null) && $staff_num == 2){
        return "Fuchsia";
    //予定伝票(三人以上)
    }elseif(($color == "#FF6600" || $color == null) && ($staff_num == 3 || $staff_num == 4)){
        return "#FF6600";
    //その他(混ざるとき)
    }else{
        return "black";
    }

}


?>
