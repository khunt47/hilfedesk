var app = new Vue({
    el: '#projects',
    data: {
        userToken: '',
        projectsCount: 0,
        projects: [],
        apiBaseUrl: '',
        showLoading: 'yes',
        projectId: 0,
        projectName: '',
        projectCode: '',
        projectEmail: '',
        oldProjectName: '',
        oldProjectCode: '',
        oldProjectEmail: ''
    },
    mounted: function() {
        this.getProjects()
    },
    methods: {
        getProjects: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/projects'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.projects = response.data.data
                        self.projectsCount = self.projects.length
                    }
                    else {
                        self.projectsCount = 0
                        self.projects = []
                    }
                    self.showLoading = 'no'
                })
                .catch(function (error) {
                    self.projectsCount = 0
                    self.projects = []
                    self.showLoading = 'no'
                })
        },
        getProjectDetails: function(projectId, name, projectCode, email) {
            var self = this

            self.projectId           = projectId
            
            self.projectName         = name
            self.oldProjectName      = name

            self.projectCode         = projectCode
            self.oldProjectCode      = projectCode

            self.projectEmail        = email
            self.oldProjectEmail     = email
        },
        updateProject: function() {
            var self = this

            if (self.project_id === 0) {
                Swal.fire("Error", "Project Id cannot be empty", 'error')
                return false
            }
            else if (self.projectName === self.oldProjectName
                && self.projectCode === self.oldProjectCode
                && self.projectEmail === self.oldProjectEmail) 
            {
                Swal.fire("Error", "No changes made to update", "error")
                return false
            }
            else if (self.projectName == '' || self.projectName == null) {
                Swal.fire("Error", "Project name cannot be empty", 'error')
                return false
            }
            else if (self.projectCode == '' || self.projectCode == null) {
                Swal.fire("Error", "Project code cannot be empty", "error")
                return false
            }
            else if (self.projectCode.length > 3) {
                Swal.fire("Error", "Project code should be only 3 characters long", "error")
                return false
            }
            else if (self.projectEmail == '' || self.projectEmail == null) {
                Swal.fire("Error", "Project email cannot be empty", "error")
                return false
            }

            $("#updateProjectBtn").html("Updating...");
            $('#updateProjectBtn').prop('disabled', true);

            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/projects/edit'
            axios.post(url, {
                "project_id": self.projectId,
                "project_name": self.projectName,
                "project_code": self.projectCode,
                "project_email": self.projectEmail
            },
            { 
                headers: {"Authorization" : 'Bearer '+self.userToken} 
            })
            .then(function(response) {
                if (response.data.success) {
                    Swal.fire("Success", response.data.message, "success")
                    self.getProjects();
                    $('#updateProjectModal').modal('hide');
                }
                else {
                    Swal.fire('Error!',response.data.errors,'error')
                }
                $("#updateProjectBtn").html("Update");
                $('#updateProjectBtn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                $("#updateProjectBtn").html("Update");
                $('#updateProjectBtn').prop('disabled', false);
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
