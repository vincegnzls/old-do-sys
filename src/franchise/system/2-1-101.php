<?php
/**************************
*�ѹ�����
*   ��2006/05/08��
*       �����ե�����ɽ���ܥ����ɲá�watanabe-k��
*       ɽ���ǡ�����������̾����ά�Τ��ѹ���watanabe-k��
*
*   ��2006/07/06��
*       shop_gid��ʤ�����kaji��
*    (2006/08/21)
*       �����裲��ɽ��
*    (2006/11/27)
*       ���롼�פ��ɲ� <watanabe-k>
***************************/

/*
 * ����
 * �����ա�������BɼNo.��������ô���ԡ��������ơ�
 * ��2006/02/01��      ��������watanabe-k������ô���Ԥ�ɽ�����Ƥ��뤬�����ô���Ԥ�ɽ������褦�˽���
 * ��2007/02/20��      ��������watanabe-k�����׵�ǽ�κ��
 * ��2007/03/16������¾78������watanabe-k�����̤κ�����������Ƥ�ά�Τα��˰�ư�������ѹ���󥯤�̤���������ɽ��
 *   2007/03/21                kajioka-h   �϶�Υ��쥯�ȥܥå�����Select_Get��Ȥ��褦���ѹ�
 *   2007/03/22                watanabe-k  ���롼�פǤΥ����Ȥ��ɲ�
 *  2007-04-10                  fukuda      ����������������ɲ�
 *   2007-05-05                watanbae-k  ���󭡤��ʤ��Ƥ��ѹ���󥯤�ɽ������褦�˽��� 
 *   2007-05-09                watanbae-k  ������ɽ�� 
 *   2007-05-09                watanbae-k�������Υ����Ƚ���ѹ� 
 *   2007-05-11                watanbae-k  ���ô���Ԥ�ô���ԥ����ɤ�ɽ������褦�˽���
 *   2007-05-21                watanbae-k  ���ʸ������ǽ�ˤ��롣
 *   2007-07-30                watanbae-k  ��٥���Ͻ������ɲ�
 *   2007-08-09                watanbae-k  �����ͥ����ɽ��Ͻ������ɲ�
 *   2007-11-17                watanbae-k  ����åפβ��̤���¿��ɽ�������Х�����
 *   2010-05-01  Rev.1.5����   hashimoto-y �����ΰ���ե���ȥ������ѹ���ǽ���ɲ�
 *
 */

$page_title = "������ޥ���";

// �Ķ�����ե�����
require_once("ENV_local.php");
require_once(INCLUDE_DIR."common_quickform.inc");

// HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"], null, "onSubmit=return confirm(true)");

// DB����³
$db_con = Db_Connect();

// ���¥����å�
$auth   = Auth_Check($db_con);


/****************************/
// �������������Ϣ
/****************************/
// �����ե�������������
$ary_form_list = array(
    "form_output_type"  => "1",
    "form_display_num"  => "1",
    "form_client_gr"    => "",
    "form_parents_div"  => "3",
    "form_client"       => array("cd1" => "", "cd2" => ""),
    "form_area_id"      => "",
    "form_tel"          => "",
    "form_c_staff"      => "",
    "form_btype"        => "",
    "form_state_type"   => "1",
    "form_trade"        => "",
);

// �����������
Restore_Filter2($form, "contract", "show_button", $ary_form_list);


/****************************/
// �����ѿ�����
/****************************/
$shop_id    = $_SESSION["client_id"];


/****************************/
// ���������
/****************************/
$form->setDefaults($ary_form_list);

$state_type     = "1";
$order_sql      = "ORDER BY \n";
$order_sql     .= "   t_client_gr.client_gr_cd, \n";
$order_sql     .= "   t_client_claim.client_cd, \n";
$order_sql     .= "   t_client.parents_flg DESC, \n";
$order_sql     .= "   t_client.client_cd1, \n";
$order_sql     .= "   t_client.client_cd2 \n";

$limit          = "100";    // LIMIT
$offset         = "0";      // OFFSET
$total_count    = "0";      // �����
$page_count     = ($_POST["f_page1"] != null) ? $_POST["f_page1"] : "1";    // ɽ���ڡ�����


/****************************/
// �ե�����ѡ������
/****************************/
// ���Ϸ���
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "����", "1");
$obj[]  =&  $form->createElement("radio", null, null, "CSV",  "2");
$form->addGroup($obj, "form_output_type", "");

// ɽ�����
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "100��ɽ��", "1");
$obj[]  =&  $form->createElement("radio", null, null, "����ɽ��",  "2");
$form->addGroup($obj, "form_display_num", "");

// ���롼��
$item   =   null;
$item   =   Select_Get($db_con, "client_gr");
$form->addElement("select", "form_client_gr", "", $item, $g_form_option_select);

// ���롼��̾
$form->addElement("text", "form_client_gr_name", "", "size=\"34\" maxLength=\"15\" $g_form_option");


// �ƻҶ�ʬ
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "��",   "t");
$obj[]  =&  $form->createElement("radio", null, null, "��",   "f");
$obj[]  =&  $form->createElement("radio", null, null, "��Ω", "1");
$obj[]  =&  $form->createElement("radio", null, null, "����", "3");
$form->addGroup($obj, "form_parents_div", "");

// �����襳����
Addelement_Client_64($form, "form_client", "�����襳����", "-");

// ������̾��ά��
$form->addElement("text", "form_client_name", "", "size=\"34\" maxLength=\"15\" $g_form_option");

// �϶�
$item   =   null;
$item   =   Select_Get($db_con, "area");
$form->addElement("select", "form_area_id", "", $item);

// TEL
$form->addElement("text", "form_tel", "", "size=\"15\" maxLength=\"13\" style=\"$g_form_style\" $g_form_option");

// ���ô����
$item   =   null;
$item   =   Select_Get($db_con, "staff");
$form->addElement("select", "form_c_staff", "", $item, $g_form_option_select);

// �ȼ�
$sql  = "SELECT \n";
$sql .= "   t_lbtype.lbtype_cd, \n";
$sql .= "   t_lbtype.lbtype_name, \n";
$sql .= "   t_sbtype.sbtype_id, \n";
$sql .= "   t_sbtype.sbtype_cd, \n";
$sql .= "   t_sbtype.sbtype_name \n";
$sql .= "FROM \n";
$sql .= "   t_lbtype \n";
$sql .= "   INNER JOIN t_sbtype ON t_lbtype.lbtype_id = t_sbtype.lbtype_id \n";
$sql .= "ORDER BY \n";
$sql .= "   t_lbtype.lbtype_cd, \n";
$sql .= "   t_sbtype.sbtype_cd \n";
$sql .= ";";
$res  = Db_Query($db_con, $sql);
while ($data_list = pg_fetch_array($res)){
    $max_len = ($max_len < mb_strwidth($data_list[1])) ? mb_strwidth($data_list[1]) : $max_len;
}
$result = Db_Query($db_con, $sql);
$item       = null;
$item[null] = null;
while ($data_list = pg_fetch_array($result)){
    for($i = 0; $i< $max_len; $i++){
        $data_list[1] = (mb_strwidth($data_list[1]) <= $max_len && $i != 0) ? $data_list[1]."��" : $data_list[1];
    }
    $item[$data_list[2]] = $data_list[0]." �� ".$data_list[1]."���� ".$data_list[3]." �� ".$data_list[4];
}
$form->addElement("select", "form_btype", "", $item, $g_form_option_select);

// ����
$obj    =   null;
$obj[]  =&  $form->createElement("radio", null, null, "�����", "1");
$obj[]  =&  $form->createElement("radio", null, null, "���󡦵ٻ���", "2");
//$obj[]  =&  $form->createElement("radio", null, null, "����",   "3");
$obj[]  =&  $form->createElement("radio", null, null, "����",   "4");
$form->addGroup($obj, "form_state_type", "");

// �����ȥ��
$ary_sort_item = array(
    "sl_group"          => "���롼��",
    "sl_client_cd"      => "�����襳����",
    "sl_client_name"    => "������̾",
    "sl_area"           => "�϶�",
    "sl_staff_cd"       => "ô��������",
    "sl_staff_name"     => "���ô��",
    "sl_act_client_cd"  => "����襳����",
    "sl_act_client_name"=> "�����̾",
);
AddElement_Sort_Link($form, $ary_sort_item, "sl_group");

// ɽ���ܥ���
$form->addElement("submit", "show_button", "ɽ����");

// ���ꥢ�ܥ���
$form->addElement("button", "clear_button", "���ꥢ", "onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

// ��Ͽ���̥�󥯥ܥ���
$form->addElement("button", "new_button", "��Ͽ����", "onClick=\"javascript:location.href='2-1-103.php'\"");

// �ѹ���������󥯥ܥ���
$form->addElement("button", "change_button", "�ѹ�������", "$g_button_color onClick=\"location.href='".$_SERVER["PHP_SELF"]."'\"");

// �����ʬ
$select_value = Select_Get($db_con, "trade_aord");
$form->addElement("select", 'form_trade', "", $select_value, "onKeyDown=\"chgKeycode();\" onChange =\"window.focus();\"");

//��٥����
$form->addElement("checkbox", "label_check_all", "", "��٥����", "onClick=\"javascript:All_Label_Check('label_check_all');\"");
#2010-04-27 hashimoto-y
#$form->addElement("submit","form_label_button","��٥����","onClick=\"javascript:Post_book_vote('./2-1-106.php','#');\"");
#2010-05-11 hashimoto-y
#$form->addElement("button","form_label_button","��٥����","onClick=\"javascript:Post_book_vote3('./2-1-106.php?label_font=s','#');\"");
$form->addElement("button","form_label_button","��٥����","onClick=\"javascript:Post_book_vote3('./2-1-106.php','#');\"");


#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#//��٥����2 �ե���ȥ�������
#$form->addElement("checkbox", "label_check_all2", "", "��٥����(��)", "onClick=\"javascript:All_Label_Check2('label_check_all2');\"");
#$form->addElement("button","form_label_button2","��٥����(��)","onClick=\"javascript:Post_book_vote3('./2-1-106.php?label_font=b','#');\"");

//�����ͥ�����
$form->addElement("checkbox", "card_check_all", "", "�����ͥ�����", "onClick=\"javascript:All_Card_Check('card_check_all');\"");
#2010-04-27 hashimoto-y
#$form->addElement("submit","form_card_button","�����ͥ�����","onClick=\"javascript:Post_book_vote('./2-1-102.php','#');\"");
$form->addElement("button","form_card_button","�����ͥ�����","onClick=\"javascript:Post_book_vote3('./2-1-102.php','#');\"");

/****************************/
// ����������ʥإå���ɽ���ѡ�
/****************************/
$sql  = "SELECT \n";
$sql .= "   COUNT(client_id) \n";
$sql .= "FROM \n";
$sql .= "   t_client \n";
$sql .= "WHERE \n";
if ($_SESSION["group_kind"] == "2"){
$sql .= "   t_client.shop_id IN (".Rank_Sql().") \n";
}else{
$sql .= "   t_client.shop_id = $shop_id \n";
}
$sql .= "AND \n";
$sql .= "   t_client.client_div = '1' \n";
$sql .= ";";

// �إå���ɽ�������������
$res  = Db_Query($db_con, $sql);
$all_count = pg_fetch_result($res, 0, 0);


/****************************/
// ɽ���ܥ��󲡲�����
/****************************/
# �ʤ�

#2010-04-06 hashimoto-y
if($_POST["show_button"]=="ɽ����" || $_POST != null){



/****************************/
// POST��
/****************************/
if ($_POST != null){

    // �ե�������ͤ��ѿ��˥��å�
    // ����������������˻���
    $output_type    = $_POST["form_output_type"];
    $display_num    = $_POST["form_display_num"];
    $client_gr      = $_POST["form_client_gr"];
    $parents_div    = $_POST["form_parents_div"];
    $client_cd1     = $_POST["form_client"]["cd1"];
    $client_cd2     = $_POST["form_client"]["cd2"];
    $client_name    = $_POST["form_client_name"];
    $client_gr_name = $_POST["form_client_gr_name"];
    $area_id        = $_POST["form_area_id"];
    $tel            = $_POST["form_tel"];
    $c_staff        = $_POST["form_c_staff"];
    $btype          = $_POST["form_btype"];
    $state_type     = ($_POST["form_state_type"] != null) ? $_POST["form_state_type"] : $state_type;

    $trade          = $_POST["form_trade"];

    // csv���Ϥ���ե饰
    $csv_flg = ($_POST["show_button"] != null && $output_type == "2") ? true : false;

    $sort_col = $_POST["hdn_sort_col"];

    $post_flg = true;

}else{

    $sort_col = "sl_group";

    $post_flg = true;

}


/****************************/
// �����ǡ�������������
/****************************/
if ($post_flg == true){

    /* ������� */
    $sql = null;

    // ���롼��
    $sql .= ($client_gr != null) ? "AND t_client.client_gr_id = $client_gr \n" : null;
    // �ƻҶ�ʬ
    if ($parents_div == "1"){
        $sql .= "AND t_client.parents_flg IS NULL \n";
    }else
    if ($parents_div == "t" || $parents_div == "f"){
        $sql .= "AND t_client.parents_flg = '$parents_div' \n";
    }
    // �����襳����1
    $sql .= ($client_cd1 != null) ? "AND t_client.client_cd1 LIKE '$client_cd1%' \n" : null;
    // �����襳����2
    $sql .= ($client_cd2 != null) ? "AND t_client.client_cd2 LIKE '$client_cd2%' \n" : null;
    // ������̾��ά��
    if ($client_name != null){
        $sql .= "AND \n";
        $sql .= "   ( \n";
        $sql .= "       t_client.client_name  LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_name2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_cname LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_read LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_read2 LIKE '%$client_name%' \n";
        $sql .= "       OR \n";
        $sql .= "       t_client.client_cread LIKE '%$client_name%' \n";
        $sql .= "   ) \n";
    }
    // ������̾��ά��
    if ($client_gr_name != null){
        $sql .= "AND t_client_gr.client_gr_name LIKE '%$client_gr_name%' \n";
    }

    // �϶�
    $sql .= ($area_id != null) ? "AND t_area.area_id = $area_id \n" : null;
    // TEL
    $sql .= ($tel != null) ? "AND t_client.tel LIKE '$tel%' \n" : null;
    // ���ô����
    $sql .= ($c_staff != null) ? "AND t_staff.staff_id = $c_staff \n" : null;
    // �ȼ�
    $sql .= ($btype != null) ? "AND t_sbtype.sbtype_id = $btype \n" : null;

    // �����ʬ
    $sql .= ($trade != null) ? "AND t_client.trade_id = '$trade' \n" : null;

    // �ѿ��ͤ��ؤ�
    $where_sql = $sql;

    /* �����Ⱦ�� */
    $sql = null;

    $sql .= "ORDER BY \n";

    switch ($sort_col){
        // ���롼��
        case "sl_group":
            $sql .= "   t_client_gr.client_gr_cd, \n";
            break;  
        // �����襳����
        case "sl_client_cd":
            break;  
        // ������̾
        case "sl_client_name":
            $sql .= "   t_client.client_name, \n";
            break;  
        // �϶� 
        case "sl_area":
            $sql .= "   t_area.area_cd, \n";
            break;  
        // ô��������
        case "sl_staff_cd":
            $sql .= "   t_staff.charge_cd, \n";
            break;  
        // ���ô��
        case "sl_staff_name":
            $sql .= "   t_staff.staff_name, \n";
            break;  
        // ����襳����
        case "sl_act_client_cd":
            $sql .= "   t_contract_client.client_cd1, \n";
            $sql .= "   t_contract_client.client_cd2, \n";
            break;  
        // �����̾
        case "sl_act_client_name":
            $sql .= "   t_contract_client.shop_name, \n";
            break; 
    }
    $sql .= "   t_client.client_cd1, \n";
    $sql .= "   t_client.client_cd2 \n";

    // �ѿ��ͤ��ؤ�
    $order_sql = $sql;

}


/****************************/
// �����ǡ�������
/****************************/
$sql  = "SELECT \n";
$sql .= "   t_client.client_id, \n";                        //  0 ������ID
$sql .= "   t_client.client_cd1, \n";                       //  1 �����襳���ɣ�
$sql .= "   t_client.client_cd2, \n";                       //  2 �����襳���ɣ�
$sql .= "   t_client.client_name, \n";                      //  3 ������̾
$sql .= "   t_client.client_cname, \n";                     //  4 ������̾��ά�Ρ�
$sql .= "   t_area.area_name, \n";                          //  5 �϶�̾
$sql .= "   t_client.tel, \n";                              //  6 TEL
$sql .= "   t_client.state, \n";                            //  7 ����
$sql .= "   t_staff.staff_name, \n";                        //  8 ���ô����
$sql .= "   t_staff.staff_cd1, \n";                         //  9 ���ô���ԥ����ɣ�
$sql .= "   t_staff.staff_cd2, \n";                         // 10 ���ô���ԥ����ɣ�
$sql .= "   lpad(t_staff.charge_cd, 4, 0) AS charge_cd, \n";// 11 ô���ԥ�����
$sql .= "   t_client_claim.client_name \n";
$sql .= "   AS claim_name1, \n";                            // 12 �����裱̾
$sql .= "   t_sbtype.sbtype_id, \n";                        // 13 �ȼ�ID
$sql .= "   t_client_claim2.client_name \n";
$sql .= "   AS claim_name2, \n";                            // 14 �����裲̾
$sql .= "   t_client.client_name2, \n";                     // 15 ����̾��
$sql .= "   t_client.address1, \n";                         // 16 ���꣱
$sql .= "   t_client.address2, \n";                         // 17 ���ꣲ
$sql .= "   t_client.address3, \n";                         // 18 ���ꣳ
$sql .= "   t_client_claim.client_cd \n";
$sql .= "   AS claim_cd1, \n";                              // 19 �����裱������
$sql .= "   t_client_claim2.client_cd \n";
$sql .= "   AS claim_cd2, \n";                              // 20 �����裲������
$sql .= "   t_client_gr.client_gr_cd, \n";                  // 21 ���롼�ץ�����
$sql .= "   t_client_gr.client_gr_name, \n";                // 22 ���롼��̾
$sql .= "   CASE t_client.parents_flg \n";
$sql .= "       WHEN 't' THEN '��'\n";
$sql .= "       WHEN 'f' THEN '��'\n";
$sql .= "       ELSE '��Ω'\n";
$sql .= "   END \n";
$sql .= "   AS parents_flg, \n";                            // 23 �ƻҥե饰
$sql .= "   CASE \n";
$sql .= "       WHEN t_contract.client_id IS NOT NULL \n";
$sql .= "       THEN 't' \n";
$sql .= "       ELSE 'f' \n";
$sql .= "   END \n";
$sql .= "   AS contract_flg";                               // 24 
// csv���ϻ�
if ($csv_flg == true){
$sql .= ", \n";
$sql .= "   t_intro_ac.client_cd1, \n";                     // 25 �Ȳ�����襳����
$sql .= "   t_intro_ac.client_name, \n";                    // 26 �Ҳ����̾
$sql .= "   t_client.intro_ac_name, \n";                    // 27 ���������̾
$sql .= "   t_client.intro_bank, \n";                       // 28 ���/��Ź̾
$sql .= "   t_client.intro_ac_num, \n";                     // 29 �����ֹ�
$sql .= "   t_lbtype.lbtype_name || ' ' ||t_sbtype.sbtype_name, \n";// 30 �ȼ�̾
$sql .= "   t_inst.inst_name, \n";                          // 31 ����̾
$sql .= "   t_bstruct.bstruct_name, \n";                    // 32 ����̾
$sql .= "   t_client.client_read, \n";                      // 33 ������̾���ʥեꥬ�ʡ�
$sql .= "   t_client.client_read2, \n";                     // 34 ������̾���ʥեꥬ�ʡ�
$sql .= "   t_client.client_cread, \n";                     // 35 ά�Ρʥեꥬ�ʡ�
$sql .= "   t_client.post_no1, \n";                         // 36 ͹���ֹ棱
$sql .= "   t_client.post_no2, \n";                         // 37 ͹���ֹ棲
$sql .= "   t_client.address_read, \n";                     // 38 ����ʥեꥬ�ʡ�
$sql .= "   t_client.fax, \n";                              // 39 FAX
$sql .= "   t_client.establish_day, \n";                    // 40 �϶���
$sql .= "   t_client.email, \n";                            // 41 ô����EMAIL
$sql .= "   t_client.rep_name, \n";                         // 42 ��ɽ�Ի�̾
$sql .= "   t_client.represe, \n";                          // 43 ��ɽ����
$sql .= "   CASE t_client.compellation \n";
$sql .= "       WHEN '1' THEN '����'\n";
$sql .= "       ELSE '��'\n";
$sql .= "   END \n";
$sql .= "   AS compellation, \n";                           // 44 �ɾ�
$sql .= "   t_client.company_name, \n";                     // 45 �Ʋ��̾
$sql .= "   t_client.company_tel, \n";                      // 46 �Ʋ��TEL
$sql .= "   t_client.company_address, \n";                  // 47 �Ʋ�ҽ���
$sql .= "   t_client.capital, \n";                          // 48 ���ܶ�
$sql .= "   t_client.parent_establish_day, \n";             // 49 �Ʋ���϶���
$sql .= "   t_client.parent_rep_name, \n";                  // 50 �Ʋ����ɽ�Ի�̾
$sql .= "   t_client.url, \n";                              // 51 URL
$sql .= "   t_client.charger_part1, \n";                    // 52 ô������
$sql .= "   t_client.charger_part2, \n";                    // 53 ô������
$sql .= "   t_client.charger_part3, \n";                    // 54 ô������
$sql .= "   t_client.charger_represe1, \n";                 // 55 ô���򿦣�
$sql .= "   t_client.charger_represe2, \n";                 // 56 ô���򿦣�
$sql .= "   t_client.charger_represe3, \n";                 // 57 ô���򿦣�
$sql .= "   t_client.charger1, \n";                         // 58 ô���Ի�̾��
$sql .= "   t_client.charger2, \n";                         // 59 ô���Ի�̾��
$sql .= "   t_client.charger3, \n";                         // 60 ô���Ի�̾��
$sql .= "   t_client.charger_note, \n";                     // 61 ô��������
$sql .= "   t_client.trade_stime1, \n";                     // 62 �ĶȻ��֡ʸ������ϡ�
$sql .= "   t_client.trade_etime1, \n";                     // 63 �ĶȻ��֡ʸ�����λ��
$sql .= "   t_client.trade_stime2, \n";                     // 64 �ĶȻ��֡ʸ�峫�ϡ�
$sql .= "   t_client.trade_etime2, \n";                     // 65 �ĶȻ��֡ʸ�彪λ��
$sql .= "   t_client.holiday, \n";                          // 66 ����
$sql .= "   t_client.credit_limit, \n";                     // 67 Ϳ������
$sql .= "   t_client.col_terms, \n";                        // 68 ������
$sql .= "   t_trade.trade_name, \n";                        // 69 �����ʬ
$sql .= "   CASE t_client.close_day \n";
$sql .= "       WHEN '29' THEN '����' \n";
$sql .= "       ELSE t_client.close_day || '��'\n";
$sql .= "   END AS close_day, \n";                          // 70 ����
$sql .= "   CASE t_client.pay_m \n";
$sql .= "       WHEN '0' THEN '����' \n";
$sql .= "       WHEN '1' THEN '���' \n";
$sql .= "       ELSE t_client.pay_m || '�����' \n";
$sql .= "   END AS pay_m, \n";                              // 71 �������ʷ��
$sql .= "   CASE t_client.pay_d \n";
$sql .= "       WHEN '29' THEN '����' \n";
$sql .= "       ELSE t_client.pay_d || '��' \n";
$sql .= "   END AS pay_d, \n";                              // 72 ������������
$sql .= "   CASE t_client.pay_way \n";
$sql .= "       WHEN '1' THEN '��ư����' \n";
$sql .= "       WHEN '2' THEN '����' \n";
$sql .= "       WHEN '3' THEN 'ˬ�佸��' \n";
$sql .= "       WHEN '4' THEN '���' \n";
$sql .= "       WHEN '5' THEN '����¾' \n";
$sql .= "   END AS pay_way, \n";                            // 73 ������ˡ
$sql .= "   CASE \n";
$sql .= "       WHEN t_client.account_id IS NOT NULL \n";
$sql .= "       THEN t_bank.bank_name || '��' || t_b_bank.b_bank_name || '��' || 
                CASE t_account.deposit_kind  WHEN '1' THEN '���� ' 
                    WHEN '2' THEN '���� ' END || t_account.account_no";
$sql .= "       ELSE '' \n";
$sql .= "   END \n";
$sql .= "   AS pay_bank, \n";                               // 74 ������Ը���
$sql .= "   t_client.account_name, \n";                     // 75 ����̾����
$sql .= "   t_client.pay_name, \n";                         // 76 ����̾����
$sql .= "   CASE t_client.bank_div ";
$sql .= "       WHEN '1' THEN '��������ô' \n";
$sql .= "       WHEN '2' THEN '������ô' \n";
$sql .= "   END AS bank_div, \n";                           // 77 ��Լ������ô��ʬ
$sql .= "   t_client.cont_sday, \n";                        // 78 ����ǯ����
$sql .= "   t_client.cont_rday, \n";                        // 79 ���󹹿���
$sql .= "   t_client.cont_eday, \n";                        // 80 ����λ��
$sql .= "   t_client.cont_peri, \n";                        // 81 �������
$sql .= "   CASE t_client.slip_out \n";
$sql .= "       WHEN '1' THEN 'ͭ' \n";
$sql .= "       WHEN '2' THEN '����' \n";
$sql .= "       WHEN '3' THEN '̵' \n";
$sql .= "   END AS slip_out, \n";                           // 82 ��ɼȯ��
$sql .= "   t_slip_sheet.s_pattern_name, \n";               // 83 �����ɼȯ�Ը�
$sql .= "   CASE t_client.deliver_effect \n";
$sql .= "       WHEN '1' THEN '������̵��' \n";
$sql .= "       WHEN '2' THEN '���̥�����ͭ��' \n";
$sql .= "       ELSE '���Υ�����ͭ��' \n";
$sql .= "   END AS deliver_effect, \n";                     // 84 �����ɼ�����ȸ���
$sql .= "   t_client.deliver_note, \n";                     // 85 �����ɼ������
$sql .= "   CASE t_client.claim_out \n";    
$sql .= "       WHEN '1' THEN '���������' \n"; 
$sql .= "       WHEN '2' THEN '��������' \n"; 
$sql .= "       WHEN '5' THEN '�������������' \n"; 
$sql .= "       WHEN '3' THEN '���Ϥ��ʤ�' \n"; 
$sql .= "       WHEN '4' THEN '����' \n"; 
$sql .= "       ELSE '����' \n";    
$sql .= "   END AS claim_out, \n";                          // 86 �����ȯ��
$sql .= "   CASE t_client.claim_send \n";   
$sql .= "       WHEN '1' THEN '͹��' \n";   
$sql .= "       WHEN '2' THEN '�᡼��' \n"; 
$sql .= "       WHEN '4' THEN 'WEB' \n";    
$sql .= "       ELSE '͹�����᡼��' \n";    
$sql .= "   END AS claim_send, \n";                         // 87 ���������
$sql .= "   t_claim_sheet.c_pattern_name, \n";              // 88 �����ȯ�Ը�
$sql .= "   t_client.claim_note, \n";                       // 89 ���������
$sql .= "   CASE t_client.coax \n"; 
$sql .= "       WHEN '1' THEN '�ڼ�' \n";   
$sql .= "       WHEN '2' THEN '�ͼθ���' \n";   
$sql .= "       ELSE '�ھ�' \n";    
$sql .= "   END AS claim_note, \n";                         // 90 ��۴ݤ��ʬ
$sql .= "   CASE t_client.tax_div \n";  
$sql .= "       WHEN '2' THEN '��ɼñ��' \n";   
$sql .= "       ELSE '����ñ��' \n";    
$sql .= "   END AS tax_div, \n";                            // 91 �����ǡ�����ñ��
$sql .= "   CASE t_client.tax_franct \n";   
$sql .= "       WHEN '1' THEN '�ڼ�' \n";   
$sql .= "       WHEN '2' THEN '�ͼθ���' \n";   
$sql .= "       ELSE '�ھ�' \n";    
$sql .= "   END AS tax_franct, \n";                         // 92 �����ǡ�ü����ʬ
$sql .= "   CASE t_client.c_tax_div \n";    
$sql .= "       WHEN '1' THEN '����' \n";   
$sql .= "       ELSE '����' \n";    
$sql .= "   END AS tax_div, \n";                            // 93 �����ǡ����Ƕ�ʬ
$sql .= "   t_client.note, \n";                             // 94 ����������������¾
$sql .= "   t_branch.branch_name, \n";                      // 95 ô����Ź
$sql .= "   t_staff1.staff_name, \n";                       // 96 ������ô��
$sql .= "   t_staff2.staff_name, \n";                       // 97 �������Ұ�
$sql .= "   t_client.round_day, \n";                        // 98 ��󳫻���
$sql .= "   t_client.deal_history, \n";                     // 99 �������
$sql .= "   t_client.importance, \n";                       // 100 ���׻���
$sql .= "   t_intro_ac.client_cd2,";                        // 101 �Ȳ���¥����ɣ�
$sql .= "   t_intro_ac.client_div, ";                       // 102 �Ȳ���¤μ����ʬ

$sql .= "   CASE t_client_claim.month1_flg \n";             // 103 1����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month2_flg \n";             // 104 2����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month3_flg \n";             // 105 3����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month4_flg \n";             // 106 4����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month5_flg \n";             // 107 5����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month6_flg \n";             // 108 6����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month7_flg \n";             // 109 7����������    
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month8_flg \n";             // 110 8����������    
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month9_flg \n";             // 111 9����������    
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month10_flg \n";            // 112 10����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month11_flg \n";            // 113 11����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END , ";
$sql .= "   CASE t_client_claim.month12_flg \n";            // 114 12����������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '��' \n";
$sql .= "   END  ";

}

$sql .= " ,\n";
$sql .= "   CASE \n";
$sql .= "       WHEN t_contract.client_id IS NOT NULL AND t_cont_state.count IS NULL THEN '�ٻ���' \n";
$sql .= "       WHEN t_contract.client_id IS NOT NULL AND t_cont_state.count > 0 THEN '�ѹ�'  \n";
$sql .= "       ELSE ''";
$sql .= "   END AS link, ";                 // 115
$sql .= "   t_contract_client.client_cd1 ||'-'|| t_contract_client.client_cd2 AS trust_cd, ";            // 116 ����襳����
$sql .= "   t_contract_client.shop_name AS trust_name, ";    // 117 �����̾

#2010-05-01 hashimoto-y
$sql .= "   CASE t_client.bill_address_font \n";            // 118 �������
$sql .= "       WHEN 't' THEN '��' \n";
$sql .= "       WHEN 'f' THEN '' \n";
$sql .= "   END  ";

$sql .= "FROM \n";
$sql .= "   t_client \n";

//����ޥ����ȷ��
$sql .= "       LEFT JOIN \n";
$sql .= "   ( SELECT ";
$sql .= "       t_contract.client_id,";
$sql .= "       contract_id ";
$sql .= "   FROM";
$sql .= "       (SELECT "; 
$sql .= "           MIN(line) AS line, ";
$sql .= "           t_contract.client_id ";
$sql .= "       FROM  \n";
$sql .= "           t_contract";
$sql .= "       GROUP BY ";
$sql .= "           client_id ";
$sql .= "       ) AS t_cont_line";
$sql .= "           INNER JOIN ";
$sql .= "       t_contract ";
$sql .= "       ON t_cont_line.line = t_contract.line ";
$sql .= "       AND t_cont_line.client_id = t_contract.client_id ";
$sql .= "   ) AS t_contract ";
$sql .= "   ON t_client.client_id = t_contract.client_id \n";

$sql .= "       LEFT JOIN \n";
$sql .= "   ( SELECT ";
$sql .= "       COUNT(client_id) AS count,";
$sql .= "       client_id ";
$sql .= "   FROM ";
$sql .= "       t_contract ";
$sql .= "   WHERE ";
$sql .= "       state = '1' ";
$sql .= "   GROUP BY client_id ";
$sql .= "   ) AS t_cont_state ";
$sql .= "   ON t_client.client_id = t_cont_state.client_id ";

//��Ԥη���ȷ��
$sql .= "       LEFT JOIN ";
$sql .= "   ( SELECT ";
$sql .= "       t_contract.client_id, ";
$sql .= "       trust_id ";
$sql .= "   FROM ";
$sql .= "       (SELECT ";
$sql .= "           MIN(line) AS line, ";
$sql .= "           t_contract.client_id ";
$sql .= "       FROM ";
$sql .= "           t_contract ";
$sql .= "       WHERE ";
$sql .= "           t_contract.contract_div != '1'";
$sql .= "       GROUP BY client_id ";
$sql .= "       ) AS t_cont_line";
$sql .= "           INNER JOIN ";
$sql .= "       t_contract ";
$sql .= "       ON t_cont_line.line = t_contract.line ";
$sql .= "       AND t_cont_line.client_id = t_contract.client_id ";
$sql .= "   ) AS t_con_act ";
$sql .= "   ON t_client.client_id = t_con_act.client_id ";

$sql .= "       LEFT JOIN ";
$sql .= "   t_client AS t_contract_client ";
$sql .= "   ON t_con_act.trust_id = t_contract_client.client_id ";

$sql .= "   LEFT JOIN t_con_staff \n";
$sql .= "   ON t_contract.contract_id = t_con_staff.contract_id \n";
$sql .= "   AND t_con_staff.staff_div = '0' \n";
$sql .= "   LEFT JOIN t_staff \n";
$sql .= "   ON t_con_staff.staff_id = t_staff.staff_id \n";
$sql .= "   INNER JOIN t_area \n";
$sql .= "   ON t_client.area_id = t_area.area_id \n";
$sql .= "   LEFT JOIN t_sbtype \n";
$sql .= "   ON t_client.sbtype_id = t_sbtype.sbtype_id \n";
$sql .= "   LEFT JOIN t_lbtype \n";
$sql .= "   ON t_lbtype.lbtype_id = t_sbtype.lbtype_id \n";
$sql .= "   LEFT JOIN t_client_gr \n";
$sql .= "   ON t_client.client_gr_id = t_client_gr.client_gr_id \n";
$sql .= "   INNER JOIN \n";
$sql .= "   ( \n";
$sql .= "       SELECT \n";
$sql .= "           t_claim.*, \n";
$sql .= "           t_client.client_cd1 || '-' || t_client.client_cd2 AS client_cd, \n";
$sql .= "           t_client.client_name \n";
$sql .= "       FROM \n";
$sql .= "           t_claim \n";
$sql .= "           INNER JOIN t_client \n";
$sql .= "           ON t_client.client_id = t_claim.claim_id \n";
$sql .= "           AND t_claim.claim_div = '1' \n";
$sql .= "   ) \n";
$sql .= "   AS t_client_claim \n";
$sql .= "   ON t_client.client_id = t_client_claim.client_id \n";
$sql .= "   LEFT JOIN \n";
$sql .= "   ( \n";
$sql .= "       SELECT \n";
$sql .= "           t_claim.client_id, \n";
$sql .= "           t_client.client_cd1 || '-' || t_client.client_cd2 AS client_cd, \n";
$sql .= "           t_client.client_name \n";
$sql .= "       FROM \n";
$sql .= "           t_claim \n";
$sql .= "           INNER JOIN t_client \n";
$sql .= "           ON t_client.client_id = t_claim.claim_id \n";
$sql .= "           AND t_claim.claim_div = '2' \n";
$sql .= "   ) \n";
$sql .= "   AS t_client_claim2 \n";
$sql .= "   ON t_client.client_id = t_client_claim2.client_id \n";
// csv���ϻ�
if ($csv_flg == true){
$sql .= "   INNER JOIN t_client_info \n";
$sql .= "   ON t_client.client_id = t_client_info.client_id \n";
$sql .= "   LEFT JOIN t_client AS t_intro_ac \n";
$sql .= "   ON t_client_info.intro_account_id = t_intro_ac.client_id \n";
$sql .= "   LEFT JOIN t_inst \n";
$sql .= "   ON t_client.inst_id = t_inst.inst_id \n";
$sql .= "   LEFT JOIN t_bstruct \n";
$sql .= "   ON t_bstruct.bstruct_id = t_client.b_struct \n";
$sql .= "   LEFT JOIN t_trade \n";
$sql .= "   ON t_trade.trade_id = t_client.trade_id \n";
$sql .= "   LEFT JOIN t_account \n";
$sql .= "   ON t_account.account_id = t_client.account_id \n";
$sql .= "   LEFT JOIN t_b_bank \n";
$sql .= "   ON t_b_bank.b_bank_id = t_account.b_bank_id \n";
$sql .= "   LEFT JOIN t_bank \n";
$sql .= "   ON t_bank.bank_id = t_b_bank.bank_id \n";
$sql .= "   LEFT JOIN t_slip_sheet \n";
$sql .= "   ON t_client.s_pattern_id = t_slip_sheet.s_pattern_id \n";
$sql .= "   LEFT JOIN t_claim_sheet \n";
$sql .= "   ON t_client.c_pattern_id = t_claim_sheet.c_pattern_id \n";
$sql .= "   LEFT JOIN t_branch \n";
$sql .= "   ON t_client.charge_branch_id = t_branch.branch_id \n";
$sql .= "   LEFT JOIN t_staff AS t_staff1 \n";
$sql .= "   ON t_staff1.staff_id = t_client.c_staff_id1 \n";
$sql .= "   LEFT JOIN t_staff AS t_staff2 \n";
$sql .= "   ON t_staff2.staff_id = t_client.c_staff_id2 \n";


}
$sql .= "WHERE \n";
if ($_SESSION["group_kind"] == "2"){
$sql .= "   t_client.shop_id IN (".Rank_Sql().") \n";
}else{
$sql .= "   t_client.shop_id = $shop_id \n";
}
$sql .= "AND \n";
$sql .= "   t_client.client_div = '1' \n";
if ($state_type != "4"){
$sql .= "AND \n";
$sql .= "   t_client.state = '$state_type' \n";
}
$sql .= $where_sql;
$sql .= $order_sql;

// ���̽��ϻ�
if($csv_flg != true){

    // ���������
    $res            = Db_Query($db_con, $sql.";");
    $total_count    = pg_num_rows($res);

    // LIMIT, OFFSET������
    if ($post_flg == true){

        // ɽ�����
        switch ($display_num){
            case "1":
                $limit = "100";
                break;
            case "2":
                $limit = $total_count;
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
    $page_data      = Get_Data($res, 2, "ASSOC");
// csv���ϻ�
}elseif ($csv_flg == true){

    // �������
    $res            = Db_Query($db_con, $sql.";");
    $match_count    = pg_num_rows($res);
    $page_data      = Get_Data($res, $output_type);

    // CSV����
    $result = Db_Query($db_con, $sql);
        for($i = 0; $i < $match_count; $i++){
            $csv_page_data[$i][0] = ($page_data[$i][7] == "1") ? "�����" : "���󡦵ٻ���";//����
            $csv_page_data[$i][1] = $page_data[$i][22];                 //���롼��̾
            $csv_page_data[$i][2] = $page_data[$i][23];                 //�ƻҶ�ʬ
            $csv_page_data[$i][3] = $page_data[$i][5];                  //�϶�
            $csv_page_data[$i][4] = $page_data[$i][30];                 //�ȼ�
            $csv_page_data[$i][5] = $page_data[$i][31];                 //����
            $csv_page_data[$i][6] = $page_data[$i][32];                 //����
            $csv_page_data[$i][7] = $page_data[$i][1]."-".$page_data[$i][2];    //�����襳����
            $csv_page_data[$i][8] = $page_data[$i][3];                  //������̾��
            $csv_page_data[$i][9] = $page_data[$i][15];                 //������̾��
            $csv_page_data[$i][10] = $page_data[$i][33];                //������̾���ʥեꥬ�ʡ�
            $csv_page_data[$i][11] = $page_data[$i][34];                //������̾���ʥեꥬ�ʡ�
            $csv_page_data[$i][12] = $page_data[$i][4];                 //ά��
            $csv_page_data[$i][13] = $page_data[$i][35];                //ά�Ρʥեꥬ�ʡ�
            $csv_page_data[$i][14] = $page_data[$i][44];                //�ɾ�
            $csv_page_data[$i][15] = $page_data[$i][42];                //��ɽ�Ի�̾
            $csv_page_data[$i][16] = $page_data[$i][43];                //��ɽ����
            $csv_page_data[$i][17] = $page_data[$i][36]."-".$page_data[$i][37]; //͹���ֹ�
            $csv_page_data[$i][18] = $page_data[$i][16];                //���꣱
            $csv_page_data[$i][19] = $page_data[$i][17];                //���ꣲ
            $csv_page_data[$i][20] = $page_data[$i][18];                //���ꣳ
            $csv_page_data[$i][21] = $page_data[$i][38];                //���ꣲ�ʥեꥬ�ʡ�
            $csv_page_data[$i][22] = $page_data[$i][6];                 //TEL
            $csv_page_data[$i][23] = $page_data[$i][39];                //FAX
            $csv_page_data[$i][24] = $page_data[$i][40];                //�϶���
            $csv_page_data[$i][25] = $page_data[$i][41];                //ô����Email
            $csv_page_data[$i][26] = $page_data[$i][45];                //�Ʋ��̾
            $csv_page_data[$i][27] = $page_data[$i][46];                //�Ʋ��TEL
            $csv_page_data[$i][28] = $page_data[$i][47];                //�Ʋ�ҽ���
            $csv_page_data[$i][29] = $page_data[$i][48];                //���ܶ�
            $csv_page_data[$i][30] = $page_data[$i][49];                //�Ʋ���϶���
            $csv_page_data[$i][31] = $page_data[$i][50];                //�Ʋ����ɽ�Ի�̾
            $csv_page_data[$i][32] = $page_data[$i][51];                //URL
            $csv_page_data[$i][33] = $page_data[$i][52];                //ô��������
            $csv_page_data[$i][34] = $page_data[$i][55];                //ô������
            $csv_page_data[$i][35] = $page_data[$i][58];                //ô������̾
            $csv_page_data[$i][36] = $page_data[$i][53];                //ô��������
            $csv_page_data[$i][37] = $page_data[$i][56];                //ô������
            $csv_page_data[$i][38] = $page_data[$i][59];                //ô������̾
            $csv_page_data[$i][39] = $page_data[$i][54];                //ô��������
            $csv_page_data[$i][40] = $page_data[$i][57];                //ô������
            $csv_page_data[$i][41] = $page_data[$i][60];                //ô������̾
            $csv_page_data[$i][42] = $page_data[$i][61];                //ô����������
            $csv_page_data[$i][43] = ($page_data[$i][62]!= null || $page_data[$i][63]!=null)?$page_data[$i][62]."��".$page_data[$i][63]:""; //�ĶȻ��֡ʸ�����
            $csv_page_data[$i][44] = ($page_data[$i][64]!= null || $page_data[$i][65]!=null)?$page_data[$i][64]."��".$page_data[$i][65]:""; //�ĶȻ��֡ʸ���
            $csv_page_data[$i][45] = $page_data[$i][66];                //����
            $csv_page_data[$i][46] = $page_data[$i][19];                //�����裱������
            $csv_page_data[$i][47] = $page_data[$i][12];                //�����裱̾
            $csv_page_data[$i][48] = $page_data[$i][20];                //�����裲������
            $csv_page_data[$i][49] = $page_data[$i][14];                //�����裲̾
            $csv_page_data[$i][50] = $page_data[$i][67];                //Ϳ������
            $csv_page_data[$i][51] = $page_data[$i][68];                //������
            $csv_page_data[$i][52] = $page_data[$i][69];                //�����ʬ
            $csv_page_data[$i][53] = $page_data[$i][70];                //����
            $csv_page_data[$i][54] = $page_data[$i][71]."��".$page_data[$i][72];    //������
            $csv_page_data[$i][55] = $page_data[$i][73];                //������ˡ
            $csv_page_data[$i][56] = $page_data[$i][74];                //������Ը���
            $csv_page_data[$i][57] = $page_data[$i][76];                //����̾����
            $csv_page_data[$i][58] = $page_data[$i][75];                //����̾����
            $csv_page_data[$i][59] = $page_data[$i][77];                //��Լ������ô��ʬ
            $csv_page_data[$i][60] = $page_data[$i][78];                //����ǯ����
            $csv_page_data[$i][61] = $page_data[$i][79];                //���󹹿���
            $csv_page_data[$i][62] = $page_data[$i][81];                //�������
            $csv_page_data[$i][63] = $page_data[$i][80];                //����λ��
            $csv_page_data[$i][64] = $page_data[$i][82];                //�����ɼȯ��
            $csv_page_data[$i][65] = $page_data[$i][83];                //�����ɼȯ�Ը�
            $csv_page_data[$i][66] = $page_data[$i][84];                //�����ɼ�����ȸ���
            $csv_page_data[$i][67] = $page_data[$i][85];                //�����ɼ������
            $csv_page_data[$i][68] = $page_data[$i][86];                //�����ȯ��
            $csv_page_data[$i][69] = $page_data[$i][87];                //���������
            $csv_page_data[$i][70] = $page_data[$i][88];                //�����ȯ�Ը�
            $csv_page_data[$i][71] = $page_data[$i][89];                //���������
            $csv_page_data[$i][72] = $page_data[$i][90];                //��۴ݤ��ʬ
            $csv_page_data[$i][73] = $page_data[$i][91];                //�����ǡ�����ñ��
            $csv_page_data[$i][74] = $page_data[$i][92];                //�����ǡ�ü����ʬ
            $csv_page_data[$i][75] = $page_data[$i][93];                //�����ǡ����Ƕ�ʬ
            $csv_page_data[$i][76] = $page_data[$i][94];                //����������������¾
            if($page_data[$i][102] == "3"){
                $page_data[$i][25] = $page_data[$i][25]."-".$page_data[$i][101];
            }
            $csv_page_data[$i][77] = $page_data[$i][25];                //���Ҳ���¥�����
            $csv_page_data[$i][78] = $page_data[$i][26];                //���Ҳ����̾
            $csv_page_data[$i][79] = $page_data[$i][27];                //�����������̾
            $csv_page_data[$i][80] = $page_data[$i][28];                //���/��Ź̾
            $csv_page_data[$i][81] = $page_data[$i][29];                //�����ֹ�
            $csv_page_data[$i][82] = $page_data[$i][95];                //ô����Ź
            $csv_page_data[$i][83] = $page_data[$i][96];                //������ô��
            $csv_page_data[$i][84] = $page_data[$i][97];                //�������Ұ�
            $csv_page_data[$i][85] = $page_data[$i][98];                //��󳫻���
            $csv_page_data[$i][86] = $page_data[$i][99];                //�������
            $csv_page_data[$i][87] = $page_data[$i][100];               //���׻���
            $csv_page_data[$i][88] = $page_data[$i][8];                 //���ô��
            
            //ľ�Ĥξ��Τ�
            if($_SESSION[group_kind] == "2"){
                $csv_page_data[$i][89] = $page_data[$i][116];               //����襳����
                $csv_page_data[$i][90] = $page_data[$i][117];               //�����̾
            }

            //����������
            $csv_page_data[$i][91] = $page_data[$i][103];
            $csv_page_data[$i][92] = $page_data[$i][104];
            $csv_page_data[$i][93] = $page_data[$i][105];
            $csv_page_data[$i][94] = $page_data[$i][106];
            $csv_page_data[$i][95] = $page_data[$i][107];
            $csv_page_data[$i][96] = $page_data[$i][108];
            $csv_page_data[$i][97] = $page_data[$i][109];
            $csv_page_data[$i][98] = $page_data[$i][110];
            $csv_page_data[$i][99] = $page_data[$i][111];
            $csv_page_data[$i][100] = $page_data[$i][112];
            $csv_page_data[$i][101] = $page_data[$i][113];
            $csv_page_data[$i][102] = $page_data[$i][114];

            #2010-05-01 hashimoto-y
            #�������
            $csv_page_data[$i][103] = $page_data[$i][118];

        }

        $csv_file_name = "������ޥ���".date("Ymd").".csv";
        $csv_header = array(
         "����",
         "���롼��̾",
         "�ƻҶ�ʬ",
         "�϶�",
         "�ȼ�",
         "����",
         "����",
         "�����襳����",
         "������̾��",
         "������̾��",
         "������̾���ʥեꥬ�ʡ�",
         "������̾���ʥեꥬ�ʡ�",
         "ά��",
         "ά�Ρʥեꥬ�ʡ�",
         "�ɾ�",
         "��ɽ�Ի�̾",
         "��ɽ����",
         "͹���ֹ�",
         "���꣱",
         "���ꣲ",
         "���ꣳ",
         "���ꣲ�ʥեꥬ�ʡ�",
         "TEL",
         "FAX",
         "�϶���",
         "ô����Email",
         "�Ʋ��̾",
         "�Ʋ��TEL",
         "�Ʋ�ҽ���",
         "���ܶ�",
         "�Ʋ���϶���",
         "�Ʋ����ɽ�Ի�̾",
         "URL",
         "ô��������",
         "ô������",
         "ô������̾",
         "ô��������",
         "ô������",
         "ô������̾",
         "ô��������",
         "ô������",
         "ô������̾",
         "ô����������",
         "�ĶȻ��֡ʸ�����",
         "�ĶȻ��֡ʸ���",
         "����",
         "�����裱�ʥ����ɡ�",
         "�����裱��̾����",
         "�����裲�ʥ����ɡ�",
         "�����裲��̾����",
         "Ϳ������",
         "������",
         "�����ʬ",
         "����",
         "������",
         "������ˡ",
         "������Ը���",
         "����̾����",
         "����̾����",
         "��Լ������ô��ʬ",
         "����ǯ����",
         "���󹹿���",
         "�������",
         "����λ��",
         "�����ɼȯ��",
         "�����ɼȯ�Ը�",
         "�����ɼ�����ȸ���",
         "�����ɼ������",
         "�����ȯ��",
         "���������",
         "�����ȯ�Ը�",
         "���������",
         "��۴ݤ��ʬ",
         "�����ǡ�����ñ��",
         "�����ǡ�ü����ʬ",
         "�����ǡ����Ƕ�ʬ",
         "����������������¾",
         "���Ҳ���¥�����",
         "���Ҳ����̾",
         "�����������̾",
         "���/��Ź̾",
         "�����ֹ�",
         "ô����Ź",
         "������ô��",
         "�������Ұ�",
         "��󳫻���",
         "�������",
         "���׻���",
         "���ô��",
         "����襳����",
         "�����̾",
         "1������",
         "2������",
         "3������",
         "4������",
         "5������",
         "6������",
         "7������",
         "8������",
         "9������",
         "10������",
         "11������",
         "12������",
         "�������",
          );

        if($_SESSION[group_kind] != "2"){
            unset($csv_header[89]);
            unset($csv_header[90]);
        }

        $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
        $csv_data = Make_Csv($csv_page_data, $csv_header);
        Header("Content-disposition: attachment; filename=$csv_file_name");
        Header("Content-type: application/octet-stream; name=$csv_file_name");
        print $csv_data;
        exit;

}


/****************************/
// ɽ���ѥǡ�������
/****************************/
// ����ɽ��
if($state_type == "1"){
    $state_disp = "�����";
}else
if ($state_type == "2"){
    $state_disp = "���󡦵ٻ���";
}else
if ($state_type == "3"){
    $state_disp = "����";
}else
if ($state_type == "4"){
    $state_disp = "����";
}

// �����ǡ�����������
if (count($page_data) > 0){

    $i=0;
    // �����ǡ����ǥ롼��
    foreach ($page_data as $key => $value){

        // No.
        $page_data[$key]["no"]      = (($page_count - 1) * $limit) + $key + 1;

        // ���롼��
        $page_data[$key]["group"]   = $value["client_gr_cd"]."<br>".htmlspecialchars($value["client_gr_name"])."<br>";

        // �����襳���ɡ�������̾���
        $page_data[$key]["client"]  = $value["client_cd1"]."-".$value["client_cd2"]."<br>";
        $page_data[$key]["client"] .= "<a href=\"./2-1-103.php?client_id=".$value["client_id"]."\">";
        $page_data[$key]["client"] .= htmlspecialchars($value["client_name"]);
        $page_data[$key]["client"] .= "</a>";;

        // ���������ѹ����
        if ($value["contract_flg"] == "t"){
            $page_data[$key]["chg_lnk"] = "<a href=\"./2-1-115.php?client_id=".$value["client_id"]."\">".$value["link"]."</a>";
        }else{
            $page_data[$key]["chg_lnk"] = null;
        }

        // ô�������ɡ�ô����̾
        //$page_data[$key]["staff"]   = $value["staff_cd1"].$value["staff_cd2"]."<br>".$value["staff_name"]."<br>";
        $page_data[$key]["staff"]   = $value["charge_cd"]."<br>".htmlspecialchars($value["staff_name"])."<br>";

        // ����
        if ($value["state"] == "1"){
            $page_data[$key]["state"] = "�����";
        }else
        if ($value["state"] == "2"){
            $page_data[$key]["state"] = "���󡦵ٻ���";
        }else
        if ($value["state"] == "3"){
            $page_data[$key]["state"] = "����";
        }

        // �����襳���ɡ�������̾�ʣ�������
        $page_data[$key]["claim"]   = $value["claim_cd1"]."<br>".htmlspecialchars($value["claim_name1"])."<br>";
        $page_data[$key]["claim"]  .= ($value["claim_cd2"] != null) ? $value["claim_cd2"]."<br>".htmlspecialchars($value["claim_name2"])."<br>" : null;

        //��٥����
        $label_shop_id = $page_data[$key]["client_id"]; 
        $ary_shop_id[$i] = $label_shop_id;
        $form->addElement("advcheckbox", "form_label_check[$i]", null, null, null, array("null", "$label_shop_id"));

        #2010-05-11 hashimoto-y
        ##2010-04-27 hashimoto-y
        #$form->addElement("advcheckbox", "form_label_check2[$i]", null, null, null, array("null", "$label_shop_id"));

        //�����ͥ�����
        $form->addElement("advcheckbox", "form_card_check[$i]", null, null, null, array(null, "$label_shop_id"));
        $i++;

    }
}


/****************************/
// html����
/****************************/
// �ڡ���ʬ��
$html_page  = Html_Page2($total_count, $page_count, 1, $limit);
$html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

// ����ɽ��
$html_1 .= "\n";
$html_1 .= "<b style=\"font-size: 15px; color: #555555;\">�ھ��֡� ".$state_disp."��</b>\n";
$html_1 .= "<br>\n";

// �����ơ��֥�
$html_2 .= "<table class=\"List_Table\" border=\"1\" width=\"100%\">\n";
$html_2 .= "    <tr align=\"center\" style=\"font-weight: bold;\">\n";
$html_2 .= "        <td class=\"Title_Purple\">No.</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_group")."</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">�ƻ�<br>��ʬ</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_client_name")."</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">ά��</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">����<br>����</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_area")."</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">TEL</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_staff_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_staff_name")."</td>\n";

//ľ�Ĥξ��Τ���Ԥ�ɽ��
if($_SESSION[group_kind] == "2"){
$html_2 .= "        <td class=\"Title_Purple\">".Make_Sort_Link($form, "sl_act_client_cd")."<br><br style=\"font-size: 4px;\">".Make_Sort_Link($form, "sl_act_client_name")."</td>\n";
}
if ($state_type == "4"){
$html_2 .= "        <td class=\"Title_Purple\">����</td>\n";
}
$html_2 .= "        <td class=\"Title_Purple\">�����襳����<br>������̾</td>\n";
$html_2 .= "        <td class=\"Title_Purple\">".$form->_elements[$form->_elementIndex["label_check_all"]]->toHtml()."</td>\n";

#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#$html_2 .= "        <td class=\"Title_Purple\">".$form->_elements[$form->_elementIndex["label_check_all2"]]->toHtml()."</td>\n";

$html_2 .= "        <td class=\"Title_Purple\">".$form->_elements[$form->_elementIndex["card_check_all"]]->toHtml()."</td>\n";
$html_2 .= "    </tr>\n";
if (count($page_data) > 0){
    foreach ($page_data as $key => $value){
        $html_2 .= "    <tr class=\"Result1\">\n";
        $html_2 .= "        <td align=\"right\">".$value["no"]."</td>\n";
        $html_2 .= "        <td>".$value["group"]."</td>\n";
        $html_2 .= "        <td align=\"center\">".$value["parents_flg"]."</td>\n";
        $html_2 .= "        <td>".$value["client"]."</td>\n";
        $html_2 .= "        <td>".htmlspecialchars($value["client_cname"])."</td>\n";
        $html_2 .= "        <td align=\"center\">".$value["chg_lnk"]."</td>\n";
        $html_2 .= "        <td align=\"center\">".htmlspecialchars($value["area_name"])."</td>\n";
        $html_2 .= "        <td>".$value["tel"]."</td>\n";
        $html_2 .= "        <td>".$value["staff"]."</td>\n";

        //ľ�Ĥξ��Τ���Ԥ�ɽ��
        if($_SESSION[group_kind] == "2"){
        $html_2 .= "        <td>".$value["trust_cd"]."<br>".htmlspecialchars($value["trust_name"])."</td>\n";
        }
        if ($state_type == "4"){
        $html_2 .= "        <td align=\"center\">".$value["state"]."</td>\n";
        }
        $html_2 .= "        <td>".$value["claim"]."</td>\n";
        $html_2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_label_check[$key]"]]->toHtml()."</td>\n";

        #2010-05-11 hashimoto-y
        ##2010-04-27 hashimoto-y
        #$html_2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_label_check2[$key]"]]->toHtml()."</td>\n";

        $html_2 .= "        <td align=\"center\">".$form->_elements[$form->_elementIndex["form_card_check[$key]"]]->toHtml()."</td>\n";
        $html_2 .= "    </tr>\n";
    }
}
$html_2 .= "    <tr class=\"Result2\">";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
$html_2 .= "        <td></td>";
if($_SESSION["group_kind"] == '2'){
$html_2 .= "        <td></td>";
}
$html_2 .= "        <td></td>";
$html_2 .= "        <td>".$form->_elements[$form->_elementIndex["form_label_button"]]->toHtml()."</td>";

#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#$html_2 .= "        <td>".$form->_elements[$form->_elementIndex["form_label_button2"]]->toHtml()."</td>";

$html_2 .= "        <td>".$form->_elements[$form->_elementIndex["form_card_button"]]->toHtml()."</td>";
$html_2 .= "    </tr>";
$html_2 .= "</table>\n";

/* �ơ��֥�ޤȤ� */
$html_l .= "\n";
$html_l .= "<table width=\"100%\">\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_1</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_page</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_2</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "    <tr>\n";
$html_l .= "        <td>$html_page2</td>\n";
$html_l .= "    </tr>\n";
$html_l .= "</table>\n";
$html_l .= "\n";



//��٥����
$javascript  = Create_Allcheck_Js ("All_Label_Check","form_label_check",$ary_shop_id);
 
#2010-05-11 hashimoto-y
##2010-04-27 hashimoto-y
#$javascript .= Create_Allcheck_Js ("All_Label_Check2","form_label_check2",$ary_shop_id);

$javascript .= Create_Allcheck_Js ("All_Card_Check","form_card_check",$ary_shop_id);


#2010-04-06 hashimoto-y
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
$page_menu = Create_Menu_f("system", "1");

/****************************/
// ���̥إå�������
/****************************/
$page_title .= "(��".$all_count."��)";
$page_title .= "��".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "��".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
$page_header = Create_Header($page_title);

/****************************/
// �ڡ�������
/****************************/
$html_page  = Html_Page2($total_count, $page_count, 1, $limit);
$html_page2 = Html_Page2($total_count, $page_count, 2, $limit);

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
    "javascript"    => "$javascript",
));

// html��assign
$smarty->assign("html", array(
    "html_l"    => $html_l,
));

// �ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER["PHP_SELF"].".tpl"));

?>