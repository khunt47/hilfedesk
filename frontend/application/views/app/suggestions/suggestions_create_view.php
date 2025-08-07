<div class="content">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">Create Suggestions</h5>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="suggestions">Suggestions</a></li>
                            <li class="breadcrumb-item" aria-current="page"><a style="color:black">Create Suggestions</a></li>
                        </ol>
                    </nav>
                    <hr>
                </div>

                <div class="card-body">
                    <div id="create-suggestion">
                        <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
                        <div class="form-group">
                            <label for="user_name"><b>Customer</b> <font color="red">*</font></label>
                            <v-select :options="customers" v-model="customerId" label="cust_name" :reduce="customer => customer.id"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="user_name"><b>Suggestion</b> <font color="red">*</font></label>
                            <textarea v-model="suggestion" class="form-control" placeholder="Enter your suggestion "></textarea>
                        </div>
                        <hr>
                        <button type="submit" id ="create-btn" class=" btn btn-dark" @click="createSuggestions"><i class="fa fa-save"></i>&nbsp;Create</button>
                        <button type="reset" name="cancel" class=" btn btn-danger" @click="clearFields"><i class="fa fa-times"></i>&nbsp;Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="static/assets/js/app/create_suggestions.js"></script>

