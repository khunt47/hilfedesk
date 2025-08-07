var app = new Vue({
    el: '#tickets',
    data: {
        userToken: '',
        ticketsCount: 0,
        tickets: [],
        projectId: 0,
        apiBaseUrl: '',
        ticketPriority: '',
        ticketStatus: '',
        projectName: '',
        showLoading: 'yes'
    },
    mounted: function() {
        this.getTickets()
    },
    methods: {
        getTickets: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            self.projectId = self.$refs.project_id.value
            var url = self.apiBaseUrl + '/projects/tickets/' + self.projectId + '?status=all&priority=all'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.tickets = response.data.data
                        self.ticketsCount = self.tickets.length
                        self.projectName = self.tickets[0].project_name
                        // for (var i = 0; self.tickets.length < 0; i++) {
                        //      = self.tickets[i].project_name
                        //     // console.log(self.projectName)
                        // }
                    }
                    else {
                        self.ticketsCount = 0
                        self.tickets = []
                    }
                    self.showLoading = 'no'
                })
                .catch(function (error) {
                    self.ticketsCount = 0
                    self.tickets = []
                    self.showLoading = 'no'
                })
        },
        priorityFilter: function(event) {
            var self = this
            self.ticketPriority = event.target.value
        },
        statusFilter: function(event) {
            var self = this
            self.ticketStatus = event.target.value
        },
        applyFilter: function() {
            var self = this
            if (self.ticketPriority === '') {
                self.ticketPriority = 'all'
            }
            if (self.ticketStatus === '') {
                self.ticketStatus = 'all'
            }
            var url = self.apiBaseUrl + '/projects/tickets/filter/' + self.projectId + '?status=' + self.ticketStatus + '&priority=' + self.ticketPriority
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.tickets = response.data.data
                        self.ticketsCount = self.tickets.length
                    }
                    else {
                        self.ticketsCount = 0
                        self.tickets = []
                    }
                    $('#overlay').fadeOut();
                })
                .catch(function (error) {
                    self.ticketsCount = 0
                    self.tickets = []
                    $('#overlay').fadeOut();
                })
        }, 
        clearFilter: function() {
            var self = this
            self.ticketPriority = ''
            self.ticketStatus = ''
            self.getTickets()
        }, 
        capitalizeFirstLetter: function (string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }, 
        formatDate: function(date) {
            let dateArray = date.split(" ");
            let actualDate = dateArray[0]
            dateArray = actualDate.split("-")
            console.log()
            let formatedDate = dateArray[2] + '-' + dateArray[1] + '-' + dateArray[0]
            return formatedDate
        }

    } //methods end

})
