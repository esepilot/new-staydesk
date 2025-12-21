<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url('/staydesk-login'));
    exit;
}

wp_enqueue_script('jquery');

global $wpdb;
$user_id = get_current_user_id();
$table_hotels = $wpdb->prefix . 'staydesk_hotels';
$hotel = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_hotels WHERE user_id = %d",
    $user_id
));

if (!$hotel) {
    echo '<p>Hotel profile not found.</p>';
    return;
}

$table_bookings = $wpdb->prefix . 'staydesk_bookings';
$table_rooms = $wpdb->prefix . 'staydesk_rooms';
$table_guests = $wpdb->prefix . 'staydesk_guests';

$bookings = $wpdb->get_results($wpdb->prepare("
    SELECT b.*, r.room_name, r.room_type, g.guest_name, g.guest_email, g.guest_phone
    FROM $table_bookings b
    LEFT JOIN $table_rooms r ON b.room_id = r.id
    LEFT JOIN $table_guests g ON b.guest_id = g.id
    WHERE b.hotel_id = %d
    ORDER BY b.created_at DESC
", $hotel->id));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
            padding: 25px;
        }
        
        .bookings-container {
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .page-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 30px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .page-header h1 {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            font-size: 2.2rem;
            text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
        }
        
        .filter-section {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 18px;
            margin-bottom: 20px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(212, 175, 55, 0.3);
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-section select,
        .filter-section input {
            padding: 12px;
            background: #2a2a2a;
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 15px;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-back {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            color: #F0F0F0;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .bookings-table {
            background: #1a1a1a;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        }
        
        th {
            padding: 18px;
            text-align: left;
            color: #D4AF37;
            font-weight: 700;
            border-bottom: 2px solid rgba(212, 175, 55, 0.2);
        }
        
        td {
            padding: 18px;
            color: #E8E8E8;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        tr:hover {
            background: #2a2a2a;
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #FFC107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .status-confirmed {
            background: rgba(40, 167, 69, 0.2);
            color: #28A745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .status-cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #DC3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .status-completed {
            background: rgba(23, 162, 184, 0.2);
            color: #17A2B8;
            border: 1px solid rgba(23, 162, 184, 0.3);
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #A0A0A0;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #E8E8E8;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(212, 175, 55, 0.2);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #A0A0A0;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="bookings-container">
        <div class="page-header">
            <h1>Bookings Management</h1>
            <button class="btn btn-back" onclick="window.location.href='<?php echo home_url('/staydesk-dashboard'); ?>'">← Back to Dashboard</button>
        </div>
        
        <div class="stats-row">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="value"><?php echo count($bookings); ?></div>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <div class="value"><?php echo count(array_filter($bookings, fn($b) => $b->booking_status === 'pending')); ?></div>
            </div>
            <div class="stat-card">
                <h3>Confirmed</h3>
                <div class="value"><?php echo count(array_filter($bookings, fn($b) => $b->booking_status === 'confirmed')); ?></div>
            </div>
            <div class="stat-card">
                <h3>Completed</h3>
                <div class="value"><?php echo count(array_filter($bookings, fn($b) => $b->booking_status === 'completed')); ?></div>
            </div>
        </div>
        
        <div class="filter-section">
            <select id="statusFilter">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
            </select>
            <input type="text" id="searchInput" placeholder="Search by guest name or reference...">
        </div>
        
        <div class="bookings-table">
            <?php if (empty($bookings)): ?>
                <div class="empty-state">
                    <h3>No Bookings Yet</h3>
                    <p>Bookings will appear here once guests make reservations</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr class="booking-row" data-status="<?php echo esc_attr($booking->booking_status); ?>">
                                <td><strong><?php echo esc_html($booking->booking_reference); ?></strong></td>
                                <td>
                                    <?php echo esc_html($booking->guest_name); ?><br>
                                    <small style="color: #A0A0A0;"><?php echo esc_html($booking->guest_email); ?></small>
                                </td>
                                <td>
                                    <?php echo esc_html($booking->room_name); ?><br>
                                    <small style="color: #A0A0A0;"><?php echo esc_html($booking->room_type); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($booking->check_in_date)); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking->check_out_date)); ?></td>
                                <td><strong>₦<?php echo number_format($booking->total_amount, 2); ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking->booking_status; ?>">
                                        <?php echo ucfirst($booking->booking_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking->payment_status; ?>">
                                        <?php echo ucfirst($booking->payment_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking->booking_status === 'pending'): ?>
                                        <button class="btn btn-primary btn-sm" onclick="updateStatus(<?php echo $booking->id; ?>, 'confirmed')">Confirm</button>
                                    <?php elseif ($booking->booking_status === 'confirmed'): ?>
                                        <button class="btn btn-primary btn-sm" onclick="updateStatus(<?php echo $booking->id; ?>, 'completed')">Complete</button>
                                    <?php endif; ?>
                                    <button class="btn btn-secondary btn-sm" onclick="editBooking(<?php echo $booking->id; ?>)">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteBooking(<?php echo $booking->id; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Edit Booking Modal -->
    <div id="editBookingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: #1a1a1a; padding: 30px; border-radius: 18px; max-width: 500px; width: 90%; border: 1px solid rgba(212, 175, 55, 0.3);">
            <h2 style="color: #D4AF37; margin-bottom: 20px;">Edit Booking</h2>
            <form id="editBookingForm">
                <input type="hidden" id="edit_booking_id">
                <div style="margin-bottom: 15px;">
                    <label style="color: #FFFFFF; display: block; margin-bottom: 5px;">Check-in Date:</label>
                    <input type="date" id="edit_check_in" required style="width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; color: #FFFFFF;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #FFFFFF; display: block; margin-bottom: 5px;">Check-out Date:</label>
                    <input type="date" id="edit_check_out" required style="width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; color: #FFFFFF;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="color: #FFFFFF; display: block; margin-bottom: 5px;">Number of Guests:</label>
                    <input type="number" id="edit_num_guests" required min="1" style="width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; color: #FFFFFF;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="color: #FFFFFF; display: block; margin-bottom: 5px;">Special Requests:</label>
                    <textarea id="edit_special_requests" rows="3" style="width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; color: #FFFFFF;"></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
            // Filter by status
            $('#statusFilter').on('change', function() {
                const status = $(this).val();
                if (status) {
                    $('.booking-row').hide();
                    $(`.booking-row[data-status="${status}"]`).show();
                } else {
                    $('.booking-row').show();
                }
            });
            
            // Search functionality
            $('#searchInput').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.booking-row').each(function() {
                    const text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(searchTerm) > -1);
                });
            });
        });
        
        function updateStatus(bookingId, newStatus) {
            if (!confirm('Are you sure you want to change this booking status?')) return;
            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_update_booking_status',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                    booking_id: bookingId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert('Booking status updated!');
                        location.reload();
                    } else {
                        alert('Error updating status');
                    }
                },
                error: function() {
                    alert('An error occurred');
                }
            });
        }
        
        function editBooking(bookingId) {
            // Get booking details
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_get_booking',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                    booking_id: bookingId
                },
                success: function(response) {
                    if (response.success) {
                        const booking = response.data;
                        document.getElementById('edit_booking_id').value = booking.id;
                        document.getElementById('edit_check_in').value = booking.check_in_date;
                        document.getElementById('edit_check_out').value = booking.check_out_date;
                        document.getElementById('edit_num_guests').value = booking.num_guests;
                        document.getElementById('edit_special_requests').value = booking.special_requests || '';
                        document.getElementById('editBookingModal').style.display = 'flex';
                    } else {
                        alert('Error loading booking details');
                    }
                },
                error: function() {
                    alert('An error occurred');
                }
            });
        }
        
        function closeEditModal() {
            document.getElementById('editBookingModal').style.display = 'none';
        }
        
        function deleteBooking(bookingId) {
            if (!confirm('Are you sure you want to delete this booking? This action cannot be undone.')) return;
            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_delete_booking',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                    booking_id: bookingId
                },
                success: function(response) {
                    if (response.success) {
                        alert('Booking deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error deleting booking');
                    }
                },
                error: function() {
                    alert('An error occurred');
                }
            });
        }
        
        // Handle edit form submission
        jQuery('#editBookingForm').on('submit', function(e) {
            e.preventDefault();
            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_edit_booking',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                    booking_id: jQuery('#edit_booking_id').val(),
                    check_in_date: jQuery('#edit_check_in').val(),
                    check_out_date: jQuery('#edit_check_out').val(),
                    num_guests: jQuery('#edit_num_guests').val(),
                    special_requests: jQuery('#edit_special_requests').val()
                },
                success: function(response) {
                    if (response.success) {
                        alert('Booking updated successfully!');
                        location.reload();
                    } else {
                        alert('Error updating booking');
                    }
                },
                error: function() {
                    alert('An error occurred');
                }
            });
        });
    </script>
</body>
</html>
