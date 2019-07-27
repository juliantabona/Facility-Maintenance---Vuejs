<style scoped>

  /*  Style the header bar */

  .store-header,
  .store-header >>> .ivu-menu-horizontal {
      height: auto;
  }

  .main-header {
    width: 100%;
    -webkit-box-shadow: 5px 2px 5px #00000030;
    box-shadow: 5px 2px 5px #00000030;
  }

  /*  Style sidebar collapse button */
  .main-header >>> .rotate-icon{
    transform: rotate(-90deg);
  }

  /*  Style search bar */
  .main-header >>> .main-search-input{
    margin: 10px 0px 12px 0;
  }

  /*  Style icons */
  .main-header >>> .icon-border {
    height: 30px;
    font-size: 14px;
    border-radius: 100%;
    padding: 4px;
    border: 1px solid #0066ff3b;
    color: #297eff !important;
    line-height: 60px !important;
  }

  .main-header >>> .icon-border i {
    display: flex !important;
  }

  .main-header >>> .cart-icon{
    border-radius: 10px;
  }

  .main-header >>> .cart-icon a{
    width: 90px;
    padding: 0 10px;
  }

  /*  Style profile image */
  .main-header >>> .profile-image {
    margin-top:-5px;
  }

  .main-header >>> .profile-image .roundedShape {
    padding: 8px 0 0 15px;
    color: #2d8cf0;
  }

  .main-header >>> .profile-image h1.roundedShape{
    color: #2d8cf0;
    width: 50px;
    height: 50px;
    border: 1px solid #c5c5c5;
    padding: 8px 0 0 15px;
    margin:0;
    border-radius: 100%;
    font-size: 2em;
    display: block;
  }

  .notification-bar span{
      line-height:20px;
  }

  .notification-bar .nofify-icon{
    background: #f9f9f9;
    border-radius: 50%;
    width: 35px;
    padding: 6px 7px;
    border: 1px solid #e6e6e6;
  }

  .notification-bar a:hover{
      text-decoration: underline;
  }

  .notification-bar{
    position: relative;
  }

  .notification-bar >>> .ivu-select-dropdown{
    padding: 0 !important;
  }

  .notification-bar >>> .ivu-select-dropdown::before{
    content: "";
    z-index: 2;
    position: absolute;
    top: -14px;
    right: 0px;
    border-top: 0px solid #00000000;
    border-left: 12px solid #0000;
    border-right: 12px solid #0000;
    border-bottom: 16px solid #f1f1f1;
  }

  .notification-bar >>> .notification-content{
    width: 320px;
    max-height: 300px;
    padding-top: 50px;
    overflow-y: auto;
    background: #f1f1f1;
  }

  .notification-bar >>> .nofify-header{
    width: 94.8%;
    position: absolute;
    border-radius: 6px 0px 0 0;
    top: 0px;
    z-index: 1;
    background: #f1f1f1;
    border-top: 2px solid #fff;
  }

  .notification-bar >>> .nofify-item {
      background: #fff;
  }

  .wordwrap { 
    white-space: pre-wrap;      /* CSS3 */   
    white-space: -moz-pre-wrap; /* Firefox */    
    white-space: -pre-wrap;     /* Opera <7 */   
    white-space: -o-pre-wrap;   /* Opera 7 */    
    word-wrap: break-word;      /* IE */
  }

    .demo-badge{
      color: #2d8cf0;
      padding: 5px 10px;
      background: #f5f7f9;
      border-radius: 6px;
      margin: 3px 0 0 0;
      line-height: normal;
    }

</style>

<template>

  <Header class="store-header" :style="{ width: '100%', position: 'fixed', zIndex: '1000', background: '#fff', padding: '0' }">
      
      <!-- Main Menu -->
      <Menu mode="horizontal" :style="{ border: 'none' }">
          
          <!-- Company Logo -->
          <div class="layout-logo"></div>
          
          <!-- Dashboard Header -->
          <div class="main-header">
              
            <Row :gutter="20">

                <Col :span="8">
                    <img src="/images/samples/dress_me_logo.png" style="max-height: 60px;padding: 0px 0px 0px 100px;">
                </Col>

                <Col :span="8">
                            
                    <Menu mode="horizontal" theme="light" active-name="1" style="display: table;margin: 0 auto;">
                        <MenuItem name="1">Products</MenuItem>
                        <MenuItem name="2">Tickets</MenuItem>
                        <MenuItem name="3">Events</MenuItem>
                    </Menu>

                </Col>

              <Col :span="8" style="padding-top: 12px;">
                
                <!-- Profile Image -->
                <el-dropdown class="float-right mr-5" @command="handleProfileCommand">
                  <div class="profile-image">
                    <img v-if="user.avatar" :src="user.avatar" alt="Profile Image" class="roundedShape">
                    <h1 v-else  class="roundedShape">{{ user.full_name.charAt(0) ? user.full_name.charAt(0) : '?' }}</h1>
                  </div>

                  <!-- Profile Options -->
                  <el-dropdown-menu slot="dropdown">
                    <!-- Authenticated Options -->
                    <el-dropdown-item v-if="user" command="viewProfile">Account</el-dropdown-item>
                    <el-dropdown-item v-if="user" command="viewProfile">Wishlist</el-dropdown-item>
                    <el-dropdown-item v-if="user" command="viewProfile">Quotations</el-dropdown-item>
                    <el-dropdown-item v-if="user" command="viewProfile">Purchases</el-dropdown-item>
                    <el-dropdown-item v-if="user" command="logout">Logout</el-dropdown-item>
                    <!-- Guest Options -->
                    <el-dropdown-item v-if="!user" command="signIn">Sign In</el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>

                <!-- Notification, Settings, Toolbar Icons -->
                <Badge :count="3" type="primary" class="icon-border cart-icon float-right mr-3 mt-1">
                    <router-link :to="{ name:'user-profile-settings' }" class="d-block">
                      <span class="float-left" style=" line-height: 21px; ">My Cart</span>
                      <Icon type="ios-cart-outline" :size="20" class="float-left" style="font-size: 20px;" />
                    </router-link>
                </Badge>
                <Badge :count="5" type="warning" class="float-right mr-3 mt-1">
                    <div class="demo-badge">
                      <span>Discounts</span>
                    </div>
                </Badge>
              </Col>

            </Row>

            <Row style="background: #7ea3e4;">
              <Col :span="8" :offset="8">
              
                <!-- Main Search Bar -->
                <Input v-model="searchQuery" placeholder="Search..." search enter-button class="main-search-input"></Input>

              </Col>
            </Row>

          </div>

      </Menu>
      
        <!-- 
            MODAL TO LIST SUBSCRIPTION PRODUCTS
        -->
        <productSubscriptionModal 
            v-if="isOpenProductSubscriptionModal"
            @visibility="isOpenProductSubscriptionModal = $event"
            @updated="updateClient($event)">
        </productSubscriptionModal>


  </Header>

</template>

<script>

  import Loader from './../../components/_common/loaders/Loader.vue';

  /*  Modals  */
  import productSubscriptionModal from './../../components/_common/modals/productSubscriptionModal.vue';

  
  import Notification from './notification.vue';

  export default {
    components: { 
        Loader, Notification, productSubscriptionModal
    },
    props:{
      isCollapsed: {
        default: false
      }
    },
    data() {
      return {

        user:auth.user,
        createVal: '',
        
        searchQuery:'',
        searchType:'client',
      
        isOpenProductSubscriptionModal: false,
        isLoadingNotifications: false,
        notifications: [],

        ResourceOptions: [
          {
            value: 'product',
            label: 'Products'
          }, 
          {
            value: 'ticket',
            label: 'Tickets'
          }, {
            value: 'event',
            label: 'Events'
          }, {
            value: 'jobcard',
            label: 'Jobcard'
          },
        ]
      }
    },
    computed: {
        rotateIcon () {
            return [
                'menu-icon',
                this.isCollapsed ? 'rotate-icon' : ''
            ];
        }
    },
    methods: {
      loadCreate(){
        this.createVal = '';
      }, 
      handleProfileCommand(command) {
        if(command == 'logout'){
          //  Logout the user
          auth.logout();
        }
      },
      fetchNotifications() {
          const self = this;

          //  Start loader
          self.isLoadingNotifications = true;

          console.log('Start getting unread notifications...');

          //  Use the api call() function located in resources/js/api.js
          api.call('get', '/api/notifications?unread=1')
              .then(({data}) => {
                  
                  console.log(data);

                  //  Stop loader
                  self.isLoadingNotifications = false;
                  
                  //  Get notifications
                  self.notifications = data;

              })         
              .catch(response => { 
                  console.log('Error getting notifications...');
                  console.log(response);

                  //  Stop loader
                  self.isLoadingNotifications = false;     
              });
      }
    },
    mounted() {
        
        if((this.user || {}).id){
          
          console.log('App.User.' + this.user.id);
          
          const self = this;

          Echo.private('App.User.' + this.user.id)
              .notification((notification) => {
                  console.log('notification.type below:');
                  console.log(notification);
                  console.log('notification.type above');
                  self.notifications.unshift(notification);
              });

        }
    },
    created(){
        this.fetchNotifications();
    } 
  };
</script>