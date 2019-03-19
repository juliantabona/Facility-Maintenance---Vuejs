<template>

    <div>
        <!-- Company Menu -->
        <menuToggle class="float-right" :CompanyId="localCompany.id" :editMode="localEditMode" 
                    @toggleEditMode="$emit('toggleEditMode', $event)">
        </menuToggle>

        <!-- Preview button -->
        <basicButton @click.native="downloadPDF({ preview: true })" size="small" class="float-right mr-2">Preview</basicButton>

    </div>

</template>
<script type="text/javascript">

    import basicButton from './../../../components/_common/buttons/basicButton.vue';
    import menuToggle from './../../../components/_common/dropdowns/companyMenuDropdown.vue';
   
    export default {
        props:{
            company: {
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
                localCompany: this.company,
                localEditMode: this.editMode
            }
        },
        watch: {
            //  Watch for changes on the company
            company: {
                handler: function (val, oldVal) {

                    //  Update the local company value
                    this.localCompany = val;

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
