<?php
require('../../fpdf/mbfpdf.php');

//�����
$xx = 10;
$yy = 10;

//�Ľ񤭡�mm��ࡢA4������
$pdf=new MBFPDF('P','mm','A4');

//�ե��������
$pdf->AddMBFont(GOTHIC,'SJIS');
$pdf->AddMBfont(PGOTHIC,'SJIS');
$pdf->AddMBfont(MINCHO,'SJIS');
$pdf->AddMBfont(PMINCHO,'SJIS');
$pdf->Open();
//�ڡ�������
$pdf->AddPage();

//��������
$bill = array("","","","","","","","","","","","","");

//ɽ������������
$keta = 10;

/* ��������إå��Ȼפ�����ʬ */
//�����ͤΥ����ɡ����ꡢ���̾
$pdf->SetFont('MS-Gothic','',8);
$pdf->SetXY($xx,$yy);
$pdf->SetTextColor(77,66,177);//�ե���Ȥ��Ĥ�����
$pdf->Write(8,"�����ͥ�����");
$pdf->SetTextColor(0,0,0);//�ե���Ȥ��������
$pdf->Write(8,"******-****");
$pdf->SetFontSize(10);
$pdf->SetTextColor(77,66,177);//�ե���Ȥ��Ĥ�����
$pdf->Write(7,"\n��");
$pdf->SetTextColor(0,0,0);//�ե���Ȥ��������
$pdf->Write(7,"***-****\n");
$pdf->SetX($xx+2);
$pdf->Cell(40,5,"�����ԡ�������������ݡ��ݡ���",0,2);
$pdf->Cell(40,5,"��������������������");
$pdf->SetX($xx+7);
$pdf->SetFontSize(12);
$pdf->Cell(45,20,"������������������ҡ�����");

//�����"�����"ɽ��
$pdf->SetFont('','B',18);//������18�ݥ���Ȥ�����
$pdf->SetTextColor(77,66,177);//�ե���Ȥ��Ĥ�����
$pdf->SetXY(0,$yy);
$pdf->Cell(209.97,16,"�� �� ��",0,1,"C");//�������äѤ��Υ�������·��
$pdf->SetLineWidth(0.1);//�饤���������0.1mm������
$pdf->SetDrawColor(77,66,177);//���ο����Ĥ�����
$pdf->Line($xx+81,$yy+12,$xx+108,$yy+12);//�饤��ɽ��
$pdf->Line($xx+81,$yy+12.5,$xx+108,$yy+12.5);//0.5mm�����Ƥ⤦����ɽ��
$pdf->SetLineWidth(0.2);

//������Ļ�����ɽ��
$pdf->SetFontSize(8);//�ե���ȥ�������8�ݥ���Ȥ�����
$pdf->SetXY(0,$yy+20);
$pdf->Cell(209.97,8,"����ǯ���������������",0,1,"C");//�������äѤ��Υ�������·��

//"�����ֹ�"��ɽ��
$pdf->SetXY($xx+165,$yy);
$pdf->Write(8,"�����ֹ桡");
//�ֹ��ɽ��
$pdf->SetTextColor(0,0,0);//�ե���Ȥ��������
$pdf->SetXY(0,$yy);
$pdf->Cell(204,8,"*******-1",0,1,"R");//�������äѤ��Υ���˱�·��

//����ι�����������ʬɽ��
$pdf->SetXY(0,$yy+20);
$pdf->Cell(209.97,8,"**����**����**��������",0,1,"C");//���·��

//��ҥ���ɽ��
$pdf->Image("../../image/company-rogo_clear.png",$xx+140,$yy+9,30);

//���̾ɽ��
$pdf->SetTextColor(77,66,177);//�ե���Ȥ��Ĥ�����
$pdf->SetFont('','B','12');//�ե���Ȥ�������12�ݥ���Ȥ�����
$pdf->SetXY(0,$yy+17);
$pdf->Cell(204,12,$bill[0],0,1,"R");//��·��

//��ɽ̾ɽ��
$pdf->SetFont('MS-PMincho','',9);//�ե���Ȥ�MS��P��ī�Ρ�9�ݥ���Ȥ�����
$pdf->SetXY(0,$yy+25);
$pdf->Cell(203,9,$bill[1],0,1,"R");//��·��
$pdf->SetFont('MS-PGothic','',6);//�ե���Ȥ�MS-P�����å��Ρ�6�ݥ���Ȥ�����
//���ꡢ�����ֹ�ɽ��
$pdf->SetXY(0,$yy+30.5);
$pdf->Cell(203,6,$bill[2],0,1,"R");//��·��
$pdf->SetXY(0,$yy+34);
$pdf->Cell(203,6,$bill[3],0,1,"R");//��·��

//����ʸɽ��
$pdf->SetFontSize(8);//�ե���Ȥ�8�ݥ���Ȥ�����
$pdf->SetXY(0,$yy+42);
$pdf->Cell(203,8,"�������̤�����´���夲�ޤ�",0,1,"R");//��·��

//�����襳����ɽ��
$pdf->SetTextColor(0,0,0);//�ե���Ȥ��������
$pdf->SetXY($xx,$yy+42);
$pdf->Cell(100,8,"******-****  ������������������ҡ���ʬ",0,1,"L");//��·��

$pdf->SetTextColor(77,66,177);//�ե���Ȥ��Ĥ�����
$pdf->SetFontSize(9);//�ե���Ȥ�9�ݥ���Ȥ�����
$cellname = array("����������","�����������","���ۻĹ��","���������","��������ǳ�","�ǹ�������","����������","5����ʧ��");
//��������ɽ��
for($i=0;$i<8;$i++){
    $op = 0;
    if($i<6){
        $pdf->SetFillColor(204,230,255);//�ɤ�Ĥ֤��ο�������������忧��
        $pdf->SetDrawColor(0,0,0);//���ο����������
    }else{
        $pdf->SetFillColor(77,66,177);//�ɤ�Ĥ֤��ο���������ġ�
        $pdf->SetDrawColor(77,66,177);//���ο����Ĥ�����
        $pdf->SetTextColor(255,255,255);//�ե���Ȥ��������
    }
    if($i == 7){
        $op = 1;
    }
    $pdf->RoundedRect($xx+($i*24)+$op,$yy+50,24,5,2,'DF','12');//��γѤ��ݤ����������
    $pdf->SetXY($xx+($i*24)+$op,$yy+50.5);//������֤���Ĵ��
    $pdf->Cell(24,5,$cellname[$i],0,1,'C');//���ɽ��//���ɽ��
    $pdf->SetFillcolor(255,255,255);//�ɤ�Ĥ֤��ο�����������
    $pdf->RoundedRect($xx+($i*24)+$op,$yy+55,24,10,2,'',34);//�����ݤ�Ĺ����������
}

$pdf->SetTextColor(180);//�ե���Ȥ򳥿�������
$pdf->SetXY($xx+2,$yy+66);
$pdf->Cell(150,5,"����ʬ������� ��0��������ʬ��Ĺ� ��0��������Ĵ���ۡ� 0�������������⡡0",0);

/*�������ޤǤ��إå����Ȼפ�����ʬ��*/

/*����������ơ��֥롡*/
//�ơ��֥�Υإå���ɽ��
$pdf->SetDrawColor(0,0,0);//���ο����������
$pdf->SetFillColor(204,230,255);//�ɤ�Ĥ֤��ο�������������忧��
$pdf->SetTextColor(0,0,135);//�ե���Ȥ��Ĥ�����
$pdf->RoundedRect($xx,$yy+73,18,8,2.5,'DF',1);//���夬�ݤ�����
$pdf->SetXY($xx,$yy+73);//�����٤˰��ֻ���
$pdf->Cell(16,8,"���",0,0,'C');//��������
$pdf->Cell(15,8,"��ɼ�ֹ�",1,0,'C',1);//��ɼ�ֹ楻��
$pdf->MultiCell(9,4,"���\n��ʬ",1,"C",1);//��ʬ����
$pdf->SetXY($xx+40,$yy+73);//�����٤˰��ֻ���
$pdf->Cell(80,8,"�������ʡ���̾",1,0,'C',1);//����̾����
$pdf->Cell(12,8,"������",1,0,'C',1);//���̥���
$pdf->Cell(10,8,"ñ��",1,0,'C',1);//ñ�̥���
$pdf->Cell(16,8,"ñ����",1,0,'C',1);//ñ������
$pdf->Cell(12,8,"�⡡��",1,0,'C',1);//��ۥ���
$pdf->Cell(10,8,"�Ƕ�ʬ",1,0,'C',1);//�Ƕ�ʬ����
$pdf->RoundedRect($xx+180,$yy+73,13,8,2.5,'DF',2);//���夬�ݤ�����
$pdf->Cell(13,8,"������",0,0,'C');//���⥻��

$pdf->SetXY($xx,$yy+81);//���ιԤ˰��ֻ���
$celco = 0;//�ɤ�Ĥ֤��ο���0:��1:�忧

$tabxx = $xx;//�ơ��֥�κ�ü
$tabyy = $yy+81;//�ơ��֥�ξ�ü
//����for�ǥơ��֥���Ȥ�ɽ��
for($i=0;$i<$keta;$i++){//$keta�󷫤��֤�
    if($celco==0){//���ѿ���0�ξ��
        $pdf->SetFillColor(255,255,255);//����ɤ�Ĥ֤�
        $celco=1;//���Ͽ忧
    }else{
        $pdf->SetFillColor(235,240,255);//�忧���ɤ�Ĥ֤�
        $celco=0;//������
    }
    if($i==$keta-1){//�Ǹ�ιԤ�ü�Υ����ݤ�����
        $pdf->RoundedRect($xx,$tabyy,16,5,2.5,'DF',4);//����
        $pdf->SetXY($xx+16,$tabyy);
        $pdf->Cell(15,5,"",1,0,'C',1);//��ɼ�ֹ�
        $pdf->Cell(9,5,"",1,0,"C",1);//�����ʬ
        $pdf->Cell(80,5,"",1,0,'C',1);//����̾
        $pdf->Cell(12,5,"",1,0,'C',1);//����
        $pdf->Cell(10,5,"",1,0,'C',1);//ñ��
        $pdf->Cell(16,5,"",1,0,'C',1);//ñ��
        $pdf->Cell(12,5,"",1,0,'C',1);//���
        $pdf->Cell(10,5,"",1,0,'C',1);//�Ƕ�ʬ
        $pdf->RoundedRect($xx+180,$tabyy,13,5,2.5,'DF',3);//����
    }else{
        $pdf->Cell(16,5,"",1,0,'C',1);//����
        $pdf->Cell(15,5,"",1,0,'C',1);//��ɼ�ֹ�
        $pdf->Cell(9,5,"",1,0,"C",1);//�����ʬ
        $pdf->Cell(80,5,"",1,0,'C',1);//����̾
        $pdf->Cell(12,5,"",1,0,'C',1);//����
        $pdf->Cell(10,5,"",1,0,'C',1);//ñ��
        $pdf->Cell(16,5,"",1,0,'C',1);//ñ��
        $pdf->Cell(12,5,"",1,0,'C',1);//���
        $pdf->Cell(10,5,"",1,0,'C',1);//�Ƕ�ʬ
        $pdf->Cell(13,5,"",1,0,'C',1);//����
    }
    //����������
    $pdf->SetDash(0.5,0.5);
    //���̥��������
    $pdf->Line(134,$tabyy,134,$tabyy+5);
    $pdf->Line(138,$tabyy,138,$tabyy+5);
    //ñ�����������
    $pdf->Line(157,$tabyy,157,$tabyy+5);
    $pdf->Line(161,$tabyy,161,$tabyy+5);
    //��ۥ��������
    $pdf->Line(172,$tabyy,172,$tabyy+5);
    $pdf->Line(176,$tabyy,176,$tabyy+5);
    //���⥻�������
    $pdf->Line(194.5,$tabyy,194.5,$tabyy+5);
    $pdf->Line(199,$tabyy,199,$tabyy+5);

    //��������
    $pdf->SetDash();
    //ñ�������ľ��
    $pdf->Line(165,$tabyy,165,$tabyy+5);

    $tabyy += 5;//�Ԥι⤵��­��
    $pdf->SetXY($tabxx,$tabyy);//���ιԤذ��֤�����
}
//�����ޤǤ��ơ��֥�ɽ��

//��Ԥ�ɽ��������᥽�å�
//��������ɼ�ֹ桢�����ʬ������̾������̾ɽ�����֡����̡�ñ�̡�ñ������ۡ��Ƕ�ʬ�����⡢���ֹ�
function RecWrite($md,$num,$kub,$name,$lrc,$amo,$unit,$uprice,$price,$zkub,$reci,$gyo,$pdf){
    $colyy = 91+($gyo-1)*6;//�ơ��֥�κǾ�������׻��ʰ��6mm��
    $pdf->SetXY(10,$colyy);//��������
    $pdf->Cell(16,6,$md,0,0,'C');//����
    $pdf->Cell(15,6,$num,0,0,'C');//��ɼ�ֹ�
    $pdf->Cell(9,6,$kub,0,0,'C');//�����ʬ
    $pdf->Cell(80,6,$name,0,0,$lrc);//����̾
    $pdf->Cell(12,6,$amo,0,0,'R');//����
    $pdf->Cell(10,6,$unit,0,0,'C');//ñ��
    $pdf->Cell(14,6,$uprice,0,0,'R');//ñ��
    $pdf->Cell(14,6,$price,0,0,'R');//���
    $pdf->Cell(10,6,$zkub,0,0,'C');//�Ƕ�ʬ
    $pdf->Cell(13,6,$reci,0,0,'R');//����
}

/*�������ޤǤ��ơ��֥롡*/


/*����������եå��Ȼפ�����ʬ��*/
$pdf->SetXY($xx,$tabyy+2);
$pdf->SetTextColor(77,66,177);
$pdf->SetFontSize(6);
$pdf->Write(3,"�����ԡ�");
$pdf->SetXY($xx+11,$tabyy+10);
$pdf->Cell(75,3,$bill[4],0);
$pdf->Cell(35,3,$bill[5]);
$pdf->SetXY($xx+11,$tabyy+13);
$pdf->Cell(75,3,$bill[6],0);
$pdf->Cell(35,3,$bill[7]);
$pdf->SetXY($xx+11,$tabyy+16);
$pdf->Cell(75,3,$bill[8],0);
$pdf->Cell(35,3,$bill[9]);
$pdf->SetXY($xx+11,$tabyy+19);
$pdf->Cell(75,3,$bill[10],0);
$pdf->Cell(35,3,$bill[11]);
$pdf->SetXY($xx+11,$tabyy+22);
$pdf->SetFontSize(8);
$pdf->Cell(150,6,$bill[12]);

$pdf->SetFillColor(204,230,255);
$pdf->RoundedRect($xx+150-1,$tabyy+2,4,15,1.5,'DF',14);
$pdf->SetXY($xx+150-1,$tabyy+3);
$pdf->MultiCell(4,2.5,"ô\n��\n��",0,'C');
$pdf->RoundedRect($xx+150+4-1,$tabyy+2,25,15,1.5,'',23);
$pdf->RoundedRect($xx+150+4+25-1,$tabyy+2,15,4,1.5,'DF',12);
$pdf->SetXY($xx+150+4+25-1,$tabyy+2);
$pdf->Cell(15,4,"��",0,0,'C');
$pdf->RoundedRect($xx+150+4+25-1,$tabyy+2+4,15,11,1.5,'',34);
/*�������ޤǤ��եå��Ȼפ�����ʬ��*/


$pdf->Output();
?>