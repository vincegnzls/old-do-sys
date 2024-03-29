<?php
/**
 * TaxAmount.php
 *
 * Copyright (c) 2010, Bab Hitachi Soft Co.,Ltd 
 * All rights reserved.
 *
 */

/**
 * TaxAmount Class
 *
 * @author Yukio Hashimoto <hashimoto-y@bhsk.co.jp>
 *
 */
class TaxAmount
{

    /**
     * Returns an tax amount of the client
     *
     * @param int $tax_rate taxrate 
     * @param int $aord_id  accepting order identification
     *
     * @access public
     *
     * @return int
     */
    public function getAorderTaxAmount($tax_rate , $aord_id)
    {
        $db_con = Db_Connect();

        //aord_idからclient_idを取得し
        //得意先の課税区分(c_tax_div)と消費税の端数(tax_franct)を取得
        $sql  = "SELECT \n";
        $sql .= "    c_tax_div, \n";
        $sql .= "    tax_franct \n";
        $sql .= "FROM \n";
        $sql .= "    t_client \n";
        $sql .= "WHERE \n";
        $sql .= "    client_id = \n";
        $sql .= "    (SELECT client_id FROM t_aorder_h WHERE aord_id = $aord_id)\n";
        $sql .= ";";
        $result = Db_Query($db_con, $sql);
        $c_tax_div  = pg_fetch_result($result, 0, 0);
        $tax_franct = pg_fetch_result($result, 0, 1);

        //得意先の課税区分が外税の場合
        if ($c_tax_div == "1") {
            //aord_idから受注データの課税区分が外税の売上金額の合計を取得
            $sql  = "SELECT \n";
            $sql .= "    SUM(sale_amount) \n";
            $sql .= "FROM \n";
            $sql .= "    t_aorder_d \n";
            $sql .= "WHERE \n";
            $sql .= "    aord_id = $aord_id \n";
            $sql .= "AND \n";
            $sql .= "    tax_div = '1' \n";
            $sql .= ";";
            $result = Db_Query($db_con, $sql);
            $total_sale_amount = pg_fetch_result($result, 0, 0);

            $rate = bcdiv($tax_rate, 100, 2);
            $tax_amount = bcmul($total_sale_amount, $rate, 2);
            $tax_amount = Coax_Col($tax_franct, $tax_amount);

        //得意先の課税区分が非課税の場合
        } elseif ($c_tax_div == "3") {
            $tax_amount = 0;

        }

        //消費税額を返す
        return $tax_amount;

    }

}

?>
