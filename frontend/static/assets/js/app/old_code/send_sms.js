var app = new Vue({
    el: '#send-feedback-sms', 
    data: {
        guestNumber: ''
    },  
    mounted: function() 
    {
      
    }, 
    methods: 
    {       
        sendSms: function() {
            var self = this

            if (self.guestNumber === '' || self.guestNumber === null) {
                Swal.fire('Error!','Number cannot be empty','error')
                return false
            }


            var url = 'api/v1/guest-feedback/sms'
            axios.post(url, {
                guest_number  : self.guestNumber
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.guestNumber='',

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