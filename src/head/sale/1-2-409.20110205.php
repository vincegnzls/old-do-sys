<?php

/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2006/10/10      05-003      ��          �֥饦�������ޤ������ܥ��󲡲�����hierselect���ͤ���������ʤ��Х����б�
 *  2007/01/25                  watanabe-k  �ܥ���ο��ѹ� 
 *  2009/06/15     	����No.4    aizawa-m	�����ʬ�����껦�פξ�硢�ֶ�ԡ�ɬ�ܤˤ��ʤ�
 *	2009/06/16		����No.4	aizawa-m	�����ʬ�����껦�פξ�硢�ֶ�ԡפ����Ϥ�����ȥ��顼
 *  2009/07/27                  aoyama-n    ��������ѹ�����������������������դ�����Ǥ��Ƥ��ޤ��Զ�罤��
 *  2009-10-16                  hashimoto-y �ƻҴط������򤷤��Ȥ��ηٹ�ɽ�����ѻ�
 *  2011/02/05                  watanabe-k  ���������ʸ�ˡ�����Ǥ��Ƥ��ޤ��Զ��ν���
 *
 *
 */

$page_title = "��������";

// �Ķ�����ե�����
require_once("ENV_local.php");

// HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB��³
$db_con = Db_Connect();

// �⥸�塼��̾
$mod_me = "1-2-409";


/*****************************/
// ���´�Ϣ����
/*****************************/
// ���¥����å�
$auth       = Auth_Check($db_con);
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/*****************************/
// ���������SESSION�˥��å�
/*****************************/
// GET��POST��̵�����
if ($_GET == null && $_POST == null){
    Set_Rtn_Page("payin");
}


/****************************/
// �������
/****************************/
// OK�ܥ��󲡲�����value�򥯥ꥢ
if ($_POST["form_ok_btn"] != null){
    $clear_hdn["form_ok_btn"] = "";
    $form->setConstants($clear_hdn);
}


/*****************************/
// �����ѿ�����
/*****************************/
// SESSION
$shop_id            = $_SESSION["client_id"];           // ����å�ID
$group_kind         = $_SESSION["group_kind"];          // ���롼�׼���
$staff_id           = $_SESSION["staff_id"];            // �����å�ID
$staff_name         = $_SESSION["staff_name"];          // �����å�̾
// POST
$post               = $_POST["post"];                   // �ڡ���POST����
// POST(hidden)
$calc_flg           = $_POST["hdn_calc_flg"];           // ��׻��Хե饰


/****************************/
// �Կ���Ϣ����
/****************************/
/*** ������� ***/
// ����Կ���POST����Ƥ�����Ϥ��ιԿ�
// POST����Ƥ��ʤ����ϥǥե����
$max_row = ($_POST["hdn_max_row"] != null) ? $_POST["hdn_max_row"] : 5;

/*** ���ɲ����� ***/
// ���ɲåե饰��true�ξ��
if ($_POST["hdn_add_row_flg"] == true){

    // �����+5
    $max_row = $max_row+5;

    // ���ɲåե饰�򥯥ꥢ
    $clear_hdn_flg["hdn_add_row_flg"] = "";
    $form->setConstants($clear_hdn_flg);

}

/*** �Կ���hidden�� ***/
// �Կ���hidden�˳�Ǽ
$row_num_data["hdn_max_row"] = $max_row;
$form->setConstants($row_num_data);

// hidden�ιԺ������POST���줿���
if ($_POST["hdn_ary_del_rows"] != null){

    // ����ޤǤ˺�����줿�����Ƥ������������������
    foreach ($_POST["hdn_ary_del_rows"] as $key => $value){
        $ary_del_row_history[] = $value;
    }

}

/*** �Ժ������ ***/
// ������ֹ椬POST���줿���
if ($_POST["hdn_del_row_no"] != null){

    // ������ֹ����
    $del_row_no = $_POST["hdn_del_row_no"];

    // ������ֹ����������������ɲ�
    $ary_del_row_history[] = $_POST["hdn_del_row_no"];

    // ��������������hidden�˥��å�
    $del_rows_data["hdn_ary_del_rows"] = $ary_del_row_history;
    $form->setConstants($del_rows_data);

    // hidden�κ�����ֹ�򥯥ꥢ
    $clear_hdn_data["hdn_del_row_no"] = "";
    $form->setConstants($clear_hdn_data);

}

// �Ժ�������٤�Ԥ��Ƥ��ʤ����
if ($_POST["hdn_ary_del_rows"] == null && $_POST["hdn_del_row_no"] == null){

    // ���κ���Գ�Ǽ����������
    $ary_del_row_history = array();

}


/****************************/
// �������ܥ������
/****************************/
// �������ܥ��󤬲������줿���
if ($_POST["hdn_clt_set_flg"] != null){

    // �Կ�ʬ�ʺ���ѹԴޤ�˥롼��
    for ($i=0; $i<$max_row; $i++){

        // ���������ˤ���Ԥϥ��롼
        if (!in_array($i, $ary_del_row_history)){
            // ����������Ƥ�ե�����˥��å�
            $clt_set_data["form_payin_date"][$i]["y"]   = $_POST["form_payin_date_clt_set"]["y"];   // ��������ǯ��
            $clt_set_data["form_payin_date"][$i]["m"]   = $_POST["form_payin_date_clt_set"]["m"];   // �������ʷ��
            $clt_set_data["form_payin_date"][$i]["d"]   = $_POST["form_payin_date_clt_set"]["d"];   // ������������
            $clt_set_data["form_trade"][$i]             = $_POST["form_trade_clt_set"];             // �����ʬ
            $clt_set_data["form_bank_".$i][0]           = $_POST["form_bank_clt_set"][0];           // ���
            $clt_set_data["form_bank_".$i][1]           = $_POST["form_bank_clt_set"][1];           // ��Ź
            $clt_set_data["form_bank_".$i][2]           = $_POST["form_bank_clt_set"][2];           // �����ֹ�
        }

    }

    // ����������Ƥ�ե�����˥��å�
    $form->setConstants($clt_set_data);

    // �������ե饰�򥯥ꥢ
    $clear_hdn_data["hdn_clt_set_flg"] = "";
    $form->setConstants($clear_hdn_data);

}


/****************************/
// hdn�ο���̾����static�˥��å�
/****************************/
for ($i=0; $i<$max_row; $i++){

    // ������ID�Τ���Ԥξ��
    if ($_POST["hdn_claim_id"][$i] != null){
        // ɬ�פʥǡ����ʿ���̾��1,2�ˤ򥻥å�
        $pay_account_set["form_pay_name"][$i]       = stripslashes(htmlspecialchars($_POST["hdn_pay_name"][$i]));
        $pay_account_set["form_account_name"][$i]   = stripslashes(htmlspecialchars($_POST["hdn_account_name"][$i]));
        $form->setConstants($pay_account_set);
    }

}


/****************************/
// ��������䴰�ʥ��������ϻ���
/****************************/
// POST�ǡ�����������
if ($_POST["hdn_claim_search_flg"] != null){

    // �����踡�����줿���ֹ�����
    $claim_search_row = array_search("true", $_POST["hdn_claim_search_flg"]);

    // �����踡�����줿���ֹ椬null�Ǥʤ����
    if ($claim_search_row !== null){

        // POST���줿�����襳���ɤ��ѿ�������
        $search_claim_cd1 = $_POST["form_claim_cd"][$claim_search_row]["cd1"];
        $search_claim_cd2 = $_POST["form_claim_cd"][$claim_search_row]["cd2"];

        // �������줿������ξ�������
        $sql  = "SELECT \n";
        $sql .= "   t_client.client_id, \n";
        $sql .= "   t_client.client_cname, \n";
        $sql .= "   t_client.pay_name, \n";
        $sql .= "   t_client.account_name \n";
        $sql .= "FROM \n";
        $sql .= "   t_client \n";
        $sql .= "   INNER JOIN t_claim ON t_client.client_id = t_claim.claim_id \n";
        $sql .= "WHERE \n";
        $sql .= "   t_client.client_cd1 = '$search_claim_cd1' \n";
        $sql .= "AND \n";
        $sql .= "   t_client.client_cd2 = '$search_claim_cd2' \n";
        $sql .= "AND \n";
        $sql .= "   t_client.client_div = '3' \n";
//        $sql .= "AND \n";
//        $sql .= "   t_client.state = '1' \n";
        $sql .= "AND \n";
        $sql .= "   t_client.shop_id = $shop_id \n";
        $sql .= ";";

        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);

        // �����ǡ�����������
        if ($num > 0){
            $search_claim_id        = pg_fetch_result($res, 0, 0);  // ������ID
            $search_claim_cname     = pg_fetch_result($res, 0, 1);  // ������̾��ά�Ρ�
            $search_pay_name        = pg_fetch_result($res, 0, 2);  // ����̾��1
            $search_account_name    = pg_fetch_result($res, 0, 3);  // ����̾��2
            $claim_found_flg        = true;                         // ������¸�ߥե饰
        }else{
            $search_claim_id        = "";
            $search_claim_cname     = "";
            $search_pay_name        = "";
            $search_account_name    = "";
            $claim_found_flg        = false;
        }

        // ������ID��������̾��ά�Ρˡ�����̾��1,2��ե�����˥��å�
        $claim_data["hdn_claim_id"][$claim_search_row]      = $search_claim_id;
        $claim_data["form_claim_cname"][$claim_search_row]  = $search_claim_cname;
        $claim_data["form_pay_name"][$claim_search_row]     = htmlspecialchars($search_pay_name);
        $claim_data["form_account_name"][$claim_search_row] = htmlspecialchars($search_account_name);
        $claim_data["hdn_pay_name"][$claim_search_row]      = $search_pay_name;
        $claim_data["hdn_account_name"][$claim_search_row]  = $search_account_name;
        $form->setConstants($claim_data);

        // ������¸�ߥե饰��true�ξ��
        if ($claim_found_flg == true){

/*
            // ���������谸�Ǻǿ��������ID�Υǡ����쥳���ɤλ�ʧͽ��۽��״��֤����
            $sql  = "SELECT \n";
            $sql .= "   ( \n";
            $sql .= "       CASE \n";
            $sql .= "           WHEN \n";
            $sql .= "               t_bill_d.payment_this < 0 \n";
            $sql .= "           THEN \n";
            $sql .= "               0 \n";
            $sql .= "           ELSE \n";
            $sql .= "               t_bill_d.payment_this \n";
            $sql .= "       END \n";
            $sql .= "   ) \n";
            $sql .= "   AS payment_this, \n";
            $sql .= "   t_bill_d.payment_extraction_s, \n";
            $sql .= "   t_bill_d.payment_extraction_e \n";
            $sql .= "FROM \n";
            $sql .= "   ( \n";
            $sql .= "       SELECT \n";
            $sql .= "           MAX(bill_id) AS bill_id \n";
            $sql .= "       FROM \n";
            $sql .= "           t_bill \n";
            $sql .= "       WHERE \n";
            $sql .= "           t_bill.claim_id = $search_claim_id \n";
            $sql .= "       AND \n";
            $sql .= "           t_bill.shop_id = $shop_id \n";
            $sql .= "   ) \n";
            $sql .= "   AS t_bill_max \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill_max.bill_id = t_bill_d.bill_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill_d.client_id = $search_claim_id \n";
            $sql .= "AND \n";
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            $num  = pg_num_rows($res);

            // ����ǡ�����������
            if ($num > 0){

                $search_bill_amount   = pg_fetch_result($res, 0, 0);    // �����
                $payment_extraction_s = pg_fetch_result($res, 0, 1);    // ��ʧͽ�����д��֡ʳ��ϡ�
                $payment_extraction_e = pg_fetch_result($res, 0, 2);    // ��ʧͽ�����д��֡ʽ�λ��

                // ����������
                $payment_extraction_s = ($payment_extraction_s == null) ? "2005-01-01" : $payment_extraction_s;

                // �������Ƥ������֤˳������볺�������谸�������ID�������ֹ�����
                $sql  = "SELECT \n";
                $sql .= "   t_bill.bill_id, \n";
                $sql .= "   t_bill.bill_no, \n";
                $sql .= "   ( \n";
                $sql .= "       CASE \n";
                $sql .= "           WHEN \n";
                $sql .= "               t_bill_d.payment_this < 0 \n";
                $sql .= "           THEN \n";
                $sql .= "               0 \n";
                $sql .= "           ELSE \n";
                $sql .= "               t_bill_d.payment_this \n";
                $sql .= "       END \n";
                $sql .= "   ) \n";
                $sql .= "   AS payment_this \n";
                $sql .= "FROM \n";
                $sql .= "   t_bill \n";
                $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
                $sql .= "WHERE \n";
                $sql .= "   t_bill.claim_id = $search_claim_id \n";
                $sql .= "AND \n";
                $sql .= "   t_bill.shop_id = $shop_id \n";
                $sql .= "AND \n";
                $sql .= "   t_bill.collect_day > '$payment_extraction_s' \n";
                $sql .= ($payment_extraction_e != null) ? "AND \n" : null;
                $sql .= ($payment_extraction_e != null) ? "   t_bill.collect_day <= '$payment_extraction_e' \n" : null;
                $sql .= "AND \n";
                $sql .= "   t_bill_d.bill_data_div = '0' \n";
                $sql .= "ORDER BY \n";
                $sql .= "   t_bill.bill_no DESC \n";    // ��ɼ��2�礢����α��޽���
                $sql .= ";";
                $res  = Db_Query($db_con, $sql);
                $num  = pg_num_rows($res);

                if ($num > 0){
                    $search_bill_id     = pg_fetch_result($res, 0, 0);      // �����ID
                    $search_bill_no     = pg_fetch_result($res, 0, 1);      // �����ֹ�
//                    $search_bill_amount = pg_fetch_result($res, 0, 2);      // �����
                    $bill_found_flg     = true;
                }

            }

        }
*/

            // ���������谸�Ǻǿ��������ǡ��������
            $sql  = "SELECT \n";
            $sql .= "   t_bill.bill_id, \n";
            $sql .= "   t_bill.bill_no, \n";
            $sql .= "   t_bill_d.payment_this \n";
            $sql .= "FROM \n";
            $sql .= "   ( \n";
            $sql .= "       SELECT \n";
            $sql .= "           MAX(bill_id) AS bill_id \n";
            $sql .= "       FROM \n";
            $sql .= "           t_bill \n";
            $sql .= "       WHERE \n";
            $sql .= "           t_bill.claim_id = $search_claim_id \n";
            $sql .= "       AND \n";
            $sql .= "           t_bill.shop_id = ".$_SESSION["client_id"]." \n";
            $sql .= "   ) \n";
            $sql .= "   AS t_bill_max \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill_max.bill_id = t_bill_d.bill_id \n";
            $sql .= "   INNER JOIN t_bill   ON t_bill_d.bill_id = t_bill.bill_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill_d.client_id = $search_claim_id \n";
            $sql .= "AND \n";
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= ";"; 
            $res  = Db_Query($db_con, $sql);
            $num  = pg_num_rows($res);

            if ($num > 0){
                $search_bill_id     = pg_fetch_result($res, 0, 0);      // �����ID
                $search_bill_no     = pg_fetch_result($res, 0, 1);      // �����ֹ�
                $search_bill_amount = pg_fetch_result($res, 0, 2);      // �����
                $bill_found_flg     = true; 
            }

        }

        // ����ǡ��������ե饰��true�ξ��
        if ($bill_found_flg == true){
            $set_bill_data["hdn_bill_id"][$claim_search_row]        = $search_bill_id;
            $set_bill_data["form_bill_no"][$claim_search_row]       = $search_bill_no;
            $set_bill_data["form_bill_amount"][$claim_search_row]   = $search_bill_amount;
        }else{
            $set_bill_data["hdn_bill_id"][$claim_search_row]        = "";
            $set_bill_data["form_bill_no"][$claim_search_row]       = "";
            $set_bill_data["form_bill_amount"][$claim_search_row]   = "";
        }
        $form->setConstants($set_bill_data);

    }

    // hidden�������踡�����ֹ�򥯥ꥢ
    $hdn_clear_claim["hdn_claim_search_flg[$claim_search_row]"] = "";
    $form->setConstants($hdn_clear_claim);

}


/****************************/
// ��۹�׻��н���
/****************************/
// ��۹�׻��Хե饰��true���ޤ�������ܥ��󤬲������줿���
if ($calc_flg == true || $_POST["form_verify_btn"] != null){

    // �Կ�ʬ�ʺ���ѹԴޤ�˥롼��
    for ($i=0; $i<$max_row; $i++){

        // ���������ˤ���Ԥϥ��롼
        if (!in_array($i, $ary_del_row_history)){

            // ���Ƥζ�ۤ�û����Ƥ���
            $total_amount += ($_POST["form_trade"][$i] != "35") ? $_POST["form_amount"][$i] : null; 

            // ���Ƥμ������û����Ƥ���
            $total_rebate += ($_POST["form_trade"][$i] == "35") ? $_POST["form_amount"][$i] : null; 
            $total_rebate += $_POST["form_rebate"][$i];

            // ��� 
            $total_payin  += $_POST["form_amount"][$i];
            $total_payin  += $_POST["form_rebate"][$i];

        }

    }

    // hidden�ζ�۹�ץե饰����
    // ��ۤι�פ�ե�����˥��å�
    $amount_sum_data["calc_flg"]            = "";
    $amount_sum_data["form_amount_total"]   = ($total_amount != null) ? number_format($total_amount) : 0;
    $amount_sum_data["form_rebate_total"]   = ($total_rebate != null) ? number_format($total_rebate) : 0;
    $amount_sum_data["form_payin_total"]    = ($total_payin  != null) ? number_format($total_payin)  : 0;
    $form->setConstants($amount_sum_data);

}


/****************************/
// �ե�����ѡ������
/****************************/
// �إå�����󥯥ܥ���
$ary_h_btn_list = array("�Ȳ��ѹ�" => "./1-2-403.php", "������" => "./1-2-402.php");
Make_H_Link_Btn($form, $ary_h_btn_list, 2);

// �����ʬ
$select_value_trade = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (32, 33, 34, 36, 37, 38) ");
$form->addElement("select", "form_trade_clt_set", "", $select_value_trade, $g_form_option_select);

// ������
$text = null;
$text[] =& $form->createElement("text", "y", "",
    "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
     onkeyup=\"changeText(this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]',4)\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]','form_payin_date_clt_set[d]')\"
     onBlur=\"blurForm(this)\""
);
$text[] =& $form->createElement("static", "", "", "-");
$text[] =& $form->createElement("text", "m", "",
    "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
     onkeyup=\"changeText(this.form,'form_payin_date_clt_set[m]','form_payin_date_clt_set[d]',2)\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]','form_payin_date_clt_set[d]')\"
     onBlur=\"blurForm(this)\""
);
$text[] =& $form->createElement("static", "", "", "-");
$text[] =& $form->createElement("text", "d", "",
    "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
     onFocus=\"onForm_today(this,this.form,'form_payin_date_clt_set[y]','form_payin_date_clt_set[m]','form_payin_date_clt_set[d]')\"
     onBlur=\"blurForm(this)\""
);
$form->addGroup($text, "form_payin_date_clt_set", "");

// ���
$select_value_bank  = Make_Ary_Bank($db_con);
$attach_html        = "��";
$obj_bank_select =& $form->addElement("hierselect", "form_bank_clt_set", "", "", $attach_html);
$obj_bank_select->setOptions($select_value_bank);

// ����۹��
$form->addElement(
    "text", "form_amount_total", "",
    "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\"
     readonly'"
);

// ��������
$form->addElement(
    "text", "form_rebate_total", "",
    "size=\"16\" maxLength=\"18\" 
     style=\"color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" 
     readonly"
);

// ���
$form->addElement(
    "text", "form_payin_total", "",
    "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\"
     readonly'"
);

// hidden
$form->addElement("hidden", "hdn_clt_set_flg", null, null);             // �������ܥ��󲡲��ե饰
$form->addElement("hidden", "hdn_max_row", null, null);                 // ����Կ�
$form->addElement("hidden", "hdn_add_row_flg", null, null);             // ���ɲåե饰
$form->addElement("hidden", "hdn_del_row_no", null, null);              // ������ֹ�
$form->addElement("hidden", "hdn_calc_flg", null, null);                // ��۹�׻��Хե饰
for ($i=0; $i<count($ary_del_row_history); $i++){
    $form->addElement("hidden", "hdn_ary_del_rows[$i]", null, null);    // ������ֹ�����   # ���������ʬ��������
}

// ���顼���å����ѥե�����
$form->addElement("text", "err_noway_forms", null, null);               // 1�԰ʾ����Ϥ����뤫
$form->addElement("text", "err_illegal_verify", null, null);            // ����POST
// ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
for ($i=0, $j=1; $i<$max_row; $i++){
    // ���������ˤ���Ԥϥ��롼
    if (!in_array($i, $ary_del_row_history)){
        $form->addElement("text", "err_claim1[$j]", null, null);        // ������1
        $form->addElement("text", "err_claim2[$j]", null, null);        // ������2
        $form->addElement("text", "err_payin_date1[$j]", null, null);   // ������1
        $form->addElement("text", "err_payin_date2[$j]", null, null);   // ������2
        $form->addElement("text", "err_payin_date3[$j]", null, null);   // ������3
        $form->addElement("text", "err_payin_date4[$j]", null, null);   // ������4
        $form->addElement("text", "err_payin_date5[$j]", null, null);   // ������5
        $form->addElement("text", "err_payin_date6[$j]", null, null);   // ������6
        $form->addElement("text", "err_trade[$j]", null, null);         // �����ʬ
        $form->addElement("text", "err_bank[$j]", null, null);          // ���
        $form->addElement("text", "err_amount1[$j]", null, null);       // ���1
        $form->addElement("text", "err_amount2[$j]", null, null);       // ���2
        $form->addElement("text", "err_rebate[$j]", null, null);        // �����
        $form->addElement("text", "err_limit_date1[$j]", null, null);   // �������1
        $form->addElement("text", "err_limit_date2[$j]", null, null);   // �������2
        $form->addElement("text", "err_limit_date3[$j]", null, null);   // �������3
    }
    $j++;
}


/****************************/
// �����襳���ɤ��Խ���������ǧ���̤إܥ��󤬲������줿�����н����
/****************************/
// �����ǧ���̤إܥ��󤬲����줿�����������踡���ե饰�����true��������
if ($_POST["form_verify_btn"] != null && in_array(true, $_POST["hdn_claim_search_flg"])){

    // �����踡���Կ������
    $search_key = array_search(true, $_POST["hdn_claim_search_flg"]);

    // hidden��������ID����γ���������������ID����Ǽ����Ƥ�����
    if ($_POST["hdn_claim_id"][$search_key] != null){
        // hidden�˳�Ǽ����Ƥ���������ID��POST���줿�����襳���ɤ�������������å�
        $sql  = "SELECT ";
        $sql .= "   client_id ";
        $sql .= "FROM ";
        $sql .= "   t_client ";
        $sql .= "WHERE ";
        $sql .= "   client_id = ".$_POST["hdn_claim_id"][$search_key]." ";
        $sql .= "AND "; 
        $sql .= "   client_cd1 = '".$_POST["form_claim"][$search_key]["cd1"]."' ";
        $sql .= "AND "; 
        $sql .= "   client_cd2 = '".$_POST["form_claim"][$search_key]["cd2"]."' ";
        $sql .= "AND "; 
        $sql .= "   client_div = '3' ";
//        $sql .= "AND "; 
//        $sql .= "   state = '1' ";
        $sql .= "AND "; 
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";"; 
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        // ��̤�����POST�ե饰��
        $illegal_verify_flg = ($num > 0) ? false : true; 
    // hidden��������ID����γ���������������ID����Ǽ����Ƥ��ʤ����
    }else{  
        // ����POST�ե饰��true��
        $illegal_verify_flg = true; 
    }

    // ����POST�ե饰true�ξ��ϥ��顼�򥻥å�
    if ($illegal_verify_flg == true){
        $form->setElementError("err_illegal_verify", "���������������� �����ǧ���̤إܥ��� ��������ޤ�����<br>������ľ���Ƥ���������");
    }

}


/****************************/
// ���顼�����å� - PHP
/****************************/
// �����ǧ���̤إܥ��󤬲������줿���
if ($_POST["form_verify_btn"] != null){

    /****************************/
    // �ǡ����������å�
    /****************************/
    // ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
    for ($i=0, $j=1; $i<$max_row; $i++){

        // ���������ˤ���Ԥϥ��롼
        if (!in_array($i, $ary_del_row_history)){

            // �����å���������������פ��͡�POST�ǡ������ѿ���
            $post_claim_id[$i]      = $_POST["hdn_claim_id"][$i];
            $post_claim_cd1[$i]     = $_POST["form_claim_cd"][$i]["cd1"];
            $post_claim_cd2[$i]     = $_POST["form_claim_cd"][$i]["cd2"];
            $post_claim_cname[$i]   = $_POST["form_claim_cname"][$i];
            $post_payin_date_y[$i]  = $_POST["form_payin_date"][$i]["y"];
            $post_payin_date_m[$i]  = $_POST["form_payin_date"][$i]["m"];
            $post_payin_date_d[$i]  = $_POST["form_payin_date"][$i]["d"];
            $post_trade[$i]         = $_POST["form_trade"][$i];
            $post_bank_id[$i]       = $_POST["form_bank_$i"][0];
            $post_b_bank_id[$i]     = $_POST["form_bank_$i"][1];
            $post_account_id[$i]    = $_POST["form_bank_$i"][2];
            $post_bill_no[$i]       = $_POST["form_bill_no"][$i];
            $post_bill_amount[$i]   = $_POST["form_bill_amount"][$i];
            $post_amount[$i]        = $_POST["form_amount"][$i];
            $post_rebate[$i]        = $_POST["form_rebate"][$i];
            $post_limit_date_y[$i]  = $_POST["form_limit_date"][$i]["y"];
            $post_limit_date_m[$i]  = $_POST["form_limit_date"][$i]["m"];
            $post_limit_date_d[$i]  = $_POST["form_limit_date"][$i]["d"];
            $post_bill_paper_no[$i] = $_POST["form_bill_paper_no"][$i];
            $post_note[$i]          = $_POST["form_note"][$i];

            // �����ԤΥե������1�ĤǤ����Ϥ�����Х��顼�����å���Ԥ��ʰ�������ǽ�ʥե�����Ͻ�����
            if ($post_claim_cd1[$i]     != null ||
                $post_claim_cd2[$i]     != null ||
                $post_claim_cname[$i]   != null ||
                $post_bill_no[$i]       != null ||
                $post_bill_amount[$i]   != null ||
                $post_amount[$i]        != null ||
                $post_rebate[$i]        != null ||
                $post_limit_date_y[$i]  != null ||
                $post_limit_date_m[$i]  != null ||
                $post_limit_date_d[$i]  != null ||
                $post_bill_paper_no[$i] != null ||
                $post_note[$i]          != null)
            {

                /*** ������ ***/
                // ��ɬ�ܥ����å�
                if ($post_claim_cd1[$i] == null && $post_claim_cd2[$i] == null && $post_claim_cname[$i] == null){
                    // ���顼�򥻥å�
                    $form->setElementError("err_claim1[$j]", $j."���ܡ������� ��ɬ�ܤǤ���");
                    $err_claim_flg[$i] = true;
                }

                /*** ������ ***/
                // ��ɬ�ܥ����å�
                if ($post_payin_date_y[$i] == null || $post_payin_date_m[$i] == null || $post_payin_date_d[$i] == null){
                    // ���顼�򥻥å�
                    $form->setElementError("err_payin_date1[$j]", $j."���ܡ������� ��ɬ�ܤǤ���");
                    $err_payin_date_flg[$i] = true;
                }
                // �����ͥ����å�
                if ($err_payin_date_flg[$i] != true &&
                    (!ereg("^[0-9]+$", $post_payin_date_y[$i]) ||
                     !ereg("^[0-9]+$", $post_payin_date_m[$i]) ||
                     !ereg("^[0-9]+$", $post_payin_date_d[$i]))
                ){
                    $form->setElementError("err_payin_date2[$j]", $j."���ܡ������� �����դ������ǤϤ���ޤ���");
                    $err_payin_date_flg[$i] = true;
                }
                // �����դȤ��Ƥ������������å�
                if ($err_payin_date_flg[$i] != true){
                    // ���դȤ��ƥ��顼�ξ��
                    if(!checkdate((int)$post_payin_date_m[$i], (int)$post_payin_date_d[$i], (int)$post_payin_date_y[$i])){
                        // ���顼�򥻥å�
                        $form->setElementError("err_payin_date2[$j]", $j."���ܡ������� �����դ������ǤϤ���ޤ���");
                        $err_payin_date_flg[$i] = true;
                    }
                }
                // �������ƥ೫�������������å�
                if ($err_payin_date_flg[$i] != true){ 
                    $chk_res = Sys_Start_Date_Chk($post_payin_date_y[$i], $post_payin_date_m[$i], $post_payin_date_d[$i], "������");
                    if ($chk_res != null){
                        // ���顼�򥻥å�
                        $form->setElementError("err_payin_date6[$j]", $j."���ܡ�".$chk_res);
                        $err_payin_date_flg[$i] = true;
                    }
                }
                // ��̤�����դ����Ϥ���Ƥ��ʤ��������å�
                if ($err_payin_date_flg[$i] != true){ 
                    // ̤�����դξ��
                    $post_payin_date_y2[$i] = str_pad($post_payin_date_y[$i], 4, "0", STR_PAD_LEFT);
                    $post_payin_date_m2[$i] = str_pad($post_payin_date_m[$i], 2, "0", STR_PAD_LEFT);
                    $post_payin_date_d2[$i] = str_pad($post_payin_date_d[$i], 2, "0", STR_PAD_LEFT);
                    if (date("Y-m-d") < $post_payin_date_y2[$i]."-".$post_payin_date_m2[$i]."-".$post_payin_date_d2[$i]){
                        // ���顼�򥻥å�
                        $form->setElementError("err_payin_date3[$j]", $j."���ܡ������� ��̤������դˤʤäƤ��ޤ���");
                        $err_payin_date_flg[$i] = true;
                    }
                }
                // ���ǿ��η�������������դ����Ϥ���Ƥ��ʤ��������å�
                if ($err_payin_date_flg[$i] != true){
                    // �ǿ��η�����������
                    $sql  = "SELECT ";
                    $sql .= "   to_date(MAX(close_day), 'YYYY-MM-DD') AS close_day ";
                    $sql .= "FROM ";
                    $sql .= "   t_sys_renew ";
                    $sql .= "WHERE ";
                    $sql .= "   renew_div = '2' ";
                    $sql .= "AND ";
                    $sql .= "   shop_id = $shop_id ";
                    $sql .= ";";
                    $res  = Db_Query($db_con, $sql);
                    $num  = pg_num_rows($res);
                    $last_monthly_renew_date = ($num == 1) ? pg_fetch_result($res, 0) : null;
                    // ���������������
                    if ($last_monthly_renew_date != null){
                        // �ǽ��η���������������դξ��
                        $post_payin_date_y2 = str_pad($post_payin_date_y[$i], 4, "0", STR_PAD_LEFT);
                        $post_payin_date_m2 = str_pad($post_payin_date_m[$i], 2, "0", STR_PAD_LEFT);
                        $post_payin_date_d2 = str_pad($post_payin_date_d[$i], 2, "0", STR_PAD_LEFT);
                        if ($post_payin_date_y2."-".$post_payin_date_m2."-".$post_payin_date_d2 <= $last_monthly_renew_date){
                            // ���顼�򥻥å�
                            $form->setElementError("err_payin_date4[$j]", $j."���ܡ������� ������η�������������դ����Ϥ���Ƥ��ޤ���");
                            $err_payin_date_flg[$i] = true;
                        }
                    }
                }
                // ������������������������դ����Ϥ���Ƥ��ʤ��������å�
                if ($err_payin_date_flg[$i] != true && $post_claim_id[$i] != null && $illegal_verify_flg != true){
                    // �ǿ�����������������
                    $sql  = "SELECT ";
                    $sql .= "   MAX(t_bill_d.bill_close_day_this) ";
                    $sql .= "FROM ";
                    $sql .= "   t_bill ";
                    $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id ";
                    $sql .= "WHERE ";
                    //aoyama-n 2009-07-27
                    #$sql .= "   t_bill.claim_id = $post_claim_id[$i] ";
                    $sql .= "   t_bill_d.claim_div = '1' ";
                    $sql .= "AND ";
                    $sql .= "   t_bill.claim_id = $post_claim_id[$i] ";
                    $sql .= "AND "; 
                    $sql .= "   t_bill_d.client_id = $post_claim_id[$i] ";
                    $sql .= "AND "; 
                    $sql .= "   t_bill.shop_id = $shop_id ";
                    $sql .= ";";
                    $res  = Db_Query($db_con, $sql);
                    $num  = pg_num_rows($res);
                    $last_close_date[$i] = ($num > 0) ? pg_fetch_result($res, 0) : null; 
                    // ������������
                    if ($last_close_date[$i] != null){
                        // ���Ϥ��줿����������������������������ξ��
                        if ($post_payin_date_y[$i]."-".$post_payin_date_m[$i]."-".$post_payin_date_d[$i] <= $last_close_date[$i]){
                            // ���顼�򥻥å�
                            $form->setElementError("err_payin_date5[$j]", $j."���ܡ������� �����������Ѥ����դ����Ϥ���Ƥ��ޤ���<br>���������ѹ����뤫�������������Ʋ�������");
                            $err_payin_date_flg[$i] = true;
                        }
                    }
                }

                /*** �����ʬ ***/
                // ��ɬ�ܥ����å�
                if ($post_trade[$i] == null){
                    // ���顼�򥻥å�
                    $form->setElementError("err_trade[$j]", $j."���ܡ������ʬ ��ɬ�ܤǤ���");
                }

                /*** ��� ***/
				//-- 2009/06/15 ����No.4 �ѹ�
				// �����ʬ����34:�껦�װʳ��ξ�硢��Ԥ�ɬ�ܥ����å�
                // ��ɬ�����ϥ����å�
                if ($post_trade[$i] != 34 && 
						($post_bank_id[$i] == null || $post_b_bank_id[$i] == null || $post_account_id[$i] == null)){
                //if ($post_bank_id[$i] == null || $post_b_bank_id[$i] == null || $post_account_id[$i] == null){
                    $form->setElementError("err_bank[$j]", $j."���ܡ���ԡ���Ź�������ֹ� ��ɬ�ܤǤ���");
                    $err_bank_flg[$i] = true; 
                } 
				//-- 2009/06/16 ����No.4 �ɲ�
				// �����ʬ����34:�껦�פξ�硢��Ԥ����ϥ����å�
				if ($post_trade[$i] == 34 &&
						($post_bank_id[$i] != null || $post_b_bank_id[$i] != null || $post_account_id[$i] != null)){
					$form->setElementError("err_bank[$j]", $j."���ܡ������ʬ �� 34:�껦 �ξ�硢��ԡ���Ź�������ֹ� ������Ǥ��ޤ���");
				}

                /*** �����ֹ桦����� ***/
                // ��ɬ�ܥ����å�
                if ($err_claim_flg != true && ($post_bill_no[$i] == null || $post_bill_amount[$i] == null)){
                    // ���顼�򥻥å�
                    $form->setElementError("err_claim2[$j]", $j."���ܡ�����ǡ����Τʤ� ������ �����򤵤�Ƥ��ޤ���");
                    $err_bill_no_flg[$i] = true;
                }

                /*** ��� ***/
                // ��ɬ�ܥ����å�
                if ($post_amount[$i] == null){
                    // ���顼�򥻥å�
                    $form->setElementError("err_amount1[$j]", $j."���ܡ���� ��ɬ�ܤǤ���");
                    $err_amount[$i] = true;
                }
                // �����ͥ����å�
                if ($err_amount[$i] != true && !ereg("^[-]?[0-9]+$", $post_amount[$i])){
                    // ���顼�򥻥å�
                    $form->setElementError("err_amount2[$j]", $j."���ܡ���� �Ͽ��ͤΤ����ϲ�ǽ�Ǥ���");
                    $err_amount[$i] = true;
                }

                /*** ����� ***/
                // �����ͥ����å�
                if ($post_rebate[$i] != null && !ereg("^[-]?[0-9]+$", $post_rebate[$i])){
                    // ���顼�򥻥å�
                    $form->setElementError("err_rebate[$j]", $j."���ܡ������ �Ͽ��ͤΤ����ϲ�ǽ�Ǥ���");
                    $err_rebate[$i] = true;
                }

                /*** ������� ***/
                // ������դ�ɬ�ܥ����å��ʼ����ʬ���������λ���
                if ($post_trade[$i] == 33 &&
                    ($post_limit_date_y[$i] == null &&
                     $post_limit_date_m[$i] == null &&
                     $post_limit_date_d[$i] == null)
                ){
                    $form->setElementError("err_limit_date1[$j]", $j."���ܡ������ʬ �� 33:������� �ξ�硢������� ��ɬ�ܤǤ���");
                    $err_limit_date[$i] = true;
                }
                // �����դȤ��������������å�
                if ($err_limit_date[$i] != true &&
                    ($post_limit_date_y[$i] != null ||
                     $post_limit_date_m[$i] != null ||
                     $post_limit_date_d[$i] != null)
                ){
                    if(!checkdate((int)$post_limit_date_m[$i], (int)$post_limit_date_d[$i], (int)$post_limit_date_y[$i])){
                        $form->setElementError("err_limit_date2[$j]", $j."���ܡ�������� �����դ������ǤϤ���ޤ���");
                        $err_limit_date[$i] = true;
                    }
                }
                // �������ƥ೫�������������å�
                if ($err_limit_date[$i] != true){ 
                    $chk_res = Sys_Start_Date_Chk($post_limit_date_y[$i], $post_limit_date_m[$i], $post_limit_date_d[$i], "�������");
                    if ($chk_res != null){
                        // ���顼�򥻥å�
                        $form->setElementError("err_limit_date3[$j]", $j."���ܡ�".$chk_res);
                        $err_limit_date[$i] = true;
                    }
                }

            // ���Ϥ�̵���Ԥξ��
            }else{

                // ���Ϥ�̵���Ԥι��ֹ��������������Ƥ���
                $ary_noway_forms[] = $i;

            }

            // �ºݤ�ɽ������Ƥ���ʺ������Ƥ��ʤ��˹Կ���������뤿����ֹ��������������Ƥ���
            $ary_all_forms[] = $i;

            // ���ֹ楫���󥿡ʥ��顼��å������ιԿ�ɽ���ѡ�
            $j++;

        }

    }

    /*** �ǡ�����ʬ�����ϹԿ� ***/
    // ������0������å�
    // �ºݤ�ɽ������Ƥ���Կ��ȡ����Ϥ�̵���Կ���Ʊ�����
    if (count($ary_noway_forms) == count($ary_all_forms)){
        $form->setElementError("err_noway_forms", "����ǡ��� �����Ϥ��Ʋ�������");
    }

}


/****************************
// �����顼�����å���̽���
/****************************/
// �����ǧ���̤إܥ��󤬲������줿����������POST�ե饰��true�Ǥʤ����
if ($_POST["form_verify_btn"] != null && $illegal_verify_flg != true){

    /*** ��̽��� ***/
    // ���顼�Τ���ե�����Υե�����̾���Ǽ���뤿�����������
    $ary_all_err_forms = array();
    // ���顼�Τ���ե�����Υե�����̾������˳�Ǽ
    foreach ($form as $key1 => $value1){
        if ($key1 == "_errors"){
            foreach ($value1 as $key2 => $value2){
                $ary_all_err_forms[] = $key2;
            }
        }
    }
    // ���顼�����0��ξ��ϳ�ǧ�ե饰��true�ˤ���
    $verify_flg = (count($ary_all_err_forms) == 0) ? true : false;

}


/****************************/
// ��������ǽ�ե�����Ρ���ɬ�פʥǡ�����õ�
/****************************/
// ��ǧ���̥ե饰��true�ξ��
if ($verify_flg == true){

    // ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
    for ($i=0; $i<$max_row; $i++){

        // ���������ˤ���Ԥϥ��롼
        if (!in_array($i, $ary_del_row_history)){

            // ɬ�ܹ��ܤ����Ϥ��ʤ�����INSERT���ʤ��ԡ�
            if ($_POST["form_claim_cd"][$i]["cd1"] == null){
                // ��������ǽ�ե�����Ρ���ɬ�פʥǡ����򥯥ꥢ
                $clt_form_clear["form_payin_date"][$i]["y"] = "";
                $clt_form_clear["form_payin_date"][$i]["m"] = "";
                $clt_form_clear["form_payin_date"][$i]["d"] = "";
                $clt_form_clear["form_trade[$i]"]           = "";
                $clt_form_clear["form_bank_$i"][0]          = "";
                $clt_form_clear["form_bank_$i"][1]          = "";
                $clt_form_clear["form_bank_$i"][2]          = "";
                $form->setConstants($clt_form_clear);
            }

            // ������ID�Τ���Ԥξ��
            if ($_POST["hdn_claim_id"][$i] != null){
                // ɬ�פʥǡ����ʿ���̾��1,2�ˤ򥻥å�
                $pay_account_name_set["form_pay_name"][$i]      = stripslashes(htmlspecialchars($_POST["hdn_pay_name"][$i]));
                $pay_account_name_set["form_account_name"][$i]  = stripslashes(htmlspecialchars($_POST["hdn_account_name"][$i]));
                $form->setConstants($pay_account_name_set);
            }

        }

    }

}


/****************************/
// ��Ͽ������
/****************************/
// ����OK�ܥ��󲡲���
if ($_POST["form_ok_btn"] != null){

    /*** POST�ǡ������ѿ��� ***/
    // ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
    for ($i=0; $i<$max_row; $i++){

        // ���������ˤ���Ԥϥ��롼
        if (!in_array($i, $ary_del_row_history)){

            // �����ԤΥե������1�ĤǤ����Ϥ�������ʰ�������ǽ�ʥե�����Ͻ�����
            if ($_POST["form_claim_cd"][$i]["cd1"]  != null ||
                $_POST["form_claim_cd"][$i]["cd2"]  != null ||
                $_POST["form_claim_cname"][$i]      != null ||
                $_POST["form_bill_no"][$i]          != null ||
                $_POST["form_bill_amount"][$i]      != null ||
                $_POST["form_trade"][$i]            != null ||
                $_POST["form_amount"][$i]           != null ||
                $_POST["form_rebate"][$i]           != null ||
                $_POST["form_limit_date"][$i]["y"]  != null ||
                $_POST["form_limit_date"][$i]["m"]  != null ||
                $_POST["form_limit_date"][$i]["d"]  != null ||
                $_POST["form_bill_paper_no"][$i]    != null ||
                $_POST["form_note"][$i]             != null)
            {
                // POST�ǡ������ѿ�������
                $post_claim_id[$i]          = $_POST["hdn_claim_id"][$i];
                $post_claim_cd1[$i]         = $_POST["form_claim_cd"][$i]["cd1"];
                $post_claim_cd2[$i]         = $_POST["form_claim_cd"][$i]["cd2"];
                $post_claim_cname[$i]       = $_POST["form_claim_cname"][$i];
                $post_pay_name[$i]          = $_POST["hdn_pay_name"][$i];
                $post_account_name[$i]      = $_POST["hdn_account_name"][$i];
                if ($_POST["form_payin_date"][$i]["y"] != null ||
                    $_POST["form_payin_date"][$i]["m"] != null ||
                    $_POST["form_payin_date"][$i]["d"] != null)
                {
                    $post_payin_date_y[$i]  = str_pad($_POST["form_payin_date"][$i]["y"], 4, "0", STR_PAD_LEFT);
                    $post_payin_date_m[$i]  = str_pad($_POST["form_payin_date"][$i]["m"], 2, "0", STR_PAD_LEFT);
                    $post_payin_date_d[$i]  = str_pad($_POST["form_payin_date"][$i]["d"], 2, "0", STR_PAD_LEFT);
                    $post_payin_date[$i]    = $post_payin_date_y[$i]."-".$post_payin_date_m[$i]."-".$post_payin_date_d[$i];
                }else{
                    $post_payin_date[$i]    = null;
                }
                $post_trade[$i]             = $_POST["form_trade"][$i];
                $post_bank_id[$i]           = $_POST["form_bank_$i"][0];
                $post_b_bank_id[$i]         = $_POST["form_bank_$i"][1];
                $post_account_id[$i]        = $_POST["form_bank_$i"][2];
                $hdn_bill_id[$i]            = $_POST["hdn_bill_id"][$i];
                $post_amount[$i]            = $_POST["form_amount"][$i];
                $post_rebate[$i]            = $_POST["form_rebate"][$i];
                if ($_POST["form_limit_date"][$i]["y"] != null ||
                    $_POST["form_limit_date"][$i]["m"] != null ||
                    $_POST["form_limit_date"][$i]["d"] != null)
                {
                    $post_limit_date_y[$i]  = str_pad($_POST["form_limit_date"][$i]["y"], 4, "0", STR_PAD_LEFT);
                    $post_limit_date_m[$i]  = str_pad($_POST["form_limit_date"][$i]["m"], 2, "0", STR_PAD_LEFT);
                    $post_limit_date_d[$i]  = str_pad($_POST["form_limit_date"][$i]["d"], 2, "0", STR_PAD_LEFT);
                    $post_limit_date[$i]    = $post_limit_date_y[$i]."-".$post_limit_date_m[$i]."-".$post_limit_date_d[$i];
                }else{
                    $post_limit_date[$i]    = null;
                }
                $post_bill_paper_no[$i]     = $_POST["form_bill_paper_no"][$i];
                $post_note[$i]              = $_POST["form_note"][$i];

                // �ºݤ����Ϥ�����Ԥι��ֹ�Τߤ�������������Ƥ���
                $ary_insert_forms[] = $i;

            }

        }

    }

    /*** �ǿ�+1�������ֹ����� ***/
    $sql  = "SELECT ";
    $sql .= "   MAX(pay_no) ";
    $sql .= "FROM ";
    $sql .= "   t_payin_h ";
    $sql .= "WHERE ";
    $sql .= "   shop_id = $shop_id ";
    $sql .= ";"; 
    $res  = Db_Query($db_con, $sql);
    $payin_no = str_pad(pg_fetch_result($res, 0 ,0)+1, 8, "0", STR_PAD_LEFT);

    /****************************/
    // DB����
    /****************************/
    /*** �ȥ�󥶥�����󳫻� ***/
    Db_Query($db_con, "BEGIN;");

    // ���ϤΤ���Կ�ʬ�롼��
    foreach ($ary_insert_forms as $key => $i){

        /*** ����إå�INSERT ***/
        $sql  = "INSERT INTO \n";
        $sql .= "   t_payin_h \n";
        $sql .= "( \n";
        $sql .= "   pay_id, \n";
        $sql .= "   pay_no, \n";
        $sql .= "   pay_day, \n";
        $sql .= "   collect_staff_id, \n";
        $sql .= "   collect_staff_name, \n";
        $sql .= "   client_id, \n";
        $sql .= "   client_cd1, \n";
        $sql .= "   client_cd2, \n";
        $sql .= "   client_name, \n";
        $sql .= "   client_name2, \n";
        $sql .= "   client_cname, \n";
        $sql .= "   c_bank_cd, \n";
        $sql .= "   c_bank_name, \n";
        $sql .= "   c_b_bank_cd, \n";
        $sql .= "   c_b_bank_name, \n";
        $sql .= "   c_deposit_kind, \n";
        $sql .= "   c_account_no, \n";
        $sql .= "   claim_div, \n";
        $sql .= "   pay_name, \n";
        $sql .= "   account_name, \n";
        $sql .= "   bill_id, \n";
        $sql .= "   claim_cd1, \n";
        $sql .= "   claim_cd2, \n";
        $sql .= "   claim_cname, \n";
        $sql .= "   input_day, \n";
        $sql .= "   e_staff_id, \n";
        $sql .= "   e_staff_name, \n";
        $sql .= "   ac_staff_id, \n";
        $sql .= "   ac_staff_name, \n";
        $sql .= "   sale_id, \n";
        $sql .= "   renew_flg, \n";
        $sql .= "   renew_day, \n";
        $sql .= "   shop_id \n";
        $sql .= ") \n";
        $sql .= "VALUES \n";
        $sql .= "( \n";
        $sql .= "   (SELECT COALESCE(MAX(pay_id), 0)+1 FROM t_payin_h), \n";
        $sql .= "   '$payin_no', \n";
        $sql .= "   '$post_payin_date[$i]', \n";
        $sql .= "   NULL, \n";
        $sql .= "   NULL, \n";
        $sql .= "   $post_claim_id[$i], \n";
        $sql .= "   '$post_claim_cd1[$i]', \n";
        $sql .= "   '$post_claim_cd2[$i]', \n";
        $sql .= "   (SELECT client_name FROM t_client WHERE client_id = $post_claim_id[$i]), \n";
        $sql .= "   (SELECT client_name2 FROM t_client WHERE client_id = $post_claim_id[$i]), \n";
        $sql .= "   '$post_claim_cname[$i]', \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_bank.bank_cd FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_bank.bank_name FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       LEFT JOIN t_bank ON t_b_bank.bank_id = t_bank.bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_b_bank.b_bank_cd FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_b_bank.b_bank_name FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_account.deposit_kind FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT t_account.account_no FROM t_client \n";
        $sql .= "       LEFT JOIN t_account ON t_client.account_id = t_account.account_id \n";
        $sql .= "       LEFT JOIN t_b_bank ON t_account.b_bank_id = t_b_bank.b_bank_id \n";
        $sql .= "       WHERE t_client.client_id = $post_claim_id[$i] \n";
        $sql .= "   ), \n";
        $sql .= "   '1', \n";
        $sql .= "   '$post_pay_name[$i]', \n";
        $sql .= "   '$post_account_name[$i]', \n";
        $sql .= "   $hdn_bill_id[$i], \n";
        $sql .= "   '$post_claim_cd1[$i]', \n";
        $sql .= "   '$post_claim_cd2[$i]', \n";
        $sql .= "   '$post_claim_cname[$i]', \n";
        $sql .= "   NOW(), \n";
        $sql .= "   $staff_id, \n";
        $sql .= "   '".addslashes($staff_name)."', \n";
        $sql .= "   $staff_id, \n";
        $sql .= "   '".addslashes($staff_name)."', \n";
        $sql .= "   NULL, \n";
        $sql .= "   'f', \n";
        $sql .= "   NULL, \n";
        $sql .= "   $shop_id \n";
        $sql .= ") \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        // ���顼���ϥ�����Хå�
        if($res == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        /*** ����ǡ���INSERT ***/
        // ����إå�����Ͽ��������ID�����
        $sql  = "SELECT ";
        $sql .= "   pay_id ";
        $sql .= "FROM ";
        $sql .= "   t_payin_h ";
        $sql .= "WHERE ";
        $sql .= "   pay_no = '$payin_no' ";
        $sql .= "AND "; 
        $sql .= "   shop_id = $shop_id ";
        $sql .= ";"; 
        $res  = Db_Query($db_con, $sql);
        $payin_id[$i] = pg_fetch_result($res, 0);

        // INSERT�¹�
        $sql  = "INSERT INTO ";
        $sql .= "   t_payin_d ";
        $sql .= "( ";
        $sql .= "   pay_d_id, ";
        $sql .= "   pay_id, ";
        $sql .= "   trade_id, ";
        $sql .= "   amount, ";
        $sql .= "   bank_cd, ";
        $sql .= "   bank_name, ";
        $sql .= "   b_bank_cd, ";
        $sql .= "   b_bank_name, ";
        $sql .= "   account_id, ";
        $sql .= "   deposit_kind, ";
        $sql .= "   account_no, ";
        $sql .= "   payable_day, ";
        $sql .= "   payable_no, ";
        $sql .= "   note ";
        $sql .= ") ";
        $sql .= "VALUES ";
        $sql .= "( ";
        $sql .= "   (SELECT COALESCE(MAX(pay_d_id), 0)+1 FROM t_payin_d), ";
        $sql .= "   $payin_id[$i], ";
        $sql .= "   $post_trade[$i], ";
        $sql .= "   '$post_amount[$i]', ";
        $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_cd FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_name FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_cd FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_name FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
        $sql .= ($post_account_id[$i] != null) ? " $post_account_id[$i], " : " NULL, ";
        $sql .= ($post_account_id[$i] != null) ? " (SELECT deposit_kind FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
        $sql .= ($post_account_id[$i] != null) ? " (SELECT account_no FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
        $sql .= ($post_limit_date[$i] != null) ? " '$post_limit_date[$i]', " : " NULL, ";
        $sql .= ($post_bill_paper_no[$i] != null) ? " '$post_bill_paper_no[$i]', " : " NULL, ";
        $sql .= "   '$post_note[$i]' ";
        $sql .= ") ";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        // ���顼���ϥ�����Хå�
        if($res == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        // ����������Ϥ���Ƥ�����Ϥ����1�쥳����INSERT
        if ($post_rebate[$i] != null){
            $sql  = "INSERT INTO ";
            $sql .= "   t_payin_d ";
            $sql .= "( ";
            $sql .= "   pay_d_id, ";
            $sql .= "   pay_id, ";
            $sql .= "   trade_id, ";
            $sql .= "   amount, ";
            $sql .= "   bank_cd, ";
            $sql .= "   bank_name, ";
            $sql .= "   b_bank_cd, ";
            $sql .= "   b_bank_name, ";
            $sql .= "   account_id, ";
            $sql .= "   deposit_kind, ";
            $sql .= "   account_no, ";
            $sql .= "   payable_day, ";
            $sql .= "   payable_no, ";
            $sql .= "   note ";
            $sql .= ") ";
            $sql .= "VALUES ";
            $sql .= "( ";
            $sql .= "   (SELECT COALESCE(MAX(pay_d_id), 0)+1 FROM t_payin_d), ";
            $sql .= "   $payin_id[$i], ";
            $sql .= "   35, ";
            $sql .= "   '$post_rebate[$i]', ";
            $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_cd FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_bank_id[$i] != null) ? " (SELECT bank_name FROM t_bank WHERE bank_id = $post_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_cd FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_b_bank_id[$i] != null) ? " (SELECT b_bank_name FROM t_b_bank WHERE b_bank_id = $post_b_bank_id[$i]), " : " NULL, ";
            $sql .= ($post_account_id[$i] != null) ? " $post_account_id[$i], " : " NULL, ";
            $sql .= ($post_account_id[$i] != null) ? " (SELECT deposit_kind FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
            $sql .= ($post_account_id[$i] != null) ? " (SELECT account_no FROM t_account WHERE account_id = $post_account_id[$i]), " : " NULL, ";
            $sql .= "   NULL, ";
            $sql .= "   NULL, ";
            $sql .= "   NULL ";
            $sql .= ") ";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            // ���顼���ϥ�����Хå�
            if($res == false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

        }

        /* �����ֹ�ơ��֥빹�� **/
        // ľ�Ĥξ��
        if ($group_kind == 2){
            $sql  = "INSERT INTO ";
            $sql .= "   t_payin_no_serial ";
            $sql .= "( ";
            $sql .= "   pay_no ";
            $sql .= ") ";
            $sql .= "VALUES ";
            $sql .= "( ";
            $sql .= "   '$payin_no' ";
            $sql .= ") ";
            $sql .= ";"; 
            $res  = Db_Query($db_con, $sql);
            // ���顼���ϥ�����Хå�
            if($res == false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;   
            }       
        }

        /*** �����ֹ��û����Ʒ���� ***/
        $payin_no = str_pad($payin_no+1, 8, "0", STR_PAD_LEFT);

    }

    /*** �ȥ�󥶥�����󴰷� ***/
    Db_Query($db_con, "COMMIT;");
    
    /*** �ڡ������� ***/
    // ��λ���̤إڡ�������
    header("Location: ./1-2-408.php");

}


/****************************/
// ���ѥե�����ѡ��������ɽ��
/****************************/
// ���쥯�ȥܥå��������ƥ���������
$select_value_trade     = null;
$select_value_trade     = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (32, 33, 34, 36, 37, 38) ");
$select_value_bank  = Make_Ary_Bank($db_con);
// ���hierselect��Ϣ��html
$attach_html        = "<br>";

// �ե�����θ����ܤ�static�äݤ����뤿���CSS���ѿ�������Ƥ�����Ĺ���Τǡ�
$style = "color: #585858; border: #ffffff 1px solid; background-color: #ffffff;";


// ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
for ($i=0, $row_num=0; $i<$max_row; $i++){

    // ���������ˤ���Ԥϥ��롼
    if (!in_array($i, $ary_del_row_history)){

        $row_num++;

        // ��ǧ���̥ե饰��true�Ǥʤ����
        if ($verify_flg != true){

            // �����襳����
            $text = null;
            $text[] =& $form->addElement("text", "cd1", "",
                "size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
                 onChange=\"javascript:Change_Submit('hdn_claim_search_flg[$i]','#$row_num','true','form_claim_cd[$i][cd2]')\"
                 onkeyup=\"changeText(this.form,'form_claim_cd[$i][cd1]','form_claim_cd[$i][cd2]',6)\" ".$g_form_option."\""
            );
            $text[] =& $form->addElement("static", "", "", "-");
            $text[] =& $form->addElement("text", "cd2", "",
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onChange=\"javascript:Button_Submit('hdn_claim_search_flg[$i]','#$row_num','true')\" ".$g_form_option."\""
            );
            $form->addGroup($text, "form_claim_cd[$i]", "");

            // ������̾
            $text[] =& $form->addElement("text", "form_claim_cname[$i]", "",
                "size=\"34\" style=\"color : #000000; border : #ffffff 1px solid; background-color: #ffffff;\" readonly"
            );

            // ����̾��1
            $form->addElement("static", "form_pay_name[$i]", "", "");

            // ����̾��2
            $form->addElement("static", "form_account_name[$i]", "", "");

            //�������
            $form->addElement("link", "form_claim_search[$i]", "", "#", "����",
                "tabindex=\"-1\" 
                 onClick=\"javascript:return Open_SubWin_3('../dialog/1-0-250.php',
                   Array('form_claim_cd[$i][cd1]','form_claim_cd[$i][cd2]','form_claim_cname[$i]','hdn_claim_search_flg[$i]','hdn_claim_id[$i]'),
                   500, 450, '2-409', 1, $row_num);\""
            );

            // ������
            $text = null;
            $text[] =& $form->createElement("text", "y", "",
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]',4)\"
                 onFocus=\"onForm_today(this,this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]','form_payin_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_payin_date[$i][m]','form_payin_date[$i][d]',2)\"
                 onFocus=\"onForm_today(this,this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]','form_payin_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onFocus=\"onForm_today(this,this.form,'form_payin_date[$i][y]','form_payin_date[$i][m]','form_payin_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $form->addGroup($text, "form_payin_date[$i]", "");

            // �����ʬ
            $form->addElement("select", "form_trade[$i]", "", $select_value_trade, $g_form_option_select);

            // ���
            $obj_bank_select =& $form->addElement("hierselect", "form_bank_$i", "", "", $attach_html);
            $obj_bank_select->setOptions($select_value_bank);

            // �����ֹ�
            $form->addElement("text", "form_bill_no[$i]", "", "size=\"9\" maxlength=\"8\" style=\"$style\" readonly");

            // �����
            $form->addElement("text", "form_bill_amount[$i]", "",
                "class=\"money\" size=\"9\" maxlength=\"8\" style=\"text-align: right; $style\" readonly"
            );      

            // ��� 
            $form->addElement("text", "form_amount[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
            );      

            // �����
            $form->addElement("text", "form_rebate[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
            );      

            // �������
            $text = null;
            $text[] =& $form->createElement("text", "y", "",
                "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]',4)\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]','form_limit_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onkeyup=\"changeText(this.form,'form_limit_date[$i][m]','form_limit_date[$i][d]',2)\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]','form_limit_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "",
                "size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
                 onFocus=\"onForm_today(this,this.form,'form_limit_date[$i][y]','form_limit_date[$i][m]','form_limit_date[$i][d]')\"
                 onBlur=\"blurForm(this)\""
            );
            $form->addGroup($text, "form_limit_date[$i]", "");

            // ��������ֹ�
            $form->addElement("text", "form_bill_paper_no[$i]", "", "size=\"13\" maxLength=\"10\" style=\"$g_form_style\" $g_form_option\"");

            // ����
            $form->addElement("text", "form_note[$i]", "", "size=\"34\" maxLength=\"20\" $g_form_option\"");

            // ������
            $link_no = ($i+1 == $del_row_no) ? $row_num - 1 : $row_num;
            $form->addElement("link", "form_del_row[$i]", "", "#", "<font color=\"#fefefe\">���</font>",
                "tabindex=-1 onClick=\"javascript:Dialogue_3('������ޤ���', $i, 'hdn_del_row_no', $row_num); return false;\""
            );

            // hidden
            $form->addElement("hidden", "hdn_claim_search_flg[$i]", null, null);
            $form->addElement("hidden", "hdn_claim_id[$i]", null, null);        // �ƹԤ����򤵤�Ƥ���������ID
            $form->addElement("hidden", "hdn_bill_id[$i]", null, null);         // �ƹԤ����򤵤�Ƥ���������ID����������������ID
            $form->addElement("hidden", "hdn_pay_name[$i]", null, null);        // �ƹԤ����򤵤�Ƥ���������ο���̾��1
            $form->addElement("hidden", "hdn_account_name[$i]", null, null);    // �ƹԤ����򤵤�Ƥ���������ο���̾��2

        // ��ǧ���̥ե饰��true�ξ��
        }else{

            // �����襳����
            $text = null;
            $text[] =& $form->addElement("text", "cd1", "", "size=\"7\" maxLength=\"6\" style=\"$style\" readonly");
            $text[] =& $form->addElement("static", "", "", "-");
            $text[] =& $form->addElement("text", "cd2", "", "size=\"4\" maxLength=\"4\" style=\"$style\" readonly");
            $form->addGroup($text, "form_claim_cd[$i]", "");

            // ������̾
            $text[] =& $form->addElement("text", "form_claim_cname[$i]", "", "size=\"34\" style=\"$style\" readonly");

            // ����̾��1
            $form->addElement("static", "form_pay_name[$i]", "", "");

            // ����̾��2
            $form->addElement("static", "form_account_name[$i]", "", "");

            // ������
            $text = null;
            $text[] =& $form->createElement("text", "y", "", "size=\"4\" maxLength=\"4\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $form->addGroup($text, "form_payin_date[$i]", "");

            // �����ʬ
            $verify_freeze_data_trade = $form->addElement("select", "form_trade[$i]", "", $select_value_trade, $g_form_option_select);
            $verify_freeze_data_trade->freeze();

            // ���
            $verify_freeze_data_bank = $obj_bank_select =& $form->addElement("hierselect", "form_bank_$i", "", "", $attach_html);
            $obj_bank_select->setOptions($select_value_bank);
            $verify_freeze_data_bank->freeze();

            // �����ֹ�
            $form->addElement("text", "form_bill_no[$i]", "", "size=\"9\" maxlength=\"8\" style=\"$style\" readonly");

            // �����
            $form->addElement("text", "form_bill_amount[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $style\" readonly"
            );

            // ���
            $form->addElement("text", "form_amount[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $style\" readonly"
            );

            // �����
            $form->addElement("text", "form_rebate[$i]", "",
                "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $style\" readonly"
            );

            // �������
            $text = null;
            $text[] =& $form->createElement("text", "y", "", "size=\"4\" maxLength=\"4\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "m", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $text[] =& $form->createElement("static", "", "", "-");
            $text[] =& $form->createElement("text", "d", "", "size=\"2\" maxLength=\"2\" style=\"$style\" readonly");
            $form->addGroup($text, "form_limit_date[$i]", "");

            // ��������ֹ�
            $form->addElement("text", "form_bill_paper_no[$i]", "", "size=\"13\" maxLength=\"10\" style=\"$style\" readonly");

            // ����
            $form->addElement("text", "form_note[$i]", "", "size=\"34\" maxLength=\"20\" style=\"$style\" readonly");

            // hidden
            $form->addElement("hidden", "hdn_claim_id[$i]", null, null);        // �ƹԤ����򤵤�Ƥ���������ID
            $form->addElement("hidden", "hdn_bill_id[$i]", null, null);         // �ƹԤ����򤵤�Ƥ���������ID����������������ID
            $form->addElement("hidden", "hdn_pay_name[$i]", null, null);        // �ƹԤ����򤵤�Ƥ���������ο���̾��1
            $form->addElement("hidden", "hdn_account_name[$i]", null, null);    // �ƹԤ����򤵤�Ƥ���������ο���̾��2

        }

    }

}


/****************************/
// ���ѥե�����ѡ�������ʥܥ����
/****************************/
/* ��Ͽ��ǧ���̤Ǥϡ��ʲ��Υܥ����ɽ�����ʤ� */
if($verify_flg != true){

    // ������ñ�̥ܥ���
    $form->addElement("button", "form_trans_client_btn", "������ñ��", "onClick=\"location.href('1-2-402.php')\"");

    // ���ñ�̥ܥ���
    $form->addElement("button", "form_trans_bank_btn", "���ñ��", $g_button_color." onClick=\"location.href('1-2-409.php')\"");

    // �������ܥ���
    $form->addElement("button", "form_clt_set_btn", "�������", "onClick=\"javascript:Button_Submit('hdn_clt_set_flg','#','true')\"\"");

    // ���ɲåܥ���
    $form->addElement("button", "form_add_row_btn", "���ɲ�", "onClick=\"javascript:Button_Submit_1('hdn_add_row_flg', '#foot', 'true')\"");

    // ��ץܥ���
    $form->addElement("button", "form_calc_btn", "�硡��", "onClick=\"javascript:Button_Submit('hdn_calc_flg','#foot','true')\"");

    // �����ǧ���̤إܥ���
    $form->addElement("submit", "form_verify_btn", "�����ǧ���̤�", "$disabled");

}

/* ��Ͽľ��γ�ǧ���̤Τ�ɽ�� */
if ($verify_flg == true){

    // ����OK�ܥ���
    $form->addElement("button", "hdn_form_ok_btn", "����ϣ�", "onClick=\"Double_Post_Prevent2(this);\" $disabled");

    // ����OKhidden
    $form->addElement("hidden", "form_ok_btn");

    // ���ܥ���
    $form->addElement("button", "form_return_btn", "�ᡡ��", "onClick=\"javascript:SubMenu2('#')\"");
}


/****************************/
// ɽ����html����
/****************************/
// html���ѿ����
$html = null;

// ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
for ($i=0, $j=0; $i<$max_row; $i++){

    // ���������ˤ���Ԥ�ɽ�������ʤ�
    if (!in_array($i, $ary_del_row_history)){

        // ������ξ��ּ���
        $claim_state_print[$i] = Get_Client_State($db_con, $_POST["hdn_claim_id"][$i]);

        // �����踡���ե饰��true�ܥ롼�ץ����󥿤������踡���Ԥ�Ʊ�����
        // �ʥ����ɤ�ľ�����Ϥ��줿����� ��������ID���ޤ�hdn�����äƤʤ��Τǡ�
        if ($_POST["hdn_claim_search_flg"] != null && ($i == array_search("true", $_POST["hdn_claim_search_flg"]))){
            $claim_state_print[$i] = Get_Client_State($db_con, $search_claim_id);
        }

        // html����
        $html .= "<tr class=\"Result1\">\n";
        $html .= "<A NAME=\"".++$j."\"></A>\n";
        $html .= "  <td align=\"right\">".$j."</td>\n";                                                 // ���ֹ�
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_claim_cd[$i]"]]->toHtml();          // �����襳����1
        $html .= "      ".$claim_state_print[$i];
        // ��ǧ���̤Ǥʤ����
        if ($verify_flg != true){
        $html .= " (";
        $html .=        $form->_elements[$form->_elementIndex["form_claim_search[$i]"]]->toHtml();      // �����踡�����
        $html .= ")";
        }
        $html .= "      <br>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_claim_cname[$i]"]]->toHtml();       // ������̾��ά�Ρ�
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_pay_name[$i]"]]->toHtml();          // ����̾��1
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_account_name[$i]"]]->toHtml();      // ����̾��2
        $html .= "      <br>";
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_payin_date[$i]"]]->toHtml();        // ������
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_trade[$i]"]]->toHtml();             // �����ʬ
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bank_$i"]]->toHtml();               // ���
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bill_no[$i]"]]->toHtml();           // �����ֹ�
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_bill_amount[$i]"]]->toHtml();       // �����
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_amount[$i]"]]->toHtml();            // ���
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_rebate[$i]"]]->toHtml();            // �����
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_limit_date[$i]"]]->toHtml();        // �������
        $html .= "      <br>";
        $html .=        $form->_elements[$form->_elementIndex["form_bill_paper_no[$i]"]]->toHtml();     // ��������ֹ�
        $html .= "  </td>\n";
        $html .= "  <td>\n";
        $html .=        $form->_elements[$form->_elementIndex["form_note[$i]"]]->toHtml();              // ����
        $html .= "  </td>\n";
        // ��ǧ���̤Ǥʤ����
        if ($verify_flg != true){
        $html .= "  <td class=\"Title_Add\" align=\"center\">\n";
        $html .=        $form->_elements[$form->_elementIndex["form_del_row[$i]"]]->toHtml();           // ������
        $html .= "  </td>\n";
        }
        $html .= "</tr>\n";

    }

}


/****************************/
// �ƻҴط��Τ�������������å�
/****************************/
#2009-10-16 hashimoto-y
/*
if ($verify_flg != true){

    // ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
    for ($i=0, $j=0; $i<$max_row; $i++){

        // ���������ˤ���Ԥ�ɽ�������ʤ�
        if (!in_array($i, $ary_del_row_history)){

            // ���Ϥ��줿�����襳���ɤ�ID�����
            $sql  = "SELECT \n";
            $sql .= "   t_client.client_id \n";
            $sql .= "FROM \n";
            $sql .= "   t_client \n";
            $sql .= "   INNER JOIN t_claim ON t_client.client_id = t_claim.claim_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_client.client_cd1 = '".$_POST["form_claim_cd"][$i]["cd1"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_client.client_cd2 = '".$_POST["form_claim_cd"][$i]["cd2"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_client.client_div = '3' \n";
            $sql .= "AND \n";
            $sql .= "   t_client.shop_id = $shop_id \n";
            $sql .= ";"; 
            $res  = Db_Query($db_con, $sql);
            $num  = pg_num_rows($res);

            // �����ǡ�����������
            if ($num > 0){

                // �оݤ�������ID�����
                $target_claim_id = pg_fetch_result($res, 0, 0);

                // ���оݤ�������ID�פοƻҴط�������å�����
                $filiation_flg[] = Claim_Filiation_Chk($db_con, $target_claim_id);
            }else{  

                $filiation_flg[] = null; 

            }       

        }       

    }

}
*/


/****************************/
// HTML�إå�
/****************************/
$html_header = Html_Header($page_title);

/****************************/
// HTML�եå�
/****************************/
$html_footer = Html_Footer();

/****************************/
// ��˥塼����
/****************************/
$page_menu = Create_Menu_h("sale", "4");

/****************************/
// ���̥إå�������
/****************************/
$page_title .= Print_H_Link_Btn($form, $ary_h_btn_list);
$page_header = Create_Header($page_title);

// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form��Ϣ���ѿ���assign
$smarty->assign("form", $renderer->toArray());

// ����¾���ѿ���assign
$smarty->assign("var", array(
	"html_header"   => "$html_header",
	"page_menu"     => "$page_menu",
	"page_header"   => "$page_header",
	"html_footer"   => "$html_footer",
    "html"          => "$html",
    "verify_flg"    => "$verify_flg",
    "filiation_flg" => $filiation_flg,
));

// �ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>