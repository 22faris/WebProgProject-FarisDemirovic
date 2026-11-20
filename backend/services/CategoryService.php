<?php
declare(strict_types=1);
final class CategoryService extends EntityService
{
    protected function fields(): array { return ['user_id','name','type']; }
    protected function validate(array $data,bool $creating): array
    {
        $data=$this->cleanFields($data,$creating); $out=[];
        foreach (['user_id','name','type'] as $field) if (array_key_exists($field,$data)) {
            if ($field==='user_id') { $out[$field]=$this->positiveId($data[$field],$field); if (!$this->exists('users',$out[$field])) throw new ApiException(400,'Referenced user does not exist.'); }
            elseif ($field==='name') $out[$field]=$this->text($data[$field],$field,50); else $out[$field]=$this->enum($data[$field],$field,['income','expense']);
        } return $out;
    }
}
