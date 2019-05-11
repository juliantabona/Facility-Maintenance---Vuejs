<template>
    
    <!-- Industry Selector -->
    <div>
        <Select v-model="localSelectedIndustry" :style="{ width:'100%' }" placeholder="Select industry" not-found-text="No industries found"
                @on-change="$emit('updated', $event)">
            <Option 
                v-for="(item, index) in industries" 
                :value="item" 
                :label="item" 
                :key="index">
                {{ item }}
            </Option>
        </Select>
    </div>
</template>

<script>

    export default {
        props: {
            selectedIndustry: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                localSelectedIndustry: this.selectedIndustry,
                industries: [
                    'Accounting',
                    'Administration & Office Support',
                    'Advertising, Arts & Media',
                    'Banking & Financial Services',
                    'Call Centre & Customer Service',
                    'Community Services & Development',
                    'Construction',
                    'Consulting & Strategy',
                    'Design & Architechture',
                    'Education & Training',
                    'Engineering',
                    'Farming, Animals & Conservation',
                    'Government & Defence',
                    'Healthcare & Medical',
                    'Hospitality & Tourism',
                    'Human Resources & Recruitment',
                    'Information & Communication Technology',
                    'Insurance & Superannuation',
                    'Legal',
                    'Manufacturing, Transport & Logistics',
                    'Marketing & Communications',
                    'Mining, Resources & Energy',
                    'Real Estate & Property',
                    'Retail & Consumer Products',
                    'Sales',
                    'Science & Technology',
                    'Sport & Recreation',
                    'Trades & Services',
                    'Other'
                ]
            }
        },
        watch: {
            selectedIndustry: function (val) {
                this.localSelectedIndustry = val;
            }
        }
    };
</script>