<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .staydesk-pricing {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            padding: 80px 20px;
            min-height: 100vh;
        }
        
        .pricing-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .pricing-header h1 {
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
            font-size: 1.8rem;
            background: linear-gradient(90deg, #FFD700 0%, #4FC3F7 25%, #FFD700 50%, #64B5F6 75%, #FFD700 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            animation: sparkle 3s linear infinite;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: sparkle 3s linear infinite;
            margin-bottom: 20px;
            font-weight: 800;
            letter-spacing: -1px;
            text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
        }
        
        .pricing-header p {
            font-size: 1.1rem;
            color: #B8B8B8;
        }
        
        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 40px;
            max-width: 1100px;
            margin: 0 auto;
        }
        
        .pricing-card {
            background: #1a1a1a;
            padding: 40px 35px;
            border-radius: 24px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.6);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .pricing-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            border-radius: 24px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        
        .pricing-card:hover::before {
            opacity: 0.3;
        }
        
        .pricing-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(212, 175, 55, 0.4);
            background: #2a2a2a;
        }
        
        .pricing-card.popular {
            border: 2px solid #D4AF37;
            box-shadow: 0 12px 50px rgba(212, 175, 55, 0.5);
        }
        
        .popular-badge {
            position: absolute;
            top: 25px;
            right: -40px;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            padding: 8px 50px;
            transform: rotate(45deg);
            font-weight: 800;
            font-size: 0.85rem;
            letter-spacing: 1px;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.6);
        }
        
        .plan-name {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: #F0F0F0;
            letter-spacing: -0.5px;
        }
        
        .plan-price {
            font-size: 2.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }
        
        .plan-price small {
            font-size: 1.1rem;
            color: #B8B8B8;
            font-weight: 600;
        }
        
        .discount-info {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(255, 215, 0, 0.15) 100%);
            color: #FFD700;
            padding: 14px 20px;
            border-radius: 12px;
            margin: 25px 0;
            font-size: 1rem;
            font-weight: 600;
            border: 1px solid rgba(212, 175, 55, 0.3);
            text-align: center;
        }
        
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 35px 0;
        }
        
        .plan-features li {
            padding: 14px 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            color: #E8E8E8;
            font-size: 1rem;
        }
        
        .plan-features li:before {
            content: '✓';
            color: #D4AF37;
            font-weight: 900;
            margin-right: 12px;
            font-size: 1.2rem;
        }
        
        .btn-subscribe {
            width: 100%;
            padding: 18px;
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
            position: relative;
            overflow: hidden;
        }
        
        .btn-subscribe::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-subscribe:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .btn-subscribe:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.6);
        }
        
        @media (max-width: 768px) {
            .pricing-header h1 {
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
                font-size: 1.8rem;
            }
            
            .pricing-cards {
                grid-template-columns: 1fr;
            }
            
            .pricing-card {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="staydesk-pricing">
        <div class="pricing-header">
            <h1>Choose Your Plan</h1>
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
            <p>Affordable pricing for hotels of all sizes</p>
        </div>
        
        <div class="pricing-cards">
            <div class="pricing-card">
                <div class="plan-name">Monthly Plan</div>
                <div class="plan-price">
                    ₦49,900
                    <small>/month</small>
                </div>
                
                <ul class="plan-features">
                    <li>Unlimited bookings</li>
                    <li>Room management</li>
                    <li>Payment integration</li>
                    <li>AI Chatbot (bilingual)</li>
                    <li>Email notifications</li>
                    <li>WhatsApp integration</li>
                    <li>Analytics dashboard</li>
                    <li>24/7 support</li>
                </ul>
                
                <button class="btn-subscribe" data-plan="monthly">Subscribe Now</button>
            </div>
            
            <div class="pricing-card popular">
                <div class="popular-badge">BEST VALUE</div>
                <div class="plan-name">Yearly Plan</div>
                <div class="plan-price">
                    ₦598,800
                    <small>/year</small>
                </div>
                
                <div class="discount-info">
                    💎 First 10 hotels get 10% OFF!<br>
                    Save ₦59,880 annually
                </div>
                
                <ul class="plan-features">
                    <li>Everything in Monthly Plan</li>
                    <li>Priority support</li>
                    <li>Custom branding</li>
                    <li>Advanced analytics</li>
                    <li>API access</li>
                    <li>Dedicated account manager</li>
                    <li>Free updates</li>
                    <li>Training sessions</li>
                </ul>
                
                <button class="btn-subscribe popular" data-plan="yearly">Subscribe Now</button>
            </div>
        </div>
    </div>
    
    <script>
        <?php wp_enqueue_script('jquery'); ?>
        jQuery(document).ready(function($) {
            $('.btn-subscribe').on('click', function() {
                var plan = $(this).data('plan');
                var $btn = $(this);
                
                // Check if user is logged in
                <?php if (!is_user_logged_in()): ?>
                    alert('Please login to subscribe.');
                    window.location.href = '<?php echo home_url('/staydesk-login'); ?>';
                    return;
                <?php endif; ?>
                
                $btn.prop('disabled', true).text('Processing...');
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'staydesk_subscribe',
                        nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                        plan_type: plan
                    },
                    success: function(response) {
                        console.log('Subscribe response:', response);
                        if (response.success) {
                            // Redirect to Paystack payment page
                            if (response.data && response.data.authorization_url) {
                                window.location.href = response.data.authorization_url;
                            } else {
                                alert('Payment initialization failed.');
                                $btn.prop('disabled', false).text('Subscribe Now');
                            }
                        } else {
                            alert(response.data.message || 'Failed to initialize payment.');
                            $btn.prop('disabled', false).text('Subscribe Now');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Subscribe error:', error);
                        alert('Failed to initialize payment. Please try again.');
                        $btn.prop('disabled', false).text('Subscribe Now');
                    }
                });
            });
        });
    </script>
</body>
</html>
