<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Users</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="/admin">Admin</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Users</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
          <p align="right"><a href="/admin/users/new" class="btn btn-xs btn-dark">New User</a></p>
          <div id="users">
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
          <span v-if="showLoading === 'yes'"><h4>Loading....</h4></span>
          <span v-else>
            <div class="row">
              <div class="col-md-10">
                <div class="table-responsive">
                  <div v-if = "usersCount > 0">
                    <table class="table">
                      <thead class=" text-primary">
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in users">
                          <td>{{item.fname}} {{item.lname}}</td>
                          <td>{{item.email}}</td>
                          <td>{{item.role}}</td>
                          <td>
                              <p v-if="item.user_id === loggedInUserId">You cannot make changes to your account</p>
                              <p v-else>
                              <button class="btn btn-xs" data-toggle="modal" data-target="#editUserModal" @click="setUserDetails(item.user_id, item.fname, item.lname, item.email, item.role)">Edit</button>
                              <button class="btn btn-info" data-toggle="modal" data-target="#changePasswordModal" @click="setUserId(item.user_id)">Change password</button>
                              <button class="btn btn-danger" @click="deleteUser(item.user_id)" >Delete</button>
                              </p>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-else>
                    <p><font size="3">No users found</font></p>
                  </div>
              </div>
              </div>
            </div>
          </span>

          <!-- Modal -->
          <!-- Change Password Modal -->
          <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="changePasswordModal">Change Password</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="user_name"><b>New Password</b> <font color="red">*</font></label>
                    <input class="form-control" type="password" v-model="newPassword">
                  </div>
                  <br>
                  <div class="form-group">
                    <label for="user_name"><b>Confirm New Password</b> <font color="red">*</font></label>
                    <input class="form-control" type="password" v-model="confirmNewPassword">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="updatePasswordBtn" @click="changeUserPassword">Update</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editUserModalLabel">Update User</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="user_name"><b>First Name</b> <font color="red">*</font></label>
                    <input class="form-control" type="name" v-model="userFname">
                  </div>
                  <br>
                  <div class="form-group">
                    <label for="user_name"><b>Last Name</b> <font color="red">*</font></label>
                    <input class="form-control" type="name" v-model="userLname">
                  </div>
                  <br>
                  <div class="form-group">
                    <label for="email"><b>Email</b> <font color="red">*</font></label>
                    <input class="form-control" type="email" v-model="userEmail">
                  </div>
                  <br>
                  <div class="form-group">
                    <label for="role"><b>Role</b> <font color="red">*</font></label>
                    <select class="form-control" v-model="userRole">
                      <option value="">Select Role</option>
                      <option value="admin">Admin</option>
                      <option value="manager">Manager</option>
                      <option value="user">User</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="editUserBtn" @click="updateUser">Update</button>
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
<script src="static/assets/js/app/users.js"></script>
