<?php

namespace App\Http\Controllers\Api\Usama;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Usama\Alarm;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class AlarmController extends Controller
{
    use GeneralTrait;

    protected $days = [
        'Saturday',
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday'
    ];
    //Valve
    // 0 => valve1
    // 1 => valve2
    //2 => both

    public function InsertToFirebase($uid,$valve,$state){
        $res ="
        <!-- The core Firebase JS SDK is always required and must be listed first -->
        <script src=\"https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js\"></script>
        <script src=\"https://www.gstatic.com/firebasejs/8.6.1/firebase-database.js\"></script>
        <script src=\"https://www.gstatic.com/firebasejs/8.6.1/firebase-analytics.js\"></script>
        <script>
          // Your web app's Firebase configuration
          // For Firebase JS SDK v7.20.0 and later, measurementId is optional
          var firebaseConfig = {
            apiKey: \"AIzaSyCbttqpYQiwCK9MsZ3wsxC2SGWY8YzvewI\",
            authDomain: \"ecofarm-2ade1.firebaseapp.com\",
            databaseURL: \"https://ecofarm-2ade1-default-rtdb.firebaseio.com\",
            projectId: \"ecofarm-2ade1\",
            storageBucket: \"ecofarm-2ade1.appspot.com\",
            messagingSenderId: \"916162934492\",
            appId: \"1:916162934492:web:1d8bf41166c8711b86e619\",
            measurementId: \"G-1HSBQ1QJDC\"
          };
          // Initialize Firebase
          firebase.initializeApp(firebaseConfig);
          var database = firebase.database();
          if($valve == '0'){
            firebase.database().ref('$uid'+'/valve1/').set({
                leave: 0,
                mode:'timer',
                onOff :$state,
              });
            }else if($valve == '1'){
               firebase.database().ref('$uid'+'/valve2/').set({
                leave: 0,
                mode:'timer',
                onOff : $state,
              });
            }else if ($valve == '2'){
             firebase.database().ref('$uid'+'/valve1/').set({
                leave: 0,
                mode:'timer',
                onOff :$state,
              });
               firebase.database().ref('$uid'+'/valve2/').set({
                leave: 0,
                mode:'timer',
                onOff :$state,
              });
            }
        </script>
        ";
       return $res;
    }

    public function addAlarm(Request $request){
        $validator = Validator::make($request->all(), [
            'uid' => ['required'],
            'valve' => ['required'],
            'start_at' => ['required'],
            'end_at' => ['required'],
            'day' => ['required'],
        ]);
        if ($validator->fails()) {
            return $this->returnValidationError(422, $validator);
        }else {
            $uid = $request->uid;
            $valve = $request->valve;
            $start_at = $request->start_at;
            $end_at = $request->end_at;
            $day = $request->day;
            if($request->day == 'all'){
                foreach($this->days as $dayx){
                    $alarm = Alarm::create([
                        'uid' => $uid,
                        'valve' => $valve,
                        'start_at' => $start_at,
                        'end_at' => $end_at,
                        'day' => $dayx,
                        'state'=>0
                    ]);
                }
            }else{
                $alarm = Alarm::create([
                    'uid' => $uid,
                    'valve' => $valve,
                    'start_at' => $start_at,
                    'end_at' => $end_at,
                    'day' => $day,
                    'state'=>0
                ]);
            }


            $msg = 'Done';
            return $this->returnSuccessMessage(202, 'correct email &' . $msg . '');
        }

    }
    public function ChangeState($alarm_id){
        date_default_timezone_set('Africa/Cairo');

        $alarm = Alarm::find($alarm_id);
        $time_now = date('H:i:s');

        $start=date('H:i:s', strtotime($alarm->start_at));
        $end=date('H:i:s', strtotime($alarm->end_at));

        if($time_now >= $start && $time_now <= $end){
            $alarm->update([
                'state'=>1
            ]);
            return 1;
        }else{
            $alarm->update([
                'state'=>0
            ]);
            return 0;
        }
    }
    public function UpdateUser($uid){
        $alarms = Alarm::where('uid',$uid)->get();
        $result= [];
        foreach ($alarms as $alarm){
            $state = $this->ChangeState($alarm->id);
            $result[]= [
                'id'=>$alarm->id,
                'state'=>$state
            ];
        }
        return redirect()->route('add/firebase/{uid}/{valve}/{state}');

    }
    public function UpdateAllUsers(){
        $users = User::get();
        foreach ($users as $user) {
            $uid = $user->id;
            $alarms = Alarm::where('uid', $uid)->get();
            $result = [];
            foreach ($alarms as $alarm) {
                $state = $this->ChangeState($alarm->id);
                $result[] = [
                    'id' => $alarm->id,
                    'state' => $state
                ];

               $this->InsertToFirebase($uid,$alarm->valve,$state);
            }
            return $result;
        }
    }
    public function GetUserALarms($uid){
        $alarms = Alarm::where('uid', $uid)->get();
        return $this->returnData(['user alarms'], [$alarms], count($alarms));
    }
}
