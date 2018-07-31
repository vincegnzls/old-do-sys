<?php
/***********************
変更履歴
    ・URLを表示するように変更

    (2006-08-07)(kaji)
    ・直営以外のユーザでクエリエラーが出るのでスペースを入れる

***********************/

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-12-07      ban_0097    suzuki      表示ボタン押下時に行NO初期化
 *  2006-12-14      ban_0098    kaji        状態が無効の本部商品が全件数に含まれていたのを含まないように
 *  2007-01-16      仕様変更    watanabe-k  ソート順の変更
 *  2007-05-15      仕様変更    watanabe-k  ＣＳＶを作成 
 *  
 *                  6/28辺りでfukudaがソート機能を付けたと思われる
 *  
 *  2007/09/05      バグ        kajioka-h   $_SESSION["group_kind"]を$group_kindに入れてないのに$group_kindを使っているバグ修正
 *  2009-10-08      仕様変更    hashimoto-y 在庫管理フラグをショップ別商品情報テーブルに変更
 *  2010-05-13      Rev.1.5     hashimoto-y 初期表示に検索項目だけ表示する修正
 *  2011-06-23      バグ修正    aoyama-n    CSVに仕入原価が表示されない不具合修正
 *
*/

$page_title = "商品マスタ";

// 環境設定ファイル
require_once("ENV_local.php");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// テンプレート関数をレジスト
$smarty->register_function("Make_Sort_Link_Tpl", "Make_Sort_Link_Tpl");

// DBに接続
$db_con = Db_Connect();

// 権限チェック
$auth = Auth_Check($db_con);


/****************************/
// 検索条件復元関連
/****************************/
// 検索フォーム初期値配列
$ary_form_list = array(
    "form_goods_cd"     => "",
    "form_g_goods"      => "",
    "form_product"      => "",
    "form_g_product"    => "",
    "form_goods_name"   => "",
    "form_goods_cname"  => "",
    "form_stock_only"   => "3",
    "form_state"        => "1",
    "form_output_type"  => "1",
    "form_display_num"  => "1",
);

// 検索条件復元
Restore_Filter2($form, "sale", "form_show_button", $ary_form_list);


/****************************/
// 外部変数取得
/****************************/
$shop_id = $_SESSION["client_id"];
$group_kind = $_SESSION["group_kind"];
#2011-06-23 aoyama-n
$rank_cd  = $_SESSION["rank_cd"];


/****************************/
// 初期値設定
/****************************/
$form->setDefaults($ary_form_list);

$limit          = 100;      // LIMIT
$offset         = "0";      // OFFSET
$total_count    = "0";      // 全件数
$page_count     = ($_POST["f_page1"] != null) ? $_POST["f_page1"] : "1";    // 表示ページ数


/****************************/
// フォーム生成
/****************************/
// 商品コード
$form->addElement("text", "form_goods_cd", "", "size=\"10\" maxLength=\"8\" class=\"ime_disabled\" $g_form_option");

// Ｍ区分
$item   =   null;
$item   =   Select_Get($db_con, "g_goods");
$form->addElement("select", "form_g_goods", "", $item);

// 管理区分
$item   =   null;
$item   =   Select_Get($db_con, "product");
$form->addElement("select", "form_product", "", $item);

// 商品分類
$item   =   null;
$item   =   Select_Get($db_con, "g_product");
$form->addElement("select", "form_g_product", "", $item);

// 商品名
$form->addElement("text", "form_goods_name", "", "size=\"56\" $g_form_option");

// 略記
$form->addElement("text", "form_goods_cname", "", "size=\"34\" $g_form_option");

// 在庫限り品
$obj    =   null;
$obj[]  =   $form->createElement("radio", null, null, "在庫限り",       "1");
$obj[]  =   $form->createElement("radio", null, null, "在庫限りでない", "2");
$obj[]  =   $form->createElement("radio", null, null, "全て",           "3");
$form->addGroup($obj, "form_stock_only", "");

// 状態
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "有効", "1");
$obj[]  =&  $form->createElement("radio", null, null, "無効", "2");
$obj[]  =&  $form->createElement("radio", null, null, "全て", "3");
$form->addGroup($obj, "form_state", "");

// 出力形式
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "画面", "1");
$obj[]  =&  $form->createElement("radio", null, null, "CSV",  "2");
$form->addGroup($obj, "form_output_type", "出力形式");

// 表示件数
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "100件表示", "1");
$obj[]  =&  $form->createElement("radio", null, null, "全件表示",  "2");
$form->addGroup($obj, "form_display_num", "表示件数");

// 属性区分絞り込みリンク
$ary_narrow_item = array(
    "nl_attri_div1"     => "商品",
    "nl_attri_div2"     => "部品",
    "nl_attri_div3"     => "管理",
    "nl_attri_div4"     => "道具・他",
    "nl_attri_div5"     => "保険",
    "nl_attri_div6"     => "全て",
);
AddElement_Sort_Link($form, $ary_narrow_item, "nl_attri_div6", "hdn_attri_div");

// 属性区分絞り込み条件（前回値保持用）
$form->addElement("hidden", "hdn_attri_div2");

// ソートリンク
$ary_sort_item = array(
    "sl_goods_cd"       => "商品コード",
    "sl_g_goods"        => "Ｍ区分",
    "sl_product"        => "管理区分",
    "sl_g_product"      => "商品分類",
    "sl_goods_name"     => "商品名",
    "sl_goods_cname"    => "略記",
    "sl_attribute"      => "属性区分",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_goods_cd");

// 表示ボタン
$form->addElement("submit", "form_show_button", "表　示");

// クリアボタン
$form->addElement("button", "form_clear_button", "クリア", "onClick=\"location.href='".$_SERVER["PHP_SELF"]."'\"");

// ヘッダ部リンクボタン
$ary_h_btn_list = array("登録画面" => "./2-1-221.php", "変更・一覧" => $_SERVER["PHP_SELF"]);
Make_H_Link_Btn($form, $ary_h_btn_list);


/****************************/
// 表示ボタン押下時
/****************************/
if ($_POST["form_display"] != null){

    /****************************/
    // エラーチェック
    /****************************/
    // なし

    /****************************/
    // エラーチェック結果集計
    /****************************/
    // チェック適用
    $test = $form->validate();

    // 結果をフラグに
    $err_flg = (count($form->_errors) > 0) ? true : false;

}


/****************************/
// 1. 表示ボタン押下＋エラーなし時
// 2. ページ切り替え時、その他のPOST時
/****************************/
if (($_POST["form_show_button"] != null && $err_flg != true) || ($_POST != null && $_POST["form_show_button"] == null)){

    // POSTデータを変数にセット
    $goods_cd       = $_POST["form_goods_cd"];
    $g_goods        = $_POST["form_g_goods"];
    $product        = $_POST["form_product"];
    $g_product      = $_POST["form_g_product"];
    $goods_name     = $_POST["form_goods_name"];
    $goods_cname    = $_POST["form_goods_cname"];
    $stock_only     = $_POST["form_stock_only"];
    $state          = $_POST["form_state"];
    $output_type    = $_POST["form_output_type"];
    $display_num    = $_POST["form_display_num"];

    $attri_div      = $_POST["hdn_attri_div"];

    $sort_col       = $_POST["hdn_sort_col"];

/****************************/
// 3. 初期表示時
/****************************/
}elseif ($_POST == null){

    $stock_only     = "3";
    $state          = "1";
    $output_type    = "1";
    $display_num    = "1";

    $attri_div      = "nl_attri_div6";

    $sort_col       = "sl_goods_cd";

}


#2010-05-12 hashimoto-y
if($_POST["form_show_button"]=="表　示" || $_POST != null){


/****************************/
// 一覧データ取得条件作成
/****************************/
if ($err_flg != true){

    $sql = null;

    // 商品コード
    $sql .= ($goods_cd != null) ? "AND t_goods.goods_cd LIKE '$goods_cd%' \n" : null;
    // Ｍ区分
    $sql .= ($g_goods != null) ? "AND t_goods.g_goods_id = $g_goods \n" : null;
    // 管理区分
    $sql .= ($product != null) ? "AND t_goods.product_id = $product \n" : null;
    // 商品分類
    $sql .= ($g_product != null) ? "AND t_goods.g_product_id = $g_product \n" : null;
    // 商品名
    $sql .= ($goods_name != null) ? "AND t_goods.goods_name LIKE '%$goods_name%' \n" : null;
    // 略記
    $sql .= ($goods_cname != null) ? "AND t_goods.goods_cname LIKE '%$goods_cname%' \n" : null;
    // 在庫限り品
    if ($stock_only == "1"){
        $sql .= "AND t_goods.stock_only = '1' \n";
    }else
    if ($stock_only == "2"){
        $sql .= "AND t_goods.stock_only != '1' \n";
    }
    // 状態
    // 直営は本部有効商品と直営商品が対象
    if ($group_kind == "2"){
        if ($state == "1"){
            $sql .= "AND t_goods.state IN ('1', '3') \n";
        }else
        if ($state == "2"){
            $sql .= "AND t_goods.state = '2' AND t_goods.shop_id IN (".Rank_Sql().") \n";
        }else{  
            $sql .= "AND (t_goods.state IN ('1', '3') OR (t_goods.state = '2' AND t_goods.shop_id IN (".Rank_Sql()."))) \n";
        }
    // FCは本部有効商品とFC商品が対象
    }else{
        if ($state == "1"){
            $sql .= "AND t_goods.state = '1' \n";
        }else
        if ($state == "2"){
            $sql .= "AND t_goods.state = '2' AND t_goods.shop_id = $shop_id \n";
        }else{  
            $sql .= "AND (t_goods.state = '1' OR (t_goods.state = '2' AND t_goods.shop_id = $shop_id)) \n";
        }
    }

    // 変数詰め替え
    $where_sql = $sql;


    // 属性区分
    switch ($attri_div){
        case "nl_attri_div1":
            $sql .= "AND t_goods.attri_div = '1' \n";
            break;
        case "nl_attri_div2":
            $sql .= "AND t_goods.attri_div = '2' \n";
            break;
        case "nl_attri_div3":
            $sql .= "AND t_goods.attri_div = '3' \n";
            break;
        case "nl_attri_div4":
            $sql .= "AND t_goods.attri_div = '4' \n";
            break;
        case "nl_attri_div5":
            $sql .= "AND t_goods.attri_div = '5' \n";
            break;
    }

    // 変数詰め替え
    $attri_sql = $sql;


    $sql = null;

    // ソート順
    switch ($sort_col){
        // 商品コード
        case "sl_goods_cd":
            $sql .= "   t_goods.goods_cd, \n";
            $sql .= "   t_g_goods.g_goods_cd, \n";
            $sql .= "   t_product.product_cd, \n";
            $sql .= "   t_g_product.g_product_cd \n";
            break;
        // Ｍ区分
        case "sl_g_goods":
            $sql .= "   t_g_goods.g_goods_cd, \n";
            $sql .= "   t_goods.goods_cd \n";
            break;
        // 管理区分
        case "sl_product":
            $sql .= "   t_product.product_cd, \n";
            $sql .= "   t_goods.goods_cd \n";
            break;
        // 商品分類
        case "sl_g_product":
            $sql .= "   t_g_product.g_product_cd, \n";
            $sql .= "   t_goods.goods_cd \n";
            break;
        // 商品名
        case "sl_goods_name":
            $sql .= "   t_goods.goods_name, \n";
            $sql .= "   t_goods.goods_cd \n";
            break;
        // 略記
        case "sl_goods_cname":
            $sql .= "   t_goods.goods_cname, \n";
            $sql .= "   t_goods.goods_cd \n";
            break;
        // 属性区分
        case "sl_attribute":
            $sql .= "   t_goods.attri_div, \n";
            $sql .= "   t_goods.goods_cd \n";
            break;
    }

    // 変数詰め替え
    $order_sql = $sql;

}


/****************************/
// 表示データ作成
/****************************/
if($output_type == "1"){

    /****************************/
    // データに表示する全件数取得
    /****************************/
    $sql  = "SELECT \n";
    $sql .= "   t_goods.goods_cd, \n";
    $sql .= "   t_goods.goods_id, \n";
    $sql .= "   t_goods.goods_name, \n";
    $sql .= "   t_goods.goods_cname, \n";
    $sql .= "   t_product.product_name, \n";
    $sql .= "   t_g_goods.g_goods_name, \n";
    $sql .= "   CASE t_goods.attri_div \n";
    $sql .= "       WHEN '1' THEN '商品' \n";
    $sql .= "       WHEN '2' THEN '部品' \n";
    $sql .= "       WHEN '3' THEN '管理' \n";
    $sql .= "       WHEN '4' THEN '道具・他' \n";
    $sql .= "   END, \n";
    $sql .= "   CASE t_goods.state \n";
    $sql .= "       WHEN '1' THEN '有効' \n";
    $sql .= "       WHEN '2' THEN '無効' \n";
    $sql .= "       WHEN '3' THEN '有効（直営）' \n";
    $sql .= "   END, \n";
    $sql .= "   t_goods.url, \n";
	$sql .= "   t_g_product.g_product_name,  \n";
	$sql .= "   t_goods.stock_only,  \n";
    $sql .= "   t_goods.no_change_flg \n";
    $sql .= "FROM \n";
    $sql .= "   t_goods, \n";
    $sql .= "   t_goods_info, \n";
    $sql .= "   t_g_goods, \n";
    $sql .= "   t_product, \n";
	$sql .= "   t_g_product  \n";
    $sql .= "WHERE";
    $sql .= "   t_goods_info.shop_id = $shop_id  \n";
    $sql .= "AND \n";
    $sql .= "   t_goods_info.goods_id = t_goods.goods_id \n";
    $sql .= "AND \n";
    $sql .= "   t_goods.g_goods_id = t_g_goods.g_goods_id \n";
    $sql .= "AND \n";
    $sql .= "   t_goods.product_id = t_product.product_id \n";
	$sql .= "AND \n";
	$sql .= "   t_goods.g_product_id = t_g_product.g_product_id \n";
    $sql .= "AND \n";
    $sql .= "   t_goods.compose_flg = 'f' \n";
    $sql .= "AND \n";
    $sql .= "   t_goods.accept_flg = '1' \n";
    $sql .= $where_sql;
    $sql .= $attri_sql;
    $sql .= "ORDER BY \n";
    $sql .= $order_sql;

    $res            = Db_Query($db_con, $sql.";");
    $total_count    = pg_num_rows($res);

    // 属性区分の絞り込み条件をhidden（前回値保持用）にセット
    $hdn_set["hdn_attri_div2"] = $_POST["hdn_attri_div"];
    $form->setConstants($hdn_set);

    // 属性区分リンク検索が押下された場合（hidden（前回値保持用）と、POSTされた絞り込み条件が異なる場合）
    // かつ、属性区分リンク検索の前回値のPOSTがある場合
    if ($_POST["hdn_attri_div"] != $_POST["hdn_attri_div2"] && $_POST["hdn_attri_div2"] != null){
        $page_count = 1;
    }

    // 表示件数
    switch ($display_num){
        case "1":
            $limit = "100";
            break;
        case "2":
            $limit = $total_count;
            break;
    }

    // 取得開始位置
    $offset = ($page_count != null) ? ($page_count - 1) * $limit : "0";

    // ページ内データ取得
    $limit_offset   = ($limit != null) ? "LIMIT $limit OFFSET $offset " : null;
    $res            = Db_Query($db_con, $sql.$limit_offset.";");
    $match_count    = pg_num_rows($res);
    $goods_data     = Get_Data($res, $output_type);

    // 商品アルバムをエスケープ
    for ($i = 0; $i < $match_count; $i++){
        $goods_data[$i][8] = addslashes($goods_data[$i][8]);
    }

// 表示形式がCSVの時
}elseif ($output_type == "2"){

    $sql  = "SELECT \g_goods_idn";
    $sql .= "   CASE t_goods.state  \n";                // 状態
    $sql .= "       WHEN '1' THEN '有効' \n";
    $sql .= "       WHEN '2' THEN '無効' \n";
    $sql .= "       WHEN '3' THEN '有効' \n";
    $sql .= "   END AS state, \n";
    $sql .= "   CASE t_goods.rental_flg \n";
    $sql .= "       WHEN 't' THEN 'あり' \n";
    $sql .= "       ELSE 'なし' \n";
    $sql .= "   END AS rental_flg, \n";                 // RtoR
    $sql .= "   CASE t_goods.serial_flg \n";
    $sql .= "       WHEN 't' THEN 'あり' \n";
    $sql .= "       WHEN 'f' THEN 'なし' \n";
    $sql .= "   END AS serial_flg, \n";                 // シリアル管理
    $sql .= "   t_goods.goods_cd, \n";                  // 商品コード
    $sql .= "   t_g_goods.g_goods_cd, \n";              // Ｍ区分コード
    $sql .= "   t_g_goods.g_goods_name, \n";            // Ｍ区分名
    $sql .= "   t_product.product_cd, \n";              // 管理区分コード
    $sql .= "   t_product.product_name, \n";            // 管理区分名
    $sql .= "   t_g_product.g_product_cd, \n";          // 商品分類コード
    $sql .= "   t_g_product.g_product_name, \n";        // 商品分類  分名
    $sql .= "   t_goods.goods_name, \n";                // 商品名
    $sql .= "   t_goods.goods_cname, \n";               // 略称
    $sql .= "   CASE t_goods.attri_div \n";             // 属性区分
    $sql .= "       WHEN '1' THEN '製品' \n";
    $sql .= "       WHEN '2' THEN '部品' \n";
    $sql .= "       WHEN '3' THEN '管理' \n";
    $sql .= "       WHEN '4' THEN '道具・他' \n";
    $sql .= "       WHEN '5' THEN '保険' \n";
    $sql .= "   END AS attri_div, \n";
    $sql .= "   t_goods.url, \n";                       // 商品アルバム
    $sql .= "   CASE t_goods.mark_div \n";              // マーク
    $sql .= "       WHEN '1' THEN '汎用' \n";
    $sql .= "       WHEN '2' THEN 'ＧＭ' \n";
    $sql .= "       WHEN '3' THEN 'ＥＣ' \n";
    $sql .= "       WHEN '4' THEN 'Ｇ適' \n";
    $sql .= "       WHEN '5' THEN '劇物' \n";
    $sql .= "   END AS mark_div, \n";
    $sql .= "   t_goods.unit, \n";                      // 単位
    $sql .= "   t_goods.in_num, \n";                    // 入数
    $sql .= "   CASE t_goods.public_flg \n";            // 仕入先1コード
    $sql .= "       WHEN 't' THEN t_head_client.client_cd1 \n";
    $sql .= "       WHEN 'f' THEN t_client.client_cd1 \n";
    $sql .= "   END AS client_cd, \n";
    $sql .= "   CASE t_goods.public_flg \n";            // 仕入先1名
    $sql .= "       WHEN 't' THEN t_head_client.client_name \n";
    $sql .= "       WHEN 'f' THEN t_client.client_name \n";
    $sql .= "   END AS client_name, \n";
    $sql .= "   CASE t_goods.sale_manage \n";           // 販売管理
    $sql .= "       WHEN '1' THEN '有' \n";
    $sql .= "       WHEN '2' THEN '無' \n";
    $sql .= "   END AS sale_manage, \n";
    #2009-10-08 hashimoto-y
    #$sql .= "   CASE t_goods.stock_manage \n";          // 在庫管理
    $sql .= "   CASE t_goods_info.stock_manage \n";          // 在庫管理
    $sql .= "       WHEN '1' THEN '有' \n";
    $sql .= "       WHEN '2' THEN '無' \n";
    $sql .= "   END AS stock_manage, \n";
    $sql .= "   CASE  t_goods.stock_only \n";           // 在庫限り品
    $sql .= "       WHEN '1' THEN '○' \n";
    $sql .= "       ELSE '×' \n";
    $sql .= "   END AS stock_only, \n";
    $sql .= "   t_goods_info.order_point, \n";          // 発注点
    $sql .= "   t_goods_info.order_unit, \n";           // 発注単位数
    $sql .= "   t_goods_info.lead, \n";                 // リードタイム
    $sql .= "   CASE t_goods.name_change \n";           // 品名変更
    $sql .= "       WHEN '1' THEN '変更可' \n";
    $sql .= "       WHEN '2' THEN '変更不可' \n";
    $sql .= "   END AS name_change, \n";
    $sql .= "   CASE t_goods.tax_div \n";               // 課税区分
    $sql .= "       WHEN '1' THEN '課税' \n";
    $sql .= "       WHEN '3' THEN '非課税' \n";
    $sql .= "   END AS tax_div, \n";
    $sql .= "   t_goods_info.note, \n";                 // 備考
    $sql .= "   t_goods.goods_id \n";
    $sql .= "FROM \n";
    $sql .= "   t_goods_info \n";
    $sql .= "   INNER JOIN t_goods                  ON  t_goods.goods_id = t_goods_info.goods_id \n";
    $sql .= "                                       AND t_goods_info.shop_id = $shop_id \n";
    $sql .= "   INNER JOIN t_product                ON  t_goods.product_id = t_product.product_id \n";
    $sql .= "   INNER JOIN t_g_goods                ON  t_goods.g_goods_id = t_g_goods.g_goods_id \n";
    $sql .= "   INNER JOIN t_g_product              ON  t_goods.g_product_id = t_g_product.g_product_id \n";
    $sql .= "   LEFT JOIN t_client                  ON  t_goods_info.supplier_id = t_client.client_id \n";
    $sql .= "                                       AND t_client.client_div = '2' \n";
    $sql .= "   LEFT JOIN t_client AS t_head_client ON  t_goods_info.shop_id = t_head_client.shop_id \n";
    $sql .= "                                       AND t_head_client.head_flg = 't' \n";
    $sql .= "WHERE\n";
    $sql .= "   t_goods.compose_flg = 'f' \n";
    $sql .= "AND \n";
    $sql .= "   t_goods.accept_flg = '1' \n";
    $sql .= $where_sql;
    $sql .= "ORDER BY \n";
    $sql .= "   t_goods.goods_cd \n";
    $sql .= ";";

    $res        = Db_Query($db_con, $sql);
    $goods_data = Get_Data($res, $output_type);
    $data_num   = pg_num_rows($res);

    //CSV出力
    //CSV作成
    $csv_file_name = "商品マスタ".date("Ymd").".csv";
    $csv_header = array(
        "状態",
        "RtoR",
        "シリアル管理",
        "商品コード", 
        "Ｍ区分コード",
        "Ｍ区分名",
        "管理区分コード",
        "管理区分名",
        "商品分類コード",
        "商品分類名",
        "商品名",
        "略称",
        "属性区分",
        "商品アルバム",
        "マーク",
        "単位",
        "入数",
        "仕入先コード",
        "仕入先名",
        "販売管理",
        "在庫管理",
        "在庫限り品",
        "発注点",
        "発注単位数",
        "リードタイム",
        "品名変更",
        "課税区分",
        "備考",
        "仕入原価",
        "在庫原価",
        "営業原価",
        "標準価格",
    );

    for($i = 0; $i < $data_num; $i++){
        //単価を抽出
        $sql  = "SELECT \n";
        $sql .= "   t_price.r_price, \n";
        $sql .= "   CASE rank_cd \n";
        $sql .= "       WHEN '2' THEN '3' \n";
        $sql .= "       WHEN '3' THEN '2' \n";
        $sql .= "       WHEN '4' THEN '4' \n";
        $sql .= "       ELSE '1' \n";
        $sql .= "   END AS sort \n"; 
        $sql .= "FROM \n";
        $sql .= "   t_price \n";
        $sql .= "WHERE \n";
        $sql .= "   goods_id = ".$goods_data[$i][28]." \n";
        $sql .= "   AND \n";
        $sql .= "   (shop_id = $shop_id \n";
        $sql .= "   OR ";
        $sql .= "   (rank_cd IN ('$rank_cd', '4') AND shop_id =1)";
        $sql .= "   ) \n";
        $sql .= "ORDER BY \n";
        $sql .= "   sort \n"; 
        $sql .= ";";

        $res = Db_Query($db_con, $sql);

        unset($goods_data[$i][28]);
        $num = pg_num_rows($res);
        for($j = 0; $j < $num; $j++){
            $goods_data[$i][] = pg_fetch_result($res, $j, 0);
        }
    }

    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($goods_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;

}


#2010-05-12 hashimoto-y
$display_flg = true;
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
// 全件数取得(ヘッダ部出力用)
$sql  = "SELECT \n";
$sql .= "   COUNT(t_goods_info.goods_id) \n";
$sql .= "FROM \n";
$sql .= "   t_goods_info, \n";
$sql .= "   t_goods \n";
$sql .= "WHERE \n";
$sql .= "   t_goods_info.shop_id = $shop_id \n";
$sql .= "AND \n";
$sql .= "   t_goods_info.goods_id = t_goods.goods_id \n";
$sql .= "AND \n";
$sql .= "   t_goods.compose_flg = 'f' \n";
$sql .= "AND \n";
$sql .= "   t_goods.accept_flg = '1' \n";
$sql .= "AND \n";
$sql .= "   ( \n";
if ($group_kind == "2"){
$sql .= "       t_goods.state IN ('1', '3') \n";
$sql .= "       OR \n";
$sql .="        (t_goods.state = '2' AND t_goods.shop_id = $shop_id) \n";
}else{
$sql .= "       t_goods.state = '1' \n";
$sql .= "       OR \n";
$sql .= "       t_goods.shop_id = $shop_id \n";
}
$sql .= "    ) \n";
$sql .= "AND \n";
$sql .= "   length(t_goods.goods_cd) = 8 \n";
$sql .= ";";
$res  = Db_Query($db_con, $sql);
$t_count = pg_fetch_result($res, 0, 0);

// 全件数取得(ヘッダ部出力用)
$sql  = "SELECT \n";
$sql .= "   COUNT(t_goods_info.goods_id) \n";
$sql .= "FROM \n";
$sql .= "   t_goods_info \n";
$sql .= "   INNER JOIN t_goods ON  t_goods_info.goods_id = t_goods.goods_id \n";
$sql .="                       AND t_goods_info.shop_id = $shop_id \n";
$sql .= "WHERE \n";
$sql .= ($group_kind == "2") ? "   t_goods.state IN ('1', '3') \n" : "   t_goods.state = '1' \n";
$sql .= "AND \n";
$sql .= "   t_goods.accept_flg = '1' \n";
$sql .= "AND \n";
$sql .= "   t_goods.compose_flg = 'f' \n";
$sql .= "AND \n";
$sql .= "   length(t_goods.goods_cd) = 8 \n";
$sql .= ";";
$res  = Db_Query($db_con, $sql);
$dealing_count = pg_fetch_result($res, 0, 0);

// 属性区分別件数
for ($i = 1; $i <= 5; $i++){
    $sql  = "SELECT \n";
    $sql .= "   COUNT(t_goods.goods_id) \n";
    $sql .= "FROM \n";
    $sql .= "   t_goods \n";
    $sql .= "   INNER JOIN t_goods_info ON t_goods.goods_id = t_goods_info.goods_id \n";
    $sql .= "   INNER JOIN t_g_goods    ON t_goods.g_goods_id = t_g_goods.g_goods_id \n";
    $sql .= "   INNER JOIN t_product    ON t_goods.product_id = t_product.product_id \n";
	$sql .= "   INNER JOIN t_g_product  ON t_goods.g_product_id = t_g_product.g_product_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_goods.attri_div = '$i' \n";
    $sql .= "AND \n";
    $sql .= "   t_goods.accept_flg = '1' \n";
    $sql .= "AND \n";
    $sql .= "   t_goods_info.shop_id = $shop_id \n";
    $sql .= $where_sql;
    $sql .= ";";
    $res = Db_Query($db_con, $sql);
    $attri_div_num[] = pg_fetch_result($res, 0, 0);
}

$page_title .= "(有効".$dealing_count."件/全".$t_count."件)";
$page_title .= Print_H_Link_Btn($form, $ary_h_btn_list);
$page_header = Create_Header($page_title);


/****************************/
// ページ作成
/****************************/
$html_page  = Html_Page2($total_count, $page_count, 1, $limit);
$html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

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
    "match_count"   => "$match_count",
    "html_page"     => "$html_page",
    "order_msg"     => "$order_msg",
    "html_page2"    => "$html_page2",
    "url"           => ALBUM_DIR,
    "display_num"   => "$display_num",
    "display_flg"   => "$display_flg"
));

$smarty->assign("attri_div",$attri_div_num);
$smarty->assign("page_data",$goods_data);

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER["PHP_SELF"].".tpl"));

?>
