var app = new Vue({
    el: '#create-customer',
    data: {
        userToken: '',
        apiBaseUrl: '',
        name: '', 
        ltcrmId: 0,
        geedeskId: 0
    },
    mounted: function() {
        //no mounted function
    },
    methods: {
        createCustomer: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");

            if(self.name === "" || self.name === null) {
                Swal.fire("Error",'Name cannot be empty','error')
                return false
            }
            $("#create-btn").html("Creating...");
            $('#create-btn').prop('disabled', true);

            var url = self.apiBaseUrl + '/customers/create'
            axios.post(url, {
                cust_name : self.name,
                ltcrm_id: self.ltcrmId,
                geedesk_id: self.geedeskId
            },{ 
                headers: {
                    "Authorization" : 'Bearer '+self.userToken
                } 
            })
            .then(function (response) {
                if(response.data.success) {
                    Swal.fire('Success!',response.data.message,'success')
                    self.name = ''
                }
                else {
                    Swal.fire('Error!',response.data.message,'error')
                }
                $("#create-btn").html("Create");
                $('#create-btn').prop('disabled', false);
            })
            .catch(function(error) {
                Swal.fire('Error!',error.response.data.error,'error')
                $("#create-btn").html("Create");
                $('#create-btn').prop('disabled', false);
                return false
            })
        },
        clearFields: function() {
            var self = this
            
            self.name = ''
            self.ltcrmId = 0
            self.geedeskId = 0

          }
    } //methods end
})