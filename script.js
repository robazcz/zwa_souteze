if(window.location.pathname == "/login.php"){
    var error_elements = {
        login_username: "login_username_error",
        login_password: "login_password_error"
    }

    for(let key in error_elements){
        document.getElementById(key).addEventListener("focusout", unfocus_input);
    }

    document.getElementById("login_form").addEventListener("submit", submit_form);
}
else if(window.location.pathname == "/register.php"){
    var error_elements = {
        register_username: "register_username_error",
        register_password: "register_password_error",
        register_password_2: "register_password_2_error"
    }

    for(let key in error_elements){
        document.getElementById(key).addEventListener("focusout", unfocus_input);
    }
    document.getElementById("register_form").addEventListener("submit", submit_form);
}

function unfocus_input(){
    if(this.value == ""){
        document.getElementById(error_elements[this.id]).innerHTML = "Pole musí být vyplněno";
        this.classList.add("error");
    }
    else{
        document.getElementById(error_elements[this.id]).innerHTML = "";
        this.classList.remove("error");
        if(this.id == "register_password_2"){
            if(document.getElementById("register_password").value != this.value){
                document.getElementById("register_password_2_error").innerHTML = "Hesla se neshodují";
                this.classList.add("error");
            }
        }
        else if(this.id == "register_username"){
            fetch("/check.php", {method: "POST", headers:{"Content-Type": "application/x-www-form-urlencoded"}, body: `username=${this.value}`}).then(res => res.json()).then(data => {
                console.log(data)
                if(data.exist == "true"){
                    document.getElementById(error_elements[this.id]).innerHTML = "Jméno již existuje";
                    this.classList.add("error");
                }
            });
        }
    }
}

function submit_form(event){
    for(let key in error_elements){
        document.getElementById(key).dispatchEvent(new Event("focusout"));
        if(document.getElementById(error_elements[key]).textContent != ""){
            event.preventDefault();
            }
        }
}