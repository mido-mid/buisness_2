
        const queries =  document.querySelectorAll(`[id="senderId"]`);
        for(let query of queries)
        {
            let docid = query.value;
            let status;
            db.collection("Users").doc(docid)
             .onSnapshot((docs) => {
                 console.log(docs.data());
                     if(docs.data().isOnline)
                     {
                         status =  document.getElementById(`status${docid}`);
                         status.classList.remove("offline");
                         status.classList.add("online");
                     }else{
                         status =  document.getElementById(`status${docid}`);
                         status.classList.add("offline");
                         status.classList.remove("online");
                     }
             });
        }