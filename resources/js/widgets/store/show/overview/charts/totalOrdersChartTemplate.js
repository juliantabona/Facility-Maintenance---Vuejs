export const totalOrdersChartTemplate = {
  type: 'line',
  data: {
    datasets: [{
      data: [],
      fill: false,
      pointRadius: 1,
      lineTension: 0,
      label: 'Total Orders',
      borderColor: '#19be6b',
      borderWidth: 2
  
  }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    title:{
        text:'ORDERS OVER TIME',
        display: true
    },
    tooltips: {
        mode: 'point'
    },
    scales: {
        xAxes: [{
            type: 'time',
            time: {

                //  Format of date for the x-axis label
                displayFormats: {
                    'millisecond': 'DD MMM',
                    'second': 'DD MMM',
                    'minute': 'DD MMM',
                    'hour': 'DD MMM',
                    'day': 'DD MMM',
                    'week': 'DD MMM',
                    'month': 'DD MMM',
                    'quarter': 'DD MMM',
                    'year': 'DD MMM',
                },
                
                //  Format of date for the tooltip
                tooltipFormat: 'MMM DD YYYY @ HH:mm',
            },
            ticks: {
            
                //  Only show 4 ticks (unit labels) and not all of them. We use "autoSkip" and "maxTicksLimit"
                autoSkip: true,
                maxTicksLimit: 4,

                //  Do not slant the labels, keep them horizontally straight
                maxRotation: 0,  //  90
                minRotation: 0,  //  90

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
        display: true
    },
    tooltips: {
        mode: 'x',
        intersect:false
    },
    hover:{
        mode:'x',
        intersect: false
    }
  }
}

export default totalOrdersChartTemplate;