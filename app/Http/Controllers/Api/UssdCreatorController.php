<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UssdCreatorController extends Controller
{
    private $log;
    private $text;
    private $test;
    private $level;
    private $screen;
    private $screens;
    private $api_trigger;
    private $linkedScreen;
    private $screenActions;
    private $screenContent;
    private $ussdInterface;
    private $screenInstructions;
    private $currentUserResponse;
    private $ussdBuilderMetadata;
    private $generated_variables;
    private $dynamic_data_storage;
    private $last_recorded_log_microtime;

    public function __construct(Request $request)
    {
        //  Check if we are on TEST MODE
        $this->test_mode = ($request->get('testMode') == 'true' || $request->get('testMode') == '1') ? true : false;

        //  Get the Ussd TEXT value (User Response)
        $this->text = $request->get('text');

        //  Get the Ussd Interface
        $this->ussdInterface = \App\UssdInterface::find(59);

        //  Get the Ussd builder data
        $this->ussdBuilderMetadata = $this->ussdInterface->metadata;

        //  Initiate the dynamic data storage to an empty array
        $this->dynamic_data_storage = [
            'products' => [
                ['id' => '1', 'name' => 'Package 1', 'price' => '200'],
                ['id' => '2', 'name' => 'Package 2', 'price' => '300'],
                ['id' => '3', 'name' => 'Package 3', 'price' => '400'],
            ],
        ];

        //  Set the default level
        $this->level = 1;

        //  Initiate the log to an empty array
        $this->log = [];
    }

    public function home()
    {
        $this->manageGoBackRequests();

        //  Start the process of building the USSD Application
        $response = $this->startBuildingUssd();

        if ($this->test_mode) {
            return response(['response' => $response, 'logs' => $this->log])->header('Content-Type', 'text/plain');
        } else {
            return response($response)->header('Content-Type', 'text/plain');
        }
    }

    public function startBuildingUssd()
    {
        //  Set a log that the build process has started
        $this->logInfo('Building USSD Application');

        //  Check if the builder is available and ready for use
        if ($this->builderIsReady()) {
            //  Start building and displaying the ussd screens
            return $this->startBuildingUssdScreens();
        } else {
            //  Set a log that the build process has started
            $this->logError('Error building the USSD Application. The metadata required to build the application was not found');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
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
        return !empty($this->ussdBuilderMetadata) ? true : false;
    }

    /*  startBuildingUssdScreens()
     *  This method uses the ussd builder metadata get all the ussd screens,
     *  locate the first screen and start building each display screen that
     *  must be returned.
     */
    public function startBuildingUssdScreens()
    {
        //  Check if the USSD screens exist
        $doesNotExistResponse = $this->handleNonExistentScreens();

        //  If the USSD screens do not exist return the response otherwise continue
        if ($this->isDisplayScreen($doesNotExistResponse)) return $doesNotExistResponse;

        //  Get the first display screen
        $this->screen = $this->getFirstScreenToDisplay();

        //  Handle current screen
        return $this->handleCurrentScreen();
    }

    /*  handleNonExistentScreens()
     *  This method checks if we have any screens to display. If we don't we
     *  log a warning and display the technical difficulties page.
     */
    public function handleNonExistentScreens()
    {
        //  Check if the screens exist
        if ($this->checkIfScreensExist() != true) {
            //  Set a warning log that we could not find any screens
            $this->logWarning('No screens found');

            //  Display the technical difficulties error page to notify the user of the issue
            $response = $this->displayTechnicalDifficultiesErrorPage();
        }

        //  Return null if we have screens
        return null;
    }

    /*  checkIfScreensExist()
     *  This method checks if the USSD metadata has any screens we can display.
     *  It will return true if we have screens to display and false if we don't
     *  have screens to display.
     */
    public function checkIfScreensExist()
    {
        //  Check if the screen metadata is an array that its not empty
        if (is_array($this->ussdBuilderMetadata) && !empty($this->ussdBuilderMetadata)) {
            //  Return true to indicate that the screens exist
            return true;
        }

        //  Return false to indicate that the screens do not exist
        return false;
    }

    /*  getFirstScreenToDisplay()
     *  This method gets the first screen that we should display. First we look
     *  for a screen indicated by the user. If we can't locate that screen we
     *  then default to the first available screen that we can display.
     */
    public function getFirstScreenToDisplay()
    {
        //  Set an info log that we are searching for the first screen
        $this->logInfo('Searching for the first screen');

        //  Get all the screens available
        $this->screens = $this->ussdBuilderMetadata;

        //  Get the first display screen (The one specified by the user)
        $this->screen = collect($this->screens)->where('first_display_screen', true)->first() ?? null;

        //  If we did not manage to get the first display screen specified by the user
        if (!$this->screen) {
            //  Set a warning log that the default starting screen was not found
            $this->logWarning('Default starting screen was not found');

            //  Set an info log that we will use the first available screen
            $this->logInfo('Selecting the first available screen as the default starting screen');

            //  Select the first screen on the ussd builder by default
            $this->screen = $this->ussdBuilderMetadata[0];
        }

        //  Set an info log for the first selected screen
        $this->logInfo('Selected <span class="text-primary">'.$this->screen['title'].'</span> as the first screen');

        //  Return the first screen to display
        return  $this->screen;
    }

    /*  handleCurrentScreen()
     *  This method first checks if the screen we want to handle exists. This could be the
     *  first display screen or any linked screen. In either case if the screen does not
     *  exist we log a warning and display the technical difficulties page. We then check
     *  if the user has already responded to the current screen. If (No) then we build
     *  and display the current screen. If (Yes) then we need to validate, format and
     *  store the users response respectively if specified.
     */
    public function handleCurrentScreen()
    {
        //  Check if the current display screen exists
        $doesNotExistResponse = $this->handleNonExistentScreen();

        //  If the current display screen does not exist return the response otherwise continue
        if ($this->isDisplayScreen($doesNotExistResponse)) {
            return $doesNotExistResponse;
        }

        //  Check if the current screen uses API's
        if ($this->checkIfScreenUsesAPIs()) {
            //  Handle the API Driven screen
            return $this->handleAPIDrivenScreen();
        } else {
            /* Get the screen content that will be used for determining screen details such as
             *  screen instructions, allowed actions, validation rules, formatting rules,
             *  local storage and screen specific settings.
             */
            $this->screenContent = $this->screen['content'];

            //  Handle the Non API Driven screen
            return $this->handleNonAPIDrivenScreen();
        }
    }

    public function handleAPIDrivenScreen()
    {
        //  Get the API Triggers
        $api_triggers = $this->screen['api_triggers'] ?? [];

        $response = null;

        foreach( $api_triggers as $api_trigger ){

            //  Get the current API Trigger
            $this->api_trigger = $api_trigger;

            //  Get the current Trigger API type
            $apiType = $this->getAPIType();

            //  If the current Trigger is a custom API
            if( $apiType == 'custom' ){
                
                //  Handle the custom API
                $response = $this->handleCustomApi();

            }

        }

        return $response;
    }

    public function getAPIType()
    {
        return $this->api_trigger['api_type'] ?? null;
    }

    public function handleCustomApi()
    {
        //  Run the custom API Call
        $apiCallResponse = $this->runCustomApiCall();

        //  If the response returned a screen display return the screen display otherwise continue
        if ($this->isDisplayScreen($apiCallResponse)) return $apiCallResponse;

        return $this->handleCustomApiResponse( $apiCallResponse );

    }    
    
    public function runCustomApiCall()
    {
        $url = $this->getCustomApiURL();
        $method = $this->getCustomApiMethod();
        $headers = $this->getCustomApiHeaders();
        $form_data = $this->getCustomApiFormData();
        $query_params = $this->getCustomApiQueryParams();
        $request_options = [];

        if( empty($url) || empty($method) ){

            if( empty($url) ){

                //  Set a warning log that the custom API Url was not provided
                $this->logWarning('API Url was not provided');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();

            }

            if( empty($method) ){

                //  Set a warning log that the custom API Method was not provided
                $this->logWarning('API Method was not provided');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();

            }

        }else{

            //  Set an info log of the Custome API Url provided
            $this->logInfo('API Url: <span class="text-success">' . $url .'</span>');

            //  Set an info log of the custom API Method provided
            $this->logInfo('API Method: <span class="text-success">' . ucwords($method) .'</span>');

        }

        //  Check if the provided url is correct
        if( !$this->isValidUrl($url) ){

            //  Set a warning log that the custom API Url provided is incorrect
            $this->logWarning('API Url provided is incorrect (<span class="text-danger">'.$url.'</span>)');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();

        }

        //  If we have the headers
        if( !empty( $headers ) && is_array( $headers ) ){

            foreach( $headers as $key => $value ){

                //  Set an info log of the custom API header attribute
                $this->logInfo('Headers: <span class="text-success">' . $key .'</span> = <span class="text-success">'. $value . '</span>');

            }

        }

        //  If we have the form data
        if( !empty( $query_params ) && is_array( $query_params ) ){

            foreach( $query_params as $key => $value ){

                //  Set an info log of the custom API query param attribute
                $this->logInfo('Query Params: <span class="text-success">' . $key .'</span> = <span class="text-success">'. $value . '</span>');

            }

        }

        //  If we have the form data
        if( !empty( $form_data ) && is_array( $form_data ) ){

            //  Add the form data to the form_params attribute of our API options
            array_push($request_options, ['form_params' => $form_data]);

            foreach( $form_data as $key => $value ){

                //  Set an info log of the custom API form data attribute
                $this->logInfo('Form Data: <span class="text-success">' . $key .'</span> = <span class="text-success">'. $value . '</span>');

            }

        }

        //  Create a new Http Guzzle Client
        $httpClient = new \GuzzleHttp\Client();

        try {
        
            //  Set an info log that we are performing custom API call
            $this->logInfo('Run API call to: <span class="text-success">'.$url.'</span>');
            
            //  Perform and return the Http request
            return $httpClient->request($method, $url, $request_options);

        /** About guzzle errors
         * 
         *  GuzzleHttp\Exception\ClientException for 400-level errors
         *  GuzzleHttp\Exception\ServerException for 500-level errors
         *  GuzzleHttp\Exception\BadResponseException for both (it's their superclass)
         * 
         *  Read More = http://docs.guzzlephp.org/en/latest/quickstart.html#exceptions
         */
        } catch (\GuzzleHttp\Exception\RequestException $e) {
                
            //  Set a warning log that the Api call failed
            $this->logWarning('Api call to <span class="text-danger">'.$url.'</span> failed.');

            /**
             * Here we actually catch the instance of GuzzleHttp\Psr7\Response
             * (find it in ./vendor/guzzlehttp/psr7/src/Response.php) with all
             * its own and its 'Message' trait's methods.
             *
             * So now we have: HTTP status code, message, headers and body.
             * Just check the exception object has the response before.
             * running any methods on it.
             */
            if ($e->hasResponse()) {
                
                //  Return the failed response from the current exception object
                return $e->getResponse();
                
            //  Incase we fail to get the response object
            }else{

                //  Handle try catch error
                return $this->handleTryCatchError($e);
                
            }

        //  Just incase we failed to catch RequestException
        } catch (\Throwable $e) {
                
            //  Set a warning log that the Api call failed
            $this->logWarning('Api call to <span class="text-danger">'.$url.'</span> failed.');

            //  Handle try catch error
            return $this->handleTryCatchError($e);

        //  Just incase we failed to catch RequestException and Throwable
        } catch (Exception $e) {
                
            //  Set a warning log that the Api call failed
            $this->logWarning('Api call to <span class="text-danger">'.$url.'</span> failed.');

            //  Handle try catch error
            return $this->handleTryCatchError($e);

        }
    }

    public function getCustomApiURL()
    {
        $url = $this->api_trigger['api_data']['url'] ?? null;
        
        return $url;
    }
    public function getCustomApiMethod()
    {
        $method = $this->api_trigger['api_data']['method'] ?? null;
        
        return $method;
    }
    public function getCustomApiHeaders()
    {
        $headers = $this->api_trigger['api_data']['headers'] ?? [];

        $data = [];

        foreach( $headers as $header ){

            if( !empty( $header['key'] ) ){

                $data[ $header['key'] ] = $header['value'];

            }

        }

        return $data;
    }
    public function getCustomApiFormData()
    {
        $form_data = $this->api_trigger['api_data']['form_data'] ?? [];

        $data = [];

        foreach( $form_data as $form_item ){
            
            if( !empty( $form_item['key'] ) ){

                $data[ $form_item['key'] ] = $form_item['value'];

            }

        }

        return $data;
    }
    public function getCustomApiQueryParams()
    {
        $query_params = $this->api_trigger['api_data']['query_params'] ?? [];

        $data = [];

        foreach( $query_params as $query_param ){

            if( !empty( $form_item['key'] ) ){

                $data[ $query_param['key'] ] = $query_param['value'];

        }

        }

        return $data;
    }
    public function getCustomApiStatusHandles()
    {
        $query_params = $this->api_trigger['api_data']['response_status_handles'] ?? [];

        return $query_params;
    }

    public function isValidUrl($url = '')
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }
    
    public function handleCustomApiResponse( $response = null )
    {
        if( $response ){

            /** Get the return type. We use the return type to determine how we want to handle
             *  the response of the API Call. Our options are as follows:
             * 
             *  Automatic : Automatically display the default success/error message depending on the API success
             *  Manual    : Manually setup a display screen with custom instructions, action, validation, e.t.c
             * 
             *  Default is "automatic" if no value is provided
             */
            $return_type = $this->api_trigger['api_data']['general']['response_type'] ?? 'automatic';
        
            //  Set an info log that we are starting to handle the custom API response
            $this->logInfo('Start handling Api Response.');

            if( $return_type == 'manual' ){

                return $this->handleCustomApiManualResponse( $response );

            }else if( $return_type == 'automatic' ){

                return $this->handleCustomApiAutomaticResponse( $response );

            }

        }
    }

    public function handleCustomApiAutomaticResponse( $response = null )
    {
        //  Set an info log that the custom API will be handled automatically
        $this->logInfo('Handle response <span class="text-success">Automatically</span>');

        //  Get the response status code e.g "200"
        $status_code = $response->getStatusCode();

        //  Get the response status phrase e.g "OK"
        $status_phrase = $response->getReasonPhrase() ?? '';

        //  Get the default success message
        $default_success_message = $this->api_trigger['api_data']['general']['default_success_message'] ?? 'Completed successfully';

        //  Get the default error message
        $default_error_message = $this->api_trigger['api_data']['general']['default_error_message'] ?? null;

        /** About Status Code:
         * 
         *  1xx informational response – the request was received, continuing process
         *  2xx successful – the request was successfully received, understood, and accepted
         *  3xx redirection – further action needs to be taken in order to complete the request
         *  4xx client error – the request contains bad syntax or cannot be fulfilled
         *  5xx server error – the server failed to fulfil an apparently valid request
         * 
         */
        $digit = substr($status_code, 0, 1);

        //  If the status code starts with "1", "2" or "3" e.g "100", "200", "301" e.t.c 
        if( in_array($digit, ['1', '2', '3']) ){  

            //  Set an info log of the response status code received
            $this->logInfo('API response returned a status (<span class="text-success">'.$status_code.'</span>) Status text: <span class="text-success">'.$status_phrase.'</span>');

            //  This is a good response - Display the custom succcess message
            return $this->displayCustomPage($default_success_message, ['continue' => false]);


        //  If the status code starts with "4" or "5" e.g "400", "401", "500" e.t.c 
        }elseif( in_array($digit, ['4', '5']) ){  

            //  Set an info log of the response status code received
            $this->logWarning('API response returned a status (<span class="text-danger">'.$status_code.'</span>) <br/> Status text: <span class="text-danger">'.$status_phrase.'</span>');

            //  If the custom error message was provided
            if( !empty( $default_error_message ) ){

                //  This is a bad response - Display the custom error message
                return $this->displayCustomErrorPage($default_error_message);

            //  If the custom error message was not provided 
            }else{

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();

            }

        }
    }
    public function handleCustomApiManualResponse( $response = null )
    {  
        //  Use the try/catch handles incase we run into any possible errors
        try {
            
            //  Set an info log that the custom API will be handled manually
            $this->logInfo('Handle response <span class="text-success">Manually</span>');

            //  Get the response status code e.g "200"
            $status_code = $response->getStatusCode();
            
            //  Get the response status phrase e.g "OK"
            $status_phrase = $response->getReasonPhrase() ?? '';

            //  Get the response body e.g [ "products" => [ ... ] ]  
            $response_body = json_decode($response->getBody()); 

            //  Get the response status handles
            $request_status_handles = $this->api_trigger['api_data']['response_status_handles'] ?? null;

            if( !empty( $request_status_handles ) ){

                //  Get the request status handle that matches the given status
                $selectedHandle = collect(array_filter($request_status_handles, function ($request_status_handle) use($status_code) {
                    return $request_status_handle['status'] == $status_code;
                }))->first() ?? null;

                //  If a matching response status handle was found
                if( $selectedHandle ){

                    //  Set an info log that we are storing the attributes of the custom API response
                    $this->logInfo('Start processing and storing the response attributes');

                    //  Get the response attributes
                    $response_attributes = $selectedHandle['attributes'];

                    //  Set an info log of the number of attributes found
                    $this->logInfo('Found ('.count($response_attributes).') attributes');

                    //  Add the current response body to the dynamic data storage 
                    $this->dynamic_data_storage['response'] = $response_body;

                    //  If we have dynamic variables with stored data
                    if (count($this->dynamic_data_storage)) {

                        //  Create dynamic variables
                        foreach ($this->dynamic_data_storage as $key => $value) {
                            /*  Foreach dataset use the iterator key to create the dynamic variable name and
                            *  assign the iterator value as the new variable value.
                            *
                            *  Example:
                            *
                            *  $data = ['product' => 'Orange', 'quantity' => 3, 'price' => 450, ...e.tc];
                            *
                            *  Foreach dataset, we produce dynamic variables e.g
                            *
                            *  $product = 'Orange';
                            *  $quantity = 3;
                            *  $price = 450;
                            *
                            *  ... e.t.c
                            *
                            *  Convert the value to a JSON Object. Converting each value into an object helps us
                            *  target nested values by using the "->" symbol e.g we can access deeply nested
                            *  values in this way:
                            *
                            *  $company->details->contacts->phone;
                            *
                            */
                            ${$key} = $this->convertToJsonObject($value);

                        }

                    }

                    foreach( $response_attributes as $response_attribute ){

                        //  If the attribute name and value exists
                        if( !empty($response_attribute['name'] && !empty($response_attribute['value']) ) ){

                            //  Get the attribute name
                            $name = $response_attribute['name'];

                            //  Get the attribute value
                            $mustache_tag = $response_attribute['value'];

                            //  Use the try/catch handles incase we run into any possible errors
                            try {

                                //  Convert "{{ company.name }}" into "$company->name"
                                $dynamic_variable = $this->convertMustacheTagIntoPHPVariable($mustache_tag, true);

                                //  Convert the dynamic property into its dynamic value e.g "$company->name" into "Company XYZ"
                                $output = eval("return $dynamic_variable;");

                                //  Add the current attribute value as additional dynamic data to our dynamic data storage
                                $this->dynamic_data_storage[ $name ] = $output;

                                //  If the dynamic value is a string, integer or float
                                if ( is_string($output) || is_integer($output) || is_float($output)) {

                                    //  Set an info log that we are converting the dynamic propery to its associated value
                                    $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">'.$output.'</span>');

                                //  Incase the dynamic value is not a string, integer or float
                                }else{

                                    //  Set an info log that we are converting the dynamic propery to its associated value
                                    $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">['.gettype($output).']</span>');
                                
                                }
                                
                            } catch (\Throwable $e) {

                                //  Handle try catch error
                                return $this->handleTryCatchError($e);

                            } catch (Exception $e) {

                                //  Handle try catch error
                                return $this->handleTryCatchError($e);

                            }
                            
                        }

                    }
                    
                    //  Check if we have an attribute named "response"
                    $attributeNamedResponseExists = count(array_filter($response_attributes, function ($response_attribute) {
                        return $response_attribute['name'] == 'response';
                    })) ? true : false;

                    //  If we don't have an attribute named "response"
                    if( !$attributeNamedResponseExists ){

                        //  Remove the current response body we added to the dynamic data using the name "response"
                        unset( $this->dynamic_data_storage['response'] );

                    }

                    /** Get the screen content that will be used for determining screen details such as
                     *  screen instructions, allowed actions, validation rules, formatting rules,
                     *  local storage and screen specific settings.
                     */
                    $this->screenContent = $selectedHandle['content'];

                    //  Handle the Non API Driven screen
                    return $this->handleNonAPIDrivenScreen();
                    
                }else{

                    //  Set a warning log that the custom API does not have a matching response status handle
                    $this->logWarning('No matching status handle to process the current response of status <span class="text-success">'.$status_code.'</span>');

                }

            }else{

                //  Set a warning log that the custom API does not have response status handles
                $this->logWarning('No response status handles to process the current response of status <span class="text-success">'.$status_code.'</span>');

            }

            //  Set a warning log that the custom API cannot be handled manually
            $this->logWarning('Could not handle the response <span class="text-success">Manually</span>, attempt to handle <span class="text-success">Automatically</span>');

            //  Handle the request automatically
            return $this->handleCustomApiAutomaticResponse( $response );

        } catch (\Throwable $e) {

            //  Handle try catch error
            return $this->handleTryCatchError($e);

        } catch (Exception $e) {

            //  Handle try catch error
            return $this->handleTryCatchError($e);

        }
    }









    public function handleNonExistentScreen()
    {
        /* First check if the current screen is available for processing
         *  Note that at level = 1 we are indicating that we are targeting
         *  the first display screen. Any level more that 1 e.g level = 2
         *  indicates that we are targeting the linked display screen
         */

        //  If this is the first display screen
        if ($this->level == 1) {
            //  If the first display screen is not available
            if (empty($this->screen)) {
                //  Set a warning log that the first display screen could not be found
                $this->logWarning('The first display screen could not be found');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();
            }

            //  If this is the linked display screen
        } else {
            //  If the linked display screen is not available
            if (empty($this->screen)) {
                //  Set a warning log that the first display screen could not be found
                $this->logWarning('The linked display screen could not be found');

                //  Display the technical difficulties error page to notify the user of the issue
                $response = $this->displayTechnicalDifficultiesErrorPage();
            }
        }

        return null;
    }

    public function checkIfScreenUsesAPIs()
    {
        //  Set an info log that we are checking if the current screen uses API's
        $this->logInfo('Checking if the screen uses API\'s');

        //  Check if the screen uses API's
        if ($this->screen['use_apis'] == true) {
            //  Set an info log that the current screen uses API's
            $this->logInfo('<span class="text-primary">'.$this->screen['title'].'</span> uses API\'s');

            //  Return true to indicate that the screen does use API's
            return true;
        }

        //  Set an info log that the current screen uses API's
        $this->logInfo('<span class="text-primary">'.$this->screen['title'].'</span> does not use API\'s');

        //  Return false to indicate that the screens does not use API's
        return false;
    }

    public function handleNonAPIDrivenScreen()
    {
        //  Check if the user has already responded to the current display screen
        if ($this->completedLevel($this->level)) {

            //  Get the user response, validate it, format it and store it
            $response = $this->handleCurrentScreenUserResponse();

            //  If validating, formatting or storing the user input failed the return the failed response otherwise continue
            if ($this->isDisplayScreen($response)) {
                return $response;
            }

            //  Check if the current screen must link to another screen
            if ($this->checkIfCurrentScreenMustLinkToAnotherScreen()) {

                //  Increment the current level so that we target the next screen (This means we are targeting the linked screen)
                ++$this->level;

                //  Set the current screen as the linked screen
                $this->screen = $this->linkedScreen;

                //  Reset the linked screen to nothing
                $this->linkedScreen = null;

                //  Handle the current screen (This means we are handling the linked screen)
                return $this->handleCurrentScreen();
            }
        }

        //  Build the current screen display
        return $this->buildCurrentScreenDisplay();
    }

    public function handleCurrentScreenUserResponse()
    {
        //  Get the user response (Input provided by the user) for the current display screen
        $this->getCurrentScreenUserResponse();

        //  Validate the user response (Input provided by the user)
        $failedValidationResponse = $this->validateCurrentScreenUserResponse();

        //  If the current user response failed the validation then return the failed response otherwise continue
        if ($this->isDisplayScreen($failedValidationResponse)) {
            return $failedValidationResponse;
        }

        //  Format the user response (Input provided by the user)
        $failedFormatResponse = $this->formatCurrentScreenUserResponse();

        //  If the current user response failed to format then return the failed response otherwise continue
        if ($this->isDisplayScreen($failedFormatResponse)) {
            return $failedFormatResponse;
        }

        //  Store the user response (Input provided by the user) as a named dynamic variable
        $storeInputResponse = $this->storeCurrentScreenUserResponseAsDynamicVariable();

        //  If storing the current user response failed then return the failed response otherwise continue
        if ($this->isDisplayScreen($storeInputResponse)) {
            return $storeInputResponse;
        }
    }

    /*  getCurrentScreenUserResponse()
     *  This method gets the users response for the current screen if it exists otherwise
     *  returns an empty string if it does not exist. We also log an info message to
     *  indicate the screen name associated with the provided response.
     */
    public function getCurrentScreenUserResponse()
    {
        //  Get the current screen user response (Input provided by the user)
        $this->currentUserResponse = $this->getResponseFromLevel($this->level) ?? '';   //  John Doe

        //  Set an info log that the user has responded to the current screen and show the input value
        $this->logInfo('User has responded to <span class="text-primary">'.$this->screen['title'].'</span> with <span class="text-success">'.$this->currentUserResponse.'</span>');

        //  Return the current screen user response
        return $this->currentUserResponse;
    }

    /*  validateCurrentScreenUserResponse()
     *  This method gets all the validation rules of the current screen. We then use these
     *  validation rules to validate the users response for the current screen.
     */
    public function validateCurrentScreenUserResponse()
    {
        //  Get the validation rules
        $validationRules = $this->screenContent['validation']['rules'] ?? [];

        //  Validate the user response (Input provided by the user)
        $failedValidationResponse = $this->handleValidationRules($validationRules);

        //  If the current user response failed the validation return the failed response otherwise continue
        if ($this->isDisplayScreen($failedValidationResponse)) {
            return $failedValidationResponse;
        }

        //  Return null if validation passes
        return null;
    }

    /*  validateCurrentScreenUserResponse()
     *  This method checks if the given validation rules are active (If they must be used).
     *  If the validation rule must be used then we determine which rule we are given and which
     *  validation method must be used for each given case.
     */
    public function handleValidationRules($validationRules = [])
    {
        //  If we have validation rules
        if (!empty($validationRules)) {
            //  For each validation rulle
            foreach ($validationRules as $validationRule) {
                //  If the current validation rule is active (Must be used)
                if ($validationRule['active'] == true) {
                    //  Get the type of validation rule e.g "only_letters" or "only_numbers"
                    $validationType = $validationRule['type'];

                    //  Use the switch statement to determine which validation method to use
                    switch ($validationType) {
                        case 'only_letters':

                            return $this->applyValidationRule($validationRule, 'validateOnlyLetters'); break;

                        /*
                        case 'only_numbers':

                            return $this->applyValidationRule($validationRule, 'validateOnlyNumbers'); break;

                        case 'only_numbers_and_letters':

                            return $this->applyValidationRule($validationRule, 'validateOnlyNumbersAndLetters'); break;
                        */
                    }
                }
            }
        }

        //  Return null to indicate that validation passed
        return null;
    }

    /*  validateOnlyLetters()
     *  This method validates to make sure the current screen user's response
     *  is only letters and numbers
     */
    public function validateOnlyLetters($validationRule)
    {
        //  Regex pattern to allow letters and spaces only
        $pattern = "/[a-zA-Z\s]+/";

        //  If the pattern was not matched exactly i.e validation failed
        if (!preg_match($pattern, $this->currentUserResponse)) {
            //  Handle the failed validation
            return $this->handleFailedValidation($validationRule);
        }
    }

    /*  applyValidationRule()
     *  This method gets the validation rule and callback. The callback represents the name of
     *  the validation function that we must run to validate the current users response. Since
     *  we allow custom Regex patterns for custom validation support, we must perform this under
     *  a try/catch incase the provided custom Regex pattern is invalid. This will allow us to
     *  catch any emerging error and be able to use the handleFailedValidation() in order to
     *  display the fatal error message and additional debugging details.
     */
    public function applyValidationRule($validationRule, $callback)
    {
        $initialUserResponse = $this->currentUserResponse;

        try {
            /* Perform the validation method here e.g "validateOnlyLetters()" within the try/catch
             *  method and pass the validation rule e.g "validateOnlyLetters( $validationRule )"
             */
            $callback($validationRule);
        } catch (\Throwable $e) {
            //  Handle failed validation
            $this->handleFailedValidation($validationRule);

            //  Handle try catch error
            return $this->handleTryCatchError($e);
        } catch (Exception $e) {
            //  Handle failed validation
            $this->handleFailedValidation($validationRule);

            //  Handle try catch error
            return $this->handleTryCatchError($e);
        }
    }

    /*  handleFailedValidation()
     *  This method logs a warning with details about the failed validation rule
     */
    public function handleFailedValidation($validationRule)
    {
        $this->logWarning('Validation failed using ('.$validationRule['name'].'): <span class="text-error">' + $validationRule['error_msg'].'</span>');
    }

    /*  formatCurrentScreenUserResponse()
     *  This method gets all the formatting rules of the current screen. We then use these
     *  formatting rules to format the users response for the current screen.
     */
    public function formatCurrentScreenUserResponse()
    {
        //  Get the validation rules
        $formattingRules = $this->screenContent['formatting']['rules'] ?? [];

        //  Format the user response (Input provided by the user)
        $failedFormatResponse = $this->handleFormattingRules($formattingRules);

        //  If the current user response failed to format return the failed response otherwise continue
        if ($this->isDisplayScreen($failedFormatResponse)) {
            return $failedFormatResponse;
        }

        //  Return null if formatting passes
        return null;
    }

    /*  handleFormattingRules()
     *  This method checks if the given formatting rules are active (If they must be used).
     *  If the formatting rule must be used then we determine which rule we are given and which
     *  formatting method must be used for each given case.
     */
    public function handleFormattingRules($formattingRules = [])
    {
        //  If we have formatting rules
        if (!empty($formattingRules)) {
            //  For each formatting rulle
            foreach ($formattingRules as $formattingRule) {
                //  If the current formatting rule is active (Must be used)
                if ($formattingRule['active'] == true) {
                    //  Get the type of formatting rule e.g "capitalize" or "uppercase"
                    $formattingType = $formattingRule['type'];

                    //  Use the switch statement to determine which formatting method to use
                    switch ($formattingType) {
                        case 'capitalize':

                            return $this->applyFormattingRule($formattingRule, 'capitalizeFormat'); break;

                        /*
                        case 'uppercase':

                            return $this->applyFormattingRule($formattingRule, 'uppercaseFormat'); break;

                        case 'lowercase':

                            return $this->applyFormattingRule($formattingRule, 'lowercaseFormat'); break;
                        */
                    }
                }
            }
        }

        //  Return null to indicate that formatting passed
        return null;
    }

    /*  capitalizeFormat()
     *  This method formats the current screen user's response
     *  by capitalizing the given string
     */
    public function capitalizeFormat($formattingRule)
    {
        //  Capitalize the current string
        $this->currentUserResponse = ucwords(strtolower($this->currentUserResponse));
    }

    /*  applyFormattingRule()
     *  This method gets the formatting rule and callback. The callback represents the name of
     *  the formatting function that we must run to format the current users response. Since we
     *  allow custom code for custom formatting support, we must perform this under a try/catch
     *  incase the provided custom PHP code is invalid. This will allow us to catch any emerging
     *  error and be able to use the handleFailedFormatting() in order to display the fatal
     *  error message and additional debugging details.
     */
    public function applyFormattingRule($formattingRule, $callback)
    {
        $initialUserResponse = $this->currentUserResponse;

        try {
            /* Perform the formatting method here e.g "capitalizeFormat()" within the try/catch
             *  method and pass the formatting rule e.g "capitalizeFormat( $formattingRule )"
             */
            $callback($formattingRule);
        } catch (\Throwable $e) {
            //  Handle failed formatting
            $this->handleFailedFormatting($formattingRule);

            //  Handle try catch error
            return $this->handleTryCatchError($e);
        } catch (Exception $e) {
            //  Handle failed formatting
            $this->handleFailedFormatting($formattingRule);

            //  Handle try catch error
            return $this->handleTryCatchError($e);
        }
    }

    /*  handleFailedFormatting()
     *  This method logs a warning with details about the failed formatting rule
     */
    public function handleFailedFormatting($formattingRule)
    {
        //  Handle failed formatting
        $this->logWarning('Formatting failed using ('.$formattingRule['name'].') for <span class="text-success">'.$formattingRule.'</span>');
    }

    /*  storeCurrentScreenUserResponseAsDynamicVariable()
     *  This method gets the current screen action details to determine the type of action that the
     *  screen requested. We use the type of action e.g "Input a value" or "Select an option" to
     *  determine the approach we must use in order to get the value and reference name required
     *  to create dynamic data variables e.g
     *
     *  1) Storing the input value into a variable referenced as "first_name"
     *
     *  $first_name = "John";
     *
     *  2) Storing the details of a selected option into a variable referenced as "product"
     *
     *  $product = [ "name" => "Product 1", "value" => "1", input => "1" ];
     *
     *  ... e.t.c
     *
     *  These dynamic data variables can then be reference by other screens using mustache tags
     *  e.g {{ first_name }} or {{ product.name }}
     *
     */
    public function storeCurrentScreenUserResponseAsDynamicVariable()
    {
        //  Get the current screen expected action type
        $screenActionType = $this->getCurrentScreenActionType();

        //  If the action is to select an option e.g 1, 2 or 3
        if ($screenActionType == 'select_option') {
            //  Get the current screen expected select action type e.g static_options
            $screenSelectOptionType = $this->getCurrentScreenSelectOptionType();

            //  If the select options are basic static options
            if ($screenSelectOptionType == 'static_options') {

                return $this->storeSelectedStaticOptionAsDynamicData();

            //  If the select option are dynamic options
            } elseif ($screenSelectOptionType == 'dynamic_options') {

                return $this->storeSelectedDynamicOptionAsDynamicData();

                //  If the select option are generated via the code editor
            } elseif ($screenSelectOptionType == 'code_editor_options') {

                return $this->storeSelectedCodeOptionAsDynamicData();
                
            }

            //  If the action is to input a value e.g John
        } elseif ($screenActionType == 'input_value') {
            //  Get the current screen expected input action type e.g input_value
            $screenInputType = $this->getCurrentScreenInputType();

            /* If the input is a single value input e.g
             *  Q: Enter your first name
             *  Ans: John
            */
            if ($screenInputType == 'single_value_input') {
                return $this->storeSingleValueInputAsDynamicData();

            /* If the input is a multi-value input e.g
             *  Q: Enter your first name, last name and age separated by spaces
             *  Ans: John Doe 25
            */
            } elseif ($screenInputType == 'multi_value_input') {
                return $this->storeMultiValueInputAsDynamicData();
            }
        }
    }

    /*  getCurrentScreenActionType()
     *  This method gets the type of action requested by the current screen
     *
     */
    public function getCurrentScreenActionType()
    {
        //  Available type: "no_action", "input_value" and "select_option"
        return $this->screenContent['action']['selected_action_type'] ?? '';
    }

    /*  getCurrentScreenSelectOptionType()
     *  This method gets the type of "Select Option" requested by the current screen
     *
     */
    public function getCurrentScreenSelectOptionType()
    {
        //  Available type: "static_options", "dynamic_options" and "code_editor_options"
        return $this->screenContent['action']['select_option']['selected_type'] ?? '';
    }

    /*  storeSelectedStaticOptionAsDynamicData()
     *  This method gets the value from the selected static option and stores it within the
     *  specified reference variable if provided. It also determines if the next screen
     *  has been provided, if (yes) we fetch the specified screen and save it as a
     *  screen that we must link to in future.
     *
     */
    public function storeSelectedStaticOptionAsDynamicData()
    {
        $optionsResponse = $this->getStaticSelectOptions('array');

        /* If getting the options failed then return the failed response otherwise continue.
         *  The optionsResponse must return an array options. If this response is not an array
         *  or is a error page, then return the message string or error page.
         */
        if (!is_array($optionsResponse) || $this->isDisplayScreen($optionsResponse)) {
            return $optionsResponse;
        }

        //  Get the options
        $options = $optionsResponse;

        //  Get the reference name (The name used to store the selected option value for ease of referencing)
        $reference_name = $this->screenContent['action']['select_option']['static_options']['reference_name'] ?? null;

        //  Get the custom "no results message"
        $no_results_message = $this->screenContent['action']['select_option']['static_options']['no_results_message'] ?? null;

        //  Get the custom "incorrect option selected message"
        $incorrect_option_selected_message = $this->screenContent['action']['select_option']['static_options']['incorrect_option_selected_message'] ?? null;

        return $this->storeSelectedOption($options, $reference_name, $no_results_message, $incorrect_option_selected_message);
    }

    /*  storeSelectedDynamicOptionAsDynamicData()
     *  This method gets the value from the selected dynamic option and stores it within the
     *  specified reference variable if provided. It also determines if the next screen
     *  has been provided, if (yes) we fetch the specified screen and save it as a
     *  screen that we must link to in future.
     *
     */
    public function storeSelectedDynamicOptionAsDynamicData()
    {
        $optionsResponse = $this->getDynamicSelectOptions('array');

        /* If getting the options failed then return the failed response otherwise continue.
         *  The optionsResponse must return an array options. If this response is not an array
         *  or is a error page, then return the message string or error page.
         */
        if (!is_array($optionsResponse) || $this->isDisplayScreen($optionsResponse)) {
            return $optionsResponse;
        }

        //  Get the options
        $options = $optionsResponse;

        //  Get the reference name (The name used to store the selected option value for ease of referencing)
        $reference_name = $this->screenContent['action']['select_option']['dynamic_options']['reference_name'] ?? null;

        //  Get the custom "no results message"
        $no_results_message = $this->screenContent['action']['select_option']['dynamic_options']['no_results_message'] ?? null;

        //  Get the custom "incorrect option selected message"
        $incorrect_option_selected_message = $this->screenContent['action']['select_option']['dynamic_options']['incorrect_option_selected_message'] ?? null;

        return $this->storeSelectedOption($options, $reference_name, $no_results_message, $incorrect_option_selected_message);
    }

    /*  storeSelectedCodeOptionAsDynamicData()
     *  This method gets the value from the selected code option and stores it within the
     *  specified reference variable if provided. It also determines if the next screen
     *  has been provided, if (yes) we fetch the specified screen and save it as a
     *  screen that we must link to in future.
     *
     */
    public function storeSelectedCodeOptionAsDynamicData()
    {
        $optionsResponse = $this->getCodeSelectOptions('array');

        /* If getting the options failed then return the failed response otherwise continue.
         *  The optionsResponse must return an array options. If this response is not an array
         *  or is a error page, then return the message string or error page.
         */
        if (!is_array($optionsResponse) || $this->isDisplayScreen($optionsResponse)) {
            return $optionsResponse;
        }

        //  Get the options
        $options = $optionsResponse;

        //  Get the reference name (The name used to store the selected option value for ease of referencing)
        $reference_name = $this->screenContent['action']['select_option']['code_editor_options']['reference_name'] ?? null;

        //  Get the custom "no results message"
        $no_results_message = $this->screenContent['action']['select_option']['code_editor_options']['no_results_message'] ?? null;

        //  Get the custom "incorrect option selected message"
        $incorrect_option_selected_message = $this->screenContent['action']['select_option']['code_editor_options']['incorrect_option_selected_message'] ?? null;

        return $this->storeSelectedOption($options, $reference_name, $no_results_message, $incorrect_option_selected_message);
    }

    public function storeSelectedOption($options = [], $reference_name = null, $no_results_message = null, $incorrect_option_selected_message = null)
    {
        /** $options - Represents the select options as an array
         *
         *  Example Structure:.
         *
         *  [
         *      [ "name" => "Product 1", "value" => "1", input => "1", "next_screen" => "View Product"  ]
         *      [ "name" => "Product 2", "value" => "2", input => "2", "next_screen" => "View Product"  ]
         *      ...
         *  ]
         *
         *  Structure Definition
         *
         *  name:         Represents the display name of the option (What the user will see)
         *  value:        Represents the actual value of the option (What will be stored)
         *  next_screen:  The screen to link to when this option is selected
         *  input:        What the user must input to select this option
         */

        //  Get the users current response
        $user_response = $this->currentUserResponse;

        //  Get the reference name (The name used to store the selected option value for ease of referencing)
        $reference_name = $this->screenContent['action']['select_option']['dynamic_options']['reference_name'] ?? null;

        //  Get the custom "incorrect option selected message"
        $incorrect_option_selected_message = $this->screenContent['action']['select_option']['dynamic_options']['incorrect_option_selected_message'] ?? null;

        //  Check if we have options to display
        $optionsExist = count($options) ? true : false;

        //  Get option matching user response
        $selectedOption = collect(array_filter($options, function ($option) use ($user_response) {
            /* The users response must match the input value of the option.
             *  Use urldecode() to convert all encoded values to their
             *  decoded counterparts e.g
             *
             *  "%23" is an encoded value representing "#"
             */
            return urldecode($user_response) == $option['input'];
        }))->first() ?? null;

        //  If we have options to display
        if ($optionsExist) {
            //  If the user selected an option that exists
            if (!empty($selectedOption)) {
                //  Get the selected option next screen name (The screen we must link to after the user selects this option)
                $nextScreenName = $selectedOption['next_screen'] ?? null;

                //  If we have the selected option next screen name
                if ($nextScreenName) {
                    //  Get the screen matching the given name and set it as the linked screen
                    $this->linkedScreen = $this->getScreenByName($nextScreenName);
                }

                //  If we have the reference name provided
                if (!empty($reference_name)) {
                    //  If the option value was provided
                    if (!empty($selectedOption['value'])) {
                        
                        //  Get the option value only
                        $dynamic_data = $selectedOption['value'];

                    //  If the option value was not provided
                    } else {
                        
                        //  Get the option name and input. Set value equal to option name
                        $dynamic_data = [
                            'name' => $selectedOption['name'],
                            'value' => $selectedOption['name'],
                            'input' => $selectedOption['input'],
                        ];
                        
                    }

                    //  Store the select option as dynamic data
                    $this->dynamic_data_storage[$reference_name] = $dynamic_data;
                }

                //  If the user did not select an option that exists
            } else {
                //  Display the custom "Incorrect option selected" otherwise use default
                $message = ($incorrect_option_selected_message ?? "You selected an incorrect option. Please try again")."\n" ;

                //  Display a custom message (with go back option) to notify the user of the issue
                return $this->displayCustomGoBackPage($message);
            }

            //  If we don't have options to display
        } else {
            //  Display the custom "No options available" otherwise use default
            $message = ($no_results_message ?? "No options available")."\n";

            //  Display a custom message (with go back option) to notify the user of the issue
            return $this->displayCustomGoBackPage($message);
        }
    }

    /*  getScreenByName()
     *  This method returns a screen if it exists by searching based on the screen name provided
     *
     */
    public function getScreenByName($screenName = null)
    {
        //  If the screen name has been provided
        if ($screenName) {
            //  Get the first display screen that matches the given screen name
            return collect($this->screens)->where('title', $screenName)->first() ?? null;
        }
    }

    /*  getCurrentScreenSelectOptionType()
     *  This method gets the type of "Input" requested by the current screen
     *
     */
    public function getCurrentScreenInputType()
    {
        //  Available type: "single_value_input" and "multi_value_input"
        return $this->screenContent['action']['input_value']['selected_type'] ?? '';
    }

    /*  storeSingleValueInputAsDynamicData()
     *  This method gets the single value from the input and stores it within the specified
     *  reference variable if provided. It also determines if the next screen has been provided,
     *  if (yes) we fetch the specified screen and save it as a screen that we must link to in future.
     *
     */
    public function storeSingleValueInputAsDynamicData()
    {
        //  Get the users current response
        $user_response = $this->currentUserResponse;

        //  Get the reference name (The name used to store the input value for ease of referencing)
        $reference_name = $this->screenContent['action']['input_value']['single_value_input']['reference_name'] ?? null;

        //  Get the single input next screen name (The screen we must link to after the user inputs a value)
        $nextScreenName = $this->screenContent['action']['input_value']['single_value_input']['next_screen'] ?? null;

        //  If we have the single input next screen name
        if ($nextScreenName) {
            //  Get the screen matching the given name and set it as the linked screen
            $this->linkedScreen = $this->getScreenByName($nextScreenName);
        }

        //  If we have the reference name provided
        if (!empty($reference_name)) {
            //  Store the input value as dynamic data
            $this->dynamic_data_storage[$reference_name] = $user_response;
        }
    }

    /*  storeMultiValueInputAsDynamicData()
     *  This method gets the multiple values from the input and stores them within the specified
     *  reference variables if provided. It also determines if the next screen has been provided,
     *  if (yes) we fetch the specified screen and save it as a screen that we must link to in future.
     *
     */
    public function storeMultiValueInputAsDynamicData()
    {
        /** Get the users current response. This represents a string of multiple inputs
         *
         *  Example: "John Doe 24".
         */
        //  Get the users current response
        $user_response = $this->currentUserResponse;

        /** Get the reference names (The names used to store the input values for ease of referencing) e.g
         *
         *  Example: ['first_name', 'last_name', 'age'].
         */
        $reference_names = $this->screenContent['action']['input_value']['multi_value_input']['reference_names'] ?? [];

        /** Get the separator (The character used to separate the user input values).
         *  Default to spaces if not set.
         *
         *  Example: ","
         *
         *  Default: " "
         */
        $separator = $this->screenContent['action']['input_value']['multi_value_input']['separator'] ?? ' ';
        $separator = 'spaces' ? ' ' : $separator;

        //  Get the multi input next screen name (The screen we must link to after the user inputs values)
        $nextScreenName = $this->screenContent['action']['input_value']['multi_value_input']['next_screen'] ?? null;

        //  If we have the single input next screen name
        if ($nextScreenName) {
            //  Get the screen matching the given name and set it as the linked screen
            $this->linkedScreen = $this->getScreenByName($nextScreenName);
        }

        //  If we have the reference names provided
        if (!empty($reference_names)) {
            //  Separate the multiple user responses using the separator
            $user_responses = explode($separator, $user_response);

            // Foreach ['first_name', 'last_name', 'age']
            foreach ($reference_names as $key => $reference_name) {
                // Check if the current reference name has a corresponding user response value
                if (isset($user_responses[$key])) {
                    //  Get the provided response value e.g John
                    $user_response = $user_responses[$key];
                } else {
                    //  Default to an empty string
                    $user_response = '';
                }

                //  Store the input value as dynamic data
                $this->dynamic_data_storage[$reference_name] = $user_response;
            }
        }
    }

    /*  checkIfCurrentScreenMustLinkToAnotherScreen()
     *  This method checks if the current screen has a screen it can link to. If (yes)
     *  we return true, if (no) we return false.
     *
     */
    public function checkIfCurrentScreenMustLinkToAnotherScreen()
    {
        //  If we have a screen we can link to
        if (!empty($this->linkedScreen)) {
            //  Return true to indicate that we must link to another screen
            return true;
        }

        //  Return false to indicate that we must not link to another screen
        return false;
    }

    /*  buildCurrentScreenDisplay()
     *  Build the current screen display
     *
     */
    public function buildCurrentScreenDisplay()
    {
        //  Set an info log that we are building the current screen
        $this->logInfo('Start building <span class="text-primary">'.$this->screen['title'].'</span>');

        //  Build the screen display instruction (Screen instruction / description / information)
        $screenInstructionsBuildResponse = $this->buildScreenDisplayInstructions();

        //  If the screen instructions failed to build return the failed response otherwise continue
        if ($this->isDisplayScreen($screenInstructionsBuildResponse)) {
            return $screenInstructionsBuildResponse;
        }

        //  Get the built screen instructions (E,g Welcome to Company XYZ)
        $this->screenInstructions = $screenInstructionsBuildResponse;

        //  Build the screen display actions (E.g Select options)
        $screenActionBuildResponse = $this->buildScreenDisplayActions();

        //  If the screen actions failed to build return the failed response otherwise continue
        if ($this->isDisplayScreen($screenActionBuildResponse)) {
            return $screenActionBuildResponse;
        }

        //  Build the screen display actions (E.g Select options)
        $this->screenActions = $screenActionBuildResponse;

        //  Get the screen instruction and action
        $response = $this->screenInstructions.$this->screenActions;

        //  If the processed instructions and action are not empty
        if (!empty($response)) {
            //  Set an info log of the final result
            $this->logInfo('Final result: <br /><span class="text-success">'.$response.'</span>');
        }

        //  Return the screen instructions
        return $this->displayCustomPage($response);
    }

    public function buildScreenDisplayInstructions()
    {
        //  Check if the current screen uses "Code Editor Mode"
        $uses_code_editor_mode = $this->screenContent['description']['code_editor_mode'] ?? false;

        //  If the current screen instructions uses the PHP Code Editor
        if ($uses_code_editor_mode == true) {
            //  Set an info log that the current screen uses the PHP Code Editor to build screen instructions
            $this->logInfo('<span class="text-primary">'.$this->screen['title'].'</span> uses the PHP Code Editor to build instructions');

            //  Get the screen instructions code otherwise default to a return statement that returns an empty string
            $screen_instruction_text = $this->screenContent['description']['code_editor_text'] ?? "return '';";

        //  If the current content instructions/description does not use the PHP Code Editor
        } else {
            //  Set an info log that the current screen uses does not use the PHP Code Editor to build screen instructions
            $this->logInfo('<span class="text-primary">'.$this->screen['title'].'</span> does not use the PHP Code Editor to build instructions');

            //  Get the screen description text otherwise default to an empty string
            $screen_instruction_text = $this->screenContent['description']['text'] ?? '';
        }

        //  Process dynamic content embedded within the screen instructions
        return $this->handleEmbeddedDynamicContentConversion($screen_instruction_text, $uses_code_editor_mode);
    }

    public function isDisplayScreen($text = '')
    {
        if (is_string($text)) {
            
            //  If the first 3 characters of the text match the words "CON" or "END" then this is a display screen
            return  (substr($text, 0, 3) == 'CON' || substr($text, 0, 3) == 'END') ? true : false;

        }

        return false;
    }

    public function handleEmbeddedDynamicContentConversion($text = '', $uses_code_editor_mode = true)
    {
        //  If we have dynamic variables with stored data
        if (count($this->dynamic_data_storage)) {
            //  Set an info log that we are getting variables with dynamic data
            $this->logInfo('Get variables with dynamic data');

            //  Create dynamic variables
            foreach ($this->dynamic_data_storage as $key => $value) {
                /*  Foreach dataset use the iterator key to create the dynamic variable name and
                 *  assign the iterator value as the new variable value.
                 *
                 *  Example:
                 *
                 *  $data = ['product' => 'Orange', 'quantity' => 3, 'price' => 450, ...e.tc];
                 *
                 *  Foreach dataset, we produce dynamic variables e.g
                 *
                 *  $product = 'Orange';
                 *  $quantity = 3;
                 *  $price = 450;
                 *
                 *  ... e.t.c
                 *
                 *  Convert the value to a JSON Object. Converting each value into an object helps us
                 *  target nested values by using the "->" symbol e.g we can access deeply nested
                 *  values in this way:
                 *
                 *  $company->details->contacts->phone;
                 *
                 */
                ${$key} = $this->convertToJsonObject($value);

                //  Set an info log for the created variable and its dynamic data value
                $this->logInfo('Variable <span class="text-success">$'.$key.'</span> = '.json_encode($value));
            }
        }

        //  Remove the (\u00a0) special character which represents a no-break space in HTML
        $text = $this->remove_HTML_No_Break_Space( $text );

        //  Get all instances of mustache tags within the given text
        $result = $this->getInstancesOfMustacheTags($text);

        //  Get the total number of mustache tags found within the given text
        $number_of_mustache_tags = $result['total'];

        //  Get the mustache tags found within the given text
        $mustache_tags = $result['mustache_tags'];

        if ($uses_code_editor_mode == true) {
            //  Set an info log for the total number of dynamic data found in the PHP Code Editor text
            $this->logInfo('Found ('.$number_of_mustache_tags.') dynamic content references within the PHP Code Editor');
        } else {
            //  Set an info log for the total number of dynamic data found in the text
            $this->logInfo('Found ('.$number_of_mustache_tags.') dynamic content references inside the text: <span class="text-success">'.$text.'</span>');
        }

        //  If we managed to detect one or more mustache tags
        if ($number_of_mustache_tags) {
            //  Foreach mustache tag we must convert it into a php variable
            foreach ($mustache_tags as $mustache_tag) {
                
                //  Convert "{{ company.name }}" into "$company->name"
                $dynamic_variable = $this->convertMustacheTagIntoPHPVariable($mustache_tag, true);

                /*  If the current text is not using the PHP Code Editor Mode then this means that it does
                 *  not want to process complex code e.g if-else statements, foreach statements and php
                 *  methods such as trim(), strtolower(), ucwords() e.t.c In this case we can
                 *  immediately convert the dynamic variable into its corresponding value
                 */
                if (!$uses_code_editor_mode) {
                    //  Use the try/catch handles incase we run into any possible errors
                    try {
                        //  Convert the dynamic property into its dynamic value e.g "$company->name" into "Company XYZ"
                        $output = eval("return $dynamic_variable;");

                        //  Incase the dynamic value is not a string, integer or float
                        if (!is_string($output) && !is_integer($output) && !is_float($output)) {
                            //  Set an info log for the total number of dynamic data found in the text
                            $this->logWarning('The dynamic variable '.$mustache_tag.' is of type [<span class="text-success">'.gettype($output).'</span>] therefore we could not process it any further as it must convert to into a String, Interger or Float value');

                            //  Get the result type e.g Object, Array, Boolean e.t.c and wrap in square brackets
                            $output = '['.gettype($output).']';
                        }

                        //  Set an info log that we are converting the dynamic propery to its associated value
                        $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">'.$output.'</span>');
                    } catch (\Throwable $e) {
                        //  Handle try catch error
                        return $this->handleTryCatchError($e);
                    } catch (Exception $e) {
                        //  Handle try catch error
                        return $this->handleTryCatchError($e);
                    }

                    //  Replace the mustache tag with its dynamic data e.g replace "{{ company.name }}" with "Company XYZ"
                    $text = preg_replace("/$mustache_tag/", $output, $text);

                }else{

                    //  Replace the mustache tag with its dynamic variable e.g replace "{{ company.name }}" with "$company->name"
                    $text = preg_replace("/$mustache_tag/", $dynamic_variable, $text);

                }
            }
        }

        /*  If the current text is using the PHP Code Editor Mode then render the code
         *  and process if-else statements, foreach statements and php methods such as
         *  trim(), strtolower(), ucwords() e.t.c
         */
        if ($uses_code_editor_mode) {
            //  Use the try/catch handles incase we run into any possible errors
            try {
                //  Set an info log that we are processing the PHP Code from the PHP Code Editor
                $this->logInfo('Process PHP Code from the Code Editor');

                //  Remove the PHP tags from the PHP Code
                $text = $this->removePHPTags($text);

                //  Process the PHP Code
                $text = eval("$text");
            } catch (\Throwable $e) {
                //  Handle try catch error
                return $this->handleTryCatchError($e);
            } catch (Exception $e) {
                //  Handle try catch error
                return $this->handleTryCatchError($e);
            }
        }

        //  Return the converted text
        return $text;
    }

    public function remove_HTML_No_Break_Space( $text = '' )
    {
        return preg_replace('/\xc2\xa0/', '', $text);
    }

    public function getInstancesOfMustacheTags($text = '')
    {
        //  Remove the (\u00a0) special character which represents a no-break space in HTML
        $text = $this->remove_HTML_No_Break_Space( $text );

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

        $total_results = preg_match_all($pattern, $text, $results);

        /*
         * The "$total_results" represents the number of matched mustache tags e.g
         *
         * $total_results = 3;
         *
         * The "$results[0]" represents an array of the matched mustache tags
         *
         * $results[0] = [
         *      "{{ company.name }}",
         *      "{{ company.branches.total }}",
         *      "{{ company.details.contacts.phone }}",
         *      ... e.t.c
         *  ];
         */
        return ['total' => $total_results, 'mustache_tags' => $results[0]];
    }

    public function convertMustacheTagIntoPHPVariable($text = null, $add_sign = false)
    {
        //  If the text has been provided and is type of (String)
        if (!empty($text) && is_string($text)) {
            
            //  Remove the (\u00a0) special character which represents a no-break space in HTML
            $text = $this->remove_HTML_No_Break_Space( $text );

            //  Remove any HTML Tags
            $text = strip_tags($text);

            //  Replace all curly braces and spaces with nothing e.g convert "{{ company.name }}" into "company.name"
            $text = preg_replace("/[{}\s]*/", '', $text);

            //  Replace one or more occurences of the period with "->" e.g convert "company.name" or "company..name" into "company->name"
            $text = preg_replace("/[\.]+/", '->', $text);

            //  Remove left and right spaces (If Any)
            $text = trim($text);

            //  If we should add the PHP "$" sign
            if ($add_sign == true) {
                
                //  Append the $ sign to the begining of the result e.g convert "company->name" into "$company->name"
                $text = '$'.$text;

            }

            //  Return the converted text
            return $text;
        }

        return null;
    }

    public function convertMustacheTagIntoDynamicData($mustache_tag)
    {
        //  Use the try/catch handles incase we run into any possible errors
        try {
            
            //  Create dynamic variables
            foreach ($this->dynamic_data_storage as $key => $value) {
                /*  Foreach dataset use the iterator key to create the dynamic variable name and
                    *  assign the iterator value as the new variable value.
                    *
                    *  Example:
                    *
                    *  $data = ['product' => 'Orange', 'quantity' => 3, 'price' => 450, ...e.tc];
                    *
                    *  Foreach dataset, we produce dynamic variables e.g
                    *
                    *  $product = 'Orange';
                    *  $quantity = 3;
                    *  $price = 450;
                    *
                    *  ... e.t.c
                    *
                    *  Convert the value to a JSON Object. Converting each value into an object helps us
                    *  target nested values by using the "->" symbol e.g we can access deeply nested
                    *  values in this way:
                    *
                    *  $company->details->contacts->phone;
                    *
                    */
                ${$key} = $this->convertToJsonObject($value);
            }

            //  Set an info log that we are converting the mustache tag into dynamic data
            $this->logInfo('Start converting mustache tag <span class="text-success">'.$mustache_tag.'</span> into its associated dynamic data');
    
            //  Convert "{{ company.name }}" into "$company->name" and store the result within a unique variable referenced as "dynamic_variable"
            ${ $this->generateUniqueVariable('dynamic_variable') } = $this->convertMustacheTagIntoPHPVariable($mustache_tag, true);

            //  Convert the dynamic property into its dynamic value e.g "$company->name" into "Company XYZ"
            $output = eval("return ${ $this->generated_variables['dynamic_variable'] };");

            //  Incase the dynamic value is not a string, integer or float
            if (!is_string($output) && !is_integer($output) && !is_float($output)) {

                //  Set an info log for the final conversion result
                $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to [<span class="text-success">'.gettype($output).'</span>]');
            
            }else{

                //  Set an info log for the final conversion result
                $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">'.$output.'</span>');

            }

            //  Return the final output
            return $output;

        } catch (\Throwable $e) {

            //  Handle try catch error
            return $this->handleTryCatchError($e);

        } catch (Exception $e) {

            //  Handle try catch error
            return $this->handleTryCatchError($e);

        }
    }

    public function removePHPTags($text = '')
    {
        //  Remove PHP Tags
        $text = trim(preg_replace("/<\?php|\?>/i", '', $text));

        return $text;
    }

    public function buildScreenDisplayActions()
    {
        //  Get the current screen expected action type
        $screenActionType = $this->getCurrentScreenActionType();

        //  If the action is to select an option e.g 1, 2 or 3
        if ($screenActionType == 'select_option') {
            
            //  Get the current screen expected select action type e.g static_options
            $screenSelectOptionType = $this->getCurrentScreenSelectOptionType();

            //  If the select options are basic static options
            if ($screenSelectOptionType == 'static_options') {

                return $this->getStaticSelectOptions('string');

            //  If the select option are dynamic options
            } elseif ($screenSelectOptionType == 'dynamic_options') {

                return $this->getDynamicSelectOptions('string');

            //  If the select option are generated via the code editor
            } elseif ($screenSelectOptionType == 'code_editor_options') {

                return $this->getCodeSelectOptions('string');

            }
        }
    }

    /*  getStaticSelectOptions()
     *  This method builds the static select options for display on the screen
     */
    public function getStaticSelectOptions($returnType = 'array')
    {
        /** Get the available static options
         *
         *  Example Structure:.
         *
         *  [
         *      [ "name" => "Product 1", "value" => "1", input => "1", "next_screen" => "View Product"  ]
         *      [ "name" => "Product 2", "value" => "2", input => "2", "next_screen" => "View Product"  ]
         *      ...
         *  ]
         *
         *  Structure Definition
         *
         *  name:         Represents the display name of the option (What the user will see)
         *  value:        Represents the actual value of the option (What will be stored)
         *  next_screen:  The screen to link to when this option is selected
         *  input:        What the user must input to select this option
         */
        $options = $this->screenContent['action']['select_option']['static_options']['options'] ?? [];

        //  Get the custom "no results message"
        $no_results_message = $this->screenContent['action']['select_option']['static_options']['no_results_message'] ?? null;

        //  Check if we have options to display
        $optionsExist = count($options) ? true : false;

        //  If we have options to display
        if ($optionsExist) {
            $text = "\n";
            $collection = [];

            //  Foreach option
            for ($x = 0; $x < count($options); ++$x) {
                //  Get the current option
                $option = $options[$x];

                //  If the expected user input is a number e.g "1", "2" or "3"
                if (preg_match('/[0-9]+/', $option['input'])) {
                    //  Put the number and a period infront of it e.g "1. ", "2. " or "3. "
                    $input = $option['input'].'. ';

                //  If the expected user input is not a number e.g "#"
                } else {
                    //  Put the character and a space infront of it e.g "# "
                    $input = $option['input'].' ';
                }

                if ($returnType == 'array') {
                    //  Add the option
                    array_push($collection, [
                        //  Get the option name
                        'name' => $option['name'] ?? null,
                        //  Get the option input
                        'input' => $option['input'] ?? null,
                        //  If the option value was not provided
                        'value' => (empty($option['value']))
                                //  Use the entire option data as the value
                                ? $option
                                //  Otherwise use the converted version of the value provided
                                : $option['value'],
                        //  Get the option next screen
                        'next_screen' => $option['next_screen'],
                    ]);
                } elseif ($returnType == 'string') {
                    //  Add the option
                    $text .= $input.$option['name']."\n";
                }
            }

            if ($returnType == 'array') {
                //  Return the options
                return $collection;
            } elseif ($returnType == 'string') {
                //  Return the options
                return $text;
            }

            //  If we don't have options to display
        } else {
            //  If we have instructions to be displayed then add break lines
            $text = (!empty($this->screenInstructions) ? "\n\n" : '');

            //  Get the custom "No options available" otherwise use default
            $text .= ($no_results_message ?? 'No options available');

            //  Return the custom or default "No options available"
            return $text;
        }
    }

    /*  getDynamicSelectOptions()
     *  This method builds the dynamic options for display on the screen
     *
     *  @param returnType = array, string
     */
    public function getDynamicSelectOptions($returnType = 'array')
    {
        
        //  Get the type to return
        ${ $this->generateUniqueVariable('returnType') } = $returnType;

        //  Get the dynamic select options data
        $dynamicOptionsData = $this->screenContent['action']['select_option']['dynamic_options'] ?? null;

        //  Check if the dynamic options data exists
        if (empty($dynamicOptionsData)) {
            //  Set an warning log that the dynamic options data does not exist
            $this->logWarning('The data required to build the dynamic options does not exist');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        //  Check if the dynamic options data is an array
        if (!is_array($dynamicOptionsData)) {
            //  Set an warning log that the dynamic options data does not exist
            $this->logWarning('The data required to build the dynamic options must be of type [Array]');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        /* To avoid variable naming conflicts, create a custom generated unique variable name such as
         *  variable_1272509158_243453 that can be referenced as data_structure. This variable will be
         *  used to store our select dynamic options structure.
         *
         */
        $this->generateUniqueVariable('data_structure');

        //  Initialize our custom generated variable and equate it to our dynamic options data
        ${ $this->generated_variables['data_structure'] } = $this->convertToJsonObject($dynamicOptionsData);

        //  Set an info log that we are getting variables with dynamic data
        $this->logInfo('Get variables with dynamic data');

        //  Create dynamic variables
        foreach ($this->dynamic_data_storage as $key => $value) {
            /*  Foreach dataset use the iterator key to create the dynamic variable name and
                *  assign the iterator value as the new variable value.
                *
                *  Example:
                *
                *  $data = ['product' => 'Orange', 'quantity' => 3, 'price' => 450, ...e.tc];
                *
                *  Foreach dataset, we produce dynamic variables e.g
                *
                *  $product = 'Orange';
                *  $quantity = 3;
                *  $price = 450;
                *
                *  ... e.t.c
                *
                *  Convert the value to a JSON Object. Converting each value into an object helps us
                *  target nested values by using the "->" symbol e.g we can access deeply nested
                *  values in this way:
                *
                *  $company->details->contacts->phone;
                *
                */
            ${$key} = $this->convertToJsonObject($value);

            //  Set an info log for the created variable and its dynamic data value
            $this->logInfo('Variable <span class="text-success">$'.$key.'</span> = '.json_encode($value));
        }

        // If mustache tags are not provided
        if (empty(${ $this->generated_variables['data_structure'] }->group_reference)) {
            //  Set an warning log that the group reference value does not exist
            $this->logWarning('The group reference mustache tag was not provided on the Dynamic Select Option and therefore we cannot create the dynamic select options');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        // If mustache tags are not valid
        if (!$this->isValidMustacheTag(${ $this->generated_variables['data_structure'] }->group_reference)) {
            //  Set an warning log that the group reference value does not exist
            $this->logWarning('The given group reference mustache tag provided on the Dynamic Select Option is not a valid mustache syntax and therefore we cannot create the dynamic select options');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        // If the template reference name is not provided
        if (empty(${ $this->generated_variables['data_structure'] }->template_reference_name)) {
            //  Set an warning log that the group reference value does not exist
            $this->logWarning('The template reference name was not provided on the Dynamic Select Option and therefore we cannot create the dynamic select options');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        /* Convert the mustache tag into a PHP variable using the convertMustacheTagIntoPHPVariable()
         *  function and assign the converted value to a new custom generated unique variable referenced
         *  as items. Pass false as Param 2 to return the converted value without adding the "$" sign
         */

        // Create and set the custom generated variable equal to the converted result
        ${ $this->generateUniqueVariable('items') } =

            // Convert the group_reference value mustache tag into a PHP valid variable name e.g "{{ products }}" into "products"
            $this->convertMustacheTagIntoPHPVariable(
                // Param 1 - Pass the value to convert (The group reference mustache tag)
                ${ $this->generated_variables['data_structure'] }->group_reference,

                // Param 2 - Pass False to return without a "$" sign
                false
            );

        //  Check if the variable exists at this moment - Use PHP isset() to check.
        if (!isset(${ $this->generated_variables['items'] })) {
            /** Does not exist
             *
             *  Set an warning log that the group reference value does not exist.
             */
            $providedMustacheTag = ${ $this->generated_variables['data_structure'] }->group_reference;
            $this->logWarning('The given group reference mustache tag <span class="text-success">'.$providedMustacheTag.'</span> represents a non-existent value therefore we cannot create the dynamic select options');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        //  Get the value of the items
        $this->generated_variables['items'] = ${ $this->generated_variables['items'] };

        /** Note that empty arrays ( i.e = [] ) are converted to null values due to the
         *  convertToJsonObject() method, therefore it is very possible that the value
         *  of ${ $this->generated_variables['items'] } may be an empty array eventhough
         *  running gettype() may return "null" instead of type of "array". This means 
         *  that we must first check if the value is null before we can check if this 
         *  is of type of "array". We will then later treat this null value as an
         *  empty array and display an "no results message"
         * 
         */
        if (!is_null(${ $this->generated_variables['items'] })) {

            //  Check if the variable is of type [Array] - Use PHP is_array() to check.
            if (!is_array(${ $this->generated_variables['items'] })) {
                /** Does not exist
                 *
                 *  Set an warning log that the group reference value must be of type array.
                 */
                $dataType = ucwords(gettype(${ $this->generated_variables['items'] }));
                $providedMustacheTag = ${ $this->generated_variables['data_structure'] }->group_reference;
                $this->logWarning('The given group reference mustache tag <span class="text-success">'.$providedMustacheTag.'</span> must be of type [<span class="text-success">Array</span>] however we received a value of type [<span class="text-success">'.$dataType.'</span>] therefore we cannot create the dynamic select options');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();
            }
        }

        //  Use the try/catch handles incase we run into any possible errors
        try {
            //  Set an info log that we are starting to list the dynamic options
            $this->logInfo('Start listing dynamic options');

            /** Check if we have options to display
             *  The options must be an non-empty array and must not be null
             */
            ${ $this->generateUniqueVariable('optionsExist') } = ( count( ${ $this->generated_variables['items'] } ) != 0 && !is_null( ${ $this->generated_variables['items'] } ) ) ? true : false;

            //  If we have options to display
            if (${ $this->generated_variables['optionsExist'] } == true) {

                ${ $this->generateUniqueVariable('number') } = null;

                ${ $this->generateUniqueVariable('text') } = "\n";

                ${ $this->generateUniqueVariable('collection') } = [];

                ${ $this->generateUniqueVariable('option') } = [];

                ${ $this->generateUniqueVariable('option_name') } = null;

                ${ $this->generateUniqueVariable('option_value') } = null;

                ${ $this->generateUniqueVariable('buildResponse') } = null;

                //  Foreach option
                for (${ $this->generateUniqueVariable('x') } = 0; ${ $this->generated_variables['x'] } < count(${ $this->generated_variables['items'] }); ++${ $this->generated_variables['x'] }) {
                    //  Generate the option number
                    ${ $this->generated_variables['number'] } = ${ $this->generated_variables['x'] } + 1;

                    //  Add the current item using our custom template reference name as additional dynamic data to our dynamic data storage
                    $this->dynamic_data_storage[${ $this->generated_variables['data_structure'] }->template_reference_name] = ${ $this->generated_variables['items'] }[${ $this->generated_variables['x'] }];

                    //  Process dynamic content embedded within the template display name
                    ${ $this->generated_variables['buildResponse'] } = $this->handleEmbeddedDynamicContentConversion(
                        //  Text containing embedded dynamic content that must be convert
                        ${ $this->generated_variables['data_structure'] }->template_display_name,
                        //  Is this text information generated using the PHP Code Editor
                        false
                    );

                    //  If the option name failed to build return the failed response otherwise continue
                    if ($this->isDisplayScreen(${ $this->generated_variables['buildResponse'] })) {
                        return ${ $this->generated_variables['buildResponse'] };
                    }

                    //  Get the built option name
                    ${ $this->generated_variables['option_name'] } = ${ $this->generated_variables['buildResponse'] };

                    //  If the provided value is a valid mustache tag
                    if( $this->isValidMustacheTag( ${ $this->generated_variables['data_structure'] }->template_value, false ) ){

                        //  Convert the mustache tag into its associated dynamic data value e.g "{{ product }}" into an array [...] of the product data
                        ${ $this->generated_variables['buildResponse'] } = $this->convertMustacheTagIntoDynamicData( ${ $this->generated_variables['data_structure'] }->template_value );

                    //  If the provided value is not a valid mustache tag
                    }else{

                        //  Process dynamic content embedded within the template value e.g "This is {{ company.name }}" into "This is Company XYZ"
                        ${ $this->generated_variables['buildResponse'] } = $this->handleEmbeddedDynamicContentConversion(
                            //  Text containing embedded dynamic content that must be convert
                            ${ $this->generated_variables['data_structure'] }->template_value,
                            //  Is this text information generated using the PHP Code Editor
                            false
                        ); 

                    }

                    //  If the option value failed to build return the failed response otherwise continue
                    if ($this->isDisplayScreen(${ $this->generated_variables['buildResponse'] })) {
                        return ${ $this->generated_variables['buildResponse'] };
                    }

                    //  Get the built option value
                    ${ $this->generated_variables['option_value'] } = ${ $this->generated_variables['buildResponse'] };

                    //  Set an info log of the option display name
                    $this->logInfo('Option name: <span class="text-success">'.${ $this->generated_variables['option_name'] }.'</span>');

                    //  Set an info log of the option value
                    $this->logInfo('Option value: <span class="text-success">'.json_encode( ${ $this->generated_variables['option_value'] } ).'</span>');


                    //  If the return type is an array format
                    if (${ $this->generated_variables['returnType'] } == 'array') {
                        //  Build the option as an array
                        ${ $this->generated_variables['option'] } = [
                            //  Get the option name
                            'name' => ${ $this->generated_variables['option_name'] },
                            //  Get the option input
                            'input' => ${ $this->generated_variables['number'] },
                            //  If the option value was not provided
                            'value' => (empty(${ $this->generated_variables['data_structure'] }->template_value))
                                    //  Use the entire option data as the value
                                    ? ${ $this->generated_variables['items'] }[${ $this->generated_variables['x'] }]
                                    //  Otherwise use the converted version of the value provided
                                    : ${ $this->generated_variables['option_value'] },
                            //  Get the option next screen
                            'next_screen' => ${ $this->generated_variables['data_structure'] }->next_screen,
                        ];

                        array_push(${ $this->generated_variables['collection'] }, ${ $this->generated_variables['option'] });

                    //  If the return type is a string format
                    } elseif (${ $this->generated_variables['returnType'] } == 'string') {
                        //  Build the option as a string
                        ${ $this->generated_variables['text'] } .= ${ $this->generated_variables['number'] }.'. '.${ $this->generated_variables['option_name'] }."\n";
                    }
                }

                //  If the return type is an array format
                if (${ $this->generated_variables['returnType'] } == 'array') {

                    //  Return the options as an array of options
                    return ${ $this->generated_variables['collection'] };

                //  If the return type is a string format
                } elseif (${ $this->generated_variables['returnType'] } == 'string') {

                    //  Build the option as a string
                    return ${ $this->generated_variables['text'] };

                }

                //  If we don't have options to display
            } else {

                //  If we have instructions to be displayed then add break lines
                ${ $this->generateUniqueVariable('text') } = (!empty($this->screenInstructions) ? "\n\n" : '');

                //  Get the custom "no results message"
                ${ $this->generateUniqueVariable('no_results_message') } = $this->screenContent['action']['select_option']['dynamic_options']['no_results_message'] ?? null;

                //  Get the custom "No options available" otherwise use default
                ${ $this->generated_variables['text'] } .= (${ $this->generated_variables['no_results_message'] } ?? 'No options available');

                //  Return the custom or default "No options available"
                return ${ $this->generated_variables['text'] };
                
            }
        } catch (\Throwable $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        } catch (Exception $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        }
    }

    /*  getCodeSelectOptions()
     *  This method builds the code options for display on the screen
     */
    public function getCodeSelectOptions($returnType = 'array')
    {
        //  Get the PHP Code
        $code = $this->screenContent['action']['select_option']['code_editor_options']['code_editor_text'] ?? "return '';";

        //  Get the custom "no results message"
        $no_results_message = $this->screenContent['action']['select_option']['code_editor_options']['no_results_message'] ?? null;

        //  Use the try/catch handles incase we run into any possible errors
        try {
            //  Set an info log that we are processing the PHP Code from the PHP Code Editor
            $this->logInfo('Process PHP Code from the Code Editor');

            //  Remove the PHP tags from the PHP Code
            $code = $this->removePHPTags($code);

            //  Process the PHP Code
            $options = eval("$code");

            if (is_array($options)) {
                //  Check if we have options to display
                $optionsExist = count($options) ? true : false;

                //  If we have options to display
                if ($optionsExist) {
                    $text = "\n";
                    $collection = [];

                    //  Foreach option
                    for ($x = 0; $x < count($options); ++$x) {
                        //  Get the current option
                        $option = $options[$x];

                        //  If the expected user input is a number e.g "1", "2" or "3"
                        if (preg_match('/[0-9]+/', $option['input'])) {
                            //  Put the number and a period infront of it e.g "1. ", "2. " or "3. "
                            $input = $option['input'].'. ';

                        //  If the expected user input is not a number e.g "#"
                        } else {
                            //  Put the character and a space infront of it e.g "# "
                            $input = $option['input'].' ';
                        }

                        if ($returnType == 'array') {
                            //  Add the option
                            array_push($collection, [
                                //  Get the option name
                                'name' => $option['name'] ?? null,
                                //  Get the option input
                                'input' => $option['input'] ?? null,
                                //  If the option value was not provided
                                'value' => (empty($option['value']))
                                        //  Use the entire option data as the value
                                        ? $option
                                        //  Otherwise use the converted version of the value provided
                                        : $option['value'],
                                //  Get the option next screen
                                'next_screen' => $option['next_screen'],
                            ]);
                        } elseif ($returnType == 'string') {
                            //  Add the option
                            $text .= $input.$option['name']."\n";
                        }
                    }

                    if ($returnType == 'array') {
                        //  Return the options
                        return $collection;
                    } elseif ($returnType == 'string') {
                        //  Return the options
                        return $text;
                    }

                    //  If we don't have options to display
                } else {
                    //  If we have instructions to be displayed then add break lines
                    $text = (!empty($this->screenInstructions) ? "\n\n" : '');

                    //  Get the custom "No options available" otherwise use default
                    $text .= ($no_results_message ?? 'No options available');

                    //  Return the custom or default "No options available"
                    return $text;
                }
            } else {
                return null;
            }
        } catch (\Throwable $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        } catch (Exception $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        }
    }

    public function generateUniqueVariable($reference_name = 'variable')
    {
        //  If the provided reference name if of type String
        if (is_string($reference_name)) {
            //  Generate a unique variable name
            $unique_variable_name = uniqid($reference_name.'_').'_'.random_int(1, 100000);

            //  Add the unique variable name to our generated variables array
            $this->generated_variables[$reference_name] = $unique_variable_name;

            //  Return the unique variable name
            return $unique_variable_name;
        }
    }

    public function convertToJsonObject($data = null)
    {
        //  If we have the data to convert
        if (!empty($data)) {
            //  Convert the data into a JSON Object and return
            return json_decode(json_encode($data));
        }

        //  Return null if we don't have any data to convert
        return null;
    }

    public function isValidMustacheTag($text = null, $log_warning = true)
    {
        //  If we have the data to verify
        if (!empty($text)) {
            //  If the data to verify is of type String
            if (is_string($text)) {

                //  Remove the (\u00a0) special character which represents a no-break space in HTML
                $text = $this->remove_HTML_No_Break_Space( $text );

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

                //  Check if the given data passes validation
                if (preg_match($pattern, $text)) {
                    //  Return true to indicate that this is a valid mustache tag
                    return true;
                }
            }
        }

        //  If we should log a warning
        if ($log_warning == true) {
            //  Incase the value received is not a string
            if (!is_string($text)) {
                $this->logWarning('The provided mustache tag is not a valid mustache tag syntax. Instead we received a value of type ['.gettype($text).']');
            } else {
                $this->logWarning('The provided mustache tag '.$text.' is not a valid mustache tag syntax');
            }
        }

        //  Return false to indicate that this is not a valid mustache tag
        return false;
    }

    /** handleTryCatchError()
     *  This method is used to handle errors caught during
     *  try-catch screnerios. It logs the error, indicates
     *  that an error occured and returns null.
     */
    public function handleTryCatchError($error, $loadDisplay = true)
    {
        //  Set an error log
        $this->logError('Error:  '.$error->getMessage());

        if ($loadDisplay) {
            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }
    }

    /** logInfo()
     *  This method is used to log information about the USSD
     *  application build process.
     */
    public function logInfo($description = '')
    {
        $data = [
            'type' => 'info',
            'description' => $description,
            'level' => $this->level ?? null,
            'screen' => $this->screen['title'] ?? null,
            'datetime' => (\Carbon\Carbon::now())->format('Y-m-d H:i:s')
        ];

        $this->updateLog($data);
    }

    /** logWarning()
     *  This method is used to log warnings about the USSD
     *  application build process.
     */
    public function logWarning($description = '')
    {
        $data = [
            'type' => 'warning',
            'description' => $description,
            'level' => $this->level ?? null,
            'screen' => $this->screen['title'] ?? null
        ];

        $this->updateLog($data);
    }

    /** logError()
     *  This method is used to log errors about the USSD
     *  application build process.
     */
    public function logError($description = '')
    {
        $data = [
            'type' => 'error',
            'description' => $description,
            'level' => $this->level ?? null,
            'screen' => $this->screen['title'] ?? null
        ];

        $this->updateLog($data);
    }

    public function updateLog($data)
    {
        //  Get the last recorded log microtime
        if( empty( $this->last_recorded_log_microtime ) ){
            
            $this->last_recorded_log_microtime = $this->getMicroTime();

        }

        //  Calculate the current log time since the last recorded log time
        $microtime_since_last_log = ( $this->getMicroTime() - $this->last_recorded_log_microtime ) / 1000;

        //  Update our log data stack
        array_push( $data, ['microtime_since_last_log', $microtime_since_last_log] );

        //  Push the latest log update
        array_push($this->log, $data);
    }

    public function getMicroTime()
    {
        return microtime(true);
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

    public function displayTechnicalDifficultiesErrorPage()
    {
        $response = $this->displayCustomErrorPage('Sorry, we are experiencing technical difficulties');

        return $response;
    }
}
