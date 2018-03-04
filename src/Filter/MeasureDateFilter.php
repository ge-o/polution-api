<?php
namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class MeasureDateFilter extends DateFilter
{
    /**
     * {@inheritdoc}
     */
    protected function filterProperty(string $property, $values, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        // Expect $values to be an array having the period as keys and the date value as values
        if (!\is_array($values) ||
            !$this->isPropertyEnabled($property, $resourceClass) ||
            !$this->isPropertyMapped($property, $resourceClass) ||
            !$this->isDateField($property, $resourceClass)
        ) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        if ($this->isPropertyNested($property, $resourceClass)) {
            list($alias, $field) = $this->addJoinsForNestedProperty($property, $alias, $queryBuilder, $queryNameGenerator, $resourceClass);
        }

        $nullManagement = $this->properties[$property] ?? null;
        $type = $this->getDoctrineFieldType($property, $resourceClass);

        if (self::EXCLUDE_NULL === $nullManagement) {
            $queryBuilder->andWhere($queryBuilder->expr()->isNotNull(sprintf('%s.%s', $alias, $field)));
        }

        if (isset($values[self::PARAMETER_BEFORE])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                self::PARAMETER_BEFORE,
                $values[self::PARAMETER_BEFORE],
                $nullManagement,
                $type
            );
        }

        if (isset($values[self::PARAMETER_STRICTLY_BEFORE])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                self::PARAMETER_STRICTLY_BEFORE,
                $values[self::PARAMETER_STRICTLY_BEFORE],
                $nullManagement,
                $type
            );
        }

        if (isset($values[self::PARAMETER_AFTER])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                self::PARAMETER_AFTER,
                $values[self::PARAMETER_AFTER],
                $nullManagement,
                $type
            );
        }

        if (isset($values[self::PARAMETER_STRICTLY_AFTER])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                self::PARAMETER_STRICTLY_AFTER,
                $values[self::PARAMETER_STRICTLY_AFTER],
                $nullManagement,
                $type
            );
        }
    }

    /**
     * Adds the where clause according to the chosen null management.
     *
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $alias
     * @param string                      $field
     * @param string                      $operator
     * @param string                      $value
     * @param string|null                 $nullManagement
     * @param string|Type                 $type
     */
    protected function addWhere(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $alias,
        string $field,
        string $operator,
        string $value,
        string $nullManagement = null,
        $type = null
    ) {
        try {
            $value = false === strpos($type, '_immutable') ? new \DateTime($value) : new \DateTimeImmutable($value);
        } catch (\Exception $e) {
            // Silently ignore this filter if it can not be transformed to a \DateTime
            $this->logger->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(sprintf('The field "%s" has a wrong date format. Use one accepted by the \DateTime constructor', $field)),
            ]);

            return;
        }

        $valueParameter = $queryNameGenerator->generateParameterName($field);
        $operatorValue = [
            self::PARAMETER_BEFORE => '<=',
            self::PARAMETER_STRICTLY_BEFORE => '<',
            self::PARAMETER_AFTER => '>=',
            self::PARAMETER_STRICTLY_AFTER => '>',
        ];
        $baseWhere = sprintf('%s.%s %s :%s', $alias, $field, $operatorValue[$operator], $valueParameter);

        if (null === $nullManagement || self::EXCLUDE_NULL === $nullManagement) {
            $queryBuilder->andWhere($baseWhere);
        } elseif ((\in_array($operator, [self::PARAMETER_BEFORE, self::PARAMETER_STRICTLY_BEFORE], true) && self::INCLUDE_NULL_BEFORE === $nullManagement) ||
            (\in_array($operator, [self::PARAMETER_AFTER, self::PARAMETER_STRICTLY_AFTER], true) && self::INCLUDE_NULL_AFTER === $nullManagement)
        ) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $baseWhere,
                $queryBuilder->expr()->isNull(sprintf('%s.%s', $alias, $field))
            ));
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->andX(
                $baseWhere,
                $queryBuilder->expr()->isNotNull(sprintf('%s.%s', $alias, $field))
            ));
        }

        $queryBuilder->setParameter($valueParameter, $value, $type);
    }
}
