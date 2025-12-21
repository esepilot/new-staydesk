<style>
        /* Scroll Progress Bar */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #FFD700, #4FC3F7, #64B5F6, #FFD700);
            background-size: 200% 100%;
            z-index: 99999;
            transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            animation: gradient-slide 3s linear infinite;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: #0a0a0a;
        }
        
        .staydesk-login {
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
        
        .staydesk-login::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        
        .login-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
            max-width: 480px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
            border: 1px solid rgba(212, 175, 55, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .login-header h1 {
            background: linear-gradient(120deg, #FFD700 0%, #4FC3F7 25%, #64B5F6 50%, #FFD700 75%, #4FC3F7 100%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-shift 4s ease-in-out infinite;
            font-size: 1.6rem;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        
        .login-header p {
            color: #D1D1D1;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #FFFFFF;
            font-weight: 600;
            letter-spacing: 0.2px;
            font-size: 13px;
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
            padding: 14px 16px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            font-size: 13px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
            background: rgba(26, 26, 26, 0.8);
            color: #FFFFFF;
        }
        
        .form-group input::placeholder {
            color: #999;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
            background: rgba(42, 42, 42, 0.9);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 28px;
        }
        
        .remember-me input {
            width: auto;
            margin-right: 12px;
        }
        
        .remember-me label {
            color: #A0A0A0;
            margin-bottom: 0;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 8px;
            margin-bottom: 20px;
        }
        
        .forgot-password a {
            color: #D4AF37;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: #FFD700;
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.3px;
            box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.5);
        }
        
        .btn-login:disabled {
            background: linear-gradient(135deg, #555 0%, #777 100%);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 28px;
            color: #A0A0A0;
            font-size: 1rem;
        }
        
        .signup-link a {
            color: #D4AF37;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .signup-link a:hover {
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
        
        /* ============================================
           ANIMATIONS - All keyframes consolidated
           ============================================ */
        
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        @keyframes gradient-slide {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.5; }
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
        
        @media (max-width: 768px) {
            .login-container {
                padding: 20px;
            }
        }
    </style>
    
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress"></div>
    
    <script>
        // Scroll Progress Bar
        window.addEventListener('scroll', function() {
            var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            var scrolled = (winScroll / height) * 100;
            document.querySelector('.scroll-progress').style.width = scrolled + '%';
        });
    </script>
    
<div class="staydesk-login">
        <div class="login-container">
            <div class="login-header">
                <h1>Welcome Back!</h1>
                <p>Login to your StayDesk account</p>
            </div>
            
            <?php if (isset($_GET['verified']) && $_GET['verified'] == '1'): ?>
                <div class="alert alert-success" style="display: block;">
                    Email verified successfully! You can now login.
                </div>
            <?php endif; ?>
            
            <div id="login-alert" class="alert"></div>
            
            <form id="staydesk-login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <span class="password-toggle" onclick="togglePassword('password', this)">👁️</span>
                    </div>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <div class="forgot-password">
                    <a href="<?php echo home_url('/staydesk-forgot-password'); ?>">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn-login" id="login-btn">Login</button>
                
                <div class="signup-link">
                    Don't have an account? <a href="<?php echo home_url('/staydesk-signup'); ?>">Sign up here</a>
                </div>
            </form>
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
                    initLoginForm();
                }
            }, 100);
            
            function initLoginForm() {
                jQuery(document).ready(function($) {
                    console.log('StayDesk Login Form Initialized');
                    console.log('AJAX URL:', '<?php echo admin_url('admin-ajax.php'); ?>');
                    
                    $('#staydesk-login-form').on('submit', function(e) {
                        e.preventDefault();
                        console.log('Login form submitted');
                        
                        var $btn = $('#login-btn');
                        var $alert = $('#login-alert');
                        
                        $btn.prop('disabled', true).text('Logging in...');
                        $alert.hide();
                        
                        var ajaxData = {
                            action: 'staydesk_login',
                            nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                            email: $('#email').val(),
                            password: $('#password').val(),
                            remember: $('#remember').is(':checked')
                        };
                        
                        console.log('Sending login AJAX request');
                        
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: ajaxData,
                            success: function(response) {
                                console.log('Login AJAX Success:', response);
                                if (response.success) {
                                    $alert.removeClass('alert-error').addClass('alert-success')
                                          .text(response.data.message).show();
                                    
                                    setTimeout(function() {
                                        window.location.href = response.data.redirect;
                                    }, 600);
                                } else {
                                    $alert.removeClass('alert-success').addClass('alert-error')
                                          .text(response.data.message || 'An error occurred. Please try again.').show();
                                    $btn.prop('disabled', false).text('Login');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Login AJAX Error:', {
                                    status: status,
                                    error: error,
                                    responseText: xhr.responseText,
                                    response: xhr.responseJSON
                                });
                                
                                var errorMessage = 'An error occurred. Please try again.';
                                
                                // Try to parse error response
                                try {
                                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                                        errorMessage = xhr.responseJSON.data.message;
                                    } else if (xhr.responseText) {
                                        errorMessage = 'Server error. Please check console for details.';
                                    }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                }
                                
                                $alert.removeClass('alert-success').addClass('alert-error')
                                      .text(errorMessage).show();
                                $btn.prop('disabled', false).text('Login');
                            }
                        });
                    });
                });
            }
        })();
    </script>
