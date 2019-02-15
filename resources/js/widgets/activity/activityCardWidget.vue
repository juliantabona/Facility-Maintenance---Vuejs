<template>

    <Card :style="{ width: '100%' }">

        <!-- Loader for when loading the chart information -->
        <Loader v-if="isLoading" :loading="isLoading" type="text" :style="{ marginTop:'40px' }">Loading stats...</Loader>

        <!-- Activity Number Card -->
        <Row v-else :gutter="20">
            <Col :span="24/(statisticLabels.length)" v-for="(item, i) in statisticLabels" :key="i">
                
                <moneyAndCounterCard :title="statisticLabels[i]" :amount="statisticGrandTotal[i]" :count="statisticTotalCount[i]" 
                                     :currency="baseCurrency" :showZero="false" class="mb-2" 
                                     :route="{ name: 'invoices', query: { status: ( statisticLabels[i] ) } }"
                                     :active="statisticLabels[i] == $route.query.status"
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
                baseCurrency: null,

                //  store is a global custom class found in store.js
                //  We use it to access data accessible globally
                //  In this case we need to access the data
                //  allocationType to know whether the user
                //  wants company/branch related data
                allocationType: store.allocationType,
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

                //  The allocationType: Whether to get company/branch specific data
                var allocationType = this.allocationType ? '&&allocation='+this.allocationType : ''; 

                console.log('Start getting card activity statistics...');

                if( this.url ){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api'+this.url+'?'+allocationType)
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
        created () {

            //  Fetch the statistics
            this.fetch();

            //  Listen for global changes on the allocation type. 
            //  The reource is used to reflect which data we want to get.
            //  It may be the users company/branch specific data.

            var self = this;

            Event.$on('updatedAllocationType', function(updatedAllocationType){
                
                //  Get the updated allocationType e.g) company/branch
                self.allocationType = updatedAllocationType;

                //  Fetch the statistics
                self.fetch();
                
            });
        },
        beforeDestroy() {
            //  Stop listening for global changes on the allocation type.
            Event.$off('updatedAllocationType');
        }
    }
</script>