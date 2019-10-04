<?php
namespace Jeff\MyShip\Model\Carrier;

use Magento\Shipping\Model\Rate\Result;

class MyShip extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
	protected $_code = 'myship';

	/** \Magento\Shipping\Model\Rate\ResultFactory */
	protected $_rateResultFactory;

	/** \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory */
	protected $_rateMethodFactory;

	protected $_logger;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
		\Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
		array $data = []
	){
		$this->_logger = $logger;
		$this->_rateResultFactory = $rateResultFactory;
		$this->_rateMethodFactory = $rateMethodFactory;

		parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
	}

	public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
	{
		if(!$this->getConfigFlag('active')) {
			return false;
		}

		$this->_logger->info($this->getConfigData('express_price'));
		$result = $this->_rateResultFactory->create();

		if($this->getConfigData('express_enabled')) {
			$method = $this->_rateMethodFactory->create();
			$method->setCarrier($this->_code);
			$method->setCarrierTitle($this->getConfigData('name'));
			$method->setMethod('express');
			$method->setMethodTitle($this->getConfigData('express_title'));
			$method->setPrice($this->getConfigData('express_price'));
			$method->setCost($this->getConfigData('express_price'));

			$result->append($method);
		}

		return $result;
	}

	public function getAllowedMethods() {
		return ['myship' => $this->getConfigData('name')];
	}

	public function isTrackingAvailable() {
		return true;
	}
}