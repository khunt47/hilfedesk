var app = new Vue({
    el: '#trip-advisor', 
    data: {
        tripAdvisorLink: '',
        oldtripAdvisorLink: ''

    },  
    mounted: function() {
       this.getTripAdvisorLink()
    }, 
    methods: {       
        getTripAdvisorLink: function() {
            var self = this

            var url = 'api/v1/admin/tripadvisor-link'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.tripAdvisorLink    = response.data   
                    self.oldtripAdvisorLink = response.data     
                }
                else 
                {
                   
                    self.tripAdvisorLink = ''
                }
            })
            .catch(function (error) {
                self.tripAdvisorLink = ''
                console.log('error')
            })
        },
        saveTripAdvisorLink: function() {
            var self = this
        
            if (self.tripAdvisorLink ===  self.oldtripAdvisorLink) 
            {
                Swal.fire("Message",'No changes done','info');
                return false;
            }
            else if (self.tripAdvisorLink === "" || self.tripAdvisorLink === null) 
            {
                Swal.fire("Error",'Chat Id cannot be empty','error');
                return false;
            }

            var url = 'api/v1/admin/tripadvisor-link/save'
            axios.post(url, {
                trip_advisor_link : self.tripAdvisorLink
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.getTripAdvisorLink()
                    
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