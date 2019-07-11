<template>
    <Row :gutter="20">

        <!-- Show when we have staff -->
        <Col v-if="productTotal && !isLoading" :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon type="ios-contact-outline":style="{ marginTop:'-10px', fontSize:'1.5rem' }" />
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">{{ titleName }}</h1>
                </template>

                <!-- Slot Extra functionality -->
                <template slot="extra">
                    <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
                    <allocationTypeButton></allocationTypeButton>
                </template>


            </pageToolbar>

            <Row :gutter="20">

                <Col :span="24" :style="{ padding: '0 20px' }">

                    <!-- Get the client activity cards -->
                    <activityCardWidget 
                        url="/users/stats" 
                        routeName="users"
                        :renameTitleList="[{search: 'Client', replace: 'Clients'}, {search: 'Supplier', replace: 'Suppliers'}]">
                    </activityCardWidget>

                </Col>
                
            </Row>

            <!-- Get the filterable user client list -->
            <userListWidget></userListWidget>

        </Col>

        <!-- Show when we don't have staff -->
        <Col v-else :span="20" :offset="2">
            <div class="pb-3 border-bottom">
                <h2>Users</h2>
            </div>

            <Row :gutter="20">
                <Col :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Add your team</h1>
                        <p class="mb-3" style="font-size:14px;">Get your team onboard and start collaborating together to grow your business. You can import existing team members</p>

                        <!-- Add User Button -->
                        <basicButton @click.native="$router.push({ name:'create-invoice', query: { clientId: company.id } })" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add User</span>
                        </basicButton>

                        <!-- Import User Button -->
                        <basicButton @click.native="$router.push({ name:'create-invoice', query: { clientId: company.id } })" 
                                     size="large" type="success" class="float-left mb-3">
                                     <Icon type="ios-cloud-upload-outline" :size="20" class="mr-1" />
                                     <span>Import</span>
                        </basicButton>

                    </div>
                     <span>Need help? <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
                </Col>
                <Col :span="16">
                    <img style="width:100%;" class="mt-4" src="/images/backgrounds/team-work-calculations.png">
                </Col>
            </Row>
        </Col>

    </Row>
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import allocationTypeButton from './../../../../components/_common/buttons/allocationTypeButton.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import userListWidget from './../../../../widgets/user/list/userListWidget.vue';

    import basicButton from './../../../../components/_common/buttons/basicButton.vue';
    
    export default {
        components: { pageToolbar, allocationTypeButton, activityCardWidget, userListWidget, basicButton },
        data(){
            return {
                productTotal: 0,
                isLoading: false,
            }
        },
        computed:{
            titleName(){
                
                var title;

                if( this.$route.query.status == 'Supplier' ){
                    title = 'Individual Suppliers';
                }else if( this.$route.query.status == 'Client' ){
                    title = 'Individual Clients';
                }else if( this.$route.query.status == 'Staff' ){
                    title = 'Staff Members';
                }else{
                    title = 'Individuals';
                }

                return title;
            }
        },
        methods: {
            fetchUser() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoading = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting total number of users...');

                //  Additional data to eager load along with the product found
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/staff/?count=1'+connections)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Store the users data
                        self.productTotal = data;

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        //  Console log Error Location
                        console.log('dashboard/product/show/main.vue - Error getting total number of users...');

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            //  Fetch the product
            this.fetchUser();
        }
    }
</script>
