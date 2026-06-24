/**
 * Modal Component
 * jQuery-based modal dialog for confirmations and forms.
 */
(function($) {
    'use strict';

    $.fn.hrmModal = function(options) {
        const settings = $.extend({
            title: 'Confirm',
            message: 'Are you sure?',
            confirmText: 'Confirm',
            cancelText: 'Cancel',
            onConfirm: null,
            onCancel: null,
            closeOnOverlay: true,
        }, options);

        const modalId = 'hrm-modal-' + Date.now();
        const modal = `
            <div id="${modalId}" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                <div class="modal-overlay absolute inset-0 bg-black bg-opacity-50"></div>
                <div class="modal-content bg-white rounded-lg shadow-xl z-10 max-w-md w-full mx-4">
                    <div class="flex justify-between items-center border-b px-6 py-4">
                        <h4 class="text-lg font-semibold">${settings.title}</h4>
                        <button class="modal-close text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <div class="px-6 py-4">
                        <p>${settings.message}</p>
                    </div>
                    <div class="flex justify-end space-x-2 border-t px-6 py-4">
                        <button class="modal-cancel px-4 py-2 text-gray-700 bg-gray-100 rounded hover:bg-gray-200">${settings.cancelText}</button>
                        <button class="modal-confirm px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">${settings.confirmText}</button>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modal);
        const $modal = $('#' + modalId);

        function open() {
            $modal.removeClass('hidden');
        }

        function close() {
            $modal.addClass('hidden');
            $modal.remove();
        }

        $modal.find('.modal-overlay').on('click', function() {
            if (settings.closeOnOverlay) {
                if (settings.onCancel) settings.onCancel();
                close();
            }
        });

        $modal.find('.modal-close, .modal-cancel').on('click', function() {
            if (settings.onCancel) settings.onCancel();
            close();
        });

        $modal.find('.modal-confirm').on('click', function() {
            if (settings.onConfirm) settings.onConfirm();
            close();
        });

        open();

        return {
            open: open,
            close: close,
        };
    };

    // Global confirmation helper
    window.confirmAction = function(message, callback) {
        $(document).hrmModal({
            title: 'Confirm Action',
            message: message || 'Are you sure you want to proceed?',
            confirmText: 'Yes, proceed',
            cancelText: 'Cancel',
            onConfirm: callback,
        });
    };

})(jQuery);