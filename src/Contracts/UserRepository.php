<?php

namespace UAlberta\IST\Authentication\Contracts;

use Depotwarehouse\Toolbox\DataManagement\Repositories\ActiveRepository;
use Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface UserRepository extends ActiveRepository
{

    /**
     * Finds a user by their CCID
     * @param $ccid
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ModelNotFoundException
     */
    public function findByCCID($ccid);

} 
