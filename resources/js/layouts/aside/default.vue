<template>

  <Sider class="layout-aside">
      
      <!-- Menu -->
      <div v-bar>
        <Menu :class="menuitemClasses"
              :style="{ maxHeight: '500px', paddingTop: '30px' }" 
              :active-name="activeLink" :open-names="['1']" theme="light">

            <router-link :to="{name:'overview'}">
              <MenuItem name="overview">
                <Icon type="ios-analytics-outline" :size="24"/>
                <span>Overview</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'overview'}">
              <MenuItem name="subscriptions">
                <Icon type="ios-chatboxes-outline" :size="24"/>
                <span>Subscriptions</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'business-tools'}">
              <MenuItem name="business-tools">
               <Icon type="ios-bulb-outline" :size="24"/>
                <span>Business Tools</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'customers'}">
              <MenuItem name="customers">
               <Icon type="ios-people-outline" :size="26"/>
                <span>Customers</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'products'}">
              <MenuItem name="products">
               <Icon type="ios-basket-outline" :size="24"/>
                <span>Products</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'users'}">
              <MenuItem name="users">
               <Icon type="ios-man-outline" :size="24"/>
                <span>Users</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'overview'}">
              <MenuItem name="settings">
                <Icon type="ios-settings-outline" :size="24"/>
                <span>Settings</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'overview'}">
              <MenuItem name="support" class="support-btn mt-4 d-block">
                <Icon type="ios-help-buoy-outline" :size="24"/>
                <span>Support</span></el-menu-item>
              </MenuItem>
            </router-link>

        </Menu>
      </div>

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