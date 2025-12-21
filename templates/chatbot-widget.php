<div class="staydesk-whatsapp-widget" id="staydesk-whatsapp-widget">
    <button class="whatsapp-button" id="whatsapp-toggle">
        <svg viewBox="0 0 32 32" width="32" height="32">
            <path fill="currentColor" d="M16 0c-8.837 0-16 7.163-16 16 0 2.825 0.737 5.607 2.137 8.048l-2.137 7.952 8.135-2.135c2.369 1.313 5.061 2.010 7.865 2.010 8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 29.333c-2.547 0-5.033-0.727-7.193-2.101l-0.509-0.311-5.285 1.387 1.408-5.245-0.341-0.528c-1.515-2.344-2.315-5.053-2.315-7.869 0-7.364 5.991-13.355 13.355-13.355s13.355 5.991 13.355 13.355-5.991 13.355-13.355 13.355zM23.197 19.484c-0.389-0.195-2.299-1.137-2.656-1.267s-0.616-0.195-0.875 0.195c-0.259 0.389-1.005 1.267-1.232 1.527s-0.453 0.292-0.843 0.097c-0.389-0.195-1.643-0.605-3.129-1.932-1.157-1.032-1.939-2.308-2.165-2.697s-0.024-0.6 0.171-0.795c0.176-0.176 0.389-0.453 0.584-0.681s0.259-0.389 0.389-0.648c0.129-0.259 0.065-0.487-0.032-0.681s-0.875-2.109-1.199-2.889c-0.316-0.759-0.637-0.656-0.875-0.669-0.227-0.013-0.487-0.016-0.747-0.016s-0.681 0.097-1.037 0.487c-0.357 0.389-1.364 1.333-1.364 3.249s1.397 3.768 1.591 4.027c0.195 0.259 2.749 4.199 6.659 5.884 0.931 0.403 1.657 0.643 2.223 0.823 0.935 0.297 1.785 0.255 2.457 0.155 0.749-0.112 2.299-0.939 2.624-1.845s0.325-1.683 0.227-1.845c-0.097-0.163-0.357-0.259-0.747-0.453z"/>
        </svg>
    </button>
    
    <div class="whatsapp-chat-window" id="whatsapp-chat-window" style="display: none;">
        <div class="chat-header">
            <div class="header-content">
                <strong>StayDesk Support</strong>
                <small>Typically replies instantly</small>
            </div>
            <button class="close-chat" id="close-chat">×</button>
        </div>
        
        <div class="chat-body" id="chat-body">
            <div class="chat-message bot-message">
                <p>Hello! 👋 Need help? Click below to chat with us on WhatsApp!</p>
            </div>
        </div>
        
        <div class="chat-footer">
            <a href="https://wa.me/2347120018023" target="_blank" class="whatsapp-link">
                <svg viewBox="0 0 32 32" width="20" height="20">
                    <path fill="currentColor" d="M16 0c-8.837 0-16 7.163-16 16 0 2.825 0.737 5.607 2.137 8.048l-2.137 7.952 8.135-2.135c2.369 1.313 5.061 2.010 7.865 2.010 8.837 0 16-7.163 16-16s-7.163-16-16-16z"/>
                </svg>
                Chat on WhatsApp
            </a>
        </div>
    </div>
</div>

<style>
    .staydesk-whatsapp-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .whatsapp-button {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #25D366;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .whatsapp-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(37, 211, 102, 0.5);
    }
    
    .whatsapp-chat-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 350px;
        max-width: 90vw;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: slideUp 0.3s ease-out;
    }
    
    .chat-header {
        background: #075E54;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .header-content strong {
        display: block;
        font-size: 1rem;
    }
    
    .header-content small {
        font-size: 0.85rem;
        opacity: 0.8;
    }
    
    .close-chat {
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        line-height: 1;
        padding: 0;
        width: 30px;
        height: 30px;
    }
    
    .chat-body {
        padding: 20px;
        background: #ECE5DD;
        min-height: 200px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .chat-message {
        margin-bottom: 15px;
        animation: fadeIn 0.3s ease-out;
    }
    
    .bot-message {
        background: white;
        padding: 12px 15px;
        border-radius: 10px;
        max-width: 80%;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .bot-message p {
        margin: 0;
        color: #333;
        line-height: 1.5;
    }
    
    .chat-footer {
        padding: 15px;
        background: white;
        border-top: 1px solid #e0e0e0;
    }
    
    .whatsapp-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 20px;
        background: #25D366;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .whatsapp-link:hover {
        background: #128C7E;
        transform: translateY(-2px);
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
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
        .staydesk-whatsapp-widget {
            bottom: 15px;
            right: 15px;
        }
        
        .whatsapp-button {
            width: 50px;
            height: 50px;
        }
        
        .whatsapp-chat-window {
            width: calc(100vw - 30px);
            right: -15px;
        }
    }
</style>

<script>
    (function() {
        var toggleBtn = document.getElementById('whatsapp-toggle');
        var chatWindow = document.getElementById('whatsapp-chat-window');
        var closeBtn = document.getElementById('close-chat');
        
        if (toggleBtn && chatWindow) {
            toggleBtn.addEventListener('click', function() {
                if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                    chatWindow.style.display = 'block';
                } else {
                    chatWindow.style.display = 'none';
                }
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                chatWindow.style.display = 'none';
            });
        }
    })();
</script>
