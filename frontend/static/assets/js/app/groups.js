var app = new Vue({
    el: '#groups',
    data: {
        groups: [],
        groupsCount: 0,
        apiBaseUrl: '',
        showLoading: 'yes',
        userToken: ''
    },
    mounted: function() {
        this.getGroups()
    },
    methods: {
        getGroups: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/admin/groups'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.groups = response.data.data
                        self.groupsCount = self.groups.length
                    }
                    else {
                        self.groupsCount = 0
                        self.groups = []
                    }
                    self.showLoading = 'no'
                })
                .catch(function (error) {
                    self.groupsCount = 0
                    self.groups = []
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
