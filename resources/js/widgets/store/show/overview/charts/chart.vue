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

        <canvas id="cursor"></canvas>
        <canvas id="tooltip"></canvas>
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
            
        }
    },
    methods: {
        createChart() {

            //  Current vu instance
            const self = this;

            //  Get the chart canvas
            const ctx = $('#'+self.chartId);

            //  Create the chart
            let chart = new Chart(ctx, {

                //  Set the chart type
                type: self.chartData.type,

                //  Get the chart data
                data: self.chartData.data,

                //  Get the chart options
                options: self.chartData.options

            });

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
        this.resizeCanvas();
        this.createVerticalLine();

    }
}
</script>