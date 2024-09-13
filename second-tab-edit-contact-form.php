<?php

require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-cf7-integration.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-api.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-contact-form7.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'third-tab-edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'edit-contact-form.php';

//CF7 Fields Name
add_action('admin_init', 'cf7_fields_naming_settings_init');

function cf7_fields_naming_settings_init() {

  add_settings_section(
    'cf7_integration_section',
    'CF7 Fields Name',
    'cf7_integration_section_callback',
    'cf7_integration'
  );

  // Přidání polí
  $form_id = null;
  if(isset($_GET['post'])){
    $form_id = $_GET['post'];
  add_settings_field(
    'contact_form_field_name_' . $form_id,
    'Field name',
    'contact_form_field_name_callback',
    'cf7_integration',
    'cf7_integration_section',
    ['form_id' => $form_id]
  );
}
    register_setting('cf7_integration', 'contact_form_field_name_' . $form_id);

  // Přidání polí
  add_settings_field(
    'contact_form_field_surname_' . $form_id,
    'Field Surname',
    'contact_form_field_surname_callback',
    'cf7_integration',
    'cf7_integration_section',
    ['form_id' => $form_id]
  );
    register_setting('cf7_integration', 'contact_form_field_surname_' . $form_id);

  // Přidání polí
  add_settings_field(
    'contact_form_field_email_' . $form_id,
    'Field Email',
    'contact_form_field_email_callback',
    'cf7_integration',
    'cf7_integration_section',
    ['form_id' => $form_id]
  );
    register_setting('cf7_integration', 'contact_form_field_email_' . $form_id);

    add_settings_field(
      'contact_form_field_phone_' . $form_id,
      'Field Phone',
      'contact_form_field_phone_callback',
      'cf7_integration',
      'cf7_integration_section',
      ['form_id' => $form_id]
    );
    register_setting('cf7_integration', 'contact_form_field_phone_' . $form_id);
  }

  function cf7_integration_section_callback() {
    echo 'Put in actual fields names';
    echo '<h3>Fields names</h3>';
    echo '<p>Set the fields names for each form:</p>';
  }

  function contact_form_field_name_callback($args) {
    $form_id = esc_attr($args['form_id']);
    $name = esc_attr(get_option('contact_form_field_name_' . $form_id));
    echo "<input type='text' id='contact_form_field_name_{$form_id}' name='contact_form_field_name_{$form_id}' value='$name' />";
  }

  function contact_form_field_surname_callback($args) {
    $form_id = esc_attr($args['form_id']);
    $surname = esc_attr(get_option('contact_form_field_surname_' . $form_id));
    echo "<input type='text' id='contact_form_field_surname_{$form_id}' name='contact_form_field_surname_{$form_id}' value='$surname' />";
  }

  function contact_form_field_email_callback($args) {
    $form_id = esc_attr($args['form_id']);
    $email = esc_attr(get_option('contact_form_field_email_' . $form_id));
    echo "<input type='text' id='contact_form_field_email_{$form_id}' name='contact_form_field_email_{$form_id}' value='$email' />";
  }

  function contact_form_field_phone_callback($args) {
    $form_id = esc_attr($args['form_id']);
    $phone = esc_attr(get_option('contact_form_field_phone_' . $form_id));
    echo "<input type='text' id='contact_form_field_phone_{$form_id}' name='contact_form_field_phone_{$form_id}' value='$phone' />";
  }

  if (isset($_POST['submit']) && $_POST['submit'] == 'Add fields') {
    if(isset($_GET['post'])){
      $form_id = $_GET['post'];
    }
    update_option('contact_form_field_name_' . $form_id, $_POST['contact_form_field_name_' . $form_id]);
    update_option('contact_form_field_surname_' . $form_id, $_POST['contact_form_field_surname_' . $form_id]);
    update_option('contact_form_field_email_' . $form_id, $_POST['contact_form_field_email_' . $form_id]);
    update_option('contact_form_field_phone_' . $form_id, $_POST['contact_form_field_phone_' . $form_id]);
  }

add_filter( 'wpcf7_editor_panels', function($panels) {
  $panels['preview-panel'] = array(
          'title' => __( 'Fields names', 'contact-form-7' ),
          'callback' => 'cf7_name_form_fields'
  );
  return $panels;
});

add_action( 'wpcf7_save_contact_form', 'cf7_name_form_fields');

// Nastavení pluginu
function cf7_name_form_fields() {
  ?>
  <div class="wrap">
    <form method="post" action="">
      <?php
      settings_fields('cf7_integration');
      do_settings_sections('cf7_integration');
      submit_button('Add fields');
      ?>
    </form>
  </div>
  <?php
}