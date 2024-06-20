<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{


    public function send(
        $to_email,
        $to_name,
        $subject,
        $template,
        $vars = null
    ) {
        // recup d'un template
        $content = file_get_contents(dirname(__DIR__) . '/Mail/' . $template);


        // on recup les variables facultativeset on les reconnaitra avec leur clé.
        if ($vars != null) {
            foreach ($vars as $key => $var) {
                $content = str_replace('{' . $key . '}', $var, $content);
            }
        }

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);

        // Define your request body

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "contact@ducompagnon.fr",
                        'Name' => "Le Compagnon de Com"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 6073250,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];

        $mj->post(Resources::$Email, ['body' => $body]);


    }
}