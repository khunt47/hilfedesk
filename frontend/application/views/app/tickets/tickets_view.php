<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Tickets</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="tickets/projects">Projects</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Tickets</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
          <div id="tickets">
            <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <input ref="project_id" value="<?php echo $project_id;?>" hidden>
            <span v-if="showLoading === 'yes'"><h4>Loading....</h4></span>
            <span v-else>
              <h3>Project Name: {{projectName}}</h3>
              <div class="row">
                <div class="col-md-10">
                  <div class="table-responsive">
                    <div v-if = "ticketsCount > 0">
                      <table class="table">
                        <thead class=" text-primary">
                          <th>
                            ID
                          </th>
                          <th>Title</th>
                          <th>Priority</th>
                          <th>Status</th>
                          <th>Created on</th>
                        </thead>
                        <tbody>
                          <tr v-for="(item, index) in tickets">
                            <td>{{item.display_ticket_id}}</td>
                            <td>
                              <a :href="'tickets/details/'+projectId+'/'+item.id"><font color="black"><u>{{item.heading}}</u></font></a>
                            </td>
                            <td>{{capitalizeFirstLetter(item.priority)}}</td>
                            <td>
                              <span v-if="item.status === 'inprogress'">
                                In Progress
                              </span>
                              <span v-else-if="item.status === 'onhold'">
                                On Hold
                              </span>
                              <span v-else>
                                {{capitalizeFirstLetter(item.status)}}
                              </span>
                            </td>
                            <td>{{formatDate(item.created_on)}}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div v-else>
                      <p><font size="3">No tickets found</font></p>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <p align="right"><a href="tickets/new" class="btn btn-xs btn-dark">New Ticket</a></p>
                  <h4><b>Filters</b></h4>
                  <b>From date</b>
                  <input type="date" class="form-control"><br>
                  <b>To date</b>
                  <input type="date" class="form-control"><br>
                  <select class="form-control" @click="priorityFilter($event)">
                    <option value='' :selected="ticketPriority===''" :disabled="ticketPriority===''">Choose priority</option>
                    <option value='low' :selected="ticketPriority==='low'" :disabled="ticketPriority==='low'">Low</option>
                    <option value='medium' :selected="ticketPriority==='medium'" :disabled="ticketPriority==='medium'">Medium</option>
                    <option value='high' :selected="ticketPriority==='high'" :disabled="ticketPriority==='high'">High</option>
                    <option value='critical' :selected="ticketPriority==='critical'" :disabled="ticketPriority==='critical'">Critical</option>
                  </select>
                  <br>
                  <select class="form-control" @change="statusFilter($event)">
                    <option value='' :selected="ticketStatus===''" :disabled="ticketStatus===''">Choose status</option>
                    <option value='new' :selected="ticketStatus==='new'" :disabled="ticketStatus==='new'">New</option>
                    <option value='open' :selected="ticketStatus==='open'" :disabled="ticketStatus==='open'">Open</option>
                    <option value='inprogress' :selected="ticketStatus==='inprogress'" :disabled="ticketStatus==='inprogress'">In Progress</option>
                    <option value='onhold' :selected="ticketStatus==='onhold'" :disabled="ticketStatus==='onhold'">On Hold</option>
                    <option value='resolved' :selected="ticketStatus==='resolved'" :disabled="ticketStatus==='resolved'">Resolved</option>
                    <option value='deleted' :selected="ticketStatus==='deleted'" :disabled="ticketStatus==='deleted'">Deleted</option>
                  </select>
                  <br>
                  <p align="right">
                    <button class="btn btn-dark btn-sm" @click="applyFilter">Apply</button>
                    <button class="btn btn-danger btn-sm" @click="clearFilter">Clear</button>
                  </p>
                  <br>
                </div>
              </div>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/tickets.js"></script>
