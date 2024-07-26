<?php

namespace common\models\work\document_in_out;

use common\models\scaffold\InOutDocuments;
use common\models\work\general\PeopleWork;

/**
 * @property PeopleWork $responsibleWork
 */
class InOutDocumentsWork extends InOutDocuments
{
    public static function fill(
        $documentInId,
        $documentOutId = null,
        $date = null,
        $responsibleId = null
    )
    {
        $entity = new static();
        $entity->document_in_id = $documentInId;
        $entity->document_out_id = $documentOutId;
        $entity->date = $date;
        $entity->responsible_id = $responsibleId;

        return $entity;
    }

    public function isDocumentOutEmpty()
    {
        return $this->document_out_id == null;
    }

    public function isNoPeopleTarget()
    {
        return $this->responsible_id == null;
    }

    public function isNoAnswerDate()
    {
        return $this->date == null;
    }

    public function getRowClass()
    {
        if ($this->document_out_id !== null) {
            return ['class' => 'default'];
        }
        else if ($this->date !== null && $this->date < date("Y-m-d")) {
            return ['class' => 'danger'];
        }
        else {
            return ['class' => 'warning'];
        }
    }

    // --relationships--

    public function getResponsibleWork()
    {
        return $this->hasOne(PeopleWork::class, ['id' => 'responsible_id']);
    }
}