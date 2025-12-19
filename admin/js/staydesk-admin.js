/**
 * StayDesk Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Tab switching
        $('.staydesk-admin-tab').on('click', function() {
            var target = $(this).data('target');
            
            $('.staydesk-admin-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.staydesk-tab-content').hide();
            $(target).show();
        });

        // DataTables initialization (if available)
        if ($.fn.DataTable) {
            $('.staydesk-table').DataTable({
                responsive: true,
                pageLength: 25
            });
        }

        // Charts initialization (if Chart.js available)
        if (typeof Chart !== 'undefined') {
            initCharts();
        }

        function initCharts() {
            // Revenue chart
            var revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Revenue',
                            data: [12000, 19000, 15000, 25000, 22000, 30000],
                            borderColor: '#0066CC',
                            tension: 0.4
                        }]
                    }
                });
            }
        }

        // Export data
        $('#export-data').on('click', function() {
            // Implementation for data export
            alert('Export functionality would be implemented here');
        });

        // Refresh dashboard
        $('#refresh-dashboard').on('click', function() {
            location.reload();
        });
    });

})(jQuery);
