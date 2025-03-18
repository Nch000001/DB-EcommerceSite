require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 建立 PHPMailer 實例
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com';
    $mail->Password = 'your_app_password'; // 應用程式密碼
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // 設定收件人
    $mail->setFrom('your_email@gmail.com', 'Your Store');
    $mail->addAddress('receiver@example.com');

    // 設定郵件內容
    $mail->Subject = '您的驗證碼';
    $mail->Body = '您的驗證碼是 123456';

    // 發送郵件
    $mail->send();
    echo '郵件發送成功！';
} catch (Exception $e) {
    echo "郵件發送失敗：" . $mail->ErrorInfo;
}
