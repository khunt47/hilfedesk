var app = new Vue({
    el: '#google-login',
    data: {
        baseUrl: '',
        apiBaseUrl: '',
        userToken: '',
        deviceId: '',
        userEmail: ''
    },
    mounted: function() {
        this.loginUser()
    },
    methods: {
        loginUser: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.baseUrl = self.$refs.base_url.value
            self.userEmail = self.$refs.user_email.value
            self.deviceId = localStorage.getItem("hd_device_id");
            if(self.deviceId === '' || self.deviceId === null) {
                self.deviceId = 'new';
            }
            var url = self.apiBaseUrl + '/login/social/google'
                axios.post(url,{
                    user_name: self.userEmail,
                    device_id: self.deviceId
                })
                    .then(function (response) {
                        if (response.data.success) {
                            var userToken = response.data.data.access_token
                            var isAdmin = response.data.data.is_admin
                            var deviceId = response.data.data.device_id
                            localStorage.setItem("hd_user_token", userToken);
                            localStorage.setItem("hd_is_admin", isAdmin);
                            localStorage.setItem("hd_device_id", deviceId);
                            var redirectUrl = self.baseUrl + 'dashboard'
                            window.location.replace(redirectUrl);
                        }
                        else {
                            Swal.fire('Error!',response.data.errors,'error')
                            var redirectUrl = self.baseUrl + 'login'
                            window.location.replace(redirectUrl);
                        }
                    })
                    .catch(function (error) {
                        var redirectUrl = self.baseUrl + 'login'
                        window.location.replace(redirectUrl);
                        Swal.fire('Error!','There was an error, please try again','error')
                    })
        }

    } //methods end

})
