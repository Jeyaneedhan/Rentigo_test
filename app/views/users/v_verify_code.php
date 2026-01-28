<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - Rentigo</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="auth-login-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <h1>Rentigo</h1>
                </div>
                <h2>Verify Code</h2>
                <p class="subtitle">Enter the 6-digit code sent to your email.</p>
            </div>

            <?php if (isset($_SESSION['reset_flash'])): ?>
                <div class="flash-message <?php echo (strpos($_SESSION['reset_flash'], 'Failed') !== false) ? 'error' : 'success'; ?>">
                    <?php
                    echo $_SESSION['reset_flash'];
                    unset($_SESSION['reset_flash']);
                    ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" action="<?php echo URLROOT; ?>/users/verifyCode" method="POST">
                <div class="form-group <?php echo (!empty($data['code_err'])) ? 'has-error' : ''; ?>">
                    <label for="code">Verification Code</label>
                    <input type="text"
                        id="code"
                        name="code"
                        placeholder="••••••"
                        maxlength="6"
                        pattern="\d{6}"
                        value="<?php echo $data['code']; ?>"
                        required
                        style="text-align: center; font-size: 24px; letter-spacing: 10px;">
                    <?php if (!empty($data['code_err'])): ?>
                        <span class="error-message"><?php echo $data['code_err']; ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="auth-btn">Verify Code</button>

                <div class="auth-footer">
                    <p>Didn't receive code? <a href="<?php echo URLROOT; ?>/users/forgotPassword">Try again</a></p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
