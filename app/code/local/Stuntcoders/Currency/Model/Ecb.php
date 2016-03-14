<?php

class Stuntcoders_Currency_Model_Ecb extends Mage_Directory_Model_Currency_Import_Abstract
{
    const RATES_XML_URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    protected $_messages = array();

    protected function _convert($currencyFrom, $currencyTo)
    {
        try {
            $rates = simplexml_load_string(file_get_contents(self::RATES_XML_URL), null, LIBXML_NOERROR);

            if ($rates) {
                $fromEurRate = 1;
                $toEurRate = 1;

                if ($nodes = $rates->xpath("//*[@currency='{$currencyFrom}']")) {
                    if ($nodes && is_array($nodes) && ($node = reset($nodes))) {
                        $fromEurRate = (float) $node->attributes()->rate;
                    }
                }

                if ($nodes = $rates->xpath("//*[@currency='{$currencyTo}']")) {
                    if ($nodes && is_array($nodes) && ($node = reset($nodes))) {
                        $toEurRate = (float) $node->attributes()->rate;
                    }
                }

                return 1 / $fromEurRate * $toEurRate;
            }
        } catch (Exception $e) {
            $this->_messages[] = Mage::helper('adminhtml')->__('Cannot retrieve rate from ECB.');
            Mage::logException($e);
        }

        return false;
    }
}
