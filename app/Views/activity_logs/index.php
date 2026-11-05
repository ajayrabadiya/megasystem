<div id="page-content" class="page-wrapper clearfix">
    <div class="card shadow-sm">
        <div class="page-title clearfix border-bottom">
            <h1 class="mb-0"><?php echo app_lang('activity_logs'); ?></h1>
            <div class="title-button-group">
                <a href="<?= get_uri('activity-logs/export?' . http_build_query($filters)) ?>" class="btn btn-primary">
                    <i data-feather="download" class="icon-16"></i> <?php echo app_lang('export'); ?>
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Statistics Cards -->
            <div class="row mb-4 g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-card-sky">
                        <div class="stat-icon">
                            <i data-feather="calendar" class="icon"></i>
                        </div>
                        <div class="stat-content">
                            <h2 class="stat-number"><?= $statistics['today'] ?></h2>
                            <p class="stat-label"><?php echo app_lang('today_activities'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-card-success">
                        <div class="stat-icon">
                            <i data-feather="calendar" class="icon"></i>
                        </div>
                        <div class="stat-content">
                            <h2 class="stat-number"><?= $statistics['week'] ?></h2>
                            <p class="stat-label"><?php echo app_lang('this_week'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-card-warning">
                        <div class="stat-icon">
                            <i data-feather="calendar" class="icon"></i>
                        </div>
                        <div class="stat-content">
                            <h2 class="stat-number"><?= $statistics['month'] ?></h2>
                            <p class="stat-label"><?php echo app_lang('this_month'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card stat-card-coral">
                        <div class="stat-icon">
                            <i data-feather="list" class="icon"></i>
                        </div>
                        <div class="stat-content">
                            <h2 class="stat-number"><?= $total_logs ?></h2>
                            <p class="stat-label"><?php echo app_lang('total_logs'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-section bg-light p-3 rounded mb-4">
                <form method="get" action="<?= get_uri('activity-logs') ?>" id="filterForm">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <label class="form-label"><?php echo app_lang('section'); ?></label>
                            <?php
                            $sections_array = ['' => '- ' . app_lang('all_sections') . ' -'];
                            foreach ($sections as $sec) {
                                $sections_array[$sec->section] = ucfirst($sec->section);
                            }
                            echo form_dropdown(
                                "section",
                                $sections_array,
                                $filters['section'],
                                "class='form-select'"
                            );
                            ?>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label"><?php echo app_lang('date_from'); ?></label>
                            <?php
                            echo form_input(array(
                                "name" => "date_from",
                                "value" => $filters['date_from'],
                                "class" => "form-control",
                                "placeholder" => app_lang('date_from'),
                                "autocomplete" => "off",
                                "type" => "date"
                            ));
                            ?>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <label class="form-label"><?php echo app_lang('date_to'); ?></label>
                            <?php
                            echo form_input(array(
                                "name" => "date_to",
                                "value" => $filters['date_to'],
                                "class" => "form-control",
                                "placeholder" => app_lang('date_to'),
                                "autocomplete" => "off",
                                "type" => "date"
                            ));
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <label class="form-label"><?php echo app_lang('search'); ?></label>
                            <?php
                            echo form_input(array(
                                "name" => "search",
                                "value" => $filters['search'],
                                "class" => "form-control",
                                "placeholder" => app_lang('search_by_user_or_description')
                            ));
                            ?>
                        </div>
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="search" class="icon-16"></i> <?php echo app_lang('filter'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-auto">
                            <a href="<?= get_uri('activity-logs') ?>" class="btn btn-outline-secondary">
                                <i data-feather="refresh-cw" class="icon-16"></i> <?php echo app_lang('reset'); ?>
                            </a>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-danger" id="deleteSelected">
                                <i data-feather="trash-2" class="icon-16"></i> <?php echo app_lang('delete_selected'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Activity Logs Table -->
            <div class="table-responsive">
                <form method="post" action="<?= get_uri('activity-logs/delete-multiple') ?>" id="deleteForm">
                    <?= csrf_field() ?>
                    <table id="activity-logs-table" class="table table-hover align-middle" cellspacing="0" width="100%">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">
                                    <input type="checkbox" id="checkAll" class="form-check-input">
                                </th>
                                <th style="width: 60px;"><?php echo app_lang('serial_no'); ?></th>
                                <th><?php echo app_lang('user'); ?></th>
                                <th><?php echo app_lang('phone'); ?></th>
                                <th><?php echo app_lang('section'); ?></th>
                                <th><?php echo app_lang('activity'); ?></th>
                                <th><?php echo app_lang('date_time'); ?></th>
                                <th class="text-center" style="width: 100px;"><?php echo app_lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php 
                                $serial = (($current_page - 1) * $per_page) + 1;
                                foreach ($logs as $log): 
                                ?>
                                    <tr id="activity-log-row-<?= $log->id ?>" class="log-row">
                                        <td class="text-center">
                                            <input type="checkbox" name="log_ids[]" value="<?= $log->id ?>" class="form-check-input log-checkbox">
                                        </td>
                                        <td><span class="badge bg-secondary"><?= $serial++ ?></span></td>
                                        <td>
                                            <div class="user-info">
                                                <strong class="d-block"><?= esc($log->user_name) ?></strong>
                                                <?php if (isset($log->user_email) && $log->user_email): ?>
                                                    <small class="text-muted"><?= esc($log->user_email) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= esc($log->user_phone ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-info text-dark"><?= ucfirst(esc($log->section)) ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $actionColors = [
                                                'created' => 'success',
                                                'updated' => 'warning',
                                                'deleted' => 'danger',
                                                'logged_in' => 'primary',
                                                'logged_out' => 'secondary',
                                                'viewed' => 'info'
                                            ];
                                            $color = $actionColors[$log->action] ?? 'secondary';
                                            ?>
                                            <div class="activity-info">
                                                <span class="badge bg-<?= $color ?> mb-1">
                                                    <?= ucfirst(esc($log->action)) ?>
                                                </span>
                                                <div class="text-muted small"><?= esc($log->description) ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="datetime-info">
                                                <div class="fw-medium"><?= format_to_date($log->created_at, false) ?></div>
                                                <small class="text-muted"><?= format_to_time($log->created_at) ?></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            echo anchor(get_uri("activity-logs/delete/" . $log->id), "<i data-feather='trash-2' class='icon-16'></i>", array(
                                                "class" => "btn btn-outline-danger btn-sm delete",
                                                "title" => app_lang('delete'),
                                                "data-fade-out-on-success" => "#activity-log-row-" . $log->id
                                            ));
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i data-feather="info" class="icon-48 text-muted mb-3"></i>
                                            <p class="text-muted"><?php echo app_lang('no_activity_logs_found'); ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <?php if (!empty($logs) && $pager): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <?= $pager ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // Check all checkboxes
    const checkAllBox = document.getElementById('checkAll');
    if (checkAllBox) {
        checkAllBox.addEventListener('change', function() {
            var checkboxes = document.getElementsByClassName('log-checkbox');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
    }

    // Update "check all" state when individual checkboxes change
    const logCheckboxes = document.querySelectorAll('.log-checkbox');
    logCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(logCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(logCheckboxes).some(cb => cb.checked);
            if (checkAllBox) {
                checkAllBox.checked = allChecked;
                checkAllBox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Delete selected logs
    const deleteBtn = document.getElementById('deleteSelected');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            var checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
            if (checkedBoxes.length === 0) {
                alert('Please select at least one log to delete');
                return;
            }

            if (confirm('Are you sure you want to delete ' + checkedBoxes.length + ' selected log(s)?')) {
                document.getElementById('deleteForm').submit();
            }
        });
    }

    // Initialize feather icons if available
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

<style>
/* Statistics Cards */
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid rgba(0,0,0,0.05);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    flex-shrink: 0;
}

.stat-card-sky .stat-icon {
    background: linear-gradient(135deg, #5bc0de 0%, #4a9db8 100%);
    color: white;
}

.stat-card-success .stat-icon {
    background: linear-gradient(135deg, #5cb85c 0%, #4a9847 100%);
    color: white;
}

.stat-card-warning .stat-icon {
    background: linear-gradient(135deg, #f0ad4e 0%, #d89a3d 100%);
    color: white;
}

.stat-card-coral .stat-icon {
    background: linear-gradient(135deg, #d9534f 0%, #c44440 100%);
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
    line-height: 1;
    color: #2c3e50;
}

.stat-label {
    margin: 5px 0 0 0;
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

/* Filter Section */
.filter-section {
    border: 1px solid #dee2e6;
}

/* Table Styles */
.table-responsive {
    border-radius: 8px;
    /*overflow: hidden;*/
    border: 1px solid #dee2e6;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
    padding: 12px;
}

.table tbody tr {
    transition: background-color 0.2s;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table td {
    padding: 12px;
    vertical-align: middle;
}

/* User Info */
.user-info strong {
    color: #2c3e50;
    font-size: 14px;
}

.user-info small {
    font-size: 12px;
}

/* Activity Info */
.activity-info .badge {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
}

.activity-info .small {
    font-size: 12px;
    line-height: 1.4;
}

/* DateTime Info */
.datetime-info .fw-medium {
    color: #2c3e50;
    font-size: 13px;
}

.datetime-info small {
    font-size: 11px;
}

/* Empty State */
.empty-state {
    padding: 40px 20px;
}

.empty-state .icon-48 {
    width: 48px;
    height: 48px;
}

/* Page Title */
.page-title {
    padding: 20px 24px;
    background: white;
}

.page-title h1 {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
}

/* Buttons */
.btn {
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.btn-outline-danger:hover {
    transform: translateY(-1px);
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #ced4da;
    padding: 8px 12px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

/* Checkbox Styling */
.form-check-input {
    cursor: pointer;
    width: 18px;
    height: 18px;
    margin-top: 0;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Icon Sizing */
.icon-16 {
    width: 16px;
    height: 16px;
}

.icon-48 {
    width: 48px;
    height: 48px;
}

/* Badges */
.badge {
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 4px;
}

ul.pagination .active a {background: #0D6EFD;}
.pagination li:first-child a {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}
ul.pagination li a {
    border: 1px solid #DEE2E6;
    padding: 0.375rem 0.735rem;
}
ul.pagination li:last-child  a {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}
.pagination li.active a {
    color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
    .stat-number {
        font-size: 24px;
    }
    
    .stat-label {
        font-size: 12px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
    }
    
    .page-title h1 {
        font-size: 20px;
    }
    
    .table {
        font-size: 14px;
    }
}
</style>