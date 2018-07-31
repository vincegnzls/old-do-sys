<?php

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006/11/07      08-072      suzuki      帳票出力時にはサニタイジングを行わないように修正
 *  2006/11/07      08-100      suzuki      売上IDに文字が入力されたときにＴＯＰに遷移させるように修正
 *  2006/11/07      08-101      suzuki      自ショップ以外の売上IDが入力されたときにＴＯＰに遷移させるように修正
 *
 *
 */

/*
 変更履歴
 2006-11-27 納品書に御中を印字するように修正 <suzuki>
 *   2007/03/01                  morita-d    商品名は正式名称を表示するように変更 
 *   2007/05/11                  watanabe-k  直送の場合、出荷案内書の左上は「直送先の情報」右下は「依頼元の情報」を表示する 
 *   2007/06/08                  watanabe-k  取引区分により金額をマイナス表示するように修正 
 *   2007/06/14                  watanabe-k  変数の初期化ができなったバグの修正 
 *   2009/09/16                  aoyama-n    値引商品及び取引区分が値引・返品の場合は赤字で表示
*/

require_once("ENV_local.php");
require(FPDF_DIR);
$pdf=new MBFPDF('P','pp','a4');
$pdf->SetProtection(array('print', 'copy'));
$pdf->AddMBFont(GOTHIC ,'SJIS');
$pdf->AddMBFont(MINCHO ,'SJIS');
$pdf->SetAutoPageBreak(false);

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth = Auth_Check($db_con);

/****************************/
//外部変数取得
/****************************/
$staff_id     = $_SESSION[staff_id];
$shop_id      = $_SESSION[client_id];

$photo = COMPANY_SEAL_DIR.$shop_id.".jpg";    //社印のファイル名（得意先の担当支店の）
$photo_exists = file_exists($photo);                    //社印が存在するかフラグ

/****************************/
//契約関数定義
/****************************/
require_once(INCLUDE_DIR."function_keiyaku.inc");
//不正判定
Get_ID_Check3($_GET["sale_id"]);

//遷移元判定
if($_GET["sale_id"] != NULL){
    //確認画面から遷移
    $sale_id      = array($_GET["sale_id"]);
	//整合性判定関数
	Injustice_check($db_con,"sale",$sale_id[0],$shop_id);
}else{
    //一括発行から遷移
    if($_POST["hdn_button"]==  "再発行"){
        $ary_sale_id      = @array_values($_POST["re_slip_check"]);          //売上ID配列
    }else{
        $ary_sale_id      = @array_values($_POST["slip_check"]);          //売上ID配列
    }

    for($i = 0; $i < count($ary_sale_id); $i++){
        if($ary_sale_id[$i] != 'f' &&  $ary_sale_id[$i] != null ){
            $sale_id[] = $ary_sale_id[$i];
			//整合性判定関数
			Injustice_check($db_con,"sale",$ary_sale_id[$i],$shop_id);
        }
    }

    //チェックされているかカウント
    if(count($sale_id) > 0){
        $imp_sale_id = implode(",",$sale_id);
    }else{
        print "<font color=\"red\"><b><li>発行する伝票が一つも選択されていません。</b></font>";
        exit;
    }

    //伝票発行フラグ更新処理
    Db_Query($db_con,"BEGIN");

    $sql  = "UPDATE t_sale_h ";
    $sql .= " SET";
    $sql .= "   slip_flg = 't',";
    $sql .= "   slip_out_day = NOW()";
    $sql .= " WHERE";
    $sql .= "   sale_id IN ($imp_sale_id)";
    $sql .= "   AND ";
    $sql .= "   slip_flg = 'f'";
    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    if($result === false){
        Db_Query($db_con, "ROLLBACK");
        exit;
    }

    Db_Query($db_con, "COMMIT");
}

for($s=0;$s<count($sale_id);$s++){
    $pdf->AddPage();
    /****************************/
    //売上ヘッダー識別SQL
    /****************************/
    $sql  = "SELECT ";
    $sql .= "    t_sale_h.renew_flg, ";
    $sql .= "    t_sale_h.direct_id, ";
    $sql .= "    CASE t_sale_h.trade_id ";
    $sql .= "       WHEN '11' THEN net_amount ";
    $sql .= "       WHEN '15' THEN net_amount ";
    $sql .= "       WHEN '61' THEN net_amount ";
    $sql .= "       ELSE net_amount * -1 ";
    $sql .= "    END AS net_amount, ";
    $sql .= "    CASE t_sale_h.trade_id ";
    $sql .= "       WHEN '11' THEN tax_amount ";
    $sql .= "       WHEN '15' THEN tax_amount ";
    $sql .= "       WHEN '61' THEN tax_amount ";
    $sql .= "       ELSE tax_amount * -1 ";
    //aoyama-n 2009-09-16
    #$sql .= "    END AS tax_amount ";
    $sql .= "    END AS tax_amount, ";
    $sql .= "    t_sale_h.trade_id ";
    $sql .= "FROM ";
    $sql .= "    t_sale_h ";
    $sql .= "WHERE ";
    $sql .= "    t_sale_h.sale_id = ".$sale_id[$s].";";
    $result = Db_Query($db_con,$sql);
    Get_Id_Check($result);
    $stat = Get_Data($result);
    $renew_flg   = $stat[0][0];            //日次更新フラグ
    $direct_id   = $stat[0][1];            //直送先ID

    $sale_amount = $stat[0][2];            //売上金額合計（税抜）
    $tax_amount  = $stat[0][3];            //消費税

    //aoyama-n 2009-09-16
    $total_amount = $sale_amount+$tax_amount; //売上金額合計（税込）

    //aoyama-n 2009-09-16
    $trade       = $stat[0][4];            //取引区分

    /****************************/
    //ヘッダ情報取得SQL
    /****************************/
    //日次更新後か
    if($renew_flg == 't'){
        //日次更新後
        $sql  = "SELECT ";
        $sql .= "    t_sale_h.client_cd1,";
        $sql .= "    t_sale_h.client_cd2,";
        $sql .= "    t_sale_h.c_post_no1,";
        $sql .= "    t_sale_h.c_post_no2,";
        $sql .= "    t_sale_h.c_address1,";
        $sql .= "    t_sale_h.c_address2,";
        $sql .= "    t_sale_h.c_address3,";
        $sql .= "    t_sale_h.c_shop_name,";
        $sql .= "    t_sale_h.c_shop_name2,";
        $sql .= "    t_sale_h.sale_day,";
        $sql .= "    t_sale_h.sale_no,";
        $sql .= "    t_sale_h.c_staff_name, ";
        $sql .= "    t_client.tax_div, ";
		$sql .= "    t_client.compellation, ";
        $sql .= "    t_sale_h.note ";      
        $sql .= "FROM ";
        $sql .= "    t_sale_h ";
        $sql .= "    INNER JOIN t_client ON t_client.client_id = t_sale_h.client_id ";
        $sql .= "WHERE ";
        $sql .= "    t_sale_h.sale_id = ".$sale_id[$s].";";
    }else{
        //日次更新前
        $sql  = "SELECT ";
        $sql .= "    t_sale_h.client_cd1,";
        $sql .= "    t_sale_h.client_cd2,";
        $sql .= "    t_sale_h.c_post_no1,";
        $sql .= "    t_sale_h.c_post_no2,";
        $sql .= "    t_sale_h.c_address1,";
        $sql .= "    t_sale_h.c_address2,";
        $sql .= "    t_sale_h.c_address3,";
        $sql .= "    t_sale_h.c_shop_name,";
        $sql .= "    t_sale_h.c_shop_name2,";
        $sql .= "    t_sale_h.sale_day,";
        $sql .= "    t_sale_h.sale_no,";
        $sql .= "    t_sale_h.c_staff_name, ";
        $sql .= "    t_client.tax_div, ";
		$sql .= "    t_client.compellation, ";      
        $sql .= "    t_sale_h.note ";      
        $sql .= "FROM ";
        $sql .= "    t_sale_h ";
        $sql .= "    INNER JOIN t_client ON t_client.client_id = t_sale_h.client_id ";
        $sql .= "    INNER JOIN t_staff ON t_staff.staff_id = t_sale_h.c_staff_id ";
        $sql .= "WHERE ";
        $sql .= "    t_sale_h.sale_id = ".$sale_id[$s].";";
    }
    $result = Db_Query($db_con,$sql);
    $data_list = Get_Data($result,2);
    
    //直送先がある場合
    if($direct_id != NULL){
        $sql  = "SELECT ";
        $sql .= "    t_sale_h.direct_cd,";
        $sql .= "    t_sale_h.d_post_no1,";
        $sql .= "    t_sale_h.d_post_no2,";
        $sql .= "    t_sale_h.d_address1,";
        $sql .= "    t_sale_h.d_address2,";
        $sql .= "    t_sale_h.d_address3,";
        $sql .= "    t_sale_h.direct_name,";
        $sql .= "    t_sale_h.direct_name2,";
        $sql .= "    t_sale_h.d_tel,";
        $sql .= "    t_sale_h.d_fax,";
        $sql .= "    t_aorder_h.hope_day,";
        $sql .= "    t_sale_h.green_flg,";
        $sql .= "    t_aorder_h.note_your ";
        $sql .= "FROM ";
        $sql .= "    t_sale_h ";
        $sql .= "    LEFT JOIN t_direct ON t_direct.direct_id = t_sale_h.direct_id ";
        $sql .= "    LEFT JOIN t_aorder_h ON t_sale_h.aord_id = t_aorder_h.aord_id ";
        $sql .= "WHERE ";
        $sql .= "    t_sale_h.sale_id = ".$sale_id[$s].";";
    }

    $result = Db_Query($db_con,$sql);
    $direct_data = Get_Data($result,2);


    $code = $data_list[0][0]."-".$data_list[0][1]; //お客様コードNo
    $post = $data_list[0][2]."-".$data_list[0][3]; //郵便番号
    $address1 = $data_list[0][4];                  //住所１行目
    $address2 = $data_list[0][5];                  //住所２行目
    $address3 = $data_list[0][6];                  //住所３行目
    $company  = $data_list[0][7];                  //会社名1
    $company2 = $data_list[0][8];                  //会社名2
    $memo     = Textarea_Non_Break($data_list[0][14],3);//メモ

	//敬称判定
    if($data_list[0][13] == 1){
        //御中
        $compell      = "御中";
    }else{
        //様
        $compell      = "様";
    }

    //敬称結合判定
    if($company2 != NULL){
        $company2 .= "　".$compell;
    }else{
        $company .= "　".$compell;
    }

    //変数初期化
    unset($direct);
    unset($client_data);
    //直送先が指定されていた場合
    if($direct_id != null){


        $client_data["direct"] = $direct_data[0][6]."　様分";     //直送先名
        $client_data["name"] = $data_list[0][7]."　様";         //得意先名

        if($direct_data[0][11] == 't'){
            $client_data[4]  = "希望納期：".$direct_data[0][10]."　グリーン指定：有";
        }else{
            $client_data[4]  = "希望納期：".$direct_data[0][10]."　グリーン指定：無";
        }

        $direct[0]      = "直送先コードNo.";
        $direct[1]      = $direct_data[0][0];
        $direct[2]      = $direct_data[0][1]."-".$direct_data[0][2];
        $direct[3]      = $direct_data[0][3];
        $direct[4]      = $direct_data[0][4];
        $direct[5]      = $direct_data[0][5];

		//直送先２指定判定
		if($direct_data[0][7] != NULL){
			$direct[6]  = $direct_data[0][6];
			$direct[7]  = $direct_data[0][7]."　御中";
        }else{
			$direct[6]  = $direct_data[0][6]."　御中";
        }

        $direct[8]      = "TEL：".$direct_data[0][8]."　FAX:".$direct_data[0][9];

    }else{


        $direct[0]      = "お客先コードNo.";
        $direct[1]      = $data_list[0][0]."-".$data_list[0][1];
        $direct[2]      = $data_list[0][2]."-".$data_list[0][3];
        $direct[3]      = $data_list[0][4];
        $direct[4]      = $data_list[0][5];
        $direct[5]      = $data_list[0][6];
        $direct[6]      = $company;
        $direct[7]      = $company2;
    }
    
    //日付生成
    $date = explode('-',$data_list[0][9]);
    $date_y = $date[0];                            //年
    $date_m = $date[1];                            //月
    $date_d = $date[2];                            //日
    $slip   = $data_list[0][10];                   //伝票番号
    $charge = $data_list[0][11];                   //担当者
    $tax_div = $data_list[0][12];                  //課税単位

    /****************************/
    //納品書コメント設定
    /****************************/
    $sql  = "SELECT ";
    $sql .= "    d_memo1, ";        //納品書コメント1
    $sql .= "    d_memo2, ";        //納品書コメント2
    $sql .= "    d_memo3 ";         //納品書コメント3
    $sql .= "FROM ";
    $sql .= "    t_h_ledger_sheet;";
    $result = Db_Query($db_con,$sql);
    $d_memo = Get_Data($result,2);

    //売上伝票でメモが設定されていない場合はプレビューを優先する
    if($memo == null){
        $memo = Textarea_Non_Break($d_memo[0][2],3);
    }
    /****************************/
    //データ情報取得SQL
    /****************************/
    $data_sql  = "SELECT ";
    //日次更新フラグがtrueか
    if($renew_flg == 't'){
        $data_sql .= "    t_sale_d.goods_cd,";
    }else{
        $data_sql .= "    t_sale_d.goods_cd,";
    }
    $data_sql .= "    t_sale_d.official_goods_name,";
    $data_sql .= "    t_sale_d.num,";
    $data_sql .= "    t_sale_d.unit,";
    $data_sql .= "    CASE t_sale_h.trade_id \n";
    $data_sql .= "      WHEN '11' THEN t_sale_d.sale_price ";
    $data_sql .= "      WHEN '15' THEN t_sale_d.sale_price ";
    $data_sql .= "      WHEN '61' THEN t_sale_d.sale_price ";
    $data_sql .= "      ELSE  t_sale_d.sale_price * -1";
    $data_sql .= "    END AS sale_price, ";
    $data_sql .= "    CASE t_sale_h.trade_id \n";
    $data_sql .= "      WHEN '11' THEN t_sale_d.sale_amount ";
    $data_sql .= "      WHEN '15' THEN t_sale_d.sale_amount ";
    $data_sql .= "      WHEN '61' THEN t_sale_d.sale_amount ";
    $data_sql .= "      ELSE  t_sale_d.sale_amount * -1";
    //aoyama-n 2009-09-16
    #$data_sql .= "    END AS sale_amount ";
    $data_sql .= "    END AS sale_amount, ";
    $data_sql .= "    t_goods.discount_flg ";
    $data_sql .= "FROM ";
    $data_sql .= "    t_sale_d ";
    $data_sql .= "    INNER JOIN t_sale_h ON t_sale_d.sale_id = t_sale_h.sale_id ";
    $data_sql .= "    INNER JOIN t_goods ON t_sale_d.goods_id = t_goods.goods_id ";
    $data_sql .= "WHERE ";
    $data_sql .= "    t_sale_d.sale_id = ".$sale_id[$s];
    $data_sql .= " AND ";
    $data_sql .= "    t_sale_h.shop_id = $shop_id ";
    $data_sql .= "ORDER BY ";
    //日次更新フラグがtrueか
    if($renew_flg == 't'){
        $data_sql .= "    t_sale_d.line;";
    }else{
        $data_sql .= "    t_sale_d.line;";
    }

    $result = Db_Query($db_con,$data_sql);
    $sale_data = Get_Data($result,2);

    //改ページ件数計算
    $sale_count = count($sale_data);
    $page_count = $sale_count / 5;
    $page_count = floor($page_count);
    $page_count2 = $sale_count % 5;
    if($page_count2 != 0){
        $page_count++;
    }

    //各ヘッダの商品データ分表示
    for($page=1;$page<=$page_count;$page++){
        //枝番を０埋め
        $branch_no = str_pad($page, 2, "0", STR_PAD_LEFT);
        $branch_no = "-".$branch_no;       //伝票番号

        //一ページ目は、既にヘッダ部分で作成しているから、ページを追加しない
        if($page != 1){
            $pdf->AddPage();
        }
        
        /****************************/
        //納品書描画処理
        /****************************/
        $left_margin = 45;
        $posY = 11;

        //線の太さ
        $pdf->SetLineWidth(0.2);
        //線の色
		$pdf->SetDrawColor(153,102,0);
        //フォント設定
        $pdf->SetFont(GOTHIC,'', 9);
        //テキストの色
		$pdf->SetTextColor(153,102,0); 
        //背景色
		$pdf->SetFillColor(240,230,140);

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+10, $posY+10);
        $pdf->Cell(60, 12,'お客様コードNo.', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+70, $posY+10);
        $pdf->Cell(100, 12,$code, '0', '1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        //郵便番号
        $pdf->SetFont(GOTHIC,'', 9.5);
        $pdf->SetXY($left_margin+10, $posY+25);
        $pdf->Cell(15, 12,'〒', '0', '1', 'L','0');

        $pdf->SetXY($left_margin+25, $posY+25);
        $pdf->Cell(50, 12,$post, '0', '1', 'L','0');

        //住所・社名
        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+15, $posY+38);
        $pdf->Cell(50, 12,$address1, '0', '1', 'L','0');
        $pdf->SetXY($left_margin+15, $posY+50);
        $pdf->Cell(50, 12,$address2, '0', '1', 'L','0');
        $pdf->SetXY($left_margin+15, $posY+62);
        $pdf->Cell(50, 12,$address3, '0', '1', 'L','0');
        $pdf->SetFont(GOTHIC,'', 11);
        $pdf->SetXY($left_margin+20, $posY+77);
        $pdf->Cell(50, 12,$company, '0', '1', 'L','0');
        $pdf->SetXY($left_margin+20, $posY+92);
        $pdf->Cell(50, 12,$company2, '0', '1', 'L','0');
        if($direct_id != NULL){
            $pdf->SetFont(GOTHIC,'', 8);
            $pdf->SetXY($left_margin+15, $posY+107);
            $pdf->Cell(50, 12,$client_data["direct"], '0', '1', 'L','0');
        }

		//テキストの色
		$pdf->SetTextColor(153,102,0); 

        $pdf->SetFont(GOTHIC,'', 10);
        $pdf->SetXY($left_margin+214, $posY+5);
        $pdf->Cell(160, 15,'納　　品　　書 　(控)', '1', '1', 'C','1');

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+392, $posY+7);
        $pdf->Cell(20, 12,$date_y, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+412, $posY+7);
        $pdf->Cell(12, 12,'年', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+424, $posY+7);
        $pdf->Cell(12, 12,$date_m, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+436, $posY+7);
        $pdf->Cell(12, 12,'月', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+448, $posY+7);
        $pdf->Cell(12, 12,$date_d, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+460, $posY+7);
        $pdf->Cell(12, 12,'日', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+487, $posY+7);
        $pdf->Cell(33, 12,'伝票No.', '0', '1', 'R','0');
        $pdf->SetXY($left_margin+520, $posY+7);
        $pdf->Cell(50, 12,$slip.$branch_no, '0', '1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        $pdf->SetFont(GOTHIC,'B', 10.5);
        $pdf->SetXY($left_margin+400, $posY+34);
        $pdf->Cell(45, 12,'株式会社', '0', '1', 'C','0');
        $pdf->SetFont(GOTHIC,'B', 15);
        $pdf->SetXY($left_margin+453, $posY+32);
        $pdf->Cell(115,14,'ア メ ニ テ ィ', '0','1', 'C','0');
        $pdf->SetFont(MINCHO,'B', 7);
        $pdf->SetXY($left_margin+422, $posY+51);
        $pdf->Cell(37,12,'代表取締役', '0','1', 'R','0');
        $pdf->SetFont(MINCHO,'B', 9);
        $pdf->SetXY($left_margin+459, $posY+50);
        $pdf->Cell(110,14,'山 　戸 　里 　志', '0','1', 'C','0');

        $pdf->SetFont(GOTHIC,'', 6);
        $pdf->SetXY($left_margin+415, $posY+68);
        $pdf->Cell(174,8,$d_memo[0][0], '0','1', 'L','0');

        $pdf->SetXY($left_margin+415, $posY+77);
        $pdf->Cell(174,8,$d_memo[0][1], '0','1', 'L','0');

        //テキストの色
		$pdf->SetTextColor(153,102,0); 

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+332, $posY+90);
        $pdf->Cell(40, 12,'担当者 : ', '0', '1', 'C','0');
	
		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        $pdf->SetFont(GOTHIC,'', 10);
        $pdf->SetXY($left_margin+367, $posY+90);
        $pdf->Cell(188, 12,$charge, 'B', '1', 'L','0');

		//テキストの色
		$pdf->SetTextColor(153,102,0); 

        $pdf->SetFont(GOTHIC,'', 7);
        $pdf->SetXY($left_margin+330, $posY+109);
        $pdf->Cell(250, 10,'毎度ありがとうございます。下記の通り納品致しますので御査収下さい。', '0', '1', 'R','0');

        //線の太さ
        $pdf->SetLineWidth(0.8);
        $pdf->RoundedRect($left_margin+10, $posY+120, 575, 115, 5, 'FD',1234);

        //線の太さ
        $pdf->SetLineWidth(0.2);
        $pdf->RoundedRect($left_margin+10, $posY+120, 575, 10, 5, 'FD',12);
        $pdf->SetXY($left_margin+10, $posY+120);
        $pdf->Cell(219, 10,'コ　ー　ド　/　ア イ テ ム 名', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+229, $posY+120);
        $pdf->Cell(74, 10,'数　　 量', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+303, $posY+120);
        $pdf->Cell(30, 10,'単 位', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+333, $posY+120);
        $pdf->Cell(89, 10,'単　　　価', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+422, $posY+120);
        $pdf->Cell(96, 10,'金　　　 額', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+518, $posY+120);
        $pdf->Cell(67, 10,'備　　考', '0', '1', 'C','0');

        //背景色
        $pdf->SetFillColor(255);
        $pdf->RoundedRect($left_margin+10, $posY+130, 575, 105, 5, 'FD',34);

        //線の太さ
        $pdf->SetLineWidth(0.8);
        $pdf->RoundedRect($left_margin+333, $posY+236, 185, 47, 5, 'FD',34);

        $pdf->SetFont(GOTHIC,'', 8);
        //線の太さ
        $pdf->SetLineWidth(0.2);

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        //商品データ行数描画
        $height = array('130','151','172','193','214');
        $h=0;
        for($x=0+(($page-1)*5);$x<($page*5);$x++){

            //aoyama-n 2009-09-16
            //値引商品及び取引区分が値引・返品の場合は赤字で表示
            if($sale_data[$x][6] === 't' || $trade == '13' || $trade == '14' || $trade == '63' || $trade == '64'){
                $pdf->SetTextColor(255,0,16);
            }else{
                $pdf->SetTextColor(0,0,0);
            }

            if($x==($page*5)-1){
                $pdf->SetXY($left_margin+10, $posY+$height[$h]);
                //商品コードがNULLじゃないときだけ表示
                if($sale_data[$x][0] != NULL && $sale_data[$x][1] != NULL){
                    $pdf->Cell(219, 21,$sale_data[$x][0]."/".$sale_data[$x][1], '0', '1', 'L','0');  //商品コード・名前
                }else{
                    $pdf->Cell(219, 21,'', '0', '1', 'C','0');  //空表示
                }
                $pdf->SetXY($left_margin+229, $posY+$height[$h]);
                //数量がNULLじゃないときだけ形式変更
                if($sale_data[$x][2] != NULL){
                    $pdf->Cell(74, 21,number_format($sale_data[$x][2]), '1', '1', 'R','0');          //数量
                }else{
                    $pdf->Cell(74, 21,($sale_data[$x][2]), '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+303, $posY+$height[$h]);
                $pdf->Cell(30, 21,$sale_data[$x][3], '1', '1', 'C','0');                             //単位
                $pdf->SetXY($left_margin+333, $posY+$height[$h]);
                //単価がNULLじゃないときだけ表示
                if($sale_data[$x][4] != NULL){
                    $pdf->Cell(89, 21,number_format($sale_data[$x][4],2), 'R', '1', 'R','0');        //単価
                }else{
                    $pdf->Cell(89, 21,'', 'R', '1', 'R','0');                              
                }
                $pdf->SetXY($left_margin+422, $posY+$height[$h]);
                //金額がNULLじゃないときだけ形式変更
                if($sale_data[$x][5] != NULL){
                    $pdf->Cell(96, 21,number_format($sale_data[$x][5]), 'R', '1', 'R','0');          //金額
                }else{
                    $pdf->Cell(96, 21,$sale_data[$x][5], 'R', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+518, $posY+$height[$h]);
                $pdf->Cell(67, 21,'', '0', '1', 'L','0');                                            //備考
            }else{
                $pdf->SetXY($left_margin+10, $posY+$height[$h]);
                if($sale_data[$x][0] != NULL && $sale_data[$x][1] != NULL){
                    $pdf->Cell(219, 21,$sale_data[$x][0]."/".$sale_data[$x][1], '1', '1', 'L','0');  
                }else{
                    $pdf->Cell(219, 21,'', '1', '1', 'L','0');  //空表示
                }
                $pdf->SetXY($left_margin+229, $posY+$height[$h]);
                if($sale_data[$x][2] != NULL){
                    $pdf->Cell(74, 21,number_format($sale_data[$x][2]), '1', '1', 'R','0');
                }else{
                    $pdf->Cell(74, 21,$sale_data[$x][2], '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+303, $posY+$height[$h]);
                $pdf->Cell(30, 21,$sale_data[$x][3], '1', '1', 'C','0');
                $pdf->SetXY($left_margin+333, $posY+$height[$h]);
                //単価がNULLじゃないときだけ表示
                if($sale_data[$x][4] != NULL){
                    $pdf->Cell(89, 21,number_format($sale_data[$x][4],2), '1', '1', 'R','0');
                }else{
                    $pdf->Cell(89, 21,'', '1', '1', 'C','0');                              
                }
                $pdf->SetXY($left_margin+422, $posY+$height[$h]);
                if($sale_data[$x][5] != NULL){
                    $pdf->Cell(96, 21,number_format($sale_data[$x][5]), '1', '1', 'R','0');
                }else{
                    $pdf->Cell(96, 21,$sale_data[$x][5], '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+518, $posY+$height[$h]);
                $pdf->Cell(67, 21,'', '1', '1', 'L','0');
            }
            $h++;
        }

        //背景色
		$pdf->SetFillColor(240,230,140);
        $pdf->RoundedRect($left_margin+333, $posY+235, 89, 48, 5, 'FD',4);
        //背景色
        $pdf->SetFillColor(255);
        $pdf->RoundedRect($left_margin+422, $posY+235, 96, 48, 5, 'FD',3);

		//テキストの色
		$pdf->SetTextColor(153,102,0); 

        //税抜金額
        $pdf->SetFont(GOTHIC,'', 9);
        $pdf->SetXY($left_margin+333, $posY+235);
        $pdf->Cell(89, 16,'小　　計', 'RB', '1', 'C','0');

		//テキストの色
        //aoyama-n 2009-09-16
        #$pdf->SetTextColor(0,0,0); 
        if($trade == '13' || $trade == '14' || $trade == '63' || $trade == '64' || $total_amount < 0){
            $pdf->SetTextColor(255,0,16);
        }else{
            $pdf->SetTextColor(0,0,0); 
        }

        $pdf->SetXY($left_margin+422, $posY+235);
        //伝票が複数枚ある場合、最後のページに合計金額表示
        if($page==$page_count){
            $pdf->Cell(96, 16,number_format($sale_amount), 'B', '1', 'R','0');
        }else{
            $pdf->Cell(96, 16,'', 'B', '1', 'R','0');
        }

        //課税単位判定
        //伝票単位

		//テキストの色
		$pdf->SetTextColor(153,102,0); 

        //消費税
        $pdf->SetFont(GOTHIC,'', 9);
        $pdf->SetXY($left_margin+333, $posY+251);
        $pdf->Cell(89, 16,'消　費　税', 'RB', '1', 'C','0');

		//テキストの色
        //aoyama-n 2009-09-16
        #$pdf->SetTextColor(0,0,0); 
        if($trade == '13' || $trade == '14' || $trade == '63' || $trade == '64' || $total_amount < 0){
            $pdf->SetTextColor(255,0,16);
        }else{
            $pdf->SetTextColor(0,0,0); 
        }

        $pdf->SetXY($left_margin+422, $posY+251);
        if($page==$page_count){
            $pdf->Cell(96, 16,number_format($tax_amount), 'B', '1', 'R','0');
        }else{
            $pdf->Cell(96, 16,"", 'B', '1', 'R','0');
        }

		//テキストの色
		$pdf->SetTextColor(153,102,0); 

        //税込金額
        //aoyama-n 2009-09-16
        #$total_amount = $sale_amount+$tax_amount;
        $pdf->SetFont(GOTHIC,'', 9);
        $pdf->SetXY($left_margin+333, $posY+267);
        $pdf->Cell(89, 16,'合　　計', 'R', '1', 'C','0');

		//テキストの色
        //aoyama-n 2009-09-16
        #$pdf->SetTextColor(0,0,0); 
        if($trade == '13' || $trade == '14' || $trade == '63' || $trade == '64' || $total_amount < 0){
            $pdf->SetTextColor(255,0,16);
        }else{
            $pdf->SetTextColor(0,0,0); 
        }

        $pdf->SetXY($left_margin+422, $posY+267);
        if($page==$page_count){
            $pdf->Cell(96, 16,number_format($total_amount), '0', '1', 'R','0');
        }else{
            $pdf->Cell(96, 16,"", '0', '1', 'R','0');
        }

		//テキストの色
		$pdf->SetTextColor(153,102,0); 


        //摘要
		$pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+10, $posY+240);
        $pdf->Cell(10, 12,'メ モ ', '0', '1', 'L','0');

		$pdf->SetTextColor(0,0,0); 
        $pdf->RoundedRect($left_margin+10, $posY+250, 300, 33, 5, 'FD',1234);
        $pdf->SetXY($left_margin+10, $posY+253);
        $pdf->MultiCell(300, 10,$memo, '0', '1', '','0');


		$pdf->SetTextColor(153,102,0); 
        $pdf->SetXY($left_margin+525, $posY+238);
        $pdf->Cell(33, 28,'', '1', '1', 'C','0');
        $pdf->SetXY($left_margin+558, $posY+238);
        $pdf->Cell(33, 28,'', '1', '1', 'C','0');

        $pdf->Image(IMAGE_DIR.'company-rogo.PNG',$left_margin+540, $posY+270,56,18);

        /**********************************/
        //納品書描画
        /**********************************/
        $posY = 315;

        //線の太さ
        $pdf->SetLineWidth(0.2);
        //線の色
		$pdf->SetDrawColor(129,53,255);
        //フォント設定
        $pdf->SetFont(GOTHIC,'', 9);
        //テキストの色
		$pdf->SetTextColor(129,53,255); 
        //背景色
		$pdf->SetFillColor(238,227,255);

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+10, $posY+10);
        $pdf->Cell(60, 12,'お客様コードNo.', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+70, $posY+10);
        $pdf->Cell(100, 12,$code, '0', '1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 
        //郵便番号
        $pdf->SetFont(GOTHIC,'', 9.5);
        $pdf->SetXY($left_margin+10, $posY+25);
        $pdf->Cell(15, 12,'〒', '0', '1', 'L','0');
        $pdf->SetXY($left_margin+25, $posY+25);
        $pdf->Cell(50, 12,$post, '0', '1', 'L','0');
    
        //住所・社名
        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+15, $posY+38);
        $pdf->Cell(50, 12,$address1, '0', '1', 'L','0');
        $pdf->SetXY($left_margin+15, $posY+50);
        $pdf->Cell(50, 12,$address2, '0', '1', 'L','0');
        $pdf->SetXY($left_margin+15, $posY+62);
        $pdf->Cell(50, 12,$address3, '0', '1', 'L','0');
        $pdf->SetFont(GOTHIC,'', 11);
        $pdf->SetXY($left_margin+20, $posY+77);
        $pdf->Cell(50, 12,$company, '0', '1', 'L','0');
        $pdf->SetXY($left_margin+20, $posY+92);
        $pdf->Cell(50, 12,$company2, '0', '1', 'L','0');
        if($direct_id != NULL){
            $pdf->SetFont(GOTHIC,'', 8);
            $pdf->SetXY($left_margin+15, $posY+107);
            $pdf->Cell(50, 12,$client_data["direct"], '0', '1', 'L','0');
        }

		
        //テキストの色
        $pdf->SetTextColor(129,53,255); 

        $pdf->SetFont(GOTHIC,'', 10);
        $pdf->SetXY($left_margin+214, $posY+5);
        $pdf->Cell(160, 15,'納　 　品　 　書', '1', '1', 'C','1');

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+392, $posY+7);
        $pdf->Cell(20, 12,$date_y, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+412, $posY+7);
        $pdf->Cell(12, 12,'年', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+424, $posY+7);
        $pdf->Cell(12, 12,$date_m, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+436, $posY+7);
        $pdf->Cell(12, 12,'月', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+448, $posY+7);
        $pdf->Cell(12, 12,$date_d, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+460, $posY+7);
        $pdf->Cell(12, 12,'日', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+487, $posY+7);
        $pdf->Cell(33, 12,'伝票No.', '0', '1', 'R','0');
        $pdf->SetXY($left_margin+520, $posY+7);
        $pdf->Cell(50, 12,$slip.$branch_no, '0', '1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        if($photo_exists){
            $pdf->Image($photo,$left_margin+480, $posY+35,52);
        }

        $pdf->SetFont(GOTHIC,'B', 10.5);
        $pdf->SetXY($left_margin+400, $posY+34);
        $pdf->Cell(45, 12,'株式会社', '0', '1', 'C','0');
        $pdf->SetFont(GOTHIC,'B', 15);
        $pdf->SetXY($left_margin+453, $posY+32);
        $pdf->Cell(115,14,'ア メ ニ テ ィ', '0','1', 'C','0');
        $pdf->SetFont(MINCHO,'B', 7);
        $pdf->SetXY($left_margin+422, $posY+51);
        $pdf->Cell(37,12,'代表取締役', '0','1', 'R','0');
        $pdf->SetFont(MINCHO,'B', 9);
        $pdf->SetXY($left_margin+459, $posY+50);
        $pdf->Cell(110,14,'山 　戸 　里 　志', '0','1', 'C','0');

        $pdf->SetFont(GOTHIC,'', 6);
        $pdf->SetXY($left_margin+415, $posY+68);
        $pdf->Cell(174,8,$d_memo[0][0], '0','1', 'L','0');

        $pdf->SetXY($left_margin+415, $posY+77);
        $pdf->Cell(174,8,$d_memo[0][1], '0','1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(129,53,255); 

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+332, $posY+90);
        $pdf->Cell(40, 12,'担当者 : ', '0', '1', 'C','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        $pdf->SetFont(GOTHIC,'', 10);
        $pdf->SetXY($left_margin+367, $posY+90);
        $pdf->Cell(188, 12,$charge, 'B', '1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(129,53,255); 

        $pdf->SetFont(GOTHIC,'', 7);
        $pdf->SetXY($left_margin+330, $posY+109);
        $pdf->Cell(250, 10,'毎度ありがとうございます。下記の通り納品致しますので御査収下さい。', '0', '1', 'R','0');

        //線の太さ
        $pdf->SetLineWidth(0.8);
        $pdf->RoundedRect($left_margin+10, $posY+120, 575, 115, 5, 'FD',1234);

        //線の太さ
        $pdf->SetLineWidth(0.2);
        $pdf->RoundedRect($left_margin+10, $posY+120, 575, 10, 5, 'FD',12);
        $pdf->SetXY($left_margin+10, $posY+120);
        $pdf->Cell(219, 10,'コ　ー　ド　/　ア イ テ ム 名', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+229, $posY+120);
        $pdf->Cell(74, 10,'数　　 量', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+303, $posY+120);
        $pdf->Cell(30, 10,'単 位', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+333, $posY+120);
        $pdf->Cell(89, 10,'単　　　価', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+422, $posY+120);
        $pdf->Cell(96, 10,'金　　　 額', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+518, $posY+120);
        $pdf->Cell(67, 10,'備　　考', '0', '1', 'C','0');

        //背景色
        $pdf->SetFillColor(255);
        $pdf->RoundedRect($left_margin+10, $posY+130, 575, 105, 5, 'FD',34);

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        //線の太さ
        $pdf->SetLineWidth(0.8);
        $pdf->RoundedRect($left_margin+333, $posY+235, 185, 47, 5, 'FD',34);
        $pdf->SetFont(GOTHIC,'', 8);
        //線の太さ
        $pdf->SetLineWidth(0.2);
        //商品データ行数描画
        $height = array('130','151','172','193','214');
        $h=0;
        for($x=0+(($page-1)*5);$x<($page*5);$x++){

            //aoyama-n 2009-09-16
            //値引商品及び取引区分が値引・返品の場合は赤字で表示
            if($sale_data[$x][6] === 't' || $trade == '13' || $trade == '14' || $trade == '63' || $trade == '64'){
                $pdf->SetTextColor(255,0,16);
            }else{
                $pdf->SetTextColor(0,0,0);
            }

            if($x==($page*5)-1){
                $pdf->SetXY($left_margin+10, $posY+$height[$h]);
                //商品コードがNULLじゃないときだけ表示
                if($sale_data[$x][0] != NULL && $sale_data[$x][1] != NULL){
                    $pdf->Cell(219, 21,$sale_data[$x][0]."/".$sale_data[$x][1], '0', '1', 'L','0');  //商品コード・名前
                }else{
                    $pdf->Cell(219, 21,'', '0', '1', 'C','0');  //空表示
                }
                $pdf->SetXY($left_margin+229, $posY+$height[$h]);
                //数量がNULLじゃないときだけ形式変更
                if($sale_data[$x][2] != NULL){
                    $pdf->Cell(74, 21,number_format($sale_data[$x][2]), '1', '1', 'R','0');          //数量
                }else{
                    $pdf->Cell(74, 21,($sale_data[$x][2]), '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+303, $posY+$height[$h]);
                $pdf->Cell(30, 21,$sale_data[$x][3], '1', '1', 'C','0');                             //単位
                $pdf->SetXY($left_margin+333, $posY+$height[$h]);
                //単価がNULLじゃないときだけ表示
                if($sale_data[$x][4] != NULL){
                    $pdf->Cell(89, 21,number_format($sale_data[$x][4],2), 'R', '1', 'R','0');        //単価
                }else{
                    $pdf->Cell(89, 21,'', 'R', '1', 'C','0');                              
                }
                $pdf->SetXY($left_margin+422, $posY+$height[$h]);
                //金額がNULLじゃないときだけ形式変更
                if($sale_data[$x][5] != NULL){
                    $pdf->Cell(96, 21,number_format($sale_data[$x][5]), 'R', '1', 'R','0');          //金額
                }else{
                    $pdf->Cell(96, 21,$sale_data[$x][5], 'R', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+518, $posY+$height[$h]);
                $pdf->Cell(67, 21,'', '0', '1', 'L','0');                                            //備考
            }else{
                $pdf->SetXY($left_margin+10, $posY+$height[$h]);
                if($sale_data[$x][0] != NULL && $sale_data[$x][1] != NULL){
                    $pdf->Cell(219, 21,$sale_data[$x][0]."/".$sale_data[$x][1], '1', '1', 'L','0');  
                }else{
                    $pdf->Cell(219, 21,'', '1', '1', 'C','0');  //空表示
                }
                $pdf->SetXY($left_margin+229, $posY+$height[$h]);
                if($sale_data[$x][2] != NULL){
                    $pdf->Cell(74, 21,number_format($sale_data[$x][2]), '1', '1', 'R','0');
                }else{
                    $pdf->Cell(74, 21,$sale_data[$x][2], '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+303, $posY+$height[$h]);
                $pdf->Cell(30, 21,$sale_data[$x][3], '1', '1', 'C','0');
                $pdf->SetXY($left_margin+333, $posY+$height[$h]);
                //単価がNULLじゃないときだけ表示
                if($sale_data[$x][4] != NULL){
                    $pdf->Cell(89, 21,number_format($sale_data[$x][4],2), '1', '1', 'R','0');
                }else{
                    $pdf->Cell(89, 21,'', '1', '1', 'C','0');                              
                }
                $pdf->SetXY($left_margin+422, $posY+$height[$h]);
                if($sale_data[$x][5] != NULL){
                    $pdf->Cell(96, 21,number_format($sale_data[$x][5]), '1', '1', 'R','0');
                }else{
                    $pdf->Cell(96, 21,$sale_data[$x][5], '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+518, $posY+$height[$h]);
                $pdf->Cell(67, 21,'', '1', '1', 'L','0');
            }
            $h++;
        }
        //背景色
		$pdf->SetFillColor(238,227,255);
        $pdf->RoundedRect($left_margin+333, $posY+235, 89, 48, 5, 'FD',4);
        //背景色
        $pdf->SetFillColor(255);
        $pdf->RoundedRect($left_margin+422, $posY+235, 96, 48, 5, 'FD',3);

		//テキストの色
        $pdf->SetTextColor(129,53,255); 

        //税抜金額
        $pdf->SetFont(GOTHIC,'', 9);
        $pdf->SetXY($left_margin+333, $posY+235);
        $pdf->Cell(89, 16,'小　　計', 'RB', '1', 'C','0');

		//テキストの色
        //aoyama-n 2009-09-16
        #$pdf->SetTextColor(0,0,0); 
        if($trade == '13' || $trade == '14' || $trade == '63' || $trade == '64' || $total_amount < 0){
            $pdf->SetTextColor(255,0,16);
        }else{
            $pdf->SetTextColor(0,0,0); 
        }

        $pdf->SetXY($left_margin+422, $posY+235);
        //伝票が複数枚ある場合、最後のページに合計金額表示
        if($page==$page_count){
            $pdf->Cell(96, 16,number_format($sale_amount), 'B', '1', 'R','0');
        }else{
            $pdf->Cell(96, 16,'', 'B', '1', 'C','0');
        }
        
		//テキストの色
        $pdf->SetTextColor(129,53,255); 

        //課税単位判定
        if($tax_div == '1'){
            //課税単位が締日の場合は、消費税欄に「別途」と表示し、合計に税抜金額を表示

            //消費税
            $pdf->SetFont(GOTHIC,'', 9);
            $pdf->SetXY($left_margin+333, $posY+251);
            $pdf->Cell(89, 16,'消　費　税', 'RB', '1', 'C','0');
            $pdf->SetXY($left_margin+422, $posY+251);
            if($page==$page_count){
                $pdf->Cell(96, 16,"別途", 'B', '1', 'C','0');
            }else{
                $pdf->Cell(96, 16,"", 'B', '1', 'C','0');
            }

            //税抜金額
            //aoyama-n 2009-09-16
            #$total_amount = $sale_amount;
            $pdf->SetFont(GOTHIC,'', 9);
            $pdf->SetXY($left_margin+333, $posY+267);
            $pdf->Cell(89, 16,'合　　計', 'R', '1', 'C','0');

			//テキストの色
            //aoyama-n 2009-09-16
        	#$pdf->SetTextColor(0,0,0); 
            if($trade == '13' || $trade == '14' || $trade == '63' || $trade == '64' || $total_amount < 0){
                $pdf->SetTextColor(255,0,16);
            }else{
        	    $pdf->SetTextColor(0,0,0); 
            }
         

            $pdf->SetXY($left_margin+422, $posY+267);
            if($page==$page_count){
                //aoyama-n 2009-09-16
                #$pdf->Cell(96, 16,number_format($total_amount), '0', '1', 'R','0');
                $pdf->Cell(96, 16,number_format($sale_amount), '0', '1', 'R','0');
            }else{
                $pdf->Cell(96, 16,"", '0', '1', 'C','0');
            }
        }else{
            //伝票単位

            //消費税
            $pdf->SetFont(GOTHIC,'', 9);
            $pdf->SetXY($left_margin+333, $posY+251);
            $pdf->Cell(89, 16,'消　費　税', 'RB', '1', 'C','0');

			//テキストの色
            //aoyama-n 2009-09-16
        	#$pdf->SetTextColor(0,0,0); 
            if($trade == '13' || $trade == '14' || $trade == '63' || $trade == '64' || $total_amount < 0){
                $pdf->SetTextColor(255,0,16);
            }else{
        	    $pdf->SetTextColor(0,0,0); 
            }

            $pdf->SetXY($left_margin+422, $posY+251);
            if($page==$page_count){
                $pdf->Cell(96, 16,number_format($tax_amount), 'B', '1', 'R','0');
            }else{
                $pdf->Cell(96, 16,"", 'B', '1', 'C','0');
            }

			//テキストの色
        	$pdf->SetTextColor(129,53,255); 

            //税込金額
            //aoyama-n 2009-09-16
            #$total_amount = $sale_amount+$tax_amount;
            $pdf->SetFont(GOTHIC,'', 9);
            $pdf->SetXY($left_margin+333, $posY+267);
            $pdf->Cell(89, 16,'合　　計', 'R', '1', 'C','0');

			//テキストの色
            //aoyama-n 2009-09-16
        	#$pdf->SetTextColor(0,0,0); 
            if($trade == '13' || $trade == '14' || $trade == '63' || $trade == '64' || $total_amount < 0){
                $pdf->SetTextColor(255,0,16);
            }else{
        	    $pdf->SetTextColor(0,0,0); 
            }

            $pdf->SetXY($left_margin+422, $posY+267);
            if($page==$page_count){
                $pdf->Cell(96, 16,number_format($total_amount), '0', '1', 'R','0');
            }else{
                $pdf->Cell(96, 16,"", '0', '1', 'C','0');
            }
        }

		//テキストの色
        $pdf->SetTextColor(129,53,255); 

        //摘要
		$pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+10, $posY+240);
        $pdf->Cell(10, 12,'メ モ ', '0', '1', 'L','0');

        $pdf->SetTextColor(0,0,0); 
        $pdf->RoundedRect($left_margin+10, $posY+250, 300, 33, 5, 'FD',1234);
        $pdf->SetXY($left_margin+10, $posY+253);
        $pdf->MultiCell(300, 10,$memo, '0', '1', '','0');


        $pdf->SetTextColor(129,53,255); 
        $pdf->SetXY($left_margin+525, $posY+238);
        $pdf->Cell(33, 28,'', '1', '1', 'C','0');
        $pdf->SetXY($left_margin+558, $posY+238);
        $pdf->Cell(33, 28,'', '1', '1', 'C','0');

        $pdf->Image(IMAGE_DIR.'company-rogo.PNG',$left_margin+540, $posY+270,56,18);

        /*********************************/
        //出荷案内書
        /*********************************/

        //$posY = 637;
        $posY = 625;

        //線の太さ
        $pdf->SetLineWidth(0.2);
        //線の色
        $pdf->SetDrawColor(238,0,14);
        //フォント設定
        $pdf->SetFont(GOTHIC,'', 9);
        //テキストの色
        $pdf->SetTextColor(255,0,16); 
        //背景色
        $pdf->SetFillColor(255,204,207);

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+10, $posY+10);
        $pdf->Cell(60, 12,$direct[0], '0', '1', 'C','0');
        $pdf->SetXY($left_margin+70, $posY+10);
        $pdf->Cell(100, 12,$direct[1],'0', '1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        //郵便番号
        $pdf->SetFont(GOTHIC,'', 9.5);
        $pdf->SetXY($left_margin+10, $posY+25);
        $pdf->Cell(15, 12,'〒', '0', '1', 'L','0');
        $pdf->SetXY($left_margin+25, $posY+25);
        $pdf->Cell(50, 12,$direct[2], '0', '1', 'L','0');
    
        //住所・社名
        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+15, $posY+38);
        $pdf->Cell(50, 12,$direct[3], '0', '1', 'L','0');
        $pdf->SetXY($left_margin+15, $posY+50);
        $pdf->Cell(50, 12,$direct[4], '0', '1', 'L','0');
        $pdf->SetXY($left_margin+15, $posY+62);
        $pdf->Cell(50, 12,$direct[5], '0', '1', 'L','0');
        //直送先が指定されている場合
        if($direct_id != NULL){
            $pdf->SetFont(GOTHIC,'', 7);
            $pdf->SetXY($left_margin+15, $posY+74);
            $pdf->Cell(50, 10,$direct[8], '0', '1', 'L','0');
            $pdf->SetFont(GOTHIC,'', 11);
            $pdf->SetXY($left_margin+20, $posY+84);
            $pdf->Cell(50, 12,$direct[6], '0', '1', 'L','0');
            $pdf->SetXY($left_margin+20, $posY+96);
            $pdf->Cell(50, 12,$direct[7], '0', '1', 'L','0');
        }else{
            $pdf->SetFont(GOTHIC,'', 11);
            $pdf->SetXY($left_margin+20, $posY+77);
            $pdf->Cell(50, 12,$direct[6], '0', '1', 'L','0');
            $pdf->SetXY($left_margin+20, $posY+92);
            $pdf->Cell(50, 12,$direct[7], '0', '1', 'L','0');
        }

		//テキストの色
        $pdf->SetTextColor(255,0,16); 

        $pdf->SetFont(GOTHIC,'', 10);

        $pdf->SetFont(GOTHIC,'', 10);
        $pdf->SetXY($left_margin+214, $posY+5);
        $pdf->Cell(160, 15,'出　荷　案　内　書', '1', '1', 'C','1');

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+392, $posY+7);
        $pdf->Cell(20, 12,$date_y, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+412, $posY+7);
        $pdf->Cell(12, 12,'年', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+424, $posY+7);
        $pdf->Cell(12, 12,$date_m, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+436, $posY+7);
        $pdf->Cell(12, 12,'月', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+448, $posY+7);
        $pdf->Cell(12, 12,$date_d, '0', '1', 'C','0');
        $pdf->SetXY($left_margin+460, $posY+7);
        $pdf->Cell(12, 12,'日', '0', '1', 'C','0');
        $pdf->SetXY($left_margin+487, $posY+7);
        $pdf->Cell(33, 12,'伝票No.', '0', '1', 'R','0');
        $pdf->SetXY($left_margin+520, $posY+7);
        $pdf->Cell(50, 12,$slip.$branch_no, '0', '1', 'L','0');

        if($photo_exists){
            $pdf->Image($photo,$left_margin+480, $posY+35,52);
        }

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        $pdf->SetFont(GOTHIC,'B', 10.5);
        $pdf->SetXY($left_margin+400, $posY+34);
        $pdf->Cell(45, 12,'株式会社', '0', '1', 'C','0');
        $pdf->SetFont(GOTHIC,'B', 15);
        $pdf->SetXY($left_margin+453, $posY+32);
        $pdf->Cell(115,14,'ア メ ニ テ ィ', '0','1', 'C','0');
        $pdf->SetFont(MINCHO,'B', 7);
        $pdf->SetXY($left_margin+422, $posY+51);
        $pdf->Cell(37,12,'代表取締役', '0','1', 'R','0');
        $pdf->SetFont(MINCHO,'B', 9);
        $pdf->SetXY($left_margin+459, $posY+50);
        $pdf->Cell(110,14,'山 　戸 　里 　志', '0','1', 'C','0');

        $pdf->SetFont(GOTHIC,'', 6);
        $pdf->SetXY($left_margin+415, $posY+68);
        $pdf->Cell(174,8,$d_memo[0][0], '0','1', 'L','0');

        $pdf->SetXY($left_margin+415, $posY+77);
        $pdf->Cell(174,8,$d_memo[0][1], '0','1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(255,0,16); 

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+332, $posY+90);
        $pdf->Cell(40, 12,'担当者 : ', '0', '1', 'C','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        $pdf->SetFont(GOTHIC,'', 10);
        $pdf->SetXY($left_margin+367, $posY+90);
        $pdf->Cell(188, 12,$charge, 'B', '1', 'L','0');

		//テキストの色
        $pdf->SetTextColor(255,0,16); 

        $pdf->SetFont(GOTHIC,'', 7);
        $pdf->SetXY($left_margin+376, $posY+109);
        $pdf->Cell(150, 10,'毎度ありがとうございます。下記の通り出荷致しました。', '0', '1', 'R','0');


        //線の太さ
        $pdf->SetLineWidth(0.8);
        $pdf->RoundedRect($left_margin+10, $posY+120, 575, 115, 5, 'FD',1234);

        //線の太さ
        $pdf->SetLineWidth(0.2);
        $pdf->RoundedRect($left_margin+10, $posY+120, 575, 10, 5, 'FD',12);
        $pdf->SetXY($left_margin+10, $posY+120);
        $pdf->Cell(219, 10,'コ　ー　ド　/　ア イ テ ム 名', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+229, $posY+120);
        $pdf->Cell(74, 10,'数　　 量', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+303, $posY+120);
        $pdf->Cell(30, 10,'単 位', 'R', '1', 'C','0');
        $pdf->SetXY($left_margin+333, $posY+120);
        $pdf->Cell(252, 10,'備　　　考', '0', '1', 'C','0');

		//テキストの色
        $pdf->SetTextColor(0,0,0); 

        //背景色
        $pdf->SetFillColor(255);
        $pdf->RoundedRect($left_margin+10, $posY+130, 575, 105, 5, 'FD',34);

        $pdf->SetFont(GOTHIC,'', 8);
        //線の太さ
        $pdf->SetLineWidth(0.2);
        //商品データ行数描画
        $height = array('130','151','172','193','214');
        $h=0;
        for($x=0+(($page-1)*5);$x<($page*5);$x++){

            //aoyama-n 2009-09-16
            //値引商品及び取引区分が値引・返品の場合は赤字で表示
            if($sale_data[$x][6] === 't' || $trade == '13' || $trade == '14' || $trade == '63' || $trade == '64'){
                $pdf->SetTextColor(255,0,16);
            }else{
                $pdf->SetTextColor(0,0,0);
            }

            if($x==($page*5)-1){
                $pdf->SetXY($left_margin+10, $posY+$height[$h]);
                //商品コードがNULLじゃないときだけ表示
                if($sale_data[$x][0] != NULL && $sale_data[$x][1] != NULL){
                    $pdf->Cell(219, 21,$sale_data[$x][0]."/".$sale_data[$x][1], '0', '1', 'L','0');  //商品コード・名前
                }else{
                    $pdf->Cell(219, 21,'', '0', '1', 'C','0');  //空表示
                }
                $pdf->SetXY($left_margin+229, $posY+$height[$h]);
                if($sale_data[$x][2] != NULL){
                    $pdf->Cell(74, 21,number_format($sale_data[$x][2]), '1', '1', 'R','0');          //数量
                }else{
                    $pdf->Cell(74, 21,$sale_data[$x][2], '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+303, $posY+$height[$h]);
                $pdf->Cell(30, 21,$sale_data[$x][3], '1', '1', 'C','0');                             //単位
                $pdf->SetXY($left_margin+333, $posY+$height[$h]);
                $pdf->Cell(252, 21,$client_data[$h], '0', '1', 'L','0');
            }else{
                $pdf->SetXY($left_margin+10, $posY+$height[$h]);
                if($sale_data[$x][0] != NULL && $sale_data[$x][1] != NULL){
                    $pdf->Cell(219, 21,$sale_data[$x][0]."/".$sale_data[$x][1], '1', '1', 'L','0');  
                }else{
                    $pdf->Cell(219, 21,'', '1', '1', 'C','0');  //空表示
                }
                $pdf->SetXY($left_margin+229, $posY+$height[$h]);
                if($sale_data[$x][2] != NULL){
                    $pdf->Cell(74, 21,number_format($sale_data[$x][2]), '1', '1', 'R','0');
                }else{
                    $pdf->Cell(74, 21,$sale_data[$x][2], '1', '1', 'R','0');
                }
                $pdf->SetXY($left_margin+303, $posY+$height[$h]);
                $pdf->Cell(30, 21,$sale_data[$x][3], '1', '1', 'C','0');
                $pdf->SetXY($left_margin+333, $posY+$height[$h]);
                $pdf->Cell(252, 21,$client_data[$h], '0', '1', 'L','0');
            }
            $h++;
        }

        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+47, $posY+247);

		//テキストの色
        $pdf->SetTextColor(255,0,16); 

        //摘要  
        $pdf->SetFont(GOTHIC,'', 8);
        $pdf->SetXY($left_margin+10, $posY+240);
        $pdf->Cell(10, 12,'メ モ ', '0', '1', 'L','0');

        $pdf->SetTextColor(0,0,0);
        $pdf->RoundedRect($left_margin+10, $posY+250, 300, 33, 5, 'FD',1234);
        $pdf->SetXY($left_margin+10, $posY+253);
        $pdf->MultiCell(300, 10,$memo, '0', '1', '','0');


        $pdf->SetXY($left_margin+525, $posY+238);
        $pdf->Cell(33, 28,'', '1', '1', 'C','0');
        $pdf->SetXY($left_margin+558, $posY+238);
        $pdf->Cell(33, 28,'', '1', '1', 'C','0');
        $pdf->Image(IMAGE_DIR.'company-rogo.PNG',$left_margin+540, $posY+270,56,18);
    }
}

$pdf->Output();

//テキストエリアで改行が指定数以上の場合は3つ目以降は無視する。
function Textarea_Non_Break($val, $cnt){
    //文列中の改行の数を数える
    $break_count = substr_count($val, "\n");

    //指定数より改行数が少ない場合は何もしない
    if($break_count < $cnt){
        return $val;
    }

    //指定数以降の改行は無視
    $split_val = str_split($val);
    $break=0;
    for($i = 0; $i < count($split_val); $i++){
        if($split_val[$i] == "\n"){
            $break++;
        }

        if(($break > 2 && $split_val[$i] == "\n")){
            $split_val[$i] = '';
        }
    }
    return implode('', $split_val);
}

?>
