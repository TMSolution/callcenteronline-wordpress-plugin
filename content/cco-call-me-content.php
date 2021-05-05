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
        $ccoStylePath = plugin_dir_url(__FILE__).'../css/style.css';
        wp_enqueue_style('cco-style', $ccoStylePath, array(), '0.1.0', 'all');
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
            . 'background-image: url('. (plugin_dir_url(__FILE__)."../image/logo-390.png").');' .'"></button>
             ';
    }

    public function cco_code()
    {
        $getAllFieldsFromApi = new cco_api($this->options);
        $token = $getAllFieldsFromApi->getToken();

        $textBefore = $this->options['text_before'] ? $this->options['text_before'] : CCO_DEFAULT_TEXT_BEFORE;
        $textAfter = $this->options['text_after'] ? $this->options['text_after'] : CCO_DEFAULT_TEXT_AFTER;
        $buttonCancel = $this->options['button_cancel'] ? $this->options['button_cancel'] : CCO_DEFAULT_BUTTON_CANCEL;
        $buttonSend = $this->options['button_send'] ? $this->options['button_send'] : CCO_DEFAULT_BUTTON_SEND;
        $buttonClose = $this->options['button_close'] ? $this->options['button_close'] : CCO_DEFAULT_BUTTON_CLOSE;
        $contactStatus = $this->options["contact_status"]?$this->options["contact_status"]:CCO_DEFAULT_CONTACT_STATUS;
        $consent = $this->options['consent'];
        $form = $this->prepare_form();
        $color = $this->options['color'];
        $imageDir = plugin_dir_url(__DIR__) . 'image/logo-mini.png';
        $textSuccess = $this->options["text_success"];
        $textFailed = $this->options["text_failed"];
        $campaign = $this->options["campaign"];
        $endpoint = $this->options["endpoint"];
        $defaultPriority = CCO_DEFAULT_CONTACT_PRIORITY;

        $ccoJsPath = plugin_dir_url(__FILE__).'../js/script.js';

        $scriptData = "
            document.getElementById('cco-button-open').addEventListener('click',function(){
                if(document.getElementById('cco-mask').style.display==\"block\"){
                    ccoOnClick(\"none\")
                }
                else{
                    startMessage('".$textBefore."','".$textAfter."','".$form."', '".$color."', '".$buttonCancel."', '".$buttonSend."','".$consent."', '".$imageDir."')
                    addEvents('".$textSuccess."', '".$textFailed."', '".$consent."', '".$color."', '".$buttonClose."', '".$imageDir."', '".$campaign."', '".$contactStatus."', '".$token."', '".$endpoint."',".$defaultPriority.");
                    ccoOnClick(\"block\")

                }
            })


            document.getElementById('cco-button-cancel-x').addEventListener('click',function(){
                ccoOnClick(\"none\")
            });

            document.getElementById('cco-mask').addEventListener('click',function(){
                ccoOnClick(\"none\")
            });

            document.addEventListener(\"keydown\", ({key}) => {
                if (key==\"Escape\" && document.getElementById('cco-mask').style.display==\"block\"){
                    ccoOnClick(\"none\")
                }
            })
        ";

        wp_enqueue_script( 'cco-script', $ccoJsPath );
        wp_add_inline_script( 'cco-script', $scriptData, "after");

    }
}
