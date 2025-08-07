var app = new Vue({
	el: '#custom-report', 
	data: {
        fromDate: '', 
        toDate: '', 
        reportRowsCount: 0, 
        reports: [], 
        downloadUrl: ''
	}, 
	mounted: function() {
       // this.getDashboardCards()
	}, 
	methods: {
        generateReport: function() {
            var self = this

            if (self.fromDate === '' || self.fromDate === null) 
            {
                Swal.fire('Error!','From date cannot be empty','error')
                return false
            }
            else if (self.toDate === '' || self.toDate === null) 
            {
                Swal.fire('Error!','To date cannot be empty','error')
                return false
            }
            else if (self.fromDate > self.toDate) 
            {
                Swal.fire('Error!','From date cannot be more than to date','error')
                return false
            }

            self.fetchReportData()
        },
        fetchReportData: function() {
            var self = this

            var url = 'api/v1/reports/custom?fdt=' + self.fromDate + '&tdt=' + self.toDate

            axios.get(url)
            .then(function (response) {
                if (response.data.status != 'failed') 
                {
                    self.reportRowsCount = 1
                    self.reports = response.data

                    self.downloadUrl = 'reports/custom/download?fdt=' + self.fromDate + '&tdt=' + self.toDate                    
                }
                else 
                {
                    self.reportRowsCount = 0
                    self.reports = []
                }
            })
            .catch(function (error) 
            {
                self.reportRowsCount = 0
                self.reports = []
            })
        }, 
        formatDate: function(date) {
            var self = this

            if (date === '' || date === null) 
            {
                return "-"
            }
            else 
            {
                var dateArr = date.split("-")
                return date = dateArr[2] + "/" + dateArr[1] + "/" + dateArr[0]
            }
        }

        
    } //methods end    
})