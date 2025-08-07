<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Timezone</h5>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="home">Home</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black" href="/admin">Admin</a></li>
              <li class="breadcrumb-item" aria-current="page"><a style="color:black">Timezone</a></li>
            </ol>
          </nav>
          <hr>
        </div>
        <div class="card-body">
         <div id="timezone">
              <input ref="api_base_url" value="<?php echo $api_base_url; ?>" hidden>
              <span v-if="showLoading === 'yes'"><h4>Loading....</h4></span>
              <span v-else>
                 <div class="form-group">
                  <label>Current Timezone:</label>
                  <p>{{ selectedTimezone || '-' }}</p>
               
                  <label for="timezone">Select Timezone:</label><br>
                  <select class="form-select" v-model="selectedTimezone">
                    <option value="">-- Select Timezone --</option>
                    <option v-for="tz in allTimezones" :key="tz.id" :value="tz.timezone">{{ tz.timezone }}</option>
                  </select>
                  <br><br>
                <!-- Update button -->
                <button class="btn btn-primary" id="update-btn" @click="updateTimezone">Update Timezone</button>
              </div>
              </span>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="static/assets/js/app/timezone.js"></script>
