<?php

namespace Shrub;

use Leaf\Config;
use Leaf\Mail\Mailer;

class FormMailer
{
    /**
     * Handle post data and send to user.
     * @return array
     * @throws \Exception
     */
    public static function processRequest(): array
    {
        $logger = Config::get('log');
        $data = request()->postData();

        $meta = [
            'from' => Config::get("formmail.from"),
            'fromName' => Config::get("formmail.from_name"),
            'to' => Config::get("formmail.to"),
            'toName' => Config::get("formmail.to_name"),
            'subject' => Config::get("formmail.subject"),
            'reply-to' => null,
            'success' => Config::get("formmail.success") ?? $_SERVER['HTTP_REFERER'], // todo: safer way
            'current' => $_SERVER['HTTP_REFERER'], // todo: safer way
            'error' => Config::get("formmail.error"),
            'ignore' => null,
            'required' => null,
        ];

        foreach (array_keys($meta) as $metakey) {
            if (array_key_exists($metakey, $data)) {
                $meta[$metakey] = $data[$metakey];
                unset($data[$metakey]);
            }
        }

        if (empty($meta['from']) || empty($meta['to']) || empty($meta['subject'])) {
            // todo custom exception
            throw new \Exception("Missing required parameter 'from', 'to' or 'subject'.");
        }
        if (empty($meta['fromName'])) {
            $meta['fromName'] = $meta['from'];
        }
        if (empty($meta['toName'])) {
            $meta['toName'] = $meta['to'];
        }
        if (!empty($meta['reply-to'])) {
            $meta['reply-to'] = $data[$meta['reply-to']];
        }

        if (!empty($meta['ignore'])) {
            foreach (explode(',', $meta['ignore']) as $ignore) {
                unset($data[$ignore]);
            }
        }

        $missing = [];
        if (!empty($meta['required'])) {
            foreach (explode(',', $meta['required']) as $required) {
                if (empty($data[$required])) {
                    $missing[] = $required;
                }
            }
        }
        if (!empty($missing)) {
            return [
                'success' => false,
                'redirect' => $meta['current'],
                'missing' => $missing
            ];
        }

        $body = '';
        foreach ($data as $key => $value) {
            $body .= "$key: $value\n";
        }

        $mail = [
            'senderEmail' => $meta['from'],
            'senderName' => $meta['fromName'],
            'recipientEmail' => $meta['to'],
            'recipientName' => $meta['toName'],
            'subject' => $meta['subject'],
            'body' => $body,
            'replyToEmail' => $meta['reply-to'],
        ];

        $connection = false;
        if (Config::has("mailer.smtp.host")) {
            $connection = [
                'host' => Config::get("mailer.smtp.host"),
                'port' => Config::get("mailer.smtp.port"),
                'security' => Config::get("mailer.smtp.security"),
                'debug' => Config::get("mailer.smtp.debug") ?? 0,
            ];
            if (!empty(Config::get("mailer.smtp.user"))) {
                $connection['auth'] = [
                    'username' => Config::get("mailer.smtp.user"),
                    'password' => Config::get("mailer.smtp.password"),
                ];
            }
        }

        // todo: attachments

        $mailer = mailer($mail);
        $mailer->connect($connection);
        $success = $mailer->send();
        if (is_string($success)) {
            $logger->info($success);
            $success = !str_contains($success, 'failed');
        }
        if (is_bool($success) && $success) {
            return [
                'success' => true,
                'redirect' => $meta['success']
            ];
        } else {
            $logger->error('Sending Mail failed.', Mailer::errors());
            return [
                'success' => false,
                'redirect' => $meta['error']
            ];
        }
    }
}