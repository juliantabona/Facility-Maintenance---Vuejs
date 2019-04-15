<template>

    <div>
        
        <!-- Fade loader - Shows when saving the recurring schedule plan  -->
        <fadeLoader :loading="isSavingRecurringSchedulePlan" msg="Saving schedule plan, please wait..."></fadeLoader>

        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="1" :showCheckMark="showCheckMark" 
            :showHeader="showHeader" 
            :disabled="false" :showVerticalLine="true" :leftWidth="24"
            :isSaving="isSavingRecurringSchedulePlan">

            <!-- Header  -->
            <template slot="header">

                <Icon type="ios-information-circle-outline" :size="28" style="margin-top: -4px;"/>
                <span>This recurring schedule is not approved. You can only approve once all 3 steps are completed. <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
                <br>

            </template>

            <template slot="leftContent">

                <h4 :class="'text-secondary' + ( isEditingStage ? ' mt-3 mb-4': '')">Schedule Plan:</h4>
                <div v-if="!isEditingStage" class="d-inline-block mt-2" :style="{ lineHeight: '1.6em' }">
                    <p><b>Repeat {{ localRecurringSettings.schedulePlan.chosen }}:</b> {{ scheduleSummaryInWords }}</p>
                    <p><b>Dates:</b> {{ scheduleStartAndStopDatesInWords }}</p>
                    <p><b>Time zone:</b> Africa/Gaborone</p>
                </div>

            </template>

            <!-- Extra Content  -->
            <template slot="extraContent">

                <!-- Schedule settings -->
                <Row v-if="isEditingStage">
                    
                    <!-- Schedule Frequency e.g) Daily, Weekly, Monthly, Yearly or Custom  -->
                    <Col span="24">

                        <!-- Reminder method Selector -->
                        <span class="float-left d-block mr-1 mb-3">Repeat this {{ resourceName }}</span>

                        <!-- Timeline Options -->
                        <Select v-model="localRecurringSettings.schedulePlan.chosen" 
                                :style="{ width:'100px', marginTop:'-5px',position:'relative',zIndex:'1' }" class="float-left d-block mb-3" placeholder="Select frequency"
                                @on-change="updateSchedulePlans()">
                            <Option value="Daily">Daily</Option>
                            <Option value="Weekly">Weekly</Option>
                            <Option value="Monthly">Monthly</Option>
                            <Option value="Yearly">Yearly</Option>
                            <Option value="Custom">Custom</Option>
                        </Select>

                        <!-- If Weekly -->
                        <span v-show="localRecurringSettings.schedulePlan.chosen == 'Weekly'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1">every</span>
                            <Select v-model="localRecurringSettings.schedulePlan.weekly" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day of week"
                                    @on-change="updateSchedulePlans()">
                                <Option value="1">Monday</Option>
                                <Option value="2">Tuesday</Option>
                                <Option value="3">Wednesday</Option>
                                <Option value="4">Thursday</Option>
                                <Option value="5">Friday</Option>
                                <Option value="6">Saturday</Option>
                                <Option value="0">Sunday</Option>
                            </Select>
                        </span>
                        
                        <!-- If Monthly -->
                        <span v-show="localRecurringSettings.schedulePlan.chosen == 'Monthly'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1">on the</span>
                            <Select v-model="localRecurringSettings.schedulePlan.monthly" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day"
                                    @on-change="updateSchedulePlans()">
                                <Option value="1">1st</Option>
                                <Option value="2">2nd</Option>
                                <Option value="3">3rd</Option>
                                <Option value="4">4th</Option>
                                <Option value="5">5th</Option>
                                <Option value="6">6th</Option>
                                <Option value="7">7th</Option>
                                <Option value="8">8th</Option>
                                <Option value="9">9th</Option>
                                <Option value="10">10th</Option>
                                <Option value="11">11th</Option>
                                <Option value="12">12th</Option>
                                <Option value="13">13th</Option>
                                <Option value="14">14th</Option>
                                <Option value="15">15th</Option>
                                <Option value="16">16th</Option>
                                <Option value="17">17th</Option>
                                <Option value="18">18th</Option>
                                <Option value="19">19th</Option>
                                <Option value="20">20th</Option>
                                <Option value="21">21st</Option>
                                <Option value="22">22nd</Option>
                                <Option value="23">23rd</Option>
                                <Option value="24">24th</Option>
                                <Option value="25">25th</Option>
                                <Option value="26">26th</Option>
                                <Option value="27">27th</Option>
                                <Option value="28">28th</Option>
                                <Option value="29">29th</Option>
                                <Option value="30">30th</Option>
                                <Option value="31">31st</Option>
                            </Select>
                            <span class="float-left d-block ml-1 mr-1">day of every month</span>
                        </span>

                        <!-- If Yearly -->
                        <span v-show="localRecurringSettings.schedulePlan.chosen == 'Yearly'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1">every</span>
                            <Select v-model="localRecurringSettings.schedulePlan.yearly.month" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day of week"
                                    @on-change="updateSchedulePlans()">
                                    <Option value="0">January</Option>
                                    <Option value="1">February</Option>
                                    <Option value="2">March</Option>
                                    <Option value="3">April</Option>
                                    <Option value="4">May</Option>
                                    <Option value="5">June</Option>
                                    <Option value="6">July</Option>
                                    <Option value="7">August</Option>
                                    <Option value="8">September</Option>
                                    <Option value="9">October</Option>
                                    <Option value="10">November</Option>
                                    <Option value="11">December</Option>
                            </Select>
                            
                            <span class="float-left d-block ml-1 mr-1">on the</span>
                            <Select v-model="localRecurringSettings.schedulePlan.yearly.day" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day"
                                    @on-change="updateSchedulePlans()">
                                <Option value="1">1st</Option>
                                <Option value="2">2nd</Option>
                                <Option value="3">3rd</Option>
                                <Option value="4">4th</Option>
                                <Option value="5">5th</Option>
                                <Option value="6">6th</Option>
                                <Option value="7">7th</Option>
                                <Option value="8">8th</Option>
                                <Option value="9">9th</Option>
                                <Option value="10">10th</Option>
                                <Option value="11">11th</Option>
                                <Option value="12">12th</Option>
                                <Option value="13">13th</Option>
                                <Option value="14">14th</Option>
                                <Option value="15">15th</Option>
                                <Option value="16">16th</Option>
                                <Option value="17">17th</Option>
                                <Option value="18">18th</Option>
                                <Option value="19">19th</Option>
                                <Option value="20">20th</Option>
                                <Option value="21">21st</Option>
                                <Option value="22">22nd</Option>
                                <Option value="23">23rd</Option>
                                <Option value="24">24th</Option>
                                <Option value="25">25th</Option>
                                <Option value="26">26th</Option>
                                <Option value="27">27th</Option>
                                <Option value="28">28th</Option>
                                <Option value="29">29th</Option>
                                <Option value="30">30th</Option>
                                <Option value="31">31st</Option>
                            </Select>
                        </span>

                        <!-- If Custom -->
                        <span v-show="localRecurringSettings.schedulePlan.chosen == 'Custom'" class="mb-3">
                            <span class="float-left d-block ml-1 mr-1 mb-3">every</span>
                            <el-input v-model="localRecurringSettings.schedulePlan.custom.count" type="number" min="1" :maxlength="2"
                                      placeholder="E.g) 6" size="mini" class="float-left d-block mr-1 mb-3" :style="{ maxWidth:'60px', marginTop:'-3px' }"
                                      @input="updateSchedulePlans()">
                            </el-input>
                            <Select v-model="localRecurringSettings.schedulePlan.custom.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week"
                                @on-change="updateSchedulePlans()">
                                <Option value="Days">Day(s)</Option>
                                <Option value="Weeks">Week(s)</Option>
                                <Option value="Months">Month(s)</Option>
                                <Option value="Years">Year(s)</Option>
                            </Select>

                            <!-- If weeks -->
                            <span v-show="localRecurringSettings.schedulePlan.custom.chosen == 'Weeks'">
                                <span class="float-left d-block ml-1 mr-1 mb-3">on every</span>
                                <Select v-model="localRecurringSettings.schedulePlan.custom.weeks" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week"
                                    @on-change="updateSchedulePlans()">
                                    <Option value="1">Monday</Option>
                                    <Option value="2">Tuesday</Option>
                                    <Option value="3">Wednesday</Option>
                                    <Option value="4">Thursday</Option>
                                    <Option value="5">Friday</Option>
                                    <Option value="6">Saturday</Option>
                                    <Option value="0">Sunday</Option>
                                </Select>
                            </span>
                            
                            <!-- If months -->
                            <span v-show="localRecurringSettings.schedulePlan.custom.chosen == 'Months'">
                                <span class="float-left d-block ml-1 mr-1 mb-3">on the</span>
                                    <Select v-model="localRecurringSettings.schedulePlan.custom.months" :style="{ width:'60px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day"
                                            @on-change="updateSchedulePlans()">
                                        <Option value="1">1st</Option>
                                        <Option value="2">2nd</Option>
                                        <Option value="3">3rd</Option>
                                        <Option value="4">4th</Option>
                                        <Option value="5">5th</Option>
                                        <Option value="6">6th</Option>
                                        <Option value="7">7th</Option>
                                        <Option value="8">8th</Option>
                                        <Option value="9">9th</Option>
                                        <Option value="10">10th</Option>
                                        <Option value="11">11th</Option>
                                        <Option value="12">12th</Option>
                                        <Option value="13">13th</Option>
                                        <Option value="14">14th</Option>
                                        <Option value="15">15th</Option>
                                        <Option value="16">16th</Option>
                                        <Option value="17">17th</Option>
                                        <Option value="18">18th</Option>
                                        <Option value="19">19th</Option>
                                        <Option value="20">20th</Option>
                                        <Option value="21">21st</Option>
                                        <Option value="22">22nd</Option>
                                        <Option value="23">23rd</Option>
                                        <Option value="24">24th</Option>
                                        <Option value="25">25th</Option>
                                        <Option value="26">26th</Option>
                                        <Option value="27">27th</Option>
                                        <Option value="28">28th</Option>
                                        <Option value="29">29th</Option>
                                        <Option value="30">30th</Option>
                                        <Option value="31">31st</Option>
                                </Select>
                                <span class="float-left d-block ml-1 mr-1 mb-3">day of every month</span>
                            </span>

                            <!-- If years -->
                            <span v-show="localRecurringSettings.schedulePlan.custom.chosen == 'Years'">
                                <span class="float-left d-block ml-1 mr-1 mb-3">on every</span>
                                <Select v-model="localRecurringSettings.schedulePlan.custom.years.month" :style="{ width:'120px', marginTop:'-5px' }" class="float-left d-block mb-3" placeholder="Select day of week"
                                    @on-change="updateSchedulePlans()">
                                    <Option value="0">January</Option>
                                    <Option value="1">February</Option>
                                    <Option value="2">March</Option>
                                    <Option value="3">April</Option>
                                    <Option value="4">May</Option>
                                    <Option value="5">June</Option>
                                    <Option value="6">July</Option>
                                    <Option value="7">August</Option>
                                    <Option value="8">September</Option>
                                    <Option value="9">October</Option>
                                    <Option value="10">November</Option>
                                    <Option value="11">December</Option>
                                </Select>
                                <span class="float-left d-block ml-1 mr-1 mb-3">on the</span>
                                <Select v-model="localRecurringSettings.schedulePlan.custom.years.day" :style="{ width:'60px', marginTop:'-5px' }" class="float-left mb-3" placeholder="Select day"
                                        @on-change="updateSchedulePlans()">
                                    <Option value="1">1st</Option>
                                    <Option value="2">2nd</Option>
                                    <Option value="3">3rd</Option>
                                    <Option value="4">4th</Option>
                                    <Option value="5">5th</Option>
                                    <Option value="6">6th</Option>
                                    <Option value="7">7th</Option>
                                    <Option value="8">8th</Option>
                                    <Option value="9">9th</Option>
                                    <Option value="10">10th</Option>
                                    <Option value="11">11th</Option>
                                    <Option value="12">12th</Option>
                                    <Option value="13">13th</Option>
                                    <Option value="14">14th</Option>
                                    <Option value="15">15th</Option>
                                    <Option value="16">16th</Option>
                                    <Option value="17">17th</Option>
                                    <Option value="18">18th</Option>
                                    <Option value="19">19th</Option>
                                    <Option value="20">20th</Option>
                                    <Option value="21">21st</Option>
                                    <Option value="22">22nd</Option>
                                    <Option value="23">23rd</Option>
                                    <Option value="24">24th</Option>
                                    <Option value="25">25th</Option>
                                    <Option value="26">26th</Option>
                                    <Option value="27">27th</Option>
                                    <Option value="28">28th</Option>
                                    <Option value="29">29th</Option>
                                    <Option value="30">30th</Option>
                                    <Option value="31">31st</Option>
                                </Select>
                            </span>
                        </span>
                    </Col>

                    <Col span="24 mb-3">
                        
                        <!-- Set the time -->
                        <span class="mb-3">
                            <span class="float-left d-block ml-1 mr-1">Always send at</span>
                            <Select v-model="scheduleTime" :style="{ width:'90px', marginTop:'-5px' }" class="float-left d-block" placeholder="Select day"
                                    @on-change="updateSchedulePlans()">

                                <Option value="0600AM">06:00 AM</Option>
                                <Option value="0630AM">06:30 AM</Option>
                                <Option value="0700AM">07:00 AM</Option>
                                <Option value="0730AM">07:30 AM</Option>
                                <Option value="0800AM">08:00 AM</Option>
                                <Option value="0830AM">08:30 AM</Option>
                                <Option value="0900AM">09:00 AM</Option>

                                <Option value="0930AM">09:30 AM</Option>
                                <Option value="1000AM">10:00 AM</Option>
                                <Option value="1030AM">10:30 AM</Option>
                                <Option value="1100AM">11:00 AM</Option>
                                <Option value="1130AM">11:30 AM</Option>
                                <Option value="1200PM">12:00 PM</Option>

                                <Option value="1230PM">12:30 PM</Option>
                                <Option value="1300PM">13:00 PM</Option>
                                <Option value="1330PM">13:30 PM</Option>
                                <Option value="1400PM">14:00 PM</Option>
                                <Option value="1430PM">14:30 PM</Option>
                                <Option value="1500PM">15:00 PM</Option>

                                <Option value="1530PM">15:30 PM</Option>
                                <Option value="1600PM">16:00 PM</Option>
                                <Option value="1630PM">16:30 PM</Option>
                                <Option value="1700PM">17:00 PM</Option>
                                <Option value="1730PM">17:30 PM</Option>
                                <Option value="1800PM">18:00 PM</Option>

                            </Select>
                            
                        </span>
                    </Col>

                    <!-- Schedule Start and End period  -->
                    <Col span="24" class="border-top pt-3">
                        
                        <!-- Text for when to create first resource -->
                        <span class="float-left d-block mr-1 mb-3">Create first {{ resourceName }} on</span>

                        <!-- First Send - Start Date -->
                        <el-date-picker v-model="localRecurringSettings.schedulePlan.startDate" type="date" 
                            :clearable="false" placeholder="e.g) January 1, 2018" size="mini" 
                            class="float-left mb-2" :style="{ maxWidth:'135px', marginTop:'-3px' }"
                            format="MMM dd yyyy" value-format="yyyy-MM-dd"
                            :picker-options="pickerOptions"
                            @change="updateScheduleInWords()">
                        </el-date-picker>

                        <!-- Text for when to end -->
                        <span class="float-left d-block mr-1 ml-1 mb-3">and end</span>

                        <!-- Text for when to end -->
                        <Select v-model="localRecurringSettings.schedulePlan.stop.chosen" :style="{ width:'100px', marginTop:'-5px' }" class="float-left mb-3" placeholder="Select day of week"
                            @on-change="updateScheduleInWords()">
                            <Option value="Count">After</Option>
                            <Option value="Date">On</Option>
                            <Option value="Never">Never</Option>
                        </Select>
                        <el-input v-show="localRecurringSettings.schedulePlan.stop.chosen == 'Count'" type="number" min="1" :maxlength="2" v-model="localRecurringSettings.schedulePlan.stop.count" placeholder="E.g) 3" size="mini" class="float-left mr-1 ml-1 mb-3" :style="{ maxWidth:'80px', marginTop:'-3px' }"
                            @input="updateScheduleInWords()">
                        </el-input>
                        <!-- Text for when to end -->
                        <span v-if="localRecurringSettings.schedulePlan.stop.chosen == 'Count'" class="float-left d-block mr-1 ml-1 mb-3">{{ resourceName }}(s) have been sent</span>
                        <el-date-picker v-show="localRecurringSettings.schedulePlan.stop.chosen == 'Date'" v-model="localRecurringSettings.schedulePlan.stop.date" type="date" :clearable="false" placeholder="e.g) January 1, 2018" size="mini" class="float-left mr-1 ml-1 mb-3" :style="{ maxWidth:'135px', marginTop:'-3px' }"
                            format="MMM dd yyyy" value-format="yyyy-MM-dd"
                            @change="updateScheduleInWords()">
                        </el-date-picker>

                    </Col>

                </Row>

                <Row>

                    <Col span="24 mt-2">
                        <Row>
                            <Col span="24">

                                <!-- Focus Ripple  -->
                                <focusRipple color="blue" :ripple="rippleEffect" class="float-right">
                                    
                                    <!-- Next Button -->
                                    <Button v-if="isEditingStage" class="float-right" type="primary" size="large" @click="saveSchedulePlan()">
                                        <span>{{ showNextStepBtn ? 'Save Changes': 'Next Step' }}</span>
                                    </Button>

                                    <Button v-else class="float-right" type="default" size="large" @click="activateEditMode()">
                                        <span>Edit Schedule</span>
                                    </Button>

                                </focusRipple>

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

    import moment from 'moment';

    export default {
        components: { fadeLoader, stagingCard, focusRipple },
        props: {
            recurringSettings: {
                type: Object,
                default: null
            },
            resourceName:{
                type: String,
                default: '___'
            },
            resourceNamePlural:{
                type: String,
                default: '___'
            },
            showHeader:{
                type: Boolean,
                default: false    
            },
            showCheckMark:{
                type: Boolean,
                default: false    
            },
            showNextStepBtn:{
                type: Boolean,
                default: false    
            },
            isEditing: {
                type: Boolean,
                default: false 
            },
            rippleEffect:{
                type: Boolean,
                default: false  
            },
            url:{
                type: String,
                default: null
            }
        },
        data(){
            var vm = this;
            return {
                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main recurring settings. This is so that whatever changes 
                    we make to the localRecurringSettings, they must not affect the parent "Recurring Settings". 
                    We will only update the parent details when we save the changes to the database.
                */
                localRecurringSettings: _.cloneDeep( (this.recurringSettings || {}) ),
                isEditingStage: ((_.cloneDeep( (this.recurringSettings || {}) ).editing || {}).schedulePlan),
                isSavingRecurringSchedulePlan: false,
                scheduleSummaryInWords:'',
                scheduleStartAndStopDatesInWords: '',
                scheduleTime: '0800AM',
                pickerOptions: {
                    disabledDate(time) {
                        if( vm.localRecurringSettings ){

                            var schedulePlan = vm.localRecurringSettings.schedulePlan;
                            var chosenSchedule = schedulePlan.chosen;             //  Daily, Weekly, Monthly, Yearly, Custom

                            var weekly = schedulePlan.weekly;                     //  0, 1, 2, 3, 4, 5, 6
                            var monthly = schedulePlan.monthly;                   //  0, 1, 2, 3,... 31
                            var yearlyMonth = schedulePlan.yearly.month;          //  0, 1, 2,... 11
                            var yearlyDay = schedulePlan.yearly.day;              //  0, 1, 2, 3,... 31

                            //  If chose custom from the $chosenSchedule
                            var customCount = schedulePlan.custom.count;          //  2
                            var chosenCustom = schedulePlan.custom.chosen;        //  Days, Weeks, Months, Years
                            var weeks = schedulePlan.custom.weeks;                //  0, 1, 2, 3, 4, 5, 6
                            var months = schedulePlan.custom.months;              //  0, 1, 2, 3,... 31
                            var yearsMonth = schedulePlan.custom.years.month;     //  0, 1, 2,... 11
                            var yearsDay = schedulePlan.custom.years.day;         //  0, 1, 2, 3,... 31
                        
                            if( moment(time) < moment(vm.localRecurringSettings.schedulePlan.startDate) 
                                && (chosenSchedule != 'Custom') ){
                                //  If the time is less than the scheduled start date then it must be disabled
                                return true;
                            }else{

                                //  Format the next schedule according to user specifications
                                if( moment(time) < moment() ){
                                    return true;
                                }else if(chosenSchedule == 'Weekly'){
                                    return parseInt(weekly) != moment(time).weekday();

                                }else if(chosenSchedule == 'Monthly'){
                                    return parseInt(monthly) != moment(time).date();
                                    
                                }else if(chosenSchedule == 'Yearly'){
                                    var todaysYear = moment().get('year');
                                    var scheduledYear = moment(time).get('year'); 

                                    return ( parseInt(yearlyDay) != moment(time).date() || 
                                            parseInt(yearlyMonth) != moment(time).get('month') ) || 
                                            (scheduledYear < todaysYear)
                                }else if(chosenSchedule == 'Custom'){
                                    if( moment(time) < moment() ){
                                        return true;
                                    }else{
                                        if(chosenCustom == 'Weeks'){
                                            return parseInt(weeks) != moment(time).weekday();

                                        }else if(chosenCustom == 'Months'){
                                            return parseInt(months) != moment(time).date();
                                            
                                        }else if(chosenCustom == 'Years'){
                                            var todaysYear = moment().get('year');
                                            var scheduledYear = moment(time).get('year'); 

                                            return ( parseInt(yearsDay) != moment(time).date() || 
                                                        parseInt(yearsMonth) != moment(time).get('month') ) || 
                                                        (scheduledYear < todaysYear)
                                        }
                                    }
                                }

                            }

                        }
                    },
                }
            }
        },
        watch: {

            //  Watch for changes on the recurringSettings
            recurringSettings: {
                handler: function (val, oldVal) {
                    
                    //  Update the local recurringSettings value
                    this.localRecurringSettings = _.cloneDeep(val);

                    //  Update the editing schedule shortcut
                    this.isEditingStage = ((_.cloneDeep( (val || {}) ).editing || {}).schedulePlan);

                    this.updateSchedulePlans();
                    this.updateScheduleInWords();

                },
                deep: true
            }
        },
        methods: {
            updateSchedulePlans(){
                var schedulePlan = this.localRecurringSettings.schedulePlan;
                
                /*********************************************************************
                 *   SET THE NEXT SCHEDULE TIME ACCORDING TO RECURRING SCHEDULE      *
                 *********************************************************************/
                var chosenSchedule = schedulePlan.chosen;             //  Daily, Weekly, Monthly, Yearly, Custom
                var weekly = schedulePlan.weekly;                     //  0, 1, 2, 3, 4, 5, 6
                var monthly = schedulePlan.monthly;                   //  0, 1, 2, 3,... 31
                var yearlyMonth = schedulePlan.yearly.month;          //  01, 02, 03,... 12
                var yearlyDay = schedulePlan.yearly.day;              //  0, 1, 2, 3, 4, 5, 6

                //  If chose custom from the $chosenSchedule
                var customCount = schedulePlan.custom.count;          //  2
                var chosenCustom = schedulePlan.custom.chosen;        //  Days, Weeks, Months, Years
                var weeks = schedulePlan.custom.weeks;                //  0, 1, 2, 3, 4, 5, 6
                var months = schedulePlan.custom.months;              //  0, 1, 2, 3,... 31
                var yearsMonth = schedulePlan.custom.years.month;     //  01, 02, 03,... 12
                var yearsDay = schedulePlan.custom.years.day;         //  0, 1, 2, 3,... 31

                var chosenStopMethod = schedulePlan.stop.chosen       //  Count, Date, Never
                
                //  Get the current send date
                var currentDate = moment().format('yyyy-MM-dd');
                
                //  Format the next schedule according to user specifications
                if (chosenSchedule == 'Daily') {
                    var today = moment().isoWeekday();  // Number
                    var tommorrow = today + 1;          // Number
                    var newStartDate = moment().isoWeekday(tommorrow);
                    
                    //  Assumed to end after 7 days
                    var newEndDate = moment(newStartDate).add(7, 'days');
                    
                    //  Explain in words
                    this.scheduleSummaryInWords = 'Everyday';

                }else if(chosenSchedule == 'Weekly'){
                    //  Get the day of the week relative to this current week.
                    var today = moment();
                    var newStartDate = moment().day( parseInt(weekly) )
                                       // Set the time
                                      .set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');
                    
                    //  If the date is in the past of the current day
                    if( today > moment(newStartDate) ){
                        //  Add 7 days to move it to the next week
                        newStartDate = newStartDate.add(7, 'days');
                    }

                    //  Assumed to end after 4 weeks
                    var newEndDate = moment(newStartDate).add(4, 'weeks');

                    //  Explain in words 
                    this.scheduleSummaryInWords = 'Every Week on ' + moment(newStartDate).format('dddd');

                }else if(chosenSchedule == 'Monthly'){
                    var today = moment();
                    var newStartDate = moment().set('date', parseInt(monthly) )
                                       // Set the time
                                      .set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');
                    

                    if(today >= newStartDate){
                        var newStartDate = newStartDate.add(1, 'months');
                    }
                
                    //  Assumed to end after 12 months
                    var newEndDate = moment(newStartDate).add(12, 'months');

                    //  Explain in words
                    this.scheduleSummaryInWords = 'Every Month on the ' + moment(newStartDate).format('Do');

                }else if(chosenSchedule == 'Yearly'){
                    var today = moment();
                    var todaysYear = today.get('year');
                    var newStartDate = moment(todaysYear+'-'+(parseInt(yearlyMonth)+1)+'-'+parseInt(yearlyDay),'YYYY-MM-DD')
                                       // Set the time
                                      .set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');
                      

                    if(today >= newStartDate){
                        var newStartDate = newStartDate.add(1, 'years');
                    }

                    //  Assumed to end after 3 years
                    var newEndDate = moment(newStartDate).add(3, 'years');

                    //  Explain in words
                    this.scheduleSummaryInWords = 'Every Year on the ' + moment(newStartDate).format('Do') +' of ' + moment(newStartDate).format('MMMM');

                }else if(chosenSchedule == 'Custom'){

                    if(chosenCustom == 'Days'){  
                        
                        var newStartDate = moment().add(customCount, 'days')
                                           // Set the time
                                           .set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');

                        //  Assumed to end after 3 cycles
                        var newEndDate = moment(newStartDate).add(customCount*3, 'days');

                        //  Explain in words
                        this.scheduleSummaryInWords = 'Every ' + customCount + (customCount == 1 ? ' Day': ' Days');

                    }else if(chosenCustom == 'Weeks'){  
                        var newStartDate = moment().day( parseInt(weeks) ).add(customCount, 'weeks')
                                           // Set the time
                                           .set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');

                        //  Assumed to end after 3 cycles
                        var newEndDate = moment(newStartDate).add(customCount*3, 'weeks');

                        //  Explain in words
                        this.scheduleSummaryInWords = 'Every ' + customCount + (customCount == 1 ? ' Week': ' Weeks') + ' on ' + moment(newStartDate).format('dddd');

                    }else if(chosenCustom == 'Months'){
                        var today = moment();
                        var setDate = moment().set('date', parseInt(months) ).add(customCount, 'months')
                                      // Set the time
                                      .set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');

                        //  Assumed to end after 3 cycles
                        var newEndDate = moment(newStartDate).add(customCount*3, 'months');

                        //  Explain in words
                        this.scheduleSummaryInWords = 'Every ' + customCount + (customCount == 1 ? ' Month': ' Months') + ' on the ' + moment(newStartDate).format('Do');
                            
                    }else if(chosenCustom == 'Years'){
                        var today = moment();
                        var todaysYear = today.get('year');
                        var setDate = moment(todaysYear+'-'+(parseInt(yearsMonth)+1)+'-'+parseInt(yearsDay),'YYYY-MM-DD')
                                            .add(customCount, 'years');  

                        //  Assumed to end after 3 cycles
                        var newEndDate = moment(newStartDate).add(customCount*3, 'years');

                        //  Explain in words
                        this.scheduleSummaryInWords = 'Every ' + customCount + (customCount == 1 ? ' Year': ' Years') + ' on the ' + moment(newStartDate).format('Do') +' of ' + moment(newStartDate).format('MMMM');

                    }

                }

                if(newStartDate){

                    //  Set the time
                    newStartDate = newStartDate.set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');
                    newEndDate = newEndDate.set('hour', this.scheduleTime.substring(0, 2)).set('minute', this.scheduleTime.substring(2, 4)).set('second', '00');

                    //  Set the format  
                    var updatedStartDate = newStartDate.format('YYYY-MM-DD HH:mm:ss');
                    var updatedEndDate = newEndDate.format('YYYY-MM-DD HH:mm:ss');

                    this.$set(this.localRecurringSettings.schedulePlan, 'startDate', updatedStartDate);
                    this.$set(this.localRecurringSettings.schedulePlan.stop, 'date', updatedEndDate);

                    this.updateScheduleInWords();
                }
            },
            updateScheduleInWords(){

                    //  Explain in words
                    var currStartDate = this.localRecurringSettings.schedulePlan.startDate;
                    var currStopDate = this.localRecurringSettings.schedulePlan.stop.date;
                    var currStopCount = this.localRecurringSettings.schedulePlan.stop.count;          //  2
                    var chosenStopMethod = this.localRecurringSettings.schedulePlan.stop.chosen       //  Count, Date, Never
                    var time = this.scheduleTime.substring(0, 2) +':'+ this.scheduleTime.substring(2, 4) + this.scheduleTime.substring(4, 6);

                    var startDateInWods = 'Create first '+this.resourceName+' on ' + moment(currStartDate).format('MMM Do YYYY') + ' at ' + time;
                    var stopDate = ' and send the last on '+moment(currStopDate).format('MMM Do YYYY') + ' at ' + time;
                    var stopCount = ' and stop after ' + (currStopCount) + (currStopCount == 1 ? ' '+ this.resourceName +' has': ' '+this.resourceNamePlural+' have')+' been sent';
                    var stopNever = ' and never stop sending';

                    this.scheduleStartAndStopDatesInWords = startDateInWods + (chosenStopMethod == 'Date' ? stopDate  : ( chosenStopMethod == 'Count' ? stopCount : stopNever) );  
            },
            activateEditMode(){
                //  Get all the plans and their edit state
                var editingSchedulePlan = ( this.localRecurringSettings.editing.schedulePlan );
                var editingDeliveryPlan = ( this.localRecurringSettings.editing.deliveryPlan );
                var editingPaymentPlan = ( this.localRecurringSettings.editing.paymentPlan );

                //  If we are still editing the delivery/payment plan 
                if( editingDeliveryPlan || editingPaymentPlan ){
                    //  Tell the user to save first before editing
                    this.$Notice.warning({
                        title: 'Save '+(editingDeliveryPlan ? 'Delivery Plans': 'Payment Plans')+'!',
                        desc: 'Save your '+(editingDeliveryPlan ? 'Delivery Plans': 'Payment Plans')+' first before editing your Schedule Plans',
                    });
                }else{
                    this.localRecurringSettings.editing.schedulePlan = true;
                    this.isEditingStage = true;
                }
            },
            saveSchedulePlan(){

                var self = this;

                //  Start loader
                self.isSavingRecurringSchedulePlan = true;

                console.log('Attempt to save recurring schedule plan...');

                //  Form data to send
                let RecurringSettingsData = { settings: self.localRecurringSettings };

                if( this.url ){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', this.url, RecurringSettingsData)
                        .then(({ data }) => {
                            
                            console.log(data);

                            //  Stop loader
                            self.isSavingRecurringSchedulePlan = false;
                            
                            //  Alert creation success
                            self.$Message.success('Schedule plan saved sucessfully!');

                            self.$emit('saved', data);

                        })         
                        .catch(response => { 
                            //  Stop loader
                            self.isSavingRecurringSchedulePlan = false;

                            console.log('recurringSettingsStage.vue - Error saving recurring schedule...');
                            console.log(response);
                        });

                }

            }
        },
        created(){
            this.updateSchedulePlans();
        }
    }
</script>
