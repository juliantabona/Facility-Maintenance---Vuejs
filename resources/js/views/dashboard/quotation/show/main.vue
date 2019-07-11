<template>

    <Row :gutter="20">
        
        <Col v-if="isLoading" span="8" offset="8">
            <!-- Loader -->
            <Loader :loading="true" type="text" class="text-left" theme="white">Loading quotation...</Loader>
        </Col>

        <Col v-else span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :fallbackRoute="{ name: 'quotations' }"></pageToolbar>

            <!-- Get the quotation details -->
            <quotationSummaryWidget :quotation="quotation" :key="renderKey"></quotationSummaryWidget>

        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Widgets   */
    import quotationSummaryWidget from './../../../../widgets/quotation/show/main.vue';


    export default {
        components: { 
          Loader, pageToolbar, quotationSummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                quotation: null,
                isLoading: false,
            }
        },
        watch: {
            //  Watch for changes on the quotation id
            '$route.params.id': function (id) {
                
                // react to route changes by fetching the associated quotation...
                this.fetchQuotation();

            }
        },
        methods: {
            fetchQuotation() {

                //  If we have the route id set
                if( this.$route.params.id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    console.log('Start getting quotation details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/quotations/'+this.$route.params.id)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            //  Store the quotation data
                            self.quotation = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            //  Error Location
                            console.log('dashboard/quotation/show/main.vue - Error getting quotation details...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            },
            renderComponent: function(){
                //  Re-render the component
                this.renderKey++;
            }
        },
        created(){
            //  Fetch the quotation
            this.fetchQuotation();
        }
    };
</script>