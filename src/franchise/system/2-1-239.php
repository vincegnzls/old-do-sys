<?php
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2006/11/11      03-027      kajioka-h   ������ϥ���饤����Ԥ���
 *  2009/09/22                  hashimoto-y �Ͱ������ʤ��ֻ�ɽ��
 *
 */

$page_title = "��԰�������ʼ������ѡ�";

//�Ķ�����ե�����
require_once("ENV_local.php");

//HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB��³
$db_con = Db_Connect();

// ���¥����å�
$auth       = Auth_Check($db_con);

/****************************/
//�����ѿ�����
/****************************/
$get_flg      = $_GET["get_flg"];        //���ܸ�Ƚ��ե饰
$back_display = $_GET["back_display"];   //ͽ�����٤����ܸ�
$array_id     = $_GET["aord_id_array"];  //�����������Ƥμ���ID
$aord_id      = $_GET["aord_id"];        //����ID
$shop_id      = $_SESSION["client_id"];  //��������ID

//���󥷥ꥢ�饤����
$array_id = stripslashes($array_id);
$array_id = urldecode($array_id);
$array_id = unserialize($array_id);


//ľ��Ƚ��
$rank_cd  = $_SESSION["rank_cd"];
$sql  = "SELECT group_kind FROM t_rank WHERE rank_cd = '$rank_cd' AND group_kind = '3';";
$result = Db_Query($db_con, $sql);
Get_Id_Check($result);

//����Ƚ��
Get_ID_Check3($aord_id);
Get_ID_Check3($array_id);

//���ꥢ�饤����
$array_id = serialize($array_id);
$array_id = urlencode($array_id);

/****************************/
//�ե��������
/****************************/
//���ܥ���������Ƚ��
switch($get_flg){
	case 'cal':
		//ͽ������

		$form->addElement("button","form_back","�ᡡ��","onClick=\"location.href='".FC_DIR."sale/2-2-106.php?aord_id[0]=$aord_id&aord_id_array=$array_id&back_display=$back_display'\"");
		break;
	case 'reason':
		//ͽ��ǡ�������
		$form->addElement("button","form_back","�ᡡ��","onClick=\"location.href='".FC_DIR."sale/2-2-107.php?aord_id=$aord_id&back_display=$back_display&aord_id_array=$array_id'\"");
		break;
}

/****************************/
//�ǡ���ɽ�����ʺ�������
/****************************/
$sql  = "SELECT DISTINCT ";
$sql .= "    t_contract.trust_line, ";              //��No0
$sql .= "    t_contract.act_request_day,";          //������1
$sql .= "    t_contract.route,";                    //��ϩ2
$sql .= "    CASE t_con_info.divide ";              //�����ʬ3
$sql .= "         WHEN '01' THEN '��ԡ���'";
$sql .= "         WHEN '02' THEN '����'";
$sql .= "         WHEN '03' THEN '��󥿥�'";
$sql .= "         WHEN '04' THEN '�꡼��'";
$sql .= "         WHEN '05' THEN '����'";
$sql .= "         WHEN '06' THEN '����¾'";
$sql .= "    END,";
$sql .= "    CASE t_con_info.serv_pflg ";           //�����ӥ�����4
$sql .= "         WHEN 't' THEN '��'";
$sql .= "         WHEN 'f' THEN '��'";
$sql .= "    END,";
$sql .= "    t_serv.serv_name,";                    //�����ӥ�̾5

$sql .= "    CASE t_con_info.goods_pflg ";          //�����ƥ����6
$sql .= "         WHEN 't' THEN '��'";
$sql .= "         WHEN 'f' THEN '��'";
$sql .= "    END,";
$sql .= "    t_con_info.goods_name,";               //�����ƥ�̾��ά�Ρ�7
$sql .= "    t_con_info.set_flg,";                  //�켰�ե饰8
$sql .= "    t_con_info.num,";                      //����9

$sql .= "    t_con_info.trust_cost_price,";        //�Ķȸ���10
$sql .= "    t_con_info.sale_price,";               //���ñ��11
$sql .= "    t_con_info.trust_cost_amount,";       //�Ķȶ��12
$sql .= "    t_con_info.sale_amount,";              //�����13

$sql .= "    t_con_info.egoods_name,";              //������̾14
$sql .= "    t_con_info.egoods_num, ";              //�����ʿ���15

$sql .= "    t_con_info.rgoods_name,";              //����̾16
$sql .= "    t_con_info.rgoods_num,";               //���ο���17

$sql .= "    t_contract.act_request_rate,";         //������18
$sql .= "    t_contract.request_state, ";           //��������19   
$sql .= "    t_contract.round_div,";                //����ʬ20
$sql .= "    t_contract.cycle,";                    //����21
$sql .= "    t_contract.cycle_unit,";               //����ñ��22
$sql .= "    CASE t_contract.cale_week ";           //��̾(1-4)23
$sql .= "        WHEN '1' THEN ' ��1'";
$sql .= "        WHEN '2' THEN ' ��2'";
$sql .= "        WHEN '3' THEN ' ��3'";
$sql .= "        WHEN '4' THEN ' ��4'";
$sql .= "    END,";
$sql .= "    CASE t_contract.abcd_week ";           //��̾(ABCD)24
$sql .= "        WHEN '1' THEN 'A(4���ֳ�)��'";
$sql .= "        WHEN '2' THEN 'B(4���ֳ�)��'";
$sql .= "        WHEN '3' THEN 'C(4���ֳ�)��'";
$sql .= "        WHEN '4' THEN 'D(4���ֳ�)��'";
$sql .= "        WHEN '5' THEN 'A,C(2���ֳ�)��'";
$sql .= "        WHEN '6' THEN 'B,D(2���ֳ�)��'";
$sql .= "        WHEN '21' THEN 'A(8���ֳ�)��'";
$sql .= "        WHEN '22' THEN 'B(8���ֳ�)��'";
$sql .= "        WHEN '23' THEN 'C(8���ֳ�)��'";
$sql .= "        WHEN '24' THEN 'D(8���ֳ�)��'";
$sql .= "    END,";
$sql .= "    t_contract.rday, ";                    //������25
$sql .= "    CASE t_contract.week_rday ";           //��������26
$sql .= "        WHEN '1' THEN ' ����'";
$sql .= "        WHEN '2' THEN ' ����'";
$sql .= "        WHEN '3' THEN ' ����'";
$sql .= "        WHEN '4' THEN ' ����'";
$sql .= "        WHEN '5' THEN ' ����'";
$sql .= "        WHEN '6' THEN ' ����'";
$sql .= "        WHEN '7' THEN ' ����'";
$sql .= "    END,";
$sql .= "    t_contract.stand_day,";                //��ȴ����27
$sql .= "    t_contract.last_day,";                 //�ǽ������28

$sql .= "    '1:' || t_staff1.staff_name || ";      //ô���ԣ������Ψ��29
$sql .= "    '(' || t_staff1.sale_rate || '%)',"; 
$sql .= "    '2:' || t_staff2.staff_name || ";      //ô���ԣ������Ψ��30
$sql .= "    '(' || t_staff2.sale_rate || '%)',"; 
$sql .= "    '3:' || t_staff3.staff_name || ";      //ô���ԣ������Ψ��31
$sql .= "    '(' || t_staff3.sale_rate || '%)',"; 
$sql .= "    '4:' || t_staff4.staff_name || ";      //ô���ԣ������Ψ��32
$sql .= "    '(' || t_staff4.sale_rate || '%)',"; 
$sql .= "    t_contract.trust_note,";               //����33
$sql .= "    t_contract.contract_id,";              //�������ID34
$sql .= "    t_contract.client_id, ";               //������ID35
$sql .= "    t_con_info.con_info_id, ";             //��������ID36
$sql .= "    t_client.client_cname,";               //������̾ 37
$sql .= "    t_con_info.line, ";                    //�������ƹ� 38
$sql .= "    t_con_info.official_goods_name, ";     //�����ƥ�̾�������� 39
$sql .= "    t_contract.act_div,";                  //�������ʬ 40
$sql .= "    t_contract.trust_sale_amount,";        //�����41
$sql .= "    t_contract.act_request_rate, ";         //������ʡ��42

#2009-09-22 hashimoto-y
$sql .= "    t_item.discount_flg ";                  //�Ͱ����ե饰43

$sql .= "FROM "; 
$sql .= "    t_con_info ";

$sql .= "    INNER JOIN t_contract ON t_contract.contract_id = t_con_info.contract_id ";
$sql .= "    INNER JOIN t_client ON t_contract.client_id = t_client.client_id ";

$sql .= "    LEFT JOIN t_serv ON t_serv.serv_id = t_con_info.serv_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '0'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff1 ON t_staff1.contract_id = t_contract.contract_id ";
 
$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '1'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff2 ON t_staff2.contract_id = t_contract.contract_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '2'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff3 ON t_staff3.contract_id = t_contract.contract_id ";

$sql .= "    LEFT JOIN ";
$sql .= "        (SELECT ";
$sql .= "             t_con_staff.contract_id,";
$sql .= "             t_staff.staff_name,";
$sql .= "             t_con_staff.sale_rate ";
$sql .= "         FROM ";
$sql .= "             t_con_staff ";
$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
$sql .= "         WHERE ";
$sql .= "             t_con_staff.staff_div = '3'";
$sql .= "         AND ";
$sql .= "             t_con_staff.sale_rate != '0'";
$sql .= "        )AS t_staff4 ON t_staff4.contract_id = t_contract.contract_id ";

#2009-09-22 hashimoto-y
$sql .= "    LEFT JOIN t_goods AS t_item ON t_item.goods_id = t_con_info.goods_id ";

$sql .= "WHERE "; 
$sql .= "    t_contract.trust_id = $shop_id "; 
$sql .= "AND ";
$sql .= "    t_contract.contract_div = '2' ";

$sql .= "ORDER BY ";
$sql .= "    t_contract.request_state,";
$sql .= "    t_contract.trust_line,";
$sql .= "    t_contract.contract_id,";
$sql .= "    t_con_info.line;";
$result = Db_Query($db_con, $sql); 
$disp_data = Get_Data($result);

//�ǡ���¸��Ƚ��
if($disp_data == NULL){
	//�ǡ�����¸�ߤ��ʤ����ˡ�ɽ���������١��ե饰�ˤ�ä�ɽ�������ѹ�
	$early_flg = true;
}

/****************************/
//�ǡ���ɽ�������ѹ�
/****************************/
$row = 0;          //�Կ��������
for($i=0;$i<count($disp_data);$i++){

	//��ϩ�����ѹ�
	if($disp_data[$i][2] != NULL){
		$disp_data[$i][2] = str_pad($disp_data[$i][2], 4, 0, STR_POS_LEFT);
		$route1 = substr($disp_data[$i][2],0,2);
		$route2 = substr($disp_data[$i][2],2,2);
		$disp_data[$i][2] = $route1."-".$route2;
	}


	//�������
	//�����
	if($disp_data[$i][40] == "2"){
		$disp_data[$i][18] = Minus_Numformat($disp_data[$i][41])."��<br>(�����)";

	//��
	}elseif($disp_data[$i][40] == "3"){
		$disp_data[$i][18] = Minus_Numformat($disp_data[$i][41])."��<br>(".$disp_data[$i][42]."%)";

	//ȯ�����ʤ�
	}else{
		$disp_data[$i][18] = "̵��";
	}

	//������ɽ��Ƚ��
	//$disp_data[$i][18] = $disp_data[$i][18]."%";

	//����
	$disp_data[$i][9] = my_number_format($disp_data[$i][9]);
	$disp_data[$i][15] = my_number_format($disp_data[$i][15]);
	$disp_data[$i][17] = my_number_format($disp_data[$i][17]);
	//ñ�������ѹ�
	for($c=10;$c<=11;$c++){
		$disp_data[$i][$c] = number_format($disp_data[$i][$c],2);
	}
	//��۷����ѹ�
	for($c=12;$c<=13;$c++){
		$disp_data[$i][$c] = number_format($disp_data[$i][$c]);
	}
	

	//����������ѹ�
	if($disp_data[$i][20] == "1"){
		//���
		$round_data[$i] = $disp_data[$i][24].$disp_data[$i][26];
	}else if($disp_data[$i][20] == "2"){
		//���
		$date_data = substr($disp_data[$i][27],0,7);

		if($disp_data[$i][25] == "30"){
			$round_data[$i] = "��� ���� <br>(".$date_data.")";
		}else{
			$round_data[$i] = "��� ".$disp_data[$i][25]."�� <br>(".$date_data.")";
		}
	}else if($disp_data[$i][20] == "3"){
		//���
		$date_data = substr($disp_data[$i][27],0,7);

		$round_data[$i] = "���".$disp_data[$i][23].$disp_data[$i][26]."<br>(".$date_data.")";
	}else if($disp_data[$i][20] == "4"){
		//���
		$round_data[$i] = $disp_data[$i][21]."���ּ�����".$disp_data[$i][26]."<br>(".$disp_data[$i][27].")";
	}else if($disp_data[$i][20] == "5"){
		//���
		$date_data = substr($disp_data[$i][27],0,7);
		if($disp_data[$i][25] == "30"){
			$round_data[$i] = $disp_data[$i][21]."��������� ���� <br>(".$date_data.")";
		}else{
			$round_data[$i] = $disp_data[$i][21]."��������� ".$disp_data[$i][25]."�� <br>(".$date_data.")";
		}
	}else if($disp_data[$i][20] == "6"){
		//���
		$date_data = substr($disp_data[$i][27],0,7);

		$round_data[$i] = $disp_data[$i][21]."��������� ".$disp_data[$i][23].$disp_data[$i][26]."<br>(".$date_data.")";
	}else if($disp_data[$i][20] == "7"){
		//���
		$round_data[$i] = "��§��<br>(�ǽ���:".$disp_data[$i][28].")";
	}

	//���ô���������Ψɽ��Ƚ��
	for($c=29;$c<=32;$c++){
		//����Ƚ��
		if(!ereg("[0-9]",$disp_data[$i][$c])){
			//�ͤ����Ϥ���Ƥ��ʤ����ϡ�NULL
			$disp_data[$i][$c] = NULL;
		}else{
			//�ͤ����Ϥ���Ƥ�����ϡ��ᥤ��ʳ��ϲ��Ԥ��ɲ�
			if($c!=29){
				$disp_data[$i][$c] = "<br>".$disp_data[$i][$c];
			}
		}
	}

	//��No.�ѹ�Ƚ��
	if($disp_data[$i][34] != $line_num){
		//�����Ƚ��
		if($i != 0){
			//������Υǡ������Կ���������ɲ�
			$disp_data[$i-$row][101] = $row;

			//�Ԥ��طʿ�����
			if($color_flg == true){
				//�����Фˤ���
				$disp_data[$i-$row][100] = true;
				$color_flg = false;
			}else{
				//������ˤ���
				$disp_data[$i-$row][100] = false;
				$color_flg = true;
			}

			//���ι�No.�򥻥å�
			$line_num = $disp_data[$i][34];
			$row = 0;
		}else{
			//����ܤξ��ϡ���No.�򥻥å�
			$line_num = $disp_data[$i][34];
		}
	}

	//�Ԥ��طʿ�����
	if($color_flg == true){
		//�����Фˤ���
		$disp_data[$i][100] = true;
	}else{
		//������ˤ���
		$disp_data[$i][100] = false;
	}

	$row++;

	/*
	//��������Ƚ��
	$sql  = "SELECT con_info_id FROM t_con_detail WHERE con_info_id = ".$disp_data[$i][36].";";
	$result = Db_Query($db_con, $sql);
	$row_num = pg_num_rows($result);
	if(1 <= $row_num){
		//�������ɽ��
		$disp_data[$i][41] = true;
	}else{
		//���������ɽ��
		$disp_data[$i][41] = false;
	}

	*/

}

//�Ǹ�ιԿ��򥻥å�
$disp_data[$i-$row][101] = $row;

//�Ԥ��طʿ�����
if($color_flg == true){
	//�����Фˤ���
	$disp_data[$i-$row][100] = true;
}else{
	//������ˤ���
	$disp_data[$i-$row][100] = false;
}

/****************************/
//���������
/****************************/
$client_sql  = " SELECT ";
$client_sql .= "     t_client.client_id ";
$client_sql .= " FROM";
$client_sql .= "     t_client ";
$client_sql .= "     INNER JOIN t_contract ON t_client.client_id = t_contract.client_id ";
$client_sql .= " WHERE";
$client_sql .= "     t_contract.trust_id = $shop_id";
$client_sql .= "     AND";
$client_sql .= "     t_client.client_div = '1'";
$client_sql .= "     AND";
$client_sql .= "     t_contract.contract_div = '2'";

//�إå�����ɽ�������������
$count_res = Db_Query($db_con, $client_sql.";");
$total_count = pg_num_rows($count_res);

/****************************/
//HTML�إå�
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//�ǥ��쥯�ȥ�
/****************************/
$fc_page = FC_DIR."system/2-1-104.php";

/****************************/
//HTML�եå�
/****************************/
$html_footer = Html_Footer();

/****************************/
//��˥塼����
/****************************/
$page_menu = Create_Menu_f('system','1');

/****************************/
//���̥إå�������
/****************************/
$page_title .= "(��".$total_count."��)";
$page_header = Create_Header($page_title);

// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form��Ϣ���ѿ���assign
$smarty->assign('form',$renderer->toArray());

//����¾���ѿ���assign
$smarty->assign('var',array(
	'html_header'    => "$html_header",
	'page_menu'      => "$page_menu",
	'page_header'    => "$page_header",
	'html_footer'    => "$html_footer",
	'fc_page'        => "$fc_page",
	'client_flg'     => "$client_flg",
	'early_flg'      => "$early_flg",
	'cname'          => "$cname",
	'intro_ac_money' => "$intro_ac_money",
	'trade_name'     => "$trade_name",
	'state'          => "$state",
	'ac_name'        => "$ac_name",
	'get_flg'        => "$get_flg",
));

//ɽ���ǡ���
$smarty->assign("disp_data", $disp_data);
$smarty->assign("round_data", $round_data);

//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>