<?php

//�Ķ�����ե�����
require_once("ENV_local.php");
require(FPDF_DIR);

//��٥���ϴؿ��ե�����
require_once(INCLUDE_DIR."function_label.inc");


// DB��³����
$db_con = Db_Connect();

// ���¥����å�
$auth = Auth_Check($db_con);

//����å�ID�����
$shop_id = $_POST["form_label_check"];

//����åפ��ȤΥ�٥��ɽ������ǡ��������
$label_data = Get_Label_Data($db_con, $shop_id);

if($label_data === false){
    print "<b>";
    print " <font color=#ff0000><li>��٥�������������������򤷤Ʋ�������</font>";
    print "</b>";

    exit;
}


$pdf=new MBFPDF('P','pt','a4');

//PDF����
Make_Label_Pdf ($pdf, $label_data);

?>