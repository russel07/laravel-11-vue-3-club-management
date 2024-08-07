<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Team;
use Validator;
use Illuminate\Http\JsonResponse;
     
class TeamController extends BaseController
{
    /**
     * Display a listing of the teams.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user_id = $request->user()->id;
        $teams = Team::where('coach_id', $user_id)->with(['club', 'sport'])->get();
        return $this->sendResponse($teams, '');
    }

    /**
     * Store a newly created team in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'coach_name' => 'required|string|max:255',
            'coach_email' => 'required|string|email|max:255',
            'club_id' => 'required|exists:clubs,id',
            'sport_id' => 'required|exists:sports,id',
            'coach_id' => 'required|exists:users,id',
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $validatedData = $request->all();

        $team = Team::create($validatedData);

        return $this->sendResponse($team, 'Team created successfully.');
    }

    /**
     * Display the specified team.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user_id = $request->user()->id;
        $team = Team::findOrFail($id);

        if(!$team) {
            return $this->sendError('Invalid Request.', ['No team found']);   
        }

        if($team->coach_id !== $user_id) {
            return $this->sendError('You don\'t have permission to view this team', ['You don\'t have permission to view this team']);  
        }

        $team->load(['club', 'sport']);

        return $this->sendResponse($team, '');
    }

    /**
     * Update the specified team in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user_id = $request->user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'coach_name' => 'required|string|max:255',
            'coach_email' => 'required|string|email|max:255',
            'club_id' => 'required|exists:clubs,id',
            'sport_id' => 'required|exists:sports,id',
            'coach_id' => 'required|exists:users,id',
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $validatedData = $request->all();

        $team = Team::findOrFail($id);

        if(!$team) {
            return $this->sendError('Invalid Request.', ['No team found']);   
        }

        if($team->coach_id !== $user_id) {
            return $this->sendError('You don\'t have permission to update this team', ['You don\'t have permission to update this team']);  
        }

        $validatedData = $request->all();

        $team->update($validatedData);

        $team->load(['club', 'sport']);

        return $this->sendResponse($team, 'Team updated successfully.');
    }

    /**
     * Remove the specified club from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( Request $request, $id )
    {
        $user_id = $request->user()->id;
        $team = Team::findOrFail($id);

        if(!$team) {
            return $this->sendError('Invalid Request.', ['No team found']);   
        }

        if($team->coach_id !== $user_id) {
            return $this->sendError('You don\'t have permission to delete this team', ['You don\'t have permission to delete this team']);  
        }

        if($team->delete()) {
            return $this->sendResponse([], 'Team deleted successfully.');
        } else {
            return $this->sendError('Update Error.', ['Something went wrong try again later']);   
        }
    }
}