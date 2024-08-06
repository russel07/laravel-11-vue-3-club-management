<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Sport;

class SportController extends BaseController 
{
    /**
     * Display a listing of the sports.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sports = Sport::all();
        return $this->sendResponse($sports, '');
    }

    /**
     * Store a newly created sport in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $fields = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $fields);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $input = $request->all();

        $sport = Sport::create($input);
   
        return $this->sendResponse($sport, 'Sport created successfully.');
    }

    /**
     * Display the specified sport.
     *
     * @param  \App\Models\Sport  $sport
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);

        if(!$sport) {
            return $this->sendError('Invalid Request.', ['No sports found']);   
        }

        return $this->sendResponse($sport, '');
    }

    /**
     * Update the specified sport in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $sport = Sport::findOrFail($id);

        if(!$sport) {
            return $this->sendError('Invalid Request.', ['No sports found']);   
        }

		$sport = $sport->fill($request->all());

        if($sport->save()) {
            return $this->sendResponse($sport, 'Sport updated successfully.');
        } else {
            return $this->sendError('Update Error.', ['Something went wrong try again later']);   
        }
    }

    /**
     * Remove the specified sport from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( Request $request, $id )
    {
        $sport = Sport::findOrFail($id);

        if(!$sport) {
            return $this->sendError('Invalid Request.', ['No sports found']);   
        }

        if($sport->delete()) {
            return $this->sendResponse([], 'Sport deleted successfully.');
        } else {
            return $this->sendError('Update Error.', ['Something went wrong try again later']);   
        }
    }
}
