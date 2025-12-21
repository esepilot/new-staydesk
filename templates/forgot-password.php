<style>
        body {
            margin: 0;
            padding: 0;
            background: #0a0a0a;
        }
        
        .staydesk-forgot {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .staydesk-forgot::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.5; }
        }
        
        .forgot-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.8);
            max-width: 520px;
            width: 100%;
            animation: slideIn 0.6s ease-out;
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .forgot-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .forgot-header h1 {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.2rem;
            margin-bottom: 12px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
        }
        
        .forgot-header p {
            color: #B8B8B8;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.3px;
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.3rem;
            cursor: pointer;
            color: #D4AF37;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #FFD700;
            transform: translateY(-50%) scale(1.1);
        }
        
        .form-group input {
            width: 100%;
            padding: 16px 18px;
            border: 2px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background: rgba(26, 26, 26, 0.8);
            color: #FFFFFF;
        }
        
        .form-group input::placeholder {
            color: #888;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2);
            background: rgba(42, 42, 42, 0.9);
        }
        
        .btn-submit {
            width: 100%;
            padding: 17px;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            border: none;
            border-radius: 12px;
            font-size: 1.15rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.5px;
            box-shadow: 0 6px 25px rgba(212, 175, 55, 0.4);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.6);
        }
        
        .btn-submit:disabled {
            background: linear-gradient(135deg, #666 0%, #888 100%);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .login-link {
            text-align: center;
            margin-top: 28px;
            color: #A0A0A0;
            font-size: 1rem;
        }
        
        .login-link a {
            color: #D4AF37;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            color: #FFD700;
            text-decoration: underline;
        }
        
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 22px;
            display: none;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(35, 150, 60, 0.15) 100%);
            color: #4ADE80;
            border: 1px solid rgba(40, 167, 69, 0.4);
        }
        
        .alert-error {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(200, 35, 51, 0.15) 100%);
            color: #FF6B7A;
            border: 1px solid rgba(220, 53, 69, 0.4);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hidden {
            display: none;
        }
        
        @media (max-width: 768px) {
            .forgot-container {
                padding: 30px;
            }
        }
    </style>
    
<div class="staydesk-forgot">
        <div class="forgot-container">
            <div class="forgot-header">
                <h1>🔐 Forgot Password</h1>
                <p>Reset your password in 2 simple steps</p>
            </div>
            
            <div id="forgot-alert" class="alert"></div>
            
            <!-- Step 1: Request Code -->
            <form id="request-code-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <button type="submit" class="btn-submit" id="request-btn">Send Reset Code</button>
            </form>
            
            <!-- Step 2: Reset Password (hidden initially) -->
            <form id="reset-password-form" class="hidden">
                <div class="form-group">
                    <label for="code">Reset Code (from email)</label>
                    <input type="text" id="code" name="code" maxlength="6" placeholder="Enter 6-digit code" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                        <span class="password-toggle" onclick="togglePassword('new_password', this)">👁️</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                        <span class="password-toggle" onclick="togglePassword('confirm_password', this)">👁️</span>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit" id="reset-btn">Reset Password</button>
                
                <div class="login-link" style="margin-top: 15px;">
                    <a href="#" id="resend-code-link">Resend code</a>
                </div>
            </form>
            
            <div class="login-link">
                Remember your password? <a href="<?php echo home_url('/staydesk-login'); ?>">Login here</a>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId, icon) {
            var field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = '🙈';
            } else {
                field.type = 'password';
                icon.textContent = '👁️';
            }
        }
        
        (function() {
            // Wait for jQuery to be available
            var checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined') {
                    clearInterval(checkJQuery);
                    initForgotPasswordForm();
                }
            }, 100);
            
            function initForgotPasswordForm() {
                jQuery(document).ready(function($) {
                    console.log('StayDesk Forgot Password Form Initialized');
                    
                    var userEmail = '';
                    
                    // Request code form
                    $('#request-code-form').on('submit', function(e) {
                        e.preventDefault();
                        
                        var $btn = $('#request-btn');
                        var $alert = $('#forgot-alert');
                        userEmail = $('#email').val();
                        
                        $btn.prop('disabled', true).text('Sending...');
                        $alert.hide();
                        
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'staydesk_forgot_password',
                                nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                                email: userEmail
                            },
                            success: function(response) {
                                if (response.success) {
                                    $alert.removeClass('alert-error').addClass('alert-success')
                                          .text(response.data.message).show();
                                    
                                    // Show reset form, hide request form
                                    $('#request-code-form').hide();
                                    $('#reset-password-form').removeClass('hidden');
                                } else {
                                    $alert.removeClass('alert-success').addClass('alert-error')
                                          .text(response.data.message).show();
                                    $btn.prop('disabled', false).text('Send Reset Code');
                                }
                            },
                            error: function() {
                                $alert.removeClass('alert-success').addClass('alert-error')
                                      .text('An error occurred. Please try again.').show();
                                $btn.prop('disabled', false).text('Send Reset Code');
                            }
                        });
                    });
                    
                    // Reset password form
                    $('#reset-password-form').on('submit', function(e) {
                        e.preventDefault();
                        
                        var $btn = $('#reset-btn');
                        var $alert = $('#forgot-alert');
                        var newPassword = $('#new_password').val();
                        var confirmPassword = $('#confirm_password').val();
                        
                        if (newPassword !== confirmPassword) {
                            $alert.removeClass('alert-success').addClass('alert-error')
                                  .text('Passwords do not match.').show();
                            return;
                        }
                        
                        $btn.prop('disabled', true).text('Resetting...');
                        $alert.hide();
                        
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'staydesk_reset_password',
                                nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                                email: userEmail,
                                code: $('#code').val(),
                                new_password: newPassword
                            },
                            success: function(response) {
                                if (response.success) {
                                    $alert.removeClass('alert-error').addClass('alert-success')
                                          .text(response.data.message).show();
                                    
                                    setTimeout(function() {
                                        window.location.href = response.data.redirect;
                                    }, 1500);
                                } else {
                                    $alert.removeClass('alert-success').addClass('alert-error')
                                          .text(response.data.message).show();
                                    $btn.prop('disabled', false).text('Reset Password');
                                }
                            },
                            error: function() {
                                $alert.removeClass('alert-success').addClass('alert-error')
                                      .text('An error occurred. Please try again.').show();
                                $btn.prop('disabled', false).text('Reset Password');
                            }
                        });
                    });
                    
                    // Resend code
                    $('#resend-code-link').on('click', function(e) {
                        e.preventDefault();
                        var $alert = $('#forgot-alert');
                        
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'staydesk_forgot_password',
                                nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                                email: userEmail
                            },
                            success: function(response) {
                                if (response.success) {
                                    $alert.removeClass('alert-error').addClass('alert-success')
                                          .text('A new reset code has been sent to your email.').show();
                                }
                            }
                        });
                    });
                });
            }
        })();
    </script>
