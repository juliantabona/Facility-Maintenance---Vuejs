<style>

</style>

<template>

    <Card>

        <span slot="extra">
            <el-button type="success" class="float-right ml-2" size="small" @click="saveChanges()">
                Save Changes
            </el-button>
        </span>
        <Row :gutter="20" class="mt-3">
            <Col v-if="sections" :span="24">
                <Divider orientation="left"><Icon type="ios-information-circle-outline" size="20" /> Template Details</Divider>
                <el-input v-model="sections.name" type="text"  placeholder="Template name..." size="mini" :maxlength="100" class="w-100 mb-2"></el-input>
                <el-input v-model="sections.description" type="textarea" placeholder="Template description..." rows="4" :maxlength="300" resize class="w-100"></el-input>
            </Col>
            <Col v-if="template" :span="24">
                <Divider orientation="left">Category</Divider>
                <el-checkbox-group
                    v-model="template.category.value" class="w-100">
                    <el-checkbox
                        v-for="option in template.category.options"
                        :key="option.value"
                        :label="option.value"
                        :disabled="option.disabled">
                        {{ option.value }}
                    </el-checkbox>
                </el-checkbox-group>
            </Col>
            <Col :span="24">
                <Divider orientation="left"><Icon type="ios-copy-outline" size="20" /> Document Templates</Divider>
                <Table ref="selection" :columns="docTemplateColumns" :data="docTemplateData"></Table>
            </Col>
        </Row>
    </Card>

</template>

<script>
    export default {
        props:{
            sections: {
                default:() => {}
            },
            template: {
                default:() => {}
            }
        },
        data(){
            return {
                docTemplateColumns: [
                    {
                        type: 'selection',
                        width: 35,
                        align: 'center'
                    },  
                    {
                        title: 'Name',
                        key: 'name'
                    },
                    {
                        title: 'Action',
                        key: 'action',
                        fixed: 'right',
                        width: 120,
                        render: (h, params) => {
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'text',
                                        size: 'small'
                                    }
                                }, 'View'),
                                h('Button', {
                                    props: {
                                        type: 'text',
                                        size: 'small'
                                    }
                                }, 'Edit')
                            ]);
                        }
                    }
                ],
                docTemplateData: [
                    {
                        name: 'Asset Summary'
                    },
                    {
                        name: 'Jobcard Summary'
                    },
                    {
                        name: 'Supplier Jobcard'
                    },
                    {
                        name: 'Client Signoff'
                    }
                ],
            }
        }
    }
</script>