<?php
/******************************
 *�ѹ�����
 *  ��������Ͽ��򼫼����������ѹ���20060811��watanabe-k
 *
 *  �Ұ�����Ͽ����������ɲá�20060831��kaji
 *
******************************/

/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2006-12-09      ban_0107    suzuki      ���դ򥼥����
 *  2007-09-27                  watanabe-k  ���������˺������������դ���Ͽ���줿���˥��顼��ɽ������������ɲ�
 *  2009/12/24                  aoyama-n    �������Ψ�ȸ�������Ψ��ɽ��������������Ψ����Ͽ�Ǥ���褦���ѹ�
 *
*/

$page_title = "�����ץ��ե�����";

//�Ķ�����ե�����
require_once("ENV_local.php");

//HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB��³
$db_con = Db_Connect();

// ���¥����å�
$auth       = Auth_Check($db_con);
// ���ϡ��ѹ�����̵����å�����
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//SESSION����
/****************************/
$client_id = $_SESSION[client_id];

/****************************/
//�����(radio)
/****************************/
$def_fdata = array(
    "form_claim_num"	=> "1",
    "form_coax"	=> "1",
    "from_fraction_div"	=> "1",
);
$form->setDefaults($def_fdata);

//�Ұ��Υѥ�
$path_shain = HEAD_DIR."system/1-1-301-3.php?shop_id=".$client_id;

/****************************/
//���ɽ������
/****************************/
$select_sql  = "SELECT ";
$select_sql .= "    client_cd1,";			//���������ɣ�
$select_sql .= "    client_cd2,";			//���������ɣ�
$select_sql .= "    shop_name,";			//��̾
$select_sql .= "    shop_name2,";           //��̾2
$select_sql .= "    shop_read,";			//��̾(�եꥬ��)
$select_sql .= "    shop_read2,";			//��̾2(�եꥬ��)
$select_sql .= "    represe,";				//��ɽ����
$select_sql .= "    rep_name,";				//��ɽ�Ի�̾
$select_sql .= "    rep_htel,";				//��ɽ�Է���
$select_sql .= "    post_no1,";				//͹���ֹ�
$select_sql .= "    post_no2,";				//͹���ֹ�
$select_sql .= "    address1,";				//����1
$select_sql .= "    address2,";				//����2
$select_sql .= "    address3,";				//����3
$select_sql .= "    address_read,";			//����(�եꥬ��)
$select_sql .= "    capital,";				//���ܶ�
$select_sql .= "    tel,";					//TEL
$select_sql .= "    fax,";					//FAX
$select_sql .= "    email,";				//Email
$select_sql .= "    url,";					//URL
$select_sql .= "    direct_tel,";			//ľ��TEL
#2009-12-24 aoyama-n
#$select_sql .= "    tax_rate_n,";			//���߾�����Ψ
#$select_sql .= "    tax_rate_c,";			//������Ψ
#$select_sql .= "    tax_rate_cday,";		//��Ψ������
$select_sql .= "    NULL,";			        //���߾�����Ψ
$select_sql .= "    NULL,";			        //������Ψ
$select_sql .= "    NULL,";	     	        //��Ψ������
$select_sql .= "    my_close_day,";			//����
$select_sql .= "    pay_m,";				//��ʧ��(��)
$select_sql .= "    pay_d,";				//��ʧ��(��)
$select_sql .= "    cutoff_month,";			//�軻��
$select_sql .= "    cutoff_day,";			//�軻��
$select_sql .= "    claim_set,";			//������ֹ�����
$select_sql .= "    ware_id, ";				//���ܽв��Ҹ�
$select_sql .= "    client_cname,";			//ά��
$select_sql .= "    client_cread, ";     	//ά��(�եꥬ��)
$select_sql .= "    my_coax, ";             //��۴ݤ��ʬ
#2009-12-24 aoyama-n
#$select_sql .= "    my_tax_franct ";        //������ü����ʬ
$select_sql .= "    my_tax_franct,";        //������ü����ʬ
$select_sql .= "    tax_rate_old,";		    //�������Ψ
$select_sql .= "    tax_rate_now,";		    //��������Ψ
$select_sql .= "    tax_change_day_now,";	//����Ψ������
$select_sql .= "    tax_rate_new,";		    //��������Ψ
$select_sql .= "    tax_change_day_new";	//����Ψ������
$select_sql .= " FROM";
$select_sql .= "    t_client";
$select_sql .= " WHERE";
$select_sql .= "    t_client.client_div = '0'";
$select_sql .= ";";

//������ȯ��
$result = Db_Query($db_con, $select_sql);
Get_Id_Check($result);
//�ǡ�������
$head_data = @pg_fetch_array ($result, 0, PGSQL_NUM);

$select_sql = "SELECT ";
$select_sql .= "    stand_day";
$select_sql .= " FROM";
$select_sql .= "    t_stand";
$select_sql .= " WHERE";
$select_sql .= "    client_id = $client_id";
$select_sql .= ";";
//������ȯ��
$result = Db_Query($db_con, $select_sql);
Get_Id_Check($result);
//�ǡ�������
$stand_row = @pg_num_rows($result);
$abcd_data = @pg_fetch_array ($result, 0, PGSQL_NUM);

//����ͥǡ���
$defa_data["form_head_cd"]["cd1"]         = $head_data[0];         //����������1
$defa_data["form_head_cd"]["cd2"]         = $head_data[1];         //����������2
$defa_data["form_comp_name"]              = $head_data[2];         //��̾
$defa_data["form_comp_name2"]             = $head_data[3];         //��̾2
$defa_data["form_comp_read"]              = $head_data[4];         //��̾(�եꥬ��)
$defa_data["form_comp_read2"]             = $head_data[5];         //��̾2(�եꥬ��)
$defa_data["form_represe"]                = $head_data[6];         //��ɽ����
$defa_data["form_rep_name"]               = $head_data[7];         //��ɽ�Ի�̾
$defa_data["form_represent_cell"]         = $head_data[8];         //��ɽ�Է���
$defa_data["form_post_no"]["no1"]         = $head_data[9];         //͹���ֹ�
$defa_data["form_post_no"]["no2"]         = $head_data[10];        //͹���ֹ�
$defa_data["form_address1"]               = $head_data[11];        //����1
$defa_data["form_address2"]               = $head_data[12];        //����2
$defa_data["form_address3"]               = $head_data[13];        //����3
$defa_data["form_address_read"]           = $head_data[14];        //����(�եꥬ��)
$defa_data["form_capital_money"]          = $head_data[15];        //���ܶ�
$defa_data["form_tel"]                    = $head_data[16];        //TEL
$defa_data["form_fax"]                    = $head_data[17];        //FAX
$defa_data["form_email"]                  = $head_data[18];        //Email
$defa_data["form_url"]                    = $head_data[19];        //URL
$defa_data["form_direct_tel"]             = $head_data[20];        //ľ��TEL
#2009-12-24 aoyama-n
#$defa_data["form_tax_now"]                = $head_data[21];        //���߾�����Ψ
#$defa_data["form_tax"]                    = $head_data[22];        //������Ψ
#$rate_day[y] = substr($head_data[23],0,4);
#$rate_day[m] = substr($head_data[23],5,2);
#$rate_day[d] = substr($head_data[23],8,2);
#$defa_data["form_tax_rate_day"]["y"]      = $rate_day[y];          //��Ψ������(ǯ)
#$defa_data["form_tax_rate_day"]["m"]      = $rate_day[m];          //��Ψ������(��)
#$defa_data["form_tax_rate_day"]["d"]      = $rate_day[d];          //��Ψ������(��)
$defa_data["form_close_day"]              = $head_data[24];        //����
$defa_data["form_pay_month"]              = $head_data[25];        //��ʧ��(��)
$defa_data["form_pay_day"]                = $head_data[26];        //��ʧ��(��)
$defa_data["form_cutoff_month"]           = $head_data[27];        //�軻��
$defa_data["form_cutoff_day"]             = $head_data[28];        //�軻��
//������ֹ����꤬¸�ߤ��뤫Ƚ��
if($head_data[29] != NULL){
	$defa_data["form_claim_num"]              = $head_data[29];        //������ֹ�����
}
$defa_data["form_ware"]                   = $head_data[30];        //���ܽв��Ҹ�
$defa_data["form_cname"]                  = $head_data[31];        //ά��
$defa_data["form_cread"]                  = $head_data[32];        //ά��(�եꥬ��)
//��۴ݤ��ʬ��¸�ߤ��뤫Ƚ��
if($head_data[33] != NULL){
	$defa_data["form_coax"]              = $head_data[33];         //��۴ݤ��ʬ
}
//������ü����ʬ��¸�ߤ��뤫Ƚ��
if($head_data[34] != NULL){
	$defa_data["from_fraction_div"]       = $head_data[34];         //������ü����ʬ
}

#2009-12-24 aoyama-n
$defa_data["form_tax_rate_old"]           = $head_data[35];         //�������Ψ
$defa_data["form_tax_rate_now"]           = $head_data[36];         //��������Ψ
$change_day_now[y] = substr($head_data[37],0,4);
$change_day_now[m] = substr($head_data[37],5,2);
$change_day_now[d] = substr($head_data[37],8,2);
$defa_data["form_tax_change_day_now"]["y"] = $change_day_now[y];    //����Ψ������
$defa_data["form_tax_change_day_now"]["m"] = $change_day_now[m];    //����Ψ������
$defa_data["form_tax_change_day_now"]["d"] = $change_day_now[d];    //����Ψ������
$defa_data["form_tax_rate_new"]           = $head_data[38];         //��������Ψ
$change_day_new[y] = substr($head_data[39],0,4);
$change_day_new[m] = substr($head_data[39],5,2);
$change_day_new[d] = substr($head_data[39],8,2);
$defa_data["form_tax_change_day_new"]["y"] = $change_day_new[y];    //����Ψ������
$defa_data["form_tax_change_day_new"]["m"] = $change_day_new[m];    //����Ψ������
$defa_data["form_tax_change_day_new"]["d"] = $change_day_new[d];    //����Ψ������

$abcd_day[y] = substr($abcd_data[0],0,4);
$abcd_day[m] = substr($abcd_data[0],5,2);
$abcd_day[d] = substr($abcd_data[0],8,2);
$defa_data["form_abcd_day"]["y"]          = $abcd_day[y];          //ABCD�������(ǯ)
$defa_data["form_abcd_day"]["m"]          = $abcd_day[m];          //ABCD�������(��)
$defa_data["form_abcd_day"]["d"]          = $abcd_day[d];          //ABCD�������(��)

//���������                                         
$form->setDefaults($defa_data);

/****************************/
//�ե���������
/****************************/
//����������
$form_head_cd[] =& $form->createElement(
		"text","cd1","�ƥ����ȥե�����","size=\"7\" maxLength=\"6\" style=\"$g_form_style\"
		$g_form_option"
		);
$form_head_cd[] =& $form->createElement(
		"static","","","-"
		);
$form_head_cd[] =& $form->createElement(
		"text","cd2","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
		$g_form_option"
		);
$form->addGroup( $form_head_cd, "form_head_cd", "form_head_cd");

//��̾
$form->addElement(
		"text","form_comp_name","�ƥ����ȥե�����","size=\"44\" maxLength=\"25\" 
		$g_form_option"
		);

//��̾2
$form->addElement(
		"text","form_comp_name2","�ƥ����ȥե�����","size=\"44\" maxLength=\"25\" 
		$g_form_option"
		);

//��̾(�եꥬ��)
$form->addElement(
        "text","form_comp_read","�ƥ����ȥե�����","size=\"46\" maxLength=\"50\" 
        $g_form_option"
        );

//��̾2(�եꥬ��)
$form->addElement(
        "text","form_comp_read2","�ƥ����ȥե�����","size=\"46\" maxLength=\"50\" 
        $g_form_option"
        );

//ά��
$form->addElement(
		"text","form_cname","�ƥ����ȥե�����","size=\"44\" maxLength=\"20\" 
		$g_form_option"
		);

//ά�Ρʥեꥬ�ʡ�
$form->addElement(
		"text","form_cread","�ƥ����ȥե�����","size=\"46\" maxLength=\"40\" 
		$g_form_option"
		);

//��ɽ����
$form->addElement(
		"text","form_represe","�ƥ����ȥե�����","size=\"22\" maxLength=\"10\" 
		$g_form_option"
		);

//��ɽ�Ի�̾
$form->addElement(
		"text","form_rep_name","�ƥ����ȥե�����","size=\"34\" maxLength=\"15\" 
		$g_form_option"
		);

//ľ��TEL
$form->addElement(
        "text","form_direct_tel","","size=\"34\" maxLength=\"30\" style=\"$g_form_style\""." $g_form_option"
        );

//��ɽ�Է���
$form->addElement(
        "text","form_represent_cell","�ƥ����ȥե�����","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

//͹���ֹ�
$form_post[] =& $form->createElement(
		"text","no1","�ƥ����ȥե�����","size=\"3\" maxLength=\"3\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_post_no[no1]','form_post_no[no2]',3)\"".$g_form_option."\""
		);
$form_post[] =& $form->createElement(
		"static","","","-"
		);
$form_post[] =& $form->createElement(
		"text","no2","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
		$g_form_option"
		);
$form->addGroup( $form_post, "form_post_no", "form_post_no");

//���ܶ�
$form->addElement(
        "text","form_capital_money","",
        "class=\"money\" size=\"11\" maxLength=\"9\" 
        style=\"text-align: right;$g_form_style\"".$g_form_option.""
        );

//����1
$form->addElement(

		"text","form_address1","�ƥ����ȥե�����","size=\"44\" maxLength=\"25\" 
		$g_form_option"
		);

//����2
$form->addElement(
		"text","form_address2","�ƥ����ȥե�����","size=\"44\" maxLength=\"25\" 
		$g_form_option"
		);

//����3
$form->addElement(
		"text","form_address3","�ƥ����ȥե�����","size=\"44\" maxLength=\"30\" 
		$g_form_option"
		);

//����2(�եꥬ��)
$form->addElement(
        "text","form_address_read","�ƥ����ȥե�����","size=\"46\" maxLength=\"50\" 
        $g_form_option"
        );

//TEL
$form->addElement(
		"text","form_tel","�ƥ����ȥե�����","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
		$g_form_option"
		);

//FAX
$form->addElement(
		"text","form_fax","�ƥ����ȥե�����","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
		$g_form_option"
		);

//Email
$form->addElement(
        "text","form_email","","size=\"34\" maxLength=\"60\" style=\"$g_form_style\""." $g_form_option"
        );

//URL
$form->addElement(
        "text","form_url","�ƥ����ȥե�����","size=\"34\" maxLength=\"30\" style=\"$g_form_style\"
        $g_form_option"
        );

#2009-12-24 aoyama-n
/********
//������Ψ(����)
$form->addElement(
		"text","form_tax_now","�ƥ����ȥե�����","size=\"1\" maxLength=\"2\" 
		 style=\"text-align: center; border : #ffffff; background-color: #ffffff;$g_form_style\" readonly"
		 );
//������Ψ
$form->addElement(
		"text","form_tax","�ƥ����ȥե�����","size=\"1\" maxLength=\"2\" 
		$g_form_option 
		style=\"text-align: right;$g_form_style\"");

//��Ψ������
$form_tax_rate_day[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_tax_rate_day[y]','form_tax_rate_day[m]',4)\" 
        ".$g_form_option."\""
        );
$form_tax_rate_day[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_rate_day[] =& $form->createElement(
        "text","m","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_tax_rate_day[m]','form_tax_rate_day[d]',2)\" 
        ".$g_form_option."\""
        );
$form_tax_rate_day[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_rate_day[] =& $form->createElement(
        "text","d","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        ".$g_form_option."\""
        );
$form->addGroup( $form_tax_rate_day,"form_tax_rate_day","");
**********/

//�������Ψ
$form->addElement(
		"text","form_tax_rate_old","�ƥ����ȥե�����","size=\"2\" maxLength=\"2\" 
		 style=\"text-align: center; border : #ffffff; background-color: #ffffff;$g_form_style\" readonly"
		 );

//��������Ψ
$form->addElement(
		"text","form_tax_rate_now","�ƥ����ȥե�����","size=\"2\" maxLength=\"2\" 
		 style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
         );

//����Ψ������
$form_tax_change_day_now[] =& $form->createElement(
        "text","y","","size=\"5\" maxLength=\"4\" 
		 style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
		 );
$form_tax_change_day_now[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_now[] =& $form->createElement(
        "text","m","","size=\"2\" maxLength=\"2\"
		 style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
        );
$form_tax_change_day_now[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_now[] =& $form->createElement(
        "text","d","","size=\"2\" maxLength=\"2\"
		 style=\"text-align: center; border : #ffffff; background-color: #ffffff; color: blue; font-weight: bold;$g_form_style\" readonly"
        );
$form->addGroup( $form_tax_change_day_now,"form_tax_change_day_now","");


//��������Ψ����ե饰
$form->addElement("checkbox", "form_tax_setup_flg", "", "");

//��������Ψ
$form->addElement(
		"text","form_tax_rate_new","�ƥ����ȥե�����","size=\"1\" maxLength=\"2\" 
		$g_form_option 
		style=\"text-align: right;$g_form_style\"");

//����Ψ������
$form_tax_change_day_new[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_tax_change_day_new[y]','form_tax_change_day_new[m]',4)\" 
        ".$g_form_option."\""
        );
$form_tax_change_day_new[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_new[] =& $form->createElement(
        "text","m","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_tax_change_day_new[m]','form_tax_change_day_new[d]',2)\" 
        ".$g_form_option."\""
        );
$form_tax_change_day_new[] =& $form->createElement(
        "static","","","-"
        );
$form_tax_change_day_new[] =& $form->createElement(
        "text","d","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        ".$g_form_option."\""
        );
$form->addGroup( $form_tax_change_day_new,"form_tax_change_day_new","");


//����
$close_day = Select_Get($db_con, "close" );
$form->addElement('select', 'form_close_day', '���쥯�ȥܥå���', $close_day, "onchange=\"window.focus();\"");

//��ʧ��
//��
$select_month[null] = null;
for($i = 0; $i <= 12; $i++){
    if($i == 0){
        $select_month[0] = "����";
    }elseif($i == 1){
        $select_month[1] = "���";
    }else{
        $select_month[$i] = $i."�����";
    }
}
$form->addElement("select", "form_pay_month", "���쥯�ȥܥå���", $select_month, $g_form_option_select);

//��
for($i = 0; $i <= 29; $i++){
    if($i == 29){
        $select_day[$i] = '����';
    }elseif($i == 0){
        $select_day[null] = null;
    }else{
        $select_day[$i] = $i."��";
    }
}
$form->addElement("select", "form_pay_day", "���쥯�ȥܥå���", $select_day, $g_form_option_select);

//�軻��
$form->addElement(
		"text","form_cutoff_month","�ƥ����ȥե�����","size=\"1\" maxLength=\"2\"  style=\"text-align: right;$g_form_style\"
		".$g_form_option."\""
		);

//�軻��
$form->addElement(
		"text","form_cutoff_day","�ƥ����ȥե�����","size=\"1\" maxLength=\"2\"  style=\"text-align: right;$g_form_style\"
		".$g_form_option."\""
		);

//ABCD�������
$form_abcd_day[] =& $form->createElement(
        "text","y","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_abcd_day[y]','form_abcd_day[m]',4)\" 
        ".$g_form_option."\""
        );
$form_abcd_day[] =& $form->createElement(
        "static","","","-"
        );
$form_abcd_day[] =& $form->createElement(
        "text","m","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        onkeyup=\"changeText(this.form,'form_abcd_day[m]','form_abcd_day[d]',2)\" 
        ".$g_form_option."\""
        );
$form_abcd_day[] =& $form->createElement(
        "static","","","-"
        );
$form_abcd_day[] =& $form->createElement(
        "text","d","","size=\"1\" maxLength=\"2\" style=\"$g_form_style\"
        ".$g_form_option."\""
        );
$form->addGroup( $form_abcd_day,"form_abcd_day","");

//������ֹ�����
$form_claim_num[] =& $form->createElement(
		"radio",NULL,NULL, "�̾�","1"
		);
$form_claim_num[] =& $form->createElement(
		"radio",NULL,NULL, "ǯ����","2"
		);
$form_claim_num[] =& $form->createElement(
		"radio",NULL,NULL, "����","3"
		);
$form->addGroup($form_claim_num, "form_claim_num", "������ֹ�����");

//�ޤ���ʬ
$form_coax[] =& $form->createElement(
         "radio",NULL,NULL, "�ڼ�","1"
         );
$form_coax[] =& $form->createElement(
         "radio",NULL,NULL, "�ͼθ���","2"
         );
$form_coax[] =& $form->createElement(
         "radio",NULL,NULL, "�ھ�","3"
         );
$form->addGroup($form_coax, "form_coax", "�ޤ���ʬ");

//ü����ʬ
$from_fraction_div[] =& $form->createElement(
         "radio",NULL,NULL, "�ڼ�","1"
         );
$from_fraction_div[] =& $form->createElement(
         "radio",NULL,NULL, "�ͼθ���","2"
         );
$from_fraction_div[] =& $form->createElement(
         "radio",NULL,NULL, "�ھ�","3"
         );
$form->addGroup($from_fraction_div, "from_fraction_div", "ü����ʬ");

//���ܽв��Ҹ�
$select_value = Select_Get($db_con,'ware');
$form->addElement('select', 'form_ware', '���쥯�ȥܥå���', $select_value,$g_form_option_select);

//��ư���ϥܥ���
$button[] = $form->createElement(
		"button","input_button","��ư����","onClick=\"javascript:Button_Submit_1('input_button_flg','#','true')\""
		); 

//��Ͽ�ܥ���
$button[] = $form->createElement(
        "submit","entry_button","�С�Ͽ",
        "onClick=\"javascript:return Dialogue('��Ͽ���ޤ���','#')\" $disabled"
);

//�Ұ����ѹ��ܥ���
$button[] = $form->createElement(
		"button","change_stamp","�Ұ����ѹ�","onClick=\"location.href('1-1-301-2.php')\" $disabled"
		);

//�Ұ��κ���ܥ���
$button[] = $form->createElement(
		"submit","delete_stamp","�Ұ��κ��","onClick=\"javascript:return dialogue5('�Ұ��������ޤ���','#')\" $disabled"
		);

$form->addGroup($button, "button", "");

//hidden
$form->addElement("hidden","input_button_flg");

/***************************/
//�롼�������QuickForm��
/***************************/

//������������
//��ɬ�ܥ����å�
//��Ⱦ�ѿ��������å�
$form->addGroupRule('form_head_cd', array(
        'cd1' => array(
                array('���������ɣ���Ⱦ�ѿ����ΤߤǤ���', 'required'),
                array('���������ɣ���Ⱦ�ѿ����ΤߤǤ���', "regex", "/^[0-9]+$/")
        ),
        'cd2' => array(
                array('���������ɣ���Ⱦ�ѿ����ΤߤǤ���','required'),
                array('���������ɣ���Ⱦ�ѿ����ΤߤǤ���',"regex", "/^[0-9]+$/")
        ),
));

//����̾
//��ɬ�ܥ����å�
$form->addRule("form_comp_name", "��̾��1ʸ���ʾ�25ʸ���ʲ��Ǥ���","required");
// ����/Ⱦ�ѥ��ڡ����Τߥ����å�
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_comp_name", "��̾ �˥��ڡ����Τߤ���Ͽ�ϤǤ��ޤ���", "no_sp_name");

//��ά��
//��ɬ�ܥ����å�
$form->addRule("form_cname", "ά�Τ�1ʸ���ʾ�20ʸ���ʲ��Ǥ���","required");
$form->addRule("form_cname", "ά�� �˥��ڡ����Τߤ���Ͽ�ϤǤ��ޤ���", "no_sp_name");

//����ɽ�Ի�̾
//��ɬ�ܥ����å�
$form->addRule("form_rep_name", "��ɽ�Ի�̾��1ʸ���ʾ�15ʸ���ʲ��Ǥ���","required");

//��͹���ֹ�
//��ɬ�ܥ����å�
//��Ⱦ�ѿ��������å�
//��ʸ���������å�
$form->addGroupRule('form_post_no', array(
        'no1' => array(
                array('͹���ֹ��Ⱦ�ѿ����Τ�7��Ǥ���', 'required'),
                array('͹���ֹ��Ⱦ�ѿ����Τ�7��Ǥ���', "regex", "/^[0-9]+$/"),
                array('͹���ֹ��Ⱦ�ѿ����Τ�7��Ǥ���', "rangelength", array(3,3))
        ),
        'no2' => array(
                array('͹���ֹ��Ⱦ�ѿ����Τ�7��Ǥ���','required'),
                array('͹���ֹ��Ⱦ�ѿ����Τ�7��Ǥ���',"regex", "/^[0-9]+$/"),
                array('͹���ֹ��Ⱦ�ѿ����Τ�7��Ǥ���', "rangelength", array(4,4))
        ),
));

//�����꣱
//��ɬ�ܥ����å�
$form->addRule("form_address1", "���꣱��1ʸ���ʾ�25ʸ���ʲ��Ǥ���","required");

//��TEL
//��ɬ�ܥ����å�
$form->addRule("form_tel", "TEL��Ⱦ�ѿ����Ȏ�-���Τ�30�����Ǥ���", "required");

#2009-12-24 aoyama-n
//��������Ψ
//��Ⱦ�ѿ��������å�
//$form->addRule("form_tax", "������Ψ��Ⱦ�ѿ����ΤߤǤ���", "regex", "/^[0-9]+$/");
#$form->addRule("form_tax", "������Ψ��Ⱦ�ѿ����ΤߤǤ���", "regex", '/^[0-9]+$/');

//����Ψ������
//��Ⱦ�ѿ��������å�
#$form->addGroupRule('form_tax_rate_day', array(
#        'y' => array(
#                array('��Ψ�����������դ������ǤϤ���ޤ���',"regex", "/^[0-9]+$/")
#        ),
#        'm' => array(
#                array('��Ψ�����������դ������ǤϤ���ޤ���',"regex", "/^[0-9]+$/")
#        ),
#        'd' => array(
#                array('��Ψ�����������դ������ǤϤ���ޤ���',"regex", "/^[0-9]+$/")
#        ),
#));

//������
//��ɬ�ܥ����å�
$form->addRule("form_close_day", "���������򤷤Ƥ���������","required");

//����ʧ���ʷ��
//��ɬ�ܥ����å�
//��Ⱦ�ѿ��������å�
$form->addRule("form_pay_month", "��ʧ���ʷ�ˤ�Ⱦ�ѿ����ΤߤǤ���", "required");
$form->addRule("form_pay_month", "��ʧ���ʷ�ˤ�Ⱦ�ѿ����ΤߤǤ���", "regex", "/^[0-9]+$/");

//����ʧ��������
//��ɬ�ܥ����å�
//��Ⱦ�ѿ��������å�
$form->addRule("form_pay_day", "��ʧ�������ˤ�Ⱦ�ѿ����ΤߤǤ���", "required");
$form->addRule("form_pay_day", "��ʧ�������ˤ�Ⱦ�ѿ����ΤߤǤ���", "regex", "/^[0-9]+$/");

//���軻��
//��Ⱦ�ѿ��������å�
$form->addRule("form_cutoff_month", "�軻���Ⱦ�ѿ����ΤߤǤ���","regex", "/^[0-9]+$/");

//�����ܶ�
//��Ⱦ�ѿ��������å�
//$form->addRule("form_capital_money", "���ܶ��Ⱦ�ѿ����ΤߤǤ���","regex", "/^[0-9]+$/");
$form->addRule("form_capital_money", "���ܶ��Ⱦ�ѿ����ΤߤǤ���","regex", '/^[0-9]+$/');

//���軻��
//��Ⱦ�ѿ��������å�
$form->addRule("form_cutoff_day", "�軻����Ⱦ�ѿ����ΤߤǤ���","regex", "/^[0-9]+$/");

//�����ܽв��Ҹ�
//��ɬ�ܥ����å�
$form->addRule("form_ware", "���ܽв��Ҹˤ����򤷤Ƥ���������","required");

/****************************/
//��Ͽ�ܥ��󲡲�
/****************************/
if($_POST["button"]["entry_button"] == "�С�Ͽ"){

    /****************************/
    //POST����
    /****************************/
    $head_cd1         = $_POST["form_head_cd"]["cd1"];          //����������1
    $head_cd2         = $_POST["form_head_cd"]["cd2"];          //����������2
    $comp_name        = $_POST["form_comp_name"];               //��̾
    $comp_name_read   = $_POST["form_comp_read"];               //��̾(�եꥬ��)
    $comp_name2       = $_POST["form_comp_name2"];              //��̾2
    $comp_name_read2  = $_POST["form_comp_read2"];              //��̾2(�եꥬ��)
    $cname            = $_POST["form_cname"];                   //ά��
    $cread            = $_POST["form_cread"];                   //ά��(�եꥬ��)
    $represe          = $_POST["form_represe"];                 //��ɽ����
    $rep_name         = $_POST["form_rep_name"];                //��ɽ�Ի�̾
    $rep_htel         = $_POST["form_represent_cell"];          //��ɽ�Է���
    $post_no1         = $_POST["form_post_no"]["no1"];          //͹���ֹ�
    $post_no2         = $_POST["form_post_no"]["no2"];          //͹���ֹ�
    $address1         = $_POST["form_address1"];                //����1
    $address2         = $_POST["form_address2"];                //����2
    $address3         = $_POST["form_address3"];                //����3
    $address_read     = $_POST["form_address_read"];            //����(�եꥬ��)
    $capital_money    = $_POST["form_capital_money"];           //���ܶ�
    $tel              = $_POST["form_tel"];                     //TEL
    $fax              = $_POST["form_fax"];                     //FAX
    $email            = $_POST["form_email"];                   //Email
    $url              = $_POST["form_url"];                     //URL
    $direct_tel       = $_POST["form_direct_tel"];              //ľ��TEL
    #2009-12-24 aoyama-n
    $tax_setup_flg    = $_POST["form_tax_setup_flg"];           //��������Ψ����ե饰
    #$tax              = $_POST["form_tax"];                     //������Ψ
    $tax_rate_new     = $_POST["form_tax_rate_new"];            //��������Ψ

    #2009-12-24 aoyama-n
    #$rate_day         = str_pad($_POST["form_tax_rate_day"]["y"],4,0,STR_PAD_LEFT);       //��Ψ������(ǯ)
    #$rate_day        .= "-";
    #$rate_day        .= str_pad($_POST["form_tax_rate_day"]["m"],2,0,STR_PAD_LEFT);       //��Ψ������(��)
    #$rate_day        .= "-";
    #$rate_day        .= str_pad($_POST["form_tax_rate_day"]["d"],2,0,STR_PAD_LEFT);       //��Ψ������(��)
    $tax_change_day_new  = str_pad($_POST["form_tax_change_day_new"]["y"],4,0,STR_PAD_LEFT);  //����Ψ������(ǯ)
    $tax_change_day_new .= "-";
    $tax_change_day_new .= str_pad($_POST["form_tax_change_day_new"]["m"],2,0,STR_PAD_LEFT);  //����Ψ������(��)
    $tax_change_day_new .= "-";
    $tax_change_day_new .= str_pad($_POST["form_tax_change_day_new"]["d"],2,0,STR_PAD_LEFT);  //����Ψ������(��)

    $close_day        = $_POST["form_close_day"];               //����
    $pay_month        = $_POST["form_pay_month"];               //��ʧ��(��)
    $pay_day          = $_POST["form_pay_day"];                 //��ʧ��(��)
    $cutoff_month     = $_POST["form_cutoff_month"];            //�軻��
    $cutoff_day       = $_POST["form_cutoff_day"];              //�軻��
    
    $abcday_y         = $_POST["form_abcd_day"]["y"];           //ABCD�������(ǯ)
    $abcday_m        .= $_POST["form_abcd_day"]["m"];           //ABCD�������(��)
    $abcday_d        .= $_POST["form_abcd_day"]["d"];           //ABCD�������(��)
    
    $claim_num        = $_POST["form_claim_num"];               //������ֹ�����
    $coax             = $_POST["form_coax"];                    //��۴ݤ��ʬ
    $franct           = $_POST["from_fraction_div"];            //������ü����ʬ
    $ware_id          = $_POST["form_ware"];                    //���ܽв��Ҹ�
    
    /***************************/
    //�����
    /***************************/
    //���������ɣ�
    $head_cd1 = str_pad($head_cd1, 6, 0, STR_POS_LEFT);

    //���������ɣ�
    $head_cd2 = str_pad($head_cd2, 4, 0, STR_POS_LEFT);

	/***************************/
	//�롼�������PHP��
	/***************************/
	//����������
	if($head_cd1 != null && $head_cd2 != null){
	    $head_cd_sql  = "SELECT";
	    $head_cd_sql  .= " client_id FROM t_client";
	    $head_cd_sql  .= " WHERE";
	    $head_cd_sql  .= " client_cd1 = '$head_cd1'";
	    $head_cd_sql  .= " AND";
	    $head_cd_sql  .= " '$head_data[0]' != '$head_cd1'";
	    $head_cd_sql  .= " AND";
	    $head_cd_sql  .= " client_cd2 = '$head_cd2'";
	    $head_cd_sql  .= " AND";
	    $head_cd_sql  .= " '$head_data[1]' != '$head_cd2'";
	    $head_cd_sql  .= ";";
		$select_shop = Db_Query($db_con, $head_cd_sql);
		$select_shop = pg_num_rows($select_shop);
		if($select_shop != 0){
			$head_cd_err = "���Ϥ��줿���������ɤϻ�����Ǥ���";
			$err_flg = true;
		}
	}
	
	//��TEL
	//��Ⱦ�ѿ����ȡ�-�װʳ��ϥ��顼
	if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$tel)){
		$tel_err = "TEL��Ⱦ�ѿ����Ȏ�-���Τ�30�����Ǥ���";
	  	$err_flg = true;
	}

	//��FAX
	//��Ⱦ�ѿ����ȡ�-�װʳ��ϥ��顼
	if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$fax) && $fax != null){
		$fax_err = "FAX��Ⱦ�ѿ����Ȏ�-���Τ�30�����Ǥ���";
	  	$err_flg = true;
	}

    //��Email
	if (!(ereg("^[^@]+@[^.]+\..+", $email)) && $email != "") {
        $email_err = "Email�������ǤϤ���ޤ���";
        $err_flg = true;
    }

    //��URL
    //�����ϥ����å�
    if (!preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $url) && $url != null) {
        $url_err = "������URL�����Ϥ��Ʋ�������";
        $err_flg = true;
    }

    //��ľ��TEL
    //��Ⱦ�ѿ����ȡ�-�װʳ��ϥ��顼
    if(!ereg("^[0-9]+-?[0-9]+-?[0-9]+$",$direct_tel) && $direct_tel != ""){
        $d_tel_err = "ľ��TEL��Ⱦ�ѿ����Ȏ�-���Τ�30�����Ǥ���";
        $err_flg = true;
    }

    //����Ψ������
    #2009-12-24 aoyama-n
    //�����դ������������å�
    #$rday_y = (int)$_POST["form_tax_rate_day"]["y"];
    #$rday_m = (int)$_POST["form_tax_rate_day"]["m"];
    #$rday_d = (int)$_POST["form_tax_rate_day"]["d"];

    #if($rday_m != null || $rday_d != null || $rday_y != null){
    #    $rday_flg = true;
    #}

    #$check_r_day = checkdate($rday_m,$rday_d,$rday_y);
    #if($check_r_day == false && $rday_flg == true){
    #    $rday_err = "��Ψ�����������դ������ǤϤ���ޤ���";
    #    $err_flg = true;
    //̤������ե����å�
    #}elseif(date("Ymd", mktime(0,0,0,$rday_m, $rday_d, $rday_y)) <= date("Ymd")){
    #    $rday_err = "��Ψ��������̤������դ���ꤷ�Ʋ�������";
    #    $err_flg = true;
    #}

    #2009-12-24 aoyama-n
    #��������Ψ�����ꤹ��˥����å���������Τ߽�����Ԥ�
    if($tax_setup_flg == 1){

        //��������Ψ
        //��ɬ�ܥ����å�
        $form->addRule("form_tax_rate_new", "��������Ψ��Ⱦ�ѿ����ΤߤǤ���","required");
        //��Ⱦ�ѿ��������å�
        $form->addRule("form_tax_rate_new", "��������Ψ��Ⱦ�ѿ����ΤߤǤ���", "regex", '/^[0-9]+$/');

        //����Ψ������
        //��Ⱦ�ѿ��������å�
        $form->addGroupRule('form_tax_change_day_new', array(
                'y' => array(
                        array('����Ψ�����������դ������ǤϤ���ޤ���',"regex", "/^[0-9]+$/")
                ),
                'm' => array(
                        array('����Ψ�����������դ������ǤϤ���ޤ���',"regex", "/^[0-9]+$/")
                ),
                'd' => array(
                       array('����Ψ�����������դ������ǤϤ���ޤ���',"regex", "/^[0-9]+$/")
                ),
        ));

	    //�����դ������������å�
	    $rday_y = (int)$_POST["form_tax_change_day_new"]["y"];
	    $rday_m = (int)$_POST["form_tax_change_day_new"]["m"];
	    $rday_d = (int)$_POST["form_tax_change_day_new"]["d"];
	
	    if($rday_m != null || $rday_d != null || $rday_y != null){
            //�����������å�
	        $check_r_day = checkdate($rday_m,$rday_d,$rday_y);
	        if($check_r_day == false){
		        $rday_err = "����Ψ�����������դ������ǤϤ���ޤ���";
	  	        $err_flg = true;
            //̤������ե����å�
	        }elseif(date("Ymd", mktime(0,0,0,$rday_m, $rday_d, $rday_y)) <= date("Ymd")){
		        $rday_err = "����Ψ��������̤������դ���ꤷ�Ʋ�������";
	  	        $err_flg = true;
            }
	    }else{
		    $rday_err = "����Ψ�����������դ������ǤϤ���ޤ���";
	  	    $err_flg = true;
        }
    #��������Ψ�����ꤹ��˥����å���������Τ߽�����Ԥ�
    }else{

        #��������Ψ�򿷤������ꤹ����
        if($defa_data["form_tax_rate_new"]         == null &&
           #2010-05-31 hashimoto-y �Х�����
           #($_POST["form_tax_change_day_new"]      != null ||
            #$_POST["form_tax_change_day_new"]["y"] != null || 
            ($_POST["form_tax_change_day_new"]["y"] != null || 
            $_POST["form_tax_change_day_new"]["m"] != null || 
            $_POST["form_tax_change_day_new"]["d"] != null)){
           $rday_err = "��������Ψ�����ꤹ����ϡ������å��ܥå����˥����å�������Ƥ���������";
           $err_flg = true;
        }
    }
	
	//��ABCD�������
	//�����դ������������å�
	//�����դ���������Ƚ��
    //�������Ϥ��줿���˥����å���Ԥ�
    if($abcday_m != null || $abcday_d != null || $abcday_y != null){
        $abcday_y = (int)$abcday_y;
        $abcday_m = (int)$abcday_m;
        $abcday_d = (int)$abcday_d;

		//���դ���������Ƚ��
		$now = mktime(0, 0, 0, $abcday_m, $abcday_d, $abcday_y);
		$date = date(w,$now);
		if($date != 0){
			$form->setElementError("form_abcd_day","ABCD������� �������ǤϤ���ޤ���");
		}

        //���դ������������å�
        if(checkdate($abcday_m,$abcday_d,$abcday_y)){
            $abcday = "'".str_pad($abcday_y,4,0,STR_PAD_LEFT)."-".str_pad($abcday_m,2,0,STR_PAD_LEFT)."-".str_pad($abcday_d,2,0,STR_PAD_LEFT)."'";
        }else{
            $form->setElementError("form_abcd_day","ABCD������� �������ǤϤ���ޤ���");
        }

		//���դ������������Ƚ��
		$to_day = date("Y-m-d");
		if($to_day <= $abcday){
			$form->setElementError("form_abcd_day","ABCD������� �������ǤϤ���ޤ���");
		}

    }else{
        //�������Ϥ���Ƥ��ʤ�����NULL����
        $abcday = "null";
    }

}
if($_POST["button"]["entry_button"] == "�С�Ͽ" && $form->validate() && $err_flg != true ){
	//��Ͽ��λ��å�����
	$fin_msg = "��Ͽ���ޤ�����";
	Db_Query($db_con, "BEGIN;");
	
	$update_sql  = "UPDATE ";
	$update_sql .= "    t_client";
	$update_sql .= " SET";
	$update_sql .= "    client_cd1 = '$head_cd1',";
	$update_sql .= "    client_cd2 = '$head_cd2',";
	$update_sql .= "    shop_name = '$comp_name',";
	$update_sql .= "    shop_read = '$comp_name_read',";
    $update_sql .= "    shop_name2 = '$comp_name2',";
	$update_sql .= "    shop_read2 = '$comp_name_read2',";
    $update_sql .= "    client_cname = '$cname',";
	$update_sql .= "    client_cread = '$cread',";
	$update_sql .= "    represe = '$represe',";
    #2009-12-24 aoyama-n
	#$update_sql .= "    tax_rate_c = '$tax',";	
	#if($rate_day == "--"){
	#	$update_sql .= "    tax_rate_cday = null,";	
	#}else{
	#	$update_sql .= "    tax_rate_cday = '$rate_day',";	
	#}
    #�ֿ�������Ψ�����ꤹ��פ˥����å���������Τ߽�����Ԥ�
    if($tax_setup_flg == 1){
	    $update_sql .= "    tax_rate_new = '$tax_rate_new',";	
		$update_sql .= "    tax_change_day_new = '$tax_change_day_new',";	
	}
	$update_sql .= "    cutoff_month = '$cutoff_month',";	
	$update_sql .= "    cutoff_day = '$cutoff_day',";	
	$update_sql .= "    rep_name = '$rep_name',";
	$update_sql .= "    rep_htel = '$rep_htel',";
	$update_sql .= "    post_no1 = '$post_no1',";
	$update_sql .= "    post_no2 = '$post_no2',";
	$update_sql .= "    address1 = '$address1',";
	$update_sql .= "    address2 = '$address2',";
    $update_sql .= "    address3 = '$address3',";
	$update_sql .= "    address_read = '$address_read',";
	$update_sql .= "    capital = '$capital_money',";
	$update_sql .= "    tel = '$tel',";
	$update_sql .= "    fax = '$fax',";
	$update_sql .= "    email = '$email',";
	$update_sql .= "    url = '$url',";
	$update_sql .= "    direct_tel = '$direct_tel',";
	$update_sql .= "    my_close_day = '$close_day',";
	$update_sql .= "    pay_m = '$pay_month',";
	$update_sql .= "    pay_d = '$pay_day',";
	$update_sql .= "    claim_set = '$claim_num',";
	$update_sql .= "    my_coax = '$coax',";
	$update_sql .= "    my_tax_franct = '$franct',";
    $update_sql .= "    ware_id = $ware_id ";
	$update_sql .= " WHERE ";
	$update_sql .= "    client_div = '0'";
	$update_sql .= ";";
	$result = Db_Query($db_con, $update_sql);
	if($result === false){
		Db_Query($db_con, "ROLLBACK;");
		exit;
	}
    //�����������������˻Ĥ�
    $result = Log_Save( $db_con, "client", "2", $head_cd1."-".$head_cd2, $comp_name);
    if($result === false){
        Db_Query($db_con, "ROLLBACK");
        exit;
    }

    //ABCD���������Ͽ������
	if($stand_row != 0){
		$abcd_sql  = "UPDATE ";
		$abcd_sql .= "    t_stand";
		$abcd_sql .= " SET";
		$abcd_sql .= "    stand_day = $abcday";
		$abcd_sql .= " WHERE";
		$abcd_sql .= "    client_id = $client_id";
		$abcd_sql .= ";";
	}else{
		$abcd_sql  = "INSERT INTO ";
		$abcd_sql .= " t_stand(";
		$abcd_sql .= "    client_id,";
		$abcd_sql .= "    stand_day";
		$abcd_sql .= ")values(";
		$abcd_sql .= "    '$client_id',";
		$abcd_sql .= "    $abcday";
		$abcd_sql .= ");";
	}
	$result = Db_Query($db_con, $abcd_sql);
	if($result === false){
		Db_Query($db_con, "ROLLBACK;");
		exit;
	}
    //�����������������˻Ĥ�
    $result = Log_Save( $db_con, "stand", "2", $head_cd1."-".$head_cd2, $comp_name);
    if($result === false){
        Db_Query($db_con, "ROLLBACK");
        exit;
    }
	
	Db_Query($db_con, "COMMIT;");
}
/****************************/
//��ư���ϥܥ��󲡲�
/****************************/
if($_POST["input_button_flg"]==true){
	$post1     = $_POST["form_post_no"]["no1"];             //͹���ֹ棱
	$post2     = $_POST["form_post_no"]["no2"];             //͹���ֹ棲
	$post_value = Post_Get($post1,$post2,$db_con);
	//͹���ֹ�ե饰�򥯥ꥢ
	$cons_data["input_button_flg"] = "";
	//͹���ֹ椫�鼫ư����
	$cons_data["form_post_no"]["no1"] = $_POST["form_post_no"]["no1"];
	$cons_data["form_post_no"]["no2"] = $_POST["form_post_no"]["no2"];
    $cons_data["form_address_read"] = $post_value[0];
	$cons_data["form_address1"] = $post_value[1];
	$cons_data["form_address2"] = $post_value[2];

	$form->setConstants($cons_data);
}
/****************************/
//�Ұ��������
/****************************/
if($_POST["button"]["delete_stamp"] == "�Ұ��κ��"){

    $shain_file = COMPANY_SEAL_DIR.$client_id.".jpg";

    // �ե�����¸��Ƚ��
    if(file_exists($shain_file)){
        // �ե�������
        $res = unlink($shain_file);
        header("Location: ".$_SERVER[PHP_SELF]." ");
    }
}


/****************************/
//HTML�إå�
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//HTML�եå�
/****************************/
$html_footer = Html_Footer();

/****************************/
//��˥塼����
/****************************/
$page_menu = Create_Menu_h('system','3');
/****************************/
//���̥إå�������
/****************************/
$page_header = Create_Header($page_title);

// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form��Ϣ���ѿ���assign
$smarty->assign('form',$renderer->toArray());

//����¾���ѿ���assign
$smarty->assign('var',array(
	'html_header'   => "$html_header",
	'page_menu'     => "$page_menu",
	'page_header'   => "$page_header",
	'html_footer'   => "$html_footer",
	'head_cd_err'   => "$head_cd_err",
	'tel_err'       => "$tel_err",
	'fax_err'       => "$fax_err",
	'rday_err'      => "$rday_err",
//	'abcday_err'    => "$abcday_err",
    'email_err'     => "$email_err",
    'url_err'       => "$url_err",
    'd_tel_err'     => "$d_tel_err",
	'fin_msg'   	=> "$fin_msg",
    'auth_r_msg'    => "$auth_r_msg",
    'path_shain'    => "$path_shain",
));

//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>