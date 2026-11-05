<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'mega_activity_logs_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'user_name',
        'user_phone',
        'section',
        'action',
        'record_id',
        'description',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'user_id'   => 'required|integer',
        'section'   => 'required|string|max_length[100]',
        'action'    => 'required|string|max_length[50]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Log an activity
     * 
     * @param array $data
     * @return int|bool
     */
public function logActivity(array $data)
{
    $session = session();
    $request = \Config\Services::request();
    $db = \Config\Database::connect(); // Connect to the database

    // 1. Get user_id from session if not provided in $data
    if (!isset($data['user_id']) && $session->get('user_id')) {
        $data['user_id'] = $session->get('user_id');
    }

    // 2. If we have a user_id, fetch missing info from the database
    if (isset($data['user_id'])) {
        $builder = $db->table('users'); // Change 'users' to your actual table name
        $user = $builder->where('id', $data['user_id'])->get()->getRow();

        if ($user) {
            if (!isset($data['user_name'])) {
                $data['user_name'] = $user->first_name . ' ' . $user->last_name; // Adjust column name as per your DB
            }
            if (!isset($data['user_phone'])) {
                $data['user_phone'] = $user->phone; // Adjust column name as per your DB
            }
        }
    }

    // 3. Add metadata
    $data['ip_address'] = $request->getIPAddress();
    $data['user_agent'] = $request->getUserAgent()->getAgentString();
    $data['created_at'] = date('Y-m-d H:i:s');

    return $this->insert($data);
}

    /**
     * Get all activity logs with filters
     * 
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getActivityLogs(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('mega_activity_logs_users.*, users.email as user_email');
        $builder->join('users', 'users.id = mega_activity_logs_users.user_id', 'left');

        // Apply filters
        if (!empty($filters['section'])) {
            $builder->where('mega_activity_logs_users.section', $filters['section']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('mega_activity_logs_users.user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $builder->where('mega_activity_logs_users.action', $filters['action']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(mega_activity_logs_users.created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(mega_activity_logs_users.created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart();
            $builder->like('mega_activity_logs_users.description', $filters['search']);
            $builder->orLike('mega_activity_logs_users.user_name', $filters['search']);
            $builder->groupEnd();
        }

        $builder->orderBy('mega_activity_logs_users.created_at', 'DESC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResult();
    }

    /**
     * Count total logs with filters
     * 
     * @param array $filters
     * @return int
     */
    public function countLogs(array $filters = [])
    {
        $builder = $this->db->table($this->table);

        if (!empty($filters['section'])) {
            $builder->where('section', $filters['section']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $builder->where('action', $filters['action']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(created_at) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart();
            $builder->like('description', $filters['search']);
            $builder->orLike('user_name', $filters['search']);
            $builder->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Delete multiple logs
     * 
     * @param array $ids
     * @return bool
     */
    public function deleteMultiple(array $ids)
    {
        return $this->whereIn('id', $ids)->delete();
    }

    /**
     * Get unique sections
     * 
     * @return array
     */
    public function getSections()
    {
        return $this->select('section')
                    ->distinct()
                    ->orderBy('section', 'ASC')
                    ->findAll();
    }

    /**
     * Get activity statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $stats = [];

        // Total activities today
        $stats['today'] = $this->where('DATE(created_at)', date('Y-m-d'))
                              ->countAllResults();

        // Total activities this week
        $stats['week'] = $this->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                             ->countAllResults();

        // Total activities this month
        $stats['month'] = $this->where('MONTH(created_at)', date('m'))
                              ->where('YEAR(created_at)', date('Y'))
                              ->countAllResults();

        // Most active users
        $builder = $this->db->table($this->table);
        $stats['top_users'] = $builder->select('user_name, COUNT(*) as count')
                                      ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
                                      ->groupBy('user_id')
                                      ->orderBy('count', 'DESC')
                                      ->limit(5)
                                      ->get()
                                      ->getResult();

        return $stats;
    }

    /**
     * Clear old logs
     * 
     * @param int $days
     * @return int
     */
    public function clearOldLogs(int $days = 90)
    {
        $date = date('Y-m-d', strtotime("-{$days} days"));
        return $this->where('created_at <', $date)->delete();
    }
}