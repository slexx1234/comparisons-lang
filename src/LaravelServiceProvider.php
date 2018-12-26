<?php

namespace Slexx\CL;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        Builder::macro('CLFilter', function($field, string $filter, string $typeName = 'int') {
            $table = null;
            $column = null;

            if (is_string($field)) {
                $field = explode('.', $field, 2);
            }

            if (count($field) === 2) {
                $table = $field[0];
                $column = $field[1];
            } else {
                $table = $field[0];
            }

            $type = null;

            if ($typeName === 'int' || $typeName === 'integer') {
                $type = Tokenizer::T_INT;
            } else if ($typeName === 'float' || $typeName === 'double') {
                $type = Tokenizer::T_FLOAT;
            } else if ($typeName === 'date') {
                $type = Tokenizer::T_DATE;
            } else if ($typeName === 'datetime' || $typeName === 'date_time' || $typeName === 'date-time') {
                $type = Tokenizer::T_DATE_TIME;
            } else {
                throw new \Exception('Unknown type "' . $typeName . '"!');
            }

            return $this->whereRaw((new CL($filter, $type))->compileToSQL($table, $column));
        });
    }
    
    /**
     * @return void
     */
    public function register()
    {
        //
    }
}