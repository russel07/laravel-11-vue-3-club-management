<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use Workbench\App\Models\User as ModelsUser;

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

        $user->load('teams');
        
        return $this->sendResponse($user, $input['user_type'].' register successfully.');
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