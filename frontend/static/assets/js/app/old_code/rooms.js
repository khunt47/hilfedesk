var app = new Vue({
	el: '#rooms', 
	data: {
     
        totalRoomsCount: 0, 
        allRooms: [],
        roomId:''
	}, 
	mounted: function() 
    {
       this.getAllRooms()
	}, 
	methods: 
    {   
        getAllRooms: function() {
            var self = this

            var url = 'api/v1/admin/rooms'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.totalRoomsCount = 1
                    self.allRooms = response.data
                }
                else 
                {
                    self.totalRoomsCount = 0
                    self.allRooms = []
                }
            })
            .catch(function (error) {
                self.totalRoomsCount = 0
                self.allRooms = []
                console.log('error')
            })
        },
        deleteRooms: function(roomId) {
            var self = this

            self.roomId = roomId

            var url = 'api/v1/admin/rooms/delete'
            axios.post(url, {
                room_id: self.roomId
            })
            .then(function (response) {
                if (response.data.status === 'success') {
                    self.getAllRooms()
                    Swal.fire('Success!',response.data.message,'success')
                }
                else {
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