<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
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
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.5; }
        }
        
        .login-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 55px;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.8);
            max-width: 480px;
            width: 100%;
            animation: slideIn 0.6s ease-out;
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 45px;
        }
        
        .login-header h1 {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.3rem;
            margin-bottom: 12px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        
        .login-header p {
            color: #A0A0A0;
            font-size: 1.05rem;
        }
        
        .form-group {
            margin-bottom: 28px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #E8E8E8;
            font-weight: 600;
            letter-spacing: 0.3px;
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
            color: #E8E8E8;
        }
        
        .form-group input::placeholder {
            color: #666;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.2);
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
        
        .btn-login {
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
        
        .btn-login:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.6);
        }
        
        .btn-login:disabled {
            background: linear-gradient(135deg, #666 0%, #888 100%);
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
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="staydesk-login">
        <div class="login-container">
            <div class="login-header">
                <h1>Welcome Back!</h1>
                <p>Login to your StayDesk account</p>
            </div>
            
            <?php if (isset($_GET['confirmed']) && $_GET['confirmed'] == '1'): ?>
                <div class="alert alert-success" style="display: block;">
                    Email confirmed successfully! You can now login.
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
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn-login" id="login-btn">Login</button>
                
                <div class="signup-link">
                    Don't have an account? <a href="<?php echo home_url('/staydesk-signup'); ?>">Sign up here</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php wp_enqueue_script('jquery'); ?>
    <script>
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
                                    }, 1500);
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
</body>
</html>
