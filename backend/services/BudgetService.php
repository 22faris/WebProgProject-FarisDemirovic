<?php
declare(strict_types=1);
final class BudgetService extends EntityService
{
    protected function fields(): array { return ['user_id','category_id','amount_limit','month']; }
    protected function validate(array $data,bool $creating): array
    {
        $data=$this->cleanFields($data,$creating); $out=[];
        foreach (['user_id','category_id'] as $field) if (array_key_exists($field,$data)) $out[$field]=$this->positiveId($data[$field],$field);
        if (isset($out['user_id'])&&!$this->exists('users',$out['user_id'])) throw new ApiException(400,'Referenced user does not exist.');
        if (isset($out['category_id'],$out['user_id'])&&!$this->userCategoryExists($out['category_id'],$out['user_id'])) throw new ApiException(400,'Category must exist and belong to the selected user.');
        if (isset($data['category_id'])&&!isset($out['user_id'])&&!$this->exists('categories',$this->positiveId($data['category_id'],'category_id'))) throw new ApiException(400,'Referenced category does not exist.');
        if (array_key_exists('amount_limit',$data)) $out['amount_limit']=$this->amount($data['amount_limit'],'amount_limit');
        if (array_key_exists('month',$data)) { $out['month']=$this->date($data['month'],'month'); if (substr($out['month'],8,2)!=='01') throw new ApiException(400,'month must be the first day of its month.'); } return $out;
    }
}
