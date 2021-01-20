<?php

namespace CrudBundle\View\ViewModel;

use CrudBundle\Interfaces\FormDataMapper;
use CrudBundle\View\ViewModel;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class FormViewModel extends ViewModel
{
    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Request|null
     */
    private $request;

    public function __construct(
        FormFactoryInterface $formFactory,
        ContainerInterface $container,
        RequestStack $requestStack
    ) {
        parent::__construct();

        $this->formFactory = $formFactory;
        $this->container = $container;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getVariable($name, $default = null)
    {
        if ('form' === $name) {
            $formType = parent::getOption('form_type');
            if (empty($formType)) {
                throw new InvalidArgumentException(sprintf('Argument options[form_type] required for %s', get_class($this)));
            }

            $formDataMapper = parent::getOption('form_data_mapper');
            if (true === $this->container->has($formDataMapper)) {
                $formDataMapper = $this->container->get($formDataMapper);
            }

            $data = parent::getVariable('data', []);
            $result = parent::getVariable('result', []);

            if (is_object($result) && $formDataMapper instanceof FormDataMapper) {
                $result = $formDataMapper->prepareDataToForm($result);
            }
            $formData = (!empty($data)) ? $data : $result;

            $formName = parent::getOption('form_name', '');
            $formOptions = parent::getOption('form_options', []);

            $this->form = $this->formFactory->createNamed($formName, $formType, $formData, $formOptions);
            $this->form->handleRequest($this->request);

            return $this->form->createView();
        }

        return parent::getVariable($name, $default);
    }
}
