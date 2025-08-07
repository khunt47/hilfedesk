var app = new Vue({
    el: '#validate-admin', 
    data: {
        isAdmin: 'no'
    }, 
    mounted: function() {
       this.checkIfUserAdmin()
    }, 
    methods: {
        checkIfUserAdmin: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");

            var url = self.apiBaseUrl + '/is-user-admin'
            axios.post(url, {},
            {
                headers:
                {
                    "Authorization" : 'Bearer '+self.userToken
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    self.isAdmin = response.data.data.is_admin
                    localStorage.setItem("hd_is_admin", self.isAdmin);
                    if (self.isAdmin === 'no') {
                        var redirectUrl = '/tickets/all'
                        window.location.replace(redirectUrl);
                    }
                    else {
                    }
                }
                else {
                    self.isAdmin = 'no'
                }
            })
            .catch(function (error) {
                self.isAdmin = 'no'
            })
        }
        
    } //methods end    
})