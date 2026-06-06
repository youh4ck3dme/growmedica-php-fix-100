<?php
    $GDPR_PAGE_ID = 130;
    $KONTAKT_PAGE_ID = 113;
?>

<script type="module">
    import './js/cookieconsent/cookieconsent.umd.js';

    CookieConsent.run({
        guiOptions: {
            consentModal: {
                layout: "box inline",
                position: "bottom right"
            },
            preferencesModal: {
                layout: "box",
                position: "right"
            }
        },
        categories: {
            necessary: {
                readOnly: true
            },
            analytics: {
                services: {
                    ga: {
                        label: 'Google Analytics',
                        cookies: [
                            {
                                name: /^(_ga|_gid)/
                            }
                        ]
                    }
                }
            },
            marketing: {}
        },
        language: {
            default: '<?= DEFAULT_LANG; ?>',
            translations: {
                '<?= DEFAULT_LANG; ?>': {
                    consentModal: {
                        title: '<?= $cTranslator->getTranslation('Na zlepšenie našich služieb využívame cookies.', 0); ?>',
                        description: '<?= getContentByLabel('Cookies panel - hlavný text', 0); ?>',
                        acceptAllBtn: '<?= $cTranslator->getTranslation('Prijať všetko', 0); ?>',
                        acceptNecessaryBtn: '<?= $cTranslator->getTranslation('Odmietnúť všetko', 0); ?>',
                        showPreferencesBtn: '<?= $cTranslator->getTranslation('Vlastné nastavenie', 0); ?>'
                    },
                    preferencesModal: {
                        title: '<?= $cTranslator->getTranslation('Nastavenia cookies', 0); ?>',
                        acceptAllBtn: '<?= $cTranslator->getTranslation('Prijať všetko', 0); ?>',
                        acceptNecessaryBtn: '<?= $cTranslator->getTranslation('Odmietnúť všetko', 0); ?>',
                        savePreferencesBtn: '<?= $cTranslator->getTranslation('Uložiť nastavenia', 0); ?>',
                        closeIconLabel: '<?= $cTranslator->getTranslation('Zavrieť', 0); ?>',
                        sections: [
                            {
                                title: '<?= $cTranslator->getTranslation('Používanie súborov cookie', 0); ?>',
                                description: '<?= getContentByLabel('Cookies panel - použitie cookies', 0); ?> <a href="<?= Menu::getHyperlinkById($GDPR_PAGE_ID) ?>" class="cc-link"><?= Menu::getHyperLinkTextById($GDPR_PAGE_ID) ?></a>.'
                            },
                            {
                                title: '<?= $cTranslator->getTranslation('Nevyhnutné cookies na prevádzku webu', 0); ?> <span class="pm__badge"><?= $cTranslator->getTranslation('Vždy povolené', 0); ?></span>',
                                description: '<?= $cTranslator->getTranslation('Sú nevyhnutné pre fungovanie našej webovej stránky a nemožno ich vypnúť.', 0); ?>',
                                linkedCategory: "necessary"
                            },
                            {
                                title: '<?= $cTranslator->getTranslation('Analytické cookies na sledovanie a analýzu návštevnosti', 0); ?>',
                                description: '<?= $cTranslator->getTranslation('Pomáhajú nám zlepšovať spôsob fungovania našich webových stránok (napr. tým, že zaisťujú, že používatelia ľahko nájdu to, čo hľadajú).', 0); ?>',
                                linkedCategory: "analytics"
                            },
                            {
                                title: '<?= $cTranslator->getTranslation('Reklamné cookies na cielenie reklamy', 0); ?>',
                                description: '<?= $cTranslator->getTranslation('Tieto cookies zhromažďujú informácie o tom, ako používate webovú stránku, ktoré stránky ste navštívili a na ktoré odkazy ste klikli.', 0); ?>',
                                linkedCategory: "marketing"
                            },
                            {
                                title: '<?= $cTranslator->getTranslation('Viac informácií', 0); ?>',
                                description: '<?= getContentByLabel('Cookies panel - viac informácií', 0); ?> <a class="cc__link" href="<?= Menu::getHyperlinkById($KONTAKT_PAGE_ID) ?>"><?= $cTranslator->getTranslation('kontaktovať', 0); ?></a>.'
                            }
                        ]
                    }
                }
            }
        }
    });
</script>
