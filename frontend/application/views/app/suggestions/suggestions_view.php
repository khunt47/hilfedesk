<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Suggestions</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Suggestions</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
          <div id="suggestions">
            <p align="center" id="overlay">
              <img src="static/assets/images/loading_img.gif" alt="Loading" />
            </p>
            <input ref="api_base_url" value="<?php echo $api_base_url;?>" hidden>
            <span v-if="showLoading === 'yes'"><h4>Loading....</h4></span>
            <span v-else>
              <div class="row">
                <div class="col-md-12 text-right">
                  <a href="suggestions/create">
                    <button class="btn btn-sm btn-success"><i class="fa fa-plus fa-1x"></i>&nbsp;New Suggestion</button>
                  </a>
                </div><br><br><br>
                <div class="col-md-12">
                  <div class="table-responsive">
                    <div v-if = "suggestionsCount > 0">
                      <table class="table">
                        <thead class=" text-primary">
                          <th>S.No.</th>
                          <th>Customer</th>
                          <th>Suggestion</th>
                          <th>Created By</th>
                          <th>Created On</th>
                          <th>Action</th>
                        </thead>
                        <tbody>
                          <tr v-for="(item, index) in suggestions">
                            <td>{{index+1}}</td>
                            <td>{{item.customer_name}}</td>
                            <td>{{item.suggestion}}</td>
                            <td>{{item.fname}} {{item.lname}}</td>
                            <td>{{formatDate(item.created_on)}}</td>
                            <td>
                              <button class="btn btn-xs" data-toggle="modal" data-target="#editSuggestionModal" @click="setSuggestionDetails(item.id, item.suggestion)">Edit</button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div v-else>
                      <p><font size="3">No suggestions found</font></p>
                    </div>
                </div>
              </div>

              <!-- Edit Suggestion Modal -->
              <div class="modal fade" id="editSuggestionModal" tabindex="-1" role="dialog" aria-labelledby="editSuggestionModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editSuggestionModalLabel">Update Suggestion</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group">
                        <label for="suggestion"><b>Suggestion</b> <font color="red">*</font></label>
                        <textarea class="form-control" type="text" v-model="suggestion"></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" id="editSuggestionBtn" @click="updateSuggestion">Update</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal -->
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/suggestions.js"></script>