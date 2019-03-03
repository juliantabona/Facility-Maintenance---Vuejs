<template>
    
    <!-- Short Code  Selector -->

    <Poptip slot="append" word-wrap width="200" trigger="hover" :content="shortCodeExample ? shortCodeExample : 'Hover for examples...'">
        <Select v-model="selectedShortCode" :key="shortCodeRenderKey" size="small" style="width:180px"
                placeholder="+ Add Dynamic Content">
            <Option v-for="(shortcode, shortcode_notation) in localShortCodes" :key="shortcode_notation" :value="shortcode" 
                    @click.native="handleSelection(shortcode_notation)"
                    @mouseover.native="shortCodeExample = shortcode"
                    @mouseleave.native="shortCodeExample = ''">
                {{ shortcode_notation }}
            </Option>
        </Select>
    </Poptip>
    
</template>

<script>

    export default {
        props: {
            shortCodes: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                localShortCodes: this.shortCodes,
                selectedShortCode: '',
                shortCodeExample: '',
                shortCodeRenderKey: 0,
            }
        },
        watch: {
            shortCodes: function (val) {
                this.localShortCodes = val;
            }
        },
        methods: {
            handleSelection(shortcode_notation){
                this.selectedShortCode = '';
                this.shortCodeRenderKey += 1;
                this.$emit( 'selected', shortcode_notation);

            }
        }
    };
</script>