<?php
/******************************
 *  �ѹ�����
 *      �� 2006-07-26 ���ʥޥ����ι����ѹ���ȼ����о��ν�����watanabe-k��
 *      �� 2006-10-12 ��ԤκݤαĶȸ����׻���������<suzuki>
 *      �� 2006-10-24 �������ѹ����˾��ʤ��������ʤ��褦�˽���<suzuki>
 *                      �����ɼ�ξ��˼��Ҿ��ʤ��ޤޤ�Ƥ��뤫Ƚ�ꤹ������ɲ�<suzuki>
 *      �� 2006-10-27 �ѹ�������Զ�ʬ�򥪥�饤����Ԥˤ����Ȥ��ˡ��ѹ����μ�����ɼ����<suzuki>
 *                      �������̤����ܥ��󲡲������ѹ���suzuki��
 *      �� 2006-10-30 ʣ���ɲäμ������إܥ��󲡲����˥����å��ܥå������ͤ���������褦�˽���<suzuki>
 *
 *      �� 2006/11/16 �����ʤλҤ�ñ�������ꤵ��Ƥ��ʤ��ä���ɽ�����ʤ��褦�˽���(suzuki)
 *      �� 2006/11/28 ������˥�����Ĥ�(suzuki)
 *      �� 2006/12/01 �����ɼ�αĶȸ����ϰ�����δݤ�����(suzuki)
 *      �� 2006/12/28 �Ԥ����ϥ��ꥢ���ɲ�(morita-d)
 *      �� 2007/01/19 ����󥵥�����˴�������ɲ�(morita-d)
 *      �� 2007/01/22 ����ͭ�������ɲ�(morita-d)
 *      �� 2007/01/24 ����λ�����ɲ�(morita-d)
 *      �� 2007/03/29 �ѹ������ɽ�����뤿�ᡢ�����˵�Ͽ������ܤ��ɲ�(morita-d)
 *      �� 2007/03/30 ��������ά���ʹ�No.�ν���ͼ�����ؿ�����<morita-d>
 *      �� 2007/03/30 �����Ҹˤ���������������<morita-d>
 *      �� 2007/04/03 ������̾���ѹ����Ƥ⥢���ƥ�������ˤ������󤬶���ˤʤ�ʤ��Զ�����<morita-d>
 *      �� 2007/04/04 ���顼��å�������setElementError����Ѥ���褦������<morita-d>
 *      �� 2007/04/04 �����ƥ������̾�ˤȥ����ƥ��ά�ΡˤΥ��顼�������ɲ�<morita-d>
 *      �� 2007/04/04 ������ơ��֥����ɼ������������դΤߤ���Ͽ����褦�˽���<morita-d>
 *      �� 2007/04/05 �Ҳ��������̵ͭ�����ꤹ�뵡ǽ���ɲ�<morita-d>
 *      �� 2007/04/06 ������֤������ǽ�ˤ��뵡ǽ���ɲ�<morita-d>
 *      �� 2007/04/10 ����ID�Υ����å����ɲ�<morita-d>
 *      �� 2007/04/10 ������̾�����ξ���̾������ξ��Υ��顼�����å����ɲ�<morita-d>
 *      �� 2007/04/23 ������ʸ���ۡˤ��ɲ�<morita-d>
 *      �� 2007/04/24 ����ȯ����������ȯ����������������򥨥顼�Ȥ���褦���ѹ�<morita-d>
 *      �� 2007/04/27 �̾狼�饪��饤����Ԥ��ѹ�����ȥ��顼�Ȥʤ��������<morita-d>
 *      �� 2007/04/27 �������Ϣ�Υ��顼��å�������ܺ٤�ɽ������褦����<morita-d>
 *      �� 2007/04/28 ����饤����Ԥ��饪�ե饤����Ԥ��ѹ�����Ȱ�������ɼ���Ĥä��ޤޤˤʤ��Զ�����<morita-d>
 *      �� 2007-06-01 ��ɼ�κ����ϡ���������ñ�̡פǤʤ��ַ���ñ�̡פǼ»ܤ���褦�˽���<morita-d>
 *      �� 2007-06-05 �����ʤޤ������Τ˴ְ�ä����ʥ����ɤ����Ϥ���ȥ����ƥ������̾������ˤʤ��Զ�����<morita-d>
 *      �� 2007-06-05 ����ȯ�����Ƚ���ȯ�����Υ��顼��ܺ٤˽Ф��褦�˽���<morita-d>
 *      �� 2007-06-09 ͽ��ǡ�������ʣ���ƺ����������Ϸٹ��ɽ������褦�˽���<morita-d>
 *      �� 2007-06-11 ���ʥޥ�����Ʊ�����뤫�����ǽ�˽���<morita-d>
 *      �� 2007-06-19 ������˴ؤ���������ɲ�<morita-d>
 *      �� 2007-06-21 ʣ���ɲäμ������إܥ�������ܸ塢���ܥ���ɽ������ʤ��Զ�����<morita-d>
 *      

/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2006-11-07      01-013      suzuki      ����NO��1000�λ��˥��顼ɽ��
 *  2006-11-07      01-012      suzuki      ������η�ˣ�������Ϥ��Ƥ���ɼ�����������褦�˽���
 *  2006-11-10      01-015      suzuki      ��԰������׻���������ư���褦�˽���
 *  2006-12-07      ban_0103    suzuki      �������򥼥����
 *  2006-12-10      03-081      suzuki      �����ɼ�κݤˤϡ����֤�ͭ���ξ��ʤΤ߻��Ѳ�
 *  2006-12-14      0060        suzuki      �����ɼ��Ͽ���ˡ������αĶȸ����׻������ɲ�
 *  2007/07/04      xx-xxx      kajioka-h   ����饤����ԤǤϡ���ϩ�����ô���Ԥ򹹿���������ʤ��褦���ѹ�
 *  2007/08/29                  kajioka-h   �����ɼ�������������Τǰ켰���ξ�硢������׳ۤϸ���ñ����Ʊ���ˤ���
 *  2009/06/25		����No.37	aizawa-m	��§����ǯ����2ǯ�֢�5ǯ�֤��ѹ�
 *  2009/09/09		            aoyama-n    �Ͱ���ǽ�ɲ� 
 *  2009/09/17		            aoyama-n    �����ʬ���Ͱ������ʤ�����Ͱ����ʤ��ֻ���ɽ�� 
 *
******************************/
$s_time = microtime();
$page_title = "����ޥ���";

require_once("ENV_local.php");                    //�Ķ�����ե����� 
require_once(INCLUDE_DIR."error_msg_list.inc");   //���顼��å�����
require_once(INCLUDE_DIR."function_keiyaku.inc"); //�����Ϣ�δؿ�
//require_once(INCLUDE_DIR."rental.inc"); //�����Ǥ��������ؿ������Ѥ��뤿��

//DB����³
$db_con = Db_Connect();

//HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

// ���¥����å�
$auth       = Auth_Check($db_con);
// ���ϡ��ѹ�����̵����å�����
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;
// ����ܥ���Disabled
$del_disabled = ($auth[1] == "f") ? "disabled" : null;

/****************************/
//�����ѿ�����
/****************************/
$client_id   = $_GET["client_id"];      //������
$daiko_id    = $_POST["daiko_id"];      //���ID
$flg         = $_GET["flg"];            //�ɲá�����Ƚ�̥ե饰
$get_con_id  = $_GET["contract_id"];    //�������ID
$return_flg  = $_GET["return_flg"];     //���ܥ���ɽ��Ƚ��ե饰
$get_flg     = $_GET["get_flg"];        //�����פ����ܥ�������Ƚ��ե饰
$c_check     = $_GET["c_check"];        //��§������Ƚ��ե饰
$client_h_id = $_SESSION["client_id"];  //��������桼��ID
$rank_cd     = $_SESSION["fc_rank_cd"]; //�ܵҶ�ʬ������
$staff_id    = $_SESSION["staff_id"];   //���������ID
$group_kind  = $_SESSION["group_kind"]; //���롼�׼���

//ľ��󥯤����ܤ��Ƥ������ˤϡ�TOP�����Ф�
if(count($_GET) == 0){
	Get_ID_Check2();
}

//������ID��hidden�ˤ���ݻ�����
if($_GET["client_id"] != NULL){
	$con_data2["hdn_client_id"] = $client_id;
}else{
	$client_id = $_POST["hdn_client_id"];
}

//���ID��hidden�ˤ���ݻ�����
if($_POST["hdn_daiko_id"] != NULL){
	$con_data2["hdn_daiko_id"] = $daiko_id;
}else{
	$daiko_id = $_POST["hdn_daiko_id"];
}

//�ɲá�����Ƚ�̥ե饰��hidden�ˤ���ݻ�����
if($_GET["flg"] != NULL){
	$con_data2["hdn_flg"] = $flg;
}else{
	$flg = $_POST["hdn_flg"];
}

//�������ID��hidden�ˤ���ݻ�����
if($_GET["contract_id"] != NULL){
	$con_data2["hdn_con_id"] = $get_con_id;
}else{
	$get_con_id = $_POST["hdn_con_id"];
}

//���ܥ���ɽ��Ƚ��ե饰��hidden�ˤ���ݻ�����
if($_GET["return_flg"] != NULL){
	$con_data2["hdn_return_flg"] = $return_flg;
}else{
	$return_flg = $_POST["hdn_return_flg"];
}

//�����פ����ܥ�������Ƚ��ե饰��hidden�ˤ���ݻ�����
if($_GET["get_flg"] != NULL){
	$con_data2["hdn_get_flg"] = $get_flg;
}else{
	$get_flg = $_POST["hdn_get_flg"];
}

//��§�������ե饰��hidden�ˤ���ݻ�����
if($_GET["c_check"] != NULL){
	$con_data2["hdn_c_check_flg"] = $c_check;
}else{
	$c_check = $_POST["hdn_c_check_flg"];
}


//����Ƚ��
Get_ID_Check3($client_id);
Get_ID_Check3($daiko_id);
Get_ID_Check3($get_con_id);


/****************************/
//���ɽ����Ƚ��
/****************************/
if ($flg == "copy" && $client_id != NULL && $get_con_id != NULL){
	//echo "ʣ��";
	$init_type = "ʣ��";

} elseif ($flg == "chg" && $client_id != NULL && $get_con_id != NULL){
	//echo "�ѹ�";
	$init_type = "�ѹ�";

} elseif ($flg == "add" && $get_con_id == NULL){
	//echo "����";
	$init_type = "����";

} else {
	echo "�������ͤ����Ϥ���ޤ���";
	exit;
}

/****************************/
//�������
/****************************/
//��§�����̤������� or �����������˲��̹���
if( $c_check == true || $_POST[renew_flg] == "1" ){

	//�����
	if($_POST["form_stand_day"]["y"] !="" && $_POST["form_stand_day"]["m"]!="" && $_POST["form_stand_day"]["d"]!="" ){
		$stand_ymd[0] = str_pad($_POST["form_stand_day"]["y"],4,0,STR_PAD_LEFT);
		$stand_ymd[1] = str_pad($_POST["form_stand_day"]["m"],2,0,STR_PAD_LEFT);
		$stand_ymd[2] = str_pad($_POST["form_stand_day"]["d"],2,0,STR_PAD_LEFT);
		$stand_day    = $stand_ymd[0]."-".$stand_ymd[1]."-".$stand_ymd[2];    //��ȴ����
	}

	//����ͭ��������
	if($_POST["form_update_day"]["y"] !="" && $_POST["form_update_day"]["m"]!="" && $_POST["form_update_day"]["d"]!="" ){
		$update_ymd[0] = str_pad($_POST["form_update_day"]["y"],4,0,STR_PAD_LEFT);
		$update_ymd[1] = str_pad($_POST["form_update_day"]["m"],2,0,STR_PAD_LEFT);
		$update_ymd[2] = str_pad($_POST["form_update_day"]["d"],2,0,STR_PAD_LEFT);
		$update_day    = $update_ymd[0]."-".$update_ymd[1]."-".$update_ymd[2]; 
	}

	//����λ������
	if($_POST["form_contract_eday"]["y"] !="" && $_POST["form_contract_eday"]["m"]!="" && $_POST["form_contract_eday"]["d"]!="" ){
		$contract_ymd[0] = str_pad($_POST["form_contract_eday"]["y"],4,0,STR_PAD_LEFT);
		$contract_ymd[1] = str_pad($_POST["form_contract_eday"]["m"],2,0,STR_PAD_LEFT);
		$contract_ymd[2] = str_pad($_POST["form_contract_eday"]["d"],2,0,STR_PAD_LEFT);
		$contract_eday   = $contract_ymd[0]."-".$contract_ymd[1]."-".$contract_ymd[2];
	}

	//�����ʬ
	$con_data["daiko_check"]    = $_POST["daiko_check"];
	$con_data["intro_ac_div[]"] = $_POST["intro_ac_div"][0];
	$con_data["act_div[]"]      = $_POST["act_div"][0];



}else if ($init_type == "����"){

	$con_data["daiko_check"]        = 1; //�����ʬ
	$con_data["state"]              = 1; //�������
	$con_data["form_round_div1[]"]  = 1; //�����
	$con_data["intro_ac_div[]"]     = 1; //�Ҳ��������ȯ�����ʤ���
	$con_data["act_div[]"]          = 1; //�������ȯ�����ʤ���

	for($i=1;$i<=5;$i++){
		$con_data["form_aprice_div[$i]"]      = 1; //������
		$con_data["form_ad_offset_radio[$i]"] = 1; //�����껦
		//����������ɽ�����줿���
		if($_POST[renew_flg] != "1"){
			$mst_link["mst_sync_flg[$i]"] = ""; //Ʊ��
		}
	}
	$form->setConstants($mst_link);

	//������Υ饸���ܥ���ν���ͤ򥻥å�
	$form_load  = "Check_read( '1' );";

    //������client_id��NULL�ʤ鳵�פǤ��������ѹ�
    if ($client_id == NULL) {
        Set_Rtn_Page("contract");
    }

//ʣ�� or �ѹ�
}else if ($init_type == "ʣ��" || $init_type == "�ѹ�"){

	//���Ҥη���ID�������å�
	Chk_Contract_Id($db_con,$get_con_id);

	/****************************/
	//����ޥ���
	/****************************/
	$sql  = "SELECT ";
	$sql .= "    t_contract.line,";            //��No0
	$sql .= "    t_contract.route,";           //��ϩ1
	$sql .= "    t_staff1.staff_id,";          //ô����1 2
	$sql .= "    t_staff1.sale_rate,";         //���Ψ1 3
	$sql .= "    t_staff2.staff_id,";          //ô����2 4

	$sql .= "    t_staff2.sale_rate,";         //���Ψ2 5
	$sql .= "    t_staff3.staff_id,";          //ô����3 6
	$sql .= "    t_staff3.sale_rate,";         //���Ψ3 7
	$sql .= "    t_staff4.staff_id,";          //ô����4 8
	$sql .= "    t_staff4.sale_rate,";         //���Ψ4 9

	$sql .= "    t_contract.round_div,";       //����ʬ10
	$sql .= "    t_contract.cycle,";           //����11
	$sql .= "    t_contract.cycle_unit,";      //����ñ��12
	$sql .= "    t_contract.cale_week,";       //��̾13
	$sql .= "    t_contract.abcd_week,";       //ABCD��14

	$sql .= "    t_contract.rday,";            //������15
	$sql .= "    t_contract.week_rday,";       //��������16
	$sql .= "    t_contract.stand_day,";       //�����17
	$sql .= "    t_contract.contract_day,";    //������18
	$sql .= "    t_contract.note, ";           //����19

	$sql .= "    t_contract.last_day,";        //��§�ǽ���20
	$sql .= "    t_contract.contract_div,";    //�����ʬ21
	$sql .= "    t_contract.trust_id,";        //������ID22
	$sql .= "    t_contract.act_request_rate,";//������ʡ��23
	$sql .= "    t_contract.trust_ahead_note,";//�����谸����24

	$sql .= "    t_contract.claim_id || ',' || t_contract.claim_div, ";  //������ID,�������ʬ25
	$sql .= "    t_contract.update_day, ";      //����ͭ����26
	$sql .= "    t_contract.contract_eday, ";   //����λ��27
	$sql .= "    t_contract.intro_ac_div, ";    //�Ҳ�������ե饰28
	$sql .= "    t_contract.intro_ac_price, ";  //�Ҳ�������ʸ���ۡ�29
	$sql .= "    t_contract.intro_ac_rate, " ;   //�Ҳ�������ʡ��30
	$sql .= "    t_contract.state, " ;           //�������31
	$sql .= "    t_contract.act_div, ";          //������ե饰32
	$sql .= "    t_contract.act_request_price, ";   //������ʸ���ۡ�33
	$sql .= "    t_contract.claim_div ";          //�������ʬ34


	$sql .= "FROM ";                
	$sql .= "    t_contract ";

	$sql .= "    LEFT JOIN t_goods ON t_goods.goods_id = t_contract.act_goods_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '0'";
	$sql .= "        )AS t_staff1 ON t_staff1.contract_id = t_contract.contract_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '1'";
	$sql .= "        )AS t_staff2 ON t_staff2.contract_id = t_contract.contract_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '2'";
	$sql .= "        )AS t_staff3 ON t_staff3.contract_id = t_contract.contract_id ";

	$sql .= "    LEFT JOIN ";
	$sql .= "        (SELECT ";
	$sql .= "             t_con_staff.contract_id,";
	$sql .= "             t_staff.staff_id,";
	$sql .= "             t_con_staff.sale_rate ";
	$sql .= "         FROM ";
	$sql .= "             t_con_staff ";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_con_staff.staff_id ";
	$sql .= "         WHERE ";
	$sql .= "             t_con_staff.staff_div = '3'";
	$sql .= "        )AS t_staff4 ON t_staff4.contract_id = t_contract.contract_id ";

	$sql .= "WHERE ";
	$sql .= "    t_contract.contract_id = $get_con_id;";
	
	$result = Db_Query($db_con, $sql); 
	Get_Id_Check($result);
	$data_list = Get_Data($result,2);

	$con_data["form_line"]                = $data_list[0][0];   //��
	//��ϩ�����ꤵ��Ƥ������˷����ѹ�
	if($data_list[0][1] != NULL){
		$data_list[0][1]                      = str_pad($data_list[0][1], 4, 0, STR_POS_LEFT); //��ϩ
		$con_data["form_route_load"][1]       = substr($data_list[0][1],0,2);  
		$con_data["form_route_load"][2]       = substr($data_list[0][1],2,2);
	} 
	$con_data["form_c_staff_id1"]         = $data_list[0][2];  //ô���ԣ�
	$con_data["form_sale_rate1"]          = $data_list[0][3];  //���Ψ��
	$con_data["form_c_staff_id2"]         = $data_list[0][4];  //ô���ԣ�
	$con_data["form_sale_rate2"]          = $data_list[0][5];  //���Ψ��
	$con_data["form_c_staff_id3"]         = $data_list[0][6];  //ô���ԣ�
	$con_data["form_sale_rate3"]          = $data_list[0][7];  //���Ψ��
	$con_data["form_c_staff_id4"]         = $data_list[0][8];  //ô���ԣ�
	$con_data["form_sale_rate4"]          = $data_list[0][9];  //���Ψ��
	$con_data["form_round_div1[]"]        = $data_list[0][10];  //����ʬ

	//����ʬȽ�� 
	if($data_list[0][10] == 1){
		//���
		$con_data["form_abcd_week1"]      = $data_list[0][14];  //��̾��ABCD��
		$con_data["form_week_rday1"]      = $data_list[0][16];  //��������

	}else if($data_list[0][10] == 2){
		//���
		$con_data["form_rday2"]           = $data_list[0][15];  //������

	}else if($data_list[0][10] == 3){
		//���
		$con_data["form_cale_week3"]      = $data_list[0][13];  //��̾�ʣ�������
		$con_data["form_week_rday3"]      = $data_list[0][16];  //��������

	}else if($data_list[0][10] == 4){
		//���
		$con_data["form_cale_week4"]      = $data_list[0][11];  //����

		$week[1] = "��";
		$week[2] = "��";
		$week[3] = "��";
		$week[4] = "��";
		$week[5] = "��";
		$week[6] = "��";
		$week[7] = "��";
		$con_data["form_week_rday4"] = $week[$data_list[0][16]]; //��������

	}else if($data_list[0][10] == 5){
		//���
		$con_data["form_cale_month5"]     = $data_list[0][11];  //����
		$con_data["form_week_rday5"]      = $data_list[0][15];  //������

	}else if($data_list[0][10] == 6){
		//���
		$con_data["form_cale_month6"]     = $data_list[0][11];  //����
		$con_data["form_cale_week6"]      = $data_list[0][13];  //��̾�ʣ�������
		$con_data["form_week_rday6"]      = $data_list[0][16];  //��������

	//}else if($data_list[0][10] == 7 && $c_check != true){
	}else if($data_list[0][10] == 7){
		//���(����§�����̤������ܤ��Ƥ����Ȥ��ϡ������ͤ�ͥ�褹��ٰʲ��ν����ϹԤ�ʤ�)
		
		$sql  = "SELECT ";
		$sql .= "    round_day ";                //�����
		$sql .= "FROM ";
		$sql .= "    t_round ";
		$sql .= "WHERE ";
		$sql .= "    contract_id = $get_con_id;";
		$result = Db_Query($db_con, $sql);
		$round_data = Get_Data($result);
		//��Ͽ���줿�����ʬhidden����������ͤ�����
		for($i=0;$i<count($round_data);$i++){
			$year  = (int) substr($round_data[$i][0],0,4);
			$month = (int) substr($round_data[$i][0],5,2);
			$day   = (int) substr($round_data[$i][0],8,2);
			
			$input_date = "check_".$year."-".$month."-".$day;
			//�ͤ���������٤���§���Υ����å���hidden�Ǻ���
			$form->addElement("hidden","$input_date","");
			//�ͤΥ��å�
			$con_data["$input_date"] = 1;
		}

		$last_day = $data_list[0][20];  //������κǽ���

	}
	
	$stand_day = $data_list[0][17];             //�����
	$stan_day = explode('-',$data_list[0][17]);             //�����
	$con_data["form_stand_day"]["y"] = $stan_day[0];
	$con_data["form_stand_day"]["m"] = $stan_day[1];
	$con_data["form_stand_day"]["d"] = $stan_day[2];

	$con_day = explode('-',$data_list[0][18]);                  //������
	$con_data["form_contract_day"]["y"] = $con_day[0];
	$con_data["form_contract_day"]["m"] = $con_day[1];
	$con_data["form_contract_day"]["d"] = $con_day[2];

	$con_data["form_note"]              = $data_list[0][19];    //����
	$con_data["daiko_check"]            = $data_list[0][21];    //�����ʬ
	$daiko_div                          = $data_list[0][21];    //�����ʬ
	$daiko_id                           = $data_list[0][22];    //������ID
	$con_data["hdn_daiko_id"]           = $data_list[0][22];  
	$con_data["form_daiko_note"]        = $data_list[0][24];    //�����谸����
	$con_data["form_claim"]             = $data_list[0][25];    //������

	$update_day = $data_list[0][26];
	$update_ymd = explode('-',$data_list[0][26]);                  //����ͭ����
	$con_data["form_update_day"]["y"] = $update_ymd[0];
	$con_data["form_update_day"]["m"] = $update_ymd[1];
	$con_data["form_update_day"]["d"] = $update_ymd[2];

	$contract_eday = $data_list[0][27];
	$contract_ymd = explode('-',$data_list[0][27]);                  //����λ��
	$con_data["form_contract_eday"]["y"] = $contract_ymd[0];
	$con_data["form_contract_eday"]["m"] = $contract_ymd[1];
	$con_data["form_contract_eday"]["d"] = $contract_ymd[2];

	$con_data["intro_ac_div[]"]    = $data_list[0][28];
	$con_data["intro_ac_price"]    = $data_list[0][29];
	$con_data["intro_ac_rate"]     = $data_list[0][30];
	$con_data["state"]             = $data_list[0][31];
	$con_data["act_div[]"]         = $data_list[0][32];
	$con_data["act_request_price"] = $data_list[0][33];
	$con_data["act_request_rate"]  = $data_list[0][23];    //��԰������ʡ��

	$form_ad_rest_price = Minus_Numformat(Advance_Offset_Claim($db_con, $g_today, $client_id, $data_list[0][34]));
	$con_data["form_ad_rest_price"] = $form_ad_rest_price;    //������Ĺ�

	/****************************/
	//�������ܥ����Ѥ�ID����
	/****************************/
	$id_data = Make_Get_Id2($db_con,$client_id,$get_con_id);
	$next_id = $id_data[0];
	$back_id = $id_data[1];

	/****************************/
	//�������ƥơ��֥�
	/****************************/
	$sql  = "SELECT ";
	$sql .= "    t_con_info.line,";          //�Կ�0
	$sql .= "    t_con_info.divide, ";       //�����ʬ1
	$sql .= "    t_con_info.serv_pflg,";     //�����ӥ������ե饰2
	$sql .= "    t_con_info.serv_id,";       //�����ӥ�ID3

	$sql .= "    t_con_info.goods_pflg,";    //�����ƥ�����ե饰4
	$sql .= "    t_con_info.goods_id,";      //�����ƥ�ID5
	$sql .= "    t_item.goods_cd,";          //�����ƥ�CD6
	$sql .= "    t_item.name_change,";       //�����ƥ���̾�ѹ�7
	$sql .= "    t_con_info.goods_name,";    //�����ƥ�̾8
	$sql .= "    t_con_info.num,";           //�����ƥ��9

	$sql .= "    t_con_info.set_flg,";       //�켰�ե饰10
	$sql .= "    t_con_info.trade_price,";   //�Ķȸ���11
	$sql .= "    t_con_info.sale_price,";    //���ñ��12
	$sql .= "    t_con_info.trade_amount,";  //�Ķȶ��13   
	$sql .= "    t_con_info.sale_amount,";   //�����14  

	$sql .= "    t_con_info.rgoods_id,";     //����ID15
	$sql .= "    t_body.goods_cd,";          //����CD16
	$sql .= "    t_body.name_change,";       //������̾�ѹ�17
	$sql .= "    t_con_info.rgoods_name,";   //����̾18
	$sql .= "    t_con_info.rgoods_num,";    //���ο�19
	
	$sql .= "    t_con_info.egoods_id,";     //������ID20
	$sql .= "    t_expend.goods_cd,";        //������CD21
	$sql .= "    t_expend.name_change,";     //��������̾�ѹ�22
	$sql .= "    t_con_info.egoods_name,";   //������̾23
	$sql .= "    t_con_info.egoods_num,";    //�����ʿ�24

	$sql .= "    t_con_info.account_price,"; //����ñ��25
	$sql .= "    t_con_info.account_rate, "; //����Ψ26  
	$sql .= "    t_con_info.con_info_id, ";   //��������ID27  
	$sql .= "    t_con_info.official_goods_name, ";   //����̾��������28
	$sql .= "    t_con_info.mst_sync_flg, ";           //���ʥޥ���Ʊ���ե饰 29
	$sql .= "    t_con_info.advance_flg, ";            //�����껦�ե饰 30
    //aoyama-n 2009-09-09
	#$sql .= "    t_con_info.advance_offset_amount ";   //�����껦�� 31
	$sql .= "    t_con_info.advance_offset_amount, ";   //�����껦�� 31
	$sql .= "    t_item.discount_flg ";                //�Ͱ��ե饰 32

	$sql .= "FROM ";
	$sql .= "    t_con_info ";
	$sql .= "    LEFT JOIN t_goods AS t_item ON t_item.goods_id = t_con_info.goods_id ";
	$sql .= "    LEFT JOIN t_goods AS t_body ON t_body.goods_id = t_con_info.rgoods_id ";
	$sql .= "    LEFT JOIN t_goods AS t_expend ON t_expend.goods_id = t_con_info.egoods_id ";
	
	$sql .= "WHERE ";
	$sql .= "    contract_id = $get_con_id; ";
	$result = Db_Query($db_con, $sql);
	$sub_data = Get_Data($result,2);

	//����ID�˳�������ǡ�����¸�ߤ��뤫
	for($s=0;$s<count($sub_data);$s++){
		$search_line = $sub_data[$s][0];   //���������
		//���¶�ʬ�ν���ͤ����ꤷ�ʤ�������
		$aprice_array[] = $search_line;

		$con_data["form_divide"][$search_line]                = $sub_data[$s][1];   //�����ʬ


		/*
		 * ����
		 * �����ա�������BɼNo.��������ô���ԡ��������ơ�
		 * ��2006/10/30��01-003��������suzuki-t����ʣ���ɲäμ������إܥ��󲡲����˥����å��ܥå������ͤ���������褦�˽���
		 *
		*/
		//���ɽ���Τ�����
		if($_POST["check_value_flg"] == 't' || ($flg == 'copy' && $_POST["copy_value_flg"] == NULL)){
			//�����å��դ��뤫Ƚ��
			if($sub_data[$s][2] == 't'){
				$con_data2["form_print_flg1"][$search_line]   = $sub_data[$s][2];    //�����ӥ������ե饰
			}
			$con_data2["copy_value_flg"] = "t";

			//�����å��դ��뤫Ƚ��
			if($sub_data[$s][29] == 't'){
				$con_data2["mst_sync_flg"][$search_line]              = $sub_data[$s][29];    //���ʥޥ���Ʊ���ե饰
			}
		}
		$con_data["form_serv"][$search_line]                  = $sub_data[$s][3];    //�����ӥ�

		/*
		 * ����
		 * �����ա�������BɼNo.��������ô���ԡ��������ơ�
		 * ��2006/10/30��01-003��������suzuki-t����ʣ���ɲäμ������إܥ��󲡲����˥����å��ܥå������ͤ���������褦�˽���
		 *
		*/
		//���ɽ���Τ�����
		if($_POST["check_value_flg"] == 't' || ($flg == 'copy' && $_POST["copy_value_flg"] == NULL)){
			//�����å��դ��뤫Ƚ��
			if($sub_data[$s][4] == 't'){
				$con_data2["form_print_flg2"][$search_line]   = $sub_data[$s][4];    //�����ƥ���ɼ�����ե饰
			}
			$con_data2["copy_value_flg"] = "t";
		}

		$con_data["hdn_goods_id1"][$search_line]              = $sub_data[$s][5];    //�����ƥ�ID
		$con_data["form_goods_cd1"][$search_line]             = $sub_data[$s][6];    //�����ƥ�CD
		$con_data["hdn_name_change1"][$search_line]           = $sub_data[$s][7];    //�����ƥ���̾�ѹ��ե饰
		$hdn_name_change[1][$search_line]                     = $sub_data[$s][7];    //POST�������˥����ƥ�̾���ѹ��Բ�Ƚ���Ԥʤ���
		$con_data["form_goods_name1"][$search_line]           = $sub_data[$s][8];    //�����ƥ�̾��ά�Ρ�
		$con_data["form_goods_num1"][$search_line]            = $sub_data[$s][9];    //�����ƥ��
		$con_data["official_goods_name"][$search_line]        = $sub_data[$s][28];    //�����ƥ�̾��������

		/*
		 * ����
		 * �����ա�������BɼNo.��������ô���ԡ��������ơ�
		 * ��2006/10/30��01-003��������suzuki-t����ʣ���ɲäμ������إܥ��󲡲����˥����å��ܥå������ͤ���������褦�˽���
		 *
		*/
		//���ɽ���Τ�����
		if($_POST["check_value_flg"] == 't' || ($flg == 'copy' && $_POST["copy_value_flg"] == NULL)){
			//�����å��դ��뤫Ƚ��
			if($sub_data[$s][10] == 't'){
				$con_data2["form_issiki"][$search_line]       = $sub_data[$s][10];    //�켰�ե饰
			}
			$con_data2["copy_value_flg"] = "t";
		}

		$cost_price = explode('.', $sub_data[$s][11]);                                //�Ķȸ���
		$con_data["form_trade_price"][$search_line]["1"] = $cost_price[0];  
		$con_data["form_trade_price"][$search_line]["2"] = ($cost_price[1] != null)? $cost_price[1] : '00';    

		$sale_price = explode('.', $sub_data[$s][12]);                                //���ñ��
		$con_data["form_sale_price"][$search_line]["1"] = $sale_price[0];  
		$con_data["form_sale_price"][$search_line]["2"] = ($sale_price[1] != null)? $sale_price[1] : '00';

		$con_data["form_trade_amount"][$search_line]    = number_format($sub_data[$s][13]);  //�Ķȶ��
		$con_data["form_sale_amount"][$search_line]     = number_format($sub_data[$s][14]);  //�����
	
		$con_data["hdn_goods_id2"][$search_line]              = $sub_data[$s][15];    //����ID
		$con_data["form_goods_cd2"][$search_line]             = $sub_data[$s][16];    //����CD
		$con_data["hdn_name_change2"][$search_line]           = $sub_data[$s][17];    //������̾�ѹ��ե饰
		$hdn_name_change[2][$search_line]                     = $sub_data[$s][17];    //POST������������̾���ѹ��Բ�Ƚ���Ԥʤ���
		$con_data["form_goods_name2"][$search_line]           = $sub_data[$s][18];    //����̾
		$con_data["form_goods_num2"][$search_line]            = $sub_data[$s][19];    //���ο�

		$con_data["hdn_goods_id3"][$search_line]              = $sub_data[$s][20];    //������ID
		$con_data["form_goods_cd3"][$search_line]             = $sub_data[$s][21];    //������CD
		$con_data["hdn_name_change3"][$search_line]           = $sub_data[$s][22];    //��������̾�ѹ��ե饰
		$hdn_name_change[3][$search_line]                     = $sub_data[$s][22];    //POST�������˾�����̾���ѹ��Բ�Ƚ���Ԥʤ���
		$con_data["form_goods_name3"][$search_line]           = $sub_data[$s][23];    //������̾
		$con_data["form_goods_num3"][$search_line]            = $sub_data[$s][24];    //�����ʿ�

		//����ñ��
		if($sub_data[$s][25] != NULL){
			//��
			$con_data["form_account_price"][$search_line]       = $sub_data[$s][25];  //����ñ��
			$con_data["form_aprice_div[$search_line]"] = 2;
		}else if($sub_data[$s][26] != NULL){
			//Ψ
			$con_data["form_account_rate"][$search_line]        = $sub_data[$s][26];  //����Ψ
			$con_data["form_aprice_div[$search_line]"] = 3;
		}else{
			//�ʤ�
			$con_data["form_aprice_div[$search_line]"] = 1;
		}

		$con_data["form_ad_offset_radio"][$search_line]        = $sub_data[$s][30];  //�����껦�ե饰
		$con_data["form_ad_offset_amount"][$search_line]       = $sub_data[$s][31];  //�����껦��

        //aoyama-n 2009-09-09
		$con_data["hdn_discount_flg"][$search_line]            = $sub_data[$s][32];  //�Ͱ��ե饰
		
	}
	
	//�ǡ�����̵���Ԥν��������
	for($a=1;$a<=5;$a++){
		if(!in_array($a,$aprice_array)) {
			//�ʤ�
			$con_data["form_aprice_div[$a]"] = 1;
			$con_data["form_ad_offset_radio[$a]"] = 1;
			//$con_data2["mst_sync_flg[$a]"] = f;
		}
	}

	if($group_kind == 2){
		$t_price_readonly = "t_price_readonly('$daiko_div');";
	}
	//������Υ饸���ܥ���ν���ͤ򥻥å�
	$form_load = "Check_read('".$data_list[0][10]."'); $t_price_readonly ";

}



$form->setDefaults($con_data);

/****************************/
//�����ʬ��Ƚ��
/****************************/
/*
if ( ($con_data["daiko_check"] == "2") && ($group_kind == "2") ){
	//echo "����饤����ԡʰ������";
	$mode = "2";

} elseif ( ($con_data["daiko_check"] == "2") && ($group_kind != "2") ){
	//echo "����饤����ԡʼ������";
	$mode = "3";

//�����ʬ��2��ľ�Ĥξ��ϥ��ե饤�����
} elseif ( ($con_data["daiko_check"] == "3") && ($group_kind == "2") ){
	//echo "���ե饤�����";
	$mode = "4";

} elseif ($con_data["daiko_check"] == "1" || $con_data["daiko_check"] == ""){
	//echo "����";
	$mode = "1";
}
*/
/****************************/
//��������
/****************************/
if($_POST["clear_flg"] == true){
	$action = "�����ꥢ";

} elseif($_POST["clear_line"] == true){
	$action = "�ԥ��ꥢ";

} elseif ($_POST["form_ad_sum_btn"] != ""){                                                        
	$action = "����";

} elseif ($_POST["delete_flg"] == true){                                                        
	$action = "���";
	
} elseif($_POST["entry_flg"] == true){
	$action = "��Ͽ";

} elseif($_POST["goods_search_row"] != null){
	$action = "���ʸ���";

} elseif($_POST["daiko_search_flg"] == true || ($daiko_cd1 != NULL && $daiko_cd2 != NULL)){
	$action = "����踡��";

} elseif($_POST["client_search_flg"] == true || ($client_cd1 != NULL && $client_cd2 != NULL)){
	$action = "�����踡��";

} else {
}

/****************************/
//������ID����
/****************************/
$client_flg = false;   //�ǡ���¸��Ƚ��ե饰
$client_cd1         = $_POST["form_client"]["cd1"];       //�����襳����1
$client_cd2         = $_POST["form_client"]["cd2"];       //�����襳����2

//��������������orPOST�˥����ɤ�������
if($_POST["client_search_flg"] == true || ($client_cd1 != NULL && $client_cd2 != NULL)){

    //������ξ�������
    $sql  = "SELECT";
    $sql .= "   client_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_cd1 = '$client_cd1'";
    $sql .= "   AND";
	$sql .= "   client_cd2 = '$client_cd2'";
    $sql .= "   AND";
    $sql .= "   client_div = '1' ";

	//�������������Ϥϡ�������������Τ�ɽ��
	if($_POST["client_search_flg"] == true){
		$sql .= "AND ";
		$sql .= "    state = '1' ";
	}

    $sql .= "   AND";
    $sql .= "    t_client.shop_id = $client_h_id";
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $num = pg_num_rows($result);
	//�����ǡ���������
	if($num == 1){
		//�ǡ�������
		$client_id      = pg_fetch_result($result, 0,0);        //������ID
	}else{
		//�ǡ����ʤ�
		$client_flg = true;
		//������Ͽ�ΰ١��ɲåܥ����GET����ʤ�
		$client_id = "";
	}
}

/****************************/
//��ԥ��������Ͻ���
/****************************/
$daiko_flg = false;   //�ǡ���¸��Ƚ��ե饰
$daiko_cd1 = $_POST["form_daiko"]["cd1"];       //��ԥ�����1
$daiko_cd2 = $_POST["form_daiko"]["cd2"];       //��ԥ�����2

//��������������orPOST�˥����ɤ�������
if($_POST["daiko_search_flg"] == true || ($daiko_cd1 != NULL && $daiko_cd2 != NULL)){

    //FC�ξ�������
    $sql  = "SELECT";
    $sql .= "   client_id ";
    $sql .= " FROM";
    $sql .= "   t_client ";
	$sql .= "   INNER JOIN t_rank ON t_rank.rank_cd = t_client.rank_cd \n";
    $sql .= " WHERE";
    $sql .= "   t_client.client_cd1 = '$daiko_cd1'";
    $sql .= "   AND";
	$sql .= "   t_client.client_cd2 = '$daiko_cd2'";
    $sql .= "   AND";
    $sql .= "   t_client.client_div = '3' ";
    $sql .= "   AND";
    $sql .= "   t_client.state = '1' ";
	$sql .= "   AND ";
    $sql .= "   t_rank.group_kind = '3' \n";
    $sql .= ";";

    $result = Db_Query($db_con, $sql); 
    $num = pg_num_rows($result);
	//�����ǡ���������
	if($num == 1){
		//�ǡ�������
		$daiko_id      = pg_fetch_result($result, 0,0);        //������ID
		$con_data2["hdn_daiko_id"] = $daiko_id;
	}else{
		//�ǡ����ʤ�
		$daiko_flg = true;
		//������Ͽ�ΰ١��ɲåܥ����GET����ʤ�
		$daiko_id = "";
	}
}


/****************************/
//������������
/****************************/
//�ǡ�����¸�ߤ���orʣ�̻��˼¹�
if(($client_flg == false && $client_id != NULL && $client_cd1 != NULL && $client_cd2 != NULL) || ($flg == "copy" && $client_id != NULL)){

	//������ξ�������
	$sql  = "SELECT";
	$sql .= "   t_client.coax, ";
	$sql .= "   t_client.client_cname,";
	$sql .= "   t_client.client_cd1 || '-' || t_client.client_cd2,";
	$sql .= "   t_client.trade_id,";
	$sql .= "   t_client.tax_franct,";
	$sql .= "   t_client.client_cd1, ";
	$sql .= "   t_client.client_cd2,";
	$sql .= "   t_client.d_staff_id1,";
	$sql .= "   t_client.d_staff_id2,";
	$sql .= "   t_client.d_staff_id3";
	//$sql .= "   t_client.intro_ac_div,";
	//$sql .= "   t_client.intro_ac_price,";
	//$sql .= "   t_client.intro_ac_rate ";
	$sql .= " FROM";
	$sql .= "   t_client ";
	$sql .= " WHERE";
	$sql .= "   t_client.client_id = $client_id";
	$sql .= ";";

	$result = Db_Query($db_con, $sql); 
	Get_Id_Check($result);
	$data_list = Get_Data($result,2);

	$coax            = $data_list[0][0];        //�ݤ��ʬ�ʾ��ʡ�
	$cname           = $data_list[0][1];        //�ܵ�̾
	$client_cd       = $data_list[0][2];        //������CD
	$trade_id        = $data_list[0][3];        //���������
	$tax_franct      = $data_list[0][4];        //�����ǡ�ü����ʬ��
	$client_cd1      = $data_list[0][5];        //������CD1
	$client_cd2      = $data_list[0][6];        //������CD2
	$staff1          = $data_list[0][7];        //���ô���ԣ�
	$staff2          = $data_list[0][8];        //���ô���ԣ�
	$staff3          = $data_list[0][9];        //���ô���ԣ�
	//$ac_div          = $data_list[0][10];       //�Ҳ��ʬ
	//$client_ac_price = $data_list[0][11];       //�Ҳ����ʸ����ۡ�
	//����̵���ξ��ϡ�NULL����
	//if($client_ac_price == NULL){
		//$client_ac_price = 'NULL';
	//}

	//$client_ac_rate  = $data_list[0][12];       //�Ҳ����ʡ�����

	//�Ҳ����̾����
	$sql  = "SELECT";
	#$sql .= "   CASE t_client_info.intro_account_id";
	#$sql .= "   WHEN NULL THEN '̵��'";
	#$sql .= "   ELSE t_client_intro.client_cd1 || ' - ' || t_client_intro.client_cd2 || ' ' ||t_client_intro.client_cname";
	#$sql .= "   END, ";
	$sql .= "   t_client_intro.client_cd1 || ' - ' || t_client_intro.client_cd2 || ' ' ||t_client_intro.client_cname,";
	$sql .= "   t_client_info.intro_account_id ";
	//$sql .= "   CASE t_client.intro_ac_div ";
	//$sql .= "   WHEN '' THEN '̵��' ";
	//$sql .= "   END ";
	$sql .= " FROM";
	$sql .= "   t_client_info ";
	$sql .= "   INNER JOIN t_client AS t_client_intro ON t_client_intro.client_id = t_client_info.intro_account_id ";
	$sql .= "   INNER JOIN t_client ON t_client.client_id = t_client_info.client_id ";
	$sql .= " WHERE";
	$sql .= "   t_client_info.client_id = $client_id";
	$sql .= ";";
	$result = Db_Query($db_con, $sql); 
	$info_list = Get_Data($result);

	$ac_name         = $info_list[0][0];        //�Ҳ������
	$client_ac_id    = $info_list[0][1];        //�Ҳ����ID

	if($ac_name == ""){
		$ac_name = "̵��";
	}

	//�ܵ��ѹ���or���פ������ܤ�����Ƚ��
	if($_POST["client_search_flg"] == true || ($_GET["return_flg"] != NULL && $_GET["flg"] == "add")){

		//������(������η��󳫻����򥻥å�)
		$cont_sdata = Get_Init_Contract_Day($db_con,$client_id);
		$con_data2["form_contract_day"]["y"] = $cont_sdata[y];
		$con_data2["form_contract_day"]["m"] = $cont_sdata[m];
		$con_data2["form_contract_day"]["d"] = $cont_sdata[d];


		//��No.�ʷ���ι�No.����MAX+1��ե�����˥��åȡ�
		$con_data2["form_line"] = Get_Init_Con_Line($db_con,$client_id);


		//���ô����
		//ô���ԣ������˥��å�
		$con_data2["form_c_staff_id1"] = $staff1;
		$con_data2["form_c_staff_id2"] = $staff2;
		$con_data2["form_c_staff_id3"] = $staff3;

		//���Ψ(�ᥤ��)
		//���Ψ��100
		$con_data2["form_sale_rate1"] = 100;

		//�ܵ��ѹ����Τߥե���������
		if($_POST["client_search_flg"] == true){
			$con_data2["form_route_load"][1]   = "";
			$con_data2["form_route_load"][2]   = "";
			$con_data2["form_note"]            = "";
			$con_data2["form_round_div1[]"]    = 1;
			$con_data2["form_abcd_week1"]      = "";
			$con_data2["form_week_rday1"]      = "";
			$con_data2["form_rday2"]           = "";
			$con_data2["form_cale_week3"]      = "";
			$con_data2["form_week_rday3"]      = "";
			$con_data2["form_cale_week4"]      = "";
			$con_data2["form_week_rday4"]      = "";
			$con_data2["form_cale_month5"]     = "";
			$con_data2["form_week_rday5"]      = "";
			$con_data2["form_cale_month6"]     = "";
			$con_data2["form_cale_week6"]      = "";
			$con_data2["form_week_rday6"]      = "";

			//����������ƽ����
			for($c=1;$c<=5;$c++){
				Clear_Line_Data($form,$c);
			}

			//��§�����ͽ����
			$year  = date("Y");
			$month = date("m");
			for($i=0;$i<28;$i++){
				//�����������
				$now = mktime(0, 0, 0, $month+$i,1,$year);
				$num = date("t",$now);
				//��ǯʬ�ǡ�������
				for($s=1;$s<=$num;$s++){
					$now = mktime(0, 0, 0, $month+$i,$s,$year);
					$syear  = (int) date("Y",$now);
					$smonth = (int) date("n",$now);
					$sday   = (int) date("d",$now);
					$input_date = "check_".$syear."-".$smonth."-".$sday;

					//�����å����줿���դ��������
					if($_POST["$input_date"] != NULL){
						$con_data2["$input_date"]      = "";
					}
				}
			}

			//����������¶�ʬ�򡢽��������ե饰
			$post_flg = true;
		}
	}else if($flg == "copy"){
		//ʣ���ɲäξ��

		//��No.�ʷ���ι�No.����MAX+1��ե�����˥��åȡ�
		$con_data2["form_line"] = Get_Init_Con_Line($db_con,$client_id);


	}

	//POST�����ѹ�
	$con_data2["hdn_coax"]            = $coax;
	$con_data2["form_client"]["cd1"]  = $client_cd1;
	$con_data2["form_client"]["cd2"]  = $client_cd2;
	$con_data2["form_client"]["name"] = $cname;
	$con_data2["client_search_flg"]   = "";
	
}else{
	//POST�����ѹ�
	$con_data2["form_client"]["cd1"] = "";
	$con_data2["form_client"]["cd2"] = "";
	$con_data2["form_client"]["name"] = "";
	$con_data2["client_search_flg"]   = "";
	$con_data2["form_sale_rate1"] = "";
	$con_data2["form_line"] = "";
}

$cname = addslashes($cname);  //��'�פ��ޤޤ���ǽ�������뤿������¹�

/****************************/
//��Ծ������
/****************************/
//�ǡ�����¸�ߤ���orʣ�̻��˼¹�
if(($daiko_flg == false && $daiko_id != NULL) || ($flg == "copy" && $daiko_id != NULL)){

	//������ξ�������
	$sql  = "SELECT";
	$sql .= "   t_client.client_cname,";
	$sql .= "   t_client.client_cd1, ";
	$sql .= "   t_client.client_cd2, ";
	$sql .= "   t_client.coax ";
	$sql .= " FROM";
	$sql .= "   t_client ";
	$sql .= " WHERE";
	$sql .= "   t_client.client_id = $daiko_id";
	$sql .= ";";

	$result = Db_Query($db_con, $sql); 
	Get_Id_Check($result);
	$data_list = Get_Data($result,2);

	$daiko_cname    = $data_list[0][0];        //��̾
	$daiko_cd1      = $data_list[0][1];        //���CD1
	$daiko_cd2      = $data_list[0][2];        //���CD2
	$daiko_coax     = $data_list[0][3];        //��Ԥδݤ��ʬ

	//POST�����ѹ�
	$con_data2["form_daiko"]["cd1"]  = $daiko_cd1;
	$con_data2["form_daiko"]["cd2"]  = $daiko_cd2;
	$con_data2["form_daiko"]["name"] = $daiko_cname;
	$con_data2["hdn_daiko_coax"]     = $daiko_coax;
	$con_data2["daiko_search_flg"]   = "";
	
}else{
	//POST�����ѹ�
	$con_data2["form_daiko"]["cd1"] = "";
	$con_data2["form_daiko"]["cd2"] = "";
	$con_data2["form_daiko"]["name"] = "";
	$con_data2["hdn_daiko_coax"]     = "";
	$con_data2["daiko_search_flg"]   = "";
}

/****************************/
//���ʥ���������
/****************************/
if($_POST["goods_search_row"] != null){
	
	//���ʥ����ɼ��̾���
	$row_data = $_POST["goods_search_row"];
	//���ʥǡ�������������
	$search_row = substr($row_data,0,1);
	//���ʥǡ��������������
	$search_line = substr($row_data,1,1);

	//�̾ﾦ�ʡ������ʤλҼ���SQL
	$sql  = " SELECT \n";
	$sql .= "     t_goods.goods_id, \n";                      //����ID
	$sql .= "     t_goods.name_change, \n";                   //��̾�ѹ��ե饰
	$sql .= "     t_goods.goods_cd, \n";                      //���ʥ�����
	$sql .= "     t_goods.goods_cname, \n";                   //ά��
	$sql .= "     initial_cost.r_price AS initial_price, \n"; //�Ķ�ñ��
	$sql .= "     sale_price.r_price AS sale_price,  \n";     //���ñ��
	$sql .= "     t_goods.compose_flg,  \n";                   //�����ʥե饰
    //aoyama-n 2009-09-09
	#$sql .= "     (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS official_goods_name \n";                   //����̾
	$sql .= "     (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS official_goods_name, \n";                   //����̾
	$sql .= "     t_goods.discount_flg \n";                   //�Ͱ��ե饰
	                 
	$sql .= " FROM \n";
	$sql .= "     t_goods  \n";
	$sql .= "     INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
	$sql .= "     INNER JOIN  t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id \n";
	$sql .= "     INNER JOIN  t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id \n";

	$sql .= " WHERE \n";
	$sql .= "     t_goods.goods_cd = '".$_POST["form_goods_cd".$search_line][$search_row]."'  \n";
	$sql .= " AND  \n";
	$sql .= "     t_goods.compose_flg = 'f'  \n";
	$sql .= " AND  \n";
	$sql .= "     initial_cost.rank_cd = '2'  \n";
	$sql .= " AND  \n";
	$sql .= "     sale_price.rank_cd = '4' \n";
//watanabe-k�ѹ�
    $sql .= " AND  \n";
    $sql .= "     t_goods.accept_flg = '1' \n";
    $sql .= " AND  \n";
    //ľ�Ĥ����̾���ɼȽ��
    if($_SESSION[group_kind] == "2" && $_POST["daiko_check"] == 1 ){
        //ľ�Ĥ����̾���ɼ
        $sql .= "     t_goods.state IN (1,3)";
    }else{
        //FCor�����ɼ
        $sql .= "     t_goods.state = 1";
    }
    $sql .= " AND  \n";
    $sql .= "         initial_cost.shop_id = $client_h_id  \n";

	$sql .= " AND  \n";
	$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id) \n";

	//�����ʬȽ��
	if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
		$sql .= "AND \n";
		$sql .= "    t_goods.public_flg = 't' \n";
	}

	//���ξ��ʰʳ��Ϲ����ʥǡ��������
	if($search_line != 2){
		//�����ʤοƼ���SQL
		$sql .= "UNION  \n";
		$sql .= " SELECT \n";
		$sql .= "     t_goods.goods_id, \n";                      //����ID
		$sql .= "     t_goods.name_change, \n";                   //��̾�ѹ��ե饰
		$sql .= "     t_goods.goods_cd, \n";                      //���ʥ�����
		$sql .= "     t_goods.goods_cname, \n";                   //ά��
		$sql .= "     NULL, \n";
		$sql .= "     NULL, \n";
		$sql .= "     t_goods.compose_flg,  \n";                   //�����ʥե饰
        //aoyama-n 2009-09-09
		#$sql .= "     t_goods.goods_name  AS official_goods_name \n";                   //����̾
		$sql .= "     t_goods.goods_name  AS official_goods_name, \n";                   //����̾
	    $sql .= "     t_goods.discount_flg \n";                   //�Ͱ��ե饰
		$sql .= " FROM \n";
		$sql .= "     t_goods  \n";
		$sql .= " WHERE \n";
		$sql .= "     t_goods.goods_cd = '".$_POST["form_goods_cd".$search_line][$search_row]."'  \n";
		$sql .= " AND  \n";
		$sql .= "     t_goods.compose_flg = 't'  \n";
//watanabe-k�ѹ�
        $sql .= " AND  \n";
        $sql .= "     t_goods.accept_flg = '1'   \n";
        $sql .= " AND  \n";

		//ľ�Ĥ����̾���ɼȽ��
	    if($_SESSION[group_kind] == "2" && $_POST["daiko_check"] == 1 ){
	        //ľ�Ĥ����̾���ɼ
	        $sql .= "     t_goods.state IN (1,3) \n";
	    }else{
	        //FCor�����ɼ
	        $sql .= "     t_goods.state = 1 \n";
	    }

		//�����ʬȽ��
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			$sql .= "AND \n";
			$sql .= "    t_goods.public_flg = 't' \n";
		}
	}

	$result = Db_Query($db_con, $sql.";");
    $data_num = pg_num_rows($result);
	//�ǡ�����¸�ߤ�����硢�ե�����˥ǡ�����ɽ��
	if($data_num == 1){
    	$goods_data = pg_fetch_array($result);

		$con_data2["hdn_goods_id".$search_line][$search_row]         = $goods_data[0];   //����ID
		$con_data2["hdn_name_change".$search_line][$search_row]      = $goods_data[1];   //��̾�ѹ��ե饰
		$con_data2["form_goods_cd".$search_line][$search_row]        = $goods_data[2];   //����CD
		$con_data2["form_goods_name".$search_line][$search_row]      = $goods_data[3];   //����̾
		$hdn_name_change[$search_line][$search_row]                  = $goods_data[1];   //POST�������˾���̾���ѹ��Բ�Ƚ���Ԥʤ���

		//�����ƥ���ξ��ʥ����ɤ����Ϥ��줿���϶�ۤη׻���»ܤ���
		if($search_line == 1){
			$con_data2["official_goods_name"][$search_row] = $goods_data[official_goods_name];   //����̾

            //aoyama-n 2009-09-09
	        $con_data2["hdn_discount_flg"][$search_row]    = $goods_data[8];                     //�Ͱ��ե饰

			//������Ƚ��
			if($goods_data[6] == 'f'){
				//�����ʤǤϤʤ�

				//����ñ�����������Ⱦ�������ʬ����
				$com_c_price = $goods_data[4];
				$c_price = explode('.', $goods_data[4]);
				$con_data2["form_trade_price"][$search_row]["1"] = $c_price[0];  //�Ķ�ñ��
				$con_data2["form_trade_price"][$search_row]["2"] = ($c_price[1] != null)? $c_price[1] : '00';     

				//���ñ�����������Ⱦ�������ʬ����
				$com_s_price = $goods_data[5];
				$s_price = explode('.', $goods_data[5]);
				$con_data2["form_sale_price"][$search_row]["1"] = $s_price[0];  //���ñ��
				$con_data2["form_sale_price"][$search_row]["2"] = ($s_price[1] != null)? $s_price[1] : '00';

				//��۷׻�����Ƚ��
				if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] != null){
				//�켰�������̡��ξ�硢�Ķȶ�ۤϡ�ñ���߿��̡�����ۤϡ�ñ���ߣ�
					//�Ķȶ�۷׻�
		            $cost_amount = bcmul($goods_data[4], $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//����۷׻�
		            $sale_amount = bcmul($goods_data[5], 1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
				//�켰�������̡ߤξ�硢ñ���ߣ�
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//�Ķȶ�۷׻�
		            $cost_amount = bcmul($goods_data[4],1,2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//����۷׻�
		            $sale_amount = bcmul($goods_data[5],1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
				//�켰�ߡ����̡��ξ�硢ñ���߿���
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//�Ķȶ�۷׻�
		            $cost_amount = bcmul($goods_data[4], $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//����۷׻�
		            $sale_amount = bcmul($goods_data[5], $_POST["form_goods_num1"][$search_row],2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				
				}
			}else{
				//������

				//�����ʤλҤξ��ʾ������
				$sql  = "SELECT ";
				$sql .= "    parts_goods_id ";                       //������ID
				$sql .= "FROM ";
				$sql .= "    t_compose ";
				$sql .= "WHERE ";
				$sql .= "    goods_id = ".$goods_data[0].";";
				$result = Db_Query($db_con, $sql);
				$goods_parts = Get_Data($result);

				//�ƹ����ʤ�ñ������
				$com_c_price = 0;     //�����ʿƤαĶȸ���
				$com_s_price = 0;     //�����ʿƤ����ñ��

				for($i=0;$i<count($goods_parts);$i++){
					$sql  = " SELECT ";
					$sql .= "     t_compose.count,";                       //����
					$sql .= "     initial_cost.r_price AS initial_price,"; //�Ķ�ñ��
					$sql .= "     sale_price.r_price AS sale_price, ";     //���ñ��
					$sql .= "     buy_price.r_price AS buy_price  ";       //����ñ��
					                 
					$sql .= " FROM";
					$sql .= "     t_compose ";

					$sql .= "     INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
					$sql .= "     INNER JOIN t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
					$sql .= "     INNER JOIN t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";
					$sql .= "     INNER JOIN t_price AS buy_price ON t_goods.goods_id = buy_price.goods_id";

					$sql .= " WHERE";
					$sql .= "     t_compose.goods_id = ".$goods_data[0];
					$sql .= " AND ";
					$sql .= "     t_compose.parts_goods_id = ".$goods_parts[$i][0];
					$sql .= " AND ";
					$sql .= "     initial_cost.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     sale_price.rank_cd = '4'";
					$sql .= " AND ";
					$sql .= "     buy_price.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     initial_cost.shop_id = $client_h_id  \n";
					$sql .= " AND  \n";
					$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id); \n";
					$result = Db_Query($db_con, $sql);
					$com_data = Get_Data($result);
					//�����ʤλҤ�ñ�������ꤵ��Ƥ��ʤ���Ƚ��
					if($com_data == NULL){
						$reset_goods_flg = true;   //���Ϥ��줿���ʾ���򥯥ꥢ
					}

					//�����ʿƤαĶ�ñ���׻�������ɲ�(�Ҥο��̡߻ҤαĶȸ���)
					$com_cp_amount = bcmul($com_data[0][0],$com_data[0][1],2);
		            $com_cp_amount = Coax_Col($coax, $com_cp_amount);
					$com_c_price = $com_c_price + $com_cp_amount;
					//�����ʿƤ����ñ���׻�������ɲ�(�Ҥο��̡߻Ҥ����ñ��)
					$com_sp_amount = bcmul($com_data[0][0],$com_data[0][2],2);
		            $com_sp_amount = Coax_Col($coax, $com_sp_amount);
					$com_s_price = $com_s_price + $com_sp_amount;
				}

				//����ñ�����������Ⱦ�������ʬ����
		        $com_cost_price = explode('.', $com_c_price);
				$con_data2["form_trade_price"][$search_row]["1"] = $com_cost_price[0];  //�Ķ�ñ��
				$con_data2["form_trade_price"][$search_row]["2"] = ($com_cost_price[1] != null)? $com_cost_price[1] : '00';     

				//���ñ�����������Ⱦ�������ʬ����
		        $com_sale_price = explode('.', $com_s_price);
				$con_data2["form_sale_price"][$search_row]["1"] = $com_sale_price[0];  //���ñ��
				$con_data2["form_sale_price"][$search_row]["2"] = ($com_sale_price[1] != null)? $com_sale_price[1] : '00';

				//�����ʿƤζ�۷׻�����Ƚ��
				if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] != null){
				//�켰�������̡��ξ�硢�Ķȶ�ۤϡ�ñ���߿��̡�����ۤϡ�ñ���ߣ�
					//�Ķȶ�۷׻�
		            $cost_amount = bcmul($com_c_price, $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//����۷׻�
		            $sale_amount = bcmul($com_s_price, 1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				//�켰�������̡ߤξ�硢ñ���ߣ�
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//�Ķȶ�۷׻�
		            $cost_amount = bcmul($com_c_price,1,2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//����۷׻�
		            $sale_amount = bcmul($com_s_price,1,2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				//�켰�ߡ����̡��ξ�硢ñ���߿���
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//�Ķȶ�۷׻�
		            $cost_amount = bcmul($com_c_price, $_POST["form_goods_num1"][$search_row],2);
		            $cost_amount = Coax_Col($coax, $cost_amount);
					//����۷׻�
		            $sale_amount = bcmul($com_s_price, $_POST["form_goods_num1"][$search_row],2);
		            $sale_amount = Coax_Col($coax, $sale_amount);
				}
			}

			/*
			//���Ƚ��
			if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){

				//�Ķȸ��������ñ������԰�����Ψ��
				$daiko_money = bcmul($com_s_price,bcdiv($_POST["act_request_price"],100,2),2);

			    $eigyo_money = explode('.',$daiko_money);

				$con_data2["form_trade_price"][$search_row]["1"] = $eigyo_money[0];
				if($eigyo_money[1] != NULL){
					$con_data2["form_trade_price"][$search_row]["2"] = $eigyo_money[1];
				}else{
					$con_data2["form_trade_price"][$search_row]["2"] = '00';
				}

				//�Ķȶ�۷׻�����Ƚ��
				if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] != null){
				//�켰�������̡��ξ�硢�Ķȶ�ۤϡ�ñ���߿��̡�
					//�Ķȶ�۷׻�
			        $cost_amount = bcmul($daiko_money, $_POST["form_goods_num1"][$search_row],2);
			        $cost_amount = Coax_Col($daiko_coax, $cost_amount);
				//�켰�������̡ߤξ�硢ñ���ߣ�
				}else if($_POST["form_goods_num1"][$search_row] == null && $_POST["form_issiki"][$search_row] != null){
					//�Ķȶ�۷׻�
			        $cost_amount = bcmul($daiko_money,1,2);
			        $cost_amount = Coax_Col($daiko_coax, $cost_amount);
				//�켰�ߡ����̡��ξ�硢ñ���߿���
				}else if($_POST["form_goods_num1"][$search_row] != null && $_POST["form_issiki"][$search_row] == null){
					//�Ķȶ�۷׻�
			        $cost_amount = bcmul($daiko_money, $_POST["form_goods_num1"][$search_row],2);
			        $cost_amount = Coax_Col($daiko_coax, $cost_amount);
				}
			}
			*/
			$con_data2["form_trade_amount"][$search_row]    = number_format($cost_amount);
			$con_data2["form_sale_amount"][$search_row]     = number_format($sale_amount);


		}else{
			//�󡦻����ܤξ���

			//������Ƚ��
			if($goods_data[6] == 't'){
				//�����ʤλҤξ��ʾ������
				$sql  = "SELECT ";
				$sql .= "    parts_goods_id ";                       //������ID
				$sql .= "FROM ";
				$sql .= "    t_compose ";
				$sql .= "WHERE ";
				$sql .= "    goods_id = ".$goods_data[0].";";
				$result = Db_Query($db_con, $sql);
				$goods_parts = Get_Data($result);

				for($i=0;$i<count($goods_parts);$i++){
					$sql  = " SELECT ";
					$sql .= "     t_compose.count,";                       //����
					$sql .= "     initial_cost.r_price AS initial_price,"; //�Ķ�ñ��
					$sql .= "     sale_price.r_price AS sale_price, ";     //���ñ��
					$sql .= "     buy_price.r_price AS buy_price  ";       //����ñ��
					                 
					$sql .= " FROM";
					$sql .= "     t_compose ";

					$sql .= "     INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
					$sql .= "     INNER JOIN t_price AS initial_cost ON t_goods.goods_id = initial_cost.goods_id";
					$sql .= "     INNER JOIN t_price AS sale_price ON t_goods.goods_id = sale_price.goods_id";
					$sql .= "     INNER JOIN t_price AS buy_price ON t_goods.goods_id = buy_price.goods_id";

					$sql .= " WHERE";
					$sql .= "     t_compose.goods_id = ".$goods_data[0];
					$sql .= " AND ";
					$sql .= "     t_compose.parts_goods_id = ".$goods_parts[$i][0];
					$sql .= " AND ";
					$sql .= "     initial_cost.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     sale_price.rank_cd = '4'";
					$sql .= " AND ";
					$sql .= "     buy_price.rank_cd = '2' ";
					$sql .= " AND ";
					$sql .= "     initial_cost.shop_id = $client_h_id  \n";
					$sql .= " AND  \n";
					$sql .= "     (sale_price.shop_id = (SELECT client_id FROM t_client WHERE client_div = '0') OR sale_price.shop_id = $client_h_id); \n";
					$result = Db_Query($db_con, $sql);
					$com_data = NULL;
					$com_data = Get_Data($result);
					//�����ʤλҤ�ñ�������ꤵ��Ƥ��ʤ���Ƚ��
					if($com_data == NULL){
						$reset_goods_flg = true;   //���Ϥ��줿���ʾ���򥯥ꥢ
					}
				}
			}
		}

		//�����ʤλҤ�ñ�������ꤵ��Ƥ��ʤ��Ȥ������ʾ��󥯥ꥢ
		if($reset_goods_flg == true){
			//�ǡ�����̵�����ϡ������
			$con_data2["hdn_goods_id".$search_line][$search_row]         = "";
			$con_data2["hdn_name_change".$search_line][$search_row]      = "";
			$con_data2["form_goods_cd".$search_line][$search_row]        = "";
			$con_data2["form_goods_name".$search_line][$search_row]      = "";
			$con_data2["form_goods_num".$search_line][$search_row]       = "";

			//�����λ�ɸ
			$sline = $search_line+1;
			$con_data2["form_print_flg".$sline][$search_row]  = "";
			$con_data2["mst_sync_flg"][$search_row]           = "";

			//��۽�����ϡ������ƥ���ξ��Τ�
			if($search_line == 1){
				$con_data2["form_trade_price"][$search_row]["1"] = "";
				$con_data2["form_trade_price"][$search_row]["2"] = "";
				$con_data2["form_trade_amount"][$search_row]     = "";
				$con_data2["form_sale_price"][$search_row]["1"] = "";
				$con_data2["form_sale_price"][$search_row]["2"] = "";
				$con_data2["form_sale_amount"][$search_row]     = "";
			}
		}
	}else{
		//�ǡ�����̵�����ϡ������
		$con_data2["hdn_goods_id".$search_line][$search_row]         = "";
		$con_data2["hdn_name_change".$search_line][$search_row]      = "";
		$con_data2["form_goods_cd".$search_line][$search_row]        = "";
		$con_data2["form_goods_name".$search_line][$search_row]      = "";
		$con_data2["form_goods_num".$search_line][$search_row]       = "";

		//�����λ�ɸ
		$sline = $search_line+1;
		$con_data2["form_print_flg".$sline][$search_row]  = "";
		$con_data2["mst_sync_flg"][$search_row]           = "";

		//��۽�����ϡ������ƥ���ξ��Τ�
		if($search_line == 1){
			$con_data2["official_goods_name"][$search_row]   = "";
			$con_data2["form_trade_price"][$search_row]["1"] = "";
			$con_data2["form_trade_price"][$search_row]["2"] = "";
			$con_data2["form_trade_amount"][$search_row]     = "";
			$con_data2["form_sale_price"][$search_row]["1"]  = "";
			$con_data2["form_sale_price"][$search_row]["2"]  = "";
			$con_data2["form_sale_amount"][$search_row]      = "";
            //aoyama-n 2009-09-09
			$con_data2["hdn_discount_flg"][$search_row]      = "";
		}
	}
	$con_data2["goods_search_row"]                  = "";

}

/****************************/
//���ꥢ�ܥ��󲡲�����
/****************************/
if($_POST["clear_flg"] == true){

	for($i=1;$i<=5;$i++){
		Clear_Line_Data($form,$i);
	}
	$post_flg2 = true;                //���¶�ʬ�򡢽��������ե饰
	$con_data2["clear_flg"] = "";    //���ꥢ�ܥ��󲡲��ե饰
}
/****************************/
//POST�ǡ�������
/****************************/
//print_array($_POST);
$line              = $_POST["form_line"];            //��No
$intro_ac_div      = $_POST["intro_ac_div"][0];
$intro_ac_price    = $_POST["intro_ac_price"];
$intro_ac_rate     = $_POST["intro_ac_rate"];
$act_div           = $_POST["act_div"][0];
$act_request_price = $_POST["act_request_price"];
$act_request_rate  = $_POST["act_request_rate"];
$state             = $_POST["state"];
$trade_amount      = NULL;                         //�Ķȶ�۽����
$sale_amount       = NULL;                         //����۽����

#2010-05-29 hashimoto-y
$ad_offset_tamount = NULL;

//5��ʬ����
for($s=1;$s<=5;$s++){
	$divide[$s]  = $_POST["form_divide"][$s];        //�����ʬ
	$serv_id[$s] = $_POST["form_serv"][$s];          //�����ӥ�ID

	$slip_flg[$s] = $_POST["form_print_flg1"][$s];   //�����ӥ������ե饰1
	if($slip_flg[$s] == NULL){
		$slip_flg[$s] = 'false';
	}else{
		$slip_flg[$s] = 'true';
	}

	$set_flg[$s] = $_POST["form_issiki"][$s];        //�켰�ե饰1
	if($set_flg[$s] == NULL){
		$set_flg[$s] = 'false';
	}else{
		$set_flg[$s] = 'true';
	}

	$mst_sync_flg[$s] = $_POST["mst_sync_flg"][$s];        //����Ʊ���ե饰
	if($mst_sync_flg[$s] == NULL){
		$mst_sync_flg[$s] = 'false';
	}else{
		$mst_sync_flg[$s] = 'true';
	}

	//�Ķȸ���
	$t_price1[$s] = $_POST["form_trade_price"][$s][1]; 
	$t_price2[$s] = $_POST["form_trade_price"][$s][2];
	$trade_price[$s] = $t_price1[$s].".".$t_price2[$s];

	//���ñ��
	$s_price1[$s] = $_POST["form_sale_price"][$s][1]; 
	$s_price2[$s] = $_POST["form_sale_price"][$s][2]; 
	$sale_price[$s] = $s_price1[$s].".".$s_price2[$s];

	//��۷׻�����Ƚ��
	if($set_flg[$s] == 'true' && $_POST["form_goods_num1"][$s] != null){
	//�켰�������̡��ξ�硢�Ķȶ�ۤϡ�ñ���߿��̡�����ۤϡ�ñ���ߣ�
		//�Ķȶ��
		$trade_amount[$s] = bcmul($trade_price[$s], $_POST["form_goods_num1"][$s],2);
		//���Ƚ��
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			//������δݤ��ʬ
			$trade_amount[$s] = Coax_Col($daiko_coax, $trade_amount[$s]);
		}else{
			//������δݤ��ʬ
			$trade_amount[$s] = Coax_Col($coax, $trade_amount[$s]);
		}

		//�����
		$sale_amount[$s] = bcmul($sale_price[$s], 1,2);
	    $sale_amount[$s] = Coax_Col($coax, $sale_amount[$s]);
	
	//�켰�������̡ߤξ�硢ñ���ߣ�
	}else if($set_flg[$s] == 'true' && $_POST["form_goods_num1"][$s] == null){
		//�Ķȶ��
		$trade_amount[$s] = bcmul($trade_price[$s], 1,2);
		//���Ƚ��
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			//������δݤ��ʬ
	    	$trade_amount[$s] = Coax_Col($daiko_coax, $trade_amount[$s]);
		}else{
			//������δݤ��ʬ
			$trade_amount[$s] = Coax_Col($coax, $trade_amount[$s]);
		}

		//�����
		$sale_amount[$s] = bcmul($sale_price[$s], 1,2);
	    $sale_amount[$s] = Coax_Col($coax, $sale_amount[$s]);
	
	//�켰�ߡ����̡��ξ�硢ñ���߿���
	}else if($set_flg[$s] == 'false' && $_POST["form_goods_num1"][$s] != null){
		//�Ķȶ��
		$trade_amount[$s] = bcmul($trade_price[$s], $_POST["form_goods_num1"][$s],2);
		//���Ƚ��
		if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
			//������δݤ��ʬ
	    	$trade_amount[$s] = Coax_Col($daiko_coax, $trade_amount[$s]);
		}else{
			//������δݤ��ʬ
			$trade_amount[$s] = Coax_Col($coax, $trade_amount[$s]);
		}

		//�����
		$sale_amount[$s] = bcmul($sale_price[$s], $_POST["form_goods_num1"][$s],2);
		$sale_amount[$s] = Coax_Col($coax, $sale_amount[$s]);
	}

	//�ܵ��褬�ѹ����줿or���ꥢ�ܥ��󲡲��ξ�硢���������٤��������ʤ�
	if($post_flg != true && $post_flg2 != true){
		//���Τ��򤫤�ʤ��������åȤ��Ƥ����ʤ�����������ʤ���
		$aprice_div[$s] = $_POST["form_aprice_div"][$s][0];                //���¶�ʬ
		//$con_data2["form_aprice_div[$s]"] = $aprice_div[$s];
	}

	//����θ�����
	//���¶�ʬȽ��
	if($aprice_div[$s] == 2){
		//������
		$ac_price[$s] = $_POST["form_account_price"][$s];                 //����ñ��
	}else if($aprice_div[$s] == 3){
		//Ψ
		$ac_rate[$s] = $_POST["form_account_rate"][$s];                   //����Ψ
	}
	
	//����̵���ξ��ϡ�NULL����
	if($ac_price[$s] == NULL){
		$ac_price[$s] = 'NULL';
	}

	//$sale_tamount = $sale_tamount + $sale_amount[$s];                               //����۹��

	$advance_flg[$s]           = $_POST["form_ad_offset_radio"][$s][0];             //�����껦�ե饰
	if($advance_flg[$s] != 2){
		$advance_flg[$s] = 1;
	}

    #echo "ad_offset_tamount:" .$ad_offset_tamount;
    #echo "ad_offset_tamount:" .$_POST["form_ad_offset_amount"][$s] ."<br>";

    #2010-05-29 hashimoto-y
	$advance_offset_amount[$s] = $_POST["form_ad_offset_amount"][$s];               //�����껦��
	#$ad_offset_tamount = $ad_offset_tamount + $advance_offset_amount[$s];           //�����껦�ۡʹ�ס�
    #if( is_int($_POST["form_ad_offset_amount"][$s]) ){
    if( is_numeric($_POST["form_ad_offset_amount"][$s]) ){
        #echo "ad_offset_tamount:" .$_POST["form_ad_offset_amount"][$s] ."<br>";

        #2010-06-28 hashimoto-y
	    #$advance_offset_amount[$s] = $_POST["form_ad_offset_amount"][$s];               //�����껦��
	    $ad_offset_tamount = $ad_offset_tamount + $advance_offset_amount[$s];           //�����껦�ۡʹ�ס�
    }

}


$route  = $_POST["form_route_load"][1];      //��ϩ
$route .= $_POST["form_route_load"][2];

//���ô��������
$staff_check = NULL;                         //��ʣȽ������
$staff_rate = NULL;                          //���Ψ��Ͽ������

$staff1 = $_POST["form_c_staff_id1"];        //���ô����
$staff_check[0] = $staff1;
$rate1 = $_POST["form_sale_rate1"];          //���Ψ��
$staff_rate[0] = $rate1;

$staff2 = $_POST["form_c_staff_id2"];        //���ô����
//�����ͤ�������˽�ʣȽ�����������
if($staff2 != NULL){
	$staff_check[1] = $staff2;
}
$rate2 = $_POST["form_sale_rate2"];          //���Ψ��
$staff_rate[1] = $rate2;

$staff3 = $_POST["form_c_staff_id3"];        //���ô����
//�����ͤ�������˽�ʣȽ�����������
if($staff3 != NULL){
	$staff_check[2] = $staff3;
}
$rate3 = $_POST["form_sale_rate3"];          //���Ψ��
$staff_rate[2] = $rate3;

$staff4 = $_POST["form_c_staff_id4"];        //���ô����
//�����ͤ�������˽�ʣȽ�����������
if($staff4 != NULL){
	$staff_check[3] = $staff4;
}
$rate4 = $_POST["form_sale_rate4"];          //���Ψ��
$staff_rate[3] = $rate4;


//������ǡ�������
//�ܵ��褬�ѹ����줿��硢���������٤��������ʤ�
if($post_flg != true){
	$round_div = $_POST["form_round_div1"][0][0];     //����ʬ
	//�饸���ܥ���Υե�����̾���ü�ʤ��ᡢ���åȤ��Ƥ����ʤ�����������ʤ���
	$con_data2["form_round_div1[]"] = $round_div;
	$con_data2["intro_ac_div[]"] = $intro_ac_div;
	$con_data2["act_div[]"]      = $act_div;
}


//����ʬȽ�� 
if($round_div == 1){
	//���
	$abcd_week = $_POST["form_abcd_week1"];       //��̾��ABCD��
	$week_rday = $_POST["form_week_rday1"];       //��������

}else if($round_div == 2){
	//���
	$rday = $_POST["form_rday2"];                 //������

}else if($round_div == 3){
	//���
	$cale_week = $_POST["form_cale_week3"];       //��̾�ʣ�������
	$week_rday = $_POST["form_week_rday3"];       //��������

}else if($round_div == 4){
	//���
	$cycle_unit = "W";    //����ñ��
	$cycle      = $_POST["form_cale_week4"];      //����

	$week_list["��"] = 1;
	$week_list["��"] = 2;
	$week_list["��"] = 3;
	$week_list["��"] = 4;
	$week_list["��"] = 5;
	$week_list["��"] = 6;
	$week_list["��"] = 7;
	
	$week_name  = $_POST["form_week_rday4"];      //��������
	$week_rday = $week_list[$week_name];          //��������������

}else if($round_div == 5){
	//���
	$cycle_unit = "M";   //����ñ��
	$cycle      = $_POST["form_cale_month5"];     //����
	$rday       = $_POST["form_week_rday5"];      //������

}else if($round_div == 6){
	//���
	$cycle_unit = "M";   //����ñ��
	$cycle      = $_POST["form_cale_month6"];     //����
	$cale_week  = $_POST["form_cale_week6"];      //��̾�ʣ�������
	$week_rday  = $_POST["form_week_rday6"];      //��������

}

//��§���ν�����0.7��
//������ϡ�����ʬ��������ʤ��Ƥ���§���������ǽ
/****************************/
//��§���ǡ�������
/****************************/
$date_array = NULL;
//POST�ǡ�����������
$year  = date("Y");
$month = date("m");


//-- 2009/06/25 ����No.37 �ɲ�
// ��§����ɽ��ǯ�����ѹ�
$hensoku_term 	= 5;	//ǯ��
$m_term 		= $hensoku_term * 12; //����򻻽�
for($i=0;$i<$m_term;$i++){
//for($i=0;$i<28;$i++){
//---------------------------------
	//�����������
	$now = mktime(0, 0, 0, $month+$i,1,$year);
	$num = date("t",$now);

	//2ǯʬ�ǡ�������
	for($s=1;$s<=$num;$s++){
		$now = mktime(0, 0, 0, $month+$i,$s,$year);
		$syear  = (int) date("Y",$now);
		$smonth = (int) date("n",$now);
		$sday   = (int) date("d",$now);
		$input_date = "check_".$syear."-".$smonth."-".$sday;

		//�����å����줿���դ��������
		if($_POST["$input_date"] != NULL){
			$smonth = str_pad($smonth,2, 0, STR_POS_LEFT);
			$sday = str_pad($sday,2, 0, STR_POS_LEFT);
			$date_array[] = $syear."-".$smonth."-".$sday;
			
			//�ͤ���������٤���§���Υ����å���hidden�Ǻ���
			$form->addElement("hidden","$input_date","");
			//POST�κǽ�����ͥ�褹��ե饰
			$last_flg = true;
		}
	}
}

//���̤˥����å����դ����Ǹ������ɽ��
if($last_flg == true){
	$last_day = $date_array[count($date_array)-1];
}

//FC��ľ��Ƚ��
if($group_kind == 2){
	$daiko_div = $_POST["daiko_check"];                           //�����ʬ
	$t_price_readonly  = " t_price_readonly('$daiko_div'); ";
	$t_price_readonly .= " Mult_double_All('$coax',false,'$daiko_coax'); ";

}else{
	//�ƣäη�����Ͽ�ϡ������ʬ���̾�ˤ���
	$daiko_div = '1';
}

//���ɽ�����ѹ����ʳ��ϡ�������饸���ܥ���ˤ�POST����򥻥å�
if($_POST["form_round_div1"]["0"] != NULL){
	$form_load  = "Check_read('".$_POST["form_round_div1"][0][0]."'); $t_price_readonly ";
}

//�����������ѹ�
$cont_day_y           = $_POST["form_contract_day"]["y"];     //������
$cont_day_m           = $_POST["form_contract_day"]["m"];           
$cont_day_d           = $_POST["form_contract_day"]["d"];
if($cont_day_y != null && $cont_day_m != null && $cont_day_d != null){
	$contract_day = str_pad($cont_day_y,4,"0",STR_PAD_LEFT)."-".str_pad($cont_day_m,2,"0",STR_PAD_LEFT)."-".str_pad($cont_day_d,2,"0",STR_PAD_LEFT);
}

$claim_data = $_POST["form_claim"];                           //������,�������ʬ
$c_data     = explode(',', $claim_data);
$claim_id   = $c_data[0];                                     //������ID
$claim_div  = $c_data[1];                                     //�������ʬ
$note       = $_POST["form_note"];                                  //����

//$daiko_price = $_POST["act_request_price"];                    //��԰�����
$daiko_note = $_POST["form_daiko_note"];                      //�����谸����

//5��ʬ����
for($s=1;$s<=5;$s++){
	//�����󥢥��ƥ�
	$goods_item_id[$s] = $_POST["hdn_goods_id1"][$s];                   //����ID

	//����Ƚ��
	if($goods_item_id[$s] != NULL){

		$goods_item_cd[$s]       = $_POST["form_goods_cd1"][$s];       //���ʥ�����
		$goods_item_name[$s]     = $_POST["form_goods_name1"][$s];     //����̾��ά�Ρ�
		$official_goods_name[$s] = $_POST["official_goods_name"][$s];  //����̾��������
		$goods_item_num[$s]      = $_POST["form_goods_num1"][$s];      //����
		$goods_item_flg[$s]      = $_POST["form_print_flg2"][$s];      //��ɼ�����ե饰
		if($goods_item_flg[$s] == NULL){
			$goods_item_flg[$s] = 'false';
		}else{
			$goods_item_flg[$s] = 'true';
		}

		$sql = "SELECT compose_flg FROM t_goods WHERE goods_id = ".$goods_item_id[$s].";";
		$result = Db_Query($db_con, $sql);
		$goods_item_com[$s] = pg_fetch_result($result,0,0);          //�����ʥե饰

		//������Ƚ��
		if($goods_item_com[$s] == 'f'){
			//�����ʤǤ�̵����硢Ǽ�ʥե饰��false
			$goods_item_deli[$s] = 'false';
		}else{
			//�ƹ����ʤξ��ʾ������
			$sql  = "SELECT ";
			$sql .= "    parts_goods_id ";                       //������ID
			$sql .= "FROM ";
			$sql .= "    t_compose ";
			$sql .= "WHERE ";
			$sql .= "    goods_id = ".$goods_item_id[$s].";";
			$result = Db_Query($db_con, $sql);
			$item_parts[$s] = Get_Data($result);

			//�ƹ����ʤο��̼���
			for($i=0;$i<count($item_parts[$s]);$i++){
				$sql  = "SELECT ";
				$sql .= "    t_goods.goods_name,";
				$sql .= "    t_goods.goods_cname,";
				$sql .= "    t_compose.count ";
				$sql .= "FROM ";
				$sql .= "    t_compose INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
				$sql .= "WHERE ";
				$sql .= "    t_compose.goods_id = ".$goods_item_id[$s]." ";
				$sql .= "AND ";
				$sql .= "    t_compose.parts_goods_id = ".$item_parts[$s][$i][0].";";
				$result = Db_Query($db_con, $sql);
				$item_parts_name[$s][$i]  = pg_fetch_result($result,0,0);     //����̾
				$item_parts_cname[$s][$i] = pg_fetch_result($result,0,1);     //ά��
				$parts_num                = pg_fetch_result($result,0,2);     //�����ʤ��Ф������
				$item_parts_num[$s][$i]   = $parts_num * $goods_item_num[$s]; //����
			}
		}
	}else{
		//�켰�ե饰�������ƥ���̤Υ��顼Ƚ��ΰ١������ͤ����
		$goods_item_num[$s] = $_POST["form_goods_num1"][$s];            //����
		$goods_item_flg[$s] = $_POST["form_print_flg2"][$s];            //��ɼ�����ե饰
		if($goods_item_flg[$s] == NULL){
			$goods_item_flg[$s] = 'false';
		}else{
			$goods_item_flg[$s] = 'true';
		}
	}

	//����������
	$goods_body_id[$s] = $_POST["hdn_goods_id2"][$s];            //����ID
	//����Ƚ��
	if($goods_body_id[$s] != NULL){

		$goods_body_cd[$s]   = $_POST["form_goods_cd2"][$s];     //���ʥ�����
		$goods_body_name[$s] = $_POST["form_goods_name2"][$s];   //ά��
		$goods_body_num[$s]  = $_POST["form_goods_num2"][$s];    //����
		
	}else{
		//���ξ��ʡ����̤Υ��顼Ƚ��ΰ١������ͤ����
		$goods_body_num[$s] = $_POST["form_goods_num2"][$s];     //����
	}

	//�����������
	$goods_expend_id[$s] = $_POST["hdn_goods_id3"][$s];          //����ID
	//����Ƚ��
	if($goods_expend_id[$s] != NULL){

		$goods_expend_cd[$s]   = $_POST["form_goods_cd3"][$s];     //���ʥ�����
		$goods_expend_name[$s] = $_POST["form_goods_name3"][$s];   //����̾
		$goods_expend_num[$s]  = $_POST["form_goods_num3"][$s];    //����
		
		$sql = "SELECT compose_flg FROM t_goods WHERE goods_id = ".$goods_expend_id[$s].";";
		$result = Db_Query($db_con, $sql);
		$goods_expend_com[$s] = pg_fetch_result($result,0,0);    //�����ʥե饰

		//������Ƚ��
		if($goods_expend_com[$s] == 'f'){
			//�����ʤǤ�̵����硢Ǽ�ʥե饰��false
			$goods_expend_deli[$s] = 'false';
		}else{
			//�ƹ����ʤξ��ʾ������
			$sql  = "SELECT ";
			$sql .= "    parts_goods_id ";                 //������ID
			$sql .= "FROM ";
			$sql .= "    t_compose ";
			$sql .= "WHERE ";
			$sql .= "    goods_id = ".$goods_expend_id[$s].";";
			$result = Db_Query($db_con, $sql);
			$expend_parts[$s] = Get_Data($result);

			//�ƹ����ʤο��̼���
			for($i=0;$i<count($expend_parts[$s]);$i++){
				$sql  = "SELECT ";
				$sql .= "    t_goods.goods_name,";
				$sql .= "    t_goods.goods_cname,";
				$sql .= "    t_compose.count ";
				$sql .= "FROM ";
				$sql .= "    t_compose INNER JOIN t_goods ON t_compose.parts_goods_id = t_goods.goods_id ";
				$sql .= "WHERE ";
				$sql .= "    t_compose.goods_id = ".$goods_expend_id[$s]." ";
				$sql .= "AND ";
				$sql .= "    t_compose.parts_goods_id = ".$expend_parts[$s][$i][0].";";
				$result = Db_Query($db_con, $sql);

				$expend_parts_name[$s][$i] = pg_fetch_result($result,0,0);      //����̾
				$expend_parts_cname[$s][$i] = pg_fetch_result($result,0,1);     //ά��
				$parts_num = pg_fetch_result($result,0,2);                      //�����ʤ��Ф������
				$expend_parts_num[$s][$i] = $parts_num * $goods_expend_num[$s]; //����
			}
		}
	}else{
		//�����ʡ����̤Υ��顼Ƚ��ΰ١������ͤ����
		$goods_expend_num[$s] = $_POST["form_goods_num3"][$s];    //����
	}


}

/****************************/
//�������
/****************************/
require_once(INCLUDE_DIR."keiyaku.inc");

/****************************/
//����ޥ�����Ͽ
/****************************/
//�����ʬ����Ԥξ��ϡ��ʲ��Υ��顼�����å���Ԥ�
if($_POST["entry_flg"] == true){

	/****************************/
	//���顼�����å�(addRule)
	/****************************/
	if($daiko_div != "1" ){
		//������
		$form->addGroupRule('form_daiko', array(
	        'cd1' => array(
	                array($h_mess[1], 'required'),
	        ),      
	        'cd2' => array(
	                array($h_mess[1],'required'),
	        ),       
	        'name' => array(
	                array($h_mess[1],'required'),
	        )       
		));
		//$form->addGroupRule("form_daiko", $h_mess[1],"required");
	
		//��԰�����
		//��ɬ�ܥ����å�
		//��Ⱦ�ѿ��������å�
		//$form->addRule("act_request_price","$h_mess[2]",'required');
		//$form->addRule("act_request_price","$h_mess[3]", "numeric");
	}
	
	//�����ʬ���̾�ξ��Τߡ��ʲ��Υ��顼�����å���Ԥ�
	if($daiko_div == "1"){

		//���ô��(�ᥤ��)
		$form->addRule('form_c_staff_id1',$h_mess[8],'required');
		$form->addRule('form_sale_rate1',$h_mess[12],'required');
	
		//���֣�
		//��ô���Ԥ����Ψ��ξ�����Ϥ���Ƥ��뤫
		if($staff2 == NULL && $rate2 != NULL){
			$form->addRule('form_c_staff_id2',$h_mess[9],'required');
		}
		if($staff2 != NULL && $rate2 == NULL && ((int)$rate1 + (int)$rate3 + (int)$rate4) != 100){
			$form->addRule('form_sale_rate2',$h_mess[9],'required');
		}
	
		//���֣�
		//��ô���Ԥ����Ψ��ξ�����Ϥ���Ƥ��뤫
		if($staff3 == NULL && $rate3 != NULL){
			$form->addRule('form_c_staff_id3',$h_mess[10],'required');
		}
		if($staff3 != NULL && $rate3 == NULL && ((int)$rate1 + (int)$rate2 + (int)$rate4) != 100){
			$form->addRule('form_sale_rate3',$h_mess[10],'required');
		}
	
		//���֣�
		//��ô���Ԥ����Ψ��ξ�����Ϥ���Ƥ��뤫
		if($staff4 == NULL && $rate4 != NULL){
			$form->addRule('form_c_staff_id4',$h_mess[11],'required');
		}
		if($staff4 != NULL && $rate4 == NULL && ((int)$rate1 + (int)$rate2 + (int)$rate3) != 100){
			$form->addRule('form_sale_rate4',$h_mess[11],'required');
		}
	
		//��ϩ
		$form->addGroupRule("form_route_load", $h_mess[18],"required");
		$form->addGroupRule("form_route_load", $h_mess[18],"numeric");
	
	}
	
	//����ʬ���顼Ƚ�� 
	if($round_div == 1){
		//���
		$form->addRule('form_abcd_week1',$h_mess["19-2"],'required');
		$form->addRule('form_week_rday1',$h_mess["19-4"],'required');
	
	}else if($round_div == 2){
		//���
		$form->addRule('form_rday2',$h_mess["19-3"],'required');
	
	}else if($round_div == 3){
		//���
		$form->addRule('form_cale_week3',$h_mess["19-2"],'required');
		$form->addRule('form_week_rday3',$h_mess["19-4"],'required');
	
	}else if($round_div == 4){
		//���
		$form->addRule('form_cale_week4',$h_mess["19-2"],'required');
	
	}else if($round_div == 5){
		//���
		$form->addRule('form_cale_month5',$h_mess["19-1"],'required');
		$form->addRule('form_week_rday5',$h_mess["19-3"],'required');
	
	}else if($round_div == 6){
		//���
		$form->addRule('form_cale_month6',$h_mess["19-1"],'required');
		$form->addRule('form_cale_week6',$h_mess["19-2"],'required');
		$form->addRule('form_week_rday6',$h_mess["19-4"],'required');
	
	}else if($round_div == 7){
		//���
		//��§���˥����å����դ��Ƥ��뤫Ƚ��
		if($last_day == NULL){
			$form->setElementError("hensoku_err",$h_mess[24]);
		}

		//����λ���ʹߤ���§��������ȥ��顼
		if(($contract_eday < $last_day) && ($contract_eday != NULL)){
			$form->setElementError("hensoku_err",$h_mess[59]);
		}
	}

	//���Ҳ�������Υ����å�
	//�Ҳ�������ʸ���ۡˤξ��
	if($intro_ac_div == "2" ){
		$form->addRule('intro_ac_price',$h_mess[55],"required");
	}
	$form->addRule('intro_ac_price',$h_mess[55],"regex", '/^[0-9]+$/');
	$form->addRule('intro_ac_price',$h_mess[55],"nonzero");

	//�Ҳ�������ʡ�ˤξ��
	if($intro_ac_div == "3" ){
		$form->addRule('intro_ac_rate',$h_mess[56],"required");
	}
	$form->addRule('intro_ac_rate',$h_mess[56],"regex", '/^[0-9]+$/');
	$form->addRule('intro_ac_rate',$h_mess[56],"nonzero");

	if($group_kind == 2){
    //��������Υ����å�
    //�������ʬ���ָ���ۡפξ�硢������ʸ���ۡˤ�ɬ��
    if($act_div == "2" ){
        $form->addRule('act_request_price', $h_mess[60], "required");
    }
    $form->addRule('act_request_price', $h_mess[60], "regex", '/^[0-9]+$/');

    //�������ʬ���֡�פξ�硢������ʡ�ˤ�ɬ��
    if($act_div == "3" ){
        $form->addRule('act_request_rate', $h_mess[61], "required");
    }
    $form->addRule('act_request_rate', $h_mess[61], "regex", '/^[0-9]+$/');
    $form->registerRule("check_percent", "function", "Check_Percent_Qf");
    $form->addRule("act_request_rate", $h_mess[61], "check_percent");

    //�̾���ξ�硢������ϡ�ȯ�����ʤ��פΤ�
    if($daiko_div == "1" && $act_div != "1"){
        $form->setElementError("daiko_check", $h_mess[62]);
    }

    //��Ԥξ�硢������ϡ�ȯ������פΤ�
    if($daiko_div != "1" && $act_div == "1"){
        $form->setElementError("daiko_check", $h_mess[67]);
    }

    //����饤����Ԥξ�硢������ϡ����Ψ�פΤ�
    if($daiko_div == "2" && $act_div != "3"){
        $form->setElementError("daiko_check", $h_mess[63]);
    }
	}

	$input_goods_flg = false;   //��������Ƚ��ե饰
	for($n=1;$n<=5;$n++){
		
		//���Ϲ�Ƚ��
	if($divide[$n] != NULL || $slip_flg[$n] == 'true' || $serv_id[$n] != NULL || $goods_item_flg[$n] == 'true' || $goods_item_id[$n] != NULL || $goods_item_num[$n] != NULL || $set_flg[$n] == 'true' || $t_price1[$n] != NULL || $t_price2[$n] != NULL || $s_price1[$n] != NULL || $s_price2[$n] != NULL || $trade_amount[$n] != NULL || $sale_amount[$n] != NULL || $goods_body_id[$n] != NULL || $goods_expend_id[$n] || $goods_body_num[$n] != NULL || $goods_expend_num[$n] != NULL || $aprice_div[$n] == 2 || $aprice_div[$n] == 3){
			//�����ʬ
			//��ɬ�ܥ����å�
			$form->addRule("form_divide[$n]",$d_mess[0][$n],'required');
	
			//�����ӥ��������ƥ�
			//��ɬ�ܥ����å�
			if($serv_id[$n] == NULL && $goods_item_id[$n] == NULL){
				//ξ�����Ϥ��Ƥ��ʤ���票�顼
				$form->addRule("form_serv[$n]",$d_mess[1][$n],'required');
			}else if($serv_id[$n] == NULL && $set_flg[$n] == 'true'){
				//�켰�˥����å������ꡢ�����ӥ������򤵤�Ƥ��ʤ���票�顼
				$form->addRule("form_serv[$n]",$d_mess[2][$n],'required');
			}
	
			//���̡��켰
			//��ɬ�ܥ����å�
			if($goods_item_num[$n] == NULL && $set_flg[$n] == 'false' && $serv_id[$n] != NULL){
				//�����ӥ������򤵤�Ƥ�����ˡ�ξ�����Ϥ��Ƥ��ʤ���票�顼
				$form->addRule("form_goods_num1[$n]",$d_mess[3][$n],'required');
			}
	
			//�Ķȸ��������ñ��(ɬ��+Ⱦ�ѱѿ���
			$form->addGroupRule("form_trade_price[$n]", $d_mess[5][$n],"required");
			$form->addGroupRule("form_trade_price[$n]", $d_mess[5][$n],"numeric");
			$form->addGroupRule("form_sale_price[$n]", $d_mess[7][$n],"required");
			$form->addGroupRule("form_sale_price[$n]", $d_mess[7][$n],"numeric");
		
			//���¶�ʬȽ��
			if($aprice_div[$n] == 2){
				//������
				$form->addRule("form_account_price[$n]",$d_mess[13][$n],'required');
			}else if($aprice_div[$n] == 3){
				//Ψ
				$form->addRule("form_account_rate[$n]",$d_mess[13][$n],'required');
			}
	
			//��������Ƚ��ե饰
			$input_goods_flg = true;
		}
	}
	
	$form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
	$form->registerRule("check_date","function","Check_Date");
	
	//����orʣ�̤ξ��Ͻ������������å�
	if($init_type == "����" || $init_type == "ʣ��"){
		$form->addGroupRule("form_stand_day", $h_mess[20],  "required");      //�������
		$form->addRule("form_stand_day", $h_mess[20],  "check_date",$_POST["form_stand_day"]);         //�������
	
	//�ѹ��ξ��Ͻ���ͭ����������å�
	} else {
		$form->addGroupRule("form_update_day", $h_mess[52],  "required");       //����ͭ����
		$form->addRule("form_update_day", $h_mess[52],  "check_date",$_POST["form_update_day"]);       //����ͭ����
	
	}
	$form->addRule("form_contract_day",  $h_mess[53],  "check_date",$_POST["form_contract_day"]);   //������
	$form->addRule("form_contract_eday", $h_mess[53],  "check_date",$_POST["form_contract_eday"]); //����λ��
	
	
	//��ɬ�ܥ����å�
	//��Ⱦ�ѿ��������å�
	for($n=1;$n<=5;$n++){
		//�����ƥࡦ����
		if($goods_item_id[$n] != NULL){
			$form->addRule("form_goods_num1[$n]",$d_mess[8][$n],'required'); 
		}
		//�����ʡ�����
		if($goods_expend_id[$n] != NULL){
			$form->addRule("form_goods_num3[$n]",$d_mess[9][$n],'required');
		}
		//���Ρ�����
		if($goods_body_id[$n] != NULL){
			$form->addRule("form_goods_num2[$n]",$d_mess[10][$n],'required');
		}
	}
	
	//���͡�ʸ������
	$form->addRule("form_note",$h_mess[23],"mb_maxlength","100");
	
	//�����谸���͡�ʸ������
	$form->addRule("form_daiko_note",$h_mess[0],"mb_maxlength","100");
	
	//�����ʬ��̵����票�顼ɽ���ʸ�Ǥ��ν����Ϻ����
	if($trade_id == NULL){
		$trade_error_flg = true;
	}

	/****************************/
	//���顼�����å�(PHP)
	/****************************/

	//����ʬ���������ξ�硢��������ɬ��Ƚ��
	if($rday == 0 && ($round_div == 2 || $round_div == 5)){
		$form->setElementError("form_rday2",$h_mess[19]);
	}

	//�ܵ�������Ƚ��
	if($client_id == NULL){
		//����
		$form->setElementError("form_client",$h_mess[34]);

		//�����ե���������
		$con_data2["form_client"]["cd1"] = "";
		$con_data2["form_client"]["cd2"] = "";
		$con_data2["form_client"]["name"] = "";
		$con_data2["client_search_flg"]   = "";
	}else{
		//�����ͤϡ��ԤΥ��顼Ƚ���¹�
		
		//����No.
		//����Ƚ��
		if(ereg("^[0-9]+$",$line) && $line > 0){
			//����(0�����)

			//����ʣ�����å�
		    //���Ϥ��������ɤ��ޥ�����¸�ߤ��뤫�����å�
		    $sql  = "SELECT ";
		    $sql .= "    line ";
		    $sql .= "FROM ";
		    $sql .= "    t_contract ";
		    $sql .= "WHERE ";
		    $sql .= "    line = $line ";
		    $sql .= "AND ";
		    $sql .= "    client_id = $client_id ";
		    //�ѹ��ξ��ϡ���ʬ�Υǡ����ʳ��򻲾Ȥ���
		    if($flg == "chg"){
		        $sql .= " AND NOT ";
		        $sql .= "contract_id = $get_con_id";
		    }
	        $sql .= ";";
	        $result = Db_Query($db_con, $sql);
	        $row_count = pg_num_rows($result);
	        if($row_count != 0){
	            $form->setElementError("form_line",$h_mess[7]);
	        }
	    }else{
			//NULLȽ��
			if($line == NULL){
				//NULL
				$form->setElementError("form_line",$h_mess[5]);
			}else{
				//���Ͱʳ�or����
				$form->setElementError("form_line",$h_mess[6]);
			}
		}
	}

	//�̾����
	if($daiko_div == "1"){

		//�����ô����
		//���Ψ�ι�פ�100%��Ƚ��
		//���Ϥ��줿���Ψ�ι�פ�100��Ƚ��
		if(($rate1 + (int)$rate2 + (int)$rate3 + (int)$rate4) != 100){
			$form->setElementError("sale_rate_sum",$h_mess[16]);
		}

		//���ô���Ԥν�ʣ�����å�
		$cnum1 = count($staff_check);
		$staff_check2 = array_unique($staff_check);
		$cnum2 = count($staff_check2);
		//���ǿ�Ƚ��
		if($cnum1 != $cnum2){
			//���ǿ����㤦��硢��ʣ�ͤ�¸�ߤ���
			$form->setElementError("staff_uniq",$h_mess[17]);
		}

		//���Ψ�����ʾ�ο��ͤ�Ƚ��
		for($s=0;$s<5;$s++){
			$ecount = 12 + $s;
			//�����ͤ�������˿���Ƚ��
			if(!ereg("^[0-9]+$",$staff_rate[$s]) && $staff_rate[$s] != NULL){
				$form->setElementError("form_sale_rate".($s+1),$h_mess[$ecount]);
			}
		}

		//��ϩ
		//��ʸ���������å�
		//�����ͥ����å�
		if(!ereg("^[0-9]{4}$",$route)){
			$form->setElementError("form_route_load","$h_mess[18]");
		}

	//��Ԥξ��
	}else{
		//���������Ƚ��
		if($daiko_id == NULL){
			$form->setElementError("form_daiko",$h_mess[33]);

			//�����ե���������
			$con_data2["form_daiko"]["cd1"] = "";
			$con_data2["form_daiko"]["cd2"] = "";
			$con_data2["form_daiko"]["name"] = "";
			$con_data2["daiko_search_flg"]   = "";
		}
	}

	//���ʹ����Ƥ�̤���Ϥξ�硢���顼
	if($input_goods_flg == false){
		$form->setElementError("goods_enter",$h_mess[21]);
	}

	for($n=1;$n<=5;$n++){
		//���Ϲ�Ƚ��
		if($divide[$n] != NULL || $slip_flg[$n] == 'true' || $serv_id[$n] != NULL ||$goods_item_flg[$n] == 'true' || $goods_item_id[$n] != NULL || $goods_item_num[$n] != NULL || $set_flg[$n] == 'true' || $t_price1[$n] != NULL || $t_price2[$n] != NULL || $s_price1[$n] != NULL || $s_price2[$n] != NULL || $trade_amount[$n] != NULL || $sale_amount[$n] != NULL || $goods_body_id[$n] != NULL || $goods_expend_id[$n] || $goods_body_num[$n] != NULL || $goods_expend_num[$n] != NULL || $aprice_div[$n] == 2 || $aprice_div[$n] == 3 ){
		
			//�������ӥ��������켰�ե饰
			//�������ϥ����å�
			//�����ӥ�ID������������ʤ����ˡ��켰�ե饰���դ��Ƥ��뤫Ƚ��
			if($serv_id[$n] != NULL && $slip_flg[$n] == 'false' && $set_flg[$n] == 'true'){
				$form->setElementError("form_print_flg1[$n]",$d_mess[15][$n]);
			}
	
			//�������ӥ������������ƥ����
			//�������ϥ����å�	
			//�����ӥ�ID or �����ƥ�ID�������硢�����ӥ��������ƥ�Τ����줫���������Ƥ��뤫Ƚ��
			if(($serv_id[$n] != NULL || $goods_item_id[$n] != NULL) && $slip_flg[$n] == 'false' && $goods_item_flg[$n] == 'false'){
				$form->setElementError("form_print_flg2[$n]",$d_mess[17][$n]);
			}
			//�����ӥ������������硢�����ӥ�ID�����뤫Ƚ��
			if($serv_id[$n] == NULL && $slip_flg[$n] == 'true'){
				$form->setElementError("form_serv[$n]",$d_mess[2][$n]);
			}
			
			//�������ƥ����
			//�������ϥ����å�
			//�����ƥ�����������硢�����ƥ�ID�����뤫Ƚ��
			if($goods_item_id[$n] == NULL && $goods_item_flg[$n] == 'true'){
				$form->setElementError("form_goods_cd1[$n]",$d_mess[16][$n]);
			}

			//���ʤ����Ϥ��줿���
			if($goods_item_id[$n] != NULL){
				//�����ƥ������,ά�Ρˤ����Ϥ���Ƥ��뤫�����å�����
				$form->addRule("official_goods_name[$n]",$d_mess[27][$n],'required'); 
				$form->addRule("form_goods_name1[$n]",$d_mess[28][$n],'required'); 
			}

			//��������(������)
			//��������������å�
			if($aprice_div[$n] == 2){
				//����ۤ���������¿����票�顼
				if($sale_amount[$n] < $ac_price[$n]){
					$form->setElementError("form_account_price[$n]",$d_mess[31][$n]);
				}
				//��������������Ƚ��
				$count_price = strpos($ac_price[$n],'.');
				if($count_price != 0){
					$form->setElementError("form_account_price[$n]",$d_mess[13][$n]);
				}
				//���������ޥ��ʥ���Ƚ��
				if(!ereg("^[0-9]+$",$ac_price[$n])){
					$form->setElementError("form_account_price[$n]",$d_mess[13][$n]);
				}
				//�����������ʲ���Ƚ��
				if($ac_price[$n] <= 0){
					$form->setElementError("form_account_price[$n]",$d_mess[13][$n]);
				}
			}
			//��������(�����)
			//��������������å�
			if($aprice_div[$n] == 3){
				//����ۤ���������¿����票�顼
				if(100 < $ac_rate[$n]){
					$form->setElementError("form_account_rate[$n]",$d_mess[14][$n]);
				}
				//��������������Ƚ��
				$count_rate = strpos($ac_rate[$n],'.');
				if($count_rate != 0){
					$form->setElementError("form_account_rate[$n]",$d_mess[13][$n]);
				}
				//���������ޥ��ʥ���Ƚ��
				if(!ereg("^[0-9]+$",$ac_rate[$n])){
					$form->setElementError("form_account_rate[$n]",$d_mess[13][$n]);
				}
				//�����������ʲ���Ƚ��
				if($ac_rate[$n] <= 0){
					$form->setElementError("form_account_rate[$n]",$d_mess[13][$n]);
				}
			}

			//��������
			//�������껦��������򤷤���������������Ϥ�ɬ��
			if($advance_flg[$n] == 2){
				$form->addRule("form_ad_offset_amount[$n]",$d_mess[35][$n],'required'); 
			}
			$form->addRule("form_ad_offset_amount[$n]",$d_mess[35][$n],"regex", '/^[0-9]+$/');

            //aoyama-n 2009-09-09
            /**************
			//���Ķȸ���
			//������Ƚ��
			if(!ereg("^[0-9]+\.[0-9]+$",$trade_price[$n])){
				$form->setElementError("form_trade_price[$n]",$d_mess[5][$n]);
			}
			//�����ñ��
			//������Ƚ��
			if(!ereg("^[0-9]+\.[0-9]+$",$sale_price[$n])){
				$form->setElementError("form_sale_price[$n]",$d_mess[7][$n]);
			}
            **************/
            //�Ͱ�����
            if($_POST["hdn_discount_flg"][$n] === 't'){
			    //���Ķȸ���
			    //������Ƚ��
			    if(!ereg("^[-0-9]+\.[0-9]+$",$trade_price[$n])){
				    $form->setElementError("form_trade_price[$n]",$d_mess[36][$n]);
			    }elseif($trade_price[$n] > 0){
                    $form->setElementError("form_trade_price[$n]",$d_mess[36][$n]);
                }
			    //�����ñ��
			    //������Ƚ��
			    if(!ereg("^[-0-9]+\.[0-9]+$",$sale_price[$n])){
				    $form->setElementError("form_sale_price[$n]",$d_mess[37][$n]);
			    }elseif($sale_price[$n] > 0){
                    $form->setElementError("form_sale_price[$n]",$d_mess[37][$n]);
                }
            //�Ͱ����ʰʳ�
            }else{
			    //���Ķȸ���
			    //������Ƚ��
			    if(!ereg("^[0-9]+\.[0-9]+$",$trade_price[$n])){
				    $form->setElementError("form_trade_price[$n]",$d_mess[5][$n]);
			    }
			    //�����ñ��
			    //������Ƚ��
			    if(!ereg("^[0-9]+\.[0-9]+$",$sale_price[$n])){
				    $form->setElementError("form_sale_price[$n]",$d_mess[7][$n]);
			    }
            }


			//������̾�Υ����å�
			if($goods_expend_id[$n] != NULL){
				$form->addRule("form_goods_name3[$n]",$d_mess[29][$n],'required'); 
			}

			//����̾�Υ����å�
			if($goods_body_id[$n] != NULL){
				$form->addRule("form_goods_name2[$n]",$d_mess[30][$n],'required'); 
			}

			//�������ƥ����
			//������Ƚ��
			if(($serv_id[$n] != NULL || $goods_item_id[$n] != NULL) && !ereg("^[0-9]+$",$goods_item_num[$n]) && $goods_item_num[$n] != NULL){
				$form->setElementError("form_goods_num1[$n]",$d_mess[8][$n]);
			}
			//�����ο���
			//��ɬ��Ƚ��
			if($goods_body_num[$n] != NULL && $goods_body_id[$n] == NULL){
				$form->setElementError("error_goods_num2[$n]",$d_mess[12][$n]);
			}
			//������Ƚ��
			if($goods_body_id[$n] != NULL && !ereg("^[0-9]+$",$goods_body_num[$n])){
				$form->setElementError("form_goods_num2[$n]",$d_mess[10][$n]);
			}
			//�������ʿ���
			//��ɬ��Ƚ��
			if($goods_expend_num[$n] != NULL && $goods_expend_id[$n] == NULL){
				$form->setElementError("error_goods_num3[$n]",$d_mess[11][$n]);
			}
			//������Ƚ��
			if($goods_expend_id[$n] != NULL && !ereg("^[0-9]+$",$goods_expend_num[$n])){
				$form->setElementError("form_goods_num3[$n]",$d_mess[9][$n]);
			}

			/****************************/
			//������Ƚ��ؿ�
			/****************************/
			//���������ƥब̵���ä����
			if($goods_item_id[$n] != NULL){
				//�����ʬȽ��
				if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
					//��Ԥ��������ʤ������ͤ�Ƚ��
					$injust = Injustice_check($db_con,"h_goods",$goods_item_id[$n],$goods_item_cd[$n]);
				}else{
					//����
					$injust = Injustice_check($db_con,"goods",$goods_item_id[$n],$goods_item_cd[$n]);
				}
				if($injust == false){

					//�����ʬȽ��
					if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
						//���
						$form->setElementError("form_goods_cd1[$n]",$d_mess[21][$n]);
					}else{
						//����
						$form->setElementError("form_goods_cd1[$n]",$d_mess[18][$n]);
					}
					//�����ե���������
					$con_data2["hdn_goods_id1"][$n]         = "";
					$con_data2["hdn_name_change1"][$n]      = "";
					$con_data2["form_goods_cd1"][$n]        = "";
					$con_data2["form_goods_name1"][$n]      = "";
					$con_data2["official_goods_name"][$n]   = "";
					$con_data2["form_trade_price"][$n]["1"] = "";
					$con_data2["form_trade_price"][$n]["2"] = "";
					$con_data2["form_trade_amount"][$n]     = "";
					$con_data2["form_sale_price"][$n]["1"]  = "";
					$con_data2["form_sale_price"][$n]["2"]  = "";
					$con_data2["form_sale_amount"][$n]      = "";
				}
			}
			//���ξ�������Ƚ��
			if($goods_body_id[$n] != NULL){
				//�����ʬȽ��
				if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
					//��Ԥ��������ʤ������ͤ�Ƚ��
					$injust = Injustice_check($db_con,"h_goods",$goods_body_id[$n],$goods_body_cd[$n]);
				}else{
					//����
					$injust = Injustice_check($db_con,"goods",$goods_body_id[$n],$goods_body_cd[$n]);
				}
				if($injust == false){

					//�����ʬȽ��
					if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
						//���
						$form->setElementError("form_goods_cd2[$n]",$d_mess[23][$n]);
					}else{
						//����
						$form->setElementError("form_goods_cd2[$n]",$d_mess[20][$n]);
					}

					//�����ե���������
					$con_data2["hdn_goods_id2"][$n]         = "";
					$con_data2["hdn_name_change2"][$n]      = "";
					$con_data2["form_goods_cd2"][$n]        = "";
					$con_data2["form_goods_name2"][$n]      = "";
				}
			}
			//����������Ƚ��
			if($goods_expend_id[$n] != NULL){
				//�����ʬȽ��
				if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
					//��Ԥ��������ʤ������ͤ�Ƚ��
					$injust = Injustice_check($db_con,"h_goods",$goods_expend_id[$n],$goods_expend_cd[$n]);
				}else{
					//����
					$injust = Injustice_check($db_con,"goods",$goods_expend_id[$n],$goods_expend_cd[$n]);
				}
				if($injust == false){
					//�����ʬȽ��
					if($_POST["daiko_check"] != 1 && $_SESSION[group_kind] == "2"){
						//���
						$form->setElementError("form_goods_cd3[$n]",$d_mess[22][$n]);
					}else{
						//����
						$form->setElementError("form_goods_cd3[$n]",$d_mess[19][$n]);
					}

					//�����ե���������
					$con_data2["hdn_goods_id3"][$n]         = "";
					$con_data2["hdn_name_change3"][$n]      = "";
					$con_data2["form_goods_cd3"][$n]        = "";
					$con_data2["form_goods_name3"][$n]      = "";
				}
			}
			$trade_amount_sum = $trade_amount_sum + $trade_amount[$n];

		}
	}


	//�������ʬ���ָ���ۡס����ġ�����������ȱĶȶ�۹�פ����פ��ʤ����
	if($act_div == "2" && ($trade_amount_sum != $act_request_price)){
		$form->setElementError("act_request_price",$h_mess[68]);
	}

	$con_data2["entry_flg"] = "";    //��Ͽ�ܥ��󲡲��ե饰�����

	//����ȯ����������������ϥ��顼
	if ($init_type == "�ѹ�" &&  ($update_day < $g_today)  ){
		$form->setElementError("form_update_day",$h_mess[64]);
	}

	//����ȯ����������������ϥ��顼
	if(($init_type == "����" || $init_type == "ʣ��") && $stand_day < $g_today){
		$form->setElementError("form_stand_day",$h_mess[65]);
	}

	//����λ��������������ϥ��顼
	if(($contract_eday < $g_today) && ($contract_eday != NULL)){
		$form->setElementError("form_contract_eday",$h_mess[66]);
	}

	//$validate_result = $form->validate();

	//�ѹ��ξ��ϡֽ���ȯ�����פȡֽ���ʬ�פ�������������å�����
	if ($init_type == "�ѹ�" && ($form->getElementError("form_update_day") == NULL)){
		$error_mesg = Round_Check($db_con,$update_ymd[0],$update_ymd[1],$update_ymd[2],$round_div,$abcd_week,$cale_week,$week_rday,$rday);

		if ($error_mesg != NULL){ 
			$form->setElementError("form_update_day","����ȯ������".$error_mesg);
		}

	//������ʣ�̤ξ��ϡַ���ȯ�����פȡֽ���ʬ�פ�������������å�����
	}elseif(($init_type == "����" || $init_type == "ʣ��") && ($form->getElementError("form_stand_day") == NULL)){
		$error_mesg = Round_Check($db_con,$stand_ymd[0],$stand_ymd[1],$stand_ymd[2],$round_div,$abcd_week,$cale_week,$week_rday,$rday);

		if ($error_mesg != NULL){ 
			$form->setElementError("form_stand_day","����ȯ������".$error_mesg);
		}
		
	}

	//****************************/
	//��Ͽ���ѹ���������
	//****************************/
	//if($form->validate() && $error_flg == false){
	//if($validate_result && $error_flg == false){
	if( $form->validate() ){
		Db_Query($db_con, "BEGIN");

		//���������ռ���
		$con_today = date("Y-m-d");

		//���ɲó���
		//�̾����ξ��
		if($daiko_div == "1"){
			//��Ԥ˴ؤ�����ܤ�NULL
			$daiko_id    = NULL;
			$con_today   = NULL;
			$daiko_note  = NULL;
		}

		if($daiko_div == "2"){
			//����饤����Ԥξ�硢������ˤ���
			$request_state = 1;
		}else{
			//�̾���ե饤����Ԥξ�硢������
			$request_state = 2;
		}

		$update_columns = array(
			line               => $line,
			//route              => $route,
			round_div          => $round_div,
			cycle              => $cycle,
			cycle_unit         => $cycle_unit,
			cale_week          => $cale_week,
			abcd_week          => $abcd_week,
			rday               => $rday,
			week_rday          => $week_rday,
			stand_day          => $stand_day,
			contract_day       => $contract_day,
			note               => stripslashes($note),
			last_day           => $last_day,
			contract_div       => $daiko_div,
			act_request_day    => $con_today,
			trust_id           => $daiko_id,
			act_div            => $act_div,
			act_request_price  => $act_request_price,
			act_request_rate   => $act_request_rate,
			trust_ahead_note   => $daiko_note,
			request_state      => $request_state,
			intro_ac_div       => $intro_ac_div,
			intro_ac_price     => $intro_ac_price,
			intro_ac_rate      => $intro_ac_rate,
			claim_id           => $claim_id,
			update_day         => $update_day,
			contract_eday      => $contract_eday,
			claim_div          => $claim_div,
			state              => $state,
			trust_trade_amount => NULL,
			trust_sale_amount  => NULL,
		);
        //����饤����ԤǤϽ�ϩ��Ĥ�
        if($daiko_div != "2"){
            $update_columns["route"] = $route;
        }

		//���ɲý�λ


/****************************/
//�ѹ�����
/****************************/
		if($flg == "chg"){

			$work_div = 2;
			$contract_id = $get_con_id;

			/*
			 * ����
			 * �����ա�������BɼNo.��������ô���ԡ��������ơ�
			 * ��2006/10/27��01-007��������suzuki-t�����ѹ�������Զ�ʬ�򥪥�饤����Ԥˤ����Ȥ��ˡ��ѹ����μ�����ɼ����
			 *
			*/
			//�ѹ�������ɼ�������ID����
			$sql  = "SELECT ";
			$sql .= "    trust_id ";
			$sql .= "FROM ";
			$sql .= "    t_contract ";
			$sql .= "WHERE ";
			$sql .= "    contract_id = $contract_id;";
			$result = Db_Query($db_con, $sql); 
			$trust_list = Get_Data($result,3);
			$trust_id = $trust_list[0][0];

			//SQL���󥸥���������к�
			$contract_columns = pg_convert($db_con,'t_contract',$update_columns);
	
			//UPDATE���
			$where[contract_id] = $contract_id;
			$where              = pg_convert($db_con,'t_contract',$where);
	
			//�����ѹ�
			//print_array($contract_columns);
			$return = Db_Update($db_con, t_contract, $contract_columns, $where);
	
			/*
			$sql  = "UPDATE t_contract SET ";
			$sql .= "    line = $line,";
			//��ϩ����Ƚ��
			if($route != NULL){
				$sql .= "    route = $route,";
			}else{
				$sql .= "    route = NULL,";
			}
			$sql .= "    round_div = '$round_div',";
			$sql .= "    cycle = '$cycle',";
			$sql .= "    cycle_unit = '$cycle_unit',";
			$sql .= "    cale_week = '$cale_week',";
			$sql .= "    abcd_week = '$abcd_week',";
			$sql .= "    rday = '$rday',";
			$sql .= "    week_rday = '$week_rday',";
			//����������ꤵ��Ƥ��뤫
			if($stand_day != NULL){
				$sql .= "    stand_day = '$stand_day',";
			}
			//�����������ꤵ��Ƥ��뤫
			if($contract_day != NULL){
				$sql .= "    contract_day = '$contract_day',";
			}else{
				$sql .= "    contract_day = NULL,";
			}
			$sql .= "    note = '$note',";
			//�ǽ�����������ꤵ��Ƥ��뤫
			if($last_day != NULL){
				$sql .= "    last_day = '$last_day',";
			}else{
				$sql .= "    last_day = NULL,";
			}
			$sql .= "    contract_div = '$daiko_div',";
			//�����ʬȽ��
			if($daiko_div != "1"){
				//���
				$sql .= "    act_request_day = '$con_today',";
				$sql .= "    trust_id = $daiko_id,";
				$sql .= "    act_request_rate = '$daiko_price',";
				$sql .= "    trust_ahead_note = '$daiko_note',";
			}else{
				//�̾�ξ���NULL������
				$sql .= "    act_request_day = NULL,";
				$sql .= "    trust_id = NULL,";
				$sql .= "    act_request_rate = NULL,";
				$sql .= "    trust_ahead_note = NULL,";
			}
			//�����ʬȽ��
			if($daiko_div == "2"){
				//����饤����Ԥξ�硢������ˤ���
				$sql .= "    request_state = '1',";
			}else{
				//�̾���ե饤����Ԥξ�硢������
				$sql .= "    request_state = '2',";
			}
			//$sql .= "    intro_ac_price = $intro_ac_price,";   //����ñ��(������)
			//$sql .= "    intro_ac_rate = '$intro_ac_rate',";   //����Ψ(������)
			$sql .= "    claim_id = $claim_id,";                //������ID

			if($update_day != NULL){
				$sql .= "    update_day = '$update_day', ";        //����ͭ����
			}else{
				$sql .= "    update_day = NULL, ";                 //����ͭ����
			}
			if($contract_eday != NULL){
				$sql .= "    contract_eday = '$contract_eday', ";  //����λ��
			}else{
				$sql .= "    contract_eday = NULL, ";              //����λ��
			}
			$sql .= "    claim_div = $claim_div, ";             //�������ʬ
			$sql .= "    intro_ac_div = '$intro_ac_div', ";             //�������ʬ
			$sql .= "    state = '$state' ";             //������ֶ�ʬ
			
			$sql .= " WHERE ";
			$sql .= "    contract_id = $contract_id;";
			*/

/****************************/
//��Ͽ��ʣ���ɲý���
/****************************/
		}else{
			$work_div = 1;
			//$contract_id = Get_Pkey();

        //�������ID����
        $sql  = "SELECT COALESCE(MAX(contract_id),0)+1 FROM t_contract ";
        $result = Db_Query($db_con, $sql);
        $contract_id = pg_fetch_result($result,0,0);

			$insert_columns = array(
				contract_id      => $contract_id,
				client_id        => $client_id,
				shop_id          => $client_h_id,
			);

			$contract_columns = array_merge($insert_columns,$update_columns);

			//SQL���󥸥���������к�
			$contract_columns = pg_convert($db_con,'t_contract',$contract_columns);

			//������Ͽ
			//print_array($contract_columns);
			$return = Db_Insert($db_con, t_contract, $contract_columns);

/*
			$sql  = "INSERT INTO t_contract( ";
			$sql .= "    contract_id,";
			$sql .= "    client_id,";
			$sql .= "    line,";
			$sql .= "    route,";
			$sql .= "    round_div,";
			$sql .= "    cycle,";
			$sql .= "    cycle_unit,";
			$sql .= "    cale_week,";
			$sql .= "    abcd_week,";
			$sql .= "    rday,";
			$sql .= "    week_rday,";
			$sql .= "    stand_day,";
			$sql .= "    contract_day,";
			$sql .= "    note,";
			$sql .= "    last_day,";
			$sql .= "    shop_id,";
			$sql .= "    contract_div,";
			//��Ԥξ��Τ߹���
			if($daiko_div != "1"){
				$sql .= "    act_request_day,";
				$sql .= "    trust_id,";
				$sql .= "    act_request_rate,";
				$sql .= "    trust_ahead_note,";
			}
			$sql .= "    request_state,";
			$sql .= "    intro_ac_price,";
			$sql .= "    intro_ac_rate,";
			$sql .= "    claim_id,";
			$sql .= "    update_day, ";
			$sql .= "    contract_eday, ";
			$sql .= "    claim_div, ";
			$sql .= "    intro_ac_div, ";
			$sql .= "    state ";
			$sql .= "    )VALUES(";
			$sql .= "    $contract_id,";
			$sql .= "    $client_id,";
			$sql .= "    $line,";
			//��ϩ����Ƚ��
			if($route != NULL){
				$sql .= "    $route,";
			}else{
				$sql .= "    NULL,";
			}
			$sql .= "    '$round_div',";
			$sql .= "    '$cycle',";
			$sql .= "    '$cycle_unit',";
			$sql .= "    '$cale_week',";
			$sql .= "    '$abcd_week',";
			$sql .= "    '$rday',";
			$sql .= "    '$week_rday',";
			//����������ꤵ��Ƥ��뤫
			if($stand_day != NULL){
				$sql .= "    '$stand_day',";
			}else{
				$sql .= "    NULL,";
			}
			//�����������ꤵ��Ƥ��뤫
			if($contract_day != NULL){
				$sql .= "    '$contract_day',";
			}else{
				$sql .= "    NULL,";
			}
			$sql .= "    '$note',";
			//�ǽ�����������ꤵ��Ƥ��뤫
			if($last_day != NULL){
				$sql .= "    '$last_day',";
			}else{
				$sql .= "    NULL,";
			}
			$sql .= "    $client_h_id,";
			$sql .= "    '$daiko_div',";
			//��Ԥξ��Τ߹���
			if($daiko_div != "1"){
				$sql .= "    '$con_today',";
				$sql .= "    $daiko_id,";
				$sql .= "    '$daiko_price',";
				$sql .= "    '$daiko_note',";
			}
			//�����ʬȽ��
			if($daiko_div == "2"){
				//����饤����Ԥξ�硢������ˤ���
				$sql .= "    '1',";
			}else{
				//�̾���ե饤����Ԥξ�硢������
				$sql .= "    '2',";
			}
			$sql .= "    $intro_ac_price,";
			$sql .= "    '$intro_ac_rate',";
			$sql .= "    $claim_id,";

			if($update_day != NULL){
				$sql .= "    '$update_day', ";        //����ͭ����
			}else{
				$sql .= "    NULL, ";                 //����ͭ����
			}
			if($contract_eday != NULL){
				$sql .= "    '$contract_eday', ";  //����λ��
			}else{
				$sql .= "    NULL, ";              //����λ��
			}
			
			$sql .= "    '$claim_div', ";	
			$sql .= "    '$intro_ac_div', ";	
			$sql .= "    '$state' ";	

			$sql .= "    );";
	*/

		}

		//$result = Db_Query($db_con, $sql);
		if($result === false){
	        Db_Query($db_con, "ROLLBACK");
	        exit;
	    }

        //�������ID����
        $sql  = "SELECT ";
        $sql .= "    contract_id ";
        $sql .= "FROM ";
        $sql .= "    t_contract ";
        $sql .= "WHERE ";
        $sql .= "    client_id = $client_id ";
        $sql .= "AND ";
        $sql .= "    line = $line;";
        $result = Db_Query($db_con, $sql);
        $contract_id = pg_fetch_result($result,0,0);


		/****************************/
		//�֥ǡ��������������ס֥ǡ���������λ���פη��ꡡ�͡�������ʬ�ϴؿ���
		/****************************/
		/*
		$cal_array = Cal_range($db_con,$client_h_id);

		$start_day   = $cal_array[0];    //������������������ɼ�����������ˡ����������ա�
		$cal_end_day = $cal_array[1];    //����������λ��
		$cal_peri    = $cal_array[2];    //��������ɽ������
		$end_day     = $cal_end_day;     //��ɼ������λ��

    //��ͽ��ǡ��������������η���
    //�ֿ����פ���ʣ�̡פξ��ϡַ���ȯ�����פ��θ����
    if($init_type == "����" || $init_type == "ʣ��"){

			//�������������������������ξ�硢�����������ɼ���������
			if ( ($start_day < $stand_day) && ($stand_day != NULL) ){
				$start_day = $stand_day;
			}

    //�ѹ��ξ��
    }else{
			$stand_day = $update_day;  //�ֽ���ȯ�����פ���Ȥ���

			//����ȯ������������������������ξ�硢����ȯ����������ɼ��ȯ�Ԥ��롣
			if ( ($start_day < $update_day) && ($update_day != NULL) ){
				$start_day = $update_day;
			}

    }

		//�ַ���λ��������������λ��������ס����ġ���NULL�Ǥʤ��ס��ξ��ϡ�����λ���򥫥�������λ���Ȥ��롣
		if ( ($contract_eday < $end_day) && ($contract_eday != NULL) ){
			$end_day = $contract_eday;
		}

		//���֤��ֲ��󡦼����פξ��
		if ( $state == "2" ){
			$end_day = date("Y-m-d",mktime(0,0,0,date(m),date(d)-1,date(Y))); //������λ���Ȥ���
		}
		*/

		//��ɼ�������֤����
		$cal_date = Cal_Span($db_con,$client_h_id,$state,$init_type,$stand_day,$update_day,$contract_eday);
		$stand_day   = $cal_date[0];    //����ȯ�����ʴ������		
		$start_day   = $cal_date[1];    //��ɼ����������
		$end_day     = $cal_date[2];    //��ɼ������λ��

		//��§���ʳ��ϡ�������򻻽�
		if($round_div != 7){
			$date_array = NULL;
			$date_array = Round_day($db_con,$cycle,$cale_week,$abcd_week,$rday,$week_rday,$stand_day,$round_div,$start_day,$end_day);
		}

		//****************************
		//ͽ����ɼ�ν�ʣ���������å�
		//****************************
		if(!empty($date_array)){

			//��������Ȥ˽�ʣ������å�����
			foreach($date_array AS $key => $date){
				$duplicat = Get_Aord_Duplicat($db_con,$contract_id,$date); 

				//��ʣ���ʤ����
				if ($duplicat === false) {

				//��ʣ��������
				} else {
					$duplicat_mesg .= $duplicat;
					$commit_flg = false; //���ߥåȤ��ʤ�
				}

			}
		}
			                                                                 

		/****************************/
		//������ơ��֥���Ͽ�ʰ��ٺ�����������Ͽ��
		/****************************/
		Delete_ConRound($db_con,$contract_id); 

		//��§���ξ��ϡ�������ơ��֥���Ͽ�����§�ǽ�����UPDATE
		if($round_div == 7){
			//������ơ��֥���Ͽ
			Regist_ConRound($db_con,$contract_id,$date_array,NULL);

			//���ǽ��������
			$last_day = Get_Max_ConRound($db_con,$contract_id);

			$update_columns           = NULL; //�����
			$update_columns[last_day] = $last_day;
	
			//SQL���󥸥���������к�
			$update_columns = pg_convert($db_con,'t_contract',$update_columns);	
				
			//UPDATE���
			$where[contract_id] = $contract_id;
			$where              = pg_convert($db_con,'t_contract',$where);

			//��§�ǽ�����UPDATE
			Db_Update($db_con, t_contract, $update_columns, $where);

		//��§���ʳ��ξ��
		}else{
			//������ơ��֥���Ͽ
			Regist_ConRound($db_con,$contract_id,$date_array,$end_day);
		}



		/****************************/
		//���ô���ԥơ��֥���Ͽ�ʰ��ٺ�����������Ͽ��
		/****************************/
        //����饤����ԤǤϺ�����ʤ�
        if($daiko_div != "2"){
    		Delete_ConStaff($db_con,$contract_id);
        }

		//�̾����Ͻ��ô���Ԥ���Ͽ����
		if($daiko_div == "1"){
			Regist_ConStaff($db_con,$contract_id,$staff_check,$staff_rate);
		}

		/****************************/
		//����������Ͽ�ʰ��ٺ�����������Ͽ��
		/****************************/
		Delete_ConInfo($db_con,$contract_id);

		for($s=1;$s<=5;$s++){
			//�����ƥ�or�����ӥ�����Ƚ��
			if($goods_item_id[$s] != NULL || $serv_id[$s] != NULL){

				$sql  = "INSERT INTO t_con_info( ";
				$sql .= "    con_info_id,";
				$sql .= "    contract_id,";
				$sql .= "    line,";
				$sql .= "    divide,";
				$sql .= "    serv_pflg,";
				$sql .= "    serv_id,";
				$sql .= "    goods_pflg,";
				$sql .= "    goods_id,";
				$sql .= "    goods_name,";
				$sql .= "    num,";
				$sql .= "    set_flg,";
				$sql .= "    trade_price,";
				$sql .= "    trade_amount,";
				$sql .= "    sale_price,";
				$sql .= "    sale_amount,";
				$sql .= "    rgoods_id,";
				$sql .= "    rgoods_name,";
				$sql .= "    rgoods_num,";
				$sql .= "    egoods_id,";
				$sql .= "    egoods_name,";
				$sql .= "    egoods_num,";
				$sql .= "    account_price,";
				$sql .= "    account_rate, ";
				$sql .= "    mst_sync_flg, ";
				$sql .= "    official_goods_name, ";
				$sql .= "    advance_flg, ";
				$sql .= "    advance_offset_amount ";
				$sql .= "    )VALUES(";
				$sql .= "    (SELECT COALESCE(MAX(con_info_id),0)+1 FROM t_con_info),";  //��������ID
				$sql .= "    $contract_id,";                                             //�������ID
				$sql .= "    $s,";                                                       //�Կ�
				$sql .= "    '".$divide[$s]."',";                                        //�����ʬ
				$sql .= "    '".$slip_flg[$s]."',";                                      //�����ӥ�����
				//�����ӥ�IDȽ��
				if($serv_id[$s] != NULL){
					$sql .= "    ".$serv_id[$s].",";                                     //�����ӥ�ID
				}else{
					$sql .= "    NULL,";
				}
				//�����ƥ�����ե饰
				if($goods_item_id[$s] != NULL){
					$sql .= "    '".$goods_item_flg[$s]."',";                            //�����ƥ����
				}else{
					$sql .= "    'false',";
				}
				//�����ƥ�����Ƚ��
				if($goods_item_id[$s] != NULL){
					$sql .= "    ".$goods_item_id[$s].",";                               //�����ƥ�ID
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$goods_item_name[$s]."',";                               //�����ƥ�̾
				//�����ƥ������Ƚ��
				if($goods_item_num[$s] != NULL){
					$sql .= "    ".$goods_item_num[$s].",";                              //�����ƥ��
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$set_flg[$s]."',";                                       //�켰�ե饰
				$sql .= "    ".$trade_price[$s].",";                                     //�Ķȸ���
				$sql .= "    ".$trade_amount[$s].",";                                    //�Ķȶ��
				$sql .= "    ".$sale_price[$s].",";                                      //���ñ��
				$sql .= "    ".$sale_amount[$s].",";                                     //�����
				//��������Ƚ��
				if($goods_body_id[$s] != NULL){
					$sql .= "    ".$goods_body_id[$s].",";                               //����ID
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$goods_body_name[$s]."',";                               //����̾
				//���ο�����Ƚ��
				if($goods_body_num[$s] != NULL){
					$sql .= "    ".$goods_body_num[$s].",";                              //���ο�
				}else{
					$sql .= "    NULL,";
				}
				//����������Ƚ��
				if($goods_expend_id[$s] != NULL){
					$sql .= "    ".$goods_expend_id[$s].",";                             //������ID
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    '".$goods_expend_name[$s]."',";                             //������̾
				//�����ʿ�����Ƚ��
				if($goods_expend_num[$s] != NULL){
					$sql .= "    ".$goods_expend_num[$s].",";                            //�����ʿ�
				}else{
					$sql .= "    NULL,";
				}
				$sql .= "    ".$ac_price[$s].",";                                       //����ñ��
				$sql .= "    '".$ac_rate[$s]."',";                                      //����Ψ
				$sql .= "    '".$mst_sync_flg[$s]."',";                                 //���ʥޥ���Ʊ���ե饰
				$sql .= "    '".$official_goods_name[$s]."',";                          //�����ƥ�̾
				$sql .= "    '".$advance_flg[$s]."',";                                  //�����껦�ե饰

				//�����껦����ξ��
				if($advance_flg[$s] == "2"){
					$sql .= "    ".$advance_offset_amount[$s]."";                            //�����껦��
				}else{
					$sql .= "    NULL";
				}
				$sql .= ");";  
				
				$result = Db_Query($db_con, $sql);
				if($result === false){
				    Db_Query($db_con, "ROLLBACK");
				    exit;
				}
			}
		}
		
		//��ۤ򥢥åץǡ���
		$contract_amount = Update_Act_Amount($db_con, $contract_id,"contract");
		$sale_tamount = $contract_amount[amount][sale][2];

		//�ǹ�����פ�������껦�ۤ�¿�����
        #2010-05-29 hashimoto-y
		#if ($sale_tamount < $ad_offset_tamount){
		if ($ad_offset_tamount != NULL && $sale_tamount < $ad_offset_tamount){
			$advance_mesg  = "<font color=\"#ff00ff\">[�ٹ�]<br>";
			$advance_mesg .= "���ǹ�����ס�".$sale_tamount."�ߡˤ��¿�������껦��ס�".$ad_offset_tamount."�ߡˤ����Ϥ���Ƥ��ޤ���<br>";
			$advance_mesg .= "<br>����ʤ���С����Τޤޡ���Ͽ�ץܥ���򲡤��Ʋ�������";
			$advance_mesg .= "<br>���꤬����С��������껦��ספ��ѹ����ơ���Ͽ�ץܥ���򲡤��Ʋ�������<p>";
			$advance_mesg .= "</font>";

			$commit_flg = false; //���ߥåȤ��ʤ�
		}

		/****************************/
		//�и��ʥơ��֥���Ͽ
		/****************************/
		//�и��ʤ�ɬ�ץǡ�����$value�Ȥ�����Ͽ
		$value = array(
			$divide,
			$goods_item_com,
			$goods_item_id,
			$goods_item_name,
			$goods_item_num,
			$goods_expend_com,
			$goods_expend_id,
			$goods_expend_name,
			$goods_expend_num,
			$item_parts,
			$item_parts_cname,
			$item_parts_num,
			$expend_parts,
			$expend_parts_cname,
			$expend_parts_num
		);
		Regist_ConShipDetail($db_con,$contract_id,$value);

		//����ޥ������ͤ�����˽񤭹���ʥǡ��������ɡ�������CD-��No.  ̾�Ρ�������̾-����ܹ�No.��
		$result = Log_Save($db_con,'contract',$work_div,$client_cd."-".$line,$cname."-����".$line,$contract_id);
		//������Ͽ���˥��顼�ˤʤä����
		if($result == false){
			Db_Query($db_con,"ROLLBACK;");
			exit;
		}

		/****************************/
		//�����ǡ�������ʧ��Ͽ     
		/****************************/
		//����饤����԰ʳ��Τ���Ͽ
		if($daiko_div != "2"){
			//Aorder_Query($db_con,$client_h_id,$client_id,$start_day,$end_day);
			Regist_Aord_Contract($db_con,$client_h_id,$contract_id,$start_day,$end_day);
		}

		//����饤����Ԥ��ѹ��������ϼ����إå�����
		if($flg == "chg" && $daiko_div == "2" && $_SESSION["group_kind"] == '2'){
			Delete_Aorder($db_con,$client_id,$start_day,$end_day,$contract_id);
		}

		//****************************
		//���ߥåȽ���
		//****************************
		//��ͽ��ǡ����ν�ʣ�ʤ��פޤ��ϡ�ͽ��ǡ����ν�ʣOK��
		if (($commit_flg !== false) || ($_POST[duplicat_ok] == "1") || ($_POST[advance_ok] == "1") ){
		//if (($commit_flg !== false)){
			Db_Query($db_con, "COMMIT;");                                                     
			header("Location: ./2-1-115.php?client_id=$client_id");                                                                                                          
			exit;
		}
		
		//ͽ��ǡ�������ʣ����������硢�ٹ��ǧ�ե饰��1�ˤ���
		if(($commit_flg === false) && ($_POST[duplicat_ok] != "1")){
			$form->setConstants(array(duplicat_ok=>"1"));
		}

		//����������껦�ۤ�¿�����
		if(($commit_flg === false) && ($_POST[advance_ok] != "1")){
			$form->setConstants(array(advance_ok=>"1"));
		}
		Db_Query($db_con, "ROLLBACK;");


	//���顼�����ä���硢�ٹ��ǧ�ե饰��̤��ǧ���᤹
	}else{
		$form->setConstants(array(duplicat_ok=>"0"));
		$form->setConstants(array(advance_ok=>"0"));
	}


/****************************/                                                           
//����ޥ������                                                                         
/****************************/                                                           
}elseif($_POST["delete_flg"] == true){                                                        

	Db_Query($db_con, "BEGIN");

	//����ޥ������
	Delete_Contract($db_con, $get_con_id,$cname);

	//�ޥ�����������ߡʥǡ��������ɡ�������CD-��No.  ̾�Ρ�������̾-����ܹ�No.��
	$cname = addslashes($cname);  //��'�פ��ޤޤ���ǽ�������뤿������¹�
	$result = Log_Save($db_con,'contract',3,$client_cd."-".$line,$cname."-����".$line);
	//������Ͽ���˥��顼�ˤʤä����
	if($result == false){
		Db_Query($db_con,"ROLLBACK;");
		exit;
	}


	
	Db_Query($db_con, "COMMIT");
	header("Location: ./2-1-115.php?client_id=$client_id");
	
//****************************
//�ԥ��ꥢ����
//****************************
}elseif($_POST[clear_line]!=""){
	Clear_Line_Data($form,$_POST[clear_line]);


//****************************
//���׽�������
//****************************
}elseif($action == "����"){
	$form_ad_rest_price = Minus_Numformat(Advance_Offset_Claim($db_con, $g_today, $client_id, $claim_div));
	$con_data2["form_ad_rest_price"] = $form_ad_rest_price;    //������Ĺ�
}

/****************************/
//POST������ͤ��ѹ�
/****************************/
$form->setConstants($con_data2);

if($_POST["client_search_flg"]){
	$con_intro_ac_div["intro_ac_div[]"] = 1;
	$form->setConstants($con_intro_ac_div);
}

/****************************/
//javascript
/****************************/
$java_sheet  = Js_Keiyaku();
$java_sheet .= Create_Js("daiko");


//�켰�˥����å����դ�����硢��۷׻�����
$java_sheet  .= "function Set_num(row, coax, daiko_coax){\n";
//FC��ľ��Ƚ��
if($group_kind == 2){
    //ľ�Ĥϡ���������θ�����׻�
    //$java_sheet  .= "    Mult_double3('form_goods_num1['+row+']','form_sale_price['+row+'][1]','form_sale_price['+row+'][2]','form_sale_amount['+row+']','form_trade_price['+row+'][1]','form_trade_price['+row+'][2]','form_trade_amount['+row+']','form_issiki['+row+']',coax,false,row,'',daiko_coax);\n";
    $java_sheet  .= "    Mult_double_All(coax, false, daiko_coax, row);\n";
}else{
    //�ƣäϡ����̤ΰ켰�η׻�
    $java_sheet  .= "    Mult_double2('form_goods_num1['+row+']','form_sale_price['+row+'][1]','form_sale_price['+row+'][2]','form_sale_amount['+row+']','form_trade_price['+row+'][1]','form_trade_price['+row+'][2]','form_trade_amount['+row+']','form_issiki['+row+']',coax,false);\n";
}
$java_sheet  .= "}\n\n";


//�ե�����롼�׿�
$loop_num = array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");

$form_load  = 'onLoad="'. $form_load .' ad_offset_radio_disable(); "';

/****************************/
//HTML�إå�
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//HTML�եå�
/****************************/
$html_footer = Html_Footer();

/****************************/
//���̥إå�������
/****************************/
$page_title .= "(��".Kiyaku_Data_Count($db_con,1)."��)";
$page_title .= "��".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_title .= "��".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
//$page_title .= "��".$form->_elements[$form->_elementIndex[all_button]]->toHtml();
$page_header = Create_Header($page_title);

// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form��Ϣ���ѿ���assign
$smarty->assign('form',$renderer->toArray());
$smarty->assign('num',$foreach_num);
$smarty->assign('loop_num',$loop_num);

//����¾���ѿ���assign
$smarty->assign('var',array(
	'html_header'     => "$html_header",
	'page_menu'       => "$page_menu",
	'page_header'     => "$page_header",
	'html_footer'     => "$html_footer",
	'java_sheet'      => "$java_sheet",
	'flg'             => "$flg",
	'last_day'        => "$last_day",
	'ac_name'         => "$ac_name",
	'return_flg'      => "$return_flg",
	'get_flg'         => "$get_flg",
	'client_id'       => "$client_id",
	'form_load'       => "$form_load",
	'trade_error_flg' => "$trade_error_flg",
	'auth_r_msg'      => "$auth_r_msg",
	'duplicat_mesg'   => "$duplicat_mesg",
	'advance_mesg'    => "$advance_mesg",
	'group_kind'      => "$group_kind"
));
//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));
/*
$e_time = microtime();
echo "$s_time"."<br>";
echo "$t_time"."<br>";
echo "$e_time"."<br>";
print_array($_POST);
*/

//���Ҥη���ID�������å�����
function Chk_Contract_Id($db_con,$contract_id){

	$sql = "
		SELECT 
		COUNT(contract_id) 
		FROM t_contract 
		WHERE shop_id = $_SESSION[client_id]
		AND contract_id = '$contract_id'; 
	";
	$result = Db_Query($db_con, $sql);
	$count  = pg_fetch_result($result,0,0);

	//0��ξ���TOP������
	if ($count == "0"){
		header("Location: ".TOP_PAGE_F);
		exit;
	}

}




?>