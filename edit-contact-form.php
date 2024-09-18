<?php

error_reporting(E_ALL ^ E_NOTICE);
error_reporting(0);

require_once ECOMAIL_CF7_INTEGRATION_DIR . 'table_functions.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-api.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-contact-form7.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'second-tab-edit-contact-form.php';

//Ecomail CF7 Integration Settings
add_action('admin_init', 'ecomail_cf7_integration_settings_init_1');

function ecomail_cf7_integration_settings_init_1()
{

  add_settings_section(
    'ecomail_cf7_integration_section',
    'Ecomail CF7 Integration Settings',
    'ecomail_cf7_integration_section_callback_1',
    'ecomail_cf7_integration'
  );

  //Přidání polí
  add_settings_field(
    'sender_name_field',
    'Enter the name of sender',
    'ecomail_name_sender_field_callback',
    'ecomail_cf7_integration',
    'ecomail_cf7_integration_section'
  );

  register_setting('ecomail_cf7_integration', 'email_address_field');

  //Přidání polí
  add_settings_field(
    'email_address_field',
    'Put your email address in this field',
    'ecomail_email_address_field_callback',
    'ecomail_cf7_integration',
    'ecomail_cf7_integration_section'
  );

  register_setting('ecomail_cf7_integration', 'email_address_field');

  // Přidání polí
  add_settings_field(
    'ecomail_api_key_1',
    'API Key',
    'ecomail_api_key_callback_1',
    'ecomail_cf7_integration',
    'ecomail_cf7_integration_section'
  );

  register_setting('ecomail_cf7_integration', 'ecomail_api_key_1');

  // Přidání polí
  $form_id = null;
  if (isset($_GET['post'])) {
    $form_id = $_GET['post'];
    add_settings_field(
      'ecomail_list_id_' . $form_id,
      'List ID for Form',
      'ecomail_custom_list_id_callback_1',
      'ecomail_cf7_integration',
      'ecomail_cf7_integration_section',
      ['form_id' => $form_id]
    );
  }
  register_setting('ecomail_cf7_integration', 'ecomail_list_id_' . $form_id);

  add_settings_field(
    'ecomail_list_id_' . $form_id . '_template_id_1',
    'Template for Form',
    'ecomail_custom_template_id_callback_1',
    'ecomail_cf7_integration',
    'ecomail_cf7_integration_section',
    ['form_id' => $form_id]
  );

  register_setting('ecomail_cf7_integration', 'ecomail_list_id_' . $form_id . '_template_id_1');

  add_settings_field(
    'ecomail_list_id_' . $form_id . '_template_id_1_email',
    'Emails for templates_id: ',
    'ecomail_custom_emails_for_template_id_callback_1',
    'ecomail_cf7_integration',
    'ecomail_cf7_integration_section',
    ['form_id' => $form_id]
  );

  register_setting('ecomail_cf7_integration', 'ecomail_list_id_' . $form_id . '_template_id_1_email');
}

function ecomail_cf7_integration_section_callback_1()
{
  $html = 'Enter your Ecomail API information:';
  $html .= '<h3>List IDs</h3>';
  $html .= '<p>Set the List ID for each form:</p>';
  echo $html;
}

function ecomail_name_sender_field_callback()
{
  $nameSender = esc_attr(get_option('sender_name_field'));
  echo '<input type="text" id="sender_name_field" name="sender_name_field" value="' . $nameSender . '" />';
}

function ecomail_email_address_field_callback()
{
  $emailAddress = esc_attr(get_option('email_address_field'));
  echo '<input type="text" id="email_address_field" name="email_address_field" value="' . $emailAddress . '" />';
}

function ecomail_email_password_field_callback()
{
  $emailPassword = esc_attr(get_option('email_password_field'));
  echo '<input type="text" id="email_password_field" name="email_password_field" value="' . $emailPassword . '" />';
}

function ecomail_api_key_callback_1()
{
  $api_key = esc_attr(get_option('ecomail_api_key_1'));
  echo '<input type="text" id="ecomail_api_key_1" name="ecomail_api_key_1" value="' . $api_key . '" />';
}

function ecomail_custom_list_id_callback_1($args)
{
  $form_id = esc_attr($args['form_id']);
  $list_id = esc_attr(get_option('ecomail_list_id_' . $form_id . '_list_id_1'));
  echo '<input type="text" name="ecomail_list_id_' . $form_id . '_list_id_1" value="' . $list_id . '" />';
}

function ecomail_custom_template_id_callback_1($args)
{
  global $wpdb;
  $form_id = esc_attr($args['form_id']);

  $option_ids = array();
  $arr = array();

  for ($i = 1; $i <= 2000; $i++) {
    $array = $wpdb->get_results($wpdb->prepare(
      "SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = %s ORDER BY option_id ASC",
      'ecomail_list_id_' . $form_id . '_template_id_' . $i
    ));

    if ($array) {
      foreach ($array as $result) {
        $option_ids[] = $result->option_id;
      }
    }
  }

  // Проверка на пустой массив перед сортировкой
  if (!empty($option_ids)) {
    // Сортировка данных
    sort($option_ids);

    // Вывод отсортированных данных
    foreach ($option_ids as $option_id) {
      $option_value = $wpdb->get_var($wpdb->prepare(
        "SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = %d",
        $option_id
      ));

      // Добавляем только существующие значения
      if ($option_value !== null) {
        $arr[] = $option_value;
      }
    }
  }

  // Преобразуем массив в строку, разделенную запятыми
  $fields_string = implode(',', $arr);

  // Возвращаем input с данными
  echo '<input type="text" name="ecomail_list_id_' . $form_id . '_template_id_1" value="' . esc_attr($fields_string) . '" ><br>';
}


function ecomail_custom_emails_for_template_id_callback_1($args)
{
  global $wpdb;
  $form_id = esc_attr($args['form_id']);

  $option_ids = array(); // Create an empty array to store the fields
  $arr = array();

  for ($i = 1; $i <= 2000; $i++) {
    $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'ecomail_list_id_{$form_id}_template_id_{$i}' ORDER BY option_id ASC");
    if ($array) {
      foreach ($array as $result) {
        $option_ids[] = $result->option_id;
      }
    }
  }

  if (!empty($option_ids)) {
    // Сортировка данных
    sort($option_ids);

    // Вывод отсортированных данных
    foreach ($option_ids as $option_id) {
      $arr[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
    }
  }

  $subject_value = esc_attr(get_option('ecomail_list_id_' . $form_id . '_template_id_' . $arr[0] . '_subject'));

  echo "<label for='ecomail_list_id_" . $form_id . "_template_id_" . $arr[0] . "_subject'>Subject for sender:
    <input type='text' name='ecomail_list_id_" . $form_id . "_template_id_" . $arr[0] . "_subject' id='ecomail_list_id_" . $form_id . "_template_id_" . $arr[0] . "_subject' value='{$subject_value}'>
    </label><br><br>";

  //Убирает первое поле(template_id)
  $isFirst = true;
  foreach ($arr as $field) {
    if ($isFirst) {
      $isFirst = false;
      continue;
    }
    // Get the current values from the database
    $email_value = esc_attr(get_option('ecomail_list_id_' . $form_id . '_template_id_' . $field . '_email'));
    $subject_value = esc_attr(get_option('ecomail_list_id_' . $form_id . '_template_id_' . $field . '_subject'));

    echo "<label for='ecomail_list_id_" . $form_id . "_template_id_" . $field . "_email'>Email for template_id_{$field}
      <input type='text' name='ecomail_list_id_" . $form_id . "_template_id_" . $field . "_email' id='ecomail_list_id_" . $form_id . "_template_id_" . $field . "_email' value='{$email_value}'>
      </label>
      <label for='ecomail_list_id_" . $form_id . "_template_id_" . $field . "_subject'>Subject for template_id_{$field}
      <input type='text' name='ecomail_list_id_" . $form_id . "_template_id_" . $field . "_subject' id='ecomail_list_id_" . $form_id . "_template_id_" . $field . "_subject' value='{$subject_value}'>
      </label><br>";
  }
}

// Zpracování formuláře
if (isset($_POST['submit']) && $_POST['submit'] == 'Add settings') {
  update_option('sender_name_field', $_POST['sender_name_field']);
  update_option('email_address_field', $_POST['email_address_field']);
  update_option('email_password_field', $_POST['email_password_field']);
  update_option('ecomail_api_key_1', $_POST['ecomail_api_key_1']);

  if (isset($_GET['post'])) {
    $form_id = $_GET['post'];
  }

  if (isset($_POST['ecomail_list_id_' . $form_id . '_list_id_1'])) {
    update_option('ecomail_list_id_' . $form_id . '_list_id_1', $_POST['ecomail_list_id_' . $form_id . '_list_id_1']);
  }

  if (isset($_POST['ecomail_list_id_' . $form_id . '_template_id_1'])) {
    $rows = $_POST['ecomail_list_id_' . $form_id . "_template_id_1"];
    $rows_array = preg_split('/[, ]+/', $rows);

    // Удаление всех предыдущих значений template_id
    for ($i = 1; $i <= 2000; $i++) { // Здесь 2000 - максимальное значение template_id, которое нужно удалить, можно изменить в соответствии с вашими требованиями
      delete_option('ecomail_list_id_' . $form_id . "_template_id_{$i}");
      delete_option('ecomail_list_id_' . $form_id . "_template_id_{$i}_email");
      delete_option('ecomail_list_id_' . $form_id . "_template_id_{$i}_subject");
    }

    // Запись новых значений template_id
    foreach ($rows_array as $key => $row) {
      $template_id_option = 'ecomail_list_id_' . $form_id . '_template_id_' . $row;
      update_option($template_id_option, $row);

      $email_field_name = 'ecomail_list_id_' . $form_id . '_template_id_' . $row . '_email';
      $email = $_POST[$email_field_name];
      update_option($email_field_name, $email);

      $subject_field_name = 'ecomail_list_id_' . $form_id . '_template_id_' . $row . '_subject';
      $subject = $_POST[$subject_field_name];
      update_option($subject_field_name, $subject);
    }
  }
}

// Přidání panelu nastavení CF7
add_filter('wpcf7_editor_panels', 'add_ecomail_integration');
function add_ecomail_integration($panels)
{
  if (current_user_can('wpcf7_edit_contact_form')) {
    $panels['wpcf7cf-html-message'] = array(
      'title'    => __('Ecomail integration', 'wpcf7cf'),
      'callback' => 'ecomail_cf7_integration_create_admin_page_1',
    );
  }
  return $panels;
}

add_action('wpcf7_save_contact_form', 'ecomail_cf7_integration_create_admin_page_1');

// Nastavení pluginu
function ecomail_cf7_integration_create_admin_page_1()
{
?>
  <div class="wrap">
    <form method="post" action="">
      <?php
      settings_fields('ecomail_cf7_integration');
      do_settings_sections('ecomail_cf7_integration');
      submit_button('Add settings');
      ?>
    </form>
  </div>
<?php
}
