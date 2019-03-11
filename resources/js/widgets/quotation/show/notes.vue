<template>

    <Row class="mb-5">
        <Col span="24">

            <h3 v-if="!localEditMode" class="text-dark mb-2">{{ localQuotation.notes.title }}</h3>
            <el-input v-if="localEditMode" placeholder="E.g) Notes/Payment Information" v-model="localQuotation.notes.title" size="large" class="mb-2" :style="{ maxWidth:'400px' }"></el-input>
            <br>
            <p v-if="!localEditMode" v-html="localQuotation.notes.details"></p>
            <div v-show="localEditMode">
                <froalaEditor :content.sync="localQuotation.notes.details" ></froalaEditor>     
            </div>
            
        </Col>
    </Row>

</template>


<script type="text/javascript">


    import froalaEditor from './../../../components/_common/wiziwigEditors/froalaEditor.vue';   

    export default {
        props: {
            quotation: {
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
                localQuotation: this.quotation,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the quotation
            quotation: {
                handler: function (val, oldVal) {

                    //  Update the local quotation value
                    this.localQuotation = val;

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
