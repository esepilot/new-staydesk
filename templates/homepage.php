<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .staydesk-homepage {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #0a0a0a;
        }
        
        .hero-section {
            min-height: 90vh;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.5; }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            animation: fadeInUp 1.2s ease-out;
        }
        
        .hero-content h1 {
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
            font-size: 3.8rem;
            font-weight: 900;
            margin-bottom: 30px;
            line-height: 1.1;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 50%, #D4AF37 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            animation: sparkle 3s linear infinite;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: sparkle 3s linear infinite;
            letter-spacing: -2px;
            text-shadow: 0 2px 12px rgba(212, 175, 55, 0.3);
        }
        
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 50px;
            color: #F0F0F0;
            font-weight: 300;
            letter-spacing: 0.5px;
            line-height: 1.6;
        }
            opacity: 0.95;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary, .btn-secondary {
            padding: 15px 40px;
            font-size: 14px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 700;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before, .btn-secondary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn-primary:hover::before, .btn-secondary:hover::before {
            width: 400px;
            height: 400px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            box-shadow: 0 6px 30px rgba(212, 175, 55, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 50px rgba(212, 175, 55, 0.6);
        }
        
        .btn-secondary {
            background: transparent;
            color: #D4AF37;
            border: 2px solid #D4AF37;
        }
        
        .btn-secondary:hover {
            background: #D4AF37;
            color: #000;
            transform: translateY(-3px);
            box-shadow: 0 8px 35px rgba(212, 175, 55, 0.4);
        }
        
        .features-section {
            padding: 120px 20px;
            background: #0a0a0a;
            position: relative;
        }
        
        .features-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.5), transparent);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 80px;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            letter-spacing: -1px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 45px;
        }
        
        .feature-card {
            background: #1a1a1a;
            padding: 45px;
            border-radius: 20px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.5);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeIn 0.8s ease-out;
            border: 1px solid rgba(212, 175, 55, 0.2);
            position: relative;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            border-radius: 20px;
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: -1;
        }
        
        .feature-card:hover::before {
            opacity: 0.3;
        }
        
        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 60px rgba(212, 175, 55, 0.4);
            background: #2a2a2a;
        }
        
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 25px;
            filter: drop-shadow(0 0 20px rgba(212, 175, 55, 0.5));
        }
        
        .feature-card h3 {
            font-size: 1.6rem;
            margin-bottom: 18px;
            color: #D4AF37;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .feature-card p {
            color: #A0A0A0;
            line-height: 1.7;
            font-size: 1.05rem;
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
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
                font-size: 1.8rem;
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
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
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
