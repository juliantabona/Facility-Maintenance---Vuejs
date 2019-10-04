<style scoped>

  .nofify-icon{
    background: #f9f9f9;
    border-radius: 50%;
    width: 35px;
    padding: 6px 7px;
    border: 1px solid #e6e6e6;
  }

  .wordwrap { 
    line-height: 1.4em;
    white-space: pre-wrap;      /* CSS3 */   
    white-space: -moz-pre-wrap; /* Firefox */    
    white-space: -pre-wrap;     /* Opera <7 */   
    white-space: -o-pre-wrap;   /* Opera 7 */    
    word-wrap: break-word;      /* IE */
  }

</style>

<template>

    <Row v-if="belongsTo(userNotifications)">
        <Col :span="4">
            <Icon class="nofify-icon" type="ios-person-outline" :size="20"/>
        </Col>
        <Col :span="20">
        <Row>
            <Col :span="24"> 
                <span v-if="notificationType == 'UserUpdated'" class="wordwrap text-capitalize">Profile changes <b>updated</b> successfully for <router-link :to="{ name: 'show-user', params: { id: notification.data.id }}">{{ notification.data.first_name }} {{ notification.data.last_name }}</router-link></span>  
            </Col>
                
            <Col :span="24">
                <small class="mt-2 float-right">
                    <span class="float-right">09:43AM</span>
                    <Icon type="md-time" :size="15" class="float-right" :style="{ margin: '-2px 3px 0 3px' }"/> 
                    <span class="float-right">22 Jan 2019</span>
                </small>
            </Col>
        </Row>
        </Col>
    </Row>

    <Row v-else-if="belongsTo(invoiceNotifications)">
        <Col :span="4">
            <Icon class="nofify-icon" type="ios-cash-outline" :size="20"/>
        </Col>
        <Col :span="20">
        <Row>
            <Col :span="24">
                <span v-if="notificationType == 'InvoiceCreated'" class="wordwrap text-capitalize">
                    <router-link :to="{ name: 'show-invoice', params: { id: notification.id }}">Invoice #{{ notification.data.reference_no_value }}</router-link> <b>created</b> for <router-link :to="{ name: 'show-client', params: { id: notification.data.customized_customer_details.id }}">{{ notification.data.customized_customer_details.name }}</router-link> by <a href="#">Julian Tabona</a>
                </span>  
                <span v-if="notificationType == 'InvoiceApproved'" class="wordwrap text-capitalize">
                    <router-link :to="{ name: 'show-invoice', params: { id: notification.id }}">Invoice #{{ notification.data.reference_no_value }}</router-link> <b>approved</b> for <router-link :to="{ name: 'show-client', params: { id: notification.data.customized_customer_details.id }}">{{ notification.data.customized_customer_details.name }}</router-link> by <a href="#">Julian Tabona</a>
                </span>  
                <span v-if="notificationType == 'InvoiceUpdated'" class="wordwrap text-capitalize">
                    <router-link :to="{ name: 'show-invoice', params: { id: notification.id }}">Invoice #{{ notification.data.reference_no_value }}</router-link> <b>updated</b> for <router-link :to="{ name: 'show-client', params: { id: notification.data.customized_customer_details.id }}">{{ notification.data.customized_customer_details.name }}</router-link> by <a href="#">Julian Tabona</a>
                </span>  
                <span v-if="notificationType == 'InvoiceSent'" class="wordwrap text-capitalize">
                    <router-link :to="{ name: 'show-invoice', params: { id: notification.id }}">Invoice #{{ notification.data.reference_no_value }}</router-link> <b>sent</b> for <router-link :to="{ name: 'show-client', params: { id: notification.data.customized_customer_details.id }}">{{ notification.data.customized_customer_details.name }}</router-link> by <a href="#">Julian Tabona</a>
                </span>  
                <span v-if="notificationType == 'InvoicePaid'" class="wordwrap text-capitalize">
                    <router-link :to="{ name: 'show-invoice', params: { id: notification.id }}">Invoice #{{ notification.data.reference_no_value }}</router-link> <b>paid</b> for <router-link :to="{ name: 'show-client', params: { id: notification.data.customized_customer_details.id }}">{{ notification.data.customized_customer_details.name }}</router-link> by <a href="#">Julian Tabona</a>
                </span>  
                <span v-if="notificationType == 'InvoicePaymentCancelled'" class="wordwrap text-capitalize">
                    <router-link :to="{ name: 'show-invoice', params: { id: notification.id }}">Invoice #{{ notification.data.reference_no_value }}</router-link> <b>payment cancelled</b> for <router-link :to="{ name: 'show-client', params: { id: notification.data.customized_customer_details.id }}">{{ notification.data.customized_customer_details.name }}</router-link> by <a href="#">Julian Tabona</a>
                </span>  
            </Col>
                
            <Col :span="24">
                <small class="mt-2 float-right">
                    <span class="float-right">09:43AM</span>
                    <Icon type="md-time" :size="15" class="float-right" :style="{ margin: '-2px 3px 0 3px' }"/> 
                    <span class="float-right">22 Jan 2019</span>
                </small>
            </Col>
        </Row>
        </Col>
    </Row>

</template>

<script>

  export default {
    props:{
      notification: {
        default: null
      }
    },
    data() {
      return {
          userNotifications : ['UserUpdated'],
          invoiceNotifications : ['InvoiceCreated', 'InvoiceApproved', 'InvoiceUpdated', 'InvoiceSent', 'InvoicePaid', 'InvoicePaymentCancelled']
      }
    },
    computed:{
        notificationType: function(){
            //  Get the notification type
            return this.notification.type.split('\\').pop();
        }
    },
    methods: {
        belongsTo: function(notificationGroup){
            //  Does the notification type exist in the notification group
            return notificationGroup.includes(this.notificationType);
        }
    } 
  };
</script>