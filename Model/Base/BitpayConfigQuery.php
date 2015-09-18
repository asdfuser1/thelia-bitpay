<?php

namespace Bitpay\Model\Base;

use \Exception;
use \PDO;
use Bitpay\Model\BitpayConfig as ChildBitpayConfig;
use Bitpay\Model\BitpayConfigQuery as ChildBitpayConfigQuery;
use Bitpay\Model\Map\BitpayConfigTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'bitpay_config' table.
 *
 * 
 *
 * @method     ChildBitpayConfigQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildBitpayConfigQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method     ChildBitpayConfigQuery groupByName() Group by the name column
 * @method     ChildBitpayConfigQuery groupByValue() Group by the value column
 *
 * @method     ChildBitpayConfigQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildBitpayConfigQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildBitpayConfigQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildBitpayConfig findOne(ConnectionInterface $con = null) Return the first ChildBitpayConfig matching the query
 * @method     ChildBitpayConfig findOneOrCreate(ConnectionInterface $con = null) Return the first ChildBitpayConfig matching the query, or a new ChildBitpayConfig object populated from the query conditions when no match is found
 *
 * @method     ChildBitpayConfig findOneByName(string $name) Return the first ChildBitpayConfig filtered by the name column
 * @method     ChildBitpayConfig findOneByValue(string $value) Return the first ChildBitpayConfig filtered by the value column
 *
 * @method     array findByName(string $name) Return ChildBitpayConfig objects filtered by the name column
 * @method     array findByValue(string $value) Return ChildBitpayConfig objects filtered by the value column
 *
 */
abstract class BitpayConfigQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Bitpay\Model\Base\BitpayConfigQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Bitpay\\Model\\BitpayConfig', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildBitpayConfigQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildBitpayConfigQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Bitpay\Model\BitpayConfigQuery) {
            return $criteria;
        }
        $query = new \Bitpay\Model\BitpayConfigQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildBitpayConfig|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BitpayConfigTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(BitpayConfigTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildBitpayConfig A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT NAME, VALUE FROM bitpay_config WHERE NAME = :p0';
        try {
            $stmt = $con->prepare($sql);            
            $stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildBitpayConfig();
            $obj->hydrate($row);
            BitpayConfigTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildBitpayConfig|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildBitpayConfigQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BitpayConfigTableMap::NAME, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildBitpayConfigQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BitpayConfigTableMap::NAME, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBitpayConfigQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(BitpayConfigTableMap::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBitpayConfigQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $value)) {
                $value = str_replace('*', '%', $value);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(BitpayConfigTableMap::VALUE, $value, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildBitpayConfig $bitpayConfig Object to remove from the list of results
     *
     * @return ChildBitpayConfigQuery The current query, for fluid interface
     */
    public function prune($bitpayConfig = null)
    {
        if ($bitpayConfig) {
            $this->addUsingAlias(BitpayConfigTableMap::NAME, $bitpayConfig->getName(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the bitpay_config table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BitpayConfigTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            BitpayConfigTableMap::clearInstancePool();
            BitpayConfigTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildBitpayConfig or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildBitpayConfig object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BitpayConfigTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(BitpayConfigTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        BitpayConfigTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            BitpayConfigTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // BitpayConfigQuery
