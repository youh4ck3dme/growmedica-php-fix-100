<div id="leftMenu">
    <?
    if (STATUS_NEWSLETTER == '1') {
        echo 'Tu sa Vám zobrazujú e-mailové adresy užívateľov, ktorí sa prihlásili na odber newsletterov.';
    } else {
        echo 'Slúži na hromadné rozosielanie emailových správ. Upozornenie - z bezpečnostných dôvodov nie je možné odoslať viac než 500 emailových správ za hodinu.';
    }
    ?></div>
<div id="moduleContent">
    <?
    switch ($_GET['action']) {
        case 'submit':
            if (isset($_POST)) {
                if (!empty($_POST["emails"])) {
                    require_once("../shared/email_message.php");
                    $message = "<body>";
                    $message .= $_POST['text'];
                    $message .= '<div style="clear: both;margin:20px 0 0;">';
                    $message .= '<a href="' . ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID) . '/newsletter-odhlasenie/{_-=unsubscribe-mail=-_}">' . $cTranslator->getTranslation('Odhlásiť sa z odberu', 0) . '</a>';
                    $message .= '</div>';
                    $message .= "</body>";

                    $from_address = $fromAddress;
                    $from_name = $fromName;
                    $reply_name = $fromName;
                    $reply_address = $fromAddress;
                    $error_delivery_name = $fromName;
                    $error_delivery_address = $fromAddress;

                    // mail prijemca
                    //$emailAddresses = $_POST['emaily'];

                    foreach (explode(';', $_POST['emails']) as $to) {
                        $to_address .= $to . ';';
                        $to_name .= $to . ';';
                    }

                    //$to_address = $_POST['emaily']; //substr($emailAddresses, 0, strlen($emailAddresses) - 1); //$to;
                    //$to_name = "Office";

                    $email_message = new email_message_class;
                    $email_message->SetEncodedEmailHeader("To", $to_address, $to_name);
                    //$email_message->SetEncodedEmailHeader("Bcc","macak@sixnet.sk","Stefan Macak");
                    $email_message->SetEncodedEmailHeader("From", $from_address, $from_name);
                    $email_message->SetEncodedEmailHeader("Reply-To", $reply_address, $reply_name);
                    $email_message->SetHeader("Sender", $from_address);
                    $email_message->SetHeader("Subject", $_POST['subject']);

                    $message = $html_url . $message;
                    $text_message = str_replace('\"', '"', $message);

                    $email_message->AddHTMLPart($text_message, "utf-8");

                    if ($_FILES['priloha-1']['name'] <> "") {

                        copy($_FILES['priloha-1']['tmp_name'], "../attachment/" . $_FILES['priloha-1']['name']);

                        $image_attachment = array("FileName" => "../attachment/" . $_FILES['priloha-1']['name'],
                            "Content-Type" => "automatic/name",
                            "Disposition" => "attachment"
                        );

                        $email_message->AddFilePart($image_attachment);
                    }

                    if ($_FILES['priloha-2'] ['name'] <> "") {

                        copy($_FILES['priloha-2']['tmp_name'], "../attachment/" . $_FILES['priloha-2']['name']);

                        $image_attachment = array(
                            "FileName" => "../attachment/" . $_FILES['priloha-2'] ['name'],
                            "Content-Type" => "automatic/name",
                            "Disposition" => "attachment"
                        );

                        $email_message->AddFilePart($image_attachment);
                    }

                    if ($_FILES['priloha-3']['name'] <> "") {

                        copy($_FILES['priloha-3']['tmp_name'], "../attachment/" . $_FILES['priloha-3']['name']);

                        $image_attachment = array(
                            "FileName" => "../attachment/" . $_FILES['priloha-3']['name'],
                            "Content-Type" => "automatic/name",
                            "Disposition" => "attachment"
                        );

                        $email_message->AddFilePart($image_attachment);
                    }

                    // odoslanie mailu
                    $error = $email_message->Send();
                    //print_r($error);
                    //echo $message;

                    if ($_FILES['priloha-1']['name'] <> "")
                        unlink("../attachment/" . $_FILES['priloha-1']['name']);
                    if ($_FILES['priloha-2']['name'] <> "")
                        unlink("../attachment/" . $_FILES ['priloha-2']['name']);
                    if ($_FILES['priloha-3']['name'] <> "")
                        unlink("../attachment/" . $_FILES['priloha-3']['name']);

                    if (strcmp($error, "")) {
                        Message::setMessage("Newsletter sa nepodarilo rozposlať.", 2);
                    } else {
                        Message::setMessage("Newsletter bol rozposlaný.", 0);
                    }
                } else {
                    Message::setMessage("Žiadny email pre odoslanie.", 2);
                }
            }
        default:
            ?>
            <h1>Newsletter</h1>
            <table border="0" cellspacing="0" cellpadding="2" class="tablelist" summary="">
                <tr>
                    <th>&nbsp;</th>
                </tr>
            </table>
            <?
            if (STATUS_NEWSLETTER == '1') {
                ?>
                <table border="0" align="center" cellpadding="2" cellspacing="0" class="contactForm" width="100%">
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td valign="top">E-mailové adresy prihlásené na odber newslettra</td>
                        <td>
                            <?
                            $result = mysql_query("select mail, username from " . TABLE_PREFIX . "user where newsletter='1'");
                            while ($row = mysql_fetch_array($result)) {
                                if (!empty($row['mail'])) {
                                    $emails[] = $row['mail'];
                                } else {
                                    $emails[] = $row['username'];
                                }
                            }
                            $emails = implode(";", array_unique($emails));
                            ?>
                            <textarea class="emails-export" cols="80" rows="12"><?= $emails ?></textarea>
                        </td>
                    </tr>
                </table>
                <?
            } elseif (STATUS_NEWSLETTER == '2') {
                ?>
                <form action="index.php?module=newsletter&action=submit" method="post" enctype="multipart/form-data" name="ContactForm" id="ContactForm">
                    <table border="0" align="center" cellpadding="2" cellspacing="0" class="contactForm" width="100%">
                        <tr>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <td valign="top">Predmet emailu</td>
                            <td><input type="text" name="subject" value=" <?= $_POST['subject']; ?>" /></td>
                        </tr>
                        <tr>
                            <td valign="top">Obsah emailu</td>
                            <td><textarea id="ckeditor" class="ckeditor" name="text"><?= $_POST['text']; ?></textarea></td>
                        </tr>
                        <tr>
                            <td valign="top">E-maily na ktoré bude newsletter rozposlaný</td>
                            <td>
                                <?
                                $result = mysql_query("select mail, username from " . TABLE_PREFIX . "user where newsletter='1'");
                                while ($row = mysql_fetch_array($result)) {
                                    if (!empty($row['mail'])) {
                                        $emails[] = $row['mail'];
                                    } else {
                                        $emails[] = $row['username'];
                                    }
                                }
                                $emails = implode(";", array_unique($emails));
                                ?>
                                <textarea name="emails" cols="80" rows="12"><?= $emails ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Príloha 1</td>
                            <td><input name="priloha-1" type="file" id="priloha-1" /></td>
                        </tr>
                        <tr>
                            <td>Príloha 2</td>
                            <td><input name="priloha-2" type="file" id="priloha-2" /></td>
                        </tr>
                        <tr>
                            <td>Príloha 3</td>
                            <td><input name="priloha-3" type="file" id="priloha-3" /></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php include("../popups/foto.php"); ?>
                                <?php include("../popups/docs.php"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;<input name="Reset" type="reset" class="formButton" value="Zmazať" />
                                <input name="submit" type="submit" class="formButton" value="Rozposlať" />
                                <input name="send" type="hidden" value="1" /></td>
                        </tr>
                        <tr>
                            <th>&nbsp;</th>
                        </tr>
                    </table>
                </form>
                <?
            }
    }
    ?>
</div>
<script type="text/javascript">
    CKEDITOR.replace('ckeditor', {
        customConfig: 'config-full.js'
    });
</script>