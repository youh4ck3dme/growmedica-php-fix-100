<?php

class email_message_class {
    /* Private variables */

    var $headers = array("To" => "", "Subject" => "");
    var $body = -1;
    var $body_parts = 0;
    var $parts = array();
    var $total_parts = 0;
    var $free_parts = array();
    var $total_free_parts = 0;
    var $delivery = array("State" => "");
    var $next_token = "";
    var $php_version = 0;
    var $mailings = array();
    var $last_mailing = 0;
    var $header_length_limit = 512;
    var $auto_message_id = 1;
    var $mailing_path = "";
    var $body_cache = array();
    var $line_break = "\n";
    var $line_length = 75;
    var $email_address_pattern = "([-!#\$%&'*+./0-9=?A-Z^_`a-z{|}~])+@([-!#\$%&'*+/0-9=?A-Z^_`a-z{|}~]+\\.)+[a-zA-Z]{2,6}";
    var $bulk_mail = 0;

    /* Public variables */
    var $email_regular_expression = "^([-!#\$%&'*+./0-9=?A-Z^_`a-z{|}~])+@([-!#\$%&'*+/0-9=?A-Z^_`a-z{|}~]+\\.)+[a-zA-Z]{2,6}\$";
    var $mailer = 'http://www.phpclasses.org/mimemessage $Revision: 1.56 $';
    var $mailer_delivery = 'mail';
    var $default_charset = "ISO-8859-2";
    var $line_quote_prefix = "> ";
    var $file_buffer_length = 8000;
    var $debug = "";
    var $cache_body = 0;
    var $error = "";

    /* Private methods */

    Function Tokenize($string, $separator = "") {
        if (!strcmp($separator, "")) {
            $separator = $string;
            $string = $this->next_token;
        }
        for ($character = 0; $character < strlen($separator); $character++) {
            if (GetType($position = strpos($string, $separator[$character])) == "integer")
                $found = (IsSet($found) ? min($found, $position) : $position);
        }
        if (IsSet($found)) {
            $this->next_token = substr($string, $found + 1);
            return(substr($string, 0, $found));
        } else {
            $this->next_token = "";
            return($string);
        }
    }

    Function GetFilenameExtension($filename) {
        return(GetType($dot = strrpos($filename, ".")) == "integer" ? substr($filename, $dot) : "");
    }

    Function OutputError($error) {
        if (strcmp($function = $this->debug, "") && strcmp($error, ""))
            $function($error);
        return($this->error = $error);
    }

    Function OutputPHPError($error, &$php_error_message) {
        if (IsSet($php_error_message) && strlen($php_error_message))
            $error.=": " . $php_error_message;
        return($this->OutputError($error));
    }

    Function GetPHPVersion() {
        if ($this->php_version == 0) {
            $version = explode(".", function_exists("phpversion") ? phpversion() : "3.0.7");
            $this->php_version = $version[0] * 1000000 + $version[1] * 1000 + $version[2];
        }
        return($this->php_version);
    }

    Function GetRFC822Addresses($address, &$addresses) {
        if (function_exists("imap_rfc822_parse_adrlist")) {
            if (GetType($parsed_addresses = @imap_rfc822_parse_adrlist($address, $this->localhost)) != "array")
                return("it was not specified a valid address list");
            for ($entry = 0; $entry < count($parsed_addresses); $entry++) {
                if ($parsed_addresses[$entry]->host == ".SYNTAX-ERROR.")
                    return($parsed_addresses[$entry]->mailbox . " " . $parsed_addresses[$entry]->host);
                $parsed_address = $parsed_addresses[$entry]->mailbox . "@" . $parsed_addresses[$entry]->host;
                if (IsSet($addresses[$parsed_address]))
                    $addresses[$parsed_address] ++;
                else
                    $addresses[$parsed_address] = 1;
            }
        }
        else {
            $length = strlen($address);
            for ($position = 0; $position < $length;) {
                $match = split($this->email_address_pattern, strtolower(substr($address, $position)), 2);
                if (count($match) < 2)
                    break;
                $position+=strlen($match[0]);
                $next_position = $length - strlen($match[1]);
                $found = substr($address, $position, $next_position - $position);
                if (!strcmp($found, ""))
                    break;
                if (IsSet($addresses[$found]))
                    $addresses[$found] ++;
                else
                    $addresses[$found] = 1;
                $position = $next_position;
            }
        }
        return("");
    }

    Function FormatHeader($header_name, $header_value) {
        $length = strlen($header_value);
        for ($header_data = "", $header_line = $header_name . ": ", $line_length = strlen($header_line), $position = 0; $position < $length;) {
            for ($space = $position, $line_length = strlen($header_line); $space < $length;) {
                if (GetType($next = strpos($header_value, " ", $space + 1)) != "integer")
                    $next = $length;
                if ($next - $position + $line_length > $this->header_length_limit) {
                    if ($space == $position)
                        $space = $next;
                    break;
                }
                $space = $next;
            }
            $header_data.=$header_line . substr($header_value, $position, $space - $position);
            if ($space < $length)
                $header_line = "";
            $position = $space;
            if ($position < $length)
                $header_data.=$this->line_break;
        }
        return($header_data);
    }

    Function GenerateMessageID($sender) {
        $micros = $this->Tokenize(microtime(), " ");
        $seconds = $this->Tokenize("");
        $local = $this->Tokenize($sender, "@");
        $host = $this->Tokenize(" @");
        if ($host[strlen($host) - 1] == "-")
            $host = substr($host, 0, strlen($host) - 1);
        return($this->FormatHeader("Message-ID", "<" . strftime("%Y%m%d%H%M%S", $seconds) . substr($micros, 1, 5) . "." . preg_replace("[^A-Za-z]", "-", $local) . "@" . $host . ">"));
    }

    Function SendMail($to, $subject, &$body, &$headers, $return_path) {

        if (!function_exists("mail"))
            return($this->OutputError("the mail() function is not available in this PHP installation"));

        $unsubscribe_mail = explode(';', $to);
        $body = str_replace('{_-=unsubscribe-mail=-_}', sha1('EmailHash', $unsubscribe_mail[0]), $body);

        if (strlen($return_path)) {
            if (!defined("PHP_OS"))
                return($this->OutputError("it is not possible to set the Return-Path header with your PHP version"));
            if (!strcmp(substr(PHP_OS, 0, 3), "WIN"))
                return($this->OutputError("it is not possible to set the Return-Path header directly from a PHP script on Windows"));
            if ($this->GetPHPVersion() < 4000005)
                return($this->OutputError("it is not possible to set the Return-Path header in PHP version older than 4.0.5"));
            if (function_exists("ini_get") && ini_get("safe_mode"))
                return($this->OutputError("it is not possible to set the Return-Path header due to PHP safe mode restrictions"));
            $success = @_mail($to, $subject, $body, $headers, "-f" . $return_path);
        } else {
            $success = @_mail($to, $subject, $body, $headers);
        }
        return($success ? "" : $this->OutputPHPError("it was not possible to send e-mail message", $php_errormsg));
    }

    Function StartSendingMessage() {
        if (strcmp($this->delivery["State"], ""))
            return($this->OutputError("the message was already started to be sent"));
        $this->delivery = array("State" => "SendingHeaders");
        return("");
    }

    Function SendMessageHeaders($headers) {
        if (strcmp($this->delivery["State"], "SendingHeaders")) {
            if (!strcmp($this->delivery["State"], ""))
                return($this->OutputError("the message was not yet started to be sent"));
            else
                return($this->OutputError("the message headers were already sent"));
        }
        $this->delivery["Headers"] = $headers;
        $this->delivery["State"] = "SendingBody";
        return("");
    }

    Function SendMessageBody(&$data) {
        if (strcmp($this->delivery["State"], "SendingBody"))
            return($this->OutputError("the message headers were not yet sent"));
        if (IsSet($this->delivery["Body"]))
            $this->delivery["Body"].=$data;
        else
            $this->delivery["Body"] = $data;
        return("");
    }

    Function EndSendingMessage() {
        if (strcmp($this->delivery["State"], "SendingBody"))
            return($this->OutputError("the message body data was not yet sent"));
        if (!IsSet($this->delivery["Headers"]) || count($this->delivery["Headers"]) == 0)
            return($this->OutputError("message has no headers"));
        $line_break = ((defined("PHP_OS") && !strcmp(substr(PHP_OS, 0, 3), "WIN")) ? "\r\n" : $this->line_break);
        $headers = $this->delivery["Headers"];
        for ($has = array(), $headers_text = "", $header = 0, Reset($headers); $header < count($headers); Next($headers), $header++) {
            $header_name = Key($headers);
            switch (strtolower($header_name)) {
                case "to":
                case "subject":
                    $has[strtolower($header_name)] = $headers[$header_name];
                    break;
                case "from":
                case "return-path":
                case "message-id":
                    $has[strtolower($header_name)] = $headers[$header_name];
                default:
                    $header_line = $header_name . ": " . $headers[$header_name];
                    if (strlen($headers_text))
                        $headers_text.=$this->line_break . $header_line;
                    else
                        $headers_text = $header_line;
            }
        }
        if (!IsSet($has["to"]))
            return($this->OutputError("it was not specified a valid To: header"));
        if (!IsSet($has["subject"]))
            return($this->OutputError("it was not specified a valid Subject: header"));
        if (!IsSet($has["message_id"]) && $this->auto_message_id) {
            $sender = (IsSet($has["return-path"]) ? $has["return-path"] : (IsSet($has["from"]) ? $has["from"] : $has["to"]));
            $header_line = $this->GenerateMessageID($sender);
            if (strlen($headers_text))
                $headers_text.=$this->line_break . $header_line;
            else
                $headers_text = $header_line;
        }
        if (strcmp($error = $this->SendMail($has["to"], $has["subject"], $this->delivery["Body"], $headers_text, IsSet($has["return-path"]) ? $has["return-path"] : ""), ""))
            return($error);
        $this->delivery = array("State" => "");
        return("");
    }

    Function StopSendingMessage() {
        $this->delivery = array("State" => "");
        return("");
    }

    Function GetPartBoundary($part) {
        if (!IsSet($this->parts[$part]["BOUNDARY"]))
            $this->parts[$part]["BOUNDARY"] = md5(uniqid($part . time()));
    }

    Function GetPartHeaders(&$headers, $part) {
        if (!IsSet($this->parts[$part]["Content-Type"]))
            return($this->OutputError("it was added a part without Content-Type: defined"));
        $type = $this->Tokenize($full_type = strtolower($this->parts[$part]["Content-Type"]), "/");
        $sub_type = $this->Tokenize("");
        switch ($type) {
            case "text":
            case "image":
            case "audio":
            case "video":
            case "application":
            case "message":
                $headers["Content-Type"] = $full_type . (IsSet($this->parts[$part]["CHARSET"]) ? "; charset=" . $this->parts[$part]["CHARSET"] : "") . (IsSet($this->parts[$part]["NAME"]) ? "; name=\"" . $this->parts[$part]["NAME"] . "\"" : "");
                if (IsSet($this->parts[$part]["Content-Transfer-Encoding"]))
                    $headers["Content-Transfer-Encoding"] = $this->parts[$part]["Content-Transfer-Encoding"];
                if (IsSet($this->parts[$part]["DISPOSITION"]) && strlen($this->parts[$part]["DISPOSITION"]))
                    $headers["Content-Disposition"] = $this->parts[$part]["DISPOSITION"] . (IsSet($this->parts[$part]["NAME"]) ? "; filename=\"" . $this->parts[$part]["NAME"] . "\"" : "");
                break;
            case "multipart":
                switch ($sub_type) {
                    case "alternative":
                    case "related":
                    case "mixed":
                    case "parallel":
                        $this->GetPartBoundary($part);
                        $headers["Content-Type"] = $full_type . "; boundary=\"" . $this->parts[$part]["BOUNDARY"] . "\"";
                        break;
                    default:
                        return($this->OutputError("multipart Content-Type sub_type $sub_type not yet supported"));
                }
                break;
            default:
                return($this->OutputError("Content-Type: $full_type not yet supported"));
        }
        if (IsSet($this->parts[$part]["Content-ID"]))
            $headers["Content-ID"] = "<" . $this->parts[$part]["Content-ID"] . ">";
        return("");
    }

    Function GetPartBody(&$body, $part) {
        if (!IsSet($this->parts[$part]["Content-Type"]))
            return($this->OutputError("it was added a part without Content-Type: defined"));
        $type = $this->Tokenize($full_type = strtolower($this->parts[$part]["Content-Type"]), "/");
        $sub_type = $this->Tokenize("");
        $body = "";
        switch ($type) {
            case "text":
            case "image":
            case "audio":
            case "video":
            case "application":
            case "message":
                if (IsSet($this->parts[$part]["FILENAME"])) {
                    $size = @filesize($this->parts[$part]["FILENAME"]);
                    if (!($file = @fopen($this->parts[$part]["FILENAME"], "rb")))
                        return($this->OutputPHPError("could not open part file " . $this->parts[$part]["FILENAME"], $php_errormsg));
                    while (!feof($file)) {
                        if (GetType($block = @fread($file, $this->file_buffer_length)) != "string") {
                            fclose($file);
                            return($this->OutputPHPError("could not read part file", $php_errormsg));
                        }
                        $body.=$block;
                    }
                    fclose($file);
                    if (GetType($size) == "integer" && strlen($body) != $size)
                        return($this->OutputError("the length of the file that was read does not match the size of the part file " . $this->parts[$part]["FILENAME"] . " due to possible data corruption"));
                    if (function_exists("ini_get") && ini_get("magic_quotes_runtime"))
                        $body = StripSlashes($body);
                }
                else {
                    if (!IsSet($this->parts[$part]["DATA"]))
                        return($this->OutputError("it was added a part without a body PART"));
                    $body = $this->parts[$part]["DATA"];
                }
                $encoding = (IsSet($this->parts[$part]["Content-Transfer-Encoding"]) ? strtolower($this->parts[$part]["Content-Transfer-Encoding"]) : "");
                switch ($encoding) {
                    case "base64":
                        $body = chunk_split(base64_encode($body));
                        break;
                    case "":
                    case "quoted-printable":
                    case "7bit":
                        break;
                    default:
                        return($this->OutputError($encoding . " is not yet a supported encoding type"));
                }
                break;
            case "multipart":
                switch ($sub_type) {
                    case "alternative":
                    case "related":
                    case "mixed":
                    case "parallel":
                        $this->GetPartBoundary($part);
                        $boundary = $this->line_break . "--" . $this->parts[$part]["BOUNDARY"];
                        $parts = count($this->parts[$part]["PARTS"]);
                        for ($multipart = 0; $multipart < $parts; $multipart++) {
                            $body.=$boundary . $this->line_break;
                            $part_headers = array();
                            $sub_part = $this->parts[$part]["PARTS"][$multipart];
                            if (strlen($error = $this->GetPartHeaders($part_headers, $sub_part)))
                                return($error);
                            for ($part_header = 0, Reset($part_headers); $part_header < count($part_headers); $part_header++, Next($part_headers)) {
                                $header = Key($part_headers);
                                $body.=$header . ": " . $part_headers[$header] . $this->line_break;
                            }
                            $body.=$this->line_break;
                            if (strlen($error = $this->GetPartBody($part_body, $sub_part)))
                                return($error);
                            $body.=$part_body;
                        }
                        $body.=$boundary . "--" . $this->line_break;
                        break;
                    default:
                        return($this->OutputError("multipart Content-Type sub_type $sub_type not yet supported"));
                }
                break;
            default:
                return($this->OutputError("Content-Type: $full_type not yet supported"));
        }
        return("");
    }

    /* Public functions */

    Function ValidateEmailAddress($address) {
        return(eregi($this->email_regular_expression, $address));
    }

    Function QuotedPrintableEncode($text, $header_charset = "", $break_lines = 1) {
        $ln = strlen($text);
        $h = (strlen($header_charset) > 0);
        if ($h) {
            $break_lines = 0;
            for ($i = 0; $i < $ln; $i++) {
                switch ($text[$i]) {
                    case "=":
                    case "?":
                    case "_":
                    case "(":
                    case ")":
                        break 2;
                    default:
                        $o = Ord($text[$i]);
                        if ($o < 32 || $o > 127)
                            break 2;
                }
            }
            if ($i > 0)
                return(substr($text, 0, $i) . $this->QuotedPrintableEncode(substr($text, $i), $header_charset, 0));
        }
        for ($w = $e = "", $l = 0, $i = 0; $i < $ln; $i++) {
            $c = $text[$i];
            $o = Ord($c);
            $en = 0;
            switch ($o) {
                case 9:
                case 32:
                    if (!$h) {
                        $w = $c;
                        $c = "";
                    } else {
                        if ($o == 32)
                            $c = "_";
                        else
                            $en = 1;
                    }
                    break;
                case 10:
                case 13:
                    if (strlen($w)) {
                        if ($break_lines && $l + 3 > 75) {
                            $e.="=" . $this->line_break;
                            $l = 0;
                        }
                        $e.=sprintf("=%02X", Ord($w));
                        $l+=3;
                        $w = "";
                    }
                    $e.=$c;
                    $l = 0;
                    continue 2;
                default:
                    if ($o > 127 || $o < 32 || !strcmp($c, "=") || ($h && (!strcmp($c, "?") || !strcmp($c, "_") || !strcmp($c, "(") || !strcmp($c, ")"))))
                        $en = 1;
                    break;
            }
            if (strlen($w)) {
                if ($break_lines && $l + 1 > 75) {
                    $e.="=" . $this->line_break;
                    $l = 0;
                }
                $e.=$w;
                $l++;
                $w = "";
            }
            if (strlen($c)) {
                if ($en) {
                    $c = sprintf("=%02X", $o);
                    $el = 3;
                } else
                    $el = 1;
                if ($break_lines && $l + $el > 75) {
                    $e.="=" . $this->line_break;
                    $l = 0;
                }
                $e.=$c;
                $l+=$el;
            }
        }
        if (strlen($w)) {
            if ($break_lines && $l + 3 > 75)
                $e.="=" . $this->line_break;
            $e.=sprintf("=%02X", Ord($w));
        }
        if ($h && strcmp($text, $e))
            return("=?$header_charset?q?$e?=");
        else
            return($e);
    }

    Function WrapText($text, $line_length = 0, $line_break = "", $line_prefix = "") {
        if (strlen($line_break) == 0)
            $line_break = $this->line_break;
        if ($line_length == 0)
            $line_length = $this->line_length;
        $lines = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $text)));
        for ($wrapped = "", $line = 0; $line < count($lines); $line++) {
            if (strlen($text_line = $lines[$line])) {
                for (; strlen($text_line = $line_prefix . $text_line) > $line_length;) {
                    if (GetType($cut = strrpos(substr($text_line, 0, $line_length), " ")) != "integer") {
                        $wrapped.=substr($text_line, 0, $line_length) . $line_break;
                        $cut = $line_length;
                    } else {
                        $wrapped.=substr($text_line, 0, $cut) . $line_break;
                        $cut++;
                    }
                    $text_line = substr($text_line, $cut);
                }
            }
            $wrapped.=$text_line . $line_break;
        }
        return($wrapped);
    }

    Function QuoteText($text, $quote_prefix = "") {
        if (strlen($quote_prefix) == 0)
            $quote_prefix = $this->line_quote_prefix;
        return($this->WrapText($text, $line_length = 0, $line_break = "", $quote_prefix));
    }

    Function SetHeader($header, $value, $encoding_charset = "") {
        if (strlen($this->error))
            return($this->error);
        $this->headers["$header"] = (!strcmp($encoding_charset, "") ? "$value" : $this->QuotedPrintableEncode($value, $encoding_charset));
        return("");
    }

    Function SetEncodedHeader($header, $value) {
        return($this->SetHeader($header, $value, $this->default_charset));
    }

    Function SetEncodedEmailHeader($header, $address, $name) {
        return($this->SetHeader($header, $address . " (" . $this->QuotedPrintableEncode($name, $this->default_charset) . ")"));
    }

    Function SetMultipleEncodedEmailHeader($header, $addresses) {
        Reset($addresses);
        $end = (GetType($address = Key($addresses)) != "string");
        for ($value = ""; !$end;) {
            if (strlen($value))
                $value.=", ";
            $value.=$address . " (" . $this->QuotedPrintableEncode($addresses[$address], $this->default_charset) . ")";
            Next($addresses);
            $end = (GetType($address = Key($addresses)) != "string");
        }
        var_dump($header . ": " . $value);
        return($this->SetHeader($header, $value));
    }

    Function ResetMessage() {
        $this->headers = array();
        $this->body = -1;
        $this->body_parts = 0;
        $this->parts = array();
        $this->total_parts = 0;
        $this->free_parts = array();
        $this->total_free_parts = 0;
        $this->delivery = array("State" => "");
        $this->error = "";
    }

    Function CreatePart(&$definition, &$part) {
        $part = -1;
        if (strlen($this->error))
            return($this->error);
        if ($this->total_free_parts) {
            $this->total_free_parts--;
            $part = $this->free_parts[$this->total_free_parts];
            Unset($this->free_parts[$this->total_free_parts]);
        } else {
            $part = $this->total_parts;
            $this->total_parts++;
        }
        $this->parts[$part] = $definition;
        return("");
    }

    Function AddPart($part) {
        if (strlen($this->error))
            return($this->error);
        switch ($this->body_parts) {
            case 0;
                $this->body = $part;
                break;
            case 1:
                $parts = array(
                    $this->body,
                    $part
                );
                if (strlen($error = $this->CreateMixedMultipart($parts, $body)))
                    return($error);
                $this->body = $body;
                break;
            default:
                $this->parts[$this->body]["PARTS"][] = $part;
                break;
        }
        $this->body_parts++;
        return("");
    }

    Function ReplacePart($old_part, $new_part) {
        $this->parts[$old_part] = $this->parts[$new_part];
        $this->free_parts[$this->total_free_parts] = $new_part;
        $this->total_free_parts++;
        return("");
    }

    Function CreateAndAddPart(&$definition) {
        if (strlen($error = $this->CreatePart($definition, $part)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreatePlainTextPart($text, $charset, &$part) {
        if (!strcmp($charset, ""))
            $charset = $this->default_charset;
        $definition = array(
            "Content-Type" => "text/plain",
            "DATA" => $text
        );
        if (strcmp(strtoupper($charset), "ASCII"))
            $definition["CHARSET"] = $charset;
        return($this->CreatePart($definition, $part));
    }

    Function AddPlainTextPart($text, $charset = "") {
        if (strlen($error = $this->CreatePlainTextPart($text, $charset, $part)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreateEncodedQuotedPrintableTextPart($text, $charset, &$part) {
        if (!strcmp($charset, ""))
            $charset = $this->default_charset;
        $definition = array(
            "Content-Type" => "text/plain",
            "Content-Transfer-Encoding" => "quoted-printable",
            "CHARSET" => $charset,
            "DATA" => $text
        );
        return($this->CreatePart($definition, $part));
    }

    Function AddEncodedQuotedPrintableTextPart($text, $charset = "") {
        if (strlen($error = $this->CreateEncodedQuotedPrintableTextPart($text, $charset, $part)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreateQuotedPrintableTextPart($text, $charset, &$part) {
        return($this->CreateEncodedQuotedPrintableTextPart($this->QuotedPrintableEncode($text), $charset, $part));
    }

    Function AddQuotedPrintableTextPart($text, $charset = "") {
        return($this->AddEncodedQuotedPrintableTextPart($this->QuotedPrintableEncode($text), $charset));
    }

    Function CreateHTMLPart($html, $charset, &$part) {
        if (!strcmp($charset, ""))
            $charset = $this->default_charset;
        $definition = array(
            "Content-Type" => "text/html",
            "CHARSET" => $charset,
            "DATA" => $html
        );
        return($this->CreatePart($definition, $part));
    }

    Function AddHTMLPart($html, $charset = "") {
        if (strlen($error = $this->CreateHTMLPart($html, $charset, $part)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreateEncodedQuotedPrintableHTMLPart($html, $charset, &$part) {
        if (!strcmp($charset, ""))
            $charset = $this->default_charset;
        $definition = array(
            "Content-Type" => "text/html",
            "Content-Transfer-Encoding" => "quoted-printable",
            "CHARSET" => $charset,
            "DATA" => $html
        );
        return($this->CreatePart($definition, $part));
    }

    Function AddEncodedQuotedPrintableHTMLPart($html, $charset = "") {
        if (strlen($error = $this->CreateEncodedQuotedPrintableHTMLPart($html, $charset, $part)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreateQuotedPrintableHTMLPart($html, $charset, &$part) {
        return($this->CreateEncodedQuotedPrintableHTMLPart($this->QuotedPrintableEncode($html), $charset, $part));
    }

    Function AddQuotedPrintableHTMLPart($html, $charset = "") {
        return($this->AddEncodedQuotedPrintableHTMLPart($this->QuotedPrintableEncode($html), $charset));
    }

    Function GetFileDefinition(&$file, &$definition, $require_name = 1) {
        if (strlen($this->error))
            return($this->error);
        $name = "";
        if (IsSet($file["FileName"]))
            $name = basename($file["FileName"]);
        else {
            if (!IsSet($file["Data"]))
                return($this->OutputError("it was not specified the file part file name"));
        }
        if (IsSet($file["Name"]))
            $name = $file["Name"];
        if ($require_name && strlen($name) == 0)
            return($this->OutputError("it was not specified the file part name"));
        $encoding = "base64";
        if (IsSet($file["Content-Type"])) {
            $content_type = $file["Content-Type"];
            $type = $this->Tokenize(strtolower($content_type), "/");
            $sub_type = $this->Tokenize("");
            switch ($type) {
                case "text":
                case "image":
                case "audio":
                case "video":
                case "application":
                    break;
                case "message":
                    $encoding = "7bit";
                    break;
                case "automatic":
                    switch ($sub_type) {
                        case "name":
                            if (strlen($name) == 0)
                                return($this->OutputError("it is not possible to determine content type from the name"));
                            switch (strtolower($this->GetFilenameExtension($name))) {
                                case ".xls":
                                    $content_type = "application/excel";
                                    break;
                                case ".hqx":
                                    $content_type = "application/macbinhex40";
                                    break;
                                case ".doc":
                                case ".dot":
                                case ".wrd":
                                    $content_type = "application/msword";
                                    break;
                                case ".pdf":
                                    $content_type = "application/pdf";
                                    break;
                                case ".pgp":
                                    $content_type = "application/pgp";
                                    break;
                                case ".ps":
                                case ".eps":
                                case ".ai":
                                    $content_type = "application/postscript";
                                    break;
                                case ".ppt":
                                    $content_type = "application/powerpoint";
                                    break;
                                case ".rtf":
                                    $content_type = "application/rtf";
                                    break;
                                case ".tgz":
                                case ".gtar":
                                    $content_type = "application/x-gtar";
                                    break;
                                case ".gz":
                                    $content_type = "application/x-gzip";
                                    break;
                                case ".php":
                                case ".php3":
                                    $content_type = "application/x-httpd-php";
                                    break;
                                case ".js":
                                    $content_type = "application/x-javascript";
                                    break;
                                case ".ppd":
                                case ".psd":
                                    $content_type = "application/x-photoshop";
                                    break;
                                case ".swf":
                                case ".swc":
                                case ".rf":
                                    $content_type = "application/x-shockwave-flash";
                                    break;
                                case ".tar":
                                    $content_type = "application/x-tar";
                                    break;
                                case ".zip":
                                    $content_type = "application/zip";
                                    break;
                                case ".mid":
                                case ".midi":
                                case ".kar":
                                    $content_type = "audio/midi";
                                    break;
                                case ".mp2":
                                case ".mp3":
                                case ".mpga":
                                    $content_type = "audio/mpeg";
                                    break;
                                case ".ra":
                                    $content_type = "audio/x-realaudio";
                                    break;
                                case ".wav":
                                    $content_type = "audio/wav";
                                    break;
                                case ".bmp":
                                    $content_type = "image/bitmap";
                                    break;
                                case ".gif":
                                    $content_type = "image/gif";
                                    break;
                                case ".iff":
                                    $content_type = "image/iff";
                                    break;
                                case ".jb2":
                                    $content_type = "image/jb2";
                                    break;
                                case ".jpg":
                                case ".jpe":
                                case ".jpeg":
                                    $content_type = "image/jpeg";
                                    break;
                                case ".jpx":
                                    $content_type = "image/jpx";
                                    break;
                                case ".png":
                                    $content_type = "image/png";
                                    break;
                                case ".tif":
                                case ".tiff":
                                    $content_type = "image/tiff";
                                    break;
                                case ".wbmp":
                                    $content_type = "image/vnd.wap.wbmp";
                                    break;
                                case ".xbm":
                                    $content_type = "image/xbm";
                                    break;
                                case ".css":
                                    $content_type = "text/css";
                                    break;
                                case ".txt":
                                    $content_type = "text/plain";
                                    break;
                                case ".htm":
                                case ".html":
                                    $content_type = "text/html";
                                    break;
                                case ".xml":
                                    $content_type = "text/xml";
                                    break;
                                case ".mpg":
                                case ".mpe":
                                case ".mpeg":
                                    $content_type = "video/mpeg";
                                    break;
                                case ".qt":
                                case ".mov":
                                    $content_type = "video/quicktime";
                                    break;
                                case ".avi":
                                    $content_type = "video/x-ms-video";
                                    break;
                                case ".eml":
                                    $content_type = "message/rfc822";
                                    $encoding = "7bit";
                                    break;
                                default:
                                    $content_type = "application/octet-stream";
                                    break;
                            }
                            break;
                        default:
                            return($this->OutputError($content_type . " is not a supported automatic content type detection method"));
                    }
                    break;
                default:
                    return($this->OutputError($content_type . " is not a supported file content type"));
            }
        } else
            $content_type = "application/octet-stream";
        $definition = array(
            "Content-Type" => $content_type,
            "Content-Transfer-Encoding" => $encoding,
            "NAME" => $name
        );
        if (IsSet($file["Disposition"])) {
            switch (strtolower($file["Disposition"])) {
                case "inline":
                case "attachment":
                    break;
                default:
                    return($this->OutputError($file["Disposition"] . " is not a supported message part content disposition"));
            }
            $definition["DISPOSITION"] = $file["Disposition"];
        }
        if (IsSet($file["FileName"]))
            $definition["FILENAME"] = $file["FileName"];
        else {
            if (IsSet($file["Data"]))
                $definition["DATA"] = $file["Data"];
        }
        return("");
    }

    Function CreateFilePart(&$file, &$part) {
        if (strlen($this->GetFileDefinition($file, $definition)))
            return($this->error);
        return($this->CreatePart($definition, $part));
    }

    Function AddFilePart(&$file) {
        if (strlen($error = $this->CreateFilePart($file, $part)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreateMessagePart(&$message, &$part) {
        $message["Content-Type"] = "message/rfc822";
        $message["Disposition"] = "inline";
        if (strlen($this->GetFileDefinition($message, $definition)))
            return($this->error);
        return($this->CreatePart($definition, $part));
    }

    Function AddMessagePart(&$message) {
        if (strlen($error = $this->CreateMessagePart($message, $part)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreateMultipart(&$parts, &$part, $type) {
        $definition = array(
            "Content-Type" => "multipart/" . $type,
            "PARTS" => $parts
        );
        return($this->CreatePart($definition, $part));
    }

    Function AddMultipart(&$parts, $type) {
        if (strlen($error = $this->CreateMultipart($parts, $part, $type)) || strlen($error = $this->AddPart($part)))
            return($error);
        return("");
    }

    Function CreateAlternativeMultipart(&$parts, &$part) {
        return($this->CreateMultiPart($parts, $part, "alternative"));
    }

    Function AddAlternativeMultipart(&$parts) {
        return($this->AddMultipart($parts, "alternative"));
    }

    Function CreateRelatedMultipart(&$parts, &$part) {
        return($this->CreateMultipart($parts, $part, "related"));
    }

    Function AddRelatedMultipart(&$parts) {
        return($this->AddMultipart($parts, "related"));
    }

    Function CreateMixedMultipart(&$parts, &$part) {
        return($this->CreateMultipart($parts, $part, "mixed"));
    }

    Function AddMixedMultipart(&$parts) {
        return($this->AddMultipart($parts, "mixed"));
    }

    Function CreateParallelMultipart(&$parts, &$part) {
        return($this->CreateMultipart($parts, $part, "paralell"));
    }

    Function AddParalellMultipart(&$parts) {
        return($this->AddMultipart($parts, "paralell"));
    }

    Function GetPartContentID($part) {
        if (!IsSet($this->parts[$part]))
            return("");
        if (!IsSet($this->parts[$part]["Content-ID"])) {
            $extension = (IsSet($this->parts[$part]["NAME"]) ? $this->GetFilenameExtension($this->parts[$part]["NAME"]) : "");
            $this->parts[$part]["Content-ID"] = md5(uniqid($part . time())) . $extension;
        }
        return($this->parts[$part]["Content-ID"]);
    }

    Function GetDataURL($file) {
        if (strlen($this->GetFileDefinition($file, $definition, 0)))
            return($this->error);
        if (IsSet($definition["FILENAME"])) {
            $size = @filesize($definition["FILENAME"]);
            if (!($file = @fopen($definition["FILENAME"], "rb")))
                return($this->OutputPHPError("could not open data file " . $definition["FILENAME"], $php_errormsg));
            for ($body = ""; !feof($file);) {
                if (GetType($block = @fread($file, $this->file_buffer_length)) != "string") {
                    $this->OutputPHPError("could not read data file", $php_errormsg);
                    fclose($file);
                    return("");
                }
                $body.=$block;
            }
            fclose($file);
            if (GetType($size) == "integer" && strlen($body) != $size) {
                $this->OutputError("the length of the file that was read does not match the size of the part file " . $definition["FILENAME"] . " due to possible data corruption");
                return("");
            }
            if (function_exists("ini_get") && ini_get("magic_quotes_runtime"))
                $body = StripSlashes($body);
            $body = chunk_split(base64_encode($body));
        }
        else {
            if (!IsSet($definition["DATA"])) {
                $this->OutputError("it was not specified a file or data block");
                return("");
            }
            $body = chunk_split(base64_encode($definition["DATA"]));
        }
        return("data:" . $definition["Content-Type"] . ";base64," . $body);
    }

    Function Send() {
        if (strlen($this->error))
            return($this->error);
        $headers = $this->headers;
        if (strcmp($this->mailer, "")) {
            $headers["X-Mailer"] = $this->mailer;
            if (strlen($this->mailer_delivery))
                $headers["X-Mailer"].=' (' . $this->mailer_delivery . ')';
        }
        $headers["MIME-Version"] = "1.0";
        if ($this->body_parts == 0)
            return($this->OutputError("message has no body parts"));
        if (strlen($error = $this->GetPartHeaders($headers, $this->body)))
            return($error);
        if ($this->cache_body && IsSet($this->body_cache[$this->body]))
            $body = $this->body_cache[$this->body];
        else {
            if (strlen($error = $this->GetPartBody($body, $this->body)))
                return($error);
            if ($this->cache_body)
                $this->body_cache[$this->body] = $body;
        }
        if (strcmp($error = $this->StartSendingMessage(), ""))
            return($error);
        if (strlen($error = $this->SendMessageHeaders($headers)) == 0 && strlen($error = $this->SendMessageBody($body)) == 0)
            $error = $this->EndSendingMessage();
        $this->StopSendingMessage();
        return($error);
    }

    Function Mail($to, $subject, $message, $additional_headers = "", $additional_parameters = "") {
        $this->ResetMessage();
        $this->headers = array("To" => $to, "Subject" => $subject);
        $content_type = "";
        while (strlen($additional_headers)) {
            ereg("([^\r\n]+)(\r?\n)?(.*)\$", $additional_headers, $matches);
            $header = $matches[1];
            $additional_headers = $matches[3];
            if (!ereg("^([^:]+):[ \t]+(.+)\$", $header, $matches)) {
                $this->error = "invalid header \"$header\"";
                return(0);
            }
            if (strtolower($matches[1]) == "content-type") {
                if (strlen($content_type)) {
                    $this->error = "the content-type header was specified more than once.";
                    return(0);
                }
                $content_type = $matches[2];
            } else
                $this->SetHeader($matches[1], $matches[2]);
        }
        if (strlen($additional_parameters)) {
            if (ereg("^[ \t]*-f[ \t]*([^@]+@[^ \t]+)[ \t]*(.*)\$"/* "^[ \t]?-f([^@]@[^ \t]+)[ \t]?(.*)\$" */, $additional_parameters, $matches)) {
                if (!eregi($this->email_regular_expression, $matches[1])) {
                    $this->error = "it was specified an invalid e-mail address for the additional parameter -f";
                    return(0);
                }
                if (strlen($matches[2])) {
                    $this->error = "it were specified some additional parameters after -f e-mail address parameter that are not supported";
                    return(0);
                }
                $this->SetHeader("Return-Path", $matches[1]);
            } else {
                $this->error = "the additional parameters that were specified are not supported";
                return(0);
            }
        }
        if (strlen($content_type) == 0)
            $content_type = "text/plain";
        $definition = array(
            "Content-Type" => $content_type,
            "DATA" => $message
        );
        $this->CreateAndAddPart($definition);
        $this->Send();
        return(strlen($this->error) == 0);
    }

    Function ChangeBulkMail($on) {
        return(1);
    }

    Function SetBulkMail($on) {
        if (strlen($this->error))
            return(0);
        if (!$this->bulk_mail == !$on)
            return(1);
        if (!$this->ChangeBulkMail($on))
            return(0);
        $this->bulk_mail = !!$on;
        return(1);
    }

    Function OpenMailing(&$mailing, &$mailing_properties) {
        if (strlen($this->error))
            return($this->error);
        if (!IsSet($mailing_properties["Name"]) || strlen($mailing_properties["Name"]) == 0)
            return($this->OutputError("it was not specified a valid mailing Name"));
        if (!IsSet($mailing_properties["Return-Path"]) || strlen($mailing_properties["Return-Path"]) == 0)
            return($this->OutputError("it was not specified a valid mailing Return-Path"));
        $separator = "";
        $directory_separator = (defined("DIRECTORY_SEPARATOR") ? DIRECTORY_SEPARATOR : ((defined("PHP_OS") && !strcmp(substr(PHP_OS, 0, 3), "WIN")) ? "\\" : "/"));
        $length = strlen($this->mailing_path);
        if ($length) {
            if ($this->mailing_path[$length - 1] != $directory_separator)
                $separator = $directory_separator;
        }
        $base_path = $this->mailing_path . $separator . $mailing_properties["Name"];
        if ($this->body_parts == 0)
            return($this->OutputError("message has no body parts"));
        $line_break = "\n";
        $headers = $this->headers;
        if (strlen($this->mailer))
            $headers["X-Mailer"] = $this->mailer;
        $headers["MIME-Version"] = "1.0";
        if (strlen($error = $this->GetPartHeaders($headers, $this->body)))
            return($error);
        if (!($header_file = @fopen($base_path . ".h", "wb")))
            return($this->OutputPHPError("could not open mailing headers file " . $base_path . ".h", $php_errormsg));
        for ($header = 0, Reset($headers); $header < count($headers); Next($headers), $header++) {
            $header_name = Key($headers);
            if (!@fwrite($header_file, $header_name . ": " . $headers[$header_name] . $line_break)) {
                fclose($header_file);
                return($this->OutputPHPError("could not write to the mailing headers file " . $base_path . ".h", $php_errormsg));
            }
        }
        if (!@fflush($header_file)) {
            fclose($header_file);
            @unlink($base_path . ".h");
            return($this->OutputPHPError("could not write to the mailing headers file " . $base_path . ".h", $php_errormsg));
        }
        fclose($header_file);
        if (strlen($error = $this->GetPartBody($body, $this->body))) {
            @unlink($base_path . ".h");
            return($error);
        }
        if (!($body_file = @fopen($base_path . ".b", "wb"))) {
            @unlink($base_path . ".h");
            return($this->OutputPHPError("could not open mailing body file " . $base_path . ".b", $php_errormsg));
        }
        if (!@fwrite($body_file, $body) || !@fflush($body_file)) {
            fclose($body_file);
            @unlink($base_path . ".b");
            @unlink($base_path . ".h");
            return($this->OutputPHPError("could not write to the mailing body file " . $base_path . ".b", $php_errormsg));
        }
        fclose($body_file);
        if (!($envelope = @fopen($base_path . ".e", "wb"))) {
            @unlink($base_path . ".b");
            @unlink($base_path . ".h");
            return($this->OutputPHPError("could not open mailing envelope file " . $base_path . ".e", $php_errormsg));
        }
        if (!@fwrite($envelope, "F" . $mailing_properties["Return-Path"] . chr(0)) || !@fflush($envelope)) {
            @fclose($envelope);
            @unlink($base_path . ".e");
            @unlink($base_path . ".b");
            @unlink($base_path . ".h");
            return($this->OutputPHPError("could not write to the return path to the mailing envelope file " . $base_path . ".e", $php_errormsg));
        }
        $mailing = ++$this->last_mailing;
        $this->mailings[$mailing] = array(
            "Envelope" => $envelope,
            "BasePath" => $base_path
        );
        return("");
    }

    Function AddMailingRecipient($mailing, &$recipient_properties) {
        if (strlen($this->error))
            return($this->error);
        if (!IsSet($this->mailings[$mailing]))
            return($this->OutputError("it was not specified a valid mailing"));
        if (!IsSet($recipient_properties["Address"]) || strlen($recipient_properties["Address"]) == 0)
            return($this->OutputError("it was not specified a valid mailing recipient Address"));
        if (!@fwrite($this->mailings[$mailing]["Envelope"], "T" . $recipient_properties["Address"] . chr(0)))
            return($this->OutputPHPError("could not write recipient address to the mailing envelope file", $php_errormsg));
        return("");
    }

    Function EndMailing($mailing) {
        if (strlen($this->error))
            return($this->error);
        if (!IsSet($this->mailings[$mailing]))
            return($this->OutputError("it was not specified a valid mailing"));
        if (!IsSet($this->mailings[$mailing]["Envelope"]))
            return($this->OutputError("the mailing was already ended"));
        if (!@fwrite($this->mailings[$mailing]["Envelope"], chr(0)) || !@fflush($this->mailings[$mailing]["Envelope"]))
            return($this->OutputPHPError("could not end writing to the mailing envelope file", $php_errormsg));
        fclose($this->mailings[$mailing]["Envelope"]);
        Unset($this->mailings[$mailing]["Envelope"]);
        return("");
    }

    Function SendMailing($mailing) {
        if (strlen($this->error))
            return($this->error);
        if (!IsSet($this->mailings[$mailing]))
            return($this->OutputError("it was not specified a valid mailing"));
        if (IsSet($this->mailings[$mailing]["Envelope"]))
            return($this->OutputError("the mailing was not yet ended"));
        $this->ResetMessage();
        $base_path = $this->mailings[$mailing]["BasePath"];
        if (GetType($header_lines = @File($base_path . ".h")) != "array")
            return($this->OutputPHPError("could not read the mailing headers file " . $base_path . ".h", $php_errormsg));
        for ($line = 0; $line < count($header_lines); $line++) {
            $header_name = $this->Tokenize($header_lines[$line], ": ");
            $this->headers[$header_name] = trim($this->Tokenize("\n"));
        }
        if (!($envelope_file = @fopen($base_path . ".e", "rb")))
            return($this->OutputPHPError("could not open the mailing envelope file " . $base_path . ".e", $php_errormsg));
        for ($bcc = $data = "", $position = 0; !feof($envelope_file) || strlen($data);) {
            if (GetType($break = strpos($data, chr(0), $position)) != "integer") {
                if (GetType($chunk = @fread($envelope_file, $this->file_buffer_length)) != "string") {
                    fclose($envelope_file);
                    return($this->OutputPHPError("could not read the mailing envelop file " . $base_path . ".e", $php_errormsg));
                }
                $data = substr($data, $position) . $chunk;
                $position = 0;
                continue;
            }
            if ($break == $position)
                break;
            switch ($data[$position]) {
                case "F":
                    $this->headers["Return-Path"] = substr($data, $position + 1, $break - $position - 1);
                    break;
                case "T":
                    $bcc.=(strlen($bcc) == 0 ? "" : ", ") . substr($data, $position + 1, $break - $position - 1);
                    break;
                default:
                    return($this->OutputError("invalid mailing envelope file " . $base_path . ".e"));
            }
            $position = $break + 1;
        }
        fclose($envelope_file);
        if (strlen($bcc) == 0)
            return($this->OutputError("the mailing envelop file " . $base_path . ".e does not contain any recipients"));
        $this->headers["Bcc"] = $bcc;
        if (!($body_file = @fopen($base_path . ".b", "rb")))
            return($this->OutputPHPError("could not open the mailing body file " . $base_path . ".b", $php_errormsg));
        for ($data = ""; !feof($body_file);) {
            if (GetType($chunk = @fread($body_file, $this->file_buffer_length)) != "string") {
                fclose($body_file);
                return($this->OutputPHPError("could not read the mailing body file " . $base_path . ".b", $php_errormsg));
            }
            $data.=$chunk;
        }
        fclose($body_file);
        if (strlen($error = $this->StartSendingMessage()))
            return($error);
        if (strlen($error = $this->SendMessageHeaders($this->headers)) == 0 && strlen($error = $this->SendMessageBody($data)) == 0)
            $error = $this->EndSendingMessage();
        $this->StopSendingMessage();
        return($error);
    }

}

;
?>