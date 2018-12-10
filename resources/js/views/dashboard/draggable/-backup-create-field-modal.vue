<style>

    .content-box .field-label {
        font-size: 14px;
        margin: 0 10px 5px 0;
        display: inline-block;
    }

    .field-editor .settings-box {
        max-height: 350px !important;
        padding: 20px;
        border: 1px solid #0000002b;
        -webkit-box-shadow: inset 1px 2px 5px #0000005c;
        box-shadow: inset 1px 2px 5px #0000005c;
        overflow-y:auto;
    }

    .field-editor .settings-box .options-box{
        border: 1px dotted #409eff;
        box-shadow: 10px 10px #409eff30;
        padding: 20px;
        margin-bottom: 20px;
    }

</style>

<template>
    <el-dialog
        title="Create Field"
        :visible.sync="dialogVisible"
        width="50%"
        :before-close="handleBackdropClose"
        @close="$emit('closed')">

        <el-row v-if="showMainOptions" :gutter="20">
            <el-col :span="8" v-for="option in mainOptions" :key="option.name" class="mb-2">
                <el-card shadow="hover" :body-style="{ padding: '0px' }" >
                    <div style="padding: 14px;">
                        <i :class="option.icon+' text-center'" style="font-size: 50px; display: block;"></i>
                        <p class="text-center">{{ option.name }}</p>
                        <el-button type="primary" class="float-right" @click="nextStep(option)">Continue</el-button>
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <el-row v-if="showSubOptions" :gutter="20">
            <el-col :span="8" v-for="option in subOptions" :key="option.name" class="mb-2">
                <p>{{ option.name }}</p>
            </el-col>
        </el-row>

        <el-row v-if="showOptionSettings && selectedOption" :gutter="20">
            <el-col :span="24" class="mb-3">

                <div class="content-box">

                    <div v-if="selectedOption.field.type == 'input-text'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-input type="text" 
                                :id="selectedOption.field.id" 
                                :placeholder="selectedOption.field.placeholder" 
                                v-model="selectedOption.field.value"
                                :suffix-icon="selectedOption.field.suffixIcon"
                                :disabled="selectedOption.field.disabled"
                                :prefix-icon="selectedOption.field.prefixIcon"
                                :clearable="selectedOption.field.clearable"
                                :size="selectedOption.field.size"
                                :maxlength="selectedOption.field.maxlength"
                                :readonly="selectedOption.field.readonly"
                                class="w-100">
                                <template v-if="selectedOption.field.prepend" slot="prepend">{{ selectedOption.field.prepend }}</template>
                                <template v-if="selectedOption.field.append" slot="append">{{ selectedOption.field.append }}</template>
                        </el-input>
                        <div class="field-editor mt-3">
                            <h2 class="mt-2 mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                                    <el-input type="text" 
                                        v-model="selectedOption.field.value" 
                                        :maxlength="selectedOption.field.maxlength"
                                        size="mini">
                                    </el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.placeholder" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Prepend:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.prepend" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Append:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.append" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Prefix Icon:</b> E.g) el-icon-search</span>
                                    <el-input type="text" v-model="selectedOption.field.prefixIcon" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Suffix Icon:</b> E.g) el-icon-date</span>
                                    <el-input type="text" v-model="selectedOption.field.suffixIcon" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Size:</b></span>
                                    <el-radio-group v-model="selectedOption.field.size" class="w-100">
                                        <el-radio v-for="option in [{label:'large'}, {label:'small'}, {label:'mini'}]" :key="option.label" :label="option.label"></el-radio>
                                    </el-radio-group>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Characters:</b></span>
                                    <el-input-number v-model="selectedOption.field.maxlength" size="mini" :max="200" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                                    <el-switch v-model="selectedOption.field.disabled" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Clearable:</b></span>
                                    <el-switch v-model="selectedOption.field.clearable" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Read Only:</b></span>
                                    <el-switch v-model="selectedOption.field.readonly" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'input-textarea'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-input type="textarea" 
                                :id="selectedOption.field.id" 
                                :placeholder="selectedOption.field.placeholder" 
                                v-model="selectedOption.field.value"
                                :disabled="selectedOption.field.disabled"
                                :rows="selectedOption.field.rows"
                                :maxlength="selectedOption.field.maxlength"
                                :autosize="selectedOption.field.autosize"
                                :resize="selectedOption.field.resize ? '' : 'none'"
                                :readonly="selectedOption.field.readonly"
                                class="w-100">
                        </el-input>
                        <div class="field-editor mt-3">
                            <h2 class="mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                                    <el-input type="text" 
                                        v-model="selectedOption.field.value" 
                                        :maxlength="selectedOption.field.maxlength"
                                        size="mini">
                                    </el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.placeholder" size="mini"></el-input>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Height:</b></span>
                                    <el-input-number v-model="selectedOption.field.rows" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Characters:</b></span>
                                    <el-input-number v-model="selectedOption.field.maxlength" size="mini" :max="2000" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                                    <el-switch v-model="selectedOption.field.disabled" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Resizable:</b></span>
                                    <el-switch v-model="selectedOption.field.resize" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Read Only:</b></span>
                                    <el-switch v-model="selectedOption.field.readonly" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'input-number'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-input-number 
                            :id="selectedOption.field.id" 
                            :placeholder="selectedOption.field.placeholder" 
                            v-model="selectedOption.field.value"
                            :disabled="selectedOption.field.disabled"
                            :min="selectedOption.field.min" 
                            :max="selectedOption.field.max"
                            :step="selectedOption.field.step"
                            :precision="selectedOption.field.precision"
                            :size="selectedOption.field.size"
                            :controls-position="selectedOption.field.controlsPosition"
                            class="w-100">
                        </el-input-number>
                        <div class="field-editor mt-3">
                            <h2 class="mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                                    <el-input type="text" 
                                        v-model="selectedOption.field.value" 
                                        :maxlength="selectedOption.field.maxlength"
                                        size="mini">
                                    </el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.placeholder" size="mini"></el-input>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Minimum Number:</b></span>
                                    <el-input-number v-model="selectedOption.field.min" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Number:</b></span>
                                    <el-input-number v-model="selectedOption.field.max" size="mini" :max="2000" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Step:</b></span>
                                    <el-input-number v-model="selectedOption.field.step" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Precision:</b></span>
                                    <el-input-number v-model="selectedOption.field.precision" size="mini" :max="2000" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Size:</b></span>
                                    <el-radio-group v-model="selectedOption.field.size" class="w-100">
                                        <el-radio v-for="option in [{label:'large'}, {label:'small'}, {label:'mini'}]" :key="option.label" :label="option.label"></el-radio>
                                    </el-radio-group>
                                </el-col>
                                <el-col :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Align:</b></span>
                                    <el-radio-group v-model="selectedOption.field.controlsPosition" class="w-100">
                                        <el-radio v-for="option in [{label:'Vertical', value:''}, {label:'Horizontal', value:'right'}]" :key="option.label" :label="option.value">{{ option.label }}</el-radio>
                                    </el-radio-group>
                                </el-col>
                                <el-col :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                                    <el-switch v-model="selectedOption.field.disabled" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                            </el-row>
                        </div>
                    </div>
  
                    <div v-if="selectedOption.field.type == 'select'" >
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-select 
                            v-model="selectedOption.field.value" 
                            :placeholder="selectedOption.field.placeholder"
                            :disabled="selectedOption.field.disabled"
                            :clearable="selectedOption.field.clearable"
                            :multiple="selectedOption.field.multiple"
                            :collapse-tags="selectedOption.field.collapseTags"
                            :filterable="selectedOption.field.filterable"
                            :allow-create="selectedOption.field.allowCreate"
                            class="w-100">
                            <el-option
                                v-for="option in selectedOption.field.options"
                                :key="option.value"
                                :value="option.value"
                                :disabled="option.disabled">
                            </el-option>
                        </el-select>
                        <div class="field-editor mt-3">
                            <h2 class="mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.placeholder" size="mini"></el-input>
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
                                        :list="selectedOption.field.options" 
                                        :options="{draggable:'.select-option', group:'dropdown-select-options', handle:'.select-dragger'}" 
                                        @start="drag=true" 
                                        @end="drag=false"
                                        class="options-box">
                                            <el-col v-for="(option, index) in selectedOption.field.options" :key="'option_'+option.label+'_'+index" :span="24" class="select-option pb-2 pt-1 mb-1 mt-1">
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
                                    <el-switch v-model="selectedOption.field.disabled" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Clearable:</b></span>
                                    <el-switch v-model="selectedOption.field.clearable" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Multiple:</b></span>
                                    <el-switch @change="isMultiple(selectedOption)" v-model="selectedOption.field.multiple" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Collapse Tags:</b></span>
                                    <el-switch v-model="selectedOption.field.collapseTags" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Filterable:</b></span>
                                    <el-switch v-model="selectedOption.field.filterable" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Allow Create:</b></span>
                                    <el-switch v-model="selectedOption.field.allowCreate" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'slider'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-slider 
                                v-model="selectedOption.field.value"
                                :min="selectedOption.field.min"
                                :max="selectedOption.field.max"
                                :show-tooltip="selectedOption.field.showTooltip"
                                :step="selectedOption.field.step"
                                :show-stops="selectedOption.field.showStops"
                                :show-input="selectedOption.field.showInput"
                                :range="selectedOption.field.range"
                                :vertical="selectedOption.field.vertical"
                                :height="selectedOption.field.verticalHeight+'px'"
                                class="w-100">
                        </el-slider>

                        <div class="field-editor mt-3">
                            <h2 class="mt-2 mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Default Value:</b></span>
                                    <el-input-number v-model="selectedOption.field.value" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Steps:</b></span>
                                    <el-input-number v-model="selectedOption.field.step" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Minimum Value:</b></span>
                                    <el-input-number v-model="selectedOption.field.min" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Value:</b></span>
                                    <el-input-number v-model="selectedOption.field.max" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Show Stops:</b></span>
                                    <el-switch v-model="selectedOption.field.showStops" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Show Input:</b></span>
                                    <el-switch v-model="selectedOption.field.showInput" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Show Range:</b></span>
                                    <el-switch v-model="selectedOption.field.range" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Make Vertical:</b></span>
                                    <el-switch v-model="selectedOption.field.vertical" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Vertical Height:</b></span>
                                    <el-input-number v-model="selectedOption.field.verticalHeight" size="mini" class="w-100"></el-input-number>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'file-upload'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-upload
                            v-model="selectedOption.field.value"
                            :drag="selectedOption.field.drag"
                            :multiple="selectedOption.field.multiple"
                            :limit="selectedOption.field.limit"
                            action="https://jsonplaceholder.typicode.com/posts/"
                            class="w-100">
                            <i class="el-icon-upload"></i>
                            <div v-show="selectedOption.field.instruction" class="el-upload__text"><span v-html="selectedOption.field.instruction"></span></div>
                            <div v-show="selectedOption.field.tip" class="el-upload__tip" slot="tip"><span v-html="selectedOption.field.tip"></span></div>
                        </el-upload>
                        <div class="field-editor mt-3">
                            <h2 class="mt-2 mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Multiple Uploads:</b></span>
                                    <el-switch v-model="selectedOption.field.multiple" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col v-if="selectedOption.field.multiple" :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Uploads Limit:</b></span>
                                    <el-input-number v-model="selectedOption.field.limit" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Drag & Drop:</b></span>
                                    <el-switch v-model="selectedOption.field.drag" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Instruction:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.instruction" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Tips/Notes:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.tip" size="mini"></el-input>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'radio'" >
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>

                        <el-radio-group
                            v-model="selectedOption.field.value" 
                            class="w-100">
                            <el-radio
                                v-for="(option, index) in selectedOption.field.options"
                                :key="option.value"
                                :value="option.value+'_'+index"
                                :label="option.value"
                                :disabled="option.disabled">
                            </el-radio>
                        </el-radio-group>

                        <div class="field-editor mt-3">
                            <h2 class="mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
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
                                        :list="selectedOption.field.options" 
                                        :options="{draggable:'.select-option', group:'radio-options', handle:'.select-dragger'}" 
                                        @start="drag=true" 
                                        @end="drag=false"
                                        class="options-box">
                                            <el-col v-for="(option, index) in selectedOption.field.options" :key="'option_'+option.label+'_'+index" :span="24" class="select-option pb-2 pt-1 mb-1 mt-1">
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

                    <div v-if="selectedOption.field.type == 'checkbox'" >
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        
                        <el-checkbox-group
                            v-model="selectedOption.field.value" 
                            :min="selectedOption.field.min"
                            :max="selectedOption.field.max"
                            class="w-100">
                            <el-checkbox
                                v-for="option in selectedOption.field.options"
                                :key="option.value"
                                :label="option.value"
                                :disabled="option.disabled">
                                {{ option.value }}
                            </el-checkbox>
                        </el-checkbox-group>

                        <div class="field-editor mt-3">
                            <h2 class="mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
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
                                        :list="selectedOption.field.options" 
                                        :options="{draggable:'.select-option', group:'checkbox-options', handle:'.select-dragger'}" 
                                        @start="drag=true" 
                                        @end="drag=false"
                                        class="options-box">
                                            <el-col v-for="(option, index) in selectedOption.field.options" :key="'option_'+option.label+'_'+index" :span="24" class="select-option pb-2 pt-1 mb-1 mt-1">
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
                                    <el-input-number v-model="selectedOption.field.min" size="mini" class="w-100"></el-input-number>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Maximum Checked:</b></span>
                                    <el-input-number v-model="selectedOption.field.max" size="mini" class="w-100"></el-input-number>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'switch'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-switch
                            v-model="selectedOption.field.value"
                            :active-color="selectedOption.field.activeColor"
                            :inactive-color="selectedOption.field.inactiveColor"
                            :active-text="selectedOption.field.activeText"
                            :inactive-text="selectedOption.field.inactiveText"
                            class="w-100">
                        </el-switch>
                        <div class="field-editor mt-3">
                            <h2 class="mt-2 mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Active Text:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.activeText" size="mini"></el-input>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>InActive Text:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.inactiveText" size="mini"></el-input>
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-block mr-2 mb-2"><b>Active Color:</b></span>
                                     <el-tooltip class="item" effect="dark" content="Click the OK button after selecting your prefered color" placement="top-start">
                                        <el-color-picker v-model="selectedOption.field.activeColor"></el-color-picker>
                                    </el-tooltip>
                                    
                                </el-col>
                                <el-col :span="12" class="mb-3">
                                    <span class="field-label d-block mr-2 mb-2"><b>InActive Color:</b></span>
                                     <el-tooltip class="item" effect="dark" content="Click the OK button after selecting your prefered color" placement="top-start">
                                        <el-color-picker v-model="selectedOption.field.inactiveColor"></el-color-picker>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'rating'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-rate 
                            v-model="selectedOption.field.value"
                            :colors="selectedOption.field.colors"
                            :texts="itemObjToArray"
                            :show-text="selectedOption.field.showText"
                            :disabled="selectedOption.field.disabled"
                            class="w-100">
                        </el-rate>
                        <div class="field-editor mt-3">
                            <h2 class="mt-2 mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Placeholder:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.placeholder" size="mini"></el-input>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Disabled:</b></span>
                                    <el-switch v-model="selectedOption.field.disabled" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Show Text:</b></span>
                                    <el-switch v-model="selectedOption.field.showText" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col v-if="selectedOption.field.showText" :span="24" class="mb-2 mt-2">
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
                                                @change="selectedOption.field.texts.splice(index, 1, option.value);">
                                            </el-input>
                                        </el-col>
                                    </el-row>
                                </el-col>
                                <el-col :span="8" class="mb-3" v-for="(option, index) in selectedOption.field.colors" :key="'color_'+option.value+'_'+index">
                                    <span class="field-label d-block mr-2 mb-2"><b>Color {{ (index+1) }}:</b></span>
                                     <el-tooltip class="item" effect="dark" content="Click the OK button after selecting your prefered color" placement="top-start">
                                        <el-color-picker v-model="selectedOption.field.colors[index]"></el-color-picker>
                                    </el-tooltip>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                    <div v-if="selectedOption.field.type == 'alert'">
                        <span v-if="selectedOption.field.label" class="field-label">{{ selectedOption.field.label }}</span>
                        <el-alert
                            :title="selectedOption.field.title"
                            :description="selectedOption.field.description"
                            :type="selectedOption.field.alertType"
                            :show-icon="selectedOption.field.showIcon"
                            :closable="selectedOption.field.closable"
                            class="w-100">
                        </el-alert>
                        <div class="field-editor mt-3">
                            <h2 class="mt-2 mb-3">Settings</h2>
                            <el-row :gutter="20" class="settings-box">
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Label:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.label" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Title:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.title" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-3">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Description:</b></span>
                                    <el-input type="text" v-model="selectedOption.field.description" size="mini"></el-input>
                                </el-col>
                                <el-col :span="24" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Type:</b></span>
                                    <el-radio-group v-model="selectedOption.field.alertType" class="w-100">
                                        <el-radio v-for="option in [{label:'success'}, {label:'info'}, {label:'warning'}, {label:'error'}]" :key="option.label" :label="option.label"></el-radio>
                                    </el-radio-group>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Show Icon:</b></span>
                                    <el-switch v-model="selectedOption.field.showIcon" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                                <el-col :span="12" class="mb-2 mt-2">
                                    <span class="field-label d-inline-block mr-2 mb-2"><b>Closable:</b></span>
                                    <el-switch v-model="selectedOption.field.closable" active-text="Yes" inactive-text="No"></el-switch>
                                </el-col>
                            </el-row>
                        </div>
                    </div>

                </div>

            </el-col>
        </el-row>

        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">Cancel</el-button>
            <el-button v-if="showOptionSettings && selectedOption" type="primary" @click="createField(selectedOption)">Create</el-button>
        </span>
    </el-dialog>

</template>

<script>
  import draggable from 'vuedraggable'
  export default {
        props:{
            showModal: {
                default: false
            }
        },
        components: {
            draggable
        },
        data() {
        return {
            dialogVisible: this.showModal,
            //  Main options
            showMainOptions: true,
            mainOptions: [
                {
                    name: 'Text',
                    icon: 'el-icon-edit',
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
                    name: 'Number',
                    icon: 'el-icon-edit',
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
                    name: 'Paragraph',
                    icon: 'el-icon-edit-outline',
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
                        resize: 'none',                                 //  options: "", "none"
                        readonly: false,                   
                    }
                },
                {
                    name: 'Dropdown',
                    icon: 'el-icon-edit',
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
                                value: 'Option 1',
                                disabled: false
                            }
                        ]                
                    }
                },
                {
                    name: 'Slider',
                    icon: 'el-icon-edit',
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
                    name: 'Checkbox',
                    icon: 'el-icon-edit',
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
                                value: 'Option 1',
                                disabled: false
                            }
                        ]   
                    }
                },
                {
                    name: 'Radio',
                    icon: 'el-icon-more-outline',
                    field: { 
                        width: 24,
                        type: "radio",
                        id: "",
                        label: "Enter field label...",
                        value: "",
                        options: [
                            {
                                value: 'Option 1',
                                disabled: false
                            }
                        ]   
                    }
                },
                {
                    name: 'Date/Time',
                    icon: 'el-icon-edit',
                    optionsHeading: 'Date And Time Options',
                    options: [
                        {
                            name: 'Time selector',
                            type: 'select-time-picker'
                        },
                        {
                            name: 'Time picker',
                            type: 'arbitrary-time-picker'
                        },
                        {
                            name: 'Time range picker',
                            type: 'arbitrary-time-range'
                        },
                        {
                            name: 'Date picker',
                            type: 'date-picker'
                        },
                        {
                            name: 'Date range picker',
                            type: 'date-range'
                        },
                        {
                            name: 'Datetime picker',
                            type: 'datetime-picker'
                        },
                        {
                            name: 'Datetime range picker',
                            type: 'datetime-range'
                        }
                    ]
                },
                {
                    name: 'Upload',
                    icon: 'el-icon-upload',
                    field: {
                        width: 24,
                        type: "file-upload",
                        id: "",
                        label: "Upload documents...",
                        value: "",
                        drag: true,
                        multiple: false,
                        limit: 3,
                        instruction: 'Drop file here or <em>click to upload</em>',
                        tip: 'jpg/png files with a size less than 500kb',
                        
                    }
                },
                {
                    name: 'Rating',
                    icon: 'el-icon-edit',
                    field: {
                        width: 24,
                        type: "rating",
                        id: "field_company_name_10000000014",
                        label: "Basic Rating",
                        value: 3,
                        colors: ['#ff3737', '#16ff07', '#FF9900'],
                        showText: true,
                        texts: ['Very Bad', 'Bad', 'Good', 'Very Good', 'Excellent'],
                        disabled: false
                    }
                },
                {
                    name: 'Switch',
                    icon: 'el-icon-star-off',
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
                    name: 'Alert',
                    icon: 'el-icon-edit',
                    type: 'alert',
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
            ],
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
    watch: {
        showModal: function (val) {
            this.dialogVisible = val;
        }
    },
    methods: {
        nextStep(option){
            console.log('Next step...');

            //   const name = option.options.length; // Cannot read property 'length' of undefined

            const subOptionsExist = ((option || {}).options || {}).length;

            //  Hide all panels
            this.hideAllPanels();

            //  If we have suboptions, lets show them to the user
            if(subOptionsExist){
                //  Show sub options
                console.log('showing sub options...');
                this.showSubOptions = true;
                this.subOptions = option.options;
            
            //  Otherwise display the current field settings
            }else{
                //  Show the option settings
                console.log('showing settings...');
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
                    errors.push('One of the options does not have a value.');
                }
            }

            //  If we have any errors, alert the user
            if(errors.length){
                alert(errors[0]);
            }else{
                selectedOption.field.id = 'field_' + Date.now();
                this.$emit('created', selectedOption.field);
                this.dialogVisible = false;
            }
        },
        isMultiple(selectedOption){
            if(selectedOption.field.multiple && !Array.isArray(selectedOption.field.value)){
                selectedOption.field.value = [];
            }else if(!selectedOption.field.multiple && Array.isArray(selectedOption.field.value)){
                selectedOption.field.value = '';
            }
        },
        getElementRowData() {
            return {
                props: {
                    gutter: 20              //  Allow spacing between row columns
                }
            };
        },
        addOption(){
            var Options = (this.selectedOption.field || {}).options;
            var optionValue = "Option " + ((Options || 0).length + 1);
            
            Options.push({ value: optionValue, disabled: false });
        },
        removeOption(index){

            var Options = (this.selectedOption.field || {}).options;

            if(Options.length <= 1){
                alert('You must have atleast one option');
            }else{
                Options.splice(index, 1);
            }
        },
        handleBackdropClose(done) {
        this.$confirm('Are you sure to close this dialog?')
            .then(_ => {
                console.log('Closed!!!');
                done();
            })
            .catch(_ => {
                console.log('Open!!!');
            });
        }
    },
    computed: {

        itemArrayToObj: {
            // getter
            get: function () {
                const itemsArray = (((this.selectedOption || {}).field || {}).texts || []);
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
    } 
  };
</script>