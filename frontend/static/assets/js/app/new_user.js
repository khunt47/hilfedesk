var app = new Vue({
    el: '#new-user',
    data: {
        userFname: '',
        userLname: '',
        userEmail: '',
        userPaswd: '',
        userConfPaswd: '',
        userRole: '',
        xxx: '',
        projectName: '',
        projectCode: '',
        projectEmail: '',
        userToken: '',
        apiBaseUrl: ''
    },
    mounted: function() {
        this.autoLoginUser()
    },
    methods: {
        autoLoginUser: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
        },
        getSelectedUserRole: function(event) {
            var self = this
            self.userRole = event.target.value
        }, 
        createProject: function() {
            var self = this

            if (self.userFname === '' || self.userFname === null) {
                Swal.fire('Error!','User first name cannot be empty','error')
                return false
            }
            else if (self.userLname === '' || self.userLname === null) {
                Swal.fire('Error!','User last name cannot be empty','error')
                return false
            }
            else if (self.userEmail === '' || self.userEmail === null) {
                Swal.fire('Error!','User email cannot be empty','error')
                return false
            }
            else if (self.userPaswd === '' || self.userPaswd === null) {
                Swal.fire('Error!','User password cannot be empty','error')
                return false
            }
            else if (self.userPaswd.length < 8) {
                Swal.fire('Error!','User password should be more than 8 characters','error')
                return false
            }
            else if (self.userConfPaswd === '' || self.userConfPaswd === null) {
                Swal.fire('Error!','Confirm password cannot be empty','error')
                return false
            }
            else if (self.userConfPaswd !== self.userPaswd) {
                Swal.fire('Error!','Password and confirm password are different','error')
                return false
            }

            $("#createBtn").html("Creating...");
            $('#createBtn').prop('disabled', true);

            var url = self.apiBaseUrl + '/admin/users/create'
            axios.post(url,
            {
                first_name: self.userFname,
                last_name: self.userLname,
                email: self.userEmail,
                password: self.userPaswd,
                role: self.userRole
            },
            {
                headers:
                {
                    "Authorization" : 'Bearer '+self.userToken
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    self.userFname = ''
                    self.userLname = ''
                    self.userEmail = ''
                    self.userPaswd = ''
                    self.userConfPaswd = ''
                    self.userRole = ''
                    Swal.fire('Success!',response.data.message,'success')
                }
                else {
                    Swal.fire('Error!',response.data.errors,'error')
                }
                $("#createBtn").html("Create");
                $('#createBtn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                $("#createBtn").html("Create");
                $('#createBtn').prop('disabled', false);
            })
        },
        clearFields: function() {
            var self = this
            
            self.userFname = ''
            self.userLname = ''
            self.userEmail = ''
            self.userPaswd = ''
            self.userConfPaswd = ''
            self.userRole = ''
        }

    } //methods end

})
