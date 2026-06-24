/**
 * DataTable Component
 * jQuery DataTables wrapper for server-side paginated tables.
 */
(function($) {
    'use strict';

    $.fn.hrmDataTable = function(options) {
        const settings = $.extend({
            pageLength: 15,
            ordering: true,
            searching: true,
            processing: true,
            serverSide: false,
            ajax: null,
            columns: [],
            dom: '<"flex justify-between items-center mb-4"<"flex items-center space-x-2"l><"flex items-center"f>>rt<"flex justify-between items-center mt-4"<"text-sm text-gray-600"i><"flex items-center"p>>',
            language: {
                search: '<i class="fas fa-search mr-2"></i>',
                searchPlaceholder: 'Search...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total entries)',
                zeroRecords: 'No matching records found',
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                },
            },
            initComplete: function() {
                // Style the search input
                $(this).closest('.dataTables_wrapper').find('input[type="search"]')
                    .addClass('border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500')
                    .attr('placeholder', settings.language.searchPlaceholder || 'Search...');

                // Style the length select
                $(this).closest('.dataTables_wrapper').find('select')
                    .addClass('border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500');

                // Style pagination buttons
                $(this).closest('.dataTables_wrapper').find('.paginate_button')
                    .addClass('px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-100 mx-0.5');
                $(this).closest('.dataTables_wrapper').find('.paginate_button.current')
                    .addClass('bg-blue-600 text-white border-blue-600 hover:bg-blue-700');
                $(this).closest('.dataTables_wrapper').find('.paginate_button.disabled')
                    .addClass('text-gray-400 cursor-not-allowed hover:bg-transparent');
            },
            drawCallback: function() {
                // Re-style pagination after each draw
                $(this).closest('.dataTables_wrapper').find('.paginate_button')
                    .addClass('px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-100 mx-0.5');
                $(this).closest('.dataTables_wrapper').find('.paginate_button.current')
                    .addClass('bg-blue-600 text-white border-blue-600 hover:bg-blue-700');
            },
        }, options);

        return this.each(function() {
            if ($.fn.DataTable) {
                $(this).DataTable(settings);
            } else {
                console.warn('DataTables library not loaded. Using basic table display.');
            }
        });
    };

    // Auto-initialize tables with datatable class
    $(document).ready(function() {
        $('table.datatable').each(function() {
            const $table = $(this);
            const options = $table.data('datatable-options');
            $table.hrmDataTable(options || {});
        });
    });

})(jQuery);