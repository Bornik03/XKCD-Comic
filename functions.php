<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendPHPMailer($to, $subject, $bodyHtml) {
    $mail = new PHPMailer(true);

    try {
        // SMTP setup
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bornikdekaviraj@gmail.com';         // 🔒 Your Gmail address
        $mail->Password = 'dfia uxlm erom pcch';           // 🔒 App password (NOT Gmail password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email content
        $mail->setFrom('yourgmail@gmail.com', 'XKCD Bot');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function sendVerificationEmail($email, $code, $unsubscribe = false) {
    $subject = $unsubscribe ? "Confirm Un-subscription" : "Your Verification Code";
    $body = $unsubscribe ?
        "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>" :
        "<p>Your verification code is: <strong>$code</strong></p>";

    sendPHPMailer($email, $subject, $body);
}

function verifyCode($actual, $userInput) {
    return trim($actual) === trim($userInput);
}

function fetchAndFormatXKCDData() {
    $randomId = rand(1, 2800);
    $url = "https://xkcd.com/$randomId/info.0.json";
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    $img = $data['img'] ?? '';
    $title = $data['safe_title'] ?? 'XKCD Comic';
    return "<h2>$title</h2><img src=\"$img\" alt=\"XKCD Comic\" style=\"max-width:100%;\"><p><a href=\"http://localhost:8000/unsubscribe.php\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
}

function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES);
    $content = fetchAndFormatXKCDData();
    $subject = "Your XKCD Comic";

    foreach ($emails as $email) {
        sendPHPMailer($email, $subject, $content);
    }
}
