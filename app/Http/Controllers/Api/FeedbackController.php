<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{

    /**
     * @SWG\Get(
     *      path="/feedback",
     *      operationId="getUserInfo",
     *      tags={"user"},
     *      summary="User information",
     *      description="Get user",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns Auth User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->sendJson([
            'user' => $request->user()->getFullInfo()
        ]);
    }

    /**
     * @SWG\Get(
     *      path="/feedback/add",
     *      operationId="getUserInfo",
     *      tags={"user"},
     *      summary="feedback information",
     *      description="Get user",
     *      security={{"X-Api-Token":{}}},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns Auth User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'rating' => 'required',
            'cooment' => 'text',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        Feedback::create([
            "user_id" =>  request("user_id"),
            "rating" =>  request("rating"),
            "comment" => request("comment")
        ]);

        return $this->sendJson(["message" => "Thanks for your feedback."]);

    }
}