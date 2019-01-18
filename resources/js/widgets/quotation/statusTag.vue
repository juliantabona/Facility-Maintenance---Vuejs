<template>

    <span v-if="quotation.expiry_date_value">
        <Poptip word-wrap width="200" trigger="hover" :content="status.description">
            <Tag :style="{ 
                width: '70px',
                background: status.color + '10 !important',
                border: '1px solid '+status.color + ' !important'}">
                <span :style="{ color: status.color }">{{ status.text }}</span>
            </Tag>
        </Poptip>
    </span>

</template>
<script type="text/javascript">

    import moment from 'moment';

    export default {
        props:{
            quotation: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                status: {
                    text: '',
                    description: '',
                    color: ''
                }
            }
        },
        methods: {
            determineStatus() {

                if(this.quotation.expiry_date_value){

                    var date = moment(this.quotation.expiry_date_value);
                    var now = moment();

                    if (now > date) {
                        // date is past, therefore quotation has expired
                        this.status.description = 'This quotation has exceeded its period of validity';
                        this.status.text = 'Expired';
                        this.status.color = '#ed4014';
                    } else {
                        // date is future, therefore quotation has not expired
                        this.status.description = 'Quotation is approved';
                        this.status.text = 'Approved';
                        this.status.color = '#19be6b';
                    }

                }
                    
            }
        },
        created() {
            this.determineStatus();
        }
    }
</script>
