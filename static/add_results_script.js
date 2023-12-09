document.querySelectorAll("input[name='selected-categories[]']").forEach(elem => {
    elem.addEventListener("change", change_check);
});

document.querySelectorAll("input[data=NP-check]").forEach(elem => {
    elem.addEventListener("change", change_NP);
});

document.querySelectorAll("button").forEach(elem => {
    elem.addEventListener("click", add_result_row);
});

document.querySelectorAll("input[type=text]").forEach(elem => {
    elem.addEventListener("keyup", change_dataset)
});

function change_check(){
    let category_list = document.getElementById(`cat-${this.id}`);
    if(this.checked){
        category_list.parentElement.classList.remove("category-list-hid");
        category_list.parentElement.classList.add("category-list");
        category_list.querySelectorAll(`input[name='results-name-${this.id}[]']`).forEach(elem => {
            elem.setAttribute("required", "");
        });
        category_list.querySelectorAll(`input[name='results-time1-${this.id}[]']`).forEach(elem => {
            elem.setAttribute("required", "");
        });
    }
    else{
        category_list.parentElement.classList.add("category-list-hid");
        category_list.parentElement.classList.remove("category-list");
        category_list.querySelectorAll(`input[name='results-name-${this.id}[]']`).forEach(elem => {
            elem.removeAttribute("required");
        });
        category_list.querySelectorAll(`input[name='results-time1-${this.id}[]']`).forEach(elem => {
            elem.removeAttribute("required");
        });
    }
}

function change_NP(){
    if(this.checked){
        document.getElementById(`helper-${this.id}`).value = "true";
        document.getElementById(`time-${this.id}`).removeAttribute("required");
    }
    else{
        document.getElementById(`helper-${this.id}`).value = "false";
        document.getElementById(`time-${this.id}`).setAttribute("required", "");
    }
}

function add_result_row(){
    let data_id = parseInt(this.getAttribute("data-last-id"))+1;
    this.setAttribute("data-last-id", data_id);
    let cat_id = this.getAttribute("data");

    let node = document.getElementById("item-template-id").cloneNode(true);
    node.classList.remove("disnone");
    change_attribute(node, "id", cat_id, data_id);

    change_attribute(node.querySelector("input[name=results-name-template]"), "name", cat_id);
    change_attribute(node.querySelector("input[name=results-time1-template]"), "name", cat_id);
    change_attribute(node.querySelector("input[name=results-time2-template]"), "name", cat_id);
    change_attribute(node.querySelector("input[name=results-np-template]"), "name", cat_id);
    
    change_attribute(node.querySelector("input[id=time-np-template-id]"), "id", cat_id, data_id);
    change_attribute(node.querySelector("input[id=np-template-id]"), "id", cat_id, data_id);
    change_attribute(node.querySelector("input[id=helper-np-template-id]"), "id", cat_id, data_id);

    node.querySelector("input[type=text]").setAttribute("data", cat_id);

    change_attribute(node.querySelector("input[type=text]"), "list", cat_id, data_id);
    change_attribute(node.querySelector("input[type=text]").parentNode, "l-target", cat_id, data_id);

    node.querySelector("input[type=text]").addEventListener("keyup", change_dataset);

    node.querySelector("input[data=NP-check]").addEventListener("change", change_NP);
    
    document.getElementById(`cat-${this.getAttribute("data")}`).appendChild(node);
}

function change_dataset(){
    if(this.value != ""){
        if(document.contains(document.getElementById(this.getAttribute("list")))){
            document.querySelectorAll(`datalist[id=${this.getAttribute("list")}]`).forEach(elem => {
                elem.remove();
            });
        }
        fetch(`team_whisper?cat=${this.getAttribute("data")}&name=${this.value}`).then(r => r.text()).then(h => {
            let parser = new DOMParser();
            let datalist_element = parser.parseFromString(h, "text/html").body.childNodes[0];
            console.log(datalist_element);
            datalist_element.id = this.getAttribute("list");
            document.querySelector(`label[l-target=${this.getAttribute("list")}]`).appendChild(datalist_element);
        });
    } 
}

function get_new_attribute(original, cat_id, data_id=null){
    let idd = original.split("-");
    if(data_id){
        if(idd.length > 3){
            return `${idd[0]}-${idd[1]}-${cat_id}-${data_id}`;
        }
        else {
            return `${idd[0]}-${cat_id}-${data_id}`;
        }
    }
    else{
        return `${idd[0]}-${idd[1]}-${cat_id}[]`;
    }
}

function change_attribute(element, attribute, cat_id, data_id=null){
    element.setAttribute(attribute, get_new_attribute(element.getAttribute(attribute), cat_id, data_id))
}
