<div class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item" aria-current="page"><a style="color:black">Dashboard</a></li>
    </ol>
  </nav>
  <hr>
  <div id="dashboard-cards">
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-body ">
            <div class="row">
              <div class="col-5 col-md-4">
                <div class="icon-big text-center icon-warning">
                  <i class="nc-icon nc-globe text-warning"></i>
                </div>
              </div>
              <div class="col-7 col-md-8">
                <div class="numbers">
                 <p class="card-category">Tickets</p>
                 <p class="card-title">100</p>
                 </div>
               </div>
             </div>
           </div>
           <div class="card-footer ">
            <hr>
            <div class="stats">
              <!-- <i class="fa fa-refresh"></i> Update Now -->
              <i class="fa fa-calendar"></i> Since last weeek
            </div>
          </div>
        </div>
      </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card card-stats">
            <div class="card-body ">
              <div class="row">
                <div class="col-5 col-md-4">
                  <div class="icon-big text-center icon-warning">
                    <i class="nc-icon nc-favourite-28 text-success"></i>
                  </div>
                </div>
                <div class="col-7 col-md-8">
                  <div class="numbers">
                    <p class="card-category">Resolved percentage</p>
                    <p class="card-title">50%</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer ">
              <hr>
              <div class="stats">
                <i class="fa fa-calendar"></i> Since last weeek
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card card-stats">
          <div class="card-body ">
            <div class="row">
              <div class="col-5 col-md-4">
                <div class="icon-big text-center icon-warning">
                  <i class="nc-icon nc-money-coins text-danger"></i>
                </div>
              </div>
              <div class="col-7 col-md-8">
                <div class="numbers">
                  <p class="card-category">Escalation percentage</p>
                  <p class="card-title">20%</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer ">
              <hr>
              <div class="stats">
                <i class="fa fa-calendar"></i> Since last weeek
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card card-stats">
            <div class="card-body ">
              <div class="row">
                <div class="col-5 col-md-4">
                  <div class="icon-big text-center icon-warning">
                    <i class="nc-icon nc-vector text-primary"></i>
                  </div>
                </div>
                <div class="col-7 col-md-8">
                  <div class="numbers">
                    <p class="card-category">Projects</p>
                    <p class="card-title">4</p>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer ">
            <hr>
            <div class="stats">
              <i class="fa fa-calendar"></i> Since last weeek
              <!-- <i class="fa fa-refresh"></i> Update now -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- <div class="row">
    <div class="col-md-12">
      <div class="card ">
        <div class="card-header ">
          <h5 class="card-title">Guests Feedback</h5>
          <p class="card-category">24 Hours performance</p>
        </div>
        <div class="card-body ">
          <canvas id=chartHours width="400" height="100"></canvas>
        </div>
        <div class="card-footer ">
          <hr>
          <div class="stats">
            <i class="fa fa-history"></i> Updated 3 minutes ago
          </div>
        </div>
      </div>
    </div>
  </div> -->
  <div class="row">
    <div class="col-md-4">
      <div class="card ">
        <div class="card-header ">
          <h5 class="card-title">Tickets</h5>
          <!-- <p class="card-category">Last Campaign Performance</p> -->
        </div>
        <div class="card-body ">
          <canvas id="chartRoom"></canvas>
        </div>
        <div class="card-footer ">
          <div class="legend">
            <i class="fa fa-circle text-success"></i> Tickets
            <i class="fa fa-circle text-danger"></i> Escalated
            <i class="fa fa-circle text-warning"></i> Resolved
          </div>
          <hr>
          <div class="stats">
            <i class="fa fa-calendar"></i> Since last weeek
          </div>
        </div>
      </div>
    </div>
    <!-- <div class="col-md-4">
      <div class="card ">
        <div class="card-header ">
          <h5 class="card-title">Projects</h5>
          <p class="card-category">Last Campaign Performance</p>
        </div>
        <div class="card-body ">
          <canvas id="chartService"></canvas>
        </div>
        <div class="card-footer ">
          <div class="legend">
            <i class="fa fa-circle text-success"></i> Positive
            <i class="fa fa-circle text-danger"></i> Negative
            <i class="fa fa-circle text-warning"></i> Average
          </div>
          <hr>
          <div class="stats">
            <i class="fa fa-calendar"></i> Since last weeek
          </div>
        </div>
      </div>
    </div> -->
    <!-- <div class="col-md-4">
      <div class="card ">
        <div class="card-header ">
          <h5 class="card-title">Food</h5>
          <p class="card-category">Last Campaign Performance</p>
        </div>
        <div class="card-body ">
          <canvas id="chartFood"></canvas>
        </div>
        <div class="card-footer ">
          <div class="legend">
            <i class="fa fa-circle text-success"></i> Positive
            <i class="fa fa-circle text-danger"></i> Negative
            <i class="fa fa-circle text-warning"></i> Average
          </div>
          <hr>
          <div class="stats">
            <i class="fa fa-calendar"></i> Since last weeek
          </div>
        </div>
      </div>
    </div> -->
    <div class="col-md-8">
      <div class="card card-chart">
        <div class="card-header">
          <h5 class="card-title">Tickets Flow</h5> <!-- Line chart to show rising number of tickets in a week -->
          <p class="card-category">Line Chart with Points</p>
        </div>
        <div class="card-body">
          <canvas id="speedChart" width="400" height="100"></canvas>
        </div>
        <div class="card-footer">
          <div class="chart-legend">
            <i class="fa fa-circle text-info"></i> Tesla Model S
            <i class="fa fa-circle text-warning"></i> BMW 5 Series
          </div>
          <hr/>
          <div class="card-stats">
            <i class="fa fa-check"></i> Data information certified
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="static/assets/js/app/dashboard.js"></script>
<script src="static/assets/js/plugins/chartjs.min.js"></script>
<script type="text/javascript">
   ctx = document.getElementById('chartRoom').getContext("2d");

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
          data: [342, 480, 530, 120]
          //data: [<?php echo $room_positive; ?>, <?php echo $room_average; ?>, <?php echo $room_negative; ?>]
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


    ctx = document.getElementById('chartService').getContext("2d");

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
          // data: [342, 480, 530, 120]
          data: [<?php echo $service_positive; ?>, <?php echo $service_negative; ?>, <?php echo $service_average; ?>]
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


    ctx = document.getElementById('chartFood').getContext("2d");

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
          // data: [342, 480, 530, 120]
          data: [<?php echo $food_positive; ?>, <?php echo $food_negative; ?>, <?php echo $food_average; ?>]
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

</script>
