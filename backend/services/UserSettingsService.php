<?php
declare(strict_types=1);
final class UserSettingsService extends EntityService
{
    protected function fields(): array { return ['user_id','preferred_currency']; }
    protected function validate(array $data,bool $creating): array
    {
        if (!$creating&&array_key_exists('user_id',$data)) throw new ApiException(400,'user_id cannot be changed.');
        $data=$this->cleanFields($data,false); if ($creating&&(!isset($data['user_id'],$data['preferred_currency'])||count($data)!==2)) throw new ApiException(400,'user_id and preferred_currency are required.'); $out=[];
        if (isset($data['user_id'])) { $out['user_id']=$this->positiveId($data['user_id'],'user_id'); if (!$this->exists('users',$out['user_id'])) throw new ApiException(400,'Referenced user does not exist.'); }
        if (isset($data['preferred_currency'])) { $currency=$data['preferred_currency']; if (!is_string($currency)||preg_match('/^[A-Z]{3}$/',$currency)!==1) throw new ApiException(400,'preferred_currency must be a 3-letter uppercase currency code.'); $out['preferred_currency']=$currency; }
        return $out;
    }
    public function all(): array { return $this->dao->findAll(); }
    public function get(int $id): array { $row=$this->dao->findById($id); if ($row===null) throw new ApiException(404,'Record not found.','not_found'); return $row; }
    public function create(array $data): array { $id=$this->dao->create($this->validate($data,true)); return $this->get($id); }
    public function update(int $id,array $data): array { $this->get($id); if (!$this->dao->update($id,$this->validate($data,false))) throw new ApiException(404,'Record not found.','not_found'); return $this->get($id); }
}
