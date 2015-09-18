<?php
namespace Bitpay\Form;

use Bitpay\Bitpay;
use Bitpay\Model\BitpayConfig;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ConfigureSandboxBitpay extends BaseForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $config_data = BitpayConfig::read();
        $this->formBuilder
            ->add("pairingKey","text", array(
                'label'=>Translator::getInstance()->trans("Pairing Key"),
                'label_attr'=>array(
                    'for'=>'pairingKey'
                ),
                'data'=>(isset($config_data['pairingKey'])?$config_data['pairingKey']:""),
            ))
            ->add("apiKey","text", array(
                'label'=>Translator::getInstance()->trans("API Key"),
                'label_attr'=>array(
                    'for'=>'apiKey'
                ),
                'data'=>(isset($config_data['apiKey'])?$config_data['apiKey']:""),
            ))
            ->add("sandbox", "checkbox", array(
                'label'=>Translator::getInstance()->trans("Activate sandbox mode"),
                'label_attr'=>array(
                    'for'=>'sandbox'
                ),
                'value'=>(isset($config_data['sandbox'])?$config_data['sandbox']:"")
            ))
        ;
    }

    /**
     * @return string the name of your form. This name must be unique
     */
    public function getName()
    {
        return "configuresanboxbitpayform";
    }

}
