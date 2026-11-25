<?php

declare(strict_types=1);

namespace Modules\Core\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Generated from SQL snapshot: cakra (9).sql; app-owned schema only
 *
 * IMPORTANT:
 * - Intended for a fresh database only.
 * - Does not import INSERT statements or production data.
 * - Framework-owned Shield/Settings tables are managed by their own migrations.
 */
final class CreateCakraApplicationSchema extends Migration
{
    protected $DBGroup = 'default';

    private array $createStatements = [
        'CREATE TABLE `auth_rbac_audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `actor_user_id` int UNSIGNED DEFAULT NULL,
  `target_user_id` int UNSIGNED DEFAULT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varbinary(16) DEFAULT NULL,
  `user_agent` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `core_organizations` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `legal_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'active\',
  `timezone` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'Asia/Jakarta\',
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'IDR\',
  `settings` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `core_organization_memberships` (
  `id` bigint UNSIGNED NOT NULL,
  `organization_id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `membership_role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'member\',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'active\',
  `is_default` tinyint(1) NOT NULL DEFAULT \'0\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `core_organization_membership_units` (
  `id` bigint UNSIGNED NOT NULL,
  `membership_id` bigint UNSIGNED NOT NULL,
  `organization_unit_id` bigint UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT \'0\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `core_organization_units` (
  `id` bigint UNSIGNED NOT NULL,
  `organization_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'department\',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'active\',
  `metadata` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `developer_recovery_audits` (
  `id` bigint UNSIGNED NOT NULL,
  `recovery_session_id` bigint UNSIGNED DEFAULT NULL,
  `actor_user_id` int UNSIGNED DEFAULT NULL,
  `event` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `outcome` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent_hash` char(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `metadata_json` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `developer_recovery_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `status` varchar(16) COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'active\',
  `verification_method` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `session_fingerprint` char(64) COLLATE utf8mb4_general_ci NOT NULL,
  `totp_counter` bigint UNSIGNED NOT NULL,
  `activated_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `developer_recovery_totp` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `secret_encrypted` text COLLATE utf8mb4_unicode_ci,
  `pending_secret_encrypted` text COLLATE utf8mb4_unicode_ci,
  `confirmed_at` datetime DEFAULT NULL,
  `last_counter` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `menus` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT \'#\',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_id` int UNSIGNED DEFAULT \'0\',
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT \'sidebar\',
  `section` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT \'backend\',
  `sequence` int DEFAULT \'0\',
  `description` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_divider` tinyint(1) NOT NULL DEFAULT \'0\',
  `is_active` tinyint(1) DEFAULT \'1\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `menus_backup_20261118` (
  `id` int UNSIGNED NOT NULL DEFAULT \'0\',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT \'#\',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_id` int UNSIGNED DEFAULT \'0\',
  `position` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT \'sidebar\',
  `section` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT \'backend\',
  `sequence` int DEFAULT \'0\',
  `description` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_divider` tinyint(1) NOT NULL DEFAULT \'0\',
  `is_active` tinyint(1) DEFAULT \'1\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;',
        'CREATE TABLE `menu_groups` (
  `menu_id` int UNSIGNED NOT NULL,
  `group_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `route_registry` (
  `id` bigint UNSIGNED NOT NULL,
  `module` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `controller` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `endpoint` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_discovered` tinyint(1) NOT NULL DEFAULT \'1\',
  `is_routable` tinyint(1) NOT NULL DEFAULT \'0\',
  `is_allowed` tinyint(1) NOT NULL DEFAULT \'0\',
  `scan_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'new\',
  `last_scanned_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `route_registry_menus` (
  `id` bigint UNSIGNED NOT NULL,
  `route_id` bigint UNSIGNED NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `route_registry_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `route_id` bigint UNSIGNED NOT NULL,
  `permission` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `route_registry_verbs` (
  `id` bigint UNSIGNED NOT NULL,
  `route_id` bigint UNSIGNED NOT NULL,
  `http_verb` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `sessions` (
  `id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT \'0\',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `storage_files` (
  `id` bigint UNSIGNED NOT NULL,
  `disk` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `storage_key` varchar(512) COLLATE utf8mb4_general_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `mime_type` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `extension` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `checksum` char(64) COLLATE utf8mb4_general_ci NOT NULL,
  `organization_id` bigint UNSIGNED DEFAULT NULL,
  `uploaded_by` int UNSIGNED DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'active\',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `storage_file_relations` (
  `id` bigint UNSIGNED NOT NULL,
  `file_id` bigint UNSIGNED NOT NULL,
  `module` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `entity_type` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `entity_id` varchar(191) COLLATE utf8mb4_general_ci NOT NULL,
  `purpose` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'attachment\',
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `tbl_email_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'system\',
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'email\',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
  `body_html` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `body_text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `variables` json DEFAULT NULL,
  `from_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply_to` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` int UNSIGNED NOT NULL DEFAULT \'1\',
  `is_system` tinyint(1) NOT NULL DEFAULT \'0\',
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `tbl_settings` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `input_type` enum(\'input\',\'textarea\',\'radio\',\'checkbox\',\'dropdown\',\'timezones\',\'file\') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `options` text COMMENT \'Use for radio and dropdown: key|value on each line\',
  `is_type` varchar(31) DEFAULT NULL,
  `show_editor` enum(\'0\',\'1\') NOT NULL DEFAULT \'0\',
  `label` varchar(128) NOT NULL,
  `value` text COMMENT \'If translate is 1, just start with your default language\',
  `class` varchar(255) NOT NULL,
  `help_text` text,
  `validation` varchar(128) NOT NULL,
  `sort_order` tinyint UNSIGNED NOT NULL,
  `created_at` varchar(31) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT \'general\',
  `deleted` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;',
        'CREATE TABLE `user_organizations` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `organization_unit_id` int UNSIGNED NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT \'1\',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum(\'aktif\',\'selesai\') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'aktif\',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `user_positions` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `position_id` int UNSIGNED NOT NULL,
  `organization_unit_id` int UNSIGNED DEFAULT NULL,
  `atasan_user_id` int UNSIGNED DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT \'1\',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum(\'aktif\',\'selesai\') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'aktif\',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `user_profiles` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `nama_lengkap` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_panggilan` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_pegawai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum(\'L\',\'P\') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_telepon_alternatif` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `foto_profil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `user_profile_photos` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `file_id` bigint UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;'
    ];

    private array $alterStatements = [
        'ALTER TABLE `auth_rbac_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rbac_audit_actor_time` (`actor_user_id`,`created_at`),
  ADD KEY `idx_rbac_audit_target_time` (`target_user_id`,`created_at`),
  ADD KEY `idx_rbac_audit_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_rbac_audit_action_time` (`action`,`created_at`),
  ADD KEY `idx_rbac_audit_request` (`request_id`);',
        'ALTER TABLE `core_organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_core_org_code` (`code`),
  ADD UNIQUE KEY `uq_core_org_slug` (`slug`),
  ADD KEY `idx_core_org_status` (`status`),
  ADD KEY `idx_core_org_deleted` (`deleted_at`);',
        'ALTER TABLE `core_organization_memberships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_core_org_user` (`organization_id`,`user_id`),
  ADD KEY `idx_core_membership_user_status` (`user_id`,`status`),
  ADD KEY `idx_core_membership_unit` (`unit_id`);',
        'ALTER TABLE `core_organization_membership_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_membership_unit` (`membership_id`,`organization_unit_id`),
  ADD KEY `idx_membership_units_unit` (`organization_unit_id`),
  ADD KEY `idx_membership_units_default` (`membership_id`,`is_default`);',
        'ALTER TABLE `core_organization_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_core_unit_org_code` (`organization_id`,`code`),
  ADD KEY `idx_core_unit_parent` (`parent_id`),
  ADD KEY `idx_core_unit_org_status` (`organization_id`,`status`);',
        'ALTER TABLE `developer_recovery_audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recovery_audit_actor_time` (`actor_user_id`,`created_at`),
  ADD KEY `idx_recovery_audit_event_time` (`event`,`created_at`);',
        'ALTER TABLE `developer_recovery_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recovery_user_status_expiry` (`user_id`,`status`,`expires_at`),
  ADD KEY `idx_recovery_totp_counter` (`totp_counter`);',
        'ALTER TABLE `developer_recovery_totp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_developer_recovery_totp_user` (`user_id`);',
        'ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);',
        'ALTER TABLE `menu_groups`
  ADD PRIMARY KEY (`menu_id`,`group_name`);',
        'ALTER TABLE `route_registry`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_route_registry_endpoint` (`endpoint`),
  ADD KEY `idx_route_registry_module` (`module`),
  ADD KEY `idx_route_registry_controller` (`controller`),
  ADD KEY `idx_route_registry_status` (`is_discovered`,`is_routable`,`is_allowed`);',
        'ALTER TABLE `route_registry_menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_route_registry_menus_route` (`route_id`);',
        'ALTER TABLE `route_registry_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_route_registry_permission` (`route_id`,`permission`),
  ADD KEY `idx_route_registry_permission` (`permission`);',
        'ALTER TABLE `route_registry_verbs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_route_registry_verb` (`route_id`,`http_verb`);',
        'ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`,`ip_address`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`),
  ADD KEY `id` (`id`);',
        'ALTER TABLE `storage_files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `disk_storage_key` (`disk`,`storage_key`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `status` (`status`),
  ADD KEY `checksum` (`checksum`);',
        'ALTER TABLE `storage_file_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `module_entity_type_entity_id` (`module`,`entity_type`,`entity_id`),
  ADD KEY `purpose` (`purpose`);',
        'ALTER TABLE `tbl_email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email_templates_code` (`code`),
  ADD KEY `idx_email_templates_category` (`category`),
  ADD KEY `idx_email_templates_channel_active` (`channel`,`is_active`),
  ADD KEY `idx_email_templates_active` (`is_active`);',
        'ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `updated_by` (`updated_by`);',
        'ALTER TABLE `user_organizations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_organizations_user` (`user_id`),
  ADD KEY `idx_user_organizations_unit` (`organization_unit_id`),
  ADD KEY `idx_user_organizations_status` (`status`),
  ADD KEY `idx_user_organizations_period` (`user_id`,`tanggal_mulai`,`tanggal_selesai`);',
        'ALTER TABLE `user_positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_positions_user` (`user_id`),
  ADD KEY `idx_user_positions_position` (`position_id`),
  ADD KEY `idx_user_positions_unit` (`organization_unit_id`),
  ADD KEY `idx_user_positions_atasan` (`atasan_user_id`),
  ADD KEY `idx_user_positions_status` (`status`),
  ADD KEY `idx_user_positions_period` (`user_id`,`tanggal_mulai`,`tanggal_selesai`);',
        'ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_profiles_user` (`user_id`),
  ADD UNIQUE KEY `uq_user_profiles_nomor_pegawai` (`nomor_pegawai`);',
        'ALTER TABLE `user_profile_photos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_profile_photos_user_id` (`user_id`);',
        'ALTER TABLE `auth_rbac_audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `core_organizations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;',
        'ALTER TABLE `core_organization_memberships`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;',
        'ALTER TABLE `core_organization_membership_units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;',
        'ALTER TABLE `core_organization_units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;',
        'ALTER TABLE `developer_recovery_audits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `developer_recovery_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `developer_recovery_totp`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `menus`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=318;',
        'ALTER TABLE `route_registry`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;',
        'ALTER TABLE `route_registry_menus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;',
        'ALTER TABLE `route_registry_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;',
        'ALTER TABLE `route_registry_verbs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;',
        'ALTER TABLE `storage_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `storage_file_relations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `tbl_email_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;',
        'ALTER TABLE `tbl_settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;',
        'ALTER TABLE `user_organizations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `user_positions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `user_profiles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;',
        'ALTER TABLE `user_profile_photos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `auth_rbac_audit_logs`
  ADD CONSTRAINT `fk_rbac_audit_actor` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rbac_audit_target` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;',
        'ALTER TABLE `menu_groups`
  ADD CONSTRAINT `menu_groups_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `route_registry_menus`
  ADD CONSTRAINT `fk_route_registry_menus_route` FOREIGN KEY (`route_id`) REFERENCES `route_registry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `route_registry_permissions`
  ADD CONSTRAINT `fk_route_registry_permissions_route` FOREIGN KEY (`route_id`) REFERENCES `route_registry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `route_registry_verbs`
  ADD CONSTRAINT `fk_route_registry_verbs_route` FOREIGN KEY (`route_id`) REFERENCES `route_registry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;'
    ];

    private array $tables = [
        'auth_rbac_audit_logs',
        'core_organizations',
        'core_organization_memberships',
        'core_organization_membership_units',
        'core_organization_units',
        'developer_recovery_audits',
        'developer_recovery_sessions',
        'developer_recovery_totp',
        'menus',
        'menus_backup_20261118',
        'menu_groups',
        'route_registry',
        'route_registry_menus',
        'route_registry_permissions',
        'route_registry_verbs',
        'sessions',
        'storage_files',
        'storage_file_relations',
        'tbl_email_templates',
        'tbl_settings',
        'user_organizations',
        'user_positions',
        'user_profiles',
        'user_profile_photos'
    ];

    public function up(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        try {
            foreach ($this->createStatements as $sql) {
                $this->db->query($sql);
            }

            foreach ($this->alterStatements as $sql) {
                $this->db->query($sql);
            }
        } finally {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    public function down(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        try {
            foreach (array_reverse($this->tables) as $table) {
                $this->db->query('DROP TABLE IF EXISTS `' . $table . '`');
            }
        } finally {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}
