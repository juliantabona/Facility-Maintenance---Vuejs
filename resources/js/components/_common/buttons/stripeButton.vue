<style scoped>

    @-webkit-keyframes MOVE-BG {
        from {
            -webkit-transform: translateX(0);
        }
        to { 
            -webkit-transform: translateX(46px);
        }
    }

    @keyframes MOVE-BG {
        from {
            transform: translateX(0);
        }
        to { 
            transform: translateX(46px);
        }
    }

    .loading {
        height: auto;
        text-align: center;
        color: #fff;	
        position: relative;
        overflow: hidden;
        padding: 1rem;
        font-style: italic;
        cursor:pointer;
    }

    .loading:hover {
        opacity:0.8;
    }

    .bg{
        position: absolute;
        left: -46px;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: -1;
        
        background: -webkit-repeating-linear-gradient( 145deg, #16a95f 1px, #19be6b 2px, #19be6b 11px, #16a95f 12px, #16a95f 20px );
        background: repeating-linear-gradient( -55deg, #16a95f 1px, #19be6b 2px, #19be6b 11px, #16a95f 12px, #16a95f 20px );
        
        -webkit-animation-name: MOVE-BG;
        -webkit-animation-duration: .6s;
        -webkit-animation-timing-function: linear;
        -webkit-animation-iteration-count: infinite;
        
        animation-name: MOVE-BG;
        animation-duration: 2s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
    }

</style>

<template>

    <!-- Focus Ripple  -->
    <focusRipple :ripple="ripple">

        <div :class="'loading animated fadeIn p-4' + (customClass ? ' '+customClass : '')">
            <Icon v-if="showIcon" :type="icon" :size="20" style="margin-top: -4px;"/>
            <slot></slot>
            <div class="bg"></div>
        </div>

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
            icon: {
                type: String,
                default: 'ios-add'
            },
            showIcon: {
                type: Boolean,
                default: true
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
