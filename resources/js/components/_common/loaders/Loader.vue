<template>

    <Button :type="type" :loading="loading" :long="long" size="large"><slot>Loading...</slot></Button>

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
        }
        

    }
  };
  
</script>