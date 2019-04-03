import moment from 'moment';

function truncate(string, limit){
    return (string.length > limit) ? string.substring(0, limit - 3)+'...' : string;
}

module.exports.buildSms = function(appointment){

    var subject = appointment.subject;
    var agenda = appointment.agenda;
    var start_date = moment(appointment.start_date).format('MMM DD YYYY');

    return 'Subject: ' + subject+', details: ' + agenda +' on ' + start_date;
}