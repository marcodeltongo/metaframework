<?php

/**
 * JsonBehavior class.
 *
 * Adds toJSON and fromJSON methods.
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class JsonBehavior extends CActiveRecordBehavior
{

    /**
     * Return a JSON representation of current model.
     */
    public function toJSON()
    {
        $owner = $this->getOwner();
        $attributes = $owner->getAttributes();
        $related = $this->getRelated();

        $src = array(
                'model' => get_class($owner),
                'data' => array(
                        'attributes' => $attributes,
                        'related' => $related
                )
        );

        return CJSON::encode($src);
    }

    /**
     * Return related data.
     *
     * @return string
     */
    private function getRelated()
    {
        $related = array();
        $obj = null;

        foreach ($this->owner->relations() as $name => $relation) {
            $obj = $this->owner->getRelated($name);

            if ($obj instanceof ActiveRecord) {
                $related[$name] = array(
                        'model' => $relation[1],
                        'data' => array($obj)
                );
            } elseif (is_array($obj)) {
                $related[$name] = array(
                        'model' => $relation[1],
                        'data' => $obj
                );
            }
        }

        return $related;
    }

    /**
     * Create a new model using JSON source for attributes.
     *
     * @param string $source
     */
    public function fromJSON($source)
    {
        /*
         * Map attributes
         */

        /*
         * Map related models
         */

        die("TO BE IMPLEMENTED");
    }

}
