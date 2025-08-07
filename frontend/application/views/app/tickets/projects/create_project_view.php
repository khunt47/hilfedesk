<div class="content">
  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header">
          <h5 class="title">New Project</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="tickets/projects">Projects</a></li>
              <li  class="breadcrumb-item active" aria-current="page"><a style="color:black">New Project</a></li>
            </ol>
          </nav>
        </div>
        <div class="card-body">
          <div id="new-project">
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <div class="form-group">
              <label for="user_name"><b>Name</b></label>
              <input class="form-control" type="text" v-model="projectName" placeholder="Enter project name">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Code <small>(3 characters)</small></b></label>
              <input class="form-control" type="text" v-model="projectCode" placeholder="Enter project code">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Email</b></label>
              <input class="form-control" type="email" v-model="projectEmail" placeholder="Enter project email">
            </div>
            <hr>
            <button class="btn btn-dark" @click="createProject" id="createBtn">Create</button>
            <button type="reset" name="cancel" class=" btn btn-danger" @click="clearFields"><i class="fa fa-times"></i>&nbsp;Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
<script src="static/assets/js/app/new_project.js"></script>
