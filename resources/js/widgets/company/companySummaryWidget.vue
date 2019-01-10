<template>

    <div>

        <!-- Company Loader -->
        <Loader v-if="isLoading" :loading="isLoading"></Loader>

        <!-- Company Not Found Alert -->
        <notFoundAlert v-if="!companyExists && !isLoading" :message="'No '+relationType+' Found'"></notFoundAlert>

        <Card v-if="companyExists" :style="{ width: '100%' }">

            <!-- Copmany Header -->

                <!-- Copmany Title -->
                <companyHeaderTitle slot="title" v-bind="$props"></companyHeaderTitle>

                <!-- Copmany Menu -->
                <companyHeaderMenu slot="extra" v-bind="$props"></companyHeaderMenu>

            <!-- Copmany Body -->
            
            <companySummaryBody v-bind="$props"></companySummaryBody>

            <!-- Copmany Footer -->
            
            <companySummaryFooter v-bind="$props"></companySummaryFooter>
            
        </Card>

    </div>

</template>

<script>

    import Loader from './../../components/_common/loader/Loader.vue';
    import notFoundAlert from './../../components/_common/notFound/notFoundAlert.vue';

    import companyHeaderTitle from './../../components/company/header/title.vue';
    import companyHeaderMenu from './../../components/company/header/menu.vue';
    import companySummaryBody from './../../components/company/body/main.vue';
    import companySummaryFooter from './../../components/company/footer/main.vue';
    
    export default {
        components: { Loader, notFoundAlert, companyHeaderTitle, companyHeaderMenu, companySummaryBody, companySummaryFooter },
        
        /*  We create a property "company" linked to the parent v-model. We still 
        *  need to register the "company" property in the props array as seen below.
        *  After this we listen to the "updated" event usually called after
        *  we get a new company object when making an Api call. This will 
        *  update the v-model company value. As such our widget will be
        *  updated with the new company information. 
        * 
        *  Read Docs: https://vuejs.org/v2/guide/components-custom-events.html
        */
        model: {
            prop: 'company',
            event: 'updated'
        },
        props: {
            /*  Note that you can either provide a Company Object,
             *  Company Id, or Company Branch Id to build the widget
             *  If we don't have a Company Object, then we use the
             *  CompanyId/CompanyBranchId to make an Api and 
             *  retrieve the company information.
             */
            company: {              //  Linked to the v-model property of the parent element
                type: Object,
                default: null
            },
            companyId: {
                type: Number,
                default: null
            },
            companyBranchId: {
                type: Number,
                default: null
            },
            
            relationType: {         //  e.g) client, supplier
                type: String, 
                default: '',
            },

            /*  Company header related properties  */
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            showAuthourizedStatus: {
                type: Boolean,
                default: true
            },

            /*  Company body related properties  */
            showLogo: {
                type: Boolean,
                default: true
            }, 
            showName: {
                type: Boolean,
                default: true
            }, 
            showAddress: {
                type: Boolean,
                default: true
            }, 
            showCityOrTown: {
                type: Boolean,
                default: true
            }, 
            showPhone: {
                type: Boolean,
                default: true
            }, 
            showEmail: {
                type: Boolean,
                default: true
            }, 
            showContacts: {
                type: Boolean,
                default: true
            },

            /*  Company footer related properties  */
            showViewBtn: {
                type: Boolean,
                default: true
            }

        },
        data(){
            return {
                isLoading: false
            }
        },
        computed: {
            companyExists: function(){
                if(this.company){
                    return Object.keys(this.company).length;
                }

                return false;
            }
        },
        watch: {
            companyId: function (val) {
                //  Get the company using the Company Object, Company Id or Company Branch Id
                this.getCompany();
            },
            companyBranchId: function (val) {
                //  Get the company using the Company Object, Company Id or Company Branch Id
                this.getCompany();
            }
        },
        methods: {
            fetch(){

                if(this.modelId != null && this.modelType != ''){
                 
                    const self = this;

                    /*  The model type helps us know whether the associated id 
                     *  belongs to the company or company branch. This will allow 
                     *  us to make an Api that will either use the company id/branch id
                     *  to get the associated company branch
                     */

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/companies/'+this.modelId+'?model='+this.modelType)
                        .then(({data}) => {

                            //  Stop loader
                            self.isLoading = false;

                            //  Emit an status of updated to the parent v-model so that we capture 
                            //  a new company object to display.
                            self.$emit('updated', data);
                
                        })         
                        .catch(response => { 
                            console.log(response);

                            //  Stop loader
                            self.isLoading = false;
                        });

                    }
            },
            getCompany: function(){

                //  Start Loader
                this.isLoading = true;

                //  Check if the company object is empty
                if(!this.companyExists){

                   //  Check if the company id is set
                    if(this.companyId != null){
                        
                        //  Get the company id and set that this id belongs to a Company Model
                        this.modelId = this.companyId;
                        this.modelType = 'Company';

                        //  Make an Api call to get the company information
                        this.fetch();

                    //  Check if the company branch id is set
                    }else if(this.companyBranchId != null){
                        
                        //  Get the company id and set that this id belongs to a Company Branch Model
                        this.modelId = this.companyBranchId;
                        this.modelType = 'CompanyBranch';

                        //  Make an Api call to get the company information
                        this.fetch();

                    }else{
                        //  Stop loading if we don't have anything at all
                        this.isLoading = false;
                    }

                }else{
                    //  Stop loading if we have the company object
                    this.isLoading = false;
                }
            }
        },
        created(){
            //  Get the company using the Company Object, Company Id or Company Branch Id
            this.getCompany();
        }
    };
    
</script>