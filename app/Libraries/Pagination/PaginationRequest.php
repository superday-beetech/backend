<?php

declare(strict_types=1);

namespace App\Libraries\Pagination;

use CodeIgniter\Database\BaseBuilder;
use Modules\Core\Exceptions\DomainException;

final class PaginationRequest
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;
    private const MAX_PER_PAGE = 100;

    public function __construct(
        private readonly int $page = self::DEFAULT_PAGE,
        private readonly int $perPage = self::DEFAULT_PER_PAGE,
        private readonly int $maxPerPage = self::MAX_PER_PAGE
    ) {
        if ($page < 1) {
            throw DomainException::badRequest('Page minimal 1.');
        }

        if ($perPage < 1) {
            throw DomainException::badRequest('Per page minimal 1.');
        }

        if ($maxPerPage < 1) {
            throw DomainException::badRequest(
                'Max per page minimal 1.'
            );
        }
    }

    public static function fromArray(array $params): self
    {
        return new self(
            self::parsePositiveInt(
                $params['page'] ?? self::DEFAULT_PAGE,
                'page'
            ),
            self::parsePositiveInt(
                $params['per_page'] ?? self::DEFAULT_PER_PAGE,
                'per_page'
            ),
            self::parsePositiveInt(
                $params['max_per_page'] ?? self::MAX_PER_PAGE,
                'max_per_page'
            )
        );
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return min($this->perPage, $this->maxPerPage);
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage();
    }

    public function apply(BaseBuilder $builder): BaseBuilder
    {
        return $builder->limit(
            $this->perPage(),
            $this->offset()
        );
    }

    private static function parsePositiveInt(
        mixed $value,
        string $field
    ): int {
        if (!is_int($value) && !is_string($value)) {
            throw DomainException::badRequest(
                "Parameter {$field} harus berupa angka positif."
            );
        }

        if (is_string($value) && ($value === '' || !ctype_digit($value))) {
            throw DomainException::badRequest(
                "Parameter {$field} harus berupa angka positif."
            );
        }

        $result = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($result === false) {
            throw DomainException::badRequest(
                "Parameter {$field} harus berupa angka positif."
            );
        }

        return $result;
    }
}
