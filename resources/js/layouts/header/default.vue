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

  .main-header >>> .profile-image img {
    width: 50px;
    height: 50px;
    padding: 2px;
    border: 1px solid #c5c5c5;
    border-radius: 100%;
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

  .notification-bar >>> .nofify-header{
    width: 94.8%;
    position: absolute;
    border-radius: 6px 0px 0 0;
    top: 0px;
    z-index: 1;
    background: #f1f1f1;
    border-top: 2px solid #fff;
  }

  .wordwrap { 
    white-space: pre-wrap;      /* CSS3 */   
    white-space: -moz-pre-wrap; /* Firefox */    
    white-space: -pre-wrap;     /* Opera <7 */   
    white-space: -o-pre-wrap;   /* Opera 7 */    
    word-wrap: break-word;      /* IE */
  }

</style>

<template>

  <Header :style="{ width: '100%', position: 'fixed', zIndex: '5', background: '#fff', padding: '0' }">
      
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
                    <img :src="user.avatar" alt="Profile Image">
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
                      <DropdownMenu :style="{ width: '320px', maxHeight: '300px', paddingTop: '50px', overflowY: 'scroll' }" slot="list">
                          <Row class="nofify-header pt-2 pb-2 pl-3 pr-3">
                              <Col :span="12">
                                <h5 class="text-secondary mt-1">Notifications</h5>
                              </Col>
                              <Col :span="12">
                                <Button class="float-right">View All</Button>
                              </Col>
                          </Row>
                          <DropdownItem 
                                v-for="(notification, i) in notifications" :key="i"
                                v-if="notification.type.split('\\').pop() == 'InvoiceCreated'" 
                                class="nofify-item pb-2 mb-1 border-bottom ">
                              <Row>
                                  <Col :span="4">
                                    <Icon class="nofify-icon" type="ios-cash-outline" :size="20"/>
                                  </Col>
                                  <Col :span="20">
                                    <Row>
                                        <Col :span="24">
                                          <span class="wordwrap">
                                            <router-link :to="{ name: 'show-invoice', params: { id: notification.data.invoice.id }}">Invoice #{{ notification.data.invoice.reference_no_value }}</router-link> created for <a href="#">{{ notification.data.invoice.customized_client_details.name }}</a> by <a href="#">Julian Tabona</a>
                                          </span>  
                                        </Col>
                                          
                                        <Col :span="24"><small class="float-right">22 Jan 2019 @ 09:43AM</small></Col>
                                    </Row>
                                  </Col>
                              </Row>
                              <Row v-if="!notifications.length">
                                
                                <Loader v-if="isLoadingNotifications" :loading="isLoadingNotifications" type="text" class="mt-2 mb-2">Loading notifications...</Loader>
                                
                                <Alert v-else show-icon class="mt-2 mb-2">
                                    <Icon type="ios-notifications-outline" slot="icon" :size="20"></Icon>
                                    No notifications yet
                                </Alert>
                              
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
                <Badge :count="0" class="icon-border float-right mr-3 mt-1">
                    <Icon type="ios-apps-outline" :size="20"/>
                </Badge>

              </Col>

            </Row>

          </div>

      </Menu>
      
  </Header>

</template>

<script>

  import Loader from './../../components/_common/loader/Loader.vue';

  export default {
    components: { 
        Loader
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
                  //alert(notification);
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