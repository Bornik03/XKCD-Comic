<?php

function generateVerificationCode(): string {
    return str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function sendVerificationEmail(string $email, string $code): bool {
    $subject = "Your Verification Code";
    $body = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "From: XKCD Bot <no-reply@example.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    return mail($email, $subject, $body, $headers);
}

function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (!in_array($email, $emails, true)) {
        return file_put_contents($file, $email . PHP_EOL, FILE_APPEND) !== false;
    }
    return false;
}

function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return false;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    return file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL) !== false;
}

function fetchAndFormatXKCDData(): string {
    $randomId = rand(1, 2800);
    $url = "https://xkcd.com/$randomId/info.0.json";
    $json = @file_get_contents($url);
    if (!$json) return "<p>Could not fetch XKCD comic.</p>";
    $data = json_decode($json, true);
    $img = $data['img'] ?? '';
    $title = $data['title'] ?? '';
    $alt = $data['alt'] ?? '';
    $comicUrl = "https://xkcd.com/$randomId/";
    return "<h2>XKCD Comic: " . htmlspecialchars($title) . "</h2>
            <a href=\"$comicUrl\" target=\"_blank\">
                <img src=\"" . htmlspecialchars($img) . "\" alt=\"" . htmlspecialchars($alt) . "\" style=\"max-width:100%;height:auto;\">
            </a>
            <p>" . htmlspecialchars($alt) . "</p>
            <p><a href=\"http://localhost:8000/unsubscribe.php\">Unsubscribe</a></p>";
}

function sendXKCDUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $content = fetchAndFormatXKCDData();
    $subject = "Your XKCD Comic";
    $sendEmail = function(string $to, string $subject, string $body, bool $isHtml = true): bool {
        $headers = "From: XKCD Bot <no-reply@example.com>\r\n";
        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        }
        return mail($to, $subject, $body, $headers);
    };
    foreach ($emails as $email) {
        $sendEmail($email, $subject, $content);
    }
}