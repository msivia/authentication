<?php

namespace UAlberta\Authentication;

use Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface {

    /**
     * Finds a user by their CCID
     * @param $ccid
     * @return Illuminate\Database\Eloquent\Model|\Depotwarehouse\Toolbox\Datamanagement\EloquentModels\BaseModel|static
     */
    public function findByCCID($ccid);

} 