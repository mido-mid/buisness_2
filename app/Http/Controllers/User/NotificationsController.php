<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\FieldValue;
use Auth;
use App\User;

class NotificationsController extends Controller
{
    //

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //------------------------------------------------Method--End--Begin----------------------------------------------------------------------//
    public function index()
    {
        $docid = Auth::User()->id;
        $databaseinstanceSet = app('firebase.firestore')->database()->collection('Notification')->document($docid)->collection('NotificationBody')->orderBy("createAt", "DESC");
        $documents = $databaseinstanceSet -> documents();
        foreach($documents as $document)
        {
            if ($document->exists())
            {
                $notification = $document->data();
                $notifications[] = $notification ;
            }
        }
        $databaseinstanceSet = app('firebase.firestore')->database()->collection('Notification')->document($docid);
                $databaseinstanceSet->update([
                    ['path' => 'notificationUnReadCtr','value' => 0],
                    ]);
        return view('User.notifications.notifications')->with('notifications',$notifications);
    }
   //------------------------------------------------Method--End--Begin----------------------------------------------------------------------//
   public function savetoken(Request $request)
   {
    $docid = Auth::User()->id;
    $databaseinstance = app('firebase.firestore')->database()->collection('Users')->document($docid);
    $data = [
        'active' => true,
        'webToken' => $request->token,
    ];
    if($databaseinstance ->set($data,['merge' => true]))
    {
        return response()->json(['code'=>0]);
    }else{
        return response()->json(['code'=>200]);
    }
   }
    //------------------------------------------------Method--End--Begin----------------------------------------------------------------------//
   public function fireNotification($model_id,$type)
   {
    //------------------------------------------------Variable--Declaration--Begin-----------------------------------------------------------//
       $notificationTitle = '';
       $notificationBody = '';
       $notificationType = '';
       $notificationModel = '';
       $notificationPrepositions = '';
       $senderID = 0;
       $senderName = '';
       $receiverID = 0;
       $receiverToken = '';
       $createAt = time()*1000;
    //------------------------------------------------Variable--Declaration--End-----------------------------------------------------------//

    //------------------------------------------------Notification--Preparation--Begin-----------------------------------------------------//
    
    if($type == "react")          //this code Area is responsible for getting author id and get the type of notification ie: react or post
    {
        $notificationType = " reacted";
        $notificationModel =  DB::table('likes')->where('model_id',$model_id)->value('model_type');
        $notificationPrepositions = " To Your ";
        if($notificationModel == "post")
        {
            $notificationBody = DB::table('posts')->where('id',$model_id)->value('body');
        }
        $senderID = Auth::User()->id;
    }elseif($type == "comment")
    {
        $notificationType = " commented";
        $notificationModel = "post" ;
        $notificationPrepositions = " On Your ";
        $senderID = Auth::User()->id;
        $notificationBody = DB::table('comments')->where('id',$model_id)->value('body');
    }elseif($type == "reply")
    {
        $notificationType = " replied";
        $notificationModel = "comment" ;
        $notificationPrepositions = " To Your ";
        $senderID = Auth::User()->id;
    }elseif($type == "share")
    {
        $notificationType = " shared";
        $notificationModel = "post" ;
        $notificationPrepositions = " Your ";
        $senderID = Auth::User()->id;
    }
    $receiverID = DB::table('posts')->where('id',$model_id)->value('publisherId');
    if($receiverID == null)
    {
        $receiverID = DB::table('comments')->where('id',$model_id)->value('user_id');
    }
                                                      
    $senderName = DB::table('users')->where('id',$senderID)->value('name');
    if($notificationBody == null)
    {
        $notificationBody = DB::table('comments')->where('id',$model_id)->value('body');
    }
    $notificationTitle = $senderName.$notificationType.$notificationPrepositions.$notificationModel;
    $notificationBody = substr($notificationBody,0,15);
    //------------------------------------------------Notification--Preparation--End-----------------------------------------------------//

    //------------------------------------------------Firebase--Dealing--Begin-----------------------------------------------------------//
    
    if($receiverID != Auth::User()->id)
    {
    $databaseinstanceSet = app('firebase.firestore')->database()->collection('Notification')->document($receiverID)->collection('NotificationBody')->newDocument();
    $notificationData = [
        'body' => $notificationTitle."( ".$notificationBody." )",                            //this code responsible for setting notification inside firebase 
        'createAt' => $createAt,
        'targetId' => $model_id,
        'targetName' => $notificationModel,
        'title' => $notificationTitle,
    ];
    $databaseinstanceSet ->set($notificationData);
    $databaseinstanceSet = app('firebase.firestore')->database()->collection('Notification')->document($receiverID);
    $databaseinstanceSet->update([
        ['path' => 'notificationUnReadCtr','value' => FieldValue::increment(1)]
        ]);

    $databaseinstanceGet = app('firebase.firestore')->database()->collection('Users')->document($receiverID);
    $snapshot = $databaseinstanceGet->snapshot();               //this code responsible for getting author token
    $firebaseWebToken =  $snapshot->data()['webToken'];
    $firebaseMobToken =  $snapshot->data()['token'];
    //------------------------------------------------Firebase--Dealing--End------------------------------------------------------------//

    //------------------------------------------------Sending--Notification--Begin------------------------------------------------------//
   // $firebaseToken = $receiverToken;
    $SERVER_API_KEY = 'AAAAaD6g69M:APA91bEx4CMm_Fs37PloKmvt5RIURXJr5Elc8a7Tb3Ox8wx8HLTrenuuD3pDG9jNNalV_hew9fKxSNUhAdxjG6pM2sfHqoRGyaRiKR6TfFeQsQzeX59Nxd-mMmAkOlBNnwUCLpJJwAq9';
    
    $data = [
        //"to" => $firebaseToken,
        "registration_ids" => [
            $firebaseWebToken,
            $firebaseMobToken,
        ],
        "notification" => [
            "title" => $notificationTitle,
            "body" => $notificationTitle."( ".$notificationBody." )",
            "click_action" => "/chatroom",
            "content_available" => true,
            "priority" => "high",
            
        ]
        
    ];
    $dataString = json_encode($data);

    $headers = [
        'Authorization: key='.$SERVER_API_KEY,
        'Content-Type: application/json',
    ];
        
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    $response = curl_exec($ch);
    dd($response);
    }
   }

   //------------------------------------------------Sending--Notification--End--------------------------------------------------------//
//------------------------------------------------Method--End--Begin----------------------------------------------------------------------//
   public function deleteNotifications()
   {
    $docid = Auth::User()->id;
    $databaseinstanceDel = app('firebase.firestore')->database()->collection('Notification')->document($docid)->collection('NotificationBody');
    $documents = $databaseinstanceDel -> documents();
    foreach($documents as $document)
    {
        if ($document->exists())
        {
          //$document->id()
          //$document->delete();
          $document->reference()->delete();
        }
    }
    $databaseinstanceDel = app('firebase.firestore')->database()->collection('Notification')->document($docid)->collection('NotificationBody')->newDocument();
    $notificationData = [
        'body' => "",                            //this code responsible for setting notification inside firebase 
        'createAt' => "",
        'targetId' => "",
        'targetName' => "",
        'title' => "",
    ];
    $databaseinstanceDel ->set($notificationData);
    return redirect()->back()->with('DELETED', 'Deleted');
   }
    //------------------------------------------------Method--End--Begin----------------------------------------------------------------------//
}
