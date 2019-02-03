<template>

    <!-- Edit Mode Switch - Using Focus Ripple  -->
    <span>
        <Poptip word-wrap width="200" trigger="hover" content="Turn on to edit invoice">
            
            <!-- Focus Ripple  -->
            <focusRipple :ripple="ripple" :waveStyle="{ left:'auto', right:'16%' }">

                <!-- Icon  -->
                <Icon type="ios-create-outline mr-1" :size="24" />

                <!-- Text  -->
                <strong>Edit Mode: </strong>

                <!-- Switch  -->
                <i-switch :value="editMode" @on-change="$emit('update:editMode', $event)" class="ml-1" size="large">
                    <span slot="open">On</span>
                    <span slot="close">Off</span>
                </i-switch>

            </focusRipple>

        </Poptip>
    </span>
    
</template>

<script type="text/javascript">
    
    /*  Ripples  */
    import focusRipple from './../ripples/focusRipple.vue';

    export default {
        props:['editMode', 'ripple'],
        components: { focusRipple }
    }
    
</script>
