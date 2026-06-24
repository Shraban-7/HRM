/**
 * Employee Page Script
 * DataTable initialization, form handling, delete confirmations.
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize employee DataTable if it exists
        const $employeeTable = $('#employees-table');
        if ($employeeTable.length && $.fn.DataTable) {
            $employeeTable.hrmDataTable({
                pageLength: 15,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: -1 },
                ],
            });
        }

        // Delete employee confirmation
        $(document).on('click', '.delete-employee', function(e) {
            e.preventDefault();
            const $form = $(this).closest('form');
            const employeeName = $(this).data('employee-name') || 'this employee';

            window.confirmAction(
                'Are you sure you want to terminate ' + employeeName + '? This action will mark them as terminated.',
                function() {
                    $form.trigger('submit');
                }
            );
        });

        // Employee search/filter
        const $searchInput = $('#employee-search');
        const $departmentFilter = $('#department-filter');
        const $statusFilter = $('#status-filter');

        if ($searchInput.length || $departmentFilter.length || $statusFilter.length) {
            function filterEmployees() {
                const search = $searchInput.val()?.toLowerCase() || '';
                const department = $departmentFilter.val() || '';
                const status = $statusFilter.val() || '';

                $employeeTable.find('tbody tr').each(function() {
                    const $row = $(this);
                    const text = $row.text().toLowerCase();
                    const dept = $row.data('department') || '';
                    const st = $row.data('status') || '';
                    let visible = true;

                    if (search && !text.includes(search)) visible = false;
                    if (department && dept !== department) visible = false;
                    if (status && st !== status) visible = false;

                    $row.toggle(visible);
                });
            }

            $searchInput.on('keyup', filterEmployees);
            $departmentFilter.on('change', filterEmployees);
            $statusFilter.on('change', filterEmployees);
        }

        // Employee form validation
        const $employeeForm = $('#employee-form');
        if ($employeeForm.length) {
            $employeeForm.on('submit', function(e) {
                const $salary = $('#salary');
                const $email = $('#email');

                if ($salary.length && parseFloat($salary.val()) < 0) {
                    e.preventDefault();
                    window.HRM.notify.error('Salary must be a positive number.');
                    return false;
                }

                if ($email.length && !$email.val().includes('@')) {
                    e.preventDefault();
                    window.HRM.notify.error('Please enter a valid email address.');
                    return false;
                }
            });

            // Auto-generate employee ID
            const $employeeIdField = $('#employee_id');
            const $deptSelect = $('#department_id');
            if ($employeeIdField.length && $employeeIdField.val() === '') {
                $deptSelect.on('change', function() {
                    const deptId = $(this).val();
                    if (deptId) {
                        const timestamp = Date.now().toString().slice(-6);
                        $employeeIdField.val('EMP-' + deptId + '-' + timestamp);
                    }
                });
            }
        }
    });

})(jQuery);