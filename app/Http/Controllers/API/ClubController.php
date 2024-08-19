<?php
     
namespace App\Http\Controllers\API;
     
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Club;
     
class ClubController extends BaseController
{
    /**
     * Display a listing of the club.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $user_id = $request->user()->id;
        if( 'Admin' === $user->user_type ) {
            $clubs = Club::with('sports')->get();
        } else {
            $clubIds = Club::where('manager_id', $user_id)->pluck('id')->toArray();
            $clubs = Club::whereIn('id', $clubIds)->with('sports')->get();
        }
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
        $validatedData = $request->all();
        $rules = [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager_name' => 'required|string|max:255',
            'manager_email' => 'required|email',
            'sports' => 'array',
            'sports.*' => 'exists:sports,id'
        ];

        if ( empty($validatedData['manager_id']) ) {
            $rules['password'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if ( empty($validatedData['manager_id']) ) {
            $userData = [
                'name' => $validatedData['manager_name'],
                'email' => $validatedData['manager_email'],
                'password' => bcrypt($validatedData['password']),
                'user_type' => 'Club Admin'
            ];

            $user = User::create($userData);
            $validatedData['manager_id'] = $user->id;
        } 

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
    public function show(Request $request, $id)
    {
        $club = Club::findOrFail($id);

        if(!$club) {
            return $this->sendError('Invalid Request.', ['No club found']);   
        }

        $club->load('sports');

        return $this->sendResponse($club, '');
    }

    public function bySports (Request $request, $sport_id) 
    {
        $user_id = $request->user()->id;
        $clubs = Club::whereHas('sports', function ($query) use ($sport_id, $user_id) {
            $query->where('sport_id', $sport_id)
                ->where('manager_id', $user_id);
        })->with('sports')->get();

        if ($clubs->isEmpty()) {
            return $this->sendError('No clubs found for this sport.');
        }

        return $this->sendResponse($clubs, 'Clubs retrieved successfully.');
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
        $validatedData = $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'sports' => 'array',
            'sports.*' => 'exists:sports,id'
        ]);

        if ( empty($validatedData['manager_id']) ) {
            $rules['password'] = 'required';
        }
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $club = Club::findOrFail($id);

        if(!$club) {
            return $this->sendError('Invalid Request.', ['No club found']);   
        }

        if ( empty($validatedData['manager_id']) ) {
            $userData = [
                'name' => $validatedData['manager_name'],
                'email' => $validatedData['manager_email'],
                'password' => bcrypt($validatedData['password']),
                'user_type' => 'Club Admin'
            ];

            $user = User::create($userData);
            $validatedData['manager_id'] = $user->id;
        } 

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
    public function destroy( Request $request, $id )
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