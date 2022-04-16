<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoteRequest;
use App\Models\Rimotatsu;
use App\Models\User;
use App\Models\Vote;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * ユーザーが投票できるかどうかを確認する。
     * @param Request $request
     * @param Rimotatsu $rimotatsu
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserCanVote(Request $request, Rimotatsu $rimotatsu) {
        $achieved_tasks = Auth::user()->tasks()->where('rimotatsu_id', $rimotatsu->id)->get();
        $userHasRight = count($achieved_tasks) >= 15;

        return $this->jsonResponse([
            'status' => 200,
            'right_to_vote' => $userHasRight,
        ]);
    }

    public function store(StoreVoteRequest $request, Rimotatsu $rimotatsu) {
        // form validation
        $validated = $request->validated();

        $user = Auth::user();
        // TODO: レスポンスの記述の重複を減らせる方法

        try {
            $vote = new Vote([
                'user_id' => $user->id,
                'rimotatsu_id' => $rimotatsu->getKey(),
                'voted_num' => $validated['voted_num'],
            ]);
            $vote->save();
        } catch (Exception $exception) {
            return $this->jsonResponse([
                'status' => 500,
                'message' => '投票が失敗しました。',
            ], 500);
        }

        return $this->jsonResponse([
            'status' => 200,
            'message' => '投票が成功しました。',
        ]);
    }

    /**
     * 宝くじの当選結果を取得する。
     *
     * @param Request $request
     * @param Rimotatsu $rimotatsu
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWinner(Request $request, Rimotatsu $rimotatsu) {
        // RimotatsusテーブルのChampion_idを取得
        // 初期値-1なら投票期間が終わっていない
        // -2なら当選者はいない
        // IDがあれば当選者が決定されている

        $winnerId = $rimotatsu['champion_id'];

        if ($winnerId === -1) {
            if ($this->decideWinner($rimotatsu)) {
                // 当選者情報が変更されたため、モデルをリフレッシュ
                $winnerId = $rimotatsu->refresh()['champion_id'];
            } else {
                return $this->jsonResponse([
                    'status' => 400,
                    'message' => '当選発表前です。',
                    'user_id' => -1,
                    'number' => -1
                ], 400);
            }
        }

        if ($winnerId === -2) {
            return $this->jsonResponse([
                'status' => 200,
                'message' => '当選者はいませんでした。',
                'user_id' => -2,
                'number' => -2
            ]);
        } else {
            return $this->jsonResponse([
                'status' => 200,
                'message' => '当選者が決定しました。',
                'user_id' => $winnerId,
                'number' => $rimotatsu['champion_num'],
                'name' => User::find($winnerId)->name
            ]);
        }
    }

    /**
     * 宝くじの投票で一番小さい数字を投票した人を当選者に決定する。投票がない場合や複数人対象がいた場合は、当選者はいないとする。
     *
     * @param Rimotatsu $rimotatsu
     *
     * @return bool
     */
    private function decideWinner(Rimotatsu $rimotatsu)
    {
        // 投票期間中の場合は何もしない
        $endDate = $rimotatsu->end_date;
        $now = new DateTime('now');
        if (is_null($endDate) || $endDate > $now) {
            return false;
        }

        $votes = $rimotatsu->votes;
        $votesHaveMinNum = $votes->where('voted_num', $votes->min('voted_num'));

        if ($votesHaveMinNum->count() === 1) {
            // 当選者の情報を登録
            try {
                $winnerVote = $votesHaveMinNum->first();
                $rimotatsu['champion_id'] = $winnerVote->user->id;
                $rimotatsu['champion_num'] = $winnerVote['voted_num'];
                $rimotatsu->save();
            } catch (Exeption $exception) {
                return false;
            }

            return true;
        }

        // 当選者がいないことを登録
        try {
            $winnerVote = $votesHaveMinNum->first();
            $rimotatsu['champion_id'] = -2;
            $rimotatsu['champion_num'] = -2;
            $rimotatsu->save();
        } catch (Exeption $exception) {
            return false;
        }

        return true;
    }
}
