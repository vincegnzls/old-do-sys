<?php


/**
 * �оݤμ����ID���������SQL
 *
 * �ѹ�����
 * 1.0.0 (2007/01/18) ��������(kajioka-h)
 *
 * @param       int         $shop_id        ����å�ID
 * @param       string      $trade_div      �����ʬ
 *                                              "sale": ���
 *                                              "buy":  ���
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 * @param       boolean     $renew_flg      ���������ե饰
 *                                              true:   ���������ѤΤ�ΤΤ�
 *                                              false:  ����
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/01/18)
 *
 */
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2007/04/19      ��˾20-4    kajioka-h   �������Ρ���ʬ�ơ��֥�ζ�ۤ�NULL����ʤ���Ĥ����
 */
function Monthly_All_Client_Sql($shop_id, $trade_div, $end_day, $renew_flg){

    //�оݤΥơ��֥�̾�������
    if($trade_div == "sale"){
        $pay_table      = "t_payin_h";
        $balance_table  = "t_ar_balance";
    }else{
        $pay_table      = "t_payout_h";
        $balance_table  = "t_ap_balance";
    }


    //��������������塢���⡢����������or��ݻĹ�������ˤΤ��ä������ID���������
    $sql  = "
        SELECT DISTINCT(client_id) AS client_id 
        FROM ( 
            SELECT client_id 
            FROM t_".$trade_div."_h 
            WHERE shop_id = $shop_id 
    ";
    if($renew_flg){
        $sql .= "            AND renew_flg = true ";
    }
    $sql .= "
            AND ".$trade_div."_day <= '$end_day' 

            UNION 

            SELECT client_id 
            FROM $pay_table 
            WHERE shop_id = $shop_id 
    ";
    if($renew_flg){
        $sql .= "            AND renew_flg = true ";
    }
    $sql .= "
            AND pay_day <= '$end_day' 

    ";
    //������ݡˤξ�硢��ʬ����ǡ����ơ��֥�ζ�ۤ�NULL����ʤ���Ĥ����
    if($trade_div == "sale"){
        $sql .= "
            UNION 

            SELECT 
                t_payallocation_d.client_id 
            FROM 
                t_payin_h 
                LEFT JOIN t_payallocation_d ON t_payin_h.pay_id = t_payallocation_d.pay_id 
            WHERE 
                t_payin_h.shop_id = $shop_id 
                AND t_payin_h.pay_day <= '$end_day' 
                AND t_payin_h.payin_div = '2' 
                AND t_payallocation_d.amount IS NOT NULL 
        ";
        if($renew_flg){
            $sql .= "            AND t_payin_h.renew_flg = true ";
        }
    }
    $sql .= $test = "
            UNION 

            SELECT client_id 
            FROM $balance_table 
            WHERE shop_id = $shop_id 
            AND monthly_close_day_this < '$end_day' 
        ) AS t_all_client 
    ";

    return $sql;

}



/**
 * ��������η��������/��|��/�ݻĹ�����SQL
 *
 * �ѹ�����
 * 1.0.0 (2007/01/23) ��������(kajioka-h)
 *
 * @param       int         $shop_id        ����оݤΥ���å�ID
 * @param       string      $trade_div      �����ʬ
 *                                              "sale": ���
 *                                              "buy":  ���
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/01/23)
 *
 */
function Monthly_Balance_Last_Sql($shop_id, $trade_div){

    //�оݤΥơ��֥�̾�������
    $table_name = ($trade_div == "sale") ? "ar" : "ap";

    $sql  = "
        SELECT 
            t_".$table_name."_balance.client_id, 
            t_".$table_name."_balance.monthly_close_day_last, 
            t_".$table_name."_balance2.".$table_name."_balance_last 
        FROM 
            ( 
                SELECT 
                    client_id, 
                    MAX(monthly_close_day_this) AS monthly_close_day_last 
                FROM 
                    t_".$table_name."_balance 
                WHERE 
                    shop_id = $shop_id 
                GROUP BY 
                    client_id 
            ) AS t_".$table_name."_balance 
            INNER JOIN 
            ( 
                SELECT 
                    client_id, 
                    monthly_close_day_this AS monthly_close_day_last, 
                    ".$table_name."_balance_this AS ".$table_name."_balance_last 
                FROM 
                    t_".$table_name."_balance 
                WHERE 
                    shop_id = $shop_id 
            ) AS t_".$table_name."_balance2 ON t_".$table_name."_balance.client_id = t_".$table_name."_balance2.client_id 
                AND t_".$table_name."_balance.monthly_close_day_last = t_".$table_name."_balance2.monthly_close_day_last
    ";

    return $sql;

}



/**
 * ������֤�����׳ۡʳ��䡢���䡢������ˤȤ��ξ����ǹ�׳ۤ���Ф���SQL
 *
 * �ѹ�����
 * 1.0.0 (2007/01/18) ��������(kajioka-h)
 *
 * @param       int         $shop_id        ����оݤΥ���å�ID
 * @param       string      $start_day      �������ʤ��������������齸�ס�
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 * @param       boolean     $renew_flg      ���������ե饰
 *                                              true:   ���������ѤΤ�ΤΤ�
 *                                              false:  ����
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/01/18)
 *
 */
function Monthly_Sale_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg){

    $sql  = "
        SELECT 
            client_id, 
            SUM( 
                CASE 
                    WHEN trade_id IN (11, 61, 15) THEN net_amount 
                    ELSE -(net_amount) 
                END 
            ) AS net_sale_amount, 
            SUM( 
                CASE 
                    WHEN trade_id IN (11, 61, 15) THEN tax_amount 
                    ELSE -(tax_amount) 
                END 
            ) AS tax_amount 
        FROM 
            t_sale_h 
        WHERE 
            shop_id = $shop_id 
            AND sale_day > '$start_day' 
            AND sale_day <= '$end_day' 
    ";
    if($renew_flg){
        $sql .= "            AND renew_flg = true ";
    }
    $sql .= "
        GROUP BY 
            client_id 
    ";

    return $sql;

}


/**
 * ������֤������׳ۡʽ�Ĵ���ۡˡ�Ĵ���ۤ���Ф���SQL
 *
 * �ѹ�����
 * 1.0.0 (2007/01/18) ��������(kajioka-h)
 * 1.0.0 (2007/08/09) ������⤬���٤ƿƤ˵󤬤��Զ�������watanabe-k)
 *
 * @param       int         $shop_id        ����å�ID
 * @param       string      $start_day      �������ʤ��������������齸�ס������������Ȥ�����ꤹ���
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 * @param       boolean     $renew_flg      ���������ե饰
 *                                              true:   ���������ѤΤ�ΤΤ�
 *                                              false:  ����
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.1.0 (2007/04/11)
 *
 */
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2007/04/11      ��˾20-4    kajioka-h   ���������б�
 *  2007-07-13                  fukuda      ����Ĵ���Ȥ���ʳ��μ����ʬ����1��ɼ���1:n, n:1�ξ��˵������Զ�����
 */
function Monthly_Payin_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg)
{

    // ������ID�������׳ۡ�Ĵ����׳ۤ����
    $sql  = "SELECT \n";
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_payin_h.client_id IS NOT NULL \n";
    $sql .= "       THEN t_payin_h.client_id \n";
    $sql .= "       ELSE t_payallocation_d.client_id \n";
    $sql .= "   END \n";
    $sql .= "   AS client_id, \n";
    $sql .= "   COALESCE(t_payin_h.payin_amount, 0) + COALESCE(t_payallocation_d.payin_amount, 0) AS payin_amount, \n";
    $sql .= "   COALESCE(t_payin_h.tune_amount,  0) + COALESCE(t_payallocation_d.tune_amount,  0) AS tune_amount \n";
    $sql .= "FROM \n";
    // �̾�����
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_payin_h.client_id, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN t_payin_d.trade_id != 38 \n";
    $sql .= "                   THEN COALESCE(t_payin_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS payin_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN t_payin_d.trade_id  = 38 \n";
    $sql .= "                   THEN COALESCE(t_payin_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS tune_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           t_payin_h \n";
    $sql .= "           LEFT JOIN t_payin_d ON t_payin_h.pay_id = t_payin_d.pay_id \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_payin_h.shop_id = $shop_id \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day > '$start_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day <= '$end_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.payin_div = '1' \n";
    if ($renew_flg){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = true \n";
    }
    $sql .= "       GROUP BY \n";
    $sql .= "           t_payin_h.client_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_payin_h \n";
    // �������
    $sql .= "   FULL OUTER JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
//    $sql .= "           t_payin_h.client_id, \n";
//����оݥ������ѹ�<watanabe-k>
    $sql .= "           t_payallocation_d.client_id, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN t_payallocation_d.trade_id != 38 \n";
    $sql .= "                   THEN COALESCE(t_payallocation_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS payin_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN t_payallocation_d.trade_id  = 38 \n";
    $sql .= "                   THEN COALESCE(t_payallocation_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS tune_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           t_payin_h \n";
    $sql .= "           LEFT JOIN t_payallocation_d ON t_payin_h.pay_id = t_payallocation_d.pay_id \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_payin_h.shop_id = $shop_id \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day > '$start_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day <= '$end_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.payin_div = '2' \n";
    if ($renew_flg){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = true \n";
    }
    $sql .= "       GROUP BY \n";
//    $sql .= "           t_payin_h.client_id \n";
//GROUP BY�������ѹ�<watanabe-k>
    $sql .= "           t_payallocation_d.client_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_payallocation_d \n";
    $sql .= "   ON t_payin_h.client_id = t_payallocation_d.client_id \n";

    return $sql;

}


/**
 * ����     ������֤�������������Ф��륯������֤�
 *
 * ����     ��
 *
 * @param   int     $shop_id        ������å�ID
 * @param   str     $start_day      ��д��ֳ�����
 * @param   str     $end_day        ��д��ֽ�λ��
 * @param   bool    $renew_flg      ���������»ܥե饰
 *
 */
function Monthly_Payin_Rebate_Sql($shop_id, $start_day, $end_day, $renew_flg)
{
    // ������ID������������
    $sql .= "SELECT \n";
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_payin_h.client_id IS NOT NULL \n";
    $sql .= "       THEN t_payin_h.client_id \n";
    $sql .= "       ELSE t_payallocation_d.client_id \n";
    $sql .= "   END \n";
    $sql .= "   AS client_id, \n";
    $sql .= "   COALESCE(t_payin_h.payin_amount, 0) + COALESCE(t_payallocation_d.payin_amount, 0) AS payin_rebate \n";
    $sql .= "FROM \n";
    // �̾�����ʼ������
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_payin_h.client_id, \n";
    $sql .= "           SUM(COALESCE(t_payin_d.amount, 0)) AS payin_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           t_payin_h \n";
    $sql .= "           LEFT JOIN t_payin_d \n";
    $sql .= "               ON  t_payin_h.pay_id = t_payin_d.pay_id \n";
    $sql .= "               AND t_payin_d.trade_id = 35 \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_payin_h.shop_id = $shop_id \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day > '$start_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day <= '$end_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.payin_div = '1' \n";
    if ($renew_flg){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = true \n";
    }
    $sql .= "       GROUP BY \n";
    $sql .= "           t_payin_h.client_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_payin_h \n";
    // �������ʼ������
    $sql .= "   FULL OUTER JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_payallocation_d.client_id, \n";
    $sql .= "           SUM(COALESCE(t_payallocation_d.amount, 0)) AS payin_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           t_payin_h \n";
    $sql .= "           LEFT JOIN t_payallocation_d \n";
    $sql .= "               ON  t_payin_h.pay_id = t_payallocation_d.pay_id \n";
    $sql .= "               AND t_payallocation_d.trade_id = 35 \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_payin_h.shop_id = $shop_id \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day > '$start_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day <= '$end_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.payin_div = '2' \n";
    if ($renew_flg){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = true \n";
    }
    $sql .= "       GROUP BY \n";
    $sql .= "           t_payallocation_d.client_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_payallocation_d \n";
    $sql .= "   ON t_payin_h.client_id = t_payallocation_d.client_id \n";

    return $sql;

}


/**
 * ������֤λ�����׳ۡʳݻ���������������������ˤȤ��ξ����ǹ�׳ۤ���Ф���SQL
 *
 * �ѹ�����
 * 1.0.0 (2007/01/27) ��������(kajioka-h)
 *
 * @param       int         $shop_id        ����оݤΥ���å�ID
 * @param       string      $start_day      �������ʤ��������������齸�ס������������Ȥ�����ꤹ���
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 * @param       boolean     $renew_flg      ���������ե饰
 *                                              true:   ���������ѤΤ�ΤΤ�
 *                                              false:  ����
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/01/27)
 *
 */
function Monthly_Buy_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg){

    $sql  = "
        SELECT 
            client_id, 
            SUM( 
                CASE 
                    WHEN trade_id IN (21, 71, 25) THEN net_amount 
                    ELSE -(net_amount) 
                END 
            ) AS net_buy_amount, 
            SUM( 
                CASE 
                    WHEN trade_id IN (21, 71, 25) THEN tax_amount 
                    ELSE -(tax_amount) 
                END 
            ) AS tax_amount 
        FROM 
            t_buy_h 
        WHERE 
            shop_id = $shop_id 
            AND buy_day > '$start_day' 
            AND buy_day <= '$end_day' 
    ";
    if($renew_flg){
        $sql .= "            AND renew_flg = true ";
    }
    $sql .= "
        GROUP BY 
            client_id 
    ";

    return $sql;

}


/**
 * ������֤λ�ʧ��׳ۡʽ�Ĵ���ۡˡ�Ĵ���ۤ���Ф���SQL
 *
 * �ѹ�����
 * 1.0.0 (2007/01/27) ��������(kajioka-h)
 *
 * @param       int         $shop_id        ����å�ID
 * @param       string      $start_day      �������ʤ��������������齸�ס�
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 * @param       boolean     $renew_flg      ���������ե饰
 *                                              true:   ���������ѤΤ�ΤΤ�
 *                                              false:  ����
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/01/27)
 *
 */
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2007-07-13                  fukuda      ��ʧĴ���Ȥ���ʳ��μ����ʬ����1��ɼ���1:n, n:1�ξ��˵������Զ�����
 */
function Monthly_Payout_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg){

    // �����ID����ʧ��׳ۡ�Ĵ����׳ۤ����
    $sql  = "SELECT \n";
    $sql .= "   t_payout_h.client_id, \n";
    $sql .= "   SUM( \n";
    $sql .= "       CASE \n";
    $sql .= "           WHEN t_payout_d.trade_id != 46 \n";
    $sql .= "           THEN COALESCE(t_payout_d.pay_amount, 0) \n";
    $sql .= "           ELSE 0 \n";
    $sql .= "       END \n";
    $sql .= "   ) \n";
    $sql .= "   AS payout_amount, \n";
    $sql .= "   SUM( \n";
    $sql .= "       CASE \n";
    $sql .= "           WHEN t_payout_d.trade_id  = 46 \n";
    $sql .= "           THEN COALESCE(t_payout_d.pay_amount, 0) \n";
    $sql .= "           ELSE 0 \n";
    $sql .= "       END \n";
    $sql .= "   ) \n";
    $sql .= "   AS tune_amount \n";
    $sql .= "FROM \n";
    $sql .= "   t_payout_h \n";
    $sql .= "   INNER JOIN t_payout_d ON t_payout_h.pay_id = t_payout_d.pay_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_payout_h.shop_id = $shop_id \n";
    $sql .= "AND \n";
    $sql .= "   t_payout_h.pay_day > '$start_day' \n";
    $sql .= "AND \n";
    $sql .= "   t_payout_h.pay_day <= '$end_day' \n";
    if ($renew_flg){
    $sql .= "AND \n";
    $sql .= "   t_payout_h.renew_flg = true \n";
    }
    $sql .= "GROUP BY \n";
    $sql .= "   t_payout_h.client_id \n";

    return $sql;

}


/**
 * ����     ������֤λ�ʧ���������Ф��륯������֤�
 *
 * ����     ��
 *
 * @param   int     $shop_id        ������å�ID
 * @param   str     $start_day      ��д��ֳ�����
 * @param   str     $end_day        ��д��ֽ�λ��
 * @param   bool    $renew_flg      ���������»ܥե饰
 *
 */
function Monthly_Payout_Rebate_Sql($shop_id, $start_day, $end_day, $renew_flg){

    // �����ID���������
    $sql  = "SELECT \n";
    $sql .= "   t_payout_h.client_id, \n";
    $sql .= "   SUM(COALESCE(t_payout_d.pay_amount, 0)) AS payout_rebate \n";
    $sql .= "FROM \n";
    $sql .= "   t_payout_h \n";
    $sql .= "   LEFT JOIN t_payout_d \n";
    $sql .= "       ON  t_payout_h.pay_id = t_payout_d.pay_id \n";
    $sql .= "       AND t_payout_d.trade_id = 48 \n";
    $sql .= "WHERE \n";
    $sql .= "   t_payout_h.shop_id = $shop_id \n";
    $sql .= "AND \n";
    $sql .= "   t_payout_h.pay_day > '$start_day' \n";
    $sql .= "AND \n";
    $sql .= "   t_payout_h.pay_day <= '$end_day' \n";
    if ($renew_flg){
    $sql .= "AND \n";
    $sql .= "   t_payout_h.renew_flg = true \n";
    }
    $sql .= "GROUP BY \n";
    $sql .= "   t_payout_h.client_id \n";

    return $sql;

}


/**
 * ������֤Υ������ƥ��Ȥ��ξ����ǳۡ��ޤ��ϰ������ǳۤ���Ф���SQL
 *
 * �ѹ�����
 * 1.0.0 (2007/03/07) ��������(kajioka-h)
 *
 * @param       int         $shop_id        ����å�ID
 * @param       string      $start_day      �������ʤ��������������齸�ס�
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 * @param       string      $data_div       �ǡ�����ʬ
 *                                              1�� �������ƥ��Ȥ��ξ����Ǥ����
 *                                              2�� �������Ǥ����
 * @param       string      $trade_div      �����ʬ
 *                                              "sale": ���
 *                                              "buy":  ���
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/03/07)
 *
 */
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2007/05/28      B0702-058   kajioka-h   �����ξ�硢Ʊ��client_id�������㤤�⤹��Τǡ��������ƥ����������Ǥζ��̤��Ĥ��ʤ��Х�����
 *
 */
function Monthly_Lump_Amount_Sql($shop_id, $start_day, $end_day, $data_div, $trade_div)
{
    $sql  = "
        SELECT 
            client_id, 
    ";
    //�ǡ�����ʬ="1"�ξ�硢�������ƥ��ۡ������ǳ�
    if($data_div == "1"){
        $sql .= "
            SUM(net_amount) AS royalty_price, 
            SUM(tax_amount) AS royalty_tax 
        ";
    //�ǡ�����ʬ="2"�ξ�硢�������ǳ�
    }elseif($data_div == "2"){
        $sql .= "
            SUM(tax_amount) AS adjust_tax 
        ";
    }
    $sql .= "
        FROM 
            t_lump_amount 
        WHERE 
            shop_id = $shop_id
            AND allocate_day > '$start_day' 
            AND allocate_day <= '$end_day' 
            AND data_div = '$data_div' 
    ";
    if($trade_div == "sale"){
        $sql .= "
            AND bill_id IS NOT NULL 
        ";
    }elseif($trade_div == "buy"){
        $sql .= "
            AND schedule_payment_id IS NOT NULL 
        ";
    }else{
        return false;
    }
    $sql .= "
        GROUP BY 
            client_id 
    ";

    return $sql;

}


/**
 * �оݤμ�����/��|��/�ݻĹ���������
 *
 * �ѹ�����
 * 1.0.0 (2007/04/13) ��������(kajioka-h)
 *
 * @param       resource    $db_con         DB��³�꥽����
 * @param       string      $trade_div      �����ʬ
 *                                              "sale": ��ݻĹ�����
 *                                              "buy":  ��ݻĹ�����
 * @param       int         $client_id      ��᤿�������ID
 * @param       int         $shop_id        ����å�ID
 *                                              �ǥե���Ȥ� $_SESSION["client_id"]
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 *                                              'yyyy-mm-dd' �������Ϥ��Ƥ�
 *                                              �ǥե���Ȥ�����������
 * @param       boolean     $renew_flg      ���������ե饰
 *                                              true:   ���������ѤΤ�ΤΤ߽���
 *                                              false:  ���ơʥǥե���ȡ�
 *
 * @return      ������      int             /��|��/�ݻĹ�
 *              ���ԡ�      boolean         false
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.0.0 (2007/04/13)
 *
 */
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2007/05/11      B0702-051   kajioka-h   Ĵ���ۤ���椬�ְ�äƤ���Τ���
 *  2007/05/28      B0702-058   kajioka-h   �����ξ�硢Ʊ��client_id������㤤����Τǡ��������ƥ����������Ǥζ��̤��Ĥ��ʤ����Ѥ��б�
 *
 */
function Get_Balance($db_con, $trade_div, $client_id, $shop_id=null, $end_day=null, $renew_flg=false)
{

    //�����ʬ���顢��Ф���ơ��֥��������
    if($trade_div == "sale"){
        $arp        = "ar";         //���
        $pay_div    = "payin";      //����
    }elseif($trade_div == "buy"){
        $arp        = "ap";         //���
        $pay_div    = "payout";     //��ʧ
    }else{
        return false;
    }

    //����å�ID
    $shop_id = ($shop_id === null) ? $_SESSION["client_id"] : $shop_id;

    //��д��֤λϤ�����������ˤ����
    $sql  = "SELECT \n";
    $sql .= "    close_day \n";
    $sql .= "FROM \n";
    $sql .= "    t_sys_renew \n";
    $sql .= "WHERE \n";
    $sql .= "    shop_id = ".$shop_id." \n";
    $sql .= "    AND \n";
    $sql .= "    renew_div = '2' \n";
    $sql .= "ORDER BY \n";
    $sql .= "    close_day DESC \n";
    $sql .= "LIMIT 1 \n";
    $sql .= ";\n";

    $result = Db_Query($db_con, $sql);
    if(pg_num_rows($result) == 0){
        $start_day = START_DAY;                         //�����ƥ೫����
    }else{
        $start_day = pg_fetch_result($result, 0, 0);    //����η������
    }

    //��д��֤λϤ�����
    global $g_today;
    $end_day = ($end_day == null) ? $g_today : $end_day;


    //�Ĺ����SQL����
    $sql  = "
        SELECT
            ( COALESCE(t_".$arp."_balance_last.".$arp."_balance_last, 0)
              - COALESCE(t_".$pay_div."_amount.".$pay_div."_amount, 0)
              - COALESCE(t_".$pay_div."_amount.tune_amount, 0)
              + COALESCE(t_".$trade_div."_amount.net_".$trade_div."_amount, 0)
              + COALESCE(t_royalty.royalty_price, 0)
              + COALESCE(t_".$trade_div."_amount.tax_amount, 0)
              + COALESCE(t_royalty.royalty_tax, 0)
              + COALESCE(t_adjust_tax.adjust_tax, 0)
            ) AS ".$arp."_balance_this

        FROM
            t_client 
    ";

    //�����������η����������ݻĹ�����
    $sql .= "
            LEFT JOIN
            (
    ";
    $sql .= Monthly_Balance_Last_Sql($shop_id, $trade_div);
    $sql .= "
            ) AS t_".$arp."_balance_last ON t_client.client_id = t_".$arp."_balance_last.client_id
    ";

    //������������������ޤǤ�/���|����/�ۡ���ȴ�ˡ������ǳۡ�/���|����/�ۡ��ǹ��ˤ����
    $sql .= "
            LEFT JOIN
            (
    ";
    if($trade_div == "sale"){
        $sql .= Monthly_Sale_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
    }else{
        $sql .= Monthly_Buy_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
    }
    $sql .= "
            ) AS t_".$trade_div."_amount ON t_client.client_id = t_".$trade_div."_amount.client_id
    ";

    //������������������ޤǤ�����ۡʽ�Ĵ���ۡˡ�Ĵ���ۤ����
    $sql .= "
            LEFT JOIN
            (
    ";
    if($trade_div == "sale"){
        $sql .= Monthly_Payin_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
    }else{
        $sql .= Monthly_Payout_Amount_Sql($shop_id, $start_day, $end_day, $renew_flg);
    }
    $sql .= "
            ) AS t_".$pay_div."_amount ON t_client.client_id = t_".$pay_div."_amount.client_id
    ";

    //������������������ޤǤΥ������ƥ��ۡ����ξ����ǳۤ����
    $sql .= "
            LEFT JOIN
            (
    ";
    $sql .= Monthly_Lump_Amount_Sql($shop_id, $start_day, $end_day, "1", $trade_div);
    $sql .= "
            ) AS t_royalty ON t_client.client_id = t_royalty.client_id
    ";

    //������������������ޤǤΰ������ǳۤ����
    $sql .= "
            LEFT JOIN
            (
    ";
    $sql .= Monthly_Lump_Amount_Sql($shop_id, $start_day, $end_day, "2", $trade_div);
    $sql .= "
            ) AS t_adjust_tax ON t_client.client_id = t_adjust_tax.client_id
    ";

    //�оݤ�������ID����
    $sql .= "WHERE \n";
    $sql .= "    t_client.client_id = $client_id \n";

    $sql .= ";\n";
//print_array($sql);


    $result = Db_Query($db_con, $sql);
    if(pg_num_rows($result) == 0 || $result === false){
        return false;
    }

    $balance = pg_fetch_result($result, 0, 0);

    return $balance;

}


/**
 * ��ݡ���ݻĹ�������оݤμ����ID���������SQL
 *
 * �ѹ�����
 * 1.0.0 (2011/01/29) ��������
 *
 * @param       int         $shop_id        ����å�ID
 * @param       string      $trade_div      �����ʬ
 *                                              "sale": ���
 *                                              "buy":  ���
 * @param       string      $end_day        ��λ���ʤ������ޤǽ��ס�
 * @param       boolean     $renew_flg      ���������ե饰
 *                                              true:   ���������ѤΤ�ΤΤ�
 *                                              false:  ����
 *
 * @return      string      $sql            ��������SQL
 *
 * @author      watanabe-k <watanabe-k@bhsk.co.jp>
 * @version     1.0.0 (20l1/01/29)
 *
 */
function Monthly_All_Client_Sql_For_Balance($shop_id, $trade_div, $end_day, $renew_flg){

    //�оݤΥơ��֥�̾�������
    if($trade_div == "sale"){
        $pay_table      = "t_payin_h";
        $balance_table  = "t_ar_balance";
    }else{
        $pay_table      = "t_payout_h";
        $balance_table  = "t_ap_balance";
    }


    //��������������塢���⡢����������or��ݻĹ�������ˤΤ��ä������ID���������
    $sql  = "
        SELECT DISTINCT(client_id) AS client_id 
        FROM ( 
            SELECT client_id 
            FROM t_".$trade_div."_h 
            WHERE shop_id = $shop_id 
    ";
    if($renew_flg){
        $sql .= "            AND renew_flg = true ";
    }
    $sql .= "
            AND ".$trade_div."_day <= '$end_day' 

            UNION 

            SELECT client_id 
            FROM $pay_table 
            WHERE shop_id = $shop_id 
    ";
    if($renew_flg){
        $sql .= "            AND renew_flg = true ";
    }
    $sql .= "
            AND pay_day <= '$end_day' 

    ";
    //������ݡˤξ�硢��ʬ����ǡ����ơ��֥�ζ�ۤ�NULL����ʤ���Ĥ����
    if($trade_div == "sale"){
        $sql .= "
            UNION 

            SELECT 
                t_payallocation_d.client_id 
            FROM 
                t_payin_h 
                LEFT JOIN t_payallocation_d ON t_payin_h.pay_id = t_payallocation_d.pay_id 
            WHERE 
                t_payin_h.shop_id = $shop_id 
                AND t_payin_h.pay_day <= '$end_day' 
                AND t_payin_h.payin_div = '2' 
                AND t_payallocation_d.amount IS NOT NULL 
        ";
        if($renew_flg){
            $sql .= "            AND t_payin_h.renew_flg = true ";
        }
    }
    $sql .= $test = "
            UNION 

            SELECT client_id 
            FROM $balance_table 
            WHERE shop_id = $shop_id 
            AND monthly_close_day_this <= '$end_day' 
        ) AS t_all_client 
    ";

    return $sql;

}


?>