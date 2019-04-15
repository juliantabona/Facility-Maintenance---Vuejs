<template>

    <Row class="border-bottom mb-3 pb-3">

        <!-- Appointment Reference Number  -->
        <Col span="6">
            <h2 class="text-dark">Appointment #{{ localAppointment.id }}</h2>
        </Col>

        <!-- Invoice Client  -->
        <Col span="5">
            <h6 class="text-secondary">Customer</h6>
            <h5><router-link :to="{ name: urlName, params: { id: (localAppointment.client || {}).id } }">{{ customerName }}</router-link></h5>         
        </Col>

        <!-- Appointment Status  -->
        <Col span="4">
            <h6 class="text-secondary">Status</h6>
            <h5><AppointmentStatusTag :appointment="appointment"></AppointmentStatusTag></h5>   
        </Col>

        <!-- Appointment Type e.g) Private, Goverment, Parastatal  -->
        <Col span="4">
            <h6 class="text-secondary">Deadline</h6>     
            <h5>{{ localAppointment.end_date | moment("from", "now")  }}</h5>         
        </Col>

        <!-- Appointment Due Date  -->
        <Col span="4">
            <h6 class="text-secondary">Created</h6>
            <h5>{{ localAppointment.created_at | moment("from", "now")  }}</h5>            
        </Col>

        <!-- Appointment Menu -->
        <Col span="1">
            <menuToggle :appointmentId="localAppointment.id" :editMode="localEditMode" @toggleEditMode="$emit('toggleEditMode', $event)"></menuToggle>
        </Col>
    </Row>

</template>
<script type="text/javascript">

    /*  Statuses   */
    import AppointmentStatusTag from './../../../components/_common/statuses/AppointmentStatusTag.vue';  

    /*  Tags   */
    import priorityTag from './../../../components/_common/tags/priorityTag.vue'; 

    /*  Menu Dropdowns   */
    import menuToggle from './../../../components/_common/dropdowns/appointmentMenuDropdown.vue';

    export default {
        props: {
            editMode: {
                type: Boolean,
                default: false
            },
            appointment: {
                type: Object,
                default: null
            }
        },
        components: { AppointmentStatusTag, priorityTag, menuToggle },
        data() {
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
        },
        computed:{
            customerName: function(){
                if((this.localAppointment.client || {}).model_type == 'user'){
                    return (this.localAppointment.client || {}).full_name;
                }else if((this.localAppointment.client || {}).model_type == 'company'){
                    return (this.localAppointment.client || {}).name;
                }
            },
            urlName: function(){
                if((this.localAppointment.client || {}).model_type == 'user'){
                    return 'show-user';
                }else if((this.localAppointment.client || {}).model_type == 'company'){
                    return 'show-company';
                }
            }
        },
        methods: {
            
        },
        created () {
            
        }
    }
</script>
