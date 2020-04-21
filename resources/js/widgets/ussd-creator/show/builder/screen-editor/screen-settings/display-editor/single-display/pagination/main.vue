<template>

    <div>
        
        <!-- Display Pagination Explainer -->
        <Alert type="info" style="line-height: 1.4em;" class="mb-2" closable>
            Use <span class="font-italic text-success font-weight-bold">Pagination</span> to limit your screen content and allow
            users to <span class="font-italic text-success font-weight-bold">Scroll Up</span> or 
            <span class="font-italic text-success font-weight-bold">Scroll Down</span>
        </Alert>

        <!-- Pagination Manager -->
        <div class="mb-2">

            <span class="d-block font-weight-bold text-dark">Pagination: </span>

            <div class="bg-grey-light border mt-2 mb-3 pt-3 px-2 pb-2">
                
                <paginationManager :paginations="display.content.pagination"></paginationManager>

            </div>

        </div>

    </div>
    
</template>

<script>

    //  Get the pagination manager
    import paginationManager from './paginationManager.vue';

    export default {
        props: { 
            display: {
                type: Object,
                default:() => {}
            },
            screen: {
                type: Object,
                default:() => {}
            },
            screens: {
                type: Array,
                default: () => []
            }
        },
        components: { 
            paginationManager  
        },
        data(){
            return {
                localDisplay: this.display
            }
        }, 
        computed: {

        },
        methods: {

        }
    };
  
</script>