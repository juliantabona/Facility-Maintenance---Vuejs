<template>

  <!-- 
    Layout used by authenticated users to access their dashboard
    Contains Header, SideMenu, Content and Footer
  -->
  <div class="layout">

      <Layout>

          <!-- Dashboard Header -->
          <storeHeader></storeHeader>

          <Layout class="ivu-layout-has-sider">

            <!-- Dashboard Aside -->
            <storeAside></storeAside>

            <Layout :style="{marginTop: '75px', padding: '20px'}">

                <!-- Dashboard content -->
                <Content :style="{ position: 'relative', minHeight: '2000px' }">
                    
                  <!-- Put Overview, Orders, Products e.t.c resource content here -->
                  <!-- Only authenticated users can access this content -->

                  <transition name="slide">
                      
                      <slot></slot>

                  </transition>

                </Content>

            </Layout>

          </Layout>

      </Layout>
  </div>

</template>


<script>

  import moment from 'moment';
  
  import storeHeader from './../header/store-header.vue';
  
  import storeAside from './../aside/store-aside.vue';

  export default {
    components:{ storeHeader, storeAside },
    data(){
      return {

      }
    }
  }

</script>