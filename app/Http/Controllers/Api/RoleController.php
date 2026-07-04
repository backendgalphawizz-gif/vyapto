<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Role;
use Illuminate\Http\Request;
use App\Models\Api\UserToken;
use Validator;

class RoleController extends Controller
{
    // Get all roles
    public function getRole(Request $request)
    {

		$validator = Validator::make($request->all(), [
			'role_id' => 'required'
		]);
		
		if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
		
		$token = str_replace('Bearer ', '', $request->header('Authorization'));
		if(!empty($token)){
		    $userToken = UserToken::where('token', $token)->first();
			if (!$userToken) {
				return response()->json(['status' => false, 'message' => 'Invalid or expired token'], 401);
			}
		}

        $roles = Role::find($request->role_id);
        return response()->json([
            'status' => true,
			'code' => 200,
            'message' => 'roles Details ',
            'data' => $roles
        ]);
    }
}