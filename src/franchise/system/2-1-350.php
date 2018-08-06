<?php
/*
 * ����
 * ���ա�������BɼNo.��������ô���ԡ��������ơ�
 *  -----------------------------------------------------------
 *   2015/05/01                amano  Dialogue�ؿ��ǥܥ���̾�������ʤ� IE11 �Х��б�
 * 
 */
$page_title = "��������";

//�Ķ�����ե�����
require_once("ENV_local.php");

//DB��³
$db_con = Db_Connect();
	
//HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

// ���¥����å�
$auth       = Auth_Check($db_con);
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

if($_POST["form_all_clear"] != "�����ꥢ"){
	/****************************/
	//�ǥե������
	/****************************/
	$sql  = "SELECT ";
	$sql .= "    holiday ";
	$sql .= "FROM ";
	$sql .= "    t_holiday ";
	$sql .= "WHERE ";
	$sql .= "    shop_id = $_SESSION[client_id] ";

	$result = Db_Query($db_con,$sql);
	//DB���ͤ��������¸
	$holiday = Get_Data($result,2);

	for($i=0;$i<count($holiday);$i++){
		$today_date = "holiday[".date($holiday[$i][0])."]";
		$def_data[$today_date] = 1;
	}
	$form->setDefaults($def_data);
}else{
	/****************************/
	//�ǥե������
	/****************************/
	$sql  = "SELECT ";
	$sql .= "    holiday ";
	$sql .= "FROM ";
	$sql .= "    t_holiday ";
	$sql .= "WHERE ";
	$sql .= "    shop_id = $_SESSION[client_id] ";

	$result = Db_Query($db_con,$sql);
	//DB���ͤ��������¸
	$holiday = Get_Data($result,2);

	for($i=0;$i<count($holiday);$i++){
		$today_date = "holiday[".date($holiday[$i][0])."]";
		$def_data[$today_date] = "";
	}
	$form->setDefaults($def_data);
}

/****************************/
//�������
/****************************/
//��Ͽ�ܥ���
$form->addElement(
    "submit","form_set","�ߡ���",
    "onClick=\"javascript:return Dialogue('���ꤷ�ޤ���','#', this)\" $disabled"
);

$form->addElement(
	"button","form_clear","���ꥢ",
	"onClick=\"location.href='$_SERVER[PHP_SELF]'\""
);

//��������(�����������ɤ򸫤䤹�����뤿������)
define('YEAR', 0);
define('MONTH', 1);
define('DAY', 2);

//����������(ǯ�����)��$date������Ȥ�������
$date = date('Y n d');
$date = explode(' ', $date);
$date[MONTH] = (int) $date[MONTH]-1;

for($m2=1;$m2<7;$m2++){
	$calendar .= '<table cellpadding="15"><tr><td valign="top">'."\n";

	for($m1=1;$m1<5;$m1++){
		//����Ū���ѿ������������Ѵ�
		if($date_flg1!=true){
			$date[MONTH] = 0;
			$date_flg1=true;
		}
		$date[YEAR] = (int) $date[YEAR];
		$date[DAY] = (int) $date[DAY];
		//�12����ä��鼡�η��1��
		if($date[MONTH] == 12){
			$date[MONTH] = 1;
			$date[YEAR] = $date[YEAR]+1;
		//12��ʳ����ä���ܣ���
		}else{
			$date[MONTH] = $date[MONTH]+1;
		}

		//������������ǽ�������Ǹ����������������
		$days = date('d', mktime(0, 0, 0, $date[MONTH]+1, 0, $date[YEAR]));
		$first_day = date('w', mktime(0, 0, 0, $date[MONTH], 1, $date[YEAR]));
		$last_day = date('w', mktime(0, 0, 0, $date[MONTH], $days, $date[YEAR]));

		if($first_day == 0){
			$first_day = 6;
		}else{
			$first_day = $first_day-1;
		}
		
		if($last_day == 0){
			$last_day = 6;
		}else{
			$last_day = $last_day-1;
		}
		
		//�Ǹ�ν�������������
		$last_week_days = ($days + $first_day) % 7;
		if ($last_week_days == 0){
			$weeks = ($days + $first_day) / 7;
		}else{
			$weeks = ceil(($days + $first_day) / 7);
		}
		//����������ɽ�Ȥ��ƽ��Ϥ���
		$calendar .= '<table class=\'List_Table\' border=\'1\' width=\'200\'>'."\n";
		$calendar .= '<caption><b><font size="+1">��'.$date[YEAR].'ǯ'.$date[MONTH].'���</font></b></caption>';

		$calendar .= '<tr><th class=\'Title_Purple\'>��</th><th class=\'Title_Purple\'>��</th><th class=\'Title_Purple\'>��</th><th class=\'Title_Purple\'>��</th><th class=\'Title_Purple\'>��</th><th class=\'Title_Purple\'>��</th><th class=\'Title_Purple\'>��</th></tr>';

		$i=$j=$day=0;
		while($i<$weeks){
			$calendar .= '<tr class=\'Result1\' align=\'center\'>'."\n";
			$j=0;
			while($j<7){
				$calendar .= '<td';
				if(($i==0 && $j<$first_day) || ($i==$weeks-1 && $j>$last_day)){
					$calendar .= '> '."\n";
				}else{
					$calendar .= '>'."\n";
					++$day;
					//�����å��ܥå������
					$today = "holiday[".$date[YEAR]."-".str_pad($date[MONTH], 2, 0, STR_PAD_LEFT)."-".str_pad($day, 2, 0, STR_PAD_LEFT)."]";
					$form->addElement('checkbox',$today, '�����å��ܥå���', "<br>"." $day");
					$calendar .= $form->_elements[$form->_elementIndex[$today]]->toHtml();
				}
				$calendar .= '</td>'."\n";
				$j++;
			}
			$calendar .= '</tr>'."\n";
			$i++;
		}
		$calendar .= '</table></td><td valign="top">'."\n";
	}
	$calendar .= '</tr>'."\n";
}

//����ܥ��󲡲���
if($_POST["form_set"] == "�ߡ���"){
	//��Ͽ��λ��å�����
	$fin_msg = "��Ͽ���ޤ�����";
	//���դ�����ˤ���
	$holiday = array_keys($_POST[holiday]);

	Db_Query($db_con, "BEGIN;");
	$sql = "DELETE FROM ";
	$sql .= "t_holiday ";
	$sql .= "WHERE ";
	$sql .= "shop_id = $_SESSION[client_id];";

	for($i = 0;$i<count($holiday);$i++){
		$sql .= "INSERT INTO ";
		$sql .= "t_holiday( ";
		$sql .= "holiday,";
		$sql .= "shop_id";
		$sql .= ")VALUES(";
		$sql .= "'$holiday[$i]',";
		$sql .= "'$_SESSION[client_id]');";
	}
	$result = Db_Query($db_con,$sql);
	if($result == false){
		Db_Query($db_con,"ROLLBACK;");
		exit;
	}else{
		Db_Query($db_con,"COMMIT;");
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
//���̥إå�������
/****************************/
$page_header = Create_Header($page_title);

// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form��Ϣ���ѿ���assign
$smarty->assign('form',$renderer->toArray());

//����¾���ѿ���assign
$smarty->assign('var',array(
	'html_header'   => "$html_header",
//	'page_menu'     => "$page_menu",
	'page_header'   => "$page_header",
//	'html_footer'   => "$html_footer",
	'calendar'   => "$calendar",
	'onload'     => "$onload",
	'fin_msg'   	=> "$fin_msg",
));

//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>