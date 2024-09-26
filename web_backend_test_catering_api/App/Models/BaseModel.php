<?php 

namespace App\Models;

use App\Plugins\Di\Injectable;
use Exception;

/**
 * Class BaseModel
 *
 * @package App\Models
 */
class BaseModel extends Injectable {
    /**
     * @var string The name of the table associated with the model.
     */
    protected $table;

    protected $select;

    /**
     * @var array The query conditions.
     */
    protected $query;

    /**
     * @var int|null The limit for the query.
     */
    protected $limit;

    /**
     * @var array|null The order by clause for the query.
     */
    protected $orderBy;

    /**
     * @var string The primary key of the table.
     */
    protected $primaryKey = 'id';

    /**
     * @var array The relationships to eager load.
     */
    protected $with = [];
    
    /**
     * BaseModel constructor.
     */
    public function __construct(array|object $data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    
        $this->query = [];
        $this->limit = null;
        $this->orderBy = null;
        $this->select = null;
    }

    /**
     * Initialize a new query builder instance.
     *
     * @return static
     */
    public static function query() {
        return new static();
    }

    /**
     * Finds a model matching the given id.
     *
     * @param mixed $value
     * @return static|null
     */
    public function findById($value): ?static {
        return $this->where($this->primaryKey, '=', $value)->first();
    }    

    /**
     * Add a where condition to the query.
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function where($column, $operator, $value) {
        $this->query[] = [$column, $operator, $value];
        return $this;
    }

    /**
     * Add a like condition to the query.
     *
     * @param string $column
     * @param string $value
     * @return $this
     */
    public function like($column, $value) {
        return $this->where($column, 'LIKE', $value);
    }

    
    /**
     * Get the constructed SQL query string.
     *
     * @return string
     */
    public function getQuery() {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->query)) {
            $conditions = [];
            foreach ($this->query as $condition) {
                $conditions[] = "{$condition[0]} {$condition[1]} '{$condition[2]}'";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy[0]} {$this->orderBy[1]}";
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->select) {
            $sql = str_replace('*', $this->select, $sql);
        }

        return $sql;
    }   

    /**
     * Set the limit for the query.
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the order by clause for the query.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy = [$column, $direction];
        return $this;
    }

    /**
     * Set the select columns for the query.
     *
     * @param string $columns
     * @return $this
     */
    public function select($columns) {
        $this->select = $columns;
        return $this;
    }

    /**
     * Add a random order clause to the query.
     *
     * @return $this
     */
    public function inRandomOrder() {
        $this->orderBy = ['RAND()', ''];
        return $this;
    }

    /**
     * Add relationships to eager load.
     *
     * @param array $relations
     * @return $this
     */
    public function with($relations) {
        if (is_string($relations)) {
            $relations = [$relations];
        }
        
        foreach ($relations as $relation) {
            if (!method_exists($this, $relation)) {
                throw new Exception("Relation method {$relation} does not exist on model " . static::class);
            }
        }
    
        $this->with = array_merge($this->with, $relations);
        
        return $this;
    }

    /**
     * Execute the query and get the results.
     *
     * @return array
     */
    public function get() {
        $sql = "SELECT * FROM {$this->table}";
    
        if (!empty($this->query)) {
            $conditions = [];
            foreach ($this->query as $condition) {
                $conditions[] = "{$condition[0]} {$condition[1]} '{$condition[2]}'";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
    
        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy[0]} {$this->orderBy[1]}";
        }
    
        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }
    
        if ($this->select) {
            $sql = str_replace('*', $this->select, $sql);
        }
    
        $this->db->executeQuery($sql);
        $results = $this->db->getStatement()->fetchAll(\PDO::FETCH_ASSOC);
        
        $instances = [];
        foreach ($results as $result) {
            $instances[] = new static($result);
        }
    
        // Eager load relationships if any
        if (!empty($this->with)) {
            $this->eagerLoadRelations($instances);
        }
    
        return $instances;
    }


    // public function whereHas($relation, callable $callback)
    // {
    //     if (!method_exists($this, $relation)) {
    //         throw new Exception("Relation method {$relation} does not exist on model " . static::class);
    //     }

    //     // Get the query builder for the related model
    //     $relatedQuery = $this->{$relation}();

    //     // Apply the callback to modify the related query
    //     $callback($relatedQuery);

    //     // Fetch the related IDs that match the query
    //     $relatedIds = $relatedQuery->pluck($this->getForeignKey());

    //     // Apply the condition to the parent query (this model)
    //     if (!empty($relatedIds)) {
    //         $this->query[] = [$this->primaryKey, 'IN', '(' . implode(',', $relatedIds) . ')'];
    //     }

    //     return $this;
    // }

    // public function getForeignKey() {
    //     return $this->table . '_id';
    // }


        /**
     * Add an "or where" clause to the query for a relationship.
     *
     * @param string $relationModel The name of the relationship.
     * @param \Closure $callback The callback to apply to the relationship query.
     * @return $this
     */
    public function orWhereHas($relation, $foreignKey, callable $callback, $ownerKey = null) {
        $relatedModels = $this->hasMany($relation,  $foreignKey, $ownerKey);
        $filteredModels = $callback($relatedModels);
        $relatedIds = array_map(function($model) {
            return $model->id;
        }, $relatedModels);

        if (!empty($relatedIds)) {
            $this->query[] = ['OR', 'id', 'IN', '(' . implode(',', $relatedIds) . ')'];
        }

        return $this;
    }

        
    /**
     * Add an "or where" clause to the query for a relationship (scope version).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $relation The name of the relationship.
     * @param \Closure $callback The callback to apply to the relationship query.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrWhereHas($query, $relation, $callback) {
        return $query->orWhereHas($relation, $callback);
    }


    protected function eagerLoadRelations(array &$models) {
        foreach ($this->with as $relation) {
            $relationMethod = $relation;
            if (!method_exists($this, $relationMethod)) {
                continue;
            }
    
            // Group the model ids
            $ids = array_map(function ($model) {
                return $model->{$this->primaryKey};
            }, $models);
    
            // Fetch related models for this relation
            $relatedModels = $this->$relationMethod()->whereIn($this->primaryKey, $ids)->get();
    
            // Assign the related models to the parent models
            foreach ($models as $model) {
                $model->$relation = array_filter($relatedModels, function ($related) use ($model, $relationMethod) {
                    return $related->{$this->primaryKey} == $model->{$this->primaryKey};
                });
            }
        }
    }
    
    /**
     * Execute the query and get the first result.
     *
     * @return static|null
     */
    public function first() {
        $this->limit(1);
        $results = $this->get();
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Execute the query and get the last result.
     *
     * @return static|null
     */
    public function last() {
        $this->orderBy($this->primaryKey, 'DESC')->limit(1);
        $results = $this->get();
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Execute the delete query.
     *
     * @return int The number of affected rows.
     */
    public function delete() {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
    
        return $this->db->executeQuery($sql, [$this->id]);
    }

    /**
     * Execute the update query.
     *
     * @param array $data The data to update.
     * @return int The number of affected rows.
     */
    public function update(array $data) {
        $sql = "UPDATE {$this->table} SET ";
        $updates = [];
        foreach ($data as $column => $value) {
            $updates[] = "{$column} = '{$value}'";
        }
        $sql .= implode(', ', $updates);

        if (!empty($this->query)) {
            $conditions = [];
            foreach ($this->query as $condition) {
                $conditions[] = "{$condition[0]} {$condition[1]} '{$condition[2]}'";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        return $this->db->executeQuery($sql);
    }

    /**
     * Save the model to the database.
     *
     * @return bool
     */
    public function save() {
        $data = get_object_vars($this);

        // Get the list of protected properties
        $reflection = new \ReflectionClass($this);
        $protectedProperties = array_filter($reflection->getProperties(), function($property) {
            return $property->isProtected();
        });
        $protectedPropertyNames = array_map(function($property) {
            return $property->getName();
        }, $protectedProperties);

        // Remove protected properties from the data array
        foreach ($protectedPropertyNames as $protectedProperty) {
            unset($data[$protectedProperty]);
        }

        if (isset($this->{$this->primaryKey})) {
            // Update existing record
            $sql = "UPDATE {$this->table} SET ";
            $updates = [];
            foreach ($data as $column => $value) {
                $updates[] = "{$column} = ?";
            }
            $sql .= implode(', ', $updates);
            $sql .= " WHERE {$this->primaryKey} = ?";
            $values = array_values($data);
            $values[] = $this->{$this->primaryKey};

            return $this->db->executeQuery($sql, $values);
        }

        // Insert new record
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($data), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $values = array_values($data);

        return $this->db->executeQuery($sql, $values);
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $data The data to insert.
     * @return static|null The newly created model instance, or null on failure.
     * @throws Exception If the query fails.
     */
    public function create(array $data) {
        // Get the list of protected properties
        $reflection = new \ReflectionClass($this);
        $protectedProperties = array_filter($reflection->getProperties(), function($property) {
            return $property->isProtected();
        });
        $protectedPropertyNames = array_map(function($property) {
            return $property->getName();
        }, $protectedProperties);

        // Remove protected properties from the data array
        foreach ($protectedPropertyNames as $protectedProperty) {
            unset($data[$protectedProperty]);
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders});";

        // Execute the query using the database connection
        $this->db->executeQuery($sql, array_values($data));

        // Get the ID of the newly inserted record
        $lastInsertedId = $this->db->getLastInsertedId();

        // Fetch the newly created record
        $newRecord = $this->query()->where($this->primaryKey, '=', $lastInsertedId)->get();
        // Return the newly created model instance
        return $newRecord ? new static($newRecord[0]) : null;
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param string $related The related model class.
     * @param string $foreignKey The foreign key on the related model.
     * @param string $localKey The local key on this model.
     * @return static|null
     */
    public function hasOne($related, $foreignKey, $localKey = null) {
        $localKey = $localKey ?: $this->primaryKey;
        return (new $related)->where($foreignKey, '=', $this->$localKey)->first();
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param string $related The related model class.
     * @param string $foreignKey The foreign key on the related model.
     * @param string $localKey The local key on this model.
     * @return array
     */
    public function hasMany($related, $foreignKey, $localKey = null) {
        $localKey = $localKey ?: $this->primaryKey;
        return (new $related)->where($foreignKey, '=', $this->$localKey)->get();
    }

    /**
     * Define a belongs-to relationship.
     *
     * @param string $related The related model class.
     * @param string $foreignKey The foreign key on this model.
     * @param string $ownerKey The key on the related model.
     * @return static|null
     */
    public function belongsTo($related, $foreignKey, $ownerKey = null) {
        $ownerKey = $ownerKey ?: (new $related)->primaryKey;
        return (new $related)->where($ownerKey, '=', $this->$foreignKey)->first();
    }

    /**
     * Define a belongs-to-many relationship.
     *
     * @param string $related The related model class.
     * @param string $pivotTable The pivot table that connects the two models.
     * @param string $foreignKey The foreign key on the pivot table for the current model.
     * @param string $relatedKey The foreign key on the pivot table for the related model.
     * @param string|null $localKey The local key on the current model.
     * @param string|null $relatedPrimaryKey The primary key on the related model.
     * @return array
     */
    public function belongsToMany($related, $pivotTable, $foreignKey, $relatedKey, $localKey = null, $relatedPrimaryKey = null) {
        $localKey = $localKey ?: $this->primaryKey;
        $relatedPrimaryKey = $relatedPrimaryKey ?: (new $related)->primaryKey;

        $sql = "SELECT r.* FROM {$this->table} t
                INNER JOIN {$pivotTable} p ON t.{$localKey} = p.{$foreignKey}
                INNER JOIN {$related} r ON p.{$relatedKey} = r.{$relatedPrimaryKey}
                WHERE t.{$localKey} = ?";

        $this->db->executeQuery($sql, [$this->$localKey]);
        $results = $this->db->getStatement()->fetchAll(\PDO::FETCH_ASSOC);
        $instances = [];
        foreach ($results as $result) {
            $instances[] = new $related($result);
        }

        return $instances;
    }

    /**
     * Add a whereIn condition to the query.
     *
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function whereIn($column, array $values) {
        $inValues = implode(',', array_map(fn($val) => "'{$val}'", $values));
        $this->query[] = ["{$column}", 'IN', "({$inValues})"];
        return $this;
    }

}