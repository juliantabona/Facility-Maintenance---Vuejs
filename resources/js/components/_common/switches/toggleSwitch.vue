<template>

    <!-- Show Mode Switch - Using Focus Ripple  -->
    <span>
        <Poptip word-wrap width="200" trigger="hover" :content="poptipMsg">
            
            <!-- Focus Ripple  -->
            <focusRipple :ripple="ripple" :waveStyle="{ left:'auto', right:'16%' }">

                <!-- Icon  -->
               <Icon v-if="showIcon" :type="toggleValue ? onIcon : offIcon" :size="24" />

                <!-- Text  -->
                <strong v-if="title">{{ title }}</strong>

                <!-- Switch  -->
                <i-switch :value="toggleValue ? true : false" @on-change="$emit('update:toggleValue', $event)" class="ml-1" size="large">
                    <span slot="open">{{ onText }}</span>
                    <span slot="close">{{ offText }}</span>
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
            toggleValue:{
                type: [Number, Boolean],
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
            onIcon:{
                type: String,
                default: 'ios-eye-off-outline'    
            },
            offIcon:{
                type: String,
                default: 'ios-eye-outline'    
            },
            title:{
                type: String,
                default: 'Toggle:'                
            },
            onText:{
                type: String,
                default: 'Yes'    
            },
            offText:{
                type: String,
                default: 'No'    
            },
            poptipMsg:{
                type: String,
                default: 'Turn on to toggle'    
            }
        },
        components: { focusRipple }
    }
    
</script>
