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
        <input ref="shortcode_input" type="hidden" :value="inputValue">
    </Poptip>
    
</template>

<script>

    export default {
        props: {
            shortcodes: {
                type: Object,
                default: null
            },
            copyToClipboard: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return {
                localShortCodes: this.shortcodes,
                selectedShortCode: '',
                shortCodeExample: '',
                inputValue: '',
                shortCodeRenderKey: 0,
            }
        },
        watch: {
            shortcodes: function (val) {
                this.localShortCodes = val;
            }
        },
        methods: {
            handleSelection(shortcode_notation){
                if( this.copyToClipboard ){
                    this.copyShortcode(shortcode_notation);
                }

                this.selectedShortCode = '';
                this.shortCodeRenderKey += 1;
                this.$emit( 'selected', shortcode_notation);

            },
            copyShortcode (shortcode_notation) {

                this.inputValue = shortcode_notation;
                var codeToCopy = this.$refs.shortcode_input;

                var self = this;

                setTimeout(() => {
                    codeToCopy.setAttribute('type', 'text')
                    codeToCopy.select();
                    try {
                        document.execCommand('copy');
                        self.$Message.success('Shortcode copied! Now paste');
                    } catch (err) {
                        self.$Message.error('Sorry, unable to copy');
                    }
                    codeToCopy.setAttribute('type', 'hidden')
                }, 10);

            }
        }
    };
</script>