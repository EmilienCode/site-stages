<?php
namespace tests\Models;

use App\Models\UtilisateurModel;
use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

class UtilisateurModelTest extends TestCase {
    
    public function testDeleteUserRetourneVraiQuandLaSuppressionReussit() {
        
        // 🎬 ACTE 1 : Préparer la doublure de la requête (PDOStatement)
        // On crée un faux objet PDOStatement (celui qui gère le execute)
        $fausseRequete = $this->createMock(PDOStatement::class);
        
        // On donne son script à la fausse requête :
        // "Tu vas être appelée UNE FOIS avec la méthode 'execute' contenant l'ID 5."
        // "Quand ça arrive, tu renvoies TRUE (pour faire croire que ça a marché)."
        $fausseRequete->expects($this->once())
                      ->method('execute')
                      ->with(['id' => 5])
                      ->willReturn(true);

                      
        // 🎬 ACTE 2 : Préparer la doublure de la base de données (PDO)
        // On crée un faux objet PDO
        $fauxPDO = $this->createMock(PDO::class);
        
        // On donne son script au faux PDO :
        // "Tu vas être appelé UNE FOIS avec la méthode 'prepare'."
        // "Quand ça arrive, tu donnes notre $fausseRequete créée juste au-dessus."
        $fauxPDO->expects($this->once())
                ->method('prepare')
                ->willReturn($fausseRequete);


        // 🎬 ACTE 3 : L'action !
        // On instancie TON vrai modèle, mais on lui glisse notre doublure $fauxPDO en douce
        $modele = new UtilisateurModel($fauxPDO);

        // On lance la fonction (elle va utiliser les doublures sans s'en rendre compte)
        $resultat = $modele->deleteUser(5);


        // 🎬 ACTE 4 : La vérification (L'assertion)
        // On s'attend à ce que la fonction nous renvoie bien "true"
        $this->assertTrue($resultat);
    }

    public function testGetRolesRetourneUnTableauDeRoles(){

        $fausseRequete = $this->createMock(PDOStatement::class);
        
        // On donne son script à la fausse requête :
        // "Tu vas être appelée UNE FOIS avec la méthode 'execute' contenant l'ID 5."
        // "Quand ça arrive, tu renvoies TRUE (pour faire croire que ça a marché)."
        $fauxtableau=[[],[]]
                      ->method('execute')
                      ->with(['id' => 5])
                      ->willReturn(true);

                      
        // 🎬 ACTE 2 : Préparer la doublure de la base de données (PDO)
        // On crée un faux objet PDO
        $fauxPDO = $this->createMock(PDO::class);
        
        // On donne son script au faux PDO :
        // "Tu vas être appelé UNE FOIS avec la méthode 'prepare'."
        // "Quand ça arrive, tu donnes notre $fausseRequete créée juste au-dessus."
        $fauxPDO->expects($this->once())
                ->method('prepare')
                ->willReturn($fausseRequete);


        // 🎬 ACTE 3 : L'action !
        // On instancie TON vrai modèle, mais on lui glisse notre doublure $fauxPDO en douce
        $modele = new UtilisateurModel($fauxPDO);

        // On lance la fonction (elle va utiliser les doublures sans s'en rendre compte)
        $resultat = $modele->deleteUser(5);


        // 🎬 ACTE 4 : La vérification (L'assertion)
        // On s'attend à ce que la fonction nous renvoie bien "true"
        $this->assertTrue($resultat);
    }
}