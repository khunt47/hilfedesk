var app = new Vue({
	el: '#guest-feedback', 
	data: {
        totalFeedbackCount: 0, 
        allFeedbacks: [],
        feedbackId:''
	}, 
	mounted: function() 
    {
       this.getAllFeedbacks()
	}, 
	methods: 
    {   
        getAllFeedbacks: function() {
            var self = this

            var url = 'api/v1/guest-feedbacks'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.totalFeedbackCount = 1
                    self.allFeedbacks = response.data
                }
                else 
                {
                    self.totalFeedbackCount = 0
                    self.allFeedbacks = []
                }
            })
            .catch(function (error) {
                self.totalFeedbackCount = 0
                self.allFeedbacks = []
                console.log('error')
            })
        },
        capitaliseWord: function(word) {
            var wordCapital = word.charAt(0).toUpperCase() + word.slice(1)
            return wordCapital
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