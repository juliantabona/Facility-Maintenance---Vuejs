<template>
    
    <!-- View Button -->
    <Button type="primary" class="float-right" @click.native="viewJobcard">
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