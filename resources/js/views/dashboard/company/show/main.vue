<template>

    <Row :gutter="20">
        
        <Col v-if="!company" span="20" offset="2">
            <!-- Loader -->
            <Loader v-if="true" :loading="true" type="text" class="text-left" theme="white">Loading company...</Loader>
        </Col>

        <Col v-else span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar 
                :showBackBtn="true"
                :fallbackRoute="{ name: 'companies', params: { id: company.id } }">

            </pageToolbar>

            <!-- Get the company details -->
            <companySummaryWidget v-if="company" :company="company" :key="renderKey"></companySummaryWidget>
            
        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Widgets   */
    import companySummaryWidget from './../../../../widgets/company/show/main.vue';


    export default {
        components: { 
          Loader, pageToolbar, companySummaryWidget
        },
        data(){
            return {
                renderKey: 1,
                company: null,
                isLoading: false,
            }
        },
        watch: {
            //  Watch for changes on the company id
            '$route.params.id': function (id) {
                
                // react to route changes by fetching the associated company...
                this.fetchInvoice();

            }
        },
        methods: {
            fetchInvoice() {

                //  If we have the route id set
                if( this.$route.params.id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoading = true;

                    console.log('Start getting company details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.$route.params.id)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoading = false;

                            //  Store the company data
                            self.company = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoading = false;

                            //  Error Location
                            console.log('dashboard/company/show/main.vue - Error getting company details...');

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
            //  Fetch the company
            this.fetchInvoice();
        }
    };
</script>