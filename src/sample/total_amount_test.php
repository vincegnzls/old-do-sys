<?php
    
require_once("ENV_local.php");

$db_con = Db_Connect();

$price_data[0]  = 350;
$tax_div[0]     = 1;
$coax           = 1;
$tax_franct     = 1;
$tax_rate       = 5;
$client_id      = 3680;

$data = Total_Amount2($price_data, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $db_con);  

print_array($data);


function Total_Amount2($price_data, $tax_div, $coax="1", $tax_franct="1", $tax_rate="5", $client_id, $db_con){
    //得意先の課税区分を抽出
    $sql  = "SELECT";
    $sql .= "   c_tax_div ";
    $sql .= "FROM";
    $sql .= "   t_client ";
    $sql .= "WHERE";
    $sql .= "   client_id = $client_id";
    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    $c_tax_div = pg_fetch_result($result, 0,0);    //消費税率
    $rate = bcdiv($tax_rate, 100, 2);    //消費税率税　＋　１
    $in_rate = bcadd($rate,1,2);    //伝票合計を求める場合

    if(is_array($price_data) === true){
        //商品数ループ
        for($i = 0; $i < count($price_data); $i++){
            $buy_amount = str_replace(',','',$price_data[$i]);            //課税区分を決める
            if($c_tax_div == '1' && $tax_div[$i] == '1'){
                $tax_div[$i] = '1';
            //外税
            }elseif($c_tax_div == '2' && $tax_div[$i] == '1'){
                $tax_div[$i] = '2';
            //内税
            }elseif($tax_div[$i] == '3'){
                $tax_div[$i] = '3';
            //非課税
            }

            //配列の中身がNULLの場合は、処理を行わない
            if($buy_amount != null){
                //課税区分ごとに商品の合計を求める
                //外税の場合
                if($tax_div[$i] == '1'){
                    $outside_buy_amount     = bcadd($outside_buy_amount,$buy_amount);                //内税の場合
                }elseif($tax_div[$i] == '2'){
                    $inside_amount          = bcadd($inside_amount, $buy_amount);
                }elseif($tax_div[$i] == '3'){
                    $exemption_buy_amount   = bcadd($exemption_buy_amount, $buy_amount);
                }
            }
        }

        //消費税を求める
        //外税
        if($outside_buy_amount != 0){
            $outside_tax_amount   = bcmul($outside_buy_amount, $rate,2);                    //消費税額（丸め前）
            $outside_tax_amount   = Coax_Col2($tax_franct, $outside_tax_amount);             //消費税額（丸め後）
        }       

        //内税          if($inside_amount != 0){
            $in_rate              = bcmul($in_rate,100);
            $inside_tax_amount    = bcdiv($inside_amount, $in_rate,2);
            $inside_tax_amount    = bcmul($inside_tax_amount, $tax_rate,2);
            $inside_tax_amount    = Coax_Col2($tax_franct, $inside_tax_amount);
            $inside_buy_amount    = bcsub($inside_amount, $inside_tax_amount);
        }


        //税抜き金額合計
        $buy_amount_all     = $outside_buy_amount + $inside_buy_amount + $exemption_buy_amount;
        //消費税合計
        $tax_amount_all     = $outside_tax_amount + $inside_tax_amount;
        //税込み金額合計
        $total_amount       = $buy_amount_all + $tax_amount_all;

/*
                //消費税を算出
                //外税
                if($tax_div[$i] == '1'){
                    $tax_amount = bcmul($buy_amount,$rate,2);
                    $tax_amount = Coax_Col($tax_franct,$tax_amount);
                //内税
                }elseif($tax_div[$i] == '2'){
                    $tax_amount = bcdiv($buy_amount, $in_rate,2);
                    $tax_amount = bcsub($buy_amount, $tax_amount,2);
                    $tax_amount = Coax_Col($tax_franct,$tax_amount);
                    $buy_amount = bcsub($buy_amount, $tax_amount);

                //非課税
                }elseif($tax_div[$i] == '3'){
                    $tax_amount = 0;
                }

                //消費税合計
                $tax_amount_all = bcadd($tax_amount_all, $tax_amount);

                //仕入金額合計（税抜）
                $buy_amount_all = bcadd($buy_amount_all, $buy_amount);

                //仕入金額合計（税込）
                $total_amount = bcadd($buy_amount_all, $tax_amount_all);
                $total_amount_all = bcadd($total_amount_all, $total_amount);
            }
        }
*/
    //行単位の合計を求める場合
    }else{
        if($tax_div == null){
            $tax_div == '1';
        }

        //課税区分を決める
        if($c_tax_div == '1' && $tax_div == '1'){
            $tax_div = '1';             //外税
        }elseif($c_tax_div == '2' && $tax_div == '1'){
            $tax_div = '2';             //内税
        }elseif($tax_div == '3'){
            $tax_div = '3';             //非課税
        }

        //金額
        $buy_amount = str_replace(',','',$price_data);

        //消費税
        //外税
        if($tax_div[$i] == '1'){
            $tax_amount = bcmul($buy_amount,$rate,2);
            $tax_amount = Coax_Col2($tax_franct, $tax_amount);
        //内税
        //先に消費税額を求め、合計金額から消費税額を引いたものを税抜き金額とする。
        }elseif($tax_div[$i] == '2'){
            $tax_amount = bcdiv($buy_amount, $in_rate,2);
            $tax_amount = bcsub($buy_amount, $tax_amount,2);
            $tax_amount = Coax_Col2($tax_franct, $tax_amount);
            $buy_amount = bcsub($buy_amount, $tax_amount);
        //非課税
        }elseif($tax_div[$i] == '3'){
            $tax_amount = 0;
        }

        $buy_amount_all = $buy_amount;
        $tax_amount_all = $tax_amount;

        $total_amount = bcadd($buy_amount, $tax_amount);
    }

    $data = array($buy_amount_all, $tax_amount_all, $total_amount);
    return $data;
}

function Coax_Col2($coax, $price){
    if($coax == '1'){
        $price = floor($price);
    }elseif($coax == '2'){
        $price = round($price);
    }elseif($coax == '3'){
        $price = ceil($price);
    }

    return $price;
}



?>
