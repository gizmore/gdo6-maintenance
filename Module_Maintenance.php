<?php
namespace GDO\Maintenance;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_User;
use GDO\Login\Method\Logout;
use GDO\Core\GDT_Response;
use GDO\Maintenance\Method\ShowMaintenance;
use GDO\Core\Website;
use GDO\Date\GDT_DateTime;

/**
 * Maintenance module.
 * @author gizmore
 * @version 6.10.1
 * @since 6.10.1
 */
final class Module_Maintenance extends GDO_Module
{
    public $module_priority = 7; # kill user early.
    
    ##############
    ### Config ###
    ##############
    public function getConfig()
    {
        return [
            GDT_Checkbox::make('maintenance')->initial('0'),
            GDT_DateTime::make('maintenance_end'),
        ];
    }
    public function cfgMaintenance() { return $this->getConfigValue('maintenance'); }
    
    ############
    ### Init ###
    ############
    public function onInit()
    {
        if ($this->cfgMaintenance())
        {
            if (!GDO_User::current()->isAdmin())
            {
                $response = GDT_Response::make();
                if (module_enabled('Login'))
                {
                    $response->add(Logout::make()->executeWithInit());
                }
                Website::redirectMessage('err_maintenance_mode', null, href('Maintenance', 'ShowMaintenance'));
                GDO_User::setCurrent(GDO_User::ghost());
            }
        }
    }
    
}
