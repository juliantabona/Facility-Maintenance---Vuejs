<style scoped>

    /*  Style side menu texts */
    .menu-item > span{
        display: inline-block;
        overflow: hidden;
        width: 75px;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: bottom;
        transition: width .2s ease .2s;
    }

    /*  Style side menu arrows */
    .menu-item i{
        transform: translateX(0px);
        transition: font-size .2s ease, transform .2s ease;
        vertical-align: middle;
        font-size: 16px;
    }

    /*  Style side menu hide texts on collapse */
    .collapsed-menu span{
        width: 0px;
        transition: width .2s ease;
    }

    /*  Style side menu align arrows on collapse */
    .collapsed-menu i{
        transform: translateX(5px);
        transition: font-size .2s ease .2s, transform .2s ease .2s;
        vertical-align: middle;
        font-size: 22px;
    }

    .ivu-menu-submenu >>> .ivu-menu{
      background: #f3f3f3;
    }

</style>

<template>

  <Sider ref="mainSideBar" v-model="isCollapsedState" hide-trigger collapsible :collapsed-width="78"
         hide-trigger :style="{ background: '#fff', position: 'relative' }">
      
      <!--  Main Menu -->
      <Menu :class="menuitemClasses"
            :style="{ width: (isCollapsedState ? '78px' : '200px'), overflowY:'scroll',position: 'fixed', top: '64px', bottom: 0, paddingTop: '22px', zIndex: '4' }" 
            :active-name="activeLink" :open-names="['1']" theme="light">

          <router-link :to="{name:'overview'}">
            <MenuItem name="overview">
              <Icon type="ios-analytics-outline" :size="20"/>
              <span>Overview</span>
            </MenuItem>
          </router-link>

          <router-link :to="{ name: 'users', query: { status: 'Staff', location: 'bottom' } }">
            <MenuItem name="staff">
              <Icon type="ios-man-outline" :size="20"/>
              <span>Staff</span>
            </MenuItem>
          </router-link>

          <Submenu name="3" style="color:#2d8cf0;">

              <template slot="title">
                  <Icon type="ios-happy-outline" :size="20"/>
                  <span>Clients</span>
              </template>

              <router-link :to="{ name: 'companies', query: { status: 'Client', location: 'bottom' } }">
                <MenuItem name="companies">Companies</MenuItem>
              </router-link>

              <router-link :to="{ name: 'users', query: { status: 'Client', location: 'bottom' } }">
                <MenuItem name="individuals">Individuals</MenuItem>
              </router-link>

          </Submenu>

          <Submenu name="4" style="color:#2d8cf0;">

              <template slot="title">
                  <Icon type="ios-briefcase-outline" :size="20"/>
                  <span>Suppliers</span>
              </template>

              <router-link :to="{ name: 'companies', query: { status: 'Supplier', location: 'bottom' } }">
                <MenuItem name="companies">Companies</MenuItem>
              </router-link>

              <router-link :to="{ name: 'users', query: { status: 'Supplier', location: 'bottom' } }">
                <MenuItem name="individuals">Individuals</MenuItem>
              </router-link>

          </Submenu>

          <Submenu name="5" style="color:#2d8cf0;">

              <template slot="title">
                  <Icon type="ios-cash-outline" :size="20"/>
                  <span>Sales</span>
              </template>

              <router-link :to="{ name:'create-quotation' }">
                <MenuItem name="4-1">
                    <Button type="primary" size="small">
                        + Create Quotation
                    </Button>
                </MenuItem>
              </router-link>

              <router-link :to="{name:'quotations'}">
                <MenuItem name="quotations">Quotations</MenuItem>
              </router-link>

              <router-link :to="{name:'invoices'}">
                <MenuItem name="invoices">Invoices</MenuItem>
              </router-link>

              <router-link :to="{name:'invoices'}">
                <MenuItem name="import">Import</MenuItem>
              </router-link>

          </Submenu>

          <Submenu name="6" style="color:#2d8cf0;">

              <template slot="title">
                  <Icon type="ios-copy-outline" :size="20"/>
                  <span>Jobcards</span>
              </template>

              <router-link :to="{ name:'create-jobcard' }">
                <MenuItem name="6-1">
                    <Button type="primary" size="small">
                        + Create Jobcard
                    </Button>
                </MenuItem>
              </router-link>

              <router-link :to="{name:'jobcards'}">
                <MenuItem name="jobcards">Recent Jobcards</MenuItem>
              </router-link>

              <router-link :to="{name:'jobcards'}">
                <MenuItem name="jobcardSettings">Jobcard Settings</MenuItem>
              </router-link>

          </Submenu>

          <Submenu name="7" style="color:#2d8cf0;">

              <template slot="title">
                  <Icon type="ios-filing-outline" :size="20"/>
                  <span>Templates</span>
              </template>

              <router-link to="/jobcard/create">
                <MenuItem name="7-1">
                    <Button type="primary" size="small">
                        + Create Template
                    </Button>
                </MenuItem>
              </router-link>

              <router-link :to="{name:'templates'}">
                <MenuItem name="templates">All Templates</MenuItem>
              </router-link>

              <router-link :to="{name:'draggable'}">
                <MenuItem name="draggable">Draggable</MenuItem>
              </router-link>

          </Submenu>

          <router-link :to="{name:'calendar'}">
            <MenuItem name="calendar">
              <Icon type="ios-calendar-outline" :size="20"/>
              <span>Calendar</span>
            </MenuItem>
          </router-link>

          <router-link :to="{name:'reports'}">
            <MenuItem name="reports">
              <Icon type="ios-pie-outline" :size="20"/>
              <span>Reports</span>
            </MenuItem>
          </router-link>
          <MenuItem name="10-1">
            <Icon type="ios-help-buoy-outline" :size="20"/>
            <span>Support</span></el-menu-item>
          </MenuItem>

          <MenuItem name="11-1">
            <Icon type="ios-key-outline" :size="20"/>
            <span>User Roles</span>
          </MenuItem>
      </Menu>

  </Sider>

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
        activeLink: null
      }
    },
    methods: {
          goTo: function(routeName){
            this.$router.push(routeName);
        }
    },
    computed: {
        menuitemClasses () {
            return [ 'menu-item', this.isCollapsed ? 'collapsed-menu' : '' ]
        },
        isCollapsedState () {
            return this.isCollapsed;
        }
    },
    mounted () {
      this.activeLink = this.$route.name
    },
    watch: {
      $route (newVal, oldVal) {
        //  Update the active link
        this.activeLink = newVal.name;
      }
    }
  };
</script>