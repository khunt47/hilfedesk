<div class="content">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">Create Customers</h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="customers">Customers</a></li>
                            <li class="breadcrumb-item" aria-current="page"><a style="color:black">Create Customers</a></li>
                        </ol>
                    </nav>
                    <hr>
                </div>
                
                <div class="card-body">
                    <div id="create-customer">
                        <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
                        <div class="form-group">
                            <label for="name"><b>Name</b> <font color="red">*</font></label>
                            <input class="form-control" type="text" v-model="name" placeholder="Enter customer name">
                        </div>
                        <div class="form-group">
                            <label for="name"><b>LTCRM ID</b> </label>
                            <input class="form-control" type="text" v-model="ltcrmId" placeholder="Enter customer name">
                        </div>
                        <div class="form-group">
                            <label for="name"><b>Geedesk ID</b> </label>
                            <input class="form-control" type="text" v-model="geedeskId" placeholder="Enter customer name">
                        </div>
                        <hr>
                        <button type="submit" id ="create-btn" class=" btn btn-dark" @click="createCustomer"><i class="fa fa-save"></i>&nbsp;Create</button>
                        <button type="reset" name="cancel" class=" btn btn-danger" @click="clearFields"><i class="fa fa-times"></i>&nbsp;Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="static/assets/js/app/create_customers.js"></script>




