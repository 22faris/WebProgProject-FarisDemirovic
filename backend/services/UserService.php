<?php
declare(strict_types=1);
final class UserService extends EntityService
{
    protected function fields(): array { return ['full_name','email','password']; }
    protected function safe(array $row): array { unset($row['password_hash']); return $row; }
    protected function validate(array $data,bool $creating): array
    {
        $data=$this->cleanFields($data,false); if ($creating&&(!isset($data['full_name'],$data['email'],$data['password']))) throw new ApiException(400,'full_name, email, and password are required.'); $out=[];
        if (array_key_exists('full_name',$data)) $out['full_name']=$this->text($data['full_name'],'full_name',100);
        if (array_key_exists('email',$data)) { $email=$this->text($data['email'],'email',254); if (!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new ApiException(400,'email must be a valid email address.'); $out['email']=$email; }
        if (array_key_exists('password',$data)) { if (!is_string($data['password'])||strlen($data['password'])<8||strlen($data['password'])>255) throw new ApiException(400,'password must be 8 to 255 characters.'); $out['password_hash']=password_hash($data['password'],PASSWORD_DEFAULT); }
        return $out;
    }
    public function create(array $data): array { $id=$this->dao->create($this->validate($data,true)); return $this->get($id); }
}
