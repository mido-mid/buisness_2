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
//        $messages = [
//            'name.required' => trans('error.name_required'),
//            'name.string' => trans('error.name_string'),
//            'name.max' => trans('error.name_max'),
//            'name.regex' => trans('error.name_regex'),
//            'user_name.required' => trans('error.user_name_required'),
//            'user_name.string' => trans('error.user_name_string'),
//            'user_name.max' => trans('error.user_name_max'),
//            'user_name.alpha_dash' => trans('error.user_name_alpha'),
//            'user_name.unique' => trans('error.user_name_unique'),
//            'email.required' => trans('error.email_required'),
//            'email.string' => trans('error.email_string'),
//            'email.email' => trans('error.email_email'),
//            'email.max' => trans('error.email_max'),
//            'email.unique' => trans('error.email_unique'),
//            'country_id.required' => trans('error.country_required'),
//            'country_id.integer' => trans('error.country_integer'),
//            'city_id.required' => trans('error.city_required'),
//            'city_id.integer' => trans('error.city_integer'),
//            'category_id.required' => trans('error.category_required'),
//            'category_id.integer' => trans('error.category_integer'),
//            'gender.required' => trans('error.gender_required'),
//            'gender.string' => trans('error.gender_string'),
//            'password.required' => trans('error.password_required'),
//            'password.string' => trans('error.password_string'),
//            'password.confirmed' => trans('error.password_confirmed'),
//            'password.min' => trans('error.password_min'),
//            'birthDate.required' => trans('error.birthdate_required'),
//            'birthDate.date' => trans('error.birthdate_date'),
//            'birthDate.before' => trans('error.birthdate_before'),
//            'jobTitle.required' => trans('error.job_title_required'),
//            'jobTitle.string' => trans('error.job_title_string'),
//            'jobTitle.regex' => trans('error.job_title_regex'),
//            'jobTitle.max' => trans('error.job_title_max'),
//            'phone.required' => trans('error.phone_required'),
//            'phone.string' => trans('error.phone_string'),
//        ];
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255' , 'not_regex:/([%\$#\*<>]+)/'],
            'user_name' => ['required', 'string', 'max:255' ,'unique:users', 'alpha_dash','not_regex:/([%\$#\*<>]+)/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birthDate' => ['required','date','before:today'],
            'phone' => ['required', 'digits:11', Rule::unique('users', 'phone')],
            'jobTitle' => ['required', 'string', 'max:255' , 'not_regex:/([%\$#\*<>]+)/'],
            'city_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'category_id' => ['required','array'],
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
        $birthDate = $data['birthDate'];

        $currentDate = date("Y-m-d");

        $age = date_diff(date_create($birthDate), date_create($currentDate));

        $user = User::create([
            'name' => $data['name'],
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'age' => $age->format("%y"),
            'birthDate' => $data['birthDate'],
            'type' => 0,
            'phone' => $data['phone'],
            'jobTitle' => $data['jobTitle'],
            'city_id' => $data['city_id'],
            'country_id' => $data['country_id'],
            'gender' => $data['gender'],
            'stateId' => 'allowed',
            'official' => 0
        ]);

        foreach ($data['category_id'] as $category){
            DB::table('user_categories')->insert([
                'userId' => $user->id,
                'categoryId' => $category
            ]);
        }

        return $user;
    }
}
