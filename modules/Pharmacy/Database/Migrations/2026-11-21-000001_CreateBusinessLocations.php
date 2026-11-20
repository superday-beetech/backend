<?php

declare(strict_types=1);

namespace Modules\Pharmacy\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Introduces the business and physical-location foundation.
 *
 * This migration is intentionally additive: it does not rewrite existing
 * pharmacy transactions. Existing data can be backfilled in a later,
 * explicitly verified migration after an organization/branch is selected.
 */
class CreateBusinessLocations extends Migration
{
    protected $DBGroup = 'pharmacy';

    private const TABLE_OPTIONS = [
        'ENGINE' => 'InnoDB',
        'CHARSET' => 'utf8mb4',
        'COLLATE' => 'utf8mb4_unicode_ci',
    ];

    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'legal_name' => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true, 'default' => null],
            'tax_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'default' => null],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'default' => null],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'default' => null],
            'address' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('code', false, true, 'uq_organizations_code');
        $this->forge->createTable('organizations', false, self::TABLE_OPTIONS);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'organization_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'code' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'branch_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'store'],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'default' => null],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true, 'default' => null],
            'address' => ['type' => 'TEXT', 'null' => true],
            'timezone' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => false, 'default' => 'Asia/Jakarta'],
            'is_default' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['organization_id', 'code'], false, true, 'uq_branches_org_code');
        $this->forge->addKey(['organization_id', 'is_active'], false, false, 'idx_branches_org_active');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'RESTRICT', 'fk_branches_organization');
        $this->forge->createTable('branches', false, self::TABLE_OPTIONS);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'organization_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'branch_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'code' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'warehouse_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'general'],
            'address' => ['type' => 'TEXT', 'null' => true],
            'is_default' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['branch_id', 'code'], false, true, 'uq_warehouses_branch_code');
        $this->forge->addKey(['organization_id', 'branch_id'], false, false, 'idx_warehouses_org_branch');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'RESTRICT', 'fk_warehouses_organization');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'RESTRICT', 'fk_warehouses_branch');
        $this->forge->createTable('warehouses', false, self::TABLE_OPTIONS);

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'organization_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'branch_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => false],
            'role' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false, 'default' => 'staff'],
            'is_default' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
            'updated_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'branch_id'], false, true, 'uq_branch_users_user_branch');
        $this->forge->addKey(['organization_id', 'branch_id', 'is_active'], false, false, 'idx_branch_users_scope');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'RESTRICT', 'fk_branch_users_organization');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'RESTRICT', 'fk_branch_users_branch');
        $this->forge->createTable('branch_users', false, self::TABLE_OPTIONS);
    }

    public function down()
    {
        // Drop dependants first to preserve foreign-key order.
        $this->forge->dropTable('branch_users', true);
        $this->forge->dropTable('warehouses', true);
        $this->forge->dropTable('branches', true);
        $this->forge->dropTable('organizations', true);
    }
}
