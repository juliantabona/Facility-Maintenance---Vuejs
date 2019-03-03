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
            }
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