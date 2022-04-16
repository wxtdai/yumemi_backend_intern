<?php

namespace App\Http\Controllers;

use App\Models\Rimotatsu;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    public function getTasks(Rimotatsu $rimotatsu)
    {
        $result = Task::query()->where('rimotatsu_id', $rimotatsu->id)->select(['id','title','achievement_condition'])->get();
        return $this->jsonResponse(['tasks'=>$result]);
    }

    public function getAchievedTasks(Rimotatsu $rimotatsu)
    {
        $achieved_tasks = Auth::user()->tasks()->where('rimotatsu_id', $rimotatsu->id)->get();
        $achieved_tasks_ids = $achieved_tasks->modelKeys();
        return $this->jsonResponse(['tasks' => $achieved_tasks_ids, 'count' => count($achieved_tasks)]);
    }

    public function achieveTask(Rimotatsu $rimotatsu, Task $task)
    {
        $successResponse = $this->jsonResponse([
            "status"  => 200,
            "message" => "タスクを達成しました。"
        ]);
        $illegalResponse = $this->jsonResponse([
            "status"  => 401,
            "message" => "送信されたタスクは存在しません。"
        ]);

        //taskのrimotatsu_idが異なる場合
        if($task->rimotatsu_id != $rimotatsu->id){
            return $illegalResponse;
        }
        //元から達成していた場合
        $is_achieved = Auth::user()->tasks()->find($task->id) != null;
        if($is_achieved){return $successResponse;} //元から達成されていても成功扱い
        //達成していない通常の場合
        Auth::user()->tasks()->attach($task->id);
        return $successResponse;
    }

    public function undoneTask(Rimotatsu $rimotatsu, Task $task)
    {
        $successResponse = $this->jsonResponse([
            "status"  => 200,
            "message" => "タスクの達成を取り消しました。"
        ]);
        $illegalResponse = $this->jsonResponse([
            "status"  => 401,
            "message" => "送信されたタスクは存在しません。"
        ]);

        if($task->rimotatsu_id != $rimotatsu->id){
            return $illegalResponse;
        }
        Auth::user()->tasks()->detach($task->id); //達成していたなら削除 していないなら何もしない
        return $successResponse;
    }
}

