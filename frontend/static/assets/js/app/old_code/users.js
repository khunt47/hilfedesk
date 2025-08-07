var app = new Vue({
	el: '#users', 
	data: {
     
        totalUsersCount: 0, 
        allUsers: [],
        curUserId:'',
        userId:''
	}, 
	mounted: function() 
    {
       this.getAllUsers()
	}, 
	methods: 
    {   
        getAllUsers: function() {
            var self = this

            self.curUserId = self.$refs.user_id.value

            var url = 'api/v1/admin/users'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.totalUsersCount = 1
                    self.allUsers = response.data
                }
                else 
                {
                    self.totalUsersCount = 0
                    self.allUsers = []
                }
            })
            .catch(function (error) {
                self.totalUsersCount = 0
                self.allUsers = []
                console.log('error')
            })
        },
        suspendUsers: function(userId) {
            var self = this

            self.userId = userId

            var url = 'api/v1/admin/users/suspend'
            axios.post(url, {
                user_id: self.userId
            })
            .then(function (response) {
                if (response.data.status === 'success') {
                    self.getAllUsers()
                    Swal.fire('Success!',response.data.message,'success')
                }
                else {
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