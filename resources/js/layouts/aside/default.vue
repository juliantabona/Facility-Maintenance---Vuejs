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

    .ivu-menu-vertical >>> .ivu-menu-submenu-title-icon {
      margin-right: 20px;
    }

    .support-btn{
      color: #fff;
      background: #19be6b;
    }

    .support-btn:hover{
      color: #fff;
      background: #00dc6d;
    }

    .vb >>> .vb-dragger {
        z-index: 1000;
        width: 10px;
        right: 0;
    }

    .vb >>> .vb-dragger > .vb-dragger-styler {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: rotate3d(0,0,0,0);
        transform: rotate3d(0,0,0,0);
        -webkit-transition:
            background-color 100ms ease-out,
            margin 100ms ease-out,
            height 100ms ease-out;
        transition:
            background-color 100ms ease-out,
            margin 100ms ease-out,
            height 100ms ease-out;
        background-color: rgba(48, 121, 244,.1);
        margin: 5px 5px 5px 0;
        border-radius: 20px;
        height: calc(100% - 10px);
        display: block;
    }

    .vb.vb-scrolling-phantom >>> .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.3);
    }

    .vb >>> .vb-dragger:hover > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
        margin: 0px;
        height: 100%;
    }

    .vb.vb-dragging >>> .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
        margin: 0px;
        height: 100%;
    }

    .vb.vb-dragging-phantom >>> .vb-dragger > .vb-dragger-styler {
        background-color: rgba(48, 121, 244,.5);
    }


</style>

<template>

  <Sider ref="mainSideBar" v-model="isCollapsedState" hide-trigger collapsible :collapsed-width="78"
         hide-trigger :style="{ background: '#fff', position: 'relative' }">
      
      <!--  Main Menu -->
      <div v-bar :style="{ width: (isCollapsedState ? '78px' : '200px') , top: '64px', bottom: 0, zIndex: '4' }" >
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

            <router-link :to="{name:'overview'}">
              <MenuItem name="tools">
               <Icon type="ios-bulb-outline" :size="24"/>
                <span>Business Tools</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'overview'}">
              <MenuItem name="customers">
               <Icon type="ios-people-outline" :size="26"/>
                <span>Customers</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'overview'}">
              <MenuItem name="products">
               <Icon type="ios-basket-outline" :size="24"/>
                <span>Products</span>
              </MenuItem>
            </router-link>

            <router-link :to="{name:'overview'}">
              <MenuItem name="staff">
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