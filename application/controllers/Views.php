<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * views controller
 *
 * @author Paul
 */
class Views extends Application {

    public function index() {
        $this->data['pagetitle'] = 'Ordered TODO List';
        $tasks = $this->tasks->all();   // get all the tasks
        $this->data['content'] = 'Ok'; // so we don't need pagebody
        $this->data['leftside'] = 'by_priority';
        $this->data['rightside'] = 'by_category';
        $this->data['leftside'] = $this->makePrioritizedPanel($tasks);
        $this->data['rightside'] = $this->makeCategorizedPanel($tasks);

        $this->render('template_secondary');
    }

    function makePrioritizedPanel($tasks) {
        $parms = ['display_tasks' => []];
        // extract the undone tasks
        foreach ($tasks as $task) {
            if ($task->status != 2) {
                $undone[] = $task;
            }
        }
        // order them by priority
        usort($undone, "orderByPriority");
        // substitute the priority name
        foreach ($undone as $task) {
            $task->priority = $this->app->priority($task->priority);
        }
        // convert the array of task objects into an array of associative objects       
        foreach ($undone as $task) {
            $converted[] = (array) $task;
        }

        // and then pass them on
        $parms = ['display_tasks' => $converted];
        return $this->parser->parse('by_priority', $parms, true);
    }

    function makeCategorizedPanel($tasks) {
        $parms = ['display_tasks' => $this->tasks->getCategorizedTasks()];
        return $this->parser->parse('by_category', $parms, true);
    }

}

// return -1, 0, or 1 of $a's priority is higher, equal to, or lower than $b's
function orderByPriority($a, $b) {
    if ($a->priority > $b->priority) {
        return -1;
    } else if ($a->priority < $b->priority) {
        return 1;
    } else {
        return 0;
    }
}