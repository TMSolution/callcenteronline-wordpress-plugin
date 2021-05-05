function validateField(element){

    var valid=true;
    if(element.required && !element.value.length){
            valid = false;
        }

        if(element.min && element.value && (element.value*1) < (element.min*1)){
            valid = false;
        }

        if(element.max && element.value && (element.value*1) > (element.max*1)){
            valid = false;
        }
        if(valid){
            element.setAttribute('style', 'border-color:rgb(128, 128, 128) !important');
        }
        else{
            element.setAttribute('style', 'border-color:#ff0000 !important');
        }

        return valid;

 }

 function ccoOnClick(type){
    document.getElementById('cco-mask').style.display = type;
    document.getElementById('cco-modal').style.display = type;
 }

 function addEvents(text_success, text_failed, consent, color, button_close, imageDir, campaign, contact_status, token, endpoint){
    document.getElementById('cco-button-send').addEventListener('click',function(){
        document.forms['cco-form'].submit();
    });

    document.getElementById('cco-button-cancel').addEventListener('click',function(){
        ccoOnClick("none")
    });

    document.forms['cco-form'].submit=function (){
        sendData(this.elements, text_success, text_failed, consent, color, button_close, imageDir, campaign, contact_status, token, endpoint)
        return false;
    }
 }

 function validateForm(elements){

    var valid=true;

    for (var i = 0, element; element = elements[i++];) {
        if (!validateField(element)){
            valid=false;
        }
    }

    return valid;
 }

 function startMessage(text_before,text_after, form, color, button_cancel, button_send,consent, imageDir){
    document.getElementById("cco-message").innerHTML='<div class="cco-body">'
               +'<div class="cco-text-before">'+text_before+'</div>'
               +'<div>'+form+'</div>'
               +'<div class="cco-text-after">'+text_before+'</div>'
           +'</div>'
           +'<div class="cco-container-buttons">'
               +'<div class="cco-buttons">'
               +'<button id="cco-button-cancel" class="cco-modal-button cco-modal-button-secondary" style="border-color:'+color+';">'+button_cancel+'</button>'
               +'<button id="cco-button-send" class="cco-modal-button cco-modal-button-primary" style="border-color:'+color+' !important;background-color:'+color+' !important;">'+button_send+'</button>'
               +'</div>'
               +'<div class="cco-container-powered-by">'
               +(consent == "on" ? "<a href=\"https://callcenteronline.pl\" target=\"_blank\" class=\"cco-powered-by\"><div class=\"cco-powered-by-text\">Połączy nas</div><img class=\"cco-powered-by-image\" src=\""+imageDir+"\"/></a>" : "")
               +'</div>'
           '</div>';
}

function endMessage(isOK=true, text_success, text_failed, consent, color, button_close, imageDir){
    document.getElementById("cco-message").innerHTML='<div class="cco-body cco-body-end">'
               +(isOK==true?text_success:text_failed)
               +'</div>'
               +'<div class="cco-container-buttons">'
                   +'<div class="cco-buttons cco-buttons-end">'
                       +'<button id="cco-button-end" onclick="ccoOnClick(\'none\')" class="cco-modal-button cco-modal-button-secondary" style="border-color:'+color+' !important;">'+button_close+'</button>'
                   +'</div>'
                   +'<div class="cco-container-powered-by">'
                       +(consent == "on" ? "<a href=\"https://callcenteronline.pl\" target=\"_blank\" class=\"cco-powered-by\"><div class=\"cco-powered-by-text\">Połączy nas</div><img class=\"cco-powered-by-image\" src=\""+imageDir+"\"/></a>" : "")
                   +'</div>'
               '</div>';
}

function sendData(elements, text_success, text_failed, consent, color, button_close, imageDir, campaign, contact_status, token, endpoint, priority){

    if(validateForm(elements)){

    var businessParameters={};

    for (var i = 0, element; element = elements[i++];) {
        businessParameters[element.name.replace('*','')]=element.value;
    }

    var data = {
        'campaignId': campaign,
        'priority': priority,
        'statusId': contact_status,
        'businessParams': JSON.stringify(businessParameters)
    };

    var xmlhttp = new XMLHttpRequest();



    xmlhttp.onreadystatechange = function() {

        if (xmlhttp.readyState == XMLHttpRequest.DONE) { 
            if (xmlhttp.status == 200 || xmlhttp.status == 201) {
                endMessage(true, text_success, text_failed, consent, color, button_close, imageDir)
            }
            else {
                endMessage(false, text_success, text_failed, consent, color, button_close, imageDir)
            }
        }
    };

    let uri = endpoint+'/api/contact/new?access_token='+token;
    xmlhttp.open("POST", uri, true);
    xmlhttp.send(JSON.stringify(data));
 }
}
