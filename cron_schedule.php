<?php
//Add Interval [ Hour ]

//Zde funkce přebírají data z tabulek a funkce "true_moi_interval" naplánují odesílání polí do rozhraní API

add_filter( 'cron_schedules', 'true_moi_interval');

function true_moi_interval( $raspisanie ) {
    $raspisanie[ 'nast_cron' ] = array(
        'interval' => get_option('contact_form_field_email_cron'),
        'display' => 'Každou ' . get_option('contact_form_field_email_cron')/60 . ' minutu' // отображаемое имя
    );
    return $raspisanie;
}

if( ! wp_next_scheduled( 'mycron' ) ) {
    wp_schedule_event( time(), 'nast_cron', 'mycron' );
}

add_action( 'mycron', 'myWpTask');

function myWpTask() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'transactional_mail';
    $fields = $wpdb->get_results("SELECT * FROM `$table_name`");
    foreach ($fields as $field) {
            $api_key = get_option('ecomail_api_key_1');
            $api = new EcomailApi($api_key);
            // Odeslání transakčního e-mailu
            $api->sendTransactionalEmail(array(
                'template_id' => $field->template_id ? $field->template_id: '',
                'subject' => $field->subject ? $field->subject: '',
                'from_name' => $field->from_name ? $field->from_name: '',
                'from_email' => esc_attr(get_option('email_address_field')),
                'reply_to' => $field->reply_to ? $field->reply_to: '',
                'email' => $field->email ? $field->email: '',
                'name' => $field->name ? $field->name: '',
                'mergeTagName' => $field->mergeTagName ? $field->mergeTagName: '',
                'mergeTagSurname' => $field->mergeTagSurname ? $field->mergeTagSurname: '',
                'mergeTagEmail' => $field->mergeTagEmail ? $field->mergeTagEmail: '',
                'mergeTagPhone' => $field->mergeTagPhone ? $field->mergeTagPhone: '',
                'content' => $field->content ? $field->content: ''
            ),
                TRUE, TRUE);
    }
    $wpdb->query("DELETE FROM `$table_name`");
}

// если ещё не запланировано - планируем
if( ! wp_next_scheduled( 'mycron_1' ) ) {
    wp_schedule_event( time(), 'nast_cron', 'mycron_1' );
}

add_action( 'mycron_1', 'myWpSecondTask' );

    function myWpSecondTask()
    {
        global $wpdb;
        $table_name_2 = $wpdb->prefix . 'transactional_mail_second';
        $fields = $wpdb->get_results("SELECT * FROM `$table_name_2`");
        foreach ($fields as $field) {
        $api_key = get_option('ecomail_api_key_1');
        $api = new EcomailApi($api_key);
        $api->sendTransactionalEmail([
            'template_id' => $field->template_id ? $field->template_id: '',
            'subject' => $field->subject ? $field->subject: '',
            'from_name' => $field->from_name ? $field->from_name: '',
            'from_email' => esc_attr(get_option('email_address_field')),
            'reply_to' => $field->reply_to ? $field->reply_to: '',
            'email' => $field->email ? $field->email: '',
            'name' => $field->name ? $field->name: '',
            'mergeTagName' => $field->mergeTagName ? $field->mergeTagName: '',
            'mergeTagSurname' => $field->mergeTagSurname ? $field->mergeTagSurname: '',
            'mergeTagEmail' => $field->mergeTagEmail ? $field->mergeTagEmail: '',
            'mergeTagPhone' => $field->mergeTagPhone ? $field->mergeTagPhone: '',
            'content' => $field->content ? $field->content: ''
        ],
            TRUE, TRUE);
        }
        $wpdb->query("DELETE FROM `$table_name_2`");
    }


// если ещё не запланировано - планируем
if( ! wp_next_scheduled( 'mycron_2' ) ) {
    wp_schedule_event( time(), 'nast_cron', 'mycron_2' );
}

add_action( 'mycron_2', 'myWpThirdTask' );

function myWpThirdTask()
{
    global $wpdb;
    $table_name_3 = $wpdb->prefix . 'transactional_mail_third';
    $fields = $wpdb->get_results("SELECT * FROM `$table_name_3`");
    foreach ($fields as $field) {
        $api_key = get_option('ecomail_api_key_1');
        $api = new EcomailApi($api_key);
        $api->sendRemainingMergeTagsWithTransactionalEmail($field->form_id,[
            'template_id' => $field->template_id ? $field->template_id: '',
            'subject' => $field->subject ? $field->subject: '',
            'from_name' => $field->from_name ? $field->from_name: '',
            'from_email' => $field->from_email ? $field->from_email: '',
            'reply_to' => $field->reply_to ? $field->reply_to: '',
            'html' => $field->html ? $field->html: '',
            'email' => $field->email ? $field->email: '',
            'mergeTagFirst' => $field->mergeTagFirst ? $field->mergeTagFirst: '',
            'mergeTagSecond' => $field->mergeTagSecond ? $field->mergeTagSecond: '',
            'mergeTagThird' => $field->mergeTagThird ? $field->mergeTagThird: '',
            'mergeTagFourth' => $field->mergeTagFourth ? $field->mergeTagFourth: '',
            'mergeTagFifth' => $field->mergeTagFifth ? $field->mergeTagFifth: '',
        ],
            TRUE, TRUE);
    }
    $wpdb->query("DELETE FROM `$table_name_3`");
}


// если ещё не запланировано - планируем
if( ! wp_next_scheduled( 'mycron_3' ) ) {
    wp_schedule_event( time(), 'nast_cron', 'mycron_3' );
}

add_action( 'mycron_3', 'myWpFourthTask' );

function myWpFourthTask()
{
    global $wpdb;
    $table_name_4 = $wpdb->prefix . 'transactional_mail_fourth';
    $fields = $wpdb->get_results("SELECT * FROM `$table_name_4`");
    foreach ($fields as $field) {
        $api_key = get_option('ecomail_api_key_1');
        $api = new EcomailApi($api_key);
        $api->sendMergeTagsWithTransactionalEmail($field->form_id,[
            'template_id' => $field->template_id ? $field->template_id: '',
            'subject' => $field->subject ? $field->subject: '',
            'from_name' => $field->from_name ? $field->from_name: '',
            'from_email' => $field->from_email ? $field->from_email: '',
            'reply_to' => $field->reply_to ? $field->reply_to: '',
            'html' => $field->html ? $field->html: '',
            'email' => $field->email ? $field->email: '',
            'mergeTagFirst' => $field->mergeTagFirst ? $field->mergeTagFirst: '',
            'mergeTagSecond' => $field->mergeTagSecond ? $field->mergeTagSecond: '',
            'mergeTagThird' => $field->mergeTagThird ? $field->mergeTagThird: '',
            'mergeTagFourth' => $field->mergeTagFourth ? $field->mergeTagFourth: '',
            'mergeTagFifth' => $field->mergeTagFifth ? $field->mergeTagFifth: '',
        ],
            TRUE, TRUE);
    }
    $wpdb->query("DELETE FROM `$table_name_4`");
}