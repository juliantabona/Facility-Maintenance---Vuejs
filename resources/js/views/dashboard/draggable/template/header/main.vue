<style>

</style>

<template>

    <Row :gutter="20" v-if="sections">

        <Col :span="24">
            <el-button type="primary" class="float-right mt-3" size="small" @click="createSectionModal = true;">+ Add Section</el-button>
        </Col>

        <Col :span="24">

            <Divider orientation="left"><h3>Sections</h3></Divider>

            <Tooltip placement="top" content="Drag and drop to order sections">
                <draggable 
                    :list="sections"
                    element="el-breadcrumb"
                    :component-data="getElementBreadCrumbData()"
                    :options="{draggable:'.bcrumb-section', group:'bcrumb-sections'}" 
                    @start="drag=true" 
                    @end="drag=false"
                    class="mb-3">

                    <el-breadcrumb-item v-for="section in sections" :key="section.id" class="bcrumb-section" @click.native="displaySectionModal(section)">
                        <el-badge :value="section.name" class="item" type="primary"></el-badge>
                    </el-breadcrumb-item>
                
                </draggable>
            </Tooltip>

        </Col>

        <create-draggable-section 
            v-if="createSectionModal"
            :show-modal="createSectionModal" 
            v-on:closed="createSectionModal = !createSectionModal;"
            v-on:created="sectionCreated">
        </create-draggable-section>

    </Row>

</template>

<script>
    import draggable from 'vuedraggable';
    export default {
        props:{
            sections: {
                default:() => {}
            }
        },
        components: {
            draggable
        },
        data(){
            return {
                createSectionModal: false,
            }
        },
        methods: {
            sectionCreated(section){
                //  Add the new section
                this.sections.unshift(section);

                this.$Notice.success({
                    title: 'Section added successfully'
                });
            },
            getElementBreadCrumbData() {
                return {
                    props: {
                        separatorClass: "el-icon-arrow-right",  //  Separator icon
                    }
                };
            }
        }
    }
</script>