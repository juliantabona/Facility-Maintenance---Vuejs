<template>
  
    <Row :gutter="20">

        <Col :span="24">

            <!-- Screen Menu Heading -->    
            <div class="clearfix pb-2 mb-3 border-bottom">

                <span class="d-block mt-2 font-weight-bold text-dark float-left">Screens</span>

                <!-- Create Screen Button -->
                <Button class="p-1 float-right" @click.native="$emit('addScreen')">
                    <Icon type="ios-add" :size="20" />
                </Button>

            </div>

            <!-- Screen Menu List & Dragger  -->
            <draggable 
                class="ussd-builder-screen-menus"
                :list="builder.screens"
                v-if="screensExist"
                @start="drag=true" 
                @end="drag=false" 
                :options="{
                    group:'screen-menu',
                    draggable:'.screen-menu-option', 
                    handle:'.screen-dragger-handle'
                }">

                <!-- Single Screen Menu  -->
                <singleScreenMenu v-for="(currentScreen, index) in builder.screens" :key="index"   
                    :index="index"
                    :builder="builder"
                    :screen="currentScreen"
                    :activeScreen="screen"
                    @removedScreen="$emit('removedScreen', $event)"
                    @selectedScreen="$emit('selectedScreen', $event)"
                    @duplicatedScreen="$emit('duplicatedScreen', $event)">
                </singleScreenMenu>

            </draggable>

            <!-- No screens message -->
            <Alert v-else type="info" class="mb-2" show-icon>No screens</Alert>

        </Col>

    </Row>

</template>

<script>
    
    import draggable from 'vuedraggable';

    //  Get the single screen menu component
    import singleScreenMenu from './single-screen-menu/main.vue';

    export default {
        props: { 
            screen:{
                type: Object,
                default: () => {}
            },
            builder: {
                type: Object,
                default: () => {}
            }
        },
        components: { 
            draggable, singleScreenMenu
        },
        data(){
            return {
                
            }
        },
        computed: {

            //  Check if the screens exists
            screensExist(){
                return (((this.builder || {}).screens || []).length) ? true : false ;
            }

        },
        methods: {

        }
    };
  
</script>