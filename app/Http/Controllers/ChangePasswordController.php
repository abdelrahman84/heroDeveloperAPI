<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ChangePasswordRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\User;

class ChangePasswordController extends Controller
{
    public function changePassword(ChangePasswordRequest $request) {
        
        return $this->getPasswordResetTableRow($request)->count()>0 ? $this->makeNewPassword($request) : $this->tokenNotFound();
    }

    private function getPasswordResetTableRow($request) {

        return DB::table('password_resets')->where(['email' => $request->email, 'token'=>$request->resetToken]);
    }

    private function tokenNotFound() {
      
        return response()->json(['error' => 'Token or Email incorrect'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function makeNewPassword($request) {

        $user = User::whereEmail($request->email)->first();
        
        $user->update(['password' => $request->password]);

        $this->getPasswordResetTableRow($request)->delete();

        return response()->json([
            'data' => 'Password sucessfully changed'
        ], Response::HTTP_CREATED);

    }
}
