<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user= User::where('email', $request->email)->first();
       
        if (!$user || !Hash::check($request->password, $user->password) || $user->is_delete == 1) {
            return response([
                "status" => "0",
                "title" => "Credential failed!",
                'message' => "Oops, Login failed! Your email or password might be incorrect."
                    ], 200);
        }
       
        if ($user->is_active == 0) {
            return response([
                "status" => "0",
                "title" => "Account suspended!",
                'message' => "Very sorry! Your account has been suspended."
                    ], 200);
        }
    
        $token = $user->createToken('my-app-token')->plainTextToken;
    
        $response = [
            'status' => '1',
            'user' => $user,
            'accessToken' => $token
        ];
        return response($response, 200);
    
    }  

    public function user_register(Request $request){

        try {
            $users = new User;
            $users->name = isset($request->name) ? $request->name:"";
            $users->mobile_no = isset($request->mobile_no) ? $request->mobile_no:"";
            $users->email = isset($request->email) ? $request->email:"";
            $users->password = isset($request->password) ? Hash::make($request->password):"";
            $users->address = isset($request->address) ? $request->address : "";
            $users->is_active = isset($request->is_active) ? $request->is_active : "1";
            $users->is_delete = isset($request->is_delete) ? $request->is_delete : "0";
            $data = $users->save();

            $status = 201;
            if ($data) {
                $status = 200;
                $response['status'] = "1";
                $response['title'] = "successfully";
                $response['message'] = "User has been created successfully.";
            } else {
                $status = 201;
                $response['status'] = "0";
                $response['title'] = "failure";
                $response['message'] = "Oops! Something went wrong.";
            }
            return response($response, $status);
        } catch (\Exception $e) {
            $response['status'] = "0";
            $response['Error'] = $e->getMessage();
            return response($response, 500);
        }
    }



    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        $response = [
            'status' => '1',
            'title' => "Logout",
            'message' => "Logout successfully"
        ];
        return response($response, 200);
    }

    public function unauthorised() {
        $response = [
            'title' => "Unauthorised",
            'message' => "Careful! We found an unauthorized access."
        ];

        return response($response, 401);
    }
}
