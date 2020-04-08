<template>

    <div :class="layout == 'inline' ? 'd-flex' : 'd-block'">

        <div :class="layout == 'inline' ? '' : 'ml-2'">

            <!-- 
                Link to screen checkbox
                Disable only if we have one screen
             -->
            <Checkbox 
                :class="layout == 'inline' ? 'mr-2 mt-2 mb-0' : 'mr-2'"
                @on-change="handleSelection('screen')"
                :disabled="linkToScreenValue"
                :value="linkToScreenValue">
                Link to screen
            </Checkbox>

            <!-- 
                Link to screen display checkbox
                Disable only if we have one display
             -->
            <Checkbox 
                :class="layout == 'inline' ? 'mt-2 mb-0' : ''"
                :disabled="disableLinkToDisplayCheckbox"
                @on-change="handleSelection('display')"
                :value="linkToDisplayValue">
                Link to display
            </Checkbox>

        </div>

        <div>
            <div v-if="linkToScreenValue" class="d-flex">
                <Icon type="ios-pin-outline" size="20" class="mt-1 ml-1 mr-2 text-muted font-weight-bold" />
                <Select v-model="localLink.name" filterable placeholder="Link to screen" 
                        @on-change="runUpdate()">

                    <Option v-for="(availableScreen, key) in screens" :key="key" 
                            :value="availableScreen.name" :label="availableScreen.name"
                            :disabled="availableScreen.name == screen.name">
                    </Option>

                </Select>
            </div>

            <div v-if="linkToDisplayValue" class="d-flex">
                <Icon type="ios-pin-outline" size="20" class="mt-1 ml-1 mr-2 text-muted font-weight-bold" />
                <Select v-model="localLink.name" filterable placeholder="Link to display" 
                        @on-change="runUpdate()">

                    <Option v-for="(availableDisplay, key) in screen.displays" :key="key"
                            :value="availableDisplay.name" :label="availableDisplay.name"
                            :disabled="availableDisplay.name == display.name">
                    </Option>

                </Select>
            </div>
        </div>

    </div>

</template>

<script>

    export default {
        props: { 
            link: {
                type: Object,
                default:() => {}
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
            layout: {
                type: String,
                default: 'block'    //  inline
            }
        },
        data(){
            return {
                localLink: this.link
            }
        },
        computed: {
            linkToScreenValue(){
                return this.localLink.type == 'screen';
            },
            linkToDisplayValue(){
                return this.localLink.type == 'display';
            },
            moreThanOneDisplayExist(){
                return (this.screen.displays.length > 1);
            },
            disableLinkToDisplayCheckbox(){

                /** We only disable the (select display link) if we don't have any other display to link to.
                 *  If we don't have more that one (1) display then we return true to disable. We also disable
                 *  the (select display link) if it is already selected. This is so that the user does
                 *  not uncheck the checkbox.
                 */
                return (this.linkToDisplayValue || !this.moreThanOneDisplayExist);

            }
        },
        methods: {
            handleSelection( linkType ){
                
                //  If we want to link to a display (We need to have enough displays to allow this)
                if( linkType == 'display' ){  

                    //  If we have less than (2) displays then we can't allow this
                    if( this.screen.displays < 2  ){

                        //  We need to default to the (link to screen) option
                        linkType = 'screen';

                    }

                }
                
                //  Update the link type
                this.localLink.type = linkType;

                this.runUpdate();

            },
            runUpdate(){

                if( this.localLink.name == undefined ){
                    
                    this.localLink.name = '';

                }

                this.$emit('on-change', this.localLink);
            }
        }
    };
  
</script>