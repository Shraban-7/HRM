<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Command Palette Modal -->
<div id="commandPaletteModal" class="fixed inset-0 z-50 bg-gray-950/70 backdrop-blur-sm hidden flex items-start justify-center pt-20 p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-200 overflow-hidden transform transition-all">
        <div class="p-4 border-b border-gray-200 flex items-center space-x-3 bg-gray-50">
            <i class="fas fa-search text-gray-400 text-lg"></i>
            <input type="text" id="commandInput" placeholder="Quick search modules, employees, actions... (Esc to close)" class="w-full bg-transparent border-none text-sm text-gray-900 focus:outline-none placeholder-gray-400" autofocus>
            <span class="text-[10px] font-mono text-gray-400 bg-gray-200 px-1.5 py-0.5 rounded">ESC</span>
        </div>
        <div id="commandResults" class="max-h-80 overflow-y-auto p-2 text-xs space-y-1">
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider px-3 py-1">Quick Navigation</div>
            <a href="{{ route('employees.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-users w-4 text-center text-gray-400"></i>
                <span>Employee Directory</span>
            </a>
            <a href="{{ route('payrolls.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-calculator w-4 text-center text-gray-400"></i>
                <span>Payroll Processing</span>
            </a>
            <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-fingerprint w-4 text-center text-gray-400"></i>
                <span>Time & Attendance</span>
            </a>
            <a href="{{ route('leave.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-umbrella-beach w-4 text-center text-gray-400"></i>
                <span>Leave Management</span>
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-laptop w-4 text-center text-gray-400"></i>
                <span>Hardware Assets</span>
            </a>
            <a href="{{ route('documents.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-folder w-4 text-center text-gray-400"></i>
                <span>Digital Documents</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-receipt w-4 text-center text-gray-400"></i>
                <span>Expense Claims</span>
            </a>
            <a href="{{ route('loans.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-hand-holding-usd w-4 text-center text-gray-400"></i>
                <span>Loans & Advances</span>
            </a>
            <a href="{{ route('helpdesk.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-headset w-4 text-center text-gray-400"></i>
                <span>Help Desk Tickets</span>
            </a>
            <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 text-gray-700 font-medium">
                <i class="fas fa-sliders-h w-4 text-center text-gray-400"></i>
                <span>Company Settings & White-Labeling</span>
            </a>
        </div>
    </div>
</div>

<script>
    function openCommandPalette() {
        $('#commandPaletteModal').removeClass('hidden');
        $('#commandInput').val('').focus();
    }

    function closeCommandPalette() {
        $('#commandPaletteModal').addClass('hidden');
    }

    $(document).keydown(function(e) {
        // Ctrl+K or Cmd+K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if ($('#commandPaletteModal').hasClass('hidden')) {
                openCommandPalette();
            } else {
                closeCommandPalette();
            }
        }
        // Esc to close
        if (e.key === 'Escape' && !$('#commandPaletteModal').hasClass('hidden')) {
            closeCommandPalette();
        }
    });

    $('#commandPaletteModal').click(function(e) {
        if (e.target === this) {
            closeCommandPalette();
        }
    });

    $('#commandInput').on('keyup', function() {
        var term = $(this).val().toLowerCase();
        $('#commandResults a').each(function() {
            var text = $(this).text().toLowerCase();
            if (text.indexOf(term) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(document).ready(function () {
        function refreshNotificationBadge() {
            $.get('{{ route('notifications.unread-count') }}', function (data) {
                var count = data.count || 0;
                var $badge = $('#notificationBadge');
                if (count > 0) {
                    $badge.text(count > 99 ? '99+' : count).removeClass('hidden');
                } else {
                    $badge.addClass('hidden');
                }
            }).fail(function () { });
        }
        refreshNotificationBadge();
        setInterval(refreshNotificationBadge, 60000);
    });
</script>
@stack('scripts')
