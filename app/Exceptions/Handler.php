<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        /*  API Error handling
         */
        if (oq_viaAPI($request)) {
            switch (true) {
                /*  Error handle if the model data does not exist. Helpful for error handling especially for
                 *  Route-Model binding scenerios e.g create(Company $company){} but the resource is not found
                 */
                case $e instanceof ModelNotFoundException:
                    //  Found inside helper.php function
                    return oq_api_notify_no_resource();
                    break;
                /*  Error handle if the route to a page does not exist
                 */
                case $e instanceof NotFoundHttpException:
                //  Found inside helper.php function
                return oq_api_notify_no_page();
                    break;
                /*  Error handle if the request method is not supported
                 */
                case $e instanceof MethodNotAllowedHttpException:
                //  Found inside helper.php function
                return oq_api_notify(['message' => 'Method not allowed'], 405);
                    break;
                /*  Error handle if the resource relationship is not found
                 *  e.g) when we use Model::with($relationship) and its not found
                 */
                case $e instanceof RelationNotFoundException:
                //  Found inside helper.php function
                return oq_api_notify(['message' => 'Relationship not found'], 404);
                    break;
            }
        }

        return parent::render($request, $e);
    }
}
