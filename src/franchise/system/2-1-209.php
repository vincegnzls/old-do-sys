<?php

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-12-08      ban_0088    suzuki      ログに登録するマスタ名変更
 *  2006-12-11      ban_0143    suzuki      CSV出力時にはサニタイジングを行わないように修正
 *  2015/05/01                  amano  Dialogue関数でボタン名が送られない IE11 バグ対応
 *  
 *
*/

$page_title = "管理区分マスタ";

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

$get_product_id = $_GET["product_id"];

/* GETしたIDの正当性チェック */
$where  = "(public_flg = 't' AND accept_flg = '1') ";
$where .= "OR ( ";
$where .= "shop_id = 1 OR ";
$where .= ($_SESSION["group_kind"] == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = ".$_SESSION["client_id"]." ";
$where .= ") ";
if ($_GET["product_id"] != null && Get_Id_Check_Db($conn, $_GET["product_id"], "product_id", "t_product", "num", $where) != true){
    header("Location: ../top.php");
}

/****************************/
//初期値を抽出
/****************************/
if($get_product_id != null){
    $sql  = "SELECT";
    $sql .= "   product_cd,";
    $sql .= "   product_name,";
    $sql .= "   note,";
    $sql .= "   public_flg ";             //共有フラグ
    $sql .= " FROM";
    $sql .= "   t_product";
    $sql .= " WHERE";
    $sql .= "   product_id = $get_product_id";
    $sql .= "   AND";
    $sql .= "   (public_flg = 't' ";
    $sql .= "   AND";
    $sql .= "   accept_flg = '1'";
    $sql .= "   OR ";
    $sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
    $sql .= ")";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    Get_Id_Check($result);

    $def_data["form_product_cd"]       = pg_fetch_result($result,0,0);
    $def_product_cd                    = pg_fetch_result($result,0,0);
    $def_data["form_product_name"]     = ($def_product_cd >= 5000 && $def_product_cd <= 8999) ? pg_fetch_result($result,0,1)
                                                                                              : htmlspecialchars(pg_fetch_result($result,0,1));
    $def_data["form_product_note"]     = ($def_product_cd >= 5000 && $def_product_cd <= 8999) ? pg_fetch_result($result,0,2)
                                                                                              : htmlspecialchars(pg_fetch_result($result,0,2));
    $public_flg                        = pg_fetch_result($result,0,3);
    $def_data["update_flg"]            = true;
    $form->setDefaults($def_data);
}

/*****************************/
//オブジェクト作成
/*****************************/
//管理区分コード
//共有フラグ判定
if($public_flg=='t'){
    $form->addElement("static","form_product_cd","","");
}else{
    $form->addElement("text","form_product_cd","","size=\"4\" maxLength=\"4\" style=\"$g_form_style;text-align: left;\"".$g_form_option."\"");
}
//管理区分名
//共有フラグ判定
if($public_flg=='t'){
    $form->addElement("static","form_product_name","","");
}else{
    $form->addElement("text","form_product_name","","size=\"22\" maxLength=\"10\"".$g_form_option."\"");
}

// 入力権限のあるスタッフのみ出力
//ボタン
//共有フラグ判定
if($public_flg!='t'){
    $form->addElement("submit","form_entry_button","登　録","onClick=\"return Dialogue('登録します。', '#', this)\" $disabled");
}
$form->addElement("button","form_clear_button","クリア","onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","form_csv_button","CSV出力","onClick=\"javascript:Button_Submit('csv_button_flg', '#', 'true');\"");

//備考
//共有フラグ判定
if($public_flg=='t'){
    $form->addElement("static","form_product_note","","");
}else{
    $form->addElement("text","form_product_note","","size=\"34\" maxLength=\"30\" ".$g_form_option."\"");
}

//hidden
$form->addElement("hidden","csv_button_flg");
$form->addElement("hidden","update_flg");

/****************************/
//ルール作成
/****************************/
//管理区分コード
$form->addRule("form_product_cd", "管理区分コードは半角数字のみ4桁です。","required");
$form->addRule("form_product_cd", "管理区分コードは半角数字のみ4桁です。", "regex", "/^[0-9]+$/");

//管理区分名
$form->addRule("form_product_name", "管理区分名は1文字以上10文字以下です。","required");
// 全角/半角スペースのみチェック
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_product_name", "管理区分名 にスペースのみの登録はできません。", "no_sp_name");

/****************************/
//登録ボタン押下処理
/****************************/
if($_POST["form_entry_button"] == "登　録"){

    /****************************/
    //POST情報取得
    /****************************/
    $product_cd        = $_POST["form_product_cd"];                                   //管理区分CD
    $product_name      = $_POST["form_product_name"];                                 //管理区分名
    $product_note      = $_POST["form_product_note"];                                 //備考
    $update_flg        = $_POST["update_flg"];

    /***************************/
    //管理区分コード整形
    /***************************/
    $product_cd = str_pad($product_cd, 4, 0, STR_PAD_LEFT);

    $sql  = "SELECT";
    $sql .= "   product_cd";
    $sql .= " FROM";
    $sql .= "   t_product";
    $sql .= " WHERE";
    $sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
    $sql .= "   AND";
    $sql .= "   product_cd = '$product_cd'";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $num = pg_num_rows($result);


    //使用済みエラー
    if($num > 0 && ($update_flg != true || ($update_flg == true && $def_product_cd != $product_cd))){
        $form->setElementError("form_product_cd","既に使用されている管理区分コードです。");
    }

    //コード体系
    if($product_cd < 5000 || $product_cd >= 9000){
        $form->setElementError("form_product_cd","管理区分コードに5000〜8999以外は利用できません。");
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

            $insert_sql  = "INSERT INTO t_product(";
            $insert_sql .= "    product_id,";
            $insert_sql .= "    product_cd,";
            $insert_sql .= "    product_name,";
            $insert_sql .= "    note,";
            $insert_sql .= "    public_flg,";
            $insert_sql .= "    shop_id";
            $insert_sql .= ")VALUES(";
            $insert_sql .= "    (SELECT COALESCE(MAX(product_id), 0)+1 FROM t_product),";
            $insert_sql .= "    '$product_cd',";
            $insert_sql .= "    '$product_name',";
            $insert_sql .= "    '$product_note',";
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
            $result = Log_Save( $conn, "product", "1", $product_cd, $product_name);
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
            $insert_sql .= "    t_product";
            $insert_sql .= " SET";
            $insert_sql .= "    product_cd = '$product_cd',";
            $insert_sql .= "    product_name = '$product_name',";
            $insert_sql .= "    note = '$product_note'";
            $insert_sql .= " WHERE";
            $insert_sql .= "    product_id = $get_product_id";
            $insert_sql .= ";";

            $result = Db_Query($conn, $insert_sql);
            if($result === false){
                Db_Query($conn, "ROLLBACK");
                exit;
            }

            //登録した情報をログに残す
            $result = Log_Save( $conn, "product", "2", $product_cd, $product_name);
            //失敗した場合はロールバック
            if($result === false){
                Db_Query($conn, "ROLLBACK");
                exit;
            }
            $message = "変更しました。";
        }
        Db_Query($conn, "COMMIT");

        $set_data["form_product_cd"]    = "";                                   //管理区分CD
        $set_data["form_product_name"]  = "";                                 //管理区分名
        $set_data["form_product_note"]  = "";                                 //備考  
        $set_data["update_flg"]         = "";

        $form->setConstants($set_data);
    }

}


/*****************************
//一覧作成
/*****************************/
$sql  = "SELECT";
$sql .= "   DISTINCT";
$sql .= "   product_cd,";
$sql .= "   product_id,";
$sql .= "   product_name,";
$sql .= "   note";
$sql .= " FROM";
$sql .= "   t_product";
$sql .= " WHERE";
$sql .= "   public_flg = 't'";
$sql .= "   AND";
$sql .= "   accept_flg = '1'";
$sql .= "   OR";
$sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
$sql .= "   ORDER BY product_cd";
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
	$sql .= "   product_cd,";
	$sql .= "   product_id,";
	$sql .= "   product_name,";
	$sql .= "   note";
	$sql .= " FROM";
	$sql .= "   t_product";
	$sql .= " WHERE";
	$sql .= "   public_flg = 't'";
	$sql .= "   AND";
	$sql .= "   accept_flg = '1'";
	$sql .= "   OR";
	$sql .= ($group_kind == "2") ? " shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
	$sql .= "   ORDER BY product_cd";
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

    $csv_file_name = "管理区分マスタ".date("Ymd").".csv";
    $csv_header = array(
        "管理区分コード",
        "管理区分名",
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
