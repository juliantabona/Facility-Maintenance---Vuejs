<template>

    <div>

        <!-- Transaction Heading / Add Transaction Button -->
        <Row class="border-bottom pb-3">

            <Col :span="24">

                <!-- Transaction Heading -->
                <h2 class="font-weight-bold pl-4">Transactions</h2>

            </Col>

        </Row>

        <!-- Transaction Table List With Search Input, Filters & Sort Functionality -->
        <Row>

            <Col :span="24">

                <!-- Transaction Table Widget -->
                <transactionTableWidget :transactionsUrl="transactionsUrl"></transactionTableWidget>

            </Col>

        </Row>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue'; 
    
    /*  Active Transaction Widget  */
    import showActiveTransactionWidget from './../show/main.vue';

    /*  Local Widgets  */
    import transactionTableWidget from './table.vue'

    export default {
        props: {
            transactionsUrl: {
                type: String,
                default: null
            }
        }, 
        components: { 
            basicButton, showActiveTransactionWidget, transactionTableWidget
        },
        data(){
            return {
                //  Current store transactions
                localTransactions: []
            }
        }
    };
  
</script>