<style>
    .image-uploader{
        display: inline-block;
        width: 60px;
        height: 60px;
        text-align: center;
        line-height: 60px;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        margin-right: 4px;
    }

    .highlight-line{
        border: 1px solid transparent;
        box-shadow: 0 1px 1px rgba(0,0,0,.2);
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
        font-size: 20px;
        cursor: pointer;
        margin: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

</style>

<template>
    <div>
        <div :class="'image-uploader' + ( allowUpload ? ' highlight-line' : '')" v-for="item in uploadList" :style="thumbnailStyle">
            <template v-if="item.status === 'finished'">
                <img :src="item.url">
                <div class="image-uploader-cover">
                    <Icon type="ios-eye-outline" @click.native="handleView(item)"></Icon>
                </div>
            </template>
            <template v-else>
                <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
            </template>
        </div>
        <Poptip v-show="allowUpload" word-wrap width="200" trigger="hover" :content="uploadMsg">
            
            <Upload
                ref="upload"
                :show-upload-list="false"
                :default-file-list="defaultList"
                :on-success="handleSuccess"
                :format="['jpg','jpeg','png']"
                :max-size="2048"
                :on-format-error="handleFormatError"
                :on-exceeded-size="handleMaxSize"
                :before-upload="handleBeforeUpload"
                :multiple="multiple"
                type="drag"
                action="//jsonplaceholder.typicode.com/posts/"
                style="display: inline-block;width:58px;">
                
                <div style="width: 58px;height:58px;line-height: 58px;">
                    <Icon type="md-images" size="20"></Icon>
                </div>
                
            </Upload>
        </Poptip>
        <Modal title="View Image" v-model="visible">
            <img :src="imgUrl" v-if="visible" style="width: 100%">
        </Modal>
    </div>
</template>
<script>
    export default {
        props:{
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
            imageList: {
                type: Array,
                default: []
            },
            thumbnailStyle: {
                type: Object,
                default: {}
            },
        },
        data () {
            return {
                defaultList: this.imageList,
                imgName: '',
                imgUrl: '',
                visible: false,
                uploadList: []
            }
        },
        watch: {
            allowUpload: function (newValue) {

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
                file.url = 'https://o5wwk8baw.qnssl.com/7eb99afb9d5f317c912f08b5212fd69a/avatar';
                file.name = '7eb99afb9d5f317c912f08b5212fd69a';
            },
            handleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });
            },
            handleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });
            },
            handleBeforeUpload () {
                const check = this.uploadList.length < 5;
                if (!check) {
                    this.$Notice.warning({
                        title: 'Up to five pictures can be uploaded.'
                    });
                }
                return check;
            }
        },
        mounted () {
            this.uploadList = this.$refs.upload.fileList;
            
        }
    }
</script>