/**
 * Payroll Page Script
 * Payroll calculation, payslip viewing, and DataTable initialization.
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize payroll DataTable
        const $payrollTable = $('#payrolls-table');
        if ($payrollTable.length && $.fn.DataTable) {
            $payrollTable.hrmDataTable({
                pageLength: 15,
                order: [[0, 'desc']],
            });
        }

        // Initialize payslips DataTable
        const $payslipsTable = $('#payslips-table');
        if ($payslipsTable.length && $.fn.DataTable) {
            $payslipsTable.hrmDataTable({
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: -1 },
                ],
            });
        }

        // Payroll calculation confirmation
        $(document).on('click', '#calculate-payroll', function(e) {
            e.preventDefault();
            const $form = $(this).closest('form');
            const periodLabel = $(this).data('period') || 'this period';

            window.confirmAction(
                'Calculate payroll for ' + periodLabel + '? This will generate payslips for all active employees.',
                function() {
                    $form.trigger('submit');
                }
            );
        });

        // Filter payslips by employee
        const $employeeFilter = $('#employee-filter');
        const $statusFilter = $('#status-filter');

        if ($employeeFilter.length || $statusFilter.length) {
            function filterPayslips() {
                const employeeId = $employeeFilter.val() || '';
                const status = $statusFilter.val() || '';

                $payslipsTable.find('tbody tr').each(function() {
                    const $row = $(this);
                    const emp = $row.data('employee-id') || '';
                    const st = $row.data('status') || '';
                    let visible = true;

                    if (employeeId && emp !== employeeId) visible = false;
                    if (status && st !== status) visible = false;

                    $row.toggle(visible);
                });
            }

            $employeeFilter.on('change', filterPayslips);
            $statusFilter.on('change', filterPayslips);
        }

        // Tax calculation form
        const $taxForm = $('#tax-calculator-form');
        if ($taxForm.length) {
            $taxForm.on('submit', function(e) {
                e.preventDefault();
                const grossAmount = parseFloat($('#gross-amount').val()) || 0;
                if (grossAmount <= 0) {
                    window.HRM.notify.error('Please enter a valid gross amount.');
                    return;
                }

                // Show loading
                $('#tax-result').addClass('hidden');
                $('#tax-loading').removeClass('hidden');

                $.ajax({
                    url: $taxForm.attr('action') || '/tax/calculate',
                    method: 'POST',
                    data: $taxForm.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        $('#tax-loading').addClass('hidden');
                        $('#tax-result').removeClass('hidden');
                        $('#tax-amount').text(response.tax?.toFixed(2) || '0.00');
                        $('#effective-rate').text(response.effective_rate?.toFixed(2) || '0.00');
                        $('#net-amount').text(response.net?.toFixed(2) || '0.00');
                    },
                    error: function(xhr) {
                        $('#tax-loading').addClass('hidden');
                        const msg = xhr.responseJSON?.message || 'An error occurred during calculation.';
                        window.HRM.notify.error(msg);
                    },
                });
            });
        }

        // Payroll period date range validation
        const $periodStart = $('#period_start');
        const $periodEnd = $('#period_end');
        if ($periodStart.length && $periodEnd.length) {
            $periodStart.on('change', function() {
                $periodEnd.attr('min', $(this).val());
            });
            $periodEnd.on('change', function() {
                if ($periodStart.val() && $(this).val() <= $periodStart.val()) {
                    window.HRM.notify.warning('End date must be after start date.');
                }
            });
        }
    });

})(jQuery);