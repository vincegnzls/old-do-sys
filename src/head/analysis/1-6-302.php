<?php
$page_title = "���ӥǡ���";

//�Ķ�����ե�����
require_once("ENV_local.php");

//HTML_QuickForm�����
$form =& new HTML_QuickForm( "$_SERVER[PHP_SELF]","POST");

//DB��³
$db_con = Db_Connect();

// ���¥����å�
$auth       = Auth_Check($db_con);

//�������ѥǡ���
$button[] = $form->createElement("button","csvh26","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-103.php')\"");
$button[] = $form->createElement("button","csvh27","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-101.php')\"");
$button[] = $form->createElement("button","csvh28","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-100.php')\"");
$button[] = $form->createElement("button","csvh29","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-102.php')\"");
$button[] = $form->createElement("button","csvh30","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-108.php')\"");
$button[] = $form->createElement("button","csvh31","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-105.php')\"");
$button[] = $form->createElement("button","csvh32","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-106.php')\"");
$button[] = $form->createElement("button","csvh33","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-107.php')\"");
$button[] = $form->createElement("button","csvh34","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-112.php')\"");
$button[] = $form->createElement("button","csvh35","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-111.php')\"");
$button[] = $form->createElement("button","csvh36","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-122.php')\"");
$button[] = $form->createElement("button","csvh37","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-121.php')\"");
$button[] = $form->createElement("button","csvh38","���ϲ��̤�","onClick=\"javascript:location.href('".HEAD_DIR."analysis/1-6-131.php')\"");
$form->addGroup($button, "button", "");

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
$page_menu = Create_Menu_h('analysis','3');

/****************************/
//���̥إå�������
/****************************/
$page_header = Create_Header($page_title);


/****************************/
//�ڡ�������
/****************************/
//���η��
$total_count = 100;

//ɽ���ϰϻ���
$range = "20";

//�ڡ����������
$page_count = $_POST["f_page1"];

$html_page = Html_Page($total_count,$page_count,1,$range);
$html_page2 = Html_Page($total_count,$page_count,2,$range);



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
	'html_page'     => "$html_page",
	'html_page2'    => "$html_page2",
));

//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>