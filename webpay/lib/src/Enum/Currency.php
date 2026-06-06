<?php

/**
 * This file is part of the Pixidos package.
 *
 *  (c) Ondra Votava <ondra@votava.dev>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Pixidos\GPWebPay\Enum;

use Pixidos\GPWebPay\EnumClass\AutoInstances;
use Pixidos\GPWebPay\EnumClass\Enum;

/**
 * @method static Currency AFN()
 * @method static Currency EUR()
 * @method static Currency ALL()
 * @method static Currency DZD()
 * @method static Currency USD()
 * @method static Currency AOA()
 * @method static Currency XCD()
 * @method static Currency ARS()
 * @method static Currency AMD()
 * @method static Currency AWG()
 * @method static Currency AUD()
 * @method static Currency AZN()
 * @method static Currency BSD()
 * @method static Currency BHD()
 * @method static Currency BDT()
 * @method static Currency BBD()
 * @method static Currency BYN()
 * @method static Currency BZD()
 * @method static Currency XOF()
 * @method static Currency BMD()
 * @method static Currency INR()
 * @method static Currency BTN()
 * @method static Currency BOB()
 * @method static Currency BOV()
 * @method static Currency BAM()
 * @method static Currency BWP()
 * @method static Currency NOK()
 * @method static Currency BRL()
 * @method static Currency BND()
 * @method static Currency BGN()
 * @method static Currency BIF()
 * @method static Currency CVE()
 * @method static Currency KHR()
 * @method static Currency XAF()
 * @method static Currency CAD()
 * @method static Currency KYD()
 * @method static Currency CLP()
 * @method static Currency CLF()
 * @method static Currency CNY()
 * @method static Currency COP()
 * @method static Currency COU()
 * @method static Currency KMF()
 * @method static Currency CDF()
 * @method static Currency NZD()
 * @method static Currency CRC()
 * @method static Currency HRK()
 * @method static Currency CUP()
 * @method static Currency CUC()
 * @method static Currency ANG()
 * @method static Currency CZK()
 * @method static Currency DKK()
 * @method static Currency DJF()
 * @method static Currency DOP()
 * @method static Currency EGP()
 * @method static Currency SVC()
 * @method static Currency ERN()
 * @method static Currency ETB()
 * @method static Currency FKP()
 * @method static Currency FJD()
 * @method static Currency XPF()
 * @method static Currency GMD()
 * @method static Currency GEL()
 * @method static Currency GHS()
 * @method static Currency GIP()
 * @method static Currency GTQ()
 * @method static Currency GBP()
 * @method static Currency GNF()
 * @method static Currency GYD()
 * @method static Currency HTG()
 * @method static Currency HNL()
 * @method static Currency HKD()
 * @method static Currency HUF()
 * @method static Currency ISK()
 * @method static Currency IDR()
 * @method static Currency XDR()
 * @method static Currency IRR()
 * @method static Currency IQD()
 * @method static Currency ILS()
 * @method static Currency JMD()
 * @method static Currency JPY()
 * @method static Currency JOD()
 * @method static Currency KZT()
 * @method static Currency KES()
 * @method static Currency KPW()
 * @method static Currency KRW()
 * @method static Currency KWD()
 * @method static Currency KGS()
 * @method static Currency LAK()
 * @method static Currency LBP()
 * @method static Currency LSL()
 * @method static Currency ZAR()
 * @method static Currency LRD()
 * @method static Currency LYD()
 * @method static Currency CHF()
 * @method static Currency MOP()
 * @method static Currency MKD()
 * @method static Currency MGA()
 * @method static Currency MWK()
 * @method static Currency MYR()
 * @method static Currency MVR()
 * @method static Currency MRU()
 * @method static Currency MUR()
 * @method static Currency XUA()
 * @method static Currency MXN()
 * @method static Currency MXV()
 * @method static Currency MDL()
 * @method static Currency MNT()
 * @method static Currency MAD()
 * @method static Currency MZN()
 * @method static Currency MMK()
 * @method static Currency NAD()
 * @method static Currency NPR()
 * @method static Currency NIO()
 * @method static Currency NGN()
 * @method static Currency OMR()
 * @method static Currency PKR()
 * @method static Currency PAB()
 * @method static Currency PGK()
 * @method static Currency PYG()
 * @method static Currency PEN()
 * @method static Currency PHP()
 * @method static Currency PLN()
 * @method static Currency QAR()
 * @method static Currency RON()
 * @method static Currency RUB()
 * @method static Currency RWF()
 * @method static Currency SHP()
 * @method static Currency WST()
 * @method static Currency STN()
 * @method static Currency SAR()
 * @method static Currency RSD()
 * @method static Currency SCR()
 * @method static Currency SLL()
 * @method static Currency SGD()
 * @method static Currency XSU()
 * @method static Currency SBD()
 * @method static Currency SOS()
 * @method static Currency SSP()
 * @method static Currency LKR()
 * @method static Currency SDG()
 * @method static Currency SRD()
 * @method static Currency SZL()
 * @method static Currency SEK()
 * @method static Currency CHE()
 * @method static Currency CHW()
 * @method static Currency SYP()
 * @method static Currency TWD()
 * @method static Currency TJS()
 * @method static Currency TZS()
 * @method static Currency THB()
 * @method static Currency TOP()
 * @method static Currency TTD()
 * @method static Currency TND()
 * @method static Currency TRY()
 * @method static Currency TMT()
 * @method static Currency UGX()
 * @method static Currency UAH()
 * @method static Currency AED()
 * @method static Currency USN()
 * @method static Currency UYU()
 * @method static Currency UYI()
 * @method static Currency UYW()
 * @method static Currency UZS()
 * @method static Currency VUV()
 * @method static Currency VES()
 * @method static Currency VND()
 * @method static Currency YER()
 * @method static Currency ZMW()
 * @method static Currency ZWL()
 * @method static Currency XBA()
 * @method static Currency XBB()
 * @method static Currency XBC()
 * @method static Currency XBD()
 * @method static Currency XTS()
 * @method static Currency XXX()
 * @method static Currency XAU()
 * @method static Currency XPD()
 * @method static Currency XPT()
 * @method static Currency XAG()
 */
final class Currency extends Enum {
    use AutoInstances;

    const AFN = '971';
    // Afghani
    const EUR = '978';
    // Euro
    const ALL = '008';
    // Lek
    const DZD = '012';
    // Algerian Dinar
    const USD = '840';
    // US Dollar
    const AOA = '973';
    // Kwanza
    const XCD = '951';
    // East Caribbean Dollar
    const ARS = '032';
    // Argentine Peso
    const AMD = '051';
    // Armenian Dram
    const AWG = '533';
    // Aruban Florin
    const AUD = '036';
    // Australian Dollar
    const AZN = '944';
    // Azerbaijan Manat
    const BSD = '044';
    // Bahamian Dollar
    const BHD = '048';
    // Bahraini Dinar
    const BDT = '050';
    // Taka
    const BBD = '052';
    // Barbados Dollar
    const BYN = '933';
    // Belarusian Ruble
    const BZD = '084';
    // Belize Dollar
    const XOF = '952';
    // CFA Franc BCEAO
    const BMD = '060';
    // Bermudian Dollar
    const INR = '356';
    // Indian Rupee
    const BTN = '064';
    // Ngultrum
    const BOB = '068';
    // Boliviano
    const BOV = '984';
    // Mvdol
    const BAM = '977';
    // Convertible Mark
    const BWP = '072';
    // Pula
    const NOK = '578';
    // Norwegian Krone
    const BRL = '986';
    // Brazilian Real
    const BND = '096';
    // Brunei Dollar
    const BGN = '975';
    // Bulgarian Lev
    const BIF = '108';
    // Burundi Franc
    const CVE = '132';
    // Cabo Verde Escudo
    const KHR = '116';
    // Riel
    const XAF = '950';
    // CFA Franc BEAC
    const CAD = '124';
    // Canadian Dollar
    const KYD = '136';
    // Cayman Islands Dollar
    const CLP = '152';
    // Chilean Peso
    const CLF = '990';
    // Unidad de Fomento
    const CNY = '156';
    // Yuan Renminbi
    const COP = '170';
    // Colombian Peso
    const COU = '970';
    // Unidad de Valor Real
    const KMF = '174';
    // Comorian Franc
    const CDF = '976';
    // Congolese Franc
    const NZD = '554';
    // New Zealand Dollar
    const CRC = '188';
    // Costa Rican Colon
    const HRK = '191';
    // Kuna
    const CUP = '192';
    // Cuban Peso
    const CUC = '931';
    // Peso Convertible
    const ANG = '532';
    // Netherlands Antillean Guilder
    const CZK = '203';
    // Czech Koruna
    const DKK = '208';
    // Danish Krone
    const DJF = '262';
    // Djibouti Franc
    const DOP = '214';
    // Dominican Peso
    const EGP = '818';
    // Egyptian Pound
    const SVC = '222';
    // El Salvador Colon
    const ERN = '232';
    // Nakfa
    const ETB = '230';
    // Ethiopian Birr
    const FKP = '238';
    // Falkland Islands Pound
    const FJD = '242';
    // Fiji Dollar
    const XPF = '953';
    // CFP Franc
    const GMD = '270';
    // Dalasi
    const GEL = '981';
    // Lari
    const GHS = '936';
    // Ghana Cedi
    const GIP = '292';
    // Gibraltar Pound
    const GTQ = '320';
    // Quetzal
    const GBP = '826';
    // Pound Sterling
    const GNF = '324';
    // Guinean Franc
    const GYD = '328';
    // Guyana Dollar
    const HTG = '332';
    // Gourde
    const HNL = '340';
    // Lempira
    const HKD = '344';
    // Hong Kong Dollar
    const HUF = '348';
    // Forint
    const ISK = '352';
    // Iceland Krona
    const IDR = '360';
    // Rupiah
    const XDR = '960';
    // SDR (Special Drawing Right)
    const IRR = '364';
    // Iranian Rial
    const IQD = '368';
    // Iraqi Dinar
    const ILS = '376';
    // New Israeli Sheqel
    const JMD = '388';
    // Jamaican Dollar
    const JPY = '392';
    // Yen
    const JOD = '400';
    // Jordanian Dinar
    const KZT = '398';
    // Tenge
    const KES = '404';
    // Kenyan Shilling
    const KPW = '408';
    // North Korean Won
    const KRW = '410';
    // Won
    const KWD = '414';
    // Kuwaiti Dinar
    const KGS = '417';
    // Som
    const LAK = '418';
    // Lao Kip
    const LBP = '422';
    // Lebanese Pound
    const LSL = '426';
    // Loti
    const ZAR = '710';
    // Rand
    const LRD = '430';
    // Liberian Dollar
    const LYD = '434';
    // Libyan Dinar
    const CHF = '756';
    // Swiss Franc
    const MOP = '446';
    // Pataca
    const MKD = '807';
    // Denar
    const MGA = '969';
    // Malagasy Ariary
    const MWK = '454';
    // Malawi Kwacha
    const MYR = '458';
    // Malaysian Ringgit
    const MVR = '462';
    // Rufiyaa
    const MRU = '929';
    // Ouguiya
    const MUR = '480';
    // Mauritius Rupee
    const XUA = '965';
    // ADB Unit of Account
    const MXN = '484';
    // Mexican Peso
    const MXV = '979';
    // Mexican Unidad de Inversion (UDI)
    const MDL = '498';
    // Moldovan Leu
    const MNT = '496';
    // Tugrik
    const MAD = '504';
    // Moroccan Dirham
    const MZN = '943';
    // Mozambique Metical
    const MMK = '104';
    // Kyat
    const NAD = '516';
    // Namibia Dollar
    const NPR = '524';
    // Nepalese Rupee
    const NIO = '558';
    // Cordoba Oro
    const NGN = '566';
    // Naira
    const OMR = '512';
    // Rial Omani
    const PKR = '586';
    // Pakistan Rupee
    const PAB = '590';
    // Balboa
    const PGK = '598';
    // Kina
    const PYG = '600';
    // Guarani
    const PEN = '604';
    // Sol
    const PHP = '608';
    // Philippine Peso
    const PLN = '985';
    // Zloty
    const QAR = '634';
    // Qatari Rial
    const RON = '946';
    // Romanian Leu
    const RUB = '643';
    // Russian Ruble
    const RWF = '646';
    // Rwanda Franc
    const SHP = '654';
    // Saint Helena Pound
    const WST = '882';
    // Tala
    const STN = '930';
    // Dobra
    const SAR = '682';
    // Saudi Riyal
    const RSD = '941';
    // Serbian Dinar
    const SCR = '690';
    // Seychelles Rupee
    const SLL = '694';
    // Leone
    const SGD = '702';
    // Singapore Dollar
    const XSU = '994';
    // Sucre
    const SBD = '090';
    // Solomon Islands Dollar
    const SOS = '706';
    // Somali Shilling
    const SSP = '728';
    // South Sudanese Pound
    const LKR = '144';
    // Sri Lanka Rupee
    const SDG = '938';
    // Sudanese Pound
    const SRD = '968';
    // Surinam Dollar
    const SZL = '748';
    // Lilangeni
    const SEK = '752';
    // Swedish Krona
    const CHE = '947';
    // WIR Euro
    const CHW = '948';
    // WIR Franc
    const SYP = '760';
    // Syrian Pound
    const TWD = '901';
    // New Taiwan Dollar
    const TJS = '972';
    // Somoni
    const TZS = '834';
    // Tanzanian Shilling
    const THB = '764';
    // Baht
    const TOP = '776';
    // Pa’anga
    const TTD = '780';
    // Trinidad and Tobago Dollar
    const TND = '788';
    // Tunisian Dinar
    //const TRY = '949';
    // Turkish Lira
    const TMT = '934';
    // Turkmenistan New Manat
    const UGX = '800';
    // Uganda Shilling
    const UAH = '980';
    // Hryvnia
    const AED = '784';
    // UAE Dirham
    const USN = '997';
    // US Dollar (Next day)
    const UYU = '858';
    // Peso Uruguayo
    const UYI = '940';
    // Uruguay Peso en Unidades Indexadas (UI)
    const UYW = '927';
    // Unidad Previsional
    const UZS = '860';
    // Uzbekistan Sum
    const VUV = '548';
    // Vatu
    const VES = '928';
    // Bolívar Soberano
    const VND = '704';
    // Dong
    const YER = '886';
    // Yemeni Rial
    const ZMW = '967';
    // Zambian Kwacha
    const ZWL = '932';
    // Zimbabwe Dollar
    const XBA = '955';
    // Bond Markets Unit European Composite Unit (EURCO)
    const XBB = '956';
    // Bond Markets Unit European Monetary Unit (E.M.U.-6)
    const XBC = '957';
    // Bond Markets Unit European Unit of Account 9 (E.U.A.-9)
    const XBD = '958';
    // Bond Markets Unit European Unit of Account 17 (E.U.A.-17)
    const XTS = '963';
    // Codes specifically reserved for testing purposes
    const XXX = '999';
    // The codes assigned for transactions where no currency is involved
    const XAU = '959';
    // Gold
    const XPD = '964';
    // Palladium
    const XPT = '962';
    // Platinum
    const XAG = '961';
    // Silver
}
