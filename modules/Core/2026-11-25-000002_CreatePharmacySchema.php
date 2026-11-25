<?php

declare(strict_types=1);

namespace Modules\Core\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Generated from SQL snapshot: cakra_pharmacy (12).sql
 *
 * IMPORTANT:
 * - Intended for a fresh database only.
 * - Does not import INSERT statements or production data.
 * - Framework-owned Shield/Settings tables are managed by their own migrations.
 */
final class CreatePharmacySchema extends Migration
{
    protected $DBGroup = 'apps';

    private array $createStatements = [
        'CREATE TABLE `active_ingredients` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `inn_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cas_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `cash_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `transaction_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_date` datetime NOT NULL,
  `transaction_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` bigint UNSIGNED NOT NULL,
  `description` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `module` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'general\',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'umum\',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `category_types` (
  `id` tinyint UNSIGNED NOT NULL,
  `module` varchar(30) NOT NULL,
  `code` varchar(30) NOT NULL,
  `label` varchar(50) NOT NULL,
  `sort_order` smallint UNSIGNED DEFAULT \'0\',
  `is_active` tinyint(1) DEFAULT \'1\'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;',
        'CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'retail\',
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `customer_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `distribution_order_id` bigint UNSIGNED DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `deliveries` (
  `id` bigint UNSIGNED NOT NULL,
  `delivery_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distribution_order_id` bigint UNSIGNED NOT NULL,
  `delivery_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `recipient_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipient_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `delivery_items` (
  `id` bigint UNSIGNED NOT NULL,
  `delivery_id` bigint UNSIGNED NOT NULL,
  `distribution_item_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `quantity_dispatched` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_price` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `distribution_items` (
  `id` bigint UNSIGNED NOT NULL,
  `distribution_order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_price` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `distribution_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `order_date` date NOT NULL,
  `requested_delivery_date` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `tax_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `total_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `doctors` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sip_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialization` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `drug_interactions` (
  `id` bigint UNSIGNED NOT NULL,
  `ingredient_a_id` bigint UNSIGNED NOT NULL,
  `ingredient_b_id` bigint UNSIGNED NOT NULL,
  `severity` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'moderate\',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `management` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `expense_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `goods_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `receipt_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `received_date` date NOT NULL,
  `supplier_delivery_number` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `goods_receipt_items` (
  `id` bigint UNSIGNED NOT NULL,
  `goods_receipt_id` bigint UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity_received` decimal(16,3) NOT NULL DEFAULT \'0.000\',
  `unit_id` bigint UNSIGNED NOT NULL,
  `conversion_factor` decimal(18,6) NOT NULL DEFAULT \'1.000000\',
  `quantity_base` decimal(18,3) GENERATED ALWAYS AS (round((`quantity_received` * `conversion_factor`),3)) STORED,
  `batch_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expired_at` date DEFAULT NULL,
  `unit_cost` decimal(16,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `prescriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `prescription_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `doctor_id` bigint UNSIGNED DEFAULT NULL,
  `doctor_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_sip_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_age` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prescription_date` date NOT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'pending\',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `prescription_items` (
  `id` bigint UNSIGNED NOT NULL,
  `prescription_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `sale_item_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` decimal(18,3) NOT NULL,
  `conversion_factor` decimal(18,6) NOT NULL DEFAULT \'1.000000\',
  `quantity_base` decimal(18,3) GENERATED ALWAYS AS (round((`quantity` * `conversion_factor`),3)) STORED,
  `dosage_instruction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usage_instruction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT \'0\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `print_templates` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `paper_type` varchar(20) NOT NULL,
  `paper_config` json NOT NULL,
  `design_json` json NOT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;',
        'CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `purchase_price` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `selling_price` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `product_attributes` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `attr_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attr_group` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `severity` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value_num` decimal(18,3) DEFAULT NULL,
  `value_num_2` decimal(18,3) DEFAULT NULL,
  `value_json` json DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `ref_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT \'0\',
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT \'0\',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `product_batches` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `goods_receipt_item_id` bigint UNSIGNED NOT NULL,
  `batch_number` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` date DEFAULT NULL,
  `received_quantity` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `quantity_remaining` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_cost` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'available\',
  `status_before_recall` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `product_details` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `generic_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_ingredient` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dosage_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strength` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drug_class` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bpom_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requires_prescription` tinyint(1) NOT NULL DEFAULT \'0\',
  `unit_conversion` json DEFAULT NULL,
  `min_stock` int NOT NULL DEFAULT \'0\',
  `max_stock` int DEFAULT NULL,
  `rack_location` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `product_dosage_forms` (
  `id` tinyint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT \'0\',
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `product_drug_classes` (
  `id` tinyint UNSIGNED NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT \'0\',
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `product_prices` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `customer_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'NULL berarti harga umum\',
  `min_qty` decimal(18,3) NOT NULL DEFAULT \'1.000\',
  `price` decimal(18,2) NOT NULL,
  `valid_from` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valid_to` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `customer_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (coalesce(`customer_type`,_utf8mb4\'__GENERAL__\')) STORED,
  `from_key` datetime GENERATED ALWAYS AS (`valid_from`) STORED,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `product_recalls` (
  `id` bigint UNSIGNED NOT NULL,
  `recall_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'medium\',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'open\',
  `recalled_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` datetime DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `product_recall_batches` (
  `id` bigint UNSIGNED NOT NULL,
  `recall_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `quantity_at_recall` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `action_taken` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `product_recall_products` (
  `id` bigint UNSIGNED NOT NULL,
  `recall_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `product_units` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `conversion_factor` decimal(18,6) NOT NULL DEFAULT \'1.000000\' COMMENT \'Jumlah satuan dasar dalam satu unit ini\',
  `barcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_base` tinyint(1) NOT NULL DEFAULT \'0\',
  `is_purchase_unit` tinyint(1) NOT NULL DEFAULT \'0\',
  `is_sale_unit` tinyint(1) NOT NULL DEFAULT \'1\',
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT \'0\',
  `base_key` tinyint UNSIGNED GENERATED ALWAYS AS (if((`is_base` = 1),1,NULL)) STORED,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `purchase_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `purchase_mode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT \'delivery\',
  `order_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `tax_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `total_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `purchase_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_id` bigint UNSIGNED NOT NULL,
  `conversion_factor` decimal(18,6) NOT NULL DEFAULT \'1.000000\',
  `quantity_base` decimal(18,3) GENERATED ALWAYS AS (round((`quantity` * `conversion_factor`),3)) STORED,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `purchase_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `return_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `goods_receipt_id` bigint UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `purchase_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `goods_receipt_item_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_cost` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `sale_date` datetime NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `tax_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `rounding_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `total_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `paid_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `amount_received` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `change_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `payment_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'unpaid\',
  `settlement_mode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'paid\',
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `collateral_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `sales_audit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;',
        'CREATE TABLE `sales_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `return_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_id` bigint UNSIGNED DEFAULT NULL,
  `return_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `sales_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sales_return_id` bigint UNSIGNED NOT NULL,
  `sale_item_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_item_id` bigint UNSIGNED DEFAULT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_price` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `restockable` tinyint(1) NOT NULL DEFAULT \'0\',
  `condition_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_id` bigint UNSIGNED NOT NULL,
  `conversion_factor` decimal(18,6) NOT NULL DEFAULT \'1.000000\',
  `quantity_base` decimal(18,3) GENERATED ALWAYS AS (round((`quantity` * `conversion_factor`),3)) STORED,
  `unit_price` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `discount_amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `subtotal` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `unit_cost` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `sale_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `reference_number` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
);',
        'CREATE TABLE `sale_sync_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `idempotency_key` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `payload_hash` char(64) COLLATE utf8mb4_general_ci NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
        'CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `movement_date` datetime NOT NULL,
  `movement_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` bigint UNSIGNED NOT NULL,
  `quantity_in` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `quantity_out` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `unit_cost` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `stock_opnames` (
  `id` bigint UNSIGNED NOT NULL,
  `opname_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opname_date` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `stock_opname_items` (
  `id` bigint UNSIGNED NOT NULL,
  `stock_opname_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `system_quantity` decimal(18,3) NOT NULL DEFAULT \'0.000\',
  `counted_quantity` decimal(18,3) DEFAULT NULL,
  `difference_quantity` decimal(18,3) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT \'1\',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `supplier_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_number` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `purchase_order_id` bigint UNSIGNED DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT \'0.00\',
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
        'CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
    ];

    private array $alterStatements = [
        'ALTER TABLE `active_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ai_name` (`name`);',
        'ALTER TABLE `cash_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_number` (`transaction_number`),
  ADD KEY `transaction_date` (`transaction_date`),
  ADD KEY `transaction_type` (`transaction_type`),
  ADD KEY `reference_type_reference_id` (`reference_type`,`reference_id`);',
        'ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_categories_module` (`module`),
  ADD KEY `idx_categories_type` (`type`);',
        'ALTER TABLE `category_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_module_code` (`module`,`code`);',
        'ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `uq_customers_member_number` (`member_number`),
  ADD UNIQUE KEY `uq_customers_barcode` (`barcode`),
  ADD KEY `name` (`name`),
  ADD KEY `customer_type` (`customer_type`),
  ADD KEY `idx_customers_code` (`code`);',
        'ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_number` (`payment_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `distribution_order_id` (`distribution_order_id`),
  ADD KEY `payment_date` (`payment_date`),
  ADD KEY `fk_customer_payments_sale` (`sale_id`);',
        'ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_number` (`delivery_number`),
  ADD KEY `distribution_order_id` (`distribution_order_id`),
  ADD KEY `delivery_date` (`delivery_date`),
  ADD KEY `status` (`status`);',
        'ALTER TABLE `delivery_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_id` (`delivery_id`),
  ADD KEY `distribution_item_id` (`distribution_item_id`),
  ADD KEY `batch_id` (`batch_id`);',
        'ALTER TABLE `distribution_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `distribution_order_id` (`distribution_order_id`),
  ADD KEY `product_id` (`product_id`);',
        'ALTER TABLE `distribution_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_date` (`order_date`),
  ADD KEY `status` (`status`);',
        'ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_doctors_sip` (`sip_number`),
  ADD KEY `idx_doctors_name` (`name`),
  ADD KEY `idx_doctors_active` (`is_active`);',
        'ALTER TABLE `drug_interactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_drug_interactions_pair` (`ingredient_a_id`,`ingredient_b_id`),
  ADD KEY `idx_drug_interactions_ingredient_b` (`ingredient_b_id`),
  ADD KEY `idx_drug_interactions_severity` (`severity`),
  ADD KEY `idx_drug_interactions_active` (`is_active`);',
        'ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_number` (`expense_number`),
  ADD KEY `expense_date` (`expense_date`),
  ADD KEY `category` (`category`);',
        'ALTER TABLE `goods_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `received_date` (`received_date`),
  ADD KEY `status` (`status`);',
        'ALTER TABLE `goods_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goods_receipt_id` (`goods_receipt_id`),
  ADD KEY `purchase_order_item_id` (`purchase_order_item_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_gri_product_unit` (`product_id`,`unit_id`),
  ADD KEY `idx_gri_batch_expiry` (`batch_number`,`expired_at`);',
        'ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_prescriptions_number` (`prescription_number`),
  ADD KEY `idx_prescriptions_sale` (`sale_id`),
  ADD KEY `idx_prescriptions_customer` (`customer_id`),
  ADD KEY `idx_prescriptions_doctor` (`doctor_id`),
  ADD KEY `idx_prescriptions_date` (`prescription_date`),
  ADD KEY `idx_prescriptions_status` (`status`);',
        'ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prescription_items_prescription` (`prescription_id`),
  ADD KEY `idx_prescription_items_product_unit` (`product_id`,`unit_id`),
  ADD KEY `idx_prescription_items_sale_item` (`sale_item_id`);',
        'ALTER TABLE `print_templates`
  ADD PRIMARY KEY (`id`);',
        'ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `barcode` (`barcode`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `idx_products_active_category` (`is_active`,`category_id`),
  ADD KEY `idx_products_name` (`name`),
  ADD KEY `idx_products_deleted_at` (`deleted_at`);',
        'ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pa_product_type` (`product_id`,`attr_type`),
  ADD KEY `idx_pa_type_group` (`attr_type`,`attr_group`),
  ADD KEY `idx_pa_ref` (`ref_type`,`ref_id`);',
        'ALTER TABLE `product_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `goods_receipt_item_id` (`goods_receipt_item_id`),
  ADD KEY `batch_number` (`batch_number`),
  ADD KEY `expired_at` (`expired_at`),
  ADD KEY `product_id_expired_at` (`product_id`,`expired_at`),
  ADD KEY `idx_product_batches_product_status` (`product_id`,`status`,`quantity_remaining`),
  ADD KEY `idx_product_batches_fefo` (`product_id`,`status`,`expired_at`,`quantity_remaining`),
  ADD KEY `idx_product_batches_number` (`batch_number`);',
        'ALTER TABLE `product_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`),
  ADD KEY `drug_class` (`drug_class`),
  ADD KEY `idx_pd_drug_class` (`drug_class`);',
        'ALTER TABLE `product_dosage_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `is_active` (`is_active`);',
        'ALTER TABLE `product_drug_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `is_active` (`is_active`);',
        'ALTER TABLE `product_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_product_prices_rule` (`product_id`,`unit_id`,`customer_key`,`min_qty`,`from_key`),
  ADD KEY `idx_product_prices_lookup` (`product_id`,`unit_id`,`is_active`,`customer_type`,`min_qty`,`valid_from`,`valid_to`),
  ADD KEY `idx_product_prices_period` (`valid_from`,`valid_to`);',
        'ALTER TABLE `product_recalls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_product_recalls_number` (`recall_number`),
  ADD KEY `idx_product_recalls_status` (`status`),
  ADD KEY `idx_product_recalls_date` (`recalled_at`);',
        'ALTER TABLE `product_recall_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_recall_batches` (`recall_id`,`batch_id`),
  ADD KEY `idx_recall_batches_batch` (`batch_id`);',
        'ALTER TABLE `product_recall_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_recall_products` (`recall_id`,`product_id`),
  ADD KEY `idx_recall_products_product` (`product_id`);',
        'ALTER TABLE `product_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_product_units_product_unit` (`product_id`,`unit_id`),
  ADD UNIQUE KEY `uq_product_units_base` (`product_id`,`base_key`),
  ADD UNIQUE KEY `uq_product_units_barcode` (`barcode`),
  ADD KEY `idx_product_units_unit` (`unit_id`),
  ADD KEY `idx_product_units_product_active` (`product_id`,`is_active`);',
        'ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `order_date` (`order_date`),
  ADD KEY `status` (`status`);',
        'ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_poi_product_unit` (`product_id`,`unit_id`);',
        'ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `goods_receipt_id` (`goods_receipt_id`),
  ADD KEY `return_date` (`return_date`),
  ADD KEY `status` (`status`);',
        'ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_id` (`purchase_return_id`),
  ADD KEY `goods_receipt_item_id` (`goods_receipt_item_id`),
  ADD KEY `batch_id` (`batch_id`);',
        'ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_sale_number` (`sale_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `sale_date` (`sale_date`),
  ADD KEY `status` (`status`),
  ADD KEY `payment_status` (`payment_status`),
  ADD KEY `idx_sales_status_id` (`status`,`id`);',
        'ALTER TABLE `sales_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `created_at` (`created_at`);',
        'ALTER TABLE `sales_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `delivery_id` (`delivery_id`),
  ADD KEY `return_date` (`return_date`),
  ADD KEY `status` (`status`),
  ADD KEY `fk_sales_returns_sale` (`sale_id`);',
        'ALTER TABLE `sales_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_return_id` (`sales_return_id`),
  ADD KEY `delivery_item_id` (`delivery_item_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `fk_sales_return_items_sale_item` (`sale_item_id`);',
        'ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `idx_sale_items_sale_id` (`sale_id`),
  ADD KEY `idx_sale_items_product_unit` (`product_id`,`unit_id`);',
        'ALTER TABLE `sale_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale_payments_sale` (`sale_id`),
  ADD KEY `idx_sale_payments_method` (`payment_method`),
  ADD KEY `idx_sale_payments_paid_at` (`paid_at`),
  ADD KEY `idx_sale_payments_reference` (`reference_number`);',
        'ALTER TABLE `sale_sync_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sale_sync_user_key` (`user_id`,`idempotency_key`),
  ADD KEY `sale_sync_requests_sale_id_index` (`sale_id`);',
        'ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `movement_date` (`movement_date`),
  ADD KEY `reference_type_reference_id` (`reference_type`,`reference_id`),
  ADD KEY `product_id_movement_date` (`product_id`,`movement_date`);',
        'ALTER TABLE `stock_opnames`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `opname_number` (`opname_number`),
  ADD KEY `opname_date` (`opname_date`),
  ADD KEY `status` (`status`);',
        'ALTER TABLE `stock_opname_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_opname_id_batch_id` (`stock_opname_id`,`batch_id`),
  ADD KEY `stock_opname_id` (`stock_opname_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`);',
        'ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `name` (`name`);',
        'ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_number` (`payment_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `payment_date` (`payment_date`);',
        'ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `abbreviation` (`abbreviation`);',
        'ALTER TABLE `active_ingredients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `cash_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `category_types`
  MODIFY `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `customer_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `deliveries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `delivery_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `distribution_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `distribution_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `doctors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `drug_interactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `goods_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `goods_receipt_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `prescriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `prescription_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `print_templates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_attributes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_batches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_dosage_forms`
  MODIFY `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_drug_classes`
  MODIFY `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_prices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_recalls`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_recall_batches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_recall_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `product_units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `purchase_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `purchase_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `sales_audit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `sales_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `sales_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `sale_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `sale_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `stock_opnames`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `stock_opname_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `supplier_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;',
        'ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_payments_distribution_order_id_foreign` FOREIGN KEY (`distribution_order_id`) REFERENCES `distribution_orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_customer_payments_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;',
        'ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_distribution_order_id_foreign` FOREIGN KEY (`distribution_order_id`) REFERENCES `distribution_orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `delivery_items`
  ADD CONSTRAINT `delivery_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `delivery_items_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `delivery_items_distribution_item_id_foreign` FOREIGN KEY (`distribution_item_id`) REFERENCES `distribution_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `distribution_items`
  ADD CONSTRAINT `distribution_items_distribution_order_id_foreign` FOREIGN KEY (`distribution_order_id`) REFERENCES `distribution_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `distribution_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `distribution_orders`
  ADD CONSTRAINT `distribution_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `drug_interactions`
  ADD CONSTRAINT `fk_drug_interactions_ingredient_a` FOREIGN KEY (`ingredient_a_id`) REFERENCES `active_ingredients` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_drug_interactions_ingredient_b` FOREIGN KEY (`ingredient_b_id`) REFERENCES `active_ingredients` (`id`) ON DELETE RESTRICT;',
        'ALTER TABLE `goods_receipts`
  ADD CONSTRAINT `goods_receipts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `goods_receipt_items`
  ADD CONSTRAINT `fk_gri_product_unit` FOREIGN KEY (`product_id`,`unit_id`) REFERENCES `product_units` (`product_id`, `unit_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `goods_receipt_items_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `goods_receipt_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `goods_receipt_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_prescriptions_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prescriptions_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prescriptions_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;',
        'ALTER TABLE `prescription_items`
  ADD CONSTRAINT `fk_prescription_items_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prescription_items_product_unit` FOREIGN KEY (`product_id`,`unit_id`) REFERENCES `product_units` (`product_id`, `unit_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prescription_items_sale_item` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;',
        'ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `product_attributes`
  ADD CONSTRAINT `fk_pa_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `product_batches`
  ADD CONSTRAINT `product_batches_goods_receipt_item_id_foreign` FOREIGN KEY (`goods_receipt_item_id`) REFERENCES `goods_receipt_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `product_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `product_details`
  ADD CONSTRAINT `product_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `product_prices`
  ADD CONSTRAINT `fk_product_prices_product_unit` FOREIGN KEY (`product_id`,`unit_id`) REFERENCES `product_units` (`product_id`, `unit_id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `product_recall_batches`
  ADD CONSTRAINT `fk_recall_batches_batch` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recall_batches_recall` FOREIGN KEY (`recall_id`) REFERENCES `product_recalls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `product_recall_products`
  ADD CONSTRAINT `fk_recall_products_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recall_products_recall` FOREIGN KEY (`recall_id`) REFERENCES `product_recalls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `product_units`
  ADD CONSTRAINT `fk_product_units_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_units_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `fk_poi_product_unit` FOREIGN KEY (`product_id`,`unit_id`) REFERENCES `product_units` (`product_id`, `unit_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_return_items_goods_receipt_item_id_foreign` FOREIGN KEY (`goods_receipt_item_id`) REFERENCES `goods_receipt_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `sales`
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `sales_audit_logs`
  ADD CONSTRAINT `fk_sales_audit_logs_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;',
        'ALTER TABLE `sales_returns`
  ADD CONSTRAINT `fk_sales_returns_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_returns_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `sales_return_items`
  ADD CONSTRAINT `fk_sales_return_items_sale_item` FOREIGN KEY (`sale_item_id`) REFERENCES `sale_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_return_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_return_items_delivery_item_id_foreign` FOREIGN KEY (`delivery_item_id`) REFERENCES `delivery_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_return_items_sales_return_id_foreign` FOREIGN KEY (`sales_return_id`) REFERENCES `sales_returns` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `sale_items`
  ADD CONSTRAINT `fk_sale_items_product_unit` FOREIGN KEY (`product_id`,`unit_id`) REFERENCES `product_units` (`product_id`, `unit_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `sale_payments`
  ADD CONSTRAINT `fk_sale_payments_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `sale_sync_requests`
  ADD CONSTRAINT `sale_sync_requests_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;',
        'ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
        'ALTER TABLE `stock_opname_items`
  ADD CONSTRAINT `stock_opname_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `product_batches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_opname_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_opname_items_stock_opname_id_foreign` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;'
    ];

    private array $tables = [
        'active_ingredients',
        'cash_transactions',
        'categories',
        'category_types',
        'customers',
        'customer_payments',
        'deliveries',
        'delivery_items',
        'distribution_items',
        'distribution_orders',
        'doctors',
        'drug_interactions',
        'expenses',
        'goods_receipts',
        'goods_receipt_items',
        'prescriptions',
        'prescription_items',
        'print_templates',
        'products',
        'product_attributes',
        'product_batches',
        'product_details',
        'product_dosage_forms',
        'product_drug_classes',
        'product_prices',
        'product_recalls',
        'product_recall_batches',
        'product_recall_products',
        'product_units',
        'purchase_orders',
        'purchase_order_items',
        'purchase_returns',
        'purchase_return_items',
        'sales',
        'sales_audit_logs',
        'sales_returns',
        'sales_return_items',
        'sale_items',
        'sale_payments',
        'sale_sync_requests',
        'stock_movements',
        'stock_opnames',
        'stock_opname_items',
        'suppliers',
        'supplier_payments',
        'units'
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
