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
//TODO: prevent default form submit if errors

function unfocus_input(){
    if(this.value == ""){
        error_element = ""
        switch(this.id){
            case "login_username":
                error_element = "login_username_error";
                break
            case "login_password":
                error_element = "login_password_error";
                break
            case "register_username":
                error_element = "register_username_error";
                break
            case "register_password":
                error_element = "register_password_error";
                break
        }
        document.getElementById(error_element).innerHTML = "Pole musí být vyplněno";
    }
    else{
        document.getElementById(error_elements[this.id]).innerHTML = "";
        if(this.id == "register_password_2"){
            if(document.getElementById("register_password").value != this.value){
                document.getElementById("register_password_2_error").innerHTML = "Hodnoty se neshodují"
            }
        }
    }
}

function submit_form(event){
    for(let key in error_elements){
        if(element = document.getElementById(key).value != ""){
            console.log(document.getElementById(error_elements[key]).text);
            event.preventDefault()
        }
    }
}