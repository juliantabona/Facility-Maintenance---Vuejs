<style scoped>

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        width: 80% !important;
        white-space: nowrap !important;
        display: block;
    }

    /*  Option Toolbox */

    .draggable-option >>> .draggable-toolbox{
        z-index: 1;
        position: relative;
        margin: -2px 0 0 0;
        background: #fff;
    }

    .draggable-option >>> .draggable-toolbox,
    .draggable-option >>> .draggable-toolbox .hidable{
        opacity:0;
    }

    .draggable-option:hover >>> .draggable-toolbox,
    .draggable-option:hover >>> .draggable-toolbox .hidable{
        opacity:1;
    }

    .draggable-option >>> .draggable-toolbox .toolbox-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .draggable-option >>> .draggable-toolbox .toolbox-icon:hover{
        color: #ffffff;
        background: #2d8cf0;
    }

    .draggable-option >>> .ivu-card-body{
        padding:0 !important;
    }

    .ivu-card >>> .ivu-card-head{
        line-height: 2;
    }

</style>

<template>

    <Card v-if="localOption" class="draggable-option mb-2">

        <!-- Option Name -->
        <div slot="title">

            <!-- Option Name Label  -->
            <span class="font-weight-bold cut-text">

                <!-- If we have a display name  -->
                <span v-if="localOption.name" class="d-inline-block" :style="{ height:'20px' }" v-html="localOption.name"></span>
                
                <!-- If we don't have a display name  -->
                <span v-else class="text-danger d-inline-block" :style="{ height:'20px' }">No display name</span>

            </span>
            
        </div>

        <!-- Option Toolbar (Edit, Move, Delete Buttons) -->
        <div slot="extra" class="d-flex" style="margin-top: 5px;">

            <!-- Failed Link Warning -->
            <Poptip trigger="hover" width="350" placement="top" word-wrap>

                <List slot="content" size="small">

                    <ListItem class="p-0">
                        <span class="font-weight-bold mr-1">Name: </span>
                        <span v-if="!localOption.name" class="text-danger">No Display Name</span>
                        <span v-else v-html="localOption.name" class="cut-text"></span>
                    </ListItem>

                    <ListItem class="p-0">
                        <span class="font-weight-bold mr-1">Value: </span>
                        <span v-if="localOption.value.code_editor_mode">

                            <Icon type="ios-code" class="mr-1" size="20" />
                            <span>Custom Code</span>

                        </span>
                        <span v-else v-html="localOption.value.text" class="cut-text"></span>
                    </ListItem>

                    <ListItem class="p-0">
                        <span class="font-weight-bold mr-1">Input: </span>
                        <span v-if="!localOption.input" class="text-danger">No Input</span>
                        <span v-else v-html="localOption.input" class="cut-text"></span>
                    </ListItem>

                    <ListItem class="p-0">
                        <span class="font-weight-bold mr-1">Link: </span>
                        <span v-if="!localOption.link.name" class="text-danger">No Link</span>
                        <span v-else class="d-flex w-100">
                            <Icon v-if="isValidLink" type="ios-pin-outline" class="mr-1" size="20" />
                            <Icon v-else type="ios-alert-outline" class="text-danger mr-1" size="20" />
                            <span class="text-primary cut-text">{{ localOption.link.name }} ({{ localOption.link.type | capitalize }})</span>
                        </span>
                    </ListItem>

                </List>

                <Icon v-if="isValidOption" type="ios-information-circle-outline" class="text-primary mr-1" :style="{ marginTop: '-5px' }" size="30" />
                <Icon v-else type="ios-alert-outline" class="text-danger mr-1" :style="{ marginTop: '-5px' }" size="30" />

            </Poptip>

            <div class="draggable-toolbox">

                <!-- Remove Option Button  -->
                <Poptip confirm title="Are you sure you want to remove this option?" 
                        ok-text="Yes" cancel-text="No" width="300" @on-ok="handleRemoveOption(index)"
                        placement="left">
                    <Icon type="ios-trash-outline" class="toolbox-icon hidable mr-2" size="20"/>
                </Poptip>

                <!-- Edit Option Button  -->
                <Icon type="ios-create-outline" class="toolbox-icon hidable mr-2" size="20" @click="handleEditOption()" />

                <!-- Copy Option Button  -->
                <Icon type="ios-copy-outline" class="toolbox-icon hidable mr-2" size="20" @click="handleDuplicateOption(index)"/>

                <!-- Move Option Button  -->
                <Icon type="ios-move" class="toolbox-icon dragger-handle hidable mr-2" size="20" />
            
            </div>

        </div>   
    
        <!-- 
            MODAL TO EDIT EXISTING EVENT
        -->
        <editOptionModal v-if="isOpenEditOptionModal" 
            @visibility="isOpenEditOptionModal = $event"
            :option="localOption"
            :screens="screens"
            :display="display"
            :screen="screen">
        </editOptionModal> 

    </Card>

</template>

<script>

    //  Get the edit option modal
    import editOptionModal from './editOptionModal.vue';

    export default {
        props:{
            index: {
                type: Number,
                default:null
            },
            display: {
                type: Object,
                default:() => {}
            },
            screen: {
                type: Object,
                default:() => {}
            },
            screens: {
                type: Array,
                default: () => []
            },
            option: {
                type: Object,
                default:() => {}
            },
            options: {
                type: Array,
                default:() => []
            }
        }, 
        components: { editOptionModal },
        data(){
            return {
                localOption: this.option,
                isOpenEditOptionModal: false
            }
        },
        watch: {
            //  Keep track of changes on the option
            option: {

                handler: function (val, oldVal) {
                    
                    //  Update the localOption
                    this.localOption = val;

                },
                deep: true

            },
        },
        computed: {

            isValidOption(){
                
                //  If the option name, input or invalid link is not provided
                if(!this.localOption.name || !this.localOption.input || !this.isValidLink){
                    
                    return false;

                }

                return true;

            },
            isValidLink(){
                
                //  If the link name is provided
                if(this.localOption.link.name){

                    //  If we are linking to a screen
                    if( this.localOption.link.type == 'screen' ){
                        
                        //  If we have a matching screen  return true otherwise false
                        return this.screens.filter( (screen) => {
                            
                            return ( screen.name == this.localOption.link.name ) ? true : false;

                        }).length ? true : false;

                    //  If we are linking to a display
                    }else if( this.localOption.link.type == 'display' ){

                        //  If we have a matching display return true otherwise false
                        return this.screen.displays.filter( (display) => {
                            
                            return ( display.name == this.localOption.link.name ) ? true : false;

                        }).length ? true : false;

                    }

                }

                //  Otherwise return true
                return true;

            },
            optionNumber(){
                /**
                 *  Returns the option number. We use this as we list the options.
                 *  It works like a counter.
                 */
                return (this.index != null ? this.index + 1 : '');
            }
        },
        methods: {
            handleEditOption(){
                this.isOpenEditOptionModal = true;
            },
            handleDuplicateOption(index) {

                //  Duplicate the option
                var duplicateOption = _.cloneDeep( this.options[index] );

                //  Create the duplicate option number
                var duplicateNumber = this.options.length + 1;

                //  Create the duplicate option name
                var duplicateName = duplicateNumber + '. Option - #' + duplicateNumber;

                //  Set the duplicate option name
                duplicateOption.name = duplicateName;

                //  Add the duplicate option to the rest of the other options
                this.options.push(duplicateOption);

            },
            handleRemoveOption(index) {

                //  Remove option from list
                this.options.splice(index, 1);

            }
        }
    }

</script>