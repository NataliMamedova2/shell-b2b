<?php

namespace Tests\Unit\Security\Application\Listener;

use App\Security\Application\Listener\CabinetAuthenticationSubscriber;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CabinetAuthenticationSubscriberTest extends TestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|TranslatorInterface
     */
    private $translatorMock;
    /**
     * @var CabinetAuthenticationSubscriber
     */
    private $subscriber;
    /**
     * @var AuthenticationFailureEvent|\Prophecy\Prophecy\ObjectProphecy
     */
    private $authenticationFailureEventMock;

    protected function setUp(): void
    {
        $this->translatorMock = $this->prophesize(TranslatorInterface::class);
        $this->authenticationFailureEventMock = $this->prophesize(AuthenticationFailureEvent::class);

        $this->subscriber = new CabinetAuthenticationSubscriber($this->translatorMock->reveal());
    }

    public function testGetSubscribedEventsReturnArray(): void
    {
        $array = [
            'lexik_jwt_authentication.on_authentication_failure' => ['onAuthenticationFailureResponse'],
        ];
        $result = CabinetAuthenticationSubscriber::getSubscribedEvents();

        $this->assertEquals($array, $result);
    }

    public function testOnAuthenticationFailureResponse(): void
    {
        $exception = $this->prophesize(AuthenticationException::class);

        $this->authenticationFailureEventMock->getException()
            ->shouldBeCalled()
            ->willReturn($exception);

        $messageKey = 'messageKey';
        $exception->getMessageKey()
            ->shouldBeCalled()
            ->willReturn($messageKey);

        $messageData = [];
        $exception->getMessageData()
            ->shouldBeCalled()
            ->willReturn($messageData);

        $transMessage = '$transMessage';
        $this->translatorMock->trans($messageKey, $messageData, 'frontend')
            ->shouldBeCalled()
            ->willReturn($transMessage);

        $data = [
            'username' => [$transMessage],
        ];

        $response = new JsonResponse($data, 400);

        $this->authenticationFailureEventMock->setResponse($response)
            ->shouldBeCalled();

        $this->subscriber->onAuthenticationFailureResponse($this->authenticationFailureEventMock->reveal());
    }
}
