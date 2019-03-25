<style scoped>

  /*  Style the header bar */
  .main-header {
    width: 100%;
    height:60px;
    -webkit-box-shadow: 5px 2px 5px #00000030;
    box-shadow: 5px 2px 5px #00000030;
  }

  /*  Style sidebar collapse button */
  .main-header >>> .rotate-icon{
    transform: rotate(-90deg);
  }

  /*  Style search bar */
  .main-header >>> .main-search-input{
    margin-top:14px;
  }

  /*  Style icons */
  .main-header >>> .icon-border {
    height:30px;
    font-size: 14px;
    border-radius: 100%;
    padding: 4px;
    border: 1px solid #0066ff3b;
    color: #297eff;
  }

  .main-header >>> .icon-border i {
    display: flex !important;
  }

  /*  Style profile image */
  .main-header >>> .profile-image {
    margin-top:-5px;
  }

  .main-header >>> .profile-image .roundedShape {
    width: 50px;
    height: 50px;
    padding: 2px;
    border: 1px solid #c5c5c5;
    border-radius: 100%;
    display: block;
  }

  .main-header >>> .profile-image h1.roundedShape{
    padding: 8px 0 0 15px;
    color: #2d8cf0;
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

  <Header :style="{ width: '100%', position: 'fixed', zIndex: '1000', background: '#fff', padding: '0' }">
      
      <!-- Main Menu -->
      <Menu mode="horizontal" :style="{ border: 'none' }">
          
          <!-- Company Logo -->
          <div class="layout-logo"></div>
          
          <!-- Dashboard Header -->
          <div class="main-header">
              
            <Row :gutter="20">

                <Col :span="8">

                  <!-- Sidebar Toggle Button -->
                  <Icon @click.native="$emit('toggleCollapsed')" :class="rotateIcon" :style="{ margin: '0 20px' }" type="md-menu" size="24"></Icon>

                  <!-- Create Resources Button -->
                  <Dropdown trigger="click" class="float-right">
                    
                    <Button type="primary">
                        + Create
                        <Icon type="ios-arrow-down"></Icon>
                    </Button>
                    <DropdownMenu slot="list">
                        <DropdownItem>Staff</DropdownItem>
                        <DropdownItem divided>Client</DropdownItem>
                        <DropdownItem>Supplier</DropdownItem>
                        <DropdownItem divided>Asset</DropdownItem>
                        <DropdownItem><router-link :to="{ name:'create-jobcard' }">Jobcard</router-link></DropdownItem>
                        <DropdownItem divided><router-link :to="{ name:'create-quotation' }">Quotation</router-link></DropdownItem>
                        <DropdownItem><router-link :to="{ name:'create-invoice' }">Invoice</router-link></DropdownItem>
                    </DropdownMenu>

                </Dropdown>

              </Col>

              <Col :span="8">
              
                <!-- Main Search Bar -->
                <Input v-model="searchQuery" placeholder="Search..." search enter-button class="main-search-input">
                    <Select v-model="searchType" slot="prepend" style="width: 80px" not-found-text="No resources found">
                        <Option v-for="item in ResourceOptions" :key="item.value" :value="item.value">{{ item.label }}</Option>
                    </Select>
                </Input>

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
                    <el-dropdown-item command="viewProfile">Profile</el-dropdown-item>
                    <el-dropdown-item command="logout">Logout</el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>

                <!-- Notification, Settings, Toolbar Icons -->
                <Badge :count="notifications.length" class="icon-border float-right mr-3 mt-1">
                  <Dropdown class="notification-bar" trigger="click" placement="bottom-end">
                    <span :style="{ marginTop: '-35px', float: 'left' }">
                      <Icon type="ios-notifications-outline" :size="20"/>
                    </span>
                      <DropdownMenu class="notification-content" slot="list">
                          <Row class="nofify-header pt-2 pb-2 pl-3 pr-3">
                              <Col :span="12">
                                <h5 class="text-secondary mt-1">Notifications</h5>
                              </Col>
                              <Col :span="12">
                                <Button class="float-right">View All</Button>
                              </Col>
                          </Row>
                          
                          <DropdownItem v-for="(notification, i) in notifications" :key="i" class="nofify-item pt-3 pb-2 border-bottom ">
                              
                              <Notification :style="(i == 0) ? { paddingTop: '10px' } :''" :notification="notification"></Notification>

                          </DropdownItem>
                          <DropdownItem v-if="!notifications.length" class="nofify-item pb-2 mb-1 border-bottom ">
                              
                              <Row>
                                <Col :span="24">
                                  
                                  <Loader v-if="isLoadingNotifications" :loading="isLoadingNotifications" type="text" class="mt-2 mb-2">Loading notifications...</Loader>
                                  
                                  <Alert v-else show-icon class="mt-2 mb-2">
                                      <Icon type="ios-notifications-outline" slot="icon" :size="20"></Icon>
                                      No notifications yet
                                  </Alert>
                                
                                </Col>
                              </Row>
                          </DropdownItem>

                      </DropdownMenu>
                  </Dropdown>
                </Badge>
                <Badge :count="0" class="icon-border float-right mr-3 mt-1">
                    <router-link :to="{ name:'user-profile-settings' }">
                      <Icon type="ios-settings-outline" :size="20"/>
                    </router-link>
                </Badge>
                <Badge :count="0" class="icon-border float-right mr-3 mt-1"
                       @click.native="isOpenProductSubscriptionModal = !isOpenProductSubscriptionModal">
                    <Icon type="ios-apps-outline" :size="20"/>
                </Badge>
                <Badge :count="37" type="primary" class="float-right mr-3 mt-1">
                    <div class="demo-badge">
                      <span>Sms Power</span>
                    </div>
                </Badge>
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
            value: 'client',
            label: 'Client'
          }, 
          {
            value: 'supplier',
            label: 'Supplier'
          }, {
            value: 'staff',
            label: 'Staff Member'
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
        
        if(this.user.id){
          
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