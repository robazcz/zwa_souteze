document.querySelector("form").addEventListener("submit", submit_form);

document.querySelectorAll("input[required]").forEach(elem => {
    elem.addEventListener("focusout", unfocus_input);
});

function unfocus_input(){
    let err_elem = document.getElementById(`${this.id}_error`);

    if(this.value == ""){
        err_elem.innerHTML = "Pole musí být vyplněno";
        this.classList.add("error");
    }
    else{
        err_elem.innerHTML = "";
        this.classList.remove("error");

        if(this.id == "register_password_2"){
            if(document.getElementById("register_password").value != this.value){
                err_elem.innerHTML = "Hesla se neshodují";
                this.classList.add("error");
            }
        }
        else if(this.id == "register_username"){
            fetch("check", {method: "POST", headers:{"Content-Type": "application/x-www-form-urlencoded"}, body: `username=${this.value}`})
            .then(res => res.json())
            .then(data => {
                if(data.exist == "true"){
                    err_elem.innerHTML = "Jméno již existuje";
                    this.classList.add("error");
                }
            });
        }
    }
}

function submit_form(event){
    document.querySelectorAll("input[required]").forEach(elem => {
        elem.dispatchEvent(new Event("focusout"));
        if(document.getElementById(`${elem.id}_error`).textContent != ""){ //tady to nefunguje, když je to takhle v loopu a checkuje to hodnotu, která se přidává asynchroně i když tam už dávno je
            event.preventDefault();
        }
    });
}
