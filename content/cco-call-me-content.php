<?php

class cco_call_me_content
{

    private $options;

    public function __construct()
    {
        $this->options = get_option('cco');

    }

    public function show()
    {
        add_action('wp_head', array($this, 'cco_stylesheet_url'));
        add_action('admin_head', array($this, 'cco_stylesheet_url'));
        add_action('wp_footer', array($this, 'cco_code'));
        add_action('the_content', array($this, 'show_cco_html'));
    }
    public function cco_stylesheet_url()
    {
        echo '<link rel="stylesheet" href="' . plugin_dir_url(__DIR__) . 'css/style.css?build=' . date("Ymd", strtotime('-24 days')) . '" type="text/css" media="screen" />';
    }

    protected function prepare_form()
    {
        $form = '<form id="cco-form" name="cco-form">';
        $fields = $this->options['fields'];
        $mapper = json_decode($this->options['names_mapper'], true);

        if ($fields && $mapper) {
            $fields = explode("\n", $fields);
            foreach ($fields as $field) {
                if ($field) {

                    $isPhone = substr(trim($field), 0, 4) == "ph__" ? "true" : "false";

                    $isMandatory = substr(trim($field), -1) == "*" ? "*" : "";
                    $form .= '<label for="' . $field . '" class="cco-label">' . (array_key_exists(trim(str_replace('*', '', $field)), $mapper) ? $mapper[trim(str_replace('*', '', $field))] . $isMandatory : trim($field) . $isMandatory) . '</label>
                    <input class="cco-field" oninput="return validateField(this)" type="text" id="' . $field . '" name="' . $field . '"  ' . ($isMandatory == "*" ? "required=\"true\"" : "") . ' ' . ($isPhone == "true" ? "maxlength=11 min=100000000 max=99999999999 pattern=\"\\\d+\"" : "") . ' />';
                }
            }
        }
        $form .= '</form>';

        return str_replace("\n", "", str_replace("\r", "", $form));
    }

    public function show_cco_html()
    {
        echo '  <div id="cco-mask" class="cco-mask" style="display:none;"></div>
                <div id="cco-modal" class="cco-modal" style="display:none;">
                    <div class="cco-title" style="background-color:' . $this->options['color'] . ';">
                        ' . ($this->options['title'] ? $this->options['title'] : CCO_DEFAULT_TITLE) . '
                        <button id="cco-button-cancel-x" class="cco-modal-button-x">X</button>
                    </div>
                    <div id="cco-message">

                    </div>
                </div>
                <button id="cco-button-open" class="cco-button" style="' .
            ($this->options['button_position_x'] === 'center' ? 'left' : ($this->options['button_position_x'] ? $this->options['button_position_x'] : 'right'))
            .
            ($this->options['button_position_x'] === 'center' ? ':50%;' : ':30px;')
            .
            ($this->options['button_position_y'] === 'center' ? 'top' : ($this->options['button_position_y'] ? $this->options['button_position_y'] : 'bottom'))
            .
            ($this->options['button_position_y'] === 'center' ? ':50%;' : ':30px;')
            . 'background-image: url('. CCO_CONFIG_BUTTON_IMAGE.');' .'"></button>
             ';
    }

    public function cco_code()
    {
        $getAllFieldsFromApi = new cco_api($this->options);
        $token = $getAllFieldsFromApi->getToken();
        ?>
			<script>

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

             function startMessage(){
                 document.getElementById("cco-message").innerHTML='<div class="cco-body">'
                            +'<div class="cco-text-before"><?php echo ($this->options['text_before'] ? $this->options['text_before'] : CCO_DEFAULT_TEXT_BEFORE); ?></div>'
                            +'<div><?php echo $this->prepare_form(); ?></div>'
                            +'<div class="cco-text-after"><?php echo ($this->options['text_after'] ? $this->options['text_after'] : CCO_DEFAULT_TEXT_AFTER); ?></div>'
                        +'</div>'
                        +'<div class="cco-container-buttons">'
                            +'<div class="cco-buttons">'
                            +'<button id="cco-button-cancel" class="cco-modal-button cco-modal-button-secondary" style="border-color:<?php echo $this->options['color']; ?>;"><?php echo ($this->options['button_cancel'] ? $this->options['button_cancel'] : CCO_DEFAULT_BUTTON_CANCEL); ?></button>'
                            +'<button id="cco-button-send" class="cco-modal-button cco-modal-button-primary" style="border-color:<?php echo $this->options['color']; ?> !important;background-color:<?php echo $this->options['color']; ?> !important;"><?php echo ($this->options['button_send'] ? $this->options['button_send'] : CCO_DEFAULT_BUTTON_SEND); ?></button>'
                            +'</div>'
                            +'<div class="cco-container-powered-by">'
                            +("<?php echo $this->options['consent']; ?>" == "on" ? "<a href=\"https://callcenteronline.pl\" target=\"_blank\" class=\"cco-powered-by\"><div class=\"cco-powered-by-text\">Połączy nas</div><img class=\"cco-powered-by-image\" src=\"<?php echo plugin_dir_url(__DIR__) . 'image/logo-mini.png'; ?>\"/></a>" : "")
                            +'</div>'
                        '</div>';
             }

             function endMessage(isOK=true){
                 document.getElementById("cco-message").innerHTML='<div class="cco-body cco-body-end">'
                            +(isOK==true?'<?php echo $this->options["text_success"]; ?>':'<?php echo $this->options["text_failed"]; ?>')
                            +'</div>'
                            +'<div class="cco-container-buttons">'
                                +'<div class="cco-buttons cco-buttons-end">'
                                    +'<button id="cco-button-end" onclick="ccoOnClick(\'none\')" class="cco-modal-button cco-modal-button-secondary" style="border-color:<?php echo $this->options['color']; ?> !important;"><?php echo ($this->options['button_close'] ? $this->options['button_close'] : CCO_DEFAULT_BUTTON_CLOSE); ?></button>'
                                +'</div>'
                                +'<div class="cco-container-powered-by">'
                                    +("<?php echo $this->options['consent']; ?>" == "on" ? "<a href=\"https://callcenteronline.pl\" target=\"_blank\" class=\"cco-powered-by\"><div class=\"cco-powered-by-text\">Połączy nas</div><img class=\"cco-powered-by-image\" src=\"<?php echo plugin_dir_url(__DIR__) . 'image/logo-mini.png'; ?>\"/></a>" : "")
                                +'</div>'
                            '</div>';
             }

             function addEvents(){
                document.getElementById('cco-button-send').addEventListener('click',function(){
                    //console.debug(document.forms['cco-form'].elements);
                    document.forms['cco-form'].submit();

                });

                document.getElementById('cco-button-cancel').addEventListener('click',function(){
                    ccoOnClick("none")
                });

                document.forms['cco-form'].submit=function (){
                    sendData(this.elements)
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

             function sendData(elements){

                if(validateForm(elements)){



                var businessParameters={};

                for (var i = 0, element; element = elements[i++];) {
                    businessParameters[element.name.replace('*','')]=element.value;
                }

                var data = {
                    'campaignId': '<?php echo $this->options["campaign"]; ?>',
                    'priority': '100',
                    'statusId':'<?php echo $this->options["contact_status"]?$this->options["contact_status"]:CCO_DEFAULT_CONTACT_STATUS; ?>',
                    'businessParams': JSON.stringify(businessParameters)
                };

                var xmlhttp = new XMLHttpRequest();



                xmlhttp.onreadystatechange = function() {

                    if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
                        if (xmlhttp.status == 200 || xmlhttp.status == 201) {
                            //document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
                            endMessage(true)

                        }
                        else {
                            endMessage(false)
                        }
                    }
                };

                let uri = '<?php echo $this->options['endpoint']; ?>/api/contact/new?access_token=<?php echo $token; ?>';
                xmlhttp.open("POST", uri, true);
                //xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xmlhttp.send(JSON.stringify(data));
             }
            }

             document.getElementById('cco-button-open').addEventListener('click',function(){
                if(document.getElementById('cco-mask').style.display=="block"){
                    ccoOnClick("none")
                }
                else{
                    startMessage();
                    addEvents();
                    ccoOnClick("block")

                }
             })


             document.getElementById('cco-button-cancel-x').addEventListener('click',function(){
                ccoOnClick("none")
             });

             document.getElementById('cco-mask').addEventListener('click',function(){
                ccoOnClick("none")
             });

             document.addEventListener("keydown", ({key}) => {
                if (key=="Escape" && document.getElementById('cco-mask').style.display=="block"){
                    ccoOnClick("none")
                }
             })

			</script>
		<?php

    }
}
