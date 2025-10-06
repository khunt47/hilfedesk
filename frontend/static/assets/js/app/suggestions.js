var app = new Vue({
    el: '#suggestions',
    data: {
        id: 0,
        suggestion: '',
        oldSuggestion: '',
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
        setSuggestionDetails: function(id, suggestion) {
            var self = this;

            self.id            = id;
            self.suggestion    = suggestion;
            self.oldSuggestion = suggestion;
        },
        updateSuggestion: function() {
            var self = this

            if (self.id === 0) {
                Swal.fire('Error!','ID cannot be empty','error')
                return false
            }
            else if (self.suggestion === self.oldSuggestion ) {
                Swal.fire('Error!','No changes made to update','error')
                return false
            }
            else if (self.suggestion === '' || self.suggestion === null) {
                Swal.fire('Error!','Suggestion cannot be empty','error')
                return false
            }

            $("#editSuggestionBtn").html("Updating...");
            $('#editSuggestionBtn').prop('disabled', true);

            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/suggestions/update'
            axios.post(url, {
                "id": self.id,
                "suggestion": self.suggestion,
            },
            {
                headers: {
                    "Authorization" : 'Bearer '+self.userToken
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    Swal.fire('Success!', response.data.message,'success')
                    self.getSuggestions();
                    $('#editSuggestionModal').modal('hide');
                }
                else {
                    Swal.fire('Error!',response.data.errors,'error')
                }
                $("#editSuggestionBtn").html("Update");
                $('#editSuggestionBtn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                $("#editSuggestionBtn").html("Update");
                $('#editSuggestionBtn').prop('disabled', false);
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
