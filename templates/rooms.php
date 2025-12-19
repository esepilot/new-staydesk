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

$table_rooms = $wpdb->prefix . 'staydesk_rooms';
$rooms = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_rooms WHERE hotel_id = %d ORDER BY created_at DESC",
    $hotel->id
));
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
        
        .rooms-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .page-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 35px;
            border-radius: 18px;
            margin-bottom: 35px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .page-header h1 {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            font-size: 2.5rem;
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 35px rgba(212, 175, 55, 0.6);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #DC3545 0%, #C82333 100%);
            color: white;
            box-shadow: 0 4px 20px rgba(220, 53, 69, 0.3);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 35px rgba(220, 53, 69, 0.5);
        }
        
        .btn-back {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            color: #E8E8E8;
            border: 1px solid rgba(212, 175, 55, 0.2);
            margin-right: 15px;
        }
        
        .btn-back:hover {
            border-color: rgba(212, 175, 55, 0.5);
        }
        
        .form-section {
            background: #1a1a1a;
            padding: 35px;
            border-radius: 18px;
            margin-bottom: 35px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(212, 175, 55, 0.2);
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
        
        .form-section h2 {
            color: #D4AF37;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #E8E8E8;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px;
            background: #2a2a2a;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            color: #E8E8E8;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        .rooms-table {
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
        
        .status-available {
            background: rgba(40, 167, 69, 0.2);
            color: #28A745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .status-unavailable {
            background: rgba(220, 53, 69, 0.2);
            color: #DC3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert.show {
            display: block;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28A745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            color: #DC3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
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
    </style>
</head>
<body>
    <div class="rooms-container">
        <div class="page-header">
            <h1>Room Management</h1>
            <div>
                <button class="btn btn-back" onclick="window.location.href='<?php echo home_url('/staydesk-dashboard'); ?>'">← Back to Dashboard</button>
                <button class="btn btn-primary" onclick="toggleForm()">+ Add New Room</button>
            </div>
        </div>
        
        <div id="alertBox" class="alert"></div>
        
        <div id="roomForm" class="form-section">
            <h2>Add New Room</h2>
            <form id="addRoomForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Room Name *</label>
                        <input type="text" name="room_name" required>
                    </div>
                    <div class="form-group">
                        <label>Room Type *</label>
                        <select name="room_type" required>
                            <option value="">Select Type</option>
                            <option value="Single">Single</option>
                            <option value="Double">Double</option>
                            <option value="Suite">Suite</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Presidential">Presidential</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price per Night (₦) *</label>
                        <input type="number" name="price_per_night" required min="0" step="100">
                    </div>
                    <div class="form-group">
                        <label>Max Guests *</label>
                        <input type="number" name="max_guests" required min="1" max="10">
                    </div>
                    <div class="form-group">
                        <label>Bed Type</label>
                        <select name="bed_type">
                            <option value="Single">Single Bed</option>
                            <option value="Double">Double Bed</option>
                            <option value="Queen">Queen Bed</option>
                            <option value="King">King Bed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Size (sq ft)</label>
                        <input type="number" name="room_size" min="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Amenities (comma separated)</label>
                    <input type="text" name="amenities" placeholder="WiFi, TV, Air Conditioning, Mini Bar">
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="availability_status">
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">Save Room</button>
                    <button type="button" class="btn btn-back" onclick="toggleForm()">Cancel</button>
                </div>
            </form>
        </div>
        
        <div class="rooms-table">
            <?php if (empty($rooms)): ?>
                <div class="empty-state">
                    <h3>No Rooms Yet</h3>
                    <p>Click "Add New Room" to get started</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Room Name</th>
                            <th>Type</th>
                            <th>Price/Night</th>
                            <th>Max Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><strong><?php echo esc_html($room->room_name); ?></strong></td>
                                <td><?php echo esc_html($room->room_type); ?></td>
                                <td>₦<?php echo number_format($room->price_per_night, 0); ?></td>
                                <td><?php echo esc_html($room->max_guests); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $room->availability_status; ?>">
                                        <?php echo ucfirst($room->availability_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-sm" onclick="toggleStatus(<?php echo $room->id; ?>, '<?php echo $room->availability_status; ?>')">
                                            <?php echo $room->availability_status === 'available' ? 'Disable' : 'Enable'; ?>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteRoom(<?php echo $room->id; ?>)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function toggleForm() {
            const form = document.getElementById('roomForm');
            form.classList.toggle('active');
            if (form.classList.contains('active')) {
                document.getElementById('addRoomForm').reset();
            }
        }
        
        function showAlert(message, type) {
            const alert = document.getElementById('alertBox');
            alert.className = 'alert alert-' + type + ' show';
            alert.textContent = message;
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }
        
        jQuery(document).ready(function($) {
            $('#addRoomForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'staydesk_add_room',
                        nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                        hotel_id: <?php echo $hotel->id; ?>,
                        ...Object.fromEntries(new FormData(this))
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Room added successfully!', 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showAlert(response.data.message || 'Error adding room', 'error');
                        }
                    },
                    error: function() {
                        showAlert('An error occurred. Please try again.', 'error');
                    }
                });
            });
        });
        
        function toggleStatus(roomId, currentStatus) {
            const newStatus = currentStatus === 'available' ? 'unavailable' : 'available';
            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_update_room_status',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                    room_id: roomId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('Room status updated!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('Error updating status', 'error');
                    }
                }
            });
        }
        
        function deleteRoom(roomId) {
            if (!confirm('Are you sure you want to delete this room?')) return;
            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_delete_room',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                    room_id: roomId
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('Room deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('Error deleting room', 'error');
                    }
                }
            });
        }
    </script>
</body>
</html>
