var app = new Vue({
    el: '#feedback-details', 
    data: {
        feedbackId: '',
        feedbackDetails: [], 
        feedbackDt: ''
    }, 
    mounted: function() 
    {
       this.getFeedbackDetails()
    }, 
    methods: {   
        getFeedbackDetails: function() {
            var self = this

            self.feedbackId = self.$refs.feedback_id.value

            var url = 'api/v1/guest-feedback/details?feedback_id=' + self.feedbackId
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.feedbackDetails = response.data
                    self.feedbackDt = response.data.feedback_dt
                }
                else 
                {
                    self.feedbackDetails = []
                }
            })
            .catch(function (error) {
                self.feedbackDetails = []
            })
        }, 
        formatDate: function(date) {
            var self = this

            if (date === '' || date === null) 
            {
                return "-"
            }
            else 
            {
                var dateArr = date.split("-")
                return date = dateArr[2] + "/" + dateArr[1] + "/" + dateArr[0]
            }
        }

    } //methods end
    
})