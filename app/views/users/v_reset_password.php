<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Rentigo</title>
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
                <h2>New Password</h2>
                <p class="subtitle">Please enter your new password.</p>
            </div>

            <form class="auth-form" action="<?php echo URLROOT; ?>/users/resetPassword" method="POST">
                <div class="form-group <?php echo (!empty($data['password_err'])) ? 'has-error' : ''; ?>">
                    <label for="password">New Password</label>
                    <div class="password-input">
                        <input type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'password-eye')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    <?php if (!empty($data['password_err'])): ?>
                        <span class="error-message"><?php echo $data['password_err']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?php echo (!empty($data['confirm_password_err'])) ? 'has-error' : ''; ?>">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-input">
                        <input type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="••••••••"
                            required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'confirm-eye')">
                            <i class="fas fa-eye" id="confirm-eye"></i>
                        </button>
                    </div>
                    <?php if (!empty($data['confirm_password_err'])): ?>
                        <span class="error-message"><?php echo $data['confirm_password_err']; ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="auth-btn">Reset Password</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, eyeId) {
            const passwordInput = document.getElementById(inputId);
            const passwordEye = document.getElementById(eyeId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordEye.classList.remove('fa-eye');
                passwordEye.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordEye.classList.remove('fa-eye-slash');
                passwordEye.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
