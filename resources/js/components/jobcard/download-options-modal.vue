
<template>
    <div>
        <Modal
            title="Download Options"
            v-model="modalVisible"
            @on-close="abortChanges">
            <Row :gutter="20">
                <Col :span="24">
                    <span><strong>Include the following:</strong></span>
                    <Tree :data="downlodableSections" show-checkbox></Tree>
                </Col>
            </Row>
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="abortChanges">Cancel</el-button>
                <el-button size="small" type="primary" @click="download">Download</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>
    export default {
        props:{
            show: {
                type: Boolean,
                default: false,
            }
        },
        data(){
            return{
                downlodableSections: [
                    {
                        title: 'Jobcard',
                        expand: false,
                        checked: true,
                        children: [
                            {
                                title: 'Title',
                                expand: false,
                                selected: true
                            },
                            {
                                title: 'Description',
                                expand: false
                            },
                            {
                                title: 'Deadline',
                                expand: false
                            },
                            {
                                title: 'Start Date',
                                expand: false
                            },
                            {
                                title: 'End Date',
                                expand: false
                            },
                            {
                                title: 'Priority',
                                expand: false
                            },
                            {
                                title: 'Category',
                                expand: false
                            },
                            {
                                title: 'Cost Centers',
                                expand: false
                            },
                            {
                                title: 'Created By',
                                expand: false
                            },
                            {
                                title: 'Created Date',
                                expand: false
                            },
                            {
                                title: 'Authourized By',
                                expand: false
                            },
                            {
                                title: 'Authourized Date',
                                expand: false
                            },
                        ]
                    },
                    {
                        title: 'Client',
                        expand: false,
                        checked: true,
                        children: [
                            {
                                title: 'Logo',
                                expand: false,
                                selected: true
                            },
                            {
                                title: 'Name',
                                expand: false,
                                selected: true
                            },
                            {
                                title: 'Address',
                                expand: false
                            },
                            {
                                title: 'City',
                                expand: false
                            },
                            {
                                title: 'State/Region',
                                expand: false
                            },
                            {
                                title: 'Industry',
                                expand: false
                            },
                            {
                                title: 'Type',
                                expand: false
                            },
                            {
                                title: 'Email',
                                expand: false
                            },
                            {
                                title: 'Phone',
                                expand: false
                            },
                            {
                                title: 'Fax',
                                expand: false
                            }
                        ]
                    },
                    {
                        title: 'Contractor',
                        expand: false,
                        checked: true,
                        children: [
                            {
                                title: 'Logo',
                                expand: false,
                                selected: true
                            },
                            {
                                title: 'Name',
                                expand: false,
                                selected: true
                            },
                            {
                                title: 'Address',
                                expand: false
                            },
                            {
                                title: 'City',
                                expand: false
                            },
                            {
                                title: 'State/Region',
                                expand: false
                            },
                            {
                                title: 'Industry',
                                expand: false
                            },
                            {
                                title: 'Type',
                                expand: false
                            },
                            {
                                title: 'Email',
                                expand: false
                            },
                            {
                                title: 'Phone',
                                expand: false
                            },
                            {
                                title: 'Fax',
                                expand: false
                            }
                        ]
                    }
                ]
            }
        },
        computed:{
            modalVisible:{
                get(){
                    return this.show;
                },
                set(v){ 
                    this.$emit("closed");
                }
            }
        },
        methods: {
            download(){
                this.$emit('download', this.downlodableSections);
            },
            abortChanges(){
                this.modalVisible = false;
            }
        }
    }
</script>