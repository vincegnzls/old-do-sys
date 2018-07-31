<?php
/******************************
 *変更履歴
 *      ・ページ分けをなくす。
 *      ・権限チェック追加
******************************/
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/12　06-011　　　　watanabe-k　発注データIDが４桁以上だとSQLエラーが表示されるバグの修正
 *   2006/10/16  06-012        watanabe-k　年月日の入力チェックで文字列が入力された場合が考慮されていないバグの修正
 *   2006/10/16  06-084        watanabe-k　商品名がナンバーフォーマットされている
 */

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/13      06-086      ふ          仕入完了済発注データの強制完了を行わないよう修正
 *  2006/11/13      06-113      ふ          チェックボックスと発注データIDの同期をとるよう修正
 *  2006/11/13      06-121      ふ          削除または変更されている発注データに強制完了できるバグを修正
 *  2006/11/13      06-117      ふ          全○件の件数表示が間違えているのを修正
 *  2006/12/06      ban_0031    suzuki      日付にゼロ埋め追加
 *  2007/01/24      仕様変更  　watanabe-k  ボタンの色変更 
 *  2007/02/06                  watanabe-k  発信日を表示しないように変更
 *  2007-04-04                  fukuda      検索条件復元処理追加
 *  2009-10-16                  hashimoto-y 検索フォームの発注日の初期値を空値に変更
 *
 *
 */

$page_title = "発注残一覧";

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
$auth       = Auth_Check($db_con);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/****************************/
// 検索条件復元関連
/****************************/
// 検索フォーム初期値配列
$ary_form_list = array(
    "form_display_num"  => "1", 
    "form_client"       => array("cd1" => "", "cd2" => "", "name" => ""), 
    "form_c_staff"      => array("cd" => "", "select" => ""), 
    "form_ware"         => "",  
    #2009-10-16 hashimoto-y
    #"form_ord_day"      => array(
    #    "sy" => date("Y"),
    #    "sm" => date("m"),
    #    "sd" => "01",
    #    "ey" => date("Y"),
    #    "em" => date("m"),
    #    "ed" => date("t", mktime(0, 0, 0, date("m"), date("d"), date("Y")))
    #),
    "form_ord_day"      => array(
        "sy" => "",
        "sm" => "",
        "sd" => "",
        "ey" => "",
        "em" => "",
        "ed" => ""
    ),
    "form_multi_staff"  => "",  
    "form_ord_no"       => array("s" => "", "e" => ""), 
    //"form_arrival_day"  => array("sy" => "", "sm" => "", "sd" => "", "ey" => "", "em" => "", "ed" => ""), 
    "form_hope_day"     => array("sy" => "", "sm" => "", "sd" => "", "ey" => "", "em" => "", "ed" => ""), 
    "form_direct"       => "",
    "form_goods"        => array("cd" => "", "name" => ""),
);

// 再遷移時に復元を除外するフォーム配列
$ary_pass_list = array(
    "ord_comp_button_flg"  => "",
);

// 検索条件復元
Restore_Filter2($form, array("buy", "ord"), "show_button", $ary_form_list, $ary_pass_list);


/****************************/
//外部変数取得
/****************************/
$shop_id    = $_SESSION["client_id"];
$staff_id   = $_SESSION["staff_id"];


/****************************/
// 初期値設定
/****************************/
$total_count = "0";


/****************************/
// フォームパーツ定義
/****************************/
/* 共通フォーム */
Search_Form_Ord_H($db_con, $form, $ary_form_list);

/* モジュール別フォーム */
// 商品コード
$obj    =   null;
$obj[]  =&  $form->createElement("text", "cd", "", "class=\"ime_disabled\"  size=\"10\" maxLength=\"8\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", " ");
$obj[]  =&  $form->createElement("text", "name", "", "size=\"34\" maxLength=\"30\" ".$g_form_option."\"");
$form->addGroup($obj, "form_goods", "", "");

// ソートリンク
$ary_sort_item = array(
    "sl_ord_day"        => "発注日",
    "sl_input_day"      => "入力日",
    "sl_client_cd"      => "発注先コード",
    "sl_client_name"    => "発注先名",
    "sl_ord_no"         => "発注番号",
    "sl_hope_day"       => "希望納期",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_ord_day");

// 表示ボタン
$form->addElement("submit", "show_button", "表　示");

//クリアボタン
$form->addElement("button", "clear_button", "クリア", "onClick=\"javascript:location.href('".$_SERVER["PHP_SELF"]."');\"");

// 発注完了ボタン
$form->addElement("button", "ord_comp_button", "発注完了",
    "onClick=\"javascript:Dialogue_2('発注完了します。', '".$_SERVER["PHP_SELF"]."', 'true', 'ord_comp_button_flg')\" $disabled"
);

// ヘッダ部リンクボタン
$form->addElement("button", "104_button", "照会・変更", "onClick=\"javascript:Referer('1-3-104.php');\"");
$form->addElement("button", "102_button", "入　力",     "onClick=\"javascript:Referer('1-3-102.php');\"");
$form->addElement("button", "106_button", "発注残一覧", "$g_button_color onClick=\"location.href='".$_SERVER["PHP_SELF"]."'\"");

// 処理フラグ
$form->addElement("hidden", "ord_comp_button_flg");

// エラーメッセージ埋め込み用フォーム
$form->addElement("text", "err_non_buy");       // 仕入が起こっていない
$form->addElement("text", "err_non_check");     // 強制完了チェックが1つも無い
$form->addElement("text", "err_bought_slip");   // 仕入が完了している
$form->addElement("text", "err_non_reason");    // 理由未入力
$form->addElement("text", "err_valid_data");    // 正当でない（変更/削除された）発注データ


/******************************/
// 発注完了ボタン押下処理
/*****************************/
if($_POST["ord_comp_button_flg"]== "true"){

    $row_num    = $_POST["close_ord_d_id"];         //発注完了チェック行番号
    $reason     = $_POST["form_reason"];            //強制完了理由
    $ord_err_flg= false;                            //エラー判定フラグ

    /****************************/
    //エラーチェック(PHP)
    /****************************/
    //◇発注完了チェックボックス
    //・値の存在チェック
    if($row_num == null){
        $form->setElementError("err_non_check", "発注完了する商品を選択して下さい。");
        $ord_err_flg = true;
    }

    //発注データID取得
    for($c=0; $c<count($_POST["hdn_ord_d_id"]); $c++){

        //チェックが付いている行の発注データIDと理由と商品IDを取得
        if($row_num[$c] == 1){

            // チェックされた発注が正当か調べる（伝票作成日時を元に）
            $sql  = "SELECT * FROM t_order_h \n";
            $sql .= "INNER JOIN t_order_d ON t_order_h.ord_id = t_order_d.ord_id \n";
            $sql .= "WHERE t_order_d.ord_d_id = ".$_POST["hdn_ord_d_id"][$c]." \n";
            $sql .= "AND t_order_h.enter_day = '".$_POST["hdn_enter_day"][$c]."' \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);

            // 正当な場合のみ以下の処理を行う
            if (pg_num_rows($res) > 0){

                // 仕入を全く起こしていない発注データは、強制完了させない
                $sql  = "SELECT COUNT(ord_id) FROM t_buy_h \n";
                $sql .= "WHERE ord_id = (SELECT ord_id FROM t_order_d WHERE ord_d_id = ".$_POST["hdn_ord_d_id"][$c].") \n";
                $sql .= ";";
                $res = Db_Query($db_con, $sql);
                $num = pg_fetch_result($res, 0, 0);
                if ($num == 0){
                    $form->setElementError("err_non_buy", "仕入を行っていない発注は完了できません。");
                    $ord_err_flg = true;
                }

                // 仕入済で発注残に残っていないデータは強制完了させない
                $sql  = "SELECT * FROM t_order_d WHERE rest_flg = 'f' AND ord_d_id = ".$_POST["hdn_ord_d_id"][$c].";";
                $res  = Db_Query($db_con, $sql);
                if (pg_num_rows($res) > 0){
                    $form->setElementError("err_bought_slip", "仕入が完了している発注は完了できません。");
                    $ord_err_flg = true;
                }

                //◇強制完了理由
                //・必須入力チェック
                if($reason[$c] == null){
                    $form->setElementError("err_non_reason", "発注完了理由は必須入力です。");
                    $ord_err_flg = true;
                }

                $reason_data[] = $reason[$c];                   //理由
                $ord_d_id[]    = $_POST["hdn_ord_d_id"][$c];    //発注ID
                // ord_d_idの商品IDを取得
                $sql = "SELECT goods_id FROM t_order_d WHERE ord_d_id = ".$_POST["hdn_ord_d_id"][$c].";";
                $res = Db_Query($db_con, $sql);
                $goods_id[]    = pg_fetch_result($res, 0, 0);

            // 正当でない（発注データが削除されている）発注データが選択された場合
            }else{

                $form->setElementError("err_valid_data", "変更または削除されている発注データが選択されたため、完了できません。");
                $ord_err_flg = true; 

            }

        }

    }

    //エラーの際には、処理を行わない
    if($ord_err_flg != true){

        Db_Query($db_con, "BEGIN;");

        for($i=0;$i<count($ord_d_id);$i++){
            //発注ヘッダテーブルの情報を更新
            $sql  = "UPDATE";
            $sql .= "    t_order_h ";
            $sql .= "SET";
            $sql .= "    finish_flg = 't', ";                     //強制完了フラグ
            $sql .= "    change_day = CURRENT_TIMESTAMP ";
            $sql .= "WHERE";
            $sql .= "    ord_id = (";
            $sql .= "        SELECT ";
            $sql .= "            ord_id ";
            $sql .= "        FROM ";
            $sql .= "            t_order_d ";
            $sql .= "        WHERE ";
            $sql .= "            ord_d_id = ".$ord_d_id[$i];
            $sql .= "        );";
            $result = Db_Query($db_con,$sql);
            if($result == false){
                Db_Query($db_con,"ROLLBACK;");
                exit;
            }

            //発注データテーブルの情報を更新
            $sql  = "UPDATE";
            $sql .= "    t_order_d ";
            $sql .= "SET";
            $sql .= "    rest_flg   = 'f', ";                   //発注残フラグ
            $sql .= "    finish_flg = 't', ";                   //強制完了フラグ
            $sql .= "    reason     = '".$reason_data[$i]."' "; //強制完了理由
            $sql .= "WHERE";
            $sql .= "   ord_d_id = ".$ord_d_id[$i]."";

            $result = Db_Query($db_con,$sql);
            if($result == false){
                Db_Query($db_con,"ROLLBACK;");
                exit;
            }
            //在庫受払テーブルの情報を登録
            $sql  = "INSERT INTO";
            $sql .= "    t_stock_hand (";
            $sql .= "        goods_id,";              //商品ID
            $sql .= "        enter_day,";             //入力日
            $sql .= "        work_day,";              //作業実施日
            $sql .= "        work_div,";              //作業区分
            $sql .= "        client_id,";             //得意先ID
            $sql .= "        ware_id,";               //倉庫ID
            $sql .= "        io_div,";                //入出庫区分
            $sql .= "        num,";                   //個数
            $sql .= "        slip_no,";               //伝票番号
            $sql .= "        ord_d_id,";              //発注データID
            $sql .= "        staff_id,";              //作業者
            $sql .= "        shop_id) ";              //ショップ識別ID
            $sql .= "VALUES(";
            $sql .= "    (SELECT ";
            $sql .= "        goods_id ";
            $sql .= "    FROM ";
            $sql .= "        t_order_d ";
            $sql .= "    WHERE ";
            $sql .= "        ord_d_id = ".$ord_d_id[$i];
            $sql .= "    ),";
            $sql .= "    (SELECT ";
            $sql .= "        CURRENT_DATE";
            $sql .= "    ),";
            $sql .= "    (SELECT ";
            $sql .= "        CURRENT_DATE";
            $sql .= "    ),";
            $sql .= "    3,";
            $sql .= "    (SELECT ";
            $sql .= "        client_id ";
            $sql .= "    FROM ";
            $sql .= "        t_order_h ";
            $sql .= "    INNER JOIN t_order_d ON t_order_h.ord_id = t_order_d.ord_id ";
            $sql .= "    WHERE ";
            $sql .= "        t_order_d.ord_d_id = ".$ord_d_id[$i];
            $sql .= "    ),";
            $sql .= "    (SELECT ";
            $sql .= "        ware_id ";
            $sql .= "    FROM ";
            $sql .= "        t_order_h ";
            $sql .= "    INNER JOIN t_order_d ON t_order_h.ord_id = t_order_d.ord_id  ";
            $sql .= "    WHERE ";
            $sql .= "        t_order_d.ord_d_id = ".$ord_d_id[$i];
            $sql .= "    ),";
            $sql .= "    2,";
            $sql .= "    (SELECT ";
            $sql .= "        t_order_d.num - COALESCE(t_buy.buy_num,0) AS inventory_num ";
            $sql .= "    FROM ";
            $sql .= "        t_order_d ";
            $sql .= "    LEFT JOIN ";
            $sql .= "        (SELECT ";
            $sql .= "            ord_d_id,";
            $sql .= "            SUM(num) AS buy_num";
            $sql .= "        FROM ";
            $sql .= "            t_buy_d ";
            $sql .= "        GROUP BY ";
            $sql .= "            ord_d_id ";
            $sql .= "        )AS t_buy";
            $sql .= "    ON t_order_d.ord_d_id = t_buy.ord_d_id ";
            $sql .= "    WHERE ";
            $sql .= "        t_order_d.ord_d_id = ".$ord_d_id[$i];
            $sql .= "    ),";
            $sql .= "    (SELECT ";
            $sql .= "        ord_no ";
            $sql .= "    FROM ";
            $sql .= "        t_order_h ";
            $sql .= "    INNER JOIN t_order_d ON t_order_h.ord_id = t_order_d.ord_id ";
            $sql .= "    WHERE ";
            $sql .= "        t_order_d.ord_d_id = ".$ord_d_id[$i];
            $sql .= "    ),";
            $sql .= "    ".$ord_d_id[$i].",";
            $sql .= "    ".$staff_id.",";
            $sql .= "    ".$shop_id;
            $sql .= ");";
            $result = Db_Query($db_con,$sql);
            if($result == false){
                Db_Query($db_con,"ROLLBACK;");
                exit;
            }
        }
        //発注ヘッダテーブルの情報を更新
        $sql  = "SELECT ";
        $sql .= "DISTINCT ";
        $sql .= "    rest_flg ";
        $sql .= "FROM";
        $sql .= "    t_order_d ";                         //処理状況
        $sql .= "WHERE";
        $sql .= "    ord_id = (";
        $sql .= "        SELECT ";
        $sql .= "            ord_id ";
        $sql .= "        FROM ";
        $sql .= "            t_order_d ";
        $sql .= "        WHERE ";
        $sql .= "            ord_d_id = ".$ord_d_id[0];
        $sql .= "        );";
        $result = Db_Query($db_con,$sql);
        //全ての発注データの発注残フラグがfalseか
        $rest_count = pg_num_rows($result);
        if($rest_count==1){
            //発注残フラグにfalseしか存在しない
            $ps_stat = "    ps_stat = '3' ";             //処理状況（処理完了）
        }else{
            //発注残フラグにtrueとfalseが存在する
            $ps_stat = "    ps_stat = '2' ";             //処理状況（処理中）
        }

        //発注ヘッダテーブルの情報を更新
        $sql  = "UPDATE";
        $sql .= "    t_order_h ";
        $sql .= "SET";
        $sql .= $ps_stat;
        $sql .= "WHERE";
        $sql .= "    ord_id = (";
        $sql .= "        SELECT ";
        $sql .= "            ord_id ";
        $sql .= "        FROM ";
        $sql .= "            t_order_d ";
        $sql .= "        WHERE ";
        $sql .= "            ord_d_id = ".$ord_d_id[0];
        $sql .= "        );";
        $result = Db_Query($db_con,$sql);
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            exit;
        }
        Db_Query($db_con, "COMMIT;");
        //更新
        header("Location: 1-3-106.php?search=1");

    }

    //発注完了フラグを初期化
    $Cons_date = array(
        "ord_comp_button_flg"   => ""
    );
    $form->setConstants($Cons_date);

    // hiddenの発注データIDと伝票作成日時をクリア
    if (count($_POST["hdn_ord_d_id"]) > 0){
    foreach ($_POST["hdn_ord_d_id"] as $key => $value){
        $clear_hdn_ord_d_id["hdn_ord_d_id"][$key]   = "";
        $clear_hdn_ord_d_id["hdn_enter_day"][$key]  = "";
    }
    }

    $post_flg = true;

}


/****************************/
// 表示ボタン押下処理
/****************************/
if ($_POST["show_button"] != null){

    // 日付POSTデータの0埋め
    $_POST["form_ord_day"]  = Str_Pad_Date($_POST["form_ord_day"]);
    //$_POST["form_arrival_day"]  = Str_Pad_Date($_POST["form_arrival_day"]);
    $_POST["form_hope_day"] = Str_Pad_Date($_POST["form_hope_day"]);

    /****************************/
    // エラーチェック
    /****************************/
    // ■発注担当者
    $err_msg = "発注担当者 は数値のみ入力可能です。";
    Err_Chk_Num($form, "form_c_staff", $err_msg);

    // ■発注日
    $err_msg = "発注日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_ord_day", $err_msg);

    // ■発注担当複数選択
    $err_msg = "発注担当複数選択 は数値と「,」のみ入力可能です。";
    Err_Chk_Delimited($form, "form_multi_staff", $err_msg);
/*
    // ■入荷予定日
    $err_msg = "入荷予定日 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_arrival_day", $err_msg);
*/
    // ■希望納期
    $err_msg = "希望納期 の日付が妥当ではありません。";
    Err_Chk_Date($form, "form_hope_day", $err_msg);

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
    $_POST["form_ord_day"]  = Str_Pad_Date($_POST["form_ord_day"]);
    //$_POST["form_arrival_day"]  = Str_Pad_Date($_POST["form_arrival_day"]);
    $_POST["form_hope_day"] = Str_Pad_Date($_POST["form_hope_day"]);

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
    $ord_day_sy     = $_POST["form_ord_day"]["sy"];
    $ord_day_sm     = $_POST["form_ord_day"]["sm"];
    $ord_day_sd     = $_POST["form_ord_day"]["sd"];
    $ord_day_ey     = $_POST["form_ord_day"]["ey"];
    $ord_day_em     = $_POST["form_ord_day"]["em"];
    $ord_day_ed     = $_POST["form_ord_day"]["ed"];
    $multi_staff    = $_POST["form_multi_staff"];
    $ord_no_s       = $_POST["form_ord_no"]["s"];
    $ord_no_e       = $_POST["form_ord_no"]["e"];
/*
    $arr_day_sy     = $_POST["form_arrival_day"]["sy"];
    $arr_day_sm     = $_POST["form_arrival_day"]["sm"];
    $arr_day_sd     = $_POST["form_arrival_day"]["sd"];
    $arr_day_ey     = $_POST["form_arrival_day"]["ey"];
    $arr_day_em     = $_POST["form_arrival_day"]["em"];
    $arr_day_ed     = $_POST["form_arrival_day"]["ed"];
*/
    $hope_day_sy    = $_POST["form_hope_day"]["sy"];
    $hope_day_sm    = $_POST["form_hope_day"]["sm"];
    $hope_day_sd    = $_POST["form_hope_day"]["sd"];
    $hope_day_ey    = $_POST["form_hope_day"]["ey"];
    $hope_day_em    = $_POST["form_hope_day"]["em"];
    $hope_day_ed    = $_POST["form_hope_day"]["ed"];
    $direct_name    = $_POST["form_direct"];
    $goods_cd       = $_POST["form_goods"]["cd"];
    $goods_name     = $_POST["form_goods"]["name"];

    $post_flg = true;

}


/****************************/
// 一覧データ取得条件作成
/****************************/
if ($post_flg == true && $err_flg != true){

    $sql = null;

    // 発注先コード１
    $sql .= ($client_cd1 != null) ? "AND t_order_h.client_cd1 LIKE '$client_cd1%' \n" : null;
    // 発注先コード２
    $sql .= ($client_cd2 != null) ? "AND t_order_h.client_cd2 LIKE '$client_cd2%' \n" : null;
    // 発注先名
    if ($client_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_order_h.client_name  LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_order_h.client_name2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_order_h.client_cname LIKE '%$client_name%' \n";
        $sql .= "   ) \n";
    }
    // 発注担当者コード
    $sql .= ($c_staff_cd != null) ? "AND t_staff.charge_cd = '$c_staff_cd' \n" : null;
    // 発注担当者セレクト
    $sql .= ($c_staff_select != null) ? "AND t_order_h.c_staff_id = $c_staff_select \n" : null;
    // 倉庫
    $sql .= ($ware != null) ? "AND t_order_h.ware_id = $ware \n" : null;
    // 発注日（開始）
    $ord_day_s = $ord_day_sy."-".$ord_day_sm."-".$ord_day_sd;
    $sql .= ($ord_day_s != "--") ? "AND t_order_h.ord_time >= '$ord_day_s 00:00:00' \n" : null;
    // 発注日（終了）
    $ord_day_e = $ord_day_ey."-".$ord_day_em."-".$ord_day_ed;
    $sql .= ($ord_day_e != "--") ? "AND t_order_h.ord_time <= '$ord_day_e 23:59:59' \n" : null;
    // 発注担当複数選択
    if ($multi_staff != null){
        $ary_multi_staff = explode(",", $multi_staff);
        $sql .= "AND \n";
        $sql .= "   t_staff.charge_cd IN (";
        foreach ($ary_multi_staff as $key => $value){
            $sql .= "'".trim($value)."'";
            $sql .= ($key+1 < count($ary_multi_staff)) ? ", " : ") \n";
        }
    }
    // 発注番号（開始）
    $sql .= ($ord_no_s != null) ? "AND t_order_h.ord_no >= '".str_pad($ord_no_s, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // 発注番号（終了）
    $sql .= ($ord_no_e != null) ? "AND t_order_h.ord_no <= '".str_pad($ord_no_e, 8, 0, STR_PAD_LEFT)."' \n" : null;
/*
    // 入荷予定日（開始）
    $arr_day_s = $arr_day_sy."-".$arr_day_sm."-".$arr_day_sd;
    $sql .= ($arr_day_s != "--") ? "AND t_order_h.arrival_day >= '$arr_day_s' \n" : null;
    // 入荷予定日（終了）
    $arr_day_e = $arr_day_ey."-".$arr_day_em."-".$arr_day_ed;
    $sql .= ($arr_day_e != "--") ? "AND t_order_h.arrival_day <= '$arr_day_e' \n" : null;
*/
    // 希望納期（開始）
    $hope_day_s = $hope_day_sy."-".$hope_day_sm."-".$hope_day_sd;
    $sql .= ($hope_day_s != "--") ? "AND t_order_h.hope_day >= '$hope_day_s' \n" : null;
    // 希望納期（終了）
    $hope_day_e = $hope_day_ey."-".$hope_day_em."-".$hope_day_ed;
    $sql .= ($hope_day_e != "--") ? "AND t_order_h.hope_day <= '$hope_day_e' \n" : null;
    // 直送先
    if ($direct_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_order_h.direct_name  LIKE '%$direct_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_order_h.direct_name2 LIKE '%$direct_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_order_h.direct_cname LIKE '%$direct_name%' \n";
        $sql .= "   ) \n";
    }
    // 商品コード
    $sql .= ($goods_cd != null) ? "AND t_order_d.goods_cd LIKE '$goods_cd%' \n" : null; 
    // 商品名
    $sql .= ($goods_name != null) ? "AND t_order_d.goods_name LIKE '%$goods_name%' \n" : null; 

    // 変数詰め替え
    $where_sql = $sql;


    $sql = null;

    // ソート順
    switch ($_POST["hdn_sort_col"]){
        // 発注日
        case "sl_ord_day":
            $sql .= "   t_order_h.ord_time, \n";
            $sql .= "   t_order_h.client_cd1, \n";
            $sql .= "   t_order_h.client_cd2, \n";
            $sql .= "   t_order_h.ord_no, \n";
            $sql .= "   t_order_h.hope_day, \n";
            $sql .= "   t_order_d.line \n";
            break;
        // 入力日
        case "sl_input_day":
            $sql .= "   t_order_h.enter_day, \n";
            $sql .= "   t_order_h.ord_time, \n";
            $sql .= "   t_order_h.client_cd1, \n";
            $sql .= "   t_order_h.client_cd2, \n";
            $sql .= "   t_order_h.ord_no, \n";
            $sql .= "   t_order_h.hope_day, \n";
            $sql .= "   t_order_d.line \n";
            break;
        // 発注先コード
        case "sl_client_cd":
            $sql .= "   t_order_h.client_cd1, \n";
            $sql .= "   t_order_h.client_cd2, \n";
            $sql .= "   t_order_h.ord_time, \n";
            $sql .= "   t_order_h.ord_no, \n";
            $sql .= "   t_order_h.hope_day, \n";
            $sql .= "   t_order_d.line \n";
            break;
        // 発注先名
        case "sl_client_name":
            $sql .= "   t_order_h.client_cname, \n";
            $sql .= "   t_order_h.ord_time, \n";
            $sql .= "   t_order_h.client_cd1, \n";
            $sql .= "   t_order_h.client_cd2, \n";
            $sql .= "   t_order_h.ord_no, \n";
            $sql .= "   t_order_h.hope_day, \n";
            $sql .= "   t_order_d.line \n";
            break;
        // 発注番号
        case "sl_ord_no":
            $sql .= "   t_order_h.ord_no, \n";
            $sql .= "   t_order_h.ord_time, \n";
            $sql .= "   t_order_h.client_cd1, \n";
            $sql .= "   t_order_h.client_cd2, \n";
            $sql .= "   t_order_h.hope_day, \n";
            $sql .= "   t_order_d.line \n";
            break;
        // 希望納期
        case "sl_hope_day":
            $sql .= "   t_order_h.hope_day, \n";
            $sql .= "   t_order_h.ord_time, \n";
            $sql .= "   t_order_h.client_cd1, \n";
            $sql .= "   t_order_h.client_cd2, \n";
            $sql .= "   t_order_h.ord_no, \n";
            $sql .= "   t_order_h.hope_day, \n";
            $sql .= "   t_order_d.line \n";
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
    $sql .= "   t_order_h.ord_id \n";
    $sql .= "FROM \n"; 
    $sql .= "   t_order_h \n";
    $sql .= "   INNER JOIN t_order_d ON t_order_h.ord_id = t_order_d.ord_id \n";
    $sql .= "   INNER JOIN t_client  ON t_order_h.client_id = t_client.client_id \n";
    $sql .= "   LEFT  JOIN t_staff   ON t_order_h.c_staff_id = t_staff.staff_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_order_h.shop_id = $shop_id \n";
    $sql .= "AND \n";
    $sql .= "   (t_order_h.ord_stat IN ('1', '2') OR t_order_h.ord_stat IS NULL) \n";
    $sql .= "AND \n";
    $sql .= "   t_order_d.rest_flg = 't' \n";
    $sql .= $where_sql;
    $sql .= "GROUP BY \n";
    $sql .= "   t_order_h.ord_id \n";

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
    $sql .= "   t_order_h.client_cd, \n";                       // 発注先コード
    $sql .= "   t_order_h.client_cname, \n";                    // 発注先名
    $sql .= "   t_order_h.ord_id, \n";                          // 発注ID
    $sql .= "   t_order_h.ord_no, \n";                          // 発注No.
    $sql .= "   to_char(t_order_h.ord_time, 'yyyy-mm-dd') AS ord_day, \n";
                                                                // 発注日
    //$sql .= "   t_order_h.arrival_day, \n";                     // 入荷予定日
    $sql .= "   t_order_h.hope_day, \n";                        // 希望納期
    $sql .= "   t_order_d.goods_cd, \n";                        // 商品コード
    $sql .= "   t_order_d.goods_name, \n";                      // 商品名
    $sql .= "   t_order_d.num AS order_num, \n";                // 発注数
    $sql .= "   COALESCE(t_buy.buy_num,0) AS buy_num, \n";      // 仕入数
    $sql .= "   t_order_d.num - COALESCE(t_buy.buy_num,0) AS inventory_num, \n";
                                                                // 発注残
    $sql .= "   t_order_h.ware_name, \n";                       // 倉庫
    $sql .= "   t_order_d.ord_d_id, \n";
    $sql .= "   t_order_h.ord_stat, \n";
    $sql .= "   t_order_h.ps_stat, \n";
    $sql .= "   t_order_d.goods_id, \n";
    $sql .= "   to_char(t_order_h.send_date, 'yyyy-mm-dd'), \n";
    $sql .= "   to_char(t_order_h.ord_time, 'hh24:mi'), \n";
    $sql .= "   t_order_h.enter_day \n";
    $sql .= "FROM \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_order_h.ord_id, \n";
    $sql .= "           t_order_h.ord_no, \n";
    $sql .= "           t_order_h.ord_time, \n";
    //$sql .= "           t_order_h.arrival_day, \n";
    $sql .= "           t_order_h.hope_day, \n";
    $sql .= "           t_order_h.ord_stat, \n";
    $sql .= "           t_order_h.ps_stat, \n";
    $sql .= "           CASE t_client.client_div \n";
    $sql .= "               WHEN '3' THEN t_order_h.client_cd1 || '-' || t_order_h.client_cd2 \n";
    $sql .= "               ELSE          t_order_h.client_cd1 \n";
    $sql .= "           END \n";
    $sql .= "           AS client_cd, \n";
    $sql .= "           t_order_h.client_cd1, \n";
    $sql .= "           t_order_h.client_cd2, \n";
    $sql .= "           t_order_h.client_cname, \n";
    $sql .= "           t_order_h.ware_name, \n";
    $sql .= "           t_order_h.send_date, \n";
    $sql .= "           t_order_h.enter_day \n";
    $sql .= "       FROM \n";
    $sql .= "           t_order_h \n";
    $sql .= "           INNER JOIN t_client ON t_order_h.client_id = t_client.client_id \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_order_h.shop_id = 1 \n";
    $sql .= "       AND \n";
    $sql .= "           (t_order_h.ord_stat IN ('1', '2') OR t_order_h.ord_stat IS NULL) \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_order_h \n";
    $sql .= "   INNER JOIN t_order_d ON  t_order_h.ord_id = t_order_d.ord_id \n";
    $sql .= "                        AND t_order_d.rest_flg = 't' \n";
    $sql .= "   LEFT JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           ord_d_id, \n";
    $sql .= "           SUM(num) AS buy_num \n";
    $sql .= "       FROM \n";
    $sql .= "           t_buy_d \n";
    $sql .= "       GROUP BY \n";
    $sql .= "           ord_d_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_buy \n";
    $sql .= "   ON t_order_d.ord_d_id = t_buy.ord_d_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_order_h.ord_id IN (";
    foreach ($ary_list_data as $key => $value){
    $sql .= $value["ord_id"];
    $sql .= ($key+1 < count($ary_list_data)) ? ", " : ") \n";
    }
    $sql .= "ORDER BY \n";
    $sql .= $order_sql;
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);

    // ページ内データ取得
    $res        = Db_Query($db_con, $sql);
    $get_num    = pg_num_rows($res);
    $ary_data   = Get_Data($res, 2, "ASSOC");

}


/****************************/
// 表示用データ作成
/****************************/
if ($post_flg == true && $err_flg != true){

    /****************************/
    // 表示用データ整形
    /****************************/
    // No.初期値
    $i = 0;
    // 
    $html_l = null;
    // ページ内データでループ
    if (count($ary_data) > 0){
        foreach ($ary_data as $key => $value){

            // 前・次の参照行を変数に入れて使いやすくしておく
            $back = $ary_data[$key-1];
            $next = $ary_data[$key+1];

            // ■No.の出力設定
            // 配列の最初、または前回と今回の発注IDが異なる場合
            if ($key == 0 || $back["ord_id"] != $value["ord_id"]){
                $no             = ++$i;
            }else{
                $no             = null;
            }
            // ■発注日
            // No.がある場合
            if ($no != null){
                $ord_day        = $value["ord_day"];
            }else{
                $ord_day        = null;
            }

            // ■入力日
            // No.がある場合
            if($no != null){
                $ord_ent_day    = substr($value["enter_day"],0,10);
            }else{
                $ord_ent_day    = null;
            }

            // ■発注先
            // No.がある場合
            if ($no != null){
                $client         = $value["client_cd"];
                $client        .= "<br>";
                $client        .= htmlspecialchars($value["client_cname"]);
                $client        .= "<br>";
            }else{
                $client         = null;
            }
            // ■発注番号リンク
            if ($no != null){
                if ($value["ord_stat"] == "3" || ($value["ord_stat"] == null && $value["ps_stat"] == "1")){
                    $link_mod   = "1-3-102.php";
                }else{
                    $link_mod   = "1-3-103.php";
                }
                $ord_no_link    = "<a href=\"./$link_mod?ord_id=".$value["ord_id"]."\">".$value["ord_no"]."</a>";
            }else{
                $ord_no_link    = null;
            }
/*
            // ■入荷予定日
            if ($no != null){
                $arrival_day    = $value["arrival_day"];
            }else{
                $arrival_day    = null;
            }
*/
            // ■希望納期
            if ($no != null){
                $hope_day       = $value["hope_day"];
            }else{
                $hope_day       = null;
            }
            // ■商品
            $goods              = $value["goods_cd"]."<br>".htmlspecialchars($value["goods_name"])."<br>";
            // ■発注数
            $order_num          = number_format($value["order_num"]);
            // ■仕入数
            $buy_num            = number_format($value["buy_num"]);
            // ■発注残
            $inventory_num      = number_format($value["inventory_num"]);
            // ■仕入倉庫
            $ware               = htmlspecialchars($value["ware_name"]);
            // ■仕入入力リンク
            if ($no != null){
                $buy_link       = "<a href=\"./1-3-207.php?ord_id=".$value["ord_id"]."\">入力</a>";
            }else{
                $buy_link       = null;
            }
            // ■発注完了理由
            $form->addElement("checkbox", "close_ord_d_id[$key]", "");
            $form->addElement("text", "form_reason[$key]", "", "size=\"22\" maxLength=\"15\" $g_form_option");
            // ■行色css
            if ($no != null){
                $css            = (bcmod($no, 2) == 0) ? "Result2" : "Result1";
            }else{
                $css            = $css;
            }

            // ■まとめ
            // 行色css
            $disp_data[$key]["css"]             = $css;
            // No.
            $disp_data[$key]["no"]              = $no;
            // 発注日
            $disp_data[$key]["ord_day"]         = $ord_day;
            // 発注先
            $disp_data[$key]["client"]          = $client;
            // 発注番号リンク
            $disp_data[$key]["ord_no_link"]     = $ord_no_link;
/*
            // 入荷予定日
            $disp_data[$key]["arrival_day"]     = $arrival_day;
*/
            // 入荷予定日
            $disp_data[$key]["hope_day"]        = $hope_day;
            // 商品
            $disp_data[$key]["goods"]           = $goods;
            // 発注数
            $disp_data[$key]["order_num"]       = $order_num;
            // 仕入数
            $disp_data[$key]["buy_num"]         = $buy_num;
            // 発注残
            $disp_data[$key]["inventory_num"]   = $inventory_num;
            // 仕入倉庫
            $disp_data[$key]["ware_name"]       = $ware_name;
            // 仕入リンク
            $disp_data[$key]["buy_link"]        = $buy_link;

            // 発注完了理由の初期化
            $set_delete_data["close_ord_d_id"][$key]    = "";
            $set_delete_data["form_reason"][$key]       = "";

            // 各行の発注IDを持たせるhiddenを作成（強制完了させる発注IDを判定させるため）
            // 各行の伝票作成日時を持たせるhiddenを作成（強制完了させる発注IDを判定させるため）
            $form->addElement("hidden", "hdn_ord_d_id[$key]", null, null);
            $form->addElement("hidden", "hdn_enter_day[$key]", null, null);

            // 作成したhiddenに発注IDをセット
            $set_hdn_ord_d_id["hdn_ord_d_id[$key]"]   = $value["ord_d_id"];
            $set_hdn_ord_d_id["hdn_enter_day[$key]"]  = $value["enter_day"];

            // 一覧html作成
            $html_l .= "    <tr class=\"$css\">\n";
            $html_l .= "        <td align=\"right\">$no</td>\n";
            $html_l .= "        <td align=\"center\">$ord_day<br>".$ord_ent_day."</td>\n";
            $html_l .= "        <td>$client</td>\n";
            $html_l .= "        <td align=\"center\">$ord_no_link</td>\n";
            //$html_l .= "        <td align=\"center\">$arrival_day</td>\n";
            $html_l .= "        <td align=\"center\">$hope_day</td>\n";
            $html_l .= "        <td>$goods</td>\n";
            $html_l .= "        <td align=\"right\">$order_num</td>\n";
            $html_l .= "        <td align=\"right\">$buy_num</td>\n";
            $html_l .= "        <td align=\"right\">$inventory_num</td>\n";
            $html_l .= "        <td>$ware_name</td>\n";
            $html_l .= "        <td align=\"center\">$buy_link</td>\n";
            $html_l .= "        <td align=\"center\">\n";
            $html_l .= "            ".$form->_elements[$form->_elementIndex["close_ord_d_id[$key]"]]->toHtml();
            $html_l .= "            理由：".$form->_elements[$form->_elementIndex["form_reason[$key]"]]->toHtml()."<br>";
            $html_l .= "        </td>\n";
            $html_l .= "    </tr>\n";

        }
    }

    $get_num = ($get_num > 0) ? $get_num : 0;

    // 発注完了（チェック）
    $form->addElement("checkbox", "form_ord_comp_check", "", "発注完了",
        "onClick=\"javascript:All_check('form_ord_comp_check', 'close_ord_d_id', $get_num);\""
    );

    // 発注完了・理由初期化
    $set_delete_data["form_ord_comp_check"] = "";

    // setConstants
    $form->setConstants($set_delete_data);
    $form->setConstants($set_hdn_ord_d_id);

}


/****************************/
// HTML作成（検索部）
/****************************/
// 共通検索テーブル
$html_s .= Search_Table_Ord_H($form);
$html_s .= "<br style=\"font-size: 4px;\">\n";
// モジュール個別検索テーブル１
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 70px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">商品</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_goods"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
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
$page_menu = Create_Menu_h('buy','1');

/****************************/
// 画面ヘッダー作成
/****************************/
$page_title .= "　".$form->_elements[$form->_elementIndex["104_button"]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex["102_button"]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex["106_button"]]->toHtml();
$page_header = Create_Header($page_title);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form関連の変数をassign
$smarty->assign("form", $renderer->toArray());

// その他の変数をassign
$smarty->assign("var", array(
    "html_header"    => "$html_header",
    "page_menu"      => "$page_menu",
    "page_header"    => "$page_header",
    "html_footer"    => "$html_footer",
    "html_page"      => "$html_page",
    "html_page2"     => "$html_page2",
    "total_count"    => "$total_count",
    "r_count"        => "$r_count",
    "ord_d_id_error" => "$ord_d_id_error",
    "reason_error"   => "$reason_error",
    "post_flg"      => "$post_flg",
    "err_flg"       => "$err_flg",
));

$smarty->assign("row", $row_data);

$smarty->assign("html", array(
    "html_s"    => "$html_s",
    "html_l"    => "$html_l",
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER["PHP_SELF"].".tpl"));

?>
