/**
 * Chart Component
 * Chart.js wrapper for rendering charts on dashboards and reports.
 */
(function($) {
    'use strict';

    $.fn.hrmChart = function(options) {
        const settings = $.extend({
            type: 'bar',
            data: {
                labels: [],
                datasets: [],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                        },
                        ticks: {
                            font: {
                                size: 11,
                            },
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 11,
                            },
                        },
                    },
                },
            },
        }, options);

        return this.each(function() {
            const canvas = this;
            const ctx = canvas.getContext('2d');

            if (window.Chart) {
                new window.Chart(ctx, {
                    type: settings.type,
                    data: settings.data,
                    options: settings.options,
                });
            } else {
                console.warn('Chart.js library not loaded. Rendering placeholder.');
                $(canvas).after('<div class="bg-gray-100 rounded p-8 text-center text-gray-500">Chart not available</div>');
                $(canvas).hide();
            }
        });
    };

    // Auto-initialize charts on canvas elements with data-chart attribute
    $(document).ready(function() {
        $('canvas[data-chart]').each(function() {
            const $canvas = $(this);
            try {
                const options = $canvas.data('chart-options') || {};
                // Parse data attributes
                const chartType = $canvas.data('chart-type') || 'bar';
                const chartLabels = $canvas.data('chart-labels') || [];
                const chartValues = $canvas.data('chart-values') || [];

                if (chartLabels.length && chartValues.length) {
                    const mergedOptions = $.extend({
                        type: chartType,
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: $canvas.data('chart-label') || 'Data',
                                data: chartValues,
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.6)',
                                    'rgba(16, 185, 129, 0.6)',
                                    'rgba(245, 158, 11, 0.6)',
                                    'rgba(239, 68, 68, 0.6)',
                                    'rgba(139, 92, 246, 0.6)',
                                ],
                                borderColor: [
                                    'rgba(59, 130, 246, 1)',
                                    'rgba(16, 185, 129, 1)',
                                    'rgba(245, 158, 11, 1)',
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(139, 92, 246, 1)',
                                ],
                                borderWidth: 1,
                            }],
                        },
                    }, options);

                    $canvas.hrmChart(mergedOptions);
                }
            } catch (e) {
                console.error('Chart initialization error:', e);
            }
        });
    });

})(jQuery);