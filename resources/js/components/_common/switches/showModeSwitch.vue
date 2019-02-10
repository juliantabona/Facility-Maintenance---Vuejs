<template>

    <!-- Show Mode Switch - Using Focus Ripple  -->
    <span>
        <Poptip word-wrap width="200" trigger="hover" content="Turn on to show">
            
            <!-- Focus Ripple  -->
            <focusRipple :ripple="ripple" :waveStyle="{ left:'auto', right:'16%' }">

                <!-- Icon  -->
               <Icon v-if="!showIcon" :type="showMode ? 'ios-eye-off-outline' : 'ios-eye-outline'" :size="24" />

                <!-- Text  -->
                <strong v-if="title">{{ title }}</strong>

                <!-- Switch  -->
                <i-switch :value="showMode" @on-change="$emit('update:showMode', $event)" class="ml-1" size="large">
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
        props:{
            showMode:{
                type: Boolean,
                default: false
            },
            ripple:{
                type: Boolean,
                default: true                
            },
            showIcon:{
                type: Boolean,
                default: true                
            },
            title:{
                type: String,
                default: 'Show:'                
            },
        },
        components: { focusRipple }
    }
    
</script>
