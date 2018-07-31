<?php
/**
 *
 * 予定データ訂正
 *
 *
 * 変更履歴
 *    2006/10/13 (suzuki)
 *      ・遷移元情報をGETに追加
 *    2006/10/26 (kaji)
 *      ・予定データ訂正と、保留伝票変更を合体（以下の処理を追加）
 *          保留理由の入力が必須
 *          保留削除（今回の巡回なし）にする処理
 *    2006-10-30 内訳画面の戻るボタン押下処理変更＜suzuki＞
 *    2006-10-31 倉庫名をサニタイジング＜suzuki＞
 *               ログイン者名に\マークを追加
 *    2006-11-06 内訳ダイアログを表示する際にキャッシュを読み込まないように変更＜suzuki＞
 *   (2006/11/16) (suzuki)
 *     ・構成品の子に単価が設定されていなかったら表示しないように修正
 *      ・ (2006/12/01) 代行伝票の営業原価は委託先の丸めを使用(suzuki)
 *
 */
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/09      03-019      kajioka-h   保留削除ボタンの表示条件修正
 *  2006/11/10      03-024      kajioka-h   保留削除の直前に売上確定されていないかチェック追加
 *  2006/11/13      03-028      kajioka-h   変更の直前に巡回報告されていないかチェック追加
 *  2006/12/10      02-068      suzuki      営業原価・売上単価データ復元処理修正
 *  2006/12/12      03-064      suzuki      変更の直前に巡回報告されていないかチェック追加
 *  2007/02/05      要件26-1    kajioka-h   代行伝票の場合に数量を変更できるように変更
 *  2007/02/16      要件5-1     kajioka-h   商品出荷予定で在庫移動済の予定データは出荷倉庫を担当者(メイン)の担当倉庫にする処理追加
 *  2007/03/02      要望6-3     kajioka-h   アイテム名を「正式（商品分類＋商品名）」と「略称」表示に
 *  2007/03/08      B0702-016   kajioka-h   商品を品名変更不可→変更可に変えた際に、フォームが品名変更可能にならないバグ修正
 *  2007/03/22      要望21      kajioka-h   予定伝票削除機能により、保留伝票変更・削除機能をなくした
 *  2007/03/22      要望21      kajioka-h   予定伝票削除機能により、保留伝票変更・削除機能をなくした
 *  2007/03/29      B0702-017   kajioka-h   代行伝票のアイテム数が0に変更できないバグ修正
 *  2007/04/06      要望1-4     kajioka-h   紹介口座料の仕様変更
 *  2007/04/10      B0702-035   kajioka-h   代行伝票で、受注ヘッダの金額が更新されないバグ修正
 *  2007/04/11      要望1-4     kajioka-h   紹介口座料のラジオボタンが契約マスタと合っていなかったのを修正
 *  2007/04/13      その他      kajioka-h   「配送日」→「予定巡回日」に表示変更
 *                  B0702-037   kajioka-h   代行伝票を受託側で変更すると紹介口座が設定されていない場合にエラーになるのを修正
 *  2007/04/16      その他      morita-d    伝票訂正時に伝票発行日（slip_out_day）をNULLにするよう変更
 *  2007/04/26      他79,152    kajioka-h   代行料の仕様変更
 *  2007/05/17      xx-xxx      kajioka-h   予定データ明細、予定データ訂正、売上伝票訂正、手書伝票でレイアウトを合わせた
 *  2007/05/23      xx-xxx      kajioka-h   代行伝票の取引区分を現金に変更可能にした
 *  2007/06/06      xx-xxx      kajioka-h   代行料の仕様が再度変更になった件
 *  2007/06/15      その他14    kajioka-h   前受金
 *  2007/06/27      xx-xxx      kajioka-h   商品マスタ同期フラグの処理を追加
 *  2007/08/20                  kajioka-h   予定データ訂正でオフライン代行、固定額の場合に営業原価を変更できるように変更
 *  2007/08/29                  kajioka-h   代行伝票で代行料が売上％ので一式○の場合、原価合計額は原価単価と同じにする
 *  2007/11/03                  kajioka-h   オンライン代行で受託先が巡回日を変更すると請求日も変わるように
 *  2009/09/14                  aoyama-n    値引機能追加
 *  2009/09/21                  hashimoto-y 値引商品を選択した場合に赤字表示
 *  2009/09/26                  hashimoto-y 値引赤字表示の不要な処理削除
 *  2009/10/06      rev.1.3     kajioka-h   予定巡回日の訂正時に2ヶ月以上前だと警告メッセージ追加
 *  2009/12/22                  aoyama-n    税率をTaxRateクラスから取得
 *
 */

$page_title = "予定データ訂正";

//環境設定ファイル
require_once("ENV_local.php");

//DBに接続
$db_con = Db_Connect();

//echo "<font color=red style=\"font-family: 'HG創英角ﾎﾟｯﾌﾟ体';\"><b>編集中につき、登録できません</b></font>";

// 権限チェック
$auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;
// 削除ボタンDisabled
$del_disabled = ($auth[1] == "f") ? "disabled" : null;

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");


/****************************/
//エラーメッセージ定義
/****************************/
require_once(INCLUDE_DIR."error_msg_list.inc");

/****************************/
//契約関数定義
/****************************/
require_once(INCLUDE_DIR."function_keiyaku.inc");

//取引区分の関数
require_once(PATH ."function/trade_fc.fnc");

//いまだけ
require_once(INCLUDE_DIR."rental.inc"); //消費税を取得する関数を利用するため


/****************************/
//外部変数取得
/****************************/
$aord_id      = $_GET["aord_id"];        //受注ID
$back_display = $_GET["back_display"];   //予定明細の遷移元
$array_id     = $_GET["aord_id_array"];  //該当日の全ての受注ID
//アンシリアライズ化
$array_id = stripslashes($array_id);
$array_id = urldecode($array_id);
$array_id = unserialize($array_id);

$client_h_id = $_SESSION["client_id"];  //ログインユーザID
$staff_id    = $_SESSION["staff_id"];   //ログイン者ID
$staff_name  = $_SESSION["staff_name"]; //ログイン者名
$group_kind  = $_SESSION["group_kind"]; //グループ種別

$plan_aord_flg = true;  //予定データ訂正フラグ


//受注IDをhiddenにより保持する
if($_GET["aord_id"] != NULL){
	$con_data2["hdn_aord_id"] = $aord_id;
}else{
	$aord_id = $_POST["hdn_aord_id"];
}

//該当日の全ての受注IDをhiddenにより保持する
if($_GET["aord_id_array"] != NULL){
	//受注IDシリアライズ化
	$array_id2 = serialize($array_id);
	$array_id2 = urlencode($array_id2);
	$con_data2["hdn_aord_id_array"] = $array_id2;
}else{
	$array_id = $_POST["hdn_aord_id_array"];
	//アンシリアライズ化
	$array_id = stripslashes($array_id);
	$array_id = urldecode($array_id);
	$array_id = unserialize($array_id);
}

//予定明細の遷移元をhiddenにより保持する
if($_GET["back_display"] != NULL){
	$con_data2["hdn_back_display"] = $back_display;
}else{
	$back_display = $_POST["hdn_back_display"];
}
//直リンクで遷移してきた場合には、TOPに飛ばす
Get_ID_Check2($aord_id);

//不正判定
Get_ID_Check3($aord_id);
Get_ID_Check3($array_id);



/****************************/
//初期設定
/****************************/

#2009-12-22 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($client_h_id);

//受注ヘッダー
$sql  = "SELECT ";
$sql .= "    t_aorder_h.ord_no,";                     //伝票番号 0
$sql .= "    t_aorder_h.ord_time,";                   //予定巡回日 1
$sql .= "    t_aorder_h.route,";                      //順路 2
$sql .= "    t_aorder_h.client_cd1,";                 //得意先cd1 3
$sql .= "    t_aorder_h.client_cd2,";                 //得意先cd2 4
$sql .= "    t_aorder_h.client_cname,";               //得意先名  5   
$sql .= "    t_aorder_h.trade_id,";                   //取引区分  6
$sql .= "    t_aorder_h.hope_day,";                   //売上計上日 7
$sql .= "    t_aorder_h.arrival_day,";                //請求日 8
$sql .= "    t_staff1.staff_id,";                     //担当者１9
$sql .= "    t_staff1.sale_rate,";                    //売上率１10
$sql .= "    t_staff2.staff_id,";                     //担当者２11
$sql .= "    t_staff2.sale_rate,";                    //売上率２12
$sql .= "    t_staff3.staff_id,";                     //担当者３13
$sql .= "    t_staff3.sale_rate,";                    //売上率３14
$sql .= "    t_staff4.staff_id,";                     //担当者４15
$sql .= "    t_staff4.sale_rate,";                    //売上率４16
$sql .= "    t_aorder_h.note, ";                      //備考 17
$sql .= "    t_aorder_h.reason_cor,";                 //訂正理由 18
$sql .= "    t_aorder_h.net_amount, ";                //税抜合計 19
$sql .= "    t_aorder_h.tax_amount, ";                //消費税 20
$sql .= "    t_client.client_id, ";                   //得意先ID 21

$sql .= "    t_aorder_h.act_request_rate, ";          //代行依頼料 22
$sql .= "    t_aorder_h.contract_div, ";              //契約区分 23
$sql .= "    t_aorder_h.act_id, ";                    //代行先ID 24
$sql .= "    t_aorder_h.ware_id, ";                   //出荷倉庫ID 25
$sql .= "    t_aorder_h.ware_name,";                  //出荷倉庫名 26
$sql .= "    t_aorder_h.claim_id || ',' || t_aorder_h.claim_div,";  //請求先ID,請求先区分27
$sql .= "    t_aorder_h.round_form, ";                //巡回形式 28
$sql .= "    t_aorder_h.trust_note, ";                 //備考(受託先) 29
$sql .= "    t_aorder_h.del_flg, ";                     //削除フラグ 30
$sql .= "    t_aorder_h.intro_account_id, ";            //紹介者ID 31
$sql .= "    t_aorder_h.intro_ac_cd1, ";                //紹介者CD1 32
$sql .= "    t_aorder_h.intro_ac_cd2, ";                //紹介者CD2 33
$sql .= "    t_aorder_h.intro_ac_name, ";               //紹介者名 34
$sql .= "    t_aorder_h.intro_ac_div, ";                //紹介口座区分 35
$sql .= "    t_aorder_h.intro_ac_price, ";              //紹介口座単価（得意先） 36
$sql .= "    t_aorder_h.intro_ac_rate, ";               //紹介口座率（得意先） 37
$sql .= "    t_aorder_h.act_div, ";                     //代行料区分 38
$sql .= "    t_aorder_h.act_request_price, ";           //代行料（固定額）39
$sql .= "    t_aorder_h.trust_net_amount, ";            //売上金額（受託先）40
$sql .= "    t_aorder_h.trust_tax_amount, ";            //消費税額（受託先）41
$sql .= "    t_aorder_h.act_cd1, ";                     //代行者CD1 42
$sql .= "    t_aorder_h.act_cd2, ";                     //代行者CD2 43
$sql .= "    t_aorder_h.act_name, ";                    //代行者名 44
$sql .= "    t_aorder_h.claim_div, ";                   //請求先区分 45
$sql .= "    t_aorder_h.advance_offset_totalamount, ";  //前受相殺額合計 46
$sql .= "    t_aorder_h.confirm_flg, ";                 //確定フラグ 47
$sql .= "    t_aorder_h.trust_confirm_flg ";            //受託先用確定フラグ 48

$sql .= "FROM ";
$sql .= "    t_aorder_h ";

$sql .= "    INNER JOIN t_client ON t_aorder_h.client_id  = t_client.client_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_aorder_staff.aord_id,";
$sql .= "             t_staff.staff_id,";
$sql .= "             t_aorder_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_aorder_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_aorder_staff.staff_div = '0'";
$sql .= "        )AS t_staff1 ON t_staff1.aord_id = t_aorder_h.aord_id ";
 
$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_aorder_staff.aord_id,";
$sql .= "             t_staff.staff_id,";
$sql .= "             t_aorder_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_aorder_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_aorder_staff.staff_div = '1'";
$sql .= "        )AS t_staff2 ON t_staff2.aord_id = t_aorder_h.aord_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_aorder_staff.aord_id,";
$sql .= "             t_staff.staff_id,";
$sql .= "             t_aorder_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_aorder_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_aorder_staff.staff_div = '2'";
$sql .= "        )AS t_staff3 ON t_staff3.aord_id = t_aorder_h.aord_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_aorder_staff.aord_id,";
$sql .= "             t_staff.staff_id,";
$sql .= "             t_aorder_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_aorder_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_aorder_staff.staff_div = '3'";
$sql .= "        )AS t_staff4 ON t_staff4.aord_id = t_aorder_h.aord_id ";

$sql .= "WHERE ";
$sql .= "    t_aorder_h.aord_id = $aord_id \n";
$sql .= "    AND \n";
$sql .= "    t_aorder_h.del_flg = false \n";
$sql .= "    AND \n";
$sql .= "    t_aorder_h.hand_plan_flg = false \n";
//受託先はオフライン代行は見えない
if($group_kind != "2"){
    $sql .= "    AND \n";
    $sql .= "    t_aorder_h.contract_div != '3' \n";
}
$sql .= ";";
$result = Db_Query($db_con, $sql); 
Get_Id_Check($result);


//データ配列取得
$result_count = pg_num_rows($result);

for($i = 0; $i < $result_count; $i++){
    $row[] = @pg_fetch_array ($result, $i,PGSQL_NUM);
}
for($i = 0; $i < $result_count; $i++){
    for($j = 0; $j < count($row[$i]); $j++){

		/*
		 * 履歴：
		 * 　日付　　　　B票No.　　　　担当者　　　内容　
		 * 　2006/10/31　02-025　　　　suzuki-t　　倉庫名をサニタイジングする
		 *
		*/
		//サニタイジング判定
		if($j == 5 || $j == 26){
			//得意先名・倉庫名はする
			$data_list[$i][$j] = htmlspecialchars($row[$i][$j]);
		}else{
			//以外
			$data_list[$i][$j] = $row[$i][$j];
		}
    }
}


$contract_div       = $data_list[0][23];    //契約区分
$confirm_flg        = $data_list[0][47];    //確定フラグ
$trust_confirm_flg  = $data_list[0][48];    //受託先用確定フラグ

//確定済はフリーズ
if($confirm_flg == "t"){
    $form->freeze();

//未確定で報告済は訂正理由、前受相殺額以外フリーズ（オンライン代行）
}elseif($trust_confirm_flg == "t" && $contract_div == "2"){
    //ヘッダ部の追加フリーズ
    //intro_ac_div[] は plan_data.inc 内でやってます
    $freeze_data = array(
        "form_delivery_day",
        "trade_aord",
        "form_request_day",
        "form_claim",
        "intro_ac_price",
        "intro_ac_rate",
        "form_note",
    );
    //データ部の追加フリーズ
    for($i=1; $i<=5; $i++){
        array_push($freeze_data, 
            "form_issiki[$i]",
            "form_goods_num1[$i]",
            "form_goods_num2[$i]",
            "form_goods_num3[$i]"
        );
    }

    $form->freeze($freeze_data);
}


$ord_no                                 = $data_list[0][0];                              //伝票番号
$deli_day                               = explode('-',$data_list[0][1]);                 //予定巡回日
$con_data["form_delivery_day"]["y"]     = $deli_day[0];
$con_data["form_delivery_day"]["m"]     = $deli_day[1];
$con_data["form_delivery_day"]["d"]     = $deli_day[2];
$con_data["hdn_former_deli_day"]        = $data_list[0][1];                              //rev.1.3 変更前の予定巡回日をhiddenに保持
$form->addElement("hidden", "hdn_former_deli_day");
$data_list[0][2]                        = str_pad($data_list[0][2], 4, 0, STR_POS_LEFT); //順路
$con_data["form_route_load"][1]         = substr($data_list[0][2],0,2);  
$con_data["form_route_load"][2]         = substr($data_list[0][2],2,2);  
$client_cd                              = $data_list[0][3]."-".$data_list[0][4];         //得意先CD
$client_name = $data_list[0][5];                                                         //得意先名
$con_data["trade_aord"]                 = $data_list[0][6];                              //取引区分
/*
$sale_day                               = explode('-',$data_list[0][7]);                 //売上計上日
$con_data["form_sale_day"]["y"]         = $sale_day[0];
$con_data["form_sale_day"]["m"]         = $sale_day[1];
$con_data["form_sale_day"]["d"]         = $sale_day[2];
*/
$req_day                                = explode('-',$data_list[0][8]);                 //請求日
$con_data["form_request_day"]["y"]      = $req_day[0];
$con_data["form_request_day"]["m"]      = $req_day[1];
$con_data["form_request_day"]["d"]      = $req_day[2];
$con_data["form_c_staff_id1"]           = $data_list[0][9];                              //担当者１
$con_data["form_sale_rate1"]            = $data_list[0][10];                             //売上率１
$con_data["form_c_staff_id2"]           = $data_list[0][11];                             //担当者２
$con_data["form_sale_rate2"]            = $data_list[0][12];                             //売上率２
$con_data["form_c_staff_id3"]           = $data_list[0][13];                             //担当者３
$con_data["form_sale_rate3"]            = $data_list[0][14];                             //売上率３
$con_data["form_c_staff_id4"]           = $data_list[0][15];                             //担当者４
$con_data["form_sale_rate4"]            = $data_list[0][16];                             //売上率４

$con_data2["hdn_contract_div"]          = $contract_div;                                 //hiddenに保持 

$con_data["form_reason"]                = $data_list[0][18];                             //訂正理由

//通常・直営側の代行
$con_data["form_note"] = ($contract_div == "1" || $group_kind == "2") ? $data_list[0][17] : $data_list[0][29];  //備考
$money                              = $data_list[0][19];                                //税抜合計
$tax_money                          = $data_list[0][20];                                //消費税

$total_money                            = $money + $tax_money;                           //伝票合計
//$money = number_format($money);
//$tax_money = number_format($tax_money);
//$total_money = number_format($total_money);
$con_data["form_sale_total"]            = number_format($money);
$con_data["form_sale_tax"]              = number_format($tax_money);
$con_data["form_sale_money"]            = number_format($total_money);

$advance_offset_totalamount         = ($data_list[0][46] != null) ? number_format($data_list[0][46]) : null;    //前受相殺額合計
$con_data["form_ad_offset_total"]   = $advance_offset_totalamount;

$client_id                              = $data_list[0][21];                             //得意先ID
//得意先IDをhiddenにより保持する
if($client_id != NULL){
	$con_data2["hdn_client_id"] = $client_id;
	
}else{
	$client_id = $_POST["hdn_client_id"];
}

/*
//代行依頼料指定判定
if($data_list[0][22] != NULL){
	$trust_rate                             = $data_list[0][22];                        
}else{
	$trust_rate                             = '0';                                       
}
$con_data2["hdn_trust_rate"]            = $trust_rate;                                   //hiddenに保持 
*/

/*
//直営側から代行伝票を訂正する場合、営業原価は入力不可
if($contract_div != '1' && $group_kind != 3){
    //営業原価は入力不可、前受相殺フラグがなしなら金額フォームを入力不可
	//$form_load = "onLoad=\"daiko_checked();\"";
}
*/

//自社巡回、または直営側の代行伝票の場合、前受金が入力できる
if($contract_div == "1" || $group_kind == "2"){
    $form_load = "onLoad=\"ad_offset_radio_disable();\"";
}

$act_id                                 = $data_list[0][24];                             //代行先ID
$con_data["hdn_act_id"]                 = $act_id;
$act_div                                = $data_list[0][38];                             //代行料区分
$con_data["act_request_price"]          = $data_list[0][39];                             //代行料（固定）

if($contract_div != "1"){
    //受託先
    $act_name = $data_list[0][42]."-".$data_list[0][43]."<br>".htmlspecialchars($data_list[0][44]);

    //委託料
    if($act_div == "1"){
        $act_amount = "発生しない";
    }elseif($act_div == "2"){
        //$act_amount = number_format($data_list[0][40])."（固定額）";
        $form->addElement("text","act_request_price","円",'class="money_num" size="7" maxLength="6"'.$g_form_option);
    }elseif($act_div == "3"){
        $act_amount = number_format($data_list[0][40])."（売上の".$data_list[0][22]."％）";
    }

    //代行先のまるめ区分を取得
    $tmp = Get_Tax_div($db_con, $act_id);
    $daiko_coax = $tmp["coax"];

}

$ware_id                                = $data_list[0][25];                             //出荷倉庫ID
$ware_name                              = $data_list[0][26]."】";                        //商品出荷倉庫名

$con_data["form_claim"]                 = $data_list[0][27];                             //請求先

$round_form                             = $data_list[0][28];                             //巡回形式

$intro_account_id                       = $data_list[0][31];                             //紹介者ID
$con_data["hdn_intro_account_id"]       = $intro_account_id;
$intro_ac_cd1                           = $data_list[0][32];                             //紹介者CD1
$intro_ac_cd2                           = $data_list[0][33];                             //紹介者CD2
$intro_ac_name                          = $data_list[0][34];                             //紹介者名
$intro_ac_div                           = $data_list[0][35];                             //紹介口座区分
$con_data["intro_ac_div[]"]             = $intro_ac_div;
$intro_ac_price                         = $data_list[0][36];                             //紹介口座単価
$con_data["intro_ac_price"]             = $intro_ac_price;
$intro_ac_rate                          = $data_list[0][37];                             //紹介口座率
$con_data["intro_ac_rate"]              = $intro_ac_rate;

$claim_div                              = $data_list[0][45];                             //請求先区分

$ad_rest_price                          = Advance_Offset_Claim($db_con, $data_list[0][1], $client_id, $claim_div);  //前受金残高
$con_data["form_ad_rest_price"]         = number_format($ad_rest_price);

//紹介者がFCか仕入先か判定
if($intro_account_id != null){
    $sql = "SELECT client_div FROM t_client WHERE client_id = $intro_account_id;";
    $result = Db_Query($db_con, $sql);
    //仕入先の場合、紹介者CD1のみ
    if(pg_fetch_result($result, 0, "client_div") == "2"){
        $ac_name = $intro_ac_cd1."<br>".htmlspecialchars($intro_ac_name);
    }else{
        $ac_name = $intro_ac_cd1."-".$intro_ac_cd2."<br>".htmlspecialchars($intro_ac_name);
    }
//紹介者がない場合
}else{
    $ac_name = "無し";
    $con_data2["intro_ac_div[]"] = 1;
}


/****************************/
//不正値判定
/****************************/
//FC側の代行伝票表示判定
if($contract_div != '1' && $group_kind == 3){
	//ＦＣ側の代行伝票
	Injustice_check($db_con,"a_trust",$aord_id,$client_h_id);
}else{
	//通常伝票・オフライン代行伝票・直営側のオンライン代行伝票
	Injustice_check($db_con,"aorder",$aord_id,$client_h_id);
}

/****************************/
//受注データテーブル
/****************************/
$sql  = "SELECT ";
$sql .= "    t_aorder_d.line,";            //行数0
$sql .= "    t_aorder_d.sale_div_cd, ";    //販売区分1
$sql .= "    t_aorder_d.serv_print_flg,";  //サービス印字フラグ2
$sql .= "    t_aorder_d.serv_id,";         //サービスID3

$sql .= "    t_aorder_d.goods_print_flg,"; //アイテム印字フラグ4
$sql .= "    t_aorder_d.goods_id,";        //アイテムID5
$sql .= "    t_aorder_d.goods_cd,";        //アイテムCD6
$sql .= "    t_item.name_change,";         //アイテム品名変更7
$sql .= "    t_aorder_d.goods_name,";      //アイテム名（略称）8
$sql .= "    t_aorder_d.num,";             //アイテム数9

$sql .= "    t_aorder_d.set_flg,";         //一式フラグ10

//営業原価判定
if($contract_div != '1' && $group_kind == 3){
	//ＦＣ側で代行を訂正した場合
	$sql .= "    t_aorder_d.trust_cost_price,";     //営業原価(受託先)11
}else{
	//通常伝票、直営で代行を訂正
	$sql .= "    t_aorder_d.cost_price,";           //営業原価11
}
$sql .= "    t_aorder_d.sale_price,";       //売上単価12
//営業金額判定
if($contract_div != '1' && $group_kind == 3){
	//ＦＣ側で代行を訂正した場合
	$sql .= "    t_aorder_d.trust_cost_amount,";    //営業金額(受託先)13
}else{
	//通常伝票、直営で代行を訂正
	$sql .= "    t_aorder_d.cost_amount,";          //営業金額13
}
$sql .= "    t_aorder_d.sale_amount,";      //売上金額14  

$sql .= "    t_aorder_d.rgoods_id,";       //本体ID15
$sql .= "    t_aorder_d.rgoods_cd,";       //本体CD16
$sql .= "    t_body.name_change,";         //本体品名変更17
$sql .= "    t_aorder_d.rgoods_name,";     //本体名18
$sql .= "    t_aorder_d.rgoods_num,";      //本体数19

$sql .= "    t_aorder_d.egoods_id,";       //消耗品ID20
$sql .= "    t_aorder_d.egoods_cd,";       //消耗品CD21
$sql .= "    t_expend.name_change,";       //消耗品品名変更22
$sql .= "    t_aorder_d.egoods_name,";     //消耗品名23
$sql .= "    t_aorder_d.egoods_num,";      //消耗品数24

$sql .= "    t_aorder_d.account_price,";   //口座単価25
$sql .= "    t_aorder_d.account_rate, ";   //口座率26  
$sql .= "    t_aorder_d.aord_d_id, ";      //受注データID27 
$sql .= "    t_aorder_d.contract_id, ";    //契約情報ID28 
$sql .= "    t_aorder_d.official_goods_name, ";     //アイテム名（正式）29
$sql .= "    t_aorder_d.advance_flg, ";             //前受相殺フラグ 30
$sql .= "    t_aorder_d.advance_offset_amount, ";   //前受相殺額 31
//aoyama-n 2009-09-14
#$sql .= "    t_aorder_d.mst_sync_flg ";             //マスタ同期フラグ 32
$sql .= "    t_aorder_d.mst_sync_flg, ";             //マスタ同期フラグ 32
$sql .= "    t_item.discount_flg ";                  //値引フラグ 33

$sql .= "FROM ";
$sql .= "    t_aorder_d ";
$sql .= "    LEFT JOIN t_goods AS t_item ON t_item.goods_id = t_aorder_d.goods_id ";
$sql .= "    LEFT JOIN t_goods AS t_body ON t_body.goods_id = t_aorder_d.rgoods_id ";
$sql .= "    LEFT JOIN t_goods AS t_expend ON t_expend.goods_id = t_aorder_d.egoods_id ";

$sql .= "WHERE ";
$sql .= "    t_aorder_d.aord_id = $aord_id ";
$sql .= ";";
//print($sql);
$result = Db_Query($db_con, $sql);
$sub_data = Get_Data($result,2);

//受注IDに該当するデータが存在するか
for($s=0;$s<count($sub_data);$s++){
	$search_line = $sub_data[$s][0];   //復元する行
	//口座区分の初期値に設定しない行配列
	$aprice_array[] = $search_line;

	$con_data["form_divide"][$search_line]                = $sub_data[$s][1];   //販売区分

	//初期表示のみ復元
	if($_POST["check_value_flg"] == 't'){
		//チェック付けるか判定
		if($sub_data[$s][2] == 't'){
			$con_data2["form_print_flg1"][$search_line]   = $sub_data[$s][2];    //サービス印字フラグ
		}
	}
	$con_data["form_serv"][$search_line]                  = $sub_data[$s][3];    //サービス

	//初期表示のみ復元
	if($_POST["check_value_flg"] == 't'){
		//チェック付けるか判定
		if($sub_data[$s][4] == 't'){
			$con_data2["form_print_flg2"][$search_line]   = $sub_data[$s][4];    //アイテム伝票印字フラグ
		}
	}
	$con_data["hdn_goods_id1"][$search_line]              = $sub_data[$s][5];    //アイテムID
	$con_data["form_goods_cd1"][$search_line]             = $sub_data[$s][6];    //アイテムCD
	$con_data["hdn_name_change1"][$search_line]           = $sub_data[$s][7];    //アイテム品名変更フラグ
	$hdn_name_change[1][$search_line]                     = $sub_data[$s][7];    //POSTする前にアイテム名の変更不可判定を行なう為
	$con_data["form_goods_name1"][$search_line]           = $sub_data[$s][8];    //アイテム名（略称）
	$con_data["form_goods_num1"][$search_line]            = $sub_data[$s][9];    //アイテム数
	$con_data["official_goods_name"][$search_line]        = $sub_data[$s][29];   //アイテム名（正式）
	
	//初期表示のみ復元
	if($_POST["check_value_flg"] == 't'){

		//契約区分が代行のみ形式変更
		if($contract_div != '1'){
			//チェック付けるか判定
			if($sub_data[$s][10] == 't'){
				$con_data2["form_issiki"][$search_line]       = "一式";    //一式フラグ
			}
		}else{
			//チェック付けるか判定
			if($sub_data[$s][10] == 't'){
				$con_data2["form_issiki"][$search_line]       = $sub_data[$s][10];    //一式フラグ
			}
		}
	}

	//改行判定
	if($sub_data[$s][9] != NULL && $sub_data[$s][10] == 't'){
		//数量と一式の場合は、間に改行をいれる
		$br_flg = 'true';
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

    //前受
    $con_data["form_ad_offset_radio"][$search_line]     = $sub_data[$s][30];        //前受相殺フラグ
    $con_data["form_ad_offset_amount"][$search_line]    = $sub_data[$s][31];        //前受相殺額

    $con_data["hdn_mst_sync_flg"][$search_line]         = $sub_data[$s][32];        //マスタ同期フラグ

    //aoyama-n 2009-09-14
    $con_data["hdn_discount_flg"][$search_line]         = $sub_data[$s][33];        //値引フラグ


	/****************************/
	//内訳テーブル
	/****************************/
/*
	$sql  = "SELECT ";
	$sql .= "    aord_d_id, ";                //受注データID
	$sql .= "    line ";                      //行数
	$sql .= "FROM ";
	$sql .= "    t_aorder_d ";
	$sql .= "WHERE ";
	$sql .= "    aord_d_id = ".$sub_data[$s][27].";";
	$result = Db_Query($db_con, $sql);
	$id_data = Get_Data($result);

	//受注IDに該当する受注内容データが存在するか
	for($c=0;$c<count($id_data);$c++){
		$sql  = "SELECT ";
		$sql .= "    t_aorder_detail.line,";          //行
		$sql .= "    t_aorder_detail.goods_id,";      //商品ID
		$sql .= "    t_aorder_detail.goods_cd,";      //商品CD
		$sql .= "    t_goods.name_change,";           //品名変更
		$sql .= "    t_aorder_detail.goods_name,";    //商品名
		$sql .= "    t_aorder_detail.num,";           //数量
		//営業原価・営業判定
		if($contract_div != '1' && $group_kind == 3){
			//ＦＣ側で代行を訂正した場合
			$sql .= "    t_aorder_detail.trust_trade_price,";  //営業原価(受託先)
			$sql .= "    t_aorder_detail.trust_trade_amount,"; //営業金額(受託先)
		}else{
			//通常伝票
			$sql .= "    t_aorder_detail.trade_price,";        //営業原価
			$sql .= "    t_aorder_detail.trade_amount,";       //営業金額
		}
		$sql .= "    t_aorder_detail.sale_price,";    //売上単価
		$sql .= "    t_aorder_detail.sale_amount ";   //売上金額
		$sql .= "FROM ";
		$sql .= "    t_aorder_detail ";
		$sql .= "    INNER JOIN t_goods ON t_goods.goods_id = t_aorder_detail.goods_id ";
		$sql .= "WHERE ";
		$sql .= "    t_aorder_detail.aord_d_id = ".$id_data[$c][0].";";  
		$result = Db_Query($db_con, $sql);
		$detail_data = Get_Data($result,2);

		//受注登録の行番号
		$search_row = $id_data[$c][1];

		//受注内容IDに該当するデータが存在するか
		for($d=0;$d<count($detail_data);$d++){
			$search_line2 = $detail_data[$d][0];                                  //復元する行
			$con_data["hdn_bgoods_id"][$search_row][$search_line2]      = $detail_data[$d][1]; //商品ID
			$con_data["break_goods_cd"][$search_row][$search_line2]     = $detail_data[$d][2]; //商品CD
			$con_data["hdn_bname_change"][$search_row][$search_line2]   = $detail_data[$d][3]; //品名変更
			$con_data["break_goods_name"][$search_row][$search_line2]   = $detail_data[$d][4]; //商品名
			$con_data["break_goods_num"][$search_row][$search_line2]    = $detail_data[$d][5]; //数量

			$t_price = explode('.', $detail_data[$d][6]);
			$con_data["break_trade_price"][$search_row][$search_line2]["1"] = $t_price[0];     //営業原価
			$con_data["break_trade_price"][$search_row][$search_line2]["2"] = ($t_price[1] != null)? $t_price[1] : ($t_price[0] != null)? '00' : '';     

			$s_price = explode('.', $detail_data[$d][8]);
			$con_data["break_sale_price"][$search_row][$search_line2]["1"] = $s_price[0];     //売上単価
			$con_data["break_sale_price"][$search_row][$search_line2]["2"] = ($s_price[1] != null)? $s_price[1] : '00';     

			$con_data["break_trade_amount"][$search_row][$search_line2] = number_format($detail_data[$d][7]); //営業金額
			$con_data["break_sale_amount"][$search_row][$search_line2]  = number_format($detail_data[$d][9]); //売上金額
		}
	}
*/
	
	//受注情報IDをhiddenにより保持する
	$contract_id = $sub_data[$s][28];
	if($contract_id != NULL){
		$con_data2["hdn_contract_id"] = $contract_id;
		
	}else{
		$contract_id = $_POST["hdn_contract_id"];
	}
}

//データが無い口座区分の初期値設定
for($a=1;$a<=5;$a++){
	if(!in_array($a,$aprice_array)) {
		//なし
		$con_data["form_aprice_div[$a]"] = 1;
        $con_data["form_ad_offset_radio"][$a] = "1";
	}
}

$form->setDefaults($con_data);

#2009-09-18 hashimoto-y
#print_r($con_data);
#$form->setConstants($con_data);


/****************************/
//得意先情報取得
/****************************/
//自社伝票か直営なら得意先の丸め
$sql  = "SELECT";
$sql .= "   t_client.coax ";
$sql .= " FROM";
$sql .= "   t_client ";
$sql .= " WHERE";
$sql .= "   t_client.client_id = $client_id";
$sql .= ";";
$result = Db_Query($db_con, $sql); 
Get_Id_Check($result);
$data_list = Get_Data($result);
$coax   = $data_list[0][0];         //丸め区分（得意先）

if($contract_div != '1' && $_SESSION["group_kind"] != "2"){
	//代行伝票は東陽の丸め
	//東陽のclient_idを取得（各ショップの得意先マスタに自動で登録されるやつ）
	$sql = "SELECT client_id FROM t_client WHERE shop_id = $client_h_id AND act_flg = true;";
	$result = Db_Query($db_con, $sql);
	$toyo_id = pg_fetch_result($result, 0, 0);
	//丸め区分取得
	$sql  = "SELECT ";
	$sql .= "   t_client.coax ";    
	$sql .= " FROM";
	$sql .= "   t_client ";
	$sql .= " WHERE";
	$sql .= "   t_client.client_id = $toyo_id";
	$sql .= ";";
    $result = Db_Query($db_con, $sql); 
    Get_Id_Check($result);
    $data_list = Get_Data($result);
    $toyo_coax  = $data_list[0][0];     //丸め区分（受託先の得意先マスタにある東陽）
}


/****************************/
//ログインユーザ情報取得処理
/****************************/
//得意先の情報を抽出
$sql  = "SELECT";
$sql .= "   t_client.rank_cd ";
$sql .= " FROM";
$sql .= "   t_client ";
$sql .= " WHERE";
$sql .= "   t_client.client_id = $client_h_id";
$sql .= ";";
$result = Db_Query($db_con, $sql); 
Get_Id_Check($result);
$data_list = Get_Data($result);

$rank_cd        = $data_list[0][0];        //顧客区分CD

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
	$sql  = " SELECT";
	$sql .= "     t_goods.goods_id,";                      //商品ID 0
	$sql .= "     t_goods.name_change,";                   //品名変更フラグ 1
	$sql .= "     t_goods.goods_cd,";                      //商品コード 2
	$sql .= "     t_goods.goods_cname,";                   //略称 3
	$sql .= "     initial_cost.r_price AS initial_price,"; //営業単価 4
	$sql .= "     sale_price.r_price AS sale_price, ";     //売上単価 5
	$sql .= "     t_goods.compose_flg, ";                  //構成品フラグ 6
    $sql .= "     CASE \n";
    $sql .= "         WHEN t_g_product.g_product_name IS NULL THEN t_goods.goods_name \n";
    $sql .= "         ELSE t_g_product.g_product_name || ' ' || t_goods.goods_name \n";
    #$sql .= "     END \n";                              //商品分類名＋半スペ＋商品名 7
    $sql .= "     END, \n";                              //商品分類名＋半スペ＋商品名 7
    $sql .= "     t_goods.discount_flg \n";              //値引フラグ 8

	$sql .= " FROM";
	$sql .= "     t_goods ";
    $sql .= "     LEFT JOIN t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";

	$sql .= "     INNER JOIN  t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
	$sql .= "     INNER JOIN  t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";

	$sql .= " WHERE";
	$sql .= "     t_goods.goods_cd = '".$_POST["form_goods_cd".$search_line][$search_row]."' ";
	$sql .= " AND ";
	$sql .= "     t_goods.compose_flg = 'f' ";
	$sql .= " AND ";
	$sql .= "     initial_cost.rank_cd = '2' ";
	$sql .= " AND ";
	$sql .= "     sale_price.rank_cd = '4'";
	$sql .= " AND ";
//watanabe-k 変更
    $sql .= "     t_goods.accept_flg = '1'";
    $sql .= " AND";
    //直営判定
	if($_SESSION["group_kind"] == "2"){
        //直営
        $sql .= "     t_goods.state IN (1,3)";
    }else{
        //FC
        $sql .= "     t_goods.state = 1";
    }
    $sql .= " AND";

	//直営判定
	if($_SESSION["group_kind"] == "2"){
		//直営
        $sql .= "     initial_cost.shop_id IN (".Rank_Sql().") ";
    }else{
		//FC
        $sql .= "     initial_cost.shop_id = $client_h_id  \n";
    }
	$sql .= " AND  \n";
	//直営判定
	if($_SESSION["group_kind"] == "2"){
		//直営
		$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id IN (".Rank_Sql().")) \n";
	}else{
		//FC
		$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id) \n";
	}

	//本体商品以外は構成品データを取得
	if($search_line != 2){
		//構成品の親取得SQL
		$sql .= "UNION ";
		$sql .= " SELECT";
		$sql .= "     t_goods.goods_id,";                      //商品ID 0
		$sql .= "     t_goods.name_change,";                   //品名変更フラグ 1
		$sql .= "     t_goods.goods_cd,";                      //商品コード 2
		$sql .= "     t_goods.goods_cname,";                   //略称 3
		$sql .= "     NULL,";                                   // 4
		$sql .= "     NULL,";                                   // 5
		$sql .= "     t_goods.compose_flg, ";                  //構成品フラグ 6
        $sql .= "     CASE \n";
        $sql .= "         WHEN t_g_product.g_product_name IS NULL THEN t_goods.goods_name \n";
        $sql .= "         ELSE t_g_product.g_product_name || ' ' || t_goods.goods_name \n";
        //aoyama-n 2009-09-14
        #$sql .= "     END \n";                                 //商品分類名＋半スペ＋商品名 7
        $sql .= "     END, \n";                                 //商品分類名＋半スペ＋商品名 7
        $sql .= "     t_goods.discount_flg \n";                 //値引フラグ 8
		$sql .= " FROM";
		$sql .= "     t_goods ";
        $sql .= "     LEFT JOIN t_g_product ON t_goods.g_product_id = t_goods.g_product_id \n";
		$sql .= " WHERE";
		$sql .= "     t_goods.goods_cd = '".$_POST["form_goods_cd".$search_line][$search_row]."' ";
		$sql .= " AND ";
		$sql .= "     t_goods.compose_flg = 't' ";
//watanabe-k　変更
        $sql .= " AND ";
        $sql .= "     t_goods.accept_flg = '1'";
        $sql .= " AND ";
        //直営判定
	    if($_SESSION["group_kind"] == "2"){
            //直営
            $sql .= "     t_goods.state IN (1,3)";
        }else{
            //FC
            $sql .= "     t_goods.state = 1";
        }

	}

	$result = Db_Query($db_con, $sql.";");
    $data_num = pg_num_rows($result);
	//データが存在した場合、フォームにデータを表示
	if($data_num == 1){
    	$goods_data = pg_fetch_array($result);

		$con_data2["hdn_goods_id".$search_line][$search_row]         = $goods_data[0];   //商品ID

		$con_data2["hdn_name_change".$search_line][$search_row]      = $goods_data[1];   //品名変更フラグ
		$hdn_name_change[$search_line][$search_row]                  = $goods_data[1];   //POSTする前に商品名の変更不可判定を行なう為
		$_POST["hdn_name_change".$search_line][$search_row]          = $goods_data[1];   //商品変更前のhiddenで判定しないため

		$con_data2["form_goods_cd".$search_line][$search_row]        = $goods_data[2];   //商品CD
		$con_data2["form_goods_name".$search_line][$search_row]      = $goods_data[3];   //商品名（略称）

		//一列目の商品判定
		if($search_line == 1){
			//一列目の商品だけ計算する

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
				
					$con_data2["form_trade_amount"][$search_row]    = number_format($cost_amount);
					$con_data2["form_sale_amount"][$search_row]     = number_format($sale_amount);
				//一式○　数量×の場合、単価×１
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//営業金額計算
		            $cost_amount = bcmul($goods_data[4],1,2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($goods_data[5],1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
					$con_data2["form_trade_amount"][$search_row]    = number_format($cost_amount);
					$con_data2["form_sale_amount"][$search_row]     = number_format($sale_amount);
				//一式×　数量○の場合、単価×数量
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//営業金額計算
		            $cost_amount = bcmul($goods_data[4], $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//売上金額計算
		            $sale_amount = bcmul($goods_data[5], $_POST["form_goods_num1"][$search_row],2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
					$con_data2["form_trade_amount"][$search_row]    = number_format($cost_amount);
					$con_data2["form_sale_amount"][$search_row]     = number_format($sale_amount);
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
					$sql .= "     sale_price.r_price AS sale_price  ";     //売上単価
					                 
					$sql .= " FROM";
					$sql .= "     t_compose ";

					$sql .= "     INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
					$sql .= "     INNER JOIN  t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
					$sql .= "     INNER JOIN  t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";

					$sql .= " WHERE";
					$sql .= "     t_compose.goods_id = ".$goods_data[0];
					$sql .= " AND ";
					$sql .= "     t_compose.parts_goods_id = ".$goods_parts[$i][0];
					$sql .= " AND ";
					$sql .= "     initial_cost.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     sale_price.rank_cd = '4'";
					$sql .= " AND ";
					//直営判定
					if($_SESSION["group_kind"] == "2"){
						//直営
				        $sql .= "     initial_cost.shop_id IN (".Rank_Sql().") ";
				    }else{
						//FC
				        $sql .= "     initial_cost.shop_id = $client_h_id  \n";
				    }
					$sql .= " AND \n";
					//直営判定
					if($_SESSION["group_kind"] == "2"){
						//直営
						$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id IN (".Rank_Sql().")); \n";
					}else{
						//FC
						$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id); \n";
					}
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
			if($_POST["hdn_contract_div"] != 1){

				//営業原価（売上単価×代行依頼料率）
				$daiko_money = $com_s_price * ($_POST["hdn_trust_rate"] / 100);

				$eigyo_money = $daiko_money;
				$con_data2["form_trade_price"][$search_row]["1"] = $eigyo_money;
				$con_data2["form_trade_price"][$search_row]["2"] = '00';

				//営業金額計算処理判定
				if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] != null){
				//一式○　数量○の場合、営業金額は、単価×数量。
					//営業金額計算
			        $cost_amount = bcmul($eigyo_money, $_POST["form_goods_num1"][$search_row],2);
			        $cost_amount = Coax_Col($coax, $cost_amount);
				//一式○　数量×の場合、単価×１
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//営業金額計算
			        $cost_amount = bcmul($eigyo_money,1,2);
			        $cost_amount = Coax_Col($coax, $cost_amount);
				//一式×　数量○の場合、単価×数量
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//営業金額計算
			        $cost_amount = bcmul($eigyo_money, $_POST["form_goods_num1"][$search_row],2);
			        $cost_amount = Coax_Col($coax, $cost_amount);
				}
			}
*/
			$con_data2["form_trade_amount"][$search_row]    = number_format($cost_amount);
			$con_data2["form_sale_amount"][$search_row]     = number_format($sale_amount);

    		$con_data2["official_goods_name"][$search_row]  = $goods_data[7];   //商品名（正式）

            //aoyama-n 2009-09-14
    		$con_data2["hdn_discount_flg"][$search_row]     = $goods_data[8];   //値引フラグ
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
					$sql .= "     sale_price.r_price AS sale_price  ";     //売上単価
					                 
					$sql .= " FROM";
					$sql .= "     t_compose ";

					$sql .= "     INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
					$sql .= "     INNER JOIN  t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
					$sql .= "     INNER JOIN  t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";

					$sql .= " WHERE";
					$sql .= "     t_compose.goods_id = ".$goods_data[0];
					$sql .= " AND ";
					$sql .= "     t_compose.parts_goods_id = ".$goods_parts[$i][0];
					$sql .= " AND ";
					$sql .= "     initial_cost.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     sale_price.rank_cd = '4'";
					$sql .= " AND ";
					//直営判定
					if($_SESSION["group_kind"] == "2"){
						//直営
				        $sql .= "     initial_cost.shop_id IN (".Rank_Sql().") ";
				    }else{
						//FC
				        $sql .= "     initial_cost.shop_id = $client_h_id  \n";
				    }
					$sql .= " AND \n";
					//直営判定
					if($_SESSION["group_kind"] == "2"){
						//直営
						$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id IN (".Rank_Sql().")); \n";
					}else{
						//FC
						$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id); \n";
					}
					$result = Db_Query($db_con, $sql);
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
			$con_data2["form_print_flg".$sline][$search_row]      = "";

			//金額初期化は、アイテム欄の場合のみ
			if($search_line == 1){
				$con_data2["form_trade_price"][$search_row]["1"] = "";
				$con_data2["form_trade_price"][$search_row]["2"] = "";
				$con_data2["form_trade_amount"][$search_row]     = "";
				$con_data2["form_sale_price"][$search_row]["1"] = "";
				$con_data2["form_sale_price"][$search_row]["2"] = "";
				$con_data2["form_sale_amount"][$search_row]     = "";

    			$con_data2["official_goods_name"][$search_row]  = "";
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
		$con_data2["form_print_flg".$sline][$search_row]      = "";

		//金額初期化は、アイテム欄の場合のみ
		if($search_line == 1){
			$con_data2["form_trade_price"][$search_row]["1"] = "";
			$con_data2["form_trade_price"][$search_row]["2"] = "";
			$con_data2["form_trade_amount"][$search_row]     = "";
			$con_data2["form_sale_price"][$search_row]["1"] = "";
			$con_data2["form_sale_price"][$search_row]["2"] = "";
			$con_data2["form_sale_amount"][$search_row]     = "";

		    $con_data2["official_goods_name"][$search_row]  = "";

            //aoyama-n 2009-09-14
		    $con_data2["hdn_discount_flg"][$search_row]     = "";
		}
	}
	$con_data2["goods_search_row"]                  = "";

}


/****************************/
//クリアボタン押下処理
/****************************/
if($_POST["clear_flg"] == true || $_POST["clear_line"] != ""){

    if($_POST["clear_flg"] == true){
        $min = 1;
        $max = 5;
    }else{
        $min = $_POST["clear_line"];
        $max = $_POST["clear_line"];
    }

	//商品欄を全て初期化
	//for($c=1;$c<=5;$c++){
	for($c=$min; $c<=$max; $c++){

		if($contract_div == '1'){
			for($f=1;$f<=3;$f++){
				$con_data2["form_print_flg".$f][$c]      = "";
				$con_data2["hdn_goods_id".$f][$c]        = "";
				$con_data2["hdn_name_change".$f][$c]     = "";
				$con_data2["form_goods_cd".$f][$c]       = "";
				$con_data2["form_goods_name".$f][$c]     = "";
			}
			$con_data2["form_serv"][$c]             = "";
			$con_data2["form_divide"][$c]           = "";
			$con_data2["official_goods_name"][$c]   = "";
			$con_data2["form_sale_price"][$c]["1"]  = "";
			$con_data2["form_sale_price"][$c]["2"]  = "";
			$con_data2["form_sale_amount"][$c]      = "";
			$con_data2["form_account_price"][$c]    = "";
			$con_data2["form_account_rate"][$c]     = "";
			$con_data2["form_aprice_div[$c]"]       = 1;
            $con_data2["form_ad_offset_radio"][$c]  = "1";
            $con_data2["form_ad_offset_amount"][$c] = "";

            //aoyama-n 2009-09-14
            $con_data2["hdn_discount_flg"][$c]      = "f";
		}

        //自社巡回、またはFC側の代行伝票は、営業金額も初期化
        if($contract_div == "1" || $group_kind == "3"){
            $con_data2["form_trade_price"][$c]["1"] = "";
            $con_data2["form_trade_price"][$c]["2"] = "";
            $con_data2["form_trade_amount"][$c]     = "";

		}

		$con_data2["form_issiki"][$c]           = "";
		for($f=1;$f<=3;$f++){
	        $con_data2["form_goods_num".$f][$c]     = "";
		}


/*
		for($j=1;$j<=5;$j++){
			//代行伝票は、営業金額のみ初期化
			if($contract_div == '1'){
				$con_data2["break_goods_cd"][$c][$j] = "";
				$con_data2["break_goods_name"][$c][$j] = "";
				$con_data2["break_goods_num"][$c][$j] = "";
				$con_data2["hdn_bgoods_id"][$c][$j] = "";
				$con_data2["hdn_bname_change"][$c][$j] = "";
				$con_data2["break_sale_price"][$c][$j][1] = "";
				$con_data2["break_sale_price"][$c][$j][2] = "";
				$con_data2["break_sale_amount"][$c][$j] = "";
			}
			$con_data2["break_trade_price"][$c][$j][1] = "";
			$con_data2["break_trade_price"][$c][$j][2] = "";
			$con_data2["break_trade_amount"][$c][$j] = "";
		}
*/
	}

	$post_flg2 = true;               //口座区分を、初期化するフラグ
	$con_data2["clear_flg"] = "";    //クリアボタン押下フラグ
	$con_data2["clear_line"] = "";  //クリアリンク押下フラグ
}


/****************************/
//部品定義
/****************************/

#2009-09-18 hashimoto-y
#plan_data.incに予定データ訂正のフラグを渡す
$plan_edit_flg = 't';
$plan_edit_goods_data = $con_data2;

//受注マスタ用部品(全フォームを定義)
require_once(INCLUDE_DIR."plan_data.inc");


//未確定で報告済は訂正理由、前受相殺額以外フリーズ（オンライン代行）
if($trust_confirm_flg == "t" && $contract_div == "2"){
    //ヘッダ部の追加フリーズ
    //intro_ac_div[] は plan_data.inc 内でやってます
    $freeze_data = array(
        "form_delivery_day",
        "trade_aord",
        "form_request_day",
        "form_claim",
        "intro_ac_price",
        "intro_ac_rate",
        "form_note",
    );
    //データ部の追加フリーズ
    for($i=1; $i<=5; $i++){
        array_push($freeze_data, 
            "form_issiki[$i]",
            "form_goods_num1[$i]",
            "form_goods_num2[$i]",
            "form_goods_num3[$i]"
        );
    }

    $form->freeze($freeze_data);
}


/****************************/
//行クリア処理
/****************************/
/*
if($_POST["clear_line"] != ""){
    Clear_Line_Data2($form, $_POST["clear_line"]);
}
*/


/****************************/
//前受金集計ボタン押下処理
/****************************/
if($_POST["ad_sum_button_flg"] == true || $_POST["correction_flg"] == "true"){

    //配送日
    //●必須チェック
    //$form->addGroupRule("form_delivery_day", $h_mess[26], "required");
    $form->addGroupRule("form_delivery_day", array(
        "y" => array(array("予定巡回日 を入力してください。", "required")),
        "m" => array(array("予定巡回日 を入力してください。", "required")),
        "d" => array(array("予定巡回日 を入力してください。", "required")),
    ));
    //数値チェック
    $form->addGroupRule("form_delivery_day", array(
        "y" => array(array($h_mess[35], "regex", "/^[0-9]+$/")),
        "m" => array(array($h_mess[35], "regex", "/^[0-9]+$/")),
        "d" => array(array($h_mess[35], "regex", "/^[0-9]+$/")),
    ));

    if($form->validate()){
        //妥当性チェック
        if(!checkdate((int)$_POST["form_delivery_day"]["m"], (int)$_POST["form_delivery_day"]["d"], (int)$_POST["form_delivery_day"]["y"])){
            $form->setElementError("form_delivery_day", $h_mess[35]);
        }
    }

    $error_flg = (count($form->_errors) > 0) ? true : false;


    //エラーがない場合、残高集計
    if($error_flg == false){
        $count_day  = str_pad($_POST["form_delivery_day"]["y"], 4, 0, STR_PAD_LEFT);
        $count_day .= "-";
        $count_day .= str_pad($_POST["form_delivery_day"]["m"], 2, 0, STR_PAD_LEFT);
        $count_day .= "-";
        $count_day .= str_pad($_POST["form_delivery_day"]["d"], 2, 0, STR_PAD_LEFT);

        $claim_data = $_POST["form_claim"];     //請求先,請求先区分
        $c_data = explode(',', $claim_data);

        $ad_rest_price  = Advance_Offset_Claim($db_con, $count_day, $client_id, $c_data[1]);
        $ad_rest_price2 = Numformat_Ortho($ad_rest_price, 0, true);

    //エラーの場合、空をセット
    }else{
        $ad_rest_price2 = "";

    }

    $con_data2["form_ad_rest_price"]    = $ad_rest_price2;
    $con_data2["ad_sum_button_flg"]     = "";

}


/****************************/
//POSTデータ取得、エラーチェック(addRule)など
/****************************/
require_once(INCLUDE_DIR."fc_sale_post_bfr.inc");


/****************************/
//受注ヘッダー訂正
/****************************/
if($_POST["correction_flg"] == true || $_POST["warn_ad_flg"] != null){
//訂正ボタンが押されたとき

	/****************************/
    //エラーチェック(PHP)、不正値判定関数など
    /****************************/
    require_once(INCLUDE_DIR."fc_sale_post_atr.inc");

    /****************************/
    //同時実行制御
    //　訂正、変更、保留削除の直前に
    //　　・売上確定された
    //　　・削除された
    //　　・巡回報告された
    //　　・巡回承認された
    //　のチェックして、されてたら訂正・変更、保留削除不可
    /****************************/
    $concurrent_err_flg = false;    //同時実行チェックのエラーフラグ（変更、訂正ボタンを消し戻るボタンを表示）

    $sql = "SELECT confirm_flg, trust_confirm_flg, del_flg FROM t_aorder_h WHERE aord_id = $aord_id;";
    $result = Db_Query($db_con, $sql);

    $chk_confirm_flg        = pg_fetch_result($result, 0, "confirm_flg");       //確定フラグ（代行のときは承認）
    $chk_trust_confirm_flg  = pg_fetch_result($result, 0, "trust_confirm_flg"); //巡回報告フラグ
    $chk_del_flg            = pg_fetch_result($result, 0, "del_flg");           //削除フラグ

    //確定チェック
    //自社巡回の場合
    if($contract_div == "1" && $chk_confirm_flg == "t"){
        $err_mess_confirm_flg = "伝票が売上確定されたため、訂正できません。";
        $error_flg = true;
        $concurrent_err_flg = true;
    //オンライン代行で直営、またはオフライン代行の場合
    //}elseif((($contract_div == "2" && $group_kind == 2) || $contract_div == "3") && $chk_confirm_flg == "t"){
	}elseif(($contract_div == "2" || $contract_div == "3") && $chk_confirm_flg == "t"){
        $err_mess_confirm_flg = "伝票が巡回承認されたため、訂正できません。";
        $error_flg = true;
        $concurrent_err_flg = true;
    //オンライン代行でFCが代行料を売上げた（FC）場合
    //}elseif(($contract_div == "2" && $group_kind != 2) && $chk_trust_confirm_flg == "t"){
    }elseif($contract_div == "2" && $chk_trust_confirm_flg == "t" && $group_kind != "2"){
        $err_mess_confirm_flg = "伝票が巡回報告されたため、訂正できません。";
        $error_flg = true;
        $concurrent_err_flg = true;
    }

    //削除伝票のチェック
    if($chk_del_flg == "t"){
        $del_mess = "伝票が削除されたため、変更できません。";
        $error_flg = true;
        $concurrent_err_flg = true;
    }

    if($concurrent_err_flg){
        $form->addElement("button","slip_del_ok","Ｏ　Ｋ","onClick=\"location.href='".FC_DIR."sale/2-2-106.php?aord_id[0]=$aord_id&aord_id_array=".$array_id."&back_display=$back_display'\"");
        $form->freeze();
    }


	$con_data2["correction_flg"] = "";  //訂正ボタン押下フラグ初期化
	$con_data2["warn_ad_flg"] = "";     //警告を無視して訂正ボタン押下フラグ初期化


    //課税区分、仕入単価
    for($i=1,$goods_item_tax_div=array();$i<=5;$i++){
        if($serv_id[$i] == null && $goods_item_id[$i] == null){
            //サービス、アイテム両方ない場合はnull
            //$goods_item_tax_div[$i] = null;
        }elseif($goods_item_id[$i] != null){
            //アイテムがある場合はアイテムの課税区分
            $sql = "SELECT tax_div FROM t_goods WHERE goods_id = ".$goods_item_id[$i].";";
            $result = Db_Query($db_con, $sql);
            $goods_item_tax_div[$i] = pg_fetch_result($result, 0, 0);
        }else{
            //サービスだけの場合はサービスの課税区分
            $sql = "SELECT tax_div FROM t_serv WHERE serv_id = ".$serv_id[$i].";";
            $result = Db_Query($db_con, $sql);
            $goods_item_tax_div[$i] = pg_fetch_result($result, 0, 0);
        }
    }


    //営業金額の配列詰め直し
    $i=0;
    foreach($trade_amount as $value){
        $trade_amount2[$i] = $value;
        $i++;
    }

    //売上金額の配列詰め直し
    $i=0;
    foreach($sale_amount as $value){
        $sale_amount2[$i] = $value;
        $i++;
    }

    //課税区分の配列詰め直し
    $i=0;
    foreach($goods_item_tax_div as $value){
        $goods_item_tax_div2[$i] = $value;
        $i++;
    }

    $tmp     = Get_Tax_div($db_con, $client_id);
    $tax_franct = $tmp["tax_franct"];                   //端数区分
    #2009-12-22 aoyama-n
    #$tax_num = Get_Tax_Rate($db_con, $client_h_id);     //消費税率

    #2009-12-22 aoyama-n
    $tax_rate_obj->setTaxRateDay($_POST["form_delivery_day"]["y"]."-".$_POST["form_delivery_day"]["m"]."-".$_POST["form_delivery_day"]["d"]);
    $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

    //営業金額の合計処理
    //自社巡回の場合は得意先の丸めで計算
    if($contract_div == "1"){
        $total_money = Total_Amount($trade_amount2, $goods_item_tax_div2, $coax, $tax_franct, $tax_num, $client_id, $db_con);
    //代行の場合、受託先の丸めで計算
    }else{
        $total_money = Total_Amount($trade_amount2, $goods_item_tax_div2, $daiko_coax, $tax_franct, $tax_num, $act_id, $db_con);
    }
    $cost_money  = $total_money[0];

    //売上金額・消費税額の合計処理
    $total_money = Total_Amount($sale_amount2, $goods_item_tax_div2, $coax, $tax_franct, $tax_num, $client_id, $db_con);
    $sale_money  = $total_money[0];
    $sale_tax    = $total_money[1];


    //前受金額のチェック
    if($ad_offset_flg){

        //伝票合計（税込）より前受相殺額合計が大きい場合、警告を表示
        if(($sale_money + $sale_tax) < $ad_offset_total_amount && $_POST["warn_ad_flg"] == null){
            $ad_total_warn_mess = $h_mess[78];

			//rev.1.3 予定巡回日の警告と共用するためコメントアウト
            // 無視用ボタン
            //$form->addElement("button", "form_ad_warn", "警告を無視して訂正",
            //    "onClick=\"javascript:Button_Submit('warn_ad_flg', '".$_SERVER["REQUEST_URI"]."', true);\" $disabled"
            //);
            //$form->addElement("hidden", "warn_ad_flg");

        }

    }//前受金額チェックおわり


    //オフライン代行で固定額の場合、原価金額合計と固定額を変更可能に
    if($contract_div == "3" && $act_div == "2"){
        $act_request_price = $_POST["act_request_price"];       //代行料（固定額）

        //必須チェック
        $form->addRule('act_request_price', $h_mess[60], "required");
        //数値チェック
        $form->addRule('act_request_price', $h_mess[60], "regex", '/^[0-9]+$/');
        //原価金額合計と固定額が等しいかチェック
        if($cost_money != $act_request_price){
            $form->setElementError("act_request_price", $h_mess[68]);
        }
    }


    $form->validate();
    $error_flg = (count($form->_errors) > 0) ? true : $error_flg;


    //rev.1.3 警告無視ボタン押下されてない場合は2ヶ月以上離れているかチェック
    if($error_flg == false && $_POST["warn_ad_flg"] == null){
        $b_lump_day = date("Y-m-d", mktime(0, 0, 0, $delivery_day_m - 2, $delivery_day_d, $delivery_day_y));    //2ヶ月前
        $a_lump_day = date("Y-m-d", mktime(0, 0, 0, $delivery_day_m + 2, $delivery_day_d, $delivery_day_y));    //2ヶ月後
        //2ヶ月以上離れている
        if(($_POST["hdn_former_deli_day"] <= $b_lump_day) || ($_POST["hdn_former_deli_day"] >= $a_lump_day)){
            $warn_lump_change = "入力した予定巡回日は2ヶ月以上離れています。";
        //守備範囲内
        }else{
            $warn_lump_change = null;
        }
    }else{
        $warn_lump_change = null;
    }

	//rev.1.3 前受金、予定巡回日で使う警告無視ボタン
	if($ad_total_warn_mess != null || $warn_lump_change != null){
        // 無視用ボタン
        $form->addElement("button", "form_ad_warn", "警告を無視して訂正",
            "onClick=\"javascript:Button_Submit('warn_ad_flg', '".$_SERVER["REQUEST_URI"]."', true);\" $disabled"
        );
        $form->addElement("hidden", "warn_ad_flg");
	}


    //エラーの場合はこれ以降の表示処理を行なわない
    //if($form->validate() && $error_flg == false){
    //if($error_flg == false && $ad_total_warn_mess == null){
	//rev.1.3 エラーじゃなく、前受金警告が出てなく、予定巡回日が2ヶ月以上離れていない
    if($error_flg == false && $ad_total_warn_mess == null && $warn_lump_change == null){

        //商品予定出荷一覧で在庫移動済の予定データは、出荷倉庫を担当者(メイン)の担当倉庫に変更する
        $sql = "SELECT move_flg FROM t_aorder_h WHERE aord_id = $aord_id;";
        $result = Db_Query($db_con, $sql);
        $move_flg = pg_fetch_result($result, 0, 0);     //移動フラグ

		Db_Query($db_con, "BEGIN");

		//契約区分が通常か判定
		if($contract_div == '1'){
			//通常

			/****************************/
			//受注ヘッダ・受注データ・巡回担当者・内訳・出庫品・受払、訂正処理
			/****************************/
			require_once(INCLUDE_DIR."plan_data_sql.inc");

		}else{
			//代行伝票

            /****************************/
            //受注ヘッダ訂正処理(直営・ＦＣ両方とも更新ＯＫ)
            /****************************/
            //受注ヘッダー更新SQL
            $sql  = "UPDATE t_aorder_h SET \n";

            //受託先が報告していると以下は更新しない
            if($trust_confirm_flg != "t"){
                $sql .= "    ord_time = '$delivery_day', \n";       //予定巡回日
                $sql .= "    trade_id = $trade_aord, \n";           //取引区分
            }

            //直営判定
            if($group_kind == 2){
                //直営
                if($trust_confirm_flg != "t"){
                    //受託先が報告してると以下は更新しない
                    $sql .= "    arrival_day = '$request_day', \n"; //請求日
                    $sql .= "    claim_div = '$claim_div', \n";     //請求区分
                    $sql .= "    claim_id = $claim_id, \n";         //請求先ID
                    $sql .= "    note = '$note', \n";               //備考
                }
                if($ad_offset_flg){
                    $sql .= "    advance_offset_totalamount = $ad_offset_total_amount, \n"; //前受相殺額合計
                }else{
                    $sql .= "    advance_offset_totalamount = NULL, \n";
                }
                //オフライン代行で固定額の場合、固定額を更新
                if($contract_div == "3" && $act_div == "2"){
                    $sql .= "    act_request_price = $act_request_price, \n";
                }
            }else{
                //FC
                $sql .= "    arrival_day = '$delivery_day', \n";    //請求日（予定巡回日と同じにする）
                $sql .= "    route = $route, \n";               //順路
                $sql .= "    trust_note = '$note', \n";         //備考(受託先)
                //商品予定出荷で移動済の場合は出荷倉庫も変更
                if($move_flg == "t"){
                    $sql .= "    ware_id = (SELECT ware_id FROM t_attach WHERE staff_id = ".$staff_check[0]." AND shop_id = ".$_SESSION["client_id"]."), \n";
                    $sql .= "    ware_name = (SELECT ware_name FROM t_ware WHERE ware_id = ";
                    $sql .= "(SELECT ware_id FROM t_attach WHERE staff_id = ".$staff_check[0]." AND shop_id = ".$_SESSION["client_id"].")), \n";
                }
                //前受金ありの場合、受託先が入力した予定巡回日を請求日にも更新
                if($ad_offset_flg){
                    $sql .= "    arrival_day = '$delivery_day', \n";
                }
            }

            $sql .= "    reason_cor = '$reason', \n";       //訂正理由
            $sql .= "    change_flg = 't', \n";             //確定前変更フラグ
            $sql .= "    slip_flg = 'f', \n";               //伝票出力フラグ
            $sql .= "    slip_out_day = NULL, \n";          //伝票出力日
            $sql .= "    ship_chk_cd = NULL, \n";           //変更チェックコード
            $sql .= "    ord_staff_id = $staff_id, \n";     //入力者ID
            /*
             * 履歴：
             * 　日付　　　　B票No.　　　　担当者　　　内容　
             * 　2006/10/31　02-024　　　　suzuki-t　　スタッフ名に\マーク追加 
             */
            $sql .= "    ord_staff_name = '".addslashes($_SESSION["staff_name"])."', \n";   //入力者名
            $sql .= "    change_day = CURRENT_TIMESTAMP \n";

            $sql .= "WHERE \n";
            $sql .= "    aord_id = $aord_id \n";
            $sql .= ";";
//print_array($sql);
            $result = Db_Query($db_con, $sql);
            if($result == false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            //ＦＣ判定
            if($group_kind == 3){

				/****************************/
				//巡回担当者テーブル登録
				/****************************/
				$sql  = "DELETE FROM ";
				$sql .= "    t_aorder_staff ";
				$sql .= "WHERE ";
				$sql .= "    aord_id = $aord_id;";
				$result = Db_Query($db_con, $sql);
				if($result === false){
				    Db_Query($db_con, "ROLLBACK;");
				    exit;
				}

				for($c=0;$c<=3;$c++){
					//スタッフが指定されているか判定
					if($staff_check[$c] != NULL){
						//履歴用
						$sql = "SELECT staff_name FROM t_staff WHERE staff_id = ".$staff_check[$c].";";
						$result = Db_Query($db_con, $sql);
						$staff_data = Get_Data($result,3);

						$sql  = "INSERT INTO t_aorder_staff( ";
						$sql .= "    aord_id,";
						$sql .= "    staff_div,";
						$sql .= "    staff_id,";
						$sql .= "    sale_rate, ";
						$sql .= "    staff_name ";
						$sql .= "    )VALUES(";
						$sql .= "    $aord_id,";                          //受注ID
						$sql .= "    '$c',";                              //巡回担当者識別
						$sql .= "    ".$staff_check[$c].",";              //巡回担当者ID
						//売上率指定判定
						if($staff_rate[$c] != NULL){
							$sql .= "    ".$staff_rate[$c].",";           //売上率
						}else{
							$sql .= "    NULL,";
						}
						$sql .= "    '".$staff_data[0][0]."'";            //担当者名
						$sql .= ");";
						$result = Db_Query($db_con, $sql);
						if($result === false){
						    Db_Query($db_con, "ROLLBACK");
						    exit;
						}
					}
				}
            }

			/****************************/
			//受注データ更新
			/****************************/
			$sale_data = NULL;
			$cost_data = NULL;
            $tax_div   = NULL;
			for($s=1;$s<=5;$s++){
                //数量、一式、金額更新
                //更新行判定
				//if($sale_amount[$s] != NULL){
				if($divide[$s] != NULL){
                    //受注データの課税区分、仕入単価、原価単価、売上単価、仕入単価（受託先）、原価単価（受託先）、売上単価（受託先）を取得
                    $sql = "SELECT tax_div, buy_price, cost_price, sale_price, trust_buy_price, trust_cost_price, trust_sale_price FROM t_aorder_d WHERE aord_id = $aord_id AND line = $s ;";
                    $result = Db_Query($db_con, $sql);

                    $tax_div[]          = pg_fetch_result($result, 0, "tax_div");           //課税区分
                    $d_buy_price        = pg_fetch_result($result, 0, "buy_price");         //仕入単価
                    $d_sale_price       = pg_fetch_result($result, 0, "sale_price");        //売上単価
                    $d_trust_buy_price  = pg_fetch_result($result, 0, "trust_buy_price");   //仕入単価（受託先）

                    //原価単価
                    if($group_kind == "2" && $contract_div == "3" && $act_div == "2"){
                        //直営でオフライン代行で固定額の場合はPOSTから
                        $d_cost_price       = $trade_price[$s];

                        //仕入単価を原価単価と同じに
                        $d_buy_price        = $d_cost_price;
                    }else{
                        //それ以外はDBから
                        $d_cost_price       = pg_fetch_result($result, 0, "cost_price");
                    }

                    //原価単価（受託先）
                    if($group_kind == "2"){
                        //直営の場合はDBから
                        $d_trust_cost_price = pg_fetch_result($result, 0, "trust_cost_price");
                    }else{
                        //受託先の場合はPOSTから
                        $d_trust_cost_price = $trade_price[$s];
                    }
                    //$d_trust_sale_price = pg_fetch_result($result, 0, "trust_sale_price");  //売上単価（受託先）


                    //金額計算処理判定
                    if($set_flg[$s] == 'true' && $_POST["form_goods_num1"][$s] != null){
                    //一式○　数量○の場合、仕入金額、営業金額は、単価×数量。売上金額は、単価×１
                        //仕入金額
                        $d_buy_amount = bcmul($d_buy_price, $goods_item_num[$s], 2);
                        $d_buy_amount = Coax_Col($daiko_coax, $d_buy_amount);

                        //営業金額
                        $d_cost_amount = bcmul($d_cost_price, $goods_item_num[$s], 2);
                        $d_cost_amount = Coax_Col($daiko_coax, $d_cost_amount);

                        //売上金額
                        $d_sale_amount = bcmul($d_sale_price, 1, 2);
                        $d_sale_amount = Coax_Col($coax, $d_sale_amount);

                    //一式○　数量×の場合、単価×１
                    }else if($set_flg[$s] == 'true' && $_POST["form_goods_num1"][$s] == null){
                        //仕入金額
                        $d_buy_amount = bcmul($d_buy_price, 1, 2);
                        $d_buy_amount = Coax_Col($daiko_coax, $d_buy_amount);

                        //営業金額
                        $d_cost_amount = bcmul($d_cost_price, 1, 2);
                        $d_cost_amount = Coax_Col($daiko_coax, $d_cost_amount);

                        //売上金額
                        $d_sale_amount = bcmul($d_sale_price, 1, 2);
                        $d_sale_amount = Coax_Col($coax, $d_sale_amount);

                    //一式×　数量○の場合、単価×数量
                    }else if($set_flg[$s] == 'false' && $_POST["form_goods_num1"][$s] != null){
                        //仕入金額
                        $d_buy_amount = bcmul($d_buy_price, $goods_item_num[$s], 2);
                        $d_buy_amount = Coax_Col($daiko_coax, $d_buy_amount);

                        //営業金額
                        $d_cost_amount = bcmul($d_cost_price, $goods_item_num[$s], 2);
                        $d_cost_amount = Coax_Col($daiko_coax, $d_cost_amount);

                        //売上金額
                        $d_sale_amount = bcmul($d_sale_price, $goods_item_num[$s], 2);
                        $d_sale_amount = Coax_Col($coax, $d_sale_amount);
                    }


					$sql  = "UPDATE t_aorder_d SET \n";
                    //アイテム数量
                    if($goods_item_num[$s] != null){
                        $sql .= "    num = ".$goods_item_num[$s].", \n";
                    }else{
                        $sql .= "    num = NULL, \n";
                    }
                    //本体商品数量
                    if($goods_body_id[$s] != null){
                        $sql .= "    rgoods_num = ".$goods_body_num[$s].", \n";
                    }
                    //消耗品数量
                    if($goods_expend_id[$s] != null){
                        $sql .= "    egoods_num = ".$goods_expend_num[$s].", \n";
                    }
                    //一式フラグ
                    $sql .= "    set_flg = '".$set_flg[$s]."', \n";

                    //直営でオフライン代行で固定額の場合は営業原価、仕入単価を登録
                    if($group_kind == "2" && $contract_div == "3" && $act_div == "2"){
                        $sql .= "    cost_price = $d_cost_price, \n";   //原価単価
                        $sql .= "    buy_price = $d_buy_price, \n";     //仕入単価
                    }

                    //受託先の場合は営業原価（受託先）を登録
                    if($group_kind == "3"){
                        $sql .= "    trust_cost_price = $d_trust_cost_price, \n";

                    //委託先の場合は前受相殺を登録
                    }else{
                        $sql .= "    advance_flg = '".$ad_flg[$s]."', \n";
                        $sql .= ($ad_flg[$s] == "2") ? "    advance_offset_amount = ".$ad_offset_amount[$s].", \n" : "    advance_offset_amount = NULL, \n";
                    }
                    $sql .= "    buy_amount = $d_buy_amount, \n";   //仕入金額
                    $sql .= "    cost_amount = $d_cost_amount, \n"; //営業金額
                    $sql .= "    sale_amount = $d_sale_amount \n";  //売上金額

					$sql .= " WHERE \n";
					$sql .= "    line = $s \n";
					$sql .= " AND \n";
					$sql .= "    aord_id = $aord_id \n";
                    $sql .= ";\n";

					$result = Db_Query($db_con, $sql);
					if($result === false){
					    Db_Query($db_con, "ROLLBACK;");
					    exit;
					}

                    //ヘッダに登録する金額合計
                    $cost_data[] = $d_cost_amount;  //原価金額（税抜）
                    $sale_data[] = $d_sale_amount;  //売上金額（税抜）
                    //$buy_data[]  = $d_buy_amount;   //消費税額

				}
			}


			/****************************/
			//受注ヘッダー 金額 登録処理
			/****************************/
			if($sale_data != NULL){
                $sql = "SELECT tax_franct FROM t_client WHERE client_id = $client_id;";
				$result = Db_Query($db_con, $sql);
				if($result === false){
				    Db_Query($db_con, "ROLLBACK;");
				    exit;
				}
                $tax_franct = pg_fetch_result($result, 0, 0);   //得意先の消費税端数区分

                #2009-12-22 aoyama-n
                #$sql  = "SELECT tax_rate_n \n";
                #$sql .= "FROM t_client INNER JOIN t_aorder_h ON t_client.client_id = t_aorder_h.shop_id \n";
                #$sql .= "WHERE aord_id = $aord_id;";
				#$result = Db_Query($db_con, $sql);
				#if($result === false){
				#    Db_Query($db_con, "ROLLBACK;");
				#    exit;
				#}
                #$tax_num = pg_fetch_result($result, 0, 0);      //委託先の消費税率（現在）

                #2009-12-22 aoyama-n
                $sql  = "SELECT shop_id \n";
                $sql .= "FROM t_aorder_h \n";
                $sql .= "WHERE aord_id = $aord_id;";
				$result = Db_Query($db_con, $sql);
				if($result === false){
				    Db_Query($db_con, "ROLLBACK;");
				    exit;
				}
                $toyo_shop_id = pg_fetch_result($result, 0, 0);      //東陽のショップID

                #2009-12-22 aoyama-n
                //税率クラス　インスタンス生成
                $act_tax_rate_obj = new TaxRate($toyo_shop_id);
                $act_tax_rate_obj->setTaxRateDay($_POST["form_delivery_day"]["y"]."-".$_POST["form_delivery_day"]["m"]."-".$_POST["form_delivery_day"]["d"]);
                $tax_num = $act_tax_rate_obj->getClientTaxRate($client_id);

				//売上金額、消費税額の合計処理
				$total_money  = Total_Amount($sale_data, $tax_div,$coax,$tax_franct,$tax_num,$client_id,$db_con);
				$sale_money   = $total_money[0];
                $tax_money    = $total_money[1];
				//原価金額（DB）の合計処理
				//$total_money  = Total_Amount($cost_data, $tax_div,$coax,$tax_franct,$tax_num,$client_id,$db_con);
				$total_money  = Total_Amount($cost_data, $tax_div, $daiko_coax, $tax_franct, $tax_num, $act_id, $db_con);
				$cost_money   = $total_money[0];

                //受注ヘッダの原価金額（税抜）、売上金額（税抜）、消費税額を更新
                //紹介口座を更新
                $sql  = "UPDATE t_aorder_h SET ";
                $sql .= "    net_amount = $sale_money, ";       //売上金額（税抜）
                $sql .= "    tax_amount = $tax_money, ";        //消費税額
                $sql .= "    cost_amount = $cost_money, ";      //原価金額（税抜）
                if($intro_account_id != NULL){
                    $sql .= "    intro_account_id = $intro_account_id, ";       //紹介口座先ID
                    $sql .= "    intro_ac_cd1 = (SELECT client_cd1 FROM t_client WHERE client_id = $intro_account_id), ";       //紹介口座先CD1
                    $sql .= "    intro_ac_cd2 = (SELECT client_cd2 FROM t_client WHERE client_id = $intro_account_id), ";       //紹介口座先CD2
                    $sql .= "    intro_ac_name = (SELECT client_cname FROM t_client WHERE client_id = $intro_account_id), ";    //紹介口座先名
                }else{
                    $sql .= "    intro_account_id = NULL, ";    //紹介口座先ID
                    $sql .= "    intro_ac_cd1 = NULL, ";        //紹介口座先CD1
                    $sql .= "    intro_ac_cd2 = NULL, ";        //紹介口座先CD2
                    $sql .= "    intro_ac_name = NULL, ";       //紹介口座先名
                }
                $sql .= "    intro_ac_div = '$intro_ac_div', ";             //紹介口座区分
                if($intro_ac_price != NULL){
                    $sql .= "    intro_ac_price = $intro_ac_price, ";       //紹介口座単価
                }else{
                    $sql .= "    intro_ac_price = NULL, ";
                }
                if($intro_ac_rate != NULL){
                    $sql .= "    intro_ac_rate = '$intro_ac_rate' ";        //紹介口座率
                }else{
                    $sql .= "    intro_ac_rate = NULL ";
                }
                $sql .= "WHERE ";
                $sql .= "    aord_id = $aord_id ";
                $sql .= ";";
//print($sql);

                $result = Db_Query($db_con, $sql);
                if($result == false){
                    Db_Query($db_con, "ROLLBACK");
                    exit;
                }

                //紹介料を計算
                if($intro_ac_div != "1" && $intro_account_id != null){
                    $intro_amount = FC_Intro_Amount_Calc($db_con, "aord", $aord_id);

                    if($intro_amount === false){
                        Db_Query($db_con, "ROLLBACK");
                        exit;
                    }
                }else{
                    $intro_amount = null;
                }

                //紹介料を更新
                $sql  = "UPDATE t_aorder_h SET ";
                if($intro_amount !== null){
                    $sql .= "    intro_amount = $intro_amount ";    //紹介口座料
                }else{
                    $sql .= "    intro_amount = NULL ";
                }
                $sql .= "WHERE ";
                $sql .= "    aord_id = $aord_id;";

                $result = Db_Query($db_con, $sql);
                if($result == false){
                    Db_Query($db_con, "ROLLBACK");
                    exit;
                }

			}

            //金額更新関数（以下のカラムを更新）
            //受注ヘッダ
            //・営業金額（受託先）
            //・売上金額（受託先）
            //・消費税（受託先）
            //受注データ
            //・仕入金額（受託先）
            //・営業金額（受託先）
            //・売上金額（受託先）
            $result = Update_Act_Amount($db_con, $aord_id, "aord");
			if($result === false){
			    Db_Query($db_con, "ROLLBACK;");
				exit;
			}


			/****************************/
			//内訳テーブル更新
			/****************************/
/*
			for($s=1;$s<=5;$s++){
				//受注データID取得SQL
				$sql  = "SELECT ";
				$sql .= "    aord_d_id ";
				$sql .= "FROM ";
				$sql .= "    t_aorder_d ";
				$sql .= "WHERE ";
				$sql .= "    aord_id = $aord_id ";
				$sql .= "AND ";
				$sql .= "    line = $s;";
				$result = Db_Query($db_con, $sql);
				$row_num = pg_num_rows($result);

				//受注データID存在判定
				if($row_num == 1){
					$aord_d_id = pg_fetch_result($result,0,0);    //受注データID

					for($d=1;$d<=5;$d++){
						//内訳商品IDが指定されているか判定
						if($break_goods_id[$s][$d] != NULL){
							$sql  = "UPDATE t_aorder_detail SET ";
							$sql .= "    trust_trade_price = ".$break_trade_price[$s][$d].",";       //営業原価
							$sql .= "    trust_trade_amount = ".$break_trade_amount[$s][$d];         //営業金額
							$sql .= " WHERE ";
							$sql .= "    line = $d ";
							$sql .= " AND ";
							$sql .= "    aord_d_id = $aord_d_id;";

							$result = Db_Query($db_con, $sql);
							if($result === false){
							    Db_Query($db_con, "ROLLBACK");
							    exit;
							}
						}	
					}
				}
			}
*/
			/****************************/
			//出庫品削除
			/****************************/
            $sql  = "DELETE FROM \n";
            $sql .= "    t_aorder_ship \n";
            $sql .= "WHERE \n";
            $sql .= "    aord_d_id IN (SELECT aord_d_id FROM t_aorder_d WHERE aord_id = $aord_id) \n";
            $sql .= ";";

			$result = Db_Query($db_con, $sql);
			if($result === false){
				Db_Query($db_con, "ROLLBACK;");
				exit;
			}

			/****************************/
			//受払データ削除
			/****************************/
            $sql  = "DELETE FROM \n";
            $sql .= "    t_stock_hand \n";
            $sql .= "WHERE \n";
            $sql .= "    aord_d_id IN (SELECT aord_d_id FROM t_aorder_d WHERE aord_id = $aord_id) \n";
            $sql .= ";";

			$result = Db_Query($db_con, $sql);
			if($result === false){
				Db_Query($db_con, "ROLLBACK;");
				exit;
			}


			/****************************/
			//出庫品・受払データ登録
			/****************************/
            for($s=1;$s<=5;$s++){
                if($sale_amount[$s] != NULL){
                    $sql = "SELECT aord_d_id FROM t_aorder_d WHERE aord_id = $aord_id AND line = $s;";
        			$result = Db_Query($db_con, $sql);
		        	if($result === false){
				        Db_Query($db_con, "ROLLBACK;");
        				exit;
		        	}
                    $aord_d_id = pg_fetch_result($result, 0, 0);

                    require(INCLUDE_DIR."plan_data_sql_stock_hand.inc");
                }
            }
		}

		Db_Query($db_con, "COMMIT;"); 

		//予定明細に遷移ボタン
		header("Location: ".FC_DIR."sale/2-2-106.php?aord_id[0]=$aord_id&aord_id_array=".$array_id."&back_display=$back_display");

/*
		//カレンダー識別判定
		switch($back_display){
			case 'cal_month':
				//カレンダー(月)
				header("Location: ".FC_DIR."sale/2-2-101-2.php");
				break;
			case 'cal_week':
				//カレンダー(週)
				header("Location: ".FC_DIR."sale/2-2-102-2.php");
				break;
			case 'confirm':
				//売上確定
				header("Location: ".FC_DIR."sale/2-2-206.php");
				break;
			case 'output':
				//伝票発行
				header("Location: ".FC_DIR."sale/2-2-204.php");
				break;
			case 'count_daily':
				//集計日報
				header("Location: ".FC_DIR."sale/2-2-113.php");
				break;
			case 'round':
				//巡回報告一覧(委託先)
				header("Location: ".FC_DIR."system/2-1-237.php");
				break;
			case 'round_act':
				//巡回報告一覧(受託先)
				header("Location: ".FC_DIR."system/2-1-238.php");
				break;
			Default:
				//無い場合は、カレンダー(月)に遷移
				header("Location: ".FC_DIR."sale/2-2-101-2.php");
		}
*/
	}
}


/****************************/
//POST情報の値を変更
/****************************/
$form->setConstants($con_data2);

($freeze_data != null) ? $form->freeze($freeze_data) : "";


#2009-09-21 hashimoto-y
$freeze_flg = $form->isFrozen();
#echo "freeze_flg:" .$freeze_flg;

if($contract_div == '1' || $hand_slip_flg == true || $hand_plan_flg == true){
}else{
#if($contract_div != '1' || $hand_slip_flg != true || $hand_plan_flg != true){
    #echo "freeze";

    $num = 5;
    $toSmarty_discount_flg = array();
    for ($i=1; $i<=$num; $i++){
        $hdn_discount_flg = $form->getElementValue("hdn_discount_flg[$i]");
        #2009-09-26 hashimoto-y
        #if($hdn_discount_flg === 't' ||
        #   $trade_sale_select[0] == '13' || $trade_sale_select[0] == '14' || $trade_sale_select[0] == '63' || $trade_sale_select[0] == '64'
        if($hdn_discount_flg === 't'
        ){
            $toSmarty_discount_flg[$i] = 't';
        }else{
            $toSmarty_discount_flg[$i] = 'f';

        }
    }
}



/****************************/
//javascript
/****************************/

//一式にチェックを付けた場合、金額計算処理
$java_sheet  = "function Set_num(row,coax,daiko_coax){ \n";
//直営は代行伝票で代行料％の場合、営業原価も売上金額と同様に単価×１にする
if($group_kind == "2"){
    $java_sheet .= "    Mult_double2( \n";
    $java_sheet .= "        'form_goods_num1['+row+']', \n";
    $java_sheet .= "        'form_sale_price['+row+'][1]', \n";
    $java_sheet .= "        'form_sale_price['+row+'][2]', \n";
    $java_sheet .= "        'form_sale_amount['+row+']', \n";
    $java_sheet .= "        'form_trade_price['+row+'][1]', \n";
    $java_sheet .= "        'form_trade_price['+row+'][2]', \n";
    $java_sheet .= "        'form_trade_amount['+row+']', \n";
    $java_sheet .= "        'form_issiki['+row+']', \n";
    $java_sheet .= "        coax, \n";
    $java_sheet .= "        false, \n";
    $java_sheet .= "        daiko_coax, \n";
    $java_sheet .= "        '$contract_div', \n";
    $java_sheet .= "        '$act_div' \n";
    $java_sheet .= "    );\n";
}else{
    $java_sheet .= "    Mult_double2( \n";
    $java_sheet .= "        'form_goods_num1['+row+']', \n";
    $java_sheet .= "        'form_sale_price['+row+'][1]', \n";
    $java_sheet .= "        'form_sale_price['+row+'][2]', \n";
    $java_sheet .= "        'form_sale_amount['+row+']', \n";
    $java_sheet .= "        'form_trade_price['+row+'][1]', \n";
    $java_sheet .= "        'form_trade_price['+row+'][2]', \n";
    $java_sheet .= "        'form_trade_amount['+row+']', \n";
    $java_sheet .= "        'form_issiki['+row+']', \n";
    $java_sheet .= "        coax, \n";
    $java_sheet .= "        false, \n";
    $java_sheet .= "        daiko_coax \n";
    $java_sheet .= "    );\n";
}
$java_sheet .= "}\n\n";

//代行用
$java_sheet .= <<<DAIKO

//オンライン代行・オフライン代行の場合、営業原価入力不可
function daiko_checked(){
	//営業原価
	document.dateForm.elements["form_trade_price[1][1]"].readOnly = true;
	document.dateForm.elements["form_trade_price[1][2]"].readOnly = true;
	document.dateForm.elements["form_trade_price[2][1]"].readOnly = true;
	document.dateForm.elements["form_trade_price[2][2]"].readOnly = true;
	document.dateForm.elements["form_trade_price[3][1]"].readOnly = true;
	document.dateForm.elements["form_trade_price[3][2]"].readOnly = true;
	document.dateForm.elements["form_trade_price[4][1]"].readOnly = true;
	document.dateForm.elements["form_trade_price[4][2]"].readOnly = true;
	document.dateForm.elements["form_trade_price[5][1]"].readOnly = true;
	document.dateForm.elements["form_trade_price[5][2]"].readOnly = true;

}

DAIKO;

/*
//商品ダイアログ関数
function Open_SubWin_Plan(url, arr, x, y,display,select_id,shop_aid,place,head_flg)
{
	//ダイアログが指定されている場合は、倉庫ID or 棚卸調査ID が必要
	if((display == undefined && select_id == undefined) || (display != undefined && select_id != undefined)){

		//契約区分が通常以外は、本部の商品だけを表示
		if(head_flg != 1){
			//オンライン・オフライン代行
			rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,true);
		}else{
			//通常
			rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,false);
		}

		if(typeof(rtnarr) != "undefined"){
			for(i=0;i<arr.length;i++){
				dateForm.elements[arr[i]].value=rtnarr[i];
			}
		}

		//契約マスタの場合は画面のリンク先にsubmitする
		if(display==6 || display==7){
			var next = '#'+place;
            document.dateForm.action=next;
			document.dateForm.submit();
		}

	}

	return false;
}

DAIKO;
*/

//plan_data.inc の JS を追加
$java_sheet .= $plan_data_js;


//フォームループ数
$loop_num = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");

//エラーループ数1
$error_loop_num1 = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");

//エラーループ数2
$error_loop_num2 = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");

//エラーループ数3
$error_loop_num3 = array(0=>"0",1=>"1",2=>"2",3=>"3",4=>"4");

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
$page_header = Create_Header($page_title);


//print_array($_POST);


// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());
$smarty->assign('num',$foreach_num);
$smarty->assign('loop_num',$loop_num);
$smarty->assign('error_loop_num1',$error_loop_num1);
$smarty->assign('error_loop_num2',$error_loop_num2);
$smarty->assign('error_loop_num3',$error_loop_num3);
$smarty->assign('error_msg4',$error_msg4);
$smarty->assign('error_msg5',$error_msg5);
//$smarty->assign('error_msg6',$error_msg6);
//$smarty->assign('error_msg7',$error_msg7);
//$smarty->assign('error_msg8',$error_msg8);
//$smarty->assign('error_msg9',$error_msg9);
$smarty->assign('error_msg10',$error_msg10);
//$smarty->assign('error_msg11',$error_msg11);
//$smarty->assign('error_msg13',$error_msg13);

//その他の変数をassign
$smarty->assign('var',array(
	'html_header'   => "$html_header",
	'page_menu'     => "$page_menu",
	'page_header'   => "$page_header",
	'html_footer'   => "$html_footer",
	'java_sheet'    => "$java_sheet",
	'flg'           => "$flg",
	'last_day'      => "$last_day",
	'error_flg'     => $error_flg,
	'error_msg'     => "$error_msg",
	'error_msg2'    => "$error_msg2",
	'error_msg3'    => "$error_msg3",
	'error_msg12'   => "$error_msg12",
	'error_msg14'   => "$error_msg14",
	'error_msg15'   => "$error_msg15",
	'error_msg16'   => "$error_msg16",
	'ac_name'       => "$ac_name",
	'act_name'      => "$act_name",
	'act_amount'    => "$act_amount",
	'act_div'       => "$act_div",
	'return_flg'    => "$return_flg",
	'get_flg'       => "$get_flg",
	'client_id'     => "$client_id",
	'form_load'     => "$form_load",
	'ware_name'     => "$ware_name",
	'trade_error_flg' => "$trade_error_flg",
	'client_cd'     => "$client_cd ",          
	'client_name'   => "$client_name ",
	'money'         => "$money",
	'tax_money'     => "$tax_money",
	'total_money'   => "$total_money",
	'ord_no'        => "$ord_no",
	'contract_div'  => "$contract_div",
	'group_kind'    => "$group_kind",
	'round_form'    => "$round_form",
	'br_flg'        => "$br_flg",
	'err_mess_confirm_flg'  => "$err_mess_confirm_flg",
	'del_mess'      => "$del_mess",
	'concurrent_err_flg'    => $concurrent_err_flg,
    'ad_total_warn_mess'    => $ad_total_warn_mess,
    'warn_lump_change'      => $warn_lump_change,	//rev.1.3 予定巡回日警告メッセージ
));

//trade_error_flgは後で削除


#2009-09-17 hashimoto-y
$smarty->assign('toSmarty_discount_flg',$toSmarty_discount_flg);


//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));


//print_array($_POST);
//print_array($form->_errors);


?>
