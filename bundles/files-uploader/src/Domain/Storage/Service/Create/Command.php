<?php

namespace FilesUploader\Domain\Storage\Service\Create;

final class Command
{

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string
     */
    public $originalName;

    /**
     * @var string
     */
    public $type;

    /**
     * @var integer
     */
    public $size;

    /**
     * @var array|null
     */
    public $metaInfo;

    public static function formArray(array $data): self
    {
        $entity = new self();
        $entity->populate($data);

        return $entity;
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    private function populate(array $array = [])
    {
        $state = get_object_vars($this);

        $stateIntersect = array_intersect_key($array, $state);

        foreach ($stateIntersect as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }
}
