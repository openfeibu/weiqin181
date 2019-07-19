<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator          |
    |              on 2018-07-17 15:20:03              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace MmqmMarket\controller\mobile; defined("\111\116\x5f\x49\101") || die("\101\x63\143\145\x73\163\40\x44\145\x6e\151\x65\x64"); class MemberCenter extends \MmqmMarket\controller\BaseController { public function __construct($Site, $arguments) { goto I1YzM; JSdqw: method_exists($this, $GLOBALS["\137\107\x50\103"]["\x61\x63"]) && ($this->{$GLOBALS["\137\107\120\103"]["\x61\x63"]}() && die || die); goto kyiIU; T_Sjc: $GLOBALS["\120\101\x52\x41\115\x53"] = array("\155\x65\x6d\x62\x65\x72" => mc_fansinfo($GLOBALS["\137\x57"]["\x66\141\x6e\163"]["\x6f\160\x65\156\151\x64"])); goto E929p; I1YzM: parent::__construct($Site, $arguments); goto JSdqw; kyiIU: load()->model("\155\143"); goto T_Sjc; E929p: include $this->template(); goto dErFi; dErFi: } private function tabulation() { } }