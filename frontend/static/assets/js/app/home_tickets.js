var app = new Vue({
    el: '#tickets',
    data: {
        userToken: '',
        ticketsCount: 0,
        tickets: [],
        projectId: 0,
        apiBaseUrl: '',
        baseUrl: '',
        ticketPriority: '',
        ticketStatus: '',
        projectName: '',
        showLoading: 'yes',
        showTableLoading: 'no',
        currentPage: 1,
        perPage: 20,
        projects: [],
        projectsCount: 0

    },
    mounted: function() {
        this.getTickets()
        this.getProjects()
    },
    methods: {
        formatDate: function(date) {
          //Using date.js library
          var dateTime = new Date(date);
          var formatedDate = dateTime.toString("MMMM dS, yyyy  HH:mm");
          return formatedDate;
        },
        getTickets: function() {
            var self = this
            var apiResults = ''
            self.showTableLoading = 'yes'
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.baseUrl = self.$refs.base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            if (self.userToken === '' || self.userToken === null) {
                var redirectUrl = self.baseUrl + 'login'
                window.location.replace(redirectUrl);
            }
            var url = self.apiBaseUrl + '/tickets?status=all&priority=all&page='+self.currentPage
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.success) {
                        apiResults = response.data.data
                        self.tickets = apiResults.data
                        self.ticketsCount = apiResults.total
                    }
                    else {
                        self.ticketsCount = 0
                        self.tickets = []
                    }
                    self.showLoading = 'no'
                    self.showTableLoading = 'no'
                })
                .catch(function (error) {
                    self.ticketsCount = 0
                    self.tickets = []
                    self.showLoading = 'no'
                    self.showTableLoading = 'no'
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
            self.showLoading = 'yes'
            if (self.ticketPriority === '') {
                self.ticketPriority = 'all'
            }
            if (self.ticketStatus === '') {
                self.ticketStatus = 'all'
            }
            var url = self.apiBaseUrl + '/tickets/filter?project_id=' + self.projectId + '&status=' + self.ticketStatus + '&priority=' + self.ticketPriority
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
                    self.showLoading = 'no'
                })
                .catch(function (error) {
                    self.ticketsCount = 0
                    self.tickets = []
                    self.showLoading = 'no'
                })
        }, 
        clearFilter: function() {
            var self = this
            self.ticketPriority = ''
            self.ticketStatus = ''
            self.projectId = 0
            self.tickets = []
            self.getTickets()
        }, 
        capitalizeFirstLetter: function (string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }, 
        onPageChange(page) {
          var self = this
          // Update current page and fetch data for the new page
          self.currentPage = page;
          self.showTableLoading = 'yes'
          self.getTickets();
        }, 
        getProjects: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/projects'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.projects = response.data.data
                        self.projectsCount = self.projects.length
                    }
                    else {
                        self.projectsCount = 0
                        self.projects = []
                    }
                })
                .catch(function (error) {
                    self.projectsCount = 0
                    self.projects = []
                })
        },
        statusProject: function(event) {
            var self = this
            self.projectId = event.target.value
        },

    } //methods end

})
