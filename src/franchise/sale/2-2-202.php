<?php
/**
 *
 * 売上伝票（訂正）
 *
 * 1.0.0 (2006/08/30) 新規作成
 * 1.1.0 (2006/09/11) 確認画面用 freeze処理
 * 1.1.1 (2006/09/20) (kaji)
 *   ・月次更新、請求締処理より先の日付かチェック処理追加
 * 1.1.2 (2006/09/30) (kaji)
 *   ・請求日に請求締処理より先の日付入力された場合のエラーメッセージ変更
 * 1.1.3 (2006/11/16) (suzuki)
 *   ・構成品の子に単価が設定されていなかったら表示しないように修正
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.1.3 (2006/11/16)
 *
 */
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/10      03-015      kajioka-h   得意先名の余計なサニタイジングを取った
 *                  03-016      kajioka-h   紹介料が0のときは仕入を起さないように
 *  2006/11/11      03-026      kajioka-h   変更の直前に日次更新された場合にエラーにする処理を追加
 *                  03-025      kajioka-h   変更の直前に売上取消された場合にエラーにする処理を追加
 *                  03-051      kajioka-h   口座料の「円」「%」を灰色に、余計な<br>をなくす
 *  2006-11-29      scl_308-8-4 suzuki      代行伝票のときに一式が正常に表示されるように修正
 *  2006-12-10      03-090      suzuki      構成品の商品コード表示するように修正
 *  2006-12-11      03-068      suzuki      処理の途中で、確定取消されたらエラー表示するように修正
 *  2007/03/05      要望6-2     kajioka-h   消耗品、本体商品、紹介口座料を登録可能に
 *  2007/03/08      B0702-015   kajioka-h   商品を品名変更不可→変更可に変えた際に、フォームが品名変更可能にならないバグ修正
 *  2007/04/05      その他25    kajioka-h   紹介料を本部の仕入する場合のチェック処理追加
 *  2007/04/09      要望1-4     kajioka-h   紹介口座料の仕様変更
 *  2007/04/11      要望1-4     kajioka-h   紹介口座料のラジオボタンが契約マスタと合っていなかったのを修正
 *  2007/04/13      その他      kajioka-h   「配送日」→「予定巡回日」に表示変更
 *  2007/05/17      xx-xxx      kajioka-h   予定データ明細、予定データ訂正、売上伝票訂正、手書伝票でレイアウトを合わせた
 *  2007/06/05      xx-xxx      kajioka-h   予定手書で直送先を登録できるようにした件
 *  2007/06/16      その他14    kajioka-h   前受金
 *  2007/06/18      B0702-062   kajioka-h   紹介料ありで本部の月次更新日、仕入締日チェック漏れを修正
 *  2007-07-12                  fukuda      「支払締」を「仕入締」に変更
 *  2007/07/17      B0702-072   kajioka-h   紹介口座先ありで紹介口座料が発生しない場合にエラーになるバグ修正
 *  2007/08/01      B0702-075   kajioka-h   紹介口座料なしの売上を紹介口座料ありに変更した場合にエラーになるバグ修正
 *  2008/01/19                  watanabe-k  代行料固定額の金額抽出カラムをtrust_net_amount => act_request_amountに変更
 *  2009/09/26                  hashimoto-y 値引商品を選択した場合に赤字表示
 *  2009/12/22                  aoyama-n    税率をTaxRateクラスから取得
 *  2010/07/06                  watanabe-k  売上データへの登録クエリで、販売区分の値を''で囲うように修正
 *
 */

$page_title = "売上伝票";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
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


/****************************/
// 外部変数取得
/****************************/

//売上ID
if($_GET["sale_id"] == null){
    $sale_id = $_POST["hdn_sale_id"];
}else{
    $sale_id = $_GET["sale_id"];
}
//直リンクで遷移してきた場合には、TOPに飛ばす
Get_Id_Check2($sale_id);

//不正判定
Get_ID_Check3($sale_id);

$con_data["hdn_sale_id"] = $sale_id;
$shop_id    = $_SESSION["client_id"];  //ログインユーザID
$client_h_id = $_SESSION["client_id"];  //ログインユーザID
$staff_id   = $_SESSION["staff_id"];   //ログイン者ID

$group_kind = $_SESSION["group_kind"];  //グループ種別

$plan_sale_flg = true;                  //売上伝票（訂正）フラグ


#2009-12-22 aoyama-n
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);


//select作成用部品
//require_once(PATH."include/select_part.php");
/*
//作　成
$form->addElement("button","form_make_button","作　成","style=\"color: #ff0000;\" onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
//修　正
$form->addElement("button","form_revision_button","修　正","onClick=\"javascript:Referer('2-2-202.php')\"");
//発　行
$form->addElement("button","form_public_button","発　行","onClick=\"javascript:Referer('2-2-203.php')\"");
*/

//確定取消判定
$sql  = "SELECT confirm_flg FROM t_aorder_h WHERE aord_id = $sale_id;";
$result = Db_Query($db_con, $sql);
$check_data = Get_Data($result);
if($check_data[0][0] == "f"){
    $error_msg14 = "伝票が確定取消されている為、変更できません。";
    $check_error_flg = true;
    //売上確定一覧遷移ボタン
//    $form->addElement("button","disp_btn","O　K","onClick=\"location.href='".FC_DIR."sale/2-2-207.php'\"");
    $form->addElement("button","disp_btn","Ｏ　Ｋ", "onClick=\"location.href='2-2-207.php?search=1'\"");
}

//確定取消されてたら以降処理しない
if($check_error_flg == false){

    /****************************/
    // 日次更新済か
    /****************************/

    $error_flg = false;

    //GETにrenew_flgがある（明細表示）場合、または日次更新済の場合freezeするよ
    if($_GET["renew_flg"] == "true"){
        $renew_flg = true;
    }else{
        $sql  = "SELECT renew_flg FROM t_sale_h WHERE sale_id = $sale_id AND ";
        $sql .= ($group_kind == "2") ? "shop_id IN (".Rank_Sql().");" : "shop_id = $shop_id;";
        $result = Db_Query($db_con, $sql);
        
        if(pg_num_rows($result) == 0){
            if(isset($_POST["form_claim"]) == false){
                Get_ID_Check($result);
            }
            $renew_flg = true;
            $concurrent_err_flg = true;
        }else{
            $renew_flg = (pg_fetch_result($result, 0, "renew_flg") == "t") ? true : false;
            //日次更新されていて、変更ボタンが押されている場合はエラー表示
            if($renew_flg && $_POST["entry_flg"] == "true"){
                $slip_renew_mess = $h_mess[50];
                $concurrent_err_flg = true;
                $error_flg = true;
            }
        }
    }


    /****************************/
    // 自動で起きた代行料の売上か（FCのみ）
    /****************************/

    if($group_kind != "2"){
        $sql  = "SELECT COUNT(sale_id) FROM t_sale_h WHERE sale_id = $sale_id AND contract_div = '2' AND shop_id = $shop_id;";
        $result = Db_Query($db_con, $sql);
        $renew_flg = (pg_fetch_result($result, 0, 0) == 1) ? true : $renew_flg;
    }

    if($group_kind == "2"){
        //代行の場合は直営もフリーズ
        $sql  = "SELECT COUNT(sale_id) FROM t_sale_h WHERE sale_id = $sale_id AND contract_div != '1' AND shop_id IN (".Rank_Sql().");";
        $result = Db_Query($db_con, $sql);
        $renew_flg = (pg_fetch_result($result, 0, 0) == 1) ? true : $renew_flg;


        //直営で、紹介口座があり、紹介口座がFCで、その仕入が本部で日次更新されているとフリーズ
        $sql  = "SELECT \n";
        $sql .= "    t_sale_h.intro_account_id, \n";
        $sql .= "    t_client.client_div, \n";
        $sql .= "    t_buy_h.renew_flg \n";
        $sql .= "FROM \n";
        $sql .= "    t_sale_h \n";
        $sql .= "    LEFT JOIN t_client ON t_sale_h.intro_account_id = t_client.client_id \n";
        $sql .= "    LEFT JOIN t_buy_h ON t_sale_h.sale_id = t_buy_h.intro_sale_id \n";
        $sql .= "WHERE \n";
        $sql .= "    t_sale_h.sale_id = $sale_id \n";
        $sql .= ";";
        $result = Db_Query($db_con, $sql);

        if( pg_fetch_result($result, 0, "intro_account_id") != null && 
            pg_fetch_result($result, 0, "client_div") == "3" && 
            pg_fetch_result($result, 0, "renew_flg") == "t" 
        ){
            $renew_flg = true;
        }
    }

    //日次更新されている or 自動売上の場合はフリーザ
    if($renew_flg){
        $form->freeze();
    }



    //受注ID（仮に）
    //$aord_id = "hand_slip";

    /****************************/
    // 売上ヘッダ、データ取得
    /****************************/

    if(true){
    //if(isset($_POST["form_claim"]) == false || $concurrent_err_flg != true){
        //売上ヘッダ、売上担当者
        $sql  = "SELECT \n";
        $sql .= "    t_sale_h.sale_no, \n";         //0
        $sql .= "    t_sale_h.aord_id, \n";         //1
        $sql .= "    t_sale_h.sale_day, \n";        //2
        $sql .= "    t_sale_h.claim_day, \n";       //3
        //$sql .= "    t_trade.trade_name, \n";
        $sql .= "    t_sale_h.trade_id, \n";        //4
        $sql .= "    t_sale_h.client_id, \n";       //5
        $sql .= "    t_sale_h.c_shop_name, \n";     //6
        $sql .= "    t_sale_h.c_shop_name2, \n";    //7
        $sql .= "    t_sale_h.client_cd1, \n";      //8
        $sql .= "    t_sale_h.client_cd2, \n";      //9
        $sql .= "    t_sale_h.client_cname, \n";    //10
        $sql .= "    t_sale_h.claim_id, \n";        //11
        $sql .= "    t_sale_h.intro_account_id, \n";    //12
        $sql .= "    t_sale_h.intro_ac_name, \n";   //13
        $sql .= "    t_sale_h.ware_id, \n";         //14
        $sql .= "    t_sale_h.ware_name, \n";       //15
        $sql .= "    t_sale_staff0.staff_id AS staff_id0, \n";      //16
        $sql .= "    t_sale_staff0.staff_name AS staff_name0, \n";  //17
        $sql .= "    t_sale_staff0.sale_rate AS sale_rate0, \n";    //18
        $sql .= "    t_sale_staff1.staff_id AS staff_id1, \n";      //19
        $sql .= "    t_sale_staff1.staff_name AS staff_name1, \n";  //20
        $sql .= "    t_sale_staff1.sale_rate AS sale_rate1, \n";    //21
        $sql .= "    t_sale_staff2.staff_id AS staff_id2, \n";      //22
        $sql .= "    t_sale_staff2.staff_name AS staff_name2, \n";  //23
        $sql .= "    t_sale_staff2.sale_rate AS sale_rate2, \n";    //24
        $sql .= "    t_sale_staff3.staff_id AS staff_id3, \n";      //25
        $sql .= "    t_sale_staff3.staff_name AS staff_name3, \n";  //26
        $sql .= "    t_sale_staff3.sale_rate AS sale_rate3, \n";    //27
        $sql .= "    t_sale_h.cost_amount, \n";     //28
        $sql .= "    t_sale_h.net_amount, \n";      //29
        $sql .= "    t_sale_h.tax_amount, \n";      //30
        $sql .= "    t_sale_h.note, \n";            //31
        $sql .= "    t_sale_h.slip_flg, \n";        //32
        $sql .= "    t_sale_h.slip_out, \n";        //33
        $sql .= "    t_sale_h.act_id, \n";          //34
        $sql .= "    t_sale_h.intro_ac_price, \n";  //35
        $sql .= "    t_sale_h.intro_ac_rate, \n";   //36
        $sql .= "    t_sale_h.contract_div, \n";    //37
        $sql .= "    t_sale_h.intro_amount, \n";    //38
        $sql .= "    t_sale_h.act_request_flg, \n"; //39
        $sql .= "    t_sale_h.route, \n";           //40
        $sql .= "    t_sale_h.reason_cor, \n";      //41
        $sql .= "    t_client.coax, \n";            //42
        $sql .= "    t_sale_h.round_form, \n";      //43
        $sql .= "    t_sale_h.claim_div, \n";       //44
        $sql .= "    t_sale_h.shop_id, \n";         //45
        $sql .= "    t_sale_h.intro_ac_cd1, \n";    //46
        $sql .= "    t_sale_h.intro_ac_cd2, \n";    //47
        $sql .= "    t_sale_h.intro_ac_div, \n";    //48
        $sql .= "    t_sale_h.act_cd1, \n";         //49
        $sql .= "    t_sale_h.act_cd2, \n";         //50
        $sql .= "    t_sale_h.act_cname, \n";       //51
        $sql .= "    t_sale_h.act_div, \n";         //52
        $sql .= "    t_sale_h.act_request_price, \n";   //53
        $sql .= "    t_sale_h.act_request_rate, \n";    //54
        $sql .= "    t_sale_h.hand_plan_flg, \n";       //55 予定手書フラグ
        $sql .= "    t_sale_h.direct_id, \n";           //56 直送先ID
        $sql .= "    t_sale_h.advance_offset_totalamount \n";   //57 前受相殺額合計

        $sql .= "FROM \n";
        $sql .= "    t_sale_h \n";
        $sql .= "    INNER JOIN t_client ON t_sale_h.client_id = t_client.client_id \n";
        //$sql .= "    INNER JOIN t_trade ON t_sale_h.trade_id = t_trade.trade_id \n";
        $sql .= "    LEFT JOIN t_sale_staff AS t_sale_staff0 \n";
        $sql .= "        ON t_sale_h.sale_id = t_sale_staff0.sale_id \n";
        $sql .= "        AND t_sale_staff0.staff_div = '0' \n";
        $sql .= "    LEFT JOIN t_sale_staff AS t_sale_staff1 \n";
        $sql .= "        ON t_sale_h.sale_id = t_sale_staff1.sale_id \n";
        $sql .= "        AND t_sale_staff1.staff_div = '1' \n";
        $sql .= "    LEFT JOIN t_sale_staff AS t_sale_staff2 \n";
        $sql .= "        ON t_sale_h.sale_id = t_sale_staff2.sale_id \n";
        $sql .= "        AND t_sale_staff2.staff_div = '2' \n";
        $sql .= "    LEFT JOIN t_sale_staff AS t_sale_staff3 \n";
        $sql .= "        ON t_sale_h.sale_id = t_sale_staff3.sale_id \n";
        $sql .= "        AND t_sale_staff3.staff_div = '3' \n";
        $sql .= "WHERE \n";
        $sql .= "    t_sale_h.sale_id = $sale_id \n";
        $sql .= "    AND \n";
        if($_SESSION["group_kind"] == "2"){
            $sql .= "    t_sale_h.shop_id IN (".Rank_Sql().") \n";
        }else{
            $sql .= "    t_sale_h.shop_id = $shop_id \n";
        }
        $sql .= ";\n";
/*
echo "売上ヘッダ：";
print_array($sql);
*/

        $result = Db_Query($db_con, $sql);
        Get_Id_Check($result);
        if($result == false){
            exit();
        }

        $data_list = Get_Data($result, 2);
//print_array($data_list);

        $sale_no                                = $data_list[0][0];                              //伝票番号

        $deli_day                               = explode('-',$data_list[0][2]);                 //予定巡回日
        $con_data["form_delivery_day"]["y"]     = $deli_day[0];
        $con_data["form_delivery_day"]["m"]     = $deli_day[1];
        $con_data["form_delivery_day"]["d"]     = $deli_day[2];
        $req_day                                = explode('-',$data_list[0][3]);                 //請求日
        $con_data["form_request_day"]["y"]      = $req_day[0];
        $con_data["form_request_day"]["m"]      = $req_day[1];
        $con_data["form_request_day"]["d"]      = $req_day[2];
        $con_data["trade_aord"]                 = $data_list[0][4];                              //取引区分
        $data_list[0][40]                       = str_pad($data_list[0][40], 4, 0, STR_POS_LEFT);   //順路
        $con_data["form_route_load"][1]         = substr($data_list[0][40],0,2);
        $con_data["form_route_load"][2]         = substr($data_list[0][40],2,2);
        $client_id                              = $data_list[0][5];                             //得意先ID
        //$shop_name                              = $data_list[0][6];                             //ショップ名
        //$shop_name2                             = $data_list[0][7];                             //ショップ名2
        //$client_cd                              = $data_list[0][8]."-".$data_list[0][9];        //得意先CD
        $con_data["form_client"]["cd1"]         = $data_list[0][8];                             //得意先CD1
        $con_data["form_client"]["cd2"]         = $data_list[0][9];                             //得意先CD1
        $con_data["form_client"]["name"]        = $data_list[0][10];          //得意先名（略称）

        $con_data["form_claim"]                 = $data_list[0][11].",".$data_list[0][44];      //請求先ID、請求先区分

        $con_data["form_ac_id"]                 = $data_list[0][12];                            //紹介者ID
        $intro_account_id                       = $data_list[0][12];                            //口座料指定判定で使用

        //$con_data["form_ac_name"]               = $data_list[0][13];                            //紹介者名
        $intro_ac_div                           = $data_list[0][48];                            //紹介口座区分
        $con_data["intro_ac_div[]"]             = $intro_ac_div;                                //紹介口座区分
        $con_data["intro_ac_price"]             = $data_list[0][35];                            //紹介口座単価
        $con_data["intro_ac_rate"]              = $data_list[0][36];                            //紹介口座率

        //紹介者がFCか仕入先か判定
        if($intro_account_id != null){
            $sql = "SELECT client_div FROM t_client WHERE client_id = $intro_account_id;";
            $result = Db_Query($db_con, $sql);
            //仕入先の場合、紹介者CD1のみ
            if(pg_fetch_result($result, 0, "client_div") == "2"){
                $ac_name = $data_list[0][46]."<br>".htmlspecialchars($data_list[0][13]);
            }else{
                $ac_name = $data_list[0][46]."-".$data_list[0][47]."<br>".htmlspecialchars($data_list[0][13]);
            }
        //紹介者がない場合
        }else{
            $ac_name = "無し";
            $con_data2["intro_ac_div[]"] = 1;
        }


        //$con_data["form_ware_id"]               = $data_list[0][14];                            //出荷倉庫ID
        //$con_data["form_ware_name"]             = $data_list[0][15];                            //出荷倉庫名
        $ware_name                              = htmlspecialchars($data_list[0][15]);           //出荷倉庫名

        $con_data["form_c_staff_id1"]           = $data_list[0][16];                             //担当者１
        $con_data["form_sale_rate1"]            = $data_list[0][18];                             //売上率１
        $con_data["form_c_staff_id2"]           = $data_list[0][19];                             //担当者２
        $con_data["form_sale_rate2"]            = $data_list[0][21];                             //売上率２
        $con_data["form_c_staff_id3"]           = $data_list[0][22];                             //担当者３
        $con_data["form_sale_rate3"]            = $data_list[0][24];                             //売上率３
        $con_data["form_c_staff_id4"]           = $data_list[0][25];                             //担当者４
        $con_data["form_sale_rate4"]            = $data_list[0][27];                             //売上率４

        $money                                  = $data_list[0][29];                             //売上金額（税抜）
        $tax_money                              = $data_list[0][30];                             //消費税
        $total_money                            = $money + $tax_money;                           //伝票合計
        //$money = number_format($money);
        //$tax_money = number_format($tax_money);
        //$total_money = number_format($total_money);
        $con_data["form_sale_total"]            = number_format($money);
        $con_data["form_sale_tax"]              = number_format($tax_money);
        $con_data["form_sale_money"]            = number_format($total_money);


        //照会画面（日次更新済、明細表示等）ではこの伝票で発生した前受金は残高に含めない
        if($renew_flg){
            $ad_rest_price                      = Advance_Offset_Claim($db_con, $data_list[0][2], $data_list[0][5], $data_list[0][44]);
        }else{
            $ad_rest_price                      = Advance_Offset_Claim($db_con, $data_list[0][2], $data_list[0][5], $data_list[0][44], $sale_id);
        }
        $con_data["form_ad_rest_price"]         = number_format($ad_rest_price);
        $con_data["form_ad_offset_total"]       = ($data_list[0][57] != null) ? number_format($data_list[0][57]) : "";      //前受相殺額合計

        $con_data["form_note"]                  = $data_list[0][31];                             //備考
        $con_data["form_reason"]                = $data_list[0][41];                             //訂正理由

        //得意先IDをhiddenにより保持する
        if($client_id != NULL){
            $con_data["hdn_client_id"] = $client_id;
        }else{
            $client_id = $_POST["hdn_client_id"];
        }

        //代行依頼料指定判定
        if($data_list[0][22] != NULL){
            $trust_rate                             = $data_list[0][22];
        }else{
            $trust_rate                             = '0';
        }
        $con_data2["hdn_trust_rate"]            = $trust_rate;                                   //hiddenに保持

        $contract_div                           = $data_list[0][37];                             //契約区分
        $con_data2["hdn_contract_div"]          = $contract_div;                                 //hiddenに保持

/*
        //直営側から代行伝票を訂正する場合、営業原価は入力不可
        if($contract_div != '1' && $group_kind != 3){
            $form_load = "onLoad=\"daiko_checked();\"";
        }
*/
        $act_id                                 = $data_list[0][24];                                //代行先ID
        if($contract_div != "1" && $group_kind == "2"){
            $con_data["form_act_cd"]            = $data_list[0][49]." - ".$data_list[0][50];        //代行先CD
            $con_data["form_act_name"]          = $data_list[0][51];                                //代行先名（略称）

#watanabe-k
#            $sql = "SELECT trust_net_amount FROM t_aorder_h WHERE aord_id = ".$data_list[0][1].";";
            $sql = "SELECT act_request_price, trust_net_amount FROM t_aorder_h WHERE aord_id = ".$data_list[0][1].";";
            $result = Db_Query($db_con, $sql);

            //委託料
            if($data_list[0][52] == "1"){
                $con_data["form_act_price"] = "発生しない";
            }elseif($data_list[0][52] == "2"){
                $con_data["form_act_price"] = number_format(pg_fetch_result($result, 0, 0))."（固定額）";
            }elseif($data_list[0][52] == "3"){
                $con_data["form_act_price"] = number_format(pg_fetch_result($result, 0, 1))."（売上の".$data_list[0][54]."％）";
            }
        }

        $coax                                   = $data_list[0][42];                                //まるめ区分

        $con_data["hdn_h_intro_ac_price"]       = $data_list[0][35];                                //紹介料（単価）（ヘッダ）
        $con_data["hdn_h_intro_ac_rate"]        = $data_list[0][36];                                //紹介料（率）（ヘッダ）
        $round_form                             = $data_list[0][43];                                //巡回日（文字）

        $shop_id                                = $data_list[0][45];                                //担当支店

        $hand_plan_flg                          = ($data_list[0][55] == "t") ? true : false;        //予定手書フラグ
        $con_data["form_direct_select"]         = $data_list[0][56];                                //直送先ID


        //----------------//
        // 売上データ取得
        //----------------//
        $sql  = "SELECT \n";
        $sql .= "    t_sale_d.sale_d_id, \n";           //0
        $sql .= "    t_sale_d.sale_id, \n";             //1
        $sql .= "    t_sale_d.line, \n";                //2
        $sql .= "    t_sale_d.sale_div_cd, \n";         //3
        $sql .= "    t_sale_d.serv_print_flg, \n";      //4
        $sql .= "    t_sale_d.serv_id, \n";             //5
        $sql .= "    t_sale_d.serv_cd, \n";             //6
        $sql .= "    t_sale_d.serv_name, \n";           //7
        $sql .= "    t_sale_d.set_flg, \n";             //8
        $sql .= "    t_sale_d.goods_print_flg, \n";     //9
        $sql .= "    t_sale_d.goods_id, \n";            //10
        $sql .= "    t_sale_d.goods_cd, \n";            //11
        //$sql .= "    t_sale_d.g_product_name, \n";
        $sql .= "    t_sale_d.goods_name, \n";          //12
        $sql .= "    t_item.name_change AS goods_name_change, \n";  //13
        $sql .= "    t_sale_d.num, \n";                 //14
        $sql .= "    t_sale_d.unit, \n";                //15
        $sql .= "    t_sale_d.tax_div, \n";             //16
        $sql .= "    t_sale_d.buy_price, \n";           //17
        $sql .= "    t_sale_d.cost_price, \n";          //18
        $sql .= "    t_sale_d.sale_price, \n";          //19
        $sql .= "    t_sale_d.buy_amount, \n";          //20
        $sql .= "    t_sale_d.cost_amount, \n";         //21
        $sql .= "    t_sale_d.sale_amount, \n";         //22
        $sql .= "    t_sale_d.rgoods_id, \n";           //23
        $sql .= "    t_sale_d.rgoods_cd, \n";           //24
        $sql .= "    t_sale_d.rgoods_name, \n";         //25
        $sql .= "    t_body.name_change AS rgoods_name_change, \n"; //26
        $sql .= "    t_sale_d.rgoods_num, \n";          //27
        $sql .= "    t_sale_d.egoods_id, \n";           //28
        $sql .= "    t_sale_d.egoods_cd, \n";           //29
        $sql .= "    t_sale_d.egoods_name, \n";         //30
        $sql .= "    t_expend.name_change AS egoods_name_change, \n";   //31
        $sql .= "    t_sale_d.egoods_num, \n";          //32
        $sql .= "    t_sale_d.aord_d_id, \n";           //33
        $sql .= "    t_sale_d.contract_id, \n";         //34
        $sql .= "    t_sale_d.account_price, \n";       //35
        $sql .= "    t_sale_d.account_rate, \n";        //36
        $sql .= "    t_sale_d.official_goods_name, \n"; //37
        $sql .= "    t_sale_d.advance_flg, \n";         //38
        #2009-09-26 hashimoto-y
        #$sql .= "    t_sale_d.advance_offset_amount \n";//39
        $sql .= "    t_sale_d.advance_offset_amount, \n";//39
        $sql .= "    t_item.discount_flg  \n";//40

        $sql .= "FROM \n";
        $sql .= "    t_sale_d \n";
        $sql .= "    LEFT JOIN t_goods AS t_item ON t_item.goods_id = t_sale_d.goods_id \n";
        $sql .= "    LEFT JOIN t_goods AS t_body ON t_body.goods_id = t_sale_d.rgoods_id \n";
        $sql .= "    LEFT JOIN t_goods AS t_expend ON t_expend.goods_id = t_sale_d.egoods_id \n";
        $sql .= "WHERE \n";
        $sql .= "    t_sale_d.sale_id = $sale_id \n";
        $sql .= "ORDER BY \n";
        $sql .= "    t_sale_d.line \n";
        $sql .= ";";
/*
echo "売上データ：";
print_array($sql);
*/

        $result = Db_Query($db_con, $sql);
        if($result == false){
            exit();
        }
        $sale_d_count = pg_num_rows($result);

        $sub_data = Get_Data($result, 2);
//print_array($sub_data);


        //受注IDに該当するデータが存在するか
        for($s=0;$s<$sale_d_count;$s++){
            $search_line = $sub_data[$s][2];        //行番号
            //口座区分の初期値に設定しない行配列
            $aprice_array[] = $search_line;

            $sale_d_id = $sub_data[$s][0];          //売上データID
            $con_data["hdn_aord_d_id"][$search_line]                = $sub_data[$s][0]; //売上データID

            $con_data["form_divide"][$search_line]                  = $sub_data[$s][3];   //販売区分

            $con_data["form_print_flg1"][$search_line] = ($sub_data[$s][4] == 't') ? "1" : "";  //サービス印字フラグ
            $con_data["form_serv"][$search_line]                    = $sub_data[$s][5];    //サービス

            $con_data["form_print_flg2"][$search_line] = ($sub_data[$s][9] == 't') ? "1" : "";  //アイテム伝票印字フラグ
            $con_data["hdn_goods_id1"][$search_line]                = $sub_data[$s][10];  //アイテムID
            $con_data["form_goods_cd1"][$search_line]               = $sub_data[$s][11];  //アイテムCD
            $con_data["hdn_name_change1"][$search_line]             = $sub_data[$s][13];  //アイテム品名変更フラグ
            $hdn_name_change[1][$search_line]                       = $sub_data[$s][13];  //POSTする前にアイテム名の変更不可判定を行なう為
            $con_data["form_goods_name1"][$search_line]             = $sub_data[$s][12];  //アイテム名（略称）
            $con_data["form_goods_num1"][$search_line]              = $sub_data[$s][14];  //アイテム数
            $con_data["official_goods_name"][$search_line]          = $sub_data[$s][37];  //アイテム名（正式）

            //契約区分が代行のみ形式変更
            if($contract_div != '1'){
                $con_data["form_issiki"][$search_line] = ($sub_data[$s][8] == 't') ? "一式" : "";   //一式フラグ
            }else{
                $con_data["form_issiki"][$search_line] = ($sub_data[$s][8] == 't') ? "1" : "";      //一式フラグ
            }

            $cost_price = explode('.', $sub_data[$s][18]);                                //営業原価
            $con_data["form_trade_price"][$search_line]["1"] = $cost_price[0];
            $con_data["form_trade_price"][$search_line]["2"] = ($cost_price[1] != null) ? $cost_price[1] : '00';

            $sale_price = explode('.', $sub_data[$s][19]);                                //売上単価
            $con_data["form_sale_price"][$search_line]["1"] = $sale_price[0];
            $con_data["form_sale_price"][$search_line]["2"] = ($sale_price[1] != null) ? $sale_price[1] : '00';

            $con_data["form_trade_amount"][$search_line]    = number_format($sub_data[$s][21]);  //営業金額
            $con_data["form_sale_amount"][$search_line]     = number_format($sub_data[$s][22]);  //売上金額

            $con_data["hdn_goods_id2"][$search_line]                = $sub_data[$s][23];    //本体ID
            $con_data["form_goods_cd2"][$search_line]               = $sub_data[$s][24];    //本体CD
            $con_data["hdn_name_change2"][$search_line]             = $sub_data[$s][26];    //本体品名変更フラグ
            $hdn_name_change[2][$search_line]                       = $sub_data[$s][26];    //POSTする前に本体名の変更不可判定を行なう為
            $con_data["form_goods_name2"][$search_line]             = $sub_data[$s][25];    //本体名
            $con_data["form_goods_num2"][$search_line]              = $sub_data[$s][27];    //本体数

            $con_data["hdn_goods_id3"][$search_line]                = $sub_data[$s][28];    //消耗品ID
            $con_data["form_goods_cd3"][$search_line]               = $sub_data[$s][29];    //消耗品CD
            $con_data["hdn_name_change3"][$search_line]             = $sub_data[$s][31];    //消耗品品名変更フラグ
            $hdn_name_change[3][$search_line]                       = $sub_data[$s][31];    //POSTする前に消耗品名の変更不可判定を行なう為
            $con_data["form_goods_name3"][$search_line]             = $sub_data[$s][30];    //消耗品名
            $con_data["form_goods_num3"][$search_line]              = $sub_data[$s][32];    //消耗品数

            //商品単位
            if($sub_data[$s][35] != NULL){
                //円
                $con_data["form_account_price"][$search_line]       = $sub_data[$s][35];    //口座単位
                $con_data["form_aprice_div[$search_line]"] = 2;
            }else if($sub_data[$s][36] != NULL){
                //率
                $con_data["form_account_rate"][$search_line]        = $sub_data[$s][36];    //口座率
                $con_data["form_aprice_div[$search_line]"] = 3;
            }else{
                //なし
                $con_data["form_aprice_div[$search_line]"] = 1;
            }

            $con_data["form_ad_offset_radio"][$search_line]         = $sub_data[$s][38];    //前受相殺フラグ
            $con_data["form_ad_offset_amount"][$search_line]        = $sub_data[$s][39];    //前受相殺額

            #2009-09-26 hashimoto-y
            $con_data["hdn_discount_flg"][$search_line]             = $sub_data[$s][40];    //値引きフラグ

            $sub_data[$s][27] = $sale_d_id;
            //$arr_sale_d_id[$search_line] = $sale_d_id;

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
*/
/*
            //受注IDに該当する受注内容データが存在するか
            //for($c=0;$c<count($id_data);$c++){
            $sql  = "SELECT ";
            $sql .= "    t_sale_detail.line,";          //行0
            $sql .= "    t_sale_detail.goods_id,";      //商品ID1
            $sql .= "    t_sale_detail.goods_cd,";      //商品CD2
            $sql .= "    t_goods.name_change,";           //品名変更3
            $sql .= "    t_sale_detail.goods_name,";    //商品名4
            $sql .= "    t_sale_detail.num,";           //数量5
            //営業原価判定
            //if($contract_div != '1' && $group_kind == 3){
            //    //ＦＣ側で代行を訂正した場合
            //    $sql .= "    t_sale_detail.trust_trade_price,";  //営業原価(受託先)
            //}else{
            //    //通常伝票
                $sql .= "    t_sale_detail.trade_price,";        //営業原価6
            //}
            $sql .= "    t_sale_detail.sale_price,";    //売上単価7
            //営業金額判定
            //if($contract_div != '1' && $group_kind == 3){
            //    //ＦＣ側で代行を訂正した場合
            //    $sql .= "    t_sale_detail.trust_trade_amount,"; //営業金額(受託先)
            //}else{
            //    //通常伝票
                $sql .= "    t_sale_detail.trade_amount,";       //営業金額8
            //}
            $sql .= "    t_sale_detail.sale_amount ";   //売上金額9
            $sql .= "FROM ";
            $sql .= "    t_sale_detail ";
            $sql .= "    INNER JOIN t_goods ON t_goods.goods_id = t_sale_detail.goods_id ";
            $sql .= "WHERE ";
            $sql .= "    t_sale_detail.sale_d_id = ".$sale_d_id.";";
            $result = Db_Query($db_con, $sql);
            if(pg_num_rows($result) != 0){
                $arr_sale_d_id[$search_line] = $sale_d_id;
            }
            $detail_data = Get_Data($result, 2);

            //受注登録の行番号
            //$search_row = $id_data[$c][1];
            $search_row = $search_line;

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

                $s_price = explode('.', $detail_data[$d][7]);
                $con_data["break_sale_price"][$search_row][$search_line2]["1"] = $s_price[0];     //売上単価
                $con_data["break_sale_price"][$search_row][$search_line2]["2"] = ($s_price[1] != null)? $s_price[1] : '00';

                $con_data["break_trade_amount"][$search_row][$search_line2] = number_format($detail_data[$d][8]); //営業金額
                $con_data["break_sale_amount"][$search_row][$search_line2]  = number_format($detail_data[$d][9]); //売上金額
            }
//    }
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
                $con_data["form_ad_offset_radio[$a]"] = 1;
            }
        }


        $form->setDefaults($con_data);

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
        $sql  = " SELECT";
        $sql .= "     t_goods.goods_id,";                      //商品ID
        $sql .= "     t_goods.name_change,";                   //品名変更フラグ
        $sql .= "     t_goods.goods_cd,";                      //商品コード
        $sql .= "     t_goods.goods_cname,";                   //略称
        $sql .= "     initial_cost.r_price AS initial_price,"; //営業単価
        $sql .= "     sale_price.r_price AS sale_price, ";     //売上単価
        $sql .= "     t_goods.compose_flg, ";                  //構成品フラグ
        $sql .= "     CASE \n";
        $sql .= "         WHEN t_g_product.g_product_name IS NULL THEN t_goods.goods_name \n";
        $sql .= "         ELSE t_g_product.g_product_name || ' ' || t_goods.goods_name \n";
        #2009-09-26 hashimoto-y
        #$sql .= "     END \n";                    //商品分類名＋半スペ＋商品名 7
        $sql .= "     END, \n";                                 //商品分類名＋半スペ＋商品名 7
        $sql .= "     t_goods.discount_flg \n";                //値引フラグ

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
        if($_SESSION[group_kind] == "2"){
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
            $sql .= "     t_goods.goods_id,";                      //商品ID
            $sql .= "     t_goods.name_change,";                   //品名変更フラグ
            $sql .= "     t_goods.goods_cd,";                      //商品コード
            $sql .= "     t_goods.goods_cname,";                   //略称
            $sql .= "     NULL,";
            $sql .= "     NULL,";
            $sql .= "     t_goods.compose_flg, ";                  //構成品フラグ
            $sql .= "     CASE \n";
            $sql .= "         WHEN t_g_product.g_product_name IS NULL THEN t_goods.goods_name \n";
            $sql .= "         ELSE t_g_product.g_product_name || ' ' || t_goods.goods_name \n";
            #2009-09-26 hashimoto-y
            #$sql .= "     END \n";                    //商品分類名＋半スペ＋商品名 7
            $sql .= "     END, \n";                    //商品分類名＋半スペ＋商品名 7
            $sql .= "     NULL ";
            $sql .= " FROM";
            $sql .= "     t_goods ";
            $sql .= "     LEFT JOIN t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
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

            //一列目の商品だけ計算する
            if($search_line == 1){

                #2009-09-26 hashimoto-y
                $con_data2["hdn_discount_flg"][$search_row]          = $goods_data[8];   //値引きフラグ

                //構成品判定
                if($goods_data[6] == 'f'){
                    //構成品ではない

                    //原価単価を整数部と少数部に分ける
                    $cost_price = explode('.', $goods_data[4]);
                    $con_data2["form_trade_price"][$search_row]["1"] = $cost_price[0];  //営業単価
                    $con_data2["form_trade_price"][$search_row]["2"] = ($cost_price[1] != null)? $cost_price[1] : '00';     

                    //売上単価を整数部と少数部に分ける
                    $sale_price = explode('.', $goods_data[5]);
                    $con_data2["form_sale_price"][$search_row]["1"] = $sale_price[0];  //売上単価
                    $con_data2["form_sale_price"][$search_row]["2"] = ($sale_price[1] != null)? $sale_price[1] : '00';

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
                        $sql .= " AND  \n";
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
                    $con_data2["form_trade_amount"][$search_row]    = number_format($cost_amount);
                    $con_data2["form_sale_amount"][$search_row]     = number_format($sale_amount);
                }
                $con_data2["official_goods_name"][$search_row]  = $goods_data[7];   //商品名（正式）

            }else{
                //二・三列目の商品
                
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
                    if($_SESSION[group_kind] == "2"){
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
            }
            #2009-09-26 hashimoto-y
            $con_data2["hdn_discount_flg"][$search_row]  = "";
        }
        $con_data2["goods_search_row"]                  = "";

    }


    /****************************/
    //クリアボタン、クリアリンク押下処理
    /****************************/
    //if($_POST["clear_flg"] == true){
    if($_POST["clear_flg"] == true || $_POST["clear_line"] != ""){

        if($_POST["clear_line"] != ""){
            $min = $_POST["clear_line"];
            $max = $_POST["clear_line"];
        }else{
            $min = 1;
            $max = 5;
        }

        //商品欄を全て初期化
        for($c=$min;$c<=$max;$c++){

            for($f=1;$f<=3;$f++){
                $con_data2["form_print_flg".$f][$c]      = "";
                $con_data2["hdn_goods_id".$f][$c]        = "";
                $con_data2["hdn_name_change".$f][$c]     = "";
                $con_data2["form_goods_cd".$f][$c]       = "";
                $con_data2["form_goods_name".$f][$c]     = "";
                $con_data2["form_goods_num".$f][$c]      = "";
            }
            $con_data2["form_serv"][$c]             = "";
            $con_data2["form_divide"][$c]           = "";
            $con_data2["form_issiki"][$c]           = "";
            $con_data2["official_goods_name"][$c]   = "";
            $con_data2["form_trade_price"][$c]["1"] = "";
            $con_data2["form_trade_price"][$c]["2"] = "";
            $con_data2["form_trade_amount"][$c]     = "";
            $con_data2["form_sale_price"][$c]["1"]  = "";
            $con_data2["form_sale_price"][$c]["2"]  = "";
            $con_data2["form_sale_amount"][$c]      = "";
            $con_data2["form_account_price"][$c]    = "";
            $con_data2["form_account_rate"][$c]     = "";
            $con_data2["form_aprice_div[$c]"]       = 1;
            $con_data2["form_ad_offset_radio[$c]"]  = 1;
            $con_data2["form_ad_offset_amount[$c]"] = "";

            #2009-09-26 hashimoto-y
            $con_data2["hdn_discount_flg"][$c]             = "";

/*
            for($j=1;$j<=5;$j++){
                $con_data2["break_goods_cd"][$c][$j] = "";
                $con_data2["break_goods_name"][$c][$j] = "";
                $con_data2["break_goods_num"][$c][$j] = "";
                $con_data2["hdn_bgoods_id"][$c][$j] = "";
                $con_data2["hdn_bname_change"][$c][$j] = "";
                $con_data2["break_trade_price"][$c][$j][1] = "";
                $con_data2["break_trade_price"][$c][$j][2] = "";
                $con_data2["break_trade_amount"][$c][$j] = "";
                $con_data2["break_sale_price"][$c][$j][1] = "";
                $con_data2["break_sale_price"][$c][$j][2] = "";
                $con_data2["break_sale_amount"][$c][$j] = "";
            }
*/
        }
        
        $post_flg2 = true;               //口座区分を、初期化するフラグ
        $con_data2["clear_flg"] = "";    //クリアボタン押下フラグ
        $con_data2["clear_line"] = "";   //クリアリンク行番号
    }



    /****************************/
    // フォームパーツ定義
    /****************************/

    #2009-09-26 hashimoto-y
    #plan_data.incに売上データ訂正のフラグを渡す
    $plan_edit_flg = 't';
    $plan_edit_goods_data = $con_data2;


    //受注マスタ用部品(全フォームを定義)
    require_once(INCLUDE_DIR."plan_data.inc");

    //if($flg == 'add'){

    //顧客名
    $form_client[] =& $form->createElement(
                "text","cd1","",
                "size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
                 onChange=\"javascript:Change_Submit('client_search_flg','#','true','form_client[cd2]')\"
                 onkeyup=\"changeText(this.form,'form_client[cd1]','form_client[cd2]',6)\"".$g_form_option."\""
                );
    $form_client[] =& $form->createElement("static","","","-");
    $form_client[] =& $form->createElement(
                "text","cd2","",
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onChange=\"javascript:Button_Submit('client_search_flg','#','true')\"".$g_form_option."\""
                );
    $form_client[] =& $form->createElement(
                "text","name","",
                "size=\"34\" $g_text_readonly"
                );
    $freeze_form_client = $form->addGroup($form_client, "form_client", "");
    $freeze_form_client->freeze();


    //登録ボタン
    $form->addElement("button","entry_button","登　録","onClick=\"return Dialogue_2('登録します。','".$_SERVER["PHP_SELF"]."',true,'entry_flg');\" $disabled");

    //戻るボタン
    if($renew_flg){
        $form->addElement("button","form_back_btn","戻　る", "onClick=\"location.href='".Make_Rtn_Page("sale")."'\"");
    }else{
        $form->addElement("button","form_back_btn","戻　る", "onClick=\"location.href='2-2-207.php?search=1'\"");
    }

    //ＯＫボタン
    $form->addElement("button","form_ok_btn","Ｏ　Ｋ", "onClick=\"location.href='2-2-207.php?search=1'\"");


    //直営側の代行伝票の場合、代行者を表示
    if($group_kind == "2" && $contract_div != "1"){
        $form->addElement("text", "form_act_cd");
        $form->addElement("text", "form_act_name");
        $form->addElement("text", "form_act_price");

        $freeze_data = array("form_act_cd", "form_act_name", "form_act_price");
        $form->freeze($freeze_data);
    }


    /****************************/
    //hiddenで予定訂正の部品定義
    /****************************/

    //hidden
    $form->addElement("hidden", "hdn_sale_id");           //売上ID
    $form->addElement("hidden", "entry_flg");           //登録ボタン押下フラグ
    //$form->addElement("hidden", "client_search_flg");   //得意先検索フラグ

    //$form->addElement("hidden", "hdn_page_title", $page_title); //ページタイトル

    $form->addElement("hidden", "form_ac_id");           //紹介者ID
    $form->addElement("hidden", "hdn_h_intro_ac_price");           //紹介料（単価）（ヘッダ）
    $form->addElement("hidden", "hdn_h_intro_ac_rate");           //紹介料（率）（ヘッダ）


    /****************************/
    //得意先の情報を抽出
    /****************************/
    $sql  = "SELECT";
    $sql .= "   t_client.coax, ";
    $sql .= "   t_client.trade_id,";
    $sql .= "   t_client.tax_franct,";
    $sql .= "   t_client.slip_out ";
    //$sql .= "   t_client.intro_ac_price,";
    //$sql .= "   t_client.intro_ac_rate ";
    $sql .= " FROM";
    $sql .= "   t_client ";
    $sql .= " WHERE";
    $sql .= "   client_id = $client_id";
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    Get_Id_Check($result);
    $data_list = Get_Data($result);

    $coax           = $data_list[0][0];        //丸め区分（商品）
    //$trade_id       = $data_list[0][1];        //取引コード
    $tax_franct     = $data_list[0][2];        //消費税（端数区分）
    //$slip_out       = $data_list[0][3];        //伝票形式
    //$intro_ac_price = $data_list[0][4];        //紹介口座料
    //$intro_ac_rate  = $data_list[0][5];        //紹介口座率


    /****************************/
    //ログインユーザ情報取得処理
    /****************************/
    $sql  = "SELECT";
    $sql .= "   t_ware.ware_name,";
    $sql .= "   t_ware.ware_id,";
    #2009-12-22 aoyama-n
    #$sql .= "   t_client.tax_rate_n,";
    $sql .= "   NULL,";
    $sql .= "   t_client.rank_cd ";
    $sql .= " FROM";
    $sql .= "   t_client LEFT JOIN t_ware ON t_client.ware_id = t_ware.ware_id ";
    $sql .= " WHERE";
    $sql .= "   client_id = $client_h_id";
    $sql .= ";";
    //echo "ログインユーザ情報取得：$sql<br>";
    $result = Db_Query($db_con, $sql); 
    Get_Id_Check($result);
    $data_list = Get_Data($result);

    //$ware_name      = $data_list[0][0];        //商品出荷倉庫名
    //$ware_id        = $data_list[0][1];        //出荷倉庫ID
    #2009-12-22 aoyama-n
    #$tax_num        = $data_list[0][2];        //消費税(現在)
    $rank_cd        = $data_list[0][3];        //顧客区分CD



    /****************************/
    //前受金集計ボタン押下処理
    /****************************/
    //if($_POST["ad_sum_button_flg"] == true || $_POST["correction_flg"] == "true"){
    if($_POST["ad_sum_button_flg"] == true || $_POST["correction_flg"] == "true" || $_POST["entry_flg"] == true || $_POST["warn_ad_flg"] != null){

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

            $ad_rest_price  = Advance_Offset_Claim($db_con, $count_day, $client_id, $c_data[1], $sale_id);
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
    //登録ボタン押下
    /****************************/
    if($_POST["entry_flg"] == true || $_POST["warn_ad_flg"] != null){

        //紹介口座IDとその取引先区分を取得
        if($_POST["form_ac_id"] != null){
            $sql  = "SELECT \n";
            $sql .= "    client_div \n";
            $sql .= "FROM \n";
            $sql .= "    t_client \n";
            $sql .= "WHERE \n";
            $sql .= "    client_id = ".$_POST["form_ac_id"]." \n";
            $sql .= ";";
            $result = Db_Query($db_con, $sql);
            $intro_account_id = $_POST["form_ac_id"];                       //紹介口座先ID
            $intro_client_div = pg_fetch_result($result, 0, "client_div");  //紹介口座先の取引先区分
        }else{
            $intro_account_id = null;       //紹介口座先ID
            $intro_client_div = null;       //紹介口座先の取引先区分
        }


        //本部のclient_idを取得
        $sql = "SELECT client_id FROM t_client WHERE rank_cd = (SELECT rank_cd FROM t_rank WHERE group_kind = '1');";
        $result = Db_Query($db_con, $sql);
        $head_id = pg_fetch_result($result, 0, "client_id");        //本部のclient_id


        /****************************/
        //エラーチェック(PHP)、不正値判定関数など
        /****************************/
        require_once(INCLUDE_DIR."fc_sale_post_atr.inc");


        //売上削除（取消）されていない場合、本部で紹介料の仕入が日次更新されていないかチェック
        if($error_msg14 == null){
            //直営の場合、紹介料仕入が本部で日次更新されているとエラー
            if($intro_account_id != null && $group_kind == "2"){
                $sql = "SELECT renew_flg FROM t_buy_h WHERE intro_sale_id = $sale_id;";
                $result = Db_Query($db_con, $sql);
                if(pg_num_rows($result) != 0){
                    if(pg_fetch_result($result, 0, 0) == "t"){
                        $error_flg = true;
                        $buy_err_mess = "紹介口座料";

                        //OKボタン
                        $form->addElement("button","disp_btn","Ｏ　Ｋ", "onClick=\"location.href='2-2-207.php?search=1'\"");
                    }
                }
            }
        }


        $con_data2["entry_flg"] = "";   //登録ボタン押下フラグ初期化


        #2009-09-26 hashimoto-y
        //●値引商品選択時の取引区分チェック（値引・返品は使用不可）
        #$trade_sale = $_POST["trade_aord"];
        #if(($trade_sale == '13' || $trade_sale == '14' || $trade_sale == '63' || $trade_sale == '64') && (in_array('t', $_POST[hdn_discount_flg]))){
        #    $form->setElementError("trade_sale_select", $h_mess[79]);
        #}


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

        #2009-12-22 aoyama-n
        $tax_rate_obj->setTaxRateDay($_POST["form_delivery_day"]["y"]."-".$_POST["form_delivery_day"]["m"]."-".$_POST["form_delivery_day"]["d"]);
        $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

        //営業金額の合計処理
        $total_money = Total_Amount($trade_amount2, $goods_item_tax_div2, $coax, $tax_franct, $tax_num, $client_id, $db_con);
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

                // 無視用ボタン
                $form->addElement("button", "form_ad_warn", "警告を無視して訂正",
                    "onClick=\"javascript:Button_Submit('warn_ad_flg', '".$_SERVER["REQUEST_URI"]."', true);\" $disabled"
                );
                $form->addElement("hidden", "warn_ad_flg");

            }

            //「掛売上」の場合
            if($trade_aord == "11"){
                //前受相殺額合計が現在の前受金残高より大きい場合はエラー
                if($ad_offset_total_amount > $ad_rest_price){
                    $form->setElementError("form_ad_offset_total", $h_mess[75]);
                }
            }

        }//前受金額チェックおわり


        $form->validate();
        $error_flg = (count($form->_errors) > 0) ? true : $error_flg;

        //エラーの場合はこれ以降の表示処理を行なわない
        //if($form->validate() && $error_flg == false){
        if($error_flg == false && $ad_total_warn_mess == null){

            Db_Query($db_con, "BEGIN;");


            //売上ヘッダ更新
            $sql  = "UPDATE \n";
            $sql .= "    t_sale_h \n";
            $sql .= "SET \n";
            $sql .= "    sale_day = '$delivery_day', \n";
            $sql .= "    claim_day = '$request_day', \n";
            $sql .= "    trade_id = $trade_aord, \n";
            $sql .= "    claim_id= $claim_id, \n";
            $sql .= "    claim_div = '$claim_div', \n";
            $sql .= "    cost_amount = $cost_money, \n";
            $sql .= "    net_amount = $sale_money, \n";
            $sql .= "    tax_amount = $sale_tax, \n";
            $sql .= "    note = '$note', \n";
            $sql .= "    reason_cor = '$reason', \n";
            $sql .= "    route = $route, \n";
            if($ad_offset_flg == true){                     //前受相殺額合計
                $sql .= "    advance_offset_totalamount = $ad_offset_total_amount, ";
            }else{
                $sql .= "    advance_offset_totalamount = null, ";
            }
            //直送先が指定されている場合
            if($direct_id != null){
                $sql .= "    direct_id = $direct_id, ";
                $sql .= "    direct_cd = (SELECT direct_cd FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    direct_name = (SELECT direct_name FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    direct_name2 = (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    direct_cname = (SELECT direct_cname FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    d_post_no1 = (SELECT post_no1 FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    d_post_no2 = (SELECT post_no2 FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    d_address1 = (SELECT address1 FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    d_address2 = (SELECT address2 FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    d_address3 = (SELECT address3 FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    d_tel = (SELECT tel FROM t_direct WHERE direct_id = $direct_id), ";
                $sql .= "    d_fax = (SELECT fax FROM t_direct WHERE direct_id = $direct_id) ";
            }else{
                $sql .= "    direct_id = null, ";
                $sql .= "    direct_cd = null, ";
                $sql .= "    direct_name = null, ";
                $sql .= "    direct_name2 = null, ";
                $sql .= "    direct_cname = null, ";
                $sql .= "    d_post_no1 = null, ";
                $sql .= "    d_post_no2 = null, ";
                $sql .= "    d_address1 = null, ";
                $sql .= "    d_address2 = null, ";
                $sql .= "    d_address3 = null, ";
                $sql .= "    d_tel = null, ";
                $sql .= "    d_fax = null ";
            }

            $sql .= "WHERE \n";
            $sql .= "    sale_id = $sale_id \n";
            $sql .= ";\n";
/*
echo "売上ヘッダ";
print_array($sql);
*/

            $result = Db_Query($db_con, $sql);
            if($result == false){
                $result = Db_Query($db_con, "ROLLBACK;");
                exit();
            }


            //売上巡回担当者テーブル削除
            $sql = "DELETE FROM t_sale_staff WHERE sale_id = $sale_id;";
            $result = Db_Query($db_con, $sql);
            if($result == false){
                $result = Db_Query($db_con, "ROLLBACK;");
                exit();
            }

            //売上データテーブル削除
            //  売上内訳、売上出庫品、在庫受払、入金の各テーブルも削除されます
            $sql = "DELETE FROM t_sale_d WHERE sale_id = $sale_id;";
            $result = Db_Query($db_con, $sql);
            if($result == false){
                $result = Db_Query($db_con, "ROLLBACK;");
                exit();
            }

            //割賦売上テーブル削除
            $sql = "DELETE FROM t_installment_sales WHERE sale_id = $sale_id;";
            $result = Db_Query($db_con, $sql);
            if($result == false){
                $result = Db_Query($db_con, "ROLLBACK;");
                exit();
            }

            //入金テーブル削除
            $sql = "DELETE FROM t_payin_h WHERE sale_id = $sale_id;";
            $result = Db_Query($db_con, $sql);
            if($result == false){
                $result = Db_Query($db_con, "ROLLBACK;");
                exit();
            }

            //仕入テーブル削除（紹介料）
            $sql = "DELETE FROM t_buy_h WHERE intro_sale_id = $sale_id;";
            $result = Db_Query($db_con, $sql);
            if($result == false){
                $result = Db_Query($db_con, "ROLLBACK;");
                exit();
            }


            /****************************/
            //巡回担当者テーブル登録
            /****************************/
            //契約区分が通常orＦＣのみ登録
            for($c=0;$c<=3;$c++){
                //スタッフが指定されているか判定
                if($staff_check[$c] != NULL){
                    //履歴用
                    $sql = "SELECT staff_name FROM t_staff WHERE staff_id = ".$staff_check[$c].";";
                    $result = Db_Query($db_con, $sql);
                    $staff_data = Get_Data($result, 3);

                    $sql  = "INSERT INTO t_sale_staff ( ";
                    $sql .= "    sale_id, ";
                    $sql .= "    staff_div, ";
                    $sql .= "    staff_id, ";
                    $sql .= "    sale_rate, ";
                    $sql .= "    staff_name ";
                    $sql .= ") VALUES ( ";
                    $sql .= "    $sale_id, ";                   //売上ID
                    $sql .= "    '$c', ";                       //巡回担当者識別
                    $sql .= "    ".$staff_check[$c].", ";       //巡回担当者ID
                    //売上率指定判定
                    if($staff_rate[$c] != NULL){
                        $sql .= "    ".$staff_rate[$c].", ";    //売上率
                    }else{
                        $sql .= "    NULL, ";
                    }
                    $sql .= "    '".$staff_data[0][0]."' ";     //担当者名
                    $sql .= ");";
                    $result = Db_Query($db_con, $sql);
                    if($result === false){
                        Db_Query($db_con, "ROLLBACK;");
                        exit;
                    }
                }
            }


            //売上データの登録
            for($i=1;$i<=5;$i++){
                //サービスまたはアイテムが設定されている場合、売上データを登録
                if($serv_id[$i] != null || $goods_item_id[$i] != null){
                    //売上データID生成
                    $microtime = NULL;
                    $microtime = explode(" ",microtime());
                    $sale_d_id = $microtime[1].substr("$microtime[0]", 2, 5);

                    $sql  = "INSERT INTO \n";
                    $sql .= "    t_sale_d \n";
                    $sql .= "( \n";
                    $sql .= "    sale_d_id, \n";
                    $sql .= "    sale_id, \n";
                    $sql .= "    sale_div_cd, \n";
                    if($serv_id[$i] != null){
                        $sql .= "    serv_print_flg, \n";
                        $sql .= "    serv_id, \n";
                        $sql .= "    serv_cd, \n";
                        $sql .= "    serv_name, \n";
                    }
                    $sql .= "    set_flg, \n";
                    if($goods_item_id[$i] != null){
                        $sql .= "    goods_print_flg, \n";
                        $sql .= "    goods_id, \n";
                        $sql .= "    goods_cd, \n";
                        $sql .= "    g_product_name, \n";
                        $sql .= "    official_goods_name, \n";
                        $sql .= "    goods_name, \n";
                        $sql .= "    unit, \n";
                    }
                    if($goods_item_num[$i] != null){
                        $sql .= "    num, \n";
                    }
                    $sql .= "    tax_div, \n";
                    $sql .= "    buy_price, \n";
                    $sql .= "    buy_amount, \n";
                    $sql .= "    cost_price, \n";
                    $sql .= "    sale_price, \n";
                    $sql .= "    cost_amount, \n";
                    $sql .= "    sale_amount, \n";
                    if($goods_body_id[$i] != null){
                        $sql .= "    rgoods_id, \n";
                        $sql .= "    rgoods_cd, \n";
                        $sql .= "    rgoods_name, \n";
                        $sql .= "    rgoods_num, \n";
                    }
                    if($goods_expend_id[$i] != null){
                        $sql .= "    egoods_id, \n";
                        $sql .= "    egoods_cd, \n";
                        $sql .= "    egoods_name, \n";
                        $sql .= "    egoods_num, \n";
                    }
                    $sql .= "    account_price, \n";
                    $sql .= "    account_rate, \n";
                    $sql .= "    advance_flg, \n";
                    $sql .= "    advance_offset_amount, \n";
                    $sql .= "    line \n";
                    $sql .= ") VALUES ( \n";
                    $sql .= "    $sale_d_id, \n";
                    $sql .= "    $sale_id, \n";
					# 2010-07-06 watanabe-k
                    $sql .= "    '$divide[$i]', \n";
                    if($serv_id[$i] != null){
                        $sql .= "    $slip_flg[$i], \n";
                        $sql .= "    $serv_id[$i], \n";
                        $sql .= "    (SELECT serv_cd FROM t_serv WHERE serv_id = $serv_id[$i]), \n";
                        $sql .= "    (SELECT serv_name FROM t_serv WHERE serv_id = $serv_id[$i]), \n";
                    }
                    $sql .= "    $set_flg[$i], \n";
                    if($goods_item_id[$i] != null){
                        $sql .= "    $goods_item_flg[$i], \n";
                        $sql .= "    $goods_item_id[$i], \n";
                        $sql .= "    (SELECT goods_cd FROM t_goods WHERE goods_id = $goods_item_id[$i]), \n";
                        $sql .= "    (SELECT g_product_name FROM t_g_product WHERE g_product_id = (SELECT g_product_id FROM t_goods WHERE goods_id = $goods_item_id[$i])), \n";
                        $sql .= "    '".$official_goods_name[$i]."', \n";
                        $sql .= "    '$goods_item_name[$i]', \n";
                        $sql .= "    (SELECT unit FROM t_goods WHERE goods_id = $goods_item_id[$i]), \n";
                    }
                    if($goods_item_num[$i] != null){
                        $sql .= "    $goods_item_num[$i], \n";
                    }
                    $sql .= "    $goods_item_tax_div[$i], \n";

                    //仕入単価・金額
                    //アイテムが入力されてる場合、アイテムの在庫単価を入れる
                    if($goods_item_id[$i] != null){
                        $compose_flg_sql = "SELECT compose_flg FROM t_goods WHERE goods_id = ".$goods_item_id[$i].";";
                        $result = Db_Query($db_con, $compose_flg_sql);
                        if($result === false){
                            Db_Query($db_con, "ROLLBACK;");
                            exit;
                        }
                        $compose_flg = pg_fetch_result($result, 0, "compose_flg");  //構成品フラグ

                        //構成品の場合、子の単価を合計する
                        if($compose_flg == "t"){
                            $buy_price_arr = Compose_price($db_con, $shop_id, $goods_item_id[$i]);
                            $buy_price = $buy_price_arr[2];     //仕入単価（在庫単価）
                        //構成品じゃない場合、DBから単価を取得
                        }else{
                            $buy_price_sql  = "SELECT \n";
                            $buy_price_sql .= "    t_price.r_price \n";
                            $buy_price_sql .= "FROM \n";
                            $buy_price_sql .= "    t_price \n";
                            $buy_price_sql .= "WHERE \n";
                            $buy_price_sql .= "    t_price.goods_id = ".$goods_item_id[$i]." \n";
                            $buy_price_sql .= "    AND \n";
                            $buy_price_sql .= "    t_price.rank_cd = '3' \n";   //在庫単価
                            $buy_price_sql .= "    AND \n";
                            if($group_kind == "2"){
                                $buy_price_sql .= "    t_price.shop_id IN (".Rank_Sql().") \n";
                            }else{
                                $buy_price_sql .= "    t_price.shop_id = $shop_id \n";
                            }
                            $buy_price_sql .= ";";
                            $result = Db_Query($db_con, $buy_price_sql);
                            if($result === false){
                                Db_Query($db_con, "ROLLBACK;");
                                exit;
                            }
                            $buy_price = pg_fetch_result($result, 0, 0);    //仕入単価（在庫単価）
                        }
                        if($goods_item_num[$i] != null){
                            $buy_amount = Coax_Col($coax, bcmul($buy_price, $goods_item_num[$i], 2));
                        }else{
                            $buy_amount = Coax_Col($coax, $buy_price);
                        }
                    //アイテムがない場合、原価単価・合計を仕入単価・金額に入れる
                    }else{
                        $buy_price = $trade_price[$i];
                        $buy_amount = $trade_amount[$i];
                    }

                    $sql .= "    $buy_price, \n";
                    $sql .= "    $buy_amount, \n";
                    $sql .= "    $trade_price[$i], \n";
                    $sql .= "    $sale_price[$i], \n";
                    $sql .= "    $trade_amount[$i], \n";
                    $sql .= "    $sale_amount[$i], \n";
                    if($goods_body_id[$i] != null){
                        $sql .= "    $goods_body_id[$i], \n";
                        $sql .= "    (SELECT goods_cd FROM t_goods WHERE goods_id = $goods_body_id[$i]), \n";
                        $sql .= "    '$goods_body_name[$i]', \n";
                        $sql .= "    $goods_body_num[$i], \n";
                    }
                    if($goods_expend_id[$i] != null){
                        $sql .= "    $goods_expend_id[$i], \n";
                        $sql .= "    (SELECT goods_cd FROM t_goods WHERE goods_id = $goods_expend_id[$i]), \n";
                        $sql .= "    '$goods_expend_name[$i]', \n";
                        $sql .= "    $goods_expend_num[$i], \n";
                    }
                    $sql .= "    $ac_price[$i], \n";
                    $sql .= "    '".$ac_rate[$i]."', \n";
                    $sql .= "    '".$ad_flg[$i]."', \n";
                    $sql .= ($ad_flg[$i] != "1") ? "    ".$ad_offset_amount[$i].", \n" : "    NULL, \n";
                    $sql .= "    $i \n";
                    $sql .= ") \n";
                    $sql .= ";\n";
/*
echo "売上データSQL";
print_array($sql);
*/

                    $result = Db_Query($db_con, $sql);
                    if($result === false){
                        Db_Query($db_con, "ROLLBACK;");
                        exit;
                    }


                    /****************************/
                    //出庫品テーブル登録
                    /****************************/

                    //販売区分が、リースorレンタルの場合、出庫品テーブルには登録しない
                    if($divide[$i] != '03' && $divide[$i] != '04'){

                        //構成品・サービスだけの契約か判定
                        if($goods_item_com[$i] != 't' && $goods_item_id[$i] != NULL){
                            //アイテムが入力されている契約

                            //同じ商品か判定。
                            if($goods_ship_id[$goods_item_id[$i]] == NULL){
                                //新規の商品

                                //出庫品登録配列
                                //配列[商品ID][0] = 略称
                                //配列[商品ID][1] = 数量
                                $goods_ship_id[$goods_item_id[$i]][0] = $goods_item_name[$i];
                                $goods_ship_id[$goods_item_id[$i]][1] = $goods_item_num[$i];
                            }else{
                                //同じ商品の場合は数量を足す

                                //出庫品登録配列(配列[商品ID] = 現在の数量 + 数量)
                                $goods_ship_id[$goods_item_id[$i]][1] = $goods_ship_id[$goods_item_id[$i]][1] + $goods_item_num[$i];
                            }
                        }else{
                            //商品が構成品の場合、構成品の子だけを登録

                            for($d=0;$d<count($item_parts[$i]);$d++){
                                //同じ商品か判定。
                                if($goods_ship_id[$item_parts[$i][$d][0]] == NULL){
                                    //新規の商品

                                    //出庫品登録配列
                                    //配列[商品ID][0] = 略称
                                    //配列[商品ID][1] = 数量
                                    $goods_ship_id[$item_parts[$i][$d][0]][0] = $item_parts_cname[$i][$d];
                                    $goods_ship_id[$item_parts[$i][$d][0]][1] = $item_parts_num[$i][$d];
                                }else{
                                    //同じ商品の場合は数量を足す

                                    //出庫品登録配列(配列[商品ID] = 現在の数量 + 数量)
                                    $goods_ship_id[$item_parts[$i][$d][0]][1] = $goods_ship_id[$item_parts[$i][$d][0]][1] + $item_parts_num[$i][$d];
                                }
                            }
                        }

                        //構成品・サービスだけの契約か判定
                        if($goods_expend_com[$i] != 't' && $goods_expend_id[$i] != NULL){
                            //消耗品が入力されている契約

                            //同じ商品か判定。
                            if($goods_ship_id[$goods_expend_id[$i]] == NULL){
                                //新規の商品

                                //出庫品登録配列
                                //配列[商品ID][0] = 略称
                                //配列[商品ID][1] = 数量
                                $goods_ship_id[$goods_expend_id[$i]][0] = $goods_expend_name[$i];
                                $goods_ship_id[$goods_expend_id[$i]][1] = $goods_expend_num[$i];
                            }else{
                                //同じ商品の場合は数量を足す

                                //出庫品登録配列(配列[商品ID] = 現在の数量 + 数量)
                                $goods_ship_id[$goods_expend_id[$i]][1] = $goods_ship_id[$goods_expend_id[$i]][1] + $goods_expend_num[$i];
                            }
                        }else{
                            //商品が構成品の場合、構成品の子だけを登録

                            for($d=0;$d<count($expend_parts[$i]);$d++){
                                //同じ商品か判定。
                                if($goods_ship_id[$expend_parts[$i][$d][0]] == NULL){
                                    //新規の商品

                                    //出庫品登録配列
                                    //配列[商品ID][0] = 略称
                                    //配列[商品ID][1] = 数量
                                    $goods_ship_id[$expend_parts[$i][$d][0]][0] = $expend_parts_cname[$i][$d];
                                    $goods_ship_id[$expend_parts[$i][$d][0]][1] = $expend_parts_num[$i][$d];
                                }else{
                                    //同じ商品の場合は数量を足す

                                    //出庫品登録配列(配列[商品ID] = 現在の数量 + 数量)
                                    $goods_ship_id[$expend_parts[$i][$d][0]][1] = $goods_ship_id[$expend_parts[$i][$d][0]][1] + $expend_parts_num[$i][$d];
                                }
                            }
                        }

                        //サービスだけの契約以外は、用意した配列データを登録
                        if($goods_item_id[$i] != NULL || $goods_expend_id[$i] != NULL){
                            //出庫品登録SQL
                            while($goods_ship_num = each($goods_ship_id)){
                                //添え字の商品ID取得
                                $ship = $goods_ship_num[0];

                                //履歴用カラムデータ取得
                                $sql  = "SELECT ";
                                $sql .= "    t_goods.goods_cd ";
                                $sql .= "FROM ";
                                $sql .= "    t_goods ";
                                $sql .= "WHERE ";
                                $sql .= "    t_goods.goods_id = $ship;";
                                $result = Db_Query($db_con, $sql);
                                $ship_data = Get_Data($result);

                                $sql  = "INSERT INTO t_sale_ship ( ";
                                $sql .= "    sale_d_id,";
                                $sql .= "    goods_id,";
                                $sql .= "    goods_name,";
                                $sql .= "    num, ";
                                $sql .= "    goods_cd ";
                                $sql .= "    )VALUES(";
                                $sql .= "    $sale_d_id,";                     //受注データID
                                $sql .= "    $ship,";                          //商品ID
                                $sql .= "    '".$goods_ship_id[$ship][0]."',"; //商品名
                                $sql .= "    ".$goods_ship_id[$ship][1].",";   //数量
                                $sql .= "    '".$ship_data[0][0]."'";          //商品CD
                                $sql .= "    );";
                                $result = Db_Query($db_con, $sql);
/*
echo "出庫品";
print_array($sql);
*/

                                if($result === false){
                                    Db_Query($db_con, "ROLLBACK;");
                                    exit;
                                }

                            }
                        }
                    }
                    //出庫品登録配列初期化
                    $goods_ship_id = NULL;
                }
            }


            //商品予定出荷一覧で在庫移動済の場合、出荷倉庫を担当者メインの担当倉庫にする
            $sql  = "SELECT \n";
            $sql .= "    t_aorder_h.move_flg \n";
            $sql .= "FROM \n";
            $sql .= "    t_sale_h \n";
            $sql .= "    INNER JOIN t_aorder_h ON t_sale_h.aord_id = t_aorder_h.aord_id \n";
            $sql .= "WHERE \n";
            $sql .= "    t_sale_h.sale_id = $sale_id \n";
            $sql .= ";";
            $result = Db_Query($db_con, $sql);
            if($result === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
            if(pg_fetch_result($result, 0, "move_flg") == "t"){
                //メインの担当倉庫ID
                $staff_ware_id = Get_Staff_Ware_Id($db_con, $staff_check[0]);

                $sql  = "UPDATE \n";
                $sql .= "    t_sale_h \n";
                $sql .= "SET \n";
                $sql .= "    ware_id = ".$staff_ware_id.", \n";
                $sql .= "    ware_name = (SELECT ware_name FROM t_ware WHERE ware_id = ".$staff_ware_id.") \n";
                $sql .= "WHERE \n";
                $sql .= "    sale_id = $sale_id \n";
                $sql .= ";";
                $result = Db_Query($db_con, $sql);
                if($result === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }
            }


            //紹介口座関連を更新
            $sql  = "UPDATE \n";
            $sql .= "    t_sale_h \n";
            $sql .= "SET \n";
            $sql .= ($intro_ac_price != null) ? "    intro_ac_price = $intro_ac_price, \n" : "    intro_ac_price = null, \n";
            $sql .= ($intro_ac_rate != null)  ? "    intro_ac_rate = '$intro_ac_rate', \n" : "    intro_ac_rate = null, \n";
            $sql .= "    intro_ac_div = '$intro_ac_div' \n";
            $sql .= "WHERE \n";
            $sql .= "    sale_id = $sale_id \n";
            $sql .= ";";
            $result = Db_Query($db_con, $sql);
            if($result === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            //紹介口座料を更新
            if($intro_account_id != null && $intro_ac_div != "1"){
                //紹介料計算
                $intro_amount = FC_Intro_Amount_Calc($db_con, "sale", $sale_id);
                if($intro_amount === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }
            }else{
                $intro_amount = null;
            }

            $sql  = "UPDATE \n";
            $sql .= "    t_sale_h \n";
            $sql .= "SET \n";
            $sql .= ($intro_amount !== null) ? "    intro_amount = $intro_amount \n" : "    intro_amount = null \n";
            $sql .= "WHERE \n";
            $sql .= "    sale_id = $sale_id \n";
            $sql .= ";";
            $result = Db_Query($db_con, $sql);
            if($result === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            //紹介口座料発生しないじゃない、かつ0じゃない場合、紹介料の仕入を起こす
            if($intro_ac_div != "1" && $intro_amount > 0){
                $result = FC_Intro_Buy_Query($db_con, $sale_id, $intro_account_id);
                if($result === false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }
            }


            /****************************/
            //受払テーブルに登録関数
            /****************************/
            $result = FC_Trade_Query($db_con, $sale_id);
            if($result === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }else{
                Db_Query($db_con, "COMMIT;");
                header("Location: ".FC_DIR."sale/2-2-207.php?search=1");
                exit;
            }


        }

    }
}


/****************************/
// setConstantsなど
/****************************/
$form->setConstants($con_data2);


//フォームがフリーズしてるか
$freeze_flg = $form->isFrozen();

#2009-09-26 hashimoto-y
if($renew_flg === true){
    #echo "freeze";
    $num = 5;
    $toSmarty_discount_flg = array();
    for ($i=1; $i<=$num; $i++){
        $hdn_discount_flg = $form->getElementValue("hdn_discount_flg[$i]");
        if($hdn_discount_flg === 't'){
            $toSmarty_discount_flg[$i] = 't';
        }else{
            $toSmarty_discount_flg[$i] = 'f';

        }
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
//$page_menu = Create_Menu_f('sale','2');


/****************************/
//javascript
/****************************/

//一式にチェックを付けた場合、金額計算処理
$java_sheet   = "function Set_num(row,coax) {\n";
$java_sheet  .= "    Mult_double2('form_goods_num1['+row+']','form_sale_price['+row+'][1]','form_sale_price['+row+'][2]','form_sale_amount['+row+']','form_trade_price['+row+'][1]','form_trade_price['+row+'][2]','form_trade_amount['+row+']','form_issiki['+row+']',coax);\n";
$java_sheet  .= " }\n\n";

/*
//代行用
$java_sheet  .= <<<DAIKO

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


//plan_data.incのJSを追加
$java_sheet .= $plan_data_js;


/****************************/
//画面ヘッダー作成
/****************************/
//フォームループ数
//$count = ($renew_flg == true) ? count($arr_sale_d_id) : 5;
$count = 5;
for($i=1;$i<=$count;$i++){
    //$loop_num = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");
    $loop_num[$i] = $i;
}

//エラーループ数1
$error_loop_num1 = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");

//エラーループ数2
$error_loop_num2 = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");

//エラーループ数3
$error_loop_num3 = array(0=>"0",1=>"1",2=>"2",3=>"3",4=>"4");


/****************************/
//画面ヘッダー作成
/****************************/
/*
$page_title .= "　　　".$form->_elements[$form->_elementIndex[form_make_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[form_revision_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[form_public_button]]->toHtml();
*/
$page_header = Create_Header($page_title);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());
//$smarty->assign('num',$foreach_num);
$smarty->assign('loop_num',$loop_num);
$smarty->assign('error_loop_num1',$error_loop_num1);
$smarty->assign('error_loop_num2',$error_loop_num2);
$smarty->assign('error_loop_num3',$error_loop_num3);
$smarty->assign('error_msg4',$error_msg4);
$smarty->assign('error_msg5',$error_msg5);
$smarty->assign('error_msg6',$error_msg6);
$smarty->assign('error_msg7',$error_msg7);
$smarty->assign('error_msg8',$error_msg8);

$smarty->assign('error_msg10',$error_msg10);
$smarty->assign('error_msg11',$error_msg11);
$smarty->assign('error_msg13',$error_msg13);

//その他の変数をassign
$smarty->assign('var',array(
    'html_header'   => "$html_header",
    //'page_menu'     => "$page_menu",
    'page_header'   => "$page_header",
    'html_footer'   => "$html_footer",
    'java_sheet'    => "$java_sheet",

    //'flg'           => "$flg",
    //'last_day'      => "$last_day",
    'error_flg'     => $error_flg,
    'error_msg'     => "$error_msg",
    'error_msg2'    => "$error_msg2",
    'error_msg3'    => "$error_msg3",
    'error_msg14'   => "$error_msg14",
    'ac_name'       => "$ac_name",
    //'return_flg'    => "$return_flg",
    //'get_flg'       => "$get_flg",
    'client_id'     => "$client_id",
    //'form_load'     => "$form_load",
    'ware_name'     => "$ware_name",
    //'trade_error_flg' => "$trade_error_flg",
    //'client_cd'     => "$client_cd ",
    //'client_name'   => "$client_name ",
    //'money'         => "$money",
    //'tax_money'     => "$tax_money",
    //'total_money'   => "$total_money",
    'ord_no'        => "$sale_no",
    'round_form'    => "$round_form",
    'renew_flg'     => $renew_flg,
    'sale_id'       => $sale_id,
    'sale_d_id'     => $arr_sale_d_id,
    'concurrent_err_flg'    => $concurrent_err_flg,
    'slip_renew_mess'       => $slip_renew_mess,
    'buy_err_mess'  => "$buy_err_mess",
    'ad_total_warn_mess'    => "$ad_total_warn_mess",
    'contract_div'  => "$contract_div",
    'freeze_flg'    => $freeze_flg,
    'hand_plan_flg' => $hand_plan_flg,
));

#2009-09-26 hashimoto-y
$smarty->assign('toSmarty_discount_flg',$toSmarty_discount_flg);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER["PHP_SELF"].".tpl"));


//print_array($_POST);
//print_array($_SESSION);


?>
