<?php namespace Pensoft\Mails;

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents(): array
    {
        return [];
    }

    public function registerSettings(): array
    {
        return [];
    }

    public function registerNavigation(): array
    {
        return [
            'main-menu-item' => [
                'label'       => 'Mails',
                'url'         => Backend::url('pensoft/mails/groups'),
                'icon'        => 'icon-envelope-square',
                'permissions' => ['pensoft.mails.groups'],
                'order'       => 500,

                'sideMenu' => [
                    'groups' => [
                        'label' => 'Groups',
                        'url'   => Backend::url('pensoft/mails/groups'),
                        'icon'  => 'icon-users',
                        'permissions' => ['pensoft.mails.*'],
                    ],
                    'data' => [
                        'label' => 'Data',
                        'url'   => Backend::url('pensoft/mails/data'),
                        'icon'  => 'icon-database',
                        'permissions' => ['pensoft.mails.*'],
                    ],
                ],
            ],
        ];
    }

    public function registerPermissions(): array
    {
        return [
            'pensoft.mails' => [
                'tab' => 'Mails',
                'label' => 'Mail data',
            ],
            'pensoft.mails.groups' => [
                'tab' => 'Mails',
                'label' => 'Mail groups',
            ],
        ];
    }
}
