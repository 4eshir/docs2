<?php

namespace frontend\events\document_in;

use frontend\events\EventInterface;

class InOutDocumentLinkedEvent implements EventInterface
{
    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}