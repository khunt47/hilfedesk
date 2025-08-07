<div class="content">
  <div class="containe">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">My Profile</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">My Profile</a></li>
            </ol>
          </nav>
        </div>
        <div class="card-body" id="my-profile">
        <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>

            <div class="profile-pic-mouse-hover">
             <a
             data-original-title='Click to change profile pic'>
             <p><img src="static/images/images.jpeg" alt="user profile pic"  width="100"/>
             </a>
           </div>
        <div>


        <p align="center" id="overlay">
          <img src="static/assets/images/loading_img.gif" alt="Loading" />
        </p>
          <p><b>{{userFullName}}</b></p>
        <p>{{userEmail}}</p>
        <p></p>
        <p><a href="my-profile/password/change" class="btn btn-inverse">Change password</a></p>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<script src="static/assets/js/app/user_my_profile.js"></script>
