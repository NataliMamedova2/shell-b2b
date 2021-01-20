<?php
namespace App\Partners\Domain\Document\UseCase\ActChecking;

use App\Partners\Application\Validator\Constraints\PartnerExist as PartnerEntityExist;
use App\Partners\Domain\Partner\Partner;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Partner
     *
     * @Assert\NotBlank()
     * @PartnerEntityExist()
     */
    private $partner;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\DateTime(format="Y-m")
     */
    private $dateFrom;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\DateTime(format="Y-m")
     */
    private $dateTo;

    public function __construct(Partner $partner, string $dateFrom, string $dateTo)
    {
        $this->partner = $partner;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function getPartner(): Partner
    {
        return $this->partner;
    }

    public function getDateFrom(): \DateTimeImmutable
    {
        $dateFrom = new \DateTimeImmutable($this->dateFrom);

        return new \DateTimeImmutable($dateFrom->format('Y-m-01 00:00:00'));
    }

    public function getDateTo(): \DateTimeImmutable
    {
        $dateTo = new \DateTimeImmutable($this->dateTo);

        $endOfMonth = new \DateTimeImmutable($dateTo->format('Y-m-t 23:59:59'));
        $today = new \DateTimeImmutable();
        if ($today->format('n') === $dateTo->format('n') &&
            $today->format('j') < $endOfMonth->format('j')
        ) {
            return new \DateTimeImmutable($today->format('Y-m-d 23:59:59'));
        }

        return $endOfMonth;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     * @throws \Exception
     */
    public function validate(ExecutionContextInterface $context)
    {
        $validator = $context->getValidator();

        $dateFromViolations = $validator->validateProperty($this, 'dateFrom');
        $dateToViolations = $validator->validateProperty($this, 'dateTo');

        if (0 === $dateFromViolations->count() && 0 === $dateToViolations->count()) {
            $dateFrom = new \DateTime($this->dateFrom);
            $dateTo = new \DateTime($this->dateTo);

            if ($dateFrom->getTimestamp() > $dateTo->getTimestamp()) {
                $context->buildViolation('This value should be greater than {{ dateFrom }}')
                    ->atPath('dateTo')
                    ->setParameter('{{ dateFrom }}', $dateFrom->format('Y-m'))
                    ->addViolation();

                return;
            }
            $today = new \DateTimeImmutable();
            if ($dateTo->getTimestamp() > $today->getTimestamp()) {
                $context->buildViolation('This value should be less than or equal to {{ today }}.')
                    ->atPath('dateTo')
                    ->setParameter('{{ today }}', $today->format('Y-m'))
                    ->addViolation();

                return;
            }
        }
    }
}
