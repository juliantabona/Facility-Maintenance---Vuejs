<template>

  <!-- 
    Layout used by authenticated users to access Ussd Service dashboard
    Contains Header, SideMenu, Content and Footer
  -->
  <div class="layout">

      <Layout>

          <!-- Dashboard Header -->
          <ussdServiceHeader>

            <Row>

                <!-- Ussd Service header -->
                <Col :span="22" :offset="2" class="d-flex">

                    <!-- Button to go back to Ussd Service list -->
                    <Button size="large" type="text" class="text-white mr-2" ghost @click.native="goToUssdService()">
                        <Icon type="ios-arrow-back" />
                        <span>Ussd Services</span>
                    </Button>

                    <h1 v-if="ussdService && !isLoadingUssdService" class="text-light">{{ ussdService.name }}</h1>

                </Col>

            </Row>

          </ussdServiceHeader>

          <Layout class="ivu-layout-has-sider">

            <!-- Dashboard Aside -->
            <ussdServiceAside :url="localUssdServiceUrl"></ussdServiceAside>

            <Layout :style="{marginTop: '75px', padding: '20px'}">

                <!-- Dashboard content -->
                <Content :style="{ position: 'relative', minHeight: '2000px' }">
                    
                  <!-- Put Overview, Orders, Products e.t.c resource content here -->
                  <!-- Only authenticated users can access this content -->

                  <transition name="slide">
                    
                    <template v-if="ussdService">

                        <!-- Ussd Service Home -->
                        <home v-if="activeLink == 'home'" :ussdService="ussdService" @updatedUssdService="handleUpdatedUssdService($event)">></home>

                        <!-- Ussd Service Builder -->
                        <builder v-if="activeLink == 'builder'" :ussdService="ussdService"></builder>

                        <!-- Ussd Service Billing -->
                        <billing v-if="activeLink == 'billing'" :ussdService="ussdService"></billing>
                        
                        <!-- Ussd Service Subcriptions -->
                        <subcriptions v-if="activeLink == 'subcriptions'" :ussdService="ussdService"></subcriptions>
                        
                        <!-- Ussd Service Customers -->
                        <customers v-if="activeLink == 'customers'" :ussdService="ussdService"></customers>
                        
                        <!-- Ussd Service Analytics -->
                        <analytics v-if="activeLink == 'analytics'" :ussdService="ussdService"></analytics>
                        
                        <!-- Ussd Service Settings -->
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
    
    import ussdServiceHeader from './../../../layouts/header/ussd-service-header.vue';
    
    import ussdServiceAside from './../../../layouts/aside/ussd-service-aside.vue';

    import subcriptions from './subcriptions/main.vue';
    import customers from './customers/main.vue';
    import analytics from './analytics/main.vue';
    import settings from './settings/main.vue';
    import billing from './billing/main.vue';
    import builder from './builder/main.vue';
    import home from './home/main.vue';

    export default {
        components:{ ussdServiceHeader, ussdServiceAside, subcriptions, customers, analytics, settings, billing, builder, home },
        data(){
            return {
                ussdService: null,
                isLoadingUssdService: false,
            }
        },
        computed: {
            activeLink(){
                return this.$route.query.menu || 'home';
            },
            localUssdServiceUrl(){
                return decodeURIComponent(this.$route.params.url);
            },
        },
        methods: {
            goToUssdService(){
                this.$router.push({ name: 'ussd-services' });
            },
            handleUpdatedUssdService(updatedUssdService){
                this.ussdService = updatedUssdService;
            },
            fetchUssdService() {

                if(this.localUssdServiceUrl){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingUssdService = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting ussd service...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localUssdServiceUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingUssdService = false;

                            //  Get the Ussd Service data
                            self.ussdService = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingUssdService = false;

                            //  Console log Error Location
                            console.log('dashboard/ussd-service/show/main.vue - Error getting ussd service...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){

            //  Fetch the ussd service
            this.fetchUssdService();

        }
    };
  
</script>