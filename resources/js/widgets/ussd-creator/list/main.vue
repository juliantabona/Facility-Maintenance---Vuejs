<template>

    <Row>

        <Col :span="24" v-if="activeUssdCreatorUrl">

            <!-- Show Ussd Creator widget -->
            <showUssdCreatorsWidget :ussdCreatorUrl="activeUssdCreatorUrl" :ussdCreators="ussdCreators"
                @changeUssdCreator="activeUssdCreatorUrl = $event" 
                @goBack="fetchUssdCreators()">
            </showUssdCreatorsWidget>

        </Col>

        <Col v-else :span="22" :offset="1">

            <div class="pb-3 border-bottom">
                <h2>Ussd Creators</h2>
            </div>

            <Row :gutter="20">

                <!-- Loading ussd creators -->
                <Col v-if="isLoadingUssdCreators" :span="8">
                
                    <!-- Show loader -->
                    <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading creators...</Loader>

                </Col>

                <!-- Wants to create a new ussd creator  -->
                <template v-if="wantsToCreateUssdCreator && createUssdCreatorUrl">

                    <Col :span="8" class="pt-5">

                        <!-- Show create ussd creator form -->
                        Refer to the store/list/main.vue to get an idea of how to setup the
                        creator form in order to create a new ussd creator. 

                    </Col>

                </template>

                <template v-else>

                    <!-- Show list of ussd creators -->
                    <Col v-if="(ussdCreators || {}).length && !isLoadingUssdCreators" :span="8">
                    
                        <div class="mt-5 mb-3 pb-3 clearfix border-bottom">
                            <div class="mb-3">
                                
                                <Card v-for="(ussdCreator, key) in ussdCreators" :key="key" class="mb-2"
                                    @click.native="activeUssdCreatorUrl = ((ussdCreator._links || {}).self || {}).href">

                                    <!-- Ussd creator name -->
                                    <span>{{ ussdCreator.name }}</span>
                                    <Icon type="md-arrow-forward" :size="15" class="float-right mt-1"/>

                                </Card>
                            </div>
                        </div>

                        <!-- Add Ussd Creator Button -->
                        <basicButton @click.native="wantsToCreateUssdCreator = true" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Project</span>
                        </basicButton>

                    </Col>

                </template>

                <!-- Show when we don't have ussd creators -->
                <Col v-if="!(ussdCreators || {}).length && !isLoadingUssdCreators" :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Add your Ussd Creator</h1>
                        <p class="mb-3" style="font-size:14px;">Get started by creating your first Ussd Creator project to offer your products and services to consumers offline.</p>

                        <!-- Add Project Button -->
                        <basicButton @click.native="createUssdCreator = !createUssdCreator" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Project</span>
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
    
    import showUssdCreatorsWidget from './../show/main.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    export default {
        components: {
            showUssdCreatorsWidget, basicButton, Loader
        },
        data(){
            return {
                ussdCreators: null,
                user: auth.user,
                createUssdCreatorUrl: null,
                activeUssdCreatorUrl: null,
                isLoadingUssdCreators: false,
                wantsToCreateUssdCreator: false
            }
        },
        methods: {
            handleCreateSuccess(ussdCreator){

                //  Close the create ussd creator form
                this.wantsToCreateUssdCreator = false;

                //  Set the active ussd creator url to the url of the ussd creator we just created
                this.activeUssdCreatorUrl = ((ussdCreator._links || {}).self || {}).href;

            },
            fetchUssdCreators() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingUssdCreators = true;

                //  Make sure we are not displaying any ussd creator
                self.activeUssdCreatorUrl = null;

                //  Console log to acknowledge the start of api process
                console.log('Start getting ussd creators...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', this.user._links['oq:ussd_creators'].href)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoadingUssdCreators = false;

                        //  Store the list of ussd creators
                        self.ussdCreators = ((data || {})._embedded || {}).ussd_interfaces || [];

                        //  Store the ussd creator url (This is the URL used to make a POST Request to when creating a ussd creator)
                        self.createUssdCreatorUrl = (((data || {})._links || {}).self || {}).href;

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingUssdCreators = false;

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            
            //  Fetch the ussd creators
            this.fetchUssdCreators();

        }
    };
  
</script>