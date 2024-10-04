<?php

namespace App\Tests\Entity;

use App\Entity\Produit;
use PHPUnit\Framework\TestCase;

class ProduitTest extends TestCase
{
    public function testNomProduit(): void
    {
        $produit = new Produit();
        $produit->setNom('Cannes');

        $this->assertEquals('Cannes', $produit->getNom());
    }

    public function testPrixProduit(): void
    {
        $produit = new Produit();
        $produit->setPrix(30);

        $this->assertSame(30, $produit->getPrix());
    }
}
