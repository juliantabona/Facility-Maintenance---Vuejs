<template>

  <!-- 
    Ussd Service Settings
  -->
  <div>

    Ussd Service Settings
    
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