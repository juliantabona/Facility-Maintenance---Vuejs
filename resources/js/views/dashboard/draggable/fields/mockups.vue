<style>

    /*  Upload Field */

    .el-upload{
        width:100% !important;
    }

    .el-upload .el-upload-dragger{
        width:100% !important;
        height: 100px !important;
    }

    .el-upload .el-upload-dragger .el-icon-upload{
        margin: 10px 0 10px !important;
    }

</style>

<template>

    <div>

        <div v-if="fieldInstance.type == 'input-text'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-input type="text" 
                        :id="fieldInstance.id" 
                        :placeholder="fieldInstance.placeholder" 
                        v-model="fieldInstance.value"
                        :suffix-icon="fieldInstance.suffixIcon"
                        :disabled="fieldInstance.disabled"
                        :prefix-icon="fieldInstance.prefixIcon"
                        :clearable="fieldInstance.clearable"
                        :size="fieldInstance.size"
                        :maxlength="fieldInstance.maxlength"
                        :readonly="fieldInstance.readonly"
                        class="w-100">
                        <template v-if="fieldInstance.prepend" slot="prepend">{{ fieldInstance.prepend }}</template>
                        <template v-if="fieldInstance.append" slot="append">{{ fieldInstance.append }}</template>
                </el-input>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                        <el-input type="text" 
                            v-model="fieldInstance.value" 
                            :maxlength="fieldInstance.maxlength"
                            size="mini">
                        </el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Prepend:</b></span>
                        <el-input type="text" v-model="fieldInstance.prepend" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Append:</b></span>
                        <el-input type="text" v-model="fieldInstance.append" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Prefix Icon:</b> E.g) el-icon-search</span>
                        <el-input type="text" v-model="fieldInstance.prefixIcon" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Suffix Icon:</b> E.g) el-icon-date</span>
                        <el-input type="text" v-model="fieldInstance.suffixIcon" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Size:</b></span>
                        <el-radio-group v-model="fieldInstance.size" class="w-100">
                            <el-radio v-for="option in [{label:'large'}, {label:'small'}, {label:'mini'}]" :key="option.label" :label="option.label"></el-radio>
                        </el-radio-group>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Characters:</b></span>
                        <el-input-number v-model="fieldInstance.maxlength" size="mini" :max="200" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Clearable:</b></span>
                        <el-switch v-model="fieldInstance.clearable" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Read Only:</b></span>
                        <el-switch v-model="fieldInstance.readonly" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'input-textarea'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-input type="textarea" 
                        :id="fieldInstance.id" 
                        :placeholder="fieldInstance.placeholder" 
                        v-model="fieldInstance.value"
                        :disabled="fieldInstance.disabled"
                        :rows="fieldInstance.rows"
                        :maxlength="fieldInstance.maxlength"
                        :autosize="fieldInstance.autosize"
                        :resize="fieldInstance.resize ? '' : 'none'"
                        :readonly="fieldInstance.readonly"
                        class="w-100">
                </el-input>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                        <el-input type="text" 
                            v-model="fieldInstance.value" 
                            :maxlength="fieldInstance.maxlength"
                            size="mini">
                        </el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Height:</b></span>
                        <el-input-number v-model="fieldInstance.rows" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Characters:</b></span>
                        <el-input-number v-model="fieldInstance.maxlength" size="mini" :max="2000" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Resizable:</b></span>
                        <el-switch v-model="fieldInstance.resize" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Read Only:</b></span>
                        <el-switch v-model="fieldInstance.readonly" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'input-number'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label d-inline-block mb-1"><strong>{{ fieldInstance.label }}</strong></span>
                <el-input-number 
                    :id="fieldInstance.id" 
                    :placeholder="fieldInstance.placeholder" 
                    v-model="fieldInstance.value"
                    :disabled="fieldInstance.disabled"
                    :min="fieldInstance.min" 
                    :max="fieldInstance.max"
                    :step="fieldInstance.step"
                    :precision="fieldInstance.precision"
                    :size="fieldInstance.size"
                    :controls-position="fieldInstance.controlsPosition"
                    class="w-100">
                </el-input-number>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                        <el-input type="text" 
                            v-model="fieldInstance.value" 
                            :maxlength="fieldInstance.maxlength"
                            size="mini">
                        </el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Minimum Number:</b></span>
                        <el-input-number v-model="fieldInstance.min" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Number:</b></span>
                        <el-input-number v-model="fieldInstance.max" size="mini" :max="2000" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Step:</b></span>
                        <el-input-number v-model="fieldInstance.step" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Precision:</b></span>
                        <el-input-number v-model="fieldInstance.precision" size="mini" :max="2000" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Size:</b></span>
                        <el-radio-group v-model="fieldInstance.size" class="w-100">
                            <el-radio v-for="option in [{label:'large'}, {label:'small'}, {label:'mini'}]" :key="option.label" :label="option.label"></el-radio>
                        </el-radio-group>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Align:</b></span>
                        <el-radio-group v-model="fieldInstance.controlsPosition" class="w-100">
                            <el-radio v-for="option in [{label:'Vertical', value:''}, {label:'Horizontal', value:'right'}]" :key="option.label" :label="option.value">{{ option.label }}</el-radio>
                        </el-radio-group>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'select'" >
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-select 
                    :key="refreshKey"
                    v-model="fieldInstance.value" 
                    :placeholder="fieldInstance.placeholder"
                    :disabled="fieldInstance.disabled"
                    :clearable="fieldInstance.clearable"
                    :multiple="fieldInstance.multiple"
                    :collapse-tags="fieldInstance.collapseTags"
                    :filterable="fieldInstance.filterable"
                    :allow-create="fieldInstance.allowCreate"
                    class="w-100">
                    <el-option
                        v-for="option in fieldInstance.options"
                        :key="option.value"
                        :value="option.value"
                        :disabled="option.disabled">
                    </el-option>
                </el-select>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                        <el-col :span="24" class="mb-3">
                        <h3 class="mt-4 float-left">Options</h3>
                        <el-tooltip class="item" effect="dark" content="Add a custom option" placement="top-start">
                            <el-button style="margin:10px auto;" type="primary" icon="el-icon-plus" class="float-right"
                                        @click="addOption">Add Option
                            </el-button>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <draggable 
                            element="el-row"
                            :component-data="getElementRowData()"
                            :list="fieldInstance.options" 
                            :options="{draggable:'.select-option', group:'dropdown-select-options', handle:'.select-dragger'}" 
                            @start="drag=true" 
                            @end="drag=false"
                            class="options-box">
                                <el-col v-for="(option, index) in fieldInstance.options" :key="'option_'+option.label+'_'+index" :span="24" class="select-option pb-2 pt-1 mb-1 mt-1">
                                    <el-input type="text" 
                                        :id="'option_'+option.label+'_'+index"
                                        :placeholder="'Enter option '+(index+1)" 
                                        v-model="option.value"
                                        :clearable="true"
                                        size="mini"
                                        class="w-100">
                                        <template slot="append">
                                            <i class="field-icon el-icon-delete mr-2" @click="removeOption(index)"></i>
                                            <i class="field-icon el-icon-remove-outline mr-2" 
                                                @click="option.disabled = !option.disabled"
                                                :style="option.disabled ? 'color:red;': ''">
                                            </i>
                                            <i class="select-dragger field-icon el-icon-rank mr-2"></i>
                                        </template>
                                    </el-input>
                                </el-col>
                            </draggable>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Clearable:</b></span>
                        <el-switch v-model="fieldInstance.clearable" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Multiple:</b></span>
                        <el-switch @change="forceRerender" 
                                    v-model="fieldInstance.multiple" active-text="Yes" inactive-text="No">
                        </el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Collapse Tags:</b></span>
                        <el-switch v-model="fieldInstance.collapseTags" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Filterable:</b></span>
                        <el-switch v-model="fieldInstance.filterable" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Allow Create:</b></span>
                        <el-switch v-model="fieldInstance.allowCreate" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'slider'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-slider 
                        v-model="fieldInstance.value"
                        :min="fieldInstance.min"
                        :max="fieldInstance.max"
                        :show-tooltip="fieldInstance.showTooltip"
                        :step="fieldInstance.step"
                        :show-stops="fieldInstance.showStops"
                        :show-input="fieldInstance.showInput"
                        :range="fieldInstance.range"
                        :vertical="fieldInstance.vertical"
                        :height="fieldInstance.verticalHeight"
                        class="w-100">
                </el-slider>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                        <el-input-number v-model="fieldInstance.value" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Steps:</b></span>
                        <el-input-number v-model="fieldInstance.step" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Minimum Value:</b></span>
                        <el-input-number v-model="fieldInstance.min" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Value:</b></span>
                        <el-input-number v-model="fieldInstance.max" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Show Stops:</b></span>
                        <el-switch v-model="fieldInstance.showStops" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Show Input:</b></span>
                        <el-switch v-model="fieldInstance.showInput" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Show Range:</b></span>
                        <el-switch v-model="fieldInstance.range" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Make Vertical:</b></span>
                        <el-switch v-model="fieldInstance.vertical" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Vertical Height:</b></span>
                        <el-input-number v-model="fieldInstance.verticalHeight" size="mini" class="w-100"></el-input-number>
                        {{ fieldInstance.verticalHeight }}
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'file-upload'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-upload
                    v-model="fieldInstance.value"
                    :drag="fieldInstance.drag"
                    :multiple="fieldInstance.multiple"
                    :limit="fieldInstance.limit"
                    action="https://jsonplaceholder.typicode.com/posts/"
                    class="w-100">
                    <i class="el-icon-upload"></i>
                    <div v-show="fieldInstance.instruction" class="el-upload__text"><span v-html="fieldInstance.instruction"></span></div>
                    <div v-show="fieldInstance.tip" class="el-upload__tip" slot="tip"><span v-html="fieldInstance.tip"></span></div>
                </el-upload>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Multiple Uploads:</b></span>
                        <el-switch v-model="fieldInstance.multiple" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col v-if="fieldInstance.multiple" :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Uploads Limit:</b></span>
                        <el-input-number v-model="fieldInstance.limit" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Drag & Drop:</b></span>
                        <el-switch v-model="fieldInstance.drag" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Instruction:</b></span>
                        <el-input type="text" v-model="fieldInstance.instruction" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Tips/Notes:</b></span>
                        <el-input type="text" v-model="fieldInstance.tip" size="mini"></el-input>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'radio'" >
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>

                <el-radio-group
                    v-model="fieldInstance.value" 
                    class="w-100">
                    <el-radio
                        v-for="(option, index) in fieldInstance.options"
                        :key="option.value"
                        :value="option.value+'_'+index"
                        :label="option.value"
                        :disabled="option.disabled">
                    </el-radio>
                </el-radio-group>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                        <el-col :span="24" class="mb-3">
                        <h3 class="mt-4 float-left">Options</h3>
                        <el-tooltip class="item" effect="dark" content="Add a custom option" placement="top-start">
                            <el-button style="margin:10px auto;" type="primary" icon="el-icon-plus" class="float-right"
                                        @click="addOption">Add Option
                            </el-button>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <draggable 
                            element="el-row"
                            :component-data="getElementRowData()"
                            :list="fieldInstance.options" 
                            :options="{draggable:'.select-option', group:'radio-options', handle:'.select-dragger'}" 
                            @start="drag=true" 
                            @end="drag=false"
                            class="options-box">
                                <el-col v-for="(option, index) in fieldInstance.options" :key="'option_'+option.label+'_'+index" :span="24" class="select-option pb-2 pt-1 mb-1 mt-1">
                                    <el-input type="text" 
                                        :id="'option_'+option.label+'_'+index"
                                        :placeholder="'Enter option '+(index+1)" 
                                        v-model="option.value"
                                        :clearable="true"
                                        size="mini"
                                        class="w-100">
                                        <template slot="append">
                                            <i class="field-icon el-icon-delete mr-2" @click="removeOption(index)"></i>
                                            <i class="field-icon el-icon-remove-outline mr-2" 
                                                @click="option.disabled = !option.disabled"
                                                :style="option.disabled ? 'color:red;': ''">
                                            </i>
                                            <i class="select-dragger field-icon el-icon-rank mr-2"></i>
                                        </template>
                                    </el-input>
                                </el-col>
                            </draggable>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'checkbox'" >
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                
                <el-checkbox-group
                    v-model="fieldInstance.value" 
                    :min="fieldInstance.min"
                    :max="fieldInstance.max"
                    class="w-100">
                    <el-checkbox
                        v-for="option in fieldInstance.options"
                        :key="option.value"
                        :label="option.value"
                        :disabled="option.disabled">
                        {{ option.value }}
                    </el-checkbox>
                </el-checkbox-group>
            </div>

            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                        <el-col :span="24" class="mb-3">
                        <h3 class="mt-4 float-left">Options</h3>
                        <el-tooltip class="item" effect="dark" content="Add a custom option" placement="top-start">
                            <el-button style="margin:10px auto;" type="primary" icon="el-icon-plus" class="float-right"
                                        @click="addOption">Add Option
                            </el-button>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <draggable 
                            element="el-row"
                            :component-data="getElementRowData()"
                            :list="fieldInstance.options" 
                            :options="{draggable:'.select-option', group:'checkbox-options', handle:'.select-dragger'}" 
                            @start="drag=true" 
                            @end="drag=false"
                            class="options-box">
                                <el-col v-for="(option, index) in fieldInstance.options" :key="'option_'+option.label+'_'+index" :span="24" class="select-option pb-2 pt-1 mb-1 mt-1">
                                    <el-input type="text" 
                                        :id="'option_'+option.label+'_'+index"
                                        :placeholder="'Enter option '+(index+1)" 
                                        v-model="option.value"
                                        :clearable="true"
                                        size="mini"
                                        class="w-100">
                                        <template slot="append">
                                            <i class="field-icon el-icon-delete mr-2" @click="removeOption(index)"></i>
                                            <i class="field-icon el-icon-remove-outline mr-2" 
                                                @click="option.disabled = !option.disabled"
                                                :style="option.disabled ? 'color:red;': ''">
                                            </i>
                                            <i class="select-dragger field-icon el-icon-rank mr-2"></i>
                                        </template>
                                    </el-input>
                                </el-col>
                            </draggable>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Minimum Checked:</b></span>
                        <el-input-number v-model="fieldInstance.min" size="mini" class="w-100"></el-input-number>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Checked:</b></span>
                        <el-input-number v-model="fieldInstance.max" size="mini" class="w-100"></el-input-number>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'switch'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-switch
                    v-model="fieldInstance.value"
                    :active-color="fieldInstance.activeColor"
                    :inactive-color="fieldInstance.inactiveColor"
                    :active-text="fieldInstance.activeText"
                    :inactive-text="fieldInstance.inactiveText"
                    class="w-100">
                </el-switch>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>InActive Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.inactiveText" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Active Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.activeText" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-block mr-2 mb-2"><b>InActive Color:</b></span>
                            <el-tooltip class="item" effect="dark" content="Click the OK button after selecting your prefered color" placement="top-start">
                            <el-color-picker v-model="fieldInstance.inactiveColor"></el-color-picker>
                        </el-tooltip>
                    </el-col>
                    <el-col :span="12" class="mb-3">
                        <span class="field-label d-block mr-2 mb-2"><b>Active Color:</b></span>
                            <el-tooltip class="item" effect="dark" content="Click the OK button after selecting your prefered color" placement="top-start">
                            <el-color-picker v-model="fieldInstance.activeColor"></el-color-picker>
                        </el-tooltip>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'rating'">
            <div class="field-box">
            <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-rate 
                    v-model="fieldInstance.value"
                    :colors="fieldInstance.colors"
                    :texts="itemObjToArray"
                    :show-text="fieldInstance.showText"
                    :disabled="fieldInstance.disabled"
                    class="w-100">
                </el-rate>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Show Text:</b></span>
                        <el-switch v-model="fieldInstance.showText" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col v-if="fieldInstance.showText" :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Display Text:</b></span><br>
                        <span class="text-warning mr-2 mb-2"><b>NOTE: Click outside the input to update changes.</b></span>
                        <el-row :gutter="20">
                            <el-col :span="24" class="mb-2 mt-2"
                                    v-for="(option, index) in itemArrayToObj" :key="'option_'+option.value+'_'+index">
                                <el-input type="text" 
                                    :id="'option_'+option.value+'_'+index"
                                    :placeholder="'Enter text for star '+(index+1)" 
                                    v-model="option.value"
                                    :clearable="true"
                                    size="mini"
                                    class="w-100"
                                    @change="fieldInstance.texts.splice(index, 1, option.value);">
                                </el-input>
                            </el-col>
                        </el-row>
                    </el-col>
                    <el-col :span="8" class="mb-3" v-for="(option, index) in fieldInstance.colors" :key="'color_'+option.value+'_'+index">
                        <span class="field-label d-block mr-2 mb-2"><b>Color {{ (index+1) }}:</b></span>
                            <el-tooltip class="item" effect="dark" content="Click the OK button after selecting your prefered color" placement="top-start">
                            <el-color-picker v-model="fieldInstance.colors[index]"></el-color-picker>
                        </el-tooltip>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'alert'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-alert
                    :title="fieldInstance.title"
                    :description="fieldInstance.description"
                    :type="fieldInstance.alertType"
                    :show-icon="fieldInstance.showIcon"
                    :closable="fieldInstance.closable"
                    class="w-100">
                </el-alert>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Title:</b></span>
                        <el-input type="text" v-model="fieldInstance.title" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Description:</b></span>
                        <el-input type="text" v-model="fieldInstance.description" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Type:</b></span>
                        <el-radio-group v-model="fieldInstance.alertType" class="w-100">
                            <el-radio v-for="option in [{label:'success'}, {label:'info'}, {label:'warning'}, {label:'error'}]" :key="option.label" :label="option.label"></el-radio>
                        </el-radio-group>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Show Icon:</b></span>
                        <el-switch v-model="fieldInstance.showIcon" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Closable:</b></span>
                        <el-switch v-model="fieldInstance.closable" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'select-time-picker'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-time-select 
                        v-model="fieldInstance.value"
                        :placeholder="fieldInstance.placeholder" 
                        :disabled="fieldInstance.disabled"
                        :picker-options="fieldInstance.pickerOptions"
                        class="w-100">
                </el-time-select>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Start Time:</b></span>
                        <el-time-select 
                                v-model="fieldInstance.pickerOptions.start"
                                placeholder="Pick Start"
                                :picker-options=" { start: '00:00', step: fieldInstance.pickerOptions.step, end: '23:59' } "
                                class="w-100">
                        </el-time-select>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Start Time:</b></span>
                        <el-time-select 
                                v-model="fieldInstance.pickerOptions.end"
                                placeholder="Pick End" 
                                :picker-options=" { start: '00:00', step: fieldInstance.pickerOptions.step, end: '23:59' } "
                                class="w-100">
                        </el-time-select>
                    </el-col>

                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Time Interval:</b></span>
                        <el-select 
                            v-model="fieldInstance.pickerOptions.step" 
                            placeholder="Select time interval"
                            class="w-100">
                            <el-option
                                v-for="option in 
                                    [
                                        {label: '1 min', value: '00:01'}, {label: '5 mins', value: '00:05'}, 
                                        {label: '10 mins', value: '00:10'}, {label: '15 mins', value: '00:15'}, {label: '20 mins', value: '00:20'}, 
                                        {label: '30 mins', value: '00:30'}, {label: '1 hour', value: '01:00'}, {label: '2 hours', value: '02:00'}
                                    ]"
                                :key="option.label"
                                :value="option.value"
                                :label="option.label">
                            </el-option>
                        </el-select>
                    </el-col>

                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'arbitrary-time-picker'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-time-picker
                        :key="refreshKey"
                        v-model="fieldInstance.value"
                        :placeholder="fieldInstance.placeholder" 
                        :arrow-control="fieldInstance.arrowControl"
                        :picker-options="fieldInstance.pickerOptions.selectableRange"
                        :disabled="fieldInstance.disabled"
                        class="w-100">
                </el-time-picker>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>

                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Arrow Control:</b></span>
                        <el-switch v-model="fieldInstance.arrowControl" active-text="Yes" inactive-text="No" @change="forceRerender"></el-switch>
                    </el-col>

                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'arbitrary-time-range'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-time-picker
                        :key="refreshKey"
                        v-model="fieldInstance.value"
                        :arrow-control="fieldInstance.arrowControl"
                        is-range
                        :range-separator="fieldInstance.rangeSeparator"
                        :start-placeholder="fieldInstance.startPlaceholder"
                        :end-placeholder="fieldInstance.endPlaceholder"
                        :disabled="fieldInstance.disabled"
                        class="w-100">
                </el-time-picker>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Start Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.startPlaceholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>End Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.endPlaceholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Separator Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.rangeSeparator" size="mini"></el-input>
                    </el-col>

                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Arrow Control:</b></span><br/>
                        <el-switch v-model="fieldInstance.arrowControl" active-text="Yes" inactive-text="No" @change="forceRerender"></el-switch>
                    </el-col>

                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span><br/>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'date-picker'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-date-picker
                        v-model="fieldInstance.value"
                        :placeholder="fieldInstance.placeholder"
                        :type="fieldInstance.dateType"
                        :format="datePickerDateFormated"
                        :disabled="fieldInstance.disabled"
                        class="w-100">
                </el-date-picker>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Type:</b></span>
                        <el-select 
                            v-model="fieldInstance.dateType" 
                            class="d-inline">
                            <el-option
                                v-for="option in 
                                    [
                                        {label: 'Date', value: 'date'}, {label: 'Week', value: 'week'},
                                        {label: 'Month', value: 'month'}, {label: 'Year', value: 'year'},
                                    ]"
                                :key="option.label"
                                :value="option.value"
                                :label="option.label">
                            </el-option>
                        </el-select>
                    </el-col>
                    <el-col :span="24" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Date Format:</b></span>
                        <el-row :gutter="5">
                            <el-col :span="8">
                                <el-select 
                                    v-model="datePickerDateFormat.entry1" 
                                    placeholder="Entry 1"
                                    class="d-inline">
                                    <el-option
                                        v-for="option in 
                                            [
                                                {label: 'None', value: ''}, 
                                                {label: 'Year - 2017', value: 'yyyy'}, 
                                                {label: 'Month - 1', value: 'M'}, {label: 'Month - 01', value: 'MM'},
                                                {label: 'Week - 4', value: 'W'}, {label: 'Week - 04', value: 'WW'}, 
                                                {label: 'Day - 2', value: 'd'}, {label: 'Day - 02', value: 'dd'},
                                                {label: 'Hour - 3', value: 'h'}, {label: 'Hour - 03', value: 'hh'}, {label: 'Hour - 15', value: 'HH'},
                                                {label: 'Minute - 5', value: 'm'}, {label: 'Minute - 05', value: 'mm'},
                                                {label: 'Second - 5', value: 's'}, {label: 'Second - 05', value: 'ss'},
                                                {label: 'am/pm', value: 'a'}, {label: 'AM/PM', value: 'A'}
                                            ]"
                                        :key="option.label"
                                        :value="option.value"
                                        :label="option.label">
                                    </el-option>
                                </el-select>
                                <el-input type="text" v-model="datePickerDateFormat.separator1" size="mini" class="mt-1"></el-input>
                            </el-col>
                            <el-col :span="8">
                                <el-select 
                                    v-model="datePickerDateFormat.entry2" 
                                    placeholder="Entry 1"
                                    class="d-inline">
                                    <el-option
                                        v-for="option in 
                                            [
                                                {label: 'None', value: ''}, 
                                                {label: 'Year - 2017', value: 'yyyy'}, 
                                                {label: 'Month - 1', value: 'M'}, {label: 'Month - 01', value: 'MM'},
                                                {label: 'Week - 4', value: 'W'}, {label: 'Week - 04', value: 'WW'}, 
                                                {label: 'Day - 2', value: 'd'}, {label: 'Day - 02', value: 'dd'},
                                                {label: 'Hour - 3', value: 'h'}, {label: 'Hour - 03', value: 'hh'}, {label: 'Hour - 15', value: 'HH'},
                                                {label: 'Minute - 5', value: 'm'}, {label: 'Minute - 05', value: 'mm'},
                                                {label: 'Second - 5', value: 's'}, {label: 'Second - 05', value: 'ss'},
                                                {label: 'am/pm', value: 'a'}, {label: 'AM/PM', value: 'A'}
                                            ]"
                                        :key="option.label"
                                        :value="option.value"
                                        :label="option.label">
                                    </el-option>
                                </el-select>
                                <el-input type="text" v-model="datePickerDateFormat.separator2" size="mini" class="mt-1"></el-input>
                            </el-col>
                            <el-col :span="8">
                                <el-select 
                                    v-model="datePickerDateFormat.entry3" 
                                    placeholder="Entry 1"
                                    class="d-inline">
                                    <el-option
                                        v-for="option in 
                                            [
                                                {label: 'None', value: ''}, 
                                                {label: 'Year - 2017', value: 'yyyy'}, 
                                                {label: 'Month - 1', value: 'M'}, {label: 'Month - 01', value: 'MM'},
                                                {label: 'Week - 4', value: 'W'}, {label: 'Week - 04', value: 'WW'}, 
                                                {label: 'Day - 2', value: 'd'}, {label: 'Day - 02', value: 'dd'},
                                                {label: 'Hour - 3', value: 'h'}, {label: 'Hour - 03', value: 'hh'}, {label: 'Hour - 15', value: 'HH'},
                                                {label: 'Minute - 5', value: 'm'}, {label: 'Minute - 05', value: 'mm'},
                                                {label: 'Second - 5', value: 's'}, {label: 'Second - 05', value: 'ss'},
                                                {label: 'am/pm', value: 'a'}, {label: 'AM/PM', value: 'A'}
                                            ]"
                                        :key="option.label"
                                        :value="option.value"
                                        :label="option.label">
                                    </el-option>
                                </el-select>
                            </el-col>
                        </el-row>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span><br/>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'date-range'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-date-picker
                        v-model="fieldInstance.value"
                        type="daterange"
                        :range-separator="fieldInstance.rangeSeparator"
                        :start-placeholder="fieldInstance.startPlaceholder"
                        :end-placeholder="fieldInstance.endPlaceholder"
                        :disabled="fieldInstance.disabled"
                        class="w-100">
                </el-date-picker>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Start Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.startPlaceholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>End Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.endPlaceholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Separator Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.rangeSeparator" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span><br/>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'datetime-picker'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-date-picker
                        type="datetime"
                        v-model="fieldInstance.value"
                        :placeholder="fieldInstance.placeholder"
                        :disabled="fieldInstance.disabled"
                        class="w-100">
                </el-date-picker>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                        <el-input type="text" v-model="fieldInstance.placeholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span><br/>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>

        <div v-if="fieldInstance.type == 'datetime-range'">
            <div class="field-box">
                <span v-if="fieldInstance.label" class="field-label"><strong>{{ fieldInstance.label }}</strong></span>
                <el-date-picker
                    v-model="fieldInstance.value"
                    type="datetimerange"
                    :range-separator="fieldInstance.rangeSeparator"
                    :start-placeholder="fieldInstance.startPlaceholder"
                    :end-placeholder="fieldInstance.endPlaceholder"
                    :disabled="fieldInstance.disabled"
                    class="w-100">
                </el-date-picker>
            </div>
            <div v-if="showSettings" class="field-editor mt-3">
                <Divider orientation="left"><h5><Icon type="ios-flash-outline" size="20" /> Settings</h5></Divider>
                <el-row :gutter="20" class="settings-box">
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                        <el-input type="text" v-model="fieldInstance.label" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Start Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.startPlaceholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>End Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.endPlaceholder" size="mini"></el-input>
                    </el-col>
                    <el-col :span="24" class="mb-3">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Separator Text:</b></span>
                        <el-input type="text" v-model="fieldInstance.rangeSeparator" size="mini"></el-input>
                    </el-col>
                    <el-col :span="12" class="mb-2 mt-2">
                        <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span><br/>
                        <el-switch v-model="fieldInstance.disabled" active-text="Yes" inactive-text="No"></el-switch>
                    </el-col>
                </el-row>
            </div>
        </div>


    </div>

</template>

<script>
    import draggable from 'vuedraggable'
    export default {
        props:{
            field: {
                default: null
            },
            showSettings: {
                default: false
            }
        },
        components: {
            draggable
        },
        data() {
            return {
                refreshKey: 0,
                datePickerDateFormat: {
                    entry1: 'dd',
                    separator1: '/',
                    entry2: 'MM',
                    separator2: '/',
                    entry3: 'yyyy',
                }
            };
        },
        computed: {
            datePickerDateFormated(){
                return this.datePickerDateFormat.entry1 + this.datePickerDateFormat.separator1 +
                       this.datePickerDateFormat.entry2 + this.datePickerDateFormat.separator2 +
                       this.datePickerDateFormat.entry3
            },
            fieldInstance() {
                return this.field || {
                    type: null
                };
            },

            itemArrayToObj: {
                // getter
                get: function () {
                    const itemsArray = ((this.fieldInstance || {}).texts || []);
                    const itemsObj = [];

                    for(var x=0; x < itemsArray.length; x++){
                        itemsObj.push({value: itemsArray[x]});
                    }

                    //  
                    console.log(itemsObj);
                    

                    return itemsObj;
                },
                // setter
                set: function (newValue) {
                    console.log(newValue);
                }
            },

            itemObjToArray(){
                const itemsObj = (this.itemArrayToObj || {});
                const itemsArray = [];

                for(var x=0; x < itemsObj.length; x++){
                    itemsArray.push(itemsObj[x].value);
                }

                console.log(itemsArray);

                return itemsArray;
            }
        },
        methods: {
            forceRerender() {
                this.refreshKey += 1;  
            },
            getElementRowData() {
                return {
                    props: {
                        gutter: 20              //  Allow spacing between row columns
                    }
                };
            },
            addOption(){
                console.log('Field Options');
                console.log(this.fieldInstance);
                var Options = (this.fieldInstance || {}).options;
                var optionValue = "Option " + ((Options || 0).length + 1);
                
                Options.push({ value: optionValue, disabled: false });
            },
            removeOption(index){

                var Options = (this.fieldInstance || {}).options;

                if(Options.length <= 1){
                    alert('You must have atleast one option');
                }else{
                    Options.splice(index, 1);
                }
            }

        } 
    };
</script>