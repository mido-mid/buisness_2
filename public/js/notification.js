//------------------------------------------Variables--declaration--BEGIN------------------------------------------------//
let docid = document.getElementById("userid").value;
let notificationCtr = 0;
let oldNotificationCtr = document.getElementById("notificationCtr").innerText
let unReadMessagesCounter = 0;
let oldunReadMessagesCounter = document.getElementById("unReadMessagesCounter").innerText

//------------------------------------------Variables--declaration--END-------------------------------------------------//
db.collection("Notification").doc(docid)//listener to notificationCtr
    .onSnapshot((doc) => {
            notificationCtr = doc.data().notificationUnReadCtr;
        document.getElementById("notificationCtr").innerHTML = oldNotificationCtr.replace(oldNotificationCtr,notificationCtr);
        notificationCtr = 0
    });

//-------------------------------------------------------------------------------------------------------------------------//
db.collection("Users").doc(docid)//listener to unReadMessagesCounter
    .onSnapshot((doc) => {
        unReadMessagesCounter = doc.data().unReadMessagesCounter;
        document.getElementById("unReadMessagesCounter").innerHTML = oldunReadMessagesCounter.replace(oldunReadMessagesCounter,unReadMessagesCounter);
        unReadMessagesCounter = 0
    });