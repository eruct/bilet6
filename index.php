<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 600px; 
            margin: 20px auto; 
            padding: 20px; 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
        }
        input, textarea { 
            width: 100%; 
            padding: 8px; 
            box-sizing: border-box; 
        }
        button { 
            background: #007bff; 
            color: white; border: none; 
            padding: 10px 15px; 
            cursor: pointer; }
        .message { 
            padding: 10px; 
            margin-top: 15px; 
            border-radius: 4px; 
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
        }
    </style>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Email получателя:</label>
            <input type="email" name="recipient_email" required>
        </div>
        <div class="form-group">
            <label>Тема письма:</label>
            <input type="text" name="subject" required>
        </div>
        <div class="form-group">
            <label>Текст сообщения:</label>
            <textarea name="message" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label>Прикрепить файл (макс. 5MB):</label>
            <input type="file" name="attachment">
        </div>
        <button type="submit">Отправить</button>
    </form>

    <?php
    define('SMTP_USER', '№');
    define('SMTP_PASSWORD', '#'); 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Подключение PHPMailer
        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $recipient = filter_var($_POST['recipient_email'], FILTER_VALIDATE_EMAIL);
            $subject = htmlspecialchars($_POST['subject']);
            $body = htmlspecialchars($_POST['message']);
            
            if (!$recipient) {
                throw new Exception("Некорректный email получателя");
            }

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
                $maxFileSize = 5 * 1024 * 1024; // 5MB
                if ($_FILES['attachment']['size'] > $maxFileSize) {
                    throw new Exception("Размер файла превышает 5MB");
                }
            }

            $mail->isSMTP();
            $mail->Host = 'smtp.mail.ru';
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_USER, 'Отправитель');
            $mail->addAddress($recipient);

            $mail->Subject = $subject;
            $mail->Body = $body;

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
                $mail->addAttachment(
                    $_FILES['attachment']['tmp_name'],
                    $_FILES['attachment']['name']
                );
            }

            $mail->send();
            echo '<div class="message success">Письмо успешно отправлено!</div>';
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            echo '<div class="message error">Ошибка отправки: ' . $e->getMessage() . '</div>';
        } catch (Exception $e) {
            echo '<div class="message error">Ошибка: ' . $e->getMessage() . '</div>';
        }
    }
    ?>
</body>
</html>