var app = new Vue({
    el: '#room-update', 
    data: {
        roomId: '',
        roomName:'',
        oldRoomName:''
    },  
    mounted: function() 
    {
       this.getRoomName()
    }, 
    methods: 
    {   
        getRoomName: function() {
            var self = this

            self.roomId = self.$refs.room_id.value

            var url = 'api/v1/admin/room-name?room_id=' + self.roomId
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.roomName = response.data
                    self.oldRoomName = response.data                           
                }
                else 
                {
                   
                    self.roomName = ''
                }
            })
            .catch(function (error) {
                self.roomName = ''
                console.log('error')
            })
        },
        updateRooms: function() {
            var self = this

            console.log(self.roomName)

            if  (self.roomName === self.oldRoomName) 
            {
                Swal.fire('Error!','No changes made to update','error')
                return false
            }
            else if (self.roomName === '' || self.roomName === null) {
                Swal.fire('Error!','Room name cannot be empty','error')
                return false
            }

            var url = 'api/v1/admin/rooms/edit'
            axios.post(url, {
                room_id     : self.roomId, 
                room_name   : self.roomName
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.getRoomName()
                    
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