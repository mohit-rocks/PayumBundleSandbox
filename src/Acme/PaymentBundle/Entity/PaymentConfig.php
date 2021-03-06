<?php
namespace Acme\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\PaymentConfig as BasePaymentConfig;

/**
 * @ORM\Table(name="payum_payment_configs")
 * @ORM\Entity
 */
class PaymentConfig extends BasePaymentConfig
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
}
