<style scoped>

    .white {
        background-color: white !important;
    }

</style>

<template>

    <Button :type="type" :loading="loading" :long="long" size="large" :class="theme">
        <slot>Loading...</slot>
    </Button>

</template>

<script>

  export default {
    props: {
        loading: {
            type: Boolean,
            default: false
        },
        long: {
            type: Boolean,
            default: true
        },
        type: {
            type: String,
            default: 'dashed'
        },
        theme: {
            type: String,
            default: ''  
        }
        

    }
  };
  
</script>