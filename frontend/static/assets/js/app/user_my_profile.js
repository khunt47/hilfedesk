var app = new Vue({
    el: '#my-profile',
    data: {
        userFullName: '',
        userEmail: ''
    },
    mounted: function() {
        this.getMyProfileInfo()
    },
    methods: {
        getMyProfileInfo: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/users/my-profile'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.userFullName = response.data.data.user_fname + ' ' + response.data.data.user_lname
                        self.userEmail = response.data.data.user_email
                    }
                    else {
                        self.userFullName = ''
                        self.userEmail = ''
                    }
                    $('#overlay').fadeOut();
                })
                .catch(function (error) {
                    self.userFullName = ''
                    self.userEmail = ''
                    $('#overlay').fadeOut();
                })
        }

    } //methods end

})
