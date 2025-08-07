var app = new Vue({
	el: '#dashboard-cards', 
	data: {
        submittedFeedbackCount: 0, 
        occupiedRRoomsCount: 0, 
        guestScore: 0, 
        negativeScore: 0
	}, 
	mounted: function() {
       this.getDashboardCards()
	}, 
	methods: {
        getDashboardCards: function() {
            var self = this
            var url = 'api/v1/dashboard/cards'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.submittedFeedbackCount = response.data.feedback_count
                    self.occupiedRRoomsCount = response.data.occupied_rooms
                    self.guestScore = response.data.guest_score
                    self.negativeScore = response.data.negative_score
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
        }

        
    } //methods end    
})