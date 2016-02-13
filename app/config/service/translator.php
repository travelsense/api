<?php
/**
 * Translator service config
 * @var $app Application
 */

$app->register(new \Silex\Provider\TranslationServiceProvider());

$app['translator.domains'] = [
    'email' => [
        'en' => [
            'acct_confirmation' => 'Account confirmation',
            'click_link_to_finish' => 'Please follow the link below to finish registration',
        ]
    ]
];
