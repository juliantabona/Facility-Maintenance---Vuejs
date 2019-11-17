<template>

    <Row :gutter="20">

        <Col v-if="activeStoreUrl">
            
            <storeOverview :storeUrl="activeStoreUrl" @goBack="fetchStores()"></storeOverview>

        </Col>

        <Col v-else :span="20" :offset="2">

            <div class="pb-3 border-bottom">
                <h2>Stores</h2>
            </div>

            <Row :gutter="20">

                <!-- Show when we have stores -->
                <Col v-if="isLoadingStores" :span="8">
                
                    <!-- Loader -->
                    <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading stores...</Loader>

                </Col>

                <template v-if="wantsToCreateStore && createStoreUrl">

                    <Col :span="8" class="pt-5">

                        <!-- Create Store Form -->
                        <createStoreForm 
                            :postURL="createStoreUrl"
                            @createSuccess="handleCreateSuccess($event)">
                        </createStoreForm>

                    </Col>

                </template>

                <template v-else>

                    <!-- Show when we have stores -->
                    <Col v-if="(stores || {}).length && !isLoadingStores" :span="8">
                    
                        <div class="mt-5 mb-3 pb-3 clearfix border-bottom">
                            <div class="mb-3">
                                
                                <Card v-for="(store, key) in stores" :key="key"
                                    @click.native="activeStoreUrl = ((store._links || {}).self || {}).href"
                                    class="mb-2">
                                    <span>{{ store.name }}</span>
                                    <Icon type="md-arrow-forward" :size="15" class="float-right mt-1"/>
                                </Card>
                            </div>
                        </div>

                        <!-- Add Store Button -->
                        <basicButton @click.native="wantsToCreateStore = true" 
                                        size="large" class="float-left mb-3 mr-1">
                                        <span>+ Add Store</span>
                        </basicButton>

                    </Col>

                </template>

                <!-- Show when we don't have stores -->
                <Col v-if="!(stores || {}).length && !isLoadingStores" :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Add your store</h1>
                        <p class="mb-3" style="font-size:14px;">Get started by creating your first store to sell products, services, events, memberships and even collect donations.</p>

                        <!-- Add Store Button -->
                        <basicButton @click.native="createStore = !createStore" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Store</span>
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
    
    import storeOverview from './../overview/main.vue';

    /*  Create Store Form  */
    import createStoreForm from './../../../components/_common/forms/create-store/createStore.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            storeOverview, createStoreForm, basicButton, Loader
        },
        data(){
            return {
                stores: null,
                user: auth.user,
                createStoreUrl: null,
                activeStoreUrl: null,
                isLoadingStores: false,
                wantsToCreateStore: false
            }
        },
        methods: {
            handleCreateSuccess(store){
                this.wantsToCreateStore = false;
                this.activeStoreUrl = ((store._links || {}).self || {}).href;
            },
            fetchStores() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoadingStores = true;

                //  Make sure we are not displaying a store
                self.activeStoreUrl = null;

                //  Console log to acknowledge the start of api process
                console.log('Start getting stores...');

                //  Additional data to eager load along with the store found
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', this.user._links['oq:stores'].href)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoadingStores = false;

                        //  Store the stores data
                        self.stores = ((data || {})._embedded || {}).stores || [];

                        //  Store the create store url
                        self.createStoreUrl = (((data || {})._links || {}).self || {}).href;

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoadingStores = false;

                        //  Console log Error Location
                        console.log('dashboard/store/show/main.vue - Error getting stores...');

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            //  Fetch the store
            this.fetchStores();
        }
    };
  
</script>