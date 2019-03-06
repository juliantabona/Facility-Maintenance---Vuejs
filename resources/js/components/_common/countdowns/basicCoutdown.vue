<style>

    footer {
        position: absolute; 
        bottom: 0; 
        left: 0; 
        right: 0;
        height: 40px;
        border-radius: 0 0 8px 8px;

        /** Extra personal styles **/
        font-size:12px;
        background-color: #000;
        color: white;
        text-align: center;
        line-height: 30px;
    }

</style>

<template>

    <div><b v-if="anotherTime != textAtZero">{{ text }}</b>{{ anotherTime }}</div>

</template>


<script type="text/javascript">

    import moment from 'moment';

    export default {
        props: {
            date: {
                type: String,
                default: null
            },
            text: {
                type: String,
                default: ''
            },
            textAtZero: {
                type: String,
                default: '' 
            }
        },
        data(){
            return {
                localDate: this.date,
                dateSetTimeOut: null,
                anotherTime: ''
            }
        },
        watch: {

            //  Watch for changes on the cdate value
            date: {
                handler: function (val, oldVal) {

                    //  Update the localDate value
                    this.localDate = val;

                    //  Restart the countdown
                    this.startCountDown();

                }
            }
        },
        computed: {
            time: function(){
                return moment(this.localDate).format('mm:ss');
            }
        },
        methods: {
            startCountDown: function(){
                if( this.localDate ){

                    var eventTime= moment(this.localDate).unix();
                    var currentTime = moment().unix();
                    var diffTime = eventTime - currentTime;
                    var duration = moment.duration(diffTime*1000, 'milliseconds');
                    var interval = 1000;

                    var self = this;

                    this.dateSetTimeOut = setInterval(() => {
                        
                        duration = moment.duration(duration - interval, 'milliseconds');
                        
                        var years = ( duration.years() == 1 ? duration.years() + ' year' : duration.years() + ' years');
                        var months = ( duration.months() == 1 ? duration.months() + ' month' : duration.months() + ' months');
                        var days = ( duration.days() == 1 ? duration.days() + ' day' : duration.days() + ' days');
                        var hours = ( duration.hours() == 1 ? duration.hours() + ' hour' : duration.hours() + ' hours');
                        var minutes = ( duration.minutes() == 1 ? duration.minutes() + ' minute' : duration.minutes() + ' minutes');
                        var seconds = ( duration.seconds() == 1 ? duration.seconds() + ' second' : duration.seconds() + ' seconds');

                        if(duration.years() > 0){
                            self.anotherTime = (years + ' and ' + months);
                        }else if(duration.months() > 0){
                            self.anotherTime = (months + ' and ' + days);
                        }else if(duration.days() > 0){
                            self.anotherTime = (days + ' and ' + hours);
                        }else if(duration.hours() > 0){
                            self.anotherTime = (hours + ' and ' + minutes);
                        }else if(duration.minutes() > 0){
                            self.anotherTime = (minutes + ' and ' + seconds);
                        }else if(duration.seconds() > -1){
                            self.anotherTime = (seconds);
                        }else{
                            self.anotherTime = self.textAtZero;
                        }

                    }, interval);

                }
            },
            restartCountDown: function(){
                //  Stop the current countdown
                clearTimeout(this.dateSetTimeOut);

                //  Start a new countdown
                this.startCountDown();
            }
        },
        mounted: function(){   
            this.startCountDown();
        }
    }

</script>
