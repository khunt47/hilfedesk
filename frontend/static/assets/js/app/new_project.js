var app = new Vue({
    el: '#new-project',
    data: {
        projectName: '',
        projectCode: '',
        projectEmail: '',
        userToken: '',
        apiBaseUrl: ''
    },
    mounted: function() {
        this.autoLoginUser()
    },
    methods: {
        autoLoginUser: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
        },
        createProject: function() {
            var self = this

            if (self.projectName === '' || self.projectName === null) {
                Swal.fire('Error!','Project name cannot be empty','error')
                return false
            }
            else if (self.projectCode === '' || self.projectCode === null) {
                Swal.fire('Error!','Project code cannot be empty','error')
                return false
            }
            else if (self.projectCode.length > 3) {
                Swal.fire('Error!','Project code should be only 3 characters long','error')
                return false
            }
            else if (self.projectEmail === '' || self.projectEmail === null) {
                Swal.fire('Error!','Project email cannot be empty','error')
                return false
            }

            $("#createBtn").html("Creating...");
            $('#createBtn').prop('disabled', true);

                var url = self.apiBaseUrl + '/projects/create'
                axios.post(url,
                {
                    name: self.projectName,
                    code: self.projectCode.toUpperCase(),
                    email: self.projectEmail
                },
                {
                    headers:
                    {
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.projectName = ''
                            self.projectCode = ''
                            self.projectEmail = ''
                            Swal.fire('Success!',response.data.message,'success')
                        }
                        else {
                            Swal.fire('Error!',response.data.errors,'error')
                        }
                        $("#createBtn").html("Create");
                        $('#createBtn').prop('disabled', false);
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                        $("#createBtn").html("Create");
                        $('#createBtn').prop('disabled', false);
                    })
        },
        clearFields: function() {
            var self = this
            
            self.projectName = ''
            self.projectCode = ''
            self.projectEmail = ''
        }


    } //methods end

})
