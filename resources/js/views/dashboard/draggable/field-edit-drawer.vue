<style>
    .field-drawer-footer{
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
            title="Edit Field"
            v-model="visible"
            width="500"
            :mask-closable="false"
            :styles="styles"
            @on-close="abortChanges">
            <Row :gutter="32">
                <Col :span="24">
                    <oq-Template-Field-Mockup :field="field" :show-settings="true"></oq-Template-Field-Mockup>
                </Col>
            </Row>
            <div class="field-drawer-footer">
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
            field: {
                default: null
            }
        },
        data () {
            return {
                visible: this.show,
                _fieldBeforeChange: {},
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
                this._fieldBeforeChange = Object.assign({}, this.field);
            }  
        },
        methods: {
            saveChanges(){
                this.$emit('closed');
            },
            abortChanges(){
                Object.assign(this.field, this._fieldBeforeChange);
                this.$emit('closed');
            }
        }
    }
</script>