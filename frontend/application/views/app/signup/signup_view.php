<div class="container">
  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <h1 class="logo-name"><img src="static/images/logo/daysupport_logo.png" alt="LTHD Logo" width="400"></h1><br>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title">Sign up</h1>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="signup">
    <input ref="api_base_url" value="<?php echo $api_base_url; ?>" hidden>
      <input ref="base_url" value="<?php echo base_url(); ?>" hidden>
    <div class="col-md-6 col-xs-6">
      <div class="panel-body">
        <fieldset>
          <div class="form-group">
            <input class="form-control" type="text" placeholder="Enter your first name" v-model="userFName" required>
          </div>
          <br>
          <div class="form-group">
            <input class="form-control" type="text" placeholder="Enter your last name" v-model="userLName" required>
          </div>
          <br>
          <div class="form-group">
            <input class="form-control" type="text" placeholder="Enter your organization" v-model="userOrg" required>
          </div>
          <br>
          <div class="form-group">
            <input class="form-control" type="email" placeholder="Enter your email address" v-model="userName" required>
          </div>
          <br>
          <div class="form-group">
            <input class="form-control" type="password" placeholder="Enter password for your choice" v-model="password" required>
          </div>
          <br>
          <button class="btn btn-lg btn-success btn-block" id="signupBtn" @click="signupUser">Signup</button>
        </fieldset>
        <hr>
      </div>
      <p>If you already have an account then, please <a href="/login"><b>Login</b></a> here.</p>
    </div>
    <div class="col-md-1 col-xs-1"></div>
    <div class="col-md-5 col-xs-5">
      <h6><b>Who are we?</b></h6>
      <p>Hi, I am Krishnan and I am a small business owner. I built DaySupport for small businesses so that they can have access to a helpdesk software with all features at an affordable price. </p>
      <br>
      <h6><b>Why use Daysupport?</b></h6>
      <p>Small businesses at some point need a helpdesk software with the right features at an affordable cost. </p>
      <p>Most applications that are quite popular in the industry today either arm twist them to pay more for the required features or simply do without them. </p>
      <p>DaySupport is a good alternative as it has all the features that a small business needs at an affordable price.</p>
    </div>
  </div>
  <hr>
</div>
<script src="static/assets/js/app/signup.js"></script>
