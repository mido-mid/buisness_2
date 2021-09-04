<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getCities($country_id){

        $cities = DB::table('cities')->where('country_id',$country_id)->get();

        $view = view('includes.cities',compact('cities'));

        $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

        return $sections['cities'];
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255' , 'not_regex:/([%\$#\*<>]+)/'],
            'user_name' => ['required', 'string', 'max:255' ,'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birthDate' => ['required','date'],
            'phone' => ['required', 'digits:11', Rule::unique('users', 'phone')],
            'jobTitle' => ['required', 'string', 'max:255' , 'not_regex:/([%\$#\*<>]+)/'],
            'city_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'category_id' => ['required', 'not_in:0'],
            'gender' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'birthDate' => $data['birthDate'],
            'type' => 0,
            'phone' => $data['phone'],
            'jobTitle' => $data['jobTitle'],
            'city_id' => $data['city_id'],
            'country_id' => $data['country_id'],
            'gender' => $data['gender'],
            'stateId' => 4
        ]);

        foreach ($data['category_id'] as $category){
            DB::table('user_categories')->insert([
                'user_id' => $user->id,
                'category_id' => $category
            ]);
        }

        return $user;
    }
}
