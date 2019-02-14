<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 11/5/18
 * Time: 03:33
 */

if(!function_exists('emailTypeBuilder')) {

    function emailTypeBuilder(string $typeName, string $typeClass, string $typeSlug = null) {
        $typeBuilder = App::make('EmailRecordTypeBuilder');
        dd($typeBuilder);
    }

    /*
    public function make(string $typeName, string $typeClass, string $typeSlug = null) {
        if(is_null($typeSlug)) {
            $typeSlug = str_slug($typeName);
        }

        return [
            'type_name' => $typeName,
            'type_class' => $typeClass,
            'type_slug' => $typeSlug
        ];
    }*/

}
