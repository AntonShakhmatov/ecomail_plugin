<?php

require_once ECOMAIL_CF7_INTEGRATION_DIR . 'fourth-tab-edit-contact-form.php';

//CF7 Tags field Name
add_action('admin_init', 'cf7_fields_tags_naming_settings_init');

function cf7_fields_tags_naming_settings_init()
{

  add_settings_section(
    'cf7_integration_tag_section',
    'CF7 Tags field Name',
    'cf7_tags_integration_section_callback',
    'cf7_tags_integration'
  );

  // Přidání polí
  $form_id = null;
  if (isset($_GET['post'])) {
    $form_id = $_GET['post'];
    add_settings_field(
      'contact_form_field_name_tag_' . $form_id,
      'Field Tag company name',
      'contact_form_field_name_tag_callback',
      'cf7_tags_integration',
      'cf7_integration_tag_section',
      ['form_id' => $form_id]
    );
  }
  register_setting('cf7_tags_integration', 'contact_form_field_name_tag_' . $form_id);

  // Přidání polí
  add_settings_field(
    'contact_form_field_email_tag_' . $form_id,
    'Field Tag Email',
    'contact_form_field_email_tag_callback',
    'cf7_tags_integration',
    'cf7_integration_tag_section',
    ['form_id' => $form_id]
  );
  register_setting('cf7_tags_integration', 'contact_form_field_email_tag_' . $form_id);

  add_settings_field(
    'contact_form_field_phone_tag_' . $form_id,
    'Field Tag Phone',
    'contact_form_field_phone_tag_callback',
    'cf7_tags_integration',
    'cf7_integration_tag_section',
    ['form_id' => $form_id]
  );
  register_setting('cf7_tags_integration', 'contact_form_field_phone_tag_' . $form_id);
}

function cf7_tags_integration_section_callback()
{
  echo 'Put in actual fields tag names';
  echo '<h3>Tag fields names</h3>';
  echo '<p>Set the fields tag names for each form:</p>';
}

function contact_form_field_name_tag_callback($args)
{
  $form_id = esc_attr($args['form_id']);
  $name = esc_attr(get_option('contact_form_field_name_tag_' . $form_id));
  echo "<input type='text' id='contact_form_field_name_tag_{$form_id}' name='contact_form_field_name_tag_{$form_id}' value='$name' />";
}

function contact_form_field_email_tag_callback($args)
{
  $form_id = esc_attr($args['form_id']);
  $email = esc_attr(get_option('contact_form_field_email_tag_' . $form_id));
  echo "<input type='text' id='contact_form_field_email_tag_{$form_id}' name='contact_form_field_email_tag_{$form_id}' value='$email' />";
}

function contact_form_field_phone_tag_callback($args)
{
  $form_id = esc_attr($args['form_id']);
  $phone = esc_attr(get_option('contact_form_field_phone_tag_' . $form_id));
  echo "<input type='text' id='contact_form_field_phone_tag_{$form_id}' name='contact_form_field_phone_tag_{$form_id}' value='$phone' />";
}

if (isset($_POST['submit']) && $_POST['submit'] == 'Add tags') {
  if (isset($_GET['post'])) {
    $form_id = $_GET['post'];
  }
  update_option('contact_form_field_name_tag_' . $form_id, $_POST['contact_form_field_name_tag_' . $form_id]);
  update_option('contact_form_field_surname_tag_' . $form_id, $_POST['contact_form_field_surname_tag_' . $form_id]);
  update_option('contact_form_field_email_tag_' . $form_id, $_POST['contact_form_field_email_tag_' . $form_id]);
  update_option('contact_form_field_phone_tag_' . $form_id, $_POST['contact_form_field_phone_tag_' . $form_id]);
}

add_filter('wpcf7_editor_panels', function ($panels) {
  $panels['mail-tags-panel'] = array(
    'title' => __('Fields of tags', 'contact-form-7'),
    'callback' => 'cf7_name_tag_form_fields'
  );
  return $panels;
});

add_action('wpcf7_save_contact_form', 'cf7_name_tag_form_fields');

// Nastavení pluginu
function cf7_name_tag_form_fields()
{
?>
  <div class="wrap">
    <form method="post" action="">
      <?php
      settings_fields('cf7_tags_integration');
      do_settings_sections('cf7_tags_integration');
      submit_button('Add tags');
      ?>
    </form>
  </div>
<?php
}
