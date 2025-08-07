<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Helpdesk - Setup Wizard</h5>
          <hr>
        </div>
        <div class="card-body">
          <div id="tickets">
            <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <input ref="base_url" value="<?php echo base_url(); ?>" hidden>
            <div class="row">
              <div class="col-md-8">
                <p>Configuring DaySupport is quite simple. All you have to do is follow the following steps and you should be up and running in no time.</p>
                <br>
                <h5><b>Step 1 - Create a project</b></h5>
                <p>Projects are support channels that you create to manage your support channels.<br>
                <button class="btn btn-dark" data-toggle="modal" data-target="#createProjectModal">Create Project</button></p>
                <hr>
                <h5><b>Step 2 - Update timezone</b></h5>
                <p>Projects are support channels that you create to manage your support channels.<br>
                <button class="btn btn-dark" data-toggle="modal" data-target="#updateTimezoneModal">Update Timezone</button></p>
              </div>
              <div class="col-md-1"></div>
              <div class="col-md-3">
                <p>Hello, thank you for signing up for DaySupport. We have built this setup wizard to guide you through the setup process.</p>
              </div>
            </div>

            <!-- Create Project Modal -->
            <div class="modal fade" id="createProjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Create Project Modal -->

            <!-- Update Timezone Modal -->
            <div class="modal fade" id="updateTimezoneModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Timezone</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Update Timezone Modal -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- <script src="static/assets/js/app/home_tickets.js"></script> -->
