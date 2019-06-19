<style scoped>
    .image_upload_poptip,
    .image_upload_poptip >>> .ivu-poptip-rel{
        width: 100% !important;
    }

    .image_upload_poptip >>> .ivu-upload{
        width: 100% !important;
    }

    .image-uploader{
        display: inline-block;
        width: 100% !important;
        height: auto !important;
        min-height:150px;
        text-align: center;
        line-height: 60px;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        margin-right: 4px;

        background-image: url(/images/assets/icons/star_loader.svg);
        background-repeat: no-repeat;
        background-position: center;
        background-size: 65px;
    }

    .highlight-line{
        border: 1px solid transparent;
        box-shadow: 0px 1px 4px rgba(0,0,0,.2);
    }

    .image-uploader img{
        width: 100%;
        height: 100%;
    }
    .image-uploader-cover{
        display: none;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,.6);
    }
    .image-uploader:hover .image-uploader-cover{
        display: block;
    }
    .image-uploader-cover i{
        color: #fff;
        font-size: 70px;
        cursor: pointer;
        margin: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .image-preview{
        max-width:180px;
        margin:auto;
    }

    .image-preview img{
        width:100% !important;
        height:auto !important;
        margin: auto;
        display: block;
    }

    .no-image-box{
        line-height: 135px;
        border: 1px dashed #e8eaec;
        text-align: center;
    }

</style>

<template>
    <div>
        
        <!-- Loader for when loading the docs -->
        <Loader v-if="isLoadingDocs" :loading="true" type="text" class="mt-2 mb-2">Loading...</Loader>
        <Loader v-if="isUploadingDocs" :loading="true" type="text" class="mt-2 mb-2">Uploading...</Loader>
    
        <div v-if="previewImage && !isLoadingDocs && !isUploadingDocs" class="image-preview">
            <img :src="previewImage" :style="{ width:'150px',  }">
        </div>

        <div v-if="!isLoadingDocs && !isUploadingDocs && uploadList.length"
             v-for="item in uploadList" :style="thumbnailStyle"
             :class="'image-uploader' + ( allowUpload ? ' highlight-line' : '')">
            <template v-if="item.url">
                <img :src="item.url">
                <div class="image-uploader-cover">
                    <Icon type="ios-eye-outline" @click.native="handleView(item)"></Icon>
                </div>
            </template>
            <template v-else>
                <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
            </template>
        </div>

        <Poptip v-show="allowUpload && !isLoadingDocs && !isUploadingDocs" word-wrap width="200" trigger="hover" :content="uploadMsg" class="image_upload_poptip">
            
            <Upload
                ref="upload"
                :show-upload-list="false"
                :on-success="handleSuccess"
                :format="['jpg','jpeg','png']"
                :max-size="2048"
                :on-format-error="handleFormatError"
                :on-exceeded-size="handleMaxSize"
                :before-upload="handleBeforeUpload"
                :multiple="multiple"
                type="drag"
                :data="postData"
                action="/api/upload"
                style="display: inline-block;width:58px;">
                
                <div v-if="uploadList.length || previewImage">
                    <span class="btn btn-link">Change Logo</span>
                </div>

                <div v-else style="line-height: 135px;">
                    <Icon type="ios-image-outline" size="70"></Icon>
                    <span class="">Add Logo</span>
                </div>
                
            </Upload>
        </Poptip>
        <div v-show="!allowUpload && !isLoadingDocs && !isUploadingDocs && !uploadList.length" class="no-image-box">
            <Icon type="ios-image-outline" size="70"></Icon>
            <span class="">No Image</span>
        </div>
        <Modal v-model="visible">
            <img :src="imgUrl" v-if="visible" style="width: 100%">
        </Modal>
    </div>
</template>
<script>


    /*  Loaders  */
    import Loader from './../loaders/Loader.vue';

    export default {
        components: { Loader },
        props:{
            docUrl: {
                type: String,
                default: null
            },
            postData: {
                type: Object,
                default: null
            },
            allowUpload: {
                type: Boolean,
                default: true
            },
            uploadMsg:{
                type: String,
                default: 'Upload Image'
            },
            multiple: {
                type: Boolean,
                default: false
            },
            thumbnailStyle: {
                type: Object,
                default: {}
            }
        },
        data () {
            return {
                imgName: '',
                imgUrl: '',
                visible: false,
                uploadList: [],
                isLoadingDocs: false,
                isUploadingDocs: false,
                previewImage: null,
            }
        },
        methods: {
            handleView (item) {
                this.imgName = item.name;
                this.imgUrl = item.url;
                this.visible = true;
            },
            handleRemove (file) {
                const fileList = this.$refs.upload.fileList;
                this.$refs.upload.fileList.splice(fileList.indexOf(file), 1);
            },
            handleSuccess (res, file) {
                //  Stop loader
                this.isUploadingDocs = false;

                //  If we don't allow multiple files, then clear the upload list
                if(!this.multiple){
                    this.uploadList = [];
                }
                
                //  Add the new file to the upload list
                this.uploadList.push(res);

                //  Remove preview
                this.removePreview();

                //  Notify success message
                this.$Notice.success({
                    title: 'Upload completed!'
                });

                this.$emit('updated', res);

            },
            handleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });

                //  Remove preview
                this.removePreview();
            },
            handleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });

                //  Remove preview
                this.removePreview();
            },
            handleBeforeUpload (file) {

                if( (this.postData || {}).modelId && (this.postData || {}).modelType ){

                    //  Start loader
                    this.isUploadingDocs = true;

                    //  Handle Validation
                    const check = this.uploadList.length < 5;

                    if (!check) {
                        this.$Notice.warning({
                            title: 'Up to five pictures can be uploaded.'
                        });
                    }

                    return check;

                }else{

                    //  Create a preview of the file
                    this.createPreview(file);

                    //  Emit the file
                    this.$emit('fileBeforeUpload', file);

                    //  Do not allow the upload to proceed
                    return false;

                }
            },
            createPreview(file){
                
                //  Make a preview image
                var self = this;
                var reader  = new FileReader();

                reader.addEventListener("load", function () {
                    self.previewImage = reader.result;
                }, false);

                reader.readAsDataURL(file);

            },
            removePreview(){

                //  Remove preview image
                this.previewImage = null;

            },
            getDocs() {
                
                if(this.docUrl){
                    
                    const self = this;

                    //  Start loader
                    self.isLoadingDocs = true;

                    console.log('Start getting docs...');

                    //  Additional data to eager load along with docs found
                    var connections = '';
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.docUrl + connections)
                            .then(({data}) => {
                                
                                console.log(data);

                                //  Stop loader
                                self.isLoadingDocs = false;

                                self.uploadList = data;
                                
                            })         
                            .catch(response => { 

                                //  Stop loader
                                self.isLoadingDocs = false;

                                console.log('imageUploader.vue - Error getting docs...');
                                console.log(response);    
                            });
                }
            },

        },
        mounted () {
            this.getDocs();
            this.uploadList = this.$refs.upload.fileList;
            
        }
    }
</script>