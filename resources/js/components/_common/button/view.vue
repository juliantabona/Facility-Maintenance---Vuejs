<template>
    
    <!-- View Button -->
    <Button type="primary" @click.native="viewJobcard" :size="size">
        View
        <Icon type="md-arrow-forward" />
    </Button>

</template>
<script>
    export default {
        props:{
            route: {
                type: Object,
                default: null
            },
            size: {
                type: String,
                default: 'default'      //  default means "medium size"
            }
        },
        methods: {
            viewJobcard: function(){
                if(this.route){
                    //  Go to the jobcard indicated by the route
                    this.$router.push( this.route );
                }
            }
        } 
    };
</script>