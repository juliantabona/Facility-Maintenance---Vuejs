function ussd_creator_instructions_sample_code()
{

    return `<?php

    /** Use this editor to write custom PHP code. Always use the return statement
     *	when you want to output your information. Below is an example: 
     */

    $variable = "dynamic";

    return 'Example using ' . $variable . ' screen information';

?>`    

};

function ussd_creator_custom_formatting_sample_code()
{

    return `<?php

    /** Use this editor to write custom php code to format the users input. Remember 
     *	that the users input must be referenced using the $input variable. Always use 
     *	the return statement when you want to output your information. 
     *	Below is an example: 
     */

    // Make the users input lowercase using the PHP method strtolower()
    return strtolower( $input );

?>`
};

export default {
    'ussd_creator_instructions_sample_code': ussd_creator_instructions_sample_code(),
    'ussd_creator_custom_formatting_sample_code': ussd_creator_custom_formatting_sample_code()
}

