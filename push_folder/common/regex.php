<?php

//メールアドレスのみ許可
$email_checker = '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/';

//半角数字のみ許可 *郵便番号、
$halfsizenumber_checker = '/\A[0-9]+\z/';

//半角数字とハイフンのみ許可 *電話番号、
$halfsizenumber_and_hyphen_checker = '/\A\d{2,5}-?\d{2,5}-?\d{4,5}\z/';

?>