<style>
        body {
            margin: 0;
            padding: 0;
            background: #0a0a0a;
        }
        
        .staydesk-verify {
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
        
        .staydesk-verify::before {
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
        
        .verify-container {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 55px;
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.8);
            max-width: 500px;
            width: 100%;
            animation: slideIn 0.6s ease-out;
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: relative;
            z-index: 1;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .verify-header h1 {
            margin: 0 0 15px 0;
            font-size: 36px;
            font-weight: 700;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
        }
        
        .verify-header p {
            color: #A0A0A0;
            font-size: 15px;
            margin: 0;
            line-height: 1.6;
        }
        
        .email-display {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin: 25px 0;
            text-align: center;
        }
        
        .email-display strong {
            color: #FFD700;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 28px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #E8E8E8;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.3px;
        }
        
        .form-group input {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            color: #E8E8E8;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 12px;
            text-align: center;
            font-family: 'Courier New', monospace;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #D4AF37;
            background: rgba(212, 175, 55, 0.05);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1), 0 0 20px rgba(212, 175, 55, 0.2);
        }
        
        .form-group input::placeholder {
            color: #4a4a4a;
            font-size: 20px;
            letter-spacing: 8px;
        }
        
        .verify-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            border: none;
            border-radius: 12px;
            color: #0a0a0a;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }
        
        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.4);
        }
        
        .verify-btn:active {
            transform: translateY(0);
        }
        
        .verify-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .resend-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .resend-section p {
            color: #A0A0A0;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .resend-btn {
            background: transparent;
            border: 2px solid rgba(212, 175, 55, 0.3);
            color: #D4AF37;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .resend-btn:hover {
            border-color: #D4AF37;
            background: rgba(212, 175, 55, 0.1);
            transform: translateY(-2px);
        }
        
        .resend-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            display: none;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .message.success {
            background: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        
        .message.error {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }
        
        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        
        .back-link a {
            color: #A0A0A0;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .back-link a:hover {
            color: #D4AF37;
        }
        
        @media (max-width: 600px) {
            .verify-container {
                padding: 35px 25px;
            }
            
            .verify-header h1 {
                font-size: 28px;
            }
            
            .form-group input {
                font-size: 24px;
                letter-spacing: 8px;
            }
        }
    </style>
    
<div class="staydesk-verify">
        <div class="verify-container">
            <div class="verify-header">
                <h1>✉️ Verify Your Email</h1>
                <p>We've sent a 6-digit verification code to your email address. Please enter it below to complete your registration.</p>
            </div>

            <?php
            $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
            if ($email) {
                echo '<div class="email-display">Code sent to: <strong>' . esc_html($email) . '</strong></div>';
            }
            ?>

            <div id="verifyMessage" class="message"></div>

            <form id="verifyForm">
                <div class="form-group">
                    <label for="code">Verification Code</label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        maxlength="6"
                        pattern="[0-9]{6}"
                        placeholder="000000"
                        required
                        autocomplete="off"
                    >
                </div>

                <input type="hidden" id="email" name="email" value="<?php echo esc_attr($email); ?>">

                <button type="submit" class="verify-btn" id="verifyBtn">
                    Verify Email
                </button>
            </form>

            <div class="resend-section">
                <p>Didn't receive the code?</p>
                <button type="button" class="resend-btn" id="resendBtn">
                    Resend Code
                </button>
            </div>

            <div class="back-link">
                <a href="<?php echo home_url('/staydesk-login'); ?>">← Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        (function() {
            // Wait for jQuery to be available
            var checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined') {
                    clearInterval(checkJQuery);
                    initializeVerifyForm();
                }
            }, 100);

            function initializeVerifyForm() {
                console.log('StayDesk Verify Form Initialized');

                var $ = jQuery;
                var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
                var nonce = '<?php echo wp_create_nonce('staydesk_nonce'); ?>';

                // Auto-focus on code input
                $('#code').focus();

                // Only allow numbers in code input
                $('#code').on('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                // Handle verification form submission
                $('#verifyForm').on('submit', function(e) {
                    e.preventDefault();

                    var code = $('#code').val().trim();
                    var email = $('#email').val();

                    if (code.length !== 6) {
                        showMessage('Please enter a 6-digit code.', 'error');
                        return;
                    }

                    if (!email) {
                        showMessage('Email is missing. Please go back to signup.', 'error');
                        return;
                    }

                    var $btn = $('#verifyBtn');
                    $btn.prop('disabled', true).text('Verifying...');

                    console.log('Submitting verification:', { email: email, code: code });

                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'staydesk_verify_email',
                            nonce: nonce,
                            email: email,
                            code: code
                        },
                        success: function(response) {
                            console.log('Verification response:', response);

                            if (response.success) {
                                showMessage(response.data.message, 'success');
                                setTimeout(function() {
                                    window.location.href = response.data.redirect;
                                }, 800);
                            } else {
                                showMessage(response.data.message, 'error');
                                $btn.prop('disabled', false).text('Verify Email');
                                $('#code').val('').focus();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Verification error:', error, xhr);
                            showMessage('An error occurred. Please try again.', 'error');
                            $btn.prop('disabled', false).text('Verify Email');
                        }
                    });
                });

                // Handle resend code
                $('#resendBtn').on('click', function() {
                    var email = $('#email').val();

                    if (!email) {
                        showMessage('Email is missing. Please go back to signup.', 'error');
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).text('Sending...');

                    console.log('Resending code to:', email);

                    $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'staydesk_resend_code',
                            nonce: nonce,
                            email: email
                        },
                        success: function(response) {
                            console.log('Resend response:', response);

                            if (response.success) {
                                showMessage(response.data.message, 'success');
                                // Re-enable after 60 seconds
                                setTimeout(function() {
                                    $btn.prop('disabled', false).text('Resend Code');
                                }, 60000);
                            } else {
                                showMessage(response.data.message, 'error');
                                $btn.prop('disabled', false).text('Resend Code');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Resend error:', error, xhr);
                            showMessage('An error occurred. Please try again.', 'error');
                            $btn.prop('disabled', false).text('Resend Code');
                        }
                    });
                });

                function showMessage(message, type) {
                    var $msg = $('#verifyMessage');
                    $msg.removeClass('success error').addClass(type).text(message).show();

                    if (type === 'success') {
                        setTimeout(function() {
                            $msg.hide();
                        }, 5000);
                    }
                }
            }
        })();
    </script>
