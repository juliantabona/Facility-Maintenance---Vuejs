<template>
    

    <div>

        <div class="d-flex mb-2">
            
            <span class="font-weight-bold text-dark d-block mt-1 mb-2 mr-2">Markers:</span>

            <!-- Marker list -->
            <el-tag v-for="(marker, index) in screen.markers" class="mr-1"
                    :key="index" :disable-transitions="false" closable
                    @close="handleRemoveMarker(index)"
                    placeholder="e.g Main Menu">
                {{ marker }}
            </el-tag>

        </div>

        <div class="bg-grey-light p-2 mx-0 mb-2">

            <div v-if="inputVisible || !builder.markers.length" class="d-flex">

                <!-- Create Marker Input -->
                <Input 
                    type="text"
                    v-model="inputValue"
                    @blur="addMarker(inputValue)"
                    placeholder="Marker name e.g Main Menu"
                    @keyup.enter.native="addMarker(inputValue)">
                </Input>
            
                <!-- Close button -->
                <Button v-if="builder.markers.length" class="ml-1" type="error" @click.native="hideInput()">
                    <span>Close</span>
                </Button>

            </div>

            <div v-else class="d-flex">

                <!-- Marker selector -->
                <Select
                    not-found-text="No markers found" 
                    @on-change="addMarker($event)"
                    placeholder="Select marker" 
                    v-model="selectedMarker"
                    :key="renderKey"
                    filterable>
                    <Option v-for="(marker, index) in builder.markers" 
                        :value="marker" :key="index">{{ marker }}</Option>
                </Select>
            
                <!-- Create button -->
                <Button class="py-1 px-2 ml-2" @click.native="showInput()">
                    <Icon type="ios-add" :size="20" />
                    <span>Create Marker</span>
                </Button>

            </div>

        </div>

    </div>

</template>

<script>

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
        data(){
            return {
                renderKey: 1,
                inputValue: '',
                inputVisible: false,
                selectedMarker: null
            }
        }, 
        methods: {

            hideInput() {

                //  Hide the create marker input
                this.inputVisible = false;

            },

            showInput() {

                //  Show the create marker input
                this.inputVisible = true;

                const self = this;

                //  Focus on the create marker input
                this.$nextTick(() => {

                    //  Focus on the reply input field
                    self.$refs.inputValue.$refs.input.focus();

                });

            },

            addMarker(newMarker){

                //  Add the marker to the screen
                this.handleAddMarkerToScreen(newMarker);

                //  Add the marker to the builder
                this.handleAddMarkerToBuilder(newMarker);

                //  Empty the input value
                this.inputValue = '';

                //  Reset the select input
                this.resetSelect();

            },

            resetSelect(){

                //  Unselect any selected marker
                this.selectedMarker = null;

                //  Reset the select input
                this.renderKey += 1;
            },

            handleAddMarkerToScreen(newMarker){

                //  Check if the new marker name alrady exists
                var alreadyExists = this.screen.markers.filter( (marker) => { 
                    return marker == newMarker;
                }).length ? true : false;

                //  If the marker does not already exist
                if( !alreadyExists && newMarker != ''){
                    
                    //  Add the new marker
                    this.screen.markers.push( newMarker );

                }

            },

            handleAddMarkerToBuilder(newMarker){

                //  Check if the new marker name alrady exists
                var alreadyExists = this.builder.markers.filter( (marker) => { 
                    return marker == newMarker;
                }).length ? true : false;

                //  If the marker does not already exist
                if( !alreadyExists && newMarker != '' ){

                    //  Add the new marker
                    this.builder.markers.push( newMarker );

                }

            },

            handleRemoveMarker(index) {
                this.screen.markers.splice(index, 1);
            },

        }
    };
  
</script>