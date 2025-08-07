var app = new Vue({
    el: '#send-feedback-email', 
    data: {
        guestEmail: ''
    },  
    mounted: function() 
    {
      
    }, 
    methods: 
    {       
        sendEmail: function() {
            var self = this

            if (self.guestEmail === '' || self.guestEmail === null) {
                Swal.fire('Error!','Email cannot be empty','error')
                return false
            }


            var url = 'api/v1/guest-feedback/email'
            axios.post(url, {
                guest_email  : self.guestEmail
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.guestEmail='',

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