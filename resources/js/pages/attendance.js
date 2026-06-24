/**
 * Attendance Page Script
 * Clock in/out handling, calendar view, and leave request management.
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize attendance DataTable
        const $attendanceTable = $('#attendance-table');
        if ($attendanceTable.length && $.fn.DataTable) {
            $attendanceTable.hrmDataTable({
                pageLength: 25,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: -1 },
                ],
            });
        }

        // Initialize leave requests DataTable
        const $leaveTable = $('#leave-requests-table');
        if ($leaveTable.length && $.fn.DataTable) {
            $leaveTable.hrmDataTable({
                pageLength: 15,
                order: [[0, 'desc']],
            });
        }

        // Clock in button
        $(document).on('click', '#clock-in-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Clocking in...');

            $.ajax({
                url: $btn.data('url') || '/attendance/clock-in',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    window.HRM.notify.success(response.message || 'Clocked in successfully!');
                    $btn.replaceWith(`
                        <button id="clock-out-btn"
                                data-url="/attendance/clock-out"
                                class="bg-red-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-red-700 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>Clock Out
                        </button>
                    `);
                    // Update status
                    $('#attendance-status').html('<span class="text-green-600 font-semibold"><i class="fas fa-check-circle mr-1"></i>Clocked In</span>');
                    $('#clock-time').text('In: ' + (response.time || new Date().toLocaleTimeString()));
                    $('#clock-date').text(new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }));
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt mr-2"></i>Clock In');
                    const msg = xhr.responseJSON?.message || 'Failed to clock in. Please try again.';
                    window.HRM.notify.error(msg);
                },
            });
        });

        // Clock out button
        $(document).on('click', '#clock-out-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Clocking out...');

            $.ajax({
                url: $btn.data('url') || '/attendance/clock-out',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    window.HRM.notify.success(response.message || 'Clocked out successfully!');
                    $btn.replaceWith(`
                        <button id="clock-in-btn"
                                data-url="/attendance/clock-in"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-blue-700 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Clock In
                        </button>
                    `);
                    $('#attendance-status').html('<span class="text-gray-600"><i class="fas fa-clock mr-1"></i>Not Clocked In</span>');
                    if (response.clock_out) {
                        $('#clock-time').append(' | Out: ' + response.clock_out);
                    }
                    if (response.hours_worked) {
                        $('#hours-worked').text('Hours: ' + response.hours_worked);
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-sign-out-alt mr-2"></i>Clock Out');
                    const msg = xhr.responseJSON?.message || 'Failed to clock out. Please try again.';
                    window.HRM.notify.error(msg);
                },
            });
        });

        // Leave request form
        const $leaveForm = $('#leave-request-form');
        if ($leaveForm.length) {
            // Date range validation
            const $startDate = $('#start_date');
            const $endDate = $('#end_date');
            const $typeSelect = $('#type');

            $startDate.on('change', function() {
                $endDate.attr('min', $(this).val());
                calculateLeaveDays();
            });
            $endDate.on('change', calculateLeaveDays);

            function calculateLeaveDays() {
                const start = new Date($startDate.val());
                const end = new Date($endDate.val());
                if (start && end && !isNaN(start) && !isNaN(end) && end >= start) {
                    let days = 0;
                    const current = new Date(start);
                    while (current <= end) {
                        const day = current.getDay();
                        if (day !== 0 && day !== 6) days++; // Skip weekends
                        current.setDate(current.getDate() + 1);
                    }
                    $('#leave-days-count').text(days > 0 ? days + ' working day(s)' : 'Select valid dates');
                } else {
                    $('#leave-days-count').text('Select valid dates');
                }
            }

            $leaveForm.on('submit', function(e) {
                const start = $startDate.val();
                const end = $endDate.val();
                const type = $typeSelect.val();

                if (!start || !end) {
                    e.preventDefault();
                    window.HRM.notify.error('Please select start and end dates.');
                    return false;
                }
                if (new Date(end) < new Date(start)) {
                    e.preventDefault();
                    window.HRM.notify.error('End date must be on or after start date.');
                    return false;
                }
                if (!type) {
                    e.preventDefault();
                    window.HRM.notify.error('Please select a leave type.');
                    return false;
                }
            });
        }

        // Approve/Reject leave request
        $(document).on('click', '.approve-leave, .reject-leave', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const action = $btn.hasClass('approve-leave') ? 'approve' : 'reject';
            const leaveId = $btn.data('leave-id');
            const employeeName = $btn.data('employee') || 'this request';

            window.confirmAction(
                'Are you sure you want to ' + action + ' the leave request for ' + employeeName + '?',
                function() {
                    const $form = $('<form method="POST" action="/leave/' + leaveId + '/' + action + '"></form>');
                    $form.append('<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">');
                    $('body').append($form);
                    $form.trigger('submit');
                }
            );
        });

        // Attendance calendar navigation
        $(document).on('click', '#calendar-prev, #calendar-next', function() {
            const $btn = $(this);
            const month = $btn.data('month');
            const year = $btn.data('year');
            if (month && year) {
                window.location.href = '?month=' + month + '&year=' + year;
            }
        });
    });

})(jQuery);