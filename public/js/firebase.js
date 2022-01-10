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
function printSentMessage(room)
{
    let newMessageId = '';
    let oldMessageId = '';
    room['lastMessage'] = room['lastMessage'].replace(/-/g, " ");
    let docid = document.getElementById("docid").value;
    let oldlastmessage = document.getElementById("lastmessage").innerText;
        db.collection("Thread").doc(docid)
        .onSnapshot((docs) => {
                room['lastMessage'] = docs.data().lastMessage ;
                room['lastMessage'] = room['lastMessage'].replace(/-/g, " ");
                document.getElementById("lastmessage").innerHTML = oldlastmessage.replace(oldlastmessage,room['lastMessage']);
        });

        db.collection("Thread").doc(docid).collection("chatCollection").orderBy("createAt", "desc").limit(1)
        .onSnapshot((docs) => {
            docs.forEach((doc) => {
                let chat = doc.data();
                newMessageId = doc.id ;
                setMessagesRead(docid,doc.id);
                //console.log(check);
                if(newMessageId != oldMessageId){
                    oldMessageId = newMessageId ;
                    chat['createAt']= formatMessageTime(chat['createAt']);
                    chat['message'] =chat['message'].replace(/-/g, " ");
                    if(chat['receiverId'] == room['senderId'])
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
messaging.onMessage(function(payload) {
    const noteTitle = payload.notification.title;
    const noteOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon,
        click_action: payload.notification.click_action,
    };
    new Notification(noteTitle, noteOptions);
});

