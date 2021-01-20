<?php

namespace App\Partners\Domain\Invoice\UseCase\CreateFromAmount;

use App\Partners\Application\Validator\Constraints\PartnerExist as PartnerEntityExist;
use App\Partners\Domain\Partner\Partner;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Partner
     *
     * @Assert\NotBlank()
     * @PartnerEntityExist()
     */
    public $partner;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="1000000")
     */
    public $amount;
}
