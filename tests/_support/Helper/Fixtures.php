<?php

namespace Tests\Helper;

use App\Users\Domain\User\ValueObject\UserId;
use Codeception\Module;
use Faker\Factory;

final class Fixtures extends Module
{
    /**
     * @var Module\Db
     */
    private $db;

    /**
     * @var Module\Filesystem
     */
    private $filesystem;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    public function _initialize()
    {
        $this->db = $this->getModule('Db');
        $this->filesystem = $this->getModule('Filesystem');
        $this->filesystem->copyDir('tests/_data/storage', 'storage/source/tests');

        $this->faker = Factory::create('uk_UA');
    }

    public function createUser($data = [])
    {
        $date = new \DateTimeImmutable();

        $createDt = $date->format('Y-m-d H:i:s');
        $updateDt = $date->format('Y-m-d H:i:s');

        $id = UserId::next();

        $width = 100;
        $height = 100;
        $cropData = [
            'x' => 0,
            'y' => $height,
            'width' => $width,
            'height' => $height,
        ];

        $defaultData = [
            'id' => (string) $id,
            'email' => $this->faker->unique()->email,
            'username' => $this->faker->unique()->userName,
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$j6mHiXd8jNMwAKPNuNb0oA$gei3ZhxmdyxDSMijBCohh7kbeKIpHpruyDIVWOxssao',
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'roles' => ['ROLE_ADMIN'],
            'status' => 1,
            'avatarPath' => 'tests/',
            'avatarFileName' => 'chess.jpg',
            'avatarCropData' => $cropData,
            'lastLoggedAt' => null,
            'createdAt' => $createDt,
            'updatedAt' => $updateDt,
        ];
        $data = array_merge($defaultData, $data);

        $this->db->haveInDatabase('users', [
            'id' => $data['id'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$j6mHiXd8jNMwAKPNuNb0oA$gei3ZhxmdyxDSMijBCohh7kbeKIpHpruyDIVWOxssao',
            'name' => $data['name'],
            'phone' => $data['phone'],
            'roles' => json_encode($data['roles']),
            'status' => $data['status'],
            'avatar_path' => $data['avatarPath'],
            'avatar_file_name' => $data['avatarFileName'],
            'avatar_crop_data' => json_encode($data['avatarCropData']),
            'last_logged_at' => $data['lastLoggedAt'],
            'created_at' => $data['createdAt'],
            'updated_at' => $data['updatedAt'],
        ]);

        return $data;
    }

    public function clearTable($table): void
    {
        $driver = $this->db->_getDriver();

        $driver->load(["TRUNCATE TABLE {$table} CASCADE;"]);
    }
}
