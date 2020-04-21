<template>

  <!-- 
    Layout used by authenticated users to access creator dashboard
    Contains Header, SideMenu, Content and Footer
  -->
  <div class="layout">

      <Layout>

          <!-- Dashboard Header -->
          <creatorHeader>

            <Row>

                <!-- Creator header -->
                <Col :span="22" :offset="2" class="d-flex">

                    <!-- Button to go back to creator list -->
                    <Button size="large" type="text" class="text-white mr-2" ghost @click.native="goToCreators()">
                        <Icon type="ios-arrow-back" />
                        <span>Creators</span>
                    </Button>

                    <h1 v-if="creator && !isLoadingCreator" class="text-light">{{ creator.name }}</h1>

                </Col>

            </Row>

          </creatorHeader>

          <Layout class="ivu-layout-has-sider">

            <!-- Dashboard Aside -->
            <creatorAside :url="localCreatorUrl"></creatorAside>

            <Layout :style="{marginTop: '75px', padding: '20px'}">

                <!-- Dashboard content -->
                <Content :style="{ position: 'relative', minHeight: '2000px' }">
                    
                  <!-- Put Overview, Orders, Products e.t.c resource content here -->
                  <!-- Only authenticated users can access this content -->

                  <transition name="slide">
                    
                    <template v-if="creator">

                        {{ creator }}

                        <!-- Creator Home -->
                        <home v-if="activeLink == 'home'" :ussdCreator="creator" @updatedCreator="handleUpdatedCreator($event)">></home>

                        <!-- Creator Builder -->
                        <builder v-if="activeLink == 'builder'" :ussdCreator="creator"></builder>

                        <!-- Creator Billing -->
                        <billing v-if="activeLink == 'billing'" :ussdCreator="creator"></billing>
                        
                        <!-- Creator Subcriptions -->
                        <subcriptions v-if="activeLink == 'subcriptions'" :ussdCreator="creator"></subcriptions>
                        
                        <!-- Creator Customers -->
                        <customers v-if="activeLink == 'customers'" :ussdCreator="creator"></customers>
                        
                        <!-- Creator Analytics -->
                        <analytics v-if="activeLink == 'analytics'" :ussdCreator="creator"></analytics>
                        
                        <!-- Creator Settings -->
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
    
    import creatorHeader from './../../../layouts/header/creator-header.vue';
    
    import creatorAside from './../../../layouts/aside/creator-aside.vue';

    import subcriptions from './subcriptions/main.vue';
    import customers from './customers/main.vue';
    import analytics from './analytics/main.vue';
    import settings from './settings/main.vue';
    import billing from './billing/main.vue';
    import builder from './builder/main.vue';
    import home from './home/main.vue';

    export default {
        components:{ creatorHeader, creatorAside, subcriptions, customers, analytics, settings, billing, builder, home },
        data(){
            return {
                creator: null,
                isLoadingCreator: false,
            }
        },
        computed: {
            activeLink(){
                return this.$route.query.menu || 'home';
            },
            localCreatorUrl(){
                return decodeURIComponent(this.$route.params.url);
            },
        },
        methods: {
            goToCreators(){
                this.$router.push({ name: 'creators' });
            },
            handleUpdatedCreator(updatedCreator){
                this.creator = updatedCreator;
            },
            fetchCreator() {

                if(this.localCreatorUrl){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingCreator = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting creator...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localCreatorUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingCreator = false;

                            //  Creator the creator data
                            self.creator = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingCreator = false;

                            //  Console log Error Location
                            console.log('dashboard/creator/show/main.vue - Error getting creator...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){

            //  Fetch the creator
            this.fetchCreator();

        }
    };
  
</script>