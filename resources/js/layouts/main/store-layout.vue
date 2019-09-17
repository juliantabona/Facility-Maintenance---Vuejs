<style>
  /*  Import Ecommerce Stylesheet based on the Wookie Theme
      - https://themeforest.net/item/wokiee-ecommerce-html-template/22564267
  */
  @import '/css/wookie-store-theme.css';

  /*  Style the go back to top button */
  .back-to-top {
      padding: 9px 0;
      background: #2d8cf0;
      color: #fff;
      text-align: center;
      border-radius: 50%;
      width: 40px;
      height: 40px;
  }

  .slide-enter,
  .slide-leave-to { opacity: 0; z-index:100; }

  .slide-enter-to,
  .slide-leave { opacity: 1; z-index:200; }

  .slide-enter-active,
  .slide-leave-active { position: absolute; width:100%; } 

  .slide-enter-active,
  .slide-leave-active { transition: all 750ms ease }

</style>

<template>

  <!-- 
    Layout used by authenticated users to access their dashboard
    Contains Header, SideMenu, Content and Footer
  -->
  <div class="layout">

      <Layout>
          <!-- Store Header -->
          <storeHeader :isCollapsed="isCollapsed" @toggleCollapsed="isCollapsed = !isCollapsed"></storeHeader>

          <Layout class="ivu-layout-has-sider" :style="{ background: '#ffffff' }">

            <Layout :style="{ background: '#ffffff', marginTop: '115px', padding: '10px'}">

                <!-- Store content -->
                <Content :style="{ position: 'relative', minHeight: '1000px' }">
                    
                  <!-- Put Store, Cart, Checkout, e.t.c resource content here -->
                  <!-- Both authenticated and guest users can access this content -->

                  <transition name="slide">
                      
                      <slot></slot>

                  </transition>

                  <!-- Go Back To Top Button -->
                  <BackTop>
                    <div class="back-to-top"><Icon type="ios-arrow-dropup" :style="{ padding:'0' }"/></div>
                  </BackTop>

                </Content>

                <Footer class="layout-footer-center" :style="{ background: '#e0e6e8' }">
                    <span class="text-center d-block m-auto">{{ moment().year() }} &copy; Optimum Q - Technology Driven Solutions</span>
                </Footer>

            </Layout>

          </Layout>

      </Layout>
  </div>

</template>


<script>

  import moment from 'moment';
  import storeHeader from '../header/store-header.vue';

  export default {
    components:{ storeHeader },
    data(){
      return {
        moment: moment,
        isCollapsed: false,
        user: auth.user
      }
    }
  }

</script>