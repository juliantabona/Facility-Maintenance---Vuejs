<style>

    /*  Some classes found inside the <field-template> element   */

    #field-creation-modal .content-box .field-label {
        font-size: 14px;
        margin: 0 10px 5px 0;
        display: inline-block;
    }

    #field-creation-modal .field-editor .settings-box {
        max-height: 280px !important;
        padding: 20px;
        border: 1px solid #0000002b;
        -webkit-box-shadow: inset 1px 2px 5px #0000005c;
        box-shadow: inset 1px 2px 5px #0000005c;
        overflow-y:auto;
    }

    #field-creation-modal .field-editor .settings-box .options-box{
        border: 1px dotted #409eff;
        box-shadow: 10px 10px #409eff30;
        padding: 20px;
        margin-bottom: 20px;
    }

    #field-creation-modal .el-dialog__header{
        padding-bottom:0px !important;
    }

    #field-creation-modal .el-dialog__body{
        padding-top:0px !important;
    }

    #field-creation-modal .field-box{
        border: 1px dotted #cecccc;
        padding: 10px;
    }

    #field-creation-modal .modal-field-option{
        cursor: pointer;
    }

</style>

<template>

    <Modal 
        id="field-creation-modal"
        v-model="modalVisible"
        :mask-closable="false">
        <p slot="header">
            <Icon type="ios-add-circle-outline" :size="20" />
            <span>Create Field</span>
        </p>

        <Row v-if="showMainOptions" :gutter="20">

            <Col :span="8" v-for="option in mainOptions" :key="option.name" class="mb-2">

                <Card @click.native="nextStep(option)" :padding="0" class="modal-field-option">
                    
                    <div style="padding: 14px;">
                        <Icon :type="option.icon" size="32" class="text-center" style="display: block;"/>
                        <p class="text-center" style="padding-top:15px;">{{ option.name }}</p>
                    </div>

                </Card>

            </Col>

        </Row>

        <Row v-if="showSubOptions" :gutter="20">

            <Col :span="8" v-for="option in subOptions" :key="option.name" class="mb-2">

                <Card @click.native="nextStep(option)" :padding="0" class="modal-field-option">
                    
                    <div style="padding: 14px;">
                        <p class="text-center">{{ option.name }}</p>
                    </div>

                </Card>

            </Col>

        </Row>

        <el-row v-if="showOptionSettings && selectedOption" :gutter="20">
            <el-col :span="24" class="mb-3">
                <div class="content-box">
                    <oq-Template-Field-Mockup :field="selectedOption.field" :show-settings="true"></oq-Template-Field-Mockup>
                </div>
            </el-col>
        </el-row>
        <div slot="footer">
            <Button type="default" size="large"  @click="close()">Cancel</Button>
            <Button v-if="showOptionSettings && selectedOption" type="primary" size="large"  @click="createField(selectedOption)">+ Create Field</Button>
        </div>
    </Modal>

</template>

<script>
  import draggable from 'vuedraggable'
  export default {
        props:{
            show: {
                default: false
            }
        },
        components: {
            draggable
        },
        data() {
            return {
                //  Main options
                showMainOptions: true,
                mainOptions: [],

                //  Sub options
                showSubOptions: false,
                subOptions: [],

                //  Option settings
                showOptionSettings: false,
                selectedOption: null,

                section: {
                    id: "",
                    name: "",
                    description:"",
                    showFields:true,
                    fields: []
                }
            };
        },
        computed:{
            modalVisible:{
                get(){
                    this.hideAllPanels();
                    this.mainOptions = this.getMainOptions();
                    this.showMainOptions = true;
                    return this.show;
                },
                set(v){ 
                    this.$emit("closed");
                }
            }
        },
        methods: {
            close(){
                if(this.showOptionSettings){
                    this.$Modal.confirm({
                        title: "Are you sure you want to close this?",
                        content: "Any progress made will be forgotten.",
                        okText: "Yes",
                        cancelText: "No",
                        closable: false,
                        onOk: this.closeModal,
                        onCancel: function(){}
                    });
                }else{
                    this.closeModal();
                }
            },
            closeModal(){
                this.modalVisible = false;
            },
            getMainOptions(){ 
                return [
                    {
                        name: "Text",
                        icon: "ios-code-working",
                        field: {  
                            width: 24,
                            type: "input-text",
                            id: "",
                            label: "Enter field label...",
                            value: "",
                            placeholder: "Enter field placeholder...",
                            suffixIcon: "",
                            prefixIcon: "",
                            prepend: "",
                            append: "",
                            size: "large",                                //  options: large, small, mini
                            maxlength: 200,
                            disabled: false,
                            clearable: false,
                            readonly: false,
                        },
                    },
                    {
                        name: "Number",
                        icon: "ios-keypad-outline",
                        field: {
                            width: 24,
                            type: "input-number",
                            id: "field_company_name_10000000003",
                            label: "Employee No.",
                            value: "",
                            placeholder: "Enter Company description",   
                            disabled: false,
                            min:10,
                            max:20,
                            step:0.2,
                            precision:2,
                            size: "mini",                                    //  options: large, small, mini    
                            controlsPosition: "right"                        //  options: "", "right"                   
                        }
                    },
                    {
                        name: "Paragraph",
                        icon: "ios-paper-outline",
                        field: {
                            width: 24,
                            type: "input-textarea",
                            id: "",
                            label: "Enter field label...",
                            value: "",
                            placeholder: "Enter field placeholder...",
                            disabled: false,                               
                            maxlength: 500,
                            autosize: null,                                 //  autosize:{ minRows: 2, maxRows: 4 },
                            rows: 2,                                        //  comment: "autosize: null" for this to work
                            resize: "none",                                 //  options: "", "none"
                            readonly: false,                   
                        }
                    },
                    {
                        name: "Dropdown",
                        icon: "ios-list",
                        field: {
                            width: 24,
                            type: "select",
                            id: "",
                            label: "Enter field label...",
                            value: "",
                            placeholder: "Select",
                            disabled: false,
                            clearable: false,
                            multiple : false,
                            collapseTags: false,
                            filterable: false,
                            allowCreate: false,
                            options: [
                                {
                                    value: "Option 1",
                                    disabled: false
                                }
                            ]                
                        }
                    },
                    {
                        name: "Slider",
                        icon: "ios-options-outline",
                        field: {
                            width: 24,
                            type: "slider",
                            id: "",
                            label: "Enter field label...",
                            value: 0,
                            min: 0,
                            max: 100,
                            disabled: false,
                            showTooltip: true,
                            step: 10,
                            showStops: true,
                            showInput: true,
                            range: false,
                            vertical: false,
                            verticalHeight: 50
                        }
                    },
                    {
                        name: "Checkbox",
                        icon: "ios-checkbox-outline",
                        field: { 
                            width: 24,
                            type: "checkbox",
                            id: "",
                            label: "Enter field label...",
                            value: [],
                            min: 0,
                            max: 10,
                            options: [
                                {
                                    value: "Option 1",
                                    disabled: false
                                }
                            ]   
                        }
                    },
                    {
                        name: "Radio",
                        icon: "ios-more-outline",
                        field: { 
                            width: 24,
                            type: "radio",
                            id: "",
                            label: "Enter field label...",
                            value: "",
                            options: [
                                {
                                    value: "Option 1",
                                    disabled: false
                                }
                            ]   
                        }
                    },
                    {
                        name: "Date/Time",
                        icon: "ios-alarm-outline",
                        optionsHeading: "Date And Time Options",
                        options: [
                            {
                                name: "Time selector",
                                field: { 
                                    width: 24,
                                    type: "select-time-picker",
                                    id: "",
                                    label: "Enter field label...",
                                    value: "",
                                    placeholder: "Enter field placeholder...",
                                    disabled: false,
                                    pickerOptions: {
                                        start: "08:30",
                                        step: "00:15",
                                        end: "18:30"
                                    } 
                                }
                            },
                            {
                                name: "Time picker",
                                field: { 
                                    width: 24,
                                    type: "arbitrary-time-picker",
                                    id: "",
                                    label: "Enter field label...",
                                    value: "",
                                    placeholder: "Enter field placeholder...",
                                    disabled: false,
                                    pickerOptions: {
                                        selectableRange: "00:00:00 - 23:59:59"
                                    },
                                    arrowControl: false
                                }
                            },
                            {
                                name: "Time range picker",
                                field: { 
                                    width: 24,
                                    type: "arbitrary-time-range",
                                    id: "",
                                    label: "Enter field label...",
                                    value: "",
                                    disabled: false,
                                    arrowControl: false,
                                    //  Time range properties
                                    rangeSeparator: 'To',
                                    startPlaceholder: 'Start Time',
                                    endPlaceholder: 'End Time'
                                }
                            },
                            {
                                name: "Date picker",
                                field: { 
                                    width: 24,
                                    type: "date-picker",
                                    id: "",
                                    label: "Enter field label...",
                                    value: "",
                                    placeholder: "Enter field placeholder...",
                                    dateType: "date",                                   //  options: date, week, month, year
                                    dateFormat: "dd/MM/yyyy",                           /*  E.g) dateFormat:"Week WW" if the dateType:"week" refer to 
                                                                                            "http://element.eleme.io/#/en-US/component/date-picker" for Date Formats
                                                                                        */
                                    disabled: false
                                }
                            },
                            {
                                name: "Date range picker",
                                field: { 
                                    width: 24,
                                    type: "date-range",
                                    id: "",
                                    label: "Enter field label...",
                                    value: "",
                                    rangeSeparator: "To",
                                    startPlaceholder: "Start Date...",
                                    endPlaceholder: "End Date...",
                                    disabled: false
                                }

                            },
                            {
                                name: "Datetime picker",
                                field: { 
                                    width: 24,
                                    type: "datetime-picker",
                                    id: "",
                                    label: "Enter field label...",
                                    value: 50,
                                    placeholder: "Enter field placeholder...",
                                }
                            },
                            {
                                name: "Datetime range picker",
                                field: { 
                                    width: 24,
                                    type: "datetime-range",
                                    id: "",
                                    label: "Enter field label...",
                                    value: "",
                                    rangeSeparator: "To",
                                    startPlaceholder: "Start Datetime...",
                                    endPlaceholder: "End Datetime..."
                                }
                            }
                        ]
                    },
                    {
                        name: "Upload",
                        icon: "ios-cloud-upload-outline",
                        field: {
                            width: 24,
                            type: "file-upload",
                            id: "",
                            label: "Upload documents...",
                            value: "",
                            drag: true,
                            multiple: false,
                            limit: 3,
                            instruction: "Drop file here or <em>click to upload</em>",
                            tip: "jpg/png files with a size less than 500kb",
                            
                        }
                    },
                    {
                        name: "Rating",
                        icon: "ios-star-outline",
                        field: {
                            width: 24,
                            type: "rating",
                            id: "field_company_name_10000000014",
                            label: "Basic Rating",
                            value: 3,
                            colors: ["#ff3737", "#16ff07", "#FF9900"],
                            showText: true,
                            texts: ["Very Bad", "Bad", "Good", "Very Good", "Excellent"],
                            disabled: false
                        }
                    },
                    {
                        name: "Switch",
                        icon: "ios-switch-outline",
                        field: {
                            width: 24,
                            type: "switch",
                            id: "",
                            label: "Enter field label...",
                            value: 0,
                            activeColor: "#13ce66",
                            inactiveColor: "#dcdfe6",
                            activeText: "Pay by month",
                            inactiveText: "Pay by year"
                        }
                    },
                    {
                        name: "Alert",
                        icon: "ios-alert-outline",
                        type: "alert",
                        field: {
                            width: 24,
                            type: "alert",
                            id: "",
                            title: "Sample Title",
                            description: "Sample description",
                            alertType: "success",
                            showIcon: true,
                            closable: false
                        }
                    }
                ]
            },
            nextStep(option){

                //  Hide all panels
                this.hideAllPanels();

                //   const subOptionsExist = option.options.length; // Cannot read property "length" of undefined
                const subOptionsExist = ((option || {}).options || {}).length;

                //  If we have suboptions, lets show them to the user
                if(subOptionsExist){
                    //  Show sub options
                    this.showSubOptions = true;
                    this.subOptions = option.options;
                
                //  Otherwise display the current field settings
                }else{
                    //  Show the option settings
                    this.showOptionSettings = true;
                    this.selectedOption = option;
                } 
            },
            hideAllPanels(){
                this.showMainOptions = false;
                this.showSubOptions = false;
                this.showOptionSettings = false;
            },
            createField(selectedOption){
                var canCreate = true;
                var Options = ((this.selectedOption.field || {}).options || {});
                var errors = [];

                //  Make sure that all options have values otherwise alert the user
                for(var x = 0; x < Options.length ; x++){
                    if(!Options[x].value){
                        canCreate = false;
                        errors.push("One of the options does not have a value.");
                    }
                }

                //  If we have any errors, alert the user
                if(errors.length){
                    alert(errors[0]);
                }else{
                    selectedOption.field.id = "field_" + Date.now();
                    this.$emit("created", selectedOption.field);
                    this.modalVisible = false;
                }
            }
        } 
  };
</script>