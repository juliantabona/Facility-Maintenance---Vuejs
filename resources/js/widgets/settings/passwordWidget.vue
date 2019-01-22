<style scoped>

    .el-form-item >>> .el-form-item__label{
        margin:0 !important;
        padding:0 !important;
        line-height: 24px !important;
    }

    .form-label{
        font-size:14px;
    }

</style>

<template>

    <Card :bordered="false">
        <h3 slot="title">Password Settings</h3>
        <Row>
            <Col :span="24" class="mt-3 mb-2">
                <Alert>Update you passwords to increase security over your account.</Alert>
            </Col>
            <Col :span="24">
                <el-form label-position="top" label-width="100px" :model="formData">

                    <Row :gutter="20" class="mb-1">

                        <Col :span="24">
                            <el-form-item label="Old Password:" prop="old_password" class="mb-2">
                                <el-input v-model="formData.old_password" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="24">
                            <el-form-item label="New Password:" prop="new_password" class="mb-2">
                                <el-input v-model="formData.new_password" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                        <Col :span="24">
                            <el-form-item label="Confirm Password:" prop="confirm_password" class="mb-2">
                                <el-input v-model="formData.confirm_password" size="small" style="width:100%"></el-input>
                            </el-form-item>
                        </Col>

                    </Row>
                    
                </el-form>
            </Col>
        </Row>
    </Card>

</template>

<script>
    import Loader from './../../components/_common/loader/Loader.vue'; 

    export default {
        components: { 
            Loader
        },
        data(){
            return {
                formData: {
                    old_password: '',
                    new_password: '',
                    confirm_password: '',
                }
            }
        }
    };
  
</script>