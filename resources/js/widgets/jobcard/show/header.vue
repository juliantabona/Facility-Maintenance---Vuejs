<template>

    <div>
        <!-- Jobcard Menu -->
        <menuToggle class="float-right" :JobcardId="localJobcard.id" :editMode="localEditMode" 
                    @toggleEditMode="$emit('toggleEditMode', $event)">
        </menuToggle>

        <!-- Preview button -->
        <basicButton @click.native="downloadPDF({ preview: true })" size="small" class="float-right mr-2">Preview</basicButton>

        <!-- Preview button -->
        <basicButton @click.native="downloadPDF({ preview: true })" size="small" class="float-right mr-2">View Calendar</basicButton>
        
    </div>

</template>
<script type="text/javascript">

    import basicButton from './../../../components/_common/buttons/basicButton.vue';
    import menuToggle from './../../../components/_common/dropdowns/jobcardMenuDropdown.vue';
   
    export default {
        props:{
            jobcard: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        components: { basicButton, menuToggle },
        data(){
            return {
                localJobcard: this.jobcard,
                localEditMode: this.editMode
            }
        },
        watch: {
            //  Watch for changes on the jobcard
            jobcard: {
                handler: function (val, oldVal) {

                    //  Update the local jobcard value
                    this.localJobcard = val;

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
        }
    }

</script>
