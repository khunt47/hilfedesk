<div class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<h5 class="title">Reports</h5>
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
						<li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
						<li class="breadcrumb-item" aria-current="page"><a style="color:black">Reports</a></li>
						</ol>
					</nav>
					<hr>
				</div>
				<div class="card-body">
					<div id="ticketMetricsApp">
						<input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
						<label class="label info">From</label>
						<input type="date" v-model="fromDate" >&nbsp&nbsp
						<label for="tickets">To</label>
						<input type="date" v-model="toDate" > &nbsp
						<button class="btn btn-info" @click="applyFilter">Apply</button>
						<div class="row mb-5">
							<div class="col-md-6 mt-sm-5">
								<!-- Tickets Count -->
								<h2><b><center>Tickets Count</center></b></h2>
								<div v-if="showLoading === 'yes'"><center><h4>Loading....</h4></center></div>
								<div v-else>
									<div v-if="metrics.total > 0">
										<table class="table">
											<thead class="text-primary">
												<tr>
													<th>Status</th>
													<th>Count</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td><b>Open</b></td>
													<td>{{ metrics.counts['new'] || 0 }}</td>
												</tr>
												<tr>
													<td><b>Inprogress</b></td>
													<td>{{ metrics.counts['inprogress'] || 0 }}</td>
												</tr>
												<tr>
													<td><b>Onhold</b></td>
													<td>{{ metrics.counts['onhold'] || 0 }}</td>
												</tr>
												<tr>
													<td><b>Resolved</b></td>
													<td>{{ metrics.counts['resolved'] || 0 }}</td>
												</tr>
												<tr class="table-info font-weight-bold">
													<td><b>Total</b></td>
													<td><b>{{ metrics.total }}</b></td>
												</tr>
											</tbody>
										</table>
									</div>
									<div v-else>
										<p><font size="3"><center>No tickets found<center></font></p>
									</div>
								</div>
							</div>

							<div class="col-md-6 mt-sm-5">
								<!-- Tickets by Status Chart -->
								<h2><b><center>Tickets by Status</center></b></h2>
								<canvas id="ticketsChart"></canvas>
							</div>
						</div>

						<div class="row mt-5 mb-5 pt-5">
							<div class="col-md-6 mt-sm-5">
								<!-- Agent Workload -->
								<h2><b><center>Agents Workload</center></b></h2>
								<div v-if="showLoading === 'yes'"><center><h4>Loading....</h4></center></div>
								<div v-else>
									<div v-if="workload.length">
										<table class="table">
											<thead class="text-primary">
												<tr>
													<th>Agent</th>
													<th>Ticket Count</th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="agent in workload" :key="agent.agent_id">
													<td><b>{{ agent.fname }} {{agent.lname}}</b></td>
													<td>{{ agent.ticket_count || 0 }}</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div v-else>
										<p><font size="3"><center>No ticket data available for the agents</center></font></p>
									</div>
								</div>
							</div>
							<div class="col-md-6 mt-sm-5">
								<!-- Daily Ticket Trends -->
								<h2><b><center>Daily Ticket Trends</center></b></h2>
								<div v-if="showLoading === 'yes'"><center><h4>Loading....</h4></center></div>
								<canvas id="ticketTrends"></canvas>
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
	</div>
</div>

<script src="static/assets/js/app/reports.js"></script>

<!-- Ignore this
💰 Paid Cloud Version – Advanced Reports
These offer real value for team leads, operations managers, and business owners:

1. SLA Performance
SLA breach count

Avg. first response time

Avg. resolution time

SLA compliance % by agent/team

2. Agent Performance
Time to first response

Time to close

CSAT (if you add surveys later)

Tickets handled per shift/day/week

3. Ticket Analytics
By tag/topic/category

By source (email, widget, API)

By customer organization

4. Custom Reports
Filter-based reporting (time range, status, agent, tag)

Export to CSV/Excel

5. Email Reports (Optional Add-on)
Weekly performance digest to admins

SLA violation alerts
-->
             

