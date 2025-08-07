  <script>
    Trix.config.attachments.preview.caption = { name: false, size: false }

    document.addEventListener('trix-initialize', () => {
      document.querySelector('.version').textContent = 'v' + Trix.VERSION;
    });
  </script>
<div class="content">
  <div class="row">
    <div class="col-md-10">
      <div class="card">
        <div class="card-header">
          <h5 class="title">New Ticket</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="tickets/projects">Projects</a></li>
              <li  class="breadcrumb-item active" aria-current="page"><a style="color:black">New Ticket</a></li>
            </ol>
          </nav>
        </div>
        <div class="card-body">
          <div id="new-ticket">
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
          <input ref="base_url" value="<?php echo base_url(); ?>" hidden>
            <div class="form-group">
              <label for="user_name"><b>Project</b> <font color="red">*</font></label>
              <select v-model="projectId" @change="setProjectId($event)" class="form-control">
                <option value=0 :selected="projectId === 0" :disabled="projectId === 0">Select project</option>
                <option v-for="(item, index) in projects" :value="item.id" :selected="projectId === item.id" :disabled="projectId === item.id">
                  {{item.name}}
                </option>
              </select>
            </div>
            <!-- <div class="form-group">
              <label for="user_name"><b>Customer</b> <font color="red">*</font></label>
              <select v-model="customerId" @change="setCustomerId($event)" class="form-control">
                <option value=0 :selected="customerId === 0" :disabled="customerId === 0">Select customer</option>
                <option v-for="(item, index) in customers" :value="item.id" :selected="customerId === item.id" :disabled="customerId === item.id">
                  {{item.cust_name}}
                </option>
              </select>
            </div> -->
            <div class="form-group">
              <label for="user_name"><b>Customer</b> <font color="red">*</font></label>
              <v-select :options="customers" v-model="customerId" label="cust_name" :reduce="customer => customer.id"></v-select>
            </div>
            <div class="form-group">
              <label for="user_name"><b>Title</b> <font color="red">*</font></label>
              <input class="form-control" type="email" v-model="ticketTitle" placeholder="Enter ticket title">
            </div>
            <div class="form-group">
              <label for="user_name"><b>First Name</b> <font color="red">*</font></label>
              <input class="form-control" type="email" v-model="contactFName" placeholder="Enter contact first name">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Last Name</b> <font color="red">*</font></label>
              <input class="form-control" type="email" v-model="contactLName" placeholder="Enter contact last name">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Email</b> <font color="red">*</font></label>
              <input class="form-control" type="email" v-model="contactEmail" placeholder="Enter contact email">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Description</b> <font color="red">*</font></label>
              <textarea v-model="ticketDesc" class="form-control" placeholder="Describe the ticket in detail"></textarea>
                <!-- <input id="content" type="hidden">
                <trix-editor input="content" @trix-change="onTrixChange"></trix-editor> -->
            </div>
            <div class="form-group">
              <label for="user_role" class="label-default"><b>Priority</b></label>
                <select class="form-control" v-model="ticketPriority" @click="setPriority($event)">
                    <option value='low' :selected="ticketPriority==='low'" :disabled="ticketPriority==='low'">Low</option>
                    <option value='medium' :selected="ticketPriority==='medium'" :disabled="ticketPriority==='medium'">Medium</option>
                    <option value='high' :selected="ticketPriority==='high'" :disabled="ticketPriority==='high'">High</option>
                    <option value='critical' :selected="ticketPriority==='critical'" :disabled="ticketPriority==='critical'">Critical</option>
                </select>
            </div>
            <input type="file" ref="fileInput">
            <hr>
            <button class="btn btn-dark" @click="createTicket" id="createBtn">Create</button>
            <button type="reset" name="cancel" class=" btn btn-danger" @click="clearFields"><i class="fa fa-times"></i>&nbsp;Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/new_ticket.js"></script>
