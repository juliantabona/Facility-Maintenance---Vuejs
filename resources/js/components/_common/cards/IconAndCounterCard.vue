<style scoped>
  .ivu-card:hover{
    cursor: pointer;
  }
  
  .ivu-badge >>> .ivu-badge-count {
    font-family: inherit !important;
    font-weight: 600 !important;
    min-width: 35px !important;
    height: 20px !important;
    z-index: 1 !important;
  }

  .animatedCheckmark{
    width: 36px;
    height: 36px;
    padding: 2px;
    position: absolute;
    right: 20px;
    background: #f5f5f5;
    border: 1px solid #d4d4d4;
    border-radius: 50%;
  }

</style>
<template>

    <Card @click.native="clicked()" :style="cardStyle">
        
        <div style="padding: 0px 15px;">
            <Badge :show-zero="showZero" :count="count" :type="type" style="width:100%;">
                <Icon :type="icon" size="45" class="text-center" style="display: block;"/>
                <p class="text-center" style="padding-top:5px;">{{ title }}</p>
            </Badge>
        </div>
        <!-- Animated checkmark  -->
        <div :class="showCheckMark ? 'animatedCheckmark' : ''">
            <animatedCheckmark v-if="checkMarkVisibility" :style="{ width: '30px', height: 'auto' }" :speed="true"></animatedCheckmark>
        </div>
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
            icon: {
                type: String,
                default: 'ios-briefcase-outline' 
            },
            type: {
                type: String,
                default: 'primary' 
            },
            route: {
                type: Object,
                default: null
            },
            showCheckMark: {
                type: Boolean,
                default: false
            },
            checkMarkVisibility: {
                type: Boolean,
                default: false
            },
        },
        components: { animatedCheckmark },
        methods: {
            clicked(){
                if(this.route){
                    this.$router.push(this.route);
                }

                this.$emit('clicked');
            }
        },
    };
    
</script>