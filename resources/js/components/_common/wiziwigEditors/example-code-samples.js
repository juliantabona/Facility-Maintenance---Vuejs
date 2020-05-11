function ussd_service_instructions_sample_code()
{

    return `<?php

    /** Use this editor to write custom PHP code. Always use the return statement
     *	when you want to output your information. Below is an example: 
     */

    $variable = "dynamic";

    return 'Example using ' . $variable . ' screen information';

?>`    

};

function ussd_service_custom_formatting_sample_code()
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

function ussd_service_select_options_action_sample_code()
{

    return `<?php

    /** Use this editor to write custom php code to create dynamic options.  
     *	Remember that you can reference dynamic content using mustache tags 
     *  such as {{ products }} or PHP variables $products. Always use the
     *	return statement when you want to output your information. 
     *	Below is an example: 
     */

    //  Our products (Usually dynamic information e.g From an API call)
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
     *  main attributes:
     * 
     *  @name: The option display name
     *  @value: The option value that must be stored as dynamic data
     *  @link: [
     *     @type: Whether you want to link to a "screen" or "display"
     *     @name: The name of the screen or display to link to after option selection
     *   ]
     *  @input: A number, letter or symbol the user must input to select the option
     *
     *	---	Optional Parameters ---
     *  @separator: [
     *     @top: Characters to add above to separate the option from other options e.g '---'
     *     @bottom: Characters to add below to separate the option from other options e.g '---'
     *   ]
     *
     */
    foreach($items as $key => $item){

        //  Every option must have the following structure
        $option = [
            'input' => ++$key,
            'value' => $item['id'],
            'name' => $item['name'],
            'link' => [
                'type' => 'screen',         //  or "display"
                'name' => 'View Product'
            ],
            'separator' => [
                'top' => '',
                'bottom' => ''
            ]
        ];

        //  Add each option to our options list
        array_push($options, $option);

    }

    //  Return all our options
    return $options;

?>`
};

function ussd_service_local_storage_sample_code()
{

    return `<?php

    /** Use this editor to write custom php code to store static and dynamic
     *  data on Local Storage. Remember that you can reference dynamic content 
     *  using mustache tags such as {{ user.name }} or by using PHP variables 
     *  $user->name. Always use the return statement when you want to output 
     *  your information.
     */

    /** ARRAY STORAGE 
     * 
     *  If you are using the Code Editor with the Local Storage "Mode" set to 
     *  "Array Replace", "Array Append" or "Array Prepend" then you must 
     *  always return an array for proper storage. Below is an example: 
     */

    $first_name = 'John';
    $last_name = 'Doe';
    $age = '24';

    /** Example 1:
     * 
     *  We want to store the data provided by the user into an array as values
     */
    return [$first_name, $last_name, $age]; //  or [{{ first_name }}, {{ last_name }}, {{ age }}];


    /** Example 2:
     *
     *  We want to store the data provided by the user into an array as key/values
     */
    return [
        'first_name' => $first_name, 
        'last_name' => $last_name, 
        'age' => $age
    ]; 

    /** STRING STORAGE 
     * 
     *  If you are using the Code Editor with the Local Storage Mode set to
     *  "String Replace" or "String Join (Concatenate)" then you must always
     *  return a string for proper storage. A good use of Local Storage using
     *  strings is creating templated information. Below is an example: 
     */

    /** Example 1:
     * 
     *  We want to store the data provided by the user into an array as values
     */
     return 'Hello, ' .$first_name. ' welcome back;

?>`
};

function ussd_service_revisit_sample_code()
{

    return `<?php

    /** Use this editor to write custom php code to add additional responses
     *  that should be run immediately after revisiting a particular screen.
     *  This can be helpful to automate certain behaviour on behalf of the
     *  end user. 
     * 
     *  Remember that you can reference dynamic content using mustache tags 
     *  such as {{ user.name }} or by using PHP variables $user->name. 
     *  Return the responses in order, separated with the * symbol.
     *  
     */

    /** Example 1 (without dynamic content):
     * 
     *  The following example means that after we get to the desired screen,
     *  we should enter "1", then "2" and then finally "3".
     */
    return '1*2*3';

    /** Example 2 (with dynamic content):
     * 
     *  The following example means that after we get to the desired screen,
     *  we should enter "1", then "{{order.number}}" and then "3" and finally
     *  "{{order.amount}}". Notice that {{order.number}} and {{order.amount}}
     *  are dynamic properties that will convert into their appropriate values
     *  e.g {{order.number}} = 00223 and {{order.amount}} = 450.00
     */
    return '1*{{order.number}}*3*{{order.amount}}';

?>`
};

export default {
    'ussd_service_instructions_sample_code': ussd_service_instructions_sample_code(),
    'ussd_service_custom_formatting_sample_code': ussd_service_custom_formatting_sample_code(),
    'ussd_service_select_options_action_sample_code': ussd_service_select_options_action_sample_code(),
    'ussd_service_local_storage_sample_code': ussd_service_local_storage_sample_code(),
    'ussd_service_revisit_sample_code': ussd_service_revisit_sample_code()
}

