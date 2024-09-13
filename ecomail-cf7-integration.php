<?php
/**
 * Plugin Name: Ecomail CF7 Integration
 * Description: Plugin pro integrace do ecomailu
 * Plugin URI:  Ссылка на страницу плагина
 * Author URI:  https://www.facebook.com/profile.php?id=100005206990497
 * Author:      Anton Shakhmatov
 * Version:     1.0
 *
 * Text Domain: ID перевода, указывается в load_plugin_textdomain()
 * Domain Path: Путь до файла перевода.
 * Requires at least: 2.5
 * Requires PHP: >=7.0
 *
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:     Укажите "true" для возможности активировать плагин для сети Multisite.
 * Update URI: https://example.com/link_to_update
 */

define('ECOMAIL_CF7_INTEGRATION_DIR', plugin_dir_path(__FILE__));

require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-api.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-contact-form7.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'second-tab-edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'third-tab-edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'fourth-tab-edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'fifth-tab-edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'cron_schedule.php';

//Jsou tabulky, které se přidávají při aktivaci pluginu

//subscribers tabulka, ve které jsou zapsáni všichni účastníci ecomailu
function addSubscriber() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  $table_name = $wpdb->prefix . 'subscribers';
  $sql = "CREATE TABLE $table_name (
      subscriber_id INT(11) NOT NULL AUTO_INCREMENT,
      email VARCHAR(255) NOT NULL,
      name VARCHAR(255) NOT NULL,
      surname VARCHAR(255) NOT NULL,
      phone VARCHAR(255) NOT NULL,
      PRIMARY KEY (subscriber_id)
  )$charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}

register_activation_hook( __FILE__, 'addSubscriber' );

//tabulka ktera se vyuziva cronem, bere prvni prvek pole
function addTransactionMailFields() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name_4 = $wpdb->prefix . 'transactional_mail';
    $sql_4 = "CREATE TABLE $table_name_4 (
    transactional_mail_id INT(11) NOT NULL AUTO_INCREMENT,
    template_id INT(11) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    from_email VARCHAR(255) NOT NULL,
    reply_to VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    mergeTagName VARCHAR(255) NOT NULL,
    mergeTagSurname VARCHAR(255) NOT NULL,
    mergeTagEmail VARCHAR(255) NOT NULL,
    mergeTagPhone VARCHAR(255) NOT NULL,
    content VARCHAR(255) NOT NULL,
    PRIMARY KEY (transactional_mail_id)
    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_4 );
}

register_activation_hook( __FILE__, 'addTransactionMailFields' );

//tabulka ktera se vyuziva cronem, bere prvni zbytek pole, spojen s "transactional_mail"
function addSecondTransactionMailFields() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name_5 = $wpdb->prefix . 'transactional_mail_second';
    $sql_5 = "CREATE TABLE $table_name_5 (
    second_transactional_mail_id INT(11) NOT NULL AUTO_INCREMENT,
    template_id INT(11) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    from_email VARCHAR(255) NOT NULL,
    reply_to VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    mergeTagName VARCHAR(255) NOT NULL,
    mergeTagSurname VARCHAR(255) NOT NULL,
    mergeTagEmail VARCHAR(255) NOT NULL,
    mergeTagPhone VARCHAR(255) NOT NULL,
    content VARCHAR(255) NOT NULL,
    PRIMARY KEY (second_transactional_mail_id)
    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_5 );
}

register_activation_hook( __FILE__, 'addSecondTransactionMailFields' );

function addTransactionMailCopyFields() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name_6 = $wpdb->prefix . 'transactional_mail_copy';
    $sql_6 = "CREATE TABLE $table_name_6 (
    transactional_mail_copy_id INT(11) NOT NULL AUTO_INCREMENT,
    template_id INT(11) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    from_email VARCHAR(255) NOT NULL,
    reply_to VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    mergeTagName VARCHAR(255) NOT NULL,
    mergeTagSurname VARCHAR(255) NOT NULL,
    mergeTagEmail VARCHAR(255) NOT NULL,
    mergeTagPhone VARCHAR(255) NOT NULL,
    content VARCHAR(255) NOT NULL,
    PRIMARY KEY (transactional_mail_copy_id)
    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_6 );
}

register_activation_hook( __FILE__, 'addTransactionMailCopyFields' );

//tabulka ktera se vyuziva cronem, bere prvni prvek pole
function addFourthTransactionMailFields() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name_8 = $wpdb->prefix . 'transactional_mail_fourth';
    $sql_8 = "CREATE TABLE $table_name_8 (
    fourth_transactional_mail_id INT(11) NOT NULL AUTO_INCREMENT,
    template_id INT(11) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    from_email VARCHAR(255) NOT NULL,
    reply_to VARCHAR(255) NOT NULL,
    text VARCHAR(255) NOT NULL,
    html VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mergeTagFirst VARCHAR(255) NOT NULL,
    mergeTagSecond VARCHAR(255) NOT NULL,
    mergeTagThird VARCHAR(255) NOT NULL,
    mergeTagFourth VARCHAR(255) NOT NULL,
    mergeTagFifth VARCHAR(255) NOT NULL,
    form_id INT(11) NOT NULL,
    PRIMARY KEY (fourth_transactional_mail_id)
    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_8 );
}

register_activation_hook( __FILE__, 'addFourthTransactionMailFields' );

//tabulka ktera se vyuziva cronem, bere prvni zbytek pole, spojen s "transactional_mail_fourth"
function addTransactionMailThirdFields() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name_7 = $wpdb->prefix . 'transactional_mail_third';
    $sql_7 = "CREATE TABLE $table_name_7 (
    third_transactional_mail_id INT(11) NOT NULL AUTO_INCREMENT,
    template_id INT(11) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    from_email VARCHAR(255) NOT NULL,
    reply_to VARCHAR(255) NOT NULL,
    text VARCHAR(255) NOT NULL,
    html VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mergeTagFirst VARCHAR(255) NOT NULL,
    mergeTagSecond VARCHAR(255) NOT NULL,
    mergeTagThird VARCHAR(255) NOT NULL,
    mergeTagFourth VARCHAR(255) NOT NULL,
    mergeTagFifth VARCHAR(255) NOT NULL,
    form_id INT(11) NOT NULL,
    PRIMARY KEY (third_transactional_mail_id)
    )$charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_7 );
}

register_activation_hook( __FILE__, 'addTransactionMailThirdFields' );
