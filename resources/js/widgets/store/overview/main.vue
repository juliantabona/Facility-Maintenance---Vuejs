<style scoped>

    .roundedShape{
        width: 60px;
        height: 60px;
        border: 1px solid #c5c5c5;
        padding: 3px;
        margin: -15px 10px 0 0;
        border-radius: 100%;
        display:inline-block;
    }

</style>

<template>

    <Row :gutter="20">

        <!-- Show when we have store -->
        <Col v-if="isLoadingStore" :span="12" :offset="6"> 
            <!-- Loader -->
            <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading store...</Loader>
        </Col>

        <!-- Show when we have store -->
        <Col v-if="store && !isLoadingStore" :span="22" :offset="1">
        
            <div class="mb-3 pb-3">
                <pageToolbar :fallbackRoute="{ name: 'stores' }"
                             :onlyBackBtn="true" class="d-inline-block mr-2">
                </pageToolbar>
                <img v-if="store.logo" :src="(store.logo || {}).url" alt="Store Logo" class="roundedShape">
                <h2 class="d-inline-block">{{ store.name }}</h2>
            </div>

            <div class="mt-3">
                <Card class="p-2">
                    <!-- Store Tabs -->
                    <Tabs type="card" :animated="false" class="pb-5">

                        <!-- Orders Tab -->
                        <TabPane label="Orders" class="p-1">
                            
                            <orderWidget :storeId="store.id"></orderWidget>
                        
                        </TabPane>

                        <!-- Products Tab -->
                        <TabPane label="Products" class="p-1">

                            <productWidget :storeId="store.id"></productWidget>

                        </TabPane>

                        <!-- Customers Tab -->
                        <TabPane label="Customers" class="p-1">
                            
                            <Card class="pt-3 pb-3">
                                <Alert type="info" :style="{ maxWidth: '250px' }" show-icon>No customers</Alert>
                            </Card>

                        </TabPane>

                        <!-- Transactions Tab -->
                        <TabPane label="Transactions" class="p-1">
                            
                            <Card class="pt-3 pb-3">
                                <Alert type="info" :style="{ maxWidth: '250px' }" show-icon>No transactions</Alert>
                            </Card>

                        </TabPane>

                        <!-- Settings Tab -->
                        <TabPane label="Settings" class="p-1">
                            
                            <Card class="pt-3 pb-3">
                                <span>Settings Here</span>
                            </Card>

                        </TabPane>
                        
                    </Tabs>
                </Card>
            </div>

        </Col>

        <!-- Show when we don't have store -->
        <Col v-if="!store && !isLoadingStore" :span="20" :offset="2">
            <Row :gutter="20">
                <Col :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Couldn't Find Store</h1>
                        <p class="mb-3" style="font-size:14px;">Create a new store to sell products, services, events, memberships and even collect donations.</p>

                        <!-- Add Store Button -->
                        <basicButton @click.native="$router.push({ name:'create-store' })" 
                                    size="large" class="float-left mb-3 mr-1">
                                    <span>+ Create Store</span>
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
    
    import pageToolbar from './../../../components/_common/toolbars/pageToolbar.vue';
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    /*  Widgets  */
    import orderWidget from './orderWidget.vue';
    import productWidget from './productWidget.vue';

    export default {
        components: { 
            pageToolbar, basicButton, Loader, orderWidget, productWidget
        },
        data(){
            return {

                store: null,
                storeId: this.$route.params.storeId,
                isLoadingStore: false
 
            }
        },
        methods: {
            fetchStore() {

                if(this.storeId){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingStore = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting store...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', '/api/stores/'+this.storeId)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingStore = false;

                            //  Store the store data
                            self.store = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingStore = false;

                            //  Console log Error Location
                            console.log('dashboard/store/show/main.vue - Error getting store...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the store
            this.fetchStore();
        }
    };
  
</script>