var app = new Vue({
    el: '#edit-user', 
    data: {
        userId: '',
        userDetails: [],
        userName:'',
        userPhone:'',
        userFname:'',
        userLname:'',
        userRole:'',
        selectedUserRole:'',
        oldUserName:'',
        oldUserPhone:'',
        oldUserFname:'',
        oldUserLname:'',
        oldUserRole:''

    },  
    mounted: function() 
    {
       this.getUserDetails()
    }, 
    methods: 
    {   
        getUserDetails: function() {
            var self = this

            self.userId = self.$refs.user_id.value

            var url = 'api/v1/admin/user-details?user_id=' + self.userId
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.userDetails = response.data
                    self.userName = response.data.user_name
                    self.userPhone = response.data.phone_number
                    self.userFname = response.data.user_fname
                    self.userLname = response.data.user_lname
                    self.userRole = response.data.user_role

                    self.oldUserName = response.data.user_name
                    self.oldUserPhone = response.data.phone_number
                    self.oldUserFname = response.data.user_fname
                    self.oldUserLname = response.data.user_lname
                    self.oldUserRole = response.data.user_role
                    
                }
                else 
                {
                   
                    self.userDetails = []
                }
            })
            .catch(function (error) {
                self.userDetails = []
                console.log('error')
            })
        },
        getUserRole: function(event) {
            var self = this
            self.selectedUserRole = event.target.value
        },
        updateUsers: function() {
            var self = this

            if  (self.userName === self.oldUserName 
                && self.userPhone === self.oldUserPhone 
                && self.userFname === self.oldUserFname 
                && self.userLname === self.oldUserLname 
                && self.userRole === self.oldUserRole) 
            {
                Swal.fire('Error!','No changes made to update','error')
                return false
            }
            else if (self.userName === '' || self.userName === null) {
                Swal.fire('Error!','User name cannot be empty','error')
                return false
            }
            else if (self.userPhone === '' || self.userPhone === null) {
                Swal.fire('Error!','Phone number cannot be empty','error')
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

            var url = 'api/v1/admin/users/edit'
            axios.post(url, {
                user_id     : self.userId, 
                user_name   : self.userName, 
                user_phone  : self.userPhone, 
                user_fname  : self.userFname, 
                user_lname  : self.userLname, 
                user_role   : self.userRole
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.getUserDetails()
                    
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
        }, 
        capitaliseWord: function(word) {
            var wordCapital = word.charAt(0).toUpperCase() + word.slice(1)
            return wordCapital
        }
    } //methods end    
})