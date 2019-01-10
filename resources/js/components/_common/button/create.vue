<template>
    
    <!-- View Button -->
    <Button type="primary" @click.native="$emit('runCreate')" :size="size">
        Create
    </Button>

</template>
<script>
    export default {
        props:{
            size: {
                type: String,
                default: 'default'      //  default means "medium size"
            }
        }
    };
</script>