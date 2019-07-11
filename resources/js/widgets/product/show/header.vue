<template>

    <div>
        <!-- Appointment Menu -->
        <menuToggle class="float-right" :AppointmentId="localAppointment.id" :editMode="localEditMode" 
                    @toggleEditMode="$emit('toggleEditMode', $event)">
        </menuToggle>

        <!-- Preview button -->
        <basicButton @click.native="downloadPDF({ preview: true })" size="small" class="float-right mr-2">Preview</basicButton>

        <!-- Preview button -->
        <basicButton @click.native="$router.push({name:'appointment-calendar'})" size="small" class="float-right mr-2">View Calendar</basicButton>
        
    </div>

</template>
<script type="text/javascript">

    import basicButton from './../../../components/_common/buttons/basicButton.vue';
    import menuToggle from './../../../components/_common/dropdowns/appointmentMenuDropdown.vue';
   
    export default {
        props:{
            appointment: {
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
                localAppointment: this.appointment,
                localEditMode: this.editMode
            }
        },
        watch: {
            //  Watch for changes on the appointment
            appointment: {
                handler: function (val, oldVal) {

                    //  Update the local appointment value
                    this.localAppointment = val;

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
