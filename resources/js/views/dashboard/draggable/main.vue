<style>


    .form-section .card-section-desc {
        font-size: 12px;
        color: #808695;
        font-weight: 100;
    }

    .form-section .section-field{
        list-style: none;
        cursor: pointer;
        border: 1px dotted transparent;
        transition:all 1s ease;
    }

    .form-section .section-field:hover{
        border: 1px dotted #adadad;
        background: #409eff12;
    }

    .form-section .section-field .field-label{
        font-size: 14px;
        margin: 0 10px 5px 0;
        display: inline-block;
    }

    .form-section .section-field input,
    .form-section .section-field span{
            font-size: 12px;
    }

    .sortable-ghost{
        background:#4bff0059 !important;
        transition:all 1s ease;
    }

	.sortable-chosen{
        /*
        background:red !important;
        opacity: 1 !important;
        */
    }

    .sortable-drag{
        opacity: 1 !important;
    }

    /*  Section Toolbox */

    .form-section .section-toolbox{
        margin: 10px 15px 0 0;
    }

    .form-section .section-toolbox .section-icon.glowable{
        color: #ffffff;
        background: gold;
    }

    .form-section .section-toolbox .hidable{
        opacity:0;
    }

    .form-section:hover .section-toolbox .hidable{
        opacity:1;
    }

    .form-section .section-toolbox .section-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .form-section .section-toolbox .section-icon:hover{
        color: #ffffff;
        background: gold;
    }

    /*  Section Field */

    .form-section .section-field{
        position: relative;
    }

    /*  Field Toolbox */

    .form-section .section-field .field-toolbox{
        opacity:0;
        position: absolute;
        top: -20px;
        right: 0px;
        background: #f3f3f3;
        border-radius: 20px 0 0 20px;
        z-index: 1;
    }

    .form-section .section-field:hover .field-toolbox{
        opacity:1;
    }

    .form-section .section-field .el-badge__content{
        top:0;
    }

    .form-section .section-field .field-toolbox .field-icon{
        padding: 2px;
        border-radius: 100%;
        color: black;
        cursor: pointer;
    }

    .form-section .section-field .field-toolbox .field-icon:hover{
        color: #ffffff;
        background: gold;
    }

    /*  Upload Field */

    .el-upload{
        width:100%;
    }

    .el-upload .el-upload-dragger{
        width:100%;
        height: 100px;
    }

    .el-upload .el-upload-dragger .el-icon-upload{
        margin: 10px 0 10px;
    }

    /*  If we hide the fields, remove padding from the parent el-card body */

    .el-card.hidden-content .el-card__body{
        padding:0 !important;
    }

    /*  Draggable breadcrumb item */

    .el-breadcrumb__item {
        padding: 5px;
        border-radius: 5px;
    }

    .el-breadcrumb__item,
    .el-breadcrumb__item .el-breadcrumb__inner,
    .el-breadcrumb__separator[class*=icon]{
        cursor: pointer !important;cursor: pointer;
    }

    .el-breadcrumb__separator[class*=icon] {
        margin: 0 -5px 0 6px;
    }

    .el-breadcrumb__item .el-badge__content {
        height: 25px;
        line-height: 21px;
        padding: 2px 10px;
        top: 0;
    }
                
    .el-loading-spinner i {
        color: #ffffff;
        font-size: 18px;
    }

    .el-loading-spinner .el-loading-text {
        color: #ffffff;
        margin: -5px 0 0 5px;
        font-size: 14px;
        display: inline;
    }

</style>

<template>

    <Row :gutter="20" :style="{ margin: '0' }">

        <Col :span="16">
            <oq-Template-Content :sections="sections"></oq-Template-Content>
        </Col>

        <Col :span="8">
            <oq-Template-Sidebar :template="template" :sections="sections"></oq-Template-Sidebar>
        </Col>

        <show-draggable-section
            v-if="showSectionModal"
            :show-modal="showSectionModal" 
            :section="showSection"
            v-on:closed="showSectionModal = false"
            v-on:updated="alert('updated!')">
        </show-draggable-section>

    </Row>

</template>

<script>
    import draggable from 'vuedraggable';
    import Editor from '@tinymce/tinymce-vue';

    export default {
        components: {
            draggable,
            Editor
        },
        data(){
            return {
                showSectionModal: false,
                showFieldDrawer: false,
                showField:null,

                editor: '<div class="mceNonEditable">You cannot edit this part of the content.</div>However you can edit here',

                template: {
                    name:"Sample template",
                    description:"Sample template description",
                    category: {
                        value: [],
                        options: [
                            {
                                value: 'Category 1',
                                disabled: false
                            },
                            {
                                value: 'Category 2',
                                disabled: false
                            }
                        ]
                    }
                },
                sections:[ { "id": "section_1544055589419", "name": "Customer Details", "description": "Please complete the application form below. Make sure to answer all questions and attach any documents/files where applicable", "showFields": true, "updated": false, "fields": [ { "width": 12, "type": "input-text", "id": "field_1544055655339", "label": "First Name:", "value": "", "placeholder": "Enter your first name...", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "mini", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 12, "type": "input-text", "id": "field_1544055676364", "label": "Last Name", "value": "", "placeholder": "Enter your last name...", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "mini", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 24, "type": "date-picker", "id": "field_1544056046478", "label": "Date Of Birth", "value": "", "placeholder": "Select Date", "dateType": "date", "dateFormat": "dd/MM/yyyy", "disabled": false }, { "width": 12, "type": "radio", "id": "field_1544055715107", "label": "Gender", "value": "", "options": [ { "value": "Male", "disabled": false }, { "value": "Female", "disabled": false }, { "value": "N/A", "disabled": false } ] }, { "width": 8, "type": "radio", "id": "field_1544055764417", "label": "Marital Status", "value": "Option 3", "options": [ { "value": "Married", "disabled": false }, { "value": "Single", "disabled": false } ] }, { "width": 24, "type": "input-textarea", "id": "field_1544057069475", "label": "Biography", "value": "", "placeholder": "Write something short about yourself...", "disabled": false, "maxlength": 500, "autosize": null, "rows": 2, "resize": true, "readonly": false }, { "width": 24, "type": "input-text", "id": "field_1544056188195", "label": "Country:", "value": "", "placeholder": "Enter your country of birth...", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "large", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 12, "type": "input-text", "id": "field_1544055843961", "label": "Address 1", "value": "", "placeholder": "Enter address 1", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "mini", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 12, "type": "input-text", "id": "field_1544055921865", "label": "Address 2", "value": "", "placeholder": "Enter address 2", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "mini", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 24, "type": "file-upload", "id": "field_1544056112837", "label": "Attach Omang (ID)", "value": "", "drag": true, "multiple": false, "limit": 3, "instruction": "Drop file here or <em>click to upload</em>", "tip": "Attachments must be jpg/png files with a size less than 500kb" } ] }, { "id": "section_1544056272594", "name": "Company Details", "description": "Please complete the application form below. Make sure to answer all questions and attach any documents/files where applicable", "showFields": true, "updated": false, "fields": [ { "width": 12, "type": "input-text", "id": "field_1544056315437", "label": "Company Name:", "value": "", "placeholder": "Enter the company name...", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "mini", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 12, "type": "input-text", "id": "field_1544056345513", "label": "Branch Name", "value": "", "placeholder": "Enter the branch name...", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "mini", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 12, "type": "date-picker", "id": "field_1544056894126", "label": "Incorporation Date", "value": "", "placeholder": "Select Date", "dateType": "date", "dateFormat": "dd/MM/yyyy", "disabled": false }, { "width": 12, "type": "select", "id": "field_1544056726985", "label": "Industry", "value": "", "placeholder": "Select", "disabled": false, "clearable": false, "multiple": false, "collapseTags": false, "filterable": false, "allowCreate": false, "options": [ { "value": "ICT", "disabled": false }, { "value": "Events", "disabled": false }, { "value": "Consultancy", "disabled": false }, { "value": "Education", "disabled": false } ] }, { "width": 24, "type": "slider", "id": "field_1544056531520", "label": "Number Of Employees:", "value": 10, "min": 0, "max": 100, "disabled": false, "showTooltip": true, "step": 1, "showStops": false, "showInput": true, "range": false, "vertical": false, "verticalHeight": 50 }, { "width": 22, "type": "checkbox", "id": "field_1544056431197", "label": "Services Requested", "value": [], "min": 0, "max": 10, "options": [ { "value": "Website", "disabled": false }, { "value": "Hosting", "disabled": false }, { "value": "Domain", "disabled": false }, { "value": "Email", "disabled": false }, { "value": "Graphic Design", "disabled": false }, { "value": "Advertising", "disabled": false } ] }, { "width": 8, "type": "switch", "id": "field_1544056623457", "label": "Billing Plan", "value": true, "activeColor": "#13ce66", "inactiveColor": "#3A98F1", "activeText": "Pay by month", "inactiveText": "Pay by year" }, { "width": 12, "type": "rating", "id": "field_1544056666068", "label": "Rate Your Experience", "value": 3, "colors": [ "#ff3737", "#16ff07", "#FF9900" ], "showText": true, "texts": [ "Very Bad", "Bad", "Good", "Very Good", "Excellent" ], "disabled": false } ] } ],
                loader: false
            }
        },
        methods: {
            displaySectionModal(section){
                this.showSection = section; 
                this.showSectionModal = true;
            },
            displayFieldEditDrawer(field){
                this.showField = field; 
                this.showFieldDrawer = true;
            },
            startSavingLoader() {
                this.loader = this.$loading({
                    lock: true,
                    text: 'Saving...',
                    spinner: 'el-icon-loading',
                    background: 'rgba(6, 50, 88, 0.7)'
                });
            },
            stopSavingLoader() {
                this.loader.close();
            },
            saveChanges(){
                const self = this;

                //  Start loader
                self.startSavingLoader();

                let draggableData = {
                    form_structure: this.sections
                };
                
                console.log('Attempt to send data...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/process-forms', draggableData)
                    .then(({data}) => {

                        //  Stop loader
                        self.stopSavingLoader();

                        console.log(data);
                    })         
                    .catch(({response}) => { 
                        console.log('draggable.vue - Error saving...');
                        //  Stop loader
                        //self.stopSavingLoader();  
                    });
            }
        }
    }
</script>