<template>

  <!-- 
    Layout used by authenticated users to access store dashboard
    Contains Header, SideMenu, Content and Footer
  -->
  <div class="layout">

      <Layout>

          <!-- Dashboard Header -->
          <storeHeader>

            <Row>

                <!-- Store header -->
                <Col :span="22" :offset="2" class="d-flex">

                    <!-- Button to go back to store list -->
                    <Button size="large" type="text" class="text-white mr-2" ghost @click.native="goToStores()">
                        <Icon type="ios-arrow-back" />
                        <span>Stores</span>
                    </Button>

                    <h1 v-if="store && !isLoadingStore" class="text-light">{{ store.name }}</h1>

                </Col>

            </Row>

          </storeHeader>

          <Layout class="ivu-layout-has-sider">

            <!-- Dashboard Aside -->
            <storeAside :url="localStoreUrl"></storeAside>

            <Layout :style="{marginTop: '75px', padding: '20px'}">

                <!-- Dashboard content -->
                <Content :style="{ position: 'relative', minHeight: '2000px' }">
                    
                  <!-- Put Overview, Orders, Products e.t.c resource content here -->
                  <!-- Only authenticated users can access this content -->

                  <transition name="slide">
                    
                    <template v-if="store">

                        <!-- Store Home -->
                        <home v-if="activeLink == 'home'" :store="store" @updatedStore="handleUpdatedStore($event)">></home>

                        <!-- Store Orders -->
                        <orders v-if="activeLink == 'orders'" :store="store"></orders>
                        
                        <!-- Store Products -->
                        <products v-if="activeLink == 'products'"></products>
                        
                        <!-- Store Customers -->
                        <customers v-if="activeLink == 'customers'"></customers>
                        
                        <!-- Store Analytics -->
                        <analytics v-if="activeLink == 'analytics'"></analytics>
                        
                        <!-- Mobile Store -->
                        <mobileStore v-if="activeLink == 'mobile-store'" :store="store"></mobileStore>
                        
                        <!-- Store Settings -->
                        <settings v-if="activeLink == 'settings'"></settings>

                    </template>

                  </transition>

                </Content>

            </Layout>

          </Layout>

      </Layout>
  </div>

</template>

<script>
    
    import storeHeader from './../../../layouts/header/store-header.vue';
    
    import storeAside from './../../../layouts/aside/store-aside.vue';

    import home from './home/main.vue';
    import orders from './orders/main.vue';
    import products from './products/main.vue';
    import customers from './customers/main.vue';
    import mobileStore from './mobile-store/main.vue';



    export default {
        components:{ storeHeader, storeAside, home, orders, products, customers, mobileStore },
        data(){
            return {
                store: null,
                isLoadingStore: false,
            }
        },
        computed: {
            activeLink(){
                return this.$route.query.menu || 'home';
            },
            localStoreUrl(){
                return decodeURIComponent(this.$route.params.url);
            },
        },
        methods: {
            goToStores(){
                this.$router.push({ name: 'stores' });
            },
            handleUpdatedStore(updatedStore){
                this.store = updatedStore;
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
            handleStoreSetup(){

                //  Fetch the store
                this.fetchStore().then((data) => {
                    if( !this.store.is_mobile_verified ){
                        this.fetchDefaultMobilePhone();
                    }
                });

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