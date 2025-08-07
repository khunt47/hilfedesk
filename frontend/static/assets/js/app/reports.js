var app = new Vue({
    el: '#ticketMetricsApp',
    data: {
    apiBaseUrl: '',
    userToken: '',
    showLoading: 'yes',
    fromDate: '',
    toDate: '',
    pieChartInstance: null,
    workload: [],
    metrics: {
        total: 0,
        counts: {}
    },
    },
    mounted: function() {
        this.autoLoad();
        this.getTicketMetrics();
        this.fetchAgentWorkload();
        this.fetchTicketTrends();
    },
    methods: {
        autoLoad: function() {
            var self = this;
            self.apiBaseUrl = self.$refs.api_base_url.value;
            self.userToken  = localStorage.getItem("hd_user_token");
        },
        getTicketMetrics: function() { 
            var self = this
            var url = self.apiBaseUrl + '/reports/ticket-metrics' + '?fromDate=' + self.fromDate +'&toDate=' + self.toDate

            axios.get(url, {
                headers: {
                    "Authorization": 'Bearer ' + self.userToken 
                }
            })
            .then(function(response) {
                if (response.data.success) {
                    self.metrics = response.data.data;
                    self.ticketsChart();
                    self.ticketTrends();
                } else {
                    self.metrics = { total: 0, counts: {} };
                }
                self.showLoading = 'no';
            })
            .catch(function (error) {
                self.metrics = { total: 0, counts: {} };
                self.showLoading = 'no';
                return false;
            });
        },
        fetchAgentWorkload() {
            var self = this
            var url = self.apiBaseUrl + '/reports/agent-workload' + '?fromDate=' + self.fromDate + '&toDate=' + self.toDate
            
            axios.get(url, {
                headers: {
                    "Authorization": 'Bearer ' + self.userToken 
                }
            })
            .then(function(response) {
                if (response.data.success) {
                    self.workload = response.data.data;
                } else {
                    self.workload = [];
                }
                self.showLoading = 'no';
            })
            .catch(function(error) {
                self.workload = [];
                self.showLoading = 'no';
                return false;
            })
        },
        fetchTicketTrends() {
            var self = this
            var url = self.apiBaseUrl + '/reports/ticket-trends' + '?fromDate=' + self.fromDate + '&toDate=' + self.toDate;
            
            axios.get(url, {
                headers: {
                    "Authorization": 'Bearer ' + self.userToken 
                }
            })
            .then(function(response) {
                if (response.data.success) {
                    self.showLoading = 'no';
                    const trend = {};
                    response.data.data.forEach(t => {
                        trend[t.date] = t.count;
                    });

                    const labels = [];
                    const counts = [];

                    for (let i = 6; i >= 0; i--) {
                        const d = new Date();
                        d.setDate(d.getDate() - i);
                        const dateStr = d.toISOString().slice(0, 10); 

                        labels.push(dateStr);
                        counts.push(trend[dateStr] || 0);
                    }
                    self.ticketTrends(labels, counts);
                } else {
                    $.notify(response.data.error, "error");
                    return false;
                }
                self.showLoading = 'no';
            })
            .catch(function (error) {
                $.notify(response.data.error, "error");
                self.showLoading = 'no';
                return false;
            })
        },
        applyFilter() {
            var self = this;
            
            if (!self.fromDate || self.fromDate.trim() === '') {
                $.notify('From date should not be empty', "error");
                return false;
            }
            if (!self.toDate || self.toDate.trim() === '') {
                $.notify('To date should not be empty', "error");
                return false;
            }
            
            const fromDate = new Date(self.fromDate);
            const toDate = new Date(self.toDate);

            if (fromDate > toDate) {
                $.notify('From date should not be greater than to date', "error");
                return false;
            } 

            self.showLoading = 'yes';

            self.getTicketMetrics();
            self.fetchAgentWorkload();
            self.fetchTicketTrends();
        },
        ticketsChart() {
        const ctx = document.getElementById('ticketsChart').getContext('2d');

        if (!ctx) {
            console.error("Canvas element with ID 'ticketsChart' not found.");
            return false;
        }

        if (this.pieChartInstance) {
            this.pieChartInstance.destroy();
        }

        const chartData = [
            this.metrics.counts['new'] || 0,
            this.metrics.counts['inprogress'] || 0,
            this.metrics.counts['onhold'] || 0,
            this.metrics.counts['resolved'] || 0,
            this.metrics.counts['deleted'] || 0
        ];

        this.pieChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
            labels: ['New', 'In Progress', 'Onhold', 'Resolved', 'Deleted'],
            datasets: [{
                label: 'Ticket Status',
                data: chartData,
                pointRadius: 0,
                pointHoverRadius: 0,
                backgroundColor: [
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(43, 210, 60, 0.65)',
                'rgba(65, 89, 226, 0.65)',
                'rgba(255, 99, 132, 0.7)'
                ],
                borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(43, 210, 60, 0.65)',
                'rgba(65, 89, 226, 0.65)',
                'rgba(255, 99, 132, 0.7)'
                ],
                borderWidth: 1
            }]
            },
            options: {
                    responsive: true,

                    plugins: {
                        legend: { position: 'bottom' },

                        title: {
                        display: true,
                        text: 'Ticket Status Distribution'
                        }
                    },

                    pieceLabel: {
                    render: 'percentage',
                    fontColor: ['white'],
                    precision: 2
                    },

                    tooltips: {
                    enabled: true
                    },

                    scales: {
                    yAxes: [{

                        ticks: {
                        display: false
                        },
                        gridLines: {
                        drawBorder: false,
                        zeroLineColor: "transparent",
                        color: 'rgba(255,255,255,0.05)'
                        }

                    }],

                    xAxes: [{
                        barPercentage: 1.6,
                        gridLines: {
                        drawBorder: false,
                        color: 'rgba(255,255,255,0.1)',
                        zeroLineColor: "transparent"
                        },
                        ticks: {
                        display: false,
                        }
                    }]
                    },
                }
        });
        },
        ticketTrends(labels, counts) {
            const ctx = document.getElementById('ticketTrends').getContext('2d');

        if (!ctx) {
            console.error("Canvas element with ID 'ticketsChart' not found.");
            return;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
            labels: labels,
            datasets: [{
                label: 'Ticket Trends',
                data: counts,
                pointRadius: 4,
                pointHoverRadius: 8,
                pointBackgroundColor: 'rgba(10, 210, 255, 0.7)',
                pointBorderColor:'rgba(10, 210, 255, 0.7)',
                backgroundColor: [
                'rgba(75, 190, 192, 0.94)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(43, 210, 60, 0.65)'
                ],
                borderColor: [
                'rgba(75, 190, 192, 0.94)',
                'rgba(255, 206, 86, 1)',
                'rgba(43, 210, 60, 0.65)'
                ],
                borderWidth: 4,
                tension: 0.1,
                fill: false,
            }]
            },
            options: {
                    responsive: true,

                    plugins: {
                        legend: { position: 'bottom' },

                        title: {
                        display: true,
                        text: 'Ticket Status Distribution'
                        }
                    },

                
                    point: {
                        hoverRadius: 15,
                    },

                    pieceLabel: {
                    render: 'percentage',
                    fontColor: ['white'],
                    precision: 2
                    },

                    tooltips: {
                    enabled: true
                    },

                    scales: {
                    yAxes: [{

                        scaleLabel: {
                        display: true,
                        labelString: 'Tickets Count'   
                        },

                        gridLines: {
                        drawBorder: false,
                        }

                    }],

                    xAxes: [{

                        scaleLabel: {
                        display: true,
                        labelString: 'Tickets per Day'   
                        },

                        barPercentage: 1.6,
                        gridLines: {
                        drawBorder: true,
                        },

                        ticks: {
                        display: true,
                        }
                    }]
                    },
                }
        });
        }
    }
});