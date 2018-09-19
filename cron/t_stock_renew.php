<?php
/*�߸˥ơ��֥�Υǡ�������*/

/******************************
 *  �ѹ�����
 *      ����2009-08-18�˿������� <hashimoto-y>
 *      ����2010-01-21��ȯ���ġ������ʳ��Υǡ�����̵���Ƚ�ʣ�����쥳���ɤ�������Զ�罤�� <hashimoto-y>
 *
*******************************/

//�Ķ�����ե�����
require_once("ENV_local.php");

//DB����³
$conn = Db_Connect();

//����
$today = date("Y-m-d H:i:s");
error_log("$today �߸˥ơ��֥빹���������� \n",3,LOG_FILE);

//���顼�ѿ������
$error_con  = NULL;
$error_time = NULL;
$error_msg  = NULL;
$e_contents = NULL;
$error_flg  = NULL;

/**************************/
//��������ǡ��������
/**************************/
$today = date("Y-m-d");

Db_Query($conn,"BEGIN");

Db_Query($conn,"LOCK TABLE t_stock IN ACCESS EXCLUSIVE MODE");

Db_Query($conn,"TRUNCATE t_stock");

$t_stock_renew_sql = <<<_EOD_

INSERT INTO t_stock
   SELECT
        CASE
            WHEN t_stock.goods_id IS NOT NULL THEN t_stock.goods_id
            WHEN t_stock.goods_id IS NULL AND t_allowance1.goods_id IS NOT NULL THEN t_allowance1.goods_id
            ELSE NULL::integer
        END AS goods_id,
        CASE
            WHEN t_stock.ware_id IS NOT NULL THEN t_stock.ware_id
            WHEN t_stock.ware_id IS NULL AND t_allowance1.ware_id IS NOT NULL THEN t_allowance1.ware_id
            ELSE NULL::integer
        END AS ware_id,
        COALESCE(t_stock.stock_num, 0::bigint) AS stock_num,
        COALESCE(t_stock.rstock_num, 0::bigint) AS rstock_num,
        COALESCE(t_allowance1.allowance_num, 0::bigint) AS rorder_num,
        CASE
            WHEN t_stock.shop_id IS NOT NULL THEN t_stock.shop_id
            WHEN t_stock.shop_id IS NULL AND t_allowance1.shop_id IS NOT NULL THEN t_allowance1.shop_id
            ELSE NULL::integer
        END AS shop_id


   FROM

   (
      SELECT
           CASE
               WHEN t_stock.goods_id IS NOT NULL THEN t_stock.goods_id
               WHEN t_stock.goods_id IS NULL AND t_allowance.goods_id IS NOT NULL THEN t_allowance.goods_id
               ELSE NULL::integer
           END AS goods_id,
           CASE
               WHEN t_stock.ware_id IS NOT NULL THEN t_stock.ware_id
               WHEN t_stock.ware_id IS NULL AND t_allowance.ware_id IS NOT NULL THEN t_allowance.ware_id
               ELSE NULL::integer
           END AS ware_id,
           COALESCE(t_stock.stock_num, 0::bigint) AS stock_num,
           COALESCE(t_allowance.allowance_num, 0::bigint) AS rstock_num,
           CASE
               WHEN t_stock.shop_id IS NOT NULL THEN t_stock.shop_id
               WHEN t_stock.shop_id IS NULL AND t_allowance.shop_id IS NOT NULL THEN t_allowance.shop_id
               ELSE NULL::integer
           END AS shop_id,

           ( CASE
               WHEN t_stock.goods_id IS NOT NULL THEN t_stock.goods_id
               WHEN t_stock.goods_id IS NULL AND t_allowance.goods_id IS NOT NULL THEN t_allowance.goods_id
               ELSE NULL::integer
           END ) || '-' ||
           ( CASE
               WHEN t_stock.ware_id IS NOT NULL THEN t_stock.ware_id
               WHEN t_stock.ware_id IS NULL AND t_allowance.ware_id IS NOT NULL THEN t_allowance.ware_id
               ELSE NULL::integer
           END ) || '-' ||
           ( CASE
               WHEN t_stock.shop_id IS NOT NULL THEN t_stock.shop_id
               WHEN t_stock.shop_id IS NULL AND t_allowance.shop_id IS NOT NULL THEN t_allowance.shop_id
               ELSE NULL::integer
           END ) AS stock_cd

      FROM ( SELECT t_stock_hand.goods_id, t_stock_hand.ware_id, sum(t_stock_hand.num *
                   CASE t_stock_hand.io_div
                       WHEN 1 THEN 1
                       WHEN 2 THEN -1
                       ELSE NULL::integer
                   END) AS stock_num, t_stock_hand.shop_id, (((t_stock_hand.goods_id::text || '-'::text) || t_stock_hand.ware_id::text) || '-'::text) || t_stock_hand.shop_id::text AS stock_cd
              FROM t_stock_hand
             WHERE t_stock_hand.work_div::text <> 1::text AND t_stock_hand.work_div::text <> 3::text AND t_stock_hand.work_day <= now()
             GROUP BY t_stock_hand.goods_id, t_stock_hand.ware_id, t_stock_hand.shop_id) t_stock
      FULL JOIN ( SELECT t_stock_hand.goods_id, t_stock_hand.ware_id, sum(t_stock_hand.num *
                   CASE t_stock_hand.io_div
                       WHEN 1 THEN -1
                       WHEN 2 THEN 1
                       ELSE NULL::integer
                   END) AS allowance_num, t_stock_hand.shop_id, (((t_stock_hand.goods_id::text || '-'::text) || t_stock_hand.ware_id::text) || '-'::text) || t_stock_hand.shop_id::text AS stock_cd
              FROM t_stock_hand
             WHERE t_stock_hand.work_div::text = 1::text AND t_stock_hand.work_day <= now()
             GROUP BY t_stock_hand.goods_id, t_stock_hand.ware_id, t_stock_hand.shop_id) t_allowance ON t_stock.stock_cd = t_allowance.stock_cd
   ) AS t_stock


   FULL JOIN ( SELECT t_stock_hand.goods_id, t_stock_hand.ware_id, sum(t_stock_hand.num *
           CASE t_stock_hand.io_div
               WHEN 1 THEN -1
               WHEN 2 THEN 1
               ELSE NULL::integer
           END) AS allowance_num, t_stock_hand.shop_id, (((t_stock_hand.goods_id::text || '-'::text) || t_stock_hand.ware_id::text) || '-'::text) || t_stock_hand.shop_id::text AS stock_cd
      FROM t_stock_hand
     WHERE t_stock_hand.work_div::text = 3::text AND t_stock_hand.work_day <= now()
     GROUP BY t_stock_hand.goods_id, t_stock_hand.ware_id, t_stock_hand.shop_id) t_allowance1 ON t_stock.stock_cd = t_allowance1.stock_cd
;


_EOD_;


$result = Db_Query($conn, $t_stock_renew_sql);
if($result === false){
	$error_con = pg_last_error();
	$error_time = date("Y-m-d H:i");
	$error_msg[0] = "$error_time ".__FILE__." \n".__LINE__."���� �߸˥ơ��֥�ι������� \n ";
	$error_msg[1] = "$error_con \n\n";
}


//����Ƚ��
if($error_msg == NULL){
	//����

	Db_Query($conn,"COMMIT");
	$today = date("Y-m-d H:i:s");
	//FILE�˽���
	error_log("$today �߸˥ơ��֥빹����λ \n\n",3,LOG_FILE);
}else{
	//�۾�
	Db_Query($conn, "ROLLBACK");

	//FILE�˥��顼����
	$estr = NULL;
	for($i=0;$i<count($error_msg);$i++){
		error_log($error_msg[$i],3,LOG_FILE);
		$e_contents .= $error_msg[$i];
	}
	//�᡼��ǥ��顼����
	Error_send_mail($g_error_mail,$g_error_add,"�߸˥ơ��֥빹�������ǰ۾�ȯ��",$e_contents);
}


?>
