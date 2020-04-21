<template>

  <!-- 
    Creator Settings
  -->
  <div>

    Creator Settings
    
  </div>

</template>

<script>

    export default {
        props: {
            creator: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localCreator: this.creator
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