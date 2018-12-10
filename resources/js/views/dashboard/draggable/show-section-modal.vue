<style>

    .content-box .field-label {
        font-size: 14px;
        margin: 0 10px 5px 0;
        display: inline-block;
    }

    .field-editor .settings-box {
        max-height: 350px !important;
        padding: 20px;
        border: 1px solid #0000002b;
        -webkit-box-shadow: inset 1px 2px 5px #0000005c;
        box-shadow: inset 1px 2px 5px #0000005c;
        overflow-y:auto;
    }

    .field-editor .settings-box .options-box{
        border: 1px dotted #409eff;
        box-shadow: 10px 10px #409eff30;
        padding: 20px;
        margin-bottom: 20px;
    }

</style>

<template>
    <el-dialog
        :title="section.name"
        :visible.sync="dialogVisible"
        width="50%"
        :before-close="handleBackdropClose"
        @close="$emit('closed')">

        <el-row :gutter="20">
            <el-col v-for="field in section.fields" :key="field.id" :span="field.width" class="section-field pb-2 pt-1 mb-1 mt-1">
                <field-template :field="field" :show-settings="true"></field-template>
            </el-col>
        </el-row>

        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">Cancel</el-button>
            <el-button type="primary" @click="update()">Save Changes</el-button>
        </span>
    </el-dialog>

</template>

<script>
  export default {
        props:{
            showModal: {
                default: false
            },
            section: {
                default: null
            }
        },
        data() {
            return {
                dialogVisible: this.showModal,
            };
        },
    watch: {
        showModal: function (val) {
            this.dialogVisible = val;
        }
    },
    methods: {
        handleBackdropClose(done) {
        this.$confirm('Are you sure to close this dialog?')
            .then(_ => {
                console.log('Closed!!!');
                done();
            })
            .catch(_ => {
                console.log('Open!!!');
            });
        }
    },
    computed: {

    } 
  };
</script>