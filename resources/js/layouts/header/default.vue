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
                  <Icon @click.native="$emit('toggleCollapsed')" :class="rotateIcon" :style="{margin: '0 20px'}" type="md-menu" size="24"></Icon>

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
                    </DropdownMenu>
                </Dropdown>

              </Col>

              <Col :span="8">
              
                <!-- Main Search Bar -->
                <Input v-model="searchQuery" placeholder="Search..." search enter-button class="main-search-input">
                    <Select v-model="searchType" slot="prepend" style="width: 80px">
                        <Option v-for="item in ResourceOptions" :key="item.value" :value="item.value">{{ item.label }}</Option>
                    </Select>
                </Input>

              </Col>

              <Col :span="8" style="padding-top: 12px;">

                <!-- Profile Image -->
                <el-dropdown class="float-right mr-5" @command="handleProfileCommand">
                  <div class="profile-image">
                    <img src="https://scontent.fgbe2-2.fna.fbcdn.net/v/t1.0-1/p160x160/27540018_1876784582363504_131854681115569669_n.jpg?_nc_cat=108&amp;_nc_ht=scontent.fgbe2-2.fna&amp;oh=942e649e1e7c64c89b2cf25adbe74ab9&amp;oe=5C3BEE16" alt="Profile Image">
                  </div>

                  <!-- Profile Options -->
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item command="viewProfile">Profile</el-dropdown-item>
                    <el-dropdown-item command="logout">Logout</el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>

                <!-- Notification, Settings, Toolbar Icons -->
                <Badge :count="3" class="icon-border float-right mr-3 mt-1">
                    <Icon type="ios-notifications-outline" :size="20"/>
                </Badge>
                <Badge :count="0" class="icon-border float-right mr-3 mt-1">
                    <Icon type="ios-settings-outline" :size="20"/>
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

  export default {
    props:{
      isCollapsed: {
        default: false
      }
    },
    data() {
      return {

        createVal: '',
        
        searchQuery:'',
        searchType:'client',
      
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
          }

        ],
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
      }
    } 
  };
</script>