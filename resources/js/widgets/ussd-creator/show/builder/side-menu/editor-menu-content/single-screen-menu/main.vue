<style scoped>

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 85% !important;
        white-space: nowrap !important;
        display: block;
    }

    /*  Screen Toolbox */

    .screen-name{
        cursor: pointer;
    }

    .screen-menu-option{
        position: relative;
    }

    .screen-menu-option >>> .screen-toolbox{
        top:0;
        right:0;
        background:#fff;
        position: absolute;
    }

    .screen-menu-option:hover >>> .screen-name{
        color: #3490dc !important;
    }

    .screen-menu-option:hover >>> .screen-toolbox{
        opacity:1;
    }

    .screen-menu-option >>> .screen-toolbox{
        opacity:0;
    }

    .screen-menu-option >>> .screen-toolbox .screen-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .screen-menu-option >>> .screen-toolbox .screen-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .screen-menu-option >>> .ivu-card-body{
        padding:0 !important;
    }

    .screen-details{
        padding:16px;
    }

</style>

<template>

    <div v-if="screen" class="screen-menu-option mb-1">

        <Row>

            <Col :span="24">

                <div>

                    <!-- Screen Menu Name  -->
                    <span :class="(screen.name == (activeScreen || {}).name ? 'text-primary ': '' ) + 'screen-name cut-text'" 
                        @click="handleSelectedScreen(index)">
                        {{ screen.name }}
                    </span>

                    <!-- First Display Screen Pointer -->
                    <Icon v-if="screen.first_display_screen" type="ios-pin-outline" size="20" 
                          :style="{ position: 'absolute', top: 0, right: 0 }" 
                          class="text-success font-weight-bold" />

                </div>

                <div class="screen-toolbox">

                    <!-- Remove Screen Button  -->
                    <Poptip confirm title="Are you sure you want to remove this screen?" 
                            ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveScreen(index)"
                            placement="top-start">
                        <Icon type="ios-trash-outline" class="screen-icon hidable mr-1" size="20"/>
                    </Poptip>

                    <!-- Copy Screen Button  -->
                    <Icon type="ios-copy-outline" class="screen-icon mr-1" size="20" @click="handleDuplicateScreen(index)"/>

                    <!-- Move Screen Button  -->
                    <Icon type="ios-move" class="screen-icon screen-dragger-handle mr-0" size="20" />
                
                </div>

            </Col>

        </Row> 

    </div>

</template>

<script>

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            screen: {
                type: Object,
                default:() => {}
            },
            screens: {
                type: Array,
                default: () => []
            },
            activeScreen: {
                type: Object,
                default:() => {}
            },
        }, 
        data(){
            return {
                
            }
        },
        computed: {
            getScreenNumber(){
                /**
                 *  Returns the screen number. We use this as we list the screens.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            }
        },
        methods: {
            handleSelectedScreen(index) {

                //  If the user selected any screen menu accept the current active menu
                if( (this.screens[index] || {}).name != (this.activeScreen || {}).name ){

                    //  Send an update of the selected screen
                    this.$emit('selectedScreen', index);

                }

            },
            handleDuplicateScreen(index) {
                //  Send an update on the duplicated screen
                this.$emit('duplicatedScreen', index);

            },
            handleRemoveScreen(index) {
                //  Send an update on the deleted screen
                this.$emit('removedScreen', index);

            }
        }
    }

</script>