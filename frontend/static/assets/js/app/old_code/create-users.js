var app = new Vue({
    el: '#create-users', 
    data: {
        userName: '',
        userPhone:'',
        userPassword:'',
        userCnfPassword:'',
        userFname:'',
        userLname:'',
        userRole:'',
        selectedUserRole:''
    },  
    mounted: function() 
    {
      
    }, 
    methods: 
    {   

        getSelectedRole: function(event) {
            var self = this
            self.selectedUserRole = event.target.value
        },    
        createUsers: function() {
            var self = this

            if (self.userName === '' || self.userName === null) {
                Swal.fire('Error!','Username cannot be empty','error')
                return false
            }
            else if (self.userPhone === '' || self.userPhone === null) {
                Swal.fire('Error!','Phone number cannot be empty','error')
                return false
            }
            else if (self.userPassword === '' || self.userPassword === null) {
                Swal.fire('Error!','Password cannot be empty','error')
                return false
            }
            else if (self.userCnfPassword === '' || self.userCnfPassword === null) {
                Swal.fire('Error!','Confirm password cannot be empty','error')
                return false
            }
            else if (self.userPassword != self.userCnfPassword) {
                Swal.fire('Error!','Passwords not matching','error')
                return false
            }
            else if (self.userFname === '' || self.userFname === null) {
                Swal.fire('Error!','Firstname cannot be empty','error')
                return false
            }
            else if (self.userLname === '' || self.userLname === null) {
                Swal.fire('Error!','Lastname cannot be empty','error')
                return false
            }
            else if (self.userRole === '' || self.userRole === null) {
                Swal.fire('Error!','User role cannot be empty','error')
                return false
            }

            var url = 'api/v1/admin/users/create'
            axios.post(url, {
                user_name     : self.userName, 
                user_phone    : self.userPhone, 
                user_password : self.userPassword,
                user_fname    : self.userFname, 
                user_lname    : self.userLname, 
                user_role     : self.selectedUserRole
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.userName='',
                    self.userPhone='',
                    self.userPassword='',
                    self.userCnfPassword='',
                    self.userFname='',
                    self.userLname='',
                    self.userRole='',
                    Swal.fire('Success!',response.data.message,'success')
                }
                else 
                {
                    Swal.fire('Error!',response.data.message,'error')
                }
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                return false
            })
        }

    } //methods end    
})