var app = new Vue({
    el: '#suggestions',
    data: {
        userToken: '',
        suggestionsCount: 0,
        showLoading: 'yes',
        suggestions: [],
        apiBaseUrl: '',
    },
    mounted: function() {
        this.getSuggestions()
    },
    methods: {
        getSuggestions: function() {
            var self = this

            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");

            var url = self.apiBaseUrl + '/suggestions'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.suggestions = response.data.data
                        self.suggestionsCount = self.suggestions.length
                        self.showLoading = 'no'
                    }
                    else {
                        self.suggestionsCount = 0
                        self.suggestions = []
                    }
                    $('#overlay').fadeOut();
                })
                .catch(function (error) {
                    self.suggestionsCount = 0
                    self.suggestions = []
                    $('#overlay').fadeOut();
                    return false;
                })
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
