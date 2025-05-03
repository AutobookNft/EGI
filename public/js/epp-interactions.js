// public/js/epp-interactions.js

/**
 * EPP Dashboard Interactions Module
 *
 * Handles all interactions and dynamic visualizations for the Environment Protection Programs
 * dashboard, including charts, filters, and real-time data updates.
 *
 * @since 1.0.0
 * @requires Chart.js
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all charts and visualizations
    initializeCharts();

    // Set up filter event listeners
    setupFilters();

    // Initialize any interactive elements
    initializeInteractions();
});

/**
 * Initializes all charts on the dashboard using Chart.js
 */
function initializeCharts() {
    initializeDistributionChart();
    initializeImpactGrowthChart();
    initializeProgramSpecificCharts();
}

/**
 * Creates the distribution chart showing contribution allocation across program types
 */
function initializeDistributionChart() {
    const distributionCtx = document.getElementById('distribution-chart');

    if (!distributionCtx) return;

    // Get distribution data from the data-* attributes or fetch from API
    const distributionData = getChartData('distribution');

    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: distributionData.labels,
            datasets: [{
                data: distributionData.values,
                backgroundColor: [
                    '#4caf50', // ARF - Green
                    '#2196f3', // APR - Blue
                    '#ffc107'  // BPE - Yellow
                ],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: €${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Creates the impact growth chart showing progress over time
 */
function initializeImpactGrowthChart() {
    const growthCtx = document.getElementById('impact-growth-chart');

    if (!growthCtx) return;

    // Get growth data from the data-* attributes or fetch from API
    const growthData = getChartData('growth');

    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: growthData.labels,
            datasets: [
                {
                    label: 'ARF Impact',
                    data: growthData.datasets[0].data,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'APR Impact',
                    data: growthData.datasets[1].data,
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'BPE Impact',
                    data: growthData.datasets[2].data,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${value}% impact increase`;
                        }
                    }
                }
            }
        }
    });
 }

    /**
     * Initializes program-specific progress charts for ARF, APR, and BPE
     */
    function initializeProgramSpecificCharts() {
        initializeArfChart();
        initializeAprChart();
        initializeBpeChart();
    }

    /**
     * Creates the ARF-specific progress chart
     */
    function initializeArfChart() {
        const arfCtx = document.getElementById('arf-progress-chart');

        if (!arfCtx) return;

        // Get ARF-specific data
        const arfData = getChartData('arf');

        new Chart(arfCtx, {
            type: 'bar',
            data: {
                labels: arfData.labels,
                datasets: [{
                    label: 'Trees Planted',
                    data: arfData.datasets[0].data,
                    backgroundColor: 'rgba(76, 175, 80, 0.7)',
                    borderColor: '#4caf50',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Trees'
                        }
                    }
                }
            }
        });
    }

    /**
     * Creates the APR-specific progress chart
     */
    function initializeAprChart() {
        const aprCtx = document.getElementById('apr-progress-chart');

        if (!aprCtx) return;

        // Get APR-specific data
        const aprData = getChartData('apr');

        new Chart(aprCtx, {
            type: 'bar',
            data: {
                labels: aprData.labels,
                datasets: [{
                    label: 'Plastic Removed (tons)',
                    data: aprData.datasets[0].data,
                    backgroundColor: 'rgba(33, 150, 243, 0.7)',
                    borderColor: '#2196f3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Tons of Plastic'
                        }
                    }
                }
            }
        });
    }

    /**
     * Creates the BPE-specific progress chart
     */
    function initializeBpeChart() {
        const bpeCtx = document.getElementById('bpe-progress-chart');

        if (!bpeCtx) return;

        // Get BPE-specific data
        const bpeData = getChartData('bpe');

        new Chart(bpeCtx, {
            type: 'bar',
            data: {
                labels: bpeData.labels,
                datasets: [{
                    label: 'Hives Created',
                    data: bpeData.datasets[0].data,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: '#ffc107',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Hives'
                        }
                    }
                }
            }
        });
    }

    /**
     * Retrieves chart data either from data attributes or via API call
     * @param {string} chartType - The type of chart to fetch data for
     * @returns {Object} Chart data containing labels and datasets
     */
    function getChartData(chartType) {
        // Check if the data is embedded in the page via data attributes
        const dataElement = document.getElementById('epp-dashboard-data');

        if (dataElement && dataElement.dataset[chartType + 'Data']) {
            return JSON.parse(dataElement.dataset[chartType + 'Data']);
        }

        // Otherwise return dummy data for now and fetch real data via AJAX
        // In a production environment, this would be replaced with actual API calls

        switch(chartType) {
            case 'distribution':
                return {
                    labels: ['ARF (Reforestation)', 'APR (Ocean Cleanup)', 'BPE (Bee Protection)'],
                    values: [45000, 32000, 28000]
                };
            case 'growth':
                return {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        { data: [10, 15, 20, 25, 30, 35] },
                        { data: [5, 10, 15, 20, 25, 30] },
                        { data: [8, 12, 18, 22, 28, 32] }
                    ]
                };
            case 'arf':
                return {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        { data: [500, 700, 900, 1200, 1500, 1800] }
                    ]
                };
            case 'apr':
                return {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        { data: [10, 15, 22, 28, 35, 42] }
                    ]
                };
            case 'bpe':
                return {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        { data: [25, 40, 60, 85, 110, 140] }
                    ]
                };
            default:
                return { labels: [], datasets: [{ data: [] }] };
        }
    }

    /**
     * Sets up event listeners for filter changes
     */
    function setupFilters() {
        const typeFilter = document.getElementById('epp-type-filter');
        const timeFilter = document.getElementById('time-period-filter');
        const orderFilter = document.getElementById('metrics-order');

        if (typeFilter) {
            typeFilter.addEventListener('change', function() {
                applyFilters();
            });
        }

        if (timeFilter) {
            timeFilter.addEventListener('change', function() {
                applyFilters();
            });
        }

        if (orderFilter) {
            orderFilter.addEventListener('change', function() {
                applyFilters();
            });
        }
    }

    /**
     * Applies selected filters and updates the dashboard
     */
    function applyFilters() {
        const typeFilter = document.getElementById('epp-type-filter');
        const timeFilter = document.getElementById('time-period-filter');
        const orderFilter = document.getElementById('metrics-order');

        const typeValue = typeFilter ? typeFilter.value : 'all';
        const timeValue = timeFilter ? timeFilter.value : 'all';
        const orderValue = orderFilter ? orderFilter.value : 'contribution-desc';

        // Show/hide impact sections based on type filter
        const impactSections = document.querySelectorAll('.impact-type-container');

        impactSections.forEach(section => {
            if (typeValue === 'all' || section.id.includes(typeValue.toLowerCase())) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        // Fetch updated data based on filters - in production, this would make AJAX calls
        fetchFilteredData(typeValue, timeValue, orderValue);
    }

    function processMilestones(milestones) {
        return milestones.map(milestone => {
            // Format the completion date
            if (milestone.completion_date) {
                milestone.formattedDate = new Date(milestone.completion_date)
                    .toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });
            } else {
                milestone.formattedDate = 'Pending';
            }

            return milestone;
        });
    }

    /**
     * Fetches filtered data from the server and updates UI
     * @param {string} type - The program type filter value
     * @param {string} time - The time period filter value
     * @param {string} order - The sort order filter value
     */
    function fetchFilteredData(type, time, order) {
        // In a production environment, this would make an AJAX call
        console.log(`Fetching filtered data: type=${type}, time=${time}, order=${order}`);

        // Prepare the request URL
        let url = '/api/epps/dashboard-data';
        url += `?type=${type}&time=${time}&order=${order}`;

        // Show loading state
        showLoadingState();

        // Use fetch API to get data
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update charts with new data
                updateChartsWithData(data);

                // Hide loading indicators
                hideLoadingState();
            })
            .catch(error => {
                console.error('Error fetching filtered data:', error);
                hideLoadingState();

                // Show error message to user
                showErrorMessage('Could not load dashboard data. Please try again later.');
            });
    }

    function showErrorMessage(message) {
        const dashboardContainer = document.querySelector('.epp-dashboard-container');

        if (dashboardContainer) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'dashboard-error-message';
            errorDiv.textContent = message;

            // Add the error message to the top of the dashboard
            dashboardContainer.insertBefore(errorDiv, dashboardContainer.firstChild);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 5000);
        }
    }


    /**
     * Shows loading indicators while data is being fetched
     */
    function showLoadingState() {
        const charts = document.querySelectorAll('canvas');

        charts.forEach(chart => {
            chart.classList.add('loading');
            chart.parentElement.insertAdjacentHTML('beforeend', '<div class="loading-overlay"><div class="spinner"></div></div>');
        });
    }

    /**
     * Hides loading indicators after data is fetched
     */
    function hideLoadingState() {
        const loadingOverlays = document.querySelectorAll('.loading-overlay');
        const charts = document.querySelectorAll('canvas.loading');

        loadingOverlays.forEach(overlay => {
            overlay.remove();
        });

        charts.forEach(chart => {
            chart.classList.remove('loading');
        });
    }

    /**
     * Updates all charts with newly filtered data
     */
    function updateChartsWithFilteredData() {
        // In a production environment, this would use real data from AJAX responses
        // For demo purposes, we'll just destroy and reinitialize the charts
        const chartElements = document.querySelectorAll('canvas');

        chartElements.forEach(canvas => {
            // Get the chart instance from Chart.js
            const chartInstance = Chart.getChart(canvas);

            // Destroy the existing chart
            if (chartInstance) {
                chartInstance.destroy();
            }
        });

        // Reinitialize all charts
        initializeCharts();
    }

    /**
     * Initializes any interactive elements in the dashboard
     */
    function initializeInteractions() {
        // Add smooth scrolling to links
        const links = document.querySelectorAll('a[href^="#"]');

        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add hover effects for project items
        const projectItems = document.querySelectorAll('.project-item');

        projectItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.classList.add('hover');
            });

            item.addEventListener('mouseleave', function() {
                this.classList.remove('hover');
            });
        });
    }
