<template>

    <Card :style="{ width: '100%' }">

        <!-- Loader for when loading the chart information -->
        <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading stats...</Loader>

        <!-- Activity Number Card -->
        <Row v-else :gutter="20">
            <Col :span="24/(statisticLabels.length)" v-for="(item, i) in statisticLabels" :key="i">
                
                <moneyAndCounterCard :title="statisticLabels[i]" :amount="statisticGrandTotal[i]" :count="statisticTotalCount[i]" 
                                     :currency="baseCurrency" :showZero="false" class="mb-2" 
                                     :type="determineType(i)">
                </moneyAndCounterCard>
            
            </Col>
        </Row>

    </Card>

</template>

<script>

    /*  Loaders  */
    import Loader from './../../components/_common/loaders/Loader.vue';

    /*  Chart.js  */
    import Chart from 'chart.js';
    
    /*  Cards  */
    import moneyAndCounterCard from './../../components/_common/cards/moneyAndCounterCard.vue';

    export default {
        props:{
            url: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                isLoading: false,
                fetchedStats: [],
                baseCurrency: null
            }
        },
        components: { Loader, moneyAndCounterCard },
        computed: {
            statisticLabels: function(){
                if( (this.fetchedStats || {}).length){
                    return this.fetchedStats.map(stat => stat.name);
                }

                return [];
                
            },
            statisticTotalCount: function(){
                if( (this.fetchedStats || {}).length){
                    return this.fetchedStats.map(stat => stat.total_count);
                }

                return [];

            },
            statisticGrandTotal: function(){
                if( (this.fetchedStats || {}).length){
                    return this.fetchedStats.map(stat => stat.grand_total);
                }

                return [];

            }
        },
        methods: {
            determineType(i){
                if( this.statisticLabels[i] == 'Paid' ){
                    return 'success';
                }else if( this.statisticLabels[i] == 'Expired' ){
                    return 'error';
                }else if( this.statisticLabels[i] == 'Cancelled' ){
                    return 'warning';
                }else{
                    return 'normal';
                }
            },
            fetch() {
                const self = this;

                //  Start loader
                self.isLoading = true;

                console.log('Start getting card activity statistics...');

                if( this.url ){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api'+this.url)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;
                            
                            //  Get the data
                            self.fetchedStats = data['stats'];
                            self.baseCurrency = data['base_currency'];

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            console.log('activityCardWidget.vue - Error getting card activity statistics...');
                            console.log(response);    
                        });

                }
            }
        },
        mounted() {
            this.fetch();
        }
    }
</script>