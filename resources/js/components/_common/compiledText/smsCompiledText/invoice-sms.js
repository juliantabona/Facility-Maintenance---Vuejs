import moment from 'moment';

function formatPrice(money, symbol) {
    let val = (money/1).toFixed(2).replace(',', '.');
    return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function truncate(string, limit){
    return (string.length > limit) ? string.substring(0, limit - 3)+'...' : string;
}

export default {
    buildSms(invoice){

        var referenceNo = invoice.reference_no_value;
        var items = '';
        var currency = (((invoice || {}).currency_type || {}).currency || {}).symbol || '';
        var grand_total = formatPrice( (invoice.grand_total || 0), currency);
        var expiry_date = moment(invoice.expiry_date).format('MMM DD YYYY');
        var company = ((invoice || {}).customized_company_details || {});
    
        for( var x = 0; x < invoice.items.length; x++  ){
            x == 0 ? items += '' : items +=' ';
            items += ( (invoice.items[x].quantity) +'x '+(invoice.items[x].name) );
        }
    
        var characterLimit = 160;
        //  Company info text limit = 23
        var companyName = truncate(company.name.trim(), 21) + ( company.name.length <= 21 ? ':' : '' );       //  Optimum Quality: 
        //  Reference text limit = 16
        var reference = 'Invoice #'+referenceNo;                        //  Invoice #002
        //  Amount text limit = 20
        var amount = 'Amount ' + grand_total;                           //  Amount P350.00
        //  Due date text limit = 21
        var dueDate = ' due '+expiry_date;                              //  due on 15 Feb 2018
        //  Reply for payment text limit = 32
        var replyWith = '.Reply with '+referenceNo+'#<pin> to pay';     //  Reply with 002#<pin> to pay
        
        //  items text limit = Remaining characters left
        var charLeft = (characterLimit - (companyName+reference+amount+dueDate+replyWith).length);
        var items = truncate(' for ' + items + ( items.length <= charLeft ? '.' : '' ) , charLeft);    //  for 1x Basic Website, 1x Web Hosting, 5x Emails. 
    
        var message = companyName+reference+items+amount+dueDate+replyWith;
    
        //  Return the compiled message
        return message;
    }
}

