<template>

    <Row>

        <Col :span="22" :offset="1">

            <div class="pb-3 border-bottom">
                <h2>Ussd Services</h2>
            </div>

            <Row :gutter="20">

                <!-- Loading services -->
                <Col v-if="isLoadingServices" :span="8">
                
                    <!-- Show loader -->
                    <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading services...</Loader>

                </Col>

                <!-- Wants to create a service  -->
                <template v-if="wantsToCreateService && createServiceUrl">

                    <Col :span="8" class="pt-5">

                        <!-- Show create service form -->
                        <createUssdServiceForm 
                            :postURL="createServiceUrl"
                            @createSuccess="handleCreateSuccess($event)">
                        </createUssdServiceForm>

                    </Col>

                </template>

                <template v-else>

                    <!-- Show list of services -->
                    <Col v-if="(services || {}).length && !isLoadingServices" :span="8">
                    
                        <div class="mt-5 mb-3 pb-3 clearfix border-bottom">
                            <div class="mb-3">
                                
                                <Card v-for="(service, key) in services" :key="key" class="mb-2"
                                    @click.native="goToService(service)">

                                    <!-- Service name -->
                                    <span>{{ service.name }}</span>
                                    <Icon type="md-arrow-forward" :size="15" class="float-right mt-1"/>

                                </Card>
                            </div>
                        </div>

                        <!-- Add Service Button -->
                        <basicButton @click.native="wantsToCreateService = true" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Service</span>
                        </basicButton>

                    </Col>

                </template>

                <!-- Show when we don't have services -->
                <Col v-if="!(services || {}).length && !isLoadingServices" :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Add your service</h1>
                        <p class="mb-3" style="font-size:14px;">Get started by creating your first Ussd Service to display products, services, events, memberships and even process bill payments, subcriptions and instant messaging via SMS</p>

                        <!-- Add Service Button -->
                        <basicButton @click.native="createService = !createService" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Service</span>
                        </basicButton>

                    </div>
                     <span>Need help? <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
                </Col>
                
                <Col :span="16">
                    <img style="width:100%;" class="mt-4" src="/images/backgrounds/mobile-ecommerce.png">
                </Col>
            </Row>

        </Col>

    </Row>

</template>

<script>
    
    import showServiceWidget from './../show/main.vue';

    /*  Create Service Form  */
    import createUssdServiceForm from './../../../components/_common/forms/create-ussd-service/createUssdService.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    export default {
        components: {
            showServiceWidget, createUssdServiceForm, basicButton, Loader
        },
        data(){
            return {
                services: null,
                user: auth.user,
                createServiceUrl: null,
                isLoadingServices: false,
                wantsToCreateService: false
            }
        },
        methods: {
            handleCreateSuccess(service){

                //  Close the create service form
                this.wantsToCreateService = false;

                //  Go to the service
                this.goToService(service);

            },
            goToService(service){
                
                var url = ((service._links || {}).self || {}).href;

                this.$router.push({ name: 'show-ussd-service', params: { url: encodeURIComponent(url) } });
            },
            fetchServices() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingServices = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting services...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', this.user._links['oq:ussd_services'].href)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoadingServices = false;

                        //  Service the services data
                        self.services = ((data || {})._embedded || {}).ussd_services || [];

                        //  Service the create service url (This is the URL used to make a POST Request to when creating a service)
                        self.createServiceUrl = (((data || {})._links || {}).self || {}).href;

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingServices = false;

                        //  Console log Error Location
                        console.log('dashboard/service/show/main.vue - Error getting services...');

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            
            //  Fetch the service
            this.fetchServices();

        }
    };
  
</script>