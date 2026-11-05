<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class ActivityLogs extends Security_Controller
{
    protected $activityLogModel;
    public $session;

    function __construct()
    {
        parent::__construct();
        $this->activityLogModel = new ActivityLogModel();
        $this->session = session();
        helper(['form', 'url', 'activity_log']);
    }

    /**
     * Display activity logs listing
     */
public function index()
{
    
    // Get filters from GET request
    $filters = [
        'section'   => $this->request->getGet('section'),
        'user_id'   => $this->request->getGet('user_id'),
        'action'    => $this->request->getGet('action'),
        'date_from' => $this->request->getGet('date_from'),
        'date_to'   => $this->request->getGet('date_to'),
        'search'    => $this->request->getGet('search')
    ];
    
    // Pagination
    $perPage = 50;
    $page = $this->request->getGet('page') ?? 1;
    
    $view_data['logs'] = $this->activityLogModel->getActivityLogs(
        $filters,
        $perPage,
        ($page - 1) * $perPage
    );
    
    $view_data['total_logs'] = $this->activityLogModel->countLogs($filters);
    $view_data['sections'] = $this->activityLogModel->getSections();
    $view_data['statistics'] = $this->activityLogModel->getStatistics();
    $view_data['filters'] = $filters;
    
    // Pagination
    $pager = \Config\Services::pager();
    $view_data['pager'] = $pager->makeLinks($page, $perPage, $view_data['total_logs'], 'default_full');
    $view_data['current_page'] = $page;
    $view_data['per_page'] = $perPage;
    
    return $this->template->rander("activity_logs/index", $view_data);
}

    /**
     * Delete single log
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid log ID');
        }

        if ($this->activityLogModel->delete($id)) {
            return redirect()->to('/activity-logs')->with('success', 'Activity log deleted successfully');
        }

        return redirect()->to('/activity-logs')->with('error', 'Failed to delete activity log');
    }

    /**
     * Delete multiple logs
     */
    public function deleteMultiple()
    {
        $ids = $this->request->getPost('log_ids');

        if (empty($ids)) {
            return redirect()->to('/activity-logs')->with('error', 'Please select logs to delete');
        }

        if ($this->activityLogModel->deleteMultiple($ids)) {
            $count = count($ids);
            return redirect()->to('/activity-logs')->with('success', "{$count} activity log(s) deleted successfully");
        }

        return redirect()->to('/activity-logs')->with('error', 'Failed to delete activity logs');
    }

    /**
     * Export logs to CSV
     */
    public function export()
    {
        $filters = [
            'section'   => $this->request->getGet('section'),
            'user_id'   => $this->request->getGet('user_id'),
            'action'    => $this->request->getGet('action'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
            'search'    => $this->request->getGet('search')
        ];

        // Get all logs without pagination
        $logs = $this->activityLogModel->getActivityLogs($filters, 10000, 0);

        // Set headers for CSV download
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($output, ['Serial No', 'User', 'Phone', 'Section', 'Action', 'Description', 'Date & Time']);

        // Add data rows
        $serial = 1;
        foreach ($logs as $log) {
            fputcsv($output, [
                $serial++,
                $log->user_name,
                $log->user_phone ?? '',
                ucfirst($log->section),
                ucfirst($log->action),
                $log->description,
                date('d M Y, h:i A', strtotime($log->created_at))
            ]);
        }

        fclose($output);
        exit;
    }
    

    /**
     * Clear old logs (older than specified days)
     */
    public function clearOldLogs()
    {
        $days = $this->request->getPost('days') ? (int)$this->request->getPost('days') : 90;

        $deleted = $this->activityLogModel->clearOldLogs($days);

        if ($deleted) {
            return redirect()->to('/activity-logs')->with('success', "Successfully deleted {$deleted} old activity log(s)");
        }

        return redirect()->to('/activity-logs')->with('error', 'No old logs found to delete');
    }
}