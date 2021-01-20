<?php

namespace CrudBundle\View\ViewModel;

use CrudBundle\View\ViewModel;
use Symfony\Component\Serializer\SerializerInterface;

final class TableViewModel extends ViewModel
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        parent::__construct();

        $this->serializer = $serializer;
    }

    public function getVariable($name, $default = null)
    {
        if ('rowsData' == $name) {
            $bodyRowsData = parent::getVariable('rowsData');

            return $this->serializer->normalize($bodyRowsData);
        }

        if ('headRows' == $name) {
            $columnsConf = parent::getVariable('columns');

            $result = [];
            $row = [];
            foreach ($columnsConf as $columnConf) {
                $row[] = $columnConf['head'];
            }
            $result[] = $row;

            return $result;
        }

        return parent::getVariable($name, $default);
    }
}
