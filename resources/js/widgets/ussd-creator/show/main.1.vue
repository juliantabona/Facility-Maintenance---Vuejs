<style scoped>

    .ussd-creator-menu {
        height: 48px;
        line-height: 30px;
        background: transparent;
    }

    .ussd-creator-menu:after {
        background: transparent;
    }

    .ussd-creator-menu li {
        padding: 0 15px;
        font-size: 12px !important;
    }
    
    .ussd-creator-selector {
        width: 250px;
    }

    .ussd-creator-selector >>> .ivu-select-selection .ivu-select-selected-value {
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        width: 220px;
    }

</style>

<template>

    <Row>

        <Col :span="24">

            <Row v-if="!hideUssdCreatorMenu" class="border-bottom mb-3">

                <!-- Ussd creator header -->
                <Col :span="10">

                    <!-- Button to go back to ussd creators list -->
                    <Button type="text" @click.native="$emit('goBack')">
                        <Icon type="ios-arrow-back" />
                        <span>Back</span>
                    </Button>

                    <!-- Change ussd creator selector -->
                    <Select v-model="localUssdCreatorUrl" filterable class="ussd-creator-selector">
                        
                        <Option v-for="(ussdCreator, key) in ussdCreators" :key="key" class="mb-2"
                                :value="((ussdCreator._links || {}).self || {}).href" :label="ussdCreator.name"
                                @click.native="changeUssdCreator(ussdCreator)">
                            
                            <!-- Ussd Creator logo -->
                            <Avatar src="https://logosvector.net/wp-content/uploads/2013/08/debonairs-pizza-vector-logo.png" slot="prefix" size="small" />

                            <!-- Ussd Creator name -->
                            <span>{{ ussdCreator.name }}</span>

                        </Option>

                    </Select>

                </Col>

                <Col :span="14">

                    <Menu mode="horizontal" theme="light" :active-name="activeUssdCreatorTab" class="ussd-creator-menu"
                          @on-select="changeActiveUssdCreatorTab($event)">
                        <MenuItem name="creator">
                            <Icon type="ios-stats-outline" :size="20" />
                            Creator
                        </MenuItem>
                        <MenuItem name="sessions">
                            <Icon type="ios-paper-outline" :size="20" />
                            Sessions
                        </MenuItem>
                        <MenuItem name="settings">
                            <Icon type="ios-settings-outline" :size="20" />
                            Settings
                        </MenuItem>
                    </Menu>

                </Col>

            </Row>
            
            <Row>

                <!-- Loading ussd creator -->
                <Col v-if="isLoadingUssdCreator" :span="12" :offset="6"> 

                    <!-- Show loader -->
                    <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading creator...</Loader>

                </Col>

                <!-- Show when we have ussd creator -->
                <Col v-if="ussdCreator && !isLoadingUssdCreator" :span="24">
                
                    <!-- Creator Widget -->
                    <showCreatorwWidget v-if="activeUssdCreatorTab == 'creator'" :ussdCreator="ussdCreator"></showCreatorwWidget>
                            
                </Col>

                <!-- Show when we don't have ussd creator -->
                <Col v-if="!ussdCreator && !isLoadingUssdCreator" :span="20" :offset="2">
                    <Row :gutter="20">
                        <Col :span="8">
                            <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                                <h1 class="mb-3">Couldn't Find Ussd Creator</h1>
                                <p class="mb-3" style="font-sire:14px;">This ussd creator may have been deleted. Try reloading your browser incase we had connection issues trying to get the ussd creator.</p>
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

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue'; 

    /*  Widgets  */
    import showCreatorwWidget from './creator/main.vue';

    export default {
        props:{
            ussdCreatorUrl: {
                type: String,
                default: null
            },
            ussdCreators: {
                type: Array,
                default: function(){
                    return []
                }
            }
        },
        components: { 
            Loader, showCreatorwWidget
        },
        data(){
            return {

                ussdCreator: null,
                hideUssdCreatorMenu: false,
                isLoadingUssdCreator: false,
                localUssdCreatorUrl: this.ussdCreatorUrl
 
            }
        },
        watch: {

            //  Watch for changes on the ussdCreatorUrl
            ussdCreatorUrl: {
                handler: function (val, oldVal) {

                    //  If the updated ussd creator url is not the same as the current local ussd creator url
                    if( this.localUssdCreatorUrl != val ){

                        //  Update the local ussd creator url value
                        this.localUssdCreatorUrl = val;

                        //  Setup the ussd creator
                        this.handleUssdCreatorSetup();

                    }

                },
                deep: true
            }
        },
        computed: {
            activeUssdCreatorTab(){
                return this.$route.query.activeUssdCreatorTab || 'creator';
            }
        },
        methods: {
            handleUssdCreatorSetup(){

                //  Fetch the ussd creator
                this.fetchUssdCreator();

            },
            changeUssdCreator(ussdCreator){

                this.$emit('changeUssdCreator', ((ussdCreator._links || {}).self || {}).href);

            },
            changeActiveUssdCreatorTab(activeUssdCreatorTabName){

                //  Update the url query with the active tab name
                this.$router.replace({name: 'ussd-creators', query: {
                    
                    //  Get all the current url queries
                    ...this.$route.query, 

                    //  Add / Update our query
                    activeUssdCreatorTab: activeUssdCreatorTabName

                }});

            },
            fetchUssdCreator() {

                if(this.localUssdCreatorUrl){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingUssdCreator = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting ussd interface...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.localUssdCreatorUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingUssdCreator = false;

                            //  Store the ussd creator data
                            self.ussdCreator = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingUssdCreator = false;

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            handleSuccess(){
                this.fetchUssdCreator();
            }
        },
        created(){

            //  Setup the ussd creator
            this.handleUssdCreatorSetup();

        }
    };
  
</script>