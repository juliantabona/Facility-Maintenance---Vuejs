<template>

  <!-- 
    Ussd Service Billing
  -->
  <div>

    Ussd Service Billing
    
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