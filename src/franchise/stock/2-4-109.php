<?php

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/12/06      ban_0024    suzuki      日付のゼロ埋め追加
 *  2006/12/06      ban_0026    suzuki      日付エラー判定修正
 *  2007/02/22                  watanabe-k  不要機能を削除
 *  2007-04-17      B0702-041   kajioka-h   ウィンドウタイトルに画面切替ボタンのHTMLが出力されていたのを修正
 *  2009-07-23                  aoyama-n    在庫管理しない商品が表示される不具合修正
 *  2009/10/12                  hashimoto-y 在庫管理フラグをショップ別商品情報テーブルに変更
 *  2009/10/13      rev.1.3     kajioka-h   在庫数0のものは表示しない
 *
 *
*/

$page_title = "滞留在庫一覧";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm( "$_SERVER[PHP_SELF]","POST");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);

/****************************/
//外部変数取得
/****************************/
$shop_id    = $_SESSION["client_id"];                     //ショップID
$fc_shop_id = $_SESSION["fc_client_id"];                  //FCショップID
$shop_div   = $_SESSION["shop_div"];                      //本社・支社区分
//$shop_gid   = $_SESSION["shop_gid"];                      //ショップグループID

//本社の場合だけ表示
if($shop_div=='1'){
    //事業所
    $select_value[null] = null;
    $select_value = Select_Get($db_con,'fcshop');
    $form->addElement('select', 'form_cshop','セレクトボックス', $select_value,$g_form_option_select);
}

/****************************/
//初期値設定
/****************************/
//担当者
$def_data["form_cshop"]        = $fc_shop_id;
//$def_data["form_output_type"]  = "1";                    //表示形式
$def_data["form_sale_buy"]     = "1";                   //入出庫区分
$form->setDefaults($def_data);

/****************************/
//フォーム作成
/****************************/
//表示形式
/*
$form_output_type[] =& $form->createElement( "radio",NULL,NULL, "画面","1");
$form_output_type[] =& $form->createElement( "radio",NULL,NULL, "帳票","2");
$form->addGroup($form_output_type, "form_output_type", "出力形式");
*/

//対象在庫
$form_stock_date[] =& $form->createElement(
    "text","y","テキストフォーム","size=\"4\" maxLength=\"4\" 
    onkeyup=\"changeText(this.form,'form_stock_date[y]','form_stock_date[m]',4)\" 
    onFocus=\"onForm_today(this,this.form,'form_stock_date[y]','form_stock_date[m]','form_stock_date[d]')\" 
    onBlur=\"blurForm(this)\""
);
$form_stock_date[] =& $form->createElement(
    "static","","","-"
);
$form_stock_date[] =& $form->createElement(
    "text","m","テキストフォーム","size=\"1\" maxLength=\"2\"
     onkeyup=\"changeText(this.form,'form_stock_date[m]','form_stock_date[d]',2)\" 
     onFocus=\"onForm_today(this,this.form,'form_stock_date[y]','form_stock_date[m]','form_stock_date[d]')\" 
     onBlur=\"blurForm(this)\""
);
$form_stock_date[] =& $form->createElement(
    "static","","","-"
    );
$form_stock_date[] =& $form->createElement(
    "text","d","テキストフォーム","size=\"1\" maxLength=\"2\" 
    onFocus=\"onForm_today(this,this.form,'form_stock_date[y]','form_stock_date[m]','form_stock_date[d]')\" 
    onBlur=\"blurForm(this)\""
);
$form->addGroup( $form_stock_date,"form_stock_date","対象在庫");

$form->addElement(
    "text","form_over_day","","size=\"4\" maxLength=\"4\" $g_form_option
    style=\" text-align: right\""
);
$form_sale_buy[] =& $form->createElement( 
    "radio",NULL,NULL, "売上・仕入なし","1"
);
$form_sale_buy[] =& $form->createElement( 
    "radio",NULL,NULL, "売上なし","2"
);
$form_sale_buy[] =& $form->createElement( 
    "radio",NULL,NULL, "仕入なし","4"
);
$form->addGroup($form_sale_buy, "form_sale_buy", "対象在庫");

//Ｍ区分
$select_value = Select_Get($db_con,'g_goods');
$form->addElement('select', 'form_g_goods', '', $select_value,$g_form_option_select);

//製品区分
$select_value = Select_Get($db_con,'product');
$form->addElement('select', 'form_product', '', $select_value,$g_form_option_select);

//商品コード
$form->addElement(
    "text","form_goods_cd","","size=\"10\" maxLength=\"8\" 
    $g_form_option"
);

//商品名
$form->addElement(
    "text","form_goods_cname","","size=\"34\" maxLength=\"30\" 
    $g_form_option"
);

//倉庫
$select_value = Select_Get($db_con,'ware');
$form->addElement('select', 'form_ware', '', $select_value,$g_form_option_select);

//表示ボタン
/*
$form->addElement("submit","form_show_button","表　示","
        onClick=\"javascript:Which_Type('form_output_type','2-4-110.php','# ');\""
);
*/
$form->addElement("submit","form_show_button","表　示","");

//クリアボタン
$form->addElement(
    "button","form_clear_button","クリア",
    "onClick=\"javascript:location.href('$_SERVER[PHP_SELF]')\""
);

// 在庫照会リンクボタン
$form->addElement("button", "4_101_button", "在庫照会", "onClick=\"location.href('./2-4-101.php');\"");

// 在庫受払リンクボタン
$form->addElement("button", "4_105_button", "在庫受払", "onClick=\"location.href('./2-4-105.php');\"");

// 滞留在庫一覧リンクボタン
$form->addElement("button", "4_109_button", "滞留在庫一覧", "$g_button_color onClick=\"javascript:location.href('".$_SERVER["PHP_SELF"]."')\"");

/****************************/
//表示形式調整
/****************************/
function Get_Stock_Data($result){
    $i            = 0;                     //行番号
    $result_count = pg_numrows($result);   //要素数
    $goods_count  = 1;                     //出荷予定日結合数
    $css_color    = false;                 //受注番号ごとに色を変更

    for($i = 0; $i < $result_count; $i++){
        //色を交互に付ける為、前の色と比較する
        if($css_color==false){
            $html_row = "<tr class=\"Result1\">";
            $css_color = true;
        }else{
            $html_row = "<tr class=\"Result2\">";
            $css_color = false;
        }
        $stock_data[$i] = @pg_fetch_array ($result, $i, PGSQL_NUM);
        for($j=0;$j<count($stock_data[$i]);$j++){
            //Ｍ区分
            if($j==0){
                //最初の行かＭ区分が変わった場合に、配列にＭ区分名代入
                if($i==0 || $aord_cd1 != $stock_data[$i][$j]){
                    $aord_cd1 = $stock_data[$i][$j];
                    $ord_array = $stock_data[$i][$j];
                }else{
                    //前の行と同じ場合省略する
                    $ord_array = "";
                }
            }else{
                //数値判定
                if(is_numeric($stock_data[$i][$j])){
					if($j==5 || $j==6 ){
                    $stock_data[$i][$j] = number_format($stock_data[$i][$j],2);
                    }else if($j==4 || $j==7 ){
                    $stock_data[$i][$j] = number_format($stock_data[$i][$j]);
                    }
                }
                $ord_array = $stock_data[$i][$j];
            }
            //出荷予定日が連結していなかったら行作成
            if($goods_count==1){
                $row[$i][$j] = htmlspecialchars($ord_array);
            }
        }
        //配列の最後にCSS挿入
        $row[$i][15] = $html_row;
    }
    //NULLの行は配列に入れない
    for($i = 0; $i < count($row); $i++){
        if($row[$i]!=null){
            $row_data[] = $row[$i];
        }
    }

    return $row_data;
}


/****************************/
//ルール作成(QuickForm)
/****************************/
//■対象在庫
//必須チェック
//半角チェック
$form->addGroupRule('form_stock_date', array(
        'y' => array(
                array('対象在庫の日付は妥当ではありません。','required'),
                array('対象在庫の日付は妥当ではありません。','numeric')
        ),
        'm' => array(
                array('対象在庫の日付は妥当ではありません。','required'),
                array('対象在庫の日付は妥当ではありません。','numeric')
        ),
        'd' => array(
                array('対象在庫の日付は妥当ではありません。','required'),
                array('対象在庫の日付は妥当ではありません。','numeric')
        )
));


//■対象期間
//必須チェック
$form->addGroupRule("form_over_day","対象在庫は必須入力です。","required"); 
$form->addGroupRule("form_over_day","対象在庫は半角数字のみです。","numeric"); 

/****************************/
//表示ボタン押下処理
/****************************/
if($_POST["form_show_button"] == "表　示"){
    /*****************************/
    //POST情報取得
    /*****************************/
//    $output_type           = $_POST["form_output_type"];               //表示形式
    $stock_date_y          = $_POST["form_stock_date"]["y"];           //$stock_date(年)
    $stock_date_m          = $_POST["form_stock_date"]["m"];           //$stock_date(月)
    $stock_date_d          = $_POST["form_stock_date"]["d"];           //$stock_date(日)
	$stock_date_y = str_pad($stock_date_y,4, 0, STR_PAD_LEFT);  
	$stock_date_m = str_pad($stock_date_m,2, 0, STR_PAD_LEFT); 
	$stock_date_d = str_pad($stock_date_d,2, 0, STR_PAD_LEFT); 

    $over_day              = $_POST["form_over_day"];                  //$over_day
    $sale_buy              = $_POST["form_sale_buy"];                  //入出庫区分
    $g_goods               = $_POST["form_g_goods"];                   //Ｍ区分
    $product               = $_POST["form_product"];                   //製品区分
    $goods_cd              = $_POST["form_goods_cd"];                  //Ｍ区分
    $goods_cname            = $_POST["form_goods_cname"];                //商品名
    $ware                  = $_POST["form_ware"];                      //倉庫
    $cshop                 = $_POST["form_cshop"];                     //事業所

    $stock_date = $stock_date_y."-".$stock_date_m."-".$stock_date_d;   //$stock_date
    $stock_before = date("Y-m-d", mktime(0,0,0,$stock_date_m,$stock_date_d - $over_day,$stock_date_y));   //$stock_date
/*
    if(!checkdate((int)$stock_date_m, (int)$stock_date_d, (int)$stock_date_y)){
            $form->setElementError("form_stock_date","$stock_dateの日付は妥当ではありません。");
    }
*/
	//◇対象在庫
	//数値判定
	if(!ereg("^[0-9]{4}$",$stock_date_y) || !ereg("^[0-9]{2}$",$stock_date_m) || !ereg("^[0-9]{2}$",$stock_date_d)){
		$form->setElementError("form_stock_date","対象在庫の日付は妥当ではありません。");
	}

	//・文字種チェック
    $stock_date_y = (int)$stock_date_y;
    $stock_date_m = (int)$stock_date_m;
    $stock_date_d = (int)$stock_date_d;
    if(!checkdate($stock_date_m,$stock_date_d,$stock_date_y)){
		$form->setElementError("form_stock_date","対象在庫の日付は妥当ではありません。");
    }
}

/****************************/
//事業所名取得
/****************************/
if($cshop == NULL){
    //セッションのショップID
    $sql  = "SELECT ";
    $sql .= "    client_cname ";
    $sql .= "FROM ";
    $sql .= "    t_client ";
    $sql .= "WHERE ";
    $sql .= "    client_id = $shop_id;";
}else{
    //プルダウンのショップID
    $sql  = "SELECT ";
    $sql .= "    client_cname ";
    $sql .= "FROM ";
    $sql .= "    t_client ";
    $sql .= "WHERE ";
    $sql .= "    client_id = $cshop;";
}
$result = Db_Query($db_con,$sql);
$data = Get_Data($result);
$cshop_name = $data[0][0];

if($form->validate()){

    /****************************/
    //FROM_SQL作成
    /****************************/
    if($sale_buy == '1'){
	    $select_sql  = " SELECT ";
	    $select_sql .= " 	t_g_goods.g_goods_name , ";
	    $select_sql .= " 	t_product.product_name , ";
	    $select_sql .= " 	t_goods.goods_name , ";
	    $select_sql .= " 	t_ware.ware_name , ";
	    $select_sql .= " 	stock.stock_num , ";
	    $select_sql .= " 	t_price.r_price , ";
	    $select_sql .= " 	(stock.stock_num * t_price.r_price) AS stock_amount , ";
	    $select_sql .= " 	long_stock.inventory_days , ";
	    $select_sql .= " 	long_stock.max_sales_day , ";
	    $select_sql .= " 	long_stock.max_purchase_day ,";
	    $select_sql .= " 	t_goods.goods_cd ";
        $from  = " FROM ";
        $from .= " (";
        $from .= " 	SELECT";
        $from .= " 		inventory_day.goods_id ,";
        $from .= " 		inventory_day.ware_id ,";
        $from .= " 		inventory_day.goods_id || '-' || inventory_day.ware_id AS goods_ware_id,";
        $from .= " 		inventory_day.inventory_days ,";
        $from .= " 		sales_day.max_sales_day ,";
        $from .= " 		purchase_day.max_purchase_day";
        $from .= " 	FROM";
        $from .= " 		(";
        $from .= " SELECT ";
        $from .= " 	t_stock_hand2.goods_id , ";
        $from .= " 	t_stock_hand2.ware_id , ";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id,";
        $from .= " 	date '$stock_date' - max(t_stock_hand2.work_day) AS inventory_days ";
        $from .= " FROM ";
        $from .= " 	(";
        $from .= " SELECT 	";
        $from .= " 	goods_id , ware_id , work_day , work_div , ";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id ";
        $from .= " FROM t_stock_hand ";
        $from .= " WHERE ";
        $from .= " 	(work_div = 2 OR work_div = 4) ";
        if($cshop!=null){
	        $from .= " 	AND shop_id = $cshop ";
        }
        $from .= " 	) AS t_stock_hand2";
        $from .= " WHERE t_stock_hand2.goods_ware_id NOT IN";
        $from .= " 	(";
        $from .= " SELECT";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id ";
        $from .= " FROM t_stock_hand";
        $from .= " WHERE 	";
        $from .= " 	(work_div = 2 OR work_div = 4) AND ";
        if($cshop!=null){
	        $from .= " 	shop_id = $cshop AND ";
		}
        $from .= " 	(";
        $from .= " 	(date '$stock_date' - interval '($over_day -1) day ' ) <= work_day AND ";
        $from .= " 	work_day < date ' ($stock_date + 1) '";
        $from .= " 	) ";
        $from .= " GROUP BY goods_id , ware_id";
        $from .= " 	)";
        $from .= " 	AND t_stock_hand2.work_day < (date '$stock_date' - interval '($over_day - 1) day')";
        $from .= " GROUP BY";
        $from .= " 	t_stock_hand2.goods_id ,";
        $from .= " 	t_stock_hand2.ware_id";
        $from .= " 		) AS inventory_day";
        $from .= " 		LEFT JOIN";
        $from .= " 		(";
        $from .= " SELECT";
        $from .= " 	t_stock_hand2.goods_id ,";
        $from .= " 	t_stock_hand2.ware_id ,";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id,";
        $from .= " 	max(t_stock_hand2.work_day) AS max_sales_day";
        $from .= " FROM";
        $from .= " 	(";
        $from .= " SELECT";
        $from .= " 	goods_id , ware_id , work_day , work_div , ";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id ";
        $from .= " FROM t_stock_hand ";
        $from .= " WHERE ";
        $from .= " 	work_div = 4 ";
        if($cshop!=null){
	        $from .= "  AND shop_id = $cshop ";
        }
        $from .= " 	) AS t_stock_hand2";
        $from .= " WHERE t_stock_hand2.goods_ware_id NOT IN";
        $from .= " 	(";
        $from .= " SELECT";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id ";
        $from .= " FROM t_stock_hand ";
        $from .= " WHERE 	";
        $from .= " 	(work_div = 2 OR work_div = 4) AND ";
        if($cshop!=null){
	        $from .= " 	shop_id = $cshop AND ";
        }
        $from .= " 	(";
        $from .= " 	(date '$stock_date' - interval '($over_day -1) day ' ) <= work_day AND ";
        $from .= " 	work_day < date ' ($stock_date + 1) '";
        $from .= " 	) ";
        $from .= " GROUP BY goods_id , ware_id	";
        $from .= " 	)";
        $from .= " 	AND t_stock_hand2.work_day < (date '$stock_date' - interval '($over_day - 1) day')";
        $from .= " GROUP BY";
        $from .= " 	t_stock_hand2.goods_id ,";
        $from .= " 	t_stock_hand2.ware_id";
        $from .= " 		) AS sales_day";
        $from .= " 		ON";
        $from .= " 		inventory_day.goods_ware_id = sales_day.goods_ware_id";
        $from .= " 		LEFT JOIN";
        $from .= " 		(";
        $from .= " SELECT";
        $from .= " 	t_stock_hand2.goods_id ,";
        $from .= " 	t_stock_hand2.ware_id ,";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id,		";
        $from .= " 	max(t_stock_hand2.work_day) AS max_purchase_day";
        $from .= " FROM";
        $from .= " 	(";
        $from .= " SELECT ";
        $from .= " 	goods_id , ware_id , work_day , work_div , ";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id ";
        $from .= " FROM t_stock_hand 	";
        $from .= " WHERE ";
        $from .= " 	work_div = 2 ";
        if($cshop!=null){
	        $from .= "  AND shop_id = $cshop ";
        }
        $from .= " 	) AS t_stock_hand2";
        $from .= " WHERE t_stock_hand2.goods_ware_id NOT IN";
        $from .= " 	(";
        $from .= " SELECT ";
        $from .= " 	goods_id || '-' || ware_id AS goods_ware_id ";
        $from .= " FROM t_stock_hand ";
        $from .= " WHERE 	";
        $from .= " 	(work_div = 2 OR work_div = 4) AND ";
        if($cshop!=null){
	        $from .= " 	shop_id = $cshop AND ";
        }
        $from .= " 	(";
        $from .= " 	(date '$stock_date' - interval '($over_day -1) day ' ) <= work_day AND ";
        $from .= " 	work_day < date ' ($stock_date + 1) '";
        $from .= " 	)";
        $from .= " GROUP BY goods_id , ware_id";
        $from .= " 	)";
        $from .= " 	AND t_stock_hand2.work_day < (date '$stock_date' - interval '($over_day - 1) day') 	";
        $from .= " GROUP BY";
        $from .= " 	t_stock_hand2.goods_id , ";
        $from .= " 	t_stock_hand2.ware_id ";
        $from .= " 		) AS purchase_day";
        $from .= " 		ON	";
        $from .= " 		inventory_day.goods_ware_id = purchase_day.goods_ware_id";
        $from .= " ) AS long_stock";
        $from .= " INNER JOIN";
        $from .= " (";
        $from .= " 	SELECT";
        $from .= " 		goods_id || '-' || ware_id AS goods_ware_id ,";
        $from .= " 		stock_num ";
        $from .= " 	FROM t_stock";
        if($cshop!=null){
	        $from .= " 	WHERE shop_id = $cshop";
        }
        $from .= " )AS stock";
        $from .= " ON long_stock.goods_ware_id = stock.goods_ware_id";
        $from .= " INNER JOIN t_goods";
        $from .= " ON long_stock.goods_id = t_goods.goods_id";
        $from .= " INNER JOIN t_ware";
        $from .= " ON long_stock.ware_id = t_ware.ware_id";
        $from .= " INNER JOIN t_g_goods";
        $from .= " ON t_goods.g_goods_id = t_g_goods.g_goods_id";
        $from .= " INNER JOIN t_price";
        $from .= " ON t_goods.goods_id = t_price.goods_id";
        $from .= " INNER JOIN t_product";
        $from .= " ON t_goods.product_id = t_product.product_id";
        #2009-10-12 hashimoto-y
        $from .= " INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id \n";

        $from .= " WHERE";
        $from .= " 	t_price.rank_cd = '3' ";
        #2009-10-12 hashimoto-y
        //aoyama-n 2009-07-23
        #$from .= " 	AND t_goods.stock_manage = '1' ";
        $from .= "  AND \n";
        $from .= "  t_goods_info.stock_manage = '1' \n";
        $from .= "  AND \n";
        $from .= "  t_goods_info.shop_id = $shop_id ";

//        $from .= " 	AND t_price.shop_gid = $_SESSION[fc_shop_gid]";
        $from .= " 	AND t_price.shop_id = $shop_id";
		//rev.1.3 在庫数0ははぶく
        $from .= " 	AND stock.stock_num != 0 \n";
    }else{
	    $select_sql  = " SELECT ";
	    $select_sql .= " 	t_g_goods.g_goods_name , ";
	    $select_sql .= " 	t_product.product_name , ";
	    $select_sql .= " 	t_goods.goods_name , ";
	    $select_sql .= " 	t_ware.ware_name , ";
	    $select_sql .= " 	stock.stock_num , ";
	    $select_sql .= " 	t_price.r_price , ";
	    $select_sql .= " 	(stock.stock_num * t_price.r_price) AS stock_amount , ";
	    $select_sql .= " 	long_stock.inventory_days , ";
	    $select_sql .= " 	long_stock.max_workday , ";
	    $select_sql .= " 	t_goods.goods_cd ";
        $from  = " FROM";
        $from .= " (";
        $from .= " 	SELECT ";
        $from .= " 		t_stock_hand2.goods_id ,";
        $from .= " 		t_stock_hand2.ware_id ,";
        $from .= " 		goods_id || '-' || ware_id AS goods_ware_id,		";
        $from .= " 		date '$stock_date' - max(t_stock_hand2.work_day) AS inventory_days ,		";
        $from .= " 		max(t_stock_hand2.work_day) AS max_workday";
        $from .= " 	FROM ";
        $from .= " 		(";
        $from .= " 		SELECT";
        $from .= " goods_id , ware_id , work_day , work_div ,";
        $from .= " goods_id || '-' || ware_id AS goods_ware_id ";
        $from .= " 		FROM t_stock_hand";
        $from .= " 		WHERE";
        $from .= " work_div = $sale_buy ";
        if($cshop!=null){
	        $from .= " AND shop_id = $cshop";
        }
        $from .= " 		) AS t_stock_hand2";
        $from .= " 	WHERE t_stock_hand2.goods_ware_id NOT IN ";
        $from .= " 		(";
        $from .= " 		SELECT";
        $from .= " goods_id || '-' || ware_id AS goods_ware_id";
        $from .= " 		FROM t_stock_hand";
        $from .= " 		WHERE";
        $from .= " work_div = $sale_buy AND";
        if($cshop!=null){
	        $from .= " shop_id = $cshop AND";
        }
        $from .= " (";
        $from .= " (date '$stock_date' - interval '($over_day -1) day ' ) <= work_day AND";
        $from .= " work_day < date ' ($stock_date + 1) '";
        $from .= " )";
        $from .= " 		GROUP BY goods_id , ware_id";
        $from .= " 		)		";
        $from .= " 		AND t_stock_hand2.work_day < (date '$stock_date' - interval '($over_day - 1) day')";
        $from .= " 	GROUP BY ";
        $from .= " 		t_stock_hand2.goods_id ,";
        $from .= " 		t_stock_hand2.ware_id ";
        $from .= " ) AS long_stock ";
        $from .= " INNER JOIN";
        $from .= " (";
        $from .= " 	SELECT ";
        $from .= " 		goods_id || '-' || ware_id AS goods_ware_id , ";
        $from .= " 		stock_num ";
        $from .= " 	FROM t_stock ";
        if($cshop!=null){
	        $from .= " 	WHERE shop_id = $cshop";
	    }
        $from .= " )AS stock ";
        $from .= " ON long_stock.goods_ware_id = stock.goods_ware_id ";
        $from .= " INNER JOIN t_goods ";
        $from .= " ON long_stock.goods_id = t_goods.goods_id ";
        $from .= " INNER JOIN t_ware ";
        $from .= " ON long_stock.ware_id = t_ware.ware_id ";
        $from .= " INNER JOIN t_g_goods ";
        $from .= " ON t_goods.g_goods_id = t_g_goods.g_goods_id ";
        $from .= " INNER JOIN t_price";
        $from .= " ON t_goods.goods_id = t_price.goods_id ";
        $from .= " INNER JOIN t_product";
        $from .= " ON t_goods.product_id = t_product.product_id";
        #2009-10-12 hashimoto-y
        $from .= " INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id \n";

        $from .= " WHERE";
        $from .= " 	t_price.rank_cd = '3' ";
        #2009-10-12 hashimoto-y
        //aoyama-n 2009-07-23
        #$from .= " 	AND t_goods.stock_manage = '1' ";
        $from .= "  AND \n";
        $from .= "  t_goods_info.stock_manage = '1' \n";
        $from .= "  AND \n";
        $from .= "  t_goods_info.shop_id = $shop_id ";

//        $from .= " 	AND t_price.shop_gid = $_SESSION[fc_shop_gid]";
        $from .= " 	AND t_price.shop_id = $shop_id";
		//rev.1.3 在庫数0ははぶく
        $from .= " 	AND stock.stock_num != 0 \n";
    }

    /****************************/
    //WHERE_SQL作成
    /****************************/
    //Ｍ区分が指定された場合
    if($g_goods != null){
        $where .= " AND t_g_goods.g_goods_id = $g_goods";
    }

    //商品名が指定された場合
    if($product != null){
        $where .= " AND t_product.product_id = $product";
    }

    //倉庫が選択された場合
    if($ware != null){
        $where .= " AND t_ware.ware_id = $ware";
    }

    //商品コードが指定された場合
    if($goods_cd != null){
        $where .= " AND t_goods.goods_cd LIKE '$goods_cd%'";
    }

    //商品名が指定された場合
    if($goods_cname != null){
        $where .= " AND t_goods.goods_name LIKE '%$goods_cname%'";
    }

    /****************************/
    //並び替えSQL
    /****************************/
    $order_by = " ORDER BY t_g_goods.g_goods_cd , t_product.product_cd , t_goods.goods_cd , t_ware.ware_cd ";

    //全得意先の件数取得
    $result = @Db_Query($db_con, $select_sql.$from.$where.$order_by.";");
    $row_data = Get_Stock_Data($result);
    $page_data  = Get_Data($result);
    
    $match_count   = pg_num_rows($result);
    //Ｍ区分計を求める
    for($i = 0; $i <= $match_count; $i++){
        if($i != 0 && $page_data[$i][0] != $page_data[$i-1][0]){
            if($page_data[$i-2][0] != $page_data[$i-1][0]){
                $g_goods_total[$i-1] = $page_data[$i-1][6];
            }else{
                $total_amount    = bcadd($total_amount, $page_data[$i-1][6]);
                $g_goods_total[$i-1] = $total_amount;
            }

            $total_amount    = 0;
        }else{
            $total_amount    = bcadd($total_amount, $page_data[$i-1][6],2);
        }
    }

    //総合計を求める
    $total_amount = number_format(@array_sum($g_goods_total),2);

    //ナンバーフォーマット
    for($i = 0; $i < $match_count; $i++){
        if($g_goods_total[$i] != null){
            $g_goods_total[$i] = number_format($g_goods_total[$i],2);
        }
        $page_data[$i][5]  = number_format($page_data[$i][5],2);
        $page_data[$i][6]  = number_format($page_data[$i][6],2);
    }
	if($match_count == false){
		$match_count = '0';
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
//$page_menu = Create_Menu_f('stock','1');

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "　".$form->_elements[$form->_elementIndex["4_101_button"]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex["4_105_button"]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex["4_109_button"]]->toHtml();
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
    'match_count'   => "$match_count",
    'total_amount'   => "$total_amount",
	'cshop_name'    => "$cshop_name",
));

$smarty->assign('row',$row_data);
$smarty->assign("g_goods_total",$g_goods_total);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
