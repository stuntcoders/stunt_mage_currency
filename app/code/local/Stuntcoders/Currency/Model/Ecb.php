<?php

class Stuntcoders_Currency_Model_Ecb extends Mage_Directory_Model_Currency_Import_Abstract
{
    const RATES_XML_URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    protected $_messages = array();

    /**
     * @var SimpleXMLElement
     */
    protected $_rates;

    /**
     * @param   string $currencyFrom
     * @param   string $currencyTo
     * @return  float
     */
    protected function _convert($currencyFrom, $currencyTo)
    {
        try {
            $this->_rates = $this->_fetchRates();
            if ($this->_rates) {
                return 1 / $this->_getRateFor($currencyFrom) * $this->_getRateFor($currencyTo);
            }
        } catch (Exception $e) {
            $this->_messages[] = Mage::helper('adminhtml')->__('Cannot retrieve rate from ECB.');
            Mage::logException($e);
        }

        return 1.0;
    }

    /**
     * @param string $currency
     * @return float
     */
    protected function _getRateFor($currency)
    {
        $nodes = $this->_rates->xpath("//*[@currency='{$currency}']");
        if (!is_array($nodes) || !($node = reset($nodes))) {
            return 1.0;
        }

        return (float) $node->attributes()->rate;
    }

    /**
     * @return SimpleXMLElement
     */
    protected function _fetchRates()
    {
        return simplexml_load_file(self::RATES_XML_URL, null, LIBXML_NOERROR);
    }
}
