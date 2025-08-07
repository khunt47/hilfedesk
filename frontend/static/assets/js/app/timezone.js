var app = new Vue({
    el: '#timezone',
    data: {
        userToken: '',
        apiBaseUrl: '',
        showLoading: 'yes',
        selectedTimezone: '',     
        allTimezones: [],
        timezone: ''
    },
    mounted: function() {
        this.autoLoad();
        this.getTimezone();
        this.getAllTimezones();
    },
    methods: {
        autoLoad: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token")
        },
        getAllTimezones: function () {
            var self = this
            var url = self.apiBaseUrl + '/admin/all-timezones'
            axios.get(url, {
                headers: { "Authorization": 'Bearer ' + self.userToken }
            })
            .then(function (response) {
                if (response.data.success) {
                    self.allTimezones = response.data.data
                } else {
                    self.allTimezones = []
                }
            })
            .catch(function (error) {
                self.allTimezones = []
            })
        },
        getTimezone: function() {
            var self = this
            var url = self.apiBaseUrl + '/admin/timezone'
            axios.get(url, {
                headers: { "Authorization": 'Bearer ' + self.userToken }
            })
            .then(function (response) {
                if (response.data.success) {
                    self.timezone = response.data.data.timezone;
                    self.selectedTimezone = self.timezone;
                } else {
                    self.timezone = ''
                }
                self.showLoading = 'no'
            })
            .catch(function (error) {
                self.timezone = ''
                self.showLoading = 'no'
            })
        },
        updateTimezone: function () {
            var self = this

            if (self.selectedTimezone === self.timezone) {
                $.notify("No changes made to update");
                return false;
            }

            $("#update-btn").html("Updating Timezone...");
            $('#update-btn').prop('disabled', true);

            var url = self.apiBaseUrl + '/admin/timezone/update'
            axios.post(url, {
                timezone: self.selectedTimezone
            }, {
                headers: {
                    "Authorization": 'Bearer ' + self.userToken
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    self.getTimezone();
                    $.notify("Timezone updated successfully!");
                } else {
                    $.notify(response.data.message);
                }
                $("#update-btn").html("Update Timezone");
                $('#update-btn').prop('disabled', false);
            })
            .catch(function (error) {
                $.notify("An error occurred while updating timezone.");
                $("#update-btn").html("Update Timezone");
                $('#update-btn').prop('disabled', false);
            });
        }

    }
})