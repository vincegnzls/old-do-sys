<?php
/*********************************************************************
 * @Program         function_qf_parts.inc
 * @fnc.Overview    QuickForm�ѡ���
 * @author          
 * @Cng.Tracking    #1: 20060112
 ********************************************************************/

/*------------------------------------------------------------------
    �ե��������
------------------------------------------------------------------*/

/*--------------------------------------------------------
    ����:   QuickForm - text
    param:  $form                  �ե�����
            $element_type          ����
            $f_name                �ե�����̾
            $width                 �ե������Ĺ��
            $length                ʸ��������
            $l_name                ��٥�
            $opt_type              ����¾°�����ץ����Υ�����(��ά��)
            $ary_opt               ����¾°�����ץ�������ѿ�����(��ά��)
    return: �ե�����
--------------------------------------------------------*/
function Qf_Text_Part($form, $element_type, $f_name, $width, $length, $l_name, $opt_type = null, $ary_opt = null){

    $option = "size=\"".$width."\" maxlength=\"".$length."\" ";

    switch ($opt_type){

        // ����ʸ�������ϸ塢��ư�ե���������ư
        //    $ary_opt[0]: ���롼��̾
        //    $ary_opt[1]: �ե���������ư��ե�����̾
        case "next_focus":
            $option .= "onkeyup=\"Nextfocus(this.form, '$ary_opt[0]', '$f_name', '$ary_opt[1]', '$length')\" ";
            $option .= "onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"";
            break;

        // ���Ϥ�̵����и��ߤ����դ��䴰������˰���ʸ�������ϸ塢��ư�ե���������ư
        //    $ary_opt[0]: ���롼��̾
        //    $ary_opt[1]: �ե���������ư��ե�����̾
        //    $ary_opt[2]: ���եե�����̾(ǯ�����ν���󼡸�����)
        case "today_compform_next_focus":
            $option .= "onkeyup=\"Nextfocus(this.form, '$ary_opt[0]', '$f_name', '$ary_opt[1]', '$length')\" ";
            $option .= "onFocus=\"Comp_DateForm_Today(this, this.form, '$ary_opt[0]', '".$ary_opt[2][0]."', '".$ary_opt[2][1]."', '".$ary_opt[2][2]."')\" ";
            $option .= "onBlur=\"blurForm(this)\"";
            break;

        // valueɽ��(����������¦)
        //    $ary_opt[0]: js�桼���ؿ��Ѥ�̾��
        //    $ary_opt[1]: �ե�����Υ��롼��̾
        //    $ary_opt[2]: value�ե�����̾
        case "disp_value_jack":
            $option .= "onKeyup=\"javascript:Code_Value_$ary_opt[0]('$ary_opt[1]', this, '$ary_opt[2]')\" ";
            $option .= "onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"";
            break;

        // valueɽ��(�ǡ���ɽ��¦)
        case "disp_value_gill":
            $option .= "style=\"color: #555555; border: #ffffff 1px solid; ";
            $option .= "background-color: #ffffff;\" readonly tabindex=\"-1\"";
            break;

        // else
        default:
            $option .= "onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"";

    }

    return $form->$element_type("text", $f_name, $l_name, $option);

}

/*--------------------------------------------------------
    ����:   QuickForm - text
    param:  $form                  �ե�����
            $element_type          ����
            $f_name                �ե�����̾
            $width                 �ե������Ĺ��
            $length                ʸ��������
            $l_name                ��٥�
            $opt                   ���ץ����(��ά��)
                [*][0]             ���ץ���󥿥���
                [*][1��]           ���ץ�����ѥǡ���
    return: �ե�����
--------------------------------------------------------*/
function Qf_Text_Part1($form, $element_type, $f_name, $width, $length, $l_name, $opt = null){

    // ���ץ�����������
    $option = "size=\"".$width."\" maxlength=\"".$length."\" ";

    // �ե����������������BGColor����ʥǥե����ON��
    $ary_onform_flg[] = true;
    $ary_blurform_flg[] = true;

    // �Ρ��ޥ륿���פΥե������$opt��̵���ˤξ��
    if ($opt == null){
        $option .= "onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"";
        return $form->$element_type("text", $f_name, $l_name, $option);
    }

    // �����$opt�ˤǻ��ꤵ��Ƥ��륪�ץ������ɲ�
    foreach ($opt as $ary_opt){

        switch ($ary_opt[0]){

            // ����ʸ�������Ϥ��줿��ե��������򼡤Υե�����ذܤ�
            case "next_focus":
                // [1]: ���롼��̾
                // [2]: �ե�������������Υե�����̾
                $option .= "onkeyup=\"Nextfocus(this.form, '".$ary_opt[1]."', '$f_name', '".$ary_opt[2]."', '$length');\" ";
                break;

            // ���Ϥ�̵�����������ǯ�����򤽤줾��Υե�����(ǯ,��,��)���䴰����
            case "comp_form_today":
                // [1]: ���롼��̾
                // [2]: ǯ�Υե�����̾
                // [3]: ��Υե�����̾
                // [4]: ���Υե�����̾
                $option .= "onFocus=\"Comp_Form_Today(this, this.form, ";
                $option .= "'".$ary_opt[1]."', '".$ary_opt[2]."', '".$ary_opt[3]."', '".$ary_opt[4]."');\" ";
                $ary_onform_flg[] = false;
                break;

            // keyup������Υե�������ͤ��䴰����(����Υե�����ǡ�����null�ξ��)
            case "comp_form_val":
                // [1]: ���롼��̾
                // [2]: �䴰�оݥե�����̾
                // [3]: �䴰ʸ����
                $option .= "onKeyup=\"Comp_Form_Val(this.form, '".$ary_opt[1]."', '".$f_name."', '".$ary_opt[2]."', '".$ary_opt[3]."');\" ";
                break;

            // ���Ϥ��줿�����ɤ��Ф���value��ɽ���ʥ���������¦�ѡ�
            // �����ɤΥե����ब2�Ĥξ��ϡ�����ʸ�������Ϥ��줿��ե����ࣱ����ե����ࣲ�إե���������ư
            case "disp_value_jack":
                // [1]: Javascript�桼���ؿ�̾�˻��Ѥ���ʸ����
                // [2]: ���롼��̾
                // [3]: valueɽ���ե�����̾
                // [4][0]: �����ɣ��Υե�����̾
                // [4][1]: �����ɣ��Υե�����̾(�����ɤΥե����ब2�Ĥξ��)
                if (isset($ary_opt[4][1])){
                    $cd_form = "['".$ary_opt[4][0]."', '".$ary_opt[4][1]."', $length]";
                }else{
                    $cd_form = "['".$f_name."']";
                }
                $option .= "onKeyup=\"javascript:Code_Value_".$ary_opt[1]."('".$ary_opt[2]."', '".$ary_opt[3]."', ".$cd_form.");\" ";
                break;

            // ���Ϥ��줿�����ɤ��Ф���value��ɽ���ʥǡ���ɽ��¦�ѡ������ե�����ξ���
            case "disp_value_gill1":
                $option .= "style=\"color: #555555; border: #ffffff 1px solid; ";
                $option .= "background-color: #ffffff;\" readonly tabindex=\"-1\"";
                $ary_onform_flg[] = false;
                $ary_blurform_flg[] = false;
                break;

        }

    }

    // onForm���ץ������դ���/�դ��ʤ�
    if (in_array(false, $ary_onform_flg)){
        $option .= null;
    }else{
        $option .="onFocus=\"onForm(this)\" ";
    }

    // blurForm���ץ������դ���/�դ��ʤ�
    if (in_array(false, $ary_blurform_flg)){
        $option .= null;
    }else{
        $option .="onBlur=\"blurForm(this)\" ";
    }

# debug print
#print "���ץ����".$option."<br>";

    // QuickForm����
    return $form->$element_type("text", $f_name, $l_name, $option);

}

/*--------------------------------------------------------
    ����:   QuickForm - password
    param:  $form                  �ե�����
            $element_type          ����
            $f_name                �ե�����̾
            $width                 �ե������Ĺ��
            $length                ʸ��������
            $l_name                ��٥�
    return: �ե�����
--------------------------------------------------------*/
function Qf_Password_Part($form, $element_type, $f_name, $width, $length, $l_name){

    $option  = "size=\"".$width."\" maxlength=\"".$length."\" ";
    $option .= "onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"";

    return $form->$element_type("password", $f_name, $l_name, $option);

}

/*--------------------------------------------------------
    ����:   QuickForm - textarea
    param:  $form                  �ե�����
            $element_type          ����
            $f_name                �ե�����̾
            $rows                  �Կ�
            $cols                  ���
            $l_name                ��٥�
    return: �ե�����
--------------------------------------------------------*/
function Qf_Textarea_Part($form, $element_type, $f_name, $rows, $cols, $l_name){

    $option = "rows=\"".$rows."\" cols=\"".$cols."\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"";

    return $form->$element_type("textarea", $f_name, $l_name, $option);

}

/*--------------------------------------------------------
    ����:   QuickForm - button
    param:  $form                  �ե�����
            $element_type          ����
            $button_type           �ܥ���Υ�����°��
            $f_name                �ե�����̾
            $f_value               �ܥ���ΥХ�塼°��
            $opt_type              ����¾°�����ץ����Υ�����(��ά��)
            $ary_opt               ����¾°�����ץ�������ѿ�����(��ά��)
    return: �ե�����
--------------------------------------------------------*/
function Qf_button_Part($form, $element_type, $button_type, $f_name, $f_value, $opt_type = null, $ary_opt = null){

    switch ($opt_type){

        // ��Ͽ���ޤ��������������ڡ�������
        case "submit_01":
            $option = "onClick=\"javascript:return Dialogue('��Ͽ���ޤ���','#')\"";
            break;

        // ���
        case "return_01":
            $option = "onClick=\"javascript:history.back()\"";
            break;

        // ����
        // $ary_opt[0]: hidden�Υե�����̾
        // $ary_opt[1]: ������ڡ���
        // $ary_opt[2]: hidden���䴰����ʸ����
        case "search_01":
            $option = "onClick=\"javascript:Button_Submit('$ary_opt[0]', '$ary_opt[1]', '$ary_opt[2]')\"";
            break;

        // else
        default:
            $option = null;

    }

    return $form->$element_type($button_type, $f_name, $f_value, $option);

}


/*------------------------------------------------------------------
    �롼�����
------------------------------------------------------------------*/

/*--------------------------------------------------------
    ����:   QuickForm - ɬ�ܹ��ܥ����å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
    return: void
--------------------------------------------------------*/
function Qf_Chk_Required($form, $f_name, $l_name){

    $form->addRule($f_name, $l_name." ��ɬ�ܹ��ܤǤ���", "required", null);

}

/*--------------------------------------------------------
    ����:   QuickForm - [0-9]�����å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
            $maxlen                ������
    return: void
--------------------------------------------------------*/
function Qf_Chk_Numeric($form, $f_name, $l_name, $maxlen){

    $form->addRule($f_name, $l_name." ��Ⱦ�ѿ���".$maxlen."�����Ǥ�", "numeric��", null);

}

/*--------------------------------------------------------
    ����:   QuickForm - [0-9-]�����å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
            $maxlen                ������
    return: void
--------------------------------------------------------*/
function Qf_Chk_Numhyphen($form, $f_name, $l_name, $maxlen){

    $form->addRule($f_name, $l_name." ��Ⱦ�ѿ����ȡ�-�פ�".$maxlen."�����Ǥ���", "numhyphen");

}

/*--------------------------------------------------------
    ����:   QuickForm - ��ʣ�����å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
            $duplicate_flg         ��ʣ�ե饰(��ʣ: true)
    return: void
--------------------------------------------------------*/
function Qf_Chk_Duplicate($form, $f_name, $l_name, $duplicate_flg){

    if($duplicate_flg == true){
        $form->setElementError($f_name, "���˻��Ѥ���Ƥ���".$l_name."�Ǥ���");
    }

}

/*--------------------------------------------------------
    ����:   QuickForm - Ʊ�쥷��å���Ǥν�ʣ�������å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
            $num_over_flg          �Ŀ������Хե饰(���顼: true)
            $maxnum                ����Ŀ�
    return: void
--------------------------------------------------------*/
function Qf_Chk_Numover($form, $f_name, $l_name, $num_over_flg, $maxnum){

    if($num_over_flg == true){
        $form->setElementError($f_name, $l_name." ��Ʊ�쥷��å����".$maxnum."�ĤޤǤǤ���");
    }

}

/*--------------------------------------------------------
    ����:   QuickForm - ͹���ֹ�ե����ޥåȥ����å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
            $maxlen1               ��������
            $maxlen2               ��������
            $format_err_flg        �ե����ޥåȥ��顼�ե饰(���顼: true)
    return: void
--------------------------------------------------------*/
function Qf_Chk_Post($form, $f_name, $l_name, $maxlen1, $maxlen2, $format_err_flg){

    if($format_err_flg == true){
        $form->setElementError($f_name, $l_name." ��Ⱦ�ѿ���".$maxlen1."��-".$maxlen2."��Ǥ���");
    }

}

/*--------------------------------------------------------
    ����:   QuickForm - TEL, FAX�ե����ޥåȥ����å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
            $maxlen                ������
    return: void
--------------------------------------------------------*/
function Qf_Chk_Telfax($form, $f_name, $l_name, $maxlen){

    $form->addRule($f_name, $l_name." ��Ⱦ�ѿ����ȡ�-�פ�".$maxlen."�����Ǥ���", "telfax");

}

/*--------------------------------------------------------
    ����:   QuickForm - ʸ����Ĺ�����å�
    param:  $form                  �ե�����
            $f_name                �ե�����̾
            $l_name                ����̾
            $maxlen                ������
            $fnc_type              ���Ѥ���ؿ�(1byte/MultiByte��)
    return: void
--------------------------------------------------------*/
function Qf_Chk_Strlen($form, $f_name, $l_name, $maxlen, $unit, $fnc_type){

    $form->addRule($f_name, $l_name." ��".$maxlen.$unit."����Ǥ���", $fnc_type, $maxlen);
    $form->applyFilter($f_name, "trim");

}

















/*
    //�����������
    function date_part($form,$num){
	
		$f_date_a = "f_date_a".$num;

		//4ʸ��-2ʸ��-2ʸ��
		$text[] =& $form->createElement("text","y_input","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" value=\"\" onkeyup=\"changeText3(this.form,$num)\" onFocus=\"onForm2(this,this.form,$num)\" onBlur=\"blurForm(this)\"");
		$text[] =& $form->createElement("text","m_input","�ƥ����ȥե�����","size=\"2\" maxLength=\"2\" value=\"\" onkeyup=\"changeText4(this.form,$num)\" onFocus=\"onForm2(this,this.form,$num)\" onBlur=\"blurForm(this)\"");
		$text[] =& $form->createElement("text","d_input","�ƥ����ȥե�����","size=\"2\" maxLength=\"2\" value=\"\" onFocus=\"onForm2(this,this.form,$num)\" onBlur=\"blurForm(this)\"");
		$form->addGroup( $text,"$f_date_a","$f_date_a","-");

		//��������
		$text = array();

		$f_date_b = "f_date_b".$num;

		//4ʸ��-2ʸ��-2ʸ����4ʸ��-2ʸ��-2ʸ��
		$text[] =& $form->createElement("text","y_start","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" value=\"\" onkeyup=\"changeText5(this.form,$num)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
		$text[] =& $form->createElement("static","","","-");
		$text[] =& $form->createElement("text","m_start","�ƥ����ȥե�����","size=\"2\" maxLength=\"2\" value=\"\" onkeyup=\"changeText6(this.form,$num)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
		$text[] =& $form->createElement("static","","","-");
		$text[] =& $form->createElement("text","d_start","�ƥ����ȥե�����","size=\"2\" maxLength=\"2\" value=\"\" onkeyup=\"changeText7(this.form,$num)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
		$text[] =& $form->createElement("static","","","������");
		$text[] =& $form->createElement("text","y_end","�ƥ����ȥե�����","size=\"4\" maxLength=\"4\" value=\"\" onkeyup=\"changeText8(this.form,$num)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
		$text[] =& $form->createElement("static","","","-");
		$text[] =& $form->createElement("text","m_end","�ƥ����ȥե�����","size=\"2\" maxLength=\"2\" value=\"\" onkeyup=\"changeText9(this.form,$num)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
		$text[] =& $form->createElement("static","","","-");
		$text[] =& $form->createElement("text","d_end","�ƥ����ȥե�����",'size="2" maxLength="2" value="" onFocus="onForm(this)" onBlur="blurForm(this)"');
		$form->addGroup( $text,"$f_date_b","$f_date_b");
		
	}

	//����������
 function money_part($form,$num){

		$f_code_c = "f_code_c".$num;

		//9ʸ��.2ʸ��
		$text[] =& $form->createElement("text","f_text9","�ƥ����ȥե�����","size=\"11\" maxLength=\"9\" value=\"\" onkeyup=\"changeText10(this.form,$num)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\" style=\"text-align: right\"");
		$text[] =& $form->createElement("text","f_text2","�ƥ����ȥե�����",'size="2" maxLength="2" value="" onFocus="onForm(this)" onBlur="blurForm(this)" style="text-align: left"');
		$form->addGroup( $text, "$f_code_c", "$f_code_c",".");

		$radio15[] =& $form->createElement( "radio",NULL,NULL, "������","1");
		$radio15[] =& $form->createElement( "radio",NULL,NULL, "�߸˿�0�ʳ�","2");
		$radio15[] =& $form->createElement( "radio",NULL,NULL, "�߸˿�0","3");
		$form->addGroup( $radio15, "f_radio6", "�оݾ���");

		//ɬ�����ϥ����å��ʥ�����¦��
		$form->addRule('f_radio6', 'ǯ��ɬ�ܹ��ܤǤ�����', 'required');

		//�����å�Ƚ��
		$form->validate();

	}

	function goods_part($form,$num){

		$f_goods = "f_goods".$num;
		$t_goods = "t_goods".$num;
		$goods = "'goods".$num."'";

		//���ʥ�����
		$form->addElement("text","$f_goods","�ƥ����ȥե�����","size=\"10\" maxLength=\"8\" value=\"\" onKeyUp=\"javascript:display4(this,$goods)\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\"");
$form->addElement("text","$t_goods","�ƥ����ȥե�����","size=\"34\" maxLength=\"30\" onFocus=\"onForm(this)\" onBlur=\"blurForm(this)\" style=\"color : #000000; border : #ffffff 1px solid; background-color: #ffffff;\" readonly");

	}
*/
?>