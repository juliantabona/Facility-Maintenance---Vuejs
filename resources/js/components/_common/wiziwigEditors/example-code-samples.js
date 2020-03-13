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

function ussd_creator_select_options_action_sample_code()
{

    return `<?php

    /** Use this editor to write custom php code to create dynamic options.  
     *	Remember that you can reference dynamic content using mustache tags 
     *  such as {{ products }} or PHP variables $products. Always use the
     *	return statement when you want to output your information. 
     *	Below is an example: 
     */

    //  Our products
    $products = [
        ["id"=>"1", "name"=>"Product 1"],
        ["id"=>"2", "name"=>"Product 2"],
        ["id"=>"3", "name"=>"Product 3"]
    ];

    // We will store all our options inside an empty array called "options"
    $options = [];

    // Let items be an array of the dynamic information we want to list e.g "products"
    $items = $products;  // $items = {{ products }};

    /** Foreach item we will build the option. Each option must have four
     *  attributes:
     * 
     *  @name: The option display name
     *  @value: The option value that must be stored as dynamic data
     *  @next_screen: The name of the screen to link to after option selection
     *  @input: A number, letter or symbol the user must input to select the option
     */
    foreach($items as $key => $item){

        //  Every option must have the following structure
        $option = [
            'input' => ++$key,
            'value' => $item['id'],
            'name' => $item['name'],
            'next_screen' => 'View Product'
        ];

        //  Add each option to our options list
        array_push($options, $option);

    }

    //  Return all our options
    return $options;

?>`
};

export default {
    'ussd_creator_instructions_sample_code': ussd_creator_instructions_sample_code(),
    'ussd_creator_custom_formatting_sample_code': ussd_creator_custom_formatting_sample_code(),
    'ussd_creator_select_options_action_sample_code': ussd_creator_select_options_action_sample_code()
}

