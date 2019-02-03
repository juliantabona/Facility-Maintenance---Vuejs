<template>

    <!-- Focus Ripple  -->
    <focusRipple :ripple="ripple">

        <!-- Create Invoice Button  -->
        <Button :type="type" :size="size">
            <span>Create Invoice</span>
        </Button>

    </focusRipple>
    
</template>

<script type="text/javascript">
    
    /*  Ripples  */
    import focusRipple from './../ripples/focusRipple.vue';

    export default {
        props:['type', 'size', 'ripple'],
        components: { focusRipple }
    }
    
</script>
