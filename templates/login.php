<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .staydesk-login {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header h1 {
            color: #0066CC;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0066CC;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember-me input {
            width: auto;
            margin-right: 10px;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: #0066CC;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: #0052A3;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 102, 204, 0.3);
        }
        
        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .signup-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }
        
        .signup-link a {
            color: #0066CC;
            text-decoration: none;
            font-weight: 600;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
    
    <script>
        jQuery(document).ready(function($) {
            $('#staydesk-login-form').on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $('#login-btn');
                var $alert = $('#login-alert');
                
                $btn.prop('disabled', true).text('Logging in...');
                $alert.hide();
                
                $.ajax({
                    url: staydesk_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'staydesk_login',
                        nonce: staydesk_ajax.nonce,
                        email: $('#email').val(),
                        password: $('#password').val(),
                        remember: $('#remember').is(':checked')
                    },
                    success: function(response) {
                        if (response.success) {
                            $alert.removeClass('alert-error').addClass('alert-success')
                                  .text(response.data.message).show();
                            
                            setTimeout(function() {
                                window.location.href = response.data.redirect;
                            }, 1000);
                        } else {
                            $alert.removeClass('alert-success').addClass('alert-error')
                                  .text(response.data.message).show();
                            $btn.prop('disabled', false).text('Login');
                        }
                    },
                    error: function() {
                        $alert.removeClass('alert-success').addClass('alert-error')
                              .text('An error occurred. Please try again.').show();
                        $btn.prop('disabled', false).text('Login');
                    }
                });
            });
        });
    </script>
</body>
</html>
