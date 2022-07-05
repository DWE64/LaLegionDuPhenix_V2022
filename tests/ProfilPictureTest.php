<?php

namespace App\Tests;

use App\Entity\ProfilPicture;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ProfilPictureTest extends TestCase
{
    private ProfilPicture $profilPicture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->profilPicture = new ProfilPicture();
    }

    public function testGetUser(): void
    {
        $value = new User();
        $response = $this->profilPicture->setUser($value);

        $this->assertInstanceOf(ProfilPicture::class, $response);
        $this->assertInstanceOf(User::class, $this->profilPicture->getUser());
    }

    public function testProfilPicture(){
        $value = 'monimage.jpg';
        $response = $this->profilPicture->setProfilPicture($value);

        $this->assertInstanceOf(ProfilPicture::class, $response);
        $this->assertEquals($value, $this->profilPicture->getProfilPicture());
    }
}
