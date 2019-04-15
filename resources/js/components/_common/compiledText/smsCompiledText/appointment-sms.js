import moment from 'moment';

function truncate(string, limit){
    return (string.length > limit) ? string.substring(0, limit - 3)+'...' : string;
}

export default {
    buildSms(appointment){

        var ref_no = appointment.id;
        var subject = appointment.subject;
        var agenda = appointment.agenda;
        var start_date = moment(appointment.start_date).format('H:mmA DD MMM YYYY');
        var end_date = moment(appointment.end_date).format('H:mmA DD MMM YYYY');
        var location = appointment.location;
        var company = ((appointment || {}).company || {});
        var client = ((appointment || {}).client || {});

        var characterLimit = 160;

        //  Company info text limit = 23
        var company_name = truncate(company.name.trim(), 21) + ( company.name.length <= 21 ? ':' : '' );       //  Optimum Quality: 
        //  Reference text limit = No limit (est. 16)
        var ref_no = 'Appt #'+ref_no;                                       //  Appt #xxxxxxxxxxx e.g) Appt #10000000000
        //  Start date text limit = No limit (est. 21)
        var start_date = ' @ '+start_date;                                  //  at 08:30AM 15 Feb 2018
        //  Location text limit = No limit (est. 21)
        var location_text = '.Address: '+location;                          //  .Address:Commerce Park unit 2,1st floor,office 3
        //  Reply for payment text limit = No limit (est. 32)
        var reply_with = '.Reply YES to confirm, NO to reschedule';         //  .Reply YES to confirm, NO to reschedule

        //  Subject text limit = Remaining characters left
        var charLeft = (characterLimit - (company_name+ref_no+start_date+location_text+reply_with).length);
        var subject_text = truncate(' '+subject, charLeft);        //  Adjustment of braces and whitening of teeth

        var message = company_name+ref_no+subject_text+start_date+location_text+reply_with;

        /*  
         *  EXAMPLE COMPILED TEXT
         * 
         **************************************************************************************************************
         *  Optimum Quality:Appointment #043 Adjustment of braces and whitening of teeth at 08:30AM 15 Feb 2018.      *
         *  Address:Commerce Park unit 2,1st floor,office 3.Reply YES to confirm, NO to reschedule                    *
         **************************************************************************************************************
         */

        //  Return the compiled message
        return message;

    }
}