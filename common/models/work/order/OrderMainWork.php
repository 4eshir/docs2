<?php

use common\events\EventTrait;
use common\models\scaffold\OrderMain;
use common\models\work\document_in_out\InOutDocumentsWork;
use common\models\work\general\CompanyWork;
use common\models\work\general\PeopleWork;
use common\models\work\general\PositionWork;

/**
 * @property PeopleWork $correspondentWork
 * @property PeopleWork $creatorWork
 * @property PeopleWork $lastUpdateWork
 * @property PeopleWork $executorWork
 * @property PeopleWork $bringWork
 *
 *
 */
class OrderMainWork extends OrderMain
{
    use EventTrait;

    /**
     * Имена файлов для сохранения в БД
     */
    public $scanName;
    public $docName;
    public $appName;

    /**
     * Переменные для input-file в форме
     */
    public $scanFile;
    public $docFiles;
    public $appFiles;

















}