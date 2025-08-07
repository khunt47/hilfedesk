var app = new Vue({
    el: '#customers',
    data: {
        userToken: '',
        customersCount: 0,
        customers: [],
        apiBaseUrl: '',
    },
    mounted: function() {
        this.getCustomers()
    },
    methods: {
        getCustomers: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/customers'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.customers = response.data.data
                        self.customersCount = self.customers.length
                    }
                    else {
                        self.customersCount = 0
                        self.customers = []
                    }
                    $('#overlay').fadeOut();
                })
                .catch(function (error) {
                    self.customersCount = 0
                    self.customers = []
                    $('#overlay').fadeOut();
                })
        },
        setProductFilter: function(event) {
            //
        },
        applyFilter: function(event) {
            //
        }

    } //methods end

})
