<template>

    <Row :gutter="20">

        <Col :span="24">

            <draggable 
                :list="sections"
                @start="drag=true" 
                @end="drag=false" 
                :options="{
                    draggable:'.form-section', 
                    group:'sections', 
                    handle:'.section-dragger'}">

                <oq-Template-Section v-for="(section, index) in sections" :key="index"   
                    :section="section"
                    @removeSection="removeSection(index)"
                    @showEditSectionDrawer="showEditSectionDrawer"
                    @showFieldEditDrawer="showEditFieldDrawer"
                    @showAddFieldModal="showAddFieldModal">
                </oq-Template-Section>

            </draggable>

            <Alert v-if="!sections.length" show-icon>
                No Content Found
                <Icon type="ios-bulb-outline" slot="icon"></Icon>
                <template slot="desc">Start creating your first section and add fields to it.</template>
            </Alert>

        </Col>

        <!-- 
            DRAWER TO EDIT A SECTION
        -->
        <section-edit-drawer
            v-show="isOpenEditSectionDrawer" 
            :show="isOpenEditSectionDrawer"
            :section="storedSection"
            v-on:closed="isOpenEditSectionDrawer = !isOpenEditSectionDrawer">
        </section-edit-drawer>

        <!-- 
            MODAL TO ADD A FIELD
        -->
        <create-draggable-field 
            :show="isOpenAddFieldModal" 
            v-on:closed="isOpenAddFieldModal = false"
            v-on:created="fieldCreated">
        </create-draggable-field>

        <!-- 
            DRAWER TO EDIT A FIELD
        -->
        <field-edit-drawer
            v-show="isOpenEditFieldDrawer" 
            :show="isOpenEditFieldDrawer"
            :field="storedField"
            v-on:closed="isOpenEditFieldDrawer = !isOpenEditFieldDrawer">
        </field-edit-drawer>

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
                storedField: null,
                storedSection: null,
                isOpenAddFieldModal: false,
                isOpenEditSectionDrawer: false,
                isOpenEditFieldDrawer: false
            }
        },
        methods: {
            showEditSectionDrawer(section){
                this.storedSection = section; 
                this.isOpenEditSectionDrawer = true;
            },
            showAddFieldModal(section){
                this.storedSection = section;
                this.isOpenAddFieldModal = true;
            },
            showEditFieldDrawer(field){
                this.storedField = field; 
                this.isOpenEditFieldDrawer = true;
            },
            fieldCreated(field){
                //  Add the new field to specified section
                this.storedSection.fields.unshift(field);

                //  Open the section if it is closed
                this.storedSection.showFields = true;

                this.$Notice.success({
                    title: 'Field added successfully'
                });
            },
            removeSection(index){
                this.$delete( this.sections, index );

                this.$Notice.success({
                    title: 'Section removed successfully'
                });
            }
        }
    }
</script>