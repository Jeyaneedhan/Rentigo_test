<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Rentigo</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .legal-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 1rem;
        }

        .legal-modal.show {
            display: flex;
        }

        .legal-modal-content {
            width: min(1100px, 96vw);
            height: min(88vh, 900px);
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
        }

        .legal-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }

        .legal-modal-title {
            margin: 0;
            font-size: 0.95rem;
            color: #1f2937;
            font-weight: 600;
        }

        .legal-modal-close {
            border: none;
            background: transparent;
            color: #4b5563;
            font-size: 1.25rem;
            cursor: pointer;
            line-height: 1;
            padding: 0.25rem;
        }

        .legal-modal-close:hover {
            color: #111827;
        }

        .legal-modal-frame {
            width: 100%;
            height: 100%;
            border: 0;
            background: #ffffff;
        }
    </style>
</head>

<body>
    <div class="auth-register-container">
        <div class="auth-card">
            <!-- Logo Section -->
            <div class="auth-header">
                <div class="logo">
                    <h1>Rentigo</h1>
                </div>
                <h2>Welcome to</h2>
                <h3>Rental Property Management System</h3>
                <p class="subtitle">Enter your information below to continue</p>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Account Type</div>
                </div>
                <div class="progress-line completed"></div>
                <div class="progress-step active">
                    <div class="step-number">2</div>
                    <div class="step-label">Your Details</div>
                </div>
            </div>

            <!-- Selected User Type Display -->
            <div class="selected-type-display">
                <div class="selected-type-info">
                    <i class="fas fa-<?php echo getUserTypeIcon($data['user_type']); ?>"></i>
                    <span>Creating <?php echo ucfirst(str_replace('_', ' ', $data['user_type'])); ?> Account</span>
                </div>
                <a href="<?php echo URLROOT; ?>/users/usertype" class="change-type-btn">Change</a>
            </div>

            <!-- Registration Form -->
            <form class="auth-form" action="<?php echo URLROOT; ?>/users/register" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email"
                        id="email"
                        name="email"
                        placeholder="kamrul@gmail.com"
                        value="<?php echo $data['email']; ?>"
                        required>
                    <?php if (!empty($data['email_err'])): ?>
                        <span class="error-message"><?php echo $data['email_err']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text"
                        id="name"
                        name="name"
                        placeholder="Kamrul Hassan"
                        value="<?php echo $data['name']; ?>"
                        required>
                    <?php if (!empty($data['name_err'])): ?>
                        <span class="error-message"><?php echo $data['name_err']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Create Password</label>
                        <div class="password-input">
                            <input type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', 'password-eye1')">
                                <i class="fas fa-eye" id="password-eye1"></i>
                            </button>
                        </div>
                        <?php if (!empty($data['password_err'])): ?>
                            <span class="error-message"><?php echo $data['password_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="password-input">
                            <input type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="••••••••"
                                required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'password-eye2')">
                                <i class="fas fa-eye" id="password-eye2"></i>
                            </button>
                        </div>
                        <?php if (!empty($data['confirm_password_err'])): ?>
                            <span class="error-message"><?php echo $data['confirm_password_err']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php require APPROOT . '/views/inc/components/terms_checkbox.php'; ?>

                <?php if (!empty($data['terms_err'])): ?>
                    <span class="error-message" style="display: block; margin-top: -0.5rem; margin-bottom: 1rem;">
                        <?php echo $data['terms_err']; ?>
                    </span>
                <?php endif; ?>

                <div class="form-actions-row">
                    <a href="<?php echo URLROOT; ?>/users/usertype" class="auth-btn secondary">Back</a>
                    <button type="submit" class="auth-btn primary">Create Account</button>
                </div>

                <div class="auth-footer">
                    <p>Already have an account? <a href="<?php echo URLROOT; ?>/users/login">Login Here</a></p>
                </div>
            </form>
        </div>
    </div>

    <div class="legal-modal" id="legalModal" aria-hidden="true">
        <div class="legal-modal-content" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
            <div class="legal-modal-header">
                <h4 class="legal-modal-title" id="legalModalTitle">Legal Document</h4>
                <button type="button" class="legal-modal-close" id="legalModalClose" aria-label="Close">&times;</button>
            </div>
            <iframe id="legalModalFrame" class="legal-modal-frame" title="Legal Document"></iframe>
        </div>
    </div>

    <script>
        const REGISTER_FORM_STATE_KEY = 'rentigo_register_form_state_v1';

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

        function saveRegisterFormState() {
            const email = document.getElementById('email');
            const name = document.getElementById('name');
            const acceptTerms = document.getElementById('accept_terms');

            const state = {
                email: email ? email.value : '',
                name: name ? name.value : '',
                acceptTerms: acceptTerms ? acceptTerms.checked : false
            };

            sessionStorage.setItem(REGISTER_FORM_STATE_KEY, JSON.stringify(state));
        }

        function restoreRegisterFormState() {
            const raw = sessionStorage.getItem(REGISTER_FORM_STATE_KEY);
            if (!raw) {
                return;
            }

            try {
                const state = JSON.parse(raw);
                const email = document.getElementById('email');
                const name = document.getElementById('name');
                const acceptTerms = document.getElementById('accept_terms');

                // Only fill when server hasn't already populated these fields.
                if (email && !email.value && state.email) {
                    email.value = state.email;
                }

                if (name && !name.value && state.name) {
                    name.value = state.name;
                }

                if (acceptTerms && !acceptTerms.checked && state.acceptTerms) {
                    acceptTerms.checked = true;
                }
            } catch (e) {
                sessionStorage.removeItem(REGISTER_FORM_STATE_KEY);
            }
        }

        // Restore form state when user returns from Terms/Privacy pages.
        restoreRegisterFormState();

        // Keep state up to date while typing/checking.
        ['email', 'name', 'accept_terms'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', saveRegisterFormState);
                el.addEventListener('change', saveRegisterFormState);
            }
        });

        document.querySelectorAll('.terms-link').forEach((link) => {
            link.addEventListener('click', saveRegisterFormState);
        });

        const legalModal = document.getElementById('legalModal');
        const legalModalFrame = document.getElementById('legalModalFrame');
        const legalModalTitle = document.getElementById('legalModalTitle');
        const legalModalClose = document.getElementById('legalModalClose');

        function openLegalModal(url, title) {
            if (!legalModal || !legalModalFrame || !legalModalTitle) {
                window.location.href = url;
                return;
            }

            legalModalTitle.textContent = title;
            legalModalFrame.src = url;
            legalModal.classList.add('show');
            legalModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeLegalModal() {
            if (!legalModal || !legalModalFrame) {
                return;
            }

            legalModal.classList.remove('show');
            legalModal.setAttribute('aria-hidden', 'true');
            legalModalFrame.src = '';
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.terms-link').forEach((link) => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                saveRegisterFormState();

                const url = this.getAttribute('href');
                const title = this.textContent.trim() || 'Legal Document';
                openLegalModal(url, title);
            });
        });

        if (legalModalClose) {
            legalModalClose.addEventListener('click', closeLegalModal);
        }

        if (legalModal) {
            legalModal.addEventListener('click', function(e) {
                if (e.target === legalModal) {
                    closeLegalModal();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && legalModal && legalModal.classList.contains('show')) {
                closeLegalModal();
            }
        });

        window.addEventListener('beforeunload', saveRegisterFormState);

        // Form validation
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            saveRegisterFormState();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    </script>
</body>

</html>

<?php
// Helper function to get user type icon
function getUserTypeIcon($userType)
{
    switch ($userType) {
        case 'tenant':
            return 'user';
        case 'landlord':
            return 'home';
        case 'property_manager':
            return 'users';
        case 'admin':
            return 'cog';
        default:
            return 'user';
    }
}
?>