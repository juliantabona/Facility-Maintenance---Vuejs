<style scoped>

    .appointment-widget{
        position: relative;
    }

    .fade-enter,
    .fade-leave-active {
        opacity: 0;
        transform: translateX(50px);
    }
    .fade-leave-active {
        position: absolute;
    }
 
    .animated {
        transition: all 0.5s;
        display: flex;
        width: 100%;
    }

</style>

<template>

    <div id="appointment-widget">

        <!-- Get the summary header to display the appointment #, status, due date, amount due and menu options -->
        <overview 
            v-if="!createMode && localAppointment.has_approved"
            :appointment="localAppointment" 
            :editMode="editMode" 
            :createMode="createMode"
            @toggleEditMode="toggleEditMode($event)">
        </overview>

        <!-- Loaders for creating/saving appointment -->
        <Row v-if="!createMode">
            <Col :span="24">
                <div v-if="isCreatingAppointment" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                <div v-if="isSavingAppointment" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
            </Col>
        </Row>

        
        <transition-group name="fade">
            
            <Row :gutter="20" key="recurring_details" class="animated">

                <!-- Recurring toggle switch, Recurring settings toolbox, Save changes button -->
                <Col :span="24">
                
                    <!-- Save changes button -->
                    <basicButton  v-if="!createMode && appointmentHasChanged" 
                                    class="float-right pt-2 ml-4" :style="{ position:'relative' }"
                                    type="success" size="small" 
                                    :ripple="true"
                                    @click.native="saveAppointment()">
                        Save Changes
                    </basicButton>

                    <!-- Recurring Settings Icon Button -->
                    <span v-if="localAppointment.is_recurring" class="float-right d-block pt-2">
                        <div @click="showRecurringSettings = !showRecurringSettings" :style="{ position: 'relative', zIndex: '1' }">
                            <Icon :style="showRecurringSettings ? { fontSize: '20px',height: '33px',color: '#2d8cf0',background: '#eee',borderRadius: '50% 50% 0 0',padding: '3px 6px',marginTop: '-3px',boxShadow: '#c8c8c8 1px 1px 1px inset',cursor: 'pointer' }: { cursor: 'pointer' }"
                                type="ios-settings-outline" :size="20" />
                        </div>
                    </span>

                    <!-- Make recurring switch -->
                    <toggleSwitch v-bind:toggleValue.sync="localAppointment.is_recurring" 
                        @update:toggleValue="updateReccuring($event)"
                        :ripple="false" :showIcon="true" onIcon="ios-repeat" offIcon="ios-repeat" 
                        title="Make Recurring:" onText="Yes" offText="No" poptipMsg="Turn on to make recurring"
                        class="float-right p-2">
                    </toggleSwitch>

                    <div class="clearfix"></div>

                    <!-- Next recurring appointment countdown -->
                    <basicCoutdown 
                        v-if="localAppointment.has_approved_recurring_settings" 
                        class="p-3 mb-3 float-right bg-warning"
                        :date="nextRecurringAppointmentDate"
                        text="Sending next appointment in: "
                        textAtZero="Sent the scheduled appointment!">
                    </basicCoutdown>
                    
                    <!-- Make recurring settings -->
                    <Row v-show="showRecurringSettings" key="dynamic" class="animated mb-3">

                        <!-- White overlay when creating/saving appointment -->
                        <Spin size="large" fix v-if="isSavingAppointment || isCreatingAppointment" style="border-radius: 15px;"></Spin>

                        <Col span="24">
                            <div style="background:#eee;padding: 20px">

                                <!-- Get the staging toolbar to display the recurring schedule settigns, 
                                     configure payment methods aswell as automated/manual sending stages -->
                                <recurringSettingsSteps v-if="!createMode"
                                    :appointment="localAppointment" 
                                    @saved="updateAppointmentData($event)">
                                </recurringSettingsSteps>
                                
                            </div>
                        </Col>
                    </Row>

                </Col>

            </Row>

            <!-- Activity cards & Appointment Steps -->
            <Row :gutter="20" key="activity_n_steps" class="animated">
                <!-- White overlay when creating/saving appointment -->
                <Spin size="large" fix v-if="isSavingAppointment || isCreatingAppointment" style="border-radius: 15px;"></Spin>

                <!-- Acitvity cards for showing summary of activities, sent companies, and sent receipt -->
                <Col v-if="localAppointment.has_approved" :span="5">
                
                    <!-- Activity Number Card -->
                    <IconAndCounterCard title="Activity" icon="ios-pulse-outline" :count="localAppointment.activity_count.total" class="mb-2" type="success"
                                        :route="{ name: 'show-appointment-activities', params: { id: localAppointment.id } }">
                    </IconAndCounterCard>
                
                </Col>

                <!-- Appointment steps, Approval step, Sending step and Confirmation step -->
                <Col :span="localAppointment.has_approved ? 19 : 24">
                    <steps 
                        v-if="!createMode"
                        :appointment="localAppointment" :editMode="editMode" :createMode="createMode" 
                        @toggleEditMode="toggleEditMode($event)" 
                        @approved="updateAppointmentData($event)"
                        @sent="updateAppointmentData($event)"
                        @skipped="updateAppointmentData($event)"
                        @confirmed="updateAppointmentData($event)"
                        @cancelled="updateAppointmentData($event)"
                        @reminderSet="updateAppointmentData($event)">
                    </steps>
                </Col>

            </Row>

            <!-- Loaders for creating/saving appointment -->
            <Row key="create_n_save_loaders" class="animated">
                <Col :span="24">
                    <div v-if="isCreatingAppointment" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Creating, please wait...</div>
                    <div v-if="isSavingAppointment" class="mt-1 mb-3 text-center text-uppercase font-weight-bold text-success animate-opacity">Saving, please wait...</div>
                </Col>
            </Row>

            <!-- Appointment View/Editor -->
            <Row id="appointment-summary"  key="appointment_template" class="animated mb-5">
                <Col :span="24">
                    <Card :style="{ width: '100%' }">
                        
                        <!-- White overlay when creating/saving appointment -->
                        <Spin size="large" fix v-if="isSavingAppointment || isCreatingAppointment" style="border-radius: 15px;"></Spin>

                        <!-- Main header -->
                        <div slot="title">
                            <h5>Appointment Summary</h5>
                        </div>

                        <!-- Appointment options -->
                        <div slot="extra" v-if="showMenuBtn && !createMode">
                            
                            <mainHeader :appointment="localAppointment" :editMode="editMode" @toggleEditMode="toggleEditMode($event)"></mainHeader>

                        </div>

                        <Row>

                            <Col span="24" class="pr-4">

                                <!-- Create button -->
                                <basicButton v-if="createMode" 
                                                class="float-right mb-2 ml-3" 
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="createAppointment()">
                                    Create Appointment
                                </basicButton>

                                <!-- Save changes button -->
                                <basicButton  v-if="!createMode && appointmentHasChanged" 
                                                class="float-right mb-2 ml-3" :style="{ position:'relative' }"
                                                type="success" size="small" 
                                                :ripple="true"
                                                @click.native="saveAppointment()">
                                    Save Changes
                                </basicButton>

                                <!-- Edit mode switch -->
                                <editModeSwitch v-bind:editMode.sync="editMode" :ripple="false" class="float-right mb-2"></editModeSwitch>

                                <div class="clearfix"></div>

                            </Col>

                        </Row>

                        <Row>

                            <Col v-if="editMode" :span="24" class="mt-2 mb-2">
                                <h4 class="ml-2 mb-2 text-dark">Client Details</h4>
                            </Col>

                            <Col span="12" class="mb-3">

                                <!-- Client selector -->
                                <clientOrVendorSelector v-if="editMode" :style="{maxWidth: '250px'}" class="clearfix mb-2"
                                    @updated="changeClient($event)">
                                </clientOrVendorSelector>

                                <!-- Client information -->
                                <companyOrIndividualDetails 
                                    :style="{ border: '1px solid #e0e0e0', boxShadow: '1px 2px 5px #e0e0e0',borderRadius: '10px', padding: '15px' }"
                                    :editMode="editMode"
                                    refName="Client"
                                    :profile="localAppointment.client" 
                                    :modelId="( createMode ? $route.query.clientId : localAppointment.client_id )" 
                                    :modelType="( createMode ? $route.query.clientId : localAppointment.client_type )" 
                                    :showCompanyOrUserSelector="false"
                                    :showClientOrSupplierSelector="true"
                                    :showAddPhoneBtn="false"
                                    :isPhoneHideable="false"
                                    @updated:companyOrIndividual="updateClient($event)"
                                    @updated:phones="updatePhoneChanges(localAppointment.client, $event)"
                                    @reUpdateParent="storeOriginalAppointment()">
                                </companyOrIndividualDetails>

                            </Col>

                        </Row>

                        <Row>

                            <Col :span="24">
                                <Divider dashed class="mt-1 mb-2" />
                            </Col>

                            <Col v-if="editMode" :span="24" class="mt-2 mb-2">
                                <h4 class="ml-2 mb-2 text-dark">Appointment Details</h4>
                            </Col>

                            <Col span="24" class="pl-2">
                            
                                <!-- Create/Edit Appointment -->
                                <appointmentWidget 
                                    :editMode="editMode"
                                    :createMode="createMode"
                                    :appointmentId="null"
                                    v-bind:appointment.sync="localAppointment"
                                    @update:appointment="localAppointment = $event"
                                    :showClientOrSupplierSelector="true"
                                    :hideBio="false" 
                                    :hideSaveBtn="true"
                                    :hideSummaryToggle="true" 
                                    :activateSummaryMode="true"
                                    :canSaveOnCreate="false"
                                    @created:appointment=""
                                    @updated:appointment="">
                                </appointmentWidget>
                                
                            </Col>

                        </Row>

                    </Card>
                </Col>
                
            </Row>

        </transition-group>

    </div>

</template>

<script>

    /*  Local components    */
    import overview from './overview.vue';
    import steps from './steps.vue';
    import mainHeader from './header.vue';
    import recurringSettingsSteps from './recurringSettingsSteps.vue';
    import appointmentWidget from './appointmentDetails.vue'; 
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';
    import editModeSwitch from './../../../components/_common/switches/editModeSwitch.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    /*  Selectors  */
    import companyOrIndividualDetails from './companyOrIndividualDetails.vue';
    import clientOrVendorSelector from './../../../components/_common/selectors/clientOrVendorSelector.vue'; 
    
    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            overview, steps, mainHeader, appointmentWidget,
            basicButton, toggleSwitch, editModeSwitch,
            Loader, IconAndCounterCard, companyOrIndividualDetails,
            clientOrVendorSelector, recurringSettingsSteps
        },
        props: {
            appointment: {
                type: Object,
                default: function () { 
                    return {
                        subject: '',
                        agenda: '',
                        start_date: '',
                        end_date: '',
                        location: '',
                        categories: [],
                        assigned_staff: [],
                        client: null,
                    }
                }
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            },
            create: {
                type: Boolean,
                default: false
            },
            modelType:{
                type: String,
                default: ''
            },
            modelId:{
                type: Number,
                default: null
            }
        },
        data(){
            return {

                user: auth.user,

                //  Modes
                editMode: false,
                createMode: this.create,

                //  Loading States
                isSavingAppointment: false,
                isCreatingAppointment: false,

                //  Local Appointment and state changes
                localAppointment: (this.appointment || {}),
                _localAppointmentBeforeChange: {},
                appointmentHasChanged: false,
                showRecurringSettings: false
                
            }
        },
        watch: {
            localAppointment: {
                handler: function (val, oldVal) {
                    console.log('Changes detected!!!!!');
                    console.log(val);
                    console.log('checkIfappointmentHasChanged - 1');
                    this.appointmentHasChanged = this.checkIfappointmentHasChanged(val);
                },
                deep: true
            }
        },
        methods: {
            updateAppointment(appointment){
                //  Update the appointment
                this.localAppointment = appointment;

                //  Store the current state of the appointment as the original appointment
                this.storeOriginalAppointment();
                
                this.appointmentHasChanged = this.checkIfappointmentHasChanged();
            },
            toggleEditMode(activate = true){

                var self = this,
                    options = {
                        easing: 'ease-in-out',
                        offset: -100,
                        force: true,
                        cancelable: true,
                        onStart: function(element) {
                            // scrolling started
                        },
                        onDone: function(element) {
                            //  Activate edit mode
                            self.editMode = activate;
                        },
                        onCancel: function() {
                        // scrolling has been interrupted
                        },
                        x: false,
                        y: true
                    }

                //var cancelScroll = VueScrollTo.scrollTo('appointment-summary', 500, options)

                // or alternatively inside your components you can use
                var cancelScroll = this.$scrollTo('#appointment-summary', 1000, options);

                // to cancel scrolling you can call the returned function
                //cancelScroll()
            },
            updateReccuring(val){
                
                this.localAppointment.is_recurring = val ? 1 : 0;
                
                this.showRecurringSettings = val;
                
            },
            activateCreateMode: function(){
                //  Activate edit mode
                this.editMode = true;
            },
            changeClient(newClient){

                if(newClient.model_type == 'user'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.first_name +  ' ' + newClient.last_name
                    });

                }else if(newClient.model_type == 'company'){
                    this.$Notice.success({
                        title: 'Client changed to ' + newClient.name
                    });
                }

                this.client = this.$set(this.localAppointment, 'client', newClient);
                
                this.appointmentHasChanged = this.checkIfappointmentHasChanged();

            },
            updateClient(newClientDetails){

                this.client = this.$set(this.localAppointment, 'client', newClientDetails);

                this.appointmentHasChanged = this.checkIfappointmentHasChanged();

            },
            updatePhoneChanges(companyOrIndividual, phones){
                
                //  Only update if the phones have changed
                var currentPhone = _.cloneDeep(phones);
                var isNotEqual = !_.isEqual(currentPhone, companyOrIndividual.phones);

                if(isNotEqual){

                    companyOrIndividual.phones = phones;
                    
                    this.appointmentHasChanged = this.checkIfappointmentHasChanged();

                }

            },
            checkIfappointmentHasChanged: function(updatedAppointment = null){
                
                var currentAppointment = _.cloneDeep(updatedAppointment || this.localAppointment);
                var isNotEqual = !_.isEqual(currentAppointment, this._localAppointmentBeforeChange);

                return isNotEqual;
            },
            storeOriginalAppointment(){
                //  Store the original appointment
                this._localAppointmentBeforeChange = _.cloneDeep(this.localAppointment);
                console.log('stored _localAppointmentBeforeChange');
                console.log(this._localAppointmentBeforeChange);
            },
            saveAppointment(){
                var self = this;

                //  Start loader
                this.isSavingAppointment = true;

                console.log('Attempt to create appointment...');

                console.log( this.localAppointment );

                //  Form data to send
                let appointmentData = { 
                        appointment: {
                            subject: this.localAppointment.subject,
                            agenda: this.localAppointment.agenda,
                            start_date: this.localAppointment.start_date,
                            end_date: this.localAppointment.end_date,
                            location: this.localAppointment.location,
                            categories: this.localAppointment.categories.map( (category) => { return category.id } ),
                            assigned_staff: this.localAppointment.assigned_staff.map( (staff) => { return staff.id } ),
                            client_id: (this.localAppointment.client || {}).id,
                            client_model_type: (this.localAppointment.client || {}).model_type
                        }
                 };

                console.log(appointmentData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+this.localAppointment.id, appointmentData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isSavingAppointment = false;

                        /*
                        *  updateAppointmentData() : This method ensures the property is
                        *  updated as a reactive property and triggers future view updates:
                        */
                        self.updateAppointmentData(data);

                        //  Alert creation success
                        self.$Message.success('Appointment saved sucessfully!');

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingAppointment = false;

                        console.log('appointmentSummaryWidget.vue - Error saving appointment...');
                        console.log(response);
                    });
            },
            createAppointment(){

                var self = this;

                //  Start loader
                this.isCreatingAppointment = true;

                console.log('Attempt to create appointment...');

                console.log( this.localAppointment );

                //  Form data to send
                let appointmentData = { 
                        appointment: {
                            subject: this.localAppointment.subject,
                            agenda: this.localAppointment.agenda,
                            start_date: this.localAppointment.start_date,
                            end_date: this.localAppointment.end_date,
                            location: this.localAppointment.location,
                            categories: this.localAppointment.categories.map( (category) => { return category.id } ),
                            assigned_staff: this.localAppointment.assigned_staff.map( (staff) => { return staff.id } ),
                            client_id: (this.localAppointment.client || {}).id,
                            client_model_type: (this.localAppointment.client || {}).model_type
                        }
                 };

                console.log(appointmentData);

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments', appointmentData)
                    .then(({ data }) => {

                        //  Stop loader
                        self.isCreatingAppointment = false;

                        //  Disable edit mode
                        self.editMode = false;

                        //  Store the current state of the appointment as the original appointment
                        self.storeOriginalAppointment();
                        
                        console.log('checkIfappointmentHasChanged - 3');
                        self.appointmentHasChanged = self.checkIfappointmentHasChanged();

                        //  Alert creation success
                        self.$Message.success('Appointment created sucessfully!');

                        //  Notify parent of changes
                        self.$emit('appointmentCreated', data);

                        //  Go to appointment
                        self.$router.push({ name: 'show-appointment', params: { id: data.id } });

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCreatingAppointment = false;

                        console.log('appointmentSummaryWidget.vue - Error creating appointment...');
                        console.log(response);
                    });


            },
            updateAppointmentData(newAppointment){
                
                //  Disable edit mode
                this.editMode = false;
                
                /*
                 *  Vue.set()
                 *  We use Vue.set to set a new object property. This method ensures the  
                 *  property is created as a reactive property and triggers view updates:
                 */
            
                for(var x = 0; x <  _.size(newAppointment); x++){
                    var key = Object.keys(this.localAppointment)[x];
                    this.$set(this.localAppointment, key, newAppointment[key]);
                }

                //  Store the current state of the appointment as the original appointment
                this.storeOriginalAppointment();

                console.log('checkIfappointmentHasChanged - 4');

                //  Update the appointment change status
                this.appointmentHasChanged = this.checkIfappointmentHasChanged();

            },
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Store the current state of the appointment as the original appointment
                console.log('Now lets store that original appointment!');

                this.storeOriginalAppointment();

                //  Update the appointment change status
                this.appointmentHasChanged = this.checkIfappointmentHasChanged();

                if(this.createMode){
                    this.activateCreateMode();
                }

            })
        }
    };
  
</script>