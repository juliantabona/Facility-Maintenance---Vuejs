<template>

    <div>

        <div class="bg-grey-light border mt-2 p-2">

            <!-- Single Value Input Reference Name -->
            <Input v-model="localDisplay.content.action.input_value.single_value_input.reference_name" 
                maxlength="30" type="text" class="w-100 mb-2" placeholder="Reference name">
                <div slot="prepend">@</div>
            </Input>

        </div>
                
        <div class="bg-grey-light border mt-2 p-2">

            <!-- Next Screen Selector -->
            <screenLinkSelector
                @on-change="localDisplay.content.action.input_value.single_value_input.link = $event"
                :link="localDisplay.content.action.input_value.single_value_input.link"
                :display="localDisplay"
                :builder="builder"
                :screen="screen">
            </screenLinkSelector>

        </div>

    </div>
    
</template>

<script>

    //  Get the screen settings
    import screenLinkSelector from './../../../../../screenLinkSelector.vue';

    export default {
        props: { 
            display: {
                type: Object,
                default:() => {}
            },
            screen: {
                type: Object,
                default:() => {}
            },
            builder: {
                type: Object,
                default: () => {}
            }
        },
        components: { 
            screenLinkSelector
        },
        data(){
            return {
                localDisplay: this.display
            }
        }, 
        computed: {

        },
        methods: {

        }
    };
  
</script>