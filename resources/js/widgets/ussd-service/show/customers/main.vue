<template>

  <!-- 
    Ussd Service Customers
  -->
  <div>

    Ussd Service Customers
    
  </div>

</template>

<script>

    export default {
        props: {
            ussdService: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localUssdService: this.ussdService
            }
        },
        computed: {
        },
        methods: {
        },
        created(){

        }
    };
  
</script>