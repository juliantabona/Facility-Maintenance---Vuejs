<style scoped>
    .white {
        background-color: white !important;
    }

</style>

<template>

    <div v-show="loading" :style="customStyle" :class="theme">
        <img src="/images/assets/icons/star_loader.svg" alt="Loader" style=" width: 40px;">
        <slot>Loading...</slot>
    </div>

</template>

<script>

  export default {
    props: {
        loading: {
            type: Boolean,
            default: false
        },
        customStyle:{
            type: Object,
            default: function (){ 
                return {
                    paddingLeft: '5px',
                    borderRadius:'4px'
                }
            }
        },
        theme: {
            type: String,
            default: ''  
        }
        

    }
  };
  
</script>