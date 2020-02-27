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

                <!-- Screen Menu Name  -->
                <span :class="(screen.title == (activeScreen || {}).title ? 'text-primary ': '' ) + 'screen-name cut-text'" 
                      @click="handleSelectedScreen(index)">
                    {{ localScreen.title }}
                </span>

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
            activeScreen: {
                type: Object,
                default:() => {}
            },
        }, 
        data(){
            return {
                localScreen: this.screen,
            }
        },

        watch: {
            /*  Keep track of changes on the screen.  */
            screen: {

                handler: function (val, oldVal) {

                    /*  Update the local screen  */
                    this.localScreen = val;

                },
                deep: true

            },
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
                
                this.$emit('selected', index);

            },
            handleDuplicateScreen(index) {

                this.$emit('duplicate', index);

            },
            handleRemoveScreen(index) {

                this.$emit('remove', index);

            }
        }
    }

</script>