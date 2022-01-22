<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\FieldValue;
use Auth;
use App\User;

class ChatController extends Controller
{
    //

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('User.chat.chatRoom');
    }
     //----------------------------------------Method-End-Begin------------------------------------//
   public function getAllChatRooms() 
   {
        $chatRooms = collect ();
        $chatRoomsCollections = collect ();
        $databaseinstance = app('firebase.firestore')->database()->collection('Thread');
        $documents = $databaseinstance -> documents();
        //try {
         foreach($documents as $document)
         {
          if ($document->exists())
          {
              if(str_contains($document->id(),'-'.Auth::User()->id)||str_contains($document->id(),Auth::User()->id.'-'))
              {
                  $chatRooms = $document->data();
                  $chatRoomsId = explode("-",$document->id());;
                  if(Auth::User()->id == $chatRoomsId[0])
                  {
                    $chatRooms['senderId'] = $chatRoomsId[1];
                  }elseif (Auth::User()->id == $chatRoomsId[1])
                  {
                    $chatRooms['senderId'] = $chatRoomsId[0];
                  }
                  $chatRooms['senderName']=$document->data()[$chatRooms['senderId']]['name'];
                  $chatRooms['senderImage']=$document->data()[$chatRooms['senderId']]['image'];
                  $chatRooms['lastMessage'] = str_replace(" ", "-",$document->data()['lastMessage']);
                  $rooms[] = $chatRooms ;
              }
          }
         }
  // }// catch(\Exception $exception) {
      // return view('adminsError');
   //}
   
   return view('User.chat.chatRoom')->with('rooms',$rooms);
   }

    //----------------------------------------Method-End-Begin------------------------------------//

   public function getChatRoomCollection($docid)
   {
    $chats= collect ();
    $databaseinstance = app('firebase.firestore')->database()->collection('Thread')->document($docid)->collection('chatCollection');
    $documents = $databaseinstance ->orderBy('createAt', 'asc')-> documents();
    //try {
     foreach($documents as $document)
     {
      if ($document->exists())
      {
        $chatRoomsCollections = $document->data();
        //$chatRoomsCollections['message'] = str_replace(" ", "-",$document->data()['message']);
        $chats[] = $chatRoomsCollections;
      }
     }
     return response()->json($chats);
   }

   //----------------------------------------Method-End-Begin------------------------------------//

   public function sendMessage(Request $request,$docid)
   {
    $chatRoomId = explode("-",$docid);
    if(Auth::User()->id == $chatRoomId[0])
    {
        $senderId = $chatRoomId[0];//the person who sends the message
        $clientId = $chatRoomId[1];//the person who receives the message
    }elseif (Auth::User()->id == $chatRoomId[1])
    {
        $senderId = $chatRoomId[1];//the person who sends the message
        $clientId = $chatRoomId[0];//the person who receives the message
    }
   // $clientId = (int)substr($docid, 0 ,-2);//the person who receives the message
    $createAt = time()*1000;
   // $senderId = (int)substr($docid,strpos($docid,"-")+1,5);//the person who sends the message
    $databaseinstance = app('firebase.firestore')->database()->collection('Thread')->document($docid);
    $databaseinstance ->update([
        ['path' => 'lastMessage','value' => $request->messagebody], // update las meassage
        ]);
    $databaseChatInstance = $databaseinstance->collection('chatCollection')->newDocument();
    $data = [
        'clientId' => $senderId, // add the sent meassage to firebase
        'createAt'=>$createAt,
        'image' => '',
        'last' => true,
        'message' => $request->messagebody,
        'receiverId' => $clientId,
        'seen' => false,
    ];

    $documentsCollections = $databaseinstance ->collection('chatCollection')->where('last', '=', true)->documents();
    foreach($documentsCollections as $chatCollection)
    {
        $documentsCollectionsSetter = $databaseinstance->collection('chatCollection')->document($chatCollection->id())->update([
            ['path' => 'last','value' => false], // set message seen true
            ]);
    }

    $databaseCountCollectionInstanceOfSender = $databaseinstance->collection('unreadCountCollection')->document($senderId);
    $databaseCountCollectionInstanceOfSender ->update([
        ['path' => 'unreadCount','value' => 0], // set the un read counter
        ]);

    $usersDatabaseInstance = app('firebase.firestore')->database()->collection('Users')->document($clientId)->update([
        ['path' => 'unReadMessagesCounter','value' => FieldValue::increment(1)], // decrement un read counter
        ]);

    $databaseCountCollectionInstanceOfClient = $databaseinstance->collection('unreadCountCollection')->document($clientId);
    $databaseCountCollectionInstanceOfClient ->update([
        ['path' => 'unreadCount','value' => FieldValue::increment(1)], // set the un read counter
        ]);

    if($databaseChatInstance ->set($data))
    {
        return response()->json(['code'=>0]);
    }else{
        return response()->json(['code'=>200]);
    }
    //return redirect()->route('chatroom');
    
   }
//----------------------------------------Method-End-Begin----------------------------------------------------------//

   public function setAllMessagesRead($docid)
   {
    $oldunReadMessagesCounter = 0;

    $databaseinstance = app('firebase.firestore')->database()->collection('Thread');
    
    $documentsUnReadCollections = $databaseinstance ->document($docid)->collection('unreadCountCollection')->document(Auth::User()->id);

    $documentsUnReadCollectionsSnapShot = $documentsUnReadCollections -> snapshot();

    $oldunReadMessagesCounter = $documentsUnReadCollectionsSnapShot->data()['unreadCount'];

    $usersDatabaseInstance = app('firebase.firestore')->database()->collection('Users')->document(Auth::User()->id)->update([
        ['path' => 'unReadMessagesCounter','value' => FieldValue::increment(-$oldunReadMessagesCounter)], // decrement un read counter
        ]);

    $documentsUnReadCollections->update([
        ['path' => 'unreadCount','value' => 0], // set the un read counter
        ]);

    $documentsCollections = $databaseinstance ->document($docid)->collection('chatCollection')->where('seen', '=', false)->documents();
    foreach($documentsCollections as $chatCollection)
    {
        $documentsCollectionsSetter = $databaseinstance ->document($docid)->collection('chatCollection')->document($chatCollection->id())->update([
            ['path' => 'seen','value' => true], // set message seen true
            ]);
    }
    return response()->json(['code'=>0]);
   }

//----------------------------------------Method-End-Begin-------------------------------------------------------------------//

   public function setUnReadCounter($docid)
   {
    $databaseinstance = app('firebase.firestore')->database()->collection('Thread');
    $documentsUnReadCollections = $databaseinstance ->document($docid)->collection('unreadCountCollection')->document(Auth::User()->id)->update([
        ['path' => 'unreadCount','value' => 0], // set the un read counter
        ]);
   }
   
//----------------------------------------Method-End-Begin-------------------------------------------------------------------//

   public function setMessagesRead($docid,$messageId)
   {
    $databaseinstance = app('firebase.firestore')->database()->collection('Thread');
    $documentsCollections = $databaseinstance ->document($docid)->collection('chatCollection')->document($messageId);
    $document = $documentsCollections -> snapshot();
    if(Auth::User()->id == $document->data()['receiverId'])
     {
        $this->setAllMessagesRead($docid);
        $this->setUnReadCounter($docid);
        $documentsCollections ->update([
                ['path' => 'seen','value' => true], // set message seen true
                ]);
        return response()->json(['code'=>0]);
     }
   }

//----------------------------------------Method-End-Begin-------------------------------------------------------------------//
}
