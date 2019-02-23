<template>

    <div>

        <!-- Fade loader - Shows when saving the recurring invoice schedule plan  -->
        <fadeLoader :loading="isSavingRecurringSchedulePlan" msg="Saving schedule plan, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="1" :showCheckMark="localInvoice.has_set_recurring_schedule_plan && !isEditingSchedulePlan" 
            :showHeader="!localInvoice.has_approved_recurring_schedule" 
            :disabled="false" :showVerticalLine="true" :leftWidth="24"
            :isSaving="isSavingRecurringSchedulePlan">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This recurring schedule is not approved. You can only approve once all 3 steps are completed. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
            
            </template>

            <template slot="leftContent">

                <h4 :class="'text-secondary' + ( isEditingSchedulePlan ? ' mt-3 mb-4': '')">Schedule Plan:</h4>
                <div v-if="!isEditingSchedulePlan" class="d-inline-block mt-2" :style="{ lineHeight: '1.6em' }">
                    <p><b>Repeat weekly:</b> Every Tuesday</p>
                    <p><b>Dates:</b> Create first invoice on February 19th 2019,end after 2 invoices</p>
                    <p><b>Time zone:</b> Africa/Gaborone</p>
                </div>

            </template>

            <!-- Extra Content  -->
            <template slot="extraContent">

                <!-- Schedule settings -->
                <Row v-if="isEditingSchedulePlan">

                    <!-- Schedule Frequency e.g) Daily, Weekly, Monthly, Yearly or Custom  -->
                    <Col span="24">
                    
                        <!-- Reminder method Selector -->
                        <span class="float-left d-block mr-1 mb-3">Repeat this invoice</span>

                        <!-- Timeline Options -->
                        <Select v-model="localInvoice.recurringSettings.schedulePlan.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select frequency">
                            <Option value="Daily">Daily</Option>
                            <Option value="Weekly">Weekly</Option>
                            <Option value="Monthly">Monthly</Option>
                            <Option value="Yearly">Yearly</Option>
                            <Option value="Custom">Custom</Option>
                        </Select>

                        <!-- If Weekly -->
                        <span v-show="localInvoice.recurringSettings.schedulePlan.chosen == 'Weekly'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1">every</span>
                            <Select v-model="localInvoice.recurringSettings.schedulePlan.weekly" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day of week">
                                <Option value="Monday">Monday</Option>
                                <Option value="Tuesday">Tuesday</Option>
                                <Option value="Wednesday">Wednesday</Option>
                                <Option value="Thursday">Thursday</Option>
                                <Option value="Friday">Friday</Option>
                                <Option value="Saturday">Saturday</Option>
                                <Option value="Sunday">Sunday</Option>
                            </Select>
                        </span>
                        
                        <!-- If Monthly -->
                        <span v-show="localInvoice.recurringSettings.schedulePlan.chosen == 'Monthly'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1">on the</span>
                            <Select v-model="localInvoice.recurringSettings.schedulePlan.monthly" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day">
                                <Option value="1st">1st</Option>
                                <Option value="2nd">2nd</Option>
                                <Option value="3rd">3rd</Option>
                                <Option value="4th">4th</Option>
                                <Option value="5th">5th</Option>
                                <Option value="6th">6th</Option>
                                <Option value="7th">7th</Option>
                                <Option value="8th">8th</Option>
                                <Option value="9th">9th</Option>
                                <Option value="10th">10th</Option>
                                <Option value="11th">11th</Option>
                                <Option value="12th">12th</Option>
                                <Option value="13th">13th</Option>
                                <Option value="14th">14th</Option>
                                <Option value="15th">15th</Option>
                                <Option value="16th">16th</Option>
                                <Option value="17th">17th</Option>
                                <Option value="18th">18th</Option>
                                <Option value="19th">19th</Option>
                                <Option value="20th">20th</Option>
                                <Option value="21st">21st</Option>
                                <Option value="22nd">22nd</Option>
                                <Option value="23rd">23rd</Option>
                                <Option value="24th">24th</Option>
                                <Option value="25th">25th</Option>
                                <Option value="26th">26th</Option>
                                <Option value="27th">27th</Option>
                                <Option value="28th">28th</Option>
                                <Option value="29th">29th</Option>
                                <Option value="30th">30th</Option>
                                <Option value="31st">31st</Option>
                            </Select>
                            <span class="float-left d-block ml-1 mr-1">day of every month</span>
                        </span>

                        <!-- If Yearly -->
                        <span v-show="localInvoice.recurringSettings.schedulePlan.chosen == 'Yearly'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1">every</span>
                            <Select v-model="localInvoice.recurringSettings.schedulePlan.yearly.month" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day of week">
                                <Option value="January">January</Option>
                                <Option value="February">February</Option>
                                <Option value="March">March</Option>
                                <Option value="April">April</Option>
                                <Option value="May">May</Option>
                                <Option value="June">June</Option>
                                <Option value="July">July</Option>
                                <Option value="August">August</Option>
                                <Option value="September">September</Option>
                                <Option value="October">October</Option>
                                <Option value="November">November</Option>
                                <Option value="December">December</Option>
                            </Select>
                            
                            <span class="float-left d-block ml-1 mr-1">on the</span>
                            <Select v-model="localInvoice.recurringSettings.schedulePlan.yearly.day" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day">
                                <Option value="1st">1st</Option>
                                <Option value="2nd">2nd</Option>
                                <Option value="3rd">3rd</Option>
                                <Option value="4th">4th</Option>
                                <Option value="5th">5th</Option>
                                <Option value="6th">6th</Option>
                                <Option value="7th">7th</Option>
                                <Option value="8th">8th</Option>
                                <Option value="9th">9th</Option>
                                <Option value="10th">10th</Option>
                                <Option value="11th">11th</Option>
                                <Option value="12th">12th</Option>
                                <Option value="13th">13th</Option>
                                <Option value="14th">14th</Option>
                                <Option value="15th">15th</Option>
                                <Option value="16th">16th</Option>
                                <Option value="17th">17th</Option>
                                <Option value="18th">18th</Option>
                                <Option value="19th">19th</Option>
                                <Option value="20th">20th</Option>
                                <Option value="21st">21st</Option>
                                <Option value="22nd">22nd</Option>
                                <Option value="23rd">23rd</Option>
                                <Option value="24th">24th</Option>
                                <Option value="25th">25th</Option>
                                <Option value="26th">26th</Option>
                                <Option value="27th">27th</Option>
                                <Option value="28th">28th</Option>
                                <Option value="29th">29th</Option>
                                <Option value="30th">30th</Option>
                                <Option value="31st">31st</Option>
                            </Select>
                        </span>

                        <!-- If Custom -->
                        <span v-show="localInvoice.recurringSettings.schedulePlan.chosen == 'Custom'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1 mb-3">every</span>
                            <el-input v-model="localInvoice.recurringSettings.schedulePlan.custom.count" placeholder="E.g) 6" size="mini" class="float-left d-block mr-1 mb-3" :style="{ maxWidth:'60px', marginTop:'-3px' }"></el-input>
                            <Select v-model="localInvoice.recurringSettings.schedulePlan.custom.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week">
                                <Option value="Days">Day(s)</Option>
                                <Option value="Weeks">Week(s)</Option>
                                <Option value="Months">Month(s)</Option>
                                <Option value="Years">Year(s)</Option>
                            </Select>

                            <!-- If weeks -->
                            <span v-show="localInvoice.recurringSettings.schedulePlan.custom.chosen == 'Weeks'">
                                <span class="float-left d-block ml-1 mr-1 mb-3">on every</span>
                                <Select v-model="localInvoice.recurringSettings.schedulePlan.custom.weeks" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week">
                                    <Option value="Monday">Monday</Option>
                                    <Option value="Tuesday">Tuesday</Option>
                                    <Option value="Wednesday">Wednesday</Option>
                                    <Option value="Thursday">Thursday</Option>
                                    <Option value="Friday">Friday</Option>
                                    <Option value="Saturday">Saturday</Option>
                                    <Option value="Sunday">Sunday</Option>
                                </Select>
                            </span>
                            
                            <!-- If months -->
                            <span v-show="localInvoice.recurringSettings.schedulePlan.custom.chosen == 'Months'">
                                <span class="float-left d-block ml-1 mr-1 mb-3">on the</span>
                                    <Select v-model="localInvoice.recurringSettings.schedulePlan.custom.months" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day">
                                        <Option value="1st">1st</Option>
                                        <Option value="2nd">2nd</Option>
                                        <Option value="3rd">3rd</Option>
                                        <Option value="4th">4th</Option>
                                        <Option value="5th">5th</Option>
                                        <Option value="6th">6th</Option>
                                        <Option value="7th">7th</Option>
                                        <Option value="8th">8th</Option>
                                        <Option value="9th">9th</Option>
                                        <Option value="10th">10th</Option>
                                        <Option value="11th">11th</Option>
                                        <Option value="12th">12th</Option>
                                        <Option value="13th">13th</Option>
                                        <Option value="14th">14th</Option>
                                        <Option value="15th">15th</Option>
                                        <Option value="16th">16th</Option>
                                        <Option value="17th">17th</Option>
                                        <Option value="18th">18th</Option>
                                        <Option value="19th">19th</Option>
                                        <Option value="20th">20th</Option>
                                        <Option value="21st">21st</Option>
                                        <Option value="22nd">22nd</Option>
                                        <Option value="23rd">23rd</Option>
                                        <Option value="24th">24th</Option>
                                        <Option value="25th">25th</Option>
                                        <Option value="26th">26th</Option>
                                        <Option value="27th">27th</Option>
                                        <Option value="28th">28th</Option>
                                        <Option value="29th">29th</Option>
                                        <Option value="30th">30th</Option>
                                        <Option value="31st">31st</Option>
                                </Select>
                                <span class="float-left d-block ml-1 mr-1 mb-3">day of every month</span>
                            </span>

                            <!-- If years -->
                            <span v-show="localInvoice.recurringSettings.schedulePlan.custom.chosen == 'Years'">
                                <span class="float-left d-block ml-1 mr-1 mb-3">on every</span>
                                <Select v-model="localInvoice.recurringSettings.schedulePlan.custom.years.month" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week">
                                    <Option value="January">January</Option>
                                    <Option value="February">February</Option>
                                    <Option value="March">March</Option>
                                    <Option value="April">April</Option>
                                    <Option value="May">May</Option>
                                    <Option value="June">June</Option>
                                    <Option value="July">July</Option>
                                    <Option value="August">August</Option>
                                    <Option value="September">September</Option>
                                    <Option value="October">October</Option>
                                    <Option value="November">November</Option>
                                    <Option value="December">December</Option>
                                </Select>
                                <span class="float-left d-block ml-1 mr-1 mb-3">on the</span>
                                <Select v-model="localInvoice.recurringSettings.schedulePlan.custom.years.day" :style="{ width:'60px', marginTop:'-5px' }" class="float-left mb-3" placeholder="Select day">
                                    <Option value="1st">1st</Option>
                                    <Option value="2nd">2nd</Option>
                                    <Option value="3rd">3rd</Option>
                                    <Option value="4th">4th</Option>
                                    <Option value="5th">5th</Option>
                                    <Option value="6th">6th</Option>
                                    <Option value="7th">7th</Option>
                                    <Option value="8th">8th</Option>
                                    <Option value="9th">9th</Option>
                                    <Option value="10th">10th</Option>
                                    <Option value="11th">11th</Option>
                                    <Option value="12th">12th</Option>
                                    <Option value="13th">13th</Option>
                                    <Option value="14th">14th</Option>
                                    <Option value="15th">15th</Option>
                                    <Option value="16th">16th</Option>
                                    <Option value="17th">17th</Option>
                                    <Option value="18th">18th</Option>
                                    <Option value="19th">19th</Option>
                                    <Option value="20th">20th</Option>
                                    <Option value="21st">21st</Option>
                                    <Option value="22nd">22nd</Option>
                                    <Option value="23rd">23rd</Option>
                                    <Option value="24th">24th</Option>
                                    <Option value="25th">25th</Option>
                                    <Option value="26th">26th</Option>
                                    <Option value="27th">27th</Option>
                                    <Option value="28th">28th</Option>
                                    <Option value="29th">29th</Option>
                                    <Option value="30th">30th</Option>
                                    <Option value="31st">31st</Option>
                                </Select>
                            </span>
                        </span>
                    </Col>

                    <!-- Schedule Start and End period  -->
                    <Col span="24" class="border-top pt-3">
                    
                        <!-- Text for when to create first invoice -->
                        <span class="float-left d-block mr-1 mb-3">Create first invoice on</span>

                        <!-- First Invoice - Start Date -->
                        <el-date-picker v-model="localInvoice.recurringSettings.schedulePlan.start" type="date" :clearable="false" placeholder="e.g) January 1, 2018" size="mini" class="float-left mb-2" :style="{ maxWidth:'135px', marginTop:'-3px' }"
                            format="MMM dd yyyy" value-format="yyyy-MM-dd">
                        </el-date-picker>

                        <!-- Text for when to end -->
                        <span class="float-left d-block mr-1 ml-1 mb-3">and end</span>

                        <!-- Text for when to end -->
                        <Select v-model="localInvoice.recurringSettings.schedulePlan.stop.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left mb-3" placeholder="Select day of week">
                            <Option value="Count">After</Option>
                            <Option value="Date">On</Option>
                            <Option value="Never">Never</Option>
                        </Select>
                        <el-input v-show="localInvoice.recurringSettings.schedulePlan.stop.chosen == 'Count'" v-model="localInvoice.recurringSettings.schedulePlan.stop.count" placeholder="E.g) 3" size="mini" class="float-left mr-1 ml-1 mb-3" :style="{ maxWidth:'80px', marginTop:'-3px' }"></el-input>
                        <el-date-picker v-show="localInvoice.recurringSettings.schedulePlan.stop.chosen == 'Date'" v-model="localInvoice.recurringSettings.schedulePlan.stop.date" type="date" :clearable="false" placeholder="e.g) January 1, 2018" size="mini" class="float-left mr-1 ml-1 mb-3" :style="{ maxWidth:'135px', marginTop:'-3px' }"
                            format="MMM dd yyyy" value-format="yyyy-MM-dd">
                        </el-date-picker>

                    </Col>

                </Row>

                <Row>

                    <Col span="24 mt-2">
                        <Row>
                            <Col span="24">
                                <!-- Next Button -->
                                <Button v-if="isEditingSchedulePlan" class="float-right" type="primary" size="large" @click="saveSchedulePlan()">
                                    <span>{{ localInvoice.has_set_recurring_schedule_plan ? 'Save Changes': 'Next Step' }}</span>
                                </Button>
                                <Button v-else class="float-right" type="default" size="large" @click="activateEditMode()">
                                    <span>Edit Schedule</span>
                                </Button>
                            </Col>
                        </Row>

                    </Col>

                </Row>

            </template>
            
        </stagingCard>

    </div>

</template>
<script type="text/javascript">

    import stagingCard from './main.vue';
    import fadeLoader from './../loaders/fadeLoader.vue';
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    export default {
        components: { fadeLoader, stagingCard, focusRipple },
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                isSavingRecurringSchedulePlan: false,
                localInvoice: this.invoice,
                isEditingSchedulePlan: (this.invoice.recurringSettings.editing.schedulePlan == 'true')
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

                    //  Update the editing schedule shortcut
                    this.isEditingSchedulePlan = (val.recurringSettings.editing.schedulePlan == 'true')

                },
                deep: true
            }
        },
        methods: {
            activateEditMode(){
                //  Get all the plans and their edit state
                //  JSON.parse converts the 'true/false' string to Boolean
                
                var editingSchedulePlan = ( this.localInvoice.recurringSettings.editing.schedulePlan == 'true' );
                var editingDeliveryPlan = ( this.localInvoice.recurringSettings.editing.deliveryPlan == 'true' );
                var editingPaymentPlan = ( this.localInvoice.recurringSettings.editing.paymentPlan == 'true' );

                //  If we are still editing the delivery/payment plan 
                if( editingDeliveryPlan || editingPaymentPlan ){
                    //  Tell the user to save first before editing
                    this.$Notice.warning({
                        title: 'Save '+(editingDeliveryPlan ? 'Delivery Plans': 'Payment Plans')+'!',
                        desc: 'Save your '+(editingDeliveryPlan ? 'Delivery Plans': 'Payment Plans')+' first before editing your Schedule Plans',
                    });
                }else{
                    this.localInvoice.recurringSettings.editing.schedulePlan = 'true';
                    this.isEditingSchedulePlan = true;
                }
            },
            saveSchedulePlan(){

                var self = this;

                //  Start loader
                self.isSavingRecurringSchedulePlan = true;

                console.log('Attempt to save recurring schedule plan...');

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/recurring/update-schedule-plan', invoiceData)
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSavingRecurringSchedulePlan = false;
                        
                        //  Alert creation success
                        self.$Message.success('Schedule plan saved sucessfully!');

                        self.$emit('approved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingRecurringSchedulePlan = false;

                        console.log('recurringSettingsStage.vue - Error saving recurring schedule...');
                        console.log(response);
                    });
            }
        }
    }
</script>
