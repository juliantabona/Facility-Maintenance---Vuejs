<template>

    <Card :style="{ width: '100%' }">

            <!-- Loader for when loading the chart information -->
            <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading chart...</Loader>

            <!-- Chart Data -->
            <canvas v-show="!isLoading" id="chart"></canvas>

    </Card>

</template>

<script>

    /*  Loaders  */
    import Loader from './../../components/_common/loaders/Loader.vue';

    /*  Chart.js  */
    import Chart from 'chart.js';

    export default {
        props:{
            modelId: {
                type: [Number, String],
                default: null
            },
            modelType: {
                type: String,
                default: null
            },
            allocation: {
                type: String,
                default: 'company'
            },
            count: {
                type: [Number, String],
                default: null
            },
            groupBy: {
                type: String,
                default: null
            },
            chartLabel: {
                type: String,
                default: ''
            },
            chartType: {
                type: String,
                default: 'bar'
            },
            chartHeight: {
                type: Number,
                default: 200
            },
            chartOptions: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                isLoading: false,
                fetchedChartDetails: []
            }
        },
        components: { Loader },
        computed: {
            chartLabels: function(){
                if(this.fetchedChartDetails.length){
                    return this.fetchedChartDetails.map(chart => chart.group);
                }
                
            },
            chartNumbers: function(){
                if(this.fetchedChartDetails.length){
                    return this.fetchedChartDetails.map(chart => chart.total_activities);
                }
            },
            chartData: function(){
                 
                return [{
                    type: this.chartType,
                    data: {
                        labels: this.chartLabels,
                        datasets: [
                            {
                                label: this.chartLabel,
                                data: this.chartNumbers,
                                borderWidth: 1,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 159, 64, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255,99,132,1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                ]
                            }
                        ]
                    },
                    //  Return the available chart options otherwise return the default configurations
                    options: this.chartOptions || {
                        scaleShowValues: true,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    autoSkip: false
                                }
                            }]
                        }
                    }
                }][0];
            }
        },
        methods: {
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting activity statistics...');

                var modelId = this.modelId ? '&modelId=' + this.modelId : '';
                var modelType = this.modelType ? '&modelType=' + this.modelType : '';
                var allocation = this.allocation ? '&allocation=' + this.allocation : '';
                var count = this.count ? '&count=' + this.count : '';
                var groupBy = this.groupBy ? '&groupBy=' + this.groupBy : '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/recentactivities?'+modelId + modelType + allocation + count + groupBy)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;
                        
                        //  Get the data
                        self.fetchedChartDetails = data.data;

                        console.log('heres the data');
                        console.log(data);
                       
                       //   Create the chart
                       self.createChart('chart', self.chartData);
                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        console.log('invoiceActivityChartWidget.vue - Error getting activity statistics...');
                        console.log(response);    
                    });
            },
            createChart(chartId, chartData) {
                const ctx = document.getElementById(chartId);
                ctx.height = this.chartHeight;
                const myChart = new Chart(ctx, {
                    type: chartData.type,
                    data: chartData.data,
                    options: chartData.options,
                });
            }
        },
        mounted() {
            this.fetch();
        }
    }
</script>