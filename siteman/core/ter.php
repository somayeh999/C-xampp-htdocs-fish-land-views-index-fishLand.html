<?php

require_once('PHPMailer/class.smtp.php');
require_once('PHPMailer/class.phpmailer.php');

$mail=new SMTP();

$mail=new PHPMailer();

$mail->isSMTP();
$mail->Host='mail.selectpersia.com';
$mail->SMTPAuth=true;

$mail->Username='info@selectpersia.com';

$mail->Password='Mehdijd+select4820*1374*';

// $mail->SMTPSecure='ssl';

$mail->Port=587;

$mail->Subject='selectpersia';

$mail->From='info@selectpersia.com';

$mail->CharSet='utf-8';

$mail->FromName='fromname';

$mail->ContentType='text/html;charset=utf-8';

$messagehtml='messagehtml';

$messagetext='messagetext';

$mail->isHTML(true);

$mail->addAddress('mr.mehdijavanmard@gmail.com');

$mail->Body=$messagehtml;

$mail->AltBody=$messagetext;

$mail->send();




if($mail->isError()){

    echo 'error';

}

else{

    echo 'ok';

}
$mail->smtpClose();
