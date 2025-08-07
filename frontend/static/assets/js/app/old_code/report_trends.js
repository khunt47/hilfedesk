var app = new Vue({
	el: '#dashboard-cards', 
	data: {
        submittedFeedbackCount: 0, 
        occupiedRRoomsCount: 0, 
        guestScore: 0, 
        negativeScore: 0, 
        fromDt: '', 
        toDt: '', 
        roomPositiveVal: 0, 
        roomAverageVal: 0, 
        roomNegativeVal: 0, 
        servicePositiveVal: 0, 
        serviceAverageVal: 0, 
        serviceNegativeVal: 0, 
        foodPositiveVal: 0, 
        foodAverageVal: 0, 
        foodNegativeVal: 0
	}, 
	mounted: function() {
       this.getDashboardCards()
	}, 
	methods: {
        getDashboardCards: function() {
            var self = this

            var url = 'api/v1/dashboard/cards'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.submittedFeedbackCount = response.data.feedback_count
                    self.occupiedRRoomsCount    = response.data.occupied_rooms
                    self.guestScore             = response.data.guest_score
                    self.negativeScore          = response.data.negative_score

                    self.roomPositiveVal        = response.data.room_positive
                    self.roomAverageVal         = response.data.room_average
                    self.roomNegativeVal        = response.data.room_negative

                    self.servicePositiveVal     = response.data.service_positive
                    self.serviceAverageVal      = response.data.service_average
                    self.serviceNegativeVal     = response.data.service_negative
                   
                    self.foodPositiveVal        = response.data.food_positive
                    self.foodAverageVal         = response.data.food_average
                    self.foodNegativeVal        = response.data.food_negative

                    self.roomChart()
                    self.serviceChart()
                    self.foodChart()
                }
                else 
                {
                    self.submittedFeedbackCount = 0
                    self.occupiedRRoomsCount    = 0
                    self.guestScore             = 0
                    self.negativeScore          = 0
                    self.roomPositiveVal        = 0
                    self.roomAverageVal         = 0
                    self.roomNegativeVal        = 0
                    self.servicePositiveVal     = 0
                    self.serviceAverageVal      = 0
                    self.serviceNegativeVal     = 0
                    self.foodPositiveVal        = 0
                    self.foodAverageVal         = 0
                    self.foodNegativeVal        = 0

                    self.roomChart()
                    self.serviceChart()
                    self.foodChart()
                    
                }
            })
            .catch(function (error) {
                self.totalFeedbackCount = 0
                self.allFeedbacks = []
                console.log('error')
            })
        }, 
        roomChart: function() {
            var self = this

            ctx = document.getElementById('chartRoom').getContext("2d");

               // var roomPositive = $("#roomPositive").val();
               // var roomAverage  = $("#roomAverage").val();
               // var roomNegative = $("#roomNegative").val();

                myChart = new Chart(ctx, {
                  type: 'pie',
                  data: {
                    labels: [1, 2, 3],
                    datasets: [{
                      label: "Emails",
                      pointRadius: 0,
                      pointHoverRadius: 0,
                      backgroundColor: [
                        // '#e3e3e3',
                        '#599E0F',
                        '#fcc468',
                        '#ef8157'
                      ],
                      borderWidth: 0,
                      data: [self.roomPositiveVal, self.roomAverageVal, self.roomNegativeVal]
                    }]
                  },

                  options: {

                    legend: {
                      display: false
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
        serviceChart: function() {
            var self = this

            ctx = document.getElementById('chartService').getContext("2d");

            // var servicePositive = $("#servicePositive").val();
            // var serviceAverage  = $("#serviceAverage").val();
            // var serviceNegative = $("#serviceNegative").val();

            myChart = new Chart(ctx, {
              type: 'pie',
              data: {
                labels: [1, 2, 3],
                datasets: [{
                  label: "Emails",
                  pointRadius: 0,
                  pointHoverRadius: 0,
                  backgroundColor: [
                    // '#e3e3e3',
                    '#599E0F',
                    '#fcc468',
                    '#ef8157'
                  ],
                  borderWidth: 0,
                  data: [self.servicePositiveVal, self.serviceAverageVal, self.serviceNegativeVal]
                }]
              },

              options: {

                legend: {
                  display: false
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
        foodChart: function() {
            var self = this

            ctx = document.getElementById('chartFood').getContext("2d");

            // var foodPositive = $("#foodPositive").val();
            // var foodAverage  = $("#foodAverage").val();
            // var foodNegative = $("#foodNegative").val();

            myChart = new Chart(ctx, {
              type: 'pie',
              data: {
                labels: [1, 2, 3],
                datasets: [{
                  label: "Emails",
                  pointRadius: 0,
                  pointHoverRadius: 0,
                  backgroundColor: [
                    // '#e3e3e3',
                    '#599E0F',
                    '#fcc468',
                    '#ef8157'
                  ],
                  borderWidth: 0,
                  data: [self.foodPositiveVal, self.foodAverageVal, self.foodNegativeVal]
                }]
              },

              options: {

                legend: {
                  display: false
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
        applyFilter: function() {
            var self = this

            if (self.fromDt === '' || self.fromDt === null) 
            {
                Swal.fire('Error!','From date cannot be empty','error')
                return false
            }
            else if (self.toDt === '' || self.toDt === null) 
            {
                Swal.fire('Error!','To date cannot be empty','error')
                return false
            }
            else if (self.fromDt > self.toDt) 
            {
                Swal.fire('Error!','From date cannot be more than to date','error')
                return false
            }

            var url = 'api/v1/dashboard/cards?fdt=' + self.fromDt + '&tdt=' + self.toDt
            console.log(url)
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.submittedFeedbackCount = response.data.feedback_count
                    self.occupiedRRoomsCount    = response.data.occupied_rooms
                    self.guestScore             = response.data.guest_score
                    self.negativeScore          = response.data.negative_score

                    self.roomPositiveVal        = response.data.room_positive
                    self.roomAverageVal         = response.data.room_average
                    self.roomNegativeVal        = response.data.room_negative

                    self.servicePositiveVal     = response.data.service_positive
                    self.serviceAverageVal      = response.data.service_average
                    self.serviceNegativeVal     = response.data.service_negative
                   
                    self.foodPositiveVal        = response.data.food_positive
                    self.foodAverageVal         = response.data.food_average
                    self.foodNegativeVal        = response.data.food_negative

                    self.roomChart()
                    self.serviceChart()
                    self.foodChart()
                }
                else 
                {
                    self.submittedFeedbackCount = 0
                    self.occupiedRRoomsCount    = 0
                    self.guestScore             = 0
                    self.negativeScore          = 0
                    self.roomPositiveVal        = 0
                    self.roomAverageVal         = 0
                    self.roomNegativeVal        = 0
                    self.servicePositiveVal     = 0
                    self.serviceAverageVal      = 0
                    self.serviceNegativeVal     = 0
                    self.foodPositiveVal        = 0
                    self.foodAverageVal         = 0
                    self.foodNegativeVal        = 0

                    self.roomChart()
                    self.serviceChart()
                    self.foodChart()
                }
            })
            .catch(function (error) {
                self.totalFeedbackCount = 0
                self.allFeedbacks = []
                console.log('error')
            })
        }

        
    } //methods end    
})