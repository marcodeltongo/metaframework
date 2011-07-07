<?php

class DashboardController extends AclController
{

    public function actionIndex()
    {
        $this->render('index');
    }

    protected $aclTurnedOff = false;
}