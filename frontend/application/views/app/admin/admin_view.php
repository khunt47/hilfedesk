<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Admin</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Admin</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body" id="validate-admin">
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                	<p><a href="/admin/users">Users</a></p>
                  <p><a href="/admin/projects">Projects</a></p>
                  <p><a href="/admin/timezone">Timezone</a></p>
              </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/validateadmin.js"></script>