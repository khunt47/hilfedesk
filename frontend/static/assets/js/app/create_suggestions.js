Vue.component('v-select', VueSelect.VueSelect);

var app = new Vue({
    el: '#create-suggestion',
    data: {
        userToken: '',
        apiBaseUrl: '',
        customers: [],
        customerId: 0,
        suggestion: ''
    },
    mounted: function() {
        this.autoLoad()
        this.getCustomers()
    },
    methods: {
        autoLoad: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
        },
        createSuggestions: function() {
            var self = this

            if (self.customerId === 0 || self.customerId === "" || self.customerId === null) {
                Swal.fire("Error",'Customer cannot be empty','error')
                return false
            }
            if (self.suggestion === "" || self.suggestion === null) {
                Swal.fire("Error",'Suggestions cannot be empty','error')
                return false
            }

            $("#create-btn").html("Creating...");
            $('#create-btn').prop('disabled', true);

            var url = self.apiBaseUrl + '/suggestions/create'
            axios.post(url, {
                customer_id : self.customerId,
                suggestion : self.suggestion
            },{ 
                headers: {
                    "Authorization" : 'Bearer '+self.userToken
                } 
            })
            .then(function (response) {
                if (response.data.success) {
                    Swal.fire('Success!',response.data.message,'success')
                    self.customerId = 0
                    self.suggestion = ''
                }
                else {
                    Swal.fire('Error!',response.data.error,'error')
                }
                $("#create-btn").html("Create");
                $('#create-btn').prop('disabled', false);
            })
            .catch(function(error) {
                Swal.fire('Error!',error.response.data.error,'error')
                $("#create-btn").html("Create");
                $('#create-btn').prop('disabled', false);
            })
        },
        getCustomers: function() {
            var self = this

            var url = self.apiBaseUrl + '/customers'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.customers = response.data.data
                    }
                    else {
                        self.customers = []
                    }
                })
                .catch(function (error) {
                    self.customers = []
                })
        },
        clearFields: function() {
            var self = this
            
            self.customerId = 0
            self.suggestion = ''
        }
    }
})