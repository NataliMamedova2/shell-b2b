<?php

namespace App\Clients\Domain\Driver\UseCase\Update;

use App\Clients\Domain\Driver\ValueObject\DriverId;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;
use App\Clients\Application\Validator\Constraints as ClientsAssert;
use App\Application\Validator\Constraints as AppAssert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var DriverId
     */
    private $driverId;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
     * @ClientsAssert\Name()
     */
    public $firstName;

    /**
     * @Assert\Length(max=30)
     * @ClientsAssert\DriverMiddleName()
     * @ClientsAssert\Name()
     */
    public $middleName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=30)
     * @ClientsAssert\Name()
     */
    public $lastName;

    /**
     * @var string|null
     *
     * @Assert\Length(max=64)
     * @AppAssert\Email(mode="strict")
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"\App\Clients\Domain\Driver\ValueObject\Status", "getNames"})
     */
    public $status;

    /**
     * @var string|null
     *
     * @Assert\Length(max=250)
     */
    public $note;

    /**
     * @Assert\Count(min="1")
     * @Assert\Unique(),
     * @Assert\All(constraints={
     *     @Assert\Collection(
     *         fields = {
     *              "number" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\Length(13),
     *                  @Assert\Regex(pattern="/^\+[0-9]{0,12}$/")
     *              }
     *          }
     *     )
     * })
     */
    public $phones = [];

    /**
     * @Assert\Unique(),
     * @Assert\All(constraints={
     *     @Assert\Collection(
     *         fields = {
     *              "number" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\Length(max="12")
     *              }
     *          }
     *     )
     * })
     */
    public $carsNumbers = [];

    public function __construct(DriverId $id)
    {
        $this->driverId = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->driverId->getId();
    }
}
