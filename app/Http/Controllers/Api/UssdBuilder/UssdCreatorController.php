<?php

namespace App\Http\Controllers\Api\UssdBuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UssdCreatorController extends Controller
{
    private $log;
    private $text;
    private $test;
    private $level;
    private $event;
    private $screen;
    private $screens;
    private $display;
    private $displays;
    private $session_id;
    private $api_trigger;
    private $linkedScreen;
    private $service_code;
    private $phone_number;
    private $screenContent;
    private $linkedDisplay;
    private $ussdInterface;
    private $is_paginating;
    private $display_content;
    private $display_actions;
    private $pagination_index;
    private $currentUserResponse;
    private $ussdBuilderMetadata;
    private $generated_variables;
    private $display_instructions;
    private $dynamic_data_storage;
    private $last_recorded_log_microtime;
    private $forward_navigation_step_number;
    private $backward_navigation_step_number;

    public function __construct(Request $request)
    {
        //  Check if we are on TEST MODE
        $this->test_mode = ($request->get('testMode') == 'true' || $request->get('testMode') == '1') ? true : false;

        //  Get the Ussd TEXT value (User Response)
        $this->text = $request->get('text');

        /*  Get the USSD Phone Number value. We use the "preg_replace" method
         *  to remove "+" symbol that comes with the phone number. This way
         *  we only keep the numbers.
         *
         *  Before: +26775993221
         *  After:  26775993221
         *
         *  If we don't have a phone number provided default to "26770123456"
         */
        $this->phone_number = $request->get('phoneNumber') ?? '26770123456';
        $this->phone_number = preg_replace('/[^0-9]/', '', $this->phone_number);

        /*  Get the Session Id  */
        $this->session_id = $request->get('sessionId');

        /*  Get the Service Code  */
        $this->service_code = $request->get('serviceCode');

        //  Get the Ussd Interface
        $this->ussdInterface = \App\UssdInterface::find(59);

        //  Get the Ussd builder data
        $this->ussdBuilderMetadata = $this->ussdInterface->metadata;

        //  Initiate the dynamic data storage to an empty array
        $this->dynamic_data_storage = [];

        $this->ussd = [
            'text' => $this->text,
            'session_id' => $this->session_id,
            'service_code' => $this->service_code,
            'phone_number' => $this->phone_number,
            'user_responses' => $this->getUserResponses(),
            'user_response' => null
        ];

        //  Store the ussd data using the given item reference name
        $this->storeDynamicData('ussd', $this->ussd);

        //  Set the default level
        $this->level = 1;

        //  Initiate the log to an empty array
        $this->log = [];

        //  Reset the display pagination settings
        $this->resetPagination();
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

    /******************************************
     *  SCREEN METHODS                        *
     *****************************************/

    /*  startBuildingUssdScreens()
     *  This method uses the ussd builder metadata get all the ussd screens,
     *  locate the first screen and start building each display screen that
     *  must be returned.
     */
    public function startBuildingUssdScreens()
    {
        //  Check if the USSD screens exist
        $doesNotExistResponse = $this->handleNonExistentScreens();

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($doesNotExistResponse)) {
            return $doesNotExistResponse;
        }

        //  Get the first screen
        $this->getFirstScreen();

        //  Handle current screen
        $response = $this->handleCurrentScreen();

        /** Check if the display data returned is greater than 160 characters.
         *  If it is set a warning log. Subtract out the first five characters
         *  first to remove the "CON " and "END "
         * 
         */ 
        $characters = (strlen($response) - 4);

        if( $characters > 160 ){
            
            //  Set a warning log that the content received is too long
            $this->logWarning('The screen content exceeds the maximum allowed content length of 160 characters. Returned <span class="text-success">'.$characters.'</span> characters');

        }else{
                 
            //  Set an info log of the content character length
            $this->logInfo('Content Characters: <span class="text-success">'.$characters.'</span> characters');

        }

        return $response;
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
            return $this->displayTechnicalDifficultiesErrorPage();
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

    /*  getFirstScreen()
     *  This method gets the first screen that we should display. First we look
     *  for a screen indicated by the user. If we can't locate that screen we
     *  then default to the first available screen that we can display.
     */
    public function getFirstScreen()
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
        $this->logInfo('Selected <span class="text-primary">'.$this->screen['name'].'</span> as the first screen');
    }

    /*  handleCurrentScreen()
     *  This method first checks if the screen we want to handle exists. This could be the
     *  first display screen or any linked screen. In either case if the screen does not
     *  exist we log a warning and display the technical difficulties page. We then check
     *  if the user has already responded to the current screen. If (No) then we build
     *  and return the current screen. If (Yes) then we need to validate, format and
     *  store the users response respectively if specified.
     */
    public function handleCurrentScreen()
    {
        //  Check if the current screen exists
        $doesNotExistResponse = $this->handleNonExistentScreen();

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($doesNotExistResponse)) {
            return $doesNotExistResponse;
        }

        //  Check if the current screen repeats
        if ($this->checkIfScreenRepeats()) {
            //  Handle before repeat events
            $handleEventsResponse = $this->handleBeforeRepeatEvents();

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($handleEventsResponse)) {
                return $handleEventsResponse;
            }

            //  Handle the repeat screen
            $handleScreenResponse = $this->handleRepeatScreen();

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($handleScreenResponse)) {
                return $handleScreenResponse;
            }

            //  Handle after repeat events
            $handleEventsResponse = $this->handleAfterRepeatEvents();

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($handleEventsResponse)) {
                return $handleEventsResponse;
            }
        } else {
            //  Start building the current screen displays
            return $this->startBuildingDisplays();
        }
    }

    public function handleNonExistentScreen()
    {
        /* Note that the checkIfScreensExist() helps us make sure that the first screen is always available.
         *  If its not available we use the handleNonExistentScreens() to take care of that. This means that
         *  we never have to worry about the first screen, however any other screen that we link to must be
         *  verified for existence.
         */

        //  If the linked screen is not available
        if (empty($this->screen)) {
            //  Set a warning log that the linked screen could not be found
            $this->logWarning('The linked screen could not be found');

            //  Return the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        return null;
    }

    public function checkIfScreenRepeats()
    {
        //  Set an info log that we are checking if the current screen repeats
        $this->logInfo('Checking if the screen should repeat');

        //  If the screen is set to repeats
        if ($this->screen['type']['selected_type'] == 'repeat') {
            //  Set an info log that the current screen does repeat
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> does repeat');

            //  Return true to indicate that the screen does repeat
            return true;
        }

        //  Set an info log that the current screen does not repeat
        $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> does not repeat');

        //  Return false to indicate that the screens does not repeat
        return false;
    }

    public function handleRepeatScreen()
    {
        //  Get the repeat type e.g "repeat_on_number" or "repeat_on_items"
        $repeatType = $this->screen['type']['repeat']['selected_type'];

        //  If the screen is set to repeats
        if ($repeatType == 'repeat_on_number') {
            //  Set an info log that the current screen repeats on a given number
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> repeats on a given number');

            //  Handle repeat screen on number
            return $this->handleRepeatScreenOnNumber();
        } elseif ($repeatType == 'repeat_on_items') {
            //  Set an info log that the current screen repeats on a set of items
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> repeats on a group of items');

            //  Handle repeat screen on items
            return $this->handleRepeatScreenOnItems();
        }
    }

    public function handleRepeatScreenOnNumber()
    {
    }

    public function handleRepeatScreenOnItems()
    {
        $repeat_data = $this->screen['type']['repeat']['repeat_on_items'];

        //  Get the group reference value (Usually in mustache tag format) e.g "{{ products }}"
        $mustache_tag = $repeat_data['group_reference'];

        //  Get the current item reference name e.g "product"
        $item_reference_name = $repeat_data['item_reference_name'];

        //  Get the total options reference name e.g "total_products"
        $total_items_reference_name = $repeat_data['total_items_reference_name'];

        //  Get the current item index reference name e.g "product_index"
        $item_index_reference_name = $repeat_data['item_index_reference_name'];

        //  Get the current item number reference name e.g "product_number"
        $item_number_reference_name = $repeat_data['item_number_reference_name'];

        //  Get the reference name for confirming if the current item is the first item e.g "is_first_product"
        $is_first_item_reference_name = $repeat_data['is_first_item_reference_name'];

        //  Get the reference name for confirming if the current item is the last item e.g "is_last_product"
        $is_last_item_reference_name = $repeat_data['is_last_item_reference_name'];

        //  Get the no results message
        $no_results_message = $repeat_data['no_results_message'];

        //  Convert "{{ products }}" into "$products"
        $variable = $this->convertMustacheTagIntoPHPVariable($mustache_tag, true);

        //  Convert the dynamic property into its dynamic value e.g "$products" into "[ ['name' => 'Product 1', ...], ... ]"
        $outputResponse = $this->processPHPCode("return $variable;");

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($outputResponse)) {
            return $outputResponse;
        }

        //  Get the generated output e.g "A list of products"
        $items = $outputResponse;

        //  If the dynamic value is a string, integer or float
        if (is_string($items) || is_integer($items) || is_float($items)) {
            //  Set an info log that we are converting the dynamic property to its associated value
            $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">'.$items.'</span>');

        //  Incase the dynamic value is not a string, integer or float
        } else {
            
            $dataType = ucwords(gettype($items));

            //  Set an info log that we are converting the dynamic property to its associated value
            $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">['.$dataType.']</span>');
        }

        //  Check if the given options are of type Array
        if (is_array($items)) {
            for ($x = 0; $x < count($items); $x++) {
                //  Set an info log that we are converting the dynamic property to its associated value
                $this->logInfo('<span class="text-success">'.$this->screen['name'].'</span> repeat instance <span class="text-success">['.($x + 1).']</span>');
                

                //  If the item reference name is provided
                if (!empty($item_reference_name)) {
                    //  Store the current item using the given item reference name
                    $this->storeDynamicData($item_reference_name, $items[$x]);
                }

                //  If the total options reference name is provided
                if (!empty($total_items_reference_name)) {
                    //  Store the current total options using the given reference name
                    $this->storeDynamicData($total_items_reference_name, count($items));
                }

                //  If the item index reference name is provided
                if (!empty($item_index_reference_name)) {
                    $this->logInfo('Index <span class="text-success">['.$x.']</span>');
                    //  Store the current item index using the given item reference name
                    $this->storeDynamicData($item_index_reference_name, $x);
                }

                //  If the item number reference name is provided
                if (!empty($item_number_reference_name)) {
                    $this->logInfo('Number <span class="text-success">['.($x + 1).']</span>');
                    //  Store the current item number using the given item reference name
                    $this->storeDynamicData($item_number_reference_name, ($x + 1));
                }

                //  If the first item reference name is provided
                if (!empty($is_first_item_reference_name)) {
                    //  Store the true/false result for first item using the given item reference name
                    $this->storeDynamicData($is_first_item_reference_name, ($x == 0));
                }

                //  If the last item reference name is provided
                if (!empty($is_last_item_reference_name)) {
                    //  Store the true/false result for last item using the given item reference name
                    $this->storeDynamicData($is_last_item_reference_name, (($x + 1) == count($items)));
                }

                //  Start building the current screen displays
                $buildResponse = $this->startBuildingDisplays();

                //  If we must navigate forward then proceed to next iteration otherwise continue
                if ($buildResponse == 'navigate-forward') {

                    /** Use the forward navigation step number to decide which next iteration to target. For instance if 
                     *  the number we receive equals 1 it means target the first next item. If the number we receive 
                     *  equals 2 it means target the second next item. This is of course we assume the item in that 
                     *  requested position exists. If it does not exist we work backwards to target the closest 
                     *  available item. For instance lets assume we have items in position 1, 2, 3 and 4. We are
                     *  currently in position 1. If the step number equals "1" we target item in position "2". 
                     *  If the step number equals "2" we target item in position "3" and so on. Now lets 
                     *  assume we have number equals "4", this means we target item in position "5" but 
                     *  such an item does not exist. This means we work backwards to target item in 
                     *  position "4" instead.
                     * 
                     *  $this->forward_navigation_step_number = 1, 2, 3 ... e.t.c
                     */

                    $step = $this->forward_navigation_step_number;
                    
                    /** Assume $step = 5, this means we want to skip to every 5th item. 
                     *  
                     *  If $y = 0 ; This means we are currently targeting [Item 1]
                     *  
                     *  If $step = 5; This means we want to target item of index number "5" [Item 6] (if it exists).
                     *  Note that item of index "5" is actually [Item 6]. A simple way to see this
                     *  is in this manner:
                     * 
                     *  [Item 1] + 5 steps = [Item 6]
                     * 
                     *  Visual example with $step = 5
                     *  --------------------------------------------------------
                     *  From    [1] 2  3  4  5  6  7  8  9  10  11  12 ...
                     *  To       1  2  3  4  5 [6] 7  8  9  10  11  12 ...
                     *  ...      1  2  3  4  5  6  7  8  9  10 [11] 12 ...
                     *           .  .  .  .  .  .  .  .  .   .   .   . 
                     *           .  .  .  .  .  .  .  .  .   .   .   . 
                     *  --------------------------------------------------------
                     *  Indexes: 0  1  2  3  4  5  6  7  8   9  10  11
                     *  --------------------------------------------------------
                     *  
                     *  Translated into index format:
                     * 
                     *  [Item Index 0] + 5 steps = [Item Index 5]
                     * 
                     */
                    
                    for($y = $step; $y >= 1; --$y){
                        
                        // Example: For $y = 5 ... 4 ... 3 ... 2 ... 1

                        /** Note $items[$x] targets the current item and $items[$x + $y] targets the next item.
                         *  If the item we want to target does not exist, then we attempt to target the item
                         *  before it. We repeat this until we can get an existing item to target.
                         * 
                         *  Example: If we wanted to target [item 6] but it does not exist, then we try to 
                         *  target [item 5], then [item 4] and so on... If we reach a point where no items
                         *  after [item 1] can be found then we do not iterate anymore.
                         */

                        if( isset( $items[$x + $y] ) ){
                            
                            $this->logInfo('Navigating to <span class="text-success">Item #' . ($x + $y + 1) . '</span>');

                            /** If the item exists then we need to alter the parent for($x){ ... } method to target
                             *  the item we want.
                             * 
                             *  Lets assume [item 6] was found 5 steps after [item 1]. Since normally the for($x){ ... }
                             *  would increment the $x value by only (1), we need to alter its bahaviour to increment
                             *  based on the $y value we have. Basically to target the item we want we will use:
                             * 
                             *  $items[index] where index = ($x + $y)
                             * 
                             *  However on the next iteration the index value will be incremented by (1) and the result
                             *  will be:
                             * 
                             *  $items[index] where index = ($x + $y + 1)
                             * 
                             *  To counteract this result we must make sure that the index value is decremented by (1)
                             *  i.e index = ($x + $y - 1) so that on next iteration index = ($x + $y - 1 + 1) giving
                             *  us the final output of index = ($x + $y) to target the item we want
                             */
                            $x = ($x + $y - 1);

                            //  Stop the current loop
                            break 1;

                        }

                    }

                    //  Do nothing else so that we iterate to the next specified item on the list

                }else if ($buildResponse == 'navigate-backward') {

                    /** Use the forward navigation step number to decide which next iteration to target. For instance if 
                     *  the number we receive equals 1 it means target the first previous item. If the number we receive 
                     *  equals 2 it means target the second previous item. This is of course we assume the item in that 
                     *  requested position exists. If it does not exist we work forward to target the closest available
                     *  item. For instance lets assume we have items in position 1, 2, 3 and 4. We are currently in
                     *  position 4. If the step number equals "1" we target item in position "3". If the step number
                     *  equals "2" we target item in position "2" and so on. Now lets assume we have number equals "4", 
                     *  this means we target item in position "0" but such an item does not exist. This means we work 
                     *  forward to target item in position "1" instead.
                     * 
                     *  $this->backward_navigation_step_number = 1, 2, 3 ... e.t.c
                     */

                    $step = $this->backward_navigation_step_number;
                    
                    /** Assume $step = 5, this means we want to skip to every previous 5th item. 
                     *  
                     *  If $y = 10 ; This means we are currently targeting [Item 11]
                     *  
                     *  If $step = 5; This means we want to target item of index number "5" [Item 6] (if it exists).
                     *  Note that item of index "5" is actually [Item 6]. A simple way to see this
                     *  is in this manner:
                     * 
                     *  [Item 11] - 5 steps = [Item 6]
                     * 
                     *  Visual example with $step = 5
                     *  --------------------------------------------------------
                     *  From     1  2  3  4  5  6  7  8  9  10 [11] 12 ...
                     *  To       1  2  3  4  5 [6] 7  8  9  10  11  12 ...
                     *  ...     [1] 2  3  4  5  6  7  8  9  10  11  12 ...
                     *           .  .  .  .  .  .  .  .  .   .   .   . 
                     *           .  .  .  .  .  .  .  .  .   .   .   . 
                     *  --------------------------------------------------------
                     *  Indexes: 0  1  2  3  4  5  6  7  8   9  10  11
                     *  --------------------------------------------------------
                     *  
                     *  Translated into index format:
                     * 
                     *  [Item Index 10] - 5 steps = [Item Index 5]
                     * 
                     */
                    
                    for($y = $step; $y >= 0; --$y){
                        
                        // Example: For $y = 5 ... 4 ... 3 ... 2 ... 1 ... 0

                        /** Note $items[$x] targets the current item and $items[$x - $y] targets the previous item.
                         *  If the item we want to target does not exist, then we attempt to target the item
                         *  after it. We repeat this until we can get an existing item to target.
                         * 
                         *  Example: If we wanted to target [item -1] but it does not exist, then we try to 
                         *  target [item 0], then [item 1] and so on... If we reach a point where no items
                         *  after [item -1] can be found then we do not iterate anymore.
                         */

                        if( isset( $items[$x - $y] ) ){
                            
                            $this->logInfo('Navigating to <span class="text-success">Item #' . ($x - $y + 1) . '</span>');

                            /** If the item exists then we need to alter the parent for($x){ ... } method to target
                             *  the item we want.
                             * 
                             *  Lets assume [item 6] was found 5 steps before [item 11]. Since normally the for($x){ ... }
                             *  would increment the $x value by only (1), we need to alter its bahaviour to increment
                             *  based on the $y value we have. Basically to target the item we want we will use:
                             * 
                             *  $items[index] where index = ($x - $y)
                             * 
                             *  However on the next iteration the index value will be incremented by (1) and the result
                             *  will be:
                             * 
                             *  $items[index] where index = ($x - $y + 1)
                             * 
                             *  To counteract this result we must make sure that the index value is decremented by (1)
                             *  i.e index = ($x - $y - 1) so that on next iteration index = ($x - $y - 1 + 1) giving
                             *  us the final output of index = ($x - $y) to target the item we want
                             */

                            //return 'CON $x = '.$x.' $y = '.$y;

                            $x = ($x - $y - 1);

                            //return 'CON Final $x = '.$x;

                            //  Stop the current loop
                            break 1;

                        }

                    }

                    //  If we reached this area, then we could not find any

                    //  Do nothing else so that we iterate to the next specified item on the list

                } else {

                    //  If we have a screen to show return the response otherwise continue
                    if ($this->shouldDisplayScreen($buildResponse)) return $buildResponse;

                }
            }
        } else {
            
            $dataType = ucwords(gettype($items));

            //  Set a warning log that the dynamic property is not an array
            $this->logWarning('The <span class="text-success">'.$mustache_tag.'</span> provided must be of type <span class="text-success">[Array]</span> however we received type of <span class="text-success">['.$dataType.']</span>. For this reason we cannot repeat on options');
        }
    }

    /******************************************
     *  DISPLAY METHODS                        *
     *****************************************/

    /*  startBuildingDisplays()
     *  This method uses the current screen to get all the displays,
     *  locate the first display and start building each display that
     *  must be returned.
     */
    public function startBuildingDisplays()
    {
        //  Check if the current screen displays exist
        $doesNotExistResponse = $this->handleNonExistentDisplays();

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($doesNotExistResponse)) {
            return $doesNotExistResponse;
        }

        //  Get the first display
        $this->getFirstDisplay();

        //  Handle current display
        return $this->handleCurrentDisplay();
    }

    /*  handleNonExistentDisplays()
     *  This method checks if we have any displays. If we don't we
     *  log a warning and display the technical difficulties page.
     */
    public function handleNonExistentDisplays()
    {
        //  Check if the displays exist
        if ($this->checkIfDisplaysExist() != true) {
            //  Set a warning log that we could not find any displays
            $this->logWarning('No displays found');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        //  Return null if we have displays
        return null;
    }

    /*  checkIfDisplaysExist()
     *  This method checks if the current screen has any displays.
     *  It will return true if we have displays and false if we don't
     *  have displays.
     */
    public function checkIfDisplaysExist()
    {
        //  Check if the current screen displays is an array that its not empty
        if (is_array($this->screen['displays']) && !empty($this->screen['displays'])) {
            //  Return true to indicate that the displays exist
            return true;
        }

        //  Return false to indicate that the displays do not exist
        return false;
    }

    /*  getFirstDisplay()
     *  This method gets the first display of the current screen. First we look
     *  for a display indicated by the user. If we can't locate that display we
     *  then default to the first available display we can find.
     */
    public function getFirstDisplay()
    {
        //  Set an info log that we are searching for the first display
        $this->logInfo('Searching for the first display');

        //  Get all the displays available
        $this->displays = $this->screen['displays'];

        //  Get the first display (The one specified by the user)
        $this->display = collect($this->displays)->where('first_display', true)->first() ?? null;

        //  If we did not manage to get the first display specified by the user
        if (!$this->display) {
            //  Set a warning log that the default starting display was not found
            $this->logWarning('Default starting display was not found');

            //  Set an info log that we will use the first available display
            $this->logInfo('Selecting the first available display as the default starting display');

            //  Select the first display on the available displays by default
            $this->display = $this->displays[0];
        }

        //  Set an info log for the first selected display
        $this->logInfo('Selected <span class="text-primary">'.$this->display['name'].'</span> as the first display');
    }

    /*  handleCurrentDisplay()
     *  This method first checks if the display we want to handle exists. This could be the
     *  first display screen or any linked display. In either case if the display does not
     *  exist we log a warning and return the technical difficulties page. We then check
     *  if the user has already responded to the current display. If (No) then we build
     *  and return the current display. If (Yes) then we need to validate, format and
     *  store the users response if specified.
     */
    public function handleCurrentDisplay()
    {
        //  Check if the current display exists
        $doesNotExistResponse = $this->handleNonExistentDisplay();

        //  If the current display does not exist return the response otherwise continue
        if ($this->shouldDisplayScreen($doesNotExistResponse)) {
            return $doesNotExistResponse;
        }

        //  Handle before display events
        $handleEventsResponse = $this->handleBeforeResponseEvents();

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($handleEventsResponse)) {
            return $handleEventsResponse;
        }

        //  Build the current screen display
        $builtDisplay = $this->buildCurrentDisplay();

        //  Check if the user has already responded to the current display screen
        if ($this->completedLevel($this->level)) {

            //  Get the user response (Input provided by the user) for the current display screen
            $this->getCurrentScreenUserResponse();

            //  Store the user response (Input provided by the user) as a named dynamic variable
            $storeInputResponse = $this->storeCurrentScreenUserResponseAsDynamicVariable();

            //  Handle after display events
            $handleEventsResponse = $this->handleAfterResponseEvents();

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($handleEventsResponse)) {
                return $handleEventsResponse;
            }

            //  Handle forward navigation
            $handleForwardNavigationResponse = $this->handleForwardNavigation();

            //  If we have any returned data return the response otherwise continue
            if (!empty($handleForwardNavigationResponse)){

                $this->resetPagination();

                return $handleForwardNavigationResponse;

            } 

            //  Handle backward navigation
            $handleBackwardNavigationResponse = $this->handleBackwardNavigation();

            //  If we have any returned data return the response otherwise continue
            if (!empty($handleBackwardNavigationResponse)){

                $this->resetPagination();

                return $handleBackwardNavigationResponse;

            }

            //  Handle linking display
            $handleLinkingDisplayResponse = $this->handleLinkingDisplay();

            //  If we have any returned data return the response otherwise continue
            if (!empty($handleLinkingDisplayResponse)){

                return $handleLinkingDisplayResponse;

            }

        }

        return $builtDisplay;
    }

    public function handleNonExistentDisplay()
    {
        /* Note that the checkIfDisplaysExist() helps us make sure that the first display is always available.
         *  If its not available we use the handleNonExistentDisplays() to take care of that. This means that
         *  we never have to worry about the first display, however any other displays that we link to must be
         *  verified for existence.
         */

        //  If the linked display is not available
        if (empty($this->display)) {
            //  Set a warning log that the linked display could not be found
            $this->logWarning('The linked display could not be found');

            //  Return the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        return null;
    }

    /*  buildCurrentDisplay()
     *  Build the current display
     *
     */
    public function buildCurrentDisplay()
    {
        //  Set an info log that we are building the display
        $this->logInfo('Start building display <span class="text-primary">'.$this->display['name'].'</span>');

        //  Build the display instruction
        $instructionsBuildResponse = $this->buildDisplayInstructions();

        //  If the instructions failed to build return the failed response otherwise continue
        if ($this->shouldDisplayScreen($instructionsBuildResponse)) {
            return $instructionsBuildResponse;
        }

        //  Get the built display instructions (E,g Welcome to Company XYZ)
        $this->display_instructions = $instructionsBuildResponse;

        //  Build the display actions (E.g Select options)
        $actionBuildResponse = $this->buildDisplayActions();

        //  If the display actions failed to build return the failed response otherwise continue
        if ($this->shouldDisplayScreen($actionBuildResponse)) {
            return $actionBuildResponse;
        }

        //  Build the display actions (E.g Select options)
        $this->display_actions = $actionBuildResponse;

        //  Get the display instruction and action
        $this->display_content = $this->display_instructions.$this->display_actions;
        
        //  Handle the display pagination
        $outputResponse = $this->handlePagination();

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($outputResponse)) return $outputResponse;

        //  If the processed instructions and action are not empty
        if (!empty( $this->display_content )) {
            
            //  Set an info log of the final result
            $this->logInfo('Final result: <br /><span class="text-success">'.$this->display_content.'</span>');

        }

        //  Return the display instruction and action
        return $this->displayCustomPage($this->display_content);
    }

    public function buildDisplayInstructions()
    {
        //  Check if the current display uses "Code Editor Mode"
        $uses_code_editor_mode = $this->display['content']['description']['code_editor_mode'] ?? false;

        //  If the current display instructions uses the PHP Code Editor
        if ($uses_code_editor_mode == true) {
            //  Set an info log that the current display uses the PHP Code Editor to build display instructions
            $this->logInfo('Display <span class="text-primary">'.$this->display['name'].'</span> uses the PHP Code Editor to build instructions');

            //  Get the display instructions code otherwise default to a return statement that returns an empty string
            $instruction_text = $this->display['content']['description']['code_editor_text'] ?? "return '';";

        //  If the current content instructions/description does not use the PHP Code Editor
        } else {
            //  Set an info log that the current display uses does not use the PHP Code Editor to build screen instructions
            $this->logInfo('Display <span class="text-primary">'.$this->display['name'].'</span> does not use the PHP Code Editor to build instructions');

            //  Get the display description text otherwise default to an empty string
            $instruction_text = $this->display['content']['description']['text'] ?? '';
        }

        //  Process dynamic content embedded within the display instructions
        return $this->handleEmbeddedDynamicContentConversion($instruction_text, $uses_code_editor_mode);
    }

    public function buildDisplayActions()
    {
        //  Get the current display expected action type
        $displayActionType = $this->getDisplayActionType();

        //  If the action is to select an option e.g 1, 2 or 3
        if ($displayActionType == 'select_option') {
            //  Get the current display expected select action type e.g static_options
            $displaySelectOptionType = $this->getDisplaySelectOptionType();

            //  If the select options are basic static options
            if ($displaySelectOptionType == 'static_options') {
                return $this->getStaticSelectOptions('string');

            //  If the select option are dynamic options
            } elseif ($displaySelectOptionType == 'dynamic_options') {
                return $this->getDynamicSelectOptions('string');

            //  If the select option are generated via the code editor
            } elseif ($displaySelectOptionType == 'code_editor_options') {
                return $this->getCodeSelectOptions('string');
            }
        }
    }

    /*  getDisplayActionType()
     *  This method gets the type of action requested by the current screen
     *
     */
    public function getDisplayActionType()
    {
        //  Available type: "no_action", "input_value" and "select_option"
        return $this->display['content']['action']['selected_type'] ?? '';
    }

    /*  getDisplaySelectOptionType()
     *  This method gets the type of "Select Option" requested by the current screen
     *
     */
    public function getDisplaySelectOptionType()
    {
        //  Available type: "static_options", "dynamic_options" and "code_editor_options"
        return $this->display['content']['action']['select_option']['selected_type'] ?? '';
    }

    /*  getStaticSelectOptions()
     *  This method builds the static select options for display on the display
     */
    public function getStaticSelectOptions($returnType = 'array')
    {
        /** Get the available static options
         *
         *  Example Structure:.
         *
         *  [
         *      [
         *          "name": "1. My Messages ({{ messages.total }})",
         *          "value" => [
         *               "text" => "",
         *               "code_editor_text" => "",
         *               "code_editor_mode" => false
         *           ],
         *           "input" => "1",
         *           "separator" => [
         *               "top" => "---",
         *               "bottom" => "---"
         *           ],
         *           "link" => [
         *               "type" => "screen",        //  screen, display
         *               "name" => "messages"
         *           ]
         *      ],
         *      ...
         *  ]
         *
         *  Structure Definition
         *
         *  name:   Represents the display name of the option (What the user will see)
         *  value:  Represents the actual value of the option (What will be stored)
         *  link:   The screen or display to link to when this option is selected
         *  separator: The top and bottom characters to use as a separator
         *  input:  What the user must input to select this option
         */
        $options = $this->display['content']['action']['select_option']['static_options']['options'] ?? [];

        //  Get the custom "no results message"
        $no_results_message = $this->display['content']['action']['select_option']['static_options']['no_results_message'] ?? null;

        //  Check if we have options to display
        $optionsExist = count($options) ? true : false;

        //  If we have options to display
        if ($optionsExist) {
            $text = "\n";
            $collection = [];

            //  Foreach option
            for ($x = 0; $x < count($options); $x++) {
                //  Get the current option
                $curr_option = $options[$x];
                $curr_option_name = $options[$x]['name'];
                $curr_option_value = $options[$x]['value'];

                //  Generate the option number
                $curr_option_number = $x + 1;

                /*************************
                 * BUILD OPTION NAME     *
                 ************************/

                //  Process dynamic content embedded within the option display name
                $buildResponse = $this->handleEmbeddedDynamicContentConversion(
                    //  Text containing embedded dynamic content that must be convert
                    $curr_option_name,
                    //  Is this text information generated using the PHP Code Editor
                    false
                );

                //  If we have a screen to show return the response otherwise continue
                if ($this->shouldDisplayScreen($buildResponse)) {
                    return $buildResponse;
                }

                //  Get the built option name
                $option_name = $buildResponse;

                //  Set an info log of the option display name
                $this->logInfo('Option name: <span class="text-success">'.$option_name.'</span>');

                /*************************
                 * BUILD OPTION VALUE     *
                 ************************/

                $option_value = null;

                if (!empty($curr_option_value['text']) || !empty($curr_option_value['code_editor_text'])) {
                    //  Check if the current option value uses "Code Editor Mode"
                    $uses_code_editor_mode = $curr_option_value['code_editor_mode'] ?? false;

                    //  If we are not using Code Editor Mode and the provided option value is a valid mustache tag
                    if ($uses_code_editor_mode == false && $this->isValidMustacheTag($curr_option_value, false)) {
                        $mustache_tag = $curr_option_value;

                        // Convert the mustache tag into dynamic data
                        $outputResponse = $this->convertMustacheTagIntoDynamicData($mustache_tag);

                        //  If we have a screen to show return the response otherwise continue
                        if ($this->shouldDisplayScreen($outputResponse)) {
                            return $outputResponse;
                        }

                        //  Get the mustache tag dynamic data and use it as the option value
                        $option_value = $outputResponse;

                    //  If the provided value is not a valid mustache tag
                    } else {
                        //  If the current option value uses the PHP Code Editor
                        if ($uses_code_editor_mode == true) {
                            //  Set an info log that the current option uses the PHP Code Editor to build its value
                            $this->logInfo('<span class="text-success">'.$option_name.'</span> uses the PHP Code Editor to build its value');

                            //  Get the option code otherwise default to a return statement that returns an empty string
                            $curr_option_value_text = $curr_option_value['code_editor_text'] ?? "return '';";

                        //  If the current content option value does not use the PHP Code Editor
                        } else {
                            //  Set an info log that the option value does not use the PHP Code Editor to build its value
                            $this->logInfo('<span class="text-success">'.$option_name.'</span> does not use the PHP Code Editor to build its value');

                            //  Get the display description text otherwise default to an empty string
                            $curr_option_value_text = $curr_option_value['text'] ?? '';
                        }

                        //  Process dynamic content embedded within the template value
                        $buildResponse = $this->handleEmbeddedDynamicContentConversion(
                            //  Text containing embedded dynamic content that must be convert
                            $curr_option_value_text,
                            //  Is this text information generated using the PHP Code Editor
                            $uses_code_editor_mode
                        );

                        //  If we have a screen to show return the response otherwise continue
                        if ($this->shouldDisplayScreen($buildResponse)) {
                            return $buildResponse;
                        }

                        //  Get the built option value
                        $option_value = $buildResponse;
                    }
                }

                //  Set an info log of the option value
                //  Use json_encode($option_value) to show $option_value data instead of gettype($option_value)
            
                $dataType = ucwords(gettype($option_value));
                $this->logInfo('Option value: <span class="text-success">['.$dataType.']</span>');

                //  If the return type is an array format
                if ($returnType == 'array') {
                    //  Build the option as an array
                    $option = [
                        //  Get the option name
                        'name' => $option_name,
                        //  Get the option input
                        'input' => $curr_option_number,
                        //  If the option value was not provided
                        'value' => (is_null($option_value))
                                //  Use the entire option data as the value
                                ? $options[$x]
                                //  Otherwise use the converted version of the value provided
                                : $option_value,
                        //  Get the option link
                        'link' => $options[$x]['link'],
                    ];

                    //  Add the option to the rest of our options
                    array_push($collection, $option);

                //  If the return type is a string format
                } elseif ($returnType == 'string') {
                    //  If we have a top separator
                    if (!empty($curr_option['separator']['top'])) {
                        $text .= $curr_option['separator']['top']."\n";
                    }

                    //  Build the option as a string
                    $text .= $option_name."\n";

                    //  If we have a bottom separator
                    if (!empty($curr_option['separator']['bottom'])) {
                        $text .= $curr_option['separator']['bottom']."\n";
                    }
                }
            }

            if ($returnType == 'array') {
                //  Return the collection of options as an array
                return $collection;
            } elseif ($returnType == 'string') {
                //  Return the options as text
                return $text;
            }

            //  If we don't have options to display
        } else {
            //  If we have instructions to be displayed then add break lines
            $text = (!empty($this->display_instructions) ? "\n\n" : '');

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
        /** Get the dynamic select options data
         *
         *  Example Structure:.
         *
         *  [
         *      "group_reference" => "{{ options }}",
         *      "template_reference_name" => "item",
         *      "template_display_name" => "{{ item.name }} - {{ item.price }}",
         *      "template_value" => [
         *          "text" => "",
         *          "code_editor_text" => "",
         *          "code_editor_mode" => false
         *      ],
         *      "reference_name" => "selected_item",
         *      "no_results_message" => "No options found",
         *      "incorrect_option_selected_message" => "You selected an incorrect option. Please try again",
         *      "link" => [
         *          "type" => "screen",
         *          "name" => ""
         *      ]
         *  ]
         */
        $data_structure = $this->display['content']['action']['select_option']['dynamic_options'] ?? null;

        $mustache_tag = $data_structure['group_reference'] ?? null;
        $template_reference_name = $data_structure['template_reference_name'] ?? null;
        $template_display_name = $data_structure['template_display_name'] ?? null;
        $template_value = $data_structure['template_value'] ?? null;

        //  Get the custom "no results message"
        $no_results_message = $data_structure['no_results_message'] ?? null;

        //  Get the next display or screen link
        $link = $data_structure['link'] ?? null;

        //  Check if the dynamic options data exists
        if (empty($data_structure)) {
            //  Set an warning log that the dynamic options data does not exist
            $this->logWarning('The data required to build the dynamic options does not exist');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        //  Check if the dynamic options data is an array
        if (!is_array($data_structure)) {
            //  Set an warning log that the dynamic options data does not exist
            $this->logWarning('The data required to build the dynamic options must be of type [Array]');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        // If mustache tags are not provided
        if (empty($mustache_tag)) {
            //  Set an warning log that the group reference value does not exist
            $this->logWarning('The group reference mustache tag was not provided on the Dynamic Select Option and therefore we cannot create the dynamic select options');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        // If mustache tags are not valid
        if (!$this->isValidMustacheTag($mustache_tag)) {
            //  Set an warning log that the group reference value does not exist
            $this->logWarning('The given group reference mustache tag provided on the Dynamic Select Option is not a valid mustache syntax and therefore we cannot create the dynamic select options');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        // If the template reference name is not provided
        if (empty($template_reference_name)) {
            //  Set an warning log that the group reference value does not exist
            $this->logWarning('The template reference name was not provided on the Dynamic Select Option and therefore we cannot create the dynamic select options');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        // Convert the mustache tag into dynamic data
        $outputResponse = $this->convertMustacheTagIntoDynamicData($mustache_tag);

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($outputResponse)) {
            return $outputResponse;
        }

        $options = $outputResponse;

        /** Note that empty arrays ( i.e = [] ) are converted to null values due to the convertToJsonObject() method.
         *  This method is executed while running the convertMustacheTagIntoDynamicData() to get the options dynamic
         *  data. This means it is very possible that the value of $options may be an empty array eventhough running
         *  gettype() may return "null" instead of type of "array". This means that we must first check if the
         *  value is null before we can check if this is of type of "array". We will then later treat this null
         *  value as an empty array and display an "no results message".
         */
        if (!is_null($options)) {
            //  Check if the variable is of type [Array] - Use PHP is_array() to check.
            if (!is_array($options)) {
                $dataType = ucwords(gettype($options));
                $providedMustacheTag = $data_structure['group_reference'];

                //  Set an warning log that the group reference value must be of type array.
                $this->logWarning('The given group reference mustache tag <span class="text-success">'.$providedMustacheTag.'</span> must be of type <span class="text-success">[Array]</span> however we received a value of type <span class="text-success">['.$dataType.']</span> therefore we cannot create the dynamic select options');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();
            }
        }

        /* NOTE:
         *
         *  We only continue if the given options value is of type [Null] or [Array]. We allow type = [Null]
         *  since the convertToJsonObject() converts empty arrays ( i.e = [] ) into [Null] values. Simply
         *  put, if options is of type [Array] then it contains options, however if its of type [Null]
         *  then it contains no options. therefore we allow
         *
         *  $options = [ ... ] or
         *  $options = Null
         *
         */

        //  Use the try/catch handles incase we run into any possible errors
        try {
            //  Set an info log that we are starting to list the dynamic options
            $this->logInfo('Start listing dynamic options');

            /** Check if we have options to display
             *  The options must not be empty or null (i.e $options != [] and $options != null).
             */
            $optionsExist = (!empty($options) && !is_null($options)) ? true : false;

            //  If we have options to display
            if ($optionsExist == true) {
                $text = "\n";
                $collection = [];

                //  Foreach option
                for ($x = 0; $x < count($options); $x++) {
                    //  Generate the option number
                    $number = $x + 1;

                    //  Add the current item using our custom template reference name as additional dynamic data to our dynamic data storage
                    $this->storeDynamicData($template_reference_name, $options[$x]);

                    /*************************
                     * BUILD OPTION NAME     *
                     ************************/

                    //  Process dynamic content embedded within the template display name
                    $buildResponse = $this->handleEmbeddedDynamicContentConversion(
                        //  Text containing embedded dynamic content that must be convert
                        $template_display_name,
                        //  Is this text information generated using the PHP Code Editor
                        false
                    );

                    //  If we have a screen to show return the response otherwise continue
                    if ($this->shouldDisplayScreen($buildResponse)) {
                        return $buildResponse;
                    }

                    //  Get the built option name
                    $option_name = $buildResponse;

                    //  Set an info log of the option display name
                    $this->logInfo('Option name: <span class="text-success">'.$option_name.'</span>');

                    /*************************
                     * BUILD OPTION VALUE     *
                     ************************/

                    $option_value = null;

                    if (!empty($template_value['text']) || !empty($template_value['code_editor_text'])) {
                        //  Check if the current option value uses "Code Editor Mode"
                        $uses_code_editor_mode = $template_value['code_editor_mode'] ?? false;

                        //  If we are not using Code Editor Mode and the provided option value is a valid mustache tag
                        if ($uses_code_editor_mode == false && $this->isValidMustacheTag($template_value['text'], false)) {
                            $mustache_tag = $template_value['text'];

                            // Convert the mustache tag into dynamic data
                            $outputResponse = $this->convertMustacheTagIntoDynamicData($mustache_tag);

                            //  If we have a screen to show return the response otherwise continue
                            if ($this->shouldDisplayScreen($outputResponse)) {
                                return $outputResponse;
                            }

                            //  Get the mustache tag dynamic data and use it as the option value
                            $option_value = $outputResponse;

                        //  If the provided value is not a valid mustache tag
                        } else {
                            //  If the current option value uses the PHP Code Editor
                            if ($uses_code_editor_mode == true) {
                                //  Set an info log that the current option uses the PHP Code Editor to build its value
                                $this->logInfo('<span class="text-success">'.$option_name.'</span> uses the PHP Code Editor to build its value');

                                //  Get the option code otherwise default to a return statement that returns an empty string
                                $template_value_text = $template_value['code_editor_text'] ?? "return '';";

                            //  If the current content option value does not use the PHP Code Editor
                            } else {
                                //  Set an info log that the option value does not use the PHP Code Editor to build its value
                                $this->logInfo('<span class="text-success">'.$option_name.'</span> does not use the PHP Code Editor to build its value');

                                //  Get the display description text otherwise default to an empty string
                                $template_value_text = $template_value['text'] ?? '';
                            }

                            //  Process dynamic content embedded within the template value
                            $buildResponse = $this->handleEmbeddedDynamicContentConversion(
                                //  Text containing embedded dynamic content that must be convert
                                $template_value_text,
                                //  Is this text information generated using the PHP Code Editor
                                $uses_code_editor_mode
                            );

                            //  If we have a screen to show return the response otherwise continue
                            if ($this->shouldDisplayScreen($buildResponse)) {
                                return $buildResponse;
                            }

                            //  Get the built option value
                            $option_value = $buildResponse;
                        }
                    }

                    //  Set an info log of the option value
                    //  Use json_encode($option_value) to show $option_value data instead of gettype($option_value)
                    $dataType = ucwords(gettype($option_value));
                    $this->logInfo('Option value: <span class="text-success">['.$dataType.']</span>');

                    //  If the return type is an array format
                    if ($returnType == 'array') {
                        //  Build the option as an array
                        $option = [
                            //  Get the option name
                            'name' => $option_name,
                            //  Get the option input
                            'input' => $number,
                            //  If the option value was not provided
                            'value' => (is_null($option_value))
                                    //  Use the entire option data as the value
                                    ? $options[$x]
                                    //  Otherwise use the converted version of the value provided
                                    : $option_value,
                            //  Get the option link
                            'link' => $link,
                        ];

                        //  Add the option to the rest of our options
                        array_push($collection, $option);

                    //  If the return type is a string format
                    } elseif ($returnType == 'string') {
                        //  Build the option as a string
                        $text .= $number.'. '.$option_name."\n";
                    }
                }

                if ($returnType == 'array') {
                    //  Return the collection of options as an array
                    return $collection;
                } elseif ($returnType == 'string') {
                    //  Return the options as text
                    return $text;
                }

                //  If we don't have options to display
            } else {
                //  If we have instructions to be displayed then add break lines
                $text = (!empty($this->display_instructions) ? "\n\n" : '');

                //  Get the custom "No options available" otherwise use default
                $text .= ($no_results_message ?? 'No options available');

                //  Return the custom or default "No options available"
                return $text;
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
        $code = $this->display['content']['action']['select_option']['code_editor_options']['code_editor_text'] ?? 'return null;';

        //  Get the custom "no results message"
        $no_results_message = $this->display['content']['action']['select_option']['code_editor_options']['no_results_message'] ?? null;

        //  Use the try/catch handles incase we run into any possible errors
        try {
            //  Set an info log that we are processing the PHP Code from the PHP Code Editor
            $this->logInfo('Process PHP Code from the Code Editor');

            //  Remove the PHP tags from the PHP Code
            $code = $this->removePHPTags($code);

            //  Process the PHP Code
            $outputResponse = $this->processPHPCode("$code");

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($outputResponse)) {
                return $outputResponse;
            }

            //  Get the options
            $options = $outputResponse;

            if (is_array($options)) {
                //  Check if we have options to display
                $optionsExist = count($options) ? true : false;

                //  If we have options to display
                if ($optionsExist) {
                    $text = "\n";
                    $collection = [];

                    //  Foreach option
                    for ($x = 0; $x < count($options); $x++) {
                        //  Get the current option
                        $option = $options[$x];

                        //  If the option name was not provided
                        if (!isset($option['name']) || empty($option['name'])) {
                            //  Set an warning log that the option name  was not provided
                            $this->logWarning('The <span class="text-success">Option Name</span> is not provided');

                        //  If the option name is not a type of [String] or [Integer]
                        } elseif (!is_string($option['name'] || is_integer($option['name']))) {
                            $dataType = ucwords(gettype($option['name']));

                            //  Set an warning log that the option name must be of type [String].
                            $this->logWarning('The given <span class="text-success">Option Name</span> must return data of type <span class="text-success">[String]</span> or <span class="text-success">[Interger]</span> however we received a value of type <span class="text-success">['.$dataType.']</span>');

                        //  If the option input was not provided
                        } elseif (!isset($option['input']) || empty($option['input'])) {
                            //  Set an warning log that the option name  was not provided
                            $this->logWarning('The <span class="text-success">Option Input</span> is not provided');

                        //  If the option input is not a type of [String] or [Integer]
                        } elseif (!is_string($option['input'] || is_integer($option['input']))) {
                            $dataType = ucwords(gettype($option['name']));

                            //  Set an warning log that the option input must be of type [String].
                            $this->logWarning('The given <span class="text-success">Option Input</span> must return data of type <span class="text-success">[String]</span> or <span class="text-success">[Interger]</span> however we received a value of type <span class="text-success">['.$dataType.']</span>');

                        //  If the option link was set but is not of type [Array]
                        } elseif (isset($option['link']) && !is_array($option['link'])) {
                            $dataType = ucwords(gettype($option['link']));

                            //  Set an warning log that the option input must be of type [String].
                            $this->logWarning('The given <span class="text-success">Option Link</span> must return data of type <span class="text-success">[Array]</span> however we received a value of type <span class="text-success">['.$dataType.']</span>');
                        } elseif (isset($option['link']['name']) && !is_string($option['link']['name'])) {
                            $dataType = ucwords(gettype($option['link']['name']));

                            //  Set an warning log that the option link name must be of type [String].
                            $this->logWarning('The given <span class="text-success">Option->Link->Name</span> must return data of type <span class="text-success">[String]</span> however we received a value of type <span class="text-success">['.$dataType.']</span>');
                        } elseif (isset($option['link']['type']) && !is_string($option['link']['type'])) {
                            $dataType = ucwords(gettype($option['link']));

                            //  Set an warning log that the option link name must be of type [String].
                            $this->logWarning('The given <span class="text-success">Option->Link->Type</span> must return data of type <span class="text-success">[String]</span> however we received a value of type <span class="text-success">['.$dataType.']</span>');
                        }

                        //  If the return type is an array format
                        if ($returnType == 'array') {
                            //  Build the option as an array
                            $option = [
                                //  Get the option name
                                'name' => $option['name'] ?? null,
                                //  Get the option input
                                'input' => $option['input'] ?? null,
                                //  Get the option value
                                'value' => $option['value'] ?? null,
                                //  Get the option link
                                'link' => $option['link'],
                            ];

                            //  Add the option to the rest of our options
                            array_push($collection, $option);

                        //  If the return type is a string format
                        } elseif ($returnType == 'string') {
                            //  If we have a top separator
                            if (!empty($option['separator']['top'])) {
                                $text .= $option['separator']['top']."\n";
                            }

                            //  Build the option as a string
                            $text .= $option['name']."\n";

                            //  If we have a bottom separator
                            if (!empty($option['separator']['bottom'])) {
                                $text .= $option['separator']['bottom']."\n";
                            }
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
                    $text = (!empty($this->display_instructions) ? "\n\n" : '');

                    //  Get the custom "No options available" otherwise use default
                    $text .= ($no_results_message ?? 'No options available');

                    //  Return the custom or default "No options available"
                    return $text;
                }
            } else {
                $dataType = ucwords(gettype($options));

                //  Set an warning log that the code must return data of type array.
                $this->logWarning('The given <span class="text-success">Code</span> must return data of type <span class="text-success">[Array]</span> however we received a value of type <span class="text-success">['.$dataType.']</span> therefore we cannot create the select options');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();
            }
        } catch (\Throwable $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        } catch (Exception $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        }
    }

    /** storeCurrentScreenUserResponseAsDynamicVariable()
     *  This method gets the current screen action details to determine the type of action that the
     *  screen requested. We use the type of action e.g "Input a value" or "Select an option" to
     *  determine the approach we must use in order to get the value and reference name required
     *  to create dynamic data variables e.g.
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
     *  These dynamic data variables can then be reference by other displays using mustache tags
     *  e.g {{ first_name }} or {{ product.name }}
     */
    public function storeCurrentScreenUserResponseAsDynamicVariable()
    {
        //  Get the current screen expected action type
        $screenActionType = $this->getDisplayActionType();

        //  If the action is to select an option e.g 1, 2 or 3
        if ($screenActionType == 'select_option') {
            //  Get the current screen expected select action type e.g static_options
            $screenSelectOptionType = $this->getDisplaySelectOptionType();

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

    /*  storeSelectedStaticOptionAsDynamicData()
     *  This method gets the value from the selected static option and stores it within the
     *  specified reference variable if provided. It also determines if the next display or
     *  screen link has been provided, if (yes) we fetch the specified display or screen
     *  and save it for linking in future methods.
     */
    public function storeSelectedStaticOptionAsDynamicData()
    {
        $outputResponse = $this->getStaticSelectOptions('array');

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($outputResponse)) {
            return $outputResponse;
        }

        //  Get the options
        $options = $outputResponse;

        $staticOptions = $this->display['content']['action']['select_option']['static_options'];

        //  Get the reference name (The name used to store the selected option value for ease of referencing)
        $reference_name = $staticOptions['reference_name'] ?? null;

        //  Get the custom "no results message"
        $no_results_message = $staticOptions['no_results_message'] ?? null;

        //  Get the custom "incorrect option selected message"
        $incorrect_option_selected_message = $staticOptions['incorrect_option_selected_message'] ?? null;

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
        $outputResponse = $this->getDynamicSelectOptions('array');

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($outputResponse)) {
            return $outputResponse;
        }

        //  Get the options
        $options = $outputResponse;

        $dynamicOptions = $this->display['content']['action']['select_option']['dynamic_options'];

        //  Get the reference name (The name used to store the selected option value for ease of referencing)
        $reference_name = $dynamicOptions['reference_name'] ?? null;

        //  Get the custom "no results message"
        $no_results_message = $dynamicOptions['no_results_message'] ?? null;

        //  Get the custom "incorrect option selected message"
        $incorrect_option_selected_message = $dynamicOptions['incorrect_option_selected_message'] ?? null;

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
        $outputResponse = $this->getCodeSelectOptions('array');

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($outputResponse)) {
            return $outputResponse;
        }

        //  Get the options
        $options = $outputResponse;

        $codeOptions = $this->display['content']['action']['select_option']['code_editor_options'];

        //  Get the reference name (The name used to store the selected option value for ease of referencing)
        $reference_name = $codeOptions['reference_name'] ?? null;

        //  Get the custom "no results message"
        $no_results_message = $codeOptions['no_results_message'] ?? null;

        //  Get the custom "incorrect option selected message"
        $incorrect_option_selected_message = $codeOptions['incorrect_option_selected_message'] ?? null;

        return $this->storeSelectedOption($options, $reference_name, $no_results_message, $incorrect_option_selected_message);
    }

    public function storeSelectedOption($options = [], $reference_name = null, $no_results_message = null, $incorrect_option_selected_message = null)
    {
        /** $options represents a set of action options
         *
         *  Example Structure:.
         *
         *  [
         *      [
         *          "name": "1. My Messages ({{ messages.total }})",
         *          "value" => [ ... ],
         *          "input" => "1"
         *          "link" => [
         *               "type" => "screen",        //  screen, display
         *               "name" => "messages"
         *          ]
         *      ],
         *      ...
         *  ]
         *
         *  Structure Definition
         *
         *  name:   Represents the display name of the option (What the user will see)
         *  value:  Represents the actual value of the option (What will be stored)
         *  link:   The screen or display to link to when this option is selected
         *  input:  What the user must input to select this option
         */

        //  Check if we have options to display
        $optionsExist = count($options) ? true : false;

        //  Get option matching user response
        $selectedOption = collect(array_filter($options, function ($option) {
            
            return $this->currentUserResponse == $option['input'];

        }))->first() ?? null;

        //  If we have options to display
        if ($optionsExist) {
            //  If the user selected an option that exists
            if (!empty($selectedOption)) {
                //  Get the selected option link (The display or screen we must link to after the user selects this option)
                $link = $selectedOption['link'] ?? null;

                //  Setup the link for the next display or screen
                $this->setupLink($link);

                //  If we have the reference name provided
                if (!empty($reference_name)) {
                    //  Get the option value only
                    $dynamic_data = $selectedOption['value'];

                    //  Store the select option as dynamic data
                    $this->storeDynamicData($reference_name, $dynamic_data);
                }

                //  If the user did not select an option that exists
            } else {
                //  Display the custom "Incorrect option selected" otherwise use default
                $message = ($incorrect_option_selected_message ?? 'You selected an incorrect option. Please try again')."\n";

                //  Display a custom message (with go back option) to notify the user of the issue
                return $this->displayCustomGoBackPage($message);
            }

            //  If we don't have options to display
        } else {
            //  Display the custom "No options available" otherwise use default
            $message = ($no_results_message ?? 'No options available')."\n";

            //  Display a custom message (with go back option) to notify the user of the issue
            return $this->displayCustomGoBackPage($message);
        }
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
        $reference_name = $this->display['content']['action']['input_value']['single_value_input']['reference_name'] ?? null;

        //  Get the single input link (The display or screen we must link to after the user inputs a value)
        $link = $this->display['content']['action']['input_value']['single_value_input']['link'] ?? null;

        //  Setup the link for the next display or screen
        $this->setupLink($link);

        //  If we have the reference name provided
        if (!empty($reference_name)) {
            //  Store the input value as dynamic data
            $this->storeDynamicData($reference_name, $user_response);
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
        $reference_names = $this->display['content']['action']['input_value']['multi_value_input']['reference_names'] ?? [];

        /** Get the separator (The character used to separate the user input values).
         *  Default to spaces if not set.
         *
         *  Example: ","
         *
         *  Default: " "
         */
        $separator = $this->display['content']['action']['input_value']['multi_value_input']['separator'] ?? ' ';
        $separator = 'spaces' ? ' ' : $separator;

        //  Get the multi input link (The display or screen we must link to after the user inputs a value)
        $link = $this->display['content']['action']['input_value']['multi_value_input']['link'] ?? null;

        //  Setup the link for the next display or screen
        $this->setupLink($link);

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
                $this->storeDynamicData($reference_name, $user_response);
            }
        }
    }

    public function setupLink($link)
    {
        //  If we have a link
        if (isset($link) && !empty($link)) {
            //  If the link name and type has been provided
            if (isset($link['name']) && !empty($link['name']) && isset($link['type']) && !empty($link['type'])) {
                $name = $link['name'];

                //  If we should link to a display
                if ($link['type'] == 'display') {
                    //  Get the screen matching the given name and set it as the linked screen
                    $this->linkedDisplay = $this->getDisplayByName($name);

                //  If we should link to a screen
                } elseif ($link['type'] == 'screen') {
                    //  Get the screen matching the given name and set it as the linked screen
                    $this->linkedScreen = $this->getScreenByName($name);
                }
            }
        }
    }

    /*  getDisplayByName()
     *  This method returns a display of the current screen if it exists by searching based on
     *  the display name provided
     *
     */
    public function getDisplayByName($name = null)
    {
        //  If the display name has been provided
        if (!empty($name)) {
            //  Get the first display that matches the given name
            return collect($this->screen['displays'])->where('name', $name)->first() ?? null;
        }
    }

    /*  getScreenByName()
     *  This method returns a screen if it exists by searching based on the screen name provided
     *
     */
    public function getScreenByName($name = null)
    {
        //  If the screen name has been provided
        if ($name) {
            //  Get the first screen that matches the given screen name
            return collect($this->screens)->where('name', $name)->first() ?? null;
        }
    }

    /*  getCurrentScreenUserResponse()
     *  This method gets the users response for the current screen if it exists otherwise
     *  returns an empty string if it does not exist. We also log an info message to
     *  indicate the screen name associated with the provided response.
     */
    public function getCurrentScreenUserResponse()
    {
        $this->currentUserResponse = $this->getResponseFromLevel($this->level) ?? '';   //  John Doe

        //  Update the ussd data
        $this->ussd['user_response'] = $this->currentUserResponse;

        //  Store the ussd data using the given item reference name
        $this->storeDynamicData('ussd', $this->ussd, false);

        //  Set an info log that the user has responded to the current screen and show the input value
        $this->logInfo('User has responded to <span class="text-primary">'.$this->screen['name'].'</span> with <span class="text-success">'.$this->currentUserResponse.'</span>');
        
        //  Return the current screen user response
        return $this->currentUserResponse;
    }

    public function handlePagination()
    {
        $paginations = $this->display['content']['pagination'];

        foreach( $paginations as $pagination ){

            //  Set an info log that we are handling pagination
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span>, handling pagination (<span class="text-success">'.$pagination['name'].'</span>)');

            //  Get the pagination type
            $pagination_type = $pagination['selected_type'];

            //  Get the pagination content target
            $content_target = $pagination['content_target']['selected_type'];
            
            //  Get the pagination start slice
            $start_slice = $pagination['slice']['start'];

            //  Get the pagination end slice
            $end_slice = $pagination['slice']['end'];

            //  Get the pagination input
            $input = $pagination['input'];

            //  Get the pagination show more visibility
            $show_more_visible = $pagination['show_more']['visible'];

            //  Get the pagination show more text
            $show_more_text = $pagination['show_more']['text'];
            
            //  Process dynamic content embedded within the start slice
            $outputResponse = $this->handleEmbeddedDynamicContentConversion($start_slice, false);

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($outputResponse)) return $outputResponse;

            //  Get the processed value (Convert from [String] to [Number]) - Default to 0 if anything goes wrong
            $start_slice = (int) $outputResponse ?? 0;
            
            //  Process dynamic content embedded within the end slice
            $outputResponse = $this->handleEmbeddedDynamicContentConversion($end_slice, false);

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($outputResponse)) return $outputResponse;

            //  Get the processed value (Convert from [String] to [Number]) - Default to 160 if anything goes wrong
            $end_slice = (int) $outputResponse ?? 160;
            
            //  Process dynamic content embedded within the input
            $outputResponse = $this->handleEmbeddedDynamicContentConversion($input, false);

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($outputResponse)) return $outputResponse;
            
            $input = $outputResponse;

            if( $show_more_visible ){
            
                //  Process dynamic content embedded within the show more text
                $outputResponse = $this->handleEmbeddedDynamicContentConversion($show_more_text, false);
    
                //  If we have a screen to show return the response otherwise continue
                if ($this->shouldDisplayScreen($outputResponse)) return $outputResponse;
                
                $show_more_text = $outputResponse;

            }

            //  Set an array to store all the content slices
            $content_slices = [];

            if( $content_target == 'instruction' ){

                $content = $this->display_instructions ?? '';

            }elseif( $content_target == 'action' ){

                $content = $this->display_actions ?? '';

            }elseif( $content_target == 'both' ){

                $content = $this->display_content ?? '';

            }

            //  Get the content that must always be at the top
            $fixed_content = substr($content, 0, $start_slice);

            //  Get the rest of the content as the content to paginate
            $pagination_content = substr($content, $start_slice);

            //  Get the trail for showing we have more content
            $trailing_characters = '...';

            //  If the show more text is set to be visible and its not empty
            if( $show_more_visible == true && !empty($show_more_text) ){
                
                //  Conbine the trail the show more text
                $trailing_characters .= "\n".$show_more_text;

            }

            //  Start slicing the content
            while ( !empty( $pagination_content ) ) {

                //  Get the trail characters
                $trail_length = strlen($trailing_characters);

                //  If we slice the content and don't have any left overs (Remaining characters)
                if( empty( substr($pagination_content, $end_slice) ) ){
                
                    //  Get the content slice without the trail
                    $content_slice = substr($pagination_content, 0, $end_slice);

                    //  Update the pagination content left after slicing
                    $pagination_content = substr($pagination_content, $end_slice);

                //  If we slice the content and we have left overs (Remaining characters)
                }else{

                    //  Get the content slice with the trail
                    $content_slice = substr($pagination_content, 0, $end_slice - $trail_length) . $trailing_characters;

                    //  Update the pagination content left after slicing
                    $pagination_content = substr($pagination_content, $end_slice - $trail_length);

                }

                //  Add the slice to the content slices
                array_push($content_slices, $content_slice);

            }

            //  If we have the input
            if ( !empty($input) ) {
                    
                $input = trim($input);

                //  Start slicing the content
                while ( $this->completedLevel( $this->level ) ) {
                
                    $userResponse = $this->getResponseFromLevel($this->level) ?? '';   //  99

                    //  If the user response matches the pagination input
                    if ( $userResponse == $input) {
                        
                        //  Set an info log that we are scrolling on the content
                        $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> scrolling ' . ($pagination_type == 'scroll_up' ? 'up' : 'down'));

                        if( $pagination_type == 'scroll_up'){

                            if( $this->pagination_index > 0 ){

                                //  Decrement the pagination index so that we target the previous pagination content slice
                                --$this->pagination_index;

                            }

                        }else if( $pagination_type == 'scroll_down'){

                            //  Increment the pagination index so that next time we target the next pagination content slice
                            ++$this->pagination_index;

                        }

                        // Increment the current level so that we target the next display
                        ++$this->level;

                    }else{
                        
                        //  Stop the loop
                        break 1;

                    }
                }
            }

            //  Get the pagination content
            $paginated_content_slice = isset( $content_slices[ $this->pagination_index ] ) ? $content_slices[ $this->pagination_index ] : '';

            //  Set the current paginated content as the display content
            $this->display_content = $fixed_content . $paginated_content_slice;

        }
        
    }

    public function resetPagination()
    {
        $this->pagination_index = 0;
    }

    public function handleForwardNavigation()
    {
        //  If the screen is set to repeat
        if ($this->screen['type']['selected_type'] == 'repeat') {

            //  Set an info log that we are checking if the display can navigate forward
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span>, checking if the display can navigate forward');

            $forward_navigation = $this->display['content']['screen_repeat_navigation']['forward_navigation'];

            foreach( $forward_navigation as $navigation ){
    
                //  Get the navigation step settings
                $step = $navigation['custom']['step'];

                //  Check if the step uses "Code Editor Mode"
                $uses_code_editor_mode = $step['code_editor_mode'];

                //  If the step uses the PHP Code Editor
                if ($uses_code_editor_mode == true) {

                    //  Get the step code otherwise default to a return statement that returns 1
                    $step_text = $step['code_editor_text'] ?? "return '1';";

                //  If the step does not use the PHP Code Editor
                } else {
                    
                    //  Get the step text otherwise default to a string of 1
                    $step_text = $step['text'] ?? '1';

                }

                //  Process dynamic content embedded within the step text
                $outputResponse = $this->handleEmbeddedDynamicContentConversion($step_text, $uses_code_editor_mode);

                //  If we have a screen to show return the response otherwise continue
                if ($this->shouldDisplayScreen($outputResponse)) return $outputResponse;

                //  Get the processed step value (Convert from [String] to [Number]) - Default to 1 if anything goes wrong
                $step_number = (int) $outputResponse ?? 1;

                //  If the processed forward navigation step number is not an integer or a number greater than 1
                if( !is_integer( $step_number ) || !( $step_number >= 1 ) ){

                    $dataType = ucwords(gettype($step_number));

                    //  Set an warning log that the step number must be of type array.
                    if( !is_integer( $step_number ) ) $this->logWarning('The given forward navigation step number must be of type <span class="text-success">[Integer]</span>. Value received <span class="text-success">['.$step_number.']</span> is of type <span class="text-success">['.$dataType.']</span>');

                    if( !( $step_number >= 1 ) ) $this->logWarning('The given forward navigation step number equals [<span class="text-success">'.$step_number.'</span>]. The expected value must equal [<span class="text-success">1</span>] or an integer greater than [<span class="text-success">1</span>].For this reason we will use the default value of [<span class="text-success">1</span>]');

                    //  Default the forward navigation step number to 1
                    $this->forward_navigation_step_number = 1;

                }else{

                    $this->forward_navigation_step_number = $step_number;

                }

                if ($navigation['selected_type'] == 'custom') {

                    //  Set an info log that we are checking if the display can navigate forward
                    $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> supports custom forward navigation');
    
                    //  Get the custom inputs e.g "1, 2, 3"
                    $inputs = $navigation['custom']['inputs'];

                    //  If we have inputs
                    if (!empty($inputs)) {

                        //  Seprate the inputs by comma ","
                        $valid_inputs = explode(',', $inputs);
    
                        foreach ($valid_inputs as $key => $input) {
                            //  Make sure each input has no left and right spaces
                            $valid_inputs[$key] = trim($input);
                        }
    
                        //  If the user response matches any valid navigation input
                        if (in_array($this->currentUserResponse, $valid_inputs)) {
                            
                            //  Set an info log that user response has been allowed for forward navigation
                            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> user response allowed for forward navigation');
                
                            /** Increment the current level so that we target the next repeat display
                             *  (This means we are targeting the same display but different instance)
                             */
                            ++$this->level;
            
                            /* Return an indication that we want to navigate forward (i.e Go to the next iteration)
                                *
                                *  Refer to: handleRepeatScreenOnItems() and handleRepeatScreenOnNumber()
                                *
                                */
                            return 'navigate-forward';

                        }
                    }
                }
            }
        }
    }

    public function handleBackwardNavigation()
    {
        //  If the screen is set to repeat
        if ($this->screen['type']['selected_type'] == 'repeat') {

            //  Set an info log that we are checking if the display can navigate forward
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span>, checking if the display can navigate backward');

            $backward_navigation = $this->display['content']['screen_repeat_navigation']['backward_navigation'];

            foreach( $backward_navigation as $navigation ){
    
                //  Get the navigation step settings
                $step = $navigation['custom']['step'];

                //  Check if the step uses "Code Editor Mode"
                $uses_code_editor_mode = $step['code_editor_mode'];

                //  If the step uses the PHP Code Editor
                if ($uses_code_editor_mode == true) {

                    //  Get the step code otherwise default to a return statement that returns 1
                    $step_text = $step['code_editor_text'] ?? "return '1';";

                //  If the step does not use the PHP Code Editor
                } else {
                    
                    //  Get the step text otherwise default to a string of 1
                    $step_text = $step['text'] ?? '1';

                }

                //  Process dynamic content embedded within the step text
                $outputResponse = $this->handleEmbeddedDynamicContentConversion($step_text, $uses_code_editor_mode);

                //  If we have a screen to show return the response otherwise continue
                if ($this->shouldDisplayScreen($outputResponse)) return $outputResponse;

                //  Get the processed step value (Convert from [String] to [Number]) - Default to 1 if anything goes wrong
                $step_number = (int) $outputResponse ?? 1;

                //  If the processed backward navigation step number is not an integer or a number greater than 1
                if( !is_integer( $step_number ) || !( $step_number >= 1 ) ){

                    //  Set an warning log that the step number must be of type array.
                    if( !is_integer( $step_number ) ) $this->logWarning('The given backward navigation step number must be of type [<span class="text-success">Integer</span>]. Value received [<span class="text-success">'.$step_number.'</span>] is of type [<span class="text-success">'.gettype($output).'</span>]');

                    if( !( $step_number >= 1 ) ) $this->logWarning('The given backward navigation step number equals [<span class="text-success">'.$step_number.'</span>]. The expected value must equal [<span class="text-success">1</span>] or an integer greater than [<span class="text-success">1</span>].For this reason we will use the default value of [<span class="text-success">1</span>]');

                    //  Default the backward navigation step number to 1
                    $this->backward_navigation_step_number = 1;

                }else{

                    $this->backward_navigation_step_number = $step_number;

                }

                if ($navigation['selected_type'] == 'custom') {

                    //  Set an info log that we are checking if the display can navigate backward
                    $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> supports custom backward navigation');
    
                    //  Get the custom inputs e.g "1, 2, 3"
                    $inputs = $navigation['custom']['inputs'];

                    //  If we have inputs
                    if (!empty($inputs)) {

                        //  Seprate the inputs by comma ","
                        $valid_inputs = explode(',', $inputs);
    
                        foreach ($valid_inputs as $key => $input) {
                            //  Make sure each input has no left and right spaces
                            $valid_inputs[$key] = trim($input);
                        }
    
                        //  If the user response matches any valid navigation input
                        if (in_array($this->currentUserResponse, $valid_inputs)) {
                            
                            //  Set an info log that user response has been allowed for backward navigation
                            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> user response allowed for backward navigation');
                
                            /** Increment the current level so that we target the next repeat display
                             *  (This means we are targeting the same display but different instance)
                             */
                            ++$this->level;
            
                            /** Return an indication that we want to navigate forward (i.e Go to the next iteration)
                             *
                             *  Refer to: handleRepeatScreenOnItems() and handleRepeatScreenOnNumber()
                             *
                             */
                            return 'navigate-backward';

                        }
                    }
                }
            }
        }
    }

    public function handleLinkingDisplay()
    {
        //  Check if the current display must link to another display or screen
        if ($this->checkIfDisplayMustLink()) {

            //  Increment the current level so that we target the next screen (This means we are targeting the linked screen)
            ++$this->level;

            //  If we have a display we can link to
            if (!empty($this->linkedDisplay)) {

                //  Set the current display as the linked display
                $this->display = $this->linkedDisplay;

                //  Reset the linked display to nothing
                $this->linkedDisplay = null;

                $this->resetPagination();

                //  Handle the current display (This means we are handling the linked display)
                return $this->handleCurrentDisplay();

            //  If we have a screen we can link to
            } elseif (!empty($this->linkedScreen)) {

                //  Set the current screen as the linked screen
                $this->screen = $this->linkedScreen;

                //  Reset the linked screen to nothing
                $this->linkedScreen = null;

                $this->resetPagination();

                //  Handle the current screen (This means we are handling the linked screen)
                return $this->handleCurrentScreen();
                
            }
        }
    }

    /******************************************
     *  REPEAT EVENT METHODS                *
     *****************************************/

    public function handleBeforeRepeatEvents()
    {
        //  Check if the screen has before repeat events
        if (count($this->screen['type']['repeat']['events']['before_repeat'])) {
            //  Get the events to handle
            $events = $this->screen['type']['repeat']['events']['before_repeat'];

            //  Set an info log that the current screen has before repeat events
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> has (<span class="text-success">'.count($events).'</span>) before repeat events');

            //  Start handling the given events
            return $this->handleEvents($events);
        } else {
            //  Set an info log that the current screen does not have before repeat events
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> does not have before repeat events.');

            return null;
        }
    }

    public function handleAfterRepeatEvents()
    {
        //  Check if the screen has after repeat events
        if (count($this->screen['type']['repeat']['events']['after_repeat'])) {
            //  Get the events to handle
            $events = $this->screen['type']['repeat']['events']['after_repeat'];

            //  Set an info log that the current screen has after repeat events
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> has (<span class="text-success">'.count($events).'</span>) after repeat events');

            //  Start handling the given events
            return $this->handleEvents($events);
        } else {
            //  Set an info log that the current screen does not have after repeat events
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> does not have after repeat events');

            return null;
        }
    }

    /******************************************
     *  DISPLAY EVENT METHODS                *
     *****************************************/

    public function handleBeforeResponseEvents()
    {
        //  Check if the display has before user response events
        if (count($this->display['content']['events']['before_reply'])) {
            //  Get the events to handle
            $events = $this->display['content']['events']['before_reply'];

            //  Set an info log that the current screen has before user response events
            $this->logInfo('Display <span class="text-primary">'.$this->display['name'].'</span> has (<span class="text-success">'.count($events).'</span>) before user response events.');

            //  Start handling the given events
            return $this->handleEvents($events);
        } else {
            //  Set an info log that the current display does not have before user response events
            $this->logInfo('Display <span class="text-primary">'.$this->display['name'].'</span> does not have before user response events.');

            return null;
        }
    }

    public function handleAfterResponseEvents()
    {
        //  Check if the display has after user response events
        if (count($this->display['content']['events']['after_reply'])) {
            //  Get the events to handle
            $events = $this->display['content']['events']['after_reply'];

            //  Set an info log that the current screen has after user response events
            $this->logInfo('Display <span class="text-primary">'.$this->display['name'].'</span> has (<span class="text-success">'.count($events).'</span>) after user response events.');

            //  Start handling the given events
            return $this->handleEvents($events);
        } else {
            //  Set an info log that the current display does not have after user response events
            $this->logInfo('Display <span class="text-primary">'.$this->display['name'].'</span> does not have after user response events.');

            return null;
        }
    }

    /******************************************
     *  EVENT METHODS                         *
     *****************************************/

    public function handleEvents($events = [])
    {
        //  If we have events to handle
        if (count($events)) {
            //  Foreach event
            foreach ($events as $event) {
                //  Handle the current event
                $handleEventResponse = $this->handleEvent($event);

                //  If the given response is a display screen then return the response otherwise continue
                if ($this->shouldDisplayScreen($handleEventResponse)) {
                    //  Set an info log that the current event wants to display information
                    $this->logInfo('Event: <span class="text-success">'.$event['name'].'</span>, wants to display information, we are not running any other events or processes, instead we will return information to display.');

                    //  Return the screen information
                    return $handleEventResponse;
                }
            }
        }
    }

    public function handleEvent($event = null)
    {
        //  If we have an active event to handle
        if ($event['active']) {
            //  Set an info log that we are preparing to handle the given event
            $this->logInfo('<span class="text-primary">'.$this->screen['name'].'</span> preparing to handle the <span class="text-success">'.$event['name'].'</span> event');

            //  Get the current event
            $this->event = $event;

            if ($event['type'] == 'CRUD API') {
                return $this->handle_CRUD_API_Event();
            } elseif ($event['type'] == 'SMS API') {
                return $this->handle_SMS_API_Event();
            } elseif ($event['type'] == 'Email API') {
                return $this->handle_Email_API_Event();
            } elseif ($event['type'] == 'Location API') {
                return $this->handle_Location_API_Event();
            } elseif ($event['type'] == 'Billing API') {
                return $this->handle_Billing_API_Event();
            } elseif ($event['type'] == 'Subcription API') {
                return $this->handle_Subcription_API_Event();
            } elseif ($event['type'] == 'Validation') {
                return $this->handle_Validation_Event();
            } elseif ($event['type'] == 'Formatting') {
                return $this->handle_Formatting_Event();
            } elseif ($event['type'] == 'Local Storage') {
                return $this->handle_Local_Storage_Event();
            } elseif ($event['type'] == 'Custom Code') {
                return $this->handle_Custom_Code_Event();
            } elseif ($event['type'] == 'Redirect') {
                return $this->handle_Redirect_Event();
            }
        } else {
            //  Set an info log that the current event is not activated
            $this->logInfo('Event: <span class="text-success">'.$event['name'].' is not activated, therefore will not be executed.');
        }
    }

    /******************************************
     *  CRUD API EVENT METHODS                *
     *****************************************/
    public function handle_CRUD_API_Event()
    {
        if ($this->event) {
            //  Run the CRUD API Call
            $apiCallResponse = $this->run_CRUD_Api_Call();

            //  If the response returned a screen display return the screen display otherwise continue
            if ($this->shouldDisplayScreen($apiCallResponse)) {
                return $apiCallResponse;
            }

            return $this->handle_CRUD_Api_Response($apiCallResponse);
        }
    }

    public function run_CRUD_Api_Call()
    {
        $url = $this->get_CRUD_Api_URL();

        //  If we have a screen to show return the response otherwise continue
        if ($this->shouldDisplayScreen($url)) return $url;

        $method = $this->get_CRUD_Api_Method();
        $headers = $this->get_CRUD_Api_Headers();
        $form_data = $this->get_CRUD_Api_Form_Data();
        $query_params = $this->get_CRUD_Api_Query_Params();
        $request_options = [];

        //  Check if the CRUD Url and Method has been provided
        if (empty($url) || empty($method)) {
            //  Check if the CRUD Url has been provided
            if (empty($url)) {
                //  Set a warning log that the CRUD API Url was not provided
                $this->logWarning('API Url was not provided');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();
            }

            //  Check if the CRUD Method has been provided
            if (empty($method)) {
                //  Set a warning log that the CRUD API Method was not provided
                $this->logWarning('API Method was not provided');

                //  Display the technical difficulties error page to notify the user of the issue
                return $this->displayTechnicalDifficultiesErrorPage();
            }
        } else {
            //  Set an info log of the CRUD API Url provided
            $this->logInfo('API Url: <span class="text-success">'.$url.'</span>');

            //  Set an info log of the CRUD API Method provided
            $this->logInfo('API Method: <span class="text-success">'.ucwords($method).'</span>');
        }

        //  Check if the provided url is correct
        if (!$this->isValidUrl($url)) {
            //  Set a warning log that the CRUD API Url provided is incorrect
            $this->logWarning('API Url provided is incorrect (<span class="text-danger">'.$url.'</span>)');

            //  Display the technical difficulties error page to notify the user of the issue
            return $this->displayTechnicalDifficultiesErrorPage();
        }

        //  If we have the headers
        if (!empty($headers) && is_array($headers)) {
            foreach ($headers as $key => $value) {
                //  Set an info log of the CRUD API header attribute
                $this->logInfo('Headers: <span class="text-success">'.$key.'</span> = <span class="text-success">'.$value.'</span>');
            }
        }

        //  If we have the form data
        if (!empty($query_params) && is_array($query_params)) {
            foreach ($query_params as $key => $value) {
                //  Set an info log of the CRUD API query param attribute
                $this->logInfo('Query Params: <span class="text-success">'.$key.'</span> = <span class="text-success">'.$value.'</span>');
            }
        }

        //  If we have the form data
        if (!empty($form_data) && is_array($form_data)) {
            //  Add the form data to the form_params attribute of our API options
            array_push($request_options, ['form_params' => $form_data]);

            foreach ($form_data as $key => $value) {
                //  Set an info log of the CRUD API form data attribute
                $this->logInfo('Form Data: <span class="text-success">'.$key.'</span> = <span class="text-success">'.$value.'</span>');
            }
        }

        //  Create a new Http Guzzle Client
        $httpClient = new \GuzzleHttp\Client();

        try {
            //  Set an info log that we are performing CRUD API call
            $this->logInfo('Run API call to: <span class="text-success">'.$url.'</span>');

            //  Perform and return the Http request
            return $httpClient->request($method, $url, $request_options);

            /* About guzzle errors
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

            /*
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
            } else {
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

    public function get_CRUD_Api_URL()
    {
        $url = $this->event['event_data']['url'] ?? null;

        if( $url ){

            //  Process dynamic content embedded within the url
            $buildResponse = $this->handleEmbeddedDynamicContentConversion(
                //  Text containing embedded dynamic content that must be convert
                $url,
                //  Is this text information generated using the PHP Code Editor
                false
            );

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($buildResponse)) {
                return $buildResponse;
            }

            //  Get the built option name
            $url = $buildResponse; 
        }

        return $url;
    }

    public function get_CRUD_Api_Method()
    {
        $method = $this->event['event_data']['method'] ?? null;

        return $method;
    }

    public function get_CRUD_Api_Headers()
    {
        $headers = $this->event['event_data']['headers'] ?? [];

        $data = [];

        foreach ($headers as $header) {
            if (!empty($header['key']) && !empty($header['value'])) {
                $data[$header['key']] = $header['value'];
            }
        }

        return $data;
    }

    public function get_CRUD_Api_Form_Data()
    {
        $form_data = $this->event['event_data']['form_data'] ?? [];

        $data = [];

        foreach ($form_data as $form_item) {
            if (!empty($form_item['key']) && !empty($form_item['value'])) {
                $data[$form_item['key']] = $form_item['value'];
            }
        }

        return $data;
    }

    public function get_CRUD_Api_Query_Params()
    {
        $query_params = $this->event['event_data']['query_params'] ?? [];

        $data = [];

        foreach ($query_params as $query_param) {
            if (!empty($query_param['key']) && !empty($query_param['value'])) {
                $data[$query_param['key']] = $query_param['value'];
            }
        }

        return $data;
    }

    public function get_CRUD_Api_Status_Handles()
    {
        $response_status_handles = $this->event['event_data']['response']['manual']['response_status_handles'] ?? [];

        return $response_status_handles;
    }

    public function isValidUrl($url = '')
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }

    public function handle_CRUD_Api_Response($response = null)
    {
        if ($response) {
            /** Get the CRUD API return type. We use the return type to determine how we
             *  want to handle the response of the API Call. Our options are as follows:.
             *
             *  Automatic : Automatically display the default success/error message depending on the API success
             *  Manual    : Manually display the provided custom information or message
             *
             *  Default is "automatic" if no value is provided
             */
            $return_type = $this->event['event_data']['response']['selected_type'] ?? 'automatic';

            //  Set an info log that we are starting to handle the CRUD API response
            $this->logInfo('Start handling CRUD Api Response');

            if ($return_type == 'manual') {
                return $this->handle_CRUD_Api_Manual_Response($response);
            } elseif ($return_type == 'automatic') {
                return $this->handle_CRUD_Api_Automatic_Response($response);
            }
        }
    }

    public function handle_CRUD_Api_Automatic_Response($response = null)
    {
        //  Set an info log that the CRUD API will be handled automatically
        $this->logInfo('Handle response <span class="text-success">Automatically</span>');

        //  Get the response status code e.g "200"
        $status_code = $response->getStatusCode();

        //  Get the response status phrase e.g "OK"
        $status_phrase = $response->getReasonPhrase() ?? '';

        //  Get the default success message
        $default_success_message = $this->event['event_data']['response']['general']['default_success_message'] ?? 'Completed successfully';

        //  Get the default error message
        $default_error_message = $this->event['event_data']['response']['general']['default_error_message'] ?? null;

        $on_success_handle_type = $this->event['event_data']['response']['automatic']['on_handle_success'] ?? 'use_default_success_msg';
        $on_error_handle_type = $this->event['event_data']['response']['automatic']['on_handle_error'] ?? 'use_default_error_msg';

        //  Check if this is a good status code e.g "100", "200", "301" e.t.c
        if ($this->checkIfGoodStatusCode($status_code)) {
            //  Set an info log of the response status code received
            $this->logInfo('API response returned a status (<span class="text-success">'.$status_code.'</span>) Status text: <span class="text-success">'.$status_phrase.'</span>');

            //  Since this is a successful response, check if we should display a default success message or do nothing
            if ($on_success_handle_type == 'use_default_success_msg') {
                //  Set an info log that we are displaying the custom success message
                $this->logInfo('Display default success message: <span class="text-success">'.$default_success_message.'</span>');

                //  This is a good response - Display the custom succcess message
                return $this->displayCustomPage($default_success_message, ['continue' => false]);
            } elseif ($on_success_handle_type == 'do_nothing') {
                //  Return nothing
                return null;
            }

            //  If this is a bad status code e.g "400", "401", "500" e.t.c
        } else {
            //  Set an info log of the response status code received
            $this->logWarning('API response returned a status (<span class="text-danger">'.$status_code.'</span>) <br/> Status text: <span class="text-danger">'.$status_phrase.'</span>');

            //  Since this is a failed response, check if we should display a default error message or do nothing
            if ($on_error_handle_type == 'use_default_error_msg') {
                //  Set an info log that we are displaying the custom error message
                $this->logInfo('Display default error message: <span class="text-success">'.$default_error_message.'</span>');

                //  If the default error message was provided
                if (!empty($default_error_message)) {
                    //  This is a bad response - Display the custom error message
                    return $this->displayCustomErrorPage($default_error_message);

                //  If the default error message was not provided
                } else {
                    //  Set an warning log that the default error message was not provided
                    $this->logWarning('The default error message was not provided, using the default technical difficulties message instead');

                    //  Display the technical difficulties error page to notify the user of the issue
                    return $this->displayTechnicalDifficultiesErrorPage();
                }
            } elseif ($on_error_handle_type == 'do_nothing') {
                //  Return nothing
                return null;
            }
        }
    }

    public function handle_CRUD_Api_Manual_Response($response = null)
    {
        //  Use the try/catch handles incase we run into any possible errors
        try {
            //  Set an info log that the CRUD API will be handled manually
            $this->logInfo('Handle response <span class="text-success">Manually</span>');

            //  Get the response status code e.g "200"
            $status_code = $response->getStatusCode();

            //  Get the response status phrase e.g "OK"
            $status_phrase = $response->getReasonPhrase() ?? '';

            //  Get the response body e.g [ "products" => [ ... ] ]
            $response_body = json_decode($response->getBody());

            //  Get the response status handles

            $response_status_handles = $this->event['event_data']['response']['manual']['response_status_handles'] ?? [];

            if (!empty($response_status_handles)) {
                //  Get the request status handle that matches the given status
                $selectedHandle = collect(array_filter($response_status_handles, function ($request_status_handle) use ($status_code) {
                    return $request_status_handle['status'] == $status_code;
                }))->first() ?? null;

                //  If a matching response status handle was found
                if ($selectedHandle) {
                    //  Get the response attributes
                    $response_attributes = $selectedHandle['attributes'];

                    //  Get the response handle type e.g "use_custom_msg" or "do_nothing"
                    $on_handle_type = $selectedHandle['on_handle']['selected_type'];

                    //  Check if the current response status handle uses "Code Editor Mode"
                    $uses_code_editor_mode = $selectedHandle['on_handle']['use_custom_msg']['code_editor_mode'];

                    //  Set an info log that we are storing the attributes of the custom API response
                    $this->logInfo('Start processing and storing the response attributes');

                    //  Set an info log of the number of response attributes found
                    $this->logInfo('Found ('.count($response_attributes).') response attributes');

                    //  Add the current response body to the dynamic data storage
                    $this->dynamic_data_storage['response'] = $response_body;

                    foreach ($response_attributes as $response_attribute) {
                        //  Get the attribute name
                        $name = trim($response_attribute['name']);

                        //  Get the attribute value
                        $value = trim($response_attribute['value']);

                        //  If the attribute name and value exists
                        if (!empty($name) && !empty($value)) {
                            //  Get the attribute value (Usually in mustache tag format)
                            $mustache_tag = $value;

                            //  Convert "{{ company.name }}" into "$company->name"
                            $dynamic_variable = $this->convertMustacheTagIntoPHPVariable($mustache_tag, true);

                            //  Convert the dynamic property into its dynamic value e.g "$company->name" into "Company XYZ"
                            $outputResponse = $this->processPHPCode("return $dynamic_variable;", false);

                            //  If processing the PHP Code failed, return the failed response otherwise continue
                            if ($this->shouldDisplayScreen($outputResponse)) {
                                return $outputResponse;
                            }

                            //  Get the generated output
                            $output = $outputResponse;

                            $dataType = ucwords(gettype($output));

                            //  If the dynamic value is a string, integer or float
                            if (is_string($output) || is_integer($output) || is_float($output)) {
                                //  Set an info log that we are converting the dynamic property to its associated value
                                $this->logInfo(
                                    //  Use json_encode($output) to show $output data instead of gettype($output)
                                    'Converting attribute: <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">['.$dataType.']</span> '.
                                    'and assigning the value to <span class="text-success">'.$response_attribute['name'].'</span>'
                                );

                            //  Incase the dynamic value is not a string, integer or float
                            } else {
                                //  Set an info log that we are converting the dynamic property to its associated value
                                $this->logInfo(
                                    //  Use json_encode($output) to show $output data instead of gettype($output)
                                    'Converting attribute: <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">['.$dataType.']</span> '.
                                    'and assigning the value to <span class="text-success">'.$response_attribute['name'].'</span>'
                                );
                            }

                            //  Store the attribute data as dynamic data
                            $this->storeDynamicData($name, $output);
                        }
                    }

                    if ($on_handle_type == 'use_custom_msg') {
                        //  Check if this is a good status code e.g "100", "200", "301" e.t.c
                        if ($this->checkIfGoodStatusCode($status_code)) {
                            //  Set an info log that we are displaying the custom message
                            $this->logInfo('Start processing the custom message to display for status code <span class="text-success">'.$status_code.'</span>');
                        } else {
                            //  Set an info log that we are displaying the custom message
                            $this->logInfo('Start processing the custom message to display for status code <span class="text-danger">'.$status_code.'</span>');
                        }

                        //  If the custom message uses the PHP Code Editor
                        if ($uses_code_editor_mode == true) {
                            //  Get the custom message code otherwise default to a return statement that returns an empty string
                            $custom_message_text = $selectedHandle['on_handle']['use_custom_msg']['code_editor_text'] ?? "return '';";

                        //  If the custom message does not use the PHP Code Editor
                        } else {
                            //  Get the custom message text otherwise default to an empty string
                            $custom_message_text = $selectedHandle['on_handle']['use_custom_msg']['text'] ?? '';
                        }

                        //  Process dynamic content embedded within the custom message
                        $outputResponse = $this->handleEmbeddedDynamicContentConversion($custom_message_text, $uses_code_editor_mode);

                        //  If processing the custom message failed, return the failed response otherwise continue
                        if ($this->shouldDisplayScreen($outputResponse)) {
                            return $outputResponse;
                        }

                        //  Set an info log of the final result
                        $this->logInfo('Final result: <br /><span class="text-success">'.$outputResponse.'</span>');

                        //  Return the processed custom message display
                        return $this->displayCustomPage($outputResponse);
                    } elseif ($on_handle_type == 'do_nothing') {
                        //  Return nothing
                        return null;
                    }
                } else {
                    //  Set a warning log that the custom API does not have a matching response status handle
                    $this->logWarning('No matching status handle to process the current response of status <span class="text-success">'.$status_code.'</span>');
                }
            } else {
                //  Set a warning log that the custom API does not have response status handles
                $this->logWarning('No response status handles to process the current response of status <span class="text-success">'.$status_code.'</span>');
            }

            //  Set a warning log that the custom API cannot be handled manually
            $this->logWarning('Could not handle the response <span class="text-success">Manually</span>, attempt to handle <span class="text-success">Automatically</span>');

            //  Handle the request automatically
            return $this->handle_CRUD_Api_Automatic_Response($response);
        } catch (\Throwable $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        } catch (Exception $e) {
            //  Handle try catch error
            return $this->handleTryCatchError($e);
        }
    }

    public function checkIfGoodStatusCode($status_code = '')
    {
        /** About Status Codes:
         *
         *  1xx informational response – the request was received, continuing process
         *  2xx successful – the request was successfully received, understood, and accepted
         *  3xx redirection – further action needs to be taken in order to complete the request
         *  4xx client error – the request contains bad syntax or cannot be fulfilled
         *  5xx server error – the server failed to fulfil an apparently valid request.
         */
        $digit = substr($status_code, 0, 1);

        //  If the status code starts with "1", "2" or "3" e.g "100", "200", "301" e.t.c
        if (in_array($digit, ['1', '2', '3'])) {
            //  Return true for good status code
            return true;
        }

        //  Return false for bad status code
        return false;
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
        if ($this->shouldDisplayScreen($failedValidationResponse)) {
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
        if ($this->shouldDisplayScreen($failedFormatResponse)) {
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

    /*  getDisplaySelectOptionType()
     *  This method gets the type of "Input" requested by the current screen
     *
     */
    public function getCurrentScreenInputType()
    {
        //  Available type: "single_value_input" and "multi_value_input"
        return $this->screenContent['action']['input_value']['selected_type'] ?? '';
    }

    /*  checkIfDisplayMustLink()
     *  This method checks if the current screen has a screen it can link to. If (yes)
     *  we return true, if (no) we return false.
     *
     */
    public function checkIfDisplayMustLink()
    {
        //  If we have a display or screen we can link to
        if (!empty($this->linkedDisplay) || !empty($this->linkedScreen)) {
            //  Return true to indicate that we must link to another display or screen
            return true;
        }

        //  Return false to indicate that we must not link to another screen
        return false;
    }

    public function shouldDisplayScreen($text = '')
    {
        if (is_string($text)) {
            //  If the first 3 characters of the text match the words "CON" or "END" then this is a display screen
            return  (substr($text, 0, 3) == 'CON' || substr($text, 0, 3) == 'END') ? true : false;
        }

        return false;
    }

    public function handleEmbeddedDynamicContentConversion($text = '', $uses_code_editor_mode = true)
    {
        //  Remove the (\u00a0) special character which represents a no-break space in HTML
        $text = $this->remove_HTML_No_Break_Space($text);

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
            $this->logInfo('Found ('.$number_of_mustache_tags.') dynamic content references within the text: <span class="text-success">'.$text.'</span>');
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
                    //  Convert the dynamic property into its dynamic value e.g "$company->name" into "Company XYZ"
                    $outputResponse = $this->processPHPCode("return $dynamic_variable;");

                    //  If processing the PHP Code failed, return the failed response otherwise continue
                    if ($this->shouldDisplayScreen($outputResponse)) {
                        return $outputResponse;
                    }

                    //  Get the generated output
                    $output = $outputResponse;

                    //  Incase the dynamic value is not a string, integer or float
                    if (!is_string($output) && !is_integer($output) && !is_float($output)) {
                        
                        $dataType = ucwords(gettype($output));

                        //  Get the result type e.g Object, Array, Boolean e.t.c and wrap in square brackets
                        $output = '['.$dataType.']';
                    }

                    //  Set an info log that we are converting the dynamic property to its associated value
                    $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">'.$output.'</span>');

                    //  Replace the mustache tag with its dynamic data e.g replace "{{ company.name }}" with "Company XYZ"
                    $text = preg_replace("/$mustache_tag/", $output, $text);
                } else {
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
            //  Set an info log that we are processing the PHP Code from the PHP Code Editor
            $this->logInfo('Process PHP Code from the Code Editor');

            //  Remove the PHP tags from the PHP Code
            $text = $this->removePHPTags($text);

            //  Process the PHP Code
            $outputResponse = $this->processPHPCode("$text");

            //  If processing the PHP Code failed, return the failed response otherwise continue
            if ($this->shouldDisplayScreen($outputResponse)) {
                return $outputResponse;
            }

            //  Get the generated output
            $text = $outputResponse;
        }

        //  Remove any HTML or PHP tags
        $text = strip_tags($text);

        //  Return the converted text
        return $text;
    }

    public function remove_HTML_No_Break_Space($text = '')
    {
        return preg_replace('/\xc2\xa0/', '', $text);
    }

    public function getInstancesOfMustacheTags($text = '')
    {
        //  Remove the (\u00a0) special character which represents a no-break space in HTML
        $text = $this->remove_HTML_No_Break_Space($text);

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
            $text = $this->remove_HTML_No_Break_Space($text);

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
            //  Set an info log that we are converting the mustache tag into dynamic data
            $this->logInfo('Start converting mustache tag <span class="text-success">'.$mustache_tag.'</span> into its associated dynamic data');

            //  Convert "{{ products }}" into "$products"
            $variable = $this->convertMustacheTagIntoPHPVariable($mustache_tag, true);

            //  Convert the dynamic property into its dynamic value e.g "$products" into "[ ['name' => 'Product 1', ...], ... ]"
            $outputResponse = $this->processPHPCode("return $variable;");

            //  If we have a screen to show return the response otherwise continue
            if ($this->shouldDisplayScreen($outputResponse)) {
                return $outputResponse;
            }

            //  Get the generated output and convert to a JSON Object
            $output = $this->convertToJsonObject($outputResponse);

            $dataType = ucwords(gettype($output));

            //  Set an info log for the final conversion result
            $this->logInfo('Converting <span class="text-success">'.$mustache_tag.'</span> to <span class="text-success">['.$dataType.']</span>');

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

    public function processPHPCode($code = 'return null', $log_dynamic_data = true)
    {
        //  Use the try/catch handles incase we run into any possible errors
        try {
            //  If we have dynamic data
            if (count($this->dynamic_data_storage)) {
                //  Set an info log that we are creating variables with dynamic data
                if ($log_dynamic_data) {
                    $this->logInfo('Creating variables using stored dynamic data');
                }

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
                    if ($log_dynamic_data) {
                        
                        $dataType = ucwords(gettype($value));

                        //  Use json_encode($output) to show $value data instead of gettype($value)
                        $this->logInfo('Variable <span class="text-success">$'.$key.'</span> = <span class="text-success">['.$dataType.']</span>');
                    }
                }
            }

            //  Execute PHP Code
            return eval($code);
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

    public function storeDynamicData($name = null, $value = null, $log_status = true)
    {
        if (isset($name) && !empty($name)) {
            if (isset($this->dynamic_data_storage[$name])) {
                //  Set an warning log that we are overeiding existing data
                if ($log_status) {
                    $this->logWarning('Found existing data already stored within the reference name <span class="text-success">'.$name.'</span>, overiding the information.');
                }
                        
                $dataType = ucwords(gettype($this->dynamic_data_storage[$name]));

                //  Set an info log of the old data stored
                if ($log_status) {

                    //  Use json_encode($option_value) to show $option_value data instead of gettype($option_value)
                    $this->logInfo('Old Data: <span class="text-success">['.$dataType.']</span>');
                    
                }

                //  Add the value as additional dynamic data to our dynamic data storage
                if ($log_status) {
                    $this->dynamic_data_storage[$name] = $value;
                }

                //  Set an info log of the new data stored
                if ($log_status) {
                    $this->logInfo('New Data: <span class="text-success">['.$dataType.']</span>');
                }
            } else {
                //  Add the value as additional dynamic data to our dynamic data storage
                $this->dynamic_data_storage[$name] = $value;
            }
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
                $text = $this->remove_HTML_No_Break_Space($text);

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

    /*******************************************
    /*******************************************
     * LOGGING FUNCTIONS                       *
     *******************************************
     ******************************************/

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
            'screen' => $this->screen['name'] ?? null,
            'datetime' => (\Carbon\Carbon::now())->format('Y-m-d H:i:s'),
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
            'screen' => $this->screen['name'] ?? null,
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
            'screen' => $this->screen['name'] ?? null,
        ];

        $this->updateLog($data);
    }

    public function updateLog($data)
    {
        //  Get the last recorded log microtime
        if (empty($this->last_recorded_log_microtime)) {
            $this->last_recorded_log_microtime = $this->getMicroTime();
        }

        //  Calculate the current log time since the last recorded log time
        $microtime_since_last_log = ($this->getMicroTime() - $this->last_recorded_log_microtime) / 1000;

        //  Update our log data stack
        array_push($data, ['microtime_since_last_log', $microtime_since_last_log]);

        //  Push the latest log update
        array_push($this->log, $data);
    }

    public function getMicroTime()
    {
        return microtime(true);
    }

    /*******************************************
    /*******************************************
     * DISPLAY FUNCTIONS                       *
     *******************************************
     ******************************************/

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

        return trim($response);
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

    /*******************************************
    /*******************************************
     * BASIC USSD FUNCTIONS                    *
     *******************************************
     ******************************************/

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

            /** We want to say if we have levelNumber = 1 we should get the landing page data
             *  (since thats level 1) but technically $user_responses[0] = landing page response.
             *  This means to get the response for the level we want we must decrement by one unit.
             *  
             *  Use urldecode() to convert all encoded values to their
             *  decoded counterparts e.g
             *
             *  "%23" is an encoded value representing "#"
             */

            return isset($user_responses[$levelNumber - 1]) ? urldecode($user_responses[$levelNumber - 1]) : null;
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

        for ($x = 0; $x < $count; $x++) {
            for ($y = 0; $y < count($updated_responses); $y++) {
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
}
