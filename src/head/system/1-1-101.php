<?php

/******************************************************/
//変更履歴  
//  (2006/03/16)
//  ・DB構成変更により、一覧SQL変更
//  ・検索フォーム表示ボタン追加  
//  (2006/07/07 kaji)
//  ・shop_gidをなくす
/******************************************************/

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-12-05      ban_0005     suzuki     CSV出力時にはサニタイジングしないように修正
 *  2007-01-23      仕様変更     watanabe-k ボタンカラー変更
 *  2007-02-22                   watanabe-k 不要機能の削除 
 *  2007-04-04                   watanabe-k 略称で検索できるように修正 
 *  2007-05-09                   kaku-m     csv出力項目を追加
 *  2007-05-21                   watanabe-k かな検索を追加 
 *  2007-07-27                   watanabe-k ラベル出力を追加 
 *  2010-01-29                   hashimoto-y非課税を追加
 *  2010-05-01      Rev.1.5　　  hashimoto-y請求書の宛先フォントサイズ変更機能の追加
 *  2010-05-13      Rev.1.5　　  hashimoto-y初期表示に検索項目だけ表示する修正
 *
*/

$page_title = "FC・取引先マスタ";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

// テンプレート関数をレジスト
$smarty->register_function("Make_Sort_Link_Tpl", "Make_Sort_Link_Tpl");

//DBに接続
$conn = Db_Connect();

// 権限チェック
$auth       = Auth_Check($conn);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;

/****************************/
//外部変数取得
/****************************/
$shop_id  = $_SESSION[shop_id];
//$shop_gid = $_SESSION[shop_gid];
//$shop_aid = $_SESSION[shop_aid];

/****************************/
//フォーム生成
/****************************/
$def_fdata = array(
    "form_output_type"  => "1",
    "form_state_type"   => "1"
);
$form->setDefaults($def_fdata);


// 出力形式
$radio1[] =& $form->createElement( "radio", null, null, "画面", "1");
$radio1[] =& $form->createElement( "radio", null, null, "CSV", "2");
$form->addGroup($radio1, "form_output_type", "出力形式");

// 状態
$radio2[] =& $form->createElement( "radio", null, null, "取引中", "1");
$radio2[] =& $form->createElement( "radio", null, null, "解約・休止中", "2");
$radio2[] =& $form->createElement( "radio", null, null, "全て", "3");
$form->addGroup($radio2, "form_state_type", "状態");

// チェックボックス
$form->addElement("checkbox", "f_check", "チェックボックス", "");

// TEL
$form->addElement("text", "form_tel", "", "size=\"15\" maxLength=\"13\" style=\"$g_form_style\"  $g_form_option");

// ショップコード
$text1[] =& $form->createElement("text", "cd1", "ショップコード１", "size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onKeyup=\"changeText(this.form,'form_shop_cd[cd1]','form_shop_cd[cd2]',6);\" $g_form_option");
$text1[] =& $form->createElement("static","","","-");
$text1[] =& $form->createElement("text", "cd2", "ショップコード２", "size=\"4\" maxLength=\"4\" style=\"$g_form_style\" $g_form_option");
$form->addGroup( $text1, "form_shop_cd", "ショップコード");

// ショップ名・社名
$form->addElement("text", "form_shop_name", "ショップ名・社名", "size=\"34\" maxLength=\"15\""." $g_form_option");

// 請求先コード
$text2[] =& $form->createElement("text", "cd1", "請求先コード１", "size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_claim_cd[cd1]','form_claim_cd[cd2]',6);\" $g_form_option");
$text2[] =& $form->createElement("static", "", "", "-");
$text2[] =& $form->createElement("text", "cd2", "請求先コード２", "size=\"4\" maxLength=\"4\" style=\"$g_form_style\" $g_form_option");
$form->addGroup( $text2, "form_claim_cd", "form_claim_cd");

// 請求先名
$form->addElement("text", "form_claim_name", "請求先名", "size=\"34\" maxLength=\"15\""." $g_form_option");

// FC・取引先区分
$select_value = Select_Get($conn, "rank");
$form->addElement("select", "form_rank_1", "FC・取引先区分", $select_value, $g_form_option_select);

// 地区
$select_value = Select_Get($conn, "area");
$form->addElement("select", "form_area_1", "地区", $select_value, $g_form_option_select);

// 担当者
$select_value = Select_Get($conn, "staff");
$form->addElement("select", "form_staff_1", "担当者", $select_value, $g_form_option_select);

// ソートリンク
$ary_sort_item = array(
    "sl_client_cd"      => "ショップコード",
    "sl_client_name"    => "ショップ名",
    "sl_shop_name"      => "社名",
    "sl_rank"           => "FC・取引先区分",
    "sl_area"           => "地区",
    "sl_claim_cd"       => "請求先コード",
    "sl_claim_name"     => "請求先名",
    "sl_staff"          => "担当者名",
    "sl_tel"            => "T E L",
    "sl_day"            => "契約年月日",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_area");

//表示ボタン
$button[] = $form->createElement("submit","show_button","表　示");

//クリアボタン
$button[] = $form->createElement("button","clear_button","クリア","onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

//検索
$form->addElement("submit","form_search_button","検索フォームを表示","");

//登録
$form->addElement("button","new_button","登録画面","onClick=\"javascript:Referer('1-1-103.php')\"");

//変更・一覧
//$form->addElement("button","change_button","変更・一覧","style=\"color: #ff0000;\" onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","change_button","変更・一覧", $g_button_color." onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

$form->addGroup($button, "form_button", "ボタン");

$form->addElement("checkbox", "label_check_all", "", "ラベル出力", "onClick=\"javascript:All_Label_Check('label_check_all');\"");
#2010-05-13 hashimoto-y
#$form->addElement("submit","form_label_button","ラベル出力","onClick=\"javascript:Post_book_vote('./1-1-111.php','#');\"");
$form->addElement("button","form_label_button","ラベル出力","onClick=\"javascript:Post_book_vote3('./1-1-111.php','#');\"");

//一括発行
/*
$form->addElement(
    "button","slip_button","一括発行",
    "onClick=\"javascript:Post_book_vote('".HEAD_DIR."system/1-1-102.php')\""
);
*/
$form->addElement("hidden", "hdn_search_flg");        //検索フォーム表示フラグ

//検索条件記憶用
$form->addElement("hidden", "hdn_output_type");   //出力形式
$form->addElement("hidden", "hdn_state_type");    //状態
$form->addElement("hidden", "hdn_shop_cd1");      //ショップコード１
$form->addElement("hidden", "hdn_shop_cd2");      //ショップコード２
$form->addElement("hidden", "hdn_shop_name");     //ショップ名
$form->addElement("hidden", "hdn_rank");          //FC・取引先区分
$form->addElement("hidden", "hdn_area");          //地区
$form->addElement("hidden", "hdn_claim_cd1");     //請求先コード１
$form->addElement("hidden", "hdn_claim_cd2");     //請求先コード２
$form->addElement("hidden", "hdn_claim_name");    //請求先名
$form->addElement("hidden", "hdn_staff");         //担当者
$form->addElement("hidden", "hdn_tel");



#2010-05-13 hashimoto-y
if($_POST["form_button"]["show_button"] == "表　示"){



/****************************/
//全件数取得
/****************************/
$fc_sql  = " SELECT ";
$fc_sql .= "     t_client.client_id,";                      // 0 得意先ID
$fc_sql .= "     t_client.client_cd1,";                     // 1 得意先コード１
$fc_sql .= "     t_client.client_cd2,";                     // 2 得意先コード２
$fc_sql .= "     t_client.client_name,";                    // 3 得意先名
$fc_sql .= "     t_client.shop_name,";                      // 4 社名
$fc_sql .= "     t_rank.rank_name,";                        // 5 FC・取引先区分
$fc_sql .= "     t_area.area_name,";                        // 6 地区
$fc_sql .= "     t_client_claim.client_cd1,";               // 7 請求先コード１
$fc_sql .= "     t_client_claim.client_cd2,";               // 8 請求先コード２
$fc_sql .= "     t_client_claim.shop_name,";                // 9 請求先名
$fc_sql .= "     t_staff.staff_name,";                      // 10 担当者名
$fc_sql .= "     t_client.state,";                          // 11 状態
$fc_sql .= "     t_client.tel,";                            // 12 Tel
$fc_sql .= "     t_client.cont_sday";                       // 13 契約年月日
//csv出力用
if($_POST["form_button"]["show_button"] == "表　示"){$output_type    = $_POST["form_output_type"];}
if($output_type == 2){
$fc_sql .= "    ,";
$fc_sql .= "    t_sbtype.sbtype_name,";                     // 14 小分類業種名
$fc_sql .= "    t_inst.inst_name,";                         // 15 施設名
$fc_sql .= "    t_bstruct.bstruct_name, ";                  // 16 業態名
$fc_sql .= "    t_staff_sv.staff_name, ";                   // 17 SV
$fc_sql .= "    t_staff_1.staff_name, ";                    // 18 担当１
$fc_sql .= "    t_staff_2.staff_name, ";                    // 19 担当２
$fc_sql .= "    t_staff_3.staff_name, ";                    // 20 担当３
$fc_sql .= "    t_staff_4.staff_name, ";                    // 21 担当４
$fc_sql .= "    t_client.client_read, ";                    // 22 得意先名（フリガナ）
$fc_sql .= "    t_client.client_cname,";                    // 23 略称
$fc_sql .= "    t_client.client_cread, ";                   // 24 略称（フリガナ）
$fc_sql .= "    CASE t_client.compellation ";               // 25 敬称
$fc_sql .= "        WHEN '1' THEN '御中' ";
$fc_sql .= "        WHEN '2' THEN '様' ";
$fc_sql .= "    END AS compellation, ";
$fc_sql .= "    t_client.shop_read, ";                      // 26 社名（フリガナ）
$fc_sql .= "    t_client.shop_name2, ";                     // 27 社名２
$fc_sql .= "    t_client.shop_read2, ";                     // 28 社名２（フリガナ）
$fc_sql .= "    t_client.post_no1, ";                       // 29 郵便番号１
$fc_sql .= "    t_client.post_no2, ";                       // 30 郵便番号２
$fc_sql .= "    t_client.address1, ";                       // 31 住所１
$fc_sql .= "    t_client.address2, ";                       // 32 住所２
$fc_sql .= "    t_client.address3, ";                       // 33 住所３
$fc_sql .= "    t_client.address_read, ";                   // 34 住所（フリガナ）
$fc_sql .= "    t_client.fax, ";                            // 35 FAX
$fc_sql .= "    t_client.email, ";                          // 36 Email
$fc_sql .= "    t_client.url, ";                            // 37 URL
$fc_sql .= "    t_client.capital, ";                        // 38 資本金
$fc_sql .= "    t_client.rep_name, ";                       // 39 代表者氏名
$fc_sql .= "    t_client.represe, ";                        // 40 代表者役職
$fc_sql .= "    t_client.rep_htel, ";                       // 41 代表者携帯
$fc_sql .= "    t_client.direct_tel, ";                     // 42 直通TEL
$fc_sql .= "    t_client.join_money, ";                     // 43 加盟金
$fc_sql .= "    t_client.guarant_money, ";                  // 44 保証金
$fc_sql .= "    t_client.royalty_rate, ";                   // 45 ロイヤリティ
$fc_sql .= "    t_client.cutoff_month, ";                   // 46 決算日（月）
$fc_sql .= "    t_client.cutoff_day, ";                     // 47 決算日（日）
$fc_sql .= "    t_client.col_terms, ";                      // 48 回収条件
$fc_sql .= "    t_client.credit_limit, ";                   // 49 与信限度
$fc_sql .= "    CASE t_client.close_day ";                  // 50 締日
$fc_sql .= "        WHEN '29' THEN '月末' ";
$fc_sql .= "        ELSE t_client.close_day || '日' ";
$fc_sql .= "    END AS close_day, ";
$fc_sql .= "    CASE t_client.pay_m ";                      // 51 集金日（月）
$fc_sql .= "        WHEN '0' THEN '当月' ";
$fc_sql .= "        WHEN '1' THEN '翌月' ";
$fc_sql .= "        ELSE t_client.pay_m || 'ヵ月後'";
$fc_sql .= "    END AS pay_m, ";
$fc_sql .= "    CASE t_client.pay_d ";                      // 52 集金日（日）
$fc_sql .= "        WHEN '29' THEN '月末' ";
$fc_sql .= "        ELSE t_client.pay_d || '日' ";
$fc_sql .= "    END AS pay_d, ";
$fc_sql .= "    CASE t_client.pay_way ";                    // 53 集金方法
$fc_sql .= "        WHEN '1' THEN '自動引落' ";
$fc_sql .= "        WHEN '2' THEN '振込' ";
$fc_sql .= "        WHEN '3' THEN '訪問集金' ";
$fc_sql .= "        WHEN '4' THEN '手形' ";
$fc_sql .= "        WHEN '5' THEN 'その他' ";
$fc_sql .= "    END AS pay_way, ";
$fc_sql .= "    t_client.pay_name, ";                       // 54 振込名義
$fc_sql .= "    t_client.account_name, ";                   // 55 口座名義
$fc_sql .= "    CASE WHEN t_client.account_id IS NOT NULL ";// 56 振込銀行
$fc_sql .= "        THEN t_bank.bank_name || ' ' || t_b_bank.b_bank_name || ' ' ||CASE t_account.deposit_kind  WHEN '1' THEN '普通 'WHEN '2' THEN '当座 ' END || t_account.account_no ";
$fc_sql .= "    END AS pay_bank, ";
$fc_sql .= "    t_trade.trade_name, ";                      // 57 得意先取引区分
$fc_sql .= "    CASE t_client.payout_m ";                   // 58 支払日（月）
$fc_sql .= "        WHEN '0' THEN '当月' ";
$fc_sql .= "        WHEN '1' THEN '翌月' ";
$fc_sql .= "        ELSE t_client.payout_m || 'ヵ月後' ";
$fc_sql .= "    END AS payout_m, ";
$fc_sql .= "    CASE t_client.payout_d";                    // 59 支払日（日）
$fc_sql .= "        WHEN '29' THEN '月末' ";
$fc_sql .= "        ELSE t_client.payout_d || '日' ";
$fc_sql .= "    END AS payout_d, ";
$fc_sql .= "    t_client.bank_name, ";                      // 60 振込口座
$fc_sql .= "    t_client.b_bank_name, ";                    // 61 振込口座略称
$fc_sql .= "    t_buy_trade.trade_name, ";                  // 62 仕入先取引区分
$fc_sql .= "    t_client.charger_name, ";                   // 63 連絡担当者氏名
$fc_sql .= "    t_client.charger, ";                        // 64 連絡担当者役職
$fc_sql .= "    t_client.cha_htel, ";                       // 65 連絡担当者携帯
$fc_sql .= "    t_client.accountant_name, ";                // 66 会計担当者氏名
$fc_sql .= "    t_client.account_tel, ";                    // 67 会計担当者携帯
$fc_sql .= "    t_client.surety_name1, ";                   // 68 保証人１氏名
$fc_sql .= "    t_client.surety_addr1, ";                   // 69 保証人１住所
$fc_sql .= "    t_client.surety_name2, ";                   // 70 保障人２氏名
$fc_sql .= "    t_client.surety_addr2, ";                   // 71 保証人２住所
$fc_sql .= "    t_client.trade_base, ";                     // 72 営業拠点
$fc_sql .= "    t_client.holiday, ";                        // 73 休日
$fc_sql .= "    t_client.trade_area, ";                     // 74 商圏
$fc_sql .= "    t_client.c_compa_name, ";                   // 75 契約会社名
$fc_sql .= "    t_client.c_compa_rep, ";                    // 76 契約代表社名
$fc_sql .= "    t_client.cont_sday, ";                      // 77 契約年月日
$fc_sql .= "    t_client.cont_rday, ";                      // 78 契約更新日
$fc_sql .= "    t_client.cont_eday, ";                      // 79 契約終了日
$fc_sql .= "    t_client.cont_peri, ";                      // 80 契約期間
$fc_sql .= "    t_client.establish_day, ";                  // 81 創業日
$fc_sql .= "    t_client.regist_day, ";                     // 82 法人登記日
$fc_sql .= "    CASE t_client.slip_out ";                   // 83 伝票発行
$fc_sql .= "        WHEN '1' THEN '有' ";
$fc_sql .= "        WHEN '2' THEN '指定' ";
$fc_sql .= "        WHEN '3' THEN '無' ";
$fc_sql .= "    END AS slip_out, ";
$fc_sql .= "    CASE t_client.deliver_effect ";             // 84 納品書コメント（効果）
$fc_sql .= "        WHEN '1' THEN 'コメント有効' ";
$fc_sql .= "        WHEN '2' THEN '個別コメント有効' ";
$fc_sql .= "        WHEN '3' THEN '全体コメント有効' ";
$fc_sql .= "    END AS deliver_effect, ";
$fc_sql .= "    t_client.deliver_note, ";                   // 85 納品書コメント
$fc_sql .= "    CASE t_client.claim_out ";                  // 86 請求書発行
$fc_sql .= "        WHEN '1' THEN '明細請求書' ";
$fc_sql .= "        WHEN '2' THEN '合計請求書' ";
$fc_sql .= "        WHEN '3' THEN '出力しない' ";
$fc_sql .= "        WHEN '4' THEN '指定' ";
$fc_sql .= "    END AS claim_out, ";
$fc_sql .= "    CASE t_client.claim_send ";                 // 87 請求書送付
$fc_sql .= "        WHEN '1' THEN '郵送' ";
$fc_sql .= "        WHEN '2' THEN 'メール' ";
$fc_sql .= "        WHEN '3' THEN '両方' ";
$fc_sql .= "    END AS claim_send, ";
$fc_sql .= "    t_claim_sheet.c_pattern_name, ";            // 88 請求書様式
$fc_sql .= "    CASE t_client.coax ";                       // 89 金額丸め区分
$fc_sql .= "        WHEN '1' THEN '切捨' ";
$fc_sql .= "        WHEN '2' THEN '四捨五入' ";
$fc_sql .= "        WHEN '3' THEN '切上' ";
$fc_sql .= "    END AS coax, ";
$fc_sql .= "    CASE t_client.tax_div ";                    // 90 消費税:課税単位
$fc_sql .= "        WHEN '1' THEN '締日単位' ";
$fc_sql .= "        WHEN '2' THEN '伝票単位' ";
$fc_sql .= "    END AS tax_div, ";
$fc_sql .= "    CASE t_client.tax_franct ";                 // 91 消費税:端数区分
$fc_sql .= "        WHEN '1' THEN '切捨' ";
$fc_sql .= "        WHEN '2' THEN '四捨五入' ";
$fc_sql .= "        WHEN '3' THEN '切上' ";
$fc_sql .= "    END AS tax_franct, ";
$fc_sql .= "    CASE t_client.c_tax_div ";                  // 92 消費税:課税区分
$fc_sql .= "        WHEN '1' THEN '外税' ";
$fc_sql .= "        WHEN '2' THEN '内税' ";
#2010-01-29 hashimoto-y
$fc_sql .= "        WHEN '3' THEN '非課税' ";
$fc_sql .= "    END AS c_tax_div, ";
$fc_sql .= "    t_client.license, ";                        // 93 取得資格・得意分野
$fc_sql .= "    t_client.s_contract, ";                     // 94 特約
$fc_sql .= "    t_client.importance, ";                     // 95 重要事項
$fc_sql .= "    t_client.other, ";                          // 96 その他
$fc_sql .= "    t_client.deal_history, ";                    // 97 取引履歴

#2010-05-01 hashimoto-y
$fc_sql .= "   CASE t_client.bill_address_font \n";         // 98 請求書宛先
$fc_sql .= "       WHEN 't' THEN '大' \n";
$fc_sql .= "       WHEN 'f' THEN '' \n";
$fc_sql .= "   END  ";

}
$fc_sql .= " FROM";
$fc_sql .= "     t_client ";
$fc_sql .= "     AS ";
$fc_sql .= "     t_client_claim,";
$fc_sql .= "     t_claim,";
$fc_sql .= "     t_area,";
$fc_sql .= "     t_rank, ";
$fc_sql .= "     t_client ";
$fc_sql .= "     LEFT JOIN ";
$fc_sql .= "     t_staff ";
$fc_sql .= "     ON t_staff.staff_id = t_client.sv_staff_id ";
//csv出力用
if($output_type == 2){
$fc_sql .= "     LEFT JOIN t_sbtype ";
$fc_sql .= "     ON t_client.sbtype_id = t_sbtype.sbtype_id ";
$fc_sql .= "     LEFT JOIN t_inst ";
$fc_sql .= "     ON t_client.inst_id = t_inst.inst_id ";
$fc_sql .= "     LEFT JOIN t_bstruct ";
$fc_sql .= "     ON t_bstruct.bstruct_id = t_client.b_struct ";
$fc_sql .= "     LEFT JOIN t_staff AS t_staff_sv ";
$fc_sql .= "     ON t_staff_sv.staff_id = t_client.sv_staff_id ";
$fc_sql .= "     LEFT JOIN t_staff AS t_staff_1 ";
$fc_sql .= "     ON t_staff_1.staff_id = t_client.b_staff_id1 ";
$fc_sql .= "     LEFT JOIN t_staff AS t_staff_2 ";
$fc_sql .= "     ON t_staff_2.staff_id = t_client.b_staff_id2 ";
$fc_sql .= "     LEFT JOIN t_staff AS t_staff_3 ";
$fc_sql .= "     ON t_staff_3.staff_id = t_client.b_staff_id3 ";
$fc_sql .= "     LEFT JOIN t_staff AS t_staff_4 ";
$fc_sql .= "     ON t_staff_4.staff_id = t_client.b_staff_id4 ";
$fc_sql .= "     LEFT JOIN t_account \n";
$fc_sql .= "     ON t_account.account_id = t_client.account_id \n";
$fc_sql .= "     LEFT JOIN t_b_bank \n";
$fc_sql .= "     ON t_b_bank.b_bank_id = t_account.b_bank_id \n";
$fc_sql .= "     LEFT JOIN t_bank \n";
$fc_sql .= "     ON t_bank.bank_id = t_b_bank.bank_id \n";
$fc_sql .= "     LEFT JOIN t_trade ";
$fc_sql .= "     ON t_trade.trade_id = t_client.trade_id ";
$fc_sql .= "     LEFT JOIN t_trade as t_buy_trade ";
$fc_sql .= "     ON t_buy_trade.trade_id = t_client.buy_trade_id ";
$fc_sql .= "     LEFT JOIN t_claim_sheet ";
$fc_sql .= "     ON t_claim_sheet.c_pattern_id = t_client.c_pattern_id ";

}
//$fc_sql .= "     t_shop_gr";
$fc_sql .= " WHERE";
$fc_sql .= "     t_client.area_id = t_area.area_id";
$fc_sql .= "     AND";
$fc_sql .= "     t_client.client_id = t_claim.client_id";
$fc_sql .= "     AND";
$fc_sql .= "     t_client_claim.client_id = t_claim.claim_id";
$fc_sql .= "     AND";
//$fc_sql .= "     t_client.attach_gid = t_shop_gr.shop_gid";
//$fc_sql .= "     AND";
//$fc_sql .= "     t_shop_gr.rank_cd = t_rank.rank_cd";
$fc_sql .= "     t_client.rank_cd = t_rank.rank_cd";
$fc_sql .= "     AND";
$fc_sql .= "     t_client.client_div = 3";
/*
//初期表示時は取引中のデータのみ
if($_POST["form_button"]["show_button"] != "表　示" || $_POST["form_search_button"] != "検索フォームを表示"){
    $fc_sql .= "     AND";
    $fc_sql .= "     t_client.state = '1'";
}

/****************************/
//画面ヘッダー作成
/****************************/
$count_sql  = " SELECT ";
$count_sql .= "     COUNT(client_cd1)";
$count_sql .= " FROM";
$count_sql .= "    t_client ";
$count_sql .= " WHERE";
$count_sql .= "    client_div = '3'";
$count_sql .= "     AND";
$count_sql .= "     t_client.state = 1";
$count_sql .= ";";
//ヘッダーに表示させる取引中データ件数
$result = Db_Query($conn, $count_sql);
$dealing_count = pg_fetch_result($result,0,0);

$count_sql  = " SELECT ";
$count_sql .= "     COUNT(client_cd1)";
$count_sql .= " FROM";
$count_sql .= "    t_client ";
$count_sql .= " WHERE";
$count_sql .= "    client_div = '3'";
$count_sql .= ";";
//ヘッダーに表示させる全件数
$result = Db_Query($conn, $count_sql);
$total_count = pg_fetch_result($result,0,0);

/****************************/
//検索フォームを表示ボタン押下処理
/****************************/
if($_POST["form_search_button"] == "検索フォームを表示"){
    $search_flg = true;
    $search_data["hdn_search_flg"] = $search_flg;
    $form->setConstants($search_data);

    $state = '1';
    $output_type = '1';

    $sort_col = $_POST["hdn_sort_col"];

    $post_flg = true;
}

/****************************/
//表示ボタン押下処理
/****************************/
if($_POST["form_button"]["show_button"] == "表　示"){
    $output_type    = $_POST["form_output_type"];          //出力形式
    $state          = $_POST["form_state_type"];           //状態
    $shop_cd1       = trim($_POST["form_shop_cd"]["cd1"]); //ショップコード1
    $shop_cd2       = trim($_POST["form_shop_cd"]["cd2"]); //ショップコード2
    $shop_name      = $_POST["form_shop_name"];            //ショップ名/社名
    $rank           = $_POST["form_rank_1"];               //FC・取引先区分
    $area           = $_POST["form_area_1"];               //地区
    $claim_cd1      = trim($_POST["form_claim_cd"]["cd1"]);//請求先コード1
    $claim_cd2      = trim($_POST["form_claim_cd"]["cd2"]);//請求先コード2
    $claim_name     = $_POST["form_claim_name"];           //請求先名
    $staff          = $_POST["form_staff_1"];              //担当者
    $tel            = $_POST["form_tel"];                  //TEL
    $post_flg       = true;                                //POSTフラグ

    $sort_col = $_POST["hdn_sort_col"];

/*****************************/
//カラムリンクが押下された場合
/*****************************/
}elseif(count($_POST) > 0
    && $_POST["form_button"]["show_button"] != "表　示"
    && $_POST["form_search_button"] != "検索フォームを表示"){

    $output_type    = $_POST["hdn_output_type"];            //出力形式
    $state          = $_POST["hdn_state_type"];             //状態
    $shop_cd1       = $_POST["hdn_shop_cd1"];               //ショップコード１
    $shop_cd2       = $_POST["hdn_shop_cd2"];               //ショップコード２
    $shop_name      = $_POST["hdn_shop_name"];              //ショップ名/社名
    $rank           = $_POST["hdn_rank"];                   //FC・取引先区分
    $area           = $_POST["hdn_area"];                   //地区
    $claim_cd1      = $_POST["hdn_claim_cd1"];              //請求先コード１
    $claim_cd2      = $_POST["hdn_claim_cd2"];              //請求先コード２
    $claim_name     = $_POST["hdn_claim_name"];             //請求先名
    $staff          = $_POST["hdn_staff"];                  //担当者
    $tel            = $_POST["hdn_tel"];                    //TEL

    $sort_col = $_POST["hdn_sort_col"];
    $post_flg       = true;
}else{
    $post_flg       = true;
    $sort_col       = "sl_area";
    $state = '1';
}

if($post_flg == true){
    /****************************/
    //検索データをセット
    /****************************/
    $set_data["form_output_type"]       = stripslashes($output_type);     //出力形式
    $set_data["form_state_type"]        = stripslashes($state);           //状態
    $set_data["form_shop_cd"]["cd1"]    = stripslashes($shop_cd1);        //ショップコード１
    $set_data["form_shop_cd"]["cd2"]    = stripslashes($shop_cd2);        //ショップコード２
    $set_data["form_shop_name"]         = stripslashes($shop_name);       //ショップ名/社名
    $set_data["form_rank_1"]            = stripslashes($rank);            //FC・取引先区分
    $set_data["form_area_1"]            = stripslashes($area);            //地区
    $set_data["form_claim_cd"]["cd1"]   = stripslashes($claim_cd1);       //請求先コード１
    $set_data["form_claim_cd"]["cd2"]   = stripslashes($claim_cd2);       //請求先コード２
    $set_data["form_claim_name"]        = stripslashes($claim_name);      //請求先名
    $set_data["form_staff_1"]           = stripslashes($staff);           //担当者
    $set_data["form_tel"]               = stripslashes($tel);             //TEL

    $set_data["hdn_output_type"]        = stripslashes($output_type);     //出力形式
    $set_data["hdn_state_type"]         = stripslashes($state);      //状態
    $set_data["hdn_shop_cd1"]           = stripslashes($shop_cd1);        //ショップコード１
    $set_data["hdn_shop_cd2"]           = stripslashes($shop_cd2);        //ショップコード２
    $set_data["hdn_shop_name"]          = stripslashes($shop_name);       //ショップ名/社名
    $set_data["hdn_rank"]               = stripslashes($rank);            //FC・取引先区分
    $set_data["hdn_area"]               = stripslashes($area);            //地区
    $set_data["hdn_claim_cd1"]          = stripslashes($claim_cd1);       //請求先コード１
    $set_data["hdn_claim_cd2"]          = stripslashes($claim_cd2);       //請求先コード２
    $set_data["hdn_claim_name"]         = stripslashes($claim_name);      //請求先名
    $set_data["hdn_staff"]              = stripslashes($staff);           //担当者
    $set_data["hdn_tel"]                = stripslashes($tel);             //TEL

    $form->setConstants($set_data);
        
    /****************************/
    //where_sql作成
    /****************************/
    //ショップコード1
    if($shop_cd1 != null){
        $shop_cd1_sql  = " AND t_client.client_cd1 LIKE '$shop_cd1%'";
    }

    //ショップコード2
    if($shop_cd2 != null){
        $shop_cd2_sql  = " AND t_client.client_cd2 LIKE '$shop_cd2%'";
    }

    //ショップ名
    if($shop_name != null){
        $shop_name_sql  = " AND (t_client.client_name LIKE '%$shop_name%' 
                            OR t_client.shop_name LIKE '%$shop_name%' 
                            OR t_client.client_read LIKE '%$shop_name%' 
                            OR t_client.client_read2 LIKE '%$shop_name%' 
                            OR t_client.client_cread LIKE '%$shop_name%' 
                            OR t_client.client_cname LIKE '%$shop_name%')";
    }

    //FC・取引先区分
    if($rank != 0){
        $rank_id_sql = " AND t_rank.rank_cd = '$rank'";
    }

    //地区
    if($area != 0){
        $area_sql = " AND t_area.area_id = '$area'";
    }

    //請求先コード1
    if($claim_cd1 != null){
        $claim_cd1_sql  = " AND t_client_claim.client_cd1 LIKE '$claim_cd1%'";
    }

    //請求先コード2
    if($claim_cd2 != null){
        $claim_cd2_sql  = " AND t_client_claim.client_cd2 LIKE '$claim_cd2%'";
    }

    //請求先名
    if($claim_name != null){
        $claim_name_sql  = " AND t_client_claim.client_name LIKE '%$claim_name%'";
    }

    //担当者
    if($staff != 0){
        $staff_sql = " AND t_staff.staff_id = '$staff'";
    }

    //TEL
    if($tel != null){
        $tel_sql  = " AND t_client.tel LIKE '$tel%'";
    }
        
    //状態
    if($state != 3){
        $state_sql = " AND t_client.state = '$state'";
    }

    $where_sql  = $shop_cd1_sql;
    $where_sql .= $shop_cd2_sql;
    $where_sql .= $shop_name_sql;
    $where_sql .= $rank_id_sql;
    $where_sql .= $area_sql;
    $where_sql .= $claim_cd1_sql;
    $where_sql .= $claim_cd2_sql;
    $where_sql .= $claim_name_sql;
    $where_sql .= $staff_sql;
    $where_sql .= $tel_sql;
        
    $where_sql .= $state_sql;
}

/****************************/
//並び替えSQL作成
/****************************/
//ショップコードの昇順判定
if($sort_col == "sl_client_cd"){
    $oder_by_sql = " ORDER BY t_client.client_cd1,t_client.client_cd2 ASC ";
//ショップ名の昇順判定
}else if($sort_col == "sl_client_name"){
    $oder_by_sql = " ORDER BY t_client.client_name,t_client.client_cd1,t_client.client_cd2 ASC ";
//社名の昇順判定
}else if($sort_col == "sl_shop_name"){
    $oder_by_sql = " ORDER BY t_client.shop_name,t_client.client_cd1,t_client.client_cd2 ASC ";
//FC・取引先区分の昇順判定
}else if($sort_col == "sl_rank"){
    $oder_by_sql = " ORDER BY t_rank.rank_name,t_client.client_cd1,t_client.client_cd2 ASC ";
//地区の昇順判定
}else if($sort_col == "sl_area"){
    $oder_by_sql = " ORDER BY t_area.area_cd,t_client.client_cd1,t_client.client_cd2 ASC ";
//請求書コードの昇順判定
}else if($sort_col == "sl_claim_cd"){
    $oder_by_sql = " ORDER BY t_client_claim.client_cd1,t_client_claim.client_cd2 ASC ";
//請求書名の昇順判定
}else if($sort_col == "sl_claim_name"){
    $oder_by_sql = " ORDER BY t_client_claim.client_name,t_client.client_cd1,t_client.client_cd2 ASC ";
//担当者の昇順判定
}else if($sort_col == "sl_staff"){
    $oder_by_sql = " ORDER BY t_staff.staff_name,t_client.client_cd1,t_client.client_cd2 ASC ";
//TELの昇順判定
}else if($sort_col == "sl_tel"){
    $oder_by_sql = " ORDER BY t_client.tel,t_client.client_cd1,t_client.client_cd2 ASC ";
//契約年月日の昇順判定
}else if($sort_col == "sl_day"){
    $oder_by_sql = " ORDER BY t_client.cont_sday,t_client.client_cd1,t_client.client_cd2 ASC ";
//デフォルトはショップコードの昇順
}else{
    $oder_by_sql = " ORDER BY t_area.area_cd,t_client.client_cd1,t_client.client_cd2 ASC ";
}

/****************************/
//表示データ作成
/****************************/
//画面選択時
if($output_type == 1 || $output_type == null){

    //該当データ
    $fc_sql .= $where_sql.$oder_by_sql.";";
    $serch_count_sql = $fc_sql;
    $serch_res = Db_Query($conn, $serch_count_sql);
    $match_count = pg_num_rows($serch_res);
    $page_data = Get_Data($serch_res, $output_type);

    //ラベル出力チェックボックス作成
    for($i = 0; $i < $match_count; $i++){
        $label_shop_id = $page_data[$i][0]; 
        $ary_shop_id[$i] = $label_shop_id;
        $form->addElement("advcheckbox", "form_label_check[$i]", null, null, null, array("null", "$label_shop_id"));
    }

}else if($output_type == 2){

    //データに表示させる全件数
    $fc_sql .= $where_sql.$oder_by_sql.";";
    $count_res = Db_Query($conn, $fc_sql);
    $match_count = pg_num_rows($count_res);
    $page_data = Get_Data($count_res,2);

    //CSV作成
    for($i = 0; $i < $match_count; $i++){
        if($page_data[$i][11] == 1){
            $page_data[$i][11] = "取引中";
        }else{
            $page_data[$i][11] = "解約・休止中";
        }
        $csv_page_data[$i][0] = $page_data[$i][11];         //状態
        $csv_page_data[$i][1] = $page_data[$i][6];          // 地区
        $csv_page_data[$i][2] = $page_data[$i][14];         // 小分類業種名
        $csv_page_data[$i][3] = $page_data[$i][15];         // 施設名
        $csv_page_data[$i][4] = $page_data[$i][16];         // 業態名
        $csv_page_data[$i][5] = $page_data[$i][17];         // SV
        $csv_page_data[$i][6] = $page_data[$i][18];         // 担当１
        $csv_page_data[$i][7] = $page_data[$i][19];         // 担当２
        $csv_page_data[$i][8] = $page_data[$i][20];         // 担当３
        $csv_page_data[$i][9] = $page_data[$i][5];         // FC・取引先区分
        $csv_page_data[$i][10] = $page_data[$i][1]."-".$page_data[$i][2];        // ショップコード
        $csv_page_data[$i][11] = $page_data[$i][3];         // ショップ名
        $csv_page_data[$i][12] = $page_data[$i][22];        // ショップ名（フリガナ）
        $csv_page_data[$i][13] = $page_data[$i][23];        // 略称
        $csv_page_data[$i][14] = $page_data[$i][24];        // 略称（フリガナ）
        $csv_page_data[$i][15] = $page_data[$i][25];        // 敬称
        $csv_page_data[$i][16] = $page_data[$i][4];         // 社名
        $csv_page_data[$i][17] = $page_data[$i][26];        // 社名（フリガナ）
        $csv_page_data[$i][18] = $page_data[$i][27];        // 社名２
        $csv_page_data[$i][19] = $page_data[$i][28];        // 社名２（フリガナ）
        $csv_page_data[$i][20] = $page_data[$i][29]."-".$page_data[$i][30];        // 郵便番号
        $csv_page_data[$i][21] = $page_data[$i][31];        // 住所１
        $csv_page_data[$i][22] = $page_data[$i][32];        // 住所２
        $csv_page_data[$i][23] = $page_data[$i][33];        // 住所３
        $csv_page_data[$i][24] = $page_data[$i][34];        // 住所（フリガナ）
        $csv_page_data[$i][25] = $page_data[$i][12];        // Tel
        $csv_page_data[$i][26] = $page_data[$i][35];        // FAX
        $csv_page_data[$i][27] = $page_data[$i][36];        // Email
        $csv_page_data[$i][28] = $page_data[$i][37];        // URL
        $csv_page_data[$i][29] = $page_data[$i][38];        // 資本金
        $csv_page_data[$i][30] = $page_data[$i][39];        // 代表者氏名
        $csv_page_data[$i][31] = $page_data[$i][40];        // 代表者役職
        $csv_page_data[$i][32] = $page_data[$i][41];        // 代表者携帯
        $csv_page_data[$i][33] = $page_data[$i][42];        // 直通TEL
        $csv_page_data[$i][34] = $page_data[$i][43];        // 加盟金
        $csv_page_data[$i][35] = $page_data[$i][44];        // 保証金
        $csv_page_data[$i][36] = $page_data[$i][45];        // ロイヤリティ
        $csv_page_data[$i][37] = (($page_data[$i][46] != null )?$page_data[$i][46]."月":"").(($page_data[$i][47] != null)?$page_data[$i][47]."日":""); // 決算日
        $csv_page_data[$i][38] = $page_data[$i][48];        // 回収条件
        $csv_page_data[$i][39] = $page_data[$i][49];        // 与信限度
        $csv_page_data[$i][40] = $page_data[$i][50];        // 締日
        $csv_page_data[$i][41] = ($page_data[$i][7]!=null)?$page_data[$i][7]."-".$page_data[$i][8]:""; // 請求先コード
        $csv_page_data[$i][42] = $page_data[$i][9];         // 請求先名
        $csv_page_data[$i][43] = $page_data[$i][10];        // 担当者名
        $csv_page_data[$i][44] = $page_data[$i][51]."の".$page_data[$i][52];        // 集金日
        $csv_page_data[$i][45] = $page_data[$i][53];        // 集金方法
        $csv_page_data[$i][46] = $page_data[$i][54];        // 振込名義
        $csv_page_data[$i][47] = $page_data[$i][55];        // 口座名義
        $csv_page_data[$i][48] = $page_data[$i][56];        // 振込銀行
        $csv_page_data[$i][49] = $page_data[$i][57];        // 得意先取引区分
        $csv_page_data[$i][50] = $page_data[$i][58]."の".$page_data[$i][59];        // 支払日
        $csv_page_data[$i][51] = $page_data[$i][60];        // 振込口座
        $csv_page_data[$i][52] = $page_data[$i][61];        // 振込口座略称
        $csv_page_data[$i][53] = $page_data[$i][62];        // 仕入先取引区分
        $csv_page_data[$i][54] = $page_data[$i][63];        // 連絡担当者氏名
        $csv_page_data[$i][55] = $page_data[$i][64];        // 連絡担当者役職
        $csv_page_data[$i][56] = $page_data[$i][65];        // 連絡担当者携帯
        $csv_page_data[$i][57] = $page_data[$i][66];        // 会計担当者氏名
        $csv_page_data[$i][58] = $page_data[$i][67];        // 会計担当者携帯
        $csv_page_data[$i][59] = $page_data[$i][68];        // 保証人１氏名
        $csv_page_data[$i][60] = $page_data[$i][69];        // 保証人１住所
        $csv_page_data[$i][61] = $page_data[$i][70];        // 保障人２氏名
        $csv_page_data[$i][62] = $page_data[$i][71];        // 保証人２住所
        $csv_page_data[$i][63] = $page_data[$i][72];        // 営業拠点
        $csv_page_data[$i][64] = $page_data[$i][73];        // 休日
        $csv_page_data[$i][65] = $page_data[$i][74];        // 商圏
        $csv_page_data[$i][66] = $page_data[$i][75];        // 契約会社名
        $csv_page_data[$i][67] = $page_data[$i][76];        // 契約代表者名
        $csv_page_data[$i][68] = $page_data[$i][77];        // 契約年月日
        $csv_page_data[$i][69] = $page_data[$i][78];        // 契約更新日
        $csv_page_data[$i][70] = $page_data[$i][80];        // 契約期間
        $csv_page_data[$i][71] = $page_data[$i][79];        // 契約終了日
        $csv_page_data[$i][72] = $page_data[$i][81];        // 創業日
        $csv_page_data[$i][73] = $page_data[$i][82];        // 法人登記日
        $csv_page_data[$i][74] = $page_data[$i][83];        // 伝票発行
        $csv_page_data[$i][75] = $page_data[$i][84];        // 納品書コメント（効果）
        $csv_page_data[$i][76] = $page_data[$i][85];        // 納品書コメント
        $csv_page_data[$i][77] = $page_data[$i][86];        // 請求書発行
        $csv_page_data[$i][78] = $page_data[$i][87];        // 請求書送付
        $csv_page_data[$i][79] = $page_data[$i][88];        // 請求書様式
        $csv_page_data[$i][80] = $page_data[$i][89];        // 金額:丸め区分
        $csv_page_data[$i][81] = $page_data[$i][90];        // 消費税:課税単位
        $csv_page_data[$i][82] = $page_data[$i][91];        // 消費税:端数区分
        $csv_page_data[$i][83] = $page_data[$i][92];        // 消費税:課税区分
        $csv_page_data[$i][84] = $page_data[$i][93];        // 取得資格・得意分野
        $csv_page_data[$i][85] = $page_data[$i][94];        // 特約
        $csv_page_data[$i][86] = $page_data[$i][97];        // 取引履歴
        $csv_page_data[$i][87] = $page_data[$i][95];        // 重要事項
        $csv_page_data[$i][88] = $page_data[$i][96];        // その他

        #2010-05-01 hashimoto-y
        $csv_page_data[$i][89] = $page_data[$i][98];        // 請求書宛先

    }

    $csv_file_name = "FC・取引先マスタ".date("Ymd").".csv";
    $csv_header = array(
            "状態",
            "地区",
            "業種",
            "施設",
            "業態",
            "SV",
            "担当１",
            "担当２",
            "担当３",
            "FC・取引先区分",
            "ショップコード",
            "ショップ名",
            "ショップ名（フリガナ）",
            "略称",
            "略称（フリガナ）",
            "敬称",
            "社名１",
            "社名１（フリガナ）",
            "社名２",
            "社名２（フリガナ）",
            "郵便番号",
            "住所１",
            "住所２",
            "住所３",
            "住所２（フリガナ）",
            "Tel",
            "FAX",
            "Email",
            "URL",
            "資本金",
            "代表者氏名",
            "代表者役職",
            "代表者携帯",
            "直通TEL",
            "加盟金",
            "保証金",
            "ロイヤリティ",
            "決算日",
            "回収条件",
            "与信限度",
            "締日",
            "請求先コード",
            "請求先名",
            "担当者名",
            "集金日",
            "集金方法",
            "振込名義",
            "口座名義",
            "振込銀行",
            "得意先取引区分",
            "支払日",
            "振込口座",
            "振込口座略称",
            "仕入先取引区分",
            "連絡担当者氏名",
            "連絡担当者役職",
            "連絡担当者携帯",
            "会計担当者氏名",
            "会計担当者携帯",
            "保証人１氏名",
            "保証人１住所",
            "保証人２氏名",
            "保証人２住所",
            "営業拠点",
            "休日",
            "商圏",
            "契約会社名",
            "契約代表者名",
            "契約年月日",
            "契約更新日",
            "契約期間",
            "契約終了日",
            "創業日",
            "法人登記日",
            "伝票発行",
            "納品書コメント（効果）",
            "納品書コメント",
            "請求書発行",
            "請求書送付",
            "請求書様式",
            "金額:丸め区分",
            "消費税:課税単位",
            "消費税:端数区分",
            "消費税:課税区分",
            "取得資格・得意分野",
            "特約",
            "取引履歴",
            "重要事項",
            "その他",
            "請求書宛先",
          );

    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($csv_page_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;
}

/*
//ショップ宛（ALLチェック）
$form->addElement('checkbox', 'form_shop_all', 'チェックボックス', 'ショップ宛',"onClick=\"javascript:All_check('form_shop_all','form_shop_check',$dealing_count)\"");

//代表者宛（ALLチェック）
$form->addElement('checkbox', 'form_staff_all', 'チェックボックス', '代表者宛',"onClick=\"javascript:All_check('form_staff_all','form_staff_check',$dealing_count)\"");

//登録カード（ALLチェック）
$form->addElement('checkbox', 'form_input_all', 'チェックボックス', '登録カード',"onClick=\"javascript:All_check('form_input_all','form_input_check',$dealing_count)\"");

//チェックボックス作成
for($i = 0; $i < $match_count; $i++){
    //ショップ宛
    $form->addElement("checkbox", "form_shop_check[$i]");

    //担当者宛
    $form->addElement("checkbox", "form_staff_check[$i]");

}
*/

//ラベル出力
$javascript  = Create_Allcheck_Js ("All_Label_Check","form_label_check",$ary_shop_id);



#2010-05-13 hashimoto-y
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
$page_menu = Create_Menu_h('system','1');

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "(取引中".$dealing_count."件/全".$total_count."件)";
$page_title .= "　".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
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
    'match_count'   => "$match_count",
    'order_msg'     => "$order_msg",
    'javascript'    => "$javascript",
    'display_flg'    => "$display_flg",
));

$smarty->assign('row',$page_data);
//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
