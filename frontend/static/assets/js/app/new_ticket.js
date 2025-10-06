Vue.component('v-select', VueSelect.VueSelect);

var app = new Vue({
    el: '#new-ticket',
    data: {
        projectId: 0,
        ticketTitle: '',
        ticketDesc: '',
        ticketPriority: 'low',
        contactFName: '',
        contactLName: '',
        contactEmail: '',
        customers: [],
        customerId: 0,
        userToken: '',
        apiBaseUrl: '',
        custEmail: '',
        baseUrl: '',
        projects: [],
        projectsCount: 0,
        contacts: [],
        contact_input: true,
        customer_input: true,
        customerName: '',
        crmCustomerId: 0,
        geedeskCompanyId: 0,
        contactId: 0
    },
    mounted: function() {
        this.autoLoginUser()
        this.getProjects()
        this.getCustomers()
        this.getContacts()
    },
    methods: {
        autoLoginUser: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            self.baseUrl = self.$refs.base_url.value
        },
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
                })
                .catch(function (error) {
                    self.projectsCount = 0
                    self.projects = []
                })
        },
        getCustomers: function() {
            //pending
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
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
        setProjectId: function(event) {
            var self = this
            self.projectId = event.target.value
             console.log(self.projectId);
            return;
        },
        setCustomerId: function(event) {
            var self = this
            self.customerId = event.target.value
            self.contactId = 0;
            self.getContacts();
        },
        setContactId: function(event) {
            var self = this
            // self.contactId = event.target.value
            self.contactId = parseInt(event.target.value);
        },
        createCustomerInput: function() {
            var self = this
            self.customer_input = false
            self.contact_input  = false
        },
        openContactInput: function() {
            var self = this
            self.contact_input = false
        },
        getContacts: function() {
            var self = this
            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/customers/contacts?customer_id=' + self.customerId
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
        onTrixChange(event) {
            var self = this
            // self.ticketDesc = event.target.innerHTML; // full HTML
            // Alternatively:
            this.ticketDesc = event.target.editor.getDocument().toString(); // plain text
        },
        createTicket: function() {
            var self = this

            if (self.projectId === 0) {
                $.notify('Product cannot be empty', "error");
                return false
            }
            else if (self.ticketTitle === '' || self.ticketTitle === null) {
                $.notify('Ticket title cannot be empty', "error");
                return false
            }
            else if (self.contact_input && (!self.contactId || Number(self.contactId) === 0)) {
                $.notify('Please select a contact', "error");
                return false;
            }
            else if (self.customer_input === false) {
                if (self.customerName === '' || self.customerName === null) {
                    $.notify('Customer name cannot be empty', "error");
                    return false
                }
            }
            else if (self.contact_input === false) {
                if (self.contactFName === '' || self.contactFName === null) {
                    $.notify('Contact first name cannot be empty', "error");
                    return false
                }
                else if (self.contactLName === '' || self.contactLName === null) {
                    $.notify('Contact last name cannot be empty', "error");
                    return false
                }
                else if (self.contactEmail === '' || self.contactEmail === null) {
                    $.notify('Contact email cannot be empty', "error");
                    return false
                }
            }
            else if (self.ticketDesc === '' || self.ticketDesc === null) {
                $.notify('Ticket description cannot be empty', "error");
                return false
            }
            else if (self.ticketPriority === '' || self.ticketPriority === null) {
                $.notify('Ticket priority cannot be empty', "error");
                return false
            }

            $("#createBtn").html("Creating...");
            $('#createBtn').prop('disabled', true);

            const fileInput = this.$refs.fileInput;
            var formData = new FormData();
            // Append form data
            formData.append('project_id', self.projectId);
            formData.append('heading', self.ticketTitle);
            formData.append('description', self.ticketDesc);
            formData.append('priority', self.ticketPriority);
            formData.append('customer_id', self.customerId);
            formData.append('contact_fname', self.contactFName);
            formData.append('contact_lname', self.contactLName);
            formData.append('contact_email', self.contactEmail);
            formData.append('customer_name', self.customerName);
            formData.append('contact_id', self.contactId);
            formData.append('crm_customer_id', self.crmCustomerId);
            formData.append('geedesk_company_id', self.geedeskCompanyId);
            formData.append('source', 'web');

            if (fileInput.files.length > 0) {
                formData.append('files', fileInput.files[0]);
            }
                var url = self.apiBaseUrl + '/tickets/create'
                axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        "Authorization" : 'Bearer '+self.userToken
                    }
                })
                    .then(function (response) {
                        if (response.data.success) {
                            self.ticketTitle = ''
                            self.ticketDesc = ''
                            self.contactFName = ''
                            self.contactLName = ''
                            self.contactEmail = ''
                            fileInput.value = null;
                            var newTicketId = response.data.data.id
                            var redirectUrl = self.baseUrl + 'tickets/details/' + self.projectId + '/' + newTicketId
                            window.location.replace(redirectUrl);
                            $.notify(response.data.message, "success");
                        }
                        else {
                            $.notify(response.data.message, "error");
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
        setPriority: function(event) {
            var self = this
            self.ticketPriority = event.target.value
        },
        clearFields: function() {
            var self = this
            
            self.projectId = 0
            self.customerId = 0
            self.ticketTitle = ''
            self.ticketPriority = 'low'
            self.contactFName = ''
            self.contactLName = ''
            self.contactEmail = ''
            self.ticketDesc = ''
        }


    } //methods end

})
