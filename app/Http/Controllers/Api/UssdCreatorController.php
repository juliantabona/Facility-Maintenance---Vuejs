<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UssdCreatorController extends Controller
{
    private $text;
    private $level;
    private $screen;
    private $ussdBuilder;
    private $ussdInterface;
    private $dynamic_data_storage;

    public function __construct(Request $request)
    {
        //  Get the Ussd TEXT value (User Response)
        $this->text = $request->get('text');

        //  Get the Ussd Interface
        $this->ussdInterface = \App\UssdInterface::find(59);

        //  Get the Ussd builder data
        $this->ussdBuilder = $this->ussdInterface->metadata;

        $this->level = 1;

        $this->dynamic_data_storage = [];
    }

    public function home()
    {
        $this->manageGoBackRequests();

        $response = $this->startBuildingUssd();

        /*
        $response .= "\n" . '_______________________________' ;
        $response .= "\n" . 'TEXT = ' . $this->text;
        $response .= "\n" . '_______________________________';
        $response .= "\n" . 'DYNAMIC = ' . json_encode($this->dynamic_data_storage);
        */

        return response($response)->header('Content-Type', 'text/plain');
    }

    public function startBuildingUssd()
    {
        //  Check if the builder is available and ready for use
        if ($this->builderIsReady()) {
            //  Start building and displaying the ussd screens
            return $this->setupUssdScreens();
        } else {
            //  Display a custom error to notify the user of the issue
            return $this->displayCustomErrorPage('Sorry we cannot build the USSD Application. This is because we could not find any data to build');
        }
    }

    /*  builderIsReady()
     *  This method checks if we have the data we need in order to build the
     *  ussd application. Incase we do not have the data, we must immediately
     *  stop any future operations and notify the user of the issue.
     */
    public function builderIsReady()
    {
        //  If we have any builder data return true otherwise false
        return !empty($this->ussdBuilder) ? true : false;
    }

    /*  setupUssdScreens()
     *  This method uses the ussd builder data to start building the actual
     *  display screens that must be returned.
     */
    public function setupUssdScreens()
    {
        //  First we make sure that the builder data is an array at its top level
        if (is_array($this->ussdBuilder)) {
            $response = $this->determineScreenToDisplay();
        } else {
            //  Display a custom error to notify the user of the issue
            $response = $this->displayCustomErrorPage('Sorry we cannot build the USSD Application. This is because the data structure is incorrect');
        }

        return $response;
    }

    public function determineScreenToDisplay()
    {
        //  If we are displaying the first screen (i.e we haven't displayed anything yet)
        if ($this->level == 1) {
            //  Select the first screen on the ussd builder by default
            $this->screen = $this->ussdBuilder[0];
        }

        //  Check if the screen data exists
        if (empty($this->screen)) {
            //  Display a custom error to notify the user of the issue
            return $this->displayCustomErrorPage('Sorry we cannot build the USSD Application. This is because the screen data was not found');
        }

        //  Build the current screen display and its store its reply attributes and data
        $response = $this->buildScreenDisplay();

        /* If the user responses are greater than the number of screens displayed, then we need
         *  to determine whether or not we can access the follow-up screens to match the user
         *  responses e.g:
         *
         *  At level (0) the number of user responses = 0 which means we display screen 1
         *  At level (1) the number of user responses = 1 which means we display screen 2 (Since we have screen 1 response)
         *  At level (2) the number of user responses = 2 which means we display screen 3 (Since we have screen 2 response)
         *
         */

        if ($this->completedLevel($this->level)) {
            //  If the screen uses dynamic data (e.g via an API)
            if ($this->screen['is_dynamic'] == true) {
            } else {
                $user_response = $this->getResponseFromLevel($this->level);   //  John Doe

                if ($this->screen['content']['reply_type'] == 'Select Option') {
                    $user_response = (int) $user_response;   //  e.g 1, 2 or 3 e.t.c
                    $available_screen_options = $this->screen['content']['select_reply']['static_options'] ?? [];
                    $number_of_available_screen_options = count($available_screen_options);

                    //  Make sure that the user did not select an incorrect option
                    if ($user_response > $number_of_available_screen_options || $user_response < 0) {
                        //  Display a custom error (with go back option) to notify the user of the issue
                        return $this->displayCustomGoBackPage("You selected an incorrect option. Please try again.\n");
                    }

                    $selected_option = $available_screen_options[$user_response - 1];

                    //  Get the current selected option value and set it as the dynamic data value
                    $dynamic_data_value = [
                        'input' => $user_response,
                        'name' => $selected_option['name'],
                        'value' => $selected_option['value'] ?? $selected_option['name'],
                    ] ?? null;  //  e.g Buy Tickets

                    //  Get the next screen link
                    $next_screen = $available_screen_options[$user_response - 1]['next_screen'] ?? null;  //  e.g Tickets
                }

                if ($this->screen['content']['reply_type'] == 'Input Value') {
                    //  Get the current input value and set it as the dynamic data value
                    $dynamic_data_value = $user_response;  //  e.g John Doe

                    //  Get the next screen link
                    $next_screen = $this->screen['content']['next_screen'] ?? null;  //  e.g register
                }

                if ($this->screen['content']['reply_type'] != 'No Action') {
                    //  Get the reply name (if available)
                    $reply_name = $this->screen['content']['reply_name'];   //  e.g first_name

                    //  If the reply name is not empty and the user reponse has been set
                    if (!empty($reply_name) && isset($dynamic_data_value) && !empty($dynamic_data_value)) {
                        //  Save the dynamic attribute and its associated value in the dynamic data storage
                        $this->dynamic_data_storage[$reply_name] = $dynamic_data_value;
                    }

                    //  If the reply name is not empty and the user reponse has been set
                    if (isset($next_screen) && !empty($next_screen)) {
                        foreach ($this->ussdBuilder as $screen) {
                            //  To avoid self infinite loops
                            if ($screen['title'] != $this->screen['title']) {
                                if ($screen['title'] == $next_screen) {
                                    //  Increment the level
                                    $this->level = ++$this->level;

                                    //  Set the global screen to the current screen that we are linking to
                                    $this->screen = $screen;

                                    //  Build the screen display
                                    $response = $this->determineScreenToDisplay();

                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $response;
    }

    public function buildScreenDisplay()
    {
        //  If the screen uses dynamic data (e.g via an API)
        if ($this->screen['is_dynamic'] == true) {
            //  If the screen does not use dynamic data (simple screen)
        } else {
            //  Build the screen information using the screen content
            $response = $this->buildScreenContent($this->screen['content']);
        }

        return $response;
    }

    public function buildScreenContent($content)
    {
        $description_uses_code_editor_mode = $content['description']['code_editor_mode'] ?? '';

        if( $description_uses_code_editor_mode ){

            //  Get the screen description code otherwise default to a return statement that returns an empty string
            $screen_text = $content['description']['code_editor_text'] ?? "return ''";

        }else{    

            //  Get the screen description text otherwise default to an empty string
            $screen_text = $content['description']['text'] ?? '';

        }

        //  Initialize the screen options to an empty string
        $screen_options = '';

        //  If the screen content must display select options
        if ($content['reply_type'] == 'Select Option') {
            //  Add a line break before showing the options
            $screen_options .= "\n";

            //  If the select options are dynamic
            if ($content['select_reply']['is_dynamic'] == true) {
                //  If the select options are static
            } else {
                //  Get the static options
                $options = $content['select_reply']['static_options'];

                foreach ($options as $key => $option) {
                    //  Set the option number
                    $number = (++$key).'. ';

                    //  Get the option value otherwise default to "No option"
                    $option = ($option['name'] ? $option['name'] : 'No option');

                    //  Add the current option to the rest of the screen options
                    $screen_options .= $number.$option."\n";     //  e.g 1. View Products
                }
            }
        }

        $response = $this->replaceTextWithDynamicData($screen_text, $description_uses_code_editor_mode).$screen_options;

        return $this->displayCustomPage($response);
    }

    public function replaceTextWithDynamicData($text = "''", $code_editor_mode = false)
    {
        //  Create dynamic variables
        foreach ($this->dynamic_data_storage as $key => $value) {
            /* If $data = ['product' => 'Orange', 'quantity' => 3, 'price' => 450, ...e.tc];
             *  Then we produce dynamic variables e.g
             *
             *  $product = 'Orange';
             *  $quantity = 3;
             *  $price = 450;
             *  ... e.t.c
             */

            /* Convert the value to a JSON Object. Converting each value into an object helps us
             *  target nested values by using the "->" symbol e.g we can access deeply nested
             *  values in this way:
             *
             *  $company->details->contacts->phone;
             *
             */
            ${$key} = json_decode(json_encode($value));
        }

        //  Remove the (\u00a0) special character which represents a no-break space in HTML
        $text = preg_replace('/\xc2\xa0/', '', $text);

        /** Detect Dynamic Variables
         *
         *  Pattern Meaning:.
         *
         *  [{]{2} = The string must have exactly 2 opening curly braces e.g {{ not that "{{{" or "({{" or "[{{" will also pass
         *
         *  [\s]* = The string may have zero or more occurences of spaces e.g "{{company" or "{{ company" or "{{   company"
         *
         *  [a-zA-Z_]{1} = The first character at this point must be a lowercase or uppercase alphabet or an underscrore (_)
         *                 e.g "{{ c" or "{{ company" or "{{ _company" but deny "{{ 123" or "{{ 123_company" e.t.c
         *
         *  [a-zA-Z0-9_\.]{0,} = After the first character the string may have zero or more occurances of lowercase or uppercase
         *             alphabets, numbers, underscores (_) and periods (.) e.g "{{ company_123" or "{{ company.name" e.t.c
         *
         *  [\s]* = The string may have zero or more occurences of spaces afterwards "{{ company" or "{{ company   " e.t.c
         *
         *  [}]{2} = The string must end with exactly 2 closing curly braces e.g }} not that "}}}" or "}})" or "}}]" will also pass
         */

        $pattern = "/[{]{2}[\s]*[a-zA-Z_]{1}[a-zA-Z0-9_\.]{0,}[\s]*[}]{2}/";

        $total_results = preg_match_all($pattern, $text , $results);

        /**
         * The "$total_results" represents the number of matched dynamic content.
         *
         * $total_results = 3;
         *
         * The "$results[0]" represents an array of the matched dynamic content
         *
         * $results[0] = [
         *      "{{ company.name }}",
         *      "{{ company.branches.total }}",
         *      "{{ company.details.contacts.phone }}",
         *      ... e.t.c
         *  ];
         */

        //  Foreach dynamic content matched/found
        foreach ($results[0] as $result) {
            //  Replace all curly braces and spaces with nothing e.g convert "{{ company.name }}" into "company.name"
            $formatted_result = preg_replace("/[{}\s]*/", '', $result);

            //  Replace one or more occurences of the period with "->" e.g convert "company.name" or "company..name" into "company->name"
            $formatted_result = preg_replace("/[\.]+/", '->', $formatted_result);

            //  Append the $ sign to the begining of the result e.g convert "company->name" into "$company->name"
            $formatted_result = '$'.$formatted_result;

            /* If the current text is not using code editor mode meaning that it does not
             *  want to process complex code e.g if-else statements, foreach statements
             *  and php methods such as trim(), strtolower(), ucwords() e.t.c
             */
            if (!$code_editor_mode) {
                try {
                    //  Convert the dynamic property into its dynamic value e.g "$company->name" into "Company XYZ"
                    $formatted_result = eval("return $formatted_result;");

                    //  Incase the dynamic value is not a string, integer or float
                    if (!is_string($formatted_result) && !is_integer($formatted_result) && !is_float($formatted_result)) {
                        //  Get the result type e.g Object, Array, Boolean e.t.c and wrap in square brackets
                        $formatted_result = '['.gettype($formatted_result).']';
                    }
                } catch (\Exception $e) {
                    //  Get the error message
                    $formatted_result = '*'.$e->getMessage().'*';

                    //  Log the error
                    //  ...
                }
            }

            //  Replace the dynamic variable with its dynamic content
            $text = preg_replace("/$result/", $formatted_result, $text);
        }

        /* If this text is using code editor mode then render the code and
         *  process if-else statements, foreach statements and php methods
         *  such as trim(), strtolower(), ucwords() e.t.c
         */
        if ($code_editor_mode) {
            try {

                //  Remove PHP Tags
                $text = trim( preg_replace("/<\?php|\?>/i", '', $text) );

                //  Process the code
                $text = eval("$text");

                //  Return the processed text
                return $text;
                
            } catch (Exception $e) {
                /* NOTE: For some reason we cannot catch the error so i decide to
                 *  to use the finally method so that when we hit a error we don't
                 *  stop the code execution instead we return the value of $text
                 *  before we tried to run the eval() method which caused errors
                 *
                 *  However ideally i want to catch the error exception and then
                 *  grab the exact error message so that we can embbed it into
                 *  the $text and show the user the actual error on the USSD
                 *  screen.
                 *
                 */

                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        } else {
            //  Return the processed text
            return $text;
        }
    }

    public function getUserResponses($text = null)
    {
        /*  The text variable represent the response from the user.
         *  To extract the users information we must explode the text
         *  to retrieve the users information concatenated using the *
         *  symbol over several interations.
         *
         *  $user_responses[0] = Response from screen 1 (Landing Page)
         *  $user_responses[1] = Response from screen 2
         *  e.t.c
         */

        $responses = explode('*', $text ?? $this->text);

        /*  Remove empty keys  */
        $responses = array_filter($responses, function ($value) {
            return !is_null($value) && $value !== '';
        });

        return array_values($responses);
    }

    public function countUserResponses()
    {
        return count($this->getUserResponses() ?? []);
    }

    public function getResponseFromLevel($levelNumber = null)
    {
        if ($levelNumber) {
            /*  Get all the user reponses.  */
            $user_responses = $this->getUserResponses();

            /*  We want to say if we have levelNumber = 1 we should get the landing page data
             *  (since thats level 1) but technically $user_responses[0] = landing page response.
             *  This means to get the response for the level we want we must decrement by one unit.
             */
            return isset($user_responses[$levelNumber - 1]) ? $user_responses[$levelNumber - 1] : null;
        }
    }

    public function completedLevel($levelNumber = null)
    {
        /*  If we have a level number  */
        if ($levelNumber) {
            /*  Check if we have a response for this level number  */
            $level = $this->getResponseFromLevel($levelNumber);

            /*  If the level specified is completed (Has a response from the user)  */
            return isset($level) && $level != '';
        }
    }

    /*  Scan and remove any responses the user indicated to omit. This is to help
     *  simulate the ability for the user to go back to previous screens so that
     *  they can choose another option. This will help the appllication to focus
     *  on the important responses knowing that any irrelevant response was
     *  already removed.
     */
    public function manageGoBackRequests()
    {
        /*  Get the user's response text value.
         */
        $text = $this->text;

        /*  Assuming the $text value is as follows:
         *
         *  1*001*002*003*0*0*0
         *
         *  We can explode it into an array of responses to get
         *
         *  ["1", "001", "002", "003", "0", "0", "0"]
         *
         */
        $responses = explode('*', $this->text);

        /*  Lets count how many times the zero (0) value appears
         *  from the responses we have.
         */
        $count = 0;

        foreach ($responses as $response) {
            if ($response == '0') {
                $count = ++$count;
            }
        }

        /*  Since we now know the number of times the value zero (0) appears on the
         *  user responses, we can loop through each instance knowing that we will
         *  find a zero (0) value. Lets assume we have the following responses
         *
         *  ["1", "001", "002", "003", "0", "0", "0"]
         *
         *  At this point our application can count the number of times the zero (0)
         *  value appears which is 2 times in the above example. This means we need
         *  to setup a looping function that will loop three times where for each
         *  loop we will locate the corresponding zero (0) value. Once any zero (0)
         *  value is located we will remove that zero (0) value and the immediate
         *  value that appears before that zero (0). In our example above we want
         *  that foreach time we loop we create a new loop that we go through all
         *  the response values trying to find the zero (0) value. once the value
         *  is located, we will remove it and then remove the value before. This
         *  is like we are cancelling or making that value non-existent. This will
         *  simulate the idea of going back since we cancel or remove the users
         *  previous response. So for instance in first loop, we will make a loop
         *  go through all the responses and locate a zero (0) and then remove it
         *  and the value before it, we will have the following result
         *
         *  ["1", "001", "002", "0", "0"]
         *
         *  Once we locate that zero value and remove it along with the previous
         *  value, we need to update a special array called $updated_responses
         *  with the new updated responses. After the first loop we have:
         *
         *  $updated_responses Before = ["1", "001", "002", "003", "0", "0", "0"]
         *  $updated_responses After  = ["1", "001", "002", "0", "0"]
         *
         *  On the second loop we have
         *
         *  $updated_responses Before = ["1", "001", "002", "0", "0"]
         *  $updated_responses After  = ["1", "001", "0"]
         *
         *  $updated_responses Before = ["1", "001", "0"]
         *  $updated_responses After  = ["1"]
         *
         *  In the end the result will be:
         *
         *  $updated_responses After = ["1"]
         *
         *  This makes sense because we started with three zero (0) values. Each
         *  zero (0) value was meant to cancel out each previous response thereby
         *  simulating a go back functionality
         *
         */

        $updated_responses = $responses;

        for ($x = 0; $x < $count; ++$x) {
            for ($y = 0; $y < count($updated_responses); ++$y) {
                if ($updated_responses[$y] == '0') {
                    unset($updated_responses[$y]);

                    if (isset($updated_responses[$y - 1])) {
                        unset($updated_responses[$y - 1]);
                    }

                    $updated_responses = array_values($updated_responses);

                    break;
                }
            }
        }

        /*  Now since we have updated the responses, we need to update the
         *  actual text value so that future methods and functions can use
         *  the updated text responses without any zero (0) values and the
         *  omitted responses.
         */

        $updated_text = implode('*', $updated_responses);

        $this->text = $updated_text;
    }

    /********************************
     *  DISPLAY SCREENS             *
     ********************************/

    /*  displayCustomPage()
     *  This is the page displayed when we want to still continue the session.
     *  We therefore display the custom message.
     */
    public function displayCustomPage($message = '', $options = [])
    {
        $default_options = [
            'continue' => true,
            'use_line_breaker' => true,
            'show_go_back' => false,
        ];

        $options = array_merge($default_options, $options);

        $response = $options['continue'] ? 'CON ' : 'END ';
        $response .= $message;
        $response .= $options['use_line_breaker'] ? "\n" : '';
        $response .= $options['show_go_back'] ? '0. Go Back' : '';

        return $response;
    }

    /*  displayCustomGoBackPage()
     *  This is the page displayed when a problem was encountered and but we want
     *  to still continue the session. We therefore display the custom error
     *  message but also display the option to go back.
     */
    public function displayCustomGoBackPage($message = '', $options = null)
    {
        $response = $this->displayCustomPage($message, $options ?? [
            'show_go_back' => true,
        ]);

        return $response;
    }

    /*  displayCustomErrorPage()
     *  This is the page displayed when a problem was encountered and we want
     *  to end the session with a custom error message.
     */
    public function displayCustomErrorPage($error_message = '', $options = null)
    {
        $response = $this->displayCustomPage($error_message, $options ?? [
            'continue' => false,
        ]);

        return $response;
    }
}
