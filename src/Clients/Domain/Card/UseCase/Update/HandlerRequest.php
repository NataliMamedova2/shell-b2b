<?php

namespace App\Clients\Domain\Card\UseCase\Update;

use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use App\Application\Validator\Constraints\EntityExist;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private $id;

    /**
     * @Assert\Collection(
     *     fields = {
     *          "day" = {
     *              @Assert\NotBlank(),
     *              @Assert\GreaterThanOrEqual(1),
     *          },
     *          "week" = {
     *              @Assert\NotBlank(),
     *              @Assert\GreaterThanOrEqual(1),
     *          },
     *          "month" = {
     *              @Assert\NotBlank(),
     *              @Assert\GreaterThanOrEqual(1),
     *          },
     *     }
     * )
     */
    public $totalLimits = [];

    /**
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    public $startUseTime;

    /**
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    public $endUseTime;

    /**
     * @Assert\Choice(multiple=true, callback={"\App\Clients\Domain\Card\ValueObject\ServiceSchedule", "getNames"})
     */
    public $serviceDays = [];

    /**
     * @Assert\Count(min="1")
     * @Assert\All(constraints={
     *     @Assert\Collection(
     *         fields = {
     *              "id" = {
     *                  @Assert\NotBlank(),
     *                  @EntityExist(repository="app.clients.infrastructure.fuel.type.repository")
     *              },
     *              "dayLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              },
     *              "weekLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              },
     *              "monthLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              }
     *          },
     *          allowExtraFields = true
     *     )
     * })
     */
    public $fuelLimits = [];

    /**
     * @Assert\All(constraints={
     *     @Assert\Collection(
     *         fields = {
     *              "id" = {
     *                  @Assert\NotBlank(),
     *                  @EntityExist(repository="app.clients.infrastructure.fuel.type.repository")
     *              },
     *              "dayLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              },
     *              "weekLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              },
     *              "monthLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              }
     *          },
     *          allowExtraFields = true
     *     )
     * })
     */
    public $goodsLimits = [];

    /**
     * @Assert\All(constraints={
     *     @Assert\Collection(
     *         fields = {
     *              "id" = {
     *                  @Assert\NotBlank(),
     *                  @EntityExist(repository="app.clients.infrastructure.fuel.type.repository")
     *              },
     *              "dayLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              },
     *              "weekLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              },
     *              "monthLimit" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\GreaterThanOrEqual(1)
     *              }
     *          },
     *          allowExtraFields = true
     *     )
     * })
     */
    public $servicesLimits = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLimits(): array
    {
        return array_merge($this->fuelLimits, $this->goodsLimits, $this->servicesLimits);
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->startUseTime instanceof \DateTimeInterface && $this->endUseTime instanceof \DateTimeInterface) {
            if ($this->startUseTime->getTimestamp() >= $this->endUseTime->getTimestamp()) {
                $context->buildViolation('This value should be greater than {{ startUseTime }}')
                    ->atPath('endUseTime')
                    ->setParameter('{{ startUseTime }}', $this->startUseTime->format('H:i'))
                    ->addViolation();
            }
        }
    }
}
