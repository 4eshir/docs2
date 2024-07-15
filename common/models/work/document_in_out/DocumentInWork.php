<?php

namespace common\models\work\document_in_out;

use common\helpers\StringFormatter;
use common\models\scaffold\DocumentIn;
use common\models\work\general\CompanyWork;
use common\models\work\general\PeopleWork;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use PHPUnit\Framework\InvalidArgumentException;
use Yii;
use yii\helpers\Url;

class DocumentInWork extends DocumentIn
{
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fullNumber' => '№ п/п',
            'localDate' => 'Дата поступления<br>документа',
            'realDate' => 'Дата входящего<br>документа',
            'realNumber' => 'Рег. номер<br>входящего док.',
            'companyName' => 'Наименование<br>корреспондента',
            'documentTheme' => 'Тема документа',
            'sendMethodName' => 'Способ получения',
            'needAnswer' => 'Ответ',
        ]);
    }

    public function getFullNumber()
    {
        if ($this->local_postfix == null)
            return $this->local_number;
        else
            return $this->local_number.'/'.$this->local_postfix;
    }

    public function getCompanyName()
    {
        return $this->companyWork->name;
    }

    public function getSendMethodName()
    {
        return Yii::$app->sendMethods->get($this->send_method);
    }

    public function getRealDate()
    {
        return $this->real_date;
    }

    public function getLocalDate()
    {
        return $this->local_date;
    }

    public function getRealNumber()
    {
        return $this->real_number;
    }

    public function getDocumentTheme()
    {
        return $this->document_theme;
    }

    /**
     * Возвращает строку с отображением необходимости ответа на входящий документ
     * @param int $format формат возвращаемого значения (при наличии такой опции) @see StringFormatter
     * @return string
     */
    public function getNeedAnswer(int $format = StringFormatter::FORMAT_RAW)
    {
        if (array_key_exists($format, StringFormatter::getFormats())) {
            $links = (Yii::createObject(InOutDocumentsRepository::class))->get($this->id);

            /** @var InOutDocumentsWork $links */
            if ($links == null) {
                return '';
            }

            if ($links->isDocumentOutEmpty()) {
                if ($links->isNoPeopleTarget()) {
                    if ($links->isNoAnswerDate()) {
                        return 'Требуется ответ';
                    }
                    return 'До '.$links->date;
                }
                return 'До '.$links->date.' от '.$links->responsibleWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS);
            }

            $str = 'Исходящий документ "'.(Yii::createObject(DocumentOutRepository::class))->get($links->document_out_id)->document_theme.'"';
            return $format == StringFormatter::FORMAT_LINK ?
                StringFormatter::stringAsLink($str, Url::to(['document-out/view', 'id' => $links->document_out_id])) : $str;
        }
        throw new \InvalidArgumentException('Неизвестный формат строки');
    }


    // --relationships--

    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }

    public function getInOutDocumentsWork()
    {
        return $this->hasMany(InOutDocumentsWork::class, ['document_in_id' => 'id']);
    }
}