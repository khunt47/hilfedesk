var app = new Vue({
    el: '#headerapp', 
    data: {
        isAdmin: 'no'
    }, 
    mounted: function() {
       this.checkIfUserAdmin()
    }, 
    methods: {
        checkIfUserAdmin: function() {
            var self = this
            self.isAdmin = localStorage.getItem("hd_is_admin");
            console.log(self.isAdmin)
        }

        
    } //methods end    
})