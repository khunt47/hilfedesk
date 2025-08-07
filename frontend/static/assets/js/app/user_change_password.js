var app = new Vue({
    el: '#change-password',
    data: {
        currentPassword: '',
        newPassword: '',
        newPasswordConfirm: '',
        apiBaseUrl: ''
    },
    mounted: function() {
    },
    methods: {
        changePassword: function() {
            var self = this

            if (self.currentPassword === '' || self.currentPassword === null) {
                Swal.fire('Error!','Current password cannot be empty','error')
                return false
            }
            else if (self.currentPassword.length < 8) {
                Swal.fire('Error!','Current password is too short. There should be minimum 8 characters','error')
                return false
            }
            else if (self.newPassword === '' || self.newPassword === null) {
                Swal.fire('Error!','New password cannot be empty','error')
                return false
            }
            else if (self.newPassword.length < 8) {
                Swal.fire('Error!','New password is too short. There should be minimum 8 characters','error')
                return false
            }
            else if (self.currentPassword === self.newPassword) {
                Swal.fire('Error!','Current and new password cannot be the same','error')
                return false
            }
            else if (self.newPasswordConfirm === '' || self.newPasswordConfirm === null) {
                Swal.fire('Error!','Confirm new password cannot be empty','error')
                return false
            }
            else if (self.newPasswordConfirm.length < 8) {
                Swal.fire('Error!','Confirm new password is too short. There should be minimum 8 characters','error')
                return false
            }
            else if (self.newPassword !== self.newPasswordConfirm) {
                Swal.fire('Error!','New password does not match with confirm new password','error')
                return false
            }

            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");

            $("#changePasswordBtn").html("Changing...");
            $('#changePasswordBtn').prop('disabled', true);

                var url = self.apiBaseUrl + '/users/password/change'
                axios.post(url,
                {
                    current_password: self.currentPassword,
                    new_password: self.newPassword
                },
                {
                    headers:
                    {
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.currentPassword = ''
                            self.newPassword = ''
                            self.newPasswordConfirm = ''
                            Swal.fire('Success!',response.data.message,'success')
                        }
                        else {
                            Swal.fire('Error!',response.data.errors,'error')
                        }
                        $("#changePasswordBtn").html("Change password");
                        $('#changePasswordBtn').prop('disabled', false);
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                        $("#changePasswordBtn").html("Change password");
                        $('#changePasswordBtn').prop('disabled', false);
                    })

        }

    } //methods end

})
