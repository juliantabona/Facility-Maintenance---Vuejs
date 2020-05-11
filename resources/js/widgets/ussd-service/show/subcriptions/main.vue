<template>

  <!-- 
    Ussd Service Subcriptions
  -->
  <div>

    Ussd Service Subcriptions
    
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