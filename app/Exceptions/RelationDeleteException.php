<?php

namespace App\Exceptions;

use Exception;

class RelationDeleteException extends Exception
{
    private $recordId;

    public function __construct($recordId = null)
    {
        $this->recordId = $recordId;

        parent::__construct('Имеются записи, которые зависят от удаляемой');
    }

    public function render() {
        return response(array_merge([ 'allowCheckRelations' => true, 'message' => $this->message ], $this->recordId ? [ 'id' => $this->recordId ] : [] ), 409);
    }
}
