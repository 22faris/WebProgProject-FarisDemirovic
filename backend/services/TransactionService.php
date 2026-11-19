<?php
declare(strict_types=1);
final class TransactionService extends EntityService
{
    protected function fields(): array { return ['user_id','category_id','amount','type','description','transaction_date']; }
    protected function validate(array $data,bool $creating): array
    {
        $data=$this->cleanFields($data,$creating); $out=[];
        foreach (['user_id','category_id'] as $field) if (array_key_exists($field,$data)) $out[$field]=$this->positiveId($data[$field],$field);
        if (isset($out['user_id'])&&!$this->exists('users',$out['user_id'])) throw new ApiException(400,'Referenced user does not exist.');
        if (isset($out['category_id'],$out['user_id'])&&!$this->userCategoryExists($out['category_id'],$out['user_id'])) throw new ApiException(400,'Category must exist and belong to the selected user.');
        if (isset($data['category_id'])&&!isset($out['user_id'])&&!$this->exists('categories',$this->positiveId($data['category_id'],'category_id'))) throw new ApiException(400,'Referenced category does not exist.');
        if (array_key_exists('amount',$data)) $out['amount']=$this->amount($data['amount'],'amount');
        if (array_key_exists('type',$data)) $out['type']=$this->enum($data['type'],'type',['income','expense']);
        if (array_key_exists('description',$data)) $out['description']=$this->text($data['description'],'description',255);
        if (array_key_exists('transaction_date',$data)) $out['transaction_date']=$this->date($data['transaction_date'],'transaction_date'); return $out;
    }
}
