<template>

    <Row>

        <Col :span="22" :offset="1">

            <div class="pb-3 border-bottom">
                <h2>Creators</h2>
            </div>

            <Row :gutter="20">

                <!-- Loading creators -->
                <Col v-if="isLoadingCreators" :span="8">
                
                    <!-- Show loader -->
                    <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading creators...</Loader>

                </Col>

                <!-- Wants to create a creator  -->
                <template v-if="wantsToCreateCreator && createCreatorUrl">

                    <Col :span="8" class="pt-5">

                        <!-- Show create creator form -->
                        <createCreatorForm 
                            :postURL="createCreatorUrl"
                            @createSuccess="handleCreateSuccess($event)">
                        </createCreatorForm>

                    </Col>

                </template>

                <template v-else>

                    <!-- Show list of creators -->
                    <Col v-if="(creators || {}).length && !isLoadingCreators" :span="8">
                    
                        <div class="mt-5 mb-3 pb-3 clearfix border-bottom">
                            <div class="mb-3">
                                
                                <Card v-for="(creator, key) in creators" :key="key" class="mb-2"
                                    @click.native="goToCreator(creator)">

                                    <!-- Creator name -->
                                    <span>{{ creator.name }}</span>
                                    <Icon type="md-arrow-forward" :size="15" class="float-right mt-1"/>

                                </Card>
                            </div>
                        </div>

                        <!-- Add Creator Button -->
                        <basicButton @click.native="wantsToCreateCreator = true" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Creator</span>
                        </basicButton>

                    </Col>

                </template>

                <!-- Show when we don't have creators -->
                <Col v-if="!(creators || {}).length && !isLoadingCreators" :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Add your creator</h1>
                        <p class="mb-3" style="font-size:14px;">Get started by creating your first Ussd Creator to display products, services, events, memberships and even process bill payments, subcriptions and instant messaging via SMS</p>

                        <!-- Add Creator Button -->
                        <basicButton @click.native="createCreator = !createCreator" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Creator</span>
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
    
    import showCreatorWidget from './../show/main.vue';

    /*  Create Creator Form  */
    import createCreatorForm from './../../../components/_common/forms/create-creator/createCreator.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    export default {
        components: {
            showCreatorWidget, createCreatorForm, basicButton, Loader
        },
        data(){
            return {
                creators: null,
                user: auth.user,
                createCreatorUrl: null,
                isLoadingCreators: false,
                wantsToCreateCreator: false
            }
        },
        methods: {
            handleCreateSuccess(creator){

                //  Close the create creator form
                this.wantsToCreateCreator = false;

                //  Go to the creator
                this.goToCreator(creator);

            },
            goToCreator(creator){
                
                var url = ((creator._links || {}).self || {}).href;

                this.$router.push({ name: 'show-ussd-creator', params: { url: encodeURIComponent(url) } });
            },
            fetchCreators() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingCreators = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting creators...');

                //  Use the api call() function located in resources/js/api.js
                api.call('get', this.user._links['oq:ussd_creators'].href)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoadingCreators = false;

                        //  Creator the creators data
                        self.creators = ((data || {})._embedded || {}).ussd_interfaces || [];

                        //  Creator the create creator url (This is the URL used to make a POST Request to when creating a creator)
                        self.createCreatorUrl = (((data || {})._links || {}).self || {}).href;

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingCreators = false;

                        //  Console log Error Location
                        console.log('dashboard/creator/show/main.vue - Error getting creators...');

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            
            //  Fetch the creator
            this.fetchCreators();

        }
    };
  
</script>