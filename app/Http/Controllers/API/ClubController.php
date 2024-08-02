<?php
     
namespace App\Http\Controllers\API;
     
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Club;
     
class ClubController extends BaseController
{
    /**
     * Display a listing of the club.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $clubs = Club::with('sports')->get();
        return $this->sendResponse($clubs, '');
    }

    /**
     * Store a newly created club in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email',
            'sports' => 'array',
            'sports.*' => 'exists:sports,id'
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $validatedData = $request->all();

        $club = Club::create($validatedData);

        if (isset($validatedData['sports'])) {
            $club->sports()->attach($validatedData['sports']);
        }

        $club->load('sports');

        return $this->sendResponse($club, 'Club created successfully.');
    }

    /**
     * Display the specified club.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $club = Club::findOrFail($id);

        if(!$club) {
            return $this->sendError('Invalid Request.', ['No club found']);   
        }

        $club->load('sports');

        return $this->sendResponse($club, '');
    }

    /**
     * Update the specified club in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email',
            'sports' => 'array',
            'sports.*' => 'exists:sports,id'
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $club = Club::findOrFail($id);

        if(!$club) {
            return $this->sendError('Invalid Request.', ['No club found']);   
        }

        $validatedData = $request->all();

        $club->update($validatedData);

        if (isset($validatedData['sports'])) {
            $club->sports()->sync($validatedData['sports']);
        }

        $club->load('sports');

        return $this->sendResponse($club, 'Club updated successfully.');
    }

    /**
     * Remove the specified club from storage.
     *
     * @param   int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( $id )
    {
        $club = Club::findOrFail($id);

        if(!$club) {
            return $this->sendError('Invalid Request.', ['No club found']);   
        }

        $club->sports()->detach();

        if($club->delete()) {
            return $this->sendResponse([], 'Club deleted successfully.');
        } else {
            return $this->sendError('Update Error.', ['Something went wrong try again later']);   
        }
    }
}