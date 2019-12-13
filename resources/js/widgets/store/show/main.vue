<style scoped>

    .store-menu {
        height: 48px;
        line-height: 30px;
        background: transparent;
    }

    .store-menu:after {
        background: transparent;
    }

    .store-menu li {
        padding: 0 15px;
        font-size: 12px !important;
    }
    
    .store-selector {
        width: 250px;
    }

    .store-selector >>> .ivu-select-selection .ivu-select-selected-value {
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        width: 220px;
    }

</style>

<template>

    <Row>

        <Col :span="24">

            <Row class="border-bottom">

                <!-- Store header -->
                <Col :span="10">

                    <!-- Button to go back to store list -->
                    <Button type="text" @click.native="$emit('goBack')">
                        <Icon type="ios-arrow-back" />
                        <span>Stores</span>
                    </Button>

                    <!-- Change store selector -->
                    <Select v-model="localStoreUrl" filterable class="store-selector">
                        
                        <Option v-for="(store, key) in stores" :key="key" class="mb-2"
                                :value="((store._links || {}).self || {}).href" :label="store.name"
                                @click.native="changeStore(store)">
                            
                            <!-- Store logo -->
                            <Avatar src="https://logosvector.net/wp-content/uploads/2013/08/debonairs-pizza-vector-logo.png" slot="prefix" size="small" />

                            <!-- Store name -->
                            <span>{{ store.name }}</span>

                        </Option>

                    </Select>

                </Col>

                <Col :span="14">

                    <Menu mode="horizontal" theme="light" :active-name="activeStoreTab" class="store-menu"
                          @on-select="changeActiveStoreTab($event)">
                        <MenuItem name="overview">
                            <Icon type="ios-stats-outline" :size="20" />
                            Overview
                        </MenuItem>
                        <MenuItem name="orders">
                            <Icon type="ios-paper-outline" :size="20" />
                            Orders
                        </MenuItem>
                        <MenuItem name="customers">
                            <Icon type="ios-people-outline" :size="20" />
                            Customers
                        </MenuItem>
                        <MenuItem name="mobile_store">
                            <Icon type="ios-phone-portrait" :size="20" />
                            Mobile Store
                        </MenuItem>
                        <MenuItem name="settings">
                            <Icon type="ios-settings-outline" :size="20" />
                            Settings
                        </MenuItem>
                    </Menu>

                </Col>

            </Row>
            
            <Row>

                <!-- Loading store -->
                <Col v-if="isLoadingStore" :span="12" :offset="6"> 

                    <!-- Show loader -->
                    <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading store...</Loader>

                </Col>

                <!-- Show when we have store -->
                <Col v-if="store && !isLoadingStore" :span="24">

                    <div class="mt-3">
                        
                        <Card v-if="!store.is_mobile_verified" class="p-2">
                            
                            <Row :gutter="20">

                                <Col :span="8">

                                    <div v-if="default_mobile_phone && !isLoadingDefaultMobilePhone">

                                        <Card class="mb-2">
                                            <span class="font-weight-bold text-primary">Please verify your mobile number: </span>
                                            <span class="d-block font-weight-bold mt-2 text-primary" style="font-size:20px;">
                                                {{ default_mobile_phone.full_number }}
                                            </span>
                                        </Card>
                                            
                                        <Card class="mb-2">
                                            
                                            <!-- Phone Verification Form -->
                                            <phoneVerificationForm 
                                                :phone="default_mobile_phone"
                                                @success="handleSuccess($event)">
                                            </phoneVerificationForm>

                                        </Card>
                                        
                                    </div>

                                    <!-- Loader -->
                                    <Loader v-if="isLoadingDefaultMobilePhone" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading phones...</Loader>
            
                                </Col>

                            </Row>

                        </Card>

                        <template v-else>

                            <!-- Overview Tab -->
                            <overviewWidget v-if="activeStoreTab == 'overview'" :store="store"></overviewWidget>

                            <!-- Orders Tab -->
                            <orderWidget v-if="activeStoreTab == 'orders'" :ordersUrl="(store._links['oq:orders'] || {}).href" :store="store"></orderWidget>

                            <!-- Customers Tab -->
                            <customerWidget v-if="activeStoreTab == 'customers'" :customersUrl="(store._links['oq:customer_contacts'] || {}).href"></customerWidget>

                            <!-- USSD Interface Tab -->
                            <ussdInterfaceWidget v-if="activeStoreTab == 'mobile_store'" :store="store"></ussdInterfaceWidget>

                            <!-- Settings Tab -->
                            <Card v-if="activeStoreTab == 'settings'" class="pt-3 pb-3">
                                <span>Settings Here</span>
                            </Card>

                        </template>

                        
                    </div>

                </Col>

                <!-- Show when we don't have store -->
                <Col v-if="!store && !isLoadingStore" :span="20" :offset="2">
                    <Row :gutter="20">
                        <Col :span="8">
                            <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                                <h1 class="mb-3">Couldn't Find Store</h1>
                                <p class="mb-3" style="font-size:14px;">This store may have been deleted. Try reloading your browser incase we had connection issues trying to get the store.</p>
                            </div>
                            <span>Need help? <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
                        </Col>
                        <Col :span="16">
                            <img style="width:100%;" class="mt-4" src="/images/backgrounds/mobile-ecommerce.png">
                        </Col>
                    </Row>
                </Col>

            </Row>

        </Col>

    </Row>

</template>

<script>
    
    /*  Phone Verification Form  */
    import phoneVerificationForm from './../../../components/_common/forms/phone/verifyPhone.vue';
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    /*  Widgets  */
    import overviewWidget from './overview/main.vue';
    import orderWidget from './../../order/list/main.vue';
    import customerWidget from './customerWidget.vue'
    import ussdInterfaceWidget from './ussdInterfaceWidget.vue'
    import productWidget from './productWidget.vue';
    import messageWidget from './messageWidget.vue';
    import reviewWidget from './reviewWidget.vue';

    export default {
        props:{
            storeUrl: {
                type: String,
                default: null
            },
            stores: {
                type: Array,
                default: function(){
                    return []
                }
            }
        },
        components: { 
            phoneVerificationForm, basicButton, Loader, overviewWidget, orderWidget, customerWidget, 
            ussdInterfaceWidget, productWidget, messageWidget, reviewWidget
        },
        data(){
            return {

                store: null,
                isLoadingStore: false,
                default_mobile_phone: null,
                localStoreUrl: this.storeUrl,
                isLoadingDefaultMobilePhone: false,
 
            }
        },
        watch: {

            //  Watch for changes on the storeUrl
            storeUrl: {
                handler: function (val, oldVal) {

                    //  If the updated store url is not the same as the current local store url
                    if( this.localStoreUrl != val ){

                        //  Update the local store url value
                        this.localStoreUrl = val;

                        //  Setup the store
                        this.handleStoreSetup();

                    }

                },
                deep: true
            }
        },
        computed: {
            activeStoreTab(){
                return this.$route.query.activeStoreTab || 'overview';
            }
        },
        methods: {
            handleStoreSetup(){

                //  Fetch the store
                this.fetchStore().then((data) => {
                    if( !this.store.is_mobile_verified ){
                        this.fetchDefaultMobilePhone();
                    }
                });

            },
            changeStore(store){

                this.$emit('changeStore', ((store._links || {}).self || {}).href);

            },
            changeActiveStoreTab(activeStoreTabName){

                //  Update the url query with the active tab name
                this.$router.replace({name: 'stores', query: {
                    
                    //  Get all the current url queries
                    ...this.$route.query, 

                    //  Add / Update our query
                    activeStoreTab: activeStoreTabName

                }});

            },
            fetchStore() {

                if(this.localStoreUrl){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingStore = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting store...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localStoreUrl)
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

            },
            fetchDefaultMobilePhone() {

                if( ((this.store._links || [])['oq:mobiles'] || {}).href ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingDefaultMobilePhone = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting default mobile phone...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', ((this.store._links || [])['oq:default_mobile'] || {}).href )
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingDefaultMobilePhone = false;

                            //  Store the store data
                            self.default_mobile_phone = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingDefaultMobilePhone = false;

                            //  Console log Error Location
                            console.log('dashboard/store/show/main.vue - Error getting default mobile phone...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            handleSuccess(){
                this.fetchStore();
            }
        },
        created(){

            //  Setup the store
            this.handleStoreSetup();

        }
    };
  
</script>