<?php
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2010/09/04                  aoyama-n    得意先名２を出力するように変更
 *  2010/09/04                  aoyama-n    印字フラグを判定するように変更
 *  2010/09/04                  aoyama-n    敬称を出力するように変更
*/



//ラベルに表示するデータを抽出
function Get_Label_Data ($db_con, $shop_id){

    //複数のショップ（得意先）が選択されている場合
    if(is_array($shop_id)){
        $in_shop_id = implode(',', $shop_id);
        $in_shop_id = str_replace("f", "null", $in_shop_id);
    //一つのショップ（得意先）が選択されている場合
    }else{
        $in_shop_id = $shop_id;
    }


    #2010-09-04 aoyama-n
    /*
    $sql = "
            SELECT
                post_no1 || '-' || post_no2 AS post_no,
                address1,
                address2,
                address3,
                client_name,
                client_name2,
                shop_name,
                tel,
                bill_address_font
            FROM
                t_client 
            WHERE
                client_id IN (".$in_shop_id.")
            ORDER BY
                client_cd1,
                client_cd2 
        ";
    */
    $sql = "
            SELECT
                post_no1 || '-' || post_no2 AS post_no,
                address1,
                address2,
                address3,
                client_name,
                client_name2,
                shop_name,
                tel,
                bill_address_font,
                client_slip1,
                client_slip2,
                CASE compellation
                    WHEN 1 THEN '御中'
                    WHEN 2 THEN '様'
                END AS compellation
            FROM
                t_client 
            WHERE
                client_id IN (".$in_shop_id.")
            ORDER BY
                client_cd1,
                client_cd2 
        ";

    $result = Db_Query($db_con, $sql);
    
    //データ件数
    if(pg_num_rows($result) == 0){
        return false;
    }

    $data = pg_fetch_all($result);

    return $data;
}


function Make_Label_Pdf ($pdf, $label_data){

    /*********************************************/
    $pdf->AddMBFont(GOTHIC ,'SJIS');$pdf->AddMBFont(PGOTHIC,'SJIS');
    $pdf->AddMBFont(MINCHO ,'SJIS');
    $pdf->AddMBFont(PMINCHO,'SJIS');
    $pdf->AddMBFont(KOZMIN ,'SJIS');
    $pdf->SetFont(GOTHIC, 'B', 7);
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage();
    /**********************データ描画***************************/
    $width = array("18","215","413");
    $line_height = array("62","181","300","420","540","659");
    $height = array("14","24","34","44","54","64","74");

    //現在の列番号
    $j = 0;
    //現在の行番号
    $h = 0;
    //現在のラベル数
    $i = 0;

    for($s = 0; $s < count($label_data); $s++){

        #2010-05-07 hashimoto-y
        #2010-05-11
        #列追加は無くなったのでコメントアウト
        #if($_GET["label_font"] == "s"){ 
        #    $pdf->SetFontSize(7);
        #}elseif($_GET["label_font"] == "b"){
        #    $pdf->SetFontSize(9);
        #}

        #2010-05-11 hashimoto-y
        if($label_data[$s]["bill_address_font"] == "t"){ 
            $pdf->SetFontSize(9);
        }else{
            $pdf->SetFontSize(7);
        }

        /**************改ページ*****************/
        if($i == 18){ 
            $pdf->AddPage();
        
            //初期化
            $j = 0; 
            $h = 0; 
            $i = 0; 
        }
        /***************************************/


        /***********次の行にポイント移動********/
        if($i == 3*($h+1)){
            $j = 0; 
            $h++;   
        }
        /***************************************/

        //郵便番号
        $pdf->SetXY($width[$j]-5,$line_height[$h]+$height[0]);
        $pdf->Cell(20,5,$label_data[$s]["post_no"],'0','1','L');

        //住所１
        $pdf->SetXY($width[$j],$line_height[$h]+$height[1]);
        $pdf->Cell(80,5,$label_data[$s]["address1"],'0','1','L');

        //住所２
        $pdf->SetXY($width[$j],$line_height[$h]+$height[2]);
        $pdf->Cell(80,5,$label_data[$s]["address2"],'0','1','L');

        //住所3
        $pdf->SetXY($width[$j],$line_height[$h]+$height[3]);
        $pdf->Cell(80,5,$label_data[$s]["address3"],'0','1','L');

        //本部の場合
        if($_SESSION["group_kind"] == "1"){
            //社名
            $pdf->SetXY($width[$j],$line_height[$h]+$height[4]);
            #2010-09-04 aoyama-n
            #$pdf->Cell(80,5,trim($label_data[$s]["shop_name"])." 御中",'0','1','L');
            $pdf->Cell(80,5,trim($label_data[$s]["shop_name"])." ".$label_data[$s]["compellation"],'0','1','L');
            //ショップ名
            $pdf->SetXY($width[$j],$line_height[$h]+$height[5]);
            $pdf->Cell(80,5,"(".trim($label_data[$s]["client_name"]).")",'0','1','L');
        //ショップの場合
        }else{
            #2010-09-04 aoyama-n
            if($label_data[$s]["client_name2"] != "" && $label_data[$s]["client_slip2"] != "1"){
                $compellation1 = "";
                $compellation2 = $label_data[$s]["compellation"];
            }elseif($label_data[$s]["client_name"] != "" && $label_data[$s]["client_slip1"] != "1"){
                $compellation1 = $label_data[$s]["compellation"];
                $compellation2 = "";
            }else{
                $compellation1 = "";
                $compellation2 = "";
            }

            if($label_data[$s]["client_slip1"] != "1"){
                //得意先名１
                $pdf->SetXY($width[$j],$line_height[$h]+$height[4]);
                $pdf->Cell(80,5,trim($label_data[$s]["client_name"])." ".$compellation1,'0','1','L');
            } 
            if($label_data[$s]["client_slip2"] != "1"){
                //得意先名２
                $pdf->SetXY($width[$j],$line_height[$h]+$height[5]);
                #$pdf->Cell(80,5,trim($label_data[$s]["client_name"])." 御中",'0','1','L');
                $pdf->Cell(80,5,trim($label_data[$s]["client_name2"])." ".$compellation2,'0','1','L');
            }
        }

        //tel
        $pdf->SetXY($width[$j],$line_height[$h]+$height[6]);
        $pdf->Cell(80,5,$label_data[$s]["tel"]."",'0','1','L');

        $i++;
        $j++;
    }

    $pdf->Output();
}

?>
