<?php
/**********************************/
//�ѹ�����
//  DB�����ѹ��ˤȤ�ʤ��������ѹ�9.11
//
//
//
//
//
/**********************************/



$page_title = "���������";

// �Ķ�����ե�����
require_once("ENV_local.php");

// HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

// DB��³
$db_con = Db_Connect();

// ���¥����å�
$auth       = Auth_Check($db_con);
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
// �����ѿ�����
/****************************/
$client_id  = $_SESSION[h_client_id];

/****************************/
// �ǥե������
/****************************/
$def_data["font_size_select_0"] = "12";
$def_data["font_size_select_1"] = "9";
$def_data["font_size_select_2"] = "6";
$def_data["font_size_select_3"] = "6";

$form->setDefaults($def_data);

/****************************/
// �������
/****************************/
// ���쥯�ȥܥå���
$select_value = Select_Get($db_con, "claim_pattern");
$form->addElement("select", "pattern_select", "", $select_value, "size=\"5\" style=\"width: 350px;\"");

// �ѥ�����̾
$commit_freeze[] = $form->addElement("text", "pattern_name", "", "size=\"34\" maxLength=\"30\" $g_form_option");

// ���̾
$commit_freeze[] = $form->addElement("text", "c_memo1", "", "maxLength=\"46\" style=\"width: 300px; font-size: 15px;\" $g_form_option");
// ��ɽ������
$commit_freeze[] = $form->addElement("text", "c_memo2", "", "maxLength=\"46\" style=\"width: 300px; font-size: 13px;\" $g_form_option");
// ����
$commit_freeze[] = $form->addElement("text", "c_memo3", "", "maxLength=\"46\" style=\"width: 300px; font-size: 10px;\" $g_form_option");
// TEL/FAX
$commit_freeze[] = $form->addElement("text", "c_memo4", "", "maxLength=\"46\" style=\"width: 300px; font-size: 10px;\" $g_form_option");
// ������
for($x=5;$x<=12;$x++){
    $commit_freeze[] = $form->addElement("text", "c_memo".$x,"", "size=\"56\" maxLength=\"62\" style=\"font-size: 10px;\" $g_form_option");
}
// ������
$commit_freeze[] = $form->addElement("text", "c_memo13", "", "size=\"119\" maxLength=\"110\" style=\"font-size: 10px;\" $g_form_option");

// �����ȯ�ԥܥ���
$form->addElement("submit", "preview_button", "�ץ�ӥ塼", "onClick=\"javascript:PDF_POST('1-1-310-2.php')\"");

// ������Ͽ�ܥ���
$form->addElement("submit", "form_new_button", "������Ͽ", "onClick=\"javascript:Button_Submit('form_new_flg','#','true')\" $disabled");

// ��Ͽ�ܥ���
$form->addElement("submit", "new_button", "�С�Ͽ", "onClick=\"javascript:return Dialogue('��Ͽ���ޤ���','#')\" $disabled");

// �ѹ��ܥ���
$form->addElement("submit", "change_button", "�ѡ���", "onClick=\"javascript:Button_Submit('form_update_flg','#','true')\" $disabled");

// ���ꥢ�ܥ���
$form->addElement("button", "clear_button", "���ꥢ", "onClick=\"location.href='$_SERVER[PHP_SELF]'\"");

// OK�ܥ���
$form->addElement("button", "ok_button", "�ϡ���", "onClick=javascript:location.href='1-1-310.php' $disabled");

// �ե���ȥ�����
for($i=0; $i<4; $i++){
    $data["5"]  = "5";
    $data["6"]  = "6";
    $data["7"]  = "7";
    $data["8"]  = "8";
    $data["9"]  = "9";
    $data["10"] = "10";
    $data["11"] = "11";
    $data["12"] = "12";
    $data["13"] = "13";
    $data["14"] = "14";
    $data["15"] = "15";
    $form->addElement("select", "font_size_select_".$i, "", $data);
}

// hidden
$form->addElement("hidden", "form_new_flg");       // ������Ͽ�ܥ���ե饰
$form->addElement("hidden", "form_update_flg");    // �ѹ��ܥ���ե饰
$form->addElement("hidden", "h_pattern_id");       // �ѥ�����

/****************************/
// �ѹ��ܥ��󲡲�����
/****************************/
if($_POST["change_button"] == "�ѡ���"){
    if($_POST[pattern_select]!=null){
        $sql  = "SELECT ";
        $sql .= "    c_pattern_name,";  // �ѥ�����̾
        $sql .= "    c_memo1, ";        // ������1
        $sql .= "    c_memo2, ";        // ������2
        $sql .= "    c_memo3, ";        // ������3
        $sql .= "    c_memo4, ";        // ������4
        $sql .= "    c_fsize1,";        // ������1�ե���ȥ�����
        $sql .= "    c_fsize2,";        // ������2�ե���ȥ�����
        $sql .= "    c_fsize3,";        // ������3�ե���ȥ�����
        $sql .= "    c_fsize4,";        // ������4�ե���ȥ�����
        $sql .= "    c_memo5, ";        // ������5
        $sql .= "    c_memo6, ";        // ������6
        $sql .= "    c_memo7, ";        // ������7
        $sql .= "    c_memo8, ";        // ������8
        $sql .= "    c_memo9, ";        // ������9
        $sql .= "    c_memo10, ";       // ������10
        $sql .= "    c_memo11, ";       // ������11
        $sql .= "    c_memo12, ";       // ������12
        $sql .= "    c_memo13 ";        // ������13
        $sql .= "FROM ";
        $sql .= "    t_claim_sheet ";
        $sql .= "WHERE ";
        $sql .= "    shop_id = $client_id ";
        $sql .= "AND ";
        $sql .= "    c_pattern_id = $_POST[pattern_select];";

        $result = Db_Query($db_con,$sql);
        // DB���ͤ��������¸
        $c_memo = Get_Data($result,2);

        // �ǡ�����ե�����˥��åȤ���
        $update_button_data["pattern_name"]        = $c_memo[0][0];
        $update_button_data["c_memo1"]             = $c_memo[0][1];
        $update_button_data["c_memo2"]             = $c_memo[0][2];
        $update_button_data["c_memo3"]             = $c_memo[0][3];
        $update_button_data["c_memo4"]             = $c_memo[0][4];
        $update_button_data["font_size_select_0"]  = $c_memo[0][5];
        $update_button_data["font_size_select_1"]  = $c_memo[0][6];
        $update_button_data["font_size_select_2"]  = $c_memo[0][7];
        $update_button_data["font_size_select_3"]  = $c_memo[0][8];
        $update_button_data["c_memo5"]             = $c_memo[0][9];
        $update_button_data["c_memo6"]             = $c_memo[0][10];
        $update_button_data["c_memo7"]             = $c_memo[0][11];
        $update_button_data["c_memo8"]             = $c_memo[0][12];
        $update_button_data["c_memo9"]             = $c_memo[0][13];
        $update_button_data["c_memo10"]            = $c_memo[0][14];
        $update_button_data["c_memo11"]            = $c_memo[0][15];
        $update_button_data["c_memo12"]            = $c_memo[0][16];
        $update_button_data["c_memo13"]            = $c_memo[0][17];
        $update_button_data["form_new_flg"]        = false;
        $update_button_data["h_pattern_id"]        = $_POST["pattern_select"];    // ���򤵤줿�ѥ������hidden�˥��åȤ���
        $form->setConstants($update_button_data);
    }else{
        $pattern_err = "���ѹ�����ѥ���������򤷤Ƥ���������";
    }
}

/****************************/
// ��Ͽ�ܥ��󲡲�����
/****************************/
if (isset($_POST["new_button"])){

    /****************************/
    // ���顼�����å�
    /****************************/
    // �ѥ�����̾
    // ��ɬ�ܥ����å�
    $form->addRule("pattern_name", "�ѥ�����̾��30ʸ������Ǥ���", "required");

    $qf_err_flg = ($form->validate() == false) ? true : false;

}

if($_POST["new_button"] == "�С�Ͽ" && $qf_err_flg === false){
    $c_pattern_name = $_POST["pattern_name"];           // �ѥ�����̾
    $c_memo1        = $_POST["c_memo1"];                // ������1
    $c_memo2        = $_POST["c_memo2"];                // ������2
    $c_memo3        = $_POST["c_memo3"];                // ������3
    $c_memo4        = $_POST["c_memo4"];                // ������4
    $c_fsize1       = $_POST["font_size_select_0"];     // ������1(�ե���ȥ�����)
    $c_fsize2       = $_POST["font_size_select_1"];     // ������2(�ե���ȥ�����)
    $c_fsize3       = $_POST["font_size_select_2"];     // ������3(�ե���ȥ�����)
    $c_fsize4       = $_POST["font_size_select_3"];     // ������4(�ե���ȥ�����)
    $c_memo5        = $_POST["c_memo5"];                // ������5
    $c_memo6        = $_POST["c_memo6"];                // ������6
    $c_memo7        = $_POST["c_memo7"];                // ������7
    $c_memo8        = $_POST["c_memo8"];                // ������8
    $c_memo9        = $_POST["c_memo9"];                // ������9
    $c_memo10       = $_POST["c_memo10"];               // ������10
    $c_memo11       = $_POST["c_memo11"];               // ������11
    $c_memo12       = $_POST["c_memo12"];               // ������12
    $c_memo13       = $_POST["c_memo13"];               // ������13
    $pattern_id     = $_POST["h_pattern_id"];           // �ѥ�����ID
    
    Db_Query($db_con, "BEGIN;");

    // ������Ͽ������Ƚ��
    if($_POST["form_new_flg"] ==true){
        $sql  = "INSERT INTO ";
        $sql .= "t_claim_sheet ";
        $sql .= "(shop_id,";
        $sql .= "c_pattern_id,";
        $sql .= "c_pattern_name,";
        $sql .= "c_memo1,";
        $sql .= "c_memo2,";
        $sql .= "c_memo3,";
        $sql .= "c_memo4,";
        $sql .= "c_fsize1,";
        $sql .= "c_fsize2,";
        $sql .= "c_fsize3,";
        $sql .= "c_fsize4,";
        $sql .= "c_memo5,";
        $sql .= "c_memo6,";
        $sql .= "c_memo7,";
        $sql .= "c_memo8,";
        $sql .= "c_memo9,";
        $sql .= "c_memo10,";
        $sql .= "c_memo11,";
        $sql .= "c_memo12,";
        $sql .= "c_memo13) ";
        $sql .= "VALUES(";
        $sql .= "'$client_id',";
        $sql .= "(SELECT COALESCE(MAX(c_pattern_id), 0)+1 FROM t_claim_sheet),";
        $sql .= "'$c_pattern_name',";
        $sql .= "'$c_memo1',";
        $sql .= "'$c_memo2',";
        $sql .= "'$c_memo3',";
        $sql .= "'$c_memo4',";
        $sql .= "'$c_fsize1',";
        $sql .= "'$c_fsize2',";
        $sql .= "'$c_fsize3',";
        $sql .= "'$c_fsize4',";
        $sql .= "'$c_memo5',";
        $sql .= "'$c_memo6',";
        $sql .= "'$c_memo7',";
        $sql .= "'$c_memo8',";
        $sql .= "'$c_memo9',";
        $sql .= "'$c_memo10',";
        $sql .= "'$c_memo11',";
        $sql .= "'$c_memo12',";
        $sql .= "'$c_memo13');";
    }else{
        $sql  = "UPDATE ";
        $sql .= "t_claim_sheet ";
        $sql .= "SET ";
        $sql .= "c_memo1        = '$c_memo1', ";
        $sql .= "c_pattern_name = '$c_pattern_name',";
        $sql .= "c_memo2        = '$c_memo2', ";
        $sql .= "c_memo3        = '$c_memo3', ";
        $sql .= "c_memo4        = '$c_memo4', ";
        $sql .= "c_fsize1       = '$c_fsize1', ";
        $sql .= "c_fsize2       = '$c_fsize2', ";
        $sql .= "c_fsize3       = '$c_fsize3', ";
        $sql .= "c_fsize4       = '$c_fsize4', ";
        $sql .= "c_memo5        = '$c_memo5', ";
        $sql .= "c_memo6        = '$c_memo6', ";
        $sql .= "c_memo7        = '$c_memo7', ";
        $sql .= "c_memo8        = '$c_memo8', ";
        $sql .= "c_memo9        = '$c_memo9', ";
        $sql .= "c_memo10       = '$c_memo10', ";
        $sql .= "c_memo11       = '$c_memo11', ";
        $sql .= "c_memo12       = '$c_memo12', ";
        $sql .= "c_memo13       = '$c_memo13' ";
        $sql .= "WHERE ";
        $sql .= "   shop_id = $client_id";
        $sql .= "   AND ";
        $sql .= "   c_pattern_id = $pattern_id;";
    }

    $result = Db_Query($db_con,$sql);
    if($result == false){
        Db_Query($db_con,"ROLLBACK;");
        exit;
    }
    $claim_data[0]  = $c_pattern_name;
    $claim_data[1]  = $c_memo1;
    $claim_data[2]  = $c_memo2;
    $claim_data[3]  = $c_memo3;
    $claim_data[4]  = $c_memo4;
    $claim_data[5]  = $c_memo5;
    $claim_data[6]  = $c_memo6;
    $claim_data[7]  = $c_memo7;
    $claim_data[8]  = $c_memo8;
    $claim_data[9]  = $c_memo9;
    $claim_data[10] = $c_memo10;
    $claim_data[11] = $c_memo11;
    $claim_data[12] = $c_memo12;
    $claim_data[13] = $c_memo13;

    $commit_flg = true;
    Db_Query($db_con, "COMMIT;");
}

if ($commit_flg == true){
    $commit_freeze_form = $form->addGroup($commit_freeze, "commit_freeze", "");
    $commit_freeze_form->freeze();
}

/****************************/
// HTML�إå�
/****************************/
//$html_header = Html_Header($page_title);
$html_header = Html_Header($page_title, "amenity.js", "global.css", "slip.css");

/****************************/
// HTML�եå�
/****************************/
$html_footer = Html_Footer();

/****************************/
// ��˥塼����
/****************************/
$page_menu = Create_Menu_h("system", "2");

/****************************/
// ���̥إå�������
/****************************/
$page_header = Create_Header($page_title);


// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

// form��Ϣ���ѿ���assign
$smarty->assign("form", $renderer->toArray());

$smarty->assign("row", $claim_data);

// ����¾���ѿ���assign
$smarty->assign("var", array(
    "html_header"   => "$html_header",
    "page_menu"     => "$page_menu",
    "page_header"   => "$page_header",
    "html_footer"   => "$html_footer",
    "comp_msg"      => "$comp_msg",
    "commit_flg"    => "$commit_flg",
    "pattern_err"   => "$pattern_err",
));

// �ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF].".tpl"));

?>