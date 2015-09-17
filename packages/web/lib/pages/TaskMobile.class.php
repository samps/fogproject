<?php
class TaskMobile extends FOGPage {
    public $node = 'tasks';
    public function __construct($name = '') {
        $this->name = 'Task Management';
        // Call parent constructor
        parent::__construct($this->name);
        if (is_numeric($_REQUEST[id]) && intval($_REQUEST[id])) $this->obj = $this->getClass(Task,$_REQUEST[id]);
        // Header Data
        $this->headerData = array(
            _('Force'),
            _('Task Name'),
            _('Host'),
            _('Type'),
            _('State'),
            _('Kill'),
        );
        // Attributes
        $this->attributes = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array('class'=>'filter-false'),
        );
        // Templates
        $this->templates = array(
            '${task_force}',
            '${task_name}',
            '${host_name}',
            '${task_type}',
            '${task_state}',
            '<a href="?node=${node}&sub=killtask&id=${task_id}"><i class="fa fa-minus-circle fa-2x task"></i></a>',
        );
    }
    public function index() {
        $Tasks = $this->getClass(TaskManager)->find(array(stateID=>array(1,2,3)));
        foreach($Tasks AS $i => &$Task) {
            $Host = $Task->getHost();
            $this->data[] = array(
                task_force=>(!$Task->get(isForced) ? '<a href="?node=${node}&sub=force&id=${task_id}"><i class="fa fa-step-forward fa-2x task"></i></a>' : '<i class="fa fa-play fa-2x task"></i>'),
                node=>$_REQUEST[node],
                task_id=>$Task->get(id),
                task_name=>$Task->get(name),
                host_name=>($Task->get(isForced) ? '* '.$Host->get(name) : $Host->get(name)),
                task_type=>$Task->getTaskTypeText(),
                task_state=>$Task->getTaskStateText(),
            );
        }
        unset($Task);
        $this->render();
    }
    public function search() {
        unset($this->headerData[0],$this->headerData[5],$this->attributes[0],$this->attributes[5],$this->templates[0],$this->templates[5]);
        parent::search();
    }
    public function search_post() {
        unset($this->headerData[0],$this->headerData[5],$this->attributes[0],$this->attributes[5],$this->templates[0],$this->templates[5]);
        $Tasks = $this->getClass(TaskManager)->search();
        foreach($Tasks AS $i => &$Task) {
            if (in_array($Task->get(stateID),array(0,1,2,3))) {
                $Host = $Task->getHost();
                $this->data[] = array(
                    task_id=>$Task->get(id),
                    task_name=>$Task->get(name),
                    host_name=>($Task->get(isForced) ? '* '.$Host->get(name) : $Host->get(name)),
                    task_type=> $Task->getTaskTypeText(),
                    task_state=> $Task->getTaskStateText(),
                );
            }
        }
        unset($Task);
        $this->render();
    }
    public function force() {
        $this->obj->set(isForced,true)->save();
        $this->FOGCore->redirect('?node='.$this->node);
    }
    public function killtask() {
        $this->obj->cancel();
        $this->FOGCore->redirect('?node='.$this->node);
    }
    public function active() {
        $Tasks = $this->getClass(TaskManager)->find(array(stateID=>array(1,2,3)));
        foreach ($Tasks AS $i => &$Task) {
            $Host = $Task->getHost();
            $this->data[] = array(
                task_id=>$Task->get(id),
                task_name=>$Task->get(name),
                host_name=>($Task->get(isForced) ? '* '.$Host->get(name) : $Host->get(name)),
                task_type=> $Task->getTaskTypeText(),
                task_state=> $Task->getTaskStateText(),
                task_force=>(!$Task->get(isForced) ? '<a href="?node=${node}&sub=force&id=${task_id}"><i class="fa fa-step-forward fa-2x task"></i></a>' : '<i class="fa fa-play fa-2x task"></i>'),
            );
        }
        unset($Task);
        $this->render();
    }
}
