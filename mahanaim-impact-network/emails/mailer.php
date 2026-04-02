<?php
// === emails/mailer.php ===

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $toName, $subject, $htmlBody, $attachments = []) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change to your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Your email
        $mail->Password = 'your_app_password'; // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('noreply@mahanaimnetwork.org', 'Mahanaim Impact Network');
        $mail->addAddress($to, $toName);
        $mail->addReplyTo('info@mahanaimnetwork.org', 'Mahanaim Support');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);
        
        // Attachments
        foreach ($attachments as $attachment) {
            if (file_exists($attachment['path'])) {
                $mail->addAttachment($attachment['path'], $attachment['name']);
            }
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

function getEmailTemplate($title, $content) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
                background: #f4f4f4;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
            .header {
                background: linear-gradient(135deg, #4B0082 0%, #6A0DAD 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
            }
            .content {
                padding: 30px;
            }
            .button {
                display: inline-block;
                padding: 12px 30px;
                background: #D4AF37;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 10px 0;
                font-weight: 600;
            }
            .footer {
                background: #f8f9fa;
                padding: 20px;
                text-align: center;
                color: #666;
                font-size: 14px;
            }
            .social-links {
                margin: 15px 0;
            }
            .social-links a {
                color: #4B0082;
                margin: 0 10px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>{$title}</h1>
                <p style='margin: 0; opacity: 0.9;'>Mahanaim Impact Network</p>
            </div>
            <div class='content'>
                {$content}
            </div>
            <div class='footer'>
                <p style='margin: 0 0 10px 0;'><strong>Empowering Purpose. Building Legacy.</strong></p>
                <p style='margin: 0 0 10px 0;'>
                    <a href='mailto:info@mahanaimnetwork.org' style='color: #4B0082;'>info@mahanaimnetwork.org</a>
                </p>
                <div class='social-links'>
                    <a href='#'>Facebook</a> |
                    <a href='#'>Twitter</a> |
                    <a href='#'>Instagram</a> |
                    <a href='#'>LinkedIn</a>
                </div>
                <p style='margin: 10px 0 0 0; font-size: 12px; color: #999;'>
                    &copy; " . date('Y') . " Mahanaim Impact Network. All Rights Reserved.
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}
?>
