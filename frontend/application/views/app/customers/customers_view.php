<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Customers</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Customers</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
          <div id="customers">
          <p align="center" id="overlay">
            <img src="static/assets/images/loading_img.gif" alt="Loading" />
          </p>
          <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <div class="row">
              <div class="col-md-12 text-right">
                <a href="customers/create">
                  <button class="btn btn-sm btn-success"><i class="fa fa-plus fa-1x"></i>&nbsp;New Customer</button>
                </a>
              </div><br><br><br>
              <div class="col-md-12">
                <div class="table-responsive">
                  <div v-if = "customersCount > 0">
                    <table class="table">
                      <thead class=" text-primary">
                        <th>S.No.</th>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Geedesk ID</th>
                        <th>LTCRM ID</th>
                      </thead>
                      <tbody>
                        <tr v-for="(item, index) in customers">
                          <td>{{index+1}}</td>
                          <td>{{item.id}}</td>
                          <td>
                            <a :href="'customers/details/'+item.id"><font color="black"><u>{{item.cust_name}}</u></font></a>
                          </td>
                          <td>{{item.geedesk_company_id}}</td>
                          <td>{{item.crm_customer_id}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div v-else>
                    <p><font size="3">No customers found</font></p>
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
<script src="static/assets/js/app/customers.js"></script>
