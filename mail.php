<?php
mb_language('Japanese');
mb_internal_encoding('UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$name    = mb_convert_encoding(strip_tags($_POST['name']    ?? ''), 'UTF-8', 'auto');
$email   = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$subject = mb_convert_encoding(strip_tags($_POST['subject'] ?? ''), 'UTF-8', 'auto');
$message = mb_convert_encoding(strip_tags($_POST['message'] ?? ''), 'UTF-8', 'auto');

if (!$name || !$email || !$subject || !$message || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.html#contact');
    exit;
}

$to = 'singeryoh2020@gmail.com';

$mail_subject = mb_encode_mimeheader('[お問い合わせ] ' . $subject, 'UTF-8', 'B');

$mail_body = <<<EOT
星 陽子 オフィシャルサイト お問い合わせ
----------------------------------------
お名前　　: {$name}
メール　　: {$email}
件名　　　: {$subject}

メッセージ:
{$message}
----------------------------------------
EOT;

$headers  = 'From: noreply@hoshiyoko.com' . "\r\n";
$headers .= 'Reply-To: ' . $email . "\r\n";
$headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
$headers .= 'Content-Transfer-Encoding: base64' . "\r\n";

$result = mb_send_mail($to, $mail_subject, $mail_body, $headers);

if ($result) {
    header('Location: index.html?sent=1#contact');
} else {
    header('Location: index.html?error=1#contact');
}
exit;
