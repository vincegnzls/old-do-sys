<?php
/*********************************************************************
 *
 * �դ������󤹤�
 *
 * @author      �դ���
 * @version     1.0.0 (2006 ��ʬ)
 *
 *********************************************************************/

// ����
require_once("db.fnc");

/**
 *
 * POST���줿�ǡ�����������Ͽ����Ƥ���ǡ����Ƚ�ʣ���Ƥ��ʤ��������å�
 *
 * �ѹ�����
 * 1.0.0 (2006 ��ʬ)    �դ�����������
 *
 * @author      �դ���
 *
 * @version     1.0.0 (2006 ��ʬ)
 *
 * @param       string      $con            DB��³�ؿ�
 * @param       string      $status         ���ơ������ʿ������ѹ���
 * @param       string      $origin_data    �ѹ����ǡ�����̵������null��
 * @param       string      $post_data      POST���줿�ǡ���
 * @param       string      $sql            ��ʣ������å�����SQL
 *
 * @return      boolean                     ���顼��true
 *
 */
/*
function Duplicate_Chk($con, $status, $origin_data, $post_data, $sql){

    // POST���줿�ǡ���������ξ��ʣ��ե�����ν�ʣ�����å��ξ���
    if (is_array($post_data)){
        $post_data = $post_data[0]."-".$post_data[1];
        // �ѹ��ξ��ʸ��ǡ������������
        if ($status == "chg"){
            $origin_data = $origin_data[0]."-".$origin_data[1];
        }
    }

    // ������Ͽ�ξ�硦�ѹ��Ǹ��ǡ�����POST�ǡ������㤦���
    if ( (($status == "add") && ($post_data != null) ||
         (($status == "chg") && ($origin_data != $post_data) && ($post_data) != null)) ){

        $res = Db_Query($con, $sql);

        // �����쥳���ɤ�1��ʾ夢��н�ʣ���顼
        return (pg_fetch_result($res, 0, 0) >= 1) ? true : false;

    }

    return false;

}
*/

/**
 *
 * POST���줿�ǡ�����DB��¸�ߤ��Ƥ��뤫�����å�
 *
 * �ѹ�����
 * 1.0.0 (2006/02/06)    �դ�����������
 *
 * @author      �դ���
 *
 * @version     1.0.0 (2006/02/06)
 *
 * @param       string      $con            DB��³�ؿ�
 * @param       string      $post_data      POST���줿�ǡ���
 * @param       string      $sql            ¸�ߤ�����å�����SQL
 *
 * @return      boolean                     ���顼��false
 *
 */
/*
function Exist_Chk($con, $post_data, $sql){

    $res = Db_Query($con, $sql);

    // �����쥳���ɤ�0��ξ��ϥ��顼
    return (pg_fetch_result($res, 0, 0) == 0) ? false : true;

}
*/

/**
 *
 * �����ơ��֥�Υ����ɤ�̾�������Ƽ���
 * �����ɤΥե����ब2�Ĥξ��ϼ�ư�ե���������ư
 *
 * �ѹ�����
 * 1.0.0 (2006 ��ʬ)    �դ�����������
 *
 * @author      �դ���
 *
 * @version     1.0.0 (2006 ��ʬ)
 *
 * @param       string      $con            DB��³�ؿ�
 * @param       string      $table          �ơ��֥�̾
 * @param       string      $cd_col         �����ɤΥ����̾(2�Ĥξ��������)
 * @param       string      $value_col      ̾���Υ����̾
 * @param       string      $option         ��л��ξ��(SQLʸ��WHERE���Ʊ���ե����ޥåȤ�)
 *
 * @return                  $js             Javascript
 *
 */
/*
function Code_Value_Disp2($name, $cd_col, $value_col, $ary_rec_data){

    $row = count($ary_rec_data);

    // Javascript����
    $js  = "function Code_Value_$name(me, cd_form){\n";
    $js .= "    data = new Array($row);\n";

    // �����ɤΥե����ब��������ξ��ʥ����ɥե����ब���ġ�
    if (is_array($cd_col)){

        for ($i = 0; $i < $row; $i++){
            $js .= "    data['".$ary_rec_data[$i][$cd_col[0]]."-".$ary_rec_data[$i][$cd_col[1]]."']=\"".$ary_rec_data[$i][$value_col]."\"\n";
        }
        $js .= "    var cd1  = cd_form[0]+\"[\"+cd_form[1]+\"]\";\n";
        $js .= "    var cd2  = cd_form[0]+\"[\"+cd_form[2]+\"]\";\n";
        $js .= "    var data = data[me.elements[cd1].value+\"-\"+me.elements[cd2].value];\n";

    // �����ɤΥե����ब����ǤϤʤ����ʥ����ɥե����ब���ġ�
    }else{

        for ($i = 0; $i < $row; $i++){
            $js .= "    data['".$ary_rec_data[$i][$cd_col]."']=\"".$ary_rec_data[$i][$value_col]."\"\n";
        }
#        $js .= "    var cd1  = form+\"[\"+cd_form[0]+\"]\";\n";
#        $js .= "    var cd1  = form+\"[\"+cd_form+\"]\";\n";
#        $js .= "    var cd   = cd_form;\n";
        $js .= "    var data = data[me.elements[cd_form].value];\n";

    }
    $js .= "    if (data == undefined){\n";
    $js .= "        document.all.$name.innerHTML = \"\";\n";
    $js .= "    }else{\n";
    $js .= "        document.all.$name.innerHTML = data;\n";
    $js .= "    }\n";
    $js .= "}\n";

    return $js;

}

*/








#-------------------------�������鲼�ϸŤ���--------------------------------

/*--------------------------------------------------------
    �ؿ�̾: Address_Get
    ����:   ���Ϥ��줿͹���ֹ椫�齻��쥳���ɤ����
    param:  $post1_data      ͹���ֹ��3������ϥǡ���
            $post2_data      ͹���ֹ沼4������ϥǡ���
            $post_table      ͹���ֹ�ơ��֥�̾
            $post_no_col     ͹���ֹ�Υ����̾
    return: $ary_addr_record ����ǡ���������
--------------------------------------------------------*/
function Address_Get($post1_data, $post2_data, $post_table, $post_no_col){

    // ���Ϥ��줿͹���ֹ�ǡ�����trim
    $post1_data = trim($post1_data);
    $post2_data = trim($post2_data);

    // ���Ϥ��줿͹���ֹ�Υե����ޥåȤ������ʾ��
    if (Post_Format_Chk($post1_data, $post2_data, 3, 4) == false &&
        $post1_data != null && $post2_data != null){

        $sql  = "SELECT * FROM $post_table ";
        $sql .= "WHERE $post_no_col = '".$post1_data.$post2_data."' ";
        $sql .= ";";
        $con  = Db_Connect();
        $res  = Db_Query($con, $sql);

        // ����쥳���ɤ�1�濫��н���ǡ�������
        if (pg_num_rows($res) == 1){
            $ary_addr_record = pg_fetch_array($res, 0, PGSQL_ASSOC);
            return $ary_addr_record;
        }

    }

}


/*--------------------------------------------------------
    �ؿ�̾: Code_Value_Disp
    ����:   �����ơ��֥�Υ����ɤ�̾�������Ƽ���
            �����ɤΥե����ब2�Ĥξ��ϼ�ư�ե���������ư
    param:  $name            Javascript�桼���ؿ�̾(Code_Value_$name)
            $table           �ơ��֥�̾
            $cd_col          �����ɤΥ����̾(2�Ĥξ��������)
            $value_col       ̾���Υ����̾
            $option          ��л��ξ��(SQLʸ��WHERE���Ʊ���ե����ޥåȤ�)
    return: $ary_record      �����ɤ�̾��������
--------------------------------------------------------*/
// ��������('A`)
function code_value_disp($name, $table, $cd_col, $value_col, $option = null){

    // SQLʸ�ѥ����ɥ����������
    if (is_array($cd_col)){
        $sql_opt = "$cd_col[0], $cd_col[1]";
    }else{
        $sql_opt = "$cd_col";
    }

    // SQLʸ
    $sql  = "SELECT ";
    $sql .= "$sql_opt, ";
    $sql .= "$value_col ";
    $sql .= "FROM $table ";
    $sql .= $option;
    $sql .= " ;";

    // �����ɤ�̾�������
    $con  = Db_Connect();
    $res  = Db_Query($con, $sql);
    $row  = pg_num_rows($res);
    for ($i = 0; $i < $row; $i++){
        $ary_record[$i] = pg_fetch_array($res, $i, PGSQL_ASSOC);
    }

    // Javascript����
    $js  = "function Code_Value_$name(form, value_form, cd_form){\n";
    $js .= "    data = new Array($row);\n";

    if (is_array($cd_col)){

        // �����ɤΥե����ब2�Ĥ�����
        for ($i = 0; $i < $row; $i++){
            $js .= "    data['".$ary_record[$i][$cd_col[0]]."-".$ary_record[$i][$cd_col[1]]."']=\"".$ary_record[$i][$value_col]."\"\n";
        }
        $js .= "    var cd1   = form+\"[\"+cd_form[0]+\"]\";\n";
        $js .= "    var cd2   = form+\"[\"+cd_form[1]+\"]\";\n";
        $js .= "    var data = data[document.dateForm.elements[cd1].value+\"-\"+document.dateForm.elements[cd2].value];\n";
        // 0000�䴰�ʻ��ȥơ��֥뤬����åץơ��֥�ξ��Τߡ�
        if ($table == "t_shop"){
            $js .= "    if ((document.dateForm.elements[cd1].value != \"\") && (document.dateForm.elements[cd2].value == \"\")){\n";
            $js .= "        document.dateForm.elements[cd2].value = \"0000\";\n";
            $js .= "        document.dateForm.elements[cd2].value = \"0000\";\n";
            $js .= "    }\n";
        }
        // �ե���������ư�ʤ����ʸ�����������֤ˡ�
        $js .= "    len = document.dateForm.elements[cd1].value.length;\n";
        $js .= "    if (cd_form[2]==len){\n";
        $js .= "        document.dateForm.elements[cd2].focus()\n";
        $js .= "        document.dateForm.elements[cd2].select()\n";
        $js .= "    }\n";

    }else{

        // �����ɤΥե����ब1�Ĥξ��
        for ($i = 0; $i < $row; $i++){
            $js .= "    data['".$ary_record[$i][$cd_col]."']=\"".$ary_record[$i][$value_col]."\"\n";
        }
        $js .= "    var cd1  = form+\"[\"+cd_form[0]+\"]\";\n";
        $js .= "    var data = data[document.dateForm.elements[cd1].value];\n";
    }

    $js .= "    var value = form+\"[\"+value_form+\"]\";\n";
    $js .= "    if (data == undefined){\n";
    $js .= "        document.dateForm.elements[value].value = \"\";\n";
    $js .= "    }else{\n";
    $js .= "        document.dateForm.elements[value].value = data;\n";
    $js .= "    }\n";
    $js .= "}\n";

    return $js;

}


?>