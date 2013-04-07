<?php

namespace Sm\TaskBundle\Model;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractModel
{
    /**
     * Populate object
     *
     * @param array $data
     */
    public function populate(array $data)
    {
        foreach ($this as $param => $value) {
            if (isset($data[$param])) {
                $this->{$param} = $data[$param] ? $data[$param] : null;
            }
        }
    }

    /**
     * Bind request
     *
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->populate($request->query->all());
        $this->populate($request->request->all());
        $this->populate($request->files->all());
    }

    /**
     * Save entity
     */
    abstract function save();
}