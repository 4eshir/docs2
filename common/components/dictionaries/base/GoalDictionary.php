<?php

namespace common\components\dictionaries\base;

class GoalDictionary extends BaseDictionary
{
    private const RELAX = 0;
    private const INCREASE_LEVEL = 1;
    private const DEVELOP = 2;
    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::RELAX => 'выявления, формирования, поддержки и развития способностей и талантов у детей и молодежи 
            на территории Астраханской области, оказания содействия в получении ими дополнительного образования,
             в том числе образования в области искусств, естественных наук, физической культуры и спорта,
              а также обеспечения организации их свободного времени (досуга) и отдыха',
            self::INCREASE_LEVEL => 'удовлетворения образовательных и профессиональных потребностей, профессионального развития человека,
             обеспечения соответствия его квалификации меняющимся условиям профессиональной деятельности и социальной среды,
              совершенствования и (или) получения новой компетенции, необходимой для профессиональной деятельности,
               и (или) повышения профессионального уровня в рамках имеющейся квалификации',
            self::DEVELOP => ' участия в формировании образовательной политики Астраханской области в области выявления,
             сопровождения и дальнейшего развития проявивших выдающиеся способности детей и молодежи в соответствии 
             с задачами социально-экономического, научно-технологического, промышленного 
             и пространственного развития Астраханской области'
        ];

    }

    public function customSort()
    {
        return [
            $this->list[self::RELAX],
            $this->list[self::INCREASE_LEVEL],
            $this->list[self::DEVELOP],
        ];
    }
}