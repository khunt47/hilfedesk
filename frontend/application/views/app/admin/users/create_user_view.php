<div class="content">
  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header">
          <h5 class="title">New User</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="/admin">Admin</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="/admin/users">Users</a></li>
              <li  class="breadcrumb-item active" aria-current="page"><a style="color:black">New</a></li>
            </ol>
          </nav>
        </div>
        <div class="card-body">
          <div id="new-user">
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <div class="form-group">
              <label for="user_name"><b>First Name</b></label>
              <input class="form-control" type="text" v-model="userFname" placeholder="Enter first name">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Last Name</b></label>
              <input class="form-control" type="text" v-model="userLname" placeholder="Enter last name">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Email</b></label>
              <input class="form-control" type="text" v-model="userEmail" placeholder="Enter email address">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Password</b></label>
              <input class="form-control" type="password" v-model="userPaswd" placeholder="Enter password">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Confirm Password</b></label>
              <input class="form-control" type="password" v-model="userConfPaswd" placeholder="Confirm entered password">
            </div>
            <div class="form-group">
              <label for="user_name"><b>Role</b></label>
              <select class="form-control" v-model="userRole" @change="getSelectedUserRole($event)">
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="user">User</option>
              </select>
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
<script src="static/assets/js/app/new_user.js"></script>