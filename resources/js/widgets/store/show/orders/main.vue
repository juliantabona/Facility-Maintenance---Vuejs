<template>

  <!-- 
    Store Orders
  -->
  <div>
      
      Store Orders

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