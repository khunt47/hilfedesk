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
                          <th>Landline</th>
                          <th>Mobile</th>
                          <th>Organization</th>
                          <th>Actions</th>
                        </thead>
                        <tbody>
                          <tr v-for="(item, index) in contacts">
                            <td>
                              <a :href="'contacts/details/'+item.id">{{item.fname}} {{item.lname}}</a>
                            </td>
                            <td>{{item.email}}</td>
                            <td>{{item.phone}}</td>
                            <td>{{item.mobile}}</td>
                            <td>{{item.cust_name}}</td>
                            <td>                              
                              <button class="btn btn-xs" data-toggle="modal" data-target="#editContactModal" @click="setContactDetails(item.id, item.fname, item.lname, item.email, item.phone, item.mobile, item.customer_id)">Edit</button>
                            </td>
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

              <!-- Edit Contact Modal -->
            <div class="modal fade" id="editContactModal" tabindex="-1" role="dialog" aria-labelledby="editContactModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editContactModalLabel">Update Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="contact_name"><b>First Name</b> <font color="red">*</font></label>
                      <input class="form-control" type="name" v-model="contactFname">
                    </div>
                    <br>
                    <div class="form-group">
                      <label for="contact_name"><b>Last Name</b> <font color="red">*</font></label>
                      <input class="form-control" type="name" v-model="contactLname">
                    </div>
                    <br>
                    <div class="form-group">
                      <label for="email"><b>Email</b> <font color="red">*</font></label>
                      <input class="form-control" type="email" v-model="contactEmail">
                    </div>
                    <br>
                    <div class="form-group">
                      <label for="phone"><b>Landline</b> </label>
                      <input class="form-control" type="number" v-model="contactPhone">
                    </div>
                    <br>
                    <div class="form-group">
                      <label for="mobile"><b>Mobile</b> <font color="red">*</font></label>
                      <input class="form-control" type="number" v-model="contactMobile">
                    </div>
                    <br>
                    <div class="form-group">
                      <label for="org"><b>Organization</b> <font color="red">*</font></label>
                      <!-- <input class="form-control" type="text" v-model="contactOrganization"> -->
                      <select class="form-control" v-model="contactOrganization">
                        <option disabled value="">-- Select Organization --</option>
                        <option v-for="(item, index) in customers" :key="item.id" :value="item.id">
                          {{ item.cust_name }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editContactlBtn" @click="updateContact">Update</button>
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
<script src="static/assets/js/app/contacts.js"></script>
