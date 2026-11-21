<?php

declare(strict_types=1);

namespace Modules\Pharmacy\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPosSalesReturnIndexes extends Migration
{
    protected $DBGroup = 'pharmacy';

    public function up()
    {
        $this->db->query(
            'ALTER TABLE sales_returns ADD INDEX sales_returns_sale_status_idx (sale_id, status)'
        );

        $this->db->query(
            'ALTER TABLE sales_return_items ADD INDEX sales_return_items_sale_item_idx (sale_item_id)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE sales_return_items DROP INDEX sales_return_items_sale_item_idx'
        );

        $this->db->query(
            'ALTER TABLE sales_returns DROP INDEX sales_returns_sale_status_idx'
        );
    }
}
