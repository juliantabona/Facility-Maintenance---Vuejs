<template>

  <Sider class="layout-aside">
      
      <!-- Menu -->
      <div v-bar>
        <Menu class="menu-item"
              :style="{ maxHeight: '500px', paddingTop: '30px' }" 
              :active-name="activeLink" :open-names="['1']" theme="light">

              <MenuItem name="home" class="btn-link" @click.native="navigateTo('home')">
                <Icon type="ios-home-outline" :size="24"/>
                <span>Home</span>
              </MenuItem>

              <MenuItem name="orders" class="btn-link" @click.native="navigateTo('orders')">
              <Icon type="ios-paper-outline" :size="24"/>
                <span>Orders</span>
              </MenuItem>
              
              <MenuItem name="products" class="btn-link" @click.native="navigateTo('products')">
               <Icon type="ios-basket-outline" :size="24"/>
                <span>Products</span>
              </MenuItem>

              <MenuItem name="customers" class="btn-link" @click.native="navigateTo('customers')">
               <Icon type="ios-people-outline" :size="26"/>
                <span>Customers</span>
              </MenuItem>

              <MenuItem name="analytics" class="btn-link" @click.native="navigateTo('analytics')">
                <Icon type="ios-stats-outline" :size="24"/>
                <span>Analytics</span>
              </MenuItem>

              <MenuItem name="mobile-store" class="btn-link" @click.native="navigateTo('mobile-store')">
              <Icon type="ios-phone-portrait" :size="24"/>
                <span>Mobile Store</span>
              </MenuItem>

              <MenuItem name="settings" class="btn-link" @click.native="navigateTo('settings')">
                <Icon type="ios-settings-outline" :size="24"/>
                <span>Settings</span>
              </MenuItem>

        </Menu>
      </div>

  </Sider>

</template>

<script>

  export default {
    props: {
      url:{
        type: String,
        default: null
      }
    },
    data() {
      return {
        activeLink: null,
        localUrl: this.url
      }
    },
    watch: {
      //  Keep track of changes on the url
      url: {

          handler: function (val, oldVal) {

              //  Update the local url
              this.localUrl = val;

          },
          deep: true

      }
    },
    methods: {
          navigateTo: function(linkName){
            
            if( this.localUrl ){

              //  this.$route.menu = linkName;
              this.$router.push({ name: 'show-store', params: { url: encodeURIComponent(this.localUrl) }, query: { menu: linkName } });

            }

        }
    },
    mounted () {
      this.activeLink = this.$route.menu || 'home';
    }
  };
</script>