<?php
/**************************
*変更履歴
*   （2006/05/08）
*       検索フォーム表示ボタン追加（watanabe-k）
*       表示データを得意先名から略称に変更（watanabe-k）
*
*   （2006/07/06）
*       shop_gidをなくす（kaji）
*    (2006/08/21)
*       請求先２を表示
*    (2006/11/27)
*       グループを追加 <watanabe-k>
***************************/

/*
 * 履歴：
 * 　日付　　　　B票No.　　　　担当者　　　内容　
 * 　2006/02/01　      　　　　watanabe-k　契約担当者を表示しているが、巡回担当者を表示するように修正
 * 　2007/02/20　      　　　　watanabe-k　不要機能の削除
 * 　2007/03/16　その他78　　　watanabe-k　種別の削除、契約内容を略称の右に移動、契約変更リンクを未契約時は非表示
 *   2007/03/21                kajioka-h   地区のセレクトボックスをSelect_Getを使うように変更
 *   2007/03/22                watanabe-k  グループでのソートを追加
 *  2007-04-10                  fukuda      検索条件復元処理追加
 *   2007-05-05                watanbae-k  契約�，�なくても変更リンクを表示するように修正 
 *   2007-05-09                watanbae-k  代行先を表示 
 *   2007-05-09                watanbae-k　一覧のソート順を変更 
 *   2007-05-11                watanbae-k  巡回担当者の担当者コードを表示するように修正
 *   2007-05-21                watanbae-k  かな検索を可能にする。
 *   2007-07-30                watanbae-k  ラベル出力処理を追加
 *   2007-08-09                watanbae-k  お客様カード出力処理を追加
 *   2007-11-17                watanbae-k  ショップの画面に列が多く表示されるバグを修正
 *   2010-05-01  Rev.1.5　　   hashimoto-y 請求書の宛先フォントサイズ変更機能の追加
 *
 */

$page_title = "得意先マスタ";

// 環境設定ファイル
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");

// HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"], null, "onSubmit=return confirm(true)");

// DBに接続
$db_con = Db_Connect();

// 権限チェック
$auth   = Auth_Check($db_con);


/****************************/
// 検索条件復元関連
/****************************/
// 検索フォーム初期値配列
$ary_form_list = array(
    "form_output_type"  => "1",
    "form_display_num"  => "1",
    "form_client_gr"    => "",
    "form_parents_div"  => "3",
    "form_client"       => array("cd1" => "", "cd2" => ""),
    "form_area_id"      => "",
    "form_tel"          => "",
    "form_c_staff"      => "",
    "form_btype"        => "",
    "form_state_type"   => "1",
    "form_trade"        => "",
);

// 検索条件復元
Restore_Filter2($form, "contract", "show_button", $ary_form_list);


/****************************/
// 外部変数取得
/****************************/
$shop_id    = $_SESSION["client_id"];


/****************************/
// 初期値設定
/****************************/
$form->setDefaults($ary_form_list);

$state_type     = "1";
$order_sql      = "ORDER BY \n";
$order_sql     .= "   t_client_gr.client_gr_cd, \n";
$order_sql     .= "   t_client_claim.client_cd, \n";
$order_sql     .= "   t_client.parents_flg DESC, \n";
$order_sql     .= "   t_client.client_cd1, \n";
$order_sql     .= "   t_client.client_cd2 \n";

$limit          = "100";    // LIMIT
$offset         = "0";      // OFFSET
$total_count    = "0";      // 全件数
$page_count     = ($_POST["f_page1"] != null) ? $_POST["f_page1"] : "1";    // 表示ページ数


/****************************/
// フォームパーツ定義
/****************************/
// 出力形式
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "画面", "1");
$obj[]  =&  $form->createElement("radio", null, null, "CSV",  "2");
$form->addGroup($obj, "form_output_type", "");

// 表示件数
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "100件表示", "1");
$obj[]  =&  $form->createElement("radio", null, null, "全件表示",  "2");
$form->addGroup($obj, "form_display_num", "");

// グループ
$item   =   null;
$item   =   Select_Get($db_con, "client_gr");
$form->addElement("select", "form_client_gr", "", $item, $g_form_option_select);

// グループ名
$form->addElement("text", "form_client_gr_name", "", "size=\"34\" maxLength=\"15\" $g_form_option");


// 親子区分
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "親",   "t");
$obj[]  =&  $form->createElement("radio", null, null, "子",   "f");
$obj[]  =&  $form->createElement("radio", null, null, "独立", "1");
$obj[]  =&  $form->createElement("radio", null, null, "全て", "3");
$form->addGroup($obj, "form_parents_div", "");

// 得意先コード
Addelement_Client_64($form, "form_client", "得意先コード", "-");

// 得意先名・略称
$form->addElement("text", "form_client_name", "", "size=\"34\" maxLength=\"15\" $g_form_option");

// 地区
$item   =   null;
$item   =   Select_Get($db_con, "area");
$form->addElement("select", "form_area_id", "", $item);

// TEL
$form->addElement("text", "form_tel", "", "size=\"15\" maxLength=\"13\" style=\"$g_form_style\" $g_form_option");

// 巡回担当者
$item   =   null;
$item   =   Select_Get($db_con, "staff");
$form->addElement("select", "form_c_staff", "", $item, $g_form_option_select);

// 業種
$sql  = "SELECT \n";
$sql .= "   t_lbtype.lbtype_cd, \n";
$sql .= "   t_lbtype.lbtype_name, \n";
$sql .= "   t_sbtype.sbtype_id, \n";
$sql .= "   t_sbtype.sbtype_cd, \n";
$sql .= "   t_sbtype.sbtype_name \n";
$sql .= "FROM \n";
$sql .= "   t_lbtype \n";
$sql .= "   INNER JOIN t_sbtype ON t_lbtype.lbtype_id = t_sbtype.lbtype_id \n";
$sql .= "ORDER BY \n";
$sql .= "   t_lbtype.lbtype_cd, \n";
$sql .= "   t_sbtype.sbtype_cd \n";
$sql .= ";";
$res  = Db_Query($db_con, $sql);
while ($data_list = pg_fetch_array($res)){
    $max_len = ($max_len < mb_strwidth($data_list[1])) ? mb_strwidth($data_list[1]) : $max_len;
}
$result = Db_Query($db_con, $sql);
$item       = null;
$item[null] = null;
while ($data_list = pg_fetch_array($result)){
    for($i = 0; $i< $max_len; $i++){
        $data_list[1] = (mb_strwidth($data_list[1]) <= $max_len && $i != 0) ? $data_list[1]."　" : $data_list[1];
    }
    $item[$data_list[2]] = $data_list[0]." ： ".$data_list[1]."　　 ".$data_list[3]." ： ".$data_list[4];
}
$form->addElement("select", "form_btype", "", $item, $g_form_option_select);

// 状態
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "取引中", "1");
$obj[]  =&  $form->createElement("radio", null, null, "解約・休止中", "2");
//$obj[]  =&  $form->createElement("radio", null, null, "解約",   "3");
$obj[]  =&  $form->createElement("radio", null, null, "全て",   "4");
$form->addGroup($obj, "form_state_type", "");

// ソートリンク
$ary_sort_item = array(
    "sl_group"          => "グループ",
    "sl_client_cd"      => "得意先コード",
    "sl_client_name"    => "得意先名",
    "sl_area"           => "地区",
    "sl_staff_cd"       => "担当コード",
    "sl_staff_name"     => "巡回担当",
    "sl_act_client_cd"  => "代行先コード",
    "sl_act_client_name"=> "代行先名",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_group");

// 表示ボタン
$form->addElement("submit", "show_button", "表　示");

// クリアボタン
$form->addElement("button", "clear_button", "クリア", "onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

// 登録画面リンクボタン
$form->addElement("button", "new_button", "登録画面", "onClick=\"javascript:location.href='2-1-103.php'\"");

// 変更・一覧リンクボタン
$form->addElement("button", "change_button", "変更・一覧", "$g_button_color onClick=\"location.href='".$_SERVER["PHP_SELF"]."'\"");

// 取引区分
$select_value = Select_Get($db_con, "trade_aord");
$form->addElement("select", 'form_trade', "", $select_value, "onKeyDown=\"chgKeycode();\" onChange =\"window.focus();\"");

//ラベル出力
$form->addElement("checkbox", "label_check_all", "", "ラベル出力", "onClick=\"javascript:All_Label_Check('label_check_all');\"");
#2010-04-27 hashimoto-y
#$form->addElement("submit","form_label_button","ラベル出力","onClick=\"javascript:Post_book_vote('./2-1-106.php','#');\"");
#2010-05-11 hashimoto-y
#$form->addElement("button","form_label_button","ラベル出力","onClick=\"javascript:Post_book_vote3('./2-1-106.php?label_font=s','#');\"");
$form->addElement("button","form_label_button","ラベル出力","onClick=\"javascript:Post_book_vote3('./2-1-106.php','#');\"");


#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#//ラベル出力2 フォントサイズ大
#$form->addElement("checkbox", "label_check_all2", "", "ラベル出力(大)", "onClick=\"javascript:All_Label_Check2('label_check_all2');\"");
#$form->addElement("button","form_label_button2","ラベル出力(大)","onClick=\"javascript:Post_book_vote3('./2-1-106.php?label_font=b','#');\"");

//お客様カード
$form->addElement("checkbox", "card_check_all", "", "お客様カード", "onClick=\"javascript:All_Card_Check('card_check_all');\"");
#2010-04-27 hashimoto-y
#$form->addElement("submit","form_card_button","お客様カード","onClick=\"javascript:Post_book_vote('./2-1-102.php','#');\"");
$form->addElement("button","form_card_button","お客様カード","onClick=\"javascript:Post_book_vote3('./2-1-102.php','#');\"");

/****************************/
// 全件数取得（ヘッダ部表示用）
/****************************/
$sql  = "SELECT \n";
$sql .= "   COUNT(client_id) \n";
$sql .= "FROM \n";
$sql .= "   t_client \n";
$sql .= "WHERE \n";
if ($_SESSION["group_kind"] == "2"){
$sql .= "   t_client.shop_id IN (".Rank_Sql().") \n";
}else{
$sql .= "   t_client.shop_id = $shop_id \n";
}
$sql .= "AND \n";
$sql .= "   t_client.client_div = '1' \n";
$sql .= ";";

// ヘッダに表示させる全件数
$res  = Db_Query($db_con, $sql);
$all_count = pg_fetch_result($res, 0, 0);


/****************************/
// 表示ボタン押下処理
/****************************/
# なし

#2010-04-06 hashimoto-y
if($_POST["show_button"]=="表　示" || $_POST != null){



/****************************/
// POST時
/****************************/
if ($_POST != null){

    // フォームの値を変数にセット
    // 一覧取得クエリ条件に使用
    $output_type    = $_POST["form_output_type"];
    $display_num    = $_POST["form_display_num"];
    $client_gr      = $_POST["form_client_gr"];
    $parents_div    = $_POST["form_parents_div"];
    $client_cd1     = $_POST["form_client"]["cd1"];
    $client_cd2     = $_POST["form_client"]["cd2"];
    $client_name    = $_POST["form_client_name"];
    $client_gr_name = $_POST["form_client_gr_name"];
    $area_id        = $_POST["form_area_id"];
    $tel            = $_POST["form_tel"];
    $c_staff        = $_POST["form_c_staff"];
    $btype          = $_POST["form_btype"];
    $state_type     = ($_POST["form_state_type"] != null) ? $_POST["form_state_type"] : $state_type;

    $trade          = $_POST["form_trade"];

    // csv出力するフラグ
    $csv_flg = ($_POST["show_button"] != null && $output_type == "2") ? true : false;

    $sort_col = $_POST["hdn_sort_col"];

    $post_flg = true;

}else{

    $sort_col = "sl_group";

    $post_flg = true;

}


/****************************/
// 一覧データ取得条件作成
/****************************/
if ($post_flg == true){

    /* 検索条件 */
    $sql = null;

    // グループ
    $sql .= ($client_gr != null) ? "AND t_client.client_gr_id = $client_gr \n" : null;
    // 親子区分
    if ($parents_div == "1"){
        $sql .= "AND t_client.parents_flg IS NULL \n";
    }else
    if ($parents_div == "t" || $parents_div == "f"){
        $sql .= "AND t_client.parents_flg = '$parents_div' \n";
    }
    // 得意先コード1
    $sql .= ($client_cd1 != null) ? "AND t_client.client_cd1 LIKE '$client_cd1%' \n" : null;
    // 得意先コード2
    $sql .= ($client_cd2 != null) ? "AND t_client.client_cd2 LIKE '$client_cd2%' \n" : null;
    // 得意先名・略称
    if ($client_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_client.client_name  LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_name2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_cname LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_read LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_read2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_cread LIKE '%$client_name%' \n";
        $sql .= "   ) \n";
    }
    // 得意先名・略称
    if ($client_gr_name != null){
        $sql .= "AND t_client_gr.client_gr_name LIKE '%$client_gr_name%' \n";
    }

    // 地区
    $sql .= ($area_id != null) ? "AND t_area.area_id = $area_id \n" : null;
    // TEL
    $sql .= ($tel != null) ? "AND t_client.tel LIKE '$tel%' \n" : null;
    // 巡回担当者
    $sql .= ($c_staff != null) ? "AND t_staff.staff_id = $c_staff \n" : null;
    // 業種
    $sql .= ($btype != null) ? "AND t_sbtype.sbtype_id = $btype \n" : null;

    // 取引区分
    $sql .= ($trade != null) ? "AND t_client.trade_id = '$trade' \n" : null;

    // 変数詰め替え
    $where_sql = $sql;

    /* ソート条件 */
    $sql = null;

    $sql .= "ORDER BY \n";

    switch ($sort_col){
        // グループ
        case "sl_group":
            $sql .= "   t_client_gr.client_gr_cd, \n";
            break;  
        // 得意先コード
        case "sl_client_cd":
            break;  
        // 得意先名
        case "sl_client_name":
            $sql .= "   t_client.client_name, \n";
            break;  
        // 地区 
        case "sl_area":
            $sql .= "   t_area.area_cd, \n";
            break;  
        // 担当コード
        case "sl_staff_cd":
            $sql .= "   t_staff.charge_cd, \n";
            break;  
        // 巡回担当
        case "sl_staff_name":
            $sql .= "   t_staff.staff_name, \n";
            break;  
        // 代行先コード
        case "sl_act_client_cd":
            $sql .= "   t_contract_client.client_cd1, \n";
            $sql .= "   t_contract_client.client_cd2, \n";
            break;  
        // 代行先名
        case "sl_act_client_name":
            $sql .= "   t_contract_client.shop_name, \n";
            break; 
    }
    $sql .= "   t_client.client_cd1, \n";
    $sql .= "   t_client.client_cd2 \n";

    // 変数詰め替え
    $order_sql = $sql;

}


/****************************/
// 一覧データ取得
/****************************/
$sql  = "SELECT \n";
$sql .= "   t_client.client_id, \n";                        //  0 得意先ID
$sql .= "   t_client.client_cd1, \n";                       //  1 得意先コード１
$sql .= "   t_client.client_cd2, \n";                       //  2 得意先コード２
$sql .= "   t_client.client_name, \n";                      //  3 得意先名
$sql .= "   t_client.client_cname, \n";                     //  4 得意先名（略称）
$sql .= "   t_area.area_name, \n";                          //  5 地区名
$sql .= "   t_client.tel, \n";                              //  6 TEL
$sql .= "   t_client.state, \n";                            //  7 状態
$sql .= "   t_staff.staff_name, \n";                        //  8 巡回担当者
$sql .= "   t_staff.staff_cd1, \n";                         //  9 巡回担当者コード１
$sql .= "   t_staff.staff_cd2, \n";                         // 10 巡回担当者コード２
$sql .= "   lpad(t_staff.charge_cd, 4, 0) AS charge_cd, \n";// 11 担当者コード
$sql .= "   t_client_claim.client_name \n";
$sql .= "   AS claim_name1, \n";                            // 12 請求先１名
$sql .= "   t_sbtype.sbtype_id, \n";                        // 13 業種ID
$sql .= "   t_client_claim2.client_name \n";
$sql .= "   AS claim_name2, \n";                            // 14 請求先２名
$sql .= "   t_client.client_name2, \n";                     // 15 得先名２
$sql .= "   t_client.address1, \n";                         // 16 住所１
$sql .= "   t_client.address2, \n";                         // 17 住所２
$sql .= "   t_client.address3, \n";                         // 18 住所３
$sql .= "   t_client_claim.client_cd \n";
$sql .= "   AS claim_cd1, \n";                              // 19 請求先１コード
$sql .= "   t_client_claim2.client_cd \n";
$sql .= "   AS claim_cd2, \n";                              // 20 請求先２コード
$sql .= "   t_client_gr.client_gr_cd, \n";                  // 21 グループコード
$sql .= "   t_client_gr.client_gr_name, \n";                // 22 グループ名
$sql .= "   CASE t_client.parents_flg \n";
$sql .= "       WHEN 't' THEN '親'\n";
$sql .= "       WHEN 'f' THEN '子'\n";
$sql .= "       ELSE '独立'\n";
$sql .= "   END \n";
$sql .= "   AS parents_flg, \n";                            // 23 親子フラグ
$sql .= "   CASE \n";
$sql .= "       WHEN t_contract.client_id IS NOT NULL \n";
$sql .= "       THEN 't' \n";
$sql .= "       ELSE 'f' \n";
$sql .= "   END \n";
$sql .= "   AS contract_flg";                               // 24 
// csv出力時
if ($csv_flg == true){
$sql .= ", \n";
$sql .= "   t_intro_ac.client_cd1, \n";                     // 25 照会口座先コード
$sql .= "   t_intro_ac.client_name, \n";                    // 26 紹介口座名
$sql .= "   t_client.intro_ac_name, \n";                    // 27 振込先口座名
$sql .= "   t_client.intro_bank, \n";                       // 28 銀行/支店名
$sql .= "   t_client.intro_ac_num, \n";                     // 29 口座番号
$sql .= "   t_lbtype.lbtype_name || ' ' ||t_sbtype.sbtype_name, \n";// 30 業種名
$sql .= "   t_inst.inst_name, \n";                          // 31 施設名
$sql .= "   t_bstruct.bstruct_name, \n";                    // 32 業態名
$sql .= "   t_client.client_read, \n";                      // 33 得意先名１（フリガナ）
$sql .= "   t_client.client_read2, \n";                     // 34 得意先名２（フリガナ）
$sql .= "   t_client.client_cread, \n";                     // 35 略称（フリガナ）
$sql .= "   t_client.post_no1, \n";                         // 36 郵便番号１
$sql .= "   t_client.post_no2, \n";                         // 37 郵便番号２
$sql .= "   t_client.address_read, \n";                     // 38 住所（フリガナ）
$sql .= "   t_client.fax, \n";                              // 39 FAX
$sql .= "   t_client.establish_day, \n";                    // 40 創業日
$sql .= "   t_client.email, \n";                            // 41 担当者EMAIL
$sql .= "   t_client.rep_name, \n";                         // 42 代表者氏名
$sql .= "   t_client.represe, \n";                          // 43 代表者役職
$sql .= "   CASE t_client.compellation \n";
$sql .= "       WHEN '1' THEN '御中'\n";
$sql .= "       ELSE '様'\n";
$sql .= "   END \n";
$sql .= "   AS compellation, \n";                           // 44 敬称
$sql .= "   t_client.company_name, \n";                     // 45 親会社名
$sql .= "   t_client.company_tel, \n";                      // 46 親会社TEL
$sql .= "   t_client.company_address, \n";                  // 47 親会社住所
$sql .= "   t_client.capital, \n";                          // 48 資本金
$sql .= "   t_client.parent_establish_day, \n";             // 49 親会社創業日
$sql .= "   t_client.parent_rep_name, \n";                  // 50 親会社代表者氏名
$sql .= "   t_client.url, \n";                              // 51 URL
$sql .= "   t_client.charger_part1, \n";                    // 52 担当部署１
$sql .= "   t_client.charger_part2, \n";                    // 53 担当部署２
$sql .= "   t_client.charger_part3, \n";                    // 54 担当部署３
$sql .= "   t_client.charger_represe1, \n";                 // 55 担当役職１
$sql .= "   t_client.charger_represe2, \n";                 // 56 担当役職２
$sql .= "   t_client.charger_represe3, \n";                 // 57 担当役職３
$sql .= "   t_client.charger1, \n";                         // 58 担当者氏名１
$sql .= "   t_client.charger2, \n";                         // 59 担当者氏名２
$sql .= "   t_client.charger3, \n";                         // 60 担当者氏名３
$sql .= "   t_client.charger_note, \n";                     // 61 担当者備考
$sql .= "   t_client.trade_stime1, \n";                     // 62 営業時間（午前開始）
$sql .= "   t_client.trade_etime1, \n";                     // 63 営業時間（午前終了）
$sql .= "   t_client.trade_stime2, \n";                     // 64 営業時間（午後開始）
$sql .= "   t_client.trade_etime2, \n";                     // 65 営業時間（午後終了）
$sql .= "   t_client.holiday, \n";                          // 66 休日
$sql .= "   t_client.credit_limit, \n";                     // 67 与信限度
$sql .= "   t_client.col_terms, \n";                        // 68 回収条件
$sql .= "   t_trade.trade_name, \n";                        // 69 取引区分
$sql .= "   CASE t_client.close_day \n";
$sql .= "       WHEN '29' THEN '月末' \n";
$sql .= "       ELSE t_client.close_day || '日'\n";
$sql .= "   END AS close_day, \n";                          // 70 締日
$sql .= "   CASE t_client.pay_m \n";
$sql .= "       WHEN '0' THEN '当月' \n";
$sql .= "       WHEN '1' THEN '翌月' \n";
$sql .= "       ELSE t_client.pay_m || 'ヵ月後' \n";
$sql .= "   END AS pay_m, \n";                              // 71 集金日（月）
$sql .= "   CASE t_client.pay_d \n";
$sql .= "       WHEN '29' THEN '月末' \n";
$sql .= "       ELSE t_client.pay_d || '日' \n";
$sql .= "   END AS pay_d, \n";                              // 72 集金日（日）
$sql .= "   CASE t_client.pay_way \n";
$sql .= "       WHEN '1' THEN '自動引落' \n";
$sql .= "       WHEN '2' THEN '振込' \n";
$sql .= "       WHEN '3' THEN '訪問集金' \n";
$sql .= "       WHEN '4' THEN '手形' \n";
$sql .= "       WHEN '5' THEN 'その他' \n";
$sql .= "   END AS pay_way, \n";                            // 73 集金方法
$sql .= "   CASE \n";
$sql .= "       WHEN t_client.account_id IS NOT NULL \n";
$sql .= "       THEN t_bank.bank_name || '　' || t_b_bank.b_bank_name || '　' || 
                CASE t_account.deposit_kind  WHEN '1' THEN '普通 ' 
                    WHEN '2' THEN '当座 ' END || t_account.account_no";
$sql .= "       ELSE '' \n";
$sql .= "   END \n";
$sql .= "   AS pay_bank, \n";                               // 74 振込銀行口座
$sql .= "   t_client.account_name, \n";                     // 75 振込名義１
$sql .= "   t_client.pay_name, \n";                         // 76 振込名義２
$sql .= "   CASE t_client.bank_div ";
$sql .= "       WHEN '1' THEN 'お客様負担' \n";
$sql .= "       WHEN '2' THEN '自社負担' \n";
$sql .= "   END AS bank_div, \n";                           // 77 銀行手数料負担区分
$sql .= "   t_client.cont_sday, \n";                        // 78 契約年月日
$sql .= "   t_client.cont_rday, \n";                        // 79 契約更新日
$sql .= "   t_client.cont_eday, \n";                        // 80 契約終了日
$sql .= "   t_client.cont_peri, \n";                        // 81 契約期間
$sql .= "   CASE t_client.slip_out \n";
$sql .= "       WHEN '1' THEN '有' \n";
$sql .= "       WHEN '2' THEN '指定' \n";
$sql .= "       WHEN '3' THEN '無' \n";
$sql .= "   END AS slip_out, \n";                           // 82 伝票発行
$sql .= "   t_slip_sheet.s_pattern_name, \n";               // 83 売上伝票発行元
$sql .= "   CASE t_client.deliver_effect \n";
$sql .= "       WHEN '1' THEN 'コメント無効' \n";
$sql .= "       WHEN '2' THEN '個別コメント有効' \n";
$sql .= "       ELSE '全体コメント有効' \n";
$sql .= "   END AS deliver_effect, \n";                     // 84 売上伝票コメント効果
$sql .= "   t_client.deliver_note, \n";                     // 85 売上伝票コメント
$sql .= "   CASE t_client.claim_out \n";    
$sql .= "       WHEN '1' THEN '明細請求書' \n"; 
$sql .= "       WHEN '2' THEN '合計請求書' \n"; 
$sql .= "       WHEN '5' THEN '個別明細請求書' \n"; 
$sql .= "       WHEN '3' THEN '出力しない' \n"; 
$sql .= "       WHEN '4' THEN '指定' \n"; 
$sql .= "       ELSE '指定' \n";    
$sql .= "   END AS claim_out, \n";                          // 86 請求書発行
$sql .= "   CASE t_client.claim_send \n";   
$sql .= "       WHEN '1' THEN '郵送' \n";   
$sql .= "       WHEN '2' THEN 'メール' \n"; 
$sql .= "       WHEN '4' THEN 'WEB' \n";    
$sql .= "       ELSE '郵送・メール' \n";    
$sql .= "   END AS claim_send, \n";                         // 87 請求書送付
$sql .= "   t_claim_sheet.c_pattern_name, \n";              // 88 請求書発行元
$sql .= "   t_client.claim_note, \n";                       // 89 請求書備考
$sql .= "   CASE t_client.coax \n"; 
$sql .= "       WHEN '1' THEN '切捨' \n";   
$sql .= "       WHEN '2' THEN '四捨五入' \n";   
$sql .= "       ELSE '切上' \n";    
$sql .= "   END AS claim_note, \n";                         // 90 金額丸め区分
$sql .= "   CASE t_client.tax_div \n";  
$sql .= "       WHEN '2' THEN '伝票単位' \n";   
$sql .= "       ELSE '締日単位' \n";    
$sql .= "   END AS tax_div, \n";                            // 91 消費税：課税単位
$sql .= "   CASE t_client.tax_franct \n";   
$sql .= "       WHEN '1' THEN '切捨' \n";   
$sql .= "       WHEN '2' THEN '四捨五入' \n";   
$sql .= "       ELSE '切上' \n";    
$sql .= "   END AS tax_franct, \n";                         // 92 消費税：端数区分
$sql .= "   CASE t_client.c_tax_div \n";    
$sql .= "       WHEN '1' THEN '外税' \n";   
$sql .= "       ELSE '内税' \n";    
$sql .= "   END AS tax_div, \n";                            // 93 消費税：課税区分
$sql .= "   t_client.note, \n";                             // 94 設備情報等・その他
$sql .= "   t_branch.branch_name, \n";                      // 95 担当支店
$sql .= "   t_staff1.staff_name, \n";                       // 96 現契約担当
$sql .= "   t_staff2.staff_name, \n";                       // 97 初期契約社員
$sql .= "   t_client.round_day, \n";                        // 98 巡回開始日
$sql .= "   t_client.deal_history, \n";                     // 99 取引履歴
$sql .= "   t_client.importance, \n";                       // 100 重要事項
$sql .= "   t_intro_ac.client_cd2,";                        // 101 照会口座コード２
$sql .= "   t_intro_ac.client_div, ";                       // 102 照会口座の取引区分

$sql .= "   CASE t_client_claim.month1_flg \n";             // 103 1月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month2_flg \n";             // 104 2月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month3_flg \n";             // 105 3月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month4_flg \n";             // 106 4月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month5_flg \n";             // 107 5月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month6_flg \n";             // 108 6月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month7_flg \n";             // 109 7月請求書作成    
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month8_flg \n";             // 110 8月請求書作成    
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month9_flg \n";             // 111 9月請求書作成    
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month10_flg \n";            // 112 10月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month11_flg \n";            // 113 11月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month12_flg \n";            // 114 12月請求書作成
$sql .= "       WHEN 't' THEN '○' \n";
$sql .= "       WHEN 'f' THEN '×' \n";
$sql .= "   END  ";

}

$sql .= " ,\n";
$sql .= "   CASE \n";
$sql .= "       WHEN t_contract.client_id IS NOT NULL AND t_cont_state.count IS NULL THEN '休止中' \n";
$sql .= "       WHEN t_contract.client_id IS NOT NULL AND t_cont_state.count > 0 THEN '変更'  \n";
$sql .= "       ELSE ''";
$sql .= "   END AS link, ";                 // 115
$sql .= "   t_contract_client.client_cd1 ||'-'|| t_contract_client.client_cd2 AS trust_cd, ";            // 116 代行先コード
$sql .= "   t_contract_client.shop_name AS trust_name, ";    // 117 代行先名

#2010-05-01 hashimoto-y
$sql .= "   CASE t_client.bill_address_font \n";            // 118 請求書宛先
$sql .= "       WHEN 't' THEN '大' \n";
$sql .= "       WHEN 'f' THEN '' \n";
$sql .= "   END  ";

$sql .= "FROM \n";
$sql .= "   t_client \n";

//契約マスタと結合
$sql .= "       LEFT JOIN \n";
$sql .= "   ( SELECT ";
$sql .= "       t_contract.client_id,";
$sql .= "       contract_id ";
$sql .= "   FROM";
$sql .= "       (SELECT "; 
$sql .= "           MIN(line) AS line, ";
$sql .= "           t_contract.client_id ";
$sql .= "       FROM  \n";
$sql .= "           t_contract";
$sql .= "       GROUP BY ";
$sql .= "           client_id ";
$sql .= "       ) AS t_cont_line";
$sql .= "           INNER JOIN ";
$sql .= "       t_contract ";
$sql .= "       ON t_cont_line.line = t_contract.line ";
$sql .= "       AND t_cont_line.client_id = t_contract.client_id ";
$sql .= "   ) AS t_contract ";
$sql .= "   ON t_client.client_id = t_contract.client_id \n";

$sql .= "       LEFT JOIN \n";
$sql .= "   ( SELECT ";
$sql .= "       COUNT(client_id) AS count,";
$sql .= "       client_id ";
$sql .= "   FROM ";
$sql .= "       t_contract ";
$sql .= "   WHERE ";
$sql .= "       state = '1' ";
$sql .= "   GROUP BY client_id ";
$sql .= "   ) AS t_cont_state ";
$sql .= "   ON t_client.client_id = t_cont_state.client_id ";

//代行の契約と結合
$sql .= "       LEFT JOIN ";
$sql .= "   ( SELECT ";
$sql .= "       t_contract.client_id, ";
$sql .= "       trust_id ";
$sql .= "   FROM ";
$sql .= "       (SELECT ";
$sql .= "           MIN(line) AS line, ";
$sql .= "           t_contract.client_id ";
$sql .= "       FROM ";
$sql .= "           t_contract ";
$sql .= "       WHERE ";
$sql .= "           t_contract.contract_div != '1'";
$sql .= "       GROUP BY client_id ";
$sql .= "       ) AS t_cont_line";
$sql .= "           INNER JOIN ";
$sql .= "       t_contract ";
$sql .= "       ON t_cont_line.line = t_contract.line ";
$sql .= "       AND t_cont_line.client_id = t_contract.client_id ";
$sql .= "   ) AS t_con_act ";
$sql .= "   ON t_client.client_id = t_con_act.client_id ";

$sql .= "       LEFT JOIN ";
$sql .= "   t_client AS t_contract_client ";
$sql .= "   ON t_con_act.trust_id = t_contract_client.client_id ";

$sql .= "   LEFT JOIN t_con_staff \n";
$sql .= "   ON t_contract.contract_id = t_con_staff.contract_id \n";
$sql .= "   AND t_con_staff.staff_div = '0' \n";
$sql .= "   LEFT JOIN t_staff \n";
$sql .= "   ON t_con_staff.staff_id = t_staff.staff_id \n";
$sql .= "   INNER JOIN t_area \n";
$sql .= "   ON t_client.area_id = t_area.area_id \n";
$sql .= "   LEFT JOIN t_sbtype \n";
$sql .= "   ON t_client.sbtype_id = t_sbtype.sbtype_id \n";
$sql .= "   LEFT JOIN t_lbtype \n";
$sql .= "   ON t_lbtype.lbtype_id = t_sbtype.lbtype_id \n";
$sql .= "   LEFT JOIN t_client_gr \n";
$sql .= "   ON t_client.client_gr_id = t_client_gr.client_gr_id \n";
$sql .= "   INNER JOIN \n";
$sql .= "   ( \n";
$sql .= "       SELECT \n";
$sql .= "           t_claim.*, \n";
$sql .= "           t_client.client_cd1 || '-' || t_client.client_cd2 AS client_cd, \n";
$sql .= "           t_client.client_name \n";
$sql .= "       FROM \n";
$sql .= "           t_claim \n";
$sql .= "           INNER JOIN t_client \n";
$sql .= "           ON t_client.client_id = t_claim.claim_id \n";
$sql .= "           AND t_claim.claim_div = '1' \n";
$sql .= "   ) \n";
$sql .= "   AS t_client_claim \n";
$sql .= "   ON t_client.client_id = t_client_claim.client_id \n";
$sql .= "   LEFT JOIN \n";
$sql .= "   ( \n";
$sql .= "       SELECT \n";
$sql .= "           t_claim.client_id, \n";
$sql .= "           t_client.client_cd1 || '-' || t_client.client_cd2 AS client_cd, \n";
$sql .= "           t_client.client_name \n";
$sql .= "       FROM \n";
$sql .= "           t_claim \n";
$sql .= "           INNER JOIN t_client \n";
$sql .= "           ON t_client.client_id = t_claim.claim_id \n";
$sql .= "           AND t_claim.claim_div = '2' \n";
$sql .= "   ) \n";
$sql .= "   AS t_client_claim2 \n";
$sql .= "   ON t_client.client_id = t_client_claim2.client_id \n";
// csv出力時
if ($csv_flg == true){
$sql .= "   INNER JOIN t_client_info \n";
$sql .= "   ON t_client.client_id = t_client_info.client_id \n";
$sql .= "   LEFT JOIN t_client AS t_intro_ac \n";
$sql .= "   ON t_client_info.intro_account_id = t_intro_ac.client_id \n";
$sql .= "   LEFT JOIN t_inst \n";
$sql .= "   ON t_client.inst_id = t_inst.inst_id \n";
$sql .= "   LEFT JOIN t_bstruct \n";
$sql .= "   ON t_bstruct.bstruct_id = t_client.b_struct \n";
$sql .= "   LEFT JOIN t_trade \n";
$sql .= "   ON t_trade.trade_id = t_client.trade_id \n";
$sql .= "   LEFT JOIN t_account \n";
$sql .= "   ON t_account.account_id = t_client.account_id \n";
$sql .= "   LEFT JOIN t_b_bank \n";
$sql .= "   ON t_b_bank.b_bank_id = t_account.b_bank_id \n";
$sql .= "   LEFT JOIN t_bank \n";
$sql .= "   ON t_bank.bank_id = t_b_bank.bank_id \n";
$sql .= "   LEFT JOIN t_slip_sheet \n";
$sql .= "   ON t_client.s_pattern_id = t_slip_sheet.s_pattern_id \n";
$sql .= "   LEFT JOIN t_claim_sheet \n";
$sql .= "   ON t_client.c_pattern_id = t_claim_sheet.c_pattern_id \n";
$sql .= "   LEFT JOIN t_branch \n";
$sql .= "   ON t_client.charge_branch_id = t_branch.branch_id \n";
$sql .= "   LEFT JOIN t_staff AS t_staff1 \n";
$sql .= "   ON t_staff1.staff_id = t_client.c_staff_id1 \n";
$sql .= "   LEFT JOIN t_staff AS t_staff2 \n";
$sql .= "   ON t_staff2.staff_id = t_client.c_staff_id2 \n";


}
$sql .= "WHERE \n";
if ($_SESSION["group_kind"] == "2"){
$sql .= "   t_client.shop_id IN (".Rank_Sql().") \n";
}else{
$sql .= "   t_client.shop_id = $shop_id \n";
}
$sql .= "AND \n";
$sql .= "   t_client.client_div = '1' \n";
if ($state_type != "4"){
$sql .= "AND \n";
$sql .= "   t_client.state = '$state_type' \n";
}
$sql .= $where_sql;
$sql .= $order_sql;

// 画面出力時
if($csv_flg != true){

    // 全件数取得
    $res            = Db_Query($db_con, $sql.";");
    $total_count    = pg_num_rows($res);

    // LIMIT, OFFSET条件作成
    if ($post_flg == true){

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

        // 行削除でページに表示するレコードが無くなる場合の対処
        if($page_count != null){
            // 行削除でtotal_countとoffsetの関係が崩れた場合
            if ($total_count <= $offset){
                // オフセットを選択件前に
                $offset     = $offset - $limit;
                // 表示するページを1ページ前に（一気に2ページ分削除されていた場合などには対応してないです）
                $page_count = $page_count - 1;
                // 選択件数以下時はページ遷移を出力させない(nullにする)
                $page_count = ($total_count <= $display_num) ? null : $page_count;
            }
        }else{
            $offset = 0;
        }

    }

    // ページ内データ取得
    $limit_offset   = ($limit != null) ? "LIMIT $limit OFFSET $offset " : null;
    $res            = Db_Query($db_con, $sql.$limit_offset.";");
    $match_count    = pg_num_rows($res);
    $page_data      = Get_Data($res, 2, "ASSOC");
// csv出力時
}elseif ($csv_flg == true){

    // 該当件数
    $res            = Db_Query($db_con, $sql.";");
    $match_count    = pg_num_rows($res);
    $page_data      = Get_Data($res, $output_type);

    // CSV作成
    $result = Db_Query($db_con, $sql);
        for($i = 0; $i < $match_count; $i++){
            $csv_page_data[$i][0] = ($page_data[$i][7] == "1") ? "取引中" : "解約・休止中";//状態
            $csv_page_data[$i][1] = $page_data[$i][22];                 //グループ名
            $csv_page_data[$i][2] = $page_data[$i][23];                 //親子区分
            $csv_page_data[$i][3] = $page_data[$i][5];                  //地区
            $csv_page_data[$i][4] = $page_data[$i][30];                 //業種
            $csv_page_data[$i][5] = $page_data[$i][31];                 //施設
            $csv_page_data[$i][6] = $page_data[$i][32];                 //業態
            $csv_page_data[$i][7] = $page_data[$i][1]."-".$page_data[$i][2];    //得意先コード
            $csv_page_data[$i][8] = $page_data[$i][3];                  //得意先名１
            $csv_page_data[$i][9] = $page_data[$i][15];                 //得意先名２
            $csv_page_data[$i][10] = $page_data[$i][33];                //得意先名１（フリガナ）
            $csv_page_data[$i][11] = $page_data[$i][34];                //得意先名２（フリガナ）
            $csv_page_data[$i][12] = $page_data[$i][4];                 //略称
            $csv_page_data[$i][13] = $page_data[$i][35];                //略称（フリガナ）
            $csv_page_data[$i][14] = $page_data[$i][44];                //敬称
            $csv_page_data[$i][15] = $page_data[$i][42];                //代表者氏名
            $csv_page_data[$i][16] = $page_data[$i][43];                //代表者役職
            $csv_page_data[$i][17] = $page_data[$i][36]."-".$page_data[$i][37]; //郵便番号
            $csv_page_data[$i][18] = $page_data[$i][16];                //住所１
            $csv_page_data[$i][19] = $page_data[$i][17];                //住所２
            $csv_page_data[$i][20] = $page_data[$i][18];                //住所３
            $csv_page_data[$i][21] = $page_data[$i][38];                //住所２（フリガナ）
            $csv_page_data[$i][22] = $page_data[$i][6];                 //TEL
            $csv_page_data[$i][23] = $page_data[$i][39];                //FAX
            $csv_page_data[$i][24] = $page_data[$i][40];                //創業日
            $csv_page_data[$i][25] = $page_data[$i][41];                //担当者Email
            $csv_page_data[$i][26] = $page_data[$i][45];                //親会社名
            $csv_page_data[$i][27] = $page_data[$i][46];                //親会社TEL
            $csv_page_data[$i][28] = $page_data[$i][47];                //親会社住所
            $csv_page_data[$i][29] = $page_data[$i][48];                //資本金
            $csv_page_data[$i][30] = $page_data[$i][49];                //親会社創業日
            $csv_page_data[$i][31] = $page_data[$i][50];                //親会社代表者氏名
            $csv_page_data[$i][32] = $page_data[$i][51];                //URL
            $csv_page_data[$i][33] = $page_data[$i][52];                //担当１部署
            $csv_page_data[$i][34] = $page_data[$i][55];                //担当１役職
            $csv_page_data[$i][35] = $page_data[$i][58];                //担当１氏名
            $csv_page_data[$i][36] = $page_data[$i][53];                //担当２部署
            $csv_page_data[$i][37] = $page_data[$i][56];                //担当２役職
            $csv_page_data[$i][38] = $page_data[$i][59];                //担当２氏名
            $csv_page_data[$i][39] = $page_data[$i][54];                //担当３部署
            $csv_page_data[$i][40] = $page_data[$i][57];                //担当３役職
            $csv_page_data[$i][41] = $page_data[$i][60];                //担当３氏名
            $csv_page_data[$i][42] = $page_data[$i][61];                //担当情報備考
            $csv_page_data[$i][43] = ($page_data[$i][62]!= null || $page_data[$i][63]!=null)?$page_data[$i][62]."〜".$page_data[$i][63]:""; //営業時間（午前）
            $csv_page_data[$i][44] = ($page_data[$i][64]!= null || $page_data[$i][65]!=null)?$page_data[$i][64]."〜".$page_data[$i][65]:""; //営業時間（午後）
            $csv_page_data[$i][45] = $page_data[$i][66];                //休日
            $csv_page_data[$i][46] = $page_data[$i][19];                //請求先１コード
            $csv_page_data[$i][47] = $page_data[$i][12];                //請求先１名
            $csv_page_data[$i][48] = $page_data[$i][20];                //請求先２コード
            $csv_page_data[$i][49] = $page_data[$i][14];                //請求先２名
            $csv_page_data[$i][50] = $page_data[$i][67];                //与信限度
            $csv_page_data[$i][51] = $page_data[$i][68];                //回収条件
            $csv_page_data[$i][52] = $page_data[$i][69];                //取引区分
            $csv_page_data[$i][53] = $page_data[$i][70];                //締日
            $csv_page_data[$i][54] = $page_data[$i][71]."の".$page_data[$i][72];    //集金日
            $csv_page_data[$i][55] = $page_data[$i][73];                //集金方法
            $csv_page_data[$i][56] = $page_data[$i][74];                //振込銀行口座
            $csv_page_data[$i][57] = $page_data[$i][76];                //振込名義１
            $csv_page_data[$i][58] = $page_data[$i][75];                //振込名義２
            $csv_page_data[$i][59] = $page_data[$i][77];                //銀行手数料負担区分
            $csv_page_data[$i][60] = $page_data[$i][78];                //契約年月日
            $csv_page_data[$i][61] = $page_data[$i][79];                //契約更新日
            $csv_page_data[$i][62] = $page_data[$i][81];                //契約期間
            $csv_page_data[$i][63] = $page_data[$i][80];                //契約終了日
            $csv_page_data[$i][64] = $page_data[$i][82];                //売上伝票発行
            $csv_page_data[$i][65] = $page_data[$i][83];                //売上伝票発行元
            $csv_page_data[$i][66] = $page_data[$i][84];                //売上伝票コメント効果
            $csv_page_data[$i][67] = $page_data[$i][85];                //売上伝票コメント
            $csv_page_data[$i][68] = $page_data[$i][86];                //請求書発行
            $csv_page_data[$i][69] = $page_data[$i][87];                //請求書送付
            $csv_page_data[$i][70] = $page_data[$i][88];                //請求書発行元
            $csv_page_data[$i][71] = $page_data[$i][89];                //請求書備考
            $csv_page_data[$i][72] = $page_data[$i][90];                //金額丸め区分
            $csv_page_data[$i][73] = $page_data[$i][91];                //消費税：課税単価
            $csv_page_data[$i][74] = $page_data[$i][92];                //消費税：端数区分
            $csv_page_data[$i][75] = $page_data[$i][93];                //消費税：課税区分
            $csv_page_data[$i][76] = $page_data[$i][94];                //設備情報等・その他
            if($page_data[$i][102] == "3"){
                $page_data[$i][25] = $page_data[$i][25]."-".$page_data[$i][101];
            }
            $csv_page_data[$i][77] = $page_data[$i][25];                //ご紹介口座コード
            $csv_page_data[$i][78] = $page_data[$i][26];                //ご紹介口座名
            $csv_page_data[$i][79] = $page_data[$i][27];                //お振込先口座名
            $csv_page_data[$i][80] = $page_data[$i][28];                //銀行/支店名
            $csv_page_data[$i][81] = $page_data[$i][29];                //口座番号
            $csv_page_data[$i][82] = $page_data[$i][95];                //担当支店
            $csv_page_data[$i][83] = $page_data[$i][96];                //現契約担当
            $csv_page_data[$i][84] = $page_data[$i][97];                //初期契約社員
            $csv_page_data[$i][85] = $page_data[$i][98];                //巡回開始日
            $csv_page_data[$i][86] = $page_data[$i][99];                //取引履歴
            $csv_page_data[$i][87] = $page_data[$i][100];               //重要事項
            $csv_page_data[$i][88] = $page_data[$i][8];                 //巡回担当
            
            //直営の場合のみ
            if($_SESSION[group_kind] == "2"){
                $csv_page_data[$i][89] = $page_data[$i][116];               //代行先コード
                $csv_page_data[$i][90] = $page_data[$i][117];               //代行先名
            }

            //請求書作成月
            $csv_page_data[$i][91] = $page_data[$i][103];
            $csv_page_data[$i][92] = $page_data[$i][104];
            $csv_page_data[$i][93] = $page_data[$i][105];
            $csv_page_data[$i][94] = $page_data[$i][106];
            $csv_page_data[$i][95] = $page_data[$i][107];
            $csv_page_data[$i][96] = $page_data[$i][108];
            $csv_page_data[$i][97] = $page_data[$i][109];
            $csv_page_data[$i][98] = $page_data[$i][110];
            $csv_page_data[$i][99] = $page_data[$i][111];
            $csv_page_data[$i][100] = $page_data[$i][112];
            $csv_page_data[$i][101] = $page_data[$i][113];
            $csv_page_data[$i][102] = $page_data[$i][114];

            #2010-05-01 hashimoto-y
            #請求書宛先
            $csv_page_data[$i][103] = $page_data[$i][118];

        }

        $csv_file_name = "得意先マスタ".date("Ymd").".csv";
        $csv_header = array(
         "状態",
         "グループ名",
         "親子区分",
         "地区",
         "業種",
         "施設",
         "業態",
         "得意先コード",
         "得意先名１",
         "得意先名２",
         "得意先名１（フリガナ）",
         "得意先名２（フリガナ）",
         "略称",
         "略称（フリガナ）",
         "敬称",
         "代表者氏名",
         "代表者役職",
         "郵便番号",
         "住所１",
         "住所２",
         "住所３",
         "住所２（フリガナ）",
         "TEL",
         "FAX",
         "創業日",
         "担当者Email",
         "親会社名",
         "親会社TEL",
         "親会社住所",
         "資本金",
         "親会社創業日",
         "親会社代表者氏名",
         "URL",
         "担当１部署",
         "担当１役職",
         "担当１氏名",
         "担当２部署",
         "担当２役職",
         "担当２氏名",
         "担当３部署",
         "担当３役職",
         "担当３氏名",
         "担当情報備考",
         "営業時間（午前）",
         "営業時間（午後）",
         "休日",
         "請求先１（コード）",
         "請求先１（名前）",
         "請求先２（コード）",
         "請求先２（名前）",
         "与信限度",
         "回収条件",
         "取引区分",
         "締日",
         "集金日",
         "集金方法",
         "振込銀行口座",
         "振込名義１",
         "振込名義２",
         "銀行手数料負担区分",
         "契約年月日",
         "契約更新日",
         "契約期間",
         "契約終了日",
         "売上伝票発行",
         "売上伝票発行元",
         "売上伝票コメント効果",
         "売上伝票コメント",
         "請求書発行",
         "請求書送付",
         "請求書発行元",
         "請求書備考",
         "金額丸め区分",
         "消費税：課税単価",
         "消費税：端数区分",
         "消費税：課税区分",
         "設備情報等・その他",
         "ご紹介口座コード",
         "ご紹介口座名",
         "お振込先口座名",
         "銀行/支店名",
         "口座番号",
         "担当支店",
         "現契約担当",
         "初期契約社員",
         "巡回開始日",
         "取引履歴",
         "重要事項",
         "巡回担当",
         "代行先コード",
         "代行先名",
         "1月請求",
         "2月請求",
         "3月請求",
         "4月請求",
         "5月請求",
         "6月請求",
         "7月請求",
         "8月請求",
         "9月請求",
         "10月請求",
         "11月請求",
         "12月請求",
         "請求書宛先",
          );

        if($_SESSION[group_kind] != "2"){
            unset($csv_header[89]);
            unset($csv_header[90]);
        }

        $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
        $csv_data = Make_Csv($csv_page_data, $csv_header);
        Header("Content-disposition: attachment; filename=$csv_file_name");
        Header("Content-type: application/octet-stream; name=$csv_file_name");
        print $csv_data;
        exit;

}


/****************************/
// 表示用データ作成
/****************************/
// 状態表示
if($state_type == "1"){
    $state_disp = "取引中";
}else
if ($state_type == "2"){
    $state_disp = "解約・休止中";
}else
if ($state_type == "3"){
    $state_disp = "解約";
}else
if ($state_type == "4"){
    $state_disp = "全て";
}

// 一覧データがある場合
if (count($page_data) > 0){

    $i=0;
    // 一覧データでループ
    foreach ($page_data as $key => $value){

        // No.
        $page_data[$key]["no"]      = (($page_count - 1) * $limit) + $key + 1;

        // グループ
        $page_data[$key]["group"]   = $value["client_gr_cd"]."<br>".htmlspecialchars($value["client_gr_name"])."<br>";

        // 得意先コード・得意先名リンク
        $page_data[$key]["client"]  = $value["client_cd1"]."-".$value["client_cd2"]."<br>";
        $page_data[$key]["client"] .= "<a href=\"./2-1-103.php?client_id=".$value["client_id"]."\">";
        $page_data[$key]["client"] .= htmlspecialchars($value["client_name"]);
        $page_data[$key]["client"] .= "</a>";;

        // 契約内容変更リンク
        if ($value["contract_flg"] == "t"){
            $page_data[$key]["chg_lnk"] = "<a href=\"./2-1-115.php?client_id=".$value["client_id"]."\">".$value["link"]."</a>";
        }else{
            $page_data[$key]["chg_lnk"] = null;
        }

        // 担当コード・担当者名
        //$page_data[$key]["staff"]   = $value["staff_cd1"].$value["staff_cd2"]."<br>".$value["staff_name"]."<br>";
        $page_data[$key]["staff"]   = $value["charge_cd"]."<br>".htmlspecialchars($value["staff_name"])."<br>";

        // 状態
        if ($value["state"] == "1"){
            $page_data[$key]["state"] = "取引中";
        }else
        if ($value["state"] == "2"){
            $page_data[$key]["state"] = "解約・休止中";
        }else
        if ($value["state"] == "3"){
            $page_data[$key]["state"] = "解約";
        }

        // 請求先コード・請求先名（１・２）
        $page_data[$key]["claim"]   = $value["claim_cd1"]."<br>".htmlspecialchars($value["claim_name1"])."<br>";
        $page_data[$key]["claim"]  .= ($value["claim_cd2"] != null) ? $value["claim_cd2"]."<br>".htmlspecialchars($value["claim_name2"])."<br>" : null;

        //ラベル出力
        $label_shop_id = $page_data[$key]["client_id"]; 
        $ary_shop_id[$i] = $label_shop_id;
        $form->addElement("advcheckbox", "form_label_check[$i]", null, null, null, array("null", "$label_shop_id"));

        #2010-05-11 hashimoto-y
        ##2010-04-27 hashimoto-y
        #$form->addElement("advcheckbox", "form_label_check2[$i]", null, null, null, array("null", "$label_shop_id"));

        //お客様カード
        $form->addElement("advcheckbox", "form_card_check[$i]", null, null, null, array(null, "$label_shop_id"));
        $i++;

    }
}


/****************************/
// html作成
/****************************/
// ページ分け
$html_page  = Html_Page2($total_count, $page_count, 1, $limit);
$html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

// 状態表示
$html_1 .= "\n";
$html_1 .= "<b style=\"font-size: 15px; color: #555555;\">【状態： ".$state_disp."】</b>\n";
$html_1 .= "<br>\n";

// 一覧テーブル
$html_2 .= "<table class=\"List_Table\" border=\"1\" width=\"100%\">\n";
$html_2 .= "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
$html_2 .= "        <td class=\"Title_Purple\">No.</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_group")."</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">親子<br>区分</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_client_name")."</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">略称</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">契約<br>内容</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_area")."</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">TEL</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_staff_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_staff_name")."</td>\n";

//直営の場合のみ代行を表示
if($_SESSION[group_kind] == "2"){
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_act_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_act_client_name")."</td>\n";
}
if ($state_type == "4"){
$html_2 .= "        <td class=\"Title_Purple\">状態</td>\n";
}
$html_2 .= "        <td class=\"Title_Purple\">請求先コード<br>請求先名</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".$form->_elements[$form->_elementIndex["label_check_all"]]->toHtml()."</td>\n";

#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#$html_2 .= "        <td class=\"Title_Purple\">".$form->_elements[$form->_elementIndex["label_check_all2"]]->toHtml()."</td>\n";

$html_2 .= "        <td class=\"Title_Purple\">".$form->_elements[$form->_elementIndex["card_check_all"]]->toHtml()."</td>\n";
$html_2 .= "    </tr>\n";
if (count($page_data) > 0){
    foreach ($page_data as $key => $value){
        $html_2 .= "    <tr class=\"Result1\">\n";
        $html_2 .= "        <td align=\"right\">".$value["no"]."</td>\n";
        $html_2 .= "        <td>".$value["group"]."</td>\n";
        $html_2 .= "        <td align=\"center\">".$value["parents_flg"]."</td>\n";
        $html_2 .= "        <td>".$value["client"]."</td>\n";
        $html_2 .= "        <td>".htmlspecialchars($value["client_cname"])."</td>\n";
        $html_2 .= "        <td align=\"center\">".$value["chg_lnk"]."</td>\n";
        $html_2 .= "        <td align=\"center\">".htmlspecialchars($value["area_name"])."</td>\n";
        $html_2 .= "        <td>".$value["tel"]."</td>\n";
        $html_2 .= "        <td>".$value["staff"]."</td>\n";

        //直営の場合のみ代行を表示
        if($_SESSION[group_kind] == "2"){
        $html_2 .= "        <td>".$value["trust_cd"]."<br>".htmlspecialchars($value["trust_name"])."</td>\n";
        }
        if ($state_type == "4"){
        $html_2 .= "        <td align=\"center\">".$value["state"]."</td>\n";
        }
        $html_2 .= "        <td>".$value["claim"]."</td>\n";
        $html_2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_label_check[$key]"]]->toHtml()."</td>\n";

        #2010-05-11 hashimoto-y
        ##2010-04-27 hashimoto-y
        #$html_2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_label_check2[$key]"]]->toHtml()."</td>\n";

        $html_2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_card_check[$key]"]]->toHtml()."</td>\n";
        $html_2 .= "    </tr>\n";
    }
}
$html_2 .= "    <tr class=\"Result2\">";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
if($_SESSION["group_kind"] == '2'){
$html_2 .= "        <td></td>";
}
$html_2 .= "        <td></td>";
$html_2 .= "        <td>".$form->_elements[$form->_elementIndex["form_label_button"]]->toHtml()."</td>";

#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#$html_2 .= "        <td>".$form->_elements[$form->_elementIndex["form_label_button2"]]->toHtml()."</td>";

$html_2 .= "        <td>".$form->_elements[$form->_elementIndex["form_card_button"]]->toHtml()."</td>";
$html_2 .= "    </tr>";
$html_2 .= "</table>\n";

/* テーブルまとめ */
$html_l .= "\n";
$html_l .= "<table width=\"100%\">\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_1</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_page</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_2</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_page2</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "</table>\n";
$html_l .= "\n";



//ラベル出力
$javascript  = Create_Allcheck_Js ("All_Label_Check","form_label_check",$ary_shop_id);
 
#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#$javascript .= Create_Allcheck_Js ("All_Label_Check2","form_label_check2",$ary_shop_id);

$javascript .= Create_Allcheck_Js ("All_Card_Check","form_card_check",$ary_shop_id);


#2010-04-06 hashimoto-y
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
$page_title .= "(全".$all_count."件)";
$page_title .= "　".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
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
    "javascript"    => "$javascript",
));

// htmlをassign
$smarty->assign("html", array(
    "html_l"    => $html_l,
));

// テンプレートへ値を渡す
$smarty->display(basename($_SERVER["PHP_SELF"].".tpl"));

?>
