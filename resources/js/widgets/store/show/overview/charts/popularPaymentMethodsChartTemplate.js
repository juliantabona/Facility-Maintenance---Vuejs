export const popularPaymentMethodsChartTemplate = {
  type: 'bar',
  data: {
    labels: ['Bar 1', 'Bar 2', 'Bar 3'],
    datasets: [
        {
            backgroundColor: '#19be6b',
            barPercentage: 0.5,
            barThickness: 15,
            maxBarThickness: 15,
            minBarLength: 2,
            data: [10, 20, 30]
        }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    title:{
        text:'Popular Payment Methods',
        display: false
    },
    tooltips: {
        mode: 'point'
    },
    scales: {
        xAxes: [{
            ticks: {

                //  Padding for the A-axis
                padding: 5

            }
        }],
        yAxes: [{
            ticks: {

                //  Always start at zero
                beginAtZero: true,

                //  Always return whole numbers not decimals
                callback: function(value) { if (value % 1 === 0) { return value; } }
            }
        }]
    },
    legend:{
        position:'bottom',
        display: false
    }
  }
}

export default popularPaymentMethodsChartTemplate;