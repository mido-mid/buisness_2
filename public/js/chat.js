function printChat(room,chats) {
    if(document.querySelector(".chat-history"))
    {
        console.log("in if");
        document.getElementById("unique-chat").removeChild(document.querySelector(".chat-header"));
        document.getElementById("unique-chat").removeChild(document.querySelector(".chat-history"));
        document.getElementById("unique-form").removeChild(document.querySelector(".chat-message"));
        let div = document.createElement("div");
        div.classList.add("chat-header");
        div.classList.add("clearfix");
        let chat = `<div class="row">
                            <div class="col-lg-6">
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                    <img src=${room['senderImage']} alt="avatar">
                                </a>
                                <div class="chat-about">
                                    <h6 class="m-b-0">${room['senderName']}</h6>
                                    <input type="hidden" id="docid" name="docid" value="${room['docId']}">
                                    <input type="hidden" id="senderId" name="senderId" value="${room['senderId']}">
                                </div>
                            </div>
                        </div>`;
        div.innerHTML=chat;
        let divChatHistory = document.createElement("div");
        divChatHistory.classList.add("chat-history");
        let divChatHistoryContent = `<ul id="chatMessages" class="m-b-0">
        <!--Chat--Messages--begin-->
        <!--Chat--Messages--end-->
    </ul>`;
        divChatHistory.innerHTML=divChatHistoryContent;
        let divChatInput = document.createElement("div");
        divChatInput.classList.add("chat-message");
        divChatInput.classList.add("clearfix");
        let divChatContent = `<div class="input-group mb-0">
        <div class="input-group-prepend rounded-circle">
            <span class="input-group-text rounded-circle">
            <button id="sendbtn" type="submit" class="fa fa-send"></button>
            </span>
        </div>
        <input type="text" name="messagebody" id="messageInput" class="messageInput form-control-custom" placeholder="type your message...">
        <i class="emojiIcon fa fa-smile-o input-group-text" style="margin-top:10px; background-color: white !important; border-color: white !important; cursor:pointer;" aria-hidden="true"></i>
    </div>`;
    divChatInput.innerHTML=divChatContent;
        document.getElementById("unique-chat").insertBefore(divChatHistory,document.getElementById("unique-form"));
        document.getElementById("unique-chat").insertAdjacentElement("afterbegin",div);
        document.getElementById("unique-form").appendChild(divChatInput);
        for(let chat of chats)
    {
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
    }else{
        console.log("in else");
        let div = document.createElement("div");
        div.classList.add("chat-header");
        div.classList.add("clearfix");
        let chat = `<div class="row">
                            <div class="col-lg-6">
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                    <img src=${room['senderImage']} alt="avatar">
                                </a>
                                <div class="chat-about">
                                    <h6 class="m-b-0">${room['senderName']}</h6>
                                    <input type="hidden" id="docid" name="docid" value="${room['docId']}">
                                    <input type="hidden" id="senderId" name="senderId" value="${room['senderId']}">
                                </div>
                            </div>
                        </div>`;
        div.innerHTML=chat;
        let divChatHistory = document.createElement("div");
        divChatHistory.classList.add("chat-history");
        let divChatHistoryContent = `<ul id="chatMessages" class="m-b-0">
        <!--Chat--Messages--begin-->
        <!--Chat--Messages--end-->
    </ul>`;
        divChatHistory.innerHTML=divChatHistoryContent;
        let divChatInput = document.createElement("div");
        divChatInput.classList.add("chat-message");
        divChatInput.classList.add("clearfix");
        let divChatContent = `<div class="input-group mb-0">
        <div class="input-group-prepend rounded-circle">
            <span class="input-group-text rounded-circle">
            <button id="sendbtn" type="submit" class="fa fa-send"></button>
            </span>
        </div>
        <input type="text" name="messagebody" id="messageInput" class="messageInput form-control-custom" placeholder="type your message...">
        <i class="emojiIcon fa fa-smile-o input-group-text" style="margin-top:10px; background-color: white !important; border-color: white !important; cursor:pointer;" aria-hidden="true"></i>
    </div>`;
    divChatInput.innerHTML=divChatContent;
        document.getElementById("unique-chat").insertBefore(divChatHistory,document.getElementById("unique-form"));
        document.getElementById("unique-chat").insertAdjacentElement("afterbegin",div);
        document.getElementById("unique-form").appendChild(divChatInput);
        for(let chat of chats)
    {
        if(chat['message'] != room['lastMessage'])
        {
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
    }
    }
}




let form = document.getElementById("unique-form");

form.addEventListener("submit",function(e){
    let docid = document.getElementById("docid").value;
    e.preventDefault();
    $.ajax({
     url:`sendMessage/${docid}`,
     method:'post',
     data:new FormData(form),
     processData:false,
     dataType:'json',
     contentType:false,
     beforeSend:function(){
     },
     success:function(data){
          if(data.code == 0){
        $(form)[0].reset();
          }else{
              alert("error");
          }
     }
 });
});

function searchChats() {
    // Declare variables
    let input, filter, ul, li, a, i, txtValue;
    input = document.getElementById('myInput');
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName('li');
  
    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
      a = li[i].getElementsByTagName("a")[0];
      txtValue = a.textContent || a.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        li[i].style.display = "";
      } else {
        li[i].style.display = "none";
      }
    }
  }


function formatMessageTime(timestamp){
    let result =0;
    //----------------------------Today---Date-----------------------------//
    let today = new Date();
    let currentDay = today.getDay();
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth();
    let currentDateDay = today.getDate();
    //----------------------------Parsed---Date-----------------------------//
    let date = new Date(timestamp);
    let dateDay = date.getDate();
    let day = date.getDay();
    let year = date.getFullYear();
    let month = date.getMonth();
    let hours = date.getHours();
    let minutes = "0"+ date.getMinutes();
    let amPm = 0;
    if(hours > 12){
        hours = hours-12;
        amPm = "PM";
    }else if (hours === 12){
        amPm = "PM";
    }else if (hours === 0){
        hours = 12;
        amPm = "AM";
    }else{
        hours = hours;
        amPm = "AM";
    }
    if(year < currentYear){
        month = setMonthName(month);
       result = year + ':' + month + ':'+dateDay + ':' + hours + ':' + minutes.substr(-2) +` ${amPm}`;
    }else if(month < currentMonth){
        month = setMonthName(month);
        result = month + ':'+dateDay+':' + hours + ':' + minutes.substr(-2) +` ${amPm}`;
     }else if (month == currentMonth){
         if((currentDateDay - dateDay) >= 7){
             month = setMonthName(month);
            result = month + ':'+dateDay+':' + hours + ':' + minutes.substr(-2) +` ${amPm}`;
         }else if ((currentDay - day) == 1){
            result = 'Yesterday'+':'+hours + ':' + minutes.substr(-2) +` ${amPm}`;
         }else if (day == currentDay){
            result = hours + ':' + minutes.substr(-2) +` ${amPm}`;
         }else {
            day = setDayName(day);
            result = day+':' + hours + ':' + minutes.substr(-2) +` ${amPm}`;
         }
     }
    return result ;
}

function setDayName(day){
    switch(day) {
        case 0:
            day = "Sun";
            break;
          case 1:
            day = "Mon";
            break;
          case 2:
             day = "Tue";
            break;
          case 3:
            day = "Wed";
            break;
          case 4:
            day = "Thu";
            break;
          case 5:
            day = "Fri";
            break;
          case 6:
            day = "Sat"

    }
    return day ;
}

function setMonthName(month){
    switch(month) {
        case 0:
            month = "Jan";
            break;
        case 1:
            month = "Feb";
            break;
        case 2:
             month = "Mar";
            break;
        case 3:
            month = "Apr";
            break;
        case 4:
            month = "May";
            break;
        case 5:
            month = "Jun";
            break;
        case 6:
            month = "Jul"
            break;
        case 7:
            month = "Aug"
            break;
        case 8:
            month = "Sep"
            break;
        case 9:
            month = "Oct"
            break;
        case 10:
            month = "Nov"
            break;
        case 11:
            month = "Dec"
         
    }
    return month ;
}

