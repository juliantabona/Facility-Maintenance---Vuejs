<template>

    <!-- Focus Ripple  -->
    <focusRipple :ripple="ripple">

        <!-- Button  -->
        <Button :type="type" :size="size" :class="customClass">
            <span><slot></slot></span>
            <Icon v-if="showIcon" type="ios-send-outline" :size="20" style="margin-top: -4px;"/>
        </Button>

    </focusRipple>
    
</template>

<script type="text/javascript">
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    export default {
        props:{
            type: {
                type: String,
                default: 'primary'
            },
            size: {
                type: String,
                default: 'small'
            },
            showIcon: {
                type: Boolean,
                default: false
            },
            ripple: {
                type: Boolean,
                default: false
            },
            customClass: {
                type: String,
                default: ''
            },
        },
        components: { focusRipple }
    }
    
</script>
