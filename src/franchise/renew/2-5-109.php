<?php

/*
 *  ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 * 
 *  2009-10-12                  aoyama-n    ���״��ֳ����������ϲ�ǽ�˽���
 * 
 * 
 */

$page_title = "�Хå�ɽ";

// �Ķ�����ե�����
require_once("ENV_local.php");

// HTML_QuickForm�����
$form =& new HTML_QuickForm( "$_SERVER[PHP_SELF]","POST");

// DB��³����
$db_con = Db_Connect();


/****************************/
// ���´�Ϣ����
/****************************/
$auth   = Auth_Check($db_con);


/****************************/
// �����ѿ�����
/****************************/
$shop_id    = $_SESSION["client_id"];
$group_kind = $_SESSION["group_kind"];
$end_day    = $_POST["end_day"];
$staff_id   = null;
$branch_id  = $_POST["form_branch"];


//2009-10-12 aoyama-n
#$end_y      = ($_POST["hdn_end_day"]["y"] != null) ? str_pad($_POST["hdn_end_day"]["y"], 4, 0, STR_POS_LEFT) : null; 
#$end_m      = ($_POST["hdn_end_day"]["m"] != null) ? str_pad($_POST["hdn_end_day"]["m"], 2, 0, STR_POS_LEFT) : null; 
#$end_d      = ($_POST["hdn_end_day"]["d"] != null) ? str_pad($_POST["hdn_end_day"]["d"], 2, 0, STR_POS_LEFT) : null; 
#$end_day    = ($end_y != null && $end_m != null && $end_d != null) ? $end_y."-".$end_m."-".$end_d : null; 


//2009-10-12 aoyama-n
//���ɽ����2-5-105.php�������ܡ�
if($_POST["form_start_day"] != null && $_POST["form_end_day"] != null){
    $start_y    = ($_POST["form_start_day"]["y"] != null) ? str_pad($_POST["form_start_day"]["y"], 4, 0, STR_POS_LEFT) : null; 
    $start_m    = ($_POST["form_start_day"]["m"] != null) ? str_pad($_POST["form_start_day"]["m"], 2, 0, STR_POS_LEFT) : null; 
    $start_d    = ($_POST["form_start_day"]["d"] != null) ? str_pad($_POST["form_start_day"]["d"], 2, 0, STR_POS_LEFT) : null; 
    $start_day  = ($start_y != null && $start_m != null && $start_d != null) ? $start_y."-".$start_m."-".$start_d : null; 
    $end_y      = ($_POST["form_end_day"]["y"] != null) ? str_pad($_POST["form_end_day"]["y"], 4, 0, STR_POS_LEFT) : null; 
    $end_m      = ($_POST["form_end_day"]["m"] != null) ? str_pad($_POST["form_end_day"]["m"], 2, 0, STR_POS_LEFT) : null; 
    $end_d      = ($_POST["form_end_day"]["d"] != null) ? str_pad($_POST["form_end_day"]["d"], 2, 0, STR_POS_LEFT) : null; 
    $end_day    = ($end_y != null && $end_m != null && $end_d != null) ? $end_y."-".$end_m."-".$end_d : null; 
//ɽ���ܥ��󤬲����줿���
}else{
    $start_y    = ($_POST["hdn_start_day"]["y"] != null) ? str_pad($_POST["hdn_start_day"]["y"], 4, 0, STR_POS_LEFT) : null; 
    $start_m    = ($_POST["hdn_start_day"]["m"] != null) ? str_pad($_POST["hdn_start_day"]["m"], 2, 0, STR_POS_LEFT) : null; 
    $start_d    = ($_POST["hdn_start_day"]["d"] != null) ? str_pad($_POST["hdn_start_day"]["d"], 2, 0, STR_POS_LEFT) : null; 
    $start_day  = ($start_y != null && $start_m != null && $start_d != null) ? $start_y."-".$start_m."-".$start_d : null; 
    $end_y      = ($_POST["hdn_end_day"]["y"] != null) ? str_pad($_POST["hdn_end_day"]["y"], 4, 0, STR_POS_LEFT) : null; 
    $end_m      = ($_POST["hdn_end_day"]["m"] != null) ? str_pad($_POST["hdn_end_day"]["m"], 2, 0, STR_POS_LEFT) : null; 
    $end_d      = ($_POST["hdn_end_day"]["d"] != null) ? str_pad($_POST["hdn_end_day"]["d"], 2, 0, STR_POS_LEFT) : null; 
    $end_day    = ($end_y != null && $end_m != null && $end_d != null) ? $end_y."-".$end_m."-".$end_d : null; 
}

/****************************/
// ľ����к�
/****************************/
if ($_POST == null){
    header("Location: ../top.php");
}


/****************************/
// �ե�������������
/****************************/
// POST�ǡ��������ꡢɽ���ܥ���̤�������ʻ�Ź���ٲ��̤���POST������줿����
if ($_POST != null && $_POST["form_show_button"] == null){
    //2009-10-12 aoyama-n
    #$def_data["form_branch"] = $_POST["hdn_branch"];
    $def_data["form_branch"] = $_POST["form_branch"];
    $form->setConstants($def_data);
}


/****************************/
// �ե�����ѡ������
/****************************/
// ��Ź
$select_value   = "";
//$select_value   = array(null);
$select_value   = Select_Get($db_con, "branch");
$form->addElement("select", "form_branch", "", $select_value, "$g_form_option_select");

// ɽ���ܥ���
$form->addElement("submit", "form_show_button", "ɽ����");

// ���ܥ���
$form->addElement("button", "form_return_button", "��Ź���٤����", "onClick=\"javascript: Submit_Page('./2-5-105.php');\"");

//2009-10-12 aoyama-n
// hidden ���״���(����)
$hdn_start_day[] = $form->createElement("hidden", "y", "", "");
$hdn_start_day[] = $form->createElement("hidden", "m", "", "");
$hdn_start_day[] = $form->createElement("hidden", "d", "", "");
$form->addGroup($hdn_start_day, "hdn_start_day", "", " - ");

// hidden ���״���
$hdn_end_day[] = $form->createElement("hidden", "y", "", "");
$hdn_end_day[] = $form->createElement("hidden", "m", "", "");
$hdn_end_day[] = $form->createElement("hidden", "d", "", "");
$form->addGroup($hdn_end_day, "hdn_end_day", "", " - ");

// hidden ��Ź
$form->addElement("hidden", "hdn_branch");


/****************************/
// ����/������������ؿ�
/****************************/
function Renew_Day($db_con, $renew_type, $shop_id){

    // ����/�Ƚ��
    $renew_div = ($renew_type == "daily") ? "1" : "2";

    // �ǽ�����������
    $sql  = "SELECT \n";
    $sql .= "   MAX(close_day) \n";
    $sql .= "FROM \n";
    $sql .= "   t_sys_renew \n";
    $sql .= "WHERE \n";
    $sql .= "   renew_div = '".$renew_div."' \n";
    $sql .= "AND \n";
    $sql .= "   shop_id = $shop_id \n";
    $sql .= ";"; 
    $res  = Db_Query($db_con, $sql);
    $renew_day = pg_fetch_result($res, 0);
    if ($renew_day != null){
        $ary_renew_day = explode("-", $renew_day);
        $renew_day = date("Y-m-d", mktime(0, 0, 0, $ary_renew_day[1] , $ary_renew_day[2]+1, $ary_renew_day[0]));
    }else{
        $renew_day = START_DAY;
    }

    // ���դ��֤�
    return $renew_day;

}

/****************************/
// ������ǡ�������
/****************************/
// �ǽ������
//2009-10-12 aoyama-n
#$monthly_renew_day  = Renew_Day($db_con, "monthly", $shop_id);


/****************************/
// ɽ���ܥ��󲡲�������
/****************************/
//2009-10-12 aoyama-n
#if (isset($_POST["form_show_button"])){

    //2009-10-12 aoyama-n
    #$start_y = ($_POST["hdn_start_day"]["y"] != null) ? str_pad($_POST["hdn_start_day"]["y"], 4, 0, STR_POS_LEFT) : null;
    #$start_m = ($_POST["hdn_start_day"]["m"] != null) ? str_pad($_POST["hdn_start_day"]["m"], 2, 0, STR_POS_LEFT) : null;
    #$start_d = ($_POST["hdn_start_day"]["d"] != null) ? str_pad($_POST["hdn_start_day"]["d"], 2, 0, STR_POS_LEFT) : null;
    #$start_day = ($start_y != null && $start_m != null && $start_d != null) ? $start_y."-".$start_m."-".$start_d : null;

    // POST���줿���դ�����
    //2009-10-12 aoyama-n
    #$end_y = ($_POST["hdn_end_day"]["y"] != null) ? str_pad($_POST["hdn_end_day"]["y"], 4, 0, STR_POS_LEFT) : null;
    #$end_m = ($_POST["hdn_end_day"]["m"] != null) ? str_pad($_POST["hdn_end_day"]["m"], 2, 0, STR_POS_LEFT) : null;
    #$end_d = ($_POST["hdn_end_day"]["d"] != null) ? str_pad($_POST["hdn_end_day"]["d"], 2, 0, STR_POS_LEFT) : null;

    // YYYY-MM-DD�η��ˤ��Ƥ���
    //2009-10-12 aoyama-n
    #$end_day = ($end_y != null && $end_m != null && $end_d != null) ? $end_y."-".$end_m."-".$end_d : null;

    // hidden�˻�ŹID�򥻥å�
    $hdn_set["hdn_branch"] = $_POST["form_branch"];

    //2009-10-12 aoyama-n
    // hidden�˽��״��֤򥻥å�
    $hdn_set["hdn_start_day"]["y"] = $start_y;
    $hdn_set["hdn_start_day"]["m"] = $start_m;
    $hdn_set["hdn_start_day"]["d"] = $start_d;

    $hdn_set["hdn_end_day"]["y"] = $end_y;
    $hdn_set["hdn_end_day"]["m"] = $end_m;
    $hdn_set["hdn_end_day"]["d"] = $end_d;

    $form->setConstants($hdn_set);

#}


/****************************/
// �������Ѵؿ�
/****************************/
/* ����ɽ����ô��������Ѷ�ۼ���SQL */
function Staff_Amount_Sql($renew_type, $staff_id, $shop_id, $branch_id, $monthly_renew_day, $end_day){

    $sql  = "SELECT \n";
    $sql .= "   t_attach_staff.staff_id, \n";
    $sql .= "   t_attach_staff.staff_name, \n";
    $sql .= "   COALESCE(t_sale_data.sale_genkin_amount, 0)     AS sale_genkin_amount, \n";
    $sql .= "   COALESCE(t_sale_data.sale_kake_amount, 0)       AS sale_kake_amount, \n";
    $sql .= "   COALESCE(t_sale_data.sale_tax_amount, 0)        AS sale_tax_amount, \n";
    $sql .= "   COALESCE(t_payin_data.payin_collect_amount, 0)  AS payin_collect_amount, \n";
    $sql .= "   COALESCE(t_payin_data.payin_uriage_amount, 0)   AS payin_uriage_amount, \n";
    $sql .= "   COALESCE(t_payin_data.payin_advance_amount, 0)  AS payin_advance_amount \n";
    $sql .= "FROM \n";
    // ��°
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_shop_union.shop_id, \n";
    $sql .= "           t_shop_union.staff_id, \n";
    $sql .= "           t_staff.charge_cd, \n";
    $sql .= "           t_staff.staff_name \n";
    $sql .= "       FROM \n";
    $sql .= "           ( \n";
    $sql .= "               ( \n";
    $sql .= "                   SELECT \n";
    $sql .= "                       t_sale_h_amount.shop_id, \n";
    $sql .= "                       t_sale_h_amount.main_staff_id AS staff_id \n";
    $sql .= "                   FROM \n";
    $sql .= "                       t_sale_h_amount \n";
    if ($branch_id != null){
    $sql .= "                       INNER JOIN t_client \n";
    $sql .= "                       ON t_sale_h_amount.client_id = t_client.client_id AND t_client.charge_branch_id = $branch_id \n";
    }
    $sql .= "                   WHERE \n";
    $sql .= "                       t_sale_h_amount.shop_id = $shop_id \n";
    if ($renew_type == "daily"){
    $sql .= "                   AND \n";
    $sql .= "                       t_sale_h_amount.renew_flg = 'f' \n";
    }else
    if ($renew_type == "halfway"){
    $sql .= "                   AND \n";
    $sql .= "                       t_sale_h_amount.renew_flg = 't' \n";
    }
    $sql .= "                   AND \n";
    $sql .= "                       t_sale_h_amount.trade_id IN (61, 63, 64, 11, 15, 13, 14) \n";
    $sql .= "                   AND \n";
    $sql .= "                       t_sale_h_amount.contract_div = '1' \n";
    $sql .= "                   AND \n";
    $sql .= "                       t_sale_h_amount.sale_day >= '$monthly_renew_day' \n";
    $sql .= "                   AND \n";
    $sql .= "                       t_sale_h_amount.sale_day <= '$end_day' \n";
    $sql .= "               ) \n";
    $sql .= "               UNION ALL \n";
    $sql .= "               ( \n";
    $sql .= "                   SELECT \n";
    $sql .= "                       t_payin_h.shop_id, \n";
    $sql .= "                       t_payin_h.collect_staff_id AS staff_id \n";
    $sql .= "                   FROM \n";
    $sql .= "                       t_payin_h \n";
    $sql .= "                       INNER JOIN t_payin_d ON t_payin_h.pay_id = t_payin_d.pay_id \n";
    if ($branch_id != null){
    $sql .= "                       INNER JOIN t_client \n";
    $sql .= "                       ON t_payin_h.client_id = t_client.client_id AND t_client.charge_branch_id = $branch_id \n";
    }
    $sql .= "                   WHERE \n";
    $sql .= "                       t_payin_h.shop_id = $shop_id \n";
    if ($renew_type == "daily"){
    $sql .= "                   AND \n";
    $sql .= "                       t_payin_h.renew_flg = 'f' \n";
    }else
    if ($renew_type == "halfway"){
    $sql .= "                   AND \n";
    $sql .= "                       t_payin_h.renew_flg = 't' \n";
    }
    $sql .= "                   AND \n";
    $sql .= "                       t_payin_d.trade_id IN (31, 39, 40) \n";
    $sql .= "                   AND \n";
    $sql .= "                       t_payin_h.act_client_id IS NULL \n";
    $sql .= "                   AND \n";
    $sql .= "                       t_payin_h.pay_day >= '$monthly_renew_day' \n";
    $sql .= "                   AND \n";
    $sql .= "                       t_payin_h.pay_day <= '$end_day' \n";
    $sql .= "               ) \n";
    $sql .= "           ) \n";
    $sql .= "           AS t_shop_union \n";
    $sql .= "           LEFT JOIN t_staff ON t_shop_union.staff_id = t_staff.staff_id \n";
    if ($staff_id != null){
    $sql .= "       WHERE \n";
    $sql .= "           t_shop_union.staff_id = $staff_id \n";
    }
    $sql .= "       GROUP BY \n";
    $sql .= "           t_shop_union.shop_id, \n";
    $sql .= "           t_shop_union.staff_id, \n";
    $sql .= "           t_staff.charge_cd, \n";
    $sql .= "           t_staff.staff_name \n";
    $sql .= "       ORDER BY \n";
    $sql .= "           t_staff.charge_cd \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_attach_staff \n";
    // ���إå�
    $sql .= "   LEFT JOIN \n";
    $sql .= "   (\n";
    $sql .= "       SELECT \n";
    $sql .= "           shop_id, \n";
    $sql .= "           staff_id, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (61) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * 1 \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (63, 64) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * -1 \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS sale_genkin_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (11, 15) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * 1 \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (13, 14) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * -1 \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS sale_kake_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (61, 11, 15) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(tax_amount, 0) * 1 \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (63, 64, 13, 14) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(tax_amount, 0) * -1 \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS sale_tax_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           ( \n";
    $sql .= "               SELECT \n";
    $sql .= "                   t_sale_h_amount.shop_id, \n";
    $sql .= "                   t_sale_h_amount.trade_id, \n";
    $sql .= "                   t_sale_h_amount.main_staff_id AS staff_id, \n";
    $sql .= "                   COALESCE(t_sale_h_amount.all_net_amount, 0) AS net_amount, \n";
    $sql .= "                   COALESCE(t_sale_h_amount.all_tax_amount, 0) AS tax_amount \n";
    $sql .= "               FROM \n";
    $sql .= "                   t_sale_h_amount \n";
    if ($branch_id != null){
    $sql .= "                   INNER JOIN t_client \n";
    $sql .= "                   ON t_sale_h_amount.client_id = t_client.client_id AND t_client.charge_branch_id = $branch_id \n";
    }
    $sql .= "               WHERE \n";
    $sql .= "                   t_sale_h_amount.shop_id = $shop_id \n";
    if ($renew_type == "daily"){
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.renew_flg = 'f' \n";
    }else
    if ($renew_type == "halfway"){
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.renew_flg = 't' \n";
    }
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.trade_id IN (61, 63, 64, 11, 15, 13, 14) \n";
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.contract_div = '1' \n";
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.sale_day >= '$monthly_renew_day' \n";
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.sale_day <= '$end_day' \n";
    $sql .= "           ) \n";
    $sql .= "           AS t_sale_union \n";
    $sql .= "       GROUP BY \n";
    $sql .= "           shop_id, \n";
    $sql .= "           staff_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_sale_data \n";
    $sql .= "   ON t_attach_staff.shop_id = t_sale_data.shop_id \n";
    $sql .= "   AND t_attach_staff.staff_id = t_sale_data.staff_id \n";
    // ����إå�(join ����ǡ���)
    $sql .= "   LEFT JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_payin_h.shop_id, \n";
    $sql .= "           t_payin_h.collect_staff_id AS collect_staff_id, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       t_payin_d.trade_id = 31 \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(t_payin_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS payin_collect_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       t_payin_d.trade_id = 39 \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(t_payin_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS payin_uriage_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       t_payin_d.trade_id = 40 \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(t_payin_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS payin_advance_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           t_payin_h \n";
    $sql .= "           INNER JOIN t_payin_d ON t_payin_h.pay_id = t_payin_d.pay_id \n";
    if ($branch_id != null){
    $sql .= "           INNER JOIN t_client \n";
    $sql .= "           ON t_payin_h.client_id = t_client.client_id AND t_client.charge_branch_id = $branch_id \n";
    }
    $sql .= "       WHERE \n";
    $sql .= "           t_payin_h.shop_id = $shop_id \n";
    if ($renew_type == "daily"){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = 'f' \n";
    }else
    if ($renew_type == "halfway"){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = 't' \n";
    }
    $sql .= "       AND \n";
    $sql .= "           t_payin_d.trade_id IN (31, 39, 40) \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.act_client_id IS NULL \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day >= '$monthly_renew_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day <= '$end_day' \n";
    $sql .= "       GROUP BY \n";
    $sql .= "           t_payin_h.shop_id, \n";
    $sql .= "           t_payin_h.collect_staff_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_payin_data \n";
    $sql .= "   ON t_attach_staff.shop_id = t_payin_data.shop_id \n";
    $sql .= "   AND t_attach_staff.staff_id = t_payin_data.collect_staff_id \n";
    $sql .= "ORDER BY \n";
    $sql .= "   t_attach_staff.charge_cd \n";
    $sql .= ";";

    // ��������֤�
    return $sql;

}

/* ����ɽ������ԡ��Ѷ�ۼ���SQL */
function Act_Amount_Sql($renew_type, $shop_id, $branch_id, $monthly_renew_day, $end_day){

    $sql  = "SELECT \n";
    $sql .= "   COALESCE(t_sale_data.sale_genkin_amount, 0)     AS sale_genkin_amount, \n";
    $sql .= "   COALESCE(t_sale_data.sale_kake_amount, 0)       AS sale_kake_amount, \n";
    $sql .= "   COALESCE(t_sale_data.sale_tax_amount, 0)        AS sale_tax_amount, \n";
    $sql .= "   COALESCE(t_payin_data.payin_collect_amount, 0)  AS payin_collect_amount, \n";
    $sql .= "   COALESCE(t_payin_data.payin_advance_amount, 0)  AS payin_advance_amount \n";
    $sql .= "FROM \n";
    // �����ޥ���
    $sql .= "   t_client \n";
    // ���إå�
    $sql .= "   LEFT JOIN \n";
    $sql .= "   (\n";
    $sql .= "       SELECT \n";
    $sql .= "           t_sale_union.shop_id, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (61) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * 1 \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (63, 64) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * -1 \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS sale_genkin_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (11, 15) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * 1 \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (13, 14) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(net_amount, 0) * -1 \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS sale_kake_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (61, 11, 15) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(tax_amount, 0) * 1 \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       trade_id IN (63, 64, 13, 14) \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(tax_amount, 0) * -1 \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS sale_tax_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           ( \n";
    $sql .= "               SELECT \n";
    $sql .= "                   t_sale_h_amount.shop_id, \n";
    $sql .= "                   t_sale_h_amount.trade_id, \n";
    $sql .= "                   COALESCE(t_sale_h_amount.all_net_amount, 0) AS net_amount, \n";
    $sql .= "                   COALESCE(t_sale_h_amount.all_tax_amount, 0) AS tax_amount \n";
    $sql .= "               FROM \n";
    $sql .= "                   t_sale_h_amount \n";
    if ($branch_id != null){
    $sql .= "                   INNER JOIN t_client \n";
    $sql .= "                   ON t_sale_h_amount.client_id = t_client.client_id AND t_client.charge_branch_id = $branch_id \n";
    }
    $sql .= "               WHERE \n";
    $sql .= "                   t_sale_h_amount.shop_id = $shop_id \n";
    if ($renew_type == "daily"){
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.renew_flg = 'f' \n";
    }else
    if ($renew_type == "halfway"){
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.renew_flg = 't' \n";
    }
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.trade_id IN (61, 63, 64, 11, 15, 13, 14) \n";
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.contract_div != '1' \n";
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.sale_day >= '$monthly_renew_day' \n";
    $sql .= "               AND \n";
    $sql .= "                   t_sale_h_amount.sale_day <= '$end_day' \n";
    $sql .= "           ) \n";
    $sql .= "           AS t_sale_union \n";
    $sql .= "       GROUP BY \n";
    $sql .= "           t_sale_union.shop_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_sale_data \n";
    $sql .= "   ON t_client.client_id = t_sale_data.shop_id \n";
    // ����إå�(join ����ǡ���)
    $sql .= "   LEFT JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_payin_h.shop_id, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       t_payin_d.trade_id = 31 \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(t_payin_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS payin_collect_amount, \n";
    $sql .= "           SUM( \n";
    $sql .= "               CASE \n";
    $sql .= "                   WHEN \n";
    $sql .= "                       t_payin_d.trade_id = 40 \n";
    $sql .= "                   THEN \n";
    $sql .= "                       COALESCE(t_payin_d.amount, 0) \n";
    $sql .= "                   ELSE 0 \n";
    $sql .= "               END \n";
    $sql .= "           ) \n";
    $sql .= "           AS payin_advance_amount \n";
    $sql .= "       FROM \n";
    $sql .= "           t_payin_h \n";
    $sql .= "           INNER JOIN t_payin_d ON t_payin_h.pay_id = t_payin_d.pay_id \n";
    if ($branch_id != null){
    $sql .= "           INNER JOIN t_client \n";
    $sql .= "           ON t_payin_h.client_id = t_client.client_id AND t_client.charge_branch_id = $branch_id \n";
    }
    $sql .= "       WHERE \n";
    $sql .= "           t_payin_h.shop_id = $shop_id \n";
    if ($renew_type == "daily"){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = 'f' \n";
    }else
    if ($renew_type == "halfway"){
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.renew_flg = 't' \n";
    }
    $sql .= "       AND \n";
    $sql .= "           t_payin_d.trade_id IN (31, 40) \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day >= '$monthly_renew_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.pay_day <= '$end_day' \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.act_client_id IS NOT NULL \n";
    $sql .= "       AND \n";
    $sql .= "           t_payin_h.collect_staff_id IS NULL \n";
    $sql .= "       GROUP BY \n";
    $sql .= "           t_payin_h.shop_id \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_payin_data \n";
    $sql .= "   ON t_client.client_id = t_payin_data.shop_id \n";
    $sql .= "WHERE \n";
    $sql .= "   t_client.client_id = $shop_id \n";
    $sql .= ";";

    // ��������֤�
    return $sql;

}


/****************************/
// ����ɽ���ѥǡ�������
/****************************/
// ���顼��̵�����
if ($err_flg != true){

    // ô������ ����̤�»��߷ץǡ�������
    //2009-10-12 aoyama-n
    #$sql                = Staff_Amount_Sql("daily",   $staff_id, $shop_id, $branch_id, $monthly_renew_day, $end_day);
    $sql                = Staff_Amount_Sql("daily",   $staff_id, $shop_id, $branch_id, $start_day, $end_day);
    $res_staff_daily    = Db_Query($db_con, $sql);
    $num_staff_daily    = pg_num_rows($res_staff_daily);

    // ô������ �����»ܺ��߷ץǡ�������
    //2009-10-12 aoyama-n
    #$sql                = Staff_Amount_Sql("halfway", $staff_id, $shop_id, $branch_id, $monthly_renew_day, $end_day);
    $sql                = Staff_Amount_Sql("halfway", $staff_id, $shop_id, $branch_id, $start_day, $end_day);
    $res_staff_halfway  = Db_Query($db_con, $sql);
    $num_staff_halfway  = pg_num_rows($res_staff_halfway);

    // ô������ ��סʷ�߷ס˥ǡ�������
    //2009-10-12 aoyama-n
    #$sql                = Staff_Amount_Sql("monthly", $staff_id, $shop_id, $branch_id, $monthly_renew_day, $end_day);
    $sql                = Staff_Amount_Sql("monthly", $staff_id, $shop_id, $branch_id, $start_day, $end_day);
    $res_staff_monthly  = Db_Query($db_con, $sql);
    $num_staff_monthly  = pg_num_rows($res_staff_monthly);

    // ��� ����̤�»��߷ץǡ�������
    //2009-10-12 aoyama-n
    #$sql                = Act_Amount_Sql("daily",   $shop_id, $branch_id, $monthly_renew_day, $end_day);
    $sql                = Act_Amount_Sql("daily",   $shop_id, $branch_id, $start_day, $end_day);
    $res_act_daily      = Db_Query($db_con, $sql);
    $num_act_daily      = pg_num_rows($res_act_daily);

    // ��� �����»ܺ��߷ץǡ�������
    //2009-10-12 aoyama-n
    #$sql                = Act_Amount_Sql("halfway", $shop_id, $branch_id, $monthly_renew_day, $end_day);
    $sql                = Act_Amount_Sql("halfway", $shop_id, $branch_id, $start_day, $end_day);
    $res_act_halfway    = Db_Query($db_con, $sql);
    $num_act_halfway    = pg_num_rows($res_act_halfway);

    // ��� ��סʷ�߷ס˥ǡ�������
    //2009-10-12 aoyama-n
    #$sql                = Act_Amount_Sql("monthly", $shop_id, $branch_id, $monthly_renew_day, $end_day);
    $sql                = Act_Amount_Sql("monthly", $shop_id, $branch_id, $start_day, $end_day);
    $res_act_monthly    = Db_Query($db_con, $sql);
    $num_act_monthly    = pg_num_rows($res_act_monthly);

}


/****************************/
// �����ǡ��������Ѵؿ�
/****************************/
function Make_Data($res_staff, $num_staff, $res_act, $num_act){

    /* ô������ǡ��������� */
    // �ǡ�����0��Ǥʤ����
    if ($num_staff != 0){

        // �����ǡ���������������ʥ��˥������ù���
        $ary_staff = Get_Data($res_staff, "2", "ASSOC");

        // �����ѥǡ�������˥ǡ���������
        foreach ($ary_staff as $key => $value){
            // ô����ID
            $disp_data[$key][0] = $value["staff_id"];
            // ô����̾
            $disp_data[$key][1] = $value["staff_name"];
            // �������
            $disp_data[$key][2] = $value["sale_genkin_amount"];
            // �����
            $disp_data[$key][3] = $value["sale_kake_amount"];
            // ����ס���ȴ��
            $disp_data[$key][4] = $value["sale_genkin_amount"] + $value["sale_kake_amount"];
            // ������
            $disp_data[$key][5] = $value["sale_tax_amount"];
            // ����ס��ǹ���
            $disp_data[$key][6] = $value["sale_genkin_amount"] + $value["sale_kake_amount"] + $value["sale_tax_amount"];
            // ����
            $disp_data[$key][7] = $value["payin_collect_amount"];
            // ����������
            $disp_data[$key][8] = $value["payin_collect_amount"] + $value["payin_uriage_amount"];
            // �����껦
            $disp_data[$key][9] = $value["payin_advance_amount"];
        }

    // �ǡ�����0��ξ��
    }else{

        $disp_data = null;

    }

    /* ��ԥǡ��������� */
    // �ǡ�����0��Ǥʤ����
    if ($num_act != 0){

        // �����ǡ���������������ʥ��˥������ù���
        $ary_act = Get_Data($res_act, "2", "ASSOC");

        // �����ѥǡ�������ιԿ���ǧ
        $count = count($disp_data);

        // ô����ID
        $disp_data[$count][0] = 0;
        // ô����̾
        $disp_data[$count][1] = "���";
        // �������
        $disp_data[$count][2] = $ary_act[0]["sale_genkin_amount"];
        // �����
        $disp_data[$count][3] = $ary_act[0]["sale_kake_amount"];
        // ����ס���ȴ��
        $disp_data[$count][4] = $ary_act[0]["sale_genkin_amount"] + $ary_act[0]["sale_kake_amount"];
        // ������
        $disp_data[$count][5] = $ary_act[0]["sale_tax_amount"];
        // ����ס��ǹ���
        $disp_data[$count][6] = $ary_act[0]["sale_genkin_amount"] + $ary_act[0]["sale_kake_amount"] + $ary_act[0]["sale_tax_amount"];
        // ����
        $disp_data[$count][7] = $ary_act[0]["payin_collect_amount"];
        // ����������
        $disp_data[$count][8] = $ary_act[0]["payin_collect_amount"];
        // �����껦
        $disp_data[$count][9] = ($_SESSION["group_kind"] == "2") ? $ary_act[0]["payin_advance_amount"] : "-";

    // �ǡ�����0��ξ��
    }else{

        // �����ѥǡ�������ιԿ���ǧ
        $count = count($disp_data);

        // ô����ID
        $disp_data[$count][0] = 0;
        // ô����̾
        $disp_data[$count][1] = "���";
        // �������
        $disp_data[$count][2] = 0;
        // �����
        $disp_data[$count][3] = 0;
        // ����ס���ȴ��
        $disp_data[$count][4] = 0;
        // ������
        $disp_data[$count][5] = 0;
        // ����ס��ǹ���
        $disp_data[$count][6] = 0;
        // ����
        $disp_data[$count][7] = 0;
        // ����������
        $disp_data[$count][8] = 0;
        // �����껦
        $disp_data[$count][9] = ($_SESSION["group_kind"] == "2") ? 0 : "-";

    }

    /* ����̤�»��߷׹�ץǡ��������� */
    // �����ѥǡ������󤬤�����
    if (count($disp_data) != 0){

        // �����ѥǡ�������ιԿ���ǧ
        $count = count($disp_data);

        // ô����ID
        $disp_data[$count][0] = "";
        // ô����̾
        $disp_data[$count][1] = "���";

        // ���Ϥ�������ͤ��פ��������ѥǡ�������κǸ���ɲ�
        foreach ($disp_data as $key => $value){
            $disp_data[$count][2] += (int)$value[2];
            $disp_data[$count][3] += (int)$value[3];
            $disp_data[$count][4] += (int)$value[4];
            $disp_data[$count][5] += (int)$value[5];
            $disp_data[$count][6] += (int)$value[6];
            $disp_data[$count][7] += (int)$value[7];
            $disp_data[$count][8] += (int)$value[8];
            $disp_data[$count][9] += (int)$value[9];
        }

    }

    // �����ѥǡ���������֤�
    return $disp_data;

}

/****************************/
// �����ǡ���������Ѥ�����
/****************************/
// ���顼��̵�����
if ($err_flg != true){

    /* ����̤�»��߷�ô������ǡ��������� */
    $disp_daily_data   = Make_Data($res_staff_daily,   $num_staff_daily,   $res_act_daily,   $num_act_daily);

    /* �����»ܺ��߷�ô������ǡ��������� */
    $disp_halfway_data = Make_Data($res_staff_halfway, $num_staff_halfway, $res_act_halfway, $num_act_halfway);

    /* ��סʷ�߷ס�ô������ǡ��������� */
    $disp_monthly_data = Make_Data($res_staff_monthly, $num_staff_monthly, $res_act_monthly, $num_act_monthly);

}


/****************************/
// ���������ǡ�������Ϥ���
/****************************/
// ��ۤ�����ξ��˥������ʸ�������֤ˤ���ؿ�
function Font_Color($num){
    return ((int)$num < 0) ? " style=\"color: #ff0000;\"" : null;
}

function Format_Value($val){
    return (!ereg("^[-]?[0-9]+$", $val)) ? "<center>$val</center>" : number_format($val);
}

function Make_Html($form, $title, $disp_data, $form_staff_link, $html){

    /* ����̤�»��߷�html���� */
    $html  = "\n";
    $html .= "<span style=\"font: bold 15px; color: #555555;\">��".$title."��</span>\n";
    $html .= "<table class=\"List_Table\" border=\"1\">\n";
    $html .= "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
    $html .= "        <td class=\"Title_Green\">ô����</td>\n";
    $html .= "        <td class=\"Title_Green\">�������</td>\n";
    $html .= "        <td class=\"Title_Green\">�����</td>\n";
    $html .= "        <td class=\"Title_Green\">�����<br>����ȴ��</td>\n";
    $html .= "        <td class=\"Title_Green\">������</td>\n";
    $html .= "        <td class=\"Title_Green\">�����<br>���ǹ���</td>\n";
    $html .= "        <td class=\"Title_Green\"></td>\n";
    $html .= "        <td class=\"Title_Green\">����</td>\n";
    $html .= "        <td class=\"Title_Green\">��������<br>���</td>\n";
    $html .= "        <td class=\"Title_Green\">�����껦</td>\n";
    $html .= "    </tr>\n";

    // �Կ��������
    $count = count($disp_data);

    // �����ѥǡ�������ǥ롼��
    foreach ($disp_data as $key => $value){

        // �Կ�����
        $row_color = ($key%2 == 0)      ? "Result1" : "Result2";
        $row_color = ($key == $count-2) ? "Result5" : $row_color;
        $row_color = ($key == $count-1) ? "Result3" : $row_color;

        // ô����/��Ԥξܺ٥ڡ�����󥯤����
        if ($key != $count-1){
            $get_staff  = "?id=".$value[0];
            $option     = "onClick=\"javascript:return Submit_Page2('2-5-105.php".$get_staff."')\"";
            $form->addElement("link", $form_staff_link."[$key]", "", "", htmlspecialchars($value[1]), $option);
        }

        // html����
        $html .= "    <tr class=\"$row_color\">\n";
        if ($key != $count-1){
        $html .= "        <td width=\"120\">".$form->_elements[$form->_elementIndex["".$form_staff_link."[$key]"]]->toHtml()."</td>";
        }else{
        $html .= "        <td align=\"center\">���</td>\n";
        }
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[2]).">".Format_Value($value[2])."</td>\n";
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[3]).">".Format_Value($value[3])."</td>\n";
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[4]).">".Format_Value($value[4])."</td>\n";
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[5]).">".Format_Value($value[5])."</td>\n";
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[6]).">".Format_Value($value[6])."</td>\n";
        $html .= "        <td></td>\n";
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[7]).">".Format_Value($value[7])."</td>\n";
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[8]).">".Format_Value($value[8])."</td>\n";
        $html .= "        <td width=\"80\" align=\"right\"".Font_Color($value[9]).">".Format_Value($value[9])."</td>\n";
        $html .= "    </tr>\n";

    }

    $html .= "</table>\n";
    $html .= "<br>\n";

    return $html;

}

// ���顼��̵�����
if ($err_flg != true){

    /* ����̤�»��߷�html���� */
    $html_d = Make_Html($form, "����̤�»��߷�", $disp_daily_data,   "form_staff_link_d", $html_d);

    /* �����»ܺ��߷�html���� */
    $html_h = Make_Html($form, "�����»ܺ��߷�", $disp_halfway_data, "form_staff_link_h", $html_h);

    /* ��סʷ�߷ס�html���� */
    $html_m = Make_Html($form, "���",           $disp_monthly_data, "form_staff_link_m", $html_m);

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
$page_menu = Create_Menu_f("renew", "1");

/****************************/
// ���̥إå�������
/****************************/
$page_header = Create_Header($page_title);

/****************************/
// �ڡ�������
/****************************/
// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form��Ϣ���ѿ���assign
$smarty->assign("form", $renderer->toArray());

// ����¾���ѿ���assign
$smarty->assign("var", array(
    "html_header"       => "$html_header",
    "page_menu"         => "$page_menu",
    "page_header"       => "$page_header",
    "html_footer"       => "$html_footer",
));
$smarty->assign("html", array(
    "renew_day"         => "$monthly_renew_day",
    //2009-10-12 aoyama-n
    "start_day"         => "$start_day",
    "end_day"           => "$end_day",
    "html_d"            => "$html_d",
    "html_h"            => "$html_h",
    "html_m"            => "$html_m",
));

// �ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>