<template>

    <!-- Summary Mode Switch - Using Focus Ripple  -->
    <span>
        <Poptip word-wrap width="200" trigger="hover" content="Turn on to add more details">
            
            <!-- Focus Ripple  -->
            <focusRipple :ripple="ripple" :waveStyle="{ left:'auto', right:'16%' }">

                <!-- Icon  -->
                <Icon type="ios-code-working" :size="24" />

                <!-- Text  -->
                <strong>More Details: </strong>

                <!-- Switch  -->
                <i-switch :value="detailMode" @on-change="$emit('update:detailMode', $event)" class="ml-1" size="large">
                    <span slot="open">Yes</span>
                    <span slot="close">No</span>
                </i-switch>

            </focusRipple>

        </Poptip>
    </span>
    
</template>

<script type="text/javascript">
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    export default {
        props:['detailMode', 'ripple'],
        components: { focusRipple }
    }
    
</script>
