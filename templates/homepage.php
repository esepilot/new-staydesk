<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .staydesk-homepage {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .hero-section {
            min-height: 80vh;
            background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><path fill="rgba(255,255,255,0.05)" d="M0,300 Q300,200 600,300 T1200,300 L1200,600 L0,600 Z"/></svg>') no-repeat bottom;
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-content h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary, .btn-secondary {
            padding: 16px 40px;
            font-size: 1.1rem;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .btn-primary {
            background: #D4AF37;
            color: #333;
        }
        
        .btn-primary:hover {
            background: #F0C650;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #0066CC;
            transform: translateY(-2px);
        }
        
        .features-section {
            padding: 100px 20px;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 60px;
            color: #333;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }
        
        .feature-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            animation: fadeIn 0.6s ease-out;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #0066CC;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-content p {
                font-size: 1.2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="staydesk-homepage">
        <section class="hero-section">
            <div class="hero-content">
                <h1>Welcome to StayDesk</h1>
                <p>The Ultimate Hotel Management Platform for Nigerian Hotels</p>
                <div class="cta-buttons">
                    <a href="<?php echo home_url('/staydesk-signup'); ?>" class="btn-primary">Get Started</a>
                    <a href="<?php echo home_url('/staydesk-pricing'); ?>" class="btn-secondary">View Pricing</a>
                </div>
            </div>
        </section>
        
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Powerful Features for Your Hotel</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🏨</div>
                        <h3>Booking Management</h3>
                        <p>Streamline your booking process with our intuitive dashboard. Manage reservations, check-ins, and check-outs effortlessly.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">💳</div>
                        <h3>Payment Integration</h3>
                        <p>Seamless Paystack integration for secure Nigerian Naira payments. Track all transactions in one place.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">🤖</div>
                        <h3>AI Chatbot</h3>
                        <p>Bilingual chatbot (English & Pidgin) that handles guest enquiries, bookings, and support 24/7.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h3>Analytics Dashboard</h3>
                        <p>Real-time insights into your hotel performance, revenue, and booking trends.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">📧</div>
                        <h3>Automated Notifications</h3>
                        <p>Email and WhatsApp notifications keep your guests informed every step of the way.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h3>Secure & Reliable</h3>
                        <p>Built with WordPress best practices, ensuring your data is safe and secure.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
