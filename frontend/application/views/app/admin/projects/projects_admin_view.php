<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Projects</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="/admin">Admin</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Projects</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
          <p align="right"><a href="tickets/projects/new" class="btn btn-xs btn-dark">New Project</a></p><br>
          <div id="projects">
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
          <span v-if="showLoading === 'yes'"><h4>Loading....</h4></span>
            <span v-else>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <div v-if = "projectsCount > 0">
                    <table class="table">
                      <thead class=" text-primary">
                        <th>Name</th>
                        <th>Email</th>
                        <th>Forward Email</th>
                        <th>Code</th>
                        <th>Users</th>
                        <th>Action</th>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in projects">
                          <td>
                            <a :href="'projects/tickets/'+item.id"><font color="black"><u>{{item.name}}</u></font></a>
                          </td>
                          <td>{{item.email}}</td>
                          <td>{{item.forward_email}}</td>
                          <td>{{item.project_code}}</td>
                          <td>
                            <a class="btn btn-xs btn-dark" href="/amin/projects/map-users">Map Users</a>
                          </td>
                          <td>
                            <button class="btn btn-xs btn-danger">Delete</button>
                            <button class="btn btn-xs" data-toggle="modal" data-target="#updateProjectModal" @click="getProjectDetails(item.id, item.name, item.project_code, item.email)">Edit</button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-else>
                    <p><font size="3">No projects found</font></p>
                  </div>
              </div>
              </div>
              <!-- <div class="col-md-2">
                <h4><b>Filters</b></h4>
              </div> -->
            </div>
          </span>

          <!-- Modal -->
          <!-- Update Project Modal -->
          <div class="modal fade" id="updateProjectModal" tabindex="-1" role="dialog" aria-labelledby="updateProjectModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="updateProjectModal">Update Project</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="name"><b>Name</b> <font color="red">*</font></label>
                    <input class="form-control" type="name" v-model="projectName">
                  </div>
                  <br>
                  <div class="form-group">
                    <label for="user_name"><b>Code</b> <font color="red">*</font></label>
                    <input class="form-control" type="code" v-model="projectCode">
                  </div>
                  <br>
                  <div class="form-group">
                    <label for="mail"><b>Email</b> <font color="red">*</font></label>
                    <input class="form-control" type="mail" v-model="projectEmail">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="updateProjectBtn" @click="updateProject">Update</button>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/projects.js"></script>
