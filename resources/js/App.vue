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

  /*  Removes the blue outline when clicking on any iview text button using ghost=true  */
  .ivu-btn-ghost{
    box-shadow: none !important;
  }

  /*  The badge count shows on top of the loaders so we need to make it move behind the loader   */
  .ivu-badge .ivu-badge-count{
    z-index:1 !important;
  }

  .border-top-dashed{
    border-top: 1px dashed #dee2e6 !important;
  }

  .border-bottom-dashed{
    border-bottom: 1px dashed #dee2e6 !important;
  }
  
  .editable-content-field{
    width: 100%;
    display: block;
    overflow-y:auto;
    min-height:33px;
    resize: vertical;
    line-height: 1.5;
    color: #606266;
    padding: 5px 15px;
    font-size: inherit;
    border-radius: 4px;
    background-image: none;
    box-sizing: border-box;
    background-color: #fff;
    border: 1px solid #dcdfe6;
    -webkit-box-sizing: border-box;
    -webkit-transition: border-color .2s cubic-bezier(.645,.045,.355,1);
    transition: border-color .2s cubic-bezier(.645,.045,.355,1);
  }

  .editable-content-field.medium-height{
    min-height: 90px;
  }

  .editable-content-field > .dynamic-content-label{
    width: auto;
    height: 20px;
    margin: 0px 4px;
    padding: 4px 8px;
    border-radius: 2px;
    color: rgb(45, 140, 240);
    background-color: rgb(220, 237, 255);
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