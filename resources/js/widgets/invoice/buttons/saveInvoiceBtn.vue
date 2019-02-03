<template>

    <!-- Focus Ripple  -->
    <focusRipple :ripple="ripple">

        <!-- Save Changes Button  -->
        <Button :type="type" :size="size">
            <span>Save Changes</span>
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
