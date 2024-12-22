<?php

namespace frontend\tests\unit\models\training_group;

use common\repositories\educational\TrainingGroupRepository;
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
        $this->groupRepository = Yii::createObject(TrainingGroupRepository::class);
    }

    protected function _after()
    {
    }

    /**
     * @dataProvider getData
     */
    public function testCreateGroup($data)
    {
        if (is_array($data)) {
            foreach ($data as $item) {
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

    public function getData()
    {
        $data = new TrainingGroupCreateData();

        return [
            [
                $data
            ],
        ];
    }
}