var app = new Vue({
    el: '#holidays',
    data: {
        holidays: [],
        holidaysCount: 0,
        apiBaseUrl: '',
        showLoading: 'yes',
        userToken: ''
    },
    mounted: function() {
        this.getHolidays()
    },
    methods: {
        getHolidays: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/admin/holidays'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.holidays = response.data.data
                        self.holidaysCount = self.holidays.length
                    }
                    else {
                        self.holidaysCount = 0
                        self.holidays = []
                    }
                    self.showLoading = 'no'
                })
                .catch(function (error) {
                    self.holidaysCount = 0
                    self.holidays = []
                    self.showLoading = 'no'
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
