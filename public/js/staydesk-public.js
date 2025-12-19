/**
 * StayDesk Public JavaScript
 * Smooth interactions and animations
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 800, 'swing');
            }
        });

        // Toast notification system
        window.staydeskToast = function(message, type) {
            type = type || 'success';
            
            var toast = $('<div class="staydesk-toast ' + type + '">' + message + '</div>');
            $('body').append(toast);
            
            setTimeout(function() {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        };

        // Modal system
        $('.staydesk-modal-trigger').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $(target).fadeIn(300);
        });

        $('.staydesk-modal-close, .staydesk-modal').on('click', function(e) {
            if (e.target === this) {
                $(this).closest('.staydesk-modal').fadeOut(300);
            }
        });

        // Number counter animation
        $('.stat-value').each(function() {
            var $this = $(this);
            var countTo = $this.text().replace(/,/g, '');
            
            if (!isNaN(countTo)) {
                $({ countNum: 0 }).animate({
                    countNum: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum).toLocaleString());
                    },
                    complete: function() {
                        $this.text(this.countNum.toLocaleString());
                    }
                });
            }
        });

        // Staggered card reveal on scroll
        function revealOnScroll() {
            $('.feature-card, .stat-card, .section-card').each(function(index) {
                var $card = $(this);
                var cardTop = $card.offset().top;
                var windowBottom = $(window).scrollTop() + $(window).height();
                
                if (cardTop < windowBottom - 100) {
                    setTimeout(function() {
                        $card.css({
                            'opacity': '1',
                            'transform': 'translateY(0)'
                        });
                    }, index * 100);
                }
            });
        }

        // Initialize cards for reveal
        $('.feature-card, .stat-card, .section-card').css({
            'opacity': '0',
            'transform': 'translateY(30px)',
            'transition': 'all 0.5s ease-out'
        });

        // Trigger reveal on scroll
        $(window).on('scroll', revealOnScroll);
        revealOnScroll(); // Initial check

        // Form validation
        $('form[data-validate="true"]').on('submit', function(e) {
            var isValid = true;
            
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).css('border-color', '#DC3545');
                } else {
                    $(this).css('border-color', '#e0e0e0');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                staydeskToast('Please fill in all required fields', 'error');
            }
        });

        // Loading state for buttons
        $(document).on('click', '[data-loading]', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            
            $btn.data('original-text', originalText);
            $btn.prop('disabled', true).html('<span class="staydesk-spinner"></span>');
        });

        // Parallax effect for hero sections
        $(window).on('scroll', function() {
            var scrolled = $(window).scrollTop();
            $('.hero-section').css('transform', 'translateY(' + (scrolled * 0.5) + 'px)');
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var image = entry.target;
                        image.src = image.dataset.src;
                        image.classList.remove('lazy');
                        imageObserver.unobserve(image);
                    }
                });
            });

            $('.lazy').each(function() {
                imageObserver.observe(this);
            });
        }

        // Chatbot initialization
        window.staydeskChatbot = {
            init: function(hotelId) {
                this.hotelId = hotelId;
                this.sessionId = 'session_' + Date.now();
            },
            
            sendMessage: function(message, language) {
                var self = this;
                language = language || 'en';
                
                return $.ajax({
                    url: staydesk_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'staydesk_chatbot_message',
                        hotel_id: self.hotelId,
                        session_id: self.sessionId,
                        message: message,
                        language: language
                    }
                });
            }
        };

        // Animate elements on hover
        $('.staydesk-card, .feature-card').on('mouseenter', function() {
            $(this).css('transition', 'all 0.3s ease');
        });

        // Add ripple effect to buttons
        $('.staydesk-btn, .btn-primary, .btn-secondary').on('click', function(e) {
            var $button = $(this);
            var $ripple = $('<span class="ripple"></span>');
            
            $button.append($ripple);
            
            var x = e.pageX - $button.offset().left;
            var y = e.pageY - $button.offset().top;
            
            $ripple.css({
                left: x,
                top: y
            }).addClass('ripple-effect');
            
            setTimeout(function() {
                $ripple.remove();
            }, 600);
        });

        // Auto-hide alerts
        $('.alert').delay(5000).fadeOut(300);

        // Copy to clipboard functionality
        $('[data-copy]').on('click', function() {
            var text = $(this).data('copy');
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            
            staydeskToast('Copied to clipboard!', 'success');
        });

        // Print functionality
        $('[data-print]').on('click', function() {
            window.print();
        });

        // Dark mode toggle (if implemented)
        $('#dark-mode-toggle').on('click', function() {
            $('body').toggleClass('dark-mode');
            localStorage.setItem('darkMode', $('body').hasClass('dark-mode'));
        });

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            $('body').addClass('dark-mode');
        }
    });

})(jQuery);
