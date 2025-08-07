var app = new Vue({
    el: '#telegram', 
    data: {
        telegramChatId: '',
        oldTelegramChatId: ''

    },  
    mounted: function() {
       this.getTelegramId()
    }, 
    methods: {       
        getTelegramId: function() {
            var self = this

            var url = 'api/v1/admin/telegram-id'
            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.telegramChatId    = response.data   
                    self.oldTelegramChatId = response.data     
                }
                else 
                {
                   
                    self.telegramChatId = ''
                }
            })
            .catch(function (error) {
                self.telegramChatId = ''
                console.log('error')
            })
        },
        createChatId: function() {
            var self = this
        
            if (self.telegramChatId ===  self.oldTelegramChatId) 
            {
                Swal.fire("Message",'No changes done','info');
                return false;
            }
            else if (self.telegramChatId === "" || self.telegramChatId === null) 
            {
                Swal.fire("Error",'Chat Id cannot be empty','error');
                return false;
            }

            var url = 'api/v1/admin/telegram-id/create'
            axios.post(url, {
                telegram_chat_id : self.telegramChatId
            })
            .then(function (response) {
                if (response.data.status === 'success') 
                {
                    self.getTelegramId()
                    
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