<?php

namespace App\Http\Controllers;

use App\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ResetPasswordController extends Controller
{
    public function sendEmail(Request $request){
      if (!$this->validateEmail($request->email)) {
          return $this->failedResponse();
      }
        $this->send($request->email);
        return $this->sucessResponse();
    }

    public function send($email){
        $token = $this->createToken($email);
        Mail::to($email)->send (new ResetPasswordMail($token));
    }

    public function createToken($email) {
       
        $oldToken = DB::table('password_resets')->where('email',$email)->first();

        if ($oldToken) {
            return $oldToken;
        }
        $token = Str::random(60);
        $this->saveToken($token,$email);

        return $token;
    }

    public function saveToken($token,$email){
      DB::table('password_resets')->insert([
          'email' => $email,
          'token' => $token,
          'created_at' => Carbon::now()
      ]);
    }



    
    public function validateEmail($email)
    {
        return !!User::where('email',$email)->first();  
    }

    public function failedResponse() {
        return response()->json([
            'error'=> 'Email doesn\'t exist'
        ], Response::HTTP_NOT_FOUND);
    }

    public function sucessResponse() {
        return response()->json([
            'data'=> 'Reset email sent sucessfully, please check your email'
        ], Response::HTTP_OK);
    }
}
