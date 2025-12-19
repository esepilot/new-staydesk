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
        
        .staydesk-signup {
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
        
        .staydesk-signup::before {
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
        
        .signup-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 55px;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.8);
            max-width: 520px;
            width: 100%;
            animation: slideIn 0.6s ease-out;
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: relative;
            z-index: 1;
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 45px;
        }
        
        .signup-header h1 {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.3rem;
            margin-bottom: 12px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        
        .signup-header p {
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
        
        .btn-signup {
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
        
        .btn-signup:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.6);
        }
        
        .btn-signup:disabled {
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
    
    <?php wp_enqueue_script('jquery'); ?>
    <script>
        (function() {
            // Wait for jQuery to be available
            var checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined') {
                    clearInterval(checkJQuery);
                    initSignupForm();
                }
            }, 100);
            
            function initSignupForm() {
                jQuery(document).ready(function($) {
                    console.log('StayDesk Signup Form Initialized');
                    console.log('AJAX URL:', '<?php echo admin_url('admin-ajax.php'); ?>');
                    
                    $('#staydesk-signup-form').on('submit', function(e) {
                        e.preventDefault();
                        console.log('Form submitted');
                        
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
                        
                        var ajaxData = {
                            action: 'staydesk_signup',
                            nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                            hotel_name: $('#hotel_name').val(),
                            email: $('#email').val(),
                            phone: $('#phone').val(),
                            password: password
                        };
                        
                        console.log('Sending AJAX request with data:', ajaxData);
                        
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: ajaxData,
                            success: function(response) {
                                console.log('AJAX Success:', response);
                                if (response.success) {
                                    $alert.removeClass('alert-error').addClass('alert-success')
                                          .text(response.data.message).show();
                                    
                                    setTimeout(function() {
                                        window.location.href = response.data.redirect;
                                    }, 2000);
                                } else {
                                    $alert.removeClass('alert-success').addClass('alert-error')
                                          .text(response.data.message || 'An error occurred. Please try again.').show();
                                    $btn.prop('disabled', false).text('Create Account');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', {
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
                                        // Try to extract error from HTML response
                                        var match = xhr.responseText.match(/<body[^>]*>(.*?)<\/body>/is);
                                        if (match) {
                                            errorMessage = 'Server error. Please check console for details.';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing response:', e);
                                }
                                
                                $alert.removeClass('alert-success').addClass('alert-error')
                                      .text(errorMessage).show();
                                $btn.prop('disabled', false).text('Create Account');
                            }
                        });
                    });
                });
            }
        })();
    </script>
</body>
</html>
