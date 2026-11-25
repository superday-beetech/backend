<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

/**
 * Add referential integrity for the Core organization domain.
 *
 * Existing organization tables and indexes are already present in the
 * cakra schema. This migration adds only foreign keys; it does not recreate
 * tables, rewrite records, or change the existing unique indexes.
 */
final class AddCoreOrganizationForeignKeys extends Migration
{
    private const CONSTRAINTS = [
        'fk_core_units_organization',
        'fk_core_units_parent',
        'fk_core_memberships_organization',
        'fk_core_memberships_user',
        'fk_core_memberships_legacy_unit',
        'fk_core_membership_units_membership',
        'fk_core_membership_units_unit',
    ];

    public function up(): void
    {
        $requiredTables = [
            'users',
            'core_organizations',
            'core_organization_units',
            'core_organization_memberships',
            'core_organization_membership_units',
        ];

        foreach ($requiredTables as $table) {
            if (! $this->db->tableExists($table)) {
                throw new RuntimeException(
                    "Cannot add Core organization constraints: table {$table} does not exist."
                );
            }
        }

        $this->assertNoOrphans(
            'SELECT COUNT(*) AS total
             FROM core_organization_units u
             LEFT JOIN core_organizations o ON o.id = u.organization_id
             WHERE o.id IS NULL',
            'core_organization_units.organization_id references a missing organization.'
        );

        $this->assertNoOrphans(
            'SELECT COUNT(*) AS total
             FROM core_organization_units u
             LEFT JOIN core_organization_units p ON p.id = u.parent_id
             WHERE u.parent_id IS NOT NULL AND p.id IS NULL',
            'core_organization_units.parent_id references a missing unit.'
        );

        $this->assertNoOrphans(
            'SELECT COUNT(*) AS total
             FROM core_organization_memberships m
             LEFT JOIN core_organizations o ON o.id = m.organization_id
             WHERE o.id IS NULL',
            'core_organization_memberships.organization_id references a missing organization.'
        );

        $this->assertNoOrphans(
            'SELECT COUNT(*) AS total
             FROM core_organization_memberships m
             LEFT JOIN users u ON u.id = m.user_id
             WHERE u.id IS NULL',
            'core_organization_memberships.user_id references a missing user.'
        );

        $this->assertNoOrphans(
            'SELECT COUNT(*) AS total
             FROM core_organization_memberships m
             LEFT JOIN core_organization_units u ON u.id = m.unit_id
             WHERE m.unit_id IS NOT NULL AND u.id IS NULL',
            'core_organization_memberships.unit_id references a missing legacy unit.'
        );

        $this->assertNoOrphans(
            'SELECT COUNT(*) AS total
             FROM core_organization_membership_units mu
             LEFT JOIN core_organization_memberships m ON m.id = mu.membership_id
             WHERE m.id IS NULL',
            'core_organization_membership_units.membership_id references a missing membership.'
        );

        $this->assertNoOrphans(
            'SELECT COUNT(*) AS total
             FROM core_organization_membership_units mu
             LEFT JOIN core_organization_units u ON u.id = mu.organization_unit_id
             WHERE u.id IS NULL',
            'core_organization_membership_units.organization_unit_id references a missing unit.'
        );

        $this->addConstraint(
            'fk_core_units_organization',
            'ALTER TABLE core_organization_units
             ADD CONSTRAINT fk_core_units_organization
             FOREIGN KEY (organization_id)
             REFERENCES core_organizations (id)
             ON DELETE RESTRICT ON UPDATE CASCADE'
        );

        $this->addConstraint(
            'fk_core_units_parent',
            'ALTER TABLE core_organization_units
             ADD CONSTRAINT fk_core_units_parent
             FOREIGN KEY (parent_id)
             REFERENCES core_organization_units (id)
             ON DELETE RESTRICT ON UPDATE CASCADE'
        );

        $this->addConstraint(
            'fk_core_memberships_organization',
            'ALTER TABLE core_organization_memberships
             ADD CONSTRAINT fk_core_memberships_organization
             FOREIGN KEY (organization_id)
             REFERENCES core_organizations (id)
             ON DELETE RESTRICT ON UPDATE CASCADE'
        );

        $this->addConstraint(
            'fk_core_memberships_user',
            'ALTER TABLE core_organization_memberships
             ADD CONSTRAINT fk_core_memberships_user
             FOREIGN KEY (user_id)
             REFERENCES users (id)
             ON DELETE RESTRICT ON UPDATE CASCADE'
        );

        $this->addConstraint(
            'fk_core_memberships_legacy_unit',
            'ALTER TABLE core_organization_memberships
             ADD CONSTRAINT fk_core_memberships_legacy_unit
             FOREIGN KEY (unit_id)
             REFERENCES core_organization_units (id)
             ON DELETE SET NULL ON UPDATE CASCADE'
        );

        $this->addConstraint(
            'fk_core_membership_units_membership',
            'ALTER TABLE core_organization_membership_units
             ADD CONSTRAINT fk_core_membership_units_membership
             FOREIGN KEY (membership_id)
             REFERENCES core_organization_memberships (id)
             ON DELETE CASCADE ON UPDATE CASCADE'
        );

        $this->addConstraint(
            'fk_core_membership_units_unit',
            'ALTER TABLE core_organization_membership_units
             ADD CONSTRAINT fk_core_membership_units_unit
             FOREIGN KEY (organization_unit_id)
             REFERENCES core_organization_units (id)
             ON DELETE RESTRICT ON UPDATE CASCADE'
        );
    }

    public function down(): void
    {
        $this->dropConstraint(
            'core_organization_membership_units',
            'fk_core_membership_units_unit'
        );
        $this->dropConstraint(
            'core_organization_membership_units',
            'fk_core_membership_units_membership'
        );
        $this->dropConstraint(
            'core_organization_memberships',
            'fk_core_memberships_legacy_unit'
        );
        $this->dropConstraint(
            'core_organization_memberships',
            'fk_core_memberships_user'
        );
        $this->dropConstraint(
            'core_organization_memberships',
            'fk_core_memberships_organization'
        );
        $this->dropConstraint(
            'core_organization_units',
            'fk_core_units_parent'
        );
        $this->dropConstraint(
            'core_organization_units',
            'fk_core_units_organization'
        );
    }

    private function assertNoOrphans(string $sql, string $message): void
    {
        $row = $this->db->query($sql)->getRowArray();

        if ((int) ($row['total'] ?? 0) > 0) {
            throw new RuntimeException($message);
        }
    }

    private function addConstraint(string $name, string $sql): void
    {
        if ($this->constraintExists($name)) {
            return;
        }

        $this->db->query($sql);
    }

    private function dropConstraint(string $table, string $name): void
    {
        if (! $this->constraintExists($name)) {
            return;
        }

        $this->db->query(
            "ALTER TABLE {$table} DROP FOREIGN KEY {$name}"
        );
    }

    private function constraintExists(string $name): bool
    {
        $row = $this->db->query(
            'SELECT COUNT(*) AS total
             FROM information_schema.REFERENTIAL_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND CONSTRAINT_NAME = ?',
            [$name]
        )->getRowArray();

        return (int) ($row['total'] ?? 0) > 0;
    }
}
