<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Projects</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Projects</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
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
                        </thead>
                        <tbody>
                          <tr v-for="(item, index) in projects">
                            <td>
                              <a :href="'projects/tickets/'+item.id"><font color="black"><u>{{item.name}}</u></font></a>
                            </td>
                            <td>{{item.email}}</td>
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
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script src="static/assets/js/app/projects.js"></script>
