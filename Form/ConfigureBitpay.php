<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
namespace Bitpay\Form;

use Bitpay\Model\BitpayConfig;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ConfigureBitpay extends BaseForm
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
        $config_data=BitpayConfig::read();
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

        ;
    }

    /**
     * @return string the name of your form. This name must be unique
     */
    public function getName()
    {
        return "configurebitpayform";
    }

}
