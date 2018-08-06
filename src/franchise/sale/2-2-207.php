<?php
/********************
 * ���������
 *
 *
 * �ѹ�����
 *    2006/09/11 (kaji)
 *      ����ɼ�ֹ�Υ�����freeze�����ѹ����̤��ѹ�
 *    2006/09/20 (kaji)
 *      ������̾���ѹ��ʳ���������ɼ�����������������
 *      ��ɽ�������10������Τ���
 *    2006/10/10 (suzuki)
 *      �������ɽ���������ٸ�������ȥ��顼�ˤʤ�Τ���
 *    2006/10/25 (kaji)
 *      �����ô���Ԥθ����ϥᥤ��������ä�����褦���ѹ�
 *    2006/10/26 (suzuki)
 *      �����Ψ��0%��ô���Ԥ�ɽ������褦���ѹ�
 *    2006/10/30 (suzuki)
 *      ����ɼȯ�Բ������˼�������򹹿����ʤ��褦���ѹ�
 *    2006/12/04 (suzuki)
 *      ������ʬ��̾������̾�Τ����SQL���ѹ�
 *
 ********************/
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2006/11/11      03-003      kajioka-h   ����ξ��ϳݤ����˶�ۤ�ɽ������褦�ˤ���
 *                  03-049      kajioka-h   ����ۤο��ͥ����å����ɲ�
 *  2006/12/07      bun_0062����suzuki���������դ򥼥����
 *  2006/12/09      03-058����  suzuki�������������ϳ����ۤ˴ޤޤ��褦�˽���
 *  2006/12/12      03-055����  suzuki��������������ʬ������ɽ������褦�˽���
 *  2006/12/27      xx-xxx      kajioka-h   ��ɼ�ֹ�Ǥθ������ϰϻ����
 *  2007/01/05      xx-xxx      kajioka-h   ���ô����(�ᥤ��1)�Υ��쥯�ȥܥå��������ޥ����Υ��쥯�ȥܥå�����Ʊ����
 *  2007/01/06      xx-xxx      kajioka-h   ľ�Ĥξ�����ԼԤǸ����Ǥ���褦�ˤ���
 *  2007/02/22      xx-xxx      kajioka-h   Ģɼ��CSV���ϵ�ǽ����
 *  2007/03/05      ��ȹ���12  �դ���      �ݡ�����ι�פ����
 *  2007/03/06      xx-xxx      kajioka-h   ô����Ź�Ǹ�����ǽ���ɲ�
 *  2007/03/15      xx-xxx      �դ���      ���Ƥθ������ܤ��̤Τ�Τ��ѹ���WHERE��ʤɤ��ѹ���
 *  2007/03/20      xx-xxx      watanabe-k  �إå��Υ���ಽ��������������ɽ�����ѹ��������ɼȯ�Ԣͺ�ȯ�Ԥ��ѹ�
 *                  xx-xxx      kajioka-h   ��å�󥯤�ʤ����ʷ��󤫤����ɼ��ֺ���ץ�󥯤�Ȥ���
 *  2007/03/28      ��˾21¾    kajioka-h   ͽ�꤫����������������ͽ��˰�����Ԥ��褦���ѹ�
 *  2007/04/05      ����¾25    kajioka-h   ��������Ҳ����������λ���������Υ����å������ɲ�
 *  2007/04/06                  watanabe-k  ��ɼȯ�Ի��˽вٰ�����Ϥ�̵ͭ������Ǥ���褦�˽���
 *  2007/04/09                  fukuda      ���̥ե�����Υ��顼�����å��ϴؿ��ҤȤĤǼ¹Ԥ���褦����
 *                  ����¾25    kajioka-h   ��������Ҳ����������λ����������������줿���ѹ��������󥯤�ɽ�����ʤ�
 *  2007/04/12      xx-xxx      kajioka-h   ���ե饤����Ԥκ�����ǽ��
 *  2007/06/10      xx-xxx      watanabe-k  �вٰ����ȯ�Ԥ�̵ͭ�������̵���ˤ���褦�˽���
 *
 *      �ʤ��Τؤ�Ǥդ����������ȵ�ǽ��Ĥ�����
 *
 *  2007-06-21                  fukuda      ��׹Ԥγۤ�ܤȡݤ�ʬ��
 *  2007/06/22      xx-xxx      kajioka-h   CSV���ϵ�ǽ�ɲ�
 *                              kajioka-h   ��������Ҳ�������������μ����ʬ�򸫤�褦���ѹ�
 *  2007-06-29                  fukuda      CSV���ϻ���Ʊ����ɼ���ʣ�����ʤ�������Ǥ�إå����ǡ����Ͼ�ά���ʤ��褦����
 *  2007-07-03                  watanabe-k  ��ȯ�����ȯ�ԥ��ơ�������ʬ����褦�ʷ����ѹ�
 *  2007-07-10                  watanabe-k  ������ɽ������褦�˽���
 *  2007-07-11                  fukuda      ������1���ܤˤ��׹Ԥ���Ϥ���
 *  2007-07-12                  watanabe-k  ��ɼȯ�Ԥ�̵��¾ɽ���ѹ�
 *  2007-07-25                  watanabe-k  CSV��������̾����ɽ��
 *  2007-08-17                  fukuda      CSV�˥����ӥ������ɤ��ɲ�
 *  2007-10-05                  watanabe-k  ����åפε�ǽ��CSV����Ϥ������˥إå������Ƥ����פ��ʤ��Х��ν���
 *  2007-11-03                  watanabe-k  CSV�˥��롼��ɽ������褦�˽��� 
 *  2009-09-21                  watanabe-k  �����ԡ��ȵ�ǽ���ɲ� 
 *  2009/09/28      �ʤ�        hashimoto-y �����ʬ�����Ͱ������ѻ�
 *  2009/10/08      �ʤ�        hashimoto-y Rev.1.0����¸�ߤ������ߥХ�����
 *  2010/04/28      Rev.1.5     hashimoto-y �����ƥ�������ʬ�θ������ɲ�(Rev.1.5)
 *
 */

// �ڡ���̾
$page_title = "���������";

// �Ķ�����ե�����
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");

//�����ʬ�δؿ�
require_once(PATH ."function/trade_fc.fnc");

// HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"]);

// DB��³
$db_con = Db_Connect();

// ���¥����å�
$auth       = Auth_Check($db_con);
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

#print_r($_POST);
/****************************/
// �������������Ϣ
/****************************/
// �����ե�������������
$ary_form_list = array(
    "form_display_num"  => "1", 
    "form_output_type"  => "1",
    "form_client_branch"=> "",
    "form_attach_branch"=> "",
    "form_client"       => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_round_staff"  => array("cd" => "", "select" => ""),
    "form_part"         => "",
    "form_claim"        => array("cd1" => "", "cd2" => "", "name" => ""),
    "form_multi_staff"  => "",
    "form_ware"         => "",
    "form_charge_fc"    => array("cd1" => "", "cd2" => "", "name" => "", "select" => array("0" => "", "1" => "")),
    "form_claim_day"    => array("sy" => "", "sm" => "", "sd" => "", "ey" => "", "em" => "", "ed" => ""), 
    "form_slip_no"      => array("s" => "", "e" => ""),
    "form_sale_amount"  => array("s" => "", "e" => ""),
    "form_sale_day"     => array(
        "sy" => date("Y"),
        "sm" => date("m"),
        "sd" => "01",
        "ey" => date("Y"),
        "em" => date("m"),
        "ed" => date("t", mktime(0, 0, 0, date("m"), date("d"), date("Y")))
    ),
    "form_contract_div" => "1",
    "form_client_gr"    => array("name" => "", "select" => ""),
    "form_trade"        => "",
    "form_renew"        => "1",
	"form_repeat"		=> "1"
);

$ary_pass_list = array(
    "form_output_type"  => "1",
);

// �����������
//Restore_Filter2($form, "sale", "form_display", $ary_form_list);
Restore_Filter2($form, "sale", "form_display", $ary_form_list, $ary_pass_list);


/****************************/
// �����ѿ�����
/****************************/
$shop_id   = $_SESSION["client_id"];


/****************************/
// ���������
/****************************/
$limit          = null;     // LIMIT
$offset         = "0";      // OFFSET
$total_count    = "0";      // �����
$page_count     = ($_POST["f_page1"] != null) ? $_POST["f_page1"] : "1";    // ɽ���ڡ�����


/****************************/
// �ե�����ѡ������
/****************************/
/* ���̥ե����� */
Search_Form($db_con, $form, $ary_form_list, true);

// ͽ����������
$form->removeElement("form_round_day");

/* �⥸�塼���̥ե����� */
// ��ɼ�ֹ�ʳ��ϡ���λ��
$obj    =   null;
$obj[]  =&  $form->createElement("text", "s", "", "size=\"10\" maxlength=\"8\" class=\"ime_disabled\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", "��");
$obj[]  =&  $form->createElement("text", "e", "", "size=\"10\" maxlength=\"8\" class=\"ime_disabled\" $g_form_option");
$form->addGroup($obj, "form_slip_no", "", "");

// ����ۡʳ��ϡ���λ��
Addelement_Money_Range($form, "form_sale_amount", "", "");

// ���׾����ʳ��ϡ���λ��
Addelement_Date_Range($form, "form_sale_day", "���׾���", "-");

// �����ʬ
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "����",           "1");
$obj[]  =&  $form->createElement("radio", null, null, "�̾�",           "2");
$obj[]  =&  $form->createElement("radio", null, null, "����饤�����", "3");
$obj[]  =&  $form->createElement("radio", null, null, "���ե饤�����", "4");
$form->addGroup($obj, "form_contract_div", "", "");

// ���롼��
$item   =   null;
$item   =   Select_Get($db_con, "client_gr");
$obj    =   null;
$obj[]  =   $form->createElement("text", "name", "", "size=\"34\" maxLength=\"25\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", " ");
$obj[]  =   $form->createElement("select", "select", "",$item, $g_form_option_select);
$form->addGroup($obj, "form_client_gr", "", "");

// ��������
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "����",   "1");
$obj[]  =&  $form->createElement("radio", null, null, "̤�»�", "2");
$obj[]  =&  $form->createElement("radio", null, null, "�»ܺ�", "3");
$form->addGroup($obj, "form_renew", "", "");

// �����ʬ
$item   =   null;   
$item   =   Select_Get($db_con, "trade_sale");

#2009-09-28 hashimoto-y
#$form->addElement("select", "form_trade", "", $item, $g_form_option_select);

$trade_form=$form->addElement('select', 'form_trade', null, null, $g_form_option_select);

#�Ͱ������ѻ�
$select_value_key = array_keys($item);
for($i = 0; $i < count($item); $i++){
    if( $select_value_key[$i] != 14 && $select_value_key[$i] != 64){
        $trade_form->addOption($item[$select_value_key[$i]], $select_value_key[$i]);
    }
}

// �����ԡ���
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "����",           "1");
$obj[]  =&  $form->createElement("radio", null, null, "���",           "2");
$obj[]  =&  $form->createElement("radio", null, null, "�����",         "3");
$form->addGroup($obj, "form_repeat", "", "");

#2010-04-26 hashimoto-y
// ����
$obj    =   null;
$obj[]  =&  $form->createElement("text", "cd", "", "size=\"10\" maxLength=\"8\" class=\"ime_disabled\" $g_form_option");
$obj[]  =&  $form->createElement("static", "", "", " ");
$obj[]  =&  $form->createElement("text", "name", "", "size=\"34\" maxLength=\"15\" $g_form_option");
$form->addGroup($obj, "form_goods", "", "");

#2010-04-26 hashimoto-y
//�����ʬ
$array_divide = Select_Get($db_con, "divide_con");
$form->addElement('select', "form_divide", "", $array_divide, "$g_form_option_select");


// �����ȥ��
$ary_sort_item = array(
    "sl_client_cd"      => "�����襳����",
    "sl_client_name"    => "������̾",
    "sl_slip"           => "��ɼ�ֹ�",
    "sl_sale_day"       => "���׾���",
    "sl_round_staff"    => "���ô����<br>�ʥᥤ�󣱡�",
    "sl_act_client_cd"  => "����襳����",
    "sl_act_client_name"=> "�����̾",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_sale_day");

// ɽ���ܥ���
$form->addElement("submit", "form_display", "ɽ����");

// ���ꥢ�ܥ���
$form->addElement("button","form_clear","���ꥢ", "onClick=\"javascript:location.href('".$_SERVER["PHP_SELF"]."');\"");

// �����ե饰
$form->addElement("hidden", "sale_slip_flg");       // ȯ�ԥե饰
$form->addElement("hidden", "sale_republish_flg");  // ��ȯ�ԥե饰
$form->addElement("hidden", "hdn_cancel_id");       // ������ID
$form->addElement("hidden", "hdn_delete_id");       // ������ID


/***************************/
// ȯ�ԥܥ��󲡲���
/***************************/
if ($_POST["sale_slip_flg"] == "true"){

    // ��ɼ����POST����Ƥ�����
    if ($_POST["form_slip_check"] != null){

        $ary_output_sale = null;    // ��ɼȯ�����ID����

        // POST���줿��ɼ�����å��ǥ롼��
        foreach ($_POST["form_slip_check"] as $key => $value){
            // �ͤ�1�ξ�硢��ɼȯ�����ID�����
            if ($value == "1"){
                $ary_output_sale[] = $_POST["output_id_array"][$key];
            }
        }

    }

    // �����å����դ��Ƥ��ʤ��ä����
    if ($ary_output_sale == null){
        // ���顼��å�����
        $err_msg_print = "ȯ�Ԥ�����ɼ�����򤵤�Ƥ��ޤ���";
    }

    // ��ȯ�ԥե饰���ꥢ
    $clear_data["sale_slip_flg"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}

/***************************/
// ��ȯ�ԥܥ��󲡲���
/***************************/
if ($_POST["sale_republish_flg"] == "true"){

    // ��ɼ����POST����Ƥ�����
    if ($_POST["form_republish_check"] != null){

        $ary_output_sale = null;    // ��ɼȯ�����ID����

        // POST���줿��ɼ�����å��ǥ롼��
        foreach ($_POST["form_republish_check"] as $key => $value){
            // �ͤ�1�ξ�硢��ɼȯ�����ID�����
            if ($value == "1"){
                $ary_output_sale[] = $_POST["output_id_array"][$key];
            }
        }

    }

    // �����å����դ��Ƥ��ʤ��ä����
    if ($ary_output_sale == null){
        // ���顼��å�����
        $err_msg_print = "��ȯ�Ԥ�����ɼ�����򤵤�Ƥ��ޤ���";
    }

    // ��ȯ�ԥե饰���ꥢ
    $clear_data["sale_republish_flg"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}



/***************************/
// �����󥯲�������ͽ�꤫�����ɼ��
/***************************/
if ($_POST["hdn_cancel_id"] != null){

    Db_Query($db_con, "BEGIN;");

    // ����ID�������ɼ�ե饰������
    $sql = "SELECT aord_id, renew_flg, intro_account_id, intro_amount, contract_div FROM t_sale_h WHERE sale_id = ".$_POST["hdn_cancel_id"].";";
    $result = Db_Query($db_con, $sql);
    if($result == false){ 
        Db_Query($db_con, "ROLLBACK;");
        exit;
    }
    $cancel_aord_id = pg_fetch_result($result, 0, "aord_id");       // ����оݤμ���ID
    $renew_flg      = pg_fetch_result($result, 0, "renew_flg");     // ���������ե饰
    $intro_account_id   = pg_fetch_result($result, 0, "intro_account_id");      // �Ҳ��ID
    $intro_amount   = pg_fetch_result($result, 0, "intro_amount");  // �Ҳ���
    $contract_div   = pg_fetch_result($result, 0, "contract_div");  // �����ʬ

    //�����ɼ�ξ�硢�����������������������Ƥʤ��������å�
    if($_SESSION["group_kind"] == "2" && $contract_div != "1"){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE act_sale_id = ".$_POST["hdn_cancel_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){ 
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $act_buy_renew_err = "��̳����� �λ�����ɼ��������������������Ƥ��뤿�����Ǥ��ޤ���";
        }
    }

    //�Ҳ����λ�����¸�ߤ����硢�Ҳ���������������������Ƥʤ��������å�
    if($intro_account_id != null && $intro_amount > 0){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE intro_sale_id = ".$_POST["hdn_cancel_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){ 
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $intro_buy_renew_err = "�Ҳ������ �λ�����ɼ��������������������Ƥ��뤿�����Ǥ��ޤ���";
        }
    }


    //���顼����ʤ���硢��������
    if($renew_flg == "f" && $act_buy_renew_err == null && $intro_buy_renew_err == null){

        // �����ɼ���
        $sql  = "DELETE FROM \n";
        $sql .= "   t_sale_h \n";
        $sql .= "WHERE \n";
        $sql .= "   sale_id = ".$_POST["hdn_cancel_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if ($res === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        // �����إå��γ���ե饰��false��
        $sql  = "UPDATE \n";
        $sql .= "   t_aorder_h \n";
        $sql .= "SET \n";
        $sql .= "   confirm_flg = 'f', \n";
        $sql .= "   ps_stat = '1' \n";
        $sql .= "WHERE \n";
        $sql .= "   aord_id = $cancel_aord_id \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if ($res === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        //�ʲ������ҽ�󡢤ޤ��ϥ���饤����Ԥξ��
        if($contract_div == "1" || $contract_div == "2"){


            // ����饤����Ԥξ��ϡ������إå��γ���ե饰(��������)��false����åե饰��true
            $sql  = "UPDATE \n";
            $sql .= "   t_aorder_h \n";
            $sql .= "SET \n";
            $sql .= "   trust_confirm_flg = 'f', \n";
            $sql .= "   cancel_flg = 't' \n";
            $sql .= "WHERE \n";
            $sql .= "   contract_div = '2' \n";
            $sql .= "AND \n";
            $sql .= "   aord_id = $cancel_aord_id \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if ($res === false){
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            // �����Ƥ��Ҹ�ID�����
            $move_ware_id = FC_Move_Ware_Id($db_con, $cancel_aord_id);
            if($move_ware_id === false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }

            // ����ID���鼫�ҽ��ξ��ϥ���å�ID����Ԥξ��������ID�����
            $sql  = "SELECT \n";
            $sql .= "    CASE contract_div \n";
            $sql .= "        WHEN '1' THEN shop_id \n";
            $sql .= "        ELSE act_id \n";
            $sql .= "    END \n";
            $sql .= "FROM \n";
            $sql .= "    t_aorder_h \n";
            $sql .= "WHERE \n";
            $sql .= "    aord_id = $cancel_aord_id \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if($res == false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
            $move_shop_id = pg_fetch_result($res, 0, 0);    // �ºݤ˽���Ԥ�����å�ID

            // �и��ʥơ��֥뤫�����������ǡ���ID������ID�������������
            $sql  = "SELECT \n";
            $sql .= "    t_aorder_h.client_id, \n";     // 0 ������ID
            $sql .= "    t_aorder_h.client_cname, \n";  // 1 ������̾��ά�Ρ�
            $sql .= "    t_aorder_h.ord_no, \n";        // 2 ��ɼ�ֹ�
            $sql .= "    t_aorder_ship.aord_d_id, \n";  // 3 �����ǡ���ID
            $sql .= "    t_aorder_ship.goods_id, \n";   // 4 ����ID
            $sql .= "    t_aorder_ship.num \n";         // 5 ����
            $sql .= "FROM \n";
            $sql .= "    t_aorder_h \n";
            $sql .= "    INNER JOIN t_aorder_d ON t_aorder_h.aord_id = t_aorder_d.aord_id \n";
            $sql .= "    INNER JOIN t_aorder_ship ON t_aorder_d.aord_d_id = t_aorder_ship.aord_d_id \n";
            $sql .= "WHERE \n";
            $sql .= "    t_aorder_h.aord_id = $cancel_aord_id \n";
            $sql .= ";";
            $res  = Db_Query($db_con, $sql);
            if($res == false){ 
                Db_Query($db_con, "ROLLBACK;");
                exit;
            }
            $move_goods_data = Get_Data($res, 3);    // �����뾦�ʤ��Ȥ�
            $move_goods_data_count = pg_num_rows($res);

            for($i = 0; $i < $move_goods_data_count; $i++){
                // ��ȶ�ʬ��1�������ס����и˶�ʬ��2���иˡ�
                $sql  = "INSERT INTO \n";
                $sql .= "   t_stock_hand \n";
                $sql .= "( \n";
                $sql .= "   goods_id, \n";
                $sql .= "   enter_day, \n";
                $sql .= "   work_day, \n";
                $sql .= "   work_div, \n";
                $sql .= "   client_id, \n";
                $sql .= "   client_cname, \n";
                $sql .= "   ware_id, \n";
                $sql .= "   io_div, \n";
                $sql .= "   num, \n";
                $sql .= "   slip_no, \n";
                $sql .= "   aord_d_id, \n";
                $sql .= "   staff_id, \n";
                $sql .= "   shop_id \n";
                $sql .= ")VALUES( \n";
                $sql .= "   ".$move_goods_data[$i][4].", \n";
                $sql .= "   CURRENT_TIMESTAMP, \n";
                $sql .= "   CURRENT_TIMESTAMP, \n";
                $sql .= "   '1', \n";
                $sql .= "   ".$move_goods_data[$i][0].", \n";
                $sql .= "   '".$move_goods_data[$i][1]."', \n";
                $sql .= "   $move_ware_id, \n";
                $sql .= "   '1', \n";
                $sql .= "   ".$move_goods_data[$i][5].", \n";
                $sql .= "   '".$move_goods_data[$i][2]."', \n";
                $sql .= "   ".$move_goods_data[$i][3].", \n";
                $sql .= "   ".$_SESSION["staff_id"].", \n";
                $sql .= "   $move_shop_id \n";
                $sql .= ") \n";
                $sql .= ";";
                $res = Db_Query($db_con, $sql);
                if($res == false){
                    Db_Query($db_con, "ROLLBACK;");
                    exit;
                }
            }
        }

        Db_Query($db_con, "COMMIT;");

    }else{
        Db_Query($db_con, "ROLLBACK;");
    }

    // ��ɼ��åե饰���ꥢ
    $clear_data["hdn_cancel_id"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}


/****************************/
// �����󥯲������ʼ����ɼ��
/****************************/
if($_POST["hdn_delete_id"] != null){

    // ���������ե饰���Ҳ��ID���Ҳ����������ʬ������
    $sql = "SELECT renew_flg, intro_account_id, intro_amount, contract_div FROM t_sale_h WHERE sale_id = ".$_POST["hdn_delete_id"].";";
    $result = Db_Query($db_con, $sql);
    if($result == false){
        Db_Query($db_con, "ROLLBACK;");
        exit;
    }
    $renew_flg      = pg_fetch_result($result, 0, "renew_flg");     // ���������ե饰
    $intro_account_id   = pg_fetch_result($result, 0, "intro_account_id");      // �Ҳ��ID
    $intro_amount   = pg_fetch_result($result, 0, "intro_amount");  // �Ҳ���
    $contract_div   = pg_fetch_result($result, 0, "contract_div");  // �����ʬ

    //�����ɼ�ξ�硢�����������������������Ƥʤ��������å�
    if($_SESSION["group_kind"] == "2" && $contract_div != "1"){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE act_sale_id = ".$_POST["hdn_delete_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $act_buy_renew_err = "��̳����� �λ�����ɼ��������������������Ƥ��뤿�����Ǥ��ޤ���";
        }
    }

    //�Ҳ����λ�����¸�ߤ����硢�Ҳ���������������������Ƥʤ��������å�
    if($intro_account_id != null && $intro_amount > 0){
        $sql = "SELECT renew_flg FROM t_buy_h WHERE intro_sale_id = ".$_POST["hdn_delete_id"].";";
        $result = Db_Query($db_con, $sql);
        if($result == false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }
        if(pg_fetch_result($result, 0, "renew_flg") == "t"){
            $intro_buy_renew_err = "�Ҳ������ �λ�����ɼ��������������������Ƥ��뤿�����Ǥ��ޤ���";
        }
    }


    //���顼����ʤ���硢��������
    if($renew_flg == "f" && $act_buy_renew_err == null && $intro_buy_renew_err == null){

        Db_Query($db_con, "BEGIN;");

        // ���ǡ������ơ������إå�������إå����SQL
        $sql  = "DELETE FROM \n";
        $sql .= "   t_sale_h \n";
        $sql .= "WHERE \n";
        $sql .= "   sale_id = ".$_POST["hdn_delete_id"]." \n";
        $sql .= ";";
        $res  = Db_Query($db_con, $sql);
        if($res === false){ 
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }

        Db_Query($db_con, "COMMIT;");
    }

    // ��ɼ����ե饰���ꥢ
    $clear_data["hdn_delete_id"] = "";
    $form->setConstants($clear_data);

    $post_flg = true;

}


/****************************/
// ɽ���ܥ��󲡲���
/****************************/
if ($_POST["form_display"] != null){

    /****************************/
    // ���顼�����å�
    /****************************/
    // �����̥ե���������å�
    Search_Err_Chk($form);

    // ����ɼ�ֹ�
    // ���顼��å�����
    $err_msg = "��ɼ�ֹ� �Ͽ��ͤΤ����ϲ�ǽ�Ǥ���";
    Err_Chk_Num($form, "form_slip_no", $err_msg);

    // �������
    // ���顼��å�����
    $err_msg = "����� �Ͽ��ͤΤ����ϲ�ǽ�Ǥ���";
    Err_Chk_Int($form, "form_sale_amount", $err_msg);

    // �����׾���
    // ���顼��å�����
    $err_msg = "���׾��� �����դ������ǤϤ���ޤ���";
    Err_Chk_Date($form, "form_sale_day", $err_msg);

    /****************************/
    // ���顼�����å���̽���
    /****************************/
    // �����å�Ŭ��
    $test = $form->validate();

    // ��̤�ե饰��
    $err_flg = (count($form->_errors) > 0) ? true : false;

    $post_flg = ($err_flg != true) ? true : false;

}


/****************************/
// 1. ɽ���ܥ��󲡲��ܥ��顼�ʤ���
// 2. �ڡ����ڤ��ؤ���������¾��POST��
/****************************/
if (($_POST["form_display"] != null && $err_flg != true) || ($_POST != null && $_POST["form_display"] == null)){

    // ����POST�ǡ�����0���
    $_POST["form_claim_day"] = Str_Pad_Date($_POST["form_claim_day"]);
    $_POST["form_sale_day"]  = Str_Pad_Date($_POST["form_sale_day"]);

    // 1. �ե�������ͤ��ѿ��˥��å�
    // 2. SESSION��hidden�ѡˤ��͡ʸ�����������ؿ���ǥ��åȡˤ��ѿ��˥��å�
    // ����������������˻���
    $display_num        = $_POST["form_display_num"];
    $output_type        = $_POST["form_output_type"];
    $client_branch      = $_POST["form_client_branch"];
    $attach_branch      = $_POST["form_attach_branch"];
    $client_cd1         = $_POST["form_client"]["cd1"];
    $client_cd2         = $_POST["form_client"]["cd2"];
    $client_name        = $_POST["form_client"]["name"];
    $round_staff_cd     = $_POST["form_round_staff"]["cd"];
    $round_staff_select = $_POST["form_round_staff"]["select"];
    $part               = $_POST["form_part"];
    $claim_cd1          = $_POST["form_claim"]["cd1"];
    $claim_cd2          = $_POST["form_claim"]["cd2"];
    $claim_name         = $_POST["form_claim"]["name"];
    $multi_staff        = $_POST["form_multi_staff"];
    $ware               = $_POST["form_ware"];
    $claim_day_sy       = $_POST["form_claim_day"]["sy"];
    $claim_day_sm       = $_POST["form_claim_day"]["sm"];
    $claim_day_sd       = $_POST["form_claim_day"]["sd"];
    $claim_day_ey       = $_POST["form_claim_day"]["ey"];
    $claim_day_em       = $_POST["form_claim_day"]["em"];
    $claim_day_ed       = $_POST["form_claim_day"]["ed"];
    $charge_fc_cd1      = $_POST["form_charge_fc"]["cd1"];
    $charge_fc_cd2      = $_POST["form_charge_fc"]["cd2"];
    $charge_fc_name     = $_POST["form_charge_fc"]["name"];
    $charge_fc_select   = $_POST["form_charge_fc"]["select"]["1"];
    $slip_no_s          = $_POST["form_slip_no"]["s"];
    $slip_no_e          = $_POST["form_slip_no"]["e"];
    $sale_amount_s      = $_POST["form_sale_amount"]["s"];
    $sale_amount_e      = $_POST["form_sale_amount"]["e"];
    $sale_day_sy        = $_POST["form_sale_day"]["sy"];
    $sale_day_sm        = $_POST["form_sale_day"]["sm"];
    $sale_day_sd        = $_POST["form_sale_day"]["sd"];
    $sale_day_ey        = $_POST["form_sale_day"]["ey"];
    $sale_day_em        = $_POST["form_sale_day"]["em"];
    $sale_day_ed        = $_POST["form_sale_day"]["ed"];
    $contract_div       = $_POST["form_contract_div"];
    $client_gr_name     = $_POST["form_client_gr"]["name"];
    $client_gr_select   = $_POST["form_client_gr"]["select"];
    $trade              = $_POST["form_trade"];
    $renew              = $_POST["form_renew"];
    $repeat             = $_POST["form_repeat"];

    #2010-04-28 hashimoto-y
    $form_goods_cd      = $_POST["form_goods"]["cd"]; 
    $form_goods_name    = $_POST["form_goods"]["name"]; 
    $form_divide        = $_POST["form_divide"]; 

    $post_flg = true;

}


/****************************/
// �����ǡ�������������
/****************************/
if ($post_flg == true && $err_flg != true){

    $sql = null;

    // �ܵ�ô����Ź
    $sql .= ($client_branch != null) ? "AND t_client.charge_branch_id = $client_branch \n" : null;
    // ��°�ܻ�Ź
    if ($attach_branch != null){
        $sql .= "AND \n";
        $sql .= "   t_round_staff.staff_id IN \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           t_attach.staff_id \n";
        $sql .= "       FROM \n";
        $sql .= "           t_attach \n";
        $sql .= "           INNER JOIN t_part ON t_attach.part_id = t_part.part_id \n";
        $sql .= "       WHERE \n";
        $sql .= "           t_part.branch_id = $attach_branch \n";
        $sql .= "   ) \n";
    }
    // �����襳���ɣ�
    $sql .= ($client_cd1 != null) ? "AND t_sale_h.client_cd1 LIKE '$client_cd1%' \n" : null;
    // �����襳���ɣ�
    $sql .= ($client_cd2 != null) ? "AND t_sale_h.client_cd2 LIKE '$client_cd2%' \n" : null;
    // ������̾
    if ($client_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_sale_h.client_name  LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_sale_h.client_name2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_sale_h.client_cname LIKE '%$client_name%' \n";
        $sql .= "   ) \n";
    }
    // ���ô���ԥ�����
    $sql .= ($round_staff_cd != null) ? "AND t_round_staff.charge_cd = '$round_staff_cd' \n" : null;
    // ���ô���ԥ��쥯��
    $sql .= ($round_staff_select != null) ? "AND t_round_staff.staff_id = $round_staff_select \n" : null;
    // ����
    $sql .= ($part != null) ? "AND t_round_staff.part_id = $part \n" : null;
    // �����襳���ɣ�   
    $sql .= ($claim_cd1 != null) ? "AND t_client_claim.client_cd1 LIKE '$claim_cd1%' \n" : null;
    // �����襳���ɣ�
    $sql .= ($claim_cd2 != null) ? "AND t_client_claim.client_cd2 LIKE '$claim_cd2%' \n" : null;
    // ������̾
    if ($claim_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_client_claim.client_name  LIKE '%$claim_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client_claim.client_name2 LIKE '%$claim_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client_claim.client_cname LIKE '%$claim_name%' \n";
        $sql .= "   ) \n";
    }
    // ʣ������
    if ($multi_staff != null){
        $ary_multi_staff = explode(",", $multi_staff);
        $sql .= "AND \n";
        $sql .= "   t_round_staff.charge_cd IN (";
        foreach ($ary_multi_staff as $key => $value){
            $sql .= "'".trim($value)."'";
            $sql .= ($key+1 < count($ary_multi_staff)) ? ", " : ") \n";
        }
    }
    // �Ҹ�
    $sql .= ($ware != null) ? "AND t_sale_h.ware_id = $ware \n" : null;
    // �������ʳ��ϡ�
    $claim_day_s  = $claim_day_sy."-".$claim_day_sm."-".$claim_day_sd;
    $sql .= ($claim_day_s != "--") ? "AND t_sale_h.claim_day >= '$claim_day_s' \n" : null;
    // �������ʽ�λ��
    $claim_day_e  = $claim_day_ey."-".$claim_day_em."-".$claim_day_ed;
    $sql .= ($claim_day_e != "--") ? "AND t_sale_h.claim_day <= '$claim_day_e' \n" : null;
    // ������FC�����ɣ�
    $sql .= ($charge_fc_cd1 != null) ? "AND t_sale_h.act_cd1 LIKE '$charge_fc_cd1%' \n" : null;
    // ������FC�����ɣ�
    $sql .= ($charge_fc_cd2 != null) ? "AND t_sale_h.act_cd2 LIKE '$charge_fc_cd2%' \n" : null;
    // ������FC̾
    if ($charge_fc_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_act_client.client_name  LIKE '%$charge_fc_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_act_client.client_name2 LIKE '%$charge_fc_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_act_client.client_cname LIKE '%$charge_fc_name%' \n";
        $sql .= "   ) \n";
    }
    // ������FC���쥯��
    $sql .= ($charge_fc_select != null) ? "AND t_sale_h.act_id = $charge_fc_select \n" : null;
    // ��ɼ�ֹ�ʳ��ϡ�
    $sql .= ($slip_no_s != null) ? "AND t_sale_h.sale_no >= '".str_pad($slip_no_s, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // ��ɼ�ֹ�ʽ�λ��
    $sql .= ($slip_no_e != null) ? "AND t_sale_h.sale_no <= '".str_pad($slip_no_e, 8, 0, STR_PAD_LEFT)."' \n" : null;
    // ����ۡʳ��ϡ�
    if ($sale_amount_s != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           CASE \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * 1 \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "           END \n";
        $sql .= "   ) \n";
        $sql .= "   >= $sale_amount_s \n";
    }
    // ����ۡʽ�λ��
    if ($sale_amount_e != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "           CASE \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * 1 \n";
        $sql .= "               WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
        $sql .= "               THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
        $sql .= "           END \n";
        $sql .= "   ) \n";
        $sql .= "   <= $sale_amount_e \n";
    }
    // ���׾����ʳ��ϡ�
    $sale_day_s = $sale_day_sy."-".$sale_day_sm."-".$sale_day_sd;
    $sql .= ($sale_day_s != "--") ? "AND t_sale_h.sale_day >= '$sale_day_s' \n" : null;
    // ���׾����ʽ�λ��
    $sale_day_e = $sale_day_ey."-".$sale_day_em."-".$sale_day_ed;
    $sql .= ($sale_day_e != "--") ? "AND t_sale_h.sale_day <= '$sale_day_e' \n" : null;
    // �����ʬ
    if ($contract_div == "2"){
        $sql .= "AND t_sale_h.contract_div = '1' \n";
    }elseif ($contract_div == "3"){
        $sql .= "AND t_sale_h.contract_div = '2' \n";
    }elseif ($contract_div == "4"){
        $sql .= "AND t_sale_h.contract_div = '3' \n";
    }
    // ���롼��̾
    $sql .= ($client_gr_name != null) ? "AND t_client_gr.client_gr_name LIKE '%$client_gr_name%' \n" : null;
    // ���롼�ץ��쥯��
    $sql .= ($client_gr_select != null) ? "AND t_client.client_gr_id = $client_gr_select \n" : null;
    // �����ʬ
    $sql .= ($trade != null) ? "AND t_sale_h.trade_id = $trade \n" : null;
    // ��������
    if ($renew == "2"){
        $sql .= "AND t_sale_h.renew_flg = 'f' \n";
    }elseif ($renew == "3"){
        $sql .= "AND t_sale_h.renew_flg = 't' \n";
    }

	// �����ԡ���
	switch ($repeat){
		// ���
		case '2' :
        	$sql .= "AND t_aorder_h.contract_id IS NOT NULL \n";
			break;
		// �����
		case '3' :
        	$sql .= "AND t_aorder_h.contract_id IS NULL \n";
			break;
	}

    #2010-04-28 hashimoto-y
    // ���ʥ����ɡ�����̾�����ʶ�ʬ
    if ($form_goods_cd != null || $form_goods_name != null || $form_divide != null){
        $sql .= "AND \n";
        $sql .= "   t_sale_h.sale_id IN ";
        $sql .= "   ( \n";
        $sql .= "       SELECT \n";
        $sql .= "            DISTINCT(sale_id) \n";
        $sql .= "       FROM  \n";
        $sql .= "            t_sale_d \n";
        $sql .= "       WHERE \n";

        $sql .= ($form_divide     != null) ? "sale_div_cd = '$form_divide' \n" : null;

        if($form_divide == null){
            $sql .= ($form_goods_cd   != null) ? "goods_cd LIKE '$form_goods_cd%' \n" : null;
        }else{
            $sql .= ($form_goods_cd   != null) ? "AND goods_cd LIKE '$form_goods_cd%' \n" : null;
        }

        if($form_divide == null && $form_goods_cd == null){
            $sql .= ($form_goods_name != null) ? "official_goods_name LIKE '%$form_goods_name%' \n" : null;
        }else{
            $sql .= ($form_goods_name != null) ? "AND official_goods_name LIKE '%$form_goods_name%' \n" : null;
        }

        $sql .= "   ) \n";
    }

    // �ѿ��ͤ��ؤ�
    $where_sql = $sql;


    $sql = null;

    // �����Ƚ�
    switch ($_POST["hdn_sort_col"]){
        // �����襳����
        case "sl_client_cd":
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // ������̾
        case "sl_client_name":
            $sql .= "   t_sale_h.client_cname, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // ��ɼ�ֹ�
        case "sl_slip":
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // ���׾���
        case "sl_sale_day":
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // ���ô���ԡʥᥤ�󣱡�
        case "sl_round_staff":
            $sql .= "   t_round_staff.charge_cd, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // ����襳����
        case "sl_act_client_cd":
            $sql .= "   t_act_client.client_cd1, \n";
            $sql .= "   t_act_client.client_cd2, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
        // �����̾
        case "sl_act_client_name":
            $sql .= "   t_act_client.client_cname, \n";
            $sql .= "   t_act_client.client_cd1, \n";
            $sql .= "   t_act_client.client_cd2, \n";
            $sql .= "   t_sale_h.sale_day, \n";
            $sql .= "   t_sale_h.client_cd1, \n";
            $sql .= "   t_sale_h.client_cd2, \n";
            $sql .= "   t_sale_h.sale_no \n";
            break;
    }

    // �ѿ��ͤ��ؤ�
    $order_sql = $sql;

}

#echo $order_sql;


/****************************/
// �����ǡ�������
/****************************/
#2009-10-08 hashimoto-y
#
#�����ɼ�������̤���Ͽ����̥�����ɥ�������������ν��ɽ����Ԥ�
#�����ɼ��������Ͽ�塢��λ�ܥ���򥯥�å���������SQL���顼�Ȥʤ�
#
#$order_sql�����åȤ���Ƥʤ����Ͻ��ɽ��
if($order_sql == "") $post_flg = false;


if ($post_flg == true && $err_flg != true){

    $sql  = "SELECT \n";
    $sql .= "   t_sale_h.sale_id, \n";                                          //  0 ���ID
    $sql .= "   t_sale_h.sale_no, \n";                                          //  1 ����ֹ�
    $sql .= "   t_sale_h.sale_day, \n";                                         //  2 ���׾���
    $sql .= "   t_sale_h.client_cd1 || '-' || t_sale_h.client_cd2 \n";
    $sql .= "   AS client_cd, \n";                                              //  3 �����襳����
    $sql .= "   t_sale_h.client_cname, \n";                                     //  4 ������ά��
    $sql .= "   t_round_staff.charge_cd, \n";                                   //  5 ô���ԥ�����
    $sql .= "   t_round_staff.staff_name, \n";                                  //  6 ô����̾
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN t_sale_h.net_amount * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN t_sale_h.net_amount * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS net_amount,\n";                                              //  7 ����ۡ���ȴ��
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN t_sale_h.tax_amount * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN t_sale_h.tax_amount * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS tax_amount,\n";                                              //  8 ������
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN (t_sale_h.net_amount + t_sale_h.tax_amount) * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS sale_amount,\n";                                             //  9 ����ۡ��ǹ���
    $sql .= "   CASE t_sale_h.renew_flg \n";
    $sql .= "       WHEN 't' THEN '��' \n";
    $sql .= "       ELSE NULL \n";
    $sql .= "   END \n";
    $sql .= "   AS renew, \n";                                                  // 10 ��������
    $sql .= "   t_sale_h.hand_slip_flg, \n";                                    // 11 �����ɼ�ե饰
    $sql .= "   t_sale_h.aord_id, \n";                                          // 12 ����ID
    $sql .= "   t_sale_h.contract_div AS s_contract_div, \n";                                   // 13 �����ʬ
    $sql .= "   t_aorder_h.contract_div, \n";                                   // 13 �����ʬ
    $sql .= "   t_sale_h.act_request_flg, \n";                                  // 14 ������ե饰
    $sql .= "   CASE t_sale_h.trade_id \n";
    $sql .= "       WHEN '11' THEN '�����' \n";
    $sql .= "       WHEN '15' THEN '�������' \n";
    $sql .= "       WHEN '13' THEN '������' \n";
    $sql .= "       WHEN '14' THEN '���Ͱ�' \n";
    $sql .= "       WHEN '61' THEN '�������' \n";
    $sql .= "       WHEN '63' THEN '��������' \n";
    $sql .= "       WHEN '64' THEN '�����Ͱ�' \n";
    $sql .= "   END \n";
    $sql .= "   AS trade, \n";                                                  // 15 �����ʬ
    $sql .= "   CASE t_client.slip_out \n";                                     // 16 ��ɼȯ��
    $sql .= "       WHEN '1' THEN 'ͭ' \n";
    $sql .= "       ELSE '̵' \n";
    $sql .= "   END \n";
    $sql .= "   AS slip_out, \n";
    $sql .= "   t_sale_h.total_split_num, \n";                                  // 17 ������
    $sql .= "   t_act_client.client_cd1 || '-' || t_act_client.client_cd2 \n";
    $sql .= "   AS act_cd, \n";                                                 // 18 ����襳����
    $sql .= "   t_act_client.client_cname AS act_cname, \n";                    // 19 �����̾ά��
    $sql .= "   to_date(t_sale_h.renew_day, 'YYYY-MM-DD') AS renew_day, \n";    // 20 ����������
    $sql .= "   CASE \n";
    $sql .= "       WHEN (intro_buy_h.renew_flg = true OR act_buy_h.renew_flg = true) THEN true \n";
    $sql .= "       ELSE false \n";
    $sql .= "   END AS buy_renew_flg, \n";                                      // 21 ��������Ҳ����λ��������������ե饰
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11, 15, 61) \n";
    $sql .= "       THEN t_sale_h.advance_offset_totalamount * 1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 14, 63, 64) \n";
    $sql .= "       THEN t_sale_h.advance_offset_totalamount * -1 \n";
    $sql .= "   END \n";
    $sql .= "   AS advance_offset_totalamount, \n";                             // 22 �����껦�۹��
    $sql .= "   CASE \n";
    $sql .= "       WHEN act_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "       THEN (act_buy_h.net_amount + act_buy_h.tax_amount) *  1 \n";
    $sql .= "       WHEN act_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "       THEN (act_buy_h.net_amount + act_buy_h.tax_amount) * -1 \n";
    $sql .= "   END AS act_amount, \n";                                         // 23 ��������ǹ���
    $sql .= "   CASE \n";
    $sql .= "       WHEN intro_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "       THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) *  1 \n";
    $sql .= "       WHEN intro_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "       THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) * -1 \n";
    $sql .= "   END AS intro_amount, \n";                                        // 24 �Ҳ���������ǹ���

    //��ɼȯ�� // 25
    $sql .= "   CASE t_client.slip_out \n";
    $sql .= "       WHEN '1' THEN CASE t_sale_h.hand_slip_flg ";
    $sql .= "                       WHEN 't' THEN CASE\n";
    $sql .= "                                       WHEN t_sale_h.slip_out_day IS NOT NULL THEN CAST(t_sale_h.slip_out_day AS varchar)";
    $sql .= "                                       ELSE NULL ";
    $sql .= "                                   END ";
    $sql .= "                       ELSE CASE \n";
    $sql .= "                           WHEN t_aorder_h.slip_out_day IS NOT NULL THEN CAST(t_aorder_h.slip_out_day AS varchar)";
    $sql .= "                           ELSE NULL ";
    $sql .= "                       END ";
    $sql .= "                   END ";
    $sql .= "       WHEN '2' THEN '����' ";
    $sql .= "       WHEN '3' THEN '¾ɼ' ";
    $sql .= "   END AS slip_out_day, " ;

    //�������26
    $sql .= "   CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (11,15,61) \n";
    $sql .= "       THEN t_sale_h.cost_amount *  1 \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13,14,63,64)\n";
    $sql .= "       THEN t_sale_h.cost_amount * -1 \n";
    $sql .= "   END AS cost_amount, \n";                                        // 24 �Ҳ���������ǹ���

	// ��ԡ���
	$sql .= "	CASE \n";
	$sql .= "		WHEN t_aorder_h.contract_id IS NOT NULL THEN NULL \n";
	$sql .= "		WHEN t_aorder_h.contract_id IS NULL THEN '�����' \n";
	$sql .= "	END AS repeat \n";

    $sql .= "FROM \n";
    $sql .= "   t_sale_h \n";
    $sql .= "   INNER JOIN t_client ON t_sale_h.client_id = t_client.client_id \n";
    $sql .= "   LEFT JOIN t_client AS t_client_claim ON t_sale_h.claim_id = t_client_claim.client_id \n";
    $sql .= "   LEFT JOIN t_client AS t_act_client   ON t_sale_h.act_id = t_act_client.client_id \n";
    $sql .= "   LEFT JOIN t_aorder_h ON t_sale_h.aord_id = t_aorder_h.aord_id \n";
    $sql .= "   LEFT JOIN \n";
    $sql .= "   ( \n";
    $sql .= "       SELECT \n";
    $sql .= "           t_sale_staff.sale_id, \n";
    $sql .= "           t_sale_staff.staff_id, \n";
    $sql .= "           CAST(LPAD(t_staff.charge_cd, 4, '0') AS TEXT) AS charge_cd, \n";
    $sql .= "           t_sale_staff.staff_name, \n";
    $sql .= "           t_attach.part_id \n";
    $sql .= "       FROM \n";
    $sql .= "           t_sale_staff \n";
    $sql .= "           LEFT JOIN t_staff ON t_sale_staff.staff_id = t_staff.staff_id \n";
    $sql .= "           LEFT JOIN t_attach ON t_sale_staff.staff_id = t_attach.staff_id \n";
    $sql .= "           AND t_attach.shop_id = $shop_id \n";
    $sql .= "       WHERE \n";
    $sql .= "           t_sale_staff.staff_div = '0' \n";
    $sql .= "       AND \n";
    $sql .= "           t_sale_staff.sale_rate IS NOT NULL \n";
    $sql .= "   ) \n";
    $sql .= "   AS t_round_staff \n";
    $sql .= "   ON t_sale_h.sale_id = t_round_staff.sale_id \n";
    $sql .= "   LEFT JOIN t_buy_h AS intro_buy_h ON t_sale_h.sale_id = intro_buy_h.intro_sale_id \n";
    $sql .= "   LEFT JOIN t_buy_h AS act_buy_h ON t_sale_h.sale_id = act_buy_h.act_sale_id \n";
    $sql .= "   LEFT JOIN t_client_gr ON t_client.client_gr_id = t_client_gr.client_gr_id \n";
    // ����å�
    $sql .= "WHERE \n";
    if ($_SESSION["group_kind"] == "2"){
    $sql .= "   t_sale_h.shop_id IN (".Rank_Sql().") \n";
    }else{
    $sql .= "   t_sale_h.shop_id = $shop_id \n";
    }
    // ��ɼ�ֹ����ֺ�
    $sql .= "AND \n";
    $sql .= "   t_sale_h.sale_no IS NOT NULL \n";

    $sql .= $where_sql;
    $sql .= "ORDER BY \n";
    $sql .= $order_sql;

    // ���������
    $res            = Db_Query($db_con, $sql.";");
    $total_count    = pg_num_rows($res);

    // LIMIT, OFFSET������
    if ($post_flg == true && $err_flg != true){

        // ɽ�����
        switch ($display_num){
            case "1":
                $limit = $total_count;
                break;
            case "2":
                $limit = "100";
                break;
        }

        // �������ϰ���
        $offset = ($page_count != null) ? ($page_count - 1) * $limit : "0";

        // �Ժ���ǥڡ�����ɽ������쥳���ɤ�̵���ʤ�����н�
        if($page_count != null){
            // �Ժ����total_count��offset�δط������줿���
            if ($total_count <= $offset){
                // ���ե��åȤ����������
                $offset     = $offset - $limit;
                // ɽ������ڡ�����1�ڡ������ˡʰ쵤��2�ڡ���ʬ�������Ƥ������ʤɤˤ��б����Ƥʤ��Ǥ���
                $page_count = $page_count - 1;
                // �������ʲ����ϥڡ������ܤ���Ϥ����ʤ�(null�ˤ���)
                $page_count = ($total_count <= $display_num) ? null : $page_count;
            }
        }else{
            $offset = 0;
        }

    }

    // �ڡ�����ǡ�������
    $limit_offset   = ($limit != null) ? "LIMIT $limit OFFSET $offset " : null;
    $res            = Db_Query($db_con, $sql.$limit_offset.";");
    $match_count    = pg_num_rows($res);
    $ary_sale_data  = Get_Data($res, 2, "ASSOC");

}


/****************************/
// CSV����
/****************************/
if ($post_flg == true && $err_flg != true && $output_type == "2"){

    $sql  = "SELECT \n";
    $sql .= "    t_sale_h.sale_id, \n";             // 0
    $sql .= "    t_sale_h.contract_div, \n";        // 1
    $sql .= "    t_sale_h.client_cd1 || '-' || t_sale_h.client_cd2 AS client_cd, \n";   // 2
    $sql .= "    t_sale_h.client_cname, \n";        // 3
    $sql .= "    t_sale_h.sale_no, \n";             // 4
    $sql .= "    t_sale_h.sale_day, \n";            // 5
    $sql .= "    t_round_staff.charge_cd, \n";      // 6
    $sql .= "    t_round_staff.staff_name, \n";     // 7
    $sql .= "    t_round_staff.sale_rate, \n";      // 8
    $sql .= "    t_sale_h.act_cd1 || '-' || t_sale_h.act_cd2 AS act_cd, \n";    // 9
    $sql .= "    t_sale_h.act_cname, \n";           //10
    $sql .= "    t_sale_h.act_div, \n";             //11
    $sql .= "    t_sale_h.act_request_price, \n";   //12
    $sql .= "    t_sale_h.act_request_rate, \n";    //13
    $sql .= "    CASE \n";
    $sql .= "        WHEN act_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "        THEN (act_buy_h.net_amount + act_buy_h.tax_amount) *  1 \n";
    $sql .= "        WHEN act_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "        THEN (act_buy_h.net_amount + act_buy_h.tax_amount) * -1 \n";
    $sql .= "    END AS act_amount, \n";            //14 ��������ǹ���
    $sql .= "    CASE \n";
    $sql .= "        WHEN intro_buy_h.trade_id IN (21, 25, 71) \n";
    $sql .= "        THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) *  1 \n";
    $sql .= "        WHEN intro_buy_h.trade_id IN (23, 24, 73, 74) \n";
    $sql .= "        THEN (intro_buy_h.net_amount + intro_buy_h.tax_amount) * -1 \n";
    $sql .= "    END AS intro_amount, \n";          //15 �Ҳ���������ǹ���
    $sql .= "    CASE t_sale_h.trade_id \n";
    $sql .= "        WHEN '11' THEN '�����' \n";
    $sql .= "        WHEN '15' THEN '�������' \n";
    $sql .= "        WHEN '13' THEN '������' \n";
    $sql .= "        WHEN '14' THEN '���Ͱ�' \n";
    $sql .= "        WHEN '61' THEN '�������' \n";
    $sql .= "        WHEN '63' THEN '��������' \n";
    $sql .= "        WHEN '64' THEN '�����Ͱ�' \n";
    $sql .= "    END AS trade, \n";                 //16
    $sql .= "    t_sale_h.cost_amount, \n";         //17
    $sql .= "    t_sale_h.net_amount, \n";          //18
    $sql .= "    t_sale_h.tax_amount, \n";          //19
    $sql .= "    t_sale_h.advance_offset_totalamount, \n";      //20
    $sql .= "    t_sale_h.total_split_num, \n";     //21
    $sql .= "    to_date(t_sale_h.renew_day, 'YYYY-MM-DD') AS renew_day, \n";   //22
    $sql .= "    CASE t_sale_d.sale_div_cd \n";
    $sql .= "        WHEN '01' THEN '��ԡ���' \n";
    $sql .= "        WHEN '02' THEN '����' \n";
    $sql .= "        WHEN '03' THEN '��󥿥�' \n";
    $sql .= "        WHEN '04' THEN '�꡼��' \n";
    $sql .= "        WHEN '05' THEN '����' \n";
    $sql .= "        WHEN '06' THEN '����¾' \n";
    $sql .= "    END AS sale_div, \n";              //23
    $sql .= "    t_sale_d.serv_name, \n";           //24
    $sql .= "    t_sale_d.goods_cd, \n";            //25
    $sql .= "    t_sale_d.official_goods_name, \n"; //26
    $sql .= "    CASE t_sale_d.set_flg \n";
    $sql .= "        WHEN true THEN '��' \n";
    $sql .= "    END AS set_flg, \n";               //27


    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (13, 63) THEN t_sale_d.num * -1 \n";
    $sql .= "       ELSE t_sale_d.num \n";
    $sql .= "    END AS num, \n";                   //28

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,64) THEN t_sale_d.cost_price * -1 \n";
    $sql .= "       ELSE t_sale_d.cost_price \n";
    $sql .= "    END AS cost_price, \n";            //29

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,13,64,63) THEN t_sale_d.cost_amount * -1 \n";
    $sql .= "       ELSE t_sale_d.cost_amount \n";
    $sql .= "    END AS cost_amount, \n";           //30

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,64) THEN t_sale_d.sale_price * -1 \n";
    $sql .= "       ELSE t_sale_d.sale_price \n";
    $sql .= "    END AS sale_price, \n";          //31

    $sql .= "    CASE \n";
    $sql .= "       WHEN t_sale_h.trade_id IN (14,13,64,63) THEN t_sale_d.sale_price * -1 \n";
    $sql .= "       ELSE t_sale_d.sale_amount \n";  //32
    $sql .= "    END AS sale_amount, ";

    $sql .= "    t_sale_d.egoods_cd, \n";           //33
    $sql .= "    t_sale_d.egoods_name, \n";         //34
    $sql .= "    t_sale_d.egoods_num, \n";          //35
    $sql .= "    t_sale_d.rgoods_cd, \n";           //36
    $sql .= "    t_sale_d.rgoods_name, \n";         //37
    $sql .= "    t_sale_d.rgoods_num, \n";          //38
    $sql .= "    t_sale_h.intro_ac_div, \n";        //39
    $sql .= "    t_sale_d.account_price, \n";       //40
    $sql .= "    t_sale_d.account_rate, \n";        //41
    $sql .= "    CASE t_sale_d.advance_flg \n";
    $sql .= "        WHEN '1' THEN '�ʤ�' \n";
    $sql .= "        ELSE CAST(t_sale_d.advance_offset_amount AS TEXT) \n";
    $sql .= "    END AS advance_offset, \n";        //42
    $sql .= "   t_sale_h.claim_day, \n";            // 43
    $sql .= "   t_sale_h.client_name2, \n";         // 44
    $sql .= "    t_sale_d.serv_cd, \n";             // 45
    $sql .= "    t_client_gr.client_gr_cd,\n ";     //46�����襰�롼�ץ�����
    $sql .= "    t_client_gr.client_gr_name, \n";   //47�����襰�롼��̾
    // 48��ԡ���
	$sql .= "	 CASE \n";
	$sql .= "		WHEN t_aorder_h.contract_id IS NOT NULL THEN NULL \n";
    $sql .= "		WHEN t_aorder_h.contract_id IS NULL THEN '�����' \n";
	$sql .= "	 END \n";

    $sql .= "FROM \n";
    $sql .= "    t_sale_h \n";
    $sql .= "    INNER JOIN t_sale_d ON t_sale_h.sale_id = t_sale_d.sale_id \n";
    $sql .= "	 LEFT JOIN t_aorder_h ON t_sale_h.aord_id = t_aorder_h.aord_id \n";
    $sql .= "    INNER JOIN t_client ON t_sale_h.client_id = t_client.client_id \n";
    $sql .= "    LEFT JOIN t_client AS t_client_claim ON t_sale_h.claim_id = t_client_claim.client_id \n";
    $sql .= "    LEFT JOIN t_client AS t_act_client ON t_sale_h.act_id = t_act_client.client_id \n";
    $sql .= "    LEFT JOIN ( \n";
    $sql .= "        SELECT \n";
    $sql .= "            t_sale_staff.sale_id, \n";
    $sql .= "            t_sale_staff.staff_id, \n";
    $sql .= "            CAST(LPAD(t_staff.charge_cd, 4, '0') AS TEXT) AS charge_cd, \n";
    $sql .= "            t_sale_staff.staff_name, \n";
    $sql .= "            t_sale_staff.sale_rate, \n";
    $sql .= "            t_attach.part_id \n";
    $sql .= "        FROM \n";
    $sql .= "            t_sale_staff \n";
    $sql .= "            LEFT JOIN t_staff ON t_sale_staff.staff_id = t_staff.staff_id \n";
    $sql .= "            LEFT JOIN t_attach ON t_sale_staff.staff_id = t_attach.staff_id \n";
    if($_SESSION["group_kind"] == "2"){
        $sql .= "                AND t_attach.shop_id IN (".Rank_Sql().") \n";
    }else{
        $sql .= "                AND t_attach.shop_id = $shop_id \n";
    }
    $sql .= "        WHERE \n";
    $sql .= "            t_sale_staff.staff_div = '0' \n";
    $sql .= "    ) AS t_round_staff ON t_sale_h.sale_id = t_round_staff.sale_id \n";
    $sql .= "    LEFT JOIN t_buy_h AS intro_buy_h ON t_sale_h.sale_id = intro_buy_h.intro_sale_id \n";
    $sql .= "    LEFT JOIN t_buy_h AS act_buy_h ON t_sale_h.sale_id = act_buy_h.act_sale_id \n";
    $sql .= "    LEFT JOIN t_client_gr ON t_client.client_gr_id = t_client_gr.client_gr_id \n";

    $sql .= "WHERE \n";
    if($_SESSION["group_kind"] == "2"){
        $sql .= "    t_sale_h.shop_id IN (".Rank_Sql().") \n";
    }else{
        $sql .= "    t_sale_h.shop_id = $shop_id \n";
    }
    $sql .= $where_sql;

    $sql .= "ORDER BY \n";
    $sql .= $order_sql.", ";
    $sql .= "    t_sale_d.line \n";

    $result = Db_Query($db_con, $sql.";");
    $page_data  = Get_Data($result, 2);

    // �ե�����̾
    $csv_file_name = "���������.csv";

    //CSV�إå�����
    $csv_header   = array();    //��������
    $csv_header[] = "���롼�ץ�����";
    $csv_header[] = "���롼��̾";
    $csv_header[] = "�����襳����";
    $csv_header[] = "������̾";
    $csv_header[] = "������̾2";
    $csv_header[] = "��ɼ�ֹ�";
    $csv_header[] = "���׾���";
    $csv_header[] = "������";
    $csv_header[] = "���ô���ԥ����ɡʥᥤ�󣱡�";
    $csv_header[] = "���ô����̾�ʥᥤ�󣱡�";
    if($_SESSION["group_kind"] == "2"){
        $csv_header[] = "����襳����";
        $csv_header[] = "�����̾";
        $csv_header[] = "��԰�����";
    }
    $csv_header[] = "�Ҳ������";
    $csv_header[] = "�����ʬ";
    $csv_header[] = "�������";
    $csv_header[] = "�����";
    $csv_header[] = "�����ǳ�";
    $csv_header[] = "�����껦�۹��";
    $csv_header[] = "ʬ����";
    $csv_header[] = "��ԡ���";
    $csv_header[] = "����������";

    $csv_header[] = "�����ʬ";
    $csv_header[] = "�����ӥ�������";
    $csv_header[] = "�����ӥ�̾";
    $csv_header[] = "�����ƥॳ����";
    $csv_header[] = "�����ƥ�";
    $csv_header[] = "�켰";
    $csv_header[] = "����";
    $csv_header[] = "�Ķȸ���";
    $csv_header[] = "������׳�";
    $csv_header[] = "���ñ��";
    $csv_header[] = "����׳�";
    $csv_header[] = "�����ʥ�����";
    $csv_header[] = "������";
    $csv_header[] = "����";
    $csv_header[] = "���ξ��ʥ�����";
    $csv_header[] = "���ξ���";
    $csv_header[] = "����";
    $csv_header[] = "������(����ñ��)";
    $csv_header[] = "�����껦��";

    // CSV�ǡ�������
    $sale_data  = array();
    $sale_id    = null;

    for ($i = 0; $i < count($page_data); $i++){

        $sale_data[$i][] = $page_data[$i][46];          //���롼�ץ�����
        $sale_data[$i][] = $page_data[$i][47];          //���롼��̾
        
        $sale_data[$i][] = $page_data[$i][2];           // �����襳����
        $sale_data[$i][] = $page_data[$i][3];           // ������̾
        $sale_data[$i][] = $page_data[$i][44];          // ������
        $sale_data[$i][] = $page_data[$i][4];           // ��ɼ�ֹ�
        $sale_data[$i][] = $page_data[$i][5];           // ���׾���
        $sale_data[$i][] = $page_data[$i][43];          // ������
        $sale_data[$i][] = $page_data[$i][6];           // ���ô���ԥ����ɡʥᥤ�󣱡�
        $sale_data[$i][] = $page_data[$i][7];           // ���ô����̾�ʥᥤ�󣱡�
        if($_SESSION["group_kind"] == "2"){
            $sale_data[$i][] = $page_data[$i][9];       // ����襳����
            $sale_data[$i][] = $page_data[$i][10];      // �����̾
            $sale_data[$i][] = $page_data[$i][14];      // �����
        }
        $sale_data[$i][] = $page_data[$i][15];          // �Ҳ���
        $sale_data[$i][] = $page_data[$i][16];          // �����ʬ
        if($page_data[$i][16] == "�����" || $page_data[$i][16] == "�������" || $page_data[$i][16] == "�������"){
            $sale_data[$i][] = $page_data[$i][17];          // �������
            $sale_data[$i][] = $page_data[$i][18];      // �����
            $sale_data[$i][] = $page_data[$i][19];      // �����ǳ�
        }else{
            $sale_data[$i][] = -1 * $page_data[$i][17];          // �������
            $sale_data[$i][] = -1 * $page_data[$i][18]; // �����
            $sale_data[$i][] = -1 * $page_data[$i][19]; // �����ǳ�
        }
        $sale_data[$i][] = $page_data[$i][20];          // �����껦��
        $sale_data[$i][] = $page_data[$i][21];          // ʬ����
        $sale_data[$i][] = $page_data[$i][48];          // ��ԡ���
        $sale_data[$i][] = $page_data[$i][22];          // ��������

        $sale_data[$i][] = $page_data[$i][23];          // �����ʬ
        $sale_data[$i][] = $page_data[$i][45];          // �����ӥ�������
        $sale_data[$i][] = $page_data[$i][24];          // �����ӥ�̾
        $sale_data[$i][] = $page_data[$i][25];          // �����ƥॳ����
        $sale_data[$i][] = $page_data[$i][26];          // �����ƥ�
        $sale_data[$i][] = $page_data[$i][27];          // �켰
        $sale_data[$i][] = $page_data[$i][28];          // ����
        $sale_data[$i][] = $page_data[$i][29];          // �Ķȸ���
        $sale_data[$i][] = $page_data[$i][30];          // �Ķȶ��
        $sale_data[$i][] = $page_data[$i][31];          // ���ñ��
        $sale_data[$i][] = $page_data[$i][32];          // �����
        $sale_data[$i][] = $page_data[$i][33];          // �����ʥ�����
        $sale_data[$i][] = $page_data[$i][34];          // ������
        $sale_data[$i][] = $page_data[$i][35];          // ����
        $sale_data[$i][] = $page_data[$i][36];          // ���ξ��ʥ�����
        $sale_data[$i][] = $page_data[$i][37];          // ���ξ���
        $sale_data[$i][] = $page_data[$i][38];          // ����
        // �Ҳ�����ʬ���־����̡פξ�硢��������ʬ�����
        if ($page_data[$i][39] == "4"){
            if ($page_data[$i][40] != null){
                $sale_data[$i][] = "����� ".(string)$page_data[$i][40]."��";   // ������(����ñ��)�ʸ���ۡ�
            }else
            if ($page_data[$i][41] != null){
                $sale_data[$i][] = "���� ".$page_data[$i][41]."��";           // ������(����ñ��)��Ψ��
            }
        // ����ʳ��϶�������
        }else{
            $sale_data[$i][] = "";
        }
        $sale_data[$i][] = $page_data[$i][42];          // �����껦��

    }// �ǡ����Կ�ʬ�롼�פ����


    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($sale_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;

}


/****************************/
// ɽ���ѥǡ�������
/****************************/
if ($post_flg == true && $err_flg != true){

    // �����å��ܥå�����ɽ�����ֹ�����
    $hidden_check = array();

    if ($ary_sale_data != null){
    foreach ($ary_sale_data as $key => $value){

        // ���ID��hidden���ɲ�
        $form->addElement("hidden", "output_id_array[$key]");           // ��ɼ�������ID����
        $sale_id_set["output_id_array[$key]"] = $value["sale_id"];      // ���ID

        // ��ɼȯ��Ƚ��
        if($value["slip_out"] == "̵"){
            // �����å��ܥå�����ɽ�����ֹ����
            $hidden_check[] = $key;
        }

        // �����Ѳ�������
        $ary_sale_data[$key]["return"] = (bcmod($key, 45) == 0 && $key != "0") ? " style=\"page-break-before: always;\"" : null;

        // ��ɼ�ֹ��󥯺���
        $link_module = ($value["hand_slip_flg"] == "f") ? "2-2-202" : "2-2-201";
        $ary_sale_data[$key]["slip_no_link"]  = "<a href=\"./".$link_module.".php?sale_id=".$value["sale_id"]."&renew_flg=true\">";
        $ary_sale_data[$key]["slip_no_link"] .= $value["sale_no"];
        $ary_sale_data[$key]["slip_no_link"] .= "</a>";

        // ��ô���ԥ����ɡ�ô����̾�פ����
        if ($value["charge_cd"] != null){
            $ary_sale_data[$key]["staff"] = str_pad($value["charge_cd"], 4, "0", STR_PAD_LEFT)."��".htmlspecialchars($value["staff_name"]);
        }

        // �ݡ�����θ�ʬ��
        if ($value["trade"] == "�����" || $value["trade"] == "������" || $value["trade"] == "���Ͱ�" || $value["trade"] == "�������"){
            $ary_sale_data[$key]["trade_type"] = "kake";
        }else{
            $ary_sale_data[$key]["trade_type"] = "genkin";
        }

        // ʬ������󥯺���
        if ($value["trade"] == "�������"){
            $ary_sale_data[$key]["split_link"]  = "<a href=\"./2-2-214.php?sale_id=".$value["sale_id"]."&division_flg=true\">";
            $ary_sale_data[$key]["split_link"] .= $value["total_split_num"]."��";
            $ary_sale_data[$key]["split_link"] .= "</a>";
        }else{
            $ary_sale_data[$key]["split_link"]  = null;
        }

        // �ѹ�����á������󥯺���
        //�ʲ��Τ��Ť줫�����ƤϤޤ�����ѹ��������󥯤�ɽ�����ʤ�
        //----------------------------//
        //�����������Ƥ���
        //�����������������ɼ
        //����饤�����
        //�����ɼ����ʤ��ơ��������餭����ɼ����ʤ��ʰ�̣������
        //�����ˤ�������������Ҳ����λ�����������������Ƥ���
        //���⤽�⸢�¤��ʤ�
        //----------------------------//
        if (
            $value["renew"] == "��" || $value["act_request_flg"] == "t" ||
            //$value["contract_div"] == "2" || $value["contract_div"] == "3" ||
            $value["contract_div"] == "2" || 
            ($value["hand_slip_flg"] == "f" && $value["aord_id"] == NULL) ||
            $value["buy_renew_flg"] == "t" || 
            $disabled != null
        ){
            // �ѹ����
            $ary_sale_data[$key]["chg_link"] = null;
            // ������
            $ary_sale_data[$key]["del_link"] = null;
        }else{
            // �ѹ����
            if($value["contract_div"] == "3"){
                $ary_sale_data[$key]["chg_link"] = null;
            }else{
                $ary_sale_data[$key]["chg_link"] = "<a href=\"./".$link_module.".php?sale_id=".$value["sale_id"]."\">�ѹ�</a>";
            }
            // ������
            if ($value["hand_slip_flg"] == "t"){
                $ary_sale_data[$key]["del_link"]  = "<a href=\"#\" ";
                $ary_sale_data[$key]["del_link"] .= "onClick=\"return Dialogue_2('������ޤ���', '".$_SERVER["PHP_SELF"]."', ".$value["sale_id"].", 'hdn_delete_id');\"";
                $ary_sale_data[$key]["del_link"] .= ">���</a>";
            }else{
                $ary_sale_data[$key]["del_link"]  = "<a href=\"#\" ";
                $ary_sale_data[$key]["del_link"] .= "onClick=\"return Dialogue_2('������ޤ���', '".$_SERVER["PHP_SELF"]."',  ".$value["sale_id"].", 'hdn_cancel_id');\"";
                $ary_sale_data[$key]["del_link"] .= ">���</a>";
            }
        }

        // �ݡ��������ȴ�������ǳۤ򻻽�
        // ������
        if ($value["trade"] == "�����" || $value["trade"] == "������" || $value["trade"] == "���Ͱ�" || $value["trade"] == "�������"){
            // ����������ȴ�˻���
            $kake_notax     += $value["net_amount"];
            // �ݾ����ǻ���
            $kake_tax       += $value["tax_amount"];
        // ��������
        }else{
            // ����������ȴ�˻���
            $genkin_notax   += $value["net_amount"];
            // ��������ǻ���
            $genkin_tax     += $value["tax_amount"];
        }


        // ��׹��Ѥ˶�ۻ���
        switch ($value["trade"]){
            case "�����":
            case "�������":
                $gross_act_amount           += $value["act_amount"];
                $gross_intro_amount         += $value["intro_amount"];
                $gross_kake_notax_amount    += $value["net_amount"];
                $gross_kake_ontax_amount    += $value["net_amount"] + $value["tax_amount"];
                $gross_advance_offset       += $value["advance_offset_totalamount"];
                $gross_cost_amount          += $value["cost_amount"];
                break;
            case "������":
            case "���Ͱ�":
                $minus_act_amount           += $value["act_amount"];
                $minus_intro_amount         += $value["intro_amount"];
                $minus_kake_notax_amount    += $value["net_amount"];
                $minus_kake_ontax_amount    += $value["net_amount"] + $value["tax_amount"];
                $minus_advance_offset       += $value["advance_offset_totalamount"];
                $minus_cost_amount          += $value["cost_amount"];
                break;
            case "�������":
                $gross_act_amount           += $value["act_amount"];
                $gross_intro_amount         += $value["intro_amount"];
                $gross_genkin_notax_amount  += $value["net_amount"];
                $gross_genkin_ontax_amount  += $value["net_amount"] + $value["tax_amount"];
                $gross_advance_offset       += $value["advance_offset_totalamount"];
                $gross_cost_amount          += $value["cost_amount"];
                break;
            case "��������":
            case "�����Ͱ�":
                $minus_act_amount           += $value["act_amount"];
                $minus_intro_amount         += $value["intro_amount"];
                $minus_genkin_notax_amount  += $value["net_amount"];
                $minus_genkin_ontax_amount  += $value["net_amount"] + $value["tax_amount"];
                $minus_advance_offset       += $value["advance_offset_totalamount"];
                $minus_cost_amount          += $value["cost_amount"];
                break;
        }
        // �������פ򻻽�
        $sum_act_amount += $value["act_amount"];

        // �Ҳ��������פ򻻽�
        $sum_intro_amount += $value["intro_amount"];

        // �����껦�ۤι�פ򻻽�
        $sum_advance_offset += $value["advance_offset_totalamount"];

        //�������
        $sum_cost_amount += $value["cost_amount"];
    }
    }
    $form->setConstants($sale_id_set); 

    // ������
    $kake_ontax     = $kake_notax + $kake_tax;
    // ������
    $genkin_ontax   = $genkin_notax + $genkin_tax;
    // ��ȴ���
    $notax_amount   = $kake_notax + $genkin_notax;
    // �����ǹ��
    $tax_amount     = $kake_tax + $genkin_tax;
    // �ǹ����
    $ontax_amount   = $kake_ontax + $genkin_ontax;

}


/****************************/
// POST���˥����å������
/****************************/
//2009-09-08 hashimoto-y
if ($_POST != null){
    for($i = 0; $i < $match_count; $i++){
        $clear_check["form_slip_check"][$i] = "";
        $clear_check["form_republish_check"][$i] = "";
    }
    $clear_check["form_slip_all_check"] = "";
    $clear_check["form_republish_all_check"] = "";
    $form->setConstants($clear_check);
}


/****************************/
// �ե�����ưŪ���ʺ���
/****************************/
// ��ɼȯ��ALL�����å�
$form->addElement("checkbox", "form_slip_all_check", "", "ȯ��", "
    onClick=\"javascript:All_check('form_slip_all_check', 'form_slip_check', $match_count)\"
");

// ��ɼ��ȯ��ALL�����å�
//2009-09-08 hashimoto-y
$form->addElement("checkbox", "form_republish_all_check", "", "��ȯ��", "
    onClick=\"javascript:All_check('form_republish_all_check', 'form_republish_check', $match_count)\"
");


// ��ɼȯ�ԥ����å�
for ($i = 0; $i < $match_count; $i++){

    //�����å��ܥå���ɽ��Ƚ��
    //��ɼȯ��ͭ��̤ȯ��
    if($ary_sale_data[$i]["slip_out_day"] == '' && $ary_sale_data[$i]["slip_out"] == "ͭ"){
        $form->addElement("checkbox", "form_slip_check[$i]", "", "", "", "");
    }else{
        $freeze = $form->addElement("text", "form_slip_check[$i]", "", "");
        $set_data["form_slip_check"][$i] = $ary_sale_data["$i"]["slip_out_day"]; 
        $freeze->freeze();
    }


    //2009-09-08 hashimoto-y
    if($ary_sale_data[$i]["slip_out_day"] != '' && $ary_sale_data[$i]["slip_out"] == "ͭ"){
        $form->addElement("checkbox", "form_republish_check[$i]", "", "", "", "");
    }else{
        $freeze = $form->addElement("text", "form_republish_check[$i]", "", "");
        $freeze->freeze();
    }


/*
    // ɽ����Ƚ��
    if (!in_array("$i", $hidden_check)){
        // ̤��ǧ�Ԥϥ����å��ܥå������
        $form->addElement("checkbox", "form_slip_check[$i]", "", "", "", "");
    }else{
        // ��ǧ�Ԥ���ɽ���ˤ���٤�hidden�����
        $form->addElement("hidden","form_slip_check[$i]");
    }
*/
}

// ȯ�ԥܥ���
$form->addElement("button", "form_sale_slip", "ȯ����", "
    onClick=\"javascript:Post_book_vote2('�����ɼ��ȯ�Ԥ��ޤ���', 'sale_slip_flg',
    '".FC_DIR."sale/2-2-205.php', '".$_SESSION["PHP_SELF"]."', 'form_slip_check', $match_count)\"
");

// ��ȯ�ԥܥ���
$form->addElement("button", "form_sale_republish", "��ȯ��", "
    onClick=\"javascript:Post_book_vote2('�����ɼ���ȯ�Ԥ��ޤ���', 'sale_republish_flg',
    '".FC_DIR."sale/2-2-205.php', '".$_SESSION["PHP_SELF"]."', 'form_republish_check', $match_count)\"
");

// �вٰ����˥ǥե�����ͥ��å�
$form->setConstants($set_data);


/****************************/
// HTML�Ѵؿ�
/****************************/
function Number_Format_Color($num){
    return ($num < 0) ? "<span style=\"color: #ff0000;\">".number_format($num)."</span>" : number_format($num);
}


/****************************/
// HTML�����ʸ�������
/****************************/
// ���̸����ơ��֥�
$html_s .= Search_Table($form, true);
// �⥸�塼����̸����ơ��֥룱
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">��ɼ�ֹ�</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_slip_no"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">�����</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_sale_amount"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// �⥸�塼����̸����ơ��֥룲
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
if ($_SESSION["group_kind"] == "2"){
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";
}
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">���׾���</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_sale_day"]]->toHtml()."</td>\n";
if ($_SESSION["group_kind"] == "2"){
$html_s .= "        <td class=\"Td_Search_3\">�����ʬ</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_contract_div"]]->toHtml()."</td>\n";
}
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// �⥸�塼����̸����ơ��֥룳
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"746px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">���롼��</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_client_gr"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// �⥸�塼����̸����ơ��֥룴
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";
$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">�����ʬ</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_trade"]]->toHtml()."</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">��������</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_renew"]]->toHtml()."</td>\n";
$html_s .= "    </tr>\n";
$html_s .= "</table>\n";
$html_s .= "\n";
// �⥸�塼����̸����ơ��֥룵
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";
#2010-04-26 hashimot-y
$html_s .= "    <col width=\" 90px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"350px\">\n";

$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">��ԡ���</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_repeat"]]->toHtml()."</td>\n";
#2010-04-26 hashimot-y
$html_s .= "        <td class=\"Td_Search_3\">�����ƥ�</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_goods"]]->toHtml()."</td>\n";

$html_s .= "    </tr>\n";
$html_s .= "</table>\n";


// �⥸�塼����̸����ơ��֥룵
#2010-04-26 hashimot-y
$html_s .= "<br style=\"font-size: 4px;\">\n";
$html_s .= "\n";
$html_s .= "<table class=\"Table_Search\">\n";
$html_s .= "    <col width=\" 80px\" style=\"font-weight: bold;\">\n";
$html_s .= "    <col width=\"300px\">\n";

$html_s .= "    <tr>\n";
$html_s .= "        <td class=\"Td_Search_3\">�����ʬ</td>\n";
$html_s .= "        <td class=\"Td_Search_3\">".$form->_elements[$form->_elementIndex["form_divide"]]->toHtml()."</td>\n";

$html_s .= "    </tr>\n";
$html_s .= "</table>\n";


// �ܥ���
$html_s .= "<table align=\"right\"><tr><td>\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["form_display"]]->toHtml()."��\n";
$html_s .= "    ".$form->_elements[$form->_elementIndex["form_clear"]]->toHtml()."\n";
$html_s .= "</td></tr></table>";
$html_s .= "\n";


/****************************/
// HTML�����ʰ�������
/****************************/
if ($post_flg == true){

    // �ڡ���ʬ��
    $html_page  = Html_Page2($total_count, $page_count, 1, $limit);
    $html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

    // �����ơ��֥�
    $html_1  = "\n";
    $html_1 .= "<table width=\"100%\" border=\"0\">\n";
    $html_1 .= "    <tr>\n";
    $html_1 .= "        <td align=\"right\">\n";
    $html_1 .= "        <span style=\"color: #0000ff; font-weight: bold;\">����ԡ�����ˤϷ���ޥ�������Ͽ�Τʤ���Τ���������פ�ɽ������ޤ�</span>\n";
    $html_1 .= "        </td>\n";
    $html_1 .= "</table>\n";
    $html_1 .= "<table class=\"List_Table\" width=\"100%\" border=\"1\">\n";
    $html_1 .= "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">No.</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_client_name")."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_slip")."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_sale_day")."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">".Make_Sort_Link($form, "sl_round_staff")."</td>\n";
    if ($_SESSION["group_kind"] == "2"){
        $html_1 .= "        <td class=\"Title_Act\" rowspan=\"2\">".Make_Sort_Link($form, "sl_act_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_act_client_name")."</td>\n";
        $html_1 .= "        <td class=\"Title_Act\" rowspan=\"2\">��԰�����</td>\n";
    }
    $html_1 .= "        <td class=\"Title_Act\" rowspan=\"2\">�Ҳ������</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">�����ʬ</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">�������</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" colspan=\"2\">������</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" colspan=\"2\">������</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">����<br>�껦��</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">�ѹ�</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">���</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">ʬ��<br>���</td>\n";
    //2009-09-08 hashimoto-y
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">�؎ˎߎ���</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">��������</td>\n";
//    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">�вٰ����</td>";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">";
    $html_1 .=          $form->_elements[$form->_elementIndex["form_slip_all_check"]]->toHtml()."</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\" rowspan=\"2\">";
    $html_1 .=          $form->_elements[$form->_elementIndex["form_republish_all_check"]]->toHtml()."</td>\n";
    $html_1 .= "    </tr>\n";
    $html_1 .= "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
    $html_1 .= "        <td class=\"Title_Pink\">��ȴ</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\">�ǹ�</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\">��ȴ</td>\n";
    $html_1 .= "        <td class=\"Title_Pink\">�ǹ�</td>\n";
    $html_1 .= "    </tr>\n";
    $html_1 .= "    <tr class=\"Result3\">\n";
    $html_1 .= "        <td><b>���</b></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    if ($_SESSION["group_kind"] == "2"){
    $html_1 .= "        <td></td>\n";
    //$html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_act_amount)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_act_amount)."<br>".Numformat_Ortho($minus_act_amount)."<br>".Numformat_Ortho($sum_act_amount)."<br></td>\n";
    }
/*
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_intro_amount)."</td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($sum_advance_offset)."</td>\n";
*/
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_intro_amount)."<br>".Numformat_Ortho($minus_intro_amount)."<br>".Numformat_Ortho($sum_intro_amount)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_cost_amount)."<br>".Numformat_Ortho($minus_cost_amount)."<br>".Numformat_Ortho($sum_cost_amount)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_notax_amount)."<br>".Numformat_Ortho($minus_kake_notax_amount)."<br>".Numformat_Ortho($kake_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_ontax_amount)."<br>".Numformat_Ortho($minus_kake_ontax_amount)."<br>".Numformat_Ortho($kake_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_notax_amount)."<br>".Numformat_Ortho($minus_genkin_notax_amount)."<br>".Numformat_Ortho($genkin_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_ontax_amount)."<br>".Numformat_Ortho($minus_genkin_ontax_amount)."<br>".Numformat_Ortho($genkin_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_advance_offset)."<br>".Numformat_Ortho($minus_advance_offset)."<br>".Numformat_Ortho($sum_advance_offset)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    //2009-09-08 hashimoto-y
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "    </tr>\n";
    if ($ary_sale_data != null){
    foreach ($ary_sale_data as $key => $value){
        if (bcmod($key, 2) == 0){
        $html_1 .= "    <tr class=\"Result1\"".$value["return"].">\n";
        }else{
        $html_1 .= "    <tr class=\"Result2\"".$value["return"].">\n";
        }
        $html_1 .= "        <td align=\"right\">".((($page_count - 1) * $limit) + $key + 1)."</td>\n";
        $html_1 .= "        <td>".$value["client_cd"]."<br>".htmlspecialchars($value["client_cname"])."<br></td>\n";
        $html_1 .= "        <td align=\"center\">".$value["slip_no_link"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["sale_day"]."</td>\n";
        $html_1 .= "        <td>".$value["staff"]."</td>\n";
        if ($_SESSION["group_kind"] == "2"){
            $html_1 .= "        <td>".$value["act_cd"]."<br>".htmlspecialchars($value["act_cname"])."<br></td>\n";
            $html_1 .= "        <td align=\"right\">".Numformat_Ortho($value["act_amount"], null, true)."</td>\n";
        }
        $html_1 .= "        <td align=\"right\">".Numformat_Ortho($value["intro_amount"], null, true)."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["trade"]."</td>";
        $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["cost_amount"])."</td>";
        if ($value["trade_type"] == "kake"){
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["net_amount"])."</td>\n";
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["sale_amount"])."</td>\n";
            $html_1 .= "        <td></td>\n";
            $html_1 .= "        <td></td>\n";
        }else{
            $html_1 .= "        <td></td>\n";
            $html_1 .= "        <td></td>\n";
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["net_amount"])."</td>\n";
            $html_1 .= "        <td align=\"right\">".Number_Format_Color($value["sale_amount"])."</td>\n";
        }
        $html_1 .= "        <td align=\"right\">".Numformat_Ortho($value["advance_offset_totalamount"], null, true)."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["chg_link"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["del_link"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["split_link"]."</td>\n";
        //2009-09-08 hashimoto-y
        $html_1 .= "        <td align=\"center\">".$value["repeat"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$value["renew_day"]."</td>\n";
        $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_slip_check[$key]"]]->toHtml()."</td>\n";
        $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_republish_check[$key]"]]->toHtml()."</td>\n";
        $html_1 .= "    </tr>\n";
    }
    }
    $html_1 .= "    <tr class=\"Result3\">\n";
    $html_1 .= "        <td><b>���</b></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    if ($_SESSION["group_kind"] == "2"){
    $html_1 .= "        <td></td>\n";
    //$html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_act_amount)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_act_amount)."<br>".Numformat_Ortho($minus_act_amount)."<br>".Numformat_Ortho($sum_act_amount)."<br></td>\n";
    }
/*
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($sum_intro_amount)."</td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($kake_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_notax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($genkin_ontax)."</td>\n";
    $html_1 .= "        <td align=\"right\">".Number_format_Color($sum_advance_offset)."</td>\n";
*/
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_intro_amount)."<br>".Numformat_Ortho($minus_intro_amount)."<br>".Numformat_Ortho($sum_intro_amount)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_cost_amount)."<br>".Numformat_Ortho($minus_cost_amount)."<br>".Numformat_Ortho($sum_cost_amount)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_notax_amount)."<br>".Numformat_Ortho($minus_kake_notax_amount)."<br>".Numformat_Ortho($kake_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_kake_ontax_amount)."<br>".Numformat_Ortho($minus_kake_ontax_amount)."<br>".Numformat_Ortho($kake_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_notax_amount)."<br>".Numformat_Ortho($minus_genkin_notax_amount)."<br>".Numformat_Ortho($genkin_notax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_genkin_ontax_amount)."<br>".Numformat_Ortho($minus_genkin_ontax_amount)."<br>".Numformat_Ortho($genkin_ontax)."<br></td>\n";
    $html_1 .= "        <td align=\"right\">".Numformat_Ortho($gross_advance_offset)."<br>".Numformat_Ortho($minus_advance_offset)."<br>".Numformat_Ortho($sum_advance_offset)."<br></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td></td>\n";
    //2009-09-08 hashimoto-y
    $html_1 .= "        <td></td>\n";
    $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_sale_slip"]]->toHtml()."</td>\n";
    $html_1 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_sale_republish"]]->toHtml()."</td>\n";
    $html_1 .= "    </tr>\n";
    $html_1 .= "</table>\n";
    $html_1 .= "\n";

    // ��ץơ��֥�
    $html_2  = "\n";
    $html_2 .= "<table class=\"List_Table\" border=\"1\" width=\"500px\" align=\"right\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"right\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"right\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"center\" style=\"font-weight: bold;\">\n";
    $html_2 .= "    <col width=\"100px\" align=\"right\">\n";
    $html_2 .= "    <tr class=\"Result1\">\n";
    $html_2 .= "        <td class=\"Title_Pink\">������</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($kake_notax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">���������</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($kake_tax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">������</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($kake_ontax)."</td>\n";
    $html_2 .= "    </tr>\n";
    $html_2 .= "    <tr class=\"Result1\">\n";
    $html_2 .= "        <td class=\"Title_Pink\">������</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($genkin_notax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">���������</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($genkin_tax)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">������</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($genkin_ontax)."</td>\n";
    $html_2 .= "    </tr>\n";
    $html_2 .= "    <tr class=\"Result1\">\n";
    $html_2 .= "        <td class=\"Title_Pink\">��ȴ���</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($notax_amount)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">�����ǹ��</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($tax_amount)."</td>\n";
    $html_2 .= "        <td class=\"Title_Pink\">�ǹ����</td>\n";
    $html_2 .= "        <td align=\"right\">".Number_format_Color($ontax_amount)."</td>\n";
    $html_2 .= "    </tr>\n";
    $html_2 .= "</table>\n";
    $html_2 .= "\n";

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
// ���̥إå�������
/****************************/
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

    "renew_flg"     => "$renew_flg",
    "act_buy_renew_err"     => "$act_buy_renew_err",
    "intro_buy_renew_err"   => "$intro_buy_renew_err",
));

// html��assign
$smarty->assign("html", array(
    "html_page"     =>  $html_page,
    "html_page2"    =>  $html_page2,
    "html_s"        =>  $html_s,
    "html_1"        =>  $html_1,
    "html_2"        =>  $html_2,
));

// msg��assign
$smarty->assign("msg", array(
    "err_msg_print" => $err_msg_print,
));

// �ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF].".tpl"));

?>