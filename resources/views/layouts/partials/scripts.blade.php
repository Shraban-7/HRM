<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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