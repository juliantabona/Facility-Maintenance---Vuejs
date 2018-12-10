<style>
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
        opacity:0;
        margin: 10px 15px 0 0;
    }

    .form-section:hover .section-toolbox{
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
    <el-row :gutter="20">
        <el-col :span="16">
            <el-row :gutter="20" class="mb-3">
                <el-col :span="24">
                    <el-button type="success" class="float-right ml-2" size="small" @click="saveChanges()">Save Changes</el-button>
                    <el-button type="primary" class="float-right" size="small" @click="createSectionModalVisible = true;">+ Add Section</el-button>
                </el-col>
            </el-row>
            <h3 class="mb-3">Sections</h3>
            <el-row :gutter="24">
                <el-col :span="14">
                    <el-tooltip class="item" effect="dark" content="Drag and drop to order sections" placement="top-start">
                        <draggable v-model="sections" 
                                element="el-breadcrumb"
                                :component-data="getElementBreadCrumbData()"
                                :options="{draggable:'.bcrumb-section', group:'bcrumb-sections'}" 
                                @start="drag=true" 
                                @end="drag=false"
                                class="mb-5">
                            <el-breadcrumb-item v-for="section in sections" :key="section.id" class="bcrumb-section" @click.native="showSectionModal(section)">
                                <el-badge :value="section.name" class="item" type="primary"></el-badge>
                            </el-breadcrumb-item>
                        </draggable>
                    </el-tooltip>
                </el-col>
            </el-row>
            
            <draggable v-model="sections" 
                       :options="{draggable:'.form-section', group:'sections', handle:'.section-dragger'}" 
                       @start="drag=true" 
                       @end="drag=false">
                <el-card shadow="hover" v-for="(section, index) in sections" :key="section.id" :class="'box-card form-section mb-2'+(!section.showFields ? ' hidden-content':'')">
                    <div slot="header" class="clearfix">
                        <el-tooltip class="item" effect="dark" content="Add Field" placement="top-start">
                            <el-button style="float: right;" type="primary" icon="el-icon-plus" circle
                                        @click="addFieldToSection(section)">
                            </el-button>
                        </el-tooltip>
                        <div class="section-toolbox float-right d-block">
                            <i :class="'section-icon el-icon-caret-'+(section.showFields ? 'top':'bottom')+' mr-2'" @click="section.showFields = !section.showFields"></i>
                            <i class="section-icon el-icon-delete mr-2" @click="removeSection(index)"></i>
                            <i class="section-icon el-icon-edit mr-2"></i>
                            <i class="section-icon section-dragger el-icon-rank"></i>
                        </div>
                        
                        <h3>{{section.name}} - {{section.id}}</h3>
                        <p class="text-smaller">{{section.description}}</p>
                    </div>         
                    <draggable 
                        v-show="section.showFields"
                        element="el-row"
                        :component-data="getElementRowData()"
                        :list="section.fields" 
                        :move="checkMove"
                        :options="{draggable:'.section-field', group:'section-fields', handle:'.field-dragger'}" 
                        @start="drag=true" 
                        @end="drag=false" 
                        style="min-height:100px;">
                            
                            <el-col v-for="(field, index) in section.fields" :key="field.id" :span="field.width" class="section-field pb-2 pt-1 mb-1 mt-1">
                                <div class="field-toolbox float-right d-block">
                                    <span class="mr-5">
                                        <el-badge :value="field.width+'/24'" class="item" type="primary"></el-badge>
                                    </span>
                                    <i class="field-icon field-dragger el-icon-rank mr-2"></i>
                                    <i class="field-icon el-icon-delete mr-2" @click="removeField(section, index)"></i>
                                    <i class="field-icon el-icon-edit mr-2"></i>
                                    <i class="field-icon el-icon-d-arrow-left" @click="minimizeField(field, 12)"></i>
                                    <i class="field-icon el-icon-arrow-left" @click="minimizeField(field, 4)"></i>
                                    <i class="field-icon el-icon-arrow-right" @click="maximizeField(field, 4)"></i>
                                    <i class="field-icon el-icon-d-arrow-right" @click="maximizeField(field, 12)"></i>
                                    
                                </div>
                        
                                <div class="section-content-box">
                                    <div v-if="field.type == 'input-text'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-input type="text" 
                                                :id="field.id" 
                                                :placeholder="field.placeholder" 
                                                v-model="field.value"
                                                :suffix-icon="field.suffixIcon"
                                                :disabled="field.disabled"
                                                :prefix-icon="field.prefixIcon"
                                                :clearable="field.clearable"
                                                :size="field.size"
                                                :maxlength="field.maxlength"
                                                :readonly="field.readonly"
                                                class="w-100">
                                                <template v-if="field.prepend" slot="prepend">{{ field.prepend }}</template>
                                                <template v-if="field.append" slot="append">{{ field.append }}</template>
                                        </el-input>
                                    </div>
                                    <div v-if="field.type == 'input-textarea'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-input type="textarea" 
                                                :id="field.id" 
                                                :placeholder="field.placeholder" 
                                                v-model="field.value"
                                                :disabled="field.disabled"
                                                :rows="field.rows"
                                                :maxlength="field.maxlength"
                                                :autosize="field.autosize"
                                                :resize="field.resize ? '' : 'none'"
                                                :readonly="field.readonly"
                                                class="w-100">
                                        </el-input>
                                    </div>
                                    <div v-if="field.type == 'input-number'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-input-number 
                                                :id="field.id" 
                                                :placeholder="field.placeholder" 
                                                v-model="field.value"
                                                :disabled="field.disabled"
                                                :min="field.min" 
                                                :max="field.max"
                                                :step="field.step"
                                                :precision="field.precision"
                                                :size="field.size"
                                                :controls-position="field.controlsPosition"
                                                class="w-100">
                                        </el-input-number>
                                    </div>
                                    <div v-if="field.type == 'select'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-select 
                                                v-model="field.value" 
                                                :placeholder="field.placeholder"
                                                :disabled="field.disabled"
                                                :clearable="field.clearable"
                                                :multiple="field.multiple"
                                                :collapse-tags="field.collapseTags"
                                                :filterable="field.filterable"
                                                :allow-create="field.allowCreate"
                                                class="w-100">
                                            <el-option
                                                v-for="option in field.options"
                                                :key="option.value"
                                                :label="option.label"
                                                :value="option.value"
                                                :disabled="option.disabled">
                                            </el-option>
                                        </el-select>
                                    </div>
                                    <div v-if="field.type == 'slider'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-slider 
                                                v-model="field.value"
                                                :show-tooltip="field.showTooltip"
                                                :step="field.step"
                                                :show-stops="field.showStops"
                                                :show-input="field.showInput"
                                                :range="field.range"
                                                :vertical="field.vertical"
                                                :height="field.verticalHeight"
                                                class="w-100">
                                        </el-slider>
                                    </div>
                                    <div v-if="field.type == 'select-time-picker'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-time-select 
                                                v-model="field.value"
                                                :placeholder="field.placeholder"
                                                :picker-options="field.pickerOptions"
                                                class="w-100">
                                        </el-time-select>
                                    </div>
                                    <div v-if="field.type == 'arbitrary-time-picker'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-time-picker
                                                v-model="field.value"
                                                :placeholder="field.placeholder"
                                                :arrow-control="field.arrowControl"
                                                :picker-options="field.pickerOptions"
                                                class="w-100">
                                        </el-time-picker>
                                    </div>
                                    <div v-if="field.type == 'arbitrary-time-range'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-time-picker
                                                v-model="field.value"
                                                :arrow-control="field.arrowControl"
                                                is-range
                                                :range-separator="field.rangeSeparator"
                                                :start-placeholder="field.startPlaceholder"
                                                :end-placeholder="field.endPlaceholder"
                                                class="w-100">
                                        </el-time-picker>
                                    </div>
                                    <div v-if="field.type == 'date-picker'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-date-picker
                                                v-model="field.value"
                                                :placeholder="field.placeholder"
                                                :type="field.dateType"
                                                :format="field.dateFormat"
                                                class="w-100">
                                        </el-date-picker>
                                    </div>
                                    <div v-if="field.type == 'date-range'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-date-picker
                                                v-model="field.value"
                                                type="daterange"
                                                :range-separator="field.rangeSeparator"
                                                :start-placeholder="field.startPlaceholder"
                                                :end-placeholder="field.endPlaceholder"
                                                class="w-100">
                                        </el-date-picker>
                                    </div>
                                    <div v-if="field.type == 'datetime-picker'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-date-picker
                                                type="datetime"
                                                v-model="field.value"
                                                :placeholder="field.placeholder"
                                                class="w-100">
                                        </el-date-picker>
                                    </div>
                                    <div v-if="field.type == 'datetime-range'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-date-picker
                                            v-model="field.value"
                                            type="datetimerange"
                                            :range-separator="field.rangeSeparator"
                                            :start-placeholder="field.startPlaceholder"
                                            :end-placeholder="field.endPlaceholder"
                                            class="w-100">
                                        </el-date-picker>
                                    </div>
                                    <div v-if="field.type == 'file-upload'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-upload
                                            v-model="field.value"
                                            :drag="field.drag"
                                            :multiple="field.multiple"
                                            :limit="field.limit"
                                            action="https://jsonplaceholder.typicode.com/posts/"
                                            class="w-100">
                                            <i class="el-icon-upload"></i>
                                            <div v-show="field.instruction" class="el-upload__text"><span v-html="field.instruction"></span></div>
                                            <div v-show="field.tip" class="el-upload__tip" slot="tip"><span v-html="field.tip"></span></div>
                                        </el-upload>
                                    </div>
                                    <div v-if="field.type == 'rating'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-rate 
                                            v-model="field.value"
                                            :colors="field.colors"
                                            :texts="field.texts"
                                            :show-text="field.showText"
                                            :disabled="field.disabled"
                                            class="w-100">
                                        </el-rate>
                                    </div>
                                    <div v-if="field.type == 'radio'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-radio-group
                                            v-model="field.value"
                                            class="w-100">
                                            <el-radio
                                                v-for="(option, index) in field.options"
                                                :key="option.value"
                                                :value="option.value+'_'+index"
                                                :label="option.value"
                                                :disabled="option.disabled">
                                            </el-radio>
                                        </el-radio-group>
                                    </div>
                                    <div v-if="field.type == 'checkbox'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-checkbox-group
                                            v-model="field.value" 
                                            :min="field.min"
                                            :max="field.max"
                                            class="w-100">
                                            <el-checkbox
                                                v-for="option in field.options"
                                                :key="option.value"
                                                :label="option.value"
                                                :disabled="option.disabled">
                                                {{ option.value }}
                                            </el-checkbox>
                                        </el-checkbox-group>
                                    </div>
                                    <div v-if="field.type == 'switch'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-switch
                                            v-model="field.value"
                                            :active-color="field.activeColor"
                                            :inactive-color="field.inactiveColor"
                                            :active-text="field.activeText"
                                            :inactive-text="field.inactiveText"
                                            class="w-100">
                                        </el-switch>
                                    </div>
                                    <div v-if="field.type == 'alert'">
                                        <span v-if="field.label" class="field-label">{{ field.label }}</span>
                                        <el-alert
                                            :title="field.title"
                                            :description="field.description"
                                            :type="field.alertType"
                                            :show-icon="field.showIcon"
                                            :closable="field.closable"
                                            class="w-100">
                                        </el-alert>
                                    </div>
                                </div>
                            </el-col>
                    </draggable>
                    <el-row v-if="!section.fields.length" :gutter="20" style="background: #EBEEF5;">
                        <el-alert
                            title="No content"
                            type="warning"
                            show-icon
                            :closable="false">
                        </el-alert>
                    </el-row>
                </el-card>
            </draggable>

            <el-row v-if="!sections.length" :gutter="20" class="mt-5">
                <el-col :span="24">
                    <el-alert
                        title="No Sections."
                        type="warning"
                        show-icon
                        :closable="false">
                    </el-alert>
                </el-col>
            </el-row>
        </el-col>
        <el-col :span="8">
            {{ sections }}
        </el-col>

        <create-draggable-section 
            v-if="createSectionModalVisible"
            :show-modal="createSectionModalVisible" 
            v-on:closed="createSectionModalVisible = !createSectionModalVisible;"
            v-on:created="sectionCreated">
        </create-draggable-section>
        <create-draggable-field 
            v-if="createFieldModalVisible"
            :show-modal="createFieldModalVisible" 
            v-on:closed="createFieldModalVisible = !createFieldModalVisible;"
            v-on:created="fieldCreated">
        </create-draggable-field>
        <show-draggable-section
            v-if="showSectionModalVisible"
            :show-modal="showSectionModalVisible" 
            :section="showSection"
            v-on:closed="showSectionModalVisible = !showSectionModalVisible;"
            v-on:updated="alert('updated!')">
        </show-draggable-section>

    </el-row>

</template>

<script>
    import draggable from 'vuedraggable'
    export default {
        components: {
            draggable
        },
        data(){
            return {
                createSectionModalVisible: false,
                createFieldModalVisible: false,
                showSectionModalVisible: false,
                showSection: null,
                addToSection: null,
                sections:[  
                    {  
                        id:"company_details_10000000001",
                        name:"Company Details",
                        description:"Complete the company fields below...",
                        showFields:true,
                        updated: false,
                        fields:[  
                            {  
                                width: 12,
                                type: "input-text",
                                id: "field_company_name_10000000001",
                                label: "Company Name",
                                value: "",
                                placeholder: "Enter Company Name",
                                suffixIcon: "el-icon-date",
                                prefixIcon: "el-icon-search",
                                prepend: "https://",
                                append: "Go",
                                size: "",                                //  options: large, small, mini
                                maxlength: 5,
                                disabled: false,
                                clearable: false,
                                readonly: false,
                            },
                            {  
                                width: 12,
                                type: "input-textarea",
                                id: "field_company_name_10000000002",
                                label: "Company Name",
                                value: "",
                                placeholder: "Enter Company description",
                                disabled: false,                               
                                maxlength: 2,
                                autosize: { 
                                    minRows: 2, 
                                    maxRows: 4
                                },
                                rows: 10,                                       //  comment: "autosize: null" for this to work
                                resize: 'none',                                 //  options: "", "none"
                                readonly: false,                                
                            },
                            {  
                                width: 12,
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
                            },
                            {  
                                width: 12,
                                type: "select",
                                id: "field_company_name_10000000004",
                                label: "Company Type",
                                value: "",
                                disabled: false,
                                clearable: true,
                                multiple : true,
                                collapseTags: true,
                                filterable: true,
                                allowCreate: true,
                                options: [
                                    {
                                        value: 'Option1',
                                        label: 'Option 1',
                                        disabled: false
                                    }, 
                                    {
                                        value: 'Option2',
                                        label: 'Option 2',
                                        disabled: false
                                    },
                                    {
                                        value: 'Option3',
                                        label: 'Option 3',
                                        disabled: true
                                    }
                                ] 
                            },
                            {  
                                width: 12,
                                type: "slider",
                                id: "field_company_name_10000000005",
                                label: "Company Limits",
                                value: 50,
                                disabled: false,
                                showTooltip: true,
                                step: 10,
                                showStops: true,
                                showInput: true,
                                range: false,
                                vertical: false,
                                verticalHeight: '50px'
                            }
                            ,
                            {  
                                width: 12,
                                type: "select-time-picker",
                                id: "field_company_name_10000000006",
                                label: "SelectCompany Time",
                                value: 50,
                                placeholder: "Select Company time",   
                                disabled: false,
                                pickerOptions: {
                                    start: '08:30',
                                    step: '00:15',
                                    end: '18:30'
                                }
                            },
                            {  
                                width: 12,
                                type: "arbitrary-time-picker",
                                id: "field_company_name_10000000007",
                                label: "Arbitrary Company Time",
                                value: 50,
                                placeholder: "Choose arbitrary company time",   
                                disabled: false,
                                pickerOptions: {
                                    selectableRange: '18:30:00 - 20:30:00'
                                },
                                arrowControl: true

                            },
                            {  
                                width: 12,
                                type: "arbitrary-time-range",
                                id: "field_company_name_10000000008",
                                label: "Arbitrary Company Time Range",
                                value: 50, 
                                disabled: false,
                                arrowControl: false,
                                //  Time range properties
                                rangeSeparator: 'To',
                                startPlaceholder: 'Start Here',
                                endPlaceholder: 'End Here'

                            },
                            {  
                                width: 12,
                                type: "date-picker",
                                id: "field_company_name_10000000009",
                                label: "Date of incorporation.",
                                value: "",
                                placeholder: "Enter date of incoporation", 
                                dateType: "date",                           //  options: date, week, month, year
                                dateFormat: ""                              /*  E.g) dateFormat:"Week WW" if the dateType:"week" refer to 
                                                                               "http://element.eleme.io/#/en-US/component/date-picker" for Date Formats
                                                                            */

                            },
                            {  
                                width: 12,
                                type: "date-range",
                                id: "field_company_name_10000000010",
                                label: "Range date of incorporation.",
                                value: "",
                                rangeSeparator: "Until",
                                startPlaceholder: "Start Date...",
                                endPlaceholder: "End Date..."

                            },
                            {  
                                width: 12,
                                type: "datetime-picker",
                                id: "field_company_name_10000000011",
                                label: "DateTime",
                                value: 50,
                                placeholder: "Choose a datetime"

                            },
                            {  
                                width: 12,
                                type: "datetime-range",
                                id: "field_company_name_10000000012",
                                label: "Range date of incorporation.",
                                value: "",
                                rangeSeparator: "Until",
                                startPlaceholder: "Start Datetime...",
                                endPlaceholder: "End Datetime..."

                            },
                            {  
                                width: 12,
                                type: "file-upload",
                                id: "field_company_name_10000000013",
                                label: "Upload documents...",
                                value: "",
                                drag: true,
                                multiple: true,
                                limit: 3,
                                instruction: 'Drop file here or <em>click to upload</em>',
                                tip: 'jpg/png files with a size less than 500kb',
                            },
                            {  
                                width: 12,
                                type: "rating",
                                id: "field_company_name_10000000014",
                                label: "Basic Rating",
                                value: 3,
                                colors: ['#ff3737', '#16ff07', '#FF9900'],
                                showText: true,
                                texts: ['Very Bad', 'Bad', 'Good', 'Very Good', 'Excellent'],
                                disabled: false
                            },
                            {  
                                width: 12,
                                type: "radio",
                                id: "field_company_name_10000000015",
                                label: "Company Type",
                                value: "Option 1",
                                options: [
                                    {
                                        label: 'Option 1',
                                        disabled: false
                                    }, 
                                    {
                                        label: 'Option 2',
                                        disabled: false
                                    },
                                    {
                                        label: 'Option 3',
                                        disabled: true
                                    }
                                ] 
                            },
                            {  
                                width: 12,
                                type: "checkbox",
                                id: "field_company_name_10000000016",
                                label: "Company Type",
                                value: ['Option 1', 'Option 2'],
                                min: 0,
                                max: 10,
                                options: [
                                    {
                                        label: 'Option 1',
                                        disabled: false
                                    }, 
                                    {
                                        label: 'Option 2',
                                        disabled: false
                                    },
                                    {
                                        label: 'Option 3',
                                        disabled: false
                                    },
                                    {
                                        label: 'Option 4',
                                        disabled: true
                                    }
                                ] 
                            },
                            {  
                                width: 12,
                                type: "switch",
                                id: "field_company_name_10000000017",
                                label: "Company Type",
                                value: 0,
                                activeColor: "#13ce66",
                                inactiveColor: "#dcdfe6",
                                activeText: "Pay by month",
                                inactiveText: "Pay by year"
                            },
                            {  
                                width: 12,
                                type: "alert",
                                id: "field_company_name_10000000018",
                                title: "Sample Title",
                                description: "Sample description",
                                alertType: "success",
                                showIcon: true,
                                closable: false
                            }
                        ]
                    },
                    {
                        id:"customer_details_10000000002",
                        name:"Customer Details", 
                        description:"Complete the customer fields below...",
                        showFields:true,
                        updated: false,
                        fields:[]
                    }
                ],
                loader: false
            }
        },
        methods: {
            checkMove: function(evt){
                console.log('evt.draggedContext');
                console.log(evt.draggedContext);
                console.log('evt.relatedContext');
                console.log(evt.relatedContext);
            },
            getElementRowData() {
                return {
                    props: {
                        class: "row-bg", 
                        gutter: 20              //  Allow spacing between row columns
                    }
                };
            },
            getElementBreadCrumbData() {
                return {
                    props: {
                        separatorClass: "el-icon-arrow-right",          //  Separator icon
                    }
                };
            },
            minimizeField(field, factor){

                if(field.width - factor > 0){
                    field.width -= factor;
                }

            },
            maximizeField(field, factor){

                if(field.width + factor <= 24){
                    field.width += factor;
                }else if(field.width + factor/2 <= 24){
                    field.width += factor/2;
                }else if(field.width + factor/4 <= 24){
                    field.width += factor/4;
                }


            },
            removeSection(index){
                this.$delete( this.sections, index );
            },
            removeField(section, index){
                this.$delete( section.fields, index );
            },
            sectionCreated(section){
                //  Add the new section
                this.sections.unshift(section);
            },
            addFieldToSection(section){
                this.createFieldModalVisible = true;
                this.addToSection = section;
            },
            fieldCreated(field){
                console.log('Field created!');
                console.log(field);
                //  Add the new field to specified section
                this.addToSection.fields.unshift(field);
            },
            showSectionModal(section){
                this.showSection = section; 
                this.showSectionModalVisible = true;
                console.log('show modal');
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