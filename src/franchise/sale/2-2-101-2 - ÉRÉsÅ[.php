<?php

/******************************
 *  変更履歴
 *      ・（2006-××-××）表示ボタン押下時にクエリー実行<suzuki>
 *      ・（2006-10-26）売上率が0%の担当者も表示<suzuki>
 *                      得意先・ショップの取引状態に関係なく表示<suzuki>
 *      ・（2006-10-31）検索時にPOST情報初期化<suzuki>
 *      ・（2006-11-01）委託先検索時に、前月からカレンダーを表示するように修正<suzuki>
 *
******************************/
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/10      02-046      suzuki      担当者・委託先選択時に前月・翌月ボタン押すと、カレンダー表示期間を表示
 *  2006/12/09      03-082      suzuki      FC側で代行伝票を表示する際に、抽出条件変更
 *  2007/02/07      要望14-1    kajioka-h   表示を得意先でまとめて表示（リンク、件数）
 *  2007/02/22      xx-xxx      kajioka-h   帳票出力機能を削除
 *  2007/02/26      B0702-009   kajioka-h   表示ボタン押下時にJSエラーが出ていたのを修正
 *  2007/03/14      xx-xxx      kajioka-h   保留削除された伝票は表示しないように変更
 *  2007/03/27      xx-xxx      watanabe-k  巡回担当者のリストをスタッフマスタをもとに作成するように修正 
 *  2007-04-09                  fukuda      検索条件復元処理追加
 *  2007-04-23      その他166   fukuda      再遷移時、表示月の復元ができていない不具合を修正
 *  2007/05/07      その他149上 kajioka-h   過去1年分見れるように変更
 *                  xx-xxx      kajioka-h   コメントアウトされてた箇所（コースあたり）をざっくり削除
 *  2009/09/05                  watanabe-k	カレンダーＣＳＶ出力機能を追加
 *  2009/09/29                  hashimoto-y	カレンダーＣＳＶ出力条件変更（confirm_flg,ps_stat削除）
 *  2010/05/13      Rev.1.5     hashimoto-y	売上率０％の場合、全額予定額に集計される不具合の修正
 *
 */

$page_title = "巡回予定カレンダー(月)";

//環境設定ファイル
require_once("ENV_local.php");

/*********************
//ローカル関数定義
**********************/
/* 
 * CSV用のデータを抽出する関数
 *
 * @param $start_day	string 	検索開始日		yyyy-mm-dd
 * @param $end_day   	string 	検索終了日 		yyyy-mm-dd
 * @param $part_id	 	integer	検索部署ID		
 * @param $staff_id  	integer	検索担当者ID
 * @param $fc_id	 	integer	検索FCID
 * @param $client_id	integer ショップID		$_SESSION["client_id"]
 * @return array
**/
function get_calendar_csv($start_day, $end_day, $part_id=null, $staff_id=null, $fc_id=null, $client_id=null){
    global $db_con;

	//以下の検索条件を指定された場合はマッチするデータがない。
	if (($part_id || $staff_id) && $fc_id){
		return array(); 
	}

	//委託先を指定していない場合
	//通常伝票
	if(!$fc_id){
		//照会データ取得（契約区分が通常）
		//※ソート順はCSV出力時に削除するので、一番最後に指定すること。
		$sql  = "SELECT  \n"
			  . "    t_aorder_h.ord_time, \n"          		 // 1  受注日
			  . "    t_aorder_h.client_cd1 || '-' || t_aorder_h.client_cd2 AS client_cd, \n"// 2 	得意先コード
			  . "    t_aorder_h.client_cname, \n"            // 3  得意先名
			  . "    t_staff0.charge_cd AS charge0_cd,  \n"  // 4  担当者コード
			  . "    t_staff0.staff_name AS staff0_name, \n" // 5  スタッフ名
			  . "    t_staff1.charge_cd AS charge1_cd,  \n"  // 6  担当者コード
			  . "    t_staff1.staff_name AS staff1_name, \n" // 7  スタッフ名
			  . "    t_staff2.charge_cd AS charge2_cd,  \n"  // 8  担当者コード
			  . "    t_staff2.staff_name AS staff2_name, \n" // 9  スタッフ名
			  . "    t_staff3.charge_cd AS charge3_cd,  \n"  // 10 担当者コード
			  . "    t_staff3.staff_name AS staff3_name, \n" // 11 スタッフ名
			  . "    t_aorder_h.net_amount, \n"        		 // 12 売上金額
			  . "    t_aorder_h.tax_amount,  \n"             // 13 消費税額 

			  . "    CASE WHEN t_aorder_h.contract_id IS NOT NULL THEN NULL \n" // 14 リピート 
			  . "    WHEN t_aorder_h.contract_id IS NULL THEN '不定期' END AS repeat,  \n" 

			  . "    t_staff0.part_cd,  \n"  	             // 14 部署コード(メイン) 
			  . "    t_aorder_h.ord_no,  \n"  	             // 15 受注番号 
			  . "	 1 AS sort_no \n"						 // 16 ソート順
			  . "FROM  \n"
			  . "    t_aorder_h  \n";

		// 巡回担当者分ループ（メイン、サブ１、２、３）
		for($i = 0; $i < 4; $i++){
			$sql .= "	LEFT JOIN  \n"
			      . "(SELECT  \n"
			      . "	t_aorder_staff.aord_id, \n"
			      . "	t_staff.staff_id, \n"
				  . "	t_aorder_staff.staff_name, \n"
				  . "	t_staff.staff_cd1, \n"
				  . "	t_staff.staff_cd2, \n"
			      . "   CAST(t_staff.charge_cd AS TEXT) AS charge_cd, \n"
				  . "	t_aorder_staff.sale_rate, \n"
				  . "	t_part.part_cd, \n"
                  . " 	t_part.part_id \n"
				  . "FROM  \n"
				  . "   t_aorder_staff  \n"
				  . "   	LEFT JOIN \n"
				  . "   t_staff \n"
				  . "	ON t_staff.staff_id = t_aorder_staff.staff_id \n"
			  	  . "    	INNER JOIN \n"
				  . "	t_attach \n"
				  . "	ON t_staff.staff_id = t_attach.staff_id \n"
			  	  . "    	INNER JOIN \n"
				  . "	t_part \n" 
				  . "	ON t_attach.part_id = t_part.part_id  \n"
				  . " WHERE  \n"
				  . " 	t_aorder_staff.staff_div = '".$i."' \n"
				  . " 	AND  \n"
				  . "   t_aorder_staff.sale_rate IS NOT NULL  \n"
			  	  . "   AND  \n"
			  	  . "   t_attach.h_staff_flg = 'false'  \n"
				  . ")AS t_staff$i \n" 
				  . "ON t_staff$i.aord_id = t_aorder_h.aord_id  \n";
		}

		$sql .= "WHERE \n";

		// shop_id
		if($client_id){
			$sql .= "    t_aorder_h.shop_id = $client_id  \n";
		}else{
			$sql .= "    t_aorder_h.shop_id IN (".Rank_Sql().") \n";
		}

		$sql .= "    AND  \n"
              #2009-09-29 hashimoto-y
			  #. "    t_aorder_h.ps_stat != '4'  \n"
			  #. "    AND  \n"
			  #. "    t_aorder_h.confirm_flg = 'f'  \n"
			  #. "    AND  \n"
			  . "    t_aorder_h.contract_div = '1' \n"
			  . "    AND  \n"
			  . "    t_aorder_h.ord_time >= '$start_day'  \n"
			  . "    AND  \n"
			  . "    t_aorder_h.ord_time <= '$end_day'  \n"
			  . "    AND  \n"
		 	  . "    t_aorder_h.del_flg = 'f'  \n"
			  . "	 AND  \n"
			  . "	 t_staff0.staff_id IS NOT NULL \n";
			// 部署指定判定
			if($part_id){
				$sql .= "AND  \n"
					  . "(t_staff0.part_id = $part_id  \n"
					  . " OR "
				      . " t_staff1.part_id = $part_id \n"
					  . " OR "
				      . " t_staff2.part_id = $part_id \n"
					  . " OR "
				      . " t_staff3.part_id = $part_id \n"
                      . ")";
			}

			// 担当者指定判定
			if($staff_id){
				$sql .= "AND  \n"
					  . "(t_staff0.staff_id = $staff_id  \n"
					  . " OR "
					  . " t_staff1.staff_id = $staff_id \n"
					  . " OR "
					  . " t_staff2.staff_id = $staff_id \n"
					  . " OR "
					  . " t_staff3.staff_id = $staff_id \n"
					  . ")";
			}

		//FCの場合は、代行のカレンダーのデータも抽出する。
		if($client_id){
			$sql .= "UNION  \n"
			 	  . "SELECT  \n"
			  	  . "    t_aorder_h.ord_time, \n"          		 // 1  受注日
			  	  . "    t_aorder_h.client_cd1 || '-' || t_aorder_h.client_cd2 AS client_cd, \n"// 2 	得意先コード
			  	  . "    t_aorder_h.client_cname, \n"            // 3  得意先名
			  	  . "    t_staff0.charge_cd AS charge0_cd,  \n"  // 4  担当者コード
			  	  . "    t_staff0.staff_name AS staff0_name, \n" // 5  スタッフ名
			  	  . "    t_staff1.charge_cd AS charge1_cd,  \n"  // 6  担当者コード
			  	  . "    t_staff1.staff_name AS staff1_name, \n" // 7  スタッフ名
			  	  . "    t_staff2.charge_cd AS charge2_cd,  \n"  // 8  担当者コード
			  	  . "    t_staff2.staff_name AS staff2_name, \n" // 9  スタッフ名
			  	  . "    t_staff3.charge_cd AS charge3_cd,  \n"  // 10 担当者コード
			  	  . "    t_staff3.staff_name AS staff3_name, \n" // 11 スタッフ名
			  	  . "    t_aorder_h.net_amount, \n"        		 // 12 売上金額
			  	  . "    t_aorder_h.tax_amount,  \n"             // 13 消費税額 

			  . "    CASE WHEN t_aorder_h.contract_id IS NOT NULL THEN NULL \n" // 14 リピート 
			  . "    WHEN t_aorder_h.contract_id IS NULL THEN '不定期' END AS repeat,  \n" 

			  	  . "    t_staff0.part_cd,  \n"             	 // 14 部署コード 
			  	  . "    t_aorder_h.ord_no,  \n"             	 // 15 受注番号
			  	  . "	 1 AS sort_no \n"						 // 16 ソート順
				  . "FROM  \n"
				  . "    t_aorder_h  \n";
			// 巡回担当者分ループ（メイン、サブ１、２、３）
			for($i = 0; $i < 4; $i++){
				$sql .= "	LEFT JOIN  \n"
					  . "(SELECT  \n"
					  . "   t_aorder_staff.aord_id, \n"
					  . "   t_staff.staff_id, \n"
					  . "   t_aorder_staff.staff_name, \n"
					  . "   t_staff.staff_cd1, \n"
					  . "   t_staff.staff_cd2, \n"
					  . "   CAST(t_staff.charge_cd AS TEXT) AS charge_cd, \n"
					  . "   t_aorder_staff.sale_rate, \n"
					  . "	t_part.part_cd, \n"
                      . "	t_part.part_id \n"
					  . "FROM  \n"
					  . "   t_aorder_staff  \n"
					  . "   	LEFT JOIN \n" 
					  . "	t_staff \n"
					  . "	ON t_staff.staff_id = t_aorder_staff.staff_id  \n"
					  . "		LEFT JOIN \n"
					  . "	t_attach \n"
					  . "	ON t_staff.staff_id = t_attach.staff_id \n"
                      . "	AND \n"
                      . "	t_attach.h_staff_flg = 'false' \n"
					  . "		LEFT JOIN \n"
                      . "	t_part ON t_attach.part_id = t_part.part_id  \n"
					  . "WHERE  \n"
					  . "   t_aorder_staff.staff_div = '".$i."' \n"
					  . "	AND  \n"
					  . "   t_aorder_staff.sale_rate IS NOT NULL  \n"
					  . ")AS t_staff$i ON t_staff$i.aord_id = t_aorder_h.aord_id  \n";
			}
			$sql .= "WHERE  \n"
				  . "    t_aorder_h.act_id = $client_id  \n"
                  #2009-09-29 hashimoto-y
				  #. "    AND  \n"
				  #. "    t_aorder_h.ps_stat != '4'  \n"
				  #. "    AND  \n"
				  #. "    t_aorder_h.confirm_flg = 'f'  \n"
				  . "    AND  \n"
				  . "    t_aorder_h.contract_div = '2' \n"
				  . "    AND  \n"
				  . "    t_aorder_h.ord_time >= '$start_day'  \n"
				  . "    AND  \n"
				  . "    t_aorder_h.ord_time <= '$end_day'  \n"
				  . "    AND  \n"
				  . "    t_aorder_h.del_flg = 'f'  \n"
				  . "	 AND  \n"
				  . "	 t_staff0.staff_id IS NOT NULL \n";

			// 部署指定判定
			if($part_id){
				$sql .= "AND  \n"
					  . "(t_staff0.part_id = $part_id  \n"
					  . " OR "
				      . " t_staff1.part_id = $part_id \n"
					  . " OR "
				      . " t_staff2.part_id = $part_id \n"
					  . " OR "
				      . " t_staff3.part_id = $part_id \n"
                      . ")";
			}

			// 担当者指定判定
			if($staff_id){
				$sql .= "AND  \n"
					  . "(t_staff0.staff_id = $staff_id  \n"
					  . " OR "
					  . " t_staff1.staff_id = $staff_id \n"
					  . " OR "
					  . " t_staff2.staff_id = $staff_id \n"
					  . " OR "
					  . " t_staff3.staff_id = $staff_id \n"
					  . ")";
			}
		}
	}

	//部署・担当者が条件指定されておらず、且つ直営の場合のみ代行伝票を抽出
	if(!$part_id && !$staff_id && !$client_id){

		if($sql){
			$sql .= "UNION \n";
		}

		//照会データ取得（契約区分が代行）
		$sql .= "SELECT  \n"
			  . "    t_aorder_h.ord_time, \n"           	 // 1  受注日
			  . "    t_aorder_h.client_cd1 || '-' || t_aorder_h.client_cd2 AS client_cd, \n"// 2  得意先コード
			  . "    t_aorder_h.client_cname, \n"       	 // 3  得意先名
			  . "    t_aorder_h.act_cd1 || '-' || t_aorder_h.act_cd2 AS charge0_cd, \n"	// 4  受託先コード
			  . "    t_aorder_h.act_name, \n"           	 // 5  受託先名
			  . "    null,  \n"        					 	 // 6  担当者コード 
			  . "    null,  \n"                              // 7  スタッフ名
			  . "    null,  \n"                              // 8  担当者コード
			  . "    null,  \n"                              // 9  スタッフ名
			  . "    null,  \n"                              // 10 担当者コード 
			  . "    null,  \n"                              // 11 スタッフ名
			  . "    t_aorder_h.net_amount, \n"         	 // 12 売上金額
			  . "    t_aorder_h.tax_amount, \n"         	 // 13 消費税額 

			  . "    CASE WHEN t_aorder_h.contract_id IS NOT NULL THEN NULL \n" // 14 リピート 
			  . "    WHEN t_aorder_h.contract_id IS NULL THEN '不定期' END AS repeat,  \n" 

			  . "    null as part_cd, \n"					 // 14 部署コード 
			  . "    null as ord_no, \n"					 // 15 受注番号 
              . "	 2 AS sort_no \n"					     // 16 ソート順
			  . "FROM  \n"
			  . "    t_aorder_h  \n"
			  . "WHERE  \n"
			  . "    t_aorder_h.shop_id IN (".Rank_Sql().") \n";

		//委託先指定判定
		if($fc_id){
			$sql .= "    AND  \n"
				  . "    t_aorder_h.act_id = $fc_id  \n";
		}

		$sql .= "    AND  \n"
              #2009-09-29 hashimoto-y
			  #. "    t_aorder_h.ps_stat != '4'  \n"
			  #. "    AND  \n"
			  #. "    t_aorder_h.confirm_flg = 'f'  \n"
			  #. "    AND  \n"
			  . "    (t_aorder_h.contract_div = '2' OR t_aorder_h.contract_div = '3') \n"
			  . "    AND  \n"
			  . "    t_aorder_h.ord_time >= '$start_day'  \n"
			  . "    AND  \n"
			  . "    t_aorder_h.ord_time <= '$end_day'  \n"
			  . "    AND  \n"
			  . "    t_aorder_h.del_flg = 'f'  \n";
	}

	$sql .= "ORDER BY  \n"
		 . "    ord_time, \n"      		//受注日
         . "    sort_no, \n"			//ソート順	
		 . "	part_cd, \n"  		    //部署コード	巡回担当者（メイン）
		 . "	charge0_cd, \n"			//担当者コード	巡回担当者（メイン）
		 . "	client_cd, \n" 			//得意先コード
         . "	ord_no \n";				//受注番号

	$result = Db_Query($db_con, $sql);
	if($data_list = Get_Data($result)){
		return $data_list;
	}else{
		return array();
	}
}

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);

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

// 再遷移時に復元を除外するフォーム配列
$ary_pass_list = array(
    "form_sale_day" => array(
        "y" => date("Y"),
        "m" => date("m"),
    ),
);

// 検索条件復元
Restore_Filter2($form, "contract", "indicate_button", $ary_form_list, $ary_pass_list);

// モジュール番号取得
$module_no = Get_Mod_No();

// 再遷移時は表示月を復元
if ($_GET["search"] == "1"){
    $_POST["next_button_flg"]   = $_SESSION[$module_no]["all"]["next_button_flg"];
    $_POST["back_button_flg"]   = $_SESSION[$module_no]["all"]["back_button_flg"];
    $_POST["form_sale_day"]["y"]= $_SESSION[$module_no]["all"]["form_sale_day"]["y"];
    $_POST["form_sale_day"]["m"]= $_SESSION[$module_no]["all"]["form_sale_day"]["m"];
}

/****************************/
//契約関数定義
/****************************/
//require_once(INCLUDE_DIR."function_keiyaku.inc");

/****************************/
//外部変数
/****************************/
$client_id = $_SESSION["client_id"];

//巡回基準日
$sql  = "SELECT stand_day FROM t_stand;";
$result = Db_Query($db_con, $sql); 
$stand_day = pg_fetch_result($result,0,0);   
$day_by = substr($stand_day,0,4);
$day_bm = substr($stand_day,5,2);
$day_bd = substr($stand_day,8,2);

//巡回基準日の月の日数取得
$stand_num = date("t",mktime(0, 0, 0,$day_bm,1,$day_by));
$monday_array = NULL;   //日曜日配列
//巡回基準日の月の、日曜日の日付を取得
for($s=1;$s<=$stand_num;$s++){
	$monday = date('w', mktime(0, 0, 0,$day_bm,$s,$day_by));
	//日曜か判定
	if($monday == 0){
		$monday_array[] = date('d', mktime(0, 0, 0,$day_bm,$s,$day_by));
	}
}
//巡回基準日の週を取得
for($s=0;$s<count($monday_array);$s++){
	if($day_bd == $monday_array[$s]){
		$stand_day_week = $s+1;
	}
}


/****************************/
//初期表示
/****************************/
$def_fdata = array(
    "form_sale_day" => array(
        "y" => date("Y"),
        "m" => date("m"),
    ),
    "form_output_type" => '1'
);

$form->setDefaults($def_fdata);


/****************************/
//POST情報取得
/****************************/
if ($_POST != null){
    $post_part_id   = $_POST["form_part_1"];    //部署
    $post_staff_id  = $_POST["form_staff_1"];   //担当者
    $post_fc_id     = $_POST["form_fc"];        //委託先
    $post_y         = $_POST["form_sale_day"]["y"];     //巡回予定日（年）
    $post_m         = $_POST["form_sale_day"]["m"];     //巡回予定日（月）
}

/****************************/
//フォーム定義
/****************************/

//部署
$select_value = NULL;
$select_value = Select_Get($db_con,'part');
$form->addElement('select', 'form_part_1', 'セレクトボックス', $select_value,$g_form_option_select);

//担当者
$select_value = NULL;
$select_value = Select_Get($db_con, "round_staff_m");
$form->addElement('select', 'form_staff_1', 'セレクトボックス', $select_value,$g_form_option_select);

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

//ヘッダのボタン
$form->addElement("button","month_button","　月　",$g_button_color."onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","week_button","　週　","onClick=\"location.href='2-2-102-2.php'\"");

//hidden
$form->addElement("hidden", "back_button_flg");     //前月ボタン押下判定
$form->addElement("hidden", "next_button_flg");     //翌月ボタン押下判定
$form->addElement("hidden", "next_count");          //今月から何ヶ月後
$form->addElement("hidden", "back_count");          //今月から何ヶ月前

//予定巡回日（年月）
$form_sale_day = null;
$form_sale_day[] =& $form->createElement(
                        "text", "y", "", 
                        "size=\"4\" maxLength=\"4\" $g_form_option style=\"$g_form_style\" 
                         onkeyup=\"changeText(this.form,'form_sale_day[y]','form_sale_day[m]',4);\" 
                         onFocus=\"onForm_today2(this,this.form,'form_sale_day[y]','form_sale_day[m]');\""
                    );
$form_sale_day[] =& $form->createElement("static", "", "", "-");
$form_sale_day[] =& $form->createElement(
                        "text", "m", "", 
                        "size=\"1\" maxLength=\"2\" $g_form_option style=\"$g_form_style\" 
                         onFocus=\"onForm_today2(this,this.form,'form_sale_day[y]','form_sale_day[m]');\""
                    );
$form->addGroup($form_sale_day, "form_sale_day", "");

// 出力形式
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "画面", "1");
$obj[]  =&  $form->createElement("radio", null, null, "CSV", "2");
$form->addGroup($obj, "form_output_type", "", " ");


//ここからカレンダー表示処理
if($_POST != null){

    //予定巡回月のエラーチェック
    $form->addGroupRule('form_sale_day', array(
            'y' => array(
                    array("予定巡回月 は必須です。", 'required'),
            ),
            'm' => array(
                    array("予定巡回月 は必須です。", 'required'),
            ),
    ));

    if(!checkdate((int)$post_m, 1, (int)$post_y)){
        $form->setElementError("form_sale_day", "予定巡回月 は妥当ではありません。");
    }

    $form->validate();
    $error_flg = (count($form->_errors) > 0) ? true : false;


    //-- 表示年月を指定 --//
    //表示ボタンが押された場合
    if($_POST["indicate_button"] != null){
        $year   = str_pad($post_y, 4, 0, STR_PAD_LEFT);
        $month  = str_pad($post_m, 2, 0, STR_PAD_LEFT);

    //前月ボタンが押された場合
    }elseif($_POST["back_button_flg"] == 'true'){
        $str    = mktime(0, 0, 0, $post_m - 1, 1, $post_y);
        $year   = date("Y", $str);
        $month  = date("m", $str);

        $con_data["back_button_flg"] = "";

    //翌月ボタンが押された場合
    }elseif($_POST["next_button_flg"] == 'true'){
        $str    = mktime(0, 0, 0, $post_m + 1, 1, $post_y);
        $year   = date("Y", $str);
        $month  = date("m", $str);

        $con_data["next_button_flg"] = "";
    }

    $con_data["form_sale_day"]["y"] = $year;
    $con_data["form_sale_day"]["m"] = $month;

    $form->setConstants($con_data);

    $_SESSION[$module_no]["all"]["form_sale_day"]["y"] = $year;
    $_SESSION[$module_no]["all"]["form_sale_day"]["m"] = $month;


    //----------------------------//
    // フォームデータをセッションに
    //----------------------------//
    // 表示ボタン、日付系ボタン押下かつ予定データ明細の戻るが押下されていないかつエラーの無い時
    if (($_POST["indicate_button"] != null ||
         $_POST["next_button_flg"] != null ||
         $_POST["back_button_flg"] != null )
        && $error_flg != true){

        // POSTデータをSESSIONにセット
        $_SESSION[$module_no]["all"]["next_button_flg"]    = $_POST["next_button_flg"];
        $_SESSION[$module_no]["all"]["back_button_flg"]    = $_POST["back_button_flg"];

    }

    //----------------------------//
    //得意先情報取得
    //----------------------------//
    //カレンダー表示期間取得
    $sql  = "SELECT ";
    $sql .= "    cal_peri ";    //カレンダー表示期間
    $sql .= "FROM ";
    $sql .= "    t_client ";
    $sql .= "WHERE ";
    $sql .= "    client_id = $client_id;";
    $result = Db_Query($db_con, $sql);
    $num = pg_num_rows($result);
    //ボタンの間に表示する為だけのカレンダー表示期間の範囲
    if($num == 1){
	    //今月＋カレンダー表示期間分表示
    	$num_range = pg_fetch_result($result, 0,0);
    }else{
	    //今月＋一ヶ月
    	$num_range = 1;
    }

    //----------------------------//
    //日付データ取得
    //----------------------------//
    //最終期間の月取得
    $str = mktime(0, 0, 0, date("n") - $back_cal_month, 1, date("Y"));
    $b_year  = date("Y",$str);
    $b_month = date("m",$str);

    //カレンダー表示期間の最後の月取得
    $str = mktime(0, 0, 0, date("n")+$num_range,1,date("Y"));
    $c_year  = date("Y",$str);
    $c_month = date("m",$str);

    //カレンダー表示期間
    $cal_range = $b_year."年 ".$b_month."月 〜 ".$c_year."年 ".$c_month."月";

    //指定年月がカレンダー表示期間内か判定
    $cal_range_err = ("$b_year-$b_month-01" <= "$year-$month-01" && "$c_year-$c_month-01" >= "$year-$month-01") ? false : true;

    //前月ボタンdisabled判定
    $b_disabled_flg = ("$b_year-$b_month-01" >= "$year-$month-01" || $cal_range_err == true) ? "disabled" : "";

    //翌月ボタンdisabled判定
    $n_disabled_flg = ("$c_year-$c_month-01" <= "$year-$month-01" || $cal_range_err == true) ? "disabled" : "";

    if($cal_range_err){
        $form->setElementError("form_sale_day", "巡回予定月 はカレンダー表示期間内を指定して下さい。");
    }

    //カレンダーHTML
    $calendar = NULL;
    $cal_data_flg = false;  //全ての担当者の受注データ存在フラグ判定

//エラーじゃない、かつカレンダー表示期間内ならば処理開始
if($error_flg != true && $cal_range_err != true){

    //--------------------------//
    //休日データ取得
    //--------------------------//
    $sql  = "SELECT ";
    $sql .= "    holiday ";     //休日
    $sql .= "FROM ";
    $sql .= "    t_holiday ";
    $sql .= "WHERE ";
    $sql .= "    shop_id = $client_id ";
    $sql .= "    AND ";
    $sql .= "    holiday LIKE '$year-$month-%' ";
    $sql .= ";";
    $result = Db_Query($db_con, $sql);
    $h_data = Get_Data($result);
    for($h=0;$h<count($h_data);$h++){
	    //休日データを連想配列によって定義
    	$holiday[$h_data[$h][0]] = "1";
    }


//カレンダー表示期間分、カレンダー表示HTML作成
	//月の日数取得
	$day = date("t",mktime(0, 0, 0, $month, 1, $year));
	//月の最初の日の曜日
	$first_day = date('w', mktime(0, 0, 0, $month, 1, $year));
	//月の最後の日の曜日
	$last_day = date('w', mktime(0, 0, 0, $month, $day, $year));


	//月の週の数を得る
	$last_week_days = ($day + $first_day) % 7;
	if ($last_week_days == 0){
		$weeks = ($day + $first_day) / 7;
	}else{
		$weeks = ceil(($day + $first_day) / 7);
	}

	//巡回担当者識別配列
	$aorder_staff = array(1=>"0",2=>"1",3=>"2",4=>"3");

	/****************************/
	// CSV表示処理(CSV出力は表示ボタンが押下された場合のみ有効)
	/****************************/
	if ($_POST['indicate_button'] === '表　示' && $_POST['form_output_type'] === '2'){
		$start_day = $year . '-' . $month . '-' . '01';	//当月1日
		$end_day   = $year . '-' . $month . '-' . $day;	//当月末日

		// 直営場合
		if($_SESSION["group_kind"] == '2'){
			$data_list = get_calendar_csv($start_day, $end_day, $post_part_id, $post_staff_id, $post_fc_id);
		// FCの場合
		}else{
			$data_list = get_calendar_csv($start_day, $end_day, $post_part_id, $post_staff_id, $post_fc_id, $client_id);
		}
		#print_array($data_list);

		// 必要ないデータを削除
		foreach($data_list AS $line){
			array_pop($line);	// 部署コード 
			array_pop($line);	// 受注番号 
			array_pop($line);	// ソート順
			$csv_data[] = $line;
		}

		// 項目定義
		$csv_header    = array( "受注日",
								"得意先コード",
								"得意先名",
								"(メイン)担当者コード",
								"(メイン)スタッフ名",
								"(サブ１)担当者コード",
								"(サブ１)スタッフ名",
								"(サブ２)担当者コード",
								"(サブ２)スタッフ名",
								"(サブ３)担当者コード",
								"(サブ３)スタッフ名",
								"税抜金額",
								"消費税額",
								"リピート");

		$csv_file_name = "巡回予定カレンダー".date("Ymd").".csv";
		$csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC"); 
    	$csv_data = Make_Csv($csv_data, $csv_header);
    	Header("Content-disposition: attachment; filename=$csv_file_name");
    	Header("Content-type: application/octet-stream; name=$csv_file_name");
    	print $csv_data;
		exit;
	}


	//委託先が指定していない場合に通常伝票表示
	if($post_fc_id == NULL){
		/****************************/
		//照会データ取得（契約区分が通常）
		/****************************/

		for($i=1;$i<=4;$i++){

			//担当者（メイン）判定
			if($i!=1){
				//メイン以外はUNIONで結合
				$sql .= "UNION  \n";
				$sql .= "SELECT  \n";
			}else{
				//メイン
				$sql  = "SELECT  \n";
			}

			//確定前
			$sql .= "    t_part.part_name, \n";              //部署名0
			$sql .= "    t_staff$i.staff_name, \n";          //スタッフ名1
			$sql .= "    t_aorder_h.net_amount, \n";         //売上金額2
			$sql .= "    t_aorder_h.ord_time, \n";           //受注日3
			$sql .= "    t_aorder_h.route, \n";              //順路4
			$sql .= "    t_aorder_h.client_cname, \n";       //得意先名5
			$sql .= "    t_aorder_h.aord_id, \n";            //受注ID6
			$sql .= "    NULL, \n";                          //手書伝票フラグ7
			$sql .= "    t_aorder_h.del_flg, \n";    //保留伝票削除フラグ8
			$sql .= "    NULL,  \n";                         //更新フラグ9
			$sql .= "    t_staff$i.staff_cd1, \n";           //スタッフコード1 10
			$sql .= "    t_staff$i.staff_cd2,  \n";          //スタッフコード2 11
			$sql .= "    t_staff$i.staff_id,  \n";           //スタッフID12
			$sql .= "    t_aorder_h.reason, \n";             //保留理由13
			$sql .= "    t_aorder_h.confirm_flg, \n";        //確定伝票14
			$sql .= "    t_aorder_h.client_id, \n";          //得意先ID15
			$sql .= "    t_staff$i.charge_cd,  \n";          //担当者コード16
			$sql .= "    t_aorder_h.client_cd1, \n";         //得意先コード1 17
			$sql .= "    t_aorder_h.client_cd2, \n";         //得意先コード2 18
			$sql .= "    t_aorder_h.tax_amount, \n";         //消費税額 19
			$sql .= "    t_staff$i.sale_rate,  \n";          //売上率 20
			$sql .= "    t_part.part_cd, \n";                //部署名CD 21
			$sql .= "    t_staff_count.num,\n";              //伝票人数 22
			$sql .= "    t_aorder_h.act_id \n";              //代行先ID 23

			$sql .= "FROM  \n";
			$sql .= "    t_aorder_h  \n";

			$sql .= "    INNER JOIN ( \n";
			$sql .= "        SELECT \n";
			$sql .= "            aord_id,\n";
			$sql .= "            count(aord_id)AS num \n";
			$sql .= "        FROM \n";
			$sql .= "            t_aorder_staff \n";
			$sql .= "        WHERE ";
			$sql .= "            sale_rate IS NOT NULL \n";
			$sql .= "        GROUP BY \n";
			$sql .= "            aord_id \n";
			$sql .= "    )AS t_staff_count ON t_staff_count.aord_id = t_aorder_h.aord_id  \n";

			$sql .= "    INNER JOIN  \n";
			$sql .= "        (SELECT  \n";
			$sql .= "             t_aorder_staff.aord_id, \n";
			$sql .= "             t_staff.staff_id, \n";
			$sql .= "             t_aorder_staff.staff_name, \n";
			$sql .= "             t_staff.staff_cd1, \n";
			$sql .= "             t_staff.staff_cd2, \n";
			$sql .= "             t_staff.charge_cd, \n";
			$sql .= "             t_aorder_staff.sale_rate \n";
			$sql .= "         FROM  \n";
			$sql .= "             t_aorder_staff  \n";
			$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id  \n";
			$sql .= "         WHERE  \n";
			$sql .= "             t_aorder_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　01-005　　　　suzuki-t　　売上率が0%の担当者を表示
 *

			$sql .= "         AND  \n";
			$sql .= "             t_aorder_staff.sale_rate != '0'  \n";
*/
			$sql .= "         AND  \n";
			$sql .= "             t_aorder_staff.sale_rate IS NOT NULL  \n";
			$sql .= "        )AS t_staff$i ON t_staff$i.aord_id = t_aorder_h.aord_id  \n";

			$sql .= "    INNER JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id  \n";

			$sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id  \n";

			$sql .= "WHERE  \n";

			if($_SESSION["group_kind"] == '2'){
			    $sql .= "    t_aorder_h.shop_id IN (".Rank_Sql().") \n";
			}else{
			    $sql .= "    t_aorder_h.shop_id = $client_id  \n";
			}
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　01-006　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *
			$sql .= "    AND  \n";
			$sql .= "    t_client.state = '1'  \n";
*/
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.ps_stat != '4'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.confirm_flg = 'f'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.contract_div = '1' \n";
			$sql .= "    AND  \n";
			$sql .= "    t_attach.h_staff_flg = 'false'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.ord_time >= '$year-$month-01'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.ord_time <= '$year-$month-$day'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.del_flg = 'f'  \n";

			//部署指定判定
			if($post_part_id != NULL){
				$sql .= "    AND  \n";
				$sql .= "    t_part.part_id = $post_part_id  \n";
			}
			//担当者指定判定
			if($post_staff_id != NULL){
				$sql .= "    AND  \n";
				$sql .= "    t_staff$i.staff_id = $post_staff_id  \n";
			}

			$sql .= "UNION  \n";

			//確定後
			$sql .= "SELECT  \n";
			$sql .= "    t_part.part_name, \n";              //部署名0
			$sql .= "    t_staff$i.staff_name, \n";          //スタッフ名1
			$sql .= "    t_sale_h.net_amount, \n";           //売上金額2
			$sql .= "    t_sale_h.sale_day, \n";             //売上日3
			$sql .= "    t_aorder_h.route, \n";              //順路4
			$sql .= "    t_sale_h.client_cname, \n";         //得意先名5
			$sql .= "    t_sale_h.sale_id, \n";              //売上ID6
			$sql .= "    NULL, \n";                          //手書伝票フラグ7
			$sql .= "    t_aorder_h.del_flg, \n";    //保留伝票削除フラグ8
			$sql .= "    NULL, \n";                          //更新フラグ9
			$sql .= "    t_staff$i.staff_cd1, \n";           //スタッフコード1 10
			$sql .= "    t_staff$i.staff_cd2, \n";           //スタッフコード2 11
			$sql .= "    t_staff$i.staff_id,  \n";           //スタッフID12
			$sql .= "    t_aorder_h.reason,  \n";            //保留理由13
			$sql .= "    t_aorder_h.confirm_flg, \n";        //確定伝票14
			$sql .= "    t_sale_h.client_id,  \n";           //得意先ID15
			$sql .= "    t_staff$i.charge_cd,  \n";          //担当者コード16
			$sql .= "    t_sale_h.client_cd1, \n";           //得意先コード1 17
			$sql .= "    t_sale_h.client_cd2, \n";           //得意先コード2 18
			$sql .= "    t_sale_h.tax_amount, \n";           //消費税額 19
			$sql .= "    t_staff$i.sale_rate, \n";           //売上率 20
			$sql .= "    t_part.part_cd, \n";                //部署名CD 21
			$sql .= "    t_staff_count.num, \n";             //伝票人数 22
			$sql .= "    t_aorder_h.act_id \n";              //代行先ID 23

			$sql .= "FROM  \n";
			$sql .= "    t_sale_h  \n";
			$sql .= "    INNER JOIN ( \n";
			$sql .= "        SELECT \n";
			$sql .= "            sale_id,\n";
			$sql .= "            count(sale_id)AS num \n";
			$sql .= "        FROM \n";
			$sql .= "            t_sale_staff \n";
			$sql .= "        WHERE ";
			$sql .= "            sale_rate IS NOT NULL \n";
			$sql .= "        GROUP BY \n";
			$sql .= "            sale_id \n";
			$sql .= "    )AS t_staff_count ON t_staff_count.sale_id = t_sale_h.sale_id  \n";

			$sql .= "    INNER JOIN  \n";
			$sql .= "        (SELECT  \n";
			$sql .= "             t_sale_staff.sale_id, \n";
			$sql .= "             t_staff.staff_id, \n";
			$sql .= "             t_sale_staff.staff_name, \n";
			$sql .= "             t_staff.staff_cd1, \n";
			$sql .= "             t_staff.staff_cd2, \n";
			$sql .= "             t_staff.charge_cd, \n";
			$sql .= "             t_sale_staff.sale_rate \n";
			$sql .= "         FROM  \n";
			$sql .= "             t_sale_staff  \n";
			$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_sale_staff.staff_id  \n";
			$sql .= "         WHERE  \n";
			$sql .= "             t_sale_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　01-005　　　　suzuki-t　　売上率が0%の担当者を表示
 *
			$sql .= "         AND  \n";
			$sql .= "             t_sale_staff.sale_rate != '0'  \n";
*/
			$sql .= "         AND  \n";
			$sql .= "             t_sale_staff.sale_rate IS NOT NULL  \n";
			$sql .= "        )AS t_staff$i ON t_staff$i.sale_id = t_sale_h.sale_id  \n";

			$sql .= "    INNER JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id  \n";

			$sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id  \n";

			$sql .= "    INNER JOIN  t_aorder_h ON t_aorder_h.aord_id = t_sale_h.aord_id  \n";

			$sql .= "WHERE  \n";
			if($_SESSION["group_kind"] == '2'){
			    $sql .= "    t_sale_h.shop_id IN (".Rank_Sql().") \n";
			}else{
			    $sql .= "    t_sale_h.shop_id = $client_id  \n";
			}
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　01-006　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *
			$sql .= "    AND  \n";
			$sql .= "    t_client.state = '1'  \n";
*/
			$sql .= "    AND  \n";
			$sql .= "    t_attach.h_staff_flg = 'false'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_sale_h.act_request_flg = 'f'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.confirm_flg = 't'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.contract_div = '1' \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.ord_time >= '$year-$month-01'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.ord_time <= '$year-$month-$day'  \n";
			$sql .= "    AND  \n";
			$sql .= "    t_aorder_h.del_flg = 'f'  \n";

			//部署指定判定
			if($post_part_id != NULL){
				$sql .= "    AND  \n";
				$sql .= "    t_part.part_id = $post_part_id  \n";
			}
			//担当者指定判定
			if($post_staff_id != NULL){
				$sql .= "    AND  \n";
				$sql .= "    t_staff$i.staff_id = $post_staff_id  \n";
			}

			//FCの場合は、代行のカレンダーは結合して表示
			if($_SESSION["group_kind"] == '3'){
				$sql .= "UNION  \n";

				//確定前
				$sql .= "SELECT  \n";
				$sql .= "    t_part.part_name, \n";              //部署名0
				$sql .= "    t_staff$i.staff_name, \n";          //スタッフ名1
				$sql .= "    t_aorder_h.net_amount, \n";         //売上金額2
				$sql .= "    t_aorder_h.ord_time, \n";           //受注日3
				$sql .= "    t_aorder_h.route, \n";              //順路4
				$sql .= "    t_aorder_h.client_cname, \n";       //得意先名5
				$sql .= "    t_aorder_h.aord_id, \n";            //受注ID6
				$sql .= "    NULL, \n";                          //手書伝票フラグ7
				$sql .= "    t_aorder_h.del_flg, \n";    //保留伝票削除フラグ8
				$sql .= "    NULL,  \n";                         //更新フラグ9
				$sql .= "    t_staff$i.staff_cd1, \n";           //スタッフコード1 10
				$sql .= "    t_staff$i.staff_cd2,  \n";          //スタッフコード2 11
				$sql .= "    t_staff$i.staff_id,  \n";           //スタッフID12
				$sql .= "    t_aorder_h.reason, \n";             //保留理由13
				$sql .= "    t_aorder_h.confirm_flg, \n";        //確定伝票14
				$sql .= "    t_aorder_h.client_id, \n";          //得意先ID15
				$sql .= "    t_staff$i.charge_cd,  \n";          //担当者コード16
				$sql .= "    t_aorder_h.client_cd1, \n";         //得意先コード1 17
				$sql .= "    t_aorder_h.client_cd2, \n";         //得意先コード2 18
				$sql .= "    t_aorder_h.tax_amount, \n";         //消費税額 19
				$sql .= "    t_staff$i.sale_rate,  \n";          //売上率 20
				$sql .= "    t_part.part_cd, \n";                //部署名CD 21
				$sql .= "    NULL,\n";                           //伝票人数 22
				$sql .= "    t_aorder_h.act_id \n";              //代行先ID 23

				$sql .= "FROM  \n";
				$sql .= "    t_aorder_h  \n";
				$sql .= "    INNER JOIN  \n";
				$sql .= "        (SELECT  \n";
				$sql .= "             t_aorder_staff.aord_id, \n";
				$sql .= "             t_staff.staff_id, \n";
				$sql .= "             t_aorder_staff.staff_name, \n";
				$sql .= "             t_staff.staff_cd1, \n";
				$sql .= "             t_staff.staff_cd2, \n";
				$sql .= "             t_staff.charge_cd, \n";
				$sql .= "             t_aorder_staff.sale_rate \n";
				$sql .= "         FROM  \n";
				$sql .= "             t_aorder_staff  \n";
				$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id  \n";
				$sql .= "         WHERE  \n";
				$sql .= "             t_aorder_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　01-005　　　　suzuki-t　　売上率が0%の担当者を表示
 *
				$sql .= "         AND  \n";
				$sql .= "             t_aorder_staff.sale_rate != '0'  \n";
*/
				$sql .= "         AND  \n";
				$sql .= "             t_aorder_staff.sale_rate IS NOT NULL  \n";
				$sql .= "        )AS t_staff$i ON t_staff$i.aord_id = t_aorder_h.aord_id  \n";

				$sql .= "    LEFT JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id AND t_attach.h_staff_flg = 'false' \n";

				$sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id  \n";

				$sql .= "WHERE  \n";

				$sql .= "    t_aorder_h.act_id = $client_id  \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-009　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *				
				$sql .= "    AND  \n";
				$sql .= "    t_client.state = '1'  \n";
*/
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.ps_stat != '4'  \n";
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.confirm_flg = 'f'  \n";
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.contract_div = '2' \n";
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.ord_time >= '$year-$month-01'  \n";
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.ord_time <= '$year-$month-$day'  \n";
    			$sql .= "    AND  \n";
	    		$sql .= "    t_aorder_h.del_flg = 'f'  \n";

				//部署指定判定
				if($post_part_id != NULL){
					$sql .= "    AND  \n";
					$sql .= "    t_part.part_id = $post_part_id  \n";
				}
				//担当者指定判定
				if($post_staff_id != NULL){
					$sql .= "    AND  \n";
					$sql .= "    t_staff$i.staff_id = $post_staff_id  \n";
				}

				$sql .= "UNION  \n";

				//確定後
				$sql .= "SELECT  \n";
				$sql .= "    t_part.part_name, \n";              //部署名0
				$sql .= "    t_staff$i.staff_name, \n";          //スタッフ名1
				$sql .= "    t_sale_h.net_amount, \n";           //売上金額2
				$sql .= "    t_sale_h.sale_day, \n";             //売上日3
				$sql .= "    t_aorder_h.route, \n";              //順路4
				$sql .= "    t_sale_h.client_cname, \n";         //得意先名5
				$sql .= "    t_sale_h.sale_id, \n";              //売上ID6
				$sql .= "    NULL, \n";                          //手書伝票フラグ7
				$sql .= "    t_aorder_h.del_flg, \n";    //保留伝票削除フラグ8
				$sql .= "    NULL, \n";                          //更新フラグ9
				$sql .= "    t_staff$i.staff_cd1, \n";           //スタッフコード1 10
				$sql .= "    t_staff$i.staff_cd2, \n";           //スタッフコード2 11
				$sql .= "    t_staff$i.staff_id,  \n";           //スタッフID12
				$sql .= "    t_aorder_h.reason,  \n";            //保留理由13
				$sql .= "    t_aorder_h.confirm_flg, \n";        //確定伝票14
				$sql .= "    t_sale_h.client_id,  \n";           //得意先ID15
				$sql .= "    t_staff$i.charge_cd,  \n";          //担当者コード16
				$sql .= "    t_sale_h.client_cd1, \n";           //得意先コード1 17
				$sql .= "    t_sale_h.client_cd2, \n";           //得意先コード2 18
				$sql .= "    t_sale_h.tax_amount, \n";           //消費税額 19
				$sql .= "    t_staff$i.sale_rate, \n";           //売上率 20
				$sql .= "    t_part.part_cd, \n";                //部署名CD 21
				$sql .= "    NULL,\n";                           //伝票人数 22
				$sql .= "    t_aorder_h.act_id \n";              //代行先ID 23
		  
				$sql .= "FROM  \n";
				$sql .= "    t_sale_h  \n";
				$sql .= "    INNER JOIN  \n";
				$sql .= "        (SELECT  \n";
				$sql .= "             t_sale_staff.sale_id, \n";
				$sql .= "             t_staff.staff_id, \n";
				$sql .= "             t_sale_staff.staff_name, \n";
				$sql .= "             t_staff.staff_cd1, \n";
				$sql .= "             t_staff.staff_cd2, \n";
				$sql .= "             t_staff.charge_cd, \n";
				$sql .= "             t_sale_staff.sale_rate \n";
				$sql .= "         FROM  \n";
				$sql .= "             t_sale_staff  \n";
				$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_sale_staff.staff_id  \n";
				$sql .= "         WHERE  \n";
				$sql .= "             t_sale_staff.staff_div = '".$aorder_staff[$i]."' \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　01-005　　　　suzuki-t　　売上率が0%の担当者を表示
 *
				$sql .= "         AND  \n";
				$sql .= "             t_sale_staff.sale_rate != '0'  \n";
*/
				$sql .= "         AND  \n";
				$sql .= "             t_sale_staff.sale_rate IS NOT NULL  \n";
				$sql .= "        )AS t_staff$i ON t_staff$i.sale_id = t_sale_h.sale_id  \n";

				$sql .= "    LEFT JOIN t_attach ON t_staff$i.staff_id = t_attach.staff_id AND t_attach.h_staff_flg = 'false' \n";

				$sql .= "    LEFT JOIN t_part ON t_attach.part_id = t_part.part_id  \n";

				$sql .= "    INNER JOIN  t_aorder_h ON t_aorder_h.aord_id = t_sale_h.aord_id  \n";

				$sql .= "WHERE  \n";
		
			    $sql .= "    t_aorder_h.act_id = $client_id  \n";
/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-009　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *
				$sql .= "    AND  \n";
				$sql .= "    t_client.state = '1'  \n";
*/
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.contract_div = '2' \n";
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.confirm_flg = 't'  \n";
				$sql .= "    AND  \n";
				$sql .= "    t_sale_h.act_request_flg = 'f'  \n";
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.ord_time >= '$year-$month-01'  \n";
				$sql .= "    AND  \n";
				$sql .= "    t_aorder_h.ord_time <= '$year-$month-$day'  \n";
    			$sql .= "    AND  \n";
	    		$sql .= "    t_aorder_h.del_flg = 'f'  \n";

				//部署指定判定
				if($post_part_id != NULL){
					$sql .= "    AND  \n";
					$sql .= "    t_part.part_id = $post_part_id  \n";
				}
				//担当者指定判定
				if($post_staff_id != NULL){
					$sql .= "    AND  \n";
					$sql .= "    t_staff$i.staff_id = $post_staff_id  \n";
				}
			}
		}
		$sql .= "ORDER BY  \n";
		$sql .= "    22, \n";    //部署CD
		$sql .= "    17, \n";    //担当者CD
		$sql .= "    4, \n";     //受注日or売上日
		$sql .= "    18, \n";    //得意先CD1
		$sql .= "    19, \n";    //得意先CD2
		$sql .= "    7; \n";     //受注ID

		$result = Db_Query($db_con, $sql);

		//委託先の検索時には通常伝票非表示
		if($post_fc_id == NULL){
			$data_list = Get_Data($result);
		}

		/****************************/
		//巡回担当者HTML作成
		/****************************/
		$n_data_list = NULL;
		$staff_data_flg = false;  //受注データ存在フラグ判定

		for($x=0;$x<count($data_list);$x++){
			$ymd = $data_list[$x][3];          //巡回日
			$staff_id = $data_list[$x][12];    //スタッフID

			//連想配列に登録する。
			$n_data_list[$staff_id][$ymd][] = $data_list[$x];
		
			$data_list2[$staff_id][0][0] = $data_list[$x][0];    //部署名
			$data_list2[$staff_id][0][1] = $data_list[$x][1];    //スタッフ名

			//予定金額 (売上金額+消費税額)×売上率
			$money1 = $data_list[$x][2] + $data_list[$x][19];
			$money2 = $data_list[$x][20] / 100;

            #2010-05-13 hashimoto-y
            #echo "money1:" .$money1 ."<br>";
            #echo "money2:" .$money2 ."<br>";
            #echo "売上率:" .$data_list[$x][20] ."<br>";
            #echo "巡回担当者：" .$staff_id ."<br>";
            #echo "売上率:" .$data_list[$x][20] ."<br>";

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

			$data_list2[$staff_id][0][3] = bcadd($total1,$data_list2[$staff_id][0][3]);

			//受注データが存在した
			$staff_data_flg = true;
			//一件以上受注データが存在した
			$cal_data_flg = true;
		}

		//受注データが存在した場合にその担当者データをカレンダー配列作成
		if($staff_data_flg == true){
			/****************************/
			//カレンダーテーブル処理
			/****************************/

			//数値形式変更
			while($money_num = each($data_list2)){
				$money = $money_num[0];
				//予定金額切捨て
				$data_list2[$money][0][3] = floor($data_list2[$money][0][3]);
				$data_list2[$money][0][3] = number_format($data_list2[$money][0][3]);
			}
			//指標をリセットする
			reset($data_list2);

			//ABCD週の表示配列
			$abcd[1] = "A";
			$abcd[2] = "B";
			$abcd[3] = "C";
			$abcd[4] = "D";

			//ABCD配列の添字
			//基準日の月か判定
			if("$day_by-$day_bm-$day_bd 00:00" > "$year-$month-01 00:00"){
				//基準日から始まる為A週にする
				$base_date[0] = 1;
			}else{
				//月の最初の日が何週か取得処理
				$base_date = Basic_date($day_by,$day_bm,$day_bd,$year,$month,1);
			}
			$row = $base_date[0];

			$day_num = 0;   //カレンダーに表示させる日
			$day_num2 = 0;  //データを表示させる為に使用する日

			//担当者ごとに巡回データ作成
			while($staff_num = each($n_data_list)){
				//担当者の添字取得
				$staff = $staff_num[0];

				//前月ボタン
				$form->addElement("button","back_button[$staff]",
                    "<<　前月","onClick=\"javascript:Button_Submit('back_button_flg','".$_SERVER["PHP_SELF"]."#$staff','true')\" 
                     $b_disabled_flg"
                );

				//翌月ボタン
				$form->addElement("button","next_button[$staff]","翌月　>>",
                    "onClick=\"javascript:Button_Submit('next_button_flg','".$_SERVER["PHP_SELF"]."#$staff','true')\" 
                     $n_disabled_flg"
                );

				//月のカレンダー作成
				for($c=1;$c<=$weeks;$c++){
					//既にカレンダー配列が作成されているか判定
					if($c==1 && $calendar[$staff] != NULL){
						//既にあった場合は配列を上書きする
						$calendar[$staff] = "<tr class=\"cal_flame\">";
					}else{
						//二行目からは、配列にただ結合する
						$calendar[$staff] .= "<tr class=\"cal_flame\">";
					}

					$calendar[$staff] .= "	    <td align=\"center\" valign=\"center\" bgcolor=\"#e5e5e5\" rowspan=\"2\">";
					//巡回基準日の月の場合は、基準日の週からABCD週を表示。基準日の月以外は、通常通り表示
					if($stand_day_week <= $c || $stand_day < "$year-$month-01"){
						$calendar[$staff] .= $abcd[$row];
					}
					$calendar[$staff] .= "      </td>";
					$j=0;
					//セルの上作成処理
					while($j<7){
						//休日判定を行なうか判定
						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							//月がまだ始まっていない為、休日判定を行わない
							$cal_flg = true;
						}else{
							//月が開始された為、休日判定を行なう
							$cal_flg = false;

							//休日判定に使用するセルの日付取得
							$cal_num = $day_num + 1;
							$cal_today = str_pad($cal_num, 2, 0, STR_POS_LEFT);
							$cal_date = "$year-$month-$cal_today"; 
						}

						//曜日判定
						if($j == 6 && ($holiday[$cal_date] != 1)){
							//土曜かつ休日ではない
							$calendar[$staff] .= "	    <td class=\"cal\" width=\"135px\" align=\"center\" valign=\"top\" bgcolor=\"#99FFFF\">";
						}else if($j == 0 || ($holiday[$cal_date] == 1 && $cal_flg != true)){
							//日曜or休日
							$calendar[$staff] .= "	    <td class=\"cal\" width=\"135px\" align=\"center\" valign=\"top\" bgcolor=\"#FFDDE7\">";
						}else{
							//月〜金
							$calendar[$staff] .= "<td class=\"cal\" width=\"135px\" align=\"center\" valign=\"top\">";
						}

						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							$calendar[$staff] .= "</td>";
						}else{
							$day_num++;
							//データが存在する場合は、日にちをリンクにする
							if($n_data_list[$staff][$cal_date][0][6] != NULL){
								//データが存在する
								
								$aord_id_array = NULL;     //受注ID配列
								//該当日の受注ID全てを予定明細に渡す
								for($p=0;$p<count($n_data_list[$staff][$cal_date]);$p++){
									$aord_id_array[$p] = $n_data_list[$staff][$cal_date][$p][6];
								}

								//シリアライズ化
								$array_id = serialize($aord_id_array);
								$array_id = urlencode($array_id);

                                //明細画面でコース名表示しないためstaff_idをなくす
								$calendar[$staff] .= "<a href=\"2-2-106.php?aord_id_array=".$array_id."&back_display=cal_month\" style=\"color: #555555;\">$day_num</a></td>";
							}else{
								//データが存在しない
								$calendar[$staff] .= "$day_num</td>";
							}
						}
						$j++;
					}
					$calendar[$staff] .= "</tr>";

					//カレンダーに各担当者のデータを表示
					$calendar[$staff] .= "<tr>";
					$j=0;
					//セルの下作成処理
					while($j<7){

						//休日判定を行なうか判定
						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							//月がまだ始まっていない為、休日判定を行わない
							$cal_flg2 = true;
						}else{
							//月が開始された為、休日判定を行なう
							$cal_flg2 = false;

							//休日判定に使用するセルの日付取得
							$cal_num = $day_num2 + 1;
							$cal_today = str_pad($cal_num, 2, 0, STR_POS_LEFT);
							$cal_date = "$year-$month-$cal_today"; 
						}

						//曜日判定
						if($j == 6 && ($holiday[$cal_date] != 1)){
							//土曜かつ休日ではない
							$calendar[$staff] .= "	    <td class=\"cal2\" width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#99FFFF\" height=\"33\" >";
						}else if($j == 0 || ($holiday[$cal_date] == 1 && $cal_flg2 != true)){
							//日曜or休日
							$calendar[$staff] .= "	    <td class=\"cal2\" width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#FFDDE7\" height=\"33\" >";
						}else{
							//月〜金
							$calendar[$staff] .= "	    <td class=\"cal2\" width=\"135px\" align=\"left\" valign=\"top\" height=\"33\">";
						}
							
						//月の最初の日が何曜日から始まるか判定
						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							//始まる前の曜日
							$calendar[$staff] .= "</td>";
						}else{
							//始まる日より後の曜日
				
							$day_num2++;
							$today = str_pad($day_num2, 2, 0, STR_POS_LEFT);
							$date = "$year-$month-$today";  //各セルに表示する日付
							
							//データ表示判定
								//順路・得意先表示

								//該当日に巡回データが複数存在した場合、その件数分表示
                                $n_data_list_count = count($n_data_list[$staff][$date]);
                                $calendar_tmp = array();
                                $client_cname_tmp = array();
                                $link_color = null;
								for($p=0;$p<$n_data_list_count;$p++){
									//該当日に巡回データが存在するか判定
									if($n_data_list[$staff][$date][$p][6] != NULL){

                                        //得意先ごとにまとめる
                                        // [得意先ID][] = 受注ID という配列を作る
                                        $client_id_tmp = $n_data_list[$staff][$date][$p][15];   //得意先ID
                                        $calendar_tmp[$client_id_tmp][] = $n_data_list[$staff][$date][$p][6];       //受注IDを詰める配列
                                        $client_cname_tmp[$client_id_tmp] = $n_data_list[$staff][$date][$p][5];     //得意先名(略称)を詰める配列
                                        //リンクの色を詰める配列
                                        $link_color[$client_id_tmp] = L_Link_Color($link_color[$client_id_tmp], $n_data_list[$staff][$date][$p][14], $n_data_list[$staff][$date][$p][23], $n_data_list[$staff][$date][$p][22]);
									}
                                }

                                //得意先ごとにまとめたデータからリンクを生成
                                foreach($calendar_tmp as $client_id_key => $aord_id_array){
                                    //シリアライズ化
                                    $array_id = serialize($aord_id_array);
                                    $array_id = urlencode($array_id);

                                    $calendar[$staff] .= " <a href=\"./2-2-106.php?aord_id_array=".$array_id."&back_display=cal_month\"";
                                    $calendar[$staff] .= " style=\"color: ".$link_color[$client_id_key].";\"";
                                    $calendar[$staff] .= ">";
                                    $calendar[$staff] .= $client_cname_tmp[$client_id_key]."</a><br>";

                                    $data_list2[$staff][0][2]++;     //予定件数
								}
							$calendar[$staff] .= "</td>";
						}
						$j++;
						$course_array = NULL;
					}
					$calendar[$staff] .= "</tr>";
					//巡回基準日の月の場合は、基準日の週からABCD週を表示。基準日の月以外は、通常通り表示
					if($stand_day_week <= $c || $stand_day < "$year-$month-01"){
						$row++;
						//D週になったら、またA週に値をもどす
						if($row == 5){
							$row = 1;
						}
					}
				}
				//ABCD配列の添字
				//基準日の月か判定
				if("$day_by-$day_bm-$day_bd 00:00" > "$year-$month-01 00:00"){
					//基準日から始まる為A週にする
					$base_date[0] = 1;
				}else{
					//月の最初の日が何週か取得処理
					$base_date = Basic_date($day_by,$day_bm,$day_bd,$year,$month,1);
				}
				$row = $base_date[0];

				$day_num = 0;
				$day_num2 = 0;
			}
			//指標をリセットする
			reset($n_data_list);
			//月のカレンダーデータがある
			$cal_msg[$staff] .= NULL;
		}else{
			//データない月は警告表示
			$data_list2[$staff] = "error";
			$calendar[$staff] .= "<br>";
			$cal_msg[$staff]  .= "巡回データがありません。";
		}
	}

	//部署・担当者の検索時かつFCの場合に代行カレンダー非表示
	if($post_part_id == NULL && $post_staff_id == NULL && $_SESSION["group_kind"] != 3){

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
 * 　2006/10/26　01-006　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *
		$sql .= "    AND  \n";
		$sql .= "    t_client.state = '1'  \n";

 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-009　　　　suzuki-t　　代行先の取引状態に関係なく表示
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
		$sql .= "    AND  \n";
		$sql .= "    t_aorder_h.ord_time >= '$year-$month-01'  \n";
		$sql .= "    AND  \n";
		$sql .= "    t_aorder_h.ord_time <= '$year-$month-$day'  \n";
		$sql .= "    AND  \n";
		$sql .= "    t_aorder_h.del_flg = 'f'  \n";

		$sql .= "UNION  \n";

		//確定後
		$sql .= "SELECT  \n";
		$sql .= "    t_sale_h.act_cname, \n";            //受託先名(略称)0
		$sql .= "    t_sale_h.act_id, \n";               //受託先ID1
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
 * 　2006/10/26　01-006　　　　suzuki-t　　得意先の取引状態に関係なく表示
 *
		$sql .= "    AND  \n";
		$sql .= "    t_client.state = '1'  \n";

 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/10/26　02-009　　　　suzuki-t　　代行先の取引状態に関係なく表示
 *
		$sql .= "    AND  \n";
		$sql .= "    t_act.state = '1'  \n";
*/
		$sql .= "    AND  \n";
		$sql .= "    t_aorder_h.confirm_flg = 't'  \n";
		$sql .= "    AND  \n";
		$sql .= "    (t_aorder_h.contract_div = '2' OR t_aorder_h.contract_div = '3') \n";
		$sql .= "    AND  \n";
		$sql .= "    t_aorder_h.ord_time >= '$year-$month-01'  \n";
		$sql .= "    AND  \n";
		$sql .= "    t_aorder_h.ord_time <= '$year-$month-$day'  \n";
		$sql .= "    AND  \n";
		$sql .= "    t_aorder_h.del_flg = 'f'  \n";

		$sql .= "ORDER BY  \n";
		$sql .= "    3, \n";    //受託先コード1
		$sql .= "    4, \n";    //受託先コード2
		$sql .= "    6, \n";    //売上日
		$sql .= "    12, \n";   //得意先コード1
		$sql .= "    13, \n";   //得意先コード2
		$sql .= "    9; \n";    //受注 or 売上ID

		$result = Db_Query($db_con, $sql);
		$act_data_list = Get_Data($result);
//print_array($sql);
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
		
			$act_data_list2[$act_id][0][0] = $act_data_list[$x][0];    //受託先名

			//予定金額 (売上金額+消費税額)
			$total1 = $act_data_list[$x][4] + $act_data_list[$x][13];
			$act_data_list2[$act_id][0][2] = bcadd($total1,$act_data_list2[$act_id][0][2]);

			//受注データが存在した
			$act_data_flg = true;
			//一件以上受注データが存在した
			$cal_data_flg = true;
		}

		//受注データが存在した場合にその受託データをカレンダー配列作成
		if($act_data_flg == true){
			/****************************/
			//カレンダーテーブル処理
			/****************************/

			//数値形式変更
			$money_num = NULL;
			while($money_num = each($act_data_list2)){
				$money = $money_num[0];
				//予定金額切捨て
				$act_data_list2[$money][0][2] = floor($act_data_list2[$money][0][2]);
				$act_data_list2[$money][0][2] = number_format($act_data_list2[$money][0][2]);
			}
			//指標をリセットする
			reset($act_data_list2);

			//ABCD週の表示配列
			$abcd[1] = "A";
			$abcd[2] = "B";
			$abcd[3] = "C";
			$abcd[4] = "D";

			//ABCD配列の添字
			//基準日の月か判定
			$base_date = NULL;
			$row = NULL;
			if("$day_by-$day_bm-$day_bd 00:00" > "$year-$month-01 00:00"){
				//基準日から始まる為A週にする
				$base_date[0] = 1;
			}else{
				//月の最初の日が何週か取得処理
				$base_date = Basic_date($day_by,$day_bm,$day_bd,$year,$month,1);
			}
			$row = $base_date[0];

			$day_num = 0;   //カレンダーに表示させる日
			$day_num2 = 0;  //データを表示させる為に使用する日

			//受託先ごとに巡回データ作成
			$act_num = NULL;
			while($act_num = each($n_data_list)){
				//受託先の添字取得
				$act = $act_num[0];

				//前月ボタン
				$form->addElement("button","back_button[$act]","<<　前月",
                    "onClick=\"javascript:Button_Submit('back_button_flg','".$_SERVER["PHP_SELF"]."#$act','true')\" 
                     $b_disabled_flg"
                );

				//翌月ボタン
				$form->addElement("button","next_button[$act]","翌月　>>",
                    "onClick=\"javascript:Button_Submit('next_button_flg','".$_SERVER["PHP_SELF"]."#$act','true')\" 
                     $n_disabled_flg"
                );

				//月のカレンダー作成
				for($c=1;$c<=$weeks;$c++){
					//既にカレンダー配列が作成されているか判定
					if($c==1 && $act_calendar[$act] != NULL){
						//既にあった場合は配列を上書きする
						$act_calendar[$act] = "<tr class=\"cal_flame\">";
					}else{
						//二行目からは、配列にただ結合する
						$act_calendar[$act] .= "<tr class=\"cal_flame\">";
					}

					$act_calendar[$act] .= "	    <td align=\"center\" valign=\"center\" bgcolor=\"#e5e5e5\" rowspan=\"2\">";
					//巡回基準日の月の場合は、基準日の週からABCD週を表示。基準日の月以外は、通常通り表示
					if($stand_day_week <= $c || $stand_day < "$year-$month-01"){
						$act_calendar[$act] .= $abcd[$row];
					}
					$act_calendar[$act] .= "      </td>";
					$j=0;
					//セルの上作成処理
					while($j<7){
						//休日判定を行なうか判定
						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							//月がまだ始まっていない為、休日判定を行わない
							$cal_flg = true;
						}else{
							//月が開始された為、休日判定を行なう
							$cal_flg = false;

							//休日判定に使用するセルの日付取得
							$cal_num = $day_num + 1;
							$cal_today = str_pad($cal_num, 2, 0, STR_POS_LEFT);
							$cal_date = "$year-$month-$cal_today"; 
						}

						//曜日判定
						if($j == 6 && ($holiday[$cal_date] != 1)){
							//土曜かつ休日ではない
							$act_calendar[$act] .= "	    <td class=\"cal\" width=\"135px\" align=\"center\" valign=\"top\" bgcolor=\"#99FFFF\">";
						}else if($j == 0 || ($holiday[$cal_date] == 1 && $cal_flg != true)){
							//日曜or休日
							$act_calendar[$act] .= "	    <td class=\"cal\" width=\"135px\" align=\"center\" valign=\"top\" bgcolor=\"#FFDDE7\">";
						}else{
							//月〜金
							$act_calendar[$act] .= "<td class=\"cal\" width=\"135px\" align=\"center\" valign=\"top\">";
						}

						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							$act_calendar[$act] .= "</td>";
						}else{
							$day_num++;
							//データが存在する場合は、日にちをリンクにする
							if($n_data_list[$act][$cal_date][0][8] != NULL){
								//データが存在する
								
								$aord_id_array = NULL;     //受注ID配列
								//該当日の受注ID全てを予定明細に渡す
								for($p=0;$p<count($n_data_list[$act][$cal_date]);$p++){
									$aord_id_array[$p] = $n_data_list[$act][$cal_date][$p][8];
								}

								//シリアライズ化
								$array_id = serialize($aord_id_array);
								$array_id = urlencode($array_id);

								$act_calendar[$act] .= "<a href=\"2-2-106.php?aord_id_array=".$array_id."&back_display=cal_month\" style=\"color: #555555;\">$day_num</a></td>";
							}else{
								//データが存在しない
								$act_calendar[$act] .= "$day_num</td>";
							}
						}
						$j++;
					}
					$act_calendar[$act] .= "</tr>";

					//カレンダーに各担当者のデータを表示
					$act_calendar[$act] .= "<tr>";
					$j=0;
					//セルの下作成処理
					while($j<7){

						//休日判定を行なうか判定
						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							//月がまだ始まっていない為、休日判定を行わない
							$cal_flg2 = true;
						}else{
							//月が開始された為、休日判定を行なう
							$cal_flg2 = false;

							//休日判定に使用するセルの日付取得
							$cal_num = $day_num2 + 1;
							$cal_today = str_pad($cal_num, 2, 0, STR_POS_LEFT);
							$cal_date = "$year-$month-$cal_today"; 
						}

						//曜日判定
						if($j == 6 && ($holiday[$cal_date] != 1)){
							//土曜かつ休日ではない
							$act_calendar[$act] .= "	    <td class=\"cal2\" width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#99FFFF\" height=\"33\" >";
						}else if($j == 0 || ($holiday[$cal_date] == 1 && $cal_flg2 != true)){
							//日曜or休日
							$act_calendar[$act] .= "	    <td class=\"cal2\" width=\"135px\" align=\"left\" valign=\"top\" bgcolor=\"#FFDDE7\" height=\"33\" >";
						}else{
							//月〜金
							$act_calendar[$act] .= "	    <td class=\"cal2\" width=\"135px\" align=\"left\" valign=\"top\" height=\"33\">";
						}
							
						//月の最初の日が何曜日から始まるか判定
						if(($c-1==0 && $j<$first_day) || ($c-1==$weeks-1 && $j>$last_day)){
							//始まる前の曜日
							$act_calendar[$act] .= "</td>";
						}else{
							//始まる日より後の曜日
				
							$day_num2++;
							$today = str_pad($day_num2, 2, 0, STR_POS_LEFT);
							$date = "$year-$month-$today";  //各セルに表示する日付

								//得意先表示

                                $calendar_tmp = array();
                                $client_cname_tmp = array();
                                $link_color = null;

								//該当日に巡回データが複数存在した場合、その件数分表示
								for($p=0;$p<count($n_data_list[$act][$date]);$p++){
									//該当日に巡回データが存在するか判定
									if($n_data_list[$act][$date][$p][8] != NULL){
                                        //得意先ごとにまとめる
                                        // [得意先ID][] = 受注ID という配列を作る
                                        $client_id_tmp = $n_data_list[$act][$date][$p][9];      //得意先ID
                                        $calendar_tmp[$client_id_tmp][] = $n_data_list[$act][$date][$p][8];     //受注IDを詰める配列
                                        $client_cname_tmp[$client_id_tmp] = $n_data_list[$act][$date][$p][7];   //得意先名(略称)を詰める配列
                                        //リンクの色を詰める配列
                                        $link_color[$client_id_tmp] = L_Link_Color($link_color[$client_id_tmp], $n_data_list[$act][$date][$p][15], $n_data_list[$act][$date][$p][1], 1);

									}
								}

                                //得意先ごとにまとめたデータからリンクを生成
                                foreach($calendar_tmp as $client_id_key => $aord_id_array){
                                    //シリアライズ化
                                    $array_id = serialize($aord_id_array);
                                    $array_id = urlencode($array_id);

                                    $act_calendar[$act] .= " <a href=\"./2-2-106.php?aord_id_array=".$array_id."&back_display=cal_month\"";
                                    $act_calendar[$act] .= " style=\"color: ".$link_color[$client_id_key].";\"";
                                    $act_calendar[$act] .= ">";
                                    $act_calendar[$act] .= $client_cname_tmp[$client_id_key]."</a><br>";

                                    $act_data_list2[$act][0][1]++;   //予定件数
                                }

							$act_calendar[$act] .= "</td>";
						}
						$j++;
					}
					$act_calendar[$act] .= "</tr>";
					//巡回基準日の月の場合は、基準日の週からABCD週を表示。基準日の月以外は、通常通り表示
					if($stand_day_week <= $c || $stand_day < "$year-$month-01"){
						$row++;
						//D週になったら、またA週に値をもどす
						if($row == 5){
							$row = 1;
						}
					}
				}
				//ABCD配列の添字
				//基準日の月か判定
				if("$day_by-$day_bm-$day_bd 00:00" > "$year-$month-01 00:00"){
					//基準日から始まる為A週にする
					$base_date[0] = 1;
				}else{
					//月の最初の日が何週か取得処理
					$base_date = Basic_date($day_by,$day_bm,$day_bd,$year,$month,1);
				}
				$row = $base_date[0];

				$day_num = 0;
				$day_num2 = 0;
			}
			//指標をリセットする
			reset($n_data_list);
			//月のカレンダーデータがある
			$act_cal_msg[$act] = NULL;
		}else{

			/*
			 * 履歴：
			 * 　日付　　　　B票No.　　　　担当者　　　内容　
			 * 　2006/11/01　02-019　　　　suzuki-t　　委託先検索時に、前月からカレンダーを表示するように修正
			 *
			*/
			//データない月は警告表示
			$act_data_list2[$act] = "error";

			$act_calendar[$act] .= "<br>";
			$act_cal_msg[$act] .= "巡回データがありません。";
		}
	}
}

//データが無い場合は、リンク先指定なしのボタン作成
if($cal_data_flg == false){

	//前月ボタン
	$form->addElement("button","back_button","<<　前月",
        "onClick=\"javascript:Button_Submit('back_button_flg','".$_SERVER["PHP_SELF"]."','true')\" $b_disabled_flg"
    );

	//翌月ボタン
	$form->addElement("button","next_button","翌月　>>",
        "onClick=\"javascript:Button_Submit('next_button_flg','".$_SERVER["PHP_SELF"]."','true')\" $n_disabled_flg"
    );
	
	$data_msg = "巡回データがありません。";
}


}//ここまでカレンダー表示処理


//print date(H：i：s)."<br>";
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
	'cal_data_flg'    => "$cal_data_flg",
	'cal_range'       => "$cal_range",
	'data_msg'        => "$data_msg",
	'cal_range_err'   => $cal_range_err,
));

//表示データ
$smarty->assign("disp_data", $data_list2);
$smarty->assign("calendar", $calendar);
$smarty->assign("act_disp_data", $act_data_list2);
$smarty->assign("act_calendar", $act_calendar);
$smarty->assign("cal_msg", $cal_msg);
$smarty->assign("act_cal_msg", $act_cal_msg);
$smarty->assign("year", $year);
$smarty->assign("month", $month);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER["PHP_SELF"].".tpl"));


//print_array($_POST, '$_POST');
//print_array($_SESSION, '$_SESSION');


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
