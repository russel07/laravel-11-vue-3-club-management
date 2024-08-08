<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Club;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    /**
     * Create User api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $input = $request->all();

        $fields = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ];

        if ( isset($input['user_type']) && 'Athlete' === $input['user_type']) {
            $fields['team_id'] = 'required';
            $fields['gender'] = 'required';
            $fields['birth_year'] = 'required';
        }

        $validator = Validator::make($request->all(), $fields);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->load('teams');
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user'] =  $this->getUserData( $user );
   
        return $this->sendResponse($success, 'User register successfully.');
    }
     
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['user'] =  $this->getUserData( $user );
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
     * Create User api
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request): JsonResponse
    {
        $input = $request->all();

        $fields = [
            'name'      => 'required',
            'gender'    => 'required',
            'email'     => 'required|email',
            'password'  => 'required',
        ];

        if ( isset($input['user_type']) && 'Athlete' === $input['user_type']) {
            $fields['team_id'] = 'required';
            $fields['birth_year'] = 'required';
        }

        $validator = Validator::make($request->all(), $fields);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        if( 'Coach' === $user->user_type ) {
            $user->load('teams');
        }else if( 'Athlete' === $user->user_type ) {
            $user->load('team');
        }
        
        return $this->sendResponse($user, $input['user_type'].' register successfully.');
    }

    public function get_coach( Request $request ) 
    {
        $user = $request->user();
        if( 'Club Admin' === $user->user_type ) {
            $clubIds = Club::where('manager_id', $user->id)->pluck('id')->toArray();
            $userIds = Team::whereIn('club_id', $clubIds)->pluck('coach_id')->toArray();
            $users = User::with('teams')->whereIn('id', $userIds)->where('user_type', 'Coach')->get();
            return $this->sendResponse($users, '');
        } else {
            return $this->sendError('You don\'t have permission to view coach list', ['You don\'t have permission to view coach list']);
        }
    }

    public function getUserByEmail( Request $request , $email ) 
    {
        $user = $request->user();
        $user = User::where('email', $email)->first();
        return $this->sendResponse($user, '');
    }

    public function byUserType(Request $request, $type) {
        switch ($type) {
            case 'Coach':
                $users = User::with('teams')->where('user_type', $type)->get();
                break;
    
            case 'Athlete':
                $users = User::with('team')->where('user_type', $type)->get();
                break;
    
            default:
                $users = User::where('user_type', $type)->get();
                break;
        }
    
        return $this->sendResponse($users, '');
    }

        /**
     * Update User api
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if(!$user) {
            return $this->sendError('Invalid Request.', ['No user found']);   
        }

        $input = $request->all();

        $fields = [
            'name'      => 'required',
            'gender'    => 'required',
            'email'     => 'required|email',
        ];

        if ( isset($input['user_type']) && 'Athlete' === $input['user_type']) {
            $fields['team_id'] = 'required';
            $fields['birth_year'] = 'required';
        }

        $validator = Validator::make($request->all(), $fields);
     
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        unset($input['password']);

        $user->update($input);

        if( 'Coach' === $user->user_type ) {
            $user->load('teams');
        }else if( 'Athlete' === $user->user_type ) {
            $user->load('team');
        }
        
        return $this->sendResponse($user, $input['user_type'].' updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy( Request $request, $id )
    {
        $user_id = $request->user()->id;
        $user = User::findOrFail($id);
        $user_type = $user->user_type;

        if( ! $user ) {
            return $this->sendError('Invalid Request.', ['No user found']);   
        }

        if($user->delete()) {
            return $this->sendResponse([], $user_type.' deleted successfully.');
        } else {
            return $this->sendError('Delete Error.', ['Something went wrong try again later']);   
        }
    }
    

    private function getUserData ( $user ) {
       return [
            'id'                =>  $user->id,
            'team_id'           =>  $user->team_id,
            'name'              =>  $user->name,
            'gender'            =>  $user->gender,
            'user_type'         =>  $user->user_type,
            'birth_year'        =>  $user->birth_year,
            'email'             =>  $user->email,
            'user_type'         =>  $user->user_type,
            'teams'             => $user->teams,
        ];
    }
}