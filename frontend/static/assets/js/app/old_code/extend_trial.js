var app = new Vue({
	el: '#extend-trial', 
	data: {
        trialPeriod: 0, 
        custId: '', 
        billingEndDt: ''
	}, 
	mounted: function() {

	}, 
	methods: {
        extendTrial: function() {
            var self = this

            self.custId = self.$refs.cust_id.value
            self.billingEndDt = self.$refs.billing_end_date.value

            if (self.trialPeriod === '' || self.trialPeriod === null) {
                Swal.fire(
                    'Error!',
                    'Trial period cannot be empty',
                    'error'
                    )
                return false
            }
            else if (self.trialPeriod === 0) {
                Swal.fire(
                    'Error!',
                    'Trial period cannot be empty',
                    'error'
                    )
                return false
            }

            var url = 'api/v1/console/customers/extend-trial'
            axios.post(url, {
                cust: self.custId, 
                trial_period: self.trialPeriod, 
                billing_end_dt: self.billingEndDt
            })
            .then(function (response) {
                if (response.data.status === 'success') {
                    Swal.fire(
                    'Success!',
                    response.data.message,
                    'success'
                    )
                }
                else {
                    Swal.fire(
                    'Error!',
                    response.data.message,
                    'error'
                    )
                    console.log('failed')
                }
            })
            .catch(function (error) {
                Swal.fire(
                    'Error!',
                    'There was an error, please try again',
                    'error'
                    )
                return false
                console.log('error')
            })
        }

    } //methods end
    
})