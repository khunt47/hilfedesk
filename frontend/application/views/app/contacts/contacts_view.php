<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Contacts</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Contacts</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
          <div id="contacts">
          <p align="center" id="overlay">
            <img src="static/assets/images/loading_img.gif" alt="Loading" />
          </p>
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <div v-if = "contactsCount > 0">
                    <table class="table">
                      <thead class=" text-primary">
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Mobile</th>
                        <th>Organization</th>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in contacts">
                          <td>
                            <a :href="'contacts/details/'+item.id">{{item.fname}} {{item.lname}}</a>
                          </td>
                          <td>{{item.email}}</td>
                          <td>{{item.phone}}</td>
                          <td>{{item.mobile}}</td>
                          <td>{{item.email}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-else>
                    <p><font size="3">No contacts found</font></p>
                  </div>
              </div>
              </div>
              <!-- <div class="col-md-2">
                <h4><b>Filters</b></h4>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/contacts.js"></script>
