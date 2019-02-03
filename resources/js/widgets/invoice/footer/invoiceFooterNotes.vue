<template>

    <Row class="mb-5">
        <Col span="24">

            <h3 v-if="!localEditMode" class="text-dark mb-2">{{ localInvoice.notes.title }}</h3>
            <el-input v-if="localEditMode" placeholder="E.g) Notes/Payment Information" v-model="localInvoice.notes.title" size="large" class="mb-2" :style="{ maxWidth:'400px' }"></el-input>
            <br>
            <p v-if="!localEditMode" v-html="localInvoice.notes.details"></p>
            <div v-show="localEditMode">
                <froalaEditor :content.sync="localInvoice.notes.details" ></froalaEditor>     
            </div>
            
        </Col>
    </Row>

</template>


<script type="text/javascript">


    import froalaEditor from './../wiziwigEditors/froalaEditor.vue';   

    export default {
        props: {
            invoice: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            },
        },
        components: { froalaEditor },
        data(){
            return {
                localInvoice: this.invoice,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

                },
                deep: true
            },

            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {

                    //  Update the edit mode value
                    this.localEditMode = val;

                }
            }

        },
    }

</script>
