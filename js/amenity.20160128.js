/**
 * @fileoverview アメニティ共通JavaScript
 *
 * ローカルルール
 * ・既存の関数名使っちゃ駄目
 * ・ファイルを開いたまま離席しちゃ駄目
 * ・エラーを出したまま離席しちゃ駄目
 * ・汎用性のない関数はそれぞれのPHPスクリプト内（もしくは別jsファイル）に
 * ・ほとんど処理が同じなのに、2とか3とか名前をつけて関数を増やしてファイルを重くしない
 *   （関数側の変更で済む場合は、関数側で対応する）
 * ・使わなくなった関数は消す（コメントアウトする）
 *
 *
 * @author
 * @version
 *
 */
/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2015/04/03                  amano  Dialogue、Button_Submit関数でボタン名が送られない IE11 バグ対応
 *  2015/05/12                  amano  Button_Submit_1関数でボタン名が送られない IE11 バグ対応
 *  2015/07/05                  amano  onForm_today 年を正しく取得できるように修正
 *  2016/01/13                  amano  Resrv_Form_NextToday, onForm_today2, onForm2, Comp_Form_NextToday, Comp_Form_Today 年を正しく取得できるように修正
 *  2016/01/28                  amano  IE11 では chgKeycode でキーコードをtabに変換できないので、次の要素を探すように修正
 */

/*
フレーム内のタイトルを親に表示する。
*/
top.document.title = document.title;



/*****************商品グループ設定*********************************/
//追加リンク押下処理
function insert_row(hidden){
    //追加リンクが押された
    document.dateForm.elements[hidden].value = true;
    document.dateForm.target="_self";
    document.dateForm.submit();
}

//変更リンク押下処理
function update_row(str_check,hidden,row){
    document.dateForm.elements[hidden].value = row;
    document.dateForm.target="_self";
    document.dateForm.submit();
}

/****************************日付入力*******************************/

//年　月　週　曜日テキストの自動フォーカス移動
//引数（フォーム,グループ名,移動元,移動先,移動先識別番号）
function move_text(me,main,name,next,num){

    //年→月
    if(num==1){
        var Y = main+"["+name+"]";
        var M = main+"["+next+"]";

        len = me.elements[Y].value.length;
        if( 4==len){
            me.elements[M].focus();
        }
    //月→週
    }else if(num==2){
        var M = main+"["+name+"]";
        var W = main+"["+next+"]";

        len = me.elements[M].value.length;
        if( 2==len){
            me.elements[W].focus();
        }
    //週→曜日
    }else if(num==3){
        var W = main+"["+name+"]";
        var D = main+"["+next+"]";
        len = me.elements[W].value.length;
        if( 1==len){
            me.elements[D].focus();
        }
    }
}

//日付から曜日を取得
function date_week(me,y_name,m_name,d_name,week){
    myTbl     = new Array("日","月","火","水","木","金","土");
    var y = me.elements[y_name].value;
    var m = me.elements[m_name].value;
    var d = me.elements[d_name].value;
   
    //年月日の入力値判定 
    if(y != "" && m != "" && d != ""){
        myD       = new Date(y,m-1,d);
        myDay     = myD.getDay();
        myweek   = myTbl[myDay];

        if(myweek != undefined){
            me.elements[week].value = myweek;
        }
    }else{
        me.elements[week].value = "";
    }
}

/**
 * 売上日を変更すると請求日も変更する
 * 引数のフォーム名は
 *   年月日のフォームを[y][m][d]でAddGroupしている前提
 * 
 * @param   {String}    source_date     元のフォーム名
 * @param   {String}    change_date     変更されるフォーム名
 * 
 */
function Claim_Day_Change(source_date, change_date){

    SY = source_date + "[y]";
    SM = source_date + "[m]";
    SD = source_date + "[d]";
    CY = change_date + "[y]";
    CM = change_date + "[m]";
    CD = change_date + "[d]";

    if(document.dateForm.elements[SY].value != "" && document.dateForm.elements[SM].value != "" && document.dateForm.elements[SD].value != ""){
        document.dateForm.elements[CY].value = document.dateForm.elements[SY].value;
        document.dateForm.elements[CM].value = document.dateForm.elements[SM].value;
        document.dateForm.elements[CD].value = document.dateForm.elements[SD].value;
    }

    return true;

}


/**********************TOP・SUBメニュー処理***************************/

//トップメニューがクリックされた場合は、クッキ情報を送る
//サブメニューがクリックされた場合は、引数にオブジェクトの"id"を設定することで、
//ツリー構造メニューの開閉を行う
//引数（メニュー項目,本部・FC識別,サブメニュー名,top・sub識別）
function TS_Menu(menu,where_menu,tName,ts_menu) {
    //本部・FC識別
    if(where_menu == 'head'){
        /******本部******/
        switch (menu){
            case 'sale':
                data_menu = new Array("受注取引","売上取引","請求管理","入金管理","実績管理");
                break;
            case 'buy':
                data_menu = new Array("発注取引","仕入取引","支払管理","実績管理");
                break;
            case 'stock':
                data_menu = new Array("在庫取引","棚卸管理");
                break;
            case 'renew':
                data_menu = new Array("更新管理");
                break;
            case 'system':
                //data_menu = new Array("マスタ管理","システム設定");
                data_menu = new Array("マスタ管理","帳票設定","システム設定");
                break;
            case 'analysis':
                data_menu = new Array("統計情報","月例販売精算書","CSV出力");
                break;
            default:
        }
    }else{
    /******FC******/
        switch (menu){
            case 'sale':
                data_menu = new Array("予定取引","売上取引","請求管理","入金管理","実績管理");
                break;
            case 'buy':
                data_menu = new Array("発注取引","仕入取引","支払管理","実績管理");
                break;
            case 'stock':
                data_menu = new Array("在庫取引","棚卸管理");
                break;
            case 'renew':
                data_menu = new Array("更新管理");
                break;
            case 'system':
                //data_menu = new Array("マスタ管理","システム設定");
                data_menu = new Array("マスタ管理","帳票設定","システム設定");
                break;
            case 'analysis':
                data_menu = new Array("統計情報","CSV出力");
                break;
            default:
        }
    }
    //有効期限の計算をします。
    var today = new Date();
    today.setHours(today.getHours() + 1);

    //TOP・SUBメニュー判定
    if(ts_menu == "top"){
        //TopMenu処理
        //メニュー項目数分判別
        for(var i=0;i<data_menu.length;i++){
            var j = i + 1;
            var str = "history"+j;
            var str2 = "history_flg"+j;

            //展開されたメニューは何か
            if(tName == data_menu[i]){
                //展開したメニュー名
                document.cookie = str + "=" + j + "; expires=" + today.toGMTString() + ", path=/";
                //展開・閉じる識別フラグ
                document.cookie = str2 + "=" + 'true' + "; expires=" + today.toGMTString() + ", path=/";
            }
        }
    }else{
        //SubMenu処理
        //スタイルオブジェクト取得
        tMenu = document.getElementById(tName).style;
        imgName = tName+'Img';

        //サブメニューがクリックされたか
        if(tMenu.display=='none'){
            //メニューを展開する
            tMenu.display='block';
            document.getElementById(imgName).src='../../../image/minus.png';
            //メニュー項目数分判別
            for(var i=0;i<data_menu.length;i++){
                var j = i + 1;
                var str = "history"+j;
                var str2 = "history_flg"+j;
                //展開されたメニューは何か
                if(tName == data_menu[i]){
                    //展開したメニュー名
                    document.cookie = str + "=" + j + "; expires=" + today.toGMTString() + ", path=/";
                    //展開・閉じる識別フラグ
                    document.cookie = str2 + "=" + 'true' + "; expires=" + today.toGMTString() + ", path=/";
                }
            }                                      
        }else{
            //メニューを閉じる
            tMenu.display='none';
            document.getElementById(imgName).src='../../../image/plus.png';
            for(var i=0;i<data_menu.length;i++){
                var j = i + 1;
                var str = "history"+j;
                var str2 = "history_flg"+j;
                //閉じられたメニューは何か
                if(tName == data_menu[i]){
                    document.cookie = str + "=" + j + "; expires=" + today.toGMTString() + ", path=/";
                    //展開・閉じる識別フラグ
                    document.cookie = str2 + "=" + 'false' + "; expires=" + today.toGMTString() + ", path=/";
                }
            }
        }
    }                       
}

//サブメニューがクリックされた場合、セッション情報を送る
function SubMenu2(tName) {
    document.dateForm.action=tName;
    document.dateForm.submit();
}

//メニューの色を付けるのに、リファラーをとばす
function Referer(tName) {
//  document.referer.action=tName;
//    document.referer.submit();
    location.href(tName);
}

//クッキー削除
function deleteCookie(){
    today = new Date;
    today.setHours(today.getHours() - 4); //4時間前に設定
    for(var i=1;i<6;i++){
        var str = "history"+i;
        var str2 = "history_flg"+i;
        document.cookie = str + "= taka; expires=" + today.toGMTString() + ", path=/";
        document.cookie = str2 + "= taka; expires=" + today.toGMTString() + ", path=/";
    }
}

/*********************ダイアログ表示************************/
//登録/変更ダイアログ表示
function Win_open(str_check,next){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        document.dateForm.action=next;
        document.dateForm.target="_blank";
        document.dateForm.submit();
        return true;
    }else{
        return false;
    }
}

// 登録/変更ダイアログ表示
function Dialogue(str_check,next,obj){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        if(str_check=="登録後発注書を発行します。"){
            window.open('../../head/buy/1-3-105.php','_blank','');
        }else{
        	//alert(obj.name);
        	if (null != obj) {
        		var element = document.createElement('input'); 
	        	element.type = "hidden"; 
	        	element.name = obj.name; 
	        	element.value = obj.value;
	        	document.dateForm.appendChild(element);
        	}
            document.dateForm.action=next;
            document.dateForm.target="_self";
            document.dateForm.submit();
        }
        return true;
    }else{
        return false;
    }
}

// 登録/変更ダイアログ表示
function Dialogue2(str_check,next,obj){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        if(str_check=="登録後発注書を発行します。"){
            window.open('2-3-105.php','_blank','');
        }else if(str_check=="変則日を設定します。"){
            window.close();
        }else{
        	if (null != obj) {
        		var element = document.createElement('input'); 
	        	element.type = "hidden"; 
	        	element.name = obj.name; 
	        	element.value = obj.value;
	        	document.dateForm.appendChild(element);
        	}
            document.dateForm.action=next;
            document.dateForm.submit();
        }
        return true;
    }else{
        return false;
    }
}

function Dialogue3(str_check){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    if (res == true){
        window.close();
    }else{
        return false;
    }
}

function Dialogue4(str_check){
    if(window.confirm(str_check+"\nよろしいですか？")==true){
        return true;
    }else{
        return false;
    }
}
//リンクでsubmitする
function dialogue5(str_check){

    if(window.confirm(str_check+"\nよろしいですか？")==true){
        //POST情報を送信する
        document.dateForm.submit();
        return true;
    }else{
        return false;
    }

}

//リンク用ダイアログ表示
function Dialogue_1(str_check, row, hidden,obj){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        	if (null != obj) {
        		var element = document.createElement('input'); 
	        	element.type = "hidden"; 
	        	element.name = obj.name; 
	        	element.value = obj.value;
	        	document.dateForm.appendChild(element);
        	}
        var hdn = hidden;
        var next = '#';
        document.dateForm.elements[hdn].value = row;
        //同じウィンドウで遷移する
        document.dateForm.target="_self";
        //自画面に遷移する
        document.dateForm.action=next;
        //POST情報を送信する
        document.dateForm.submit();
             return true;
    }else{
             return false;
        }
}

function Dialogue_2(str_check, next, row, hidden,obj){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        	if (null != obj) {
        		var element = document.createElement('input'); 
	        	element.type = "hidden"; 
	        	element.name = obj.name; 
	        	element.value = obj.value;
	        	document.dateForm.appendChild(element);
        	}
        var hdn = hidden;
        document.dateForm.elements[hdn].value = row;
        //同じウィンドウで遷移する
        document.dateForm.target="_self";
        //自画面に遷移する
        document.dateForm.action=next;
        //POST情報を送信する
        document.dateForm.submit();
             return true;
    }else{
             return false;
        }
}

//削除リンクダイアログ
function Dialogue_3(str_check, row, hidden, row_num,obj){
    //確認ダイアログ表示
    res = window.confirm(str_check+"よろしいですか？");
    //選択分岐
    if (res == true){
        	if (null != obj) {
        		var element = document.createElement('input'); 
	        	element.type = "hidden"; 
	        	element.name = obj.name; 
	        	element.value = obj.value;
	        	document.dateForm.appendChild(element);
        	}
        var hdn = hidden;
        //画面内跳び先指定
        var next = '#'+row_num;
        document.dateForm.elements[hdn].value = row;
        //同じウィンドウで遷移する
        document.dateForm.target="_self";
        //遷移する
        document.dateForm.action=next;
        //POST情報を送信する
        document.dateForm.submit();
        return true;
    }
}

//削除リンク用ダイアログ表示
function Dialogue_4(str_check, row, hidden1, date, hidden2,obj){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        	if (null != obj) {
        		var element = document.createElement('input'); 
	        	element.type = "hidden"; 
	        	element.name = obj.name; 
	        	element.value = obj.value;
	        	document.dateForm.appendChild(element);
        	}
        var hdn1 = hidden1;
        var hdn2 = hidden2;
        var next = '#';
        document.dateForm.elements[hdn1].value = row;
        document.dateForm.elements[hdn2].value = date;
        //同じウィンドウで遷移する
        document.dateForm.target="_self";
        //自画面に遷移する
        document.dateForm.action=next;
        //POST情報を送信する
        document.dateForm.submit();
             return true;
    }else{
             return false;
        }
}

//削除リンク用ダイアログ表示
function Dialogue_5(str_check, row, hidden1, date, hidden2, next,obj){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        	if (null != obj) {
        		var element = document.createElement('input'); 
	        	element.type = "hidden"; 
	        	element.name = obj.name; 
	        	element.value = obj.value;
	        	document.dateForm.appendChild(element);
        	}
        var hdn1 = hidden1;
        var hdn2 = hidden2;
        document.dateForm.elements[hdn1].value = row;
        document.dateForm.elements[hdn2].value = date;
        //同じウィンドウで遷移する
        document.dateForm.target="_self";
        //自画面に遷移する
        document.dateForm.action=next;
        //POST情報を送信する
        document.dateForm.submit();
             return true;
    }else{
             return false;
    }
}

//ボタン用ダイアログ表示
//フォーカス時に、現在の日付を表示する
function onForm_today(which,me,form_y,form_m,form_d){
    if (document.all || document.getElementById){
        which.style.backgroundColor="#FDFD66"
    }
    today       = new Date();
    //Year    = today.getYear();
    //年を正しく取得できるように修正 2015-07-05
    Year    = today.getFullYear();
    Month   = today.getMonth()+1;
    Day     = today.getDate();
    var Y = form_y;
    var M = form_m;
    var D = form_d;
    //既に入力されているか
    if(me.elements[Y].value == "" && me.elements[M].value == "" && me.elements[D].value == ""){
        me.elements[Y].value = Year;
        me.elements[M].value = Month;
        me.elements[D].value = Day;
        //一桁なら0を付ける
        if(me.elements[M].value <= 9){
                me.elements[M].value = "0" + Month;
        }
        if(me.elements[D].value <= 9){
                me.elements[D].value = "0" + Day;
        }
    }
}


// フォーカス時に指定日の翌日の日付を表示
function Resrv_Form_NextToday(which, me, form, form_y, form_m, form_d, form_base, form_base_y, form_base_m, form_base_d){

    // フォーカス時はフォームの色を変える
    if (document.all || document.getElementById){
        which.style.backgroundColor="#FDFD66"
    }


    //基準日
    var Base_Y = form_base+"["+form_base_y+"]";
    var Base_M = form_base+"["+form_base_m+"]";
    var Base_D = form_base+"["+form_base_d+"]";
    var yy = document.dateForm.elements[Base_Y].value;
    var mm = document.dateForm.elements[Base_M].value;
    var dd = document.dateForm.elements[Base_D].value;
    mm = mm-1; //月は0?11なので形式を合わせる

    //基準日が入力されていない場合
    if (yy == "" || mm == "" || dd == ""){
        return;
    }

    today = new Date(yy,mm,dd);
    today.setDate (today.getDate () + 1); // 1日後に設定
    //Year    = today.getYear();
    //年を正しく取得できるように修正 2016-01-13
    Year    = today.getFullYear();
    Month = today.getMonth()+1;
    Day   = today.getDate();

    //値を戻すフォーム名
    var Y = form+"["+form_y+"]";
    var M = form+"["+form_m+"]";
    var D = form+"["+form_d+"]";


    // 入力状態なら日付補完
    if (me.elements[Y].value == "" && me.elements[M].value == "" && me.elements[D].value == ""){
    
        //妥当でない日付の場合は""を表示
        if(0<Year && Year<10000){
            me.elements[Y].value = Year;
        }else{
            me.elements[Y].value = "";
        }

        if(0<Month && Month<13){
            me.elements[M].value = Month;

            // 1桁なら0埋め
            if (me.elements[M].value <= 9){
                me.elements[M].value = "0" + Month;
            }            

        }else{
            me.elements[M].value = "";
        }
        
        if(0<Day && Day<32){
            me.elements[D].value = Day;

            // 1桁なら0埋め
            if (me.elements[D].value <= 9){
                me.elements[D].value = "0" + Day;
            }

        }else{
            me.elements[D].value = "";
        }


    }

}


//フォーカス時に、現在の年月を表示する
function onForm_today2(which,me,form_y,form_m){
    if (document.all || document.getElementById){
        which.style.backgroundColor="#FDFD66"
    }
    today       = new Date();
    //Year    = today.getYear();
    //年を正しく取得できるように修正 2016-01-13
    Year    = today.getFullYear();
    Month   = today.getMonth()+1;

    var Y = form_y;
    var M = form_m;
    
    //既に入力されているか
    if(me.elements[Y].value == "" && me.elements[M].value == ""){
        me.elements[Y].value = Year;
        me.elements[M].value = Month;
    
        //一桁なら0を付ける
        if(me.elements[M].value <= 9){
                me.elements[M].value = "0" + Month;
        }
    }
}

/*********************自動フォーカス***********************/


//フォーカス時に初期値を表示
//引数（formオブジェクト・項目・フォーカス移動文字数）
function Default_focus(me,name,next,length){
    len = name.value.length;
    if(me.elements[next].value == "")
    {
        me.elements[next].value = "00";
    }
    if(length==len){
        me.elements[next].focus();
    }
}

//フォーカス移動
function changeText(me,name,next,length,val){
    var F1 = name;
    var F2 = next;
    
    if(val != undefined)
    {
        if(me.elements[F2].value == "")
        {
            me.elements[F2].value = val;
        }
    }
    len = me.elements[F1].value.length;
    if(length==len){
        me.elements[F2].focus();
    }
}

//6-4桁テキストの自動フォーカス移動
function changeText1(me,num){
    var S = "f_code_a"+num+"[f_text6]";
    var E = "f_code_a"+num+"[f_text4]";
    len = me.elements[S].value.length;
    if(me.elements[E].value == "")
    {
        me.elements[E].value = "0000";
    }
    if( 6==len){
        me.elements[E].focus();
    }
}


//6-3桁テキストの自動フォーカス移動
function changeText2(me,num){
    var S = "f_code_b"+num+"[f_text6]";
    var E = "f_code_b"+num+"[f_text3]";
    len = me.elements[S].value.length;
    if( 6==len){
        me.elements[E].focus();
    }
}

//4文字-2文字-2文字の自動フォーカス移動
function changeText3(me,num){
    var Y = "f_date_a"+num+"[y_input]";
    var M = "f_date_a"+num+"[m_input]";
    len = me.elements[Y].value.length;
    if( 4==len){
        me.elements[M].focus();
    }
}
function changeText4(me,num){
    var M = "f_date_a"+num+"[m_input]";
    var D = "f_date_a"+num+"[d_input]";
    len = me.elements[M].value.length;
    if( 2<=len){
        me.elements[D].focus();
    }
}


//4文字-2文字-2文字?4文字-2文字-2文字の自動フォーカス移動
function changeText5(me,num){
    var Y = "f_date_b"+num+"[y_start]";
    var M = "f_date_b"+num+"[m_start]";
    len = me.elements[Y].value.length;
    if( 4==len){
        me.elements[M].focus();
    }
}
function changeText6(me,num){
    var M = "f_date_b"+num+"[m_start]";
    var D = "f_date_b"+num+"[d_start]";
    len = me.elements[M].value.length;
    if( 2<=len){
        me.elements[D].focus();
    }
}
function changeText7(me,num){
    var D = "f_date_b"+num+"[d_start]";
    var Y = "f_date_b"+num+"[y_end]";
    len = me.elements[D].value.length;
    if( 2<=len){
        me.elements[Y].focus();
    }
}
function changeText8(me,num){
    var Y = "f_date_b"+num+"[y_end]";
    var M = "f_date_b"+num+"[m_end]";
    len = me.elements[Y].value.length;
    if( 4==len){
        me.elements[M].focus();
    }
}
function changeText9(me,num){
    var M = "f_date_b"+num+"[m_end]";
    var D = "f_date_b"+num+"[d_end]";
    len = me.elements[M].value.length;
    if( 2<=len){
        me.elements[D].focus();
    }
}

function changeText18(me,num){
    var M = "f_time_a"+num+"[h_input]";
    var D = "f_time_a"+num+"[m_input]";
    len = me.elements[M].value.length;
    if( 2==len){
        me.elements[D].focus();
    }
}


//9文字.2文字の自動フォーカス移動
function changeText10(me,num){
    var S = "f_code_c"+num+"[f_text9]";
    var E = "f_code_c"+num+"[f_text2]";
    len = me.elements[S].value.length;
    if(me.elements[E].value == "")
    {
        me.elements[E].value = "00";
    }
    if( 9==len){
        me.elements[E].focus();
    }
}


//3文字-4文字の自動フォーカス移動
function changeText11(me,num){
    var S = "f_code_d"+num+"[f_text3]";
    var E = "f_code_d"+num+"[f_text4]";
    len = me.elements[S].value.length;
    if( 3==len){
        me.elements[E].focus();
    }
}

//9文字-9文字の自動フォーカス移動
function changeText12(me,num){
    var S = "f_code_e"+num+"[f_text9_1]";
    var E = "f_code_e"+num+"[f_text9_2]";
    len = me.elements[S].value.length;
    if( 9==len){
        me.elements[E].focus();
    }
}

//4-2桁テキストの自動フォーカス移動
function changeText13(me,num){
    var Y = "f_date_c"+num+"[y_input]";
    var M = "f_date_c"+num+"[m_input]";
    len = me.elements[Y].value.length;
    if( 4==len){
        me.elements[M].focus();
    }
}

//2-2桁テキストの自動フォーカス移動
function changeText14(me,num){
    var S = "f_code_f"+num+"[f_text2_1]";
    var E = "f_code_f"+num+"[f_text2_2]";
    len = me.elements[S].value.length;
    if( 2==len){
        me.elements[E].focus();
    }
}

//4文字-2文字?4文字-2文字の自動フォーカス移動
function changeText15(me,num){
    var Y = "f_date_d"+num+"[y_start]";
    var M = "f_date_d"+num+"[m_start]";
    len = me.elements[Y].value.length;
    if( 4==len){
        me.elements[M].focus();
    }
}
function changeText16(me,num){
    var M = "f_date_d"+num+"[m_start]";
    var Y = "f_date_d"+num+"[y_end]";
    len = me.elements[M].value.length;
    if( 2<=len){
        me.elements[Y].focus();
    }
}

function changeText17(me,num){
    var Y = "f_date_d"+num+"[y_end]";
    var M = "f_date_d"+num+"[m_end]";
    len = me.elements[Y].value.length;
    if( 4==len){
        me.elements[M].focus();
    }
}

//4-4桁テキストの自動フォーカス移動
function changeText18(me,num){
    var S = "f_code_g"+num+"[f_text4_1]";
    var E = "f_code_g"+num+"[f_text4_2]";
    len = me.elements[S].value.length;
    if( 4==len){
        me.elements[E].focus();
    }
}

//年月の自動フォーカス移動（グループ化していない）
function date_text(me,num){
    var S = "y_input"+num;
    var E = "m_input"+num;
    len = me.elements[S].value.length;
    if( 4==len){
        me.elements[E].focus();
    }
}

function changeText_staff(me){
    var code1 = "f_staff[code1]";
    var code2 = "f_staff[code2]";
    len = me.elements[code1].value.length;
    if(me.elements[code2].value == "")
    {
        me.elements[code2].value = "000";
    }
    if( 6==len){
        me.elements[code2].focus();
    }
}

function changeText_customer(me,num){
    if(num == undefined){
        var code1 = "f_customer[code1]";
        var code2 = "f_customer[code2]";
    }else{
        var code1 = "f_customer"+num+"[code1]";
        var code2 = "f_customer"+num+"[code2]";
    }
    len = me.elements[code1].value.length;
    if(me.elements[code2].value == "")
    {
        me.elements[code2].value = "0000";
    }
    if( 6==len){
        me.elements[code2].focus();
    }
}

function changeText_shop(me){
    var code1 = "f_shop[code1]";
    var code2 = "f_shop[code2]";
    len = me.elements[code1].value.length;
    if(me.elements[code2].value == "")
    {
        me.elements[code2].value = "0000";
    }
    if( 6==len){
        me.elements[code2].focus();
    }
}

//9文字.2文字の自動フォーカス移動
function input_price(me,num){
    var S = "form_price["+num+"][i]";
    var E = "form_price["+num+"][d]";
    len = me.elements[S].value.length;
    if(me.elements[E].value == "")
    {
        me.elements[E].value = "00";
    }
    if( 9==len){
        me.elements[E].focus();
    }
}

//4文字-2文字-2文字の自動フォーカス移動
function changedate1(me,num){
        var Y = "form_rank_date["+num+"][y]";
        var M = "form_rank_date["+num+"][m]";
        len = me.elements[Y].value.length;
        if( 4==len){
                me.elements[M].focus();
        }
}
function changedate2(me,num){
        var M = "form_rank_date["+num+"][m]";
        var D = "form_rank_date["+num+"][d]";
        le = me.elements[M].value.length;
        if( 2<=len){
                me.elements[D].focus();
        }
}

// 自動フォーカス移動
function Nextfocus(me, form, form1, form2, num){

    var S = form+"["+form1+"]";
    var E = form+"["+form2+"]";
    len = me.elements[S].value.length;
    if(num==len){
        me.elements[E].focus();
        me.elements[E].select();
    }

}

// 自動フォーカス移動
// 移動先フォームにテキスト補完（任意）
// 移動先テキストを選択
function Next_Focus(me, form, form1, form2, num, val){

    var F1 = form+"["+form1+"]";
    var F2 = form+"["+form2+"]";
    if (val != undefined){
        if (me.elements[F2].value == ""){
            me.elements[F2].value = val;
        }
    }
    len = me.elements[F1].value.length;
    if (num == len){
        me.elements[F2].focus();
        me.elements[F2].select();
    }

}

//演算結果の誤差をなくす
function trimFixed(a) {
    var x = "" + a;
    var m = 0;
    var e = x.length;
    for (var i = 0; i < x.length; i++) {
        var c = x.substring(i, i + 1);
        if (c >= "0" && c <= "9") {
            if (m == 0 && c == "0") {
            } else {
                m++;
            }
        } else if (c == " " || c == "+" || c == "-" || c == ".") {
        } else if (c == "E" || c == "e") {
            e = i;
            break;
        } else {
            return a;
        }
    }

    var b = 1.0 / 3.0;
    var y = "" + b;
    var q = y.indexOf(".");
    var n;
    if (q >= 0) {
        n = y.length - (q + 1);
    } else {
        return a;
    }

    if (m < n) {
        return a;
    }

    var p = x.indexOf(".");
    if (p == -1) {
        return a;
    }
    var w = " ";
    for (var i = e - (m - n) - 1; i >= p + 1; i--) {
        var c = x.substring(i, i + 1);
        if (i == e - (m - n) - 1) {
            continue;
        }
        if (i == e - (m - n) - 2) {
            if (c == "0" || c == "9") {
                w = c;
                continue;
            } else {
                return a;
            }
        }
        if (c != w) {
            if (w == "0") {
                var z = (x.substring(0, i + 1) + x.substring(e, x.length)) - 0;
                return z;
            } else if (w == "9") {
                var z = (x.substring(0, i) + ("" + ((c - 0) + 1)) + x.substring(e, x.length)) - 0;
                return z;
            } else {
                return a;
            }
        }
    }
    if (w == "0") {
        var z = (x.substring(0, p) + x.substring(e, x.length)) - 0;
        return z;
    } else if (w == "9") {
        var z = x.substring(0, p) - 0;
        var f;
        if (a > 0) {
            f = 1;
        } else if (a < 0) {
            f = -1;
        } else {
            return a;
        }
        var r = (("" + (z + f)) + x.substring(e, x.length)) - 0;
        return r;
    } else {
        return a;
    }
}

//数字をカンマで区切る
function myFormatNumber(x) { 
    var s = "" + x; //文字列型に変換する。
    var p = s.indexOf("."); // 小数点の位置を0オリジンで求める。
    if (p < 0) { // 小数点が見つからなかった時
        p = s.length; // 仮想的な小数点の位置とする
    }
    var r = s.substring(p, s.length); // 小数点の桁と小数点より右側の文字列
    for (var i = 0; i < p; i++) { // (10 ^ i) の位について
        var c = s.substring(p - 1 - i, p - 1 - i + 1); // (10 ^ i) の位のひとつの桁の数字。
        if (c < "0" || c > "9") { // 数字以外のもの(符合など)が見つかった
            r = s.substring(0, p - i) + r; // 残りを全部付加する
            break;
        }
        if (i > 0 && i % 3 == 0) { // 3 桁ごと、ただし初回は除く
            r = "," + r; // カンマを付加する
        }
        r = c + r; // 数字を一桁追加する。
    }
    return r; // 例では "95,839,285,734.3245"
}

//2010-05-14 hashimoto-y
//端数処理関数　切捨て
function Amenity_floor(price){
    var PI = price;

    //正数の場合
    if( PI >= 0){
        PI = Math.floor(PI);
    //負数の場合
    }else{
        PI = Math.ceil(PI);
    }
    return PI;
}

//2010-05-14 hashimoto-y
//端数処理関数　四捨五入
//負数のroundの挙動は五捨六入となるため小数点以下の値をみて処理する
function Amenity_round(price,price_d){
    var PI = price;
    var PD = price_d;

    //正数の場合
    if( PI >= 0){
        PI = Math.round(PI);
    //負数の場合
    }else{
        if( Number(0+"."+PD) < 0.5 ){
            PI = Math.ceil(PI);
        }else{
            PI = Math.floor(PI);
        }
    }
    return PI;
}

//2010-05-14 hashimoto-y
//端数処理関数　切上
function Amenity_ceil(price){
    var PI = price;

    //正数の場合
    if( PI >= 0){
        PI = Math.ceil(PI);
    //負数の場合
    }else{
        PI = Math.floor(PI);
    }
    return PI;
}

//税抜き・消費税・税込計算
function Tax_Cal(goods_id,order_num,price_i,price_d,coax,buy_amount,tax_franct,tax_amount,buy_price){
    var HG = goods_id;
    var ON = order_num;
    var PI = price_i;
    var PD = price_d;
    var BA = buy_amount;
    var TA = tax_amount;
    var BP = buy_price;

    //hiddenの商品IDがあるか
    if(document.dateForm.elements[HG].value != ""){
        document.dateForm.elements[BA].value = document.dateForm.elements[ON].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
        //切捨ての場合
        if(coax == '1'){
            document.dateForm.elements[BA].value = Math.floor(document.dateForm.elements[BA].value * 100)/100;
        //四捨五入の場合
        }else if(coax == '2'){
            document.dateForm.elements[BA].value = Math.round(document.dateForm.elements[BA].value * 100)/100;
        //切上げの場合
        }else if(coax == '3'){
            document.dateForm.elements[BA].value = Math.ceil(document.dateForm.elements[BA].value * 100)/100;
        }
        
        //小数点以下を省略しない(０埋め)
        decimal = document.dateForm.elements[BA].value.indexOf(".",0); 
        len = document.dateForm.elements[BA].value.length;
        if(decimal == -1){
            document.dateForm.elements[BA].value = document.dateForm.elements[BA].value+'.00';
        }else if(len == decimal+2){
            document.dateForm.elements[BA].value = document.dateForm.elements[BA].value+'0';
        }
        
        //数字でない場合は空を返す
        if(isNaN(document.dateForm.elements[BA].value) == true){
            document.dateForm.elements[BA].value = "";
        }
        
        document.dateForm.elements[TA].value = eval(document.dateForm.elements[ON].value * (eval(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value))) * 0.05;
        //切捨ての場合
        if(tax_franct == '1'){
            document.dateForm.elements[TA].value = Math.floor(document.dateForm.elements[TA].value * 100)/100;
        //四捨五入の場合
        }else if(tax_franct == '2'){
            document.dateForm.elements[TA].value = Math.round(document.dateForm.elements[TA].value * 100)/100;
        //切上げの場合
        }else if(tax_franct == '3'){
            document.dateForm.elements[TA].value = Math.ceil(document.dateForm.elements[TA].value * 100)/100;
        }
        
        //小数点以下を省略しない(０埋め)
        decimal = document.dateForm.elements[TA].value.indexOf(".",0); 
        len = document.dateForm.elements[TA].value.length;
        if(decimal == -1){
            document.dateForm.elements[TA].value = document.dateForm.elements[TA].value+'.00';
        }else if(len == decimal+2){
            document.dateForm.elements[TA].value = document.dateForm.elements[TA].value+'0';
        }
        
        //数字でない場合は空を返す
        if(isNaN(document.dateForm.elements[TA].value) == true){
            document.dateForm.elements[TA].value = "";
        }
        
        document.dateForm.elements[BP].value = eval(document.dateForm.elements[BA].value) + eval(document.dateForm.elements[TA].value);
        
        //小数点以下を省略しない(０埋め)
        decimal = document.dateForm.elements[BP].value.indexOf(".",0); 
        len = document.dateForm.elements[BP].value.length;
        if(decimal == -1){
            document.dateForm.elements[BP].value = document.dateForm.elements[BP].value+'.00';
        }else if(len == decimal+2){
            document.dateForm.elements[BP].value = document.dateForm.elements[BP].value+'0';
        }
        
        //数字でない場合は空を返す
        if(isNaN(document.dateForm.elements[BP].value) == true){
            document.dateForm.elements[BP].value = "";
        }
        document.dateForm.elements[BP].value = trimFixed(document.dateForm.elements[BP].value);
        document.dateForm.elements[TA].value = myFormatNumber(document.dateForm.elements[TA].value);
        document.dateForm.elements[BA].value = myFormatNumber(document.dateForm.elements[BA].value);
        document.dateForm.elements[BP].value = myFormatNumber(document.dateForm.elements[BP].value);
        
        return true;
    }else{
        return false;
    }
}

//税抜き・消費税・税込計算(値が二つ)
function Tax_Cal2(goods_id,sale_num,s_price_i,s_price_d,coax,sale_amount,c_price_i,c_price_d,cost_amount,tax_franct,tax_amount,total_price){

    var HG  = goods_id;
    var ON  = sale_num;
    var PI  = s_price_i;
    var PD  = s_price_d;
    var BA  = sale_amount;
    var PI2 = c_price_i;
    var PD2 = c_price_d;
    var BA2 = cost_amount;
    var TA  = tax_amount;
    var BP  = total_price;
    //消費税率
    var tax_value = 0.05;

    //hiddenの商品IDがあるか
    if(document.dateForm.elements[HG].value != ""){
        //計算１
        document.dateForm.elements[BA].value = document.dateForm.elements[ON].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
        //切捨ての場合
        if(coax == '1'){
            document.dateForm.elements[BA].value = Math.floor(document.dateForm.elements[BA].value * 100)/100;
        //四捨五入の場合
        }else if(coax == '2'){
            document.dateForm.elements[BA].value = Math.round(document.dateForm.elements[BA].value * 100)/100;
        //切上げの場合
        }else if(coax == '3'){
            document.dateForm.elements[BA].value = Math.ceil(document.dateForm.elements[BA].value * 100)/100;
        }
        
        //小数点以下を省略しない(０埋め)
        decimal = document.dateForm.elements[BA].value.indexOf(".",0); 
        len = document.dateForm.elements[BA].value.length;
        if(decimal == -1){
            document.dateForm.elements[BA].value = document.dateForm.elements[BA].value+'.00';
        }else if(len == decimal+2){
            document.dateForm.elements[BA].value = document.dateForm.elements[BA].value+'0';
        }
        
        //数字でない場合は空を返す
        if(isNaN(document.dateForm.elements[BA].value) == true){
            document.dateForm.elements[BA].value = "";
        }

        //計算２
        document.dateForm.elements[BA2].value = document.dateForm.elements[ON].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
        //切捨ての場合
        if(coax == '1'){
            document.dateForm.elements[BA2].value = Math.floor(document.dateForm.elements[BA2].value * 100)/100;
        //四捨五入の場合
        }else if(coax == '2'){
            document.dateForm.elements[BA2].value = Math.round(document.dateForm.elements[BA2].value * 100)/100;
        //切上げの場合
        }else if(coax == '3'){
            document.dateForm.elements[BA2].value = Math.ceil(document.dateForm.elements[BA2].value * 100)/100;
        }
        
        //小数点以下を省略しない(０埋め)
        decimal = document.dateForm.elements[BA2].value.indexOf(".",0); 
        len = document.dateForm.elements[BA2].value.length;
        if(decimal == -1){
            document.dateForm.elements[BA2].value = document.dateForm.elements[BA2].value+'.00';
        }else if(len == decimal+2){
            document.dateForm.elements[BA2].value = document.dateForm.elements[BA2].value+'0';
        }
        
        //数字でない場合は空を返す
        if(isNaN(document.dateForm.elements[BA2].value) == true){
            document.dateForm.elements[BA2].value = "";
        }

        //消費税＆税込計
        document.dateForm.elements[TA].value = eval(document.dateForm.elements[ON].value * (eval(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value))) * tax_value;
        //切捨ての場合
        if(tax_franct == '1'){
            document.dateForm.elements[TA].value = Math.floor(document.dateForm.elements[TA].value * 100)/100;
        //四捨五入の場合
        }else if(tax_franct == '2'){
            document.dateForm.elements[TA].value = Math.round(document.dateForm.elements[TA].value * 100)/100;
        //切上げの場合
        }else if(tax_franct == '3'){
            document.dateForm.elements[TA].value = Math.ceil(document.dateForm.elements[TA].value * 100)/100;
        }
        
        //小数点以下を省略しない(０埋め)
        decimal = document.dateForm.elements[TA].value.indexOf(".",0); 
        len = document.dateForm.elements[TA].value.length;
        if(decimal == -1){
            document.dateForm.elements[TA].value = document.dateForm.elements[TA].value+'.00';
        }else if(len == decimal+2){
            document.dateForm.elements[TA].value = document.dateForm.elements[TA].value+'0';
        }
        
        //数字でない場合は空を返す
        if(isNaN(document.dateForm.elements[TA].value) == true){
            document.dateForm.elements[TA].value = "";
        }
        
        document.dateForm.elements[BP].value = eval(document.dateForm.elements[BA].value) + eval(document.dateForm.elements[TA].value);
        
        //小数点以下を省略しない(０埋め)
        decimal = document.dateForm.elements[BP].value.indexOf(".",0); 
        len = document.dateForm.elements[BP].value.length;
        if(decimal == -1){
            document.dateForm.elements[BP].value = document.dateForm.elements[BP].value+'.00';
        }else if(len == decimal+2){
            document.dateForm.elements[BP].value = document.dateForm.elements[BP].value+'0';
        }
        
        //数字でない場合は空を返す
        if(isNaN(document.dateForm.elements[BP].value) == true){
            document.dateForm.elements[BP].value = "";
        }
        document.dateForm.elements[BP].value = trimFixed(document.dateForm.elements[BP].value);
        document.dateForm.elements[TA].value = myFormatNumber(document.dateForm.elements[TA].value);
        document.dateForm.elements[BA].value = myFormatNumber(document.dateForm.elements[BA].value);
        document.dateForm.elements[BP].value = myFormatNumber(document.dateForm.elements[BP].value);
        
        return true;
    }else{
        return false;
    }
}

//小数と整数の乗算
function Mult(id,num,price_i,price_d,amount,coax){

    var GI  = id;
    var SN  = num;
    var PI  = price_i;
    var PD  = price_d;
    var SA  = amount;

    //hiddenの商品IDがあるか
    if(document.dateForm.elements[GI].value != ""){
        var str  = document.dateForm.elements[PI].value;
        var str2 = document.dateForm.elements[PD].value;
        if(isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD].value) == false && str2.search(/.*\..*/i) == -1){
            //計算１
            document.dateForm.elements[SA].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
        
            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA].value = Amenity_floor(document.dateForm.elements[SA].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA].value = Amenity_round(document.dateForm.elements[SA].value,document.dateForm.elements[PD].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA].value = Amenity_ceil(document.dateForm.elements[SA].value);
            }
            
            //数字ではない場合 or 数量が小数の場合 は空を返す
            var str = document.dateForm.elements[SN].value;
            if(isNaN(document.dateForm.elements[SA].value) == true || str.search(/.*\..*/i) != -1){
                document.dateForm.elements[SA].value = "";
            }

            document.dateForm.elements[SA].value = trimFixed(document.dateForm.elements[SA].value);
            document.dateForm.elements[SA].value = myFormatNumber(document.dateForm.elements[SA].value);
        }else{
            document.dateForm.elements[SA].value = "";
        }
        return true;
    }else{
        return false;
    }
}

/**
 * 単価×数量・一式＝合計 を計算する
 *
 * 契約マスタ、予定データ訂正、予定手書、手書伝票、売上伝票（訂正）あたりで使用
 *
 *  sale_num：数量のフォーム名
 *  c_price_i：単価（整数）のフォーム名
 *  c_price_d：単価（小数）のフォーム名
 *  cost_amount：合計を表示するフォーム名
 *  setn：一式チェックのフォーム名
 *  coax：まるめ区分
 *  place：内訳判定区分
 *  price_div：原価・売上判定区分（空だと原価、それ以外は売上）
 */
function Mult2(sale_num,c_price_i,c_price_d,cost_amount,setn,coax,place,price_div){

    var SE  = setn;

    var SN  = sale_num;

    var PI2 = c_price_i;
    var PD2 = c_price_d;
    var SA2 = cost_amount;

    //顧客先が選択されているか判定
    if(coax != ""){

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI2].value;
        var str2 = document.dateForm.elements[PD2].value;
        if(isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD2].value) == false && str2.search(/.*\..*/i) == -1){

            //デフォルトで、小数部に00を代入
            if(document.dateForm.elements[PI2].value != "" && isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD2].value == "" && place != true){
                document.dateForm.elements[PD2].value = "00";
            }

            //・一式○　数量×　は単価×１を丸めた金額を表示
            if((document.dateForm.elements[SE].checked == true || document.dateForm.elements[SE].value == '一式') && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));

            //・一式×　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));

            //・一式○　数量○　は単価×数量を丸めた金額を表示
            }else if((document.dateForm.elements[SE].checked == true || document.dateForm.elements[SE].value == '一式') && document.dateForm.elements[SN].value != ""){
                if(price_div == undefined){
                    //・原価の場合は単価×数量を丸めた金額を表示
                    document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                }else{
                    //・原価の場合は単価×数量を丸めた金額を表示
                    document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                }

            //・一式×　数量×　は0を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 0 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            }

            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
            }
            
            //・一式○　数量×か判定
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                //・一式○　数量×

                var str = document.dateForm.elements[SN].value;
                //数字ではない場合 or 数量が小数の場合 は空を返す
                if(isNaN(document.dateForm.elements[SA2].value) == true || str.search(/.*\..*/i) != -1){
                    document.dateForm.elements[SA2].value = "";
                }
            }else{
                //・一式×　数量○
                //・一式○　数量○

                //数字ではない場合
                if(isNaN(document.dateForm.elements[SA2].value) == true){
                    document.dateForm.elements[SA2].value = "";
                }
            }

            document.dateForm.elements[SA2].value = trimFixed(document.dateForm.elements[SA2].value);
            document.dateForm.elements[SA2].value = myFormatNumber(document.dateForm.elements[SA2].value);
        }else{
            document.dateForm.elements[SA2].value = "";
        }   
        return true;
    }else{
        return false;
    }
}

//小数と整数の乗算(計算が二つ)
function Mult_double(goods_id,sale_num,s_price_i,s_price_d,sale_amount,c_price_i,c_price_d,cost_amount,coax){

    var GI  = goods_id;
    var SN  = sale_num;

    var PI  = s_price_i;
    var PD  = s_price_d;
    var SA  = sale_amount;

    var PI2 = c_price_i;
    var PD2 = c_price_d;
    var SA2 = cost_amount;

    //hiddenの商品IDがあるか
    if(document.dateForm.elements[GI].value != ""){

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI].value;
        var str2 = document.dateForm.elements[PD].value;
        if(isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD].value) == false && str2.search(/.*\..*/i) == -1){
            //計算１
            document.dateForm.elements[SA].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
        
            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA].value = Amenity_floor(document.dateForm.elements[SA].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA].value = Amenity_round(document.dateForm.elements[SA].value,document.dateForm.elements[PD].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA].value = Amenity_ceil(document.dateForm.elements[SA].value);
            }
            
            //数字ではない場合 or 数量が小数の場合 は空を返す
            var str = document.dateForm.elements[SN].value;
            if(isNaN(document.dateForm.elements[SA].value) == true || str.search(/.*\..*/i) != -1){
                document.dateForm.elements[SA].value = "";
            }
            document.dateForm.elements[SA].value = trimFixed(document.dateForm.elements[SA].value);
            document.dateForm.elements[SA].value = myFormatNumber(document.dateForm.elements[SA].value);
        }else{
            document.dateForm.elements[SA].value = "";
        }

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI2].value;
        var str2 = document.dateForm.elements[PD2].value;
        if(isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD2].value) == false && str2.search(/.*\..*/i) == -1){
            //計算２
            document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                
            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
            }
            
            //数字ではない場合 or 数量が小数の場合 は空を返す
            var str = document.dateForm.elements[SN].value;
            if(isNaN(document.dateForm.elements[SA2].value) == true || str.search(/.*\..*/i) != -1){
                document.dateForm.elements[SA2].value = "";
            }

            document.dateForm.elements[SA2].value = trimFixed(document.dateForm.elements[SA2].value);
            document.dateForm.elements[SA2].value = myFormatNumber(document.dateForm.elements[SA2].value);
        }else{
            document.dateForm.elements[SA2].value = "";
        }   
        return true;
    }else{
        return false;
    }
}

//契約登録で使用する。小数と整数の乗算(計算が二つ)
//function Mult_double2(sale_num,s_price_i,s_price_d,sale_amount,c_price_i,c_price_d,cost_amount,setn,coax,place,daiko_coax){
function Mult_double2(sale_num,s_price_i,s_price_d,sale_amount,c_price_i,c_price_d,cost_amount,setn,coax,place,daiko_coax,contract_div,act_div){

    var SE  = setn;

    var SN  = sale_num;

    var PI  = s_price_i;
    var PD  = s_price_d;
    var SA  = sale_amount;

    var PI2 = c_price_i;
    var PD2 = c_price_d;
    var SA2 = cost_amount;


    //顧客先が選択されているか判定
    if(coax != ""){

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI].value;
        var str2 = document.dateForm.elements[PD].value;

        if(isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD].value) == false && str2.search(/.*\..*/i) == -1){

            //デフォルトで、小数部に00を代入
            if(document.dateForm.elements[PI].value != "" && isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD].value == "" && place != true){
                document.dateForm.elements[PD].value = "00";
            }

            //・一式○　数量×　は単価×１を丸めた金額を表示
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式×　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式○　数量○　は単価×１を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式×　数量×　は0を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA].value = 0 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            }

            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA].value = Amenity_floor(document.dateForm.elements[SA].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA].value = Amenity_round(document.dateForm.elements[SA].value,document.dateForm.elements[PD].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA].value = Amenity_ceil(document.dateForm.elements[SA].value);
            }
            
            //・一式×　数量○か判定
            if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                //・一式×　数量○

                var str = document.dateForm.elements[SN].value;
                //数字ではない場合 or 数量が小数の場合 は空を返す
                if(isNaN(document.dateForm.elements[SA].value) == true || str.search(/.*\..*/i) != -1){
                    document.dateForm.elements[SA].value = "";
                }
            }else{
                //・一式○　数量×
                //・一式○　数量○

                //数字ではない場合
                if(isNaN(document.dateForm.elements[SA].value) == true){
                    document.dateForm.elements[SA].value = "";
                }
            }

            document.dateForm.elements[SA].value = trimFixed(document.dateForm.elements[SA].value);
            document.dateForm.elements[SA].value = myFormatNumber(document.dateForm.elements[SA].value);
        }else{
            document.dateForm.elements[SA].value = "";
        }

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI2].value;
        var str2 = document.dateForm.elements[PD2].value;
        if(isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD2].value) == false && str2.search(/.*\..*/i) == -1){

            //デフォルトで、小数部に00を代入
            if(document.dateForm.elements[PI2].value != "" && isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD2].value == "" && place != true){
                document.dateForm.elements[PD2].value = "00";
            }

            //・一式○　数量×　は単価×１を丸めた金額を表示
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式×　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式○　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                //代行伝票で代行料％の場合、営業原価も売上金額と同様に単価×１にする
                if((contract_div == "2" || contract_div == "3") && act_div == "3"){
                    document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                }else{
                    document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                }
            //・一式×　数量×　は0を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 0 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            }
                
            //引数daiko_coaxが指定された場合
            if(daiko_coax != undefined && daiko_coax != ""){
                coax = daiko_coax;
            }

            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
            }
            
            //・一式○　数量×か判定
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                //・一式○　数量×

                var str = document.dateForm.elements[SN].value;
                //数字ではない場合 or 数量が小数の場合 は空を返す
                if(isNaN(document.dateForm.elements[SA2].value) == true || str.search(/.*\..*/i) != -1){
                    document.dateForm.elements[SA2].value = "";
                }
            }else{
                //・一式×　数量○
                //・一式○　数量○

                //数字ではない場合
                if(isNaN(document.dateForm.elements[SA2].value) == true){
                    document.dateForm.elements[SA2].value = "";
                }
            }

            document.dateForm.elements[SA2].value = trimFixed(document.dateForm.elements[SA2].value);
            document.dateForm.elements[SA2].value = myFormatNumber(document.dateForm.elements[SA2].value);
        }else{
            document.dateForm.elements[SA2].value = "";
        }   
        return true;
    }else{
        return false;
    }
}


//代行の場合の契約登録で使用する。小数と整数の乗算(計算が二つ)
function Mult_double3(sale_num,s_price_i,s_price_d,sale_amount,c_price_i,c_price_d,cost_amount,setn,coax,place,row,detail,daiko_coax){

    var SE  = setn;

    var SN  = sale_num;

    var PI  = s_price_i;
    var PD  = s_price_d;
    var SA  = sale_amount;

    var PI2 = c_price_i;
    var PD2 = c_price_d;
    var SA2 = cost_amount;

    //顧客先が選択されているか判定
    if(coax != ""){

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI].value;
        var str2 = document.dateForm.elements[PD].value;

        if(isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD].value) == false && str2.search(/.*\..*/i) == -1){

            //デフォルトで、小数部に00を代入
            if(document.dateForm.elements[PI].value != "" && isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD].value == "" && place != true){
                document.dateForm.elements[PD].value = "00";
            }
    
            //・一式○　数量×　は単価×１を丸めた金額を表示
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式×　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式○　数量○　は単価×１を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式×　数量×　は0を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA].value = 0 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            }

            //代行計算変数
            var sum_sale = document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value;

            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA].value = Amenity_floor(document.dateForm.elements[SA].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA].value = Amenity_round(document.dateForm.elements[SA].value,document.dateForm.elements[PD].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA].value = Amenity_ceil(document.dateForm.elements[SA].value);
            }
            
            //・一式×　数量○か判定
            if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                //・一式×　数量○

                var str = document.dateForm.elements[SN].value;
                //数字ではない場合 or 数量が小数の場合 は空を返す
                if(isNaN(document.dateForm.elements[SA].value) == true || str.search(/.*\..*/i) != -1){
                    document.dateForm.elements[SA].value = "";
                }
            }else{
                //・一式○　数量×
                //・一式○　数量○

                //数字ではない場合
                if(isNaN(document.dateForm.elements[SA].value) == true){
                    document.dateForm.elements[SA].value = "";
                }
            }
            document.dateForm.elements[SA].value = trimFixed(document.dateForm.elements[SA].value);
            document.dateForm.elements[SA].value = myFormatNumber(document.dateForm.elements[SA].value);
        }else{
            document.dateForm.elements[SA].value = "";
        }

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI2].value;
        var str2 = document.dateForm.elements[PD2].value;
        if(isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD2].value) == false && str2.search(/.*\..*/i) == -1){

            //デフォルトで、小数部に00を代入
            if(document.dateForm.elements[PI2].value != "" && isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD2].value == "" && place != true){
                document.dateForm.elements[PD2].value = "00";
            }

            //内訳画面判定
            if(detail == true){
                //内訳画面

                //代行の場合、営業原価は代行依頼料＋口座料
                if(document.dateForm.elements["daiko_check"].value != 1 && sum_sale != "" && isNaN(sum_sale) == false){
                    
                    //代行依頼料（売上料×代行依頼料率）
                    if(document.dateForm.elements["act_request_rate"].value > 0){
                        //指定あり
                    
                        //売上単価×代行委託料
                        var daiko_money = sum_sale * (document.dateForm.elements["act_request_rate"].value / 100);
                        daiko_money = trimFixed(daiko_money);

                        var d_money = String(daiko_money);
                        //小数点で分割
                        mydata = d_money.split(".");

                        //小数になる可能性がある為、小数部を丸める
                        var syosu = trimFixed(daiko_money * 100) - trimFixed(eval(mydata[0] * 100));
                        syosu = trimFixed(syosu);

                        syosu = String(syosu);

                        //小数判定
                        var check = syosu.indexOf(".",0);
                        if(check != -1){
                            //小数点で分割
                            smydata = syosu.split(".");
                            document.dateForm.elements[PD2].value = eval(smydata[0]);
                        }else{
                            //小数部に表示
                            document.dateForm.elements[PD2].value = syosu;
                        }
                        //小数部が一桁なら０埋め
                        if(document.dateForm.elements[PD2].value.length == 1){
                            document.dateForm.elements[PD2].value = "0" + document.dateForm.elements[PD2].value;
                        }

                        //整数部
                        document.dateForm.elements[PI2].value = eval(mydata[0]);
                    }else{
                        //指定なしの場合は、０代入
                        document.dateForm.elements[PI2].value = "0";
                        document.dateForm.elements[PD2].value = "00";
                    }
                }
            }else{
                //登録画面

                //代行の場合、営業原価は代行依頼料＋口座料
                if(document.dateForm.elements["daiko_check"][0].checked == false && sum_sale != "" && isNaN(sum_sale) == false){
                    //代行依頼料（売上料×代行依頼料率）
                    if(document.dateForm.elements["act_request_rate"].value > 0){
                        //指定あり
                
                        //売上単価×代行委託料
                        var daiko_money = sum_sale * (document.dateForm.elements["act_request_rate"].value / 100);
                        daiko_money = trimFixed(daiko_money);

                        var d_money = String(daiko_money);
                        //小数点で分割
                        mydata = d_money.split(".");

                        //小数になる可能性がある為、小数部を丸める
                        var syosu = trimFixed(daiko_money * 100) - trimFixed(eval(mydata[0] * 100));
                        syosu = trimFixed(syosu);

                        //2010-05-15 hashimoto-y
                        //値引き商品を使用した場合、単価の小数部分に符号が付いてしまうため
                        if(syosu < 0){
                            syosu = syosu * (-1);
                        }

                        syosu = String(syosu);

                        //小数判定
                        var check = syosu.indexOf(".",0);
                        if(check != -1){
                            //小数点で分割
                            smydata = syosu.split(".");
                            document.dateForm.elements[PD2].value = eval(smydata[0]);
                        }else{
                            //小数部に表示
                            document.dateForm.elements[PD2].value = syosu;
                        }
                        //小数部が一桁なら０埋め
                        if(document.dateForm.elements[PD2].value.length == 1){
                            document.dateForm.elements[PD2].value = "0" + document.dateForm.elements[PD2].value;
                        }

                        //整数部
                        document.dateForm.elements[PI2].value = eval(mydata[0]);

                    }else{
                        //指定なしの場合は、０代入
                        document.dateForm.elements[PI2].value = "0";
                        document.dateForm.elements[PD2].value = "00";
                    }
                }
            }

            //・一式○　数量×　は単価×１を丸めた金額を表示
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式×　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式○　数量○　は単価×数量を丸めた金額を表示
            //・一式○　数量○　は単価×１を丸めた金額を表示（代行で代行料％の場合は原価も数量関係なし）
            }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                //document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式×　数量×　は0を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 0 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            }

            //内訳画面判定
            if(detail == true){
                //内訳

                //代行伝票判定
                if(document.dateForm.elements["daiko_check"].value != 1){
                    //委託先の丸めを使用

                    //切捨ての場合
                    if(daiko_coax == '1'){
                        document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
                    //四捨五入の場合
                    }else if(daiko_coax == '2'){
                        document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
                    //切上げの場合
                    }else if(daiko_coax == '3'){
                        document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
                    //委託先が選択されていなかったら切上げ
                    }else{
                        document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
                    }
                }else{
                    //得意先の丸めを使用

                    //切捨ての場合
                    if(coax == '1'){
                        document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
                    //四捨五入の場合
                    }else if(coax == '2'){
                        document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
                    //切上げの場合
                    }else if(coax == '3'){
                        document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
                    }
                }
            }else{
                //登録

                //代行伝票判定
                if(document.dateForm.elements["daiko_check"][0].checked == false){
                    //委託先の丸めを使用

                    //切捨ての場合
                    if(daiko_coax == '1'){
                        document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
                    //四捨五入の場合
                    }else if(daiko_coax == '2'){
                        document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
                    //切上げの場合
                    }else if(daiko_coax == '3'){
                        document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
                    //委託先が選択されていなかったら切上げ
                    }else{
                        document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
                    }
                }else{
                    //得意先の丸めを使用

                    //切捨ての場合
                    if(coax == '1'){
                        document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
                    //四捨五入の場合
                    }else if(coax == '2'){
                        document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
                    //切上げの場合
                    }else if(coax == '3'){
                        document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
                    }
                }
            }
            
            //・一式○　数量×か判定
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                //・一式○　数量×

                var str = document.dateForm.elements[SN].value;
                //数字ではない場合 or 数量が小数の場合 は空を返す
                if(isNaN(document.dateForm.elements[SA2].value) == true || str.search(/.*\..*/i) != -1){
                    document.dateForm.elements[SA2].value = "";
                }
            }else{
                //・一式×　数量○
                //・一式○　数量○

                //数字ではない場合
                if(isNaN(document.dateForm.elements[SA2].value) == true){
                    document.dateForm.elements[SA2].value = "";
                }
            }

            document.dateForm.elements[SA2].value = trimFixed(document.dateForm.elements[SA2].value);
            document.dateForm.elements[SA2].value = myFormatNumber(document.dateForm.elements[SA2].value);
        }else{
            document.dateForm.elements[SA2].value = "";
        }   
        return true;

    }else{

        return false;
    }
}

//代行の場合の契約登録で使用する。Mult_double3と違うのは、全５行分の再計算を行う
function Mult_double4(sale_num,s_price_i,sale_amount,c_price_i,cost_amount,setn,coax,daiko_coax){

    //顧客先が選択されているか判定
    if(coax != ""){

        for(var i=1;i<=5;i++){

            var SE  = setn+"["+i+"]";
            var SN  = sale_num+"["+i+"]";
            var PI  = s_price_i+"["+i+"][1]";
            var PD  = s_price_i+"["+i+"][2]";
            var SA  = sale_amount+"["+i+"]";
            var PI2 = c_price_i+"["+i+"][1]";
            var PD2 = c_price_i+"["+i+"][2]";
            var SA2 = cost_amount+"["+i+"]";

            //数量・一式が指定されている行のみ計算処理実行
            if(document.dateForm.elements[SN].value != "" || document.dateForm.elements[SE].checked == true){

                //数字ではない場合は処理を行なわない
                var str  = document.dateForm.elements[PI].value;
                var str2 = document.dateForm.elements[PD].value;

                if(isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD].value) == false && str2.search(/.*\..*/i) == -1){

                    //デフォルトで、小数部に00を代入
                    if(document.dateForm.elements[PI].value != "" && isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD].value == ""){
                        document.dateForm.elements[PD].value = "00";
                    }
        
                    //・一式○　数量×　は単価×１を丸めた金額を表示
                    if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                        document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
                    //・一式×　数量○　は単価×数量を丸めた金額を表示
                    }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                        document.dateForm.elements[SA].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
                    //・一式○　数量○　は単価×１を丸めた金額を表示
                    }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                        document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
                    //・一式×　数量×　は0を表示
                    }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                        document.dateForm.elements[SA].value = 0 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
                    }

                    //代行計算変数
                    var sum_sale = document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value;

                    //切捨ての場合
                    if(coax == '1'){
                        document.dateForm.elements[SA].value = Math.floor(document.dateForm.elements[SA].value);

                    //四捨五入の場合
                    }else if(coax == '2'){
                        document.dateForm.elements[SA].value = Math.round(document.dateForm.elements[SA].value);
                    //切上げの場合
                    }else if(coax == '3'){
                        document.dateForm.elements[SA].value = Math.ceil(document.dateForm.elements[SA].value);
                    }
                    
                    //・一式×　数量○か判定
                    if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                        //・一式×　数量○

                        var str = document.dateForm.elements[SN].value;
                        //数字ではない場合 or 数量が小数の場合 は空を返す
                        if(isNaN(document.dateForm.elements[SA].value) == true || str.search(/.*\..*/i) != -1){
                            document.dateForm.elements[SA].value = "";
                        }
                    }else{
                        //・一式○　数量×
                        //・一式○　数量○

                        //数字ではない場合
                        if(isNaN(document.dateForm.elements[SA].value) == true){
                            document.dateForm.elements[SA].value = "";
                        }
                    }

                    document.dateForm.elements[SA].value = trimFixed(document.dateForm.elements[SA].value);
                    document.dateForm.elements[SA].value = myFormatNumber(document.dateForm.elements[SA].value);
                }else{
                    document.dateForm.elements[SA].value = "";
                }

                //数字ではない場合は処理を行なわない
                var str  = document.dateForm.elements[PI2].value;
                var str2 = document.dateForm.elements[PD2].value;
                if(isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD2].value) == false && str2.search(/.*\..*/i) == -1){

                    //デフォルトで、小数部に00を代入
                    if(document.dateForm.elements[PI2].value != "" && isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD2].value == ""){
                        document.dateForm.elements[PD2].value = "00";
                    }

                    //代行の場合、営業原価は代行依頼料＋口座料
                    if(document.dateForm.elements["daiko_check"][0].checked == false && sum_sale != "" && isNaN(sum_sale) == false){
                        //代行依頼料（売上料×代行依頼料率）
                        if(document.dateForm.elements["act_request_rate"].value > 0){
                            //指定あり
//alert(daiko_coax);                        
//alert(coax);                        
                        
                            //売上単価×代行委託料
                            var daiko_money = sum_sale * (document.dateForm.elements["act_request_rate"].value / 100);
                            daiko_money = trimFixed(daiko_money);

                            var d_money = String(daiko_money);
                            //小数点で分割
                            mydata = d_money.split(".");

                            //小数になる可能性がある為、小数部を丸める
                            var syosu = trimFixed(daiko_money * 100) - trimFixed(eval(mydata[0] * 100));
                            syosu = trimFixed(syosu);

                            syosu = String(syosu);

                            //小数判定
                            var check = syosu.indexOf(".",0);
                            if(check != -1){
                                //小数点で分割
                                smydata = syosu.split(".");
                                document.dateForm.elements[PD2].value = eval(smydata[0]);
                            }else{
                                //小数部に表示
                                document.dateForm.elements[PD2].value = syosu;
                            }
                            //小数部が一桁なら０埋め
                            if(document.dateForm.elements[PD2].value.length == 1){
                                document.dateForm.elements[PD2].value = "0" + document.dateForm.elements[PD2].value;
                            }

                            //整数部
                            document.dateForm.elements[PI2].value = eval(mydata[0]);
                        }else{
                            //指定なしの場合は、０代入
                            document.dateForm.elements[PI2].value = "0";
                            document.dateForm.elements[PD2].value = "00";
                        }
                    }

                    //・一式○　数量×　は単価×１を丸めた金額を表示
                    if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                        document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                    //・一式×　数量○　は単価×数量を丸めた金額を表示
                    }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                        document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                    //・一式○　数量○　は単価×数量を丸めた金額を表示
                    }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                        document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                    //・一式×　数量×　は0を表示
                    }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                        document.dateForm.elements[SA2].value = 0 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                    }

                    //切捨ての場合
                    if(daiko_coax == '1'){
                        document.dateForm.elements[SA2].value = Math.floor(document.dateForm.elements[SA2].value);
                    //四捨五入の場合
                    }else if(daiko_coax == '2'){
                        document.dateForm.elements[SA2].value = Math.round(document.dateForm.elements[SA2].value);
                    //切上げの場合
                    }else if(daiko_coax == '3'){
                        document.dateForm.elements[SA2].value = Math.ceil(document.dateForm.elements[SA2].value);
                    //委託先が選択されていなかったら切上げ
                    }else{
                        document.dateForm.elements[SA2].value = Math.ceil(document.dateForm.elements[SA2].value);
                    }
                    
                    //・一式○　数量×か判定
                    if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                        //・一式○　数量×

                        var str = document.dateForm.elements[SN].value;
                        //数字ではない場合 or 数量が小数の場合 は空を返す
                        if(isNaN(document.dateForm.elements[SA2].value) == true || str.search(/.*\..*/i) != -1){
                            document.dateForm.elements[SA2].value = "";
                        }
                    }else{
                        //・一式×　数量○
                        //・一式○　数量○

                        //数字ではない場合
                        if(isNaN(document.dateForm.elements[SA2].value) == true){
                            document.dateForm.elements[SA2].value = "";
                        }
                    }

                    document.dateForm.elements[SA2].value = trimFixed(document.dateForm.elements[SA2].value);
                    document.dateForm.elements[SA2].value = myFormatNumber(document.dateForm.elements[SA2].value);
                }else{
                    document.dateForm.elements[SA2].value = "";
                }   
            }
        }
        return true;
    }else{

        return false;
    }
}

//代行の場合の予定データ訂正で使用する。小数と整数の乗算(計算が二つ)
function Mult_double_Plan(sale_num,s_price_i,s_price_d,sale_amount,c_price_i,c_price_d,cost_amount,setn,coax,place,row,rate,daiko,detail){

    var SE  = setn;

    var SN  = sale_num;

    var PI  = s_price_i;
    var PD  = s_price_d;
    var SA  = sale_amount;

    var PI2 = c_price_i;
    var PD2 = c_price_d;
    var SA2 = cost_amount;

    //顧客先が選択されているか判定
    if(coax != ""){

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI].value;
        var str2 = document.dateForm.elements[PD].value;

        if(isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD].value) == false && str2.search(/.*\..*/i) == -1){

            //デフォルトで、小数部に00を代入
            if(document.dateForm.elements[PI].value != "" && isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD].value == "" && place != true){
                document.dateForm.elements[PD].value = "00";
            }
    
            //・一式○　数量×　は単価×１を丸めた金額を表示
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式×　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式○　数量○　は単価×１を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA].value = 1 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            //・一式×　数量×　は0を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA].value = 0 * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
            }

            //代行計算変数
            var sum_sale = (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));

            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA].value = Math.floor(document.dateForm.elements[SA].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA].value = Math.round(document.dateForm.elements[SA].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA].value = Math.ceil(document.dateForm.elements[SA].value);
            }
            
            //・一式×　数量○か判定
            if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                //・一式×　数量○

                var str = document.dateForm.elements[SN].value;
                //数字ではない場合 or 数量が小数の場合 は空を返す
                if(isNaN(document.dateForm.elements[SA].value) == true || str.search(/.*\..*/i) != -1){
                    document.dateForm.elements[SA].value = "";
                }
            }else{
                //・一式○　数量×
                //・一式○　数量○

                //数字ではない場合
                if(isNaN(document.dateForm.elements[SA].value) == true){
                    document.dateForm.elements[SA].value = "";
                }
            }
            document.dateForm.elements[SA].value = trimFixed(document.dateForm.elements[SA].value);
            document.dateForm.elements[SA].value = myFormatNumber(document.dateForm.elements[SA].value);
        }else{
            document.dateForm.elements[SA].value = "";
        }

        //数字ではない場合は処理を行なわない
        var str  = document.dateForm.elements[PI2].value;
        var str2 = document.dateForm.elements[PD2].value;
        if(isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD2].value) == false && str2.search(/.*\..*/i) == -1){

            //デフォルトで、小数部に00を代入
            if(document.dateForm.elements[PI2].value != "" && isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD2].value == "" && place != true){
                document.dateForm.elements[PD2].value = "00";
            }

            //内訳画面判定
            if(detail == true){
                //内訳画面

                //代行の場合、営業原価は代行依頼料＋口座料
                if(daiko != 1 && sum_sale != "" && isNaN(sum_sale) == false){
                    //代行依頼料（売上料×代行依頼料率）
                    if(rate > 0){
                        //指定あり
                        var daiko_money = sum_sale * (rate / 100);

                        //切捨ての場合
                        if(coax == '1'){
                            daiko_money = Math.floor(daiko_money);
                        //四捨五入の場合
                        }else if(coax == '2'){
                            daiko_money = Math.round(daiko_money);
                        //切上げの場合
                        }else if(coax == '3'){
                            daiko_money = Math.ceil(daiko_money);
                        }
                    }else{
                        //指定なしの場合は、０代入
                        var daiko_money = "0";
                    }
                    document.dateForm.elements[PI2].value = eval(daiko_money);
                }
            }else{
                //登録画面

                //代行の場合、営業原価は代行依頼料＋口座料
                if(daiko != 1 && sum_sale != "" && isNaN(sum_sale) == false){
                    //代行依頼料（売上料×代行依頼料率）
                    if(rate > 0){
                        //指定あり
                        var daiko_money = sum_sale * (rate / 100);

                        //切捨ての場合
                        if(coax == '1'){
                            daiko_money = Math.floor(daiko_money);
                        //四捨五入の場合
                        }else if(coax == '2'){
                            daiko_money = Math.round(daiko_money);
                        //切上げの場合
                        }else if(coax == '3'){
                            daiko_money = Math.ceil(daiko_money);
                        }
                    }else{
                        //指定なしの場合は、０代入
                        var daiko_money = "0";
                    }
                    document.dateForm.elements[PI2].value = eval(daiko_money);
                }
            }

            //・一式○　数量×　は単価×１を丸めた金額を表示
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 1 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式×　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式○　数量○　は単価×数量を丸めた金額を表示
            }else if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value != ""){
                document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            //・一式×　数量×　は0を表示
            }else if(document.dateForm.elements[SE].checked == false && document.dateForm.elements[SN].value == ""){
                document.dateForm.elements[SA2].value = 0 * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
            }
                
            //切捨ての場合
            if(coax == '1'){
                document.dateForm.elements[SA2].value = Math.floor(document.dateForm.elements[SA2].value);
            //四捨五入の場合
            }else if(coax == '2'){
                document.dateForm.elements[SA2].value = Math.round(document.dateForm.elements[SA2].value);
            //切上げの場合
            }else if(coax == '3'){
                document.dateForm.elements[SA2].value = Math.ceil(document.dateForm.elements[SA2].value);
            }
            
            //・一式○　数量×か判定
            if(document.dateForm.elements[SE].checked == true && document.dateForm.elements[SN].value == ""){
                //・一式○　数量×

                var str = document.dateForm.elements[SN].value;
                //数字ではない場合 or 数量が小数の場合 は空を返す
                if(isNaN(document.dateForm.elements[SA2].value) == true || str.search(/.*\..*/i) != -1){
                    document.dateForm.elements[SA2].value = "";
                }
            }else{
                //・一式×　数量○
                //・一式○　数量○

                //数字ではない場合
                if(isNaN(document.dateForm.elements[SA2].value) == true){
                    document.dateForm.elements[SA2].value = "";
                }
            }

            document.dateForm.elements[SA2].value = trimFixed(document.dateForm.elements[SA2].value);
            document.dateForm.elements[SA2].value = myFormatNumber(document.dateForm.elements[SA2].value);
        }else{
            document.dateForm.elements[SA2].value = "";
        }   
        return true;

    }else{

        return false;
    }
}

//小数と整数の乗算(計算が二つ)　＊商品が選択されていなくても計算実行
function Mult_double_ren(sale_num,s_price_i,s_price_d,sale_amount,c_price_i,c_price_d,cost_amount,coax,place,cost_coax){

    var SN  = sale_num;

    var PI  = s_price_i;
    var PD  = s_price_d;
    var SA  = sale_amount;

    var PI2 = c_price_i;
    var PD2 = c_price_d;
    var SA2 = cost_amount;

    //引数cost_coaxが指定された場合
    if(cost_coax == undefined){
    	cost_coax = coax;
    }

    //数字ではない場合は処理を行なわない
    var str  = document.dateForm.elements[PI].value;
    var str2 = document.dateForm.elements[PD].value;
    if(isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD].value) == false && str2.search(/.*\..*/i) == -1){

        //デフォルトで、小数部に00を代入
        if(document.dateForm.elements[PI].value != "" && isNaN(document.dateForm.elements[PI].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD].value == "" && place != true){
            document.dateForm.elements[PD].value = "00";
        }

        //計算１
        document.dateForm.elements[SA].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));

        //切捨ての場合
        if(coax == '1'){
            document.dateForm.elements[SA].value = Amenity_floor(document.dateForm.elements[SA].value);
        //四捨五入の場合
        }else if(coax == '2'){
            document.dateForm.elements[SA].value = Amenity_round(document.dateForm.elements[SA].value,document.dateForm.elements[PD].value);
        //切上げの場合
        }else if(coax == '3'){
            document.dateForm.elements[SA].value = Amenity_ceil(document.dateForm.elements[SA].value);
        }
        
        //数字ではない場合 or 数量が小数の場合 は空を返す
        var str = document.dateForm.elements[SN].value;
        if(isNaN(document.dateForm.elements[SA].value) == true || str.search(/.*\..*/i) != -1 || document.dateForm.elements[SN].value == ""){
            document.dateForm.elements[SA].value = "";
        }
        document.dateForm.elements[SA].value = trimFixed(document.dateForm.elements[SA].value);
        document.dateForm.elements[SA].value = myFormatNumber(document.dateForm.elements[SA].value);
    }else{
        document.dateForm.elements[SA].value = "";
    }

    //数字ではない場合は処理を行なわない
    var str  = document.dateForm.elements[PI2].value;
    var str2 = document.dateForm.elements[PD2].value;
    if(isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && isNaN(document.dateForm.elements[PD2].value) == false && str2.search(/.*\..*/i) == -1){

        //デフォルトで、小数部に00を代入
        if(document.dateForm.elements[PI2].value != "" && isNaN(document.dateForm.elements[PI2].value) == false && str.search(/.*\..*/i) == -1 && document.dateForm.elements[PD2].value == "" && place != true){
            document.dateForm.elements[PD2].value = "00";
        }

        //計算２
        document.dateForm.elements[SA2].value = document.dateForm.elements[SN].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
                
        //切捨ての場合
        if(cost_coax == '1'){
            document.dateForm.elements[SA2].value = Amenity_floor(document.dateForm.elements[SA2].value);
        //四捨五入の場合
        }else if(cost_coax == '2'){
            document.dateForm.elements[SA2].value = Amenity_round(document.dateForm.elements[SA2].value,document.dateForm.elements[PD2].value);
        //切上げの場合
        }else if(cost_coax == '3'){
            document.dateForm.elements[SA2].value = Amenity_ceil(document.dateForm.elements[SA2].value);
        }
        
        //数字ではない場合 or 数量が小数の場合 は空を返す
        var str = document.dateForm.elements[SN].value;
        if(isNaN(document.dateForm.elements[SA2].value) == true || str.search(/.*\..*/i) != -1 || document.dateForm.elements[SN].value == ""){
            document.dateForm.elements[SA2].value = "";
        }

        document.dateForm.elements[SA2].value = trimFixed(document.dateForm.elements[SA2].value);
        document.dateForm.elements[SA2].value = myFormatNumber(document.dateForm.elements[SA2].value);
    }else{
        document.dateForm.elements[SA2].value = "";
    }   
    return false;
}

//小数点同士の乗算
//(基になる値、数量、単価(整数)、単価(小数)、単価2(整数)、金額1、単価2(小数)、金額2、丸め区分)
//＊数量、単価、金額は、フォーム名
function Bc_mul(id,num,price_i,price_d,amount,price_i2,price_d2,amount2,coax){
    var ID   = id;
    var NUM  = num;

    var PI   = price_i;
    var PD   = price_d;
    var AM   = amount;

    var PI2  = price_i2;
    var PD2  = price_d2;
    var AM2  = amount2;

    //計算１
    document.dateForm.elements[AM].value = document.dateForm.elements[NUM].value * (eval(Number(document.dateForm.elements[PI].value+"."+document.dateForm.elements[PD].value)));
    //切捨ての場合
    if(coax == '1'){
        document.dateForm.elements[AM].value = Math.floor(document.dateForm.elements[AM].value * 100)/100;
    //四捨五入の場合
    }else if(coax == '2'){
        document.dateForm.elements[AM].value = Math.round(document.dateForm.elements[AM].value * 100)/100;
    //切上げの場合
    }else if(coax == '3'){
        document.dateForm.elements[AM].value = Math.ceil(document.dateForm.elements[AM].value * 100)/100;
    }
    
    //小数点以下を省略しない(０埋め)
    decimal = document.dateForm.elements[AM].value.indexOf(".",0); 
    len = document.dateForm.elements[AM].value.length;
    if(decimal == -1){
        document.dateForm.elements[AM].value = document.dateForm.elements[AM].value+'.00';
    }else if(len == decimal+2){
        document.dateForm.elements[AM].value = document.dateForm.elements[AM].value+'0';
    }
    
    //数字でない場合は空を返す
    if(isNaN(document.dateForm.elements[AM].value) == true){
        document.dateForm.elements[AM].value = "";
    }
    
    document.dateForm.elements[AM].value = myFormatNumber(document.dateForm.elements[AM].value);

    //計算２
    document.dateForm.elements[AM2].value = document.dateForm.elements[NUM].value * (eval(Number(document.dateForm.elements[PI2].value+"."+document.dateForm.elements[PD2].value)));
    //切捨ての場合
    if(coax == '1'){
        document.dateForm.elements[AM2].value = Math.floor(document.dateForm.elements[AM2].value * 100)/100;
    //四捨五入の場合
    }else if(coax == '2'){
        document.dateForm.elements[AM2].value = Math.round(document.dateForm.elements[AM2].value * 100)/100;
    //切上げの場合
    }else if(coax == '3'){
        document.dateForm.elements[AM2].value = Math.ceil(document.dateForm.elements[AM2].value * 100)/100;
    }
    
    //小数点以下を省略しない(０埋め)
    decimal = document.dateForm.elements[AM2].value.indexOf(".",0); 
    len = document.dateForm.elements[AM2].value.length;
    if(decimal == -1){
        document.dateForm.elements[AM2].value = document.dateForm.elements[AM2].value+'.00';
    }else if(len == decimal+2){
        document.dateForm.elements[AM2].value = document.dateForm.elements[AM2].value+'0';
    }
    
    //数字でない場合は空を返す
    if(isNaN(document.dateForm.elements[AM2].value) == true){
        document.dateForm.elements[AM2].value = "";
    }

    document.dateForm.elements[AM2].value = myFormatNumber(document.dateForm.elements[AM2].value);

    return true;
}

/***********************ページ処理**************************/
//出力形式(画面・帳票)で遷移方法を分ける
function Which_Type(output_type,next1,next2){
    var type = output_type;
    //帳票にチェックがある場合
    if(document.dateForm.elements[type][1].checked == true){
        //別ウィンドウで遷移する
        document.dateForm.target="_blank";
        document.dateForm.action=next1;
    //画面にチェックがある場合
    }else{ 
        //同ウィンドウで遷移する
        document.dateForm.target="_self";
        document.dateForm.action=next2;
    } 
}

//帳票にPOSTする際に使用
function PDF_POST(pdf){
    var pdf = pdf;
    document.dateForm.target="_blank";
    document.dateForm.action=pdf;
}

//ページ遷移
function page_check(flg){
    //下のプルダウンの場合は、上のプルダウンに代入
    if(flg == 2){
        document.dateForm.f_page1.value = document.dateForm.f_page2.value;
    }
    document.dateForm.target="_self";
    document.dateForm.action="#";
    document.dateForm.submit();
}

//戻るリンク押下
function page_back(back){
    document.dateForm.f_page1.value = back - 1;
    document.dateForm.target="_self";
    document.dateForm.action="#";
    document.dateForm.submit();
}

//進むリンク押下
function page_next(next){
    document.dateForm.f_page1.value = next + 1;
    document.dateForm.target="_self";
    document.dateForm.action="#";
    document.dateForm.submit();
}


// 検索条件復元用
// ページ遷移
function page_check2(flg, me){
    document.dateForm.switch_page_flg.value = "t";
    // 下のプルダウンの場合は、上のプルダウンに代入
    if(flg == 2){
        document.dateForm.f_page1.value = document.dateForm.f_page2.value;
    }
    document.dateForm.target="_self";
    document.dateForm.action=me;
    document.dateForm.submit();
}

// 戻るリンク押下
function page_back2(back, me){
    document.dateForm.switch_page_flg.value = "t";
    document.dateForm.f_page1.value = back - 1;
    document.dateForm.target="_self";
    document.dateForm.action=me;
    document.dateForm.submit();
}

// 進むリンク押下
function page_next2(next, me){
    document.dateForm.switch_page_flg.value = "t";
    document.dateForm.f_page1.value = next + 1;
    document.dateForm.target="_self";
    document.dateForm.action=me;
    document.dateForm.submit();
}




function Open_Win(page){
    document.dateForm.target="_blank";
    document.dateForm.action=page;
}

/********************全チェック・全反転**********************/

//チェックボックスのValue値変更
//引数（Value値を変更するフォーム名、Value値）
function Check_value(c_name,str){
    document.dateForm.elements[c_name].value = str;
}

//全チェック
function Allcheck(num){
    var Y = "check"+num+"[check]";
    var A = "allcheck"+num+"[allcheck"+num+"]";
    for(var e=0;e<document.dateForm.elements.length;e++){
        //入力フォームの内容で、チェックボックスか
        if(document.dateForm.elements[e].name == A){
            //チェック判定
            if(document.dateForm.elements[e].checked == true){
                for(var e=0;e<document.dateForm.elements.length;e++){
                    if(document.dateForm.elements[e].name == Y){
                        //チェックを付ける
                        document.dateForm.elements[e].checked = true;
                    }
                }
            }else{
                for(var e=0;e<document.dateForm.elements.length;e++){
                    if(document.dateForm.elements[e].name == Y){
                        //チェックを外す
                        document.dateForm.elements[e].checked = false;
                    }
                }
            }
        }
    }
}

//全チェック
//引数(ALLチェックのフォーム名、チェック対象のフォーム名、チェックする数)
function All_check(all_name,c_name,num){
    var A = all_name;
    //チェック判定
    if(document.dateForm.elements[A].checked == true){
        for(var x=0;x<num;x++){
            var C = c_name+"["+x+"]";
            //チェックを付ける
            document.dateForm.elements[C].checked = true;
        }
    }else{
        for(var x=0;x<num;x++){
            var C = c_name+"["+x+"]";
            //チェックを外す
            document.dateForm.elements[C].checked = false;
        }
    }
}

//全反転
function Allchange(){
    for(var c=12;c<22;c++){
        var I = "f_r_output"+c+"[in]";
        //チェック判定
        for(var e=0;e<document.dateForm.elements.length;e++){
            //入力フォームの内容で、ラジオボタンか
            if(document.dateForm.elements[e].name == I){
                //入庫・出庫判定
                if(document.dateForm.elements[e].value == "1"){
                    document.dateForm.elements[e].value = "2";
                }else{
                    document.dateForm.elements[e].value = "1";
                }
            }
        }
    }
    document.dateForm.submit();
}

//全反転

function Allchange_1(max, io_num){

    for(var c=0;c<io_num;c++){
    var I = "form_io_type["+c+"]";
    if(document.dateForm.elements[I][0].checked == true){
        document.dateForm.elements[I][1].checked = true;
    }else {
        document.dateForm.elements[I][0].checked = true;
    }
        sum(c, max);
    }
}

/********************Code_Value**********************/

//リンク表示
function display(code,place,num){
    //リンク項目が一画面に複数あるか
    if(num != undefined){
        display_flg = true;
    }else{
        display_flg = false;
    }
    //部署コード
    if(place=="position"){
        if(display_flg == true){
            var name = "f_position"+num+"[name]";
        }else{
            var name = "f_position[name]";
        }
        
        data = new Array(5);
        data['01']="営業部"
        data['02']="財務部"
        data['03']="業務部"
        data['04']="商品部"
        data['05']="FCシステム部"
    //倉庫コード
    }else if(place=="warehouse"){
        if(display_flg == true){
            var name = "f_warehouse"+num+"[name]";
        }else{
            var name = "f_warehouse[name]";
        }
        
        data = new Array(5);
        data['001']="A倉庫"
        data['002']="B倉庫"
        data['003']="C倉庫"
        data['004']="D倉庫"
        data['005']="E倉庫"
    //倉庫コード
    }else if(place=="warehouse1"){
        var name = "form_ware[form_ware_name]";
        
        data = new Array(5);
        data['001']="A倉庫"
        data['002']="B倉庫"
        data['003']="C倉庫"
        data['004']="D倉庫"
        data['005']="E倉庫"
    //業種コード
    }else if(place=="business"){
        if(display_flg == true){
            var name = "f_business"+num+"[name]";
        }else{
            var name = "f_business[name]";
        }
        
        data = new Array(5);
        data['00001']="外食ファーストフード"
        data['00002']="学校関係"
        data['00003']="医療関係"
        data['00004']="ファミレス"
        data['00005']="コンビニ"
    //銀行コード
    }else if(place=="bank"){
        if(display_flg == true){
            var name = "f_bank"+num+"[name]";
        }else{
            var name = "f_bank[name]";
        }
        
        data = new Array(5);
        data['0001']="さくら銀行　渋谷支店"
        data['0002']="みずほ銀行"
        data['0003']="三井住友銀行"
        data['0004']="UFJ銀行"
        data['0005']="さくら銀行　横浜支店"
    //製品区分コード
    }else if(place=="product"){
        if(display_flg == true){
            var name = "f_product"+num+"[name]";
        }else{
            var name = "f_product[name]";
        }
        
        data = new Array(5);
        data['0001']="リピート商品"
        data['0002']="尿石防止剤酸"
        data['0003']="尿石防止剤酵素"
        data['0004']="売切商品"
        data['0005']="工事関連"
    //Ｍ区分コード
    }else if(place=="district"){
        if(display_flg == true){
            var name = "f_district"+num+"[name]";
        }else{
            var name = "f_district[name]";
        }
        
        data = new Array(5);
        data['0001']="尿防ｴﾑﾜｲｻﾞｰ"
        data['0002']="尿防ﾋﾟﾋﾟﾀﾞﾘｱ"
        data['0003']="尿防ﾋﾟﾋﾟｿﾚｲﾕ"
        data['0004']="尿防ｾﾝｻ薬剤"
        data['0005']="尿防ﾋﾟﾋﾟｷｬｯﾁ"
    //仕入先コード
    }else if(place=="layer"){
        if(display_flg == true){
            var name = "f_layer"+num+"[name]";
        }else{
            var name = "f_layer[name]";
        }
        data = new Array(5);
        data['000001']="横浜油脂工場"
        data['000002']="リフレ"
        data['000003']="渡辺パイプ"
        data['000004']="ワラー"
        data['000005']="リエン"
    //商品コード
    }else if(place=="goods"){
        if(display_flg == true){
            var name = "f_goods"+num+"[name]";
        }else{
            var name = "f_goods[name]";
        }
        data = new Array(5);
        data['00000001']="商品1"
        data['00000002']="商品2"
        data['00000003']="商品3"
        data['00000004']="商品4"
        data['00000005']="商品5"
    //サービスコード
    }else if(place=="service"){
        if(display_flg == true){
            var name = "f_service"+num+"[name]";
        }else{
            var name = "f_service[name]";
        }
        data = new Array(5);
        data['0001']="サービスA"
        data['0002']="サービスB"
        data['0003']="サービスC"
        data['0004']="サービスD"
        data['0005']="サービスE"
    //運送業者コード
    }else if(place=="forwarding"){
        if(display_flg == true){
            var name = "f_forwarding"+num+"[name]";
        }else{
            var name = "f_forwarding[name]";
        }
        data = new Array(5);
        data['000001']="赤帽急便"
        data['000002']="佐川急便"
        data['000003']="日本通運"
        data['000004']="ヤマト運輸"
        data['000005']="佐川急便"
    //直送先コード
    }else if(place=="direct"){
        if(display_flg == true){
            var name = "f_direct"+num+"[name]";
        }else{
            var name = "f_direct[name]";
        }
        data = new Array(5);
        data['000001']="直送先A"
        data['000002']="直送先B"
        data['000003']="直送先C"
        data['000004']="直送先D"
        data['000005']="直送先E"
    //地区コード
    }else if(place=="area"){
        if(display_flg == true){
            var name = "f_area"+num+"[name]";
        }else{
            var name = "f_area[name]";
        }
        data = new Array(5);
        data['0001']="北海道"
        data['0002']="東北"
        data['0003']="関東"
        data['0004']="信越"
        data['0005']="東海"
    //顧客区分コード
    }else if(place=="client"){
        if(display_flg == true){
            var name = "f_client"+num+"[name]";
        }else{
            var name = "f_client[name]";
        }
        data = new Array(5);
        data['0001']="顧客区分A"
        data['0002']="顧客区分B"
        data['0003']="顧客区分C"
        data['0004']="顧客区分D"
        data['0005']="顧客区分E"
    //請求先コード
    }else if(place=="claim"){
        if(display_flg == true){
            var name = "f_claim"+num+"[name]";
        }else{
            var name = "f_claim[name]";
        }
        data = new Array(5);
        data['00000001']="請求先A"
        data['00000002']="請求先B"
        data['00000003']="請求先C"
        data['00000004']="請求先D"
        data['00000005']="請求先E"
    //取引区分コード
    }else if(place=="dealing"){
        if(display_flg == true){
            var name = "f_dealing"+num+"[name]";
        }else{
            var name = "f_dealing[name]";
        }
        data = new Array(5);
        data['01']="取引区分A"
        data['02']="取引区分B"
        data['03']="取引区分C"
        data['04']="取引区分D"
        data['05']="取引区分E"
    //担当者コード
    }else if(place=="charge"){
        if(display_flg == true){
            var name = "f_charge"+num+"[name]";
        }else{
            var name = "f_charge[name]";
        }
        data = new Array(5);
        data['0001']="巡回担当A"
        data['0002']="巡回担当B"
        data['0003']="巡回担当C"
        data['0004']="巡回担当D"
        data['0005']="巡回担当E"
    //分類区分コード
    }else if(place=="kind"){
        if(display_flg == true){
            var name = "f_kind"+num+"[name]";
        }else{
            var name = "f_kind[name]";
        }
        data = new Array(5);
        data['0001']="リピート"
        data['0002']="商品"
        data['0003']="レンタル"
        data['0004']="リース"
        data['0005']="卸"
        data['0006']="工事"
        data['0007']="その他"
        data['0008']="保険（傷害）"
        data['0009']="保険（賠償)"
        data['0010']="保険（共済)"
    //締日コード
    }else if(place=="close"){
        if(display_flg == true){
            var name = "f_close"+num+"[name]";
        }else{
            var name = "f_close[name]";
        }
        
        data = new Array(33);
        len = code.value.length;
        if(2==len && code.value>=1 && code.value<=30 && code.value!=null){
            data[code.value]="通常締日";
        }
        data['31']="月末指定";
        data['91']="現金得意先";
        data['99']="随時締日";
    }

    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//"検索"リンク
//銀行コード
function display1(code,place){
    if(place=="bank"){
        var name = "f_bank[name]";
        var num = "f_bank[num]";
    }else{
        var name = "t_"+place;
        var num = "n_"+place;
    }
    data = new Array(5);
    data['0001'] = new Array("さくら銀行　渋谷支店","00000000000001");
    data['0002'] = new Array("みずほ銀行","00000000000002");
    data['0003'] = new Array("三井住友銀行","00000000000003");
    data['0004'] = new Array("UFJ銀行","00000000000004");
    data['0005'] = new Array("さくら銀行　横浜支店","00000000000005");

    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        //銀行名を表示
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data[0]; 
            }
        }
        //呼出コードを表示
        if(document.dateForm.elements[d].name == num){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data[1]; 
            }
        }
    }
}
//請求先コード
function display2(code,place){
    var name = "t_"+place;
    data = new Array(5);
    data['00000001']="請求先A"
    data['00000002']="請求先B"
    data['00000003']="請求先C"
    data['00000004']="請求先D"
    data['00000005']="請求先E"
    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//取引区分コード
function display3(code,place){
    var name = "t_"+place;
    data = new Array(5);
    data['01']="取引区分A"
    data['02']="取引区分B"
    data['03']="取引区分C"
    data['04']="取引区分D"
    data['05']="取引区分E"

    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//商品コード
function display4(code,place){
    var name = "t_"+place;
    data = new Array(5);
    data['00000001']="商品1"
    data['00000002']="商品2"
    data['00000003']="商品3"
    data['00000004']="商品4"
    data['00000005']="商品5"
    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//仕入先コード
function display5(code,place){
    var name = "t_"+place;
    data = new Array(5);
    data['000001']="横浜油脂工場"
    data['000002']="リフレ"
    data['000003']="渡辺パイプ"
    data['000004']="ワラー"
    data['000005']="リエン"
    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//担当者コード
function display6(code,place){
    var name = "t_"+place;
    data = new Array(5);
    data['0001']="巡回担当A"
    data['0002']="巡回担当B"
    data['0003']="巡回担当C"
    data['0004']="巡回担当D"
    data['0005']="巡回担当E"
    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//サービスコード
function display7(code,place){
    var name = "t_"+place;
    data = new Array(5);
    data['0001']="サービスA"
    data['0002']="サービスB"
    data['0003']="サービスC"
    data['0004']="サービスD"
    data['0005']="サービスE"
    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//倉庫コード
function display8(code,place){
    var name = "t_"+place;
    data = new Array(5);
    data['001']="A倉庫"
    data['002']="B倉庫"
    data['003']="C倉庫"
    data['004']="D倉庫"
    data['005']="E倉庫"
    var data = data[code.value];
    
    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}
//分類区分コード
function display9(code,place){
    var name = "t_"+place;
    data = new Array(10);
    data['0001']="リピート"
    data['0002']="商品"
    data['0003']="レンタル"
    data['0004']="リース"
    data['0005']="卸"
    data['0006']="工事"
    data['0007']="その他"
    data['0008']="保険（傷害）"
    data['0009']="保険（賠償)"
    data['0010']="保険（共済)"
    var data = data[code.value];

    for(var d=0;d<document.dateForm.elements.length;d++){
        if(document.dateForm.elements[d].name == name){
            if(data == undefined){
                document.dateForm.elements[d].value = "";
            }else{
                document.dateForm.elements[d].value = data; 
            }
        }
    }
}

//*********************サブウィンドウ表示****************************

//■別ウィンドウで一覧を表示
//一覧リンクがクリックされた場合、別ウィンドウで一覧を表示する
function WindowOpen(fName,yoko,tate,name){
    var objWnd = null;
    Setup='width='+ yoko +',height='+ tate +',scrollbars=yes,resizable=yes';
    objWnd = window.open(fName,name,Setup);
    //画面を最前面に表示
    objWnd.focus();
}

//ウィンドウを閉じる
function WindowClose(){
    window.close();
}

//*********************テキストのvalue値変更*************************

function whileday(me,num){
    var D = "f_day_wh"+num+"[d_input]";
    var DAY = "f_text2_"+num;
    //全て入力されているか
    if(me.elements[D].value != "" && me.elements[D].value.length == "2"){
        me.elements[DAY].value = me.elements[D].value;
    }else{
        me.elements[DAY].value = "";
    }
}

//フォーカス時に、現在の日付が表示する
function onForm2(which,me,num){
    if (document.all || document.getElementById){
        which.style.backgroundColor="#FDFD66"
    }
    today       = new Date();
    //Year    = today.getYear();
    //年を正しく取得できるように修正 2016-01-13
    Year    = today.getFullYear();
    Month   = today.getMonth()+1;
    Day     = today.getDate();
    var Y = "f_date_a"+num+"[y_input]";
    var M = "f_date_a"+num+"[m_input]";
    var D = "f_date_a"+num+"[d_input]";
    //既に入力されているか
    if(me.elements[Y].value == "" && me.elements[M].value == "" && me.elements[D].value == ""){
        me.elements[Y].value = Year;
        me.elements[M].value = Month;
        me.elements[D].value = Day;
        //一桁なら0を付ける
        if(me.elements[M].value <= 9){
                me.elements[M].value = "0" + Month;
        }
        if(me.elements[D].value <= 9){
                me.elements[D].value = "0" + Day;
        }
    }
}

// フォーカス時に翌日の日付を表示
function Comp_Form_NextToday(which, me, form, form_y, form_m, form_d){

    // フォーカス時はフォームの色を変える
    if (document.all || document.getElementById){
        which.style.backgroundColor="FDFD66"
    }

    today = new Date();
    today.setDate(today.getDate() + 1);
    //Year    = today.getYear();
    //年を正しく取得できるように修正 2016-01-13
    Year    = today.getFullYear();
    Month = today.getMonth()+1;
    Day   = today.getDate();
    var Y = form+"["+form_y+"]";
    var M = form+"["+form_m+"]";
    var D = form+"["+form_d+"]";

    // 入力状態なら日付補完
    if (me.elements[Y].value == "" && me.elements[M].value == "" && me.elements[D].value == ""){
        me.elements[Y].value = Year;
        me.elements[M].value = Month;
        me.elements[D].value = Day;

        // 1桁なら0埋め
        if (me.elements[M].value <= 9){
            me.elements[M].value = "0" + Month;
        }
        if (me.elements[D].value <= 9){
            me.elements[D].value = "0" + Day;
        }
    }

}

//売上日を請求日にも表示
function setText1(me,num){
    var Y = "f_date_a"+num+"[y_input]";
    var M = "f_date_a"+num+"[m_input]";
    var S = "f_date_a96[y_input]";

    //テキストに文字を代入
    me.elements[S].value = me.elements[Y].value;

    len = me.elements[Y].value.length;
    if( 4==len){
        me.elements[M].focus();
    }
}

function setText2(me,num){
    var M = "f_date_a"+num+"[m_input]";
    var D = "f_date_a"+num+"[d_input]";
    var S = "f_date_a96[m_input]";

    //テキストに文字を代入
    me.elements[S].value = me.elements[M].value;

    len = me.elements[M].value.length;
    if (2<=len){
        me.elements[D].focus();
    }
}
function setText3(me,num){
    var D = "f_date_a"+num+"[d_input]";
    var S = "f_date_a96[d_input]";

    //テキストに文字を代入
    me.elements[S].value = me.elements[D].value;

}

// フォームに入力されたら対象のフォームに文字列を補完
function Comp_Form_Val(me, form, form1, form2, val){

    var form1 = form+"["+form1+"]";
    var form2 = form+"["+form2+"]";
    if ((me.elements[form1].value != "") && (me.elements[form2].value == "")){
//    if (me.elements[form2].value == ""){
    me.elements[form2].value = val;
    }

}

// フォーカス時に現在の日付を補完(年月日フォーム)
function Comp_Form_Today(which, me, form, form_y, form_m, form_d){

    // フォーカス時はフォームの色を変える
    if (document.all || document.getElementById){
        which.style.backgroundColor="#E6FFEC"
    }

    today = new Date();
    //Year    = today.getYear();
    //年を正しく取得できるように修正 2016-01-13
    Year    = today.getFullYear();
    Month = today.getMonth()+1;
    Day   = today.getDate();
    var Y = form+"["+form_y+"]";
    var M = form+"["+form_m+"]";
    var D = form+"["+form_d+"]";

    // 入力状態なら日付補完
    if (me.elements[Y].value == "" && me.elements[M].value == "" && me.elements[D].value == ""){
        me.elements[Y].value = Year;
        me.elements[M].value = Month;
        me.elements[D].value = Day;

        // 1桁なら0埋め
        if (me.elements[M].value <= 9){
            me.elements[M].value = "0" + Month;
        }
        if (me.elements[D].value <= 9){
            me.elements[D].value = "0" + Day;
        }
    }

}

//*********************onChangeでsubmit*************************

//onChangeでsubmit
function Change_Submit(hidden_form, page, str,next_value){
    //フォーカス先がNULLの場合は、submitしない
    if(document.dateForm.elements[next_value].value != ""){
        var hdn = hidden_form;
        document.dateForm.elements[hdn].value = str;

        //同じウィンドウで遷移する
        document.dateForm.target="_self";

        //自画面に遷移する
        document.dateForm.action=page;

        //POST情報を送信する
        document.dateForm.submit();
    }
    return true;
}

//メニューのプルダウン
function Change_Menu(me,name){
    document.dateForm.target="_self";
    //プルダウンの値の遷移先を取得
    page = me.elements[name].value;
    //遷移先があった場合だけsubmit
    if(page != "" && page != 'menu'){
        location.href = page;
    }
}

//*********************buttonでsubmit*************************

//onChangeによって別画面へPOSTする
function Change_Page(me,name){
    document.dateForm.target="_self";
    //プルダウンの値の遷移先を取得
    page = me.elements[name].value;
    //自画面に遷移する
    document.dateForm.action=page;
    //POST情報を送信する
    document.dateForm.submit();
}

// buttonでsubmit
function Button_Submit(hidden_form, page, str, obj){

    var hdn = hidden_form;
    document.dateForm.elements[hdn].value = str;

	if (null != obj) {
		var element = document.createElement('input'); 
		element.type = "hidden"; 
		element.name = obj.name; 
		element.value = obj.value;
		document.dateForm.appendChild(element);
	}

    //同じウィンドウで遷移する
    document.dateForm.target="_self";

    //自画面に遷移する
    document.dateForm.action=page;

    //POST情報を送信する
    document.dateForm.submit();

    return true;
}

// buttonでsubmit
function Button_Submit_1(hidden, page, str, obj){
    var hdn = hidden;
    document.dateForm.elements[hdn].value = str;
    
	if (null != obj) {
		var element = document.createElement('input'); 
		element.type = "hidden"; 
		element.name = obj.name; 
		element.value = obj.value;
		document.dateForm.appendChild(element);
	}
    
    //同じウィンドウで遷移する
    document.dateForm.target="_self";
    //自画面に遷移する
    document.dateForm.action=page;
    //POST情報を送信する
    document.dateForm.submit();
}

//別画面へPOSTする場合に使用
function Submit_Page(page){
    document.dateForm.target="_self";
    //自画面に遷移する
    document.dateForm.action=page;
    //POST情報を送信する
    document.dateForm.submit();
}

function Submit_Page2(page){
    document.dateForm.target="_self";
    //自画面に遷移する
    document.dateForm.action=page;
    //POST情報を送信する
    document.dateForm.submit();
    return false;
}

//*********************linkでsubmit*************************
function Link_Submit(name,hidden, page, str){
    if(document.dateForm.elements[name].checked == true){
        document.dateForm.elements[hidden].value = str;
    }else {
        document.dateForm.elements[hidden].value = "";
    }
    //同じウィンドウで遷移する
    document.dateForm.target="_self";
    //自画面に遷移する
    document.dateForm.action=page;
    //POST情報を送信する
    document.dateForm.submit();
}

//**************出力形式が画面と帳票の組み合わせの場合に使用********
function Submit_Judge(hidden_form, page1, page2, str){
    var hdn = hidden_form;
    var type = 'form_output_type';

        document.dateForm.elements[hdn].value = str;

        //同じウィンドウで遷移する
        document.dateForm.target="_self";

        if(document.dateForm.elements[type][0].checked == true){
            //自画面に遷移する
            document.dateForm.action=page1;
        }else{
            document.dateForm.action=page2;
        } 

        //POST情報を送信する
        document.dateForm.submit();
        
}

//**************帳票にPOST情報を送信********
function Post_book_vote(page,next){
    //別画面でウィンドウを開く
    document.dateForm.target="_blank";
    document.dateForm.action=page;
    //POST情報を送信する
    document.dateForm.submit();

    //自画面でもSUBMITしたい場合
    if(next != undefined){
        document.dateForm.target="_self";
        document.dateForm.action=next;
        document.dateForm.submit();
    }
}

function Post_book_vote2(str_check,hidden,page,next,check_name,num){
    // 確認ダイアログ表示
    res = window.confirm(str_check+"\nよろしいですか？");
    // 選択分岐
    if (res == true){
        document.dateForm.elements[hidden].value = true;

        //自画面でもSUBMITしたい場合
        if(next != undefined){
            document.dateForm.target="_self";
            document.dateForm.action=next;
            document.dateForm.submit();
        }

        //発行する伝票が選択されているか判定
        for(var i=0;i<num;i++){
            var form_name = check_name+"["+i+"]";
            if(document.dateForm.elements[form_name].checked == true){
                var check_flg = true;
            }
        }

        //伝票が選択されていた場合にファイルを開く
        if(check_flg == true){
            //別画面でウィンドウを開く
            document.dateForm.target="_blank";
            document.dateForm.action=page;
            //POST情報を送信する
            document.dateForm.submit();
        }

        return true;
    }else{
        return false;
    }
}

//2010-04-30 hashimoto-y
//onClickでコールされた後、targetを親ウィンドウに戻す
function Post_book_vote3(page,next){
    //別画面でウィンドウを開く
    document.dateForm.target="_blank";
    document.dateForm.action=page;
    //POST情報を送信する
    document.dateForm.submit();

    document.dateForm.target="_self";
    document.dateForm.action=next;
}

//**************予定カレンダー用ページ切替え*********

//一時使用
function Jump_Page(page){
    if(page==1){
        location.href = "./2-2-101.php";
    }else if(page==2){
        location.href = "./2-2-101-2.php";
    }else if(page==3){
        location.href = "./2-2-101-3.php";
    }

    return false;
}
function Jump_Page2(page){
    if(page==1){
        location.href = "./2-2-102.php";
    }else if(page==2){
        location.href = "./2-2-102-2.php";
    }else if(page==3){
        location.href = "./2-2-102-3.php";
    }

    return false;
}

//*****************ダイアログ表示*********************

/* 
 * 検索サブウィンドウをダイアログで表示
 * url:サブウィンドウのURL
 * arr:データを入れるフォームのnameを配列で
 * x:サブウィンドウの横幅
 * y:サブウィンドウの縦幅
 * display:商品ダイアログの識別番号
 * select_id:倉庫ID or 棚卸調査ID
 * shop_aid:ショップ識別ID
 * place:画面上のリンク先
 * head_flg:本部判定
 */
function Open_SubWin(url, arr, x, y,display,select_id,shop_aid,place,head_flg)
{
    //ダイアログが指定されている場合は、倉庫ID or 棚卸調査ID が必要
    if((display == undefined && select_id == undefined) || (display != undefined && select_id != undefined)){

        //契約マスタの場合は本部判定
        if((display==6 || display==7 || display=='1-3-207') && head_flg != undefined){
            //契約区分が通常以外は、本部の商品だけを表示
            if(document.dateForm.elements[head_flg][0].checked != true){
                //オンライン・オフライン代行
                rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,true);
            }else{
                //通常
                rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,false);
            }
        }else{
            rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid);
        }

        if(typeof(rtnarr) != "undefined"){
            for(i=0;i<arr.length;i++){
                dateForm.elements[arr[i]].value=rtnarr[i];
            }
        }

        //発注入力・受注入力の場合はsubmitする
        if(display==2 || display==5 || display=='3-403' || display=='2-503' || display=='3-207' || display==1 || display=='true' || display=='2-409' || display=='2-405'){
            var next = '#';
            document.dateForm.action=next;
            document.dateForm.submit();
        }

        //契約マスタの場合は画面のリンク先にsubmitする
        if(display==6 || display==7 || display=='1-3-207'){
            var next = '#'+place;
            document.dateForm.action=next;
            document.dateForm.submit();
        }

    }else{
        alert("倉庫を選択してください。");
    }
    return false;
}

function Open_SubWin_3(url, arr, x, y, display, select_id, row_num)
{
    //ダイアログが指定されている場合は、倉庫ID or 棚卸調査ID が必要
    if ((display == undefined && select_id == undefined) || (display != undefined && select_id != undefined)){

        rtnarr = Open_Dialog(url, x, y, display, select_id);

        if (typeof(rtnarr) != "undefined"){
            for(i=0;i<arr.length;i++){
                dateForm.elements[arr[i]].value=rtnarr[i];
            }
        }

        var next = '#'+row_num;
        document.dateForm.action=next;
        document.dateForm.submit();

    }

    return false;
}

//検索ボタン押下時
function Open_SubWin_2(url, arr, x, y,display,select_id,shop_aid,next_page){
    rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid);
    if(typeof(rtnarr) != "undefined"){
        for(i=0;i<arr.length;i++){
            dateForm.elements[arr[i]].value=rtnarr[i];
        }
    }
    document.dateForm.target="_self";
    document.dateForm.action='#'+next_page;
    document.dateForm.submit();
    return false;
}

/* 
 * Open_SubWinの処理は同じだが、違うところは、ダイアログの返り値を返す
 * url:サブウィンドウのURL
 * arr:データを入れるフォームのnameを配列で
 * x:サブウィンドウの横幅
 * y:サブウィンドウの縦幅
 * display:商品ダイアログの識別番号
 * select_id:倉庫ID or 棚卸調査ID
 * shop_aid:ショップ識別ID
 */
function Open_Contract(url, arr, x, y,display,select_id,shop_aid)
{
    //ダイアログが指定されている場合は、倉庫ID or 棚卸調査ID が必要
    if((display == undefined && select_id == undefined) || (display != undefined && select_id != undefined)){
        rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid);
        if(typeof(rtnarr) != "undefined"){
            for(i=0;i<arr.length;i++){
                dateForm.elements[arr[i]].value=rtnarr[i];
            }
        }else{
            //ダイアログをキャンセルした場合は、hiddenに返り値を代入
            rtnarr = new Array("","","","get");
            for(i=0;i<arr.length;i++){
                dateForm.elements[arr[i]].value=rtnarr[i];
            }
        }

        //発注入力・受注入力の場合はsubmitする
        if(display==2 || display==5 || display==6){
            var next = '#';
            document.dateForm.action=next;
            document.dateForm.submit();
        }

    }else{
        alert("倉庫を選択してください。");
    }
    return false;
}


//商品ダイアログ関数
//FCの予定、売上あたりで使ってます
function Open_SubWin_Plan(url, arr, x, y,display,select_id,shop_aid,place,head_flg)
{
    //ダイアログが指定されている場合は、倉庫ID or 棚卸調査ID が必要
    if((display == undefined && select_id == undefined) || (display != undefined && select_id != undefined)){

        //契約区分が通常以外は、本部の商品だけを表示
        if(head_flg != 1){
            //オンライン・オフライン代行
            rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,true);
        }else{
            //通常
            rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,false);
        }

        if(typeof(rtnarr) != "undefined"){
            for(i=0;i<arr.length;i++){
                dateForm.elements[arr[i]].value=rtnarr[i];
            }
        }

        //契約マスタの場合は画面のリンク先にsubmitする
        if(display==6 || display==7){
            var next = '#'+place;
            document.dateForm.action=next;
            document.dateForm.submit();
        }

    }

    return false;
}


/**
 * 親ウィンドウが操作できない方のダイアログ
 * @param   {url}       string      ダイアログに開くファイル
 * @param   {x}         int         ダイアログの幅
 * @param   {y}         int         ダイアログの高さ
 * @param   {display}   string      ダイアログにGETで渡す引数(商品ダイアログの識別番号)
 * @param   {select_id} string      ダイアログにGETで渡す引数(倉庫ID or 棚卸調査ID)
 * @param   {shop_aid} string       ダイアログにGETで渡す引数(ショップ識別ID)
 * @param   {head_flg} string       ダイアログにGETで渡す引数(本部識別フラグ)
 * @return  {num}       mixed       ダイアログの戻り値（恐らく配列）
 * @version
 * @author
 */
function Open_Dialog(url,x,y,display,select_id,shop_aid,head_flg){

    //ダイアログの種類が指定されているか
    if(display == undefined){
        num = showModalDialog(url,window,"status:no;dialogWidth:"+x+"px;dialogHeight:"+y+"px;edge:raised;help:no;resizable:yes;dialogTop:10px;dialogLeft:500px;");
    }else{
        //指定したダイアログを表示
        num = showModalDialog(url+"?display="+display+"&select_id="+select_id+"&shop_aid="+shop_aid+"&head_flg="+head_flg,window,"status:no;dialogWidth:"+x+"px;dialogHeight:"+y+"px;edge:raised;help:no;resizable:yes;dialogTop:10px;dialogLeft:500px;");
    }
    
    return num;
}


/**
 * 親ウィンドウが操作できる方のダイアログ
 * @param   {url}       string      ダイアログに開くファイル
 * @param   {x}         int         ダイアログの幅
 * @param   {y}         int         ダイアログの高さ
 * @return  {num}       object      windowオブジェクトを返す
 * @version
 * @author
 */
function Open_mlessDialog(url,x,y){
    num = showModelessDialog(url,window,"status:no;dialogWidth:"+x+"px;dialogHeight:"+y+"px;edge:raised;help:no;resizable:yes;dialogTop:10px;dialogLeft:500px;");
    
    return num;
}

/**
 * 親ウィンドウが操作できる方のダイアログ
 * @param   {url}       string      ダイアログに開くファイル
 * @param   {x}         int         ダイアログの幅
 * @param   {y}         int         ダイアログの高さ
 * @return  {num}       object      windowオブジェクトを返す
 * @version
 * @author
 */
function Open_submitDialog(url,x,y,hidden){
    num = showModalDialog(url,window,"status:no;dialogWidth:"+x+"px;dialogHeight:"+y+"px;edge:raised;help:no;resizable:yes;");
    if(num != null){
    var hdn = hidden;
        document.dateForm.elements[hdn].value = num;
        document.dateForm.target='_top';
        document.dateForm.action='#';
        document.dateForm.submit();
    }
}

/**
 * 親ウィンドウが操作できる方のダイアログ(GET情報含む)
 * @param   {url}        string      ダイアログに開くファイル
 * @param   {x}          int         ダイアログの幅
 * @param   {y}          int         ダイアログの高さ
 * @param   {select_id}  string      ダイアログにGETで渡す引数
 * @param   {select_id2} string      ダイアログにGETで渡す引数
 * @return  {num}        object      windowオブジェクトを返す
 * @param   {select_id3} string      ダイアログにGETで渡す引数
 * @version
 * @author
 */
function Open_mlessDialmg_g(url,select_id,select_id2,x,y,select_id3){
    num = showModelessDialog(url+"?select_id="+select_id+"&select_id2="+select_id2+"&select_id3="+select_id3,window,"status:no;dialogWidth:"+x+"px;dialogHeight:"+y+"px;edge:raised;help:no;resizable:yes;dialogTop:10px;dialogLeft:500px;");
    
    return num;
}

/* 
 * 検索サブウィンドウをダイアログで表示
 * url:サブウィンドウのURL
 * arr:データを入れるフォームのnameを配列で
 * x:サブウィンドウの横幅
 * y:サブウィンドウの縦幅
 * display:商品ダイアログの識別番号
 * select_id:倉庫ID or 棚卸調査ID
 * shop_aid:ショップ識別ID
 * place:画面上のリンク先
 * head_flg:本部判定(内訳の場合は、hiddenの為value値をみる)
 */
function Open_Detail(url, arr, x, y,display,select_id,shop_aid,place,head_flg)
{
    //ダイアログが指定されている場合は、倉庫ID or 棚卸調査ID が必要
    if((display == undefined && select_id == undefined) || (display != undefined && select_id != undefined)){
        
        //契約マスタの場合は本部判定
        if((display==6 || display==7) && head_flg != undefined){
            //契約区分が通常以外は、本部の商品だけを表示
            if(document.dateForm.elements[head_flg][0].checked != true){
                //オンライン・オフライン代行
                rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,true);
            }else{
                //通常
                rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid,false);
            }
        }else{
            rtnarr = Open_Dialog(url,x,y,display,select_id,shop_aid);
        }

        if(typeof(rtnarr) != "undefined"){
            for(i=0;i<arr.length;i++){
                dateForm.elements[arr[i]].value=rtnarr[i];
            }
        }

        //発注入力・受注入力の場合はsubmitする
        if(display==2 || display==5 || display==1){
            var next = '#';
            document.dateForm.action=next;
            document.dateForm.submit();
        }

        //契約マスタの場合は画面のリンク先にsubmitする
        if(display==6 || display==7){
            var next = '#'+place;
            document.dateForm.action=next;
            document.dateForm.submit();
        }

    }else{
        alert("倉庫を選択してください。");
    }
    return false;
}

//*****************得意先登録*********************

//得意先登録の口座料用
//初期表示時はテキストボックスreadonly
function Text_Disabled2(){
    document.dateForm.elements["form_account[price]"].disabled = true;
    document.dateForm.elements["form_account[rate]"].disabled = true;
    document.dateForm.elements["form_account[price]"].style.backgroundColor = "gainsboro";
    document.dateForm.elements["form_account[rate]"].style.backgroundColor = "gainsboro";
}

//得意先登録の口座料用
//チェックボタン
//チェックをつけるとテキストボックスに入力できるよ
function Check_Button2(n) {
    if(n==1){
        if(document.dateForm.elements["form_account[1]"].checked){
            document.dateForm.elements["form_account[price]"].disabled = false;
            document.dateForm.elements["form_account[price]"].style.backgroundColor = "white";
        }
        else{
            document.dateForm.elements["form_account[price]"].disabled = true;
            document.dateForm.elements["form_account[price]"].style.backgroundColor = "gainsboro";
        }
        document.dateForm.elements["form_account[2]"].checked = false;
        document.dateForm.elements["form_account[rate]"].disabled = true;
        document.dateForm.elements["form_account[rate]"].style.backgroundColor = "gainsboro";
    }
    else if(n==2){
        if(document.dateForm.elements["form_account[2]"].checked){
            document.dateForm.elements["form_account[rate]"].disabled = false;
            document.dateForm.elements["form_account[rate]"].style.backgroundColor = "white";
        }
        else{
            document.dateForm.elements["form_account[rate]"].disabled = true;
            document.dateForm.elements["form_account[rate]"].style.backgroundColor = "gainsboro";
        }
        document.dateForm.elements["form_account[1]"].checked = false;
        document.dateForm.elements["form_account[price]"].disabled = true;
        document.dateForm.elements["form_account[price]"].style.backgroundColor = "gainsboro";

    }else{
        document.dateForm.elements["form_account[1]"].checked = false;
        document.dateForm.elements["form_account[2]"].checked = false;
        document.dateForm.elements["form_account[price]"].disabled = true;
        document.dateForm.elements["form_account[rate]"].disabled = true;
        document.dateForm.elements["form_account[price]"].style.backgroundColor = "gainsboro";
        document.dateForm.elements["form_account[rate]"].style.backgroundColor = "gainsboro";
    }

    return true;
}

//*********************その他************************
//初期表示位置
function form_potision(height){
    window.scrollTo(0,height);
}

//■フォームに色をつける＆色を戻す
//inputタグ内に「onFocus="onForm(this)" onBlur="blurForm(this)"」のようにする
function onForm(which){
    if (document.all || document.getElementById){
        which.style.backgroundColor="#FDFD66"
    }
}

//フォームの色を戻す
function blurForm(which){
    if (document.all || document.getElementById){
        which.style.backgroundColor="#FFFFFF"
    }
}

function fontColor(which){
    if(which.value < 0){ 
        which.style.color="red"
    }else{
        which.style.color="black"
    }
}
function dayweek(me,num){
    week = new Array('7','1','2','3','4','5','6');
    var Y = "f_day_w"+num+"[y_input]";
    var M = "f_day_w"+num+"[m_input]";
    var D = "f_day_w"+num+"[d_input]";
    var DAY = "f_text1_"+num;
    //全て入力されているか
    if(me.elements[Y].value != "" && me.elements[M].value != "" && me.elements[D].value != "" && me.elements[Y].value.length == "4" && me.elements[M].value.length == "2" && me.elements[D].value.length == "2" && me.elements[M].value > 0 && me.elements[M].value < 13 && me.elements[D].value > 0 && me.elements[D].value < 32){
        target = new Date();
        target.setFullYear(me.elements[Y].value);
        target.setMonth(me.elements[M].value -1);
        target.setDate(me.elements[D].value);
        me.elements[DAY].value = week[target.getDay()];
    }else{
        me.elements[DAY].value = "";
    }
}

function goods_search(code,hidden,row){
    var hdn = hidden;
    var len = code.value.length;
    if(len==8){
            document.dateForm.elements[hdn].value = row;
        //同じウィンドウで遷移する
        document.dateForm.target="_self";
            //自画面に遷移する
        document.dateForm.action='#';
        document.dateForm.submit();
    }
}


function Link_Switch(url,x,y,client_id,select_id){
    var num = showModalDialog(url,window,"status:no;dialogWidth:"+x+"pt;dialogHeight:"+y+"pt;edge:raised;help:no");
    if(num==1){
        location.href = "../../../src/franchise/sale/2-2-107.php?aord_id="+select_id;
    }else if(num==2){
        location.href = "../../../src/franchise/system/2-1-115.php?client_id="+client_id+"&get_flg=cal";
    }else if(num==3){
        location.href = "../../../src/franchise/sale/2-2-108.php";
    }else if(num==4){
        location.href = "../../../src/franchise/system/2-1-114.php";
    }else if(num==5){
        location.href = "../../src/head/analysis/1-6-141.php";
    }

    return false;
}

function Link_Switch2(url,x,y,client_id,select_id){
    var num = showModalDialog(url,window,"status:no;dialogWidth:"+x+"pt;dialogHeight:"+y+"pt;edge:raised;help:no");
    if(num==1){
        location.href = "../../../src/franchise/sale/2-2-211.php?aord_id="+select_id;
    }else if(num==2){
        location.href = "../../../src/franchise/system/2-1-115.php?client_id="+client_id+"&get_flg=cal";
    }

    return false;
}

function Link_Switch3(url,x,y,client_id,select_id){
    var num = showModalDialog(url,window,"status:no;dialogWidth:"+x+"pt;dialogHeight:"+y+"pt;edge:raised;help:no");
    if(num==1){
        location.href = "../../../src/franchise/sale/2-2-202.php?sale_id="+select_id;
    }else if(num==2){
        location.href = "../../../src/franchise/system/2-1-115.php?client_id="+client_id+"&get_flg=cal";
    }

    return false;
}

function goods_search_1(me, name, hidden, row){
        //var ary =  name+"["+row+"]";
        var ary2=  hidden;
        //var len = me.elements[ary].value.length;
//        if(len==8 || len==null){
            me.elements[ary2].value = row;
            //同じウィンドウで遷移する
            document.dateForm.target="_self";
            //自画面に遷移する
            document.dateForm.action='#';
            document.dateForm.submit();
//        }
}

function c_goods_search(me, name, hidden, row,place){
    var ary2=  hidden; 
    me.elements[ary2].value = row;
    //同じウィンドウで遷移する
    document.dateForm.target="_self";
    //自画面に遷移する
    var next = '#'+place;
    document.dateForm.action=next;
    document.dateForm.submit();
}

//商品コード入力処理
function goods_search_2(me, name, hidden, row, row_num){
    var ary =  name+"["+row+"]";
    var ary2=  hidden;
    var len = me.elements[ary].value.length;
    me.elements[ary2].value = row;
    document.dateForm.target="_self";
    document.dateForm.action='#'+row_num;
    document.dateForm.submit();
    return ary;
}


function sum(num){
    var A = "form_b_stock_num["+num+"]";
    var B = "form_adjust_num["+num+"]";
    var C = "form_a_stock_num["+num+"]";
    var D = "form_io_type["+num+"]";
    var E = "form_goods_cd["+num+"]";
    
    for (var i = 0; i < document.dateForm.elements[D].length; i++){
        if(document.dateForm.elements[D][i].checked == true){
            var kubun = document.dateForm.elements[D][i].value;
        }
    }
        
    if (document.dateForm.elements[E].value == ''){
        document.dateForm.elements[C].value = ''; 
        return false;
    }

    if(kubun == '1'){
        var count = (document.dateForm.elements[A].value) - (-(document.dateForm.elements[B].value));
    }else{
        var count = (document.dateForm.elements[A].value) - (document.dateForm.elements[B].value);
    }

    if(isNaN(count)==false ){
        document.dateForm.elements[C].value = count; 
    }
}


function price_amount(num){
    var A = "form_buy_price["+num+"][i]";
    var B = "form_buy_price["+num+"][d]";
    var C = "form_order_num["+num+"]";
    var D = "form_buy_amount["+num+"]";
    var E = "form_goods_cd["+num+"]";

    if(document.dateForm.elements[E].value = ''){
        document.dateForm.elements[D].value = '';
        return false;
    }else{

        var count = (document.dateForm.elements[C].value) * ((document.dateForm.elements[A].value)+(document.dateForm.elements[B].value)/10);

        if(isNaN(count) == false){
            document.dateForm.elements[D].value = count;
        }
    }
}
/**
 * ページタイトルのテーブルサイズ指定
 * @param   {void}      void
 * @return  {void}      void
 * @version 
 * @author  
 */
function WindowSizeChange(){
    //IDがpage_title_tableのテーブルがあるか確認
    if(document.getElementById("page_title_table") != null){
        //IE用ウィンドウサイズの横幅を取得＆ページタイトルテーブルサイズ指定
        if(document.all){
            document.all("page_title_table").style.width = document.body.clientWidth -50;
        //NN、FFとか用ウィンドウサイズの横幅を取得＆ページタイトルテーブルサイズ指定
        }else if(document.getElementById){
            document.getElementById("page_title_table").style.width = window.innerWidth -50;
        }
    }
}
/*
window.onresize = WindowSizeChange;
window.onload = WindowSizeChange;
*/

//EnterキーをTabキーへ置き換える
function chgKeycode(){ 
	if( window.event.keyCode == 0x0d ) 
	{ 
		//window.event.keyCode = 0x09;
		for (j = 0; j < document.forms.length; j++) {
			for (i = 0; i < document.forms[j].elements.length - 1; i++) {
				if (document.activeElement == document.forms[0].elements[i]) {
					document.forms[j].elements[i + 1].focus();
					break;
				}
			}
		}
		return false;
	}
} 

//メニューを常に真ん中に表示
function Move_menu(){
    //真中を計算
    nWinWidth = document.body.scrollLeft;

    //レイヤーの幅と高さを座標にセット
    menu.style.left = nWinWidth+19; 
    menu.style.top = 0;
    nTimer=setTimeout("Move_menu()",100);
}

//別ウインドウへsubmitする
function Submit_Blank_Window(post_url,msg){
    if(Dialogue4(msg)){
        //別ウィンドウへsubmitする
        document.forms[0].target="_blank";
        document.forms[0].action=post_url;
        document.forms[0].submit();

        //submit先を自画面に戻す
        document.forms[0].target="_self";
        document.forms[0].action=document.URL;
    }

}

//出力形式(画面・帳票)でsubmit先を変更する
function Submit_If_Url(post_url,f_name){
        var name = f_name;

    //帳票にチェックがある場合は、帳票画面へsubmit
    if(document.forms[0].elements[name][1].checked == true){
        Submit_Blank_Window(post_url,"帳票を出力します。")
        return false;

    //自画面へsubmit
    }else{
        //document.forms[0].submit();
        return true;
    }
}

/**
 * 2重POST防止
 * ボタンはinput type=buttonで（submitじゃダメよ）
 * 
 * @param   {btn_name}      string      disabledにするボタン名
 * @param   {hdn_name}      string      ボタンの代わりに飛ばすhidden名
 * @param   {hdn_value}     string      hiddenで飛ばすvalue
 * 
 * @version
 * @author
 */
function Double_Post_Prevent(btn_name, hdn_name, hdn_value)
{

    var BN = btn_name;
    var HN = hdn_name;
    var HV = hdn_value;

    dateForm.elements[BN].disabled=true;
    dateForm.elements[HN].value=HV;
    dateForm.submit();

    return;

}


function Double_Post_Prevent2(me)
{

    var btn_name = me.name;
    var btn_val  = me.value;
    var hdn_name = btn_name.substr(4);

    me.disabled = true;
    document.forms[0].elements[hdn_name].value = btn_val;
    dateForm.submit();

    return;

}
