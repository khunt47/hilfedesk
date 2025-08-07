var app = new Vue({
    el: '#create-rooms', 
    data: {
        roomName: ''
    },  
    mounted: function() 
    {
      
    }, 
    methods: 
    {   
           
        createRooms: function() {
            var self = this

            if (self.roomName === '' || self.roomName === null) {
                Swal.fire('Error!','Room name cannot be empty','error')
                return false
            }
            
            var url = 'api/v1/admin/rooms/create'
            axios.post(url, {
                room_name     : self.roomName
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.roomName='',
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