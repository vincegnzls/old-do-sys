<?php

/*
 *  �ѹ�����
 *  ����            ô����      ����
 *  -----------------------------------------------------------
 *  2006/12/15      suzuki      ��μ�����value���ɲ�
 *  2009/09/09      aoyama-n    �Ͱ���ǽ�ɲ�
*/

/**************************************************
���ܾ���
**************************************************/
//�������Ƚ��
//�ܵ�̾
$form_client[] =& $form->createElement(
		        "text","cd1","","size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onChange=\"javascript:Change_Submit('client_search_flg','#','true','form_client[cd2]')\" onkeyup=\"changeText(this.form,'form_client[cd1]','form_client[cd2]',6)\"".$g_form_option."\""
	        );
$form_client[] =& $form->createElement("static","","","-");
$form_client[] =& $form->createElement(
		        "text","cd2","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onChange=\"javascript:Button_Submit('client_search_flg','#','true')\"".$g_form_option."\""
	        );
$form_client[] =& $form->createElement("text","name","","size=\"34\" $g_text_readonly");
$freeze_data = $form->addGroup( $form_client, "form_client", "");


if($flg == 'add'){
	//�ɲ�
	//�ܵ�̾���
	$form->addElement("link","form_client_link","","./2-1-104.php","�ܵ�̾","
	    onClick=\"return Open_SubWin('../dialog/2-0-250.php',Array('form_client[cd1]','form_client[cd2]','form_client[name]', 'client_search_flg'),500,450,5,1);\""
	);

}else{
	$form->addElement("static","form_client_link","","�ܵ�̾");
	//�ѹ���ʣ���ɲäξ��ϥե꡼��ɽ��
	$freeze_data->freeze();
}


//�������
$state_ary[] =& $form->createElement("radio",NULL,NULL, "�����","1");
$state_ary[] =& $form->createElement("radio",NULL,NULL, "���󡦵ٻ���","2");
$form->addGroup($state_ary, "state", "�������");


//�Ҳ�����̵ͭ
$intro_ary1[] =& $form->createElement("radio",NULL,NULL, "ȯ�����롧�����","2");
$form->addGroup($intro_ary1, "intro_ac_div[]", "�Ҳ������");

$intro_ary2[] =& $form->createElement("radio",NULL,NULL, "ȯ�����롧����","3");
$form->addGroup($intro_ary2, "intro_ac_div[]", "�Ҳ������");

$intro_ary3[] =& $form->createElement("radio",NULL,NULL, "ȯ�����롧�����̤˻���","4");
$form->addGroup($intro_ary3, "intro_ac_div[]", "�Ҳ������");

$intro_ary4[] =& $form->createElement("radio",NULL,NULL, "ȯ�����ʤ�","1");
$form->addGroup($intro_ary4, "intro_ac_div[]", "�Ҳ������");

//�Ҳ���
$form->addElement("text","intro_ac_price","��",'class="money_num" size="7" maxLength="6"'.$g_form_option);
$form->addElement("text","intro_ac_rate" ,"��",'class="num"       size="7" maxLength="3"'.$g_form_option);


//������
Addelement_Date($form,"form_contract_day","������","-");


//�������������ɽ��
if($client_id != NULL){
	//������
	$select_value = NULL;

	$sql  = "SELECT";
	$sql .= "   t_claim.claim_id || ',' || t_claim.claim_div, ";      //������ID,�������ʬ
	$sql .= "   t_client.client_cd1 || '-' ||t_client.client_cd2, ";  //������CD
	$sql .= "   t_client.client_cname,";                              //�����裱
	$sql .= "   t_claim.claim_div ";                                  //�������ʬ
	$sql .= " FROM";
	$sql .= "   t_claim ";
	$sql .= "   INNER JOIN t_client ON t_client.client_id = t_claim.claim_id ";
	$sql .= " WHERE";
	$sql .= "   t_claim.claim_div = '1' ";
	$sql .= " AND ";
	$sql .= "   t_claim.client_id = $client_id ";
	$sql .= "UNION ";
	$sql .= "SELECT";
	$sql .= "   t_claim.claim_id || ',' || t_claim.claim_div, "; //������ID,�������ʬ
	$sql .= "   t_client.client_cd1 || '-' || t_client.client_cd2, ";   //������CD
	$sql .= "   t_client.client_cname,";                         //�����裲
	$sql .= "   t_claim.claim_div ";                             //�������ʬ
	$sql .= " FROM";
	$sql .= "   t_claim ";
	$sql .= "   INNER JOIN t_client ON t_client.client_id = t_claim.claim_id ";
	$sql .= " WHERE";
	$sql .= "   t_claim.claim_div = '2' ";
	$sql .= " AND ";
	$sql .= "   t_claim.client_id = $client_id";
	$sql .= " ORDER BY claim_div ";
	$sql .= ";";
	$result = Db_Query($db_con, $sql); 
	while($claim_list = pg_fetch_array($result)){
		$claim_list[0] = htmlspecialchars($claim_list[0]);
		$claim_list[1] = htmlspecialchars($claim_list[1]);
		$claim_list[2] = htmlspecialchars($claim_list[2]);
		$select_value[$claim_list[0]] = $claim_list[1]." �� ".$claim_list[2];
	}
	$form->addElement('select', 'form_claim','���쥯�ȥܥå���',$select_value, $g_form_option_select);

	//���̹����ե饰
	$form->addElement("hidden","renew_flg","1"); 
}

/**************************************************
//���
**************************************************/
//FC��ľ��Ƚ��
if($group_kind == 2){
	//ľ��
	//�����褬���ꤵ�줿���
	if($client_id != NULL){
		//$daiko_js = "onClick=\"javascript:daiko_checked(); Mult_double_All('$coax',false,'$daiko_coax'); \"";
		$daiko_js = "onClick=\"javascript:daiko_checked(); Act_Request_Rate('$coax',false,'$daiko_coax'); \"";
		//$daiko_js = "onClick=\"javascript:daiko_checked();\"";
		//$daiko_js = "onClick=\"Mult_double_All('$coax',false,'$daiko_coax'); \"";
	}

	//��԰�������å��ܥå���
	$form_daiko = NULL;
	$form_daiko[] =& $form->createElement("radio", NULL, NULL, "���ҽ�󡡡���", "1",$daiko_js);
	$form_daiko[] =& $form->createElement("radio", NULL, NULL, "����饤�����", "2",$daiko_js);
	$form_daiko[] =& $form->createElement("radio", NULL, NULL, "���ե饤�����", "3",$daiko_js);
	$form->addGroup($form_daiko, "daiko_check", "��Զ�ʬ");

	//$act_div_js = "onClick=\"t_price_readonly(this.value); Mult_double4('form_goods_num1','form_sale_price','form_sale_amount','form_trade_price','form_trade_amount','form_issiki','$coax','$daiko_coax'); \"";
	$act_div_js = "onClick=\"t_price_readonly('$coax',this.value,'$daiko_coax'); Act_Request_Rate('$coax',false,'$daiko_coax');\"";

	//�������̵ͭ
	$act_div_ary1[] =& $form->createElement("radio",NULL,NULL, "ȯ�����롧�����","2",$act_div_js);
	$form->addGroup($act_div_ary1, "act_div[]", "��԰�����");
	
	$act_div_ary2[] =& $form->createElement("radio",NULL,NULL, "ȯ�����롧����","3",$act_div_js);
	$form->addGroup($act_div_ary2, "act_div[]", "��԰�����");
	
	$act_div_ary3[] =& $form->createElement("radio",NULL,NULL, "ȯ�����ʤ�","1",$act_div_js);
	$form->addGroup($act_div_ary3, "act_div[]", "��԰�����");

	//�����
	/*
	$form->addElement("text","act_request_price","��",'class="money_num" size="7" maxLength="6"'.$g_form_option
	 ."onKeyup=\"Mult_double4('form_goods_num1','form_sale_price','form_sale_amount','form_trade_price','form_trade_amount','form_issiki','$coax','$daiko_coax');\""
	);
	*/
	$form->addElement("text","act_request_price","��",'class="money_num" size="7" maxLength="6"'.$g_form_option);
	$form->addElement("text","act_request_rate" ,"��",'class="num"       size="7" maxLength="3"'.$g_form_option
	 //."onKeyup=\"Mult_double4('form_goods_num1','form_sale_price','form_sale_amount','form_trade_price','form_trade_amount','form_issiki','$coax','$daiko_coax');\""
	 ."onKeyup=\"Act_Request_Rate('$coax',false,'$daiko_coax');\""
	);


	//�����
	//$form->addElement("text","act_request_price","��",'class="money_num" size="7" maxLength="6"'.$g_form_option);
	//$form->addElement("text","act_request_rate" ,"��",'class="num"       size="7" maxLength="3"'.$g_form_option);

/*
	//�����
	$form->addElement("text","form_daiko_price","",'size="7" maxLength="10"'.$g_form_option." style='text-align:right; ime-mode:disabled;' onKeyup=\"Mult_double4('form_goods_num1','form_sale_price','form_sale_amount','form_trade_price','form_trade_amount','form_issiki','$coax','$daiko_coax');\"");
*/

	//������ ��FC�˥�����
	$form_daiko = NULL;
	$form_daiko[] =& $form->createElement(
		        "text","cd1","","size=\"7\" maxLength=\"6\" style=\"$g_form_style\" onChange=\"javascript:Change_Submit('daiko_search_flg','#','true','form_daiko[cd2]')\" onkeyup=\"changeText(this.form,'form_daiko[cd1]','form_daiko[cd2]',6)\"".$g_form_option."\""
		        );

	$form_daiko[] =& $form->createElement("static","","","-");
	$form_daiko[] =& $form->createElement(
		        "text","cd2","","size=\"4\" maxLength=\"4\" style=\"$g_form_style\" onChange=\"javascript:Button_Submit('daiko_search_flg','#','true')\"".$g_form_option."\""
		        );
	$form_daiko[] =& $form->createElement(
	    "text","name","",
	    "size=\"34\" $g_text_readonly"
	);
	$form->addGroup( $form_daiko, "form_daiko", "");

	//��������
	$form->addElement("link","form_daiko_link","","./2-1-104.php","�����","
	    onClick=\"return Open_SubWin('../../franchise/dialog/2-0-251.php',Array('form_daiko[cd1]','form_daiko[cd2]','form_daiko[name]', 'daiko_search_flg'),500,450,5,'daiko');\""
	);

	//�����谸����
	$form->addElement("textarea","form_daiko_note","",'size="30" maxLength="100" cols="40"'.$g_form_option_area);

}

/**************************************************
�����
**************************************************/
//���쥯�ȥܥå�����
//ABCD��
$array_abcd_week = array(
	"" => "",
	"1" => "A��(4���ֳ�)",
	"2" => "B��(4���ֳ�)",
	"3" => "C��(4���ֳ�)",
	"4" => "D��(4���ֳ�)",
	"5" => "A , C��(2���ֳ�)",
	"6" => "B , D��(2���ֳ�)",
	"21" => "A��(8���ֳ�)",
	"22" => "B��(8���ֳ�)",
	"23" => "C��(8���ֳ�)",
	"24" => "D��(8���ֳ�)",
	
);

//���̤ν�
$array_week = array(
	"" => "",
	"1" => "1",
	"2" => "2",
	"3" => "3",
	"4" => "4"
);

//�����ּ���
$array_while_week = array(
	""   => "",
	"1"  => "1",
	"2"  => "2",
	"3"  => "3",
	"4"  => "4",
	"5"  => "5",
	"6"  => "6",
	"7"  => "7",
	"8"  => "8",
	"9"  => "9",
	"12" => "12",
	"18" => "18",
	"24" => "24"
);

//52���ּ���
$array_while_week2[""] = "";
for($w=1;$w<=52;$w++){
	$array_while_week2["$w"] = $w;
}

//1��29���ܷ���
$array_week_interval[0] = "";
for($i=1;$i<29;$i++){
	$array_week_interval[$i] = $i;
}
$array_week_interval[30] = '����';


//����
$array_week_rday = array(
	"" => "",
	"1" => "��",
	"2" => "��",
	"3" => "��",
	"4" => "��",
	"5" => "��",
	"6" => "��",
	"7" => "��"
);

$need_mark = "<b><font color=\"#ff0000\">��</font></b>";
$readonly = "style=\"border : #ffffff 1px solid; background-color: #ffffff;\" readonly";

//������ʣ�̤ξ��Ϸ���ȯ������ɬ������
if($init_type == "����" || $init_type == "ʣ��"){
    //javascript(���դ˹�碌��������ɽ������)
    $option =" onkeyup=\"date_week(this.form,'form_stand_day[y]','form_stand_day[m]','form_stand_day[d]','form_week_rday4')\"";
    $option.=" onClick=\"date_week(this.form,'form_stand_day[y]','form_stand_day[m]','form_stand_day[d]','form_week_rday4')\"";

	Addelement_Date($form,"form_stand_day","����ȯ����".$need_mark,"-","y","m","d","$option"); 
	Addelement_Date($form,"form_update_day","����ȯ����<b>��</b>","-","y","m","d","disabled");

//����¾�ξ��Ͻ���ȯ������ɬ������
}else{
    //javascript(���դ˹�碌��������ɽ������)
    $option =" onkeyup=\"date_week(this.form,'form_update_day[y]','form_update_day[m]','form_update_day[d]','form_week_rday4')\"";
    $option.=" onClick=\"date_week(this.form,'form_update_day[y]','form_update_day[m]','form_update_day[d]','form_week_rday4')\"";

	Addelement_Date($form,"form_stand_day","����ȯ����<b>��</b>","-","y","m","d","$readonly");
	Addelement_Date($form,"form_update_day","����ȯ����".$need_mark,"-","y","m","d","$option");
}


//����λ��
Addelement_Date($form,"form_contract_eday","����λ��<b>��</b>","-");

//�����(1)
$form->addElement('select', 'form_abcd_week1', '(1)', $array_abcd_week, $g_form_option_select);
$form->addElement('select', 'form_week_rday1', '(1)', $array_week_rday, $g_form_option_select);

//�����(2)
$form->addElement('select', 'form_rday2', '(2)', $array_week_interval, $g_form_option_select);

//�����(3)
$form->addElement('select', 'form_cale_week3', '(3)', $array_week, $g_form_option_select);
$form->addElement('select', 'form_week_rday3', '(3)', $array_week_rday, $g_form_option_select);

//�����(4)
$form->addElement('select', 'form_cale_week4', '(4)', $array_while_week2, $g_form_option_select);
$form->addElement("text","form_week_rday4","(4)",'size="1" maxLength="2" class="Textbox_readonly" readonly');

//�����(5)
$form->addElement('select', 'form_cale_month5', '(5)', $array_while_week, $g_form_option_select);
$form->addElement('select', 'form_week_rday5', '(5)', $array_week_interval, $g_form_option_select);

//�����(6)
$form->addElement('select', 'form_cale_month6', '(6)', $array_while_week, $g_form_option_select);
$form->addElement('select', 'form_cale_week6', '(6)', $array_week, $g_form_option_select);
$form->addElement('select', 'form_week_rday6', '(6)', $array_week_rday, $g_form_option_select);

//������饸���ܥ���
for($i=0;$i<7;++$i){
	$form_round_div1 = "";
	$r_value = $i+1;
	$form_round_div1[] =& $form->createElement("radio", NULL,NULL, "($r_value)", $r_value,"onClick=\"return Check_read('$r_value');\"");
	$form->addGroup($form_round_div1, "form_round_div1[]");
}

/**************************************************
���ô����
**************************************************/
//NO
$form->addElement("text","form_line","�ƥ����ȥե�����",'style="ime-mode: disabled;" size="3" maxLength="3" '.$g_form_option);

//���ô��
$form_c_staff_id = Select_Get($db_con,'round_staff_m');
$form->addElement('select', 'form_c_staff_id1', '���ô��1', $form_c_staff_id, $g_form_option_select);
$form->addElement('select', 'form_c_staff_id2', '���ô��2', $form_c_staff_id, $g_form_option_select);
$form->addElement('select', 'form_c_staff_id3', '���ô��3', $form_c_staff_id, $g_form_option_select);
$form->addElement('select', 'form_c_staff_id4', '���ô��4', $form_c_staff_id, $g_form_option_select);

//���
$form->addElement("text","form_sale_rate1","���Ψ1",'style="ime-mode:disabled;text-align:right;" size="3" maxLength="3"' .$g_form_option);
$form->addElement("text","form_sale_rate2","���Ψ2",'style="ime-mode:disabled;text-align:right;" size="3" maxLength="3"' .$g_form_option);
$form->addElement("text","form_sale_rate3","���Ψ3",'style="ime-mode:disabled;text-align:right;" size="3" maxLength="3"' .$g_form_option);
$form->addElement("text","form_sale_rate4","���Ψ4",'style="ime-mode:disabled;text-align:right;" size="3" maxLength="3"' .$g_form_option);

//��ϩ
$form_route[] =& $form->createElement("text","1","�ƥ����ȥե�����",'style="ime-mode:disabled;" size="1" maxLength="2" onkeyup="changeText(this.form,\'form_route_load[1]\',\'form_route_load[2]\',2)" '.$g_form_option);
$form_route[] =& $form->createElement("text","2","�ƥ����ȥե�����",'style="ime-mode:disabled;" size="1" maxLength="2" '.$g_form_option);
$form->addGroup( $form_route,"form_route_load","form_route_load","-");

//����
$form->addElement("textarea","form_note","",'size="30" maxLength="100" cols="40"'.$g_form_option_area);


/**************************************************
�����
**************************************************/

$f_option_read  = " style=\"color : #000000; border : #ffffff 1px solid; background-color: #ffffff; text-align: right\" readonly";
$g_form_option_num = $g_form_option." style=\"text-align: right\"";

$num = 5;
$type = $g_form_option;
$array_divide = Select_Get($db_con, "divide_con");

for ($i=1; $i<=$num; $i++){

    //aoyama-n 2009-09-17
    //�Ͱ����ʤξ����ֻ���ɽ��
    $font_color = "";
    //�Ͱ����ʤ����Ϥ��줿���
    if($con_data2["hdn_discount_flg"][$i] === 't'){
        $font_color = "color: red; ";
    //���ꥢ��󥯤ޤ��������ꥢ�ܥ��󤬲������줿���
    }elseif($_POST["clear_line"] == $i || $_POST["clear_flg"] == true){
        $font_color = "color: #000000; ";
    //���ϺѤߤ��Ͱ����ʤξ��
    }elseif($con_data2["form_goods_cd1"][$i] == "" && $_POST["hdn_discount_flg"][$i] === 't'){
        $font_color = "color: red; ";
    //�ѹ���ʣ�̤ν��ɽ���ξ��
    }elseif($con_data["hdn_discount_flg"][$i] === 't'){
        $font_color = "color: red; ";
    }else{
        $font_color = "color: #000000; ";
    }

	//�����ʬ
    //aoyama-n 2009-09-17
	#$form->addElement('select', "form_divide[".$i."]", "", $array_divide, $g_form_option_select);
	$form->addElement('select', "form_divide[".$i."]", "", $array_divide, "style=\"$font_color\" $g_form_option_select");

	//�����ӥ��켰�ʥ����å��ܥå�����
	$form->addElement( "checkbox","form_issiki[".$i."]" ,"" ,"","onClick=\"Set_num('$i','$coax','$daiko_coax');\"");

	//�����ӥ�
	$array_serv = Select_Get($db_con, "serv_con");
    //aoyama-n 2009-09-17
	#$form->addElement('select', "form_serv[".$i."]", "", $array_serv, $g_form_option_select);
	$form->addElement('select', "form_serv[".$i."]", "", $array_serv, "style=\"$font_color\" $g_form_option_select");


	//����̾������̾��
	if($_POST["hdn_name_change1"][$i] == '2' || $hdn_name_change[1][$i] == '2'){
		//�Բ�
        //aoyama-n 2009-09-17
		#$form->addElement("text","official_goods_name[".$i."]","����","size=\"42\" $g_text_readonly" );
		$form->addElement("text","official_goods_name[".$i."]","����","size=\"42\" style=\"$font_color\"  $g_text_readonly" );
	}else{
		//��
        //aoyama-n 2009-09-17
		#$form->addElement("text","official_goods_name[".$i."]","����","size=\"42\" maxLength=\"41\" style=\"$style\" $type");
		$form->addElement("text","official_goods_name[".$i."]","����","size=\"42\" maxLength=\"41\" style=\"$font_color\" $type");
	}

	//�ޥ����Ȥ�Ʊ��
	$form->addElement( "checkbox","mst_sync_flg[".$i."]" ,"");

    //aoyama-n 2009-09-17
    //�Ͱ��ե饰
	$form->addElement("hidden","hdn_discount_flg[$i]");

	for($s=1;$s<=3;$s++){

		//��ɼ����
		$form->addElement( "checkbox","form_print_flg".$s."[".$i."]" ,"");

		//���ʥ�����      
        //aoyama-n 2009-09-17
	    $form->addElement(
	        "text","form_goods_cd".$s."[$i]","","size=\"10\" maxLength=\"8\"
	        style=\"$g_form_style;$font_color\" $type
	        onChange=\"c_goods_search(this.form, 'form_goods_cd".$s."', 'goods_search_row', $i$s,'keiyaku')\""
	    );
		

		//����̾��ά�Ρ�
		//�ѹ��Բ�Ƚ��
	    if($_POST["hdn_name_change".$s][$i] == '2' || $hdn_name_change[$s][$i] == '2'){
			//�Բ�
            //aoyama-n 2009-09-17
		    #$form->addElement("text","form_goods_name".$s."[$i]","ά��","size=\"30\" $g_text_readonly" );
		    $form->addElement("text","form_goods_name".$s."[$i]","ά��","size=\"30\" style=\"$font_color\" $g_text_readonly" );
	    }else{
			//��
            //aoyama-n 2009-09-17
		    #$form->addElement("text","form_goods_name".$s."[$i]","ά��","size=\"30\" maxLength=\"20\" style=\"$style\" $type");
		    $form->addElement("text","form_goods_name".$s."[$i]","ά��","size=\"30\" maxLength=\"20\" style=\"$font_color\" $type");
	    }

		//���ʼ���Ƚ��
		if($s == 2){

			//���ξ���
			//FC��ľ��Ƚ��
			if($group_kind == 2){
				//ľ��
				$form->addElement(
			"link","form_search".$s."[".$i."]","","#","����",
			"onClick=\"return Open_SubWin('../dialog/2-0-210.php', Array('form_goods_cd".$s."[$i]','goods_search_row'), 500, 450,7,$client_h_id,$i$s,'keiyaku','daiko_check');\""
		);
			}else{
				//FC
				$form->addElement(
			"link","form_search".$s."[".$i."]","","#","����",
			"onClick=\"return Open_SubWin('../dialog/2-0-210.php', Array('form_goods_cd".$s."[$i]','goods_search_row'), 500, 450,7,$client_h_id,$i$s,'keiyaku');\""
		);
			}
		}else{
			//�����ƥࡦ������
			//FC��ľ��Ƚ��
			if($group_kind == 2){
				//ľ��
				$form->addElement(
				"link","form_search".$s."[".$i."]","","#","����",
				"onClick=\"return Open_SubWin('../dialog/2-0-210.php', Array('form_goods_cd".$s."[$i]','goods_search_row'), 500, 450,6,$client_h_id,$i$s,'keiyaku','daiko_check');\""
			);
			}else{
				//FC
				$form->addElement(
				"link","form_search".$s."[".$i."]","","#","����",
				"onClick=\"return Open_SubWin('../dialog/2-0-210.php', Array('form_goods_cd".$s."[$i]','goods_search_row'), 500, 450,6,$client_h_id,$i$s,'keiyaku');\""
			);
			}
		}

		//���ʿ�
		if($s == 1){
			/*
			//FC��ľ��Ƚ��
			if($group_kind == 2){
				//ľ��

				//�����ƥ�
			 	$form->addElement(
		            "text","form_goods_num".$s."[$i]","",
		            "class=\"money_num\" size=\"6\" maxLength=\"5\" 
		            onKeyup=\"Mult_double3('form_goods_num1[$i]','form_sale_price[$i][1]','form_sale_price[$i][2]','form_sale_amount[$i]','form_trade_price[$i][1]','form_trade_price[$i][2]','form_trade_amount[$i]','form_issiki[$i]','$coax',false,$i,'','$daiko_coax');\"
		            style=\"$g_form_style;$style\" $type"
		        );
			}else{
				//�ƣ�
			*/
			//�����ƥ����
            //aoyama-n 2009-09-17
			if($group_kind == 2){
				$mult_double = "Act_Request_Rate('$coax',false,'$daiko_coax','$i');";

			}else{
				$mult_double = "Mult_double2('form_goods_num1[$i]','form_sale_price[$i][1]','form_sale_price[$i][2]','form_sale_amount[$i]','form_trade_price[$i][1]','form_trade_price[$i][2]','form_trade_amount[$i]','form_issiki[$i]','$coax',false);";
			}

			 	$form->addElement(
		            "text","form_goods_num".$s."[$i]","",
		            "class=\"money_num\" size=\"6\" maxLength=\"5\" 
		            onKeyup=\"$mult_double\"
		            style=\"$g_form_style; $font_color\" $type"
		        );
			//}
		}else{
			//�����ʡ�����
            //aoyama-n 2009-09-17
			$form->addElement(
	            "text","form_goods_num".$s."[$i]","",
	            " size=\"3\" maxLength=\"3\" 
	            style=\"text-align:right; $g_form_style; $font_color\" $type"
	        );
		}

		//����ID
		$form->addElement("hidden","hdn_goods_id".$s."[$i]");
		//��̾�ѹ��ե饰
		$form->addElement("hidden","hdn_name_change".$s."[$i]");

		/**************************************************
		���顼ɽ���������
		**************************************************/
		//�����ʥ��顼ɽ��
		$form->addElement("text","error_goods_num3[".$i."]","","size=\"17\" maxLength=\"10\"");
		//���ξ��ʥ��顼ɽ��
		$form->addElement("text","error_goods_num2[".$i."]","","size=\"17\" maxLength=\"10\"");

	}

	//���ꥢ��󥯺���
	$form->addElement(
		"link","clear_line[".$i."]","",$_SERVER[PHP_SELF],"���ꥢ","onClick=\"Javascript:Clear_Line_Data('$i');return false;\""
	);

	/*
	//FC��ľ��Ƚ��
	if($group_kind == 2){
		//ľ��

		//��Ԥξ��ϡ��ɤ߼��ʤΤ��طʤ򲫿��ˤ��ʤ�
		if($daiko_div != 1){
			$type = NULL;
		}

		//�Ķȸ���
		$form_cost_price[$i][] =& $form->createElement(
		        "text","1","",
		        "size=\"9\" maxLength=\"8\"
		        class=\"money\"
		        onKeyup=\"Mult_double3('form_goods_num1[$i]','form_sale_price[$i][1]','form_sale_price[$i][2]','form_sale_amount[$i]','form_trade_price[$i][1]','form_trade_price[$i][2]','form_trade_amount[$i]','form_issiki[$i]','$coax',false,$i,'','$daiko_coax');\"
		        style=\"$g_form_style;text-align: right; $style\"
		        $type"
		    );
		    $form_cost_price[$i][] =& $form->createElement(
		        "text","2","","size=\"1\" maxLength=\"2\" 
		        onKeyup=\"Mult_double3('form_goods_num1[$i]','form_sale_price[$i][1]','form_sale_price[$i][2]','form_sale_amount[$i]','form_trade_price[$i][1]','form_trade_price[$i][2]','form_trade_amount[$i]','form_issiki[$i]','$coax',true,$i,'','$daiko_coax');\"
		        style=\"$g_form_style;text-align: left; $style\"
		        $type"
		    );
	    $form->addGroup( $form_cost_price[$i], "form_trade_price[$i]", "",".");

		//�Ķȸ����ʳ��Ϸ����ʬ�ˤ�äƥե����ब�Ѥ��ʤ�
		$type = $g_form_option;

		//������׳�
	    $form->addElement(
	        "text","form_trade_amount[$i]","",
	        "size=\"17\" maxLength=\"10\" 
	        style=\"color : #000000; 
	        border : #ffffff 1px solid; 
	        background-color: #ffffff; 
	        text-align: right\" readonly'"
	    );

		//���ñ��
		$form_sale_price[$i][] =& $form->createElement(
		        "text","1","",
		        "size=\"9\" maxLength=\"8\"
		        class=\"money\"
		        onKeyup=\"Mult_double3('form_goods_num1[$i]','form_sale_price[$i][1]','form_sale_price[$i][2]','form_sale_amount[$i]','form_trade_price[$i][1]','form_trade_price[$i][2]','form_trade_amount[$i]','form_issiki[$i]','$coax',false,$i,'','$daiko_coax');\"
		        style=\"$g_form_style;text-align: right; $style\"
		        $type"
		    );
		    $form_sale_price[$i][] =& $form->createElement(
		        "text","2","","size=\"1\" maxLength=\"2\" 
		        onKeyup=\"Mult_double3('form_goods_num1[$i]','form_sale_price[$i][1]','form_sale_price[$i][2]','form_sale_amount[$i]','form_trade_price[$i][1]','form_trade_price[$i][2]','form_trade_amount[$i]','form_issiki[$i]','$coax',true,$i,'','$daiko_coax');\"
		        style=\"$g_form_style;text-align: left; $style\"
		        $type"
		    );
	    $form->addGroup( $form_sale_price[$i], "form_sale_price[$i]", "",".");

		//����׳�
		$form->addElement("text","form_sale_amount[$i]","",'class="amount"');

		//������
		$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>�ʤ�</font>","1");
		//�Ҳ���������Ƚ��
		if($client_ac_id == NULL){
			//�����������Բ�
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>�����</font>","2","disabled");
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>����</font>","3","disabled");
			$form->addElement("text","form_account_price[$i]","",'size="7" maxLength="6"'.$g_form_option." class=\"money_num\" style='text-align:right; ime-mode:disabled; background-color:gainsboro;' disabled");
	        $form->addElement("text","form_account_rate[$i]","",'size="7" maxLength="6" '.$g_form_option." style='text-align:right; ime-mode:disabled; background-color:gainsboro;' disabled");
		}else{
			//�����������
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>�����</font>","2");
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>����</font>","3");
			$form->addElement("text","form_account_price[$i]","",'size="7" maxLength="6"'.$g_form_option." class=\"money_num\" style='text-align:right; ime-mode:disabled;'");		  $form->addElement("text","form_account_rate[$i]","",'size="7" maxLength="6" '.$g_form_option." style='text-align:right; ime-mode:disabled;'");
		}
		$form->addGroup($form_aprice_div0[$i], "form_aprice_div[$i]","","<br>");

		//�������ζ���
		$text = NULL;
		$text[] =& $form->createElement(
	        "text","name","",
	        "size=\"7\" maxLength=\"10\" 
	        style=\"color : #000000; 
	        border : #ffffff 1px solid; 
	        background-color: #ffffff; 
	        text-align: right\" readonly'"
	    );
		$form->addGroup($text, "form_br","","");

/*
		$form->addElement("text","form_account_price[$i]","",'size="7" maxLength="6"'.$g_form_option." style='text-align:right; ime-mode:disabled;' onKeyup=\"Mult_double4('form_goods_num1','form_sale_price','form_sale_amount','form_trade_price','form_trade_amount','form_issiki','$coax');\" ");
		$form->addElement("text","form_account_rate[$i]","",'size="7" maxLength="6" '.$g_form_option." style='text-align:right; ime-mode:disabled;' onKeyup=\"Mult_double4('form_goods_num1','form_sale_price','form_sale_amount','form_trade_price','form_trade_amount','form_issiki','$coax');\" ");
*/

	//}else{

		//��Ԥξ�硢�����δݤ��ʬ��JS������
		if($daiko_check != "1" ){
			$d_coax = $daiko_coax;
		}

		//�ƣ�
		//�Ķȸ���
		$form_cost_price[$i][] =& $form->createElement(
		        "text","1","",
		        "size=\"9\" maxLength=\"8\"
		        class=\"money\"
		        onKeyup=\"$mult_double\"
		        style=\"$g_form_style;text-align: right; $font_color\"
		        $type"
		    );
		    $form_cost_price[$i][] =& $form->createElement(
		        "text","2","","size=\"1\" maxLength=\"2\" 
		        onKeyup=\"$mult_double\"
		        style=\"$g_form_style;text-align: left; $font_color\"
		        $type"
		    );
	    $form->addGroup( $form_cost_price[$i], "form_trade_price[$i]", "",".");

		//������׳�
	    $form->addElement(
	        "text","form_trade_amount[$i]","",
	        "size=\"17\" maxLength=\"10\" 
	        style=\"$font_color 
	        border : #ffffff 1px solid; 
	        background-color: #ffffff; 
	        text-align: right\" readonly'"
	    );

		//���ñ��
		$form_sale_price[$i][] =& $form->createElement(
		        "text","1","",
		        "size=\"9\" maxLength=\"8\"
		        class=\"money\"
		        onKeyup=\"$mult_double\"
		    style=\"$font_color\"
		        $type"
		    );
		    $form_sale_price[$i][] =& $form->createElement(
		        "text","2","","size=\"1\" maxLength=\"2\" 
		        onKeyup=\"$mult_double\"
		        style=\"$g_form_style;text-align: left; $font_color\"
		        $type"
		    );
	    $form->addGroup( $form_sale_price[$i], "form_sale_price[$i]", "",".");

		//����׳�
        //aoyama-n 2009-09-17   
		#$form->addElement("text","form_sale_amount[$i]","",'class="amount"');
		$form->addElement("text","form_sale_amount[$i]","","class=\"amount\" style=\"$font_color\" $g_text_readonly");

		//������
		$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>�ʤ�</font>","1");
		//�Ҳ���������Ƚ��
		if($client_ac_id == NULL){
			//�����������Բ�
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>�����</font>","2","disabled");
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>����</font>","3","disabled");
			$form->addElement("text","form_account_price[$i]","",'size="7" maxLength="6"'.$g_form_option." class=\"money_num\" style='text-align:right; ime-mode:disabled; background-color:gainsboro;' disabled");
	        $form->addElement("text","form_account_rate[$i]","",'size="7" maxLength="6" '.$g_form_option." style='text-align:right; ime-mode:disabled; background-color:gainsboro;' disabled");
		}else{
			//�����������
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>�����</font>","2");
			$form_aprice_div0[$i][] =& $form->createElement("radio",NULL,NULL, "<font color=#555555>����</font>","3");
			$form->addElement("text","form_account_price[$i]","",'size="7" maxLength="6"'.$g_form_option." class=\"money_num\" style='text-align:right; ime-mode:disabled;'");
	        $form->addElement("text","form_account_rate[$i]","",'size="7" maxLength="6" '.$g_form_option." style='text-align:right; ime-mode:disabled;'");
		}
		$form->addGroup($form_aprice_div0[$i], "form_aprice_div[$i]","","<br>");

		//�������ζ���
		$text = NULL;
		$text[] =& $form->createElement(
	        "text","name","",
	        "size=\"7\" maxLength=\"10\" 
	        style=\"color : #000000; 
	        border : #ffffff 1px solid; 
	        background-color: #ffffff; 
	        text-align: right\" readonly'"
	    );
		$form->addGroup($text, "form_br","","");

	//}
	
		//�����껦��
		$form_ad_offset_radio = NULL;
		$form_ad_offset_radio[$i][] =& $form->createElement("radio", NULL, NULL, "�ʤ�", "1", "onClick=\"ad_offset_radio_disable($i);\"");
		$form_ad_offset_radio[$i][] =& $form->createElement("radio", NULL, NULL, "����", "2", "onClick=\"ad_offset_radio_disable($i);\"");
		$form->addGroup($form_ad_offset_radio[$i], "form_ad_offset_radio[$i]", "�����껦�ե饰");
		$form->addElement("text", "form_ad_offset_amount[$i]", "", 
			"size=\"11\" maxLength=\"9\" ".$g_form_option." class=\"money\" style='text-align:right; ime-mode:disabled; 
'"
		);

	/*
	//����
	$form->addElement(
		"link","form_breakdown[$i]","","#","����",
		"onClick=\"javascript:return Submit_Page2('./2-1-116.php?flg=$flg&client_id=$client_id&contract_id=$get_con_id&break_row=$i'); \""
	);
	*/

}

$form->addElement("hidden", "goods_search_row");    //���ʥ��������Ϲ�    
$form->addElement("hidden", "entry_flg");           //��Ͽ�ܥ��󲡲��ե饰    
$form->addElement("hidden", "delete_flg");          //����ܥ��󲡲��ե饰    
$form->addElement("hidden", "hdn_client_id");       //������
$form->addElement("hidden", "hdn_daiko_id");        //���ID
$form->addElement("hidden", "hdn_flg");             //�ɲá�����Ƚ�̥ե饰
$form->addElement("hidden", "hdn_con_id");          //�������ID
$form->addElement("hidden", "client_search_flg");   //�����踡���ե饰
$form->addElement("hidden", "daiko_search_flg");    //�����踡���ե饰
$form->addElement("hidden", "hdn_return_flg");      //���ɽ��Ƚ��ե饰
$form->addElement("hidden", "hdn_get_flg");         //�����פ����ܥ�������Ƚ��ե饰
$form->addElement("hidden", "hdn_c_check_flg");     //��§������Ƚ��ե饰
$form->addElement("hidden", "copy_value_flg");      //ʣ���ɲäμ������إܥ��󲡲�Ƚ��ե饰
$form->addElement("hidden", "clear_flg");           //���ꥢȽ��ե饰
$form->addElement("hidden", "clear_line");          //���ꥢ�����
$form->addElement("hidden", "duplicat_ok");         //��ʣ��ɼOK
$form->addElement("hidden", "advance_ok");          //�����껦OK

//�ʲ��ϲ��̤�ɽ�����ʤ��ʥ��顼ɽ���Τ���˺�����
$form->addElement("text","sale_rate_sum","");
$form->addElement("text","staff_uniq","");
$form->addElement("text","goods_enter","");
$form->addElement("text","hensoku_err","");

/************************************************/

//�ܥ���
$form->addElement("button","copy_button","���ԡ�","");
$form->addElement("button","entry_button","�С�Ͽ","onClick=\"return Dialogue_2('��Ͽ���ޤ���','2-1-104.php?client_id=$client_id',true,'entry_flg');\" $disabled");
$form->addElement("button","form_back","�ᡡ��","onClick=\"location.href='./2-1-115.php?client_id=$client_id&return_flg=true&get_flg=$get_flg'\"");

//�ѹ����̤ξ��ɽ��
if($flg == 'chg'){
	$form->addElement("button","delete_button","���","onClick=\"return Dialogue_2('������ޤ���','2-1-104.php?client_id=$client_id',true,'delete_flg');\" $del_disabled");
}

$form->addElement("submit","hyouji","ɽ����","");

$form->addElement("button","clear_button","�����ꥢ","onClick=\"insert_row('clear_flg');\"");

//ʣ���ɲäξ��ɽ��
if($flg == 'copy'){
	//���إܥ���
	if($next_id != null){
	    $form->addElement("button","next_button","������","onClick=\"location.href='./2-1-104.php?flg=$flg&client_id=$client_id&contract_id=$next_id&return_flg=true'\"");
	}else{
	    $form->addElement("button","next_button","������","disabled");
	}
	//���إܥ���
	if($back_id != null){
	    $form->addElement("button","back_button","������","onClick=\"location.href='./2-1-104.php?flg=$flg&client_id=$client_id&contract_id=$back_id&return_flg=true'\"");
	}else{
	    $form->addElement("button","back_button","������","disabled");
	}
}

//�ѹ�������
$form->addElement("button","change_button","�ѹ�������","onClick=\"location.href='2-1-111.php'\"");
//�������(�إå���)
//$form->addElement("button","all_button","�������","onClick=\"location.href='2-1-114.php'\"");
//��Ͽ(�إå���)
$form->addElement("button","new_button","�С�Ͽ",$g_button_color."onClick=\"location.href='2-1-104.php?flg=add'\"");


//�����⽸�ץܥ���
$form->addElement("submit","form_ad_sum_btn","������");
//������Ĺ�
$form->addElement("static", "form_ad_rest_price","������Ĺ�");


/***************
��§�����
****************/
$form->addElement(
    "link","form_irr_day","","#","��§��",
	"onClick=\"javascript:return Submit_Page2('./2-1-105.php?flg=$flg&client_id=$client_id&contract_id=$get_con_id'); \""
);

/****************************/
//�������(��������)
/****************************/
/*
//������Ͽ�ιԿ�ʬ
for($i=1;$i<=5;$i++){
	//������Ͽ�ιԿ�ʬ
	for($j=1;$j<=5;$j++){
		//���ʥ�����
		$form->addElement("hidden","break_goods_cd[$i][$j]","");
		//����̾
		$form->addElement("hidden","break_goods_name[$i][$j]","");
		//���ʿ�
		$form->addElement("hidden","break_goods_num[$i][$j]","");
		//����ID
		$form->addElement("hidden","hdn_bgoods_id[$i][$j]","");
		//��̾�ѹ��ե饰
		$form->addElement("hidden","hdn_bname_change[$i][$j]","");

		//�Ķȸ���
		$form->addElement("hidden","break_trade_price[$i][$j][1]","");
		$form->addElement("hidden","break_trade_price[$i][$j][2]","");
		//�������
		$form->addElement("hidden","break_trade_amount[$i][$j]","");
		//���ñ��
		$form->addElement("hidden","break_sale_price[$i][$j][1]","");
		$form->addElement("hidden","break_sale_price[$i][$j][2]","");
		//�����
		$form->addElement("hidden","break_sale_amount[$i][$j]","");
	}
}
*/

//��Ǻ��
$form->addElement("button","form_back_trade","�ᡡ��","onClick=\"location.href='./2-1-104.php?flg=add'\"");

?>