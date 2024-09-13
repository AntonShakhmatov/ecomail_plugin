<?php

require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-cf7-integration.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-api.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'cron_schedule.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'second-tab-edit-contact-form.php';

//Práce s formuláři je rozdělena do dvou částí. První je pro odběratele e-mailů, druhý je pro transakční e-maily

// Integrace s CF7
add_action('wpcf7_before_send_mail', 'ecomail_cf7_integration_before_send_mail');

function ecomail_cf7_integration_before_send_mail($cf7) {
    global $wpdb;
    // Získání hodnot polí CF7
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();

        // Získáme nastavení (pro přidání odběratele) pluginu pro tento formulář
        $form_id = $cf7->id();
        $api_key = get_option('ecomail_api_key_1');
        $api = new EcomailApi($api_key);
        $forms = get_posts( array(
            'post_type' => 'wpcf7_contact_form',
            'post_status' => 'publish',
            'numberposts' => -1
        ));

        //Поиск id актуальной формы
        $found = false;
        foreach ( $forms as $form ) {
            $form_id = $form->ID;
            if ($form_id == $cf7->id()) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            echo '$form_id не найден';
        }
        //Prvni čast
        $email_field = get_option('contact_form_field_email_' . $form_id);
        $mail = $posted_data[$email_field];
        // Kontrola přítomnosti e-mailové adresy ve formuláři
        if (isset($mail)){
            //email
            $get_email_field = get_option('contact_form_field_email_' . $form_id);
            $email = $posted_data[$get_email_field];

            //name
            $get_name_field = get_option('contact_form_field_name_' . $form_id);
            $name = $posted_data[$get_name_field];

            //surname
            $get_surname_field = get_option('contact_form_field_surname_' . $form_id);
            $surname = $posted_data[$get_surname_field];

            //phone
            $get_phone_field = get_option('contact_form_field_phone_' . $form_id);
            $phone = $posted_data[$get_phone_field];

            //id нужного списка(вводится в админ панеле, в настройках формы cf7)
            $list_id = get_option('ecomail_list_id_' . $form_id . '_list_id_1');

            //Тэг используется в транзакционных мэйлах(пока не применяется)
            $ecomail_tag = get_option('ecomail_tags');

            for ($i = 1; $i <= 2000; $i++) {
                $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'ecomail_list_id_{$form_id}_template_id_{$i}' ORDER BY option_id ASC");
                if ($array) {
                    foreach($array as $result) {
                        $ecomail_option_ids[] = $result->option_id;
                    }
                }
            }

            // Сортировка данных
            sort($ecomail_option_ids);

            // Вывод отсортированных данных
            foreach ($ecomail_option_ids as $option_id) {
                $template_id[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
            }

            $min_template_id = $template_id[0];
//            $value_with_min_template_id = $template_id[$min_template_id];

            //Массив осташихся шаблонов
            $remaining_template_id = array();

            foreach($template_id as $key => $value) {
                if($key > array_search($min_template_id, $template_id)) {
                    $remaining_template_id[$key] = $value;
                }
            }

            for ($i = 1; $i <= 2000; $i++) {
                $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'ecomail_list_id_{$form_id}_template_id_{$i}_email' ORDER BY option_id ASC");
                if ($array) {
                    foreach($array as $result) {
                        $ecomail_option_email_id[] = $result->option_id;
                    }
                }
            }

            // Сортировка данных
            sort($ecomail_option_email_id);

            // Вывод отсортированных данных
            foreach ($ecomail_option_email_id as $option_id) {
                $admin_emails[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
            }

            $email_min = $admin_emails[0];

            $email_ids = $admin_emails;

            for ($i = 1; $i <= 2000; $i++) {
                $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'ecomail_list_id_{$form_id}_template_id_{$i}_subject' ORDER BY option_id ASC");
                if ($array) {
                    foreach($array as $result) {
                        $ecomail_ids[] = $result->option_id;
                    }
                }
            }

            // Сортировка данных
            sort($ecomail_ids);

            // Вывод отсортированных данных
            foreach ($ecomail_ids as $option_id) {
                $subject_emails[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
            }

            $min_subject_id = $subject_emails[0];

            $subject_ids = $subject_emails;

            //Имя администратора сайта
            $adminName = get_option('sender_name_field');

            //Мэйл администратора сайта
            $adminEmail = get_option('email_address_field');

            $tag_name = get_option("contact_form_field_name_tag_{$form_id}");

            $email_tag = get_option("contact_form_field_email_tag_{$form_id}");

            $phone_tag = get_option("contact_form_field_phone_tag_{$form_id}");

            $content = $tag_name . $email_tag . $phone_tag;

            global $wpdb;
            $table_name = $wpdb->prefix . 'subscribers';
            $result = $wpdb->insert(
                $table_name,
                array(
                    'name' => $name ? $name: '',
                    'surname' => $surname ? $surname: '',
                    'email' => $email ? $email: '',
                    'phone' => $phone ? $phone: ''
                ));

            foreach ($remaining_template_id as $key => $remaining_id) {
                $subject_id = $subject_ids[$key];
                $email_id = $email_ids[$key];
                global $wpdb;
                $table_name_3 = $wpdb->prefix . 'transactional_mail_second';
                $wpdb->insert(
                    $table_name_3,
                    array(
                        'template_id' => $remaining_id ? $remaining_id: '', //добавить поля из базы данных
                        'subject' => $subject_id ? $subject_id: '',
                        'from_name' => $adminName,
                        'from_email' => $adminEmail,
                        'reply_to' => $adminEmail,
                        'email' => $email_id ? $email_id: '',
                        'name' => $adminName ? $adminName: '',
                        'mergeTagName' => $name ? $name: '',
                        'mergeTagSurname' => $surname ? $surname: '',
                        'mergeTagEmail' => $email ? $email: '',
                        'mergeTagPhone' => $phone ? $phone: '',
                        'content' => $name . $email . $phone
                    )
                );
            }

            global $wpdb;
            $table_name_2 = $wpdb->prefix .'transactional_mail';
            $wpdb->insert(
                $table_name_2,
                array(
                    'template_id' => $min_template_id ? $min_template_id: '', //добавить поля из базы данных
                    'subject' => $min_subject_id ? $min_subject_id: '',
                    'from_name' => $adminName,
                    'from_email' => $adminEmail,
                    'reply_to' => $adminEmail,
                    'email' => $email ? $email: '',
                    'name' => $name ? $name: '',
                    'mergeTagName' => $name ? $name: '',
                    'mergeTagSurname' => $surname ? $surname: '',
                    'mergeTagEmail' => $email ? $email: '',
                    'mergeTagPhone' => $phone ? $phone: '',
                    'content' => $name . ' ' . $surname .  ' '  . 'has joined to subscribers' . ' mail is: ' .  $email
                ));

            global $wpdb;
            $table_name_4 = $wpdb->prefix .'transactional_mail_copy';
            $wpdb->insert(
                $table_name_4,
                array(
                    'template_id' => $min_template_id ? $min_template_id: '', //добавить поля из базы данных
                    'subject' => $min_subject_id ? $min_subject_id: '',
                    'from_name' => $adminName,
                    'from_email' => $adminEmail,
                    'reply_to' => $adminEmail,
                    'email' => $email ? $email: '',
                    'name' => $name ? $name: '',
                    'mergeTagName' => $name ? $name: '',
                    'mergeTagSurname' => $surname ? $surname: '',
                    'mergeTagEmail' => $email ? $email: '',
                    'mergeTagPhone' => $phone ? $phone: '',
                    'content' => $name . $email . $phone
                ));


            // Pokud se uložení kontaktu v Ecomail nezdařilo, uloží jej do databáze
            if (!$result) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'cfdb7';

                $form_data = $wpdb->get_row("SELECT * FROM $table_name WHERE form_post_id = $form_id ORDER BY id DESC LIMIT 1");

                // Ukládání kontaktních údajů do databáze
                $wpdb->insert(
                    $table_name,
                    array(
                        'form_post_id' => $form_id,
                        'form_value' => serialize($posted_data),
                        'form_date' => current_time('mysql')
                    )
                );
            } else {
                //names of fields
                $mergeTagText = get_option('contact_form_field_text_merge_tag_' . "$form_id");
                $mergeTagDate = get_option('contact_form_field_date_merge_tag_' . "$form_id");
                $mergeTagJson = get_option('contact_form_field_json_merge_tag_' . "$form_id");
                $mergeTagNumber = get_option('contact_form_field_number_merge_tag_' . "$form_id");
                $mergeTagDateUrl = get_option('contact_form_field_url_merge_tag_' . "$form_id");
                //first
                $get_first_field = get_option('contact_form_field_date_merge_tag_first_' . $form_id);
                $first = $posted_data[$get_first_field];
                //second
                $get_second_field = get_option('contact_form_field_date_merge_tag_second_' . $form_id);
                $second = $posted_data[$get_second_field];
                //third
                $get_third_field = get_option('contact_form_field_date_merge_tag_third_' . $form_id);
                $third = $posted_data[$get_third_field];
                //fourth
                $get_fourth_field = get_option('contact_form_field_date_merge_tag_fourth_' . $form_id);
                $fourth = $posted_data[$get_fourth_field];
                //fifth
                $get_fifth_field = get_option('contact_form_field_date_merge_tag_fifth_' . $form_id);
                $fifth = $posted_data[$get_fifth_field];

                //names of types
                $mergeTagTextType = get_option('contact_form_field_text_merge_type_' . "$form_id");
                $mergeTagDateType = get_option('contact_form_field_date_merge_type_' . "$form_id");
                $mergeTagJsonType = get_option('contact_form_field_json_merge_type_' . "$form_id");
                $mergeTagNumberType = get_option('contact_form_field_number_merge_type_' . "$form_id");
                $mergeTagDateUrlType = get_option('contact_form_field_url_merge_type_' . "$form_id");
                // Přidávání subscriberu
                $api->addSubscriber($list_id, array(
                    'email' => $email,
                    'name' => $name,
                    'surname' => $surname,
                    'phone' => $phone,
                    'tags' => $content,
                    'custom_fields' => array(
                        $mergeTagText => array(
                            'value' => $first,
                            'type' => $mergeTagTextType
                        ),
                        $mergeTagDate => array(
                            'value' => $second,
                            'type' => $mergeTagDateType
                        ),
                        $mergeTagJson => array(
                            'value' => $third,
                            'type' => $mergeTagJsonType
                        ),
                        $mergeTagNumber => array(
                            'value' => $fourth,
                            'type' => $mergeTagNumberType
                        ),
                        $mergeTagDateUrl => array(
                            'value' => $fifth,
                            'type' => $mergeTagDateUrlType
                        )
                    ),
                    FALSE, TRUE, TRUE
                ));
            }
        }

        ////Druha čast
        $email_field_2 = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field");
        $mail2 = $posted_data[$email_field_2];
        // Kontrola přítomnosti e-mailové adresy ve formuláři
        if (isset($mail2)){
        global $wpdb;
        $fromName = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_name");
        $fromEmail = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email");

        $email_field_name = get_option("cf7_mergeTag_form_id_{$form_id}_transactional_mail_form_email_field");
        $field_name_for_email = $posted_data[$email_field_name];
        //first
        $first_field = get_option('contact_form_field_date_merge_tag_first_' . $form_id);
        $first = $posted_data[$first_field];
        //second
        $second_field = get_option('contact_form_field_date_merge_tag_second_' . $form_id);
        $second = $posted_data[$second_field];
        //third
        $third_field = get_option('contact_form_field_date_merge_tag_third_' . $form_id);
        $third = $posted_data[$third_field];
        //fourth
        $fourth_field = get_option('contact_form_field_date_merge_tag_fourth_' . $form_id);
        $fourth = $posted_data[$fourth_field];
        //fifth
        $fifth_field = get_option('contact_form_field_date_merge_tag_fifth_' . $form_id);
        $fifth = $posted_data[$fifth_field];

        for ($i = 1; $i <= 2000; $i++) {
            $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'cf7_mergeTag_template_form_id_{$form_id}_template_id_{$i}' ORDER BY option_id ASC");
            if ($array) {
               foreach($array as $result) {
                   $option_ids[] = $result->option_id;
               }
            }
        }

        // Сортировка данных
        sort($option_ids);

        // Вывод отсортированных данных
        foreach ($option_ids as $option_id) {
            $template_id[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
        }

        $min_id = $template_id[0];

        //Массив осташихся шаблонов
        $remaining_ids = array();

        foreach($template_id as $key => $value) {
           if($key > array_search($min_id, $template_id)) {
              $remaining_ids[$key] = $value;
           }
        }

        for ($i = 1; $i <= 2000; $i++) {
             $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'cf7_mergeTag_template_form_id_{$form_id}_template_id_{$i}_email' ORDER BY option_id ASC");
             if ($array) {
                 foreach($array as $result) {
                        $option_email_id[] = $result->option_id;
                 }
             }
        }

        // Сортировка данных
        sort($option_email_id);

        // Вывод отсортированных данных
        foreach ($option_email_id as $option_id) {
            $admin_emails[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
        }

        $email_min = $admin_emails[0];

        $email_ids = $admin_emails;

        for ($i = 1; $i <= 2000; $i++) {
             $array = $wpdb->get_results("SELECT option_id FROM {$wpdb->prefix}options WHERE option_name = 'cf7_mergeTag_template_form_id_{$form_id}_template_id_{$i}_subject' ORDER BY option_id ASC");
             if ($array) {
                 foreach($array as $result) {
                        $ids[] = $result->option_id;
                 }
             }
        }

        // Сортировка данных
        sort($ids);

            // Вывод отсортированных данных
        foreach ($ids as $option_id) {
            $subjects_emails[] = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_id = $option_id");
        }

        $subjects_min = $subjects_emails[0];

        $subject_ids = $subjects_emails;

        foreach ($remaining_ids as $key => $remaining_id) {
            $subject_id = $subject_ids[$key];// Используем ключи для получения соответствующего email_id
            $email_id = $email_ids[$key];
            $table_name_5 = $wpdb->prefix . 'transactional_mail_third';
            $wpdb->insert(
                $table_name_5,
                array(
                    'template_id' => $remaining_id ? $remaining_id : '',
                    'subject' => $subject_id ? $subject_id : '',
                    'from_name' => $fromName ? $fromName : '',
                    'from_email' => $fromEmail ? $fromEmail : '',
                    'reply_to' => $fromEmail ? $fromEmail : '',
                    'html' => '<b>Email HTML content</b>',
                    'email' => $email_id ? $email_id : '',
                    'mergeTagFirst' => $first ? $first : '',
                    'mergeTagSecond' => $second ? $second : '',
                    'mergeTagThird' => $third ? $third : '',
                    'mergeTagFourth' => $fourth ? $fourth : '',
                    'mergeTagFifth' => $fifth ? $fifth : '',
                    'form_id' => $form_id ? $form_id : ''
                ));
        }

        $table_name_6 = $wpdb->prefix .'transactional_mail_fourth';
            $wpdb->insert(
            $table_name_6,
            array(
                    'template_id' => $min_id ? $min_id: '',
                    'subject' => $subjects_min ? $subjects_min: '',
                    'from_name' => $fromName ? $fromName: '',
                    'from_email' => $fromEmail ? $fromEmail: '',
                    'reply_to' => $fromEmail ? $fromEmail: '',
                    'html' => '<b>Email HTML content</b>',
                    'email' => $field_name_for_email ? $field_name_for_email: '',
                    'mergeTagFirst' => $first ? $first: '',
                    'mergeTagSecond' => $second ? $second: '',
                    'mergeTagThird' => $third ? $third: '',
                    'mergeTagFourth' => $fourth ? $fourth: '',
                    'mergeTagFifth' => $fifth ? $fifth: '',
                    'form_id' => $form_id ? $form_id: ''
            ));
        }
    }
}