<?php
/**
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2006/12/07      ban_0051    suzuki      ô����CD�θ�����､��
 *
 *
 * ���� �����˴ؤ���ǡ������������
 *
 * ���� ��5������count�����ꤵ�줿���ϡ����פ��������֤��ޤ���
 *
 * @param string   $db_con     DB��³�꥽����
 * @param array    $where      SQL���ʸ
 * @param string   $page_snum  ɽ�����Ϸ��
 * @param string   $page_enum  ɽ����λ���
 * @param string   $kind       ����ͤμ���
 *
 * @return array               ����ǡ���
 */
 
function Get_Slip_Data($db_con, $where, $page_snum, $page_enum,$kind=""){
	$offset = $page_snum-1; //ɽ�����ʤ����
	$limit  = ($page_enum - $page_snum) +1;    //��ڡ���������η��

	// ����POST�ǡ�����0���
	$_POST["form_round_day"] = Str_Pad_Date($_POST["form_round_day"]);
	$_POST["form_claim_day"] = Str_Pad_Date($_POST["form_claim_day"]);

	//�ѿ����
	$shop_id          = $_SESSION[client_id];
	$display_num      = $_POST["form_display_num"];           //ɽ�����
	$charge_branch_id = $_POST["form_client_branch"];         //�ܵ�ô����ŹID
	$attach_branch_id = $_POST["form_attach_branch"];         //��°��ŹID

	$client_cd1       = $_POST["form_client"]["cd1"];         //������CD1
	$client_cd2       = $_POST["form_client"]["cd2"];         //������CD2                           
	$client_name      = $_POST["form_client"]["name"];        //������̾                           
	$staff_cd         = $_POST["form_round_staff"]["cd"];     //���ô���ԥ�����
	$staff_id         = $_POST["form_round_staff"]["select"]; //���ô����ID
	$part_id          = $_POST["form_part"];                  //����

	$claim_cd1        = $_POST["form_claim"]["cd1"];          //������CD1
	$claim_cd2        = $_POST["form_claim"]["cd2"];          //������CD2                           
	$claim_name       = $_POST["form_claim"]["name"];         //������̾                           

	$staff_cd_csv     = $_POST["form_multi_staff"];           //���ô���ԥ�����(����޶��ڤ�)
	$ware_id          = $_POST["form_ware"];                  //����

	$round_day_sy     = $_POST["form_round_day"]["sy"];       //�������ʳ���ǯ�� 
	$round_day_sm     = $_POST["form_round_day"]["sm"];       //�������ʳ��Ϸ��
	$round_day_sd     = $_POST["form_round_day"]["sd"];       //�������ʳ�������
	$round_day_ey     = $_POST["form_round_day"]["ey"];       //�������ʽ�λǯ�� 
	$round_day_em     = $_POST["form_round_day"]["em"];       //�������ʽ�λ���
	$round_day_ed     = $_POST["form_round_day"]["ed"];       //�������ʽ�λ����
	$round_day_s      = $round_day_sy."-".$round_day_sm."-".$round_day_sd; //�������ʳ��ϡ�
	$round_day_e      = $round_day_ey."-".$round_day_em."-".$round_day_ed; //�������ʽ�λ��

	$claim_day_sy     = $_POST["form_claim_day"]["sy"];       //�������ʳ���ǯ�� 
	$claim_day_sm     = $_POST["form_claim_day"]["sm"];       //�������ʳ��Ϸ��
	$claim_day_sd     = $_POST["form_claim_day"]["sd"];       //�������ʳ�������
	$claim_day_ey     = $_POST["form_claim_day"]["ey"];       //�������ʽ�λǯ�� 
	$claim_day_em     = $_POST["form_claim_day"]["em"];       //�������ʽ�λ���
	$claim_day_ed     = $_POST["form_claim_day"]["ed"];       //�������ʽ�λ����
	$claim_day_s      = $claim_day_sy."-".$claim_day_sm."-".$claim_day_sd; //�������ʳ��ϡ�
	$claim_day_e      = $claim_day_ey."-".$claim_day_em."-".$claim_day_ed; //�������ʽ�λ��

	$act_cd1          = $_POST["form_charge_fc"]["cd1"];      //������CD1
	$act_cd2          = $_POST["form_charge_fc"]["cd2"];      //������CD2                           
	$act_name         = $_POST["form_charge_fc"]["name"];     //������̾                           
	$act_id           = $_POST["form_charge_fc"]["select"]["1"];   //������id                           

	$charge_fc_cd1    = $_POST["form_charge_fc"]["cd1"];      //������cd1
	$charge_fc_cd2    = $_POST["form_charge_fc"]["cd2"];      //������cd2
	$charge_fc_name   = $_POST["form_charge_fc"]["name"];     //������̾
	$charge_fc_id     = $_POST["form_charge_fc"]["select"]["1"];   //������ID

	$slip_out         = $_POST["slip_out"];                   //��ɼ����
	$slip_flg         = $_POST["slip_flg"];                   //ȯ�Ծ���
	$ord_no_s         = $_POST["ord_no"]["s"];                //��ɼ�ֹ�ʳ��ϡ�
	$ord_no_e         = $_POST["ord_no"]["e"];                //��ɼ�ֹ�ʽ�λ��
	$contract_div     = $_POST["contract_div"];               //�����ʬ

	//������ɽ�����ּ���
	$cal_array = Cal_range($db_con,$shop_id,true);
	$end_day   = $cal_array[1];     //�оݽ�λ����

	//************************************************
	//����������
	//************************************************
	//ô����ŹID
	if($charge_branch_id != null){
		$where_sql .= "  AND t_client.charge_branch_id = $charge_branch_id \n";
	}

	//��°��ŹID
	if($attach_branch_id != null){
		$where_sql .= "  AND t_staff1.staff_id IN (SELECT \n";
		$where_sql .= "                                     t_attach.staff_id  \n";
		$where_sql .= "                                 FROM \n";
		$where_sql .= "                                     t_part \n";
		$where_sql .= "                                         INNER JOIN ";
		$where_sql .= "                                     t_attach \n";
		$where_sql .= "                                     ON t_part.part_id = t_attach.part_id ";
		$where_sql .= "                                 WHERE \n";
		$where_sql .= "                                     branch_id = $attach_branch_id \n";
		$where_sql .= "                                 ) \n";
	
		//�����ɼ��ɽ�����ʤ�
		if($group_kind == '2'){
			$where_sql  .= " AND t_aorder_h.act_id IS NULL\n";
		}
	}

	//���ô���ԥ����ɤ����Ϥ��줿���
	if($staff_cd != null || $staff_id != null || $staff_cd_csv != null){
		$where_sql .= " AND t_staff1.staff_id IN (\n";
		$where_sql .= "         SELECT\n";
		$where_sql .= "             t_staff.staff_id\n";
		$where_sql .= "         FROM\n";
		$where_sql .= "             t_attach\n";
		$where_sql .= "                 INNER JOIN\n";
		$where_sql .= "             t_staff\n";
		$where_sql .= "             ON t_attach.staff_id = t_staff.staff_id\n";
		$where_sql .= "             WHERE t_attach.shop_id = $shop_id\n";

		//���ô���ԡʥ���޶��ڤ��
		if($staff_cd_csv != null){
		    $where_sql .= "        AND t_staff.charge_cd IN ($staff_cd_csv)\n";
		}
		//���ô���Ԥ����򤵤�Ƥ������
		if($staff_id != null){
		    $where_sql .= "        AND t_staff.staff_id = $staff_id\n";
		}
		//���ô���ԥ�����
		if($staff_cd != null){
		    $where_sql .= "        AND t_staff.charge_cd = $staff_cd \n";
		}
		$where_sql .= ")\n";
	
		if($group_kind == '2'){
		    #$where2_sql  .= " AND t_aorder_h.act_id IS NULL\n";
		}
	}
 
	//�������򤵤줿���
	if($part_id != NULL){
		$where_sql .= " AND t_staff1.staff_id IN (";
		$where_sql .= "             SELECT\n";
		$where_sql .= "                 staff_id\n";
		$where_sql .= "             FROM\n";
		$where_sql .= "                 t_attach\n";
		$where_sql .= "             WHERE\n";
		$where_sql .= "                 t_attach.part_id = $part_id\n";
		$where_sql .= "             )\n";
	
		/*
		* ����
		* �����ա�������BɼNo.��������ô���ԡ��������ơ�
		* ��2006/11/01��02-036��������suzuki-t��  ������¦������ô���Ԥθ�����Ǥ���褦���ѹ�
		*/
		if($group_kind == '2'){
		    $where2_sql .= " AND t_aorder_h.act_id IS NULL\n";
		}
	}

	//�Ҹ�
	if($ware_id != null){
		$where_sql .= "  AND t_aorder_h.ware_id = $ware_id \n";
	}

	//�������ʳ��ϡˤ����ꤵ�줿���
	if($round_day_s != "--"){
		$where_sql .= " AND '$round_day_s' <= t_aorder_h.ord_time \n";
	}

	//�������ʽ�λ�ˤ����ꤵ�줿���
	if($round_day_e != "--"){
		$where_sql .= " AND t_aorder_h.ord_time <= '$round_day_e' \n";
	}


	//�������ʳ��ϡˤ����ꤵ�줿���
	if($claim_day_s != "--"){
		$where_sql .= " AND '$claim_day_s' <= t_aorder_h.arrival_day \n";
	}

	//�������ʽ�λ�ˤ����ꤵ�줿���
	if($claim_day_e != "--"){
		$where_sql .= " AND t_aorder_h.arrival_day <= '$claim_day_e' \n";
	}

	//�����襳����1
	if($client_cd1 != null){
		$where_sql  .= " AND t_aorder_h.client_cd1 LIKE '$client_cd1%'";
	}
	//�����襳����2
	if($client_cd2 != null){
			$where_sql  .= " AND t_aorder_h.client_cd2 LIKE '$client_cd2%'";
	}
	//������̾
	if($client_name != null){
		$where_sql  .= " AND ";
			$where_sql .= "    ( t_aorder_h.client_cname LIKE '%$client_name%' ";
			$where_sql .= "      OR ";
			$where_sql .= "      t_aorder_h.client_name LIKE '%$client_name%' ";
			$where_sql .= "      OR ";
			$where_sql .= "      t_aorder_h.client_name2 LIKE '%$client_name%' ";
			$where_sql .= "    ) ";
	}

	//�����褬���ꤵ�줿���
	if($claim_id != null || $claim_cd1 != null || $claim_cd2 != null || $claim_name != null){
		$where_sql .= " AND t_aorder_h.claim_id IN (\n";
		$where_sql .= "                         SELECT\n";
		$where_sql .= "                             client_id \n";
		$where_sql .= "                         FROM \n";
		$where_sql .= "                             t_client \n";
		$where_sql .= "                         WHERE t_client.t_client_div != '2'\n";
	
		//������ID
		if($claim_id != null){
			$where_sql .= "                       AND t_client.client_id = $claim_id ";
		}
		//������CD��
		if($claim_cd1 != null){    
			$where_sql .= "                       AND t_client.client_cd1 LIKE '%$claim_cd1' ";
		}
		//������CD2
		if($claim_cd2 != null){
			$where_sql .= "                       AND t_client.client_cd2 LIKE '%$claim_cd2' ";
		}
		//������̾
		if($claim_name != null){
			$where_sql .= "                       AND \n";
			$where_sql .= "                           (t_client.client_name LIKE '%$claim_name%' \n";
			$where_sql .= "                           OR t_client.client_name2 LIKE '%$claim_name%' \n";
			$where_sql .= "                           OR t_client.shop_name LIKE '%$claim_name%' \n";
			$where_sql .= "                           OR t_client.client_cname LIKE '%$claim_name%')) \n";
		}
	
		$where_sql .= ")\n";
	}
 
	//�����ID
	if($charge_fc_id != null){
		$where_sql .= " AND t_aorder_h.act_id = $charge_fc_id ";
	}
	//�����CD��
	if($charge_fc_cd1 != null){    
		$where_sql .= " AND t_aorder_h.act_cd1 LIKE '%$charge_fc_cd1' ";
	}
	//�����CD2
	if($charge_fc_cd2 != null){
		$where_sql .= " AND t_aorder_h.act_cd2 LIKE '%$charge_fc_cd2' ";
	}
	//�����̾
	if($charge_fc_name != null){
		$where_sql .= " AND t_aorder_h.act_id IN (SELECT \n";
		$where_sql .= "                             client_id \n";
		$where_sql .= "                         FROM \n";
		$where_sql .= "                             t_client \n";
		$where_sql .= "                         WHERE \n";
		$where_sql .= "                             (t_client.client_name LIKE '%$charge_fc_name%' \n";
		$where_sql .= "                             OR t_client.client_name2 LIKE '%$charge_fc_name%' \n";
		$where_sql .= "                             OR t_client.shop_name LIKE '%$charge_fc_name%' \n";
		$where_sql .= "                             OR t_client.client_cname LIKE '%$charge_fc_name%')) \n";
	}

	//�̾���ɼȽ���1:�̾� 2:���� 3:¾ɽ��
	if($slip_out != "0"){
		$where_sql .= " AND t_client.slip_out = '$slip_out' \n";
	}

	//ȯ�Ժ�Ƚ��(t:ȯ�Ժ� f:̤ȯ��)
	if($slip_flg != "0"){
		$where_sql .= " AND t_aorder_h.slip_flg = '$slip_flg' \n";
	}

	//��ɼ�ֹ�ʳ��ϡ�
	if($ord_no_s != null){
		$where_sql .= " AND  '$ord_no_s' <= t_aorder_h.ord_no \n";
	}
	//��ɼ�ֹ�ʽ�λ��
	if($ord_no_e != null){
		$where_sql .= " AND t_aorder_h.ord_no <= '$ord_no_e' \n";
	}

	//************************************************
	//���������»�
	//************************************************
	//�����إå�������SQL
	$sql_column  = "SELECT \n";
	$sql_column .= "    '$offset' AS offset,\n";
	$sql_column .= "    t_aorder_h.aord_id,\n";
	$sql_column .= "    t_aorder_h.ord_no,\n";
	$sql_column .= "    t_aorder_h.ord_time,\n";
	$sql_column .= "    t_aorder_h.client_cname,\n";
	$sql_column .= "    t_staff1.charge_cd  AS staff_cd1  ,\n"; 
	$sql_column .= "    t_staff1.staff_name AS staff_name1,\n";
	$sql_column .= "    t_aorder_h.slip_flg, ";
	$sql_column .= "    t_aorder_h.act_id, ";
	$sql_column .= "    (t_aorder_h.act_cd1 || '-' || t_aorder_h.act_cd2) AS act_cd, ";
	$sql_column .= "    t_aorder_h.act_name, ";
	$sql_column .= "    t_aorder_h.route, ";
	$sql_column .= "    t_aorder_h.client_cd1, ";
	$sql_column .= "    t_aorder_h.client_cd2, ";
	$sql_column .= "    CASE t_client.slip_out ";
//	$sql_column .= "    CASE t_aorder_h.slip_out ";
	$sql_column .= "        WHEN '1' THEN '�̾�' ";
	$sql_column .= "        WHEN '2' THEN '����' ";
	$sql_column .= "        WHEN '3' THEN '¾ɼ' ";
	$sql_column .= "    END AS slip_out, \n";
	$sql_column .= "    t_aorder_h.slip_out_day ";

	$sql  = "FROM \n";
	$sql .= "    t_aorder_h \n";
	$sql .= "    INNER JOIN t_client ON t_aorder_h.client_id  = t_client.client_id \n";

	//���ô����1
	$sql .= "    LEFT JOIN \n";
	$sql .= "        (SELECT \n";
	$sql .= "             t_aorder_staff.aord_id,\n";
	$sql .= "             t_staff.staff_id, \n";
	$sql .= "             t_staff.charge_cd, \n";
	$sql .= "             t_aorder_staff.staff_name \n";
	$sql .= "         FROM \n";
	$sql .= "             t_aorder_staff \n";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id \n";
	$sql .= "         WHERE \n";
	$sql .= "             t_aorder_staff.staff_div = '0'\n";
	$sql .= "         AND \n";
	$sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
	$sql .= "        )AS t_staff1 ON t_staff1.aord_id = t_aorder_h.aord_id \n";

/*
	//���ô����2
	$sql .= "    LEFT JOIN \n";
	$sql .= "        (SELECT \n";
	$sql .= "             t_aorder_staff.aord_id,\n";
	$sql .= "             t_staff.charge_cd, \n";
	$sql .= "             t_aorder_staff.staff_name \n";
	$sql .= "         FROM \n";
	$sql .= "             t_aorder_staff \n";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id \n";
	$sql .= "         WHERE \n";
	$sql .= "             t_aorder_staff.staff_div = '1'\n";
	$sql .= "         AND \n";
	$sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
	$sql .= "        )AS t_staff2 ON t_staff2.aord_id = t_aorder_h.aord_id \n";

	//���ô����3
	$sql .= "    LEFT JOIN \n";
	$sql .= "        (SELECT \n";
	$sql .= "             t_aorder_staff.aord_id,\n";
	$sql .= "             t_staff.charge_cd, \n";
	$sql .= "             t_aorder_staff.staff_name \n";
	$sql .= "         FROM \n";
	$sql .= "             t_aorder_staff \n";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id \n";
	$sql .= "         WHERE \n";
	$sql .= "             t_aorder_staff.staff_div = '2'\n";
	$sql .= "         AND \n";
	$sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
	$sql .= "        )AS t_staff3 ON t_staff3.aord_id = t_aorder_h.aord_id \n";

	//���ô����4
	$sql .= "    LEFT JOIN \n";
	$sql .= "        (SELECT \n";
	$sql .= "             t_aorder_staff.aord_id,\n";
	$sql .= "             t_staff.charge_cd, \n";
	$sql .= "             t_aorder_staff.staff_name \n";
	$sql .= "         FROM \n";
	$sql .= "             t_aorder_staff \n";
	$sql .= "             LEFT JOIN t_staff ON t_staff.staff_id = t_aorder_staff.staff_id \n";
	$sql .= "         WHERE \n";
	$sql .= "             t_aorder_staff.staff_div = '3'\n";
	$sql .= "         AND \n";
	$sql .= "             t_aorder_staff.sale_rate IS NOT NULL\n";
	$sql .= "        )AS t_staff4 ON t_staff4.aord_id = t_aorder_h.aord_id \n";
*/
	$sql .= "WHERE \n";
	
	$sql .= "    ( \n";
	//ľ�Ĥξ��
	if($_SESSION["group_kind"] == '2'){
		$sql .= "    t_aorder_h.shop_id = $_SESSION[client_id] AND t_aorder_h.confirm_flg = 'f' \n";       //������ɼ

		//�����ʬ�����ꤵ�줿���
		if($contract_div != "0"){
			$sql .= " AND t_aorder_h.contract_div = '$contract_div' \n";
		}

	//FC�ξ��
	}else{

		//������ɼ
		if($contract_div == "0" || $contract_div == 1){
			$sql .= " (t_aorder_h.shop_id = $_SESSION[client_id] AND t_aorder_h.contract_div = 1 AND t_aorder_h.confirm_flg = 'f')";
		}
		
		if($contract_div == "0"){
			$sql .= " OR";
		}
		
		//����饤�������ɼ
		if($contract_div == "0" || $contract_div == 2){
			$sql .= " (t_aorder_h.act_id  = $_SESSION[client_id] AND t_aorder_h.contract_div = 2 AND t_aorder_h.trust_confirm_flg = 'f')";
		}

	}
	$sql .= "    ) \n";

	/*
	//ľ�Ĥξ��
	if($_SESSION["group_kind"] == '2'){
		//ľ�Ĥξ��ϡ��̾����饤�󡦥��ե饤������ɽ������١��ǥե���ȤϷ����ʬ�ϻ��ꤷ�ʤ�
		$sql .= "    t_aorder_h.shop_id = $shop_id ";
		$sql .= "AND \n";
		$sql .= "    t_aorder_h.confirm_flg = 'f' \n";       //����ե饰

	//FC�ξ��
	}else{
		//�����ʬ�������ơפޤ��ϡ��̾�פξ��
		if($con_div_sql == NULL || $con_div == '2'){
			//�̾���ɼ
			$sql .= "    t_aorder_h.shop_id = $shop_id ";
			$sql .= "AND \n";
			$sql .= "    t_aorder_h.contract_div = '1' \n";
			$sql .= "AND \n";
			$sql .= "    t_aorder_h.confirm_flg = 'f' \n";   //����ե饰

		//����饤����ɼ
		}else if($con_div == '3'){
			$sql .= "    t_aorder_h.act_id = $shop_id \n";
			$sql .= "AND \n";
			$sql .= "    t_aorder_h.contract_div = '2' \n";
			$sql .= "AND \n";
			$sql .= "    t_aorder_h.trust_confirm_flg = 'f' \n";   //����ե饰(������)
		}
	}
	*/
	$sql .= "AND \n";
	$sql .= "    (t_aorder_h.ps_stat = '1' OR t_aorder_h.ps_stat = '2') \n"; //̤����or �����
	$sql .= "AND \n";
	$sql .= "    t_aorder_h.del_flg = 'f' \n";                 //�����ɼ�Ǥʤ�
	$sql .= "AND \n";
	$sql .= "    t_aorder_h.ord_no IS NOT NULL \n";            //���ֺ���ɼ
	$sql .= "AND \n";
	$sql .= "   t_aorder_h.ord_time <= '$end_day' \n";         //��������ɽ����λ��
	$sql .= $where_sql;
	$sql_another  = "\n ORDER BY \n";

/*
	$sql_another  = "\n ORDER BY \n";
	$sql_another .= "    staff_cd1  ,\n"; 
	$sql_another .= "    t_aorder_h.ord_time,\n";
	$sql_another .= "    t_aorder_h.ord_no \n";
	//$sql_another .= " 5,3,2 ";
*/  
  switch ($where["hdn_sort_col"]){
        case "sl_client_cd":
	        $sql_another .= "   t_aorder_h.client_cd1, \n";
	        $sql_another .= "   t_aorder_h.client_cd2, \n";
	        $sql_another .= "   staff_cd1, \n"; 
	        $sql_another .= "   t_aorder_h.ord_time, \n";
	        $sql_another .= "   t_aorder_h.ord_no \n";
            break;
        case "sl_client_name":
	        $sql_another .= "   t_aorder_h.client_cname, \n";
	        $sql_another .= "   staff_cd1, \n"; 
	        $sql_another .= "   t_aorder_h.ord_time, \n";
	        $sql_another .= "   t_aorder_h.ord_no \n";
            break;
        case "sl_slip":
	        $sql_another .= "   t_aorder_h.ord_no, \n";
	        $sql_another .= "   staff_cd1, \n"; 
	        $sql_another .= "   t_aorder_h.ord_time \n";
            break;
        case "sl_round_day":
	        $sql_another .= "   t_aorder_h.ord_time, \n";
	        $sql_another .= "   staff_cd1, \n"; 
	        $sql_another .= "   t_aorder_h.ord_no \n";
            break;
        case "sl_staff":
	        $sql_another .= "   staff_cd1, \n"; 
	        $sql_another .= "   t_aorder_h.ord_time, \n";
	        $sql_another .= "   t_aorder_h.ord_no \n";
            break;
        case "sl_act_client_cd":
	        $sql_another .= "   t_aorder_h.act_cd1, \n";
	        $sql_another .= "   t_aorder_h.act_cd2, \n";
	        $sql_another .= "   staff_cd1, \n"; 
	        $sql_another .= "   t_aorder_h.ord_time, \n";
	        $sql_another .= "   t_aorder_h.ord_no \n";
            break;
        case "sl_act_client_name":
	        $sql_another .= "   t_aorder_h.act_name, \n";
	        $sql_another .= "   t_aorder_h.act_cd1, \n";
	        $sql_another .= "   t_aorder_h.act_cd2, \n";
	        $sql_another .= "   staff_cd1, \n"; 
	        $sql_another .= "   t_aorder_h.ord_time, \n";
	        $sql_another .= "   t_aorder_h.ord_no \n";
            break;
    }

	if ($where[form_display_num] != "1") {
		$sql_another .= "LIMIT $limit OFFSET $offset ";
	}

	//�����˳��������ַ���פ��֤��ƽ�λ
	if ($kind == "count") {
		$sql_column = "SELECT COUNT(t_aorder_h.aord_id) ";
		$exec_sql   = "$sql_column"."$sql";
		$result     = Db_Query($db_con, $exec_sql);
		$slip_data       = pg_fetch_result($result, 0 ,0);
		return $slip_data;

	//������ �ǡ��������
	} else {
		$exec_sql   = "$sql_column"."$sql"."$sql_another";
		$result = Db_Query($db_con, $exec_sql);

		if(pg_num_rows($result) != "0"){
			$slip_data   = pg_fetch_all($result);
		}

	}


	//print_array($slip_data);
	return $slip_data;
}


//�ǡ�����ɽ���Ѥ��Ѵ�
function HTML_Slip_Data($data,$form){

	//�ǡ�����ͭ����
	if(count($data) != "0"){

		foreach( $data as $key => $val ){
	
			//�����Ԥ���
			if ($key%2 == "0"){
				$data[$key]["class"] = "Result1";
				
			//����Ԥ���
			}else{
				$data[$key]["class"] = "Result2";
			}
	
			$aord_id = $val[aord_id];
			$data[$key][no]           = $val[offset]+$key+1;
			//$data[$key][no]           = $key+1;
			$data[$key][ord_no]       = "<a href=\"2-2-106.php?aord_id[0]=$aord_id&back_display=output\">".$val[ord_no]."</a>";
			if($val[staff_cd1] != NULL){
				$data[$key][staff_cd1]    = str_pad($val[staff_cd1], 4, 0, STR_POS_LEFT);
			}
			$data[$key][client_cname] = htmlspecialchars($val[client_cname]);
			if($val[act_cd] == "-"){
				$data[$key][act_cd]    = "";
			}
			$data[$key][act_name]     = htmlspecialchars($val[act_name]);
			$data[$key][staff_name1]  = htmlspecialchars($val[staff_name1]);

			//������ɼ�ξ��
			if ($val[slip_out] == "����" || $val[slip_out] == "¾ɼ"){

			//���̾�פ��ġ�̤ȯ�ԡפξ��
			}elseif( $val[slip_out] == "�̾�" && $val[slip_flg] == "f" ){
				$form->addElement("advcheckbox", "form_slip_check[$key]", NULL, NULL, NULL, array("f", "$aord_id"));
				$form_slip_check_list[$key] = $aord_id;

			//���̾�פ��ġ�ȯ�Ժѡפξ��Ͻ�������ɽ������
			}else{
				$form->addElement("static", "form_slip_check[$key]", NULL,$val[slip_out_day]);
				$chk_form["form_slip_check"][$key] = $val[slip_out_day]; 

				$form->addElement("advcheckbox", "form_re_slip_check[$key]", NULL, NULL, NULL, array("f", "$aord_id"));
				$form_re_slip_check_list[$key] = $aord_id;
			}
	
		}
		//ȯ�����򥻥åȡʸ������������form_slip_check��f�����åȤ���뤿���
		$form->setConstants($chk_form);

	}

	// �����ɼȯ��ALL��(JS�����)
	$javascript  = Create_Allcheck_Js ("All_Check_Slip","form_slip_check",$form_slip_check_list);
	// ��ȯ��ALL��(JS�����)
	$javascript .= Create_Allcheck_Js ("All_Check_Re_Slip","form_re_slip_check",$form_re_slip_check_list);

	$disp_data[js]   = $javascript;
	$disp_data[html] = $data;

	return $disp_data;
}


?>