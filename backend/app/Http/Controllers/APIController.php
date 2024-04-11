<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ShortURL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\SafeBrowsingService;

class APIController extends Controller
{
    public function login(Request $request){
        $username = $request->username;
        $password = $request->password;

        // Check if this user already already has an account
        if(User::where('username', $username)->exists()){
           if(Auth::attempt(['username' => $username, 'password' => $password])){
                /** @var \App\Models\User $user **/
                $user = Auth::user();
                $token = $user->createToken('authToken')->plainTextToken;
                
                return response()->json(['message' => 'user logged in', 'user' => $user, 'token' => $token], 200);
           }
           else{
            return response()->json(['message' => 'Incorrect credentials'], 500);
           }
        }
        else{
            // Create new account
            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password)
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            if($user){
                return response()->json(['message' => 'user created', 'token' => $token, 'user' => $user], 200);
            }
        }
    }

    public function shorten(Request $request, SafeBrowsingService $safeBrowsingService){
        $url = $request->url;
       

        $row = ShortURL::where('original_url', $url)->first();
        // Check if url exists
        if($row){
            $code = $row->code;
            return response()->json(['message' => 'This url already exists', 'code' => $code], 400);
            exit(0);
        }
    

        $response = $safeBrowsingService->scanURL($url);
        $response_code = $response['response_code'];

        if($response_code === 1){
            $shortCode = $this->shortCode(6);

            $query = new ShortURL;
            $query->original_url = $url;
            $query->short_code = $shortCode;
            if($query->save()){
                return response()->json(['message' => 'URL Added', 'code' => $shortCode], 200);
            }
            
        }
       
    }

    public function code($code){
        if(ShortURL::where('short_code', $code)->exists()){
            $url = ShortURL::where('short_code', $code)->first()->original_url;
            return response(['url' => $url], 200);
        }
        else{
            return response()->json(['message' => 'Not Found or Probably expired'], 500);
        }
    }


    // Display created links
    public function history(){
        $history = ShortURL::orderBy('created_at','desc')->get();
        if(count($history)>0){
            foreach($history as $h){
                $h->time = Carbon::parse($h->created_at)->diffForHumans();
                $h->url = env('BASE_URL').'/'.$h->short_code;
            }
            return response()->json(['history' => $history], 200);
        }
        else{
            return response()->json(['message' => 'No link created yet'], 400);
        }
    }

    /**
     * Generate a unique random alphanumeric hash for URL shortening.
     * @param int $len The desired length of the hash.
     * @return string The generated hash.
     */
    private function shortCode($len = 6){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';

        /*
            This may not be necessary as the possibility of having duplicate hash is low
            But just to be sure we don't have any duplicates.
        */
        while(ShortURL::where('short_code', $str)->exists()){
            $str = ''; // Reset the hash if it already exists
        }

        // To ensure that the desired hash length is within the specific range
        $len = $len < 6 ? 6 : $len; // Minimum length of 6 characters
        $len = $len > 10 ? 10 : $len; // Maximum length of 10 characters

        $charactersLength = strlen($characters);
        $str = '';
        $str .= $characters[rand(0, $charactersLength - 1)];
        $len--;

        while ($len) {
            $str .= $characters[rand(0, $charactersLength - 1)];
            $len--;
        }

        return $str;
    }
}
