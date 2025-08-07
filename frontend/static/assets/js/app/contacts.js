var app = new Vue({
    el: '#contacts',
    data: {
        userToken: '',
        contactsCount: 0,
        contacts: [],
        apiBaseUrl: '',
    },
    mounted: function() {
        this.getContacts()
    },
    methods: {
        getContacts: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/contacts'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.contacts = response.data.data
                        self.contactsCount = self.contacts.length
                    }
                    else {
                        self.contactsCount = 0
                        self.contacts = []
                    }
                    $('#overlay').fadeOut();
                })
                .catch(function (error) {
                    self.contactsCount = 0
                    self.contacts = []
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
