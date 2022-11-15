<?php

namespace Modules\core\src\Controllers;

class PageModifyController extends ProtectedAbstractController
{
    public function saveAction()
    {
        if(!\App\User::i()->checkCsrf())return;
        
        $windows = \App\Request::i()->get('windows');
        $windows = json_decode($windows);
        if(!$windows) return;

        $res = [];
        $modules = \App\Modules\Modules::i()->loadModulesFromDb();
        
        foreach(['main','body','sidebar'] as $column){
            $res[$column] = [
                'name' => $column,
                'windows' => []
            ];
            if(isset($windows->$column)){
                foreach($windows->$column as $window){
                    list($m,$w) = explode('.',$window);
                    if(!isset($modules[$m])) continue;
                    if(!isset($modules[$m]['windows'][$w])) continue;
                    $res[$column]['windows'][] = [
                        'params' => '',
                        'name' => "Modules\\$m\\windows\\$w"
                    ];
                }
            }
        }
        
        $page = 'page/main';
        \App\Store::i()->$page = $res;
        
        \App\Output::i()->output = 'success';
    }
    
    public function indexAction()
    {
        $modules = \App\Modules\Modules::i()->loadModulesFromDb();
        $res = [];
        foreach($modules as $mName => $module){
            foreach($module['windows'] as $wName => $window){
                $res["$mName.$wName"] = ($window['enabled'] AND $module['enabled']) ?1:0;
            }
        }
        $modules = $res;
        unset($res);
        
        $page = 'page/main';
        $data = \App\Store::i()->$page;
        foreach($data as $k => $v){
            $v = $v['windows'];
            foreach($v as $vk => $vv){
                $vv = explode('\\',$vv['name']);
                $name = "{$vv[1]}.{$vv[3]}";
                $v[$vk]['name'] = $name;
                if(isset($modules[$name])){
                    $v[$vk]['enabled'] = $modules[$name];
                    unset($modules[$name]);
                }
                else{
                    $v[$vk]['enabled'] = 0;
                }
            }
            $data[$k] = $v;
        }
        
        \App\Output::i()->output = \App\Twig::i()->render('pageModify/core_page_modify.twig',[
            'data' => $data,
            'modules' => $modules
        ]);
    }
}