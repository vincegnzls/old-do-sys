<?php
/**
 *
 * ��Ͽ�ܥ��󤬲����줿Ƚ�꤫��validate����������ޤǤΥ����å�
 * �ʽ�Ʊ���¹ԥ����å���
 *
 * �ƽи�
 * ��2-2-107��ͽ��ǡ���������
 * ��2-2-201�ʼ����ɼȯ�ԡ�
 * ��2-2-202�������ɼ������
 * ��2-2-118��ͽ�����
 *
 */
/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2007/05/21      B0702-055   kajioka-h   ���ʾ��󤬼����Ǥ��ʤ��ä����ˡ������ƥ������̾�������ꥢ����ʤ��Х�����
 *  2007/05/23      xx-xxx      kajioka-h   �����ɼ��ͽ����ΤߤΥ����å����ɲáʰ�ư��
 *  2007/06/09      ����¾14    kajioka-h   ������
 *  2007-07-12                  fukuda      �ֻ�ʧ���פ�ֻ������פ��ѹ�
 *  2009-09-08                  aoyama-n    �Ͱ���ǽ�ɲ�
 *
 */

/****************************/
//���顼�����å�(PHP)
/****************************/
//$error_flg = false;            //���顼Ƚ��ե饰

//�����ɼ��ͽ����Τ�
if($hand_slip_flg == true || $hand_plan_flg == true){

    //������
    //��ɬ�ܥ����å�
    $form->addGroupRule('form_client', array(
            'cd1' => array(
                    array($h_mess[27],'required')
            ),
            'cd2' => array(
                    array($h_mess[27],'required')
            ),
            'name' => array(
                    array($h_mess[27],'required')
            )
    ));
    $injust = Injustice_check($db_con, "client", $client_id, $_POST["form_client"]["cd1"], $_POST["form_client"]["cd2"]);
    if($injust == false){
        $form->setElementError("form_client", $h_mess[44]);
    }


    //���ҽ��ξ��
    if($contract_div == "1"){
        //�в��Ҹ�
        //��ɬ�ܥ����å�
        $form->addRule('form_ware_select', $h_mess[29], 'required');

        if($hand_slip_flg == true){
            //���ô����
            //��ɬ�ܥ����å�
            $form->addRule('form_ac_staff_select',$h_mess[31],'required');
        }

        //�̾���ξ�硢������ϡ�ȯ�����ʤ��פΤ�
        $form->addRule('act_div',$h_mess[62],"regex", '/^[1]$/');


    //��Ԥξ��
    }else{
        //�����
        //��ɬ�ܥ����å�
        $form->addGroupRule('form_daiko', array(
                'cd1' => array(
                        array($h_mess[1], 'required')
                ),
                'cd2' => array(
                        array($h_mess[1], 'required')
                ),
                'name' => array(
                        array($h_mess[1], 'required')
                )
        ));
        //�������������å�
        if($act_id != null){
            $injust = Injustice_check($db_con, "client", $act_id, $_POST["form_daiko"]["cd1"], $_POST["form_daiko"]["cd2"]);
            if($injust == false){
                $form->setElementError("form_daiko", $h_mess[33]);
            }
        }


        //��԰�����
        //������ʸ���ۡˤξ��
        if($act_div == "2" ){
            $form->addRule('act_request_price', $h_mess[60], "required");
        }
        $form->addRule('act_request_price', $h_mess[60], "regex", '/^[0-9]+$/');

        //������ʡ�ˤξ��
        if($act_div == "3" ){
            $form->addRule('act_request_rate', $h_mess[61], "required");
        }
        $form->addRule('act_request_rate', $h_mess[61], "regex", '/^[0-9]+$/');
        $form->registerRule("check_percent", "function", "Check_Percent_Qf");
        $form->addRule("act_request_rate", $h_mess[61], "check_percent");

        //��Ԥξ�硢������ϡ�ȯ������פΤ�
        if($act_div == "1"){
            $form->setElementError("act_div[]", "���ե饤����Ԥ���԰������ϡ�ȯ������פ����򤷤Ʋ�������");
        }

    }

}//���ͽ����Τߥ����å������


//��ͽ������
if($delivery_day_y != null && $delivery_day_m != null && $delivery_day_d != null){
    $deli_day_flg = false;    //�����Ƚ�ꤹ��

    $delivery_day_y = str_pad($delivery_day_y,4, 0, STR_PAD_LEFT);
    $delivery_day_m = str_pad($delivery_day_m,2, 0, STR_PAD_LEFT);
    $delivery_day_d = str_pad($delivery_day_d,2, 0, STR_PAD_LEFT);
    $delivery_day_ymd = $delivery_day_y."-".$delivery_day_m."-".$delivery_day_d;

    //����Ƚ��
    if(!ereg("^[0-9]{4}$",$delivery_day_y) || !ereg("^[0-9]{2}$",$delivery_day_m) || !ereg("^[0-9]{2}$",$delivery_day_d)){
        $form->setElementError("form_delivery_day",$h_mess[35]);
        $deli_day_flg = true;    //�����Ƚ�ꤷ�ʤ�
    }

    //��ʸ��������å�
    $delivery_day_y = (int)$delivery_day_y;
    $delivery_day_m = (int)$delivery_day_m;
    $delivery_day_d = (int)$delivery_day_d;
    if(!checkdate($delivery_day_m,$delivery_day_d,$delivery_day_y)){
        $form->setElementError("form_delivery_day",$h_mess[35]);
        $deli_day_flg = true;    //�����Ƚ�ꤷ�ʤ�
    }

    //�����ƥ೫����Ƚ��
    if(START_DAY > $delivery_day_ymd){
        //�����ƥ೫����������ʤ饨�顼
        $form->setElementError("form_delivery_day",$h_mess[38]);
        $deli_day_flg = true;    //�����Ƚ�ꤷ�ʤ�
    }

    //�������̤��Ƚ��
    if($g_today < $delivery_day_ymd){
        //�����ɼ
        if($hand_slip_flg){
            //�������̤��ʤ饨�顼
            $form->setElementError("form_delivery_day", "���׾���".$h_mess[69]);
            $deli_day_flg = true;    //�����Ƚ�ꤷ�ʤ�

        //�����ɼ�������ˤ������껦����
        }elseif($plan_sale_flg){
            //�������̤��ʤ饨�顼
            $form->setElementError("form_delivery_day", "ͽ������".$h_mess[69]);
            $deli_day_flg = true;    //�����Ƚ�ꤷ�ʤ�
        }
    }

    //�������˥��顼��̵����з����Ƚ��¹�
    if($deli_day_flg == false){
        //��������������������褫�����å�
        if(Check_Monthly_Renew($db_con,$client_id,"1",$delivery_day_y,$delivery_day_m,$delivery_day_d) == false){
            $form->setElementError("form_delivery_day",$h_mess[39]);
        }
    }
}

//�����ʬ���̾�orľ�ĤΤ�
if($contract_div == '1' || $group_kind == 2){
    //��������
    if($request_day_y != null && $request_day_m != null && $request_day_d != null){
        $requ_day_flg = false;    //�����Ƚ�ꤹ��

        $request_day_y = str_pad($request_day_y,4, 0, STR_PAD_LEFT);
        $request_day_m = str_pad($request_day_m,2, 0, STR_PAD_LEFT);
        $request_day_d = str_pad($request_day_d,2, 0, STR_PAD_LEFT);
        $request_day_ymd = $request_day_y."-".$request_day_m."-".$request_day_d;

        //����Ƚ��
        if(!ereg("^[0-9]{4}$",$request_day_y) || !ereg("^[0-9]{2}$",$request_day_m) || !ereg("^[0-9]{2}$",$request_day_d)){
            $form->setElementError("form_request_day",$h_mess[25]);
            $requ_day_flg = true;    //�����Ƚ�ꤷ�ʤ�
        }

        //��ʸ��������å�
        $request_day_y = (int)$request_day_y;
        $request_day_m = (int)$request_day_m;
        $request_day_d = (int)$request_day_d;
        if(!checkdate($request_day_m,$request_day_d,$request_day_y)){
            $form->setElementError("form_request_day",$h_mess[25]);
            $requ_day_flg = true;    //�����Ƚ�ꤷ�ʤ�
        }

        //�����ƥ೫����Ƚ��
        if(START_DAY > $request_day){
            //�����ƥ೫����������ʤ饨�顼
            $form->setElementError("form_request_day",$h_mess[37]);
            $requ_day_flg = true;    //�����Ƚ�ꤷ�ʤ�
        }

        //�������˥��顼��̵����з����Ƚ��¹�
        if($requ_day_flg == false){
/*
            //��������������������褫�����å�
            if(Check_Monthly_Renew($db_con,$client_id,"1",$request_day_y,$request_day_m,$request_day_d) == false){
                $form->setElementError("form_request_day",$h_mess[40]);
                $error_flg = true;
            }
*/

            //���������������褫�����å�
            //if(Check_Bill_Close_Day($db_con,$client_id,$request_day_y,$request_day_m,$request_day_d) == false){
            if(Check_Bill_Close_Day_Claim($db_con, $client_id, $claim_div, $request_day_y, $request_day_m, $request_day_d) == false){
                $error_msg16 = $h_mess[41];
                $error_flg = true;
            }

        }
    }


    //�����Υ����å�
    if($ad_offset_flg){

        //�����ɼ
        if($hand_slip_flg){
            //�ֳ����פξ��
            if($trade_sale == "11"){
                //���׾���������̤�������������ɼ������ȥ��顼
                if(Chk_Advance_Fix($db_con, $delivery_day_ymd, $client_id, $claim_div) == false){
                    $form->setElementError("form_delivery_day", "���׾���".$h_mess[74]);
                }

            //�ֳ����סֳ����ʡסֳ��Ͱ��װʳ��ξ��ϥ��顼
            }elseif(!($trade_sale == "13" || $trade_sale == "14")){
                $form->setElementError("trade_sale_select", $h_mess[76]);
            }

        //�����ɼ�ʳ�
        }else{
            //�����ɼ�������ˤ��������å�
            if($plan_sale_flg){
                //�ֳ����פξ��
                if($trade_aord == "11"){
                    //ͽ������������̤�������������ɼ������ȥ��顼
                    if(Chk_Advance_Fix($db_con, $delivery_day_ymd, $client_id, $claim_div) == false){
                        $form->setElementError("form_delivery_day", "ͽ������".$h_mess[74]);
                    }
                }
            }

            //�ָ������פξ��ϥ��顼
            if($trade_aord == "61"){
                $form->setElementError("trade_aord", $h_mess[77]);
            }
        }
    }//���������å������


    //�����ɼ
    if($hand_slip_flg){
        //�ָ������סָ������ʡסָ����Ͱ���
        if($trade_sale == "61" || $trade_sale == "63" || $trade_sale == "64"){
            //���׾��������������������ʤ��ȥ��顼
            if($delivery_day_ymd != $request_day_ymd){
                $form->setElementError("trade_sale_select", $h_mess[71]);
            }

        //�ֳ����סֳ����ʡסֳ��Ͱ���
        }elseif($trade_sale == "11" || $trade_sale == "13" || $trade_sale == "14"){
            //��������ǡ����׾��������������������ʤ��ȥ��顼
            if($ad_offset_flg && $delivery_day_ymd != $request_day_ymd){
                $form->setElementError("trade_sale_select", $h_mess[70]);
            }
        }

    //�����ɼ�ʳ�
    }else{
        //�ָ�������
        if($trade_aord == "61"){
            //ͽ�����������������������ʤ��ȥ��顼
            if($delivery_day_ymd != $request_day_ymd){
                $form->setElementError("trade_aord", $h_mess[73]);
            }

        //�ֳ�����
        }else{
            //��������ǡ�ͽ�����������������������ʤ��ȥ��顼
            if($ad_offset_flg && $delivery_day_ymd != $request_day_ymd){
                $form->setElementError("trade_aord", $h_mess[72]);
            }
        }
    }

}//�����ʬ���̾�orľ�ĤΤߥ����å������


//����饤����Ԥ�FC¦�ξ�硢ͽ��ǡ������������������Ϥ��ʤ����ɡ������ʬ���ѹ������å�
if($plan_aord_flg && $group_kind == "3" && $contract_div == "2"){

    $sql = "SELECT advance_offset_totalamount FROM t_aorder_h WHERE aord_id = $aord_id;";
    $result = Db_Query($db_con, $sql);

    if(pg_fetch_result($result, 0, "advance_offset_totalamount") != null){
        //�ָ������פξ��ϥ��顼
        if($trade_aord == "61"){
            $form->setElementError("trade_aord", "������������껦����Ͽ���Ƥ��뤿�ᡢ�����ʬ��ֳ����פˤ��Ƥ���������");
        }
    }
}


//�����ɼ��ľ�Ĥξ��
if($contract_div != "1" && $group_kind == "2" && $act_id != null){
    //����������򵯤����ݡ���������ԼԤ˷�������äƤʤ��������å�
    if(Check_Monthly_Renew($db_con, $act_id, "2", $delivery_day_y, $delivery_day_m, $delivery_day_d, $head_id) == false){
        if($hand_slip_flg){
            $form->setElementError("form_delivery_day", "���׾���".$h_mess[57]);
        }else{
            $form->setElementError("form_delivery_day", "ͽ������".$h_mess[57]);
        }
    }

    //���������λ����������夫�ɤ���
    if(Check_Payment_Close_Day($db_con, $act_id, $delivery_day_y, $delivery_day_m, $delivery_day_d, $head_id) == false){
        if($hand_slip_flg){
            $form->setElementError("form_delivery_day", "���׾���".$h_mess[58]);
        }else{
            $form->setElementError("form_delivery_day", "ͽ������".$h_mess[58]);
        }
    }
}


//�Ҳ���¤�����ξ�硢��������������å�
if($intro_account_id != null && $intro_ac_div != "1"){

    //ľ�Ĥǡ��Ҳ���¤�FC�ξ�硢�����η�������������å�
    if($group_kind == "2" && $intro_client_div == "3"){
        //�Ҳ��������򵯤����ݡ���������ԼԤ˷�������äƤʤ��������å�
        if(Check_Monthly_Renew($db_con, $intro_account_id, "2", $delivery_day_y, $delivery_day_m, $delivery_day_d, $head_id) == false){
            if($hand_slip_flg){
                $form->setElementError("form_delivery_day", "���׾���".$h_mess[57]);
            }else{
                $form->setElementError("form_delivery_day", "ͽ������".$h_mess[57]);
            }
        }

        //�Ҳ��������򵯤����ݡ���������ԼԤ˻��������äƤʤ��������å�
        if(Check_Payment_Close_Day($db_con, $intro_account_id, $delivery_day_y, $delivery_day_m, $delivery_day_d, $head_id) == false){
            if($hand_slip_flg){
                $form->setElementError("form_delivery_day", "���׾���".$h_mess[58]);
            }else{
                $form->setElementError("form_delivery_day", "ͽ������".$h_mess[58]);
            }
        }

    //����ʳ��ξ�硢��ʬ�ΤȤ��η�������������å�
    }else{
        //�Ҳ��������򵯤����ݡ���������ԼԤ˷�������äƤʤ��������å�
        if(Check_Monthly_Renew($db_con, $intro_account_id, "2", $delivery_day_y, $delivery_day_m, $delivery_day_d) == false){
            if($hand_slip_flg){
                $form->setElementError("form_delivery_day", $h_mess[43]);
            }else{
                $form->setElementError("form_delivery_day", $h_mess[39]);
            }
        }

        //�Ҳ��������򵯤����ݡ���ԼԤ˻��������äƤʤ��������å�
        if(Check_Payment_Close_Day($db_con, $intro_account_id, $delivery_day_y, $delivery_day_m, $delivery_day_d) == false){
            if($hand_slip_flg){
                $form->setElementError("form_delivery_day", "���׾��� �˾Ҳ������λ����������������դ����ϤǤ��ޤ���");
            }else{
                $form->setElementError("form_delivery_day", "ͽ������ �˾Ҳ������λ����������������դ����ϤǤ��ޤ���");
            }
        }
    }
}


//�����ʬ���̾�or�ƣäΤ�
if($contract_div == '1' || $group_kind == 3){

    //�����ɼ�ʳ��ǥ����å�
    if($hand_slip_flg != true){
        //�����ô����
        //���Ψ�ι�פ�100%��Ƚ��
        //���Ϥ��줿���Ψ�ι�פ�100��Ƚ��
        if(($rate1 + (int)$rate2 + (int)$rate3 + (int)$rate4) != 100){
            $error_msg = $h_mess[16];
            $error_flg = true;        //���顼ɽ��
        }

        //���ô���Ԥν�ʣ�����å�
        $cnum1 = count($staff_check);
        $staff_check2 = array_unique($staff_check);
        $cnum2 = count($staff_check2);
        //���ǿ�Ƚ��
        if($cnum1 != $cnum2){
            //���ǿ����㤦��硢��ʣ�ͤ�¸�ߤ���
            $error_msg2 = $h_mess[17];
            $error_flg = true;        //���顼ɽ��
        }

        //���Ψ�����ʾ�ο��ͤ�Ƚ��
        for($s=0;$s<5;$s++){
            $ecount = 12 + $s;
            //�����ͤ�������˿���Ƚ��
            if(!ereg("^[0-9]+$",$staff_rate[$s]) && $staff_rate[$s] != NULL){
                $error_msg10[$s] = $h_mess[$ecount];
                $error_flg = true;        //���顼ɽ��
            }
        }

        //��ϩ
        //��ʸ���������å�
        //�����ͥ����å�
        if(!ereg("^[0-9]{4}$",$route)){
            $form->setElementError("form_route_load","$h_mess[18]");
        }
    }

}


//��������Ƚ��
if($input_goods_flg == false){
    //���ʹ����Ƥ�̤���Ϥξ�硢���顼
    $error_msg3 = $h_mess[21];
    $error_flg = true;        //���顼ɽ��
}

//�Ҳ������
$form->addRule('intro_ac_price',$h_mess[55],"regex", '/^[0-9]+$/');
$form->addRule('intro_ac_price',$h_mess[55],"nonzero");
$form->addRule('intro_ac_rate',$h_mess[56],"regex", '/^[0-9]+$/');
$form->addRule('intro_ac_rate',$h_mess[56],"nonzero");

//�Ҳ����Ψ��1��100�δ�
if($intro_ac_div == "3" && ($intro_ac_rate <= 0 || $intro_ac_rate > 100)){
    $form->setElementError('intro_ac_rate', $h_mess[56]);
}

if($intro_ac_div == "2"){
    $form->addRule('intro_ac_price', $h_mess[55], 'required');
}elseif($intro_ac_div == "3"){
    $form->addRule('intro_ac_rate', $h_mess[56], 'required');
}


for($n=1;$n<=5;$n++){
    //���Ϲ�Ƚ��
    if($divide[$n] != NULL || $slip_flg[$n] == 'true' || $serv_id[$n] != NULL ||$goods_item_flg[$n] == 'true' || $goods_item_id[$n] != NULL || $goods_item_num[$n] != NULL || $set_flg[$n] == 'true' || $t_price1[$n] != NULL || $t_price2[$n] != NULL || $s_price1[$n] != NULL || $s_price2[$n] != NULL || $trade_amount[$n] != NULL || $sale_amount[$n] != NULL || $goods_body_id[$n] != NULL || $goods_expend_id[$n] || $goods_body_num[$n] != NULL || $goods_expend_num[$n] != NULL || $aprice_div[$n] == 2 || $aprice_div[$n] == 3 || $ad_flg[$n] == "2"){

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
            $error_msg4[$n] = $d_mess[2][$n];
            $error_flg = true;        //���顼ɽ��
        }

        //�������ƥ����
        //�������ϥ����å�
        //�����ƥ�����������硢�����ƥ�ID�����뤫Ƚ��
        if($goods_item_id[$n] == NULL && $goods_item_flg[$n] == 'true'){
            $error_msg5[$n] = $d_mess[16][$n];
            $error_flg = true;        //���顼ɽ��
        }

        //�����ʬ���̾�Τ߸�����Ƚ��
        if($contract_div == '1'){
            //��������(������)
            //��������������å�
            if($aprice_div[$n] == 2){
                //����ۤ���������¿����票�顼
                if($sale_amount[$n] < $ac_price[$n]){
                    $form->setElementError("form_account_price[$n]",$d_mess[31][$n]);
                }
                //�����������ͤΤߤ�Ƚ��
                if(!ereg("^[0-9]+$",$ac_price[$n])){
                    $form->setElementError("form_account_price[$n]",$d_mess[13][$n]);
                }
            }
            //��������(�����)
            //��������������å�
            if($aprice_div[$n] == 3){
                //����ۤ���������¿����票�顼
                if(100 < $ac_rate[$n]){
                    $form->setElementError("form_account_rate[$n]",$d_mess[31][$n]);
                }
                //�����������ͤΤߤ�Ƚ��
                if(!ereg("^[0-9]+$",$ac_rate[$n])){
                    $form->setElementError("form_account_rate[$n]",$d_mess[14][$n]);
                }
                //��������100�ʲ���Ƚ��
                if($ac_rate[$n] <= 0 || $ac_rate[$n] > 100){
                    $form->setElementError("form_account_rate[$n]",$d_mess[14][$n]);
                }
            }
        }

        //aoyama-n 2009-09-08
        /************
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
        ************/

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

        if($goods_item_num[$n] == null){
            //�������ƥྦ�ʥ�����
            if($_POST["form_goods_name1"][$n] != null || $_POST["official_goods_name"][$n] != null){
                $form->setElementError("form_goods_cd1[$n]", $d_mess[32][$n]);
            }
        }else{
            //�������ƥ����
            //������Ƚ��
            if(($serv_id[$n] != NULL || $goods_item_id[$n] != NULL) && !ereg("^[0-9]+$",$goods_item_num[$n])){
                $form->setElementError("form_goods_num1[$n]",$d_mess[8][$n]);
            }
        }

        if($_POST["form_goods_num2"][$n] == null){
            //�����ξ��ʾ��ʥ�����
            if($_POST["form_goods_cd2"][$n] == null && $_POST["form_goods_name2"][$n] != null){
                $form->setElementError("form_goods_cd2[$n]", $d_mess[34][$n]);
            }
        }else{
            //�����ο���
            //��ɬ��Ƚ��
            if($goods_body_id[$n] == NULL){
                if($contract_div == "1"){
                    $form->setElementError("error_goods_num2[$n]",$d_mess[12][$n]);
                }else{
                    $form->setElementError("form_goods_cd2[$n]", $d_mess[24][$n]);
                }
            }
            //������Ƚ��
            if($goods_body_id[$n] != NULL && !ereg("^[0-9]+$",$goods_body_num[$n])){
                $form->setElementError("form_goods_num2[$n]",$d_mess[10][$n]);
            }
        }

        if($_POST["form_goods_num3"][$n] == null){
            //�������ʾ��ʥ�����
            if($_POST["form_goods_cd3"][$n] == null && $_POST["form_goods_name3"][$n] != null){
                $form->setElementError("form_goods_cd3[$n]", $d_mess[33][$n]);
            }
        }else{
            //�������ʿ���
            //��ɬ��Ƚ��
            if($goods_expend_id[$n] == NULL){
                if($contract_div == "1"){
                    $form->setElementError("error_goods_num3[$n]",$d_mess[11][$n]);
                }else{
                    $form->setElementError("form_goods_cd3[$n]", $d_mess[25][$n]);
                }
            }
            //������Ƚ��
            if($goods_expend_id[$n] != NULL && !ereg("^[0-9]+$",$goods_expend_num[$n])){
                $form->setElementError("form_goods_num3[$n]",$d_mess[9][$n]);
            }
        }


        //--------------------------//
        //������Ƚ��ؿ�
        //--------------------------//
        //�����ƥྦ������Ƚ��
        if($goods_item_id[$n] != NULL){
            $injust = Injustice_check($db_con,"goods",$goods_item_id[$n],$goods_item_cd[$n]);
            if($injust == false){
                $form->setElementError("form_goods_cd1[$n]",$d_mess[18][$n]);

                //�����ե���������
                $con_data2["hdn_goods_id1"][$n]         = "";
                $con_data2["hdn_name_change1"][$n]      = "";
                $con_data2["form_goods_cd1"][$n]        = "";
                $con_data2["official_goods_name"][$n]   = "";
                $con_data2["form_goods_name1"][$n]      = "";
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
            $injust = Injustice_check($db_con,"goods",$goods_body_id[$n],$goods_body_cd[$n]);
            if($injust == false){
                $form->setElementError("form_goods_cd2[$n]",$d_mess[20][$n]);

                //�����ե���������
                $con_data2["hdn_goods_id2"][$n]         = "";
                $con_data2["hdn_name_change2"][$n]      = "";
                $con_data2["form_goods_cd2"][$n]        = "";
                $con_data2["form_goods_name2"][$n]      = "";
            }
        }
        //����������Ƚ��
        if($goods_expend_id[$n] != NULL){
            $injust = Injustice_check($db_con,"goods",$goods_expend_id[$n],$goods_expend_cd[$n]);
            if($injust == false){
                $form->setElementError("form_goods_cd3[$n]",$d_mess[19][$n]);

                //�����ե���������
                $con_data2["hdn_goods_id3"][$n]         = "";
                $con_data2["hdn_name_change3"][$n]      = "";
                $con_data2["form_goods_cd3"][$n]        = "";
                $con_data2["form_goods_name3"][$n]      = "";
            }
        }


        //��������ͥ����å�
        $form->addRule("form_ad_offset_amount[$n]", $d_mess[35][$n], "regex", "/^[0-9]+$/");
        //�����⤢��ξ�硢ɬ��
        if($ad_flg[$n] == "2" && $ad_offset_amount[$n] == null){
            $form->setElementError("form_ad_offset_amount[$n]", $d_mess[35][$n]);
        }

    }
}


?>