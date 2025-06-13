<?php
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $code = generateVerificationCode();
        sendVerificationEmail($email, $code);
        if (!is_dir('codes')) {
            mkdir('codes');
        }
        file_put_contents("codes/$email.txt", $code);
        $message = "Verification code sent to $email.";
    } elseif (isset($_POST['verification_code']) && isset($_POST['verify_email'])) {
        $email = $_POST['verify_email'];
        $userCode = $_POST['verification_code'];
        $actualCode = @file_get_contents("codes/$email.txt");
        if (trim($actualCode) === trim($userCode)) {
            registerEmail($email);
            unlink("codes/$email.txt");
            $message = "Email verified and subscribed!";
        } else {
            $message = "Invalid verification code. Refresh page.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subscribe to XKCD</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: #f9f9f9;
        }
        .center-container {
            background: #fff;
            padding: 2em 3em;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            text-align: center;
        }
        form {
            margin: 1em 0;
        }
        input, button {
            padding: 0.5em;
            margin: 0.3em 0;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <div class="center-container">
        <h2>Subscribe to XKCD</h2>
        <p><?= htmlspecialchars($message) ?></p>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button id="submit-email">Submit</button>
        </form>

        <form method="POST">
            <input type="hidden" name="verify_email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <input type="text" name="verification_code" maxlength="6" placeholder="Verification code" required>
            <button id="submit-verification">Verify</button>
        </form>
    </div>
</body>
</html>