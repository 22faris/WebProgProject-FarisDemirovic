<?php
declare(strict_types=1);
abstract class EntityService
{
    public function __construct(protected BaseDAO $dao, protected PDO $pdo) {}
    abstract protected function fields(): array;
    abstract protected function validate(array $data, bool $creating): array;
    protected function safe(array $row): array { return $row; }
    public function all(): array { return array_map(fn($row) => $this->safe($row), $this->dao->findAll()); }
    public function get(int $id): array { $row=$this->dao->findById($id); if ($row===null) throw new ApiException(404,'Record not found.','not_found'); return $this->safe($row); }
    public function create(array $data): array { $id=$this->dao->create($this->validate($data,true)); return $this->get($id); }
    public function update(int $id,array $data): array { $this->get($id); if (!$this->dao->update($id,$this->validate($data,false))) throw new ApiException(404,'Record not found.','not_found'); return $this->get($id); }
    public function delete(int $id): void { $this->get($id); if (!$this->dao->delete($id)) throw new ApiException(404,'Record not found.','not_found'); }
    protected function cleanFields(array $data,bool $creating): array
    {
        if ($data===[]) throw new ApiException(400,'Request body must contain fields.');
        foreach ($data as $key=>$_) if (!in_array($key,$this->fields(),true)) throw new ApiException(400,"Unsupported field: {$key}.");
        if ($creating) foreach ($this->fields() as $field) if (!array_key_exists($field,$data)) throw new ApiException(400,"Missing required field: {$field}.");
        return $data;
    }
    protected function positiveId(mixed $value,string $field): int { $id=filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]); if ($id===false) throw new ApiException(400,"{$field} must be a positive integer."); return $id; }
    protected function text(mixed $value,string $field,int $max): string { if (!is_string($value)||trim($value)===''||mb_strlen(trim($value))>$max) throw new ApiException(400,"{$field} must be a non-empty string of at most {$max} characters."); return trim($value); }
    protected function enum(mixed $value,string $field,array $allowed): string { if (!is_string($value)||!in_array($value,$allowed,true)) throw new ApiException(400,"{$field} must be one of: ".implode(', ',$allowed).'.'); return $value; }
    protected function amount(mixed $value,string $field): string
    { if (!(is_int($value)||is_float($value)||(is_string($value)&&is_numeric($value)))||!is_finite((float)$value)||(float)$value<=0||(float)$value>=10000000000||preg_match('/^\d+(?:\.\d{1,2})?$/',(string)$value)!==1) throw new ApiException(400,"{$field} must be a positive amount with at most two decimal places."); return number_format((float)$value,2,'.',''); }
    protected function date(mixed $value,string $field): string { if (!is_string($value)) throw new ApiException(400,"{$field} must be a valid YYYY-MM-DD date."); $date=DateTimeImmutable::createFromFormat('!Y-m-d',$value); if (!$date||$date->format('Y-m-d')!==$value) throw new ApiException(400,"{$field} must be a valid YYYY-MM-DD date."); return $value; }
    protected function exists(string $table,int $id): bool
    {
        $class=['users'=>UserDAO::class,'categories'=>CategoryDAO::class,'transactions'=>TransactionDAO::class,'budgets'=>BudgetDAO::class][$table]??null;
        if ($class===null) return false;
        return (new $class($this->pdo))->findById($id)!==null;
    }
    protected function userCategoryExists(int $categoryId,int $userId): bool
    {
        $category=(new CategoryDAO($this->pdo))->findById($categoryId);
        return $category!==null&&(int)$category['user_id']===$userId;
    }
}
