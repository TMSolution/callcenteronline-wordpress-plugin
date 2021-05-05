<?php

class cco_call_me_admin
{

    private $options;

    public function __construct()
    {

        $this->options = get_option('cco');
    }

    public function show()
    {
        add_action('admin_head', array($this, 'cco_admin_stylesheet_url'));
        add_action('admin_menu', array($this, 'add_cco_admin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function cco_admin_stylesheet_url()
    {
        $ccoStylePath = plugin_dir_url(__FILE__).'../css/style.css';
        wp_enqueue_style('cco-admin-style', $ccoStylePath, array(), '0.1.0', 'all');
    }

    public function add_cco_admin_page()
    {
        add_options_page(
            'Ustawienia',
            'Call Center Online',
            'manage_options',
            'cco_settings_page2',
            array($this, 'create_page')
        );
    }

    public function create_page()
    {
        ?>
        <div class="wrap">

            <img src="<?php echo plugin_dir_url(__DIR__) . 'image/logo.png' ?>" class="cco-admin-logo"/>
            <h2>Ustawienia wtyczki Call Center Online</h2>
			<form method="post" action="options.php">
            <?php

        settings_fields('cco_options');
        do_settings_sections('cco_settings_page1');
        submit_button('Zapisz');
        do_settings_sections('cco_settings_page2');
        submit_button('Zapisz');

        ?>
            </form>
		</div>
        <?php
}

    public function page_init()
    {
        register_setting(
            'cco_options',
            'cco',
            array($this, 'sanitize')
        );

        add_settings_section(
            'cco_section_api',
            CCO_CONFIG_TEXT_SECTION_ACCESS_DATA,
            array($this, 'section_1_callback'),
            'cco_settings_page1'
        );

        add_settings_section(
            'cco_section_params',
            CCO_CONFIG_TEXT_SECTION_CONFIGURATION,
            array($this, 'section_2_callback'),
            'cco_settings_page2'
        );

        add_settings_field(
            'endpoint',
            CCO_CONFIG_TEXT_ENDPOINT,
            array($this, 'endpoint_callback'),
            'cco_settings_page1',
            'cco_section_api'
        );

        add_settings_field(
            'login',
            CCO_CONFIG_TEXT_LOGIN,
            array($this, 'login_callback'),
            'cco_settings_page1',
            'cco_section_api'
        );

        add_settings_field(
            'password',
            CCO_CONFIG_TEXT_PASSWORD,
            array($this, 'password_callback'),
            'cco_settings_page1',
            'cco_section_api'
        );

        add_settings_field(
            'client_id',
            CCO_CONFIG_TEXT_CLIENT_ID,
            array($this, 'client_id_callback'),
            'cco_settings_page1',
            'cco_section_api'
        );

        add_settings_field(
            'client_secret',
            CCO_CONFIG_TEXT_SECRET,
            array($this, 'client_secret_callback'),
            'cco_settings_page1',
            'cco_section_api'
        );

        add_settings_field(
            'title',
            CCO_CONFIG_TEXT_TITLE,
            array($this, 'title_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'text_before',
            CCO_CONFIG_TEXT_BEFORE_FORM,
            array($this, 'text_before_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'text_after',
            CCO_CONFIG_TEXT_AFTER_FORM,
            array($this, 'text_after_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'text_success',
            CCO_CONFIG_TEXT_MESSAGE_SUCCESS,
            array($this, 'text_success_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'text_failed',
            CCO_CONFIG_TEXT_MESSAGE_FAIL,
            array($this, 'text_failed_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'button_cancel',
            CCO_CONFIG_TEXT_BUTTON_CANCEL,
            array($this, 'button_cancel_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'button_close',
            CCO_CONFIG_TEXT_BUTTON_CLOSE,
            array($this, 'button_close_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'button_send',
            CCO_CONFIG_TEXT_BUTTON_SEND,
            array($this, 'button_send_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'button_position_x',
            CCO_CONFIG_TEXT_BUTTON_POSITION_X,
            array($this, 'button_position_x_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'button_position_y',
            CCO_CONFIG_TEXT_BUTTON_POSITION_Y,
            array($this, 'button_position_y_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'color',
            CCO_CONFIG_TEXT_COLOR,
            array($this, 'color_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'campaign',
            CCO_CONFIG_TEXT_CAMPAIGN,
            array($this, 'campaign_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'contact_status',
            CCO_CONFIG_TEXT_STATUS,
            array($this, 'contact_status_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'fields',
            CCO_CONFIG_TEXT_FIELDS,
            array($this, 'fields_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'names_mapper',
            CCO_CONFIG_TEXT_MAPPER,
            array($this, 'names_mapper_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

        add_settings_field(
            'consent',
            CCO_CONFIG_TEXT_CONSENT,
            array($this, 'consent_callback'),
            'cco_settings_page2',
            'cco_section_params'
        );

    }

    public function sanitize($input)
    {
        return $input;
    }

    public function section_1_callback()
    {
        echo CCO_CONFIG_TEXT_SECTION_ACCESS_DATA_DESCRIPTION;
    }

    public function section_2_callback()
    {
        echo CCO_CONFIG_TEXT_SECTION_CONFIGURATION_DESCRIPTION;
    }

    public function endpoint_callback()
    {

        if (isset($this->options['endpoint'])) {
            $endpoint = esc_attr($this->options['endpoint']);
        }

        echo '<input class="cco-admin-field" type="text" id="endpoint" name="cco[endpoint]" value="' . $endpoint . '">';
    }

    public function login_callback()
    {

        if (isset($this->options['login'])) {
            $login = esc_attr($this->options['login']);
        }

        echo '<input class="cco-admin-field" type="text" id="login" name="cco[login]" value="' . $login . '">';
    }

    public function password_callback()
    {
        if (isset($this->options['password'])) {
            $password = esc_attr($this->options['password']);
        }

        echo '<input class="cco-admin-field" type="password" id="password" name="cco[password]" value="' . $password . '">';
    }
    public function client_id_callback()
    {
        if (isset($this->options['client_id'])) {
            $client_id = esc_attr($this->options['client_id']);
        }

        echo '<input class="cco-admin-field" type="text" id="client_id" name="cco[client_id]" value="' . $client_id . '">';
    }
    public function client_secret_callback()
    {
        if (isset($this->options['client_secret'])) {
            $client_secret = esc_attr($this->options['client_secret']);
        }

        echo '<input class="cco-admin-field" type="text" id="client_secret" name="cco[client_secret]" value="' . $client_secret . '">';
    }

    public function title_callback()
    {
        if (isset($this->options['title'])) {
            $title = esc_attr($this->options['title']);
        } else {
            $title = CCO_DEFAULT_TITLE;
        }

        echo '<input class="cco-admin-field" type="text" id="title" name="cco[title]" value="' . $title . '">';
    }

    public function text_before_callback()
    {
        if (isset($this->options['text_before'])) {
            $text_before = esc_attr($this->options['text_before']);
        } else {
            $text_before = esc_attr(CCO_DEFAULT_TEXT_BEFORE);
        }

        echo '<input class="cco-admin-field" type="text" id="text_before" name="cco[text_before]" value="' . $text_before . '">';
    }

    public function text_after_callback()
    {
        if (isset($this->options['text_after'])) {
            $text_after = esc_attr($this->options['text_after']);
        } else {
            $text_after = esc_attr(CCO_DEFAULT_TEXT_AFTER);
        }

        echo '<input class="cco-admin-field" type="text" id="text_after" name="cco[text_after]" value="' . $text_after . '">';
    }

    public function text_success_callback()
    {
        if (isset($this->options['text_success'])) {
            $text_success = esc_attr($this->options['text_success']);
        }

        echo '<input class="cco-admin-field" type="text" id="text_success" name="cco[text_success]" value="' . $text_success . '">';
    }

    public function text_failed_callback()
    {
        if (isset($this->options['text_failed'])) {
            $text_failed = esc_attr($this->options['text_failed']);
        }

        echo '<input class="cco-admin-field" type="text" id="text_failed" name="cco[text_failed]" value="' . $text_failed . '">';
    }

    public function button_cancel_callback()
    {
        if (isset($this->options['button_cancel'])) {
            $button_cancel = esc_attr($this->options['button_cancel']);
        } else {
            $button_cancel = esc_attr(CCO_DEFAULT_BUTTON_CANCEL);
        }

        echo '<input class="cco-admin-field" type="text" id="button_cancel" name="cco[button_cancel]" value="' . $button_cancel . '">';
    }

    public function button_close_callback()
    {
        if (isset($this->options['button_close'])) {
            $button_close = esc_attr($this->options['button_close']);
        } else {
            $button_close = esc_attr(CCO_DEFAULT_BUTTON_CLOSE);
        }

        echo '<input class="cco-admin-field" type="text" id="button_close" name="cco[button_close]" value="' . $button_close . '">';
    }

    public function button_send_callback()
    {
        if (isset($this->options['button_send'])) {
            $button_send = esc_attr($this->options['button_send']);
        } else {
            $button_send = esc_attr(CCO_DEFAULT_BUTTON_SEND);
        }

        echo '<input class="cco-admin-field" type="text" id="button_send" name="cco[button_send]" value="' . $button_send . '">';
    }

    public function color_callback()
    {
        if (isset($this->options['color'])) {
            $color = esc_attr($this->options['color']);
        }

        echo '<input class="cco-admin-short-field" type="color" id="color" name="cco[color]" value="' . $color . '">';
    }

    public function button_position_x_callback()
    {

        if (isset($this->options['button_position_x'])) {
            $button_position_x = esc_attr($this->options['button_position_x']);
        }

        echo '<select class="cco-admin-field" type="text" id="button_position_x" name="cco[button_position_x]">
                <option value="left" ' . ($button_position_x == "left" ? "selected" : "") . '>Z lewej</option>
                <option value="center" ' . ($button_position_x == "center" ? "selected" : "") . '>Na środku</option>
                <option value="right" ' . ($button_position_x == "right" ? "selected" : "") . '>Z prawej</option>
              </select>';
    }

    public function button_position_y_callback()
    {

        if (isset($this->options['button_position_y'])) {
            $button_position_y = esc_attr($this->options['button_position_y']);
        }

        echo '<select class="cco-admin-field" type="text" id="button_position_y" name="cco[button_position_y]">
                <option value="top" ' . ($button_position_y == "top" ? "selected" : "") . '>Na górze</option>
                <option value="center" ' . ($button_position_y == "center" ? "selected" : "") . '>Na środku</option>
                <option value="bottom" ' . ($button_position_y == "bottom" ? "selected" : "") . '>Na dole</option>
              </select>';
    }

    public function contact_status_callback()
    {

        if (isset($this->options['contact_status'])) {
            $contact_status = esc_attr($this->options['contact_status']);
            
        }
        
        if (!$contact_status) {$contact_status = CCO_DEFAULT_CONTACT_STATUS;}

        echo '<select class="cco-admin-field" type="text" id="contact_status" name="cco[contact_status]">';
        foreach (CCO_CONTACT_STATUSES as $key => $val) {
            echo '<option value="' . $key . '" ' . ($key == $contact_status ? 'selected' : '') . '>' . $val . '</option>';
        }

        echo '</select>';
    }

    public function campaign_callback()
    {

        $getAllFieldsFromApi = new cco_api($this->options);
        $camps = $getAllFieldsFromApi->getCampaigns();

        if (isset($this->options['campaign'])) {
            $campaign = esc_attr($this->options['campaign']);
        }

        echo '<select class="cco-admin-field" type="text" id="campaign" name="cco[campaign]">
              <option value="">Wybierz kampanię</option>';
        foreach ($camps as $camp) {
            echo '<option value="' . $camp->id . '" ' . ($camp->id == $campaign ? 'selected' : '') . '>' . $camp->name . '</option>';
        }
        echo '</select>';
    }

    public function fields_callback()
    {

        if (isset($this->options['fields'])) {
            $fields = esc_attr($this->options['fields']);
        }
        echo '<textarea class="cco-admin-field-style" id="fields" name="cco[fields]">' . $fields . '</textarea>';
    }

    public function names_mapper_callback()
    {
        if (isset($this->options['names_mapper'])) {
            $names_mapper = esc_attr($this->options['names_mapper']);
        }

        echo '<textarea class="cco-admin-field-style" id="names_mapper" name="cco[names_mapper]">' . $names_mapper . '</textarea>';
    }

    public function consent_callback()
    {
        if (isset($this->options['consent'])) {
            $consent = esc_attr($this->options['consent']);
        }

        echo '<input type="checkbox" class="cco-admin-field" id="consent" name="cco[consent]" ' . ($consent == 'on' ? 'checked="true"' : '') . '/>';
    }

}
