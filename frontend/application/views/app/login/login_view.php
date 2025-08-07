      <div class="row">
        <div class="col-md-4 col-md-offset-4" id="login">
          <input ref="api_base_url" value="<?php echo $api_base_url; ?>" hidden>
          <input ref="base_url" value="<?php echo base_url(); ?>" hidden>
          <input ref="home_page" value="<?php echo $home_page; ?>" hidden>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h1 class="panel-title">Sign in</h1>
            </div>
            <div class="panel-body">
              <fieldset>
                <div class="form-group">
                  <input class="form-control" type="email" placeholder="Enter your username" v-model="userName" required>
                </div>
                <div class="form-group">
                  <input class="form-control" type="password" id="id_password" placeholder="Enter your password" v-model="userPwd" required>
                </div>
                <button class="btn btn-lg btn-success btn-block" id="login-btn" @click="loginUser">Login</button>
              </fieldset>
              <!-- <p><a class="btn btn-danger" href="/login/social/google">Login with Google</a></p> -->
            </div>
          </div>
          <!-- <p>If you do not have an account, please <a href="/signup"><b>Sign up</b></a> here.</p> -->
        </div>
      </div>
      <hr>
    </div>
    <script src="static/assets/js/app/login.js"></script>
