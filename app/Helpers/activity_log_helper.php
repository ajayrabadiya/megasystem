<?php

/**
 * Activity Log Helper for CodeIgniter 4
 * 
 * File: app/Helpers/activity_log_helper.php
 * 
 * This helper provides convenient functions to log activities throughout your application
 */

if (!function_exists('log_activity')) {
    /**
     * Log activity helper function
     * 
     * @param string $section - Module name (clients, projects, tasks, etc.)
     * @param string $action - Action performed (created, updated, deleted, etc.)
     * @param string $description - Detailed description
     * @param int|null $record_id - Optional: ID of affected record
     * @return int|bool
     */
    function log_activity(string $section, string $action, string $description, ?int $record_id = null)
    {
        $activityLogModel = new \App\Models\ActivityLogModel();

        $data = [
            'section'     => $section,
            'action'      => $action,
            'description' => $description,
            'record_id'   => $record_id
        ];

        return $activityLogModel->logActivity($data);
    }
}

if (!function_exists('log_created')) {
    /**
     * Log creation activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @return int|bool
     */
    function log_created(string $section, string $record_name, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' has been created";
        return log_activity($section, 'created', $description, $record_id);
    }
}

if (!function_exists('log_updated')) {
    /**
     * Log update activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @param array $changes
     * @return int|bool
     */
    function log_updated(string $section, string $record_name, ?int $record_id = null, array $changes = [])
    {
        $description = ucfirst($section) . " '{$record_name}' has been updated";
        
        if (!empty($changes)) {
            $description .= ". Changes: " . implode(', ', $changes);
        }
        
        return log_activity($section, 'updated', $description, $record_id);
    }
}

if (!function_exists('log_deleted')) {
    /**
     * Log delete activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @return int|bool
     */
    function log_deleted(string $section, string $record_name, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' has been deleted";
        return log_activity($section, 'deleted', $description, $record_id);
    }
}

if (!function_exists('log_viewed')) {
    /**
     * Log view activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @return int|bool
     */
    function log_viewed(string $section, string $record_name, ?int $record_id = null)
    {
        $description = "Viewed " . strtolower($section) . " '{$record_name}'";
        return log_activity($section, 'viewed', $description, $record_id);
    }
}

if (!function_exists('log_login')) {
    /**
     * Log login activity
     * 
     * @return int|bool
     */
    function log_login()
    {
        $session = session();
        $user_name = $session->get('user_name') ?? 'Unknown User';
        $description = "User '{$user_name}' logged into the system";
        return log_activity('authentication', 'logged_in', $description);
    }
}

if (!function_exists('log_logout')) {
    /**
     * Log logout activity
     * 
     * @return int|bool
     */
    function log_logout()
    {
        $session = session();
        $user_name = $session->get('user_name') ?? 'Unknown User';
        $description = "User '{$user_name}' logged out from the system";
        return log_activity('authentication', 'logged_out', $description);
    }
}

if (!function_exists('log_status_change')) {
    /**
     * Log status change activity
     * 
     * @param string $section
     * @param string $record_name
     * @param string $old_status
     * @param string $new_status
     * @param int|null $record_id
     * @return int|bool
     */
    function log_status_change(string $section, string $record_name, string $old_status, string $new_status, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' status changed from '{$old_status}' to '{$new_status}'";
        return log_activity($section, 'status_changed', $description, $record_id);
    }
}

if (!function_exists('log_uploaded')) {
    /**
     * Log file upload activity
     * 
     * @param string $section
     * @param string $filename
     * @param int|null $record_id
     * @return int|bool
     */
    function log_uploaded(string $section, string $filename, ?int $record_id = null)
    {
        $description = "File '{$filename}' has been uploaded to " . strtolower($section);
        return log_activity($section, 'uploaded', $description, $record_id);
    }
}

if (!function_exists('log_downloaded')) {
    /**
     * Log file download activity
     * 
     * @param string $section
     * @param string $filename
     * @param int|null $record_id
     * @return int|bool
     */
    function log_downloaded(string $section, string $filename, ?int $record_id = null)
    {
        $description = "File '{$filename}' has been downloaded from " . strtolower($section);
        return log_activity($section, 'downloaded', $description, $record_id);
    }
}

if (!function_exists('log_sent')) {
    /**
     * Log sent activity (emails, notifications, etc.)
     * 
     * @param string $section
     * @param string $description
     * @param int|null $record_id
     * @return int|bool
     */
    function log_sent(string $section, string $description, ?int $record_id = null)
    {
        return log_activity($section, 'sent', $description, $record_id);
    }
}

if (!function_exists('log_assigned')) {
    /**
     * Log assignment activity
     * 
     * @param string $section
     * @param string $record_name
     * @param string $assigned_to
     * @param int|null $record_id
     * @return int|bool
     */
    function log_assigned(string $section, string $record_name, string $assigned_to, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' has been assigned to {$assigned_to}";
        return log_activity($section, 'assigned', $description, $record_id);
    }
}

if (!function_exists('log_completed')) {
    /**
     * Log completion activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @return int|bool
     */
    function log_completed(string $section, string $record_name, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' has been marked as completed";
        return log_activity($section, 'completed', $description, $record_id);
    }
}

if (!function_exists('log_cancelled')) {
    /**
     * Log cancellation activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @return int|bool
     */
    function log_cancelled(string $section, string $record_name, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' has been cancelled";
        return log_activity($section, 'cancelled', $description, $record_id);
    }
}

if (!function_exists('log_approved')) {
    /**
     * Log approval activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @return int|bool
     */
    function log_approved(string $section, string $record_name, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' has been approved";
        return log_activity($section, 'approved', $description, $record_id);
    }
}

if (!function_exists('log_rejected')) {
    /**
     * Log rejection activity
     * 
     * @param string $section
     * @param string $record_name
     * @param int|null $record_id
     * @return int|bool
     */
    function log_rejected(string $section, string $record_name, ?int $record_id = null)
    {
        $description = ucfirst($section) . " '{$record_name}' has been rejected";
        return log_activity($section, 'rejected', $description, $record_id);
    }
}

if (!function_exists('log_exported')) {
    /**
     * Log export activity
     * 
     * @param string $section
     * @param string $description
     * @param int|null $record_id
     * @return int|bool
     */
    function log_exported(string $section, string $description, ?int $record_id = null)
    {
        return log_activity($section, 'exported', $description, $record_id);
    }
}

if (!function_exists('log_imported')) {
    /**
     * Log import activity
     * 
     * @param string $section
     * @param string $description
     * @param int|null $record_id
     * @return int|bool
     */
    function log_imported(string $section, string $description, ?int $record_id = null)
    {
        return log_activity($section, 'imported', $description, $record_id);
    }
}