<template>

    <div>

        <quotationSummaryWidget 
            v-for="(quotation, i) in quotations" 
            :id="quotation.id"
            :quotation="quotation"
            :key="i">
        </quotationSummaryWidget>
    
    </div>

</template>

<script>

    import quotationSummaryWidget from './quotationSummaryWidget.vue';

    export default {
        components: { 
            quotationSummaryWidget
        },
        props: {
            quotations: {
                type: Array,
                default: null
            },
            showMenuBtn: {
                type: Boolean,
                default: true
            }

        }
    };
  
</script>