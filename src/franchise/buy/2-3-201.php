<?php
/*******************************
//�ѹ�����
    shop_aid����shop_id���ѹ�
    �쥤�������ѹ���ȼ������۽����ѹ�
    ɽ���ܥ�������ΥХ�����
  (2006-07-07 kaji)
    shop_gid��ʤ���
  (2006-07-28)
    ȯ�����Ǿä���������������Ǥ��ä��褦���ѹ�
  (2006-07-29)
    �������ƥ���Ĥ����������
  (2006-08-10)
    �Ͱ��������ʤν������ɲ�
  (2006-08-21)
    ��ʧ���������ɲ�
  (2006-08-23 kaji)
    �����̾�Υ��ڥ�ߥ�������stete �� state��
  (2006-08-29)
    ���ID���Ҳ�ID����������ѹ��Բ�
    ����ñ�����ѹ��ˤ���
  (2006-08-29)watanabe-k
    ��ʧ������������
    ����ʧ���ǡ�������ä�
  (2006-09-16) kaji
    ���������ǥե����2���
  (2006-09-20) fukuda-sss
    ���½�������
  (2006-10-03) watanabe-k
    ȯ�����鵯�����������ǤϤʤ����������ѹ��Ǥ���褦���ѹ�
    ����ͽ�����ν�����򤹤�褦���ѹ�
  (2006-10-05) watanabe-k
    ��ȯ���ֹ��֥�󥯤ˤ���ɽ���ܥ���򥯥�å��������˽��ɽ�����ݤ�����ʤ��Х��ν���
    ������ñ���ξ������ʲ�2���֥�󥯤ˤ��ơ�������ǧ���̤إܥ���򥯥�å�����ȥ��顼��å�������ɽ�������Х��ν���
  (2006-10-07)
    �������Ȳ���̤���������ϲ��̤ز������ܤ������˾���̾�ѹ��Բľ��ʤξ���̾���Խ��Ǥ��Ƥ��ޤ��Х��ν���
    �����������������ˣ������¸�ߤ��ʤ�������Ϥ���ȡ��������˷�����������������դ���Ͽ�Ǥ��ޤ��󡣤ȥ��顼ʸ����ɽ�������Х��ν���
    ��ȯ�������¿��������뤳�Ȥ��Ǥ��Ƥ��äޤ��Х��ν���
    ���������ۤ��������׻�����Ƥ��ʤ��Х��ν���
    ��������FC�ǥܥ����ʸ�����ۤʤ�Х��ν���
�������������󥯤�ɽ������Ƥ��ʤ��Х��ν���
  (2006-10-11)
    �������ѹ����˻����ѿ���ե�����˥��åȤ��Ƥ��ʤ��Х��ν���
    ���߸˴������ʤ����ʤˤĤ��Ƹ��߸Ŀ���ݤ�ɽ�����Ƥ��ʥХ��ν���
  (2006-10-12)
    ��ά����Ͽ�������ɲ�
    ��ά��ɽ���������ɲ�
    �������ѹ����˹��ɲäǤ��ʤ��Х��ν���
******************************/

/*
 * ����
 * �����ա�������BɼNo.��������ô���ԡ��������ơ�
 * ��2006/10/12��06-002��������watanabe-k�������ѹ����˻��������ѹ��Ǥ��ʤ��Х��ν���
 *   2006/10/12  06-023        watanabe-k  �������Ϥν��ɽ���������Ȱۤʤ�Х��ν���
 *   2006/10/12  06-030        watanabe-k  Ʊ�쾦�ʤ�ʣ�������줿���˷ٹ��å�������ɽ������ʤ��Х��ν��� 
 *   2006/10/13  06-031        watanabe-k  �������Ϣ������Ȳ񢪻����Խ����̤ز������ܤ�����硢�Ԥ��ɲåܥ���򥯥�å����Ƥ����ϹԤ������ʤ��Х��ν�����
 *   2006/10/13  06-027        watanabe-k  Ʊ��������Ԥ��ȥ��顼���򤤲��̤ˤʤ�Х��ν���
 *   2006/10/13  06-028        watanabe-k  Ʊ��������Ԥ��ȥ��顼���򤤲��̤ˤʤ�Х��ν���
 *   2006/10/18  06-018        watanabe-k  �����ʾ��ʥ����ɤ����Ϥ��Ƥ⥨�顼��å�������ɽ������ʤ��Х��ν��� 
 *   2006/10/18  06-037        watanabe-k  URL����SQL���顼��ɽ�������Х��ν��� 
 *   2006/10/19  06-043        watanabe-k  ���ʥ����ɤ��������Ϥ����ե����������ư���ʤ��ޤ�ȯ����ǧ���̤إܥ���򥯥�å�����ȡ���ǧ���̤����ܤ��Ƥ��ޤ���
 *   2006/10/19  06-044        watanabe-k  �����襳���ɤ��������Ϥ����ե����������ư���ʤ��ޤ�ȯ����ǧ���̤إܥ���򥯥�å�����ȡ���ǧ���̤����ܤ��Ƥ��ޤ���
 *   2006/11/11  06-098        watanabe-k�����������Ѥߤλ���ID����ꤹ��ȥ����ꥨ�顼
 *   2006/11/11  06-099        watanabe-k������̾������ɽ������Ƥ��ʤ��Х��ν���
 *   2006/11/13  06-110        watanabe-k�����åȿ���ʸ��������Ϥ����NaN��ɽ�������Х��ν���
 *   2006/11/13  06-112        watanabe-k  ������λ��Υǡ������ѹ��Ǥ��Ƥ��ޤ��Х��ν���
 *   2006/11/28  scl-0012      watanabe-k  ���������ǻ����줬�������ʤ��Х��ν���
 *   2006/11/29  scl-0028      watanabe-k  ȯ��ô���Ԥ������ɽ�������Х��ν���
 *   2006/12/01  scl-0041      watanabe-k  ��������ۤ˳��꤬�ޤޤ�Ƥ��ʤ��Х��ν���
 *   2006/12/01  scl-0057      watanabe-k  ȯ���Ĥ�������ܤξ�硢��̾�ѹ��Բľ��ʤ��ѹ��Ǥ��Ƥ��ޤ��Х��ν���
 *   2007/01/06  xx-xxx        kajioka-h   ľ�ĤΤȤ��Ρֻ�����ס�FC�����إܥ���򲫿��ˤ���
 *   2007/01/17                watanabe-k  ��Ͽ�����ܥ���򥯥�å������ȯ������Ķ���Ƥ���Ȥ������顼��ɽ������롣
 *   2007/02/21                watanabe-k  ���ɽ���Ҹˤ�����Ҹˤ��ѹ�
 *   2007/02/27                morita-d    ����̾������̾��ɽ������褦�˽���  
 *   2007/03/07                watanabe-k  �Ҹˤϵ����ҸˤΤ�ɽ������褦�˽���
 *   2007/03/09  ��˾9-1       kajioka-h   ���������ѹ�����Ȼ��������Ѥ��褦���ѹ�
 *   2007/03/13                watanabe-k  ���ɽ���ǥޥ����μ����ʬ�����ꤹ��褦�˽���
 *   2007/03/13                watanabe-k  ���ʥ����ɤν�ʣ���顼�����Ū��ɽ������褦�˽���  
 *   2007/05/18                watanabe-k  ľ����Υץ��������������ѹ� 
 *  2007-06-29                  fukuda      ���ͤ����ϥե�������礭������
 *  2007-07-12                  fukuda      �ֻ�ʧ���פ�ֻ������פ��ѹ�
 *  2007-07-13                 watanabe-k  �������ηٹ�ɽ�������Х��ν���
 *   2008-09-11                watanabe-k  ȯ���Ĥ������¾�������ɲ�
 *   2008-09-11                watanabe-k  ������������ɲ�
 *   2008-08-27                aoyama-n    �����ѹ��Ǿ��ʥ����ɤ����Ϥ����CTñ����ɽ��������Զ�罤�� 
 *   2008-09-08                aoyama-n    �Ͱ���ǽ�ɲ� 
 *   2008-09-16                aoyama-n    �����ʬ���Ͱ������ʤ�����Ͱ����ʤ��ֻ���ɽ�� 
 *   2009/09/28      �ʤ�      hashimoto-y �����ʬ�����Ͱ������ѻ�
 *   2009/10/13                hashimoto-y �߸˴����ե饰�򥷥�å��̾��ʾ���ơ��֥���ѹ�
 *   2009/12/21                aoyama-n    ��Ψ��TaxRate���饹�������
 */

$page_title = "��������";

//�Ķ�����ե�����
require_once("ENV_local.php");
require_once(INCLUDE_DIR."function_buy.inc");
require_once(INCLUDE_DIR."function_motocho.inc");

//HTML_QuickForm�����
$form =& new HTML_QuickForm("dateForm", "POST", $_SERVER["PHP_SELF"], NULL, "onSubmit=return confirm(true)");

//DB��³
$conn = Db_Connect();

// ���¥����å�
$auth       = Auth_Check($conn);
// �ܥ���Disabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;


/****************************/
//�����ѿ�����
/****************************/
$shop_id    = $_SESSION["client_id"];
//$shop_gid = $_SESSION["shop_gid"];
$rank_cd    = $_SESSION["rank_cd"];
$staff_id   = $_SESSION["staff_id"];
$group_kind = $_SESSION["group_kind"];

//�ܵҶ�ʬ���ü�ξ��
//�ϡ��ɥ����ǥ���
if($rank_cd == '0055'){
    $frein_flg = true;
}

/*****************************/
// ���������SESSION�˥��å�
/*****************************/
// GET��POST��̵�����
if ($_GET == null && $_POST == null){
    Set_Rtn_Page("buy");
}


//ȯ��ID�����
if($_GET["ord_id"] != NULL){
    Get_Id_Check3($_GET["ord_id"]);
    $get_order_id = $_GET["ord_id"];
    $order_get_flg = true;
}

//����ID�����
if($_GET["buy_id"] != NULL){
    Get_Id_Check3($_GET["buy_id"]);
    $get_buy_id = $_GET["buy_id"];
    $buy_get_flg = true;
}elseif($_POST["hdn_buy_id"] != NULL){
    $get_buy_id = $_POST["hdn_buy_id"];
    $buy_get_flg = true;
}

/****************************/
//�������
/****************************/

#2009-12-21 aoyama-n
//��Ψ���饹�����󥹥�������
$tax_rate_obj = new TaxRate($shop_id);

//ɽ���Կ�
if($_POST["max_row"] != NULL){
    $max_row = $_POST["max_row"];
}else{
    $max_row = 5;
}

//���ɽ�������ѹ�
$form_potision = "<body bgcolor=\"#D8D0C8\">";

//����Կ�
$del_history[] = NULL; 

//�����褬���ꤵ��Ƥ��뤫
if($_POST["hdn_client_id"] != NULL){
    $client_search_flg = true; 
    $client_id  = $_POST["hdn_client_id"];
    $head_flg   = $_POST["head_flg"];
    $coax       = $_POST["hdn_coax"];
    $tax_franct = $_POST["hdn_tax_franct"];
    $royalty    = $_POST["hdn_royalty"];
}

//�Ҹˤ����ꤵ��Ƥ��뤫
if($_POST["form_ware"] != NULL){
    $stock_search_flg = true;
}

//����̾�ѹ��ġ��ѹ��Բ�Ƚ��ե饰
$name_change = $_POST["hdn_name_change"];

//��ʬ�ξ����ǳۡ��������ƥ������
#2009-12-21 aoyama-n
#�������ƥ��Τ���Ф���褦���ѹ�
$sql  = "SELECT";
#$sql .= "   tax_rate_n,";
$sql .= "   royalty_rate";
$sql .= " FROM";
$sql .= "   t_client";
$sql .= " WHERE";
$sql .= "   client_id = $shop_id";
$sql .= ";"; 

$result = Db_Query($conn, $sql);
#$tax_rate = pg_fetch_result($result,0,0);
#$rate  = bcdiv($tax_rate,100,2);                //������Ψ
#$royalty_rate = pg_fetch_result($result ,0,1);  //�������ƥ�
$royalty_rate = pg_fetch_result($result ,0,0);  //�������ƥ�

//��ư���֤�ȯ���ֹ����
$sql  = "SELECT";
$sql .= "   MAX(buy_no)";
$sql .= " FROM";
$sql .= "   t_buy_h";
$sql .= " WHERE";
$sql .= "   shop_id = $shop_id";
$sql .= ";"; 

$result = Db_Query($conn, $sql);
$buy_no = pg_fetch_result($result, 0 ,0);
$buy_no = $buy_no +1;
$buy_no = str_pad($buy_no, 8, 0, STR_PAD_LEFT);

//�Ҹ�ID����
/*
$sql  = "SELECT";
$sql .= "   ware_id";
$sql .= " FROM";
$sql .= "   t_client";
$sql .= " WHERE";
$sql .= "   client_id = $shop_id";
$sql .= ";";

$result = Db_Query($conn, $sql);
$def_ware_id = @pg_fetch_result($result, 0,0);
*/
$def_ware_id = Get_ware_id($conn, Get_Branch_Id($conn));

if($def_ware_id != null){
    $stock_search_flg = true;
}

//����ͥ��å�
$def_data["form_ware"] = $def_ware_id;
$def_data["form_buy_no"] = $buy_no;
$def_data["form_buy_staff"] = $staff_id;
//$def_data["form_order_staff"] = $staff_id;
$def_data["form_trade"]  = '21';

// ������
$def_data["form_arrival_day"]["y"] = date("Y");
$def_data["form_arrival_day"]["m"] = date("m");
$def_data["form_arrival_day"]["d"] = date("d");

// ������
$def_data["form_buy_day"]["y"] = date("Y");
$def_data["form_buy_day"]["m"] = date("m");
$def_data["form_buy_day"]["d"] = date("d");

$form->setDefaults($def_data);

$freeze_flg = $_POST["freeze_flg"];
$order_freeze_flg = $_POST["order_freeze_flg"];

/****************************/
//�Կ��ɲ�
/****************************/
if($_POST["add_row_flg"]==true){

    //����Ԥˡ��ܣ�����
    $max_row = $_POST["max_row"]+5;

    //�Կ��ɲåե饰�򥯥ꥢ
    $add_row_data["add_row_flg"] = "";
    $form->setConstants($add_row_data);

}

/****************************/
//�Ժ������
/****************************/
if(isset($_POST["del_row"])){

    //����ꥹ�Ȥ����
    $del_row = $_POST["del_row"];

    //������������ˤ��롣
    $del_history = explode(",", $del_row);

    //��������Կ�
    $del_num     = count($del_history)-1;
}

/****************************/
//����ID��������
/****************************/
//if($buy_get_flg == true && $_POST["goods_search_row"] == NULL && $_POST["add_row_flg"] != true){
if($_GET[buy_id] != null 
    &&
    $_POST["add_row_flg"] != true
    &&
    $_POST["goods_search_row"] == NULL
    &&
    $_POST["stock_search_flg"] != true
    &&
    $_POST["sum_button_flg"] != true
    &&
    $_POST["del_row"] == null){
    //�����إå�
    $sql  = "SELECT";
    $sql .= "   t_buy_h.buy_id,";
    $sql .= "   t_buy_h.buy_no,";
    $sql .= "   t_order_h.ord_no,";
    $sql .= "   to_date(t_order_h.ord_time,'YYYY/mm/dd'),";
    $sql .= "   t_order_h.arrival_day,";
    $sql .= "   CASE renew_flg";
    $sql .= "       WHEN 'f' THEN (SELECT client_id FROM t_client WHERE t_client.client_id = t_buy_h.client_id) ";
    $sql .= "   END AS client_id,";
    $sql .= "   CASE renew_flg";
    $sql .= "       WHEN 'f' THEN (SELECT client_cd1 FROM t_client WHERE t_client.client_id = t_buy_h.client_id) ";
    $sql .= "   END AS client_cd1,";
    $sql .= "   CASE renew_flg";
//    $sql .= "       WHEN 'f' THEN (SELECT client_name FROM t_client WHERE t_client.client_id = t_buy_h.client_id) ";
    $sql .= "       WHEN 'f' THEN (SELECT client_cname FROM t_client WHERE t_client.client_id = t_buy_h.client_id) ";
    $sql .= "   END AS client_name,";
    $sql .= "   t_buy_h.direct_id,";
    $sql .= "   t_buy_h.ware_id,";
    $sql .= "   t_buy_h.trade_id,";
    $sql .= "   t_buy_h.c_staff_id,";
    $sql .= "   t_buy_h.note,";
    $sql .= "   t_buy_h.renew_flg,";
    $sql .= "   t_buy_h.ord_id,";
    $sql .= "   t_buy_h.buy_day,";
    $sql .= "   t_buy_h.arrival_day,";
    $sql .= "   t_buy_h.oc_staff_id,";
    $sql .= "   t_buy_h.enter_day AS buy_enter_day,";
    $sql .= "   t_order_h.enter_day AS ord_enter_day,";
    //aoyama-n 2009-08-27
    #$sql .= "   t_order_h.change_day ";
    $sql .= "   t_order_h.change_day, ";
    $sql .= "   t_client.head_flg ";

    $sql .= " FROM";
    $sql .= "   t_buy_h LEFT JOIN t_order_h ON t_buy_h.ord_id = t_order_h.ord_id ";
    $sql .= "   INNER JOIN t_client ON t_buy_h.client_id = t_client.client_id";
    $sql .= " WHERE";
    $sql .= "   t_buy_h.shop_id = $shop_id";
    $sql .= "   AND";
    $sql .= "   t_buy_h.buy_id = $get_buy_id";
//���ID�ȾȲ�ID��null
    $sql .= "   AND";
    $sql .= "   t_buy_h.intro_sale_id IS NULL";
    $sql .= "   AND";
    $sql .= "   t_buy_h.act_sale_id IS NULL";
    $sql .= "   AND";
    $sql .= "   t_buy_h.renew_flg = 'f'";
    $sql .= "   AND";
    $sql .= "   t_buy_h.buy_div = '1'";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    Get_Id_Check($result);
    $buy_h_data = pg_fetch_array($result);

    $order_id = $buy_h_data[14];

    //�����ǡ���
    $sql  = "SELECT ";
    $sql .= "   t_buy_d.ord_d_id,";
    $sql .= "   t_goods.goods_id,";
    $sql .= "   t_buy_d.goods_cd,";
    $sql .= "   t_buy_d.goods_name,";
    $sql .= "   t_goods.tax_div,";
    #2009-10-13 hashimoto-y
    #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num,";
    $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num,";

    $sql .= "   t_order_d.num AS order_num,";
    $sql .= "   t_buy_d.num AS buy_num,";
    $sql .= "   t_buy_d.buy_price,";
    $sql .= "   t_buy_d.buy_amount,";
    $sql .= "   CASE WHEN t_order_d.num IS NOT NULL THEN t_order_d.num - COALESCE(t_buy_d.num,0) END AS on_order_num,";
    $sql .= "   t_goods.name_change,";
    #2009-10-13 hashimoto-y
    #$sql .= "   t_goods.stock_manage,";
    $sql .= "   t_goods_info.stock_manage,";

    $sql .= "   t_goods.in_num,";
    //aoyama-n 2009-09-08
    #$sql .= "   t_goods.royalty";
    $sql .= "   t_goods.royalty,";
    $sql .= "   t_goods.discount_flg";
    $sql .= " FROM";
    $sql .= "   t_buy_h INNER JOIN t_buy_d ON t_buy_h.buy_id = t_buy_d.buy_id";
    $sql .= "   LEFT JOIN ";
    $sql .= "   (SELECT";
    $sql .= "       goods_id,";
    $sql .= "       SUM(stock_num)AS stock_num";
    $sql .= "   FROM";
    $sql .= "       t_stock";
    $sql .= "   WHERE";
    $sql .= "       shop_id = $shop_id";
    $sql .= "       AND";
    $sql .= "       ware_id = $buy_h_data[9]";
    $sql .= "       GROUP BY t_stock.goods_id";
    $sql .= "   )AS t_stock";
    $sql .= "   ON t_buy_d.goods_id = t_stock.goods_id";
    $sql .= "   INNER JOIN t_goods ON t_buy_d.goods_id = t_goods.goods_id";
    $sql .= "   LEFT JOIN t_order_d ON t_buy_d.ord_d_id = t_order_d.ord_d_id";
    #2009-10-13 hashimoto-y
    $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

    $sql .= " WHERE";
    $sql .= "   t_buy_d.buy_id = $get_buy_id";
    $sql .= "   AND ";
    $sql .= "   t_buy_h.shop_id = $shop_id";
    #2009-10-13 hashimoto-y
    $sql .= "   AND ";
    $sql .= "   t_goods_info.shop_id = $shop_id ";

    $sql .= " ORDER BY t_buy_d.line ";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $buy_d_data_num = pg_num_rows($result);
	$max_row = $buy_d_data_num;                 //ɽ���Կ�

    for($i = 0; $i < $buy_d_data_num; $i++){
        $buy_d_data[] = pg_fetch_array($result);
    }

    //ȯ��������ʬ��
    $order_date = explode("-",$buy_h_data[3]);

    //����ͽ������ʬ��
    $arrival_hope_date = explode("-",$buy_h_data[4]);

    //��������ʬ��
    $buy_date   = explode("-",$buy_h_data[15]);

    //��������ʬ��
    $arrival_date   = explode("-", $buy_h_data[16]);

    //�ե�������ͤ򥻥å�
    $set_buy_data["hdn_buy_id"]                     = $buy_h_data[0];           //����ID
    $set_buy_data["form_buy_no"]                    = $buy_h_data[1];           //�����ֹ�
    $set_buy_data["hdn_order_id"]                   = $buy_h_data[14];          //ȯ��ID
    $set_buy_data["form_order_no"]                  = $buy_h_data[2];           //ȯ���ֹ�
    $set_buy_data["form_order_day"]["y"]            = $order_date[0];           //ȯ������ǯ��
    $set_buy_data["form_order_day"]["m"]            = $order_date[1];           //ȯ�����ʷ��
    $set_buy_data["form_order_day"]["d"]            = $order_date[2];           //ȯ����������
    $set_buy_data["form_arrival_hope_day"]["y"]     = $arrival_hope_date[0];    //����ͽ������ǯ��
    $set_buy_data["form_arrival_hope_day"]["m"]     = $arrival_hope_date[1];    //����ͽ�����ʷ��
    $set_buy_data["form_arrival_hope_day"]["d"]     = $arrival_hope_date[2];    //����ͽ����������
    $set_buy_data["hdn_client_id"]                  = $buy_h_data[5];           //������ID
    $set_buy_data["form_client"]["cd"]              = $buy_h_data[6];           //������CD
    $set_buy_data["form_client"]["name"]            = $buy_h_data[7];           //������̾
    $set_buy_data["form_direct"]                    = $buy_h_data[8];           //ľ����
    $set_buy_data["form_ware"]                      = $buy_h_data[9];           //�Ҹ�
    $set_buy_data["form_trade"]                     = $buy_h_data[10];          //�����ʬ
    $set_buy_data["form_buy_staff"]                 = $buy_h_data[11];          //����ô����
    $set_buy_data["form_note"]                      = $buy_h_data[12];          //����
    $set_buy_data["form_arrival_day"]["y"]          = $arrival_date[0];         //��������ǯ��
    $set_buy_data["form_arrival_day"]["m"]          = $arrival_date[1];         //�������ʷ��
    $set_buy_data["form_arrival_day"]["d"]          = $arrival_date[2];         //������������
    $set_buy_data["form_buy_day"]["y"]              = $buy_date[0];             //��������ǯ��
    $set_buy_data["form_buy_day"]["m"]              = $buy_date[1];             //�������ʷ��
    $set_buy_data["form_buy_day"]["d"]              = $buy_date[2];             //������������
    $set_buy_data["form_order_staff"]               = $buy_h_data[17];          //ȯ��ô����
    $set_buy_data["hdn_buy_enter_day"]              = $buy_h_data[buy_enter_day];   //������Ͽ��
    $set_buy_data["hdn_ord_enter_day"]              = $buy_h_data[ord_enter_day];   //ȯ����Ͽ��
    $set_buy_data["hdn_ord_change_day"]             = $buy_h_data[change_day];   //ȯ���ѹ���
    //aoyama-n 2009-08-27
    $set_buy_data["head_flg"]                       = $buy_h_data[head_flg];     //�����ե饰	

    $client_id  = $buy_h_data[5];

    //������δݤ��ʬ��ü����ʬ�����
    $sql  = "SELECT";
    $sql .= "   coax,";
    $sql .= "   tax_franct";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    $sql .= "   client_id = $client_id";
    $sql .= ";";

    $result     = Db_Query($conn, $sql);
    $coax       = pg_fetch_result($result, 0, "coax");
    $tax_franct = pg_fetch_result($result, 0, "tax_franct");

    //hidden�˶�ۤޤ�ᡢü����ʬ�򥻥å�
    $set_buy_data["hdn_coax"]       = $coax;
    $set_buy_data["hdn_tax_franct"] = $tax_franct;

    for($i = 0; $i < $max_row; $i++){

        if($buy_d_data[$i][1] != NULL){
            //����ñ����ʬ��
            $price_data = explode('.',$buy_d_data[$i][8]);

            $buy_amount[]   = $buy_d_data[$i][9];
            $tax_div[]      = $buy_d_data[$i][4];
            $royalty[]      = $buy_d_data[$i]["royalty"];

            $set_buy_data["hdn_order_d_id"][$i]         = $buy_d_data[$i][0];                                       //ȯ���ǡ���ID
            $set_buy_data["hdn_goods_id"][$i]           = $buy_d_data[$i][1];                                       //����ID
            $set_buy_data["form_goods_cd"][$i]          = $buy_d_data[$i][2];                                       //����CD
            $set_buy_data["form_goods_name"][$i]        = $buy_d_data[$i][3];                                       //����̾
            $set_buy_data["hdn_tax_div"][$i]            = $buy_d_data[$i][4];                                       //���Ƕ�ʬ��hidden�ѡ�
            $set_buy_data["form_stock_num"][$i]         = ($buy_d_data[$i][12] == '1')? $buy_d_data[$i][5] : '-';   //�߸˿�
            $set_buy_data["form_order_num"][$i]         = ($buy_d_data[$i][0] != null)? $buy_d_data[$i][6] : '-';   //ȯ����
            $set_buy_data["form_buy_num"][$i]           = $buy_d_data[$i][7];                                       //������
            $set_buy_data["form_buy_price"][$i]["i"]    = $price_data[0];                                           //����ñ������������
            $set_buy_data["form_buy_price"][$i]["d"]    = ($price_data[1] != NULL)? $price_data[1] : "00";          //����ñ���ʾ�������
            $set_buy_data["form_buy_amount"][$i]        = number_format($buy_d_data[$i][9]);                        //������ۡ���ȴ����
            $set_buy_data["form_rorder_num"][$i]        = ($buy_d_data[$i][0] != null)? $buy_d_data[$i][10] : '-';  //ȯ����
            $set_buy_data["hdn_name_change"][$i]        = $buy_d_data[$i][11];                                      //��̾�ѹ�
            $set_buy_data["hdn_stock_manage"][$i]       = $buy_d_data[$i][12];                                      //�߸˴���
            $set_buy_data["form_in_num"][$i]            = $buy_d_data[$i][13];                                      //����
            $set_buy_data["form_rbuy_num"][$i]          = ($buy_d_data[$i][0] != null)? $buy_d_data[$i][6] - $buy_d_data[$i][10] : '-'; //�����ѿ�

            //��������
            if($buy_d_data[$i][7]%$buy_d_data[$i][13] == 0 && $buy_d_data[$i][13]!=null && $buy_d_data[$i][13] != 0){
                $set_buy_data["form_order_in_num"][$i]  = $buy_d_data[$i][7]/$buy_d_data[$i][13];
            }

            $set_buy_data["hdn_royalty"][$i]            = $buy_d_data[$i]["royalty"];                               //�������ƥ�

            //aoyama-n 2009-09-08
            $set_buy_data["hdn_discount_flg"][$i]       = $buy_d_data[$i][discount_flg];                            //�Ͱ��ե饰

            $name_change[$i] = $buy_d_data[$i][11];                                                                 //��̾�ѹ�
        }
    }

    $client_search_flg = true;
    $stock_search_flg  = true;

    $renew_flg = $buy_h_data[13];   //���������ե饰

    //���������ե饰��桡AND��ȯ���ֹ��NULL�ξ��
    if($renew_flg == 'f' && $order_id == NULL){
//        $order_freeze_flg = true;
//        $set_buy_data["order_freeze_flg"] = $order_freeze_flg;
    //���������ե饰��桡ȯ���ֹ�<>NULL�ξ��
    }elseif($renew_flg == 'f' && $order_id != NULL){
        $freeze_flg = true;
        $order_freeze_flg = true;
        $set_buy_data["order_freeze_flg"] = $order_freeze_flg;
        $set_buy_data["freeze_flg"] = $freeze_flg;
    }

    #2009-12-21 aoyama-n
    $tax_rate_obj->setTaxRateDay($set_buy_data["form_buy_day"]["y"]."-".$set_buy_data["form_buy_day"]["m"]."-".$set_buy_data["form_buy_day"]["d"]);
    $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

    $total_amount_data = Total_Amount($buy_amount, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $conn);

    $set_buy_data["form_buy_money"]     = number_format($total_amount_data[0]);
    $set_buy_data["form_tax_money"]     = number_format($total_amount_data[1]);
    $set_buy_data["form_total_money"]   = number_format($total_amount_data[2]);

    $form->setDefaults($set_buy_data);

/****************************/
//ɽ���ܥ��󲡲�������ȯ��ID��������
/****************************/
//GET��ȯ��ID������⤷����ɽ���ܥ��󤬲��������
//AND
//�����踡���ե饰��ture�Ǥʤ�
//AND
//�Ҹ˸����ե饰��true�Ǥʤ�
//AND
//����촰λ�ե饰��������Ƥ��ʤ�
//AND
//��ץܥ��󤬲�������Ƥ��ʤ�
}elseif(($_POST["show_button_flg"] == true || $order_get_flg == true) 
        && 
        $_POST["client_search_flg"] != true 
        && 
        $_POST["stock_search_flg"] != true 
        && 
        $_POST["comp_button"] != "������λ"
        &&
        $_POST["sum_button_flg"] != true){

    //ɽ���ܥ��󲡲���
    if($_POST["show_button_flg"] == true){
        $get_order_id = $_POST["form_order_no"];
    }

    if($get_order_id != null){
        $sql  = " SELECT \n";
        $sql .= "   t_order_h.ord_id, \n";
        $sql .= "   t_order_h.ord_no, \n";
        $sql .= "   to_date(t_order_h.ord_time,'YYYY/mm/dd'), \n";
        $sql .= "   arrival_day, \n";
        $sql .= "   t_client.client_id, \n";
        $sql .= "   t_client.client_cd1, \n";
//        $sql .= "   t_client.client_name,";
        $sql .= "   t_client.client_cname, \n";
        $sql .= "   t_order_h.direct_id, \n";
        $sql .= "   t_order_h.ware_id, \n";
        $sql .= "   t_order_h.trade_id, \n";
        $sql .= "   t_order_h.c_staff_id, \n";
        $sql .= "   t_order_h.ps_stat, \n";
        $sql .= "   t_client.head_flg, \n";
        $sql .= "   t_order_h.ord_staff_id, \n";
        $sql .= "   t_order_h.enter_day, \n";
        $sql .= "   t_order_h.change_day, \n";
        $sql .= "   t_client.tax_franct, \n";
        $sql .= "   t_client.coax \n";
        $sql .= " FROM \n";
        $sql .= "   t_order_h INNER JOIN t_client ON t_order_h.client_id = t_client.client_id \n";
        $sql .= " WHERE \n";
        $sql .= "   (t_order_h.ord_stat <> 3 \n";
        $sql .= "   OR \n";
        $sql .= "   t_order_h.ord_stat IS NULL) \n";
        $sql .= "   AND \n";
        $sql .= "   t_order_h.ord_id = $get_order_id \n";
        $sql .= "   AND \n";
        $sql .= "   t_order_h.shop_id = $shop_id \n";
/*
        $sql .= "   AND \n";
        $sql .= "   t_order_h.ps_stat IN (1,2) \n";
*/
        $sql .= ";";

        $result         = Db_Query($conn, $sql);

#print_array($sql);
        //ȯ��ID���������GET����ID�Υ����å�
        if($order_get_flg == true){
            Get_Id_Check($result);
        }

        $order_h_num    = pg_num_rows($result);

        $order_h_data   = pg_fetch_array($result);

        #$ps_stat = $order_h_data[10];
        $ps_stat = $order_h_data[11];
    }else{
        header("Location: ./2-3-201.php");
        exit;
    }

    #if($ps_stat == '3'){

	//ȯ��������������λ���������������Ѥߤξ��	
    if($ps_stat == '3' || $ps_stat == '4'){

        $finish = "���Ϥ��줿ȯ���ֹ�ξ��ʤ����ƻ����ѤߤǤ���";

		/*************************/
		//�ʲ��ե�������������
		/*************************/
		$max_row = 5;								//ɽ���Կ�
        $freeze_flg                     = false;	//�ե�����disable�ե饰
        $set_order_data["freeze_flg"]   = "";		//�ե�����disable�ե饰
		$client_id						= "";		//������ID
        $client_search_flg              = false;	//�����踡���ե饰
        $stock_search_flg               = false;	//�Ҹ˸����ե饰

		//�����襹�ơ�����ɽ���򥯥ꥢ
		$client_state_print				= "";

		//�إå����󥯥ꥢ
        $set_order_data["hdn_order_id"]                     = "";           //ȯ��ID
        $set_order_data["form_order_no"]                    = "";           //ȯ���ֹ�
        $set_order_data["form_order_day"]["y"]              = "";           //ȯ������ǯ��
        $set_order_data["form_order_day"]["m"]              = "";           //ȯ�����ʷ��
        $set_order_data["form_order_day"]["d"]              = "";           //ȯ����������
        $set_order_data["form_buy_day"]["y"]                = date('Y');    //ȯ������ǯ��
        $set_order_data["form_buy_day"]["m"]                = date('m');    //ȯ�����ʷ��
        $set_order_data["form_buy_day"]["d"]                = date('d');    //ȯ����������
        $set_order_data["form_arrival_hope_day"]["y"]       = "";           //����ͽ������ǯ��
        $set_order_data["form_arrival_hope_day"]["m"]       = "";           //����ͽ�����ʷ��
        $set_order_data["form_arrival_hope_day"]["d"]       = "";           //����ͽ����������
        $set_order_data["form_arrival_day"]["y"]            = date('Y');    //����ͽ������ǯ��
        $set_order_data["form_arrival_day"]["m"]            = date('m');    //����ͽ�����ʷ��
        $set_order_data["form_arrival_day"]["d"]            = date('d');    //����ͽ����������
        $set_order_data["hdn_client_id"]                    = "";         	//������ID
        $set_order_data["form_client"]["cd"]                = "";         	//������CD
        $set_order_data["form_client"]["name"]              = "";         	//������̾
        $set_order_data["form_direct"]                      = "";         	//ľ����
        $set_order_data["form_ware"]                        = "";         	//�Ҹ�
        $set_order_data["form_trade"]                       = '21';       	//�����ʬ
        $set_order_data["form_buy_staff"]                   = $staff_id;  	//����ô����
        $set_order_data["form_order_staff"]                 = $staff_id;  	//ȯ��ô����
        $set_order_data["form_buy_money"]                   = "";			//�������
        $set_order_data["form_tax_money"]                   = "";			//�����ǳ�
        $set_order_data["form_total_money"]                 = "";			//�ǹ�������
        $set_order_data["hdn_ord_enter_day"]            	= "";			//ȯ����Ͽ��
        $set_order_data["hdn_ord_change_day"]           	= "";			//ȯ�����ѹ���
        $set_order_data["hdn_coax"]                     	= "";			//
        $set_order_data["hdn_tax_franct"]               	= "";			//
        
		//�ǡ������󥯥ꥢ
        for($i = 0; $i < $max_row; $i++){
            $set_order_data["hdn_order_d_id"][$i]           = "";     		//ȯ���ǡ���ID
            $set_order_data["hdn_goods_id"][$i]             = "";     		//����ID
            $set_order_data["form_goods_cd"][$i]            = "";     		//���ʥ�����
            $set_order_data["form_goods_name"][$i]          = "";     		//����̾
            $set_order_data["form_stock_num"][$i]           = "";     		//�߸˿�
            $set_order_data["form_order_num"][$i]           = "";     		//ȯ����
            $set_order_data["form_rbuy_num"][$i]            = "";     		//�����ѿ�
            $set_order_data["form_buy_price"][$i]["i"]      = "";     		//����ñ������������
            $set_order_data["form_buy_price"][$i]["d"]      = "";     		//����ñ���ʾ�������
            $set_order_data["hdn_tax_div"][$i]              = "";     		//���Ƕ�ʬ
            $set_order_data["form_buy_amount"][$i]          = "";     		//������ۡ���ȴ����
            $set_order_data["hdn_name_change"][$i]          = "";     		//��̾�ѹ�
            $set_order_data["form_rorder_num"][$i]          = "";     		//ȯ����
            $set_order_data["form_buy_num"][$i]             = "";           //������
            $set_order_data["hdn_royalty"][$i]              = "";       	//��������ƥ�
            $set_order_data["form_in_num"][$i]              = "";			//���å������
            $set_order_data["form_order_in_num"][$i]        = "";	  		//���åȿ�
            //aoyama-n 2009-09-08
            $set_order_data["hdn_discount_flg"][$i]         = "";	  		//�Ͱ��ե饰

            $name_change[$i]                            	= "";			//��̾�ѹ� 
        }

    //ȯ���إå�����Ͽ�������
    #}elseif($order_h_num > 0){

	
    }else{
        $sql  = "SELECT ";
        $sql .= "   t_order_d.ord_d_id,";                               //ȯ���ǡ���ID
        $sql .= "   t_goods.goods_id,";                                 //����ID
        $sql .= "   t_goods.goods_cd,";                                 //���ʥ�����
        $sql .= "   t_order_d.goods_name,";                             //����̾
        #2009-10-13 hashimoto-y
        #$sql .= "   CASE t_goods.stock_manage";                         //�߸˴���
        $sql .= "   CASE t_goods_info.stock_manage";                         //�߸˴���

        $sql .= "        WHEN 1 THEN COALESCE(t_stock.stock_num,0)";
        $sql .= "   END AS stock_num,";
        $sql .= "   t_order_d.num AS order_num,";                       //ȯ����
        $sql .= "   COALESCE(t_buy.buy_num,0) AS buy_num,";             //������
        $sql .= "   t_order_d.buy_price,";                              //����ñ��
        $sql .= "   t_order_d.tax_div,";                                //���Ƕ�ʬ
        $sql .= "   t_order_d.buy_amount,";                             //�������
        $sql .= "   t_goods.name_change,";                              //��̾�ѹ�
        $sql .= "   t_goods.in_num,";
        $sql .= "   t_goods.royalty,";
        #2009-10-13 hashimoto-y
        //aoyama-n 2009-09-08
        #$sql .= "   t_goods.stock_manage";
        #$sql .= "   t_goods.stock_manage,";
        $sql .= "   t_goods_info.stock_manage,";

        $sql .= "   t_goods.discount_flg";
        $sql .= " FROM";
        $sql .= "   t_order_h INNER JOIN t_order_d ON t_order_h.ord_id = t_order_d.ord_id";
        $sql .= "   LEFT JOIN"; 
        $sql .= "   (SELECT";
        $sql .= "   goods_id,";
        $sql .= "   SUM(stock_num)AS stock_num";
        $sql .= "   FROM";
        $sql .= "        t_stock";
        $sql .= "   WHERE";
        $sql .= "        shop_id = $shop_id";
        $sql .= "        AND";
        $sql .= "        ware_id = $order_h_data[8]";
        $sql .= "   GROUP BY t_stock.goods_id";
        $sql .= "   )AS t_stock";
        $sql .= "   ON t_order_d.goods_id = t_stock.goods_id";
        $sql .= "   LEFT JOIN ";
        $sql .= "   (SELECT ";
        $sql .= "       ord_d_id,";
        $sql .= "       SUM(num) AS buy_num";
        $sql .= "   FROM ";
        $sql .= "       t_buy_d";
        $sql .= "   GROUP BY ord_d_id";
        $sql .= "   )t_buy";
        $sql .= "   ON t_order_d.ord_d_id = t_buy.ord_d_id";
        $sql .= "   INNER JOIN t_goods ON t_order_d.goods_id = t_goods.goods_id";
        #2009-10-13 hashimoto-y
        $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

        $sql .= " WHERE";
        $sql .= "   t_order_h.ord_id = $order_h_data[0]";
        $sql .= "   AND";   
        $sql .= "   t_order_d.rest_flg = 't'";
        $sql .= "   AND";
        $sql .= "   t_order_h.shop_id = $shop_id";
        #2009-10-13 hashimoto-y
        $sql .= "   AND ";
        $sql .= "   t_goods_info.shop_id = $shop_id ";

        $sql .= " ORDER BY t_order_d.line";

        $sql .= ";";

        $result = Db_Query($conn, $sql);
        $order_d_num = pg_num_rows($result);

        for($i = 0; $i < $order_d_num; $i++){
            $order_d_data[] = pg_fetch_array($result, $i);
        }

        //ȯ��������ʬ��
        $order_date = explode("-",$order_h_data[2]);

        //����ͽ������ʬ��
        $arrival_date = explode("-", $order_h_data[3]);

        //ȯ���ǡ�����ե�����˥��å�
        $set_order_data["hdn_order_id"]                 = $order_h_data[0];                                     //ȯ��ID
        $set_order_data["form_order_no"]                = $order_h_data[0];                                     //ȯ���ֹ�
        $set_order_data["form_order_day"]["y"]          = $order_date[0];                                       //ȯ������ǯ��
        $set_order_data["form_order_day"]["m"]          = $order_date[1];                                       //ȯ�����ʷ��
        $set_order_data["form_order_day"]["d"]          = $order_date[2];                                       //ȯ����������
        $set_order_data["form_arrival_hope_day"]["y"]   = ($arrival_date[0] == null)? "" : $arrival_date[0];    //����ͽ������ǯ��
        $set_order_data["form_arrival_hope_day"]["m"]   = ($arrival_date[1] == null)? "" : $arrival_date[1];    //����ͽ�����ʷ��
        $set_order_data["form_arrival_hope_day"]["d"]   = ($arrival_date[2] == null)? "" : $arrival_date[2];    //����ͽ����������
        $set_order_data["hdn_client_id"]                = $order_h_data[4];                                     //������ID
        $set_order_data["form_client"]["cd"]            = $order_h_data[5];                                     //������CD
        $set_order_data["form_client"]["name"]          = $order_h_data[6];                                     //������̾
        $set_order_data["form_direct"]                  = $order_h_data[7];                                     //ľ����
        $set_order_data["form_ware"]                    = $order_h_data[8];                                     //�Ҹ�
        $set_order_data["form_trade"]                   = $order_h_data[9];                                     //�����ʬ
        //$set_order_data["form_buy_staff"]               = $order_h_data[13];                                    //����ô����
        $set_order_data["form_buy_staff"]               = $_SESSION[staff_id];                                    //����ô����
        $set_order_data["form_order_staff"]             = $order_h_data[10];                                    //ȯ��ô����
        $set_order_data["form_buy_day"]["y"]            = date('Y');                                            //��������ǯ��
        $set_order_data["form_buy_day"]["m"]            = date('m');                                            //�������ʷ��
        $set_order_data["form_buy_day"]["d"]            = date('d');                                            //������������
        $set_order_data["form_arrival_day"]["y"]        = date('Y');                                            //��������ǯ��
        $set_order_data["form_arrival_day"]["m"]        = date('m');                                            //�������ʷ��
        $set_order_data["form_arrival_day"]["d"]        = date('d');                                            //������������
        $set_order_data["hdn_ord_enter_day"]            = $order_h_data["enter_day"];
        $set_order_data["hdn_ord_change_day"]           = $order_h_data["change_day"];
        $set_order_data["hdn_coax"]                     = $order_h_data["coax"];
        $set_order_data["hdn_tax_franct"]               = $order_h_data["tax_franct"];

        $tax_franct = $order_h_data["tax_franct"];      //ü����ʬ
        $coax       = $order_h_data["coax"];            //��۴ݤ��ʬ

        $head_flg = $order_h_data[11];                  //�����ե饰

        $client_id = $order_h_data[4];                  //������ID

        for($i = 0; $i < $order_d_num; $i++){

            //���Ƕ�ʬ
            $tax_div[]      = $order_d_data[$i][8];

            //�������
            $buy_amount[]   = $order_d_data[$i][9];

            //�������ƥ�
            $royalty[]      = $order_d_data[$i]["royalty"];


            //����ñ��
            $price = explode('.',$order_d_data[$i][7]);

            //ȯ���Ŀ��򻻽�
            $rorder_num  = $order_d_data[$i][5] - $order_d_data[$i][6];

            $set_order_data["hdn_order_d_id"][$i]       = $order_d_data[$i][0];                             //ȯ���ǡ���ID
            $set_order_data["hdn_goods_id"][$i]         = $order_d_data[$i][1];                             //����ID
            $set_order_data["form_goods_cd"][$i]        = $order_d_data[$i][2];                             //���ʥ�����
            $set_order_data["form_goods_name"][$i]      = $order_d_data[$i][3];                             //����̾
            $set_order_data["form_stock_num"][$i]       = ($order_d_data[$i]["stock_manage"] == '1')? $order_d_data[$i][4] : "-";   //�߸˿�
            $set_order_data["hdn_stock_manage"][$i]     = $order_d_data[$i]["stock_manage"];                //�߸˴��� 
            $set_order_data["form_order_num"][$i]       = $order_d_data[$i][5];                             //ȯ����
            $set_order_data["form_rbuy_num"][$i]        = $order_d_data[$i][6];                             //�����ѿ�
            $set_order_data["form_buy_price"][$i]["i"]  = $price[0];                                        //����ñ������������
            $set_order_data["form_buy_price"][$i]["d"]  = $price[1];                                        //����ñ���ʾ�������
            $set_order_data["hdn_tax_div"][$i]          = $order_d_data[$i][8];                             //���Ƕ�ʬ
            //$set_order_data["form_buy_amount"][$i]      = number_format($order_d_data[$i][9]);              //������ۡ���ȴ����
            $buy_amount[$i]                             = number_format(Coax_Col($coax, bcmul($order_d_data[$i][7], $rorder_num,2)));              //������ۡ���ȴ����
            $set_order_data["form_buy_amount"][$i]      = $buy_amount[$i];              //������ۡ���ȴ����
            $set_order_data["hdn_name_change"][$i]      = $order_d_data[$i][10];                            //��̾�ѹ�
            $set_order_data["form_in_num"][$i]          = $order_d_data[$i][11];                            //����
            $set_order_data["form_rorder_num"][$i]      = $rorder_num;                                      //ȯ����
            $set_order_data["form_buy_num"][$i]         = $rorder_num;                                      //������
            $set_order_data["hdn_royalty"][$i]          = $order_d_data[$i]["royalty"];                     //�������ƥ�

            //aoyama-n 2009-09-08
            $set_order_data["hdn_discount_flg"][$i]     = $order_d_data[$i]["discount_flg"];                     //�������ƥ�

            $name_change[$i]                            = $order_d_data[$i][10];                            //��̾�ѹ� 

            //���å������
            if($order_d_data[$i][5]%$order_d_data[$i][11] == 0 && $order_d_data[$i][11]!=null){
                $set_order_data["form_order_in_num"][$i]  = @($order_d_data[$i][5]/$order_d_data[$i][11]);
            }
        }

        //����Կ�����
        $max_row = $order_d_num;

        $client_search_flg  = true;
        $stock_search_flg   = true;

        $freeze_flg         = true;
        $set_order_data["freeze_flg"] = $freeze_flg;

        $order_freeze_flg = true;

        #2009-12-21 aoyama-n
        $tax_rate_obj->setTaxRateDay($set_order_data["form_buy_day"]["y"]."-".$set_order_data["form_buy_day"]["m"]."-".$set_order_data["form_buy_day"]["d"]);
        $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

        //������ۡ���ȴ�ˡ������ǹ�ס�������ۡ��ǹ��ˤ򻻽�
        $total_amount_data = Total_Amount($buy_amount, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $conn);

        $set_order_data["form_buy_money"]   = number_format($total_amount_data[0]);
        $set_order_data["form_tax_money"]   = number_format($total_amount_data[1]);
        $set_order_data["form_total_money"] = number_format($total_amount_data[2]);

/*
    }else{

        header("Location: ./2-3-201.php");
        $freeze_flg                     = false;
        $set_order_data["freeze_flg"]   = "";
        $client_search_flg              = false;
        $stock_search_flg               = false;

        $set_order_data["hdn_order_id"]                     = "";           //ȯ��ID
        $set_order_data["form_order_no"]                    = "";           //ȯ���ֹ�
        $set_order_data["form_order_day"]["y"]              = "";           //ȯ������ǯ��
        $set_order_data["form_order_day"]["m"]              = "";           //ȯ�����ʷ��
        $set_order_data["form_order_day"]["d"]              = "";           //ȯ����������
        $set_order_data["form_buy_day"]["y"]                = date('Y');    //ȯ������ǯ��
        $set_order_data["form_buy_day"]["m"]                = date('m');    //ȯ�����ʷ��
        $set_order_data["form_buy_day"]["d"]                = date('d');    //ȯ����������
        $set_order_data["form_arrival_hope_day"]["y"]       = "";           //����ͽ������ǯ��
        $set_order_data["form_arrival_hope_day"]["m"]       = "";           //����ͽ�����ʷ��
        $set_order_data["form_arrival_hope_day"]["d"]       = "";           //����ͽ����������
        $set_order_data["form_arrival_day"]["y"]            = date('Y');    //����ͽ������ǯ��
        $set_order_data["form_arrival_day"]["m"]            = date('m');    //����ͽ�����ʷ��
        $set_order_data["form_arrival_day"]["d"]            = date('d');    //����ͽ����������
        $set_order_data["hdn_client_id"]                    = "";         //������ID
        $set_order_data["form_client"]["cd"]                = "";         //������CD
        $set_order_data["form_client"]["name"]              = "";         //������̾
        $set_order_data["form_direct"]                      = "";         //ľ����
        $set_order_data["form_ware"]                        = "";         //�Ҹ�
        $set_order_data["form_trade"]                       = '21';       //�����ʬ
        $set_order_data["form_buy_staff"]                   = $staff_id;  //����ô����
        $set_order_data["form_order_staff"]                 = $staff_id;  //ȯ��ô����
        $set_order_data["form_buy_money"]                   = "";
        $set_order_data["form_tax_money"]                   = "";
        $set_order_data["form_total_money"]                 = "";
        
        for($i = 0; $i < $max_row; $i++){
            $set_order_data["hdn_order_d_id"][$i]           = "";     //ȯ���ǡ���ID
            $set_order_data["hdn_goods_id"][$i]             = "";     //����ID
            $set_order_data["form_goods_cd"][$i]            = "";     //���ʥ�����
            $set_order_data["form_goods_name"][$i]          = "";     //����̾
            $set_order_data["form_stock_num"][$i]           = "";     //�߸˿�
            $set_order_data["form_order_num"][$i]           = "";     //ȯ����
            $set_order_data["form_rbuy_num"][$i]            = "";     //�����ѿ�
            $set_order_data["form_buy_price"][$i]["i"]      = "";     //����ñ������������
            $set_order_data["form_buy_price"][$i]["d"]      = "";     //����ñ���ʾ�������
            $set_order_data["hdn_tax_div"][$i]              = "";     //���Ƕ�ʬ
            $set_order_data["form_buy_amount"][$i]          = "";     //������ۡ���ȴ����
            $set_order_data["hdn_name_change"][$i]          = "";     //��̾�ѹ�
            $set_order_data["form_rorder_num"][$i]          = "";              //ȯ����
            $set_order_data["form_buy_num"][$i]             = "";              //������
            $set_order_data["hdn_royalty"][$i]              = "";       //��������ƥ�
            $set_order_data["form_in_num"][$i]              = "";
            $max_row = 5;
        }
*/
    }

    $set_order_data["show_button_flg"] = ""; 
    $form->setConstants($set_order_data);

/****************************/
//�����襳��������
/****************************/
}elseif($_POST["client_search_flg"] == true){
    $post_client_cd = $_POST["form_client"]["cd"];

    $sql  = "SELECT";
    $sql .= "   client_id,";
    $sql .= "   client_cd1,";
    $sql .= "   client_cname,";
    $sql .= "   coax,";
    $sql .= "   tax_franct,";
    $sql .= "   head_flg,";
    $sql .= "   trade_id ";
    $sql .= " FROM";
    $sql .= "   t_client";
    $sql .= " WHERE";
    if($_SESSION[group_kind] == "2"){
        $sql .= "   shop_id IN (".Rank_Sql().") ";
    }else{
        $sql .= "   shop_id = $_SESSION[client_id]";
    }

    $sql .= "   AND";
    $sql .= "   client_cd1 = '$post_client_cd'";
    $sql .= "   AND";
    $sql .= "   client_div = '2'";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $num = pg_num_rows($result);
    $client_data = pg_fetch_array($result);
    if($num != 0){

        $head_flg = $client_data[5];                                            //�����ե饰

        //���������ǡ�����hidden���å�
        $set_client_data["client_search_flg"]       = "";
        $set_client_data["hdn_client_id"]           = $client_data[0];          //������ID 
        $set_client_data["form_client"]["cd"]       = $client_data[1];          //������CD
        $set_client_data["form_client"]["name"]     = $client_data[2];          //������̾
        $set_client_data["hdn_coax"]                = $client_data[3];          //�ݤ��ʬ�ʾ���
        $set_client_data["hdn_tax_franct"]          = $client_data[4];          //ü����ʬ�ʾ����ǡ�
        $set_client_data["head_flg"]                = $client_data[5];          //�����ե饰 
        $set_client_data["form_trade"]              = $client_data[6];          //�����ʬ

        $client_id  = $client_data[0];                                          //������ID
        $coax       = $client_data[3];                                          //�ݤ��ʬ
        $tax_franct = $client_data[4];                                          //ü����ʬ

        //�����ե饰�����ͤ��֣��ע֣͡�פޤ��ϡ֣�ע֣͡��פˤʤä���硢���Ϥ���Ƥ������ʾ�������ƥ��ꥢ
        if($head_flg != $_POST["head_flg"]){
            for($i = 0; $i < $max_row; $i++){
                $set_client_data["hdn_goods_id"][$i]            = "";           //����ID
                $set_client_data["form_goods_cd"][$i]           = "";           //���ʥ�����
                $set_client_data["form_goods_name"][$i]         = "";           //����̾
                $set_client_data["form_stock_num"][$i]          = "";           //���߸Ŀ�
                $set_client_data["form_order_num"][$i]          = "";           //ȯ����
                $set_client_data["form_buy_num"][$i]            = "";           //������
                $set_client_data["form_rbuy_num"][$i]           = "";           //�����ѿ�
                $set_client_data["form_buy_price"][$i]["i"]     = "";           //����ñ������������
                $set_client_data["form_buy_price"][$i]["d"]     = "";           //����ñ���ʾ�������
                $set_client_data["form_tax_amount"][$i]         = "";           //�����ǳ�
                $set_client_data["form_buy_amount"][$i]         = "";           //������ۡ���ȴ����
                $set_client_data["hdn_name_change"][$i]         = "";           //��̾�ѹ���hidden��
                $set_client_data["hdn_stock_manage"][$i]        = "";           //�߸˴�����hidden��
                $set_client_data["hdn_tax_div"][$i]             = "";           //���Ƕ�ʬ��hidden��
                $set_client_data["hdn_royalty"][$i]             = "";           //�������ƥ�
                $set_client_data["form_in_num"][$i]             = "";           //���åȿ�
                $set_client_data["form_order_in_num"][$i]       = "";           //���åȻ�����
                $set_client_data["form_rorder_num"][$i]         = "";           //ȯ���Ŀ�
                //aoyama-n 2009-09-08
                $set_client_data["hdn_discount_flg"][$i]        = "";           //�Ͱ��ե饰

                $name_change[$i]                                = null;
                $goods_id[$i]                                   = null;
            }
        }

        //�����褬��ʸ�ξ��ϡ�ȯ���������������դ򥻥å�
/*
        if($head_flg == 't'){
            //�����褬��ʸ�ξ��ϡ�ȯ���������������դ򥻥å�
            $set_client_data["form_order_day"]["y"] = date("Y");
            $set_client_data["form_order_day"]["m"] = date("m");
            $set_client_data["form_order_day"]["d"] = date("d");
        }
*/
        $client_search_flg = true;

    }else{
        $set_client_data["client_search_flg"]               = "";               //�����踡���ե饰
        $set_client_data["hdn_client_id"]                   = "";               //������ID
        $set_client_data["form_client"]["name"]             = "";               //������̾
        $set_client_data["head_flg"]                        = "";               //�����ե饰
        $set_client_data["hdn_coax"]                        = "";               //�ݤ��ʬ
        $set_client_data["hdn_tax_franct"]                  = "";               //ü����ʬ

        $client_id = null;

        //���Ƥ��ͤ򥯥ꥢ        
        for($i = 0; $i < $max_row; $i++){
            $set_client_data["hdn_goods_id"][$i]            = "";               //����ID
            $set_client_data["form_goods_cd"][$i]           = "";               //���ʥ�����
            $set_client_data["form_goods_name"][$i]         = "";               //����̾
            $set_client_data["form_stock_num"][$i]          = "";               //���߸Ŀ�
            $set_client_data["form_order_num"][$i]          = "";               //ȯ���ѿ�
            $set_client_data["form_rorder_num"][$i]         = "";               //ȯ���Ŀ�
            $set_client_data["form_buy_num"][$i]            = "";               //������
            $set_client_data["form_rbuy_num"][$i]           = "";               //�����ѿ�
            $set_client_data["form_buy_price"][$i]["i"]     = "";               //����ñ������������
            $set_client_data["form_buy_price"][$i]["d"]     = "";               //����ñ���ʾ�������
            $set_client_data["form_tax_amount"][$i]         = "";               //�����ǳ�
            $set_client_data["form_buy_amount"][$i]         = "";               //������ۡ���ȴ����
            $set_client_data["hdn_name_change"][$i]         = "";               //��̾�ѹ���hidden��
            $set_client_data["hdn_stock_manage"][$i]        = "";               //�߸˴�����hidden��
            $set_client_data["hdn_tax_div"][$i]             = "";               //���Ƕ�ʬ��hidden��
            $set_client_data["hdn_royalty"][$i]             = "";               //�������ƥ�
            $set_client_data["form_in_num"][$i]             = "";               //���åȿ�
            $set_client_data["form_order_in_num"][$i]       = "";               //���åȻ�����
            //aoyama-n 2009-09-08
            $set_client_data["hdn_discount_flg"][$i]        = "";               //�Ͱ��ե饰

            $name_change[$i]                                = null;
            $goods_id[$i]                                   = null;
        }
        $client_search_flg = NULL;
    }

    $set_client_data["show_button_flg"]     = "";        //ɽ���ܥ���
    $set_client_data["form_order_no"]       = "";        //ȯ���ֹ�
    $form->setConstants($set_client_data);

/****************************/
//�����Ҹ�����
/****************************/
}elseif($_POST["stock_search_flg"] == true){
    
    $ware_id = $_POST["form_ware"];
    
    //���ʤ����İʾ����򤵤�Ƥ���н�������
    if($ware_id != NULL){

        for($i = 0; $i < $max_row; $i++){

            $goods_id = $_POST["hdn_goods_id"][$i];

            if($goods_id != NULL){            
                $sql  = "SELECT";
                $sql .= "   stock_num";
                $sql .= " FROM";
                $sql .= "   t_stock";
                $sql .= " WHERE";
                $sql .= "   shop_id = $shop_id";
                $sql .= "   AND";
                $sql .= "   ware_id = $ware_id";
                $sql .= "   AND";
                $sql .= "   goods_id = $goods_id";
                $sql .= ";";

                $result         = Db_Query($conn, $sql);
                $stock_data_num = pg_num_rows($result);

                if($stock_data_num != 0){
                    $stock_data = pg_fetch_result($result,0,0);
                }
                $set_stock_data["form_stock_num"][$i] = ($stock_data != NULL)? $stock_data : 0;     //���߸Ŀ�
            }
        }

        $stock_search_flg = true;
    }else{
        $stock_search_flg = NULL;
    }
    
    $set_stock_data["stock_search_flg"]    = "";
    $set_stock_data["show_button_flg"]     = "";        //ɽ���ܥ���
    $set_stock_data["form_order_no"]       = "";        //ȯ���ֹ�
    $form->setConstants($set_stock_data);

/****************************/
//���ʥ���������
/****************************/
}elseif($_POST["goods_search_row"] != NULL){

    $search_row = $_POST["goods_search_row"];               //���ʸ�����
    $goods_cd   = $_POST["form_goods_cd"]["$search_row"];   //���ʥ�����
    $ware_id    = $_POST["form_ware"];                      //�Ҹ�ID


    //�ܵҶ�ʬ���ü�ξ��
    if($frein_flg === true){
        $goods_data = Get_Rank_Goods_Buy ($conn, $ware_id, $goods_cd);
        $goods_data_num = count($goods_data);
    }else{
        $sql  = "SELECT ";
        $sql .= "   t_goods.goods_id,";
        $sql .= "   t_goods.name_change,";
        #2009-10-13 hashimoto-y
        #$sql .= "   t_goods.stock_manage,";
        $sql .= "   t_goods_info.stock_manage,";

        $sql .= "   t_goods.goods_cd,";
        $sql .= "     (t_g_product.g_product_name || ' ' || t_goods.goods_name) AS goods_name, \n";    //����̾
        $sql .= "   t_goods.tax_div,";
        #2009-10-13 hashimoto-y
        #$sql .= "   CASE t_goods.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num,";
        $sql .= "   CASE t_goods_info.stock_manage WHEN 1 THEN COALESCE(t_stock.stock_num,0) END AS stock_num,";

        $sql .= "   t_price.r_price,";
        $sql .= "   t_goods.in_num,";
        //aoyama-n 2009-09-08
        #$sql .= "   t_goods.royalty";
        $sql .= "   t_goods.royalty,";
        $sql .= "   t_goods.discount_flg";
        $sql .= " FROM";
        $sql .= "   t_goods";
        $sql .= "   INNER JOIN  t_g_product ON t_goods.g_product_id = t_g_product.g_product_id \n";
        $sql .= "   INNER JOIN t_price ON t_goods.goods_id = t_price.goods_id";
        $sql .= "   LEFT JOIN";
        $sql .= "   (SELECT";
        $sql .= "       goods_id,";
        $sql .= "       SUM(stock_num)AS stock_num";
        $sql .= "    FROM";
        $sql .= "        t_stock";
        $sql .= "    WHERE";
        $sql .= "        shop_id = $shop_id";
        $sql .= "        AND";
        $sql .= "        ware_id = $ware_id";
        $sql .= "    GROUP BY t_stock.goods_id"; 
        $sql .= "   )AS t_stock";
        $sql .= "   ON t_goods.goods_id = t_stock.goods_id";
        #2009-10-13 hashimoto-y
        $sql .= "   INNER JOIN t_goods_info   ON t_goods.goods_id  = t_goods_info.goods_id ";

        $sql .= " WHERE";
        $sql .= "   t_goods.goods_cd = '$goods_cd'";

        #2009-10-13 hashimoto-y
        $sql .= "   AND ";
        $sql .= "   t_goods_info.shop_id = $shop_id ";

        $sql .= "   AND";
        $sql .= "   t_goods.accept_flg = '1'";
        $sql .= "   AND";
        //��2006-08-23�˥��ڥ�ߥ�����
        //$sql .= ($group_kind == 2) ? " t_goods.state IN (1,3)" : " t_goods.stete = 1";
        $sql .= ($group_kind == 2) ? " t_goods.state IN (1,3)" : " t_goods.state = 1";

        //����������������ꤵ��Ƥ�����
        if($head_flg == 't'){
            $sql .= "   AND";
            $sql .= "   t_goods.public_flg = 't' ";
            $sql .= "   AND";
            $sql .= "   t_price.rank_cd = '$rank_cd'";
        //������������ʳ������ꤵ��Ƥ�����
        }elseif($head_flg == 'f'){
            $sql .= "   AND";
            $sql .= "   t_goods.public_flg = 'f'";
            $sql .= "   AND";
            if($_SESSION[group_kind] == "2"){
                $sql .= "   t_goods.shop_id IN (".Rank_Sql().") ";
            }else{
                $sql .= "   t_goods.shop_id = $_SESSION[client_id]";
            }

            $sql .= "   AND";
            $sql .= "   t_price.rank_cd = '1'";
            $sql .= "   AND";
            if($_SESSION[group_kind] == "2"){
                $sql .= "   t_price.shop_id IN (".Rank_Sql().") ";
            }else{
                $sql .= "   t_price.shop_id = $_SESSION[client_id]";
            }
        }
        $sql .= ";";

        $result = Db_Query($conn, $sql);
        $goods_data_num = pg_num_rows($result);
        $goods_data = pg_fetch_array($result);
    }

    if($goods_data_num > 0){

        //�������
        $price = $goods_data[7];
 
        if($_POST["form_buy_num"][$search_row] != null){
            $buy_amount = bcmul($price, $_POST["form_buy_num"][$search_row],2);
            $buy_amount = Coax_Col($coax, $buy_amount);
        }

        //����ñ����ʬ��
        $price_data = explode(".",$price);

        //��̾�ѹ�
        $name_change[$search_row]   = $goods_data[1];

        //���Ƕ�ʬ
        $tax_div[$search_row]       = $goods_data[5];

        //�������ƥ�
        $royalty[$search_row]       = $goods_data["royalty"];


        $goods_id[$search_row]      = $goods_data[0];

        //���ʥǡ���
        $set_goods_data["hdn_goods_id"][$search_row]         = $goods_data[0];                                  //����ID
        $set_goods_data["hdn_name_change"][$search_row]      = $goods_data[1];                                  //��̾�ѹ�
        $set_goods_data["hdn_stock_manage"][$search_row]     = $goods_data[2];                                  //�߸˴���
        $set_goods_data["form_goods_cd"][$search_row]        = $goods_data[3];                                  //���ʥ�����
        $set_goods_data["form_goods_name"][$search_row]      = $goods_data[4];                                  //ȯ����
        $set_goods_data["form_stock_num"][$search_row]       = ($goods_data[2] == '1')? $goods_data[6] : "-";   //��ê��
        $set_goods_data["form_buy_price"][$search_row]["i"]  = $price_data[0];                                  //����ñ������������
        $set_goods_data["form_buy_price"][$search_row]["d"]  = ($price_data[1] != NULL)? $price_data[1] : '00'; //����ñ���ʾ�������
        $set_goods_data["form_buy_amount"][$search_row]      = number_format($buy_amount);                      //������ۡ���ȴ����
        $set_goods_data["form_order_num"][$search_row]       = "-";
        $set_goods_data["form_rorder_num"][$search_row]      = "-";
        $set_goods_data["form_rbuy_num"][$search_row]        = "-";
        $set_goods_data["hdn_tax_div"][$search_row]          = $goods_data[5];                                  //���Ƕ�ʬ
        $set_goods_data["form_in_num"][$search_row]          = $goods_data["in_num"];
        $set_goods_data["hdn_royalty"][$search_row]          = $goods_data["royalty"];                          //�������ƥ�
        //aoyama-n 2009-09-08
        $set_goods_data["hdn_discount_flg"][$search_row]     = $goods_data["discount_flg"];                     //�Ͱ��ե饰

        //���å������
        if($goods_data[$i][6]%$goods_data[$i]["in_num"] == 0 && $order_d_data[$i][11]!=null){
            $set_goods_data["form_order_in_num"][$i]  = $order_d_data[$i][5]/$order_d_data[$i][11];
        }

    //���ʥ����ɤ��������ͤ����Ϥ��줿���
    }else{
        //���ʥǡ���
        $set_goods_data["hdn_goods_id"][$search_row]         = "";              //����ID
        $set_goods_data["hdn_name_change"][$search_row]      = "";              //��̾�ѹ�
        $set_goods_data["hdn_stock_manage"][$search_row]     = "";              //�߸˴���
        $set_goods_data["form_goods_cd"][$search_row]        = "";              //���ʥ�����
        $set_goods_data["form_goods_name"][$search_row]      = "";              //ȯ����
        $set_goods_data["form_stock_num"][$search_row]       = "";              //��ê��
        $set_goods_data["form_buy_price"][$search_row]["i"]  = "";              //����ñ������������
        $set_goods_data["form_buy_price"][$search_row]["d"]  = "";              //����ñ���ʾ�������
        $set_goods_data["hdn_tax_div"][$search_row]          = "";              //���Ƕ�ʬ
        $set_goods_data["form_order_num"][$search_row]       = "";              //ȯ����
        $set_goods_data["form_buy_amount"][$search_row]      = "";              //������ۡ���ȴ����
        $set_goods_data["form_order_num"][$search_row]       = "";
        $set_goods_data["form_rorder_num"][$search_row]      = "";
        $set_goods_data["form_rbuy_num"][$search_row]        = "";
        $set_goods_data["form_in_num"][$search_row]          = "";
        $set_goods_data["form_order_in_num"][$search_row]    = "";
        $set_goods_data["form_buy_num"][$search_row]         = "";
        $set_goods_data["hdn_royalty"][$search_row]          = "";
        //aoyama-n 2009-09-08
        $set_goods_data["hdn_discount_flg"][$search_row]     = "";
        $name_change[$search_row]   = "";
        $tax_div[$search_row]       = "";
        $royalty[$search_row]       = "";
        $goods_id[$search_row]      = "";
    }

    $set_goods_data["goods_search_row"] = "";
    $form->setConstants($set_goods_data);
}

/****************************/
//��ץܥ��󲡲�����
/****************************/
if($_POST["sum_button_flg"] == true || $_POST["del_row"] != "" || $_POST["form_buy_button"] == "������ǧ���̤�"){

    //����ꥹ�Ȥ����
    $del_row = $_POST["del_row"];
    //������������ˤ��롣
    $del_history = explode(",", $del_row);

    $buy_data   = $_POST["form_buy_amount"];    //�������
    $price_data = NULL;                         //���ʤλ������
    $tax_div    = NULL;                         //���Ƕ�ʬ

    //������ۤι���ͷ׻�
    for($i=0;$i<$max_row;$i++){
        if($buy_data[$i] != "" && !in_array("$i", $del_history)){
            $price_data[] = $buy_data[$i];
            $tax_div[]    = $_POST["hdn_tax_div"][$i];
        }
    }

    #2009-12-21 aoyama-n
    $tax_rate_obj->setTaxRateDay($_POST["form_buy_day"]["y"]."-".$_POST["form_buy_day"]["m"]."-".$_POST["form_buy_day"]["d"]);
    $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

    $data = Total_Amount($price_data, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $conn);

    if($_POST["sum_button_flg"] == true){
        //���ɽ�������ѹ�
        $height = $max_row * 30;
        $form_potision = "<body bgcolor=\"#D8D0C8\" onLoad=\"form_potision($height);\">";
    }

    //�ե�������ͥ��å�
    $money_data["form_buy_money"]   = number_format($data[0]);
    $money_data["form_tax_money"]   = number_format($data[1]);
    $money_data["form_total_money"] = number_format($data[2]);
    $money_data["sum_button_flg"]   = "";   
    $form->setConstants($money_data);
}

/*****************************/
//��ݻĹ�����
/*****************************/
if($client_search_flg == true){

/*
    //���������
    $sql  = "SELECT\n";
    $sql .= "   close_day\n";
    $sql .= " FROM\n";
    $sql .= "   t_client\n";
    $sql .= " WHERE\n";
    $sql .= "   t_client.client_id = $client_id\n";
    $sql .= ";";

    $result = Db_Query($conn,$sql);
    $close_day = pg_fetch_result($result, 0, 0);

    //��������
    $yy = date('Y');
    $mm = date('m');

    if($close_day < 29){
        $last_close_day = date('Ymd', mktime(0,0,0,$mm, $close_day, $yy));
    }else{
        $last_close_day = date('Ymd', mktime(0,0,0,$mm+1,-1,$yy));
    }

    //������������������ۤ����
    $sql  = "SELECT\n";
    $sql .= "    (COALESCE(t_plus.net_amount,0) - COALESCE(t_minus.net_amount,0)) AS ap_balance\n";
    $sql .= " FROM\n";
    $sql .= "    (SELECT\n";
    $sql .= "        client_id,\n";
    $sql .= "        SUM(t_buy_h.net_amount)AS net_amount\n";
    $sql .= "    FROM\n";
    $sql .= "        t_buy_h\n";
    $sql .= "    WHERE\n";
    $sql .= "        t_buy_h.client_id = $client_id\n";
    $sql .= "        AND\n";
    $sql .= "        t_buy_h.trade_id IN ('21', '25')\n";
    $sql .= "        AND\n";
//    $sql .= "        t_buy_h.buy_day > '2006-05-31'\n";
    $sql .= "       t_buy_h.buy_day > (SELECT\n";
    $sql .= "                           COALESCE(MAX(payment_close_day), '".START_DAY."')\n";
    $sql .= "                       FROM\n";
    $sql .= "                           t_schedule_payment\n";
    $sql .= "                       WHERE\n";
    $sql .= "                           shop_id = $shop_id\n";
    $sql .= "                           AND\n";
    $sql .= "                           client_id = $client_id\n";
    $sql .= "                       )\n";
    $sql .= "        AND\n";
    $sql .= "        t_buy_h.buy_day < '$last_close_day'\n";
    $sql .= "    GROUP BY client_id\n";
    $sql .= "    ) AS t_plus\n";
    $sql .= "        LEFT JOIN\n";
    $sql .= "    (SELECT\n";
    $sql .= "        client_id,\n";
    $sql .= "        SUM(t_buy_h.net_amount)AS net_amount\n";
    $sql .= "    FROM\n";
    $sql .= "        t_buy_h\n";
    $sql .= "    WHERE\n";
    $sql .= "        t_buy_h.client_id = $client_id\n";
    $sql .= "        AND\n";
    $sql .= "        t_buy_h.trade_id IN ('23','24')\n";
    $sql .= "        AND\n";
    $sql .= "       t_buy_h.buy_day > (SELECT\n";
    $sql .= "                           COALESCE(MAX(payment_close_day), '".START_DAY."')\n";
    $sql .= "                       FROM\n";
    $sql .= "                           t_schedule_payment\n";
    $sql .= "                       WHERE\n";
    $sql .= "                           shop_id = $shop_id\n";
    $sql .= "                           AND\n";
    $sql .= "                           client_id = $client_id\n";
    $sql .= "                       )\n";

//    $sql .= "        t_buy_h.buy_day > '2006-05-31'\n";
    $sql .= "        AND\n";
    $sql .= "        t_buy_h.buy_day < '$last_close_day'\n";
    $sql .= "    GROUP BY client_id\n";
    $sql .= "    ) AS t_minus\n";
    $sql .= "    ON t_plus.client_id = t_minus.client_id\n";
    $sql .= ";\n";

    $result = Db_Query($conn, $sql);
    $ap_balance = number_format(@pg_fetch_result($result,0,0));
*/
    /****************************/
    // ��ݥǡ��������ʻĹ�Τߡ�
    /****************************/
    // ��ɼ���٥ǡ��������ʥǡ�����̵�����϶����������
    $sql = Ap_Particular_Sql(START_DAY, date("Y-m-d"), $client_id);
    $res = Db_Query($conn, $sql);
    $num = pg_num_rows($res);
    $ary_ap_particular_data = ($num > 0) ? Get_Data($res, 2, "ASSOC") : array(null);

    // ��ݻĹ⻻��
    foreach ($ary_ap_particular_data as $key => $value){
        $ap_balance += ($value["buy_amount"] - $value["payout_amount"]);
    }

    $ap_balance = number_format($ap_balance);

}

//***************************/
//����Կ���hidden�˥��å�
/****************************/
$max_row_data["max_row"] = $max_row;

$form->setConstants($max_row_data);

/****************************/
//���ϥ�å���������
/****************************/
if($client_search_flg == true && $stock_search_flg == true){
    if($head_flg == 't'){
        $message = "�������ʤΤ������ǽ�Ǥ���";

    }elseif($frein_flg === true){
        $message = "���ʤ������ǽ�Ǥ���";

    }else{
        $message = "�������ʰʳ��������ǽ�Ǥ���";
    }
}else{
    $warning = "������Ȼ����Ҹˤ����򤷤Ƥ���������";
}

/****************************/
//�ե��������
/****************************/
//��ɼ�ֹ�
$form->addElement(
    "text","form_buy_no","",
    "style=\"color : #525552; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);

//ȯ����
$select_value[null] = null;
$sql  = "SELECT";
$sql .= "    t_order_h.ord_id,";
$sql .= "    t_order_h.ord_no";
$sql .= " FROM";
$sql .= "    t_order_h";
$sql .= "        INNER JOIN";
$sql .= "    (SELECT";
$sql .= "        ord_id";
$sql .= "     FROM";
$sql .= "        t_order_d";
$sql .= "    WHERE";
$sql .= "        rest_flg = 't'";
$sql .= "    GROUP BY ord_id) AS t_order_d";
$sql .= "    ON t_order_h.ord_id = t_order_d.ord_id";
$sql .= " WHERE";
//$sql .= "    t_order_h.shop_id = 93";
$sql .= "    t_order_h.shop_id = $_SESSION[client_id]";
$sql .= "    AND";
$sql .= "    (t_order_h.ord_stat <> 3";
$sql .= "    OR";
$sql .= "    t_order_h.ord_stat IS NULL)";
$sql .= "    AND";
$sql .= "    t_order_h.ps_stat IN ('1','2')";
$sql .= " ORDER BY t_order_h.ord_no";
$sql .= ";";

$result = Db_Query($conn, $sql);
$num = pg_num_rows($result);
for($i = 0; $i < $num; $i++){
    $ord_id = pg_fetch_result($result,$i,0);
    $ord_no = pg_fetch_result($result,$i,1);
    $select_value[$ord_id] = $ord_no;
}

//ȯ���ֹ�
if($buy_get_flg == true){
    $form->addElement(
        "text","form_order_no","",
        "size=\"11\" maxLength=\"8\" 
        style=\"color : #525552; 
        border : #ffffff 1px solid; 
        background-color: #ffffff; 
        text-align: left\" readonly'"
    );
}else{
//   $order_freeze[] = $form->addElement("select","form_order_no","",$select_value, $g_form_option_select);
   //aoyama-n 2009-09-16
   #$form->addElement("select","form_order_no","",$select_value, $g_form_option_select);
   $head_form[] = $form->addElement("select","form_order_no","",$select_value, $g_form_option_select);
}

//ȯ����
$form_order_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\"
    style=\"color : #525552; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_order_day[] = $form->createElement("static","","","-");
$form_order_day[] = $form->createElement(
    "text","m","","size=\"2\" maxLength=\"2\" 
    style=\"color : #525552; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_order_day[] = $form->createElement("static","","","-");
$form_order_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"color : #525552; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$order_freeze[] = $form->addGroup( $form_order_day,"form_order_day","");

//����ͽ����
$form_arrival_hope_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\"
    style=\"color : #525552; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_arrival_hope_day[] = $form->createElement("static","","","-");
$form_arrival_hope_day[] = $form->createElement(
    "text","m","","size=\"2\" maxLength=\"2\" 
    style=\"color : #525552; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form_arrival_hope_day[] = $form->createElement("static","","","-");
$form_arrival_hope_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"color : #525552; 
    border : #ffffff 1px solid; 
    background-color: #ffffff; 
    text-align: left\" readonly'"
);
$form->addGroup( $form_arrival_hope_day,"form_arrival_hope_day","");

//������
$form_arrival_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\" 
    style=\"$g_form_style\"
    onkeyup=\"changeText(this.form,'form_arrival_day[y]','form_arrival_day[m]',4)\" 
    onFocus=\"onForm_today(this,this.form,'form_arrival_day[y]','form_arrival_day[m]','form_arrival_day[d]')\"
    onBlur=\"blurForm(this)\"
    onChange=\"Claim_Day_Change('form_arrival_day', 'form_buy_day')\"
    "
);
$form_arrival_day[] = $form->createElement("static","","","-");
$form_arrival_day[] = $form->createElement(
    "text","m","","size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style\"
    onkeyup=\"changeText(this.form,'form_arrival_day[m]','form_arrival_day[d]',2)\" 
    onFocus=\"onForm_today(this,this.form,'form_arrival_day[y]','form_arrival_day[m]','form_arrival_day[d]')\"
    onBlur=\"blurForm(this)\"
    onChange=\"Claim_Day_Change('form_arrival_day', 'form_buy_day')\"
    "
);
$form_arrival_day[] = $form->createElement("static","","","-");
$form_arrival_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style\"
    onFocus=\"onForm_today(this,this.form,'form_arrival_day[y]','form_arrival_day[m]','form_arrival_day[d]')\"
    onBlur=\"blurForm(this)\"
    onChange=\"Claim_Day_Change('form_arrival_day', 'form_buy_day')\"
    "
);
//aoyama-n 2009-09-16
#$form->addGroup( $form_arrival_day,"form_arrival_day","");
$head_form[] = $form->addGroup( $form_arrival_day,"form_arrival_day","");

//������
$form_buy_day[] = $form->createElement(
    "text","y","",
    "size=\"4\" maxLength=\"4\" 
    style=\"$g_form_style\"
    onkeyup=\"changeText(this.form,'form_buy_day[y]','form_buy_day[m]',4)\" 
    onFocus=\"onForm_today(this,this.form,'form_buy_day[y]','form_buy_day[m]','form_buy_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form_buy_day[] = $form->createElement("static","","","-");
$form_buy_day[] = $form->createElement(
    "text","m","","size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style\"
    onkeyup=\"changeText(this.form,'form_buy_day[m]','form_buy_day[d]',2)\" 
    onFocus=\"onForm_today(this,this.form,'form_buy_day[y]','form_buy_day[m]','form_buy_day[d]')\"
    onBlur=\"blurForm(this)\""
);
$form_buy_day[] = $form->createElement("static","","","-");
$form_buy_day[] = $form->createElement(
    "text","d","",
    "size=\"2\" maxLength=\"2\" 
    style=\"$g_form_style\"
    onFocus=\"onForm_today(this,this.form,'form_buy_day[y]','form_buy_day[m]','form_buy_day[d]')\"
    onBlur=\"blurForm(this)\""
);
//aoyama-n 2009-09-16
#$form->addGroup( $form_buy_day,"form_buy_day","");
$head_form[] = $form->addGroup( $form_buy_day,"form_buy_day","");

//������
$freeze_form = $form_client[] = $form->createElement(
    "text","cd","",
    "size=\"7\" maxLength=\"6\" 
    style=\"$g_form_style\"
    onChange=\"javascript:Button_Submit('client_search_flg','#','true')\" 
    $g_form_option"
);
$form_client[] = $form->createElement(
    "text","name","",
    "size=\"34\" $g_text_readonly"
);
//aoyama-n 2009-09-16
#$form->addGroup( $form_client, "form_client", "");
$head_form[] = $form->addGroup( $form_client, "form_client", "");

//�����襳���������Բ�
if($freeze_flg == true || $buy_get_flg == true){
    $freeze_form->freeze();
}

//ľ����
$select_value = Select_Get($conn,'direct');
//aoyama-n 2009-09-16
#$order_freeze[] = $form->addElement('select', 'form_direct', "", $select_value,"class=\"Tohaba\"".$g_form_option_select);
$head_form[] = $order_freeze[] = $form->addElement('select', 'form_direct', "", $select_value,"class=\"Tohaba\"".$g_form_option_select);

//�����Ҹ�
/*
$where  = " WHERE";
$where .= ($group_kind == "2")?  " own_shop_id IN (".Rank_Sql().") " : " shop_id = $shop_id ";
$where .= "  AND";
$where .= "  nondisp_flg = 'f'";
*/
//$select_value = Select_Get($conn,'ware', $where);
//$select_value = Select_Get($conn,'ware', $where);
$select_value = Select_Get($conn,'ware',"WHERE shop_id = $_SESSION[client_id] AND staff_ware_flg = 'f' AND nondisp_flg = 'f' ");
//aoyama-n 2009-09-16
#$order_freeze[] = $form->addElement('select', 'form_ware', '', $select_value,"onChange=\"javascript:Button_Submit('stock_search_flg','#','true')\"");
$head_form[] = $order_freeze[] = $form->addElement('select', 'form_ware', '', $select_value,"onChange=\"javascript:Button_Submit('stock_search_flg','#','true')\"");

//�����ʬ
$select_value = null;
if($get_order_id != null){
    $select_value = Select_Get($conn,'trade_buy_ord');
}else{
    $select_value = Select_Get($conn,'trade_buy');
}
$trade = $form->addElement('select', 'form_trade', null, null,$g_form_option_select);

//���ʡ��Ͱ����ο����ѹ�
$select_value_key = array_keys($select_value);
for($i = 0; $i < count($select_value); $i++){
    if($select_value_key[$i] == 23 || $select_value_key[$i] == 24 || $select_value_key[$i] == 73 || $select_value_key[$i] == 74){ 
         $color= "style=color:red";
    }else{  
          $color="";
    }
    #2009-09-28 hashimoto-y
    #�����ʬ�����Ͱ�����ɽ�����ʤ����ڤ��ᤷ�ξ��ˤϤ�����ifʸ�򳰤���
    if($select_value_key[$i] != 24 && $select_value_key[$i] != 74){
        $trade->addOption($select_value[$select_value_key[$i]], $select_value_key[$i],$color);
    }
}
//aoyama-n 2009-09-16
#$order_freeze[] = $trade;
$head_form[] = $order_freeze[] = $trade;

//ȯ��ô����
$select_value = Select_Get($conn,'staff',null,true);
$freeze =  $form->addElement('select', 'form_order_staff', '', $select_value,$g_form_option_select);
$freeze->freeze();

//����ô����
$select_value = Select_Get($conn,'staff',null,true);
//$order_freeze[] = $form->addElement('select', 'form_buy_staff', '', $select_value,$g_form_option_select);
//aoyama-n 2009-09-16
#$form->addElement('select', 'form_buy_staff', '', $select_value,$g_form_option_select);
$head_form[] = $form->addElement('select', 'form_buy_staff', '', $select_value,$g_form_option_select);

//����
//$form->addElement("text","form_note","","size=\"50\" maxLength=\"20\" $g_form_option");
//aoyama-n 2009-09-16
#$form->addElement("textarea", "form_note", "", "rows=\"2\" cols=\"75\" $g_form_option_area");
$head_form[] = $form->addElement("textarea", "form_note", "", "rows=\"2\" cols=\"75\" $g_form_option_area");

//�������(���)
$form->addElement(
        "text","form_buy_money","",
        "size=\"25\" maxLength=\"18\"
         style=\"color : #000000;
         border : #FFFFFF 1px solid; 
         background-color: #FFFFFF; 
         text-align: right\"
         readonly"
);

//�����ǳ�(���)
$form->addElement(
        "text","form_tax_money","",
        "size=\"25\" maxLength=\"18\"
         style=\"color : #000000;
         border : #FFFFFF 1px solid; 
         background-color: #FFFFFF; 
         text-align: right\"
         readonly"
);

//������ۡ��ǹ����)
$form->addElement(
        "text","form_total_money","",
        "size=\"25\" maxLength=\"18\"
         style=\"color : #000000;
         border : #FFFFFF 1px solid; 
         background-color: #FFFFFF; 
         text-align: right\"
         readonly"
);

//���ϡ��ѹ�
$form->addElement("button","new_button","������",$g_button_color."onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
//�Ȳ�
$form->addElement("button","change_button","�Ȳ��ѹ�","onClick=\"javascript:Referer('2-3-202.php')\"");
//���
$form->addElement("button","form_sum_button","�硡��","onClick=\"javascript:Button_Submit('sum_button_flg','#foot','true')\"");

//hidden
$form->addElement("hidden", "del_row");             //�����
$form->addElement("hidden", "add_row_flg");         //�ɲùԥե饰
$form->addElement("hidden", "max_row");             //����Կ�
$form->addElement("hidden", "show_button_flg");     //����Կ�
$form->addElement("hidden", "client_search_flg");   //�����踡���ե饰
$form->addElement("hidden", "hdn_coax");            //�ݤ��ʬ
$form->addElement("hidden", "hdn_tax_franct");      //ü����ʬ
$form->addElement("hidden", "goods_search_row");    //���ʥ��������Ϲ�
$form->addElement("hidden", "hdn_client_id");       //������ID
$form->addElement("hidden", "stock_search_flg");    //���߸Ŀ������ե饰
$form->addElement("hidden", "head_flg");            //�����ե饰
$form->addElement("hidden", "hdn_order_id");        //ȯ��ID
$form->addElement("hidden", "hdn_buy_id");          //����ID
$form->addElement("hidden", "sum_button_flg");      //��ץܥ��󲡲��ե饰
$form->addElement("hidden", "freeze_flg");          //���Ϲ��ܥե꡼���ե饰
$form->addElement("hidden", "order_freeze_flg");    //ȯ���ֹ�ե꡼���ե饰
$form->addElement("hidden", "hdn_ord_enter_day");   //ȯ����Ͽ��
$form->addElement("hidden", "hdn_buy_enter_day");   //������Ͽ��
$form->addElement("hidden", "hdn_ord_change_day");  //ȯ���ѹ���


//aoyama-n 2009-09-16
for($i = 0; $i < $max_row; $i++){      
    if(!in_array("$i", $del_history)){
        $form->addElement("hidden","hdn_discount_flg[$i]","","");
    }
}

/*
//ľ�Ĥξ��Τ�ɽ��
if($group_kind == '2' && $buy_get_flg != true){
    //�������о�
    $form->addElement("button","form_client_button","������","$g_button_color onClick=\"javascript:location.href='./2-3-201.php';\"");
    //FC �о�
    $form->addElement("button","form_fc_button","�ơ���","onClick=\"javascript:location.href='./2-3-207.php'\"");
}
*/

/****************************/
//�����ܥ��󲡲�����
/****************************/
if($_POST["form_buy_button"] == "������ǧ���̤�" || $_POST["comp_button"] == "������λ"){

    /******************************/
    //POST����
    /******************************/
    $client_id                  = $_POST["hdn_client_id"];                      //������ID
    $client_cd                  = $_POST["form_client"]["cd"];                  //������CD
    $buy_no                     = $_POST["form_buy_no"];                        //�����ֹ�
    $order_id                   = ($_POST["hdn_order_id"] != NULL)? $_POST["hdn_order_id"] : NULL;    //ȯ��ID
    $order_no                   = $_POST["form_order_no"];                      //ȯ���ֹ�
    $order_date                 = $_POST["form_order_day"]["y"];                //ȯ������ǯ��
    $order_date                 = $_POST["form_order_day"]["m"];                //ȯ�����ʷ��
    $order_date                 = $_POST["form_order_day"]["d"];                //ȯ����������
/*
    $arrival_hope_date["y"]     = $_POST["form_arrival_hope_day"]["y"];     //����ͽ������ǯ��
    $arrival_hope_date["m"]     = $_POST["form_arrival_hope_day"]["m"];     //����ͽ�����ʷ��
    $arrival_hope_date["d"]     = $_POST["form_arrival_hope_day"]["d"];     //����ͽ���������� 
*/
    $arrival_date["y"]          = $_POST["form_arrival_day"]["y"];              //��������ǯ��
    $arrival_date["m"]          = $_POST["form_arrival_day"]["m"];              //�������ʷ��
    $arrival_date["d"]          = $_POST["form_arrival_day"]["d"];              //������������
    $buy_date["y"]              = $_POST["form_buy_day"]["y"];                  //��������ǯ��
    $buy_date["m"]              = $_POST["form_buy_day"]["m"];                  //�������ʷ��
    $buy_date["d"]              = $_POST["form_buy_day"]["d"];                  //������������
    $direct                     = ($_POST["form_direct"] != NULL)? $_POST["form_direct"] : NULL;  //ľ����
    $ware                       = $_POST["form_ware"];                          //�����Ҹ�
    $trade                      = $_POST["form_trade"];                         //�����ʬ
    $buy_staff                  = $_POST["form_buy_staff"];                     //����ô����
    $order_staff                = $_POST["form_order_staff"];                   //����ô����
    $note                       = $_POST["form_note"];                          //����

    //ȯ�����ơ����������å�
    if($order_id != null && $buy_get_flg == false){
        $sql  = "SELECT \n";
        $sql .= "   t_order_h.ps_stat \n";
        $sql .= "FROM \n";
        $sql .= "   t_order_h \n";
        $sql .= "WHERE \n";
        $sql .= "   t_order_h.ord_id = $order_id\n";
        $sql .= ";";

        $result = Db_Query($conn, $sql);

        if(pg_num_rows > 0){
            $ps_stat = pg_fetch_result($result, 0,0);
        }
        //ȯ���ν�����������λ�ξ��
        if($ps_stat == '3'){
            header("Location:./2-3-205.php?buy_id=0&input_flg=true&ps_stat=true");
        }
    }

    //����������å�
    $sql  = "SELECT";
    $sql .= "   COUNT(client_id) ";
    $sql .= "FROM";
    $sql .= "   t_client ";
    $sql .= "WHERE";
    $sql .= "   client_id = $client_id";
    $sql .= "   AND";
    $sql .= "   client_cd1 = '$client_cd'";
    $sql .= ";";

    $result = Db_Query($conn, $sql);
    $client_num = pg_fetch_result($result, 0, 0);

    if($client_num != 1){
        $form->setElementError("form_client", "���������������� ȯ����ǧ���̤إܥ��� <br>��������ޤ�����������ľ���Ƥ���������");
    }elseif($client_cd != null){

        //aoyama-n 2009-09-08
        //�Ͱ�����������μ����ʬ�����å����Ͱ������ʤϻ����Բġ�
        if(($trade == '23' || $trade == '24' || $trade == '73' || $trade == '74') && (in_array('t', $_POST[hdn_discount_flg]))){
            $form->setElementError("form_trade", "�Ͱ����ʤ����򤷤���硢���ѤǤ�������ʬ�ϡֳݻ����������������������פΤߤǤ���");
        }

        //�ǡ��������å����Ϥ���������
        $check_ary = array($conn, $order_id, $_POST["hdn_ord_enter_day"], $get_buy_id);

        //�ǡ��������å�
        $check_data = Row_Data_Check(
                                $_POST[hdn_goods_id],       //����ID
                                $_POST[form_goods_cd],      //���ʥ�����
                                $_POST[form_goods_name],    //����̾
                                $_POST[form_buy_num],       //������
                                $_POST[form_buy_price],     //����ñ��
                                $_POST[form_buy_amount],    //�������
                                $_POST[hdn_tax_div],        //���Ƕ�ʬ
                                $del_history,               //�������
                                $max_row,                   //����Կ�
                                "buy",                      //��ʬ
                                $conn,
                                $_POST[form_order_num],     //ȯ����
                                $_POST[hdn_royalty],        //�������ƥ�
                                $_POST[hdn_order_d_id],     //ȯ���ǡ���ID
                                //aoyama-n 2009-09-08
                                #$check_ary
                                $check_ary,
                                $_POST[hdn_discount_flg]    //�Ͱ��ե饰
                                );

        //���顼�����ä����
        if($check_data[0] === true){
            //���ʤ���Ĥ����򤵤�Ƥ��ʤ����
            $form->setElementError("form_buy_no",$check_data[1]);

            //���������ʥ����ɤ����Ϥ���Ƥ��ʤ����
            $goods_err = $check_data[2];

            //ȯ�����Ȼ���ñ�������Ϥ����뤫
            $price_num_err = $check_data[3];

            //ȯ����Ⱦ�ѿ��������å�
            $num_err = $check_data[4];

            //ñ��Ⱦ�ѥ����å�
            $price_err = $check_data[5];

            $err_flg = true; 
        //���顼���ʤ��ä����
        }else{  
            //�ѿ������
            $goods_id   = null; 
            $goods_cd   = null; 
            $goods_name = null; 
            $buy_num    = null; 
            $buy_price  = null; 
            $buy_amount = null; 
            $tax_div    = null; 
            $order_num  = null; 
            $royalty    = null;
            $order_d_id = null;

            //��Ͽ�оݥǡ������ѿ��˥��å�
            $goods_id   = $check_data[1][goods_id];
            $goods_cd   = $check_data[1][goods_cd];
            $goods_name = $check_data[1][goods_name];
            $buy_num    = $check_data[1][num];
            $buy_price  = $check_data[1][price];
            $buy_amount = $check_data[1][amount];
            $tax_div    = $check_data[1][tax_div];
            $order_d_id = $check_data[1][data_id];
            $order_num  = $check_data[1][num2];
            $royalty    = $check_data[1][royalty];
            $def_line   = $check_data[1][def_line];     //�Կ�
        }

        //�������ƥ��׻�
        $royalty_data = Total_Royalty($buy_amount, $royalty, $royalty_rate, $coax);
    }
    /******************************/
    //�롼�������QuickForm��
    /******************************/
    //������
    //��ɬ�ܥ����å�
    $form->addGroupRule('form_arrival_day', array(
            'y' => array(
                    array('�����������դ������ǤϤ���ޤ���', 'required')
            ),
            'm' => array(
                    array('�����������դ������ǤϤ���ޤ���','required')
            ),
            'd' => array(
                    array('�����������դ������ǤϤ���ޤ���','required')
            )
    ));
    $form->addGroupRule('form_arrival_day', array(
            'y' => array(
                    array('�����������դ������ǤϤ���ޤ���', 'numeric')
            ),
            'm' => array(
                    array('�����������դ������ǤϤ���ޤ���','numeric')
            ),
            'd' => array(
                    array('�����������դ������ǤϤ���ޤ���','numeric')
            )
    ));
    
    //������
    //��ɬ�ܥ����å�
    $form->addGroupRule('form_buy_day', array(
            'y' => array(
                    array('�����������դ������ǤϤ���ޤ���','required')
            ),
            'm' => array(
                    array('�����������դ������ǤϤ���ޤ���','required')
            ),
            'd' => array(
                    array('�����������դ������ǤϤ���ޤ���','required')
            )
    ));
    $form->addGroupRule('form_buy_day', array(
            'y' => array(
                    array('�����������դ������ǤϤ���ޤ���','numeric')
            ),
            'm' => array(
                    array('�����������դ������ǤϤ���ޤ���','numeric')
            ),
            'd' => array(
                    array('�����������դ������ǤϤ���ޤ���','numeric')
            )
    ));

    //������CD
    //��ɬ�ܥ����å�
    $form->addGroupRule('form_client', array(
            'cd' => array(
                    array('�����������襳���ɤ����Ϥ��Ƥ���������','required')
            ),
            'name' => array(
                    array('�����������襳���ɤ����Ϥ��Ƥ���������','required')
            )
    ));

    //�����Ҹ�
    //��ɬ�ܥ����å�
    $form->addRule('form_ware','�����Ҹˤ����򤷤Ƥ���������','required');

    //�����ʬ
    //��ɬ�ܥ����å�
    $form->addRule('form_trade','�����ʬ�����򤷤Ƥ���������','required');

    //����ô����
    //��ɬ�ܥ����å�
    $form->addRule('form_buy_staff','����ô���Ԥ����򤷤Ƥ���������','required');

    // ������
    // ʸ���������å�
    $form->registerRule("mb_maxlength", "function", "Mb_Maxlength");
    $form->addRule("form_note", "���ͤ�100ʸ������Ǥ���", "mb_maxlength", "100");

    /******************************/
    //�롼�����
    /******************************/

    //������
    //�����������������å�
    if(!checkdate((int)$arrival_date["m"], (int)$arrival_date["d"], (int)$arrival_date["y"])){
        $form->setElementError("form_arrival_day", "�����������դ������ǤϤ���ޤ���");
    }else{
        //�����
        if(Check_Monthly_Renew($conn, $client_id, '2', $arrival_date["y"], $arrival_date["m"], $arrival_date["d"]) === false){
             $form->setElementError("form_arrival_day", "�������˷���������������դ���Ͽ�Ǥ��ޤ���");
        }
        //�����ƥ೫���������å�
        $arrival_day_err   = Sys_Start_Date_Chk($arrival_date["y"], $arrival_date["m"], $arrival_date["d"], "������");
        if($arrival_day_err != Null){
            $form->setElementError("form_arrival_day", $arrival_day_err);
        }       
    }

    //������
    //�����������������å�
    if(!checkdate((int)$buy_date["m"], (int)$buy_date["d"], (int)$buy_date["y"])){
        $form->setElementError("form_buy_day", "�����������դ������ǤϤ���ޤ���");
    }else{
          //�����
        if(Check_Monthly_Renew($conn, $client_id, '2', $buy_date["y"], $buy_date["m"], $buy_date["d"]) === false){
             $form->setElementError("form_buy_day", "�������˷���������������դ���Ͽ�Ǥ��ޤ���");
        }

        //�������������å�
        if(Check_Payment_Close_Day($conn, $client_id, $buy_date["y"], $buy_date["m"], $buy_date["d"]) === false){
             $form->setElementError("form_buy_day", "�������˻����������������դ���Ͽ�Ǥ��ޤ���");
        }
        //�����ƥ೫���������å�
        $buy_day_err   = Sys_Start_Date_Chk($buy_date["y"], $buy_date["m"], $buy_date["d"], "������");
        if($buy_day_err != Null){
            $form->setElementError("form_buy_day", $buy_day_err);
        }       
    }

/*
    //���ʥ����å�
    //���ʽ�ʣ�����å�
    for($i = 0; $i < count($goods_id); $i++){
        for($j = $i+1; $j < count($goods_id); $j++){
            if($goods_id[$i] != null && $goods_id[$j] != null && $i != $j && $goods_id[$i] == $goods_id[$j]){
                $goods_twice = "Ʊ�����ʤ��������򤵤�Ƥ��ޤ���";
            }
        }
    }
*/
    //���ʥ����å�
    //���ʽ�ʣ�����å�
    $goods_count = count($goods_id);
    for($i = 0; $i < $goods_count; $i++){

        //���˥����å��Ѥߤξ��ʤξ��ώ������̎�
        if(@in_array($goods_id[$i], $checked_goods_id)){
            continue;
        }

        //�����å��оݤȤʤ뾦��
        $err_goods_cd = $goods_cd[$i];
        $mst_line = $def_line[$i];

        for($j = $i+1; $j < $goods_count; $j++){
            //���ʤ�Ʊ�����
            if($goods_id[$i] == $goods_id[$j]) {
                $duplicate_line .= ", ".($def_line[$j]);
            }
        }
        $checked_goods_id[] = $goods_id[$i];    //�����å��Ѥ߾���
   
        if($duplicate_line != null){ 
            $duplicate_goods_err[] =  "���ʥ����ɡ�".$err_goods_cd."�ξ��ʤ�ʣ�����򤵤�Ƥ��ޤ���(".$mst_line.$duplicate_line."����)";
        }

        $err_goods_cd   = null;
        $mst_line       = null;
        $duplicate_line = null;
    }

    /*****************************/
    //�͸���
    /*****************************/
    if($form->validate()){

        /*******************************/
        //��Ͽ����
        /*******************************/
        if($_POST["comp_button"] == "������λ"){

            #2009-12-21 aoyama-n
            $tax_rate_obj->setTaxRateDay($_POST["form_buy_day"]["y"]."-".$_POST["form_buy_day"]["m"]."-".$_POST["form_buy_day"]["d"]);
            $tax_rate = $tax_rate_obj->getClientTaxRate($client_id);

            for($i = 0; $i < count($goods_name); $i++){
                $total_amount_data = Total_Amount($buy_amount, $tax_div, $coax, $tax_franct, $tax_rate, $client_id, $conn);
            }

            Db_Query($conn, "BEGIN");

            //������ID��������
            if($buy_get_flg == true){

                //�������������Ƥ��ʤ�����ǧ
                $update_check_flg = Update_Check($conn, "t_buy_h", "buy_id", $get_buy_id, $_POST["hdn_buy_enter_day"]);
                //���˺������Ƥ������
                if($update_check_flg === false){
                    header("Location:./2-3-205.php?buy_id=$get_buy_id&input_flg=true&del_buy_flg=true");
                    exit;
                }

                if($order_id != null){
                    //ȯ�����ѹ�����Ƥ��ʤ�����ǧ
                    $update_data_check_flg = Update_Data_Check($conn, "t_order_h", "ord_id", $order_id, $_POST["hdn_ord_change_day"]);
                    if($update_data_check_flg === false){
                        header("Location:./2-3-205.php?buy_id=0&input_flg=true&change_ord_flg=true");
                        exit;
                    } 
                }

                //��ʧ�ǡ�������ä�
                $sql  = "DELETE FROM t_payout_h WHERE buy_id = $get_buy_id;";

                $result = Db_Query($conn, $sql);
                if($result === false){
                    Db_Query($conn, "ROLLBACK;");
                    exit;
                }

                //�ֻ����إå��ơ��֥�סֻ����ǡ����ơ��֥�פ��礷�����ξ�������
                $sql  = "SELECT";
                $sql .= "   t_buy_h.ware_id,";
                $sql .= "   t_buy_d.goods_id,";
                $sql .= "   SUM(t_buy_d.num)";
                $sql .= " FROM";
                $sql .= "   t_buy_h INNER JOIN t_buy_d ON t_buy_h.buy_id = t_buy_d.buy_id";
                $sql .= " WHERE";
                $sql .= "   t_buy_h.buy_id = $get_buy_id";
                $sql .= " GROUP BY t_buy_h.ware_id, t_buy_d.goods_id, t_buy_d.line";
                $sql .= " ORDER BY t_buy_d.line";
                $sql .= ";";

                $result = Db_Query($conn, $sql);
                $count = pg_num_rows($result);

                for($i = 0; $i < $count; $i++){
                    $before_data = pg_fetch_array($result,$i,PGSQL_NUM);     
                }
     
                //�ֻ����إå��ơ��֥�פβ����ξ���򹹿�
                $insert_sql  = "UPDATE";
                $insert_sql .= "    t_buy_h";
                $insert_sql .= " SET";
                $insert_sql .= "    buy_no = '$buy_no',";
                $insert_sql .= "    buy_day = '".$buy_date["y"]."-".$buy_date["m"]."-".$buy_date["d"]."',";
                $insert_sql .= "    arrival_day = '".$arrival_date["y"]."-".$arrival_date["m"]."-".$arrival_date["d"]."',";
                $insert_sql .= "    client_id = $client_id,";
                $insert_sql .= ($direct != null) ? " direct_id = $direct," : " direct_id = NULL, ";
                $insert_sql .= "    trade_id = '$trade',";
                $insert_sql .= "    note = '$note',";
                $insert_sql .= "    c_staff_id = $buy_staff,";
                $insert_sql .= ($order_staff != null) ? " oc_staff_id = $order_staff," : " oc_staff_id = null,";
                $insert_sql .= "    ware_id = $ware,";
                $insert_sql .= "    e_staff_id = $staff_id,";
                $insert_sql .= "    net_amount = $total_amount_data[0],";
                $insert_sql .= "    tax_amount = $total_amount_data[1],";
                $insert_sql .= ($trade == 25) ? " total_split_num = 2," : " total_split_num = 1,";
                $insert_sql .= "    client_cd1 = (SELECT client_cd1 FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_name = (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    client_name2 = (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= ($direct != null) ? " direct_name = (SELECT direct_name FROM t_direct WHERE direct_id = $direct), " : " direct_name = NULL, ";
                $insert_sql .= ($direct != null) ? " direct_name2 = (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct), " : " direct_name2 = NULL, ";
                $insert_sql .= "    c_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $buy_staff), ";
                $insert_sql .= "    e_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $staff_id), ";
                $insert_sql .= ($order_staff != null) ? " oc_staff_name = (SELECT staff_name FROM t_staff WHERE staff_id = $order_staff), " : "oc_staff_name = NULL,";
                $insert_sql .= "    ware_name = (SELECT ware_name FROM t_ware WHERE ware_id = $ware), ";
                $insert_sql .= "    royalty_amount = $royalty_data,";
                $insert_sql .= "    client_cname = (SELECT client_cname FROM t_client WHERE client_id = $client_id),";
                $insert_sql .= "    change_day = CURRENT_TIMESTAMP ";
                $insert_sql .= " WHERE";
                $insert_sql .= "    buy_id = $get_buy_id";
                $insert_sql .= ";";

                $result = Db_Query($conn, $insert_sql);

                //���Ԥ������ϥ�����Хå�
                if($result === false){ 
                    Db_Query($conn, "ROLLBACK");
                    exit;   
                }

                //�ֻ����ǡ����ơ��֥�פ���GET�Ǽ�����������ID�ȥޥå�����ǡ�������
                $insert_sql  = "DELETE FROM";
                $insert_sql .= "    t_buy_d";
                $insert_sql .= " WHERE";
                $insert_sql .= "    buy_id = $get_buy_id";
                $insert_sql .= ";";
                
                $result = Db_Query($conn, $insert_sql);
                //���Ԥ������ϥ�����Хå�
                if($result === false){
                    Db_Query($conn, "ROLLBACK");
                    exit;
                }

                //ʬ���ʧ���ơ��֥����
                $insert_sql  = "DELETE FROM";
                $insert_sql .= "    t_amortization";
                $insert_sql .= " WHERE";
                $insert_sql .= "    buy_id = $get_buy_id";
                $insert_sql .= ";";

                $result = Db_Query($conn, $insert_sql);
                //���Ԥ������ϥ�����Хå�
                if($result === false){
                    Db_Query($conn, "ROLLBACK;");
                    exit;
                }

                //ȯ�����鵯�����Ƥ�����ˤϰ��ٶ�����λ���Ƥ��ʤ�ȯ�����Ф���ȯ���ĥե饰��true�ˤ���
                if($order_id != NULL){
                    $sql  = "UPDATE";
                    $sql .= "   t_order_d \n";
                    $sql .= "SET\n";
                    $sql .= "   rest_flg = 't' \n";
                    $sql .= "WHERE\n";
                    $sql .= "   ord_id = $order_id\n";
                    $sql .= "   AND\n";
                    $sql .= "   finish_flg = 'f'\n";
                    $sql .= ";";

                    $result = Db_Query($conn, $sql);
                    if($result === false){
                        Db_Query($conn, "ROLLBACK;");
                        exit;
                    }
                }       

            //������ID���ʤ����
            }else{
                if($order_id != null){
                    //ȯ�����������Ƥ��ʤ������ǧ
                    $update_check_flg = Update_Check($conn, "t_order_h", "ord_id", $order_id, $_POST["hdn_ord_enter_day"]);
                    //���˺������Ƥ������
                    if($update_check_flg === false){
                        header("Location:./2-3-205.php?buy_id=0&input_flg=true&del_ord_flg=true");
                        exit;
                    }

                    //ȯ�����ѹ�����Ƥ��ʤ�����ǧ
                    $update_data_check_flg = Update_Data_Check($conn, "t_order_h", "ord_id", $order_id, $_POST["hdn_ord_change_day"]);
                    if($update_data_check_flg === false){
                        header("Location:./2-3-205.php?buy_id=0&input_flg=true&change_ord_flg=true");
                        exit;
                    }
                }

                $insert_sql  = "INSERT INTO t_buy_h (";
                $insert_sql .= "    buy_id,";                                                                   //������ID
                $insert_sql .= "    buy_no,";                                                                   //�����ֹ�

                if($order_id != null){
                    $insert_sql .= "    ord_id,";                                                               //ȯ��ID
                }

                $insert_sql .= "    buy_day,";                                                                  //������
                $insert_sql .= "    arrival_day,";                                                              //������
                $insert_sql .= "    client_id,";                                                                //������ID

                if($direct != null){
                    $insert_sql .= "    direct_id,";                                                            //ľ����ID
                }

                $insert_sql .= "    trade_id,";                                                                 //�����CD
                $insert_sql .= "    note,";                                                                     //����
                $insert_sql .= "    c_staff_id,";                                                               //����ô����ID
                $insert_sql .= "    ware_id,";                                                                  //�Ҹ�ID
                $insert_sql .= "    e_staff_id,";                                                               //���ϼ�ID
                $insert_sql .= "    shop_id,";                                                                  //����å�ID
                //$insert_sql .= "    shop_gid,";                                                               //FC���롼��ID
                $insert_sql .= "    net_amount,";
                $insert_sql .= "    tax_amount,";
                $insert_sql .= ($order_staff != null) ? " oc_staff_id," : null;
                $insert_sql .= "    total_split_num,";
                $insert_sql .= "    client_cd1, ";
                $insert_sql .= "    client_name, ";
                $insert_sql .= "    client_name2, ";
                $insert_sql .= ($direct != null) ? " direct_name, " : null;
                $insert_sql .= ($direct != null) ? " direct_name2, " : null;
                $insert_sql .= "    c_staff_name, ";
                $insert_sql .= "    e_staff_name, ";
                $insert_sql .= ($order_staff != null) ? " oc_staff_name, " : null;
                $insert_sql .= "    ware_name ,";
                $insert_sql .= "    royalty_amount,";
                $insert_sql .= "    client_cname,";
                $insert_sql .= "    buy_div ";
                $insert_sql .= ")VALUES(";
                $insert_sql .= "    (SELECT COALESCE(MAX(buy_id), 0)+1 FROM t_buy_h),";                         //������ID
                $insert_sql .= "    '$buy_no',";                                                                //�����ֹ�

                if($order_id != null){
                    $insert_sql .= "    $order_id,";                                                            //ȯ��ID
                }

                $insert_sql .= "    '".$buy_date["y"]."-".$buy_date["m"]."-".$buy_date["d"]."',";               //������
                $insert_sql .= "    '".$arrival_date["y"]."-".$arrival_date["m"]."-".$arrival_date["d"]."',";   //����ͽ����
                $insert_sql .= "    $client_id,";                                                               //������ID

                if($direct != null){
                    $insert_sql .= "    $direct,";                                                              //ľ����ID
                }

                $insert_sql .= "    '$trade',";                                                                 //�����ʬ
                $insert_sql .= "    '$note',";                                                                  //����
                $insert_sql .= "    $buy_staff,";                                                               //����ô����ID
                $insert_sql .= "    $ware,";                                                                    //�Ҹ�ID
                $insert_sql .= "    $staff_id,";                                                                //���ϼ�ID
                $insert_sql .= "    $shop_id,";                                                                 //����å�ID
                //$insert_sql .= "    $shop_gid,";                                                              //FC���롼��ID
                $insert_sql .= "    $total_amount_data[0],";
                $insert_sql .= "    $total_amount_data[1],";
                $insert_sql .= ($order_staff != Null) ? " $order_staff," : null;
                $insert_sql .= ($trade == 25) ? "    2," : "    1,";
                $insert_sql .= "    (SELECT client_cd1 FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    (SELECT client_name FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= "    (SELECT client_name2 FROM t_client WHERE client_id = $client_id), ";
                $insert_sql .= ($direct != null) ? " (SELECT direct_name FROM t_direct WHERE direct_id = $direct), " : null;
                $insert_sql .= ($direct != null) ? " (SELECT direct_name2 FROM t_direct WHERE direct_id = $direct), " : null;
                $insert_sql .= "    (SELECT staff_name FROM t_staff WHERE staff_id = $buy_staff), ";
                $insert_sql .= "    (SELECT staff_name FROM t_staff WHERE staff_id = $staff_id), ";
                $insert_sql .= ($order_staff != Null) ? " (SELECT staff_name FROM t_staff WHERE staff_id = $order_staff), " : null;
                $insert_sql .= "    (SELECT ware_name FROM t_ware WHERE ware_id = $ware), ";
                $insert_sql .= "    $royalty_data,";
                $insert_sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id),";
                $insert_sql .= "    '1'";
                $insert_sql .= ");";

                $result = Db_Query($conn, $insert_sql);
                //���Ԥ������ϥ�����Хå�
                if($result === false){ 
                    $err_message = pg_last_error();
                    $err_format = "t_buy_h_buy_no_key";

                    Db_Query($conn, "ROLLBACK");

                    //ȯ��NO����ʣ�������            
                    if(strstr($err_message,$err_format) !== false){
                        $error = "Ʊ���˻�����Ԥä����ᡢ��ɼ�ֹ椬��ʣ���ޤ������⤦���ٻ�����ԤäƤ���������";
     
                        //����ȯ��NO���������
                        $sql  = "SELECT ";
                        $sql .= "   MAX(buy_no)";
                        $sql .= " FROM";
                        $sql .= "   t_buy_h";
                        $sql .= " WHERE";
                        $sql .= "   shop_id = $shop_id";
                        $sql .= ";"; 

                        $result = Db_Query($conn, $sql);
                        $buy_no = pg_fetch_result($result, 0 ,0);
                        $buy_no = $buy_no +1;
                        $buy_no = str_pad($buy_no, 8, 0, STR_PAD_LEFT);

                        $err_data["form_buy_no"] = $buy_no;

                        $form->setConstants($err_data);

                        $duplicate_err = true;
                    }else{  
                        exit;   
                    }       
                }         

            }

            if($duplicate_err != true){
                //����ID�����
                $sql  = "SELECT";
                $sql .= "   buy_id";
                $sql .= " FROM";
                $sql .= "   t_buy_h";
                $sql .= " WHERE";
                $sql .= "   buy_no = '$buy_no'";
                $sql .= "   AND";
                $sql .= "   shop_id = $shop_id";
                $sql .= ";";

                $result = Db_Query($conn, $sql);
                $buy_id = pg_fetch_result($result,0,0);

                //�����ʧ�ơ��֥����Ͽ
                if($trade == "25"){
                    $division_array = Division_Price($conn, $client_id, ($total_amount_data[0] + $total_amount_data[1]), $buy_date["y"], $buy_date["m"], 2);

                    for($k=0;$k<2;$k++){
                        $sql  = "INSERT INTO t_amortization (";
                        $sql .= "   amortization_id,";
                        $sql .= "   buy_id,";
                        $sql .= "   pay_day,";
                        $sql .= "   split_pay_amount ";
                        $sql .= ") VALUES (";
                        $sql .= "   (SELECT COALESCE(MAX(amortization_id),0)+1 FROM t_amortization),";
                        $sql .= "   $buy_id,";
                        $sql .= "   '".$division_array[1][$k]."', ";
                        $sql .= "   ".$division_array[0][$k]." ";
                        $sql .= ");";

                        $result = Db_Query($conn, $sql);
                        if($resutlt === false){
                            Db_Query($conn, "ROLLBACK");
                            exit;
                        }
                    }
                }

                //���Ϥ��줿���ʤο������֤�
                for($i = 0; $i < count($goods_id); $i++){

                    //��
                    $line = $i + 1;

                    //���������ȴ���������ǳۡ������ǹ�פ򻻽�
                    $price = $buy_price[$i]["i"].".".$buy_price[$i]["d"];

                    //�������
                    $buy_amount = bcmul($price, $buy_num[$i],3);

                    $buy_amount = Coax_Col($coax, $buy_amount);

    //                $data = Total_Amount($buy_amount, $tax_div[$i], $coax, $tax_franct, $tax_rate);
    //                $tax_price = $data[1];

                    //�����ǡ����ơ��֥�
                    $insert_sql  = "INSERT INTO t_buy_d (";
                    $insert_sql .= "    buy_d_id,";
                    $insert_sql .= "    buy_id,";
                    $insert_sql .= "    line,";
                    $insert_sql .= "    goods_id,";
                    $insert_sql .= "    goods_name,";
                    $insert_sql .= "    num,";
                    $insert_sql .= "    tax_div,";
                    $insert_sql .= "    buy_price,";
                    $insert_sql .= "    buy_amount,";
    //                $insert_sql .= "    tax_amount,";
                    $insert_sql .= "    ord_d_id, ";
                    $insert_sql .= "    goods_cd, ";
                    $insert_sql .= "    in_num,";
                    $insert_sql .= "    royalty ";
                    $insert_sql .= ")VALUES(";
                    $insert_sql .= "    (SELECT COALESCE(MAX(buy_d_id), 0)+1 FROM t_buy_d),";
                    $insert_sql .= "    $buy_id,";
                    $insert_sql .= "    $line,";
                    $insert_sql .= "    $goods_id[$i],";
                    $insert_sql .= "    '$goods_name[$i]',";
                    $insert_sql .= "    $buy_num[$i],";
                    $insert_sql .= "    $tax_div[$i],";
                    $insert_sql .= "    $price,";
                    $insert_sql .= "    $buy_amount,";
    //                $insert_sql .= "    $tax_price,";
                    $insert_sql .= "    $order_d_id[$i],";
                    $insert_sql .= "    (SELECT goods_cd FROM t_goods WHERE goods_id = $goods_id[$i]), ";
                    $insert_sql .= "    (SELECT in_num FROM t_goods WHERE goods_id = $goods_id[$i]), ";
                    $insert_sql .= "    '$royalty[$i]'\n";
                    $insert_sql .= ");";

                    $result = Db_Query($conn, $insert_sql);
                    //���Ԥ������ϥ�����Хå�
                    if($result === false){
                        Db_Query($conn, "ROLLBACK");
                        exit;
                    }

                    //�����ǡ���ID�����
                    $sql  = "SELECT";
                    $sql .= "   buy_d_id";
                    $sql .= " FROM";
                    $sql .= "   t_buy_d";
                    $sql .= " WHERE";
                    $sql .= "   buy_id = $buy_id";
                    $sql .= "   AND";
                    $sql .= "   line = $line";
                    $sql .= ";";

                    $result = Db_Query($conn, $sql);
                    $buy_d_id = pg_fetch_result($result,0,0);

                    //�߸˿��ơ��֥�
                    //����ȯ��ID<>NULL�ξ��
                    if($order_id != NULL){

                        //�߸˼���ʧ���ơ��֥�
                        //ȯ���ĺ��
                        $insert_sql  = "INSERT INTO t_stock_hand (";
                        $insert_sql .= "    goods_id,";
                        $insert_sql .= "    enter_day,";
                        $insert_sql .= "    work_day,";
                        $insert_sql .= "    work_div,";
                        $insert_sql .= "    client_id,";
                        $insert_sql .= "    ware_id,";
                        $insert_sql .= "    io_div,";
                        $insert_sql .= "    num,";
                        $insert_sql .= "    slip_no,";
                        $insert_sql .= "    buy_d_id,";
                        $insert_sql .= "    staff_id,";
                        $insert_sql .= "    shop_id,";
                        $insert_sql .= "    client_cname";
                        $insert_sql .= ")VALUES(";
                        $insert_sql .= "    $goods_id[$i],";
                        $insert_sql .= "    NOW(),";
                        $insert_sql .= "    '".$arrival_date["y"]."-".$arrival_date["m"]."-".$arrival_date["d"]."',";
                        $insert_sql .= "    '3',";
                        $insert_sql .= "    $client_id,";
                        $insert_sql .= "    $ware,";
                        $insert_sql .= "    '2',";
//                        $insert_sql .= "    $order_num[$i] - $buy_num[$i],";
                        $insert_sql .= "    $buy_num[$i],";
                        $insert_sql .= "    '$buy_no',";
                        $insert_sql .= "    $buy_d_id,";
                        $insert_sql .= "    $staff_id,";
                        $insert_sql .= "    $shop_id,";
                        $insert_sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id)";
                        $insert_sql .= ");";

                        $result = Db_Query($conn, $insert_sql);
                        //���Ԥ������ϥ�����Хå�
                        if($result === false){
                            Db_Query($conn,"ROLLBACK");
                            exit;
                        } 

                        //����ȯ��
                        $insert_sql  = "INSERT INTO t_stock_hand (";
                        $insert_sql .= "    goods_id,";
                        $insert_sql .= "    enter_day,";
                        $insert_sql .= "    work_day,";
                        $insert_sql .= "    work_div,";
                        $insert_sql .= "    client_id,";
                        $insert_sql .= "    ware_id,";
                        $insert_sql .= "    io_div,";
                        $insert_sql .= "    num,";
                        $insert_sql .= "    slip_no,";
                        $insert_sql .= "    buy_d_id,";
                        $insert_sql .= "    staff_id,";
                        $insert_sql .= "    shop_id,";
                        $insert_sql .= "    client_cname";
                        $insert_sql .= ")VALUES(";
                        $insert_sql .= "    $goods_id[$i],";
                        $insert_sql .= "    NOW(),";
                        $insert_sql .= "    '".$arrival_date["y"]."-".$arrival_date["m"]."-".$arrival_date["d"]."',";
                        $insert_sql .= "    '4',";
                        $insert_sql .= "    $client_id,";
                        $insert_sql .= "    $ware,";
                        $insert_sql .= "    '1',";
                        $insert_sql .= "    $buy_num[$i],";
                        $insert_sql .= "    '$buy_no',";
                        $insert_sql .= "    $buy_d_id,";
                        $insert_sql .= "    $staff_id,";
                        $insert_sql .= "    $shop_id,";
                        $insert_sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id)";
                        $insert_sql .= ");";

                        $result = Db_Query($conn, $insert_sql);
                        //���Ԥ������ϥ�����Хå�
                        if($result === false){
                            Db_Query($conn, "ROLLBACK");
                            exit;
                        }

                    //����ȯ��ID��NULL��AND�������ʬ�ᣲ���ʳݻ����ˡ������ʸ�������ˤξ��
                    }elseif($order_id == NULL && ($trade == '21' || $trade == '71' || $trade == '25')){
            
                        //�߸˼���ʧ���ơ��֥�ʻ���ȯ����
                        $insert_sql  = "INSERT INTO t_stock_hand (";
                        $insert_sql .= "    goods_id,";
                        $insert_sql .= "    enter_day,";
                        $insert_sql .= "    work_day,";
                        $insert_sql .= "    work_div,";
                        $insert_sql .= "    client_id,";
                        $insert_sql .= "    ware_id,";
                        $insert_sql .= "    io_div,";
                        $insert_sql .= "    num,";
                        $insert_sql .= "    slip_no,";
                        $insert_sql .= "    buy_d_id,";
                        $insert_sql .= "    staff_id,";
                        $insert_sql .= "    shop_id,";
                        $insert_sql .= "    client_cname";
                        $insert_sql .= " )VALUES(";
                        $insert_sql .= "    $goods_id[$i],";
                        $insert_sql .= "    NOW(),";
                        $insert_sql .= "    '".$arrival_date["y"]."-".$arrival_date["m"]."-".$arrival_date["d"]."',";
                        $insert_sql .= "    '4',";
                        $insert_sql .= "    $client_id,";
                        $insert_sql .= "    $ware,";
                        $insert_sql .= "    '1',";
                        $insert_sql .= "    $buy_num[$i],";
                        $insert_sql .= "    '$buy_no',";
                        $insert_sql .= "    $buy_d_id,";
                        $insert_sql .= "    $staff_id,";
                        $insert_sql .= "    $shop_id,";
                        $insert_sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id)";
                        $insert_sql .= ");";

                        $result = Db_Query($conn, $insert_sql);
                        //���Ԥ������ϥ�����Хå�
                        if($result === false){
                            Db_Query($conn, "ROLLBACK");
                            exit;
                        }

                    //����ȯ��ID��NULL��AND�������ʬ�ᣲ���ʳ����ʡˡ������ʸ������ʡˤξ��
                    }elseif($order_id == NULL && ($trade == '23' || $trade == '73')){

                        //�߸˼���ʧ���ʻ���ȯ����
                        $insert_sql  = "INSERT INTO t_stock_hand (\n";
                        $insert_sql .= "    goods_id,\n";
                        $insert_sql .= "    enter_day,\n";
                        $insert_sql .= "    work_day,\n";
                        $insert_sql .= "    work_div,\n";
                        $insert_sql .= "    client_id,\n";
                        $insert_sql .= "    ware_id,\n";
                        $insert_sql .= "    io_div,\n";
                        $insert_sql .= "    num,\n";
                        $insert_sql .= "    slip_no,\n"; 
                        $insert_sql .= "    buy_d_id,\n";
                        $insert_sql .= "    staff_id,\n";
                        $insert_sql .= "    shop_id,\n";
                        $insert_sql .= "    client_cname\n";
                        $insert_sql .= ")VALUES(\n";
                        $insert_sql .= "    $goods_id[$i],\n";
                        $insert_sql .= "    NOW(),\n";
                        $insert_sql .= "    '".$arrival_date["y"]."-".$arrival_date["m"]."-".$arrival_date["d"]."',\n";
                        $insert_sql .= "    '4',\n";
                        $insert_sql .= "    $client_id,\n";
                        $insert_sql .= "    $ware,\n";
                        $insert_sql .= "    '2',\n";
                        $insert_sql .= "    $buy_num[$i],\n";
                        $insert_sql .= "    '$buy_no',\n";
                        $insert_sql .= "    $buy_d_id,\n";
                        $insert_sql .= "    $staff_id,\n";
                        $insert_sql .= "    $shop_id,\n";
                        $insert_sql .= "    (SELECT client_cname FROM t_client WHERE client_id = $client_id)";
                        $insert_sql .= ");\n";

                        $result = Db_Query($conn, $insert_sql);
                        //���Ԥ������ϥ�����Хå�
                        if($result === false){
                            Db_Query($conn,"ROLLBACK");
                            exit;
                        }

                    }

                    //ȯ���򵯤������Ƥ����ΤϽ�������
                    if($order_id != NULL){
                        //ȯ���إå��ơ��֥�ʽ�����������������
                        $sql  = "UPDATE";
                        $sql .=     " t_order_d";
                        $sql .= " SET";
                        $sql .= "    rest_flg='f' ";
                        $sql .= " FROM";
                        $sql .= "   (SELECT";
                        $sql .= "        ord_d_id,";
                        $sql .= "        SUM(num) AS buy_num ";
                        $sql .= "   FROM";
                        $sql .= "       t_buy_d";
                        $sql .= "   GROUP BY ord_d_id";
                        $sql .= "   ) AS t_buy_d";
                        $sql .= " WHERE ";
                        $sql .= "   t_order_d.ord_d_id = t_buy_d.ord_d_id";
                        $sql .= "   AND ";
                        $sql .= "   t_order_d.ord_d_id = $order_d_id[$i]";
                        $sql .= "   AND ";
                        $sql .= "   t_order_d.num <= t_buy_d.buy_num";
                        $sql .= ";";

                        $result = Db_Query($conn, $sql);
                        //���Ԥ������ϥ�����Хå�
                        if($result === false){
                            Db_Query($conn, "ROLLBACK");
                            exit;
                        }

                        //���������������ȯ������Ķ���Ƥ��ʤ�����ǧ    
                        //������ǽ��������
                        $sql  = "SELECT\n";
                        $sql .= "    t_order_h.num - COALESCE(t_buy_h.num, 0) AS num \n";
                        $sql .= "FROM\n";
                        $sql .= "    (SELECT\n";
                        $sql .= "        num,\n";
                        $sql .= "        ord_d_id \n";
                        $sql .= "    FROM\n";
                        $sql .= "        t_order_d\n";
                        $sql .= "    WHERE\n";
                        $sql .= "        t_order_d.ord_d_id = $order_d_id[$i]\n";
                        $sql .= "    ) AS t_order_h\n";
                        $sql .= "        LEFT JOIN\n";
                        $sql .= "    (SELECT\n";
                        $sql .= "        SUM(num) AS num,\n";
                        $sql .= "        ord_d_id \n";
                        $sql .= "    FROM\n";
                        $sql .= "        t_buy_h\n";
                        $sql .= "            INNER JOIN\n";
                        $sql .= "        t_buy_d\n";
                        $sql .= "        ON t_buy_h.buy_id = t_buy_d.buy_id\n";
                        $sql .= "    WHERE\n";
                        $sql .= "        t_buy_d.ord_d_id = $order_d_id[$i]\n";
                        $sql .= "        AND\n";
                        $sql .= "        t_buy_h.buy_id <> $buy_id\n";
                        $sql .= "    GROUP BY ord_d_id\n";
                        $sql .= "    ) AS t_buy_h\n";
                        $sql .= "    ON t_order_h.ord_d_id = t_buy_h.ord_d_id\n";
                        $sql .= ";\n";

                        $result = Db_Query($conn, $sql);
                        $buy_ord_num = pg_fetch_result($result,0,0);

                        //��������ȯ��������äƤ�����
                        if($buy_ord_num < 0){
                            Db_Query($conn, "ROLLBACK;");
                            $rollback_flg = true;
                            $buy_ord_num_err = "��������ȯ������Ķ���Ƥ��ޤ���";
                            break;
                        }
                    }
                }

                //������Хå��ե饰��true�ǤϤʤ����
                if($rollback_flg != true){     
                    //ȯ���򵯤����Ƥ����ΤϽ�������
                    if($order_id != NULL){
                        //����������������
                        $ary_order_d_id = implode(",",$order_d_id);

                        $sql  = "SELECT";
                        $sql .= "   rest_flg";
                        $sql .= " FROM";
                        $sql .= "   t_order_d";
                        $sql .= " WHERE";
                        $sql .= "   ord_d_id IN ($ary_order_d_id)";
                        $sql .= ";";

                        $result = Db_Query($conn, $sql);
                        $data_num = pg_num_rows($result);
                        for($i = 0; $i < $data_num; $i++){
                            $rest_data[] = pg_fetch_result($result,$i,0);
                        }

                        //ȯ���������Ƥξ��ʤ�ȯ���ĥե饰���ξ��
                        if(in_array('t',$rest_data)){
                            $insert_sql  = "UPDATE";
                            $insert_sql .= "    t_order_h";
                            $insert_sql .= " SET";
                            $insert_sql .= "    ps_stat = '2'";
                            $insert_sql .= " WHERE";
                            $insert_sql .= "    ord_id = $order_id";
                            $insert_sql .= ";";
                        //�嵭�ʳ��ξ�硡
                        }else{
                            $insert_sql  = "UPDATE";
                            $insert_sql .= "    t_order_h";
                            $insert_sql .= " SET";
                            $insert_sql .= "    ps_stat = '3'";
                            $insert_sql .= " WHERE";
                            $insert_sql .= "    ord_id = $order_id";
                            $insert_sql .= ";";
                        }

                        $result = Db_Query($conn, $insert_sql);
                        //���Ԥ������ϥ�����Хå�
                        if($result === false){
                            Db_Query($conn, "ROLLBACK");
                            exit;
                        }
                    }

                    //�������μ�ư��ʧ����
                    //�����ʬ�᣷���ʸ�������ˡ���3�ʸ����Ͱ��ˡ���4�ʸ������ʡˤξ��Τ߽�������
                    if($trade == '71' || $trade == '73' || $trade == '74'){

                        //��ʧ�ֹ�κ����ͤ����
                        //ľ�Ĥξ��
                        if($group_kind == '2'){
                            $sql  = "SELECT";
                            $sql .= "   MAX(pay_no)";
                            $sql .= "FROM";
                            $sql .= "   t_payout_no_serial";
                            $sql .= ";";

                            $result = Db_Query($conn, $sql);
                            $pay_no = pg_fetch_result($result, 0,0);
                            $pay_no = $pay_no + 1;
                            $pay_no = str_pad($pay_no, 8, 0, STR_PAD_LEFT); 

                            $sql  = "INSERT INTO t_payout_no_serial(";
                            $sql .= "   pay_no";
                            $sql .= ")VALUES(";
                            $sql .= "   '$pay_no'";
                            $sql .= ");";

                            $result = Db_Query($conn, $sql);
                            //��ʣ��������
                            if($result === false){

                                $err_message = pg_last_error();
                                $err_format = "t_payout_no_serial_pkey";
                                $err_flg = true;
                                Db_Query($conn, "ROLLBACK;");

                                if(strstr($err_message, $err_format) != false){
                                    $duplicate_msg = "��ʧ��Ʊ���˹Ԥʤ�줿���ᡢ��ɼ�ֹ�����֤˼��Ԥ��ޤ�����";
                                    $duplicate_flg = true;
                                }else{
                                    exit;
                                }
                            }

                        //ľ�İʳ��ξ��
                        }else{

                            $sql  = "SELECT";
                            $sql .= "   MAX(pay_no)";
                            $sql .= " FROM";
                            $sql .= "   t_payout_h";
                            $sql .= " WHERE";
                            $sql .= "   shop_id = $shop_id";
                            $sql .= ";";

                            $result = Db_Query($conn, $sql);

                            $pay_no = pg_fetch_result($result, 0,0);
                            $pay_no = $pay_no + 1;
                            $pay_no = str_pad($pay_no, 8, 0, STR_PAD_LEFT); 
                        }

                        $sql  = "INSERT INTO t_payout_h(\n";
                        $sql .= "   pay_id,\n";               //��ʧID
                        $sql .= "   pay_no,\n";               //��ʧ�ֹ�
                        $sql .= "   pay_day,\n";              //��ʧ��
                        $sql .= "   client_id,\n";            //������ID
                        $sql .= "   client_name,\n";          //������̾
                        $sql .= "   client_name2,\n";         //������̾��
                        $sql .= "   client_cname,\n";         //�������ά�Ρ�
                        $sql .= "   client_cd1,\n";           //�����襳����
                        $sql .= "   e_staff_id,\n";           //���ϼ�ID
                        $sql .= "   e_staff_name,\n";         //���ϼ�̾
                        $sql .= "   c_staff_id,\n";           //ô����ID
                        $sql .= "   c_staff_name,\n";         //ô����̾
                        $sql .= "   input_day,\n";            //������
                        $sql .= "   buy_id,\n";               //����ID
                        $sql .= "   shop_id\n";               //����å�ID
                        $sql .= ")VALUES(\n";
                        $sql .= "   (SELECT COALESCE(MAX(pay_id), 0)+1 FROM t_payout_h),\n";
                        $sql .= "   '$pay_no',\n";
                        $sql .= "   '".$buy_date["y"]."-".$buy_date["m"]."-".$buy_date["d"]."',\n";
                        $sql .= "   $client_id,\n";
                        $sql .= "   (SELECT client_name FROM t_client WHERE client_id = $client_id),\n";
                        $sql .= "   (SELECT client_name2 FROM t_client WHERE client_id = $client_id),\n";
                        $sql .= "   (SELECT client_cname FROM t_client WHERE client_id = $client_id), \n";
                        $sql .= "   (SELECT client_cd1 FROM t_client WHERE client_id = $client_id),\n";
                        $sql .= "   $staff_id,\n";
                        $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $staff_id), \n";
                        $sql .= "   $buy_staff,\n";
                        $sql .= "   (SELECT staff_name FROM t_staff WHERE staff_id = $buy_staff),\n";
                        $sql .= "   NOW(), \n";
                        $sql .= "   $buy_id, \n";
                        $sql .= "   $shop_id \n";
                        $sql .= "); \n";

                        $result = Db_Query($conn, $sql);

                        //���Ԥ������ϥ�����Хå�
                        if($result === false){
                            $err_message = pg_last_error();
                            $err_format = "t_payout_h_pay_no_key";
                            $err_flg = true;
                            Db_Query($conn, "ROLLBACK;");

                            //��ʣ�������
                            if(strstr($err_message, $err_format) != false){
                                $duplicate_msg = "��ʧ��Ʊ���˹Ԥʤ�줿���ᡢ��ɼ�ֹ�����֤˼��Ԥ��ޤ�����";
                                $duplicate_flg = true;
                            }else{
                                exit;
                            }
                        }

                        if($duplicate_flg != true){
                            //��Ͽ������ʧ�إå�ID�����
                            $sql  = "SELECT";
                            $sql .= "   pay_id ";
                            $sql .= "FROM";
                            $sql .= "   t_payout_h ";
                            $sql .= "WHERE";
                            $sql .= "   pay_no = '$pay_no'";
                            $sql .= "   AND";
                            $sql .= "   shop_id = $shop_id";
                            $sql .= ";";

                            $result = Db_Query($conn, $sql);
                            $pay_id = pg_fetch_result($result, 0,0);

                            //��ʧ���ǡ����ơ��֥����Ͽ
                            $sql  = "INSERT INTO t_payout_d (";
                            $sql .= "   pay_d_id,";
                            $sql .= "   pay_id,";
                            $sql .= "   trade_id,";
                            $sql .= "   pay_amount";
                            $sql .= ")VALUES(";
                            $sql .= "   (SELECT COALESCE(MAX(pay_d_id),0)+1 FROM t_payout_d),";
                            $sql .= "   $pay_id,";
                            $sql .= "   '49',";
                            //�����ʬ�����������ʧ�ξ��
                            if($trade == '71'){
                                $sql .= "   $total_amount_data[2]";
                            //�����ʬ�������������ξ��
                            }else{
                                $sql .= "   $total_amount_data[2]*-1";
                            }
                            $sql .= ");";

                            $result = Db_Query($conn, $sql);
                            if($result === false){
                                Db_Query($conn, "ROLLBACK;");
                                exit;
                            }
                        }
                    }

                    Db_Query($conn, "COMMIT");
                    header("Location:./2-3-205.php?buy_id=$buy_id&input_flg=true");
                }
            }
        }else{
            //��Ͽ��ǧ���̤�ɽ���ե饰
            $comp_flg = true;
        }
    }
}

/****************************/
//�ե������������ư��
/****************************/
//���ֹ楫����
$row_num = 1;

//�����褬���򤵤�Ƥ��ʤ���������Ͽ��ǧ�ξ��ϥե꡼��
if($client_search_flg != true || $stock_search_flg != true || $comp_flg == true){
    //aoyama-n 2009-09-16
    /********************
    $style = "color : #000000;
            border : #ffffff 1px solid;
            background-color: #ffffff;";
    ********************/
    $style = "border : #ffffff 1px solid; background-color: #ffffff;";
    $type = "readonly";
}else{
    $type = $g_form_option;
}

for($i = 0; $i < $max_row; $i++){
    //ɽ����Ƚ��
    if(!in_array("$i", $del_history)){
        $del_data = $del_row.",".$i;

        //aoyama-n 2009-09-16
        //�Ͱ����ʵڤӼ����ʬ���Ͱ������ʤξ����ֻ���ɽ��
        $font_color = "";
        $form_trade       = $form->getElementValue("form_trade");
        $hdn_discount_flg = $form->getElementValue("hdn_discount_flg[$i]");
       
        if($hdn_discount_flg === 't' ||
           $form_trade[0] == '23' || $form_trade[0] == '24' || $form_trade[0] == '73' || $form_trade[0] == '74'){
            $font_color = "color: red; ";
        }else{
            $font_color = "color: #000000; ";
        }

        //ȯ��ID
        $form->addElement("hidden","hdn_order_d_id[$i]");

        //����ID
        $form->addElement("hidden","hdn_goods_id[$i]");

        //���ʥ�����
        //aoyama-n 2009-09-16
        if($freeze_flg == true){
            $form->addElement("text","form_goods_cd[$i]","",
            "style=\"$font_color
             border : #ffffff 1px solid;
             background-color: #ffffff;
             text-align: left\" readonly'
            ");
        }else{
            $form->addElement(
                "text","form_goods_cd[$i]","","size=\"10\" maxLength=\"8\"
                style=\"$font_color $style $g_form_style \" $type 
                onChange=\"return goods_search_2(this.form, 'form_goods_cd', 'goods_search_row', $i ,$row_num)\""
            );
        }
        //����̾
        //aoyama-n 2009-09-16
        if($name_change[$i] == '2'){
            /***************
            if($comp_flg == true){
                $freeze_style  = "style=\"";
                $freeze_style .= "color : #000000;";
                $freeze_style .= "border : #ffffff 1px solid;";
                $freeze_style .= "background-color: #ffffff;\"";
            }
            ***************/
            $form->addElement(
                "text","form_goods_name[$i]","",
                "size=\"54\" style=\"$font_color $style\" $g_text_readonly"
            );
        }else{
            $form->addElement(
                "text","form_goods_name[$i]","",
                "size=\"54\" maxLength=\"41\" 
                 style=\"$font_color $style\" $type"
            );
        }
        $form->addElement("hidden","hdn_name_change[$i]","","");
        $form->addElement("hidden","hdn_stock_manage[$i]","","");

        //����
        //aoyama-n 2009-09-16
        $form->addElement("text","form_in_num[$i]","",
            "size=\"11\" maxLength=\"9\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" 
            readonly'"
        );
        //��ʸ����
        //aoyama-n 2009-09-16
        $form->addElement("text","form_order_in_num[$i]","",
            "size=\"6\" maxLength=\"5\" 
            onKeyup=\"in_num($i,'hdn_goods_id[$i]','form_buy_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
            style=\"text-align: right; $font_color $style $g_form_style \"
            $type"
        );

        //���߸˿�
        //aoyama-n 2009-09-16
        $form->addElement(
            "text","form_stock_num[$i]","",
            "size=\"11\" maxLength=\"5\" 
            style=\"$font_color
            border : #ffffff 1px solid;
            background-color: #ffffff; text-align: right\" 
            readonly"
        );
/*
        //ȯ����
        $form->addElement(
            "text","form_order_num[$i]","",
            'size="11" maxLength=\"9\" 
            style="color : #000000; 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right" readonly'
        );
*/
        //ȯ����
        $form->addElement(
            "hidden","form_order_num[$i]","",
            'size="11" maxLength=\"9\" 
            style="color : #000000; 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right" readonly'
        );

        //�����ѿ�
        $form->addElement(
            "text","form_rbuy_num[$i]","",
            'size="11" maxLength=\"9\" 
            style="color : #000000; 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right" readonly'
        );

        //������
        //aoyama-n 2009-09-16
        $form->addElement("text","form_buy_num[$i]","",
            "size=\"6\" maxLength=\"5\" 
            onKeyup=\"Mult('hdn_goods_id[$i]','form_buy_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');ord_in_num($i);\"
            style=\"text-align: right; $font_color $style $g_form_style \"
            $type"
        );


        //����ñ��
        //�����褬�����ξ���ñ����ե꡼��
/*
        if($head_flg == 't'){
            $form_buy_price[$i][] =& $form->createElement(
                "text","i","",
                "style=\"color : #525552;
                border : #ffffff 1px solid;
                background-color: #ffffff;
                text-align:right\" readonly'
            ");
            $form_buy_price[$i][] =& $form->createElement("static","","",".");
            $form_buy_price[$i][] =& $form->createElement(
                "text","d","","size=\"2\" maxLength=\"2\" 
                style=\"color : #525552;
                border : #ffffff 1px solid;
                background-color: #ffffff;
                text-align:left\" readonly'
            ");
            $form->addGroup( $form_buy_price[$i], "form_buy_price[$i]", "");
        }else{
*/
        //aoyama-n 2009-09-16
            $form_buy_price[$i][] =& $form->createElement(
                "text","i","","size=\"11\" maxLength=\"9\" class=\"money\"
                onKeyup=\"Mult('hdn_goods_id[$i]','form_buy_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
                style=\"text-align: right; $font_color $style $g_form_style \"
                $type"
            );
            $form_buy_price[$i][] =& $form->createElement("static","","",".");
            $form_buy_price[$i][] =& $form->createElement(
                "text","d","","size=\"1\" maxLength=\"2\" 
                onKeyup=\"Mult('hdn_goods_id[$i]','form_buy_num[$i]','form_buy_price[$i][i]','form_buy_price[$i][d]','form_buy_amount[$i]','$coax');\"
                style=\"text-align: left; $font_color $style $g_form_style \"
                $type"
            );
            $form->addGroup( $form_buy_price[$i], "form_buy_price[$i]", "");
//        }

        //���Ƕ�ʬ
        $form->addElement(
            "text","form_tax_div[$i]","",
            "size=\"11\" maxLength=\"9\" 
            style=\"color : #000000; 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );
        $form->addElement("hidden","hdn_tax_div[$i]","","");

        //���(��ȴ��)
        //aoyama-n 2009-09-16
        $form->addElement(
            "text","form_buy_amount[$i]","",
            "size=\"25\" maxLength=\"18\" 
            style=\"$font_color 
            border : #ffffff 1px solid; 
            background-color: #ffffff; 
            text-align: right\" readonly'"
        );

        //ȯ����
        //aoyama-n 2009-09-16
        $form->addElement(
            "text","form_rorder_num[$i]","",
            "size=\"11\" maxLength=\"9\"
            style=\"$font_color
            border : #ffffff 1px solid;
            background-color: #ffffff;
            text-align: right\" readonly'"
        );

        $form->addElement("hidden","hdn_royalty[$i]","","");


        //��Ͽ��ǧ���̤ξ�����ɽ��
        if($comp_flg != true){
            //�������
            $form->addElement(
                "link","form_search[$i]","","#","����",
                "TABINDEX=-1 onClick=\"return Open_SubWin_2('../dialog/2-0-210.php', Array('form_goods_cd[$i]','goods_search_row'), 500, 450,5,$client_id,$i,$row_num);\""
            );

            //������
            //�ǽ��Ԥ��������硢���������κǽ��Ԥ˹�碌��
            if($row_num == $max_row-$del_num){
                $form->addElement(
                    "link","form_del_row[$i]","",
                    "#","<font color='#FEFEFE'>���</font>","TABINDEX=-1 onClick=\"javascript:Dialogue_3('������ޤ���', '$del_data', 'del_row' ,$row_num-1);return false;\""
                );
            //�ǽ��԰ʳ����������硢�������Ԥ�Ʊ��NO�ιԤ˹�碌��
            }else{
                $form->addElement(
                    "link","form_del_row[$i]","",
                    "#","<font color='#FEFEFE'>���</font>","TABINDEX=-1 onClick=\"javascript:Dialogue_3('������ޤ���', '$del_data', 'del_row' ,$row_num);return false;\""
                );
            }
        }
    
        /****************************/
        //ɽ����HTML����
        /****************************/
/*
        $html .= "<tr class=\"Result1\">";
        $html .=    "<A NAME=$row_num><td align=\"right\">$row_num</td></A>";
        $html .=    "<td align=\"left\">";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_cd[$i]"]]->toHtml();
        if($client_search_flg === true && $stock_search_flg === true && $freeze_flg != true && $comp_flg != true){
            $html .=    "��";
            $html .=        $form->_elements[$form->_elementIndex["form_search[$i]"]]->toHtml();
            $html .=    "��";
        }
        $html .=    "<br>";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_name[$i]"]]->toHtml();
        $html .=    "</td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_stock_num[$i]"]]->toHtml();
        $html .=    "<br>";
        $html .=        $form->_elements[$form->_elementIndex["form_order_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_in_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"center\">";
        $html .=        $form->_elements[$form->_elementIndex["form_order_in_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_rbuy_num[$i]"]]->toHtml();
        $html .=    "<br>";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_num[$i]"]]->toHtml();
        $html .=    "</td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_price[$i]"]]->toHtml();
        $html .=    "</td>";

        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_amount[$i]"]]->toHtml();
        $html .=    "</td>";

        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_rorder_num[$i]"]]->toHtml();
        $html .=    "</td>";
        if($client_search_flg === true && $stock_search_flg === true && $freeze_flg != true && $comp_flg != true){
            $html .= "  <td class=\"Title_Add\" align=\"center\">";
            $html .=        $form->_elements[$form->_elementIndex["form_del_row[$i]"]]->toHtml();
            $html .= "  </td>";
        }
        $html .= "</tr>";
*/

        $html .= "<tr class=\"Result1\">";
        $html .=    "<A NAME=$row_num><td align=\"right\">$row_num</td></A>";
        $html .=    "<td align=\"left\">";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_cd[$i]"]]->toHtml();
        if($client_search_flg === true && $stock_search_flg === true && $freeze_flg != true && $comp_flg != true){
            $html .=    "��";
            $html .=        $form->_elements[$form->_elementIndex["form_search[$i]"]]->toHtml();
            $html .=    "��";
        }
        $html .=    "<br>";
        $html .=        $form->_elements[$form->_elementIndex["form_goods_name[$i]"]]->toHtml();
        $html .=    "</td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_stock_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_rorder_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"center\">";
        $html .=        $form->_elements[$form->_elementIndex["form_order_in_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .= "  <td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_in_num[$i]"]]->toHtml();
        $html .= "  </td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_num[$i]"]]->toHtml();
        $html .=    "</td>";
        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_price[$i]"]]->toHtml();
        $html .=    "</td>";

        $html .=    "<td align=\"right\">";
        $html .=        $form->_elements[$form->_elementIndex["form_buy_amount[$i]"]]->toHtml();
        $html .=    "</td>";

        $html .=    "</td>";
        if($client_search_flg === true && $stock_search_flg === true && $freeze_flg != true && $comp_flg != true){
            $html .= "  <td class=\"Title_Add\" align=\"center\">";
            $html .=        $form->_elements[$form->_elementIndex["form_del_row[$i]"]]->toHtml();
            $html .= "  </td>";
        }
        $html .= "</tr>";

        //���ֹ��ܣ�
        $row_num = $row_num+1;
    }
}

//��Ͽ��ǧ���̤Ǥϡ��ʲ��Υܥ����ɽ�����ʤ�
if($comp_flg != true){

    //ɽ���ܥ���
//    if($order_freeze_flg != true){
    if($buy_get_flg != true){
        $form->addElement("button","form_show_button","ɽ����",
                "onClick=\"javascript:Button_Submit('show_button_flg','#','true')\""
        );
    }elseif($order_freeze_flg == true){
        for($i = 0; $i < count($order_freeze); $i++){
            $order_freeze[$i]->freeze();
        }
    }

    //button
    $form->addElement("submit","form_buy_button","������ǧ���̤�", $disabled);
    $form->addElement("button","form_back_button","�ᡡ��","onClick=\"javascript:history.back()\"");
    $form->addElement("button","form_sum_button","�硡��","onClick=\"javascript:Button_Submit('sum_button_flg','#foot','true')\"");

    //��������
    if($order_freeze_flg == true || $buy_get_flg == true){
        $form->addElement("static","form_client_link", "", "������");
    }else{
        $form->addElement("link","form_client_link","","./2-3-102.php","������","
            onClick=\"return Open_SubWin('../dialog/2-0-208.php',Array('form_client[cd]','form_client[name]', 'client_search_flg'),500,450,5,1);\""
        );
    }

    //���ɲå��
    if($client_search_flg === true && $stock_search_flg === true && $freeze_flg != true){
        //���ɲåܥ���
        $form->addElement("button","add_row_link","���ɲ�","onClick=\"javascript:Button_Submit_1('add_row_flg', '#foot', 'true')\"");
    }

}else{
    //��Ͽ��ǧ���̤Ǥϰʲ��Υܥ����ɽ��
    //���
    $form->addElement("button","return_button","�ᡡ��","onClick=\"javascript:history.back()\"");
    
    //OK
    $form->addElement("submit","comp_button","������λ", $disabled);

    //������
    $form->addElement("static","form_client_link", "", "������");
 
    //aoyama-n 2009-09-16
    #$form->freeze();
    //��ǧ���̤ǥإå���ʬ��freeze�ʥǡ�����ʬ��CSS��readonly��
    for($i = 0; $i < count($head_form); $i++){
        $head_form[$i]->freeze();
    }
}

//���åȿ���׻�����
$js  = " function in_num(num,id,order_num,price_i,price_d,amount,coax){\n";
$js .= "    var in_num = \"form_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_in_num = \"form_order_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_num = \"form_buy_num\"+\"[\"+num+\"]\";\n";
$js .= "    var buy_amount = \"form_buy_amount\"+\"[\"+num+\"]\";\n";

$js .= "    var v_in_num = document.dateForm.elements[in_num].value;\n";
$js .= "    var v_ord_in_num = document.dateForm.elements[ord_in_num].value;\n";
$js .= "    var v_ord_num = document.dateForm.elements[ord_num].value;\n";
$js .= "    var v_num = v_in_num * v_ord_in_num;\n";
$js .= "    if(isNaN(v_num) == true){\n";
$js .= "        v_num = \"\";\n";
$js .= "    }\n";

$js .= "    document.dateForm.elements[ord_num].value = v_num;\n";
$js .= "    Mult(id,order_num,price_i,price_d,amount,coax);\n";
$js .= "}\n";

//��ʸ���åȿ���׻�����
$js .= "function ord_in_num(num){\n";
$js .= "    var in_num = \"form_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_in_num = \"form_order_in_num\"+\"[\"+num+\"]\";\n";
$js .= "    var ord_num = \"form_buy_num\"+\"[\"+num+\"]\";\n";
$js .= "    var v_in_num = document.dateForm.elements[in_num].value;\n";
$js .= "    var v_ord_in_num = document.dateForm.elements[ord_in_num].value;\n";
$js .= "    var v_ord_num = document.dateForm.elements[ord_num].value;\n";
$js .= "    var result = v_ord_num % v_in_num;    if(result == 0){\n";
$js .= "        var res = v_ord_num / v_in_num;\n";
$js .= "        document.dateForm.elements[ord_in_num].value = res;\n";
$js .= "    }else{  \n";
$js .= "        document.dateForm.elements[ord_in_num].value = \"\";\n";
$js .= "    }\n";
$js .= "}\n";



/****************************/
// ������ξ��ּ���
/****************************/
$client_state_print = Get_Client_State($conn, $client_id);


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
$page_menu = Create_Menu_f('buy','2');

/****************************/
//���̥إå�������
/****************************/
$page_title .= "��".$form->_elements[$form->_elementIndex[change_button]]->toHtml();
$page_title .= "��".$form->_elements[$form->_elementIndex[new_button]]->toHtml();
$page_header = Create_Header($page_title);

// Render��Ϣ������
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form��Ϣ���ѿ���assign
$smarty->assign('form',$renderer->toArray());

//����¾���ѿ���assign
$smarty->assign('var',array(
    'html_header'       => "$html_header",
    'page_menu'         => "$page_menu",
    'page_header'       => "$page_header",
    'html_footer'       => "$html_footer",
    'html'              => "$html",
    'message'           => "$message",
    'warning'           => "$warning",
    'freeze_flg'        => "$freeze_flg",
    'client_search_flg' => "$client_search_flg",
    'stock_search_flg'  => "$stock_search_flg",
    'form_potision'     => "$form_potision",
    'error'             => "$error",
    'buy_get_flg'       => "$buy_get_flg",
    'goods_twice'       => "$goods_twice",
    'js'                => "$js",
    'duplicate_msg'     => "$duplicate_msg",
    'comp_flg'          => "$comp_flg",
    'ap_balance'        => "$ap_balance",
    'goods_twice'       => "$goods_twice",
    'buy_ord_num_err'   => "$buy_ord_num_err",
    "client_state_print"=> "$client_state_print",
    "finish"			=> "$finish",
));


$smarty->assign("goods_err", $goods_err);
$smarty->assign("price_num_err", $price_num_err);
$smarty->assign("num_err", $num_err);
$smarty->assign("price_err", $price_err);
$smarty->assign("duplicate_goods_err", $duplicate_goods_err);

//�ƥ�ץ졼�Ȥ��ͤ��Ϥ�
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>