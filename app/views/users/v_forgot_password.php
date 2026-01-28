<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Rentigo</title>
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
                <h2>Forgot Password?</h2>
                <p class="subtitle">Enter your email and we'll send you a code.</p>
            </div>

            <form class="auth-form" action="<?php echo URLROOT; ?>/users/forgotPassword" method="POST">
                <div class="form-group <?php echo (!empty($data['email_err'])) ? 'has-error' : ''; ?>">
                    <label for="email">Email</label>
                    <input type="email"
                        id="email"
                        name="email"
                        placeholder="yourname@gmail.com"
                        value="<?php echo $data['email']; ?>"
                        required>
                    <?php if (!empty($data['email_err'])): ?>
                        <span class="error-message"><?php echo $data['email_err']; ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="auth-btn">Send Code</button>

                <div class="auth-footer">
                    <p>Remember your password? <a href="<?php echo URLROOT; ?>/users/login">Log In</a></p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
