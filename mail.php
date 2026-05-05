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

$admin_to = 'singeryoh2020@gmail.com';

// 管理者への通知メール
$admin_subject = mb_encode_mimeheader('[お問い合わせ] ' . $subject, 'UTF-8', 'B');
$admin_body = <<<EOT
星 陽子 オフィシャルサイト お問い合わせ
----------------------------------------
お名前　　: {$name}
メール　　: {$email}
件名　　　: {$subject}

メッセージ:
{$message}
----------------------------------------
EOT;
$admin_headers  = 'From: noreply@hoshiyoko.com' . "\r\n";
$admin_headers .= 'Reply-To: ' . $email . "\r\n";
$admin_headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
$admin_headers .= 'Content-Transfer-Encoding: base64' . "\r\n";

$result = mb_send_mail($admin_to, $admin_subject, $admin_body, $admin_headers);

// お客様への自動返信メール
$reply_subject = mb_encode_mimeheader('【星 陽子 オフィシャルサイト】お問い合わせを受け付けました', 'UTF-8', 'B');
$reply_body = <<<EOT
{$name} 様

このたびはお問い合わせいただき、ありがとうございます。
以下の内容でお問い合わせを受け付けました。

担当者よりご連絡いたしますので、しばらくお待ちください。

----------------------------------------
件名　　　: {$subject}

メッセージ:
{$message}
----------------------------------------

※このメールは自動送信されています。
※このメールに返信いただいてもお答えできません。

星 陽子 オフィシャルサイト
EOT;
$reply_headers  = 'From: 星 陽子 オフィシャルサイト <singeryoh2020@gmail.com>' . "\r\n";
$reply_headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
$reply_headers .= 'Content-Transfer-Encoding: base64' . "\r\n";

mb_send_mail($email, $reply_subject, $reply_body, $reply_headers);

if ($result) {
    header('Location: index.html?sent=1#contact');
} else {
    header('Location: index.html?error=1#contact');
}
exit;
