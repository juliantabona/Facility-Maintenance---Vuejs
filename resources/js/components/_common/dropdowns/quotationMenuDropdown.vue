<style scoped>

    .menu-border{
        border: 1px solid #2d8cf080;
        border-radius: 50%;
        padding: 2px 5px;
        height: 25px;
        display: inline-block;
    }

</style>

<template>

    <Dropdown class="menu-border" trigger="click" placement="bottom-end">
        <a href="javascript:void(0)">
            <Icon type="md-more" :size="16"></Icon>
        </a>
        <DropdownMenu slot="list" :style="{ width: '150px' }">
            <DropdownItem v-if="!localEditMode" @click.native="$emit('toggleEditMode', true)">Edit</DropdownItem>
            <DropdownItem v-if="localEditMode" @click.native="$emit('toggleEditMode', false)">View</DropdownItem>
            <hr>
            <DropdownItem @click.native="downloadPDF()">Export As PDF</DropdownItem>
            <DropdownItem>Get Share Link</DropdownItem>
            <DropdownItem @click.native="downloadPDF({ print: true })">Print Quotation</DropdownItem>
            <hr>
            <DropdownItem>Duplicate</DropdownItem>
            <DropdownItem>Customize</DropdownItem>
            <hr>
            <DropdownItem>Delete</DropdownItem>
        </DropdownMenu>
    </Dropdown>

</template>
<script type="text/javascript">

    export default {
        props: {
            quotationId: {
                type: Number,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        components: { },
        data() {
            return {
                localQuotationId: this.quotationId,
                localEditMode: this.editMode
            }
        },
        watch: {

            //  Watch for changes on the quotation id
            quotationId: {
                handler: function (val, oldVal) {
                    this.localQuotationId = val;
                }
            },
        
            //  Watch for changes on the edit mode value
            editMode: {
                handler: function (val, oldVal) {
                    this.localEditMode = val;
                }
            }

        },
        methods: {
            downloadPDF(download = { preview: false, print: false }){
                if(this.localQuotationId){
                    var action = (download.preview ? '?preview=1' : '') + (download.print ? '?print=1' : '');
                    let routeData = this.$router.resolve({
                            path: '/api/download/quotations/'+this.localQuotationId + action
                        });

                    window.open(routeData.href.replace("#", ""), '_blank');
                }
            }            
        },
        created () {
            
        }
    }
</script>
