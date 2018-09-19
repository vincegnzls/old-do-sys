<?php

/**
 * ���� ��������ID�ơ��֥��꽸������ID����Ф��롣
 *
 * ���� 
 *
 * @param   object    $db_con  DB���ͥ������
 *
 * @return  boolean   ��ʣ���顼��ȯ���������� false
 *  OR
 * @return  int       ��������ID
 *
 */
function Get_Daily_Slip_Id($db_con){

    //��������ID�ơ��֥�
    $sql  = "SELECT";
    $sql .= "   COALESCE(MAX(daily_slip_id), 0)+1 AS max_daily_slip_id ";
    $sql .= "FROM ";
    $sql .= "   t_daily_slip_id_serial ";
    $sql .= ";"; 

    $result = Db_Query($db_con, $sql);
    $max_id = pg_fetch_result($result, 0,0);

    //MAX+1����ID����Ͽ
    $sql = "INSERT INTO t_daily_slip_id_serial (daily_slip_id) VALUES ($max_id);";
    $result = Db_Query($db_con, $sql);

    if($result === false){
        $err_message = pg_last_error();
        $err_format = "t_daily_slip_id_serial_pkey";
        Db_Query($db_con, "ROLLBACK;");

        //ID����ʣ�������
        if(strstr($err_message, $err_format) != false){
            return false;

        //����¾�Υ��顼���϶���Ū�˽�����λ
        }else{
            exit;
        }
    }

    return $max_id;
}


?>