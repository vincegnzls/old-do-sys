<?php

/*************************
 *　変更履歴
 *      （2006/08/10）件数が表示されるが、データ表示されないバグの修正
 *
 *
 *
 *
**************************/

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/10/31      08-035      ふ          検索日付の日付フォームにフォーカスを合わせても本日の日付が表示されないバグを修正
 *  2006/10/31      08-039      ふ          伝票番号リンクのリンク先モジュール間違いを修正（1-2-101→1-2-110）
 *  2006/11/06      08-036      suzuki      日付入力値が不正な場合エラー表示
 *  2006/11/06      08-037      suzuki      日付入力値が不正な場合にhiddenに入力値を設定しないように修正
 *  2006/11/06      08-038      suzuki      入力チェックの表示形式変更
 *  2006/11/08      08-120      suzuki      得意先を略称表示
 *  2006/11/09      08-121      suzuki      得意先コードを省略しないように修正
 *  2006/11/09      08-122      suzuki      東陽と福岡を別々のページに表示するように修正
 *  2006/11/09      08-130      suzuki      商品名は数値形式に変更しないように修正
 *  2006/11/13      08-156      ふ          初期表示時、全○件が「全件」と表示されていたバグを修正
 *  2006/12/01      ssl-0047    ふ          出荷していないのに、出荷数が１になっているバグの修正
 *  2006/12/07      ban_0046    suzuki      日付のゼロ埋め
 *  2007/01/07                  watanabe-k  入力チェック欄を非表示
 *  2007/01/25                  watanabe-k  ボタンの色変更
 *  2007/02/07                  watanabe-k  売上入力へのリンクをかならず表示するように修正
 *  2007-04-05                  fukuda      検索条件復元処理追加
 *  2009-10-16                  hashimoto-y 検索フォームの受注日の初期値を空値に変更
 *
 */

$page_title = "受注残一覧";

// 環境設定ファイル
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// テンプレート関数をレジスト
$smarty->register_function("Make_Sort_Link_Tpl", "Make_Sort_Link_Tpl");

// DB接続
$db_con = Db_Connect();

// 権限チェック
$auth   = Auth_Check($db_con);


/****************************/
// 検索条件復元関連
/****************************/
// 検索フォーム初期値配列
$ary_form_list = array(
    "form_display_num"  => "1",
    "form_client"       => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_c_staff"      => array("cd" => "", "select" => ""),
    "form_ware"         => "",
    "form_claim"        => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_multi_staff"  => "",
    #2009-10-16 hashimoto-y
    #"form_aord_day"     => array(
    #    "sy" => date("Y"),
    #    "sm" => date("m"),
    #    "sd" => "01",
    #    "ey" => date("Y"),
    #    "em" => date("m"),
    #    "ed" => date("t", mktime(0, 0, 0, date("m"), date("d"), date("Y")))
    #),
    "form_aord_day"     => array(
        "sy" => "",
        "sm" => "",
        "sd" => "",
        "ey" => "",
        "em" => "",
        "ed" => ""
    ),
    "form_arrival_day"  => array("sy" => "", "sm" => "", "sd" => "", "ey" => "", "em" => "", "ed" => ""),
    "form_direct"       => "",
    "form_aord_no"      => array("s" => "", "e" => ""),
    "form_ord_no"       => array("s" => "", "e" => ""),
    "form_goods"        => array("cd" => "", "name" => ""),
);

// 検索条件復元
Restore_Filter2($form, array("sale", "aord"), "show_button", $ary_form_list);


/****************************/
// 外部変数取得
/****************************/
$shop_id    = $_SESSION["client_id"];


/****************************/
// 初期値設定
/****************************/
$total_count = 0;


/****************************/
// フォームパーツ定義
/****************************/
/* 共通フォーム */
Search_Form_Aord($db_con, $form, $ary_form_list);

/* モジュール別フォーム */
// 受注番号
Addelement_Slip_Range($form, "form_aord_no", "受注番号");

// 発注番号
Addelement_Slip_Range($form, "form_ord_no", "発注番号");

// 商品コード
$obj    =   null;
$obj[]  =&  $form->createElement("text", "cd", "", "size=\"10\" maxLength=\"8\" class=\"ime_disabled\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", " ");
$obj[]  =&  $form->createElement("text", "name", "", "size=\"34\" maxLength=\"30\" $g_form_option");
$form->addGroup($obj, "form_goods", "", "");

// ソートリンク
$ary_sort_item = array(
    "sl_client_cd"      => "得意先コード",
    "sl_client_name"    => "得意先名",
    "sl_h_aord_no"      => "本部受注番号",
    "sl_fc_ord_no"      => "FC発注番号",
    "sl_direct"         => "直送先",
    "sl_aord_day"       => "受注日",
    "sl_arrival_day"    => "出荷予定日",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_client_cd");

// 表示ボタン
$form->addElement("submit","show_button","表　示");

// クリア
$form->addElement("button", "clear_button", "クリア", "onClick=\"javascript:location.href('".$_SERVER["PHP_SELF"]."');\"");

// ヘッダ部リンクボタン
$ary_h_btn_list = array("照会・変更" => "./1-2-105.php", "入　力" => "./1-2-101.php", "受注残一覧" => $_SERVER["PHP_SELF"]);
Make_H_Link_Btn($form, $ary_h_btn_list);


/****************************/
// 表示ボタン押下処理
/****************************/
if ($_POST["show_button"] == "表　示"){

    // 日付POSTデータの0埋め
    $_POST["form_ord_day"]      = Str_Pad_Date($_POST["form_ord_day"]);
    $_POST["form_arrival_day"]  = Str_Pad_Date($_POST["form_arrival_day"]);

    /****************************/
    // エラーチェック
    /****************************/
    // ■受注担当者
    $err_msg = "受注担当者 は数値のみ入力可能です。";
    Err_Chk_Num($form, "form_c_staff", $err_msg);

    // ■受注担当複数選択
    $err_msg = "受注担当複数選択 は数値と「,」のみ入力可能です。";
    Err_Chk_Delimited($form, "form_multi_staff", $err_msg);

    // ■受注日
    $err_msg = "受注日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_aord_day", $err_msg);

    // ■出荷予定日
    $err_msg = "出荷予定日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_arrival_day", $err_msg);

    /****************************/
    // エラーチェック結果集計
    /****************************/
    // チェック適用
    $form->validate();
    // 結果をフラグに
    $err_flg = (count($form->_errors) > 0) ? true : false;

    $post_flg = ($err_flg != true) ? true : false;

}


/****************************/
// 1. 表示ボタン押下＋エラーなし時
// 2. ページ切り替え時
/****************************/
if (($_POST["show_button"] != null && $err_flg != true) || ($_POST != null && $_POST["show_button"] == null)){

    // 日付POSTデータの0埋め
    $_POST["form_ord_day"]      = Str_Pad_Date($_POST["form_ord_day"]);
    $_POST["form_arrival_day"]  = Str_Pad_Date($_POST["form_arrival_day"]);

    // 1. フォームの値を変数にセット
    // 2. SESSION（hidden用）の値（検索条件復元関数内でセット）を変数にセット
    // 一覧取得クエリ条件に使用
    $display_num    = $_POST["form_display_num"];
    $client_cd1     = $_POST["form_client"]["cd1"];
    $client_cd2     = $_POST["form_client"]["cd2"];
    $client_name    = $_POST["form_client"]["name"];
    $c_staff_cd     = $_POST["form_c_staff"]["cd"];
    $c_staff_select = $_POST["form_c_staff"]["select"];
    $ware           = $_POST["form_ware"];
    $claim_cd1      = $_POST["form_claim"]["cd1"];
    $claim_cd2      = $_POST["form_claim"]["cd2"];
    $claim_name     = $_POST["form_claim"]["name"];
    $multi_staff    = $_POST["form_multi_staff"];
    $aord_day_sy    = $_POST["form_aord_day"]["sy"];
    $aord_day_sm    = $_POST["form_aord_day"]["sm"];
    $aord_day_sd    = $_POST["form_aord_day"]["sd"];
    $aord_day_ey    = $_POST["form_aord_day"]["ey"];
    $aord_day_em    = $_POST["form_aord_day"]["em"];
    $aord_day_ed    = $_POST["form_aord_day"]["ed"];
    $arrival_day_sy = $_POST["form_arrival_day"]["sy"];
    $arrival_day_sm = $_POST["form_arrival_day"]["sm"];
    $arrival_day_sd = $_POST["form_arrival_day"]["sd"];
    $arrival_day_ey = $_POST["form_arrival_day"]["ey"];
    $arrival_day_em = $_POST["form_arrival_day"]["em"];
    $arrival_day_ed = $_POST["form_arrival_day"]["ed"];
    $direct_name    = $_POST["form_direct"];
    $aord_no_s      = $_POST["form_aord_no"]["s"];
    $aord_no_e      = $_POST["form_aord_no"]["e"];
    $ord_no_s       = $_POST["form_ord_no"]["s"];
    $ord_no_e       = $_POST["form_ord_no"]["e"];
    $goods_cd       = $_POST["form_goods"]["cd"];
    $goods_name     = $_POST["form_goods"]["name"];

    $post_flg = true;

}


/****************************/
// 一覧データ取得条件作成
/****************************/
if ($post_flg == true && $err_flg != true){

    $sql = null;

    // FC・取引先コード１
    $sql .= ($client_cd1 != null) ? "AND t_aorder_h.client_cd1 LIKE '$client_cd1%' \n" : null;
    // FC・取引先コード２
    $sql .= ($client_cd2 != null) ? "AND t_aorder_h.client_cd2 LIKE '$client_cd2%' \n" : null;
    // FC・取引先名
    if ($client_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_aorder_h.client_name  LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_aorder_h.client_name2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_aorder_h.client_cname LIKE '%$client_name%' \n";
        $sql .= "   ) \n";
    }
    // 受注担当者コード
    $sql .= ($c_staff_cd != null) ? "AND t_staff.charge_cd = '$c_staff_cd' \n" : null;
    // 受注担当者セレクト
    $sql .= ($c_staff_select != null) ? "AND t_staff.staff_id = $c_staff_select \n" : null;
    // 倉庫
    $sql .= ($ware != null) ? "AND t_aorder_h.ware_id = $ware \n" : null;
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
    // 受注担当複数選択
    if ($multi_staff != null){
        $ary_multi_staff = explode(",", $multi_staff);
        $sql .= "AND \n";
        $sql .= "   t_staff.charge_cd IN (";
        foreach ($ary_multi_staff as $key => $value){
            $sql .= "'".trim($value)."'";
            $sql .= ($key+1 < count($ary_multi_staff)) ? ", " : ") \n";
        }
    }
    // 受注日（開始）
    $aord_day_s = $aord_day_sy."-".$aord_day_sm."-".$aord_day_sd;
    $sql .= ($aord_day_s != "--") ? "AND '$aord_day_s 00:00:00' <= t_aorder_h.ord_time \n" : null;
    // 受注日（終了）
    $aord_day_e = $aord_day_ey."-".$aord_day_em."-".$aord_day_ed;
    $sql .= ($aord_day_e != "--") ? "AND t_aorder_h.ord_time <= '$aord_day_e 23:59:59' \n" : null;
    // 出荷予定日（開始）
    $arrival_day_s = $arrival_day_sy."-".$arrival_day_sm."-".$arrival_day_sd;
    $sql .= ($arrival_day_s != "--") ? "AND '$arrival_day_s' <= t_aorder_h.arrival_day \n" : null;
    // 出荷予定日（終了）
    $arrival_day_e = $arrival_day_ey."-".$arrival_day_em."-".$arrival_day_ed;
    $sql .= ($arrival_day_e != "--") ? "AND t_aorder_h.arrival_day <= '$arrival_day_e' \n" : null;
    // 直送先
    if ($direct_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_aorder_h.direct_name  LIKE '%$direct_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_aorder_h.direct_name2 LIKE '%$direct_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_aorder_h.direct_cname LIKE '%$direct_name%' \n";
        $sql .= "   ) \n";
    }
    // 本部受注番号（開始）
    $sql .= ($aord_no_s != null) ? "AND t_aorder_h.ord_no >= '".str_pad($aord_no_s, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // 本部受注番号（終了）
    $sql .= ($aord_no_e != null) ? "AND t_aorder_h.ord_no <= '".str_pad($aord_no_e, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // FC発注番号（開始）
    $sql .= ($ord_no_s != null) ? "AND t_order_h.ord_no >= '".str_pad($ord_no_s, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // FC発注番号（終了）
    $sql .= ($ord_no_e != null) ? "AND t_order_h.ord_no <= '".str_pad($ord_no_e, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // 商品コード
    $sql .= ($goods_cd != null) ? "AND t_aorder_d.goods_cd LIKE '$goods_cd%' \n" : null;
    // 商品名
    $sql .= ($goods_name != null) ? "AND t_aorder_d.official_goods_name LIKE '%$goods_name%' \n" : null;

    // 変数詰め替え
    $where_sql = $sql;


    $sql = null;

    // ソート順
    switch ($_POST["hdn_sort_col"]){
        // 得意先コード
        case "sl_client_cd":
            $sql .= "   t_aorder_h.client_cd1, \n";
            $sql .= "   t_aorder_h.client_cd2, \n";
            $sql .= "   t_aorder_h.ord_no, \n";
            $sql .= "   t_aorder_h.ord_time, \n";
            $sql .= "   t_aorder_h.arrival_day, \n";
            $sql .= "   t_aorder_d.line \n";
            break;
        // 得意先名
        case "sl_client_name":
            $sql .= "   t_aorder_h.client_cname, \n";
            $sql .= "   t_aorder_h.client_cd1, \n";
            $sql .= "   t_aorder_h.client_cd2, \n";
            $sql .= "   t_aorder_h.ord_no, \n";
            $sql .= "   t_aorder_h.ord_time, \n";
            $sql .= "   t_aorder_h.arrival_day, \n";
            $sql .= "   t_aorder_d.line \n";
            break;
        // 本部受注番号
        case "sl_h_aord_no":
            $sql .= "   t_aorder_h.ord_no, \n";
            $sql .= "   t_aorder_h.client_cd1, \n";
            $sql .= "   t_aorder_h.client_cd2, \n";
            $sql .= "   t_aorder_h.ord_time, \n";
            $sql .= "   t_aorder_h.arrival_day, \n";
            $sql .= "   t_aorder_d.line \n";
            break;
        // FC発注番号
        case "sl_fc_ord_no":
            $sql .= "   t_order_h.ord_no, \n";
            $sql .= "   t_aorder_h.client_cd1, \n";
            $sql .= "   t_aorder_h.client_cd2, \n";
            $sql .= "   t_aorder_h.ord_no, \n";
            $sql .= "   t_aorder_h.ord_time, \n";
            $sql .= "   t_aorder_h.arrival_day, \n";
            $sql .= "   t_aorder_d.line \n";
            break;
        // 直送先
        case "sl_direct":
            $sql .= "   t_aorder_h.direct_cname, \n";
            $sql .= "   t_aorder_h.client_cd1, \n";
            $sql .= "   t_aorder_h.client_cd2, \n";
            $sql .= "   t_aorder_h.ord_no, \n";
            $sql .= "   t_aorder_h.ord_time, \n";
            $sql .= "   t_aorder_h.arrival_day, \n";
            $sql .= "   t_aorder_d.line \n";
            break;
        // 受注日
        case "sl_aord_day":
            $sql .= "   t_aorder_h.ord_time, \n";
            $sql .= "   t_aorder_h.client_cd1, \n";
            $sql .= "   t_aorder_h.client_cd2, \n";
            $sql .= "   t_aorder_h.ord_no, \n";
            $sql .= "   t_aorder_h.arrival_day, \n";
            $sql .= "   t_aorder_d.line \n";
            break;
        // 出荷予定日
        case "sl_arrival_day":
            $sql .= "   t_aorder_h.arrival_day, \n";
            $sql .= "   t_aorder_h.client_cd1, \n";
            $sql .= "   t_aorder_h.client_cd2, \n";
            $sql .= "   t_aorder_h.ord_no, \n";
            $sql .= "   t_aorder_h.ord_time, \n";
            $sql .= "   t_aorder_d.line \n";
            break;
    }

    // 変数詰め替え
    $order_sql = $sql;

}


/****************************/
// 出力する伝票をリストアップ
/****************************/
if ($post_flg == true && $err_flg != true){

    $sql  = "SELECT \n";
    $sql .= "   t_aorder_h.aord_id \n";
    $sql .= "FROM \n";
    $sql .= "   t_aorder_h \n";
    $sql .= "   INNER JOIN t_aorder_d                 ON t_aorder_h.aord_id = t_aorder_d.aord_id \n";
    $sql .= "   LEFT  JOIN t_order_h                  ON t_aorder_h.fc_ord_id = t_order_h.ord_id \n";
    $sql .= "   INNER JOIN t_client                   ON t_aorder_h.client_id = t_client.client_id \n";
    $sql .= "   INNER JOIN t_ware                     ON t_aorder_h.ware_id = t_ware.ware_id \n";
    $sql .= "   INNER JOIN t_goods                    ON t_aorder_d.goods_id = t_goods.goods_id \n";
    $sql .= "   LEFT  JOIN t_staff                    ON t_aorder_h.c_staff_id = t_staff.staff_id \n";
    $sql .= "   LEFT  JOIN t_client AS t_client_claim ON t_aorder_h.claim_id = t_client_claim.client_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_aorder_h.shop_id = $shop_id \n";
    $sql .= "AND \n";
    $sql .= "   t_aorder_h.ps_stat IN ('1', '2') \n";
    $sql .= $where_sql;
    $sql .= "GROUP BY \n";
    $sql .= "   t_aorder_h.aord_id \n";

    // 全件数取得
    $res            = Db_Query($db_con, $sql.";");
    $total_count    = pg_num_rows($res);

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

    // ページ内データ取得
    $limit_offset   = ($limit != null) ? "LIMIT $limit OFFSET $offset " : null; 
    $res            = Db_Query($db_con, $sql.$limit_offset.";");
    $match_count    = pg_num_rows($res);
    $ary_list_data  = Get_Data($res, 2, "ASSOC");

}


/****************************/
// 発注残データ取得SQL
/****************************/
if ($match_count > 0 && $post_flg == true && $err_flg != true){

    $sql  = "SELECT \n";
    $sql .= "   t_aorder_h.client_cd1, \n";
    $sql .= "   t_aorder_h.client_cd2, \n";
    $sql .= "   t_aorder_h.client_cname, \n";
    $sql .= "   t_aorder_h.aord_id, \n";
    $sql .= "   t_aorder_h.ord_no AS aord_no, \n";
    $sql .= "   t_order_h.ord_no, \n";
    $sql .= "   to_char(t_aorder_h.ord_time, 'yyyy-mm-dd') AS aord_day, \n";
    $sql .= "   t_aorder_h.arrival_day, \n";
    $sql .= "   t_aorder_d.goods_cd, \n";
    $sql .= "   t_aorder_d.official_goods_name, \n";
    $sql .= "   t_aorder_d.num AS aorder_num, \n";
    $sql .= "   COALESCE(t_sale_d.sale_num,0) AS sale_num, \n";
    $sql .= "   t_aorder_d.num - COALESCE(t_sale_d.sale_num,0) AS backlog_num, \n";
    $sql .= "   t_aorder_h.ware_name, \n";
    $sql .= "   t_aorder_h.check_flg, \n";
    $sql .= "   t_aorder_h.fc_ord_id, \n";
    $sql .= "   t_aorder_h.ps_stat, \n";
    $sql .= "   t_aorder_h.check_user_name, \n";
    $sql .= "   t_aorder_h.direct_cname \n";
    $sql .= "FROM \n";
    $sql .= "   t_aorder_h \n";
    $sql .= "   LEFT  JOIN t_order_h  ON t_order_h.ord_id = t_aorder_h.fc_ord_id \n";
    $sql .= "   INNER JOIN t_aorder_d ON t_aorder_h.aord_id = t_aorder_d.aord_id \n";
    $sql .= "   LEFT  JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_sale_d.aord_d_id, \n";
    $sql .= "           SUM(t_sale_d.num) AS sale_num \n";
    $sql .= "       FROM \n";
    $sql .= "           t_sale_h \n";
    $sql .= "           INNER JOIN t_sale_d ON t_sale_h.sale_id = t_sale_d.sale_id \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_sale_h.shop_id = 1 \n";
    $sql .= "       GROUP BY \n";
    $sql .= "           t_sale_d.aord_d_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_sale_d \n";
    $sql .= "   ON t_aorder_d.aord_d_id = t_sale_d.aord_d_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_aorder_h.aord_id IN (";
    foreach ($ary_list_data as $key => $value){
    $sql .= $value["aord_id"];
    $sql .= ($key+1 < count($ary_list_data)) ? ", " : ") \n";
    }
    $sql .= "ORDER BY \n";
    $sql .= $order_sql;
    $sql .= ";";

    $res  = Db_Query($db_con, $sql);

    // ページ内データ取得
    $res        = Db_Query($db_con, $sql);
    $ary_data   = Get_Data($res, 2, "ASSOC");

}


/****************************/
// 表示用データ作成
/****************************/
if ($post_flg == true && $err_flg != true){

    /****************************/
    // 表示用データ整形
    /****************************/
    // ページ内データでループ
    if (count($ary_data) > 0){
        // No.初期値
        $i = 0;
        // 行数初期値
        $row = 0;
        // 
        $html_l = null;
        foreach ($ary_data as $key => $value){

            // 前・次の参照行を変数に入れて使いやすくしておく
            $back = $ary_data[$key-1];
            $next = $ary_data[$key+1];

            // ■No.の出力設定
            // 配列の最初、または前回と今回の発注IDが異なる場合
            if ($key == 0 || $back["aord_id"] != $value["aord_id"]){
                $no             = ++$i;
            }else{
                $no             = null;
            }
            // ■FC・取引先
            // No.がある場合
            if ($no != null){
                $client         = $value["client_cd1"]."-".$value["client_cd2"];
                $client        .= "<br>";
                $client        .= htmlspecialchars($value["client_cname"]);
                $client        .= "<br>";
            }else{
                $client         = null;
            }
            // ■本部受注番号リンク
            // No.がある場合
            if ($no != null){
                // FC発注IDがある場合
                if ($value["fc_ord_id"] == null){
                    $aord_no_link   = "<a href=\"./1-2-101.php?aord_id=".$value["aord_id"]."\">".$value["aord_no"]."</a>";
                }else{
                    $aord_no_link   = "<a href=\"./1-2-110.php?aord_id=".$value["aord_id"]."\">".$value["aord_no"]."</a>";
                }
            }else{
                $aord_no_link    = null;
            }
            // ■FC発注番号
            if ($no != null){
                $ord_no         = $value["ord_no"];
            }else{
                $ord_no         = null;
            }
            // ■直送先
            if ($no != null){
                $direct         = htmlspecialchars($value["direct_cname"]);
            }else{
                $direct         = null;
            }
            // ■受注日
            if ($no != null){
                $aord_day       = $value["aord_day"];
            }else{
                $aord_day       = null;
            }
            // ■出荷予定日
            if ($no != null){
                $arrival_day    = $value["arrival_day"];
            }else{
                $arrival_day    = null;
            }
            // ■商品
            $goods              = $value["goods_cd"]."<br>".htmlspecialchars($value["official_goods_name"])."<br>";
            // ■受注数
            $aorder_num         = $value["aorder_num"];
            // ■出荷数
            $sale_num           = $value["sale_num"];
            // ■受注残
            $backlog_num        = $value["backlog_num"];
            // ■倉庫
            $ware_name          = htmlspecialchars($value["ware_name"]);
            // ■売上リンク
            if ($no != null){
                $sale_link      = "<a href=\"./1-2-201.php?aord_id=".$value["aord_id"]."\">入力</a>";
            }else{
                $sale_link      = null;
            }
            // ■行色css
            if ($no != null){
                $css            = (bcmod($no, 2) == 0) ? "Result2" : "Result1";
            }else{
                $css            = $css;
            }

            // ■まとめ
            // 行色css
            $disp_data[$row]["css"]             = $css;
            // No.
            $disp_data[$row]["no"]              = $no;
            // FC・取引先
            $disp_data[$row]["client"]          = $client;
            // 本部受注番号リンク
            $disp_data[$row]["aord_no_link"]    = $aord_no_link;
            // FC発注番号リンク
            $disp_data[$row]["ord_no"]          = $ord_no;
            // 直送先
            $disp_data[$row]["direct"]          = $direct;
            // 受注日
            $disp_data[$row]["aord_day"]        = $aord_day;
            // 出荷予定日
            $disp_data[$row]["arrival_day"]     = $ord_no_link;
            // 商品
            $disp_data[$row]["goods"]           = $goods;
            // 受注数
            $disp_data[$row]["aorder_num"]      = $aorder_num;
            // 出荷数
            $disp_data[$row]["sale_num"]        = $sale_num;
            // 受注残
            $disp_data[$row]["backlog_num"]     = $backlog_num;
            // 倉庫
            $disp_data[$row]["ware_name"]       = $ware_name;
            // 売上リンク
            $disp_data[$row]["sale_link"]       = $sale_link;

            // 一覧html作成
            $html_l .= "    <tr class=\"$css\">\n";
            $html_l .= "        <td align=\"right\">$no</td>\n";
            $html_l .= "        <td>$client</td>\n";
            $html_l .= "        <td align=\"center\">$aord_no_link</td>\n";
            $html_l .= "        <td align=\"center\">$ord_no</td>\n";
            $html_l .= "        <td>$direct</td>\n";
            $html_l .= "        <td align=\"center\">$aord_day</td>\n";
            $html_l .= "        <td align=\"center\">$arrival_day</td>\n";
            $html_l .= "        <td>$goods</td>\n";
            $html_l .= "        <td align=\"right\">$aorder_num</td>\n";
            $html_l .= "        <td align=\"right\">$sale_num</td>\n";
            $html_l .= "        <td align=\"right\">$backlog_num</td>\n";
            $html_l .= "        <td>$ware_name</td>\n";
            $html_l .= "        <td align=\"center\" class=\"color\">$sale_link</td>\n";
            $html_l .= "    </tr>\n";

            // 行数加算
            $row++;

        }
    }

}


/****************************/
// HTML作成（検索部）
/****************************/
// 共通検索テーブル
$html_s .= Search_Table_Aord($form);
$html_s .= "<br style=\"font-size: 4px;\">\n";
// モジュール個別検索テーブル１
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\"100px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <col width=\"130px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">本部受注番号</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_aord_no"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">FC発注番号</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_ord_no"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
$html_s .= "<br style=\"font-size: 4px;\">\n";
// モジュール個別検索テーブル２
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\"100px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">商品</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_goods"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
$html_s .= "<br style=\"font-size: 4px;\">\n";
// ボタン
$html_s .= "<table align=\"right\"><tr><td>\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["show_button"]]->toHtml()."　\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["clear_button"]]->toHtml()."\n";
$html_s .= "</td></tr></table>";
$html_s .= "\n";

$html_page  = Html_Page2($total_count, $page_count, 1, $limit);
$html_page2 = Html_Page2($total_count, $page_count, 2, $limit);


/****************************/
// HTMLヘッダ
/****************************/
$html_header = Html_Header($page_title);

/****************************/
// HTMLフッタ
/****************************/
$html_footer = Html_Footer();

/****************************/
// メニュー作成
/****************************/
$page_menu = Create_Menu_h("sale", "1");

/****************************/
// 画面ヘッダー作成
/****************************/
$page_title .= Print_H_Link_Btn($form, $ary_h_btn_list);
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
    "html_page"     => "$html_page",
    "html_page2"    => "$html_page2",
    "total_count"   => "$total_count",
    "post_flg"      => "$post_flg",
    "err_flg"       => "$err_flg",
));

$smarty->assign("row", $row_data);

$smarty->assign("html", array(
    "html_s"        => $html_s,
    "html_l"        => $html_l,
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER["PHP_SELF"].".tpl"));

?>
