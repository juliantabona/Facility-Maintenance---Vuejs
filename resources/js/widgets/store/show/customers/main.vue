<template>

  <!-- 
    Store Customers
  -->
  <div>
      
      Store Customers

  </div>

</template>

<script>

    export default {
        props: {
            store: {
                type: Object,
                default: null
            }
        },
        components:{ },
        data(){
            return {
                localStore: this.store
            }
        },
        methods: {
            
        },
        created(){

        }
    };
  
</script>