<?
if ($user->isAuthenticated()) {
    header("Location:" . ROOTDIR . "/setup");
    exit;
}
?>
<div id="leftMenu">Prihláste sa do redakčného systému dodanými prihlasovacími údajmi. V prípade, že sa neviete prihlásiť, kontaktujte nás na telefónnom čísle 055/72 87 533.</div>
<div id="moduleContent">
    <p>&nbsp;</p>
    <table align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td rowspan="2" align="center" valign="middle" width="123"><img src="images/login.jpg" alt="" border="0" width="123" height="121"></td>
                <td valign="top" height="33">&nbsp;</td>
            </tr>
            <tr>
                <td valign="top">
                    <form method="post" enctype="multipart/form-data" id="login_form" onsubmit="MM_validateForm('login', '', 'R');
                            return document.MM_returnValue" action="">
                        <table summary="" border="0" cellpadding="0" cellspacing="0" id="loginForm">
                            <tbody>
                                <tr>
                                    <td>
                                        <h2>Prihlasovací formulár</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input name="mail" class="textbox" id="mail" value="<?= $_POST['mail'] ?>" type="email" autofocus placeholder="Email" >
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input name="pwd" class="textbox" id="pwd" type="password" placeholder="Heslo">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="hidden" name="action" value="setup-login" />
                                        <input class="button" value="Prihlásiť" type="submit" />
                                        <label><input name="redirecttosite" type="checkbox" <?php if ($_COOKIE['redirecttosite'] == 1) echo 'checked="checked"'; ?> value="1" /> Priamo na stránku</label></td>
                                </tr>
                            </tbody>
                        </table>
                    </form></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        </tbody>
    </table>
    <script language="javascript" type="text/javascript">
        function InitFunc() {
            document.forms[0].mail.focus();
        }
        InitFunc();
        var frmvalidator = new Validator("login_form");
        frmvalidator.addValidation("mail", "maxlen=50", "Nezadali ste správnu dĺžku e-mailovej adresy");
        frmvalidator.addValidation("mail", "req", "Nezadali ste e-mailovú adresu");
        frmvalidator.addValidation("mail", "email", "Nezadali korektnú e-mailovú adresu");
        frmvalidator.addValidation("pwd", "req", "Nezadali ste heslo");
    </script>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
</div>