<?php

namespace CrudBundle\View;

use Psr\Container\ContainerInterface;

class ViewBuilder
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(Config $config, ContainerInterface $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * @param $config
     * @param array $data
     *
     * @return array|object|ViewModel
     */
    public function build($config, array $data = [])
    {
        $allOptions = $this->config->getOptions();

        $i = 0;
        $queue = new \SplQueue();
        $queue->enqueue($config);

        while ($queue->count() > 0) {
            $options = $queue->dequeue();
            $options = $this->config->applyInheritance($options);

            if (isset($options['view_model'])) {
                $viewModel = $this->container->get($options['view_model']);
            } else {
                $viewModel = new ViewModel();
            }
            if (0 == $i) {
                $rootViewModel = $viewModel;
            }

            $viewModel->setOptions($options['options'] ?? []);
            $viewModel->setTemplate($options['template'] ?? '');

            if (isset($options['capture'])) {
                $viewModel->setCaptureTo($options['capture']);
            } elseif (isset($options['id'])) {
                $viewModel->setCaptureTo($options['id']);
            }

            if (isset($options['data']['static'])) {
                $viewModel->setVariables($options['data']['static']);
            }

            if (isset($options['data']['global'])) {
                $globalVar = $options['data']['global'];
                unset($options['data']['global']);

                if (is_array($globalVar)) {
                    foreach ($globalVar as $globalVarName => $viewVarName) {
                        $globalVarValue = $this->getVarValue($globalVarName, $data);
                        $viewModel->setVariable($viewVarName, $globalVarValue);
                    }
                } else {
                    $globalVarValue = $this->getVarValue($globalVar, $data);
                    $viewModel->setVariable($globalVar, $globalVarValue);
                }
            }
            if (isset($options['parent'])) {
                /** @var ViewModel $parent */
                $parent = $options['parent'];
                $parent->addChild($viewModel, $viewModel->captureTo(), true);

                if (isset($options['data']['parent'])) {
                    $varFromParent = $options['data']['parent'];
                    $parentVars = $parent->getVariables();

                    if (is_array($varFromParent)) {
                        foreach ($varFromParent as $varFromParentName => $viewVarName) {
                            $fromParentVal = $this->getVarValue($varFromParentName, $parentVars);

                            if (null === $fromParentVal) {
                                $fromParentVal = $parent->getVariable($varFromParentName);
                            }

                            if (is_array($viewVarName)) {
                                $dataFromParent = [];
                                foreach ($viewVarName as $varName) {
                                    $dataFromParent[$varName] = $fromParentVal;
                                }
                            } else {
                                $dataFromParent = [$viewVarName => $fromParentVal];
                            }

                            $viewModel->setVariables($dataFromParent);
                        }
                    } else {
                        $viewVarName = $options['data']['parent'];
                        $fromParentVal = $this->getVarValue($viewVarName, $parentVars);

                        if (null === $fromParentVal) {
                            $fromParentVal = $parent->getVariable($viewVarName);
                        }

                        $viewModel->setVariables([$viewVarName => $fromParentVal]);
                    }
                }
            }

            if (!empty($options['children'])) {
                foreach ($options['children'] as $childId => $child) {
                    if (is_string($child)) {
                        $childId = $child;
                        $child = $allOptions[$child];
                    }

                    if (isset($options['childrenDynamicLists'][$childId])) {
                        continue;
                    }

                    $child['id'] = $childId;
                    $child['parent'] = $viewModel;

                    $queue->enqueue($child);
                }
            }

            if (isset($options['childrenDynamicLists'])) {
                foreach ($options['childrenDynamicLists'] as $childName => $listName) {
                    $list = $viewModel->getVariable($listName);

                    if (null === $list) {
                        throw new \UnexpectedValueException("Cannot build children list of '$childName' by '$listName' list . View does not contain variable '$listName'.");
                    }
                    if (!is_array($list) && !($list instanceof \Traversable)) {
                        throw new \UnexpectedValueException("Cannot build children list of '$childName' by '$listName' list . List '$listName' must be array ".gettype($list).' given.');
                    }

                    if (array_key_exists($childName, $options['children'])) {
                        $child = $options['children'][$childName];
                    } else {
                        if (in_array($childName, $options['children'])) {
                            $child = $allOptions[$childName];
                        } else {
                            throw new \UnexpectedValueException("Cannot build children list of '$childName' by '$listName' list . Child '$childName' not found");
                        }
                    }

                    $child['id'] = $childName;
                    $child['parent'] = $viewModel;
                    if (isset($child['data']['parent'])) {
                        $varFromParent = $child['data']['parent'];
                    }

                    foreach ($list as $entry) {
                        if (isset($varFromParent)) {
                            if (is_array($varFromParent)) {
                                foreach ($varFromParent as $varFromParentName => $viewVarName) {
                                    $dataForChild = [$viewVarName => $entry];
                                }
                            } else {
                                $dataForChild = [$varFromParent => $entry];
                            }

                            if (!isset($child['data']['static'])) {
                                $child['data']['static'] = [];
                            }

                            $child['data']['static'] = array_merge($child['data']['static'], $dataForChild);
                            unset($child['data']['parent']);
                        }

                        $queue->enqueue($child);
                    }
                }
            }

            ++$i;
        }

        return $rootViewModel;
    }

    /**
     * @param string $varName
     * @param array  $data
     *
     * @return mixed
     */
    private function getVarValue($varName, $data)
    {
        if (false !== strpos($varName, ':')) {
            list($varArrayName, $varNameInArray) = explode(':', $varName);

            if (isset($data[$varArrayName][$varNameInArray])) {
                return $data[$varArrayName][$varNameInArray];
            }
        }

        if (false !== strpos($varName, '.')) {
            list($varArrayName, $propertyName) = explode('.', $varName);

            $object = $data[$varArrayName];

            $method = sprintf('get%s', ucfirst($propertyName));
            if (method_exists($object, $method)) {
                return $object->$method();
            }
        }

        if (isset($data[$varName])) {
            return $data[$varName];
        }
    }
}
