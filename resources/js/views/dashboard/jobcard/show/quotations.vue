<template>

    <Row :gutter="20">
        
        <Col v-if="!company" span="20" offset="2">
            <!-- Loader -->
            <Loader :loading="true" type="text" class="text-left" theme="white">Loading quotations...</Loader>
        </Col>

        <Col v-else>

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar 
                :showBackBtn="true"
                :fallbackRoute="{ name: 'show-company', params: { id: company.id } }">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px', fontSize:'1.5rem' }" type="ios-cash-outline"></Icon>
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">Quotations</h1>
                </template>

            </pageToolbar>

            <!-- Get the summary header to display the company #, status, due date, amount due and menu options -->
            <overview 
                v-if="company.has_approved"
                :company="company" 
                :editMode="false" 
                :createMode="false"
                @toggleEditMode="">
            </overview>
            
            <!-- Create Quotation button -->
            <basicButton @click.native="$router.push({ name:'create-quotation', query: { clientId: company.id } })" size="large" class="float-right mb-2">
                + Create Quotation
            </basicButton>

            <div class="clearfix"></div>

            <!-- Get the quotation activity cards -->
            <activityCardWidget 
                :url="'/quotations/stats?companyId='+company.id" 
                :routePath="'/companies/'+company.id+'/quotations'"
                :isMoneyList="['Converted', 'Unconverted', 'Expired', 'Sent', 'Approved', 'Draft']">
            </activityCardWidget>

            <!-- Get the filterable quotation list -->
            <quotationListWidget :companyId="company.id"></quotationListWidget>

        </Col>

    </Row>

</template>

<script>

    /*  Loaders   */
    import Loader from './../../../../components/_common/loaders/Loader.vue'; 

    /*  Toolbars   */
    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';

    /*  Buttons  */
    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    import overview from './../../../../widgets/company/show/overview.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import quotationListWidget from './../../../../widgets/quotation/list/quotationListWidget.vue';


    export default {
        components: { 
          Loader, pageToolbar, basicButton, overview, activityCardWidget, quotationListWidget
        },
        data(){
            return {
                company: null,
                isLoadingQuotation: false,
            }
        },
        watch: {
            //  Watch for changes on the company id
            '$route.params.id': function (id) {
                
                // react to route changes by fetching the associated company...
                this.fetchCompany();

            }
        },
        methods: {
            fetchCompany() {

                //  If we have the route id set
                if( this.$route.params.id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingQuotation = true;

                    console.log('Start getting company details...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.$route.params.id)
                        .then(({data}) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isLoadingQuotation = false;

                            //  Store the company data
                            self.company = data;

                            //  Re-render the component
                            self.renderComponent();

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingQuotation = false;

                            //  Error Location
                            console.log('dashboard/company/show/main.vue - Error getting company details...');

                            //  Log the responce
                            console.log(response);    
                        });

                }
            }
        },
        created(){
            //  Fetch the company
            this.fetchCompany();
        }
    };
</script>