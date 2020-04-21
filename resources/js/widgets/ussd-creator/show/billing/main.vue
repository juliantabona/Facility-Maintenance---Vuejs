<template>

  <!-- 
    Creator Billing
  -->
  <div>

    Creator Billing
    
  </div>

</template>

<script>

    export default {
        props: {
            ussdCreator: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localUssdCreator: this.ussdCreator
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