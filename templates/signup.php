<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .staydesk-signup {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
        }
        
        .signup-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .signup-header h1 {
            color: #0066CC;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .signup-header p {
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
        
        .btn-signup {
            width: 100%;
            padding: 15px;
            background: #D4AF37;
            color: #333;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-signup:hover {
            background: #F0C650;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }
        
        .btn-signup:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }
        
        .login-link a {
            color: #0066CC;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
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
            .signup-container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="staydesk-signup">
        <div class="signup-container">
            <div class="signup-header">
                <h1>Join StayDesk</h1>
                <p>Create your hotel account today</p>
            </div>
            
            <div id="signup-alert" class="alert"></div>
            
            <form id="staydesk-signup-form">
                <div class="form-group">
                    <label for="hotel_name">Hotel Name</label>
                    <input type="text" id="hotel_name" name="hotel_name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="08012345678">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>
                
                <button type="submit" class="btn-signup" id="signup-btn">Create Account</button>
                
                <div class="login-link">
                    Already have an account? <a href="<?php echo home_url('/staydesk-login'); ?>">Login here</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
            $('#staydesk-signup-form').on('submit', function(e) {
                e.preventDefault();
                
                var $btn = $('#signup-btn');
                var $alert = $('#signup-alert');
                var password = $('#password').val();
                var confirmPassword = $('#confirm_password').val();
                
                // Validate passwords match
                if (password !== confirmPassword) {
                    $alert.removeClass('alert-success').addClass('alert-error')
                          .text('Passwords do not match.').show();
                    return;
                }
                
                $btn.prop('disabled', true).text('Creating account...');
                $alert.hide();
                
                $.ajax({
                    url: staydesk_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'staydesk_signup',
                        nonce: staydesk_ajax.nonce,
                        hotel_name: $('#hotel_name').val(),
                        email: $('#email').val(),
                        phone: $('#phone').val(),
                        password: password
                    },
                    success: function(response) {
                        if (response.success) {
                            $alert.removeClass('alert-error').addClass('alert-success')
                                  .text(response.data.message).show();
                            
                            setTimeout(function() {
                                window.location.href = response.data.redirect;
                            }, 2000);
                        } else {
                            $alert.removeClass('alert-success').addClass('alert-error')
                                  .text(response.data.message).show();
                            $btn.prop('disabled', false).text('Create Account');
                        }
                    },
                    error: function() {
                        $alert.removeClass('alert-success').addClass('alert-error')
                              .text('An error occurred. Please try again.').show();
                        $btn.prop('disabled', false).text('Create Account');
                    }
                });
            });
        });
    </script>
</body>
</html>
