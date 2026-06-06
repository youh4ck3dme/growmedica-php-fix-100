<?php

class sendMail {
    /*
     *
     *
     * $toName = meno prijimateľa
     * $sendTo = adresa prijimateľa
     * $subject = predmet správy
     * $attachment = príloha
     * $fromEmail = email odosielateľa
     * $fromEmailName = meno odosielateľa
     * $response = potvrdzujúci mail uživateľovi
     *             $response = array('subject' => '', 'body' => '', 'success_message' => '', 'error_message' => '');
     *
     */

    public static function send($toName, $sendTo, $subject, $body, $attachment = NULL, $fromEmail = NULL, $fromEmailName = NULL, $response = NULL) {

        global $fromAddress, $fromName, $lang, $emailAddress, $cTranslator;

        /*
          if (empty($subject) OR empty($body)) {
          Message::setMessage($cTranslator->getTranslation("Chýba predmet alebo telo správy!"), 0);
          header('Location: ' . $_SERVER['HTTP_REFERER']);
          exit;
          }
         */
        if ($fromEmail == NULL) {
            $fromEmail = $fromAddress;
        }

        if ($fromEmailName == NULL) {
            $fromEmailName = $fromName;
        }
        //
        // ADMIN E-MAIL
        //
        // attachment
        if ($attachment != NULL) {
            if ($attachment['size'] <= MAX_ATTACHMENT_SIZE) {
                //
                $random_hash = md5(date('r', time()));
                $headers = "Content-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
                //
                // príprava súboru
                $file = fopen($attachment['tmp_name'], 'rb');
                $data = fread($file, filesize($attachment['tmp_name']));
                fclose($file);
                $file = chunk_split(base64_encode($data));
                //
                // zistenie mine typu súboru
                $finfo = new finfo(FILEINFO_MIME);
                $type = $finfo->file($attachment['tmp_name']); //change the field_name
                $mime = substr($type, 0, strpos($type, ';'));
                //
                // vytvorenie dočasneho tela správy
                $tmp_body = "--PHP-mixed-$random_hash\r\n" . "Content-Type: multipart/alternative; boundary=\"PHP-alt-$random_hash\"\r\n\r\n";
                $tmp_body .= "--PHP-alt-$random_hash\r\n" . "Content-Type: text/plain; charset=\"utf-8\"\r\n" . "Content-Transfer-Encoding: 7bit\r\n\r\n";

                $tmp_body .= $body;
                $tmp_body .="\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";

                $tmp_body .= "--PHP-mixed-$random_hash\r\n" . "Content-Type: " . $mime . "; name=\"" . $attachment['name'] . "\"\r\n" . "Content-Transfer-Encoding: base64\r\n" . "Content-Disposition: attachment\r\n\r\n";

                $tmp_body .= $file;
                $tmp_body .= "/r/n--PHP-mixed-$random_hash--";
                //
                // prepísanie pôvodného tela správy
                $body = $tmp_body;
            }
        } else {
            $headers = "Content-Type: text/plain; charset=utf-8\r\n";
        }
        $error = array();
        if (!empty($subject) OR ! empty($body)) {
            //
            // headers
            $headers .= "From: " . String::diakritika($toName) . " <" . String::diakritika($sendTo) . ">\r\n";
            $headers .= "Reply-To: " . String::diakritika($toName) . " <" . String::diakritika($sendTo) . ">\r\n";
            $headers .= "X-Mailer: PHP Engine\r\n";
            //
            foreach ($emailAddress as $k => $to):
                if (!_mail($to, $subject . " - " . strtoupper($lang), String::diakritika($body), $headers)) {
                    $error['admin'][] = $v;
                    break;
                }
            endforeach;
            //
        }
        // USER E-MAIL CONFORM
        if (empty($error)) {
            unset($headers);
            $headers = "Content-Type: text/html; charset=utf-8\r\n";
            $headers .= "From: " . String::diakritika($fromEmailName) . " <" . String::diakritika($fromEmail) . ">\r\n";
            $headers .= "Reply-To: " . String::diakritika($fromEmailName) . " <" . String::diakritika($fromEmail) . ">\r\n";
            $headers .= "X-Mailer: PHP Engine\r\n";

            // body
            if ($response == NULL OR ! is_array($response)) {
                $subject = $cTranslator->getTranslation("Potvrdenie odoslania kontaktného formulára", 0);
                $body = getContentByLabel('Potvrdenie odoslania kontaktného formulára', 0);
                $success_message = $cTranslator->getTranslation("Kontaktný formulár bol úspešne odoslaný.", 0);
                $error_message = $cTranslator->getTranslation("Nastala chyba! Kontaktný formulár nebol odoslaný. Prosím skúste znova.", 0);
            } else {

                if (!empty($response['subject'])) {
                    $subject = $response['subject'];
                }

                if (!empty($response['body'])) {
                    $body = $response['body'];
                }

                if (!empty($response['success_message'])) {
                    $success_message = $response['success_message'];
                }

                if (!empty($response['error_message'])) {
                    $error_message = $response['error_message'];
                }
            }

            if (true) {
                Message::setMessage($success_message, 0);
            } else {
                Message::setMessage($error_message, 2);
            }
        } else {
            Message::setMessage($error_message, 2);
        }
    }

}
