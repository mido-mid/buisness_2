document.body.onload = function () {
    const notificationElements =  document.getElementsByClassName("notificationCreateAt")
    for(let notificationElement of notificationElements){
       notificationElement.innerHTML = formatMessageTime(notificationElement.innerHTML);
      // console.log(notificationElement.innerText);
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
    let date = new Date(parseInt(timestamp, 10));
    let dateDay = date.getDate();
    let day = date.getDay();
    let year = date.getFullYear();
    let month = date.getMonth();
    let hours = date.getHours();
    let minutes = "0"+ date.getMinutes();
    let amPm = 0;
    console.log(date);
        console.log(today);
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
