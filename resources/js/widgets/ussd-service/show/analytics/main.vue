<template>

  <!-- 
    Ussd Service Analytics
  -->
  <div>

    Ussd Service Analytics
    
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