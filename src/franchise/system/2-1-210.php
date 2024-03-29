<?php

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-12-08      ban_0082    suzuki      登録・変更のログを残すように修正
 *  2007-04-24                  watanabe-k  口座番号の桁チェックを6桁か7桁に修正
 *  2007-05-09                  kaku-m      csv項目名の修正
 *  2010-04-28      Rev.1.5　　 hashimoto-y 非表示機能の追加
 *  2015/05/01                  amano  Dialogue関数でボタン名が送られない IE11 バグ対応
 *
*/

$page_title = "銀行マスタ";

// 環境設定ファイル
require_once("ENV_local.php");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

// DB接続
$db_con = Db_Connect();

// 権限チェック
 $auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
 $auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
 $disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
// 外部変数取得
/****************************/
$shop_id    = $_SESSION["client_id"];
$group_kind = $_SESSION["group_kind"];

$update_flg = $_POST["update_flg"];
$update_id  = $_POST["update_id"];

/* GETしたIDの正当性チェック */
$where  = " b_bank_id IN (SELECT b_bank_id FROM t_b_bank WHERE bank_id IN (SELECT bank_id FROM t_bank WHERE ";
$where .= ($_SESSION["group_kind"] == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = ".$_SESSION["client_id"]." ";
$where .= ")) ";
if ($_GET["account_id"] != null && Get_Id_Check_Db($db_con, $_GET["account_id"], "account_id", "t_account", "num", $where) != true){
    header("Location: ../top.php");
}

/****************************/
// フォーム初期値設定
/****************************/
$def_fdata["form_deposit_kind"] = "1";
$form->setDefaults($def_fdata);


/****************************/
// フォームパーツ定義
/****************************/
// 銀行・支店
$sql  = "SELECT ";
$sql .= "   t_bank.bank_id, ";
$sql .= "   t_bank.bank_cd, ";
$sql .= "   t_bank.bank_name, ";
$sql .= "   t_b_bank.b_bank_id, ";
$sql .= "   t_b_bank.b_bank_cd, ";
$sql .= "   t_b_bank.b_bank_name ";
$sql .= "FROM ";
$sql .= "   t_bank ";
$sql .= "   INNER JOIN t_b_bank ON t_bank.bank_id = t_b_bank.bank_id ";
$sql .= "WHERE ";
$sql .= ($group_kind == "2") ? " t_bank.shop_id IN (".Rank_Sql().") " : " t_bank.shop_id = $shop_id ";
$sql .= "ORDER BY ";
$sql .= "   t_bank.bank_cd, t_b_bank.b_bank_cd ";
$sql .= ";";
$res  = Db_Query($db_con, $sql);
$num  = pg_num_rows($res);
// hierselect用配列定義
$ary_hier1[null] = null;
$ary_hier2       = null;
if ($num > 0){
    for ($i=0; $i<$num; $i++){
        // データ取得（レコード毎）
        $data_list[$i] = pg_fetch_array($res, $i, PGSQL_ASSOC);
        // 分かりやすいように各階層のIDを変数に代入
        $hier1_id = $data_list[$i]["bank_id"];
        $hier2_id = $data_list[$i]["b_bank_id"];
        // 現在参照レコードの銀行コードと前に参照したレコードの銀行コードが異なる場合
        if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"]){
            // 第1階層
            $ary_hier1[$hier1_id] = $data_list[$i]["bank_cd"]."：".$data_list[$i]["bank_name"];
        }
        // 現在参照レコードの銀行コードと前に参照したレコードの銀行コードが異なる
        // または、現在参照レコードの支店コードと前に参照したレコードの支店コードが異なる場合
        if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"] ||
            $data_list[$i]["b_bank_cd"] != $data_list[$i-1]["b_bank_cd"]){
            // 第2階層セレクトアイテムの最初にNULLを入れる
            if ($data_list[$i]["bank_cd"] != $data_list[$i-1]["bank_cd"]){
                $ary_hier2[$hier1_id][null] = null;
            }
            // 第2階層
            $ary_hier2[$hier1_id][$hier2_id] = $data_list[$i]["b_bank_cd"]."：".$data_list[$i]["b_bank_name"];
        }   
    }
    // 1つの配列にまとめる
    $ary_hier_item = array($ary_hier1, $ary_hier2);
}
$html = "</td></tr><tr><td class=\"Title_Purple\"><b>支店名<font color=\"#ff0000\">※</font></b></td><td class=\"Value\">";
$obj_genre_select = &$form->addElement("hierselect", "form_bank_b_bank", "", "", $html);
$obj_genre_select->setOptions($ary_hier_item);

// 預金種目
$radio = null;
$radio[] =& $form->createElement("radio", null, null, "普通", "1");
$radio[] =& $form->createElement("radio", null, null, "当座", "2");
$form->addGroup($radio, "form_deposit_kind", "");

// 口座番号
$form->addElement("text", "form_account_no", "", "size=\"8\" maxlength=\"7\" style=\"$g_form_style\" $g_form_option");

// 口座名
$form->addElement("text", "form_account_identifier", "", "size=\"50\" maxlength=\"40\" $g_form_option");

// 口座名義
$form->addElement("text", "form_account_holder", "", "size=\"50\" maxlength=\"40\" $g_form_option");

#2010-04-28 hashimoto-y
$form->addElement('checkbox', 'form_nondisp_flg', '', '');

// 備考
$form->addElement("text", "form_note", "", "size=\"34\" maxlength=\"30\" $g_form_option");

// 登録
$form->addElement("submit", "form_entry_btn", "登　録", "onClick=\"javascript:return Dialogue('登録します。','#', this)\" $disabled");

// クリア
$form->addElement("button", "form_clear_btn", "クリア", "onClick=\"javascript:location.href('$_SERVER[PHP_SELF]');\"");

// CSV出力
$form->addElement("button", "form_csv_btn", "CSV出力", "onClick=\"javascript:Button_Submit('csv_button_flg','#','true')\"");

// ヘッダ部ボタン
$form->addElement("button", "bank_button", "銀行登録画面", "onClick=\"javascript:Referer('2-1-207.php')\"");
$form->addElement("button", "bank_mine_button", "支店登録画面", "onClick=\"javascript:Referer('2-1-208.php')\"");
$form->addElement("button", "bank_account_button", "口座登録画面", $g_button_color."onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

// hidden
$form->addElement("hidden", "hdn_bank_select_flg", null, null); // 銀行選択フラグ
$form->addElement("hidden", "update_flg", null, null);          // 更新リンク押下判定フラグ
$form->addElement("hidden", "update_id", null, null);           // 更新対象ID
$form->addElement("hidden", "csv_button_flg", null, null);      // CSV出力ボタン押下フラグ


/****************************/
// CSV出力ボタン押下処理
/****************************/
// CSV出力ボタンが押下された場合
if ($_POST["csv_button_flg"] == true){

    // CSV用データ取得SQL
    $sql  = "SELECT ";
    $sql .= "   t_bank.bank_cd, ";
    $sql .= "   t_bank.bank_name, ";
    $sql .= "   t_bank.bank_cname, ";
    $sql .= "   t_b_bank.b_bank_cd, ";
    $sql .= "   t_b_bank.b_bank_name, ";
    $sql .= "   CASE t_account.deposit_kind ";
    $sql .= "       WHEN '1' THEN '普通' ";
    $sql .= "       WHEN '2' THEN '当座' ";
    $sql .= "   END, ";
    $sql .= "   t_account.account_no, ";
    $sql .= "   t_account.account_identifier, ";
    $sql .= "   t_account.account_holder, ";
    #2010-04-28 hashimoto-y
    $sql .= "   CASE t_account.nondisp_flg";
    $sql .= "   WHEN true  THEN '○'";
    $sql .= "   WHEN false THEN ''";
    $sql .= "   END,";

    $sql .= "   t_account.note ";
    $sql .= "FROM ";
    $sql .= "   t_account ";
    $sql .= "   LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id ";
    $sql .= "   LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id ";
    $sql .= "WHERE ";
    $sql .= ($group_kind == "2") ? " t_bank.shop_id IN (".Rank_Sql().") " : " t_bank.shop_id = $shop_id ";
    $sql .= "ORDER BY ";
    $sql .= "   t_bank.bank_cd, t_b_bank.b_bank_cd, t_account.account_no ";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);

    // CSV用データ取得
    $i = 0;
    while ($data_list = pg_fetch_array($res)){
        $account_data[$i][0] = $data_list[0];
        $account_data[$i][1] = $data_list[1];
        $account_data[$i][2] = $data_list[2];
        $account_data[$i][3] = $data_list[3];
        $account_data[$i][4] = $data_list[4];
        $account_data[$i][5] = $data_list[5];
        $account_data[$i][6] = $data_list[6];
        $account_data[$i][7] = $data_list[7];
        $account_data[$i][8] = $data_list[8];
        #2010-04-28 hashimoto-y
        #$account_data[$i][9] = $data_list[9];
        $account_data[$i][9] = $data_list[9];
        $account_data[$i][10] = $data_list[10];
        $i++;
    }

    // CSVボタン押下フラグをクリア
    $clear_flg["csv_button_flg"] = "";
    $form->setConstants($clear_flg);

    // CSVファイル名
    $csv_file_name = "口座マスタ".date("Ymd").".csv";

    // CSVヘッダ作成
    $csv_header = array(
        "銀行コード",
        "銀行名",
        "銀行名（略称）",
        "支店コード",
        "支店名",
        "預金種目",
        "口座番号",
        "口座名",
        "口座名義",
        #2010-04-28 hashimoto-y
        "非表示",
        "備考",
    );

    // CSVファイル作成
    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($account_data, $csv_header);

    // 出力
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;

    // 終了
    exit;
}


/****************************/
// 変更時のフォーム補完
/****************************/
// 変更リンクが押下されている場合
if($_GET["account_id"] != null && $_POST["form_entry_btn"] == null){

    // GETした変更対象IDを変数へ代入
    $update_id = $_GET["account_id"];

    $sql  = "SELECT ";
    $sql .= "   t_bank.bank_id, ";
    $sql .= "   t_b_bank.b_bank_id, ";
    $sql .= "   t_account.deposit_kind, ";
    $sql .= "   t_account.account_no, ";
    $sql .= "   t_account.account_identifier, ";
    $sql .= "   t_account.account_holder, ";

    #2010-04-28 hashimoto-y
    $sql .= "   t_account.nondisp_flg,";

    $sql .= "   t_account.note ";
    $sql .= "FROM ";
    $sql .= "   t_account ";
    $sql .= "   LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id ";
    $sql .= "   LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id ";
    $sql .= "WHERE ";
    $sql .= "   t_account.account_id = $update_id ";
    $sql .= "AND ";
    $sql .= ($group_kind == "2") ? " t_bank.shop_id IN (".Rank_Sql().") " : " t_bank.shop_id = $shop_id ";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    // GETデータ判定
    Get_Id_Check($res);
    $data_list = pg_fetch_array($res, 0);

    // フォームに値を復元
    $def_fdata["form_bank_b_bank"][0]       = $data_list[0];
    $def_fdata["form_bank_b_bank"][1]       = $data_list[1];
    $def_fdata["form_deposit_kind"]         = $data_list[2];
    $def_fdata["form_account_no"]           = $data_list[3];
    $def_fdata["form_account_identifier"]   = $data_list[4];
    $def_fdata["form_account_holder"]       = $data_list[5];
    #2010-04-28 hashimoto-y
    #$def_fdata["form_note"]                 = $data_list[6];
    $def_fdata["form_nondisp_flg"]          = ($data_list[6] == 't')? 1 : 0;
    $def_fdata["form_note"]                 = $data_list[7];
    $def_fdata["update_flg"]                = true; 
    $def_fdata["update_id"]                 = $update_id; 
    $form->setConstants($def_fdata);

}


/****************************/
// エラーチェック(AddRule)
/****************************/
// ■銀行名・支店名
// 必須チェック
$err_msg = "銀行名 と 支店名 は必須です。";
$form->addGroupRule("form_bank_b_bank", array(
    "0" => array(array($err_msg, "required")),      
    "1" => array(array($err_msg, "required")),
));

// ■口座番号
// 必須チェック
$form->addRule("form_account_no", "口座番号 は必須項目です。", "required");
// 数値チェック
$form->addRule("form_account_no", "口座番号 は半角数字のみです。", "regex", "/^[0-9]+$/");

// ■口座名
// 必須チェック
$form->addRule("form_account_identifier", "口座名 は必須項目です。", "required");
// 全角/半角スペースのみチェック
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_account_identifier", "口座名 にスペースのみの登録はできません。", "no_sp_name");

// ■口座名義
// 必須チェック
$form->addRule("form_account_holder", "口座名義 は必須項目です。", "required");
$form->addRule("form_account_holder", "口座名義 にスペースのみの登録はできません。", "no_sp_name");


/****************************/
// 登録ボタン押下時の処理
/****************************/
if($_POST["form_entry_btn"] == "登　録"){

    // validate
    $form->validate();

    /*** 結果集計 ***/
    // エラーのあるフォームのフォーム名を格納するための配列を作成
    $ary_rule_err_forms = array();
    // エラーのあるフォームのフォーム名を配列に格納
    foreach ($form as $key1 => $value1){
        if ($key1 == "_errors"){
            foreach ($value1 as $key2 => $value2){
                $ary_rule_err_forms[] = $key2;
            }       
        }       
    }

    // POSTデータを変数にセット
    $post_bank_id               = $_POST["form_bank_b_bank"][0];
    $post_b_bank_id             = $_POST["form_bank_b_bank"][1];
    $post_deposit_kind          = $_POST["form_deposit_kind"];
    $post_account_no            = $_POST["form_account_no"];
    $post_account_identifier    = $_POST["form_account_identifier"];
    $post_account_holder        = $_POST["form_account_holder"];

    #2010-04-28 hashimoto-y
    $nondisp_flg                = ($_POST["form_nondisp_flg"] == '1')? 't' : 'f';

    $post_note                  = $_POST["form_note"];
    $update_flg                 = $_POST["update_flg"];
    $update_id                  = $_POST["update_id"];

    /****************************/
    // エラーチェック(PHP)
    /****************************/
    // ■口座番号
    // ・重複チェック
    if(!in_array("form_bank_b_bank", $ary_rule_err_forms) &&
       !in_array("form_account_no", $ary_rule_err_forms))
    {

        // 0埋め
//        $post_account_no = str_pad($post_account_no, 7, 0, STR_POS_LEFT);

        // 入力した口座番号がマスタに存在するかチェック
        $sql  = "SELECT ";
        $sql .= "   t_account.account_no "; 
        $sql .= "FROM ";
        $sql .= "   t_account ";
        $sql .= "   LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id ";
        $sql .= "   LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id ";
        $sql .= "WHERE ";
        $sql .= "   t_b_bank.b_bank_id = $post_b_bank_id ";
        $sql .= "AND ";
        $sql .= "   t_account.account_no = '$post_account_no' ";
        $sql .= "AND ";
        $sql .= ($group_kind == "2") ? " t_bank.shop_id IN (".Rank_Sql().") " : " t_bank.shop_id = $shop_id ";
        // 変更の場合は、自分のデータ以外を参照する
        if($update_id != null){
            $sql .= "AND NOT ";
            $sql .= "   t_account.account_id = '$update_id'";
        }
        $sql .= ";";
        $result = Db_Query($db_con, $sql);
        $row_count = pg_num_rows($result);
        if($row_count != 0){
            $form->setElementError("form_account_no", "既に使用されている 口座番号 です。");
        }
    }

    // エラーの際には、登録処理を行わない
    if($form->validate()){
        
        Db_Query($db_con, "BEGIN;");

        // 更新か登録か判定
        if($update_id != null){
            // 作業区分は更新
            $work_div = '2';
            // 変更完了メッセージ
            $comp_msg = "変更しました。";

            $sql  = "UPDATE ";
            $sql .= "   t_account ";
            $sql .= "SET ";
            $sql .= "   b_bank_id = $post_b_bank_id, ";
            $sql .= "   deposit_kind = '$post_deposit_kind', ";
            $sql .= "   account_no = '$post_account_no', ";
            $sql .= "   account_identifier = '$post_account_identifier', ";
            $sql .= "   account_holder = '$post_account_holder', ";
            #2010-04-28 hashimoto-y
            $sql .= "   nondisp_flg = '$nondisp_flg',";
            $sql .= "   note = '$post_note' ";
            $sql .= "WHERE ";
            $sql .= "   t_account.account_id = $update_id ";
            $sql .= ";";
        }else{
            // 作業区分は登録
            $work_div = '1';
            // 登録完了メッセージ
            $comp_msg = "登録しました。";

            $sql  = "INSERT INTO t_account ";
            $sql .= "(";
            $sql .= "   account_id, ";
            $sql .= "   b_bank_id, ";
            $sql .= "   deposit_kind, ";
            $sql .= "   account_no, ";
            $sql .= "   account_identifier, ";
            $sql .= "   account_holder, ";
            #2010-04-28 hashimoto-y
            $sql .= "   nondisp_flg,";
            $sql .= "   note ";
            $sql .= ") ";
            $sql .= "VALUES ";
            $sql .= "( ";
            $sql .= "   (SELECT COALESCE(MAX(account_id), 0)+1 FROM t_account), ";
            $sql .= "   $post_b_bank_id, ";
            $sql .= "   '$post_deposit_kind', ";
            $sql .= "   '$post_account_no', ";
            $sql .= "   '$post_account_identifier', ";
            $sql .= "   '$post_account_holder', ";
            #2010-04-28 hashimoto-y
            $sql .= "   '$nondisp_flg',";
            $sql .= "   '$post_note' ";
            $sql .= ") ";
            $sql .= ";";
        }

        $result = Db_Query($db_con,$sql);
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            exit;
        }

        // 銀行マスタの値をログに書き込む
        $result = Log_Save($db_con, "account", $work_div, $post_account_no,$post_account_identifier);
        // ログ登録時にエラーになった場合
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            exit;
        }

        Db_Query($db_con, "COMMIT;");

        // フォームの値を初期化
        $def_fdata["form_bank_b_bank"][0]       = "";
        $def_fdata["form_bank_b_bank"][1]       = "";
        $def_fdata["form_deposit_kind"]         = "1"; // def値
        $def_fdata["form_account_no"]           = "";
        $def_fdata["form_account_identifier"]   = "";
        $def_fdata["form_account_holder"]       = "";
        #2010-04-28 hashimoto-y
        $def_fdata["form_nondisp_flg"]          = "";
        $def_fdata["form_note"]                 = "";
        $def_fdata["update_flg"]                = "";
        $def_fdata["update_id"]                 = "";
        $form->setConstants($def_fdata);
    }
}

/****************************/
// ヘッダーに表示させる全件数
/****************************/
/** 銀行マスタ取得SQL作成 **/
$sql  = "SELECT ";
$sql .= "   t_bank.bank_cd, ";
$sql .= "   t_bank.bank_name, ";
$sql .= "   t_bank.bank_cname, ";
$sql .= "   t_b_bank.b_bank_cd, ";
$sql .= "   t_b_bank.b_bank_name, ";
$sql .= "   t_account.account_id, ";
$sql .= "   CASE t_account.deposit_kind ";
$sql .= "       WHEN '1' THEN '普通' ";
$sql .= "       WHEN '2' THEN '当座' ";
$sql .= "   END, ";
$sql .= "   t_account.account_no, ";
$sql .= "   t_account.account_identifier, ";
$sql .= "   t_account.account_holder, ";
#2010-04-28 hashimoto-y
$sql .= "   CASE t_account.nondisp_flg";
$sql .= "   WHEN true  THEN '○'";
$sql .= "   WHEN false THEN ''";
$sql .= "   END,";

$sql .= "   t_account.note ";
$sql .= "FROM ";
$sql .= "   t_account ";
$sql .= "   LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id ";
$sql .= "   LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id ";
$sql .= "WHERE ";
$sql .= ($group_kind == "2") ? " t_bank.shop_id IN (".Rank_Sql().") " : " t_bank.shop_id = $shop_id ";
$sql .= "ORDER BY ";
$sql .= "   t_bank.bank_cd, t_b_bank.b_bank_cd, t_account.account_no ";
$sql .= ";";
$res  = Db_Query($db_con, $sql);

// 全件数取得（ヘッダへの件数表示用）
$total_count = pg_num_rows($res);

// 行データ部品を作成
$row = Get_Data($res);

// 同じ値が続く場合はnullを代入（銀行・支店の情報のみ）
for ($i=0; $i<$total_count; $i++){
    for ($j=0; $j<$total_count; $j++){
        if ($i != $j && $row[$i][0] == $row[$j][0]){
            $row[$j][0] = null;
            $row[$j][1] = null;
            $row[$j][2] = null;
            if ($row[$i][3] == $row[$j][3]){
                $row[$j][3] = null;
                $row[$j][4] = null;
            }
        }
    }
}

// 行色css作成
for ($i=0; $i<$total_count; $i++){
    if($row[$i][0] == null){
        $tr[$i] = $tr[$i-1];
    }else{  
        $tr[$i] = ($tr[$i-1] == "Result1") ? "Result2" :  "Result1";
    }
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
// メニュー作成
/****************************/
$page_menu = Create_Menu_f("system", "1");

/****************************/
// 画面ヘッダー作成
/****************************/
$page_title .= "(全".$total_count."件)";
$page_title .= "　".$form->_elements[$form->_elementIndex[bank_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[bank_mine_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[bank_account_button]]->toHtml();

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
    "total_count"   => "$total_count",
    "comp_msg"      => "$comp_msg",
    "auth_r_msg"    => "$auth_r_msg",
));
$smarty->assign('row',$row);
$smarty->assign('tr',$tr);

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
