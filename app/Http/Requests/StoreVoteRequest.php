<?php

namespace App\Http\Requests;

use App\Models\Rimotatsu;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO: リモ達の達成項目数を取得する機能が追加されたらその条件も含める

        // 該当のリモ達の宝くじに投票していない場合は認可する
        $userVoteCount = $this->user()
            ->votes
            ->where('rimotatsu_id', $this->route('rimotatsu')->getKey())
            ->count();

        return $userVoteCount === 0;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'voted_num' => 'required|integer|min:1'
        ];
    }

    /**
     * バリデーションエラーのカスタム属性の取得
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'voted_num' => '投票番号',
        ];
    }

    /**
     * 定義済みバリデーションルールのエラーメッセージ取得
     *
     * @return array
     */
    public function messages()
    {
        return [
            'voted_num.required' => ':attribute を入力してください。',
            'voted_num.integer' => ':attribute は自然数を入力してください。',
            'voted_num.min' => ':attribute は自然数を入力してください。',
        ];
    }

    // バリデーションエラーが発生した際にHTMLではなくJSONを返すようにメソッドをオーバーライド
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'status' => 422,
            'message' => $validator->errors()->get('voted_num')[0],
        ], 422);

        throw new HttpResponseException($response);
    }

    // 認可失敗時にJSONレスポンスを返す
    protected function failedAuthorization()
    {
        $response = response()->json([
            'status' => 400,
            'message' => '既に投票しています。',
        ], 400);

        throw new HttpResponseException($response);
    }
}
