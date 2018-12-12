<style>

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
            <oq-Template-Content :sections="template.sections"></oq-Template-Content>
        </Col>

        <Col :span="8">
            <oq-Template-Sidebar :template="template" :sections="template.sections"></oq-Template-Sidebar>
        </Col>

        <show-draggable-section
            v-if="showSectionModal"
            :show-modal="showSectionModal" 
            :section="showSection"
            v-on:closed="showSectionModal = false"
            v-on:updated="alert('updated!')">
        </show-draggable-section>

        {{ template }}

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
                    name:"Jobcard Lifecycle",
                    description:"This is the default jobcard lifecycle",
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
                    },
                    sections:[ { "id": "section_1544508347475", "name": "Open", "description": "The jobcard is open and ready for processing", "showFields": false, "updated": false, "fields": [] }, { "id": "section_1544508428829", "name": "Deposit Paid", "description": "A deposit was paid by the customer prior starting the job", "showFields": false, "updated": false, "fields": [ { "width": 24, "type": "input-text", "id": "field_1544508629671", "label": "Amount Paid", "value": "", "placeholder": "Enter the amount paid", "suffixIcon": "", "prefixIcon": "", "prepend": "P", "append": "", "size": "small", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 24, "type": "select", "id": "field_1544508527614", "label": "Payment Method", "value": "", "placeholder": "Select Payment Method", "disabled": false, "clearable": false, "multiple": false, "collapseTags": false, "filterable": false, "allowCreate": false, "options": [ { "value": "Cash", "disabled": false }, { "value": "Cheque", "disabled": false }, { "value": "Bank Transfer", "disabled": false }, { "value": "Other", "disabled": false } ] }, { "width": 24, "type": "input-text", "id": "field_1544508733101", "label": "Back Account", "value": "", "placeholder": "Enter the name of the bank paid to...", "suffixIcon": "", "prefixIcon": "", "prepend": "", "append": "", "size": "large", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false } ] }, { "id": "section_1544508850833", "name": "Job Started", "description": "The job has been started and is in progress", "showFields": false, "updated": false, "fields": [ { "width": 24, "type": "input-text", "id": "field_1544509057887", "label": "Job Supervisor", "value": "", "placeholder": "Enter full names of job supervisor...", "suffixIcon": "", "prefixIcon": "", "prepend": "Full Name", "append": "", "size": "small", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 24, "type": "datetime-picker", "id": "field_1544508982472", "label": "Job Started On", "value": null, "placeholder": "Enter the date and time job started..." } ] }, { "id": "section_1544509120487", "name": "Inspection", "description": "The job has been completed and is currently undergoing inspection", "showFields": true, "updated": false, "fields": [ { "width": 24, "type": "input-text", "id": "field_1544509184988", "label": "Job Inspector", "value": "", "placeholder": "Enter full names of job inspector...", "suffixIcon": "", "prefixIcon": "", "prepend": "Full Name", "append": "", "size": "small", "maxlength": 200, "disabled": false, "clearable": false, "readonly": false }, { "width": 24, "type": "datetime-picker", "id": "field_1544509273428", "label": "Job Inspected On", "value": null, "placeholder": "Enter the date and time of job inspection..." }, { "width": 24, "type": "rating", "id": "field_1544509634532", "label": "Job Rating", "value": 3, "colors": [ "#ff3737", "#16ff07", "#FF9900" ], "showText": true, "texts": [ "Very Bad", "Bad", "Good", "Very Good", "Excellent" ], "disabled": false, "placeholder": "" }, { "width": 24, "type": "input-textarea", "id": "field_1544509719359", "label": "Comments", "value": "", "placeholder": "Write review comments if applicable...", "disabled": false, "maxlength": 500, "autosize": null, "rows": 2, "resize": false, "readonly": false } ] }, { "id": "section_1544509422219", "name": "Sign Off", "description": "Job inspected and approved. Official sign off between inspector and client ", "showFields": true, "updated": false, "fields": [ { "width": 24, "type": "file-upload", "id": "field_1544509577286", "label": "Upload signed inspection sheet...", "value": "", "drag": true, "multiple": false, "limit": 3, "instruction": "Drop file here or <em>click to upload</em>", "tip": "Only jpg,png or pdf files with a size less than 2M are allowed" }, { "width": 24, "type": "input-textarea", "id": "field_1544509874809", "label": "Comments", "value": "", "placeholder": "Write comments if applicable...", "disabled": false, "maxlength": 500, "autosize": null, "rows": 2, "resize": false, "readonly": false } ] }, { "id": "section_1544509793811", "name": "Closed", "description": "The job has been completed successfully", "showFields": true, "updated": false, "fields": [] } ],
                },
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