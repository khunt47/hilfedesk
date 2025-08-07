var app = new Vue({
    el: '#ticket-details',
    data: {
        ticketId: 0,
        userToken: '',
        ticketDetails: [],
        ticketsCount: 0,
        ticketStatus: '',
        ticketPriority: '',
        ticketComments: [],
        ticketCommentsCount: 0,
        newTicketComment: '',
        newTicketStatus: '',
        newTicketPriority: '',
        ticketOwnedBy: '',
        oldTicketOwner: '',
        projectUsers: [],
        projectUsersCount: 0,
        apiBaseUrl: '',
        ticketProjectId: 0,
        linodeBaseUrl: '',
        showLoading: 'yes'
    },
    mounted: function() {
        this.getTicketDetails()
        this.getTicketComments()
    },
    methods: {
        formatDate: function(date) {
          // Convert string to Date object if needed
          const parsedDate = typeof date === 'string' ? new Date(date.replace(' ', 'T')) : date;

          const options = {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', hour12: true
          };

          const formattedDate = new Intl.DateTimeFormat('en-US', options).format(parsedDate);
          return formattedDate;
        }, 
        getTicketDetails: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.linodeBaseUrl = self.$refs.linode_base_url.value
            self.ticketId = self.$refs.ticket_id.value
            self.ticketProjectId = self.$refs.project_id.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/tickets/' + self.ticketId
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.ticketDetails = response.data.data
                        self.ticketsCount = 1
                        self.ticketStatus = self.ticketDetails.status
                        self.ticketPriority = self.ticketDetails.priority
                        self.ticketOwnedBy = self.ticketDetails.owned_by
                        //self.ticketProjectId = self.ticketDetails.project_id
                        self.getMappedUsers()
                    }
                    else {
                        self.ticketsCount = 0
                        self.ticketDetails = []
                    }
                    self.showLoading = 'no'
                })
                .catch(function (error) {
                    self.ticketsCount = 0
                    self.ticketDetails = []
                    self.showLoading = 'no'
                })
        },
        getTicketComments: function() {
            var self = this
            var url = self.apiBaseUrl + '/tickets/comments/' + self.ticketId + '/desc'
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.ticketComments = response.data.data
                        self.ticketCommentsCount = self.ticketComments.length
                    }
                    else {
                        self.ticketComments = []
                        self.ticketCommentsCount = 0
                    }
                })
                .catch(function (error) {
                    self.ticketComments = []
                    self.ticketCommentsCount = 0
                })
        },
        onTrixChange(event) {
            var self = this
            self.newTicketComment = event.target.innerHTML; // full HTML
            // Alternatively:
            // this.ticketDesc = event.target.editor.getDocument().toString(); // plain text
        },
        createTicketComment: function() {
            var self = this

            if (self.newTicketComment === '' || self.newTicketComment === null) {
                Swal.fire('Error!','Comment cannot be empty','error')
                return false
            }

            $("#saveCommentBtn").html("Saving...");
            $('#saveCommentBtn').prop('disabled', true);

            const fileInput = this.$refs.fileInput;
            var formData = new FormData();

            formData.append('ticket_id', self.ticketId);
            formData.append('new_comment', self.newTicketComment);
            formData.append('public', 'yes');

            if (fileInput.files.length > 0) {
                formData.append('files', fileInput.files[0]);
            }

                var url = self.apiBaseUrl + '/tickets/comments/add'
                axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.newTicketComment = ''
                            fileInput.value = null;
                            self.getTicketComments()
                            $.notify(response.data.message, "success");
                            document.getElementById('content').value = ''; // clears hidden input
                            document.querySelector('trix-editor').editor.loadHTML(''); // clears the visible editor
                        }
                        else {
                            $.notify(response.data.errors, "error");
                        }
                        $("#saveCommentBtn").html("Save");
                        $('#saveCommentBtn').prop('disabled', false);
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                        $("#saveCommentBtn").html("Save");
                        $('#saveCommentBtn').prop('disabled', false);
                    })
        },
        takeTicket: function() {
            var self = this

            $("#takeTicketBtn").html("Taking...");
            $('#takeTicketBtn').prop('disabled', true);

                var url = self.apiBaseUrl + '/tickets/take'
                axios.post(url,
                {
                    ticket_id: self.ticketId
                },
                {
                    headers:
                    {
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.getTicketDetails()
                            $.notify(response.data.message, "success");
                        }
                        else {
                            $.notify(response.data.errors, "error");
                        }
                        $("#takeTicketBtn").html("Take");
                        $('#takeTicketBtn').prop('disabled', false);
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                        $("#takeTicketBtn").html("Take");
                        $('#takeTicketBtn').prop('disabled', false);
                    })

        },
        changeTicketStatus: function(event) {
            var self = this
            self.newTicketStatus = event.target.value
                var url = self.apiBaseUrl + '/tickets/status/change'
                axios.post(url,
                {
                    ticket_id: self.ticketId,
                    status: self.newTicketStatus
                },
                {
                    headers:
                    {
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.getTicketDetails()
                            $.notify(response.data.message, "success");
                        }
                        else {
                            $.notify(response.data.errors, "error");
                        }
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                    })
        },
        changeTicketPriority: function(event) {
            var self = this
            self.newTicketPriority = event.target.value
                var url = self.apiBaseUrl + '/tickets/priority/change'
                axios.post(url,
                {
                    ticket_id: self.ticketId,
                    priority: self.newTicketPriority
                },
                {
                    headers:
                    {
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.getTicketDetails()
                            $.notify(response.data.message, "success");
                        }
                        else {
                            $.notify(response.data.errors, "error");
                        }
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                    })
        },
        getMappedUsers: function() {
            var self = this
            var url = self.apiBaseUrl + '/projects/mapped-users/' + self.ticketProjectId
            axios.get(url, { headers: {"Authorization" : 'Bearer '+self.userToken} })
                .then(function (response) {
                    if (response.data.status != 'failed') {
                        self.projectUsers = response.data.data
                        self.projectUsersCount = self.ticketComments.length
                    }
                    else {
                        self.projectUsers = []
                        self.projectUsersCount = 0
                    }
                })
                .catch(function (error) {
                    self.projectUsers = []
                    self.projectUsersCount = 0
                })
        },
        reassignTicket: function(event) {
            var self = this
            self.oldTicketOwner = self.ticketOwnedBy
            self.ticketOwnedBy = event.target.value

            var url = self.apiBaseUrl + '/tickets/owner/change'
                axios.post(url,
                {
                    ticket_id: self.ticketId,
                    new_owner_id: self.ticketOwnedBy
                },
                {
                    headers:
                    {
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.getTicketDetails()
                            $.notify(response.data.message, "success");
                        }
                        else {
                            $.notify(response.data.errors, "error");
                        }
                    })
                    .catch(function (error) {
                        Swal.fire('Error!','There was an error, please try again','error')
                    })
        },

    } //methods end

})
