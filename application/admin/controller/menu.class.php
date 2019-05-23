<?php
/**
 * Created by PhpStorm.
 * User: 小灰灰
 * Date:  2019/05/03 17:39
 * Other: 菜单管理
 */
defined('IN_YZMPHP') or exit('Access Denied');
yzm_base::load_controller('common', 'admin', 0);

class menu extends common {
    
    /**
     * 菜单管理列表
     */
    public function init() {
        $tree = yzm_base::load_sys_class('tree');
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $data = D('menu')->order('listorder ASC')->select();
    
        $array = array();
        foreach($data as $v) {
            $v['string'] = '
            <a title="增加子类" href="javascript:;" onclick="WeAdminShow(\'增加菜单\',\''.U('add',array('parentid'=>$v['id'])).'\',800,560)" style="text-decoration:none"  class="layui-btn layui-btn-xs">增加子类</a>
            <a title="编辑菜单" href="javascript:;" onclick="WeAdminShow(\'编辑菜单\',\''.U('edit',array('id'=>$v['id'])).'\',800,560)" style="text-decoration:none"  class="layui-btn layui-btn-xs layui-btn-normal">编辑</a>
            <a title="删除" href="javascript:;" onclick="WeAdminDel(\''.U('delete',array('id'=>$v['id'])).'\')" style="text-decoration:none"  class="layui-btn layui-btn-xs layui-btn-danger">删除</a>';
            $v['display'] = $v['display'] ? '<span class="layui-btn layui-btn-xs">显示</span>' : '<span class="layui-btn layui-btn-xs layui-bg-red">隐藏</span>';
            $array[] = $v;
        }
        $str  = "<tr>
					<td><input name='listorders[\$id]' type='text' value='\$listorder' class='input-text listorder'></td>
					<td>\$id</td>
					<td>\$spacer\$name</td>
					<td style='text-align: center'>\$display</td>
					<td>\$string</td>
				</tr>";
        $tree->init($array);
        $menus = $tree->get_tree(0, $str);
        include $this->admin_tpl('menu_list');
    }
    
    /**
     * 删除菜单
     */
    public function delete() {
        $id = intval($_GET['id']);
        $menu = D('menu');
        if($menu->where(['parentid'=>$id])->total() > 0){
            showmsg('删除失败，该菜单下有子菜单！');
        }else{
            $menu->delete(['id'=>$id]);
            delcache('menu_string_1');
        }
        showmsg(L('operation_success'));
    }
    
    /**
     * 添加菜单
     */
    public function add(){
        $menu = D('menu');
        if(isset($_POST['dosubmit'])) {
            $menu->insert($_POST, true);
            delcache('menu_string_1');
            return_json(array('status'=>1,'message'=>L('operation_success')));
        }else{
            $parentid = isset($_GET['parentid']) ? intval($_GET['parentid']) : 0;
            $tree = yzm_base::load_sys_class('tree');
            $data = D('menu')->order('listorder ASC,id DESC')->select();
            $array = array();
            foreach($data as $v) {
                $v['selected'] = $v['id'] == $parentid ? 'selected' : '';
                $array[] = $v;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_menus = $tree->get_tree(0, $str);
            include $this->admin_tpl('menu_add');
        }
    }
    
    /**
     * 编辑菜单
     */
    public function edit() {
        $menu = D('menu');
        if(isset($_POST['dosubmit'])) {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            unset($_POST['dosubmit']);
            $update = $menu->update($_POST, array('id' => $id));
            if($update){
                delcache('menu_string_1');
                return_json(['status'=>1,'message'=>L('operation_success')]);
            }else{
                return_json(['status'=>0,'message'=>'修改失败获取没有做任何修改！！！']);
            }
            
        }else{
            $parentid = isset($_GET['parentid']) ? intval($_GET['parentid']) : 0;
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $r = $menu->where(['id'=>$id])->find();
            if($r) extract($r);
            $tree = yzm_base::load_sys_class('tree');
            $r = $menu->order('listorder ASC,id DESC')->select();
            $array =[];
            foreach($r as $v) {
                $v['selected'] = $v['id'] == $parentid ? 'selected' : '';
                $array[] = $v;
            }
            $str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
            $tree->init($array);
            $select_menus = $tree->get_tree(0, $str);
            include $this->admin_tpl('menu_edit');
        }
        
    }
    
    /**
     * 菜单排序
     */
    function order() {
        if(isset($_POST['dosubmit'])) {
            $menu = D('menu');
            foreach($_POST['listorders'] as $id => $listorder) {
                $menu->update(['listorder'=>$listorder],['id'=>$id]);
            }
            delcache('menu_string_1');
            showmsg(L('operation_success'), '', 1);
        } else {
            showmsg(L('operation_failure'));
        }
    }
    
}