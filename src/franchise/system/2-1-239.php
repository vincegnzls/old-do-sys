<?php
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/11      03-027      kajioka-h   全件数はオンライン代行だけ
 *  2009/09/22                  hashimoto-y 値引き商品を赤字表示
 *
 */

$page_title = "代行依頼一覧（受託先用）";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);

/****************************/
//外部変数取得
/****************************/
$get_flg      = $_GET["get_flg"];        //遷移元判定フラグ
$back_display = $_GET["back_display"];   //予定明細の遷移元
$array_id     = $_GET["aord_id_array"];  //該当日の全ての受注ID
$aord_id      = $_GET["aord_id"];        //受注ID
$shop_id      = $_SESSION["client_id"];  //ログインID

//アンシリアライズ化
$array_id = stripslashes($array_id);
$array_id = urldecode($array_id);
$array_id = unserialize($array_id);


//直営判定
$rank_cd  = $_SESSION["rank_cd"];
$sql  = "SELECT group_kind FROM t_rank WHERE rank_cd = '$rank_cd' AND group_kind = '3';";
$result = Db_Query($db_con, $sql);
Get_Id_Check($result);

//不正判定
Get_ID_Check3($aord_id);
Get_ID_Check3($array_id);

//シリアライズ化
$array_id = serialize($array_id);
$array_id = urlencode($array_id);

/****************************/
//フォーム定義
/****************************/
//戻るボタン遷移先判定
switch($get_flg){
	case 'cal':
		//予定明細

		$form->addElement("button","form_back","戻　る","onClick=\"location.href='".FC_DIR."sale/2-2-106.php?aord_id[0]=$aord_id&aord_id_array=$array_id&back_display=$back_display'\"");
		break;
	case 'reason':
		//予定データ訂正
		$form->addElement("button","form_back","戻　る","onClick=\"location.href='".FC_DIR."sale/2-2-107.php?aord_id=$aord_id&back_display=$back_display&aord_id_array=$array_id'\"");
		break;
}

/****************************/
//データ表示部品作成処理
/****************************/
$sql  = "SELECT DISTINCT ";
$sql .= "    t_contract.trust_line, ";              //行No0
$sql .= "    t_contract.act_request_day,";          //依頼日1
$sql .= "    t_contract.route,";                    //順路2
$sql .= "    CASE t_con_info.divide ";              //販売区分3
$sql .= "         WHEN '01' THEN 'リピート'";
$sql .= "         WHEN '02' THEN '商品'";
$sql .= "         WHEN '03' THEN 'レンタル'";
$sql .= "         WHEN '04' THEN 'リース'";
$sql .= "         WHEN '05' THEN '工事'";
$sql .= "         WHEN '06' THEN 'その他'";
$sql .= "    END,";
$sql .= "    CASE t_con_info.serv_pflg ";           //サービス印字4
$sql .= "         WHEN 't' THEN '○'";
$sql .= "         WHEN 'f' THEN '×'";
$sql .= "    END,";
$sql .= "    t_serv.serv_name,";                    //サービス名5

$sql .= "    CASE t_con_info.goods_pflg ";          //アイテム印字6
$sql .= "         WHEN 't' THEN '○'";
$sql .= "         WHEN 'f' THEN '×'";
$sql .= "    END,";
$sql .= "    t_con_info.goods_name,";               //アイテム名（略称）7
$sql .= "    t_con_info.set_flg,";                  //一式フラグ8
$sql .= "    t_con_info.num,";                      //数量9

$sql .= "    t_con_info.trust_cost_price,";        //営業原価10
$sql .= "    t_con_info.sale_price,";               //売上単価11
$sql .= "    t_con_info.trust_cost_amount,";       //営業金額12
$sql .= "    t_con_info.sale_amount,";              //売上金額13

$sql .= "    t_con_info.egoods_name,";              //消耗品名14
$sql .= "    t_con_info.egoods_num, ";              //消耗品数量15

$sql .= "    t_con_info.rgoods_name,";              //本体名16
$sql .= "    t_con_info.rgoods_num,";               //本体数量17

$sql .= "    t_contract.act_request_rate,";         //受託料18
$sql .= "    t_contract.request_state, ";           //受託状況19   
$sql .= "    t_contract.round_div,";                //巡回区分20
$sql .= "    t_contract.cycle,";                    //周期21
$sql .= "    t_contract.cycle_unit,";               //周期単位22
$sql .= "    CASE t_contract.cale_week ";           //週名(1-4)23
$sql .= "        WHEN '1' THEN ' 第1'";
$sql .= "        WHEN '2' THEN ' 第2'";
$sql .= "        WHEN '3' THEN ' 第3'";
$sql .= "        WHEN '4' THEN ' 第4'";
$sql .= "    END,";
$sql .= "    CASE t_contract.abcd_week ";           //週名(ABCD)24
$sql .= "        WHEN '1' THEN 'A(4週間隔)週'";
$sql .= "        WHEN '2' THEN 'B(4週間隔)週'";
$sql .= "        WHEN '3' THEN 'C(4週間隔)週'";
$sql .= "        WHEN '4' THEN 'D(4週間隔)週'";
$sql .= "        WHEN '5' THEN 'A,C(2週間隔)週'";
$sql .= "        WHEN '6' THEN 'B,D(2週間隔)週'";
$sql .= "        WHEN '21' THEN 'A(8週間隔)週'";
$sql .= "        WHEN '22' THEN 'B(8週間隔)週'";
$sql .= "        WHEN '23' THEN 'C(8週間隔)週'";
$sql .= "        WHEN '24' THEN 'D(8週間隔)週'";
$sql .= "    END,";
$sql .= "    t_contract.rday, ";                    //指定日25
$sql .= "    CASE t_contract.week_rday ";           //指定曜日26
$sql .= "        WHEN '1' THEN ' 月曜'";
$sql .= "        WHEN '2' THEN ' 火曜'";
$sql .= "        WHEN '3' THEN ' 水曜'";
$sql .= "        WHEN '4' THEN ' 木曜'";
$sql .= "        WHEN '5' THEN ' 金曜'";
$sql .= "        WHEN '6' THEN ' 土曜'";
$sql .= "        WHEN '7' THEN ' 日曜'";
$sql .= "    END,";
$sql .= "    t_contract.stand_day,";                //作業基準日27
$sql .= "    t_contract.last_day,";                 //最終巡回日28

$sql .= "    '1:' || t_staff1.staff_name || ";      //担当者１・売上率１29
$sql .= "    '(' || t_staff1.sale_rate || '%)',"; 
$sql .= "    '2:' || t_staff2.staff_name || ";      //担当者２・売上率２30
$sql .= "    '(' || t_staff2.sale_rate || '%)',"; 
$sql .= "    '3:' || t_staff3.staff_name || ";      //担当者３・売上率３31
$sql .= "    '(' || t_staff3.sale_rate || '%)',"; 
$sql .= "    '4:' || t_staff4.staff_name || ";      //担当者４・売上率４32
$sql .= "    '(' || t_staff4.sale_rate || '%)',"; 
$sql .= "    t_contract.trust_note,";               //備考33
$sql .= "    t_contract.contract_id,";              //契約情報ID34
$sql .= "    t_contract.client_id, ";               //得意先ID35
$sql .= "    t_con_info.con_info_id, ";             //契約内容ID36
$sql .= "    t_client.client_cname,";               //得意先名 37
$sql .= "    t_con_info.line, ";                    //契約内容行 38
$sql .= "    t_con_info.official_goods_name, ";     //アイテム名（正式） 39
$sql .= "    t_contract.act_div,";                  //代行料区分 40
$sql .= "    t_contract.trust_sale_amount,";        //代行料41
$sql .= "    t_contract.act_request_rate, ";         //代行料（％）42

#2009-09-22 hashimoto-y
$sql .= "    t_item.discount_flg ";                  //値引きフラグ43

$sql .= "FROM "; 
$sql .= "    t_con_info ";

$sql .= "    INNER JOIN t_contract ON t_contract.contract_id = t_con_info.contract_id ";
$sql .= "    INNER JOIN t_client ON t_contract.client_id = t_client.client_id ";

$sql .= "    LEFT JOIN t_serv ON t_serv.serv_id = t_con_info.serv_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '0'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff1 ON t_staff1.contract_id = t_contract.contract_id ";
 
$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '1'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff2 ON t_staff2.contract_id = t_contract.contract_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '2'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff3 ON t_staff3.contract_id = t_contract.contract_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '3'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff4 ON t_staff4.contract_id = t_contract.contract_id ";

#2009-09-22 hashimoto-y
$sql .= "    LEFT JOIN t_goods AS t_item ON t_item.goods_id = t_con_info.goods_id ";

$sql .= "WHERE "; 
$sql .= "    t_contract.trust_id = $shop_id "; 
$sql .= "AND ";
$sql .= "    t_contract.contract_div = '2' ";

$sql .= "ORDER BY ";
$sql .= "    t_contract.request_state,";
$sql .= "    t_contract.trust_line,";
$sql .= "    t_contract.contract_id,";
$sql .= "    t_con_info.line;";
$result = Db_Query($db_con, $sql); 
$disp_data = Get_Data($result);

//データ存在判定
if($disp_data == NULL){
	//データが存在しない場合に、表示が崩れる為、フラグによって表示形式変更
	$early_flg = true;
}

/****************************/
//データ表示形式変更
/****************************/
$row = 0;          //行数カウント
for($i=0;$i<count($disp_data);$i++){

	//順路形式変更
	if($disp_data[$i][2] != NULL){
		$disp_data[$i][2] = str_pad($disp_data[$i][2], 4, 0, STR_POS_LEFT);
		$route1 = substr($disp_data[$i][2],0,2);
		$route2 = substr($disp_data[$i][2],2,2);
		$disp_data[$i][2] = $route1."-".$route2;
	}


	//■代行料
	//固定額
	if($disp_data[$i][40] == "2"){
		$disp_data[$i][18] = Minus_Numformat($disp_data[$i][41])."円<br>(固定額)";

	//％
	}elseif($disp_data[$i][40] == "3"){
		$disp_data[$i][18] = Minus_Numformat($disp_data[$i][41])."円<br>(".$disp_data[$i][42]."%)";

	//発生しない
	}else{
		$disp_data[$i][18] = "無し";
	}

	//受託料表示判定
	//$disp_data[$i][18] = $disp_data[$i][18]."%";

	//数量
	$disp_data[$i][9] = my_number_format($disp_data[$i][9]);
	$disp_data[$i][15] = my_number_format($disp_data[$i][15]);
	$disp_data[$i][17] = my_number_format($disp_data[$i][17]);
	//単価形式変更
	for($c=10;$c<=11;$c++){
		$disp_data[$i][$c] = number_format($disp_data[$i][$c],2);
	}
	//金額形式変更
	for($c=12;$c<=13;$c++){
		$disp_data[$i][$c] = number_format($disp_data[$i][$c]);
	}
	

	//巡回日形式変更
	if($disp_data[$i][20] == "1"){
		//巡回１
		$round_data[$i] = $disp_data[$i][24].$disp_data[$i][26];
	}else if($disp_data[$i][20] == "2"){
		//巡回２
		$date_data = substr($disp_data[$i][27],0,7);

		if($disp_data[$i][25] == "30"){
			$round_data[$i] = "毎月 月末 <br>(".$date_data.")";
		}else{
			$round_data[$i] = "毎月 ".$disp_data[$i][25]."日 <br>(".$date_data.")";
		}
	}else if($disp_data[$i][20] == "3"){
		//巡回３
		$date_data = substr($disp_data[$i][27],0,7);

		$round_data[$i] = "毎月".$disp_data[$i][23].$disp_data[$i][26]."<br>(".$date_data.")";
	}else if($disp_data[$i][20] == "4"){
		//巡回４
		$round_data[$i] = $disp_data[$i][21]."週間周期の".$disp_data[$i][26]."<br>(".$disp_data[$i][27].")";
	}else if($disp_data[$i][20] == "5"){
		//巡回５
		$date_data = substr($disp_data[$i][27],0,7);
		if($disp_data[$i][25] == "30"){
			$round_data[$i] = $disp_data[$i][21]."ヶ月周期の 月末 <br>(".$date_data.")";
		}else{
			$round_data[$i] = $disp_data[$i][21]."ヶ月周期の ".$disp_data[$i][25]."日 <br>(".$date_data.")";
		}
	}else if($disp_data[$i][20] == "6"){
		//巡回６
		$date_data = substr($disp_data[$i][27],0,7);

		$round_data[$i] = $disp_data[$i][21]."ヶ月周期の ".$disp_data[$i][23].$disp_data[$i][26]."<br>(".$date_data.")";
	}else if($disp_data[$i][20] == "7"){
		//巡回７
		$round_data[$i] = "変則日<br>(最終日:".$disp_data[$i][28].")";
	}

	//巡回担当・売上比率表示判定
	for($c=29;$c<=32;$c++){
		//数値判定
		if(!ereg("[0-9]",$disp_data[$i][$c])){
			//値が入力されていない場合は、NULL
			$disp_data[$i][$c] = NULL;
		}else{
			//値が入力されている場合は、メイン以外は改行を追加
			if($c!=29){
				$disp_data[$i][$c] = "<br>".$disp_data[$i][$c];
			}
		}
	}

	//行No.変更判定
	if($disp_data[$i][34] != $line_num){
		//一行目判定
		if($i != 0){
			//１契約のデータ結合行数を配列に追加
			$disp_data[$i-$row][101] = $row;

			//行の背景色指定
			if($color_flg == true){
				//色を緑にする
				$disp_data[$i-$row][100] = true;
				$color_flg = false;
			}else{
				//色を白にする
				$disp_data[$i-$row][100] = false;
				$color_flg = true;
			}

			//次の行No.をセット
			$line_num = $disp_data[$i][34];
			$row = 0;
		}else{
			//一行目の場合は、行No.をセット
			$line_num = $disp_data[$i][34];
		}
	}

	//行の背景色指定
	if($color_flg == true){
		//色を緑にする
		$disp_data[$i][100] = true;
	}else{
		//色を白にする
		$disp_data[$i][100] = false;
	}

	$row++;

	/*
	//内訳指定判定
	$sql  = "SELECT con_info_id FROM t_con_detail WHERE con_info_id = ".$disp_data[$i][36].";";
	$result = Db_Query($db_con, $sql);
	$row_num = pg_num_rows($result);
	if(1 <= $row_num){
		//内訳リンク表示
		$disp_data[$i][41] = true;
	}else{
		//内訳リンク非表示
		$disp_data[$i][41] = false;
	}

	*/

}

//最後の行数をセット
$disp_data[$i-$row][101] = $row;

//行の背景色指定
if($color_flg == true){
	//色を緑にする
	$disp_data[$i-$row][100] = true;
}else{
	//色を白にする
	$disp_data[$i-$row][100] = false;
}

/****************************/
//全件数取得
/****************************/
$client_sql  = " SELECT ";
$client_sql .= "     t_client.client_id ";
$client_sql .= " FROM";
$client_sql .= "     t_client ";
$client_sql .= "     INNER JOIN t_contract ON t_client.client_id = t_contract.client_id ";
$client_sql .= " WHERE";
$client_sql .= "     t_contract.trust_id = $shop_id";
$client_sql .= "     AND";
$client_sql .= "     t_client.client_div = '1'";
$client_sql .= "     AND";
$client_sql .= "     t_contract.contract_div = '2'";

//ヘッダーに表示させる全件数
$count_res = Db_Query($db_con, $client_sql.";");
$total_count = pg_num_rows($count_res);

/****************************/
//HTMLヘッダ
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//ディレクトリ
/****************************/
$fc_page = FC_DIR."system/2-1-104.php";

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
	'html_header'    => "$html_header",
	'page_menu'      => "$page_menu",
	'page_header'    => "$page_header",
	'html_footer'    => "$html_footer",
	'fc_page'        => "$fc_page",
	'client_flg'     => "$client_flg",
	'early_flg'      => "$early_flg",
	'cname'          => "$cname",
	'intro_ac_money' => "$intro_ac_money",
	'trade_name'     => "$trade_name",
	'state'          => "$state",
	'ac_name'        => "$ac_name",
	'get_flg'        => "$get_flg",
));

//表示データ
$smarty->assign("disp_data", $disp_data);
$smarty->assign("round_data", $round_data);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
