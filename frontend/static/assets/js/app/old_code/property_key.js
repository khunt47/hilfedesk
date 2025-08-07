var app = new Vue({
    el: '#property-key', 
    data: {
        propertyKey: ''

    },  
    mounted: function() 
    {
       this.getPropertyKey()
    }, 
    methods: 
    {   
        getPropertyKey: function() {
            var self = this

            var url = 'api/v1/admin/property-key'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.propertyKey = response.data        
                }
                else 
                {
                   
                    self.propertyKey = ''
                }
            })
            .catch(function (error) {
                self.userDetails = []
                console.log('error')
            })
        },
        generateKey: function() {
            var self = this

            var url = 'api/v1/admin/property-key/create'
            axios.post(url, {
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.getPropertyKey()
                    
                    Swal.fire('Success!',response.data.message,'success')
                }
                else 
                {
                    Swal.fire('Error!',response.data.message,'error')
                }
            })
            .catch(function (error) {
                Swal.fire('Error!','There was an error, please try again','error')
                return false
            })
        } 
        
    } //methods end    
})