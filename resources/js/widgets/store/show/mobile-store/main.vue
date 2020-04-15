<template>

  <!-- 
    Mobile Store
  -->
  <div>

    <!-- USSD Interface Tab -->
    <ussdInterfaceWidget :store="store"></ussdInterfaceWidget>
    
  </div>

</template>

<script>

    import ussdInterfaceWidget from './../ussdInterfaceWidget.vue';

    export default {
        props: {
            store: {
                type: Object,
                default: null
            }
        },
        components:{ ussdInterfaceWidget },
        data(){
            return {
                
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