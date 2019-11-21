<template>

    <!-- Drawer -->
    <Drawer 
        :mask="mask"
        :title="title"
        :width="width"
        :styles="styles"
        :closable="closable"
        :transfer="transfer"
        :placement="placement"
        :mask-style="maskStyle"
        :scrollable="scrollable"
        v-model="localShowDrawer"
        :mask-closable="maskClosable"
        @on-visible-change="handleChange($event)">

        <!--  Place the content here  -->
        <slot></slot>

    </Drawer> 
        
</template>
<script>

    /*  Buttons  */
    import basicButton from './../buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../loaders/Loader.vue';  

    /*  Modal Structure  */
    import mainModal from './main.vue';

    export default {
        props:{

            /*  Display Drawer or not  */
            showDrawer: {
                type: Boolean,
                default: false
            },
            /*  The title of Drawer. It is invalid 
             *   if header slot is set.
             */
            title: {
                type: String,
                default: '',
            },
            /*  The width of Drawer. It will be displayed as a 
             *  percentage when the value less than 100, 
             *  otherwise it is a pixel.
             */
            width:{
                type: String,
                default: '500',
            },
            /*  Display the close button at the right 
             *  top corner or not.
             */
            closable: {
                type: Boolean,   
                default: true,           
            },
            /*  Allow mask-closing operation (Click mask layer 
             *  around the Drawer to close it) or not.
             */
            maskClosable:{
                type: Boolean,
                default: true,
            },
            /*  Whether to display the mask layer. 
             *  (The dark overlay background).
             */
            mask:{
                type: Boolean,
                default: true,
            },
            /*  The custom style of mask.
             */
            maskStyle:{
                type: Object,
                default: null,
            },
            /*  The custom style of container.
             */
            styles:{
                type: Object,
                default: null,
            },
            /*  Allow scrolling or not.
             */
            scrollable:{
                type: Boolean,
                default: false,
            },
            /*  The placement of the Drawer, options include left, right.
             */
            placement:{
                type: String,
                default: 'right',
            },
            /*  Whether to append the layer in body.
             */
            transfer:{
                type: Boolean,
                default: true,
            },
            okText:{
                type: String,   
                default: null,           
            },
            cancelText:{
                type: String,   
                default: null,             
            }
        },
        components: { basicButton, Loader, mainModal  },
        data(){
            return{

                /*  Display Drawer or not. Use v-model to enable two-way binding.  */
                localShowDrawer: this.showDrawer

            }
        },
        watch: {
            /*  Keep track of changes on the showDrawer.  */
            showDrawer: {

                handler: function (val, oldVal) {

                    /*  Update the localShowDrawer  */
                    this.localShowDrawer = val;

                },
                deep: true

            }
        },
        methods: {
            handleChange(visibility){
            
                /*  Send the current visibility status of the drawer (true/false) to the parent
                 *  component so that it is awareof whether the drawer is visibile or not 
                 */
                this.$emit('visibility', visibility);

            }
        }
    }
</script>