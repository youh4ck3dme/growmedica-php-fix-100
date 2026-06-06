<?

interface iMessage {

    public function setMessage($message, $level); //obsluha sprav

    public function getMessage(); //vypisovanie sprav

    public function sendReport($error);
}

/* * ********************************************************** */
/* * ********************************************************** */
/* * ********************************************************** */

class Message implements iMessage {
    /*     * ************************************************************* */
    /* Metoda nastavenia spravy pri vykonanej udalosti
     *  Spravy su ulozene v poli v session.
     *  Je mozne ulozit aj viac sprav naraz.
     *  Po vypisani sa vymazu.
     *
     *  Spravy maju tri urovne
     *  0 - Dobra sprava/uspech (vypise sa v zelenom boxe so zelenym textom)
     *  1 - Informacia/varovanie (vypise sa v modrom boxe s modrym textom)
     *  2 - Zla sprava/neuspech (vypise sa v cervenom boxe s cervenym textom)
     *
     *  Priklady:
     *  Message::setMessage('Gratulujem!', 0);
     *  Message::setMessage('V Lidli maju akciu na vajicka', 1);
     *  Message::setMessage('Toto sa nepodarilo', 2);
     *
     * Je mozne ich volat aj dynamicky ($obj = new Message; $obj->setMessage(...), ale neprinasa to ziadnu vyhodu
     */

    public function setMessage($message, $level) 
    {
        $_SESSION[PROJECT_NAME.'_flash_message'][] = array('content' => $message, 'level' => $level);

        // Tu to byt nemoze, pretoze potom bude hlasit chybu na tomto riadku a v tomto subore... co je blbost.
        /*if($level == 2)
        {
            Message::sendReport($message);
        }*/
    }

    /*     * ********************************************************** */
    /* Metoda na vypis sprav.
     *  Vypise spravy a zahodi ich.
     *
     * Priklad:
     * Message::getMessage();
     */

    public function getMessage() {
        if (isset($_SESSION[PROJECT_NAME.'_flash_message']) && !empty($_SESSION[PROJECT_NAME.'_flash_message'])) {
            echo '<div id="messages-container">';
            foreach ($_SESSION[PROJECT_NAME.'_flash_message'] as $fm) {
                if ($fm['level'] == 0) {
                    if (!empty($fm['content']))
                        echo '<div class="success"><span></span>' . $fm['content'] . '</div>';
                }
                elseif ($fm['level'] == 1) {
                    if (!empty($fm['content']))
                        echo '<div class="info"><span></span>' . $fm['content'] . '</div>';
                }
                elseif ($fm['level'] == 2) {
                    if (!empty($fm['content']))
                        echo '<div class="error"><span></span>' . $fm['content'] . '</div>';
                }
            }
            echo '</div>';
            unset($_SESSION[PROJECT_NAME.'_flash_message']);
        }
    }

    /*     * ********************************************************** */

    public function sendReport($error)
    {
        global $supportAddress;
        global $fromAddress;
        global $fromName;

        $bt = debug_backtrace();
        $caller = array_shift($bt);

        $message = '<h1>ERROR REPORT: Na stranke '.PROJECT_NAME.' sa objavil problem!</h1><br/>';
        $message .= 'Znenie chyby: '.String::diakritika($error).'<br/>';
        $message .= 'Subor: '.String::diakritika($caller['file']).'<br/>';
        $message .= 'Cislo riadku: '.String::diakritika($caller['line']).'<br/>';
        $message .= 'URL: '.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'<br/>';

        if(!$supportAddress)
        {
            $supportAddress = 'sixnet@sixnet.sk';
        }

        $email_message = new email_message_class;
        $email_message->SetEncodedEmailHeader("From", $fromAddress, $fromName);
        $email_message->SetEncodedEmailHeader("Reply-To", $fromAddress, $fromName);
        $email_message->SetHeader("Sender", $fromAddress);
        $email_message->SetEncodedEmailHeader("To", $supportAddress, $supportAddress);
        $email_message->SetHeader("Subject", 'ERROR REPORT: Na stranke '.PROJECT_NAME.' sa objavil problem!');
        $email_message->AddHTMLPart(String::diakritika($message), "utf-8");
        $error = $email_message->Send();

        return $error;        
    }
}

?>