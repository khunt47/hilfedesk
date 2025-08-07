<div class="container">
    <div class="row">
    <div class="col-md-4 col-md-offset-4" id="google-login">
    <input ref="api_base_url" value="<?php echo $api_base_url; ?>" hidden>
    <input ref="base_url" value="<?php echo base_url(); ?>" hidden>
    <input ref="user_email" value="<?php echo $user_email; ?>" hidden>
     <h1 class="logo-name"><img src="static/images/logo/daysupport_logo.png" alt="daysupport logo logo" width="400"></h1><br><br>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Logging in....</h3>
        </div>
          <div class="panel-body">
            
            <hr>
          </div>
      </div>
      <p>&copy; <?php echo date('Y'); ?> Geedesk Technologies Inc.</p>
      <!-- <p>If you do not have an account, please <a href="https://console.daysupport.co.uk/signup" target="_blank">sign up</a> here.</p> -->
    </div>
  </div>
</div>
<script src="static/assets/js/app/google_login.js"></script>
