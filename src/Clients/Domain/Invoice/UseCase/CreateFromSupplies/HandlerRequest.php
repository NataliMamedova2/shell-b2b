<?php

namespace App\Clients\Domain\Invoice\UseCase\CreateFromSupplies;

use App\Clients\Application\Validator\Constraints\ClientExist as ClientEntityExist;
use App\Clients\Domain\Client\Client;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Client
     *
     * @Assert\NotBlank()
     * @ClientEntityExist()
     */
    public $client;

    /**
     * @Assert\Count(min="1")
     * @Assert\All(constraints={
     *     @Assert\Unique(),
     *     @Assert\Collection(
     *         fields = {
     *              "id" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\Uuid()
     *              },
     *              "volume" = {
     *                  @Assert\NotBlank(),
     *                  @Assert\Range(min="1", max="50000")
     *              }
     *          }
     *     )
     * })
     */
    public $items;
}
