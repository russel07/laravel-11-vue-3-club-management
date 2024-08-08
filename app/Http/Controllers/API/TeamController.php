<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Club;
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
        $user = $request->user();
        $teams = collect();
    
        switch ($user->user_type) {
            case 'Coach':
                $teams = Team::where('coach_id', $user->id)->with(['club', 'sport'])->get();
                break;
    
            case 'Club Admin':
                $clubIds = Club::where('manager_id', $user->id)->pluck('id')->toArray();
                $teams = Team::whereIn('club_id', $clubIds)->with(['club', 'sport'])->get();
                break;
    
            default:
                break;
        }
    
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

        $validatedData = $request->all();
        $rules = [
            'name' => 'required|string|max:255',
            'coach_name' => 'required|string|max:255',
            'coach_email' => 'required|string|email|max:255',
            'club_id' => 'required|exists:clubs,id',
            'sport_id' => 'required|exists:sports,id',
        ];

        if ( empty($validatedData['coach_id']) ) {
            $rules['password'] = 'required';
        }


        $validator = Validator::make($request->all(), $rules);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if ( ! isset($validatedData['coach_id']) ) {
            $userData = [
                'name' => $validatedData['coach_name'],
                'email' => $validatedData['coach_email'],
                'password' => bcrypt($validatedData['password']),
                'user_type' => 'Coach'
            ];

            $user = User::create($userData);
            $validatedData['coach_id'] = $user->id;
        } 

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
            'club_id' => 'required|exists:clubs,id',
            'sport_id' => 'required|exists:sports,id',
        ]);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $validatedData = $request->all();

        $team = Team::findOrFail($id);

        if(!$team) {
            return $this->sendError('Invalid Request.', ['No team found']);   
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