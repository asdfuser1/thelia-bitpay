<?php

namespace Bitpay\Model;

use Bitpay\Model\Base\BitpayConfig as BaseBitpayConfig;

class BitpayConfig extends BaseBitpayConfig
{
    protected $pairingKey = null;
    protected $apiKey = null;

    protected $sandbox = null;
    protected $pairingKey_sandbox = null;
    protected $apiKey_sandbox = null;

    protected function getDbValues($keysflag=true)
    {
        $pks = $this->getThisVars();
        if ($keysflag) {
            $pks = array_keys($pks);
        }
        $query = BitpayConfigQuery::create()->findPks($pks);
        return $query;
    }

    /**
     * @param  null  $file
     * @return array
     */
    public static function read()
    {
        $pks = self::getSelfVars();

        return $pks;
    }

    /**
     * @return array
     */
    public static function getSelfVars()
    {
        $obj = new BitpayConfig();
        $obj->pushValues();
        $this_class_vars = get_object_vars($obj);
        $base_class_vars = get_class_vars("\\Bitpay\\Model\\Base\\BitpayConfig");
        $pks = array_diff_key($this_class_vars, $base_class_vars);

        return $pks;
    }

    /**
     * @return array
     */
    protected function getThisVars()
    {
        $this_class_vars = get_object_vars($this);
        $base_class_vars = get_class_vars("\\Bitpay\\Model\\Base\\BitpayConfig");
        $pks = array_diff_key($this_class_vars, $base_class_vars);

        return $pks;
    }

    public function pushValues()
    {
        $query = $this->getDbValues();
        foreach ($query as $var) {
            /** @var BitpayConfig $var */
            $name = $var->getName();
            if ($this->$name === null) {
                $this->$name = $var->getValue();
            }
        }
    }

    /**
     * @param null $file
     */
    public function write()
    {
        $dbvals = $this->getDbValues();
        $isnew=array();
        foreach ($dbvals as $var) {
            /** @var BitpayConfig $var */
            $isnew[$var->getName()] = true;
        }
        $this->pushValues();
        $vars=$this->getThisVars();
        foreach ($vars as $key=>$value) {
            $tmp = new BitpayConfig();
            $tmp->setNew(!isset($isnew[$key]));
            $tmp->setName($key);
            $tmp->setValue($value);
            $tmp->save();
        }
    }

    /**
     * @return string
     */
    public function getPairingKey(){
        return $this->pairingKey;
    }

    /**
     * @param  string                        $pairingKey
     * @return \Bitpay\Model\BitpayConfig    $this
     */
    public function setPairingKey($pairingKey){
        $this->pairingKey = $pairingKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey(){
        return $this->apiKey;
    }

    /**
     * @param  string                        $apiKey
     * @return \Bitpay\Model\BitpayConfig    $this
     */
    public function setApiKey($apiKey){
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getSandbox(){
        return $this->sandbox;
    }

    /**
     * @param  string                        $sandbox
     * @return \Bitpay\Model\BitpayConfig    $this
     */
    public function setSandbox($sandbox){
        $this->sandbox = $sandbox;
        return $this;
    }

    /**
     * @return string
     */
    public function getPairingKeySandbox(){
        return $this->pairingKey_sandbox;
    }

    /**
     * @param  string                        $pairingKey_sandbox
     * @return \Bitpay\Model\BitpayConfig    $this
     */
    public function setPairingKeySandbox($pairingKey_sandbox){
        $this->pairingKey_sandbox = $pairingKey_sandbox;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKeySandbox(){
        return $this->apiKey_sandbox;
    }

    /**
     * @param  string                        $apiKey_sandbox
     * @return \Bitpay\Model\BitpayConfig    $this
     */
    public function setApiKeySandbox($apiKey_sandbox){
        $this->apiKey_sandbox = $apiKey_sandbox;
        return $this;
    }



    public function getApiKeyCurrentEnvironment() {
        if ($this->getSandbox())
            return $this->getApiKeySandbox();
        else
            return $this->getApiKey();
    }
    public function getPairingKeyCurrentEnvironment() {
        if ($this->getSandbox())
            return $this->getPairingKeySandbox();
        else
            return $this->getPairingKey();
    }

    public function setApiKeyCurrentEnvironment($apiKey) {
        if ($this->getSandbox())
            return $this->setApiKeySandbox($apiKey);
        else
            return $this->setApiKey($apiKey);
    }
    public function setPairingKeyCurrentEnvironment($pairingKey) {
        if ($this->getSandbox())
            return $this->setPairingKeySandbox($pairingKey);
        else
            return $this->setPairingKey($pairingKey);
    }
}
