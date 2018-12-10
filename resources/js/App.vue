<style>
  
  html, *, body {
    font-weight: 400;
    text-rendering: optimizeLegibility !important;
    -webkit-font-smoothing: antialiased !important;
  }

  body{
    font-family: 'Helvetica', 'Arial', 'sans-serif' !important;
  }

  .main-content-fade-enter-active, .main-content-fade-leave-active {
    transition: all 1s ease;
  }
  
  .main-content-fade-enter, .main-content-fade-leave-to
    /* .component-fade-leave-active for <2.1.8 */ {
    opacity: 0;
    display: none;
  }

  /*  Fix the Element UI checkbox alignment*/
  .el-checkbox.el-checkbox {
    margin-left: 0px !important;
    margin-right: 30px !important;
  }
  
  body {
    margin:0
  }

</style>

<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script>
  export default {
    data() {
      return {
        //  Default layout is oq-Basic-Layout refer to app.js
        defaultLayout: 'Basic',

        //  Authentication Information
        authenticated: auth.check(),
        user: auth.user
      }
    },
    mounted() {
        Event.$on('userLoggedIn', () => {
            this.authenticated = true;
            this.user = auth.user;
        });
    },
    computed: {
        layout(){
          //  Check the route meta for any set layouts to use. 
          //  If not set use the default plain layout
          return 'oq-' + (this.$route.meta.layout || this.defaultLayout ) + '-Layout';
        }
    } 
  };
</script>