<?php

namespace frontend\tests\unit\models\training_group;

use common\repositories\educational\TrainingGroupRepository;
use common\repositories\providers\training_group\TrainingGroupMockProvider;
use Exception;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;

class TrainingGroupCreateTest extends \Codeception\Test\Unit
{
    private TrainingGroupRepository $groupRepository;

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    protected $groupId;
    
    protected function _before()
    {
        $this->groupRepository = Yii::createObject(
            TrainingGroupRepository::class,
            ['groupProvider' => Yii::createObject(TrainingGroupMockProvider::class)]
        );
    }

    protected function _after()
    {
    }

    // Тестируем создание базовых учебных групп

    /**
     * @dataProvider getCreateGroupData
     */
    public function testCreateGroup(TrainingGroupCreateData $data)
    {
        $groups = $data->groups;

        if (is_array($groups)) {
            foreach ($groups as $item) {
                try {
                    $group = TrainingGroupWork::fill(
                        $item['start_date'],
                        $item['finish_date'],
                        $item['open'],
                        $item['budget'],
                        $item['branch'],
                        $item['order_stop'],
                        $item['archive'],
                        $item['protection_date'],
                        $item['protection_confirm'],
                        $item['is_network'],
                        $item['state'],
                        $item['created_at'],
                        $item['updated_at']
                    );

                    $this->groupId = $this->groupRepository->save($group);
                    $this->assertNotNull($this->groupId, 'Group ID не может быть NULL');
                }
                catch (Exception $exception) {
                    $this->fail('Ошибка сохранения группы: ' . $exception->getMessage());
                }
            }
        }
        else {
            $this->fail('Ошибка провайдера данных');
        }
    }

    public function getCreateGroupData()
    {
        $data = new TrainingGroupCreateData();

        return [
            [
                $data
            ],
        ];
    }

    // Тестируем создание одной учебной группы вместе со всеми связанными данными

    /**
     * @dataProvider getFullGroupData
     */
    public function testFullGroup(TrainingGroupCreateData $data)
    {

    }

    public function getFullGroupData()
    {
        $data = new TrainingGroupFullData();

        return [
            [
                $data
            ],
        ];
    }
}