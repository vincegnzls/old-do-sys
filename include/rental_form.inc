<?php

/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2007/01/15      xx_xxx      kajioka-h   ���ե饤�����ΤȤ����ѹ��ܥ����Ĥ�������ѹ�
*/

/****************************/
//��󥿥�TO��󥿥���������
/****************************/

//��󥿥��ֹ�
$form->addElement(
    "text","form_rental_no","",
    "style=\"color : #585858; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);

//����ɽ������Ƚ��
if(($disp_stat == 4 || $disp_stat == 2 || $disp_stat == 1) && $online_flg == 'f' && $group_kind == 1){
	//���ե饤��

	// FC�Υ���å�
	$select_value = NULL;
	//�ץ�������ͼ������
	$where  = " WHERE ";
    $where .= "     t_client.client_div = '3' ";
    $where .= " AND ";
    $where .= "     t_rank.group_kind IN ('2','3') ";
    $where .= " AND ";
	//�ѹ�ɽ��Ƚ��
	if($rental_id != NULL){
		//�ѹ�(�����orDB����Ͽ����Ƥ��륷��å�)
		$where .= "     (t_client.state = '1' OR (t_client.client_id = (SELECT shop_id FROM t_rental_h WHERE rental_id = $rental_id)))";
	}else{
		//������Ͽ(�����Τ�)
		$where .= "     t_client.state = '1' ";
	}

	$select_value = Select_Get($db_con, "rshop",$where);
	$offline = $form->addElement('select', 'form_shop_name', '���쥯�ȥܥå���', $select_value,"onKeyDown=\"chgKeycode();\" onChange =\"Button_Submit('client_search_flg','#','true');window.focus();\"");

	//���ե饤��η�����ϥ���åפ��ѹ��Բ�
	if($disp_stat == 2){
		$offline->freeze();
	}

}else{
	//����饤��

	//����å�̾
	$form->addElement(
	    "text","form_shop_name","",
	    "style=\"color : #585858; width:300pt;
	    border : #ffffff 1px solid; 
	    background-color: #ffffff; 
	    text-align: left\" readonly'"
	);
}

//����ɽ������Ƚ��
if($disp_stat == 1 || $disp_stat == 5 || (($disp_stat == 4 || $disp_stat == 2 || $disp_stat == 1) && $online_flg == 'f' && $group_kind == 1)){
	//��󥿥�ID̵������úѡ�����ѡ������(���ե饤��)����󥿥�ID̵��(���ե饤��)������ͽ��(���ե饤��)

	//��󥿥뿽����
	$form_rental_day[] =& $form->createElement("text","y","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"4\" maxLength=\"4\" onkeyup=\"changeText(this.form,'form_rental_day[y]','form_rental_day[m]',4)\" onFocus=\"onForm_today(this,this.form,'form_rental_day[y]','form_rental_day[m]','form_rental_day[d]')\" onBlur=\"blurForm(this)\"");
	$form_rental_day[] =& $form->createElement("text","m","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onkeyup=\"changeText(this.form,'form_rental_day[m]','form_rental_day[d]',2)\" onFocus=\"onForm_today(this,this.form,'form_rental_day[y]','form_rental_day[m]','form_rental_day[d]')\" onBlur=\"blurForm(this)\"");
	$form_rental_day[] =& $form->createElement("text","d","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onFocus=\"onForm_today(this,this.form,'form_rental_day[y]','form_rental_day[m]','form_rental_day[d]')\" onBlur=\"blurForm(this)\"");
	$form->addGroup( $form_rental_day,"form_rental_day","form_rental_day","-");
}else{
	//����������
	//����ѡ������
	//������
	//����ͽ��

	//��󥿥뿽����
	$form_rental_day[] =& $form->createElement("text","y","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"4\" maxLength=\"4\" onkeyup=\"changeText(this.form,'form_rental_day[y]','form_rental_day[m]',4)\" onFocus=\"onForm_today(this,this.form,'form_rental_day[y]','form_rental_day[m]','form_rental_day[d]')\" onBlur=\"blurForm(this)\"");
	$form_rental_day[] =& $form->createElement("text","m","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onkeyup=\"changeText(this.form,'form_rental_day[m]','form_rental_day[d]',2)\" onFocus=\"onForm_today(this,this.form,'form_rental_day[y]','form_rental_day[m]','form_rental_day[d]')\" onBlur=\"blurForm(this)\"");
	$form_rental_day[] =& $form->createElement("text","d","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onFocus=\"onForm_today(this,this.form,'form_rental_day[y]','form_rental_day[m]','form_rental_day[d]')\" onBlur=\"blurForm(this)\"");
	$freeze_data = $form->addGroup( $form_rental_day,"form_rental_day","form_rental_day","-");
	$freeze_data->freeze();
}

$form_staff_id = Select_Get($db_con,'cstaff');

//�ե�����Ƚ��
if((($disp_stat == 4 || $disp_stat == 2 || $disp_stat == 1) && $online_flg == 'f') || $group_kind == 1){
	//���ե饤��or����
	
	//����ô����
	$freeze_form[] = $form->addElement('text', 'form_app_staff',"",'size="23" maxLength="10"'." $g_form_option");
	//���ô����
	$freeze_form[] = $form->addElement('text', 'form_round_staff',"",'size="23" maxLength="10"'." $g_form_option");
}else{
	//����饤��(FC)

	//����ô����
	$freeze_form[] = $form->addElement('select', 'form_app_staff', '���쥯�ȥܥå���', $form_staff_id, $g_form_option_select);
	//���ô����
	$freeze_form[] = $form->addElement('select', 'form_round_staff', '���쥯�ȥܥå���', $form_staff_id, $g_form_option_select);
}

//���ե饤��Ƚ��
if(($disp_stat == 4 || $disp_stat == 2 || $disp_stat == 1) && $online_flg == 'f' && $group_kind == 1){
	//���ե饤��

	//�桼��̾1
	$form_client[] =& $form->createElement("text", "name", "",'size="34" maxLength="20"'." $g_form_option");
	$freeze_form[] = $form->addGroup( $form_client, "form_client", "");

	//�桼��̾2
	$freeze_form[] = $form->addElement('text', 'form_client_name2',"",'size="34" maxLength="25"'." $g_form_option");
}else{
	//����饤��

	//�桼��̾
	$form_client[] =& $form->createElement(
	    "text","cd1","","size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onChange=\"javascript:Change_Submit('client_search_flg','#','true','form_client[cd2]')\" onkeyup=\"changeText(this.form,'form_client[cd1]','form_client[cd2]',6)\"".$g_form_option."\""
	);
	$form_client[] =& $form->createElement(
	    "static","","","-"
	);
	$form_client[] =& $form->createElement(
	    "text","cd2","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onChange=\"javascript:Button_Submit('client_search_flg','#','true')\"".$g_form_option."\""
	);
	$form_client[] =& $form->createElement(
	    "text","name","",
	    "size=\"34\" $g_text_readonly"
	);

	//����ѡ������(FC)Ƚ��
	if($disp_stat == 2 && $group_kind == 2){
		//����ѡ������(FC)�ʤ�桼�����ѹ��Բ�
		$freeze_client = $form->addGroup( $form_client, "form_client", "");
		$freeze_client->freeze();
	}else{
		//����饤��
		$freeze_form[] = $form->addGroup( $form_client, "form_client", "");
	}
}

//����ɽ������Ƚ��
if(($disp_stat == 2) || ($disp_stat == 1 && $group_kind == 1)){
	//����ѡ������
	//��󥿥�ID̵��(����)
	//��ǧ����

	//�桼��̾��󥯤ʤ�
	$form->addElement("static","form_client_link","","�桼��̾");

}else if($disp_stat == 1 || $disp_stat == 5){
	//��󥿥�ID̵��
	//��ú�
	
	//�桼��̾���
	$form->addElement("link","form_client_link","","./2-1-141.php","�桼��̾","
	    onClick=\"return Open_SubWin('".FC_DIR."dialog/2-0-250.php',Array('form_client[cd1]','form_client[cd2]','form_client[name]', 'client_search_flg'),500,450,5,1);\""
	);
}else{
	//����������
	//������
	//����ͽ��

	//�桼��̾��󥯤ʤ�
	$form->addElement("static","form_client_link","","�桼��̾");
}

//�桼��TEL
$freeze_form[] = $form->addElement("text", "form_tel", "", "size=\"34\" maxLength=\"30\" style=\"$g_form_style\""." $g_form_option");

//͹���ֹ�
$form_post[] =& $form->createElement("text", "no1", "", "size=\"3\" maxLength=\"3\" style=\"$g_form_style\" onkeyup=\"changeText(this.form, 'form_post[no1]', 'form_post[no2]',3)\"".$g_form_option."\"");
$form_post[] =& $form->createElement("static", "", "", "-");
$form_post[] =& $form->createElement("text", "no2", "", "size=\"4\" maxLength=\"4\" style=\"$g_form_style\""." $g_form_option");
$freeze_form[] = $form->addGroup($form_post, "form_post", "");

// ���꣱
$freeze_form[] = $form->addElement("text", "form_add1", "",'size="50" maxLength="25"'." $g_form_option");
// ���ꣲ
$freeze_form[] = $form->addElement("text", "form_add2", "",'size="50" maxLength="25"'." $g_form_option");
// ���ꣳ
$freeze_form[] = $form->addElement("text", "form_add3", "",'size="55" maxLength="30"'." $g_form_option");
// ����(�եꥬ��)
$freeze_form[] = $form->addElement("text", "form_add_read", "",'size="50" maxLength="50"'." $g_form_option");

//����
$freeze_form[] = $form->addElement("textarea","form_note","",'size="30" maxLength="100" cols="40"'.$g_form_option_area);

//����Ƚ��
if($group_kind == 1){
	//����

	//��󥿥�в���
	$form_forward_day[] =& $form->createElement("text","y","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"4\" maxLength=\"4\" onkeyup=\"Rental_claim(this.form,'form_forward_day[y]','form_forward_day[m]',4,'form_forward_day[y]','form_forward_day[m]','form_claim_day[y]','form_claim_day[m]')\" onFocus=\"Forward_today(this,this.form,'form_forward_day[y]','form_forward_day[m]','form_forward_day[d]','form_claim_day[y]','form_claim_day[m]')\" onBlur=\"blurForm(this)\"");
	$form_forward_day[] =& $form->createElement("text","m","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onkeyup=\"Rental_claim(this.form,'form_forward_day[m]','form_forward_day[d]',2,'form_forward_day[y]','form_forward_day[m]','form_claim_day[y]','form_claim_day[m]')\" onFocus=\"Forward_today(this,this.form,'form_forward_day[y]','form_forward_day[m]','form_forward_day[d]','form_claim_day[y]','form_claim_day[m]')\" onBlur=\"blurForm(this)\"");
	$form_forward_day[] =& $form->createElement("text","d","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onFocus=\"Forward_today(this,this.form,'form_forward_day[y]','form_forward_day[m]','form_forward_day[d]','form_claim_day[y]','form_claim_day[m]')\" onBlur=\"blurForm(this)\"");
	$freeze_form2[] = $form->addGroup( $form_forward_day,"form_forward_day","form_forward_day","-");

	//����ô����
	$freeze_form2[] = $form->addElement('select', 'form_head_staff', '���쥯�ȥܥå���', $form_staff_id, $g_form_option_select);

	//�����
	$form_claim_day[] =& $form->createElement("text","y","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" onkeyup=\"changeText(this.form,'form_claim_day[y]','form_claim_day[m]',4)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
	$form_claim_day[] =& $form->createElement("text","m","�ƥ����ȥե�����","size=\"1\" maxLength=\"2\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
	$freeze_form2[] = $form->addGroup( $form_claim_day,"form_claim_day","form_claim_day","-");

	//����(������)
	$freeze_form2[] = $form->addElement("textarea","form_h_note","",'size="30" maxLength="100" cols="40"'.$g_form_option_area);
}else{
	//FC

	//��󥿥�в���
	$form_forward_day[] =& $form->createElement("text","y","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"4\" maxLength=\"4\" onkeyup=\"changeText(this.form,'form_forward_day[y]','form_forward_day[m]',4)\" onFocus=\"onForm_today(this,this.form,'form_forward_day[y]','form_forward_day[m]','form_forward_day[d]')\" onBlur=\"blurForm(this)\"");
	$form_forward_day[] =& $form->createElement("text","m","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onkeyup=\"changeText(this.form,'form_forward_day[m]','form_forward_day[d]',2)\" onFocus=\"onForm_today(this,this.form,'form_forward_day[y]','form_forward_day[m]','form_forward_day[d]')\" onBlur=\"blurForm(this)\"");
	$form_forward_day[] =& $form->createElement("text","d","�ƥ����ȥե�����","style=\"ime-mode:disabled;\" size=\"1\" maxLength=\"2\" onFocus=\"onForm_today(this,this.form,'form_forward_day[y]','form_forward_day[m]','form_forward_day[d]')\" onBlur=\"blurForm(this)\"");
	$freeze_data = $form->addGroup( $form_forward_day,"form_forward_day","form_forward_day","-");
	$freeze_data->freeze();

	//����ô����
	$freeze_data = $form->addElement('select', 'form_head_staff', '���쥯�ȥܥå���', $form_staff_id, $g_form_option_select);
	$freeze_data->freeze();

	//�����
	$form_claim_day[] =& $form->createElement("text","y","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" onkeyup=\"changeText(this.form,'form_claim_day[y]','form_claim_day[m]',4)\" onFocus=\"onForm_today2(this,this.form,'form_claim_day[y]','form_claim_day[m]')\" onBlur=\"blurForm(this)\"");
	$form_claim_day[] =& $form->createElement("text","m","�ƥ����ȥե�����","size=\"1\" maxLength=\"2\" onFocus=\"onForm_today2(this,this.form,'form_claim_day[y]','form_claim_day[m]')\" onBlur=\"blurForm(this)\"");
	$freeze_data = $form->addGroup( $form_claim_day,"form_claim_day","form_claim_day","-");
	$freeze_data->freeze();

	//����(������)
	$freeze_data = $form->addElement("textarea","form_h_note","",'size="30" maxLength="100" cols="40"'.$g_form_option_area);
	$freeze_data->freeze();
}

//����ɽ������Ƚ��
if($disp_stat == 6 && $group_kind == 1 && $online_flg == 't'){
	//����������(����)
	
	//��ǧ��ǧ�ܥ���
	$form->addElement("submit","comp_btn","��ǧ��ǧ���̤�","$disabled");
	//��ǧ�ϣ˥ܥ���
	$form->addElement("submit","ok_btn","��ǧOK");

	//������ó�ǧ�ܥ���
	$form->addElement("submit","cancel_btn","������ó�ǧ���̤�","$disabled");
	//������ãϣ˥ܥ���
	$form->addElement("button","cancel_ok_btn", "�������OK", "onClick=\"javascript:Button_Submit_1('cancel_flg', '#','true')\"");

}else if($disp_stat == 2 && $group_kind == 1 && $online_flg == 'f'){
	//����ѡ������(���ե饤��)

    //�ѹ���ǧ�ܥ���
    $form->addElement("submit","edit_btn", "�ѹ���ǧ���̤�", "$disabled");

    //�����ǧ�ܥ���
    $form->addElement("submit","comp_btn","�����ǧ���̤�","$disabled");

    //�ѹ���
    if($edit_flg == true){
    	//�ѹ��ϣ˥ܥ���
        $form->addElement("submit","ok_btn","�ѹ�OK");
    //�����
    }else{
        //����ϣ˥ܥ���
        $form->addElement("submit","ok_btn","����OK");
    }

	//��ư����
	$form->addElement("button","input_auto", "��ư����", "onClick=\"javascript:Button_Submit_1('auto_flg', '#', 'true')\"");

}else if($disp_stat == 2 && $group_kind == 1){
	//����ѡ������(����)

	//�ѹ���ǧ�ܥ���
	$form->addElement("submit","comp_btn","�ѹ���ǧ���̤�","$disabled");
	//�ѹ��ϣ˥ܥ���
	$form->addElement("submit","ok_btn","�ѹ�OK");

}else if($disp_stat == 3 && $group_kind == 1 && $online_flg == 't'){
	//������(����)
	
	//����ǧ���»ܳ�ǧ�ܥ���
	$form->addElement("submit","comp_btn","����ǧ���»ܳ�ǧ���̤�","$disabled");
	//����ǧ���»ܣϣ˥ܥ���
	$form->addElement("submit","ok_btn","����ǧ���»�OK");

	//�����ó�ǧ�ܥ���
	$form->addElement("submit","cancel_btn","�����ó�ǧ���̤�","$disabled");
	//�����ãϣ˥ܥ���
	$form->addElement("button","cancel_ok_btn", "������OK", "onClick=\"javascript:Button_Submit_1('cancel_flg', '#','true')\"");

}else if($disp_stat == 4 && $group_kind == 1){
	//����ͽ��(����)
	
	//�����ó�ǧ�ܥ���
	$form->addElement("submit","comp_btn","�����ó�ǧ���̤�","$disabled");
	//�����ãϣ˥ܥ���
	$form->addElement("submit","ok_btn","������OK");

}else if($disp_stat == 1 || $disp_stat == 5 || ($disp_stat == 1 && $online_flg == 'f')){
	//��󥿥�ID̵������úѡ���󥿥�ID̵��(���ե饤��)

	//��ư����
	$form->addElement("button","input_auto", "��ư����", "onClick=\"javascript:Button_Submit_1('auto_flg', '#', 'true')\"");
	//���ɲåܥ���
	$form->addElement("button","add_row_btn","���ɲ�","onClick=\"javascript:Button_Submit('add_row_flg', '#foot', 'true')\"");
	//��Ͽ��ǧ�ܥ���
	$form->addElement("submit","comp_btn","��Ͽ��ǧ���̤�","$disabled");
	//��Ͽ�ϣ˥ܥ���
	$form->addElement("submit","ok_btn","��ϿOK");

	//��󥿥�ID̵��(���ե饤��)Ƚ��
	if($disp_stat == 1 && $online_flg == 'f'){
		//�������ɽ��
		$form->addElement("button","input_form_btn", "�������ɽ��", "onClick=\"javascript:Button_Submit_1('input_form_flg', '#', 'true')\"");
	}

}else if($disp_stat == 2){
	//����ѡ������(FC)

	//��ư����
	$form->addElement("button","input_auto", "��ư����", "onClick=\"javascript:Button_Submit_1('auto_flg', '#', 'true')\"");

	$sql  = "SELECT ";
	$sql .= "    CASE rental_stat ";       //��󥿥���� 
	$sql .= "     WHEN '0'  THEN '��ú�' ";
	$sql .= "     WHEN '10' THEN '�����' ";
	$sql .= "     WHEN '11' THEN '����������' ";
	$sql .= "     WHEN '20' THEN '�����' ";
	$sql .= "     WHEN '21' THEN '������' ";
	$sql .= "     WHEN '22' THEN '����ͽ��' ";
	$sql .= "    END ";
	$sql .= "FROM ";
	$sql .= "    t_rental_d ";
	$sql .= "WHERE ";
	$sql .= "    rental_id = $rental_id;";
	$result = Db_Query($db_con, $sql);

	$stat_flg = false;   //�����¸��Ƚ��ե饰
	while($stat_data = pg_fetch_array($result)){
		if($stat_data[0] == '�����'){
			$stat_flg = true;
		}
	}

	//����Ѥ����뤫Ƚ��
	if($stat_flg == true){
		//����ѡ�����Ѥ�����

		//��������ǧ�ܥ���
		$form->addElement("submit","comp_btn","��������ǧ���̤�","$disabled");
		//�������ϣ˥ܥ���
		$form->addElement("submit","ok_btn","������OK");
	}else{
		//����ѤΤ�

		//�ѹ���ǧ�ܥ���
		$form->addElement("submit","comp_btn","�ѹ���ǧ���̤�","$disabled");
		//�ѹ��ϣ˥ܥ���
		$form->addElement("submit","ok_btn","�ѹ�OK");
	}
}

//TOP��˥塼�ʳ��������ܤ��Ƥ����������ܥ���ɽ��
if($top_menu == NULL){
	//����Ƚ��
	if($group_kind == 1){
		//����

		//���
		//$form->addElement("button","return_btn","�ᡡ��","onClick=\"location.href='".HEAD_DIR."sale/1-2-130.php'\"");
		$form->addElement("button","return_btn","�ᡡ��","onClick=\"location.href='".Make_Rtn_Page("rental")."'\"");
	}else{
		//�ƣ�

		//���
		//$form->addElement("button","return_btn","�ᡡ��","onClick=\"location.href='".FC_DIR."system/2-1-142.php'\"");
		$form->addElement("button","return_btn","�ᡡ��","onClick=\"location.href='".Make_Rtn_Page("rental")."'\"");
	}

}

//��ǧ�����Ѥ����ܥ���
$form->addElement("button","back_btn","�ᡡ��","onClick=\"javascript:history.back()\"");

//����Ƚ��
if($group_kind == 1){
	//����

	//����
	$form->addElement("button","disp_btn","�졡��","onClick=\"location.href='".HEAD_DIR."sale/1-2-130.php'\"");
}else{
	//FC

	//����
	$form->addElement("button","disp_btn","�졡��","onClick=\"location.href='".FC_DIR."system/2-1-142.php'\"");
}

//��Ͽ(�إå�)
$form->addElement("button","input_btn","�С�Ͽ",$g_button_color."onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

$form->addElement("hidden", "hdn_client_id");       //�桼��ID
$form->addElement("hidden", "hdn_shop_id");         //����å�ID
$form->addElement("hidden", "hdn_rental_id");       //��󥿥�ID
$form->addElement("hidden", "hdn_online_no");       //����饤��ե饰
$form->addElement("hidden", "auto_flg");            //��ư���ϥܥ��󲡲��ե饰    
$form->addElement("hidden", "client_search_flg");   //�桼����󥯲����ե饰    
$form->addElement("hidden", "del_row");             //�����
$form->addElement("hidden", "add_row_flg");         //�ɲùԥե饰
$form->addElement("hidden", "max_row");             //����Կ�
$form->addElement("hidden", "goods_search_row");    //���ʥ��������Ϲ�
$form->addElement("hidden", "hdn_top_menu");        //TOP��˥�����Ƚ��ե饰
$form->addElement("hidden", "input_form_flg");      //������ɽ���ե饰
$form->addElement("hidden", "cancel_flg");          //�����åե饰
?>