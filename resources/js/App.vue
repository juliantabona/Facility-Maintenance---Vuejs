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