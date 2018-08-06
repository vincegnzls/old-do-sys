<?php
/**
 *
 * ���������
 *
 *
 *
 *
 *
 *
 *
 *   !! ������FC���̤Ȥ��Ʊ�����������ƤǤ� !!
 *   !! �ѹ�������������򤤤��ä�¾���˥��ԤäƤ������� !!
 *
 *
 *
 *
 *
 *
 *
 * 1.0.0 (2006/09/09) ��������
 * 1.1.0 (2006/09/14) 
 *   �����ݻĹ⡢���ݻĹ�Υơ��֥��ѹ���ȼ��SQL�ѹ�
 * 1.1.1 (2006/09/16) 
 *   ��������λ�ʧ����ʲ��Τ褦���ѹ�
 *       t_client.pay_m �� t_client.payout_m
 *       t_client.pay_d �� t_client.payout_d
 * 1.2.0 (2006/10/19) 
 *   ������Ǽ���褬�ɲä���ƽ���Ĺ����ꤵ�줿���˹����������֤뤿�ᡢ
 *     ��������оݤȤʤ��������о����ѹ�
 *   ����������оݴ��֤�ê�������ν���äƤ��ʤ�ê�����ʤ��������å��ɲ�
 * 1.2.1 (2006/10/31) 
 *   ���оݤȤʤ������衢�ޤ��ϻ����褬0��ξ��˥����ꥨ�顼�ȤʤäƤ��ޤ��Τ���
 *
 * @author      kajioka-h <kajioka-h@bhsk.co.jp>
 * @version     1.2.2 (2007-01-29)
 *
 */
/*
 * ����
 *   ����        BɼNo.        ô����      ����
 *  -----------------------------------------------------------
 *   2007-01-09  0042          kajioka-h   FC���̤Ƿ��ݻĹ�ơ��֥�˻Ĥ�ô���Ԥ����ô��1��2�˽���
 *   2007-01-10  xx-xxx        kajioka-h   ������FC����������Τ��б�
 *   2007/01/29  xx-xxx        kajioka-h   ���ݻĹ�ơ��֥�˼�����ʬ��Ĥ��褦���ѹ�
 *   2007/03/07  ����¾-22     kajioka-h   �������ƥ����������Ǥν������ɲ�
 *                             kajioka-h   ̤���������᤬���ä����˥��顼��å�������ɽ��
 *   2007/03/19  xx-xxx        kajioka-h   �������������å����������׾���������������������������å�����褦���ѹ�
 *   2007/05-03  0014          kajioka-h   ��0007-03�פǷ��������Х�����
 *   2007/05/09  B0702-056     kajioka-h   Ĵ���ۤ���椬�ְ�äƤ���Х�����
 *   2007/05/28  B0702-058     kajioka-h   �����ξ�硢Ʊ��client_id�������㤤�⤹��Τǡ��������ƥ����������Ǥζ��̤��Ĥ��ʤ��Х�����
 *   2007/06/06                watanabe-k  ������������ԤʤäƤ��ʤ����ϥ��顼��ɽ������褦�˽���
 *   2007/05/27  xx-xxx        kajioka-h   ��Υ����å���������������Ԥ��Ƥ��ʤ����˥��顼�Ȥ���褦���ѹ�
 *  2007-07-12                  fukuda      �ֻ�ʧ���פ�ֻ������פ��ѹ�
 *  2007-07-13                  fukuda      �ֻ�ʧ�����פ�ֻ����������פ��ѹ�
 *  2007-11-12                 watanabe-k  �������������������å����������Ǥʤ��������ǹԤ��褦���ѹ�
 *   2010/01/05                aoyama-n    ��Ψ��TaxRate���饹�������
 *  2015/05/01                  amano  Dialogue�ؿ��ǥܥ���̾�������ʤ� IE11 �Х��б�
 * * */

$page_title = "���������";

//�Ķ�����ե�����
require_once("ENV_local.php");

//SQL�����ؿ�
require_once(INCLUDE_DIR."function_monthly_renew.inc");


// DB��³����
$db_con = Db_Connect();

// HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");


/*****************************/
// ���´�Ϣ����
/*****************************/
// ���¥����å�
$auth       = Auth_Check($db_con);
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/****************************/
// �����ѿ�����
/****************************/
$shop_id = $_SESSION["client_id"];
//$staff_id = $_SESSION["staff_id"];
$staff_name = $_SESSION["staff_name"];
$group_kind = $_SESSION["group_kind"];


/****************************/
// ����
/****************************/
$list_row_num = 12;     //���̤�ɽ��������������ο���12����ʬ��
$client_div = ($group_kind == "1") ? "3" : "1";         //������ʬ���������̤ϡ�3���������FC�ˡס�FC���̤ϡ�1�����̤�������ˡס�
$hf_div_msg = ($client_div == "3") ? "����" : "����";   //���������ꤵ��Ƥʤ����顼��å������˻Ȥ�

$renew_flg = true;  //���������ѤΤ�Ρ����Ȥ��ˤ������оݡʷ�����ǤϤ�����ޤ���

#2010-01-05 aoyama-n
//��Ψ���饹�����󥹥�������
$tax_rate_obj = new TaxRate($shop_id);


/****************************/
// �ե�����ѡ������
/****************************/
// ���󹹿�ǯ��
// ������
$renew_date = null;
$renew_date[] =& $form->createElement("text", "y", "", "size=\"4\" maxLength=\"4\" $g_form_option style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_renew_day[y]','form_renew_day[m]',4);\"");
$renew_date[] =& $form->createElement("static", "", "", "-");
$renew_date[] =& $form->createElement("text", "m", "", "size=\"1\" maxLength=\"2\" $g_form_option style=\"$g_form_style\"");
$form_renew_day = $form->addGroup($renew_date, "form_renew_day", "");

// �¹ԥܥ���
$form->addElement("submit", "jikkou", "�¡���", "onClick=\"javascript: return Dialogue('�¹Ԥ��ޤ���','#', this)\" $disabled");


/****************************/
//����ǯ����ɽ��
/****************************/
//����η�����������
$sql  = "SELECT \n";
$sql .= "    close_day \n";
$sql .= "FROM \n";
$sql .= "    t_sys_renew \n";
$sql .= "WHERE \n";
$sql .= "    shop_id = ".$shop_id." \n";
$sql .= "    AND \n";
$sql .= "    renew_div = '2' \n";
$sql .= "ORDER BY \n";
$sql .= "    close_day DESC \n";
$sql .= "LIMIT 1 \n";
$sql .= ";\n";

$result = Db_Query($db_con, $sql);
if(pg_num_rows($result) == 1){
    $close_day = pg_fetch_result($result, 0, "close_day");
}else{
    $close_day = null;
}

//���������
if($close_day == null){

    //���ָŤ����դ����
    $sql  = "SELECT \n";
    $sql .= "    LEAST( \n";
    $sql .= "        (SELECT MIN(sale_day) FROM t_sale_h WHERE shop_id = $shop_id), \n";
    $sql .= "        (SELECT MIN(pay_day) FROM t_payin_h WHERE shop_id = $shop_id), \n";
    $sql .= "        (SELECT MIN(buy_day) FROM t_buy_h WHERE shop_id = $shop_id), \n";
    $sql .= "        (SELECT MIN(pay_day) FROM t_payout_h WHERE shop_id = $shop_id) \n";
    $sql .= "    ) \n";
    $sql .= ";";

    $result = Db_Query($db_con, $sql);
    $least_day = pg_fetch_result($result, 0, 0);

    //null����ʤ��ʥǡ�����¸�ߤ���˾�硢���Ĥ�����ǯ�����
    if($least_day != null){

        //�������������
        $sql = "SELECT my_close_day FROM t_client WHERE client_id = $shop_id;";
        $result = Db_Query($db_con, $sql);
        $my_close_day = pg_fetch_result($result, 0, 0);     //��������

        //���ָŤ����դ�ǯ���������
        $least_day_y = substr($least_day, 0, 4);
        $least_day_m = substr($least_day, 5, 2);
        $least_day_d = substr($least_day, 8, 2);

        //������������������ʤ������������������������������ˤʤ�
        if($my_close_day != "29" && $least_day_d > $my_close_day){
            $least_day_m++;
        }

        $str_def_day = date("Y-m", mktime(0, 0, 0, $least_day_m, 1, $least_day_y));
        $arr_def_day = explode("-", $str_def_day);

        $def_data["form_renew_day"]["y"] = $arr_def_day[0]; //���ɽ������ǯ
        $def_data["form_renew_day"]["m"] = $arr_def_day[1]; //���ɽ��������

    }else{
        $def_data["form_renew_day"]["y"] = "";  //���ɽ������ǯ
        $def_data["form_renew_day"]["m"] = "";  //���ɽ��������
    }

}else{
    $close_day_y = substr($close_day, 0, 4);
    $close_day_m = substr($close_day, 5, 2);

    $str_def_day = date("Y-m", mktime(0, 0, 0, $close_day_m +1, 1, $close_day_y));
    $arr_def_day = explode("-", $str_def_day);

    $def_data["form_renew_day"]["y"] = $arr_def_day[0]; //���ɽ������ǯ
    $def_data["form_renew_day"]["m"] = $arr_def_day[1]; //���ɽ��������
}

$form->setDefaults($def_data);


/****************************/
//�¹ԥܥ��󲡲�
/****************************/
if($_POST["jikkou"] != null){

    //���顼�ե饰�����
    $renew_err_flg = false;


    //���Ϥ��줿ǯ���ɬ�ܡ����ͥ����å�
    $form->addGroupRule('form_renew_day', array(
            'y' => array(
                    array('����ǯ�� �����դ�ɬ�����ϤǤ���', 'required'),
                    array('����ǯ�� �����դ�Ⱦ�ѿ��ͤΤߤǤ���', "regex", '/^[0-9]+$/')
            ),
            'm' => array(
                    array('����ǯ�� �����դ�ɬ�����ϤǤ���', 'required'),
                    array('����ǯ�� �����դ�Ⱦ�ѿ��ͤΤߤǤ���', "regex", '/^[0-9]+$/')
            )
    ));
    //�����������å�
    if($_POST["form_renew_day"]["y"] != null && $_POST["form_renew_day"]["m"] != null){
        $renew_day_y = (int)$_POST["form_renew_day"]["y"];
        $renew_day_m = (int)$_POST["form_renew_day"]["m"];
        if(!checkdate($renew_day_m, 1, $renew_day_y)){
            $form->setElementError("form_renew_day","����ǯ�� �����դ������ǤϤ���ޤ���");
        }
    }

    //�Х�ǡ���
    $renew_err_flg = ($form->validate()) ? false : true ;

    //�������������
    if($renew_err_flg == false){
        $sql = "SELECT my_close_day FROM t_client WHERE client_id = $shop_id;";
        $result = Db_Query($db_con, $sql);
        $my_close_day = pg_fetch_result($result, 0, 0);     //��������

        //�������������ꤵ��Ƥ��ʤ���票�顼
        if($my_close_day == null){
            $form_input_err_msg = "���Ҥ����������ꤵ��Ƥ��ޤ���<br>".$hf_div_msg."�ץ��ե���������������ꤷ�Ƥ���������";
            $renew_err_flg = true;
        }
    }


    $post_close_day_y = str_pad($_POST["form_renew_day"]["y"], 4, "0", STR_PAD_LEFT);   //�����ѥ桼������ǯ
    $post_close_day_m = str_pad($_POST["form_renew_day"]["m"], 2, "0", STR_PAD_LEFT);   //�����ѥ桼�����Ϸ�

    if($renew_err_flg == false){
        $this_month_close_day  = $post_close_day_y."-".$post_close_day_m."-";   //���������
        $this_month_close_day .= str_pad($my_close_day, 2, "0", STR_PAD_LEFT);  //���������

        //���ߤ����դ�����ǯ���������᤮�Ƥ��뤫
        if($this_month_close_day >= $g_today){
            $form_input_err_msg = "���ꤵ�줿����ǯ��Ϥޤ�������᤮�Ƥ��ޤ���";
            $renew_err_flg = true;
        }

        //�����ƥ೫������᤮�Ƥ��뤫
        $start_day_err_msg = Sys_Start_Date_Chk($post_close_day_y, $post_close_day_m, $my_close_day, "����ǯ��");
        $renew_err_flg = ($start_day_err_msg != null) ? true : $renew_err_flg;

    }


    //���顼���ʤ�������Ϥ��줿����ǯ���̤����������Τ������å�
    if($renew_err_flg == false){

        //����η�����������
        $sql  = "SELECT \n";
        $sql .= "    close_day \n";
        $sql .= "FROM \n";
        $sql .= "    t_sys_renew \n";
        $sql .= "WHERE \n";
        $sql .= "    shop_id = ".$shop_id." \n";
        $sql .= "    AND \n";
        $sql .= "    renew_div = '2' \n";
        $sql .= "ORDER BY \n";
        $sql .= "    close_day DESC \n";
        $sql .= "LIMIT 1 \n";
        $sql .= ";\n";

        $result = Db_Query($db_con, $sql);
        //�������ʤ��ʽ���������ˤϡ�config.php�פΡ�START_DAY�פ򥻥å�
        if(pg_num_rows($result) == 0){

            //����������������29�ˤʤ�桼�����Ϥ�ǯ��κǽ��������
            if($my_close_day == "29"){
                $post_close_day_d = date("t", mktime(0, 0, 0, $post_close_day_m, 1, $post_close_day_y));
            }else{
                $post_close_day_d = str_pad($my_close_day, 2, "0", STR_PAD_LEFT);
            }

            $user_close_day = $post_close_day_y."-".$post_close_day_m."-".$post_close_day_d;     //�桼������ǯ������������

            //���ָŤ����դ����
            $sql  = "SELECT \n";
            $sql .= "    LEAST( \n";
            $sql .= "        (SELECT MIN(sale_day) FROM t_sale_h WHERE shop_id = $shop_id AND sale_day <= '$user_close_day'), \n";
            $sql .= "        (SELECT MIN(pay_day) FROM t_payin_h WHERE shop_id = $shop_id AND pay_day <= '$user_close_day'), \n";
            $sql .= "        (SELECT MIN(buy_day) FROM t_buy_h WHERE shop_id = $shop_id AND buy_day <= '$user_close_day'), \n";
            $sql .= "        (SELECT MIN(pay_day) FROM t_payout_h WHERE shop_id = $shop_id AND pay_day <= '$user_close_day') \n";
            $sql .= "    ) \n";
            $sql .= ";";

            $result = Db_Query($db_con, $sql);
            $least_day = pg_fetch_result($result, 0, 0);

            if($least_day == null){
                $form_input_err_msg = "����ǯ������˷��������ǡ�����¸�ߤ��ޤ���";
                $renew_err_flg = true;
            }else{
                //�ǡ�����¸�ߤ����硢���ָŤ����դϲ�ǯ����η�����ʤΤ�����
                $least_day_y = substr($least_day, 0, 4);
                $least_day_m = substr($least_day, 5, 2);
                $least_day_d = substr($least_day, 8, 2);

                //����������������29�ˤǤʤ������ָŤ����դ�������������礭�����ϡ�����������ʤ��Ȥ����ʤ���
                if($my_close_day != "29" && $least_day_d > $my_close_day){
                    $least_day_m++;
                }
                $least_day_ym = date("Y-m", mktime(0, 0, 0, $least_day_m, 1, $least_day_y));

                //��������ʤ��Ȥ����ʤ�ǯ��ȥ桼�����Ϥ�ǯ�Ʊ���ʤ餪��
                if($least_day_ym != ($post_close_day_y."-".$post_close_day_m)){
                    $next_close_day = explode("-", $least_day_ym);
                    $form_input_err_msg = $next_close_day[0]."ǯ".(int)$next_close_day[1]."��η�������»ܤ���Ƥ��ޤ���";
                    $renew_err_flg = true;
                }

            }
            //�ǡ������ʤ�����START_DAY������Ƥ���
            $close_day = START_DAY;

        //����������˼»ܤ���Ƥ�����
        }else{
            $close_day = pg_fetch_result($result, 0, "close_day");  //����η������


            /* ���Ϥ��줿���դ���������Ƥ⤤���������å� */
            //���󹹿�ǯ��ȥ桼������ǯ���1����������Ӥ��ƥ�������ʤ餪��

            $close_day_y    = substr($close_day, 0, 4); //���󹹿�ǯ
            $close_day_m    = substr($close_day, 5, 2); //���󹹿���
            $close_day_ym   = substr($close_day, 0, 7); //���󹹿�ǯ��

            $post_close_day_ym = date("Y-m", mktime(0, 0, 0, $post_close_day_m -1, 1, $post_close_day_y));  //�桼������ǯ��-1

            if($close_day_ym != $post_close_day_ym){
                //�㤦���ϡ��»ܺѤ�ǯ���̤���ǯ������å�

                $unix_close_day         = mktime(0, 0, 0, $close_day_m+1, 1, $close_day_y);
                $unix_post_close_day    = mktime(0, 0, 0, $post_close_day_m, 1, $post_close_day_y);

                if($unix_close_day >= $unix_post_close_day){
                    //������Ѥ�ǯ����ꤵ�줿���⤷���Ϥ��äȰ�����ǯ����ꤵ�줿���
                    $form_input_err_msg = "����ǯ��˷�����Ѥ�ǯ����ꤵ��Ƥ��ޤ���";
                }else{
                    //̤���ǯ����ꤵ�줿���Ϥɤ�ǯ��η����������Ƥ��ʤ���ɽ��
                    $next_close_day = date("Y-n", mktime(0, 0, 0, $close_day_m +1, 1, $close_day_y));
                    $next_close_day = explode("-", $next_close_day);
                    $form_input_err_msg = $next_close_day[0]."ǯ".$next_close_day[1]."��η�������»ܤ���Ƥ��ޤ���";
                }
                $renew_err_flg = true;

            }

        }

    }//�����ǯ������å����������


    //��������
    $next_close_day = $post_close_day_y."-".$post_close_day_m."-";
    if($my_close_day == "29"){
        $post_close_day_d = date("t", mktime(0, 0, 0, $post_close_day_m, 1, $post_close_day_y));
    }else{
        $post_close_day_d = $my_close_day;
    }
    $next_close_day .= str_pad($post_close_day_d, 2, "0", STR_PAD_LEFT);


    //�����������Ƥʤ��ǡ������ʤ��������å�
    if($renew_err_flg == false){

        //���ǡ��������å�
        $sql  = "SELECT \n";
        $sql .= "    COUNT(sale_id) \n";
        $sql .= "FROM \n";
        $sql .= "    t_sale_h \n";
        $sql .= "WHERE \n";
        //$sql .= "    ( \n";
        $sql .= "        (sale_day > '$close_day' AND sale_day <= '$next_close_day') \n";
        //$sql .= "        OR \n";
        //$sql .= "        (claim_day > '$close_day' AND claim_day <= '$next_close_day') \n";
        //$sql .= "    ) \n";
        $sql .= "    AND \n";
        $sql .= "    shop_id = $shop_id \n";
        $sql .= "    AND \n";
        $sql .= "    renew_flg = false\n";
        $sql .= ";";
//print_array($sql, "��������̤�»ܥ����å����:");

        $result = Db_Query($db_con, $sql);

        if(pg_fetch_result($result, 0, 0) !== "0"){
            $renew_day_sale_err_msg = "���������Τ���Ƥ��ʤ���夬����ޤ���";
            $renew_err_flg = true;
        }


        //�����ǡ��������å�
        $sql  = "SELECT \n";
        $sql .= "    COUNT(buy_id) \n";
        $sql .= "FROM \n";
        $sql .= "    t_buy_h \n";
        $sql .= "WHERE \n";
        //$sql .= "    ( \n";
        //$sql .= "        (buy_day > '$close_day' AND buy_day <= '$next_close_day') \n";
        //$sql .= "        OR \n";
//        $sql .= "        (arrival_day > '$close_day' AND arrival_day <= '$next_close_day') \n";
        $sql .= "        (buy_day > '$close_day' AND buy_day <= '$next_close_day') \n";
        //$sql .= "    ) \n";
        $sql .= "    AND \n";
        $sql .= "    shop_id = $shop_id \n";
        $sql .= "    AND \n";
        $sql .= "    renew_flg = false\n";
        $sql .= ";";
//print_array($sql, "��������̤�»ܥ����å�����:");

        $result = Db_Query($db_con, $sql);

        if(pg_fetch_result($result, 0, 0) !== "0"){
            $renew_day_buy_err_msg = "���������Τ���Ƥ��ʤ�����������ޤ���";
            $renew_err_flg = true;
        }


        //����ǡ��������å�
        $sql  = "SELECT \n";
        $sql .= "    COUNT(pay_id) \n";
        $sql .= "FROM \n";
        $sql .= "    t_payin_h \n";
        $sql .= "WHERE \n";
        $sql .= "    (pay_day > '$close_day' AND pay_day <= '$next_close_day') \n";
        $sql .= "    AND \n";
        $sql .= "    shop_id = $shop_id \n";
        $sql .= "    AND \n";
        $sql .= "    renew_flg = false\n";
        $sql .= ";";
//print_array($sql, "��������̤�»ܥ����å�����:");

        $result = Db_Query($db_con, $sql);

        if(pg_fetch_result($result, 0, 0) !== "0"){
            $renew_day_payin_err_msg = "���������Τ���Ƥ��ʤ����⤬����ޤ���";
            $renew_err_flg = true;
        }


        //��ʧ�ǡ��������å�
        $sql  = "SELECT \n";
        $sql .= "    COUNT(pay_id) \n";
        $sql .= "FROM \n";
        $sql .= "    t_payout_h \n";
        $sql .= "WHERE \n";
        $sql .= "    (pay_day > '$close_day' AND pay_day <= '$next_close_day') \n";
        $sql .= "    AND \n";
        $sql .= "    shop_id = $shop_id \n";
        $sql .= "    AND \n";
        $sql .= "    renew_flg = false\n";
        $sql .= ";";
//print_array($sql, "��������̤�»ܥ����å���ʧ:");

        $result = Db_Query($db_con, $sql);

        if(pg_fetch_result($result, 0, 0) !== "0"){
            $renew_day_payout_err_msg = "���������Τ���Ƥ��ʤ���ʧ������ޤ���";
            $renew_err_flg = true;
        }

    }


    //����ޤǥ��顼���ʤ����ê�����������ṹ���������������Υ����å�
    if($renew_err_flg == false){

        //ê�������ν���äƤ��ʤ�ê�����ʤ��������å�
        $sql  = "SELECT COUNT(invent_id) \n";
        $sql .= "FROM t_invent \n";
        $sql .= "WHERE expected_day <= '$next_close_day' \n";
        $sql .= "AND renew_flg = false \n";
        $sql .= "AND shop_id = $shop_id \n";
        $sql .= ";";

        $result = Db_Query($db_con, $sql);

        if(pg_fetch_result($result, 0, 0) !== "0"){
            $invent_err_msg = "ê�������Τ���Ƥ��ʤ�ê��������ޤ���";
            $renew_err_flg = true;
        }


        //���ṹ���ν���äƤ��ʤ����᤬�ʤ��������å�
        $sql  = "SELECT bill_no \n";
        $sql .= "FROM t_bill \n";
        $sql .= "WHERE shop_id = $shop_id \n";
        $sql .= "    AND last_update_flg = false \n";
        $sql .= "    AND close_day > '$close_day' \n";
        $sql .= "    AND close_day <= '$next_close_day' \n";
        $sql .= ";";

        $result = Db_Query($db_con, $sql);
        $count = pg_num_rows($result);
        if($count != 0){
            for($i=0, $bill_err_msg = "�ʲ��������ֹ�����ṹ������Ƥ��ޤ���<br>����";$i<$count;$i++){
                $bill_err_msg .= pg_fetch_result($result, $i, "bill_no");
                $bill_err_msg .= (($i+1) % 10 == 0) ? "<br>����" : " ";
            }
            $renew_err_flg = true;
        }

/*
        //���򽪤��Ƥ��ʤ������褬�ʤ��������å�
        $sql  = "SELECT \n";
        $sql .= "   t_client.close_day, \n";
        $sql .= "   COUNT(t_payment_make_history.close_day) AS close_day_count \n";
        $sql .= "FROM \n";
        $sql .= "   (SELECT \n";
        $sql .= "       '".$post_close_day_y."-".$post_close_day_m."-' || \n";
        $sql .= "       CASE \n";
        $sql .= "           WHEN t_client.close_day = '29' THEN \n";
        $sql .= "               SUBSTR(TO_DATE('".$post_close_day_y."-".$post_close_day_m."-' || 01, 'YYYY-MM-DD') \n";
        $sql .= "               + interval '1 month' - interval '1 day', 9, 2) \n";
        $sql .= "           ELSE lpad(t_client.close_day, '2', '0') \n";
        $sql .= "       END AS close_day \n";
        $sql .= "   FROM \n";
        $sql .= "       t_client \n";
        $sql .= "   WHERE \n";
        $sql .= ($group_kind == "1") ? "       client_div = '3' \n" : "       client_div = '2' \n";
        $sql .= "       AND \n";
        $sql .= "       shop_id = $shop_id \n";
        $sql .= "   ) AS t_client \n";
        $sql .= "       LEFT JOIN \n";
        $sql .= "   t_payment_make_history \n";
        $sql .= "   ON t_client.close_day = t_payment_make_history.payment_close_day \n";
        $sql .= "   AND \n";
        $sql .= "   t_payment_make_history.shop_id = $shop_id \n";
        $sql .= "GROUP BY \n";
        $sql .= "   t_client.close_day \n";
        $sql .= "ORDER BY \n";
        $sql .= "   t_client.close_day \n";
        $sql .= "; \n"; 

        $result = Db_Query($db_con, $sql);
        $payment_data = pg_fetch_all($result);

        foreach($payment_data AS $key => $val){
            //��������ԤʤäƤ��ʤ����
            if($val["close_day_count"] == 0){

                //��������ɬ�פʥꥹ��
                if($un_payment_list == null){
                    $un_payment_list  = $val["close_day"];
                }else{
                    $un_payment_list .= ", ".$val["close_day"];
                }

                $payment_err_flg = true;
            }
        }

        if($payment_err_flg === true){
            $payment_err_msg = $un_payment_list." �ǻ������������Ԥʤ��Ƥ��ޤ���";
            $renew_err_flg   = true;
        }
*/

        //������������ԤäƤ��ʤ��������å�
        $sql  = "SELECT \n";
        //$sql .= "    DISTINCT payment_close_day \n";
        $sql .= "    payment_close_day, \n";
        $sql .= "    client_cd1, \n";
        $sql .= "    client_cd2, \n";
        $sql .= "    client_cname \n";
        $sql .= "FROM \n";
        $sql .= "    t_schedule_payment \n";
        $sql .= "WHERE \n";
        //$sql .= "    payment_close_day > '$close_day' \n";
        //$sql .= "    AND \n";
        $sql .= "    payment_close_day <= '$next_close_day' \n";
        $sql .= "    AND \n";
        $sql .= "    last_update_flg = false \n";
        $sql .= "    AND \n";
        $sql .= "    first_set_flg = false \n";
        $sql .= "    AND \n";
        $sql .= "    shop_id = $shop_id \n";
        $sql .= "ORDER BY \n";
        $sql .= "    payment_close_day, \n";
        $sql .= "    client_cd1, \n";
        $sql .= "    client_cd2 \n";
        $sql .= ";";

        $result = Db_Query($db_con, $sql);
        $count = pg_num_rows($result);
        if($count != 0){
            for($i=0, $payment_close_day_tmp=null, $payment_err_msg=""; $i<$count; $i++){
                if($payment_close_day_tmp != pg_fetch_result($result, $i, "payment_close_day")){
                    $payment_close_day_tmp = pg_fetch_result($result, $i, "payment_close_day");

                    $payment_err_msg .= ($payment_err_msg != null) ? "<br>" : null;
                    $payment_err_msg .= pg_fetch_result($result, $i, "payment_close_day");
                    $payment_err_msg .= " �����ΰʲ���";
                    $payment_err_msg .= ($group_kind == "1") ? "�����" : "������";
                    $payment_err_msg .= "�ϻ�������������Ƥ��ޤ���";
                }

                $payment_err_msg .= "<br>����";
                $payment_err_msg .= pg_fetch_result($result, $i, "client_cd1");
                if($group_kind == "1"){
                    $payment_err_msg .= "-";
                    $payment_err_msg .= pg_fetch_result($result, $i, "client_cd2");
                }
                $payment_err_msg .= " ";
                $payment_err_msg .= pg_fetch_result($result, $i, "client_cname");

            }
            $renew_err_flg = true;
        }


    }//ê�����������ṹ���������������Υ����å������


    //���顼���ʤ���С����ä���ۥ�Ȥη�����������Ϥޤ��
    Db_Query($db_con,"BEGIN;");

    //���Ҥξ�����Ψ�����
    #2010-01-05 aoyama-n
    #$sql = "SELECT tax_rate_n FROM t_client WHERE client_id = $shop_id;";
    #$result = Db_Query($db_con, $sql);
    #$tax_rate_n = (pg_num_rows($result) == 1) ? pg_fetch_result($result, 0, "tax_rate_n") : "";

    //��ݻĹ�ۤ򻻽Ф�����Ͽ
    if($renew_err_flg == false){

        //���������ʹߤ��麣�������ޤǤ���ݻĹ�ʤɤ�׻�
        $sql  = "
            SELECT 
                COALESCE(t_ar_balance_last.monthly_close_day_last, '".START_DAY."') AS monthly_close_day_last, 
                t_client.close_day, 
                t_client.pay_m, 
                t_client.pay_d, 
                t_all_client.client_id, 
                t_client.client_cd1, 
                t_client.client_cd2, 
                t_client.client_name AS client_name1, 
                t_client.client_name2, 
                t_client.client_cname, 
                COALESCE(t_ar_balance_last.ar_balance_last, 0) AS ar_balance_last, 
                COALESCE(t_payin_amount.payin_amount, 0) AS pay_amount, 
                COALESCE(t_payin_amount.tune_amount, 0) AS tune_amount, 
                ( COALESCE(t_ar_balance_last.ar_balance_last, 0) 
                  - COALESCE(t_payin_amount.payin_amount, 0) 
                  - COALESCE(t_payin_amount.tune_amount, 0) 
                ) AS rest_amount, 
                ( COALESCE(t_sale_amount.net_sale_amount, 0) 
                  + COALESCE(t_royalty.royalty_price, 0) 
                ) AS net_sale_amount, 
                ( COALESCE(t_sale_amount.tax_amount, 0) 
                  + COALESCE(t_royalty.royalty_tax, 0) 
                  + COALESCE(t_adjust_tax.adjust_tax, 0) 
                ) AS tax_amount, 
                ( COALESCE(t_sale_amount.net_sale_amount, 0) 
                  + COALESCE(t_royalty.royalty_price, 0) 
                  + COALESCE(t_sale_amount.tax_amount, 0) 
                  + COALESCE(t_royalty.royalty_tax, 0) 
                  + COALESCE(t_adjust_tax.adjust_tax, 0) 
                ) AS intax_sale_amount, 
                ( COALESCE(t_ar_balance_last.ar_balance_last, 0) 
                  - COALESCE(t_payin_amount.payin_amount, 0) 
                  - COALESCE(t_payin_amount.tune_amount, 0) 
                  + COALESCE(t_sale_amount.net_sale_amount, 0) 
                  + COALESCE(t_royalty.royalty_price, 0) 
                  + COALESCE(t_sale_amount.tax_amount, 0) 
                  + COALESCE(t_royalty.royalty_tax, 0) 
                  + COALESCE(t_adjust_tax.adjust_tax, 0) 
                ) AS ar_balance_this, 
        ";
        //�������̤Ǥ�FC�ޥ�����SV�����ô��1��ô���ԥ�������Ͽ
        if($group_kind == "1"){
            $sql .= "
                (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.sv_staff_id) AS staff1_name, 
                (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.b_staff_id1) AS staff2_name, 
            ";
        //FC���̤Ǥ�������ޥ����η���ô��1������ô��2��ô���ԥ�������Ͽ
        }else{
            $sql .= "
                (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.c_staff_id1) AS staff1_name, 
                (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.c_staff_id2) AS staff2_name, 
            ";
        }
        $sql .= "
                COALESCE(t_installment_balance.installment_receivable_balance, 0) AS installment_receivable_balance, 
                COALESCE(t_installment_sales.installment_sales_amount, 0) AS installment_sales_amount, 
                t_client.c_tax_div, 
                t_client.tax_franct, 
                t_client.coax, 
                t_client.tax_div 

            FROM 
        ";
        //������������塢���⡢���������ݻĹ�������ˤΤ��ä�������ID���������
        $sql .= "
                ( 
        ";
        $sql .= Monthly_All_Client_Sql($shop_id, "sale", $next_close_day, $renew_flg);
        $sql .= "
                ) AS t_all_client 

                INNER JOIN t_client ON t_all_client.client_id = t_client.client_id 

        ";

        //�����������η����������ݻĹ�����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Balance_Last_Sql($shop_id, "sale");
        $sql .= "
                ) AS t_ar_balance_last ON t_all_client.client_id = t_ar_balance_last.client_id 

        ";

        //�����������麣�������ޤǤ����ۡ���ȴ�ˡ������ǳۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Sale_Amount_Sql($shop_id, $close_day, $next_close_day, $renew_flg);
        $sql .= "
                ) AS t_sale_amount ON t_all_client.client_id = t_sale_amount.client_id

        ";

        //�����������麣�������ޤǤ�����ۡʽ�Ĵ���ۡˡ�Ĵ���ۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Payin_Amount_Sql($shop_id, $close_day, $next_close_day, $renew_flg);
        $sql .= "
                ) AS t_payin_amount ON t_all_client.client_id = t_payin_amount.client_id 

        ";

        //���������ޤǤ����������Τ��������������ʹߤ�̤�����
        $sql .= "
                LEFT JOIN 
                ( 
                    SELECT 
                        t_sale_h.client_id, 
                        SUM(t_installment_sales.collect_amount) AS installment_receivable_balance 
                    FROM 
                        t_sale_h 
                        INNER JOIN t_installment_sales ON t_sale_h.sale_id = t_installment_sales.sale_id 
                    WHERE 
                        t_sale_h.shop_id = $shop_id 
                        AND 
                        t_sale_h.trade_id = 15 
                        AND 
                        t_sale_h.sale_day <= '$next_close_day' 
                        AND 
                        t_installment_sales.collect_day > '$next_close_day' 
                    GROUP BY 
                        t_sale_h.client_id 
                ) AS t_installment_balance ON t_all_client.client_id = t_installment_balance.client_id 

        ";
        //�����������麣�������ޤǤγ������ۡ��ǹ��ˤ����
        $sql .= "
                LEFT JOIN 
                ( 
                    SELECT 
                        client_id, 
                        SUM(net_amount + tax_amount) AS installment_sales_amount 
                    FROM 
                        t_sale_h 
                    WHERE 
                        shop_id = $shop_id 
                        AND 
                        trade_id = 15 
                        AND 
                        sale_day > '$close_day' 
                        AND 
                        sale_day <= '$next_close_day' 
                    GROUP BY 
                        client_id 
                ) AS t_installment_sales ON t_all_client.client_id = t_installment_sales.client_id 

        ";

        //�����������麣�������ޤǤΥ������ƥ��ۤȤ��ξ����ǳۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Lump_Amount_Sql($shop_id, $close_day, $next_close_day, "1", "sale");
        $sql .= "
                ) AS t_royalty ON t_all_client.client_id = t_royalty.client_id 

        ";

        //�����������麣�������ޤǤΰ������ǳۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Lump_Amount_Sql($shop_id, $close_day, $next_close_day, "2", "sale");
        $sql .= "
                ) AS t_adjust_tax ON t_all_client.client_id = t_adjust_tax.client_id 

        ";

        $sql .= "
            ORDER BY 
                t_all_client.client_id 

            ;
        ";
//print_array($sql, "��ݽ���SQL��");

        $result = Db_Query($db_con, $sql); 
        //$ar_balance = Get_Data($result, 2);
        $ar_balance = pg_fetch_all($result);
//print_array($ar_balance);


        //�������ʬ�����ݻĹ�ơ��֥�˽񤭹���
        //$count = count($ar_balance);
        $count = pg_num_rows($result);
        for($i=0;$i<$count;$i++){

            #2010-01-05 aoyama-n
            //������Ψ����
            $tax_rate_obj->setTaxRateDay($next_close_day);
            $tax_rate_n = $tax_rate_obj->getClientTaxRate($ar_balance[$i]["client_id"]);

            $sql  = "
                INSERT INTO 
                    t_ar_balance 
                ( 
                    ar_balance_id, 
                    monthly_close_day_last, 
                    monthly_close_day_this, 
                    close_day, 
                    pay_m, 
                    pay_d, 
                    client_id, 
                    client_cd1, 
                    client_cd2, 
                    client_name1, 
                    client_name2, 
                    client_cname, 
                    ar_balance_last, 
                    pay_amount, 
                    tune_amount, 
                    rest_amount, 
                    net_sale_amount, 
                    tax_amount, 
                    intax_sale_amount, 
                    ar_balance_this, 
                    staff1_name, 
                    staff2_name, 
                    installment_receivable_balance, 
                    installment_sales_amount, 
                    shop_id, 
                    tax_rate_n, 
                    c_tax_div, 
                    tax_franct, 
                    coax, 
                    tax_div 
                ) VALUES ( 
                    (SELECT COALESCE(MAX(ar_balance_id), 0)+1 FROM t_ar_balance), 
                    '".$ar_balance[$i]["monthly_close_day_last"]."', 
                    '".$next_close_day."', 
                    '".$ar_balance[$i]["close_day"]."', 
                    '".$ar_balance[$i]["pay_m"]."', 
                    '".$ar_balance[$i]["pay_d"]."', 
                    ".$ar_balance[$i]["client_id"].", 
                    '".$ar_balance[$i]["client_cd1"]."', 
                    '".$ar_balance[$i]["client_cd2"]."', 
                    '".addslashes($ar_balance[$i]["client_name1"])."', 
                    '".addslashes($ar_balance[$i]["client_name2"])."', 
                    '".addslashes($ar_balance[$i]["client_cname"])."', 
                    ".$ar_balance[$i]["ar_balance_last"].", 
                    ".$ar_balance[$i]["pay_amount"].", 
                    ".$ar_balance[$i]["tune_amount"].", 
                    ".$ar_balance[$i]["rest_amount"].", 
                    ".$ar_balance[$i]["net_sale_amount"].", 
                    ".$ar_balance[$i]["tax_amount"].", 
                    ".$ar_balance[$i]["intax_sale_amount"].", 
                    ".$ar_balance[$i]["ar_balance_this"].", 
                    '".addslashes($ar_balance[$i]["staff1_name"])."', 
                    '".addslashes($ar_balance[$i]["staff2_name"])."', 
                    ".$ar_balance[$i]["installment_receivable_balance"].", 
                    ".$ar_balance[$i]["installment_sales_amount"].", 
                    ".$shop_id.", 
                    '".$tax_rate_n."', 
                    '".$ar_balance[$i]["c_tax_div"]."', 
                    '".$ar_balance[$i]["tax_franct"]."', 
                    '".$ar_balance[$i]["coax"]."', 
                    '".$ar_balance[$i]["tax_div"]."' 
                );
            ";

//print_array($sql, "��ݻĹ�ơ��֥���ϿSQL��");

            $result = Db_Query($db_con, $sql);
            if($result == false){
                Db_Query($db_con,"ROLLBACK;");
                $form_input_err_msg = "������˼��Ԥ��ޤ�����<br>¾�Υ桼�����������ԤäƤ��ʤ�����ǧ�ξ塢���ټ¹Ԥ��Ƥ���������";
                $renew_err_flg = true;
                break;
            }
        }
    }

    //��ݻĹ�ۤ򻻽Ф�����Ͽ
    if($renew_err_flg == false){

        //���������ʹߤ��麣�������ޤǤ���ݻĹ�ʤɤ�׻�
        $sql  = "
            SELECT 
                COALESCE(t_ap_balance_last.monthly_close_day_last, '".START_DAY."') AS monthly_close_day_last, 
                t_client.close_day, 
                t_client.payout_m, 
                t_client.payout_d, 
                t_all_client.client_id, 
                t_client.client_cd1, 
                t_client.client_cd2, 
                t_client.client_name AS client_name1, 
                t_client.client_name2, 
                t_client.client_cname, 
                COALESCE(t_ap_balance_last.ap_balance_last, 0) AS ap_balance_last, 
                COALESCE(t_payout_amount.payout_amount, 0) AS pay_amount, 
                COALESCE(t_payout_amount.tune_amount, 0) AS tune_amount, 
                ( COALESCE(t_ap_balance_last.ap_balance_last, 0) 
                  - COALESCE(t_payout_amount.payout_amount, 0) 
                  - COALESCE(t_payout_amount.tune_amount, 0) 
                ) AS rest_amount, 
                ( COALESCE(t_buy_amount.net_buy_amount, 0) 
                  + COALESCE(t_royalty.royalty_price, 0) 
                ) AS net_buy_amount, 
                ( COALESCE(t_buy_amount.tax_amount, 0) 
                  + COALESCE(t_royalty.royalty_tax, 0) 
                  + COALESCE(t_adjust_tax.adjust_tax, 0) 
                ) AS tax_amount, 
                ( COALESCE(t_buy_amount.net_buy_amount, 0) 
                  + COALESCE(t_royalty.royalty_price, 0) 
                  + COALESCE(t_buy_amount.tax_amount, 0) 
                  + COALESCE(t_royalty.royalty_tax, 0) 
                  + COALESCE(t_adjust_tax.adjust_tax, 0) 
                ) AS intax_buy_amount, 
                ( COALESCE(t_ap_balance_last.ap_balance_last, 0) 
                  - COALESCE(t_payout_amount.payout_amount, 0) 
                  - COALESCE(t_payout_amount.tune_amount, 0) 
                  + COALESCE(t_buy_amount.net_buy_amount, 0) 
                  + COALESCE(t_royalty.royalty_price, 0) 
                  + COALESCE(t_buy_amount.tax_amount, 0) 
                  + COALESCE(t_royalty.royalty_tax, 0) 
                  + COALESCE(t_adjust_tax.adjust_tax, 0) 
                ) AS ap_balance_this, 
                CASE t_client.client_div 
                    WHEN '2' 
                    THEN (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.c_staff_id1) 
                    ELSE (SELECT staff_name FROM t_staff WHERE t_staff.staff_id = t_client.sv_staff_id) 
                END AS staff1_name, 
                COALESCE(t_amortization_trade_balance.amortization_trade_balance, 0) AS amortization_trade_balance, 
                COALESCE(t_amortization_buy.amortization_amount, 0) AS amortization_amount, 
                t_client.c_tax_div, 
                t_client.tax_franct, 
                t_client.coax, 
                t_client.tax_div, 
                t_client.client_div 

            FROM 
        ";
        //���������˻�������ʧ�����������ݻĹ�������ˤΤ��ä�������ID���������
        $sql .= "
                ( 
        ";
        $sql .= Monthly_All_Client_Sql($shop_id, "buy", $next_close_day, $renew_flg);
        $sql .= "
                ) AS t_all_client 
                INNER JOIN t_client ON t_all_client.client_id = t_client.client_id 
        ";

        //�����������η����������ݻĹ�����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Balance_Last_Sql($shop_id, "buy");
        $sql .= "
                ) AS t_ap_balance_last ON t_all_client.client_id = t_ap_balance_last.client_id 
        ";

        //�����������麣�������ޤǤλ����ۡ���ȴ�ˡ������ǳۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Buy_Amount_Sql($shop_id, $close_day, $next_close_day, $renew_flg);
        $sql .= "
                ) AS t_buy_amount ON t_all_client.client_id = t_buy_amount.client_id
        ";

        //�����������麣�������ޤǤλ�ʧ�ۡʽ�Ĵ���ۡˡ�Ĵ���ۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Payout_Amount_Sql($shop_id, $close_day, $next_close_day, $renew_flg);
        $sql .= "
                ) AS t_payout_amount ON t_all_client.client_id = t_payout_amount.client_id 
        ";

        //���������ޤǤ�����������Τ��������������ʹߤ�̤�����
        $sql .= "
                LEFT JOIN 
                ( 
                    SELECT 
                        t_buy_h.client_id, 
                        SUM(t_amortization.split_pay_amount) AS amortization_trade_balance 
                    FROM 
                        t_buy_h 
                        INNER JOIN t_amortization ON t_buy_h.buy_id = t_amortization.buy_id 
                    WHERE 
                        t_buy_h.shop_id = $shop_id 
                        AND 
                        t_buy_h.trade_id = 25 
                        AND 
                        t_buy_h.buy_day <= '$next_close_day' 
                        AND 
                        t_amortization.pay_day > '$next_close_day' 
                    GROUP BY 
                        t_buy_h.client_id 
                ) AS t_amortization_trade_balance ON t_all_client.client_id = t_amortization_trade_balance.client_id 
        ";

        //�����������麣�������ޤǤγ�������ۡ��ǹ��ˤ����
        $sql .= "
                LEFT JOIN 
                ( 
                    SELECT 
                        client_id, 
                        SUM(net_amount + tax_amount) AS amortization_amount 
                    FROM 
                        t_buy_h 
                    WHERE 
                        shop_id = $shop_id 
                        AND 
                        trade_id = 25 
                        AND 
                        buy_day > '$close_day' 
                        AND 
                        buy_day <= '$next_close_day' 
                    GROUP BY 
                        client_id 
                ) AS t_amortization_buy ON t_all_client.client_id = t_amortization_buy.client_id 

        ";

        //�����������麣�������ޤǤΥ������ƥ��ۤȤ��ξ����ǳۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Lump_Amount_Sql($shop_id, $close_day, $next_close_day, "1", "buy");
        $sql .= "
                ) AS t_royalty ON t_all_client.client_id = t_royalty.client_id 

        ";

        //�����������麣�������ޤǤΰ������ǳۤ����
        $sql .= "
                LEFT JOIN 
                ( 
        ";
        $sql .= Monthly_Lump_Amount_Sql($shop_id, $close_day, $next_close_day, "2", "buy");
        $sql .= "
                ) AS t_adjust_tax ON t_all_client.client_id = t_adjust_tax.client_id 

        ";

        $sql .= "
            ORDER BY 
                t_all_client.client_id 
            ;
        ";
//print_array($sql, "��ݽ���SQL��");

        $result = Db_Query($db_con, $sql); 
        //$ap_balance = Get_Data($result, 2);
        $ap_balance = pg_fetch_all($result);
//print_array($ap_balance);


        //�������ʬ�����ݻĹ�ơ��֥�˽񤭹���
        //$count = count($ap_balance);
        $count = pg_num_rows($result);
        for($i=0;$i<$count;$i++){

            #2010-01-05 aoyama-n
            //������Ψ����
            $tax_rate_obj->setTaxRateDay($next_close_day);
            $tax_rate_n = $tax_rate_obj->getClientTaxRate($ap_balance[$i]["client_id"]);

            $sql  = "
                INSERT INTO 
                    t_ap_balance 
                ( 
                    ap_balance_id, 
                    monthly_close_day_last, 
                    monthly_close_day_this, 
                    close_day, 
                    payout_m, 
                    payout_d, 
                    client_id, 
                    client_cd1, 
                    client_cd2, 
                    client_name1, 
                    client_name2, 
                    client_cname, 
                    ap_balance_last, 
                    pay_amount, 
                    tune_amount, 
                    rest_amount, 
                    net_buy_amount, 
                    tax_amount, 
                    intax_buy_amount, 
                    ap_balance_this, 
                    staff1_name, 
                    amortization_trade_balance, 
                    amortization_amount, 
                    shop_id, 
                    tax_rate_n, 
                    c_tax_div, 
                    tax_franct, 
                    coax, 
                    tax_div, 
                    client_div 
                ) VALUES ( 
                    (SELECT COALESCE(MAX(ap_balance_id), 0)+1 FROM t_ap_balance), 
                    '".$ap_balance[$i]["monthly_close_day_last"]."', 
                    '".$next_close_day."', 
                    '".$ap_balance[$i]["close_day"]."', 
                    '".$ap_balance[$i]["payout_m"]."', 
                    '".$ap_balance[$i]["payout_d"]."', 
                    ".$ap_balance[$i]["client_id"].", 
                    '".$ap_balance[$i]["client_cd1"]."', 
                    '".$ap_balance[$i]["client_cd2"]."', 
                    '".addslashes($ap_balance[$i]["client_name1"])."', 
                    '".addslashes($ap_balance[$i]["client_name2"])."', 
                    '".addslashes($ap_balance[$i]["client_cname"])."', 
                    ".$ap_balance[$i]["ap_balance_last"].", 
                    ".$ap_balance[$i]["pay_amount"].", 
                    ".$ap_balance[$i]["tune_amount"].", 
                    ".$ap_balance[$i]["rest_amount"].", 
                    ".$ap_balance[$i]["net_buy_amount"].", 
                    ".$ap_balance[$i]["tax_amount"].", 
                    ".$ap_balance[$i]["intax_buy_amount"].", 
                    ".$ap_balance[$i]["ap_balance_this"].", 
                    '".addslashes($ap_balance[$i]["staff1_name"])."', 
                    ".$ap_balance[$i]["amortization_trade_balance"].", 
                    ".$ap_balance[$i]["amortization_amount"].", 
                    ".$shop_id.", 
                    '".$tax_rate_n."', 
                    '".$ap_balance[$i]["c_tax_div"]."', 
                    '".$ap_balance[$i]["tax_franct"]."', 
                    '".$ap_balance[$i]["coax"]."', 
                    '".$ap_balance[$i]["tax_div"]."', 
                    '".$ap_balance[$i]["client_div"]."' 
                );
            ";

//print_array($sql, "��ݻĹ�ơ��֥���ϿSQL��");

            $result = Db_Query($db_con, $sql);
            if($result == false){
                Db_Query($db_con,"ROLLBACK;");
                $form_input_err_msg = "������˼��Ԥ��ޤ�����<br>¾�Υ桼�����������ԤäƤ��ʤ�����ǧ�ξ塢���ټ¹Ԥ��Ƥ���������";
                $renew_err_flg = true;
                break;
            }
        }
    }


    //�Ǹ�˹�����������ơ��֥����Ͽ
    if($renew_err_flg == false){

        $sql  = "INSERT INTO \n";
        $sql .= "    t_sys_renew \n";
        $sql .= "( \n";
        $sql .= "    renew_id, \n";
        $sql .= "    renew_div, \n";
        $sql .= "    renew_time, \n";
        $sql .= "    close_day, \n";
        $sql .= "    shop_id, \n";
        $sql .= "    operation_staff_name \n";
        $sql .= ") VALUES ( \n";
        $sql .= "    (SELECT COALESCE(MAX(renew_id), 0)+1 FROM t_sys_renew), \n";
        $sql .= "    '2', \n";
        $sql .= "    CURRENT_TIMESTAMP, \n";
        $sql .= "    '$next_close_day', \n";
        $sql .= "    $shop_id, \n";
        $sql .= "    '".addslashes($staff_name)."' \n";
        $sql .= ");";

        $result = Db_Query($db_con, $sql);
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            $form_input_err_msg = "¾�Υ桼����Ʊ���˹���������Ԥä����ᡢ������˼��Ԥ��ޤ������⤦���ټ¹Ԥ��Ʋ�������";
            $renew_err_flg = true;
        }

    }

    //���顼�ʤ���λ����Х���åȤ���
    if($renew_err_flg == false){
        Db_Query($db_con,"COMMIT;");
        $complete_msg = "�������������λ���ޤ�����";
    }

}



/****************************/
//����ɽ���Υơ��֥����
/****************************/

$sql  = "SELECT \n";
$sql .= "    SUBSTR(close_day, 1, 7) AS close_ym, \n";
$sql .= "    SUBSTR(close_day, 9, 2) AS close_d, \n";
$sql .= "    SUBSTR(renew_time, 1, 19) AS renew_time \n";
$sql .= "FROM \n";
$sql .= "    t_sys_renew \n";
$sql .= "WHERE \n";
$sql .= "    shop_id = ".$shop_id." \n";
$sql .= "    AND renew_div = '2' \n";
$sql .= "ORDER BY \n";
$sql .= "    close_day DESC \n";
$sql .= "LIMIT $list_row_num \n";
$sql .= ";";

$result = Db_Query($db_con, $sql);
if(pg_num_rows($result) !== 0){
    $rec_data = Get_Data($result);
    for($i=0;$i<count($rec_data);$i++){
        $rec_data[$i][1] = ($rec_data[$i][1] >= "29") ? "����" : (int)$rec_data[$i][1]."��";
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
//$page_menu = Create_Menu_h('renew','1');

/****************************/
//���̥إå�������
/****************************/
$page_header = Create_Header($page_title);


//print_array($_POST);


// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form��Ϣ���ѿ���assign
$smarty->assign('form',$renderer->toArray());

//����¾���ѿ���assign
$smarty->assign('var',array(
    'html_header'   => "$html_header",
    //'page_menu'     => "$page_menu",
    'page_header'   => "$page_header",
    'html_footer'   => "$html_footer",
    'renew_err_flg' => "$renew_err_flg",
    'form_input_err_msg'    => "$form_input_err_msg",
    'start_day_err_msg'     => "$start_day_err_msg",
    'complete_msg'  => "$complete_msg",
    'renew_day_sale_err_msg'    => "$renew_day_sale_err_msg",
    'renew_day_buy_err_msg'     => "$renew_day_buy_err_msg",
    'renew_day_payin_err_msg'   => "$renew_day_payin_err_msg",
    'renew_day_payout_err_msg'  => "$renew_day_payout_err_msg",
    'invent_err_msg'    => "$invent_err_msg",
    'bill_err_msg'  => $bill_err_msg,
    'payment_err_msg' => "$payment_err_msg",
));

// �����ѥ쥳���ɥǡ�����ƥ�ץ졼�Ȥ��Ϥ�
$smarty->assign("rec_data", $rec_data);


//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>