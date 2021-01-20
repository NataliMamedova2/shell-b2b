<?php

namespace App\Admin\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MenuBuilder 
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    private $tokenStorage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $authorizationChecker,
        TranslatorInterface $translator,
        TokenStorageInterface $tokenStorage
    ) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(
                [
                    'id' => 'side-menu',
                    'class' => 'nav',
                ]
            );

        $menu
            ->addChild(
                'menu.dashboard',
                [
                    'route' => 'admin_homepage',
                ]
            )
            ->setExtras(
                [
                    'icon' => '<i class="fa fa-home"></i>',
                ]
            )
            ->setLabel($this->translator->trans('menu.dashboard_label'));
        
        //        $clients = $menu
        //            ->addChild('menu.clients_label', [
        //                'uri' => '#',
        //            ])
        //            ->setExtras([
        //                'icon' => '<i class="fa fa-briefcase"></i>',
        //            ])
        //            ->setLabel($this->translator->trans('menu.clients_label'));

        $menu
            ->addChild(
                'menu.companies_list',
                [
                    'route' => 'admin_clients_client_list',
                ]
            )
            ->setExtras(
                [
                    'icon' => '<i class="fa fa-briefcase"></i>',
                ]
            )
            ->setLabel($this->translator->trans('menu.companies_label'));

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')
            || $this->authorizationChecker->isGranted('ROLE_ADMIN')
            || $this->authorizationChecker->isGranted('ROLE_MANAGER')
            || $this->authorizationChecker->isGranted('ROLE_MANAGER_CALL_CENTER')
        ) {
            $menu
                ->addChild(
                    'menu.users_list',
                    [
                        'route' => 'admin_clients_user_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-users"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.clients_users'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN') ||
            $this->authorizationChecker->isGranted('ROLE_ADMIN')
        ) {
            $menu
                ->addChild(
                    'menu.partners_list',
                    [
                        'route' => 'admin_partners_user_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-users"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.partners_users'));
        }
        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') ||
            $this->authorizationChecker->isGranted('ROLE_ADMIN') ||
            $this->authorizationChecker->isGranted('ROLE_MANAGER') ||
            $this->authorizationChecker->isGranted('ROLE_MANAGER_CALL_CENTER')
        ) {
            $menu
                ->addChild(
                    'menu.clients_card_list',
                    [
                        'route' => 'admin_clients_card_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-credit-card"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.clients_cards'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')
            || $this->authorizationChecker->isGranted('ROLE_ADMIN')
            || $this->authorizationChecker->isGranted('ROLE_MANAGER')
            || $this->authorizationChecker->isGranted('ROLE_MANAGER_CALL_CENTER')
        ) {
            $menu
                ->addChild(
                    'menu.transactions_label',
                    [
                        'route' => 'admin_transaction_card_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-list"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.transactions_label'));

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')
                || $this->authorizationChecker->isGranted('ROLE_MANAGER')
            ) {
                $user = $this->tokenStorage->getToken()->getUser();

                if (false === in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                    $menu
                        ->addChild(
                            'menu.documents_esp',
                            [
                                'route' => 'admin_users_documents_esp',
                            ]
                        )
                        ->setExtras(
                            [
                                'icon' => '<i class="fa fa-files-o"></i>',
                            ]
                        )
                        ->setLabel($this->translator->trans('menu.documents_esp'));
                }
            }
            $menu
                ->addChild(
                    'menu.feedback_label',
                    [
                        'route' => 'admin_feedback_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-link"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.feedback_label'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu
                ->addChild(
                    'menu.fuel_replacement',
                    [
                        'route' => 'admin_fuel_replacement_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-files-o"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.fuel_replacement'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu
                ->addChild(
                    'menu.users_label',
                    [
                        'route' => 'admin_users_user_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-users"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.user_label'));
        }

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN') || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu
                ->addChild(
                    'menu.translations_label',
                    [
                        'route' => 'admin_translations_list',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-language"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.translations_label'));

            $apiMenu = $menu
                ->addChild(
                    'menu.api_label',
                    [
                        'uri' => '#',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-exchange"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.api_label'));

            $apiMenu
                ->addChild(
                    'menu.api_doc.label',
                    [
                        'route' => 'admin_api_doc',
                    ]
                )
                ->setExtras(
                    [
                        'icon' => '<i class="fa fa-file-text"></i>',
                    ]
                )
                ->setLabel($this->translator->trans('menu.api_doc_label'));

            if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
                $apiMenu
                    ->addChild(
                        'menu.api_log.label',
                        [
                            'route' => 'admin_api_log_list',
                        ]
                    )
                    ->setExtras(
                        [
                            'icon' => '<i class="fa fa-bug"></i>',
                        ]
                    )
                    ->setLabel($this->translator->trans('menu.api_log_label'));

                $menu
                    ->addChild(
                        'menu.import_files.label',
                        [
                            'route' => 'admin_import_files_list',
                        ]
                    )
                    ->setExtras(
                        [
                            'icon' => '<i class="fa fa-recycle"></i>',
                        ]
                    )
                    ->setLabel($this->translator->trans('menu.import_files'));
            }
        }

        return $menu;
    }
}
