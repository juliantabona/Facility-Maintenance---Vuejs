<template>

    <Row>
        <Col>
        
            <!-- Email Address Input Field -->
            <el-input :value="localEmailAddress" size="small" style="width:100%" placeholder="Enter email address"
                        @input.native="$emit('updated:email', $event.target.value)">
            </el-input>
        
        </Col>
    </Row>

</template>

<script>

    export default {
        props:{
            emailAddress: {
                type: String,
                default: '',  
            }
        },
        data(){
            return {
                localEmailAddress: this.emailAddress
            }
        },
        watch: {

            //  Watch for changes on the email address
            emailAddress: {
                handler: function (val, oldVal) {
                    this.localEmailAddress = val;
                }
            }

        }
    };
</script>