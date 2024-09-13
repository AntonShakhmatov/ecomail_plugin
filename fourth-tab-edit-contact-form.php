<?php

add_action('admin_init', 'cf7_fields_cron_settings_init');

//CF7 Cron field Name
function cf7_fields_cron_settings_init() {
    add_settings_section(
        'cf7_integration_cron_section',
        'CF7 Cron field Name',
        'cf7_cron_integration_section_callback',
        'cf7_cron_integration'
    );

    // Přidání polí
    add_settings_field(
        'contact_form_field_email_cron',
        'Cron time field(in sec)',
        'contact_form_field_email_cron_callback',
        'cf7_cron_integration',
        'cf7_integration_cron_section',
    );
    register_setting('cf7_tags_integration', 'contact_form_field_email_cron');

}

function cf7_cron_integration_section_callback() {
    echo 'Put in actual cron settings';
    echo '<h3>Cron fields settings</h3>';
    echo '<p>Set the fields cron for sending transactions mail settings:</p>';
}

function contact_form_field_email_cron_callback($args) {
    $cron = esc_attr(get_option('contact_form_field_email_cron'));
    echo "<input type='text' id='contact_form_field_email_cron' name='contact_form_field_email_cron' value='$cron' />";
}

if (isset($_POST['submit']) && $_POST['submit'] == 'Add cron settings') {
    update_option('contact_form_field_email_cron', $_POST['contact_form_field_email_cron']);
}

add_filter( 'wpcf7_editor_panels', function($panels) {
    $panels['cron-panel'] = array(
        'title' => __( 'Field of cron settings', 'contact-form-7' ),
        'callback' => 'cf7_cron_form_fields'
    );
    return $panels;
});

add_action( 'wpcf7_save_contact_form', 'cf7_cron_form_fields');

// Nastavení pluginu
function cf7_cron_form_fields() {
    ?>
    <div class="wrap">
        <form method="post" action="">
            <?php
            settings_fields('cf7_cron_integration');
            do_settings_sections('cf7_cron_integration');
            submit_button('Add cron settings');
            ?>
        </form>
    </div>
    <?php
}