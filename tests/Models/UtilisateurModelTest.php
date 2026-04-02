<?php
namespace tests\Models;

use App\Models\UtilisateurModel;
use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

class UtilisateurModelTest extends TestCase {
    
    public function testDeleteUserRetourneVraiQuandLaSuppressionReussit() {
        
        $fausseRequete = $this->createMock(PDOStatement::class);
        
        $fausseRequete->expects($this->once())
                      ->method('execute')
                      ->with(['id' => 5])
                      ->willReturn(true);

        $fauxPDO = $this->createMock(PDO::class);
        
        $fauxPDO->expects($this->once())
                ->method('prepare')
                ->willReturn($fausseRequete);

        $modele = new UtilisateurModel($fauxPDO);

        $resultat = $modele->deleteUser(5);

        $this->assertTrue($resultat);
    }

    public function testGetRolesRetourneUnTableauDeRoles(){

        $fausseRequete = $this->createMock(PDOStatement::class);
        
        $fauxtableau=[[],[]]
                      ->method('execute')
                      ->with(['id' => 5])
                      ->willReturn(true);

        $fauxPDO = $this->createMock(PDO::class);
        
        $fauxPDO->expects($this->once())
                ->method('prepare')
                ->willReturn($fausseRequete);

        $modele = new UtilisateurModel($fauxPDO);

        $resultat = $modele->deleteUser(5);

        $this->assertTrue($resultat);
    }
}