importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyDTRo5vhomZQPaeVCd9SzrULh7Hyxyzm-k",
    authDomain: "businesschatting-13411.firebaseapp.com",
    projectId: "businesschatting-13411",
    storageBucket: "businesschatting-13411.appspot.com",
    messagingSenderId: "447727332307",
    appId: "1:447727332307:web:20b3f63b74d79eb4c6dd26",
    measurementId: "G-2JX8Q4KHK9"
  });


const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
  console.log(
    "[firebase-messaging-sw.js] Received background message ",
    payload,
  );
  /* Customize notification here */
  const notificationTitle = "Background Message Title";
  const notificationOptions = {
    body: "Background Message body.",
    icon: "/itwonders-web-logo.png",
    click_action:"chatroom",
  };

  return self.registration.showNotification(
    notificationTitle,
    notificationOptions,
  );
});