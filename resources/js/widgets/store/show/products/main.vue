<template>

  <!-- 
    Store Products
  -->
  <div>
      
      Store Products

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