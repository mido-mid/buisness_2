//---------------------------------Global--Variables--------------------------------------------------------------------------------//
firebase.initializeApp({
    apiKey: "AIzaSyDTRo5vhomZQPaeVCd9SzrULh7Hyxyzm-k",
    authDomain: "businesschatting-13411.firebaseapp.com",
    projectId: "businesschatting-13411",
    storageBucket: "businesschatting-13411.appspot.com",
    messagingSenderId: "447727332307",
    appId: "1:447727332307:web:20b3f63b74d79eb4c6dd26",
    measurementId: "G-2JX8Q4KHK9"
  });
  var db = firebase.firestore();
  const messaging = firebase.messaging();
  var unsubscribe = '';

//---------------------------------Method--Begin------------------------------------------------------------------------------------//
function printSentMessage(room)
{
   if(unsubscribe != '')
   {
    unsubscribe();
   }
    let newMessageId = '';
    let oldMessageId = '';
    room['lastMessage'] = room['lastMessage'].replace(/-/g, " ");
    let docid = document.getElementById("docid").value;
    let oldlastmessage = document.getElementById(`lastmessage${room['senderId']}`).innerText;
//---------------------------------Online--Offline--Listener---------------------------------------------------------------------//
/*
   const queries =  document.querySelectorAll(`id="senderId"`);
   for(let query of queries)
   {
       let docid = query.value;
       let status;
       db.collection("Users").doc(docid)
        .onSnapshot((docs) => {
                if(docs.data().isOnline)
                {
                    status =  document.querySelector(`status${docid}`);
                    status.classList.remove("offline");
                    status.classList.add("online");
                }
        });
   }
*/

//---------------------------------Last--Message--Listener----------------------------------------------------------------------//
        db.collection("Thread").doc(docid)
        .onSnapshot((docs) => {
                room['lastMessage'] = docs.data().lastMessage ;
                room['lastMessage'] = room['lastMessage'].replace(/-/g, " ");
                document.getElementById(`lastmessage${room['senderId']}`).innerText = oldlastmessage.replace(oldlastmessage,room['lastMessage']);
        });
//---------------------------------New--Message--Listener-----------------------------------------------------------------------//
        unsubscribe = db.collection("Thread").doc(docid).collection("chatCollection").orderBy("createAt", "desc").limit(1)
        .onSnapshot((docs) => {
            docs.forEach((doc) =>  {
                let chat = doc.data();
                let roomid = document.getElementById("docid").value;
                let roomOwners = roomid.split("-");
                newMessageId = doc.id ;
                if( ( chat['clientId'] == roomOwners[0] || chat['clientId'] == roomOwners[1] ) && ( chat['receiverId'] == roomOwners[0] || chat['receiverId'] == roomOwners[1] ) ){
                
                setMessagesRead(docid,doc.id);
                if(newMessageId != oldMessageId){
                    oldMessageId = newMessageId ;
                    chat['createAt']= formatMessageTime(chat['createAt']);
                    chat['message'] =chat['message'].replace(/-/g, " ");
                    
                    if(chat['receiverId'] == room['senderId'] && chat['last'] == false)
                    {
                        let message = `<div class="message-data text-right">
                                            <span class="message-data-time">${chat['createAt']}</span>
                                        </div>
                        <div class="message other-message float-right">${chat['message']}</div>`;
                        let li = document.createElement("li");
                        li.classList.add("clearfix");
                        li.innerHTML = message;
                        document.querySelector("#chatMessages").appendChild(li);
                    }else if (chat['clientId'] == room['senderId'])
                    {
                        let message = `<div class="message-data">
                                                <span class="message-data-time">${chat['createAt']}</span>
                                            </div>
                                            <div class="message my-message">${chat['message']}</div> `;
                        let li = document.createElement("li");
                        li.classList.add("clearfix");
                        li.innerHTML = message;
                        document.querySelector("#chatMessages").appendChild(li);
                    }
                }
                //setMessagesRead(docid,doc.id);
                }
            });
        });
}

function setAllMessagesRead(){
    let docid = document.getElementById("docid").value;
    $.ajax({
        url:`setAllMessagesRead/${docid}`,
        method:'post',
        data:'',
        processData:false,
        dataType:'json',
        contentType:false,
        beforeSend:function(){
        },
        success:function(data){
             if(data.code == 0){
             }else{
                 alert("error");
             }
        }
    });
}

function setMessagesRead(docid,messageId){
    $.ajax({
        url:`setMessagesRead/${docid}/${messageId}`,
        method:'post',
        data:'',
        processData:false,
        dataType:'json',
        contentType:false,
        beforeSend:function(){
        },
        success:function(data){
             if(data.code == 0){
             }else{
                 alert("error");
             }
        }
    });
}
//---------------------------------Notification--Message--Handeler----------------------------------------------------------------------//
messaging.onMessage(function(payload) {
    const noteTitle = payload.notification.title;
    const noteOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon,
        click_action: payload.notification.click_action,
    };
    new Notification(noteTitle, noteOptions);
});

