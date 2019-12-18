<style>
  
  html, *, body {
    font-weight: 400;
    text-rendering: optimizeLegibility !important;
    -webkit-font-smoothing: antialiased !important;
  }

   body{
    overflow: inherit !important;
    margin:0
  }

  html, body, h1, h2, h3, h4, h5, h6, p, span, sup{
    font-family: 'Helvetica', 'Arial', 'sans-serif';
  }

  .main-content-fade-enter-active, .main-content-fade-leave-active {
    transition: all 1s ease;
  }
  
  .main-content-fade-enter, .main-content-fade-leave-to
    /* .component-fade-leave-active for <2.1.8 */ {
    opacity: 0;
    display: none;
  }

  /*  Fix the Element UI checkbox alignment */
  .el-checkbox.el-checkbox {
    margin-left: 0px !important;
    margin-right: 30px !important;
  }

  /*  Cut text exceeding the width limit below */
  .cut-text { 
    text-overflow: ellipsis !important;
    overflow: hidden !important; 
    width: 235px !important; 
    white-space: nowrap !important;
  }

  
  /*  Makes rounded status tags - (Makes the Iview tag look like a Pill shaped tag e.g statuses in orders)  */
  .rounded-status-tag{
      padding: 0 10px 0 5px;
      border-radius: 15px;
      line-height: 25px;
      min-width: 90px;
      height: 25px;
  }

  /*  Fixes the colour dot by reducing size and provides proper alignment  */
  .rounded-status-tag >>> .ivu-tag-dot-inner {
      width: 8px;
      height: 8px;
      margin-right: 3px;
      margin-left: 2px;
      top: -1px;
  }

  /*  Removes the blue outline when clicking on any iview text button using ghost=true  */
  .ivu-btn-ghost{
    box-shadow: none !important;
  }

  /*  The badge count shows on top of the loaders so we need to make it move behind the loader   */
  .ivu-badge .ivu-badge-count{
    z-index:1 !important;
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