  <script>
    Trix.config.attachments.preview.caption = { name: false, size: false }

    document.addEventListener('trix-initialize', () => {
      document.querySelector('.version').textContent = 'v' + Trix.VERSION;
    });
  </script>
<div class="content">
  <div class="row">
    <div class="col-md-11">
      <div class="card">
        <div class="card-header" id="ticket-details">
          <input ref="api_base_url" value="<?php echo $api_base_url; ?>" hidden>
          <input ref="linode_base_url" value="<?php echo $linode_base_url; ?>" hidden>
          <input ref="project_id" value="<?php echo $project_id; ?>" hidden>
          <input ref="ticket_id" value="<?php echo $ticket_id; ?>" hidden>
          <span v-if="showLoading === 'yes'"><h4>Loading....</h4></span>
          <span v-else>
          <h5 class="title">{{ticketDetails.heading}}</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <!-- <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="tickets/projects">Projects</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" :href="'projects/tickets/'+ticketProjectId">Tickets</a></li> -->
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">{{ticketDetails.heading}}</a></li>
            </ol>
          </nav>
          <hr>
          <div>
            <span v-if="ticketDetails.status === 'new'">
              <p align="right"><button class="btn btn-xs btn-dark" @click=takeTicket id="takeTicketBtn">Take</button></p>
            </span>
            <div class="row">
              <div class="col-md-6">
                <p><b>ID:</b> {{ticketDetails.display_ticket_id}}</p><br>
                <p><b>Title:</b> {{ticketDetails.heading}}</p><br>
                <p><b>Status:</b> {{ticketDetails.status}}</p><br>
                <p><b>Created on:</b> {{formatDate(ticketDetails.created_on)}}</p><br>
                <p><b>Taken on:</b> {{formatDate(ticketDetails.taken_on)}}</p><br>
              </div>
              <div class="col-md-6">
                <p><b>Contact Person:</b> {{ticketDetails.contact_fname}} {{ticketDetails.contact_lname}}</p><br>
                <p><b>Organization:</b> {{ticketDetails.cust_name}}</p><br>
                <p><b>Created by:</b> {{ticketDetails.created_by_fname}} {{ticketDetails.created_by_lname}}</p><br>
                <p><b>Owned by:</b>
                  <span v-if="ticketStatus === 'resolved' || ticketStatus === 'deleted' || ticketStatus === 'merged'">
                  {{ticketDetails.owned_by_fname}} {{ticketDetails.owned_by_lname}}
                  </span>
                  <span v-else>
                    <span v-if="projectUsersCount > 0">
                      <select v-model="ticketOwnedBy" @change="reassignTicket($event)">
                        <option v-for="(item, index) in projectUsers" :value="item.mapped_user_id" :selected="ticketOwnedBy === item.mapped_user_id" :disabled="ticketOwnedBy === item.mapped_user_id">
                          {{item.user_fname}} {{item.user_lname}}
                        </option>
                      </select>
                    </span>
                    <span v-else>
                      {{ticketDetails.owned_by_fname}} {{ticketDetails.owned_by_lname}}
                    </span>
                  </span>
                </p><br>
                <p><b>Status:</b>
                <span v-if="ticketStatus === 'new' || ticketStatus === 'merged' || ticketStatus === 'deleted'">
                  {{ticketStatus}}
                </span>
                <span v-else>
                  <select v-model="ticketStatus" @change="changeTicketStatus($event)">
                    <option value="new" :selected="ticketStatus === 'new'" :disabled="ticketStatus === 'new'" hidden="ticketStatus !== 'new'">New</option>
                    <option value="inprogress" :selected="ticketStatus === 'inprogress'" :disabled="ticketStatus === 'inprogress'">In Progress</option>
                    <option value="onhold" :selected="ticketStatus === 'onhold'" :disabled="ticketStatus === 'onhold'">On Hold</option>
                    <option value="resolved" :selected="ticketStatus === 'resolved'" :disabled="ticketStatus === 'resolved'">Resolved</option>
                    <option value="deleted" :selected="ticketStatus === 'deleted'" :disabled="ticketStatus === 'deleted'">Delete</option>
                  </select>
                </span>
                </p><br>
                <p><b>Priority:</b>
                <span v-if="ticketStatus === 'merged' || ticketStatus === 'deleted'">
                  {{ticketPriority}}
                </span>
                <span v-else>
                  <select v-model="ticketPriority" @change="changeTicketPriority($event)">
                    <option value="critical" :selected="ticketPriority === 'critical'" :disabled="ticketPriority === 'critical'">Critical</option>
                    <option value="high" :selected="ticketPriority === 'high'" :disabled="ticketPriority === 'high'">High</option>
                    <option value="medium" :selected="ticketPriority === 'medium'" :disabled="ticketPriority === 'medium'">Medium</option>
                    <option value="low" :selected="ticketPriority === 'low'" :disabled="ticketPriority === 'low'">Low</option>
                  </select>
                </span>
                </p><br>
              </div>
            </div>
            <div class="card-body">
              <hr>
              <div class="row">
                <p><b>Description</b></p><br><br>
                <div class="col-md-12">
                  <p  v-html="ticketDetails.description"></p>
                  <span v-if="ticketDetails.attachment_present">
                    <p v-for="(file, index) in ticketDetails.attachments">
                      <a :href="linodeBaseUrl+'/'+file.file_name" target="_blank">{{file.file_name}}
                      </a>
                    </p>
                  </span>
                </div>
              </div>
              <hr>
              <div class="row">
                <p><b>New Comment</b></p><br><br>
                <div class="col-md-12">
                  <!-- <textarea class="form-control" v-model="newTicketComment" placeholder="Add a new comment" rows=""></textarea> -->
                  
                  <input id="content" type="hidden">
                  <trix-editor input="content" @trix-change="onTrixChange"></trix-editor>
                  <br>
                  <div class="row">
                    <div class="col-md-12">
                      <input type="file" ref="fileInput">
                    </div>
                  </div>
                  <p align="right"><button class="btn btn-xs btn-dark" @click="createTicketComment" id="saveCommentBtn">Save</button></p>
                </div>
              </div>
              <hr>
              <div class="row">
                <p><b>Comments</b></p><br><br>
                <div class="col-md-12">
                  <span v-if="ticketCommentsCount > 0">
                    <span v-for="(item, index) in ticketComments">
                      <small><p><b>Comment by:</b> {{item.created_by_fname}} {{item.created_by_lname}}<br><b>Comment on:</b> {{formatDate(item.created_on)}}</p></small>
                      <p v-html="item.comment"></p>
                      <span v-if="item.attachment == 'yes'">
                        <p v-for="(file, index) in item.attachments">
                          <a :href="linodeBaseUrl+'/'+file.file_name" target="_blank">
                            {{file.file_name}}
                          </a>
                        </p>
                      </span>
                      <hr>
                      <br>
                    </span>
                  </span>
                  <span v-else>
                    <p>No comments found</p>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </span>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/ticket_details.js"></script>
