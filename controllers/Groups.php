<?php namespace Pensoft\Mails\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Cms\Classes\Theme;
use Illuminate\Support\Facades\DB;
use Pensoft\Mails\Models\Groups as GroupsModel;

class Groups extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ReorderController::class,
    ];

    public string $listConfig = 'config_list.yaml';
    public string $formConfig = 'config_form.yaml';
    public string $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Pensoft.Mails', 'main-menu-item', 'groups');
    }

    public function onSync() {
        $theme = Theme::getActiveTheme();
        //update from alias table
        $aliasData = DB::connection('vmail')->select('SELECT * FROM alias a LEFT JOIN list_replace_options lro ON lro.list_address = a.address WHERE a.domain = \'' . trim($theme->site_domain ?? $_SERVER['SERVER_NAME']) . '\' AND a.islist = 1');
        if(count($aliasData)){
            foreach($aliasData as $alias){
                $groups = GroupsModel::where('address', $alias->address)->first();

                if(!$groups){
                    $groups = new GroupsModel();
                }
                $groups->address = $alias->address;
                $groups->goto = $alias->goto;
                $groups->moderators = $alias->moderators;
                $groups->accesspolicy = $alias->accesspolicy;
                $groups->domain = $alias->domain;
                $groups->active = (int)$alias->active;
                $groups->reply_to = $alias->reply_to;
                $groups->replace_from = $alias->replace_from;
                $groups->replace_to = $alias->replace_to;
                $groups->name_append = $alias->name_append;
                $groups->add_reply_to = $alias->add_reply_to;
                $groups->save();
            }
        }
        \Flash::info('Synced with vmail!');
        return $this->asExtension('ListController')->listRefresh();

    }
}
