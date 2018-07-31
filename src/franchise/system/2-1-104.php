<?php
/******************************
 *  変更履歴
 *      ・ 2006-07-26 商品マスタの構成変更に伴う抽出条件の修正＜watanabe-k＞
 *      ・ 2006-10-12 代行の際の営業原価計算処理を修正<suzuki>
 *      ・ 2006-10-24 委託先変更時に商品を初期化しないように修正<suzuki>
 *                      代行伝票の場合に自社商品が含まれているか判定する処理追加<suzuki>
 *      ・ 2006-10-27 変更時に代行区分をオンライン代行にしたときに、変更前の受注伝票を削除<suzuki>
 *                      内訳画面の戻るボタン押下処理変更＜suzuki＞
 *      ・ 2006-10-30 複写追加の次へ前へボタン押下時にチェックボックスの値が復元するように修正<suzuki>
 *
 *      ・ 2006/11/16 構成品の子に単価が設定されていなかったら表示しないように修正(suzuki)
 *      ・ 2006/11/28 削除時にログを残す(suzuki)
 *      ・ 2006/12/01 代行伝票の営業原価は委託先の丸めを使用(suzuki)
 *      ・ 2006/12/28 行の入力クリアを追加(morita-d)
 *      ・ 2007/01/19 全巡回サイクルに基準日を追加(morita-d)
 *      ・ 2007/01/22 修正有効日を追加(morita-d)
 *      ・ 2007/01/24 契約終了日を追加(morita-d)
 *      ・ 2007/03/29 変更履歴を表示するため、ログに記録する項目を追加(morita-d)
 *      ・ 2007/03/30 ソース簡略化（行No.の初期値取得を関数化）<morita-d>
 *      ・ 2007/03/30 基本倉庫を取得する処理を削除<morita-d>
 *      ・ 2007/04/03 得意先名を変更してもアイテム（正式）の入力欄が空白にならない不具合を修正<morita-d>
 *      ・ 2007/04/04 エラーメッセージはsetElementErrorを使用するように統一<morita-d>
 *      ・ 2007/04/04 アイテム（正式名）とアイテム（略称）のエラー処理を追加<morita-d>
 *      ・ 2007/04/04 巡回日テーブルは伝票を作成する日付のみを登録するように修正<morita-d>
 *      ・ 2007/04/05 紹介口座料の有無を設定する機能を追加<morita-d>
 *      ・ 2007/04/06 契約状態を設定可能にする機能を追加<morita-d>
 *      ・ 2007/04/10 契約IDのチェックを追加<morita-d>
 *      ・ 2007/04/10 消耗品名と本体商品名が空白の場合のエラーチェックを追加<morita-d>
 *      ・ 2007/04/23 代行料（固定額）を追加<morita-d>
 *      ・ 2007/04/24 契約発効日、修正発効日は本日より前をエラーとするように変更<morita-d>
 *      ・ 2007/04/27 通常からオンライン代行に変更するとエラーとなる問題を修正<morita-d>
 *      ・ 2007/04/27 巡回日関連のエラーメッセージを詳細に表示するよう修正<morita-d>
 *      ・ 2007/04/28 オンライン代行からオフライン代行に変更すると以前の伝票が残ったままになる不具合を修正<morita-d>
 *      ・ 2007-06-01 伝票の作成は、「得意先単位」でなく「契約単位」で実施するように修正<morita-d>
 *      ・ 2007-06-05 消耗品または本体に間違った商品コードを入力するとアイテムの正式名が空白になる不具合を修正<morita-d>
 *      ・ 2007-06-05 契約発効日と修正発効日のエラーを詳細に出すように修正<morita-d>
 *      ・ 2007-06-09 予定データが重複して作成される場合は警告を表示するように修正<morita-d>
 *      ・ 2007-06-11 商品マスタと同期するか設定可能に修正<morita-d>
 *      ・ 2007-06-19 前受金に関する処理を追加<morita-d>
 *      ・ 2007-06-21 複写追加の次へ前へボタンで遷移後、戻るボタンが表示されない不具合を修正<morita-d>
 *      

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-11-07      01-013      suzuki      契約NOが1000の時にエラー表示
 *  2006-11-07      01-012      suzuki      基準日の月に１桁で入力しても伝票が作成されるように修正
 *  2006-11-10      01-015      suzuki      代行委託料計算を正しく動作するように修正
 *  2006-12-07      ban_0103    suzuki      契約日をゼロ埋め
 *  2006-12-10      03-081      suzuki      代行伝票の際には、状態が有効の商品のみ使用可
 *  2006-12-14      0060        suzuki      代行伝票登録時に、内訳の営業原価計算処理追加
 *  2007/07/04      xx-xxx      kajioka-h   オンライン代行では、順路、巡回担当者を更新・削除しないように変更
 *  2007/08/29                  kajioka-h   代行伝票で代行料が売上％ので一式○の場合、原価合計額は原価単価と同じにする
 *  2009/06/25		改修No.37	aizawa-m	変則日の年数を2年間→5年間に変更
 *  2009/09/09		            aoyama-n    値引機能追加 
 *  2009/09/17		            aoyama-n    取引区分の値引・返品および値引商品は赤字で表示 
 *
******************************/
$s_time = microtime();
$page_title = "契約マスタ";

require_once("ENV_local.php");                    //環境設定ファイル 
require_once(INCLUDE_DIR."error_msg_list.inc");   //エラーメッセージ
require_once(INCLUDE_DIR."function_keiyaku.inc"); //契約関連の関数
//require_once(INCLUDE_DIR."rental.inc"); //消費税を取得する関数を利用するため

//DBに接続
$db_con = Db_Connect();

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

// 権限チェック
$auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;
// 削除ボタンDisabled
$del_disabled = ($auth[1] == "f") ? "disabled" : null;

/****************************/
//外部変数取得
/****************************/
$client_id   = $_GET["client_id"];      //得意先
$daiko_id    = $_POST["daiko_id"];      //代行ID
$flg         = $_GET["flg"];            //追加・更新判別フラグ
$get_con_id  = $_GET["contract_id"];    //契約情報ID
$return_flg  = $_GET["return_flg"];     //戻るボタン表示判定フラグ
$get_flg     = $_GET["get_flg"];        //契約概要の戻るボタン遷移判定フラグ
$c_check     = $_GET["c_check"];        //変則日復元判定フラグ
$client_h_id = $_SESSION["client_id"];  //ログインユーザID
$rank_cd     = $_SESSION["fc_rank_cd"]; //顧客区分コード
$staff_id    = $_SESSION["staff_id"];   //ログイン者ID
$group_kind  = $_SESSION["group_kind"]; //グループ種別

//直リンクで遷移してきた場合には、TOPに飛ばす
if(count($_GET) == 0){
	Get_ID_Check2();
}

//得意先IDをhiddenにより保持する
if($_GET["client_id"] != NULL){
	$con_data2["hdn_client_id"] = $client_id;
}else{
	$client_id = $_POST["hdn_client_id"];
}

//代行IDをhiddenにより保持する
if($_POST["hdn_daiko_id"] != NULL){
	$con_data2["hdn_daiko_id"] = $daiko_id;
}else{
	$daiko_id = $_POST["hdn_daiko_id"];
}

//追加・更新判別フラグをhiddenにより保持する
if($_GET["flg"] != NULL){
	$con_data2["hdn_flg"] = $flg;
}else{
	$flg = $_POST["hdn_flg"];
}

//契約情報IDをhiddenにより保持する
if($_GET["contract_id"] != NULL){
	$con_data2["hdn_con_id"] = $get_con_id;
}else{
	$get_con_id = $_POST["hdn_con_id"];
}

//戻るボタン表示判定フラグをhiddenにより保持する
if($_GET["return_flg"] != NULL){
	$con_data2["hdn_return_flg"] = $return_flg;
}else{
	$return_flg = $_POST["hdn_return_flg"];
}

//契約概要の戻るボタン遷移判定フラグをhiddenにより保持する
if($_GET["get_flg"] != NULL){
	$con_data2["hdn_get_flg"] = $get_flg;
}else{
	$get_flg = $_POST["hdn_get_flg"];
}

//変則日復元フラグをhiddenにより保持する
if($_GET["c_check"] != NULL){
	$con_data2["hdn_c_check_flg"] = $c_check;
}else{
	$c_check = $_POST["hdn_c_check_flg"];
}


//不正判定
Get_ID_Check3($client_id);
Get_ID_Check3($daiko_id);
Get_ID_Check3($get_con_id);


/****************************/
//初期表示の判別
/****************************/
if ($flg == "copy" && $client_id != NULL && $get_con_id != NULL){
	//echo "複写";
	$init_type = "複写";

} elseif ($flg == "chg" && $client_id != NULL && $get_con_id != NULL){
	//echo "変更";
	$init_type = "変更";

} elseif ($flg == "add" && $get_con_id == NULL){
	//echo "新規";
	$init_type = "新規";

} else {
	echo "不正な値が入力されました";
	exit;
}

/****************************/
//初期設定
/****************************/
//変則日画面から遷移 or 得意先選択後に画面更新
if( $c_check == true || $_POST[renew_flg] == "1" ){

	//基準日
	if($_POST["form_stand_day"]["y"] !="" && $_POST["form_stand_day"]["m"]!="" && $_POST["form_stand_day"]["d"]!="" ){
		$stand_ymd[0] = str_pad($_POST["form_stand_day"]["y"],4,0,STR_PAD_LEFT);
		$stand_ymd[1] = str_pad($_POST["form_stand_day"]["m"],2,0,STR_PAD_LEFT);
		$stand_ymd[2] = str_pad($_POST["form_stand_day"]["d"],2,0,STR_PAD_LEFT);
		$stand_day    = $stand_ymd[0]."-".$stand_ymd[1]."-".$stand_ymd[2];    //作業基準日
	}

	//修正有効日を結合
	if($_POST["form_update_day"]["y"] !="" && $_POST["form_update_day"]["m"]!="" && $_POST["form_update_day"]["d"]!="" ){
		$update_ymd[0] = str_pad($_POST["form_update_day"]["y"],4,0,STR_PAD_LEFT);
		$update_ymd[1] = str_pad($_POST["form_update_day"]["m"],2,0,STR_PAD_LEFT);
		$update_ymd[2] = str_pad($_POST["form_update_day"]["d"],2,0,STR_PAD_LEFT);
		$update_day    = $update_ymd[0]."-".$update_ymd[1]."-".$update_ymd[2]; 
	}

	//契約終了日を結合
	if($_POST["form_contract_eday"]["y"] !="" && $_POST["form_contract_eday"]["m"]!="" && $_POST["form_contract_eday"]["d"]!="" ){
		$contract_ymd[0] = str_pad($_POST["form_contract_eday"]["y"],4,0,STR_PAD_LEFT);
		$contract_ymd[1] = str_pad($_POST["form_contract_eday"]["m"],2,0,STR_PAD_LEFT);
		$contract_ymd[2] = str_pad($_POST["form_contract_eday"]["d"],2,0,STR_PAD_LEFT);
		$contract_eday   = $contract_ymd[0]."-".$contract_ymd[1]."-".$contract_ymd[2];
	}

	//契約区分
	$con_data["daiko_check"]    = $_POST["daiko_check"];
	$con_data["intro_ac_div[]"] = $_POST["intro_ac_div"][0];
	$con_data["act_div[]"]      = $_POST["act_div"][0];



}else if ($init_type == "新規"){

	$con_data["daiko_check"]        = 1; //契約区分
	$con_data["state"]              = 1; //契約状態
	$con_data["form_round_div1[]"]  = 1; //巡回日
	$con_data["intro_ac_div[]"]     = 1; //紹介口座料（発生しない）
	$con_data["act_div[]"]          = 1; //代行料（発生しない）

	for($i=1;$i<=5;$i++){
		$con_data["form_aprice_div[$i]"]      = 1; //口座料
		$con_data["form_ad_offset_radio[$i]"] = 1; //前受相殺
		//商品入力欄が表示された場合
		if($_POST[renew_flg] != "1"){
			$mst_link["mst_sync_flg[$i]"] = ""; //同期
		}
	}
	$form->setConstants($mst_link);

	//巡回日のラジオボタンの初期値をセット
	$form_load  = "Check_read( '1' );";

    //新規でclient_idがNULLなら概要での戻り先を変更
    if ($client_id == NULL) {
        Set_Rtn_Page("contract");
    }

//複写 or 変更
}else if ($init_type == "複写" || $init_type == "変更"){

	//自社の契約IDかチェック
	Chk_Contract_Id($db_con,$get_con_id);

	/****************************/
	//契約マスタ
	/****************************/
	$sql  = "SELECT ";
	$sql .= "    t_contract.line,";            //行No0
	$sql .= "    t_contract.route,";           //順路1
	$sql .= "    t_staff1.staff_id,";          //担当者1 2
	$sql .= "    t_staff1.sale_rate,";         //売上率1 3
	$sql .= "    t_staff2.staff_id,";          //担当者2 4

	$sql .= "    t_staff2.sale_rate,";         //売上率2 5
	$sql .= "    t_staff3.staff_id,";          //担当者3 6
	$sql .= "    t_staff3.sale_rate,";         //売上率3 7
	$sql .= "    t_staff4.staff_id,";          //担当者4 8
	$sql .= "    t_staff4.sale_rate,";         //売上率4 9

	$sql .= "    t_contract.round_div,";       //巡回区分10
	$sql .= "    t_contract.cycle,";           //周期11
	$sql .= "    t_contract.cycle_unit,";      //周期単位12
	$sql .= "    t_contract.cale_week,";       //週名13
	$sql .= "    t_contract.abcd_week,";       //ABCD週14

	$sql .= "    t_contract.rday,";            //指定日15
	$sql .= "    t_contract.week_rday,";       //指定曜日16
	$sql .= "    t_contract.stand_day,";       //基準日17
	$sql .= "    t_contract.contract_day,";    //契約日18
	$sql .= "    t_contract.note, ";           //備考19

	$sql .= "    t_contract.last_day,";        //変則最終日20
	$sql .= "    t_contract.contract_div,";    //契約区分21
	$sql .= "    t_contract.trust_id,";        //委託先ID22
	$sql .= "    t_contract.act_request_rate,";//代行料（％）23
	$sql .= "    t_contract.trust_ahead_note,";//委託先宛備考24

	$sql .= "    t_contract.claim_id || ',' || t_contract.claim_div, ";  //請求先ID,請求先区分25
	$sql .= "    t_contract.update_day, ";      //修正有効日26
	$sql .= "    t_contract.contract_eday, ";   //契約終了日27
	$sql .= "    t_contract.intro_ac_div, ";    //紹介口座料フラグ28
	$sql .= "    t_contract.intro_ac_price, ";  //紹介口座料（固定額）29
	$sql .= "    t_contract.intro_ac_rate, " ;   //紹介口座料（％）30
	$sql .= "    t_contract.state, " ;           //契約状態31
	$sql .= "    t_contract.act_div, ";          //代行料フラグ32
	$sql .= "    t_contract.act_request_price, ";   //代行料（固定額）33
	$sql .= "    t_contract.claim_div ";          //請求先区分34


	$sql .= "FROM ";                
	$sql .= "    t_contract ";

	$sql .= "    LEFT JOIN t_goods ON t_goods.goods_id = t_contract.act_goods_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '0'";
	$sql .= "        )AS t_staff1 ON t_staff1.contract_id = t_contract.contract_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '1'";
	$sql .= "        )AS t_staff2 ON t_staff2.contract_id = t_contract.contract_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '2'";
	$sql .= "        )AS t_staff3 ON t_staff3.contract_id = t_contract.contract_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '3'";
	$sql .= "        )AS t_staff4 ON t_staff4.contract_id = t_contract.contract_id ";

	$sql .= "WHERE ";
	$sql .= "    t_contract.contract_id = $get_con_id;";
	
	$result = Db_Query($db_con, $sql); 
	Get_Id_Check($result);
	$data_list = Get_Data($result,2);

	$con_data["form_line"]                = $data_list[0][0];   //行
	//順路が指定されていた場合に形式変更
	if($data_list[0][1] != NULL){
		$data_list[0][1]                      = str_pad($data_list[0][1], 4, 0, STR_POS_LEFT); //順路
		$con_data["form_route_load"][1]       = substr($data_list[0][1],0,2);  
		$con_data["form_route_load"][2]       = substr($data_list[0][1],2,2);
	} 
	$con_data["form_c_staff_id1"]         = $data_list[0][2];  //担当者１
	$con_data["form_sale_rate1"]          = $data_list[0][3];  //売上率１
	$con_data["form_c_staff_id2"]         = $data_list[0][4];  //担当者２
	$con_data["form_sale_rate2"]          = $data_list[0][5];  //売上率２
	$con_data["form_c_staff_id3"]         = $data_list[0][6];  //担当者３
	$con_data["form_sale_rate3"]          = $data_list[0][7];  //売上率３
	$con_data["form_c_staff_id4"]         = $data_list[0][8];  //担当者４
	$con_data["form_sale_rate4"]          = $data_list[0][9];  //売上率４
	$con_data["form_round_div1[]"]        = $data_list[0][10];  //巡回区分

	//巡回区分判定 
	if($data_list[0][10] == 1){
		//巡回１
		$con_data["form_abcd_week1"]      = $data_list[0][14];  //週名（ABCD）
		$con_data["form_week_rday1"]      = $data_list[0][16];  //指定曜日

	}else if($data_list[0][10] == 2){
		//巡回２
		$con_data["form_rday2"]           = $data_list[0][15];  //指定日

	}else if($data_list[0][10] == 3){
		//巡回３
		$con_data["form_cale_week3"]      = $data_list[0][13];  //週名（１〜４）
		$con_data["form_week_rday3"]      = $data_list[0][16];  //指定曜日

	}else if($data_list[0][10] == 4){
		//巡回４
		$con_data["form_cale_week4"]      = $data_list[0][11];  //周期

		$week[1] = "月";
		$week[2] = "火";
		$week[3] = "水";
		$week[4] = "木";
		$week[5] = "金";
		$week[6] = "土";
		$week[7] = "日";
		$con_data["form_week_rday4"] = $week[$data_list[0][16]]; //指定曜日

	}else if($data_list[0][10] == 5){
		//巡回５
		$con_data["form_cale_month5"]     = $data_list[0][11];  //周期
		$con_data["form_week_rday5"]      = $data_list[0][15];  //指定日

	}else if($data_list[0][10] == 6){
		//巡回６
		$con_data["form_cale_month6"]     = $data_list[0][11];  //周期
		$con_data["form_cale_week6"]      = $data_list[0][13];  //週名（１〜４）
		$con_data["form_week_rday6"]      = $data_list[0][16];  //指定曜日

	//}else if($data_list[0][10] == 7 && $c_check != true){
	}else if($data_list[0][10] == 7){
		//巡回７(＊変則日画面から遷移してきたときは、入力値を優先する為以下の処理は行わない)
		
		$sql  = "SELECT ";
		$sql .= "    round_day ";                //巡回日
		$sql .= "FROM ";
		$sql .= "    t_round ";
		$sql .= "WHERE ";
		$sql .= "    contract_id = $get_con_id;";
		$result = Db_Query($db_con, $sql);
		$round_data = Get_Data($result);
		//登録された巡回日分hiddenを作成し、値を復元
		for($i=0;$i<count($round_data);$i++){
			$year  = (int) substr($round_data[$i][0],0,4);
			$month = (int) substr($round_data[$i][0],5,2);
			$day   = (int) substr($round_data[$i][0],8,2);
			
			$input_date = "check_".$year."-".$month."-".$day;
			//値を復元する為に変則日のチェックをhiddenで作成
			$form->addElement("hidden","$input_date","");
			//値のセット
			$con_data["$input_date"] = 1;
		}

		$last_day = $data_list[0][20];  //巡回日の最終日

	}
	
	$stand_day = $data_list[0][17];             //基準日
	$stan_day = explode('-',$data_list[0][17]);             //基準日
	$con_data["form_stand_day"]["y"] = $stan_day[0];
	$con_data["form_stand_day"]["m"] = $stan_day[1];
	$con_data["form_stand_day"]["d"] = $stan_day[2];

	$con_day = explode('-',$data_list[0][18]);                  //契約日
	$con_data["form_contract_day"]["y"] = $con_day[0];
	$con_data["form_contract_day"]["m"] = $con_day[1];
	$con_data["form_contract_day"]["d"] = $con_day[2];

	$con_data["form_note"]              = $data_list[0][19];    //備考
	$con_data["daiko_check"]            = $data_list[0][21];    //契約区分
	$daiko_div                          = $data_list[0][21];    //契約区分
	$daiko_id                           = $data_list[0][22];    //委託先ID
	$con_data["hdn_daiko_id"]           = $data_list[0][22];  
	$con_data["form_daiko_note"]        = $data_list[0][24];    //委託先宛備考
	$con_data["form_claim"]             = $data_list[0][25];    //請求先

	$update_day = $data_list[0][26];
	$update_ymd = explode('-',$data_list[0][26]);                  //修正有効日
	$con_data["form_update_day"]["y"] = $update_ymd[0];
	$con_data["form_update_day"]["m"] = $update_ymd[1];
	$con_data["form_update_day"]["d"] = $update_ymd[2];

	$contract_eday = $data_list[0][27];
	$contract_ymd = explode('-',$data_list[0][27]);                  //契約終了日
	$con_data["form_contract_eday"]["y"] = $contract_ymd[0];
	$con_data["form_contract_eday"]["m"] = $contract_ymd[1];
	$con_data["form_contract_eday"]["d"] = $contract_ymd[2];

	$con_data["intro_ac_div[]"]    = $data_list[0][28];
	$con_data["intro_ac_price"]    = $data_list[0][29];
	$con_data["intro_ac_rate"]     = $data_list[0][30];
	$con_data["state"]             = $data_list[0][31];
	$con_data["act_div[]"]         = $data_list[0][32];
	$con_data["act_request_price"] = $data_list[0][33];
	$con_data["act_request_rate"]  = $data_list[0][23];    //代行委託料（％）

	$form_ad_rest_price = Minus_Numformat(Advance_Offset_Claim($db_con, $g_today, $client_id, $data_list[0][34]));
	$con_data["form_ad_rest_price"] = $form_ad_rest_price;    //前受金残高

	/****************************/
	//次へ戻るボタン用のID取得
	/****************************/
	$id_data = Make_Get_Id2($db_con,$client_id,$get_con_id);
	$next_id = $id_data[0];
	$back_id = $id_data[1];

	/****************************/
	//契約内容テーブル
	/****************************/
	$sql  = "SELECT ";
	$sql .= "    t_con_info.line,";          //行数0
	$sql .= "    t_con_info.divide, ";       //販売区分1
	$sql .= "    t_con_info.serv_pflg,";     //サービス印字フラグ2
	$sql .= "    t_con_info.serv_id,";       //サービスID3

	$sql .= "    t_con_info.goods_pflg,";    //アイテム印字フラグ4
	$sql .= "    t_con_info.goods_id,";      //アイテムID5
	$sql .= "    t_item.goods_cd,";          //アイテムCD6
	$sql .= "    t_item.name_change,";       //アイテム品名変更7
	$sql .= "    t_con_info.goods_name,";    //アイテム名8
	$sql .= "    t_con_info.num,";           //アイテム数9

	$sql .= "    t_con_info.set_flg,";       //一式フラグ10
	$sql .= "    t_con_info.trade_price,";   //営業原価11
	$sql .= "    t_con_info.sale_price,";    //売上単価12
	$sql .= "    t_con_info.trade_amount,";  //営業金額13   
	$sql .= "    t_con_info.sale_amount,";   //売上金額14  

	$sql .= "    t_con_info.rgoods_id,";     //本体ID15
	$sql .= "    t_body.goods_cd,";          //本体CD16
	$sql .= "    t_body.name_change,";       //本体品名変更17
	$sql .= "    t_con_info.rgoods_name,";   //本体名18
	$sql .= "    t_con_info.rgoods_num,";    //本体数19
	
	$sql .= "    t_con_info.egoods_id,";     //消耗品ID20
	$sql .= "    t_expend.goods_cd,";        //消耗品CD21
	$sql .= "    t_expend.name_change,";     //消耗品品名変更22
	$sql .= "    t_con_info.egoods_name,";   //消耗品名23
	$sql .= "    t_con_info.egoods_num,";    //消耗品数24

	$sql .= "    t_con_info.account_price,"; //口座単価25
	$sql .= "    t_con_info.account_rate, "; //口座率26  
	$sql .= "    t_con_info.con_info_id, ";   //契約内容ID27  
	$sql .= "    t_con_info.official_goods_name, ";   //商品名（正式）28
	$sql .= "    t_con_info.mst_sync_flg, ";           //商品マスタ同期フラグ 29
	$sql .= "    t_con_info.advance_flg, ";            //前受相殺フラグ 30
    //aoyama-n 2009-09-09
	#$sql .= "    t_con_info.advance_offset_amount ";   //前受相殺額 31
	$sql .= "    t_con_info.advance_offset_amount, ";   //前受相殺額 31
	$sql .= "    t_item.discount_flg ";                //値引フラグ 32

	$sql .= "FROM ";
	$sql .= "    t_con_info ";
	$sql .= "    LEFT JOIN t_goods AS t_item ON t_item.goods_id = t_con_info.goods_id ";
	$sql .= "    LEFT JOIN t_goods AS t_body ON t_body.goods_id = t_con_info.rgoods_id ";
	$sql .= "    LEFT JOIN t_goods AS t_expend ON t_expend.goods_id = t_con_info.egoods_id ";
	
	$sql .= "WHERE ";
	$sql .= "    contract_id = $get_con_id; ";
	$result = Db_Query($db_con, $sql);
	$sub_data = Get_Data($result,2);

	//契約IDに該当するデータが存在するか
	for($s=0;$s<count($sub_data);$s++){
		$search_line = $sub_data[$s][0];   //復元する行
		//口座区分の初期値に設定しない行配列
		$aprice_array[] = $search_line;

		$con_data["form_divide"][$search_line]                = $sub_data[$s][1];   //販売区分


		/*
		 * 履歴：
		 * 　日付　　　　B票No.　　　　担当者　　　内容　
		 * 　2006/10/30　01-003　　　　suzuki-t　　複写追加の次へ前へボタン押下時にチェックボックスの値が復元するように修正
		 *
		*/
		//初期表示のみ復元
		if($_POST["check_value_flg"] == 't' || ($flg == 'copy' && $_POST["copy_value_flg"] == NULL)){
			//チェック付けるか判定
			if($sub_data[$s][2] == 't'){
				$con_data2["form_print_flg1"][$search_line]   = $sub_data[$s][2];    //サービス印字フラグ
			}
			$con_data2["copy_value_flg"] = "t";

			//チェック付けるか判定
			if($sub_data[$s][29] == 't'){
				$con_data2["mst_sync_flg"][$search_line]              = $sub_data[$s][29];    //商品マスタ同期フラグ
			}
		}
		$con_data["form_serv"][$search_line]                  = $sub_data[$s][3];    //サービス

		/*
		 * 履歴：
		 * 　日付　　　　B票No.　　　　担当者　　　内容　
		 * 　2006/10/30　01-003　　　　suzuki-t　　複写追加の次へ前へボタン押下時にチェックボックスの値が復元するように修正
		 *
		*/
		//初期表示のみ復元
		if($_POST["check_value_flg"] == 't' || ($flg == 'copy' && $_POST["copy_value_flg"] == NULL)){
			//チェック付けるか判定
			if($sub_data[$s][4] == 't'){
				$con_data2["form_print_flg2"][$search_line]   = $sub_data[$s][4];    //アイテム伝票印字フラグ
			}
			$con_data2["copy_value_flg"] = "t";
		}

		$con_data["hdn_goods_id1"][$search_line]              = $sub_data[$s][5];    //アイテムID
		$con_data["form_goods_cd1"][$search_line]             = $sub_data[$s][6];    //アイテムCD
		$con_data["hdn_name_change1"][$search_line]           = $sub_data[$s][7];    //アイテム品名変更フラグ
		$hdn_name_change[1][$search_line]                     = $sub_data[$s][7];    //POSTする前にアイテム名の変更不可判定を行なう為
		$con_data["form_goods_name1"][$search_line]           = $sub_data[$s][8];    //アイテム名（略称）
		$con_data["form_goods_num1"][$search_line]            = $sub_data[$s][9];    //アイテム数
		$con_data["official_goods_name"][$search_line]        = $sub_data[$s][28];    //アイテム名（正式）

		/*
		 * 履歴：
		 * 　日付　　　　B票No.　　　　担当者　　　内容　
		 * 　2006/10/30　01-003　　　　suzuki-t　　複写追加の次へ前へボタン押下時にチェックボックスの値が復元するように修正
		 *
		*/
		//初期表示のみ復元
		if($_POST["check_value_flg"] == 't' || ($flg == 'copy' && $_POST["copy_value_flg"] == NULL)){
			//チェック付けるか判定
			if($sub_data[$s][10] == 't'){
				$con_data2["form_issiki"][$search_line]       = $sub_data[$s][10];    //一式フラグ
			}
			$con_data2["copy_value_flg"] = "t";
		}

		$cost_price = explode('.', $sub_data[$s][11]);                                //営業原価
		$con_data["form_trade_price"][$search_line]["1"] = $cost_price[0];  
		$con_data["form_trade_price"][$search_line]["2"] = ($cost_price[1] != null)? $cost_price[1] : '00';    

		$sale_price = explode('.', $sub_data[$s][12]);                                //売上単価
		$con_data["form_sale_price"][$search_line]["1"] = $sale_price[0];  
		$con_data["form_sale_price"][$search_line]["2"] = ($sale_price[1] != null)? $sale_price[1] : '00';

		$con_data["form_trade_amount"][$search_line]    = number_format($sub_data[$s][13]);  //営業金額
		$con_data["form_sale_amount"][$search_line]     = number_format($sub_data[$s][14]);  //売上金額
	
		$con_data["hdn_goods_id2"][$search_line]              = $sub_data[$s][15];    //本体ID
		$con_data["form_goods_cd2"][$search_line]             = $sub_data[$s][16];    //本体CD
		$con_data["hdn_name_change2"][$search_line]           = $sub_data[$s][17];    //本体品名変更フラグ
		$hdn_name_change[2][$search_line]                     = $sub_data[$s][17];    //POSTする前に本体名の変更不可判定を行なう為
		$con_data["form_goods_name2"][$search_line]           = $sub_data[$s][18];    //本体名
		$con_data["form_goods_num2"][$search_line]            = $sub_data[$s][19];    //本体数

		$con_data["hdn_goods_id3"][$search_line]              = $sub_data[$s][20];    //消耗品ID
		$con_data["form_goods_cd3"][$search_line]             = $sub_data[$s][21];    //消耗品CD
		$con_data["hdn_name_change3"][$search_line]           = $sub_data[$s][22];    //消耗品品名変更フラグ
		$hdn_name_change[3][$search_line]                     = $sub_data[$s][22];    //POSTする前に消耗品名の変更不可判定を行なう為
		$con_data["form_goods_name3"][$search_line]           = $sub_data[$s][23];    //消耗品名
		$con_data["form_goods_num3"][$search_line]            = $sub_data[$s][24];    //消耗品数

		//商品単位
		if($sub_data[$s][25] != NULL){
			//円
			$con_data["form_account_price"][$search_line]       = $sub_data[$s][25];  //口座単位
			$con_data["form_aprice_div[$search_line]"] = 2;
		}else if($sub_data[$s][26] != NULL){
			//率
			$con_data["form_account_rate"][$search_line]        = $sub_data[$s][26];  //口座率
			$con_data["form_aprice_div[$search_line]"] = 3;
		}else{
			//なし
			$con_data["form_aprice_div[$search_line]"] = 1;
		}

		$con_data["form_ad_offset_radio"][$search_line]        = $sub_data[$s][30];  //前受相殺フラグ
		$con_data["form_ad_offset_amount"][$search_line]       = $sub_data[$s][31];  //前受相殺額

        //aoyama-n 2009-09-09
		$con_data["hdn_discount_flg"][$search_line]            = $sub_data[$s][32];  //値引フラグ
		
	}
	
	//データが無い行の初期値設定
	for($a=1;$a<=5;$a++){
		if(!in_array($a,$aprice_array)) {
			//なし
			$con_data["form_aprice_div[$a]"] = 1;
			$con_data["form_ad_offset_radio[$a]"] = 1;
			//$con_data2["mst_sync_flg[$a]"] = f;
		}
	}

	if($group_kind == 2){
		$t_price_readonly = "t_price_readonly('$daiko_div');";
	}
	//巡回日のラジオボタンの初期値をセット
	$form_load = "Check_read('".$data_list[0][10]."'); $t_price_readonly ";

}



$form->setDefaults($con_data);

/****************************/
//契約区分の判別
/****************************/
/*
if ( ($con_data["daiko_check"] == "2") && ($group_kind == "2") ){
	//echo "オンライン代行（委託先）";
	$mode = "2";

} elseif ( ($con_data["daiko_check"] == "2") && ($group_kind != "2") ){
	//echo "オンライン代行（受託先）";
	$mode = "3";

//契約区分が2で直営の場合はオフライン代行
} elseif ( ($con_data["daiko_check"] == "3") && ($group_kind == "2") ){
	//echo "オフライン代行";
	$mode = "4";

} elseif ($con_data["daiko_check"] == "1" || $con_data["daiko_check"] == ""){
	//echo "自社";
	$mode = "1";
}
*/
/****************************/
//処理内容
/****************************/
if($_POST["clear_flg"] == true){
	$action = "全クリア";

} elseif($_POST["clear_line"] == true){
	$action = "行クリア";

} elseif ($_POST["form_ad_sum_btn"] != ""){                                                        
	$action = "集計";

} elseif ($_POST["delete_flg"] == true){                                                        
	$action = "削除";
	
} elseif($_POST["entry_flg"] == true){
	$action = "登録";

} elseif($_POST["goods_search_row"] != null){
	$action = "商品検索";

} elseif($_POST["daiko_search_flg"] == true || ($daiko_cd1 != NULL && $daiko_cd2 != NULL)){
	$action = "代行先検索";

} elseif($_POST["client_search_flg"] == true || ($client_cd1 != NULL && $client_cd2 != NULL)){
	$action = "得意先検索";

} else {
}

/****************************/
//得意先ID取得
/****************************/
$client_flg = false;   //データ存在判定フラグ
$client_cd1         = $_POST["form_client"]["cd1"];       //得意先コード1
$client_cd2         = $_POST["form_client"]["cd2"];       //得意先コード2

//ダイアログ入力orPOSTにコードがある場合
if($_POST["client_search_flg"] == true || ($client_cd1 != NULL && $client_cd2 != NULL)){

    //得意先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_cd1 = '$client_cd1'";
    $sql .= "   AND";
	$sql .= "   client_cd2 = '$client_cd2'";
    $sql .= "   AND";
    $sql .= "   client_div = '1' ";

	//ダイアログ入力は、取引中の得意先のみ表示
	if($_POST["client_search_flg"] == true){
		$sql .= "AND ";
		$sql .= "    state = '1' ";
	}

    $sql .= "   AND";
    $sql .= "    t_client.shop_id = $client_h_id";
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $num = pg_num_rows($result);
	//該当データがある
	if($num == 1){
		//データあり
		$client_id      = pg_fetch_result($result, 0,0);        //得意先ID
	}else{
		//データなし
		$client_flg = true;
		//新規登録の為、追加ボタンのGET情報なし
		$client_id = "";
	}
}

/****************************/
//代行コード入力処理
/****************************/
$daiko_flg = false;   //データ存在判定フラグ
$daiko_cd1 = $_POST["form_daiko"]["cd1"];       //代行コード1
$daiko_cd2 = $_POST["form_daiko"]["cd2"];       //代行コード2

//ダイアログ入力orPOSTにコードがある場合
if($_POST["daiko_search_flg"] == true || ($daiko_cd1 != NULL && $daiko_cd2 != NULL)){

    //FCの情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id ";
    $sql .= " FROM";
    $sql .= "   t_client ";
	$sql .= "   INNER JOIN t_rank ON t_rank.rank_cd = t_client.rank_cd \n";
    $sql .= " WHERE";
    $sql .= "   t_client.client_cd1 = '$daiko_cd1'";
    $sql .= "   AND";
	$sql .= "   t_client.client_cd2 = '$daiko_cd2'";
    $sql .= "   AND";
    $sql .= "   t_client.client_div = '3' ";
    $sql .= "   AND";
    $sql .= "   t_client.state = '1' ";
	$sql .= "   AND ";
    $sql .= "   t_rank.group_kind = '3' \n";
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $num = pg_num_rows($result);
	//該当データがある
	if($num == 1){
		//データあり
		$daiko_id      = pg_fetch_result($result, 0,0);        //委託先ID
		$con_data2["hdn_daiko_id"] = $daiko_id;
	}else{
		//データなし
		$daiko_flg = true;
		//新規登録の為、追加ボタンのGET情報なし
		$daiko_id = "";
	}
}


/****************************/
//得意先情報取得
/****************************/
//データが存在したor複写時に実行
if(($client_flg == false && $client_id != NULL && $client_cd1 != NULL && $client_cd2 != NULL) || ($flg == "copy" && $client_id != NULL)){

	//得意先の情報を抽出
	$sql  = "SELECT";
	$sql .= "   t_client.coax, ";
	$sql .= "   t_client.client_cname,";
	$sql .= "   t_client.client_cd1 || '-' || t_client.client_cd2,";
	$sql .= "   t_client.trade_id,";
	$sql .= "   t_client.tax_franct,";
	$sql .= "   t_client.client_cd1, ";
	$sql .= "   t_client.client_cd2,";
	$sql .= "   t_client.d_staff_id1,";
	$sql .= "   t_client.d_staff_id2,";
	$sql .= "   t_client.d_staff_id3";
	//$sql .= "   t_client.intro_ac_div,";
	//$sql .= "   t_client.intro_ac_price,";
	//$sql .= "   t_client.intro_ac_rate ";
	$sql .= " FROM";
	$sql .= "   t_client ";
	$sql .= " WHERE";
	$sql .= "   t_client.client_id = $client_id";
	$sql .= ";";

	$result = Db_Query($db_con, $sql); 
	Get_Id_Check($result);
	$data_list = Get_Data($result,2);

	$coax            = $data_list[0][0];        //丸め区分（商品）
	$cname           = $data_list[0][1];        //顧客名
	$client_cd       = $data_list[0][2];        //得意先CD
	$trade_id        = $data_list[0][3];        //取引コード
	$tax_franct      = $data_list[0][4];        //消費税（端数区分）
	$client_cd1      = $data_list[0][5];        //得意先CD1
	$client_cd2      = $data_list[0][6];        //得意先CD2
	$staff1          = $data_list[0][7];        //巡回担当者１
	$staff2          = $data_list[0][8];        //巡回担当者２
	$staff3          = $data_list[0][9];        //巡回担当者３
	//$ac_div          = $data_list[0][10];       //紹介区分
	//$client_ac_price = $data_list[0][11];       //紹介料（固定金額）
	//指定無しの場合は、NULL代入
	//if($client_ac_price == NULL){
		//$client_ac_price = 'NULL';
	//}

	//$client_ac_rate  = $data_list[0][12];       //紹介料（％指定）

	//紹介口座名取得
	$sql  = "SELECT";
	#$sql .= "   CASE t_client_info.intro_account_id";
	#$sql .= "   WHEN NULL THEN '無し'";
	#$sql .= "   ELSE t_client_intro.client_cd1 || ' - ' || t_client_intro.client_cd2 || ' ' ||t_client_intro.client_cname";
	#$sql .= "   END, ";
	$sql .= "   t_client_intro.client_cd1 || ' - ' || t_client_intro.client_cd2 || ' ' ||t_client_intro.client_cname,";
	$sql .= "   t_client_info.intro_account_id ";
	//$sql .= "   CASE t_client.intro_ac_div ";
	//$sql .= "   WHEN '' THEN '無し' ";
	//$sql .= "   END ";
	$sql .= " FROM";
	$sql .= "   t_client_info ";
	$sql .= "   INNER JOIN t_client AS t_client_intro ON t_client_intro.client_id = t_client_info.intro_account_id ";
	$sql .= "   INNER JOIN t_client ON t_client.client_id = t_client_info.client_id ";
	$sql .= " WHERE";
	$sql .= "   t_client_info.client_id = $client_id";
	$sql .= ";";
	$result = Db_Query($db_con, $sql); 
	$info_list = Get_Data($result);

	$ac_name         = $info_list[0][0];        //紹介口座先
	$client_ac_id    = $info_list[0][1];        //紹介口座ID

	if($ac_name == ""){
		$ac_name = "無し";
	}

	//顧客変更時or概要から遷移したか判定
	if($_POST["client_search_flg"] == true || ($_GET["return_flg"] != NULL && $_GET["flg"] == "add")){

		//契約日(得意先の契約開始日をセット)
		$cont_sdata = Get_Init_Contract_Day($db_con,$client_id);
		$con_data2["form_contract_day"]["y"] = $cont_sdata[y];
		$con_data2["form_contract_day"]["m"] = $cont_sdata[m];
		$con_data2["form_contract_day"]["d"] = $cont_sdata[d];


		//行No.（契約の行No.からMAX+1をフォームにセット）
		$con_data2["form_line"] = Get_Init_Con_Line($db_con,$client_id);


		//巡回担当者
		//担当者１〜３にセット
		$con_data2["form_c_staff_id1"] = $staff1;
		$con_data2["form_c_staff_id2"] = $staff2;
		$con_data2["form_c_staff_id3"] = $staff3;

		//売上率(メイン)
		//売上率を100
		$con_data2["form_sale_rate1"] = 100;

		//顧客変更時のみフォーム初期化
		if($_POST["client_search_flg"] == true){
			$con_data2["form_route_load"][1]   = "";
			$con_data2["form_route_load"][2]   = "";
			$con_data2["form_note"]            = "";
			$con_data2["form_round_div1[]"]    = 1;
			$con_data2["form_abcd_week1"]      = "";
			$con_data2["form_week_rday1"]      = "";
			$con_data2["form_rday2"]           = "";
			$con_data2["form_cale_week3"]      = "";
			$con_data2["form_week_rday3"]      = "";
			$con_data2["form_cale_week4"]      = "";
			$con_data2["form_week_rday4"]      = "";
			$con_data2["form_cale_month5"]     = "";
			$con_data2["form_week_rday5"]      = "";
			$con_data2["form_cale_month6"]     = "";
			$con_data2["form_cale_week6"]      = "";
			$con_data2["form_week_rday6"]      = "";

			//商品欄を全て初期化
			for($c=1;$c<=5;$c++){
				Clear_Line_Data($form,$c);
			}

			//変則日の値初期化
			$year  = date("Y");
			$month = date("m");
			for($i=0;$i<28;$i++){
				//月の日数取得
				$now = mktime(0, 0, 0, $month+$i,1,$year);
				$num = date("t",$now);
				//２年分データ取得
				for($s=1;$s<=$num;$s++){
					$now = mktime(0, 0, 0, $month+$i,$s,$year);
					$syear  = (int) date("Y",$now);
					$smonth = (int) date("n",$now);
					$sday   = (int) date("d",$now);
					$input_date = "check_".$syear."-".$smonth."-".$sday;

					//チェックされた日付だけを取得
					if($_POST["$input_date"] != NULL){
						$con_data2["$input_date"]      = "";
					}
				}
			}

			//巡回日・口座区分を、初期化するフラグ
			$post_flg = true;
		}
	}else if($flg == "copy"){
		//複写追加の場合

		//行No.（契約の行No.からMAX+1をフォームにセット）
		$con_data2["form_line"] = Get_Init_Con_Line($db_con,$client_id);


	}

	//POST情報変更
	$con_data2["hdn_coax"]            = $coax;
	$con_data2["form_client"]["cd1"]  = $client_cd1;
	$con_data2["form_client"]["cd2"]  = $client_cd2;
	$con_data2["form_client"]["name"] = $cname;
	$con_data2["client_search_flg"]   = "";
	
}else{
	//POST情報変更
	$con_data2["form_client"]["cd1"] = "";
	$con_data2["form_client"]["cd2"] = "";
	$con_data2["form_client"]["name"] = "";
	$con_data2["client_search_flg"]   = "";
	$con_data2["form_sale_rate1"] = "";
	$con_data2["form_line"] = "";
}

$cname = addslashes($cname);  //「'」が含まれる可能性があるため処理実行

/****************************/
//代行情報取得
/****************************/
//データが存在したor複写時に実行
if(($daiko_flg == false && $daiko_id != NULL) || ($flg == "copy" && $daiko_id != NULL)){

	//得意先の情報を抽出
	$sql  = "SELECT";
	$sql .= "   t_client.client_cname,";
	$sql .= "   t_client.client_cd1, ";
	$sql .= "   t_client.client_cd2, ";
	$sql .= "   t_client.coax ";
	$sql .= " FROM";
	$sql .= "   t_client ";
	$sql .= " WHERE";
	$sql .= "   t_client.client_id = $daiko_id";
	$sql .= ";";

	$result = Db_Query($db_con, $sql); 
	Get_Id_Check($result);
	$data_list = Get_Data($result,2);

	$daiko_cname    = $data_list[0][0];        //社名
	$daiko_cd1      = $data_list[0][1];        //代行CD1
	$daiko_cd2      = $data_list[0][2];        //代行CD2
	$daiko_coax     = $data_list[0][3];        //代行の丸め区分

	//POST情報変更
	$con_data2["form_daiko"]["cd1"]  = $daiko_cd1;
	$con_data2["form_daiko"]["cd2"]  = $daiko_cd2;
	$con_data2["form_daiko"]["name"] = $daiko_cname;
	$con_data2["hdn_daiko_coax"]     = $daiko_coax;
	$con_data2["daiko_search_flg"]   = "";
	
}else{
	//POST情報変更
	$con_data2["form_daiko"]["cd1"] = "";
	$con_data2["form_daiko"]["cd2"] = "";
	$con_data2["form_daiko"]["name"] = "";
	$con_data2["hdn_daiko_coax"]     = "";
	$con_data2["daiko_search_flg"]   = "";
}

/****************************/
//商品コード入力
/****************************/
if($_POST["goods_search_row"] != null){
	
	//商品コード識別情報
	$row_data = $_POST["goods_search_row"];
	//商品データを取得する行
	$search_row = substr($row_data,0,1);
	//商品データを取得する列
	$search_line = substr($row_data,1,1);

	//通常商品・構成品の子取得SQL
	$sql  = " SELECT \n";
	$sql .= "     t_goods.goods_id, \n";                      //商品ID
	$sql .= "     t_goods.name_change, \n";                   //品名変更フラグ
	$sql .= "     t_goods.goods_cd, \n";                      //商品コード
	$sql .= "     t_goods.goods_cname, \n";                   //略称
	$sql .= "     initial_cost.r_price AS initial_price, \n"; //営業単価
	$sql .= "     sale_price.r_price AS sale_price,  \n";     //売上単価
	$sql .= "     t_goods.compose_flg,  \n";                   //構成品フラグ
    //aoyama-n 2009-09-09
	#$sql .= "     (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS official_goods_name \n";                   //正式名
	$sql .= "     (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS official_goods_name, \n";                   //正式名
	$sql .= "     t_goods.discount_flg \n";                   //値引フラグ
	                 
	$sql .= " FROM \n";
	$sql .= "     t_goods  \n";
	$sql .= "     INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
	$sql .= "     INNER JOIN  t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id \n";
	$sql .= "     INNER JOIN  t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id \n";

	$sql .= " WHERE \n";
	$sql .= "     t_goods.goods_cd = '".$_POST["form_goods_cd".$search_line][$search_row]."'  \n";
	$sql .= " AND  \n";
	$sql .= "     t_goods.compose_flg = 'f'  \n";
	$sql .= " AND  \n";
	$sql .= "     initial_cost.rank_cd = '2'  \n";
	$sql .= " AND  \n";
	$sql .= "     sale_price.rank_cd = '4' \n";
//watanabe-k変更
    $sql .= " AND  \n";
    $sql .= "     t_goods.accept_flg = '1' \n";
    $sql .= " AND  \n";
    //直営かつ通常伝票判定
    if($_SESSION[group_kind] == "2" && $_POST["daiko_check"] == 1 ){
        //直営かつ通常伝票
        $sql .= "     t_goods.state IN (1,3)";
    }else{
        //FCor代行伝票
        $sql .= "     t_goods.state = 1";
    }
    $sql .= " AND  \n";
    $sql .= "         initial_cost.shop_id = $client_h_id  \n";

	$sql .= " AND  \n";
	$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id) \n";

	//契約区分判定
	if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
		$sql .= "AND \n";
		$sql .= "    t_goods.public_flg = 't' \n";
	}

	//本体商品以外は構成品データを取得
	if($search_line != 2){
		//構成品の親取得SQL
		$sql .= "UNION  \n";
		$sql .= " SELECT \n";
		$sql .= "     t_goods.goods_id, \n";                      //商品ID
		$sql .= "     t_goods.name_change, \n";                   //品名変更フラグ
		$sql .= "     t_goods.goods_cd, \n";                      //商品コード
		$sql .= "     t_goods.goods_cname, \n";                   //略称
		$sql .= "     NULL, \n";
		$sql .= "     NULL, \n";
		$sql .= "     t_goods.compose_flg,  \n";                   //構成品フラグ
        //aoyama-n 2009-09-09
		#$sql .= "     t_goods.goods_name  AS official_goods_name \n";                   //正式名
		$sql .= "     t_goods.goods_name  AS official_goods_name, \n";                   //正式名
	    $sql .= "     t_goods.discount_flg \n";                   //値引フラグ
		$sql .= " FROM \n";
		$sql .= "     t_goods  \n";
		$sql .= " WHERE \n";
		$sql .= "     t_goods.goods_cd = '".$_POST["form_goods_cd".$search_line][$search_row]."'  \n";
		$sql .= " AND  \n";
		$sql .= "     t_goods.compose_flg = 't'  \n";
//watanabe-k変更
        $sql .= " AND  \n";
        $sql .= "     t_goods.accept_flg = '1'   \n";
        $sql .= " AND  \n";

		//直営かつ通常伝票判定
	    if($_SESSION[group_kind] == "2" && $_POST["daiko_check"] == 1 ){
	        //直営かつ通常伝票
	        $sql .= "     t_goods.state IN (1,3) \n";
	    }else{
	        //FCor代行伝票
	        $sql .= "     t_goods.state = 1 \n";
	    }

		//契約区分判定
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			$sql .= "AND \n";
			$sql .= "    t_goods.public_flg = 't' \n";
		}
	}

	$result = Db_Query($db_con, $sql.";");
    $data_num = pg_num_rows($result);
	//データが存在した場合、フォームにデータを表示
	if($data_num == 1){
    	$goods_data = pg_fetch_array($result);

		$con_data2["hdn_goods_id".$search_line][$search_row]         = $goods_data[0];   //商品ID
		$con_data2["hdn_name_change".$search_line][$search_row]      = $goods_data[1];   //品名変更フラグ
		$con_data2["form_goods_cd".$search_line][$search_row]        = $goods_data[2];   //商品CD
		$con_data2["form_goods_name".$search_line][$search_row]      = $goods_data[3];   //商品名
		$hdn_name_change[$search_line][$search_row]                  = $goods_data[1];   //POSTする前に商品名の変更不可判定を行なう為

		//アイテム欄の商品コードが入力された場合は金額の計算を実施する
		if($search_line == 1){
			$con_data2["official_goods_name"][$search_row] = $goods_data[official_goods_name];   //商品名

            //aoyama-n 2009-09-09
	        $con_data2["hdn_discount_flg"][$search_row]    = $goods_data[8];                     //値引フラグ

			//構成品判定
			if($goods_data[6] == 'f'){
				//構成品ではない

				//原価単価を整数部と少数部に分ける
				$com_c_price = $goods_data[4];
				$c_price = explode('.', $goods_data[4]);
				$con_data2["form_trade_price"][$search_row]["1"] = $c_price[0];  //営業単価
				$con_data2["form_trade_price"][$search_row]["2"] = ($c_price[1] != null)? $c_price[1] : '00';     

				//売上単価を整数部と少数部に分ける
				$com_s_price = $goods_data[5];
				$s_price = explode('.', $goods_data[5]);
				$con_data2["form_sale_price"][$search_row]["1"] = $s_price[0];  //売上単価
				$con_data2["form_sale_price"][$search_row]["2"] = ($s_price[1] != null)? $s_price[1] : '00';

				//金額計算処理判定
				if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] != null){
				//一式○　数量○の場合、営業金額は、単価×数量。売上金額は、単価×１
					//営業金額計算
		            $cost_amount = bcmul($goods_data[4], $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($goods_data[5], 1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
				//一式○　数量×の場合、単価×１
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//営業金額計算
		            $cost_amount = bcmul($goods_data[4],1,2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($goods_data[5],1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
				//一式×　数量○の場合、単価×数量
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//営業金額計算
		            $cost_amount = bcmul($goods_data[4], $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($goods_data[5], $_POST["form_goods_num1"][$search_row],2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
				}
			}else{
				//構成品

				//構成品の子の商品情報取得
				$sql  = "SELECT ";
				$sql .= "    parts_goods_id ";                       //構成品ID
				$sql .= "FROM ";
				$sql .= "    t_compose ";
				$sql .= "WHERE ";
				$sql .= "    goods_id = ".$goods_data[0].";";
				$result = Db_Query($db_con, $sql);
				$goods_parts = Get_Data($result);

				//各構成品の単価取得
				$com_c_price = 0;     //構成品親の営業原価
				$com_s_price = 0;     //構成品親の売上単価

				for($i=0;$i<count($goods_parts);$i++){
					$sql  = " SELECT ";
					$sql .= "     t_compose.count,";                       //数量
					$sql .= "     initial_cost.r_price AS initial_price,"; //営業単価
					$sql .= "     sale_price.r_price AS sale_price, ";     //売上単価
					$sql .= "     buy_price.r_price AS buy_price  ";       //仕入単価
					                 
					$sql .= " FROM";
					$sql .= "     t_compose ";

					$sql .= "     INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
					$sql .= "     INNER JOIN t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
					$sql .= "     INNER JOIN t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";
					$sql .= "     INNER JOIN t_price AS buy_price ON t_goods.goods_id = buy_price.goods_id";

					$sql .= " WHERE";
					$sql .= "     t_compose.goods_id = ".$goods_data[0];
					$sql .= " AND ";
					$sql .= "     t_compose.parts_goods_id = ".$goods_parts[$i][0];
					$sql .= " AND ";
					$sql .= "     initial_cost.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     sale_price.rank_cd = '4'";
					$sql .= " AND ";
					$sql .= "     buy_price.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     initial_cost.shop_id = $client_h_id  \n";
					$sql .= " AND  \n";
					$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id); \n";
					$result = Db_Query($db_con, $sql);
					$com_data = Get_Data($result);
					//構成品の子に単価が設定されていないか判定
					if($com_data == NULL){
						$reset_goods_flg = true;   //入力された商品情報をクリア
					}

					//構成品親の営業単価計算配列に追加(子の数量×子の営業原価)
					$com_cp_amount = bcmul($com_data[0][0],$com_data[0][1],2);
		            $com_cp_amount = Coax_Col($coax, $com_cp_amount);
					$com_c_price = $com_c_price + $com_cp_amount;
					//構成品親の売上単価計算配列に追加(子の数量×子の売上単価)
					$com_sp_amount = bcmul($com_data[0][0],$com_data[0][2],2);
		            $com_sp_amount = Coax_Col($coax, $com_sp_amount);
					$com_s_price = $com_s_price + $com_sp_amount;
				}

				//原価単価を整数部と少数部に分ける
		        $com_cost_price = explode('.', $com_c_price);
				$con_data2["form_trade_price"][$search_row]["1"] = $com_cost_price[0];  //営業単価
				$con_data2["form_trade_price"][$search_row]["2"] = ($com_cost_price[1] != null)? $com_cost_price[1] : '00';     

				//売上単価を整数部と少数部に分ける
		        $com_sale_price = explode('.', $com_s_price);
				$con_data2["form_sale_price"][$search_row]["1"] = $com_sale_price[0];  //売上単価
				$con_data2["form_sale_price"][$search_row]["2"] = ($com_sale_price[1] != null)? $com_sale_price[1] : '00';

				//構成品親の金額計算処理判定
				if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] != null){
				//一式○　数量○の場合、営業金額は、単価×数量。売上金額は、単価×１
					//営業金額計算
		            $cost_amount = bcmul($com_c_price, $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($com_s_price, 1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				//一式○　数量×の場合、単価×１
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//営業金額計算
		            $cost_amount = bcmul($com_c_price,1,2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($com_s_price,1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				//一式×　数量○の場合、単価×数量
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//営業金額計算
		            $cost_amount = bcmul($com_c_price, $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($com_s_price, $_POST["form_goods_num1"][$search_row],2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				}
			}

			/*
			//代行判定
			if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){

				//営業原価（売上単価×代行委託料率）
				$daiko_money = bcmul($com_s_price,bcdiv($_POST["act_request_price"],100,2),2);

			    $eigyo_money = explode('.',$daiko_money);

				$con_data2["form_trade_price"][$search_row]["1"] = $eigyo_money[0];
				if($eigyo_money[1] != NULL){
					$con_data2["form_trade_price"][$search_row]["2"] = $eigyo_money[1];
				}else{
					$con_data2["form_trade_price"][$search_row]["2"] = '00';
				}

				//営業金額計算処理判定
				if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] != null){
				//一式○　数量○の場合、営業金額は、単価×数量。
					//営業金額計算
			        $cost_amount = bcmul($daiko_money, $_POST["form_goods_num1"][$search_row],2);
			        $cost_amount = Coax_Col($daiko_coax, $cost_amount);
				//一式○　数量×の場合、単価×１
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//営業金額計算
			        $cost_amount = bcmul($daiko_money,1,2);
			        $cost_amount = Coax_Col($daiko_coax, $cost_amount);
				//一式×　数量○の場合、単価×数量
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//営業金額計算
			        $cost_amount = bcmul($daiko_money, $_POST["form_goods_num1"][$search_row],2);
			        $cost_amount = Coax_Col($daiko_coax, $cost_amount);
				}
			}
			*/
			$con_data2["form_trade_amount"][$search_row]    = number_format($cost_amount);
			$con_data2["form_sale_amount"][$search_row]     = number_format($sale_amount);


		}else{
			//二・三列目の商品

			//構成品判定
			if($goods_data[6] == 't'){
				//構成品の子の商品情報取得
				$sql  = "SELECT ";
				$sql .= "    parts_goods_id ";                       //構成品ID
				$sql .= "FROM ";
				$sql .= "    t_compose ";
				$sql .= "WHERE ";
				$sql .= "    goods_id = ".$goods_data[0].";";
				$result = Db_Query($db_con, $sql);
				$goods_parts = Get_Data($result);

				for($i=0;$i<count($goods_parts);$i++){
					$sql  = " SELECT ";
					$sql .= "     t_compose.count,";                       //数量
					$sql .= "     initial_cost.r_price AS initial_price,"; //営業単価
					$sql .= "     sale_price.r_price AS sale_price, ";     //売上単価
					$sql .= "     buy_price.r_price AS buy_price  ";       //仕入単価
					                 
					$sql .= " FROM";
					$sql .= "     t_compose ";

					$sql .= "     INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
					$sql .= "     INNER JOIN t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
					$sql .= "     INNER JOIN t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";
					$sql .= "     INNER JOIN t_price AS buy_price ON t_goods.goods_id = buy_price.goods_id";

					$sql .= " WHERE";
					$sql .= "     t_compose.goods_id = ".$goods_data[0];
					$sql .= " AND ";
					$sql .= "     t_compose.parts_goods_id = ".$goods_parts[$i][0];
					$sql .= " AND ";
					$sql .= "     initial_cost.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     sale_price.rank_cd = '4'";
					$sql .= " AND ";
					$sql .= "     buy_price.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     initial_cost.shop_id = $client_h_id  \n";
					$sql .= " AND  \n";
					$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id); \n";
					$result = Db_Query($db_con, $sql);
					$com_data = NULL;
					$com_data = Get_Data($result);
					//構成品の子に単価が設定されていないか判定
					if($com_data == NULL){
						$reset_goods_flg = true;   //入力された商品情報をクリア
					}
				}
			}
		}

		//構成品の子に単価が設定されていないとき、商品情報クリア
		if($reset_goods_flg == true){
			//データが無い場合は、初期化
			$con_data2["hdn_goods_id".$search_line][$search_row]         = "";
			$con_data2["hdn_name_change".$search_line][$search_row]      = "";
			$con_data2["form_goods_cd".$search_line][$search_row]        = "";
			$con_data2["form_goods_name".$search_line][$search_row]      = "";
			$con_data2["form_goods_num".$search_line][$search_row]       = "";

			//印字の指標
			$sline = $search_line+1;
			$con_data2["form_print_flg".$sline][$search_row]  = "";
			$con_data2["mst_sync_flg"][$search_row]           = "";

			//金額初期化は、アイテム欄の場合のみ
			if($search_line == 1){
				$con_data2["form_trade_price"][$search_row]["1"] = "";
				$con_data2["form_trade_price"][$search_row]["2"] = "";
				$con_data2["form_trade_amount"][$search_row]     = "";
				$con_data2["form_sale_price"][$search_row]["1"] = "";
				$con_data2["form_sale_price"][$search_row]["2"] = "";
				$con_data2["form_sale_amount"][$search_row]     = "";
			}
		}
	}else{
		//データが無い場合は、初期化
		$con_data2["hdn_goods_id".$search_line][$search_row]         = "";
		$con_data2["hdn_name_change".$search_line][$search_row]      = "";
		$con_data2["form_goods_cd".$search_line][$search_row]        = "";
		$con_data2["form_goods_name".$search_line][$search_row]      = "";
		$con_data2["form_goods_num".$search_line][$search_row]       = "";

		//印字の指標
		$sline = $search_line+1;
		$con_data2["form_print_flg".$sline][$search_row]  = "";
		$con_data2["mst_sync_flg"][$search_row]           = "";

		//金額初期化は、アイテム欄の場合のみ
		if($search_line == 1){
			$con_data2["official_goods_name"][$search_row]   = "";
			$con_data2["form_trade_price"][$search_row]["1"] = "";
			$con_data2["form_trade_price"][$search_row]["2"] = "";
			$con_data2["form_trade_amount"][$search_row]     = "";
			$con_data2["form_sale_price"][$search_row]["1"]  = "";
			$con_data2["form_sale_price"][$search_row]["2"]  = "";
			$con_data2["form_sale_amount"][$search_row]      = "";
            //aoyama-n 2009-09-09
			$con_data2["hdn_discount_flg"][$search_row]      = "";
		}
	}
	$con_data2["goods_search_row"]                  = "";

}

/****************************/
//クリアボタン押下処理
/****************************/
if($_POST["clear_flg"] == true){

	for($i=1;$i<=5;$i++){
		Clear_Line_Data($form,$i);
	}
	$post_flg2 = true;                //口座区分を、初期化するフラグ
	$con_data2["clear_flg"] = "";    //クリアボタン押下フラグ
}
/****************************/
//POSTデータ取得
/****************************/
//print_array($_POST);
$line              = $_POST["form_line"];            //行No
$intro_ac_div      = $_POST["intro_ac_div"][0];
$intro_ac_price    = $_POST["intro_ac_price"];
$intro_ac_rate     = $_POST["intro_ac_rate"];
$act_div           = $_POST["act_div"][0];
$act_request_price = $_POST["act_request_price"];
$act_request_rate  = $_POST["act_request_rate"];
$state             = $_POST["state"];
$trade_amount      = NULL;                         //営業金額初期化
$sale_amount       = NULL;                         //売上金額初期化

#2010-05-29 hashimoto-y
$ad_offset_tamount = NULL;

//5行分取得
for($s=1;$s<=5;$s++){
	$divide[$s]  = $_POST["form_divide"][$s];        //販売区分
	$serv_id[$s] = $_POST["form_serv"][$s];          //サービスID

	$slip_flg[$s] = $_POST["form_print_flg1"][$s];   //サービス印字フラグ1
	if($slip_flg[$s] == NULL){
		$slip_flg[$s] = 'false';
	}else{
		$slip_flg[$s] = 'true';
	}

	$set_flg[$s] = $_POST["form_issiki"][$s];        //一式フラグ1
	if($set_flg[$s] == NULL){
		$set_flg[$s] = 'false';
	}else{
		$set_flg[$s] = 'true';
	}

	$mst_sync_flg[$s] = $_POST["mst_sync_flg"][$s];        //商品同期フラグ
	if($mst_sync_flg[$s] == NULL){
		$mst_sync_flg[$s] = 'false';
	}else{
		$mst_sync_flg[$s] = 'true';
	}

	//営業原価
	$t_price1[$s] = $_POST["form_trade_price"][$s][1]; 
	$t_price2[$s] = $_POST["form_trade_price"][$s][2];
	$trade_price[$s] = $t_price1[$s].".".$t_price2[$s];

	//売上単価
	$s_price1[$s] = $_POST["form_sale_price"][$s][1]; 
	$s_price2[$s] = $_POST["form_sale_price"][$s][2]; 
	$sale_price[$s] = $s_price1[$s].".".$s_price2[$s];

	//金額計算処理判定
	if($set_flg[$s] == 'true' && $_POST["form_goods_num1"][$s] != null){
	//一式○　数量○の場合、営業金額は、単価×数量。売上金額は、単価×１
		//営業金額
		$trade_amount[$s] = bcmul($trade_price[$s], $_POST["form_goods_num1"][$s],2);
		//代行判定
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			//委託先の丸め区分
			$trade_amount[$s] = Coax_Col($daiko_coax, $trade_amount[$s]);
		}else{
			//得意先の丸め区分
			$trade_amount[$s] = Coax_Col($coax, $trade_amount[$s]);
		}

		//売上金額
		$sale_amount[$s] = bcmul($sale_price[$s], 1,2);
	    $sale_amount[$s] = Coax_Col($coax, $sale_amount[$s]);
	
	//一式○　数量×の場合、単価×１
	}else if($set_flg[$s] == 'true' && $_POST["form_goods_num1"][$s] == null){
		//営業金額
		$trade_amount[$s] = bcmul($trade_price[$s], 1,2);
		//代行判定
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			//委託先の丸め区分
	    	$trade_amount[$s] = Coax_Col($daiko_coax, $trade_amount[$s]);
		}else{
			//得意先の丸め区分
			$trade_amount[$s] = Coax_Col($coax, $trade_amount[$s]);
		}

		//売上金額
		$sale_amount[$s] = bcmul($sale_price[$s], 1,2);
	    $sale_amount[$s] = Coax_Col($coax, $sale_amount[$s]);
	
	//一式×　数量○の場合、単価×数量
	}else if($set_flg[$s] == 'false' && $_POST["form_goods_num1"][$s] != null){
		//営業金額
		$trade_amount[$s] = bcmul($trade_price[$s], $_POST["form_goods_num1"][$s],2);
		//代行判定
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			//委託先の丸め区分
	    	$trade_amount[$s] = Coax_Col($daiko_coax, $trade_amount[$s]);
		}else{
			//得意先の丸め区分
			$trade_amount[$s] = Coax_Col($coax, $trade_amount[$s]);
		}

		//売上金額
		$sale_amount[$s] = bcmul($sale_price[$s], $_POST["form_goods_num1"][$s],2);
		$sale_amount[$s] = Coax_Col($coax, $sale_amount[$s]);
	}

	//顧客先が変更されたorクリアボタン押下の場合、初期化する為に復元しない
	if($post_flg != true && $post_flg2 != true){
		//何故か解からないが、セットしてあげないと復元されない。
		$aprice_div[$s] = $_POST["form_aprice_div"][$s][0];                //口座区分
		//$con_data2["form_aprice_div[$s]"] = $aprice_div[$s];
	}

	//契約の口座料
	//口座区分判定
	if($aprice_div[$s] == 2){
		//固定金額
		$ac_price[$s] = $_POST["form_account_price"][$s];                 //口座単位
	}else if($aprice_div[$s] == 3){
		//率
		$ac_rate[$s] = $_POST["form_account_rate"][$s];                   //口座率
	}
	
	//指定無しの場合は、NULL代入
	if($ac_price[$s] == NULL){
		$ac_price[$s] = 'NULL';
	}

	//$sale_tamount = $sale_tamount + $sale_amount[$s];                               //売上金額合計

	$advance_flg[$s]           = $_POST["form_ad_offset_radio"][$s][0];             //前受相殺フラグ
	if($advance_flg[$s] != 2){
		$advance_flg[$s] = 1;
	}

    #echo "ad_offset_tamount:" .$ad_offset_tamount;
    #echo "ad_offset_tamount:" .$_POST["form_ad_offset_amount"][$s] ."<br>";

    #2010-05-29 hashimoto-y
	$advance_offset_amount[$s] = $_POST["form_ad_offset_amount"][$s];               //前受相殺額
	#$ad_offset_tamount = $ad_offset_tamount + $advance_offset_amount[$s];           //前受相殺額（合計）
    #if( is_int($_POST["form_ad_offset_amount"][$s]) ){
    if( is_numeric($_POST["form_ad_offset_amount"][$s]) ){
        #echo "ad_offset_tamount:" .$_POST["form_ad_offset_amount"][$s] ."<br>";

        #2010-06-28 hashimoto-y
	    #$advance_offset_amount[$s] = $_POST["form_ad_offset_amount"][$s];               //前受相殺額
	    $ad_offset_tamount = $ad_offset_tamount + $advance_offset_amount[$s];           //前受相殺額（合計）
    }

}


$route  = $_POST["form_route_load"][1];      //順路
$route .= $_POST["form_route_load"][2];

//巡回担当チーム
$staff_check = NULL;                         //重複判定配列
$staff_rate = NULL;                          //売上率登録用配列

$staff1 = $_POST["form_c_staff_id1"];        //巡回担当１
$staff_check[0] = $staff1;
$rate1 = $_POST["form_sale_rate1"];          //売上率１
$staff_rate[0] = $rate1;

$staff2 = $_POST["form_c_staff_id2"];        //巡回担当２
//入力値がある場合に重複判定配列に代入
if($staff2 != NULL){
	$staff_check[1] = $staff2;
}
$rate2 = $_POST["form_sale_rate2"];          //売上率２
$staff_rate[1] = $rate2;

$staff3 = $_POST["form_c_staff_id3"];        //巡回担当３
//入力値がある場合に重複判定配列に代入
if($staff3 != NULL){
	$staff_check[2] = $staff3;
}
$rate3 = $_POST["form_sale_rate3"];          //売上率３
$staff_rate[2] = $rate3;

$staff4 = $_POST["form_c_staff_id4"];        //巡回担当４
//入力値がある場合に重複判定配列に代入
if($staff4 != NULL){
	$staff_check[3] = $staff4;
}
$rate4 = $_POST["form_sale_rate4"];          //売上率４
$staff_rate[3] = $rate4;


//巡回日データ取得
//顧客先が変更された場合、初期化する為に復元しない
if($post_flg != true){
	$round_div = $_POST["form_round_div1"][0][0];     //巡回区分
	//ラジオボタンのフォーム名が特殊なため、セットしてあげないと復元されない。
	$con_data2["form_round_div1[]"] = $round_div;
	$con_data2["intro_ac_div[]"] = $intro_ac_div;
	$con_data2["act_div[]"]      = $act_div;
}


//巡回区分判定 
if($round_div == 1){
	//巡回１
	$abcd_week = $_POST["form_abcd_week1"];       //週名（ABCD）
	$week_rday = $_POST["form_week_rday1"];       //指定曜日

}else if($round_div == 2){
	//巡回２
	$rday = $_POST["form_rday2"];                 //指定日

}else if($round_div == 3){
	//巡回３
	$cale_week = $_POST["form_cale_week3"];       //週名（１〜４）
	$week_rday = $_POST["form_week_rday3"];       //指定曜日

}else if($round_div == 4){
	//巡回４
	$cycle_unit = "W";    //周期単位
	$cycle      = $_POST["form_cale_week4"];      //周期

	$week_list["月"] = 1;
	$week_list["火"] = 2;
	$week_list["水"] = 3;
	$week_list["木"] = 4;
	$week_list["金"] = 5;
	$week_list["土"] = 6;
	$week_list["日"] = 7;
	
	$week_name  = $_POST["form_week_rday4"];      //指定曜日
	$week_rday = $week_list[$week_name];          //指定曜日コード

}else if($round_div == 5){
	//巡回５
	$cycle_unit = "M";   //周期単位
	$cycle      = $_POST["form_cale_month5"];     //周期
	$rday       = $_POST["form_week_rday5"];      //指定日

}else if($round_div == 6){
	//巡回６
	$cycle_unit = "M";   //周期単位
	$cycle      = $_POST["form_cale_month6"];     //周期
	$cale_week  = $_POST["form_cale_week6"];      //週名（１〜４）
	$week_rday  = $_POST["form_week_rday6"];      //指定曜日

}

//変則日の処理が0.7秒
//巡回日は、巡回区分が７じゃなくても変則日を設定可能
/****************************/
//変則日データ取得
/****************************/
$date_array = NULL;
//POSTデータ取得処理
$year  = date("Y");
$month = date("m");


//-- 2009/06/25 改修No.37 追加
// 変則日の表示年数を変更
$hensoku_term 	= 5;	//年数
$m_term 		= $hensoku_term * 12; //月数を算出
for($i=0;$i<$m_term;$i++){
//for($i=0;$i<28;$i++){
//---------------------------------
	//月の日数取得
	$now = mktime(0, 0, 0, $month+$i,1,$year);
	$num = date("t",$now);

	//2年分データ取得
	for($s=1;$s<=$num;$s++){
		$now = mktime(0, 0, 0, $month+$i,$s,$year);
		$syear  = (int) date("Y",$now);
		$smonth = (int) date("n",$now);
		$sday   = (int) date("d",$now);
		$input_date = "check_".$syear."-".$smonth."-".$sday;

		//チェックされた日付だけを取得
		if($_POST["$input_date"] != NULL){
			$smonth = str_pad($smonth,2, 0, STR_POS_LEFT);
			$sday = str_pad($sday,2, 0, STR_POS_LEFT);
			$date_array[] = $syear."-".$smonth."-".$sday;
			
			//値を復元する為に変則日のチェックをhiddenで作成
			$form->addElement("hidden","$input_date","");
			//POSTの最終日を優先するフラグ
			$last_flg = true;
		}
	}
}

//画面にチェックを付けた最後の日を表示
if($last_flg == true){
	$last_day = $date_array[count($date_array)-1];
}

//FC・直営判定
if($group_kind == 2){
	$daiko_div = $_POST["daiko_check"];                           //契約区分
	$t_price_readonly  = " t_price_readonly('$daiko_div'); ";
	$t_price_readonly .= " Mult_double_All('$coax',false,'$daiko_coax'); ";

}else{
	//ＦＣの契約登録は、契約区分を通常にする
	$daiko_div = '1';
}

//初期表示・変更時以外は、巡回日ラジオボタンにはPOST情報をセット
if($_POST["form_round_div1"]["0"] != NULL){
	$form_load  = "Check_read('".$_POST["form_round_div1"][0][0]."'); $t_price_readonly ";
}

//契約日形式変更
$cont_day_y           = $_POST["form_contract_day"]["y"];     //契約日
$cont_day_m           = $_POST["form_contract_day"]["m"];           
$cont_day_d           = $_POST["form_contract_day"]["d"];
if($cont_day_y != null && $cont_day_m != null && $cont_day_d != null){
	$contract_day = str_pad($cont_day_y,4,"0",STR_PAD_LEFT)."-".str_pad($cont_day_m,2,"0",STR_PAD_LEFT)."-".str_pad($cont_day_d,2,"0",STR_PAD_LEFT);
}

$claim_data = $_POST["form_claim"];                           //請求先,請求先区分
$c_data     = explode(',', $claim_data);
$claim_id   = $c_data[0];                                     //請求先ID
$claim_div  = $c_data[1];                                     //請求先区分
$note       = $_POST["form_note"];                                  //備考

//$daiko_price = $_POST["act_request_price"];                    //代行委託料
$daiko_note = $_POST["form_daiko_note"];                      //委託先宛備考

//5行分取得
for($s=1;$s<=5;$s++){
	//★契約アイテム
	$goods_item_id[$s] = $_POST["hdn_goods_id1"][$s];                   //商品ID

	//指定判定
	if($goods_item_id[$s] != NULL){

		$goods_item_cd[$s]       = $_POST["form_goods_cd1"][$s];       //商品コード
		$goods_item_name[$s]     = $_POST["form_goods_name1"][$s];     //商品名（略称）
		$official_goods_name[$s] = $_POST["official_goods_name"][$s];  //商品名（正式）
		$goods_item_num[$s]      = $_POST["form_goods_num1"][$s];      //数量
		$goods_item_flg[$s]      = $_POST["form_print_flg2"][$s];      //伝票印字フラグ
		if($goods_item_flg[$s] == NULL){
			$goods_item_flg[$s] = 'false';
		}else{
			$goods_item_flg[$s] = 'true';
		}

		$sql = "SELECT compose_flg FROM t_goods WHERE goods_id = ".$goods_item_id[$s].";";
		$result = Db_Query($db_con, $sql);
		$goods_item_com[$s] = pg_fetch_result($result,0,0);          //構成品フラグ

		//構成品判定
		if($goods_item_com[$s] == 'f'){
			//構成品では無い場合、納品フラグをfalse
			$goods_item_deli[$s] = 'false';
		}else{
			//各構成品の商品情報取得
			$sql  = "SELECT ";
			$sql .= "    parts_goods_id ";                       //構成品ID
			$sql .= "FROM ";
			$sql .= "    t_compose ";
			$sql .= "WHERE ";
			$sql .= "    goods_id = ".$goods_item_id[$s].";";
			$result = Db_Query($db_con, $sql);
			$item_parts[$s] = Get_Data($result);

			//各構成品の数量取得
			for($i=0;$i<count($item_parts[$s]);$i++){
				$sql  = "SELECT ";
				$sql .= "    t_goods.goods_name,";
				$sql .= "    t_goods.goods_cname,";
				$sql .= "    t_compose.count ";
				$sql .= "FROM ";
				$sql .= "    t_compose INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
				$sql .= "WHERE ";
				$sql .= "    t_compose.goods_id = ".$goods_item_id[$s]." ";
				$sql .= "AND ";
				$sql .= "    t_compose.parts_goods_id = ".$item_parts[$s][$i][0].";";
				$result = Db_Query($db_con, $sql);
				$item_parts_name[$s][$i]  = pg_fetch_result($result,0,0);     //商品名
				$item_parts_cname[$s][$i] = pg_fetch_result($result,0,1);     //略称
				$parts_num                = pg_fetch_result($result,0,2);     //構成品に対する数量
				$item_parts_num[$s][$i]   = $parts_num * $goods_item_num[$s]; //数量
			}
		}
	}else{
		//一式フラグ・アイテム数量のエラー判定の為、入力値を取得
		$goods_item_num[$s] = $_POST["form_goods_num1"][$s];            //数量
		$goods_item_flg[$s] = $_POST["form_print_flg2"][$s];            //伝票印字フラグ
		if($goods_item_flg[$s] == NULL){
			$goods_item_flg[$s] = 'false';
		}else{
			$goods_item_flg[$s] = 'true';
		}
	}

	//★契約本体
	$goods_body_id[$s] = $_POST["hdn_goods_id2"][$s];            //商品ID
	//指定判定
	if($goods_body_id[$s] != NULL){

		$goods_body_cd[$s]   = $_POST["form_goods_cd2"][$s];     //商品コード
		$goods_body_name[$s] = $_POST["form_goods_name2"][$s];   //略称
		$goods_body_num[$s]  = $_POST["form_goods_num2"][$s];    //数量
		
	}else{
		//本体商品・数量のエラー判定の為、入力値を取得
		$goods_body_num[$s] = $_POST["form_goods_num2"][$s];     //数量
	}

	//★契約消耗品
	$goods_expend_id[$s] = $_POST["hdn_goods_id3"][$s];          //商品ID
	//指定判定
	if($goods_expend_id[$s] != NULL){

		$goods_expend_cd[$s]   = $_POST["form_goods_cd3"][$s];     //商品コード
		$goods_expend_name[$s] = $_POST["form_goods_name3"][$s];   //商品名
		$goods_expend_num[$s]  = $_POST["form_goods_num3"][$s];    //数量
		
		$sql = "SELECT compose_flg FROM t_goods WHERE goods_id = ".$goods_expend_id[$s].";";
		$result = Db_Query($db_con, $sql);
		$goods_expend_com[$s] = pg_fetch_result($result,0,0);    //構成品フラグ

		//構成品判定
		if($goods_expend_com[$s] == 'f'){
			//構成品では無い場合、納品フラグをfalse
			$goods_expend_deli[$s] = 'false';
		}else{
			//各構成品の商品情報取得
			$sql  = "SELECT ";
			$sql .= "    parts_goods_id ";                 //構成品ID
			$sql .= "FROM ";
			$sql .= "    t_compose ";
			$sql .= "WHERE ";
			$sql .= "    goods_id = ".$goods_expend_id[$s].";";
			$result = Db_Query($db_con, $sql);
			$expend_parts[$s] = Get_Data($result);

			//各構成品の数量取得
			for($i=0;$i<count($expend_parts[$s]);$i++){
				$sql  = "SELECT ";
				$sql .= "    t_goods.goods_name,";
				$sql .= "    t_goods.goods_cname,";
				$sql .= "    t_compose.count ";
				$sql .= "FROM ";
				$sql .= "    t_compose INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
				$sql .= "WHERE ";
				$sql .= "    t_compose.goods_id = ".$goods_expend_id[$s]." ";
				$sql .= "AND ";
				$sql .= "    t_compose.parts_goods_id = ".$expend_parts[$s][$i][0].";";
				$result = Db_Query($db_con, $sql);

				$expend_parts_name[$s][$i] = pg_fetch_result($result,0,0);      //商品名
				$expend_parts_cname[$s][$i] = pg_fetch_result($result,0,1);     //略称
				$parts_num = pg_fetch_result($result,0,2);                      //構成品に対する数量
				$expend_parts_num[$s][$i] = $parts_num * $goods_expend_num[$s]; //数量
			}
		}
	}else{
		//消耗品・数量のエラー判定の為、入力値を取得
		$goods_expend_num[$s] = $_POST["form_goods_num3"][$s];    //数量
	}


}

/****************************/
//部品定義
/****************************/
require_once(INCLUDE_DIR."keiyaku.inc");

/****************************/
//契約マスタ登録
/****************************/
//契約区分が代行の場合は、以下のエラーチェックを行う
if($_POST["entry_flg"] == true){

	/****************************/
	//エラーチェック(addRule)
	/****************************/
	if($daiko_div != "1" ){
		//委託先
		$form->addGroupRule('form_daiko', array(
	        'cd1' => array(
	                array($h_mess[1], 'required'),
	        ),      
	        'cd2' => array(
	                array($h_mess[1],'required'),
	        ),       
	        'name' => array(
	                array($h_mess[1],'required'),
	        )       
		));
		//$form->addGroupRule("form_daiko", $h_mess[1],"required");
	
		//代行委託料
		//●必須チェック
		//●半角数字チェック
		//$form->addRule("act_request_price","$h_mess[2]",'required');
		//$form->addRule("act_request_price","$h_mess[3]", "numeric");
	}
	
	//契約区分が通常の場合のみ、以下のエラーチェックを行う
	if($daiko_div == "1"){

		//巡回担当(メイン)
		$form->addRule('form_c_staff_id1',$h_mess[8],'required');
		$form->addRule('form_sale_rate1',$h_mess[12],'required');
	
		//サブ１
		//●担当者と売上率が両方入力されているか
		if($staff2 == NULL && $rate2 != NULL){
			$form->addRule('form_c_staff_id2',$h_mess[9],'required');
		}
		if($staff2 != NULL && $rate2 == NULL && ((int)$rate1 + (int)$rate3 + (int)$rate4) != 100){
			$form->addRule('form_sale_rate2',$h_mess[9],'required');
		}
	
		//サブ２
		//●担当者と売上率が両方入力されているか
		if($staff3 == NULL && $rate3 != NULL){
			$form->addRule('form_c_staff_id3',$h_mess[10],'required');
		}
		if($staff3 != NULL && $rate3 == NULL && ((int)$rate1 + (int)$rate2 + (int)$rate4) != 100){
			$form->addRule('form_sale_rate3',$h_mess[10],'required');
		}
	
		//サブ３
		//●担当者と売上率が両方入力されているか
		if($staff4 == NULL && $rate4 != NULL){
			$form->addRule('form_c_staff_id4',$h_mess[11],'required');
		}
		if($staff4 != NULL && $rate4 == NULL && ((int)$rate1 + (int)$rate2 + (int)$rate3) != 100){
			$form->addRule('form_sale_rate4',$h_mess[11],'required');
		}
	
		//順路
		$form->addGroupRule("form_route_load", $h_mess[18],"required");
		$form->addGroupRule("form_route_load", $h_mess[18],"numeric");
	
	}
	
	//巡回区分エラー判定 
	if($round_div == 1){
		//巡回１
		$form->addRule('form_abcd_week1',$h_mess["19-2"],'required');
		$form->addRule('form_week_rday1',$h_mess["19-4"],'required');
	
	}else if($round_div == 2){
		//巡回２
		$form->addRule('form_rday2',$h_mess["19-3"],'required');
	
	}else if($round_div == 3){
		//巡回３
		$form->addRule('form_cale_week3',$h_mess["19-2"],'required');
		$form->addRule('form_week_rday3',$h_mess["19-4"],'required');
	
	}else if($round_div == 4){
		//巡回４
		$form->addRule('form_cale_week4',$h_mess["19-2"],'required');
	
	}else if($round_div == 5){
		//巡回５
		$form->addRule('form_cale_month5',$h_mess["19-1"],'required');
		$form->addRule('form_week_rday5',$h_mess["19-3"],'required');
	
	}else if($round_div == 6){
		//巡回６
		$form->addRule('form_cale_month6',$h_mess["19-1"],'required');
		$form->addRule('form_cale_week6',$h_mess["19-2"],'required');
		$form->addRule('form_week_rday6',$h_mess["19-4"],'required');
	
	}else if($round_div == 7){
		//巡回７
		//変則日にチェックが付いているか判定
		if($last_day == NULL){
			$form->setElementError("hensoku_err",$h_mess[24]);
		}

		//契約終了日以降に変則日があるとエラー
		if(($contract_eday < $last_day) && ($contract_eday != NULL)){
			$form->setElementError("hensoku_err",$h_mess[59]);
		}
	}

	//■紹介口座料のチェック
	//紹介口座料（固定額）の場合
	if($intro_ac_div == "2" ){
		$form->addRule('intro_ac_price',$h_mess[55],"required");
	}
	$form->addRule('intro_ac_price',$h_mess[55],"regex", '/^[0-9]+$/');
	$form->addRule('intro_ac_price',$h_mess[55],"nonzero");

	//紹介口座料（％）の場合
	if($intro_ac_div == "3" ){
		$form->addRule('intro_ac_rate',$h_mess[56],"required");
	}
	$form->addRule('intro_ac_rate',$h_mess[56],"regex", '/^[0-9]+$/');
	$form->addRule('intro_ac_rate',$h_mess[56],"nonzero");

	if($group_kind == 2){
    //■代行料のチェック
    //代行料区分が「固定額」の場合、代行料（固定額）は必須
    if($act_div == "2" ){
        $form->addRule('act_request_price', $h_mess[60], "required");
    }
    $form->addRule('act_request_price', $h_mess[60], "regex", '/^[0-9]+$/');

    //代行料区分が「％」の場合、代行料（％）は必須
    if($act_div == "3" ){
        $form->addRule('act_request_rate', $h_mess[61], "required");
    }
    $form->addRule('act_request_rate', $h_mess[61], "regex", '/^[0-9]+$/');
    $form->registerRule("check_percent", "function", "Check_Percent_Qf");
    $form->addRule("act_request_rate", $h_mess[61], "check_percent");

    //通常巡回の場合、代行料は「発生しない」のみ
    if($daiko_div == "1" && $act_div != "1"){
        $form->setElementError("daiko_check", $h_mess[62]);
    }

    //代行の場合、代行料は「発生する」のみ
    if($daiko_div != "1" && $act_div == "1"){
        $form->setElementError("daiko_check", $h_mess[67]);
    }

    //オンライン代行の場合、代行料は「売上率」のみ
    if($daiko_div == "2" && $act_div != "3"){
        $form->setElementError("daiko_check", $h_mess[63]);
    }
	}

	$input_goods_flg = false;   //商品入力判定フラグ
	for($n=1;$n<=5;$n++){
		
		//入力行判定
	if($divide[$n] != NULL || $slip_flg[$n] == 'true' || $serv_id[$n] != NULL || $goods_item_flg[$n] == 'true' || $goods_item_id[$n] != NULL || $goods_item_num[$n] != NULL || $set_flg[$n] == 'true' || $t_price1[$n] != NULL || $t_price2[$n] != NULL || $s_price1[$n] != NULL || $s_price2[$n] != NULL || $trade_amount[$n] != NULL || $sale_amount[$n] != NULL || $goods_body_id[$n] != NULL || $goods_expend_id[$n] || $goods_body_num[$n] != NULL || $goods_expend_num[$n] != NULL || $aprice_div[$n] == 2 || $aprice_div[$n] == 3){
			//販売区分
			//●必須チェック
			$form->addRule("form_divide[$n]",$d_mess[0][$n],'required');
	
			//サービス・アイテム
			//●必須チェック
			if($serv_id[$n] == NULL && $goods_item_id[$n] == NULL){
				//両方入力していない場合エラー
				$form->addRule("form_serv[$n]",$d_mess[1][$n],'required');
			}else if($serv_id[$n] == NULL && $set_flg[$n] == 'true'){
				//一式にチェックがあり、サービスが選択されていない場合エラー
				$form->addRule("form_serv[$n]",$d_mess[2][$n],'required');
			}
	
			//数量・一式
			//●必須チェック
			if($goods_item_num[$n] == NULL && $set_flg[$n] == 'false' && $serv_id[$n] != NULL){
				//サービスが選択されている時に、両方入力していない場合エラー
				$form->addRule("form_goods_num1[$n]",$d_mess[3][$n],'required');
			}
	
			//営業原価・売上単価(必須+半角英数）
			$form->addGroupRule("form_trade_price[$n]", $d_mess[5][$n],"required");
			$form->addGroupRule("form_trade_price[$n]", $d_mess[5][$n],"numeric");
			$form->addGroupRule("form_sale_price[$n]", $d_mess[7][$n],"required");
			$form->addGroupRule("form_sale_price[$n]", $d_mess[7][$n],"numeric");
		
			//口座区分判定
			if($aprice_div[$n] == 2){
				//固定金額
				$form->addRule("form_account_price[$n]",$d_mess[13][$n],'required');
			}else if($aprice_div[$n] == 3){
				//率
				$form->addRule("form_account_rate[$n]",$d_mess[13][$n],'required');
			}
	
			//商品入力判定フラグ
			$input_goods_flg = true;
		}
	}
	
	$form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
	$form->registerRule("check_date","function","Check_Date");
	
	//新規or複写の場合は巡回基準日をチェック
	if($init_type == "新規" || $init_type == "複写"){
		$form->addGroupRule("form_stand_day", $h_mess[20],  "required");      //巡回基準日
		$form->addRule("form_stand_day", $h_mess[20],  "check_date",$_POST["form_stand_day"]);         //巡回基準日
	
	//変更の場合は修正有効日をチェック
	} else {
		$form->addGroupRule("form_update_day", $h_mess[52],  "required");       //修正有効日
		$form->addRule("form_update_day", $h_mess[52],  "check_date",$_POST["form_update_day"]);       //修正有効日
	
	}
	$form->addRule("form_contract_day",  $h_mess[53],  "check_date",$_POST["form_contract_day"]);   //契約日
	$form->addRule("form_contract_eday", $h_mess[53],  "check_date",$_POST["form_contract_eday"]); //契約終了日
	
	
	//●必須チェック
	//●半角数字チェック
	for($n=1;$n<=5;$n++){
		//アイテム・数量
		if($goods_item_id[$n] != NULL){
			$form->addRule("form_goods_num1[$n]",$d_mess[8][$n],'required'); 
		}
		//消耗品・数量
		if($goods_expend_id[$n] != NULL){
			$form->addRule("form_goods_num3[$n]",$d_mess[9][$n],'required');
		}
		//本体・数量
		if($goods_body_id[$n] != NULL){
			$form->addRule("form_goods_num2[$n]",$d_mess[10][$n],'required');
		}
	}
	
	//備考（文字数）
	$form->addRule("form_note",$h_mess[23],"mb_maxlength","100");
	
	//委託先宛備考（文字数）
	$form->addRule("form_daiko_note",$h_mess[0],"mb_maxlength","100");
	
	//取引区分が無い場合エラー表示（後でこの処理は削除）
	if($trade_id == NULL){
		$trade_error_flg = true;
	}

	/****************************/
	//エラーチェック(PHP)
	/****************************/

	//巡回区分が２か５の場合、指定日の必須判定
	if($rday == 0 && ($round_div == 2 || $round_div == 5)){
		$form->setElementError("form_rday2",$h_mess[19]);
	}

	//顧客先不正判定
	if($client_id == NULL){
		//不正
		$form->setElementError("form_client",$h_mess[34]);

		//該当フォーム初期化
		$con_data2["form_client"]["cd1"] = "";
		$con_data2["form_client"]["cd2"] = "";
		$con_data2["form_client"]["name"] = "";
		$con_data2["client_search_flg"]   = "";
	}else{
		//正常値は、行のエラー判定を実行
		
		//◇行No.
		//数値判定
		if(ereg("^[0-9]+$",$line) && $line > 0){
			//数値(0を除く)

			//・重複チェック
		    //入力したコードがマスタに存在するかチェック
		    $sql  = "SELECT ";
		    $sql .= "    line ";
		    $sql .= "FROM ";
		    $sql .= "    t_contract ";
		    $sql .= "WHERE ";
		    $sql .= "    line = $line ";
		    $sql .= "AND ";
		    $sql .= "    client_id = $client_id ";
		    //変更の場合は、自分のデータ以外を参照する
		    if($flg == "chg"){
		        $sql .= " AND NOT ";
		        $sql .= "contract_id = $get_con_id";
		    }
	        $sql .= ";";
	        $result = Db_Query($db_con, $sql);
	        $row_count = pg_num_rows($result);
	        if($row_count != 0){
	            $form->setElementError("form_line",$h_mess[7]);
	        }
	    }else{
			//NULL判定
			if($line == NULL){
				//NULL
				$form->setElementError("form_line",$h_mess[5]);
			}else{
				//数値以外or０行
				$form->setElementError("form_line",$h_mess[6]);
			}
		}
	}

	//通常契約
	if($daiko_div == "1"){

		//◇巡回担当者
		//売上率の合計が100%か判定
		//入力された売上率の合計が100％か判定
		if(($rate1 + (int)$rate2 + (int)$rate3 + (int)$rate4) != 100){
			$form->setElementError("sale_rate_sum",$h_mess[16]);
		}

		//巡回担当者の重複チェック
		$cnum1 = count($staff_check);
		$staff_check2 = array_unique($staff_check);
		$cnum2 = count($staff_check2);
		//要素数判定
		if($cnum1 != $cnum2){
			//要素数が違う場合、重複値が存在した
			$form->setElementError("staff_uniq",$h_mess[17]);
		}

		//売上率が０以上の数値か判定
		for($s=0;$s<5;$s++){
			$ecount = 12 + $s;
			//入力値がある場合に数値判定
			if(!ereg("^[0-9]+$",$staff_rate[$s]) && $staff_rate[$s] != NULL){
				$form->setElementError("form_sale_rate".($s+1),$h_mess[$ecount]);
			}
		}

		//順路
		//●文字数チェック
		//●数値チェック
		if(!ereg("^[0-9]{4}$",$route)){
			$form->setElementError("form_route_load","$h_mess[18]");
		}

	//代行の場合
	}else{
		//代行先不正判定
		if($daiko_id == NULL){
			$form->setElementError("form_daiko",$h_mess[33]);

			//該当フォーム初期化
			$con_data2["form_daiko"]["cd1"] = "";
			$con_data2["form_daiko"]["cd2"] = "";
			$con_data2["form_daiko"]["name"] = "";
			$con_data2["daiko_search_flg"]   = "";
		}
	}

	//商品行全てが未入力の場合、エラー
	if($input_goods_flg == false){
		$form->setElementError("goods_enter",$h_mess[21]);
	}

	for($n=1;$n<=5;$n++){
		//入力行判定
		if($divide[$n] != NULL || $slip_flg[$n] == 'true' || $serv_id[$n] != NULL ||$goods_item_flg[$n] == 'true' || $goods_item_id[$n] != NULL || $goods_item_num[$n] != NULL || $set_flg[$n] == 'true' || $t_price1[$n] != NULL || $t_price2[$n] != NULL || $s_price1[$n] != NULL || $s_price2[$n] != NULL || $trade_amount[$n] != NULL || $sale_amount[$n] != NULL || $goods_body_id[$n] != NULL || $goods_expend_id[$n] || $goods_body_num[$n] != NULL || $goods_expend_num[$n] != NULL || $aprice_div[$n] == 2 || $aprice_div[$n] == 3 ){
		
			//◇サービス印字・一式フラグ
			//妥当入力チェック
			//サービスIDがあり印字しない場合に、一式フラグが付いているか判定
			if($serv_id[$n] != NULL && $slip_flg[$n] == 'false' && $set_flg[$n] == 'true'){
				$form->setElementError("form_print_flg1[$n]",$d_mess[15][$n]);
			}
	
			//◇サービス印字・アイテム印字
			//妥当入力チェック	
			//サービスID or アイテムIDがある場合、サービス・アイテムのいずれかが印字してあるか判定
			if(($serv_id[$n] != NULL || $goods_item_id[$n] != NULL) && $slip_flg[$n] == 'false' && $goods_item_flg[$n] == 'false'){
				$form->setElementError("form_print_flg2[$n]",$d_mess[17][$n]);
			}
			//サービス印字がある場合、サービスIDがあるか判定
			if($serv_id[$n] == NULL && $slip_flg[$n] == 'true'){
				$form->setElementError("form_serv[$n]",$d_mess[2][$n]);
			}
			
			//◇アイテム印字
			//妥当入力チェック
			//アイテム印字がある場合、アイテムIDがあるか判定
			if($goods_item_id[$n] == NULL && $goods_item_flg[$n] == 'true'){
				$form->setElementError("form_goods_cd1[$n]",$d_mess[16][$n]);
			}

			//商品が入力された場合
			if($goods_item_id[$n] != NULL){
				//アイテム（正式,略称）が入力されているかチェックする
				$form->addRule("official_goods_name[$n]",$d_mess[27][$n],'required'); 
				$form->addRule("form_goods_name1[$n]",$d_mess[28][$n],'required'); 
			}

			//◇口座料(固定金額)
			//金額妥当性チェック
			if($aprice_div[$n] == 2){
				//売上金額より口座料が多い場合エラー
				if($sale_amount[$n] < $ac_price[$n]){
					$form->setElementError("form_account_price[$n]",$d_mess[31][$n]);
				}
				//口座料が小数か判定
				$count_price = strpos($ac_price[$n],'.');
				if($count_price != 0){
					$form->setElementError("form_account_price[$n]",$d_mess[13][$n]);
				}
				//口座料がマイナスか判定
				if(!ereg("^[0-9]+$",$ac_price[$n])){
					$form->setElementError("form_account_price[$n]",$d_mess[13][$n]);
				}
				//口座料が０以下か判定
				if($ac_price[$n] <= 0){
					$form->setElementError("form_account_price[$n]",$d_mess[13][$n]);
				}
			}
			//◇口座料(％指定)
			//金額妥当性チェック
			if($aprice_div[$n] == 3){
				//売上金額より口座料が多い場合エラー
				if(100 < $ac_rate[$n]){
					$form->setElementError("form_account_rate[$n]",$d_mess[14][$n]);
				}
				//口座料が小数か判定
				$count_rate = strpos($ac_rate[$n],'.');
				if($count_rate != 0){
					$form->setElementError("form_account_rate[$n]",$d_mess[13][$n]);
				}
				//口座料がマイナスか判定
				if(!ereg("^[0-9]+$",$ac_rate[$n])){
					$form->setElementError("form_account_rate[$n]",$d_mess[13][$n]);
				}
				//口座料が０以下か判定
				if($ac_rate[$n] <= 0){
					$form->setElementError("form_account_rate[$n]",$d_mess[13][$n]);
				}
			}

			//■前受金
			//前受け相殺ありを選択した場合は前受金の入力が必須
			if($advance_flg[$n] == 2){
				$form->addRule("form_ad_offset_amount[$n]",$d_mess[35][$n],'required'); 
			}
			$form->addRule("form_ad_offset_amount[$n]",$d_mess[35][$n],"regex", '/^[0-9]+$/');

            //aoyama-n 2009-09-09
            /**************
			//◇営業原価
			//・数値判定
			if(!ereg("^[0-9]+\.[0-9]+$",$trade_price[$n])){
				$form->setElementError("form_trade_price[$n]",$d_mess[5][$n]);
			}
			//◇売上単価
			//・数値判定
			if(!ereg("^[0-9]+\.[0-9]+$",$sale_price[$n])){
				$form->setElementError("form_sale_price[$n]",$d_mess[7][$n]);
			}
            **************/
            //値引商品
            if($_POST["hdn_discount_flg"][$n] === 't'){
			    //◇営業原価
			    //・数値判定
			    if(!ereg("^[-0-9]+\.[0-9]+$",$trade_price[$n])){
				    $form->setElementError("form_trade_price[$n]",$d_mess[36][$n]);
			    }elseif($trade_price[$n] > 0){
                    $form->setElementError("form_trade_price[$n]",$d_mess[36][$n]);
                }
			    //◇売上単価
			    //・数値判定
			    if(!ereg("^[-0-9]+\.[0-9]+$",$sale_price[$n])){
				    $form->setElementError("form_sale_price[$n]",$d_mess[37][$n]);
			    }elseif($sale_price[$n] > 0){
                    $form->setElementError("form_sale_price[$n]",$d_mess[37][$n]);
                }
            //値引商品以外
            }else{
			    //◇営業原価
			    //・数値判定
			    if(!ereg("^[0-9]+\.[0-9]+$",$trade_price[$n])){
				    $form->setElementError("form_trade_price[$n]",$d_mess[5][$n]);
			    }
			    //◇売上単価
			    //・数値判定
			    if(!ereg("^[0-9]+\.[0-9]+$",$sale_price[$n])){
				    $form->setElementError("form_sale_price[$n]",$d_mess[7][$n]);
			    }
            }


			//消耗品名のチェック
			if($goods_expend_id[$n] != NULL){
				$form->addRule("form_goods_name3[$n]",$d_mess[29][$n],'required'); 
			}

			//本体名のチェック
			if($goods_body_id[$n] != NULL){
				$form->addRule("form_goods_name2[$n]",$d_mess[30][$n],'required'); 
			}

			//◇アイテム数量
			//・数値判定
			if(($serv_id[$n] != NULL || $goods_item_id[$n] != NULL) && !ereg("^[0-9]+$",$goods_item_num[$n]) && $goods_item_num[$n] != NULL){
				$form->setElementError("form_goods_num1[$n]",$d_mess[8][$n]);
			}
			//◇本体数量
			//・必須判定
			if($goods_body_num[$n] != NULL && $goods_body_id[$n] == NULL){
				$form->setElementError("error_goods_num2[$n]",$d_mess[12][$n]);
			}
			//・数値判定
			if($goods_body_id[$n] != NULL && !ereg("^[0-9]+$",$goods_body_num[$n])){
				$form->setElementError("form_goods_num2[$n]",$d_mess[10][$n]);
			}
			//◇消耗品数量
			//・必須判定
			if($goods_expend_num[$n] != NULL && $goods_expend_id[$n] == NULL){
				$form->setElementError("error_goods_num3[$n]",$d_mess[11][$n]);
			}
			//・数値判定
			if($goods_expend_id[$n] != NULL && !ereg("^[0-9]+$",$goods_expend_num[$n])){
				$form->setElementError("form_goods_num3[$n]",$d_mess[9][$n]);
			}

			/****************************/
			//不正値判定関数
			/****************************/
			//該当アイテムが無かった場合
			if($goods_item_id[$n] != NULL){
				//契約区分判定
				if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
					//代行は本部商品で正常値か判定
					$injust = Injustice_check($db_con,"h_goods",$goods_item_id[$n],$goods_item_cd[$n]);
				}else{
					//自社
					$injust = Injustice_check($db_con,"goods",$goods_item_id[$n],$goods_item_cd[$n]);
				}
				if($injust == false){

					//契約区分判定
					if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
						//代行
						$form->setElementError("form_goods_cd1[$n]",$d_mess[21][$n]);
					}else{
						//自社
						$form->setElementError("form_goods_cd1[$n]",$d_mess[18][$n]);
					}
					//該当フォーム初期化
					$con_data2["hdn_goods_id1"][$n]         = "";
					$con_data2["hdn_name_change1"][$n]      = "";
					$con_data2["form_goods_cd1"][$n]        = "";
					$con_data2["form_goods_name1"][$n]      = "";
					$con_data2["official_goods_name"][$n]   = "";
					$con_data2["form_trade_price"][$n]["1"] = "";
					$con_data2["form_trade_price"][$n]["2"] = "";
					$con_data2["form_trade_amount"][$n]     = "";
					$con_data2["form_sale_price"][$n]["1"]  = "";
					$con_data2["form_sale_price"][$n]["2"]  = "";
					$con_data2["form_sale_amount"][$n]      = "";
				}
			}
			//本体商品不正判定
			if($goods_body_id[$n] != NULL){
				//契約区分判定
				if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
					//代行は本部商品で正常値か判定
					$injust = Injustice_check($db_con,"h_goods",$goods_body_id[$n],$goods_body_cd[$n]);
				}else{
					//自社
					$injust = Injustice_check($db_con,"goods",$goods_body_id[$n],$goods_body_cd[$n]);
				}
				if($injust == false){

					//契約区分判定
					if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
						//代行
						$form->setElementError("form_goods_cd2[$n]",$d_mess[23][$n]);
					}else{
						//自社
						$form->setElementError("form_goods_cd2[$n]",$d_mess[20][$n]);
					}

					//該当フォーム初期化
					$con_data2["hdn_goods_id2"][$n]         = "";
					$con_data2["hdn_name_change2"][$n]      = "";
					$con_data2["form_goods_cd2"][$n]        = "";
					$con_data2["form_goods_name2"][$n]      = "";
				}
			}
			//消耗品不正判定
			if($goods_expend_id[$n] != NULL){
				//契約区分判定
				if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
					//代行は本部商品で正常値か判定
					$injust = Injustice_check($db_con,"h_goods",$goods_expend_id[$n],$goods_expend_cd[$n]);
				}else{
					//自社
					$injust = Injustice_check($db_con,"goods",$goods_expend_id[$n],$goods_expend_cd[$n]);
				}
				if($injust == false){
					//契約区分判定
					if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
						//代行
						$form->setElementError("form_goods_cd3[$n]",$d_mess[22][$n]);
					}else{
						//自社
						$form->setElementError("form_goods_cd3[$n]",$d_mess[19][$n]);
					}

					//該当フォーム初期化
					$con_data2["hdn_goods_id3"][$n]         = "";
					$con_data2["hdn_name_change3"][$n]      = "";
					$con_data2["form_goods_cd3"][$n]        = "";
					$con_data2["form_goods_name3"][$n]      = "";
				}
			}
			$trade_amount_sum = $trade_amount_sum + $trade_amount[$n];

		}
	}


	//代行料区分が「固定額」　かつ　固定代行料と営業金額合計が一致しない場合
	if($act_div == "2" && ($trade_amount_sum != $act_request_price)){
		$form->setElementError("act_request_price",$h_mess[68]);
	}

	$con_data2["entry_flg"] = "";    //登録ボタン押下フラグ初期化

	//契約発効日が本日より前はエラー
	if ($init_type == "変更" &&  ($update_day < $g_today)  ){
		$form->setElementError("form_update_day",$h_mess[64]);
	}

	//修正発効日が本日より前はエラー
	if(($init_type == "新規" || $init_type == "複写") && $stand_day < $g_today){
		$form->setElementError("form_stand_day",$h_mess[65]);
	}

	//契約終了日が本日より前はエラー
	if(($contract_eday < $g_today) && ($contract_eday != NULL)){
		$form->setElementError("form_contract_eday",$h_mess[66]);
	}

	//$validate_result = $form->validate();

	//変更の場合は「修正発効日」と「巡回区分」の整合性をチェックする
	if ($init_type == "変更" && ($form->getElementError("form_update_day") == NULL)){
		$error_mesg = Round_Check($db_con,$update_ymd[0],$update_ymd[1],$update_ymd[2],$round_div,$abcd_week,$cale_week,$week_rday,$rday);

		if ($error_mesg != NULL){ 
			$form->setElementError("form_update_day","修正発行日は".$error_mesg);
		}

	//新規か複写の場合は「契約発効日」と「巡回区分」の整合性をチェックする
	}elseif(($init_type == "新規" || $init_type == "複写") && ($form->getElementError("form_stand_day") == NULL)){
		$error_mesg = Round_Check($db_con,$stand_ymd[0],$stand_ymd[1],$stand_ymd[2],$round_div,$abcd_week,$cale_week,$week_rday,$rday);

		if ($error_mesg != NULL){ 
			$form->setElementError("form_stand_day","契約発効日は".$error_mesg);
		}
		
	}

	//****************************/
	//登録・変更処理開始
	//****************************/
	//if($form->validate() && $error_flg == false){
	//if($validate_result && $error_flg == false){
	if( $form->validate() ){
		Db_Query($db_con, "BEGIN");

		//本日の日付取得
		$con_today = date("Y-m-d");

		//■追加開始
		//通常契約の場合
		if($daiko_div == "1"){
			//代行に関する項目はNULL
			$daiko_id    = NULL;
			$con_today   = NULL;
			$daiko_note  = NULL;
		}

		if($daiko_div == "2"){
			//オンライン代行の場合、依頼中にする
			$request_state = 1;
		}else{
			//通常・オフライン代行の場合、委託済
			$request_state = 2;
		}

		$update_columns = array(
			line               => $line,
			//route              => $route,
			round_div          => $round_div,
			cycle              => $cycle,
			cycle_unit         => $cycle_unit,
			cale_week          => $cale_week,
			abcd_week          => $abcd_week,
			rday               => $rday,
			week_rday          => $week_rday,
			stand_day          => $stand_day,
			contract_day       => $contract_day,
			note               => stripslashes($note),
			last_day           => $last_day,
			contract_div       => $daiko_div,
			act_request_day    => $con_today,
			trust_id           => $daiko_id,
			act_div            => $act_div,
			act_request_price  => $act_request_price,
			act_request_rate   => $act_request_rate,
			trust_ahead_note   => $daiko_note,
			request_state      => $request_state,
			intro_ac_div       => $intro_ac_div,
			intro_ac_price     => $intro_ac_price,
			intro_ac_rate      => $intro_ac_rate,
			claim_id           => $claim_id,
			update_day         => $update_day,
			contract_eday      => $contract_eday,
			claim_div          => $claim_div,
			state              => $state,
			trust_trade_amount => NULL,
			trust_sale_amount  => NULL,
		);
        //オンライン代行では順路を残す
        if($daiko_div != "2"){
            $update_columns["route"] = $route;
        }

		//■追加終了


/****************************/
//変更処理
/****************************/
		if($flg == "chg"){

			$work_div = 2;
			$contract_id = $get_con_id;

			/*
			 * 履歴：
			 * 　日付　　　　B票No.　　　　担当者　　　内容　
			 * 　2006/10/27　01-007　　　　suzuki-t　　変更時に代行区分をオンライン代行にしたときに、変更前の受注伝票を削除
			 *
			*/
			//変更前の伝票の代行先ID取得
			$sql  = "SELECT ";
			$sql .= "    trust_id ";
			$sql .= "FROM ";
			$sql .= "    t_contract ";
			$sql .= "WHERE ";
			$sql .= "    contract_id = $contract_id;";
			$result = Db_Query($db_con, $sql); 
			$trust_list = Get_Data($result,3);
			$trust_id = $trust_list[0][0];

			//SQLインジェクション対策
			$contract_columns = pg_convert($db_con,'t_contract',$update_columns);
	
			//UPDATE条件
			$where[contract_id] = $contract_id;
			$where              = pg_convert($db_con,'t_contract',$where);
	
			//契約変更
			//print_array($contract_columns);
			$return = Db_Update($db_con, t_contract, $contract_columns, $where);
	
			/*
			$sql  = "UPDATE t_contract SET ";
			$sql .= "    line = $line,";
			//順路指定判定
			if($route != NULL){
				$sql .= "    route = $route,";
			}else{
				$sql .= "    route = NULL,";
			}
			$sql .= "    round_div = '$round_div',";
			$sql .= "    cycle = '$cycle',";
			$sql .= "    cycle_unit = '$cycle_unit',";
			$sql .= "    cale_week = '$cale_week',";
			$sql .= "    abcd_week = '$abcd_week',";
			$sql .= "    rday = '$rday',";
			$sql .= "    week_rday = '$week_rday',";
			//基準日が設定されているか
			if($stand_day != NULL){
				$sql .= "    stand_day = '$stand_day',";
			}
			//契約日が設定されているか
			if($contract_day != NULL){
				$sql .= "    contract_day = '$contract_day',";
			}else{
				$sql .= "    contract_day = NULL,";
			}
			$sql .= "    note = '$note',";
			//最終巡回日が設定されているか
			if($last_day != NULL){
				$sql .= "    last_day = '$last_day',";
			}else{
				$sql .= "    last_day = NULL,";
			}
			$sql .= "    contract_div = '$daiko_div',";
			//契約区分判定
			if($daiko_div != "1"){
				//代行
				$sql .= "    act_request_day = '$con_today',";
				$sql .= "    trust_id = $daiko_id,";
				$sql .= "    act_request_rate = '$daiko_price',";
				$sql .= "    trust_ahead_note = '$daiko_note',";
			}else{
				//通常の場合はNULLを代入
				$sql .= "    act_request_day = NULL,";
				$sql .= "    trust_id = NULL,";
				$sql .= "    act_request_rate = NULL,";
				$sql .= "    trust_ahead_note = NULL,";
			}
			//契約区分判定
			if($daiko_div == "2"){
				//オンライン代行の場合、依頼中にする
				$sql .= "    request_state = '1',";
			}else{
				//通常・オフライン代行の場合、委託済
				$sql .= "    request_state = '2',";
			}
			//$sql .= "    intro_ac_price = $intro_ac_price,";   //口座単価(得意先)
			//$sql .= "    intro_ac_rate = '$intro_ac_rate',";   //口座率(得意先)
			$sql .= "    claim_id = $claim_id,";                //請求先ID

			if($update_day != NULL){
				$sql .= "    update_day = '$update_day', ";        //修正有効日
			}else{
				$sql .= "    update_day = NULL, ";                 //修正有効日
			}
			if($contract_eday != NULL){
				$sql .= "    contract_eday = '$contract_eday', ";  //契約終了日
			}else{
				$sql .= "    contract_eday = NULL, ";              //契約終了日
			}
			$sql .= "    claim_div = $claim_div, ";             //請求先区分
			$sql .= "    intro_ac_div = '$intro_ac_div', ";             //請求先区分
			$sql .= "    state = '$state' ";             //契約状態区分
			
			$sql .= " WHERE ";
			$sql .= "    contract_id = $contract_id;";
			*/

/****************************/
//登録・複写追加処理
/****************************/
		}else{
			$work_div = 1;
			//$contract_id = Get_Pkey();

        //契約情報ID取得
        $sql  = "SELECT COALESCE(MAX(contract_id),0)+1 FROM t_contract ";
        $result = Db_Query($db_con, $sql);
        $contract_id = pg_fetch_result($result,0,0);

			$insert_columns = array(
				contract_id      => $contract_id,
				client_id        => $client_id,
				shop_id          => $client_h_id,
			);

			$contract_columns = array_merge($insert_columns,$update_columns);

			//SQLインジェクション対策
			$contract_columns = pg_convert($db_con,'t_contract',$contract_columns);

			//契約登録
			//print_array($contract_columns);
			$return = Db_Insert($db_con, t_contract, $contract_columns);

/*
			$sql  = "INSERT INTO t_contract( ";
			$sql .= "    contract_id,";
			$sql .= "    client_id,";
			$sql .= "    line,";
			$sql .= "    route,";
			$sql .= "    round_div,";
			$sql .= "    cycle,";
			$sql .= "    cycle_unit,";
			$sql .= "    cale_week,";
			$sql .= "    abcd_week,";
			$sql .= "    rday,";
			$sql .= "    week_rday,";
			$sql .= "    stand_day,";
			$sql .= "    contract_day,";
			$sql .= "    note,";
			$sql .= "    last_day,";
			$sql .= "    shop_id,";
			$sql .= "    contract_div,";
			//代行の場合のみ更新
			if($daiko_div != "1"){
				$sql .= "    act_request_day,";
				$sql .= "    trust_id,";
				$sql .= "    act_request_rate,";
				$sql .= "    trust_ahead_note,";
			}
			$sql .= "    request_state,";
			$sql .= "    intro_ac_price,";
			$sql .= "    intro_ac_rate,";
			$sql .= "    claim_id,";
			$sql .= "    update_day, ";
			$sql .= "    contract_eday, ";
			$sql .= "    claim_div, ";
			$sql .= "    intro_ac_div, ";
			$sql .= "    state ";
			$sql .= "    )VALUES(";
			$sql .= "    $contract_id,";
			$sql .= "    $client_id,";
			$sql .= "    $line,";
			//順路指定判定
			if($route != NULL){
				$sql .= "    $route,";
			}else{
				$sql .= "    NULL,";
			}
			$sql .= "    '$round_div',";
			$sql .= "    '$cycle',";
			$sql .= "    '$cycle_unit',";
			$sql .= "    '$cale_week',";
			$sql .= "    '$abcd_week',";
			$sql .= "    '$rday',";
			$sql .= "    '$week_rday',";
			//基準日が設定されているか
			if($stand_day != NULL){
				$sql .= "    '$stand_day',";
			}else{
				$sql .= "    NULL,";
			}
			//契約日が設定されているか
			if($contract_day != NULL){
				$sql .= "    '$contract_day',";
			}else{
				$sql .= "    NULL,";
			}
			$sql .= "    '$note',";
			//最終巡回日が設定されているか
			if($last_day != NULL){
				$sql .= "    '$last_day',";
			}else{
				$sql .= "    NULL,";
			}
			$sql .= "    $client_h_id,";
			$sql .= "    '$daiko_div',";
			//代行の場合のみ更新
			if($daiko_div != "1"){
				$sql .= "    '$con_today',";
				$sql .= "    $daiko_id,";
				$sql .= "    '$daiko_price',";
				$sql .= "    '$daiko_note',";
			}
			//契約区分判定
			if($daiko_div == "2"){
				//オンライン代行の場合、依頼中にする
				$sql .= "    '1',";
			}else{
				//通常・オフライン代行の場合、委託済
				$sql .= "    '2',";
			}
			$sql .= "    $intro_ac_price,";
			$sql .= "    '$intro_ac_rate',";
			$sql .= "    $claim_id,";

			if($update_day != NULL){
				$sql .= "    '$update_day', ";        //修正有効日
			}else{
				$sql .= "    NULL, ";                 //修正有効日
			}
			if($contract_eday != NULL){
				$sql .= "    '$contract_eday', ";  //契約終了日
			}else{
				$sql .= "    NULL, ";              //契約終了日
			}
			
			$sql .= "    '$claim_div', ";	
			$sql .= "    '$intro_ac_div', ";	
			$sql .= "    '$state' ";	

			$sql .= "    );";
	*/

		}

		//$result = Db_Query($db_con, $sql);
		if($result === false){
	        Db_Query($db_con, "ROLLBACK");
	        exit;
	    }

        //契約情報ID取得
        $sql  = "SELECT ";
        $sql .= "    contract_id ";
        $sql .= "FROM ";
        $sql .= "    t_contract ";
        $sql .= "WHERE ";
        $sql .= "    client_id = $client_id ";
        $sql .= "AND ";
        $sql .= "    line = $line;";
        $result = Db_Query($db_con, $sql);
        $contract_id = pg_fetch_result($result,0,0);


		/****************************/
		//「データ作成開始日」「データ作成終了日」の決定　⇒　この部分は関数化
		/****************************/
		/*
		$cal_array = Cal_range($db_con,$client_h_id);

		$start_day   = $cal_array[0];    //カレンダー開始日（伝票作成開始日）（本日の日付）
		$cal_end_day = $cal_array[1];    //カレンダー終了日
		$cal_peri    = $cal_array[2];    //カレンダー表示期間
		$end_day     = $cal_end_day;     //伝票作成終了日

    //■予定データ作成開始日の決定
    //「新規」か「複写」の場合は「契約発効日」を考慮する
    if($init_type == "新規" || $init_type == "複写"){

			//基準日がカレンダー開始日より後の場合、基準日から伝票を作成する
			if ( ($start_day < $stand_day) && ($stand_day != NULL) ){
				$start_day = $stand_day;
			}

    //変更の場合
    }else{
			$stand_day = $update_day;  //「修正発効日」を基準とする

			//修正発効日がカレンダー開始日より後の場合、修正発効日から伝票を発行する。
			if ( ($start_day < $update_day) && ($update_day != NULL) ){
				$start_day = $update_day;
			}

    }

		//「契約終了日がカレンダー終了日より前」　かつ　「NULLでない」　の場合は、契約終了日をカレンダー終了日とする。
		if ( ($contract_eday < $end_day) && ($contract_eday != NULL) ){
			$end_day = $contract_eday;
		}

		//状態が「解約・取引中」の場合
		if ( $state == "2" ){
			$end_day = date("Y-m-d",mktime(0,0,0,date(m),date(d)-1,date(Y))); //前日を終了日とする
		}
		*/

		//伝票作成期間を取得
		$cal_date = Cal_Span($db_con,$client_h_id,$state,$init_type,$stand_day,$update_day,$contract_eday);
		$stand_day   = $cal_date[0];    //契約発効日（基準日）		
		$start_day   = $cal_date[1];    //伝票作成開始日
		$end_day     = $cal_date[2];    //伝票作成終了日

		//変則日以外は、巡回日を算出
		if($round_div != 7){
			$date_array = NULL;
			$date_array = Round_day($db_con,$cycle,$cale_week,$abcd_week,$rday,$week_rday,$stand_day,$round_div,$start_day,$end_day);
		}

		//****************************
		//予定伝票の重複作成チェック
		//****************************
		if(!empty($date_array)){

			//巡回日ごとに重複をチェックする
			foreach($date_array AS $key => $date){
				$duplicat = Get_Aord_Duplicat($db_con,$contract_id,$date); 

				//重複がない場合
				if ($duplicat === false) {

				//重複がある場合
				} else {
					$duplicat_mesg .= $duplicat;
					$commit_flg = false; //コミットしない
				}

			}
		}
			                                                                 

		/****************************/
		//巡回日テーブル登録（一度削除した後で登録）
		/****************************/
		Delete_ConRound($db_con,$contract_id); 

		//変則日の場合は、巡回日テーブル登録後に変則最終日をUPDATE
		if($round_div == 7){
			//巡回日テーブル登録
			Regist_ConRound($db_con,$contract_id,$date_array,NULL);

			//巡回最終日を取得
			$last_day = Get_Max_ConRound($db_con,$contract_id);

			$update_columns           = NULL; //初期化
			$update_columns[last_day] = $last_day;
	
			//SQLインジェクション対策
			$update_columns = pg_convert($db_con,'t_contract',$update_columns);	
				
			//UPDATE条件
			$where[contract_id] = $contract_id;
			$where              = pg_convert($db_con,'t_contract',$where);

			//変則最終日をUPDATE
			Db_Update($db_con, t_contract, $update_columns, $where);

		//変則日以外の場合
		}else{
			//巡回日テーブル登録
			Regist_ConRound($db_con,$contract_id,$date_array,$end_day);
		}



		/****************************/
		//巡回担当者テーブル登録（一度削除した後で登録）
		/****************************/
        //オンライン代行では削除しない
        if($daiko_div != "2"){
    		Delete_ConStaff($db_con,$contract_id);
        }

		//通常契約は巡回担当者を登録する
		if($daiko_div == "1"){
			Regist_ConStaff($db_con,$contract_id,$staff_check,$staff_rate);
		}

		/****************************/
		//契約内容登録（一度削除した後で登録）
		/****************************/
		Delete_ConInfo($db_con,$contract_id);

		for($s=1;$s<=5;$s++){
			//アイテムorサービス入力判定
			if($goods_item_id[$s] != NULL || $serv_id[$s] != NULL){

				$sql  = "INSERT INTO t_con_info( ";
				$sql .= "    con_info_id,";
				$sql .= "    contract_id,";
				$sql .= "    line,";
				$sql .= "    divide,";
				$sql .= "    serv_pflg,";
				$sql .= "    serv_id,";
				$sql .= "    goods_pflg,";
				$sql .= "    goods_id,";
				$sql .= "    goods_name,";
				$sql .= "    num,";
				$sql .= "    set_flg,";
				$sql .= "    trade_price,";
				$sql .= "    trade_amount,";
				$sql .= "    sale_price,";
				$sql .= "    sale_amount,";
				$sql .= "    rgoods_id,";
				$sql .= "    rgoods_name,";
				$sql .= "    rgoods_num,";
				$sql .= "    egoods_id,";
				$sql .= "    egoods_name,";
				$sql .= "    egoods_num,";
				$sql .= "    account_price,";
				$sql .= "    account_rate, ";
				$sql .= "    mst_sync_flg, ";
				$sql .= "    official_goods_name, ";
				$sql .= "    advance_flg, ";
				$sql .= "    advance_offset_amount ";
				$sql .= "    )VALUES(";
				$sql .= "    (SELECT COALESCE(MAX(con_info_id),0)+1 FROM t_con_info),";  //契約内容ID
				$sql .= "    $contract_id,";                                             //契約情報ID
				$sql .= "    $s,";                                                       //行数
				$sql .= "    '".$divide[$s]."',";                                        //販売区分
				$sql .= "    '".$slip_flg[$s]."',";                                      //サービス印字
				//サービスID判定
				if($serv_id[$s] != NULL){
					$sql .= "    ".$serv_id[$s].",";                                     //サービスID
				}else{
					$sql .= "    NULL,";
				}
				//アイテム印字フラグ
				if($goods_item_id[$s] != NULL){
					$sql .= "    '".$goods_item_flg[$s]."',";                            //アイテム印字
				}else{
					$sql .= "    'false',";
				}
				//アイテム入力判定
				if($goods_item_id[$s] != NULL){
					$sql .= "    ".$goods_item_id[$s].",";                               //アイテムID
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$goods_item_name[$s]."',";                               //アイテム名
				//アイテム数入力判定
				if($goods_item_num[$s] != NULL){
					$sql .= "    ".$goods_item_num[$s].",";                              //アイテム数
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$set_flg[$s]."',";                                       //一式フラグ
				$sql .= "    ".$trade_price[$s].",";                                     //営業原価
				$sql .= "    ".$trade_amount[$s].",";                                    //営業金額
				$sql .= "    ".$sale_price[$s].",";                                      //売上単価
				$sql .= "    ".$sale_amount[$s].",";                                     //売上金額
				//本体入力判定
				if($goods_body_id[$s] != NULL){
					$sql .= "    ".$goods_body_id[$s].",";                               //本体ID
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$goods_body_name[$s]."',";                               //本体名
				//本体数入力判定
				if($goods_body_num[$s] != NULL){
					$sql .= "    ".$goods_body_num[$s].",";                              //本体数
				}else{
					$sql .= "    NULL,";
				}
				//消耗品入力判定
				if($goods_expend_id[$s] != NULL){
					$sql .= "    ".$goods_expend_id[$s].",";                             //消耗品ID
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$goods_expend_name[$s]."',";                             //消耗品名
				//消耗品数入力判定
				if($goods_expend_num[$s] != NULL){
					$sql .= "    ".$goods_expend_num[$s].",";                            //消耗品数
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    ".$ac_price[$s].",";                                       //口座単価
				$sql .= "    '".$ac_rate[$s]."',";                                      //口座率
				$sql .= "    '".$mst_sync_flg[$s]."',";                                 //商品マスタ同期フラグ
				$sql .= "    '".$official_goods_name[$s]."',";                          //アイテム名
				$sql .= "    '".$advance_flg[$s]."',";                                  //前受相殺フラグ

				//前受相殺ありの場合
				if($advance_flg[$s] == "2"){
					$sql .= "    ".$advance_offset_amount[$s]."";                            //前受相殺額
				}else{
					$sql .= "    NULL";
				}
				$sql .= ");";  
				
				$result = Db_Query($db_con, $sql);
				if($result === false){
				    Db_Query($db_con, "ROLLBACK");
				    exit;
				}
			}
		}
		
		//金額をアップデート
		$contract_amount = Update_Act_Amount($db_con, $contract_id,"contract");
		$sale_tamount = $contract_amount[amount][sale][2];

		//税込売上合計より前受相殺額が多い場合
        #2010-05-29 hashimoto-y
		#if ($sale_tamount < $ad_offset_tamount){
		if ($ad_offset_tamount != NULL && $sale_tamount < $ad_offset_tamount){
			$advance_mesg  = "<font color=\"#ff00ff\">[警告]<br>";
			$advance_mesg .= "■税込売上合計（".$sale_tamount."円）より多い前受相殺合計（".$ad_offset_tamount."円）が入力されています。<br>";
			$advance_mesg .= "<br>問題なければ、そのまま「登録」ボタンを押して下さい。";
			$advance_mesg .= "<br>問題があれば、「前受相殺合計」を変更して「登録」ボタンを押して下さい。<p>";
			$advance_mesg .= "</font>";

			$commit_flg = false; //コミットしない
		}

		/****************************/
		//出庫品テーブル登録
		/****************************/
		//出庫品に必要データを$valueとして登録
		$value = array(
			$divide,
			$goods_item_com,
			$goods_item_id,
			$goods_item_name,
			$goods_item_num,
			$goods_expend_com,
			$goods_expend_id,
			$goods_expend_name,
			$goods_expend_num,
			$item_parts,
			$item_parts_cname,
			$item_parts_num,
			$expend_parts,
			$expend_parts_cname,
			$expend_parts_num
		);
		Regist_ConShipDetail($db_con,$contract_id,$value);

		//契約マスタの値をログに書き込む（データコード：得意先CD-行No.  名称：得意先名-契約＋行No.）
		$result = Log_Save($db_con,'contract',$work_div,$client_cd."-".$line,$cname."-契約".$line,$contract_id);
		//ログ登録時にエラーになった場合
		if($result == false){
			Db_Query($db_con,"ROLLBACK;");
			exit;
		}

		/****************************/
		//受注データ＆受払登録     
		/****************************/
		//オンライン代行以外のみ登録
		if($daiko_div != "2"){
			//Aorder_Query($db_con,$client_h_id,$client_id,$start_day,$end_day);
			Regist_Aord_Contract($db_con,$client_h_id,$contract_id,$start_day,$end_day);
		}

		//オンライン代行に変更した場合は受注ヘッダを削除
		if($flg == "chg" && $daiko_div == "2" && $_SESSION["group_kind"] == '2'){
			Delete_Aorder($db_con,$client_id,$start_day,$end_day,$contract_id);
		}

		//****************************
		//コミット処理
		//****************************
		//「予定データの重複なし」または「予定データの重複OK」
		if (($commit_flg !== false) || ($_POST[duplicat_ok] == "1") || ($_POST[advance_ok] == "1") ){
		//if (($commit_flg !== false)){
			Db_Query($db_con, "COMMIT;");                                                     
			header("Location: ./2-1-115.php?client_id=$client_id");                                                                                                          
			exit;
		}
		
		//予定データが重複作成される場合、警告確認フラグを1にする
		if(($commit_flg === false) && ($_POST[duplicat_ok] != "1")){
			$form->setConstants(array(duplicat_ok=>"1"));
		}

		//売上より前受相殺額が多い場合
		if(($commit_flg === false) && ($_POST[advance_ok] != "1")){
			$form->setConstants(array(advance_ok=>"1"));
		}
		Db_Query($db_con, "ROLLBACK;");


	//エラーがあった場合、警告確認フラグを未確認に戻す
	}else{
		$form->setConstants(array(duplicat_ok=>"0"));
		$form->setConstants(array(advance_ok=>"0"));
	}


/****************************/                                                           
//契約マスタ削除                                                                         
/****************************/                                                           
}elseif($_POST["delete_flg"] == true){                                                        

	Db_Query($db_con, "BEGIN");

	//契約マスタ削除
	Delete_Contract($db_con, $get_con_id,$cname);

	//マスタログ書込み（データコード：得意先CD-行No.  名称：得意先名-契約＋行No.）
	$cname = addslashes($cname);  //「'」が含まれる可能性があるため処理実行
	$result = Log_Save($db_con,'contract',3,$client_cd."-".$line,$cname."-契約".$line);
	//ログ登録時にエラーになった場合
	if($result == false){
		Db_Query($db_con,"ROLLBACK;");
		exit;
	}


	
	Db_Query($db_con, "COMMIT");
	header("Location: ./2-1-115.php?client_id=$client_id");
	
//****************************
//行クリア処理
//****************************
}elseif($_POST[clear_line]!=""){
	Clear_Line_Data($form,$_POST[clear_line]);


//****************************
//集計処理処理
//****************************
}elseif($action == "集計"){
	$form_ad_rest_price = Minus_Numformat(Advance_Offset_Claim($db_con, $g_today, $client_id, $claim_div));
	$con_data2["form_ad_rest_price"] = $form_ad_rest_price;    //前受金残高
}

/****************************/
//POST情報の値を変更
/****************************/
$form->setConstants($con_data2);

if($_POST["client_search_flg"]){
	$con_intro_ac_div["intro_ac_div[]"] = 1;
	$form->setConstants($con_intro_ac_div);
}

/****************************/
//javascript
/****************************/
$java_sheet  = Js_Keiyaku();
$java_sheet .= Create_Js("daiko");


//一式にチェックを付けた場合、金額計算処理
$java_sheet  .= "function Set_num(row, coax, daiko_coax){\n";
//FC・直営判定
if($group_kind == 2){
    //直営は、代行料を考慮した計算
    //$java_sheet  .= "    Mult_double3('form_goods_num1['+row+']','form_sale_price['+row+'][1]','form_sale_price['+row+'][2]','form_sale_amount['+row+']','form_trade_price['+row+'][1]','form_trade_price['+row+'][2]','form_trade_amount['+row+']','form_issiki['+row+']',coax,false,row,'',daiko_coax);\n";
    $java_sheet  .= "    Mult_double_All(coax, false, daiko_coax, row);\n";
}else{
    //ＦＣは、普通の一式の計算
    $java_sheet  .= "    Mult_double2('form_goods_num1['+row+']','form_sale_price['+row+'][1]','form_sale_price['+row+'][2]','form_sale_amount['+row+']','form_trade_price['+row+'][1]','form_trade_price['+row+'][2]','form_trade_amount['+row+']','form_issiki['+row+']',coax,false);\n";
}
$java_sheet  .= "}\n\n";


//フォームループ数
$loop_num = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");

$form_load  = 'onLoad="'. $form_load .' ad_offset_radio_disable(); "';

/****************************/
//HTMLヘッダ
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//HTMLフッタ
/****************************/
$html_footer = Html_Footer();

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "(全".Kiyaku_Data_Count($db_con,1)."件)";
$page_title .= "　".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
//$page_title .= "　".$form->_elements[$form->_elementIndex[all_button]]->toHtml();
$page_header = Create_Header($page_title);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());
$smarty->assign('num',$foreach_num);
$smarty->assign('loop_num',$loop_num);

//その他の変数をassign
$smarty->assign('var',array(
	'html_header'     => "$html_header",
	'page_menu'       => "$page_menu",
	'page_header'     => "$page_header",
	'html_footer'     => "$html_footer",
	'java_sheet'      => "$java_sheet",
	'flg'             => "$flg",
	'last_day'        => "$last_day",
	'ac_name'         => "$ac_name",
	'return_flg'      => "$return_flg",
	'get_flg'         => "$get_flg",
	'client_id'       => "$client_id",
	'form_load'       => "$form_load",
	'trade_error_flg' => "$trade_error_flg",
	'auth_r_msg'      => "$auth_r_msg",
	'duplicat_mesg'   => "$duplicat_mesg",
	'advance_mesg'    => "$advance_mesg",
	'group_kind'      => "$group_kind"
));
//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));
/*
$e_time = microtime();
echo "$s_time"."<br>";
echo "$t_time"."<br>";
echo "$e_time"."<br>";
print_array($_POST);
*/

//自社の契約IDかチェックする
function Chk_Contract_Id($db_con,$contract_id){

	$sql = "
		SELECT 
		COUNT(contract_id) 
		FROM t_contract 
		WHERE shop_id = $_SESSION[client_id]
		AND contract_id = '$contract_id'; 
	";
	$result = Db_Query($db_con, $sql);
	$count  = pg_fetch_result($result,0,0);

	//0件の場合はTOPへ遷移
	if ($count == "0"){
		header("Location: ".TOP_PAGE_F);
		exit;
	}

}




?>
