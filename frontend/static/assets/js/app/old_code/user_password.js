var app = new Vue({
    el: '#change-password', 
    data: {
        userId: '',
        newPassword:'',
        cnfPassword:''
        

    },  
    mounted: function() 
    {
      
    }, 
    methods: 
    {        
        changePassword: function() {
            var self = this

             self.userId = self.$refs.user_id.value

            if (self.newPassword === '' || self.newPassword === null) {
                Swal.fire('Error!','Password cannot be empty','error')
                return false
            }
            else if (self.cnfPassword === '' || self.cnfPassword === null) {
                Swal.fire('Error!','Confirm Password cannot be empty','error')
                return false
            }
            else if (self.newPassword != self.cnfPassword) {
                Swal.fire('Error!','Passwords not matching','error')
                return false
            }

            var url = 'api/v1/admin/change-password'
            axios.post(url, {
                user_id     : self.userId, 
                password    : self.newPassword
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.newPassword='',
                    self.cnfPassword='',
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