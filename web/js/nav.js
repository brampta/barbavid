function toggle_mobinav(){
    //toggle_nav_element('nav');
    var nav = document.getElementById('nav');
    if(nav.classList.contains("mobile_nav_show")){
        nav.classList.remove("mobile_nav_show");
    }else{
        nav.classList.add("mobile_nav_show");
    }
    console.log(nav.classList);
}
function mobinav_openchild(child_id){
    var nav_children = document.getElementsByClassName('nav_child');
    console.log(nav_children);
    //for(x in nav_children){
    for(var x = 0, length = nav_children.length; x < length; x++) {
        console.log(nav_children[x].id);
        if(nav_children[x].id != child_id){
            nav_children[x].style.display = "none";
        }
    }
    toggle_nav_element(child_id);
}
function toggle_nav_element(element_id){
    var nav = document.getElementById(element_id);
    if(nav.style.display!='block'){
        nav.style.display='block';
    }else{
        nav.style.display='none';
    }
}