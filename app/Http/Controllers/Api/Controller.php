<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Response;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sendJsonErrors($errors, $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->sendJsonResponse(Response::prepareErrorResponse($errors, $status));
    }


    protected function sendJson($data = [])
    {
        return $this->sendJsonResponse(Response::prepareResponseOk($data));
    }


    private function sendJsonResponse($data)
    {
        return response()->json($data);
    }

    protected function prepareCollection(Collection $collection, $name = 'collection'){
        return [
            $name => $collection,
            'count' => $collection->count()
        ];
    }

    public function getApiInfo()
    {
        return $this->sendJson([
            'version' => config('app.version'),
            'time' => time(),
            'date' => date('Y-m-d H:i:s')
        ]);
    }
}
