<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ジャンボ宝くじ応募ページ</title>
</head>
<body>
<h2>ジャンボ宝くじ 応募フォーム</h2>
<div>対象：<span>{{ $rimotatsu->title }}</span></div>

{{-- レスポンスにより、エラーメッセージや成功メッセージを表示する --}}
<div id="form-result"></div>

<form id="form-vote">
    @csrf
    <label for="voted_num">投票する番号</label>
    <input id="voted_num" name="voted_num" type="number">
    <button type="submit">投票する</button>
</form>

<div>
    <div id="result-check-voted_num"></div>
    <button id="button-check-voted_num">確認</button>
</div>

<div>
    <div id="result-vote-winner"></div>
    <button id="button-vote-winner">当選確認</button>
</div>

<script>
    const user = @json($user);
    const form = document.getElementById("form-vote");
    const formResult = document.getElementById("form-result");

    // フォームのsubmitイベントを乗っ取る
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        sendData();
    });

    // ログインしていない場合はログインページへリダイレクト
    function redirectIfUserNotLogin(status) {
        if (status === 401) {
            location.replace('/login');
        }
    }

    // フォームを送信する
    function sendData() {
        const XHR = new XMLHttpRequest();

        // FormDataオブジェクトとform要素を紐付ける
        const FD = new FormData(form);

        XHR.addEventListener("load", function () {
            console.log("送信完了");
            const responseData = JSON.parse(XHR.response);
            const message = responseData["message"];
            const status = responseData["status"];

            redirectIfUserNotLogin(status);

            // messageを画面に表示
            if (status !== 200) {
                formResult.innerText = message;
                formResult.style.backgroundColor = "#FF8888";
                formResult.style.display = "block";
            } else {
                formResult.innerText = message;
                formResult.style.backgroundColor = "#CCFFCC";
                formResult.style.display = "block";
            }
        });

        XHR.addEventListener("error", function () {
            console.error("エラーが発生しました");
        });

        XHR.open("POST", "/api/{{$rimotatsu->getKey()}}/vote");

        XHR.send(FD);
    }

    const buttonCheckVotedNum = document.getElementById("button-check-voted_num");
    const resultCheckVotedNum = document.getElementById("result-check-voted_num");

    buttonCheckVotedNum.addEventListener("click", function (event) {
        event.preventDefault();
        getUserVote()
            .then(votedNum => {
                // 投票していない場合は-1がAPIのレスポンスで返ってくる
                if (votedNum === -1) {
                    resultCheckVotedNum.innerText = "まだ投票していません。";
                } else {
                    resultCheckVotedNum.innerText = "すでに投票しています。\n投票番号は「" + votedNum + "」です。";
                }
            });
    });

    const buttonVoteWinner = document.getElementById("button-vote-winner");
    const resultVoteWinner = document.getElementById("result-vote-winner");

    buttonVoteWinner.addEventListener("click", function (event) {
        event.preventDefault();
        getVoteWinner().then(data => {
            redirectIfUserNotLogin(data["status"]);
            const status = data["status"];
            let message = data["message"];

            // 当選結果のメッセージを作成
            if (status === 200) {
                if (data["user_id"] === user["id"]) {
                    message += "\nおめでとうございます！\n" + user["name"] + "さんは当選しました！";
                } else if (data["user_id"] !== -1 && data["user_id"] !== -2) {
                    // 自分以外の誰かが当選者になっている場合
                    message += "\n" + user["name"] + "さんは当選しませんでした。\n当選者は" + data["name"] + "さんです。";
                    message += "\n当選番号は「" + data["number"] + "」です。";
                }
            }

            resultVoteWinner.innerText = message;
        });
    })

    // ユーザーが投票した番号を取得
    function getUserVote() {
        const response = fetch("/api/{{$rimotatsu->getKey()}}/vote");
        return response
            .then(response => response.json())
            .then(data => {
                redirectIfUserNotLogin(data["status"]);
                const votedNum = data["voted_num"];
                console.log("投票番号取得完了", votedNum);
                return votedNum;
            });
    }

    // 宝くじの当選状況を取得
    function getVoteWinner() {
        const response = fetch("/api/{{$rimotatsu->getKey()}}/vote/winner");
        return response
            .then(response => response.json());
    }
</script>

<style>
    #form-result {
        display: none;
        width: fit-content;
        padding: 1rem;
        border-radius: 0.4rem;
    }
</style>
</body>
</html>
