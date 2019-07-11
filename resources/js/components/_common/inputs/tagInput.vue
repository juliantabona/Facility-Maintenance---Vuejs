<style scoped>
    .el-tag {
        margin: 0 5px 5px 0;
    }

    .fill-blue {
        background-color: #409eff;
        border-color: #409eff;
        color: #fff;
    }

    .fill-green {
        background-color: #67c23a;
        border-color: #67c23a;
        color: #fff;
    }

    .fill-orange {
        background-color: #67c23a;
        border-color: #67c23a;
        color: #fff;
    }

    .button-new-tag {
        height: 32px;
        line-height: 30px;
        padding-top: 0;
        padding-bottom: 0;
    }
    .input-new-tag {
        width: 90px;
        margin-top: -3px;
        vertical-align: middle;
    }
</style>

<template>
    
    <Row>
        <Col :span="24">
            <scrollBox v-if="(localTags || {}).length" class="border mb-2">
                <!-- Tag list -->
                <div style="max-height: 150px;" class="p-1">
                    <el-tag v-for="(tag, index) in localTags" :key="index"
                            :disable-transitions="false" closable @close="handleClose(tag)">{{ (tag.name || tag) }}</el-tag>
                    <div v-if="tagSettings.inputDirection == 'right'" class="d-inline-block clearfix">
                        <!-- Create input -->
                        <el-input v-if="inputVisible" v-model="inputValue" class="input-new-tag d-inline-block" ref="saveTagInput" size="mini"
                            @keyup.enter.native="handleCreateTag"
                            @blur="handleCreateTag">
                        </el-input>
                        
                        <!-- Create button -->
                        <el-button v-else class="button-new-tag d-inline-block" size="small" @click="showInput">
                            <span>+ Create Tag</span>
                        </el-button>
                        
                        <!-- Tags selector -->
                        <tagSelector 
                            v-if="tagSettings.showSelector"
                            style="width:100px;padding-left: 5px;margin-top: -6px;"
                            class="float-left"
                            :tagType="tagSettings.selectableTags" 
                            :clearable="true"
                            @updated="updateTags($event)">
                        </tagSelector>
                    </div>
                </div>
            </scrollBox>
        </Col>
        <Col :span="24">

            <!-- Loader -->
            <Loader v-if="isCreating" :loading="true" type="text" class="text-left">Creating...</Loader>

            <template  v-if="!isCreating && (tagSettings.inputDirection == 'bottom' || !(localTags || {}).length)">

                <!-- Create input -->
                <el-input v-if="inputVisible" v-model="inputValue" class="input-new-tag float-left" ref="saveTagInput" size="mini"
                    @keyup.enter.native="handleCreateTag"
                    @blur="handleCreateTag">
                </el-input>
                
                <!-- Create button -->
                <el-button v-else class="button-new-tag float-left" size="small" @click="showInput">
                    <span>+ Create Tag</span>
                </el-button>
                
                <!-- Tags selector -->
                <tagSelector 
                    v-if="tagSettings.showSelector"
                    style="width:100px;padding-left: 5px;margin-top: -6px;"
                    class="float-left"
                    :tagType="tagSettings.selectableTags" 
                    :clearable="true"
                    @updated="updateTags($event)">
                </tagSelector>
                
             </template>
        </Col>
    </Row>
</template>

<script>

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue'; 

    /*  Selectors   */
    import tagSelector from "./../../../components/_common/selectors/tagSelector.vue";

    /*  scrollBox  */
    import scrollBox from './../scrollBox/scrollBox.vue'; 

    export default {
        components: {Loader, tagSelector, scrollBox},
        props:{
            tags: {
                type: Array,
                default: function(){
                    return []
                },
            },
            editMode:{
                type: Boolean,
                default: true
            },
            tagSettings:{
                type: Object,
                default: function(){
                    return {
                        showSelector: true,
                        selectableTags: 'product',
                        saveToDatabase: true,
                        inputDirection: 'bottom'
                    }
                }
            }
        },
        data(){
            return {
                localTags: this.tags,
                localEditMode: this.editMode,
                tagSelectorWidth: '25px',
                inputVisible: false,
                inputValue: '',
                isCreating: false,
            }
        },
        watch: {

            //  Watch for changes on the tags
            tags: {
                handler: function (val, oldVal) {
                    this.localTags = val;
                }
            },
            editMode: {
                handler: function (val, oldVal) {
                    this.localEditMode = val;
                }
            }

        },
        methods: {
            handleClose(tag) {
                this.localTags.splice(this.localTags.indexOf(tag), 1);
            },

            showInput() {
                this.inputVisible = true;
                this.$nextTick(_ => {
                this.$refs.saveTagInput.$refs.input.focus();
                });
            },
            handleCreateTag() {
                let inputValue = this.inputValue;
                if (inputValue) {
                    if(this.tagSettings.saveToDatabase){
                        this.createAndSaveTag();
                    }else{
                        this.updateTags(inputValue);
                    }
                }
                this.inputVisible = false;
                this.inputValue = '';
            },
            createAndSaveTag() {
                const self = this;

                //  Start loader
                self.isCreating = true;

                let inputValue = this.inputValue;

                console.log('Attempt to create new tag...');
                    
                var tagData = { 
                        name: inputValue,
                        type: this.tagSettings.selectableTags
                    };

                console.log(tagData); 

                //  Additional data to eager load along with the product
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/tags'+connections, tagData)
                    .then(({data}) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isCreating = false;
                        
                        //  Alert creation success
                        self.$Message.success('Created sucessfully!');

                        self.updateTags(tagData);

                    })         
                    .catch(response => { 
                        console.log('widgets/product/show/main.vue - Error creating tag...');
                        console.log(response);

                        //  Stop loader
                        self.isCreating = false;

                    });
            },
            updateTags(newTag){
                //  Add to existing tags
                this.localTags.push(newTag);

                //  Notify parent
                this.$emit('updated', this.localTags);
            }
        },
        created(){
            console.log('tagInput Ready');
        }
    };
</script>