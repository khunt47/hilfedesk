var app = new Vue({
    el: '#login',
    data: {
        userName: '',
        userPwd: '',
        baseUrl: '',
        apiBaseUrl: '',
        homePage: ''
    },
    mounted: function() {
        this.autoLoginUser()
    },
    methods: {
        autoLoginUser: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.baseUrl = self.$refs.base_url.value
            self.homePage = self.$refs.home_page.value

            /*
            Later the token needs to validated with the backend.
            */
        },
        loginUser: function() {
            var self = this
            if (self.userName === '' || self.userName === null) {
                Swal.fire('Error!','Username cannot be empty','error')
                return false
            }
            else if (self.userPwd === '' || self.userPwd === null) {
                Swal.fire('Error!','Password cannot be empty','error')
                return false
            }
            $("#login-btn").html("Validating...");
            $('#login-btn').prop('disabled', true);
            var url = self.apiBaseUrl + '/login/auth'
            axios.post(url, {
                user_name: self.userName,
                user_password: self.userPwd,
                device_id: 'new'
            })
            .then(function (response) {
                if (response.data.success) {
                    //Swal.fire('Success!',response.data.message,'success')
                    var userToken = response.data.data.access_token
                    var isAdmin = response.data.data.is_admin
                    var deviceId = response.data.data.device_id
                    localStorage.setItem("hd_user_token", userToken);
                    localStorage.setItem("hd_is_admin", isAdmin);
                    localStorage.setItem("hd_device_id", deviceId);
                    var redirectUrl = self.baseUrl + self.homePage
                    window.location.replace(redirectUrl);
                }
                else {
                    Swal.fire('Error!',response.data.errors,'error')
                }
                $("#login-btn").html("Log in");
                $('#login-btn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                $("#login-btn").html("Log in");
                $('#login-btn').prop('disabled', false);
            })
        },
        togglePasswordVisibility: function() {
            var x = document.getElementById("userPswd");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

    } //methods end

})
