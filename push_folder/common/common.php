<?php

function sanitize($before)
{
    foreach($before as $key => $val)
    {
        $after[$key] = htmlspecialchars($val,ENT_QUOTES,'UTF-8');
    }
    return $after;
}

function pulldown_year()
{
    print '<select name="year">';
    print '<option value="2018">2018</option>';
    print '<option value="2019">2019</option>';
    print '<option value="2020">2020</option>';
    print '<option value="2021">2021</option>';
    print '<option value="2022">2022</option>';
    print '<option value="2023">2023</option>';
    print '</select>';
}

function pulldown_month()
{
    print '<select name="month">';
    print '<option value="01">1</option>';
    print '<option value="02">2</option>';
    print '<option value="03">3</option>';
    print '<option value="04">4</option>';
    print '<option value="05">5</option>';
    print '<option value="06">6</option>';
    print '<option value="07">7</option>';
    print '<option value="08">8</option>';
    print '<option value="09">9</option>';
    print '<option value="10">10</option>';
    print '<option value="11">11</option>';
    print '<option value="12">12</option>';
    print '</select>';
}

function pulldown_day()
{
    print '<select name="day">';
    print '<option value="01">1</option>';
    print '<option value="02">2</option>';
    print '<option value="03">3</option>';
    print '<option value="04">4</option>';
    print '<option value="05">5</option>';
    print '<option value="06">6</option>';
    print '<option value="07">7</option>';
    print '<option value="08">8</option>';
    print '<option value="09">9</option>';
    print '<option value="10">10</option>';
    print '<option value="11">11</option>';
    print '<option value="12">12</option>';
    print '<option value="13">13</option>';
    print '<option value="14">14</option>';
    print '<option value="15">15</option>';
    print '<option value="16">16</option>';
    print '<option value="17">17</option>';
    print '<option value="18">18</option>';
    print '<option value="19">19</option>';
    print '<option value="20">20</option>';
    print '<option value="21">21</option>';
    print '<option value="22">22</option>';
    print '<option value="23">23</option>';
    print '<option value="24">24</option>';
    print '<option value="25">25</option>';
    print '<option value="26">26</option>';
    print '<option value="27">27</option>';
    print '<option value="28">28</option>';
    print '<option value="29">29</option>';
    print '<option value="30">30</option>';
    print '<option value="31">31</option>';
    print '</select>';
}

//ランダムパスワード生成
function create_password(){

	$pwd = str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789');

	$str = substr(str_shuffle($pwd), 0, 8);// 先頭８桁をランダムパスワードとして使う

	// 大文字小文字の英字と数字が混在するかどうかをチェック
	// 混在すれば、パスワードを返し
	if( preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).*$/',$str) ){ // コーディング量が少ない反面、読みづらい、理解しにくい正規表現
		return $str;
	}
	// 混在しなければ、もう一度再帰関数を呼び出し
	else{
		return create_password();
	}

}

?>
