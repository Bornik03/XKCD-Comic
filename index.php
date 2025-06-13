<?php
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $code = generateVerificationCode();
        sendVerificationEmail($email, $code);
        file_put_contents("codes/$email.txt", $code);
        $message = "Verification code sent to $email.";
    } elseif (isset($_POST['verification_code']) && isset($_POST['verify_email'])) {
        $email = $_POST['verify_email'];
        $userCode = $_POST['verification_code'];
        $actualCode = @file_get_contents("codes/$email.txt");
        if (verifyCode($actualCode, $userCode)) {
            registerEmail($email);
            unlink("codes/$email.txt");
            $message = "Email verified and subscribed!";
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<h2>Subscribe to XKCD</h2>
<p><?= $message ?></p>

<form method="POST">
    <input type="email" name="email" required>
    <button id="submit-email">Submit</button>
</form>

<form method="POST">
    <input type="hidden" name="verify_email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    <input type="text" name="verification_code" maxlength="6" required>
    <button id="submit-verification">Verify</button>
</form>
