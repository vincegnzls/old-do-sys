<?php
/***********************************/
//変更履歴
//  （2006/05/29）売上担当者を設定するように変更
//  （2006/06/07）現在庫数表示に変更
//   (2006/06/20) 受注から起こしている売上の変更できないバグを修正
//
//   (2006/07/10) attach_gidをなくす
//   (2006/07/31) 消費税の計算方法を変更
//   (2006/08/22) 変更入力時、商品の追加ができないバグの修正
//   (2006/08/23) 受注の有無に関わらず、入力の変更ができるように修正
//   (2006/09/04) 割賦売上入力処理を追加
//   (2006/09/16) 割賦売上入力処理を正しく修正
//                デフォルト2回割賦に変更
//   (2006/09/16) 商品分類名を残すように変更  
/***********************************/

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/10/30      08-045      ふ          得意先名を略称表示に変更
 *  2006/10/30      08-061      ふ          得意先リンク（得意先一覧ダイアログ）に直営ショップが出力されていないバグを修正
 *  2006/10/31      08-005      ふ          表示しようとしている伝票が日更新実施済であれば専用モジュールへ遷移させる処理を追加
 *  2006/11/06      08-047      watanabe-k  受注残一覧にない受注番号がプルダウンに含まれているバグの修正
 *  2006/11/06      08-047      watanabe-k  取引先区分に割賦売上が表示されていなバグの修正
 *  2006/11/06      08-048      watanabe-k  売上計上日の必須エラーのメッセージが適切ではないバグの修正
 *  2006/11/06      08-050      watanabe-k  請求日のエラーメッセージが表示されていないバグの修正
 *  2006/11/06      08-047      watanabe-k  商品名が余計にサニタイズされているバグの修正
 *  2006/11/06      08-110      watanabe-k  売上日にシステム開始日以前の日付が入力可能となっているバグの修正
 *  2006/11/06      08-111      watanabe-k  請求日にシステム開始日以前の日付が入力可能となっているバグの修正
 *  2006/11/07      08-091      watanabe-k  GetIdチェック追加
 *  2006/11/07      08-092      watanabe-k  SQL条件変更
 *  2006/11/07      08-093      watanabe-k  SQL条件変更
 *  2006/11/07      08-118      watanabe-k  運送業者、直送先の略称を残すように修正
 *  2006/11/07      08-006      watanabe-k  登録前にデータ有無チェックを追加
 *  2006/11/07      08-010      watanabe-k  登録前にデータ有無チェックを追加
 *  2006/11/07      08-015      watanabe-k  登録前にデータ有無チェックを追加
 *  2006/11/09      08-131      suzuki      得意先の略称を登録
 *  2006/11/13      08-152      watanabe-k  売上登録中に受注を変更した場合にエラーが表示されないバグの修正
 *  2006/11/13      08-153      watanabe-k  受注から売上変更時、得意先名のテキストボックスがstaticでないバグの修正
 *  2006/11/13      08-154      watanabe-k  必須でない商品名に※印が付いているバグの修正
 *  2006/11/14      08-157      watanabe-k  現金取引の際に変更できないバグの修正
 *  2006/11/14      08-159      watanabe-k  確認画面でメッセージが表示されているバグの修正
 *  2006/11/30      scl_204-4-7 suzuki      現金売上の際の入金番号抽出処理を修正
 *  2006/11/30      scl_204-4-7 suzuki      現金売上の際の入金額に消費税額を含めるように修正
 *  2006/11/30      scl_0048    watanabe-k  入力するとクエリエラーが出るバグの修正    
 *  2006/12/16      scl_0085    kajioka-h   取区が割賦の場合に、売上ヘッダに分割回数2回を登録するように修正    
 *  2006/12/18                  watanabe-k  マイナス金額処理を追加
 *  2007/01/16      0052        kajioka-h   請求日の月次更新チェックと請求締日チェックのメッセージが反対になっていたのを修正
 *  2007/01/24                  ふ          自動入金クエリに、得意先正式名の登録を追加
 *  2007/01/25                  ふ          自動入金クエリの取引区分を「31:現金入金」から「39:現金売上」に変更
 *  2007/01/25                  watanabe-k  ボタンの色変更
 *  2007/02/06      B0702-001   ふ          GETしたIDの正当性チェック追加
 *  2007/02/07                  watanabe-k  受注確認していないものもリストに表示するように修正
 *  2007/02/28                  morita-d    商品名は正式名称を表示するように変更 
 *  2007/03/09      要望9-1     kajioka-h   売上計上日を変更すると請求日が変わるように変更
 *  2007/03/13                  watanabe-k  取引区分に選択したFCのデータを表示するように修正
 *  2007/03/13                  watanabe-k　同一商品の複数エラーを具体的に表示
 *  2007/05/01      なし        fukuda      得意先の状態を出力するよう修正
 *  2007/05/01      なし        fukuda      「取引中」以外の得意先も選択可能に修正
 *  2007/05/15      なし        watanabe-k  伝票の発行形式を残すように修正
 *  2007/05/18      なし        watanabe-k  直送先のプルダウンを等幅フォントに変更
 *  2007/05/22      なし        watanabe-k  受注からの売上時にも直送先を登録できるように修正
 *  2007/06/10      なし        watanabe-k  摘要をメモに変更し、テキストからテキスエリアに変更
 *  2009/07/10      なし        aoyama-n    在庫受払テーブルに得意先名（略称）を残すように修正
 *  2009/09/04      なし        aoyama-n    値引機能追加
 *  2009/09/10      なし        hashimoto-y 商品の値引き及び取引区分の返品・値引きを赤字表示に修正
 *  2009/09/28      なし        hashimoto-y 取引区分から値引きを廃止
 *  2009/10/13      なし        hashimoto-y 商品の値引き赤字表示の修正漏れ
 *  2009/10/13                  hashimoto-y 在庫管理フラグをショップ別商品情報テーブルに変更
 *  2009/10/21                  kajioka-h   合計ボタン押下時に得意先の消費税率を使っていたのを、本部の消費税率を使用するよう修正
 *  2009/10/21                  kajioka-h   消費税率を売上計上日から取得（仮）
 *  2009/11/25                  hashimoto-y 税率をTaxRateクラスから取得
 *  2010/02/03                  aoyama-n    現金返品の場合、売上金額と入金額が一致していない不具合修正 
 *  2010/09/09                  aoyama-n    変更処理でclaim_idが更新されない不具合修正 
 */

$page_title = "売上入力";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/*****************************/
// 再遷移先をSESSIONにセット
/*****************************/
// GET、POSTが無い場合
if ($_GET == null && $_POST == null){
    Set_Rtn_Page("sale");
}


/****************************/
//外部変数取得
/****************************/
$shop_id      = $_SESSION["client_id"];
$rank_cd      = $_SESSION["rank_cd"];
$e_staff_id   = $_SESSION["staff_id"];
$e_staff_name = $_SESSION["staff_name"];
$sale_id      = $_GET["sale_id"];     //売上ID
$aord_id      = $_GET["aord_id"];     //受注ID

// GETしたIDの正当性チェック
if (Get_Id_Check_Db($db_con, $_GET["sale_id"], "sale_id", "t_sale_h", "num") == false && $_GET["sale_id"] != null){
    Header("Location: ../top.php");
    exit;
}
if (Get_Id_Check_Db($db_con, $_GET["aord_id"], "aord_id", "t_aorder_h", "num") == false && $_GET["aord_id"] != null){
    Header("Location: ../top.php");
    exit;
}

//売上IDをhiddenにより保持する
if($_GET["sale_id"] != NULL){
    $set_id_data["hdn_sale_id"] = $sale_id;
    $form->setConstants($set_id_data);
}else{
    $sale_id = $_POST["hdn_sale_id"];
}
//受注IDをhiddenにより保持する
if($_GET["aord_id"] != NULL){
    $set_id_data["hdn_aord_id"] = $aord_id;
    $form->setConstants($set_id_data);
}else{
    $aord_id = $_POST["hdn_aord_id"];
}

/*****************************/
// ・日次更新されている伝票を
// ・このモジュールで開こうと
// ・した時のページ遷移処理。
/*****************************/
Get_Id_Check3($_GET["sale_id"]);
Get_Id_Check3($_GET["aord_id"]);
if ($_GET["sale_id"] != null && $_POST["form_sale_btn"] != "売上確認画面へ" && $_POST["comp_button"] != "売上OK"){
    $get_sale_id = (float)$_GET["sale_id"];
    $sql = "SELECT renew_flg FROM t_sale_h WHERE sale_id = $get_sale_id;";
    $res = Db_Query($db_con, $sql);

    if(pg_fetch_result($res, 0, 0) == "t"){
        Header("Location: ./1-2-205.php?sale_id=$get_sale_id&slip_flg=true");
        exit;   
    }
}

/****************************/
//初期設定
/****************************/

#2009-11-25 hashimoto-y
//税率クラス　インスタンス生成
$tax_rate_obj = new TaxRate($shop_id);

//表示行数
if($_POST["max_row"] != null){
    $max_row = $_POST["max_row"];
}else{
    $max_row = 5;
}
//得意先が指定されているか
if($_POST["hdn_client_id"] == null || $_POST["hdn_ware_id"] == null){
    $warning = "得意先と出荷倉庫選択してください。";
}else{
    $warning = null;
    $client_id    = $_POST["hdn_client_id"];
    $ware_id      = $_POST["hdn_ware_id"];
    $coax         = $_POST["hdn_coax"];
    $tax_franct   = $_POST["hdn_tax_franct"];
    $client_shop_id = $_POST["client_shop_id"];
    $client_name  = $_POST["form_client"]["name"];
}

//売上変更判定
if($sale_id != NULL && $client_id == NULL){
    //ヘッダー取得SQL
    $sql  = "SELECT ";
    $sql .= "    t_sale_h.sale_no,";
    $sql .= "    t_sale_h.aord_id,";
    $sql .= "    t_sale_h.client_id,";
/*
    $sql .= "    t_client.client_cd1,";
    $sql .= "    t_client.client_cd2,";
    $sql .= "    t_client.client_cname,";
*/
    $sql .= "    t_sale_h.client_cd1,";
    $sql .= "    t_sale_h.client_cd2,";
    $sql .= "    t_sale_h.client_cname,";
    $sql .= "    t_aorder_h.ord_time,";
    $sql .= "    t_aorder_h.arrival_day,";
    $sql .= "    t_sale_h.sale_day,";
    $sql .= "    t_sale_h.claim_day,";
    $sql .= "    t_sale_h.green_flg,";
    $sql .= "    t_sale_h.trans_id,";
    $sql .= "    t_sale_h.direct_id,";
    $sql .= "    t_sale_h.ware_id,";
    $sql .= "    t_sale_h.trade_id,";
    $sql .= "    t_sale_h.c_staff_id,";
    $sql .= "    t_sale_h.note,";
    $sql .= "    t_sale_h.aord_id,";
    $sql .= "    t_sale_h.ac_staff_id,";
    $sql .= "    t_client.royalty_rate, ";
    $sql .= "    t_client.rank_cd, ";
    $sql .= "    t_sale_h.enter_day, ";
    $sql .= "    t_sale_h.change_day ";
    $sql .= "FROM ";
    $sql .= "    t_sale_h";
    $sql .= "    INNER JOIN t_client ON t_sale_h.client_id = t_client.client_id ";
    $sql .= "    LEFT JOIN t_aorder_h ON t_sale_h.aord_id = t_aorder_h.aord_id ";
    $sql .= "WHERE ";
    $sql .= "    t_sale_h.sale_id = $sale_id";
    $sql .= "    AND";
    $sql .= "    t_sale_h.shop_id = $shop_id";
    $sql .= "    AND";
    $sql .= "    t_sale_h.renew_flg = 'f';";

    $result = Db_Query($db_con, $sql);

    //GETデータ判定
    Get_Id_Check($result);
    $h_data_list = Get_Data($result,2);

    //データ取得SQL
    $sql  = "SELECT ";
    $sql .= "    t_goods.goods_id,";
    $sql .= "    t_goods.name_change,";
    $sql .= "    t_sale_d.goods_cd,";
    //$sql .= "    t_sale_d.goods_name,";
    $sql .= "    t_sale_d.official_goods_name,";
    $sql .= "    t_sale_d.num,";
    $sql .= "    t_sale_d.tax_div,";
    $sql .= "    t_sale_d.cost_price,";
    $sql .= "    t_sale_d.sale_price,";
    $sql .= "    t_sale_d.cost_amount,";
    $sql .= "    t_sale_d.sale_amount,";
    //受注IDがある場合は、受注数を表示
//  if($h_data_list[0][17] != NULL){
        $sql .= "   t_aorder_d.num, ";
//  }
    #2009-10-13_1 hashimoto-y
    #$sql .= "    CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num,";
    $sql .= "    CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num,";

    //aoyama-n 2009-09-04
    #$sql .= "    t_goods.royalty ";
    $sql .= "    t_goods.royalty,";
    $sql .= "    t_goods.discount_flg ";
    $sql .= "FROM ";
    $sql .= "    t_sale_d ";
    $sql .= "    INNER JOIN t_sale_h ON t_sale_h.sale_id = t_sale_d.sale_id ";
    $sql .= "    INNER JOIN t_goods ON t_sale_d.goods_id = t_goods.goods_id ";
//  if($h_data_list[0][17] != NULL){
        $sql .= "    LEFT JOIN t_aorder_d ON t_sale_d.aord_d_id = t_aorder_d.aord_d_id ";
//  }
    $sql .= "   LEFT JOIN ";
    $sql .= "   (SELECT";
    $sql .= "       goods_id,";
    $sql .= "       SUM(stock_num)AS stock_num";
    $sql .= "   FROM";
    $sql .= "       t_stock";
    $sql .= "   WHERE";
    $sql .= "       shop_id = $shop_id";
    $sql .= "       AND";
    $sql .= "       ware_id = ".$h_data_list[0][13];
    $sql .= "       GROUP BY t_stock.goods_id";
    $sql .= "   )AS t_stock";
    $sql .= "   ON t_sale_d.goods_id = t_stock.goods_id ";
    #2009-10-13_1 hashimoto-y
    $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id\n";

    $sql .= "WHERE ";
    $sql .= "    t_sale_h.sale_id = $sale_id ";
    $sql .= "AND ";
    $sql .= "    t_sale_h.shop_id = $shop_id ";
    #2009-10-13_1 hashimoto-y
    $sql .= "AND\n";
    $sql .= "    t_goods_info.shop_id = $shop_id ";

    $sql .= "ORDER BY t_sale_d.line;";

    $result = Db_Query($db_con, $sql);
    $data_list = Get_Data($result,2);

    //得意先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id,";
    $sql .= "   coax,";
    $sql .= "   tax_franct,";
    //$sql .= "   attach_gid ";
    $sql .= "   shop_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_id = ".$h_data_list[0][2];
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $client_list = Get_Data($result,2);

    /****************************/
    //フォームに値を復元
    /****************************/
    $sale_money = NULL;                        //商品の売上金額
    $tax_div    = NULL;                        //課税区分

    if ($_POST["client_search_flg"] != true){
    //ヘッダー復元
    $update_goods_data["form_sale_no"]                 = $h_data_list[0][0];  //伝票番号
    $update_goods_data["form_ord_no"]                  = $h_data_list[0][1];  //受注番号

    $update_goods_data["form_client"]["cd1"]           = $h_data_list[0][3];  //受注先コード１
    $update_goods_data["form_client"]["cd2"]           = $h_data_list[0][4];  //受注先コード２
    $update_goods_data["form_client"]["name"]          = $h_data_list[0][5];  //受注先名

    //受注日を年月日に分ける
    $ex_ord_day = explode('-',$h_data_list[0][6]);
    $update_goods_data["form_order_day"]["y"]          = $ex_ord_day[0];   //受注日
    $update_goods_data["form_order_day"]["m"]          = $ex_ord_day[1];   
    $update_goods_data["form_order_day"]["d"]          = $ex_ord_day[2];   

    //出荷予定日を年月日に分ける
    $ex_arr_day = explode('-',$h_data_list[0][7]);
    $update_goods_data["form_arrival_day"]["y"]        = $ex_arr_day[0];   //出荷予定日
    $update_goods_data["form_arrival_day"]["m"]        = $ex_arr_day[1];   
    $update_goods_data["form_arrival_day"]["d"]        = $ex_arr_day[2];   

    //売上計上日を年月日に分ける
    $ex_sale_day = explode('-',$h_data_list[0][8]);
    $update_goods_data["form_sale_day"]["y"]           = $ex_sale_day[0];  //売上計上日
    $update_goods_data["form_sale_day"]["m"]           = $ex_sale_day[1];   
    $update_goods_data["form_sale_day"]["d"]           = $ex_sale_day[2];   

    //請求日を年月日に分ける
    $ex_claim_day = explode('-',$h_data_list[0][9]);
    $update_goods_data["form_claim_day"]["y"]          = $ex_claim_day[0];  //請求日
    $update_goods_data["form_claim_day"]["m"]          = $ex_claim_day[1];   
    $update_goods_data["form_claim_day"]["d"]          = $ex_claim_day[2];   

    //チェックを付けるか判定
    if($h_data_list[0][10]=='t'){
        $update_goods_data["form_trans_check"]         = $h_data_list[0][10];  //グリーン指定
    }

    $update_goods_data["form_trans_select"]            = $h_data_list[0][11];  //運送業者
    $update_goods_data["form_direct_select"]           = $h_data_list[0][12];  //直送先
    $update_goods_data["form_ware_select"]             = $h_data_list[0][13];  //倉庫
    $update_goods_data["hdn_ware_id"]                  = $h_data_list[0][13];  //倉庫
    $update_goods_data["trade_sale_select"]            = $h_data_list[0][14];  //取引区分
    $update_goods_data["form_cstaff_select"]           = $h_data_list[0][15];  //売上担当者
    $update_goods_data["form_note"]                    = $h_data_list[0][16];  //備考
    $update_goods_data["form_staff_select"]            = $h_data_list[0][18];  //受注担当者
    $update_goods_data["hdn_aord_id"]                  = $h_data_list[0][17];  //受注ID
    $aord_id                                           = $h_data_list[0][17];
    $update_goods_data["hdn_royalty_rate"]             = $h_data_list[0][19];  //ロイヤリティ
    $update_goods_data["hdn_rank_cd"]                  = $h_data_list[0][20];  //顧客区分
    $update_goods_data["hdn_sale_enter_day"]           = $h_data_list[0][21];  //登録日
    $update_goods_data["hdn_aord_change_day"]          = $h_data_list[0][22];  //変更日
    }

    //データ復元
    for($i=0;$i<count($data_list);$i++){
        $update_goods_data["hdn_goods_id"][$i]         = $data_list[$i][0];   //商品ID

        $update_goods_data["hdn_name_change"][$i]      = $data_list[$i][1];   //品名変更フラグ
        $hdn_name_change[$i]                           = $data_list[$i][1];   //POSTする前に商品名の変更不可判定を行なう為

        $update_goods_data["form_goods_cd"][$i]        = $data_list[$i][2];   //商品CD
        $update_goods_data["form_goods_name"][$i]      = $data_list[$i][3];   //商品名
        if($h_data_list[0][17] != NULL){
            $update_goods_data["form_aorder_num"][$i]  = $data_list[$i][10];  //受注数
        }
        $update_goods_data["form_stock_num"][$i]       = $data_list[$i][11];  //現在庫数
        $update_goods_data["hdn_royalty"][$i]          = $data_list[$i][12];  //ロイヤリティ
        $update_goods_data["form_sale_num"][$i]        = $data_list[$i][4];   //出荷数
        $update_goods_data["hdn_tax_div"][$i]          = $data_list[$i][5];   //課税区分

        //原価単価を整数部と少数部に分ける
        $cost_price = explode('.', $data_list[$i][6]);
        $update_goods_data["form_cost_price"][$i]["i"] = $cost_price[0];     //原価単価
        $update_goods_data["form_cost_price"][$i]["d"] = ($cost_price[1] != null)? $cost_price[1] : '00';     
        $update_goods_data["form_cost_amount"][$i]     = number_format($data_list[$i][8]);  //原価金額

        //売上単価を整数部と少数部に分ける
        $sale_price = explode('.', $data_list[$i][7]);
        $update_goods_data["form_sale_price"][$i]["i"] = $sale_price[0];     //売上単価
        $update_goods_data["form_sale_price"][$i]["d"] = ($sale_price[1] != null)? $sale_price[1] : '00';
        $update_goods_data["form_sale_amount"][$i]     = number_format($data_list[$i][9]);  //売上金額

        $sale_money[]                                  = $data_list[$i][9];  //売上金額合計
        $tax_div[]                                     = $data_list[$i][5];  //課税区分
        //aoyama-n 2009-09-04
        $update_goods_data["hdn_discount_flg"][$i]     = $data_list[$i][13]; //値引フラグ
    }

    //得意先情報復元
    $client_id      = $client_list[0][0];        //得意先ID
    $coax           = $client_list[0][1];        //丸め区分（金額）
    $tax_franct     = $client_list[0][2];        //端数区分（消費税）
    //$attach_gid     = $client_list[0][3];        //所属グループ
    $client_shop_id = $client_list[0][3];        //得意先のショップID
    $warning = null;
    $update_goods_data["hdn_client_id"]       = $client_id;
    $update_goods_data["hdn_coax"]            = $coax;
    $update_goods_data["hdn_tax_franct"]      = $tax_franct;
    //$update_goods_data["attach_gid"]          = $attach_gid;
    $update_goods_data["client_shop_id"]      = $client_shop_id;

/*
    //現在の消費税率
    $sql  = "SELECT ";
    $sql .= "    tax_rate_n ";
    $sql .= "FROM ";
    $sql .= "    t_client ";
    $sql .= "WHERE ";
    $sql .= "    client_id = $shop_id;";
    $result = Db_Query($db_con, $sql); 
    $tax_num = pg_fetch_result($result, 0,0);
*/
	//2009-10-21 kajioka-h 消費税率取得
	#$tax_num = Get_TaxRate_Day($db_con, $shop_id, $client_id, $h_data_list[0][8]);

    #2009-11-25 hashimoto-y
    $tax_rate_obj->setTaxRateDay($h_data_list[0][8]);
    $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

    $total_money = Total_Amount($sale_money, $tax_div,$coax,$tax_franct,$tax_num,$client_id, $db_con);

    $sale_money = number_format($total_money[0]);
    $tax_money  = number_format($total_money[1]);
    $st_money   = number_format($total_money[2]);

    //フォームに値セット
    $update_goods_data["form_sale_total"]      = $sale_money;
    $update_goods_data["form_sale_tax"]        = $tax_money;
    $update_goods_data["form_sale_money"]      = $st_money;
    $update_goods_data["sum_button_flg"]       = "";

    $form->setConstants($update_goods_data);

    //表示行数
    if($_POST["max_row"] != NULL){
        $max_row = $_POST["max_row"];
    }else{
        //受注データの数
        $max_row = count($data_list);
    }

//売上入力判定
//}else if($aord_id != NULL && $aord_id != 0){
}else if($aord_id != NULL && $aord_id != 0 && $client_id == null){

    //自動採番の伝票番号取得
    $sql  = "SELECT";
    $sql .= "   MAX(sale_no)";
    $sql .= " FROM";
    $sql .= "   t_sale_h";
    $sql .= " WHERE";
    $sql .= "   shop_id = $shop_id";
    $sql .= ";";
    $result = Db_Query($db_con, $sql);
    $sale_no = pg_fetch_result($result, 0 ,0);
    $sale_no = $sale_no +1;
    $sale_no = str_pad($sale_no, 8, 0, STR_PAD_LEFT);

    //ヘッダー取得SQL
    $sql  = "SELECT ";
//  $sql .= "    t_aorder_h.ord_no,";
    $sql .= "    t_aorder_h.aord_id,";
    $sql .= "    t_aorder_h.client_id,";
/*
    $sql .= "    t_client.client_cd1,";
    $sql .= "    t_client.client_cd2,";
    $sql .= "    t_client.client_cname,";
*/
    $sql .= "    t_aorder_h.client_cd1,";
    $sql .= "    t_aorder_h.client_cd2,";
    $sql .= "    t_aorder_h.client_cname,";
    $sql .= "    t_aorder_h.ord_time,";
    $sql .= "    t_aorder_h.arrival_day,";
    $sql .= "    t_aorder_h.green_flg,";
    $sql .= "    t_aorder_h.trans_id,";
    $sql .= "    t_aorder_h.direct_id,";
    $sql .= "    t_aorder_h.ware_id,";
    $sql .= "    t_aorder_h.trade_id,";
    $sql .= "    t_aorder_h.c_staff_id, ";
    $sql .= "    t_client.royalty_rate, ";
    $sql .= "    t_aorder_h.enter_day, ";
    $sql .= "    t_aorder_h.change_day ";
    $sql .= "FROM ";
    $sql .= "    t_aorder_h";
    $sql .= "    INNER JOIN t_client ON t_aorder_h.client_id = t_client.client_id ";
    $sql .= "WHERE ";
    $sql .= "    t_aorder_h.aord_id = $aord_id ";
    $sql .= "AND ";
    $sql .= "    t_aorder_h.shop_id = $shop_id;";

    $result = Db_Query($db_con, $sql);
    //GETデータ判定
    Get_Id_Check($result);
    $h_data_list = Get_Data($result,2);

    //データ取得SQL
    $sql  = "SELECT";
    $sql .= "   t_goods.goods_id,";
    $sql .= "   t_goods.goods_cd,";
    //$sql .= "   t_aorder_d.goods_name,";
    $sql .= "   t_aorder_d.official_goods_name,";
    $sql .= "   t_aorder_d.num,";
    $sql .= "   t_aorder_d.tax_div,";
    $sql .= "   t_aorder_d.cost_price,";
    $sql .= "   t_aorder_d.sale_price,";
    $sql .= "   t_aorder_d.cost_amount,";
    $sql .= "   t_aorder_d.sale_amount,";
    #2009-10-13_1 hashimoto-y
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num, ";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num, ";

    $sql .= "   t_goods.royalty,";
    //aoyama-n 2009-09-04
    #$sql .= "   t_goods.name_change";
    $sql .= "   t_goods.name_change,";
    $sql .= "   t_goods.discount_flg";
    $sql .= " FROM";
    $sql .= "   t_aorder_d ";

    $sql .= "   INNER JOIN  t_aorder_h ON t_aorder_d.aord_id = t_aorder_h.aord_id";
    $sql .= "   INNER JOIN  t_goods ON t_aorder_d.goods_id = t_goods.goods_id";

    $sql .= "   LEFT JOIN ";
    $sql .= "   (SELECT";
    $sql .= "       goods_id,";
    $sql .= "       SUM(stock_num)AS stock_num";
    $sql .= "   FROM";
    $sql .= "       t_stock";
    $sql .= "   WHERE";
    $sql .= "       shop_id = $shop_id";
    $sql .= "       AND";
    $sql .= "       ware_id = ".$h_data_list[0][10];
    $sql .= "       GROUP BY t_stock.goods_id";
    $sql .= "   )AS t_stock";
    $sql .= "   ON t_aorder_d.goods_id = t_stock.goods_id ";
    #2009-10-13_1 hashimoto-y
    $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

    $sql .= " WHERE ";
    $sql .= "       t_aorder_h.aord_id = $aord_id ";
    $sql .= " AND ";
    $sql .= "       t_aorder_h.shop_id = $shop_id ";
    #2009-10-13_1 hashimoto-y
    $sql .= " AND\n";
    $sql .= "       t_goods_info.shop_id = $shop_id ";

    $sql .= " ORDER BY t_aorder_d.line;";

    $result = Db_Query($db_con, $sql);
    $data_list = Get_Data($result,2);

    //得意先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id,";
    $sql .= "   coax,";
    $sql .= "   tax_franct,";
    //$sql .= "   attach_gid ";
    $sql .= "   shop_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_id = ".$h_data_list[0][1];
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $client_list = Get_Data($result);

    /****************************/
    //フォームに値を復元
    /****************************/
    $sale_money = NULL;                        //商品の売上金額
    $tax_div    = NULL;                        //課税区分

    //ヘッダー復元
    $update_goods_data["form_sale_no"]                 = $sale_no;            //伝票番号
    $update_goods_data["form_ord_no"]                  = $h_data_list[0][0];  //受注番号

    $update_goods_data["form_client"]["cd1"]           = $h_data_list[0][2];  //得意先コード１
    $update_goods_data["form_client"]["cd2"]           = $h_data_list[0][3];  //得意先コード２
    $update_goods_data["form_client"]["name"]          = $h_data_list[0][4];  //得意先名

    //受注日を年月日に分ける
    $ex_ord_day = explode('-',$h_data_list[0][5]);
    $update_goods_data["form_order_day"]["y"]          = $ex_ord_day[0];      //受注日
    $update_goods_data["form_order_day"]["m"]          = $ex_ord_day[1];   
    $update_goods_data["form_order_day"]["d"]          = $ex_ord_day[2];   

    //出荷予定日を年月日に分ける
    $ex_arr_day = explode('-',$h_data_list[0][6]);
    $update_goods_data["form_arrival_day"]["y"]        = $ex_arr_day[0];      //出荷予定日
    $update_goods_data["form_arrival_day"]["m"]        = $ex_arr_day[1];   
    $update_goods_data["form_arrival_day"]["d"]        = $ex_arr_day[2];   

    //売上計上日を年月日に分ける
    $update_goods_data["form_sale_day"]["y"]           = date('Y');  //売上計上日
    $update_goods_data["form_sale_day"]["m"]           = date('m');   
    $update_goods_data["form_sale_day"]["d"]           = date('d');   

    //請求日を年月日に分ける
    $update_goods_data["form_claim_day"]["y"]          = date('Y');  //請求日
    $update_goods_data["form_claim_day"]["m"]          = date('m');   
    $update_goods_data["form_claim_day"]["d"]          = date('d');   

    $trans_check                                       = $h_data_list[0][7];  //グリーン指定
    $update_goods_data["form_trans_select"]            = $h_data_list[0][8];  //運送業者

    $update_goods_data["form_direct_select"]           = $h_data_list[0][9];  //直送先
    $update_goods_data["form_ware_select"]             = $h_data_list[0][10];  //倉庫
    $update_goods_data["hdn_ware_id"]                  = $h_data_list[0][10];   //倉庫（hidden用）
    $update_goods_data["trade_sale_select"]            = $h_data_list[0][11]; //取引区分
    $update_goods_data["form_staff_select"]            = $h_data_list[0][12]; //担当者

    $update_goods_data["hdn_royalty_rate"]                  = $h_data_list[0][13]; //ロイヤリティ
    $update_goods_data["hdn_aord_enter_day"]            = $h_data_list[0][14]; //登録日
    $update_goods_data["hdn_aord_change_day"]          = $h_data_list[0][15]; //変更日

    //データ復元
    for($i=0;$i<count($data_list);$i++){
        $update_goods_data["hdn_goods_id"][$i]         = $data_list[$i][0];   //商品ID

        $update_goods_data["form_goods_cd"][$i]        = $data_list[$i][1];   //商品CD
        $update_goods_data["form_goods_name"][$i]      = $data_list[$i][2];   //商品名

        $update_goods_data["form_aorder_num"][$i]      = $data_list[$i][3];   //受注数
        $update_goods_data["form_stock_num"][$i]       = $data_list[$i][9];   //現在庫数
        $update_goods_data["form_sale_num"][$i]        = $data_list[$i][3];   //出荷数
        $update_goods_data["hdn_tax_div"][$i]          = $data_list[$i][4];   //課税区分

        //原価単価を整数部と少数部に分ける
        $cost_price = explode('.', $data_list[$i][5]);
        $update_goods_data["form_cost_price"][$i]["i"] = $cost_price[0];     //原価単価
        $update_goods_data["form_cost_price"][$i]["d"] = ($cost_price[1] != null)? $cost_price[1] : '00';     
        $update_goods_data["form_cost_amount"][$i]     = number_format($data_list[$i][7]);  //原価金額

        //売上単価を整数部と少数部に分ける
        $sale_price = explode('.', $data_list[$i][6]);
        $update_goods_data["form_sale_price"][$i]["i"] = $sale_price[0];     //売上単価
        $update_goods_data["form_sale_price"][$i]["d"] = ($sale_price[1] != null)? $sale_price[1] : '00';
        $update_goods_data["form_sale_amount"][$i]     = number_format($data_list[$i][8]);  //売上金額

        $update_goods_data["hdn_royalty"][$i]          = $data_list[$i][10]; //ロイヤリティ

        $sale_money[]                                  = $data_list[$i][8];  //売上金額合計
        $tax_div[]                                     = $data_list[$i][4];  //課税区分

        $update_goods_data["hdn_name_change"][$i]      = $data_list[$i][11];   //品名変更フラグ
        $hdn_name_change[$i]                           = $data_list[$i][11];   //POSTする前に商品名の変更不可判定を行なう為
        //aoyama-n 2009-09-04
        $update_goods_data["hdn_discount_flg"][$i]     = $data_list[$i][12];   //値引フラグ
    }

    //得意先情報復元
    $client_id      = $client_list[0][0];        //得意先ID
    $coax           = $client_list[0][1];        //丸め区分（金額）
    $tax_franct     = $client_list[0][2];        //端数区分（消費税）
    //$attach_gid     = $client_list[0][3];        //所属グループ
    $client_shop_id = $client_list[0][3];        //得意先のショップID
    $warning = null;
    $update_goods_data["hdn_client_id"]       = $client_id;
    $update_goods_data["hdn_coax"]            = $coax;
    $update_goods_data["hdn_tax_franct"]      = $tax_franct;
    //$update_goods_data["attach_gid"]          = $attach_gid;
    $update_goods_data["client_shop_id"]      = $client_shop_id;
    $update_goods_data["form_cstaff_select"] = $e_staff_id;

/*
    //現在の消費税率
    $sql  = "SELECT ";
    $sql .= "    tax_rate_n ";
    $sql .= "FROM ";
    $sql .= "    t_client ";
    $sql .= "WHERE ";
    $sql .= "    client_id = $shop_id;";
    $result = Db_Query($db_con, $sql); 
    $tax_num = pg_fetch_result($result, 0,0);
*/
	//2009-10-21 kajioka-h 消費税率取得
	#$tax_num = Get_TaxRate_Day($db_con, $shop_id, $client_id, date("Y-m-d"));

    #2009-11-25 hashimoto-y
    $tax_rate_obj->setTaxRateDay(date("Y-m-d"));
    $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

    $total_money = Total_Amount($sale_money, $tax_div,$coax,$tax_franct,$tax_num,$client_id, $db_con);

    $sale_money = number_format($total_money[0]);
    $tax_money  = number_format($total_money[1]);
    $st_money   = number_format($total_money[2]);

    //フォームに値セット
    $update_goods_data["form_sale_total"]      = $sale_money;
    $update_goods_data["form_sale_tax"]        = $tax_money;
    $update_goods_data["form_sale_money"]      = $st_money;
    $update_goods_data["sum_button_flg"]       = "";

    $form->setConstants($update_goods_data);
    //受注データの数
    $max_row = count($data_list);

}else{
    //自動採番の伝票番号取得
    $sql  = "SELECT";
    $sql .= "   MAX(sale_no)";
    $sql .= " FROM";
    $sql .= "   t_sale_h";
    $sql .= " WHERE";
    $sql .= "   shop_id = $shop_id";
    $sql .= ";";
    $result = Db_Query($db_con, $sql);
    $sale_no = pg_fetch_result($result, 0 ,0);
    $sale_no = $sale_no +1;
    $sale_no = str_pad($sale_no, 8, 0, STR_PAD_LEFT);

    $def_data["form_sale_no"] = $sale_no;

    //担当者
    $def_data["form_cstaff_select"] = $e_staff_id;
//  $def_data["form_staff_select"] = $e_staff_id;
    //取引区分
    $def_data["trade_sale_select"] = 11;

    //売上計上日
    $def_data["form_sale_day"]["y"] = date("Y");
    $def_data["form_sale_day"]["m"] = date("m");
    $def_data["form_sale_day"]["d"] = date("d");
    //請求日
    $def_data["form_claim_day"]["y"] = date("Y"); 
    $def_data["form_claim_day"]["m"] = date("m"); 
    $def_data["form_claim_day"]["d"] = date("d"); 

    //倉庫
    $sql  = "SELECT";
    $sql .= "   ware_id ";
    $sql .= "FROM";
    $sql .= "   t_client ";
    $sql .= "WHERE";
    $sql .= "   client_id = $shop_id";
    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    $def_ware_id = pg_fetch_result($result,0,0);

    $def_data["form_ware_select"] = $def_ware_id;
    $def_data["hdn_ware_id"]      = $def_ware_id;

    $form->setDefaults($def_data);

    //表示行数
    if($_POST["max_row"] != NULL){
        $max_row = $_POST["max_row"];
    }else{
        $max_row = 5;
    }
}

//初期表示位置変更
$form_potision = "<body bgcolor=\"#D8D0C8\">";

//削除行数
$del_history[] = NULL; 

/****************************/
//行数追加処理
/****************************/
if($_POST["add_row_flg"]==true){
    if($_POST["max_row"] == NULL){
        //初期値はPOSTが無い為、
        $max_row = 10;
    }else{
        //最大行に、＋１する
        $max_row = $_POST["max_row"]+5;
    }
    //行数追加フラグをクリア
    $add_row_data["add_row_flg"] = "";
    $form->setConstants($add_row_data);
}

/****************************/
//行削除処理
/****************************/
if($_POST["del_row"] != ""){

    //削除リストを取得
    $del_row = $_POST["del_row"];

    //削除履歴を配列にする。
    $del_history = explode(",", $del_row);
    //削除した行数
    $del_num     = count($del_history)-1;
}

//***************************/
//最大行数をhiddenにセット
/****************************/
$max_row_data["max_row"] = $max_row;
$form->setConstants($max_row_data);

//***************************/
//グリーン指定チェック処理
/****************************/
//チェックの場合は、運送業者のプルダウンの値を変更する
if($_POST["trans_check_flg"] == true){
    $where  = " WHERE ";
    $where .= "    shop_id = $shop_id";
    $where .= " AND";
    $where .= "    green_trans = 't'";

    //初期化
    $trans_data["trans_check_flg"]     = "";
    $trans_data["show_button_flg"]     = "";        //表示ボタン
    $trans_data["form_ord_no"]         = "";        //受注番号
    $form->setConstants($trans_data);
}else{
    $where = "";
}

/****************************/
//部品作成
/****************************/
//伝票番号
$form->addElement(
    "text","form_sale_no","",
    "style=\"color : #585858; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);

//受注番号
//売上照会・伝票発行から遷移した＆受注IDが無い
$select_value[] = null;
$sql  = "SELECT\n";
$sql .= "    t_aorder_h.aord_id,\n";
$sql .= "    t_aorder_h.ord_no\n";
$sql .= " FROM\n";
$sql .= "    t_aorder_h\n";
$sql .= " WHERE\n";
$sql .= "    t_aorder_h.shop_id = $shop_id \n";
$sql .= " AND \n";
$sql .= "    t_aorder_h.ps_stat < '3' \n";
//$sql .= " AND \n";
//$sql .= "    (t_aorder_h.check_flg = 't' OR t_aorder_h.fc_ord_id IS NOT NULL) \n";
$sql .= " ORDER BY t_aorder_h.ord_no\n";
$sql .= ";\n ";

$result = Db_Query($db_con, $sql);
$num = pg_num_rows($result);
for($i = 0; $i < $num; $i++){
    $ord_id = pg_fetch_result($result,$i,0);
    $ord_no = pg_fetch_result($result,$i,1);
    $select_value[$ord_id] = $ord_no;
}
$freeze = $form->addElement("select","form_ord_no","",$select_value, $g_form_option_select);

if($sale_id != NULL && $aord_id != NULL){
    $freeze->freeze();
}

//受注日
$form_order_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\"
    style=\"color : #585858;$g_form_style
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_order_day[] = $form->createElement("static","","","-");
$form_order_day[] = $form->createElement(
    "text","m","","size=\"2\" maxLength=\"2\" 
    style=\"color : #585858;$g_form_style
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_order_day[] = $form->createElement("static","","","-");
$form_order_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"color : #585858;$g_form_style
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form->addGroup( $form_order_day,"form_order_day","");

//出荷予定日
$form_arrival_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\"
    style=\"color : #585858;$g_form_style
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_arrival_day[] = $form->createElement("static","","","-");
$form_arrival_day[] = $form->createElement(
    "text","m","","size=\"2\" maxLength=\"2\" 
    style=\"color : #585858;$g_form_style
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_arrival_day[] = $form->createElement("static","","","-");
$form_arrival_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"color : #585858;$g_form_style
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form->addGroup( $form_arrival_day,"form_arrival_day","");

//売上計上日
$text = NULL;
$text[] =& $form->createElement("text","y","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_sale_day[y]','form_sale_day[m]',4)\" onFocus=\"onForm_today(this,this.form,'form_sale_day[y]','form_sale_day[m]','form_sale_day[d]')\" onBlur=\"blurForm(this)\" onChange=\"Claim_Day_Change('form_sale_day', 'form_claim_day')\"");
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement("text","m","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_sale_day[m]','form_sale_day[d]',2)\" onFocus=\"onForm_today(this,this.form,'form_sale_day[y]','form_sale_day[m]','form_sale_day[d]')\" onBlur=\"blurForm(this)\" onChange=\"Claim_Day_Change('form_sale_day', 'form_claim_day')\"");
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement("text","d","テキストフォーム","size=\"1\" maxLength=\"2\" style=\"$g_form_style\" onFocus=\"onForm_today(this,this.form,'form_sale_day[y]','form_sale_day[m]','form_sale_day[d]')\" onBlur=\"blurForm(this)\" onChange=\"Claim_Day_Change('form_sale_day', 'form_claim_day')\"");
$form->addGroup( $text,"form_sale_day","form_sale_day");

//請求日
$text = NULL;
$text[] =& $form->createElement("text","y","テキストフォーム","size=\"4\" style=\"$g_form_style\" maxLength=\"4\" onkeyup=\"changeText(this.form,'form_claim_day[y]','form_claim_day[m]',4)\" onFocus=\"onForm_today(this,this.form,'form_claim_day[y]','form_claim_day[m]','form_claim_day[d]')\" onBlur=\"blurForm(this)\"");
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement("text","m","テキストフォーム","size=\"1\" style=\"$g_form_style\" maxLength=\"2\" onkeyup=\"changeText(this.form,'form_claim_day[m]','form_claim_day[d]',2)\" onFocus=\"onForm_today(this,this.form,'form_claim_day[y]','form_claim_day[m]','form_claim_day[d]')\" onBlur=\"blurForm(this)\"");
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement("text","d","テキストフォーム","size=\"1\" style=\"$g_form_style\" maxLength=\"2\" onFocus=\"onForm_today(this,this.form,'form_claim_day[y]','form_claim_day[m]','form_claim_day[d]')\" onBlur=\"blurForm(this)\"");
$form->addGroup( $text,"form_claim_day","form_claim_day");

//得意先コード
//受注照会・受注残から遷移した場合、変更不可
if($aord_id == NULL){
//if($sale_id == NULL){
    //変更可
    $form_client[] =& $form->createElement(
            "text","cd1","","size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onChange=\"javascript:Change_Submit('client_search_flg','#','true','form_client[cd2]')\" onkeyup=\"changeText(this.form,'form_client[cd1]','form_client[cd2]',6)\"".$g_form_option."\""
            );
    $form_client[] =& $form->createElement(
            "static","","","-"
            );
    $form_client[] =& $form->createElement(
            "text","cd2","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onChange=\"javascript:Button_Submit('client_search_flg','#','true')\"".$g_form_option."\""
            );
    $form_client[] =& $form->createElement("text","name","テキストフォーム","size=\"34\" $g_text_readonly");
    $form->addGroup( $form_client, "form_client", "");
}else{
    //変更不可
    $form_client[] =& $form->createElement(
            "text","cd1","","size=\"7\" style=\"color: #585858; border: #ffffff 1px solid; background-color: #ffffff; text-align: left\" readonly\""
            );
    $form_client[] =& $form->createElement(
            "static","","","-"
            );
    $form_client[] =& $form->createElement(
            "text","cd2","","size=\"7\" style=\"color: #585858; border: #ffffff 1px solid; background-color: #ffffff; text-align: left\" readonly\""
            );
    $form_client[] =& $form->createElement("text","name","テキストフォーム","size=\"34\" $g_text_readonly");
    $form->addGroup( $form_client, "form_client", "");
}
//税抜金額
$form->addElement(
    "text","form_sale_total","",
    "size=\"25\" maxLength=\"18\" 
    style=\"color : #585858; 
    border : #FFFFFF 1px solid; 
    background-color: #FFFFFF; 
    text-align: right\" readonly'"
);

//消費税
$form->addElement(
        "text","form_sale_tax","",
        "size=\"25\" maxLength=\"18\" 
        style=\"color : #585858; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//税込金額
$form->addElement(
        "text","form_sale_money","",
        "size=\"25\" maxLength=\"18\" 
        style=\"color : #585858; 
        border : #FFFFFF 1px solid; 
        background-color: #FFFFFF; 
        text-align: right\" 
        readonly"
);

//グリーン指定
if($aord_id == NULL){
    $form->addElement('checkbox', 'form_trans_check', 'グリーン指定', '<b>グリーン指定</b>　',"onClick=\"javascript:Link_Submit('form_trans_check','trans_check_flg','#','true')\"");
}else{
    //グリーン指定判定
    if($trans_check == 't'){
        $str = "グリーン指定あり　";
    }
    $form->addElement("static","form_trans_check","","$str");
}

//運送業者
$select_value = Select_Get($db_con,'trans',$where);
if($aord_id == NULL){
    $form->addElement('select', 'form_trans_select', 'セレクトボックス', $select_value,$g_form_option_select);
}else{
    $aord_freeze[] = $form->addElement('select', 'form_trans_select', 'セレクトボックス', $select_value,$g_form_option_select);
}

//直送先
if($aord_id == NULL){
    $select_value = Select_Get($db_con,'direct');
    $form->addElement('select', 'form_direct_select', 'セレクトボックス', $select_value,"Class=\"Tohaba\"".$g_form_option_select);
}else{
    //受注からおきた売上は全ショップのデータをリストアップする。
    $select_value = Select_Get($db_con,'direct',"WHERE t_direct.shop_id IS NOT NULL");
    $aord_freeze[] = $form->addElement('select', 'form_direct_select', 'セレクトボックス', $select_value,"Class=\"Tohaba\"".$g_form_option_select);
}

//倉庫
$select_value = Select_Get($db_con,'ware');
if($aord_id == NULL){
    $form->addElement('select', 'form_ware_select', 'セレクトボックス', $select_value,"onkeydown=\"chgKeycode();\" onChange=\"javascript:Button_Submit('stock_search_flg','#','true');window.focus();\"");
}else{
    $aord_freeze[] = $form->addElement('select', 'form_ware_select', 'セレクトボックス', $select_value,$g_form_option_select);
}

//取引区分
if($aord_id == NULL){
    //売上照会・一括発行から遷移
    $select_value = Select_Get($db_con,'trade_sale');
}else{
    //受注残・受注照会から遷移
    $select_value = Select_Get($db_con,'trade_sale_aord');
}
$trade_form=$form->addElement('select', 'trade_sale_select', null, null, $g_form_option_select);

//返品、値引きの色を変更
$select_value_key = array_keys($select_value);
for($i = 0; $i < count($select_value); $i++){
    if($select_value_key[$i] == 13 || $select_value_key[$i] == 14 || $select_value_key[$i] == 63 || $select_value_key[$i] == 64){
        $color= "style=color:red";
    }else{
        $color="";
    }
    #2009-09-28 hashimoto-y
    #取引区分から値引きを表示しない。切り戻しの場合にはここのif文を外す。
    if( $select_value_key[$i] != 14 && $select_value_key[$i] != 64){
        #echo "$select_value_key[$i]<br>";
        $trade_form->addOption($select_value[$select_value_key[$i]], $select_value_key[$i],$color);
    }
}

if($sale_id != null && $aord_id != null){
    $trade_form->freeze();
}

//受注担当者担当者
if($aord_id != NULL || $sale_id != NULL){
    $select_value = Select_Get($db_con,'staff',null,true);
}
$aord_freeze[] = $form->addElement('select', 'form_staff_select', 'セレクトボックス', $select_value,$g_form_option_select);

//売上担当者担当者
$select_value = Select_Get($db_con,'staff',null,true);
$form->addElement('select', 'form_cstaff_select', 'セレクトボックス', $select_value,$g_form_option_select);

//備考
//$form->addElement("text","form_note","","size=\"20\" maxLength=\"20\" $g_form_option");

$form->addElement("textarea","form_note",""," rows=\"2\" cols=\"75\" $g_form_option_area");
$form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
$form->addRule("form_note","メモは60文字以内です。","mb_maxlength","60");


//売上計上日　請求日　取引区分　備考　以外は入力不可
for($i = 0; $i < count($aord_freeze); $i++){
    $aord_freeze[$i]->freeze();
}

/*
//売上伝票一括発行
$form->addElement("button","sale_button","売上伝票一括発行","onClick=\"javascript:Referer('1-2-202.php')\"");
//入力・変更
$form->addElement("button","new_button","入力・変更",$g_button_color."onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
//照会
$form->addElement("button","change_button","照　会","onClick=\"javascript:Referer('1-2-203.php')\"");
*/
// ヘッダ部リンクボタン
$form->addElement("button", "203_button", "照会・変更", "onClick=\"javascript: Referer('1-2-203.php');\"");
$form->addElement("button", "201_button", "入　力", "$g_button_color onClick=\"location.href='".$_SERVER["PHP_SELF"]."'\"");

//hidden
$form->addElement("hidden", "hdn_sale_id");         //売上ID
$form->addElement("hidden", "hdn_aord_id");         //受注ID
$form->addElement("hidden", "hdn_client_id");       //得意先ID
$form->addElement("hidden", "hdn_ware_id");         //出荷倉庫ID
$form->addElement("hidden", "hdn_rank_cd");         //顧客区分コード
$form->addElement("hidden", "client_shop_id");      //得意先のショップID
$form->addElement("hidden", "client_search_flg");   //得意先コード入力フラグ
$form->addElement("hidden", "hdn_coax");            //丸め区分
$form->addElement("hidden", "hdn_tax_franct");      //端数区分
$form->addElement("hidden", "del_row");             //削除行
$form->addElement("hidden", "add_row_flg");         //追加行フラグ
$form->addElement("hidden", "max_row");             //最大行数
$form->addElement("hidden", "goods_search_row");    //商品コード入力行
$form->addElement("hidden", "sum_button_flg");      //合計ボタン押下フラグ
$form->addElement("hidden", "trans_check_flg");     //グリーン指定チェックフラグ
$form->addElement("hidden", "show_button_flg");     //表示ボタン押下判定
$form->addElement("hidden", "stock_search_flg");    //現在個数検索フラグ
$form->addElement("hidden", "hdn_royalty_rate");    //ロイヤリティ
$form->addElement("hidden", "hdn_sale_enter_day");  //売上登録日
$form->addElement("hidden", "hdn_aord_enter_day");  //売上登録日
$form->addElement("hidden", "hdn_aord_change_day");  //売上登録日

#2009-09-10 hashimoto-y
for($i = 0; $i < $max_row; $i++){
    if(!in_array("$i", $del_history)){
        $form->addElement("hidden","hdn_discount_flg[$i]");
    }
}


/****************************/
//得意先コード入力処理
/****************************/
if($_POST["client_search_flg"] == true){
    $client_cd1         = $_POST["form_client"]["cd1"];       //得意先コード1
    $client_cd2         = $_POST["form_client"]["cd2"];       //得意先コード2

    //得意先の情報を抽出
    $sql  = "SELECT";
    $sql .= "   client_id,";
    $sql .= "   shop_id,";
    $sql .= "   client_cname,";
    $sql .= "   coax,";
    $sql .= "   tax_franct,";
    $sql .= "   rank_cd, ";
    $sql .= "   royalty_rate,";
    $sql .= "   client_cd1, ";
    $sql .= "   client_cd2, ";
    $sql .= "   trade_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_cd1 = '$client_cd1'";
    $sql .= "   AND";
    $sql .= "   client_cd2 = '$client_cd2'";
    $sql .= "   AND";
    $sql .= "   client_div = '3' ";
//    $sql .= "   AND";
//    $sql .= "   state = '1' ";
    $sql .= "   AND";
    $sql .= "   shop_id = $shop_id";
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $num = pg_num_rows($result);
    //該当データがある
    if($num == 1){
        $client_id      = pg_fetch_result($result, 0,0);        //得意先ID
        $client_shop_id     = pg_fetch_result($result, 0,1);    //得意先のショップID
        $client_name    = pg_fetch_result($result, 0,2);        //得意先名
        $coax           = pg_fetch_result($result, 0,3);        //丸め区分（商品）
        $tax_franct     = pg_fetch_result($result, 0,4);        //端数区分（消費税）
        $rank_cd        = pg_fetch_result($result, 0,5);        //顧客区分コード
        $royalty_rate   = pg_fetch_result($result, 0,6);        //ロイヤリティ

        //取得したデータをフォームにセット
        $client_data["client_search_flg"]   = "";
        $client_data["hdn_client_id"]       = $client_id;
        $client_data["client_shop_id"]      = $client_shop_id;
        $client_data["form_client"]["name"] = $client_name;
        $client_data["hdn_coax"]            = $coax;
        $client_data["hdn_tax_franct"]      = $tax_franct;
        $client_data["hdn_rank_cd"]         = $rank_cd;
        $client_data["hdn_royalty_rate"]    = $royalty_rate;
        $client_data["form_client"]["cd1"]    = pg_fetch_result($result, 0,7);
        $client_data["form_client"]["cd2"]    = pg_fetch_result($result, 0,8);
        $client_data["trade_sale_select"]   = pg_fetch_result($result, 0, 9);

        //出荷倉庫が選択されていたら、警告非表示
        if($_POST["form_ware_select"] != NULL){
            $warning = null;
        }

    }else{
        $warning = "得意先と出荷倉庫選択してください。";
        $client_data["client_search_flg"]   = "";
        $client_data["hdn_client_id"]       = "";
        $client_data["client_shop_id"]      = "";
        $client_data["form_client"]["name"] = "";
        $client_data["hdn_coax"]            = "";
        $client_data["hdn_tax_franct"]      = "";
        $client_data["hdn_rank_cd"]         = "";
        $client_data["hdn_royalty_rate"]    = "";
        $client_id = null;
    }

    //前に入力された値を初期化
    for($i = 0; $i < $max_row; $i++){
        //受注データ
        $client_data["hdn_goods_id"][$i]           = "";
        $client_data["form_stock_num"][$i]         = "";
        $client_data["hdn_tax_div"][$i]            = "";
        $client_data["form_goods_cd"][$i]          = "";
        $client_data["form_goods_name"][$i]        = "";
        $client_data["form_sale_num"][$i]          = "";
        $client_data["form_cost_price"]["$i"]["i"] = "";
        $client_data["form_cost_price"]["$i"]["d"] = "";
        $client_data["form_cost_amount"][$i]       = "";
        $client_data["form_sale_price"]["$i"]["i"] = "";
        $client_data["form_sale_price"]["$i"]["d"] = "";
        $client_data["form_sale_amount"][$i]       = "";
        $client_data["form_aorder_num"][$i]        = "";
        $client_data["hdn_royalty"][$i]            = "";
        //aoyama-n 2009-09-04
        $client_data["hdn_discount_flg"][$i]       = "";
    }
    $client_data["del_row"]             = "";        //削除行番号
    $client_data["max_row"]             = "";        //行数
    $client_data["form_sale_total"]     = "";        //税抜金額
    $client_data["form_sale_tax"]       = "";        //消費税
    $client_data["form_sale_money"]     = "";        //税込金額
    $client_data["show_button_flg"]     = "";        //表示ボタン
    $client_data["form_ord_no"]         = "";        //受注番号
    $form->setConstants($client_data);
}

/****************************/
//出荷倉庫入力
/****************************/
if($_POST["stock_search_flg"] == true){
    
    $ware_id = $_POST["form_ware_select"];   //出荷倉庫
  
    //商品が１つ以上選択されていれば処理開始
    if($ware_id != NULL){

        for($i = 0; $i < $max_row; $i++){

            $goods_id = $_POST["hdn_goods_id"][$i];

            if($goods_id != NULL){            
                $sql  = "SELECT";
                $sql .= "   stock_num";
                $sql .= " FROM";
                $sql .= "   t_stock";
                $sql .= " WHERE";
                $sql .= "   shop_id = $shop_id";
                $sql .= "   AND";
                $sql .= "   ware_id = $ware_id";
                $sql .= "   AND";
                $sql .= "   goods_id = $goods_id";
                $sql .= ";";

                $result         = Db_Query($db_con, $sql);
                $stock_data_num = pg_num_rows($result);

                if($stock_data_num != 0){
                    $stock_data = pg_fetch_result($result,0,0);
                }

                $set_stock_data["form_stock_num"][$i] = ($stock_data != NULL)? $stock_data : 0;     //現在個数
            }

        }

        //得意先が選択されていたら、警告非表示
        if($_POST["hdn_client_id"] != NULL){
            $warning = null;
        }
    }else{
        $warning = "得意先と出荷倉庫を選択してください。";
    }
    
    $set_stock_data["hdn_ware_id"]         = $ware_id;  
    $set_stock_data["stock_search_flg"]    = "";
    $set_stock_data["show_button_flg"]     = "";        //表示ボタン
    $set_stock_data["form_ord_no"]         = "";        //発注番号
    $form->setConstants($set_stock_data);

}

/****************************/
//合計ボタン押下処理
/****************************/
if($_POST["sum_button_flg"] == true || $_POST["del_row"] != "" || $_POST["form_sale_btn"] == "売上確認画面へ"){

    //削除リストを取得
    $del_row = $_POST["del_row"];
    //削除履歴を配列にする。
    $del_history = explode(",", $del_row);

    $sale_data  = $_POST["form_sale_amount"];  //売上金額
    $sale_money = NULL;                        //商品の売上金額
    $tax_div    = NULL;                        //課税区分

    //売上金額の合計値計算
    for($i=0;$i<$max_row;$i++){
        if($sale_data[$i] != "" && !in_array("$i", $del_history)){
            $sale_money[] = $sale_data[$i];
            $tax_div[]    = $_POST["hdn_tax_div"][$i];
        }
    }

/*
    //現在の消費税率
    $sql  = "SELECT ";
    $sql .= "    tax_rate_n ";
    $sql .= "FROM ";
    $sql .= "    t_client ";
    $sql .= "WHERE ";
    //$sql .= "    client_id = $client_id;";
	$sql .= "    client_id = $shop_id;";	//2009/10/21 kajioka-h 自分の消費税率を使う
    $result = Db_Query($db_con, $sql); 
    $tax_num = pg_fetch_result($result, 0,0);
*/
	//2009-10-21 kajioka-h 消費税率取得
	#$tax_num = Get_TaxRate_Day($db_con, $shop_id, $client_id, $_POST["form_sale_day"]["y"]."-".$_POST["form_sale_day"]["m"]."-".$_POST["form_sale_day"]["d"]);


    #2009-11-25 hashimoto-y
    $tax_rate_obj->setTaxRateDay($_POST["form_sale_day"]["y"]."-".$_POST["form_sale_day"]["m"]."-".$_POST["form_sale_day"]["d"]);
    $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

    $total_money = Total_Amount($sale_money, $tax_div,$coax,$tax_franct,$tax_num,$client_id, $db_con);

    $sale_money = number_format($total_money[0]);
    $tax_money  = number_format($total_money[1]);
    $st_money   = number_format($total_money[2]);

    if($_POST["sum_button_flg"] == true){
        //初期表示位置変更
        $height = $max_row * 30;
        $form_potision = "<body bgcolor=\"#D8D0C8\" onLoad=\"form_potision($height);\">";
    }

    //フォームに値セット
    $money_data["form_sale_total"]   = $sale_money;
    $money_data["form_sale_tax"]     = $tax_money;
    $money_data["form_sale_money"]   = $st_money;
    $money_data["sum_button_flg"]    = "";
    $form->setConstants($money_data);
}

/****************************/
//商品コード入力
/****************************/
if($_POST["goods_search_row"] != null){
    //商品データを取得する行
    $search_row = $_POST["goods_search_row"];

    //$attach_gid   = $_POST["attach_gid"];         //得意先の所属グループ
    $client_shop_id = $_POST["client_shop_id"];   //得意先のショップID
    $rank_cd      = $_POST["hdn_rank_cd"];        //得意先の顧客区分
    $ware_id      = $_POST["form_ware_select"];   //出荷倉庫

    $sql  = "SELECT\n";
    $sql .= "   t_goods.goods_id,\n";
    $sql .= "   t_goods.name_change,\n";
    $sql .= "   t_goods.goods_cd,\n";
    //$sql .= "   t_goods.goods_name,\n";
    $sql .= "   (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS goods_name, \n";    //正式名

    $sql .= "   initial_cost.r_price AS initial_price,\n";
    $sql .= "   sale_price.r_price AS sale_price,\n";
    $sql .= "   t_goods.tax_div,\n";
    #2009-10-13_1 hashimoto-y
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num, \n";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num, \n";

    //aoyama-n 2009-09-04
    #$sql .= "   royalty\n";
    $sql .= "   royalty,\n";
    $sql .= "   t_goods.discount_flg\n";

    $sql .= " FROM\n";
    $sql .= "   t_goods \n";
    $sql .= "     INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";

    $sql .= "       INNER JOIN\n";
    $sql .= "   t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id\n";
    $sql .= "       INNER JOIN\n";
    $sql .= "   t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id \n";

    $sql .= "   LEFT JOIN\n";
    $sql .= "   (SELECT\n";
    $sql .= "       goods_id,\n";
    $sql .= "       SUM(stock_num)AS stock_num\n";
    $sql .= "    FROM\n";
    $sql .= "        t_stock\n";
    $sql .= "    WHERE\n";
    $sql .= "        shop_id = $shop_id\n";
    $sql .= "        AND\n";
    $sql .= "        ware_id = $ware_id\n";
    $sql .= "    GROUP BY t_stock.goods_id\n"; 
    $sql .= "   )AS t_stock\n";
    $sql .= "   ON t_goods.goods_id = t_stock.goods_id\n";
    #2009-10-13_1 hashimoto-y
    $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id\n";

    $sql .= " WHERE \n";
    $sql .= "       t_goods.goods_cd = '".$_POST["form_goods_cd"][$search_row]."'\n";
    $sql .= " AND \n";
    $sql .= "       t_goods.public_flg = 't'\n ";
    $sql .= " AND \n";
    $sql .= "       t_goods.accept_flg = '1'\n";
    $sql .= " AND \n";
    $sql .= "       t_goods.compose_flg = 'f'\n";
    $sql .= " AND \n";
    $sql .= "       t_goods.state IN (1,3)\n";
    $sql .= " AND \n";
    $sql .= "       initial_cost.rank_cd = '1' \n";
    #2009-10-13_1 hashimoto-y
    $sql .= " AND\n";
    $sql .= "       t_goods_info.shop_id = $shop_id \n";

    $sql .= " AND \n";
    $sql .= "       sale_price.rank_cd = '$rank_cd';\n";

    $result = Db_Query($db_con, $sql);
    $data_num = pg_num_rows($result);

    //データが存在した場合、フォームにデータを表示
    if($data_num == 1){
        $goods_data = pg_fetch_array($result);

        $set_goods_data["hdn_goods_id"][$search_row]         = $goods_data[0];   //商品ID

        $set_goods_data["hdn_name_change"][$search_row]      = $goods_data[1];   //品名変更フラグ
        $hdn_name_change[$search_row]                        = $goods_data[1];   //POSTする前に商品名の変更不可判定を行なう為

        $set_goods_data["form_goods_cd"][$search_row]        = $goods_data[2];   //商品CD
        $set_goods_data["form_goods_name"][$search_row]      = $goods_data[3];   //商品名

        //原価単価を整数部と少数部に分ける
        $cost_price = explode('.', $goods_data[4]);
        $set_goods_data["form_cost_price"][$search_row]["i"] = $cost_price[0];  //原価単価
        $set_goods_data["form_cost_price"][$search_row]["d"] = ($cost_price[1] != null)? $cost_price[1] : '00';     

        //売上単価を整数部と少数部に分ける
        $sale_price = explode('.', $goods_data[5]);
        $set_goods_data["form_sale_price"][$search_row]["i"] = $sale_price[0];  //売上単価
        $set_goods_data["form_sale_price"][$search_row]["d"] = ($sale_price[1] != null)? $sale_price[1] : '00';

        $set_goods_data["hdn_royalty"][$search_row]         = $goods_data[8];

        if($_POST["form_sale_num"][$search_row] != null){
            //原価金額計算
            $cost_amount = bcmul($goods_data[4], $_POST["form_sale_num"][$search_row],2);
            $cost_amount = Coax_Col($coax, $cost_amount);
            //売上金額計算
            $sale_amount = bcmul($goods_data[5], $_POST["form_sale_num"][$search_row],2);
            $sale_amount = Coax_Col($coax, $sale_amount);
            //出荷数が入力されていた場合は、再計算
            $set_goods_data["form_cost_amount"][$search_row]     = number_format($cost_amount);
            $set_goods_data["form_sale_amount"][$search_row]     = number_format($sale_amount);
        }

        $set_goods_data["hdn_tax_div"][$search_row]          = $goods_data[6]; //課税区分
        $set_goods_data["form_stock_num"][$search_row]       = $goods_data[7]; //現在庫数
        //aoyama-n 2009-09-04
        $set_goods_data["hdn_discount_flg"][$search_row]     = $goods_data[9]; //値引フラグ

    }else{
        //データが無い場合は、初期化
        $no_goods_flg                                        = true;     //該当する商品が無ければデータを表示しない
        $set_goods_data["hdn_goods_id"][$search_row]         = "";
        $set_goods_data["hdn_name_change"][$search_row]      = "";
        $set_goods_data["form_goods_cd"][$search_row]        = "";
        $set_goods_data["form_goods_name"][$search_row]      = "";
        $set_goods_data["form_sale_num"][$search_row]        = "";
        $set_goods_data["form_cost_price"][$search_row]["i"] = "";
        $set_goods_data["form_cost_price"][$search_row]["d"] = "";
        $set_goods_data["form_cost_amount"][$search_row]     = "";
        $set_goods_data["form_sale_price"][$search_row]["i"] = "";
        $set_goods_data["form_sale_price"][$search_row]["d"] = "";
        $set_goods_data["form_sale_amount"][$search_row]     = "";
        $set_goods_data["hdn_tax_div"][$search_row]          = "";
        $set_goods_data["form_stock_num"][$search_row]       = "";
        $set_goods_data["hdn_royalty"][$search_row]       = "";
        //aoyama-n 2009-09-04
        $set_goods_data["hdn_discount_flg"][$search_row]     = "";
    }
    $set_goods_data["goods_search_row"]                  = "";
    $form->setConstants($set_goods_data);
}

/****************************/
//表示ボタン押下処理
/****************************/
if($_POST["show_button_flg"] == true && $_POST["client_search_flg"] != true && $_POST["trans_check_flg"] != true && $_POST["comp_button"] != "売上OK"){
    $aord_id = $_POST["form_ord_no"];       //受注ID

    //プルダウンの受注番号を選択した場合のみGET情報を付加して遷移する
    if($aord_id != NULL && $aord_id != 0){
        //受注IDをGETに追加し、自画面に遷移
        header("Location: ./1-2-201.php?aord_id=$aord_id");
    }else{
        header("Location: ./1-2-201.php");
    }
}

/****************************/
//エラーチェック(addRule)
/****************************/
//売上計上日
//●必須チェック
//●半角数字チェック
$form->addGroupRule('form_sale_day', array(
        'y' => array(
                array('売上計上日 の日付は妥当ではありません。', 'required'),
                array('売上計上日 の日付は妥当ではありません。', 'numeric')
        ),      
        'm' => array(
                array('売上計上日 の日付は妥当ではありません。','required'),
                array('売上計上日 の日付は妥当ではありません。', 'numeric')
        ),       
        'd' => array(
                array('売上計上日 の日付は妥当ではありません。','required'),
                array('売上計上日 の日付は妥当ではありません。', 'numeric')
        )       
));

//請求日
//●必須チェック
//●半角数字チェック
$form->addGroupRule('form_claim_day', array(
        'y' => array(
                array('請求日 の日付は妥当ではありません。', 'required'),
                array('請求日 の日付は妥当ではありません。', 'numeric')
        ),      
        'm' => array(
                array('請求日 の日付は妥当ではありません。','required'),
                array('請求日 の日付は妥当ではありません。', 'numeric')
        ),       
        'd' => array(
                array('請求日 の日付は妥当ではありません。','required'),
                array('請求日 の日付は妥当ではありません。', 'numeric')
        )       
));

//得意先
//●必須チェック
$form->addGroupRule('form_client', array(
        'cd1' => array(
                array('正しい得意先コードを入力してください。','required')
        ),
        'cd2' => array(
                array('正しい得意先コードを入力してください。','required')
        ),
        'name' => array(
                array('正しい得意先コードを入力してください。','required')
        )
));

//グリーン指定した場合は、運送業者は必須
if($_POST["form_trans_check"] != NULL){
    //運送業者
    //●必須チェック
    $form->addRule('form_trans_select','運送業者を選択してください。','required');
}

//出荷倉庫
//●必須チェック
$form->addRule('form_ware_select','出荷倉庫を選択してください。','required');

//取引区分
//●必須チェック
$form->addRule('trade_sale_select','取引区分を選択してください。','required');

//担当者
//●必須チェック
$form->addRule('form_cstaff_select','売上担当者を選択してください。','required');

/****************************/
//登録ボタン押下処理
/****************************/
if($_POST["form_sale_btn"] == "売上確認画面へ" || $_POST["comp_button"] == "売上OK"){

    //ヘッダー情報
    $sale_no              = $_POST["form_sale_no"];            //伝票番号
    $sale_day_y           = $_POST["form_sale_day"]["y"];      //売上日
    $sale_day_m           = $_POST["form_sale_day"]["m"];            
    $sale_day_d           = $_POST["form_sale_day"]["d"];            
    $claim_day_y          = $_POST["form_claim_day"]["y"];     //請求日
    $claim_day_m          = $_POST["form_claim_day"]["m"];            
    $claim_day_d          = $_POST["form_claim_day"]["d"];            
    $note                 = $_POST["form_note"];               //備考
    //POST情報があるか
    if($_POST["form_trans_check"] != NULL){
        $trans_check          = 't';                           //グリーン指定
    }
    $trans_id             = $_POST["form_trans_select"];       //運送業者

    //受注からの売上の場合は受注時の直送先IDをしよう
    if($aord_id != null){
        $sql = "SELECT direct_id FROM t_aorder_h WHERE aord_id = $aord_id;";
        $result = Db_Query($db_con,$sql);

        $direct_id = pg_fetch_result($result, 0,0);

    }else{
        $direct_id            = $_POST["form_direct_select"];      //直送先
    }
    $ware_id              = $_POST["form_ware_select"];        //倉庫
    $trade_sale           = $_POST["trade_sale_select"];       //取引区分
    $c_staff_id           = $_POST["form_cstaff_select"];      //売上担当者
    $ac_staff_id          = $_POST["form_staff_select"];       //受注担当者
    $royalty_rate         = $_POST["hdn_royalty_rate"];        //ロイヤリティ

    /****************************/
    //エラーチェック(PHP)
    /****************************/
    $error_flg = false;                                         //エラー判定フラグ

    //データ情報
    $check_ary = array(
                    $_POST[hdn_goods_id],                           //商品ID
                    $_POST[form_goods_cd],                          //商品コード
                    $_POST[form_goods_name],                        //商品名
                    $_POST[form_sale_num],                          //出荷数
                    $_POST[form_cost_price],                        //原価単価
                    $_POST[form_sale_price],                        //売上単価
                    $_POST[form_cost_amount],                       //原価金額
                    $_POST[form_sale_amount],                       //売上金額
                    $_POST[hdn_tax_div],                            //課税区分
                    $del_history,                                   //削除履歴
                    $max_row,                                       //最大行数
                    'sale',                                         //受注売上区分
                    $db_con,                                        //DBコネクション
                    $_POST[form_aorder_num],                        //受注数
                    //aoyama-n 2009-09-04
                    #$_POST[hdn_royalty]                             //ロイヤリティ
                    $_POST[hdn_royalty],                            //ロイヤリティ
                    $_POST[hdn_discount_flg]                        //値引フラグ
                );


    $check_data = Row_Data_Check2($check_ary);

    //エラーがあった場合
    if($check_data[0] === true){
        //商品未選択エラー
        $goods_error0 = $check_data[1];

        //商品コード不正
        $goods_error1 = $check_data[2];

        //受注数、原価単価、売上単価入力チェック
        $goods_error2 = $check_data[3];

        //受注数半角エラー
        $goods_error3 = $check_data[4];

        //原価単価半角エラー
        $goods_error4 = $check_data[5];

        //売上単価半角エラー
        $goods_error5 = $check_data[6];

        $error_flg = true; 
    //エラーが無かった場合
    }else{  
        $goods_id         = $check_data[1][goods_id];       //商品ID
        $goods_cd         = $check_data[1][goods_cd];       //商品ID
        $goods_name       = $check_data[1][goods_name];     //商品名
        $sale_num         = $check_data[1][sale_num];       //出荷数
        $c_price          = $check_data[1][cost_price];     //原価単価（整数部）
        $s_price          = $check_data[1][sale_price];     //売上単価（整数部）
        $tax_div          = $check_data[1][tax_div];        //課税区分
        $cost_amount      = $check_data[1][cost_amount];    //原価金額
        $sale_amount      = $check_data[1][sale_amount];    //売上金額
        $aorder_num       = $check_data[1][aord_num];       //受注数
        $royalty          = $check_data[1][royalty];        //ロイヤリティ
        $def_line         = $check_data[1][def_line];
    }

    $royalty_data = Total_Royalty($sale_amount, $royalty, $royalty_rate,$coax);

    //商品チェック
    //商品重複チェック
    $goods_count = count($goods_id);
    for($i = 0; $i < $goods_count; $i++){

        //既にチェック済みの商品の場合はｽｷｯﾌﾟ
        if(@in_array($goods_id[$i], $checked_goods_id)){
            continue;
        }

        //チェック対象となる商品
        $err_goods_cd = $goods_cd[$i];
        $mst_line = $def_line[$i];

        for($j = $i+1; $j < $goods_count; $j++){
            //商品が同じ場合
            if($goods_id[$i] == $goods_id[$j]) {
                $duplicate_line .= ", ".($def_line[$j]);
            }
        }
        $checked_goods_id[] = $goods_id[$i];    //チェック済み商品

        if($duplicate_line != null){
            $duplicate_goods_err[] =  "商品コード：".$err_goods_cd."の商品が複数選択されています。(".$mst_line.$duplicate_line."行目)";
        }

        $err_goods_cd   = null;
        $mst_line       = null;
        $duplicate_line = null;
    }

    //◇売上計上日
    //・文字種チェック
    if($sale_day_y != null && $sale_day_m != null && $sale_day_d != null){
        $sale_day_y = (int)$sale_day_y;
        $sale_day_m = (int)$sale_day_m;
        $sale_day_d = (int)$sale_day_d;
        if(!checkdate($sale_day_m,$sale_day_d,$sale_day_y)){
            $form->setElementError("form_sale_day","売上計上日 の日付は妥当ではありません。");
        }else{
            //システム開始日チェック
            $err_msge = Sys_Start_Date_Chk($sale_day_y, $sale_day_m, $sale_day_d, "売上計上日");
            if($err_msge != null){
                $form->setElementError("form_sale_day","$err_msge"); 
            }
            //◇ 売上計上日
            //月次更新
            if(Check_Monthly_Renew($db_con, $client_id, "1", $sale_day_y, $sale_day_m, $sale_day_d) === false){
                $form->setElementError("form_sale_day","売上日に月次更新日以前の日付は登録できません。");
            }
        }
    }

    //◇請求日
    //・文字種チェック
    if($claim_day_y != null || $claim_day_m != null || $claim_day_d != null){
        $claim_day_y = (int)$claim_day_y;
        $claim_day_m = (int)$claim_day_m;
        $claim_day_d = (int)$claim_day_d;
        if(!checkdate($claim_day_m,$claim_day_d,$claim_day_y)){
            $form->setElementError("form_claim_day","請求日 の日付は妥当ではありません。");
        }else{
            //システム開始日チェック
            $err_msge = Sys_Start_Date_Chk($claim_day_y, $claim_day_m, $claim_day_d, "請求日");
            if($err_msge != null){
                $form->setElementError("form_claim_day","$err_msge"); 
            }        

            //◇請求日
            //月次更新
            if(Check_Monthly_Renew($db_con, $client_id, "1", $claim_day_y, $claim_day_m, $claim_day_d) === false){
                $form->setElementError("form_claim_day","請求日に月次更新日以前の日付は登録できません。");
            }

            //◇請求日
            //請求日締日
            if(Check_Bill_Close_Day($db_con, $client_id, $claim_day_y, $claim_day_m, $claim_day_d) === false){
                $form->setElementError("form_claim_day","請求日に請求書作成済の日付が入力されています。<br>請求日を変更するか、請求書を削除して下さい。");
            }
        }
    }


    //受注番号重複チェック
    //受注から売上入力を起こす場合に、変更ではない場合
    if($aord_id != null && $sale_id == null){
        $sql  = "SELECT";
        $sql .= "   t_aorder_h.ord_no";
        $sql .= " FROM";
        $sql .= "   (SELECT";
        $sql .= "       aord_id";
        $sql .= "   FROM";
        $sql .= "       t_sale_h";
        $sql .= "   WHERE";
        $sql .= "       t_sale_h.aord_id = $aord_id";
        $sql .= "   ) AS t_sale_h";
        $sql .= "       INNER JOIN";
        $sql .= "   t_aorder_h";
        $sql .= "   ON t_sale_h.aord_id = t_aorder_h.aord_id";
        $sql .= ";";

        $result = Db_Query($db_con, $sql);
        $aord_no = @pg_fetch_result($result,0,0);

        if($aord_no != null){
            $aord_id_err = "受注番号".$aord_no."は既に売上入力済みです。";
            $error_flg = true;
        }
    }

    //aoyama-n 2009-09-04
    //値引商品選択時の取引区分チェック（値引・返品は使用不可）
    if(($trade_sale == '13' || $trade_sale == '14' || $trade_sale == '63' || $trade_sale == '64') 
      && (in_array('t', $_POST[hdn_discount_flg]))){
        $form->setElementError("trade_sale_select", "値引商品を選択した場合、使用できる取引区分は「掛売上、割賦売上、現金売上」のみです。");
    }

    //取引区分が売上以外で合計金額がマイナスの場合はエラー
    if($trade_sale != "11" 
        && 
        $trade_sale != "61" 
    ){
        $check_money = Total_Amount($sale_amount, $tax_div,$coax,$tax_franct,$tax_num, $client_id, $db_con);
    
        if($check_money[2] < 0){
            $form->setElementerror("form_sale_money","取引区分に　13,14,15,63,64　を選択した場合にマイナス金額は登録できません。");
        }
    }

    //エラーの場合はこれ以降の表示処理を行なわない
    if($form->validate() && $error_flg == false){

        //登録判定
        if($_POST["comp_button"] == "売上OK"){

/*
            //現在の消費税率
            $sql  = "SELECT ";
            $sql .= "    tax_rate_n ";
            $sql .= "FROM ";
            $sql .= "    t_client ";
            $sql .= "WHERE ";
            $sql .= "    client_id = $shop_id;";
            $result = Db_Query($db_con, $sql); 
            $tax_num = pg_fetch_result($result, 0,0);
*/
			//2009-10-21 kajioka-h 消費税率取得
			#$tax_num = Get_TaxRate_Day($db_con, $shop_id, $client_id, $sale_day_y."-".$sale_day_m."-".$sale_day_d);

            #2009-11-25 hashimoto-y
            $tax_rate_obj->setTaxRateDay($sale_day_y."-".$sale_day_m."-".$sale_day_d);
            $tax_num = $tax_rate_obj->getClientTaxRate($client_id);

            $total_money = Total_Amount($cost_amount, $tax_div,$coax,$tax_franct,$tax_num, $client_id, $db_con);
            $cost_money  = $total_money[0];

            $total_money = Total_Amount($sale_amount, $tax_div,$coax,$tax_franct,$tax_num, $client_id, $db_con);
            $sale_money  = $total_money[0];
            $sale_tax    = $total_money[1];

            //日付の形式変更
            $sale_day  = $sale_day_y."-".$sale_day_m."-".$sale_day_d;
            $claim_day  = $claim_day_y."-".$claim_day_m."-".$claim_day_d;

            //売上ヘッダ・売上データ　登録・更新SQL
            Db_Query($db_con, "BEGIN");

            //変更処理判定
            if($sale_id != NULL){

                $update_check_flg = Update_Check($db_con, "t_sale_h", "sale_id", $sale_id, $_POST["hdn_sale_enter_day"]);
                if($update_check_flg === false){
                    Db_Query($db_con, "ROLLBACK;");
                    header("Location: ./1-2-205.php?sale_id=$sale_id&input_flg=ture&del_flg=true");
                    exit;
                }

                $renew_check_flg = Renew_Check($db_con, "t_sale_h", "sale_id", $sale_id);
                if($renew_check_flg === false){
                    Db_Query($db_con, "ROLLBACK;");
                    header("Location: ./1-2-205.php?sale_id=$sale_id&input_flg=ture&renew_flg=true");
                    exit;
                }

                if($aord_id != null){
                    $update_data_check_flg = Update_Data_Check($db_con, "t_aorder_h", "aord_id", $aord_id, $_POST["hdn_aord_change_day"]);
                    if($update_data_chekc_flg === false){
                        Db_Query($db_con, "ROLLBACK;");
                        header("Location: ./1-2-205.php?sale_id=$sale_id&input_flg=ture&aord_finish_flg=true");
                        exit;
                    }
                }

                //売上ヘッダー変更
                $sql  = "UPDATE t_sale_h SET ";
                $sql .= "    sale_no = '$sale_no',";
                $sql .= "    sale_day = '$sale_day',";
                $sql .= "    claim_day = '$claim_day',";
                $sql .= "    client_id = $client_id,";

                //直送先が指定されているか
                if($direct_id != null){
                    $sql .= "    direct_id = $direct_id,";
                }else{
                    $sql .= "    direct_id = NULL,";
                }
                $sql .= "    trade_id = '$trade_sale',";

                //運送業者が指定されているか
                if($trans_id != null){
                    $sql .= "    trans_id = $trans_id,";
                }else{
                    $sql .= "    trans_id = NULL,";
                }

                //チェック値をbooleanに変更
                if($trans_check=='t'){
                    $sql .= "green_flg = true,";    
                }else{
                    $sql .= "green_flg = false,";    
                }

                $sql .= "    note = '$note',";
                $sql .= "    cost_amount = $cost_money,";    
                $sql .= "    net_amount = $sale_money,";    
                $sql .= "    tax_amount = $sale_tax,";    
                $sql .= "    c_staff_id = $c_staff_id,";
                $sql .= "    ware_id = $ware_id,";
                $sql .= "    e_staff_id = $e_staff_id, ";
                $sql .= ($ac_staff_id != null)? "ac_staff_id = $ac_staff_id,": null;
                $sql .= "    client_cd1 = (SELECT client_cd1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    client_cd2 = (SELECT client_cd2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    client_name = (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    client_name2 = (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    client_cname = (SELECT client_cname FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    c_post_no1 = (SELECT post_no1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    c_post_no2 = (SELECT post_no2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    c_address1 = (SELECT address1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    c_address2 = (SELECT address2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    c_address3 = (SELECT address3 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    c_shop_name = (SELECT shop_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    c_shop_name2 = (SELECT shop_name2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= ($direct_id != null) ? " direct_cd = (SELECT direct_cd FROM t_direct WHERE direct_id = $direct_id), " : " direct_cd = NULL, ";
                $sql .= ($direct_id != null) ? " direct_name = (SELECT direct_name FROM t_direct WHERE direct_id = $direct_id), " : "direct_name = NULL, ";
                $sql .= ($direct_id != null) ? " direct_name2 = (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct_id), " : " direct_name2 = NULL, ";
                $sql .= ($direct_id != null) ? " direct_cname = (SELECT direct_cname FROM t_direct WHERE direct_id = $direct_id), " : " direct_cname = NULL, ";
                $sql .= ($direct_id != null) ? " d_post_no1 = (SELECT post_no1 FROM t_direct WHERE direct_id = $direct_id), " : " d_post_no1 = NULL, ";
                $sql .= ($direct_id != null) ? " d_post_no2 = (SELECT post_no2 FROM t_direct WHERE direct_id = $direct_id), " : " d_post_no2 = NULL, ";
                $sql .= ($direct_id != null) ? " d_address1 = (SELECT address1 FROM t_direct WHERE direct_id = $direct_id), " : " d_address1 = NULL, ";
                $sql .= ($direct_id != null) ? " d_address2 = (SELECT address2 FROM t_direct WHERE direct_id = $direct_id), " : " d_address2 = NULL, ";
                $sql .= ($direct_id != null) ? " d_address3 = (SELECT address3 FROM t_direct WHERE direct_id = $direct_id), " : " d_address3 = NULL, ";
                $sql .= ($direct_id != null) ? " d_tel = (SELECT tel FROM t_direct WHERE direct_id = $direct_id), " : " d_tel = NULL, ";
                $sql .= ($direct_id != null) ? " d_fax = (SELECT fax FROM t_direct WHERE direct_id = $direct_id), " : " d_fax = NULL, ";
                $sql .= ($trans_id != null) ? " trans_name = (SELECT trans_name FROM t_trans WHERE trans_id = $trans_id), " : " trans_name = NULL, ";
                $sql .= ($trans_id != null) ? " trans_cname = (SELECT trans_cname FROM t_trans WHERE trans_id = $trans_id), " : " trans_cname = NULL, ";
                $sql .= "   ware_name = (SELECT ware_name FROM t_ware WHERE ware_id = $ware_id), ";
                $sql .= "   c_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $c_staff_id), ";
                $sql .= "   e_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $e_staff_id), ";
                $sql .= ($ac_staff_id != null)? " ac_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $ac_staff_id), " : null;
                $sql .= "   royalty_amount = $royalty_data,";
                #2010-09-09 aoyama-n claim_id追加
                $sql .= "   claim_id = (SELECT claim_id FROM t_claim WHERE client_id = $client_id AND claim_div = '1'),";
                $sql .= "   change_day  = CURRENT_TIMESTAMP, ";
                $sql .= "   slip_out = (SELECT slip_out FROM t_client WHERE client_id = $client_id ) ";
                $sql .= " WHERE ";
                $sql .= "    sale_id = $sale_id;";

                $result = Db_Query($db_con,$sql);
                if($result == false){
                    Db_Query($db_con,"ROLLBACK;");
                    exit;
                }

                //売上データを削除
                $sql  = "DELETE FROM";
                $sql .= "    t_sale_d";
                $sql .= " WHERE";
                $sql .= "    sale_id = $sale_id";
                $sql .= ";";

                $result = Db_Query($db_con, $sql );
                if($result == false){
                    Db_Query($db_con, "ROLLBACK");
                    exit;
                }

                //分割テーブルに登録されているデータを削除
                $sql  = "DELETE FROM\n";
                $sql .= "   t_installment_sales\n";
                $sql .= " WHERE\n";
                $sql .= "   sale_id = $sale_id\n";
                $sql .= ";"; 

                $result = Db_Query($db_con, $sql);
                //失敗した場合はロールバック
                if($result === false){ 
                    Db_Query($db_con, $sql);
                    exit;   
                }       

                //売上データを削除
                $sql  = "DELETE FROM";
                $sql .= "    t_payin_h";
                $sql .= " WHERE";
                $sql .= "    sale_id = $sale_id";
                $sql .= ";";

                $result = Db_Query($db_con, $sql );
                if($result == false){
                    Db_Query($db_con, "ROLLBACK");
                    exit;
                }

            }else{

                if($aord_id != null){
                    $aord_check_flg = Update_Check($db_con, "t_aorder_h", "aord_id", $aord_id, $_POST["hdn_aord_enter_day"]);
                    if($aord_check_flg === false){
                        Db_Query($db_con, "ROLLBACK;");
                        header("Location: ./1-2-205.php?sale_id=$sale_id&input_flg=ture&aord_del_flg=true");
                        exit;
                    }
                    $update_data_check_flg = Update_Data_Check($db_con, "t_aorder_h", "aord_id", $aord_id, $_POST["hdn_aord_change_day"]);
                    if($update_data_check_flg === false){
                        Db_Query($db_con, "ROLLBACK;");
                        header("Location: ./1-2-205.php?sale_id=$sale_id&input_flg=ture&aord_finish_flg=true");
                        exit;
                    }
                }

                //売上ヘッダー登録
                $sql  = "INSERT INTO t_sale_h (";
                $sql .= "    sale_id,";
                $sql .= "    sale_no,";
                if($aord_id != NULL){
                    $sql .= "aord_id,";
                }
                $sql .= "    sale_day,";
                $sql .= "    claim_day,";
                $sql .= "    client_id,";
                //直送先が指定されているか
                if($direct_id != null){
                    $sql .= "    direct_id,";
                }
                $sql .= "    trade_id,";
                //運送業者が指定されているか
                if($trans_id != null){
                    $sql .= "    trans_id,";
                }
                //グリーン指定が指定されているか
                if($trans_check != null){
                    $sql .= "    green_flg,";
                }
                $sql .= "    note,";
                $sql .= "    cost_amount,";   
                $sql .= "    net_amount,";                  
                $sql .= "    tax_amount,";              
                $sql .= "    c_staff_id,";
                $sql .= "    ware_id,";
                $sql .= "    e_staff_id,";
                $sql .= "    shop_id,";
                $sql .= ($ac_staff_id != null)? "ac_staff_id, ": null;
                $sql .= "    client_cd1, ";
                $sql .= "    client_cd2, ";
                $sql .= "    client_name, ";
                $sql .= "    client_name2, ";
                $sql .= "    client_cname, ";
                $sql .= "    c_post_no1, ";
                $sql .= "    c_post_no2, ";
                $sql .= "    c_address1, ";
                $sql .= "    c_address2, ";
                $sql .= "    c_address3, ";
                $sql .= "    c_shop_name, ";
                $sql .= "    c_shop_name2, ";
                if ($direct_id != null){
                    $sql .= "    direct_cd, ";
                    $sql .= "    direct_name, ";
                    $sql .= "    direct_name2, ";
                    $sql .= "    direct_cname, ";
                    $sql .= "    d_post_no1, ";
                    $sql .= "    d_post_no2, ";
                    $sql .= "    d_address1, ";
                    $sql .= "    d_address2, ";
                    $sql .= "    d_address3, ";
                    $sql .= "    d_tel, ";
                    $sql .= "    d_fax, ";
                }
                $sql .= ($trans_id != null) ? " trans_name, " : null;
                $sql .= ($trans_id != null) ? " trans_cname, " : null;
                $sql .= "    ware_name, ";
                $sql .= "    c_staff_name, ";
                $sql .= "    e_staff_name, ";
                $sql .= ($ac_staff_name != null)? "    ac_staff_name, " : null;
                $sql .= "    royalty_amount,";
                $sql .= "    claim_id, ";
                $sql .= "    claim_div,";
                $sql .= "    total_split_num,";
                $sql .= "    slip_out ";
                $sql .= ")VALUES(";
                $sql .= "    (SELECT COALESCE(MAX(sale_id), 0)+1 FROM t_sale_h),";         
                $sql .= "    '$sale_no',";
                if($aord_id != NULL){
                    $sql .= "$aord_id,";
                }
                $sql .= "    '$sale_day',";
                $sql .= "    '$claim_day',";
                $sql .= "    $client_id,";
                //直送先が指定されているか
                if($direct_id != null){
                    $sql .= "    $direct_id,";
                }
                $sql .= "    '$trade_sale',";
                //運送業者が指定されているか
                if($trans_id != null){
                    $sql .= "    $trans_id,";
                }
                //グリーン指定が指定されているか
                if($trans_check != null){
                    if($trans_check=='t'){
                        $sql .= "true,";    
                    }else{
                        $sql .= "false,";    
                    }
                }
                $sql .= "    '$note',"; 
                $sql .= "    $cost_money,";   
                $sql .= "    $sale_money,";                  
                $sql .= "    $sale_tax,";        
                $sql .= "    $c_staff_id,";
                $sql .= "    $ware_id,";           
                $sql .= "    $e_staff_id,";
                $sql .= "    $shop_id,";
                $sql .= ($ac_staff_id != null)? $ac_staff_id."," : null;
                $sql .= "    (SELECT client_cd1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT client_cd2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT post_no1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT post_no2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT address1 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT address2 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT address3 FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT shop_name FROM t_client WHERE client_id = $client_id), ";
                $sql .= "    (SELECT shop_name2 FROM t_client WHERE client_id = $client_id), ";
                if ($direct_id != null){
                    $sql .= "   (SELECT direct_cd FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT direct_name FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT direct_cname FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT post_no1 FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT post_no2 FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT address1 FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT address2 FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT address3 FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT tel FROM t_direct WHERE direct_id = $direct_id), ";
                    $sql .= "   (SELECT fax FROM t_direct WHERE direct_id = $direct_id), ";
                }
                $sql .= ($trans_id != null) ? " (SELECT trans_name FROM t_trans WHERE trans_id = $trans_id), " : null;
                $sql .= ($trans_id != null) ? " (SELECT trans_cname FROM t_trans WHERE trans_id = $trans_id), " : null;
                $sql .= "   (SELECT ware_name FROM t_ware WHERE ware_id = $ware_id), ";
                $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $c_staff_id), ";
                $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $e_staff_id), ";
                $sql .= ($ac_staff_name != null)? "   (SELECT staff_name FROM t_staff WHERE staff_id = $ac_staff_id), " : null;
                $sql .= "   $royalty_data,";
                $sql.= "    (SELECT claim_id FROM t_claim WHERE client_id = $client_id AND claim_div = '1'),";
                $sql .= "   1,";
                $sql .= ($trade_sale == "15") ? "   2," : "   1,";
                $sql .= "    (SELECT slip_out FROM t_client WHERE client_id = $client_id) ";
                $sql .= ");";

                $result = Db_Query($db_con, $sql);
                //同時実行制御処理
                if($result == false){
                    $err_message = pg_last_error();
                    $err_format = "t_sale_h_sale_no_key";

                    Db_Query($db_con, "ROLLBACK");

                    //伝票番号が重複した場合            
                    if((strstr($err_message, $err_format) != false)){ 
                        $error = "同時に登録を行ったため、伝票番号が重複しました。もう一度登録をして下さい。";
     
                        //再度伝票番号を取得する
                        $sql  = "SELECT ";
                        $sql .= "   MAX(sale_no)";
                        $sql .= " FROM";
                        $sql .= "   t_sale_h";
                        $sql .= " WHERE";
                        $sql .= "   shop_id = $shop_id";
                        $sql .= ";";

                        $result = Db_Query($db_con, $sql);
                        $sale_no = pg_fetch_result($result, 0 ,0);
                        $sale_no = $sale_no +1;
                        $sale_no = str_pad($sale_no, 8, 0, STR_PAD_LEFT);

                        $err_data["form_sale_no"] = $sale_no;

                        $form->setConstants($err_data);

                        $duplicate_flg = true;
                    }else{
                        exit;
                    }
                }
            }

            if($duplicate_flg != true){

                //売上IDを抽出
                $sql  = "SELECT";
                $sql .= "   sale_id ";
                $sql .= "FROM";
                $sql .= "   t_sale_h ";
                $sql .= "WHERE";
                $sql .= "   sale_no = '$sale_no'";
                $sql .= "   AND";
                $sql .= "   shop_id = $shop_id";
                $sql .= ";";

                $result = Db_Query($db_con, $sql);
                $insert_sale_id= pg_fetch_result($result,0,0);


                //割賦売上テーブルに登録
                if($trade_sale == "15"){
                    Db_Query($db_con, "BEGIN;");
                    $division_array = Division_Price($db_con, $client_id, ($sale_money + $sale_tax), $claim_day_y, $claim_day_m);
                    for($k=0;$k<2;$k++){
                        $sql  = "INSERT INTO t_installment_sales (";
                        $sql .= "   installment_sales_id,";
                        $sql .= "   sale_id,";
                        $sql .= "   collect_day,";
                        $sql .= "   collect_amount";
                        $sql .= ")VALUES(";
                        $sql .= "   (SELECT COALESCE(MAX(installment_sales_id),0)+1 FROM t_installment_sales),";
                        $sql .= "   $insert_sale_id,";
                        $sql .= "   '".$division_array[1][$k]."',";
                        $sql .= "   ".$division_array[0][$k]." ";
                        $sql .= ");";

                        $result = Db_Query($db_con, $sql);
                        if($result === false){
                            Db_Query($db_con, "ROLLBACK;");
                            exit;
                        }
                    }

                    //売上ヘッダに分割回数登録
                    $sql = "UPDATE t_sale_h SET total_split_num = 2 WHERE sale_id = $insert_sale_id;";
                    $result = Db_Query($db_con, $sql);
                    if($result === false){
                        Db_Query($db_con, "ROLLBACK;");
                        exit;
                    }

                    Db_Query($db_con, "COMMIT;");
                }

                //売上データ登録
                for($i = 0; $i < count($goods_id); $i++){
                    //行
                    $line = $i + 1;

                    //形式変更
//                  $c_price = $cost_price_i[$i].".".$cost_price_d[$i];   //原価金額
//                  $s_price = $sale_price_i[$i].".".$sale_price_d[$i];   //売上金額

                    //受注データIDを抽出
                    if($aord_id != NULL){
                        $sql  = "SELECT";
                        $sql .= "   aord_d_id";
                        $sql .= " FROM";
                        $sql .= "   t_aorder_d";
                        $sql .= " WHERE";
                        $sql .= "   aord_id = $aord_id";
                        $sql .= "   AND";
                        $sql .= "   line = $line";
                        $sql .= ";";

                        $result = Db_Query($db_con, $sql);
                        $aord_d_id = pg_fetch_result($result,0,0);
                    }

/*
                    //割賦売上テーブルに登録
                    $sql  = "INSERT INTO t_installment_sales (";
                    $sql .= "   installment_sales_id,";
                    $sql .= "   sale_id,";
                    $sql .= "   collect_day,";
                    $sql .= "   collect_amount";
                    $sql .= ")VALUES(";
                    $sql .= "   (SELECT COALESCE(MAX(installment_sales_id),0)+1 FROM t_installment_sales),";
                    $sql .= "   $insert_sale_id,";
                    $sql .= "   '$claim_day',";
                    $sql .= "   $sale_amount[$i]";
                    $sql .= ");";

                    $result = Db_Query($db_con, $sql);
                    if($result === false){
                        Db_Query($db_con, $sql);
                        exit;
                    }
*/

                    //売上データテーブルに登録
                    $sql  = "INSERT INTO t_sale_d (\n";
                    $sql .= "    sale_d_id,\n";
                    $sql .= "    sale_id,\n";
                    $sql .= "    line,\n";
                    $sql .= "    goods_id,\n";
                    //$sql .= "    goods_name,\n";
                    $sql .= "    official_goods_name,\n";
                    $sql .= "    num,\n";
                    $sql .= "    tax_div,\n";
                    $sql .= "    cost_price,\n";
                    $sql .= "    cost_amount,\n";
                    $sql .= "    sale_price,\n";
                    $sql .= "    sale_amount, \n";
                    if($aord_id != NULL){
                        $sql .= "aord_d_id, \n";
                    }
                    $sql .= "    goods_cd, \n";
                    $sql .= "    unit, \n";
                    $sql .= "    royalty,\n";
                    $sql .= "    g_product_name\n";
                    $sql .= ")VALUES(\n";
                    $sql .= "    (SELECT COALESCE(MAX(sale_d_id), 0)+1 FROM t_sale_d),\n";  
/*
                    $sql .= "    (SELECT";
                    $sql .= "         sale_id";
                    $sql .= "     FROM";
                    $sql .= "        t_sale_h";
                    $sql .= "     WHERE";
                    $sql .= "        sale_no = '$sale_no'";
                    $sql .= "        AND";
                    $sql .= "        shop_id = $shop_id";
                    $sql .= "    ),";
*/
                    $sql .= "    $insert_sale_id,\n";
                    $sql .= "    '$line',\n";
                    $sql .= "    $goods_id[$i],\n";
                    $sql .= "    '$goods_name[$i]',\n"; 
                    $sql .= "    '$sale_num[$i]',\n";
                    $sql .= "    '$tax_div[$i]',\n";
                    $sql .= "    $c_price[$i],\n";
                    $sql .= "    $cost_amount[$i],\n";
                    $sql .= "    $s_price[$i],\n";
                    $sql .= "    $sale_amount[$i], \n";
                    if($aord_id != NULL){
                        $sql .= "$aord_d_id, \n";
                    }
                    $sql .= "    (SELECT goods_cd FROM t_goods WHERE goods_id = $goods_id[$i]), \n";
                    $sql .= "    (SELECT unit FROM t_goods WHERE goods_id = $goods_id[$i]), \n";
                    $sql .= "   '$royalty[$i]',\n";
                    $sql .= "    (SELECT t_g_product.g_product_name FROM t_goods INNER JOIN t_g_product ON t_goods.g_product_id = t_g_product.g_product_id WHERE t_goods.goods_id = $goods_id[$i])\n";
                    $sql .= ");\n";
                    $result = Db_Query($db_con, $sql);

                    if($result == false){
                        Db_Query($db_con, "ROLLBACK");
                        exit;
                    }
                }

                //取引区分が値引きの場合は処理しない
                if($trade_sale != "14" && $trade_sale != "64"){

                    for($i = 0; $i < count($goods_id); $i++){
                        $line = $i + 1;
                        //受け払いテーブルに登録

                        $sql  = " INSERT INTO t_stock_hand (";
                        $sql .= "    goods_id,";
                        $sql .= "    enter_day,";
                        $sql .= "    work_day,";
                        $sql .= "    work_div,";
                        $sql .= "    client_id,";
                        //aoyama-n 2009-07-10
                        $sql .= "    client_cname,";
                        $sql .= "    ware_id,";
                        $sql .= "    io_div,";
                        $sql .= "    num,";
                        $sql .= "    slip_no,";
                        $sql .= "    sale_d_id,";
                        $sql .= "    staff_id,";
                        $sql .= "    shop_id";
                        $sql .= ")VALUES(";
                        $sql .= "    $goods_id[$i],";
                        $sql .= "    NOW(),";
                        $sql .= "    '$sale_day',";
                        $sql .= "    '2',";
                        $sql .= "    $client_id,";
                        //aoyama-n 2009-07-10
                        $sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id),";
                        $sql .= "    $ware_id,";

                        //取引区分が返品の場合は入庫で処理する
                        if($trade_sale == "13" || $trade_sale == "63"){
                            $sql .= "    '1',";
                        }else{
                            $sql .= "    '2',";
                        }

                        $sql .= "    $sale_num[$i],";
                        $sql .= "    '$sale_no',";
                        $sql .= "    (SELECT";
                        $sql .= "        sale_d_id";
                        $sql .= "    FROM";
                        $sql .= "        t_sale_d";
                        $sql .= "    WHERE";
                        $sql .= "        line = $line";
                        $sql .= "        AND";
                        $sql .= "        sale_id = (SELECT";
                        $sql .= "                    sale_id";
                        $sql .= "                 FROM";
                        $sql .= "                    t_sale_h";
                        $sql .= "                 WHERE";
                        $sql .= "                    sale_no = '$sale_no'";
                        $sql .= "                    AND";
                        $sql .= "                    shop_id = $shop_id";
                        $sql .= "                )";
                        $sql .= "    ),";
                        $sql .= "    $e_staff_id,";
                        $sql .= "    $shop_id";
                        $sql .= ");";

                        $result = Db_Query($db_con, $sql);
                        if($result == false){
                            Db_Query($db_con, "ROLLBACK");
                            exit;
                        }

                        //受注数がある場合は、引当も受払に登録
                        if($aord_id != NULL){
                            $sql  = " INSERT INTO t_stock_hand (";
                            $sql .= "    goods_id,";
                            $sql .= "    enter_day,";
                            $sql .= "    work_day,";
                            $sql .= "    work_div,";
                            $sql .= "    client_id,";
                            $sql .= "    ware_id,";
                            $sql .= "    io_div,";
                            $sql .= "    num,";
                            $sql .= "    slip_no,";
                            $sql .= "    sale_d_id,";
                            $sql .= "    staff_id,";
                            $sql .= "    shop_id";
                            $sql .= ")VALUES(";
                            $sql .= "    $goods_id[$i],";
                            $sql .= "    NOW(),";
                            $sql .= "    '$sale_day',";
                            $sql .= "    '1',";
                            $sql .= "    $client_id,";
                            $sql .= "    $ware_id,";
                            $sql .= "    '1',";
                            $sql .= "    $aorder_num[$i],";
                            $sql .= "    '$sale_no',";
                            $sql .= "    (SELECT";
                            $sql .= "        sale_d_id";
                            $sql .= "    FROM";
                            $sql .= "        t_sale_d";
                            $sql .= "    WHERE";
                            $sql .= "        line = $line";
                            $sql .= "        AND";
                            $sql .= "        sale_id = (SELECT";
                            $sql .= "                    sale_id";
                            $sql .= "                 FROM";
                            $sql .= "                    t_sale_h";
                            $sql .= "                 WHERE";
                            $sql .= "                    sale_no = '$sale_no'";
                            $sql .= "                    AND";
                            $sql .= "                    shop_id = $shop_id";
                            $sql .= "                )";
                            $sql .= "    ),";
                            $sql .= "    $e_staff_id,";
                            $sql .= "    $shop_id";
                            $sql .= ");";

                            $result = Db_Query($db_con, $sql);
                            if($result == false){
                                Db_Query($db_con, "ROLLBACK");
                                exit;
                            }
                        }
                    }
                }

                //受注照会・受注残から遷移してきた場合は、受注ヘッダの処理状況を完了にする
                if($aord_id != NULL){
                    $sql  = "UPDATE t_aorder_h SET ";
                    $sql .= "    ps_stat = '3' ";
                    $sql .= "WHERE ";
                    $sql .= "    aord_id = $aord_id;";
                    
                    $result = Db_Query($db_con, $sql);
                    if($result == false){
                        Db_Query($db_con, "ROLLBACK");
                        exit;
                    }
                }

                //新規登録の場合は、GET情報が無い為、GET情報作成
                if($sale_id == null){
                    //売上確認に渡す売上ID取得
                    $sql  = "SELECT ";
                    $sql .= "    sale_id ";
                    $sql .= "FROM ";
                    $sql .= "    t_sale_h ";
                    $sql .= "WHERE ";
                    $sql .= "    sale_no = '$sale_no'";
                    $sql .= "AND ";
                    $sql .= "    shop_id = $shop_id;";
                    $result = Db_Query($db_con, $sql);
                    $sale_id = pg_fetch_result($result,0,0);
                }

                //取引区分に現金を指定した場合は入金テーブルに登録を行う
                if($trade_sale == '61' || $trade_sale == '63' || $trade_sale == '64'){

                    //自動採番の伝票番号取得
                    $sql  = "SELECT";
                    $sql .= "   MAX(pay_no)";
                    $sql .= " FROM";
                    $sql .= "   t_payin_h";
                    $sql .= " WHERE";
                    $sql .= "   shop_id = $shop_id";
                    $sql .= ";"; 

                    $result = Db_Query($db_con, $sql);
                    $pay_no = pg_fetch_result($result, 0 ,0);
                    $pay_no = $pay_no +1;
                    $pay_no = str_pad($pay_no, 8, 0, STR_PAD_LEFT);

                    $sql  = "INSERT INTO t_payin_h(\n";
                    $sql .= "   pay_id,\n";                         //入金ID
                    $sql .= "   pay_no,\n";                         //入金NO
                    $sql .= "   pay_day,\n";                        //入金日
                    $sql .= "   client_id,\n";                      //得意先ID
                    $sql .= "   client_cd1,\n";                     //得意先コード１
                    $sql .= "   client_cd2,\n";                     //得意先コード２
                    $sql .= "   client_name,\n";                    //得意先名
                    $sql .= "   client_cname,\n";                   //略称
                    $sql .= "   claim_div,\n";                      //請求先区分
                    $sql .= "   claim_cd1,\n";                      //請求先コード１
                    $sql .= "   claim_cd2,\n";                      //請求先コード２
                    $sql .= "   claim_cname,\n";                    //略称
                    $sql .= "   input_day,\n";                      //入力日
                    $sql .= "   e_staff_id,\n";                     
                    $sql .= "   e_staff_name,\n";
                    $sql .= "   ac_staff_id,\n";
                    $sql .= "   ac_staff_name,\n";
                    $sql .= "   sale_id,\n";
                    $sql .= "   shop_id,\n";
                    $sql .= "   collect_staff_id,\n";
                    $sql .= "   collect_staff_name\n";
                    $sql .= ")VALUES(\n";
                    //$sql .= "   (SELECT COALESCE(MAX(pay_id),0)+1 FROM t_payin_h WHERE shop_id = $shop_id),\n";
                    $sql .= "   (SELECT COALESCE(MAX(pay_id),0)+1 FROM t_payin_h),\n";
                    $sql .= "   '$pay_no',\n";
                    $sql .= "   '$sale_day',\n";
                    $sql .= "   $client_id,\n";
                    $sql .= "   (SELECT client_cd1 FROM t_client WHERE client_id = $client_id),\n";
                    $sql .= "   (SELECT client_cd2 FROM t_client WHERE client_id = $client_id),\n";
                    $sql .= "   (SELECT client_name FROM t_client WHERE client_id = $client_id),\n";
                    $sql .= "   (SELECT client_cname FROM t_client WHERE client_id = $client_id),\n";
                    $sql .= "   '1',\n";
                    $sql .= "   (SELECT\n";
                    $sql .= "       client_cd1\n";
                    $sql .= "   FROM\n";
                    $sql .= "       t_claim\n";
                    $sql .= "           INNER JOIN \n";
                    $sql .= "       t_client\n";
                    $sql .= "       ON t_claim.claim_id = t_client.client_id\n";
                    $sql .= "   WHERE\n";
                    $sql .= "       t_claim.client_id = $client_id\n";
                    $sql .= "       AND\n";
                    $sql .= "       t_claim.claim_div = '1'\n";
                    $sql .= "   ),\n";
                    $sql .= "   (SELECT\n";
                    $sql .= "       client_cd2\n";
                    $sql .= "   FROM\n";
                    $sql .= "       t_claim\n";
                    $sql .= "           INNER JOIN\n";
                    $sql .= "       t_client\n";
                    $sql .= "       ON t_claim.claim_id = t_client.client_id\n";
                    $sql .= "   WHERE\n";
                    $sql .= "       t_claim.client_id = $client_id\n";
                    $sql .= "       AND\n";
                    $sql .= "       t_claim.claim_div = '1'\n";
                    $sql .= "   ),\n";
                    $sql .= "   (SELECT\n";
                    $sql .= "       client_cname\n";
                    $sql .= "   FROM\n";
                    $sql .= "       t_claim\n";
                    $sql .= "           INNER JOIN\n";
                    $sql .= "       t_client\n";
                    $sql .= "       ON t_claim.claim_id = t_client.client_id\n";
                    $sql .= "   WHERE\n";
                    $sql .= "       t_claim.client_id = $client_id\n";
                    $sql .= "       AND\n";
                    $sql .= "       t_claim.claim_div = '1'\n";
                    $sql .= "   ),\n";
                    $sql .= "   '$sale_day',\n";
                    $sql .= "   $e_staff_id,\n";
                    $sql .= "   '".addslashes($e_staff_name)."',\n";
                    $sql .= "   $c_staff_id,\n";
                    $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $c_staff_id),\n";
                    $sql .= "   $insert_sale_id,\n";
                    $sql .= "   $shop_id,\n";
                    $sql .= "   $c_staff_id,\n";
                    $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $c_staff_id)\n";
                    $sql .= ");";

                    $result = Db_Query($db_con, $sql);

                    //同時実行制御処理
                    if($result == false){
                        $err_message = pg_last_error();
                        $err_format = "t_payin_h_pay_no_key";

                        Db_Query($db_con, "ROLLBACK");

                        //伝票番号が重複した場合            
                        if((strstr($err_message, $err_format) != false)){ 
                            $error = "同時に登録を行ったため、伝票番号が重複しました。もう一度登録をして下さい。";
                            $duplicate_flg = true;
                        }else{
                            exit;
                        }
                    }

                    //入金データに登録

                    //現金値引き、現金返品の場合は−を付ける
                    if($trade_sale == '63' || $trade_sale == '64'){
                        $sale_money = "-".$sale_money;
                        #2010-02-03 aoyama-n 
                        $sale_tax = "-".$sale_tax;
                    }

                    $sql  = "INSERT INTO t_payin_d(\n";
                    $sql .= "   pay_d_id,\n";
                    $sql .= "   pay_id,\n";
                    $sql .= "   trade_id,\n";
                    $sql .= "   amount\n";
                    $sql .= ")VALUES(";
                    $sql .= "   (SELECT COALESCE(MAX(pay_d_id), 0)+1 FROM t_payin_d),\n";
                    $sql .= "   (SELECT\n";
                    $sql .= "       pay_id\n";
                    $sql .= "   FROM\n";
                    $sql .= "       t_payin_h\n";
                    $sql .= "   WHERE\n";
                    $sql .= "       pay_no = '$pay_no'\n";
                    $sql .= "       AND\n";
                    $sql .= "       shop_id = $shop_id\n";
                    $sql .= "   ),\n";
                    $sql .= "   '39',\n";
                    $sql .= "   ($sale_money + $sale_tax)\n";
                    $sql .= ");\n";

                    $result = Db_Query($db_con, $sql);
                    if($result === false){
                        Db_Query($db_con, "ROLLBACK");
                        exit;
                    }
                }

                Db_Query($db_con, "COMMIT");
                header("Location: ./1-2-205.php?sale_id=$sale_id&input_flg=true");
            }
        }else{
            //登録確認画面を表示フラグ
            $comp_flg = true;
        }
    }else{
        //エラーかつ受注IDが無ければ初期化
        if($aord_id == NULL){
            $client_data["show_button_flg"]     = "";        //表示ボタン
            $client_data["form_ord_no"]         = "";        //受注番号
            $form->setConstants($client_data);
        }
    }
}

/****************************/
// 得意先の状態取得
/****************************/
$client_state_print = Get_Client_State($db_con, $client_id);

/****************************/
//部品作成（可変）
/****************************/
//行番号カウンタ
$row_num = 1;


//得意先が選択されていない場合はフォーム非表示
if($warning != null || $comp_flg == true){
    #2009-09-15 hashimoto-y
    #$style = "color: #000000; border: #ffffff 1px solid; background-color: #ffffff";
    $style = "border: #ffffff 1px solid; background-color: #ffffff";
    $type = "readonly";
}else{
    $type = $g_form_option;
}

for($i = 0; $i < $max_row; $i++){
    //表示行判定
    if(!in_array("$i", $del_history)){
        $del_data = $del_row.",".$i;


        #2009-09-10 hashimoto-y
        //値引商品を選択した場合には赤字に変更
        $font_color = "";

        $trade_sale_select = $form->getElementValue("trade_sale_select");
        $hdn_discount_flg = $form->getElementValue("hdn_discount_flg[$i]");

        #print_r($hdn_discount_flg);

        if($hdn_discount_flg === 't' || 
           $trade_sale_select[0] == '13' || $trade_sale_select[0] == '14' || $trade_sale_select[0] == '63' || $trade_sale_select[0] == '64'
        ){
            $font_color = "color: red; ";
        }else{
            $font_color = "color: #000000; ";
        }


//受注の有無にかかわらず変更可にする（watanabe-k）
        //遷移前画面判定
        if($aord_id == NULL){
            //売上照会・伝票発行から遷移してきた場合

            //商品コード      
            #2009-09-10 hashimoto-y
            $form->addElement(
                "text","form_goods_cd[$i]","","size=\"10\" maxLength=\"8\"
                style=\"$font_color $style $g_form_style \" $type 
                onChange=\"return goods_search_2(this.form, 'form_goods_cd', 'goods_search_row', $i ,$row_num)\""
            );
          
        }else{
            //受注照会・受注残から遷移した場合

            //商品コード      
            #2009-09-10 hashimoto-y
            $form->addElement(
                "text","form_goods_cd[$i]","",
                "size=\"10\" style=\" $font_color
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: left\" readonly'"
            );
        }       

            //商品名
            //変更不可判定
            if(($_POST["hdn_name_change"][$i] == '2' || $hdn_name_change[$i] == '2') && $comp_flg != true){
                //不可
                #2009-09-10 hashimoto-y
                $form->addElement(
                    "text","form_goods_name[$i]","",
                    "size=\"52\" style=\" $font_color \" $g_text_readonly" 
                );
            }else{
                //可
                #2009-09-10 hashimoto-y
                $form->addElement(
                    "text","form_goods_name[$i]","",
                    "size=\"52\" maxLength=\"41\" 
                    style=\"$font_color $style\" $type"
                );
            }

            //現在庫数
            #2009-09-10 hashimoto-y
            $form->addElement(
                "text","form_stock_num[$i]","",
//                "size=\"11\" style=\"color : #585858; 
//                border : #ffffff 1px solid; 
//                background-color: #ffffff; 
//                text-align: right\" readonly'"
                "class=\"money\" size=\"11\" maxLength=\"9\"
                 style=\"$font_color border: #ffffff 1px solid; background-color: #ffffff; $g_form_style\"
                 $g_text_readonly"
            );

            //出荷数
            #2009-09-10 hashimoto-y
            #2009-10-13 hashimoto-y
            //受注から起こした売上は出荷数変更不可
            if($aord_id != null){
                $form->addElement(
                    "text","form_sale_num[$i]","",
                    "class=\"money\" size=\"11\" maxLength=\"5\"
                     style=\"$font_color border: #ffffff 1px solid; background-color: #ffffff; $g_form_style\"
                     $g_text_readonly"
                );
            }else{
                $form->addElement(
                    "text","form_sale_num[$i]","",
                    "class=\"money\" size=\"11\" maxLength=\"5\" 
                    onKeyup=\"Mult_double('hdn_goods_id[$i]','form_sale_num[$i]','form_sale_price[$i][i]','form_sale_price[$i][d]','form_sale_amount[$i]','form_cost_price[$i][i]','form_cost_price[$i][d]','form_cost_amount[$i]','$coax');\"
                    style=\"text-align: right; $font_color $style $g_form_style \" $type "
                );
            }

            //原価単価
            #2009-09-10 hashimoto-y
            $form_cost_price[$i][] =& $form->createElement(
                "text","i","",
                "size=\"11\" maxLength=\"9\"
                class=\"money\"
                onKeyup=\"Mult_double('hdn_goods_id[$i]','form_sale_num[$i]','form_sale_price[$i][i]','form_sale_price[$i][d]','form_sale_amount[$i]','form_cost_price[$i][i]','form_cost_price[$i][d]','form_cost_amount[$i]','$coax');\"
                style=\"text-align: right; $font_color $style $g_form_style\"
                $type"
            );
            $form_cost_price[$i][] =& $form->createElement("static","","",".");
            #2009-09-10 hashimoto-y
            $form_cost_price[$i][] =& $form->createElement(
                "text","d","","size=\"2\" maxLength=\"2\" 
                onKeyup=\"Mult_double('hdn_goods_id[$i]','form_sale_num[$i]','form_sale_price[$i][i]','form_sale_price[$i][d]','form_sale_amount[$i]','form_cost_price[$i][i]','form_cost_price[$i][d]','form_cost_amount[$i]','$coax');\"
                style=\"text-align: left; $font_color $style $g_form_style\"
                $type"
            );
            $form->addGroup( $form_cost_price[$i], "form_cost_price[$i]", "");

            //原価金額
            #2009-09-10 hashimoto-y
            $form->addElement(
                "text","form_cost_amount[$i]","",
                "size=\"25\" maxLength=\"18\" 
                style=\" $font_color 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            
            //売上単価
            #2009-09-10 hashimoto-y
            $form_sale_price[$i][] =& $form->createElement(
                "text","i","",
                "size=\"11\" maxLength=\"9\"
                class=\"money\"
                onKeyup=\"Mult_double('hdn_goods_id[$i]','form_sale_num[$i]','form_sale_price[$i][i]','form_sale_price[$i][d]','form_sale_amount[$i]','form_cost_price[$i][i]','form_cost_price[$i][d]','form_cost_amount[$i]','$coax');\"
                style=\"text-align: right; $font_color $style $g_form_style\"
                $type"
            );
            $form_sale_price[$i][] =& $form->createElement("static","","",".");
            #2009-09-10 hashimoto-y
            $form_sale_price[$i][] =& $form->createElement(
                "text","d","","size=\"2\" maxLength=\"2\" 
                onKeyup=\"Mult_double('hdn_goods_id[$i]','form_sale_num[$i]','form_sale_price[$i][i]','form_sale_price[$i][d]','form_sale_amount[$i]','form_cost_price[$i][i]','form_cost_price[$i][d]','form_cost_amount[$i]','$coax');\"
                style=\"text-align: left; $font_color $style $g_form_style\"
                $type"
            );
            $form->addGroup( $form_sale_price[$i], "form_sale_price[$i]", "");

            //売上金額
            #2009-09-10 hashimoto-y
            $form->addElement(
                "text","form_sale_amount[$i]","",
                "size=\"25\" maxLength=\"18\" 
                style=\" $font_color
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );

            //登録確認画面の場合は非表示
            if($comp_flg != true){
                //検索リンク
                $form->addElement(
                    "link","form_search[$i]","","#","検索",
                    "TABINDEX=-1 onClick=\"return Open_SubWin_2('../dialog/1-0-210.php', Array('form_goods_cd[$i]','goods_search_row'), 500, 450,5,'',$i,$row_num);\""
                );

                //削除リンク
                //最終行を削除する場合、削除した後の最終行に合わせる
                if($row_num == $max_row-$del_num){
                    $form->addElement(
                        "link","form_del_row[$i]","",
                        "#","<font color='#FEFEFE'>削除</font>","TABINDEX=-1 onClick=\"javascript:Dialogue_3('削除します。', '$del_data', 'del_row' ,$row_num-1);return false;\""
                    );
                //最終行以外を削除する場合、削除する行と同じNOの行に合わせる
                }else{
                    $form->addElement(
                        "link","form_del_row[$i]","",
                        "#","<font color='#FEFEFE'>削除</font>","TABINDEX=-1 onClick=\"javascript:Dialogue_3('削除します。', '$del_data', 'del_row' ,$row_num);return false;\""
                    );
                }
            }

            //受注数
            //受注入力・受注残から遷移してきた場合、受注数を表示
            if($aord_id != NULL){
                $form->addElement(
                    "text","form_aorder_num[$i]","",
//                    "size=\"11\" style=\"color : #585858; 
//                    border : #ffffff 1px solid; 
//                    background-color: #ffffff; 
//                    text-align: right\" readonly'"
                    "class=\"money\" size=\"11\" maxLength=\"9\"
                     style=\"$font_color border: #ffffff 1px solid; background-color: #ffffff; $g_form_style\"
                     $g_form_option"
                );
            }

            //商品ID
            $form->addElement("hidden","hdn_goods_id[$i]");
            //課税区分
            $form->addElement("hidden", "hdn_tax_div[$i]");
            //品名変更フラグ
            $form->addElement("hidden","hdn_name_change[$i]");
            //ロイヤリティ
            $form->addElement("hidden","hdn_royalty[$i]");
            //aoyama-n 2009-09-04
            //値引フラグ
            #$form->addElement("hidden","hdn_discount_flg[$i]");
/*
        }else{
            //受注照会・受注残から遷移した場合

            //商品コード      
            $form->addElement(
                "text","form_goods_cd[$i]","",
                "size=\"10\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: left\" readonly'"
            );
        
            //商品名
            $form->addElement(
                "text","form_goods_name[$i]","",
                "size=\"34\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: left\" readonly'"
            );

            //現在庫数
            $form->addElement(
                "text","form_stock_num[$i]","",
                "size=\"11\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );

            //出荷数
            $form->addElement(
                "text","form_sale_num[$i]","",
                "size=\"11\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );

            //原価単価
            $form_cost_price[$i][] =& $form->createElement(
                "text","i","",
                "size=\"11\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            $form_cost_price[$i][] =& $form->createElement("static","","",".");
            $form_cost_price[$i][] =& $form->createElement(
                "text","d","",
                "size=\"2\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: left\" readonly'"
            );
            $form->addGroup( $form_cost_price[$i], "form_cost_price[$i]", "");

            //原価金額
            $form->addElement(
                "text","form_cost_amount[$i]","",
                "size=\"25\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            
            //売上単価
            $form_sale_price[$i][] =& $form->createElement(
                "text","i","",
                "size=\"11\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );
            $form_sale_price[$i][] =& $form->createElement("static","","",".");
            $form_sale_price[$i][] =& $form->createElement(
                "text","d","",
                "size=\"2\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: left\" readonly'"
            );
            $form->addGroup( $form_sale_price[$i], "form_sale_price[$i]", "");

            //売上金額
            $form->addElement(
                "text","form_sale_amount[$i]","",
                "size=\"25\" style=\"color : #585858; 
                border : #ffffff 1px solid; 
                background-color: #ffffff; 
                text-align: right\" readonly'"
            );

            //受注数
            //受注入力・受注残から遷移してきた場合、受注数を表示
            if($aord_id != NULL){
                $form->addElement(
                    "text","form_aorder_num[$i]","",
                    "size=\"11\" style=\"color : #585858; 
                    border : #ffffff 1px solid; 
                    background-color: #ffffff; 
                    text-align: right\" readonly'"
                );
            }

            //商品ID
            $form->addElement("hidden","hdn_goods_id[$i]");
            //課税区分
            $form->addElement("hidden", "hdn_tax_div[$i]");
            //品名変更フラグ
            $form->addElement("hidden","hdn_name_change[$i]");
        }
        /****************************/
        //表示用HTML作成
        /****************************/
        $html .= "<tr class=\"Result1\">";
        $html .=    "<A NAME=$row_num><td align=\"right\">$row_num</td></A>";
        $html .=    "<td align=\"left\">";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_cd[$i]"]]->toHtml();
        if($warning == null && $aord_id == NULL && $comp_flg != true){
            $html .=    "（";
            $html .=        $form->_elements[$form->_elementIndex["form_search[$i]"]]->toHtml();
            $html .=    "）";
        }
        $html .=    "<br>";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_name[$i]"]]->toHtml();
        $html .=    "</td>";
        if($aord_id != NULL){
            $html .=    "<td align=\"right\">";
            $html .=        $form->_elements[$form->_elementIndex["form_aorder_num[$i]"]]->toHtml();
            $html .=    "</td>";
        }
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_stock_num[$i]"]]->toHtml();
        $html .=    "</td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_sale_num[$i]"]]->toHtml();
        $html .=    "</td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_cost_price[$i]"]]->toHtml();
        $html .=    "<br>";
        $html .=        $form->_elements[$form->_elementIndex["form_sale_price[$i]"]]->toHtml();
        $html .=    "</td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_cost_amount[$i]"]]->toHtml();
        $html .=    "<br>";
        $html .=        $form->_elements[$form->_elementIndex["form_sale_amount[$i]"]]->toHtml();
        $html .=    "</td>";

        if($warning == null && $aord_id == NULL && $comp_flg != true){
            $html .= "  <td class=\"Title_Add\" align=\"center\">";
            $html .=        $form->_elements[$form->_elementIndex["form_del_row[$i]"]]->toHtml();
            $html .= "  </td>";
        }
        $html .= "</tr>";

        //行番号を＋１
        $row_num = $row_num+1;
    }
}

//登録確認画面では、以下のボタンを表示しない
if($comp_flg != true){

    //表示
    if($sale_id == NULL){
        $form->addElement("button","form_show_button","表　示","onClick=\"javascript:Button_Submit('show_button_flg','#','true')\"");
    }

    //button
    $form->addElement("submit","form_sale_btn","売上確認画面へ", $disabled);
    $form->addElement("button","form_back_button","戻　る","onClick=\"javascript:history.back()\"");

    //合計
//  if($aord_id == NULL){
        $form->addElement("button","form_sum_btn","合　計","onClick=\"javascript:Button_Submit('sum_button_flg','#foot','true')\"");
//  }

    //行追加リンク
    if($aord_id == NULL){
        $form->addElement("button","add_row_link","行追加","onClick=\"javascript:Button_Submit_1('add_row_flg', '#foot', 'true')\"");
    }

}else{
    //登録確認画面では以下のボタンを表示
    //戻る
    $form->addElement("button","return_button","戻　る","onClick=\"javascript:history.back()\"");
    
    //OK
    $form->addElement("submit","comp_button","売上OK", $disabled);
    
    $form->freeze();
}

#2009-09-28 hashimoto-y
#$debug_trade_sale = $form->getElementValue("trade_sale_select");
#$debug_trade_sale = $trade_form->loadArray("trade_sale_select");
#$debug_trade_sale = $trade_form->getSelected();
#print_r($debug_trade_sale);


/*
print "<pre>";
print_r ($_POST);
print "</pre>";
*/
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
$page_menu = Create_Menu_h('sale','2');
/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "　".$form->_elements[$form->_elementIndex["203_button"]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex["201_button"]]->toHtml();
$page_header = Create_Header($page_title);

// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());



$smarty->assign('goods_error1',$goods_error1);
$smarty->assign('goods_error2',$goods_error2);
$smarty->assign('goods_error3',$goods_error3);
$smarty->assign('goods_error4',$goods_error4);
$smarty->assign('goods_error5',$goods_error5);
$smarty->assign("duplicate_goods_err", $duplicate_goods_err);

//その他の変数をassign
$smarty->assign('var',array(
    'html_header'   => "$html_header",
    'page_menu'     => "$page_menu",
    'page_header'   => "$page_header",
    'html_footer'   => "$html_footer",
    'warning'       => "$warning",
    'html'          => "$html",
    'form_potision' => "$form_potision",
    'aord_id'       => "$aord_id",
    'duplicate_err' => "$error",
    'goods_error0'  => "$goods_error0",
    'comp_flg'      => "$comp_flg",
    'aord_id_err'   => "$aord_id_err",
    'auth_r_msg'    => "$auth_r_msg",
    "client_state_print"    => "$client_state_print",
));

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
