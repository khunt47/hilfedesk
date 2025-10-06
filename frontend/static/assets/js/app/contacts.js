var app = new Vue({
    el: '#contacts',
    data: {
        userToken: '',
        contactsCount: 0,
        contacts: [],
        customers: [],
        apiBaseUrl: '',
        contactId: 0,
        contactFname: '',
        contactLname: '',
        contactEmail: '',
        contactPhone: '',
        contactMobile: '',
        contactOrganization: '',
        oldContactFname: '',
        oldContactLname: '',
        oldContactEmail: '',
        oldContactPhone: '',
        oldcontactMobile: '',
        oldContactOrganization:''
    },
    mounted: function() {
        this.getContacts()
        this.getCustomers()
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
        setContactDetails: function(contactId, firstName, lastName, email, phone, mobile, customerId) {
            var self = this;

            self.contactId = contactId;

            self.contactFname    = firstName;
            self.oldContactFname = firstName;

            self.contactLname    = lastName;
            self.oldContactLname = lastName;

            self.contactEmail    = email;
            self.oldContactEmail = email;

            self.contactPhone    = phone;
            self.oldContactPhone = phone;

            self.contactMobile    = mobile;
            self.oldContactMobile = mobile;

            self.contactOrganization = customerId;
            self.oldContactOrganization = customerId;
        },
        getCustomers: function() {
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
        updateContact: function() {
            var self = this

            if (self.contactId === 0) {
                Swal.fire('Error!','Contact ID cannot be empty','error')
                return false
            }
            else if (self.contactFname === self.oldContactFname 
                && self.contactLname === self.oldContactLname 
                &&self.contactEmail === self.oldContactEmail
                &&self.contactPhone === self.oldContactPhone
                && self.contactMobile === self.oldContactMobile
                && self.contactOrganization === self.oldContactOrganization) 
            {
                Swal.fire('Error!','No changes made to update','error')
                return false
            }
            else if (self.contactFname === '' || self.contactFname === null) {
                Swal.fire('Error!','Contact first name cannot be empty','error')
                return false
            }
            else if (self.contactLname === '' || self.contactLname === null) {
                Swal.fire('Error','Contact last name cannot be empty','error')
                return false
            }
            else if (self.contactEmail === '' || self.contactEmail === null){
                Swal.fire('Error','Contact email cannot be empty','error')
                return false
            }
            else if (self.contactMobile === '' || self.contactMobile === null) {
                Swal.fire('Error','Contact mobile cannot be empty','error')
                return false
            }
            else if (!/^\d{10}$/.test(self.contactMobile)) {
                Swal.fire('Error!', 'Mobile number must be exactly 10 digits', 'error');
                return false;
            }

            $("#editContactBtn").html("Updating...");
            $('#editContactBtn').prop('disabled', true);

            self.apiBaseUrl = self.$refs.api_base_url.value
            self.userToken = localStorage.getItem("hd_user_token");
            var url = self.apiBaseUrl + '/contacts/update'
            axios.post(url, {
                "contact_id" : self.contactId,
                "fname"      : self.contactFname,
                "lname"      : self.contactLname,
                "email"      : self.contactEmail,
                "phone"      : self.contactPhone,
                "mobile"     : self.contactMobile,
                "customer_id": self.contactOrganization,
            },
            {
                headers: {
                    "Authorization" : 'Bearer '+self.userToken
                }
            })
            .then(function (response) {
                if (response.data.success) {
                    Swal.fire('Success!', response.data.message,'success')
                    self.getContacts();
                    $('#editContactModal').modal('hide');
                }
                else {
                    Swal.fire('Error!',response.data.errors,'error')
                }
                $("#editContactBtn").html("Update");
                $('#editContactBtn').prop('disabled', false);
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                $("#editContactBtn").html("Update");
                $('#editContactBtn').prop('disabled', false);
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
