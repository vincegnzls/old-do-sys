<?php

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-12-08      ban_0089    suzuki      ログに登録するマスタ名を変更
 *  2006-12-11      ban_0144    suzuki      CSV出力時にはサニタイジングを行わないように修正
 *  2007-06-07                  fukuda      本部用商品分類がサイニタイジングされていない不具合を修正
 *  2015/05/13                  amano  		Dialogue関数でボタン名が送られない IE11 バグ対応
 *  
 *
*/

$page_title = "商品分類マスタ";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]",null,"onSubmit=return confirm(true)");

//DB接続
$conn = Db_Connect();

// 権限チェック
$auth       = Auth_Check($conn);
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//外部変数取得
/****************************/
$shop_id    = $_SESSION["client_id"];
$group_kind = $_SESSION["group_kind"];

$get_g_product_id = $_GET["g_product_id"];

/****************************/
//初期値を抽出
/****************************/
if($get_g_product_id !=  null){
    $sql  = "SELECT";
    $sql .= "   g_product_cd,";
    $sql .= "   g_product_name,";
    $sql .= "   note,";
    $sql .= "   public_flg ";             //共有フラグ
    $sql .= " FROM";
    $sql .= "   t_g_product";
    $sql .= " WHERE";
    $sql .= "   g_product_id = $get_g_product_id";
    $sql .= "   AND";
    $sql .= "   (public_flg = 't' ";
    $sql .= "   AND";
    $sql .= "   accept_flg = '1'";
    $sql .= "OR ";
    $sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
    $sql .= ")";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    Get_Id_Check($result, 2);

    $def_data["form_g_product_cd"]       = pg_fetch_result($result,0,0);
    $def_g_product_cd                    = pg_fetch_result($result,0,0);
    $def_data["form_g_product_name"]     = pg_fetch_result($result,0,1);
    $def_data["form_g_product_note"]     = pg_fetch_result($result,0,2);
    $public_flg                        = pg_fetch_result($result,0,3);
    $def_data["update_flg"]            = true;
    $form->setDefaults($def_data);
}

/*****************************/
//オブジェクト作成
/*****************************/
/*
//商品分類コード
//共有フラグ判定
if($public_flg=='t'){
    $form->addElement("static","form_g_product_cd","","");
}else{
    $form->addElement("text","form_g_product_cd","","size=\"4\" maxLength=\"4\" style=\"$g_form_style;text-align: left;\"".$g_form_option."\"");
}

//商品分類名
//共有フラグ判定
if($public_flg=='t'){
    $form->addElement("static","form_g_product_name","","");
}else{
    $form->addElement("text","form_g_product_name","","size=\"22\" maxLength=\"10\"".$g_form_option."\"");
}

// 入力権限のあるスタッフのみ出力
//ボタン
//共有フラグ判定
if($public_flg!='t'){
    $form->addElement("submit","form_entry_button","登　録","onClick=\"return Dialogue('登録します。', '#', this))\" $disabled");
}
//共有フラグ判定
$form->addElement("button","form_clear_button","クリア","onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","form_csv_button","CSV出力","onClick=\"javascript:Button_Submit('csv_button_flg', '#', 'true');\"");

//備考
//共有フラグ判定
if($public_flg=='t'){
    $form->addElement("static","form_g_product_note","","");
}else{
    $form->addElement("text","form_g_product_note","","size=\"34\" maxLength=\"30\" ".$g_form_option."\"");
}
*/

//商品分類コード
$freeze_form[] = $form->addElement("text", "form_g_product_cd", "", "size=\"4\" maxLength=\"4\" class=\"ime_disabled\" $g_form_option");

//商品分類名
$freeze_form[] = $form->addElement("text", "form_g_product_name", "", "size=\"22\" maxLength=\"10\" $g_form_option");

//備考
$freeze_form[] = $form->addElement("text", "form_g_product_note", "", "size=\"34\" maxLength=\"30\" $g_form_option");

// 入力権限のあるスタッフのみ出力
//ボタン
//共有フラグ判定
if($public_flg!='t'){
    $form->addElement("submit","form_entry_button","登　録","onClick=\"return Dialogue('登録します。', '#', this)\" $disabled");
}

//共有フラグ判定
$form->addElement("button","form_clear_button","クリア","onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","form_csv_button","CSV出力","onClick=\"javascript:Button_Submit('csv_button_flg', '#', 'true');\"");

//hidden
$form->addElement("hidden","csv_button_flg");
$form->addElement("hidden","update_flg");

/****************************/
// フリーズ処理
/****************************/
// 共有フラグtrueの場合
if ($public_flg == "t"){
    $gr_freeze_form = $form->addGroup($freeze_form, "freeze_form", "");
    $gr_freeze_form->freeze();
}

/****************************/
//ルール作成
/****************************/
//商品分類コード
$form->addRule("form_g_product_cd", "商品分類コードは半角数字のみ4桁です。","required");
$form->addRule("form_g_product_cd", "商品分類コードは半角数字のみ4桁です。","numeric");

//商品分類名
$form->addRule("form_g_product_name", "商品分類名は1文字以上10文字以下です。","required");

/****************************/
//登録ボタン押下処理
/****************************/
if($_POST["form_entry_button"] == "登　録"){
    /****************************/
    //POST情報取得
    /****************************/
    $g_product_cd        = $_POST["form_g_product_cd"];                                   //商品分類CD
    $g_product_name      = $_POST["form_g_product_name"];                                 //商品分類名
    $g_product_note      = $_POST["form_g_product_note"];                                 //備考  
    $update_flg        = $_POST["update_flg"]; 

    /***************************/
    //商品分類コード整形
    /***************************/
    $g_product_cd = str_pad($g_product_cd, 4,0, STR_PAD_LEFT);

    $sql  = "SELECT";
    $sql .= "   g_product_cd";
    $sql .= " FROM";
    $sql .= "   t_g_product";
    $sql .= " WHERE";
    $sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
    $sql .= "   AND";
    $sql .= "   g_product_cd = '$g_product_cd'";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $num = pg_num_rows($result);

    //使用済みエラー
    if($num > 0 && ($update_flg != true || ($update_flg == true && $def_g_product_cd != $g_product_cd))){
        $form->setElementError("form_g_product_cd","既に使用されている商品郡コードです。");
    }

    //コード体系
    if($g_product_cd < 5000 || $g_product_cd >= 9000){
        $form->setElementError("form_g_product_cd","商品分類コードに5000〜8999以外は利用できません。");
    }

    /***************************/
    //検証  
    /***************************/
    if($form->validate()){

        Db_Query($conn, "BEGIN");

        /*****************************/
        //登録処理
        /*****************************/
        if($update_flg != true){

            $insert_sql  = "INSERT INTO t_g_product(";
            $insert_sql .= "    g_product_id,";
            $insert_sql .= "    g_product_cd,";
            $insert_sql .= "    g_product_name,";
            $insert_sql .= "    note,";
            $insert_sql .= "    public_flg,";
            $insert_sql .= "    shop_id";
            $insert_sql .= ")VALUES(";
            $insert_sql .= "    (SELECT COALESCE(MAX(g_product_id), 0)+1 FROM t_g_product),";
            $insert_sql .= "    '$g_product_cd',";
            $insert_sql .= "    '$g_product_name',";
            $insert_sql .= "    '$g_product_note',";
            $insert_sql .= "    'f',";
            $insert_sql .= "    $shop_id";
            $insert_sql .= ");";

            $result = Db_Query($conn, $insert_sql);

            //失敗した場合はロールバック
            if($result === false){
                Db_Query($conn, "ROLLBACK");
                exit;
            }

            //登録した情報をログに残す
            $result = Log_Save( $conn, "g_product", "1", $g_product_cd, $g_product_name);
            //失敗した場合はロールバック
            if($result === false){
                Db_Query($conn, "ROLLBACK");
                exit;
            }

           $message = "登録しました。";

        /*******************************/
        //変更処理
        /*******************************/
        }elseif($update_flg == true){
            $insert_sql  = "UPDATE ";
            $insert_sql .= "    t_g_product";
            $insert_sql .= " SET";
            $insert_sql .= "    g_product_cd = '$g_product_cd',";
            $insert_sql .= "    g_product_name = '$g_product_name',";
            $insert_sql .= "    note = '$g_product_note'";
            $insert_sql .= " WHERE";
            $insert_sql .= "    g_product_id = $get_g_product_id";
            $insert_sql .= ";";

            $result = Db_Query($conn, $insert_sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK");
                exit;
            }

            //登録した情報をログに残す
            $result = Log_Save( $conn, "g_product", "2", $g_product_cd, $g_product_name);
            //失敗した場合はロールバック
            if($result === false){
                Db_Query($conn, "ROLLBACK");
                exit;
            }
            $message = "変更しました。";

            /*******************************/
            // 契約・未付番受注伝票の商品分類名を同期
            /*******************************/
            // 契約関連関数をインクルード
            require_once(INCLUDE_DIR."function_keiyaku.inc");

            // 商品分類名の変更を契約・未付番受注伝票に反映する
            $result = Mst_Sync_Goods($conn, $get_g_product_id, "g_name");

            // エラー時はROLLBACK
            if ($result === false){
                Db_Query($conn, "ROLLBACK;");
                exit;
            }

        }
        Db_Query($conn, "COMMIT");

        $set_data["form_g_product_cd"]    = "";                                   //商品分類CD
        $set_data["form_g_product_name"]  = "";                                 //商品分類名
        $set_data["form_g_product_note"]  = "";                                 //備考  
        $set_data["update_flg"]         = ""; 
        
        $form->setConstants($set_data);
    }

}

/*****************************
//一覧作成
/*****************************/
$sql  = "SELECT";
$sql .= "   DISTINCT";
$sql .= "   t_g_product.g_product_cd,";
$sql .= "   t_g_product.g_product_id,";
$sql .= "   t_g_product.g_product_name,";
$sql .= "   t_g_product.note";
$sql .= " FROM";
$sql .= "   t_g_product";
$sql .= " WHERE";
$sql .= "   t_g_product.public_flg = 't'";
$sql .= "   AND";
$sql .= "   t_g_product.accept_flg = '1'";
$sql .= "   OR";
$sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
$sql .= "   ORDER BY t_g_product.g_product_cd";
$sql .= ";";

$result = Db_Query($conn, $sql);
$total_count = pg_num_rows($result);
$page_data = Get_Data($result);



/*****************************/
//CSVボタン押下処理
/*****************************/
if($_POST["csv_button_flg"] == true && $_POST["form_entry_button"] != "登　録"){

	$sql  = "SELECT";
	$sql .= "   DISTINCT";
	$sql .= "   t_g_product.g_product_cd,";
	$sql .= "   t_g_product.g_product_id,";
	$sql .= "   t_g_product.g_product_name,";
	$sql .= "   t_g_product.note";
	$sql .= " FROM";
	$sql .= "   t_g_product";
	$sql .= " WHERE";
	$sql .= "   t_g_product.public_flg = 't'";
	$sql .= "   AND";
	$sql .= "   t_g_product.accept_flg = '1'";
	$sql .= "   OR";
	$sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
	$sql .= "   ORDER BY t_g_product.g_product_cd";
	$sql .= ";";

	$result = Db_Query($conn, $sql);
	$total_count = pg_num_rows($result);
	$page_data = Get_Data($result,2);

    //CSV作成
    for($i = 0; $i < $total_count; $i++){
        $csv_page_data[$i][0] = $page_data[$i][0];
        $csv_page_data[$i][1] = $page_data[$i][2];
        $csv_page_data[$i][2] = $page_data[$i][3];
    }

    $csv_file_name = "商品分類マスタ".date("Ymd").".csv";
    $csv_header = array(
        "商品分類コード",
        "商品分類名",
        "備考"
      );

    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($csv_page_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;
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
$page_menu = Create_Menu_f('system','1');

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "(全".$total_count."件)";
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
    'total_count'   => "$total_count",
    'message'       => "$message",
));

$smarty->assign('page_data', $page_data);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
