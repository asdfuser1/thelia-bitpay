<?php
namespace BitpayPayments;

use BitpayPayments\Model\BitpayPaymentsConfig;
use Propel\Generator\Model\Database;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Log\Tlog;
use Thelia\Model\Base\ModuleQuery;
use Thelia\Model\ModuleImageQuery;
use Thelia\Model\Order;
use Thelia\Module\BaseModule;
use Thelia\Module\PaymentModuleInterface;

class BitpayPayments extends BaseModule implements PaymentModuleInterface
{
    /* @var \Bitpay\PrivateKey */
    private $privateKey;
    /* @var \Bitpay\PublicKey */
    private $publicKey;

    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */
    /**
     *
     *  Method used by payment gateway.
     *
     *  If this method return a \Thelia\Core\HttpFoundation\Response instance, this response is send to the
     *  browser.
     *
     *  In many cases, it's necessary to send a form to the payment gateway. On your response you can return this form already
     *  completed, ready to be sent
     *
     * @param  \Thelia\Model\Order $order processed order
     * @return null|\Thelia\Core\HttpFoundation\Response
     */
    public function pay(Order $order)
    {
        $this->loadBitpayKeys();

        $client = new \Bitpay\Client\Client();
        $adapter = new \Bitpay\Client\Adapter\CurlAdapter();

        $config = new BitpayPaymentsConfig();
        $config->pushValues();
        if ($config->getSandbox()) {
            $pairingKey = $config->getPairingKeySandbox();
            $apiKey = $config->getApiKeySandbox();

            $network = new \Bitpay\Network\Testnet();
            $environment = "Sandbox";
        } else {
            $pairingKey = $config->getPairingKey();
            $apiKey = $config->getApiKey();

            $network = new \Bitpay\Network\Livenet();
            $environment = "Live";
        }

        $client->setPrivateKey($this->privateKey);
        $client->setPublicKey($this->publicKey);
        $client->setNetwork($network);
        $client->setAdapter($adapter);

        if (!isset($apiKey) || $apiKey == '') {
            // must create API key
            if (!isset($pairingKey) || $pairingKey == '') {
                // error: no pairing key
                $error = "Thelia BitpayPayments error: No API key or pairing key for environment $environment provided.";
                Tlog::getInstance()->error($error);
                throw new \Exception($error);
            } else {
                // pairing key available, now trying to get an API key
                $sin = \Bitpay\SinKey::create()->setPublicKey($this->publicKey)->generate();

                try {
                    $token = $client->createToken(array(
                        'pairingCode' => $pairingKey,
                        'label' => 'Thelia BitpayPayments',
                        'id' => (string)$sin,
                    ));
                } catch (\Exception $e) {
                    $request = $client->getRequest();
                    $response = $client->getResponse();
                    $error = 'Thelia BitpayPayments error:' . PHP_EOL . PHP_EOL . $request . PHP_EOL . PHP_EOL . $response . PHP_EOL . PHP_EOL;
                    Tlog::getInstance()->error($error);
                    throw new \Exception($error);
                }

                $config->setApiKeyCurrentEnvironment($token->getToken());
                $config->setPairingKeyCurrentEnvironment('');
            }
        }
        $config->save();

        // token should be available now
        $token = new \Bitpay\Token();
        $token->setToken($config->getApiKeyCurrentEnvironment());

        $client->setToken($token);
        $invoice = new \Bitpay\Invoice();

        $item = new \Bitpay\Item();
        $item->setCode('testCode');
        $item->setDescription('Purchase');
        $item->setPrice($order->getTotalAmount());

        $invoice->setItem($item);
        $invoice->setCurrency( new \Bitpay\Currency($order->getCurrency()->getCode()) );

        try {
            $client->createInvoice($invoice);
        } catch (\Exception $e) {
            $request = $client->getRequest();
            $response = $client->getResponse();
            $error = 'Thelia BitpayPayments error:' . PHP_EOL . PHP_EOL . $request . PHP_EOL . PHP_EOL . $response . PHP_EOL . PHP_EOL;
            Tlog::getInstance()->error($error);
            throw new \Exception($error);
        }

    }

    /**
     *  Tries to load the bitpay encryption keys. If that doesn't work, it creates new ones.
     */
    private function loadBitpayKeys()
    {
        try {
            $storageEngine = new \Bitpay\Storage\FilesystemStorage();
            $this->privateKey = $storageEngine->load(THELIA_CACHE_DIR . 'bitpay.pri');
            $this->publicKey = $storageEngine->load(THELIA_CACHE_DIR . 'bitpay.pub');
        } catch (\Exception $e) {
            $this->generateBitpayKeys();
        }
    }

    /**
     * Generates new bitpay encryption keys and saves them.
     */
    private function generateBitpayKeys()
    {
        $this->privateKey = new \Bitpay\PrivateKey(THELIA_CACHE_DIR . 'bitpay.pri');
        $this->privateKey->generate();

        $this->publicKey = new \Bitpay\PublicKey(THELIA_CACHE_DIR . 'bitpay.pub');
        $this->publicKey->generate();

        $storageEngine = new \Bitpay\Storage\FilesystemStorage();
        $storageEngine->persist($this->privateKey);
        $storageEngine->persist($this->publicKey);
    }

    /**
     *
     * This method is call on Payment loop.
     *
     * If you return true, the payment method will de display
     * If you return false, the payment method will not be display
     *
     * @return boolean
     */
    public function isValidPayment()
    {
        return true;
    }

    /**
     * if you want, you can manage stock in your module instead of order process.
     * Return false to decrease the stock when order status switch to pay
     *
     * @return bool
     */
    public function manageStockOnCreation()
    {
        return false;
    }

    public function preActivation(ConnectionInterface $con = null)
    {
        if (version_compare(Thelia::THELIA_VERSION, '2.1.0', '<')) {
            return false;
        }

        return true;
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));

        /* insert the images from image folder if first module activation */
        $module = $this->getModuleModel();
        if (ModuleImageQuery::create()->filterByModule($module)->count() == 0) {
            $this->deployImageFolder($module, sprintf('%s/images', __DIR__), $con);
        }

        /* set module title */
        $this->setTitle(
            $module,
            array(
                "de_DE" => "BitpayPayments",
                "en_US" => "BitpayPayments",
                "fr_FR" => "BitpayPayments",
            )
        );
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return "BitpayPayments";
    }

    /**
     * @return int
     */
    public static function getModCode($flag=false)
    {
        $obj = new BitpayPayments();
        $mod_code = $obj->getCode();
        if($flag) return $mod_code;
        $search = ModuleQuery::create()
            ->findOneByCode($mod_code);

        return $search->getId();
    }
}
