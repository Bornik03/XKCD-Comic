<?php
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = $_POST['unsubscribe_email'];
        $code = generateVerificationCode();
        sendVerificationEmail($email, $code, true);
        file_put_contents("codes/unsub_$email.txt", $code);
        $message = "Unsubscription code sent to $email.";
    } elseif (isset($_POST['verification_code']) && isset($_POST['unsub_email'])) {
        $email = $_POST['unsub_email'];
        $userCode = $_POST['verification_code'];
        $actualCode = @file_get_contents("codes/unsub_$email.txt");
        if (verifyCode($actualCode, $userCode)) {
            unsubscribeEmail($email);
            unlink("codes/unsub_$email.txt");
            $message = "Unsubscribed successfully.";
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<h2>Unsubscribe</h2>
<p><?= $message ?></p>

<form method="POST">
    <input type="email" name="unsubscribe_email" required>
    <button id="submit-unsubscribe">Unsubscribe</button>
</form>

<form method="POST">
    <input type="hidden" name="unsub_email" value="<?= htmlspecialchars($_POST['unsubscribe_email'] ?? '') ?>">
    <input type="text" name="verification_code" maxlength="6" required>
    <button id="submit-verification">Verify</button>
</form>
