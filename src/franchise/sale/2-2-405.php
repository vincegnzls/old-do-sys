<?php
/*
 * ����
 * �����ա�������BɼNo.��������ô���ԡ���������
 * ��2007/04/02��01-002��������kaku-m��������ۤ˾�������Ͽ�Ǥ��ʤ��褦�˽�����
 * ��2007/04/02��01-003��������kaku-m�������������̤������ܤ����ѹ�����Ȥ��������ֹ渡����shop_id���ɲ�
 * ��2007/04/10����������������kaku-m��������ۤη�������å���Ϥ�����
 * ��2007/04/10����������������kaku-m���������Ȳ��̤�������ܻ���client_cd1,2�ǥ����Ȥ���褦�˽���
 * ��2007/04/11����������������kaku-m������t_payallocation_d��INSERT����claim_div���ɲ�
 * ��2007/04/11����������������kaku-m�����������ʬ�����38:����Ĵ���פ���
 * ��2007/04/12����������������fukuda��������������������ɽ����������ۤ�0�ˤʤ��Զ�����
 * ��2007/04/16����������������kaku-m�����������ǧ�ܥ��󲡲����������襳���ɤΥ����å��򤹤�褦���ɲ�
 * ��2007/04/16����������������kaku-m�����������ǧ�ܥ��󲡲����������ֹ�Υ����å��򤹤�褦���ɲ�
 * ��2007/04/16����������������kaku-m�����������ѹ����ˤϿ�ʬ�ܥ������ɽ�����ѹ�
 * ��2007/04/16����������������kaku-m�������ѹ��ǡ�����л��������ʬ=2�ξ���ɲ�
 * ��2007/04/18����������������fukuda��������¾�����ʤɤ��ɲ�
 *  2007-04-18      U14-001     fukuda      ����ۤ��ʤ����ϳ�ǧ���̤�����ۤ�ʥ�С��ե����ޥåȤ��ʤ��褦����
 *  2007-04-18      U14-002     fukuda      ��ǧ���̻�������ۤ�ʥ�С��ե����ޥåȤ���褦����
 *  2007-04-18      U14-003     fukuda      ��ǧ���̻����������ʥ�С��ե����ޥåȤ���褦����
 *  2007-04-18      U14-005     fukuda      ��Ω�����������ꤹ�뤳�Ȥ��Ǥ����Զ�����
 *  2007-04-18      U14-006     fukuda      �ۤʤ�������������ֹ�ǿ�ʬ��Ԥ��ȥ����ꥨ�顼���Ǥ��Զ�����
 *  2007-04-18      U14-007     fukuda      GET���ͤ�ʸ�������ꤹ��ȥ����ꥨ�顼���Ǥ��Զ�����
 *  2007-04-18      C15-003     fukuda      ��ʬ����hidden�������ID�򹹿��Ǥ��Ƥ��ʤ��ä��Զ�����
 *  2007-04-18      C15-004     fukuda      �������ʬ�������⤷���ǡ������ѹ����̤ǻҡ�������ˤ�ɽ������ʤ��Զ�����
 *  2007-04-23      ����¾172   fukuda      ��ץܥ��󲡲������ڡ������󥯤ǹ��ɽ�����ذ�ư����
 *  2009-06-11      ����No.3	aizawa-m    �����ʬ�ˡֽ���פ�����Ĥ��ѹ�
 *  2009-07-28                  aoyama-n    �����ֹ���ѹ��������ٿ���ʬ����¹Ԥ������˹�פ������������ζ�ۤ�ɽ��������Զ�罤��
 *  2012-12-19                  hashimoto-y �ƻҰ����������ǡ��Ҥ����������������Զ��ν���
 *
 */

$page_title = "��������";

// �Ķ�����ե�����
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");
require_once(INCLUDE_DIR."2-2-405.php.inc");

// HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB��³
$db_con = Db_Connect();


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
if ($_POST["ok_button"] != null){
    $clear_hdn["ok_button"] = "";
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


/****************************/
// GET����ID�������������å�
/****************************/
if ($_GET["payin_id"] != null){

    $sql  = "SELECT \n";
    $sql .= "   pay_id \n";
    $sql .= "FROM \n";
    $sql .= "   t_payin_h \n";
    $sql .= "WHERE \n";
    $sql .= "   pay_id = ".(float)$_GET["payin_id"]." \n";
    $sql .= "AND \n";
    $sql .= ($group_kind == "2") ? "   shop_id IN (".Rank_Sql().") \n" : "   shop_id = $shop_id \n";
    $sql .= "AND \n";
    $sql .= "   payin_div = '2' \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    if (pg_num_rows($res) == 0){
        // TOP������
        header("Location: ../top.php");
    }

}


/****************************/
// ����ID������GETȽ��
/****************************/
// GET������ID������
if ($_GET["payin_id"] != null){

    // ������ɼ�κ������������
    $sql  = "SELECT \n";
    $sql .= "   enter_day \n";
    $sql .= "FROM \n";
    $sql .= "   t_payin_h \n";
    $sql .= "WHERE \n";
    $sql .= "   pay_id = ".$_GET["payin_id"]." \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $enter_day = pg_fetch_result($res, 0, 0);

    $payin_id               = $_GET["payin_id"];
    $set_hdn["hdn_payin_id"]= $payin_id;
    $get_flg                = true;
    $set_hdn["get_flg"]     = "true";
    $set_hdn["hdn_enter_day"] = $enter_day;
    $form->setConstants($set_hdn);

}else

// hidden������ID������
if ($_POST["hdn_payin_id"] != null){

    $payin_id               = $_POST["hdn_payin_id"];
    $get_flg                = ($_POST["get_flg"] == "true") ? true : false;

}else

// �ʤˤ�ʤ����϶�
if ($_GET["payin_id"] == null && $_POST["get_flg"] == ""){

    $payin_id               = "";
    $set_hdn["hdn_payin_id"]= "";
    $get_flg                = false;
    $set_hdn["get_flg"]     = "";
    $form->setConstants($set_hdn);

}


/*****************************/
// ���������
/*****************************/
$max_row    = null;     // �ҤιԿ�


/*****************************/
// �ե�����ѡ������
/*****************************/

/****************************/
// �����襳�������ϻ�
/****************************/
// POST�ǡ�����������
if ($_POST["hdn_claim_search_flg"] == "true"){

// ��
    $set_data["divide_flg"] = "";
    $form->setConstants($set_data);
// ��

    // �������줿������ξ�������
    $get_claim_data = Get_Claim_Data($db_con);

    // �����ǡ�����������
    if ($get_claim_data != null){
        $search_claim_id        = $get_claim_data[0]["client_id"];      // ������ID
        $search_claim_cname     = $get_claim_data[0]["client_cname"];   // ������̾��ά�Ρ�
        $search_pay_name        = $get_claim_data[0]["pay_name"];       // ����̾��1
        $search_account_name    = $get_claim_data[0]["account_name"];   // ����̾��2
        $claim_found_flg        = true;                                 // ������¸�ߥե饰
    }else{
        $search_claim_id        = "";
        $search_claim_cname     = "";
        $search_pay_name        = "";
        $search_account_name    = "";
        $claim_found_flg        = false;
    }

    // ������ID��������̾��ά�Ρˡ�����̾��1,2��ե�����˥��å�
    $claim_data["hdn_claim_id"]         = $search_claim_id;
    $claim_data["form_claim"]["name"]   = $search_claim_cname;
    $claim_data["form_pay_name"]        = $search_pay_name;
    $claim_data["form_account_name"]    = $search_account_name;
    $form->setConstants($claim_data);

    // ������¸�ߥե饰��true�ξ��
    if ($claim_found_flg == true){

        // ����ǡ�������
        $get_bill_data = Get_Bill_Data($db_con, $search_claim_id);

        if ($get_bill_data != null){
            $search_bill_id     = $get_bill_data[0]["bill_id"];         // �����ID
            $search_bill_no     = $get_bill_data[0]["bill_no"];         // �����ֹ�
            $search_bill_amount = $get_bill_data[0]["payment_this"];    // �����
            $bill_found_flg     = true;
        }

    }

    // ����ǡ��������ե饰��true�ξ��
    if ($bill_found_flg == true){
        $set_form["hdn_bill_id"]        = $search_bill_id;
        $set_form["hdn_bill_no"]        = $search_bill_no;
        $set_form["form_bill_no"]       = $search_bill_no;
        $set_form["form_bill_amount"]   = $search_bill_amount;
        $set_form["form_bill2_amount"]  = ($search_bill_amount != null) ? number_format($search_bill_amount) : "";
    }else{
        $set_form["hdn_bill_id"]        = "";
        $set_form["hdn_bill_no"]        = "";
        $set_form["form_bill_no"]       = "";
        $set_form["form_bill_amount"]   = "";
        $set_form["form_bill2_amount"]  = "";
    }

    // ����¾�Υե��������ˤ���
    $set_form["form_payin_day"]["y"]    = "";
    $set_form["form_payin_day"]["m"]    = "";
    $set_form["form_payin_day"]["d"]    = "";
    $set_form["form_trade"]             = "";
	$set_form["form_collect_staff"]		= "";	//--2009/06/11 ����No.3 �ɲ�
    $set_form["form_bank"]              = "";
    $set_form["form_limit_day"]["y"]    = "";
    $set_form["form_limit_day"]["m"]    = "";
    $set_form["form_limit_day"]["d"]    = "";
    $set_form["form_bill_paper_no"]     = "";
    $set_form["hdn_max_row"]            = "";
    $set_form["form_amount_total"]      = "";
    $set_form["form_rebate_total"]      = "";
    $set_form["form_payin_total"]       = "";

    // �����踡���ե饰�򥯥ꥢ
    $set_form["hdn_claim_search_flg"]   = "";

    $form->setConstants($set_form);

    // �����Կ���null�ˤ���
    $max_row = null;

}


/****************************/
// ��ۿ�ʬ����
/****************************/
// ��ۿ�ʬ�ܥ��󲡲���
//if ($_POST["divide_flg"]   && $_POST['hdn_claim_search_flg']==null){// || $_POST['form_verify_btn']!=null ){
if ($_POST["divide_flg"] == "true"){

    /****************************/
    // ���顼�����å�
    /****************************/
    // ��������
    // ɬ�ܥ����å�
    if ($_POST["form_claim"]["cd1"] == null && $_POST["form_claim"]["cd2"] == null){
        $form->setElementError("err_claim", "������ ��ɬ�ܤǤ���");
        $divide_null_flg = $err_flg = true;
    }

    // �����������褬���򤵤�Ƥ��ʤ����
    if ($err_flg != true){
        // ���Ϥ��줿�����襳���ɤ��������������
        $get_claim_data = Get_Claim_Data($db_con);
        // ���η�̤�null�ξ��
        if ($get_claim_data == null){
            $err_msg = "����������������Ϥ��Ƥ���������";
            $form->setElementError("form_claim", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }

    // �������ѹ���˶�ۿ�ʬ�ܥ��󤬲������줿���
    if ($err_flg != true){
        $err_msg = "���������������� ��ۿ�ʬ�ܥ��� ��������ޤ�����<br>������ľ���Ƥ���������";
        $divide_null_flg = $err_flg = $illegal_post_flg = Illegal_Post_Chk($form, $db_con, "err_illegal_post", $err_msg);
    }

    // �������ֹ�
    // �����ֹ�������������å�
    if ($err_flg != true){
        $bill_chk = Bill_Check($db_con);
        if ($bill_chk == "bill_false" && $_POST["form_bill_no"] != null){
            $err_msg = "���Ϥ��줿�����ֹ��¸�ߤ��ޤ���";
            $form->setElementError("form_bill_no", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }


    /****************************/
    // ���顼�����å���̽���
    /****************************/
    // �����å�Ŭ��
    $form->validate();

    // ��̤�ե饰��
    $divide_err_flg = (count($form->_errors) > 0) ? true : false;

    /****************************/
    // ���顼������
    /****************************/
    // ��ۿ�ʬ���顼��������ۤ����
    if ($divide_err_flg == true){
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $form->setConstants($clear_form);
    }

    // �������ѹ���˥ܥ��󤬲������줿���������襳���ɰʳ��Υե������hidden�����
    if ($illegal_post_flg == true){
        $clear_form["form_claim"]["name"]   = "";
        $clear_form["form_pay_name"]        = "";
        $clear_form["form_account_name"]    = "";
        $clear_form["form_bill_no"]         = "";
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $clear_form["hdn_claim_id"]         = "";
        $clear_form["hdn_bill_no"]          = "";
        $form->setConstants($clear_form);
    }

    /****************************/
    // ��ʬ����
    /****************************/
    if ($divide_err_flg != true){

        // ����ۼ���
        if ($_POST["form_bill_no"] != null){
            $sql  = "SELECT \n";
            $sql .= "   t_bill_d.payment_this, \n";
            $sql .= "   t_bill_d.bill_id \n";
            $sql .= "FROM \n";
            $sql .= "   t_bill \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id  \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill.bill_no = '".$_POST["form_bill_no"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
            $sql .= "AND \n";
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            $payment_this   = pg_fetch_result($res, 0, 0);
            $bill_id        = pg_fetch_result($res, 0, 1);
            $set_bill_amount["form_bill_amount"] = $payment_this;
            $set_bill_amount["form_bill2_amount"]= ($payment_this != null) ? number_format($payment_this) : "";
            $set_bill_amount["hdn_bill_id"]      = $bill_id;
            $form->setConstants($set_bill_amount);
        }

        // ������������˼�ư�䴰���줿�����ֹ�ξ��
        if ($bill_chk == "bill_true"){

            // �����ǡ�������
            $get_divide_data = Get_Divide_Data($db_con, $bill_id);

            // 1�԰ʾ夢���
            if ($get_divide_data != null){
                $divide_flg                         = true;
                $max_row                            = count($get_divide_data);
                $set_form_data["hdn_max_row"]       = $max_row;
                $form->setConstants($set_form_data);
                // ��ʬ�ե�����������ǡ����򥻥å�
                Set_Divide_Form($form, $max_row, $get_divide_data);
            }

        // �����ֹ椬¸�ߤ�����
        }elseif ($bill_chk == "bill_found"){

            // ���Ϥ��줿�����ֹ�������ID������ۤ����
            $sql  = "SELECT \n";
            $sql .= "   t_bill.bill_id, \n";
            $sql .= "   t_bill_d.payment_this \n";
            $sql .= "FROM \n";
            $sql .= "   t_bill \n";
            $sql .= "   INNER JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_bill.bill_no = '".$_POST["form_bill_no"]."' \n";
            $sql .= "AND \n";
            $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
            $sql .= "AND \n";
            //aoyama-n 2009-07-28
            //�����ֹ���ѹ��������ٿ���ʬ����¹Ԥ������˹�פ������������ζ�ۤ�ɽ��������Զ�罤��
            $sql .= "   t_bill_d.bill_data_div = '0' \n";
            $sql .= "AND \n";
            $sql .= "   t_bill.shop_id = ".$_SESSION["client_id"]." \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            $bill_id        = pg_fetch_result($res, 0, 0);
            $bill_amount    = pg_fetch_result($res, 0, 1);

            // �����ǡ�������
            $get_divide_data = Get_Divide_Data($db_con, $bill_id);

            // 1�԰ʾ夢���
            if ($get_divide_data != null){
                $divide_flg                         = true;
                $max_row                            = count($get_divide_data);
                $set_form_data["hdn_max_row"]       = $max_row;
                $set_form_data["hdn_bill_id"]       = $bill_id;
                $set_form_data["form_bill_no"]      = $bill_no;
                $set_form_data["form_bill_amount"]  = $bill_amount;
                $set_form_data["form_bill2_amount"] = ($bill_amount != null) ? number_format($bill_amount) : "";
                $form->setConstants($set_form_data);
                // ��ʬ�ե�����������ǡ����򥻥å�
                Set_Divide_Form($form, $max_row, $get_divide_data);
            }

        // �����ֹ椬���Ϥ���Ƥʤ����
        }elseif ($bill_chk == "bill_null"){

            $set_form_data["form_bill_amount"] = "";
            $set_form_data["form_bill2_amount"]= "";

            $sql  = "SELECT \n";
            $sql .= "   t_client.client_cd1, \n";
            $sql .= "   t_client.client_cd2, \n";
            $sql .= "   t_client.client_id, \n";
            $sql .= "   t_client.client_cname, \n";
            $sql .= "   t_claim.claim_div \n";
            $sql .= "FROM \n";
            $sql .= "   t_client \n";
            $sql .= "   INNER JOIN t_claim ON t_client.client_id = t_claim.client_id \n";
            $sql .= "WHERE \n";
            $sql .= "   t_claim.claim_id = ".$_POST["hdn_claim_id"]." \n";
            $sql .= "ORDER BY \n";
            $sql .= "   client_cd1, \n";
            $sql .= "   client_cd2 \n";
            $sql .= ";";
            $res  = Db_Query($db_con,$sql);
            $num  = pg_num_rows($res);

            if ($num > 0){
                $divide_flg                     = true;
                $client_data                    = pg_fetch_all($res);
                $set_form_data["hdn_max_row"]   = $num;
                $max_row                        = $num;
                $set_form_data["hdn_bill_id"]   = "";
                for ($i = 0; $i < $num; $i++){
                    $set_form_data["form_claim_id"][$i]         = $client_data[$i]["client_id"];
                    $set_form_data["form_claim_cd"][$i]["cd1"]  = $client_data[$i]["client_cd1"];
                    $set_form_data["form_claim_cd"][$i]["cd2"]  = $client_data[$i]["client_cd2"];
                    $set_form_data["form_claim_name"][$i]       = $client_data[$i]["client_cname"];
                    $set_form_data["hdn_claim_div"][$i]         = $client_data[$i]["claim_div"];
                    if($_POST["divide_flg"] == "true"){
                        $set_form_data["form_base_amount"][$i]  = "";
                        $set_form_data["form_amount"][$i]       = "";
                        $set_form_data["form_rebate"][$i]       = "";
                        $set_form_data["form_note"][$i]         = "";
                        $set_form_data["hdn_claim_div"][$i]     = $client_data[$i]["claim_div"];
                    }
                    $form->setConstants($set_form_data);
                }
            }

        }

        // ��ۿ�ʬ���������ֹ��hidden�˥��åȤ��Ƥ���
        $hdn_post_bill_no_set["hdn_post_bill_no"] = ($_POST["form_bill_no"] != null) ? $_POST["form_bill_no"] : "";
        $form->setConstants($hdn_post_bill_no_set);

    }

    // ��ۿ�ʬ�ե饰�ȹ�׶�ۤ򥯥ꥢ
    $set_data["divide_flg"]         = "";
    $set_data["form_amount_total"]  = "";
    $set_data["form_rebate_total"]  = "";
    $set_data["form_payin_total"]   = "";
    $form->setConstants($set_data);

}


/****************************/
// ��׽���
/****************************/
// ��ץܥ��󤬲����줿�Ȥ�
if ($_POST["calc_flg"] == true || $_POST["form_verify_btn"] != null){

    $divide_flg                         = true;
    $max_row                            = $_POST['hdn_max_row'];
    $set_form_data["form_amount_total"] = null;
    $set_form_data["form_rebate_total"] = null;
    $set_form_data["form_payin_total"]  = null;
    for ($i = 0; $i < $max_row; $i++){
        $set_form_data["form_amount"][$i]   = $_POST["form_amount"][$i];
        $set_form_data["form_amount_total"] = $set_form_data["form_amount_total"] + str_replace(",", null, $_POST["form_amount"][$i]);
        $set_form_data["form_rebate_total"] = $set_form_data["form_rebate_total"] + str_replace(",", null, $_POST["form_rebate"][$i]);
    }
    $payin_total = $set_form_data["form_amount_total"] + $set_form_data["form_rebate_total"];
    $set_form_data["form_amount_total"] = number_format($set_form_data["form_amount_total"]);
    $set_form_data["form_rebate_total"] = number_format($set_form_data["form_rebate_total"]);
    $set_form_data["form_payin_total"]  = number_format($payin_total);
    $set_form_data["calc_flg"]          = "";
    $form->setConstants($set_form_data);

}


/****************************/
// ���ϳ�ǧ
/****************************/
if ($_POST["form_verify_btn"] != null){

    // ���դ�0���
    $_POST["form_payin_day"] = Str_Pad_Date($_POST["form_payin_day"]);
    $_POST["form_limit_day"] = Str_Pad_Date($_POST["form_limit_day"]);

    // POST�ǡ������ѿ��˥��å�
    $payin_day_y   = $_POST["form_payin_day"]["y"];
    $payin_day_m   = $_POST["form_payin_day"]["m"];
    $payin_day_d   = $_POST["form_payin_day"]["d"];
    $payin_day     = $payin_day_y."-".$payin_day_m."-".$payin_day_d;
    $limit_day_y   = $_POST["form_limit_day"]["y"];
    $limit_day_m   = $_POST["form_limit_day"]["m"];
    $limit_day_d   = $_POST["form_limit_day"]["d"];
    $limit_day     = $limit_day_y."-".$limit_day_m."-".$limit_day_d;

    /****************************/
    // ���顼�����å�
    /****************************/
    // ��������
    // ɬ�ܥ����å�
    if ($_POST["form_claim"]["cd1"] == null && $_POST["form_claim"]["cd2"] == null){
        $form->setElementError("err_claim", "������ ��ɬ�ܤǤ���");
        $divide_null_flg = $err_flg = true;
    }

    // �����������褬���򤵤�Ƥ��ʤ����
    if ($err_flg != true){
        // ���Ϥ��줿�����襳���ɤ��������������
        $get_claim_data = Get_Claim_Data($db_con);
        // ���η�̤�null�ξ��
        if ($get_claim_data == null){
            $err_msg = "����������������Ϥ��Ƥ���������";
            $form->setElementError("form_claim", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }

    // �������ѹ���˶�ۿ�ʬ�ܥ��󤬲������줿���
    if ($err_flg != true){
        $err_msg = "���������������� ��ۿ�ʬ�ܥ��� ��������ޤ�����<br>������ľ���Ƥ���������";
        $divide_null_flg = $err_flg = $illegal_post_flg = Illegal_Post_Chk($form, $db_con, "err_illegal_post", $err_msg);
    }

    // �������ֹ�
    // �����ֹ�������������å�
    if ($err_flg != true){
        $bill_chk = Bill_Check($db_con);
        if ($bill_chk == "bill_false" && $_POST["form_bill_no"] != null){
            $err_msg = "���Ϥ��줿�����ֹ��¸�ߤ��ޤ���";
            $form->setElementError("form_bill_no", $err_msg);
            $divide_null_flg = $err_flg = true;
        }
    }

    // ��ۿ�ʬ���������ֹ椬�ۤʤ���
    if ($_POST["hdn_post_bill_no"] != $_POST["form_bill_no"]){
        $form->setElementError("form_bill_no", "�����ֹ椬��ۿ�ʬ�������ѹ����줿������Ͽ�Ǥ��ޤ���<br>���ٶ�ۿ�ʬ��ԤäƲ�������");
        $divide_null_flg = $err_flg = true;
    }

    // ��������
    $err_msg = "������ �������ǤϤ���ޤ���";

    // ɬ�ܥ����å�
    if ($payin_day == "--"){
        $form->setElementError("form_payin_day", "������ ��ɬ�ܤǤ���");
        $err_payin_day = true;
    }

    // ���ͥ����å�
    if ($err_payin_day != true){
        if (!ereg("^[0-9]+$", $payin_day_y) || !ereg("^[0-9]+$", $payin_day_m) || !ereg("^[0-9]+$", $payin_day_d)){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // �����������å�
    if ($err_payin_day != true){
        if (!checkdate((int)$payin_day_m, (int)$payin_day_d, (int)$payin_day_y)){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // �����ƥ೫�������������å�
    if ($err_payin_day != true){
        $chk_res = Sys_Start_Date_Chk($payin_day_y, $payin_day_m, $payin_day_d, "������");
        if ($chk_res != null){
            $form->setElementError("form_payin_day", $chk_res);
            $err_payin_day = true;
        }
    }

    // ̤�����դ����Ϥ���Ƥ��ʤ��������å�
    if ($err_payin_day != true){
        if (date("Y-m-d") < $payin_day){
            $form->setElementError("form_payin_day", "������ ��̤������դˤʤäƤ��ޤ���");
            $err_payin_day = true;
        }
    }

    // �ǿ��η�������������դ����Ϥ���Ƥ��ʤ��������å�
    if ($err_payin_day != true){
        $err_msg = "������ ������η�������������դ����Ϥ���Ƥ��ޤ���";
        $sql  = "SELECT \n";
        $sql .= "   to_date(MAX(close_day), 'YYYY-MM-DD') AS close_day \n";
        $sql .= "FROM \n";
        $sql .= "   t_sys_renew \n";
        $sql .= "WHERE \n";
        $sql .= "   renew_div = '2' \n";
        $sql .= "AND \n";
        $sql .= ($group_kind == "2") ? "   shop_id IN (".Rank_Sql().") \n" : "   shop_id = $shop_id \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        $last_monthly_renew_date = ($num == 1) ? pg_fetch_result($res, 0) : null;
        // �������������ܷ��������������������ξ��
        if ($last_monthly_renew_date != null && $payin_day <= $last_monthly_renew_date && $payin_day != "--"){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // ����������������������դ����Ϥ���Ƥ��ʤ��������å�
    if ($err_payin_day != true){
        $err_msg = "������ �����������Ѥ����դ����Ϥ���Ƥ��ޤ���<br>���������ѹ����뤫�������������Ʋ�������";
        $sql  = "SELECT \n";
        $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
        $sql .= "FROM \n";
        $sql .= "   t_bill \n";
        $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
        $sql .= "WHERE \n";
        $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
        $sql .= "AND \n";
        $sql .= "   t_bill_d.client_id = ".$_POST["hdn_claim_id"]." \n";
        $sql .= "AND \n";
        $sql .= ($group_kind == "2") ? "   t_bill.shop_id IN (".Rank_Sql().") \n" : "   t_bill.shop_id = $shop_id \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        $last_close_date = ($num > 0) ? pg_fetch_result($res, 0) : null;
        // �����������������������������ξ��
        if ($last_close_date != null && $payin_day <= $last_close_date && $payin_day != "--"){
            $form->setElementError("form_payin_day", $err_msg);
            $err_payin_day = true;
        }
    }

    // �������ʬ
    // ɬ�ܥ����å�
    if ($_POST["form_trade"] == null){
        // ���顼�򥻥å�
        $form->setElementError("form_trade", "�����ʬ ��ɬ�ܤǤ���");
    }
	//-- 2009/06/11 ����No.3 �ɲ�
	// ����ô���Ԥ����򤵤�Ƥ����硢�����ʬ�����ϥ����å�
	else if ($_POST["form_trade"] != "31" && $_POST["form_collect_staff"]) {
		$form->setElementError("form_trade", "����ô���� �����򤷤����ϡ������ʬ�ֽ���פΤ������ǽ�Ǥ���");
	}
	//-- 2009/06/11 ����No.3 �ɲ�
	// �����ʬ�ֽ���פξ�硢����ô���Ԥ�ɬ�ܥ����å�
	else if ($_POST["form_trade"] == "31" && !$_POST["form_collect_staff"]) {
		$form->setElementError("form_trade", "�����ʬ���ֽ���� �ξ��ϡ�����ô���� ��ɬ�ܤǤ���");
	}	


    // �����
    // ɬ�ܥ����å�
    if (
        ($_POST["form_trade"] == 32 || $_POST["form_trade"] == 33) &&
        ($_POST["form_bank"][0] == null && $_POST["form_bank"][1] == null && $_POST["form_bank"][2] == null)
    ){
        $form->setElementError("form_bank", "�����ʬ���������⡦�������ξ�硢��Ԥ�ɬ�ܤǤ���");
        $err_bank = true;
    }

    // Ⱦü���ϥ����å�
    if (
        ($_POST["form_bank"][0] != null || $_POST["form_bank"][1] != null || $_POST["form_bank"][2] != null) &&
        ($_POST["form_bank"][0] == null || $_POST["form_bank"][1] == null || $_POST["form_bank"][2] == null)
    ){
        $form->setElementError("form_bank", "��Ԥϸ����ֹ�ޤ����򤷤Ʋ�������");
        $err_bank = true;
    }

    // ���������
    $err_msg = "������� �������ǤϤ���ޤ���";

    // Ⱦü���ϥ����å�
    if (
        ($limit_day_y != null || $limit_day_m != null || $limit_day_d != null) &&
        ($limit_day_y == null || $limit_day_m == null || $limit_day_d == null)
    ){
        $form->setElementError("form_limit_day", $err_msg);
        $err_limit_day = true;
    }

    // ���ͥ����å�
    if ($err_limit_day != true && $limit_day != "--"){
        if (!ereg("^[0-9]+$", $limit_day_y) || !ereg("^[0-9]+$", $limit_day_m) || !ereg("^[0-9]+$", $limit_day_d)){
            $form->setElementError("form_limit_day", $err_msg);
            $err_limit_day = true;
        }   
    }      

    // �����������å�
    if ($err_limit_day != true && $limit_day != "--"){
        if (!checkdate((int)$limit_day_m, (int)$limit_day_d, (int)$limit_day_y)){
            $form->setElementError("form_limit_day", $err_msg);
            $err_limit_day = true;
        }   
    }

    // ɬ�ܥ����å�
    if ($err_limit_day != true){
        if ($_POST["form_trade"] == 33 && $limit_day == "--"){
            $form->setElementError("form_limit_day", "�����ʬ���������ξ��ϡ�������������Ϥ��Ƥ���������");
            $err_limit_day = true;
        }
    }

    // ��ʬ�ơ��֥���ɽ���ե饰��true�Ǥʤ����
    if ($divide_null_flg != true){

        // ������ۡ������
        $max_row = $_POST["hdn_max_row"];
        for ($i = 0; $i < $max_row; $i++){
            $form->addRule("form_amount[$i]", ($i+1)."���� ����������ۤ����Ϥ��Ƥ���������", "regex", "/^[-]?[0-9]+$/");
            $form->addRule("form_rebate[$i]", ($i+1)."���� ����������������Ϥ��Ƥ���������", "regex", "/^[-]?[0-9]+$/");
        }

        // �Կ�����
        $max_row = $_POST["hdn_max_row"];

        // �Կ��ǥ롼��
        for ($i = 0; $i < $max_row; $i++){

            // ����ۡ�����������ͤ����Ϥ�������
            if ($_POST["form_amount"][$i] != null || $_POST["form_rebate"][$i] != null || $_POST["form_note"][$i] != null){

                // �����ɬ�ܥ����å�
                if ($err_bank != true && $_POST["form_rebate"][$i] != null && $_POST["form_bank"][0] == null){
                    $form->setElementError("form_bank", "����� �����Ϥ�����ϡ���Ԥ�ɬ�ܤǤ���");
                }

                // �������
                // ���ͥ����å�
                if ($_POST["form_amount"][$i] != null && ereg("^(-)?([0-9])*$", $_POST["form_amount"][$i]) == false){ 
                    $form->setElementError("form_amount[$i]", ($i+1)."���� ����������ۤ����Ϥ��Ƥ���������");
                }
                // ɬ�ܥ����å��ʼ���������ͤ����Ϥ���Ƥ������
                if ($_POST["form_amount"][$i] == null && ($_POST["form_rebate"][$i] != null || $_POST["form_note"][$i] != null)){
                    $form->setElementError("form_amount[$i]", ($i+1)."���� ����������ͤ����Ϥ�����ϡ�����ۤ�ɬ�ܤǤ���");
                }
                // �������
                // ���ͥ����å�
                if ($_POST["form_rebate"][$i] != null && ereg("^(-)?([0-9])*$", $_POST["form_rebate"][$i]) == false){ 
                    $form->setElementError("form_rebate[$i]", ($i+1)."���� ����������������Ϥ��Ƥ���������");
                }
                // ��������˻Ҥ��Ƥ��Ѥ�äƤ��������θ�������������Ҥ���������
                if ($err_payin_day != true){
                    $err_msg = "������ �����������Ѥ����դ����Ϥ���Ƥ��ޤ���<br>���������ѹ����뤫�������������Ʋ�������";
                    $sql  = "SELECT \n";
                    $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
                    $sql .= "FROM \n";
                    $sql .= "   t_bill_d \n";
                    $sql .= "   INNER JOIN t_claim ON  t_bill_d.client_id = t_claim.client_id \n";
                    $sql .= "                      AND t_bill_d.claim_div = t_claim.claim_div \n";
                    $sql .= "WHERE \n";
                    $sql .= "   t_bill_d.client_id = ".$_POST["form_claim_id"][$i]." \n";
                    $sql .= "AND \n";
                    $sql .= "   t_bill_d.claim_div = ".$_POST["hdn_claim_div"][$i]." \n";
                    $sql .= ";";
                    $res  = Db_Query($db_con, $sql);
                    $num  = pg_num_rows($res);
                    if ($num > 0){
                        $max_bill_closeday_this = pg_fetch_result($res, 0, 0);

                        #20121219 hashimoto-y ��������$max_bill_closeday_this��Ʊ����硢���������Զ��
                        #if ($payin_day < $max_bill_closeday_this){
                        if ($payin_day <= $max_bill_closeday_this){
                            $form->setElementError("form_payin_day", ($i+1)."���ܡ�$err_msg");
                        }
                    }
                }
            // ����ۡ���������ɤ�������Ϥ��ʤ����
            }else{

                $form_count[] = $i;

            }

        }

        // �������
        // ��ۤ�����null�ξ��
        if(count($form_count) == $max_row){
            $form->setElementError("err_count", "����ۤ����Ϥ��Ƥ���������");
        }

    }

    /****************************/
    // ���顼�����å���̽���
    /****************************/
    // �����å�Ŭ��
    $form->validate();

    // ��̤�ե饰��
    $err_flg = (count($form->_errors) > 0) ? true : false;

    /****************************/
    // ���顼������
    /****************************/
    // ��ۿ�ʬ���顼��������ۤ����
    if ($divide_null_flg == true){
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $form->setConstants($clear_form);
    }

    // �������ѹ���˥ܥ��󤬲������줿���������襳���ɰʳ��Υե������hidden�����
    if ($illegal_post_flg == true){
        $clear_form["form_claim"]["name"]   = "";
        $clear_form["form_pay_name"]        = "";
        $clear_form["form_account_name"]    = "";
        $clear_form["form_bill_no"]         = "";
        $clear_form["form_bill_amount"]     = "";
        $clear_form["form_bill2_amount"]    = "";
        $clear_form["hdn_claim_id"]         = "";
        $clear_form["hdn_bill_no"]          = "";
        $clear_form["hdn_post_bill_no"]     = "";
        $form->setConstants($clear_form);
    }

    /****************************/
    // ���顼�ʤ�������
    /****************************/
    if ($err_flg != true){

        $freeze_flg = true;
        $divide_flg = true;

        if($_POST["get_flg"] != "true"){
            $form->addElement("static", "divide_msg", "", "�ʲ������Ƥ����⤷�ޤ�����");
        }else{
            $form->addElement("static", "divide_msg", "", "�ʲ������Ƥ������ѹ����ޤ�����");
        }

        $set_data["freeze_flg"] = true;
        $form->setConstants($set_data);

    }

}

/****************************/
// ����ܥ��󲡲�
/****************************/
if ($_POST["ok_button"] != null){

    // POST���줿�ͤ�������䤹���褦���ѿ�������
    if ($post_bill_no == null){
        $post_bill_id       = null;
        $post_bill_amount   = null;
    }else{
        $post_bill_id       = $_POST["hdn_bill_id"];                                // ����ID
        $post_bill_amount   = str_replace(",", null, $_POST["form_bill_amount"]);   // ������
    }
    $max_row = $_POST["hdn_max_row"];

    // ����������뤫
    for ($i = 0; $i < $max_row; $i++){    
        if ($_POST["form_amount"][$i] != null){
            $post_amount    = $post_amount + str_replace(",", "", $_POST["form_amount"][$i]);
        }
        if ($_POST["form_rebate"][$i] != null){
            $post_rebate    = $post_rebate + str_replace(",", "", $_POST["form_rebate"][$i]);
            $rebate_flg     = true;
        }
    }

    $_POST["form_payin_day"] = Str_Pad_Date($_POST["form_payin_day"]);
    $payin_day = $_POST["form_payin_day"]["y"]."-".$_POST["form_payin_day"]["m"]."-".$_POST["form_payin_day"]["d"];
    
    /******************************/
    // �ǽ����顼�����å�
    /******************************/
    // ��ɼ�ѹ������оݤȤʤ���ɼ������������������졢�̤���ɼ���񤭤��褦�Ȥ��Ƥ��ʤ��������å�
    if ($_POST["get_flg"] == "true"){
        // ����ID����ɼ�������֤�����ǡ������͹礻
        $same_data_flg = Update_Check($db_con, "t_payin_h", "pay_id", $_POST["hdn_payin_id"], $_POST["hdn_enter_day"]);
        if ($same_data_flg == false){
            header("Location: ./2-2-410.php?err=3");
            exit;
        }
    }

    // ��ɼ�ѹ�����������������Ƥ��ʤ��������å�
    if ($_POST["get_flg"] == "true"){
        $sql  = "SELECT \n";
        $sql .= "   renew_flg \n";
        $sql .= "FROM \n";
        $sql .= "   t_payin_h \n";
        $sql .= "WHERE \n";
        $sql .= "   pay_id = ".$_POST["hdn_payin_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $renew = pg_fetch_result($res, 0, 0);
        if ($renew == "t"){
            header("Location: ./2-2-410.php?err=1");
            $last_err_flg = true;
            exit;
        }

    }

    // ����񤬺������Ƥ��ʤ��������å�
    if ($_POST["form_bill_no"] != null){
        $sql  = "SELECT \n";
        $sql .= "   bill_id \n";
        $sql .= "FROM \n";
        $sql .= "   t_bill \n";
        $sql .= "WHERE \n";
        $sql .= "   bill_id = ".$_POST["hdn_bill_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        if ($num == 0){
            header("Location: ./2-2-410.php?err=2");
            $last_err_flg = true;
            exit;
        }
    }

    // �ǿ��η�������������դ����Ϥ���Ƥ��ʤ��������å�
    $sql  = "SELECT \n";
    $sql .= "   to_date(MAX(close_day), 'YYYY-MM-DD') AS close_day \n";
    $sql .= "FROM \n";
    $sql .= "   t_sys_renew \n";
    $sql .= "WHERE \n";
    $sql .= "   renew_div = '2' \n";
    $sql .= "AND \n";
    $sql .= ($group_kind == "2") ? "   shop_id IN (".Rank_Sql().") \n" : "   shop_id = $shop_id \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);
    $last_monthly_renew_date = ($num == 1) ? pg_fetch_result($res, 0) : null;
    // �������������ܷ��������������������ξ��
    if ($last_monthly_renew_date != null && $payin_day <= $last_monthly_renew_date && $payin_day != "--"){
        $form->setElementError("form_payin_day", "������ ������η�������������դ����Ϥ���Ƥ��ޤ���");
        $last_err_flg = true;
    }

    // ����������������������դ����Ϥ���Ƥ��ʤ��������å�
    $sql  = "SELECT \n";
    $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
    $sql .= "FROM \n";
    $sql .= "   t_bill \n";
    $sql .= "   LEFT JOIN t_bill_d ON t_bill.bill_id = t_bill_d.bill_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_bill.claim_id = ".$_POST["hdn_claim_id"]." \n";
    $sql .= "AND \n";
    $sql .= "   t_bill_d.client_id = ".$_POST["hdn_claim_id"]." \n";
    $sql .= "AND \n";
    $sql .= ($group_kind == "2") ? "   t_bill.shop_id IN (".Rank_Sql().") \n" : "   t_bill.shop_id = $shop_id \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);
    $last_close_date = ($num > 0) ? pg_fetch_result($res, 0) : null;
    // �����������������������������ξ��
    if ($last_close_date != null && $payin_day <= $last_close_date && $payin_day != "--"){
        $form->setElementError("form_payin_day", "������ �����������Ѥ����դ����Ϥ���Ƥ��ޤ���<br>���������ѹ����뤫�������������Ʋ�������");
        $last_err_flg = true;
    }

    // ��������˻Ҥ��Ƥ��Ѥ�äƤ��������θ�������������Ҥ���������������ˤʤäƤ��ʤ��������å�
    // �Կ��ǥ롼��
    if ($last_err_flg != true){
        for ($i = 0; $i < $max_row; $i++){
            // ����ۡ�����������ͤ����Ϥ�������
            if ($_POST["form_amount"][$i] != null || $_POST["form_rebate"][$i] != null || $_POST["form_note"][$i] != null){
                $sql  = "SELECT \n";
                $sql .= "   MAX(t_bill_d.bill_close_day_this) \n";
                $sql .= "FROM \n";
                $sql .= "   t_bill_d \n";
                $sql .= "   INNER JOIN t_claim ON  t_bill_d.client_id = t_claim.client_id \n";
                $sql .= "WHERE \n";
                $sql .= "   t_bill_d.client_id = ".$_POST["form_claim_id"][$i]." \n";
                $sql .= "AND \n";
                $sql .= "   t_bill_d.claim_div = ".$_POST["hdn_claim_div"][$i]." \n";
                $sql .= ";";
                $res  = Db_Query($db_con, $sql);
                $num  = pg_num_rows($res);
                if ($num > 0){
                    $max_bill_closeday_this = pg_fetch_result($res, 0, 0);
                    if ($payin_day < $max_bill_closeday_this){
                        $form->setElementError("form_payin_day", ($i+1)."���ܡ������� �����������Ѥ����դ����Ϥ���Ƥ��ޤ���<br>���������ѹ����뤫�������������Ʋ�������");
                    }
                }
            }
        }
    }

    /******************************/
    // ���顼�����ä����
    /******************************/
    if ($last_err_flg == true){

        // �����ʤ�

    }

    /******************************/
    // ���顼�ʤ�����DB����
    /******************************/
    if ($last_err_flg != true){

        /******************************/
        // ������Ͽ�ξ��
        /******************************/
        if ($_POST["get_flg"] != "true"){

            // �ȥ�󥶥�����󳫻�
            Db_Query($db_con, "BEGIN;");

            // �ǿ������ֹ����
            $payin_no = Get_New_Payin_No($db_con);

            // INSERT ����إå�
            Insert_Payin_H($db_con, $payin_no);

            // INSERT ����ǡ���
            Insert_Payin_D($db_con, $payin_no, $post_amount, $post_rebate, $rebate_flg);

            // UPDATE �����ֹ�ơ��֥�
            Insert_Serial($db_con, $payin_no);

            // INSERT �������ơ��֥�
            Insert_Allocate($db_con, $max_row, $payin_no);

            // �ȥ�󥶥�����󴰷�
            Db_Query($db_con, "COMMIT;");

            $location = "new";

        /****************************/
        // �ѹ��ξ��
        /****************************/
        }elseif ($_POST["get_flg"] == "true"){

            $payin_id = $_POST["hdn_payin_id"];

            // �ȥ�󥶥�����󳫻�
            Db_Query($db_con, "BEGIN;");

            // ����إå�����
            Update_Payin_H($db_con);

            // ����ǡ�������
            Update_Payin_D($db_con, $post_amount, $post_rebate, $rebate_flg);

            // ��ʬ�ơ��֥빹��
            Update_Allocate($db_con, $max_row);

            // �ȥ�󥶥�����󴰷�
            Db_Query($db_con, "COMMIT;");

            $location = "get";

        }

        // ��λ���̤إڡ�������
        header("Location: ./2-2-410.php?flg=".$location);

    }
        
}

/****************************/
// ��Ͽ�ѹ��ʻ��Ȳ��̤�������ܡ�
/****************************/
if ($_GET["payin_id"] != null && $_POST["get_flg"] != "true"){

    // �Ϥ��줿����ID��������ǡ��������
    // �إå���
    $sql = "SELECT \n";
    $sql .= "   t_payin_h.pay_day, \n";                 // ������
    $sql .= "   t_payin_h.client_id, \n";               // ������ID
    $sql .= "   t_payin_h.client_cd1, \n";              // �����襳���ɣ�
    $sql .= "   t_payin_h.client_cd2, \n";              // �����襳���ɣ�
    $sql .= "   t_payin_h.client_cname, \n";            // ������̾��ά�Ρ�
	$sql .= "	t_payin_h.collect_staff_id, \n";		// ����ô����ID	--2006/06/11 ����No.3 �ɲ�
    $sql .= "   t_payin_h.pay_name, \n";                // �������̾����
    $sql .= "   t_payin_h.account_name, \n";            // ����̾����
    $sql .= "   t_payin_h.renew_flg, \n";               // ���������ե饰
    $sql .= "   t_payin_h.bill_id, \n";                 // �����ID
    $sql .= "   t_bill.bill_no, \n";                    // ������ֹ�
    $sql .= "   t_payin_d.trade_id, \n";                // �����ʬID
    $sql .= "   t_bank.bank_id, \n";                    // ��ԥ�����
    $sql .= "   t_b_bank.b_bank_id, \n";                // ��Ź������
    $sql .= "   t_payin_d.account_id, \n";              // �����ֹ�
    $sql .= "   t_payin_d.payable_day, \n";             // �������
    $sql .= "   t_payin_d.payable_no \n";               // ��������ֹ�
    $sql .= "FROM \n";
    $sql .= "   t_payin_h \n";
    $sql .= "   INNER JOIN t_payin_d ON  t_payin_h.pay_id = t_payin_d.pay_id \n";
    $sql .= "   LEFT  JOIN t_bank    ON  t_bank.bank_cd = t_payin_d.bank_cd \n";
    $sql .= "                        AND t_bank.shop_id = ".$_SESSION["client_id"]." \n";
    $sql .= "   LEFT  JOIN t_b_bank  ON  t_b_bank.bank_id = t_bank.bank_id \n";
    $sql .= "                        AND t_b_bank.b_bank_cd = t_payin_d.b_bank_cd \n";
    $sql .= "   LEFT JOIN t_bill     ON  t_bill.bill_id = t_payin_h.bill_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_payin_h.pay_id = ".$_GET["payin_id"]." \n";
    $sql .= "AND \n";
    $sql .= "   trade_id != 35 \n";
    $sql .= "AND \n";
    $sql .= "   t_payin_h.shop_id = ".$_SESSION["client_id"]." \n";
    $sql .= "AND \n";
    $sql .= "   t_payin_h.payin_div = '2' \n";
    $sql .= "; \n";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);

    if ($num > 0){
        $get_payin_data = pg_fetch_array($res);
        $get_flg = true;
    }

    if($get_payin_data["bill_id"] != null){
        $sql  = "SELECT \n";
        $sql .= "   payment_this \n";
        $sql .= "FROM \n";
        $sql .= "   t_bill_d \n";
        $sql .= "WHERE \n";
        $sql .= "   bill_id = ".$get_payin_data["bill_id"]." \n";
        $sql .= "   AND bill_data_div = '0' \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        $num  = pg_num_rows($res);
        if ($num > 0){
            $bill_amount = pg_fetch_result($res, 0, 0);
        }
    }

    $set_form["hdn_claim_id"]           = $get_payin_data["client_id"];
    $set_form["form_claim"]["cd1"]      = $get_payin_data["client_cd1"];
    $set_form["form_claim"]["cd2"]      = $get_payin_data["client_cd2"];
    $set_form["form_claim"]["name"]     = $get_payin_data["client_cname"];
    $set_form["form_pay_name"]          = $get_payin_data["pay_name"];
    $set_form["form_account_name"]      = $get_payin_data["account_name"];
    if ($get_payin_data["renew_flg"] == "t"){
        $set_form["renew_flg"]          = true;
        $renew_flg                      = true;
    }
    $set_form["hdn_bill_id"]            = $get_payin_data["bill_id"];
    $set_form["form_bill_no"]           = $get_payin_data["bill_no"];
    $set_form["hdn_bill_no"]            = $get_payin_data["bill_no"];
    $set_form["hdn_post_bill_no"]       = $get_payin_data["bill_no"];
    $set_form["form_bill_amount"]       = $bill_amount;
    $set_form["form_bill2_amount"]      = ($bill_amount != null) ? number_format($bill_amount) : "";
    $set_form["form_payin_day"]["y"]    = substr($get_payin_data["pay_day"],0,4);
    $set_form["form_payin_day"]["m"]    = substr($get_payin_data["pay_day"],5,2);
    $set_form["form_payin_day"]["d"]    = substr($get_payin_data["pay_day"],8,2);
    $set_form["form_trade"]             = $get_payin_data["trade_id"];
	$set_form["form_collect_staff"]		= $get_payin_data["collect_staff_id"];	//-- 2009/06/11 ����No.3 �ɲ�
    $set_form["form_bank"][0]           = $get_payin_data["bank_id"];
    $set_form["form_bank"][1]           = $get_payin_data["b_bank_id"];
    $set_form["form_bank"][2]           = $get_payin_data["account_id"];
    $set_form["form_limit_day"]["y"]    = substr($get_payin_data["payable_day"], 0, 4);
    $set_form["form_limit_day"]["m"]    = substr($get_payin_data["payable_day"], 5, 2);
    $set_form["form_limit_day"]["d"]    = substr($get_payin_data["payable_day"], 8, 2);
    $set_form["form_bill_paper_no"]     = $get_payin_data["payable_no"];
    $form->setConstants($set_form);

    //�ǡ�����
    $sql  = "SELECT \n";
    if ($get_payin_data["bill_id"] != null){
    $sql .= "   t_bill_d.payment_this, \n";             // �����ֹ椬���ä���硢������
    }
    $sql .= "   t_payallocation_d.client_id, \n";       // ������ID
    $sql .= "   t_payallocation_d.client_cd1, \n";      // �����襳���ɣ�
    $sql .= "   t_payallocation_d.client_cd2, \n";      // �����襳���ɣ�
    $sql .= "   t_payallocation_d.client_cname, \n";    // ������̾��ά�Ρ�
    $sql .= "   t_payallocation_d.trade_id, \n";        // �����ʬID
    $sql .= "   t_payallocation_d.amount, \n";          // ���
    $sql .= "   t_payallocation_d.note, \n";            // ����
    $sql .= "   t_payallocation_d.claim_div \n";
    $sql .= "FROM \n";
    $sql .= "   t_payallocation_d \n";
    if ($get_payin_data["bill_id"] != null){
    $sql .= "   LEFT JOIN t_bill_d ON t_payallocation_d.client_id = t_bill_d.client_id \n";
    }
    $sql .= "WHERE \n";
    $sql .= "   t_payallocation_d.pay_id = ".$_GET["payin_id"]." \n";
    if ($get_payin_data["bill_id"] != null){
    $sql .= "AND \n";
    $sql .= "   t_bill_d.claim_div IS NOT NULL \n";
    $sql .= "AND \n";
    $sql .= "   t_bill_d.bill_id = ".$get_payin_data["bill_id"]." \n";
    }
    $sql .= "ORDER BY \n";
    $sql .= "   t_payallocation_d.client_cd1, \n";
    $sql .= "   t_payallocation_d.client_cd2 \n";
    $sql .= ";";
    $res  = Db_Query($db_con, $sql);
    $num  = pg_num_rows($res);

    if ($num > 0){
        $get_allocate_data = pg_fetch_all($res);
        for ($i = 0; $i < count($get_allocate_data); $i++){
            if ($get_allocate_data[$i]["trade_id"] == 35){
                $rebate_array[] = $i;
            }
        }
        $max_row = (count($get_allocate_data) - count($rebate_array));
    }

    for ($i = 0, $j = 0; $i < count($get_allocate_data); $i++){
        if (count($rebate_array) == 0 || array_search($i,$rebate_array) === false){
            $set_form["form_claim_id"][$j]          = $get_allocate_data[$i]["client_id"];
            $set_form["form_claim_cd"][$j]["cd1"]   = $get_allocate_data[$i]["client_cd1"];
            $set_form["form_claim_cd"][$j]["cd2"]   = $get_allocate_data[$i]["client_cd2"];
            $set_form["form_claim_name"][$j]        = $get_allocate_data[$i]["client_cname"];
            $set_form["hdn_claim_div"][$j]          = $get_allocate_data[$i]["claim_div"];
            if ($get_payin_data["bill_id"] != null){
                $set_form["form_base_amount"][$j]   = number_format($get_allocate_data[$i]["payment_this"]);
            }
            $set_form["form_amount"][$j] = ($renew_flg) ? 
                ($get_allocate_data[$i]["amount"] != null) ? 
                    number_format($get_allocate_data[$i]["amount"]) : $get_allocate_data[$i]["amount"]
                : $get_allocate_data[$i]["amount"];
            $set_form["form_note"][$j] = $get_allocate_data[$i]["note"];
            $sum_amount = $sum_amount + $get_allocate_data[$i]["amount"];
            $j++;
        }else{
            if ($get_allocate_data[$i]["client_id"] == $get_allocate_data[$i+1]["client_id"]){
                $set_form["form_rebate"][$j] = ($renew_flg) ?
                    ($get_allocate_data[$i]["amount"] != null) ?
                        number_format($get_allocate_data[$i]["amount"]) : $get_allocate_data[$i]["amount"]
                    : $get_allocate_data[$i]["amount"];
                $sum_rebate = $sum_rebate + $get_allocate_data[$i]["amount"];
            }elseif ($get_allocate_data[$i]["client_id"] == $get_allocate_data[$i-1]["client_id"]){
                $set_form["form_rebate"][$j-1] = ($renew_flg) ?
                    ($get_allocate_data[$i]["amount"] != null) ? 
                        number_format($get_allocate_data[$i]["amount"]) : $get_allocate_data[$i]["amount"]
                    : $get_allocate_data[$i]["amount"];
                $sum_rebate = $sum_rebate + $get_allocate_data[$i]["amount"];
            }
        }
    }

    $set_form["form_amount_total"]  = number_format($sum_amount);
    $set_form["form_rebate_total"]  = number_format($sum_rebate);
    $set_form["form_payin_total"]   = number_format($sum_amount + $sum_rebate);
    $set_form["hdn_max_row"]        = $max_row;
    $set_form["get_flg"]            = "true";
    $form->setConstants($set_form);

}


/****************************/
// �ե�����ѡ������
/****************************/
if ($freeze_flg == true){

    for ($i = 0 ; $_POST != null && $i < $max_row; $i++){
        if ($_POST["form_amount"][$i] != null){
            $set_data["form_amount"][$i] = number_format($_POST["form_amount"][$i]);
        }
        if ($_POST["form_rebate"][$i] != null){
            $set_data["form_rebate"][$i] = number_format($_POST["form_rebate"][$i]);
        }
    }
    $set_data["form_bill_amount"] = ($_POST["form_bill_amount"] != null) ? number_format($_POST["form_bill_amount"]) : null;
    $set_data["form_bill2_amount"]= $set_data["form_bill_amount"];
    $form->setConstants($set_data);
    $form->freeze();

// ���������Ѥξ��
}elseif ($renew_flg == true){

    $set_data["form_bill_amount"] = ($bill_amount != null) ? number_format($bill_amount) : "";
    $set_data["form_bill2_amount"]= $set_data["form_bill_amount"];
    $form->setConstants($set_data);
    $form->freeze();

}elseif ($_POST["calc_flg"] != null || ($get_flg != null && $_GET[""]!= null)){

    for ($i = 0; $_POST != null && $i < $max_row; $i++){
        $set_data["form_amount"][$i] = str_replace(",", null, $_POST["form_amount"][$i]);
        $set_data["form_rebate"][$i] = str_replace(",", null, $_POST["form_rebate"][$i]);
    }
    $set_data["form_bill_amount"] = str_replace(",", null, $_POST["form_bill_amount"]);
    $set_data["form_bill2_amount"]= ($set_data["form_bill_amount"] != null) ? number_format($set_data["form_bill_amount"]) : "";
    $form->setConstants($set_data);

}

// �����踡�����
$form->addElement("link", "form_claim_search", "", "#", "������","tabindex=\"-1\"
    onClick=\"javascript:return Open_SubWin('../dialog/2-0-250.php',
    Array('form_claim[cd1]','form_claim[cd2]','form_claim[name]','hdn_claim_search_flg','hdn_claim_id'),
    500, 450, '2-405', 1);\"
");

// ������
$text = null;
$text[] =& $form->createElement("text", "cd1", "", "size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
     onChange=\"javascript:Change_Submit('hdn_claim_search_flg','#','true','form_claim[cd2]');\"
     onkeyup=\"changeText(this.form,'form_claim[cd1]','form_claim[cd2]',6);\" $g_form_option
");
$text[] =& $form->createElement("static", "", "", "-");
$text[] =& $form->createElement("text", "cd2", "", "size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
    onChange=\"javascript:Button_Submit('hdn_claim_search_flg','#','true');\" $g_form_option
");
$text[] =& $form->createElement("text", "name", "", "size=\"34\" $g_text_readonly");

if ($get_flg == true || $_POST["get_flg"] == "true"){
    $form->addGroup($text, "form_claim", "")->freeze();
}else{
    $form->addGroup($text, "form_claim", "");
}

// ����̾��1
$form->addElement("text", "form_pay_name", "", "size=\"50\" style=\"border:none;text-align:left;\" readonly");

// ����̾��2
$form->addElement("text", "form_account_name", "", "size=\"30\" style=\"border:none;text-align:left;\" readonly");

// �����ֹ�
if ($get_flg == true || $_POST["get_flg"] == "true"){
    $form->addElement("text", "form_bill_no", "", "size=\"9\" maxlength=\"8\" style=\"$g_form_style\" $g_form_option")->freeze();
}else{
    $form->addElement("text", "form_bill_no", "", "size=\"9\" maxlength=\"8\" style=\"$g_form_style\" $g_form_option");
}

// ������
$form->addElement("text", "form_bill_amount", "",
    "class=\"money\" size=\"9\" maxlength=\"8\" style=\"border:none;text-align: right; $style\" readonly"
);

// ������
Addelement_Date($form, "form_payin_day", "������", "-");

// �����ʬ
//-- 2009/06/11 ����No.3 ��31:����פ��ɲ�
$select_value_trade = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (31, 32, 33, 34, 36, 37) ");
$form->addElement("select", "form_trade", "", $select_value_trade, $g_form_option_select);

// ����ô����
//-- 2009/06/11 ����No.3 �ɲ�
$sql  = "SELECT t_attach.staff_id, t_staff.charge_cd, t_staff.staff_name ";
$sql .= "FROM   t_attach ";
$sql .= "INNER JOIN t_staff ON t_attach.staff_id = t_staff.staff_id ";
$sql .= "WHERE ";
$sql .= ($_SESSION["group_kind"] == "2") ? "   t_attach.shop_id IN (".Rank_Sql().") " 
										 : "   t_attach.shop_id = ".$_SESSION["client_id"]." ";
$sql .= "AND    t_staff.state != '�࿦' ";
$sql .= "ORDER BY charge_cd ";
$sql .= ";"; 
$result = Db_Query($db_con, $sql);
$select_value[""] = "";
while($data_list = pg_fetch_array($result)){
	$data_list[0] = htmlspecialchars($data_list[0]);
	$data_list[1] = htmlspecialchars($data_list[1]);
	$data_list[1] = str_pad($data_list[1], 4, 0, STR_POS_LEFT);
	$data_list[2] = htmlspecialchars($data_list[2]);
	$select_value[$data_list[0]] = $data_list[1]." �� ".$data_list[2];
}
if ($group_kind == "2"){
	$form->addElement("select", "form_collect_staff", "", $select_value,
		"onChange=\"window.focus();\" onkeydown=\"chgKeycode();\"");
		//"onChange=\"Staff_Select(); window.focus();\" onkeydown=\"chgKeycode();\"");
}else{
	$form->addElement("select", "form_collect_staff", "", $select_value,
		"onChange=\"window.focus();\" onkeydown=\"chgKeycode();\"");
}
//---

// ���
if (($renew_flg && $get_payin_data['bank_id'] == null) || ($freeze_flg && $_POST['form_bank'][0] == null)){
}else{
    $select_value_bank  = Make_Ary_Bank($db_con);
    $attach_html        = "��";
    $obj_bank_select =& $form->addElement("hierselect", "form_bank", "", "", $attach_html);
    $obj_bank_select->setOptions($select_value_bank);
}

// �������
Addelement_Date($form, "form_limit_day", "�������", "-");

// ��������ֹ�
$form->addElement("text", "form_bill_paper_no", "", "size=\"13\" maxLength=\"10\" style=\"$style $g_form_style\" $g_form_option");

// ����۹��
$form->addElement("text", "form_amount_total", "", "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// ��������
$form->addElement("text", "form_rebate_total", "", "size=\"16\" maxLength=\"18\"
     style=\"color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// ���
$form->addElement("text", "form_payin_total", "", "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// �����ۡʲ��̲���
$form->addElement("text", "form_bill2_amount", "", "size=\"16\" maxLength=\"18\"
     style=\"$g_form_style; color: #585858; border: #FFFFFF 1px solid; background-color: #FFFFFF; text-align: right\" readonly
");

// ��ǧ���̻�
if ($freeze_flg == true){
    // ����ܥ���
    $form->addElement("button", "hdn_ok_button", "������", "onClick=\"Double_Post_Prevent2(this);\" $disabled");
    // ����ܥ���hidden
    $form->addELement("hidden", "ok_button");
    // ���ܥ���
    $form->addElement("button", "back_button", "�ᡡ��", "onClick=\"javascript:Button_Submit('calc_flg','#','true');\"");
}

// ��ۿ�ʬ�ܥ���
$form->addElement("button", "divide_button", "��ۿ�ʬ", "onClick=\"javascript:Button_Submit('divide_flg','#','true');\"");

// ���ꥢ�ܥ���
$form->addElement("button","clear_button","���ꥢ","onclick=\"location.href='2-2-405.php'\"");

// ��ץܥ���
$form->addElement("button", "sum_button", "�硡��", "onclick=\"javascript:Button_Submit('calc_flg','#sum','true');\"");

// �����ǧ���̤إܥ���
$form->addElement("submit", "form_verify_btn", "���ϳ�ǧ���̤�");

// ���ٲ��̻������ܥ���
$form->addElement("button", "get_back_btn", "�ᡡ��", "onclick=\"location.href='2-2-403.php?search=1'\"");

// �إå�����󥯥ܥ���
$ary_h_btn_list = array("�Ȳ��ѹ�" => "./2-2-403.php", "������" => "./2-2-402.php");
Make_H_Link_Btn($form, $ary_h_btn_list, 2);

// �������ڤ��ؤ��ܥ���
$form->addElement("button", "402_button", "������ñ��",   "onClick=\"location.href='2-2-402.php'\"");
$form->addElement("button", "409_button", "���ñ��",     "onClick=\"location.href='2-2-409.php'\"");
$form->addElement("button", "405_button", "�ƻҰ������", "$g_button_color onClick=\"location.href='2-2-405.php'\"");

// hidden
$form->addElement("hidden", "hdn_max_row");             // ����Կ�
$form->addElement("hidden", "hdn_claim_search_flg");    // �����踡���ե饰
$form->addElement("hidden", "hdn_bill_no");             // �����No�ʼ�ư���Ϥ��줿�����ֹ�򥻥åȡ�
$form->addElement("hidden", "hdn_post_bill_no");        // �����No�ʥե��������Ϥ��줿�����ֹ�򥻥åȡ�
$form->addElement("hidden", "hdn_claim_id");            // ������ID
$form->addElement("hidden", "hdn_bill_id");             // ������ID����������������ID
$form->addElement("hidden", "calc_flg");                // ��ץե饰
$form->addElement("hidden", "divide_flg");              // ��ۿ�ʬ�ե饰
$form->addElement("hidden", "freeze_flg");              // �����ǧ��
$form->addelement("hidden", "hdn_payin_id");            // �ѹ�������ID
$form->addelement("hidden", "get_flg");                 // ����/�ѹ�Ƚ����
$form->addelement("hidden", "hdn_enter_day");           // ��ɼ�ѹ�����������ɼ�κ����������ݻ����Ƥ���

// ���顼���å���
$form->addElement("text", "err_illegal_post");
$form->addElement("text", "err_claim");
$form->addElement("text", "err_count");


/****************************/
// ���ѥե�����ѡ��������ɽ��
/****************************/
// ���쥯�ȥܥå��������ƥ���������
$select_value_trade = null;
$select_value_trade = Select_Get($db_con, "trade_payin", " WHERE t_trade.trade_id IN (32, 33, 34, 35, 36, 37, 38) ");
$select_value_bank  = Make_Ary_Bank($db_con);
// ���hierselect��Ϣ��html
$attach_html        = "</td><td>";

// �ե�����θ����ܤ�static�äݤ����뤿���CSS���ѿ�������Ƥ�����Ĺ���Τǡ�
$style = "color: #585858; border: #ffffff 1px solid; background-color: #ffffff;";

for ($i=0; $i<$max_row; $i++){

    // �����襳����
    $text = null;
    $text[] =& $form->addElement("text", "cd1", "",
        "size=\"7\" maxLength=\"6\" style=\"border:none\" readonly");
    $text[] =& $form->addElement("static", "", "", "-");
    $text[] =& $form->addElement("text", "cd2", "",
        "size=\"4\" maxLength=\"4\" style=\"border:none\" readonly");
    $form->addGroup($text, "form_claim_cd[$i]", "");

    // ������̾
    $text[] =& $form->addElement("text", "form_claim_name[$i]", "",
        "size=\"34\" style=\"color : #000000; border : #ffffff 1px solid; background-color: #ffffff;\" readonly"
     );
    //�����
    $form->addElement("text","form_base_amount[$i]","","size=11 style=\"text-align:right;border:none\"  readonly");
    // ���
    $form->addElement("text", "form_amount[$i]", "",
        "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
    );

    // �����
    $form->addElement("text", "form_rebate[$i]", "",
        "class=\"money\" size=\"11\" maxLength=\"9\" style=\"text-align: right; $g_form_style\" $g_form_option"
    );
    // ����
    $form->addElement("text", "form_note[$i]", "", "size=\"34\" maxLength=\"20\" $g_form_option\"");

    //hidden��������ID��
    $form->addElement("hidden","form_claim_id[$i]");

    //hidden���������ʬ��
    $form->addElement("hidden", "hdn_claim_div[$i]");

}


/****************************/
// ������ξ��ּ���
/****************************/
// �����踡���ե饰��������������踡������
if ($_POST["hdn_claim_search_flg"] == "true"){
    $claim_state_print = Get_Client_State($db_con, $search_claim_id);
}else
// hidden��������ID��������ʥ�������������������踡��������ʬ�ʤɤ�POST����
if ($_POST["hdn_claim_id"] != null){
    $claim_state_print = Get_Client_State($db_con, $_POST["hdn_claim_id"]);
}else
// GET������ID��������������ѹ�����
if ($_GET["payin_id"] != null){
    $claim_state_print = Get_Client_State($db_con, $get_payin_data["client_id"]);
}


/****************************/
// ɽ����html����
/****************************/
// html���ѿ����
$html = null;

// ����Կ�ʬ�ʺ���ѹԴޤ�˥롼��
for ($i=0, $j=0; $i<$max_row; $i++){

    // html����
    $html .= "<tr class=\"Result1\">\n";
    $html .= "  <td align=\"right\">".++$j."</td>\n";                                               // ���ֹ�
    $html .= "  <td>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_claim_cd[$i]"]]->toHtml();          // �����襳����
    $html .= "      <br>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_claim_name[$i]"]]->toHtml();        // ������̾
    $html .= "  </td>\n";
    $html .= ($freeze_flg || $renew_flg) ? "    <td align=right>\n":"  <td align=center>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_base_amount[$i]"]]->toHtml();       // �Ϥ����ɽ�����Ƥ�����
    $html .= "   </td>\n";
    $html .= ($freeze_flg || $renew_flg) ? "    <td align=right>\n":"  <td align=center>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_amount[$i]"]]->toHtml();            // ���
    $html .= "   </td>\n";
    $html .= ($freeze_flg || $renew_flg) ? "    <td align=right>\n":"   <td align=center>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_rebate[$i]"]]->toHtml();            // �����
    $html .= "  </td>\n";
    $html .= "  <td>\n";
    $html .=        $form->_elements[$form->_elementIndex["form_note[$i]"]]->toHtml();              // ����
    $html .= "  </td>\n";
    $html .= "</tr>\n";

}


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
$page_menu = Create_Menu_f("sale", "4");

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
    "divide_flg"    => "$divide_flg",
    "renew_flg"     => "$renew_flg",
    "freeze_flg"    => "$freeze_flg",
    "get_flg"       => "$get_flg",

    "illegal_post_flg"  => "$illegal_post_flg",
    "divide_null_flg"   => "$divide_null_flg",
    "claim_state_print" => "$claim_state_print",
));

$smarty->assign("amount_num_err_msg",$amount_num_err_msg);
// �ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>
