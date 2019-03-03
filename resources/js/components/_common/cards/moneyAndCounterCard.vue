<style scoped>

  .ivu-card{
    position: relative;
  }

  .ivu-card:hover{
    cursor: pointer;
  }
  
    .ivu-card.active{
        border: 1px solid #ededed;
        border-bottom: 3px solid #2d8cf0;
        box-shadow: 3px -3px 3px #d6d6d6;
    }

  .ivu-badge >>> .ivu-badge-count {
    position:absolute;
    top: -65px;
    right:0;
    font-family: inherit !important;
    font-weight: 600 !important;
    min-width: 35px !important;
    height: 20px !important;
    z-index: 1 !important;
  }

</style>
<template>

    <Card @click.native="clicked()" :style="cardStyle" :class="active ? 'active': ''">
        <h3 class="text-center mt-3" :style="{ fontSize: '20px' }">{{ formatPrice(amount, symbol) }}</h3>
        <div style="padding: 0px 15px;">
            <Badge :show-zero="showZero" :count="count" :type="type" style="width:100%;">
                <p class="text-center" style="padding-top:5px;">{{ title }}</p>
            </Badge>
        </div>
        <!-- Animated checkmark  -->
        <animatedCheckmark v-if="showCheckMark" :style="{ width: '30px', height: 'auto' }"></animatedCheckmark>
    </Card>

</template>

<script>

    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';

    export default {
        props: {
            cardStyle: {
                type: Object,
                default: () => { return { padding:0, minHeight:'100px' } }
            },
            showZero: {
                type: Boolean,
                default: false
            },
            count: {
                type: Number,
                default: 0
            },
            title: {
                type: String,
                default: 'Title' 
            },
            currency: {
                type: Object,
                default: null
            },
            amount: {
                type: [String, Number],
                default: 0 
            },
            type: {
                type: String,
                default: 'primary' 
            },
            route: {
                type: Object,
                default: null
            },
            active:{
                type: Boolean,
                default: false
            },
            showCheckMark: {
                type: Boolean,
                default: false
            }
        },
        components: { animatedCheckmark },
        data(){
            return {
                symbol: ((this.currency || {}).currency || {}).symbol || ''
            }
        },
        methods: {
            clicked(){
                if(this.route){
                    this.$router.push(this.route);
                }

                this.$emit('clicked');
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        },
    };
    
</script>