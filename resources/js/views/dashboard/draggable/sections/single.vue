<style scoped>

    /*  Section Field */

    .form-section >>> .section-field{
        position: relative;
        list-style: none;
        cursor: pointer;
        border: 1px dotted transparent;
        transition:all 1s ease;
    }

    .form-section >>> .section-field:hover{
        border: 1px dotted #adadad;
        background: #409eff12;
    }

    .form-section >>> .section-field .field-label{
        font-size: 14px;
        margin: 0 10px 5px 0;
        display: inline-block;
    }

    .form-section >>> .section-field input,
    .form-section >>> .section-field span{
            font-size: 12px;
    }

    .section-title {
        color: #515a6e;
    }

    .section-desc {
        color: #808695;
        font-weight: 100;
    }

    .cut-text { 
        text-overflow: ellipsis !important;
        overflow: hidden !important; 
        width: 50% !important; 
        white-space: nowrap !important;
    }

    .unbolded >>> .field-label{
        font-weight:100 !important;
    }

</style>

<template>

    <Card 
        v-if="section"
        :id="section.id"
        :key="section.id" 
        :class="'box-card form-section mb-2'+(!section.showFields ? ' hidden-content':'')">
        <div slot="title">
            <h3 class="section-title">{{section.name}}</h3>
            <p class="section-desc cut-text">{{section.description}}</p>
        </div>
        <div slot="extra">
            <el-tooltip class="item" effect="dark" content="Add Field" placement="top-start">
                <el-button style="float: right;" type="primary" icon="el-icon-plus" circle
                            @click="$emit('showAddFieldModal', section)">
                </el-button>
            </el-tooltip>
            <div class="section-toolbox float-right d-block">
                <Poptip
                    confirm
                    title="Are you sure you want to delete this section?"
                    ok-text="Yes"
                    cancel-text="No"
                    @on-ok="$emit('removeSection')">
                    <Icon type="ios-trash-outline" class="section-icon hidable mr-2" size="20"/>
                </Poptip>
                <Icon type="ios-create-outline" class="section-icon hidable mr-2" size="20" @click="$emit('showEditSectionDrawer', section)" />
                <Icon type="ios-move" class="section-icon section-dragger hidable mr-2" size="20" />
                <Icon :type="'ios-arrow-'+(section.showFields ? 'dropdown hidable ':'dropup glowable ')" class="section-icon mr-2" size="20" 
                      @click="section.showFields = !section.showFields"/>
            </div>
        </div>
        <draggable 
            v-show="section.showFields"
            element="Row"
            :component-data="getElementRowData()"
            :list="section.fields" 
            :move="checkMove"
            :options="{draggable:'.section-field', group:'section-fields', handle:'.field-dragger'}" 
            @start="drag=true" 
            @end="drag=false" 
            style="min-height:50px;">
                
            <oq-Template-Field 
                v-for="(field, index) in section.fields" :key="index" 
                :field="field" :section="section"
                @removeField="removeField(index)"
                @showFieldEditDrawer="$emit('showFieldEditDrawer', $event)"
                class="unbolded">
            </oq-Template-Field>

        </draggable>
        <Alert v-if="!section.showFields && section.fields.length">
            {{ countFields }} - <span class="btn btn-link btn-sm p-0" @click="section.showFields = !section.showFields">Open</span>
        </Alert>
        <Alert v-if="!section.fields.length" type="warning">
            No Content Found - <span class="btn btn-link btn-sm p-0" @click="$emit('showAddFieldModal', section)">Add Field</span>
        </Alert>

    </Card>

</template>

<script>
    import draggable from 'vuedraggable';
    export default {
        props:{
            section: {
                default:() => {}
            }
        },
        components: {
            draggable
        },
        data(){
            return {
                
            }
        },
        methods: {
            removeField(index){
                console.log(index);
                this.$delete( this.section.fields, index );

                this.$Notice.success({
                    title: 'Field removed successfully'
                });

            },
            checkMove: function(evt){
                console.log('evt.draggedContext');
                console.log(evt.draggedContext);
                console.log('evt.relatedContext');
                console.log(evt.relatedContext);
            },
            getElementRowData() {
                return {
                    props: { 
                        gutter: 20              //  Allow spacing between row columns
                    }
                };
            }
        },
        computed: {
            countFields(){
                let fieldCount = this.section.fields.length;
                return fieldCount == 1 ? fieldCount + ' Field Found' : fieldCount + ' Fields Found';
            }
        }
    }
</script>