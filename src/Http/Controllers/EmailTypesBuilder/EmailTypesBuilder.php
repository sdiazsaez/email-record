<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 11/5/18
 * Time: 03:33
 */

class EmailTypesBuilder {

    public function __construct() {}

    public function make(string $typeName, string $typeClass, string $typeSlug = null) {
        if(is_null($typeSlug)) {
            $typeSlug = str_slug($typeName);
        }

        return [
            'type_name' => $typeName,
            'type_class' => $typeClass,
            'type_slug' => $typeSlug
        ];
    }

}
