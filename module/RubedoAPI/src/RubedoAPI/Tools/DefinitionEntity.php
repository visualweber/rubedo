<?php
/**
 * Created by IntelliJ IDEA.
 * User: nainterceptor
 * Date: 06/08/14
 * Time: 16:34
 */

namespace RubedoAPI\Tools;


use RubedoAPI\Exceptions\APIEntityException;
use RubedoAPI\Exceptions\APIRequestException;
use Zend\Stdlib\JsonSerializable;

class DefinitionEntity implements JsonSerializable{
    protected $name;
    protected $description;
    protected $verbList = [];

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @throws \RubedoAPI\Exceptions\APIEntityException
     * @return array
     */
    protected function getVerbsSerialized()
    {
        $verbs = [];
        foreach ($this->verbList as $key => $value) {
            if (!$value instanceof VerbDefinitionEntity)
                throw new APIEntityException('Verbs in Definition must be VerbDefinitionEntity', 500);
            $verbs[$key] = $value->jsonSerialize();
        }
        return $verbs;
    }

    /**
     * @param $verb
     * @param $function
     * @return $this
     * @internal param $array
     * @internal param array $verbList
     */
    public function editVerb($verb, $function)
    {
        $verb = strtoupper($verb);
        if(!array_key_exists($verb, $this->verbList)) {
            $this->verbList[$verb] = (new VerbDefinitionEntity())
                ->setVerb($verb)
                ->addOutputFilter(
                    (new FilterDefinitionEntity())
                        ->setKey('success')
                        ->setRequired()
                        ->setDescription('Success of the query')
                        ->setFilter('boolean')
                )
                ->addOutputFilter(
                    (new FilterDefinitionEntity())
                        ->setKey('message')
                        ->setDescription('Informations about the query')
                        ->setFilter('string')

                )
            ;
        }
        $function($this->verbList[$verb]);
        return $this;
    }

    public function getVerb($verb)
    {
        $verb = strtoupper($verb);
        if(!array_key_exists($verb, $this->verbList)) {
            throw new APIRequestException('Verb undefined', 404);
        }
        return $this->verbList[$verb];
    }

    function jsonSerialize() {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'verbs' => $this->getVerbsSerialized(),
        ];
    }
} 