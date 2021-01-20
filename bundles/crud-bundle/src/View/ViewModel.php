<?php

namespace CrudBundle\View;

use InvalidArgumentException;
use Traversable;
use ArrayAccess;
use ArrayObject;

class ViewModel implements ModelInterface
{
    /**
     * What variable a parent model should capture this model to.
     *
     * @var string
     */
    private $captureTo = 'content';

    /**
     * Child models.
     *
     * @var array
     */
    private $children = [];

    /**
     * Renderer options.
     *
     * @var array
     */
    private $options = [];

    /**
     * Template to use when rendering this model.
     *
     * @var string
     */
    private $template = '';

    /**
     * View variables.
     *
     * @var array|ArrayAccess&Traversable
     */
    private $variables = [];

    /**
     * Is this append to child  with the same capture?
     *
     * @var bool
     */
    private $append = false;

    /**
     * Constructor.
     *
     * @param array|Traversable|null $variables
     * @param array|Traversable      $options
     */
    public function __construct($variables = null, $options = null)
    {
        if (null === $variables) {
            $variables = new ArrayObject();
        }

        // Initializing the variables container
        $this->setVariables($variables, true);

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Property overloading: get variable value.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->__isset($name)) {
            return;
        }

        $variables = $this->getVariables();

        return $variables[$name];
    }

    /**
     * Property overloading: set variable value.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->setVariable($name, $value);
    }

    /**
     * Set view variable.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return ViewModel
     */
    public function setVariable($name, $value)
    {
        $this->variables[(string) $name] = $value;

        return $this;
    }

    /**
     * Property overloading: do we have the requested variable value?
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        $variables = $this->getVariables();

        return isset($variables[$name]);
    }

    /**
     * Get view variables.
     *
     * @return array|ArrayAccess|Traversable
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Set view variables en masse.
     *
     * Can be an array or a Traversable + ArrayAccess object.
     *
     * @param array|ArrayAccess|Traversable $variables
     * @param bool                          $overwrite Whether or not to overwrite the internal container with $variables
     *
     * @return ViewModel
     *
     * @throws \InvalidArgumentException
     */
    public function setVariables($variables, $overwrite = false)
    {
        if (!is_array($variables) && !$variables instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        if ($overwrite) {
            if (is_object($variables) && !$variables instanceof \ArrayAccess) {
                die(var_dump(__LINE__));
//                $variables = ArrayUtils::iteratorToArray($variables);
            }

            $this->variables = $variables;

            return $this;
        }

        foreach ($variables as $key => $value) {
            $this->setVariable($key, $value);
        }

        return $this;
    }

    /**
     * Set a single option.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return self
     */
    public function setOption($name, $value)
    {
        $this->options[(string) $name] = $value;

        return $this;
    }

    /**
     * Get a single option.
     *
     * @param string     $name    the option to get
     * @param mixed|null $default (optional) A default value if the option is not yet set
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        $name = (string) $name;

        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    /**
     * Get renderer options/hints.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set renderer options/hints en masse.
     *
     * @param array|Traversable $options
     *
     * @return ViewModel
     *
     * @throws InvalidArgumentException
     */
    public function setOptions($options)
    {
        // Assumption is that lowest common denominator for renderer configuration
        // is an array
        if ($options instanceof \Traversable) {
            die(var_dump(__LINE__));
//            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get a single view variable.
     *
     * @param string     $name
     * @param mixed|null $default (optional) default value if the variable is not present
     *
     * @return mixed
     */
    public function getVariable($name, $default = null)
    {
        $name = (string) $name;
        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        return $default;
    }

    /**
     * Get the template to be used by this model.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the template to be used by this model.
     *
     * @param string $template
     *
     * @return ViewModel
     */
    public function setTemplate($template)
    {
        $this->template = (string) $template;

        return $this;
    }

    /**
     * Add a child model.
     *
     * @param ModelInterface $child
     * @param string|null    $captureTo Optional; if specified, the "capture to" value to set on the child
     * @param bool|null      $append    Optional; if specified, append to child  with the same capture
     *
     * @return ViewModel
     */
    public function addChild(\CrudBundle\View\ModelInterface $child, $captureTo = null, $append = null)
    {
        $this->children[] = $child;
        if (null !== $captureTo) {
            $child->setCaptureTo($captureTo);
        }
        if (null !== $append) {
            $child->setAppend($append);
        }

        return $this;
    }

    /**
     * Return all children.
     *
     * Return specifies an array, but may be any iterable object.
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Does the model have any children?
     *
     * @return bool
     */
    public function hasChildren()
    {
        return 0 < count($this->children);
    }

    /**
     * Returns an array of Viewmodels with captureTo value $capture.
     *
     * @param string $capture
     * @param bool   $recursive search recursive through children, default true
     *
     * @return array
     */
    public function getChildrenByCaptureTo($capture, $recursive = true): array
    {
        $children = [];

        foreach ($this->children as $child) {
            if (true === $recursive) {
                $children += $child->getChildrenByCaptureTo($capture);
            }

            if ($child->captureTo() === $capture) {
                $children[] = $child;
            }
        }

        return $children;
    }

    public function hasChildrenByCapture($capture): bool
    {
        foreach ($this->children as $child) {
            if ($child->captureTo() === $capture) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the name of the variable to capture this model to, if it is a child model.
     *
     * @param string $capture
     *
     * @return ViewModel
     */
    public function setCaptureTo($capture)
    {
        $this->captureTo = (string) $capture;

        return $this;
    }

    /**
     * Get the name of the variable to which to capture this model.
     *
     * @return string
     */
    public function captureTo()
    {
        return $this->captureTo;
    }

    /**
     * Is this append to child  with the same capture?
     *
     * @return bool
     */
    public function isAppend()
    {
        return $this->append;
    }

    /**
     * Set flag indicating whether or not append to child  with the same capture.
     *
     * @param bool $append
     *
     * @return ViewModel
     */
    public function setAppend($append)
    {
        $this->append = (bool) $append;

        return $this;
    }

    /**
     * Return count of children.
     *
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Get iterator of children.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }
}
