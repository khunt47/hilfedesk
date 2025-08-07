var app = new Vue({
    el: '#logout',
    data: {
        baseUrl: '',
        apiBaseUrl: '',
        userToken: '',
        deviceId: '',
        loginPage: ''
    },
    mounted: function() {
        this.logoutUser()
    },
    methods: {
        logoutUser: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.baseUrl = self.$refs.base_url.value
            self.loginPage = self.$refs.login_page.value
            localStorage.removeItem("hd_is_admin");
            localStorage.removeItem("hd_user_token");
            var redirectUrl = self.baseUrl + self.loginPage
            window.location.replace(redirectUrl);
        }
        // logoutUser: function() {
        //     var self = this
        //     self.apiBaseUrl = self.$refs.api_base_url.value
        //     self.baseUrl = self.$refs.base_url.value
        //     self.userToken = localStorage.getItem("hd_user_token");
        //     self.deviceId = localStorage.getItem("hd_device_id");
        //     var url = self.apiBaseUrl + '/logout'
        //         axios.post(url,
        //         {
        //             device_id: self.deviceId
        //         },
        //         {
        //             headers:
        //             {
        //                 "Authorization" : 'Bearer '+self.userToken
        //             }
        //         })
        //             .then(function (response) {
        //                 if (response.data.success) {
        //                     localStorage.removeItem("hd_is_admin");
        //                     localStorage.removeItem("hd_user_token");
        //                     var redirectUrl = self.baseUrl + 'login'
        //                     window.location.replace(redirectUrl);
        //                     //Swal.fire('Success!',response.data.message,'success')
        //                 }
        //                 else {
        //                     Swal.fire('Error!',response.data.errors,'error')
        //                 }
        //             })
        //             .catch(function (error) {
        //                 Swal.fire('Error!','There was an error, please try again','error')
        //             })
        // }

    } //methods end

})
