var app = new Vue({
    el: '#users',
    data: {
        userToken: '',
        usersCount: 0,
        users: [],
        userDetails: [],
        apiBaseUrl: '',
        showLoading: 'yes',
        userId: 0,
        currentPassword: '',
        newPassword: '',
        confirmNewPassword: '',
        userFname: '',
        userLname: '',
        userEmail: '',
        userRole: '',
        oldUserFname: '',
        oldUserLname: '',
        oldUserEmail: '',
        oldUserRole: '',
        loggedInUserId: 0
    },
    mounted: function() {
        this.getUsers()
    },
    methods: {
        getUsers: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/admin/users'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
            .then(function (response) {
                if (response.data.success == true) {
                    self.users = response.data.data
                    self.usersCount = self.users.length
                    self.loggedInUserId = response.data.loggedin_user_id
                    self.showLoading = 'no'
                    console.log(self.showLoading)
                }
                else {
                    self.usersCount = 0
                    self.users = []
                    self.showLoading = 'no'
                }
            })
            .catch(function (error) {
                console.log('p')
                self.usersCount = 0
                self.users = []
                self.showLoading = 'no'
            })
        }, 
        setUserId: function(userId) {
            var self = this
            self.userId = userId
            self.newPassword = ''
            self.confirmNewPassword = ''
        },
        changeUserPassword: function() {
            var self = this

            if (self.userId === 0) {
                Swal.fire('Error!','User ID cannot be empty','error')
                return false
            }
            else if (self.newPassword === '' || self.newPassword === null) {
                Swal.fire('Error!','New password cannot be empty','error')
                return false
            }
            else if (self.confirmNewPassword === '' || self.confirmNewPassword === null) {
                Swal.fire('Error!','Confirm new password cannot be empty','error')
                return false
            }
            else if (self.newPassword !== self.confirmNewPassword) {
                Swal.fire('Error!','New password and confirm password does not match','error')
                return false
            }

            $("#updatePasswordBtn").html("Updating...");
            $('#updatePasswordBtn').prop('disabled', true);

            var url = self.apiBaseUrl + '/admin/users/change-password'
            axios.post(url, {
                "user_id": self.userId,
                "new_password": self.newPassword,
                "confirm_password": self.confirmNewPassword
            }, 
            {
                headers: {
                    "Authorization" : 'Bearer '+self.userToken
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    Swal.fire('Success!',response.data.message,'success')
                    self.newPassword = ''
                    self.confirmNewPassword = ''
                }
                else {
                    Swal.fire('Error!',response.data.error,'error')
                }
                $("#updatePasswordBtn").html("Update");
                $('#updatePasswordBtn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!', error.response.data.error,'error')
                $("#updatePasswordBtn").html("Update");
                $('#updatePasswordBtn').prop('disabled', false);
            })
        },
        setUserDetails: function(userId, firstName, lastName, email, userRole) {
            var self = this;

            self.userId = userId;

            self.userFname    = firstName;
            self.oldUserFname = firstName;

            self.userLname    = lastName;
            self.oldUserLname = lastName;

            self.userEmail    = email;
            self.oldUserEmail = email;

            self.userRole    = userRole;
            self.oldUserRole = userRole;
        },
        
        updateUser: function() {
            var self = this

            if (self.userId === 0) {
                Swal.fire('Error!','User ID cannot be empty','error')
                return false
            }
            else if (self.userFname === self.oldUserFname 
                && self.userLname === self.oldUserLname 
                &&self.userEmail === self.oldUserEmail
                && self.userRole === self.oldUserRole) 
            {
                Swal.fire('Error!','No changes made to update','error')
                return false
            }
            else if (self.firstName === '' || self.firstName === null) {
                Swal.fire('Error!','User first name cannot be empty','error')
                return false
            }
            else if (self.lastName === '' || self.lastName === null) {
                Swal.fire('Error','User last name cannot be empty','error')
                return false
            }
            else if (self.email === '' || self.email === null){
                Swal.fire('Error','User email cannot be empty','error')
                return false
            }

            $("#editUserBtn").html("Updating...");
            $('#editUserBtn').prop('disabled', true);

            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/admin/users/update'
            axios.post(url, {
                "user_id": self.userId,
                "fname": self.userFname,
                "lname": self.userLname,
                "email": self.userEmail,
                "role": self.userRole
            },
            {
                headers: {
                    "Authorization" : 'Bearer '+self.userToken
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    Swal.fire('Success!', response.data.message,'success')
                    self.getUsers();
                    $('#editUserModal').modal('hide');
                }
                else {
                    Swal.fire('Error!',response.data.errors,'error')
                }
                $("#editUserBtn").html("Update");
                $('#editUserBtn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                $("#editUserBtn").html("Update");
                $('#editUserBtn').prop('disabled', false);
            })
            
        },

        deleteUser: function(id) {
            var self = this

            self.userId = id;

            bootbox.confirm('Are you sure you want to delete user!', function (result){ 
                if (result) {

                    self.apiBaseUrl = self.$refs.api_base_url.value
                    self.userToken = localStorage.getItem("hd_user_token");
                    var url = self.apiBaseUrl + '/admin/users/delete';

                    axios.post(url, { "user_id": self.userId },
                        {
                            headers: {
                                "Authorization" : 'Bearer '+self.userToken
                            }
                        
                    })
                    .then(function (response) {
                        if (response.data.success) {
                            Swal.fire('Success!', response.data.message,'success')
                            self.getUsers();
                        }
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                    })
                }
            })

        },
         ticketsChart: function () {
        var self = this;

        const canvas = document.getElementById('tickets');

        if (!canvas) { 
            console.log("Canvas element with ID 'ticketsChart' not found. Chart cannot be rendered.");
            return; 
        }

        const ctx = canvas.getContext('2d');
        if(ctx) {
            console.log("Canvas element with ID 'ticketsChart' not found. Chart cannot be rendered.");
            return;
        }
         
        const myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['new', 'inprogress', 'resolved'],
                datasets: [{
                    labels: "Tickets",
                    pointRadius: 0,
                    backgroundColor: [
                        '#599E0F',
                        '#fcc468',
                        '#ef8157'
                    ],
                    borderWidth: 0,
                    data: [2, 1, 0]
                }]
            },
            options: {
                title: {
                    dispaly: true,
                    text: 'Pie chart ticket'
                },
                reposnive: true
            }

            
        })
    }   

    } //methods end

})
