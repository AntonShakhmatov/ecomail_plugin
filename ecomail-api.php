<?php

require_once ECOMAIL_CF7_INTEGRATION_DIR . 'table_functions.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'ecomail-contact-form7.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'edit-contact-form.php';
require_once ECOMAIL_CF7_INTEGRATION_DIR . 'second-tab-edit-contact-form.php';

// Připojování k API Ecomailu
class EcomailApi
{
    private $api_key;
    private $api_url = 'https://api2.ecomailapp.cz/';

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    private function sendRequest($url, $request = 'POST', $data = '')
    {

        $http_headers = array();
        $http_headers[] = "key: " . $this->api_key;
        $http_headers[] = "Content-Type: application/json";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            if ($request == 'POST') {
                curl_setopt($ch, CURLOPT_POST, TRUE);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
            }
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    public function addSubscriber($list_id, $data = array(), $trigger_autoresponders = FALSE, $update_existing = TRUE, $resubscribe = TRUE)
    {
        $url = $this->api_url . 'lists/' . $list_id . '/subscribe';
        $post = json_encode(array(
            'subscriber_data' => array(
                'name' => $data['name'],
                'surname' => $data['surname'],
                'email' => $data['email'],
                'vokativ' => $data['vokativ'],
                'vokativ_s' => $data['vokativ_s'],
                'company' => $data['company'],
                'city' => $data['city'],
                'street' => $data['street'],
                'zip' => $data['zip'],
                'country' => $data['country'],
                'phone' => $data['phone'],
                'pretitle' => $data['pretitle'],
                'surtitle' => $data['surtitle'],
                'birthday' => $data['birthday'],
                'custom_fields' => (array)$data['custom_fields'],
            ),
            'tags' => array(
                'tags' => $data['tags'],
            ),
            'trigger_autoresponders' => $trigger_autoresponders,
            'update_existing' => $update_existing,
            'resubscribe' => $resubscribe
        ));

        return $this->sendRequest($url, 'POST', $post);
    }

    public function sendTransactionalEmail($data = array(), $click_tracking = TRUE, $open_tracking = TRUE)
    {
        $url = $this->api_url . 'transactional/send-template';
        $post = json_encode(array(
            'message' => array(
                'template_id' => $data['template_id'],
                'subject' => $data['subject'],
                'from_name' => $data['from_name'],
                'from_email' => $data['from_email'],
                'reply_to' => $data['reply_to'],
                'to' => array(
                    array(
                        'email' => $data['email'],
                        'name' => $data['name'],
                    )
                ),
                'global_merge_vars' => array(
                    array(
                        'NAME' => $data['mergeTagName'],
                        'SURNAME' => $data['mergeTagSurname'],
                        'EMAIL' => $data['mergeTagEmail'],
                        'PHONE' => $data['mergeTagPhone'],
                    )
                ),
                'options' => array(
                    'click_tracking' => $click_tracking,
                    'open_tracking' => $open_tracking
                )
            )
        ));

        return $this->sendRequest($url, 'POST', $post);
    }

    public function sendMergeTagsWithTransactionalEmail($form_id, $data = array(), $click_tracking = TRUE, $open_tracking = TRUE)
    {
        $first = get_option("contact_form_field_text_merge_tag_{$form_id}");
        $second = get_option("contact_form_field_date_merge_tag_{$form_id}");
        $third = get_option("contact_form_field_json_merge_tag_{$form_id}");
        $fourth = get_option("contact_form_field_number_merge_tag_{$form_id}");
        $fifth = get_option("contact_form_field_url_merge_tag_{$form_id}");
        $url = $this->api_url . 'transactional/send-template';
        $post = json_encode(array(
            'message' => array(
                'template_id' => $data['template_id'],
                'subject' => $data['subject'],
                'from_name' => $data['from_name'],
                'from_email' => $data['from_email'],
                'reply_to' => $data['reply_to'],
                //                'text' => $data['text'],
                'html' => $data['html'],
                'to' => array(
                    array(
                        'email' => $data['email'],
                    )
                ),
                'global_merge_vars' => array(
                    array(
                        $first => $data['mergeTagFirst'],
                        $second => $data['mergeTagSecond'],
                        $third => $data['mergeTagThird'],
                        $fourth => $data['mergeTagFourth'],
                        $fifth => $data['mergeTagFifth'],
                    )
                ),
                'options' => array(
                    'click_tracking' => $click_tracking,
                    'open_tracking' => $open_tracking
                )
            )
        ));

        return $this->sendRequest($url, 'POST', $post);
    }

    public function sendRemainingMergeTagsWithTransactionalEmail($form_id, $data = array(), $click_tracking = TRUE, $open_tracking = TRUE)
    {
        $first = get_option("contact_form_field_text_merge_tag_{$form_id}");
        $second = get_option("contact_form_field_date_merge_tag_{$form_id}");
        $third = get_option("contact_form_field_json_merge_tag_{$form_id}");
        $fourth = get_option("contact_form_field_number_merge_tag_{$form_id}");
        $fifth = get_option("contact_form_field_url_merge_tag_{$form_id}");
        $url = $this->api_url . 'transactional/send-template';
        $post = json_encode(array(
            'message' => array(
                'template_id' => $data['template_id'],
                'subject' => $data['subject'],
                'from_name' => $data['from_name'],
                'from_email' => $data['from_email'],
                'reply_to' => $data['reply_to'],
                'html' => $data['html'],
                'to' => array(
                    array(
                        'email' => $data['email'],
                    )
                ),
                'global_merge_vars' => array(
                    array(
                        $first => $data['mergeTagFirst'],
                        $second => $data['mergeTagSecond'],
                        $third => $data['mergeTagThird'],
                        $fourth => $data['mergeTagFourth'],
                        $fifth => $data['mergeTagFifth'],
                    )
                ),
                'options' => array(
                    'click_tracking' => $click_tracking,
                    'open_tracking' => $open_tracking
                )
            )
        ));

        return $this->sendRequest($url, 'POST', $post);
    }
}
