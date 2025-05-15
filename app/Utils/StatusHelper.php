<?php
namespace App\Utils;

/**
 * Helper class for handling appointment status values consistently
 */
class StatusHelper {
    // Valid status values in the system
    public static $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    
    // Mapping for display and database values
    public static $statusMap = [
        'pending' => 'PENDING',
        'confirmed' => 'CONFIRMED',
        'completed' => 'COMPLETED',
        'cancelled' => 'CANCELLED'
    ];
    
    /**
     * Get the display version of a status (Title Case)
     * 
     * @param string $status The status value
     * @return string Formatted status for display
     */
    public static function getDisplayStatus($status) {
        return ucfirst(strtolower($status));
    }
    
    /**
     * Get the database version of a status (UPPERCASE)
     * 
     * @param string $status The status value
     * @return string Formatted status for database
     */
    public static function getDbStatus($status) {
        $normalizedStatus = strtolower(trim($status));
        
        if (isset(self::$statusMap[$normalizedStatus])) {
            return self::$statusMap[$normalizedStatus];
        }
        
        // If not in map, return uppercase
        return strtoupper($normalizedStatus);
    }
    
    /**
     * Check if a status value is valid
     * 
     * @param string $status The status to validate
     * @return bool True if valid, false otherwise
     */
    public static function isValidStatus($status) {
        return in_array(strtolower($status), self::$validStatuses);
    }
    
    /**
     * Get the Bootstrap CSS class for a status
     * 
     * @param string $status The status value
     * @return string Bootstrap class for the status
     */
    public static function getStatusClass($status) {
        $normalizedStatus = strtolower(trim($status));
        
        switch($normalizedStatus) {
            case 'pending':
                return 'bg-warning';
            case 'confirmed':
                return 'bg-success';
            case 'completed':
                return 'bg-info';
            case 'cancelled':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
    
    /**
     * Get count of appointments by status
     * 
     * @param PDO $db Database connection
     * @param string $status Status to filter by
     * @param string|null $date Optional date filter (Y-m-d)
     * @return int Number of appointments with the status
     */
    public static function getAppointmentCountByStatus($db, $status, $date = null) {
        $dbStatus = self::getDbStatus($status);
        
        if ($date) {
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM appointment 
                WHERE UPPER(status) = ? AND DATE(preferred_date) = ?
            ");
            $stmt->execute([$dbStatus, $date]);
        } else {
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM appointment 
                WHERE UPPER(status) = ?
            ");
            $stmt->execute([$dbStatus]);
        }
        
        return $stmt->fetchColumn();
    }
}
?> 