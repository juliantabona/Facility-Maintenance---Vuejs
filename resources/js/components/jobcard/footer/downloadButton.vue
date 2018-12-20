<template>
    <Button type="default" @click="showOptions">
        <Icon type="ios-download-outline" :size="18" :style="{ marginTop:'-3px' }"/>
        Download

        <!-- 
            MODAL TO UPDATE LIFECYCLE STATUS INFORMATION
        -->
        <downloadOptionsModal
            v-show="isOpen" 
            :show="isOpen"
            @closed="closeModal"
            @download="downloadFile">
        </downloadOptionsModal>

    </Button>
</template>
<script type="text/javascript">

    import downloadOptionsModal from './downloadOptionsModal.vue';

    export default {
        components: { downloadOptionsModal },
        props: {
            jobcardId: {
                type: Number
            }
        },
        data(){
            return {
                isOpen: false
            }
        },
        methods: {
            showOptions(){
                this.isOpen = true;
            },
            closeModal(){
                this.isOpen = false;
            },
            downloadFile(downlodableSections){
                let routeData = this.$router.resolve({
                        path: '/api/downloadFile', 
                        query: { 
                            id: this.jobcardId, 
                            model: 'jobcard', 
                            options: JSON.stringify(downlodableSections)  
                        }
                    });

                window.open(routeData.href.replace("#", ""), '_blank');
                
                this.closeModal();
            }
        }
    }
</script>
