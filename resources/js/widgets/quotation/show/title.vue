<template>

    <div>
    
        <!-- Quotation Heading -->
        <h1 v-if="!localEditMode" class="text-dark text-right" style="font-size: 35px;">{{ localQuotation.heading || '___' }}</h1>
        <el-input v-if="localEditMode" placeholder="Quotation heading" v-model="localQuotation.heading" size="large" class="mb-2" :style="{ maxWidth:'250px', float:'right' }"></el-input>

    </div>

</template>


<script type="text/javascript">


    export default {
        props: {
            quotation: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
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
