/**
 * Notification Component
 * Toast notification system for displaying success, error, warning, and info messages.
 */
(function($) {
    'use strict';

    const NotificationManager = {
        container: null,

        init: function() {
            if (!this.container) {
                this.container = $('<div id="hrm-notifications" class="fixed top-4 right-4 z-[9999] space-y-2"></div>');
                $('body').append(this.container);
            }
        },

        show: function(message, type, duration) {
            this.init();

            type = type || 'info';
            duration = duration || 5000;

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle',
            };

            const colors = {
                success: 'bg-green-50 border-green-400 text-green-800',
                error: 'bg-red-50 border-red-400 text-red-800',
                warning: 'bg-yellow-50 border-yellow-400 text-yellow-800',
                info: 'bg-blue-50 border-blue-400 text-blue-800',
            };

            const iconColors = {
                success: 'text-green-500',
                error: 'text-red-500',
                warning: 'text-yellow-500',
                info: 'text-blue-500',
            };

            const toastId = 'hrm-toast-' + Date.now();
            const toast = `
                <div id="${toastId}" class="toast-item flex items-start border-l-4 ${colors[type] || colors.info} p-4 rounded shadow-lg transition-all duration-300 transform translate-x-full opacity-0 max-w-sm">
                    <i class="fas ${icons[type] || icons.info} ${iconColors[type] || iconColors.info} mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm">${message}</p>
                    </div>
                    <button class="toast-close ml-3 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            this.container.append(toast);
            const $toast = $('#' + toastId);

            // Animate in
            setTimeout(function() {
                $toast.removeClass('translate-x-full opacity-0');
            }, 50);

            // Auto dismiss
            if (duration > 0) {
                setTimeout(function() {
                    $toast.addClass('translate-x-full opacity-0');
                    setTimeout(function() { $toast.remove(); }, 300);
                }, duration);
            }

            // Close button
            $toast.find('.toast-close').on('click', function() {
                $toast.addClass('translate-x-full opacity-0');
                setTimeout(function() { $toast.remove(); }, 300);
            });
        },

        success: function(message, duration) {
            this.show(message, 'success', duration);
        },

        error: function(message, duration) {
            this.show(message, 'error', duration);
        },

        warning: function(message, duration) {
            this.show(message, 'warning', duration);
        },

        info: function(message, duration) {
            this.show(message, 'info', duration);
        },
    };

    // Expose global notification API
    window.HRM = window.HRM || {};
    window.HRM.notify = function(message, type, duration) {
        NotificationManager.show(message, type, duration);
    };
    window.HRM.notify.success = function(message, duration) {
        NotificationManager.success(message, duration);
    };
    window.HRM.notify.error = function(message, duration) {
        NotificationManager.error(message, duration);
    };
    window.HRM.notify.warning = function(message, duration) {
        NotificationManager.warning(message, duration);
    };
    window.HRM.notify.info = function(message, duration) {
        NotificationManager.info(message, duration);
    };

    // Auto-display flash messages from session
    $(document).ready(function() {
        // Check for meta tag with flash data
        const $flash = $('meta[name="flash-message"]');
        if ($flash.length) {
            const type = $flash.data('type') || 'info';
            const message = $flash.data('message') || '';
            if (message) {
                NotificationManager.show(message, type, 6000);
            }
        }

        // Check for Laravel session flashes in data attributes on body
        const $body = $('body');
        const flashSuccess = $body.data('flash-success');
        const flashError = $body.data('flash-error');
        const flashInfo = $body.data('flash-info');

        if (flashSuccess) NotificationManager.success(flashSuccess);
        if (flashError) NotificationManager.error(flashError);
        if (flashInfo) NotificationManager.info(flashInfo);
    });

})(jQuery);