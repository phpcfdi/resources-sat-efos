<?php
 /*
 *  This file is part of the phpCfdi package.
 *  
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *  
 *  (c) 2020 phpCfdi
 *  
 */

declare(strict_types=1);

namespace PhpCfdi\Efos\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200725214112 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Tabla para almacenar el listado de RFCs que se consideran "Empresas que Facturan Operaciones Simuladas (EFOS)"';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("
            CREATE TABLE `resources_sat_efos` (
                `id` int(11) NOT NULL,
                `row_key` char(32) NOT NULL,
                `reference_key` tinyint(1) UNSIGNED NOT NULL,
                `column_name` varchar(50) NOT NULL,
                `body` longtext NOT NULL,
                `created` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            ALTER TABLE `resources_sat_efos`
                ADD PRIMARY KEY (`id`),
                ADD KEY `row_index` (`row_key`,`reference_key`,`column_name`);
            
            ALTER TABLE `resources_sat_efos`
                MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");

        $this->addSql("
            INSERT INTO `resources_sat_efos` (`id`, `row_key`, `reference_key`, `column_name`, `body`, `created`) VALUES
            (1, 'accf30d1e1274bbd84cc4f74f65a00e1', 1, 'efos_69b_listado_completo', '{\"column_name\":\"efos_69b_listado_completo\",\"description\":\"Listado completo EFOS Art\\\u00edculo 69B\",\"last_etag\":\"\",\"url\":\"http:\\/\\/omawww.sat.gob.mx\\/cifras_sat\\/Documents\\/Listado_Completo_69-B.csv\",\"headers\":[\"numero\",\"rfc\",\"contribuyente\",\"situacion\",\"num_fecha_oficio_global_presuncion_sat\",\"pub_pag_sat_presuntos\",\"num_fecha_oficio_global_presuncion_dof\",\"pub_dof_presuntos\",\"pub_pag_sat_desvirtuados\",\"num_fecha_oficio_global_contribuyentes_desvirtuaron\",\"pub_dof_desvirtuados\",\"num_fecha_oficio_global_definitivos\",\"pub_pag_sat_definitivos\",\"pub_dof_definitivos\",\"num_fecha_oficio_global_sentencia_favorable_sat\",\"pub_pag_sat_sentencia_favorable\",\"num_fecha_oficio_global_sentencia_favorable_dof\",\"pub_dof_sentencia_favorable\"],\"columns_required\":[\"rfc\",\"contribuyente\",\"situacion\",\"num_fecha_oficio_global_presuncion_sat\",\"pub_pag_sat_presuntos\",\"num_fecha_oficio_global_presuncion_dof\",\"pub_dof_presuntos\",\"pub_pag_sat_desvirtuados\",\"num_fecha_oficio_global_contribuyentes_desvirtuaron\",\"pub_dof_desvirtuados\",\"num_fecha_oficio_global_definitivos\",\"pub_pag_sat_definitivos\",\"pub_dof_definitivos\",\"num_fecha_oficio_global_sentencia_favorable_sat\",\"pub_pag_sat_sentencia_favorable\",\"num_fecha_oficio_global_sentencia_favorable_dof\",\"pub_dof_sentencia_favorable\"]}', 1597743107);
        ");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DROP TABLE `resources_sat_efos`");
    }
}
