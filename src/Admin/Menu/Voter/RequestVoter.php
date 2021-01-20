<?php

namespace App\Admin\Menu\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestVoter implements VoterInterface
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    public function matchItem(ItemInterface $item): ?bool
    {
        if ($item->getUri() === $this->request->getRequestUri()) {
            // URL's completely match
            return true;
        } elseif ('/admin' !== $item->getUri() && (substr($this->request->getRequestUri(), 0, strlen($item->getUri())) === $item->getUri())) {
            // URL isn't just "/" and the first part of the URL match
            return true;
        }

        if ('/admin/users/user/list' === $item->getUri() && false !== strpos($this->request->getRequestUri(), '/admin/users/user')) {
            return true;
        }

        if ('/admin/clients/client/list' === $item->getUri() && false !== strpos($this->request->getRequestUri(), '/admin/clients/client')) {
            return true;
        }

        if ('/admin/clients/user/list' === $item->getUri() && false !== strpos($this->request->getRequestUri(), '/admin/clients/user')) {
            return true;
        }
        if ('/admin/clients/card/list' === $item->getUri() && false !== strpos($this->request->getRequestUri(), '/admin/clients/card')) {
            return true;
        }

        return null;
    }
}
