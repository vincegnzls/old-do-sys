<?php
// �ѹ����ʤ��ǲ�����

// �ѥ������
define(PATH , "../../../");

// �ؿ��ե�������ɹ���
require_once(PATH ."function/INCLUDE.php");

//���å����Υ����å�
Session_Check_h();

// Smarty+QuickForm
require_once("Smarty/Smarty.class.php");
require_once("HTML/QuickForm.php");
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

$smarty = new Smarty();   // Smarty���֥������Ȥ�����
$smarty->template_dir = "templates";
$smarty->compile_dir = "templates_c";

?>