<style>
    .section-drawer-footer{
        width: 100%;
        position: absolute;
        bottom: 0;
        left: 0;
        border-top: 1px solid #e8e8e8;
        padding: 10px 16px;
        text-align: right;
        background: #fff;
    }
</style>

<template>
    <div>
        <Drawer
            title="Edit Section"
            v-model="visible"
            width="360"
            :mask-closable="false"
            :styles="styles"
            @on-close="abortChanges">
            <Row :gutter="32">
                <Col :span="24">
                    <b>Section Name:</b>
                    <el-input type="text" 
                        placeholder="Enter section name..." 
                        v-model="sectionInstance.name"
                        clearable
                        size="small"
                        :maxlength="30"
                        class="w-100 mb-3">
                    </el-input>

                    <b>Section Description:</b>
                    <el-input type="textarea"
                        placeholder="Enter section description..." 
                        v-model="sectionInstance.description"
                        :maxlength="200"
                        :rows="4"
                        class="w-100">
                    </el-input>
                </Col>
            </Row>
            <div class="section-drawer-footer">
                <Button style="margin-right: 8px" @click="abortChanges">Cancel</Button>
                <Button type="primary" @click="saveChanges">Save</Button>
            </div>
        </Drawer>    
    </div>
</template>
<script>
    export default {
        props:{
            show: {
                default: false
            },
            section: {
                type: Object
            }
        },
        data () {
            return {
                visible: this.show,
                _sectionBeforeChange: {},
                styles: {
                    height: 'calc(100% - 55px)',
                    overflow: 'auto',
                    paddingBottom: '53px',
                    position: 'static'
                }
            }
        },
        watch: {
            show: function (val) {
                this.visible = val;
                this._sectionBeforeChange = Object.assign({}, this.section);
            },
        },
        computed: {
            sectionInstance() {
                return this.section || {
                    name: null,
                    description: null
                };
            }
        },
        methods: {
            saveChanges(){
                this.$emit('closed');
            },
            abortChanges(){
                Object.assign(this.section, this._sectionBeforeChange);
                this.$emit('closed');
            }
        }
    }
</script>