<?php
/*
 * �ѹ�����
 * 1.0.0 (2006/04/12) ���ꣳ���ɲ�(suzuki-t)
 *
 * @author		suzuki-t <suzuki-t@bhsk.co.jp>
 *
 * @version		1.0.0 (2006/04/21)
*/

/*
 * ����
 *  ����            BɼNo.      ô����      ����
 *  -----------------------------------------------------------
 *  2006-12-08      ban_0074    suzuki      ��Ͽ���ѹ��Υ�����Ĥ��褦�˽���
 *  2007-01-23      �����ѹ�    watanabe-k  �ܥ���ο����ѹ�
 *  2007-05-07                  kaku-m      csv���Ϲ��ܤ��ɲ�
 *  2010-04-28      Rev.1.5���� hashimoto-y ��ɽ����ǽ���ɲ�
 *  
 *
*/

$page_title = "��ԥޥ���";

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
//�����ѿ�����
/****************************/
$shop_id  = $_SESSION[client_id];

/* GET����ID�������������å� */
$where = " bank_id IN (SELECT bank_id FROM t_bank WHERE shop_id = 1) ";
if ($_GET["b_bank_id"] != null && Get_Id_Check_Db($db_con, $_GET["b_bank_id"], "b_bank_id", "t_b_bank", "num", $where) != true){
    header("Location: ../top.php");
}

/****************************/
//�������
/****************************/
//���ID
$select_value = Select_Get($db_con, 'bank');
$form->addElement("select","form_bank_select", "",$select_value, $g_form_option_select);

//��Ź������
$form->addElement("text","form_b_bank_cd","�ƥ����ȥե�����","size=\"3\" maxLength=\"3\" style=\"$g_form_style\"".$g_form_option."\"");

//��Ź̾
$form->addElement("text","form_b_bank_name","�ƥ����ȥե�����","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//��Ź̾�ʥեꥬ�ʡ�
$form->addElement("text","form_b_bank_kana","�ƥ����ȥե�����","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//͹���ֹ�
$text[] =& $form->createElement("text","form_post_no1","�ƥ����ȥե�����","size=\"3\" maxLength=\"3\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_post[form_post_no1]','form_post[form_post_no2]',3)\"".$g_form_option."\"");
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement("text","form_post_no2","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"".$g_form_option."\"");
$form->addGroup( $text, "form_post", "form_post");
//���꣱
$form->addElement("text","form_address1","�ƥ����ȥե�����","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//���ꣲ
$form->addElement("text","form_address2","�ƥ����ȥե�����","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//���ꣳ
$form->addElement("text","form_address3","�ƥ����ȥե�����","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//TEL
$form->addElement("text","form_tel","�ƥ����ȥե�����","size=\"15\" maxLength=\"13\" style=\"$g_form_style\"".$g_form_option."\"");
//FAX
$form->addElement("text","form_fax","�ƥ����ȥե�����","size=\"15\" maxLength=\"13\" style=\"$g_form_style\" ".$g_form_option."\"");
//���¼���
//$form->addElement("text","form_account_kind","�ƥ����ȥե�����","size=\"5\" maxLength=\"5\"".$g_form_option."\"");
//�����ֹ�
//$form->addElement("text","form_account_no","�ƥ����ȥե�����","size=\"8\" maxLength=\"7\" style=\"$g_form_style\"".$g_form_option."\"");

#2010-04-28 hashimoto-y
$form->addElement('checkbox', 'form_nondisp_flg', '', '');

//����
$form->addElement("text","form_note","�ƥ����ȥե�����","size=\"34\" maxLength=\"30\"".$g_form_option."\"");
//��ư����
$form->addElement("button","input_button","��ư����","onClick=\"javascript:Button_Submit('input_button_flg','#','true')\"");
//���ꥢ
$form->addElement("button","clear_button","���ꥢ","onClick=\"javascript:location.href('$_SERVER[PHP_SELF]');\"");
//��Ͽ
$form->addElement("submit","entry_button","�С�Ͽ","onClick=\"javascript:return Dialogue('��Ͽ���ޤ���','#')\" $disabled");
//CSV����
$form->addElement("button","csv_button","CSV����","onClick=\"javascript:Button_Submit('csv_button_flg','#','true')\"");

$form->addElement("button","bank_button","�����Ͽ����","onClick=\"javascript:Referer('1-1-207.php')\"");
$form->addElement("button","bank_mine_button","��Ź��Ͽ����",$g_button_color." onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","bank_account_button","������Ͽ����","onClick=\"javascript:Referer('1-1-210.php')\"");


//������󥯲���Ƚ��ե饰
$form->addElement("hidden", "update_flg");
//��ư���ϥܥ��󲡲�Ƚ��ե饰
$form->addElement("hidden", "input_button_flg");
//CSV���ϥܥ��󲡲�Ƚ��ե饰
$form->addElement("hidden", "csv_button_flg");

/******************************/
//��ư���ϥܥ��󲡲�����
/*****************************/
if($_POST["input_button_flg"]==true){
    $post1     = $_POST["form_post"]["form_post_no1"];             //͹���ֹ棱
    $post2     = $_POST["form_post"]["form_post_no2"];             //͹���ֹ棲
    //͹���ֹ椫���ͼ���
    $post_value = Post_Get($post1,$post2,$db_con);
    $cons_data["form_post"]["form_post_no1"]     = $post1;
    $cons_data["form_post"]["form_post_no2"]     = $post2;
    $cons_data["form_address1"]                  = $post_value[1];  //����1
    $cons_data["form_address2"]                  = $post_value[2];  //����2
    //͹���ֹ�ե饰�򥯥ꥢ
    $cons_data["input_button_flg"] = "";
    $form->setConstants($cons_data);
}

/******************************/
//CSV���ϥܥ��󲡲�����
/*****************************/
if($_POST["csv_button_flg"]==true && $_POST["entry_button"] != "�С�Ͽ" && $_POST["input_button_flg"]!=true){
    /** CSV����SQL **/
    $sql = "SELECT ";
    $sql .= "   t_bank.bank_cd,";               //��ԥ�����
    $sql .= "   t_bank.bank_name,";             //���̾
    $sql .= "   t_bank.bank_cname,";            //���̾��ά�Ρ�
    $sql .= "   t_b_bank.b_bank_cd,";           //��Ź������
    $sql .= "   t_b_bank.b_bank_name,";         //��Ź̾
    $sql .= "   t_b_bank.b_bank_kana,";         //��Ź̾�ʥեꥬ�ʡ�
    $sql .= "   t_b_bank.post_no1,";            //͹���ֹ�1
    $sql .= "   t_b_bank.post_no2,";            //͹���ֹ�2
    $sql .= "   t_b_bank.address1,";            //����1
    $sql .= "   t_b_bank.address2,";            //����2
    $sql .= "   t_b_bank.address3,";            //����3
    $sql .= "   t_b_bank.tel,";                 //TEL
    $sql .= "   t_b_bank.fax,";                 //FAX
    #2010-04-28 hashimoto-y
    $sql .= "   CASE t_b_bank.nondisp_flg";     //��ɽ��
    $sql .= "   WHEN true  THEN '��'";
    $sql .= "   WHEN false THEN ''";
    $sql .= "   END,";

    $sql .= "   t_b_bank.note ";                //����
    $sql .= "FROM ";
    $sql .= "   t_bank";
    $sql .= "   LEFT JOIN";
    $sql .= "   t_b_bank ";
    $sql .= "   ON t_bank.bank_id = t_b_bank.bank_id";
    $sql .= " WHERE ";
    $sql .= "   t_bank.shop_id = $shop_id ";
    $sql .= "ORDER BY t_bank.bank_cd, t_b_bank.b_bank_cd;";

    $result = Db_Query($db_con,$sql);

    //CSV�ǡ�������
    $i=0;
    while($data_list = pg_fetch_array($result)){
        //��ԥ�����
        $bank_data[$i][0] = $data_list[0];
        //���̾
        $bank_data[$i][1] = $data_list[1];
        //���̾�ʥեꥬ�ʡ�
        $bank_data[$i][2] = $data_list[2];
        //ά��
        $bank_data[$i][3] = $data_list[3];
        //��Ź������
        $bank_data[$i][4] = $data_list[4];
        //��Ź̾
        $bank_data[$i][5] = $data_list[5];
        //͹���ֹ�1-͹���ֹ�2(ξ��or������̤���Ϥξ���nullɽ��)
        if($data_list[6] != null && $data_list[6] != null){
            $bank_data[$i][6] = $data_list[6]."-".$data_list[7];
        }else{
            $bank_data[$i][6] = "";
        }
        //����1
        $bank_data[$i][7] = $data_list[8];
        //���ꣲ
        $bank_data[$i][8] = $data_list[9];
        //���ꣳ
        $bank_data[$i][9] = $data_list[10];
            //TEL
        $bank_data[$i][10] = $data_list[11];
        //FAX
        $bank_data[$i][11] = $data_list[12];

        #2010-04-28 hashimoto-y
        #//����
        #$bank_data[$i][12] = $data_list[13];
        //��ɽ��
        $bank_data[$i][12] = $data_list[13];
        //����
        $bank_data[$i][13] = $data_list[14];
        $i++;
    }

    //CSV�ե�����̾
    $csv_file_name = "��Ź�ޥ���".date("Ymd").".csv";
    //CSV�إå�����
    $csv_header = array(
        "��ԥ�����", 
        "���̾",
        "ά��",
        "��Ź������",
        "��Ź̾", 
        "��Ź̾�ʥեꥬ�ʡ�", 
        "͹���ֹ�",
        "���꣱",
        "���ꣲ",
        "���ꣳ",
        "TEL",
        "FAX",
        #2010-04-28 hashimoto-y
        "��ɽ��",
        "����"
    );
    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($bank_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;
}

/****************************/
//�ѹ������ʥ�󥯡�
/****************************/

//�ѹ���󥯲���Ƚ��
if($_GET["b_bank_id"] != ""){

    //�ѹ������󥿥�ID�����
    $update_num = $_GET["b_bank_id"];

    /** ��ԥޥ�������SQL���� **/
    $sql = "SELECT ";
    $sql .= "   bank_id,";
    $sql .= "   b_bank_cd,";           //��Ź������
    $sql .= "   b_bank_name,";         //��Ź̾
    $sql .= "   b_bank_kana,";         //��Ź̾�ʥեꥬ�ʡ�
    $sql .= "   post_no1,";            //͹���ֹ�1
    $sql .= "   post_no2,";            //͹���ֹ�2
    $sql .= "   address1,";            //����1
    $sql .= "   address2,";            //����2
    $sql .= "   address3,";            //����3
    $sql .= "   tel,";                 //TEL
    $sql .= "   fax,";                 //FAX
//    $sql .= "   account_kind,";        //���¼���
//    $sql .= "   account_no,";          //�����ֹ�

    #2010-04-28 hashimoto-y
    $sql .= "nondisp_flg,";         //��ɽ��

    $sql .= "   note ";                //����
    $sql .= " FROM ";
    $sql .= "   t_b_bank ";
    $sql .= " WHERE ";
    $sql .= "   b_bank_id = ".$update_num.";";
    $result = Db_Query($db_con,$sql);
    //GET�ǡ���Ƚ��
    Get_Id_Check($result);
    $data_list = pg_fetch_array($result,0);

    //�ե�������ͤ�����
    $def_fdata["form_bank_select"]                  =    $data_list[0];  
    $def_fdata["form_b_bank_cd"]                    =    $data_list[1];  
    $def_fdata["form_b_bank_name"]                  =    $data_list[2];  
    $def_fdata["form_b_bank_kana"]                  =    $data_list[3];  
    $def_fdata["form_post"]["form_post_no1"]        =    $data_list[4];  
    $def_fdata["form_post"]["form_post_no2"]        =    $data_list[5];  
    $def_fdata["form_address1"]                     =    $data_list[6];  
    $def_fdata["form_address2"]                     =    $data_list[7];  
    $def_fdata["form_address3"]                     =    $data_list[8];  
    $def_fdata["form_tel"]                          =    $data_list[9];  
    $def_fdata["form_fax"]                          =    $data_list[10];  
//    $def_fdata["form_account_kind"]                 =    $data_list[10]; 
//    $def_fdata["form_account_no"]                   =    $data_list[11]; 
    #2010-04-28 hashimoto-y
    #$def_fdata["form_note"]                         =    $data_list[11]; 
    $def_fdata["form_nondisp_flg"]                  =    ($data_list[11] == 't')? 1 : 0;
    $def_fdata["form_note"]                         =    $data_list[12]; 
    $def_fdata["update_flg"]                        =    true; 
    
    $form->setDefaults($def_fdata);
}

/****************************/
//���顼�����å�(AddRule)
/****************************/
//���̾
$form->addRule('form_bank_select','��Ԥ�ɬ�ܹ��ܤǤ���','required');

//����Ź������
//��ɬ�ܥ����å�
$form->addRule('form_b_bank_cd','��Ź������ ��Ⱦ�ѿ����ΤߤǤ���','required');
//��ʸ��������å�
$form->addRule('form_b_bank_cd','��Ź������ ��Ⱦ�ѿ����ΤߤǤ���',"regex", "/^[0-9]+$/");

//����Ź̾
//��ɬ�ܥ����å�
$form->addRule('form_b_bank_name','��Ź̾ ��1ʸ���ʾ�15ʸ���ʲ��Ǥ���','required');
// ����/Ⱦ�ѥ��ڡ����Τߥ����å�
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_b_bank_name", "��Ź̾ �˥��ڡ����Τߤ���Ͽ�ϤǤ��ޤ���", "no_sp_name");

//����Ź̾�ʡ�
//��ɬ�ܥ����å�
$form->addRule('form_b_bank_kana','��Ź̾�ʥեꥬ�ʡ� ��1ʸ���ʾ�15ʸ���ʲ��Ǥ���','required');
// ����/Ⱦ�ѥ��ڡ����Τߥ����å�
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_b_bank_kana", "��Ź̾�ʥեꥬ�ʡ� �˥��ڡ����Τߤ���Ͽ�ϤǤ��ޤ���", "no_sp_name");

//��͹���ֹ�
//��ʸ���������å�
//��ʸ��������å�
$form->addGroupRule('form_post', array(
    'form_post_no1' => array(
        array('͹���ֹ��Ⱦ�ѿ�����7��Ǥ���','rangelength',array(3,3)),
        array('͹���ֹ��Ⱦ�ѿ�����7��Ǥ���',"regex", "/^[0-9]+$/")
    ),
    'form_post_no2' => array(
        array('͹���ֹ��Ⱦ�ѿ�����7��Ǥ���','rangelength',array(4,4)),
        array('͹���ֹ��Ⱦ�ѿ�����7��Ǥ���',"regex", "/^[0-9]+$/"),
    )
));

//�������ֹ�
//��ʸ��������å�
//$form->addRule('form_account_no','�����ֹ� ��Ⱦ�ѿ����ΤߤǤ���',"regex", "/^[0-9]+$/");

//��TEL
//��ʸ��������å�
$form->addRule("form_tel", "TEL��Ⱦ�ѿ����ȡ�-�פΤ�13��Ǥ���", "regex", "/^[0-9-]+$/");

//��FAX
//��ʸ��������å�
$form->addRule("form_fax", "FAX��Ⱦ�ѿ����ȡ�-�פΤ�13��Ǥ���", "regex", "/^[0-9-]+$/");

/****************************/
//��Ͽ����
/****************************/
if($_POST["entry_button"] == "�С�Ͽ"){
    //���ϥե������ͼ���
    $bank_id      = $_POST["form_bank_select"];             //���ID
    $b_bank_cd    = $_POST["form_b_bank_cd"];               //��Ź������
    $b_bank_name  = $_POST["form_b_bank_name"];             //��Ź̾
    $b_bank_kana  = $_POST["form_b_bank_kana"];             //��Ź̾�ʥեꥬ�ʡ�
    $post1        = $_POST["form_post"]["form_post_no1"];   //͹���ֹ�1
    $post2        = $_POST["form_post"]["form_post_no2"];   //͹���ֹ�2
    $address1     = $_POST["form_address1"];                //����1
    $address2     = $_POST["form_address2"];                //����2
    $address3     = $_POST["form_address3"];                //����3
    $tel          = $_POST["form_tel"];                     //TEL
    $fax          = $_POST["form_fax"];                     //FAX
//    $account_kind = $_POST["form_account_kind"];            //���¼��� 
//    $account_no   = $_POST["form_account_no"];              //�����ֹ� 

    #2010-04-28 hashimoto-y
    $nondisp_flg  = ($_POST["form_nondisp_flg"] == '1')? 't' : 'f';             //��ɽ��

    $note         = $_POST["form_note"];                    //���� 
    $update_flg   = $_POST["update_flg"];                   //��Ͽ������Ƚ��
    $update_num   = $_GET["b_bank_id"];                       //���ID


    /****************************/
    //���顼�����å�(PHP)
    /****************************/
    //���ƽХ�����
    //����ʣ�����å�
    if($b_bank_cd != null && $bank_id != null){
        //�ƽХ����ɤˣ�������
        $b_bank_cd = str_pad($b_bank_cd, 3, 0, STR_POS_LEFT);
        //���Ϥ��������ɤ��ޥ�����¸�ߤ��뤫�����å�
        $sql  = "SELECT ";
        $sql .= "   b_bank_cd "; 
        $sql .= " FROM ";
        $sql .= "   t_b_bank ";
        $sql .= " WHERE ";
        $sql .= "   bank_id = $bank_id";
        $sql .= "   AND";
        $sql .= "   b_bank_cd = '$b_bank_cd' ";

        //�ѹ��ξ��ϡ���ʬ�Υǡ����ʳ��򻲾Ȥ���
        if($update_num != null && $update_flg == true){
            $sql .= " AND NOT ";
            $sql .= "b_bank_id = '$update_num'";
        }
        $sql .= ";";
        $result = Db_Query($db_con, $sql);
        $row_count = pg_num_rows($result);
        if($row_count != 0){
            $form->setElementError("form_b_bank_cd","���˻��Ѥ���Ƥ��� ��Ź������ �Ǥ���");
        }
    }

    //����Ź̾�ʥեꥬ�ʡ�
    //�������ʸ���������Ѳ�ǽ
    if (!mb_ereg("^[0-9A-Z��-�ݎގߎ��� ]+$", $b_bank_kana)){
        $form->setElementError("form_b_bank_kana", "��Ź̾�ʥեꥬ�ʡ� �ϡ�Ⱦ�ю��š���ʸ���ˤ�Ⱦ�ѱѿ�������ʸ���ˤΤ߻��Ѳ�ǽ�Ǥ���");
    }


    //���顼�κݤˤϡ���Ͽ������Ԥ�ʤ�
    if($form->validate()){
        
        Db_Query($db_con, "BEGIN;");

        //��������Ͽ��Ƚ��
        if($update_flg == true){
            //��ȶ�ʬ�Ϲ���
            $work_div = '2';
            //�ѹ���λ��å�����
            $comp_msg = "�ѹ����ޤ�����";

            $sql  = "UPDATE \n";
            $sql .= "t_b_bank \n";
            $sql .= "SET \n";
            $sql .= "bank_id = '$bank_id',\n";
            $sql .= "b_bank_cd = '$b_bank_cd',\n";
            $sql .= "b_bank_name = '$b_bank_name',\n";
            $sql .= "b_bank_kana = '$b_bank_kana',\n";
            $sql .= "post_no1 = '$post1',\n";
            $sql .= "post_no2 = '$post2',\n";
            $sql .= "address1 = '$address1',\n";
            $sql .= "address2 = '$address2',\n";
            $sql .= "address3 = '$address3',\n";
            $sql .= "tel = '$tel',\n";
            $sql .= "fax = '$fax',\n";
//            $sql .= "account_kind = '$account_kind',";
//            $sql .= "account_no = '$account_no',";
            #2010-04-28 hashimoto-y
            $sql .= "nondisp_flg = '$nondisp_flg',";
            $sql .= "note = '$note' \n";
            $sql .= "WHERE \n";
            $sql .= "b_bank_id = $update_num;\n";
        }else{
            //��ȶ�ʬ����Ͽ
            $work_div = '1';
            //��Ͽ��λ��å�����
            $comp_msg = "��Ͽ���ޤ�����";

            $sql  = "INSERT INTO t_b_bank (\n";
            $sql .= "   b_bank_id,\n";
            $sql .= "   bank_id,\n";
            $sql .= "   b_bank_cd,\n";
            $sql .= "   b_bank_name,\n";
            $sql .= "   b_bank_kana,\n";
//            $sql .= "   account_kind,";
//            $sql .= "   account_no,";
            $sql .= "   post_no1,\n";
            $sql .= "   post_no2,\n";
            $sql .= "   address1,\n";
            $sql .= "   address2,\n";
            $sql .= "   address3,\n";
            $sql .= "   tel,\n";
            $sql .= "   fax,\n";
            #2010-04-28 hashimoto-y
            $sql .= "   nondisp_flg,";
            $sql .= "   note\n";
            $sql .= ")VALUES(\n";
            $sql .= "(SELECT COALESCE(MAX(b_bank_id), 0)+1 FROM t_b_bank),\n";
            $sql .= "   $bank_id,\n";
            $sql .= "   '$b_bank_cd',\n";
            $sql .= "   '$b_bank_name',\n";
            $sql .= "   '$b_bank_kana',\n";
//            $sql .= "   '$account_kind',";
//            $sql .= "   '$account_no',";
            $sql .= "   '$post1',\n";
            $sql .= "   '$post2',\n";
            $sql .= "   '$address1',\n";
            $sql .= "   '$address2',\n";
            $sql .= "   '$address3',\n";
            $sql .= "   '$tel',\n";
            $sql .= "   '$fax',\n";
            #2010-04-28 hashimoto-y
            $sql .= "   '$nondisp_flg',";
            $sql .= "   '$note'\n";
            $sql .= ");";
        }

        $result = Db_Query($db_con,$sql);
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            exit;
        }
        //��ԥޥ������ͤ�����˽񤭹���
        $result = Log_Save($db_con,'b_bank',$work_div,$b_bank_cd,$b_bank_name);
        //������Ͽ���˥��顼�ˤʤä����
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            exit;
        }
        Db_Query($db_con, "COMMIT;");

        //�ե�������ͤ�����
        $def_fdata["form_bank_select"]                        =    "";
        $def_fdata["form_b_bank_cd"]                       =    "";
        $def_fdata["form_b_bank_name"]                      =    "";
        $def_fdata["form_b_bank_kana"]                      =    "";
        $def_fdata["form_bank_cname"]                     =    "";
        $def_fdata["form_post"]["form_post_no1"]          =    "";
        $def_fdata["form_post"]["form_post_no2"]          =    "";
        $def_fdata["form_address1"]                       =    "";
        $def_fdata["form_address2"]                       =    "";
        $def_fdata["form_address3"]                       =    "";
        $def_fdata["form_tel"]                            =    "";
        $def_fdata["form_fax"]                            =    "";
//        $def_fdata["form_account_kind"]                   =    "";
//        $def_fdata["form_account_no"]                     =    "";
        #2010-04-28 hashimoto-y
        $def_fdata["form_nondisp_flg"]                    =    "";
        $def_fdata["form_note"]                           =    "";
        $def_fdata["update_flg"]                          =    "";

        $form->setConstants($def_fdata);
    }
}

/******************************/
//�إå�����ɽ�������������
/*****************************/
/** ��ԥޥ�������SQL���� **/
$sql  = "SELECT ";
$sql .= "   t_bank.bank_cd,";           //��ԥ�����
$sql .= "   t_bank.bank_name,";         //���̾
$sql .= "   t_bank.bank_cname,";        //ά��
$sql .= "   t_b_bank.b_bank_id,";       //��ŹID
$sql .= "   t_b_bank.b_bank_cd,";       //��Ź������
$sql .= "   t_b_bank.b_bank_name, ";    //��Ź̾
$sql .= "   t_b_bank.b_bank_kana, ";    //��Ź̾
//$sql .= "   t_b_bank.account_kind,";
//$sql .= "   t_b_bank.account_no,";
#2010-04-28 hashimoto-y
$sql .= "   CASE t_b_bank.nondisp_flg"; //��ɽ��
$sql .= "   WHEN true  THEN '��'";
$sql .= "   WHEN false THEN ''";
$sql .= "   END,";

$sql .= "   t_b_bank.note";
$sql .= " FROM ";
$sql .= "   t_bank";
$sql .= "   LEFT JOIN";
$sql .= "   t_b_bank ";
$sql .= "   ON t_bank.bank_id = t_b_bank.bank_id";
$sql .= " WHERE ";
$sql .= "   t_bank.shop_id = $shop_id ";
$sql .= "   ORDER BY t_bank.bank_cd, t_b_bank.b_bank_cd;";

$result = Db_Query($db_con,$sql);
//���������(�إå���)
$total_count = pg_num_rows($result);

//�ԥǡ������ʤ����
$row = Get_Data($result);

for($i = 0; $i < $total_count; $i++){
    for($j = 0; $j < $total_count; $j++){
        if($i != $j && $row[$i][0] == $row[$j][0]){
            $row[$j][0] = null;
            $row[$j][1] = null;
            $row[$j][2] = null;
        }
    }
}

for($i = 0; $i < $total_count; $i++){
    if($row[$i][0] == null){
        $tr[$i] = $tr[$i-1];
    }else{  
        if($tr[$i-1] == "Result1"){
            $tr[$i] = "Result2";
        }else{  
            $tr[$i] = "Result1";
        }       
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
$page_menu = Create_Menu_h('system','1');

/****************************/
//���̥إå�������
/****************************/
$page_title .= "(��".$total_count."��)";
$page_title .= "��".$form->_elements[$form->_elementIndex[bank_button]]->toHtml();
$page_title .= "��".$form->_elements[$form->_elementIndex[bank_mine_button]]->toHtml();
$page_title .= "��".$form->_elements[$form->_elementIndex[bank_account_button]]->toHtml();

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
    'total_count'   => "$total_count",
    'comp_msg'      => "$comp_msg",
    'auth_r_msg'    => "$auth_r_msg",
));
$smarty->assign('row',$row);
$smarty->assign('tr',$tr);

//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>