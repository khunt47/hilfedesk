<div class="content">
  <div class="row">
    <div class="col-md-5">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Change Password</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="my-profile">My Profile</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">change Password</a></li>
            </ol>
          </nav>
        </div>
        <div class="card-body" id="change-password">
        <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
           <div class="form-group">
             <label for="oldpassword"><b> Current Password:<b></label>
               <input class="form-control" type="password" v-model="currentPassword" placeholder="Enter current password">
             </div>
             <div class="form-group">
               <label for="password"><b> New Password:<b></label>
                 <input class="form-control" type="password" v-model="newPassword" placeholder="Enter new password">
               </div>
               <div class="form-group">
                <label for="password"><b>Confirm Password:<b></label>
                  <input class="form-control" type="password" v-model="newPasswordConfirm" placeholder="Re-enter new password">
                  <hr>
                  <button class="btn btn-primary" @click="changePassword" id="changePasswordBtn">Change password</button>
                  <button type="reset" class="btn btn-default">Cancel</button>
                  <hr>
              </div>
            </div>
          </div>
        </div>

      </div>

      </div>
      <script src="static/assets/js/app/user_change_password.js"></script>
