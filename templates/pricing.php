<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .staydesk-pricing {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            padding: 60px 20px;
            min-height: 100vh;
        }
        
        .pricing-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .pricing-header h1 {
            font-size: 3rem;
            color: #0066CC;
            margin-bottom: 15px;
        }
        
        .pricing-header p {
            font-size: 1.2rem;
            color: #666;
        }
        
        .pricing-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .pricing-card {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .pricing-card.popular {
            border: 3px solid #D4AF37;
        }
        
        .popular-badge {
            position: absolute;
            top: 20px;
            right: -35px;
            background: #D4AF37;
            color: #333;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-weight: 700;
            font-size: 0.85rem;
        }
        
        .plan-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }
        
        .plan-price {
            font-size: 3rem;
            font-weight: 700;
            color: #0066CC;
            margin-bottom: 10px;
        }
        
        .plan-price small {
            font-size: 1.2rem;
            color: #666;
        }
        
        .discount-info {
            background: #FFF3CD;
            color: #856404;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 0.95rem;
        }
        
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        
        .plan-features li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #666;
        }
        
        .plan-features li:before {
            content: '✓';
            color: #28A745;
            font-weight: 700;
            margin-right: 10px;
        }
        
        .btn-subscribe {
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
        
        .btn-subscribe:hover {
            background: #0052A3;
            transform: translateY(-2px);
        }
        
        .btn-subscribe.popular {
            background: #D4AF37;
            color: #333;
        }
        
        .btn-subscribe.popular:hover {
            background: #F0C650;
        }
        
        @media (max-width: 768px) {
            .pricing-header h1 {
                font-size: 2rem;
            }
            
            .pricing-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="staydesk-pricing">
        <div class="pricing-header">
            <h1>Choose Your Plan</h1>
            <p>Affordable pricing for hotels of all sizes</p>
        </div>
        
        <div class="pricing-cards">
            <div class="pricing-card">
                <div class="plan-name">Monthly Plan</div>
                <div class="plan-price">
                    ₦49,900
                    <small>/month</small>
                </div>
                
                <div class="discount-info">
                    🎉 First 10 hotels get 10% off!
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
                    💎 Save ₦0 compared to monthly!<br>
                    Plus: First 10 hotels get additional 10% off!
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
        jQuery(document).ready(function($) {
            $('.btn-subscribe').on('click', function() {
                var plan = $(this).data('plan');
                var $btn = $(this);
                
                $btn.prop('disabled', true).text('Processing...');
                
                $.ajax({
                    url: staydesk_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'staydesk_subscribe',
                        nonce: staydesk_ajax.nonce,
                        plan_type: plan
                    },
                    success: function(response) {
                        if (response.success) {
                            // Redirect to Paystack payment page
                            window.location.href = response.data.authorization_url;
                        } else {
                            alert(response.data.message);
                            $btn.prop('disabled', false).text('Subscribe Now');
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                        $btn.prop('disabled', false).text('Subscribe Now');
                    }
                });
            });
        });
    </script>
</body>
</html>
