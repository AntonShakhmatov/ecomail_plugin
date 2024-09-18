<?php

//CF7 MergeTag settings
add_action('admin_init', 'cf7_mergeTag_settings_init');

function cf7_mergeTag_settings_init()
{
    add_settings_section(
        'cf7_mergeTag_section',
        'CF7 MergeTag settings',
        'cf7_mergeTag_section_callback',
        'cf7_mergeTag'
    );

    // Přidání polí
    $form_id = null;
    if (isset($_GET['post'])) {
        $form_id = $_GET['post'];
    }

    add_settings_field(
        'cf7_mergeTag_template_form_id_' . $form_id . '_template_id_1',
        'Template for Form',
        'cf7_mergeTag_transaction_mail_form_id_callback_1',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );

    register_setting('cf7_mergeTag', 'cf7_mergeTag_template_form_id_' . $form_id . '_template_id_1');

    add_settings_field(
        'cf7_mergeTag_template_form_id_' . $form_id . '_template_id_1_email',
        'Emails for templates_id: ',
        'cf7_mergeTag_transaction_mail_custom_for_template_id_callback_1',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );

    register_setting('cf7_mergeTag', 'cf7_mergeTag_template_form_id_' . $form_id . '_template_id_1_email');

    add_settings_field(
        "cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_name",
        'Name of sender:',
        'cf7_mergeTag_name_form_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', "cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_name");

    add_settings_field(
        "cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email",
        'Email of sender:',
        'cf7_mergeTag_email_form_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', "cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email");

    add_settings_field(
        "cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field",
        'Email field name:',
        'cf7_mergeTag_email_field_form_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', "cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field");

    add_settings_field(
        'cf7_mergeTag_string_section_settings_' . $form_id,
        'First tag',
        'cf7_mergeTag_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', 'cf7_mergeTag_string_section_settings_' . $form_id);


    // Přidání polí
    add_settings_field(
        'cf7_mergeTag_date_section_settings_' . $form_id,
        'Second tag',
        'cf7_mergeTag_date_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', 'cf7_mergeTag_date_section_settings_' . $form_id);


    // Přidání polí
    add_settings_field(
        'cf7_mergeTag_json_section_settings_' . $form_id,
        'Third tag',
        'cf7_mergeTag_json_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', 'cf7_mergeTag_json_section_settings_' . $form_id);


    // Přidání polí
    add_settings_field(
        'cf7_mergeTag_number_section_settings_' . $form_id,
        'Fourth tag',
        'cf7_mergeTag_number_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', 'cf7_mergeTag_number_section_settings_' . $form_id);


    // Přidání polí
    add_settings_field(
        'cf7_mergeTag_url_section_settings_' . $form_id,
        'Fifth tag',
        'cf7_mergeTag_url_callback',
        'cf7_mergeTag',
        'cf7_mergeTag_section',
        ['form_id' => $form_id]
    );
    register_setting('cf7_mergeTag', 'cf7_mergeTag_url_section_settings_' . $form_id);
}

function cf7_mergeTag_section_callback()
{
    echo 'Put in actual mergeTag settings';
    echo '<h3>MergeTag fields settings</h3>';
    echo '<p>Set the fields tags for sending transactions mails:</p>';
}

function cf7_mergeTag_transaction_mail_form_id_callback_1($args)
{
    global $wpdb;
    $form_id = esc_attr($args['form_id']);

    $option_ids = array(); // Создаем пустой массив для хранения полей
    $arr = array();
    for ($i = 1; $i <= 2000; $i++) {
        $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'cf7_mergeTag_template_form_id_{$form_id}_template_id_{$i}' ORDER BY option_id ASC");
        if ($array) {
            foreach ($array as $result) {
                $option_ids[] = $result->option_id;
            }
        }
    }

    // Сортировка данных
    sort($option_ids);
    // Вывод отсортированных данных
    foreach ($option_ids as $option_id) {
        $arr[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
    }

    $fields_string = implode(',', $arr); // Преобразуем массив в строку, разделенную запятыми

    echo '<input type="text" name="cf7_mergeTag_template_form_id_' . $form_id . '_template_id_1" value="' . $fields_string . '" ><br>';
}

function cf7_mergeTag_transaction_mail_custom_for_template_id_callback_1($args)
{
    global $wpdb;
    $form_id = esc_attr($args['form_id']);

    $option_ids = array();
    $arr = array();

    for ($i = 1; $i <= 2000; $i++) {
        $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'cf7_mergeTag_template_form_id_{$form_id}_template_id_{$i}' ORDER BY option_id ASC");
        if ($array) {
            foreach ($array as $result) {
                $option_ids[] = $result->option_id;
            }
        }
    }

    // Сортировка данных
    sort($option_ids);

    // Вывод отсортированных данных
    foreach ($option_ids as $option_id) {
        $arr[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
    }

    $subject_value = esc_attr(get_option('cf7_mergeTag_template_form_id_' . $form_id . '_template_id_' . $arr[0] . '_subject'));

    echo "<label for='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $arr[0] . "_subject'>Subject for sender:
    <input type='text' name='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $arr[0] . "_subject' id='cf7_mergeTag_form_id_" . $form_id . "_template_id_" . $arr[0] . "_subject' value='{$subject_value}'>
    </label><br><br>";

    //Убирает первое поле(template_id)
    $isFirst = true;
    foreach ($arr as $field) {
        if ($isFirst) {
            $isFirst = false;
            continue;
        }
        // Get the current values from the database
        $email_value = esc_attr(get_option('cf7_mergeTag_template_form_id_' . $form_id . '_template_id_' . $field . '_email'));
        $subject_value = esc_attr(get_option('cf7_mergeTag_template_form_id_' . $form_id . '_template_id_' . $field . '_subject'));

        echo "<label for='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $field . "_email'>Email for template_id_{$field}
      <input type='text' name='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $field . "_email' id='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $field . "_email' value='{$email_value}'>
      </label>
      <label for='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $field . "_subject'>Subject for template_id_{$field}
      <input type='text' name='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $field . "_subject' id='cf7_mergeTag_template_form_id_" . $form_id . "_template_id_" . $field . "_subject' value='{$subject_value}'>
      </label><br>";
    }
}

function cf7_mergeTag_name_form_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $name = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_name");
    echo "<input type='text' name='cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_name' value='$name'><br>";
}

function cf7_mergeTag_email_form_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $email = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email");
    echo "<input type='text' name='cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email' value='$email'><br>";
}

function cf7_mergeTag_subject_form_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $subject = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_subject");
    echo "<input type='text' name='cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_subject' value='$subject'><br>";
}

function cf7_mergeTag_email_field_form_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $email_field = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field");
    echo "<input type='text' name='cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field' value='$email_field'><br>";
}

function cf7_mergeTag_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $mergeTag = esc_attr(get_option('contact_form_field_text_merge_tag_' . $form_id));
    $selected_option = esc_attr(get_option('contact_form_field_text_merge_type_' . $form_id));
    $first = esc_attr(get_option("contact_form_field_date_merge_tag_first_{$form_id}"));

    echo "<input type='text' id='contact_form_field_text_merge_tag_{$form_id}' name='contact_form_field_text_merge_tag_{$form_id}' value='$mergeTag' />";
    echo "<select name='type_first_{$form_id}' id='type_first_{$form_id}'>
              <option value='string' " . selected($selected_option, 'string', false) . ">string</option>
              <option value='date' " . selected($selected_option, 'date', false) . ">date</option>
              <option value='json' " . selected($selected_option, 'json', false) . ">json</option>
              <option value='int' " . selected($selected_option, 'int', false) . ">int</option>
              <option value='url' " . selected($selected_option, 'url', false) . ">url</option>
        </select>";
    echo "<label for='contact_form_field_date_merge_tag_first_{$form_id}'>Field name:<input type='text' id='contact_form_field_date_merge_tag_first_{$form_id}' name='contact_form_field_date_merge_tag_first_{$form_id}' value='$first'/></label>";
}

function cf7_mergeTag_date_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $mergeTag = esc_attr(get_option('contact_form_field_date_merge_tag_' . $form_id));
    $selected_option = esc_attr(get_option('contact_form_field_date_merge_type_' . $form_id));
    $second = esc_attr(get_option("contact_form_field_date_merge_tag_second_{$form_id}"));

    echo "<input type='text' id='contact_form_field_date_merge_tag_{$form_id}' name='contact_form_field_date_merge_tag_{$form_id}' value='$mergeTag' />";
    echo "<select name='type_second_{$form_id}' id='type_second_{$form_id}'>
              <option value='string' " . selected($selected_option, 'string', false) . ">string</option>
              <option value='date' " . selected($selected_option, 'date', false) . ">date</option>
              <option value='json' " . selected($selected_option, 'json', false) . ">json</option>
              <option value='int' " . selected($selected_option, 'int', false) . ">int</option>
              <option value='url' " . selected($selected_option, 'url', false) . ">url</option>
        </select>";
    echo "<label for='contact_form_field_date_merge_tag_second_{$form_id}'>Field name:<input type='text' id='contact_form_field_date_merge_tag_second_{$form_id}' name='contact_form_field_date_merge_tag_second_{$form_id}' value='$second'/></label>";
}

function cf7_mergeTag_json_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $mergeTag = esc_attr(get_option('contact_form_field_json_merge_tag_' . $form_id));
    $selected_option = esc_attr(get_option('contact_form_field_json_merge_type_' . $form_id));
    $third = esc_attr(get_option("contact_form_field_date_merge_tag_third_{$form_id}"));


    echo "<input type='text' id='contact_form_field_json_merge_tag_{$form_id}' name='contact_form_field_json_merge_tag_{$form_id}' value='$mergeTag' />";
    echo "<select name='type_third_{$form_id}' id='type_third_{$form_id}'>
              <option value='string' " . selected($selected_option, 'string', false) . ">string</option>
              <option value='date' " . selected($selected_option, 'date', false) . ">date</option>
              <option value='json' " . selected($selected_option, 'json', false) . ">json</option>
              <option value='int' " . selected($selected_option, 'int', false) . ">int</option>
              <option value='url' " . selected($selected_option, 'url', false) . ">url</option>
        </select>";
    echo "<label for='contact_form_field_date_merge_tag_third_{$form_id}'>Field name:<input type='text' id='contact_form_field_date_merge_tag_third_{$form_id}' name='contact_form_field_date_merge_tag_third_{$form_id}' value='$third'/></label>";
}

function cf7_mergeTag_number_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $mergeTag = esc_attr(get_option('contact_form_field_number_merge_tag_' . $form_id));
    $selected_option = esc_attr(get_option('contact_form_field_number_merge_type_' . $form_id));
    $fourth = esc_attr(get_option("contact_form_field_date_merge_tag_fourth_{$form_id}"));

    echo "<input type='text' id='contact_form_field_number_merge_tag_{$form_id}' name='contact_form_field_number_merge_tag_{$form_id}' value='$mergeTag' />";
    echo "<select name='type_fourth_{$form_id}' id='type_fourth_{$form_id}'>
              <option value='string' " . selected($selected_option, 'string', false) . ">string</option>
              <option value='date' " . selected($selected_option, 'date', false) . ">date</option>
              <option value='json' " . selected($selected_option, 'json', false) . ">json</option>
              <option value='int' " . selected($selected_option, 'int', false) . ">int</option>
              <option value='url' " . selected($selected_option, 'url', false) . ">url</option>
        </select>";
    echo "<label for='contact_form_field_date_merge_tag_fourth_{$form_id}'>Field name:<input type='text' id='contact_form_field_date_merge_tag_fourth_{$form_id}' name='contact_form_field_date_merge_tag_fourth_{$form_id}' value='$fourth'/></label>";
}

function cf7_mergeTag_url_callback($args)
{
    $form_id = esc_attr($args['form_id']);
    $mergeTag = esc_attr(get_option('contact_form_field_url_merge_tag_' . $form_id));
    $selected_option = esc_attr(get_option('contact_form_field_url_merge_type_' . $form_id));
    $fifth = esc_attr(get_option("contact_form_field_date_merge_tag_fifth_{$form_id}"));

    echo "<input type='text' id='contact_form_field_url_merge_tag_{$form_id}' name='contact_form_field_url_merge_tag_{$form_id}' value='$mergeTag' />";
    echo "<select name='type_fifth_{$form_id}' id='type_fifth_{$form_id}'>
              <option value='string' " . selected($selected_option, 'string', false) . ">string</option>
              <option value='date' " . selected($selected_option, 'date', false) . ">date</option>
              <option value='json' " . selected($selected_option, 'json', false) . ">json</option>
              <option value='int' " . selected($selected_option, 'int', false) . ">int</option>
              <option value='url' " . selected($selected_option, 'url', false) . ">url</option>
        </select>";
    echo "<label for='contact_form_field_date_merge_tag_fifth_{$form_id}'>Field name:<input type='text' id='contact_form_field_date_merge_tag_fifth_{$form_id}' name='contact_form_field_date_merge_tag_fifth_{$form_id}' value='$fifth'/></label>";
}

if (isset($_POST['submit']) && $_POST['submit'] == 'Add mergeTag settings') {
    if (isset($_GET['post'])) {
        $form_id = $_GET['post'];
    }
    update_option('contact_form_field_text_merge_tag_' . $form_id, $_POST['contact_form_field_text_merge_tag_' . $form_id]);
    update_option('contact_form_field_text_merge_type_' . $form_id, $_POST['type_first_' . $form_id]);

    update_option('contact_form_field_date_merge_tag_' . $form_id, $_POST['contact_form_field_date_merge_tag_' . $form_id]);
    update_option('contact_form_field_date_merge_type_' . $form_id, $_POST['type_second_' . $form_id]);

    update_option('contact_form_field_json_merge_tag_' . $form_id, $_POST['contact_form_field_json_merge_tag_' . $form_id]);
    update_option('contact_form_field_json_merge_type_' . $form_id, $_POST['type_third_' . $form_id]);

    update_option('contact_form_field_number_merge_tag_' . $form_id, $_POST['contact_form_field_number_merge_tag_' . $form_id]);
    update_option('contact_form_field_number_merge_type_' . $form_id, $_POST['type_fourth_' . $form_id]);

    update_option('contact_form_field_url_merge_tag_' . $form_id, $_POST['contact_form_field_url_merge_tag_' . $form_id]);
    update_option('contact_form_field_url_merge_type_' . $form_id, $_POST['type_fifth_' . $form_id]);

    //Field names:
    update_option('contact_form_field_date_merge_tag_first_' . $form_id, $_POST['contact_form_field_date_merge_tag_first_' . $form_id]);
    update_option('contact_form_field_date_merge_tag_second_' . $form_id, $_POST['contact_form_field_date_merge_tag_second_' . $form_id]);
    update_option('contact_form_field_date_merge_tag_third_' . $form_id, $_POST['contact_form_field_date_merge_tag_third_' . $form_id]);
    update_option('contact_form_field_date_merge_tag_fourth_' . $form_id, $_POST['contact_form_field_date_merge_tag_fourth_' . $form_id]);
    update_option('contact_form_field_date_merge_tag_fifth_' . $form_id, $_POST['contact_form_field_date_merge_tag_fifth_' . $form_id]);

    //    update_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_template_id", $_POST["cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_template_id"]);
    update_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_name", $_POST["cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_name"]);
    update_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email", $_POST["cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email"]);
    update_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_subject", $_POST["cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_subject"]);
    update_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field", $_POST["cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field"]);

    if (isset($_POST['cf7_mergeTag_template_form_id_' . $form_id . '_template_id_1'])) {
        $rows = $_POST['cf7_mergeTag_template_form_id_' . $form_id . '_template_id_1'];
        $rows_array = preg_split('/[, ]+/', $rows);

        // Удаление всех предыдущих значений template_id
        for ($i = 1; $i <= 2000; $i++) { // Здесь 2000 - максимальное значение template_id, которое нужно удалить, можно изменить в соответствии с вашими требованиями
            delete_option('cf7_mergeTag_template_form_id_' . $form_id . "_template_id_{$i}");
            delete_option('cf7_mergeTag_template_form_id_' . $form_id . "_template_id_{$i}_email");
            delete_option('cf7_mergeTag_template_form_id_' . $form_id . "_template_id_{$i}_subject");
        }

        // Запись новых значений template_id
        foreach ($rows_array as $key => $row) {
            $template_id_option = 'cf7_mergeTag_template_form_id_' . $form_id . '_template_id_' . $row;
            update_option($template_id_option, $row);

            $email_field_name = 'cf7_mergeTag_template_form_id_' . $form_id . '_template_id_' . $row . '_email';
            $email = $_POST[$email_field_name];
            update_option($email_field_name, $email);

            $subject_field_name = 'cf7_mergeTag_template_form_id_' . $form_id . '_template_id_' . $row . '_subject';
            $subject = $_POST[$subject_field_name];
            update_option($subject_field_name, $subject);
        }
    }
}

add_filter('wpcf7_editor_panels', function ($panels) {
    $panels['mergetag-panel'] = array(
        'title' => __('MergeTag settings', 'contact-form-7'),
        'callback' => 'cf7_mergeTag_form_fields'
    );
    return $panels;
});

add_action('wpcf7_save_mergetag_form', 'cf7_mergeTag_form_fields');

// Nastavení pluginu
function cf7_mergeTag_form_fields()
{
?>
    <div class="wrap">
        <form method="post" action="">
            <?php
            settings_fields('cf7_mergeTag');
            do_settings_sections('cf7_mergeTag');
            submit_button('Add mergeTag settings');
            ?>
        </form>
    </div>
<?php
}
