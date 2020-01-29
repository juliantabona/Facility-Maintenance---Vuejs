<style scoped>

    #legend {
        text-align: center;
        display: none;
    }
    #legend li {
        display: inline-block;
        margin: 0 5px;
    }
    #legend li span {
        display: block;
        height: 10px;
        margin-bottom: 5px;
    }
    .chart-container:hover >>> #cursor{
        opacity:1;
        transition: opacity 0.3s ease;
        -webkit-transition: opacity 0.3s ease;
    }
    #cursor{
        opacity:0;
        margin: 0 auto;
        transition: opacity 0.3s ease;
        -webkit-transition: opacity 0.3s ease;
    }
    #cursor:hover{
        opacity:1;
    }
    #cursor,
    #tooltip {
        position: absolute;
    }
    .chart-instance {
        position: relative;
    }

</style>

<template>

    <div :class="chartId+'-container chart-container'">

        <template v-if="['line'].includes(chartData.type)">
            <canvas id="cursor"></canvas>
            <canvas id="tooltip"></canvas>
        </template>
        
        <canvas :id="chartId" class="chart-instance" :width="width" :height="height"></canvas>

    </div>

</template>

<script>

//  Import Chart Js
import Chart from 'chart.js';

export default {
    props:{
        chartId: {
            type: String,
            default: 'default-chart'
        },
        width: {
            type: Number,
            default: null
        },
        height: {
            type: Number,
            default: 300
        },
        update: {
            type: Boolean,
            default: false
        },
        chartData: {
            type: Object,
            default: function(){
                return {
                    type: 'line',
                    data: {
                        labels: ['January', 'February', 'March'],
                        datasets: [{
                            label: 'Default Chart Data',
                            data: [10, 20, 30]
                        }]
                    },
                    options: {

                        //  Default chart options

                    }
                }
            }
        }
    },
    data(){
        return {
            chartInstance: null
        }
    },
    watch: {

        //  Watch for changes on the update
        update: {
            handler: function (val, oldVal) {

                //  If the update property is set to true
                if(val == true){

                    //  Update the current chart
                    this.updateChart();

                }

            },
            deep: true
        }

    },
    methods: {
        updateChart(){

            console.log('this.chartData.type');
            console.log(this.chartData.type);
            console.log('this.chartData.data');
            console.log(this.chartData.data);

            //  Update the chart
            this.chartInstance['type'] = this.chartData.type;
            this.chartInstance['data'] = this.chartData.data;
            this.chartInstance['options'] = this.chartData.options;

            this.chartInstance.update();

        },
        createChart() {

            //  Current vu instance
            const self = this;

            //  Get the chart canvas
            const ctx = $('#'+self.chartId);

            //  Create the chart
            this.chartInstance = new Chart(ctx, {

                //  Set the chart type
                type: self.chartData.type,

                //  Get the chart data
                data: self.chartData.data,

                //  Get the chart options
                options: self.chartData.options

            });
                
            this.resizeCanvas();

            this.createVerticalLine();

        },
        createVerticalLine(){

            //  Current vue instance
            const self = this;

            $(document).ready(function(){

                 //  Create vertical line
                $('#'+self.chartId).on("mousemove", function(evt) {
                    var element = $("."+self.chartId+"-container #cursor"), 
                        offsetLeft = element.offset().left,
                        domElement = element.get(0),
                        clientX = parseInt(evt.clientX - offsetLeft),
                        ctx = element.get(0).getContext('2d');
                    
                    ctx.clearRect(0, 0, domElement.width, domElement.height),
                    ctx.beginPath(),
                    ctx.moveTo(clientX, 0),
                    ctx.lineTo(clientX, domElement.height),
                    ctx.setLineDash([5, 5]),
                    ctx.strokeStyle = "#808695",
                    ctx.stroke()
                });

            });

        },
        resizeCanvas(){

            //  Current vue instance
            const self = this;

            //  When the document is ready
            $(document).ready(function(){

                //  Get the chart canvas width
                var chartWidth = $('#'+self.chartId).width();
                //  Get the chart canvas height
                var chartHeight = $('#'+self.chartId).height();

                //  Resize the cursor canvas to the same size as the chart canvas width and height
                $("."+self.chartId+"-container #cursor").attr({ width: chartWidth, height: chartHeight });

                //  Resize the tooltip canvas to the same size as the chart canvas width and height
                $("."+self.chartId+"-container #tooltip").attr({ width: chartWidth, height: chartHeight });

            });

        }
    },
    mounted() {

        this.createChart();

    }
}
</script>