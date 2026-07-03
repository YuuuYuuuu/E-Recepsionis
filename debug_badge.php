<?php
require_once 'config.php';

// Test the generateBadgeNumber function
echo "Testing generateBadgeNumber function...\n";

try {
    $badge_number = generateBadgeNumber();
    echo "Generated badge number: $badge_number\n";
    
    // Test database connection
    echo "Testing database connection...\n";
    $test_query = $koneksi->query("SELECT COUNT(*) as count FROM visitors WHERE DATE(created_at) = CURDATE()");
    if ($test_query) {
        $result = $test_query->fetch_assoc();
        echo "Today's visitor count: " . $result['count'] . "\n";
    } else {
        echo "Database query failed: " . $koneksi->error . "\n";
    }
    
    // Test inserting a sample visitor
    echo "\nTesting visitor insertion...\n";
    $test_badge = generateBadgeNumber();
    $insert_query = "INSERT INTO visitors (nama, no_telp, tujuan, status, checkin_time, badge_number) 
                     VALUES ('Test Visitor', '08123456789', 'Test Purpose', 'checked-in', NOW(), '$test_badge')";
    
    if ($koneksi->query($insert_query)) {
        $visitor_id = $koneksi->insert_id;
        echo "Test visitor inserted with ID: $visitor_id\n";
        echo "Test visitor badge number: $test_badge\n";
        
        // Clean up test data
        $koneksi->query("DELETE FROM visitors WHERE id = $visitor_id");
        echo "Test data cleaned up\n";
    } else {
        echo "Insert failed: " . $koneksi->error . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
