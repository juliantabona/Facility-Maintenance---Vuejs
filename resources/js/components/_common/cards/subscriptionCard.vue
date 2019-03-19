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

  .subscription-card-item >>> .ivu-card-body{
      padding:5px;
  }

</style>
<template>

    <Card @click.native="clicked()" :style="cardStyle" class="subscription-card-item">
        
        <div>
            <Badge :show-zero="showZero" :count="count" :type="type" style="width:100%;">
                <img :src="imgSrc" class="text-center w-100" style="display: block;"/>
                <h5 class="text-center" :style="{ padding: '5px', background: '#4a85c5', color: '#fff' }">{{ title }}</h5>
                <p :style="{ fontSize: '12px' , padding: '5px' }">{{ description }}</p>

                <!-- Action button -->
                <basicButton  class="float-right pt-2 ml-4" :style="{ position:'relative' }"
                              type="success" size="small" 
                              :ripple="ripple"
                              @click.native="saveInvoice()">
                    {{ btnName }}
                </basicButton>

            </Badge>
        </div>
    </Card>

</template>

<script>

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

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
            description:{
                type: String,
                default: 'Short description'   
            },
            btnName:{
                type: String,
                default: 'Button'   
            },
            imgSrc: {
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
            ripple: {
                type: Boolean,
                default: false
            }
        },
        components: { basicButton },
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