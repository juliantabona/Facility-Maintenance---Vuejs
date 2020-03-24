<style>

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
  <div class="layout" :style="blurBackground ? 'filter:blur(2px);' : ''">

      <Layout>
          <!-- Dashboard Header -->
          <oq-Header :isCollapsed="isCollapsed" @toggleCollapsed="isCollapsed = !isCollapsed"></oq-Header>

          <Layout class="ivu-layout-has-sider">

            <!-- Dashboard Aside -->
            <oq-Aside :isCollapsed="isCollapsed"></oq-Aside>

            <Layout :style="{marginTop: '75px', padding: '20px'}">

                <!-- Dashboard content -->
                <Content :style="{ position: 'relative', minHeight: '2000px' }">
              
                    <!-- Forced Profile Update Modal 
                          This is a modal that pops up once the user has created their account and now needs
                          to complete the setup process
                    -->
                    <updateProfileAfterSignUpModal v-if="!(this.user || {}).setup"></updateProfileAfterSignUpModal>
                    
                  <!-- Put Profile, Jobcards, Staff e.t.c resource content here -->
                  <!-- Only authenticated users can access this content -->

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

  /*  Modal  */
  import updateProfileAfterSignUpModal from './../../components/_common/modals/updateProfileAfterSignUpModal.vue';

  export default {
    components:{ updateProfileAfterSignUpModal },
    data(){
      return {
        moment: moment,
        isCollapsed: false,
        user: auth.user
      }
    },
    computed:{
      assignedCompany: function(){
        return (this.user || {}).company_id ? true : false;
      },
      blurBackground: function(){
        //  If the account setup process is not completed then blur the background
        return !(this.user || {}).setup ? true : false;
      },
    }
  }

</script>