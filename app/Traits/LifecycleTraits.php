<?php

namespace App\Traits;

trait LifecycleTraits
{
    /*  initiateUpdateLifecycleProgress() method:
     *
     *  This is used to save the lifecycle stage data for an existing jobcard.
     *  As the user progress through each stage they are asked to save the data
     *  they have provided before continuing to the next step. This data is saved
     *  using this method. It also works to store the update activity and
     *  broadcasting of notifications to users concerning the update of
     *  the jobcard lifecycle stages. An example is when the user saves
     *  data provided in the "job started" stage.
     *
     *
     */
    public function initiateUpdateLifecycleProgress($modelId = null, $modelType = null, $stageData = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /**************************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE MODEL LIFECYCLE   *
         **************************************************************/

        //  Get the model and stage data
        $modelId = request('modelId');
        $modelType = request('modelType');
        $stageData = request('stage');

        if (!isset($stageData) || empty($stageData)) {
            //  Stage instance not specified - Log the error
            $response = oq_api_notify_error('Include the stage data e.g) { stage: { type: \'payment\', instance: 1 } }', null, 404);
        } elseif (!isset($stageData['type']) || empty($stageData['type'])) {
            //  Stage type not specified - Log the error
            $response = oq_api_notify_error('Include the stage type e.g) stage: { type: \'open\' } or { type: \'payment\' }, e.t.c', null, 404);
        } elseif (!isset($stageData['instance']) || empty($stageData['instance'])) {
            //  Stage instance not specified - Log the error
            $response = oq_api_notify_error('Include the stage instance e.g) { instance: 1 } or { instance: 2 }, e.t.c', null, 404);
        }

        if (!isset($modelType) || empty($modelType)) {
            //  Model type not specified - Log the error
            $response = oq_api_notify_error('Include model type e.g) modelType=order or modelType=jobcard, e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } elseif (!isset($modelId) || empty($modelId)) {
            //  Model id not specified - Log the error
            $response = oq_api_notify_error('Include associated model id e.g) modelId=1 or modelId=2, e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } else {
            //  Create the dynamic model
            $dynamicModel = oq_generateDynamicModel($modelType);

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Find the associated record by model id
                try {
                    $dynamicModel = $dynamicModel::find($modelId);

                    //  Check if we have a result
                    if ($dynamicModel) {
                        /*****************************
                         *   SEND NOTIFICATIONS      *
                         *****************************/

                        //  $auth_user->notify(new LifecycleUpdated($dynamicModel));

                        //  Eager load other relationships wanted if specified
                        if (strtolower(request('connections'))) {
                            $dynamicModel->load(oq_url_to_array(strtolower(request('connections'))));
                        }

                        /*****************************
                         *   RECORD ACTIVITY         *
                         *****************************/

                        //  Record activity of lifecycle updated
                        $status = 'updated lifecycle stage';
                        $lifecycleUpdatedActivity = oq_saveActivity($dynamicModel, $auth_user, $status, $stageData);

                        //  Action was executed successfully
                        return ['success' => false, 'response' => $dynamicModel];
                    } else {
                        //  No resource found
                        return ['success' => false, 'response' => oq_api_notify_no_resource()];
                    }
                } catch (\Exception $e) {
                    //  Log the error
                    $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                    //  Return the error response
                    return ['success' => false, 'response' => $response];
                }
            } else {
                //  Model class does not exist - Log the error
                $response = oq_api_notify_error('Invalid model - e.g) must be jobcard/order', null, 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
            }
        }
    }

    public function initiateUndoLifecycleProgress()
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /**************************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE MODEL LIFECYCLE   *
         **************************************************************/

        //  Get the model and stage data
        $modelId = request('modelId');
        $modelType = request('modelType');
        $stageData = request('stage') ?? [];

        if (!isset($stageData) || empty($stageData)) {
            //  Stage instance not specified - Log the error
            $response = oq_api_notify_error('Include the stage data e.g) { stage: { type: \'payment\', instance: 1 } }', null, 404);
        } elseif (!isset($stageData['type']) || empty($stageData['type'])) {
            //  Stage type not specified - Log the error
            $response = oq_api_notify_error('Include the stage type e.g) stage: { type: \'open\' } or { type: \'payment\' }, e.t.c', null, 404);
        } elseif (!isset($stageData['instance']) || empty($stageData['instance'])) {
            //  Stage instance not specified - Log the error
            $response = oq_api_notify_error('Include the stage instance e.g) { instance: 1 } or { instance: 2 }, e.t.c', null, 404);
        } else {
            $stageData = array(
                'type' => $stageData['type'],
                'instance' => $stageData['instance'],
                'skip_status' => $stageData['skip_status'] ?? false,
                'manual_verification_status' => $stageData['manual_verification_status'] ?? false,
                'pending_status' => $stageData['pending_status'] ?? false,
                'cancelled_status' => $stageData['cancelled_status'] ?? false,
            );
        }

        if (!isset($modelType) || empty($modelType)) {
            //  Model type not specified - Log the error
            $response = oq_api_notify_error('Include model type e.g) modelType=order or modelType=jobcard, e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } elseif (!isset($modelId) || empty($modelId)) {
            //  Model id not specified - Log the error
            $response = oq_api_notify_error('Include associated model id e.g) modelId=1 or modelId=2, e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } else {
            //  Create the dynamic model
            $dynamicModel = oq_generateDynamicModel($modelType);

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Find the associated record by model id
                try {
                    $dynamicModel = $dynamicModel::find($modelId);

                    //  Check if we have a result
                    if ($dynamicModel) {
                        /*****************************
                         *   SEND NOTIFICATIONS      *
                         *****************************/

                        //  $auth_user->notify(new LifecycleUpdated($dynamicModel));

                        //  Eager load other relationships wanted if specified
                        if (strtolower(request('connections'))) {
                            $dynamicModel->load(oq_url_to_array(strtolower(request('connections'))));
                        }

                        /*****************************
                         *   RECORD ACTIVITY         *
                         *****************************/

                        //  Record activity of lifecycle reversed
                        $status = 'reversed lifecycle stage';
                        $lifecycleUndoActivity = oq_saveActivity($dynamicModel, $auth_user, $status, $stageData);

                        //  Action was executed successfully
                        return ['success' => true, 'response' => $dynamicModel];
                    } else {
                        //  No resource found
                        return ['success' => false, 'response' => oq_api_notify_no_resource()];
                    }
                } catch (\Exception $e) {
                    //  Log the error
                    $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                    //  Return the error response
                    return ['success' => false, 'response' => $response];
                }
            } else {
                //  Model class does not exist - Log the error
                $response = oq_api_notify_error('Invalid model - e.g) must be jobcard/order', null, 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
            }
        }
    }

    public function setToPendingPayment($modelId = null, $modelType = null){
        $stageData = [
            "type" => "payment",
            "instance" => 1, 
            "updated_stage_id" => null,
            "skip_status" => false,
            "pending_status" => true,
            "cancelled_status" => false,
            "failed_payment_status" => false,
            "notified_client_status" => false,
            "meta" => []
        ];
        return $this->initiateUpdateLifecycleProgress($modelId, $modelType, $stageData);
    }

    public function setToFailedPayment($modelId = null, $modelType = null){
        $stageData = [
            "type" => "payment",
            "instance" => 1, 
            "updated_stage_id" => null,
            "skip_status" => false,
            "pending_status" => false,
            "cancelled_status" => false,
            "failed_payment_status" => true,
            "notified_client_status" => false,
            "meta" => []
        ];
        return $this->initiateUpdateLifecycleProgress($modelId, $modelType, $stageData);
    }

    /*  summarize() method:
     *
     *  This is used to limit the information of the resource to very specific
     *  columns that can then be used for storage. We may only want to summarize
     *  the data to very important information, rather than storing everything along
     *  with useless information. In this instance we specify table columns
     *  that we want (we access the fillable columns of the model), while also
     *  removing any custom attributes we do not want to store
     *  (we access the appends columns of the model),
     */
    public function summarize()
    {
        //  Collect and select table columns
        return collect($this->fillable)
                //  Remove all custom attributes since the are all based on recent activities
                ->forget($this->appends);
    }
}
