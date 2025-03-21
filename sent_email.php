<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure PHPMailer is installed via Composer

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];

if (!$email) {
    echo json_encode(['error' => '未提供 Email']);
    exit;
}

// Generate 6-digit verification code
$verification_code = rand(100000, 999999);

// Send email
$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dbecommercesite@gmail.com'; // Replace with your Gmail
    $mail->Password = 'fpdq zxsi xdbo sbzg';    // Replace with your Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Email content
    $mail->setFrom('your_gmail@gmail.com', 'Ecommercesite');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Ecommercesite Verification Code';
    $mail->Body    = "<h2>您的驗證碼是：<strong>$verification_code</strong></h2><p>請在 5 分鐘內使用。</p>";

    $mail->send();

    // Return code to frontend (DO NOT do this in production, better store server-side)
    echo json_encode(['code' => $verification_code]);

} catch (Exception $e) {
    echo json_encode(['error' => '信件寄送失敗: ' . $mail->ErrorInfo]);
}
?>