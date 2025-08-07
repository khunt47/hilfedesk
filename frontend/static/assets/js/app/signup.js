var app = new Vue({
    el: '#signup',
    data: {
        userFName: '',
        userLName: '',
        userName: '',
        userOrg: '',
        baseUrl: '',
        apiBaseUrl: '',
        password: ''
    },
    mounted: function() {
        this.autoLoadInfo()
    },
    methods: {
        autoLoadInfo: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.baseUrl = self.$refs.base_url.value
            var deviceType = self.findDeviceType()

            /*
            Later the token needs to validated with the backend.
            */
        },
        findDeviceType: function() {
            const detectDeviceType = () =>
              /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
                navigator.userAgent
              )
                ? 'Mobile'
                : 'Desktop';
            return detectDeviceType();
        },
        signupUser: function() {
            var self = this
            if (self.userFName === '' || self.userFName === null) {
                Swal.fire('Error!','First name cannot be empty','error')
                return false
            }
            else if (self.userLName === '' || self.userLName === null) {
                Swal.fire('Error!','Last name cannot be empty','error')
                return false
            }
            else if (self.userName === '' || self.userName === null) {
                Swal.fire('Error!','Email address cannot be empty','error')
                return false
            }
            else if (self.userOrg === '' || self.userOrg === null) {
                Swal.fire('Error!','Organization cannot be empty','error')
                return false
            }
            else if (self.password === '' || self.password === null) {
                Swal.fire('Error!','Password cannot be empty','error')
                return false
            }
            else if (self.password < 8) {
                Swal.fire('Error!','Password cannot be less than 8 characters','error')
                return false
            }
            $("#signupBtn").html("Signing up...");
            $('#signupBtn').prop('disabled', true);
            var url = self.apiBaseUrl + '/signup/register'
            axios.post(url, {
                first_name: self.userFName,
                last_name: self.userLName,
                org_name: self.userOrg,
                email_addr: self.userName,
                password: self.password
            })
            .then(function (response) {
                if (response.data.success) {
                    //Swal.fire('Success!',response.data.message,'success')
                    /*
                    login user
                    */
                    var userToken = response.data.data.access_token
                    var isAdmin = response.data.data.is_admin
                    var deviceId = response.data.data.device_id
                    localStorage.setItem("hd_user_token", userToken);
                    localStorage.setItem("hd_is_admin", isAdmin);
                    localStorage.setItem("hd_device_id", deviceId);
                    var redirectUrl = self.baseUrl + 'home'
                    window.location.replace(redirectUrl);
                }
                else {
                    Swal.fire('Error!',response.data.errors,'error')
                }
                $("#signupBtn").html("Signup");
                $('#signupBtn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                $("#signupBtn").html("Signup");
                $('#signupBtn').prop('disabled', false);
            })
        }

    } //methods end

})
