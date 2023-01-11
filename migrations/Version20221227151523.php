<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221227151523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bon_chauffeur (id INT AUTO_INCREMENT NOT NULL, vehicule_chauffeur_id INT DEFAULT NULL, quantite DOUBLE PRECISION NOT NULL, montant NUMERIC(10, 0) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', piece_jointe VARCHAR(255) DEFAULT NULL, INDEX IDX_CB0ED903BC8535DD (vehicule_chauffeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bon_client (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, client_station_id INT DEFAULT NULL, numero VARCHAR(100) NOT NULL, montant DOUBLE PRECISION NOT NULL, quantite DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_paid TINYINT(1) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_D3387C6FB5991721 (type_carburant_id), INDEX IDX_D3387C6FB1E98F74 (client_station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chauffeur (id INT AUTO_INCREMENT NOT NULL, type_vehicule_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, photo_permis VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_naissance DATE DEFAULT NULL, INDEX IDX_5CA777B8153E280 (type_vehicule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_station (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) DEFAULT NULL, telephone VARCHAR(100) DEFAULT NULL, prix_carburant LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, adresse VARCHAR(100) DEFAULT NULL, INDEX IDX_CD04BCCC21BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cuve (id INT AUTO_INCREMENT NOT NULL, station_id INT NOT NULL, type_carburant_id INT NOT NULL, numero VARCHAR(100) NOT NULL, capacity INT NOT NULL, stock INT NOT NULL, description VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_actived TINYINT(1) NOT NULL, prix_achat_moyen INT NOT NULL, qte_alerte INT NOT NULL, last_prix_achat_moyen INT DEFAULT NULL, INDEX IDX_1E5066ED21BDB235 (station_id), INDEX IDX_1E5066EDB5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cuve_mesure (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, level_cm INT NOT NULL, volume INT NOT NULL, INDEX IDX_92274D049FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, libelle VARCHAR(100) NOT NULL, description VARCHAR(100) DEFAULT NULL, montant INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3405975721BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE detail_depense (id INT AUTO_INCREMENT NOT NULL, depense_id INT DEFAULT NULL, libelle VARCHAR(100) NOT NULL, montant INT NOT NULL, description VARCHAR(100) DEFAULT NULL, INDEX IDX_53CA178241D81563 (depense_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gerant (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) DEFAULT NULL, email VARCHAR(100) NOT NULL, telephone VARCHAR(100) DEFAULT NULL, INDEX IDX_D1D45C7021BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE global_stockage (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, quantite INT NOT NULL, prix_achat INT NOT NULL, manquant INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1DDAE2BCB5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indexation (id INT AUTO_INCREMENT NOT NULL, pistolet_id INT DEFAULT NULL, val_index DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, difference DOUBLE PRECISION NOT NULL, INDEX IDX_7FE1FDFB77248C26 (pistolet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jaugeage (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, quantite NUMERIC(10, 0) NOT NULL, is_last TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2BBCDDF59FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jeu_concours_bon_vehicule (id INT AUTO_INCREMENT NOT NULL, matricule VARCHAR(100) NOT NULL, quantite NUMERIC(10, 0) NOT NULL, montant BIGINT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pistolet (id INT AUTO_INCREMENT NOT NULL, pompe_id INT DEFAULT NULL, type_carburant_id INT DEFAULT NULL, numero VARCHAR(100) NOT NULL, index_pistolet DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3BD281796CCC95AD (pompe_id), INDEX IDX_3BD28179B5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pompe (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, numero VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E5D44D521BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retour_en_cuve (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, quantite INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1121771B5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE station (id INT AUTO_INCREMENT NOT NULL, structure_client_id INT NOT NULL, name VARCHAR(255) NOT NULL, adresse VARCHAR(100) DEFAULT NULL, telephone VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9F39F8B18FB3C40D (structure_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stockage (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, gloabal_stockage_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, quantite INT NOT NULL, prix_unitaire INT NOT NULL, manquant INT NOT NULL, is_last TINYINT(1) NOT NULL, INDEX IDX_CABCB4929FB71B08 (cuve_id), INDEX IDX_CABCB492B225A1E9 (gloabal_stockage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structure_client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, telephone VARCHAR(100) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, adresse VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, description VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_carburant (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(100) DEFAULT NULL, prix_littre INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_AC721A21BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_vehicule (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, qte_recompense INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E4D0D8E121BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, structure_client_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) DEFAULT NULL, telephone VARCHAR(100) NOT NULL, adresse VARCHAR(100) DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, is_admin TINYINT(1) NOT NULL, profile VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D6498FB3C40D (structure_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user_role (user_id INT NOT NULL, user_role_id INT NOT NULL, INDEX IDX_2D084B47A76ED395 (user_id), INDEX IDX_2D084B478E0E3CA6 (user_role_id), PRIMARY KEY(user_id, user_role_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, description VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule_chauffeur (id INT AUTO_INCREMENT NOT NULL, chauffeur_id INT DEFAULT NULL, type_carburant_id INT DEFAULT NULL, immatriculation VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', description VARCHAR(255) DEFAULT NULL, INDEX IDX_F057DC0385C0B3BE (chauffeur_id), INDEX IDX_F057DC03B5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_carburant (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, created_at DATETIME NOT NULL, prix_achat INT NOT NULL, prix_vente INT NOT NULL, quantite INT NOT NULL, INDEX IDX_13AD4083B5991721 (type_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_carburant_detail (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, vente_carburant_id INT DEFAULT NULL, quantite INT NOT NULL, prix_unitaire INT NOT NULL, prix_unitaire_vente INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_AB88C1459FB71B08 (cuve_id), INDEX IDX_AB88C145DD2CF0AA (vente_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_cuve (id INT AUTO_INCREMENT NOT NULL, cuve_id INT DEFAULT NULL, created_at DATETIME NOT NULL, quantite DOUBLE PRECISION NOT NULL, montant_achat BIGINT NOT NULL, montant_vente BIGINT NOT NULL, gain BIGINT NOT NULL, INDEX IDX_E0160F19FB71B08 (cuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_pistolet (id INT AUTO_INCREMENT NOT NULL, pistolet_id INT DEFAULT NULL, quantite DOUBLE PRECISION NOT NULL, montant BIGINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9B87699D77248C26 (pistolet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_pistolet_carburant (id INT AUTO_INCREMENT NOT NULL, type_carburant_id INT DEFAULT NULL, pistolet_id INT DEFAULT NULL, quantite INT NOT NULL, prix_vente INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_895208B4B5991721 (type_carburant_id), INDEX IDX_895208B477248C26 (pistolet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vente_pistolet_carburant_detail (id INT AUTO_INCREMENT NOT NULL, pistolet_id INT DEFAULT NULL, vente_pistolet_carburant_id INT DEFAULT NULL, created_at DATETIME NOT NULL, quantite INT NOT NULL, prix_vente INT NOT NULL, INDEX IDX_3DDE14D477248C26 (pistolet_id), INDEX IDX_3DDE14D4E9AA29A5 (vente_pistolet_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bon_chauffeur ADD CONSTRAINT FK_CB0ED903BC8535DD FOREIGN KEY (vehicule_chauffeur_id) REFERENCES vehicule_chauffeur (id)');
        $this->addSql('ALTER TABLE bon_client ADD CONSTRAINT FK_D3387C6FB5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE bon_client ADD CONSTRAINT FK_D3387C6FB1E98F74 FOREIGN KEY (client_station_id) REFERENCES client_station (id)');
        $this->addSql('ALTER TABLE chauffeur ADD CONSTRAINT FK_5CA777B8153E280 FOREIGN KEY (type_vehicule_id) REFERENCES type_vehicule (id)');
        $this->addSql('ALTER TABLE client_station ADD CONSTRAINT FK_CD04BCCC21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE cuve ADD CONSTRAINT FK_1E5066ED21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE cuve ADD CONSTRAINT FK_1E5066EDB5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE cuve_mesure ADD CONSTRAINT FK_92274D049FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_3405975721BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE detail_depense ADD CONSTRAINT FK_53CA178241D81563 FOREIGN KEY (depense_id) REFERENCES depense (id)');
        $this->addSql('ALTER TABLE gerant ADD CONSTRAINT FK_D1D45C7021BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE global_stockage ADD CONSTRAINT FK_1DDAE2BCB5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE indexation ADD CONSTRAINT FK_7FE1FDFB77248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
        $this->addSql('ALTER TABLE jaugeage ADD CONSTRAINT FK_2BBCDDF59FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE pistolet ADD CONSTRAINT FK_3BD281796CCC95AD FOREIGN KEY (pompe_id) REFERENCES pompe (id)');
        $this->addSql('ALTER TABLE pistolet ADD CONSTRAINT FK_3BD28179B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE pompe ADD CONSTRAINT FK_E5D44D521BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE retour_en_cuve ADD CONSTRAINT FK_1121771B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B18FB3C40D FOREIGN KEY (structure_client_id) REFERENCES structure_client (id)');
        $this->addSql('ALTER TABLE stockage ADD CONSTRAINT FK_CABCB4929FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE stockage ADD CONSTRAINT FK_CABCB492B225A1E9 FOREIGN KEY (gloabal_stockage_id) REFERENCES global_stockage (id)');
        $this->addSql('ALTER TABLE type_carburant ADD CONSTRAINT FK_AC721A21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE type_vehicule ADD CONSTRAINT FK_E4D0D8E121BDB235 FOREIGN KEY (station_id) REFERENCES station (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498FB3C40D FOREIGN KEY (structure_client_id) REFERENCES structure_client (id)');
        $this->addSql('ALTER TABLE user_user_role ADD CONSTRAINT FK_2D084B47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user_role ADD CONSTRAINT FK_2D084B478E0E3CA6 FOREIGN KEY (user_role_id) REFERENCES user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vehicule_chauffeur ADD CONSTRAINT FK_F057DC0385C0B3BE FOREIGN KEY (chauffeur_id) REFERENCES chauffeur (id)');
        $this->addSql('ALTER TABLE vehicule_chauffeur ADD CONSTRAINT FK_F057DC03B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE vente_carburant ADD CONSTRAINT FK_13AD4083B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE vente_carburant_detail ADD CONSTRAINT FK_AB88C1459FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE vente_carburant_detail ADD CONSTRAINT FK_AB88C145DD2CF0AA FOREIGN KEY (vente_carburant_id) REFERENCES vente_carburant (id)');
        $this->addSql('ALTER TABLE vente_cuve ADD CONSTRAINT FK_E0160F19FB71B08 FOREIGN KEY (cuve_id) REFERENCES cuve (id)');
        $this->addSql('ALTER TABLE vente_pistolet ADD CONSTRAINT FK_9B87699D77248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
        $this->addSql('ALTER TABLE vente_pistolet_carburant ADD CONSTRAINT FK_895208B4B5991721 FOREIGN KEY (type_carburant_id) REFERENCES type_carburant (id)');
        $this->addSql('ALTER TABLE vente_pistolet_carburant ADD CONSTRAINT FK_895208B477248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
        $this->addSql('ALTER TABLE vente_pistolet_carburant_detail ADD CONSTRAINT FK_3DDE14D477248C26 FOREIGN KEY (pistolet_id) REFERENCES pistolet (id)');
        $this->addSql('ALTER TABLE vente_pistolet_carburant_detail ADD CONSTRAINT FK_3DDE14D4E9AA29A5 FOREIGN KEY (vente_pistolet_carburant_id) REFERENCES vente_pistolet_carburant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bon_chauffeur DROP FOREIGN KEY FK_CB0ED903BC8535DD');
        $this->addSql('ALTER TABLE bon_client DROP FOREIGN KEY FK_D3387C6FB5991721');
        $this->addSql('ALTER TABLE bon_client DROP FOREIGN KEY FK_D3387C6FB1E98F74');
        $this->addSql('ALTER TABLE chauffeur DROP FOREIGN KEY FK_5CA777B8153E280');
        $this->addSql('ALTER TABLE client_station DROP FOREIGN KEY FK_CD04BCCC21BDB235');
        $this->addSql('ALTER TABLE cuve DROP FOREIGN KEY FK_1E5066ED21BDB235');
        $this->addSql('ALTER TABLE cuve DROP FOREIGN KEY FK_1E5066EDB5991721');
        $this->addSql('ALTER TABLE cuve_mesure DROP FOREIGN KEY FK_92274D049FB71B08');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_3405975721BDB235');
        $this->addSql('ALTER TABLE detail_depense DROP FOREIGN KEY FK_53CA178241D81563');
        $this->addSql('ALTER TABLE gerant DROP FOREIGN KEY FK_D1D45C7021BDB235');
        $this->addSql('ALTER TABLE global_stockage DROP FOREIGN KEY FK_1DDAE2BCB5991721');
        $this->addSql('ALTER TABLE indexation DROP FOREIGN KEY FK_7FE1FDFB77248C26');
        $this->addSql('ALTER TABLE jaugeage DROP FOREIGN KEY FK_2BBCDDF59FB71B08');
        $this->addSql('ALTER TABLE pistolet DROP FOREIGN KEY FK_3BD281796CCC95AD');
        $this->addSql('ALTER TABLE pistolet DROP FOREIGN KEY FK_3BD28179B5991721');
        $this->addSql('ALTER TABLE pompe DROP FOREIGN KEY FK_E5D44D521BDB235');
        $this->addSql('ALTER TABLE retour_en_cuve DROP FOREIGN KEY FK_1121771B5991721');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B18FB3C40D');
        $this->addSql('ALTER TABLE stockage DROP FOREIGN KEY FK_CABCB4929FB71B08');
        $this->addSql('ALTER TABLE stockage DROP FOREIGN KEY FK_CABCB492B225A1E9');
        $this->addSql('ALTER TABLE type_carburant DROP FOREIGN KEY FK_AC721A21BDB235');
        $this->addSql('ALTER TABLE type_vehicule DROP FOREIGN KEY FK_E4D0D8E121BDB235');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498FB3C40D');
        $this->addSql('ALTER TABLE user_user_role DROP FOREIGN KEY FK_2D084B47A76ED395');
        $this->addSql('ALTER TABLE user_user_role DROP FOREIGN KEY FK_2D084B478E0E3CA6');
        $this->addSql('ALTER TABLE vehicule_chauffeur DROP FOREIGN KEY FK_F057DC0385C0B3BE');
        $this->addSql('ALTER TABLE vehicule_chauffeur DROP FOREIGN KEY FK_F057DC03B5991721');
        $this->addSql('ALTER TABLE vente_carburant DROP FOREIGN KEY FK_13AD4083B5991721');
        $this->addSql('ALTER TABLE vente_carburant_detail DROP FOREIGN KEY FK_AB88C1459FB71B08');
        $this->addSql('ALTER TABLE vente_carburant_detail DROP FOREIGN KEY FK_AB88C145DD2CF0AA');
        $this->addSql('ALTER TABLE vente_cuve DROP FOREIGN KEY FK_E0160F19FB71B08');
        $this->addSql('ALTER TABLE vente_pistolet DROP FOREIGN KEY FK_9B87699D77248C26');
        $this->addSql('ALTER TABLE vente_pistolet_carburant DROP FOREIGN KEY FK_895208B4B5991721');
        $this->addSql('ALTER TABLE vente_pistolet_carburant DROP FOREIGN KEY FK_895208B477248C26');
        $this->addSql('ALTER TABLE vente_pistolet_carburant_detail DROP FOREIGN KEY FK_3DDE14D477248C26');
        $this->addSql('ALTER TABLE vente_pistolet_carburant_detail DROP FOREIGN KEY FK_3DDE14D4E9AA29A5');
        $this->addSql('DROP TABLE bon_chauffeur');
        $this->addSql('DROP TABLE bon_client');
        $this->addSql('DROP TABLE chauffeur');
        $this->addSql('DROP TABLE client_station');
        $this->addSql('DROP TABLE cuve');
        $this->addSql('DROP TABLE cuve_mesure');
        $this->addSql('DROP TABLE depense');
        $this->addSql('DROP TABLE detail_depense');
        $this->addSql('DROP TABLE gerant');
        $this->addSql('DROP TABLE global_stockage');
        $this->addSql('DROP TABLE indexation');
        $this->addSql('DROP TABLE jaugeage');
        $this->addSql('DROP TABLE jeu_concours_bon_vehicule');
        $this->addSql('DROP TABLE pistolet');
        $this->addSql('DROP TABLE pompe');
        $this->addSql('DROP TABLE retour_en_cuve');
        $this->addSql('DROP TABLE station');
        $this->addSql('DROP TABLE stockage');
        $this->addSql('DROP TABLE structure_client');
        $this->addSql('DROP TABLE type_carburant');
        $this->addSql('DROP TABLE type_vehicule');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_user_role');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE vehicule_chauffeur');
        $this->addSql('DROP TABLE vente_carburant');
        $this->addSql('DROP TABLE vente_carburant_detail');
        $this->addSql('DROP TABLE vente_cuve');
        $this->addSql('DROP TABLE vente_pistolet');
        $this->addSql('DROP TABLE vente_pistolet_carburant');
        $this->addSql('DROP TABLE vente_pistolet_carburant_detail');
    }
}
